<?php
/**
* Fires the theme : constants definition, core classes loading
*
*/
if ( ! class_exists( 'CZR___' ) ) :
  final class CZR___ extends CZR_BASE {
    public $tc_core;
    public $is_customizing;
    public static $tc_option_group;

    function __construct () {
        //following R. Aliberti advise
        if( ! defined( 'CZR_IS_MODERN_STYLE' ) )            define( 'CZR_IS_MODERN_STYLE' , false );

        //call CZR_BASE constructor
        parent::__construct();

        self::$instance =& $this;

        //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
        $this -> tc_core = apply_filters( 'tc_core',
          array(
              'fire'      =>   array(
                  array('inc' , 'init'),//defines default values (layout, socials, default slider...) and theme supports (after_setup_theme)
                  array('inc' , 'plugins_compat'),//handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
                  array('inc' , 'utils_settings_map'),//customizer setting map
                  array('inc' , 'utils'),//helpers used everywhere
                  array('inc' , 'init_retro_compat'),
                  array('inc' , 'resources'),//loads front stylesheets (skins) and javascripts
                  array('inc' , 'widgets'),//widget factory
                  array('inc/admin' , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
                  array('inc/admin' , 'admin_page')//creates the welcome/help panel including changelog and system config
              ),
              'admin'     => array(
                  array('inc/admin' , 'customize'),//loads customizer actions and resources
                  array('inc/admin' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
              ),
              //the following files/classes define the action hooks for front end rendering : header, main content, footer
              'header'    =>   array(
                  array('inc/parts' , 'header_main'),
                  array('inc/parts' , 'menu'),
                  array('inc/parts' , 'nav_walker')
              ),
              'content'   =>  array(
                  array('inc/parts', '404'),
                  array('inc/parts', 'attachment'),
                  array('inc/parts', 'breadcrumb'),
                  array('inc/parts', 'comments'),
                  array('inc/parts', 'featured_pages'),
                  array('inc/parts', 'gallery'),
                  array('inc/parts', 'headings'),
                  array('inc/parts', 'no_results'),
                  array('inc/parts', 'page'),
                  array('inc/parts', 'post_thumbnails'),
                  array('inc/parts', 'post'),
                  array('inc/parts', 'post_list'),
                  array('inc/parts', 'post_list_grid'),
                  array('inc/parts', 'post_metas'),
                  array('inc/parts', 'post_navigation'),
                  array('inc/parts', 'sidebar'),
                  array('inc/parts', 'slider')
              ),
              'footer'    => array(
                  array('inc/parts', 'footer_main'),
              ),
              'addons'    => apply_filters( 'tc_addons_classes' , array() )
          )//end of array
        );//end of filter

        self::$tc_option_group = 'tc_theme_options';

        //set files to load according to the context : admin / front / customize
        add_filter( 'tc_get_files_to_load' , array( $this , 'czr_fn_set_files_to_load' ) );


        //theme class groups instanciation
        //$this -> czr_fn__();
        add_action('czr_load', array( $this, 'czr_fn__') );

    }//end of __construct()




    /**
    * Class instanciation using a singleton factory :
    * Can be called to instantiate a specific class or group of classes
    * @param  array(). Ex : array ('admin' => array( array( 'inc/admin' , 'meta_boxes') ) )
    * @return  instances array()
    *
    * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
    *
    * @since Customizr 3.0
    */
    function czr_fn__( $_to_load = array(), $_no_filter = false ) {
        static $instances;
        //do we apply a filter ? optional boolean can force no filter
        $_to_load = $_no_filter ? $_to_load : apply_filters( 'tc_get_files_to_load' , $_to_load );

        if ( empty($_to_load) )
          return;

        foreach ( $_to_load as $group => $files ) {
          foreach ($files as $path_suffix ) {
            //checks if a child theme is used and if the required file has to be overriden
            // if ( czr_fn_is_child() && file_exists( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ) {
            //     require_once ( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
            // }
            // else {
            //     require_once ( TC_BASE . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
            // }

            $classname = 'CZR_' . $path_suffix[1];
            if( ! isset( $instances[ $classname ] ) )  {
              //check if the classname can be instantiated here
              if ( in_array( $classname, apply_filters( 'tc_dont_instantiate_in_init', array( 'CZR_nav_walker') ) ) )
                continue;
              //instantiates
              $instances[ $classname ] = class_exists($classname)  ? new $classname : '';
            }
          }
        }
        return $instances[ $classname ];
    }





    /***************************
    * HELPERS
    ****************************/
    function czr_fn_req_once( $file_path ) {
        //checks if a child theme is used and if the required file has to be overriden
        if ( czr_fn_is_child() && file_exists( TC_BASE_CHILD . $file_path ) ) {
            require_once ( TC_BASE_CHILD . $file_path ) ;
        }
        else {
            require_once ( TC_BASE . $file_path ) ;
        }
    }



    /**
    * Check the context and return the modified array of class files to load and instantiate
    * hook : tc_get_files_to_load
    * @return boolean
    *
    * @since  Customizr 3.3+
    */
    function czr_fn_set_files_to_load( $_to_load ) {
        $_to_load = empty($_to_load) ? $this -> tc_core : $_to_load;
        //Not customizing
        //1) IS NOT CUSTOMIZING : czr_fn_is_customize_left_panel() || czr_fn_is_customize_preview_frame() || czr_fn_doing_customizer_ajax()
        //---1.1) IS ADMIN
        //---1.2) IS NOT ADMIN
        //2) IS CUSTOMIZING
        //---2.1) IS LEFT PANEL => customizer controls
        //---2.2) IS RIGHT PANEL => preview
        if ( ! czr_fn_is_customizing() )
          {
            if ( is_admin() ) {
              //load
              $this -> czr_fn_req_once( 'core/czr-admin-ccat.php' );

              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize' ) );
            }
            else {
              //load
              $this -> czr_fn_req_once( 'inc/czr-front-ccat.php' );

              //Skips all admin classes
              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page') );
            }
          }
        //Customizing
        else
          {
            //load
            $this -> czr_fn_req_once( 'core/czr-admin-ccat.php' );
            $this -> czr_fn_req_once( 'core/czr-customize-ccat.php' );

            //left panel => skip all front end classes
            if ( czr_fn_is_customize_left_panel() ) {
              $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array( 'header' , 'content' , 'footer' ),
                  array( 'fire|inc|resources' , 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
              );
            }
            if ( czr_fn_is_customize_preview_frame() ) {
              //load
              $this -> czr_fn_req_once( 'inc/czr-front-ccat.php' );

              $_to_load = $this -> czr_fn_unset_core_classes(
                $_to_load,
                array(),
                array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
              );
            }
          }

        return $_to_load;
    }



    /**
    * Helper
    * Alters the original classes tree
    * @param $_groups array() list the group of classes to unset like header, content, admin
    * @param $_files array() list the single file to unset.
    * Specific syntax for single files: ex in fire|inc/admin|admin_page
    * => fire is the group, inc/admin is the path, admin_page is the file suffix.
    * => will unset inc/admin/class-fire-admin_page.php
    *
    * @return array() describing the files to load
    *
    * @since  Customizr 3.0.11
    */
    public function czr_fn_unset_core_classes( $_tree, $_groups = array(), $_files = array() ) {
        if ( empty($_tree) )
          return array();
        if ( ! empty($_groups) ) {
          foreach ( $_groups as $_group_to_remove ) {
            unset($_tree[$_group_to_remove]);
          }
        }
        if ( ! empty($_files) ) {
          foreach ( $_files as $_concat ) {
            //$_concat looks like : fire|inc|resources
            $_exploded = explode( '|', $_concat );
            //each single file entry must be a string like 'admin|inc/admin|customize'
            //=> when exploded by |, the array size must be 3 entries
            if ( count($_exploded) < 3 )
              continue;

            $gname = $_exploded[0];
            $_file_to_remove = $_exploded[2];
            if ( ! isset($_tree[$gname] ) )
              continue;
            foreach ( $_tree[$gname] as $_key => $path_suffix ) {
              if ( false !== strpos($path_suffix[1], $_file_to_remove ) )
                unset($_tree[$gname][$_key]);
            }//end foreach
          }//end foreach
        }//end if
        return $_tree;
    }//end of fn


  }//end of class
endif;

?>
<?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
*
*
*/
if ( ! class_exists( 'CZR_init' ) ) :
  class CZR_init {
      //declares the filtered default settings
      public $global_layout;
      public $skins;
      public $font_selectors;
      public $footer_widgets;
      public $widgets;
      public $post_list_layout;
      public $post_formats_with_no_heading;
      public $content_404;
      public $content_no_results;

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

          //Default skins array
          $this -> skins              =  array(
              'blue.css'        =>  __( 'Blue' , 'customizr' ),
              'black.css'       =>  __( 'Black' , 'customizr' ),
              'black2.css'      =>  __( 'Flat black' , 'customizr' ),
              'grey.css'        =>  __( 'Grey' , 'customizr' ),
              'grey2.css'       =>  __( 'Light grey' , 'customizr' ),
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


          $this -> font_selectors     = array(
              'titles' => implode(',' , apply_filters( 'tc-titles-font-selectors' , array('.site-title' , '.site-description', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
              'body'   => implode(',' , apply_filters( 'tc-body-font-selectors' , array('body' , '.navbar .nav>li>a') ) )
          );

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
              'quote'             => '',
              'author'            => '',
              'text'              => ''
          );

          //Default no search result content
          $this -> content_no_results = array(
              'quote'             => '',
              'author'            => '',
              'text'              => ''
          );

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter('body_class'                              , array( $this , 'czr_fn_set_body_classes') );
      }//end of constructor



      /**
      * Returns the active path+skin.css or tc_common.css
      *
      * @package Customizr
      * @since Customizr 3.0.15
      */
      function czr_fn_get_style_src( $_wot = 'skin' ) {
          $_sheet    = ( 'skin' == $_wot ) ? esc_attr( czr_fn_opt( 'tc_skin' ) ) : 'tc_common.css';
          $_sheet    = esc_attr( czr_fn_opt( 'tc_minified_skin' ) ) ? str_replace('.css', '.min.css', $_sheet) : $_sheet;

          //Finds the good path : are we in a child theme and is there a skin to override?
          $remote_path    = ( czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/' : false ;
          $remote_path    = ( ! $remote_path && file_exists(TC_BASE .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/' : $remote_path ;
          //Checks if there is a rtl version of common if needed
          if ( 'skin' != $_wot && ( is_rtl() || ( defined( 'WPLANG' ) && ( 'ar' == WPLANG || 'he_IL' == WPLANG ) ) ) ){
            $remote_rtl_path   = ( czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/rtl/' : false ;
            $remote_rtl_path   = ( ! $remote_rtl_path && file_exists(TC_BASE .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/rtl/' : $remote_rtl_path;
            $remote_path       = $remote_rtl_path ? $remote_rtl_path : $remote_path;
          }

          //Defines the active skin and fallback to blue.css if needed
          if ( 'skin' == $_wot )
            $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/grey.css';
          else
            $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/tc_common.css';

          return apply_filters ( 'tc_get_style_src' , $tc_get_style_src , $_wot );
      }





      /*
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_body_classes( $_classes ) {
          if ( 0 != esc_attr( czr_fn_opt( 'tc_link_hover_effect' ) ) )
            array_push( $_classes, 'tc-fade-hover-links' );
          if ( czr_fn_is_customizing() )
            array_push( $_classes, 'is-customizing' );
          if ( wp_is_mobile() )
            array_push( $_classes, 'tc-is-mobile' );
          if ( 0 != esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ) )
            array_push( $_classes, esc_attr( czr_fn_opt( 'tc_dropcap_design' ) ) );

          //adds the layout
          $_layout = CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
          if ( in_array( $_layout, array('b', 'l', 'r' , 'f') ) ) {
            array_push( $_classes, sprintf( 'tc-%s-sidebar',
              'f' == $_layout ? 'no' : $_layout
            ) );
          }

          //IMAGE CENTERED
          if ( (bool) esc_attr( czr_fn_opt( 'tc_center_img') ) ){
            $_classes = array_merge( $_classes , array( 'tc-center-images' ) );
          }

          //SKIN CLASS
          $_skin = sprintf( 'skin-%s' , basename( $this -> czr_fn_get_style_src() ) );
          array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

          return $_classes;
      }
  }//end of class
endif;

?><?php
/**
* Handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
*
*/
if ( ! class_exists( 'CZR_plugins_compat' ) ) :
  class CZR_plugins_compat {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    //credits @Srdjan
    public $default_language, $current_language;

    function __construct () {

      self::$instance =& $this;
      //add various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_set_plugins_supported'), 20 );
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_plugins_compatibility'), 30 );
      // remove qtranslateX theme options filter
      remove_filter('option_tc_theme_options', 'qtranxf_translate_option', 5);

    }//end of constructor



    /**
    * Set plugins supported ( before the plugin compat function is fired )
    * => allows to easily remove support by firing remove_theme_support() (with a priority < tc_plugins_compatibility) on hook 'after_setup_theme'
    * hook : after_setup_theme
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_plugins_supported() {
      //add support for plugins (added in v3.1+)
      add_theme_support( 'jetpack' );
      add_theme_support( 'bbpress' );
      add_theme_support( 'buddy-press' );
      add_theme_support( 'qtranslate-x' );
      add_theme_support( 'polylang' );
      add_theme_support( 'wpml' );
      add_theme_support( 'woocommerce' );
      add_theme_support( 'wc-product-gallery-zoom' );
      add_theme_support( 'wc-product-gallery-lightbox' );
      add_theme_support( 'wc-product-gallery-slider' );
      add_theme_support( 'the-events-calendar' );
      add_theme_support( 'event-tickets' );
      add_theme_support( 'optimize-press' );
      add_theme_support( 'woo-sensei' );
      add_theme_support( 'visual-composer' );//or js-composer as they call it
      add_theme_support( 'disqus' );
      add_theme_support( 'uris' );
      add_theme_support( 'tc-unlimited-featured-pages' );
      add_theme_support( 'learnpress' );
      add_theme_support( 'coauthors' );
    }



    /**
    * This function handles the following plugins compatibility : Jetpack (for the carousel addon and photon), Bbpress, Qtranslate, Woocommerce
    *
    * @package Customizr
    * @since Customizr 3.0.15
    */
    function czr_fn_plugins_compatibility() {
      /* Unlimited Featured Pages  */
      if ( current_theme_supports( 'tc-unlimited-featured-pages' ) && czr_fn_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php') )
        $this -> czr_fn_set_tc_unlimited_featured_pages_compat();

      /* JETPACK */
      //adds compatibilty with the jetpack image carousel and photon
      if ( current_theme_supports( 'jetpack' ) && czr_fn_is_plugin_active('jetpack/jetpack.php') )
        $this -> czr_fn_set_jetpack_compat();


      /* BBPRESS */
      //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
      if ( current_theme_supports( 'bbpress' ) && czr_fn_is_plugin_active('bbpress/bbpress.php') )
        $this -> czr_fn_set_bbpress_compat();

      /* BUDDYPRESS */
      //if buddypress is installed and activated, we can check the existence of the contextual boolean function is_buddypress() to execute some code
      // we have to use buddy-press instead of buddypress as string for theme support as buddypress makes some checks on current_theme_supports('buddypress') which result in not using its templates
      if ( current_theme_supports( 'buddy-press' ) && czr_fn_is_plugin_active('buddypress/bp-loader.php') )
        $this -> czr_fn_set_buddypress_compat();

      /*
      * QTranslatex
      * Credits : @acub, http://websiter.ro
      */
      if ( current_theme_supports( 'qtranslate-x' ) && czr_fn_is_plugin_active('qtranslate-x/qtranslate.php') )
        $this -> czr_fn_set_qtranslatex_compat();

      /*
      * Polylang
      * Credits : Rocco Aliberti
      */
      if ( current_theme_supports( 'polylang' ) && ( czr_fn_is_plugin_active('polylang/polylang.php') || czr_fn_is_plugin_active('polylang-pro/polylang.php') ) )
        $this -> czr_fn_set_polylang_compat();

      /*
      * WPML
      */
      if ( current_theme_supports( 'wpml' ) && czr_fn_is_plugin_active('sitepress-multilingual-cms/sitepress.php') )
        $this -> czr_fn_set_wpml_compat();

      /* The Events Calendar */
      if ( current_theme_supports( 'the-events-calendar' ) && czr_fn_is_plugin_active('the-events-calendar/the-events-calendar.php') )
        $this -> czr_fn_set_the_events_calendar_compat();

      /* Event Tickets */
      if ( current_theme_supports( 'event-tickets' ) && czr_fn_is_plugin_active('event-tickets/event-tickets.php') )
        $this -> czr_fn_set_event_tickets_compat();

      /* Optimize Press */
      if ( current_theme_supports( 'optimize-press' ) && czr_fn_is_plugin_active('optimizePressPlugin/optimizepress.php') )
        $this -> czr_fn_set_optimizepress_compat();

      /* Woocommerce */
      if ( current_theme_supports( 'woocommerce' ) && czr_fn_is_plugin_active('woocommerce/woocommerce.php') )
        $this -> czr_fn_set_woocomerce_compat();

      /* Sensei woocommerce addon */
      if ( current_theme_supports( 'woo-sensei') && czr_fn_is_plugin_active('woothemes-sensei/woothemes-sensei.php') )
        $this -> czr_fn_set_sensei_compat();

      /* Visual Composer */
      if ( current_theme_supports( 'visual-composer') && czr_fn_is_plugin_active('js_composer/js_composer.php') )
        $this -> czr_fn_set_vc_compat();

      /* Disqus Comment System */
      if ( current_theme_supports( 'disqus') && czr_fn_is_plugin_active('disqus-comment-system/disqus.php') )
        $this -> czr_fn_set_disqus_compat();

      /* Ultimate Responsive Image Slider  */
      if ( current_theme_supports( 'uris' ) && czr_fn_is_plugin_active('ultimate-responsive-image-slider/ultimate-responsive-image-slider.php') )
        $this -> czr_fn_set_uris_compat();

      /* LearnPress  */
      if ( current_theme_supports( 'learnpress' ) && czr_fn_is_plugin_active('learnpress/learnpress.php') )
        $this -> czr_fn_set_lp_compat();

      /* Coauthors-Plus */
      if ( current_theme_supports( 'coauthors' ) && czr_fn_is_plugin_active('co-authors-plus/co-authors-plus.php') )
        $this -> czr_fn_set_coauthors_compat();
    }//end of plugin compatibility function


    /*
    * Same in czr modern
    */
    /**
    * Jetpack compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_jetpack_compat() {
      //Photon jetpack's module conflicts with our smartload feature:
      //Photon removes the width,height attribute in php, then in js it compute them (when they have the special attribute 'data-recalc-dims')
      //based on the img src. When smartload is enabled the images parsed by its js which are not already smartloaded are dummy
      //and their width=height is 1. The image is correctly loaded but the space
      //assigned to it will be 1x1px. Photon js, is compatible with Auttomatic plugin lazy load and it sets the width/height
      //attribute only when the img is smartloaded. This is pretty useless to me, as it doesn't solve the main issue:
      //document's height change when the img are smartloaded.
      //Anyway to avoid the 1x1 issue we alter the img attribute (data-recalc-dims) which photon adds to the img tag(php) so
      //the width/height will not be erronously recalculated
      if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) )
        add_filter( 'czr_img_smartloaded', 'czr_fn_jp_smartload_img');
      function czr_fn_jp_smartload_img( $img ) {
        return str_replace( 'data-recalc-dims', 'data-tcjp-recalc-dims', $img );
      }
    }//end jetpack compat




    /**
    * BBPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_bbpress_compat() {
      if ( ! function_exists( 'czr_fn_bbpress_disable_feature' ) ) {
        function czr_fn_bbpress_disable_feature( $bool ) {
          return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
        }
      }

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_bbpress_disable_feature');
      //disables thumbnails and excerpt for post lists
      add_filter( 'tc_opt_tc_post_list_thumb', 'czr_fn_bbpress_disable_feature' );
      //show full content in post lists
      add_filter( 'tc_show_excerpt', 'czr_fn_bbpress_disable_feature' );
      //disables Customizr author infos on forums
      add_filter( 'tc_show_author_metas_in_post', 'czr_fn_bbpress_disable_feature' );
      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_bbpress_disable_feature' );
      //disables post metas
      add_filter( 'tc_show_post_metas', 'czr_fn_bbpress_disable_feature', 100);
      //disable the grid
      add_filter( 'tc_set_grid_hooks' , 'czr_fn_bbpress_disable_feature', 100 );
    }


    /*
    * Same in czr modern except for comments enabled filter prefix (tc_ -> czr_)
    */
    /**
    * BuddyPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_buddypress_compat() {
      add_filter( 'tc_are_comments_enabled', 'czr_fn_buddypress_disable_comments' );
      function czr_fn_buddypress_disable_comments($bool){
        return ( is_page() && function_exists('is_buddypress') && is_buddypress() ) ? false : $bool;
      }
      //disable smartload in change-avatar buddypress profile page
      //to avoid the img tag (in a template loaded with backbone) being parsed on server side but
      //not correctly processed by the front js.
      //the action hook "xprofile_screen_change_avatar" is a buddypress specific hook
      //fired before wp_head where we hook tc_parse_imgs
      //side-effect: all the images in this pages will not be smartloaded, this isn't a big deal
      //as there should be at maximum 2 images there:
      //1) the avatar, if already set
      //2) a cover image, if already set
      //anyways this page is not a regular "front" page as it pertains more to a "backend" side
      //if we can call it that way.
      add_action( 'xprofile_screen_change_avatar', 'czr_fn_buddypress_maybe_disable_img_smartload' );
      function czr_fn_buddypress_maybe_disable_img_smartload() {
        add_filter( 'tc_opt_tc_img_smart_load', '__return_false' );
      }
    }

    /*
    * same in czr modern with filter prefixes change ( tc_ -> czr_ )
    */
    /**
    * QtranslateX compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_qtranslatex_compat() {
      function czr_fn_url_lang($url) {
        return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
      }
      function czr_fn_apply_qtranslate ($text) {
        return call_user_func(  '__' , $text );
      }
      function czr_fn_remove_char_limit() {
        return 99999;
      }
      function czr_fn_change_transport( $value , $set ) {
        return ('transport' == $set) ? 'refresh' : $value;
      }

      //outputs correct urls for current language : in logo, slider
      foreach ( array( 'tc_slide_link_url', 'tc_logo_link_url') as $filter )
        add_filter( $filter, 'czr_fn_url_lang' );

      //outputs the qtranslate translation for slider
      foreach ( array( 'tc_slide_title', 'tc_slide_text', 'tc_slide_button_text', 'tc_slide_background_alt' ) as $filter )
        add_filter( $filter, 'czr_fn_apply_qtranslate' );
      //sets no character limit for slider (title, lead text and button title) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
      foreach ( array( 'tc_slide_title_length', 'tc_slide_text_length', 'tc_slide_button_length' ) as $filter )
        add_filter( $filter  , 'czr_fn_remove_char_limit');

      //outputs the qtranslate translation for archive titles;
      $tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results');
      foreach ( $tc_archive_titles as $title )
        add_filter("tc_{$title}_title", 'czr_fn_apply_qtranslate' , 20);

      // QtranslateX for FP when no FPC or FPU running
      if ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) {
        //outputs correct urls for current language : fp
        add_filter( 'tc_fp_link_url' , 'czr_fn_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'tc_fp_text', 'czr_fn_apply_qtranslate' );
        add_filter( 'tc_fp_button_text', 'czr_fn_apply_qtranslate' );

        /* The following is pretty useless at the momment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
        add_filter( 'tc_featured_page_button_text_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_one_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_two_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_three_customizer_set', 'czr_fn_change_transport', 20, 2);
      }

      //posts slider (this filter is not fired in admin )
      add_filter('tc_posts_slider_pre_model', 'czr_fn_posts_slider_qtranslate');
      function czr_fn_posts_slider_qtranslate( $pre_slides ){
        if ( empty($pre_slides) )
          return $pre_slides;

        // remove useles q-translation of the slider view
        foreach ( array( 'tc_slide_title', 'tc_slide_text', 'tc_slide_button_text', 'tc_slide_background_alt' ) as $filter )
          remove_filter( $filter, 'czr_fn_apply_qtranslate' );

        // allow q-translation pre trim/sanitize
        foreach ( array( 'tc_posts_slider_button_text_pre_trim', 'tc_post_title_pre_trim', 'tc_post_excerpt_pre_sanitize', 'tc_posts_slide_background' ) as $filter )
          add_filter( $filter, 'czr_fn_apply_qtranslate' );

        //translate button text
        $pre_slides['common']['button_text'] = $pre_slides['common']['button_text'] ? CZR_slider::$instance -> czr_fn_get_post_slide_button_text( $pre_slides['common']['button_text'] ) : '';

        //translate title and excerpt if needed
        $_posts = &$pre_slides['posts'];

        foreach ($_posts as &$_post) {
          $ID = $_post['ID'];
          $_p = get_post( $ID );
          if ( ! $_p ) continue;

          $_post['title'] = $_post['title'] ? CZR_slider::$instance -> czr_fn_get_post_slide_title($_p, $ID) : '';
          $_post['text']  = $_post['text'] ? CZR_slider::$instance -> czr_fn_get_post_slide_excerpt($_p, $ID) : '';
        }
        return $pre_slides;
      }
    }

    /*
    * same in czr modern
    */
    /**
    * Polylang compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_polylang_compat() {

      // If Polylang is active, hook function on the admin pages
      if ( function_exists( 'pll_register_string' ) )
        add_action( 'admin_init', 'czr_fn_pll_strings_setup' );

      function czr_fn_pll_strings_setup() {
        // grab theme options
        $tc_options = czr_fn__f('__options');
        // grab settings map, useful for some options labels
        $tc_settings_map = czr_fn_get_customizer_map( $get_default = true );
        $tc_controls_map = $tc_settings_map['add_setting_control'];
        // set $polylang_group;
        $polylang_group = CZR_IS_PRO ? 'Customizr-Pro' : 'Customizr';

        //get options to translate
        $tc_translatable_raw_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        $tc_pll_options              = array();

        //build array if option => array( label (gettext-ed), option )
        foreach ( $tc_translatable_raw_options as $tc_translatable_option )
          if ( isset( $tc_options[$tc_translatable_option] ) ) {
            switch ( $tc_translatable_option ) {
              case 'tc_front_slider'             : $label = __( 'Front page slider name', 'customizr' );
                                                   break;
              case 'tc_posts_slider_button_text' : $label = __( 'Posts slider button text', 'customizr' );
                                                   break;
              default:                             $label = $tc_controls_map[$tc_translatable_option]['label'];
                                                   break;
            }//endswitch
            $tc_pll_options[$tc_translatable_option]= array(
                'label'  => $label,
                'value'  => $tc_options[$tc_translatable_option]
            );
          }

        //register the strings to translate
        foreach ( $tc_pll_options as $tc_pll_option )
          pll_register_string( $tc_pll_option['label'], $tc_pll_option['value'], $polylang_group);
      }// end tc_pll_strings_setup function

      // Front
      // If Polylang is active, translate/swap featured page buttons/text/link and slider
      if ( function_exists( 'pll_get_post' ) && function_exists( 'pll__' ) && ! is_admin() ) {
        //strings translation
        //get the options to translate
        $tc_translatable_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        //translate
        foreach ( $tc_translatable_options as $tc_translatable_option )
          add_filter("tc_opt_{$tc_translatable_option}", 'pll__');

        /**
        * Tax filtering (home/blog posts filtered by cat)
        * @param array of term ids
        */
        function czr_fn_pll_translate_tax( $term_ids ){
          if ( ! ( is_array( $term_ids ) && ! empty( $term_ids ) ) )
            return $term_ids;

          $translated_terms = array();
          foreach ( $term_ids as $id ){
              $translated_term = pll_get_term( $id );
              $translated_terms[] = $translated_term ? $translated_term : $id;
          }
          return array_unique( $translated_terms );
        }

        //Translate category ids for the filtered posts in home/blog
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_pll_translate_tax');
        /*end tax filtering*/

        //Featured pages ids "translation"
        // Substitute any page id with the equivalent page in current language (if found)
        add_filter( 'tc_fp_id', 'czr_fn_pll_page_id', 20 );
        function czr_fn_pll_page_id( $fp_page_id ) {
          return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
        }
      }//end Front
    }//end polylang compat

    /*
    * same in czr modern
    */
    /**
    * WPML compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_wpml_compat() {
      //credits : @Srdjan
      $this->default_language = apply_filters( 'wpml_default_language', null );
      $this->current_language = apply_filters( 'wpml_current_language', null );

      //define the CONSTANT wpml context. This means that user have to set the translations again when switching from Customizr, to Customizr-Pro.
      //If we don't want to do this, let's go with 'Customizr-option' in any case.
      //Also I choose to use "-option" suffix to avoid confusions as with WPML you can also translate theme's strings ( gettexted -> __() ) and WPML by default assigns to theme the context 'customizr' (textdomain)
      define( 'TC_WPML_CONTEXT' ,  'customizr-option' );

      // We cannot use wpml-config.xml to translate theme options because we use to update the option even in front page after retrieved, so we have to act on
      // a different filter.
      // When registering and translate strings WPML requires a 'context' and a 'name' (in a readable format for translators) plus the string to translate
      // context will be concatenated to the name and md5 will run on the result. The new result will represent the KEY for the WPML translations cache array.
      // This means that
      // 1) We cannot use translated string for the "name" param (which actually they say should be in a readable format ..)
      // 2) We need a way to use the same "name" both when registering the string to translate and retrieving its translations
      function czr_fn_wpml_get_options_names_config() {
        $_wp_cache_key     = 'tc_wpml_get_options_names_config';
        $option_name_assoc = wp_cache_get( $_wp_cache_key );

        if ( false === $option_name_assoc ) {
          $options_to_translate = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config', array(
 //           'tc_front_slider'              => 'Front page slider name', //Handled in a different way by Srdjan
            'tc_posts_slider_button_text'  => 'Posts slider button text',
            'tc_tag_title'                 => 'Tag pages title',
            'tc_cat_title'                 => 'Category pages title',
            'tc_author_title'              => 'Author pages title',
            'tc_search_title'              => 'Search pages title',
            'tc_social_in_sidebar_title'   => 'Social link title in sidebars',
            'tc_featured_page_button_text' => 'Featured button text',
            'tc_featured_text_one'         => 'Featured text one',
            'tc_featured_text_two'         => 'Featured text two',
            'tc_featured_text_three'       => 'Featured text three'
          ) );

          foreach ( $option_name_assoc as $key => $value ) {
            //use array_key_exists when and if options_to_translate will be an associative array
            if ( ! in_array( $key, $options_to_translate ) )
              unset( $option_name_assoc[$key] );
            else
              //md5 and html are stripped in wpml string table rendering, we add it for a better key
              $option_name_assoc[$key]    = $value . ' - ' . md5($key); //name
          }

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config_pre_cache', $option_name_assoc );
          //cache this 'cause is used several times in filter callbacks
          wp_cache_set( $_wp_cache_key, $option_name_assoc );
        }
        return apply_filters( 'tc_wpml_get_options_names_config', $option_name_assoc );
      }

      //Wras wpml_object_id in a more convenient function which recursevely translates array of values
      //$object can be an array or a single value
      function czr_fn_wpml_object_id( $object_id, $type ) {
        if ( empty( $object_id ) )
          return $object_id;
        if ( is_array( $object_id ) )
          return array_map( 'czr_fn_wpml_object_id', $object_id, array_fill( 0, sizeof( $object_id ), $type ) );
        return apply_filters( 'wpml_object_id', $object_id, $type, true );
      }

      //credits: @Srdjan -> filter the slides in the current language
      function czr_fn_wpml_sliders_filter( $sliders ) {
        if ( is_array( $sliders ) )
          foreach ( $sliders as $name => $slides ) {
            foreach ( $slides as $key => $attachment_id ) {
              // Get current slide language
              $slide_language = apply_filters( 'wpml_element_language_code',
                            null, array('element_id' => $attachment_id,
                                'element_type' => 'attachment') );
              if ( CZR_plugins_compat::$instance->current_language != $slide_language ) {
                // Replace with translated slide
                $translated_slide_id = apply_filters( 'wpml_object_id',
                                $attachment_id, 'attachment', false );
                if ( $translated_slide_id )
                  $sliders[$name][$key] = $translated_slide_id;
              }
            }
            $sliders[$name] = array_unique( $sliders[$name] );
          }

        return $sliders;
      }
      //credits: @Srdjan,
      function czr_fn_wpml_add_theme_options_filter() {
        add_filter( 'option_tc_theme_options', 'czr_fn_wpml_theme_options_filter', 99 );
      }
      //credits: @Srdjan
      function czr_fn_wpml_theme_options_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
        }
        return $options;
      }
      //credits: @Srdjan
      function czr_fn_wpml_edit_attachment_action( $attachment_id ) {
        $languages = apply_filters( 'wpml_active_languages', array() );
        // TODO check which meta keys are a must
        $meta_data = get_post_custom( $attachment_id );
        foreach ( $languages as $language) {
            $translated_attachment_id = apply_filters( 'wpml_object_id',
                    $attachment_id, 'attachment', false, $language['code'] );
            // Update post meta
            foreach ( array('post_slider_key', 'slider_check_key') as $meta_key ) {
                if ( isset( $meta_data[$meta_key][0] ) ) {
                    update_post_meta( $translated_attachment_id, $meta_key, $meta_data[$meta_key][0] );
                }
            }
        }
      }

      function czr_fn_wpml_pre_update_option_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            // Force default language
            $current_language = CZR_plugins_compat::$instance->current_language;
            CZR_plugins_compat::$instance->current_language = CZR_plugins_compat::$instance->default_language;
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
            CZR_plugins_compat::$instance->current_language = $current_language;
        }
        return $options;
      }

      add_action( 'admin_init', 'czr_fn_wpml_admin_setup' );

      function czr_fn_wpml_admin_setup() {
        // If wpml-string-translation is active perform admin pages translation
        if ( function_exists( 'icl_register_string' ) ) {
          $tc_wpml_option_name = czr_fn_wpml_get_options_names_config();
          $tc_wpml_options     = array_keys($tc_wpml_option_name);

          // grab theme options
          $tc_options = czr_fn__f('__options');

          // build array of options to translate
          foreach ( $tc_wpml_options as $tc_wpml_option )
            if ( isset( $tc_options[$tc_wpml_option] ) )
              icl_register_string( TC_WPML_CONTEXT,
                $tc_wpml_option_name[$tc_wpml_option],
                esc_attr($tc_options[$tc_wpml_option]) //value
            );
        }//end of string based admin translation
        //Taxonomies/Pages "transposing" in the Customizer
        //We actually could just do this instead of A) and B) in front, but we retrieve the options in front before the compat method is called (after_setup_theme with lower priority) and I prefer to keep front and back separated in this case. Different opinions are welcome, but not too much :P.
        //we have to filter the interesting options so they appear "translated" in the customizer too, 'cause wpml filters the pages/cats to choose (fp, cat pickers), and we kinda like this :), right (less memory)?
        //Side effect example for categories: TODO
        //In English we have set to filter blog posts for cat A,B and C.
        //In Italian we do not have cat C so there will be displayed transposed cats A and B
        //if we change this option in the Customizer with lang IT removing B, e.g., when we switch to EN we'll have that the array of cats contains just A, as it as been overwritten with the new setting
        if ( czr_fn_is_customize_left_panel() )
          add_filter( 'option_tc_theme_options', 'czr_fn_wpml_customizer_options_transpose' );
        function czr_fn_wpml_customizer_options_transpose( $options ) {
          $options_to_transpose = apply_filters ( 'tc_wpml_customizer_translate_options', array(
            'page'     => ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) ? array( 'tc_featured_page_one', 'tc_featured_page_two', 'tc_featured_page_three' ) : array(),
            'category' => array( 'tc_blog_restrict_by_cat' )
            )
          );
          foreach ( $options_to_transpose as $type => $option_to_transpose )
            foreach ( $option_to_transpose as $option )
              if ( isset( $options[$option] ) )
                $options[$option] = czr_fn_wpml_object_id( $options[$option], $type);
          return $options;
        }

        //credits @Srdjan
        // Filter slides in admin screens
        add_action( '__attachment_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        add_action( '__post_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        // Update translated slide post meta
        add_action( 'edit_attachment', 'czr_fn_wpml_edit_attachment_action', 99 );
        // Pre-save hook
        add_filter( 'pre_update_option_tc_theme_options', 'czr_fn_wpml_pre_update_option_filter', 99 );

      }// end tc_wpml_admin_setup function

      // Front
      // If WPML string translator is active, translate/swap featured page buttons/text/link and slider
      if ( ! is_admin() ) {
        // String transaltion binders : requires wpml icl_t function
        if ( function_exists( 'icl_t') ) {
          /*** TC - WPML bind, wrap WPML string translator function into convenient tc functions ***/
          //define our icl_t wrapper for options filtered with tc_opt_{$option}
          if ( ! function_exists( 'czr_fn_wpml_t_opt' ) ) {
            function czr_fn_wpml_t_opt( $string ) {
              return czr_fn_wpml_t( $string, str_replace('tc_opt_', '', current_filter() ) );
            }
          }

          //define our icl_t wrapper
          if ( ! function_exists( 'czr_fn_wpml_t' ) ) {
            function czr_fn_wpml_t( $string, $opt ) {
              $tc_wpml_options_names = czr_fn_wpml_get_options_names_config();
              return icl_t( TC_WPML_CONTEXT, $tc_wpml_options_names[$opt], $string );
            }
          }
          /*** End TC - WPML bind ***/

          //get the options to translate
          $tc_wpml_options = array_keys( czr_fn_wpml_get_options_names_config() );

          //strings translation
          foreach ( $tc_wpml_options as $tc_wpml_option )
            add_filter("tc_opt_{$tc_wpml_option}", 'czr_fn_wpml_t_opt', 20 );

          //translates sliders? credits @Srdjan
          add_filter( 'tc_opt_tc_sliders', 'czr_fn_wpml_sliders_filter', 99 );

        }
        /*A) FP*/
        // Featured pages ids "translation"
        add_filter( 'tc_fp_id', 'czr_fn_wpml_page_id', 20 );
        function czr_fn_wpml_page_id( $fp_page_id ) {
          return czr_fn_wpml_object_id( $fp_page_id, 'page');
        }

        /*B) Tax */
        /**
        * Cat filtering (home/blog posts filtered by cat)
        *
        * AFAIK wpml needs to exactly know which kind of tax we're looking for, category, tag ecc..
        * @param array of term ids
        */
        function czr_fn_wpml_translate_cat( $cat_ids ){
          if ( ! ( is_array( $cat_ids ) && ! empty( $cat_ids ) ) )
            return $cat_ids;
          return array_unique( czr_fn_wpml_object_id( $cat_ids, 'category' ) );
        }
        //Translate category ids for the filtered posts in home/blog
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_wpml_translate_cat');
        /*end tax filtering*/

      }//end Front
    }//end wpml compat




    /**
    * The Events Calendar compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_the_events_calendar_compat() {
      /*
      * Are we in the Events list context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_events_list' ) ) ) {
        function czr_fn_is_tec_events_list() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_post_type_archive();
        }
      }
      /*
      * Are we in single Event context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_single_event' ) ) ) {
        function czr_fn_is_tec_single_event() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_single();
        }
      }
      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_tec_disable_tax_archive_title');
      function czr_fn_tec_disable_tax_archive_title( $bool ) {
        return czr_fn_is_tec_events_list() ? false : $bool;
      }

      // Events archive is displayed, wrongly, with our post lists classes, we have to prevent this
      add_filter( 'tc_post_list_controller', 'czr_fn_tec_disable_post_list');
      add_filter( 'tc_is_grid_enabled', 'czr_fn_tec_disable_post_list');
      function czr_fn_tec_disable_post_list( $bool ) {
        return czr_fn_is_tec_events_list() ? false : $bool;
      }

      // Now we have to display a post or page content
      add_filter( 'tc_show_single_post_content', 'czr_fn_tec_show_content' );
      function czr_fn_tec_show_content( $bool ) {
        //2 cases:
        //1 - in events lists - we force showing single post content
        //2 - in single events we have to prevent showing both page and post content
        if ( czr_fn_is_tec_events_list() )
          return true;
        else if( czr_fn_is_tec_single_event() )
          return false;
        return $bool;
      }

      // Force the tax name in the breadcrumb when list of events shown as 'Month'
      // The Events Calendar adds a filter on post_type_archive_title with __return_false callback
      // for their own reasons. This impacts on our breadcrumb 'cause we use the function post_type_archive_title() to build up the trail arg in posty_type_archives contexts.
      // What we do here is unhooking their callback before the breadcrumb is built and re-hook it after it has been displayed
      add_action( 'wp_head', 'czr_fn_tec_allow_display_breadcrumb_in_month_view');
      function czr_fn_tec_allow_display_breadcrumb_in_month_view() {
        if ( ! ( czr_fn_is_tec_events_list() && function_exists( 'tribe_is_month' ) && tribe_is_month() ) )
          return;

        add_filter( 'tc_breadcrumb_trail_args', 'czr_fn_tec_unhook_empty_post_type_archive_title');
        function czr_fn_tec_unhook_empty_post_type_archive_title( $args = null ) {
          remove_filter( 'post_type_archive_title', '__return_false', 10 );
          return $args;
        }
        add_filter( 'tc_breadcrumb_trail_display', 'czr_fn_tec_rehook_empty_post_type_archive_title', PHP_INT_MAX );
        function czr_fn_tec_rehook_empty_post_type_archive_title( $breadcrumb = null ) {
          add_filter( 'post_type_archive_title', '__return_false', 10 );
          return $breadcrumb;
        }
      }
      //disables post navigation in single tec pages
      add_filter( 'tc_show_post_navigation', 'czr_fn_tec_disable_post_navigation' );
      function czr_fn_tec_disable_post_navigation($bool) {
        return ( czr_fn_is_tec_single_event() ) ? false : $bool;
      }

      /*
      * Avoid php smartload image php parsing in events list content
      * See: https://github.com/presscustomizr/hueman/issues/285
      */
      add_filter( 'czr_disable_img_smart_load', 'czr_fn_tec_disable_img_smart_load_events_list', 999, 2);
      function czr_fn_tec_disable_img_smart_load_events_list( $_bool, $parent_filter ) {
        if ( 'the_content' == $parent_filter && czr_fn_is_tec_events_list() )
          return true;//disable
        return $_bool;
      }
    }//end the-events-calendar compat


    /**
    * Event Tickets compat hooks
    *
    * @package Customizr
    */
    private function czr_fn_set_event_tickets_compat() {
      /*
      * Are we in the tickets attendees registration
      */
      if ( ! ( function_exists( 'czr_fn_is_et_attendees_registration' ) ) ) {
        function czr_fn_is_et_attendees_registration() {
          return function_exists( 'tribe' ) && tribe( 'tickets.attendee_registration' )->is_on_page();
        }
      }
      // Workaround because of a bug on tec tickets that makes it require wp-content/themes/customizr/Custom Page Example (localized)
      // in place of wp-content/themes/customizr/custom-page.php
      add_filter( 'tribe_tickets_attendee_registration_page_template', 'czr_fn_et_ticket_fix_custom_page' );
      function czr_fn_et_ticket_fix_custom_page( $what ) {
        return str_replace( __( 'Custom Page Example', 'customizr' ), 'custom-page.php', $what );
      }

      // Attendees registration is displayed, wrongly, with our post lists classes, we have to prevent this
      add_filter( 'tc_post_list_controller', 'czr_fn_et_disable_on_attendees_registration');
      add_filter( 'tc_is_grid_enabled', 'czr_fn_et_disable_on_attendees_registration');
      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_et_disable_on_attendees_registration');
      //hide navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_et_disable_on_attendees_registration' );
      function czr_fn_et_disable_on_attendees_registration( $bool ) {
        return czr_fn_is_et_attendees_registration() ? false : $bool;
      }

      // Now we have to display a post or page content
      add_filter( 'tc_show_single_post_content', 'czr_fn_et_show_content' );
      function czr_fn_et_show_content( $bool ) {
        return czr_fn_is_et_attendees_registration() ? true : $bool;
      }

    }//end event-tickets compat




    /**
    * OptimizePress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_optimizepress_compat() {
      add_action('wp_print_scripts', 'czr_fn_op_dequeue_fancybox_js');
      function czr_fn_op_dequeue_fancybox_js(){
        if ( function_exists('is_le_page') ){
          /* Op Back End: Dequeue tc-scripts */
          if ( is_le_page() || defined('OP_LIVEEDITOR') ) {
            wp_dequeue_script('tc-scripts');
            wp_dequeue_script('tc-fancybox');
          }
          else {
            /* Front End: Dequeue Fancybox maybe already embedded in Customizr */
            wp_dequeue_script('tc-fancybox');
            //wp_dequeue_script(OP_SN.'-fancybox');
          }
        }
      }

      /* Remove fancybox loading icon*/
      add_filter('tc_js_params_plugin_compat', 'czr_fn_op_remove_fancyboxloading' );
      //@params = array() of js params
      function czr_fn_op_remove_fancyboxloading( $params ){
          return array_merge( $params, array( 'optimizepress_compat' => array( 'remove_fancybox_loading' => true ) ) );
      }
    }//end optimizepress compat



    /**
    * Sensei compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_sensei_compat() {
      //unkooks the default sensei wrappers and add customizr's content wrapper and action hooks
      global $woothemes_sensei;
      remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
      remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

      add_action('sensei_before_main_content', 'czr_fn_sensei_wrappers', 10);
      add_action('sensei_after_main_content', 'czr_fn_sensei_wrappers', 10);


      function czr_fn_sensei_wrappers() {
        switch ( current_filter() ) {
          case 'sensei_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                             break;

          case 'sensei_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                             break;
        }//end of switch on hook
      }//end of nested function

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_sensei_disable_tax_archive_title');
      function czr_fn_sensei_disable_tax_archive_title( $bool ){
        return ( function_exists('is_sensei') && ( is_sensei() || is_post_type_archive('sensei_message') ) ) ? false : $bool;
      }

      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_sensei_disable_post_navigation' );
      function czr_fn_sensei_disable_post_navigation($bool) {
        return ( function_exists('is_sensei') && is_sensei() || is_singular('sensei_message') ) ? false : $bool;
      }


      //in my courses page avoid displaying both page and single content
      add_filter( 'tc_show_single_post_content', 'czr_fn_sensei_disable_single_content_in_my_courses');
      function czr_fn_sensei_disable_single_content_in_my_courses( $bool ) {
        global $post;
        return is_page() && 'course' === $post->post_type ? false : $bool;
      }

    }//end sensei compat




    /**
    * Woocommerce compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_woocomerce_compat() {
      //unkooks the default woocommerce wrappersv and add customizr's content wrapper and action hooks
      remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
      remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
      add_action('woocommerce_before_main_content', 'czr_fn_woocommerce_wrappers', 10);
      add_action('woocommerce_after_main_content', 'czr_fn_woocommerce_wrappers', 10);

      //disable WooCommerce default breadcrumb
      if ( apply_filters( 'tc_disable_woocommerce_breadcrumb', true ) )
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

      function czr_fn_woocommerce_wrappers() {
        switch ( current_filter() ) {
          case 'woocommerce_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                                  break;

          case 'woocommerce_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                                  break;
        }//end of switch on hook
      }//end of nested function
      //Helper
      function czr_fn_wc_is_checkout_cart() {
        return ( function_exists( 'is_checkout' ) && function_exists( 'is_cart' ) ) && ( is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART') );
      }
      //Helper
      function czr_fn_woocommerce_shop_page_id( $id = null ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() && function_exists('wc_get_page_id') ) ? wc_get_page_id( 'shop' ) : $id;
      }
      //Helper
      function czr_fn_woocommerce_shop_enable( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() ) ? true : $bool;
      }
      //Helper
      function czr_fn_is_woocommerce_disable( $bool ) {
        return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }


      //enable fancybox for images in the wc short description
      add_filter( 'tc_enable_fancybox_in_wc_short_description', '__return_true' );


      //enable images smartload in the wc short description
      add_filter( 'tc_enable_img_smart_load_in_wc_short_description', '__return_true' );



      //when in the woocommerce shop page use the "shop" id
      add_filter( 'czr_id', 'czr_fn_woocommerce_shop_page_id' );

      // use Customizr title
      // initially used to display the edit button
      // doesn't work anymore, lets comment it
      /*
      add_filter( 'the_title', 'czr_fn_woocommerce_the_title' );
      function czr_fn_woocommerce_the_title( $_title ){
        if ( function_exists('is_woocommerce') && is_woocommerce() && ! is_page() )
            return apply_filters( 'tc_title_text', $_title );
        return $_title;
      }
      */
      //disable post lists in woocommerce contexts
      add_filter( 'tc_post_list_controller', 'czr_fn_is_woocommerce_disable');
      add_filter( 'tc_set_grid_hooks', 'czr_fn_is_woocommerce_disable');

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_is_woocommerce_disable' );


      //allow slider in the woocommerce shop page
      add_filter( 'tc_show_slider', 'czr_fn_woocommerce_shop_enable' );

      //allow page layout post meta in 'shop'
      add_filter( 'tc_is_page_layout', 'czr_fn_woocommerce_shop_enable' );

      //handles the woocomerce sidebar : removes action if sidebars not active
      if ( !is_active_sidebar( 'shop') ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
      }


      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_is_woocommerce_disable' );

      //removes post comment action on after_loop hook
      add_filter( 'tc_are_comments_enabled', 'czr_fn_is_woocommerce_disable' );

      //link smooth scroll: exclude woocommerce tabs
      add_filter( 'tc_anchor_smoothscroll_excl', 'czr_fn_woocommerce_disable_link_scroll' );
      function czr_fn_woocommerce_disable_link_scroll( $excl ){
        if ( false == esc_attr( czr_fn_opt('tc_link_scroll') ) ) return $excl;

        if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
          if ( ! is_array( $excl ) )
            $excl = array();

          if ( ! is_array( $excl['deep'] ) )
            $excl['deep'] = array() ;

          if ( ! is_array( $excl['deep']['classes'] ) )
              $excl['deep']['classes'] = array();

          $excl['deep']['classes'][] = 'wc-tabs';
          $excl['deep']['classes'][] = 'woocommerce-product-rating';
        }
        return $excl;
      }


      //changes customizr meta boxes priority (slider and layout not on top) if displaying woocommerce products in admin
      add_filter( 'tc_post_meta_boxes_priority', 'czr_fn_woocommerce_change_meta_boxes_priority' , 2 , 10 );
      function czr_fn_woocommerce_change_meta_boxes_priority($priority , $screen) {
        return ( 'product' == $screen ) ? 'default' : $priority ;
      }


      // Allow HEADER CART OPTIONS in the customizer
      // Returns a callback function needed by 'active_callback' to enable the options in the customizer
      add_filter( 'tc_woocommerce_options_enabled', 'czr_fn_woocommerce_options_enabled_cb' );
      function czr_fn_woocommerce_options_enabled_cb() {
        return function_exists( 'WC' ) ? '__return_true' : '__return_false';
      }

      /* rendering the cart icon in the header */
      //narrow the tagline
      add_filter( 'tc_tagline_class', 'czr_fn_woocommerce_force_tagline_width', 100 );
      function czr_fn_woocommerce_force_tagline_width( $_class ) {
        return 1 == esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart' ) ) ? 'span6' : $_class ;
      }

      // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
      add_filter( 'woocommerce_add_to_cart_fragments', 'czr_fn_woocommerce_add_to_cart_fragment' );
      function czr_fn_woocommerce_add_to_cart_fragment( $fragments ) {
        if ( 1 == esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart' ) ) ) {
          $_cart_count = WC()->cart->get_cart_contents_count();
          $fragments['span.tc-wc-count'] = sprintf( '<span class="count btn-link tc-wc-count">%1$s</span>', $_cart_count ? $_cart_count : '' );
        }
        return $fragments;
      }

      //print the cart menu in the header
      add_action( '__navbar', 'czr_fn_woocommerce_header_cart', is_rtl() ? 9 : 19 );
      function czr_fn_woocommerce_header_cart() {
        if ( 1 != esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          return;

        if ( ! function_exists( 'WC' ) )
          return;

        $_main_item_class = '';
        $_cart_count      = WC()->cart->get_cart_contents_count();
        //highlight the cart icon when in the Cart or Ceckout page
        if ( czr_fn_wc_is_checkout_cart() ) {
          $_main_item_class = 'current-menu-item';
        }

        // fix for: https://github.com/presscustomizr/customizr/issues/1223
        // WC_Cart::get_cart_url is <strong>deprecated</strong> since version 2.5! Use wc_get_cart_url instead.
        //
        if ( function_exists( 'wc_get_cart_url' ) ) {
            $wc_cart_url = esc_url( wc_get_cart_url() );
        } else {
            $wc_cart_url = esc_url( WC()->cart->get_cart_url() );
        }

       ?>
       <div class="tc-wc-menu tc-open-on-hover span1">
         <ul class="tc-wc-header-cart nav tc-hover-menu">
           <li class="<?php echo esc_attr( $_main_item_class ); ?> menu-item">
             <a class="cart-contents" href="<?php echo $wc_cart_url; ?>" title="<?php _e( 'View your shopping cart', 'customizr' ); ?>">
               <span class="count btn-link tc-wc-count"><?php echo $_cart_count ? $_cart_count : '' ?></span>
            </a>
            <?php
            ?>
            <?php if ( ! czr_fn_wc_is_checkout_cart() ) : //do not display the dropdown in the cart or checkout page ?>
              <ul class="dropdown-menu">
               <li>
                 <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                </li>
              </ul>
            <?php endif; ?>
           </li>
          </ul>
        </div>
      <?php
      }

      //add woommerce header cart classes to the header (sticky enabled)
      add_filter( 'tc_header_classes'   , 'czr_fn_woocommerce_set_header_classes');
      function czr_fn_woocommerce_set_header_classes( $_classes ) {
        if ( 1 == esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          $_classes[]          = ( 1 != esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart_sticky' ) ) ) ? 'tc-wccart-off' : 'tc-wccart-on';
        return $_classes;
      }

      //add woocommerce header cart CSS
      add_filter( 'tc_user_options_style', 'czr_fn_woocommerce_header_cart_css');
      function czr_fn_woocommerce_header_cart_css( $_css ) {
        if ( 1 != esc_attr( czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          return $_css;

        /* The only real decision I took here is the following:
        * I let the "count" number possibily overflow the parent (span1) width
        * so that as it grows it won't break on a new line. This is quite an hack to
        * keep the cart space as small as possible (span1) and do not hurt the tagline too much (from span7 to span6). Also nobody will, allegedly, have more than 10^3 products in its cart
        */
        $_header_layout      = esc_attr( czr_fn_opt( 'tc_header_layout') );
        $_resp_pos_css       = 'right' == $_header_layout ? 'float: left;' : '';
        $_wc_t_align         = 'left';

        //dropdown top arrow, as we open the drodpdown on the right we have to move the top arrow accordingly
        $_dd_top_arrow       = '.navbar .tc-wc-menu .nav > li > .dropdown-menu:before { right: 9px; left: auto;} .navbar .tc-wc-menu .nav > li > .dropdown-menu:after { right: 10px; left: auto; }';

        //rtl custom css
        if ( is_rtl() ) {
          $_wc_t_align       = 'right';
          $_dd_top_arrow     = '';
        }

        return sprintf( "%s\n%s",
              $_css,
              ".sticky-enabled .tc-header.tc-wccart-off .tc-wc-menu { display: none; }
               .sticky-enabled .tc-tagline-off.tc-wccart-on .tc-wc-menu { margin-left: 0; margin-top: 3px; }
               .sticky-enabled .tc-tagline-off.tc-wccart-on .btn-toggle-nav { margin-top: 5px; }
               .tc-header .tc-wc-menu .nav { text-align: right; }
               $_dd_top_arrow
               .tc-header .tc-wc-menu .dropdown-menu {
                  right: 0; left: auto; width: 250px; padding: 2px;
               }
               .tc-header .tc-wc-menu {
                 float: right; clear:none; margin-top: 1px;
               }
               .tc-header .tc-wc-menu .nav > li {
                 float:none;
               }
               .tc-wc-menu ul.dropdown-menu .buttons a,
               .tc-wc-menu ul {
                 width: 100%;
                 -webkit-box-sizing: border-box;
                 -moz-box-sizing: border-box;
                 box-sizing: border-box;
               }
               .tc-wc-menu ul.dropdown-menu .buttons a {
                 margin: 10px 5px 0 0px; text-align: center;
               }
               .tc-wc-menu .nav > li > a:before {
                 content: '\\f07a';
                 position:absolute;
                 font-size:1.35em; left: 0;
               }
               .tc-header .tc-wc-menu .nav > li > a {
                 position: relative;
                 padding-right: 0 !important;
                 padding-left: 0 !important;
                 display:inline-block;
                 border-bottom: none;
                 text-align: right;
                 height: 1em;
                 min-width:1.8em;
               }
               .tc-wc-menu .count {
                 font-size: 0.7em;
                 margin-left: 2.1em;
                 position: relative;
                 top: 1em;
                 pointer-events: none;
               }
               .tc-wc-menu .woocommerce.widget_shopping_cart li {
                 padding: 0.5em;
               }
               .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart p,
               .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart li {
                 padding-right: 1em;
                 padding-left: 1em;
                 text-align: $_wc_t_align;
                 font-size: inherit; font-family: inherit;
               }
               .tc-wc-menu .widget_shopping_cart .product_list_widget li a.remove {
                 position: relative; float: left; top: auto; margin-right: 0.2em;
               }
               .tc-wc-menu .widget_shopping_cart .product_list_widget {
                 max-height: 40vh;
                 overflow-y: auto;
                 padding: 1em 0;
               }
               @media (max-width: 979px) {
                .tc-wc-menu[class*=span] { width: auto; margin-top:7px; $_resp_pos_css }
                .tc-wc-menu .dropdown-menu { display: none !important;}
              }
              @media (max-width: 767px) { .sticky-enabled .tc-wccart-on .brand { width: 50%;} }
        ");
      }
      /*end rendering the cart icon in the header */
    }//end woocommerce compat


    /*
    * same in czr modern except for the filter prefix (tc_ -> czr_)
    */
    /**
    * Visual Composer compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_vc_compat() {
      //link smooth scroll: exclude all anchor links inside vc wrappers (.vc_row)
      add_filter( 'tc_anchor_smoothscroll_excl', 'czr_fn_vc_disable_link_scroll' );
      function czr_fn_vc_disable_link_scroll( $excl ){
        if ( false == esc_attr( czr_fn_opt('tc_link_scroll') ) ) return $excl;

        if ( ! is_array( $excl ) )
          $excl = array();

        if ( ! is_array( $excl['deep'] ) )
          $excl['deep'] = array() ;

        if ( ! is_array( $excl['deep']['classes'] ) )
            $excl['deep']['classes'] = array();

        $excl['deep']['classes'][] = 'vc_row';

        return $excl;
      }
    }//end woocommerce compat


    /**
    * Disqus Comment System compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_disqus_compat() {
      if ( ! function_exists( 'czr_fn_disqus_comments_enabled' ) ) {
        function czr_fn_disqus_comments_enabled() {
          return function_exists( 'dsq_is_installed' ) && function_exists( 'dsq_can_replace' )
                 && dsq_is_installed() && dsq_can_replace();
        }
      }
      //replace the default comment link anchor with a more descriptive disqus anchor
      add_filter( 'tc_bubble_comment_anchor', 'czr_fn_disqus_bubble_comment_anchor' );
      function czr_fn_disqus_bubble_comment_anchor( $anchor ) {
        return czr_fn_disqus_comments_enabled() ? '#tc-disqus-comments' : $anchor;
      }
      //wrap disqus comments template in a convenient div
      add_action( 'tc_before_comments_template' , 'czr_fn_disqus_comments_wrapper' );
      add_action( 'tc_after_comments_template'  , 'czr_fn_disqus_comments_wrapper' );
      function czr_fn_disqus_comments_wrapper() {
        if ( ! czr_fn_disqus_comments_enabled() )
          return;

        switch ( current_filter() ) {
          case 'tc_before_comments_template' : echo '<div id="tc-disqus-comments">';
                                               break;
          case 'tc_after_comments_template'  : echo '</div>';
        }
      }
    }//end disqus compat


    /*
    * same in czr modern except for the filter prefix (tc_ -> czr_)
    */
    /**
    * Ultimate Responsive Image Slider compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_uris_compat() {
      add_filter ( 'tc_img_smart_load_options', 'czr_fn_uris_disable_img_smartload' ) ;
      function czr_fn_uris_disable_img_smartload( $options ){
        if ( ! is_array( $options ) )
          $options = array();

        if ( ! is_array( $options['opts'] ) )
          $options['opts'] = array();

        if ( ! is_array( $options['opts']['excludeImg'] ) )
          $options['opts']['excludeImg'] = array();

        $options['opts']['excludeImg'][] = '.sp-image';

        return $options;
      }
    }//end uris compat


    /**
    * LearnPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.5+
    */
    private function czr_fn_set_lp_compat() {
      //do nothing if is admin
      if ( is_admin() ) {
        return;
      }
      //Helpers
      if ( ! function_exists( 'tc_lp_is_learnpress' ) ):
        function tc_lp_is_learnpress() {
          return function_exists( 'is_learnpress' ) && is_learnpress();
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_disable' ) ):
        function tc_lp_is_learnpress_disable( $bool ) {
          return tc_lp_is_learnpress() ? false : $bool;
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_enable' ) ):
        function tc_lp_is_learnpress_enable( $bool ) {
          return tc_lp_is_learnpress() ? true : $bool;
        }
      endif;
      if ( ! function_exists( 'tc_lp_is_learnpress_archive_disable' ) ):
        function tc_lp_is_learnpress_archive_disable( $bool ) {
          if ( function_exists( 'learn_press_is_course' )  && function_exists( 'learn_press_is_quiz' ) ) {
            return tc_lp_is_learnpress() && ! ( learn_press_is_course() || learn_press_is_quiz() )  ? false : $bool;
          }
          return $bool;
        }
      endif;


      //lp filters template_include falling back on the page.php theme template
      //without checking its existence, since we have no page.php template, we have to fall back
      //on index.php if what that filter returns is false ().
      //is_learnpress() function should be our reference conditional tag
      if ( ! function_exists( 'tc_lp_maybe_fall_back_on_index' ) ):
        function tc_lp_maybe_fall_back_on_index( $template ) {
          if ( ! tc_lp_is_learnpress() )
            return $template;

          if ( ! empty( $template ) )
            return $template;

          return get_template_part( 'index' );
        }
      endif;
      //See: plugins\learnpress\inc\class-lp-request-handler.php::process_request
      //where lp processes the course Enroll request at template_include|50
      //https://github.com/presscustomizr/customizr/issues/1589
      add_filter( 'template_include', 'tc_lp_maybe_fall_back_on_index', 100 );


      // Disable post lists and single views in lp contexts
      add_filter( 'tc_post_list_controller', 'tc_lp_is_learnpress_disable');
      add_filter( 'tc_show_single_post_content', 'tc_lp_is_learnpress_disable');
      // Disable archive headings
      add_action( 'tc_show_tax_archive_title', 'tc_lp_is_learnpress_archive_disable' );

      //enable page view
      add_filter( 'tc_show_page_content', 'tc_lp_is_learnpress_enable');

      //do not display metas in lp archives
      add_filter( 'tc_opt_tc_show_post_metas', 'tc_lp_is_learnpress_archive_disable' );

      //do not display post navigation, lp uses its own, when relevant
      add_filter( 'tc_opt_tc_show_post_navigation', 'tc_lp_is_learnpress_disable' );

      //disable lp breadcrumb, we'll use our own
      remove_action( 'learn_press_before_main_content', 'learn_press_breadcrumb' );
    }//end lp compat



    /* same in czr modern */
    /*
    * Coauthors-Plus plugin compat hooks
    */
    private function czr_fn_set_coauthors_compat() {
      if ( !function_exists( 'coauthors_ids' )  ) {
        return;
      }

      add_filter( 'tc_post_author_id', 'tc_coauthors_post_author_ids' );
      if ( !function_exists( 'tc_coauthors_post_author_ids' ) ) {
        function tc_coauthors_post_author_ids() {
          $author_ids = coauthors_ids( $between = ',', $betweenLast = ',', $before = null, $after = null, $echo = false );
          return explode(  ',' , $author_ids );
        }
      }
    }



    /* same in czr modern */
    /**
    * TC Unlimited Featured Pages compat hooks
    * Since Customizr 3.4.24 we changed the functions and class prefixes
    * Olf fpu versions might refer to them throwing PHP errors
    * with the code below we basically "soft-disable" the plugin
    * without actyally disabling it to allow the user to update it
    * @package Customizr
    * @since Customizr 3.4.24
    */
    private function czr_fn_set_tc_unlimited_featured_pages_compat() {
      //This has to be fired after : tc_generates_featured_pages | 10 (priority)
      if ( class_exists( 'TC_fpu' ) &&  version_compare( TC_fpu::$instance -> plug_version, '2.0.24', '<' ) ) {

        //back
        if ( method_exists( 'TC_back_fpu', 'tc_add_controls_class' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_add_controls_class' ) ,10,1);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_controls_js_css' ) )
          remove_action ( 'customize_controls_enqueue_scripts' , array(TC_back_fpu::$instance , 'tc_customize_controls_js_css' ), 100);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_register' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_customize_register' ) , 20, 1 );

        if ( method_exists( 'TC_back_fpu', 'tc_customize_preview_js' ) )
          remove_action ( 'customize_preview_init'     , array( TC_back_fpu::$instance , 'tc_customize_preview_js' ));

        //do not remove customizr free featured pages option panel
        if ( method_exists( 'TC_fpu', 'tc_delete_fp_options' ) )
          remove_filter ( 'tc_front_page_option_map'   , array( TC_fpu::$instance , 'tc_delete_fp_options' ), 20 );

        //front
        if ( class_exists( 'TC_front_fpu' ) ) {
          if ( method_exists( 'TC_front_fpu', 'tc_set_fp_hook' ) )
            remove_action( 'template_redirect'         , array( TC_front_fpu::$instance , 'tc_set_fp_hook'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_set_colors' ) )
            remove_action( 'wp_head'                   , array( TC_front_fpu::$instance , 'tc_set_colors'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_enqueue_plug_resources' ) )
            remove_action( 'wp_enqueue_scripts'        , array( TC_front_fpu::$instance , 'tc_enqueue_plug_resources') );
        }

        //needed as some plugins (lang) will check the TC_fpu class existence
        add_filter( 'tc_other_plugins_force_fpu_disable', '__return_false' );
      }
    }


    /**
    * CUSTOMIZR WRAPPERS
    * print the customizr wrappers
    *
    * @since 3.3+
    *
    * originally used for woocommerce compatibility
    */
    function czr_fn_mainwrapper_start() {
      ?>
      <div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

        <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

        <div class="container" role="main">
          <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">

            <?php do_action( '__before_article_container'); ##hook of left sidebar?>

              <div id="content" class="<?php echo implode(' ', apply_filters( 'tc_article_container_class' , array( CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'class' ) , 'article-container' ) ) ) ?>">

                <?php do_action ('__before_loop');##hooks the header of the list of post : archive, search... ?>
      <?php
    }

    function czr_fn_mainwrapper_end() {
      ?>
                <?php do_action ('__after_loop');##hook of the comments and the posts navigation with priorities 10 and 20 ?>

              </div><!--.article-container -->

              <?php do_action( '__after_article_container'); ##hook of left sidebar?>

            </div><!--.row -->
        </div><!-- .container role: main -->

        <?php do_action( '__after_main_container' ); ?>

      </div><!-- //#main-wrapper -->
      <?php
    }


    /* same in czr modern */
    public function czr_fn_get_string_options_to_translate() {
      $string_options = array(
        'tc_front_slider',
        'tc_posts_slider_button_text',
        'tc_tag_title',
        'tc_cat_title',
        'tc_author_title',
        'tc_search_title',
        'tc_social_in_sidebar_title',
      );
      if ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu')  ) && ! class_exists('TC_fpc') ) {
        $fp_areas = CZR___::$instance -> fp_ids;
        foreach ( $fp_areas as $fp_area )
          $string_options[] = 'tc_featured_text_' . $fp_area;

        $string_options[] = 'tc_featured_page_button_text';
      }
      return apply_filters( 'tc_get_string_options_to_translate', $string_options );
    }
  }//end of class
endif;

?><?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
*
*/
if ( ! class_exists( 'CZR_utils_settings_map' ) ) :
class CZR_utils_settings_map {

      static $instance;
      private $is_wp_version_before_4_0;

      function __construct () {

            self::$instance =& $this;
            //declare a private property to check wp version >= 4.0
            global $wp_version;
            $this -> is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;

            //require all the files needed by the new settings map - they contain functions used in core/utils/class-fire-utils_settings_map.php
            if ( file_exists( TC_BASE . 'core/core-settings-map.php' ) ) {
                  require_once( TC_BASE . 'core/core-settings-map.php' );
            }


      }//end of construct



      /**
      * Defines sections, settings and function of customizer and return and array
      * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
      *
      * @package Customizr
      * @since Customizr 3.0
      * TODO: unify this
      */
      public function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {

            //Hook callbacks are defined in core/utils/class-fire-utils_settings_map.php
            if ( ! empty( CZR___::$customizer_map ) ) {
                  $_customizer_map = CZR___::$customizer_map;
            }
            else {

                  //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
                  add_filter( 'tc_add_panel_map'            , 'czr_fn_popul_panels_map' );
                  add_filter( 'tc_remove_section_map'       , 'czr_fn_popul_remove_section_map' );
                  //theme switcher's enabled when user opened the customizer from the theme's page
                  add_filter( 'tc_remove_section_map'       , 'czr_fn_set_theme_switcher_visibility' );
                  add_filter( 'tc_add_section_map'          , 'czr_fn_popul_section_map' );
                  //add controls to the map
                  add_filter( 'tc_add_setting_control_map'  , 'czr_fn_popul_setting_control_map', 10, 2 );

                  //FILTER SPECIFIC SETTING-CONTROL MAPS
                  //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
                  add_filter( 'czr_fn_front_page_option_map' ,'czr_fn_generates_featured_pages', 10, 2 );


                  //MAYBE FORCE REMOVE SECTIONS (e.g. CUSTOM CSS section for wp >= 4.7 )
                  add_filter( 'tc_add_section_map'           , 'czr_fn_force_remove_section_map' );

                  /* CZR_4 compat */
                  /* ADD SPECIFIC SECTION SETTINGS */
                  //add controls to the map
                  add_filter( 'tc_add_section_map'                , array( $this, 'czr_fn_popul_section_map' ) );
                  add_filter( 'tc_add_setting_control_map'        , array( $this, 'czr_fn_popul_setting_control_map' ), 0, 2 );


                  //CACHE THE GLOBAL CUSTOMIZER MAP
                  $_customizer_map = array_merge(
                      array( 'add_panel'           => apply_filters( 'tc_add_panel_map', array() ) ),
                      array( 'remove_section'      => apply_filters( 'tc_remove_section_map', array() ) ),
                      array( 'add_section'         => apply_filters( 'tc_add_section_map', array() ) ),
                      array( 'add_setting_control' => apply_filters( 'tc_add_setting_control_map', array(), $get_default ) )
                  );
                  CZR___::$customizer_map = $_customizer_map;

            }

            if ( is_null($what) ) {
                  return apply_filters( 'tc_customizer_map', $_customizer_map );
            }

            $_to_return = $_customizer_map;
            switch ( $what ) {
                  case 'add_panel':
                        $_to_return = $_customizer_map['add_panel'];
                  break;
                  case 'remove_section':
                        $_to_return = $_customizer_map['remove_section'];
                  break;
                  case 'add_section':
                        $_to_return = $_customizer_map['add_section'];
                  break;
                  case 'add_setting_control':
                        $_to_return = $_customizer_map['add_setting_control'];
                  break;
            }
            return $_to_return;

      }


      /**
      * Populate the control map
      * hook : 'tc_add_setting_control_map'
      * => loops on a callback list, each callback is a section setting group
      * @return array()
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_popul_setting_control_map( $_map, $get_default = null ) {

            $_new_map = array();

            $_settings_sections = array(

                'czr_fn_icons_option_map', //Removed in modern style

            );

            foreach ( $_settings_sections as $_section_cb ) {
                  if ( ! method_exists( $this , $_section_cb ) )
                        continue;

                  //applies a filter to each section settings map => allows plugins (featured pages for ex.) to add/remove settings
                  //each section map takes one boolean param : $get_default
                  $_section_map = apply_filters(
                        $_section_cb,
                        call_user_func( array( $this, $_section_cb ), $get_default )
                  );

                  if ( ! is_array( $_section_map) )
                        continue;

                  $_new_map = array_merge( $_new_map, $_section_map );
            }//foreach

            /***** FILTER SPECIFIC SETTING-CONTROL MAPS defined in c4 ****/
            //alter czr4 settings sections
            $_alter_settings_sections = array(
                  //GLOBAL SETTINGS
                  'czr_fn_site_identity_option_map',
                  'czr_fn_skin_option_map',
                  'czr_fn_links_option_map',
                  'czr_fn_formatting_option_map',
                  'czr_fn_images_option_map',
                  //HEADER
                  'czr_fn_header_design_option_map',
                  'czr_fn_header_desktop_option_map',
                  'czr_fn_header_mobile_option_map',
                  'czr_fn_navigation_option_map',
                  //CONTENT
                  'czr_fn_front_page_option_map',
                  'czr_fn_layout_option_map',
                  'czr_fn_post_metas_option_map',
                  'czr_fn_post_list_option_map',
                  'czr_fn_comment_option_map',
                  'czr_fn_single_post_option_map',
                  //SIDEBARS
                  'czr_fn_sidebars_option_map',
                  'czr_fn_responsive_option_map',
                  //FOOTER
                  'czr_fn_footer_global_settings_option_map',
                  //WOOCOMMERCE PANEL OPTIONS
                  'czr_fn_woocommerce_option_map'
            );

            foreach ( $_alter_settings_sections as $_alter_section_cb ) {
                  if ( ! method_exists( $this , $_alter_section_cb ) )
                        continue;
                  add_filter( $_alter_section_cb, array( $this, $_alter_section_cb ), 10, 2 );
            }//foreach

            return array_merge( $_map, $_new_map );

      }





      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : GLOBAL SETTINGS
      *******************************************************************************************************
      ******************************************************************************************************/

      /*-----------------------------------------------------------------------------------------------------
                                     LOGO & FAVICON SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_site_identity_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_title_next_logo',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            global $wp_version;

            $_to_add = array(
                  'tc_sticky_logo_upload'  => array(
                                    'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                    'label'     =>  __( 'Sticky Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                    'section'   =>  'title_tagline' ,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'priority'  => 20,
                            //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                                    'width'     => 75,
                                    'height'    => 30,
                                    'flex_width' => true,
                                    'flex_height' => true,
                                    //to keep the selected cropped size
                                    'dst_width'  => false,
                                    'dst_height'  => false,
                                    'notice'    => __( "Use this upload control to specify a different logo on sticky header mode." , 'customizr')
                  ),
                  //favicon
                  'tc_fav_upload' => array(
                                    'control'   =>  'CZR_Customize_Upload_Control' ,
                                    'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                                    'title'     => __( 'FAVICON' , 'customizr'),
                                    'section'   =>  'title_tagline' ,
                                    'type'      => 'tc_upload',
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'priority'  => 25,
                  )
            );

            return array_merge( $_map, $_to_add );
      }




      /*-----------------------------------------------------------------------------------------------------
                                        SKIN SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_skin_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_skin_color',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }


            $_to_add = array(

                  'tc_skin'     => array(
                              'default'   => czr_fn_user_started_before_version( '3.4.32' , '1.2.31') ? 'blue3.css' : 'grey.css',
                              'control'   => 'CZR_controls' ,
                              'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                              'section'   =>  'skins_sec' ,
                              'type'      =>  'select' ,
                              'choices'    =>  $get_default ? null : $this -> czr_fn_build_skin_list(),
                              'transport'   =>  'postMessage',
                              'notice'    => __( 'Disabled if the random option is on.' , 'customizr' )
                  ),
                  'tc_skin_random' => array(
                              'default'   => 0,
                              'control'   => 'CZR_controls',
                              'label'     => __('Randomize the skin', 'customizr'),
                              'section'   => 'skins_sec',
                              'type'      => 'nimblecheck',
                              'notice'    => __( 'Apply a random color skin on each page load.' , 'customizr' )
                  ),
            );

            return array_merge( $_map, $_to_add );
      }


      /*-----------------------------------------------------------------------------------------------------
                                     LINKS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_links_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            $_to_add = array(
                  'tc_link_hover_effect'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Fade effect on link hover" , 'customizr' ),
                                'section'       => 'formatting_sec' ,
                                'type'          => 'nimblecheck' ,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
                  ),
            );

            return array_merge( $_map, $_to_add );
      }



      function czr_fn_formatting_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            $_to_add = array(
                  'tc_ext_link_style'  =>  array(
                                    'default'       => 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display an icon next to external links" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                    'transport'     => 'postMessage'
                  ),

                  'tc_ext_link_target'  =>  array(
                                    'default'       => 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Open external links in a new tab" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                    'transport'     => 'postMessage'
                  ),
                  'tc_enable_dropcap'  =>  array(
                                    'default'       => 0,
                                    'title'         => __( 'Drop caps', 'customizr'),
                                    'label'         => __('Enable drop caps' , 'customizr'),
                                    'control'       => 'CZR_controls' ,
                                    'notice'         => __( "Apply a drop cap to the first paragraph of your post / page content" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'nimblecheck',
                  ),
                  'tc_dropcap_minwords'  =>  array(
                                    'default'       => 50,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Apply a drop cap when the paragraph includes at least the following number of words :" , "customizr" ),
                                    'notice'         => __( "(number of words)" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'number' ,
                                    'step'          => 1,
                                    'min'           => 1,
                  ),
                  'tc_dropcap_design' => array(
                                    'default'     => 'skin-shadow',
                                    'control'     => 'CZR_controls',
                                    'label'       => __( 'Drop cap style' , 'customizr' ),
                                    'section'     => 'formatting_sec',
                                    'type'      =>  'select' ,
                                    'choices'     => array(
                                            'skin-shadow'    => __( "Primary theme color with a shadow" , 'customizr' ),
                                            'simple-black'   => __( 'Simple black' , 'customizr' ),
                                    ),
                  ),
                  'tc_post_dropcap'  =>  array(
                                    'default'       => 0,
                                    'label'         => __('Enable drop caps in posts' , 'customizr'),
                                    'control'       => 'CZR_controls' ,
                                    'notice'         => __( "Apply a drop cap to the first paragraph of your single posts content" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'nimblecheck',
                  ),
                  'tc_page_dropcap'  =>  array(
                                    'default'       => 0,
                                    'label'         => __('Enable drop caps in pages' , 'customizr'),
                                    'control'       => 'CZR_controls' ,
                                    'notice'         => __( "Apply a drop cap to the first paragraph of your pages" , "customizr" ),
                                    'section'       => 'formatting_sec' ,
                                    'type'          => 'nimblecheck',
                  )
            );

            return array_merge( $_map, $_to_add );
      }


      /*-----------------------------------------------------------------------------------------------------
                                     ICONS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_icons_option_map( $get_default = null ) {

            return array(
                  'tc_show_title_icon'  =>  array(
                                    'default'       => 1,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "Display icons next to titles" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 10,
                                    'notice'    => __( 'When this option is checked, a contextual icon is displayed next to the titles of pages, posts, archives, and WP built-in widgets.' , 'customizr' ),
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_page_title_icon'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display a page icon next to the page title" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 20,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_post_title_icon'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "Display a post icon next to the single post title" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 30,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_archive_title_icon'  =>  array(
                                    'default'       => 1,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "Display an icon next to the archive title" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'notice'    => __( 'When this option is checked, an archive type icon is displayed in the heading of every types of archives, on the left of the title. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                    'priority'      => 40,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_post_list_title_icon'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.0' , '1.0.11' ) ? 1 : 0,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "Display an icon next to each post title in an archive page" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'notice'    => __( 'When this option is checked, a post type icon is displayed on the left of each post titles in an archive page. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                    'priority'      => 50,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_sidebar_widget_icon'  =>  array(
                                    'default'       => 1,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "WP sidebar widgets : display icons next to titles" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 60,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_footer_widget_icon'  =>  array(
                                    'default'       => 1,
                                    'control'     => 'CZR_controls' ,
                                    'label'         => __( "WP footer widgets : display icons next to titles" , 'customizr' ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 70,
                                    'transport'   => 'postMessage'
                  )
            );
      }



      /*-----------------------------------------------------------------------------------------------------
                                     IMAGE SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_images_option_map( $_map, $get_default = null ) {

            global $wp_version;


            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

                  'tc_fancybox_autoscale' =>  array(
                                    'default'       => 1,
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                                    'section'     => 'images_sec' ,
                                    'type'        => 'nimblecheck' ,
                                    'priority'    => 2,
                                    'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
                  ),

                 'tc_display_slide_loader'  =>  array(
                                    'default'       => 1,
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( "Sliders : display on loading icon before rendering the slides" , 'customizr' ),
                                    'section'     => 'images_sec' ,
                                    'type'        => 'nimblecheck' ,
                                    'priority'    => 15,
                                    'notice'    => __( 'When checked, this option displays a loading icon when the slides are being setup.' , 'customizr' ),
                  ),

            );

            //add responsive image settings for wp >= 4.4
            if ( version_compare( $wp_version, '4.4', '>=' ) ) {
                  $_to_add[ 'tc_resp_slider_img' ] =  array(
                                    'default'     => 0,
                                    'control'     => 'CZR_controls' ,
                                    'label'       => __( 'Improve your page speed by loading smaller slider images for mobile devices' , 'customizr' ),
                                    'section'     => 'images_sec' ,
                                    'type'        => 'nimblecheck' ,
                                    'priority'    => 24,
                                    'ubq_section'   => array(
                                        'section' => 'performances_sec',
                                        'priority' => '1'
                                    )
                  );

            }

            return array_merge( $_map, $_to_add );
      }







      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : HEADER
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                     HEADER DESIGN AND LAYOUT
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_header_design_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_header_skin',
                  'tc_header_custom_bg_color',
                  'tc_header_custom_fg_color',
                  'tc_highlight_contextually_active_menu_items',
                  'tc_header_transparent_home',
                  'tc_home_header_skin',
                  'tc_header_no_borders',
                  'tc_header_title_underline',
                  'tc_header_show_topbar',
                  'tc_header_show_socials',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            //to add
            $_to_add  = array(
                  'tc_social_in_header' =>  array(
                                    'default'       => 1,
                                    'label'       => __( 'Social links in header' , 'customizr' ),
                                    'control'   =>  'CZR_controls' ,
                                    'section'     => 'header_layout_sec',
                                    'type'        => 'nimblecheck' ,
                                    'priority'      => 11,
                                    'transport'    => ( czr_fn_is_partial_refreshed_on() ) ? 'postMessage' : 'refresh',
                                    'ubq_section'   => array(
                                        'section' => 'socials_sec',
                                        'priority' => '1'
                                    ),

                  ),
                  'tc_show_tagline'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display the tagline in the header" , "customizr" ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 15,
                                    'transport'    => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                                    'ubq_section'   => array(
                                                        'section' => 'title_tagline',
                                                        'priority' => '30'
                                                     )
                  ),
                  'tc_woocommerce_header_cart' => array(
                                   'default'   => 1,
                                   'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( "Display the shopping cart in the header" , 'customizr' ) ),
                                   'control'   => 'CZR_controls' ,
                                   'section'   => 'header_layout_sec',
                                   'notice'    => __( "WooCommerce: check to display a cart icon showing the number of items in your cart next to your header's tagline.", 'customizr' ),
                                   'type'      => 'nimblecheck' ,
                                   'priority'  => 18,
                                   'active_callback' => apply_filters( 'tc_woocommerce_options_enabled', '__return_false' )
                  ),
                  'tc_display_boxed_navbar'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.13', '1.0.18' ) ? 1 : 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display menu in a box" , 'customizr' ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 25,
                                    'transport'     => 'postMessage',
                                    'notice'        => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
                  ),
                  'tc_sticky_header'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Sticky header settings' , 'customizr'),
                                    'label'         => __( "Sticky on scroll" , 'customizr' ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 30,
                                    'transport'     => 'postMessage',
                                    'notice'    => __( 'If checked, this option makes the header stick to the top of the page on scroll down.' , 'customizr' )
                  ),
                  'tc_sticky_show_tagline'  =>  array(
                                    'default'       => 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 40,
                                    'transport'     => 'postMessage',
                  ),
                  'tc_woocommerce_header_cart_sticky' => array(
                                    'default'   => 1,
                                    'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( "Sticky header: display the shopping cart" , "customizr" ) ),
                                    'control'   => 'CZR_controls' ,
                                    'section'   => 'header_layout_sec',
                                    'type'      => 'nimblecheck' ,
                                    'priority'  => 45,
                                    'transport' => 'postMessage',
                                    'active_callback' => apply_filters( 'tc_woocommerce_options_enabled', '__return_false' ),
                                    'notice'    => __( 'WooCommerce: if checked, your WooCommerce cart icon will remain visible when scrolling.' , 'customizr' )
                  ),
                  'tc_sticky_show_title_logo'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Sticky header : display the title / logo" , "customizr" ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 50,
                                    'transport'     => 'postMessage',
                  ),
                  'tc_sticky_show_menu'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Sticky header : display the menu" , "customizr" ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 60,
                                    'transport'     => 'postMessage',
                                    'notice'        => __('Also applied to the secondary menu if any.' , 'customizr')
                  ),
            );

            return array_merge( $_map, $_to_add );

      }


      function czr_fn_header_desktop_option_map( $_map, $get_default = null ) {
            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_header_desktop_search',
                  'tc_header_desktop_wc_cart',
                  'tc_header_desktop_tagline',
                  'tc_header_desktop_to_stick',
                  'tc_header_desktop_sticky',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            return $_map;
      }


      function czr_fn_header_mobile_option_map( $_map, $get_default = null ) {
            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_header_mobile_search',
                  'tc_header_mobile_wc_cart',
                  'tc_header_mobile_tagline',
                  'tc_header_mobile_sticky',
                  'tc_header_mobile_menu_layout',
                  'tc_header_mobile_menu_dropdown_on_click'
            );
            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }
            return $_map;
      }




      /*-----------------------------------------------------------------------------------------------------
                          NAVIGATION SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_navigation_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_side_menu_dropdown_on_click',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }


            //to add
            $_to_add  = array(
                  'tc_menu_resp_dropdown_limit_to_viewport'  =>  array(
                                    'default'       => 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "For mobile devices (responsive), limit the height of the dropdown menu block to the visible viewport." , 'customizr' ) ),
                                    'section'       => 'nav' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 35,
                                    //'transport'     => 'postMessage',
                  ),
                  'tc_display_menu_label'  =>  array(
                                    'default'       => 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display a label next to the menu button." , 'customizr' ),
                                    'section'       => 'nav' ,
                                    'type'          => 'nimblecheck' ,
                                    'priority'      => 45,
                                    'notice'        => __( 'Note : the label is hidden on mobile devices.' , 'customizr' ),
                  ),
                  //override
                  'tc_menu_position'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'pull-menu-left' : 'pull-menu-right',
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Menu position (for "main" menu)' , 'customizr' ),
                                    'section'       => 'nav' ,
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                            'pull-menu-center'    => __( 'Menu centered' , 'customizr' ),
                                            'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                    ),
                                    'priority'      => 50,
                                    'transport'     => 'postMessage',
                                    'notice'        => sprintf( '%1$s <a href="%2$s">%3$s</a>.',
                                        __("Note : the menu centered position is available only when" , 'customizr'),
                                        "javascript:wp.customize.section('header_layout_sec').focus();",
                                        __("the logo is centered", 'customizr')
                                    )
                  ),
                  //override
                  'tc_second_menu_position'  =>  array(
                                    'default'       => 'pull-menu-left',
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Secondary (horizontal) menu design' , 'customizr'),
                                    'label'         => __( 'Menu position (for the horizontal menu)' , 'customizr' ),
                                    'section'       => 'nav' ,
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                            'pull-menu-center'    => __( 'Menu centered' , 'customizr' ),
                                            'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                    ),
                                    'priority'      => 55,
                                    'transport'     => 'postMessage',
                                    'notice'        => sprintf( '%1$s <a href="%2$s">%3$s</a>.',
                                        __("Note : the menu centered position is available only when" , 'customizr'),
                                        "javascript:wp.customize.section('header_layout_sec').focus();",
                                        __("the logo is centered", 'customizr')
                                    )
                  ),
                  'tc_second_menu_resp_setting'  =>  array(
                                    'default'       => 'in-sn-before',
                                    'control'       => 'CZR_controls' ,
                                    'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "Choose a mobile devices (responsive) behaviour for the secondary menu." , 'customizr' ) ),
                                    'section'       => 'nav',
                                    'type'      =>  'select',
                                    'choices'     => array(
                                        'in-sn-before'   => __( 'Move before inside the side menu ' , 'customizr'),
                                        'in-sn-after'   => __( 'Move after inside the side menu ' , 'customizr'),
                                        'display-in-header'   => __( 'Display in the header' , 'customizr'),
                                        'hide'   => __( 'Hide' , 'customizr'  ),
                                    ),
                                    'priority'      => 90,
                  ),

            );

            return array_merge( $_map, $_to_add );

      }


      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : CONTENT
      *******************************************************************************************************
      ******************************************************************************************************/

      /*-----------------------------------------------------------------------------------------------------
                                    FRONT PAGE SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_front_page_option_map( $_map, $get_default = null ){

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_home_slider_overlay',
                  'tc_home_slider_dots'
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }
            return $_map;
      }


      /*-----------------------------------------------------------------------------------------------------
                                     PAGES AND POST LAYOUT SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_layout_option_map( $_map, $get_default = null ){

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_single_author_block_location',
                  'tc_single_related_posts_block_location',
                  'tc_singular_comments_block_location'
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }
            return $_map;
      }

      /*-----------------------------------------------------------------------------------------------------
                                    POST METAS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_post_metas_option_map( $_map, $get_default = null ){

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

                  /* Post metas design has been removed in c4 */
                  'tc_post_metas_design'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'buttons' : 'no-buttons',
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Metas Design' , 'customizr' ),
                                    'label'         => __( "Select a design for the post metas" , 'customizr' ),
                                    'section'       => 'post_metas_sec' ,
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                        'buttons'     => __( 'Buttons and text' , 'customizr' ),
                                        'no-buttons'  => __( 'Text only' , 'customizr' )
                                    ),
                                    'priority'      => 10
                  ),
                  'tc_post_metas_update_date_format'  =>  array(
                                    'default'       => 'days',
                                    'control'       => 'CZR_controls',
                                    'label'         => __( "Select the last update format" , 'customizr' ),
                                    'section'       => 'post_metas_sec',
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'days'     => __( 'No. of days since last update' , 'customizr' ),
                                            'date'     => __( 'Date of the last update' , 'customizr' )
                                    ),
                                    'priority'      => 55
                  ),
                  /* Update notice in title has been completely removed in c4*/
                  'tc_post_metas_update_notice_in_title'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 1 : 0,
                                    'control'       => 'CZR_controls',
                                    'title'         => __( 'Recent update notice after post titles' , 'customizr' ),
                                    'label'         => __( "Display a recent update notice" , 'customizr' ),
                                    'section'       => 'post_metas_sec',
                                    'type'          => 'nimblecheck',
                                    'priority'      => 65,
                                    'notice'    => __( 'If this option is checked, a customizable recent update notice is displayed next to the post title.' , 'customizr' )
                  ),
                  'tc_post_metas_update_notice_interval'  =>  array(
                                    'default'       => 10,
                                    'control'       => 'CZR_controls',
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'label'         => __( "Display the notice if the last update is less (strictly) than n days old" , 'customizr' ),
                                    'section'       => 'post_metas_sec',
                                    'type'          => 'number' ,
                                    'step'          => 1,
                                    'min'           => 0,
                                    'priority'      => 70,
                                    'notice'    => __( 'Set a maximum interval (in days) during which the last update notice will be displayed.' , 'customizr' ),
                  ),
                  'tc_post_metas_update_notice_text'  =>  array(
                                    'default'       => __( "Recently updated !" , 'customizr' ),
                                    'control'       => 'CZR_controls',
                                    'label'         => __( "Update notice text" , 'customizr' ),
                                    'section'       => 'post_metas_sec',
                                    'type'          => 'text',
                                    'priority'      => 75,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_post_metas_update_notice_format'  =>  array(
                                    'default'       => 'label-default',
                                    'control'       => 'CZR_controls',
                                    'label'         => __( "Update notice style" , 'customizr' ),
                                    'section'       => 'post_metas_sec',
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'label-default'   => __( 'Default (grey)' , 'customizr' ),
                                            'label-success'   => __( 'Success (green)' , 'customizr' ),
                                            'label-warning'   => __( 'Alert (orange)' , 'customizr' ),
                                            'label-important' => __( 'Important (red)' , 'customizr' ),
                                            'label-info'      => __( 'Info (blue)' , 'customizr' )
                                    ),
                                    'priority'      => 80,
                                    'transport'   => 'postMessage'
                  )
            );

            $_map = array_merge( $_map, $_to_add );

            //add notice to the update date option
            if ( isset( $_map[ 'tc_show_post_metas_update_date' ] ) )
                  $_map[ 'tc_show_post_metas_update_date' ]['notice'] = __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' );

            return $_map;

      }



      /*-----------------------------------------------------------------------------------------------------
                                    POST LISTS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_post_list_option_map( $_map, $get_default = null ) {


            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_post_list_thumb_placeholder',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }



            global $wp_version;

            //to add
            $_to_add  = array(
                  //Post list length
                  'tc_post_list_length' =>  array(
                                    'default'       => 'excerpt',
                                    'label'         => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'select' ,
                                    'choices'       => array(
                                            'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                            'full'    => __( 'Display the full content' , 'customizr' )
                                            ),
                                    'priority'       => 20,
                  ),
                  //classic grid
                  'tc_grid_in_blog'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Apply the grid layout to Home/Blog' , "customizr" ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 57
                  ),
                  'tc_grid_in_archive'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Apply the grid layout to Archives (archives, categories, author posts)' , "customizr" ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 58
                  ),
                  'tc_grid_in_search'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Apply the grid layout to Search results' , "customizr" ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 60,
                                    'notice'        => __( 'Unchecked contexts are displayed with the alternate thumbnails layout.' , 'customizr' ),
                  ),
                  'tc_grid_icons'  =>  array(
                                    'default'       => 1,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Display post format icons' , "customizr" ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'nimblecheck',
                                    'priority'      => 63,
                                    'transport'     => 'postMessage'
                  ),
                  /* Used only for the standard grid: Removed in c4 */
                  'tc_post_list_default_thumb'  => array(
                                    'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                    'label'         => __( 'Upload a default thumbnail' , 'customizr' ),
                                    'section'   =>  'post_lists_sec' ,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                            //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                                    'width'         => 570,
                                    'height'        => 350,
                                    'flex_width'    => true,
                                    'flex_height'   => true,
                                    //to keep the selected cropped size
                                    'dst_width'     => false,
                                    'dst_height'    => false,
                                    'priority'      =>  73
                  ),

                  'tc_post_list_thumb_height' => array(
                                    'default'       => 250,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                    'section'     => 'post_lists_sec' ,
                                    'type'        => 'number' ,
                                    'step'      => 1,
                                    'min'     => 0,
                                    'priority'      => 80,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_grid_thumb_height' => array(
                                    'default'       => 350,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Thumbnails max height for the grid layout' , 'customizr' ),
                                    'label'         => __( "Set the post grid thumbnail's max height in pixels" , 'customizr' ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'number' ,
                                    'step'          => 1,
                                    'min'           => 0,
                                    'priority'      => 65
                                    //'transport'   => 'postMessage'
                  ),

            );

            $_map = array_merge( $_map, $_to_add );

            //Add thumb shape
            $_map['tc_post_list_thumb_shape']['choices'] = array_merge( $_map['tc_post_list_thumb_shape']['choices'], array(
                                    'squared'               => __( 'Squared, expand on hover' , 'customizr'),
                                    'squared-expanded'      => __( 'Squared, no expansion' , 'customizr'),
                                    'rectangular'           => __( 'Rectangular with no effect' , 'customizr'  ),
                                    'rectangular-blurred'   => __( 'Rectangular with blur effect on hover' , 'customizr'  ),
                                    'rectangular-unblurred' => __( 'Rectangular with unblur effect on hover' , 'customizr')
            ) );

            //Remove czr4 only thumb shape
            unset( $_map['tc_post_list_thumb_shape']['choices']['regular'] );

            //Add thumb position
            $_map['tc_post_list_thumb_position']['choices'] = array_merge( $_map['tc_post_list_thumb_position']['choices'], array(
                                    'top'     => __( 'Top' , 'customizr' ),
                                    'bottom'    => __( 'Bottom' , 'customizr' ),
            ) );

            //Remove post list plain grid choice
            unset( $_map['tc_post_list_grid']['choices']['plain' ] );

            return $_map;
      }


      /*-----------------------------------------------------------------------------------------------------
                                     COMMENTS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_comment_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(
                  /* Removed in c4 */
                  'tc_comment_bubble_shape' => array(
                                  'default'     => 'default',
                                  'control'     => 'CZR_controls',
                                  'label'       => __( 'Comments bubble shape' , 'customizr' ),
                                  'section'     => 'comments_sec',
                                  'type'      =>  'select' ,
                                  'choices'     => array(
                                          'default'             => __( "Small bubbles" , 'customizr' ),
                                          'custom-bubble-one'   => __( 'Large bubbles' , 'customizr' ),
                                  ),
                                  'priority'    => 10,
                  ),
                  /* Removed in c4 */
                  'tc_comment_bubble_color_type' => array(
                                  'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'custom' : 'skin',
                                  'control'     => 'CZR_controls',
                                  'label'       => __( 'Comments bubble color' , 'customizr' ),
                                  'section'     => 'comments_sec',
                                  'type'      =>  'select' ,
                                  'choices'     => array(
                                          'skin'     => __( "Skin color" , 'customizr' ),
                                          'custom'   => __( 'Custom' , 'customizr' ),
                                  ),
                                  'priority'    => 20,
                  ),
                  /* Removed in c4 */
                  'tc_comment_bubble_color' => array(
                                  'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? '#F00' : CZR_utils::$inst->czr_fn_get_skin_color(),
                                  'control'     => 'WP_Customize_Color_Control',
                                  'label'       => __( 'Comments bubble color' , 'customizr' ),
                                  'section'     => 'comments_sec',
                                  'type'        =>  'color' ,
                                  'priority'    => 30,
                                  'sanitize_callback'    => 'czr_fn_sanitize_hex_color',
                                  'sanitize_js_callback' => 'maybe_hash_hex_color',
                                  'transport'   => 'postMessage'
                  ),

            );

            return array_merge( $_map, $_to_add );
      }


      /*-----------------------------------------------------------------------------------------------------
                                     COMMENTS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_single_post_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_related_posts',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }


            return $_map;
      }

      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : SIDEBARS
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                     SIDEBAR SOCIAL LINKS SETTINGS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_sidebars_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

                  'tc_social_in_sidebar_title'  =>  array(
                                    'default'       => __( 'Social links' , 'customizr' ),
                                    'label'       => __( 'Social link title in sidebars' , 'customizr' ),
                                    'control'   =>  'CZR_controls' ,
                                    'section'     => 'sidebar_socials_sec',
                                    'type'        => 'text' ,
                                    'priority'       => 30,
                                    'transport'   => 'postMessage',
                                    'notice'    => __( 'Will be hidden if empty' , 'customizr' )
                  )

            );

            return array_merge( $_map, $_to_add );
      }

      /*-----------------------------------------------------------------------------------------------------
                                    RESPONSIVE SETTINGS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_responsive_option_map( $_map, $get_default = null ) {
            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_ms_respond_css'
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            //to add
            $_to_add  = array(
                  'tc_block_reorder'  =>  array(
                                    'default'       => 1,
                                    'control'   => 'CZR_controls' ,
                                    'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( 'Dynamic sidebar reordering on small devices' , 'customizr' ) ),
                                    'section'     => 'responsive_sec' ,
                                    'type'        => 'nimblecheck' ,
                                    'notice'    => __( 'Activate this option to move the sidebars (if any) after the main content block, for smartphones or tablets viewport.' , 'customizr' ),
                  )
            );


            return array_merge( $_map, $_to_add );

      }


      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : FOOTER
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                     FOOTER GLOBAL SETTINGS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_footer_global_settings_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_footer_skin',
                  'tc_footer_horizontal_widgets'
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            return $_map;
      }









      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : WOOCOMMERCE
      *******************************************************************************************************
      ******************************************************************************************************/
      function czr_fn_woocommerce_option_map( $_map, $get_default = null ) {
            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_woocommerce_display_product_thumb_before_mw',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            return $_map;
      }






      /***************************************************************
      * POPULATE SECTIONS
      ***************************************************************/
      /**
      * hook : tc_add_section_map
      */
      function czr_fn_popul_section_map( $_sections ) {
            //removed sections
            //to unset
            $_sections_to_unset = array(
                  'site_layout_sec',
                  'placeholder_sec'
            );

            foreach ( $_sections_to_unset as $key ) {
                  unset( $_sections[ $key ] );
            }

            $_old_sections = array(

                  /*---------------------------------------------------------------------------------------------
                  -> PANEL : GLOBAL SETTINGS
                  ----------------------------------------------------------------------------------------------*/
                  'titles_icons_sec'        => array(
                                      'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                                      'priority'    =>  $this->is_wp_version_before_4_0 ? 18 : 40,
                                      'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                                      'panel'   => 'tc-global-panel'
                  ),

                  /*---------------------------------------------------------------------------------------------
                  -> PANEL : SIDEBARS
                  ----------------------------------------------------------------------------------------------*/
                  'responsive_sec'           => array(
                                      'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                      'priority'    =>  20,
                                      'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                                      'panel'   => 'tc-sidebars-panel'
                  ),
            );

            return array_merge( $_sections, $_old_sections );
      }


      /**
      * Returns the list of available skins from child (if exists) and parent theme
      *
      * @package Customizr
      * @since Customizr 3.0.11
      * @updated Customizr 3.0.15
      */
      //Valid only for customizr < 4.0
      function czr_fn_build_skin_list() {
        $tc_base        = TC_BASE;
        $tc_base_child  = TC_BASE_CHILD;

        $parent_skins   = $this -> czr_fn_get_skins( $tc_base .'inc/assets/css');
        $child_skins    = ( czr_fn_is_child() && file_exists( $tc_base_child .'inc/assets/css') ) ? $this -> czr_fn_get_skins( $tc_base_child .'inc/assets/css') : array();
        $skin_list      = array_merge( $parent_skins , $child_skins );

        return apply_filters( 'tc_skin_list', $skin_list );
      }


      /**
      * Generates skin select list
      *
      * @package Customizr
      * @since Customizr 3.0.15
      *
      */
      function czr_fn_get_skins($path) {
        //checks if path exists
        if ( !file_exists($path) )
          return;

        //gets the skins from init
        $default_skin_list    = CZR_init::$instance -> skins;

        //declares the skin list array
        $skin_list        = array();

        //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
        $files            = scandir($path) ;
        foreach( $files as $file ) {
            //skips the minified and tc_common
            if ( false !== strpos($file, '.min.') || false !== strpos($file, 'tc_common') )
              continue;

            if ( $file[0] != '.' && !is_dir($path.$file) ) {
              if ( substr( $file, -4) == '.css' ) {
                $skin_list[$file] = isset($default_skin_list[$file]) ?  call_user_func( '__' , $default_skin_list[$file] , 'customizr' ) : substr_replace( $file , '' , -4 , 4);
              }
            }
          }//endforeach
        $_to_return = array();

        //Order skins like in the default array
        foreach( $default_skin_list as $_key => $value ) {
          if( isset($skin_list[$_key]) ) {
            $_to_return[$_key] = $skin_list[$_key];
          }
        }
        //add skins not included in default
        foreach( $skin_list as $_file => $_name ) {
          if( ! isset( $_to_return[$_file] ) )
            $_to_return[$_file] = $_name;
        }
        return $_to_return;
      }//end of function

}//end of class
endif;

?><?php
/**
* Defines filters and actions used in several templates/classes
*
*/
if ( ! class_exists( 'CZR_init_retro_compat' ) ) :
  class CZR_init_retro_compat {
    static $instance;

    /*
    * This is fired very early, before the new defaults are generated
    */
    function __construct () {
      self::$instance =& $this;

      //copy old options in the new framework
      //only if user is logged in
      //then each routine has to decide what to do also depending on the user started before
      if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
        $theme_options            = czr_fn_get_unfiltered_theme_options();
        $_to_update               = false;

        if ( ! empty( $theme_options ) ) {
          //Socials
          $_new_options_w_socials     = $this -> czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

          if ( ! empty( $_new_options_w_socials ) ) {
            $theme_options              = $_new_options_w_socials;
            $_to_update                 = true;
          }

          //Custom css
          $_new_options_w_custom_css  = $this -> czr_fn_maybe_move_old_css_to_wp_embed( $theme_options );

          if ( ! empty( $_new_options_w_custom_css ) ) {
            $theme_options              = $_new_options_w_custom_css;
            $_to_update                 = true;
          }

          if ( $_to_update ) {
            update_option( CZR_THEME_OPTIONS, $theme_options );
          }
        }
      }
    }//construct

    /*
    * returns array() the new set of options or empty if there's nothing to move
    */
    function czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options ) {
      $_options = $theme_options;


      /*
      * When Memcached is active transients (object cached) might be not persistent
      * we cannot really rely on them :/
      */
      //nothing to do if new user
      //if ( ! czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
      //  return array();

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_socials', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      /*
      * In theme versions < 3.5.5  we didn't use store the __moved_opts['old_socials'] in the options
      * if there was anything to move, so we need another check here to see if new socials have been already
      * set
      */
      if ( isset( $_options[ 'tc_social_links' ] ) && !empty($_options[ 'tc_social_links' ] ) ) {
        return array();
      }

      $_old_socials          = CZR___::$instance -> old_socials;
      $_old_filtered_socials = apply_filters( 'tc_default_socials', $_old_socials );

      /*
      * old socials were in the form
      * array( 'tc_twitter' => array( .., default=>'[url]' ,..) )
      * need to be ported in the form
      * array( 'tc_twitter' => '[url]' )
      * before parse them in the options.
      */
      $_social_options       = array();
      foreach ( $_old_filtered_socials as $social => $attrs ) {
        if ( isset( $attrs['default'] ) ) {
          $_social_options[$social] = $attrs['default'];
        }
      }

      //merge options with the defaults socials
      $_options     = wp_parse_args( $_options, $_social_options );

      $_new_socials = array();
      $_index       = 0;

      /*
      * rss needs a special treatment for old users, it was a default
      * If it doesn't exist in the options we have to set it with the default value
      * if it exists but is null it will be skipped
      */
      foreach ( $_old_filtered_socials as $_old_social_id => $attributes ) {
        if ( ! empty( $_options[ $_old_social_id ] ) ) {

          //build new attributes
          $_title       = isset( $attributes[ 'link_title' ] ) ? esc_attr( $attributes[ 'link_title' ] ) :  '';
          $_social_icon = str_replace( array( 'tc_email', 'tc_'), array( 'fa-envelope', 'fa-' ), $_old_social_id );

          // email needs a special treatment
          $_social_link = esc_url_raw( 'tc_email' == $_old_social_id  ? sprintf( 'mailto:%s', $_options[ $_old_social_id ] ) : $_options[ $_old_social_id ] );

          if ( empty( $_social_link ) ) {
            continue;
          }

          //create module
          array_push( $_new_socials, array(
              'id'            => "czr_social_module_{$_index}",
              'title'         => $_title,
              'social-icon'   => $_social_icon,
              'social-link'   => $_social_link,
              'social-target' => 1,
            )
          );
          $_index++;
        }
      }

      if ( !empty( $_new_socials ) ) {
        $theme_options[ 'tc_social_links' ] = $_new_socials;
      }

      //save the state in the options
      $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
      array_push( $theme_options[ '__moved_opts' ], 'old_socials' );

      return $theme_options;
    }


    /*
    * returns array() the new set of options or empty if there's nothing to move
    */
    function czr_fn_maybe_move_old_css_to_wp_embed( $theme_options ) {
      $_options = $theme_options;


      /*
      * When Memcached is active transients (object cached) might be not persistent
      * we cannot really rely on them :/
      */
      //if ( ! czr_fn_user_started_before_version( '3.5.5', '1.3.3' ) )
      //  return array();

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'custom_css', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      /*
      * FROM
      * https://make.wordpress.org/core/2016/11/26/extending-the-custom-css-editor/
      */
      if ( function_exists( 'wp_update_custom_css_post' ) ) {
        // Migrate any existing theme CSS to the core option added in WordPress 4.7.
        $css = array_key_exists( 'tc_custom_css', $_options ) ?  html_entity_decode( esc_html( $_options['tc_custom_css'] ) ) : '';

        if ( $css ) {
          $core_css = wp_get_custom_css(); // Preserve any CSS already added to the core option.
          //avoid duplications
          $core_css = str_replace( $css, '', $core_css );
          $return = wp_update_custom_css_post( $core_css . "\n" . $css );
        }


        //save the state in the options
        $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
        array_push( $theme_options[ '__moved_opts' ], 'custom_css' );

        return $theme_options;
      }

      return array();
    }

  }//end class
endif;
?><?php
/**
* Defines filters and actions used in several templates/classes
*
*/
if ( ! class_exists( 'CZR_utils' ) ) :
  class CZR_utils {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $inst;
      static $instance;
      public $default_options;
      public $db_options;
      public $options;//not used in customizer context only
      public $is_customizing;
      public $tc_options_prefixes;

      public static $_theme_setting_list;

      function __construct () {
        self::$inst =& $this;
        self::$instance =& $this;

        //Various WP filters for
        //content
        //thumbnails => parses image if smartload enabled
        //title
        add_action( 'wp_head'                 , array( $this , 'czr_fn_wp_filters') );

        //get all options
        add_filter( '__options'               , 'czr_fn_get_theme_options' , 10, 1 );
        //get single option
        add_filter( '__get_option'            , 'czr_fn_opt' , 10, 2 );//deprecated

        //some useful filters
        add_filter( '__ID'                    , 'czr_fn_get_id' );//deprecated
        add_filter( '__screen_layout'         , array( $this , 'czr_fn_get_layout' ) , 10 , 2 );//deprecated
        add_filter( '__is_home'               , 'czr_fn_is_real_home' );
        add_filter( '__is_home_empty'         , 'czr_fn_is_home_empty' );
        add_filter( '__post_type'             , 'czr_fn_get_post_type' );
        add_filter( '__is_no_results'         , 'czr_fn_is_no_results' );
        add_filter( '__article_selectors'     , array( $this , 'czr_fn_article_selectors' ) );

        //social networks
        add_filter( '__get_socials'           , 'czr_fn_get_social_networks', 10, 0 );
      }

      /**
      * hook : wp_head
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_wp_filters() {
        add_filter( 'the_content'                         , array( $this , 'czr_fn_fancybox_content_filter' ) );
        if ( apply_filters( 'tc_enable_fancybox_in_wc_short_description', false  ) ) {
            add_filter( 'woocommerce_short_description'   , array( $this, 'czr_fn_fancybox_content_filter' ) );
        }
        /*
        * Smartload disabled for content retrieved via ajax
        */
        if ( apply_filters( 'tc_globally_enable_img_smart_load', ! czr_fn_is_ajax() && esc_attr( czr_fn_opt( 'tc_img_smart_load' ) ) ) ) {
            add_filter( 'the_content'                       , 'czr_fn_parse_imgs', PHP_INT_MAX );
            add_filter( 'tc_thumb_html'                     , 'czr_fn_parse_imgs' );
            if ( apply_filters( 'tc_enable_img_smart_load_in_wc_short_description', false  ) ) {
                add_filter( 'woocommerce_short_description' , 'czr_fn_parse_imgs' );
            }
        }
        add_filter( 'wp_title'                            , 'czr_fn_wp_title' , 10, 2 );
      }




      /**
      * Returns the current skin's primary color
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function czr_fn_get_skin_color( $_what = null ) {
          $_color_map    = CZR___::$instance -> skin_classic_color_map;
          $_color_map    = ( is_array($_color_map) ) ? $_color_map : array();

          $_active_skin =  str_replace('.min.', '.', basename( CZR_init::$instance -> czr_fn_get_style_src() ) );
          //falls back to grey.css array( '#5A5A5A', '#343434' ) if not defined
          $_to_return = array( '#5A5A5A', '#343434' );

          switch ($_what) {
            case 'all':
              $_to_return = $_color_map;
            break;

            case 'pair':
              $_to_return = ( false != $_active_skin && array_key_exists( $_active_skin, $_color_map ) && is_array( $_color_map[$_active_skin] ) ) ? $_color_map[$_active_skin] : $_to_return;
            break;

            default:
              $_to_return = ( false != $_active_skin && isset($_color_map[$_active_skin][0]) ) ? $_color_map[$_active_skin][0] : $_to_return[0];
            break;
          }
          return apply_filters( 'tc_get_skin_color' , $_to_return , $_what );
      }




      /**
      * Returns an option from the options array of the theme.
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_opt( $option_name , $option_group = null, $use_default = true ) {
          return czr_fn_opt( $option_name , $option_group, $use_default );
      }

      //backward compatibility
      //used until FPU 2.0.33
      function czr_fn_parse_imgs( $_html ) {
        return czr_fn_parse_imgs( $_html );
      }


      /**
      * Returns the "real" queried post ID or if !isset, get_the_ID()
      * Checks some contextual booleans
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_id()  {
          return czr_fn_get_id();
      }




      /**
      * This function returns the layout (sidebar(s), or full width) to apply to a context
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
          $__options                    = czr_fn__f ( '__options' );

          //Article wrapper class definition
          $global_layout                = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );

          /* DEFAULT LAYOUTS */
          //what is the default layout we want to apply? By default we apply the global default layout
          $tc_sidebar_default_layout    = esc_attr( $__options['tc_sidebar_global_layout'] );

          //checks if the 'force default layout' option is checked and return the default layout before any specific layout
          if( isset($__options['tc_sidebar_force_layout']) && 1 == $__options['tc_sidebar_force_layout'] ) {
            $class_tab  = $global_layout[$tc_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $tc_screen_layout = array(
              'sidebar' => $tc_sidebar_default_layout,
              'class'   => $class_tab
            );
            return $tc_screen_layout[$sidebar_or_class];
          }

          global $wp_query, $post;
          $tc_specific_post_layout    = false;
          $is_singular_layout         = false;

          if ( apply_filters( 'tc_is_post_layout', is_single( $post_id ), $post_id ) || czr_fn_is_attachment_image() ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_post_layout'] );
            $is_singular_layout = true;
          } elseif ( apply_filters( 'tc_is_page_layout', is_page( $post_id ), $post_id ) ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_page_layout'] );
            $is_singular_layout = true;
          }


          //builds the default layout option array including layout and article class
          $class_tab  = $global_layout[$tc_sidebar_default_layout];
          $class_tab  = $class_tab['content'];
          $tc_screen_layout             = array(
                      'sidebar' => $tc_sidebar_default_layout,
                      'class'   => $class_tab
          );

          //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
          $tc_specific_post_layout    = false;

          //if we are displaying an attachement, we use the parent post/page layout by default
          //=> but if the attachment has a layout, it will win.
          if ( isset($post) && is_singular() && 'attachment' == $post->post_type ) {
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
            if ( ! $tc_specific_post_layout ) {
                $tc_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
            }
          }
          //for a singular post or page OR for the posts page
          elseif ( $is_singular_layout || is_singular() || czr_fn_is_attachment_image() || $wp_query -> is_posts_page )
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );


          //checks if we display home page, either posts or static page and apply the customizer option
          if( ( is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page() ) {
             $tc_specific_post_layout = $__options['tc_front_layout'];
          }

          if( $tc_specific_post_layout ) {
              $class_tab  = $global_layout[$tc_specific_post_layout];
              $class_tab  = $class_tab['content'];
              $tc_screen_layout = array(
              'sidebar' => $tc_specific_post_layout,
              'class'   => $class_tab
            );
          }

          return apply_filters( 'tc_screen_layout' , $tc_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }







      /**
       * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
       *
       * @package Customizr
       * @since Customizr 2.0.7
       */
      function czr_fn_fancybox_content_filter( $content) {
          $tc_fancybox = esc_attr( czr_fn_opt( 'tc_fancybox' ) );

          if ( 1 != $tc_fancybox )
            return $content;

          global $post;
          if ( ! isset($post) )
            return $content;

          //same as smartload ones
          $allowed_image_extentions = apply_filters( 'tc_lightbox_allowed_img_extensions', array(
            'bmp',
            'gif',
            'jpeg',
            'jpg',
            'jpe',
            'tif',
            'tiff',
            'ico',
            'png',
            'svg',
            'svgz'
          ) );

          if ( empty( $allowed_image_extentions ) || ! is_array( $allowed_image_extentions ) ) {
            return $content;
          }


          $img_extensions_pattern = sprintf( "(?:%s)", implode( '|', $allowed_image_extentions ) );
          $pattern                = '#<a([^>]+?)href=[\'"]?([^\'"\s>]+\.'.$img_extensions_pattern.'[^\'"\s>]*)[\'"]?([^>]*)>#i';

          $replacement = '<a$1href="$2" class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'"$3>';

          $r_content = preg_replace( $pattern, $replacement, $content);
          $content = $r_content ? $r_content : $content;
          return apply_filters( 'tc_fancybox_content_filter', $content );
      }





      /**
      * Returns the classes for the post div.
      *
      * @param string|array $class One or more classes to add to the class list.
      * @param int $post_id An optional post ID.
      * @package Customizr
      * @since 3.0.10
      */
      function czr_fn_get_post_class( $class = '', $post_id = null ) {
        //Separates classes with a single space, collates classes for post DIV
        return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
      }





      /**
      * Displays the selectors of the article depending on the context
      *
      * @package Customizr
      * @since 3.1.0
      */
      function czr_fn_article_selectors() {

        //gets global vars
        global $post;
        global $wp_query;

        //declares selector var
        $selectors                  = '';

        // SINGLE POST
        $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
        $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // POST LIST
        $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !czr_fn__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
        $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class( 'row-fluid grid-item' ) ) : $selectors;

        // PAGE
        $page_selector_bool         = isset($post) && 'page' == czr_fn__f('__post_type') && is_singular() && !czr_fn__f( '__is_home_empty');
        $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // ATTACHMENT
        //checks if attachement is image and add a selector
        $format_image               = wp_attachment_is_image() ? 'format-image' : '';
        $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class(array('row-fluid', $format_image) ) ) : $selectors;

        // NO SEARCH RESULTS
        $selectors                  = ( is_search() && 0 == $wp_query -> post_count ) ? apply_filters( 'tc_no_results_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        // 404
        $selectors                  = is_404() ? apply_filters( 'tc_404_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        echo apply_filters( 'tc_article_selectors', $selectors );

      }//end of function



    /**
    * Returns a boolean
    * check if user started to use the theme before ( strictly < ) the requested version
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null ) {
      return czr_fn_user_started_before_version( $_czr_ver, $_pro_ver );
    }

  }//end of class
endif;

?><?php
/**
* Loads front end stylesheets and scripts
*
*/
if ( ! class_exists( 'CZR_resources' ) ) :
	class CZR_resources {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
      public $tc_script_map;
      public $current_random_skin;

      private $_resources_version;

	    function __construct () {

	        self::$instance =& $this;

          $this->_resouces_version = CZR_DEBUG_MODE || CZR_DEV_MODE ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER;

          add_action( 'wp_enqueue_scripts'            , array( $this , 'czr_fn_enqueue_gfonts' ) , 0 );
	        add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_styles' ) );
          add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_scripts' ) );

	        //Custom CSS
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_custom_css') , apply_filters( 'tc_custom_css_priority', 9999 ) );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_fonts_inline_css') );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_dropcap_inline_css') );

          /* See: https://github.com/presscustomizr/customizr/issues/605 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_apply_media_upload_front_patch' ) );
          /* See: https://github.com/presscustomizr/customizr/issues/787 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_maybe_avoid_double_social_icon' ) );

          //set random skin
          add_filter ('tc_opt_tc_skin'                , array( $this, 'czr_fn_set_random_skin' ) );

          add_action( 'wp_ajax_dismiss_style_switcher_note_front',  array( $this , 'czr_fn_dismiss_style_switcher_note_front' ) );
          add_action( 'wp_ajax_nopriv_dismiss_style_switcher_note_front',  array( $this , 'czr_fn_dismiss_style_switcher_note_front' ) );

          //stores the front scripts map in a property
          $this -> tc_script_map = $this -> czr_fn_get_script_map();

          add_filter( 'czr_style_note_content', array( $this,  'czr_fn_get_style_note_content' ) );
	    }//construct


  	  /**
  		* Registers and enqueues Customizr stylesheets
  		* @package Customizr
  		* @since Customizr 1.1
  		*/
      function czr_fn_enqueue_front_styles() {
            //Enqueue FontAwesome CSS
            if ( true == czr_fn_opt( 'tc_font_awesome_icons' ) ) {
              $_path = apply_filters( 'tc_font_icons_path' , TC_BASE_URL . 'assets/shared/fonts/fa/css/' );
              wp_enqueue_style( 'customizr-fa',
                  $_path . 'fontawesome-all.min.css',
                  array() , $this->_resouces_version, 'all' );
            }

  	      wp_enqueue_style( 'customizr-common', CZR_init::$instance -> czr_fn_get_style_src( 'common') , array() , $this->_resouces_version, 'all' );
            //Customizr active skin
  	      wp_register_style( 'customizr-skin', CZR_init::$instance -> czr_fn_get_style_src( 'skin'), array('customizr-common'), $this->_resouces_version, 'all' );
  	      wp_enqueue_style( 'customizr-skin' );
  	      //Customizr stylesheet (style.css)
  	      wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), $this->_resouces_version , 'all' );

  	      //Customizer user defined style options : the custom CSS is written with a high priority here
  	      wp_add_inline_style( 'customizr-skin', apply_filters( 'tc_user_options_style' , '' ) );
  		}



      /**
      * Helper to get all front end script
      * Fired from the constructor
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      private function czr_fn_get_script_map( $_handles = array() ) {
          $_front_path  =  'inc/assets/js/';
          $_libs_path =  CZR_ASSETS_PREFIX . 'front/js/libs/';

          $_map = array(
              'tc-js-params' => array(
                'path' => $_front_path,
                'files' => array( 'tc-js-params.js' ),
                'dependencies' => array( 'jquery' )
              ),
              //adds support for map method in array prototype for old ie browsers <ie9
              'tc-js-arraymap-proto' => array(
                'path' => $_libs_path,
                'files' => array( 'oldBrowserCompat.min.js' ),
                'dependencies' => array()
              ),
              'tc-bootstrap' => array(
                'path' => $_libs_path,
                'files' => array( 'bootstrap-classical.js' , 'bootstrap-classical.min.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery', 'tc-js-params' )
              ),
              'tc-img-original-sizes' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryimgOriginalSizes.js' ),
                'dependencies' => array('jquery')
              ),
              'tc-smoothscroll' => array(
                'path' => $_libs_path,
                'files' => array( 'smoothscroll.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'underscore' )
              ),
              'tc-outline' => array(
                'path' => $_libs_path,
                'files' => array( 'outline.js' ),
                'dependencies' => array()
              ),
              'tc-waypoints' => array(
                'path' => $_libs_path,
                'files' => array( 'waypoints.js' ),
                'dependencies' => array('jquery')
              ),
              'tc-dropcap' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryaddDropCap.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-img-smartload' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryimgSmartLoad.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-ext-links' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryextLinks.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-parallax' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryParallax.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-center-images' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryCenterImages.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'underscore' )
              ),
              //!!no fancybox dependency if fancybox not required!
              'tc-main-front' => array(
                'path' => $_front_path,
                'files' => array( 'main-ccat.js' , 'main-ccat.min.js' ),
                'dependencies' => $this -> czr_fn_is_fancyboxjs_required() ? array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-fancybox' , 'underscore' ) : array( 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap' , 'underscore' )
              ),
              //loaded separately => not included in tc-script.js
              'tc-fancybox' => array(
                'path' => $_libs_path . 'fancybox/',
                'files' => array( 'jquery.fancybox-1.3.4.min.js' ),
                'dependencies' => $this -> czr_fn_load_concatenated_front_scripts() ? array( 'jquery' ) : array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap' )
              ),
              //concats all scripts except fancybox
              'tc-scripts' => array(
                'path' => $_front_path,
                'files' => array( 'tc-scripts.js' , 'tc-scripts.min.js' ),
                'dependencies' =>  $this -> czr_fn_is_fancyboxjs_required() ? array( 'underscore', 'jquery', 'tc-fancybox' ) : array( 'underscore', 'jquery' )
              )
          );//end of scripts map

          return apply_filters('tc_get_script_map' , $_map, $_handles );
      }



  		/**
  		* Loads Customizr front scripts
      * Dependencies are defined in the script map property
      *
  		* @return  void()
  		* @uses wp_enqueue_script() to manage script dependencies
  		* @package Customizr
  		* @since Customizr 1.0
  		*/
  		function czr_fn_enqueue_front_scripts() {
  	    //wp scripts
  	  	if ( is_singular() && get_option( 'thread_comments' ) )
  		    wp_enqueue_script( 'comment-reply' );

  	    wp_enqueue_script( 'jquery' );
  	    wp_enqueue_script( 'jquery-ui-core' );

  	    wp_enqueue_script(
          'modernizr',
          TC_BASE_URL . 'assets/front/js/libs/modernizr.min.js',
          array(),
          CUSTOMIZR_VER,
          //load in head if browser is chrome => fix the issue of 3Dtransform not detected in some cases
          ( isset($_SERVER['HTTP_USER_AGENT']) && false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) ? false : true
        );

        //customizr scripts and libs
  	   	if ( $this -> czr_fn_load_concatenated_front_scripts() )	{
          if ( $this -> czr_fn_is_fancyboxjs_required() )
            $this -> czr_fn_enqueue_script( 'tc-fancybox' );
          //!!tc-scripts includes underscore, tc-js-arraymap-proto
          $this -> czr_fn_enqueue_script( 'tc-scripts' );
  			}
  			else {
          wp_enqueue_script( 'underscore' );
          //!!mind the dependencies
          $this -> czr_fn_enqueue_script( array( 'tc-js-params', 'tc-js-arraymap-proto', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-smoothscroll', 'tc-outline', 'tc-waypoints' ) );

          if ( $this -> czr_fn_is_fancyboxjs_required() )
            $this -> czr_fn_enqueue_script( 'tc-fancybox' );

          $this -> czr_fn_enqueue_script( array( 'tc-dropcap' , 'tc-img-smartload', 'tc-ext-links', 'tc-center-images', 'tc-parallax', 'tc-main-front' ) );
  			}//end of load concatenate script if

        //carousel options
        //gets slider options if any for home/front page or for others posts/pages
        $js_slidername      = czr_fn__f('__is_home') ? czr_fn_opt( 'tc_front_slider' ) : get_post_meta( czr_fn_get_id() , $key = 'post_slider_key' , $single = true );
        $js_sliderdelay     = czr_fn__f('__is_home') ? czr_fn_opt( 'tc_slider_delay' ) : get_post_meta( czr_fn_get_id() , $key = 'slider_delay_key' , $single = true );

  			//has the post comments ? adds a boolean parameter in js
  			global $wp_query;
  			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

  			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
  			$anchor_smooth_scroll 		  = ( false != esc_attr( czr_fn_opt( 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
  			if ( false != esc_attr( czr_fn_opt( 'tc_link_scroll') ) )
  				wp_enqueue_script('jquery-effects-core');
              $anchor_smooth_scroll_exclude =  apply_filters( 'tc_anchor_smoothscroll_excl' , array(
                  'simple' => array( '[class*=edd]' , '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[data-toggle="pill"]', '[class*=upme]', '[class*=um-]' ),
                  'deep'   => array(
                    'classes' => array(),
                    'ids'     => array()
                  )
              ));

        $smooth_scroll_enabled = apply_filters('tc_enable_smoothscroll', ! wp_is_mobile() && 1 == esc_attr( czr_fn_opt( 'tc_smoothscroll') ) );
        $smooth_scroll_options = apply_filters('tc_smoothscroll_options', array( 'touchpadSupport' => false ) );

        //smart load
        $smart_load_enabled   = esc_attr( czr_fn_opt( 'tc_img_smart_load' ) );
        $smart_load_opts      = apply_filters( 'tc_img_smart_load_options' , array(
              'parentSelectors' => array(
                  '.article-container', '.__before_main_wrapper', '.widget-front',
              ),
              'opts'     => array(
                  'excludeImg' => array( '.tc-holder-img' )
              )
        ));
  			//gets current screen layout
      	$screen_layout      = CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'sidebar'  );
      	//gets the global layout settings
      	$global_layout      = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
      	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
  			//Gets the left and right sidebars class for js actions
  			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
  	    $right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

        //Style switcher note
        $is_style_switch_note_on = ! is_multisite() && czr_fn_user_can_see_customize_notices_on_front() && ! czr_fn_is_customizing() && ! czr_fn_isprevdem();
        $style_note_content = '';
        if ( $is_style_switch_note_on && ! czr_fn_is_ms() && false === czr_fn_opt( 'tc_style', CZR_THEME_OPTIONS, false ) ) { //false for not default
            $tc_custom_css = esc_html( czr_fn_opt( 'tc_custom_css') );
            $tc_custom_css = trim( $tc_custom_css );
            $wp_custom_css = '';
            if ( function_exists( "wp_get_custom_css" ) ) {
                $wp_custom_css = wp_get_custom_css();
                $wp_custom_css = trim( $wp_custom_css );
            }

            $is_style_switch_note_on = $is_style_switch_note_on && empty( $tc_custom_css ) && empty( $wp_custom_css );
            $is_style_switch_note_on = apply_filters(
                'czr_is_style_switch_notification_on',
                $is_style_switch_note_on && ! CZR_IS_MODERN_STYLE && ! is_child_theme() && 'dismissed' != get_transient( 'czr_style_switch_note_status' )
            );
            if ( $is_style_switch_note_on ) {
                $style_note_content = apply_filters( 'czr_style_note_content', '' );
            }
        }

  			wp_localize_script(
  	        $this -> czr_fn_load_concatenated_front_scripts() ? 'tc-scripts' : 'tc-js-params',
  	        'TCParams',
  	        apply_filters( 'tc_customizr_script_params' , array(
  	          	'_disabled'          => apply_filters( 'tc_disabled_front_js_parts', array() ),
                'FancyBoxState' 		=> $this -> czr_fn_is_fancyboxjs_required(),
  	          	'FancyBoxAutoscale' => ( 1 == czr_fn_opt( 'tc_fancybox_autoscale') ) ? true : false,
  	          	'SliderName' 			  => $js_slidername,
  	          	'SliderDelay' 			=> $js_sliderdelay,
  	          	'SliderHover'			  => apply_filters( 'tc_stop_slider_hover', true ),
  	          	'centerSliderImg'   => esc_attr( czr_fn_opt( 'tc_center_slider_img') ),
                'SmoothScroll'      => array( 'Enabled' => $smooth_scroll_enabled, 'Options' => $smooth_scroll_options ),
                'anchorSmoothScroll'			=> $anchor_smooth_scroll,
                'anchorSmoothScrollExclude' => $anchor_smooth_scroll_exclude,
  	          	'ReorderBlocks' 		=> esc_attr( czr_fn_opt( 'tc_block_reorder') ),
  	          	'centerAllImg' 			=> esc_attr( czr_fn_opt( 'tc_center_img') ),
  	          	'HasComments' 			=> $has_post_comments,
  	          	'LeftSidebarClass' 		=> $left_sb_class,
  	          	'RightSidebarClass' 	=> $right_sb_class,
  	          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
  	          	'stickyCustomOffset' 	=> apply_filters( 'tc_sticky_custom_offset' , array( "_initial" => 0, "_scrolling" => 0, "options" => array( "_static" => true, "_element" => "" ) ) ),
  	          	'stickyHeader' 			=> esc_attr( czr_fn_opt( 'tc_sticky_header' ) ),
  	          	'dropdowntoViewport' 	=> esc_attr( czr_fn_opt( 'tc_menu_resp_dropdown_limit_to_viewport') ),
  	          	'timerOnScrollAllBrowsers' => apply_filters( 'tc_timer_on_scroll_for_all_browser' , true), //<= if false, for ie only
                'extLinksStyle'       => esc_attr( czr_fn_opt( 'tc_ext_link_style' ) ),
                'extLinksTargetExt'   => esc_attr( czr_fn_opt( 'tc_ext_link_target' ) ),
                'extLinksSkipSelectors'   => apply_filters( 'tc_ext_links_skip_selectors' , array( 'classes' => array('btn', 'button') , 'ids' => array() ) ),
                'dropcapEnabled'      => esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ),
                'dropcapWhere'      => array( 'post' => esc_attr( czr_fn_opt( 'tc_post_dropcap' ) ) , 'page' => esc_attr( czr_fn_opt( 'tc_page_dropcap' ) ) ),
                'dropcapMinWords'     => esc_attr( czr_fn_opt( 'tc_dropcap_minwords' ) ),
                'dropcapSkipSelectors'  => apply_filters( 'tc_dropcap_skip_selectors' , array( 'tags' => array('IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'), 'classes' => array('btn', 'tc-placeholder-wrap' ) , 'id' => array() ) ),

                'imgSmartLoadEnabled' => $smart_load_enabled,
                'imgSmartLoadOpts'    => $smart_load_opts,
                'imgSmartLoadsForSliders' => czr_fn_is_checked( 'tc_slider_img_smart_load' ),

                'goldenRatio'         => apply_filters( 'tc_grid_golden_ratio' , 1.618 ),
                'gridGoldenRatioLimit' => esc_attr( czr_fn_opt( 'tc_grid_thumb_height' ) ),
                'isSecondMenuEnabled'  => czr_fn_is_secondary_menu_enabled(),
                'secondMenuRespSet'   => esc_attr( czr_fn_opt( 'tc_second_menu_resp_setting' ) ),

                'isParallaxOn'        => esc_attr( czr_fn_opt( 'tc_slider_parallax') ),
                'parallaxRatio'       => apply_filters('tc_parallax_ratio', 0.55 ),

                'pluginCompats'       => apply_filters( 'tc_js_params_plugin_compat', array() ),

                //AJAX
                'adminAjaxUrl'        => admin_url( 'admin-ajax.php' ),
                'ajaxUrl'             => add_query_arg(
                      array( 'czrajax' => true ), //to scope our ajax calls
                      set_url_scheme( home_url( '/' ) )
                ),
                'frontNonce'   => array( 'id' => 'CZRFrontNonce', 'handle' => wp_create_nonce( 'czr-front-nonce' ) ),

                'isDevMode'        => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('CZR_DEV') && true === CZR_DEV ),
                'isModernStyle'    => CZR_IS_MODERN_STYLE,

                'i18n' => apply_filters( 'czr_front_js_translated_strings',
                    array(
                        'Permanently dismiss' => __('Permanently dismiss', 'customizr')
                    )
                ),

                //FRONT NOTIFICATIONS
                //ordered by priority
                'frontNotifications' => array(
                      'styleSwitcher' => array(
                          'enabled' => $is_style_switch_note_on,
                          'content' => $style_note_content,
                          'dismissAction' => 'dismiss_style_switcher_note_front',
                          'ajaxUrl' => admin_url( 'admin-ajax.php' )
                      )
                )
  	        	),
  	        	czr_fn_get_id()
  		    )//end of filter
  	     );

  	    //fancybox style
  	    if ( $this -> czr_fn_is_fancyboxjs_required() )
  	      wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'assets/front/js/libs/fancybox/jquery.fancybox-1.3.4.min.css' );

  	    //holder.js is loaded when featured pages are enabled AND FP are set to show images and at least one holder should be displayed.
        $tc_show_featured_pages 	         = class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages();
      	if ( 0 != $tc_show_featured_pages && $this -> czr_fn_maybe_is_holder_js_required() ) {
  	    	wp_enqueue_script(
  	    		'holder',
  	    		sprintf( '%1$sassets/front/js/libs/holder.min.js' , TC_BASE_URL ),
  	    		array(),
  	    		CUSTOMIZR_VER,
  	    		$in_footer = true
  	    	);
  	    }

  	    //load retina.js in footer if enabled
  	    if ( apply_filters('tc_load_retinajs', 1 == czr_fn_opt( 'tc_retina_support' ) ) )
  	    	wp_enqueue_script( 'retinajs' ,TC_BASE_URL . 'assets/front/js/libs/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

  	    //Load hammer.js for mobile
  	    if ( apply_filters('tc_load_hammerjs', wp_is_mobile() ) )
  	    	wp_enqueue_script( 'hammer' ,TC_BASE_URL . 'assets/front/js/libs/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

  		}



      /**
      * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
      * hook : tc_user_options_style
      * @package Customizr
      * @since Customizr 2.0.7
      */
      function czr_fn_write_custom_css( $_css = null ) {
        $_css               = isset($_css) ? $_css : '';

        $_moved_opts        = czr_fn_opt(  '__moved_opts' ) ;

        /*
        * Do not print old custom css if moved in the WP Custom CSS
        */
        if ( !empty( $_moved_opts ) && is_array( $_moved_opts ) && in_array( 'custom_css', $_moved_opts) )
          return $_css;

        $tc_custom_css      = esc_html( czr_fn_opt( 'tc_custom_css') );
        if ( ! isset($tc_custom_css) || empty($tc_custom_css) )
          return $_css;

        return apply_filters( 'tc_write_custom_css',
          $_css . "\n" . html_entity_decode( $tc_custom_css ),
          $_css,
          czr_fn_opt( 'tc_custom_css')
        );
      }//end of function


      /* See: https://github.com/presscustomizr/customizr/issues/605 */
      function czr_fn_apply_media_upload_front_patch( $_css ) {
        global $wp_version;
        if ( version_compare( '4.5', $wp_version, '<=' ) )
          $_css = sprintf("%s%s",
    		            	$_css,
                          'table { border-collapse: separate; }
                           body table { border-collapse: collapse; }
                          ');
        return $_css;
      }

      /*
      * Use the dynamic style to fix server side caching issue,
      * which is the main reason why we needed this patch
      * We don't subordinate this to the user_started_before a certain version
      * as it also fixes potential plugin compatibility (plugins which style .icon-* before)
      * https://github.com/presscustomizr/customizr/issues/787
      * ( all this will be removed in c4 )
      */
      function czr_fn_maybe_avoid_double_social_icon( $_css ) {
        return sprintf( "%s\n%s", $_css, '.social-links .social-icon:before { content: none } ');
      }

      /*
      * Callback of wp_enqueue_scripts
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function czr_fn_enqueue_gfonts() {
        $_font_pair         = esc_attr( czr_fn_opt( 'tc_fonts' ) );
        $_all_font_pairs    = CZR___::$instance -> font_pairs;
        if ( ! czr_fn_is_gfont( $_font_pair , '_g_') )
          return;

        wp_enqueue_style(
          'tc-gfonts',
          sprintf( '//fonts.googleapis.com/css?family=%s', str_replace( '|', '%7C', czr_fn_get_font( 'single' , $_font_pair ) ) ),
          array(),
          null,
          'all'
        );
      }



      /**
      * Callback of tc_user_options_style hook
      * + Fired in czr_fn_user_defined_tinymce_css => add the user defined font style to the wp editor
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function czr_fn_write_fonts_inline_css( $_css = null , $_context = null ) {
        $_css               = isset($_css) ? $_css : '';
        $_font_pair         = esc_attr( czr_fn_opt( 'tc_fonts' ) );
        $_body_font_size    = esc_attr( czr_fn_opt( 'tc_body_font_size' ) );
        $_font_selectors    = CZR_init::$instance -> font_selectors;

        //create the $body and $titles vars
        extract( CZR_init::$instance -> font_selectors, EXTR_OVERWRITE );

        if ( ! isset($body) || ! isset($titles) )
          return;

        //adapt the selectors in edit context => add specificity for the mce-editor
        if ( ! is_null( $_context ) ) {
          $titles = ".{$_context} h1, .{$_context} h2, .{$_context} h3";
          $body   = "body.{$_context}";
        }

        $titles = apply_filters('tc_title_fonts_selectors' , $titles );
        $body   = apply_filters('tc_body_fonts_selectors' , $body );

        if ( 'helvetica_arial' != $_font_pair ) {//check if not default
          $_selector_fonts  = explode( '|', czr_fn_get_font( 'single' , $_font_pair ) );
          if ( ! is_array($_selector_fonts) )
            return $_css;

          foreach ($_selector_fonts as $_key => $_raw_font) {
            //create the $_family and $_weight vars
            extract( $this -> czr_fn_get_font_css_prop( $_raw_font , czr_fn_is_gfont( $_font_pair ) ) );

            switch ($_key) {
              case 0 : //titles font
                $_css .= "
                  {$titles} {
                    font-family : {$_family};
                    font-weight : {$_weight};
                  }\n";
              break;

              case 1 ://body font
                $_css .= "
                  {$body} {
                    font-family : {$_family};
                    font-weight : {$_weight};
                  }\n";
              break;
            }
          }
        }//end if

        if ( 15 != $_body_font_size ) {
          $_line_height = apply_filters('tc_body_line_height_ratio', 1.6 );
          $_css .= "
            {$body} {
              font-size : {$_body_font_size}px;
              line-height : {$_line_height}em;
            }\n";
          }

        return $_css;
      }//end of fn


      /**
      * Callback of tc_user_options_style hook
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.11
      */
      function czr_fn_write_dropcap_inline_css( $_css = null , $_context = null ) {
        $_css               = isset($_css) ? $_css : '';
        if ( ! esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ) )
          return $_css;

        $_main_color_pair = CZR_utils::$inst -> czr_fn_get_skin_color( 'pair' );
        $_color           = $_main_color_pair[0];
        $_shad_color      = $_main_color_pair[1];
        $_pad_right       = false !== strpos( esc_attr( czr_fn_opt( 'tc_fonts' ) ), 'lobster' ) ? 26 : 8;
        $_css .= "
          .tc-dropcap {
            color: {$_color};
            float: left;
            font-size: 75px;
            line-height: 75px;
            padding-right: {$_pad_right}px;
            padding-left: 3px;
          }\n
          .skin-shadow .tc-dropcap {
            color: {$_color};
            text-shadow: {$_shad_color} -1px 0, {$_shad_color} 0 -1px, {$_shad_color} 0 1px, {$_shad_color} -1px -2px;
          }\n
          .simple-black .tc-dropcap {
            color: #444;
          }\n";

        return $_css;
      }


      /**
      * Set random skin
      * hook tc_opt_tc_skin
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_set_random_skin ( $_skin ) {
        if ( false == esc_attr( czr_fn_opt( 'tc_skin_random' ) ) )
          return $_skin;

        //allow custom skins to be taken in account
        $_skins = apply_filters( 'tc_get_skin_color', CZR___::$instance -> skin_classic_color_map, 'all' );

        //allow users to filter the list of skins they want to randomize
        $_skins = apply_filters( 'tc_skins_to_randomize', $_skins );

        /* Generate the random skin just once !*/
        if ( ! $this -> current_random_skin && is_array( $_skins ) )
          $this -> current_random_skin = array_rand( $_skins, 1 );

        return $this -> current_random_skin;
      }


      /*************************************
      * HELPERS
      *************************************/
      /**
      * Helper to extract font-family and weight from a Customizr font option
      * @return array( font-family, weight )
      *
      * @package Customizr
      * @since Customizr 3.3.2
      */
      private function czr_fn_get_font_css_prop( $_raw_font , $is_gfont = false ) {
        $_css_exp = explode(':', $_raw_font);
        $_weight  = isset( $_css_exp[1] ) ? $_css_exp[1] : 'inherit';
        $_family  = '';

        if ( $is_gfont ) {
          $_family = str_replace('+', ' ' , $_css_exp[0]);
        } else {
          $_family = implode("','", explode(',', $_css_exp[0] ) );
        }
        $_family = sprintf("'%s'" , $_family );

        return compact("_family" , "_weight" );
      }


      /**
      * Convenient method to normalize script enqueueing in the Customizr theme
      * @return  void
      * @uses wp_enqueue_script() to manage script dependencies
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_enqueue_script( $_handles = array() ) {
        if ( empty($_handles) )
          return;

        $_map = $this -> tc_script_map;
        //Picks the requested handles from map
        if ( 'string' == gettype($_handles) && isset($_map[$_handles]) ) {
          $_scripts = array( $_handles => $_map[$_handles] );
        }
        else {
          $_scripts = array();
          foreach ( $_handles as $_hand ) {
            if ( !isset( $_map[$_hand]) )
              continue;
            $_scripts[$_hand] = $_map[$_hand];
          }
        }

        //Enqueue the scripts with normalizes args
        foreach ( $_scripts as $_hand => $_params )
          call_user_func_array( 'wp_enqueue_script',  $this -> czr_fn_normalize_script_args( $_hand, $_params ) );

      }//end of fn



      /**
      * Helper to normalize the arguments passed to wp_enqueue_script()
      * Also handles the minified version of the file
      *
      * @return array of arguments for wp_enqueue_script
      * @package Customizr
      * @since Customizr 3.3+
      */
      private function czr_fn_normalize_script_args( $_handle, $_params ) {
        //Do we load the minified version if available ?
        if ( count( $_params['files'] ) > 1 )
          $_filename = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? $_params['files'][0] : $_params['files'][1];
        else
          $_filename = $_params['files'][0];

        return array(
          $_handle,
          sprintf( '%1$s%2$s%3$s',TC_BASE_URL , $_params['path'], $_filename ),
          $_params['dependencies'],
          CZR_DEBUG_MODE || CZR_DEV_MODE ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER,
          apply_filters( "tc_load_{$_handle}_in_footer", false )
        );
      }

      /**
      * Helper
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      function czr_fn_load_concatenated_front_scripts() {
          return apply_filters( 'tc_load_concatenated_front_scripts' , ! defined('CZR_DEV')  || ( defined('CZR_DEV') && false == CZR_DEV ) );
      }

      /**
      * Helper to check if we need fancybox or not on front
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      private function czr_fn_is_fancyboxjs_required() {
        return czr_fn_opt( 'tc_fancybox' ) || czr_fn_opt( 'tc_gallery_fancybox');
      }

      /**
      * Helper to check if we need to enqueue holder js
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      function czr_fn_maybe_is_holder_js_required(){
        $bool = false;

        if ( ! ( class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages_img() ) )
          return $bool;

        $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR___::$instance -> fp_ids);

        foreach ( $fp_ids as $fp_single_id ){
          $featured_page_id = czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
          if ( null == $featured_page_id || ! $featured_page_id || ! CZR_featured_pages::$instance -> czr_fn_get_fp_img( null, $featured_page_id, null ) ) {
            $bool = true;
            break;
          }
        }
        return $bool;
      }

      /* ------------------------------------------------------------------------- *
       *  STYLE NOTE
      /* ------------------------------------------------------------------------- */
      //hook : 'czr_style_note_content'
      //This function is invoked only when :
      //1) czr_fn_user_started_before_version( '4.0.0', '2.0.0' )
      //2) AND if the note can be displayed : czr_fn_user_can_see_customize_notices_on_front() && ! czr_fn_is_customizing() && ! czr_fn_isprevdem() && 'dismissed' != get_transient( 'czr_style_switch_note_status' )
      //It returns a welcome note html string that will be localized in the front js
      //@return html string
      function czr_fn_get_style_note_content() {
        // beautify notice text using some defaults the_content filter callbacks
        // => turns emoticon :D into an svg
        foreach ( array( 'wptexturize', 'convert_smilies', 'wpautop') as $callback ) {
          if ( function_exists( $callback ) )
              add_filter( 'czr_front_style_switch_note_html', $callback );
        }
        ob_start();
          ?>
              <?php
                  printf( '<br/><p>%1$s</p>',
                      sprintf( __('Quick tip : you can choose between two styles for the Customizr theme. Give it a try %s', 'customizr'),
                          sprintf( '<a href="%1$s">%2$s</a>',
                              czr_fn_get_customizer_url( array( 'control' => 'tc_style', 'section' => 'style_sec') ),
                              __('in the live customizer.', 'customizr')
                          )
                      )
                  );
              ?>

          <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        return $html; //apply_filters('czr_front_style_switch_note_html', $html );
    }


    //hook : czr_ajax_dismiss_style_switcher_note_front
    function czr_fn_dismiss_style_switcher_note_front() {
        set_transient( 'czr_style_switch_note_status', 'dismissed' , 60*60*24*365*20 );//20 years of peace
        wp_send_json_success( array( 'status_note' => 'dismissed' ) );
    }
  }//end of CZR_ressources
endif;

?><?php
/**
* Widgets factory : registered the different widgetized areas
* The default widget areas are defined as properties of the CZR_utils class in class-fire-utils.php
* CZR_utils::$inst -> sidebar_widgets for left and right sidebars
* CZR_utils::$inst -> footer_widgets for the footer
* The widget area are then fired in the class below
*
*/
if ( ! class_exists( 'CZR_widgets' ) ) :
  class CZR_widgets {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //widgets actions
      add_action( 'widgets_init'                    , array( $this , 'czr_fn_widgets_factory' ) );
    }

    /******************************************
    * REGISTER WIDGETS
    ******************************************/
    /**
    * Registers the widget areas
    * hook : widget_init
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_widgets_factory() {
      //default Customizr filtered args
      $default                  = apply_filters( 'tc_default_widget_args' ,
                                array(
                                  'name'                    => '',
                                  'id'                      => '',
                                  'description'             => '',
                                  'class'                   => '',
                                  'before_widget'           => '<aside id="%1$s" class="widget %2$s">',
                                  'after_widget'            => '</aside>',
                                  'before_title'            => '<h3 class="widget-title">',
                                  'after_title'             => '</h3>',
                                )
      );

      //gets the filtered default values
      $footer_widgets           = apply_filters( 'tc_footer_widgets'  , CZR_init::$instance -> footer_widgets );
      $sidebar_widgets          = apply_filters( 'tc_sidebar_widgets' , CZR___::$instance -> sidebar_widgets );
      $widgets                  = apply_filters( 'tc_default_widgets' , array_merge( $sidebar_widgets , $footer_widgets ) );

      //declares the arguments array
      $args                     = array();

      //fills in the $args array and registers sidebars
      foreach ( $widgets as $id => $infos) {
          foreach ( $default as $key => $default_value ) {
            if ('id' == $key ) {
              $args[$key] = $id;
            }
            else if ( 'name' == $key || 'description' == $key) {
              $args[$key] = !isset($infos[$key]) ? $default_value : call_user_func( '__' , $infos[$key] , 'customizr' );
            }
            else {
              $args[$key] = !isset($infos[$key]) ? $default_value : $infos[$key];
            }
          }
        //registers sidebars
        register_sidebar( $args );
      }
    }
  }//end of class
endif;

?><?php

/* HELPERS SPECIFICS FOR CLASSICAL THAT'S WHY DEFINED HERE AND NOT IN THE SHARED FUNCTIONS*/

/**
* helper
* Renders the main header
* @return  void
*/
if ( ! function_exists( 'czr_fn_render_main_header' ) ) {
  function czr_fn_render_main_header() {
    CZR_header_main::$instance->czr_fn_set_header_options();
  ?>
    <header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>">
    <?php
      // The '__header' hook is used with the following callback functions (ordered by priorities) :
      //CZR_header_main::$instance->tc_logo_title_display(), CZR_header_main::$instance->czr_fn_tagline_display(), CZR_header_main::$instance->czr_fn_navbar_display()
      do_action( '__header' );
    ?>
    </header>
  <?php
  }
}


/**
* helper
* Renders or returns the filtered and escaped tagline
* @return  void
*/
if ( ! function_exists( 'czr_fn_get_tagline_text' ) ) {
  function czr_fn_get_tagline_text( $echo = true ) {
    $tagline_text = apply_filters( 'tc_tagline_text', get_bloginfo( 'description', 'display' ) );
    if ( ! $echo )
      return $tagline_text;
    echo $tagline_text;
  }
}




//fire an action hook before loading the theme
do_action( 'czr_before_init' );
//Creates a new instance
new CZR___;
//fire an action hook after loading the theme
do_action( 'czr_after_init' );


//fire an action hook before loading the theme class groups
do_action( 'czr_before_load' );

//classic CZR___ will hook here to instantiate theme class groups
do_action('czr_load');

//may be load pro
if ( CZR_IS_PRO ) {
    new CZR_init_pro(CZR___::$theme_name );
}

//fire an action hook after loading the theme class groups
do_action( 'czr_after_load' );
?>