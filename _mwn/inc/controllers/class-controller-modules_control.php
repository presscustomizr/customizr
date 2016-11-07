<?php
/**
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
if ( ! class_exists( 'TC_modules_control' ) ) :
  class TC_modules_control extends TC_control_base {
    static $instance;

    //grid specifics
    //are set from the early hooks child on pre_get_posts
    //and used in the post_list_grid child
    static $expanded_sticky_bool = false;
    static $expanded_sticky_val = null;

    function __construct( $_args = array() ) {
      self::$instance =& $this;

      //Instanciates the parent class.
      parent::__construct( $_args );
    }



    /***************************************************************************************************************
    * FIRE RELEVANT VIEW
    ***************************************************************************************************************/
    //hook : 'wp'
    function tc_fire_views_on_query_ready() {
      if ( is_admin() )
        return;
      //THUMBNAILS
      tc_new( array( 'module' => array( array('inc/views/modules', 'post_thumbnails') ) ) );

      //SLIDER
      if ( $this -> tc_is_slider_possible() )
        tc_new( array('module' => array( array('inc/views/modules', 'slider') ) ) );

      //FEATURED PAGES
      if ( $this -> tc_are_featured_pages_on() )
        tc_new( array('module' => array( array('inc/views/modules', 'featured_pages') ) ) );

      //BREADCRUMB
      if ( $this -> tc_is_breadcrumb_on() )
        tc_new( array('module' => array( array('inc/views/modules', 'breadcrumb') ) ) );

      //POST LIST GRID
      if ( $this -> tc_is_grid_enabled() )
        tc_new( array('module' => array( array('inc/views/modules', 'post_list_grid') ) ) );

      //GALLERY
      if ( $this -> tc_is_gallery_eligible() )
        tc_new( array('module' => array( array('inc/views/modules', 'gallery') ) ) );

      //COMMENT BUBBLES
      if ( $this -> tc_are_comment_bubbles_on() )
        tc_new( array('module' => array( array('inc/views/modules', 'comment_bubbles') ) ) );
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


    /***************************************************************************************************************
    * BREADCRUMB
    ***************************************************************************************************************/
    function tc_is_breadcrumb_on() {
      return apply_filters( 'tc_show_breadcrumb',
        1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_breadcrumb') )
        && apply_filters( 'tc_show_breadcrumb_in_context' , true )
        && ! ( tc__f('__is_home') && 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_breadcrumb_home' ) ) )
      );
    }


    /***************************************************************************************************************
    * FEATURED PAGES
    ***************************************************************************************************************/
    function tc_are_featured_pages_on(){
      //gets display fp option
      $tc_show_featured_pages         = esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages' ) );

      return apply_filters( 'tc_show_fp', 0 != $tc_show_featured_pages && tc__f('__is_home') );
    }


    /***************************************************************************************************************
    * GALLERY
    ***************************************************************************************************************/
    function tc_is_gallery_eligible(){
      return is_singular() && apply_filters('tc_enable_gallery', esc_attr( TC_utils::$inst -> tc_opt('tc_enable_gallery') ) );
    }


    /***************************************************************************************************************
    * COMMENT BUBBLES
    ***************************************************************************************************************/
    /**
    * When are we displaying the comment bubble ?
    * - Must be in the loop
    * - Bubble must be enabled by user
    * - comments are enabled
    * - there is at least one comment
    * - the comment list option is enabled
    * - post type is in the eligible post type list : default = post
    * - tc_comments_in_title boolean filter is true
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_are_comment_bubbles_on() {
      $_bool_arr = array(
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_show_bubble' ) ),
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_show_comment_list' ) ),
        (bool) apply_filters( 'tc_comments_in_title', true )
      );
      return (bool) array_product($_bool_arr);
    }





    /***************************************************************************************************************
    * SLIDER
    ***************************************************************************************************************/
    //Do we have a slider to display in this context ?
    function tc_is_slider_possible() {
      //gets the front slider if any
      $tc_front_slider              = esc_attr(TC_utils::$inst->tc_opt( 'tc_front_slider' ) );
      //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types)
      $_show_slider = tc__f('__is_home') ? ! empty( $tc_front_slider ) : ! is_404() && ! is_archive() && ! is_search();
      //gets the actual page id if we are displaying the posts page
      $queried_id                   = $this -> tc_get_real_id();

      return apply_filters( 'tc_show_slider' , $_show_slider && $this -> tc_is_slider_active( $queried_id) );
    }


    /**
    * helper
    * returns the actual page id if we are displaying the posts page
    * @return  boolean
    *
    */
    function tc_is_slider_active( $queried_id ) {
      //is the slider set to on for the queried id?
      if ( tc__f('__is_home') && TC_utils::$inst->tc_opt( 'tc_front_slider' ) )
        return apply_filters( 'tc_slider_active_status', true , $queried_id );

      $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );
      if ( ! empty( $_slider_on ) && $_slider_on )
        return apply_filters( 'tc_slider_active_status', true , $queried_id );

      return apply_filters( 'tc_slider_active_status', false , $queried_id );
    }




    /***************************************************************************************************************
    * HELPERS
    ***************************************************************************************************************/
    /**
    * helper
    * returns the actual page id if we are displaying the posts page
    * @return  number
    *
    */
    function tc_get_real_id() {
      global $wp_query;
      $queried_id  = get_queried_object_id();
      return ( ! tc__f('__is_home') && $wp_query -> is_posts_page && ! empty($queried_id) ) ? $queried_id : get_the_ID();
    }

  }//end of class
endif;