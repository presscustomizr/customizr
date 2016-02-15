<?php
if ( ! class_exists( 'TC_controller_content' ) ) :
  class TC_controller_content extends TC_controllers {
    static $instance;

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }


    function tc_display_view_page() {
      return apply_filters( 'tc_show_page_content',
        'page' == $this -> tc_get_post_type()
        && is_singular()
        && ! $this -> tc_is_home_empty()
      );
    }


  }//end of class
endif;