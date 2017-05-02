<?php
class CZR_post_list_item_content_inner_model_class extends CZR_Model {

      protected $content                 ;
      protected $content_template        ;
      protected $content_args            ;


      protected $post_id                 ;
      protected $post_format             ;
      protected $content_type            ;



      /* Public api */
      public function czr_fn_get_element_class() {

            return 'full' === $this->content_type ? array( 'entry-content' ) : array( 'entry-summary' );

      }


      /*
      *  Here we setup this object with the passed args if any
      *  we also setup the item to render
      *
      *  This method must be called before trying to get this object properties
      *  it's a kind of constructor.
      *
      *  Can be called on demand OR automatically just before rendering the view
      *  depending on whether we need to know some properties of this object
      *  way lot before the rendering ( see alternate post list which needs to knwo if a post has content to show )
      *  or not ( the plain post list, e.g. doesn't need that )
      *
      */
      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'               => null,

                  'post_format'           => null,

                  'content_type'          => 'full',

            );


            $args = wp_parse_args( $args, $defaults );

            $args[ 'post_id' ]     = $args[ 'post_id' ] ? $args[ 'post_id' ] : get_the_ID();
            $args[ 'post_format' ] = !is_null( $args[ 'post_format' ] ) ? $args[ 'post_format' ] : get_post_format( $args['post_id'] );


            /* This will update the model object properties, merging the $model -> defaults too */
            $this -> czr_fn_update( $args );


            /* Set the content properties */
            $this -> czr_fn__setup_content();

            /* Toggle visibility */
            $this -> czr_fn_set_property( 'visibility',  (bool) $this->czr_fn_get_raw_content() );


      }




      /* Public api */
      public function czr_fn_get_raw_content() {

            return $this->content;

      }



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

                  'post_id'               => $this->post_id,

                  'post_format'           => $this->post_format,

                  'content_type'          => $this->content_type,

                  'content'               => null,
                  'content_template'      => '',
                  'content_args'          => array(),

                  'visibility'            => true

            );

            return parent::__construct( $model );

      }




      /* @override */
      public function czr_fn_setup_late_properties() {

            /*
            * This is if the model is called without setting up the media
            *
            */
            if ( is_null( $this->content ) ) {

                  $this->czr_fn_setup( array(

                        'post_id'               => $this->post_id,

                        'post_format'           => $this->post_format,

                        'content_type'          => $this->content_type,

                  ) );

            }

      }




      public function czr_fn__setup_content() {


            $post_id       = $this->post_id;
            $content_type  = 'all' != $this->content_type ? $this->content_type : $this->post_format;

            switch ( $content_type ) {

                  case 'quote'   :

                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'quote',
                              'render'      => false,
                              'model_class' => 'content/common/text/quote'

                        ) );


                        $_instance->czr_fn_setup();

                        $this->czr_fn__setup_content_to_render( $content = $_instance->czr_fn_get_raw_content(), $content_template = 'content/common/text/quote', $model_id = $_instance->czr_fn_get_property( 'id' ) );

                  break;

                  case 'link'   :

                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'link',
                              'render'      => false,
                              'model_class' => 'content/common/text/link'

                        ) );


                        $_instance->czr_fn_setup();

                        $this->czr_fn__setup_content_to_render( $content = $_instance->czr_fn_get_raw_content(), $content_template = 'content/common/text/link', $model_id = $_instance->czr_fn_get_property( 'id' ) );

                  break;

                  case 'full'   :
                        $more = __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' );
                        $this->czr_fn__setup_content_to_render( $content = $this -> czr_fn_add_support_for_shortcode_special_chars( get_the_content( $more ) ) );

                  break;

                  default:
                        $this->czr_fn__setup_content_to_render( apply_filters( 'the_excerpt', get_the_excerpt( $post_id ) ) );

            }//end switch

            //always fallback on the excerpt?
            if ( is_null( $this->content ) )
                  $this->czr_fn__setup_content_to_render( apply_filters( 'the_excerpt', get_the_excerpt() ) );


      }





      protected function czr_fn__setup_content_to_render( $content, $content_template = null , $model_id = null ) {

            if ( $content ) {

                  $this->czr_fn_set_property( 'content',  $content );


                  $this->czr_fn_set_property( 'content_template',  $content_template );


                  $this->czr_fn_set_property( 'content_args',  array( 'model_id' => $model_id, 'reset_to_defaults' => false ) );

            }

            return (bool) $content;

      }




      /* Helper */
      protected function czr_fn__get_instance_from_model_array( $model = array() ) {

            $_id = czr_fn_maybe_register( $model );

            return czr_fn_get_model_instance( $_id );

      }




      /* Helper */

      /**
      *
      * @param string
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      protected function czr_fn_add_support_for_shortcode_special_chars( $_content ) {
            return str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $_content ) );
      }

}