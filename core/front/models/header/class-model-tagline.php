<?php
class CZR_tagline_model_class extends CZR_Model {

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_attributes' ] = ( czr_fn_is_customizing() && 0 == esc_attr( czr_fn_get_opt( 'tc_show_tagline') ) ) ? 'style="display:none;"' : '';
    return $model;
  }
  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function czr_fn_maybe_filter_views_model() {
    parent::czr_fn_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header', array( $this, 'pre_rendering_view_header_cb' ) );
  }
  /**
  * parse header model before rendering to add 'destkop'&&'sticky' tagline visibility class
  */
  function pre_rendering_view_header_cb( $header_model ) {
    //fire once, as it is shared with the mobile tagline
    //tagline display on sticky header
    //fire once
    static $_fired = false;
    if ( $_fired ) return;
    $_fired        = true;

    if ( esc_attr( czr_fn_get_opt( "tc_sticky_header") || czr_fn_is_customizing() ) ) {
      $_class =        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_tagline') ) ? 'tc-tagline-on' : 'tc-tagline-off';
      if ( ! is_array( $header_model -> element_class ) )
        $header_model -> element_class = explode( ' ', $header_model -> element_class );
      array_push( $header_model -> element_class, $_class );
    }
  }
}