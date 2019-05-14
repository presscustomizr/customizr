<?php
class CZR_quote_model_class extends CZR_Model {

      private static $meta_key      = 'czr_quote_meta';

      private static $meta_fields   = array(
                                          'text'            => 'quote_text',
                                          'source'          => 'quote_author'
                                    );

      protected $content;
      protected $quote_item;


      public $defaults              = array(
                                          'content'         => null,
                                          'quote_item'      => '',

                                          'post_id'         => null,
                                          'visibility'       => true,
                                    );






      /* Public api */



      public function czr_fn_get_quote_text() {
            if ( array_key_exists( 'quote_text', $this->quote_item ) ) {
                  $text = esc_html( $this->quote_item[ 'quote_text' ] );

                  // If in archives and no post title the quote text is also a link to the post
                  if ( ! is_single() && ! get_the_title() ) {
                        $text = sprintf( '<a href="%1$s">%2$s</a>',
                              esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ),
                              $text
                        );
                  }

                  return $text;
            }
            return false;
      }




      public function czr_fn_get_quote_source() {

            return array_key_exists( 'quote_source', $this->quote_item ) ? esc_html( $this->quote_item[ 'quote_source' ] ) : false;

      }




      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'         => null,

            );

            $args = wp_parse_args( $args, $defaults );

            $args[ 'post_id' ]     = $args[ 'post_id' ] ? $args[ 'post_id' ] : get_the_ID();

            /* This will update the model object properties, merging the $model -> defaults too */
            $this -> czr_fn_update( $args );

            /* Set the media property */
            $this -> czr_fn__set_raw_content();

            /* Toggle visibility */
            $this -> czr_fn_set_property( 'visibility',  (bool) $this->czr_fn_get_raw_content() );

      }




      public function czr_fn_get_raw_content() {

            return $this->content;

      }




      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current quote
      */
      protected function czr_fn_setup_late_properties() {

            if ( is_null( $this->content ) ) {
                  $this -> czr_fn_setup( array(
                        'post_id'         => $this->post_id
                  ) );
            }


            $this->czr_fn__setup_the_quote_item();

      }



      protected function czr_fn__set_raw_content() {

            $this -> czr_fn_set_property( 'content', $this->czr_fn__get_post_meta() );

      }



      protected function czr_fn__setup_the_quote_item() {

            $this->czr_fn_set_property( 'quote_item', $this->czr_fn__get_the_quote() );
      }




      protected function czr_fn__get_the_quote() {

            $content      = $this->content;


            if ( empty( $content ) )
                  return array();


            $_text        = $this->czr_fn__get_quote_text();

            $_source      = $this->czr_fn__get_quote_source();

            return array(
                  'quote_text'       => $_text,
                  'quote_source'     => $_source,
            );

      }




      protected function czr_fn__get_quote_text() {

            $_content         = $this->content;

            if ( ! isset( $_content[ 'text' ] ) )
                  return false;


            return $_content[ 'text' ];

      }




      protected function czr_fn__get_quote_source() {

            $_content         = $this->content;

            if ( ! isset( $_content[ 'source' ] ) )
                  return false;

            return $_content[ 'source' ];

      }




      protected function czr_fn__get_post_meta() {

            $post_id  = $this->post_id ? $this->post_id : get_the_ID();
            $meta     = get_post_meta( $post_id, self::$meta_key, true );

            return $this -> czr_fn__validate_media_from_meta( $meta );

      }




      protected function czr_fn__validate_media_from_meta( $meta ) {


            if ( ! ( is_array( $meta ) && array_key_exists( self::$meta_fields[ 'text' ], $meta ) && !empty( $meta[ self::$meta_fields[ 'text' ] ] ) ) )
                  return false;

            $content = array();

            //build content array
            foreach ( self::$meta_fields as $key => $meta_field ) {
                  if ( array_key_exists( $meta_field, $meta ) && !empty( $meta[ $meta_field ] ) ) {
                        $content[ $key ] = $meta[ $meta_field ];
                  }
            }

            return empty( $content ) ? false : $content;
      }

}