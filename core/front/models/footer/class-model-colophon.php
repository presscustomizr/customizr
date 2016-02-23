<?php
class TC_colophon_model_class extends TC_Model {
  public $class;


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'class' ] = apply_filters('tc_colophon_class', array( 'row-fluid' ), $model );
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', $model -> class );    
  }
}//end of class
