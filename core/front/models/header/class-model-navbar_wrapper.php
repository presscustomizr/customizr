<?php
class TC_navbar_wrapper_model_class extends TC_Model {

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {

    $model[ 'element_class' ] = apply_filters( 'tc_navbar_wrapper_class', array('span9') );

    return $model;
  }



  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class( $_classes ) {
    //No navbar box
    if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
      $_classes = array_merge( $_classes , array('no-navbar' ) );
    return $_classes;
  }
}
