<?php
class CZR_cl_sidenav_menu_model_class extends CZR_cl_menu_model_class {
  /**
  * @override
  */
  protected function get_menu_class() {
    return array( 'nav', 'sn-nav');
  }

  protected function get_element_class() {
    return array( 'sn-nav-wrapper' );
  }

  /**
  * @override
  * @hook: pre_rendering_view_navbar_wrapper
  */
  function pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model ) {
    return;
  }
}//end class

