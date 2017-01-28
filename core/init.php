<?php

/**
* Fires the theme : constants definition, core classes loading
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
if ( ! class_exists( 'CZR___' ) ) :

  final class CZR___ {
        public static $instance;//@todo make private in the future
        public $czr_core;

        static $default_options;
        static $db_options;
        static $options;//not used in customizer context only

        public $collection;
        public $views;//object, stores the views
        public $controllers;//object, stores the controllers

        //stack
        public $current_model = array();


        function __construct( $_args = array()) {
            //init properties
            add_action( 'after_setup_theme'       , array( $this , 'czr_fn_init_properties') );

            //this action callback is the one responsible to load new czr main templates
            add_action( 'czr_four_template'       , array( $this, 'czr_fn_four_template_redirect' ), 10 , 1 );

            //refresh the theme options right after the _preview_filter when previewing
            add_action( 'customize_preview_init'  , array( $this , 'czr_fn_customize_refresh_db_opt' ) );

            //filters to 'the_content', 'wp_title' => in utils
            add_action( 'wp_head' , 'czr_fn_wp_filters' );
        }



        public static function czr_fn_instance() {
              if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CZR___ ) ) {
                self::$instance = new CZR___();
                self::$instance -> czr_fn_setup_constants();
                self::$instance -> czr_fn_setup_loading();
                self::$instance -> czr_fn_load();

                //FMK
                self::$instance -> collection = new CZR_Collection();
                self::$instance -> controllers = new CZR_Controllers();

                //register the model's map in front
                if ( ! is_admin() )
                  add_action('wp'         , array(self::$instance, 'czr_fn_register_model_map') );
              }
              return self::$instance;
        }



        /**
        * Init CZR_utils class properties after_setup_theme
        * Fixes the bbpress bug : Notice: bbp_setup_current_user was called incorrectly. The current user is being initialized without using $wp->init()
        * czr_fn_get_default_options uses is_user_logged_in() => was causing the bug
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
        * The purpose of this callback is to load the themes bootstrap4 main templates
        * hook : czr_four_template
        * @return  void
        */
        public function czr_fn_four_template_redirect( $template = null ) {
          $template = $template ? $template : 'index';
          $this -> czr_fn_require_once( CZR_MAIN_TEMPLATES_PATH . $template . '.php' );
        }








        private function czr_fn_setup_constants() {
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

              //CUSTOMIZR_VER is the Version
              if( ! defined( 'CUSTOMIZR_VER' ) )            define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
              //CZR_BASE is the root server path of the parent theme
              if( ! defined( 'CZR_BASE' ) )                 define( 'CZR_BASE' , get_template_directory().'/' );
              //CZR_UTILS_PREFIX is the relative path where the utils classes are located
              if( ! defined( 'CZR_CORE_PATH' ) )            define( 'CZR_CORE_PATH' , 'core/' );
              //CZR_MAIN_TEMPLATES_PATH is the relative path where the czr4 WordPress templates are located
              if( ! defined( 'CZR_MAIN_TEMPLATES_PATH' ) )  define( 'CZR_MAIN_TEMPLATES_PATH' , 'core/main-templates/' );
              //CZR_UTILS_PREFIX is the relative path where the utils classes are located
              if( ! defined( 'CZR_UTILS_PATH' ) )           define( 'CZR_UTILS_PATH' , 'core/utils/' );
              //CZR_FRAMEWORK_PATH is the relative path where the framework is located
              if( ! defined( 'CZR_FRAMEWORK_PATH' ) )       define( 'CZR_FRAMEWORK_PATH' , 'core/framework/' );
              //CZR_FRAMEWORK_FRONT_PATH is the relative path where the framework front files are located
              if( ! defined( 'CZR_FRAMEWORK_FRONT_PATH' ) ) define( 'CZR_FRAMEWORK_FRONT_PATH' , 'core/front/' );
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

              //if( ! defined( 'CZR_OPT_AJAX_ACTION' ) )      define( 'CZR_OPT_AJAX_ACTION' , 'czr_fn_get_opt' );//DEPRECATED
              //IS PRO
              if( ! defined( 'CZR_IS_PRO' ) )               define( 'CZR_IS_PRO' , file_exists( sprintf( '%score/init-pro.php' , CZR_BASE ) ) && "customizr-pro" == CZR_THEMENAME );

        }//setup_contants()




        private function czr_fn_setup_loading() {
              //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
              $this -> czr_core = apply_filters( 'czr_core',
                array(
                    'fire'      =>   array(
 //                       array('core'       , 'resources_styles'),
                        array('core'       , 'resources_fonts'),
 //                       array('core'       , 'resources_scripts'),
                        array('core'       , 'widgets'),//widget factory
 //                       array('core/back'  , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
//                        array('core/back'  , 'admin_page')//creates the welcome/help panel including changelog and system config
                    ),
                    'admin'     => array(
//                        array('core/back' , 'customize'),//loads customizer actions and resources
                        array('core/back' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
                    ),
                    'header'    =>   array(
                        array('core/front/utils', 'nav_walker')
                    ),
                    'content'   =>   array(
   //                     array('core/front/utils', 'gallery')
                    ),
 //                   'addons'    => apply_filters( 'czr_addons_classes' , array() )
                )
              );
              //check the context
//              if ( CZR_IS_PRO )
//                require_once( sprintf( '%score/init-pro.php' , CZR_BASE ) );

              //set files to load according to the context : admin / front / customize
              add_filter( 'czr_get_files_to_load' , array( $this , 'czr_fn_set_files_to_load' ) );
        }


        /**
        * Class instantiation using a singleton factory :
        * Can be called to instantiate a specific class or group of classes
        * @param  array(). Ex : array ('admin' => array( array( 'core/back' , 'meta_boxes') ) )
        * @return  instances array()
        *
        * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
        *
        * @since Customizr 3.0
        */
        function czr_fn_load( $_to_load = array(), $_no_filter = false ) {

            //loads init
            $this -> czr_fn_require_once( CZR_CORE_PATH . 'class-fire-init.php' );
            new CZR_init();

            //loads the plugin compatibility
            $this -> czr_fn_require_once( CZR_CORE_PATH . 'class-fire-plugins_compat.php' );
            new CZR_plugins_compat();

            //loads utils
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils_settings_map.php' );
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils.php' );
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils_options.php' );
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils_query.php' );
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils_thumbnails.php' );

            //Helper class to build a simple date diff object
            //Alternative to date_diff for php version < 5.3.0
            $this -> czr_fn_require_once( CZR_UTILS_PATH . 'class-fire-utils_date.php' );

            //do we apply a filter ? optional boolean can force no filter
            $_to_load = $_no_filter ? $_to_load : apply_filters( 'czr_get_files_to_load' , $_to_load );
            if ( empty($_to_load) )
              return;

            foreach ( $_to_load as $group => $files )
              foreach ($files as $path_suffix ) {
                $this -> czr_fn_require_once ( $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] . '.php');
                $classname = 'CZR_' . $path_suffix[1];
                if ( in_array( $classname, apply_filters( 'czr_dont_instantiate_in_init', array( 'CZR_nav_walker') ) ) )
                  continue;
                //instantiates
                $instances = class_exists($classname)  ? new $classname : '';
              }


            //load the new framework classes
            $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-model.php' );
            $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-collection.php' );
            $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-view.php' );
            $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-controllers.php' );
        }



        //hook : wp
        function czr_fn_register_model_map( $_map = array() ) {
          $_to_register =  ( empty( $_map ) || ! is_array($_map) ) ? $this -> czr_fn_get_model_map() : $_map;
          $CZR          = CZR();

          foreach ( $_to_register as $model ) {
            $CZR -> collection -> czr_fn_register( $model);
          }

        }


        //returns an array of models describing the theme's views
        private function czr_fn_get_model_map() {
          return apply_filters(
            'czr_model_map',
            array(
              /*********************************************
              * ROOT HTML STRUCTURE
              *********************************************/
              array(
                'hook' => 'wp_head' ,
                'template' => 'header/favicon',
              ),


              /*********************************************
              * HEADER
              *********************************************/
              array(
                'model_class'    => 'header',
                'id'             => 'header'
              ),

              /*********************************************
              * SLIDER
              *********************************************/
              /* Need to be pre-registered because of the custom style*/
              array(
                'model_class' => 'modules/slider/slider',
                'id'          => 'main_slider'
              ),
              //slider of posts
              array(
                'id'          => 'main_posts_slider',
                'model_class' => array( 'parent' => 'modules/slider/slider', 'name' => 'modules/slider/slider_of_posts' )
              ),
              /** end slider **/


            //   /*********************************************
            //   * Featured Pages
            //   *********************************************/
            //   /* contains the featured page item registration */
            //   array(
            //     'id'          => 'featured_pages',
            //     'model_class' => 'modules/featured-pages/featured_pages',
            //   ),
            //   /** end featured pages **/

              /*********************************************
              * CONTENT
              *********************************************/
              array(
                'id'             => 'main_content',
                'model_class'    => 'content',
              ),

            //   /*********************************************
            //   * FOOTER
            //   *********************************************/
            //   array(
            //     'id'           => 'footer',
            //     'model_class'  => 'footer',
            //   ),
            )
          );
        }




        /***************************
        * HELPERS
        ****************************/
        /**
        * Check the context and return the modified array of class files to load and instantiate
        * hook : czr_fn_get_files_to_load
        * @return boolean
        *
        * @since  Customizr 3.3+
        */
        function czr_fn_set_files_to_load( $_to_load ) {
          $_to_load = empty($_to_load) ? $this -> czr_core : $_to_load;
          //Not customizing
          //1) IS NOT CUSTOMIZING : czr_fn_is_customize_left_panel() || czr_fn_is_customize_preview_frame() || czr_fn_doing_customizer_ajax()
          //---1.1) IS ADMIN
          //---1.2) IS NOT ADMIN
          //2) IS CUSTOMIZING
          //---2.1) IS LEFT PANEL => customizer controls
          //---2.2) IS RIGHT PANEL => preview
          if ( ! czr_fn_is_customizing() )
            {
              if ( is_admin() )
                $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|core/back|customize' ) );
              else
                //Skips all admin classes
                $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|core/back|admin_init', 'fire|core/back|admin_page') );
            }
          //Customizing
          else
            {
              //left panel => skip all front end classes
              if (czr_fn_is_customize_left_panel() ) {
                $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array( 'header' , 'content' , 'footer' ),
                  array( 'fire|core|resources_styles' , 'fire|core', 'fire|core|resources_scripts', 'fire|core/back|admin_page' , 'admin|core/back|meta_boxes' )
                );
              }
              if ( czr_fn_is_customize_preview_frame() ) {
                $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array(),
                  array( 'fire|core/back|admin_init', 'fire|core/back|admin_page' , 'admin|core/back|meta_boxes' )
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
        * Specific syntax for single files: ex in fire|core/back|admin_page
        * => fire is the group, core/back is the path, admin_page is the file suffix.
        * => will unset core/back/class-fire-admin_page.php
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
              //$_concat looks like : fire|core|resources
              $_exploded = explode( '|', $_concat );
              //each single file entry must be a string like 'admin|core/back|customize'
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


        //called when requiring a file - will always give the precedence to the child-theme file if it exists
        //then to the theme root
        function czr_fn_get_theme_file( $path_suffix ) {
            $path_prefixes = array_unique( apply_filters( 'czr_include_paths'     , array( '' ) ) );
            $roots         = array_unique( apply_filters( 'czr_include_roots_path', array( CZR_BASE_CHILD, CZR_BASE ) ) );

            foreach ( $roots as $root )
              foreach ( $path_prefixes as $path_prefix )
                if ( file_exists( $filename = $root . $path_prefix . $path_suffix ) )
                  return $filename;

            return false;
        }

        //called when requiring a file url - will always give the precedence to the child-theme file if it exists
        //then to the theme root
        function czr_fn_get_theme_file_url( $url_suffix ) {
            $url_prefixes   = array_unique( apply_filters( 'czr_include_paths'     , array( '' ) ) );
            $roots          = array_unique( apply_filters( 'czr_include_roots_path', array( CZR_BASE_CHILD, CZR_BASE ) ) );
            $roots_urls     = array_unique( apply_filters( 'czr_include_roots_url' , array( CZR_BASE_URL_CHILD, CZR_BASE_URL ) ) );

            $combined_roots = array_combine( $roots, $roots_urls );

            foreach ( $roots as $root )
              foreach ( $url_prefixes as $url_prefix ) {
                if ( file_exists( $filename = $root . $url_prefix . $url_suffix ) )
                  return array_key_exists( $root, $combined_roots) ? $combined_roots[ $root ] . $url_prefix . $url_suffix : false;
              }
            return false;
        }


        //requires a file only if exists
        function czr_fn_require_once( $path_suffix ) {
            if ( false !== $filename = $this -> czr_fn_get_theme_file( $path_suffix ) )
              require_once( $filename );

            return (bool) $filename;
        }


        /*
        * Stores the current model in the class current_model stack
        * called by the View class before requiring the view template
        * @param $model
        */
        function czr_fn_set_current_model( $model ) {
            $this -> current_model[ $model -> id ] = &$model;
        }


        /*
        * Pops the current model from the current_model stack
        * called by the View class after the view template has been required/rendered
        */
        function czr_fn_reset_current_model() {
            array_pop( $this -> current_model );
        }


         /*
        * An handly function to get a current model property
        * @param $property (string), the property to get
        * @param $args (array) - optional, an ordered list of params to pass to the current model property getter (if defined)
        */
        function czr_fn_get( $property, $model_id = null, $args = array() ) {
            $current_model = false;
            if ( ! is_null($model_id) ) {
              if ( czr_fn_is_registered($model_id) )
                $current_model = czr_fn_get_model_instance( $model_id );
            } else {
              $current_model = end( $this -> current_model );
            }
            return is_object($current_model) ? $current_model -> czr_fn_get_property( $property, $args ) : false;
        }

        /*
        * An handly function to print a current model property (wrapper for czr_fn_get)
        * @param $property (string), the property to get
        * @param $args (array) - optional, an ordered list of params to pass to the current model property getter (if defined)
        */
        function czr_fn_echo( $property, $model_id = null, $args = array() ) {
            $prop_value = czr_fn_get( $property, $model_id, $args );
            /*
            * is_array returns false if an array is empty:
            * in that case we have to transform it in false or ''
            */
            $prop_value = $prop_value && is_array( $prop_value ) ? czr_fn_stringify_array( $prop_value ) : $prop_value;
            echo empty( $prop_value ) ? '' : $prop_value;
        }

        /*
        * An handly function to print the content wrapper class
        */
        function czr_fn_column_content_wrapper_class() {
            echo czr_fn_stringify_array( czr_fn_get_column_content_wrapper_class() );
        }

        /*
        * An handly function to print the main container class
        */
        function czr_fn_main_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_main_container_class() );
        }

        /*
        * An handly function to print the article containerr class
        */
        function czr_fn_article_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_article_container_class() );
        }


        /**
        * Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
        * @return boolean
        *
        * @since  Customizr 3.0.11
        */
        function czr_fn_is_child() {
            // get themedata version wp 3.4+
            if ( function_exists( 'wp_get_theme' ) ) {
              //get WP_Theme object of customizr
              $tc_theme       = wp_get_theme();
              //define a boolean if using a child theme
              return $tc_theme -> parent() ? true : false;
            }
            else {
              $tc_theme       = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
              return ! empty($tc_theme['Template']) ? true : false;
            }
        }

  }//end of class
endif;//endif;

//Fire
require_once( get_template_directory() . '/core/functions.php' );

// Fire Customizr
CZR();