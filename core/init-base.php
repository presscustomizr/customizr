<?php
if ( ! class_exists( 'CZR_BASE' ) ) :

  class CZR_BASE {
        //Access any method or var of the class with classname::$instance -> var or method():
        public static $instance;

        static $default_options;
        static $db_options;
        static $options;//not used in customizer context only

        static $customizer_map = array();
        static $theme_setting_list;

        static $theme_name;

        public $skin_classic_color_map;
        public $old_socials;
        public $font_pairs;
        public $fp_ids;
        public $sidebar_widgets;
        public $default_slides;

        public $tc_thumb_size;
        public $slider_full_size;
        public $slider_size;

        public $tc_grid_size;
        public $tc_grid_full_size;

        //print comments template once : plugins compatibility
        public static $comments_rendered = false;

        function __construct( $_args = array()) {
            //init properties
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_init_properties') );

            //Set image options set by user @since v3.2.0
            //! must be available in admin for plugins like regenerate thumbnails
            add_action( 'after_setup_theme'       , array( $this, 'czr_fn_set_user_defined_settings'));


            //add the text domain, various theme supports : editor style, automatic-feed-links, post formats, post-thumbnails
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_base_customizr_setup' ) );

            //IMPORTANT : this callback needs to be ran AFTER czr_fn_init_properties.
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_cache_theme_setting_list' ), 100 );

            //refresh the theme options right after the _preview_filter when previewing
            add_action( 'customize_preview_init'  , array( $this , 'czr_fn_customize_refresh_db_opt' ) );

            //modify the query with pre_get_posts
            //! wp_loaded is fired after WordPress is fully loaded but before the query is set
            // => before modern style implementation, was previously set in inc/_dev/class-content-post_list.php and core/class-fire-init.php
            add_action( 'wp_loaded'               , array( $this, 'czr_fn_set_early_hooks') );

            //Javascript detection
            add_action( 'wp_head'                 , array( $this, 'czr_fn_javascript_detection'), 0 );


            //registers the menus
            add_action( 'after_setup_theme'       , array( $this, 'czr_fn_register_menus'));

            //add retina support for high resolution devices
            add_filter( 'wp_generate_attachment_metadata'        , array( $this , 'czr_fn_add_retina_support') , 10 , 2 );
            add_filter( 'delete_attachment'                      , array( $this , 'czr_fn_clean_retina_images') );

            //prevent rendering the comments template more than once
            add_filter( 'tc_render_comments_template'            , array( $this,  'czr_fn_control_coments_template_rendering' ) );

            //Default images sizes
            $this -> tc_thumb_size      = array( 'width' => 270 , 'height' => 250, 'crop' => true ); //size name : tc-thumb
            $this -> slider_full_size   = array( 'width' => 9999 , 'height' => 500, 'crop' => true ); //size name : slider-full

            //The actual bootstrap4 container width is 1110, while it was 1170 in bootstrap2
            $this -> slider_size        = array( 'width' => CZR_IS_MODERN_STYLE ? 1110 : 1170 , 'height' => 500, 'crop' => true ); //size name : slider

            $this -> tc_grid_size       = array( 'width' => 570 , 'height' => 350, 'crop' => true ); //size name : tc-grid
            //Default images sizes
            $this -> tc_grid_full_size  = array( 'width' => CZR_IS_MODERN_STYLE ? 1110 : 1170 , 'height' => CZR_IS_MODERN_STYLE ? 444 : 350, 'crop' => true ); //size name : tc-grid-full

            //Main skin color array : array( link color, link hover color )
            $this -> skin_classic_color_map     = apply_filters( 'tc_skin_color_map' , array(
                  'blue.css'        =>  array( '#08c', '#005580' ),
                  'blue2.css'       =>  array( '#27CBCD', '#1b8b8d' ),
                  'blue3.css'       =>  array( '#27CDA5', '#1b8d71' ),
                  'green.css'       =>  array( '#9db668', '#768d44' ),
                  'green2.css'      =>  array( '#26CE61', '#1a8d43' ),
                  'yellow.css'      =>  array( '#e9a825', '#b07b12' ),
                  'yellow2.css'     =>  array( '#d2d62a', '#94971d' ),
                  'orange.css'      =>  array( '#F78C40', '#e16309' ),
                  'orange2.css'     =>  array( '#E79B5D', '#d87220' ),
                  'red.css'         =>  array( '#e10707', '#970505' ),
                  'red2.css'        =>  array( '#e7797a', '#db383a' ),
                  'purple.css'      =>  array( '#e67fb9', '#da3f96' ),
                  'purple2.css'     =>  array( '#8183D8', '#474ac6' ),
                  'grey.css'        =>  array( '#5A5A5A', '#343434' ),
                  'grey2.css'       =>  array( '#E4E4E4', '#bebebe' ),
                  'black.css'       =>  array( '#000', '#000000' ),
                  'black2.css'      =>  array( '#394143', '#16191a' )
            ) );


            //Default featured pages ids
            $this -> fp_ids             = array( 'one' , 'two' , 'three' );


            //Default sidebar widgets
            $this -> sidebar_widgets    = array(
              'left'          => array(
                              'name'                 => __( 'Left Sidebar' , 'customizr' ),
                              'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
              ),
              'right'         => array(
                              'name'                 => __( 'Right Sidebar' , 'customizr' ),
                              'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
              )
            );//end of array


            //Default social networks
            $this -> old_socials            = array(
              'tc_rss'            => array(
                                      'link_title'    => __( 'Subscribe to my rss feed' , 'customizr' ),
                                      'default'       => get_bloginfo( 'rss_url' ) //kept as it's the only one used in the transition
                                  ),
              'tc_email'          => array(
                                      'link_title'    => __( 'E-mail' , 'customizr' ),
                                    ),
              'tc_twitter'        => array(
                                      'link_title'    => __( 'Follow me on Twitter' , 'customizr' ),
                                    ),
              'tc_facebook'       => array(
                                      'link_title'    => __( 'Follow me on Facebook' , 'customizr' ),
                                    ),
              'tc_google'         => array(
                                      'link_title'    => __( 'Follow me on Google+' , 'customizr' ),
                                    ),
              'tc_instagram'      => array(
                                      'link_title'    => __( 'Follow me on Instagram' , 'customizr' ),
                                    ),
              'tc_tumblr'       => array(
                                      'link_title'    => __( 'Follow me on Tumblr' , 'customizr' ),
                                    ),
              'tc_flickr'       => array(
                                      'link_title'    => __( 'Follow me on Flickr' , 'customizr' ),
                                    ),
              'tc_wordpress'      => array(
                                      'link_title'    => __( 'Follow me on WordPress' , 'customizr' ),
                                    ),
              'tc_youtube'        => array(
                                      'link_title'    => __( 'Follow me on Youtube' , 'customizr' ),
                                    ),
              'tc_pinterest'      => array(
                                      'link_title'    => __( 'Pin me on Pinterest' , 'customizr' ),
                                    ),
              'tc_github'         => array(
                                      'link_title'    => __( 'Follow me on Github' , 'customizr' ),
                                    ),
              'tc_dribbble'       => array(
                                      'link_title'    => __( 'Follow me on Dribbble' , 'customizr' ),
                                    ),
              'tc_linkedin'       => array(
                                      'link_title'    => __( 'Follow me on LinkedIn' , 'customizr' ),
                                    ),
              'tc_vk'             => array(
                                      'link_title'    => __( 'Follow me on VKontakte' , 'customizr' ),
                                    ),
              'tc_yelp'           => array(
                                      'link_title'    => __( 'Follow me on Yelp' , 'customizr' ),
                                    ),
              'tc_xing'           => array(
                                      'link_title'    => __( 'Follow me on Xing' , 'customizr' ),
                                    ),
              'tc_snapchat'       => array(
                                      'link_title'    => __( 'Contact me on Snapchat' , 'customizr' ),
                                    )
            );//end of social array

            //Default fonts pairs
            $this -> font_pairs             = array(
              'gfont' => array(
                'name'  => __('Google fonts pairs' , 'customizr'),
                'list'  => apply_filters( 'tc_gfont_pairs' , array(
                  '_g_sintony_poppins'              => array( 'Sintony &amp; Poppins' , 'Sintony|Poppins' ),
                  '_g_fjalla_cantarell'              => array( 'Fjalla One &amp; Cantarell' , 'Fjalla+One:400|Cantarell:400' ),
                  '_g_lobster_raleway'               => array( 'Lobster &amp; Raleway' , 'Lobster:400|Raleway' ),
                  '_g_alegreya_roboto'               => array( 'Alegreya &amp; Roboto' , 'Alegreya:700|Roboto' ),
                  '_g_lato_grand_hotel'              => array( 'Lato &amp; Grand Hotel', 'Lato:400|Grand+Hotel' ),
                  '_g_dosis_opensans'                => array( 'Dosis &amp; Open Sans' , 'Dosis:400|Open+Sans' ),
                  '_g_dancing_script_eb_garamond'    => array( 'Dancing Script &amp; EB Garamond' , 'Dancing+Script:700|EB+Garamond' ),
                  '_g_amatic_josephin'               => array( 'Amatic SC &amp; Josefin Sans' , 'Amatic+SC|Josefin+Sans:700' ),
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
                    '_g_poppins'                    => array( 'Poppins' , 'Poppins|Poppins' ),
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
                  'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                              TC_BASE_URL.'assets/front/img/customizr-theme.jpg',
                                              __( 'Customizr is a clean responsive theme' , 'customizr' )
                                      )
                ),

                2 => array(
                  'title'         =>  '',
                  'text'          =>  '',
                  'button_text'   =>  '',
                  'link_id'       =>  null,
                  'link_url'      =>  null,
                  'active'        =>  '',
                  'color_style'   =>  '',
                  'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                              TC_BASE_URL.'assets/front/img/demo_slide_2.jpg',
                                              __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                      )
                )
            );///end of slides array
        }//construct








        /**
         * Sets up theme defaults and registers the various WordPress features
         * hook : after_setup_theme | 20
         *
         */
        function czr_fn_base_customizr_setup() {
            /* Set default content width for post images and media. */
            global $content_width;
            if (! isset( $content_width ) ) {
                $content_width = apply_filters( 'czr_content_width' , CZR_IS_MODERN_STYLE ? 1140 : 1170 );
            }

            /*
             * Makes Customizr available for translation.
             * Translations can be added to the /inc/lang/ directory.
             */
            load_theme_textdomain( 'customizr' , czr_fn_is_pro() ? TC_BASE . '/inc/lang_pro' : TC_BASE . '/inc/lang' );

            /* Adds RSS feed links to <head> for posts and comments. */
            add_theme_support( 'automatic-feed-links' );

            /*  This theme supports nine post formats. */
            $post_formats   = apply_filters( 'tc_post_formats', array( 'aside' , 'gallery' , 'link' , 'image' , 'quote' , 'status' , 'video' , 'audio' , 'chat' ) );
            add_theme_support( 'post-formats' , $post_formats );

            /* support for page excerpt (added in v3.0.15) */
            add_post_type_support( 'page', 'excerpt' );

            /* This theme uses a custom image size for featured images, displayed on "standard" posts. */
            add_theme_support( 'post-thumbnails' );
              //set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

            /* @since v3.2.3 see : https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/ */
            add_theme_support( 'title-tag' );

            //remove theme support => generates notice in admin @todo fix-it!
             /* remove_theme_support( 'custom-background' );
              remove_theme_support( 'custom-header' );*/

            //post thumbnails for featured pages and post lists (archive, search, ...)
            $tc_thumb_size    = apply_filters( 'tc_thumb_size' , CZR___::$instance -> tc_thumb_size );
            add_image_size( 'tc-thumb' , $tc_thumb_size['width'] , $tc_thumb_size['height'], $tc_thumb_size['crop'] );

            //slider full width
            $slider_full_size = apply_filters( 'tc_slider_full_size' , CZR___::$instance -> slider_full_size );
            add_image_size( 'slider-full' , $slider_full_size['width'] , $slider_full_size['height'], $slider_full_size['crop'] );

            //slider boxed
            $slider_size      = apply_filters( 'tc_slider_size' , CZR___::$instance -> slider_size );
            add_image_size( 'slider' , $slider_size['width'] , $slider_size['height'], $slider_size['crop'] );


            //thumbs defined only for the modern style
            if ( CZR_IS_MODERN_STYLE ) {
              /*
              * Do we want these to be filterable?
              * I don't think som as we want this aspect ratio to be preserved!
              */
              //square thumb used in post list alternate for standard posts and regular shape
              //also used in related posts
              $tc_sq_thumb_size = apply_filters( 'tc_square_thumb_size' , CZR() -> tc_sq_thumb_size );
              add_image_size( 'tc-sq-thumb' , $tc_sq_thumb_size['width'] , $tc_sq_thumb_size['height'], $tc_sq_thumb_size['crop'] );

              //wide screen thumb (16:9) used in post list alternate for image and galleries post formats
              $tc_ws_thumb_size = apply_filters( 'tc_ws_thumb_size' , CZR() -> tc_ws_thumb_size );
              add_image_size( 'tc-ws-thumb' , $tc_ws_thumb_size['width'] , $tc_ws_thumb_size['height'], $tc_ws_thumb_size['crop'] );

              //wide screen small thumb (16:9)
              //used by wp as responsive image of tc-ws-thumb
              $tc_ws_small_thumb_size = apply_filters( 'tc_ws_small_thumb_size' , CZR() -> tc_ws_small_thumb_size );
              add_image_size( 'tc-ws-small-thumb' , $tc_ws_small_thumb_size['width'] , $tc_ws_small_thumb_size['height'], $tc_ws_small_thumb_size['crop'] );

              //used by wp as responsive image of tc-slider tc-slider-full
              $tc_slider_small_size = apply_filters( 'tc_slider_small_size' , CZR() -> tc_slider_small_size  );
              add_image_size( 'tc-slider-small' , $tc_slider_small_size['width'] , $tc_slider_small_size['height'], $tc_slider_small_size['crop'] );

            }

            //add support for svg and svgz format in media upload
            add_filter( 'upload_mimes'                        , array( $this , 'czr_fn_custom_mtypes' ) );

            //add help button to admin bar
            add_action ( 'wp_before_admin_bar_render'         , array( $this , 'czr_fn_add_help_button' ));


            // Add theme support for selective refresh for widgets.
            // Only add if the link manager is not enabled
            // cf WP core ticket #39451
            if ( ! get_option( 'link_manager_enabled' ) ) {
              add_theme_support( 'customize-selective-refresh-widgets' );
            }
        }













        /*
        * hook : after_setup_theme
        */
        function czr_fn_register_menus() {
            /* This theme uses wp_nav_menu() in one location. */
            register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );
            register_nav_menu( 'secondary' , __( 'Secondary (horizontal) Menu' , 'customizr' ) );
            if ( CZR_IS_MODERN_STYLE ) {
              register_nav_menu( 'topbar' , __( 'Topnav (horizontal) Menu' , 'customizr' ) );
              register_nav_menu( 'mobile' , __( 'Mobile Menu' , 'customizr' ) );
            }
        }









      /**
      * Returns the $mimes array with svg and svgz entries added
      *
      */
      function czr_fn_custom_mtypes( $mimes ) {
        if (! apply_filters( 'tc_add_svg_mime_type' , true ) )
          return $mimes;

        $mimes['svg']   = 'image/svg+xml';
        $mimes['svgz']  = 'image/svg+xml';
        return $mimes;
      }






      /**
      * Add help button
      */
      function czr_fn_add_help_button() {
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
       * This function handles the support for high resolution devices
       *
       * @hook wp_generate_attachment_metadata (10 ,2)
       * @package Customizr
       * @since Customizr 3.0.15
       * @credits http://wp.tutsplus.com/author/chrisbavota/
       */
        function czr_fn_add_retina_support( $metadata, $attachment_id ) {
          //checks if retina is enabled in options
          if ( 0 == czr_fn_opt( 'tc_retina_support' ) )
            return $metadata;

          if ( ! is_array($metadata) )
            return $metadata;

          //Create the retina image for the main file
          if ( is_array($metadata) && isset($metadata['width']) && isset($metadata['height']) )
            $this -> czr_fn_create_retina_images( get_attached_file( $attachment_id ), $metadata['width'], $metadata['height'] , false, $_is_intermediate = false );

          //Create the retina images for each WP sizes
          foreach ( $metadata as $key => $data ) {
              if ( 'sizes' != $key )
                continue;
              foreach ( $data as $_size_name => $_attr ) {
                  if ( is_array( $_attr ) && isset($_attr['width']) && isset($_attr['height']) )
                      $this -> czr_fn_create_retina_images( get_attached_file( $attachment_id ), $_attr['width'], $_attr['height'], true, $_is_intermediate = true );
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
        function czr_fn_create_retina_images( $file, $width, $height, $crop = false , $_is_intermediate = true) {
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
        function czr_fn_clean_retina_images( $attachment_id ) {
          $meta = wp_get_attachment_metadata( $attachment_id );
          if ( !isset( $meta['file']) )
            return;

          $upload_dir = wp_upload_dir();
          $path = pathinfo( $meta['file'] );
          $sizes = $meta['sizes'];
          // append to the sizes the original file
          $sizes['original'] = array( 'file' => $path['basename'] );

          foreach ( $sizes as $size ) {
            $original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
            $retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );

            if ( file_exists( $retina_filename ) )
              unlink( $retina_filename );
          }
        }//end of function

















        /**
        * Controls the rendering of the comments template
        *
        * @param bool $bool
        * @return bool $bool
        * hook : tc_render_comments_template
        *
        */
        function czr_fn_control_coments_template_rendering( $bool ) {
            $_to_return = !self::$comments_rendered && $bool;
            self::$comments_rendered = true;
            return $_to_return;
        }
















        /**
        * Set __loop hooks and various filters based on customizer options
        * hook : wp_loaded
        *
        */
        function czr_fn_set_early_hooks() {
            //Filter home/blog postsa (priority 9 is to make it act before the grid hook for expanded post)
            add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_filter_home_blog_posts_by_tax' ), 9);
            //Include attachments in search results
            add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_include_attachments_in_search' ));
            //Include all post types in archive pages
            add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_include_cpt_in_lists' ));
        }



        /**
        * hook : pre_get_posts
        * Filter home/blog posts by tax: cat
        * @return modified query object
        * @package Customizr
        * @since Customizr 3.4.10
        */
        function czr_fn_filter_home_blog_posts_by_tax( $query ) {
            // when we have to filter?
            // in home and blog page
            if ( ! $query->is_main_query()
              || ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $query->is_posts_page )
            )
              return;

            //temp: do not filter in classic style when classic grid enabled and infinite scroll enabled in home/blog
            if ( ! CZR_IS_MODERN_STYLE &&
              'grid'== esc_attr( czr_fn_opt( 'tc_post_list_grid' ) ) &&
               class_exists( 'PC_init_infinite' ) && esc_attr( czr_fn_opt( 'tc_infinite_scroll' ) ) && esc_attr( czr_fn_opt( 'tc_infinite_scroll_in_home' ) ) )
            return;

            // categories
            // we have to ignore sticky posts (do not prepend them)
            // disable grid sticky post expansion
            $cats = czr_fn_opt('tc_blog_restrict_by_cat');
            $cats = array_filter( $cats, 'czr_fn_category_id_exists' );

            if ( is_array( $cats ) && ! empty( $cats ) ){
               $query->set('category__in', $cats );
               $query->set('ignore_sticky_posts', 1 );
               add_filter('tc_grid_expand_featured', '__return_false');
            }
        }


        /**
        * hook : pre_get_posts
        * Includes attachments in search results
        * @return modified query object
        * @package Customizr
        * @since Customizr 3.0.10
        */
        function czr_fn_include_attachments_in_search( $query ) {
            if (! is_search() || ! apply_filters( 'tc_include_attachments_in_search_results' , false ) )
              return;

            // add post status 'inherit'
            $post_status = $query->get( 'post_status' );
            if ( ! $post_status || 'publish' == $post_status )
              $post_status = array( 'publish', 'inherit' );
            if ( is_array( $post_status ) )
              $post_status[] = 'inherit';

            $query->set( 'post_status', $post_status );
        }


        /**
        * hook : pre_get_posts
        * Includes Custom Posts Types (set to public and excluded_from_search_result = false) in archives and search results
        * In archives, it handles the case where a CPT has been registered and associated with an existing built-in taxonomy like category or post_tag
        * @return modified query object
        * @package Customizr
        * @since Customizr 3.1.20
        */
        function czr_fn_include_cpt_in_lists( $query ) {
          if (
              is_admin()
              || ! $query->is_main_query()
              || ! apply_filters('tc_include_cpt_in_archives' , false)
              || ! ( $query->is_search || $query->is_archive )
              )
              return;

            //filter the post types to include, they must be public and not excluded from search
            //we also exclude the built-in types, to exclude pages and attachments, we'll add standard posts later
            $post_types         = get_post_types( array( 'public' => true, 'exclude_from_search' => false, '_builtin' => false) );

            //add standard posts
            $post_types['post'] = 'post';
            if ( $query -> is_search ){
              // add standard pages in search results => new wp behavior
              $post_types['page'] = 'page';
              // allow attachments to be included in search results by tc_include_attachments_in_search method
              if ( apply_filters( 'tc_include_attachments_in_search_results' , false ) )
                $post_types['attachment'] = 'attachment';
            }

            // add standard pages in search results
            $query->set('post_type', $post_types );
        }
















        /**
        * Set user defined options for images
        * Thumbnail's height
        * Slider's height
        * hook : after_setup_theme
        *
        */
        function czr_fn_set_user_defined_settings() {
            $_options = get_option('tc_theme_options');

            if ( isset ( $_options['tc_slider_change_default_img_size'] ) && 0 != esc_attr( $_options['tc_slider_change_default_img_size'] ) && isset ( $_options['tc_slider_default_height'] ) && 500 != esc_attr( $_options['tc_slider_default_height'] ) ) {
                add_filter( 'tc_slider_full_size'          , array($this,  'czr_fn_set_slider_img_height') );
                add_filter( 'tc_slider_size'               , array($this,  'czr_fn_set_slider_img_height') );

                //ONLY FOR MODERN STYLE
                if ( CZR_IS_MODERN_STYLE ) {
                    add_filter( 'tc_slider_small_size'         , array($this,  'czr_fn_set_slider_small_img_height') );
                }
            }

            $tc_grid_full_size     = $this -> tc_grid_full_size;
            $tc_grid_size          = $this -> tc_grid_size;

            //ONLY FOR CLASSICAL STYLE
            if ( ! CZR_IS_MODERN_STYLE ) {
                //add "rectangular" image size
                if ( isset ( $_options['tc_post_list_thumb_shape'] ) && false !== strpos(esc_attr( $_options['tc_post_list_thumb_shape'] ), 'rectangular') ) {
                  $_user_height     = isset ( $_options['tc_post_list_thumb_height'] ) ? esc_attr( $_options['tc_post_list_thumb_height'] ) : '250';
                  $_user_height     = ! esc_attr( $_options['tc_post_list_thumb_shape'] ) ? '250' : $_user_height;
                  $_rectangular_size    = apply_filters(
                    'tc_rectangular_size' ,
                    array( 'width' => '1170' , 'height' => $_user_height , 'crop' => true )
                  );
                  add_image_size( 'tc_rectangular_size' , $_rectangular_size['width'] , $_rectangular_size['height'], $_rectangular_size['crop'] );
                }


                /***********
                *** GRID ***
                ***********/
                if ( isset( $_options['tc_grid_thumb_height'] ) ) {
                    $_user_height  = esc_attr( $_options['tc_grid_thumb_height'] );
                }

                $_user_grid_height     = isset( $_options['tc_grid_thumb_height'] ) && is_numeric( $_options['tc_grid_thumb_height'] ) ? esc_attr( $_options['tc_grid_thumb_height'] ) : $tc_grid_full_size['height'];

                add_image_size( 'tc-grid-full', $tc_grid_full_size['width'], $_user_grid_height, $tc_grid_full_size['crop'] );
                add_image_size( 'tc-grid', $tc_grid_size['width'], $_user_grid_height, $tc_grid_size['crop'] );

                if ( $_user_grid_height != $tc_grid_full_size['height'] )
                  add_filter( 'tc_grid_full_size', array( $this,  'czr_fn_set_grid_img_height') );
                if ( $_user_grid_height != $tc_grid_size['height'] )
                  add_filter( 'tc_grid_size'     , array( $this,  'czr_fn_set_grid_img_height') );
            }
            else {
              //Modern style: not custom height option available
              add_image_size( 'tc-grid-full', $tc_grid_full_size['width'], $tc_grid_full_size['height'], $tc_grid_full_size['crop'] );
              add_image_size( 'tc-grid', $tc_grid_size['width'], $tc_grid_size['height'], $tc_grid_size['crop'] );

            }

        }


        /**
        * Set slider new image sizes
        * Callback of slider_full_size and slider_size filters
        * hook : might be called from after_setup_theme
        * @package Customizr
        * @since Customizr 3.2.0
        *
        */
        function czr_fn_set_slider_img_height( $_default_size ) {
            $_options = get_option('tc_theme_options');

            $_default_size['height'] = esc_attr( $_options['tc_slider_default_height'] );

            return $_default_size;
        }


        /**
        * Set post list desgin new image sizes
        * Callback of tc_grid_full_size and tc_grid_size filters
        *
        * @package Customizr
        * @since Customizr 3.1.12
        *
        */
        function czr_fn_set_grid_img_height( $_default_size ) {
            $_options = get_option('tc_theme_options');

            $_default_size['height'] =  esc_attr( $_options['tc_grid_thumb_height'] ) ;
            return $_default_size;
        }



        /*
        * Slider small thumbs
        */
        //@hook 'tc_slider_small_size'
        function czr_fn_set_slider_small_img_height( $_default_size ) {

            $_options                     = get_option('tc_theme_options');

            //original slider size
            $_slider_size                 = CZR() -> slider_size;
            $_custom_height               = esc_attr( $_options['tc_slider_default_height'] );



            if ( isset( $_slider_size[ 'height'] ) && $_slider_size[ 'height'] != 0 ) {

                $_default_size['height']  = $_default_size['height'] * $_custom_height /  $_slider_size[ 'height' ];

            }

            return $_default_size;

        }






        /**
        * Init CZR_utils class properties after_setup_theme
        * Fixes the bbpress bug : Notice: bbp_setup_current_user was called incorrectly. The current user is being initialized without using $wp->init()
        * czr_fn_get_default_options uses is_user_logged_in() => was causing the bug
        *
        * CZR_THEME_OPTIONS, CUSTOMIZR_VER, CZR_IS_PRO is defined by the child classes before "after_setup_theme"
        *
        * hook : after_setup_theme
        *
        * @package Customizr
        * @since Customizr 3.2.3
        */
        function czr_fn_init_properties() {
              //fire an action hook before theme main properties have been set up
              // theme_name
              // db_options
              // default_options
              // started using customizr(-pro) transient
              do_action( 'czr_before_caching_options' );

              self::$theme_name         = CZR_SANITIZED_THEMENAME;
              self::$db_options         = false === get_option( CZR_THEME_OPTIONS ) ? array() : (array)get_option( CZR_THEME_OPTIONS );
              self::$default_options    = czr_fn_get_default_options();
              $_trans                   = CZR_IS_PRO ? 'started_using_customizr_pro' : 'started_using_customizr';

              //What was the theme version when the user started to use Customizr?
              //new install = no options yet
              //very high duration transient, this transient could actually be an option but as per the themes guidelines, too much options are not allowed.
              $is_customizr_free_or_pro_fresh_install = 1 >= count( self::$db_options );

              if ( $is_customizr_free_or_pro_fresh_install ) {
                  set_transient(
                      $_trans,
                      sprintf('%s|%s' , 'with', CUSTOMIZR_VER ),
                      60*60*24*9999
                  );
              }
              //it can be a fresh install of the pro because the free options are not enough to check
              else if ( ! esc_attr( get_transient( $_trans ) ) ) {
                  //this might be :
                  //1) a free user updating to pro => with
                  //2) a free user updating and has cleaned transient (edge case but possible ) => before
                  //3) a pro user updating and has cleaned transient ( edge also ) => before
                  //How do make the difference between 1) and ( 2 or 3 )
                  //=> we need something written by the pro => the last update notice in options
                  if ( CZR_IS_PRO ) {
                      $is_already_pro_user = array_key_exists( 'last_update_notice_pro', self::$db_options );
                      $is_pro_fresh_install = ! $is_already_pro_user;
                      if ( $is_already_pro_user ) {
                          $pro_infos = self::$db_options['last_update_notice_pro'];
                          $is_pro_fresh_install = is_array( $pro_infos ) && array_key_exists( 'version', $pro_infos ) && $pro_infos['version'] == CUSTOMIZR_VER;
                      }
                      $user_starter_with_this_version = $is_pro_fresh_install;
                      if ( $is_already_pro_user && ! $is_pro_fresh_install ) {
                        $user_starter_with_this_version = false;
                      }

                      //if already pro user, we are in the case of the transient that have been cleaned in db
                      //if not, then it's a free user upgrading to pro
                      set_transient(
                          $_trans,
                          sprintf('%s|%s' , $user_starter_with_this_version ? 'with' : 'before', CUSTOMIZR_VER ),
                          60*60*24*9999
                      );
                  } else {
                      $has_already_installed_free = array_key_exists( 'last_update_notice', self::$db_options );
                      //we are in the case of a free user updating the free theme but has previously cleaned the transients in db
                      set_transient(
                          $_trans,
                          sprintf('%s|%s' , $has_already_installed_free ? 'before' : 'with', CUSTOMIZR_VER ),
                          60*60*24*9999
                      );
                  }
              }

              //fire an action hook after theme main properties have been set up
              // theme_name
              // db_options
              // default_options
              // started using customizr(-pro) transient
              do_action( 'czr_after_caching_options' );
        }



        /* ------------------------------------------------------------------------- *
         *  CACHE THE LIST OF THEME SETTINGS ONLY
        /* ------------------------------------------------------------------------- */
        //Fired in __construct()
        function czr_fn_cache_theme_setting_list() {
          if ( is_array(self::$theme_setting_list) && ! empty( self::$theme_setting_list ) )
            return;

          //fire an action hook before caching theme settomgs list
          do_action( 'czr_before_caching_theme_settings_list' );
          self::$theme_setting_list = czr_fn_generate_theme_setting_list();
          //fire an action hook after caching theme settomgs list
          do_action( 'czr_after_caching_theme_settings_list' );
        }


        /**
        * The purpose of this callback is to refresh and store the theme options in a property on each customize preview refresh
        * => preview performance improvement
        * 'customize_preview_init' is fired on wp_loaded, once WordPress is fully loaded ( after 'init', before 'wp') and right after the call to 'customize_register'
        * This method is fired just after the theme option has been filtered for each settings by the WP_Customize_Setting::_preview_filter() callback
        * => if this method is fired before this hook when customizing, the user changes won't be taken into account on preview refresh
        *
        * hook : customize_preview_init
        * @return  void
        */
        function czr_fn_customize_refresh_db_opt(){
          CZR___::$db_options = false === get_option( CZR_THEME_OPTIONS ) ? array() : (array)get_option( CZR_THEME_OPTIONS );
        }



        /**
         * Handles JavaScript detection.
         * hook : wp_head
         * Adds a `js` class to the root `<html>` element when JavaScript is detected.
         */
        function czr_fn_javascript_detection() {
            echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
        }


  }
endif;


//load shared fn
require_once( get_template_directory() . '/core/core-functions.php' );

//require init-pro if it exists
if ( file_exists( get_template_directory() . '/core/init-pro.php' ) )
  require_once( get_template_directory() . '/core/init-pro.php' );

//setup constants
czr_fn_setup_constants();
require_once( get_template_directory() . ( czr_fn_is_ms() ? '/core/init.php' : '/inc/czr-init-ccat.php' ) );