<?php
class CZR_post_list_item_media_model_class extends CZR_Model {

      protected $media                 ;
      protected $media_template        ;
      protected $media_args            ;

      protected $has_bg_link           ;
      protected $has_format_icon       ;

      protected $post_id               ;
      protected $post_format           ;
      protected $media_type            ;
      protected $use_thumb_placeholder ;
      protected $use_icon              ;
      protected $force_icon            ;
      protected $tumb_size             ;

      protected $image_centering       ;



      /* Public api */
      public function czr_fn_setup( $args = array() ) {

            $defaults = array (

                  'post_id'               => get_the_ID(),

                  'post_format'           => null,

                  'media_type'            => 'all',

                  'use_icon'              => false,
                  'force_icon'            => false,

                  'thumb_size'            => 'full',
                  'use_thumb_placeholder' => false,

            );


            $args = wp_parse_args( $args, $defaults );

            $args[ 'post_id' ]     = $args[ 'post_id' ] ? $args[ 'post_id' ] : get_the_ID();
            $args[ 'post_format' ] = !is_null( $args[ 'post_format' ] ) ? $args[ 'post_format' ] : get_post_format( $args['post_id'] );


            /* This will update the model object properties, merging the $model -> defaults too */
            $this -> czr_fn_update( $args );



            /* Set the media properties */
            $this -> czr_fn__setup_media();

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

            $this->defaults = array(

                  'media'                 => null,
                  'media_template'        => '',
                  'media_args'            => array(),

                  'has_bg_link'           => null,
                  'has_format_icon'       => false,

                  'post_id'               => null,
                  'post_format'           => null,
                  'media_type'            => 'all',
                  'use_thumb_placeholder' => false,
                  'use_icon'              => false,
                  'force_icon'            => false,
                  'thumb_size'            => 'full',

                  'image_centering'    => esc_attr( czr_fn_get_opt( 'tc_center_img' ) ) ? 'js-centering' : 'css-centering'
            );

            return parent::__construct( $model );
      }



      /* @override */
      public function czr_fn_setup_late_properties() {
            /*
            * This is if the model is called without setting up the media
            * but thanks to the merged defaults we have at least the defaults properties
            *
            */
            if ( is_null( $this->media ) )
                  $this->czr_fn_setup( array(
                        'post_id'               => $this->post_id,

                        'post_format'           => $this->post_format,

                        'media_type'            => $this->media_type,

                        'use_icon'              => $this->use_icon,
                        'force_icon'            => $this->force_icon,

                        'thumb_size'            => $this->thumb_size,
                        'use_thumb_placeholder' => $this->use_thumb_placeholder,

                  ) );

            $this->czr_fn__setup_media_wrapper();
      }


      /*
      *     $type can be:
      * a) all (video/audio/czr_thumb/thumbnail)
      * b) czr_thumb
      * c) wp_thumb
      *
      *
      */
      public function czr_fn__setup_media() {



            if ( $this->use_icon && $this->force_icon ) {

                  $this -> czr_fn__set_icon_media();
                  return;

            }

            $post_id     = $this->post_id;
            $media_type  = 'all' != $this->media_type ? 'thumb' : $this->post_format;

            switch ( $media_type ) {

                  case 'video' :
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_video' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_video' );

                        }

                        else {
                              $_id = czr_fn_register( array(

                                    'id'          => 'media_video',
                                    'render'      => false,
                                    'model_class' => 'content/common/media/video'

                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup ( array(

                                    'post_id'          => $post_id,

                        ));

                        $this -> media                 =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/common/media/video';

                              $this -> media_args      =  array( 'model_id' => 'media_video', 'model_class' => 'content/common/media/video', 'reset_to_defaults' => false );
                        }

                  break;

                  case 'audio' :
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_audio' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_audio' );

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_audio',
                                    'render'      => false,
                                    'model_class' => 'content/common/media/audio'

                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup ( array(

                                    'post_id'          => $post_id,

                        ));

                        $this -> media                 =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/common/media/audio';

                              $this -> media_args      =  array( 'model_id' => 'media_audio', 'model_class' => 'content/common/media/audio', 'reset_to_defaults' => false );
                        }

                  break;

                  case 'gallery' :

                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_gallery' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_gallery' );

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_gallery',
                                    'render'      => false,
                                    'model_class' => 'content/common/media/gallery'
                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup ( array(

                                    'post_id'          => $post_id,

                        ));

                        $this -> media                 =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template  =  'content/common/media/gallery';

                              $this -> media_args      =  array( 'model_id' => 'media_gallery', 'model_class' => 'content/common/media/gallery', 'reset_to_defaults' => false );

                              $this -> has_bg_link     =  true;
                        }

                  break;

                  default:
                        //medias are singletons, we just reset them before using them in loops
                        if ( czr_fn_is_registered( 'media_thumbnail' ) ) {

                              $_instance = czr_fn_get_model_instance( 'media_thumbnail' );

                        }
                        else {

                              $_id = czr_fn_register( array(

                                    'id'          => 'media_thumbnail',
                                    'render'      => false,
                                    'model_class' => 'content/common/media/thumbnail'

                              ) );

                              $_instance = czr_fn_get_model_instance( $_id );
                        }

                        $_instance->czr_fn_setup ( array(

                                    'post_id'           => $post_id,
                                    'size'              => 'normal',
                                    'use_placeholder'   => $this->use_thumb_placeholder,
                                    'use_attachment'    => 'wp_thumb' != $this->media_type

                        ));

                        $this -> media                  =  $_instance->czr_fn_get_raw_media();

                        if ( $this -> media ) {

                              $this -> media_template   =  'content/common/media/thumbnail';

                              $this -> media_args       =  array( 'model_id' => 'media_thumbnail', 'model_class' => 'content/common/media/thumbnail', 'reset_to_defaults' => false );

                              $this -> has_bg_link      =  true;

                        }

                        elseif ( $this->use_icon ) {

                              $this -> czr_fn__set_icon_media();

                        }


                  break;

            }

      }


      protected function czr_fn__set_icon_media() {

            $this -> media            = 'format-icon';

            $this -> has_format_icon  = true;
      }


      protected function czr_fn__setup_media_wrapper() {

            if ( in_array( $this -> media_template, array( 'content/common/media/thumbnail', 'content/common/media/gallery' ) ) ) {

                  switch ( $this -> image_centering ) {

                        case 'css-centering' :

                              $centering_class = 'css-centering';
                        break;

                        case 'no-js-centering' :
                              $centering_class = 'no-centering';

                        break;

                        default :
                              $centering_class = 'js-centering';
                        break;
                  }

                  //update element_class
                  $element_class = ! empty($this-> element_class) ? $this ->element_class : array();
                  $element_class = ! is_array( $element_class ) ? explode(' ', $element_class ) : $element_class;

                  $element_class[] = $centering_class;

                  $this->czr_fn_set_property( 'element_class', $element_class );
            }


      }

}