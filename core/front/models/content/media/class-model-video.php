<?php
class CZR_video_model_class extends CZR_Model {

      private static $meta_key      = 'czr_video_meta';
      private static $meta_fields   = array( 'url' => 'video_url' );

      public         $defaults      = array(
                                          'media'           => null,
                                          'video'           => null,
                                          'post_id'         => null,
                                    );




      /* Public api */
      public function czr_fn_setup_raw_media( $post_id = null ) {

            $this->post_id  = $post_id ? $post_id : get_the_ID();
            $this->media    = $this->czr_fn__get_video_meta( $this->post_id );

      }



      public function czr_fn_get_raw_media() {

            if ( is_null( $this->media ) ) {
                  return $this->czr_fn__get_video_meta( $this->post_id );
            }
            return $this->media;

      }


      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current gallery items
      */
      function czr_fn_setup_late_properties() {

            if ( is_null( $this->media ) ) {
                  $this -> czr_fn_setup_the_raw_media( $this->post_id );
            }

            if ( is_null( $this->video ) ) {
                  $this -> czr_fn__setup_the_video();
            }
      }



      protected function czr_fn__setup_the_video() {

            $this->video = $this->czr_fn__get_the_video();

      }




      protected function czr_fn__get_the_video() {

            $raw_video = $this->czr_fn_get_raw_media();


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
                  return $wp_embed->run_shortcode( '[embed]' . esc_url( $resource[ self::$meta_fields[ 'url' ] ] ) . '[/embed]' );
            }

            return false;

      }




      protected function czr_fn__get_video_meta( $post_id = null ) {

            $post_id  = $post_id ? $post_id : get_the_ID();
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