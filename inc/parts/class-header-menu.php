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
      add_action( 'wp'             , array( $this, 'tc_set_menu_hooks') );
    }


    /***************************************
    * WP HOOKS SETTINGS
    ****************************************/
    /*
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_menu_hooks() {
      if ( (bool) TC_utils::$inst->tc_opt('tc_hide_all_menus') )
        return;
      //VARIOUS USER OPTIONS
      add_filter( 'body_class'                    , array( $this , 'tc_add_body_classes') );
      //Set header css classes based on user options
      add_filter( 'tc_header_classes'             , array( $this , 'tc_set_header_classes') );
      add_filter( 'tc_social_header_block_class'  , array( $this, 'tc_set_social_header_class') );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      add_filter( 'tc_user_options_style'         , array( $this , 'tc_menu_item_style_first_letter_css') );
      //set second menu specific style including @media rules
      add_filter( 'tc_user_options_style'         , array( $this , 'tc_add_second_menu_inline_style') );

      //SIDE MENU HOOKS SINCE v3.3+
      if ( $this -> tc_is_sidenav_enabled() ){
        add_action( 'wp_head'                     , array( $this , 'tc_set_sidenav_hooks') );
        add_filter( 'tc_user_options_style'       , array( $this , 'tc_set_sidenav_style') );
      } else {
        // add main menu notice
        add_action( '__navbar'                    , array( $this, 'tc_maybe_display_main_menu_notice'), 50 );
      }
      //this adds css classes to the navbar-wrapper :
      //1) to the main menu if regular (sidenav not enabled)
      //2) to the secondary menu if enabled
      if ( ! $this -> tc_is_sidenav_enabled() || TC_utils::$inst->tc_is_secondary_menu_enabled() ) {
        add_filter( 'tc_navbar_wrapper_class'     , array( $this, 'tc_set_menu_style_options'), 0 );
      }

      //body > header > navbar action ordered by priority
      add_action ( '__navbar'                     , array( $this , 'tc_menu_display' ), 30 );
      //adds class
      add_filter ( 'wp_page_menu'                 , array( $this , 'tc_add_menuclass' ));
    }



    /***************************************
    * WP_HEAD HOOKS SETTINGS
    ****************************************/
    /**
    * Set Various hooks for the sidemenu
    * hook : wp_head
    * @return void
    */
    function tc_set_sidenav_hooks() {
      add_filter( 'body_class'              , array( $this, 'tc_sidenav_body_class') );

      // disable dropdown on click
      add_filter( 'tc_menu_open_on_click'   , array( $this, 'tc_disable_dropdown_on_click'), 10, 3 );

      // add side menu before the page wrapper
      add_action( '__before_page_wrapper'   , array( $this, 'tc_sidenav_display'), 0 );
      // add side menu help block
      add_action( '__sidenav'               , array( $this, 'tc_maybe_display_sidenav_help') );
      // add menu button to the sidebar
      add_action( '__sidenav'               , array( $this, 'tc_sidenav_toggle_button_display'), 5 );
      // add menu
      add_action( '__sidenav'               , array( $this, 'tc_sidenav_display_menu_customizer'), 10 );
    }


    /**
    * Displays a dismissable block of information in the sidenav wrapper when conditions are met
    * hook : __sidenav
    */
    function tc_maybe_display_sidenav_help() {
      if (  ! TC_placeholders::tc_is_sidenav_help_on() )
        return;
      ?>
      <div class="tc-placeholder-wrap tc-sidenav-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p><p>%3$s</p>',
              __( "This is a default page menu.", "customizr" ),
              __( "( If you don't have any pages in your website, then this side menu is empty for the moment. )" , "customizr"),
              sprintf( __("If you have already created menu(s), you can %s. If you need to create a new menu, jump to the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', TC_utils::tc_get_customizer_url( array( "section" => "nav") ), __( "change the default menu", "customizr"), __("replace this default menu by another one", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a>', admin_url('nav-menus.php'), __( "menu creation screen", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
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
          $this -> tc_regular_menu_display( 'main' );
        } else {
          $this -> tc_sidenav_toggle_button_display();
          if ( $this -> tc_is_second_menu_enabled() )
            $this -> tc_regular_menu_display( 'secondary' );
          else
            $this -> tc_maybe_display_second_menu_placeholder();
        }

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
        '__sidenav' == current_filter() ? __('Close', 'customizr') : __('Open the menu' , 'customizr')
      );
      return apply_filters( "tc_{$type}_menu_button_view", $_button );
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
    function tc_regular_menu_display( $_location = 'main' ){
      $type               = 'regular';
      $where              = 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $button_class       = array( 'btn-toggle-nav', $where );
      $button_attr        = 'data-toggle="collapse" data-target=".nav-collapse"';

      $menu_class         = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav tc-hover-menu' ) : array( 'nav' ) ;
      $menu_wrapper_class = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav-collapse collapse', 'tc-hover-menu-wrapper' ) : array( 'nav-collapse', 'collapse' );

      $menu_view = $this -> tc_wp_nav_menu_view( compact( '_location', 'type', 'menu_class', 'menu_wrapper_class' ) );

      if ( $menu_view && 'main' == $_location )
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
        $tc_side_nav_class        = implode(' ', apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) );
        $tc_side_nav_inner_class  = implode(' ', apply_filters('tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) );
        ?>
          <nav id="tc-sn" class="<?php echo $tc_side_nav_class; ?>" role="navigation">
            <div class="<?php echo $tc_side_nav_inner_class; ?>">
              <?php do_action( '__sidenav' ); ?>
            </div><!--.tc-sn-inner -->
          </nav><!-- //#tc-sn -->
        <?php
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
       //sidenav menu is always "main"
       $_location          = 'main';

       echo $this -> tc_wp_nav_menu_view( compact( '_location', 'type', 'menu_class', 'menu_wrapper_class') );
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
    * COMMON VIEW
    ****************************************/
    /**
    * WP Nav Menu View
    *
    * @return html string
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_wp_nav_menu_view( $args ) {
      extract( $args );
      //'_location', 'type', 'menu_class', 'menu_wrapper_class'

      $menu_args = apply_filters( "tc_{$type}_menu_args",
          array(
            'theme_location'  => $_location,
            'menu_class'      => implode(' ', apply_filters( "tc_{$type}_menu_class", $menu_class ) ),
            'fallback_cb'     => array( $this, 'tc_page_menu' ),
            //if no menu is set to the required location, fallsback to tc_page_menu
            //=> tc_page_menu has it's own class extension of Walker, therefore no need to specify one below
            'walker'          => ! TC_utils::$inst -> tc_has_location_menu($_location) ? '' : new TC_nav_walker($_location),
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


    /***************************************
    * PLACEHOLDERS VIEW
    ****************************************/
    /**
    * Displays the placeholder view if conditions are met in TC_placeholders::tc_is_main_menu_notice_on()
    * fired in tc_menu_display(), hook : __navbar
    * @since Customizr 3.4+
    */
    function tc_maybe_display_main_menu_notice() {
      if (  ! TC_placeholders::tc_is_main_menu_notice_on() )
          return;
      ?>
      <div class="tc-placeholder-wrap tc-main-menu-notice">
        <?php
          printf('<p><strong>%1$s<br/>%2$s</strong></p>',
              __( "You can now display your menu as a vertical and mobile friendly side menu, animated when revealed.", "customizr" ),
              sprintf( __("%s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('demo.presscustomizr.com?design=nav'), __( "Try it with the demo", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', TC_utils::tc_get_customizer_url( array( "section" => "nav") ), __( "open the customizer menu section", "customizr"), __("change your menu design now", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }


    /**
    * Displays the placeholder view if conditions are met in TC_placeholders::tc_is_second_menu_placeholder_on()
    * fired in tc_menu_display(), hook : __navbar
    * @since Customizr 3.4
    */
    function tc_maybe_display_second_menu_placeholder() {
      if (  ! TC_placeholders::tc_is_second_menu_placeholder_on() )
          return;
      ?>
      <div class="nav-collapse collapse tc-placeholder-wrap tc-menu-placeholder">
        <?php
          printf('<p><strong>%1$s<br/>%2$s</strong></p>',
              __( "You can display your main menu or a second menu here horizontally.", "customizr" ),
              sprintf( __("%s or read the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', TC_utils::tc_get_customizer_url( array( "section" => "nav") ), __( "Manage menus in the header", "customizr"), __("Manage your menus in the header now", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('http://docs.presscustomizr.com/article/101-customizr-theme-options-header-settings/#navigation'), __( "documentation", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }



    /***************************************
    * GETTERS / SETTERS
    ****************************************/
    /*
    * Set navbar menu css classes : effects, position...
    * hook : tc_navbar_wrapper_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_menu_style_options( $_classes ) {
      $_classes = ( ! wp_is_mobile() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_fade_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-fade' ) ) : $_classes;
      $_classes = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_item_move_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-move' ) ) : $_classes;
      $_classes = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array_merge( $_classes, array( 'tc-open-on-hover' ) ) : array_merge( $_classes, array( 'tc-open-on-click' ) );

      //Navbar menus positions (not sidenav)
      //CASE 1 : regular menu (sidenav not enabled), controled by option 'tc_menu_position'
      //CASE 2 : second menu ( is_secondary_menu_enabled ?), controled by option 'tc_second_menu_position'
      if ( ! $this -> tc_is_sidenav_enabled() )
        array_push( $_classes , esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
      if ( TC_utils::$inst->tc_is_secondary_menu_enabled() )
        array_push( $_classes , esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_position') ) );

      return $_classes;
    }


    /*
    * hook : body_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_add_body_classes($_classes) {
      //menu type class
      $_menu_type = $this -> tc_is_sidenav_enabled() ? 'tc-side-menu' : 'tc-regular-menu';
      array_push( $_classes, $_menu_type );

      return $_classes;
    }



    /**
    * Set the header classes
    * Callback for tc_header_classes filter
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_set_header_classes( $_classes ) {
      //backward compatibility (was not handled has an array in previous versions)
      if ( ! is_array($_classes) )
        return $_classes;

      //adds the second menu state
      if ( TC_Utils::$inst -> tc_is_secondary_menu_enabled() )
        array_push( $_classes, 'tc-second-menu-on' );
      //adds the resp. behaviour option for secondary menu
      array_push( $_classes, 'tc-second-menu-' . esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_resp_setting' ) . '-when-mobile' ) );

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

    /*
    * Second menu
    * This actually "restore" regular menu style (user options in particular) by overriding the max-width: 979px media query
    */
    function tc_add_second_menu_inline_style( $_css ) {
      if ( ! TC_Utils::$inst -> tc_is_secondary_menu_enabled() )
        return $_css;

      return sprintf("%s\n%s",
        $_css,
        "@media (max-width: 979px) {
          .tc-second-menu-on .nav-collapse {
            width: inherit;
            overflow: visible;
            height: inherit;
            position:relative;
            top: inherit;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
            background: inherit;
          }
          .tc-sticky-header.sticky-enabled #tc-page-wrap .nav-collapse, #tc-page-wrap .tc-second-menu-hide-when-mobile .nav-collapse.collapse .nav {
            display:none;
          }
          .tc-second-menu-on .tc-hover-menu.nav ul.dropdown-menu {
            display:none;
          }
          .tc-second-menu-on .navbar .nav-collapse ul.nav>li li a {
            padding: 3px 20px;
          }
          .tc-second-menu-on .nav-collapse.collapse .nav {
            display: block;
            float: left;
            margin: inherit;
          }
          .tc-second-menu-on .nav-collapse .nav>li {
            float:left;
          }
          .tc-second-menu-on .nav-collapse .dropdown-menu {
            position:absolute;
            display: none;
            -webkit-box-shadow: 0 2px 8px rgba(0,0,0,.2);
            -moz-box-shadow: 0 2px 8px rgba(0,0,0,.2);
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
            background-color: #fff;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            padding: 5px 0;
          }
          .tc-second-menu-on .navbar .nav>li>.dropdown-menu:after, .navbar .nav>li>.dropdown-menu:before{
            content: '';
            display: inline-block;
            position: absolute;
          }
          .tc-second-menu-on .tc-hover-menu.nav .caret {
            display:inline-block;
          }
          .tc-second-menu-on .tc-hover-menu.nav li:hover>ul {
            display: block;
          }
          .tc-second-menu-on .nav a, .tc-second-menu-on .tc-hover-menu.nav a {
            border-bottom: none;
          }
          .tc-second-menu-on .dropdown-menu>li>a {
            padding: 3px 20px;
          }
          .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:focus,.tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:hover,.tc-second-menu-on .tc-submenu-move .dropdown-submenu:focus>a, .tc-second-menu-on .tc-submenu-move .dropdown-submenu:hover>a {
            padding-left: 1.63em
          }
          .tc-second-menu-on .tc-submenu-fade .nav>li>ul {
            opacity: 0;
            top: 75%;
            visibility: hidden;
            display: block;
            -webkit-transition: all .2s ease-in-out;
            -moz-transition: all .2s ease-in-out;
            -o-transition: all .2s ease-in-out;
            -ms-transition: all .2s ease-in-out;
            transition: all .2s ease-in-out;
          }
          .tc-second-menu-on .tc-submenu-fade .nav li.open>ul, .tc-second-menu-on .tc-submenu-fade .tc-hover-menu.nav li:hover>ul {
            opacity: 1;
            top: 95%;
            visibility: visible;
          }
          .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a {
            -webkit-transition: all ease .241s;
            -moz-transition: all ease .241s;
            -o-transition: all ease .241s;
            transition: all ease .241s;
          }
          .tc-second-menu-on .dropdown-submenu>.dropdown-menu {
            top: 110%;
            left: 30%;
            left: 30%\9;
            top: 0\9;
            margin-top: -6px;
            margin-left: -1px;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
          }
          .tc-second-menu-on .dropdown-submenu>a:after {
            content: ' ';
          }
        }\n

        .sticky-enabled .tc-second-menu-on .nav-collapse.collapse {
          clear:none;
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

      $_sidenav_mobile_css = '
          #tc-sn { width: %1$spx;}
          nav#tc-sn { z-index: 999; }
          [class*=sn-left].sn-close #tc-sn, [class*=sn-left] #tc-sn{
            -webkit-transform: translate3d( -100%%, 0, 0 );
            -moz-transform: translate3d( -100%%, 0, 0 );
            transform: translate3d(-100%%, 0, 0 );
          }
          [class*=sn-right].sn-close #tc-sn,[class*=sn-right] #tc-sn {
            -webkit-transform: translate3d( 100%%, 0, 0 );
            -moz-transform: translate3d( 100%%, 0, 0 );
            transform: translate3d( 100%%, 0, 0 );
          }
         .animating #tc-page-wrap, .sn-open #tc-sn, .tc-sn-visible:not(.sn-close) #tc-sn{
            -webkit-transform: translate3d( 0, 0, 0 );
            -moz-transform: translate3d( 0, 0, 0 );
            transform: translate3d(0,0,0) !important;
          }
      ';
      $_sidenav_desktop_css = '
          #tc-sn { width: %1$spx;}
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
          /* stick the sticky header to the left/right of the page wrapper */
          .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-left] .tc-header { left: %1$spx; }
          .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-right] .tc-header { right: %1$spx; }
          /* ie<9 breaks using :not */
          .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-left] .tc-header { left: %1$spx; }
          .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-right] .tc-header { right: %1$spx; }
      ';

      return sprintf("%s\n%s",
        $_css,
        sprintf(
            apply_filters('tc_sidenav_inline_css',
              apply_filters( 'tc_sidenav_slide_mobile', wp_is_mobile() ) ? $_sidenav_mobile_css : $_sidenav_desktop_css
            ),
            $sidenav_width
        )
      );
    }

    /**
    * hook : body_class filter
    *
    * @since Customizr 3.3+
    */
    function tc_sidenav_body_class( $_classes ){
      $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
      array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );

      return $_classes;
    }


    /**
     * This hooks is fired in the Walker_Page extensions, by the start_el() methods.
     * It only concerns the main menu, when the sidenav is enabled.
     * @since Customizr 3.4+
     *
     * hook :tc_menu_open_on_click
     */
    function tc_disable_dropdown_on_click( $replace, $search, $_location = null ) {
      return 'main' == $_location ? $search : $replace ;
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


    /**
    * @return bool
    */
    function tc_is_second_menu_enabled() {
      return apply_filters( 'tc_is_second_menu_enabled', (bool)esc_attr( TC_utils::$inst->tc_opt( 'tc_display_second_menu' ) ) );
    }


    /**
     * Display or retrieve list of pages with optional home link.
     * Modified copy of wp_page_menu()
     * @return string html menu
     */
    function tc_page_menu( $args = array() ) {
      $defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
      $args = wp_parse_args( $args, $defaults );

      $args = apply_filters( 'wp_page_menu_args', $args );

      $menu = '';

      $list_args = $args;

      // Show Home in the menu
      if ( ! empty($args['show_home']) ) {
        if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
          $text = __('Home' , 'customizr');
        else
          $text = $args['show_home'];
        $class = '';
        if ( is_front_page() && !is_paged() )
          $class = 'class="current_page_item"';
        $menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
        // If the front page is a page, add it to the exclude list
        if (get_option('show_on_front') == 'page') {
          if ( !empty( $list_args['exclude'] ) ) {
            $list_args['exclude'] .= ',';
          } else {
            $list_args['exclude'] = '';
          }
          $list_args['exclude'] .= get_option('page_on_front');
        }
      }

      $list_args['echo'] = false;
      $list_args['title_li'] = '';
      $menu .= str_replace( array( "\r", "\n", "\t" ), '', $this -> tc_list_pages($list_args) );

      // if ( $menu )
      //   $menu = '<ul>' . $menu . '</ul>';

      //$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";

      if ( $menu )
        $menu = '<ul class="' . esc_attr($args['menu_class']) . '">' . $menu . '</ul>';

      //$menu = apply_filters( 'wp_page_menu', $menu, $args );
      if ( $args['echo'] )
        echo $menu;
      else
        return $menu;
    }


    /**
     * Retrieve or display list of pages in list (li) format.
     * Modified copy of wp_list_pages
     * @return string HTML list of pages.
     */
    function tc_list_pages( $args = '' ) {
      $defaults = array(
        'depth' => 0, 'show_date' => '',
        'date_format' => get_option( 'date_format' ),
        'child_of' => 0, 'exclude' => '',
        'title_li' => __( 'Pages', 'customizr' ), 'echo' => 1,
        'authors' => '', 'sort_column' => 'menu_order, post_title',
        'link_before' => '', 'link_after' => '', 'walker' => '',
      );

      $r = wp_parse_args( $args, $defaults );

      $output = '';
      $current_page = 0;

      // sanitize, mostly to keep spaces out
      $r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

      // Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
      $exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();

      $r['exclude'] = implode( ',', apply_filters( 'wp_list_pages_excludes', $exclude_array ) );

      // Query pages.
      $r['hierarchical'] = 0;
      $pages = get_pages( $r );

      if ( ! empty( $pages ) ) {
        if ( $r['title_li'] ) {
          $output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
        }
        global $wp_query;
        if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
          $current_page = get_queried_object_id();
        } elseif ( is_singular() ) {
          $queried_object = get_queried_object();
          if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
            $current_page = $queried_object->ID;
          }
        }

        $output .= $this -> tc_walk_page_tree( $pages, $r['depth'], $current_page, $r );

        if ( $r['title_li'] ) {
          $output .= '</ul></li>';
        }
      }

      $html = apply_filters( 'wp_list_pages', $output, $r );

      if ( $r['echo'] ) {
        echo $html;
      } else {
        return $html;
      }
    }


    /**
     * Retrieve HTML list content for page list.
     *
     * @uses Walker_Page to create HTML list content.
     * @since 2.1.0
     * @see Walker_Page::walk() for parameters and return description.
     */
    function tc_walk_page_tree($pages, $depth, $current_page, $r) {
      // if ( empty($r['walker']) )
      //   $walker = new Walker_Page;
      // else
      //   $walker = $r['walker'];
      $walker = new TC_nav_walker_page;

      foreach ( (array) $pages as $page ) {
        if ( $page->post_parent )
          $r['pages_with_children'][ $page->post_parent ] = true;
      }

      $args = array($pages, $depth, $r, $current_page);
      return call_user_func_array(array($walker, 'walk'), $args);
    }

  }//end of class
endif;
