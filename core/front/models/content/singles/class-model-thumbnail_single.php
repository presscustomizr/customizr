<?php
class TC_thumbnail_single_model_class extends TC_thumbnail_model_class {
  public $link_class            = 'tc-rectangular-thumb';
  public $thumb_wrapper_class   = 'span12';

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
    return '__before_main_wrapper' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ? 'slider-full' : 'slider';
  }

  function tc_get_the_wrapper_class() {
    return array( 'row-fluid', 'tc-single-post-thumbnail-wrapper' );
  }

  function tc_maybe_render_this_model_view() {
    return $this -> visibility; /*the actual check is done in the controller*/
  }

  /**
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_user_options_style_cb( $_css ) {
    $_single_thumb_height   = esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_height' ) );
    $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
    return sprintf("%s\n%s",
      $_css,
      ".tc-single-post-thumbnail-wrapper .tc-rectangular-thumb {
        max-height: {$_single_thumb_height}px;
        height :{$_single_thumb_height}px
      }\n"
    );
  }
}
