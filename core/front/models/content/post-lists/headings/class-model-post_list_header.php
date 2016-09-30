<?php
class CZR_cl_post_list_header_model_class extends CZR_cl_Model {
  public $has_header_format_icon;
  public $entry_title_class;

  function czr_fn_setup_late_properties() {
    $element_class = czr_fn_get('entry_header_class');
    $element_class = ! is_array( $element_class ) ? explode( ' ', $element_class ) : $element_class;

    array_push( $element_class, ! empty( get_the_title() ) ? '' : 'no-title' );

    $this -> czr_fn_update( array(
      'has_header_format_icon' => czr_fn_get( 'has_header_format_icon' ),
      'element_class'          => $element_class,
      'entry_title_class'      => czr_fn_get( 'entry_title_class' )
    ) );
  }
}