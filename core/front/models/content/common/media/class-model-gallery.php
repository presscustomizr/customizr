<?php
class CZR_gallery_model_class extends CZR_Model {

      protected      $post_id;

      protected      $media;
      protected      $gallery_items;
      protected      $size;

      /**
      * @override
      */
      public function __construct( $model = array() ) {

            /*
            * the model->defaults by default are:
            * 1) merged to the model array at instantiation time (see instance's method CZR_model.czr_fn_update )
            * 2) they also can be merged (default behavior) to the passed args when updating a model while rendering its template
            * 3) they can override the model properties when invoking instance's method CZR_model.czr_fn_reset_to_defaults
            */
            $this->defaults = array(

                  'media'           => null,
                  'gallery_items'   => null,
                  'post_id'         => null,
                  'visibility'      => true,
                  'size'            => 'full',
                  'has_lightbox'    => czr_fn_opt( 'tc_fancybox' ),

            );

            parent::__construct( $model );

      }


      /* Public api */
      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'         => null,
                  'size'            => 'full',

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
      * Each time this model view is rendered setup the current gallery items
      */
      function czr_fn_setup_late_properties() {


            if ( is_null( $this->media ) ) {
                  $this -> czr_fn_setup( array(
                        'post_id'         => $this->post_id,
                        'size'            => $this->size,
                        'has_lightbox'    => $this->has_lightbox,
                  ) );
            }


            $this -> czr_fn__setup_the_gallery_items();

      }




      protected function czr_fn__set_raw_media() {

            $this -> czr_fn_set_property( 'media', $this->czr_fn__get_post_gallery() );

      }




      protected function czr_fn__setup_the_gallery_items() {

            $this -> czr_fn_set_property( 'gallery_items', $this->czr_fn__get_the_gallery_items() );

      }




      protected function czr_fn__get_the_gallery_items() {

            $raw_media       = $this -> media;

            if ( empty( $raw_media ) )
                  return array();


            $gallery_items   = array();

            if ( czr_fn_is_checked( 'tc_slider_img_smart_load' ) ) {
                add_filter( 'wp_get_attachment_image_attributes', array( $this, 'czr_fn_set_smartload_skip_class'), 999 );
            }
            foreach ( array_keys( $raw_media ) as $id ) {

                  $img_attrs  = $this->has_lightbox ? array(
                              'data-mfp-src'    => wp_get_attachment_url( $id )
                        ) : array();

                  $img_html = wp_get_attachment_image( $id, $this->size, false, $img_attrs );

                  if ( ! czr_fn_is_ajax() && czr_fn_is_checked( 'tc_slider_img_smart_load' ) ) {
                      $gallery_items[] = czr_fn_parse_imgs( $img_html );//<- to prepare the img smartload without using the filter 'czr_thumb_html'  ( not declared if smartload not globally enabled )
                  } else {
                      $gallery_items[] = apply_filters( 'czr_thumb_html', //<- to allow the img smartload
                          $img_html,
                          $requested_size = $this->size,
                          $post_id = $this->post_id,
                          $custom_thumb_id = null,
                          $_img_attr = null,
                          $tc_thumb_size = $this->size
                      );
                  }
            }
            if ( czr_fn_is_checked( 'tc_slider_img_smart_load' ) ) {
                remove_filter( 'wp_get_attachment_image_attributes', array( $this, 'czr_fn_set_smartload_skip_class'), 999 );
            }
            return $gallery_items;

      }

      /* ------------------------------------------------------------------------- *
      *  SET SMART LOAD CLASS TO IMG => disable the smartload on load
      /* ------------------------------------------------------------------------- */
      //hook : wp_get_attachment_image_attributes
      function czr_fn_set_smartload_skip_class( $attr ) {
          //@see assets/front/js/libs/jquery-plugins/jqueryimgSmartLoad.js
          $attr['class'] = ( isset( $attr['class'] ) && is_string( $attr['class'] ) ) ? $attr['class'] . ' tc-smart-load-skip' : 'tc-smart-load-skip';
          return $attr;
      }



      protected function czr_fn__get_post_gallery() {

            $post_id          = $this->post_id ? $this->post_id : get_the_ID();
            $post_gallery     = false;

            //following a simplified version of built-in get_post_galleries() you can find in wp-includes/media.php
            //get first post gallery
            if ( ! $post = get_post( $post_id ) )
                  return $post_gallery;

            if ( ! has_shortcode( $post->post_content, 'gallery' ) )
                  return $post_gallery;

            if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
                  foreach ( $matches as $shortcode ) {
                        if ( 'gallery' === $shortcode[2] ) {

                              $shortcode_attrs = shortcode_parse_atts( $shortcode[3] );
                              if ( ! is_array( $shortcode_attrs ) ) {
                                    $shortcode_attrs = array();
                              }

                              //set our type
                              $shortcode_attrs[ 'type' ] = 'attachments-only';

                              // Specify the post id of the gallery we're viewing if the shortcode doesn't reference another post already.
                              if ( ! isset( $shortcode_attrs['id'] ) ) {
                                   $shortcode_attrs[ 'id' ] = intval( $post->ID );
                              }

                              if ( ! empty( $shortcode_attrs['ids'] ) ) {
                                    // 'ids' is explicitly ordered, unless you specify otherwise.
                                    if ( empty( $shortcode_attrs['orderby'] ) ) {
                                          $shortcode_attrs['orderby'] = 'post__in';
                                    }
                                    $shortcode_attrs['include'] = $shortcode_attrs['ids'];
                              }

                              $post_gallery = CZR_gallery::$instance->czr_fn_czr_gallery( $post_gallery, $shortcode_attrs, '' );
                              break;

                        }
                  }
            }

            return $post_gallery;

      }

}