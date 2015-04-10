<?php
/**
* Loads front end stylesheets and scripts
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
if ( ! class_exists( 'TC_resources' ) ) :
	class TC_resources {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
	    function __construct () {
	        self::$instance =& $this;
          add_action( 'wp_enqueue_scripts'            , array( $this , 'tc_enqueue_gfonts' ) , 0 );
	        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_styles' ) );
	        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_scripts' ) );
          //Custom Stylesheets
          //Write font icon
          add_filter('tc_user_options_style'          , array( $this , 'tc_write_inline_font_icons_css') , apply_filters( 'tc_font_icon_priority', 999 ) );
	        //Custom CSS
          add_filter('tc_user_options_style'          , array( $this , 'tc_write_custom_css') , apply_filters( 'tc_custom_css_priority', 9999 ) );
          add_filter('tc_user_options_style'          , array( $this , 'tc_write_fonts_inline_css') );
          add_filter('tc_user_options_style'          , array( $this , 'tc_write_dropcap_inline_css') );

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
		    wp_enqueue_style( 'customizr-common', TC_init::$instance -> tc_get_style_src( 'common') , array() , CUSTOMIZR_VER, 'all' );
        //Customizr active skin
		    wp_register_style( 'customizr-skin', TC_init::$instance -> tc_get_style_src( 'skin'), array('customizr-common'), CUSTOMIZR_VER, 'all' );
		    wp_enqueue_style( 'customizr-skin' );
		    //Customizr stylesheet (style.css)
		    wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), CUSTOMIZR_VER , 'all' );

		    //Customizer user defined style options : the custom CSS is written with a high priority here
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
	    wp_enqueue_script( 'modernizr' , TC_BASE_URL . 'inc/assets/js/modernizr.min.js', array(), CUSTOMIZR_VER, true);

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
		    	apply_filters('tc_load_script_in_footer' , false)
		    );
			}
			else
			{
	    	wp_enqueue_script(
		    	'tc-js-params',
		    	sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , 'tc-js-params.js'),
		    	array( 'jquery' ),
		    	CUSTOMIZR_VER,
		    	apply_filters('tc_load_script_in_footer' , false)
	    	);
	    	wp_enqueue_script(
		    	'tc-bootstrap',
		    	sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'bootstrap.js' : 'bootstrap.min.js'),
		    	array( 'jquery' , 'tc-js-params',  ),
		    	CUSTOMIZR_VER,
		    	apply_filters('tc_load_script_in_footer' , false)
	    	);
	    	wp_enqueue_script(
		    	'tc-fancybox',
		    	sprintf( '%1$sinc/assets/js/fancybox/%2$s' , TC_BASE_URL , 'jquery.fancybox-1.3.4.min.js' ),
		    	array( 'jquery' , 'tc-js-params', 'tc-bootstrap' ),
		    	CUSTOMIZR_VER,
		    	apply_filters('tc_load_script_in_footer' , false)
	    	);
        wp_enqueue_script(
          'tc-dropcap',
          sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , 'jqueryaddDropCap.js' ),
          array( 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' ),
          CUSTOMIZR_VER,
          apply_filters('tc_load_script_in_footer' , false)
        );
        wp_enqueue_script(
          'tc-img-smartload',
          sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , 'jqueryimgSmartLoad.js' ),
          array( 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' ),
          CUSTOMIZR_VER,
          apply_filters('tc_load_script_in_footer' , false)
        );
        wp_enqueue_script(
          'tc-ext-links',
          sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , 'jqueryextLinks.js' ),
          array( 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' ),
          CUSTOMIZR_VER,
          apply_filters('tc_load_script_in_footer' , false)
        );
        wp_enqueue_script(
          'tc-center-images',
          sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , 'jqueryCenterImages.js' ),
          array( 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' ),
          CUSTOMIZR_VER,
          apply_filters('tc_load_script_in_footer' , false)
        );
        wp_enqueue_script(
          'tc-main-front',
          sprintf( '%1$sinc/assets/js/parts/%2$s' , TC_BASE_URL , ( defined('WP_DEBUG') && true === WP_DEBUG ) ? 'main.js' : 'main.min.js'),
          array( 'jquery' , 'tc-js-params', 'tc-bootstrap', 'tc-fancybox' , 'underscore' ),
          CUSTOMIZR_VER,
          apply_filters('tc_load_script_in_footer' , false)
        );
			}//end of load concatenate script if

		  //fancybox options
			$tc_fancybox 		= ( 1 == TC_utils::$inst->tc_opt( 'tc_fancybox' ) ) ? true : false;
			$autoscale 			= ( 1 == TC_utils::$inst->tc_opt( 'tc_fancybox_autoscale') ) ? true : false ;

      //carousel options
      //gets slider options if any for home/front page or for others posts/pages
      $js_slidername      = tc__f('__is_home') ? TC_utils::$inst->tc_opt( 'tc_front_slider' ) : get_post_meta( TC_utils::tc_id() , $key = 'post_slider_key' , $single = true );
      $js_sliderdelay     = tc__f('__is_home') ? TC_utils::$inst->tc_opt( 'tc_slider_delay' ) : get_post_meta( TC_utils::tc_id() , $key = 'slider_delay_key' , $single = true );

			//has the post comments ? adds a boolean parameter in js
			global $wp_query;
			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
			$smooth_scroll 		= ( false != esc_attr( TC_utils::$inst->tc_opt( 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
			if ( false != esc_attr( TC_utils::$inst->tc_opt( 'tc_link_scroll') ) )
				wp_enqueue_script('jquery-effects-core');

			//gets current screen layout
    	$screen_layout      = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar'  );
    	//gets the global layout settings
    	$global_layout      = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
    	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
			//Gets the left and right sidebars class for js actions
			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
	    $right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

			wp_localize_script(
	        ( ! apply_filters('tc_load_concatenated_front_scripts' , true ) ) ? 'tc-js-params' : 'tc-scripts',
	        'TCParams',
	        apply_filters( 'tc_customizr_script_params' , array(
	          	'FancyBoxState' 		=> $tc_fancybox,
	          	'FancyBoxAutoscale' 	=> $autoscale,
	          	'SliderName' 			=> $js_slidername,
	          	'SliderDelay' 			=> $js_sliderdelay,
	          	'SliderHover'			=> apply_filters( 'tc_stop_slider_hover', true ),
	          	'centerSliderImg'   => esc_attr( TC_utils::$inst->tc_opt( 'tc_center_slider_img') ),
              'SmoothScroll'			=> $smooth_scroll,
              'SmoothScrollExclude' => apply_filters( 'tc_smoothscroll_excl' , array( '[class*=edd]' , '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]' ) ),
	          	'ReorderBlocks' 		=> esc_attr( TC_utils::$inst->tc_opt( 'tc_block_reorder') ),
	          	'centerAllImg' 			=> esc_attr( TC_utils::$inst->tc_opt( 'tc_center_img') ),
	          	'HasComments' 			=> $has_post_comments,
	          	'LeftSidebarClass' 		=> $left_sb_class,
	          	'RightSidebarClass' 	=> $right_sb_class,
	          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
	          	'stickyCustomOffset' 	=> apply_filters( 'tc_sticky_custom_offset' , 0 ),
	          	'stickyHeader' 			=> esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ),
	          	'dropdowntoViewport' 	=> esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_resp_dropdown_limit_to_viewport') ),
	          	'timerOnScrollAllBrowsers' => apply_filters( 'tc_timer_on_scroll_for_all_browser' , true), //<= if false, for ie only
              'extLinksStyle'       => esc_attr( TC_utils::$inst->tc_opt( 'tc_ext_link_style' ) ),
              'extLinksTargetExt'   => esc_attr( TC_utils::$inst->tc_opt( 'tc_ext_link_target' ) ),
              'extLinksSkipSelectors'   => apply_filters( 'tc_ext_links_skip_selectors' , array( 'classes' => array('btn') , 'ids' => array() ) ),
              'dropcapEnabled'      => esc_attr( TC_utils::$inst->tc_opt( 'tc_enable_dropcap' ) ),
              'dropcapWhere'      => array( 'post' => esc_attr( TC_utils::$inst->tc_opt( 'tc_post_dropcap' ) ) , 'page' => esc_attr( TC_utils::$inst->tc_opt( 'tc_page_dropcap' ) ) ),
              'dropcapMinWords'     => esc_attr( TC_utils::$inst->tc_opt( 'tc_dropcap_minwords' ) ),
              'dropcapSkipSelectors'  => apply_filters( 'tc_dropcap_skip_selectors' , array( 'tags' => array('IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'), 'classes' => array('btn') , 'id' => array() ) ),
              'imgSmartLoadEnabled' => esc_attr( TC_utils::$inst->tc_opt( 'tc_img_smart_load' ) ),
              'imgSmartLoadOpts'    => apply_filters( 'tc_img_smart_load_options' , array() ),
              'goldenRatio'         => apply_filters( 'tc_grid_golden_ratio' , 1.618 ),
              'gridGoldenRatioLimit' => esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_thumb_height' ) )
	        	),
	        	TC_utils::tc_id()
		    )//end of filter
	     );

	    //fancybox style
	    if ( $tc_fancybox )
	      	wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/assets/js/fancybox/jquery.fancybox-1.3.4.min.css' );

	    //holder.js is loaded when featured pages are enabled AND FP are set to show images
	    $tc_show_featured_pages 	    = esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages' ) );
    		$tc_show_featured_pages_img     = esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages_img' ) );
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
	    if ( apply_filters('tc_load_retinajs', 1 == TC_utils::$inst->tc_opt( 'tc_retina_support' ) ) )
	    	wp_enqueue_script( 'retinajs' ,TC_BASE_URL . 'inc/assets/js/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

	    //Load hammer.js for mobile
	    if ( apply_filters('tc_load_hammerjs', wp_is_mobile() ) )
	    	wp_enqueue_script( 'hammer' ,TC_BASE_URL . 'inc/assets/js/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

		}



		/**
    * Write the font icon in the custom stylesheet at the very beginning
    * hook : tc_user_options_style
    * @package Customizr
    * @since Customizr 3.2.3
    */
		function tc_write_inline_font_icons_css( $_css = null ) {
      $_css               = isset($_css) ? $_css : '';
      return apply_filters( 'tc_write_inline_font_icons',
        $this -> tc_get_inline_font_icons_css() . "\n" . $_css,
        $_css
      );
    }//end of function



    /**
    * @return string of css font icons
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    public function tc_get_inline_font_icons_css() {
      $_path = apply_filters( 'tc_font_icons_path' , TC_BASE_URL . 'inc/assets/css' );
      ob_start();
        ?>
        @font-face {
          font-family: 'genericons';
          src:url('<?php echo $_path ?>/fonts/fonts/genericons-regular-webfont.eot');
          src:url('<?php echo $_path ?>/fonts/fonts/genericons-regular-webfont.eot?#iefix') format('embedded-opentype'),
              url('<?php echo $_path ?>/fonts/fonts/genericons-regular-webfont.woff') format('woff'),
              url('<?php echo $_path ?>/fonts/fonts/genericons-regular-webfont.ttf') format('truetype'),
              url('<?php echo $_path ?>/fonts/fonts/genericons-regular-webfont.svg#genericonsregular') format('svg');
        }
        @font-face {
          font-family: 'entypo';
          src:url('<?php echo $_path ?>/fonts/fonts/entypo.eot');
          src:url('<?php echo $_path ?>/fonts/fonts/entypo.eot?#iefix') format('embedded-opentype'),
          url('<?php echo $_path ?>/fonts/fonts/entypo.woff') format('woff'),
          url('<?php echo $_path ?>/fonts/fonts/entypo.ttf') format('truetype'),
          url('<?php echo $_path ?>/fonts/fonts/entypo.svg#genericonsregular') format('svg');
        }
        <?php
      $_font_css = ob_get_contents();
      if ($_font_css) ob_end_clean();
      return $_font_css;
    }


    /**
    * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
    * hook : tc_user_options_style
    * @package Customizr
    * @since Customizr 2.0.7
    */
    function tc_write_custom_css( $_css = null ) {
      $_css               = isset($_css) ? $_css : '';
      $tc_custom_css      = esc_html( TC_utils::$inst->tc_opt( 'tc_custom_css') );
      if ( ! isset($tc_custom_css) || empty($tc_custom_css) )
        return $_css;

      return apply_filters( 'tc_write_custom_css',
        $_css . "\n" . html_entity_decode( $tc_custom_css ),
        $_css,
        TC_utils::$inst->tc_opt( 'tc_custom_css')
      );
    }//end of function




		/*
		* Writes the livereload script in dev mode (if Grunt watch livereload is enabled)
		*@since v3.2.4
		*/
		function tc_add_livereload_script() {
			if ( TC___::$instance -> tc_is_customizing() )
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



    /*
    * Callback of wp_enqueue_scripts
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function tc_enqueue_gfonts() {
      $_font_pair         = esc_attr( TC_utils::$inst->tc_opt( 'tc_fonts' ) );
      $_all_font_pairs    = TC_init::$instance -> font_pairs;
      if ( ! $this -> tc_is_gfont( $_font_pair , '_g_') )
        return;

      wp_enqueue_style(
        'tc-gfonts',
        sprintf( '//fonts.googleapis.com/css?family=%s', TC_utils::$inst -> tc_get_font( 'single' , $_font_pair ) ),
        array(),
        null,
        'all'
      );
    }



    /**
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function tc_write_fonts_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      $_font_pair         = esc_attr( TC_utils::$inst->tc_opt( 'tc_fonts' ) );
      $_body_font_size    = esc_attr( TC_utils::$inst->tc_opt( 'tc_body_font_size' ) );
      $_font_selectors    = TC_init::$instance -> font_selectors;

      //create the $body and $titles vars
      extract( TC_init::$instance -> font_selectors, EXTR_OVERWRITE );

      if ( ! isset($body) || ! isset($titles) )
        return;

      //adapt the selectors in edit context => add specificity for the mce-editor
      if ( ! is_null( $_context ) ) {
        $titles = ".{$_context} .h1, .{$_context} h2, .{$_context} h3";
        $body   = "body.{$_context}";
      }

      $titles = apply_filters('tc_title_fonts_selectors' , $titles );
      $body   = apply_filters('tc_body_fonts_selectors' , $body );

      if ( 'helvetica_arial' != $_font_pair ) {//check if not default
        $_selector_fonts  = explode( '|', TC_utils::$inst -> tc_get_font( 'single' , $_font_pair ) );
        if ( ! is_array($_selector_fonts) )
          return $_css;

        foreach ($_selector_fonts as $_key => $_raw_font) {
          //create the $_family and $_weight vars
          extract( $this -> tc_get_font_css_prop( $_raw_font , $this -> tc_is_gfont( $_font_pair ) ) );

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

      if ( 14 != $_body_font_size ) {
        $_line_height = round( $_body_font_size * 19 / 14 );
        $_css .= "
          {$body} {
            font-size : {$_body_font_size}px;
            line-height : {$_line_height}px;
          }\n";
        }

      return $_css;
    }//end of fn


    /**
    * Helper to check if the requested font code includes the Google font identifier : _g_
    * @return bool
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function tc_is_gfont($_font , $_gfont_id = null ) {
      $_gfont_id = $_gfont_id ? $_gfont_id : '_g_';
      return false !== strpos( $_font , $_gfont_id );
    }


    /**
    * Helper to extract font-family and weight from a Customizr font option
    * @return array( font-family, weight )
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function tc_get_font_css_prop( $_raw_font , $is_gfont = false ) {
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
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function tc_write_dropcap_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      if ( ! esc_attr( TC_utils::$inst->tc_opt( 'tc_enable_dropcap' ) ) )
        return $_css;

      $_main_color_pair = TC_utils::$inst -> tc_get_skin_color( 'pair' );
      $_color           = $_main_color_pair[0];
      $_shad_color      = $_main_color_pair[1];
      $_pad_right       = false !== strpos( esc_attr( TC_utils::$inst->tc_opt( 'tc_fonts' ) ), 'lobster' ) ? 26 : 8;
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

	}//end of TC_ressources
endif;