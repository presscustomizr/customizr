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
* Singleton factory : on demand classes instanciation
* Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
* 
*
* @since     Customizr 3.0
*/
if ( !function_exists( 'tc__' ) ) :
    function tc__ ( $group, $class = null ) {

        static $instances;

        //get the class file(s) array by group and name (if defined)
        $files = tc_get_classes ( $group, $path = null ,$class);

       foreach ( $files as $f ) 
       {
            //load class files
            locate_template ( $f , true , true );//name, load, require_once

            $classname      = 'TC_'.tc_get_file_class( $f );

            //instanciation
            if( !isset( $instances[ $classname ] ) ) 
            {
                $instances[ $classname ] = new $classname;
            }
        }//end foreach

    return $instances[ $classname ];

    }
endif;





/**
* Recursive function, takes 2 parameters ( $group is required, $class is optional)
* Scans the theme folder and returns an array of class file names according to their group/name
* 
* @since Customizr 3.0
*/
if ( !function_exists( 'tc_get_classes' ) ) :
	function tc_get_classes( $group , $path = null , $class = null ) {

	     /* TC_BASE is the root server path */
	    if ( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

	    $classes    = array();

	    $files      = scandir(TC_BASE.$path);
	    
	    foreach ( $files as $file) 
	    {
	        if ( $file[0] != '.' ) 
	        {
	            if ( is_dir(TC_BASE.$path.$file) ) 
	            {
	                $classes = array_merge( $classes, tc_get_classes( $group, $path.$file.'/' , $class));
	            }

	            else if ( substr( $file, -4) == '.php' ) 
	            {
	                switch ( $class) 
	                {
	                    //if the class is not defined
	                    case null:
	                       if ( tc_get_file_group( $file) == $group) 
	                       {
	                            $classes[] = $path.$file;
	                        }
	                    break;
	                    
	                    default:
	                       if ( tc_get_file_class( $file) == $class) 
	                       {
	                            $classes[] = $path.$file;
	                        }
	                    break;
	                }//end switch
	            }//end if
	        } //end if
	    }//end for each

	    return $classes;
	}
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
* Allows WP apply_filter() function to accept up to 3 optional arguments
* 
*
* @since Customizr 3.0
*/
if( !function_exists( 'tc__f' )) :
    function tc__f ( $filter , $arg1 = null , $arg2 = null , $arg3 = null) {

       return apply_filters( $filter , $arg1 , $arg2 , $arg3 );

    }
endif;




/* Loads the theme classes framework */
locate_template( 'inc/class-customizr-__.php' ,true,true);
tc__( 'customizr' );//fire the theme
