<?php
class TC_colophon_base_model_class extends TC_Model {
  public $inner_class;
  public $element_class = 'colophon';

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'inner_class' ] = apply_filters('tc_colophon_class', array( 'row-fluid' ) );
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    $model -> inner_class = join( ' ', $model -> inner_class );    
  }
}//end of class
