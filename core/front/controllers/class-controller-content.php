<?php
if ( ! class_exists( 'TC_controller_content' ) ) :
  class TC_controller_content extends TC_controllers {
    static $instance;

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }

    function tc_display_view_right_sidebar() {
      return $this -> tc_display_view_sidebar( 'right' );  
    }

    function tc_display_view_left_sidebar() {
      return $this -> tc_display_view_sidebar( 'left' );  
    }

    private function tc_display_view_sidebar( $position ) {
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
