<?php
/**
* Adds heme supports using WP functions, 
* Defines theme options and the functions to call them
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
class TC_init {

    function __construct () {
        add_action( 'after_setup_theme'                     , array( $this , 'tc_customizr_setup' ));
        add_filter( '__get_default_options'                 , array( $this , 'tc_get_default_options' ));
        add_filter( '__options'                             , array( $this , 'tc_get_theme_options' ));
    }




    /**
     * Sets up theme defaults and registers the various WordPress features
     * 
     *
     * @package Customizr
     * @since Customizr 1.0
     */

    function tc_customizr_setup() {
  
      /* Set default content width for post images and media. */
        global $content_width;
        if( ! isset( $content_width ) )   { $content_width = 1170; }

      /*
       * Makes Customizr available for translation.
       * Translations can be added to the /lang/ directory.
       */
       load_theme_textdomain( 'customizr' , TC_BASE . 'lang' );

      /* Adds RSS feed links to <head> for posts and comments. */
       add_theme_support( 'automatic-feed-links' );

      /*  This theme supports nine post formats. */
       add_theme_support( 'post-formats' , array( 'aside' , 'gallery' , 'link' , 'image' , 'quote' , 'status' , 'video' , 'audio' , 'chat' ) );

      /* This theme uses wp_nav_menu() in one location. */
       register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );

      /* This theme uses a custom image size for featured images, displayed on "standard" posts. */
       add_theme_support( 'post-thumbnails' );
        //set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

      //remove theme support => generates notice in admin @todo fix-it!
       /* remove_theme_support( 'custom-background' );
        remove_theme_support( 'custom-header' );*/
        //post thumbnails for dudy widget and post lists (archive, search, ...)
       add_image_size( 'tc-thumb' , $width = 270, $height = 250, $crop = true );

      //slider full width
       add_image_size( 'slider-full' , $width = 99999 , $height = 500, $crop = true);

      //slider boxed
       add_image_size( 'slider' , $width = 1170, $height = 500, $crop = true);
      }





      /**
     * Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
     * @package Customizr
     * @since Customizr 1.0
     *
     */
    function tc_get_theme_options () {
          $saved                          = (array) get_option( 'tc_theme_options' );
          $defaults                       = $this -> tc_get_default_options();
          $__options                      = wp_parse_args( $saved, $defaults );
          $__options                      = array_intersect_key( $__options, $defaults );

        return $__options;
    }





     /**
     * Returns the options array for the theme.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
      function tc_get_default_options() {
    
      $defaults = array(
          //sliders
          'tc_sliders'                    => array(),
          //skin
          'tc_skin'                       => 'blue.css' ,
          'tc_top_border'                 => 1,
          //logo and favicon
          'tc_logo_upload'                => null,
          'tc_logo_resize'                => 1,
          'tc_fav_upload'                 => null,
          //front page options
          'tc_front_slider'               => 'demo' ,
          'tc_slider_width'               => 1,
          'tc_slider_delay'               => 5000,
          'tc_front_layout'               => 'f' ,
          'tc_show_featured_pages'        => 1,
          'tc_show_featured_pages_img'    => 1,
          'tc_featured_page_one'          => null,
          'tc_featured_page_two'          => null,
          'tc_featured_page_three'        => null,
          'tc_featured_text_one'          => null,
          'tc_featured_text_two'          => null,
          'tc_featured_text_three'        => null,
          //layout options
          'tc_sidebar_global_layout'      => 'l' ,
          'tc_sidebar_force_layout'       =>  0,
          'tc_sidebar_post_layout'        => 'l' ,
          'tc_sidebar_page_layout'        => 'l' ,
          'tc_breadcrumb'                 => 1,
          //comments
          'tc_page_comments'              =>  0,
          //social options
          'tc_social_in_header'           => 1,
          'tc_social_in_right-sidebar'    => 0,
          'tc_social_in_left-sidebar'     => 0,
          'tc_social_in_footer'           => 1,
          'tc_rss'                        => get_bloginfo( 'rss_url' ),
          'tc_twitter'                    => null,
          'tc_facebook'                   => null,
          'tc_google'                     => null,
          'tc_youtube'                    => null,
          'tc_pinterest'                  => null,
          'tc_github'                     => null,
          'tc_dribbble'                   => null,
          'tc_linkedin'                   => null,
           //images
          'tc_fancybox'                   =>  1,
          //custom CSS
          'tc_custom_css'                 => null,
      );
      return $defaults;
    }
}//end of class
