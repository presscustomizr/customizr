<?php
class CZR_video_model_class extends CZR_Model {

      private static $meta_key      = 'czr_video_meta';
      private static $meta_fields   = array( 'url' => 'video_url' );

      protected      $post_id;

      protected      $media;
      protected      $video;


      public         $defaults      = array(
                                          'media'           => null,
                                          'video'           => null,
                                          'post_id'         => null,
                                          'element_class'   => '',
                                          'visibility'      => true,
                                    );




      /* Public api */
      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'         => null,

            );

            $args = wp_parse_args( $args, $defaults );

            $args[ 'post_id' ]     = $args[ 'post_id' ] ? $args[ 'post_id' ] : get_the_ID();

            /* This will update the model object properties, merging the $model -> defaults too */
            $this -> czr_fn_update( $args );

            /* Set the media property */
            $this -> czr_fn__set_raw_media();

            /* Toggle visibility */
            $this -> czr_fn_set_property( 'visibility',  (bool) $this->czr_fn_get_raw_media() );

      }




      public function czr_fn_get_raw_media() {

            return $this->media;

      }




      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current thumbnail items
      */
      function czr_fn_setup_late_properties() {

            if ( is_null( $this->media ) ) {

                  $this -> czr_fn_setup( array(
                        'post_id'         => $this->post_id,
                        'element_class'   => $this->element_class
                  ) );
            }


            $this -> czr_fn__setup_the_video();

      }




      protected function czr_fn__set_raw_media() {

            $this -> czr_fn_set_property( 'media', $this->czr_fn__get_video_meta() );

      }




      protected function czr_fn__setup_the_video() {

            $this -> czr_fn_set_property( 'video', $this->czr_fn__get_the_video() );

      }




      protected function czr_fn__get_the_video() {

            $raw_video = $this->media;


            if ( empty( $raw_video ) ) {
               return '';
            }

            return do_shortcode( $this->czr_fn__get_media_embed( $raw_video ) );

      }





      protected function czr_fn__get_media_embed( $resource ) {

            $resource = $resource ? $this -> czr_fn__validate_media_from_meta( $resource ) : $this->czr_fn_get_video_meta( $this->post_id );

            //embed
            if ( $resource ) {
                  global $wp_embed;
                  $video_html = $wp_embed->run_shortcode( '[embed]' . esc_url( $resource[ self::$meta_fields[ 'url' ] ] ) . '[/embed]' );

                  // check whether or not we can allow a responsive wrapper
                  // see: https://github.com/presscustomizr/customizr/issues/1742

                  // first we check if we've been instructed to have a responsive wrapper
                  if ( FALSE !== strpos( $this->element_class, 'czr__r-w' ) ) {
                        // check if the video html starts with an iframe => its responsiveness is well handled via CSS
                        if (  0 !== stripos( trim( $video_html ), '<iframe' ) ) {
                              // if we cannot have a responsive wrapper, let's remove the reponsive class
                              $this->element_class = preg_replace( '/(^|\s)czr__r-w([\S]+)/', '', $this->element_class );
                        }
                  }

                  return $video_html;
            }

            return false;

      }




      protected function czr_fn__get_video_meta() {

            $post_id  = $this->post_id ? $this->post_id : get_the_ID();
            $meta     = get_post_meta( $post_id, self::$meta_key, true );

            return $this -> czr_fn__validate_media_from_meta( $meta );

      }




      protected function czr_fn__validate_media_from_meta( $meta ) {

            if ( is_array( $meta ) && array_key_exists( self::$meta_fields[ 'url' ], $meta ) && !empty( $meta[ self::$meta_fields[ 'url' ] ] ) ) {
               return $meta;
            }

            return false;
      }

}
