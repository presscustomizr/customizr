<?php
class CZR_cl_navbar_wrapper_model_class extends CZR_cl_Model {

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {

    $model[ 'element_class' ] = apply_filters( 'czr_navbar_wrapper_class', array('span9') );

    return $model;
  }



  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_body_class( $_classes ) {
    //No navbar box
    if ( 1 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_display_boxed_navbar') ) )
      $_classes = array_merge( $_classes , array('no-navbar' ) );
    return $_classes;
  }
}
