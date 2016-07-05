<?php
class CZR_cl_thumbnail_single_model_class extends CZR_cl_Model{
  public $thumb_position;
  public $thumb_size;
  public $thumb_img;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $thumb_position     = CZR_cl_utils_thumbnails::$instance -> czr_fn_get_single_thumbnail_position();
    $thumb_size         = '__before_main_wrapper' == CZR_cl_utils_thumbnails::$instance -> czr_fn_get_single_thumbnail_position() ? 'slider-full' : 'slider';

    return array_merge( $model, compact( 'thumb_position', 'thumb_size' ) );
  }

  function czr_fn_setup_late_properties() {
    $thumb_model            = CZR_cl_utils_thumbnails::$instance -> czr_fn_get_thumbnail_model( $this -> thumb_size );
    extract( $thumb_model );

    if ( ! isset( $tc_thumb ) || is_null( $tc_thumb ) )
      return;

    $thumb_img              = apply_filters( 'czr_post_thumb_img', $tc_thumb, CZR_cl_utils::czr_fn_id() );
    if ( ! $thumb_img )
      return;

    $this -> czr_fn_set_property( 'thumb_img', $thumb_img );
  }

  /**
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_user_options_style_cb( $_css ) {
    $_single_thumb_height   = esc_attr( czr_fn_get_opt( 'tc_single_post_thumb_height' ) );
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
