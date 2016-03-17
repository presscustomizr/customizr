<?php
class TC_slide_model_class extends TC_Model {
  public $item_class;
  public $img_wrapper_class;
  public $caption_class;
  
  public $name_id;
  public $img;

  public $title;
  public $title_class;
  public $title_tag;

  public $text;
  public $text_class;

  public $button_text;
  public $button_class;
  public $button_link;

  public $link_url;
  public $link_target;
  
  public $color_style = '';

  public $has_caption;

  function __construct( $model ) {
    parent::__construct( $model );

    //inside the slider loop but before rendering set some properties    
    add_action( $this -> hook          , array( $this, 'tc_set_this_properties' ), -1 );    
  }


  function tc_set_this_properties() {
    //get the current slide;
    $slide   = get_query_var( 'tc_slide', null );
    if ( empty( $slide ) ) {
      $this -> tc_set_property( 'visibility', false );
      return;      
    }

    //array( $id, $data , $slider_name_id, $img_size )    
    extract ( $slide );
    
    $item_class = sprintf('%1$s %2$s',
      $data['active'],
      'slide-'.$id
    );
    
    //caption elements
    $caption           = $this -> tc_get_slide_caption_model( $slide );
    $has_caption       = ! empty( $caption );

    //color style
    $color_style       = $data['color_style'];

    //link target
    $link_target       = $data['link_target'];
    //img elements
    $img               = $data['slide_background'];
    $img_wrapper_class = apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) );

    $this -> tc_update(
        array_merge( $caption, compact('item_class', 'img', 'img_wrapper_class', 'color_style', 'has_caption', 'link_target' ) )
    );
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) { 
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array( 'caption', 'text', 'title', 'button' ) as $property ) {
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
    }
  }
  /**
  * Slide caption submodel
  * @param $_view_model = array( $id, $data , $slider_name_id, $img_size )
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  * return array( 'button' => array(), $text, 
  */
  function tc_get_slide_caption_model( $slide ) {
    //extract $_view_model = array( $id, $data , $slider_name_id, $img_size )
    extract( $slide );
    //filters the data before (=> used for demo for example )
    $data                   = apply_filters( 'tc_slide_caption_data', $data, $slider_name_id, $id );
    $show_caption           = ! ( $data['title'] == null && $data['text'] == null && $data['button_text'] == null ) ;
    if ( ! apply_filters( 'tc_slide_show_caption', $show_caption , $slider_name_id ) )
      return array();


    //apply filters first
    /* classes and tags can be skipped if we decided that must be changed only in the templates */
    $caption_class          = apply_filters( 'tc_slide_caption_class', array( 'carousel-caption' ), $show_caption, $slider_name_id );

    $_title                  = isset($data['title']) ? apply_filters( 'tc_slide_title', $data['title'] , $id, $slider_name_id ) : '';
    $_text                   = isset($data['text']) ? esc_html( apply_filters( 'tc_slide_text', $data['text'], $id, $slider_name_id ) ) : '';
    
    //    $color_style            = apply_filters( 'tc_slide_color', $data['color_style'], $id, $slider_name_id );

    $_button_text            = isset($data['button_text']) ? apply_filters( 'tc_slide_button_text', $data['button_text'], $id, $slider_name_id ) : '';

    //computes the link
    $button_link            = apply_filters( 'tc_slide_button_link', $data['link_url'] ? $data['link_url'] : 'javascript:void(0)', $id, $slider_name_id );
    


    // title elements
    if ( apply_filters( 'tc_slide_show_title', $_title != null, $slider_name_id ) ) {
      $title_tag    = apply_filters( 'tc_slide_title_tag', 'h1', $slider_name_id );
      $title        = $_title;
      $title_class  = implode( ' ', apply_filters( 'tc_slide_title_class', array( 'slide-title' ), $title , $slider_name_id ) );
    }else {
      $title        = '';
      $title_tag    = '';
      $title_class  = '';
    }

    // text elements
    if (  apply_filters( 'tc_slide_show_text', $_text != null, $slider_name_id ) ) {
      $text         = $_text;
      $text_class   = implode( ' ', apply_filters( 'tc_slide_text_class', array( 'lead' ), $text, $slider_name_id ) );
    }else {
      $text         = '';
      $text_class   = '';
    }

    // button elements
    if ( apply_filters( 'tc_slide_show_button', $_button_text != null, $slider_name_id ) ) {
      $button_text  = $_button_text;
      $button_class = implode( ' ', apply_filters( 'tc_slide_button_class', array( 'btn', 'btn-large', 'btn-primary' ), $button_text, $slider_name_id ) ) ;
      $button_link  = apply_filters( 'tc_slide_button_link', $data['link_url'] ? $data['link_url'] : 'javascript:void(0)', $id, $slider_name_id ) ;
    }else {
      $button_text  = '';
      $button_class = '';
      $button_link  = ''; 
    }

    //re-check the caption elements are set
    if ( ! ( $title || $text || $button_text ) )
      return array();

    return compact( 'caption_class', 'title', 'title_class', 'title_tag', 'text', 'text_class', 'button_text', 'button_link', 'button_class' );
  }
}//end class
