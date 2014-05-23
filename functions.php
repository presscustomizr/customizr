<?php
/**
* Customizr functions
* 
* The best way to add your own functions to Customizr is to create a child theme
* http://codex.wordpress.org/Child_Themes
*
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
* General Public License as published by the Free Software Foundation; either version 2 of the License, 
* or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU General Public License along with this program; if not, write 
* to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*
* @package   	Customizr
* @subpackage 	functions
* @since     	3.0
* @author    	Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright 	Copyright (c) 2013, Nicolas GUILLAUME
* @link      	http://themesandco.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


/**
* Singleton factory : on demand class single instanciation
* Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
* 
*
* @since     Customizr 3.0
*/
if ( !function_exists( 'tc__' ) ) :
    function tc__ ( $instance_groups = null, $class = null ) {

       static $instances;
      
       $classname = '';

       $instance_groups = is_array($instance_groups) ? $instance_groups : array($instance_groups);

       //get the class file(s) array by group and name (if defined)
       $parent_files 	= tc_get_classes ( $instance_groups, $path = null ,$class );
       //Do we have to include some child theme classes?
       $child_files 	= tc_is_child() ? tc_get_classes ( $instance_groups, $path = null ,$class, $child=true) : array();
       $files 			= array_merge($parent_files, $child_files);

       foreach ( $files as $f ) 
       {
            //load class files
            locate_template ( $f , true , true );//name, load, require_once

            $classname      = 'TC_'.tc_get_file_class( $f );

            //instanciation
            if( !isset( $instances[ $classname ] ) ) 
            {
                $instances[ $classname ] = class_exists($classname)  ? new $classname : '';
            }
        }//end foreach
    return $instances[ $classname ];
    }
endif;




/**
* Checks if we use a child theme. Uses a deprecated WP functions (get_theme_data) for versions <3.4
* @return boolean
* 
* @since     Customizr 3.0.11
*/
if ( !function_exists( 'tc_is_child' ) ) :
	function tc_is_child() {
		// get themedata version wp 3.4+
		if( function_exists( 'wp_get_theme' ) ) {
		 	//CHECK IF WE ARE USING A CHILD THEME
			//get WP_Theme object of customizr
			$tc_theme       = wp_get_theme();
			//define a boolean if using a child theme
			$is_child       = ( $tc_theme -> parent() ) ? true : false;
		 }
		 else {
		 	$tc_theme 		= get_theme_data( get_stylesheet_directory() . '/style.css' );
		 	$is_child 		= ( !empty($tc_theme['Template']) ) ? true : false;
		}

		return $is_child;
	}
endif;




/**
* Recursive function, takes 4 parameters ( $group is required, $class, $path and $child are optional)
* Scans the theme folder and returns an array of class file names according to their group/name
* 
* @since Customizr 3.0
*/
if ( !function_exists( 'tc_get_classes' ) ) :
	function tc_get_classes( $instance_groups , $path = null , $class = null, $child = null ) {

	    /* TC_BASE is the root server path of parent theme */
	    if ( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

	    if ( ! defined( 'TC_BASE_CHILD' ) )       { define( 'TC_BASE_CHILD' , get_stylesheet_directory().'/' ); }

	    //which folder are we scanning, parent or child?
	    $tc_base      = ($child) ? TC_BASE_CHILD: TC_BASE ;

	    //initializes the class files array
	    $classes = array();

	    //root class instanciation : in this case we don't want to loop through all files
	    if ( in_array('customizr', $instance_groups) ) {
	    	$classes[] = '/inc/class-customizr-__.php';
	    }

	    //all other cases
		else {

		    $files      = scandir($tc_base.$path) ;
		    		
		    foreach ( $files as $file) 
		    {
		        if ( $file[0] != '.' ) 
		        {
		            if ( is_dir($tc_base.$path.$file) ) 
		            {
		                $classes = array_merge( $classes, tc_get_classes( $instance_groups, $path.$file.'/' , $class, $child));
		            }

		            else if ( substr( $file, -4) == '.php' ) 
		            {
		                switch ( $class) 
		                {
		                    //if the class is not defined
		                    case null:
		                       if ( in_array( tc_get_file_group($file), $instance_groups) ) 
		                       {
		                            $classes[] = $path.$file;
		                        }
		                    break;
		                    
		                    default:
		                       if ( tc_get_file_class($file) == $class) 
		                       {
		                            $classes[] = $path.$file;
		                        }
		                    break;
		                }//end switch
		            }//end if
		        } //end if
		    }//end for each
		}//end if

		return $classes;

	}//end of function
endif;





/**
* Returns the class group from the file name
* 
*
* @since Customizr 3.0
*/
if ( !function_exists( 'tc_get_file_group' ) ) :
	function tc_get_file_group( $file) {

	    $group = preg_match_all( '/\-(.*?)\-/' , $file , $match );

	    if ( isset( $match[1][0] ) ) 
	    {
	        return $match[1][0];
	    }
	}
endif;





/**
* Returns the class name from the file name
* 
*
* @since Customizr 3.0
*/
if ( !function_exists( 'tc_get_file_class' ) ) :
	function tc_get_file_class( $file) {
	     //find the name of the class=>after last occurence of '-' and remove .php
	    $pos            = strripos( $haystack = $file , $needle = '-' );
	    //get the part of the string containing the class name
	    $classname      = substr( $file , $pos + 1);
	    //get rid of '.php'
	    $classname      = substr_replace( $classname , '' , -4 , 4);

	    return $classname;
	}
endif;




/**
* Allows WP apply_filter() function to accept up to 4 optional arguments
* 
*
* @since Customizr 3.0
*/
if( !function_exists( 'tc__f' )) :
    function tc__f ( $filter , $arg1 = null , $arg2 = null , $arg3 = null, $arg4 = null) {

       return apply_filters( $filter , $arg1 , $arg2 , $arg3, $arg4 );

    }
endif;



/* Gets the saved options array and make it global */
$tc_saved_options = get_option( 'tc_theme_options');
global $tc_saved_options;

/* Loads the theme classes framework */
tc__( 'customizr' );//fires the theme

/* Starts recording for server execution timeline in dev tools */
tc__f( 'rec' , __FILE__ );



/* 
* The best and safest way to add your own functions to Customizr is to create a child theme
* You can add functions here but it will be lost on upgrade. If you use a child theme, you are safe!
* http://codex.wordpress.org/Child_Themes
*/