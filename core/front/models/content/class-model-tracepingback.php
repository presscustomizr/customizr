<?php
class TC_tracepingback_model_class extends TC_Model {
  //bools
  public $has_edit_button;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //parse the comment callback params so to set this propertied
    add_filter( 'tc_comment_callback_params', array( $this, 'tc_set_tracepingback_properties'), 10, 3 );
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
  function tc_set_tracepingback_properties( $comment, $args, $depth ) {
    if ( ! in_array( $comment -> type, array('trackback', 'pingback') ) ) {
      $this -> tc_set_property( 'visibility', false );
      return $comment;
    }

    $this -> tc_set_property( 'visibility', true );
    $this -> tc_set_property( 'has_edit_button', ! TC___::$instance -> tc_is_customizing() );
    return $comment;
  }
}
