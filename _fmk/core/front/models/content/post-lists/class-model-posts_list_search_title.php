<?php
class CZR_cl_posts_list_search_title_model_class extends CZR_cl_posts_list_title_model_class {
  public $title_class;
  public $title_wrapper_class;
  public $search_form_wrapper_class;

  /*  @override */
  function czr_fn_extend_params( $model = array() ) {
    //the controlleer will check if we're in (not singular) context
    $model = parent::czr_fn_extend_params( $model );
    $model['title_class']                     = apply_filters( 'czr_archive_icon', $this -> czr_fn_get_archive_title_class() );
    $model['title_wrapper_class']             = apply_filters( 'czr_search_result_header_title_class', array('span8') );
    $model['search_form_wrapper_class']       = apply_filters( 'czr_search_result_header_form_class', array('span4') );

    return $model;
  }
  /* @override */
  function czr_fn_get_the_posts_list_context() {
    return 'search_results';
  }

  /* @override */
  function czr_fn_get_posts_list_pre_title( $context = null ) {
    return parent::czr_fn_get_posts_list_pre_title( 'search' );
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array('title', 'title_wrapper', 'search_form_wrapper') as $prop )
      $model -> {"{$prop}_class"} = $this -> czr_fn_stringify_model_property( "{$prop}_class" );
  }
}
