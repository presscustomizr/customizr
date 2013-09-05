<?php
/**
* Adds theme supports using WP functions, 
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

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action( 'after_setup_theme'                     , array( $this , 'tc_customizr_setup' ));
    }


    /**
     * Sets up theme defaults and registers the various WordPress features
     * 
     *
     * @package Customizr
     * @since Customizr 1.0
     */

    function tc_customizr_setup() {
      //record for debug
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

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

      //post with no headers (filter this if needed)
      add_filter  ( 'post_type_with_no_headers'                 , array( $this , 'tc_post_type_with_no_headers' ));
      }



      /**
     * List of posts type with no headers
     *
     * @package Customizr
     * @since Customizr 3.0.10
     */
      function tc_post_type_with_no_headers() {
        return array( 'aside' , 'status' , 'link' , 'quote' );
      }


}//end of class
