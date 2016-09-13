<?php
class CZR_cl_post_list_media_model_class extends CZR_cl_Model {
  public $has_post_media;
  public $icon_type;
  public $media_content;
  public $original_thumb_url;
  public $is_full_image;

  function czr_fn_setup_late_properties() {
    $post_format           = get_post_format();
    $has_post_media        = czr_fn_get( 'has_post_media' );
    $is_full_image         = czr_fn_get( 'is_full_image' );
    
    if ( ! $has_post_media && czr_fn_get( 'has_format_icon_media' ) && ! $is_full_image ) {
      $icon_type           = $post_format ? substr($post_format, strpos($post_format, "-" ) ) : 'text';
      $icon_type           = 'quote' == $post_format ? 'quotes' : $icon_type;
    }

    $this -> czr_fn_update( array(
      'element_class'      =>  czr_fn_get( 'czr_media_col' ),
      'has_post_media'     =>  $has_post_media,
      'icon_type'          =>  isset( $icon_type ) ? $icon_type : false,
      'original_thumb_url' =>  false,
      'is_full_image'      =>  $is_full_image
    ));
  }

  /* Test purpose only */
  function czr_fn_get_media_content() {
    /* Todo: treat case with no media -> show wanrning for admins only */
    /* TEMPORARY: HARD CODED */
    $post_format = get_post_format();

    switch ( $post_format ) {
      case 'video':
          global $post, $wp_embed;
          $slug =  $post->post_name;
          switch ( $slug ) {
            case 'post-format-video-youtube' :
                $content = 'https://youtu.be/FAECyLvSCHg';
                $class   = 'youtube';
              break;
            case 'post-format-video-wordpresstv' :
                $content = 'https://vimeo.com/176587685';
                $class   = 'vimeo';
              break;

            default : 
              $content = '';
              $class   = '';
          }

          global $wp_embed;
          $content = $content ? $wp_embed -> autoembed( $content ) : '';
          $content = ! $content && 'alternate' == czr_fn_get_opt('tc_post_list_grid') && current_user_can('manage_options') ?
            '<div class="tc-placeholder-wrap">
                <p><strong>You need to setup the video post field</strong></p>
            </div>' : $content;

          return $content ? '<div class="video-container '. $class .'">'. $content . '</div>' : '';

      case 'audio':
          global $post, $wp_embed;
          $slug =  $post->post_name;
          switch ( $slug ) {
            case 'post-format-audio' : 
              $content = 'https://soundcloud.com/digitalescort/something-in-the-way';
              $class   = 'soundcloud';
              break;
            case 'another-post-format-audio' :
              $content = 'https://play.spotify.com/track/4rjnWmrSRqXVkFWdKMG3pV';
              $class   = 'spotify';
              break;            
            default : 
              $content = '';
              $class   = '';
          }

          global $wp_embed;
          $content = $content ? $wp_embed -> autoembed( $content ) : '';
          $content = ! $content && 'alternate' == czr_fn_get_opt('tc_post_list_grid') && current_user_can('manage_options') ?
            '<div class="tc-placeholder-wrap">
                <p><strong>You need to setup the audio post field</strong></p>
            </div>' : $content;

          return $content ? '<div class="audio-container '. $class .'">'. $content . '</div>' : '';
      default:
          $_the_thumb = czr_fn_get_thumbnail_model( 'normal' );

          if ( empty ( $_the_thumb['tc_thumb']) )
            return;
          
          //get_the_post_thumbnail( null, 'normal', array( 'class' => 'post-thumbnail' ) );
          /* use utils tc thumb to retrieve the original image size */
          $this -> czr_fn_set_property( 'original_thumb_url', wp_get_attachment_image_src( $_the_thumb[ '_thumb_id' ], 'large')[0] );

          if ( $this -> is_full_image )
            return $_the_thumb[ 'tc_thumb' ] ;
          return '<a href="'. esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ) .'">'.  $_the_thumb[ 'tc_thumb' ] . '</a>';
    }
  }

}