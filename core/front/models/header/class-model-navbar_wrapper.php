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
    add_filter( 'body_class', array( $this, 'tc_add_body_classes' ) );

    $model[ 'class' ] = array('navbar-wrapper', 'clearfix', 'span9');
    //$this -> get_navbar_classes();
    
    return $model;
  }

  /**
  * parse this model properties
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', array_unique( $model -> class ) );
  }


   /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_body_classes($_classes) {
    //No navbar box
    if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
      $_classes = array_merge( $_classes , array('no-navbar' ) );
    return $_classes;
  }
}
