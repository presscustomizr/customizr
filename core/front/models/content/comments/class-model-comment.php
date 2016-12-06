<?php
class CZR_comment_model_class extends CZR_Model {
  public $comment_reply_link_args;

  //bools
  public $is_current_post_author;
  public $is_awaiting_moderation;


  /**
   * Prepare template for comments
   *
   * dynamic, props change at each loop cycle
   *
  */
  function czr_fn_setup_late_properties() {
    global $post;
    global $comment;

    $args  = czr_fn_get( 'czr_args' );
    $depth = czr_fn_get( 'czr_depth' );

    $props = array(
     'comment_text'            => apply_filters( 'comment_text', get_comment_text( $comment->comment_ID , $args ), $comment, $args ),
     'comment_reply_link_args' => array_merge( $args,
        array(
          'depth'      => $depth,
          'max_depth'  => isset($args['max_depth'] ) ? $args['max_depth'] : '',
          'add_below'  => apply_filters( 'czr_comment_reply_below' , 'div-comment' )
        )
      ),
     'is_current_post_author'   => ( $comment->user_id === $post->post_author ),
     'is_awaiting_moderation'   => '0' == $comment->comment_approved,
    );

    $this -> czr_fn_update( $props );
  }
}