<?php
class TC_regular_menu_model_class extends TC_menu_model_class {
  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class($_classes) {
    //menu type class
    if ( ! is_array( $_classes ) )
      $_classes = explode( ' ', $_classes );
    array_push( $_classes, 'tc-regular-menu' );

    return $_classes;
  }

  /**
  * @hook: pre_rendering_view_navbar_wrapper
  */
  function pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model ) {
    //Navbar regular menu position
    if ( ! is_array( $navbar_wrapper_model -> element_class ) )
      $navbar_wrapper_model -> element_class = explode( ' ', $navbar_wrapper_model -> element_class );
    array_push( $navbar_wrapper_model -> element_class, esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
  }

}//end class

