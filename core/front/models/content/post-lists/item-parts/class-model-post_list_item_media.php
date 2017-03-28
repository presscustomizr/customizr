<?php
class CZR_post_list_item_media_model_class extends CZR_Model {

      public $defaults = array(
            'media'                 => null,
            'media_template'        => '',
            'media_args'            => array(),

            'has_bg_link'           => null,
            'has_format_icon'       => false,

            'post_id'               => null,
            'post_format'           => null,
            'type'                  => 'all',
            'use_thumb_placeholder' => false,
            'use_icon'              => false,
            'force_icon'            => false,
      );


      public function czr_fn_get_raw_media() {

            return $this -> media;
      }


      public function czr_fn_setup_late_properties() {
            /*
            * This is if the model is called without setting up the media
            * but thanks to the merged defaults we have at least the defaults properties
            *
            */
            if ( is_null( $this->media ) )
                  $this -> czr_fn_setup_media( $this->post_id, $this->post_format, $this->type, $this->use_thumb_placeholder, $this->use_icon, $this->force_icon );
      }


      /*
      *     $type can be:
      * a) all (video/audio/czr_thumb/thumbnail)
      * b) czr_thumb
      * c) wp_thumb
      *
      *
      */
      public function czr_fn_setup_media( $post_id = null, $post_format = null, $type = 'all', $use_thumb_placeholder = false, $use_icon = false, $force_icon = false ) {

            if ( ! is_null( $this->media ) ) {
                  return;
            }


            $post_id = $post_id ? $post_id : get_the_ID();

            if ( $use_icon && $force_icon ) {

                  $this -> czr_fn__set_icon_media();
                  return;

            }

            $post_format = $post_format ? $post_format : get_post_format( $post_id );

            $media_type  = 'all' != $type ? 'thumb' : $post_format;

            switch ( $media_type ) {

                  case 'video' :
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_video' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_video' );

                              //reset any previous content
                              $_instance->czr_fn_reset_to_defaults();

                        }

                        else {
                              $_id = czr_fn_register( array(

                                    'id'          => 'media_video',
                                    'render'      => false,
                                    'template'    => 'content/media/video',
                                    'model_class' => 'content/media/video'

                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup_raw_media( $post_id );

                        $this -> media           =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/media/video';

                              $this -> media_args      =  array( 'model_id' => 'media_video', 'model_class' => 'content/media/video', 'reset_to_defaults' => false );
                        }

                  break;

                  case 'audio' :
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_audio' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_audio' );

                              //reset any previous content
                              $_instance->czr_fn_reset_to_defaults();

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_audio',
                                    'render'      => false,
                                    'template'    => 'content/media/audio',
                                    'model_class' => 'content/media/audio'
                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup_raw_media( $post_id );

                        $this -> media           =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/media/audio';

                              $this -> media_args      =  array( 'model_id' => 'media_audio', 'model_class' => 'content/media/audio', 'reset_to_defaults' => false );
                        }

                  break;

                  case 'gallery' :

                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_gallery' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_gallery' );

                              //reset any previous content
                              $_instance->czr_fn_reset_to_defaults();

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_gallery',
                                    'render'      => false,
                                    'template'    => 'content/media/gallery',
                                    'model_class' => 'content/media/gallery'
                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup_raw_media( $post_id );

                        $this -> media           =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/media/gallery';

                              $this -> media_args      =  array( 'model_id' => 'media_gallery', 'model_class' => 'content/media/gallery', 'reset_to_defaults' => false );

                              $this -> has_bg_link     =  true;
                        }

                  break;

                  default:
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_thumbnail' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_thumbnail' );

                              //reset any previous content
                              $_instance->czr_fn_reset_to_defaults();

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_thumbnail',
                                    'render'      => false,
                                    'template'    => 'content/media/thumbnail',
                                    'model_class' => 'content/media/thumbnail'

                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup_raw_media( $post_id, $size = 'normal', $use_placeholder = $use_thumb_placeholder,  $use_attachment = 'wp_thumb' != $type );

                        $this -> media           =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template   =  'content/media/thumbnail';

                              $this -> media_args       =  array( 'model_id' => 'media_thumbnail', 'model_class' => 'content/media/thumbnail', 'reset_to_defaults' => false );

                              $this -> has_bg_link      =  true;

                        }

                        elseif ( $use_icon ) {

                              $this -> czr_fn__set_icon_media();

                        }


                  break;

            }

        }


      protected function czr_fn__set_icon_media() {

            $this -> media            = 'format-icon';

            $this -> has_format_icon  = true;
      }

}