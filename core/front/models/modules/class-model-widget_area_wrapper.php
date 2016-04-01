<?php
abstract class TC_widget_area_wrapper_model_class extends TC_Model {
  public $inner_class        = array();
  public $action_hook_suffix = '';
  public $inner_id           = '';

  /**
  * @override
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  }
}
