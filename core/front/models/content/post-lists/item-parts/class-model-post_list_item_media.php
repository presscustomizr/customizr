<?php
class CZR_post_list_item_media_model_class extends CZR_Model {
  public $has_post_media;
  public $has_format_icon_media;
  public $only_thumb;
  public $media_content;
  public $original_thumb_url;
  public $is_full_image;

  public $defaults = array( 'use_placeholder' => false, 'allow_css_centering' => true, 'original_thumb_url' => '' );

  function czr_fn_get_element_class() {
    $post_format           = get_post_format();

    $element_class = ! empty($this-> element_class) ? $this ->element_class : array();
    $element_class = ! is_array( $element_class ) ? explode(' ', $element_class ) : $element_class;

    //centering
    if ( $this -> only_thumb || ( ! $this->has_format_icon_media && 'audio' != $post_format ) ) {
      if ( esc_attr( czr_fn_get_opt( 'tc_center_img' ) ) )
        $_centering_class = 'js-media-centering';
      elseif ( $this -> allow_css_centering )
        $_centering_class = 'no-js-media-centering';

      if ( ! empty( $_centering_class ) )
        array_push( $element_class, $_centering_class );
    }

    return $element_class;
  }

  /* To treat default thumb, might be passed by the parent ...*/
  /* Test purpose only */
  function czr_fn_get_media_content() {
    if ( $this -> has_format_icon_media )
      return;

    $post_format = $this -> only_thumb ? '' : get_post_format();

    /* TEMPORARY: HARD CODED */
    switch ( $post_format ) {
      case 'video':
          global $post, $wp_embed;
          $slug =  $post->post_name;
          switch ( $slug ) {
            case 'post-format-video-youtube' :
                $content = $wp_embed -> autoembed( 'https://youtu.be/FAECyLvSCHg' );
                $class   = 'youtube';
              break;
            case 'post-format-video-wordpresstv' :
                $content = $wp_embed -> autoembed( 'https://vimeo.com/176587685' );
                $class   = 'vimeo';
              break;
            default :
              $content = '';
              $class   = '';
          }

          /*
          $content = ! $content && 'alternate' == czr_fn_get_opt('tc_post_list_grid') && current_user_can('manage_options') ?
            '<div class="tc-placeholder-wrap">
                <p><strong>You need to setup the video post field</strong></p>
            </div>' : $content;

          */
          if ( ! $content ) {
            $video_instance = czr_fn_register( array( 'id' => 'video', 'render' => false, 'template' => 'content/media/video', 'model_class' => 'content/media/video' ) );
            $content = czr_fn_get_model_instance( $video_instance )->czr_fn_get_media_embed();
          }
          if ( ! $content ) {

            $placeholder = 'alternate' == czr_fn_get_opt('tc_post_list_grid') && current_user_can('manage_options') ? '<div class="tc-placeholder-wrap">
                <p><strong>You need to setup the video post field</strong></p>
            </div>' : '';

            return '<div class="video-container '. $class .'">'. $placeholder . '</div>';

          }else {
            return '<div class="video-container '. $class .'">'. $content . '</div>';
          }
          break;

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

          if ( ! $content ) {
            $audio_instance = czr_fn_register( array( 'id' => 'audio', 'render' => false, 'template' => 'content/media/audio', 'model_class' => 'content/media/audio' ) );
            $content = czr_fn_get_model_instance( $audio_instance )->czr_fn_get_media_embed();
          }
          if ( ! $content ) {

            $placeholder = 'alternate' == czr_fn_get_opt('tc_post_list_grid') && current_user_can('manage_options') ? '<div class="tc-placeholder-wrap">
                <p><strong>You need to setup the audio post field</strong></p>
            </div>' : '';

            return '<div class="audio-container '. $class .'">'. $placeholder . '</div>';

          }else {
            return '<div class="audio-container '. $class .'">'. $content . '</div>';
          }

          break;

      case 'gallery':

        /* Test */
        $gallery = !czr_fn_is_registered( 'gallery' ) ? czr_fn_register(
           array(
              'id'          => 'gallery',
              'render'      => false,
              'template'    => 'content/media/gallery',
              'model_class' => 'content/media/gallery',
           )
        ) : 'gallery' ;

        $gallery_instance = czr_fn_get_model_instance( $gallery );
        //reset any previous content
        $gallery_instance->czr_fn_reset();

        $content = $gallery_instance->czr_fn_get_content();

        if ( ! $content ) {
           //we need to return a placeholder;
           return false;
        }

        $the_permalink      = esc_url( apply_filters( 'the_permalink', get_the_permalink() ) );
        $the_title_attribute = the_title_attribute( array( 'before' => __('Permalink to ', 'customizr'), 'echo' => false ) );



        $_bg_link = '<a class="bg-link" rel="bookmark" title="'. $the_title_attribute.'" href="'.$the_permalink.'"></a>';

        //post action;
        ob_start();
          czr_fn_render_template( 'modules/common/post_action_button', array( 'model_args' => array( 'post_action_link' => '#', 'post_action_link_class' => 'expand-img-gallery'  ) ) );
        $_post_action = ob_get_clean();


        //gallery
        ob_start();

          czr_fn_get_view_instance( $gallery ) -> czr_fn_maybe_render();

        $_gallery_html = ob_get_clean();

        return sprintf( "%s%s%s", $_bg_link, $_post_action, $_gallery_html );

        break;
      ;

      default:
          $_the_thumb = czr_fn_get_thumbnail_model( 'normal', null, null, null, null, $this -> use_placeholder );

          if ( empty ( $_the_thumb['tc_thumb']) ) {
            return ' ';
          }

          //get_the_post_thumbnail( null, 'normal', array( 'class' => 'post-thumbnail' ) );
          /* use utils tc thumb to retrieve the original image size */
          if ( isset($_the_thumb[ '_thumb_id' ]) )
            $this -> czr_fn_set_property( 'original_thumb_url', wp_get_attachment_image_src( $_the_thumb[ '_thumb_id' ], 'large')[0] );

          $the_permalink      = esc_url( apply_filters( 'the_permalink', get_the_permalink() ) );
          $the_title_attribute = the_title_attribute( array( 'before' => __('Permalink to ', 'customizr'), 'echo' => false ) );


          $_bg_link = '<a class="bg-link" rel="bookmark" title="'. $the_title_attribute.'" href="'.$the_permalink.'"></a>';
          return $_bg_link . $_the_thumb[ 'tc_thumb' ];
    }
  }

  function czr_fn_get_has_media_action() {
    return $this -> has_post_media && ( $this -> only_thumb ||
      ! in_array( get_post_format(), array('video', 'audio', 'gallery') ) );
  }

}