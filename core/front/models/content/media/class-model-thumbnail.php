<?php
class CZR_thumbnail_model_class extends CZR_Model {

      public   $defaults            = array(
                                          'media'                 => null,

                                          'thumbnail_item'        => null,
                                          'post_id'               => null,
                                          'size'                  => 'full',
                                          'use_placeholder'       => false,
                                          'use_attachment'        => true
                                    );




      /* Public api */
      public function czr_fn_setup_raw_media( $post_id = null, $size = 'full', $use_placeholder = false, $use_attachment = true ) {

            $this->post_id         = $post_id ? $post_id : get_the_ID();
            $this->use_placeholder = $use_placeholder;
            $this->size            = $size;
            $this->use_attachment  = $use_attachment;

            $this->media    = $this->czr_fn__get_post_thumbnail( $this->post_id, $this->size, $this->use_placeholder, $this->use_attachment );

      }




      public function czr_fn_get_raw_media( $post_id = null ) {

            if ( is_null( $this->media ) )
                  return $this->czr_fn__get_post_thumbnail( $this->post_id, $this->size, $this->use_placeholder, $this->use_attachment );

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
                  $this -> czr_fn_setup_the_raw_media( $this->post_id, $this->size, $this->use_placeholder, $this->use_attachment );
            }


            if ( is_null( $this->thumbnail_item ) )
               $this -> czr_fn__setup_the_thumbnail_properties();

      }





      protected function czr_fn__setup_the_thumbnail_properties() {

            $this->thumbnail_item = $this->czr_fn__get_the_thumbnail_properties();

      }




      protected function czr_fn__get_the_thumbnail_properties() {

            $raw_media       = $this -> czr_fn_get_raw_media();

            if ( empty( $raw_media ) )
               return array();

            $thumbnail_item  = array(
                  'img'           => $raw_media[ 'tc_thumb' ],
                  //lightbox
                  'lightbox_url'  => wp_get_attachment_url( $raw_media[ '_thumb_id' ] ), //full

            );

            return $thumbnail_item;

      }




      protected function czr_fn__get_post_thumbnail( $post_id = null, $size = 'full', $use_placeholder = false, $use_attachment = true ) {

            $post_id              = $post_id ? $post_id : get_the_ID();

            //Get the Customizr thumbnail or the WordPress post thumbnail
            if ( $use_attachment ) {

                  //model array
                  $post_thumbnail = czr_fn_get_thumbnail_model( $size, $post_id, $_custom_thumb_id = null, $_enable_wp_responsive_imgs = null, $_filtered_thumb_size_name = null, $use_placeholder );

            }

            else {
                  //build array
                  $id                            = get_the_post_thumbnail_id( $post_id );
                  if ( $id ) {
                        $post_thumbnail[ '_thumb_id' ] = $id;
                        $post_thumbnail[ 'tc_thumb' ]  = get_the_post_thumbnail( $post_id, $size );

                  }
                  else {
                        $post_thumbnail = array();
                  }
            }


            return empty( $post_thumbnail ) ? false : $post_thumbnail;

      }

}