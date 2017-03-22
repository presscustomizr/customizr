<?php
class CZR_link_model_class extends CZR_Model {

      private static $meta_key      = 'czr_link_meta';

      private static $meta_fields   = array(
                                          'title'       => 'link_title',
                                          'url'         => 'link_url'
                                    );

      public $defaults              = array(
                                          'content'     => null,
                                          'link_url'    => '',
                                          'link_title'  => '',
                                    );

      private $_link;




      /* Public api */
      public function czr_fn_get_content( $post_id = null ) {

            if ( !isset( $this->content ) || is_null( $this->content ) )
                  return $this->czr_fn__get_parsed_content( $resource = null, $post_id );

            return $this->content;

      }




      public function czr_fn_reset() {
            unset($this->content);
            unset($this->_link);
      }




      public function czr_fn_get_link_title() {
            if ( ! isset( $this->_link ) )
                  $this->_link = $this->czr_fn__get_the_link();

            return esc_html( $this->_link[ 'link_title' ] );

      }




      public function czr_fn_get_link_url() {
            if ( ! isset( $this->_link ) )
                  $this->_link = $this->czr_fn__get_the_link();

            return esc_url( $this->_link[ 'link_url' ] );

      }




      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current link
      */
      //defined in the model base class
      protected function czr_fn_setup_late_properties() {


            $this->czr_fn__set_the_link();

      }




      protected function czr_fn__set_the_link() {
            //defined in the model base class
            $this->_link = $this->czr_fn__get_the_link();
      }




      protected function czr_fn__get_the_link() {


            $_url        = $this->czr_fn__get_link_url();

            if ( ! $_url )
                  return $this -> defaults;

            $_title      = $this->czr_fn__get_link_title();

            return array(
                  'link_url'       => $_url,
                  'link_title'     => $_title,
            );

      }




      protected function czr_fn__get_link_url() {

            $_content         = $this->czr_fn_get_content();

            if ( ! isset( $_content[ 'url' ] ) )
                  return false;


            return $_content[ 'url' ];

            $url             = !get_the_title() ? sprintf( '<a title="%1$s" href="%2$s">%3$s</a>',
                                    the_title_attribute( array( 'before' => __('Permalink to', 'customizr'), 'echo' => false ) ),
                                    esc_url( apply_filters( 'the_permalink', get_the_permalink() ) ),
                                    $url
                              ) : $url;

            return $url;

      }




      protected function czr_fn__get_link_title() {

            $_content         = $this->czr_fn_get_content();

            if ( ! isset( $_content[ 'url' ] ) )
                  return false;

            return $_content[ 'title' ];

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