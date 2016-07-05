<?php
class CZR_cl_featured_page_model_class extends CZR_cl_Model {
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


  function czr_fn_setup_late_properties() {
    //get the current fp
    $current_fp        = czr_fn_get( 'current_fp' );

    if ( empty ( $current_fp ) )
      return;

    //array( $fp', $fp_index );
    extract( $current_fp );

    /* first and last of row */
    $j = ( czr_fn_get( 'fp_per_row' ) > 1 ) ? $fp_index % czr_fn_get( 'fp_per_row' ) : $fp_index;

    $is_first_of_row = $j == 1;
    $is_last_of_row  = ( $j == 0 || $fp_index == czr_fn_get( 'fp_nb' ) );

    $fp_ids      = czr_fn_get( 'fp_ids' );
    $fp_id       = $fp_ids[ $fp_index - 1 ];

    $span_value  = czr_fn_get( 'span_value' );

    //array( $fp_img', $has_holder, $featured_page_id, $featured_page_title', $featured_page_link', $edit_enabled, $text )
    extract( $fp );

    //img block elements
    if ( isset( $fp_img ) && $fp_img )
      $thumb_wrapper_class = isset( $has_holder ) && $has_holder ? 'tc-holder' : '';

    //button block
    $button_block = $this -> czr_fn_setup_button_block( $fp, $fp_id );
    $this -> czr_fn_update( array_merge( $fp, $button_block, compact( 'thumb_wrapper_class', 'span_value', 'fp_id', 'is_first_of_row', 'is_last_of_row' ) ) );
  }


  function czr_fn_setup_button_block( $fp_data, $fp_single_id ) {
    //button block
    $fp_button_text = apply_filters( 'czr_fp_button_text' , esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_featured_page_button_text') ) , $fp_single_id );
    if ( $fp_button_text || CZR() -> czr_fn_is_customizing() ){
      $fp_button_class = apply_filters( 'czr_fp_button_class' , 'btn btn-primary fp-button', $fp_single_id );
      $fp_button_class = $fp_button_text ? $fp_button_class : $fp_button_class . ' hidden';
    }
    return compact( 'fp_button_class', 'fp_button_text' );
  }

}
