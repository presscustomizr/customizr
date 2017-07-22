<?php
if ( ! class_exists( 'CZR_controller_footer' ) ) :
  class CZR_controller_footer extends CZR_controllers {
    static $instance;

    function czr_fn_display_view_footer_push () {
      return esc_attr( czr_fn_opt( 'tc_sticky_footer') ) || czr_fn_is_customizing();
    }

    function czr_fn_display_view_footer_social_block() {
      return ( 1 == esc_attr( czr_fn_opt( "tc_social_in_footer" ) ) ) &&
        ( czr_fn_is_customize_preview_frame() || czr_fn_has_social_links() );
    }

    function czr_fn_display_view_btt_arrow() {
      return esc_attr( czr_fn_opt( 'tc_show_back_to_top' ) );
    }

    function czr_fn_display_view_footer_widgets() {
      $footer_widgets = apply_filters( 'czr_footer_widgets', CZR_init::$instance -> footer_widgets );
      foreach ( $footer_widgets as $key => $area ) {
        if ( is_active_sidebar( $key ) ) {
          return apply_filters( 'tc_has_footer_widgets', true );
        }
      }

      return apply_filters( 'tc_has_footer_widgets', false );
    }

  }//end of class
endif;
