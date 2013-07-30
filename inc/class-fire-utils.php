<?php
/**
* Defines filters and actions used in several templates/classes
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_utils {

    function __construct () {
        
        add_filter  ( '__get_default_options'               , array( $this , 'tc_get_default_options' ) , 10);
        add_filter  ( '__options'                           , array( $this , 'tc_get_theme_options' ) ,10);
        add_filter  ( '__default_options_from_customizer_map' , array( $this , 'tc_get_default_options_from_customizer_map' ));

        //get single option
        add_filter  ( '__get_option'                        , array( $this , 'tc_get_option' ));

        add_filter  ( '__ID'                                , array( $this , 'tc_get_the_ID' ));
        add_filter  ( '__screen_layout'                     , array( $this , 'tc_get_current_screen_layout' ));
        add_filter  ( '__screen_slider'                     , array( $this , 'tc_get_current_screen_slider' ));

        add_action  ( '__customizr_entry_date'              , array( $this , 'tc_customizr_entry_date' ));
        add_action  ( '__social'                            , array( $this , 'tc_display_social' ));

        //WP filters
        add_filter  ( 'wp_page_menu'                        , array( $this , 'add_menuclass' ));
        add_filter  ( 'the_content'                         , array( $this , 'tc_fancybox_content_filter' ));
        add_filter  ( 'wp_title'                            , array( $this , 'tc_wp_title' ), 10, 2 );
        add_filter  ( 'post_gallery'                        , array( $this , 'tc_fancybox_gallery_filter' ), 20, 2);
    }




    /**
    * Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
    * @package Customizr
    * @since Customizr 1.0
    *
    */
    function tc_get_theme_options () {
          $saved                          = (array) get_option( 'tc_theme_options' );

          $defaults                       = tc__f('__get_default_options');

          $__options                      = wp_parse_args( $saved, $defaults );

          $__options                      = array_intersect_key( $__options, $defaults );

        return $__options;
    }



    function tc_get_default_options() {
      
      $map = tc__f('__customize_map', $get_default = 'true' );

      $customizer_defaults = $this -> tc_get_default_options_from_customizer_map($map);

      return $customizer_defaults;
    }





   /**
   * Return the default options array from a customizer map + add slider option
   *
   * @package Customizr
   * @since Customizr 3.3.0
   */
    function tc_get_default_options_from_customizer_map($map) {
      
      $defaults = array(
        //initialize the default array with the sliders options
        'tc_sliders' => array(),
      );

      foreach ($map['add_setting_control'] as $key => $options) {

        //check it is a customizr option
        if(false !== strpos($haystack = $key  , $needle = 'tc_theme_options')) {

          //isolate the option name between brackets [ ]
          $option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
          if ( isset( $match[1][0] ) ) 
            {
                $option_name = $match[1][0];
            }

          //write default option in array
          if(isset($options['default'])) {
            $defaults[$option_name] = $options['default'];
          }
          else {
            $defaults[$option_name] = null;
          }
         
        }//end if

      }//end foreach

    return $defaults;
    }




     /**
     * Returns the options array for the theme.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
    function tc_get_option( $option_name) {
        $saved              = (array) get_option( 'tc_theme_options' );

        $defaults           = tc__f( '__get_default_options' );

        $options            = wp_parse_args( $saved, $defaults );
        
        $options            = array_intersect_key( $options, $defaults );

      return $options[$option_name];
    }






      /**
      * This function is similiar to the wordpress function get_the_ID but takes into account the id of the page initially called
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_the_ID()  {
          $__options          = tc__f ( '__options' );

          if (is_404() || is_search())
            return null;
          if (!isset( $__options['another_query_in_the_main_loop'])) 
          {
              $id             = get_the_ID();
          }
          else 
          {
              $id             = $__options['original_ID'];
          }
        return $id;
      }





      /**
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_current_screen_layout ( $post_id) {
        $__options              = tc__f ( '__options' );
        
        //Article wrapper class definition
          $class_tab = array(
            'r' => 'span9' ,
            'l' => 'span9' ,
            'b' => 'span6' ,
            'f' => 'span12' ,
            );

        /* DEFAULT LAYOUTS */
        //get the global default layout
        $tc_sidebar_global_layout     = $__options['tc_sidebar_global_layout'];
        //get the post default layout
        $tc_sidebar_post_layout       = $__options['tc_sidebar_post_layout'];
        //get the page default layout
        $tc_sidebar_page_layout       = $__options['tc_sidebar_page_layout'];

        //what is the default layout we want to apply? By default we apply the global default layout
        $tc_sidebar_default_layout    = $tc_sidebar_global_layout;
        if (is_single())
          $tc_sidebar_default_layout  = $tc_sidebar_post_layout;
        if (is_page())
          $tc_sidebar_default_layout  = $tc_sidebar_page_layout;

        //build the default layout option array including layout and article class
        $tc_screen_layout = array(
            'sidebar' => $tc_sidebar_default_layout,
            'class'   => $class_tab[$tc_sidebar_default_layout]
          );

        //finally we check if the 'force default layout' option is checked and return the default layout before any specific layout
        $force_layout = $__options['tc_sidebar_force_layout'];
        if( $force_layout == 1) {
          $tc_screen_layout = array(
            'sidebar' => $tc_sidebar_global_layout,
            'class'   => $class_tab[$tc_sidebar_global_layout]
          );
          return $tc_screen_layout;
        }

        //get the front page layout
        $tc_front_layout =  $__options['tc_front_layout'];

        //get info whether the front page is a list of last posts or a page
        $tc_what_on_front  = get_option( 'show_on_front' );


        //get the post specific layout if any, and if we don't apply the default layout
        $tc_specific_post_layout = esc_attr(get_post_meta( $post_id, $key = 'layout_key' , $single = true ));
        
        if((is_home() && $tc_what_on_front == 'posts' ) || is_front_page())
           $tc_specific_post_layout = $tc_front_layout;

        if( $tc_specific_post_layout) {
            $tc_screen_layout = array(
            'sidebar' => $tc_specific_post_layout,
            'class'   => $class_tab[$tc_specific_post_layout]
          );
        }
        return $tc_screen_layout;
      }




      /**
      *
      * @package Customizr
      * @since Customizr 3.0
      */
      function tc_get_current_screen_slider () {

        $id = tc__f ( '__ID' );

        return esc_attr(get_post_meta( $id, $key = 'post_slider_key' , $single = true ));
      }



      
      

      /**
      * Prints HTML with date information for current post.
      * @package Customizr
      * @since Customizr 1.0 
      */
      function tc_customizr_entry_date( $echo = true ) {
        $format_prefix = ( has_post_format( 'chat' ) || has_post_format( 'status' ) ) ? _x( '%1$s on %2$s' , '1: post format name. 2: date' , 'customizr' ): '%2$s';

        $date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>' ,
          esc_url( get_permalink() ),
          esc_attr( sprintf( __( 'Permalink to %s' , 'customizr' ), the_title_attribute( 'echo=0' ) ) ),
          esc_attr( get_the_date( 'c' ) ),
          esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
        );

        if ( $echo )
          echo $date;

        return $date;
      }






      /**
      * 
      * @package Customizr
      * @since Customizr 1.0 
      */
      function tc_display_social( $pos) {

        $__options          = tc__f( '__options' );

        if( $__options[$pos] == 0)
          return;

        $socials = array (
              'tc_rss'            => __( 'feed' , 'customizr' ),
              'tc_twitter'        => __( 'twitter' , 'customizr' ),
              'tc_facebook'       => __( 'facebook' , 'customizr' ),
              'tc_google'         => __( 'google' , 'customizr' ),
              'tc_youtube'        => __( 'youtube' , 'customizr' ),
              'tc_pinterest'      => __( 'pinterest' , 'customizr' ),
              'tc_github'         => __( 'github' , 'customizr' ),
              'tc_dribbble'       => __( 'dribbble' , 'customizr' ),
              'tc_linkedin'       => __( 'linkedin' , 'customizr' )
              );
          
          $html = '';
          //check if sidebar option is checked
          if (preg_match( '/left|right/' , $pos)) {
            $html = '<h3 class="widget-title">'.__( 'Social links' , 'customizr' ).'</h3>';
          }
          //$html .= '<ul>';
            foreach ( $socials as $key => $nw) {
              //all cases except rss
              $title = __( 'Follow me on ' , 'customizr' ).$nw;
              $target = 'target=_blank';
              //rss case
              if ( $key == 'tc_rss' ) {
                $title = __( 'Suscribe to my rss feed' , 'customizr' );
                $target = '';
              }

              if ( $__options[$key] != '' ) {
                //$html .= '<li>';
                  $html .= '<a class="social-icon icon-'.$nw.'" href="'.esc_url( $__options[$key]).'" title="'.$title.'" '.$target.'></a>';
              }
           }
          //$html .= '</li></ul>';
       
        echo $html;
      }



      /* adds a specific class to the ul wrapper */
    function add_menuclass( $ulclass) {
       return preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
    }
    



    /**
     * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
     * @package Customizr
     * @since Customizr 2.0.7
     *
     */
    function tc_fancybox_content_filter( $content) {
        $tc_fancybox = esc_attr(tc__f ( '__get_option' , 'tc_fancybox' ));

        if ( $tc_fancybox == 1 ) 
        {
             global $post;
             $pattern ="/<a(.*?)href=( '|\")(.*?).(bmp|gif|jpeg|jpg|png)( '|\")(.*?)>/i";
             $replacement = '<a$1href=$2$3.$4$5 class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'" title="'.$post->post_title.'"$6>';
             $content = preg_replace( $pattern, $replacement, $content);
        }

      return $content;
    }



    /**
     * Gallery filter to enable lightbox navigation
     * based on the WP oroginal gallery function
     * @package Customizr
     * @since Customizr 3.0.5
     *
     */
    function tc_fancybox_gallery_filter( $output, $attr) {
       
        //add a filter for link markup 
        add_filter( 'wp_get_attachment_link', array($this, 'tc_modify_attachment_link') , 20, 6 );

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
        remove_filter( 'wp_get_attachment_link', array($this, 'tc_modify_attachment_link') , 20, 6 );

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
      $tc_fancybox = esc_attr(tc__f ( '__get_option' , 'tc_fancybox' ));

      if ( $tc_fancybox == 1 && $permalink == false ) //add the filter only if link to the attachment file/image
        {
            $id = intval( $id );
            $_post = get_post( $id );

            if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
              return __( 'Missing Attachment' , 'customizr' );

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


    /**
     * Title element formating
     *
     * @since Customizr 2.1.6
     *
     */
    
      function tc_wp_title( $title, $sep ) {
        global $paged, $page;

        if ( is_feed() )
          return $title;

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description' , 'display' );
        if ( $site_description && ( is_home() || is_front_page() ) )
          $title = "$title $sep $site_description";

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 )
          $title = "$title $sep " . sprintf( __( 'Page %s' , 'customizr' ), max( $paged, $page ) );

        return $title;
      }


}//end of class