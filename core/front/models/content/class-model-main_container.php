<?php
class TC_main_container_model_class extends TC_Model {
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
  * @override
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    $model -> column_content_class = $this -> tc_stringify_model_property( 'column_content_class' );
  }
}
