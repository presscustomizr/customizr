<?php
class TC_widget_area_wrapper_model_class extends TC_Model {
  public $wrapper_class;
  public $inner_class;
  public $where;
  public $position;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    //hack to render white color icons if skin is grey or black
    $skin_class  = ( in_array( TC_utils::$inst->tc_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';
    $footer_widgets_wrapper_classes = apply_filters( 'tc_footer_widget_wrapper_class' , array('container' , 'footer-widgets', $skin_class) );
    
    $model['wrapper_class'] = 'footer' == $model['params']['where'] ? $footer_widgets_wrapper_classes : array(); 

    $model['inner_class']   = apply_filters( 'tc_footer_widget_area', array('row' ,'widget-area') );
    $model['position']      = 'footer';
    return $model;
  }

  /**
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> wrapper_class = join( ' ', array_unique( $model -> wrapper_class ) );
    $model -> inner_class = join( ' ', array_unique( $model -> inner_class ) );
  }
}
