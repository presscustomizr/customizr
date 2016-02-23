<?php
class TC_colophon_wrapper_model_class extends TC_Model {
  public $tag;
  public $class;
  public $attributes = '';

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $params                         = isset( $model['params'] ) ? $model['params'] : array();  
    $model[ 'tag' ]                 = isset( $params['tag'] )  ? $params['tag']  : 'div';
  
    $model[ 'class' ]               = isset( $params['class'] ) ? $params['class'] : array( 'span3' );

    return $model;
  }


  /**
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', array_unique( $model -> class ) );
  }
}
