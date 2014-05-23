<?php
/**
* Fires the theme : some constants definition, core classes loading
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

    function __construct () {

        self::$instance =& $this;

        /* theme class groups definition */
        global $instance_groups;
        $instance_groups = array(
            'fire' ,
            'debug',
            'header' ,
            'content',
            'footer',
            'addons' 
        );

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
        if( ! defined( 'CUSTOMIZR_VER' ) ) { define( 'CUSTOMIZR_VER' , $tc_base_data['version'] ); }

        /* TC_BASE is the root server path of the parent theme */
        if( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

        /* TC_BASE_URL http url of the loaded parent theme*/
        if( ! defined( 'TC_BASE_URL' ) )   { define( 'TC_BASE_URL' , get_template_directory_uri() . '/' ); }

        /* THEMENAME contains the Name of the currently loaded theme */
        if( ! defined( 'THEMENAME' ) )     { define( 'THEMENAME' , $tc_base_data['title'] ); }

        /* TC_WEBSITE is the home website of Customizr */
        if( ! defined( 'TC_WEBSITE' ) )     { define( 'TC_WEBSITE' , $tc_base_data['authoruri'] ); }

        /* theme class groups instanciation */
        tc__ ( $instance_groups );

    }//end of __construct()


}//end of class