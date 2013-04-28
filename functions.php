<?php

/**
 * Customizer functions and definitions.
 *
 * @package Customizr
 * @since Customizr 2.0
 * @author nikeo
 * @copyright Copyright (c) Nicolas Guillaume
 * @link    http://themesandco.com
 */
/* CUSTOMIZR_VER is the Version */
if( ! defined('CUSTOMIZR_VER' ) )    {  define( 'CUSTOMIZR_VER', '2.0' ); }




if(!function_exists('tc_get_all_options')) :
add_action( 'wp_head', 'tc_get_all_options');
/**
 * Init the customizr options array
 * @package Customizr
 * @since Customizr 1.0
 *
 */
  function tc_get_all_options () {
      global $tc_theme_options;
      $tc_theme_options = tc_get_theme_options();
      global $wp_query;
       
      //Add the dynamic options to the main option array
      $tc_theme_options['tc_current_screen_layout']  = tc_get_current_screen_layout($post_id = tc_get_the_ID());
      $tc_theme_options['tc_current_screen_slider']  = esc_attr(get_post_meta( tc_get_the_ID(), $key = 'post_slider_key', $single = true ));
      
     /*  //Add the post page parameter
      $tc_is_posts_page = $wp_query -> is_posts_page;
      $tc_theme_options['tc_is_posts_page']  = $tc_is_posts_page;*/
      return $tc_theme_options;
    }
endif;




if(!function_exists('tc_get_theme_options')) :
/**
 * Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
 * @package Customizr
 * @since Customizr 1.0
 *
 */
  function tc_get_theme_options () {
      global $tc_theme_options;
      
      //Customizer options
      $saved = (array) get_option( 'tc_theme_options' );
  
      $defaults = tc_get_default_options();

      $defaults = apply_filters( 'tc_default_theme_options', $defaults );

      $tc_theme_options = wp_parse_args( $saved, $defaults );

      $tc_theme_options = array_intersect_key( $tc_theme_options, $defaults );
      
      return $tc_theme_options;
    }
endif;



if(!function_exists('tc_get_default_options')) :
 /**
 * Returns the options array for the theme.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
  function tc_get_default_options() {
    
      $defaults = array(
          //sliders
          'sliders'                       => array(),
          //skin
          'tc_skin'                       => 'blue.css',
          //logo and favicon
          'tc_logo_upload'                => null,
          'tc_logo_resize'                => 1,
          'tc_fav_upload'                 => null,
          //front page options
          'tc_front_slider'               => 'demo',
          'tc_slider_width'               => 1,
          'tc_slider_delay'               => 5000,
          'tc_front_layout'               => 'f',
          'tc_show_featured_pages'        => 1,
          'featured_page_one'             => null,
          'featured_page_two'             => null,
          'featured_page_three'           => null,
          'featured_text_one'             => null,
          'featured_text_two'             => null,
          'featured_text_three'           => null,
          //layout options
          'tc_sidebar_global_layout'      => 'r',
          'tc_sidebar_force_layout'       =>  0,
          'tc_sidebar_post_layout'        => 'r',
          'tc_sidebar_page_layout'        => 'r',
          'tc_breadcrumb'                 => 1,
          //social options
          'tc_social_in_header'           => 1,
          'tc_social_in_right-sidebar'    => 0,
          'tc_social_in_left-sidebar'     => 0,
          'tc_social_in_footer'           => 1,
          'tc_rss'                        => get_bloginfo('rss_url'),
          'tc_twitter'                    => null,
          'tc_facebook'                   => null,
          'tc_google'                     => null,
          'tc_youtube'                    => null,
          'tc_pinterest'                  => null,
          'tc_github'                     => null,
          'tc_dribbble'                   => null,
          'tc_linkedin'                   => null,
      );

      $defaults = apply_filters( 'tc_default_theme_options', $defaults );

      return $defaults;
  }
endif;


if(!function_exists('customizr_setup')) :
add_action( 'after_setup_theme', 'customizr_setup' );
/**
 * Sets up theme defaults and registers the various WordPress features
 * 
 *
 * @package Customizr
 * @since Customizr 1.0
 */

  function customizr_setup() {
  
  
    /* Set default content width for post images and media. */
      global $content_width;
      if( ! isset( $content_width ) ) $content_width = 1170;

    /* TC_BASE is the root server path */
      if( ! defined('TC_BASE' ) )    {  define( 'TC_BASE', get_template_directory().'/' ); }

    /* TC_BASE_URL http url of the loaded template */
      if( ! defined('TC_BASE_URL' ) ){  define( 'TC_BASE_URL', get_template_directory_uri() . '/'); }

        // get themedata version wp 3.4+
        if(function_exists('wp_get_theme'))
        {
          $wp_theme_obj = wp_get_theme();
          $tc_base_data['prefix'] = $tc_base_data['Title'] = $wp_theme_obj->get('Name');
        }
        // get themedata lower versions
        else
        {
           $tc_base_data = get_theme_data( TC_BASE . 'style.css' );
           $tc_base_data['prefix'] =  $tc_base_data['Title'];
        }

    /* THEMENAME contains the Name of the currently loaded theme */
      if( ! defined('THEMENAME' ) ) { define( 'THEMENAME', $tc_base_data['Title'] ); }

    /*
  	 * Makes Customizr available for translation.
  	 *
  	 * Translations can be added to the /lang/ directory.
  	 */
  	load_theme_textdomain( 'customizr', TC_BASE . 'lang' );

  	/* Adds RSS feed links to <head> for posts and comments. */
  	add_theme_support( 'automatic-feed-links' );

  	/*  This theme supports nine post formats. */
  	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link','image', 'quote', 'status','video','audio', 'chat' ) );

  	/* This theme uses wp_nav_menu() in one location. */
  	register_nav_menu( 'main', __( 'Main Menu', 'customizr' ) );

  	/* adds a specific class to the ul wrapper */
  	function add_menuclass($ulclass) {
  	   return preg_replace('/<ul>/', '<ul class="nav">', $ulclass, 1);
  	}
  	add_filter('wp_page_menu','add_menuclass');


    /* This theme uses a custom image size for featured images, displayed on "standard" posts. */
    add_theme_support( 'post-thumbnails' );
    	//set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

    //remove theme support => generates notice in admin @todo fix-it!
     /* remove_theme_support('custom-background');
      remove_theme_support('custom-header');*/
      //post thumbnails for dudy widget and post lists (archive, search, ...)
      add_image_size( 'tc-thumb', $width = 270, $height = 250, $crop = true );

      //slider full width
      add_image_size('slider-full', $width = 99999 , $height = 500, $crop = true);

      //slider boxed
      add_image_size('slider', $width = 1170, $height = 500, $crop = true);

    /* LOADS THE FRONT END FUNCTIONS */
      require_once( TC_BASE.'inc/tc_handy_helpers.php');
      require_once( TC_BASE.'inc/tc_hot_crumble.php');
      require_once( TC_BASE.'inc/tc_voila_slider.php' );
      require_once( TC_BASE.'inc/tc_dudy_widgets.php');

    /* LOADS THE BACK END STUFFS */
     global $wp_version;
      //check WP version to include customizer functions, must be >= 3.4
     if (version_compare($wp_version, '3.4', '>=') ) {
          require_once( TC_BASE.'inc/admin/tc_customize.php');
      }
      else {
          //redirect to an upgrade message page on activation if version < 3.4
          add_action ('admin_menu', 'tc_add_fallback_page');
          add_action ('admin_init','tc_theme_activation_fallback');
      }
      require_once( TC_BASE.'inc/admin/tc_meta_boxes.php');
    }
endif;




if(!function_exists('tc_theme_activation_fallback')) :

/**
*  On activation, redirect on the customization page, set the frontpage option to "posts" with 10 posts per page
* @package Customizr
* @since Customizr 1.1
*/
  function tc_theme_activation_fallback()
  {
    global $pagenow;
    if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
    {
      #redirect to options page
      header( 'Location: '.admin_url().'themes.php?page=upgrade_wp.php' ) ;
    }
  }
endif;



if(!function_exists('tc_add_fallback_page')) :
/**
 * Add fallback admin page.
 * @package Customizr
 * @since Customizr 1.1
 */
  function tc_add_fallback_page() {
      $theme_page = add_theme_page(
          __( 'Upgrade WP', 'customizr' ),   // Name of page
          __( 'Upgrade WP', 'customizr' ),   // Label in menu
          'edit_theme_options',          // Capability required
          'upgrade_wp.php',             // Menu slug, used to uniquely identify the page
          'fallback_admin_page'         //function to be called to output the content of this page
      );
  }
endif;




if(!function_exists('fallback_admin_page')) :
  /**
 * Render fallback admin page.
 * @package Customizr
 * @since Customizr 1.1
 */
  function fallback_admin_page() {
    ?>
    <div class="wrap upgrade_wordpress">
      <div id="icon-options-general" class="icon32"><br></div>
      <h2><?php _e( 'This theme requires WordPress 3.4+', 'customizr' ) ?> </h2>
      <br />
      <p style="text-align:center">
        <a style="padding: 8px" class="button-primary" href="<?php echo admin_url().'update-core.php' ?>" title="<?php _e( 'Upgrade Wordpress Now','customizr' ) ?>">
        <?php _e( 'Upgrade Wordpress Now','customizr' ) ?></a>
        <br /><br />
      <img src="<?php echo TC_BASE_URL . 'screenshot.png' ?>" alt="Customizr" />
      </p>
    </div>
    <?php
  }
endif;





if(!function_exists('customizer_styles')) :
/**
 * Registers and enqueues Customizr stylesheets
 * @package Customizr
 * @since Customizr 1.1
 */
add_action('wp_enqueue_scripts', 'customizer_styles');
  function customizer_styles() {
    wp_register_style( 
      'customizr-skin', 
      TC_BASE_URL.'inc/css/'.tc_get_options('tc_skin'), 
      array(), 
      CUSTOMIZR_VER, 
      $media = 'all' 
      );
    //enqueue skin
    wp_enqueue_style( 'customizr-skin');

    //enqueue WP style sheet
    wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), CUSTOMIZR_VER,$media = 'all'  );
}
endif;




if(!function_exists('tc_scripts')) :
add_action('wp_enqueue_scripts', 'tc_scripts');
/**
 * Loads Customizr and JS script in footer for better time load.
 * 
 * @uses wp_enqueue_script() to manage script dependencies
 * @package Customizr
 * @since Customizr 1.0
 */
  function tc_scripts() {
      //SCRIPTS the true boolean parameter means it's loaded in the footer
      wp_enqueue_script('jquery');
      wp_enqueue_script('bootstrap',TC_BASE_URL . 'inc/js/bootstrap.min.js',array('jquery'),null,true);
     
      //tc scripts
      wp_enqueue_script('tc-scripts',TC_BASE_URL . 'inc/js/tc-scripts.js',array('jquery'),null,true);

      //tc scripts
      wp_enqueue_script('holder',TC_BASE_URL . 'inc/js/holder.js',array('jquery'),null,true);

      //modernizr (must be loaded in wp_head())
      wp_enqueue_script('modernizr',TC_BASE_URL . 'inc/js/modernizr.js',array('jquery'),null,false);
   }
endif;

/* Your smart functions here ! */