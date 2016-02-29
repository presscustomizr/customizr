<?php
class TC_archive_title_model_class extends TC_Model {
  public $class;  
  public $pre_title;
  public $title;

  function tc_extend_params( $model = array() ) {
    $model['class']     = apply_filters( 'tc_archive_icon', $this -> tc_get_class() );
    $model['pre_title'] = apply_filters( 'tc_category_archive_title' , '' );
    $model['title']     = single_cat_title( '', false );
    return $model;
  }

  function tc_get_class() {
    return ( esc_attr( TC_utils::$inst->tc_opt( 'tc_show_archive_title_icon' ) ) && esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ) ? array( 'format-icon' ) : array();
  }
  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class = join( ' ', array_unique( $model -> class ) );
  }
}
