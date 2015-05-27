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
      add_action ( 'init'                       , array( $this , 'tc_set_menu_hooks') );
    }


    /***************************************
    * HOOKS SETTINGS
    ****************************************/
    /*
    * hook : init
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_menu_hooks() {
      //VARIOUS USER OPTIONS
      if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
        add_filter( 'body_class'                  , array( $this, 'tc_set_no_navbar' ) );
      add_filter( 'tc_social_header_block_class'  , array( $this, 'tc_set_social_header_class') );

      //add a 100% wide container just after the sticky header to reset margin top
      if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) )
        add_action( '__after_header'                , array( $this, 'tc_reset_margin_top_after_sticky_header'), 0 );
      add_filter( 'tc_navbar_wrapper_class'       , array( $this, 'tc_set_menu_style_options'), 0 );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      add_filter( 'tc_user_options_style'       , array( $this , 'tc_menu_item_style_first_letter_css') );

      //MAIN VIEW
      //body > header > navbar action ordered by priority
      add_action ( '__navbar'                   , array( $this , 'tc_menu_display' ), 30, 1);
      add_filter ( 'wp_page_menu'               , array( $this , 'tc_add_menuclass' ));

      //SIDE MENU HOOKS SINCE v3.3+
      if ( $this -> tc_is_sidenav_enabled() )
        add_action( 'wp_head'                     , array( $this , 'tc_set_sidenav_hooks') );
    }


    /**
    * Set Various hooks for the sidemenu
    * hook : wp_head
    * @return void
    */
    function tc_set_sidenav_hooks() {
      add_filter( 'body_class'             , array( $this, 'tc_sidenav_body_class') );
      // remove tc menu
      remove_action( '__navbar'             , array( TC_menu::$instance, 'tc_menu_display'), 30 );
      // add toggle button
      add_action( '__navbar'                , array( $this, 'tc_sidenav_toggle_button_display'), 30 );

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
    * REGULAR VIEWS
    ****************************************/
    /**
    * Menu Rendering
    * hook : '__navbar'
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_menu_display($resp = null) {
      ob_start();
        //renders the responsive button
        if ( 'resp' == $resp ) { //resp is an argument of do_action ('__navbar' , 'resp')
          $button = sprintf('<button type="button" class="%1$s" data-toggle="collapse" data-target=".nav-collapse">%2$s%2$s%2$s</button>',
            apply_filters( 'tc_menu_button_class', 'btn btn-navbar' ),
            '<span class="icon-bar"></span>'
          );
          echo apply_filters( 'resp_menu_button', $button );
        }

        //renders the menu
        $menu_args = apply_filters( 'tc_menu_args',
                    array(
                      'theme_location'  => 'main',
                      'menu_class'      => ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? 'nav tc-hover-menu' : 'nav',
                      'fallback_cb'     => array( $this , 'tc_link_to_menu_editor' ),
                      'walker'          => TC_nav_walker::$instance,
                      'echo'            => false,
                  )
        );
        $menu_wrapper_class   = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? 'nav-collapse collapse tc-hover-menu-wrapper' : 'nav-collapse collapse';
        printf('<div class="%1$s">%2$s</div>',
            apply_filters( 'tc_menu_wrapper_class', $menu_wrapper_class ),
            wp_nav_menu( $menu_args )
        );

      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_menu_display', $html, $resp );
    } //end of funtion()




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
        printf('<nav id="pc-mc" class="%1$s" role="navigation"><div class="%2$s">',
                        implode(' ', apply_filters('tc_side_nav_class', array('pc-mc', 'navbar' ) ) ),
                        implode(' ', apply_filters('tc_side_nav_inner_class', array('pc-mc-inner','nav-collapse', 'collapse') ) )
        );
        do_action( '__sidenav' );
        echo '</div><!--end pc-mc-inner --></nav><!--end #pc-mc-->';

      $_html = ob_get_contents();

      if ( $_html ) ob_end_clean();
      echo apply_filters( 'tc_sidenav_display', $_html );
    }


    /**
    * @return html string
    * @since v3.3+
    *
    * hook: __sidenav
    */
    function tc_render_sidenav_menu(){
      //menu setup
      $menu_args = apply_filters( 'tc_sidenav_menu_args',
                 array(
                   'theme_location'  => 'main',
                   'menu_class'      => 'nav mc-nav',
                   'fallback_cb'     => array( TC_menu::$instance , 'tc_link_to_menu_editor' ),
                   'walker'          => TC_nav_walker::$instance,
                   'echo'            => false,
                 )
      );

      $menu = wp_nav_menu( $menu_args );

      if ( ! $menu )
        return;

      $menu_wrapper_class   = 'mc-nav-wrapper';

      ob_start();

        $_html = printf( '<div class="%1$s">%2$s</div>',
            apply_filters( 'tc_sidenav_menu_wrapper_class', $menu_wrapper_class ),
            $menu
        );

      $_html = ob_get_contents();
      if ( $_html ) ob_end_clean();
      echo apply_filters( 'tc_render_sidenav_menu', $_html );
    }


    /**
    * @return html string
    * @package MC
    * @since MC 1.0.0
    *
    * hooks: __sidenav, __navbar
    */
    function tc_sidenav_toggle_button_display() {
      $_where = 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $_html  = sprintf( '<button type="button" class="%1$s side-navigation-toggle">%2$s%2$s%2$s</button>',
                  implode(' ', apply_filters( 'tc_sidenav_button_class', array( 'btn', 'mc-toggle-btn', 'mc-toggle', $_where ) ) ),
                  '<span class="ico-bar"></span>'
      );

      echo apply_filters( 'tc_sidenav_toggle_button_display', $_html );
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
    function tc_set_no_navbar($_classes) {
      $_classes[] = 'no-navbar';
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
     * hook : body_class
     */
    function tc_sidenav_body_class( $_classes ){
      $_how = $this -> tc_sidenav_open_effect();
      $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
      array_push($_classes, sprintf('mc-%1$s-%2$s',
                                      $_where,
                                      $_how
                            )
      );
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
    private function tc_is_sidenav_enabled() {
      return apply_filters( 'tc_is_sidenav_enabled', 'aside' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_style') ) );
    }


    /**
    * @return string
    */
    private function tc_sidenav_open_effect() {
      return apply_filters( 'tc_sidenav_open_effect', 'slide_along' );
    }

  }//end of class
endif;
