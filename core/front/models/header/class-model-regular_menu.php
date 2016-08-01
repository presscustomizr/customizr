<?php
class CZR_cl_regular_menu_model_class extends CZR_cl_menu_model_class {
  public $menu_id = 'main-menu_tp';

  /*
  * @override
  */
  protected function get_menu_class() {
    return array_merge( parent::get_menu_class(), array( 'primary-nav__menu', 'regular' ) );
  }


  /*
  * @override
  */
  protected function get_element_class() {
    $_no_topnav_class =  czr_fn_has('navbar_secondary_menu') ? array() : array( 'left' );
    return array_merge( parent::get_element_class(), array( 'primary-nav__menu-wrapper' ), $_no_topnav_class );
  }

  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_body_class($_classes) {
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
    array_push( $navbar_wrapper_model -> element_class, esc_attr( czr_fn_get_opt( 'tc_menu_position') ) );
  }

}//end class