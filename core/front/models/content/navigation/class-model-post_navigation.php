<?php
abstract class CZR_cl_post_navigation_model_class extends CZR_cl_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class' ]       = $this -> czr_fn_get_pn_element_class( array('navigation') );

    return $model;
  }

  /* visibility in the customizer */
  function czr_fn_get_pn_element_class( $_nav_classes ) {
    if ( ! CZR() -> czr_fn_is_customizing() )
      return $_nav_classes;

    $_context                  = $this -> czr_fn_get_context();

    if ( ! $this -> czr_fn_is_post_navigation_enabled() )
      array_push( $_nav_classes, 'hide-all-post-navigation' );
    if ( !  $this -> czr_fn_is_post_navigation_context_enabled( $_context ) )
      array_push( $_nav_classes, 'hide-post-navigation' );

    return $_nav_classes;
  }

  abstract function czr_fn_get_context();


  /*
  * @param (string or bool) the context
  * @return bool
  */
  function czr_fn_is_post_navigation_context_enabled( $_context ) {
    return $_context && 1 == esc_attr( CZR_cl_utils::$inst -> czr_fn_opt( "tc_show_post_navigation_{$_context}" ) );
  }

  /*
  * @return bool
  */
  function czr_fn_is_post_navigation_enabled(){
    return 1 == esc_attr( CZR_cl_utils::$inst -> czr_fn_opt( 'tc_show_post_navigation' ) ) ;
  }
}
