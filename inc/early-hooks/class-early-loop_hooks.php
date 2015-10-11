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
  class TC_early_hooks {
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
  }//end of class
endif;