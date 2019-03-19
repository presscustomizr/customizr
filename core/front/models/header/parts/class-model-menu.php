<?php
class CZR_menu_model_class extends CZR_Model {

    protected $theme_location;
    protected $menu_id;
    protected $def_menu_class;
    protected $menu_class;
    protected $fallback_cb;
    protected $walker;
    protected $dropdown_type;
    protected $dropdown_on;

    protected $czr_menu_location; //can be sidenav, topbar, primary_navbar, secondary_navbar, mobile
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
          'def_menu_class'      => array( 'nav' ),
          'menu_class'          => array(),
          'fallback_cb'         => czr_fn_isprevdem() ? array( $this, 'czr_fn_page_menu' ) : 'czr_fn_print_add_menu_button',
          'walker'              => ''
      );

      return $_preset;
    }


    public function __construct( $model ) {

        parent::__construct( $model );

        $_model = array();

        //set properties depending on the czr_menu_location
        switch ( $this->czr_menu_location ) {

            case 'primary_navbar' :

                $_menu_position = esc_attr( czr_fn_opt( 'tc_menu_position') );

                switch ( $_menu_position ) {

                    case 'pull-menu-left' :
                        $menu_position_class = 'justify-content-start';
                        break;

                    default :
                        $menu_position_class = 'justify-content-end';
                }

                $_model = array(

                    'element_class'       => array( 'primary-nav__menu-wrapper', $menu_position_class ),
                    'theme_location'      => 'main',
                    'menu_class'          => array( 'primary-nav__menu', 'regular-nav', 'nav__menu' ),
                    'dropdown_type'       => $this->dropdown_type ? $this->dropdown_type : esc_attr( czr_fn_opt( 'tc_menu_type' ) ),
                    'dropdown_on'         => 'link-action'

                );

                break;

            case 'secondary_navbar' :

                $_menu_position = esc_attr( czr_fn_opt( 'tc_second_menu_position') );

                switch ( $_menu_position ) {

                    case 'pull-menu-left' :
                        $menu_position_class = 'justify-content-start';
                        break;

                    default :
                        $menu_position_class = 'justify-content-end';
                }

                $_model = array(

                    'element_class'       => array( 'primary-nav__menu-wrapper', $menu_position_class ),
                    'theme_location'      => 'secondary',
                    'menu_id'             => 'secondary-menu',
                    'menu_class'          => array( 'primary-nav__menu', 'regular-nav', 'nav__menu' ),
                    'dropdown_type'       => $this->dropdown_type ? $this->dropdown_type : esc_attr( czr_fn_opt( 'tc_menu_type' ) ),
                    'dropdown_on'         => 'link-action'

                );

                break;

            case 'topbar' :

                $_model = array(

                    'element_class'       => array( 'topbar-nav__menu-wrapper' ),
                    'theme_location'      => 'mobile',
                    'menu_id'             => 'topbar-menu',
                    'theme_location'      => 'topbar',
                    'menu_class'          => array( 'topbar-nav__menu', 'regular-nav', 'nav__menu' ),
                    'dropdown_type'       => $this->dropdown_type ? $this->dropdown_type : esc_attr( czr_fn_opt( 'tc_menu_type' ) ),
                    'dropdown_on'         => 'link-action'
                );



                break;

            case 'sidenav' :

                $_model = array(
                    'element_class'       => array( 'side-nav__menu-wrapper' ),
                    'menu_class'          => array( 'side-nav__menu', 'side', 'vertical-nav', 'nav__menu', 'flex-column' ),
                );

                if ( !$this->dropdown_type ) {
                    $_model[ 'dropdown_type' ] = 1 == esc_attr( czr_fn_opt( 'tc_side_menu_dropdown_on_click' ) ) ? 'click' : '';
                } else {
                    $_model[ 'dropdown_type' ] = $this->dropdown_type;
                }

                $_model[ 'dropdown_on' ] = 'click' == $_model[ 'dropdown_type' ] ? 'caret-click' : '';

                break;

            case 'mobile' :

                //MENUS
                $location_map = array(
                    'mobile_menu'    => 'mobile',
                    'main_menu'      => 'main',
                    'secondary_menu' => 'secondary',
                    'top_menu'       => 'topbar'
                );

                $mobile_menu_opt = czr_fn_opt( 'tc_header_mobile_menu_layout' );

                //SETUP THE MOBILE MENU LOCATION
                //Since v3.3.8, the default mobile menu is the topbar
                //The idea is to avoid as much as possible having an empty mobile menu for new users.
                //If no menu has been assigned to the topbar location yet, let's loop on all possible menu in a specific order to assign a menu anyway.
                $mobile_menu_location = '_not_set_';
                $has_menu_assigned = false;

                if ( is_string( $mobile_menu_opt ) && array_key_exists( $mobile_menu_opt, $location_map ) && has_nav_menu( $location_map[ $mobile_menu_opt ] ) ) {
                    $mobile_menu_location = $location_map[ $mobile_menu_opt ];
                    $has_menu_assigned = true;
                } else {
                    $match = false;
                    foreach ( $location_map as $user_opt => $theme_loc ) {
                        if ( has_nav_menu( $theme_loc ) && ! $match ) {
                            $match = true;
                            $mobile_menu_location = $theme_loc;
                        }
                    }
                }


                $_model = array(
                    'element_class'       => array( 'mobile-nav__menu-wrapper' ),
                    'theme_location'      => $mobile_menu_location,
                    'menu_id'             => 'mobile-nav-menu',
                    'menu_class'          => array( 'mobile-nav__menu', 'vertical-nav', 'nav__menu', 'flex-column' ),
                );

                if ( !$this->dropdown_type ) {
                    $_model[ 'dropdown_type' ] = 1 == esc_attr( czr_fn_opt( 'tc_header_mobile_menu_dropdown_on_click' ) ) ? 'click' : '';
                } else {
                    $_model[ 'dropdown_type' ] = $this->dropdown_type;
                }

                $_model[ 'dropdown_on' ] = 'click' == $_model[ 'dropdown_type' ] ? 'caret-click' : '';

                break;
        }

        if ( ! empty( $_model ) )
              $this->czr_fn_update( $_model );


    }



    /*
    * Fired just before the view is rendered
    * @hook: pre_rendering_view_{$this -> id}, 9999
    */
    /*
    * setup properties just before the rendering, to take in account params passed through czr_fn_render_template()
    */
    function czr_fn_setup_late_properties() {

        $walker     = ! czr_fn_has_location_menu( $this -> theme_location ) ? $this->walker : new CZR_nav_walker( $this -> theme_location );

        //menu class
        if ( ! is_array( $this->menu_class ) )
            $menu_class = explode( ' ', $this->menu_class );
        else
            $menu_class = $this->menu_class;

        $menu_class = czr_fn_stringify_array( array_merge( $menu_class, $this->def_menu_class ) );

        //element class
        if ( ! is_array( $this->element_class ) )
            $element_class  = explode( ' ', $this->element_class );
        else
            $element_class =  $this->element_class;


        $_submenu_opening_class = 'click' == $this -> dropdown_type ? 'czr-open-on-click' : 'czr-open-on-hover';

        $_submenu_opening_class = ! $this->dropdown_type ? '' : $_submenu_opening_class;

        $element_class[]        = $_submenu_opening_class;

        $element_class          = czr_fn_stringify_array( $element_class );


        $this->czr_fn_update( compact( 'walker', 'menu_class', 'element_class' ) );
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