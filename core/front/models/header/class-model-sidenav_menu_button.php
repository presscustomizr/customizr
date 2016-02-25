<?php
class TC_sidenav_menu_button_model_class extends TC_menu_button_model_class {
  public $wrapper_class;
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
    $model = parent::tc_extend_params( $model );  
    
    array_push( $model[ 'wrapper_class' ], 'sn-toggle' );
    if ( '__sidenav__' == $model['hook'] ) {
      $_close_message             = __('Close', 'customizr' );  
      //button label
      $model[ 'button_label']     = $model[ 'button_label' ] ? sprintf( '<span class="menu-label">%s</span>', $_close_message );
      //button title
      $model[ 'button_title' ]    =  $_close_message;
    }

    $model['button_attr']        = '';
    return $model;
  }
}
