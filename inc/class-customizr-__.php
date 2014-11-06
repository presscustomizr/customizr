<?php

/**
 * fire theme
 * 
 *
 * @package Customizr
 * @since Customizr 3.0
 */
class TC___ {
    
    function __construct() {

        /* GET INFORMATIONS FROM STYLE.CSS */
        // get themedata version wp 3.4+
        if(function_exists('wp_get_theme'))
          {
            $tc_theme                     = wp_get_theme();
            $tc_base_data['prefix']       = $tc_base_data['Title'] = $tc_theme->get('Name');
            $tc_base_data['Version']      = $tc_theme->get('Version');
          }
          // get themedata lower versions
        else
          {
             $tc_base_data                = get_theme_data( TC_BASE . 'style.css' );
             $tc_base_data['prefix']      = $tc_base_data['Title'];
          }
        /* CUSTOMIZR_VER is the Version */
        if( ! defined('CUSTOMIZR_VER' ) ) { define( 'CUSTOMIZR_VER', $tc_base_data['Version'] ); }

         /* TC_BASE is the root server path */
        if( ! defined('TC_BASE' ) )       { define( 'TC_BASE', get_template_directory().'/' ); }

      /* TC_BASE_URL http url of the loaded template */
        if( ! defined('TC_BASE_URL' ) )   { define( 'TC_BASE_URL', get_template_directory_uri() . '/'); }

      /* THEMENAME contains the Name of the currently loaded theme */
        if( ! defined('THEMENAME' ) )     { define( 'THEMENAME', $tc_base_data['Title'] ); }


        /* theme class groups instanciation */
        $groups = array(
            'fire',
            'main',
            'header',
            'content'
        );
        foreach ($groups as $g) {
             tc__ ($g);
        }

    }//end of __construct()


}//end of class




/**
* Singleton factory : on demand class instanciation
* Thanks to Ben Doherty!
* 
*
* @package Customizr
* @since Customizr 3.0
*/
if(!function_exists('tc__')) :
    function tc__ ( $group, $class = null) {

        static $instances;

        $files = tc_get_classes ($group, $path = null ,$class);

       foreach ($files as $f) {
            //load class files
            locate_template($f,true,true);//name, load, require_once

            $classname      = 'TC_'.tc_get_file_class($f);

            //instanciation
            if( !isset( $instances[ $classname] ) ) {
                $instances[$classname] = new $classname;
            }
        }//end for each
            
    return $instances[$classname];

    }

endif;





/**
* Recursive function, takes 2 parameters ($group is required, $class is optional)
* Scans the theme folder and returns an array of class file names according to their group/name
* 
*
* @package Customizr
* @since Customizr 3.0
*/
function tc_get_classes($group,$path = null,$class = null) {

     /* TC_BASE is the root server path */
    if( ! defined('TC_BASE' ) )       { define( 'TC_BASE', get_template_directory().'/' ); }

    $classes    = array();

    $files      =  scandir(TC_BASE.$path);
    
    foreach ($files as $file) {
        if ($file[0] != '.') {
            if (is_dir(TC_BASE.$path.$file)) {
                $classes = array_merge($classes, tc_get_classes($group, $path.$file.'/', $class));
            }
            else if (substr($file, -4) == '.php') {
                switch ($class) {
                    //if the class is not defined
                    case null:
                       if( tc_get_file_group($file) == $group) {
                            $classes[] = $path.$file;
                        }
                    break;
                    
                    default:
                       if( tc_get_file_class($file) == $class) {
                            $classes[] = $path.$file;
                        }
                    break;
                }
                
            } 
        }
    }//end for each
    return $classes;
}





/**
* Returns the class group from the file name
* 
*
* @package Customizr
* @since Customizr 3.0
*/
function tc_get_file_group($file) {
    $group = preg_match_all('/\-(.*?)\-/',$file,$match);
    if (isset($match[1][0])) {
        return $match[1][0];
    }
}





/**
* Returns the class name from the file name
* 
*
* @package Customizr
* @since Customizr 3.0
*/
function tc_get_file_class($file) {
     //find the name of the class=>after last occurence of '-' and remove .php
    $pos            = strripos($haystack = $file, $needle = '-');
    //get the part of the string containing the class name
    $classname      = substr($file, $pos + 1);
    //get rid of '.php'
    $classname      = substr_replace($classname, '', -4, 4);

    return $classname;
}





/**
* Allows apply_filter to accept up to 3 optional arguments
* 
*
* @package Customizr
* @since Customizr 3.0
*/
if(!function_exists('tc__f')) :
    function tc__f ($filter,$arg1 = null, $arg2 = null, $arg3 = null) {
       return apply_filters( $filter, $arg1, $arg2, $arg3 );
    }
endif;
