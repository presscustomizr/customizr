<?php
class TC_comments_model_class extends TC_Model {

  /*
  * @override
  */
  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );

    add_filter ( 'comment_form_defaults'  , array( $this , 'tc_set_comment_title') );
  }
 
  function tc_setup_children() {
    $children = array(
      array(
        'hook'        => '__comments__',
        'template'    => 'content/comment_list',
        'id'          => 'comment'
      ),
    );

    return $children;
  }
  /**
  * Comment title override (comment_form_defaults filter)
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_comment_title($_defaults) {
    $_defaults['title_reply'] =  __( 'Leave a comment' , 'customizr' );
    return $_defaults;
  }
}
