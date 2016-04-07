<?php
class TC_404_model_class extends TC_Model {
  public $wrapper_class;
  public $inner_class;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'wrapper_class' ]             = apply_filters( 'tc_404_wrapper_class', array('tc-content', 'span12', 'format-quote' ) );
    $model[ 'inner_class' ]               = array( 'entry-content', apply_filters( 'tc_404_content_icon', 'format-icon') );

    return $model;
  }


  /**
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    foreach ( array('wrapper', 'inner' ) as $property )
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
  }
}
