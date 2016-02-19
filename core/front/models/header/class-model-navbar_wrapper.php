<?php
class TC_navbar_wrapper_model_class extends TC_Model {
  public $class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'class' ] = implode( " ", apply_filters( 'tc_navbar_wrapper_class', array('navbar-wrapper', 'clearfix', 'span9') ) );
    
    return $model;
  }
}
