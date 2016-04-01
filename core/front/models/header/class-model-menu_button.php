<?php
class TC_menu_button_model_class extends TC_Model {
  public $button_label;
  public $button_title;
  public $button_attr;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ] = array( 'right' != esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left' );

    $model[ 'button_label'  ] = (bool)esc_attr( TC_utils::$inst->tc_opt('tc_display_menu_label') ) ? sprintf( '<span class="menu-label">%s</span>', __('Menu' , 'customizr') ) : '';
    $model[ 'button_title'  ] = __( 'Open the menu', 'customizr' );
    $model[ 'button_attr'   ] = 'data-toggle="collapse" data-target=".nav-collapse"';

    return $model;
  }
}
