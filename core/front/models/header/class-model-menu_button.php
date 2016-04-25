<?php
class CZR_cl_menu_button_model_class extends CZR_cl_Model {
  public $button_label;
  public $button_title;
  public $button_attr;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class' ] = array( 'right' != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left' );

    $model[ 'button_label'  ] = (bool)esc_attr( CZR_cl_utils::$inst->czr_fn_opt('tc_display_menu_label') ) ? sprintf( '<span class="menu-label">%s</span>', __('Menu' , 'customizr') ) : '';
    $model[ 'button_title'  ] = __( 'Open the menu', 'customizr' );
    $model[ 'button_attr'   ] = 'data-toggle="collapse" data-target=".nav-collapse"';

    return $model;
  }
}
