<?php
if ( ! class_exists( 'TC_controller_modules' ) ) :
  class TC_controller_modules extends TC_controllers {
    static $instance;
    private static $_cache = array();

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call TC_controllers constructor?
      //why this class extends TC_controllers?
    }

    function tc_display_view_social_block( $model ) {
      static $socials_map = array(
        //structural hook => option filter
        '__widget_area_left__'  => 'left-sidebar',
        '__widget_area_right__' => 'right-sidebar',
        '__navbar__'            => 'header',
        '__colophon_one__'      => 'footer'
      );

      //the block must be instanciated when
      //1) IS customizing or no model hook set
      //or
      //2a) the block is displayed in a non-standard (not option mapped) structural hook
      //and
      //2b) There are social icons set
      //or
      //3a) the relative display option IS unchecked ( matching the map array above )
      //and
      //3b) There are social icons set

      //(1)
      if ( TC___::$instance -> tc_is_customizing() )
        return true;

      $_socials = TC_utils::$inst -> tc_get_social_networks();
      //(2a)
      if ( ! isset( $socials_map[ $model['hook'] ] ) )
        return (bool) $_socials;

      //(3b)
      return ( 1 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_{$socials_map[ $model['hook'] ]}" ) ) && tc__f('__get_socials') );
    }


    /* BREADCRUMB */
    function tc_display_view_breadcrumb() {
      if ( ! apply_filters( 'tc_show_breadcrumb' , 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_breadcrumb') ) ) )
        return false;

      if ( TC_utils::$inst -> tc_is_home() )
        return 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_breadcrumb_home' ) ) ? false : true;
      if ( is_page() && 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_breadcrumb_in_pages' ) ) )
        return false;

      if ( is_single() && 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_breadcrumb_in_single_posts' ) ) )
        return false;

      if ( ! is_page() && ! is_single() && 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_breadcrumb_in_post_lists' ) ) )
        return false;

      return true;
    }


    function tc_display_view_comment_bubble( $model ) {
      if ( ! isset( self::$_cache['comment_bubble'] ) ) {
        self::$_cache[ 'comment_bubble' ] = (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_show_bubble' ) )
          && (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_show_comment_list' ) )
          && (bool) apply_filters( 'tc_comments_in_title', true );
      }

      if ( 'singular_comment_bubble' == $model['id'] ) {
        return is_singular() && self::$_cache['comment_bubble'] && $this -> tc_are_comments_enabled() &&
                  in_array( get_post_type(), apply_filters('tc_show_comment_bubbles_for_post_types' , array( 'post' , 'page') ) );
          //we need the comments enabled and post list controller here! why don't we require all the controllers?...they won't be a load
      }
      //when in a list of posts demand the control to the model
      return self::$_cache['comment_bubble'] && $this -> tc_display_view_post_list() ;
    }

    /* FOLLOWING COPIED FROM THE CONTENT CONTROLLER CLASS */
    /******************************
    VARIOUS HELPERS
    *******************************/
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
    public function tc_are_comments_enabled() {
      if ( ! isset(self::$_cache['comments_enabled'] ) ) {

        global $post;
        // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
        if ( isset( $post ) ) {
          $_bool = ( post_password_required() || TC_utils::$inst -> tc_is_home() || ! is_singular() )  ? false : true;

          //2) if user has enabled comment for this specific post / page => true
          //@todo contx : update default value user's value)
          $_bool = ( 'closed' != $post -> comment_status ) ? true && $_bool : $_bool;

          //3) check global user options for pages and posts
          if ( is_page() )
            $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_page_comments' )) && $_bool;
          else
            $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_comments' )) && $_bool;
        } else
          $_bool = false;

        self::$_cache['comments_enabled'] = $_bool;
      }
      return apply_filters( 'tc_are_comments_enabled', self::$_cache['comments_enabled'] );
    }

    function tc_display_view_post_list() {
      global $wp_query;
      //must be archive or search result. Returns false if home is empty in options.
      return apply_filters( 'tc_post_list_controller',
        ! is_singular()
        && ! is_404()
        && 0 != $wp_query -> post_count
        && ! $this -> tc_is_home_empty()
      );
    }

    function tc_display_view_post_list_grid() {
      $bool = apply_filters( 'tc_is_grid_enabled', 'grid' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_grid') ) && $this -> tc_is_grid_context_matching() );
      //hack until we implement the "routers"
      add_filter( 'tc_is_not_grid', $bool ? '__return_false' : '__return_true' );
      return $bool;
    }
    

    /* returns the type of post list we're in if any, an empty string otherwise */
    private function tc_get_grid_context() {
      global $wp_query;
        
      if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
              $wp_query->is_posts_page )
          return 'blog';
      else if ( is_search() && $wp_query->post_count > 0 )
          return 'search';
      else if ( is_archive() )
          return 'archive';
      return '';
    }

    /* performs the match between the option where to use post list grid
     * and the post list we're in */
    private function tc_is_grid_context_matching() {
      $_type = $this -> tc_get_grid_context();
      $_apply_grid_to_post_type = apply_filters( 'tc_grid_in_' . $_type, esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_in_' . $_type ) ) );
      return apply_filters('tc_grid_do',  $_type && $_apply_grid_to_post_type );
    }

  }//end of class
endif;
