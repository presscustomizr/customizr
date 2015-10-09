<?php
/**
* LOOP CONTROLLER CLASS
* FIRED ON INIT
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_controller' ) ) :
  class TC_controller extends TC_base {
    static $instance;

    function __construct( $_args ) {
      self::$instance =& $this;

      add_action( 'wp' , array( $this, 'tc_fire_views_on_query_ready' ) );
    }



    /***************************************************************************************************************
    * FIRE RELEVANT VIEW
    ***************************************************************************************************************/
    //hook : 'wp'
    function tc_fire_views_on_query_ready() {
      if ( is_admin() )
        return;

      //SINGLE POST
      if ( $this -> tc_is_single_post() )
        tc_new( array('content' => array( array('inc/parts', 'post') ) ) );

      //POST LIST (IF GRID IS NOT ENABLED)
      if ( $this -> tc_is_post_list() && ! $this -> tc_is_grid_enabled() )
        tc_new( array('content' => array( array('inc/parts', 'post_list') ) ) );

      //POST LIST GRID
      if ( $this -> tc_is_grid_enabled() )
        tc_new( array('content' => array( array('inc/parts', 'post_list_grid') ) ) );

      //HEADINGS
      tc_new( array('content' => array( array('inc/parts', 'headings') ) ) );
    }



    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/
    /***************************************************************************************************************
    * SINGLE POST
    ***************************************************************************************************************/
    /**
    * Single post view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.2.0
    */
    static function tc_is_single_post() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      return apply_filters( 'tc_show_single_post_content',
        isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! tc__f( '__is_home_empty')
      );
    }


    /***************************************************************************************************************
    * POST LISTS
    ***************************************************************************************************************/
    /**
    * @return  bool
    * Controller of the posts list view
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    static function tc_is_post_list() {
      global $wp_query;
      //must be archive or search result. Returns false if home is empty in options.
      return apply_filters( 'tc_post_list_controller',
        ! is_singular()
        && ! is_404()
        && 0 != $wp_query -> post_count
        && ! tc__f( '__is_home_empty')
      );
    }


    /***************************************************************************************************************
    * POST LISTS GRID
    ***************************************************************************************************************/
    /*
    * @return bool
    */
    public function tc_is_grid_enabled() {
      return apply_filters( 'tc_is_grid_enabled', 'grid' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_grid') ) && $this -> tc_is_grid_context_matching() );
    }


    /* performs the match between the option where to use post list grid
     * and the post list we're in
    */
    private function tc_is_grid_context_matching() {
      $_type = $this -> tc_get_grid_context();
      $_apply_grid_to_post_type = apply_filters( 'tc_grid_in_' . $_type, esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_in_' . $_type ) ) );
      return apply_filters('tc_grid_do',  $_type && $_apply_grid_to_post_type );
    }


    /* returns the type of post list we're in if any, an empty string otherwise */
    private function tc_get_grid_context() {
      global $wp_query;
      if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
              $wp_query->is_posts_page )
          return 'blog';
      else if ( is_search() )
          return 'search';
      else if ( is_archive() )
          return 'archive';
      return '';
    }
  }//end of class
endif;