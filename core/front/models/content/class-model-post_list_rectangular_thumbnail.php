<?php
class TC_post_list_rectangular_thumbnail_model_class extends TC_post_list_thumbnail_model_class {
  public $thumb_wrapper_class   = '';
  public $link_class            = 'tc-rectangular-thumb';

  /* override */
  function tc_get_no_effect_class( $thumb_model ) {
    return array(); 
  }

  /**
  * override
  * 
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_get_thumb_size( $_default_size = 'tc-thumb' ) {
    $_position = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );  
    return ( 'top' == $_position || 'bottom' == $_position ) ? 'tc_rectangular_size' : $_default_size;
  }
}
