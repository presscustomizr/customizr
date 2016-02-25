<?php
class TC_mobile_tagline_model_class extends TC_tagline_model_class {
  public $type = 'mobile';

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model                = parent::tc_extend_params( $model ); 
    $model[ 'class' ]     = apply_filters( 'tc_tagline_class', 'site-description', $model );
    return $model;
  }
}
