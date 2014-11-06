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
	        
	        //Custom CSS based on options
	        add_action ( 'wp_head'                 					, array( $this , 'tc_write_custom_css' ), apply_filters( 'tc_custom_css_priority', 20 ) );
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

		    //tc-scripts.js includes :
		    //1) Twitter Bootstrap scripts
		    //2) Holder.js
		    //3) FancyBox - jQuery Plugin
		    //4) Retina.js
		    //5) Customizr scripts
		    wp_enqueue_script( 'tc-scripts' ,TC_BASE_URL . 'inc/assets/js/tc-scripts.min.js' ,array( 'jquery' ),null, $in_footer = apply_filters('tc_load_script_in_footer' , false) );

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
		        'tc-scripts', 
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
			          	'LoadBootstrap' 		=> apply_filters( 'tc_load_bootstrap' , true ),
			          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
			          	'LoadRetina' 			=> ( 1 == tc__f( '__get_option' , 'tc_retina_support' ) ) ? true : false,
			          	'LoadCustomizrScript' 	=> apply_filters( 'tc_load_customizr_script' , true )
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
		    	wp_enqueue_script( 'holder' ,TC_BASE_URL . 'inc/assets/js/holder.min.js' ,array( 'jquery' ),null, $in_footer = true);
		    }
		}




	    /**
	    * Get the sanitized custom CSS from options array : fonts, custom css, and echoes the stylesheet
	    * 
	    * @package Customizr
	    * @since Customizr 2.0.7
	    */
	    function tc_write_custom_css() {
	        $tc_custom_css      	= esc_html( tc__f( '__get_option' , 'tc_custom_css') );
	        $tc_top_border      	= esc_attr( tc__f( '__get_option' , 'tc_top_border') );
	        if ( isset($tc_custom_css) && ! empty($tc_custom_css) )
	        	printf( '<style id="option-custom-css" type="text/css">%1$s</style>',
	        		html_entity_decode($tc_custom_css)
	        	);
	        if ( ( isset($tc_top_border) && 0 == $tc_top_border) )
	        	echo '<style id="option-top-border" type="text/css">header.tc-header {border-top: none;}</style>';
	    }//end of function
	}//end of TC_ressources
endif;