<?php
abstract class TC_post_navigation_model_class extends TC_Model {
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]       = $this -> tc_get_pn_element_class( array('navigation') );

    return $model; 
  }

  /* visibility in the customizer */    
  function tc_get_pn_element_class( $_nav_classes ) {
    if ( ! TC___::$instance -> tc_is_customizing() )
      return $_nav_classes;

    $_context                  = $this -> tc_get_context();

    if ( ! $this -> tc_is_post_navigation_enabled() )
      array_push( $_nav_classes, 'hide-all-post-navigation' );
    if ( !  $this -> tc_is_post_navigation_context_enabled( $_context ) )
      array_push( $_nav_classes, 'hide-post-navigation' );

    return $_nav_classes;
  }
  
  abstract function tc_get_context();

 
  /*
  * @param (string or bool) the context
  * @return bool
  */
  function tc_is_post_navigation_context_enabled( $_context ) {
    return $_context && 1 == esc_attr( TC_utils::$inst -> tc_opt( "tc_show_post_navigation_{$_context}" ) );
  }

  /*
  * @return bool
  */
  function tc_is_post_navigation_enabled(){
    return 1 == esc_attr( TC_utils::$inst -> tc_opt( 'tc_show_post_navigation' ) ) ;
  }
}
