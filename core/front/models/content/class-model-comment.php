<?php
class TC_comment_model_class extends TC_Model {
    public $args;
    public $comment;
    public $depth;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //parse the comment callback params so to set this propertied
    add_filter( 'tc_comment_callback_params', array( $this, 'tc_set_comment_properties'), 10, 3 );
    //render this?
    add_filter( 'tc_do_render_view_{$this -> id}',  'have_comments' ); 
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
    function tc_set_comment_properties( $comment, $args, $depth ) {
      $this -> comment = $comment;
      $this -> args = $args;
      $this -> depth = $depth;  
  }
}
