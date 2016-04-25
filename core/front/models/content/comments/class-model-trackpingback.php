<?php
class CZR_cl_trackpingback_model_class extends CZR_cl_comment_model_class {
  public $type = 'trackpingback';

  /*
  * @override
  */
  function tc_set_early_properties( $model ) {
    return $model;
  }


  /*
  * @override
  */
  function pre_rendering_my_view_cb( $model ) {
    return;
  }


  /**
   * @override
   * Prepare template for comments
   *
   * dynamic, props change at each loop cycle
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   *  Inspired from Twenty Twelve 1.0
   * @package Customizr
   * @since Customizr 1.0
  */
  function tc_set_comment_loop_properties( $comment, $args, $depth ) {
    if ( ! in_array( $comment -> comment_type, array('trackback', 'pingback') ) ) {
      $this -> tc_set_property( 'visibility', false );
      return $comment;
    }

    $this -> tc_set_property( 'visibility', true );
    return $comment;
  }
}
