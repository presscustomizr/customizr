<?php
/**
* Gallery content filters
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

class TC_gallery {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

      self::$instance =& $this;

      //adds a filter allowing lightbox navigation in galleries
      add_filter ( 'post_gallery'                   , array( $this , 'tc_fancybox_gallery_filter' ), 20, 2);

      //adds a filter for link markup (allow lightbox)
      add_filter ( 'wp_get_attachment_link'         , array($this, 'tc_modify_attachment_link') , 20, 6 );
    }


    /**
     * Gallery filter to enable lightbox navigation (based on the WP oroginal gallery function)
     * 
     * @package Customizr
     * @since Customizr 3.0.5
     */
    function tc_fancybox_gallery_filter( $output, $attr) {

        if( !apply_filters('tc_gallery_bool', true ) )
          return $output;
        
        

        //add a filter for link markup 
        //add_filter( 'wp_get_attachment_link', array($this, 'tc_modify_attachment_link') , 20, 6 );

        //COPY OF WP FUNCTION IN media.php
        $post = get_post();

        static $instance = 0;
        $instance++;

        // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
        if ( isset( $attr['orderby'] ) ) {
          $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
          if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
        }

        extract(shortcode_atts(array(
          'order'      => 'ASC',
          'orderby'    => 'menu_order ID',
          'id'         => $post->ID,
          'itemtag'    => 'dl',
          'icontag'    => 'dt',
          'captiontag' => 'dd',
          'columns'    => 3,
          'size'       => 'thumbnail',
          'include'    => '',
          'exclude'    => ''
        ), $attr));

        $id = intval($id);
        if ( 'RAND' == $order )
          $orderby = 'none';

        if ( !empty($include) ) {
          $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

          $attachments = array();
          foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
          }
        } elseif ( !empty($exclude) ) {
          $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
        } else {
          $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
        }

        if ( empty($attachments) )
          return '';

        if ( is_feed() ) {
          $output = "\n";
          foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
          return $output;
        }

        $itemtag = tag_escape($itemtag);
        $captiontag = tag_escape($captiontag);
        $icontag = tag_escape($icontag);
        $valid_tags = wp_kses_allowed_html( 'post' );
        if ( ! isset( $valid_tags[ $itemtag ] ) )
          $itemtag = 'dl';
        if ( ! isset( $valid_tags[ $captiontag ] ) )
          $captiontag = 'dd';
        if ( ! isset( $valid_tags[ $icontag ] ) )
          $icontag = 'dt';

        $columns = intval($columns);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
        $float = is_rtl() ? 'right' : 'left';

        $selector = "gallery-{$instance}";

        $gallery_style = $gallery_div = '';
        if ( apply_filters( 'use_default_gallery_style', true ) )
          $gallery_style = "
          <style type='text/css'>
            #{$selector} {
              margin: auto;
            }
            #{$selector} .gallery-item {
              float: {$float};
              margin-top: 10px;
              text-align: center;
              width: {$itemwidth}%;
            }
            #{$selector} img {
              border: 2px solid #cfcfcf;
            }
            #{$selector} .gallery-caption {
              margin-left: 0;
            }
          </style>
          <!-- see gallery_shortcode() in wp-includes/media.php -->";
        $size_class = sanitize_html_class( $size );
        $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
        $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
        $i = 0;
        foreach ( $attachments as $id => $attachment ) {

          $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

          $output .= "<{$itemtag} class='gallery-item'>";
          $output .= "
            <{$icontag} class='gallery-icon'>
              $link
            </{$icontag}>";
          if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
              <{$captiontag} class='wp-caption-text gallery-caption'>
              " . wptexturize($attachment->post_excerpt) . "
              </{$captiontag}>";
          }
          $output .= "</{$itemtag}>";
          if ( $columns > 0 && ++$i % $columns == 0 )
            $output .= '<br style="clear: both" />';
        }

        $output .= "
            <br style='clear: both;' />
          </div>\n";

        //remove the filter for link markup 
        //remove_filter( 'wp_get_attachment_link', array($this, 'tc_modify_attachment_link') , 20, 6 );

        return $output;
    }


    /**
     * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post gallery
     * Based on the original WP function
     * @package Customizr
     * @since Customizr 3.0.5
     *
     */
    function tc_modify_attachment_link( $markup, $id, $size, $permalink, $icon, $text ) {

      if( !apply_filters('tc_gallery_bool', true ) )
          return $markup;

      $tc_fancybox = esc_attr( tc__f( '__get_option' , 'tc_fancybox' ) );

      if ( $tc_fancybox == 1 && $permalink == false ) //add the filter only if link to the attachment file/image
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

}//end of class