<?php
class CZR_gallery_model_class extends CZR_Model {

      public   $defaults            = array(
                                          'media'           => null,
                                          'gallery_items'   => null,
                                          'post_id'         => null,
                                    );




      /* Public api */
      public function czr_fn_setup_raw_media( $post_id = null ) {

            $this->post_id  = $post_id ? $post_id : get_the_ID();
            $this->media    = $this->czr_fn__get_post_gallery( $post_id );

      }




      public function czr_fn_get_raw_media( $post_id = null ) {

            if ( is_null( $this->media ) )
                  return $this->czr_fn__get_post_gallery( $post_id );

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


            if ( is_null( $this->gallery_items ) )
               $this -> czr_fn__setup_the_gallery_items();

      }





      protected function czr_fn__setup_the_gallery_items() {

            $this->gallery_items = $this->czr_fn__get_the_gallery_items();

      }




      protected function czr_fn__get_the_gallery_items() {

            $raw_media       = $this -> czr_fn_get_raw_media();

            if ( empty( $raw_media ) )
               return array();


            $gallery_items   = array();

            $_gallery_ids    = isset( $raw_media[ 'ids' ] ) ? explode( ',',  $raw_media[ 'ids' ] ) : array();

            $_index          = 0;

            foreach( $raw_media[ 'src' ] as $src ) {
                  $_original_image  = '';
                  $_alt             = '';

                  if ( isset( $_gallery_ids[ $_index ] ) ) {

                        $_original_image = wp_get_attachment_url( $_gallery_ids[ $_index ] ); //'full' );

                        $_alt            = get_post_meta( $_gallery_ids[ $_index ], '_wp_attachment_image_alt', true );

                  }

                  $gallery_items[] = array(

                        'src'             => $src,
                        'data-mfp-src'    => $_original_image ? $_original_image : $src,
                        'alt'             => $_alt

                  );

                  $_index++;
            }

            return $gallery_items;

      }




      protected function czr_fn__get_post_gallery( $post_id = null ) {

            $post_id          = $post_id ? $post_id : get_the_ID();
            $post_gallery     = get_post_gallery( $post_id, false );

            return empty( $post_gallery ) ? false : $post_gallery;

      }

}