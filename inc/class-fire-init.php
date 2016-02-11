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
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_init' ) ) :
  class TC_init {
      //declares the filtered default settings
      public $global_layout;
      public $tc_thumb_size;
      public $slider_full_size;
      public $slider_size;
      public $tc_grid_full_size;
      public $tc_grid_size;
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
          $this -> tc_thumb_size      = array( 'width' => 270 , 'height' => 250, 'crop' => true ); //size name : tc-thumb
          $this -> slider_full_size   = array( 'width' => 9999 , 'height' => 500, 'crop' => true ); //size name : slider-full
          $this -> slider_size        = array( 'width' => 1170 , 'height' => 500, 'crop' => true ); //size name : slider
          $this -> tc_grid_full_size  = array( 'width' => 1170 , 'height' => 350, 'crop' => true ); //size name : tc-grid-full
          $this -> tc_grid_size       = array( 'width' => 570 , 'height' => 350, 'crop' => true ); //size name : tc-grid


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

          //Main skin color array : array( link color, link hover color )
          $this -> skin_color_map     = apply_filters( 'tc_skin_color_map' , array(
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
            'titles' => implode(',' , apply_filters( 'tc-titles-font-selectors' , array('.site-title' , '.site-description', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
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
            'tc_email'          => array(
                                    'link_title'    => __( 'E-mail' , 'customizr' ),
                                    'option_label'  => __( 'Contact E-mail address' , 'customizr' ),
                                    'default'       => null,
                                    'type'          => 'email'
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
                                  ),
            'tc_vk'             => array(
                                    'link_title'    => __( 'Follow me on VKontakte' , 'customizr' ),
                                    'option_label'  => __( 'VKontakte profile url' , 'customizr' ),
                                    'default'       => null
                                  ),
            'tc_yelp'           => array(
                                    'link_title'    => __( 'Follow me on Yelp' , 'customizr' ),
                                    'option_label'  => __( 'Yelp profile url' , 'customizr' ),
                                    'default'       => null
                                  ),
            'tc_xing'           => array(
                                    'link_title'    => __( 'Follow me on Xing' , 'customizr' ),
                                    'option_label'  => __( 'Xing profile url' , 'customizr' ),
                                    'default'       => null
                                  )
          );//end of social array


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
              'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                          TC_BASE_URL.'inc/assets/img/customizr-theme-responsive.png',
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
                                          TC_BASE_URL.'inc/assets/img/customizr-theme-customizer.png',
                                          __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                  )
            )
          );///end of slides array

          //Set image options set by user @since v3.2.0
          //! must be included in utils to be available in admin for plugins like regenerate thumbnails
          add_action( 'after_setup_theme'                      , array( $this, 'tc_set_user_defined_settings'));

          //add the text domain, various theme supports : editor style, automatic-feed-links, post formats, post-thumbnails
          add_action( 'after_setup_theme'                      , array( $this , 'tc_customizr_setup' ));
          //registers the menu
          add_action( 'after_setup_theme'                       , array( $this, 'tc_register_menus'));

          //add retina support for high resolution devices
          add_filter( 'wp_generate_attachment_metadata'        , array( $this , 'tc_add_retina_support') , 10 , 2 );
          add_filter( 'delete_attachment'                      , array( $this , 'tc_clean_retina_images') );

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter('body_class'                              , array( $this , 'tc_set_body_classes') );

      }//end of constructor



      /**
      * Set user defined options for images
      * Thumbnail's height
      * Slider's height
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function tc_set_user_defined_settings() {
        $_options = get_option('tc_theme_options');
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

        if ( isset ( $_options['tc_slider_change_default_img_size'] ) && 0 != esc_attr( $_options['tc_slider_change_default_img_size'] ) && isset ( $_options['tc_slider_default_height'] ) && 500 != esc_attr( $_options['tc_slider_default_height'] ) ) {
            add_filter( 'tc_slider_full_size'    , array($this,  'tc_set_slider_img_height') );
            add_filter( 'tc_slider_size'         , array($this,  'tc_set_slider_img_height') );
        }


        /***********
        *** GRID ***
        ***********/
        if ( isset( $_options['tc_grid_thumb_height'] ) ) {
            $_user_height  = esc_attr( $_options['tc_grid_thumb_height'] );

        }
        $tc_grid_full_size     = $this -> tc_grid_full_size;
        $tc_grid_size          = $this -> tc_grid_size;
        $_user_grid_height     = isset( $_options['tc_grid_thumb_height'] ) && is_numeric( $_options['tc_grid_thumb_height'] ) ? esc_attr( $_options['tc_grid_thumb_height'] ) : $tc_grid_full_size['height'];

        add_image_size( 'tc-grid-full', $tc_grid_full_size['width'], $_user_grid_height, $tc_grid_full_size['crop'] );
        add_image_size( 'tc-grid', $tc_grid_size['width'], $_user_grid_height, $tc_grid_size['crop'] );

        if ( $_user_grid_height != $tc_grid_full_size['height'] )
          add_filter( 'tc_grid_full_size', array( $this,  'tc_set_grid_img_height') );
        if ( $_user_grid_height != $tc_grid_size['height'] )
          add_filter( 'tc_grid_size'     , array( $this,  'tc_set_grid_img_height') );

      }



      /**
      * Set slider new image sizes
      * Callback of slider_full_size and slider_size filters
      * hook : might be called from after_setup_theme
      * @package Customizr
      * @since Customizr 3.2.0
      *
      */
      function tc_set_slider_img_height( $_default_size ) {
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
      function tc_set_grid_img_height( $_default_size ) {
        $_options = get_option('tc_theme_options');

        $_default_size['height'] =  esc_attr( $_options['tc_grid_thumb_height'] ) ;
        return $_default_size;
      }



      /**
       * Sets up theme defaults and registers the various WordPress features
       * hook : after_setup_theme | 20
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

        //add help button to admin bar
        add_action ( 'wp_before_admin_bar_render'          , array( $this , 'tc_add_help_button' ));
      }



      /*
      * hook : after_setup_theme
      */
      function tc_register_menus() {
        /* This theme uses wp_nav_menu() in one location. */
        register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );
        register_nav_menu( 'secondary' , __( 'Secondary (horizontal) Menu' , 'customizr' ) );
      }




      /**
      * Returns the active path+skin.css or tc_common.css
      *
      * @package Customizr
      * @since Customizr 3.0.15
      */
      function tc_get_style_src( $_wot = 'skin' ) {
        $_sheet    = ( 'skin' == $_wot ) ? esc_attr( TC_utils::$inst->tc_opt( 'tc_skin' ) ) : 'tc_common.css';
        $_sheet    = $this -> tc_maybe_use_min_style( $_sheet ); 

        //Finds the good path : are we in a child theme and is there a skin to override?
        $remote_path    = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/' : false ;
        $remote_path    = ( ! $remote_path && file_exists(TC_BASE .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/' : $remote_path ;
        //Checks if there is a rtl version of common if needed
        if ( 'skin' != $_wot && ( is_rtl() || ( defined( 'WPLANG' ) && ( 'ar' == WPLANG || 'he_IL' == WPLANG ) ) ) ){
          $remote_rtl_path   = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/rtl/' : false ;
          $remote_rtl_path   = ( ! $remote_rtl_path && file_exists(TC_BASE .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/rtl/' : $remote_rtl_path;
          $remote_path       = $remote_rtl_path ? $remote_rtl_path : $remote_path;
        }

        //Defines the active skin and fallback to blue.css if needed
        if ( 'skin' == $_wot )
          $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/blue3.css';
        else
          $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/tc_common.css';

        return apply_filters ( 'tc_get_style_src' , $tc_get_style_src , $_wot );
      }



      /**
      * //Move in TC_utils?
      *
      * Returns the min or normal version of the passed css filename (basename.type)
      * depending on whether or not the minified version should be used
      *  
      * @param $_sheet string
      *
      * @return string
      *
      * @package Customizr
      * @since Customizr 3.4.19
      */
      function tc_maybe_use_min_style( $_sheet ) {
        if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_minified_skin' ) ) )
          $_sheet = ( defined('TC_NOT_MINIFIED_CSS') && true === TC_NOT_MINIFIED_CSS ) ? $_sheet : str_replace('.css', '.min.css', $_sheet);
        return $_sheet;
      }



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
        if ( 0 == TC_utils::$inst->tc_opt( 'tc_retina_support' ) )
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
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_body_classes( $_classes ) {
        if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_link_hover_effect' ) ) )
          array_push( $_classes, 'tc-fade-hover-links' );
        if ( TC___::$instance -> tc_is_customizing() )
          array_push( $_classes, 'is-customizing' );
        if ( wp_is_mobile() )
          array_push( $_classes, 'tc-is-mobile' );
        if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_enable_dropcap' ) ) )
          array_push( $_classes, esc_attr( TC_utils::$inst->tc_opt( 'tc_dropcap_design' ) ) );

        //adds the layout
        $_layout = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar' );
        if ( in_array( $_layout, array('b', 'l', 'r' , 'f') ) ) {
          array_push( $_classes, sprintf( 'tc-%s-sidebar',
            'f' == $_layout ? 'no' : $_layout
          ) );
        }
        return $_classes;
      }

  }//end of class
endif;
