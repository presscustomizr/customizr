<?php
class CZR_cl_comment_model_class extends CZR_cl_Model {
  public $comment_reply_link_args;

  //bools
  public $is_current_post_author;
  public $has_edit_button;
  public $is_awaiting_moderation;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //parse the comment callback params so to set this propertied
    add_filter( 'czr_comment_callback_params', array( $this, 'czr_fn_set_comment_loop_properties'), 10, 3 );
  }
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'has_edit_button' ]         = ! CZR() -> czr_fn_is_customizing();
    return $model;
  }


  /**
   * Prepare template for comments
   *
   * dynamic, props change at each loop cycle
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   *  Inspired from Twenty Twelve 1.0
   * @package Customizr
   * @since Customizr 1.0
  */
  function czr_fn_set_comment_loop_properties( $comment, $args, $depth ) {
    global $post;

    if ( in_array( $comment -> comment_type, array('trackback', 'pingback') ) ) {
      $this -> czr_fn_set_property( 'visibility', false );
      return $comment;
    }
    $this -> czr_fn_set_property( 'visibility', true );

    $props = array(
     'comment_text'            => apply_filters( 'comment_text', get_comment_text( $comment->comment_ID , $args ), $comment, $args ),
     'comment_reply_link_args' => array_merge( $args,
        array(
          'reply_text' => __( 'Reply' , 'customizr' ).' <span>&darr;</span>',
          'depth'      => $depth,
          'max_depth'  => $args['max_depth'] ,
          'add_below'  => apply_filters( 'czr_comment_reply_below' , 'div-comment' )
        )
      ),
     'is_current_post_author'   => ( $comment->user_id === $post->post_author ),
     'is_awaiting_moderation'   => '0' == $comment->comment_approved,
    );

    $this -> czr_fn_update( $props );
    return $comment;
  }
}