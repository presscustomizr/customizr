<?php
class CZR_topnav_model_class extends CZR_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $mobile_topnav           = esc_attr( czr_fn_get_opt( 'tc_header_topnav_mobile' ) );
    $secondary_menu_position = esc_attr( czr_fn_get_opt( 'tc_second_menu_position' ) );

    switch ( $mobile_topnav ) {
      case 'collapse' : $_preset = array(
                          'element_class'       => array('menu-toggleable-md', $secondary_menu_position ),
                          'nav_class'           => array('collapse', 'nav-collapse', 'navbar-toggleable-md' ),
                          'has_mobile_button'   => true,
                        );
                        break;

      case 'show'     : $_preset = array(
                          'element_class'       => array('not-hidden-md-down', $secondary_menu_position ),
                          'nav_class'           => '',
                          'has_mobile_button'   => false,
                        );
                        break;

      default         : $_preset = array(
                          'element_class'       => array('hidden-md-down', $secondary_menu_position ),
                          'nav_class'           => '',
                          'has_mobile_button'   => false,
                        );
                        break;

    }
    return $_preset;
  }

}