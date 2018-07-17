<?php
class CZR_comment_list_model_class extends CZR_Model {
  public $comment_args = array();
  public $comment_depth;

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
    // set wp list comments callback.
    $model[ 'comment_args' ]['callback']    = array( $this , 'czr_fn_comments_callback' );

    return $model;
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
  function czr_fn_comments_callback( $comment, $args, $depth ) {
    $this -> czr_fn_update( array(
      'comment_args'  => array_merge( $args, $this -> comment_args ),
      'comment_depth' => $depth
    ) );
    if ( czr_fn_is_registered_or_possible( 'comment' ) && isset( $args['type'] ) && 'comment' == $args['type'] ) {
      czr_fn_render_template( 'content/singular/comments/comment' );
    }
    elseif ( czr_fn_is_registered_or_possible( 'trackpingback' ) && isset( $args['type'] ) && 'pings' == $args['type'] ) {
      czr_fn_render_template( 'content/singular/comments/trackpingback' );
    }
  }
}