<?php
class CZR_cl_tagline_model_class extends CZR_cl_Model {
  public $element_class     = array('inside', 'span7');
 // public $attributes;
  public $context           = 'desktop';

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_attributes' ] = ( CZR___::$instance -> tc_is_customizing() && 0 == esc_attr( CZR_cl_utils::$inst->czr_opt( 'tc_show_tagline') ) ) ? 'style="display:none;"' : '';

    $model[ 'element_class' ]      = apply_filters( 'tc_tagline_class', $this -> element_class, $model );
    return $model;
  }

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function tc_maybe_filter_views_model() {
    parent::tc_maybe_filter_views_model();
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

    if ( esc_attr( CZR_cl_utils::$inst->czr_opt( "tc_sticky_header") || CZR___::$instance -> tc_is_customizing() ) ) {
      $_class =        0 != esc_attr( CZR_cl_utils::$inst->czr_opt( 'tc_sticky_show_tagline') ) ? 'tc-tagline-on' : 'tc-tagline-off';
      if ( ! is_array( $header_model -> element_class ) )
        $header_model -> element_class = explode( ' ', $header_model -> element_class );
      array_push( $header_model -> element_class, $_class );
    }
  }
}
