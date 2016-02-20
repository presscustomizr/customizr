<?php
class TC_reset_margin_top_model_class extends TC_Model {
  public $margin_top;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $this -> margin_top = apply_filters( 'tc_default_sticky_header_height', 103 );
    
    return $model;
  }
}
