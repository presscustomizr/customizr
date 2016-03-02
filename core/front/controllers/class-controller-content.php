<?php
if ( ! class_exists( 'TC_controller_content' ) ) :
  class TC_controller_content extends TC_controllers {
    static $instance;
    static $_cache = array();

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }
 
    function tc_display_view_right_sidebar() {
      if ( ! isset( $_cache['right_sidebar'] ) )  
        self::$_cache['right_sidebar'] = $this -> tc_display_view_sidebar( 'right' );  
      return self::$_cache['right_sidebar'];
    }

    function tc_display_view_left_sidebar() {
      if ( ! isset( $_cache['left_sidebar'] ) )  
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

    function tc_display_view_posts_list_title() {
      return $this -> tc_display_view_posts_list_headings();   
    }
    function tc_display_view_posts_list_description() {
      return $this -> tc_display_view_posts_list_headings();   
    }

    function tc_display_view_page() {
      return apply_filters( 'tc_show_page_content',
        'page' == $this -> tc_get_post_type()
        && is_singular()
        && ! $this -> tc_is_home_empty()
      );
    }

    function tc_display_view_404() {
      return is_404();
    }

    function tc_display_view_headings() {
      return true;
    }

  }//end of class
endif;
