<?php
/**
* Customizer actions and filters
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
if ( ! class_exists( 'CZR_customize' ) ) :
	class CZR_customize {
    static $instance;
    public $control_translations;

    function __construct () {
      global $wp_version;
      //check if WP version >= 3.4 to include customizer functions
      //Shall we really keep this ?
      if ( ! version_compare( $wp_version, '3.4' , '>=' ) ) {
        add_action( 'admin_menu'                    , array( $this , 'czr_fn_add_fallback_page' ));
        return;
      }

      self::$instance =& $this;
  		//add control class
  		add_action ( 'customize_register'				                , array( $this , 'czr_fn_augment_customizer' ),10,1);

  		//add grid/post list buttons in the control views
  		add_action( '__before_setting_control'                  , array( $this , 'czr_fn_render_grid_control_link') );

  		//control scripts and style
  		add_action ( 'customize_controls_enqueue_scripts'	      , array( $this , 'czr_fn_customize_controls_js_css' ));
  		//add the customizer built with the builder below
  		add_action ( 'customize_register'				                , array( $this , 'czr_fn_customize_register' ), 20, 1 );

      //modify some WP built-in settings / controls / sections
      add_action ( 'customize_register'                       , array( $this , 'czr_fn_alter_wp_customizer_settings' ), 30, 1 );

      //preview scripts
      //set with priority 20 to be fired after tc_customize_store_db_opt in CZR_utils
  		add_action ( 'customize_preview_init'			              , array( $this , 'czr_fn_customize_preview_js' ), 20 );
  		//Hide donate button
  		add_action ( 'wp_ajax_hide_donate'				              , array( $this , 'czr_fn_hide_donate' ) );

      add_action ( 'customize_controls_print_footer_scripts'  , array( $this, 'czr_fn_print_js_templates' ) );
    }



    /*
    * Since the WP_Customize_Manager::$controls and $settings are protected properties, one way to alter them is to use the get_setting and get_control methods
    * Another way is to remove the control and add it back as an instance of a custom class and with new properties
    * and set new property values
    * hook : tc_customize_register:30
    * @return void()
    */
    function czr_fn_alter_wp_customizer_settings( $wp_customize ) {
      //CHANGE BLOGNAME AND BLOGDESCRIPTION TRANSPORT
      if ( is_object( $wp_customize -> get_setting( 'blogname' ) ) ) {
        $wp_customize -> get_setting( 'blogname' )->transport = 'postMessage';
      }
      if ( is_object( $wp_customize -> get_setting( 'blogdescription' ) ) ) {
        $wp_customize -> get_setting( 'blogdescription' )->transport = 'postMessage';
      }


      //IF WP VERSION >= 4.3 AND SITE_ICON SETTING EXISTS
      //=> REMOVE CUSTOMIZR FAV ICON CONTROL
      //=> CHANGE SITE ICON DEFAULT WP SECTION TO CUSTOMIZR LOGO SECTION
      global $wp_version;
      if ( version_compare( $wp_version, '4.3', '>=' ) && is_object( $wp_customize -> get_control( 'site_icon' ) ) ) {
        $tc_option_group = CZR___::$tc_option_group;
        $wp_customize -> remove_control( "{$tc_option_group}[tc_fav_upload]" );
        //note : the setting is kept because used in the customizer js api to handle the transition between Customizr favicon to WP site icon.
        $wp_customize -> get_control( 'site_icon' )->section = 'logo_sec';

        //add a favicon title after the logo upload
        add_action( '__after_setting_control' , array( $this , 'czr_fn_add_favicon_title') );
      }//end ALTER SITE ICON


      //CHANGE MENUS PROPERTIES
      $locations    = get_registered_nav_menus();
      $menus        = wp_get_nav_menus();
      $choices      = array( '' => __( '&mdash; Select &mdash;', 'customizr' ) );
      foreach ( $menus as $menu ) {
        $choices[ $menu->term_id ] = wp_html_excerpt( $menu->name, 40, '&hellip;' );
      }
      $_priorities  = array(
        'main' => 10,
        'secondary' => 20
      );

      //WP only adds the menu(s) settings and controls if the user has created at least one menu.
      //1) if no menus yet, we still want to display the menu picker + add a notice with a link to the admin menu creation panel
      //=> add_setting and add_control for each menu location. Check if they are set first by security
      //2) if user has already created a menu, the settings are already created, we just need to update the controls.
      $_priority = 0;
      //assign new priorities to the menus controls
      foreach ( $locations as $location => $description ) {
        $menu_setting_id = "nav_menu_locations[{$location}]";

        //create the settings if they don't exist
        //=> in the condition, make sure that the setting has really not been created yet (maybe over secured)
        if ( ! $menus && ! is_object( $wp_customize->get_setting($menu_setting_id ) ) ) {
          $wp_customize -> add_setting( $menu_setting_id, array(
            'sanitize_callback' => 'absint',
            'theme_supports'    => 'menus',
          ) );
        }

        //remove the controls if they exists
        if ( is_object( $wp_customize->get_control( $menu_setting_id ) ) ) {
          $wp_customize -> remove_control( $menu_setting_id );
        }

        //replace the controls by our custom controls
        $_control_properties = array(
          'label'   => $description,
          'section' => 'nav',
          'title'   => "main" == $location ? __( 'Assign menus to locations' , 'customizr') : false,
          'type'    => 'select',
          'choices' => $choices,
          'priority' => isset($_priorities[$location]) ? $_priorities[$location] : $_priority
        );

        //add a notice property if no menu created yet.
        if ( ! $menus ) {
          //adapt the nav section description for v4.3 (menu in the customizer from now on)
          $_create_menu_link =  version_compare( $GLOBALS['wp_version'], '4.3', '<' ) ? admin_url('nav-menus.php') : "javascript:wp.customize.section('nav').container.find('.customize-section-back').trigger('click'); wp.customize.panel('nav_menus').focus();";
          $_control_properties['notice'] = sprintf( __("You haven't created any menu yet. %s or check the %s to learn more about menus.", "customizr"),
            sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', $_create_menu_link, __("Create a new menu now" , "customizr") ),
            sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('codex.wordpress.org/WordPress_Menu_User_Guide'),  __("WordPress documentation" , "customizr") )
          );
        }

        $wp_customize -> add_control( new CZR_controls( $wp_customize, $menu_setting_id, $_control_properties ) );

        $_priority = $_priority + 10;
      }//foreach

      //REMOVE CUSTOM CSS ADDED IN 4.7 => IT WILL REPLACE THE CUSTOMIZR BUILT-IN ONE SOON
      //But the migration is not coded yet
      $custom_css_setting_id = sprintf( 'custom_css[%s]', get_stylesheet() );
      if ( is_object( $wp_customize -> get_setting( $custom_css_setting_id ) ) ) {
        $wp_customize -> remove_setting( $custom_css_setting_id );
      }
      if ( is_object( $wp_customize -> get_control( 'custom_css' ) ) ) {
        $wp_customize -> remove_control( 'custom_css' );
      }
      if ( is_object( $wp_customize -> get_section( 'custom_css' ) ) ) {
        $wp_customize -> remove_section( 'custom_css' );
      }

    }


    /*
    * hook : '__after_setting_control' (declared in class-tc-controls-settings.php)
    * Display a title for the favicon control, after the logo
    */
    function czr_fn_add_favicon_title($set_id) {
      if ( false !== strpos( $set_id, 'tc_sticky_logo_upload' ) )
        printf( '<h3 class="tc-customizr-title">%s</h3>', __( 'SITE ICON' , 'customizr') );
    }

		/**
		* Augments wp customize controls and settings classes
		* @package Customizr
		* @since Customizr 1.0
		*/
		function czr_fn_augment_customizer( $manager ) {
      //loads custom settings and controls classes for the Customizr theme
      //- CZR_Customize_Setting extends WP_Customize_Setting => to override the value() method
      //- CZR_controls extends WP_Customize_Control => overrides the render() method
      //- CZR_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control => introduced in v3.4.19, uses a js template to render the control
      //- CZR_Customize_Upload_Control extends WP_Customize_Control => old upload control used until v3.4.18, still used if current version of WP is < 4.3
      //- CZR_Customize_Multipicker_Control extends CZR_controls => used for multiple cat picker for example
      //- CZR_Customize_Multipicker_Categories_Control extends CZR_Customize_Multipicker_Control => extends the multipicker
      //- CZR_Walker_CategoryDropdown_Multipicker extends Walker_CategoryDropdown => needed for the multipicker to allow more than one "selected" attribute
      //locate_template( 'inc/admin/class-tc-controls-settings.php' , $load = true, $require_once = true );

      //Registered types are eligible to be rendered via JS and created dynamically.
      if ( class_exists('CZR_Customize_Cropped_Image_Control') )
        $manager -> register_control_type( 'CZR_Customize_Cropped_Image_Control' );
		}



		/**
		* Generates customizer sections, settings and controls
		* @package Customizr
		* @since Customizr 3.0
		*/
		function czr_fn_customize_register( $wp_customize) {
			return $this -> czr_fn_customize_factory (
        $wp_customize,
        $this -> czr_fn_customize_arguments(),
        CZR_utils_settings_map::$instance -> czr_fn_get_customizer_map()
      );
		}




		/**
		 * Defines authorized arguments for panels, sections, settings and controls
		 * @package Customizr
		 * @since Customizr 3.0
		 */
		function czr_fn_customize_arguments() {
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
								'sanitize_js_callback'	=>	null,
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
								'sanitize_callback',
								'sanitize_js_callback',
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
								'icon',
								'width',
								'height',
								'flex_width',
                'flex_height',
                'dst_width',
                'dst_height'
					)
			);
			return apply_filters( 'tc_customizer_arguments', $args );
		}





		/**
		 * Generates customizer
		 * @package Customizr
		 * @since Customizr 3.0
		 */
		function czr_fn_customize_factory ( $wp_customize , $args, $setup ) {
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

			//add settings and controls
			if ( isset( $setup['add_setting_control'])) {

				foreach ( $setup['add_setting_control'] as $key => $options) {
					//isolates the option name for the setting's filter
					$f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
		      $f_option_name = isset( $match[1][0] )  ? $match[1][0] : 'setting';

          $tc_option_group = CZR___::$tc_option_group;
          //build option name
          //When do we add a prefix ?
          //all customizr theme options start by "tc_" by convention
          //=> footer customizer addon starts by fc_
          //=> grid customizer addon starts by gc_
          //When do we add a prefix ?
          $add_prefix = false;
          if ( CZR_utils::$inst -> czr_fn_is_customizr_option( $key ) )
            $add_prefix = true;
          $_opt_name = $add_prefix ? "{$tc_option_group}[{$key}]" : $key;

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
          if ( class_exists('CZR_Customize_Setting') )
            $wp_customize -> add_setting( new CZR_Customize_Setting ( $wp_customize, $_opt_name, $option_settings ) );
          else
            $wp_customize -> add_setting( $_opt_name, $option_settings );

					//generate controls array
					$option_controls = array();
					foreach( $args['controls'] as $con) {
						$option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
					}

					//add control with a class instanciation if not default
					if( ! isset( $options['control']) )
						$wp_customize	-> add_control( $_opt_name, $option_controls );
					else
						$wp_customize	-> add_control( new $options['control']( $wp_customize, $_opt_name, $option_controls ));

				}//end for each
			}//end if isset
		}//end of customize generator function


    /**
    * hook __before_setting_control (declared in class-tc-controls-settings.php)
    * @echo clickable text
    */
    function czr_fn_render_grid_control_link( $set_id ) {
      if ( false !== strpos( $set_id, 'tc_post_list_show_thumb' ) )
        printf('<span class="tc-grid-toggle-controls" title="%1$s">%1$s</span>' , __('More grid design options' , 'customizr'));
    }


		/**
		 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 * @package Customizr
		 * @since Customizr 1.0
		 */

		function czr_fn_customize_preview_js() {
			global $wp_version;

			wp_enqueue_script(
				'tc-customizer-preview' ,
				sprintf('%1$s/inc/admin/js/theme-customizer-preview%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-preview', 'underscore'),
				( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUSTOMIZR_VER,
				true
			);

			//localizes
			wp_localize_script(
		        'tc-customizer-preview',
		        'TCPreviewParams',
		        apply_filters('tc_js_customizer_preview_params' ,
			        array(
			        	'themeFolder' 		=> get_template_directory_uri(),
                //can be hacked to override the preview params when a custom skin is used
                //array( 'skinName' => 'custom-skin-#40542.css', 'fullPath' => 'http://....' )
                'customSkin'      => apply_filters( 'tc_custom_skin_preview_params' , array( 'skinName' => '', 'fullPath' => '' ) ),
                'fontPairs'       => CZR_utils::$inst -> czr_fn_get_font( 'list' ),
                'fontSelectors'   => CZR_init::$instance -> font_selectors,
                //patch for old wp versions which don't trigger preview-ready signal => since WP 4.1
                'preview_ready_event_exists'   => version_compare( $wp_version, '4.1' , '>=' )
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
		function czr_fn_customize_controls_js_css() {

			wp_enqueue_style(
				'tc-customizer-controls-style',
				sprintf('%1$s/inc/admin/css/theme-customizer-control%2$s.css' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-controls' ),
				( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUSTOMIZR_VER,
				$media = 'all'
			);
			wp_enqueue_script(
				'tc-customizer-controls',
				sprintf('%1$s/inc/admin/js/theme-customizer-control%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'customize-controls' , 'underscore'),
				( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUSTOMIZR_VER,
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
			$fp_ids				= apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);

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
            'themeName'     => CZR___::$theme_name,
            'HideDonate'    => $this -> czr_fn_get_hide_donate_status(),
            'ShowCTA'       => ( true == CZR_utils::$inst->czr_fn_opt('tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false,
            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'    => array(
              'postSliderNote' => __( "This option generates a home page slider based on your last posts, starting from the most recent or the featured (sticky) post(s) if any.", "customizr" ),
              'faviconNote' => __( "Your favicon is currently handled with an old method and will not be properly displayed on all devices. You might consider to re-upload your favicon with the new control below." , 'customizr')
            ),
            'isThemeSwitchOn' => isset( $_GET['theme'])
	        )
	      )
      );

		}


    /**
    * Donate visibility
    * callback of wp_ajax_hide_donate*
    * @package Customizr
    * @since Customizr 3.1.14
    */
    function czr_fn_get_hide_donate_status() {
      //is customizr the current active theme?
      //=> check the existence of is_theme_active for backward compatibility (may be useless because introduced in 3.4... )
      $_is_customizr_active = method_exists( $GLOBALS['wp_customize'], 'is_theme_active' ) && $GLOBALS['wp_customize'] -> is_theme_active();
      $_options = get_option('tc_theme_options');
      $_user_started_customize = false !== $_options || ! empty( $_options );

      //shall we hide donate ?
      return ! $_user_started_customize || ! $_is_customizr_active || CZR_utils::$inst->czr_fn_opt('tc_hide_donate');
    }



		/**
		* Update donate options handled in ajax
    * callback of wp_ajax_hide_donate*
		* @package Customizr
		* @since Customizr 3.1.14
		*/
    function czr_fn_hide_donate() {
    	check_ajax_referer( 'tc-customizer-nonce', 'TCnonce' );
    	$options = get_option('tc_theme_options');
    	$options['tc_hide_donate'] = true;
    	update_option( 'tc_theme_options', $options );
      set_transient( 'tc_cta', 'cta_waiting' , 60*60*24 );
      wp_die();
    }





    /*
    * Renders the underscore templates for the call to actions
    * callback of 'customize_controls_print_footer_scripts'
    *@since v3.2.9
    */
    function czr_fn_print_js_templates() {
      ?>
      <script type="text/template" id="donate_template">
        <div id="tc-donate-customizer">
          <a href="#" class="tc-close-request button" title="<?php _e('dismiss' , 'customizr'); ?>">X</a>
            <?php
              printf('<h3>%1$s <a href="%2$s" target="_blank">Nicolas</a>%3$s :).</h3>',
                __( "Hi! This is" , 'customizr' ),
                esc_url('twitter.com/presscustomizr'),
                __( ", developer of the Customizr theme", 'customizr' )
              );
              printf('<span class="tc-notice">%1$s</span>',
                __( "I'm doing my best to make Customizr the perfect free theme for you. If you think it helped you in any way to build a better web presence, please support it's continued development with a donation of $20, $50, ..." , 'customizr' )
              );
              printf('<a class="tc-donate-link" href="%1$s" target="_blank" rel="nofollow"><img src="%2$s" alt="%3$s"></a>',
                esc_url('paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU'),
                esc_url('paypal.com/en_US/i/btn/btn_donate_LG.gif'),
                __( "Make a donation for Customizr" , 'customizr' )
              );
              printf('<div class="donate-alert"><p class="tc-notice">%1$s</p><span class="tc-hide-donate button">%2$s</span><span class="tc-cancel-hide-donate button">%3$s</span></div>',
                __( "Once clicked the 'Hide forever' button, this donation block will not be displayed anymore.<br/>Either you are using Customizr for personal or business purposes, any kind of sponsorship will be appreciated to support this free theme.<br/><strong>Already donator? Thanks, you rock!<br/><br/> Live long and prosper with Customizr!</strong>" , 'customizr'),
                __( "Hide forever" , 'customizr' ),
                sprintf( '%s <span style="font-size:20px">%s</span>', __( "Let me think twice" , 'customizr' ), convert_smilies( ':roll:') )
              );
            ?>
        </div>
      </script>
      <script type="text/template" id="main_cta">
        <div class="tc-cta tc-cta-wrap">
          <?php
            printf('<a class="tc-cta-btn" href="%1$s" title="%2$s" target="_blank">%2$s &raquo;</a>',
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="wfc_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Need more control on your fonts ? Style any text in live preview ( size, color, font family, effect, ...) with Customizr Pro." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="fpu_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Add unlimited featured pages with Customizr Pro." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

      <script type="text/template" id="gc_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s %2$s</span><a class="tc-cta-btn" href="%3$s" title="%4$s" target="_blank">%4$s &raquo;</a>',
              __( "Rediscover the beauty of your blog posts and increase your visitors engagement with the Grid Customizer." , 'customizr' ),
               sprintf('<a href="%1$s" class="tc-notice-inline-link" title="%2$s" target="_blank">%2$s<span class="tc-notice-ext-icon dashicons dashicons-external"></span></a>' , esc_url('demo.presscustomizr.com/?design=demo_grid_customizer'), __("Try it in the demo" , "customizr" )
              ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

       <script type="text/template" id="mc_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s %2$s</span><a class="tc-cta-btn" href="%3$s" title="%4$s" target="_blank">%4$s &raquo;</a>',
              __( "Add creative and engaging reveal animations to your side menu." , 'customizr' ),
              sprintf('<a href="%1$s" class="tc-notice-inline-link" title="%2$s" target="_blank">%2$s<span class="tc-notice-ext-icon dashicons dashicons-external"></span></a>' , esc_url('demo.presscustomizr.com/?design=nav'), __("Side menu animation demo" , "customizr" )
              ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

      <script type="text/template" id="footer_cta">
        <div class="tc-cta tc-in-control-cta-wrap">
          <?php
            printf('<span class="tc-notice">%1$s</span><a class="tc-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Customize your footer credits with Customizr Pro." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="rate-czr">
        <?php
        $_is_pro = 'customizr-pro' == CZR___::$theme_name;
          printf( '<span class="tc-rate-link">%1$s %2$s, <br/>%3$s <a href="%4$s" title="%5$s" class="tc-stars" target="_blank">%6$s</a> %7$s</span>',
            __( 'If you like' , 'customizr' ),
            ! $_is_pro ? __( 'the Customizr theme' , 'customizr') : __( 'the Customizr pro theme' , 'customizr'),
            __( 'we would love to receive a' , 'customizr' ),
            ! $_is_pro ? 'https://' . 'wordpress.org/support/view/theme-reviews/customizr?filter=5' : sprintf('%scustomizr-pro/#comments', CZR_WEBSITE ),
            __( 'Review the Customizr theme' , 'customizr' ),
            '&#9733;&#9733;&#9733;&#9733;&#9733;',
            __( 'rating. Thanks :) !' , 'customizr')
          );
        ?>
      </script>
      <?php
    }



    /**
    * Add fallback admin page.
    * @package Customizr
    * @since Customizr 1.1
    */
    function czr_fn_add_fallback_page() {
        $theme_page = add_theme_page(
            __( 'Upgrade WP' , 'customizr' ),   // Name of page
            __( 'Upgrade WP' , 'customizr' ),   // Label in menu
            'edit_theme_options' ,          // Capability required
            'upgrade_wp.php' ,             // Menu slug, used to uniquely identify the page
            array( $this , 'czr_fn_fallback_admin_page' )         //function to be called to output the content of this page
        );
    }




    /**
    * Render fallback admin page.
    * @package Customizr
    * @since Customizr 1.1
    */
    function czr_fn_fallback_admin_page() {
      ?>
        <div class="wrap upgrade_wordpress">
          <div id="icon-options-general" class="icon32"><br></div>
          <h2><?php _e( 'This theme requires WordPress 3.4+' , 'customizr' ) ?> </h2>
          <br />
          <p style="text-align:center">
            <a style="padding: 8px" class="button-primary" href="<?php echo admin_url().'update-core.php' ?>" title="<?php _e( 'Upgrade Wordpress Now' , 'customizr' ) ?>">
            <?php _e( 'Upgrade Wordpress Now' , 'customizr' ) ?></a>
            <br /><br />
          <img src="<?php echo TC_BASE_URL . 'screenshot.png' ?>" alt="Customizr" />
          </p>
        </div>
      <?php
    }

	}//end of class
endif;




/***************************************************
* AUGMENTS WP CUSTOMIZE SETTINGS
***************************************************/
if ( ! class_exists( 'CZR_Customize_Setting') ) :
  class CZR_Customize_Setting extends WP_Customize_Setting {
    /**
     * Fetch the value of the setting.
     *
     * @since 3.4.0
     *
     * @return mixed The value.
     */
    public function value() {
        // Get the callback that corresponds to the setting type.
        switch( $this->type ) {
          case 'theme_mod' :
            $function = 'get_theme_mod';
            break;
          case 'option' :
            $function = 'get_option';
            break;
          default :

            /**
             * Filter a Customize setting value not handled as a theme_mod or option.
             *
             * The dynamic portion of the hook name, `$this->id_date['base']`, refers to
             * the base slug of the setting name.
             *
             * For settings handled as theme_mods or options, see those corresponding
             * functions for available hooks.
             *
             * @since 3.4.0
             *
             * @param mixed $default The setting default value. Default empty.
             */
            return apply_filters( 'customize_value_' . $this->id_data[ 'base' ], $this->default );
        }

        // Handle non-array value
        if ( empty( $this->id_data[ 'keys' ] ) )
          return $function( $this->id_data[ 'base' ], $this->default );

        // Handle array-based value
        $values = $function( $this->id_data[ 'base' ] );

        //Ctx future backward compat
        $_maybe_array = $this->multidimensional_get( $values, $this->id_data[ 'keys' ], $this->default );
        if ( ! is_array( $_maybe_array ) )
          return $_maybe_array;
        if ( isset($_maybe_array['all_ctx']) )
          return $_maybe_array['all_ctx'];
        if ( isset($_maybe_array['all_ctx_over']) )
          return $_maybe_array['all_ctx_over'];

        return $_maybe_array;
        //$this->default;
      }
  }
endif;

















/***************************************************
* AUGMENTS WP CUSTOMIZE CONTROLS
***************************************************/
/**
* Add controls to customizer
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
if ( ! class_exists( 'CZR_controls' ) ) :
  class CZR_controls extends WP_Customize_Control {
      public $type;
      public $link;
      public $title;
      public $label;
      public $buttontext;
      public $settings;
      public $hr_after;
      public $notice;
      //number vars
      public $step;
      public $min;
      public $icon;

      public function render_content()  {
        do_action( '__before_setting_control' , $this -> id );

        switch ( $this -> type) {
            case 'hr':
              echo '<hr class="tc-customizer-separator" />';
            break;


            case 'title' :
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php if (isset( $this->notice)) : ?>
              <i class="tc-notice"><?php echo $this -> notice ?></i>
             <?php endif; ?>

            <?php
            break;


            case 'select':
              if ( empty( $this->choices ) )
                return;
              ?>
              <?php if (!empty( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php $this -> czr_fn_print_select_control( in_array( $this->id, array( 'tc_theme_options[tc_fonts]', 'tc_theme_options[tc_skin]' ) ) ? 'select2' : '' ) ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this -> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              if ( 'tc_theme_options[tc_front_slider]' == $this -> id ) {
                //retrieve all sliders in option array
                $options          = get_option( 'tc_theme_options' );
                $sliders          = array();
                if ( isset( $options['tc_sliders'])) {
                  $sliders        = $options['tc_sliders'];
                }
                if ( empty( $sliders ) ) {
                  printf('<div class="tc-notice" style="width:99%; padding: 5px;"><p class="description">%1$s<br/><a class="button-primary" href="%2$s" target="_blank">%3$s</a><br/><span class="tc-notice">%4$s <a href="%5$s" title="%6$s" target="_blank">%6$s</a></span></p>',
                    __("You haven't create any slider yet. Go to the media library, edit your images and add them to your sliders.", "customizr" ),
                    admin_url( 'upload.php?mode=list' ),
                    __( 'Create a slider' , 'customizr' ),
                    __( 'Need help to create a slider ?' , 'customizr' ),
                    esc_url( "http://docs.presscustomizr.com/article/3-creating-a-slider-with-customizr-wordpress-theme" ),
                    __( 'Check the documentation' , 'customizr' )
                  );
                }
              }

            break;


            case 'number':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="tc-number-label customize-control-title"><?php echo $this->label ?></span>
                <input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="tc-number-input small-text">
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this-> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              break;

            case 'checkbox':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
                    printf('<div class="tc-check-label"><label><span class="customize-control-title">%1$s</span></label></div>',
                    $this->label
                  );
              ?>
              <input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />

              <?php if(!empty( $this -> notice)) : ?>
               <span class="tc-notice"><?php echo $this-> notice ?></span>
              <?php endif; ?>
              <?php
            break;

            case 'textarea':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this-> notice; ?></span>
                <?php endif; ?>
                <textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
              </label>
              <?php
              break;

            case 'url':
            case 'email':
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
              printf('<label><span class="customize-control-title %1$s">%2$s</span><input type="text" value="%3$s" %4$s /></label>',
                ! empty( $this -> icon) ? $this -> icon : '',
                $this->label,
                call_user_func( array( CZR_utils_settings_map::$instance, 'czr_fn_sanitize_' . $this -> type), $this->value() ),
                call_user_func( array( $this, 'get'.'_'.'link' ) )
              );
              break;

            default:
              global $wp_version;
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <?php if ( ! empty( $this->label ) ) : ?>
                  <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $this->description ) ) : ?>
                  <span class="description customize-control-description"><?php echo $this->description; ?></span>;;;
                <?php endif; ?>
                <?php if ( ! version_compare( $wp_version, '4.0', '>=' ) ) : ?>
                  <input type="<?php echo esc_attr( $this->type ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                <?php else : ?>
                  <input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
                <?php endif; ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="tc-notice"><?php echo $this-> notice; ?></span>
                <?php endif; ?>
              </label>
              <?php
            break;
          }//end switch
          do_action( '__after_setting_control' , $this -> id );
     }//end function




    private function czr_fn_print_select_control($class) {
      printf('<select %1$s class="%2$s">%3$s</select>',
        call_user_func( array( $this, 'get'.'_'.'link' ) ),
        $class,
        $this -> czr_fn_get_select_options()
      );
    }


    private function czr_fn_get_select_options() {
      $_options_html = '';
      switch ( $this -> id ) {
        case 'tc_theme_options[tc_fonts]':
          foreach ( $this -> choices as $_opt_group => $_opt_list ) {
            $_options = array();
            foreach ( $_opt_list['list'] as $label => $value ) {
              $_options[] = sprintf('<option value="%1$s" %2$s>%3$s</option>',
                esc_attr( $label ),
                selected( $this->value(), $value, false ),
                $value
              );
            }
            $_options_html .= sprintf('<optgroup label="%1$s">%2$s</optgroup>',
              $_opt_list['name'],
              implode($_options)
            );
          }
        break;

        case 'tc_theme_options[tc_skin]':
          $_data_hex  = '';
          $_color_map = CZR_utils::$inst -> czr_fn_get_skin_color( 'all' );
          //Get the color map array structured as follow
          // array(
          //       'blue.css'        =>  array( '#08c', '#005580' ),
          //       ...
          // )
          foreach ( $this->choices as $value => $label ) {
            if ( is_array($_color_map) && isset( $_color_map[esc_attr( $value )] ) )
              $_data_hex       = isset( $_color_map[esc_attr( $value )][0] ) ? $_color_map[esc_attr( $value )][0] : '';

            $_options_html .= sprintf('<option value="%1$s" %2$s data-hex="%4$s">%3$s</option>',
              esc_attr( $value ),
              selected( $this->value(), $value, false ),
              $label,
              $_data_hex
            );
          }
        break;

        default:
          foreach ( $this->choices as $value => $label ) {
            $_options_html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
              esc_attr( $value ),
              selected( $this->value(), $value, false ),
              $label
            );
          }
        break;
      }//end switch
      return $_options_html;
    }//end of fn

  }//end of class
endif;



/*
 * @since 3.4.19
 * @package      Customizr
*/
if ( class_exists('WP_Customize_Cropped_Image_Control') && ! class_exists( 'CZR_Customize_Cropped_Image_Control' ) ) :
  class CZR_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control {
    public $type = 'tc_cropped_image';
    public $title;
    public $notice;
    public $dst_width;
    public $dst_height;


    /**
    * Refresh the parameters passed to the JavaScript via JSON.
    *
    * @since 3.4.19
    * @package      Customizr
    *
    * @Override
    * @see WP_Customize_Control::to_json()
    */
    public function to_json() {
        parent::to_json();
        $this->json['title']  = !empty( $this -> title )  ? esc_html( $this -> title ) : '';
        $this->json['notice'] = !empty( $this -> notice ) ?           $this -> notice  : '';

        $this->json['dst_width']  = isset( $this -> dst_width )  ?  $this -> dst_width  : $this -> width;
        $this->json['dst_height'] = isset( $this -> dst_height ) ?  $this -> dst_height : $this -> height;
        //overload WP_Customize_Upload_Control
        //we need to re-build the absolute url of the logo src set in old Customizr
        $value = $this->value();
        if ( $value ) {
          //re-build the absolute url if the value isn't an attachment id before retrieving the id
          if ( (int) esc_attr( $value ) < 1 ) {
            $upload_dir = wp_upload_dir();
            $value  = false !== strpos( $value , '/wp-content/' ) ? $value : $upload_dir['baseurl'] . $value;
          }
          // Get the attachment model for the existing file.
          $attachment_id = attachment_url_to_postid( $value );
          if ( $attachment_id ) {
              $this->json['attachment'] = wp_prepare_attachment_for_js( $attachment_id );
      }
      }//end overload
    }

    /**
  * Render a JS template for the content of the media control.
  *
  * @since 3.4.19
    * @package      Customizr
    *
    * @Override
  * @see WP_Customize_Control::content_template()
  */
    public function content_template() {
    ?>
    <# if ( data.title ) { #>
        <h3 class="tc-customizr-title">{{{ data.title }}}</h3>
      <# } #>
        <?php parent::content_template(); ?>
      <# if ( data.notice ) { #>
        <span class="tc-notice">{{{ data.notice }}}</span>
      <# } #>
    <?php
    }
  }//end class
endif;


/**************************************************************************************************
* MULTIPICKER CLASSES
***************************************************************************************************/
if ( ! class_exists( 'CZR_Customize_Multipicker_Control' ) ) :
  /**
  * Customize Multi-picker Control Class
  *
  * @package WordPress
  * @subpackage Customize
  * @since 3.4.10
  */
  abstract class CZR_Customize_Multipicker_Control extends CZR_controls {

    public function render_content() {

      if ( ! $this -> type ) return;
      do_action( '__before_setting_control' , $this -> id );

      $dropdown = $this -> czr_fn_get_dropdown_multipicker();

      if ( empty( $dropdown ) ) return;

      $dropdown = str_replace( '<select', '<select multiple="multiple"' . $this->get_link(), $dropdown );
      //start rendering
      if (!empty( $this->title)) :
    ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>

      <label>
        <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php echo $dropdown; ?>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="tc-notice"><?php echo $this -> notice ?></span>
         <?php endif; ?>
      </label>
    <?php
      do_action( '__after_setting_control' , $this -> id );
    }

    //to define in the extended classes
    abstract public function czr_fn_get_dropdown_multipicker();
  }//end class
endif;

if ( ! class_exists( 'CZR_Customize_Multipicker_Categories_Control' ) ) :
  class CZR_Customize_Multipicker_Categories_Control extends CZR_Customize_Multipicker_Control {

    public function czr_fn_get_dropdown_multipicker() {
      $cats_dropdown = wp_dropdown_categories(
          array(
              'name'               => '_customize-'.$this->type,
              'id'                 => $this -> id,
              //hide empty, set it to false to avoid complains
              'hide_empty'         => 0 ,
              'echo'               => 0 ,
              'walker'             => new CZR_Walker_CategoryDropdown_Multipicker(),
              'hierarchical'       => 1,
              'class'              => 'select2 '.$this->type,
              'selected'           => implode(',', $this->value() )
          )
      );

      return $cats_dropdown;
    }
  }
endif;


/**
 * @ dropdown multi-select walker
 * Create HTML dropdown list of Categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 *
 * we need to allow more than one "selected" attribute
 */

if ( ! class_exists( 'CZR_Walker_CategoryDropdown_Multipicker' ) ) :
  class CZR_Walker_CategoryDropdown_Multipicker extends Walker_CategoryDropdown {
    /**
     * Start the element output.
     *
     * @Override
     *
     * @see Walker::start_el()
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category. Used for padding.
     * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
     *                         See {@see wp_dropdown_categories()}.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
      $pad = str_repeat('&mdash;', $depth );
      /** This filter is documented in wp-includes/category-template.php */
      $cat_name = apply_filters( 'list_cats', $category->name, $category );

      $value_field = 'term_id';

      $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";
      //Treat selected arg as array
      if ( in_array( (string) $category->{$value_field}, explode( ',', $args['selected'] ) ) )
        $output .= ' selected="selected"';

      $output .= '>';
      $output .= $pad.$cat_name;
      if ( $args['show_count'] )
        $output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) .')';
      $output .= "</option>\n";
    }
  }
endif;
/**************************************************************************************************
* END OF MULTIPICKER CLASSES
***************************************************************************************************/












/*********************************************************************************
* Old upload control used until v3.4.18, still used if current version of WP is < 4.3
**********************************************************************************/

if ( ! class_exists( 'CZR_Customize_Upload_Control' ) ) :
  /**
   * Customize Upload Control Class
   *
   * @package WordPress
   * @subpackage Customize
   * @since 3.4.0
   */
  class CZR_Customize_Upload_Control extends WP_Customize_Control {
    public $type    = 'tc_upload';
    public $removed = '';
    public $context;
    public $extensions = array();
    public $title;
    public $notice;

    /**
     * Enqueue control related scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue() {
      wp_enqueue_script( 'wp-plupload' );
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @since 3.4.0
     * @uses WP_Customize_Control::to_json()
     */
    public function to_json() {
      parent::to_json();

      $this->json['removed'] = $this->removed;

      if ( $this->context )
        $this->json['context'] = $this->context;

      if ( $this->extensions )
        $this->json['extensions'] = implode( ',', $this->extensions );
    }

    /**
     * Render the control's content.
     *
     * @since 3.4.0
     */
  public function render_content() {
      do_action( '__before_setting_control' , $this -> id );
      ?>
      <?php if ( isset( $this->title) ) : ?>
        <h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>
      <label>
        <?php if ( ! empty( $this->label ) ) : ?>
          <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php endif;
        if ( ! empty( $this->description ) ) : ?>
          <span class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>
        <div>
          <a href="#" class="button-secondary tc-upload"><?php _e( 'Upload' , 'customizr'  ); ?></a>
          <a href="#" class="remove"><?php _e( 'Remove' , 'customizr'  ); ?></a>
        </div>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="tc-notice"><?php echo $this -> notice; ?></span>
        <?php endif; ?>
      </label>
      <?php
      do_action( '__after_setting_control' , $this -> id );
    }
  }
endif;

?>