<?php
if ( ! class_exists( 'TC_controller_footer' ) ) :
  class TC_controller_footer extends TC_controllers {
    static $instance;
    private $_cache;

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call TC_controllers constructor?
      //why this class extends TC_controllers?
      
      //init the cache
      $this -> _cache = array();
    }

    function tc_display_view_footer_socials() {
      //the block must be not instanciated when 
      //1) NOT customizing 
      //and
      //2a) the relative display option is unchecked
      //or
      //2b) there are no social icons set
      return ! ( ! TC___::$instance -> tc_is_customizing() && 
            ( ( 0 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_footer" ) ) ) || ! tc__f('__get_socials') ) );
    }

    function tc_display_view_btt_arrow() {
      if ( ! isset( $this -> _cache[ 'btt_arrow_view' ] ) )
        $this -> _cache[ 'btt_arrow_view' ] = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_back_to_top' ) );
      return $this -> _cache[ 'btt_arrow_view' ];
    }
    //display btt link only if btt arrow disallowed
    function tc_display_view_footer_btt() { 
      return ! $this -> tc_display_view_btt_arrow();    
    }
  }//end of class
endif;
