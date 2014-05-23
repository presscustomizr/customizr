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

class TC_customize {

    //Access any method or var of the class with classname::$instance -> var or method():
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
		return $this -> tc_customize_factory ( $wp_customize , $args = $this -> tc_customize_arguments(), $setup = TC_utils::$instance -> tc_customizer_map() );
	}




	/**
	 * Defines arguments for sections, settings and controls
	 * @package Customizr
	 * @since Customizr 3.0 
	 */
	function tc_customize_arguments() {
		$args = array(
				'sections' => array(
							'title' ,
							'priority' ,
							'description'
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
							'settings' ,
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
						$option_settings['type'] = apply_filters( $f_option_name .'_customizer_set', $option_settings['type'] , $set );
					}
					else {
						$option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
						$option_settings[$set] = apply_filters( $f_option_name .'_customizer_set' , $option_settings[$set] , $set );
					}
				}

				//add setting
				$wp_customize	-> add_setting( $key, $option_settings );
			
				//generate controls array
				$option_controls = array();
				foreach( $args['controls'] as $con) {
					$option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
				}

				//add control with a dynamic class instanciation if not default
				if(!isset( $options['control'])) {
						$wp_customize	-> add_control( $key,$option_controls );
				}
				else {
						$wp_customize	-> add_control( new $options['control']( $wp_customize, $key, $option_controls ));
				}

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
			get_template_directory_uri() . '/inc/admin/js/theme-customizer-preview.js' ,
			array( 'customize-preview' ),
			'20120827' ,
			true );
	}



	/**
	 * Add script to controls
	 * Dependency : customize-controls located in wp-includes/script-loader.php
	 * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
	 * @package Customizr
	 * @since Customizr 3.1.0
	 */
	function tc_customize_controls_js_css() {

		wp_register_style( 
			'tc-customizer-controls-style' ,
			get_template_directory_uri() . '/inc/admin/css/theme-customizer-control.css' ,
			array( 'customize-controls' ),
			null,
			$media = 'all'
		);
		wp_enqueue_style('tc-customizer-controls-style');
		wp_enqueue_script( 
			'tc-customizer-controls' ,
			get_template_directory_uri() . '/inc/admin/js/theme-customizer-control.js' ,
			array( 'customize-controls' ),
			CUSTOMIZR_VER ,
			true
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

		//adds some nice google fonts to the customizer
        wp_enqueue_style(
          'customizer-google-fonts', 
          $this-> tc_customizer_gfonts_url(), 
          array(), 
          null 
        );
	}




	/**
	* Builds Google Fonts url
	* @package Customizr
	* @since Customizr 3.1.1
	*/
	function tc_customizer_gfonts_url() {
      //declares the google font vars
      $fonts_url          = '';
      $font_families      = apply_filters( 'tc_customizer_google_fonts' , array('Raleway') );

      $query_args         = array(
          'family' => implode( '|', $font_families ),
          //'subset' => urlencode( 'latin,latin-ext' ),
      );

      $fonts_url          = add_query_arg( $query_args, "//fonts.googleapis.com/css" );

      return $fonts_url;
    }



    /**
	* Update donate options handled in ajax
	* @package Customizr
	* @since Customizr 3.1.14
	*/
    function tc_hide_donate() {
    	check_ajax_referer( 'tc-customizer-nonce', 'TCnonce' );
    	$options = get_option('tc_theme_options');
    	$options['tc_hide_donate'] = true;
    	update_option( 'tc_theme_options', $options );
    }


}//end of class