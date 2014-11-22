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
          //Set thumbnails hooks and a new image size can be set here ( => template_redirect would be too late) (since 3.2.0)
          add_action( 'init'                           , array( $this , 'tc_set_thumb_options') );
          //Set thumbnail options : shape, size
          add_action( 'template_redirect'              , array( $this , 'tc_set_thumbnail_options' ) );
      }

      

      /**
      * Callback of template_redirect
      * Set customizer user options
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_thumbnail_options() {
        //Set top border style option
        add_filter( 'tc_user_options_style'   , array( $this , 'tc_write_thumbnail_inline_css') );
      }




      /*
      * Callback of tc_user_options_style hook
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_write_thumbnail_inline_css( $_css ) {
        $_list_thumb_height     = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_height' ) );
        $_list_thumb_height     = (! $_list_thumb_height || ! is_numeric($_list_thumb_height) ) ? 250 : $_list_thumb_height;

        $_single_thumb_height   = esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_height' ) );
        $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
        return sprintf("%s\n%s",
          $_css,
          ".tc-rectangular-thumb {
            max-height: {$_list_thumb_height}px;
            height :{$_list_thumb_height}px
          }\n
          .single .tc-rectangular-thumb {
            max-height: {$_single_thumb_height}px;
            height :{$_single_thumb_height}px
          }\n"
        );
      }



      /**
      * Gets the thumbnail or the first images attached to the post if any
      * @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_thumbnail_data( $requested_size = null ) {

        //output vars declaration
        $tc_thumb                       = '';
        $tc_thumb_height                = '';
        $tc_thumb_width                 = '';
        $image                          = array();
        $_class_attr                    = array();
        
        //define a filtrable boolean to set if attached images can be used as thumbnails
        //1) must be a non single post context
        //2) user option should be checked in customizer
        $_use_attachment_as_thumb = ! TC_post::$instance -> tc_single_post_display_controller() 
        && ( 0 != esc_attr( tc__f( '__get_option' , 'tc_post_list_use_attachment_as_thumb' ) ) );

        //define the default thumb size
        $tc_thumb_size                  = is_null($requested_size) ? apply_filters( 'tc_thumb_size_name' , 'tc-thumb' ) : $requested_size ;

        //define the default thumnail if has thumbnail
        if (has_post_thumbnail()) {
            $tc_thumb_id                = get_post_thumbnail_id();
            $_filtered_thumb_size       = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size );

            //check if tc-thumb size exists and has not been filtered
            $image                      = wp_get_attachment_image_src( $tc_thumb_id, $tc_thumb_size);

            //check also if this array value isset. (=> JetPack photon bug)
            if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size ) {
              $tc_thumb_size            = 'large';

              $_class_attr              = array( 
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
            if ( empty($image[1]) || empty($image[2]) ) {
              $_img_style             = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
            }

            //Add the style value
            $_class_attr['style']     = apply_filters( 'tc_post_thumb_inline_style' , $_img_style, $_width, $_height );

            $_class_attr              = apply_filters( 'tc_post_thumbnail_img_attributes' , $_class_attr ); 

            //check if the size exists
            if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size ) {
              $tc_thumb_size          = 'slider';
            }

            $tc_thumb                 = get_the_post_thumbnail( get_the_ID(), $tc_thumb_size , $_class_attr);

            //get height and width if not empty
            if ( ! empty($image[1]) && ! empty($image[2]) ) {
              $tc_thumb_height        = $image[2];
              $tc_thumb_width         = $image[1];
            }
        }

        //check if no thumbnail then uses the first attached image if any
        elseif ( false != $_use_attachment_as_thumb ) {
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
              $image                    = wp_get_attachment_image_src( $attachment->ID, $tc_thumb_size);
              $tc_thumb_size            = ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size) ? 'medium' : $tc_thumb_size;
              $_class_attr              = ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size) ? array( 'class' => "attachment-{$tc_thumb_size} no-tc-thumb-size wp-post-image" ) : $_class_attr ;
              //check if the size exists
              if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size ) {
                $tc_thumb_size            = 'slider';
              }
              $tc_thumb                 = wp_get_attachment_image( $attachment->ID, $tc_thumb_size, $_class_attr );

              //get height and width if not empty
              if ( ! empty($image[1]) && ! empty($image[2]) ) {
                $tc_thumb_height            = $image[2];
                $tc_thumb_width             = $image[1];
              }
            }
          }
        }

        //the current post id is included in the array of parameters for a better granularity.
        return apply_filters( 'tc_get_thumbnail_data' , array( $tc_thumb, $tc_thumb_width, $tc_thumb_height ), tc__f('__ID') );

      }//end of function
          



      /**
      * Displays the thumbnail or the first images attached to the post if any
      * Takes 2 parameters : thumbnail data array (img, width, height) and layout value
      * 
      * @package Customizr
      * @since Customizr 3.0.10
      */
      function tc_display_post_thumbnail( $thumb_data , $layout = 'span3' ) {
        $thumb_img                  = !isset( $thumb_data) ? false : $thumb_data[0];
        $thumb_img                  = apply_filters( 'tc_post_thumb_img', $thumb_img, tc__f('__ID') );
        if ( ! $thumb_img )
          return;

        //handles the case when the image dimensions are too small
        $thumb_size                 = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size, tc__f('__ID')  );
        $no_effect_class            = ( isset($thumb_data[0]) && isset($thumb_data[1]) && ( $thumb_data[1] < $thumb_size['width']) ) ? 'no-effect' : '';
        $no_effect_class            = ( ! isset($thumb_data[0]) || empty($thumb_data[1]) || empty($thumb_data[2]) ) ? '' : $no_effect_class;

        //default hover effect
        $thumb_wrapper              = sprintf('<div class="%5$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s" title="%3$s"></a>%4$s</div>',
                                      implode( " ", apply_filters( 'tc_thumbnail_link_class', array( $no_effect_class ) ) ),
                                      get_permalink( get_the_ID() ),
                                      esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                                      $thumb_img,
                                      implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('thumb-wrapper') ) )
        );

        $thumb_wrapper              = apply_filters_ref_array( 'tc_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, tc__f('__ID') ) );

        //renders the thumbnail
        $html = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
          apply_filters( 'tc_post_thumb_class', $layout ),
          $thumb_wrapper
        );

        echo apply_filters_ref_array( 'tc_display_post_thumbnail', array( $html, $thumb_data, $layout ) );

      }//end of function




      /**
      * Callback of template_redirect
      * @return void
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_thumb_options() {
        //Set thumb shape with customizer options (since 3.2.0)
        add_filter ( 'tc_post_thumb_wrapper'          , array( $this , 'tc_set_thumb_shape'), 10 , 2);
        //Set thumb size depending on the customizer thumbnail position options (since 3.2.0)
        add_filter ( 'tc_thumb_size_name'             , array( $this , 'tc_set_thumb_size') );
        //2) if shape is rectangular OR single post
        // => filter the thumbnail inline style tc_post_thumb_inline_style and replace width:auto by width:100%
        // 3 args = $style, $_width, $_height
        add_filter( 'tc_post_thumb_inline_style' , array( $this , 'tc_change_thumbnail_inline_css_width'), 10, 3 );
      }



      /**
      * Callback of filter tc_post_thumb_wrapper
      * ! 2 cases here : posts lists and single posts
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
         /* 
         ********** POST LIST OPTIONS **********
         */
        if ( TC_post_list::$instance -> tc_post_list_controller() ) {
          $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
          
          //1) check if shape is rounded, squared on rectangular
          if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
            return $thumb_wrapper;
          
          $_position = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) );
          return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2s">%3$s</a></div>',
                get_permalink( get_the_ID() ),
                esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                $thumb_img,
                ( 'top' == $_position || 'bottom' == $_position ) ? '' : implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('thumb-wrapper') ) )
          );
        }

        /* 
        ******** SINGLE POST OPTIONS **********
        */
        if ( TC_post::$instance -> tc_single_post_display_controller() ) {
          return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2s">%3$s</a></div>',
                get_permalink( get_the_ID() ),
                esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                $thumb_img,
                implode( " ", apply_filters( 'tc_thumb_wrapper_class', array() ) )
          );
        }
      }



      /**
      * Callback of tc_post_thumb_inline_style
      * Replace default widht:auto by width:100%
      * @param array of args passed by apply_filters_ref_array method
      * @return  string 
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_change_thumbnail_inline_css_width( $_style, $_width, $_height) {
        $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
        $_is_rectangular = ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') ? false : true;
        if ( ! is_single() && ! $_is_rectangular )
          return $_style;

        return sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width:100%;max-height: none;', $_width, $_height );
      }



      /**
      * Callback of filter tc_thumb_size_name
      * 
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_thumb_size( $_default_size ) {
        $_shape = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_shape') );
        if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
          return $_default_size;
        
        $_position                  = esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_position' ) );
        return ( 'top' == $_position || 'bottom' == $_position ) ? 'tc_rectangular_size' : $_default_size;
      }

  }//end of class
endif;