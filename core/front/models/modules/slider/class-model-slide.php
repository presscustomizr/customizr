<?php
class CZR_cl_slide_model_class extends CZR_cl_Model {
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

  /* In the slider loop */
  function czr_fn_setup_late_properties() {
    //get the current slide;
    $current_slide        = czr_fn_get( 'current_slide' );

    if ( empty ( $current_slide ) )
      return;

    //Extract current slide
    $slide          = $current_slide['slide'];
    $slide_id       = $current_slide['slide_id'];
    $slider_name_id = czr_fn_get( 'slider_name_id' );

    //demo data
    if ( 'demo' == $slider_name_id && is_user_logged_in() )
      $slide = array_merge( $slide,  $this -> czr_fn_set_demo_slide_data( $slide, $slide_id ) );

    //Extract slide properties
    $link_whole_slide   = isset($slide['link_whole_slide']) && $slide['link_whole_slide'] && ! empty( $slide['link_url'] );
    $color_style        = isset($slide['color_style']) ? $slide['color_style'] : '';


    $element_class = array_filter( array( 'slide-'. $slide_id ) );

    //caption elements
    $caption           = $this -> czr_fn_get_slide_caption_model( $slide, $slider_name_id, $slide_id );
    $has_caption       = ! empty( $caption );

    //img elements
    $img_wrapper_class = apply_filters( 'czr_slide_content_class', 'carousel-image', $slide_id );

    $this -> czr_fn_update(
        array_merge( $slide, $caption, 
          compact('element_class', 'img_wrapper_class', 'has_caption', 'link_whole_slide', 'slider_name_id', 'slide_id', 'color_style' ) 
        )
    );
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
    $subtitle                   = isset( $data['text'] ) ? $data['text'] : null;
    $button_text            = isset( $data['button_text'] ) ? $data['button_text'] : null;
    $button_link            = isset( $data['link_url'] ) ? $data['link_url'] : 'javascript:void(0)';

    $show_caption           = ! ( $title == null && $text == null && $button_text == null ) ;
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



  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/
  /**
  * Returns the modified caption data array with a link to the doc
  * Only displayed for the demo slider and logged in users
  *
  * @package Customizr
  * @since Customizr 3.3.+
  *
  */
  function czr_fn_set_demo_slide_data( $slide, $id ) {
    switch ( $id ) {
      case 1 :
        $slide['title']        = __( 'Discover how to replace or remove this demo slider.', 'customizr' );
        $slide['link_url']     = implode('/', array('http:/','docs.presscustomizr.com' , 'article', '102-customizr-theme-options-front-page/#front-page-slider' ) ); //do we need an anchor in the doc?
        $slide['button_text']  = __( 'Check the front page slider doc &raquo;' , 'customizr');
      break;
      case 2 :
        $slide['title']        = __( 'Easily create sliders and add them in any posts or pages.', 'customizr' );
        $slide['link_url']     = implode('/', array('http:/','docs.presscustomizr.com' , 'article', '3-creating-a-slider-with-customizr-wordpress-theme' ) );
        $slide['button_text']  = __( 'Check the slider doc now &raquo;' , 'customizr');
      break;
    };
    return $slide;
  }

}//end class