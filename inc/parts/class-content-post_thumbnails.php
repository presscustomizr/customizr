<?php
/**
* Posts thumbnails actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post_thumbnails' ) ) :
  class TC_post_thumbnails {
      static $instance;
      function __construct () {
        self::$instance =& $this;
      }

      /**********************
      * THUMBNAIL MODELS
      **********************/
      /**
      * Gets the thumbnail or the first images attached to the post if any
      * inside loop
      * @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_thumbnail_model( $requested_size = null ) {
        $tc_thumb_size    = is_null($requested_size) ? apply_filters( 'tc_thumb_size_name' , 'tc-thumb' ) : $requested_size ;
        $_model           = array();

        //1) Thumbnail
        //2) Attachement
        //3) Default Thumb
        //4) Filter
        if ( has_post_thumbnail() )
          $_model = $this -> tc_get_model_from_post_thumbnail( $tc_thumb_size );
        else
          $_model = $this -> tc_get_model_from_post_attached_images( $tc_thumb_size );
        if ( empty( $_model ) )
          $_model = $this -> tc_get_model_from_default_thumb( $tc_thumb_size );

        //model = array() || array( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" )
        //the current post id is included in the array of parameters for a better granularity.
        return apply_filters( 'tc_get_thumbnail_model' , ! is_array( $_model ) ? array() : $_model );
      }



      private function tc_get_model_from_default_thumb( $tc_thumb_size ) {
        $default_thumb_id = apply_filters( 'tc_default_thumb',  esc_attr( tc__f( '__get_option', 'tc_post_list_default_thumb' ) ) );

        if ( ! $default_thumb_id )
          return array();

        $image = wp_get_attachment_image_src( $default_thumb_id, $tc_thumb_size);

        if ( empty( $image[0] ) )
          return array();

        $_class_attr       = array( 'class' => "attachment-{$tc_thumb_size} tc-default-thumb");
        $tc_thumb          = wp_get_attachment_image( $default_thumb_id, $tc_thumb_size, false, $_class_attr );
        $tc_thumb_height   = '';
        $tc_thumb_width    = '';

        //get height and width if not empty
        if ( ! empty($image[1]) && ! empty($image[2]) ) {
            $tc_thumb_height        = $image[2];
            $tc_thumb_width         = $image[1];
        }
        return isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" ) : array();
      }



      private function tc_get_model_from_post_thumbnail( $tc_thumb_size ) {
        $_img_attr                  = array();
        $tc_thumb_height            = '';
        $tc_thumb_width             = '';
        $tc_thumb_id                = get_post_thumbnail_id();
        $_filtered_thumb_size       = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size );

        //check if tc-thumb size exists and has not been filtered
        $image                      = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);

        //check also if this array value isset. (=> JetPack photon bug)
        if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size ) {
          $tc_thumb_size            = 'large';
          $_img_attr              = array(
            'class' => "attachment-{$tc_thumb_size} no-tc-thumb-size wp-post-image" ,
          );
        }

        //IMAGE INLINE STYLE IF CORRECTIONS NEEDED
        //calculer automatiquement
        $_width                   = $_filtered_thumb_size['width'];
        $_height                  = $_filtered_thumb_size['height'];
        $_img_style               = '';

        //if we have a width and a height and at least on dimension is < to default thumb
        if ( ! empty($image[1])
          && ! empty($image[2])
          && ( $image[1] < $_width || $image[2] < $_height )
          ) {
            $_img_style           = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
        }
        if ( empty($image[1]) || empty($image[2]) )
          $_img_style             = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );

        //Add the style value
        $_img_attr['style']     = apply_filters( 'tc_post_thumb_inline_style' , $_img_style, $_width, $_height );

        $_img_attr              = apply_filters( 'tc_post_thumbnail_img_attributes' , $_img_attr );

        //check if the size exists
        if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
          $tc_thumb_size          = 'slider';

        $tc_thumb                 = get_the_post_thumbnail( get_the_ID(), $tc_thumb_size , $_img_attr);

        //get height and width if not empty
        if ( ! empty($image[1]) && ! empty($image[2]) ) {
          $tc_thumb_height        = $image[2];
          $tc_thumb_width         = $image[1];
        }

        return isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" ) : array();
      }




      function tc_get_model_from_post_attached_images( $tc_thumb_size ) {
        //define a filtrable boolean to set if attached images can be used as thumbnails
        //1) must be a non single post context
        //2) user option should be checked in customizer
        $_bool = TC_post::$instance -> tc_single_post_display_controller() || 0 != esc_attr( tc__f( '__get_option' , 'tc_post_list_use_attachment_as_thumb' ) );
        if ( ! apply_filters( 'tc_use_attachement_as_thumb' , $_bool ) )
          return array();

        $_img_attr                  = array();
        $tc_thumb_height            = '';
        $tc_thumb_width             = '';
        //Case if we display a post or a page
        if ( 'attachment' != tc__f('__post_type') ) {
          //look for attachements in post or page
          $tc_args = apply_filters('tc_attachment_as_thumb_query_args' , array(
              'numberposts'             =>  1,
              'post_type'               =>  'attachment' ,
              'post_status'             =>  null,
              'post_parent'             =>  get_the_ID(),
              'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' ),
              'orderby'                 => 'post_date',
              'order'                   => 'DESC'
            )
          );

          $attachments              = get_posts( $tc_args);
        }

        //case were we display an attachment (in search results for example)
        elseif ( 'attachment' == tc__f('__post_type') && wp_attachment_is_image() ) {
          $attachments = array( get_post() );
        }


        if ( isset($attachments) ) {
          foreach ( $attachments as $attachment) {
             //check if tc-thumb size exists for attachment and return large if not
            $image               = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
            $tc_thumb_size       = ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size) ? 'medium' : $tc_thumb_size;
            $_img_attr           = ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size) ? array( 'class' => "attachment-{$tc_thumb_size} no-tc-thumb-size wp-post-image" ) : $_img_attr ;
            //check if the size exists
            if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
              $tc_thumb_size     = 'slider';

            $tc_thumb            = wp_get_attachment_image( $attachment->ID, $tc_thumb_size, false, $_img_attr );

            //get height and width if not empty
            if ( ! empty($image[1]) && ! empty($image[2]) ) {
              $tc_thumb_height            = $image[2];
              $tc_thumb_width             = $image[1];
            }
          }
        }

        return isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" ) : array();

      }//end of fn



      /**********************
      * THUMBNAIL VIEW
      **********************/
      /**
      * Display or return the thumbnail view
      * @param : thumbnail model (img, width, height), layout value, echo bool
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_render_thumb_view( $_thumb_model , $layout = 'span3', $_echo = true ) {
        if ( empty( $_thumb_model ) )
          return;
        //extract "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
        extract( $_thumb_model );
        $thumb_img        = ! isset( $_thumb_model) ? false : $tc_thumb;
        $thumb_img        = apply_filters( 'tc_post_thumb_img', $thumb_img, tc__f('__ID') );
        if ( ! $thumb_img )
          return;

        //handles the case when the image dimensions are too small
        $thumb_size       = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size, tc__f('__ID')  );
        $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['width']) ) ? 'no-effect' : '';
        $no_effect_class  = ( ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

        //default hover effect
        $thumb_wrapper    = sprintf('<div class="%5$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s" title="%3$s"></a>%4$s</div>',
                                      implode( " ", apply_filters( 'tc_thumbnail_link_class', array( $no_effect_class ) ) ),
                                      get_permalink( get_the_ID() ),
                                      esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                                      $thumb_img,
                                      implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('thumb-wrapper') ) )
        );

        $thumb_wrapper    = apply_filters_ref_array( 'tc_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, tc__f('__ID') ) );

        //cache the thumbnail view
        $html             = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
          apply_filters( 'tc_post_thumb_class', $layout ),
          $thumb_wrapper
        );
        $html = apply_filters_ref_array( 'tc_render_thumb_view', array( $html, $_thumb_model, $layout ) );
        if ( ! $_echo )
          return $html;
        echo $html;
      }//end of function


      /**********************
      * EXPOSED HELPER
      **********************/
      public function tc_has_thumb() {
        $_thumb_model = TC_post_thumbnails::$instance -> tc_get_thumbnail_model();
        return (bool) false != $_thumb_model && ! empty( $_thumb_model );
      }

  }//end of class
endif;