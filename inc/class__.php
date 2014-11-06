<?php
/**
* This is where Customizr starts. This file defines and loads the theme's components :
* 1) A global option array
* 2) A function tc__f() used everywhere in the theme and simply adding optional arguments to WP built-in apply_filters()
* 3) Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, TC_WEBSITE
* 4) Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* 5) Text Domain
* 6) Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* 7) Plugins compatibility : jetpack, bbpress, qtranslate, woocommerce and more to come
* 8) Default filtered options for the customizer
* 9) Customizr theme's hooks API : every front end components are rendered with action and filter hooks which makes Customizr entirely and very easily...customizable ;-)
* 
* The php files are loaded with an automatic scan of the theme folder which also instanciates the different classes by group.
* All classes files (except the class__.php file which loads the other) are named with the following convention : class-[instance-group]-[class-name].php
* The instanciations are made with a singleton factory using this method :  TC__::tc__( [instance-group] , [class-name] ).
* 
* For the rest, Customizr is heavily based on a custom filter and action hooks API, extending WordPress built-in API, which makes customization easy as breeze! ;-)
* More on hooks in WordPress here : http://codex.wordpress.org/Plugin_API)
*/



/**
* Allows WP apply_filter() function to accept up to 4 optional arguments
* Used everywhere in Customizr
*
* @since Customizr 3.0
*/
if( !function_exists( 'tc__f' )) :
    function tc__f ( $filter , $arg1 = null , $arg2 = null , $arg3 = null, $arg4 = null) {
       return apply_filters( $filter , $arg1 , $arg2 , $arg3, $arg4 );
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

class TC___ {
    
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    public $tc_core;

    function __construct () {

        self::$instance =& $this;

        $this -> tc_core = apply_filters( 'tc_core',
                        array(
                            'fire'      =>   array(
                                            array('inc' , 'init'),
                                            array('inc' , 'ressources'),
                                            array('inc' , 'utils'),
                                            array('inc' , 'widgets'),
                                            array('inc/admin' , 'admin_init'),
                                        ),
                            
                            'header'    =>   array(
                                            array('parts' , 'header_main'),
                                            array('parts' , 'menu'),
                                            array('parts' , 'nav_walker'),
                                        ),
                            'content'   =>  array(
                                            array('parts', '404'),
                                            array('parts', 'attachment'),
                                            array('parts', 'breadcrumb'),
                                            array('parts', 'comments'),
                                            array('parts', 'featured_pages'),
                                            array('parts', 'gallery'),
                                            array('parts', 'headings'),
                                            array('parts', 'no_results'),
                                            array('parts', 'page'),
                                            array('parts', 'post'),
                                            array('parts', 'post_list'),
                                            array('parts', 'post_metas'),
                                            array('parts', 'post_navigation'),
                                            array('parts', 'sidebar'),
                                            array('parts', 'slider'),
                                        ),
                            'footer'    => array(
                                            array('parts', 'footer_main'),
                                        ),
                            'addons'    => apply_filters('tc_addons_classes' , array() )
                        )//end of array                         
        );//end of filter
        
        /* GET INFORMATIONS FROM STYLE.CSS */
        // get themedata version wp 3.4+
        if( function_exists( 'wp_get_theme' ) )
          {
            //get WP_Theme object of customizr
            $tc_theme                     = wp_get_theme();

            //Get infos from parent theme if using a child theme
            $tc_theme = $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

            $tc_base_data['prefix']       = $tc_base_data['title'] = $tc_theme -> name;
            $tc_base_data['version']      = $tc_theme -> version;
            $tc_base_data['authoruri']    = $tc_theme -> {'Author URI'};
          }

        // get themedata for lower versions (get_stylesheet_directory() points to the current theme root, child or parent)
        else
          {
             $tc_base_data                = get_theme_data( get_stylesheet_directory().'/style.css' );
             $tc_base_data['prefix']      = $tc_base_data['title'];
          }

        /* CUSTOMIZR_VER is the Version */
        if( ! defined( 'CUSTOMIZR_VER' ) )      { define( 'CUSTOMIZR_VER' , $tc_base_data['version'] ); }

        /* TC_BASE is the root server path of the parent theme */
        if( ! defined( 'TC_BASE' ) )            { define( 'TC_BASE' , get_template_directory().'/' ); }

        /* TC_BASE_CHILD is the root server path of the child theme */
        if ( ! defined( 'TC_BASE_CHILD' ) )     { define( 'TC_BASE_CHILD' , get_stylesheet_directory().'/' ); }

        /* TC_BASE_URL http url of the loaded parent theme*/
        if( ! defined( 'TC_BASE_URL' ) )        { define( 'TC_BASE_URL' , get_template_directory_uri() . '/' ); }

        /* TC_BASE_URL_CHILD http url of the loaded child theme*/
        if( ! defined( 'TC_BASE_URL_CHILD' ) )  { define( 'TC_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' ); }

        /* THEMENAME contains the Name of the currently loaded theme */
        if( ! defined( 'THEMENAME' ) )          { define( 'THEMENAME' , $tc_base_data['title'] ); }

        /* TC_WEBSITE is the home website of Customizr */
        if( ! defined( 'TC_WEBSITE' ) )         { define( 'TC_WEBSITE' , $tc_base_data['authoruri'] ); }

        /* theme class groups instanciation */
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

        foreach ( $load as $group => $files ) {
            foreach ($files as $path_suffix ) {
               require_once ( TC_BASE . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
               $classname = 'TC_' . $path_suffix[1];
                if( !isset( $instances[ $classname ] ) ) 
                {
                    $instances[ $classname ] = class_exists($classname)  ? new $classname : '';
                }
            }
        }

        return $instances[ $classname ];
    }



    /**
    * Checks if we use a child theme. Uses a deprecated WP functions (get_theme_data) for versions <3.4
    * @return boolean
    * 
    * @since     Customizr 3.0.11
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
            $tc_theme       = get_theme_data( get_stylesheet_directory() . '/style.css' );
            $is_child       = ( !empty($tc_theme['Template']) ) ? true : false;
        }

        return $is_child;
    }

}//end of class

//Creates a new instance
new TC___;