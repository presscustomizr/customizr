<?php
class TC_body_model_class extends TC_Model {
  public $classes;
  public $attributes;

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

    //menu type class
    $_menu_type = $this -> tc_is_sidenav_enabled() ? 'tc-side-menu' : 'tc-regular-menu';
    array_push( $_classes, $_menu_type );
    //sidenav where
    $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
    array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );
 
    return $_classes;
  }

  /***************************************
  * HELPERS
  ****************************************/
  /**
  * @return bool
  */
 //used in other places, we need a different way
  function tc_is_sidenav_enabled() {
    return apply_filters( 'tc_is_sidenav_enabled', 'aside' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_style' ) ) );
  }
}
