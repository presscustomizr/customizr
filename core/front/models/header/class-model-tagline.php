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

    //build tagline class:
    $_class   = array( 'site-description' );
    if ( isset( $model['type'] ) && 'mobile' == $model['type'] )
      $_class = array_merge( $_class, array( 'inside', 'span7' ) );

    $model[ 'class' ]   = implode( ' ', apply_filters( 'tc_tagline_class', $_class, $model ) );
    return $model;
  }
}
