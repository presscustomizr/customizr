<?php
class TC_post_list_wrapper_model_class extends TC_Model {
  public $element_class    = array( 'row-fluid' );
  public $place_1 ;
  public $place_2 ;
 
  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    add_action( $model['hook'], array( $this, 'set_layout_hooks' ), 0 );
  } 
 
  function set_layout_hooks() {
    global $wp_query;
    $alternate          = true;
    $thumb_first        = false; 
    $has_post_thumbnail = true;
    $this -> place_1 = 'content';
    $this -> place_2 = 'thumb';
    if ( has_post_thumbnail() ) {
      if (  ( 0 == ( $wp_query -> current_post % 2 ) && $alternate ) || $thumb_first ) {
        $this -> place_1 = 'thumb';
        $this -> place_2 = 'content';
      }
    }else
      $has_post_thumbnail = false;
    set_query_var( 'tc_has_post_thumbnail', $has_post_thumbnail );    
  }
}
