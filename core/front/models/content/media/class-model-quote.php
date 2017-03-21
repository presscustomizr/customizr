<?php
class CZR_quote_model_class extends CZR_Model {

      private static $meta_key      = 'czr_quote_meta';

      private static $meta_fields   = array(
                                          'text'            => 'quote_text',
                                          'source'          => 'quote_author'
                                    );

      public $defaults              = array(
                                          'content'         => null,
                                          'quote_text'      => '',
                                          'quote_source'    => '',
                                    );

      private $_quote;




      /* Public api */
      public function czr_fn_get_content( $post_id = null ) {

            if ( is_null( $this->content ) )
                  return $this->czr_fn__get_parsed_content( $resource = null, $post_id );

            return $this->content;

      }

      public function czr_fn_reset() {
            unset($this->content);
            unset($this->quote);
      }

      public function czr_fn_get_quote_text() {

            return $this->_quote[ 'quote_text' ];

      }



      public function czr_fn_get_quote_source() {

            return $this->_quote[ 'quote_source' ];

      }



      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current quote
      */
      protected function czr_fn_setup_late_properties() {

            //defined in the model base class
            $this->_quote = $this->czr_fn__get_the_quote();

      }


      protected function czr_fn__get_the_quote() {


            $_text        = $this->czr_fn__get_quote_text();

            if ( ! $_text )
                  return $this -> defaults;

            $_source      = $this->czr_fn__get_quote_source();

            return array(
                  'quote_text'       => $_text,
                  'quote_source'     => $_source,
            );

      }



      protected function czr_fn__get_quote_text() {

            $_content         = $this->czr_fn_get_content();

            if ( ! isset( $_content[ 'text' ] ) )
                  return false;


            $text             = $_content[ 'text' ];

            $text             = !get_the_title() ? sprintf( '<a title="%1$s" href="%2$s">%3$s</a>',
                                    the_title_attribute( array( 'before' => __('Permalink to', 'customizr'), 'echo' => false ) ),
                                    esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ),
                                    $text
                              ) : $text;

            return $text;

      }


      protected function czr_fn__get_quote_source() {

            $_content         = $this->czr_fn_get_content();

            if ( ! isset( $_content[ 'source' ] ) )
                  return false;

            return $_content[ 'source' ];

      }





      protected function czr_fn__get_post_meta( $post_id = null ) {

            $post_id  = $post_id ? $post_id : get_the_ID();
            $meta     = get_post_meta( $post_id, self::$meta_key, true );

            return empty( $meta ) ? false : $meta;

      }


      protected function czr_fn__get_parsed_content( $resource = null, $post_id = null  ) {

            $resource = $resource ? $resource : $this->czr_fn__get_post_meta( $post_id );
            $content  = array();

            //build content array
            foreach ( self::$meta_fields as $key => $meta_field ) {
                  if ( isset( $resource[ $meta_field ] ) && !empty( $resource[ $meta_field ] ) ) {
                        $content[ $key ] = $resource[ $meta_field ];
                  }
            }

            return empty( $content ) ? false : $content;

      }

}