<?php
class TC_footer_widgets_area_wrapper_model_class extends TC_widget_area_wrapper_model_class {
  
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model       = parent::tc_extend_params( $model );   
    //hack to render white color icons if skin is grey or black
    $skin_class  = ( in_array( TC_utils::$inst->tc_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';

    $model['element_class'] = apply_filters( 'tc_footer_widget_element_class', array('container' , 'footer-widgets', $skin_class) );

    $model['inner_class']             = array('row' ,'widget-area');
    $model['action_hook_suffix']      = '_footer';
    
    return $model;
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  } 
}
