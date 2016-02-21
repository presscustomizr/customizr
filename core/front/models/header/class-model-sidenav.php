<?php
class TC_sidenav_model_class extends TC_Model {
  public $class;
  public $inner_class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    add_filter( 'body_class', array( $this, 'tc_add_body_classes' ) );

    $model[ 'class' ]         = implode(' ', apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) );
    $model[ 'inner_class' ]   = implode(' ', apply_filters('tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) );  
    
    return $model;
  }

    /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_body_classes($_classes) {
    array_push( $_classes, 'tc-side-menu' );

    //sidenav where
    $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
    array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );
    
    return $_classes;
  }
}
