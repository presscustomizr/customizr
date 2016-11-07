<?php
/**
* Header actions
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
if ( ! class_exists( 'TC_favicon' ) ) :
	class TC_favicon extends TC_view_base {
    static $instance;
    function __construct( $_args = array() ) {
      self::$instance =& $this;
      // Instanciates the parent class.
      if ( ! isset(parent::$instance) )
        parent::__construct( $_args );
    }



		/**
    * Render favicon from options
    * Since WP 4.3 : let WP do the job if user has set the WP site_icon setting.
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_render() {
      $_fav_option  			= esc_attr( TC_utils::$inst->tc_opt( 'tc_fav_upload') );
     	if ( ! $_fav_option || is_null($_fav_option) )
     		return;

     	$_fav_src 				= '';
     	//check if option is an attachement id or a path (for backward compatibility)
     	if ( is_numeric($_fav_option) ) {
     		$_attachement_id 	= $_fav_option;
     		$_attachment_data 	= apply_filters( 'tc_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'full' ) );
     		$_fav_src 			= $_attachment_data[0];
     	} else { //old treatment
     		$_saved_path 		= esc_url ( TC_utils::$inst->tc_opt( 'tc_fav_upload') );
     		//rebuild the path : check if the full path is already saved in DB. If not, then rebuild it.
       	$upload_dir 		= wp_upload_dir();
       	$_fav_src 			= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
     	}

     	//makes ssl compliant url
     	$_fav_src 				= apply_filters( 'tc_fav_src' , is_ssl() ? str_replace('http://', 'https://', $_fav_src) : $_fav_src );

      if( null == $_fav_src || !$_fav_src )
      	return;

      	$type = "image/x-icon";
      	if ( strpos( $_fav_src, '.png') ) $type = "image/png";
      	if ( strpos( $_fav_src, '.gif') ) $type = "image/gif";

    	echo apply_filters( 'tc_favicon_display',
      		sprintf('<link id="czr-favicon" rel="shortcut icon" href="%1$s" type="%2$s">' ,
      			$_fav_src,
      			$type
      		)
    	);
    }

	}//end of class
endif;