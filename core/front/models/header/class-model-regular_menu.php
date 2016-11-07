<?php
class CZR_regular_menu_model_class extends CZR_menu_model_class {
  public $menu_id = 'main-menu';

  /*
  * @override
  */
  protected function get_element_class() {
    $_menu_position_class =  esc_attr( czr_fn_get_opt( 'tc_menu_position') );
    return array_merge( parent::get_element_class(), array( 'primary-nav__menu-wrapper', $_menu_position_class ) );
  }

  /*
  * @override
  */
  protected function get_menu_class() {
    return array_merge( parent::get_menu_class(), array( 'primary-nav__menu', 'regular' ) );
  }


}//end class