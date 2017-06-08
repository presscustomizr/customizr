<?php
class CZR_post_list_alternate_model_class extends CZR_Model {

      //Default post list layout
      private static $default_post_list_layout   = array (

            'content'               => array(
                // 'width (full||semi-narrow||narrow) => ' array( xl, lg, md, sm, xs )
                'full'         => array( '', '', '8', '', '12' ),
                'semi-narrow'  => array( '', '8', '', '', '12' ),
                'narrow'       => array( '', '', '', '', '12' )
            ),
            'media'                 => array(
                // 'width (full||semi-narrow||narrow) => ' array( xl, lg, md, sm, xs )
                'full'         => array( '', '', '4', '', '12' ),
                'semi-narrow'  => array( '', '4', '', '', '12' ),
                'narrow'       => array( '', '', '', '', '12' )
            ),

            'show_thumb_first'      => true,
            'alternate'             => false,

      );

      private static $_col_bp = array(
            'xl', 'lg', 'md', 'sm', 'xs'
      );

      private $post_class     = array('col-12');

      protected $post_list_items = array();



      /**
      *
      * @override
      * fired before the model properties are parsed
      *
      * return model preset array()
      *
      * This will build the model preset - used for non singleton models
      * The args passed to the model on instatiation will be parsed into this in the model constructor
      *
      * Represents the singleton models "defaults".
      *
      * It differentiates from the CZR_Model.defaults as the latter can be (by default is) parsed into
      * passed args in rendering phase too ( for singleton models )
      *
      * Also the preset model is not retained
      */
      function czr_fn_get_preset_model() {

            $content_width         = czr_fn_get_in_content_width_class();

            $_preset = array(
                  'thumb_alternate'       => esc_attr( czr_fn_opt( 'tc_post_list_thumb_alternate' ) ),
                  'thumb_position'        => esc_attr( czr_fn_opt( 'tc_post_list_thumb_position' ) ),
                  'show_thumb'            => esc_attr( czr_fn_opt( 'tc_post_list_show_thumb' ) ),
                  'content_width'         => $content_width,
                  'format_icon_media'     => ! in_array( 'narrow', $content_width ),
                  'excerpt_length'        => esc_attr( czr_fn_opt( 'tc_post_list_excerpt_length' ) ),
                  'contained'             => false,
                  'cover_sections'        => true,
                  'wrapped'               => true,
                  'image_centering'       => 'js-centering',
                  'thumb_shape_effect'    => strstr(  esc_attr( czr_fn_opt( 'tc_post_list_thumb_shape' ) ),'rounded' ) ? czr_fn_opt( 'tc_post_list_thumb_shape' ) : 'regular'
            );

            return $_preset;
      }

      /**
      * @override
      * fired before the model properties are parsed becoming model properties
      *
      * At this stage the preset model has already been parsed into the $model array passed to the constructor
      * return model params array()
      */
      function czr_fn_extend_params( $model = array() ) {

            //merge with args
            $model                            = parent::czr_fn_extend_params( $model );

            //build properties depending on merged defaults and args

            $model[ 'has_narrow_layout' ]     = in_array( 'narrow', $model['content_width'] );

            $model[ 'post_list_layout' ]      = $this -> czr_fn__get_post_list_layout( $model );

            $model[ 'has_post_media']         = $model[ 'show_thumb' ];

            $this->post_class[]               = $model[ 'show_thumb' ] ? 'has-media' : 'no-media';

            return $model;
      }




      /**
      * add custom classes to the alternate container element
      */
      function czr_fn_get_element_class() {

            $_classes = is_array( $this->content_width ) ? $this->content_width : array();

            if ( ! empty( $this->contained ) )
                  $_classes[] = 'container';

            return $_classes;
      }




      /*
      * Fired just before the view is rendered
      * @hook: pre_rendering_view_{$this -> id}, 9999
      */
      /*
      * Each time this model view is rendered setup the current post list item
      * and add it to the post_list_items_array
      */
      function czr_fn_setup_late_properties() {

            //all post lists do this
            if ( czr_fn_is_loop_start() ) {

                  $this -> czr_fn_setup_text_hooks();
            }

            array_push( $this->post_list_items, $this->czr_fn__get_post_list_item() );
      }



      /*
      * Fired just after the view is rendered
      * @hook: post_rendering_view_{$this -> id}, 9999
      */
      function czr_fn_reset_late_properties() {

            if ( czr_fn_is_loop_end() ) {

                  //all post lists do this
                  $this -> czr_fn_reset_text_hooks();
                  //reset alternate items at loop end
                  $this -> czr_fn_reset_post_list_items();

            }

      }

      /*
      *  Public getters
      */
      function czr_fn_get_content_class() {
            return $this -> czr_fn__get_post_list_item_property( 'content_class' );
      }

      function czr_fn_get_media_class() {
            return $this -> czr_fn__get_post_list_item_property( 'media_class' );
      }

      function czr_fn_get_sections_wrapper_class() {
            return $this -> czr_fn__get_post_list_item_property( 'sections_wrapper_class' );
      }

      function czr_fn_get_grid_item_class() {
            return $this -> czr_fn__get_post_list_item_property( 'grid_item_class' );
      }

      function czr_fn_get_article_selectors() {
            return $this -> czr_fn__get_post_list_item_property( 'article_selectors' );
      }

      function czr_fn_get_media_link_class() {
            return $this -> czr_fn__get_post_list_item_property( 'media_link_class' );
      }

      function czr_fn_get_media_inner_class() {
            return $this -> czr_fn__get_post_list_item_property( 'media_inner_class' );
      }

      function czr_fn_get_has_post_media() {
            return $this -> czr_fn__get_post_list_item_property( 'has_post_media' );
      }

      function czr_fn_get_print_start_wrapper() {
            return $this -> wrapped && czr_fn_is_loop_start();
      }

      function czr_fn_get_print_end_wrapper() {
            return $this -> wrapped && czr_fn_is_loop_end();
      }





      /*
      * Private/protected getters
      */

      /*
      *  Method to compute the properties of the current (in a loop) post list item
      *  @return array
      */
      protected function czr_fn__get_post_list_item() {

            global $wp_query;

            /* Define variables */
            $media_inner_class = $thumb_shape = $thumb_effect   = null;
            $media_link_class  = 'bg-link';

            $_layout                       = apply_filters( 'czr_post_list_layout', $this -> post_list_layout );

            $_current_post_format          = get_post_format();

            $post_content                  = $this->czr_fn__get_post_content(
                  $post_id = null,
                  $post_format = $_current_post_format,
                  $type = 'all'
            );



            $is_full_image                 = $this->czr_fn_is_full_image( $_current_post_format, (bool)$post_content );

            $maybe_has_format_icon_media   = $this->czr_fn_maybe_has_format_icon_media( $_current_post_format );

            $force_format_icon_media       = $this->czr_fn_force_format_icon_media( $_current_post_format );

            //Thumb shape and effect stuff

            if ( !( $is_full_image || $force_format_icon_media ) && 'regular' != $this -> thumb_shape_effect ) {

                  $thumb_shape_effect = explode( '-', $this->thumb_shape_effect );

                  if ( count( $thumb_shape_effect ) > 1 )
                        $thumb_effect = $thumb_shape_effect[1];
                  if ( count( $thumb_shape_effect ) > 0 )
                        $thumb_shape = $thumb_shape_effect[0];

            }

            //define the image size -> only for old shape as of now
            if ( $thumb_shape ) {
                  //is rounded
                  $thumb_size = 'tc-thumb';

            }


            $is_media_bigger_than_content  = in_array( $_current_post_format , apply_filters( 'czr_alternate_big_media_post_formats',
                              $thumb_shape ? array('video') : array( 'video', 'image' ) ) )
                                    && ! $this->has_narrow_layout;

            $post_media                    = $this->show_thumb ? $this->czr_fn__get_post_media (

                    $post_id = null,
                    $post_format = $_current_post_format,
                    $type = 'all',
                    $use_img_placeholder = false,
                    $maybe_has_format_icon_media,
                    $force_format_icon_media,
                    isset($thumb_size) && !$is_media_bigger_than_content ? $thumb_size : 'full'

            ) : false;


            $has_post_media                = $post_media;

            $has_format_icon_media         = $maybe_has_format_icon_media && 'format-icon' == $post_media ;

            $cover_sections                = $this->cover_sections && !( $thumb_shape && !$has_format_icon_media );

            $_sections_wrapper_class       = array();
            $_grid_item_class              = array();


            /* End define variables */

            /* Process different cases */
            /*
            * $is_full_image: Gallery and images (with no text) should
            * - not be vertically centered
            * - avoid the media-content alternation
            */
            if ( ! $is_full_image && $has_post_media ) {

                  /*
                  * Video post formats
                  * In the new alternate layout video takes more space when global layout has less than 2 sidebars
                  * same thing for the image post format with text
                  *
                  */
                  if ( $is_media_bigger_than_content ) {
                        /* Swap the layouts */
                        $_t_l                    = $_layout[ 'media' ];
                        $_layout[ 'media' ]      = $_layout[ 'content' ];
                        $_layout[ 'content' ]    = $_t_l;
                  }


                  // conditions to swap thumb with content are:
                  // 1) show_thumb_first is false && alternate not on
                  // or
                  // 2) show_thumb_first is false && alternate on and current post number is odd (1,3,..). (First post is 0 )
                  // or
                  // 3) show_thumb_first is true && alternate on and current post number is even (2,4,..). (First post is 0 )
                  $swap = !$_layout['show_thumb_first'] && !$_layout[ 'alternate' ];
                  $swap = $swap || $_layout[ 'alternate' ] &&  0 == ( $wp_query -> current_post + (int)$_layout['show_thumb_first'] ) % 2 ;

                  $_sections_wrapper_class[] = $swap ? 'flex-row-reverse' : 'flex-row';

                  if ( ! $this->has_narrow_layout )
                        //allow centering sections
                        array_push( $_sections_wrapper_class, !$cover_sections ? 'czr-center-sections' : 'czr-cover-sections');
            }
            elseif ( $is_full_image && $has_post_media ) {

                  /*
                  * $is_full_image: Gallery and images (with no text) should
                  * - be displayed in full-width
                  * - media comes first, content will overlap
                  */
                  $_layout[ 'content' ] = $_layout[ 'media' ] = array();

            }

            elseif ( ! $has_post_media ) {

                  //full width content
                  $_layout[ 'content' ] = array('', '', '', '', '12');

            }

            $content_cols      = $this -> czr_fn_build_cols( $_layout['content'] );
            $media_class       = $media_cols  = $this -> czr_fn_build_cols( $_layout['media'] );


            //add the aspect ratio class for the full image types
            if  ( $is_full_image || 'video' == $_current_post_format ) {

                  $media_class[] = 'czr__r-w16by9';

            }

            elseif (  !$thumb_shape && !$has_format_icon_media && $has_post_media && !in_array( $_current_post_format, array( 'image', 'audio' ) ) ) {

                  $media_class[] = 'czr__r-w1by1';

            }

            elseif ( $thumb_shape && !$has_format_icon_media && !$is_media_bigger_than_content && 'audio' != $_current_post_format) {

                  $media_inner_class = 'czr__r-wTCT';
                  $media_link_class  = 'czr-link-mask';

            }

            if ( ! $is_full_image ) {

                        $_sections_wrapper_class[] = 'row';
                        $_grid_item_class[]        = 'col';

            }


            //setup article selectors;
            $article_selectors             = $this -> czr_fn__get_article_selectors(
                  $is_full_image,
                  $has_post_media,
                  $has_format_icon_media,
                  $thumb_shape,
                  $thumb_effect
            );


            //build the post item element
            $post_list_item = array(
                  'content_class'           => $content_cols,
                  'media_class'             => $media_class,
                  'media_inner_class'       => $media_inner_class,
                  'media_link_class'        => $media_link_class,
                  'sections_wrapper_class'  => $_sections_wrapper_class,
                  'grid_item_class'         => $_grid_item_class,
                  'article_selectors'       => $article_selectors,
                  'has_post_media'          => $has_post_media
            );

            return $post_list_item;
      }



      /*
      * Retrieve the article selectors for the current post
      */
      protected function czr_fn__get_article_selectors( $is_full_image, $has_post_media, $has_format_icon_media, $thumb_shape, $thumb_effect ) {

            $post_class              = $this->post_class;
            $is_full_image           = $is_full_image && $has_post_media;
            $has_thumb               = $has_post_media && !$has_format_icon_media;

            $thumb_shape             = $thumb_shape && $has_thumb && !$is_full_image ? "$thumb_shape czr-link-mask-p" : false;
            $thumb_effect            = $thumb_effect && $has_thumb && !$is_full_image ? $thumb_effect : false;

            /* Extend article selectors with info about the presence of an excerpt and/or thumb */

            $post_class              = array_merge( $post_class, array_filter( array(
                  $is_full_image                                              ? 'full-image' : '',
                  $has_thumb                                                  ? 'has-thumb' : 'no-thumb',
                  str_replace( 'rounded', 'round', $thumb_shape ),
                  $thumb_effect
            ) ) );

            $id_suffix               = is_main_query() ? '' : "_{$this -> id}";

            return czr_fn_get_the_post_list_article_selectors( array_filter($post_class), $id_suffix );

      }



      /**
      * Defines the post list layout:
      *
      * @return array() of layout data
      * @package Customizr
      * @since Customizr 3.2.0
      */
      protected function czr_fn__get_post_list_layout( $model ) {

            $_layout                       = self::$default_post_list_layout;

            $_layout[ 'position' ]         = $model[ 'thumb_position' ];

            $narrow_layout                 = $model[ 'has_narrow_layout' ];

            //since 4.5 top/bottom positions will not be optional but will be forced in narrow layouts
            if ( $narrow_layout ) {

                  $_layout['position']         = 'top';

            }
            else {

                  if ( 'top' == $_layout[ 'position' ] )
                        $_layout[ 'position' ] = 'left';

                  elseif ( 'bottom' == $_layout[ 'position' ] )
                        $_layout[ 'position' ] = 'right';

            }

            $_layout[ 'show_thumb_first' ] = in_array( $_layout['position'] , array( 'top', 'left') );
            //since 3.4.16 the alternate layout is not available when the position is top or bottom
            $_layout['alternate']          = ! ( 0 == $model[ 'thumb_alternate' ]  || in_array( $_layout['position'] , array( 'top', 'bottom') ) );

            $_content_width       = $model[ 'content_width' ];
            $_content_width       = is_array( $_content_width ) && in_array( $_content_width[0], array( 'full', 'semi-narrow', 'narrow' ) ) ?
                  $_content_width[0] : 'full';

            if ( in_array( $_layout['position'] , array( 'top', 'bottom') ) )
                  $_content_width     = 'narrow';


            $_layout['content'] = self::$default_post_list_layout['content'][$_content_width ];
            $_layout['media']   = self::$default_post_list_layout['media'][$_content_width ];

            return $_layout;

      }



      /* HELPERS AND CALLBACKS */


      /*
      * Helper
      * return the $property value if set into the post_list_item array
      */
      protected function czr_fn__get_post_list_item_property( $_property ) {

            if ( ! $_property )
                  return;

            $_properties = end( $this->post_list_items );

            return isset( $_properties[ $_property ] ) ? $_properties[ $_property ] : null;

      }



      /*
      * Return whether or not the current post should be displayed as full-width image with the content in overlay
      */
      protected function czr_fn_is_full_image( $_current_post_format, $_get_post_content ) {
            /*
            *
            * gallery and image (with no text) post formats
            *
            */
            $is_full_image           = in_array( $_current_post_format , array( 'gallery', 'image' ) ) && ( 'image' != $_current_post_format ||
                        ( 'image' == $_current_post_format && ! $_get_post_content ) );

            return $is_full_image;
      }


      /*
      * Show an icon in the media block when
      * 1) this model field is true
      *  and
      * 2a) post format is one of 'quote', 'link', 'status', 'aside', standard
      *  or
      * 2b) not 'gallery','image', 'audio', 'video' post format and no thumb
      */
      function czr_fn_maybe_has_format_icon_media( $current_post_format ) {

          if ( ! $this -> format_icon_media )
            return false;

          return in_array( $current_post_format, array( 'quote', 'link', 'status', 'aside', '' ) );

      }


      function czr_fn_force_format_icon_media( $current_post_format ) {

          if ( ! $this -> format_icon_media )
            return false;

          return in_array( $current_post_format, array( 'quote', 'link') );

      }


      /**
      * @return array() of bootstrap classes defining the responsive widths
      *
      */
      function czr_fn_build_cols( $_widths ) {

            $_col_bps = self::$_col_bp;

            $_widths = array_filter( $_widths );

            $_cols   = array();



            foreach ( $_widths as $i => $val ) {
                  $_col_bp_prefix = 'xs' == $_col_bps[$i] ? '-' : "-{$_col_bps[$i]}-";

                  $_width_class  = "col{$_col_bp_prefix}$val";
                  $_cols[]       = $_width_class;
            }

            return array_filter( array_unique( $_cols ) );

      }


      /**
      * @package Customizr
      * @since Customizr 4.0
      */
      function czr_fn_setup_text_hooks() {
            //filter the excerpt length
            add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
      }


      /**
      * @package Customizr
      * @since Customizr 4.0
      */
      function czr_fn_reset_text_hooks() {
            remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
      }


      /**
      * hook : excerpt_length hook
      * @return string
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_excerpt_length( $length ) {
            $_custom = $this -> excerpt_length;
            return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
      }


      /**
      * @package Customizr
      * @since Customizr 4.0
      */
      function czr_fn_reset_post_list_items() {

          $this -> post_list_items = array();

      }



      protected function czr_fn__get_post_media( $post_id = null, $post_format = null, $type = 'all', $use_img_placeholder = false, $has_format_icon_media = false, $force_icon = false, $thumb_size = 'full' ) {

            $_id       = czr_fn_maybe_register( array(

                    'id'          => 'media', //this must be the same of the first param used in the render_template
                    'model_class' => 'content/common/media',

            )  );

            $_instance = czr_fn_get_model_instance( $_id );

            if ( !$_instance )
                  return false;

            //setup the media
            $_instance -> czr_fn_setup( array(

                  'post_id'               => $post_id,
                  'post_format'           => $post_format,
                  'media_type'            => $type,
                  'use_thumb_placeholder' => $use_img_placeholder,
                  'use_icon'              => $has_format_icon_media,
                  'force_icon'            => $force_icon,
                  'thumb_size'            => $thumb_size,
                  'image_centering'       => $this->image_centering

            ) );


            return $_instance -> czr_fn_get_raw_media();

      }






      protected function czr_fn__get_post_content( $post_id = null, $post_format = null, $type = 'excerpt' ) {

            $_id       = czr_fn_maybe_register( array(

                    'id'          => 'post_list_item_content_inner', //this must be the same of the first param used in the render_template
                    'model_class' => 'content/post-lists/item-parts/contents/post_list_item_content_inner',

            )  );

            $_instance = czr_fn_get_model_instance( $_id );

            if ( !$_instance )
                  return false;

            //setup the media
            $_instance -> czr_fn_setup( array(

                  'post_id'               => $post_id,
                  'post_format'           => $post_format,
                  'content_type'          => $type

            ) );


            return $_instance -> czr_fn_get_raw_content();

      }

}//end of class