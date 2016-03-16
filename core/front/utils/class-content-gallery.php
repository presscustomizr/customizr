<?php
/**
* Gallery content filters
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_gallery' ) ) :
  class TC_gallery {
      static $instance;
      function __construct () {
        self::$instance =& $this;

        add_filter ( 'tc_article_container_class' , array( $this, 'tc_add_gallery_class' ), 20 );
        //adds a filter for link markup (allow lightbox)
        add_filter ( 'wp_get_attachment_link'     , array( $this, 'tc_modify_attachment_link') , 20, 6 );
      }

      /**
       *
       * Add a class to the article-container to apply Customizr galleries on hover effects
       *
       * @package Customizr
       * @since Customizr 3.3.21
       *
       */
      function tc_add_gallery_class( $_classes ){
        if (  $this -> tc_is_gallery_enabled() && apply_filters( 'tc_gallery_style', esc_attr( TC_utils::$inst -> tc_opt( 'tc_gallery_style' ) ) ) )
          array_push($_classes, 'tc-gallery-style');
        return $_classes;
      }



      /**
       * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post gallery
       * Based on the original WP function
       * @package Customizr
       * @since Customizr 3.0.5
       *
       */
      function tc_modify_attachment_link( $markup, $id, $size, $permalink, $icon, $text ) {

        if ( ! $this -> tc_is_gallery_enabled() )
          return $markup;

        $tc_gallery_fancybox = apply_filters( 'tc_gallery_fancybox', esc_attr( TC_utils::$inst -> tc_opt( 'tc_gallery_fancybox' ) ) , $id );

        if ( $tc_gallery_fancybox == 1 && $permalink == false ) //add the filter only if link to the attachment file/image
          {
              $id = intval( $id );
              $_post = get_post( $id );

              if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
                return __( 'Missing Attachment' , 'customizr');

              if ( $permalink )
                $url = get_attachment_link( $_post->ID );

              $post_title = esc_attr( $_post->post_title );

              if ( $text )
                $link_text = $text;
              elseif ( $size && 'none' != $size )
                $link_text = wp_get_attachment_image( $id, $size, $icon );
              else
                $link_text = '';

              if ( trim( $link_text ) == '' )
                $link_text = $_post->post_title;
               $markup      = '<a class="grouped_elements" rel="tc-fancybox-group" href="'.$url.'" title="'.$post_title.'">'.$link_text.'</a>';
          }


        return $markup;
      }

      /*
       * HELPERS
       */
      function tc_is_gallery_enabled(){
        return apply_filters('tc_enable_gallery', esc_attr( TC_utils::$inst -> tc_opt('tc_enable_gallery') ) );
      }
  }//end of class
endif;
