<?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
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
if ( ! class_exists( 'TC_init' ) ) :
  class TC_init {
      //declares the filtered default settings
      public $global_layout;
      public $tc_thumb_size;
      public $slider_full_size;
      public $slider_size;
      public $skins;
      public $skin_color_map;
      public $font_pairs;
      public $font_selectors;
      public $fp_ids;
      public $socials;
      public $sidebar_widgets;
      public $footer_widgets;
      public $widgets;
      public $post_list_layout;
      public $post_formats_with_no_heading;
      public $content_404;
      public $content_no_results;
      public $default_slides;

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {

          self::$instance =& $this;
          //Default layout settings
          $this -> global_layout      = array(
                                        'r' => array(
                                            'content'       => 'span9',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( 'Right sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Right sidebar' , 'customizr' ),
                                        ),
                                        'l' => array(
                                            'content'       => 'span9',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( 'Left sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Left sidebar' , 'customizr' ),
                                        ),
                                        'b' => array(
                                            'content'       => 'span6',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( '2 sidebars : Right and Left' , 'customizr' ),
                                            'metabox'       => __( '2 sidebars : Right and Left' , 'customizr' ),
                                        ),
                                        'f' => array(
                                            'content'       => 'span12',
                                            'sidebar'       => false,
                                            'customizer'    => __( 'No sidebars : full width layout', 'customizr' ),
                                            'metabox'       => __( 'No sidebars : full width layout' , 'customizr' ),
                                        ),
          );

          //Default images sizes
          $this -> tc_thumb_size      = array('width' => 270 , 'height' => 250, 'crop' => true ); //size name : tc-thumb
          $this -> slider_full_size   = array('width' => 9999 , 'height' => 500, 'crop' => true ); //size name : slider-full
          $this -> slider_size        = array('width' => 1170 , 'height' => 500, 'crop' => true ); //size name : slider

          //Default skins array
          $this -> skins              =  array(
                'blue.css'        =>  __( 'Blue' , 'customizr' ),
                'black.css'       =>  __( 'Black' , 'customizr' ),
                'black2.css'      =>  __( 'Flat black' , 'customizr' ),
                'grey.css'        =>  __( 'Grey' , 'customizr' ),
                'grey2.css'       =>  __( 'Ligth grey' , 'customizr' ),
                'purple2.css'     =>  __( 'Flat purple' , 'customizr' ),
                'purple.css'      =>  __( 'Purple' , 'customizr' ),
                'red2.css'        =>  __( 'Flat red' , 'customizr' ),
                'red.css'         =>  __( 'Red' , 'customizr' ),
                'orange.css'      =>  __( 'Orange' , 'customizr' ),
                'orange2.css'     =>  __( 'Flat orange' , 'customizr'),
                'yellow.css'      =>  __( 'Yellow' , 'customizr' ),
                'yellow2.css'     =>  __( 'Flat yellow' , 'customizr' ),
                'green.css'       =>  __( 'Green' , 'customizr' ),
                'green2.css'      =>  __( 'Light green' , 'customizr'),
                'blue3.css'       =>  __( 'Green blue' , 'customizr'),
                'blue2.css'       =>  __( 'Light blue ' , 'customizr' )

          );

          //Main skin color array
          $this -> skin_color_map     = array(
                'blue.css'        =>  '#08c',
                'blue2.css'       =>  '#27CBCD',
                'blue3.css'       =>  '#27CDA5',
                'green.css'       =>  '#9db668',
                'green2.css'      =>  '#26CE61',
                'yellow.css'      =>  '#e9a825',
                'yellow2.css'     =>  '#d2d62a',
                'orange.css'      =>  '#F78C40',
                'orange2.css'     =>  '#E79B5D',
                'red.css'         =>  '#e10707',
                'red2.css'        =>  '#e7797a',
                'purple.css'      =>  '#e67fb9',
                'purple2.css'     =>  '#8183D8',
                'grey.css'        =>  '#5A5A5A',
                'grey2.css'       =>  '#E4E4E4',
                'black.css'       =>  '#000',
                'black2.css'      =>  '#394143'
          );

          //Default fonts pairs
          $this -> font_pairs             = array(
            'gfont' => array(
              'name'  => __('Google fonts pairs' , 'customizr'),
              'list'  => apply_filters( 'tc_gfont_pairs' , array(
                '_g_fjalla_cantarell'              => array( 'Fjalla One &amp; Cantarell' , 'Fjalla+One:400|Cantarell:400' ),
                '_g_lobster_raleway'               => array( 'Lobster &amp; Raleway' , 'Lobster:400|Raleway' ),
                '_g_alegreya_roboto'               => array( 'Alegreya &amp; Roboto' , 'Alegreya:700|Roboto' ),
                '_g_lato_grand_hotel'              => array( 'Lato &amp; Grand Hotel', 'Lato:400|Grand+Hotel' ),
                '_g_dosis_opensans'                => array( 'Dosis &amp; Open Sans' , 'Dosis:400|Open+Sans' ),
                '_g_dancing_script_eb_garamond'    => array( 'Dancing Script &amp; EB Garamond' , 'Dancing+Script:700|EB+Garamond' ),
                '_g_amatic_josephin'               => array( 'Amatic SC &amp; Josefin Sans' , 'Amatic+SC:700|Josefin+Sans:700' ),
                '_g_oswald_droid'                  => array( 'Oswald &amp; Droid Serif' , 'Oswald:700|Droid+Serif:400' ),
                '_g_playfair_alice'                => array( 'Playfair Display &amp; Alice' , 'Playfair+Display:700|Alice' ),
                '_g_medula_abel'                   => array( 'Medula One &amp; Abel' , 'Medula+One:400|Abel' ),
                '_g_coustard_leckerli'             => array( 'Coustard Ultra &amp; Leckerli One' , 'Coustard:900|Leckerli+One' ),
                '_g_sacramento_alice'              => array( 'Sacramento &amp; Alice' , 'Sacramento:400|Alice' ),
                '_g_squada_allerta'                => array( 'Squada One &amp; Allerta' , 'Squada+One:400|Allerta' ),
                '_g_bitter_sourcesanspro'          => array( 'Bitter &amp; Source Sans Pro' , 'Bitter:400|Source+Sans+Pro' ),
                '_g_montserrat_neuton'             => array( 'Montserrat &amp; Neuton' , 'Montserrat:400|Neuton' )
              ) )
            ),
            'wsfont' => array(
              'name'  => __('Web safe fonts pairs' , 'customizr'),
              'list'  => apply_filters( 'tc_wsfont_pairs' , array(
                'impact_palatino'               => array( 'Impact &amp; Palatino' , 'Impact,Charcoal,sans-serif|Palatino Linotype,Book Antiqua,Palatino,serif'),
                'georgia_verdana'               => array( 'Georgia &amp; Verdana' , 'Georgia,Georgia,serif|Verdana,Geneva,sans-serif' ),
                'tahoma_times'                  => array( 'Tahoma &amp; Times' , 'Tahoma,Geneva,sans-serif|Times New Roman,Times,serif'),
                'lucida_courrier'               => array( 'Lucida &amp; Courrier' , 'Lucida Sans Unicode,Lucida Grande,sans-serif|Courier New,Courier New,Courier,monospace')
              ) )
            ),
           'default' => array(
            'name'  => __('Single fonts' , 'customizr'),
            'list'  => apply_filters( 'tc_single_fonts' , array(
                  '_g_cantarell'                  => array( 'Cantarell' , 'Cantarell:400|Cantarell:400' ),
                  '_g_raleway'                    => array( 'Raleway' , 'Raleway|Raleway' ),
                  '_g_roboto'                     => array( 'Roboto' , 'Roboto|Roboto' ),
                  '_g_grand_hotel'                => array( 'Grand Hotel', 'Grand+Hotel|Grand+Hotel' ),
                  '_g_opensans'                   => array( 'Open Sans' , 'Open+Sans|Open+Sans' ),
                  '_g_script_eb_garamond'         => array( 'EB Garamond' , 'EB+Garamond|EB+Garamond' ),
                  '_g_josephin'                   => array( 'Josefin Sans' , 'Josefin+Sans:700|Josefin+Sans:700' ),
                  '_g_droid'                      => array( 'Droid Serif' , 'Droid+Serif:400|Droid+Serif:400' ),
                  '_g_alice'                      => array( 'Alice' , 'Alice|Alice' ),
                  '_g_abel'                       => array( 'Abel' , 'Abel|Abel' ),
                  '_g_leckerli'                   => array( 'Leckerli One' , 'Leckerli+One|Leckerli+One' ),
                  '_g_allerta'                    => array( 'Allerta' , 'Allerta|Allerta' ),
                  '_g_sourcesanspro'              => array( 'Source Sans Pro' , 'Source+Sans+Pro|Source+Sans+Pro' ),
                  '_g_neuton'                     => array( 'Neuton' , 'Neuton|Neuton' ),
                  'helvetica_arial'               => array( 'Helvetica' , 'Helvetica Neue,Helvetica,Arial,sans-serif|Helvetica Neue,Helvetica,Arial,sans-serif' ),
                  'palatino'                      => array( 'Palatino Linotype' , 'Palatino Linotype,Book Antiqua,Palatino,serif|Palatino Linotype,Book Antiqua,Palatino,serif' ),
                  'verdana'                       => array( 'Verdana' , 'Verdana,Geneva,sans-serif|Verdana,Geneva,sans-serif' ),
                  'time_new_roman'                => array( 'Times New Roman' , 'Times New Roman,Times,serif|Times New Roman,Times,serif' ),
                  'courier_new'                   => array( 'Courier New' , 'Courier New,Courier New,Courier,monospace|Courier New,Courier New,Courier,monospace' )
                )
              )
            )
          );//end of font pairs

          $this -> font_selectors     = array(
            'titles' => implode(',' , apply_filters( 'tc-titles-font-selectors' , array('.site-title' , '.site-description', 'h1', 'h2', 'h3' ) ) ),
            'body'   => implode(',' , apply_filters( 'tc-body-font-selectors' , array('body' , '.navbar .nav>li>a') ) )
          );


          //Default featured pages ids
          $this -> fp_ids             = array( 'one' , 'two' , 'three' );

          //Default social networks
          $this -> socials            = array(
                                      'tc_rss'            => array(
                                                              'link_title'    => __( 'Subscribe to my rss feed' , 'customizr' ),
                                                              'option_label'  => __( 'RSS feed (default is the wordpress feed)' , 'customizr' ),
                                                              'default'       => get_bloginfo( 'rss_url' )
                                                            ),
                                      'tc_twitter'        => array(
                                                              'link_title'    => __( 'Follow me on Twitter' , 'customizr' ),
                                                              'option_label'  => __( 'Twitter profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_facebook'       => array(
                                                              'link_title'    => __( 'Follow me on Facebook' , 'customizr' ),
                                                              'option_label'  => __( 'Facebook profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_google'         => array(
                                                              'link_title'    => __( 'Follow me on Google+' , 'customizr' ),
                                                              'option_label'  => __( 'Google+ profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_instagram'      => array(
                                                              'link_title'    => __( 'Follow me on Instagram' , 'customizr' ),
                                                              'option_label'  => __( 'Instagram profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_tumblr'       => array(
                                                              'link_title'    => __( 'Follow me on Tumblr' , 'customizr' ),
                                                              'option_label'  => __( 'Tumblr url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_flickr'       => array(
                                                              'link_title'    => __( 'Follow me on Flickr' , 'customizr' ),
                                                              'option_label'  => __( 'Flickr url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_wordpress'      => array(
                                                              'link_title'    => __( 'Follow me on WordPress' , 'customizr' ),
                                                              'option_label'  => __( 'WordPress profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_youtube'        => array(
                                                              'link_title'    => __( 'Follow me on Youtube' , 'customizr' ),
                                                              'option_label'  => __( 'Youtube profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_pinterest'      => array(
                                                              'link_title'    => __( 'Pin me on Pinterest' , 'customizr' ),
                                                              'option_label'  => __( 'Pinterest profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_github'         => array(
                                                              'link_title'    => __( 'Follow me on Github' , 'customizr' ),
                                                              'option_label'  => __( 'Github profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_dribbble'       => array(
                                                              'link_title'    => __( 'Follow me on Dribbble' , 'customizr' ),
                                                              'option_label'  => __( 'Dribbble profile url' , 'customizr' ),
                                                              'default'       => null
                                                            ),
                                      'tc_linkedin'       => array(
                                                              'link_title'    => __( 'Follow me on LinkedIn' , 'customizr' ),
                                                              'option_label'  => __( 'LinkedIn profile url' , 'customizr' ),
                                                              'default'       => null
                                                            )
          );//end of social array


          //Default sidebar widgets
          $this -> sidebar_widgets    = array(
                                            'right'         => array(
                                                            'name'                 => __( 'Right Sidebar' , 'customizr' ),
                                                            'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
                                            ),
                                            'left'          => array(
                                                            'name'                 => __( 'Left Sidebar' , 'customizr' ),
                                                            'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
                                            )
          );//end of array

          //Default footer widgets
          $this -> footer_widgets     = array(
                                            'footer_one'    => array(
                                                            'name'                 => __( 'Footer Widget Area One' , 'customizr' ),
                                                            'description'          => __( 'Just use it as you want !' , 'customizr' )
                                            ),
                                            'footer_two'    => array(
                                                            'name'                 => __( 'Footer Widget Area Two' , 'customizr' ),
                                                            'description'          => __( 'Just use it as you want !' , 'customizr' )
                                            ),
                                            'footer_three'   => array(
                                                            'name'                 => __( 'Footer Widget Area Three' , 'customizr' ),
                                                            'description'          => __( 'Just use it as you want !' , 'customizr' )
                                            )
          );//end of array

          //Default post list layout
          $this -> post_list_layout   = array(
                                          'content'           => 'span8',
                                          'thumb'             => 'span4',
                                          'show_thumb_first'  => false,
                                          'alternate'         => true
          );

          //Defines post formats with no headers
          $this -> post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

          //Default 404 content
          $this -> content_404        = array(
                                          'quote'             => __( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ),
                                          'author'            => __( 'George Orwell' , 'customizr' ),
                                          'text'              => __( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' )
          );

          //Default no search result content
          $this -> content_no_results = array(
                                          'quote'             => __( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ),
                                          'author'            => __( 'Sir Winston Churchill' , 'customizr' ),
                                          'text'              => __( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' , 'customizr' )
          );

          //Default slides content
          $this -> default_slides     = array(
                                            1 => array(
                                              'title'         =>  '',
                                              'text'          =>  '',
                                              'button_text'   =>  '',
                                              'link_id'       =>  null,
                                              'link_url'      =>  null,
                                              'active'        =>  'active',
                                              'color_style'   =>  '',
                                              'slide_background'       =>  sprintf('<img width="1200" height="500" src="%1$s" class="" alt="%2$s" />',
                                                                          TC_BASE_URL.'inc/assets/img/customizr.jpg',
                                                                          __( 'Customizr is a clean responsive theme' , 'customizr' )
                                                                  )
                                            ),

                                            2 => array(
                                              'title'         =>  __( 'Style your WordPress site live!' , 'customizr' ),
                                              'text'          =>  __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' ),
                                              'button_text'   =>  __( 'Just try it!' , 'customizr' ),
                                              'link_id'       =>  null,
                                              'link_url'      =>  null,
                                              'active'        =>  '',
                                              'color_style'   =>  '',
                                              'slide_background'       =>  sprintf('<img width="1200" height="500" src="%1$s" class="" alt="%2$s" />',
                                                                          TC_BASE_URL.'inc/assets/img/phare.jpg',
                                                                          __( 'Style your WordPress site live!' , 'customizr' )
                                                                  )
                                            ),

                                            3 => array(
                                              'title'         =>  __( 'Create beautiful sliders' , 'customizr' ),
                                              'text'          =>  __( 'Customizr comes with a cool slider generator : add a slider to any post or page!' , 'customizr' ),
                                              'button_text'   =>  __( 'Discover the features' , 'customizr' ),
                                              'link_id'       =>  null,
                                              'link_url'      =>  null,
                                              'active'        =>  '',
                                              'color_style'   =>  '',
                                              'slide_background'       =>  sprintf('<img width="1200" height="500" src="%1$s" class="" alt="%2$s" />',
                                                                          TC_BASE_URL.'inc/assets/img/chevrolet.jpg',
                                                                          __( 'Create beautiful sliders' , 'customizr' )
                                                                  )
                                            )
          );///end of slides array

          //Set image options set by user @since v3.2.0
          //! must be included in utils to be available in admin for plugins like regenerate thumbnails
          add_action ( 'after_setup_theme'                      , array( $this, 'tc_set_user_defined_settings'), 10 );

          //adds the text domain, various theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails
          add_action ( 'after_setup_theme'                      , array( $this , 'tc_customizr_setup' ), 20 );

          //adds various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, ...)
          add_action ( 'after_setup_theme'                      , array( $this , 'tc_plugins_compatibility'), 30 );

          //adds retina support for high resolution devices
          add_filter ( 'wp_generate_attachment_metadata'        , array( $this , 'tc_add_retina_support') , 10 , 2 );
          add_filter ( 'delete_attachment'                      , array( $this , 'tc_clean_retina_images') );

          //adds classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter ('body_class'                              , array( $this , 'tc_set_body_classes') );

      }//end of constructor



      /**
      * Set user defined options for images
      * Thumbnail's height
      * Slider's height
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function tc_set_user_defined_settings() {
        $_options = get_option('tc_theme_options');
        //add "rectangular" image size
        if ( isset ( $_options['tc_post_list_thumb_shape'] ) && false !== strpos(esc_attr( $_options['tc_post_list_thumb_shape'] ), 'rectangular') ) {
          $_user_height     = ! esc_attr( $_options['tc_post_list_thumb_shape'] ) ? '250' : esc_attr( $_options['tc_post_list_thumb_height'] );
          $_rectangular_size    = apply_filters(
            'tc_rectangular_size' ,
            array( 'width' => '1170' , 'height' => $_user_height , 'crop' => true )
          );
          add_image_size( 'tc_rectangular_size' , $_rectangular_size['width'] , $_rectangular_size['height'], $_rectangular_size['crop'] );
        }

        if ( isset ( $_options['tc_slider_change_default_img_size'] ) && 0 != esc_attr( $_options['tc_slider_change_default_img_size'] ) ) {
            add_filter( 'tc_slider_full_size'    , array($this,  'tc_set_slider_img_height') );
            add_filter( 'tc_slider_size'         , array($this,  'tc_set_slider_img_height') );
        }
      }



      /**
      * Set slider new image sizes
      * Callback of slider_full_size and slider_size filters
      *
      * @package Customizr
      * @since Customizr 3.2.0
      *
      */
      function tc_set_slider_img_height( $_default_size ) {
        $_options = get_option('tc_theme_options');
        if ( 0 == $_options['tc_slider_default_height'] )
          return $_default_size;

        $_default_size['height'] = esc_attr( $_options['tc_slider_default_height'] );
        return $_default_size;
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
        if (! isset( $content_width ) )
          $content_width = apply_filters( 'tc_content_width' , 1170 );

        /*
         * Makes Customizr available for translation.
         * Translations can be added to the /inc/lang/ directory.
         */
        load_theme_textdomain( 'customizr' , TC_BASE . '/inc/lang' );

        /*
        * Customizr styles the visual editor to resemble the theme style,
        * Loads the editor-style specific (post formats and RTL), the active skin, the user style.css
        */
        add_editor_style( array( TC_BASE_URL.'inc/admin/css/editor-style.css', $this -> tc_active_skin() , get_stylesheet_uri() ) );

        /* Adds RSS feed links to <head> for posts and comments. */
        add_theme_support( 'automatic-feed-links' );

        /*  This theme supports nine post formats. */
        $post_formats   = apply_filters( 'tc_post_formats', array( 'aside' , 'gallery' , 'link' , 'image' , 'quote' , 'status' , 'video' , 'audio' , 'chat' ) );
        add_theme_support( 'post-formats' , $post_formats );

        /* support for page excerpt (added in v3.0.15) */
        add_post_type_support( 'page', 'excerpt' );

        /* This theme uses wp_nav_menu() in one location. */
        register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );

        /* This theme uses a custom image size for featured images, displayed on "standard" posts. */
        add_theme_support( 'post-thumbnails' );
          //set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

        /* @since v3.2.3 see : https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/ */
        add_theme_support( 'title-tag' );
        //remove theme support => generates notice in admin @todo fix-it!
         /* remove_theme_support( 'custom-background' );
          remove_theme_support( 'custom-header' );*/

        //post thumbnails for featured pages and post lists (archive, search, ...)
        $tc_thumb_size    = apply_filters( 'tc_thumb_size' , $this -> tc_thumb_size );
        add_image_size( 'tc-thumb' , $tc_thumb_size['width'] , $tc_thumb_size['height'], $tc_thumb_size['crop'] );

        //slider full width
        $slider_full_size = apply_filters( 'tc_slider_full_size' , $this -> slider_full_size );
        add_image_size( 'slider-full' , $slider_full_size['width'] , $slider_full_size['height'], $slider_full_size['crop'] );

        //slider boxed
        $slider_size      = apply_filters( 'tc_slider_size' , $this -> slider_size );
        add_image_size( 'slider' , $slider_size['width'] , $slider_size['height'], $slider_size['crop'] );

        //add support for svg and svgz format in media upload
        add_filter( 'upload_mimes'                        , array( $this , 'tc_custom_mtypes' ) );

        //add support for plugins (added in v3.1.0)
        add_theme_support( 'jetpack' );
        add_theme_support( 'bbpress' );
        add_theme_support( 'qtranslate' );
        add_theme_support( 'woocommerce' );

        //add help button to admin bar
        add_action ( 'wp_before_admin_bar_render'          , array( $this , 'tc_add_help_button' ));
      }



      /**
      * Returns the active path+skin.css
      *
      * @package Customizr
      * @since Customizr 3.0.15
      */
      function tc_active_skin() {
        $skin           = esc_attr( tc__f( '__get_option' , 'tc_skin' ) );
        $skin           = esc_attr( tc__f( '__get_option' , 'tc_minified_skin' ) ) ? str_replace('.css', '.min.css', $skin) : $skin;

        //Finds the good path : are we in a child theme and is there a skin to override?
        $remote_path    = false;
        $remote_path    = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/' . $skin) ) ? TC_BASE_URL_CHILD .'inc/assets/css/' : $remote_path ;
        $remote_path    = ( !$remote_path && file_exists(TC_BASE .'inc/assets/css/' . $skin) ) ? TC_BASE_URL .'inc/assets/css/' : $remote_path ;
        //Checks if there is a rtl version of the selected skin if needed
        if ( defined( 'WPLANG' ) && ( 'ar' == WPLANG || 'he_IL' == WPLANG ) ) {
          $remote_path   = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/rtl/' . $skin) ) ? TC_BASE_URL_CHILD .'inc/assets/css/rtl/' : $remote_path ;
          $remote_path   = ( !TC___::$instance -> tc_is_child() && file_exists(TC_BASE .'inc/assets/css/rtl/' . $skin) ) ? TC_BASE_URL .'inc/assets/css/rtl/' : $remote_path ;
        }

        //Defines the active skin and fallback to blue.css if needed
        $tc_active_skin  = $remote_path ? $remote_path.$skin : TC_BASE_URL.'inc/assets/css/blue3.css';
        return apply_filters ( 'tc_active_skin' , $tc_active_skin );
      }




      /**
     * This function handles the following plugins compatibility : Jetpack (for the carousel addon), Bbpress, Qtranslate, Woocommerce
     *
     * @package Customizr
     * @since Customizr 3.0.15
     */
      function tc_plugins_compatibility() {

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        /* JETPACK */
        //adds compatibilty with the jetpack image carousel
        if ( current_theme_supports( 'jetpack' ) && is_plugin_active('jetpack/jetpack.php') ) {
          add_filter( 'tc_gallery_bool', '__return_false' );
        }


        /* BBPRESS */
        //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
        if ( current_theme_supports( 'bbpress' ) && is_plugin_active('bbpress/bbpress.php') ) {
          //disables thumbnails and excerpt for post lists
          add_filter( 'tc_show_post_list_thumb', 'tc_bbpress_disable_thumbnail' );
          function tc_bbpress_disable_thumbnail($bool) {
             return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
          }
          add_filter( 'tc_show_post_list_excerpt', 'tc_bbpress_disable_excerpt' );
          function tc_bbpress_disable_excerpt($bool) {
             return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
          }

          //disables Customizr author infos on forums
          add_filter( 'tc_show_author_metas_in_post', 'tc_bbpress_disable_author_meta' );
          function tc_bbpress_disable_author_meta($bool) {
            return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
          }

          //disables post navigation
          add_filter( 'tc_show_post_navigation', 'tc_bbpress_disable_post_navigation' );
          function tc_bbpress_disable_post_navigation($bool) {
             return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
          }

          //disables post metas
          add_filter( 'tc_show_post_metas', 'tc_bbpress_disable_post_metas' );
          function tc_bbpress_disable_post_metas($bool) {
             return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
          }

        }//end if bbpress on



        /*
        * QTranslate
        * Credits : @acub, http://websiter.ro
        */
        if ( current_theme_supports( 'qtranslate' ) && is_plugin_active('qtranslate/qtranslate.php') ) {
          //outputs correct urls for current language : in logo, slider
          add_filter( 'tc_slide_link_url' , 'tc_url_lang' );
          add_filter( 'tc_logo_link_url' , 'tc_url_lang');
          add_filter( 'tc_fp_link_url' , 'tc_url_lang');
          function tc_url_lang($url) {
            return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
          }

          //outputs the qtranslate translation for slider, featured pages
          add_filter( 'tc_slide_title', 'tc_apply_qtranslate' );
          add_filter( 'tc_slide_text', 'tc_apply_qtranslate' );
          add_filter( 'tc_slide_button_text', 'tc_apply_qtranslate' );
          add_filter( 'tc_slide_background_alt', 'tc_apply_qtranslate' );
          add_filter( 'tc_fp_text', 'tc_apply_qtranslate' );
          add_filter( 'tc_fp_button_text', 'tc_apply_qtranslate' );
          function tc_apply_qtranslate ($text) {
            return call_user_func(  '__' , $text );
          }


          //sets no character limit for slider (title, lead text and button title) and featured pages (text) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
          add_filter( 'tc_slide_title_length'  , 'tc_remove_char_limit');
          add_filter( 'tc_slide_text_length'   , 'tc_remove_char_limit');
          add_filter( 'tc_slide_button_length' , 'tc_remove_char_limit');
          add_filter( 'tc_fp_text_length' , 'tc_remove_char_limit');
          function tc_remove_char_limit() {
            return 99999;
          }

          //modify the page excerpt=> uses the wp page excerpt instead of the generated excerpt with the_content
          add_filter( 'tc_fp_text', 'tc_use_page_excerpt', 10, 3 );
          function tc_use_page_excerpt( $featured_text , $fp_id , $page_id ) {
            $page = get_post($page_id);
            return ( empty($featured_text) && !post_password_required($page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
          }

          //modify the customizer transport from post message to null for some options
          add_filter( 'tc_featured_page_button_text_customizer_set' , 'tc_change_transport', 10, 2);
          add_filter( 'tc_featured_text_one_customizer_set' , 'tc_change_transport', 10, 2);
          add_filter( 'tc_featured_text_two_customizer_set' , 'tc_change_transport', 10, 2);
          add_filter( 'tc_featured_text_three_customizer_set' , 'tc_change_transport', 10, 2);
          function tc_change_transport( $value , $set ) {
            return ('transport' == $set) ? null : $value;
          }

        }//end Qtranslate



        /* Woocommerce */
        if ( current_theme_supports( 'woocommerce' ) && is_plugin_active('woocommerce/woocommerce.php') ) {

          //unkooks the default woocommerce wrappersv and add customizr's content wrapper and action hooks
          remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
          remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
          add_action('woocommerce_before_main_content', 'tc_woocommerce_wrappers', 10);
          add_action('woocommerce_after_main_content', 'tc_woocommerce_wrappers', 10);

          function tc_woocommerce_wrappers() {
            switch ( current_filter() ) {
              case 'woocommerce_before_main_content':

              ?>
                <div id="main-wrapper" class="<?php echo tc__f( 'tc_main_wrapper_classes' , 'container' ) ?>">

                <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

                <div class="container" role="main">
                    <div class="<?php echo tc__f( 'tc_column_content_wrapper_classes' , 'row column-content-wrapper' ) ?>">

                        <?php do_action( '__before_article_container'); ##hook of left sidebar?>

                            <div id="content" class="<?php echo tc__f( '__screen_layout' , tc__f ( '__ID' ) , 'class' ) ?> article-container">

                                <?php do_action ('__before_loop');##hooks the header of the list of post : archive, search... ?>
              <?php

                break;

              case 'woocommerce_after_main_content':

              ?>
                                <?php do_action ('__after_loop');##hook of the comments and the posts navigation with priorities 10 and 20 ?>

                            </div><!--.article-container -->

                        <?php do_action( '__after_article_container'); ##hook of left sidebar?>

                    </div><!--.row -->
                </div><!-- .container role: main -->

                <?php do_action( '__after_main_container' ); ?>

              </div><!--#main-wrapper"-->

              <?php
                break;
            }//end of switch on hook
            ?>
            <?php
          }//end of nested function


          //handles the woocomerce sidebar : removes action if sidebars not active
          if ( !is_active_sidebar( 'shop') ) {
            remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
          }


          //disables post navigation
          add_filter( 'tc_show_post_navigation', 'tc_woocommerce_disable_post_navigation' );
          function tc_woocommerce_disable_post_navigation($bool) {
             return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
          }


          //removes post comment action on after_loop hook
          add_filter( 'tc_show_comments', 'tc_woocommerce_disable_comments' );
          function tc_woocommerce_disable_comments($bool) {
             return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
          }

          //changes customizr meta boxes priority (slider and layout not on top) if displaying woocommerce products in admin
          add_filter( 'tc_post_meta_boxes_priority', 'tc_woocommerce_change_meta_boxes_priority' , 2 , 10 );
          function tc_woocommerce_change_meta_boxes_priority($priority , $screen) {
             return ( 'product' == $screen ) ? 'default' : $priority ;
          }

        }//end if woocommerce

      }//end of plugin compatibility function




      /**
      * Returns the $mimes array with svg and svgz entries added
      *
      * @package Customizr
      * @since Customizr 3.1.19
      */
      function tc_custom_mtypes( $mimes ) {
        if (! apply_filters( 'tc_add_svg_mime_type' , true ) )
          return $mimes;

        $mimes['svg']   = 'image/svg+xml';
        $mimes['svgz']  = 'image/svg+xml';
        return $mimes;
      }



      /**
     * This function handles the support for high resolution devices
     *
     * @hook wp_generate_attachment_metadata (10 ,2)
     * @package Customizr
     * @since Customizr 3.0.15
     * @credits http://wp.tutsplus.com/author/chrisbavota/
     */
      function tc_add_retina_support( $metadata, $attachment_id ) {
        //checks if retina is enabled in options
        if ( 0 == tc__f( '__get_option' , 'tc_retina_support' ) )
          return $metadata;

        if ( ! is_array($metadata) )
          return $metadata;

        //Create the retina image for the main file
        if ( is_array($metadata) && isset($metadata['width']) && isset($metadata['height']) )
          $this -> tc_create_retina_images( get_attached_file( $attachment_id ), $metadata['width'], $metadata['height'] , false, $_is_intermediate = false );

        //Create the retina images for each WP sizes
        foreach ( $metadata as $key => $data ) {
            if ( 'sizes' != $key )
              continue;
            foreach ( $data as $_size_name => $_attr ) {
                if ( is_array( $_attr ) && isset($_attr['width']) && isset($_attr['height']) )
                    $this -> tc_create_retina_images( get_attached_file( $attachment_id ), $_attr['width'], $_attr['height'], true, $_is_intermediate = true );
            }
        }
        return $metadata;
      }//end of tc_retina_support



      /**
      * Creates retina-ready images
      *
      * @package Customizr
      * @since Customizr 3.0.15
      * @credits http://wp.tutsplus.com/author/chrisbavota/
      */
      function tc_create_retina_images( $file, $width, $height, $crop = false , $_is_intermediate = true) {
          $resized_file = wp_get_image_editor( $file );
          if ( is_wp_error( $resized_file ) )
            return false;

          if ( $width || $height ) {
            $_suffix    = $_is_intermediate ? $width . 'x' . $height . '@2x' : '@2x';
            $filename   = $resized_file -> generate_filename( $_suffix );
            // if is not intermediate (main file name) => removes the "-" added by the generate_filename method
            $filename   = ! $_is_intermediate ? str_replace('-@2x', '@2x', $filename) : $filename;

            $resized_file -> resize( $width * 2, $height * 2, $crop );
            $resized_file -> save( $filename );

            $info = $resized_file -> get_size();

            /*return array(
                'file' => wp_basename( $filename ),
                'width' => $info['width'],
                'height' => $info['height'],
            );*/
          }
          //return false;
      }//end of function




      /**
     * This function deletes the generated retina images if they exist
     *
     * @hook delete_attachment
     * @package Customizr
     * @since Customizr 3.0.15
     * @credits http://wp.tutsplus.com/author/chrisbavota/
     */
      function tc_clean_retina_images( $attachment_id ) {
        //checks if retina is enabled in options
        if ( 0 == tc__f( '__get_option' , 'tc_retina_support' ) )
          return;

        $meta = wp_get_attachment_metadata( $attachment_id );
        if ( !isset( $meta['file']) )
          return;

        $upload_dir = wp_upload_dir();
        $path = pathinfo( $meta['file'] );
        foreach ( $meta as $key => $value ) {
            if ( 'sizes' === $key ) {
                foreach ( $value as $sizes => $size ) {
                    $original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
                    $retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );
                    if ( file_exists( $retina_filename ) )
                        unlink( $retina_filename );
                }
            }
        }
      }//end of function



      /**
      * Add help button
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_add_help_button() {
         if ( current_user_can( 'edit_theme_options' ) ) {
           global $wp_admin_bar;
           $wp_admin_bar->add_menu( array(
             'parent' => 'top-secondary', // Off on the right side
             'id' => 'tc-customizr-help' ,
             'title' =>  __( 'Help' , 'customizr' ),
             'href' => admin_url( 'themes.php?page=welcome.php&help=true' ),
             'meta'   => array(
                'title'  => __( 'Need help with Customizr? Click here!', 'customizr' ),
              ),
           ));
         }
      }



      /**
      * Add a class on the body element.
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_body_classes( $_classes ) {
        $_to_add = array();
        if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_link_hover_effect' ) ) )
          $_to_add[] = 'tc-fade-hover-links';
        if ( TC_utils::$instance -> tc_is_customizing() )
          $_to_add[] = 'is-customizing';
        if ( wp_is_mobile() )
          $_to_add[] = 'tc-is-mobile';
        return array_merge( $_classes , $_to_add );
      }

  }//end of class
endif;