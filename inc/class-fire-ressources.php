<?php
/**
* Loads front end stylesheets and scripts
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

class TC_ressources {

    function __construct () {
        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_customizer_styles' ));
        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_scripts' ));
    }



	/**
	 * Registers and enqueues Customizr stylesheets
	 * @package Customizr
	 * @since Customizr 1.1
	 */
	  function tc_customizer_styles() {
	    wp_register_style( 
	      'customizr-skin' , 
	      TC_BASE_URL.'inc/css/'.tc__f ( '__get_option' , 'tc_skin' ), 
	      array(), 
	      CUSTOMIZR_VER, 
	      $media = 'all' 
	      );


	    //enqueue skin
	    wp_enqueue_style( 'customizr-skin' );

	    //enqueue WP style sheet
	    wp_enqueue_style( 
	    	'customizr-style' , 
	    	get_stylesheet_uri() , 
	    	array( 'customizr-skin' ) , 
	    	CUSTOMIZR_VER , 
	    	$media = 'all'  
	    );

	}




	/**
	 * Loads Customizr and JS script in footer for better time load.
	 * 
	 * @uses wp_enqueue_script() to manage script dependencies
	 * @package Customizr
	 * @since Customizr 1.0
	 */
	  function tc_scripts() {
	      wp_enqueue_script( 'jquery' );

	      wp_enqueue_script( 'jquery-ui-core' );

	      wp_enqueue_script( 'bootstrap' ,TC_BASE_URL . 'inc/js/bootstrap.min.js' ,array( 'jquery' ),null, $in_footer = true);
	     
	      //tc scripts
	      wp_enqueue_script( 'tc-scripts' ,TC_BASE_URL . 'inc/js/tc-scripts.js' ,array( 'jquery' ),null, $in_footer = true);

	      //holder image
	      wp_enqueue_script( 'holder' ,TC_BASE_URL . 'inc/js/holder.js' ,array( 'jquery' ),null, $in_footer = true);

	      //modernizr (must be loaded in wp_head())
	      wp_enqueue_script( 'modernizr' ,TC_BASE_URL . 'inc/js/modernizr.js' ,array( 'jquery' ),null, $in_footer = false);

	      //fancybox script and style
	      $tc_fancybox = tc__f ( '__get_option' , 'tc_fancybox' );
	      if ( $tc_fancybox == 1) {
	        wp_enqueue_script( 'fancyboxjs' ,TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.js' ,array( 'jquery' ),null, $in_footer = true);
	        wp_enqueue_script( 'activate-fancybox' ,TC_BASE_URL . 'inc/js/tc-fancybox.js' ,array( 'fancyboxjs' ),null, $in_footer = true);
	        wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.css' );
	      }
	   }
}//end of TC_ressources