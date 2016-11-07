<?php
if ( ! class_exists( 'TC_controller_header' ) ) :
  class TC_controller_header extends TC_controllers {
    static $instance;

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }


    function tc_display_view_joie() {
      return is_home() || is_front_page();
    }


  }//end of class
endif;