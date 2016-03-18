<?php
class TC_posts_list_search_title_model_class extends TC_posts_list_title_model_class {
  public $element_tag = 'div';
  public $element_class;
  public $title_class;
  public $title_wrapper_class;
  public $search_form_wrapper_class;
  
  /*  @override */
  function tc_extend_params( $model = array() ) {
    //the controlleer will check if we're in (not singular) context
    $model = parent::tc_extend_params( $model );  
    $model['element_class']                   = array( 'row-fluid' );
    $model['title_class']                     = apply_filters( 'tc_archive_icon', $this -> tc_get_archive_title_class() );
    $model['title_wrapper_class']             = apply_filters( 'tc_search_result_header_title_class', array('span8') );
    $model['search_form_wrapper_class']       = apply_filters( 'tc_search_result_header_form_class', array('span4') );
    
    return $model;
  }
  /* @override */
  function tc_get_the_posts_list_context() {
    return 'search_results';
  }
 
  /* @override */
  function tc_get_posts_list_pre_title( $context = null ) {
    return parent::tc_get_posts_list_pre_title( 'search' );  
  }

  /**
  * @override
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );  
    foreach ( array('title', 'title_wrapper', 'search_form_wrapper') as $prop )
      $model -> {"{$prop}_class"} = $this -> tc_stringify_model_property( "{$prop}_class" );
  }
}
