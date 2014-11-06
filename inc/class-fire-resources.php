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
	        //Customizer user defined style options
	        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_customizer_user_options_style' ) );
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
		    //load modernizr.js in footer
		    wp_enqueue_script( 'modernizr' , TC_BASE_URL . 'inc/assets/js/modernizr.min.js', array(), CUSTOMIZR_VER, $in_footer = true);
		    //tc-scripts.js includes :
		    //1) Twitter Bootstrap scripts
		    //2) Holder.js
		    //3) FancyBox - jQuery Plugin
		    //4) Customizr scripts (loaded unminified on DEBUG)
		    wp_enqueue_script( 
		    	'tc-scripts' , 
		    	sprintf( '%1$sinc/assets/js/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'tc-scripts.js' : 'tc-scripts.min.js' ),
		    	array( 'jquery' ), 
		    	CUSTOMIZR_VER, 
		    	$in_footer = apply_filters('tc_load_script_in_footer' , false) 
		    );

		    //Load Bootstrap separetely and not minified on DEBUG mode
		    $_load_bootstrap 	= apply_filters( 'tc_load_bootstrap' , true );
		    if ( defined('WP_DEBUG') && true === WP_DEBUG ) {
		    	$_load_bootstrap = false;
		    	wp_enqueue_script( 
			    	'tc-bootstrap',
			    	sprintf( '%1$sinc/assets/js/bootstrap.js' , TC_BASE_URL ),
			    	array( 'jquery' ),
			    	CUSTOMIZR_VER,
			    	false
			    );
		    }


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
			          	'LoadBootstrap' 		=> $_load_bootstrap,
			          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
			          	'LoadCustomizrScript' 	=> apply_filters( 'tc_load_customizr_script' , true ),
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
		    	wp_enqueue_script( 'holder' ,TC_BASE_URL . 'inc/assets/js/holder.min.js' ,array( 'jquery' ), CUSTOMIZR_VER, $in_footer = true);
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





	    /**
	    * Style from customizer options
	    * 
	    * @package Customizr
	    * @since Customizr 3.2.0
	    */
	    function tc_customizer_user_options_style() {
		  	$_css = '';
	        //TOP BORDER
	        if ( 1 != esc_attr( tc__f( '__get_option' , 'tc_top_border') ) )
	       		$_css .= "
	       			header.tc-header {border-top: none;}
	       		";

	       	//THUMBNAIL SETTINGS
		  	$_list_thumb_height 	= esc_attr( tc__f( '__get_option' , 'tc_post_list_thumb_height' ) );
		  	$_list_thumb_height 	= (! $_list_thumb_height || ! is_numeric($_list_thumb_height) ) ? 250 : $_list_thumb_height;

		  	$_single_thumb_height 	= esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_height' ) );
		  	$_single_thumb_height 	= (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
		  	$_css .= "
		          .tc-rectangular-thumb {
		            max-height: {$_list_thumb_height}px;
		            height :{$_list_thumb_height}px
		          }
		          .single .tc-rectangular-thumb {
		            max-height: {$_single_thumb_height}px;
		            height :{$_single_thumb_height}px
		          }";

		    //STICKY HEADER
		    if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_shrink_title_logo') ) || TC_utils::$instance -> tc_is_customizing() ) {
		    	$_logo_shrink 	= implode (';' , apply_filters('tc_logo_shrink_css' , array("height:30px!important","width:auto!important") )	);

		    	$_title_font 	= implode (';' , apply_filters('tc_title_shrink_css' , array("font-size:0.6em","opacity:0.8","line-height:1.2em") ) );

			    $_css .= "
			    		.sticky-enabled .tc-shrink-on .site-logo img {
							{$_logo_shrink}
						}
						.sticky-enabled .tc-shrink-on .brand .site-title {
							{$_title_font}
						}";
			}

			//CUSTOM SLIDER HEIGHT
			// 1) Do we have a custom height ?
			// 2) check if the setting must be applied to all context
			$_custom_height = esc_attr( tc__f( '__get_option' , 'tc_slider_default_height') );
			if ( 500 != $_custom_height
				&& ( tc__f('__is_home')
						|| 0 != esc_attr( tc__f( '__get_option' , 'tc_slider_default_height_apply_all') )
				) ) {
				$_resp_shrink_ratios = apply_filters( 'tc_slider_resp_shrink_ratios',
					array('1200' => 0.77 , '979' => 0.618, '480' => 0.38 , '320' => 0.28 )
				);

				$_css .= "
					.carousel .item {
						line-height: {$_custom_height}px;
						min-height:{$_custom_height}px;
						max-height:{$_custom_height}px;
					}
					.tc-slider-loader-wrapper {
						line-height: {$_custom_height}px;
						height:{$_custom_height}px;
					}
					.carousel .tc-slider-controls {
						line-height: {$_custom_height}px;
						max-height:{$_custom_height}px;
					}";

				foreach ( $_resp_shrink_ratios as $_w => $_ratio) {
					if ( ! is_numeric($_ratio) )
						continue;
					$_item_dyn_height 		= $_custom_height * $_ratio;
					$_caption_dyn_height 	= $_custom_height * ( $_ratio - 0.1 );
					$_css .= "
						@media (max-width: {$_w}px) {
							.carousel .item {
								line-height: {$_item_dyn_height}px;
								max-height:{$_item_dyn_height}px;
								min-height:{$_item_dyn_height}px;
							}
							.item .carousel-caption {
								max-height: {$_caption_dyn_height}px;
								overflow: hidden;
							}
							.carousel .tc-slider-loader-wrapper {
								line-height: {$_item_dyn_height}px;
								height:{$_item_dyn_height}px;
							}
						}";
				}
			}
		  	wp_add_inline_style( 'customizr-skin', $_css );
		}

	}//end of TC_ressources
endif;