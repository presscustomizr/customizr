<?php
class CZR_cl_comment_list_model_class extends CZR_cl_Model {
  public $args;

  /*
  * @override
  */
  function tc_maybe_render_this_model_view() {
    return $this -> visibility && have_comments();
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'args' ]          = apply_filters( 'tc_list_comments_args' , array( 'callback' => array ( $this , 'tc_comment_callback' ) , 'style' => 'ul'  ) );
    return $model;
  }


  function tc_setup_children() {
    $children = array(
      array(
        'model_class' => 'content/comments/comment',
        'id'          => 'comment'
      ),
      array(
        'id'          => 'trackback',
        'model_class' => array( 'parent' => 'content/comments/comment', 'name' => 'content/comments/trackpingback' )
      ),
    );

    return $children;
  }

  /**
   * Template for comments and pingbacks.
   *
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   *  Inspired from Twenty Twelve 1.0
   * @package Customizr
   * @since Customizr 1.0
  */
  function tc_comment_callback( $comment, $args, $depth ) {
    //get user defined max comment depth
    $max_comments_depth = get_option('thread_comments_depth');
    $args['max_depth']  = isset( $max_comments_depth ) ? $max_comments_depth : 5;

    apply_filters_ref_array( 'tc_comment_callback_params', array( $comment, $args, $depth ) );
    if ( tc_has( 'comment' ) )
      tc_render_template( 'content/comments/comment', 'comment' );
    if ( tc_has( 'trackback' ) )
      tc_render_template( 'content/comments/comment', 'trackback' );
  }
}
