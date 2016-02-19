<?php
class TC_mobile_tagline_wrapper_model_class extends TC_Model {
  public $class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'class' ] = implode( ' ', array( 'container', 'outside' ) );
    
    return $model;
  }
}
