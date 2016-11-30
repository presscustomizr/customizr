<?php
class CZR_menu_model_class extends CZR_Model {
  public $theme_location = 'main';
  public $menu_class;
  public $menu_id;
  public $element_class;
  public $fallback_cb;
  public $walker;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'menu_class' ]     = $this -> get_menu_class();
    $model[ 'element_class' ]  = $this -> get_element_class();
    $model[ 'theme_location' ] = $this -> theme_location;
    $model[ 'walker' ]         = ! czr_fn_has_location_menu( $model['theme_location'] ) ? '' : new CZR_nav_walker( $model['theme_location'] );
    $model[ 'fallback_cb' ]    = array( $this, 'czr_fn_page_menu' );

    return $model;
  }

  protected function get_menu_class() {
    return ( ! wp_is_mobile() && 'hover' == esc_attr( czr_fn_get_opt( 'tc_menu_type' ) ) ) ? array( 'nav', 'navbar-nav', 'tc-open-on-hover' ) : array( 'nav', 'navbar-nav', 'tc-open-on-click' );
  }

  protected function get_element_class() {
    return (array) $this -> element_class;
  }


  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function czr_fn_maybe_filter_views_model() {
    parent::czr_fn_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header'         , array( $this, 'pre_rendering_view_header_cb' ) );
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


    if ( esc_attr( czr_fn_get_opt( "tc_sticky_header") || czr_fn_is_customizing() ) ) {
      if ( ! is_array( $header_model -> element_class ) )
        $header_model -> element_class = explode( ' ', $header_model -> element_class );
      array_push( $header_model -> element_class,
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_menu') ) ? 'tc-menu-on' : 'tc-menu-off'
      );
    }
  }

  /**
  * @hook: pre_rendering_view_navbar_wrapper
  */
  function pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model ) {
    //Navbar regular menu position
    if ( ! is_array( $navbar_wrapper_model -> element_class ) )
      $navbar_wrapper_model -> element_class = explode( ' ', $navbar_wrapper_model -> element_class );

    //this is the same for the main regular menu
    if ( ! wp_is_mobile() && 0 != esc_attr( czr_fn_get_opt( 'tc_menu_submenu_fade_effect') ) )
      array_push( $navbar_wrapper_model -> element_class, 'tc-submenu-fade' );
    if ( 0 != esc_attr( czr_fn_get_opt( 'tc_menu_submenu_item_move_effect') ) )
      array_push( $navbar_wrapper_model -> element_class, 'tc-submenu-move' );
    array_push( $navbar_wrapper_model -> element_class, ( ! wp_is_mobile() && 'hover' == esc_attr( czr_fn_get_opt( 'tc_menu_type' ) ) ) ?  'tc-open-on-hover' : 'tc-open-on-click' );
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
    parent::czr_fn_sanitize_model_properties( $model );
    $model -> menu_class = $this -> czr_fn_stringify_model_property( 'menu_class' );
  }



  /**
   * Display or retrieve list of pages with optional home link.
   * Modified copy of wp_page_menu()
   * @return string html menu
   */
  function czr_fn_page_menu( $args = array() ) {
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
    $menu .= str_replace( array( "\r", "\n", "\t" ), '', $this -> czr_fn_list_pages($list_args) );

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
  function czr_fn_list_pages( $args = '' ) {
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

      $output .= $this -> czr_fn_walk_page_tree( $pages, $r['depth'], $current_page, $r );
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
  function czr_fn_walk_page_tree($pages, $depth, $current_page, $r) {
    // if ( empty($r['walker']) )
    //   $walker = new Walker_Page;
    // else
    //   $walker = $r['walker'];
    $walker = new CZR_nav_walker_page;

    foreach ( (array) $pages as $page ) {
      if ( $page->post_parent )
        $r['pages_with_children'][ $page->post_parent ] = true;
    }

    $args = array($pages, $depth, $r, $current_page);
    return call_user_func_array(array($walker, 'walk'), $args);
  }
}//end class