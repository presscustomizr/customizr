<?php
class CZR_audio_model_class extends CZR_Model {

      private static $meta_key      = 'czr_audio_meta';
      private static $meta_fields   = array( 'url' => 'audio_url' );

      protected      $post_id;

      protected      $media;
      protected      $audio;

      public         $defaults      = array(
                                          'media'           => null,
                                          'audio'           => null,
                                          'post_id'         => null,
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
                        'post_id'         => $this->post_id
                  ) );
            }


            $this -> czr_fn__setup_the_audio();

      }




      protected function czr_fn__set_raw_media() {

            $this -> czr_fn_set_property( 'media', $this->czr_fn__get_audio_meta() );

      }




      protected function czr_fn__setup_the_audio() {

            $this -> czr_fn_set_property( 'audio', $this->czr_fn__get_the_audio() );

      }




      protected function czr_fn__get_the_audio() {

            $raw_audio = $this->media;


            if ( empty( $raw_audio ) ) {
               return '';
            }

            return do_shortcode( $this->czr_fn__get_media_embed( $raw_audio ) );

      }




      protected function czr_fn__get_media_embed( $resource ) {

            $resource = $resource ? $this -> czr_fn__validate_media_from_meta( $resource ) : $this->czr_fn_get_audio_meta( $this->post_id );

            //embed
            if ( $resource ) {
                  global $wp_embed;
                  return $wp_embed->run_shortcode( '[embed]' . esc_url( $resource[ self::$meta_fields[ 'url' ] ] ) . '[/embed]' );
            }

            return false;

      }




      protected function czr_fn__get_audio_meta() {

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