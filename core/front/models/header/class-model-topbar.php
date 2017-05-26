<?php
class CZR_topbar_model_class extends CZR_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {

    //$mobile_topnav           = esc_attr( czr_fn_opt( 'tc_header_topnav_mobile' ) );
    //By default we decided is hidden;
    $mobile_topnav           = 'hidden';


    switch ( $mobile_topnav ) {
      case 'collapse' : $_preset = array(
                          'element_class'       => array( 'navbar-toggleable-md' ),
                          'nav_class'           => array( 'collapse', 'navbar-collapse'),
                          'menu_class'          => array( 'topbar-nav__menu', 'regular', 'navbar-nav', 'nav__menu' ),
                          'has_mobile_button'   => true,
                        );
                        break;

      case 'show'     : $_preset = array(
                          'element_class'       => array('not-hidden-md-down' ),
                          'nav_class'           => '',
                          'menu_class'          => array( 'topbar-nav__menu', 'regular', 'nav__menu' ),
                          'has_mobile_button'   => false,
                        );
                        break;

      default         : $_preset = array(
                          'element_class'       => array('hidden-md-down' ),
                          'nav_class'           => '',
                          'menu_class'          => array( 'topbar-nav__menu', 'regular', 'nav__menu' ),
                          'has_mobile_button'   => false,
                        );
                        break;

    }
    return $_preset;
  }

}