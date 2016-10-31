<?php
class CZR_post_list_single_header_model_class extends CZR_Model {
  public $has_header_format_icon;
  public $entry_header_inner_class;
  public $has_edit_button;

  function czr_fn_get_element_class() {
    $element_class = $this -> element_class;
    $element_class = ! is_array( $element_class ) ? explode( ' ', $element_class ) : $element_class;

    array_push( $element_class, ! empty( get_the_title() ) ? '' : 'no-title' );
    return $element_class;
  }
}