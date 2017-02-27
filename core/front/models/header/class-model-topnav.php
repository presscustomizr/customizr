<?php
class CZR_topnav_model_class extends CZR_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    if ( esc_attr( czr_fn_get_opt( 'tc_header_topnav_collapse' ) ) ){
      $_preset = array(
        'element_class'       => 'menu-toggleable-md',
        'nav_class'           => array('collapse', 'nav-collapse', 'navbar-toggleable-md' ),
        'has_mobile_button'   => true,
      );
    }
    else {
      $_preset = array(
        'element_class'       => 'hidden-md-down',
        'menu_class'          => '',
        'has_mobile_button'   => false,
      );
    }

    return $_preset;
  }

}