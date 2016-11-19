<?php
class CZR_comment_list_model_class extends CZR_Model {
  public $czr_args = array();
  public $czr_depth;

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
    //get user defined max comment depth
    $max_comments_depth = get_option('thread_comments_depth');
    $model[ 'czr_args' ]['max_depth']   = isset( $max_comments_depth ) ? $max_comments_depth : 5 ;
    $model[ 'czr_args' ]['callback']    = array( $this , 'czr_fn_comments_callback' );

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
      'czr_args'  => array_merge( $this -> czr_args, $args ),
      'czr_depth' => $depth
    ) );

    if ( czr_fn_has( 'comment' ) && isset( $args['type'] ) && 'comment' == $args['type'] )
      czr_fn_render_template( 'content/comments/comment' );
    elseif ( czr_fn_has( 'trackpingback' ) && isset( $args['type'] ) && 'pings' == $args['type'] )
      czr_fn_render_template( 'content/comments/trackpingback' );
  }
}