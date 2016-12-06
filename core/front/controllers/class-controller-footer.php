<?php
if ( ! class_exists( 'CZR_controller_footer' ) ) :
  class CZR_controller_footer extends CZR_controllers {
    static $instance;
    private $_cache;

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call CZR_controllers constructor?
      //why this class extends CZR_controllers?

      //init the cache
      $this -> _cache = array();
    }

    function czr_fn_display_view_footer_push () {
      return esc_attr( czr_fn_get_opt( 'tc_sticky_footer') ) || czr_fn_is_customizing();
    }

    function czr_fn_display_view_footer_social_block() {
      return ( 1 == esc_attr( czr_fn_get_opt( "tc_social_in_footer" ) ) ) &&
        ( czr_fn_is_customize_preview_frame() || czr_fn_is_possible('social_block') );
    }

    function czr_fn_display_view_btt_arrow() {
      if ( ! isset( $this -> _cache[ 'btt_arrow_view' ] ) )
        $this -> _cache[ 'btt_arrow_view' ] = 1 == esc_attr( czr_fn_get_opt( 'tc_show_back_to_top' ) );
      return $this -> _cache[ 'btt_arrow_view' ];
    }

    //display btt link only if btt arrow disallowed
    function czr_fn_display_view_footer_btt() {
      return ! $this -> czr_fn_display_view_btt_arrow();
    }

    function czr_fn_display_view_footer_widgets() {
      $footer_widgets = apply_filters( 'czr_footer_widgets', CZR_init::$instance -> footer_widgets );
      foreach ( $footer_widgets as $key => $area ) {
        if ( is_active_sidebar( $key ) )
          return true;
      }

      return false;
    }

  }//end of class
endif;
