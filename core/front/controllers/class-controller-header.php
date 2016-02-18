<?php
if ( ! class_exists( 'TC_controller_header' ) ) :
  class TC_controller_header extends TC_controllers {
    static $instance;

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }

    function tc_display_view_head() {
      return true;
    }
    function tc_display_view_menu() {
      return true;
    }

    function tc_display_view_sticky_logo() {
      return false;
    }
  }//end of class
endif;
