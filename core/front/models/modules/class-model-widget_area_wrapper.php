<?php
abstract class TC_widget_area_wrapper_model_class extends TC_Model {
  public $element_class      = array();
  public $inner_class        = array();
  public $action_hook_suffix = '';
  public $inner_id           = '';

  /**
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> element_class ) )  
      $model -> element_class = join( ' ', array_unique( $model -> element_class ) );
    if ( is_array( $model -> inner_class ) )  
      $model -> inner_class = join( ' ', array_unique( $model -> inner_class ) );
  }
}
