<?php
abstract class TC_widget_area_wrapper_model_class extends TC_Model {
  public $inner_class        = array();
  public $action_hook_suffix = '';
  public $inner_id           = '';

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  }
}
