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
      //FEATURED PAGES
      if ( $this -> tc_are_featured_pages_on() )
        tc_new( array('content' => array( array('inc/parts', 'featured_pages') ) ) );

      //BREADCRUMB
      if ( $this -> tc_is_breadcrumb_on() )
        tc_new( array('content' => array( array('inc/parts', 'breadcrumb') ) ) );

      //HEADINGS
      if ( $this -> tc_has_view_heading() )
        tc_new( array('content' => array( array('inc/parts', 'headings') ) ) );

      //PAGE
      if ( $this -> tc_is_page() )
        tc_new( array('content' => array( array('inc/parts', 'page') ) ) );

      //SINGLE POST
      if ( $this -> tc_is_single_post() )
        tc_new( array('content' => array( array('inc/parts', 'post') ) ) );

      //SINGLE ATTACHMENT
      if ( $this -> tc_is_single_attachment() )
        tc_new( array('content' => array( array('inc/parts', 'attachment') ) ) );

      //POST LIST (IF GRID IS NOT ENABLED)
      if ( $this -> tc_is_post_list() && ! $this -> tc_is_grid_enabled() )
        tc_new( array('content' => array( array('inc/parts', 'post_list') ) ) );

      //POST LIST GRID
      if ( $this -> tc_is_grid_enabled() )
        tc_new( array('content' => array( array('inc/parts', 'post_list_grid') ) ) );

      //404
      if ( is_404() )
        tc_new( array('content' => array( array('inc/parts', '404') ) ) );

      //NO SEARCH RESULTS
      if ( $this -> tc_is_no_search_results() )
        tc_new( array('content' => array( array('inc/parts', 'no_results') ) ) );

      //COMMENTS
      if ( $this -> tc_are_comments_enabled() )
        tc_new( array('content' => array( array('inc/parts', 'comments') ) ) );

      //GALLERY
      if ( $this -> tc_is_gallery_eligible() )
        tc_new( array('content' => array( array('inc/parts', 'gallery') ) ) );

      //COMMENT BUBBLES
      if ( $this -> tc_are_comment_bubbles_on() )
        tc_new( array('content' => array( array('inc/parts', 'comment_bubbles') ) ) );

      //POST NAVIGATION
      if ( $this -> tc_are_post_nav_on() )
        tc_new( array('content' => array( array('inc/parts', 'post_navigation') ) ) );
    }



    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/
    /***************************************************************************************************************
    * PAGE
    ***************************************************************************************************************/
    /**
    * Page view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_is_page() {
      return apply_filters( 'tc_show_page_content',
        'page' == tc__f('__post_type')
        && is_singular()
        && ! tc__f( '__is_home_empty')
      );
    }

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
    * SINGLE ATTACHMENT
    ***************************************************************************************************************/
    function tc_is_single_attachment() {
      global $post;
      return isset($post) && ! empty($post) && 'attachment' == $post -> post_type && is_singular();
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


    /***************************************************************************************************************
    * HEADINGS
    ***************************************************************************************************************/
    //by default don't display the Customizr title in feeds
    function tc_has_view_heading(){
      return apply_filters(
        'tc_display_customizr_headings',
        ! is_feed()
        && ( $this -> tc_is_heading_archive() || $this -> tc_is_post_page_heading() )
      );
    }

    function tc_is_heading_archive() {
      global $wp_query;
      return ( $wp_query -> is_posts_page && ! is_front_page() )//case page for posts but not on front
        || is_404()
        || ( is_search() && ! is_singular() )
        || is_archive();
    }

    //by default don't display the Customizr title of the front page
    function tc_is_post_page_heading() {
      return ! ( is_front_page() && 'page' == get_option( 'show_on_front' ) );
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
    * COMMENTS
    ***************************************************************************************************************/
    /**
    * 1) if the page / post is password protected OR if is_home OR ! is_singular() => false
    * 2) if comment_status == 'closed' => false
    * 3) if user defined comment option in customizer == false => false
    *
    * By default, comments are globally disabled in pages and enabled in posts
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_are_comments_enabled() {
      global $post;
      // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
      if ( isset( $post ) ) {
        $_bool = ( post_password_required() || tc__f( '__is_home' ) || ! is_singular() )  ? false : true;

        //2) if user has enabled comment for this specific post / page => true
        //@todo contx : update default value user's value)
        $_bool = ( 'closed' != $post -> comment_status ) ? true : $_bool;

        //3) check global user options for pages and posts
        if ( is_page() )
          $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_page_comments' )) && $_bool;
        else
          $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_comments' )) && $_bool;
      } else
        $_bool = false;

      return apply_filters( 'tc_are_comments_enabled', $_bool );
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
    * NO SEARCH RESULTS
    ***************************************************************************************************************/
    function tc_is_no_search_results() {
      global $wp_query;
      return is_search() && 0 == $wp_query -> post_count;
    }



    /***************************************************************************************************************
    * POST NAVIGATION
    ***************************************************************************************************************/
    function tc_are_post_nav_on(){
      $_context                  = $this -> tc_get_context();
      $_post_nav_enabled         = $this -> tc_is_post_navigation_enabled();
      $_post_nav_context_enabled = $this -> tc_is_post_navigation_context_enabled( $_context );

      if ( TC___::$instance -> tc_is_customizing() )
        $_post_nav_enabled       = true;
      else
        $_post_nav_enabled       = $_post_nav_enabled && $_post_nav_context_enabled;

      return apply_filters( 'tc_show_post_navigation', $_post_nav_enabled );
    }


    /**
    *
    * @return string or bool
    *
    */
    function tc_get_context(){
      if ( is_page() )
        return 'page';
      if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
      if ( !is_404() && !tc__f( '__is_home_empty') )
        return 'archive';

      return false;

    }

    /*
    * @param (string or bool) the context
    * @return bool
    */
    function tc_is_post_navigation_context_enabled( $_context ) {
      return $_context && 1 == esc_attr( TC_utils::$inst -> tc_opt( "tc_show_post_navigation_{$_context}" ) );
    }

    /*
    * @return bool
    */
    function tc_is_post_navigation_enabled(){
      return 1 == esc_attr( TC_utils::$inst -> tc_opt( 'tc_show_post_navigation' ) ) ;
    }

  }//end of class
endif;