<?php
/**
* FIRED ON 'after_setup_theme'
* Before the query is ready
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
if ( ! class_exists( 'TC_early_hooks' ) ) :
  class TC_early_hooks extends TC_base {
      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
        self::$instance =& $this;
        /***************************************************************************************************************
        * POST LISTS EARLY HOOKS
        ***************************************************************************************************************/
        //Set new image size can be set here ( => wp hook would be too late) (since 3.2.0)
        add_action( 'init'                    , array( $this, 'tc_set_post_lists_thumb_early_options') );
        //modify the query with pre_get_posts
        //! wp_loaded is fired after WordPress is fully loaded but before the query is set
        add_action( 'wp_loaded'               , array( $this, 'tc_set_post_lists_early_hooks') );



        /***************************************************************************************************************
        * POST LISTS GRID EARLY HOOKS
        ***************************************************************************************************************/
        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_set_expanded_sticky_bool_and_val') );
        //must be fired after the bool and the val properties have been set
        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_maybe_excl_first_sticky'), 20 );

      }//constructor




      /***************************************************************************************************************
      * POST LISTS EARLY ACTIONS
      ***************************************************************************************************************/
      /**
      * hook : init
      * @return void
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_post_lists_thumb_early_options() {
        //Set thumb size depending on the customizer thumbnail position options (since 3.2.0)
        add_filter ( 'tc_thumb_size_name'     , array( $this , 'tc_set_thumb_size') );
      }


      /**
      * Set __loop hooks and various filters based on customizer options
      * hook : wp_loaded
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_post_lists_early_hooks() {
        //Filter home/blog postsa (priority 9 is to make it act before the grid hook for expanded post)
        add_action ( 'pre_get_posts'         , array( $this , 'tc_filter_home_blog_posts_by_tax' ), 9);
        //Include attachments in search results
        add_action ( 'pre_get_posts'         , array( $this , 'tc_include_attachments_in_search' ));
        //Include all post types in archive pages
        add_action ( 'pre_get_posts'         , array( $this , 'tc_include_cpt_in_lists' ));
      }


      /***************************************************************************************************************
      * POST LISTS EARLY CALLBACKS
      ***************************************************************************************************************/
      /**
      * hook : tc_thumb_size_name (declared in TC_post_thumbnails)
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_thumb_size( $_default_size ) {
        $_shape = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') );
        if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
          return $_default_size;

        $_position                  = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
        return ( 'top' == $_position || 'bottom' == $_position ) ? 'tc_rectangular_size' : $_default_size;
      }


      /**
      * hook : pre_get_posts
      * Filter home/blog posts by tax: cat
      * @return modified query object
      * @package Customizr
      * @since Customizr 3.4.10
      */
      function tc_filter_home_blog_posts_by_tax( $query ) {
          // when we have to filter?
          // in home and blog page
          if (
            ! $query->is_main_query()
            || ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $query->is_posts_page )
          )
            return;

         // categories
         // we have to ignore sticky posts (do not prepend them)
         // disable grid sticky post expansion
         $cats = TC_utils::$inst -> tc_opt('tc_blog_restrict_by_cat');
         $cats = array_filter( $cats, array( TC_utils::$inst , 'tc_category_id_exists' ) );

         if ( is_array( $cats ) && ! empty( $cats ) ){
             $query->set('category__in', $cats );
             $query->set('ignore_sticky_posts', 1 );
             add_filter('tc_grid_expand_featured', '__return_false');
         }
      }


      /**
      * hook : pre_get_posts
      * Includes attachments in search results
      * @return modified query object
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_include_attachments_in_search( $query ) {
          if (! is_search() || ! apply_filters( 'tc_include_attachments_in_search_results' , false ) )
            return;

          // add post status 'inherit'
          $post_status = $query->get( 'post_status' );
          if ( ! $post_status || 'publish' == $post_status )
            $post_status = array( 'publish', 'inherit' );
          if ( is_array( $post_status ) )
            $post_status[] = 'inherit';

          $query->set( 'post_status', $post_status );
      }


      /**
      * hook : pre_get_posts
      * Includes Custom Posts Types (set to public and excluded_from_search_result = false) in archives and search results
      * In archives, it handles the case where a CPT has been registered and associated with an existing built-in taxonomy like category or post_tag
      * @return modified query object
      * @package Customizr
      * @since Customizr 3.1.20
      */
      function tc_include_cpt_in_lists( $query ) {
        if (
          is_admin()
          || ! $query->is_main_query()
          || ! apply_filters('tc_include_cpt_in_archives' , false)
          || ! ( $query->is_search || $query->is_archive )
          )
          return;

        //filter the post types to include, they must be public and not excluded from search
        //we also exclude the built-in types, to exclude pages and attachments, we'll add standard posts later
        $post_types         = get_post_types( array( 'public' => true, 'exclude_from_search' => false, '_builtin' => false) );

        //add standard posts
        $post_types['post'] = 'post';
        if ( $query -> is_search ){
          // add standard pages in search results => new wp behavior
          $post_types['page'] = 'page';
          // allow attachments to be included in search results by tc_include_attachments_in_search method
          if ( apply_filters( 'tc_include_attachments_in_search_results' , false ) )
            $post_types['attachment'] = 'attachment';
        }

        // add standard pages in search results
        $query->set('post_type', $post_types );
      }



      /***************************************************************************************************************
      * POST LISTS GRID EARLY ACTIONS
      ***************************************************************************************************************/
      /**
      * hook : pre_get_posts : 10
      *
      * @return void()
      * check if we have to expand the first sticky post
      * set 2 properties accessible in the grid child class
      */
      public function tc_grid_set_expanded_sticky_bool_and_val( $query = null ){
        //user option has to be enabled
        if ( ! apply_filters( 'tc_grid_expand_featured', esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_expand_featured') ) ) )
          return;

        global $wp_query, $wpdb;
        $query = ( $query ) ? $query : $wp_query;

        if ( ! $query->is_main_query() )
          TC_base::$expanded_sticky_bool = false;
        if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $wp_query->is_posts_page ) )
          TC_base::$expanded_sticky_bool = false;



        if ( ! TC_base::$expanded_sticky_bool ) {
          $_sticky_posts = get_option('sticky_posts');
          // get last published sticky post
          if ( is_array($_sticky_posts) && ! empty( $_sticky_posts ) ) {
            $_where = implode(',', $_sticky_posts );
            TC_base::$expanded_sticky_val = $wpdb->get_var(
                   "
                   SELECT ID
                   FROM $wpdb->posts
                   WHERE ID IN ( $_where )
                   ORDER BY post_date DESC
                   LIMIT 1
                   "
            );
            TC_base::$expanded_sticky_bool = true;
          } else {
            TC_base::$expanded_sticky_val = null;
            TC_base::$expanded_sticky_bool = false;
          }
        }
      }


      /**
      * hook : pre_get_posts : 20
      * exclude the first sticky post
      */
      function tc_grid_maybe_excl_first_sticky( $query ){
        if ( TC_controller::$instance -> tc_is_grid_enabled() && TC_base::$expanded_sticky_bool )
          $query->set('post__not_in', array( TC_base::$expanded_sticky_val ) );
      }
  }//end of class
endif;