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
      if ( ! esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_logo_upload") ) )
        return false;
      if ( ! ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") ) &&
        esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') )
      ) )
        return false;
      return true;
    }
  }//end of class
endif;
