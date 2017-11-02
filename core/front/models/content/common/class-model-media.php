<?php
class CZR_media_model_class extends CZR_Model {

      protected $media                 ;

      protected $media_template        ;
      protected $media_args            ;

      protected $post_id               ;
      protected $post_format           ;
      protected $media_type            ;
      protected $use_thumb_placeholder ;
      protected $use_icon              ;
      protected $force_icon            ;
      protected $thumb_size            ;

      protected $has_permalink         ;
      protected $link_class            ;
      protected $inner_wrapper_class   ;
      protected $has_lightbox          ;

      protected $image_centering       ;



      /* Public api */


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

                  'media_type'            => 'all',

                  'use_icon'              => false,
                  'force_icon'            => false,

                  'thumb_size'            => 'full',
                  'use_thumb_placeholder' => false,

                  'link_class'            => 'bg-link',

                  'inner_wrapper_class'   => null,
                  'image_centering'       => false,

            );


            $args = wp_parse_args( $args, $defaults );

            $args[ 'post_id' ]     = $args[ 'post_id' ] ? $args[ 'post_id' ] : get_the_ID();
            $args[ 'post_format' ] = !is_null( $args[ 'post_format' ] ) ? $args[ 'post_format' ] : get_post_format( $args['post_id'] );


            /* This will update the model object properties, merging the $model -> defaults too */
            $this -> czr_fn_update( $args );

            /* Set the media properties */
            $this -> czr_fn__setup_media();

            /* Toggle visibility */
            $this -> czr_fn_set_property( 'visibility',  (bool) $this->czr_fn_get_raw_media() );

      }



      /* Public api */
      public function czr_fn_get_raw_media() {

            return $this->media;

      }

      /* END PUBLIC API */

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

                  'media'                 => null,
                  'media_template'        => '',
                  'media_args'            => array(),

                  'post_id'               => null,
                  'post_format'           => null,
                  'media_type'            => 'all',
                  'use_thumb_placeholder' => false,
                  'use_icon'              => false,
                  'force_icon'            => false,
                  'thumb_size'            => 'full',
                  'inner_wrapper_class'   => '',

                  'has_permalink'         => true,
                  'link_class'            => 'bg-link',
                  'has_lightbox'          => czr_fn_opt( 'tc_fancybox' ),

                  'visibility'            => true,

                  'image_centering'       => esc_attr( czr_fn_opt( 'tc_center_img' ) ) ? 'js-centering' : 'css-centering',
                  'inner_wrapper_class'   => null,
            );

            parent::__construct( $model );

      }



      /* @override */
      public function czr_fn_setup_late_properties() {


            /*
            * This is if the model is called without setting up the media (czr_fn_setup)
            */
            if ( is_null( $this->media ) ) {


                  $this->czr_fn_setup( array(

                        'post_id'               => $this->post_id,

                        'post_format'           => $this->post_format,

                        'media_type'            => $this->media_type,

                        'use_icon'              => $this->use_icon,
                        'force_icon'            => $this->force_icon,

                        'inner_wrapper_class'         => $this->inner_wrapper_class,

                        'thumb_size'            => $this->thumb_size,
                        'use_thumb_placeholder' => $this->use_thumb_placeholder,

                        'has_permalink'         => $this->has_permalink,
                        'has_lightbox'          => $this->has_lightbox,

                        'image_centering'       => $this->image_centering,

                  ) );


            }

            $this->czr_fn__setup_media_wrapper();
      }


      /*
      * $type can be:
      * a) all (video,audio,czr_thumb,wp_thumb)
      * b) czr_thumb
      * c) wp_thumb
      */
      public function czr_fn__setup_media() {


            if ( $this->use_icon && $this->force_icon ) {

                  $this->czr_fn__setup_media_to_render( 'format-icon' );
                  return;

            }

            $post_id                 = $this->post_id;
            $media_type              = 'all' != $this->media_type ? $this->media_type : $this->post_format;

            if ( 'gallery' == $media_type && ! apply_filters( 'czr_allow_gallery_carousel_in_post_lists', false ) ) {
                  $media_type        = ''; //<-fall back on the standard post format
            }

            switch ( $media_type ) {

                  case 'video' :

                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'media_video',
                              'render'      => false,
                              'model_class' => 'content/common/media/video'

                        ) );

                        if ( ! $_instance )
                              return;

                        $_instance->czr_fn_setup ( array(

                                    'post_id'          => $post_id,

                        ));

                        $this->czr_fn__setup_media_to_render( $media = $_instance->czr_fn_get_raw_media(), $media_template = 'content/common/media/video', $model_id = $_instance->czr_fn_get_property( 'id' ) );

                  break;

                  case 'audio' :

                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'media_audio',
                              'render'      => false,
                              'model_class' => 'content/common/media/audio'

                        ) );

                        if ( ! $_instance )
                              return;


                        $_instance->czr_fn_setup ( array(

                                    'post_id'          => $post_id,

                        ));

                        $this->czr_fn__setup_media_to_render( $media = $_instance->czr_fn_get_raw_media(), $media_template = 'content/common/media/audio', $model_id = $_instance->czr_fn_get_property( 'id' ) );

                  break;

                  //24/07/2017 gallery post format is buggy removed for now
                  //19/10/2017 gallery carousel introduced in pro
                  case 'gallery' :


                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'media_gallery',
                              'render'      => false,
                              'model_class' => 'content/common/media/gallery'

                        ) );

                        if ( ! $_instance )
                              return;

                        $_instance->czr_fn_setup ( array(
                              'post_id'          => $post_id,
                              'has_lightbox'     => $this->has_lightbox,
                              'size'             => $this->thumb_size ? $this->thumb_size : 'full'

                        ));

                        $this->czr_fn__setup_media_to_render( $media = $_instance->czr_fn_get_raw_media(), $media_template = 'content/common/media/gallery', $model_id = $_instance->czr_fn_get_property( 'id' ) );

                  break;

                  default:

                        $_instance = $this -> czr_fn__get_instance_from_model_array( array(

                              'id'          => 'media_thumbnail',
                              'render'      => false,
                              'model_class' => 'content/common/media/thumbnail'

                        ) );

                        $has_thumbnail          = false;

                        if ( $_instance ) {

                              $_instance->czr_fn_setup ( array(

                                          'post_id'           => $post_id,
                                          'size'              => $this->thumb_size ? $this->thumb_size : 'full',
                                          'use_placeholder'   => $this->use_thumb_placeholder,
                                          'use_attachment'    => 'wp_thumb' != $this->media_type,
                                          'has_lightbox'      => $this->has_lightbox

                              ));

                              $has_thumbnail    = $this->czr_fn__setup_media_to_render( $_instance->czr_fn_get_raw_media(), $media_template = 'content/common/media/thumbnail', $model_id = $_instance->czr_fn_get_property( 'id' ) );
                        }

                        if ( ! $has_thumbnail && $this->use_icon ) {

                              $this->czr_fn__setup_media_to_render( 'format-icon' );
                        }

                  break;

            }//end switch

            // media has been retrieved, if it's still null let's set it to false
            // as is null is the condition to retrieve it just before rendering
            if ( is_null( $this->media ) )
                  $this->czr_fn_set_property( 'media',  false );
      }




      protected function czr_fn__setup_media_to_render( $media, $media_template = null , $model_id = null ) {

            if ( $media ) {

                  $this->czr_fn_set_property( 'media',  $media );


                  $this->czr_fn_set_property( 'media_template',  $media_template );


                  $this->czr_fn_set_property( 'media_args',  array( 'model_id' => $model_id, 'reset_to_defaults' => false ) );

            }

            return (bool) $media;

      }




      //The centering css class will be used to target the container in which we will use the jQuery center image plugin
      //'image_centering'       => esc_attr( czr_fn_opt( 'tc_center_img' ) ) ? 'js-centering' : 'css-centering',
      protected function czr_fn__setup_media_wrapper() {

            if ( in_array( $this -> media_template, array( 'content/common/media/thumbnail', 'content/common/media/gallery' ) ) ) {
                  /* Add centering class */
                  switch ( $this -> image_centering ) {
                        case 'css-centering' :
                              $centering_class = 'css-centering';
                        break;

                        case 'no-js-centering' :
                              $centering_class = 'no-centering';
                        break;

                        // js-centering case is the default
                        // for gallery carousel, we don't want to js center all hidden cell items => too expensive
                        // => that's why we flag with this class 'granular-js-centering', which will be excluded from the default js treatment on front
                        // var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');
                        // @see jquery_plugins.part.js
                        default :
                              $centering_class = false === strpos( $this -> media_template, 'gallery') ? 'js-centering' : 'granular-js-centering';
                        break;
                  }

                  //update inner_wrapper_class
                  $inner_wrapper_class = ! empty($this-> inner_wrapper_class) ? $this ->inner_wrapper_class : array();
                  $inner_wrapper_class = ! is_array( $inner_wrapper_class ) ? explode(' ', $inner_wrapper_class ) : $inner_wrapper_class;

                  $inner_wrapper_class[] = $centering_class;

                  $this->czr_fn_set_property( 'inner_wrapper_class', $inner_wrapper_class );

            } else {

                  /* Set availability of background link */
                  $this->czr_fn_set_property( 'has_permalink', false );

            }


      }





      /* Helper */
      protected function czr_fn__get_instance_from_model_array( $model = array() ) {

            $_id = czr_fn_maybe_register( $model );

            return czr_fn_get_model_instance( $_id );

      }


}