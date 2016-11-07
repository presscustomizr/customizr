<?php
class CZR_slide_model_class extends CZR_Model {
  public $img_wrapper_class;

  public $slide_background;

  public $title;

  public $subtitle;

  public $button_text;
  public $button_link;

  public $link_url;
  public $link_target;
  public $link_whole_slide;

  public $color_style;

  public $has_caption;
  public $slide_id;

  public $slider_name_id;
  public $edit_url;

  /* In the slider loop */
  function czr_fn_setup_late_properties() {
    //get the current slide;
    $current_slide        = $this -> the_slide;

    if ( empty ( $current_slide ) )
      return;

    //Extract current slide
    $slide          = $current_slide['slide'];
    $slide_id       = $current_slide['slide_id'];
    $slider_name_id = czr_fn_get( 'slider_name_id' );

    //Extract slide properties
    $link_whole_slide   = isset($slide['link_whole_slide']) && $slide['link_whole_slide'] && ! empty( $slide['link_url'] );
    $color_style        = isset($slide['color_style']) ? $slide['color_style'] : '';


    $element_class = array_filter( array( 'slide-'. $slide_id ) );

    //caption elements
    $caption           = $this -> czr_fn_get_slide_caption_model( $slide, $slider_name_id, $slide_id );
    $has_caption       = ! empty( $caption );

    //img elements
    $img_wrapper_class = apply_filters( 'czr_slide_content_class', 'carousel-image', $slide_id );

    $edit_url          = $this -> czr_fn_get_the_edit_url( $slide, $slide_id, $slider_name_id );

    $this -> czr_fn_update(
        array_merge( $slide, $caption,
          compact('element_class', 'img_wrapper_class', 'has_caption', 'link_whole_slide', 'slider_name_id', 'slide_id', 'color_style', 'edit_url' )
        )
    );
  }


  function czr_fn_get_the_edit_url( $slide, $slide_id, $slider_name_id ) {
    if ( ! $slide_id  || 'demo' == $slider_name_id )
      return '';

    $show_slide_edit_link  = current_user_can( 'edit_post', $slide_id ) ? true : false;

    if ( ! apply_filters('czr_show_slide_edit_link' , $show_slide_edit_link && ! is_null( $slide['link_id'] ), $slide_id  ) )
      return '';

    $_edit_suffix = ! empty( $slide['edit_suffix'] ) ? $slide['edit_suffix'] : '';
    $_link        = get_edit_post_link( $slide_id );
    return $_link ? $_link . $_edit_suffix : '';
  }

  /**
  * Slide caption submodel
  * @param $_view_model = array( $id, $data , $slider_name_id, $id)
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  * return array( 'button' => array(), $text,
  */
  function czr_fn_get_slide_caption_model( $slide, $slider_name_id, $id ) {
    //filters the data before (=> used for demo for example )
    $data                   = apply_filters( 'czr_slide_caption_data', $slide, $slider_name_id, $id );
    //defaults => reset caption elements
    $defaults  = array(
      'title'        => '',
      'subtitle'     => '',
      'button_text'  => '',
      'button_link'  => 'javascript:void(0)'
    );

    //Extract slide's properties:
    $title                  = isset( $data['title'] ) ? $data['title'] : null;
    $subtitle               = isset( $data['text'] ) ? $data['text'] : null;
    $button_text            = isset( $data['button_text'] ) ? $data['button_text'] : null;
    $button_link            = isset( $data['link_url'] ) ? $data['link_url'] : 'javascript:void(0)';

    $show_caption           = ! ( $title == null && $subtitle == null && $button_text == null ) ;
    if ( ! apply_filters( 'czr_slide_show_caption', $show_caption , $slider_name_id ) )
      return array();


    //apply filters first (Lang plugins)
    $_title                  = isset($title) ? apply_filters( 'czr_slide_title', $title , $id, $slider_name_id ) : '';
    $_subtitle               = isset($subtitle) ? esc_html( apply_filters( 'czr_slide_text', $subtitle, $id, $slider_name_id ) ) : '';
    $_button_text            = isset($button_text) ? apply_filters( 'czr_slide_button_text', $button_text, $id, $slider_name_id ) : '';

    // title elements
    if ( apply_filters( 'czr_slide_show_title', $_title != null, $slider_name_id ) )
      $title        = $_title;

    // text elements
    if (  apply_filters( 'czr_slide_show_text', $_subtitle != null, $slider_name_id ) )
      $subtitle         = $_subtitle;

    // button elements
    if ( apply_filters( 'czr_slide_show_button', $_button_text != null, $slider_name_id ) )
      $button_text  = $_button_text;

    //re-check the caption elements are set
    if ( ! ( isset($title) || isset($text) || isset($button_text) ) )
      return array();

    $caption_elements = wp_parse_args( compact( 'title', 'button_text', 'subtitle', 'button_link' ), $defaults );

    return $caption_elements;
  }

}//end class