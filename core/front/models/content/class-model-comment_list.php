<?php
class TC_comment_list_model_class extends TC_Model {
  public $args;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  'have_comments' ); 
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
    $comment_list_instance = CZR() -> collection -> tc_get_model_instance( 'comment' );
    if ( $comment_list_instance ) {
      $comment_list_instance -> tc_comment_callback_( $comment, $args, $depth );
      do_action( '__comment_loop__' );
    }
    return false;
  }
}
