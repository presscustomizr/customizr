<?php
class CZR_cl_second_menu_model_class extends CZR_cl_menu_model_class {
  public $theme_location = 'secondary';

  /*
  * @override
  */
  protected function get_element_class() {
    return array_merge( parent::get_element_class(), array( 'secondary-nav__container' ) );
  }

  /*
  * @override
  */
  protected function get_menu_class() {
    return array_merge( parent::get_menu_class(), array( 'secondary-nav__menu', 'list__menu' ) );
  }

}//end class