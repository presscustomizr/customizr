<?php
abstract class TC_widget_area_wrapper_model_class extends TC_Model {
  public $wrapper_class      = array();
  public $inner_class        = array();
  public $action_hook_suffix = '';

  /**
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> wrapper_class ) )  
      $model -> wrapper_class = join( ' ', array_unique( $model -> wrapper_class ) );
    if ( is_array( $model -> inner_class ) )  
      $model -> inner_class = join( ' ', array_unique( $model -> inner_class ) );
  }
}
