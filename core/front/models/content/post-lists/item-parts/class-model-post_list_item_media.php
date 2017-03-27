<?php
class CZR_post_list_item_media_model_class extends CZR_Model {

      public $defaults = array(
            'media'             => null,
            'media_template'    => '',
            'media_args'        => array(),

            'has_bg_link'       => null,
      );


      public function czr_fn_get_raw_media() {
            return $this -> media;
      }


      public function czr_fn_setup_late_properties() {

            if ( is_null( $this->media ) )
                  $this -> czr_fn_setup_media();

      }



      public function czr_fn_setup_media( $post_id = null, $post_format = null, $type = 'all', $use_placeholder = false ) {

            if ( isset( $this->media ) )
                  return $this->media;

            $post_format = $post_format ? $post_format : get_post_format( $post_id );

            switch ( $post_format ) {

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

                        $this -> media_template  =  'content/media/video';

                        $this -> media_args      =  array( 'model_id' => 'media_video', 'model_class' => 'content/media/video', 'reset_to_defaults' => false );

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

                        $this -> media_template  =  'content/media/audio';

                        $this -> media_args      =  array( 'model_id' => 'media_audio', 'model_class' => 'content/media/audio', 'reset_to_defaults' => false );

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

                        $this -> media_template  =  'content/media/gallery';

                        $this -> media_args      =  array( 'model_id' => 'media_gallery', 'model_class' => 'content/media/gallery', 'reset_to_defaults' => false );

                        $this -> has_bg_link     = true;

                  break;

                  default:
                  //TODO
                        $_the_thumb = czr_fn_get_thumbnail_model( 'normal', null, null, null, null, $use_placeholder );

                        if ( empty ( $_the_thumb['tc_thumb']) ) {
                              return ' ';
                        }

                        //get_the_post_thumbnail( null, 'normal', array( 'class' => 'post-thumbnail' ) );
                        /* use utils tc thumb to retrieve the original image size */
                        if ( isset($_the_thumb[ '_thumb_id' ]) )
                      $this -> czr_fn_set_property( 'original_thumb_url', wp_get_attachment_image_src( $_the_thumb[ '_thumb_id' ], 'large')[0] );

                    $the_permalink       = esc_url( apply_filters( 'the_permalink', get_the_permalink() ) );
                    $the_title_attribute = the_title_attribute( array( 'before' => __('Permalink to ', 'customizr'), 'echo' => false ) );


                    $_bg_link = '<a class="bg-link" rel="bookmark" title="'. $the_title_attribute.'" href="'.$the_permalink.'"></a>';

                    return $_bg_link . $_the_thumb[ 'tc_thumb' ];
            }

        }





}