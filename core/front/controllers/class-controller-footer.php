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

    function tc_display_view_footer_push () {
      return esc_attr( TC_utils::$inst -> tc_opt( 'tc_sticky_footer') ) || TC___::$instance -> tc_is_customizing();
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

    function tc_display_view_footer_widgets_wrapper() {
      $footer_widgets = apply_filters( 'tc_footer_widgets', TC_init::$instance -> footer_widgets );
      foreach ( $footer_widgets as $key => $area ) {
        if ( is_active_sidebar( $key ) )
          return true;
      }

      return false;
    }

  }//end of class
endif;
