<?php
class TC_main_container_model_class extends TC_Model {
  public $element_class         = 'container';
  public $element_attributes    = 'role="main"';
  public $column_content_class  = array('row', 'column-content-wrapper');

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'column_content_class' ]      = apply_filters( 'tc_column_content_wrapper_classes' , $this -> column_content_class );
    return $model;
  }


  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> element_class ) )
      $model -> element_class = join( ' ', array_unique( $model -> class ) );
    if ( is_array( $model -> column_content_class ) )
      $model -> column_content_class = join( ' ', array_unique( $model -> column_content_class ) );
  }
}
