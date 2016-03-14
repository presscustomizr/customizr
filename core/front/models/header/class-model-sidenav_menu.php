<?php
class TC_sidenav_menu_model_class extends TC_menu_model_class {
  /**
  * @override
  */    
  protected function get_menu_class() {
    return array( 'nav', 'sn-nav');
  }

  protected function get_element_class() {
    return array( 'sn-nav-wrapper' );
  }
}//end class

