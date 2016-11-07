<?php
/**
* Loads front end scripts
*/
if ( ! class_exists( 'CZR_cl_resources_scripts' ) ) :
	class CZR_cl_resources_scripts {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
      public $tc_script_map;

	    function __construct () {
	        self::$instance =& $this;
          add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_scripts' ) );

          //stores the front scripts map in a property
          $this -> tc_script_map = $this -> czr_fn_get_script_map();
	    }



    /**
    * Helper to get all front end script
    * Fired from the constructor
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_get_script_map( $_handles = array() ) {
      $_map = array(
        'czr-js-params' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'tc-js-params.js' ),
          'dependencies' => array( 'jquery' )
        ),
        //adds support for map method in array prototype for old ie browsers <ie9
        'czr-js-arraymap-proto' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'oldBrowserCompat.min.js' ),
          'dependencies' => array()
        ),
        'czr-bootstrap' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'bootstrap.js' , 'bootstrap.min.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'jquery', 'czr-js-params' )
        ),
        'czr-img-original-sizes' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'jqueryimgOriginalSizes.js' ),
          'dependencies' => array('jquery')
        ),
        'czr-smoothscroll' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'smoothScroll.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'underscore' )
        ),
        'czr-outline' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'outline.js' ),
          'dependencies' => array()
        ),
        'czr-dropcap' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'jqueryaddDropCap.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-bootstrap', 'underscore' )
        ),
        'czr-img-smartload' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'jqueryimgSmartLoad.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-bootstrap', 'underscore' )
        ),
        'czr-ext-links' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'jqueryextLinks.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-bootstrap', 'underscore' )
        ),
        'czr-center-images' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'jqueryCenterImages.js' ),
          'dependencies' => array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-img-original-sizes', 'czr-bootstrap', 'underscore' )
        ),
        //!!no fancybox dependency if fancybox not required!
        'czr-main-front' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/parts/',
          'files' => array( 'main.js' , 'main.min.js' ),
          'dependencies' => $this -> czr_fn_is_fancyboxjs_required() ? array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-img-original-sizes', 'czr-bootstrap', 'czr-fancybox' , 'underscore' ) : array( 'jquery' , 'czr-js-params', 'czr-img-original-sizes', 'czr-bootstrap' , 'underscore' )
        ),
        //loaded separately => not included in tc-script.js
        'czr-fancybox' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/fancybox/',
          'files' => array( 'jquery.fancybox-1.3.4.min.js' ),
          'dependencies' => $this -> czr_fn_load_concatenated_front_scripts() ? array( 'jquery' ) : array( 'czr-js-arraymap-proto', 'jquery' , 'czr-js-params', 'czr-bootstrap' )
        ),
        //concats all scripts except fancybox
        'czr-scripts' => array(
          'path' => CZR_ASSETS_PREFIX . 'front/js/',
          'files' => array( 'tc-scripts.js' , 'tc-scripts.min.js' ),
          'dependencies' =>  $this -> czr_fn_is_fancyboxjs_required() ? array( 'jquery', 'czr-fancybox' ) : array( 'jquery' )
        )
      );//end of scripts map

      return apply_filters('czr_get_script_map' , $_map, $_handles );
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
        'modernizr'
        ,
        CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/js/modernizr.min.js',
        array(),
        CUSTOMIZR_VER,
        //load in head if browser is chrome => fix the issue of 3Dtransform not detected in some cases
        ( isset($_SERVER['HTTP_USER_AGENT']) && false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) ? false : true
      );

      //customizr scripts and libs
	   	if ( $this -> czr_fn_load_concatenated_front_scripts() )	{
        if ( $this -> czr_fn_is_fancyboxjs_required() )
          $this -> czr_fn_enqueue_script( 'czr-fancybox' );
        //!!tc-scripts includes underscore, tc-js-arraymap-proto
        $this -> czr_fn_enqueue_script( 'czr-scripts' );
			}
			else {
        wp_enqueue_script( 'underscore' );
        //!!mind the dependencies
        $this -> czr_fn_enqueue_script( array( 'czr-js-params', 'czr-js-arraymap-proto', 'czr-img-original-sizes', 'czr-bootstrap', 'czr-smoothscroll', 'czr-outline' ) );

        if ( $this -> czr_fn_is_fancyboxjs_required() )
          $this -> czr_fn_enqueue_script( 'czr-fancybox' );

        $this -> czr_fn_enqueue_script( array( 'czr-dropcap' , 'czr-img-smartload', 'czr-ext-links', 'czr-center-images', 'czr-main-front' ) );
			}//end of load concatenate script if

      //carousel options
      //gets slider options if any for home/front page or for others posts/pages
      $js_slidername      = czr_fn_is_home() ? czr_fn_get_opt( 'tc_front_slider' ) : get_post_meta( czr_fn_get_id() , $key = 'post_slider_key' , $single = true );
      $js_sliderdelay     = czr_fn_is_home() ? czr_fn_get_opt( 'tc_slider_delay' ) : get_post_meta( czr_fn_get_id() , $key = 'slider_delay_key' , $single = true );

			//has the post comments ? adds a boolean parameter in js
			global $wp_query;
			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
			$anchor_smooth_scroll 		  = ( false != esc_attr( czr_fn_get_opt( 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
			if ( false != esc_attr( czr_fn_get_opt( 'tc_link_scroll') ) )
				wp_enqueue_script('jquery-effects-core');
            $anchor_smooth_scroll_exclude =  apply_filters( 'czr_anchor_smoothscroll_excl' , array(
                'simple' => array( '[class*=edd]' , '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]', '[class*=um-]' ),
                'deep'   => array(
                  'classes' => array(),
                  'ids'     => array()
                )
            ));

      $smooth_scroll_enabled = apply_filters('czr_enable_smoothscroll', ! wp_is_mobile() && 1 == esc_attr( czr_fn_get_opt( 'tc_smoothscroll') ) );
      $smooth_scroll_options = apply_filters('czr_smoothscroll_options', array( 'touchpadSupport' => false ) );

      //smart load
      $smart_load_enabled   = esc_attr( czr_fn_get_opt( 'tc_img_smart_load' ) );
      $smart_load_opts      = apply_filters( 'czr_img_smart_load_options' , array(
            'parentSelectors' => array(
                '.article-container', '.__before_main_wrapper', '.widget-front',
            ),
            'opts'     => array(
                'excludeImg' => array( '.tc-holder-img' )
            )
      ));
			//gets current screen layout
    	$screen_layout      = czr_fn_get_layout( czr_fn_get_id() , 'sidebar'  );
    	//gets the global layout settings
    	$global_layout      = apply_filters( 'czr_global_layout' , CZR_cl_init::$instance -> global_layout );
    	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
			//Gets the left and right sidebars class for js actions
			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
	    $right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

			wp_localize_script(
	        $this -> czr_fn_load_concatenated_front_scripts() ? 'czr-scripts' : 'czr-js-params',
	        'CZRParams',
	        apply_filters( 'czr_customizr_script_params' , array(
	          	'_disabled'          => apply_filters( 'czr_disabled_front_js_parts', array() ),
              'FancyBoxState' 		=> $this -> czr_fn_is_fancyboxjs_required(),
	          	'FancyBoxAutoscale' => ( 1 == czr_fn_get_opt( 'tc_fancybox_autoscale') ) ? true : false,
	          	'SliderName' 			  => $js_slidername,
	          	'SliderDelay' 			=> $js_sliderdelay,
	          	'SliderHover'			  => apply_filters( 'czr_stop_slider_hover', true ),
	          	'centerSliderImg'   => esc_attr( czr_fn_get_opt( 'tc_center_slider_img') ),
              'SmoothScroll'      => array( 'Enabled' => $smooth_scroll_enabled, 'Options' => $smooth_scroll_options ),
              'anchorSmoothScroll'			=> $anchor_smooth_scroll,
              'anchorSmoothScrollExclude' => $anchor_smooth_scroll_exclude,
	          	'ReorderBlocks' 		=> esc_attr( czr_fn_get_opt( 'tc_block_reorder') ),
	          	'centerAllImg' 			=> esc_attr( czr_fn_get_opt( 'tc_center_img') ),
	          	'HasComments' 			=> $has_post_comments,
	          	'LeftSidebarClass' 		=> $left_sb_class,
	          	'RightSidebarClass' 	=> $right_sb_class,
	          	'LoadModernizr' 		=> apply_filters( 'czr_load_modernizr' , true ),
	          	'stickyCustomOffset' 	=> apply_filters( 'czr_sticky_custom_offset' , array( "_initial" => 0, "_scrolling" => 0, "options" => array( "_static" => true, "_element" => "" ) ) ),
	          	'stickyHeader' 			=> esc_attr( czr_fn_get_opt( 'tc_sticky_header' ) ),
	          	'dropdowntoViewport' 	=> esc_attr( czr_fn_get_opt( 'tc_menu_resp_dropdown_limit_to_viewport') ),
	          	'timerOnScrollAllBrowsers' => apply_filters( 'czr_timer_on_scroll_for_all_browser' , true), //<= if false, for ie only
              'extLinksStyle'       => esc_attr( czr_fn_get_opt( 'tc_ext_link_style' ) ),
              'extLinksTargetExt'   => esc_attr( czr_fn_get_opt( 'tc_ext_link_target' ) ),
              'extLinksSkipSelectors'   => apply_filters( 'czr_ext_links_skip_selectors' , array( 'classes' => array('btn', 'button') , 'ids' => array() ) ),
              'dropcapEnabled'      => esc_attr( czr_fn_get_opt( 'tc_enable_dropcap' ) ),
              'dropcapWhere'      => array( 'post' => esc_attr( czr_fn_get_opt( 'tc_post_dropcap' ) ) , 'page' => esc_attr( czr_fn_get_opt( 'tc_page_dropcap' ) ) ),
              'dropcapMinWords'     => esc_attr( czr_fn_get_opt( 'tc_dropcap_minwords' ) ),
              'dropcapSkipSelectors'  => apply_filters( 'czr_dropcap_skip_selectors' , array( 'tags' => array('IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'), 'classes' => array('btn') , 'id' => array() ) ),
              'imgSmartLoadEnabled' => $smart_load_enabled,
              'imgSmartLoadOpts'    => $smart_load_opts,
              'goldenRatio'         => apply_filters( 'czr_grid_golden_ratio' , 1.618 ),
              'gridGoldenRatioLimit' => esc_attr( czr_fn_get_opt( 'tc_grid_thumb_height' ) ),
              'isSecondMenuEnabled'  => czr_fn_is_secondary_menu_enabled(),
              'secondMenuRespSet'   => esc_attr( czr_fn_get_opt( 'tc_second_menu_resp_setting' ) )
	        	),
	        	czr_fn_get_id()
		    )//end of filter
	     );

	    //fancybox style
	    if ( $this -> czr_fn_is_fancyboxjs_required() )
	      wp_enqueue_style( 'fancyboxcss' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/js/fancybox/jquery.fancybox-1.3.4.min.css' );

	    //holder.js is loaded when featured pages are enabled AND FP are set to show images and at least one holder should be displayed.
        if ( apply_filters( 'czr_holder_js_required', false ) ) {
	    	wp_enqueue_script(
	    		'holder',
	    		sprintf( '%1$sfront/js/holder.min.js' , CZR_BASE_URL . CZR_ASSETS_PREFIX ),
	    		array(),
	    		CUSTOMIZR_VER,
	    		$in_footer = true
	    	);
	    }

	    //load retina.js in footer if enabled
	    if ( apply_filters('czr_load_retinajs', 1 == czr_fn_get_opt( 'tc_retina_support' ) ) )
	    	wp_enqueue_script( 'retinajs' ,CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/js/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

	    //Load hammer.js for mobile
	    if ( apply_filters('czr_load_hammerjs', wp_is_mobile() ) )
	    	wp_enqueue_script( 'hammer' ,CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/js/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

		}





    /*************************************
    * HELPERS
    *************************************/
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
        sprintf( '%1$s%2$s%3$s',CZR_BASE_URL , $_params['path'], $_filename ),
        $_params['dependencies'],
        CUSTOMIZR_VER,
        apply_filters( "czr_load_{$_handle}_in_footer", false )
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
      return apply_filters( 'czr_load_concatenated_front_scripts' , ! defined('CZR_DEV')  || ( defined('CZR_DEV') && false == CZR_DEV ) );
    }

    /**
    * Helper to check if we need fancybox or not on front
    *
    * @return boolean
    * @package Customizr
    * @since v3.3+
    */
    private function czr_fn_is_fancyboxjs_required() {
      return czr_fn_get_opt( 'tc_fancybox' ) || czr_fn_get_opt( 'tc_gallery_fancybox');
    }

  }//end of CZR_cl_resources
endif;
