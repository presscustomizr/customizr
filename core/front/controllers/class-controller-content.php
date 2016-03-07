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
        //TODO: better way
      return ! is_singular();
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

    function tc_display_view_404() {
      return is_404();
    }

    function tc_display_view_headings() {
      return true;
    }

  }//end of class
endif;
