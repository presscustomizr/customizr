<?php
class TC_footer_model_class extends TC_Model {
  public $element_id = 'footer';

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ] = apply_filters('tc_footer_classes', array(), $model );
    return $model;
  }
}//end of class
