<?php
/**
* Gallery content filters
*
*/
if ( ! class_exists( 'CZR_gallery' ) ) :
class CZR_gallery {

      static $instance;

      function __construct () {

            self::$instance =& $this;

            /*
            * Filters the default gallery shortcode output.
            * see wp-includes/media.php
            */
            add_filter( 'post_gallery'                            , array( $this, 'czr_fn_czr_gallery' ), 10, 3 );

            //add data attribute
            add_filter( 'czr_gallery_image_linking_media'         , array( $this, 'czr_fn_maybe_lighbox_attachment_link' ), 10, 3 );
            add_filter( 'czr_gallery_image_linking_no_media'      , array( $this, 'czr_fn_maybe_add_lighbox_button' ), 10, 3 );

      }


      /**
      * Builds the Gallery shortcode output.
      * see the gallery_shortcode in wp-includes/media.php
      * @return string HTML content to display gallery.
      */
      function czr_fn_czr_gallery( $gallery, $attr, $instance ) {

            $post = get_post();

            $atts = shortcode_atts( array(
                             'order'       => 'ASC',
                             'orderby'     => 'menu_order ID',
                             'id'          => $post ? $post->ID : 0,
                             'columns'     => 3,
                             'size'        => 'thumbnail',
                             'include'     => '',
                             'exclude'     => '',
                             'link'        => '',
                             'type'        => 'grid'
                        ), $attr, 'gallery' );


            //do nothing if the customizr gallery is not enabled and type different than "attachments-only"
            //we use "attachments-only" when retrieving the first post gallery in some lists of posts
            //see core/front/models/content/common/media/class-model-gallery.php::czr_fn__get_post_gallery()
            if ( 'attachments-only' != $atts[ 'type' ] && ! $this -> czr_fn_is_gallery_enabled() ) {
                  return $gallery;
            }


            $id   = intval( $atts['id'] );

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

            //we use "attachments-only" when retrieving the first post gallery in some lists of posts
            //see core/front/models/content/common/media/class-model-gallery.php::czr_fn__get_post_gallery()
            if ( 'attachments-only' == $atts[ 'type' ]  ) {
                  return $attachments;
            }


            $gallery_class = implode( ' ', array_filter( array(
                  'czr-gallery',
                   'row',
                   'flex-row',
                  1 == esc_attr( czr_fn_opt( 'tc_gallery_style' ) ) ? 'czr-gallery-style' : ''
                  )
            ) );

            $itemtag       = 'figure';
            $itemclass     = 'col col-auto';
            $captiontag    = 'figcaption';
            $icontag       = 'div';
            $iconclass     = 'czr-gallery-icon';

            $columns       = intval( $atts['columns'] );
            $size_class    = sanitize_html_class( $atts['size'] );
            $selector      = "gallery-{$instance}";

            $output        = "<div id='$selector' class='{$gallery_class} gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

            $i = 0;

            //image_attr
            $image_attr    = array();

            $image_attr[ 'sizes' ] = $this -> czr_fn_generate_gallery_img_sizes( $columns );

            foreach ( $attachments as $id => $attachment ) {

                  if ( trim( $attachment->post_excerpt ) )
                        $image_attr[ 'aria-describedby' ] =  "$selector-$id";

                  $image_output = wp_get_attachment_image( $id, $atts['size'], false, $image_attr );

                  if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
                        $link         = wp_get_attachment_url( $id );

                        $image_output = sprintf( '<a href=%1$s class="bg-link"></a>%2$s', $link, $image_output );

                        $image_output = apply_filters( 'czr_gallery_image_linking_media', $image_output, $id, $attachment );

                  } elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {

                        //no link
                        //maybe add expand img button
                        $image_output = apply_filters( 'czr_gallery_image_linking_no_media', $image_output, $id, $attachment );

                  } else {

                        //link to attachment page
                        $link = get_attachment_link( $id );

                        $image_output = sprintf( '<a href=%1$s class="bg-link"></a>%2$s', $link, $image_output );

                        //maybe add expand img button
                        $image_output = apply_filters( 'czr_gallery_image_linking_no_media', $image_output, $id, $attachment );
                  }

                  $image_meta  = wp_get_attachment_metadata( $id );
                  $orientation = '';

                  if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
                        $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
                  }

                  $output .= "<{$itemtag} class='gallery-item {$itemclass}'>";
                  $output .= "
                        <{$icontag} class='gallery-icon {$orientation} {$iconclass}'>
                              $image_output
                        </{$icontag}>";

                  if ( $captiontag && trim($attachment->post_excerpt) ) {
                        $output .= "
                              <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
                              " . wptexturize($attachment->post_excerpt) . "
                              </{$captiontag}>";
                  }

                  $output .= "</{$itemtag}>";

            }

            $output .= "
                  </div>\n";


            return $output;
      }



      function czr_fn_maybe_lighbox_attachment_link( $link_markup, $id, $attachment ) {

            if ( ! apply_filters( 'tc_gallery_fancybox', esc_attr( czr_fn_opt( 'tc_gallery_fancybox' ) ) , $id ) ) {
                  return $link_markup;
            }

            $title = trim($attachment->post_excerpt) ? ' title="'. wptexturize($attachment->post_excerpt) .'"' : '';

            return str_replace( '<a', '<a data-lb-type="grouped-gallery"'.$title, $link_markup );
      }


      function czr_fn_maybe_add_lighbox_button( $markup, $id, $attachment ) {

            if ( ! apply_filters( 'tc_gallery_fancybox', esc_attr( czr_fn_opt( 'tc_gallery_fancybox' ) ) , $id ) ) {
                  return $markup;
            }

            $title = trim($attachment->post_excerpt) ? ' title="'. wptexturize($attachment->post_excerpt).'"' : '';

            //get original expanded img
            $link  = wp_get_attachment_url( $id );
            $attr  = 'data-lb-type="grouped-gallery"' . $title;

            // check for existence of the function to fix issue when previewing customizations of Nimble Builder
            // see https://github.com/presscustomizr/nimble-builder/issues/562
            // because fn not declared when customizing
            if ( function_exists( 'czr_fn_post_action' ) ) {
                return $markup . czr_fn_post_action( $link, $class = '', $attr,  $echo = false );
            } else {
                return $markup;
            }
      }



      /*
       * HELPERS
       */

      /*
      * Builds the gallery responsive image sizes attribute.
      * @return string.
      */
      function czr_fn_generate_gallery_img_sizes( $gallery_columns ) {

            //get content breadth, depends on the global layout, can be: 'full' (no sidebars), 'semi-narrow' (1 sidebar), 'narrow' (2 sidebars)
            $content_breadth        = czr_fn_get_content_breadth();
            //starting from md (tablets, 768px and up)
            //this actually depends on CZR_init::$instnace->global_layout
            //and should be better parameterized, as in the future, might be not constant for each viewport
            $article_container_width_md_up_ratios = array(
                  'full'        => 1,
                  'semi-narrow' => 3/4, //col-md-9
                  'narrow'      => 1/2 //col-md-6
            );

            $article_container_width_md_up_ratio = $article_container_width_md_up_ratios[ $content_breadth ];


            /*
            CZR_init::$instance->$css_container_width looks like:

            array(
                //min-widths: 1200px, 992px, 768px,
                //xl, lg, md, sm, xs
                '1140', '960', '720', '540' //, no xs => 100%

                'xl' => '1140',
                'lg' => '960',
                'md' => '720',
                'sm' => '540'
            )
            */
            $css_container_widths   = CZR_init::$instance->css_container_widths;

            /*
            CZR_init::$instance->$css_mq_breakpoints looks like:

            array(
                  'xl' => '1200',
                  'lg' => '992',
                  'md' => '768',
                  'sm' => '575'
            )
            */
            $css_mq_breakpoints     = CZR_init::$instance->css_mq_breakpoints;



            $gallery_item_h_padding = 30; //px (15+15)

            //Following the principle used to set the gallery items columns in CSS)
            //
            //
            // (1) In extra small devices (max-width 575px) the gallery items take 50% of the container width
            // (2) except the 1 column case, which is displayed at 100%
            //
            // (3) From small devices to desktop (min-width 576px) the 3 columns gallery items take 1/3 of the container width
            // (4) In small devices (min-width 576px) the 4-9 columns gallery items will take 25% of the container width
            //     while starting from a ww of 768px they'll take the 100/$i ( $i in [4,9] ) of the container width (see 4.a for exceptions)
            //
            // (4.a) when displaying two sidebars, to avoid very very small gallery items, in the min-width 768px and max-width 991px viewport,
            // we limit the width of the 4-9 columns gallery items to 25% of the container width
            //

            //default, mobile first:
            // 1 column  => 100vw - (left and right padding) 30px  (1)
            // 2+ columns => 50vw - (left and right padding) 30px  (2)
            $default_sizes = 1 == $gallery_columns ? 'calc( 100vw - '. $gallery_item_h_padding . 'px )' : 'calc( 50vw - '. $gallery_item_h_padding  . 'px )';

            $sizes = array();

            //let's start with the media queries

            // Small devices (landscape phones, 576px and up)
            //(min-width: 576px) => .container = 540px => article container's width 540px ( #content.col-12 == .container width )
            $article_container_width = $css_container_widths[ 'sm' ];
            if ( 1 == $gallery_columns ) {
                  $image_size = $article_container_width;
            }

            elseif ( 2 == $gallery_columns ) {
                  $image_size = $article_container_width/2;
            }

            //(3)
            elseif ( 3 == $gallery_columns ) {
                  $image_size = $article_container_width/3;
            }
            //(4)
            else {
                  $image_size = $article_container_width/4;
            }


            $sizes[] = sprintf( '(min-width: %1$spx) %2$spx',
                  $css_mq_breakpoints[ 'sm' ],
                  $image_size - $gallery_item_h_padding
            );


            //Considering 3 columns, with two sidebars:
            //Sizes now looks like:
            //Array(
            //    [0] => (min-width: 576px) 150px
            //)



            // Medium devices (tablets, 768px and up)
            //(min-width: 768px) => .container = 720px => article container's width depends on the content breadth
            $css_container_width     = $css_container_widths[ 'md' ];

            //get the article container width in pixels
            $article_container_width = $css_container_width * $article_container_width_md_up_ratio;

            //(4.a)
            $_image_size = ( $gallery_columns > 3 && 'narrow' == $content_breadth ) ? $article_container_width / 4 :  $article_container_width / $gallery_columns ;
            //avoid adjacents duplicate sizes => make the mq's smaller bp win
            if ( $_image_size != $image_size ) {
                  $image_size = $_image_size;
                  //$sizes[] = sprintf( '(min-width: %1$spx) and (max-width: %2$spx) %3$spx',

                  $sizes[] = sprintf( '(min-width: %1$spx) %3$spx',
                        $css_mq_breakpoints[ 'md' ],
                        $css_mq_breakpoints[ 'lg' ] - 1,
                        $image_size - $gallery_item_h_padding
                  );
            }

            //Considering 3 columns, with two sidebars:
            //Sizes now looks like:
            //Array
            //(
            //    [0] => (min-width: 576px) 150px
            //    [1] => (min-width: 768px) 90px
            //)

            // Large devices (desktops, 992px and up) and Extra large devices (large desktops, 1200px and up)
            foreach ( array( 'lg', 'xl' ) as $mq ) {
                  $css_container_width     = $css_container_widths[ $mq ];
                  //get the article container width in pixels
                  $article_container_width = $css_container_width * $article_container_width_md_up_ratio;

                  //avoid adjacents duplicate sizes => make the mq's smaller bp win
                  $_image_size = $article_container_width / $gallery_columns;

                  if ( $_image_size != $image_size ) {
                        $image_size = $_image_size;
                        $sizes[] = sprintf( '(min-width: %1$spx) %2$spx',
                              $css_mq_breakpoints[ $mq ],
                              $image_size - $gallery_item_h_padding
                        );
                  }
            }


            //Considering 3 columns, with two sidebars:
            //Sizes now looks like:
            //Array
            //(
            //    [0] => (min-width: 576px) 150px
            //    [1] => (min-width: 768px) 90px
            //    [2] => (min-width: 992px) 130px
            //    [3] => (min-width: 1200px) 160px
            //)

            //the sizes order matters, from higher mq down
            $sizes   = array_reverse( $sizes );

            //add the default size
            $sizes[] = $default_sizes;

            return apply_filters( 'czr_fn_gallery_img_sizes',  join( ', ', $sizes ), $sizes, $gallery_columns );
      }



      function czr_fn_is_gallery_enabled(){
            return apply_filters('czr_enable_gallery', esc_attr( czr_fn_opt('tc_enable_gallery') ) );
      }


}//end of class
endif;
