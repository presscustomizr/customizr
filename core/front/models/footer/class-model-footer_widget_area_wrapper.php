<?php
class TC_footer_widget_area_wrapper_model_class extends TC_Model {
  public $key;
  public $class;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model['class'] = array('span4');
    $model['key']   = isset( $model['params']['key'] ) ? $model['params']['key'] : 'footer_one';
    return $model;
  }

  /**
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', array_unique( $model -> class ) );
  }

}
