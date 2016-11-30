<?php
/*
* This class only handles the visibility in the Customizer preview
*/
class CZR_posts_navigation_model_class extends CZR_Model {

  /* visibility in the customizer */
  function czr_fn_get_element_class( $_nav_classes = array() ) {

    if ( ! czr_fn_is_customizing() )
      return $_nav_classes;

    $_context                  = czr_fn_get_query_context();

    if ( ! $this -> czr_fn_is_posts_navigation_enabled() )
      array_push( $_nav_classes, 'hide-all-post-navigation' );
    if ( !  $this -> czr_fn_is_posts_navigation_context_enabled( $_context ) )
      array_push( $_nav_classes, 'hide-post-navigation' );

    return $_nav_classes;
  }


  /*
  * @param (string or bool) the context
  * @return bool
  */
  function czr_fn_is_posts_navigation_context_enabled( $_context ) {
    return $_context && 1 == esc_attr( czr_fn_get_opt( "tc_show_post_navigation_{$_context}" ) );
  }

  /*
  * @return bool
  */
  function czr_fn_is_posts_navigation_enabled(){
    return 1 == esc_attr( czr_fn_get_opt( 'tc_show_post_navigation' ) ) ;
  }
}