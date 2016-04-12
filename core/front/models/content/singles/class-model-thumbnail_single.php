<?php
class TC_thumbnail_single_model_class extends TC_Model{
  public $thumb_position;
  public $thumb_size;
  public $thumb_img;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $thumb_position     = TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position();
    $thumb_size         = '__before_main_wrapper' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ? 'slider-full' : 'slider';

    return array_merge( $model, compact( 'thumb_position', 'thumb_size' ) );
  }

  function tc_setup_late_properties() {
    $thumb_model            = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model( $this -> thumb_size );
    extract( $thumb_model );

    $thumb_img              = apply_filters( 'tc_post_thumb_img', $tc_thumb, TC_utils::tc_id() );
    if ( ! $thumb_img )
      return;

    $this -> tc_set_property( 'thumb_img', $thumb_img );
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
