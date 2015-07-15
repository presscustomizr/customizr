<?php
/**
* Menu action
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_menu' ) ) :
  class TC_menu {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //Set menu customizer options (since 3.2.0)
      add_action ( 'wp'                       , array( $this , 'tc_set_menu_hooks') );
    }


    /***************************************
    * HOOKS SETTINGS
    ****************************************/
    /*
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_menu_hooks() {
      //VARIOUS USER OPTIONS
      add_filter( 'body_class'                    , array( $this , 'tc_add_body_classes') );
      add_filter( 'tc_social_header_block_class'  , array( $this, 'tc_set_social_header_class') );

      //add a 100% wide container just after the sticky header to reset margin top
      if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) )
        add_action( '__after_header'              , array( $this, 'tc_reset_margin_top_after_sticky_header'), 0 );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      add_filter( 'tc_user_options_style'         , array( $this , 'tc_menu_item_style_first_letter_css') );

      //SIDE MENU HOOKS SINCE v3.3+
      if ( $this -> tc_is_sidenav_enabled() ){
        add_action( 'wp_head'                     , array( $this , 'tc_set_sidenav_hooks') );
        add_filter( 'tc_user_options_style'       , array( $this , 'tc_set_sidenav_style') );
      }else
        add_filter( 'tc_navbar_wrapper_class'     , array( $this, 'tc_set_menu_style_options'), 0 );

      //body > header > navbar action ordered by priority
      add_action ( '__navbar'                     , array( $this , 'tc_menu_display' ), 30 );

      add_filter ( 'wp_page_menu'                 , array( $this , 'tc_add_menuclass' ));
    }



    /**
    * Set Various hooks for the sidemenu
    * hook : wp_head
    * @return void
    */
    function tc_set_sidenav_hooks() {
      add_filter( 'body_class'              , array( $this, 'tc_sidenav_body_class') );

      // disable dropdown on click
      add_filter( 'tc_menu_open_on_click'  , array( $this, 'tc_disable_dropdown_on_click'), 10, 2 );

      // add side menu before the page wrapper
      add_action( '__before_page_wrapper'   , array( $this, 'tc_sidenav_display'), 0 );

      // add menu button to the sidebar
      add_action( '__sidenav'               , array( $this, 'tc_sidenav_toggle_button_display'), 5 );
      // add menu
      add_action( '__sidenav'               , array( $this, 'tc_sidenav_display_menu_customizer'), 10 );
    }




    /***************************************
    * VIEWS
    ****************************************/
    /**
    * Menu Rendering : renders the navbar menus, or just the sidenav toggle button
    * hook : '__navbar'
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_menu_display() {
      ob_start();

      //renders the regular menu + responsive button
      if ( ! $this -> tc_is_sidenav_enabled() ) {
        $this -> tc_regular_menu_display();
      } else
        $this -> tc_sidenav_toggle_button_display();

      $html = ob_get_contents();
      ob_end_clean();

      echo apply_filters( 'tc_menu_display', $html );
    }


    /**
    * Menu button View
    *
    * @return html string
    * @package Customizr
    * @since v3.3+
    *
    */
    function tc_menu_button_view( $args ) {
      //extracts : 'type', 'button_class', 'button_attr'
      extract( $args );

      $_button_label = sprintf( '<span class="menu-label">%s</span>',
        '__sidenav' == current_filter() ? __('Close', 'customizr') : __('Menu' , 'customizr')
      );
      $_button = sprintf( '<div class="%1$s"><button type="button" class="btn menu-btn" %2$s title="%5$s">%3$s%3$s%3$s </button>%4$s</div>',
        implode(' ', apply_filters( "tc_{$type}_button_class", $button_class ) ),
        apply_filters( "tc_{$type}_menu_button_attr", $button_attr),
        '<span class="icon-bar"></span>',
        (bool)esc_attr( TC_utils::$inst->tc_opt('tc_display_menu_label') ) ? $_button_label : '',
        '__sidenav' == current_filter() ? __('Close', 'customizr') : __('Reveal the menu' , 'customizr')
      );
      return apply_filters( "tc_{$type}_menu_button_view", $_button );
    }


    /**
    * WP Nav Menu View
    *
    * @return html string
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_wp_nav_menu_view( $args, $theme_location = 'main' ) {
       extract( $args );
       //renders the menu
       $menu_args = apply_filters( "tc_{$type}_menu_args",
                    array(
                      'theme_location'  => $theme_location,
                      'menu_class'      => implode(' ', apply_filters( "tc_{$type}_menu_class", $menu_class ) ),
                      'fallback_cb'     => array( $this , 'tc_link_to_menu_editor' ),
                      'walker'          => TC_nav_walker::$instance,
                      'echo'            => false,
                  )
        );

        $menu = wp_nav_menu( $menu_args );

        if ( $menu )
          $menu = sprintf('<div class="%1$s">%2$s</div>',
              implode(' ', apply_filters( "tc_{$type}_menu_wrapper_class", $menu_wrapper_class ) ),
              $menu
          );

        return apply_filters("tc_{$type}_menu_view", $menu );
    }



    /**
    * Menu fallback. Link to the menu editor.
    * Thanks to tosho (http://wordpress.stackexchange.com/users/73/toscho)
    * http://wordpress.stackexchange.com/questions/64515/fall-back-for-main-menu
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function tc_link_to_menu_editor( $args ) {
      if ( ! current_user_can( 'manage_options' ) )
          return;

      // see wp-includes/nav-menu-template.php for available arguments
      extract( $args );

      $link = sprintf('%1$s<a href="%2$s">%3$s%4$s%5$s</a>%6$s',
        $link_before,
        admin_url( 'nav-menus.php' ),
        $before,
        __('Add a menu','customizr'),
        $after,
        $link_after
      );

      // We have a list
      $link = ( FALSE !== stripos( $items_wrap, '<ul' ) || FALSE !== stripos( $items_wrap, '<ol' ) ) ? '<li>' . $link . '</li>' : $link;

      $output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
      $output = ( ! empty ( $container ) ) ? sprintf('<%1$s class="%2$s" id="%3$s">%4$s</%1$s>',
                                                $container,
                                                $container_class,
                                                $container_id,
                                                $output
                                            ) : $output;

      if ( $echo ) { echo $output; }
      return $output;
    }

    /***************************************
    * REGULAR VIEWS
    ****************************************/
    /**
    *  Prepare params and echo menu views
    *
    * @return html string
    * @since v3.3+
    *
    */
    function tc_regular_menu_display(){
      $type               = 'regular';
      $where              = 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $button_class       = array( 'btn-toggle-nav', $where );
      $button_attr        = 'data-toggle="collapse" data-target=".nav-collapse"';

      $menu_class         = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav tc-hover-menu' ) : array( 'nav' ) ;
      $menu_wrapper_class = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav-collapse collapse', 'tc-hover-menu-wrapper' ) : array( 'nav-collapse', 'collapse' );

      $menu_view = $this -> tc_wp_nav_menu_view( compact( 'type', 'menu_class', 'menu_wrapper_class') );

      if ( $menu_view )
        $menu_view = $menu_view . $this -> tc_menu_button_view( compact( 'type', 'button_class', 'button_attr') );

      echo $menu_view;
    }



    /***************************************
    * SIDENAV VIEWS
    ****************************************/
    /**
    * @return html string
    * @since v3.3+
    *
    * hook: __before_page_wrapper
    */
    function tc_sidenav_display() {
      ob_start();
        printf('<nav id="tc-sn" class="%1$s" role="navigation"><div class="%2$s">',
                        implode(' ', apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) ),
                        implode(' ', apply_filters('tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) )
        );
        do_action( '__sidenav' );
        echo '</div><!--end tc-sn-inner --></nav><!--end #tc-sn-->';

      $_sidenav = ob_get_contents();
      ob_end_clean();

      echo apply_filters( 'tc_sidenav_display', $_sidenav );
    }


    /**
    * @return html string
    * @since v3.3+
    *
    * hook: __sidenav
    */
    function tc_sidenav_display_menu_customizer(){
       //menu setup
       $type               = 'sidenav';
       $menu_class         = array('nav', 'sn-nav' );
       $menu_wrapper_class = array('sn-nav-wrapper');

       echo $this -> tc_wp_nav_menu_view( compact( 'type', 'menu_class', 'menu_wrapper_class') );
    }

    /**
    * @return html string
    * @since v3.3+
    *
    * hooks: __sidenav, __navbar
    */
    function tc_sidenav_toggle_button_display() {
      $type          = 'sidenav';
      $where         = 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $button_class  = array( 'btn-toggle-nav', 'sn-toggle', $where );
      $button_attr   = '';

      echo $this -> tc_menu_button_view( compact( 'type', 'button_class', 'button_attr') );
    }




    /***************************************
    * GETTERS / SETTERS
    ****************************************/
    /*
    * Set menu class position
    * hook : tc_navbar_wrapper_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_menu_style_options( $_classes ) {
      $_classes = ( ! wp_is_mobile() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_fade_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-fade' ) ) : $_classes;
      $_classes = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_item_move_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-move' ) ) : $_classes;
      $_classes = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array_merge( $_classes, array( 'tc-open-on-hover' ) ) : array_merge( $_classes, array( 'tc-open-on-click' ) );
      return array_merge( $_classes, array(esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) ) );
    }



    /*
    * hook : body_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_add_body_classes($_classes) {
      if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
        array_push( $_classes , 'no-navbar' );

      //menu type class
      $_menu_type = $this -> tc_is_sidenav_enabled() ? 'tc-side-menu' : 'tc-regular-menu';
      array_push( $_classes, $_menu_type );

      return $_classes;
    }

    /*
    * hook :  tc_social_header_block_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_social_header_class($_classes) {
      return 'span5';
    }


    /*
    * hook : __after_header hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_reset_margin_top_after_sticky_header() {
      echo apply_filters(
        'tc_reset_margin_top_after_sticky_header',
        sprintf('<div id="tc-reset-margin-top" class="container-fluid" style="margin-top:%1$spx"></div>',
          apply_filters('tc_default_sticky_header_height' , 103 )
        )
      );
    }


    /**
    * Adds a specific class to the ul wrapper
    * hook : 'wp_page_menu'
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_add_menuclass( $ulclass) {
      $html =  preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
      return apply_filters( 'tc_add_menuclass', $html );
    }


    /**
    * Adds a specific style to the first letter of the menu item
    * hook : tc_user_options_style
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function tc_menu_item_style_first_letter_css( $_css ) {
      if ( ! apply_filters( 'tc_menu_item_style_first_letter' , TC_utils::$inst -> tc_user_started_before_version( '3.2.0' , '1.0.0' ) ? true : false ) )
        return $_css;

      return sprintf("%s\n%s",
        $_css,
        ".navbar .nav > li > a:first-letter {
          font-size: 17px;
        }\n"
      );
    }

    /**
    * Adds a specific style to the first letter of the menu item
    * hook : tc_user_options_style
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function tc_set_sidenav_style( $_css ) {
      $sidenav_width = apply_filters( 'tc_sidenav_width', 330 );

      return sprintf("%s\n%s",
        $_css,
        sprintf(
            '#tc-sn { width: %1$spx;}
             .tc-sn-visible[class*=sn-left] #tc-page-wrap { left: %1$spx; }
             .tc-sn-visible[class*=sn-right] #tc-page-wrap { right: %1$spx; }
             [class*=sn-right].sn-close #tc-page-wrap, [class*=sn-left].sn-open #tc-page-wrap {
               -webkit-transform: translate3d( %1$spx, 0, 0 );
               -moz-transform: translate3d( %1$spx, 0, 0 );
               transform: translate3d( %1$spx, 0, 0 );
             }
             [class*=sn-right].sn-open #tc-page-wrap, [class*=sn-left].sn-close #tc-page-wrap {
               -webkit-transform: translate3d( -%1$spx, 0, 0 );
               -moz-transform: translate3d( -%1$spx, 0, 0 );
               transform: translate3d( -%1$spx, 0, 0 );
             }
             /* stick the sticky header to the left/right of the page wrapper*/
             .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-left] .tc-header { left: %1$spx; }
             .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-right] .tc-header { right: %1$spx; }
             /* ie<9 breaks using :not */
             .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-left] .tc-header { left: %1$spx; }
             .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-right] .tc-header { right: %1$spx; }',
            $sidenav_width
        )
      );
    }

    /**
    * hook : body_class filter
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_sidenav_body_class( $_classes ){
      $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
      array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );

      return $_classes;
    }


    /**
     * hook :tc_menu_open_on_click
     */
    function tc_disable_dropdown_on_click( $replace, $search ){
      return $search;
    }



    /***************************************
    * HELPERS
    ****************************************/
    /**
    * @return bool
    */
    function tc_is_sidenav_enabled() {
      return apply_filters( 'tc_is_sidenav_enabled', 'aside' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_style' ) ) );
    }

  }//end of class
endif;
