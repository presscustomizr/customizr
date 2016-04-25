<?php
class TC_comment_model_class extends TC_Model {
  public $comment_text;

  public $comment_reply_link_args;

  public $type = 'comment';

  //bools
  public $is_current_post_author;
  public $has_edit_button;
  public $is_awaiting_moderation;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //parse the comment callback params so to set this propertied
    add_filter( 'tc_comment_callback_params', array( $this, 'tc_set_comment_loop_properties'), 10, 3 );
  }
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'has_edit_button' ]         = ! CZR___::$instance -> tc_is_customizing();
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
  function tc_set_comment_loop_properties( $comment, $args, $depth ) {
    global $post;

    if ( in_array( $comment -> comment_type, array('trackback', 'pingback') ) ) {
      $this -> tc_set_property( 'visibility', false );
      return $comment;
    }
    $this -> tc_set_property( 'visibility', true );

    $props = array(
     'comment_text'            => apply_filters( 'comment_text', get_comment_text( $comment->comment_ID , $args ), $comment, $args ),
     'comment_reply_link_args' => array_merge( $args,
        array(
          'reply_text' => __( 'Reply' , 'customizr' ).' <span>&darr;</span>',
          'depth'      => $depth,
          'max_depth'  => $args['max_depth'] ,
          'add_below'  => apply_filters( 'tc_comment_reply_below' , 'comment' )
        )
      ),
     'is_current_post_author'   => ( $comment->user_id === $post->post_author ),
     'is_awaiting_moderation'   => '0' == $comment->comment_approved,
    );

    $this -> tc_update( $props );
    return $comment;
  }
}
