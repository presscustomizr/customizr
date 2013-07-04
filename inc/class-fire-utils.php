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
        
        add_filter  ( 'wp_page_menu'                        , array( $this , 'add_menuclass' ));
        add_filter  ( 'the_content'                         , array( $this , 'tc_fancybox' ));
        add_filter  ( 'wp_title'                            , array( $this , 'tc_wp_title' ), 10, 2 );
        add_filter  ( '__get_options'                       , array( $this , 'tc_get_options' ));
        add_filter  ( '__ID'                                , array( $this , 'tc_get_the_ID' ));
        add_filter  ( '__screen_layout'                     , array( $this , 'tc_get_current_screen_layout' ));
        add_filter  ( '__screen_slider'                     , array( $this , 'tc_get_current_screen_slider' ));

        add_action  ( '__customizr_entry_date'              , array( $this , 'tc_customizr_entry_date' ));
        add_action  ( '__social'                            , array( $this , 'tc_display_social' ));
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

    function tc_fancybox( $content) {
        $tc_fancybox = esc_attr(tc__f ( '__get_options' , 'tc_fancybox' ));

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






     /**
     * Returns the options array for the theme.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
      function tc_get_options( $option_name) {
          $__options          = tc__f ( '__options' );
          $saved              = (array) get_option( 'tc_theme_options' );
          $defaults           = tc__f( '__get_default_options' ); //located TC_init.php
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
              $title = __( 'Follow me on ' , 'tc_boostrap' ).$nw;
              $target = 'target=_blank';
              //rss case
              if ( $key == 'tc_rss' ) {
                $title = __( 'Suscribe to my rss feed' , 'tc_boostrap' );
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

}//end of class