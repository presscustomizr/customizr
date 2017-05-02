<?php
class CZR_regular_primary_menu_model_class extends CZR_menu_model_class {
  /*
  * @override
  */
  protected function czr_fn__get_element_class() {
    $element_class        =  parent::czr_fn__get_element_class();

    $_menu_position_class =  esc_attr( czr_fn_get_opt( 'tc_menu_position') );

    array_push( $element_class, $_menu_position_class );

    return $element_class;
  }

}//end class