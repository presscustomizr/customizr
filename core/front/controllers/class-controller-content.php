<?php
if ( ! class_exists( 'TC_controller_content' ) ) :
  class TC_controller_content extends TC_controllers {
    static $instance;
    static $_cache = array();

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }
 
    function tc_display_view_right_sidebar() {
      if ( ! isset( self::$_cache['right_sidebar'] ) )  
        self::$_cache['right_sidebar'] = $this -> tc_display_view_sidebar( 'right' );  
      return self::$_cache['right_sidebar'];
    }

    function tc_display_view_left_sidebar() {
      if ( ! isset( self::$_cache['left_sidebar'] ) )  
        self::$_cache['left_sidebar'] = $this -> tc_display_view_sidebar( 'left' );  
      return self::$_cache['left_sidebar'];
    }

    private function tc_display_view_sidebar( $position ) {
      if ( TC_utils::$inst -> tc_is_home_empty() )
        return false;

      static $sidebar_map = array(
        //id => allowed layout (- b both )
        'right'  => 'r',
        'left'   => 'l'
      );
      
      $screen_layout        = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar'  );
      if ( ! in_array( $screen_layout, array( $sidebar_map[$position], 'b' ) ) )
        return false;
      return true;
    }
 
    function tc_display_view_posts_list_headings() {
      if ( ! isset( self::$_cache['posts_list_headings'] ) ) {
        global $wp_query;  
        self::$_cache['posts_list_headings'] = ( $wp_query -> is_posts_page && ! is_front_page() ) ||
            is_archive(); 
      }
      return self::$_cache['posts_list_headings'];
    }

    function tc_display_view_post_list() {
      global $wp_query;
      //must be archive or search result. Returns false if home is empty in options.
      return apply_filters( 'tc_post_list_controller',
        ! is_singular()
        && ! is_404()
        && 0 != $wp_query -> post_count
        && ! tc__f( '__is_home_empty')
      );
    }

    function tc_display_view_posts_list_title() {
      return $this -> tc_display_view_posts_list_headings();   
    }
    function tc_display_view_posts_list_description() {
      return $this -> tc_display_view_posts_list_headings();   
    }

    function tc_display_view_page() {
      if ( ! isset( self::$_cache['page'] ) )  
        self::$_cache['page'] =  'page' == $this -> tc_get_post_type()
        && is_singular()
        && ! $this -> tc_is_home_empty();
      
      return apply_filters( 'tc_show_page_content', self::$_cache['page'] );
    }

    function tc_display_view_post() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      if ( ! isset( self::$_cache['post'] ) )
        self::$_cache['post'] = isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! tc__f( '__is_home_empty');
      return apply_filters( 'tc_show_single_post_content', self::$_cache['post'] );
    }


    function tc_display_view_singular_article() {
      return $this -> tc_display_view_post() || $this -> tc_display_view_page();  
    }

    function tc_display_view_post_list_title() {
      return apply_filters('tc_display_customizr_headings', $this -> tc_display_view_posts_list_headings() || is_front_page() );
    } 

    function tc_display_view_singular_title() {
      if ( ! isset( self::$_cache['singular_title'] ) )
        self::$_cache['singular_title'] =  is_singular() && ! ( is_front_page() && 'page' == get_option( 'show_on_front' ) );
      return apply_filters('tc_display_customizr_headings', self::$_cache['singular_title'] )  && ! is_feed();
    }


    function tc_display_view_post_metas() {
     if ( isset( self::$_cache['post_metas'] ) )
       return self::$_cache['post_metas'];

     //post metas are always insanciated in customizing context
     if ( TC___::$instance -> tc_is_customizing() )
       self::$_cache['post_metas'] = true;

     elseif ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas' ) ) )
       self::$_cache['post_metas'] = false;

     elseif ( is_singular() && ! is_page() && ! tc__f('__is_home') )
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_single_post' ) ) ); 
     
     elseif ( ! is_singular() && ! tc__f('__is_home') && ! is_page() )
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_post_lists' ) ) );

     elseif ( TC_utils::$inst -> tc_is_home() ) 
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_home' ) ) );
     else
       self::$_cache['post_metas'] = false;

     return self::$_cache['post_metas'];
    }


    function tc_display_view_post_metas_text() {
      return $this -> tc_display_view_post_metas() && 'buttons' != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_design' ) );
    }

    function tc_display_view_post_metas_button() {
      return $this -> tc_display_view_post_metas() && 'buttons' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_design' ) );  
    }

    function tc_display_view_post_navigation_singular() {
      if ( TC___::$instance -> tc_is_customizing() )
        return true;
      if ( isset( self::$_cache['post_navigation_singular'] ) )
        return self::$_cache['post_navigation_singular'];

      self::$_cache['post_navigation_singular'] = false;
      if ( $this -> tc_is_post_navigation_enabled() ) {
        $_context = $this -> tc_get_post_navigation_context();

        self::$_cache['post_navigation_singular'] = in_array( $_context, array('page', 'single') ) ? $this -> tc_is_post_navigation_context_enabled( $_context ) : false;
      }

      return self::$_cache['post_navigation_singular'];
    }

    function tc_display_view_post_navigation_posts() {
      if ( TC___::$instance -> tc_is_customizing() )
        return true;
      if ( isset( self::$_cache['post_navigation_posts'] ) )
        return self::$_cache['post_navigation_posts'];

      self::$_cache['post_navigation_posts'] = false;
      if ( $this -> tc_is_post_navigation_enabled() ) {
        $_context = $this -> tc_get_post_navigation_context();
        self::$_cache['post_navigation_posts'] = in_array( $_context, array('home', 'archive') ) ? $this -> tc_is_post_navigation_context_enabled( $_context ) : false;
      }

      return self::$_cache['post_navigation_posts'];
    }
 
   /******************************
    VARIOUS HELPERS
    *******************************/
    /**
    *
    * @return string or bool
    *
    */
    function tc_get_post_navigation_context(){
      if ( is_page() )
        return 'page';
      if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
      if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
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

    function tc_display_view_404() {
      return is_404();
    }

    function tc_display_view_headings() {
      return true;
    }

  }//end of class
endif;
