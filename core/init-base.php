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

        function __construct( $_args = array()) {
            //init properties
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_init_properties') );

            //IMPORTANT : this callback needs to be ran AFTER czr_fn_init_properties.
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_cache_theme_setting_list' ), 100 );

            //refresh the theme options right after the _preview_filter when previewing
            add_action( 'customize_preview_init'  , array( $this , 'czr_fn_customize_refresh_db_opt' ) );

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
              self::$db_options       = false === get_option( CZR_THEME_OPTIONS ) ? array() : (array)get_option( CZR_THEME_OPTIONS );
              self::$default_options  = czr_fn_get_default_options();
              $_trans                   = CZR_IS_PRO ? 'started_using_customizr_pro' : 'started_using_customizr';

              //What was the theme version when the user started to use Customizr?
              //new install = no options yet
              //very high duration transient, this transient could actually be an option but as per the themes guidelines, too much options are not allowed.
              if ( 1 >= count( self::$db_options ) || ! esc_attr( get_transient( $_trans ) ) ) {
                set_transient(
                  $_trans,
                  sprintf('%s|%s' , 1 >= count( self::$db_options ) ? 'with' : 'before', CUSTOMIZR_VER ),
                  60*60*24*9999
                );
              }
        }


        /* ------------------------------------------------------------------------- *
         *  CACHE THE LIST OF THEME SETTINGS ONLY
        /* ------------------------------------------------------------------------- */
        //Fired in __construct()
        function czr_fn_cache_theme_setting_list() {
          if ( is_array(self::$theme_setting_list) && ! empty( self::$theme_setting_list ) )
            return;

          self::$theme_setting_list = czr_fn_generate_theme_setting_list();
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



        protected function czr_fn_setup_constants() {

          /* GETS INFORMATIONS FROM STYLE.CSS */
          // get themedata version wp 3.4+
          if( function_exists( 'wp_get_theme' ) ) {
            //get WP_Theme object of customizr
            $tc_theme                     = wp_get_theme();

            //Get infos from parent theme if using a child theme
            $tc_theme = $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

            $tc_base_data['prefix']       = $tc_base_data['title'] = $tc_theme -> name;
            $tc_base_data['version']      = $tc_theme -> version;
            $tc_base_data['authoruri']    = $tc_theme -> {'Author URI'};
          }

          // get themedata for lower versions (get_stylesheet_directory() points to the current theme root, child or parent)
          else {
               $tc_base_data                = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
               $tc_base_data['prefix']      = $tc_base_data['title'];
          }

          self::$theme_name                 = sanitize_file_name( strtolower($tc_base_data['title']) );

          //CUSTOMIZR_VER is the Version
          if( ! defined( 'CUSTOMIZR_VER' ) )            define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
          //CZR_BASE is the root server path of the parent theme
          if( ! defined( 'CZR_BASE' ) )                 define( 'CZR_BASE' , get_template_directory().'/' );
          //CZR_UTILS_PREFIX is the relative path where the utils classes are located
          if( ! defined( 'CZR_CORE_PATH' ) )            define( 'CZR_CORE_PATH' , 'core/' );
          //CZR_MAIN_TEMPLATES_PATH is the relative path where the czr4 WordPress templates are located
          if( ! defined( 'CZR_MAIN_TEMPLATES_PATH' ) )  define( 'CZR_MAIN_TEMPLATES_PATH' , 'core/main-templates/' );
          //CZR_UTILS_PREFIX is the relative path where the utils classes are located
          if( ! defined( 'CZR_UTILS_PATH' ) )           define( 'CZR_UTILS_PATH' , 'core/_utils/' );
          //CZR_FRAMEWORK_PATH is the relative path where the framework is located
          if( ! defined( 'CZR_FRAMEWORK_PATH' ) )       define( 'CZR_FRAMEWORK_PATH' , 'core/_framework/' );
          //CZR_PHP_FRONT_PATH is the relative path where the framework front files are located
          if( ! defined( 'CZR_PHP_FRONT_PATH' ) )       define( 'CZR_PHP_FRONT_PATH' , 'core/front/' );
          //CZR_ASSETS_PREFIX is the relative path where the assets are located
          if( ! defined( 'CZR_ASSETS_PREFIX' ) )        define( 'CZR_ASSETS_PREFIX' , 'assets/' );
          //CZR_BASE_CHILD is the root server path of the child theme
          if( ! defined( 'CZR_BASE_CHILD' ) )           define( 'CZR_BASE_CHILD' , get_stylesheet_directory().'/' );
          //CZR_BASE_URL http url of the loaded parent theme
          if( ! defined( 'CZR_BASE_URL' ) )             define( 'CZR_BASE_URL' , get_template_directory_uri() . '/' );
          //CZR_BASE_URL_CHILD http url of the loaded child theme
          if( ! defined( 'CZR_BASE_URL_CHILD' ) )       define( 'CZR_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' );
          //CZR_THEMENAME contains the Name of the currently loaded theme
          if( ! defined( 'CZR_THEMENAME' ) )            define( 'CZR_THEMENAME' , $tc_base_data['title'] );
          //CZR_WEBSITE is the home website of Customizr
          if( ! defined( 'CZR_WEBSITE' ) )              define( 'CZR_WEBSITE' , $tc_base_data['authoruri'] );
          //OPTION PREFIX //all customizr theme options start by "tc_" by convention (actually since the theme was created.. tc for Themes & Co...)
          if( ! defined( 'CZR_OPT_PREFIX' ) )           define( 'CZR_OPT_PREFIX' , apply_filters( 'czr_options_prefixes', 'tc_' ) );
          //MAIN OPTIONS NAME
          if( ! defined( 'CZR_THEME_OPTIONS' ) )        define( 'CZR_THEME_OPTIONS', apply_filters( 'czr_options_name', 'tc_theme_options' ) );
          //CZR_CZR_PATH is the relative path where the Customizer php is located
          if( ! defined( 'CZR_CZR_PATH' ) )             define( 'CZR_CZR_PATH' , 'core/czr/' );
          //CZR_FRONT_ASSETS_URL http url of the front assets
          if( ! defined( 'CZR_FRONT_ASSETS_URL' ) )     define( 'CZR_FRONT_ASSETS_URL' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/' );
          //if( ! defined( 'CZR_OPT_AJAX_ACTION' ) )      define( 'CZR_OPT_AJAX_ACTION' , 'czr_fn_get_opt' );//DEPRECATED
          //IS PRO
          if( ! defined( 'CZR_IS_PRO' ) )               define( 'CZR_IS_PRO' , czr_fn_is_pro() );

          //IS DEBUG MODE
          if( ! defined( 'CZR_DEBUG_MODE' ) )           define( 'CZR_DEBUG_MODE', ( defined('WP_DEBUG') && true === WP_DEBUG ) );

          //IS DEV MODE
          if( ! defined( 'CZR_DEV_MODE' ) )             define( 'CZR_DEV_MODE', ( defined('CZR_DEV') && true === CZR_DEV ) );

          //retro compat for FPU and WFC plugins

          //TC_BASE_URL http url of the loaded parent theme (retro compat)
          if( ! defined( 'TC_BASE' ) )            define( 'TC_BASE' , CZR_BASE );
          //TC_BASE_CHILD is the root server path of the child theme
          if( ! defined( 'TC_BASE_CHILD' ) )      define( 'TC_BASE_CHILD' , CZR_BASE_CHILD );
          //TC_BASE_URL http url of the loaded parent theme (retro compat)
          if( ! defined( 'TC_BASE_URL' ) )        define( 'TC_BASE_URL' , CZR_BASE_URL );
          //TC_BASE_URL_CHILD http url of the loaded child theme
          if( ! defined( 'TC_BASE_URL_CHILD' ) )  define( 'TC_BASE_URL_CHILD' , CZR_BASE_URL_CHILD );

        }

  }
endif;


//load shared fn
require_once( get_template_directory() . '/core/functions-base.php' );

if ( czr_fn_is_modern_style() ) {
  require_once( get_template_directory() . '/core/init.php' );
} else {
  require_once( get_template_directory() . '/inc/czr-init.php' );
}