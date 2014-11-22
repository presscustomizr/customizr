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
if ( ! class_exists( 'TC_resources' ) ) :
	class TC_resources {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
	    function __construct () {
	        self::$instance =& $this;
	        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_styles' ) );
	        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_scripts' ) );
	        //Write font icon
	        add_action ( 'wp_head'                 					, array( $this , 'tc_write_inline_font_icons_css' ), apply_filters( 'tc_font_icon_priority', 0 ) );
	        //Custom CSS
	        add_action ( 'wp_head'                 					, array( $this , 'tc_write_custom_css' ), apply_filters( 'tc_custom_css_priority', 20 ) );
	        
	        //Grunt Live reload script on DEV mode (TC_DEV constant has to be defined. In wp_config for example)
	        if ( defined('TC_DEV') && true === TC_DEV && apply_filters('tc_live_reload_in_dev_mode' , true ) )
	        	add_action( 'wp_head' , array( $this , 'tc_add_livereload_script' ) );
	    }



	    /**
		* Registers and enqueues Customizr stylesheets
		* @package Customizr
		* @since Customizr 1.1
		*/
		function tc_enqueue_customizr_styles() {
		    //Customizr active skin
		    wp_register_style( 'customizr-skin', TC_init::$instance -> tc_active_skin(), array(), CUSTOMIZR_VER, 'all' );
		    wp_enqueue_style( 'customizr-skin' );
		    //Customizr stylesheet (style.css)
		    wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), CUSTOMIZR_VER , 'all' );
		    //Customizer user defined style options
		    wp_add_inline_style( 'customizr-skin', apply_filters( 'tc_user_options_style' , '' ) );
		}



		/**
		* Loads Customizr and JS script in footer for better time load.
		* 
		* @uses wp_enqueue_script() to manage script dependencies
		* @package Customizr
		* @since Customizr 1.0
		*/
		function tc_enqueue_customizr_scripts() {
		    //wp scripts
		  	if ( is_singular() && get_option( 'thread_comments' ) )
			    wp_enqueue_script( 'comment-reply' );
		    wp_enqueue_script( 'jquery' );
		    wp_enqueue_script( 'jquery-ui-core' );
		    //load modernizr.js in footer
		    wp_enqueue_script( 'modernizr' , TC_BASE_URL . 'inc/assets/js/modernizr.min.js', array(), CUSTOMIZR_VER, $in_footer = true);
		    
		   	if ( apply_filters('tc_load_concatenated_front_scripts' , true ) )
		   	{
			    //tc-scripts.min.js includes :
			    //1) Twitter Bootstrap scripts
			    //2) FancyBox - jQuery Plugin
			    //3) Customizr scripts
			    wp_enqueue_script( 
			    	'tc-scripts' , 
			    	sprintf( '%1$sinc/assets/js/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'tc-scripts.js' : 'tc-scripts.min.js' ),
			    	array( 'jquery' ), 
			    	CUSTOMIZR_VER, 
			    	$in_footer = apply_filters('tc_load_script_in_footer' , false) 
			    );
			}
			else
			{
				//in production script are minified 
		    	wp_enqueue_script(
			    	'params-dev-mode', 
			    	sprintf( '%1$sinc/assets/js/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'params-dev-mode.js' : 'params-dev-mode.min.js'),
			    	array( 'jquery' ), 
			    	CUSTOMIZR_VER, 
			    	$in_footer = apply_filters('tc_load_script_in_footer' , false)
		    	);
		    	wp_enqueue_script(
			    	'dev-bootstrap', 
			    	sprintf( '%1$sinc/assets/js/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'bootstrap.js' : 'bootstrap.min.js'),
			    	array( 'params-dev-mode' ), 
			    	CUSTOMIZR_VER, 
			    	$in_footer = apply_filters('tc_load_script_in_footer' , false)
		    	);
		    	wp_enqueue_script(
			    	'dev-fancybox', 
			    	sprintf( '%1$sinc/assets/js/fancybox/%2$s' , TC_BASE_URL , 'jquery.fancybox-1.3.4.min.js' ),
			    	array( 'params-dev-mode' ), 
			    	CUSTOMIZR_VER, 
			    	$in_footer = apply_filters('tc_load_script_in_footer' , false)
		    	);
			}//end of load concatenate script if

		    //fancybox options
			$tc_fancybox 		= ( 1 == tc__f( '__get_option' , 'tc_fancybox' ) ) ? true : false;
			$autoscale 			= ( 1 == tc__f( '__get_option' , 'tc_fancybox_autoscale') ) ? true : false ;

	        //carousel options
	        //gets slider options if any for home/front page or for others posts/pages
		    $js_slidername      = tc__f('__is_home') ? tc__f( '__get_option' , 'tc_front_slider' ) : get_post_meta( tc__f('__ID') , $key = 'post_slider_key' , $single = true );
		    $js_sliderdelay     = tc__f('__is_home') ? tc__f( '__get_option' , 'tc_slider_delay' ) : get_post_meta( tc__f('__ID') , $key = 'slider_delay_key' , $single = true );
		      
			//has the post comments ? adds a boolean parameter in js
			global $wp_query;
			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
			$smooth_scroll 		= ( false != esc_attr( tc__f( '__get_option' , 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
			if ( false != esc_attr( tc__f( '__get_option' , 'tc_link_scroll') ) )
				wp_enqueue_script('jquery-effects-core');

			//gets current screen layout
        	$screen_layout      = tc__f( '__screen_layout' , tc__f ( '__ID' ) , 'sidebar'  );
        	//gets the global layout settings
        	$global_layout      = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
        	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
			//Gets the left and right sidebars class for js actions
			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
	      	$right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

			wp_localize_script( 
		        (defined('WP_DEBUG') && true === WP_DEBUG ) ? 'params-dev-mode' : 'tc-scripts',
		        'TCParams',
		        apply_filters('tc_customizr_script_params' , array(
			          	'FancyBoxState' 		=> $tc_fancybox,
			          	'FancyBoxAutoscale' 	=> $autoscale,
			          	'SliderName' 			=> $js_slidername,
			          	'SliderDelay' 			=> $js_sliderdelay,
			          	'SliderHover'			=> apply_filters( 'tc_stop_slider_hover', true ),
			          	'SmoothScroll'			=> $smooth_scroll,
			          	'ReorderBlocks' 		=> esc_attr( tc__f( '__get_option' , 'tc_block_reorder') ),
			          	'CenterSlides' 			=> esc_attr( tc__f( '__get_option' , 'tc_center_slides') ),
			          	'HasComments' 			=> $has_post_comments,
			          	'LeftSidebarClass' 		=> $left_sb_class,
			          	'RightSidebarClass' 	=> $right_sb_class,
			          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
			          	'stickyCustomOffset' 	=> apply_filters( 'tc_sticky_custom_offset' , 0 ),
			          	'stickyHeader' 			=> esc_attr( tc__f( '__get_option' , 'tc_sticky_header' ) ),
			          	'dropdowntoViewport' 	=> esc_attr( tc__f( '__get_option' , 'tc_menu_resp_dropdown_limit_to_viewport') ),
			          	'timerOnScrollAllBrowsers' => apply_filters('tc_timer_on_scroll_for_all_browser' , true) //<= if false, for ie only
		        	),
		        	tc__f('__ID')
		       	)//end of filter
	        );

		    //fancybox style
		    if ( $tc_fancybox )
		      	wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/assets/js/fancybox/jquery.fancybox-1.3.4.min.css' );

		    //holder.js is loaded when featured pages are enabled AND FP are set to show images
		    $tc_show_featured_pages 	    = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages' ) );
      		$tc_show_featured_pages_img     = esc_attr( tc__f( '__get_option' , 'tc_show_featured_pages_img' ) );
      		if ( 0 != $tc_show_featured_pages && 0 != $tc_show_featured_pages_img ) {
		    	wp_enqueue_script( 
		    		'holder',
		    		sprintf( '%1$sinc/assets/js/holder.min.js' , TC_BASE_URL ),
		    		array(),
		    		CUSTOMIZR_VER,
		    		$in_footer = true
		    	);
		    }

		    //load retina.js in footer if enabled
		    if ( apply_filters('tc_load_retinajs', 1 == tc__f( '__get_option' , 'tc_retina_support' ) ) )
		    	wp_enqueue_script( 'retinajs' ,TC_BASE_URL . 'inc/assets/js/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

		    //Load hammer.js for mobile
		    if ( apply_filters('tc_load_hammerjs', wp_is_mobile() ) )
		    	wp_enqueue_script( 'hammer' ,TC_BASE_URL . 'inc/assets/js/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

		}



		/**
	    * Write the font icon in head
	    * 
	    * @package Customizr
	    * @since Customizr 3.2.3
	    */
		function tc_write_inline_font_icons_css() {
			$_path = apply_filters( 'tc_font_icons_path' , TC_BASE_URL . 'inc/assets/css' );
			echo apply_filters(
				'tc_inline_font_icons' ,
				sprintf('<style type="text/css" id="customizr-inline-fonts">%1$s</style>',
					"@font-face{font-family:genericons;src:url('{$_path}/fonts/fonts/genericons-regular-webfont.eot');src:url('{$_path}/fonts/fonts/genericons-regular-webfont.eot?#iefix') format('embedded-opentype'),url('{$_path}/fonts/fonts/genericons-regular-webfont.woff') format('woff'),url('{$_path}/fonts/fonts/genericons-regular-webfont.ttf') format('truetype'),url('{$_path}/fonts/fonts/genericons-regular-webfont.svg#genericonsregular') format('svg')}@font-face{font-family:entypo;src:url('{$_path}/fonts/fonts/entypo.eot);src:url({$_path}/fonts/fonts/entypo.eot?#iefix') format('embedded-opentype'),url('{$_path}/fonts/fonts/entypo.woff') format('woff'),url('{$_path}/fonts/fonts/entypo.ttf') format('truetype'),url('{$_path}/fonts/fonts/entypo.svg#genericonsregular') format('svg')}"
				)
			);
		}



	    /**
	    * Get the sanitized custom CSS from options array : fonts, custom css, and echoes the stylesheet
	    * 
	    * @package Customizr
	    * @since Customizr 2.0.7
	    */
	    function tc_write_custom_css() {
	        $tc_custom_css      	= esc_html( tc__f( '__get_option' , 'tc_custom_css') );
	        if ( isset($tc_custom_css) && ! empty($tc_custom_css) )
	        	printf( '<style id="option-custom-css" type="text/css">%1$s</style>',
	        		html_entity_decode($tc_custom_css)
	        	);
	    }//end of function




		/*
		* Writes the livereload script in dev mode (if Grunt watch livereload is enabled)
		*@since v3.2.4
		*/
		function tc_add_livereload_script() {
			if ( TC_utils::$instance -> tc_is_customizing() )
				return;
			?>
			<script id="tc-dev-live-reload" type="text/javascript">
			    document.write('<script src="http://'
			        + ('localhost').split(':')[0]
			        + ':35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
			    console.log('When WP_DEBUG mode is enabled, activate the watch Grunt task to enable live reloading. This script can be disabled with the following code to paste in your functions.php file : add_filter("tc_live_reload_in_dev_mode" , "__return_false")');
			</script>
			<?php
		}
	}//end of TC_ressources
endif;