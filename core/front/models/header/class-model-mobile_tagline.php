<?php
class TC_mobile_tagline_model_class extends TC_tagline_model_class {
  public $wrapper_class = array('container', 'outside');
  public $class         = array('site-description');

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    if ( is_array( $model -> wrapper_class ) )
      $model -> wrapper_class = join( ' ', array_unique( $model -> wrapper_class ) );
  }
}
