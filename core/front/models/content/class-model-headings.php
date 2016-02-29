<?php
abstract class TC_headings_model_class extends TC_Model {
  public $class;
  public $type;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'class' ]          = apply_filters( "tc_{$this -> type}_header_class", $this -> tc_get_class( $model ), $model );
    return $model;
  }

  abstract function tc_get_class( $model = array() );

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class = join( ' ', array_unique( $model -> class ) );
  }
}
