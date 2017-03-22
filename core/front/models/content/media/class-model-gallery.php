<?php
class CZR_gallery_model_class extends CZR_Model {

      public   $defaults            = array(
                                          'content'         => null,
                                    );

      private  $_gallery_items;


      /*
      * TODO: implement better retrieving process
      * too much automatization can make us die :D
      */

      /* Public api */
      public function czr_fn_get_content( $post_id = null ) {

            if ( !isset( $this->content ) || is_null( $this->content ) )
                  return $this->czr_fn__get_post_gallery( $post_id );

            return $this->content;

      }


      public function czr_fn_set_content( $post_id = null ) {

            $this->content = $this->czr_fn__get_post_gallery( $post_id );

      }


      public function czr_fn_reset() {

            unset( $this->content );
            unset( $this->_gallery_items );

      }




      public function czr_fn_get_the_gallery_items() {

            if ( ! isset( $this->_gallery_items ) ) {

                  $this->czr_fn__set_the_gallery_items();

            }

            return $this->_gallery_items;

      }




      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current quote
      */
      protected function czr_fn_setup_late_properties() {

            $this -> czr_fn__set_the_gallery_items();

      }




      protected function czr_fn__set_the_gallery_items() {

            //defined in the model base class
            $this->_gallery_items = $this->czr_fn__get_the_gallery_items();

      }




      function czr_fn__get_the_gallery_items() {

            $_content        = $this -> czr_fn_get_content();

            $gallery_items   = array();

            $_gallery_ids    = isset( $_content[ 'ids' ] ) ? explode( ',',  $_content[ 'ids' ] ) : array();

            $_index          = 0;

            foreach( $_content['src'] as $src ) {
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
            $post_gallery     = get_post_gallery( get_the_ID(), false );

            return empty( $post_gallery ) ? false : $post_gallery;

      }

}