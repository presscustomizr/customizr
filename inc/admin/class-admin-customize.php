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
if ( ! class_exists( 'TC_customize' ) ) :
	class TC_customize {
    static $instance;
    public $control_translations;

    function __construct () {
      global $wp_version;
      //check if WP version >= 3.4 to include customizer functions
      //Shall we really keep this ?
      if ( ! version_compare( $wp_version, '3.4' , '>=' ) ) {
        add_action( 'admin_menu'                    , array( $this , 'tc_add_fallback_page' ));
        return;
      }

      self::$instance =& $this;
  		//add control class
  		add_action ( 'customize_register'				                , array( $this , 'tc_augment_customizer' ),10,1);

  		//add grid/post list buttons in the control views
  		add_action( '__before_setting_control'                  , array( $this , 'tc_render_grid_control_link') );

  		//control scripts and style
  		add_action ( 'customize_controls_enqueue_scripts'	      , array( $this , 'tc_customize_controls_js_css' ));
  		//add the customizer built with the builder below
  		add_action ( 'customize_register'				                , array( $this , 'tc_customize_register' ), 20, 1 );

      //modify some WP built-in settings / controls / sections
      add_action ( 'customize_register'                       , array( $this , 'tc_alter_wp_customizer_settings' ), 30, 1 );

      //preview scripts
      //set with priority 20 to be fired after tc_customize_store_db_opt in TC_utils
  		add_action ( 'customize_preview_init'			              , array( $this , 'tc_customize_preview_js' ), 20 );
  		//Hide donate button
  		add_action ( 'wp_ajax_hide_donate'				              , array( $this , 'tc_hide_donate' ) );
  		//Grunt Live reload script on DEV mode (TC_DEV constant has to be defined. In wp_config for example)
      if ( defined('TC_DEV') && true === TC_DEV && apply_filters('tc_live_reload_in_dev_mode' , true ) )
      	add_action( 'customize_controls_print_scripts'        , array( $this , 'tc_add_livereload_script') );

      add_action ( 'customize_controls_print_footer_scripts'  , array( $this, 'tc_print_js_templates' ) );
    }



    /*
    * Since the WP_Customize_Manager::$controls and $settings are protected properties, one way to alter them is to use the get_setting and get_control methods
    * Another way is to remove the control and add it back as an instance of a custom class and with new properties
    * and set new property values
    * hook : tc_customize_register:30
    * @return void()
    */
    function tc_alter_wp_customizer_settings( $wp_customize ) {
      //CHANGE BLOGNAME AND BLOGDESCRIPTION TRANSPORT
      $wp_customize -> get_setting( 'blogname' )->transport = 'postMessage';
      $wp_customize -> get_setting( 'blogdescription' )->transport = 'postMessage';


      //IF WP VERSION >= 4.3 AND SITE_ICON SETTING EXISTS
      //=> REMOVE CUSTOMIZR FAV ICON CONTROL
      //=> CHANGE SITE ICON DEFAULT WP SECTION TO CUSTOMIZR LOGO SECTION
      global $wp_version;
      if ( version_compare( $wp_version, '4.3', '>=' ) && is_object( $wp_customize -> get_control( 'site_icon' ) ) ) {
        $tc_option_group = TC___::$tc_option_group;
        $wp_customize -> remove_control( "{$tc_option_group}[tc_fav_upload]" );
        //note : the setting is kept because used in the customizer js api to handle the transition between Customizr favicon to WP site icon.
        $wp_customize -> get_control( 'site_icon' )->section = 'logo_sec';

        //add a favicon title after the logo upload
        add_action( '__after_setting_control' , array( $this , 'tc_add_favicon_title') );
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

        $wp_customize -> add_control( new TC_controls( $wp_customize, $menu_setting_id, $_control_properties ) );

        $_priority = $_priority + 10;
      }//foreach
    }


    /*
    * hook : '__after_setting_control' (declared in class-tc-controls-settings.php)
    * Display a title for the favicon control, after the logo
    */
    function tc_add_favicon_title($set_id) {
      if ( false !== strpos( $set_id, 'tc_sticky_logo_upload' ) )
        printf( '<h3 class="tc-customizr-title">%s</h3>', __( 'SITE ICON' , 'customizr') );
    }

		/**
		* Augments wp customize controls and settings classes
		* @package Customizr
		* @since Customizr 1.0
		*/
		function tc_augment_customizer( $manager ) {
      //loads custom settings and controls classes for the Customizr theme
      //- TC_Customize_Setting extends WP_Customize_Setting => to override the value() method
      //- TC_controls extends WP_Customize_Control => overrides the render() method
      //- TC_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control => introduced in v3.4.19, uses a js template to render the control
      //- TC_Customize_Upload_Control extends WP_Customize_Control => old upload control used until v3.4.18, still used if current version of WP is < 4.3
      //- TC_Customize_Multipicker_Control extends TC_controls => used for multiple cat picker for example
      //- TC_Customize_Multipicker_Categories_Control extends TC_Customize_Multipicker_Control => extends the multipicker
      //- TC_Walker_CategoryDropdown_Multipicker extends Walker_CategoryDropdown => needed for the multipicker to allow more than one "selected" attribute
      locate_template( 'inc/admin/class-tc-controls-settings.php' , $load = true, $require_once = true );

      //Registered types are eligible to be rendered via JS and created dynamically.
      if ( class_exists('TC_Customize_Cropped_Image_Control') )
        $manager -> register_control_type( 'TC_Customize_Cropped_Image_Control' );
		}



		/**
		* Generates customizer sections, settings and controls
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_customize_register( $wp_customize) {
			return $this -> tc_customize_factory (
        $wp_customize,
        $this -> tc_customize_arguments(),
        TC_utils_settings_map::$instance -> tc_get_customizer_map()
      );
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

			//add settings and controls
			if ( isset( $setup['add_setting_control'])) {

				foreach ( $setup['add_setting_control'] as $key => $options) {
					//isolates the option name for the setting's filter
					$f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
		      $f_option_name = isset( $match[1][0] )  ? $match[1][0] : 'setting';

          $tc_option_group = TC___::$tc_option_group;
          //build option name
          //When do we add a prefix ?
          //all customizr theme options start by "tc_" by convention
          //=> footer customizer addon starts by fc_
          //=> grid customizer addon starts by gc_
          //When do we add a prefix ?
          $add_prefix = false;
          if ( TC_utils::$inst -> tc_is_customizr_option( $key ) )
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
          if ( class_exists('TC_Customize_Setting') )
            $wp_customize -> add_setting( new TC_Customize_Setting ( $wp_customize, $_opt_name, $option_settings ) );
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
    function tc_render_grid_control_link( $set_id ) {
      if ( false !== strpos( $set_id, 'tc_post_list_show_thumb' ) )
        printf('<span class="tc-grid-toggle-controls" title="%1$s">%1$s</span>' , __('More grid design options' , 'customizr'));
    }


		/**
		 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 * @package Customizr
		 * @since Customizr 1.0
		 */

		function tc_customize_preview_js() {
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
                'fontPairs'       => TC_utils::$inst -> tc_get_font( 'list' ),
                'fontSelectors'   => TC_init::$instance -> font_selectors,
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
		function tc_customize_controls_js_css() {

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
            'HideDonate'    => $this -> tc_get_hide_donate_status(),
            'ShowCTA'       => ( true == TC_utils::$inst->tc_opt('tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false,
            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'    => array(
              'postSliderNote' => __( "This option generates a home page slider based on your last posts, starting from the most recent or the featured (sticky) post(s) if any.", "customizr" ),
              'faviconNote' => __( "Your favicon is currently handled with an old method and will not be properly displayed on all devices. You might consider to re-upload your favicon with the new control below." , 'customizr')
            )
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
    function tc_get_hide_donate_status() {
      //is customizr the current active theme?
      //=> check the existence of is_theme_active for backward compatibility (may be useless because introduced in 3.4... )
      $_is_customizr_active = method_exists( $GLOBALS['wp_customize'], 'is_theme_active' ) && $GLOBALS['wp_customize'] -> is_theme_active();
      $_options = get_option('tc_theme_options');
      $_user_started_customize = false !== $_options || ! empty( $_options );

      //shall we hide donate ?
      return ! $_user_started_customize || ! $_is_customizr_active || TC_utils::$inst->tc_opt('tc_hide_donate');
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
      wp_die();
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
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
              sprintf('%scustomizr-pro/', TC_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="rate-czr">
        <?php
        $_is_pro = 'customizr-pro' == TC___::$theme_name;
          printf( '<span class="tc-rate-link">%1$s %2$s, <br/>%3$s <a href="%4$s" title="%5$s" class="tc-stars" target="_blank">%6$s</a> %7$s</span>',
            __( 'If you like' , 'customizr' ),
            ! $_is_pro ? __( 'the Customizr theme' , 'customizr') : __( 'the Customizr pro theme' , 'customizr'),
            __( 'we would love to receive a' , 'customizr' ),
            ! $_is_pro ? 'https://' . 'wordpress.org/support/view/theme-reviews/customizr?filter=5' : sprintf('%scustomizr-pro/#comments', TC_WEBSITE ),
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
    function tc_add_fallback_page() {
        $theme_page = add_theme_page(
            __( 'Upgrade WP' , 'customizr' ),   // Name of page
            __( 'Upgrade WP' , 'customizr' ),   // Label in menu
            'edit_theme_options' ,          // Capability required
            'upgrade_wp.php' ,             // Menu slug, used to uniquely identify the page
            array( $this , 'tc_fallback_admin_page' )         //function to be called to output the content of this page
        );
    }




    /**
    * Render fallback admin page.
    * @package Customizr
    * @since Customizr 1.1
    */
    function tc_fallback_admin_page() {
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
