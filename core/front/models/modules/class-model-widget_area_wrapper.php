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
    $model[ 'wrapper_class' ] = array();
    $model[ 'inner_classs'  ] = array();
    $model[ 'position'  ]     = '';

    if ( isset( $model['params']['where'] ) && 'footer' == $model['params']['where'] ) {
       //hack to render white color icons if skin is grey or black
       $skin_class  = ( in_array( TC_utils::$inst->tc_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';
       $footer_widgets_wrapper_classes = array('container' , 'footer-widgets', $skin_class);
    
       $model['wrapper_class'] = $footer_widgets_wrapper_classes; 

       $model['inner_class']   = array('row' ,'widget-area');

       $model['position']      = 'footer';
    }

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
