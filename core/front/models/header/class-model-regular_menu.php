<?php
class CZR_cl_regular_menu_model_class extends CZR_cl_menu_model_class {
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
  * @override
  * @hook: pre_rendering_view_navbar_wrapper
  */
  function pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model ) {
    parent::pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model );

    array_push( $navbar_wrapper_model -> element_class, esc_attr( CZR_cl_utils::$inst->czr_opt( 'tc_menu_position') ) );
  }

}//end class

