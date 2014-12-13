<?php
/**
* The tc__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
*
* By convention in Customizr, filter hooks are used as follow :
* 1) declared with add_filters in class constructors (mainly) to hook on WP built-in callbacks or create "getters" used everywhere
* 2) declared with apply_filters in methods to make the code extensible for developers
* 3) accessed with tc__f() to return values (while front end content is handled with action hooks)
*
* Used everywhere in Customizr. Can pass up to five variables to the filter callback.
*
* @since Customizr 3.0
*/
if( !function_exists( 'tc__f' )) :
    function tc__f ( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;



/**
* Fires the theme : constants definition, core classes loading
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
if ( ! class_exists( 'TC___' ) ) :
    class TC___ {
        //Access any method or var of the class with classname::$instance -> var or method():
        static $instance;
        public $tc_core;
        public $is_customizing;
        public static $theme_name;
        public static $tc_option_group;
        function __construct () {
            self::$instance =& $this;

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
            if( ! defined( 'CUSTOMIZR_VER' ) )      define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
            //TC_BASE is the root server path of the parent theme
            if( ! defined( 'TC_BASE' ) )            define( 'TC_BASE' , get_template_directory().'/' );
            //TC_BASE_CHILD is the root server path of the child theme
            if( ! defined( 'TC_BASE_CHILD' ) )      define( 'TC_BASE_CHILD' , get_stylesheet_directory().'/' );
            //TC_BASE_URL http url of the loaded parent theme
            if( ! defined( 'TC_BASE_URL' ) )        define( 'TC_BASE_URL' , get_template_directory_uri() . '/' );
            //TC_BASE_URL_CHILD http url of the loaded child theme
            if( ! defined( 'TC_BASE_URL_CHILD' ) )  define( 'TC_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' );
            //THEMENAME contains the Name of the currently loaded theme
            if( ! defined( 'THEMENAME' ) )          define( 'THEMENAME' , $tc_base_data['title'] );
            //TC_WEBSITE is the home website of Customizr
            if( ! defined( 'TC_WEBSITE' ) )         define( 'TC_WEBSITE' , $tc_base_data['authoruri'] );


            //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
            $this -> tc_core = apply_filters( 'tc_core',
                array(
                    'fire'      =>   array(
                                    array('inc' , 'init'),//defines default values (layout, socials, default slider...) and theme supports (after_setup_theme)
                                    array('inc' , 'utils_settings_map'),//customizer setting map
                                    array('inc' , 'utils'),//helpers used everywhere
                                    array('inc' , 'resources'),//loads style (skins) and scripts
                                    array('inc' , 'widgets'),//widget factory
                                    array('inc/admin' , 'admin_init'),//fires the customizer and the metaboxes for slider and layout options
                                    array('inc/admin' , 'admin_page'),//creates the welcome/help panel including changelog and system config
                                ),
                    //the following files/classes define the action hooks for front end rendering : header, main content, footer
                    'header'    =>   array(
                                    array('inc/parts' , 'header_main'),
                                    array('inc/parts' , 'menu'),
                                    array('inc/parts' , 'nav_walker'),
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
                                    array('inc/parts', 'post_metas'),
                                    array('inc/parts', 'post_navigation'),
                                    array('inc/parts', 'sidebar'),
                                    array('inc/parts', 'slider'),
                                ),
                    'footer'    => array(
                                    array('inc/parts', 'footer_main'),
                                ),
                    'addons'    => apply_filters( 'tc_addons_classes' , array() )
                )//end of array
            );//end of filters

            //check the context
            if ( file_exists( sprintf( '%sinc/init-pro.php' , TC_BASE ) ) && 'customizr-pro' == self::$theme_name ) {
              require_once( sprintf( '%sinc/init-pro.php' , TC_BASE ) );
              self::$tc_option_group = 'tc_theme_options';
            } else {
              self::$tc_option_group = 'tc_theme_options';
            }

            //theme class groups instanciation
            $this -> tc__ ( $this -> tc_core );
        }//end of __construct()


        /**
        * Class instanciation with a singleton factory :
        * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
        *
        *
        * @since Customizr 3.0
        */
        function tc__ ( $load ) {
            static $instances;

            //checks if is customizing : two context, admin and front (preview frame)
            $this -> is_customizing = $this -> tc_is_customizing();

            foreach ( $load as $group => $files ) {
                foreach ($files as $path_suffix ) {

                    //don't load admin classes if not admin && not customizing
                    if ( is_admin() && ! $this -> is_customizing ) {
                        if ( false !== strpos($path_suffix[0], 'parts') )
                            continue;
                    }
                    if ( ! is_admin() && ! $this -> is_customizing ) {
                        if ( false !== strpos($path_suffix[0], 'admin') )
                            continue;
                    }

                    //checks if a child theme is used and if the required file has to be overriden
                    if ( $this -> tc_is_child() && file_exists( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ) {
                        require_once ( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
                    }
                    else {
                        require_once ( TC_BASE . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
                    }

                    $classname = 'TC_' . $path_suffix[1];
                    if( ! isset( $instances[ $classname ] ) )
                    {
                        $instances[ $classname ] = class_exists($classname)  ? new $classname : '';
                    }
                }
            }
            return $instances[ $classname ];
        }



        /**
        * Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
        * @return boolean
        *
        * @since  Customizr 3.0.11
        */
        function tc_is_child() {
            // get themedata version wp 3.4+
            if( function_exists( 'wp_get_theme' ) ) {
                //get WP_Theme object of customizr
                $tc_theme       = wp_get_theme();
                //define a boolean if using a child theme
                $is_child       = ( $tc_theme -> parent() ) ? true : false;
             }
             else {
                $tc_theme       = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
                $is_child       = ( ! empty($tc_theme['Template']) ) ? true : false;
            }
            return $is_child;
        }


        /**
        * Returns a boolean on the customizer's state
        * @since  3.2.9
        */
        function tc_is_customizing() {
          //checks if is customizing : two contexts, admin and front (preview frame)
          global $pagenow;
          $_is_customizing = false;
          if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow ) {
            $_is_customizing = true;
          } else if ( ! is_admin() && isset($_REQUEST['wp_customize']) ) {
            $_is_customizing = true;
          }
          return $_is_customizing;
        }
    }//end of class
endif;

//Creates a new instance
new TC___;