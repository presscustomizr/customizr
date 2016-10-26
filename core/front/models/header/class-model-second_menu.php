<?php
class CZR_second_menu_model_class extends CZR_menu_model_class {
  public $theme_location = 'secondary';
  public $menu_id = 'secondary-menu';
  /*
  * @override
  */
  protected function get_element_class() {
    $_menu_position_class =  esc_attr( czr_fn_get_opt( 'tc_second_menu_position') );
    return array_merge( parent::get_element_class(), array( 'secondary-nav__menu-wrapper', $_menu_position_class ) );
  }


  /*
  * @override
  */
  protected function get_menu_class() {
    return array_merge( parent::get_menu_class(), array( 'secondary-nav__menu', 'list__menu' ) );
  }

}//end class