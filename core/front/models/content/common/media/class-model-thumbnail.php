<?php
class CZR_thumbnail_model_class extends CZR_Model {

      protected  $post_id;

      protected  $media;
      protected  $thumbnail_item;


      protected  $size;
      protected  $use_placeholder;

      protected  $use_attachment;

      public     $defaults          = array(
                                          'media'                 => null,

                                          'thumbnail_item'        => null,

                                          'post_id'               => null,
                                          'size'                  => 'full',
                                          'use_placeholder'       => false,
                                          'use_attachment'        => true,

                                          'visibility'            => true,

                                          'has_lightbox'          => true
                                    );




      /* Public api */
      public function czr_fn_get_image() {

            return array_key_exists( 'img', $this->thumbnail_item ) ? $this->thumbnail_item[ 'img' ] : null;

      }

      public function czr_fn_get_lightbox_url() {

            return array_key_exists( 'lightbox_url', $this->thumbnail_item ) ? $this->thumbnail_item[ 'lightbox_url' ] : null;

      }



      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'         => null,
                  'size'            => 'full',
                  'use_placeholder' => false,
                  'use_attachment'  => true,

                  'has_lightbox'    => true

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
                        'size'            => $this->size,
                        'use_placeholder' => $this->use_placeholder,
                        'use_attachment'  => $this->use_attachment,
                        'has_lightbox'    => $this->has_lightbox,

                  ) );

            }


            $this -> czr_fn__setup_the_thumbnail_item();

      }




      protected function czr_fn__set_raw_media() {

            $this -> czr_fn_set_property( 'media', $this->czr_fn__get_post_thumbnail() );

      }




      protected function czr_fn__setup_the_thumbnail_item() {

            $this -> czr_fn_set_property( 'thumbnail_item', $this->czr_fn__get_the_thumbnail_item() );

      }




      protected function czr_fn__get_the_thumbnail_item() {

            $raw_media       = $this -> media;

            if ( empty( $raw_media ) )
               return array();


            $thumbnail_item  = array(

                  'img'           => $raw_media[ 'tc_thumb' ],
                  //lightbox
                  'lightbox_url'  => $this->has_lightbox && array_key_exists( 'is_placeholder', $raw_media ) && $raw_media[ 'is_placeholder' ] ? '' : wp_get_attachment_url( $raw_media[ '_thumb_id' ] ), //full

            );

            return $thumbnail_item;

      }




      protected function czr_fn__get_post_thumbnail() {

            $post_id = $this->post_id ? $this->post_id : get_the_ID();

            //Get the Customizr thumbnail or the WordPress post thumbnail
            if ( $this->use_attachment ) {

                  //model array
                  $post_thumbnail = czr_fn_get_thumbnail_model( $this->size, $post_id, $_custom_thumb_id = null, $_enable_wp_responsive_imgs = null, $_filtered_thumb_size_name = null, $this->use_placeholder );

            }

            else {
                  //build array
                  $id                                  = get_post_thumbnail_id( $post_id );
                  if ( $id ) {
                        $post_thumbnail[ '_thumb_id' ]       = $id;
                        $post_thumbnail[ 'tc_thumb' ]        = get_the_post_thumbnail( $post_id, $this->size );

                  }
                  else {
                        $post_thumbnail = array();
                  }
            }


            return empty( $post_thumbnail ) ? false : $post_thumbnail;

      }

}