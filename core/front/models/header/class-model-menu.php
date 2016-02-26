<?php
class TC_menu_model_class extends TC_Model {
  public $theme_location = 'main';
  public $menu_class;
  public $wrapper_class;
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

    $model[ 'menu_class' ]    = $this -> get_menu_class();
    $model[ 'wrapper_class' ] = $this -> get_wrapper_class();
    $model['theme_location']  = $this -> theme_location;
    $model[ 'walker' ]        = ! TC_utils::$inst -> tc_has_location_menu($model['theme_location']) ? '' : new TC_nav_walker($model['theme_location']);
    $model[ 'fallback_cb' ]   = array( $this, 'tc_page_menu' );

    return $model;
  }

  protected function get_menu_class() {
    return ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav tc-hover-menu' ) : array( 'nav' );
  }

  protected function get_wrapper_class() {
    return ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array( 'nav-collapse collapse', 'tc-hover-menu-wrapper' ) : array( 'nav-collapse', 'collapse' );
  }


  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function tc_maybe_filter_views_model() {
    parent::tc_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header'         , array( $this, 'pre_rendering_view_header_cb' ) );
    if ( method_exists( $this, 'pre_rendering_view_navbar_wrapper_cb' ) )
      add_action( 'pre_rendering_view_navbar_wrapper' , array( $this, 'pre_rendering_view_navbar_wrapper_cb' ) );
  }


  /**
  * @hook; pre_rendering_view_header
  *
  * parse header model before rendering to add 'sticky' menu visibility class
  */ 
  function pre_rendering_view_header_cb( $header_model ) {
    //fire once
    static $_fired = false;
    if ( $_fired ) return $header_model;
    $_fired        = true;

    if ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") || TC___::$instance -> tc_is_customizing() ) ) {
      if ( ! is_array( $header_model -> class ) )
        $header_model -> class = explode( ' ', $header_model -> class );
      array_push( $header_model -> class, 
        0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_menu') ) ? 'tc-menu-on' : 'tc-menu-off'
      );
    }
  }


  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> menu_class ) )
        $model -> menu_class      = join( ' ', array_unique( $model -> menu_class ) );
    if ( is_array( $model -> wrapper_class ) )
      $model -> wrapper_class   = join( ' ', array_unique( $model -> wrapper_class ) );
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
}//end class

