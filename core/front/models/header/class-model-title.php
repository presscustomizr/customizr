<?php
class TC_title_model_class extends TC_Model {
  public $content;
  
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'content' ] =  __( esc_attr( get_bloginfo( 'name' ) ) );
    return $model;
  }
}
