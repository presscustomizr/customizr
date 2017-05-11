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
if ( ! class_exists( 'CZR_gallery' ) ) :
class CZR_gallery {

      static $instance;
      function __construct () {

            self::$instance =& $this;

            //adds a filter for link markup (allow lightbox)
            //add_filter ( 'wp_get_attachment_link'     , array( $this, 'czr_fn_modify_attachment_link') , 20, 6 );

            /*
            * Filters the default gallery shortcode output.
            * see wp-includes/media.php
            */
            add_filter( 'post_gallery', array( $this, 'czr_fn_czr_gallery' ), 10, 3 );

      }




      /**
      * Builds the Gallery shortcode output.
      * see the gallery_shortcode in wp-includes/media.php
      * @return string HTML content to display gallery.
      */
      function czr_fn_czr_gallery( $gallery, $attr, $instance ) {

            $post = get_post();

            //do nothing if the customizr gallery is not enabled
            if ( ! $this -> czr_fn_is_gallery_enabled() )
                  return $gallery;

            $atts =     shortcode_atts( array(
                             'order'      => 'ASC',
                             'orderby'    => 'menu_order ID',
                             'id'         => $post ? $post->ID : 0,
                             'columns'    => 3,
                             'size'       => 'thumbnail',
                             'include'    => '',
                             'exclude'    => '',
                             'link'       => ''
                        ), $attr, 'gallery' );


            $id         = intval( $atts['id'] );
            $itemtag    = 'figure';
            $captiontag = 'figcaption';
            $icontag    = 'div';

            //test
            $html5      = true;

            if ( ! empty( $atts['include'] ) ) {
                  $_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
                  $attachments = array();
                  foreach ( $_attachments as $key => $val ) {
                        $attachments[$val->ID] = $_attachments[$key];
                  }
            } elseif ( ! empty( $atts['exclude'] ) ) {
                  $attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
            } else {
                  $attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
            }

            if ( empty( $attachments ) ) {
                  return '';
            }

            if ( is_feed() ) {
                  $output = "\n";
                  foreach ( $attachments as $att_id => $attachment ) {
                        $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
                  }
                  return $output;
            }

            $columns       = intval( $atts['columns'] );
            $itemwidth     = $columns > 0 ? floor(100/$columns) : 100;

            $size_class    = sanitize_html_class( $atts['size'] );
            $output        = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} row flex-row'>";

            $i = 0;
            foreach ( $attachments as $id => $attachment ) {
                  $attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
                  if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
                        $image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
                  } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
                        $image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
                  } else {
                        $image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
                  }
                  $image_meta  = wp_get_attachment_metadata( $id );
                  $orientation = '';
                  if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
                        $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
                  }
                  $output .= "<{$itemtag} class='gallery-item col col-auto'>";
                  $output .= "
                        <{$icontag} class='gallery-icon {$orientation}'>
                              $image_output
                        </{$icontag}>";
                  if ( $captiontag && trim($attachment->post_excerpt) ) {
                        $output .= "
                              <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
                              " . wptexturize($attachment->post_excerpt) . "
                              </{$captiontag}>";
                  }
                  $output .= "</{$itemtag}>";
                  if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
                        $output .= '<br style="clear: both" />';
                  }
            }
            if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
                  $output .= "
                        <br style='clear: both' />";
            }
            $output .= "
                  </div>\n";


            return $output;
      }


      /*
       * HELPERS
       */
      function czr_fn_is_gallery_enabled(){
            return apply_filters('czr_enable_gallery', esc_attr( czr_fn_get_opt('tc_enable_gallery') ) );
      }
}//end of class
endif;
