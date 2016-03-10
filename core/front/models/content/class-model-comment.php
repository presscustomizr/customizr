<?php
class TC_comment_model_class extends TC_Model {
    public $args;
    public $comment;
    public $depth;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  'have_comments' ); 
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
    function tc_comment_callback_( $comment, $args, $depth ) {
      $this -> comment = $comment;
      $this -> args = $args;
      $this -> depth = $depth;  
  }
}
