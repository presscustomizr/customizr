<?php
class TC_featured_page_model_class extends TC_Model {
  public $thumb_wrapper_class;

  public $fp_img;

  public $fp_button_text;
  public $fp_button_class;

  public $fp_id;

  public $featured_page_title;
  public $featured_page_id;

  public $text;

  public $featured_page_link;

  public $edit_enabled;

  public $span_value;

  public $is_first_of_row;
  public $is_last_of_row;

  function __construct( $model ) {
    parent::__construct( $model );
    //WE DON'T REALLY NEED TO SET A QUERY VAR AS WE CAN PASS THE VALUES ASS PARAMS TO THE HOOK CALLBACK
    //THEN IT BECOMES JUST A MATTER OF WHAT TO PASS..
    //inside the slider loop but before rendering set some properties    
    add_action( $this -> hook          , array( $this, 'tc_set_this_properties' ), -1 );    
  }
  
  function tc_set_this_properties() {
    //get the current slide;
    $fp   = get_query_var( 'tc_fp', null );
    if ( empty( $fp ) ) {
      $this -> tc_set_property( 'visibility', false );
      return;      
    }
    
    // array( 'is_first_of_row', 'is_last_of_row', 'data', 'fp_id', 'span_value' )
    extract( $fp );

    //img block elements
    if ( isset( $data['fp_img'] ) )
      $thumb_wrapper_class = isset( $data['has_holder'] ) && $data['has_holder'] ? 'tc-holder' : '';   

    //button block
    $button_block = $this -> tc_setup_button_block( $data, $fp_id ); 

    $this -> tc_update( array_merge( $data, $button_block, compact( 'thumb_wrapper_class', 'span_value', 'fp_id', 'is_first_of_row', 'is_last_of_row' ) ) );
  }

  function tc_setup_button_block( $fp_data, $fp_single_id ) {
    //button block
    $tc_fp_button_text = apply_filters( 'tc_fp_button_text' , esc_attr( TC_utils::$inst->tc_opt( 'tc_featured_page_button_text') ) , $fp_single_id );
    if ( $tc_fp_button_text || TC___::$instance -> tc_is_customizing() ){
      $fp_button_text  = $tc_fp_button_text;  
      $fp_button_class = apply_filters( 'tc_fp_button_class' , 'btn btn-primary fp-button', $fp_single_id );
      $fp_button_class = $tc_fp_button_text ? $fp_button_class : $fp_button_class . ' hidden';
    }
    return compact( 'fp_button_class', 'fp_button_text' );
  }
}
