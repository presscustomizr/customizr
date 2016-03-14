<?php
class TC_comment_model_class extends TC_Model {
  public $comment;
  public $comment_text;
  public $comment_wrapper_class;
  public $comment_avatar_class;
  public $comment_content_class;
  public $comment_reply_btn_class;
  public $comment_avatar_size;

  public $comment_reply_link_args;

  //bools
  public $is_current_post_author;
  public $has_edit_button;
  public $is_awaiting_moderation;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //parse the comment callback params so to set this propertied
    add_filter( 'tc_comment_callback_params', array( $this, 'tc_set_comment_properties'), 10, 3 );
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}", array( $this, 'tc_maybe_render_comment' ) ); 
  }
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'comment_wrapper_class' ]   = apply_filters( 'tc_comment_wrapper_class', array('row-fluid') );
    $model[ 'comment_avatar_class'  ]   = apply_filters( 'tc_comment_avatar_class', array('comment-avatar', 'span2') );
    $model[ 'comment_content_class' ]   = apply_filters( 'tc_comment_content_class', array('span10') );
    $model[ 'comment_reply_btn_class' ] = apply_filters( 'tc_comment_reply_btn_class', array('reply btn btn-small' ) );
    $model[ 'comment_avatar_size' ]     = apply_filters( 'tc_comment_avatar_size', 80 );

    return $model;
  }

  function tc_maybe_render_comment() {
    return $this -> visibility;     
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array('wrapper', 'content', 'avatar', 'reply_btn' ) as $property )
      $model -> {"comment_{$property}_class"} = $this -> tc_stringify_model_property( "comment_{$property}_class" );
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
  function tc_set_comment_properties( $comment, $args, $depth ) {
    global $post;
    
    if ( in_array( $comment -> type, array('trackback', 'pingback') ) ) {
      $this -> tc_set_property( 'visibility', false );
      return $comment;
    }

    $props = array(
     'comment'                 => $comment,
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
     'has_edit_button'          => ! TC___::$instance -> tc_is_customizing(),
     'is_awaiting_moderation'   => '0' == $comment->comment_approved,
    );

    foreach ( $props as $property => $value ) 
      $this -> tc_set_property( $property, $value );
 
    return $comment;
  }
}
