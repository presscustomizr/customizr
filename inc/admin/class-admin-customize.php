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
	    function __construct () {
	        self::$instance =& $this;
			//add control class
			add_action ( 'customize_register'				, array( $this , 'tc_add_controls_class' ) ,10,1);
			//control scripts and style
			add_action ( 'customize_controls_enqueue_scripts'	, array( $this , 'tc_customize_controls_js_css' ));
			//add the customizer built with the builder below
			add_action ( 'customize_register'				, array( $this , 'tc_customize_register' ) , 20, 1 );
			//preview scripts
			add_action ( 'customize_preview_init'			, array( $this , 'tc_customize_preview_js' ));
			//Hide donate button
			add_action ( 'wp_ajax_hide_donate'				, array( $this ,  'tc_hide_donate' ) );
			//Grunt Live reload script on DEV mode (TC_DEV constant has to be defined. In wp_config for example)
	        if ( defined('TC_DEV') && true === TC_DEV && apply_filters('tc_live_reload_in_dev_mode' , true ) )
	        	add_action( 'customize_controls_print_scripts' , array( $this , 'tc_add_livereload_script') );
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
				array( 'customize-preview' , 'underscore' ),
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
				array( 'customize-controls' ),
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
			        	'AjaxUrl'          	=> admin_url( 'admin-ajax.php' ),
			        	'TCNonce' 			=> wp_create_nonce( 'tc-customizer-nonce' ),
			        	'HideDonate' 		=> tc__f('__get_option' ,'tc_hide_donate'),
			        )
			    )
	        );

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
	}//end of class
endif;