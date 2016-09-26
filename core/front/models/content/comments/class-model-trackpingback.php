<?php
class CZR_cl_trackpingback_model_class extends CZR_cl_Model {

  //bools
  public $is_current_post_author;
  public $has_edit_button;

  public $ping_number;

  function __construct( $model = array() ) {
    parent::__construct( $model );
echo "madonna mia perdonami";
    //parse the comment callback params so to set this propertied
    add_filter( 'czr_comment_callback_params', array( $this, 'czr_fn_set_trackpingback_loop_properties'), 10 );
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'has_edit_button' ]         = ! CZR() -> czr_fn_is_customizing();
    $model[ 'ping_number' ]             = 1;

    return $model;
  }

  /* TODO */
  /* Verify this!!!!*/
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
  function czr_fn_set_trackpingback_loop_properties( $comment ) {

    if ( ! in_array( $comment -> comment_type, array('trackback', 'pingback') ) ) {
      $this -> czr_fn_set_property( 'visibility', false );
      return $comment;
    }

    $_pn = $this->ping_number;
    $_pn++;

    $this -> czr_fn_set_property( 'ping_number', $_pn );

    return $comment;
  }
}