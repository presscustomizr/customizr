<?php
/**
* Loads front end stylesheets and scripts
*
*/
if ( ! class_exists( 'CZR_resources' ) ) :
	class CZR_resources {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
      public $tc_script_map;
      public $current_random_skin;

      private $_resources_version;

	    function __construct () {

	        self::$instance =& $this;

          $this->_resouces_version = CZR_DEBUG_MODE || CZR_DEV_MODE ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER;

          add_action( 'wp_enqueue_scripts'            , array( $this , 'czr_fn_enqueue_gfonts' ) , 0 );
	        add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_styles' ) );
          add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_scripts' ) );

	        //Custom CSS
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_custom_css') , apply_filters( 'tc_custom_css_priority', 9999 ) );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_fonts_inline_css') );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_dropcap_inline_css') );

          /* See: https://github.com/presscustomizr/customizr/issues/605 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_apply_media_upload_front_patch' ) );
          /* See: https://github.com/presscustomizr/customizr/issues/787 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_maybe_avoid_double_social_icon' ) );

          //set random skin
          add_filter ('tc_opt_tc_skin'                , array( $this, 'czr_fn_set_random_skin' ) );

          add_action( 'wp_ajax_dismiss_style_switcher_note_front',  array( $this , 'czr_fn_dismiss_style_switcher_note_front' ) );
          add_action( 'wp_ajax_nopriv_dismiss_style_switcher_note_front',  array( $this , 'czr_fn_dismiss_style_switcher_note_front' ) );

          //stores the front scripts map in a property
          $this -> tc_script_map = $this -> czr_fn_get_script_map();

          add_filter( 'czr_style_note_content', array( $this,  'czr_fn_get_style_note_content' ) );
	    }//construct


  	  /**
  		* Registers and enqueues Customizr stylesheets
  		* @package Customizr
  		* @since Customizr 1.1
  		*/
      function czr_fn_enqueue_front_styles() {
            //Enqueue FontAwesome CSS
            if ( true == czr_fn_opt( 'tc_font_awesome_icons' ) ) {
              $_path = apply_filters( 'tc_font_icons_path' , TC_BASE_URL . 'assets/shared/fonts/fa/css/' );
              wp_enqueue_style( 'customizr-fa',
                  $_path . 'fontawesome-all.min.css',
                  array() , $this->_resouces_version, 'all' );
            }

  	      wp_enqueue_style( 'customizr-common', CZR_init::$instance -> czr_fn_get_style_src( 'common') , array() , $this->_resouces_version, 'all' );
            //Customizr active skin
  	      wp_register_style( 'customizr-skin', CZR_init::$instance -> czr_fn_get_style_src( 'skin'), array('customizr-common'), $this->_resouces_version, 'all' );
  	      wp_enqueue_style( 'customizr-skin' );
  	      //Customizr stylesheet (style.css)
  	      wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), $this->_resouces_version , 'all' );

  	      //Customizer user defined style options : the custom CSS is written with a high priority here
  	      wp_add_inline_style( 'customizr-skin', apply_filters( 'tc_user_options_style' , '' ) );
  		}



      /**
      * Helper to get all front end script
      * Fired from the constructor
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      private function czr_fn_get_script_map( $_handles = array() ) {
          $_front_path  =  'inc/assets/js/';
          $_libs_path =  CZR_ASSETS_PREFIX . 'front/js/libs/';

          $_map = array(
              'tc-js-params' => array(
                'path' => $_front_path,
                'files' => array( 'tc-js-params.js' ),
                'dependencies' => array( 'jquery' )
              ),
              //adds support for map method in array prototype for old ie browsers <ie9
              'tc-js-arraymap-proto' => array(
                'path' => $_libs_path,
                'files' => array( 'oldBrowserCompat.min.js' ),
                'dependencies' => array()
              ),
              'tc-bootstrap' => array(
                'path' => $_libs_path,
                'files' => array( 'bootstrap-classical.js' , 'bootstrap-classical.min.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery', 'tc-js-params' )
              ),
              'tc-img-original-sizes' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryimgOriginalSizes.js' ),
                'dependencies' => array('jquery')
              ),
              'tc-smoothscroll' => array(
                'path' => $_libs_path,
                'files' => array( 'smoothscroll.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'underscore' )
              ),
              'tc-outline' => array(
                'path' => $_libs_path,
                'files' => array( 'outline.js' ),
                'dependencies' => array()
              ),
              'tc-waypoints' => array(
                'path' => $_libs_path,
                'files' => array( 'waypoints.js' ),
                'dependencies' => array('jquery')
              ),
              'tc-dropcap' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryaddDropCap.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-img-smartload' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryimgSmartLoad.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-ext-links' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryextLinks.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-parallax' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryParallax.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
              ),
              'tc-center-images' => array(
                'path' => $_libs_path . 'jquery-plugins/',
                'files' => array( 'jqueryCenterImages.js' ),
                'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'underscore' )
              ),
              //!!no fancybox dependency if fancybox not required!
              'tc-main-front' => array(
                'path' => $_front_path,
                'files' => array( 'main-ccat.js' , 'main-ccat.min.js' ),
                'dependencies' => $this -> czr_fn_is_fancyboxjs_required() ? array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-fancybox' , 'underscore' ) : array( 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap' , 'underscore' )
              ),
              //loaded separately => not included in tc-script.js
              'tc-fancybox' => array(
                'path' => $_libs_path . 'fancybox/',
                'files' => array( 'jquery.fancybox-1.3.4.min.js' ),
                'dependencies' => $this -> czr_fn_load_concatenated_front_scripts() ? array( 'jquery' ) : array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap' )
              ),
              //concats all scripts except fancybox
              'tc-scripts' => array(
                'path' => $_front_path,
                'files' => array( 'tc-scripts.js' , 'tc-scripts.min.js' ),
                'dependencies' =>  $this -> czr_fn_is_fancyboxjs_required() ? array( 'underscore', 'jquery', 'tc-fancybox' ) : array( 'underscore', 'jquery' )
              )
          );//end of scripts map

          return apply_filters('tc_get_script_map' , $_map, $_handles );
      }



  		/**
  		* Loads Customizr front scripts
      * Dependencies are defined in the script map property
      *
  		* @return  void()
  		* @uses wp_enqueue_script() to manage script dependencies
  		* @package Customizr
  		* @since Customizr 1.0
  		*/
  		function czr_fn_enqueue_front_scripts() {
  	    //wp scripts
  	  	if ( is_singular() && get_option( 'thread_comments' ) )
  		    wp_enqueue_script( 'comment-reply' );

  	    wp_enqueue_script( 'jquery' );
  	    wp_enqueue_script( 'jquery-ui-core' );

  	    wp_enqueue_script(
          'modernizr',
          TC_BASE_URL . 'assets/front/js/libs/modernizr.min.js',
          array(),
          CUSTOMIZR_VER,
          //load in head if browser is chrome => fix the issue of 3Dtransform not detected in some cases
          ( isset($_SERVER['HTTP_USER_AGENT']) && false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) ? false : true
        );

        //customizr scripts and libs
  	   	if ( $this -> czr_fn_load_concatenated_front_scripts() )	{
          if ( $this -> czr_fn_is_fancyboxjs_required() )
            $this -> czr_fn_enqueue_script( 'tc-fancybox' );
          //!!tc-scripts includes underscore, tc-js-arraymap-proto
          $this -> czr_fn_enqueue_script( 'tc-scripts' );
  			}
  			else {
          wp_enqueue_script( 'underscore' );
          //!!mind the dependencies
          $this -> czr_fn_enqueue_script( array( 'tc-js-params', 'tc-js-arraymap-proto', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-smoothscroll', 'tc-outline', 'tc-waypoints' ) );

          if ( $this -> czr_fn_is_fancyboxjs_required() )
            $this -> czr_fn_enqueue_script( 'tc-fancybox' );

          $this -> czr_fn_enqueue_script( array( 'tc-dropcap' , 'tc-img-smartload', 'tc-ext-links', 'tc-center-images', 'tc-parallax', 'tc-main-front' ) );
  			}//end of load concatenate script if

        //carousel options
        //gets slider options if any for home/front page or for others posts/pages
        $js_slidername      = czr_fn__f('__is_home') ? czr_fn_opt( 'tc_front_slider' ) : get_post_meta( czr_fn_get_id() , $key = 'post_slider_key' , $single = true );
        $js_sliderdelay     = czr_fn__f('__is_home') ? czr_fn_opt( 'tc_slider_delay' ) : get_post_meta( czr_fn_get_id() , $key = 'slider_delay_key' , $single = true );

  			//has the post comments ? adds a boolean parameter in js
  			global $wp_query;
  			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

  			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
  			$anchor_smooth_scroll 		  = ( false != esc_attr( czr_fn_opt( 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
  			if ( false != esc_attr( czr_fn_opt( 'tc_link_scroll') ) )
  				wp_enqueue_script('jquery-effects-core');
              $anchor_smooth_scroll_exclude =  apply_filters( 'tc_anchor_smoothscroll_excl' , array(
                  'simple' => array( '[class*=edd]' , '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[data-toggle="pill"]', '[class*=upme]', '[class*=um-]' ),
                  'deep'   => array(
                    'classes' => array(),
                    'ids'     => array()
                  )
              ));

        $smooth_scroll_enabled = apply_filters('tc_enable_smoothscroll', ! wp_is_mobile() && 1 == esc_attr( czr_fn_opt( 'tc_smoothscroll') ) );
        $smooth_scroll_options = apply_filters('tc_smoothscroll_options', array( 'touchpadSupport' => false ) );

        //smart load
        $smart_load_enabled   = esc_attr( czr_fn_opt( 'tc_img_smart_load' ) );
        $smart_load_opts      = apply_filters( 'tc_img_smart_load_options' , array(
              'parentSelectors' => array(
                  '.article-container', '.__before_main_wrapper', '.widget-front',
              ),
              'opts'     => array(
                  'excludeImg' => array( '.tc-holder-img' )
              )
        ));
  			//gets current screen layout
      	$screen_layout      = CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'sidebar'  );
      	//gets the global layout settings
      	$global_layout      = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
      	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
  			//Gets the left and right sidebars class for js actions
  			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
  	    $right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

        //Style switcher note
        $is_style_switch_note_on = ! is_multisite() && czr_fn_user_can_see_customize_notices_on_front() && ! czr_fn_is_customizing() && ! czr_fn_isprevdem();
        $style_note_content = '';
        if ( $is_style_switch_note_on && ! czr_fn_is_ms() && false === czr_fn_opt( 'tc_style', CZR_THEME_OPTIONS, false ) ) { //false for not default
            $tc_custom_css = esc_html( czr_fn_opt( 'tc_custom_css') );
            $tc_custom_css = trim( $tc_custom_css );
            $wp_custom_css = '';
            if ( function_exists( "wp_get_custom_css" ) ) {
                $wp_custom_css = wp_get_custom_css();
                $wp_custom_css = trim( $wp_custom_css );
            }

            $is_style_switch_note_on = $is_style_switch_note_on && empty( $tc_custom_css ) && empty( $wp_custom_css );
            $is_style_switch_note_on = apply_filters(
                'czr_is_style_switch_notification_on',
                $is_style_switch_note_on && ! CZR_IS_MODERN_STYLE && ! is_child_theme() && 'dismissed' != get_transient( 'czr_style_switch_note_status' )
            );
            if ( $is_style_switch_note_on ) {
                $style_note_content = apply_filters( 'czr_style_note_content', '' );
            }
        }

  			wp_localize_script(
  	        $this -> czr_fn_load_concatenated_front_scripts() ? 'tc-scripts' : 'tc-js-params',
  	        'TCParams',
  	        apply_filters( 'tc_customizr_script_params' , array(
  	          	'_disabled'          => apply_filters( 'tc_disabled_front_js_parts', array() ),
                'FancyBoxState' 		=> $this -> czr_fn_is_fancyboxjs_required(),
  	          	'FancyBoxAutoscale' => ( 1 == czr_fn_opt( 'tc_fancybox_autoscale') ) ? true : false,
  	          	'SliderName' 			  => $js_slidername,
  	          	'SliderDelay' 			=> $js_sliderdelay,
  	          	'SliderHover'			  => apply_filters( 'tc_stop_slider_hover', true ),
  	          	'centerSliderImg'   => esc_attr( czr_fn_opt( 'tc_center_slider_img') ),
                'SmoothScroll'      => array( 'Enabled' => $smooth_scroll_enabled, 'Options' => $smooth_scroll_options ),
                'anchorSmoothScroll'			=> $anchor_smooth_scroll,
                'anchorSmoothScrollExclude' => $anchor_smooth_scroll_exclude,
  	          	'ReorderBlocks' 		=> esc_attr( czr_fn_opt( 'tc_block_reorder') ),
  	          	'centerAllImg' 			=> esc_attr( czr_fn_opt( 'tc_center_img') ),
  	          	'HasComments' 			=> $has_post_comments,
  	          	'LeftSidebarClass' 		=> $left_sb_class,
  	          	'RightSidebarClass' 	=> $right_sb_class,
  	          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
  	          	'stickyCustomOffset' 	=> apply_filters( 'tc_sticky_custom_offset' , array( "_initial" => 0, "_scrolling" => 0, "options" => array( "_static" => true, "_element" => "" ) ) ),
  	          	'stickyHeader' 			=> esc_attr( czr_fn_opt( 'tc_sticky_header' ) ),
  	          	'dropdowntoViewport' 	=> esc_attr( czr_fn_opt( 'tc_menu_resp_dropdown_limit_to_viewport') ),
  	          	'timerOnScrollAllBrowsers' => apply_filters( 'tc_timer_on_scroll_for_all_browser' , true), //<= if false, for ie only
                'extLinksStyle'       => esc_attr( czr_fn_opt( 'tc_ext_link_style' ) ),
                'extLinksTargetExt'   => esc_attr( czr_fn_opt( 'tc_ext_link_target' ) ),
                'extLinksSkipSelectors'   => apply_filters( 'tc_ext_links_skip_selectors' , array( 'classes' => array('btn', 'button') , 'ids' => array() ) ),
                'dropcapEnabled'      => esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ),
                'dropcapWhere'      => array( 'post' => esc_attr( czr_fn_opt( 'tc_post_dropcap' ) ) , 'page' => esc_attr( czr_fn_opt( 'tc_page_dropcap' ) ) ),
                'dropcapMinWords'     => esc_attr( czr_fn_opt( 'tc_dropcap_minwords' ) ),
                'dropcapSkipSelectors'  => apply_filters( 'tc_dropcap_skip_selectors' , array( 'tags' => array('IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'), 'classes' => array('btn', 'tc-placeholder-wrap' ) , 'id' => array() ) ),

                'imgSmartLoadEnabled' => $smart_load_enabled,
                'imgSmartLoadOpts'    => $smart_load_opts,
                'imgSmartLoadsForSliders' => czr_fn_is_checked( 'tc_slider_img_smart_load' ),

                'goldenRatio'         => apply_filters( 'tc_grid_golden_ratio' , 1.618 ),
                'gridGoldenRatioLimit' => esc_attr( czr_fn_opt( 'tc_grid_thumb_height' ) ),
                'isSecondMenuEnabled'  => czr_fn_is_secondary_menu_enabled(),
                'secondMenuRespSet'   => esc_attr( czr_fn_opt( 'tc_second_menu_resp_setting' ) ),

                'isParallaxOn'        => esc_attr( czr_fn_opt( 'tc_slider_parallax') ),
                'parallaxRatio'       => apply_filters('tc_parallax_ratio', 0.55 ),

                'pluginCompats'       => apply_filters( 'tc_js_params_plugin_compat', array() ),

                //AJAX
                'adminAjaxUrl'        => admin_url( 'admin-ajax.php' ),
                'ajaxUrl'             => add_query_arg(
                      array( 'czrajax' => true ), //to scope our ajax calls
                      set_url_scheme( home_url( '/' ) )
                ),
                'frontNonce'   => array( 'id' => 'CZRFrontNonce', 'handle' => wp_create_nonce( 'czr-front-nonce' ) ),

                'isDevMode'        => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('CZR_DEV') && true === CZR_DEV ),
                'isModernStyle'    => CZR_IS_MODERN_STYLE,

                'i18n' => apply_filters( 'czr_front_js_translated_strings',
                    array(
                        'Permanently dismiss' => __('Permanently dismiss', 'customizr')
                    )
                ),
                'version' => CUSTOMIZR_VER,

                //FRONT NOTIFICATIONS
                //ordered by priority
                'frontNotifications' => array(
                      'styleSwitcher' => array(
                          'enabled' => $is_style_switch_note_on,
                          'content' => $style_note_content,
                          'dismissAction' => 'dismiss_style_switcher_note_front',
                          'ajaxUrl' => admin_url( 'admin-ajax.php' )
                      )
                )
  	        	),
  	        	czr_fn_get_id()
  		    )//end of filter
  	     );

  	    //fancybox style
  	    if ( $this -> czr_fn_is_fancyboxjs_required() )
  	      wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'assets/front/js/libs/fancybox/jquery.fancybox-1.3.4.min.css' );

  	    //holder.js is loaded when featured pages are enabled AND FP are set to show images and at least one holder should be displayed.
        $tc_show_featured_pages 	         = class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages();
      	if ( (bool)$tc_show_featured_pages && $this -> czr_fn_maybe_is_holder_js_required() ) {
  	    	wp_enqueue_script(
  	    		'holder',
  	    		sprintf( '%1$sassets/front/js/libs/holder.min.js' , TC_BASE_URL ),
  	    		array(),
  	    		CUSTOMIZR_VER,
  	    		$in_footer = true
  	    	);
  	    }

  	    //load retina.js in footer if enabled
  	    if ( apply_filters('tc_load_retinajs', 1 == czr_fn_opt( 'tc_retina_support' ) ) )
  	    	wp_enqueue_script( 'retinajs' ,TC_BASE_URL . 'assets/front/js/libs/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

  	    //Load hammer.js for mobile
  	    if ( apply_filters('tc_load_hammerjs', wp_is_mobile() ) )
  	    	wp_enqueue_script( 'hammer' ,TC_BASE_URL . 'assets/front/js/libs/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

  		}



      /**
      * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
      * hook : tc_user_options_style
      * @package Customizr
      * @since Customizr 2.0.7
      */
      function czr_fn_write_custom_css( $_css = null ) {
        $_css               = isset($_css) ? $_css : '';

        $_moved_opts        = czr_fn_opt(  '__moved_opts' ) ;

        /*
        * Do not print old custom css if moved in the WP Custom CSS
        */
        if ( !empty( $_moved_opts ) && is_array( $_moved_opts ) && in_array( 'custom_css', $_moved_opts) )
          return $_css;

        $tc_custom_css      = esc_html( czr_fn_opt( 'tc_custom_css') );
        if ( ! isset($tc_custom_css) || empty($tc_custom_css) )
          return $_css;

        return apply_filters( 'tc_write_custom_css',
          $_css . "\n" . html_entity_decode( $tc_custom_css ),
          $_css,
          czr_fn_opt( 'tc_custom_css')
        );
      }//end of function


      /* See: https://github.com/presscustomizr/customizr/issues/605 */
      function czr_fn_apply_media_upload_front_patch( $_css ) {
        global $wp_version;
        if ( version_compare( '4.5', $wp_version, '<=' ) )
          $_css = sprintf("%s%s",
    		            	$_css,
                          'table { border-collapse: separate; }
                           body table { border-collapse: collapse; }
                          ');
        return $_css;
      }

      /*
      * Use the dynamic style to fix server side caching issue,
      * which is the main reason why we needed this patch
      * We don't subordinate this to the user_started_before a certain version
      * as it also fixes potential plugin compatibility (plugins which style .icon-* before)
      * https://github.com/presscustomizr/customizr/issues/787
      * ( all this will be removed in c4 )
      */
      function czr_fn_maybe_avoid_double_social_icon( $_css ) {
        return sprintf( "%s\n%s", $_css, '.social-links .social-icon:before { content: none } ');
      }

      /*
      * Callback of wp_enqueue_scripts
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function czr_fn_enqueue_gfonts() {
        $_font_pair         = esc_attr( czr_fn_opt( 'tc_fonts' ) );
        $_all_font_pairs    = CZR___::$instance -> font_pairs;
        if ( ! czr_fn_is_gfont( $_font_pair , '_g_') )
          return;

        wp_enqueue_style(
          'tc-gfonts',
          sprintf( '//fonts.googleapis.com/css?family=%s', str_replace( '|', '%7C', czr_fn_get_font( 'single' , $_font_pair ) ) ),
          array(),
          null,
          'all'
        );
      }



      /**
      * Callback of tc_user_options_style hook
      * + Fired in czr_fn_user_defined_tinymce_css => add the user defined font style to the wp editor
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function czr_fn_write_fonts_inline_css( $_css = null , $_context = null ) {
        $_css               = isset($_css) ? $_css : '';
        $_font_pair         = esc_attr( czr_fn_opt( 'tc_fonts' ) );
        $_body_font_size    = esc_attr( czr_fn_opt( 'tc_body_font_size' ) );
        $_font_selectors    = CZR_init::$instance -> font_selectors;

        //create the $body and $titles vars
        extract( CZR_init::$instance -> font_selectors, EXTR_OVERWRITE );

        if ( ! isset($body) || ! isset($titles) )
          return;

        //adapt the selectors in edit context => add specificity for the mce-editor
        if ( ! is_null( $_context ) ) {
          $titles = ".{$_context} h1, .{$_context} h2, .{$_context} h3";
          $body   = "body.{$_context}";
        }

        $titles = apply_filters('tc_title_fonts_selectors' , $titles );
        $body   = apply_filters('tc_body_fonts_selectors' , $body );

        if ( 'helvetica_arial' != $_font_pair ) {//check if not default
          $_selector_fonts  = explode( '|', czr_fn_get_font( 'single' , $_font_pair ) );
          if ( ! is_array($_selector_fonts) )
            return $_css;

          foreach ($_selector_fonts as $_key => $_raw_font) {
            //create the $_family and $_weight vars
            extract( $this -> czr_fn_get_font_css_prop( $_raw_font , czr_fn_is_gfont( $_font_pair ) ) );

            switch ($_key) {
              case 0 : //titles font
                $_css .= "
                  {$titles} {
                    font-family : {$_family};
                    font-weight : {$_weight};
                  }\n";
              break;

              case 1 ://body font
                $_css .= "
                  {$body} {
                    font-family : {$_family};
                    font-weight : {$_weight};
                  }\n";
              break;
            }
          }
        }//end if

        if ( 15 != $_body_font_size ) {
          $_line_height = apply_filters('tc_body_line_height_ratio', 1.6 );
          $_css .= "
            {$body} {
              font-size : {$_body_font_size}px;
              line-height : {$_line_height}em;
            }\n";
          }

        return $_css;
      }//end of fn


      /**
      * Callback of tc_user_options_style hook
      * @return css string
      *
      * @package Customizr
      * @since Customizr 3.2.11
      */
      function czr_fn_write_dropcap_inline_css( $_css = null , $_context = null ) {
        $_css               = isset($_css) ? $_css : '';
        if ( ! esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ) )
          return $_css;

        $_main_color_pair = CZR_utils::$inst -> czr_fn_get_skin_color( 'pair' );
        $_color           = $_main_color_pair[0];
        $_shad_color      = $_main_color_pair[1];
        $_pad_right       = false !== strpos( esc_attr( czr_fn_opt( 'tc_fonts' ) ), 'lobster' ) ? 26 : 8;
        $_css .= "
          .tc-dropcap {
            color: {$_color};
            float: left;
            font-size: 75px;
            line-height: 75px;
            padding-right: {$_pad_right}px;
            padding-left: 3px;
          }\n
          .skin-shadow .tc-dropcap {
            color: {$_color};
            text-shadow: {$_shad_color} -1px 0, {$_shad_color} 0 -1px, {$_shad_color} 0 1px, {$_shad_color} -1px -2px;
          }\n
          .simple-black .tc-dropcap {
            color: #444;
          }\n";

        return $_css;
      }


      /**
      * Set random skin
      * hook tc_opt_tc_skin
      *
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_set_random_skin ( $_skin ) {
        if ( false == esc_attr( czr_fn_opt( 'tc_skin_random' ) ) )
          return $_skin;

        //allow custom skins to be taken in account
        $_skins = apply_filters( 'tc_get_skin_color', CZR___::$instance -> skin_classic_color_map, 'all' );

        //allow users to filter the list of skins they want to randomize
        $_skins = apply_filters( 'tc_skins_to_randomize', $_skins );

        /* Generate the random skin just once !*/
        if ( ! $this -> current_random_skin && is_array( $_skins ) )
          $this -> current_random_skin = array_rand( $_skins, 1 );

        return $this -> current_random_skin;
      }


      /*************************************
      * HELPERS
      *************************************/
      /**
      * Helper to extract font-family and weight from a Customizr font option
      * @return array( font-family, weight )
      *
      * @package Customizr
      * @since Customizr 3.3.2
      */
      private function czr_fn_get_font_css_prop( $_raw_font , $is_gfont = false ) {
        $_css_exp = explode(':', $_raw_font);
        $_weight  = isset( $_css_exp[1] ) ? $_css_exp[1] : 'inherit';
        $_family  = '';

        if ( $is_gfont ) {
          $_family = str_replace('+', ' ' , $_css_exp[0]);
        } else {
          $_family = implode("','", explode(',', $_css_exp[0] ) );
        }
        $_family = sprintf("'%s'" , $_family );

        return compact("_family" , "_weight" );
      }


      /**
      * Convenient method to normalize script enqueueing in the Customizr theme
      * @return  void
      * @uses wp_enqueue_script() to manage script dependencies
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_enqueue_script( $_handles = array() ) {
        if ( empty($_handles) )
          return;

        $_map = $this -> tc_script_map;
        //Picks the requested handles from map
        if ( 'string' == gettype($_handles) && isset($_map[$_handles]) ) {
          $_scripts = array( $_handles => $_map[$_handles] );
        }
        else {
          $_scripts = array();
          foreach ( $_handles as $_hand ) {
            if ( !isset( $_map[$_hand]) )
              continue;
            $_scripts[$_hand] = $_map[$_hand];
          }
        }

        //Enqueue the scripts with normalizes args
        foreach ( $_scripts as $_hand => $_params )
          call_user_func_array( 'wp_enqueue_script',  $this -> czr_fn_normalize_script_args( $_hand, $_params ) );

      }//end of fn



      /**
      * Helper to normalize the arguments passed to wp_enqueue_script()
      * Also handles the minified version of the file
      *
      * @return array of arguments for wp_enqueue_script
      * @package Customizr
      * @since Customizr 3.3+
      */
      private function czr_fn_normalize_script_args( $_handle, $_params ) {
        //Do we load the minified version if available ?
        if ( count( $_params['files'] ) > 1 )
          $_filename = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? $_params['files'][0] : $_params['files'][1];
        else
          $_filename = $_params['files'][0];

        return array(
          $_handle,
          sprintf( '%1$s%2$s%3$s',TC_BASE_URL , $_params['path'], $_filename ),
          $_params['dependencies'],
          CZR_DEBUG_MODE || CZR_DEV_MODE ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER,
          apply_filters( "tc_load_{$_handle}_in_footer", false )
        );
      }

      /**
      * Helper
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      function czr_fn_load_concatenated_front_scripts() {
          return apply_filters( 'tc_load_concatenated_front_scripts' , ! defined('CZR_DEV')  || ( defined('CZR_DEV') && false == CZR_DEV ) );
      }

      /**
      * Helper to check if we need fancybox or not on front
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      private function czr_fn_is_fancyboxjs_required() {
        return czr_fn_opt( 'tc_fancybox' ) || czr_fn_opt( 'tc_gallery_fancybox');
      }

      /**
      * Helper to check if we need to enqueue holder js
      *
      * @return boolean
      * @package Customizr
      * @since v3.3+
      */
      function czr_fn_maybe_is_holder_js_required(){
        $bool = false;

        if ( ! ( class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages_img() ) )
          return $bool;

        $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR___::$instance -> fp_ids);

        foreach ( $fp_ids as $fp_single_id ){
          $featured_page_id = czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
          if ( null == $featured_page_id || ! $featured_page_id || ! CZR_featured_pages::$instance -> czr_fn_get_fp_img( null, $featured_page_id, null ) ) {
            $bool = true;
            break;
          }
        }
        return $bool;
      }

      /* ------------------------------------------------------------------------- *
       *  STYLE NOTE
      /* ------------------------------------------------------------------------- */
      //hook : 'czr_style_note_content'
      //This function is invoked only when :
      //1) czr_fn_user_started_before_version( '4.0.0', '2.0.0' )
      //2) AND if the note can be displayed : czr_fn_user_can_see_customize_notices_on_front() && ! czr_fn_is_customizing() && ! czr_fn_isprevdem() && 'dismissed' != get_transient( 'czr_style_switch_note_status' )
      //It returns a welcome note html string that will be localized in the front js
      //@return html string
      function czr_fn_get_style_note_content() {
        // beautify notice text using some defaults the_content filter callbacks
        // => turns emoticon :D into an svg
        foreach ( array( 'wptexturize', 'convert_smilies', 'wpautop') as $callback ) {
          if ( function_exists( $callback ) )
              add_filter( 'czr_front_style_switch_note_html', $callback );
        }
        ob_start();
          ?>
              <?php
                  printf( '<br/><p>%1$s</p>',
                      sprintf( __('Quick tip : you can choose between two styles for the Customizr theme. Give it a try %s', 'customizr'),
                          sprintf( '<a href="%1$s">%2$s</a>',
                              czr_fn_get_customizer_url( array( 'control' => 'tc_style', 'section' => 'style_sec') ),
                              __('in the live customizer.', 'customizr')
                          )
                      )
                  );
              ?>

          <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        return $html; //apply_filters('czr_front_style_switch_note_html', $html );
    }


    //hook : czr_ajax_dismiss_style_switcher_note_front
    function czr_fn_dismiss_style_switcher_note_front() {
        set_transient( 'czr_style_switch_note_status', 'dismissed' , 60*60*24*365*20 );//20 years of peace
        wp_send_json_success( array( 'status_note' => 'dismissed' ) );
    }
  }//end of CZR_ressources
endif;

?>