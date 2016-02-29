<?php
class TC_tagline_model_class extends TC_Model {
  public $content;
  public $tag;
  public $class;
  public $attributes;
  public $type;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'content' ]    = apply_filters( 'tc_tagline_text', __( esc_attr( get_bloginfo( 'description' ) ) ), $model );
    $model[ 'tag']         = apply_filters( 'tc_tagline_tag', 'h2', $model );

    $model[ 'attributes' ] = ( TC___::$instance -> tc_is_customizing() && 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_tagline') ) ) ? 'style="display:none;"' : '';

    $model['type']         = isset( $model['params']['type'] ) ? $model['params']['type'] : '';
    //build tagline class:
    $_class   = array( 'site-description' );
    if ( 'mobile' != $model['type'] )
      $_class = array_merge( $_class, array( 'inside', 'span7' ) );

    $model[ 'class' ]     = apply_filters( 'tc_tagline_class', $_class, $model );
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
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', array_unique( $model -> class ) );
  }

  /**
  * parse header model before rendering to add 'destkop'&&'sticky' tagline visibility class
  */
  function pre_rendering_view_header_cb( $header_model ) {
    if ( 'mobile' != $this -> type && esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") || TC___::$instance -> tc_is_customizing() ) ) {
      $_class =        0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_tagline') ) ? 'tc-tagline-on' : 'tc-tagline-off';
      array_push( $header_model -> class, $_class );
    }
  }
}
