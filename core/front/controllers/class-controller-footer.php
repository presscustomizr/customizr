<?php
if ( ! class_exists( 'CZR_controller_footer' ) ) :
  class CZR_controller_footer extends CZR_controllers {
    static $instance;

    function czr_fn_display_view_footer_push() {
      return esc_attr( czr_fn_opt( 'tc_sticky_footer') ) || czr_fn_is_customizing();
    }

    function czr_fn_display_view_footer_social_block() {
      return ( 1 == esc_attr( czr_fn_opt( "tc_social_in_footer" ) ) ) &&
        ( czr_fn_is_customize_preview_frame() || czr_fn_has_social_links() );
    }

    function czr_fn_display_view_btt_arrow() {
      return esc_attr( czr_fn_opt( 'tc_show_back_to_top' ) );
    }



    function czr_fn_display_view_footer_horizontal_widgets() {
      if ( 'none' == czr_fn_opt( 'tc_footer_horizontal_widgets' ) ) {
        return false;
      }


      if ( is_active_sidebar( 'footer_horizontal' ) ) {
        $to_display = true;
      }else {
        //If not widgets still display in preview (will display placeholders) when not prevdem
        $to_display = czr_fn_is_customize_preview_frame() && !czr_fn_isprevdem();
      }

      return apply_filters( 'czr_has_footer_horizontal_widgets', $to_display );
    }



    function czr_fn_display_view_footer_widgets() {
      $footer_widgets = apply_filters( 'czr_footer_widgets', CZR_init::$instance -> footer_widgets );
      foreach ( $footer_widgets as $key => $area ) {
        if ( is_active_sidebar( $key ) ) {
          return apply_filters( 'czr_has_footer_widgets', true );
        }
      }

      //If not widgets still display in preview (will display placeholders) when not prevdem
      $to_display = czr_fn_is_customize_preview_frame() && !czr_fn_isprevdem();
      return apply_filters( 'czr_has_footer_widgets', $to_display );
    }

  }//end of class
endif;
