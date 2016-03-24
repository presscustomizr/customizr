<?php
class TC_body_model_class extends TC_Model {
  public $element_attributes;

  /* TODO: SHOULD FIND A BETTER WAY TO EXTEND THE MODEL PARAMS/PROPERTIES
   *  for example, the body_class filter should be accessible to all models instances
   *  so that they can actually filter them.
   *  We might do something like:
   *  1) tc_extend_params to extend "early" params
   *  2) another method to extend the model fired just before the view is instanciated/rendered
  */

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    //set this model's properties
    $model[ 'element_attributes' ] = apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"');
    return $model;
  }

  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class($_classes) {
    //SKIN CLASS
    $_skin = sprintf( 'skin-%s' , basename( TC_init::$instance -> tc_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }
}
