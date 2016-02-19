<?php
class TC_body_model_class extends TC_Model {
  public $classes;
  public $attributes;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    add_filter( 'body_class', array( $this, 'tc_add_body_classes' ) );

    //set this model's properties
    $model[ 'classes' ]    = implode( ' ', get_body_class() );
    $model[ 'attributes' ] = apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"');
    return $model;
  }

  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_body_classes($_classes) {
    //STICKY HEADER
    if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) ) {
      $_classes = array_merge( $_classes, array('tc-sticky-header', 'sticky-disabled') );
      
      //STICKY TRANSPARENT ON SCROLL
      if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_transparent_on_scroll' ) ) )
        $_classes = array_merge( $_classes, array('tc-transparent-on-scroll') );
      else
        $_classes = array_merge( $_classes, array('tc-solid-color-on-scroll') );
    }
    else {
      $_classes = array_merge( $_classes, array('tc-no-sticky-header') );
    }
    //No navbar box
    if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
      $_classes = array_merge( $_classes , array('no-navbar' ) );
    //SKIN CLASS
    $_skin = sprintf( 'skin-%s' , basename( TC_init::$instance -> tc_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }
}
