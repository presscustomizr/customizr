<?php
class TC_menu_model_class extends TC_Model {
  static $instance;

  public $theme_location;
  public $menu_class;
  public $wrapper_class;
  public $type;
  public $fallback_cb;
  public $walker;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    //IS THIS STILL USED? DON'T WE USE A CUSTOM FALLBACK? (tc_page_menu)?
    add_filter ( 'wp_page_menu'                 , array( $this , 'tc_add_menuclass' ) );

    $defaults = array(
      'theme_location' => 'main',
      'menu_class'     => implode( ' ', array( 'nav', 'sn-nav') ),
      'wrapper_class'  => implode( ' ', array( 'sn-nav-wrapper' ) ),
      'type'           => 'sidenav',
      'fallback_cb'    => array( $this, 'tc_page_menu' ),
      'walker'         => '',
    ); 

    if ( isset( $model['params']['type'] ) && 'navbar' == $model['params']['type'] ) {
      $defaults['menu_class']    = implode( ' ', ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav tc-hover-menu' ) : array( 'nav' ) );

      $defaults['wrapper_class'] = implode( " ", ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav-collapse collapse', 'tc-hover-menu-wrapper' ) : array( 'nav-collapse', 'collapse' ) );
    }

    $args = isset( $model['params'] ) ? wp_parse_args( $model['params'], $defaults ) : $defaults;
    if ( empty( $model['walker'] ) )
      $args['walker']  = ! TC_utils::$inst -> tc_has_location_menu($args['theme_location']) ? '' : new TC_nav_walker($args['theme_location']);

    $model = array_merge( $model, $args );
    
    unset( $model['params']);
    return $model;
  }


  /**
  * Adds a specific class to the ul wrapper
  * hook : 'wp_page_menu'
  *
  * @package Customizr
  * @since Customizr 3.0
  */
  function tc_add_menuclass( $ulclass ) {
    $html =  preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
    return apply_filters( 'tc_add_menuclass', $html );
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

  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class($_classes) {
    //menu type class
    if ( 'navbar' == $this -> type  && 'main' == $this -> theme_location ) {
      array_push( $_classes, 'tc-regular-menu' );
    }

    return $_classes;
  }

  /**
  * @override
  * add an action to the navbar_wrapper pre_rendering view hook in order to alter its model.
  */ 
  /*  
  function tc_maybe_filter_views_model() {
    add_action( 'pre_rendering_view_navbar_wrapper', array( $this, 'pre_rendering_view_cb' ) );
  }
  */

  /**
  * @hook to the pre_rendering_view
  */
  function pre_rendering_view_cb( $model ) {
    if ( 'navbar' == $this -> type ) {
      //NAVBAR CLASSES
      if ( 'navbar_wrapper' == $model -> id ) {
        //Navbar menus positions (not sidenav)
        //CASE 1 : regular menu (sidenav not enabled), controled by option 'tc_menu_position'
        //CASE 2 : second menu ( is_secondary_menu_enabled ?), controled by option 'tc_second_menu_position'
        if ( 'main' == $this -> theme_location )
          array_push( $model -> class, esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
        elseif( 'secondary' == $this -> theme_location )
          array_push( $model -> class, esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_position') ) );
        
        //fire once ( in case is main or secondary menu )
        static $_fired = false;
        if ( $_fired ) return;
        $_fired = true;

        if ( ! wp_is_mobile() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_fade_effect') ) )
          array_push( $model -> class, 'tc-submenu-fade' );
        if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_item_move_effect') ) ) 
          array_push( $model -> class, 'tc-submenu-move' );
        array_push( $model -> class, ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ?  'tc-open-on-hover' : 'tc-open-on-click' );    
        //end fire once
      }//end navbar_wrapper class
      elseif ( 'header' == $model -> id ) { //header class for the secondary menu
        if( 'secondary' == $this -> theme_location ) {
          $model -> class = array_merge( $model -> class,  array(
              'tc-second-menu-on',
              'tc-second-menu-' . esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_resp_setting' ) ) . '-when-mobile'
          ) );
        }
      }//end header class
    }
  }//end fn

  /**
  * @hook tc_user_options_style
  * Second menu
  * This actually "restore" regular menu style (user options in particular) by overriding the max-width: 979px media query
  */
  function tc_user_options_style_cb( $_css ) {
    if (  'secondary' != $this -> theme_location )
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
  }//end fn
}//end class

