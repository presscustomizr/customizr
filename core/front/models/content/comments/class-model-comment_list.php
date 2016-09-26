<?php
class CZR_cl_comment_list_model_class extends CZR_cl_Model {
  public $args;

  /*
  * @override
  */
  function czr_fn_maybe_render_this_model_view() {
    return $this -> visibility && have_comments();
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'args' ]          = apply_filters( 'czr_list_comments_args' , array( 'callback' => array ( $this , 'czr_fn_comment_callback' ) ) );
    return $model;
  }


  function czr_fn_setup_children() {
    //registered here as they act on czr_comment_callback_params
    $children = array(
      array(
        'model_class' => 'content/comments/comment',
        'id'          => 'comment'
      ),
      array(
        'id'          => 'trackback',
        'model_class' => 'content/comments/trackpingback'
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
  function czr_fn_comment_callback( $comment, $args, $depth ) {
    //get user defined max comment depth
    $max_comments_depth = get_option('thread_comments_depth');
    $args['max_depth']  = isset( $max_comments_depth ) ? $max_comments_depth : 5;

    apply_filters_ref_array( 'czr_comment_callback_params', array( $comment, $args, $depth ) );

    if ( czr_fn_has( 'comment' ) && isset( $args['type'] ) && 'comment' == $args['type'] )
      czr_fn_render_template( 'content/comments/comment', 'comment' );
    elseif ( czr_fn_has( 'trackback' ) && isset( $args['type'] ) && 'pings' == $args['type'] )
      czr_fn_render_template( 'content/comments/trackpingback', 'trackpingback' );
  }
}