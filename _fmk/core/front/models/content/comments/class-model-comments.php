<?php
class CZR_cl_comments_model_class extends CZR_cl_Model {

  /*
  * @override
  */
  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );

    add_filter ( 'comment_form_defaults'  , array( $this , 'czr_fn_set_comment_title') );
  }

  /**
  * Comment title override (comment_form_defaults filter)
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_comment_title($_defaults) {
    $_defaults['title_reply'] =  __( 'Leave a comment' , 'customizr' );
    return $_defaults;
  }
}
