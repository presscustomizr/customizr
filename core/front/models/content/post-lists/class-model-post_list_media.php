<?php
class CZR_cl_post_list_media_model_class extends CZR_cl_Model {
  public $czr_has_media;
  public $icon_type;
  public $media_content;

  function czr_fn_setup_late_properties() {
    $post_format = get_post_format();

    $icon_type = $post_format ? substr($post_format, strpos($post_format, "-" ) ) : 'text';
    $icon_type = 'quote' == $post_format ? 'quotes' : $icon_type;

    $this -> czr_fn_update( array(
      'element_class' =>   czr_fn_get( 'czr_media_col' ),
      'czr_has_media' =>   czr_fn_get( 'czr_has_post_media' ),
      'icon_type'     =>   $icon_type,
      ''
    ));
  }

  /* Test purpose only */
  function czr_fn_get_media_content() {
    /* Todo: treat case with no media -> show wanrning for admins only */
    if ( 'video' == get_post_format() ) {
      global $post;
      $slug =  $post->post_name;
      switch ( $slug ) {
        case 'post-format-video-youtube' : $content = 'https://youtu.be/FAECyLvSCHg';
          break;
        case 'post-format-video-wordpresstv': $content = 'https://vimeo.com/176587685';
          break;
        default: $content = "You need to setup the video post field";
      }
      
      return '<div class="video-container">'. apply_filters( 'the_content', $content ) . '</div>';      
    }
    else
      return '<a href="'. get_the_permalink() .'">'. get_the_post_thumbnail( null, 'normal', array( 'class' => 'post-thumbnail' ) ) . '</a>';
  }

}