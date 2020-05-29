<?php
if ( ! class_exists( 'CZR_controller_modules' ) ) :
  class CZR_controller_modules extends CZR_controllers {
    static $instance;
    private static $_cache = array();

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call CZR_controllers constructor?
      //why this class extends CZR_controllers?
    }

    //TODO: option based
    function czr_fn_display_view_related_posts() {
      return apply_filters( 'czr_display_related_posts', czr_fn_is_possible( 'post' ) && 'disabled' != czr_fn_opt('tc_related_posts') );
    }

    function czr_fn_display_view_search_full_page () {
      return czr_fn_is_registered_or_possible( 'nav_search' ) && czr_fn_is_checked('tc_header_search_full_width');
    }

    function czr_fn_display_view_social_block() {
      return czr_fn_has_social_links();
    }

    /* Will be in pro */
    function czr_fn_display_view_social_share() {
      return false;
    }
    /* Will be in pro */
    function czr_fn_display_view_author_socials() {
      return false;
    }

    function czr_fn_display_view_main_slider() {
      if ( ! $this -> czr_fn_display_main_slider() )
        return;

      return 'tc_posts_slider' != czr_fn_get_current_slider();
    }

    function czr_fn_display_view_main_posts_slider() {
      if ( ! $this -> czr_fn_display_main_slider() )
        return;

      return 'tc_posts_slider' == czr_fn_get_current_slider();
    }

    function czr_fn_display_main_slider() {
      //gets the front slider if any
      $tc_front_slider  = esc_attr( czr_fn_opt( 'tc_front_slider' ) );
      //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types)
      $_show_slider     = czr_fn_is_real_home() ? ! empty( $tc_front_slider ) : ! is_404() && ! is_archive() && ! is_search();

      //hide slider when home page>1
      if ( apply_filters( 'tc_hide_featured_pages_when_paged', is_main_query() && czr_fn_is_real_home() ) ) {

            global $wp_query;

            $_is_paged = isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ||
                         isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 1;

            $_show_slider   = $_show_slider && ! $_is_paged;
      }

      $_show_slider     = apply_filters( 'czr_show_slider' , $_show_slider ) && czr_fn_is_slider_active();

      return $_show_slider;
    }


    /* BREADCRUMB */
    function czr_fn_display_view_breadcrumb() {

      if ( $to_return = 1 == esc_attr( czr_fn_opt( 'tc_breadcrumb') ) ) {
        if ( is_search() )
          $to_return = 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_search' ) ) ? false : true;

        elseif ( is_404() )
          $to_return = 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_404' ) ) ? false : true;

        elseif ( czr_fn_is_real_home() )
          $to_return = 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_home' ) ) ? false : true;

        elseif ( is_page() && 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_in_pages' ) ) )
          $to_return = false;

        elseif ( is_single() && 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_in_single_posts' ) ) )
          $to_return = false;

        elseif ( ! is_page() && ! is_single() && 1 != esc_attr( czr_fn_opt( 'tc_show_breadcrumb_in_post_lists' ) ) )
          $to_return = false;
      }

      return apply_filters( 'czr_show_breadcrumb', $to_return );
    }



    function czr_fn_display_view_featured_pages() {
          //gets display fp option
          $tc_show_featured_pages 	      = esc_attr( czr_fn_opt( 'tc_show_featured_pages' ) ) && czr_fn_is_real_home();

          //hide featured pages when page>1
          if ( apply_filters( 'tc_hide_featured_pages_when_paged', is_main_query() ) ) {

                global $wp_query;

                $_is_paged = isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ||
                             isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 1;

                $tc_show_featured_pages   = $tc_show_featured_pages && ! $_is_paged;
          }

          return apply_filters( 'czr_show_fp', $tc_show_featured_pages );
    }


    function czr_fn_display_view_recently_updated() {
      return 0 != esc_attr( czr_fn_opt( 'tc_post_metas_update_notice_in_title' ) );
    }


    /* Help blocks generic controller */
    function czr_fn_display_view_help_block() {
      //never display when customizing or admin
      if ( czr_fn_is_customizing() || is_admin() )
        return;
      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;
      /*
      * Display help blocks if the option is enabled and current user is "admin"
      */
      /*
      * User option to enabe/disable all notices
      */
      return apply_filters( 'czr_is_front_help_enabled' ,
          czr_fn_opt('tc_display_front_help')
          && is_user_logged_in()
          && current_user_can('edit_theme_options')
      );
    }


    /******************************
    VARIOUS HELPERS
    *******************************/
    function czr_fn_display_view_post_list_grid() {
      return apply_filters( 'czr_is_grid_enabled', czr_fn_is_list_of_posts() && 'grid' == esc_attr( czr_fn_opt( 'tc_post_list_grid') ) );
    }

    // /**
    // * Old version: NOT USED ANYMORE: context skopified
    // */
    // function czr_fn_display_view_post_list_grid() {
    //   return apply_filters( 'czr_is_grid_enabled', czr_fn_is_list_of_posts() && 'grid' == esc_attr( czr_fn_opt( 'tc_post_list_grid') ) && $this -> czr_fn_is_grid_context_matching() );
    // }


    // /* returns the type of post list we're in if any, an empty string otherwise */
    // private function czr_fn_get_grid_context() {
    //   global $wp_query;

    //   if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
    //           $wp_query->is_posts_page )
    //       return 'blog';
    //   else if ( is_search() && $wp_query->post_count > 0 )
    //       return 'search';
    //   else if ( is_archive() )
    //       return 'archive';
    //   return '';
    // }

    // /* performs the match between the option where to use post list grid
    //  * and the post list we're in */
    // private function czr_fn_is_grid_context_matching() {
    //   $_type = $this -> czr_fn_get_grid_context();
    //   $_apply_grid_to_post_type = apply_filters( 'czr_grid_in_' . $_type, esc_attr( czr_fn_opt( 'tc_grid_in_' . $_type ) ) );
    //   return apply_filters('czr_grid_do',  $_type && $_apply_grid_to_post_type );
    // }

  }//end of class
endif;
