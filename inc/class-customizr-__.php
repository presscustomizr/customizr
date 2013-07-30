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
    
    function __construct() {

        /* GET INFORMATIONS FROM STYLE.CSS */
        // get themedata version wp 3.4+
        if(function_exists( 'wp_get_theme' ))
          {
            $tc_theme                     = wp_get_theme();
            $tc_base_data['prefix']       = $tc_base_data['Title'] = $tc_theme->get( 'Name' );
            $tc_base_data['Version']      = $tc_theme->get( 'Version' );
          }
          // get themedata lower versions
        else
          {
             $tc_base_data                = get_theme_data( TC_BASE . 'style.css' );
             $tc_base_data['prefix']      = $tc_base_data['Title'];
          }
        /* CUSTOMIZR_VER is the Version */
        if( ! defined( 'CUSTOMIZR_VER' ) ) { define( 'CUSTOMIZR_VER' , $tc_base_data['Version'] ); }

         /* TC_BASE is the root server path */
        if( ! defined( 'TC_BASE' ) )       { define( 'TC_BASE' , get_template_directory().'/' ); }

      /* TC_BASE_URL http url of the loaded template */
        if( ! defined( 'TC_BASE_URL' ) )   { define( 'TC_BASE_URL' , get_template_directory_uri() . '/' ); }

      /* THEMENAME contains the Name of the currently loaded theme */
        if( ! defined( 'THEMENAME' ) )     { define( 'THEMENAME' , $tc_base_data['Title'] ); }


        /* theme class groups instanciation */
        $groups = array(
            'fire' ,
            'main' ,
            'header' ,
            'content' 
        );

        foreach ( $groups as $g) 
        {
             tc__ ( $g);
        }

    }//end of __construct()


}//end of class





