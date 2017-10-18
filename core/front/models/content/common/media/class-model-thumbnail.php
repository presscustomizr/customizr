<?php
class CZR_thumbnail_model_class extends CZR_Model {

    protected  $post_id;

    protected  $media;

    protected  $image;
    protected  $lightbox_url;


    protected  $size;
    protected  $use_placeholder;

    protected  $use_attachment;



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

            'image'                 => null,
            'lighbox_url'           => null,

            'post_id'               => null,
            'size'                  => 'full',
            'use_placeholder'       => false,
            'use_attachment'        => true,

            'visibility'            => true,

            'has_lightbox'          => czr_fn_opt( 'tc_fancybox' ),

        );

        parent::__construct( $model );

    }



    public function czr_fn_setup( $args = array() ) {

        $defaults = array (

            'post_id'         => null,
            'size'            => 'full',
            'use_placeholder' => false,
            'use_attachment'  => true,

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
        $thumbnail_item = $this->czr_fn__get_the_thumbnail_item();

        $this -> czr_fn_set_property( 'image', $thumbnail_item[ 'image' ] );
        $this -> czr_fn_set_property( 'lightbox_url', $thumbnail_item[ 'lightbox_url' ] );

    }




    protected function czr_fn__get_the_thumbnail_item() {

        $raw_media       = $this -> media;

        if ( empty( $raw_media ) )
           return array();


        $thumbnail_item  = array(

              'image'           => $raw_media[ 'tc_thumb' ],
              //lightbox
              'lightbox_url'    => $this->has_lightbox && !( array_key_exists( 'is_placeholder', $raw_media ) && $raw_media[ 'is_placeholder' ] ) ? wp_get_attachment_url( $raw_media[ '_thumb_id' ] ) /*full*/ : '',

        );

        return $thumbnail_item;

    }




    protected function czr_fn__get_post_thumbnail() {

        $post_id = $this->post_id ? $this->post_id : get_the_ID();

        //Get the Customizr thumbnail or the WordPress post thumbnail
        if ( $this->use_attachment ) {

            //model array
            $post_thumbnail = czr_fn_get_thumbnail_model( array(
                'requested_size' => $this->size,
                'post_id'        => $post_id,
                'placeholder'    => $this->use_placeholder
            ));

        }

        else {

            //build array
            $id                                  = get_post_thumbnail_id( $post_id );
            $post_thumbnail                      = array();
            if ( $id ) {
                $post_thumbnail[ '_thumb_id' ]       = $id;
                $post_thumbnail[ 'tc_thumb' ]        = apply_filters( 'czr_thumb_html',
                    get_the_post_thumbnail( $post_id, $this->size ),
                    $requested_size = $this->size,
                    $post_id = $id,
                    $custom_thumb_id = null,
                    $_img_attr = null,
                    $tc_thumb_size = $this->size
                );
            }

        }


        return empty( $post_thumbnail ) ? false : $post_thumbnail;

    }

}