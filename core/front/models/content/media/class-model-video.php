<?php
class CZR_video_model_class extends CZR_Model {

      public $video;

      private static $meta_key     = 'czr_video_meta';
      private static $meta_fields  = array( 'url' => 'video_url' );




      /* Public api */
      public function czr_fn_get_media_content( $post_id = null ) {

            if ( ! isset( $this->video ) ) {
                  return czr_fn_get_media_embed( $resource = null, $post_id );
            }

            return $this->video;
      }


      public function czr_fn_get_video_meta( $post_id = null ) {

               $post_id  = $post_id ? $post_id : get_the_ID();
               $meta     = get_post_meta( $post_id, self::$meta_key, true );

               return empty( $meta ) ? false : $meta;

      }


      public function czr_fn_get_media_embed( $resource = null, $post_id = null  ) {

               $resource = $resource ? $resource : $this->czr_fn_get_video_meta( $post_id );

               //embed
               if ( isset( $resource[ self::$meta_fields[ 'url' ] ] ) && !empty( $resource[ self::$meta_fields[ 'url' ] ] ) ) {
                     global $wp_embed;
                     return $wp_embed->run_shortcode( '[embed]' . esc_url( $resource[ self::$meta_fields[ 'url' ] ] ) . '[/embed]' );
               }

               return false;

      }

}