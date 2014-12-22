<?php
/**
* Customizer actions and filters
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
if ( ! class_exists( 'TC_customize' ) ) :
	class TC_customize {
    static $instance;
    public $control_translations;

    function __construct () {
      self::$instance =& $this;
  		//add control class
  		add_action ( 'customize_register'				                , array( $this , 'tc_add_controls_class' ) ,10,1);
  		//control scripts and style
  		add_action ( 'customize_controls_enqueue_scripts'	      , array( $this , 'tc_customize_controls_js_css' ));
  		//add the customizer built with the builder below
  		add_action ( 'customize_register'				                , array( $this , 'tc_customize_register' ) , 20, 1 );
  		//preview scripts
  		add_action ( 'customize_preview_init'			              , array( $this , 'tc_customize_preview_js' ));
  		//Hide donate button
  		add_action ( 'wp_ajax_hide_donate'				              , array( $this , 'tc_hide_donate' ) );
  		//Grunt Live reload script on DEV mode (TC_DEV constant has to be defined. In wp_config for example)
      if ( defined('TC_DEV') && true === TC_DEV && apply_filters('tc_live_reload_in_dev_mode' , true ) )
      	add_action( 'customize_controls_print_scripts'        , array( $this , 'tc_add_livereload_script') );

      add_action ( 'customize_controls_print_footer_scripts'  , array( $this, 'tc_print_js_templates' ) );
    }



		/**
		* Adds controls to customizer
		* @package Customizr
		* @since Customizr 1.0
		*/
		function tc_add_controls_class( $type) {
			locate_template( 'inc/admin/class-controls.php' , $load = true, $require_once = true );
		}



		/**
		* Generates customizer sections, settings and controls
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_customize_register( $wp_customize) {
			return $this -> tc_customize_factory ( $wp_customize , $args = $this -> tc_customize_arguments(), $setup = TC_utils_settings_map::$instance -> tc_customizer_map() );
		}




		/**
		 * Defines authorized arguments for panels, sections, settings and controls
		 * @package Customizr
		 * @since Customizr 3.0
		 */
		function tc_customize_arguments() {
			$args = array(
					'panels' => array(
								'title' ,
								'description',
								'priority' ,
								'theme_supports',
								'capability'
					),
					'sections' => array(
								'title' ,
								'priority' ,
								'description',
								'panel',
								'theme_supports'
					),
					'settings' => array(
								'default'			=>	null,
								'capability'		=>	'manage_options' ,
								'setting_type'		=>	'option' ,
								'sanitize_callback'	=>	null,
								'transport'			=>	null
					),
					'controls' => array(
								'title' ,
								'label' ,
								'section' ,
								'settings',
								'type' ,
								'choices' ,
								'priority' ,
								'sanitize_callback' ,
								'notice' ,
								'buttontext' ,//button specific
								'link' ,//button specific
								'step' ,//number specific
								'min' ,//number specific
								'range-input' ,
								'max',
								'cssid',
								'slider_default',
								'active_callback',
								'content_after',
								'content_before',
								'icon'
					)
			);
			return apply_filters( 'tc_customizer_arguments', $args );
		}





		/**
		 * Generates customizer
		 * @package Customizr
		 * @since Customizr 3.0
		 */
		function tc_customize_factory ( $wp_customize , $args, $setup ) {
			global $wp_version;
			//add panels if current WP version >= 4.0
			if ( isset( $setup['add_panel']) && version_compare( $wp_version, '4.0', '>=' ) ) {
				foreach ( $setup['add_panel'] as $p_key => $p_options ) {
					//declares the clean section option array
					$panel_options = array();
					//checks authorized panel args
					foreach( $args['panels'] as $p_set) {
						$panel_options[$p_set] = isset( $p_options[$p_set]) ?  $p_options[$p_set] : null;
					}
					$wp_customize -> add_panel( $p_key, $panel_options );
				}
			}

			//remove sections
			if ( isset( $setup['remove_section'])) {
				foreach ( $setup['remove_section'] as $section) {
					$wp_customize	-> remove_section( $section);
				}
			}

			//add sections
			if ( isset( $setup['add_section'])) {
				foreach ( $setup['add_section'] as  $key => $options) {
					//generate section array
					$option_section = array();

					foreach( $args['sections'] as $sec) {
						$option_section[$sec] = isset( $options[$sec]) ?  $options[$sec] : null;
					}

					//add section
					$wp_customize	-> add_section( $key,$option_section);
				}//end foreach
			}//end if

			//get_settings
			if ( isset( $setup['get_setting'])) {
				foreach ( $setup['get_setting'] as $setting) {
					$wp_customize	-> get_setting( $setting )->transport = 'postMessage';
				}
			}

			//add settings and controls
			if ( isset( $setup['add_setting_control'])) {

				foreach ( $setup['add_setting_control'] as $key => $options) {
					//isolates the option name for the setting's filter
					$f_option_name = 'setting';
					$f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
		            if ( isset( $match[1][0] ) ) {$f_option_name = $match[1][0];}

					//declares settings array
					$option_settings = array();
					foreach( $args['settings'] as $set => $set_value) {
						if ( $set == 'setting_type' ) {
							$option_settings['type'] = isset( $options['setting_type']) ?  $options['setting_type'] : $args['settings'][$set];
							$option_settings['type'] = apply_filters( "{$f_option_name}_customizer_set", $option_settings['type'] , $set );
						}
						else {
							$option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
							$option_settings[$set] = apply_filters( "{$f_option_name}_customizer_set" , $option_settings[$set] , $set );
						}
					}

					//add setting
					$wp_customize	-> add_setting( $key, $option_settings );

					//generate controls array
					$option_controls = array();
					foreach( $args['controls'] as $con) {
						$option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
					}

					//add control with a class instanciation if not default
					if( ! isset( $options['control']) )
						$wp_customize	-> add_control( $key,$option_controls );
					else
						$wp_customize	-> add_control( new $options['control']( $wp_customize, $key, $option_controls ));

				}//end for each
			}//end if isset
		}//end of customize generator function





		/**
		 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 * @package Customizr
		 * @since Customizr 1.0
		 */

		function tc_customize_preview_js() {
			wp_enqueue_script(
				'tc-customizer-preview' ,
				sprintf('%1$s/inc/admin/js/theme-customizer-preview%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-preview'),
				CUSTOMIZR_VER ,
				true
			);

			//localizes
			wp_localize_script(
		        'tc-customizer-preview',
		        'TCPreviewParams',
		        apply_filters('tc_js_customizer_control_params' ,
			        array(
			        	'themeFolder' 		=> get_template_directory_uri(),
                'fontPairs'       => TC_utils::$instance -> tc_get_font( 'list' ),
                'fontSelectors'   => TC_init::$instance -> font_selectors
			        )
			       )
	        );
		}



		/**
		 * Add script to controls
		 * Dependency : customize-controls located in wp-includes/script-loader.php
		 * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
		 * @package Customizr
		 * @since Customizr 3.1.0
		 */
		function tc_customize_controls_js_css() {

			wp_enqueue_style(
				'tc-customizer-controls-style',
				sprintf('%1$s/inc/admin/css/theme-customizer-control%2$s.css' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-controls' ),
				CUSTOMIZR_VER,
				$media = 'all'
			);
			wp_enqueue_script(
				'tc-customizer-controls',
				sprintf('%1$s/inc/admin/js/theme-customizer-control%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-controls' , 'underscore'),
				CUSTOMIZR_VER ,
				true
			);

			//select2 stylesheet
			//overriden by some specific style in theme-customzer-control.css
			wp_enqueue_style(
				'tc-select2-css',
				sprintf('%1$s/inc/admin/js/lib/select2.min.css', get_template_directory_uri() ),
				array( 'customize-controls' ),
				CUSTOMIZR_VER,
				$media = 'all'
			);


			//gets the featured pages id from init
			$fp_ids				= apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids);

			//declares the common fp control fields and the dynamic arrays
			$fp_controls 			= array(
				'tc_theme_options[tc_show_featured_pages_img]',
				'tc_theme_options[tc_featured_page_button_text]'
			);
			$page_dropdowns 		= array();
			$text_fields			= array();

			//adds filtered page dropdown fields
			foreach ( $fp_ids as $id ) {
				$page_dropdowns[] 	= 'tc_theme_options[tc_featured_page_'. $id.']';
				$text_fields[]		= 'tc_theme_options[tc_featured_text_'. $id.']';
			}

			//localizes
			wp_localize_script(
		        'tc-customizer-controls',
		        'TCControlParams',
		        apply_filters('tc_js_customizer_control_params' ,
			        array(
			        	'FPControls' => array_merge( $fp_controls , $page_dropdowns , $text_fields ),
			        	'AjaxUrl'       => admin_url( 'admin-ajax.php' ),
			        	'TCNonce' 			=> wp_create_nonce( 'tc-customizer-nonce' ),
                'themeName'     => TC___::$theme_name,
                'HideDonate'    => tc__f('__get_option' ,'tc_hide_donate'),
                'ShowCTA'       => ( true == tc__f('__get_option' ,'tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false
			        )
			    )
	        );

		}



		/**
		* Update donate options handled in ajax
    * callback of wp_ajax_hide_donate*
		* @package Customizr
		* @since Customizr 3.1.14
		*/
    function tc_hide_donate() {
    	check_ajax_referer( 'tc-customizer-nonce', 'TCnonce' );
    	$options = get_option('tc_theme_options');
    	$options['tc_hide_donate'] = true;
    	update_option( 'tc_theme_options', $options );
      set_transient( 'tc_cta', 'cta_waiting' , 60*60*24 );
    }



	  /*
		* Writes the livereload script in dev mode (Grunt watch livereload enabled)
		*@since v3.2.4
		*/
		function tc_add_livereload_script() {
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
    * Renders the underscore templates for the call to actions
    * callback of 'customize_controls_print_footer_scripts'
    *@since v3.2.9
    */
    function tc_print_js_templates() {
      ?>
      <script type="text/template" id="donate_template">
        <div id="tc-donate-customizer">
          <span class="tc-close-request button">X</span>
            <?php
              printf('<h3>%1$s <a href="https://twitter.com/nicguillaume" target="_blank">Nicolas</a>%2$s :).</h3>',
                __( "Hi! This is" , 'customizr' ),
                __( ", developer of the Customizr theme", 'customizr' )
              );
              printf('<span class="tc-notice">%1$s</span>',
                __( "I'm doing my best to make Customizr the perfect free theme for you. If you think it helped you in any way to build a better web presence, please support it's continued development with a donation of $20, $50, ..." , 'customizr' )
              );
              printf('<a class="tc-donate-link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow">
                <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="%1$s"></a>',
                __( "Make a donation for Customizr" , 'customizr' )
              );
              printf('<div class="donate-alert"><p class="tc-notice">%1$s</p><span class="tc-hide-donate button">%2$s</span><span class="tc-cancel-hide-donate button">%3$s</span></div>',
                __( "Once clicked the 'Hide forever' button, this donation block will not be displayed anymore.<br/>Either you are using Customizr for personal or business purposes, any kind of sponsorship will be appreciated to support this free theme.<br/><strong>Already donator? Thanks, you rock!<br/><br/> Live long and prosper with Customizr!</strong>" , 'customizr'),
                __( "Hide forever" , 'customizr' ),
                __( "Let me think twice" , 'customizr' )
              );
            ?>
        </div>
      </script>
      <script type="text/template" id="main_cta">
        <div class="tc-cta tc-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Need more customizations options and premium support ?" , 'customizr' ),
              sprintf('%sextension/customizr-pro/', TC_WEBSITE ),
              __( "Discover Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="wfc_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
        <hr/>
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Need more control on your fonts ? Style any text in live preview ( size, color, font family, effect, ...) with the Customizr Pro theme." , 'customizr' ),
              sprintf('%sextension/customizr-pro/', TC_WEBSITE ),
              __( "Discover Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
       <script type="text/template" id="fpu_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
        <hr/>
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Add unlimited featured pages with Customizr Pro" , 'customizr' ),
              sprintf('%sextension/customizr-pro/', TC_WEBSITE ),
              __( "Discover Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <?php
    }

	}//end of class
endif;