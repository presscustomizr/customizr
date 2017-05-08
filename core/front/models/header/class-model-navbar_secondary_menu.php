<?php
class CZR_navbar_secondary_menu_model_class extends CZR_menu_model_class {

  /*
  * @override
  */
  function czr_fn_get_preset_model() {
    $_preset = parent::czr_fn_get_preset_model();

    $_this_preset = array(
        'element_class'       =>  array( 'primary-nav__menu-wrapper_new', esc_attr( czr_fn_get_opt( 'tc_second_menu_position') ) ),
        'theme_location'      => 'secondary',
        'menu_id'             => 'secondary',
        'menu_class'          => array( 'primary-nav__menu_new', 'regular', 'navbar-nav', 'nav__menu' ),
    );
    error_log( 'sono qui' );
    return array_merge( $_preset, $_this_preset );
  }

}//end class