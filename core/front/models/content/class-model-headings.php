<?php
abstract class TC_headings_model_class extends TC_Model {
  public $type;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]          = apply_filters( "tc_{$this -> type}_header_class", $this -> tc_get_class( $model ), $model );
    return $model;
  }

  abstract function tc_get_class( $model = array() );
}
