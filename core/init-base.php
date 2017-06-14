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
              self::$theme_name         = CZR_SANITIZED_THEMENAME;

              self::$db_options         = false === get_option( CZR_THEME_OPTIONS ) ? array() : (array)get_option( CZR_THEME_OPTIONS );
              self::$default_options    = czr_fn_get_default_options();
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

  }
endif;


//load shared fn
require_once( get_template_directory() . '/core/functions-base.php' );

//require init-pro if it exists
if ( file_exists( get_template_directory() . '/core/init-pro.php' ) )
  require_once( get_template_directory() . '/core/init-pro.php' );

//setup constants
czr_fn_setup_constants();

if ( czr_fn_is_modern_style() ) {
  require_once( get_template_directory() . '/core/init.php' );
} else {
  require_once( get_template_directory() . '/inc/czr-init.php' );
}