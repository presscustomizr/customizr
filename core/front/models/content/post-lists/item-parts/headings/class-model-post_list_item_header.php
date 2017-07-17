<?php
class CZR_post_list_item_header_model_class extends CZR_Model {
  public $has_header_format_icon;
  public $entry_header_inner_class;
  public $has_edit_button;
  public $the_title;


  public $defaults = array( 'the_title' => '', 'has_header_format_icon' => false, 'element_class' => '' );

  /*
  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this -> id}, 9999
  */
  /*
  * Each time this model view is rendered setup the current thumbnail items
  */
  function czr_fn_setup_late_properties() {

    $element_class   = $this -> element_class;
    $element_class   = ! is_array( $element_class ) ? explode( ' ', $element_class ) : $element_class;

    $the_title       = $this -> the_title ? $this -> the_title : get_the_title();
    $element_class[] = ! empty( $the_title ) ? '' : 'no-title';

    $this -> czr_fn_set_property( 'element_class', $element_class );
    $this -> czr_fn_set_property( 'the_title', $the_title );
  }

}