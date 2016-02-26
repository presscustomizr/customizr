<?php
class TC_main_container_model_class extends TC_Model {
  public $class = 'container';
  public $column_content_class;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'column_content_class' ]      = apply_filters( 'tc_column_content_wrapper_classes' , array('row', 'column-content-wrapper') );
    return $model;
  }


  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class = join( ' ', array_unique( $model -> class ) );
    if ( is_array( $model -> column_content_class ) )
      $model -> column_content_class = join( ' ', array_unique( $model -> column_content_class ) );
  }
}
