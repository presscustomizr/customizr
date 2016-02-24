<?php
class TC_widget_area_model_class extends TC_Model {
  public $key;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'key' ] = $model['params'][ 'key' ];
    return $model;
  }
}
