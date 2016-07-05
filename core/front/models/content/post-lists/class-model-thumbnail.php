<?php
class CZR_cl_thumbnail_model_class extends CZR_cl_Model {
  public $thumb_wrapper_class   = 'thumb-wrapper';
  public $link_class            = 'round-div';

  public $thumb_size;
  public $thumb_img;

  public $type                  = 'standard';


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'thumb_size' ]     = $this -> czr_fn_get_thumb_size();
    return $model;
  }


  function czr_fn_maybe_render_this_model_view() {
    return $this -> visibility && (bool) czr_fn_get( 'czr_fn_has_post_thumbnail' );
  }


  function czr_fn_setup_late_properties() {
    $thumb_model            = CZR_cl_utils_thumbnails::$instance -> czr_fn_get_thumbnail_model( $this -> thumb_size );
    extract( $thumb_model );

    if ( ! isset( $tc_thumb ) || is_null( $tc_thumb ) )
      return;

    $thumb_img              = apply_filters( 'czr_post_thumb_img', $tc_thumb, CZR_cl_utils::czr_fn_id() );
    if ( ! $thumb_img )
      return;

    $element_class          = $this -> czr_fn_get_the_wrapper_class();

    $no_effect_class = $this -> czr_fn_get_no_effect_class( $thumb_model );

    //add the effect
    $link_class             =  apply_filters( 'czr_thumbnail_link_class', array_merge( array( $this -> link_class ), $no_effect_class ) );
    $thumb_wrapper_class    =  apply_filters( 'czr_thumb_wrapper_class', array_merge( array( $this -> thumb_wrapper_class ), $no_effect_class ) );

    //update the model
    $this -> czr_fn_update( compact( 'thumb_wrapper', 'element_class', 'link_class', 'thumb_img') );
  }

  function czr_fn_get_no_effect_class( $thumb_model ) {
    extract( $thumb_model );
    //handles the case when the image dimensions are too small
    $thumb_size       = apply_filters( 'czr_thumb_size' , CZR_cl_init::$instance -> tc_thumb_size , CZR_cl_utils::czr_fn_id() );
    $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
    $no_effect_class  = ( esc_attr( czr_fn_get_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

    return array( $no_effect_class );
  }


  /**
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  protected function czr_fn_get_thumb_size( $_default_size = 'tc-thumb' ) {
    return $_default_size;
  }


  /* The template wrapper class */
  function czr_fn_get_the_wrapper_class() {
    return array( czr_fn_get('tc_thumbnail_width') ); /*retrieved from the post_list layout */
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array( 'thumb_wrapper', 'link' ) as $property ) {
      $model -> {"{$property}_class"} = $this -> czr_fn_stringify_model_property( "{$property}_class" );
    }
  }

}
