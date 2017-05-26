<?php
class CZR_menu_model_class extends CZR_Model {
  protected $theme_location;
  protected $menu_id;
  protected $def_menu_class;
  protected $menu_class;
  protected $fallback_cb;
  protected $walker;
  protected $dropdown_type;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {

    $_preset = array(
        'element_class'       => '',
        'theme_location'      => 'main',
        'menu_id'             => 'main-menu',
        'def_menu_class'      => array('nav'),
        'menu_class'          => array(),
        'fallback_cb'         => array( $this, 'czr_fn_page_menu' ),
        'walker'              => '',
        'dropdown_type'       => esc_attr( czr_fn_opt( 'tc_menu_type' ) )
    );

    return $_preset;
  }



  public function czr_fn_get_walker() {
    return ! czr_fn_has_location_menu( $this -> theme_location ) ? $this->walker : new CZR_nav_walker( $this -> theme_location );
  }


  public function czr_fn_get_menu_class() {
    if ( !is_array($this->menu_class) )
      $this->menu_class = array($this->menu_class);

    return czr_fn_stringify_array( array_merge( $this->menu_class, $this->def_menu_class ) );
  }


  public function czr_fn_get_element_class() {
    $_element_class = $this->czr_fn__get_element_class();

    return czr_fn_stringify_array( $_element_class );
  }

  protected function czr_fn__get_element_class() {
    $_submenu_opening_class = 'hover' == $this -> dropdown_type ? 'czr-open-on-hover' : 'czr-open-on-click';
    $_submenu_opening_class = !$this -> dropdown_type ? '' : $_submenu_opening_class;

    return array_filter( array( $_submenu_opening_class,
      $this->element_class //maybe passed
    ) );
  }




  /**
   * Display or retrieve list of pages with optional home link.
   * Modified copy of wp_page_menu()
   * @return string html menu
   */
  function czr_fn_page_menu( $args = array() ) {
    $defaults = array(
      'sort_column' => 'menu_order, post_title',
      'menu_class' => 'menu',
      'echo' => true,
      'link_before' => '',
      'link_after' => ''
    );

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
      $menu = '<ul id="' . $this->menu_id . '" class="' . esc_attr($args['menu_class']) . '">' . $menu . '</ul>';

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