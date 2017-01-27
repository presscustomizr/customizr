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
    public $czz_attr;

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
      add_action( 'customize_register'                       , array( $this , 'czr_fn_augment_customizer' ),10,1);

      //add the customizer built with the builder below
      add_action( 'customize_register'                       , array( $this , 'czr_fn_customize_register' ), 20, 1 );

      //modify some WP built-in settings / controls / sections
      add_action( 'customize_register'                       , array( $this , 'czr_fn_alter_wp_customizer_settings' ), 1000, 1 );

      //add grid/post list buttons in the control views
      add_action( '__before_setting_control'                  , array( $this , 'czr_fn_render_grid_control_link') );

      //preview scripts
      //set with priority 20 to be fired after tc_customize_store_db_opt in CZR_utils
      //add_action ( 'customize_preview_init'                   , array( $this , 'czr_fn_customize_preview_js' ), 20 );
      //Hide donate button
      add_action ( 'wp_ajax_hide_donate'                      , array( $this , 'czr_fn_hide_donate' ) );

      add_action ( 'customize_controls_print_footer_scripts'  , array( $this, 'czr_fn_print_js_templates' ) );

      //Partial refreshs
      //add_action( 'customize_register'                       , array( $this,  'czr_fn_register_partials' ) );

      //CONCATENATED WITH GRUNT
      //
      //Print modules and inputs templates
      //$this -> czr_fn_load_tmpl();

      //Add the module data server side generated + additional resources (like the WP text editor)
      //$this -> czr_fn_load_module_data_resources();
      // end CONCATENATED WITH GRUNT
      //
      //populate the css_attr property, used both server side and on the customize panel (passed via serverControlParams )
      $this -> css_attr = $this -> czr_fn_get_controls_css_attr();

      //load resources class
      $this -> czr_fn_require_czr_resources();
    }


    function czr_fn_require_czr_resources() {
      if (  ! is_object(CZR_customize_resources::$instance) )
        new CZR_customize_resources();;
    }


    /* ------------------------------------------------------------------------- *
     *  AUGMENT CUSTOMIZER SERVER SIDE
    /* ------------------------------------------------------------------------- */
    /**
    * Augments wp customize controls and settings classes
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_augment_customizer( $manager ) {
      /* Concatenated with grunt  */

      //Registered types are eligible to be rendered via JS and created dynamically.
      if ( class_exists('CZR_Customize_Cropped_Image_Control') )
        $manager -> register_control_type( 'CZR_Customize_Cropped_Image_Control' );

      if ( class_exists('CZR_Customize_Panels') )
        $manager -> register_panel_type( 'CZR_Customize_Panels');
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
                'title',
                'czr_subtitle',
                'description',
                'priority' ,
                'theme_supports',
                'capability',
                'type'
          ),
          'sections' => array(
                'title' ,
                'priority' ,
                'description',
                'panel',
                'theme_supports',
                'type',
                'active_callback'
          ),
          'settings' => array(
                'default'     =>  null,
                'capability'    =>  'manage_options' ,
                'setting_type'    =>  'option' ,
                'sanitize_callback' =>  null,
                'sanitize_js_callback'  =>  null,
                'validate_callback'  =>  null,
                'transport'     =>  null
          ),
          'controls' => array(
                'title' ,
                'label' ,
                'description',
                'section' ,
                'settings',
                'type' ,

                'module_type',
                'syncCollection',

                'choices' ,
                'priority' ,

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
          if ( class_exists( ' CZR_Customize_Panels ' ) ) {
            $wp_customize -> add_panel( new CZR_Customize_Panels( $wp_customize, $p_key, $panel_options ) );
          } else {
            $wp_customize -> add_panel( $p_key, $panel_options );
          }
        }
      }

      //remove sections
      if ( isset( $setup['remove_section'])) {
        foreach ( $setup['remove_section'] as $section) {
          $wp_customize -> remove_section( $section);
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

          //instanciate a custom class if defined
          if( ! isset( $options['section_class']) )
            $wp_customize -> add_section( $key,$option_section);
          else if ( isset( $options['section_class']) && class_exists($options['section_class']) )
            $wp_customize -> add_section( new $options['section_class']( $wp_customize, $key, $option_section ));

        }//end foreach
      }//end if


      //add setting alone
      if ( isset( $setup['add_setting'])) {

        foreach ( $setup['add_setting'] as $key => $options) {
          //isolates the option name for the setting's filter
          $f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
          $f_option_name = isset( $match[1][0] )  ? $match[1][0] : 'setting';

          $czr_option_group = CZR_THEME_OPTIONS;

          $_opt_name = "{$czr_option_group}[{$key}]";

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
        }//end for each
      }//end if isset


      //add control alone
      if ( isset( $setup['add_control'])) {

        foreach ( $setup['add_control'] as $key => $options) {
          //isolates the option name for the setting's filter
          $f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
          $f_option_name = isset( $match[1][0] )  ? $match[1][0] : 'setting';

          $czr_option_group = CZR_THEME_OPTIONS;

          $_opt_name = "{$czr_option_group}[{$key}]";

          //generate controls array
          $option_controls = array();
          foreach( $args['controls'] as $con) {
            $option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
          }

          //add control with a class instanciation if not default
          if( ! isset( $options['control']) )
            $wp_customize -> add_control( $_opt_name, $option_controls );
          else
            $wp_customize -> add_control( new $options['control']( $wp_customize, $_opt_name, $option_controls ));

        }//end for each
      }//end if isset



      //add settings and controls
      if ( isset( $setup['add_setting_control'])) {

        foreach ( $setup['add_setting_control'] as $key => $options) {
          //isolates the option name for the setting's filter
          $f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
          $f_option_name = isset( $match[1][0] )  ? $match[1][0] : 'setting';

          $czr_option_group = CZR_THEME_OPTIONS;

          //build option name
          //When do we add a prefix ?
          //all customizr theme options start by "tc_" by convention
          //=> footer customizer addon starts by fc_
          //=> grid customizer addon starts by gc_
          //When do we add a prefix ?
          $add_prefix = false;
          if ( CZR_utils::$inst -> czr_fn_is_customizr_option( $key ) )
            $add_prefix = true;
          $_opt_name = $add_prefix ? "{$czr_option_group}[{$key}]" : $key;

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
            $wp_customize -> add_control( $_opt_name, $option_controls );
          else {
            if ( class_exists( $options['control'] ) )
              $wp_customize -> add_control( new $options['control']( $wp_customize, $_opt_name, $option_controls ));
          }

        }//end for each
      }//end if isset
    }//end of customize generator function


    /* ------------------------------------------------------------------------- *
     *  HELPERS
    /* ------------------------------------------------------------------------- */
    function czr_fn_get_controls_css_attr() {
      return apply_filters('controls_css_attr',
          array(
            'multi_input_wrapper' => 'czr-multi-input-wrapper',
            'sub_set_wrapper'     => 'czr-sub-set',
            'sub_set_input'       => 'czr-input',
            'img_upload_container' => 'czr-imgup-container',

            'items_wrapper'     => 'czr-items-wrapper',
            'single_item'        => 'czr-single-item',
            'item_content'      => 'czr-item-content',
            'item_header'       => 'czr-item-header',
            'item_title'        => 'czr-item-title',
            'item_btns'         => 'czr-item-btns',
            'item_sort_handle'   => 'czr-item-sort-handle',

            //remove dialog
            'display_alert_btn' => 'czr-display-alert',
            'remove_alert_wrapper'   => 'czr-remove-alert-wrapper',
            'cancel_alert_btn'  => 'czr-cancel-button',
            'remove_view_btn'        => 'czr-remove-button',

            'edit_view_btn'     => 'czr-edit-view',
            //pre add dialog
            'open_pre_add_btn'      => 'czr-open-pre-add-new',
            'adding_new'        => 'czr-adding-new',
            'pre_add_wrapper'   => 'czr-pre-add-wrapper',
            'pre_add_item_content'   => 'czr-pre-add-view-content',
            'cancel_pre_add_btn'  => 'czr-cancel-add-new',
            'add_new_btn'       => 'czr-add-new',
            'pre_add_success'   => 'czr-add-success'
        )
      );
    }

    //@return array of WP builtin settings
    function czr_fn_get_wp_builtin_settings() {
      return array(
        'blogname',
        'blogdescription',
        'site_icon',
        //'custom_logo',
        //'background_color',
        'show_on_front',
        'page_on_front',
        'page_for_posts',
        //'header_image',
       // 'header_image_data'
      );
    }

    //@return array of grid design options
    function czr_fn_get_grid_design_controls() {
      return apply_filters( 'tc_grid_design_controls', array(
        'tc_grid_in_blog',
        'tc_grid_in_archive',
        'tc_grid_in_search',
        'tc_grid_thumb_height',
        'tc_grid_shadow',
        'tc_grid_bottom_border',
        'tc_grid_icons',
        'tc_grid_num_words'
      ) );
    }

    /**
    * hook __before_setting_control (declared in class-tc-controls-settings.php)
    * @echo clickable text
    */
    function czr_fn_render_grid_control_link( $set_id ) {
      if ( false !== strpos( $set_id, 'tc_post_list_show_thumb' ) ) {
        printf('<span class="tc-grid-toggle-controls" title="%1$s">%1$s</span>' , __('More grid design options' , 'customizr'));
      }
    }




    /*
    * hook : '__after_setting_control' (declared in class-tc-controls-settings.php)
    * Display a title for the favicon control, after the logo
    */
    function czr_fn_add_favicon_title($set_id) {
      if ( false !== strpos( $set_id, 'tc_sticky_logo_upload' ) )
        printf( '<h3 class="czr-customizr-title">%s</h3>', __( 'SITE ICON' , 'customizr') );
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
        <div id="czr-donate-customizer">
          <a href="#" class="czr-close-request button" title="<?php _e('dismiss' , 'customizr'); ?>">X</a>
            <?php
              printf('<h3>%1$s <a href="%2$s" target="_blank">Nicolas</a>%3$s :).</h3>',
                __( "Hi! This is" , 'customizr' ),
                esc_url('twitter.com/presscustomizr'),
                __( ", developer of the Customizr theme", 'customizr' )
              );
              printf('<span class="czr-notice">%1$s</span>',
                __( "I'm doing my best to make Customizr the perfect free theme for you. If you think it helped you in any way to build a better web presence, please support its continued development with a donation of $20, $50..." , 'customizr' )
              );
              printf('<a class="czr-donate-link" href="%1$s" target="_blank" rel="nofollow"><img src="%2$s" alt="%3$s"></a>',
                esc_url('paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU'),
                esc_url('paypal.com/en_US/i/btn/btn_donate_LG.gif'),
                __( "Make a donation for Customizr" , 'customizr' )
              );
              printf('<div class="donate-alert"><p class="czr-notice">%1$s</p><span class="czr-hide-donate button">%2$s</span><span class="czr-cancel-hide-donate button">%3$s</span></div>',
                __( "Once clicked the 'Hide forever' button, this donation block will not be displayed anymore.<br/>Either you are using Customizr for personal or business purposes, any kind of sponsorship will be appreciated to support this free theme.<br/><strong>Already donator? Thanks, you rock!<br/><br/> Live long and prosper with Customizr!</strong>" , 'customizr'),
                __( "Hide forever" , 'customizr' ),
                sprintf( '%s <span style="font-size:20px">%s</span>', __( "Let me think twice" , 'customizr' ), convert_smilies( ':roll:') )
              );
            ?>
        </div>
      </script>
      <script type="text/template" id="main_cta">
        <div class="czr-cta czr-cta-wrap">
          <?php
            printf('<a class="czr-cta-btn" href="%1$s" title="%2$s" target="_blank">%2$s &raquo;</a>',
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="wfc_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s</span><a class="czr-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Need more control on your fonts ? Style any text in live preview ( size, color, font family, effect, ...) with Customizr Pro." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>
      <script type="text/template" id="fpu_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s</span><a class="czr-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Add unlimited featured pages with Customizr Pro." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

      <script type="text/template" id="gc_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s</span><a class="czr-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Rediscover the beauty of your blog posts and increase your visitors engagement with the Grid Customizer." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

       <script type="text/template" id="mc_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s</span><a class="czr-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
              __( "Add creative and engaging reveal animations to your side menu." , 'customizr' ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

      <script type="text/template" id="footer_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s</span><a class="czr-cta-btn" href="%2$s" title="%3$s" target="_blank">%3$s &raquo;</a>',
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
          printf( '<span class="czr-rate-link">%1$s %2$s, <br/>%3$s <a href="%4$s" title="%5$s" class="czr-stars" target="_blank">%6$s</a> %7$s</span>',
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
  }//end class
endif;
?><?php
/**
* Customizer actions and filters
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2017, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_customize_resources' ) ) :
  class CZR_customize_resources {
    static $instance;

    function __construct () {
      self::$instance =& $this;

      //control scripts and style
      add_action( 'customize_controls_enqueue_scripts'        , array( $this, 'czr_fn_customize_controls_js_css' ), 10 );

      //Add the control dependencies
      add_action( 'customize_controls_print_footer_scripts'   , array( $this, 'czr_fn_extend_ctrl_dependencies' ), 10 );

      //Add various dom ready
      add_action( 'customize_controls_print_footer_scripts'   , array( $this, 'czr_fn_add_various_dom_ready_actions' ), 10 );

      //preview scripts
      //set with priority 20 to be fired after czr_fn_customize_store_db_opt in HU_utils
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_customize_preview_js' ), 20 );
      //exports some wp_query informations. Updated on each preview refresh.
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_add_preview_footer_action' ), 20 );
    }


    //hook : customize_preview_init
    function czr_fn_customize_preview_js() {
      global $wp_version;

      wp_enqueue_script(
        'czr-customizer-preview' ,
        sprintf('%1$s/assets/czr/js/czr-preview%2$s.js' , get_template_directory_uri(), ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
        array( 'customize-preview', 'underscore'),
        ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : HUEMAN_VER,
        true
      );

      //localizes
      wp_localize_script(
            'czr-customizer-preview',
            'CZRPreviewParams',
            apply_filters('tc_js_customizer_preview_params' ,
              array(
                'themeFolder'     => get_template_directory_uri(),
                'customSkin'      => apply_filters( 'tc_custom_skin_preview_params' , array( 'skinName' => '', 'fullPath' => '' ) ),
                'fontPairs'       => CZR_utils::$inst -> czr_fn_get_font( 'list' ),
                'fontSelectors'   => CZR_init::$instance -> font_selectors,
                'wpBuiltinSettings' => CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
                'themeOptions'  => CZR_THEME_OPTIONS,
                //patch for old wp versions which don't trigger preview-ready signal => since WP 4.1
                'preview_ready_event_exists'   => version_compare( $wp_version, '4.1' , '>=' ),
                'blogname' => get_bloginfo('name'),
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
        sprintf('%1$sassets/czr/css/czr-control%2$s.css' , TC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
        array( 'customize-controls' ),
        ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUSTOMIZR_VER,
        $media = 'all'
      );

      $_controls_css     = $this -> czr_fn_get_inline_control_css();
      wp_add_inline_style( 'tc-customizer-controls-style', $_controls_css );

      wp_enqueue_script(
        'tc-customizer-controls',
        //need the full because as of now
        sprintf('%1$sassets/czr/js/czr-control%2$s.js' , TC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
        array( 'customize-controls' , 'underscore'),
        ( defined('WP_DEBUG') && true === WP_DEBUG ) ? time() : CUSTOMIZR_VER,
        true
      );


      //gets the featured pages id from init
      $fp_ids       = apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);

      //declares the common fp control fields and the dynamic arrays
      $fp_controls      = array(
        'tc_theme_options[tc_show_featured_pages_img]',
        'tc_theme_options[tc_featured_page_button_text]'
      );
      $page_dropdowns     = array();
      $text_fields      = array();

      //adds filtered page dropdown fields
      foreach ( $fp_ids as $id ) {
        $page_dropdowns[]   = 'tc_theme_options[tc_featured_page_'. $id.']';
        $text_fields[]    = 'tc_theme_options[tc_featured_text_'. $id.']';
      }

      //localizes
      wp_localize_script(
        'tc-customizer-controls',
        'serverControlParams',
        apply_filters('czr_js_customizer_control_params' ,
          array(
            'FPControls'      => array_merge( $fp_controls , $page_dropdowns , $text_fields ),
            'AjaxUrl'         => admin_url( 'admin-ajax.php' ),
            'docURL'          => esc_url('docs.presscustomizr.com/'),

            'TCNonce'         => wp_create_nonce( 'tc-customizer-nonce' ),
            'themeName'       => CZR___::$theme_name,
            'HideDonate'      => CZR_customize::$instance -> czr_fn_get_hide_donate_status(),
            'ShowCTA'         => ( true == CZR_utils::$inst->czr_fn_opt('tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false,

            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'   => $this -> czr_fn_get_translated_strings(),

            'themeOptions'     => CZR_THEME_OPTIONS,
            'optionAjaxAction' => CZR_OPT_AJAX_ACTION,

            'isDevMode'        => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('TC_DEV') && true === TC_DEV ),

            'wpBuiltinSettings'=> CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
            'css_attr'         => CZR_customize::$instance -> czr_fn_get_controls_css_attr(),
            'isThemeSwitchOn'  => isset( $_GET['theme']),
            'themeSettingList' => CZR_utils::$_theme_setting_list,

            'faviconOptionName' => 'tc_fav_upload',

            'gridDesignControls' => CZR_customize::$instance -> czr_fn_get_grid_design_controls(),
          )
        )
      );

    }

    function czr_fn_get_inline_control_css() {
      return '
      /* temporary */
li[id*="customize-control-"] {
  border: none;
  box-shadow: none;-webkit-box-shadow: none;
  padding: 0
}
.customize-control span.customize-control-title:first-child {
  padding-left: 0;
}
    /* end temporary */

/* SELECT 2 SPECIFICS */
body .select2-dropdown {
  z-index: 998;
}
body .select2-container--open .select2-dropdown--below {
    border: 1px solid #008ec2;
}
body .select2-container--open .select2-dropdown--above {
    border-top: 1px solid #008ec2;
}

[id*=tc_theme_options-tc_skin] .select2-container .select2-selection--single .select2-selection__rendered {
  padding-left: 0;
}
body .select2-container--default .select2-selection--single .select2-selection__arrow b {
  margin-top: 0;
}
body .select2-container .select2-selection--single {
  box-sizing: content-box;
}
.select2-results .tc-select2-skin-color {
  padding: 8px 0px;
}
body .select2-container-active .select2-choice, body .select2-container-active .select2-choices {
    border: 1px solid #008ec2;
}
body .select2-results {
  max-height: 360px
}
.tc-select2-skin-color {
  display: inline-block;
  -webkit-box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
  -moz-box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
  box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
  padding: 0;
  width: 100%;
  text-align: center;
  text-shadow: 1px 1px 1px #000;
  font-weight: bold;
  color: #FFF;
  -webkit-transition: box-shadow .25s ease;
  -moz-transition: box-shadow .25s ease;
  -ms-transition: box-shadow .25s ease;
  -o-transition: box-shadow .25s ease;
  transition: box-shadow .25s ease;
  /* vertical-align: middle; */
}
.select2-chosen .tc-select2-skin-color, .tc-select2-skin-color:hover {
  -webkit-box-shadow: none;
  -moz-box-shadow:none;
  box-shadow: none;
}
/* FONTS */
.tc-select2-font {
  padding: 7px 7px 4px;
  line-height: 20px;
}
.select2-results .select2-highlighted .tc-select2-font{
  color: #555;
}

.select2-results__group {
  font-weight: 700;
  text-align: center;
  padding-top: 3px;
  line-height: 22px;
}

.tc-title-google-logo {
  display: block;
  float: left;
  position: relative;
  z-index: 100;
  padding: 2px 4px 0 14px;
  top: 10px;
}
.rtl .tc-title-google-logo {
  float: right;
  padding: 2px 18px 0 7px;
}
.tc-google-logo {
  position: relative;
  top: 6px;
}
/* Call to actions block */
.tc-grid-control-section {
  width: 100%;
  float: left;
  clear: both;
  margin-bottom: 8px;
}

.tc-grid-toggle-controls {
    font-size: 15px;
    text-transform: uppercase;
    clear: both;
    width: 100%;
    display: block;
    float: left;
    margin: 15px 0;
    cursor: pointer;
    color: #000;
}
.tc-grid-toggle-controls::before {
  content: "+";
  font-size: 18px;
  display: block;
  float: left;
  background: #000;
  padding: 5px;
  line-height: 11px;
  -webkit-border-radius: 20px;
  -moz-border-radius: 20px;
  border-radius: 20px;
  color: #FFF;
  margin-right: 5px;
  bottom: 2px;
  width: 12px;
  height: 12px;
  text-align: center;
  position: relative;
}

.tc-grid-toggle-controls.open::before {
  content: "-";
  line-height: 11px;
}

li[id*="customize-control-"].tc-grid-design {
  border-left: 2px dotted #008ec2;
  margin-left: 3%;
  padding-left: 3%;
  width: 93%;
  font-style: italic;
}
.customize-control .tc-navigate-to-post-list {
  color: #008ec2;
  font-weight: bold;
  float: left;
  clear: both;
  width: 100%;
  margin-bottom: 8px;
}

.tc-sub-control {
  padding-left: 13%;
  max-width: 87%;
  position: relative;
}

.tc-sub-control:before {
  content: "";
  height: 116%;
  background: #008ec2;
  width: 2%;
  position: absolute;
  left: 7%;
}
/* DONATE BLOCK*/
#czr-donate-customizer {
  background-color: #FFF;
  color: #666;
  border-left: 0;
  border-right: 0;
  border-bottom: 1px solid #EEE;
  margin: 0;
  padding: 4px 15px 4px;
  position: relative;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
    left: 0;
    -webkit-transition: left ease-in-out .18s;
    transition: left ease-in-out .18s;
}
.wp-customizer #czr-donate-customizer h3 {
  font-size: 13px;
  margin-bottom: 1px;
  margin-top: 0px;
  width: 94%;
  font-weight: 600;
}
#czr-donate-customizer .czr-notice {
  padding-bottom: 4px;
}
#czr-donate-customizer p {
  margin: 0px;
}
#czr-donate-customizer .czr-donate-link {
  display: block;
  text-align: center;
}
#czr-donate-customizer .donate-alert {
  display: none;
  clear: both;
  background-color: #008ec2;
  border-color: 1px solid #D6E9C6;
  color: #FFF;
  padding: 10px;
  margin-top: 0px;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}
#czr-donate-customizer .donate-alert p {
  font-size: 12px;
}
#czr-donate-customizer .donate-alert .button {
  padding: 0 9px 1px;
}

#czr-donate-customizer .czr-close-request{
  position: absolute;
  right: 8px;
  top: 4px;
  font-size: 14px;
  line-height: 19px;
  height: 21px;
  margin: 0;
  padding: 1px 6px 0;
  background-color: #008ec2;
  color: white;
  border: none;
  box-shadow: none;
}
.rtl #czr-donate-customizer .czr-close-request {
  left: 8px;
  right: inherit;
}
#czr-donate-customizer .donate-alert .czr-hide-donate, #czr-donate-customizer .donate-alert .czr-cancel-hide-donate {
  padding: 0 5px 1px;
}

.czr-cancel-hide-donate {
  float: right;
}

/* Call to actions block */
.czr-cta-wrap {
  background-color: #FFF;
  color: #666;
  border-left: 0;
  border-right: 0;
  border-bottom: 1px solid #EEE;
  margin: 0;
  padding: 4px 15px 4px;
  position: relative;
  text-align: center;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  left: 0;
  -webkit-transition: left ease-in-out .18s;
  transition: left ease-in-out .18s;
}

.czr-in-control-cta-wrap {
  background-color: #8C8C8C;
  color: #fff;
  border-left: 0;
  border-right: 0;
  margin: 10px 0;
  padding: 10px 2%;
  position: relative;
  text-align: center;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  float: left;
  clear: both;
  width: 96%;
  -webkit-border-radius: 4px;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
  -moz-box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
  box-shadow: inset 0 1px 6px rgba(0, 0, 0, 0.65);
}

.czr-in-control-cta-wrap .czr-notice {
  font-weight: bold;
  color: #fff;
}
.czr-in-control-cta-wrap .czr-notice-ext-icon {
  font-size: 17px;
  text-decoration: none;
}
.czr-in-control-cta-wrap .czr-notice-inline-link {
  color: #fff;
  text-decoration: underline!important;
}
.czr-cta .czr-cta-btn:hover {
  color: #fff;
  background: #ed9c28;
  border-color: #d58512;
}

.czr-cta .czr-cta-btn {
  font-size: 15px;
  font-weight: 500;
  margin-top: 2px;
  padding: 4px 14px;
  display: inline-block;
  color: #fff;
  background: #f0ad4e;
  border: 1px solid #eea236;
  -webkit-box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.1);
  box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.1);
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  cursor: pointer;
  border-radius: 3px;
  -webkit-transition: all 0.2s ease-in-out;
  -moz-transition: all 0.2s ease-in-out;
  -o-transition: all 0.2s ease-in-out;
  -ms-transition: all 0.2s ease-in-out;
  transition: all 0.2s ease-in-out;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  line-height: 24px;
}

.czr-cta .czr-cta-btn:active {
  -webkit-box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
  box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
}

/* Maybe nove into common css */
/*
* Fix: wp 4.7 sticky section title and footer actions z-index
*/
.expanded .wp-full-overlay-footer,
#customize-controls .customize-section-title.is-in-view.is-sticky {
  z-index: 10000001;
}

/* ROOT PANEL : SEPARATE MENUS, WIDGETS AND ADVANCED OPTIONS */
.control-panel-nav_menus > .accordion-section-title, .control-panel-widgets > .accordion-section-title {
  margin: 0 0 10px;
}
      ';
    }

    //hook : customize_preview_init
    function czr_fn_add_preview_footer_action() {
      //Add the postMessages actions
      add_action( 'wp_footer', array( $this, 'czr_fn_extend_postmessage_cb' ), 1000 );
      add_action( 'wp_footer', array( $this, 'czr_fn_add_customize_preview_data' ) , 20 );

    }

    //hook : wp_footer in the preview
    function czr_fn_extend_postmessage_cb() {
      ?>
      <script id="preview-settings-cb" type="text/javascript">
        (function (api, $, _ ) {
              var $_body    = $( 'body' ),
                  $_brand   = $( '.brand' ),
                  $_header  = $( '.tc-header' ),
                  $_bmenu   = $_header.find('.btn-toggle-nav'),
                  $_sidenav = $( '#tc-sn' ),
                  setting_cbs = api.CZR_preview.prototype.setting_cbs || {},
                  subsetting_cbs = api.CZR_preview.prototype.subsetting_cbs || {},
                  _settings_cbs;



            _settings_cbs = {
                /******************************************
                * GLOBAL SETTINGS
                ******************************************/
                  'blogname' : function(to) {
                    $( 'a.site-title' ).text( to );
                  },
                  'blogdescription' : function(to) {
                    $( 'h2.site-description' ).text( to );
                  },
                  'tc_skin' : function( to ) {
                    if ( CZRPreviewParams && CZRPreviewParams.themeFolder ) {
                      //add a new link to the live stylesheet instead of replacing the actual skin link => avoid the flash of unstyle content during the skin load
                      var $skin_style_element = ( 0 === $('#live-skin-css').length ) ? $('<link>' , { id : 'live-skin-css' , rel : 'stylesheet'}) : $('#live-skin-css'),
                          skinName = to.replace('.css' , '.min.css'),
                          skinURL = [ CZRPreviewParams.themeFolder , '/inc/assets/css/' , skinName ].join('');

                      //check if the customSkin param is filtered
                      if ( CZRPreviewParams.customSkin && CZRPreviewParams.customSkin.skinName && CZRPreviewParams.customSkin.fullPath )
                        skinURL = to == CZRPreviewParams.customSkin.skinName ? CZRPreviewParams.customSkin.fullPath : skinURL;

                      $skin_style_element.attr('href' , skinURL );
                      if (  0 === $('#live-skin-css').length )
                        $('head').append($skin_style_element);
                    }
                  },
                  'tc_fonts' : function( to ) {
                    var font_groups = CZRPreviewParams.fontPairs;
                    $.each( font_groups , function( key, group ) {
                      if ( group.list[to]) {
                        if ( -1 != to.indexOf('_g_') )
                          _addGfontLink( group.list[to][1] );
                        _toStyle( group.list[to][1] );
                      }
                    });
                  },
                  'tc_body_font_size' : function( to ) {
                    var fontSelectors  = CZRPreviewParams.fontSelectors;
                    $( fontSelectors.body )/*.not('.social-icon')*/.css( {
                      'font-size' : to + 'px',
                      'line-height' : Number((to * 19 / 14).toFixed()) + 'px'
                    });
                  },
                  'tc_link_hover_effect' : function( to ) {
                    if ( false === to )
                      $_body.removeClass('tc-fade-hover-links');
                    else
                      $_body.addClass('tc-fade-hover-links');
                  },
                  'tc_ext_link_style' : function( to ) {
                    if ( false !== to ) {
                      $('a' , '.entry-content').each( function() {
                        var _thisHref = $.trim( $(this).attr('href'));
                        if( _is_external( _thisHref ) && 'IMG' != $(this).children().first().prop("tagName") ) {
                            $(this).after('<span class="tc-external">');
                        }
                      });
                    } else {
                      $( '.tc-external' , '.entry-content' ).remove();
                    }
                  },
                  'tc_ext_link_target' : function( to ) {
                    if ( false !== to ) {
                      $('a' , '.entry-content').each( function() {
                        var _thisHref = $.trim( $(this).attr('href'));
                        if( _is_external( _thisHref ) && 'IMG' != $(this).children().first().prop("tagName") ) {
                          $(this).attr('target' , '_blank');
                        }
                      });
                    } else {
                      $(this).removeAttr('target');
                    }
                  },
                  //All icons
                  'tc_show_title_icon' :  function( to ) {
                    if ( false === to ) {
                      $('.entry-title').add('h1').add('h2').removeClass('format-icon');
                      $('.tc-sidebar').add('.footer-widgets').addClass('no-widget-icons');
                    }
                    else {
                      $('.entry-title').add('h1').add('h2').addClass('format-icon');
                      $('.tc-sidebar').add('.footer-widgets').removeClass('no-widget-icons');
                    }
                  },
                  'tc_show_page_title_icon' : function( to ) {
                    //disable if grid customizer on
                    if ( $('.tc-gc').length )
                      return;

                    if ( false === to ) {
                      $('.entry-title' , '.page').removeClass('format-icon');
                    }
                    else {
                      $('.entry-title' , '.page').addClass('format-icon');
                    }
                  },
                  'tc_show_post_title_icon' : function( to ) {
                    if ( false === to ) {
                      $('.entry-title' , '.single').removeClass('format-icon');
                    }
                    else {
                      $('.entry-title' , '.single').addClass('format-icon');
                    }
                  },
                  'tc_show_archive_title_icon' : function( to ) {
                    //disable if grid customizer on
                    if ( $('.tc-gc').length )
                      return;
                    if ( false === to ) {
                      $('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').removeClass('format-icon');
                    }
                    else {
                      $('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').addClass('format-icon');
                    }
                  },
                  'tc_show_post_list_title_icon' : function( to ) {
                    //disable if grid customizer on
                    if ( $('.tc-gc').length )
                      return;

                    if ( false === to ) {
                      $('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').removeClass('format-icon');
                    }
                    else {
                      $('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').addClass('format-icon');
                    }
                  },
                  'tc_show_sidebar_widget_icon' : function( to ) {
                    if ( false === to )
                      $('.tc-sidebar').addClass('no-widget-icons');
                    else
                      $('.tc-sidebar').removeClass('no-widget-icons');
                  },
                  'tc_show_footer_widget_icon' : function( to ) {
                    if ( false === to )
                      $('.footer-widgets').addClass('no-widget-icons');
                    else
                      $('.footer-widgets').removeClass('no-widget-icons');
                  },
                  //Smooth Scroll
                  'tc_smoothscroll' : function(to) {
                    if ( false === to )
                      smoothScroll._cleanUp();
                    else
                      smoothScroll._maybeFire();
                  },
                /******************************************
                * HEADER
                ******************************************/
                  'tc_show_tagline' : function( to ) {
                    if ( false === to ) {
                      $('.site-description').hide('slow');
                      $(window).trigger('resize');
                    }
                    else {
                      $('.site-description').show('fast');
                      $(window).trigger('resize');
                    }
                  },
                  'tc_display_boxed_navbar' : function( to ) {
                    if ( false === to )
                      $_body.addClass('no-navbar');
                    else
                      $_body.removeClass('no-navbar');
                  },
                  'tc_header_layout' : function( to ) {
                          var _current_header_class = $_header.attr('class').match(/logo-(left|right|centered)/),
                              _current_bmenu_class, _current_brand_class;

                          if ( ! ( _current_header_class && _current_header_class[0] ) )
                            return;

                          _current_header_class = _current_header_class[0];

                          _current_bmenu_class  = 'logo-right' == _current_header_class ? 'pull-left' : 'pull-right';

                          $_header.removeClass( _current_header_class ).addClass( 'logo-' + to );
                          $_bmenu.removeClass( _current_bmenu_class ).addClass( 'right' == to ? 'pull-left' : 'pull-right');

                          if ( "centered" != to ){
                            _current_brand_class = 'logo-right' == _current_header_class ? 'pull-right' : 'pull-left';
                            $_brand.removeClass( _current_brand_class ).addClass( 'pull' + to );
                    }

                    setTimeout( function() {
                      $('.brand').trigger('resize');
                    } , 400);
                  },
                  'tc_menu_position' : function( to ) {
                    if ( 'aside' != api( api.CZR_preview.prototype._build_setId('tc_menu_style') ).get() ) {
                      if ( 'pull-menu-left' == to )
                        $('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
                      else
                        $('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
                    }

                    //sidenav
                    /*
                    * move the sidenav from the current position to the new one,
                    * this means change the sidenav class sn-left|right(-eventual_effect)
                    * If already open, before the replacement takes place, we close the sidenav,
                    * and simulate a click(touchstart) to re-open it afterwards
                    */
                    if (  $_sidenav.length > 0 ){
                      var _refresh            = false,
                          _current_class      = $_body.attr('class').match(/sn-(left|right)(-\w+|$|\s)/);

                      if ( ! ( _current_class && _current_class.length > 2 ) )
                        return;

                      if ( $_body.hasClass('tc-sn-visible') ) {
                          $_body.removeClass('tc-sn-visible');
                          _refresh = true;
                      }
                      $_body.removeClass( _current_class[0] ).
                             addClass( _current_class[0].replace( _current_class[1] , to.substr(10) ) ); // 10 = length of 'pull-menu-'
                      if ( _refresh ) {
                        setTimeout( function(){
                            $_bmenu.trigger('click').trigger('touchstart');
                        }, 200);
                      }
                    }
                  },
                  'tc_second_menu_position' : function(to) {
                    if ( 'pull-menu-left' == to )
                      $('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
                    else
                      $('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
                  },
                  'tc_menu_submenu_fade_effect' : function( to ) {
                    if ( false !== to )
                      $('.navbar-wrapper').addClass('tc-submenu-fade');
                    else
                      $('.navbar-wrapper').removeClass('tc-submenu-fade');
                  },
                  'tc_menu_submenu_item_move_effect' : function( to ) {
                    if ( false !== to )
                      $('.navbar-wrapper').addClass('tc-submenu-move');
                    else
                      $('.navbar-wrapper').removeClass('tc-submenu-move');
                  },
                  'tc_sticky_header' : function( to ) {
                    if ( false !== to ) {
                      $_body.addClass('tc-sticky-header').trigger('resize');
                      //$('#tc-reset-margin-top').css('margin-top' , '');
                    }
                    else {
                      $_body.removeClass('tc-sticky-header').trigger('resize');
                      $('#tc-reset-margin-top').css('margin-top' , '' );
                    }
                  },
                  'tc_sticky_show_tagline' : function( to ) {
                    if ( false !== to )
                      $_header.addClass('tc-tagline-on').removeClass('tc-tagline-off').trigger('resize');
                    else
                      $_header.addClass('tc-tagline-off').removeClass('tc-tagline-on').trigger('resize');
                  },
                  'tc_sticky_show_title_logo' : function( to ) {
                    if ( false !== to ) {
                      $_header.addClass('tc-title-logo-on').removeClass('tc-title-logo-off').trigger('resize');
                    }
                    else {
                      $_header.addClass('tc-title-logo-off').removeClass('tc-title-logo-on').trigger('resize');
                    }
                  },
                  'tc_sticky_shrink_title_logo' : function( to ) {
                    if ( false !== to )
                      $_header.addClass('tc-shrink-on').removeClass('tc-shrink-off').trigger('resize');
                    else
                      $_header.addClass('tc-shrink-off').removeClass('tc-shrink-on').trigger('resize');
                  },
                  'tc_sticky_show_menu' : function( to ) {
                    if ( false !== to )
                      $_header.addClass('tc-menu-on').removeClass('tc-menu-off').trigger('resize');
                    else
                      $_header.addClass('tc-menu-off').removeClass('tc-menu-on').trigger('resize');
                  },
                  'tc_sticky_z_index' : function( to ) {
                    $('.tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header').css('z-index' , to);
                  },
                  'tc_sticky_transparent_on_scroll' : function( to ) {
                    if ( false !== to ) {
                      $_body.addClass('tc-transparent-on-scroll');
                      $_body.removeClass('tc-solid-color-on-scroll');
                    }
                    else {
                      $_body.removeClass('tc-transparent-on-scroll');
                      $_body.addClass('tc-solid-color-on-scroll');
                    }
                  },
                  'tc_woocommerce_header_cart_sticky' : function( to ) {
                    if ( false !== to )
                      $_header.addClass('tc-wccart-on').removeClass('tc-wccart-off').trigger('resize');
                    else
                      $_header.addClass('tc-wccart-off').removeClass('tc-wccart-on').trigger('resize');
                  },
                /******************************************
                * SLIDER
                ******************************************/
                  'tc_slider_default_height' : function( to ) {
                    $('#customizr-slider').addClass('custom-slider-height');
                    $('.carousel > .item').css('line-height' , to + 'px').css('max-height', to + 'px').css('min-height', to + 'px').trigger('resize');
                    $('.tc-slider-controls').css('line-height' , to + 'px').css('max-height', to + 'px').trigger('resize');
                  },
                /******************************************
                * FEATURED PAGES
                ******************************************/
                  'tc_featured_text_one' : function( to ) {
                    $( '.widget-front p.fp-text-one' ).html( to );
                  },
                  'tc_featured_text_two' : function( to ) {
                    $( '.widget-front p.fp-text-two' ).html( to );
                  },
                  'tc_featured_text_three' : function( to ) {
                    $( '.widget-front p.fp-text-three' ).html( to );
                  },
                  'tc_featured_page_button_text' : function( to ) {
                    if ( to )
                        $( '.fp-button' ).html( to ).removeClass( 'hidden');
                    else
                        $( '.fp-button' ).addClass( 'hidden' );
                  },
                /******************************************
                * POST METAS
                ******************************************/
                 'tc_show_post_metas' : function( to ) {
                    var $_entry_meta = $('.entry-header .entry-meta', '.article-container');

                    if ( false === to )
                      $_entry_meta.hide('slow');
                          else if (! $_body.hasClass('hide-post-metas') ){
                      $_entry_meta.show('fast');
                              $_body.removeClass('hide-all-post-metas');
                          }
                  },
                  'tc_post_metas_update_notice_text' : function( to ) {
                    $( '.tc-update-notice' ).html( to );
                  },
                  'tc_post_metas_update_notice_format' : function( to ) {
                    $( '.tc-update-notice').each( function() {
                      var classes = $(this).attr('class').split(' ');
                      for (var key in classes) {
                        if ( -1 !== (classes[key]).indexOf('label-') ) {
                          classes.splice(key, 1);
                        }
                      }
                      //rebuild the class attr
                      $(this).attr('class' , classes.join(' ') );
                    });
                    $( '.tc-update-notice' ).addClass( to );
                  },
                /******************************************
                * POST NAVIGATION
                ******************************************/
                  'tc_show_post_navigation' : function( to ) {
                    var $_post_nav = $( '#nav-below' );
                    if ( false === to )
                      $_post_nav.hide('slow');
                          else if ( ! $_post_nav.hasClass('hide-post-navigation') )
                      $_post_nav.removeClass('hide-all-post-navigation').show('fast');
                  },
                /******************************************
                * POST THUMBNAILS
                ******************************************/
                  'tc_post_list_thumb_height' : function( to ) {
                    $('.tc-rectangular-thumb').css('max-height' , to + 'px');
                    if ( 0 !== $('.tc-rectangular-thumb').find('img').length )
                      $('.tc-rectangular-thumb').find('img').trigger('refresh-height');//listened by the jsimgcentering $ plugin
                  },
                  'tc_single_post_thumb_height' : function( to ) {
                    $('.tc-rectangular-thumb').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
                  },
                /******************************************
                * SOCIALS
                ******************************************/
                  'tc_social_in_header' : function( to ) {
                    if ( false === to ) {
                      $('.tc-header .social-block').hide('slow');
                      $(window).trigger('resize');
                    }
                    else {
                      $('.tc-header .social-block').show('fast');
                      $(window).trigger('resize');
                    }
                  },
                  'tc_social_in_footer' : function( to ) {
                    if ( false === to ) {
                      $('.tc-footer-social-links-wrapper' , '#footer').hide('slow');
                      $(window).trigger('resize');
                    }
                    else {
                      $('.tc-footer-social-links-wrapper' , '#footer').show('fast');
                      $(window).trigger('resize');
                    }
                  },
                  'tc_social_in_left-sidebar' : function( to ) {
                    if ( false === to ) {
                      $('#left .social-block' , '.tc-sidebar').hide('slow');
                      $(window).trigger('resize');
                    }
                    else {
                      $('#left .social-block' , '.tc-sidebar').show('fast');
                      $(window).trigger('resize');
                    }
                  },
                  'tc_social_in_right-sidebar' : function( to ) {
                    if ( false === to ) {
                      $('#right .social-block' , '.tc-sidebar').hide('slow');
                      $(window).trigger('resize');
                    }
                    else {
                      $('#right .social-block' , '.tc-sidebar').show('fast');
                      $(window).trigger('resize');
                    }
                  },
                  'tc_social_in_sidebar_title' : function( to ) {
                    $( '.social-block .widget-title' , '.tc-sidebar' ).html( to );
                    if ( ! to )
                      $('.social-block' , '.tc-sidebar').hide('slow');
                    else
                      $('.social-block' , '.tc-sidebar').show('fast');
                  },
                /******************************************
                * GRID
                ******************************************/
                  'tc_grid_shadow' : function( to ) {
                    if ( false !== to )
                      $('.article-container').addClass('tc-grid-shadow');
                    else
                      $('.article-container').removeClass('tc-grid-shadow');
                  },
                  'tc_grid_bottom_border' : function( to ) {
                    if ( false !== to )
                      $('.article-container').addClass('tc-grid-border');
                    else
                      $('.article-container').removeClass('tc-grid-border');
                  },
                  'tc_grid_icons' : function( to ) {
                    if ( false === to )
                      $('.tc-grid-icon').each( function() { $(this).fadeOut(); } );
                    else
                      $('.tc-grid-icon').each( function() { $(this).fadeIn(); } );
                  },
                /******************************************
                * GALLERY
                ******************************************/
                  'tc_gallery_style' : function( to ) {
                    if ( false !== to )
                      $('.article-container').addClass('tc-gallery-style');
                    else
                      $('.article-container').removeClass('tc-gallery-style');
                  },
                /******************************************
                * COMMENTS
                ******************************************/
                  'tc_comment_bubble_color' : function( to ) {
                    $('#custom-bubble-color').remove();
                    var $style_element  = $('<style>' , { id : 'custom-bubble-color'}),
                      bubble_live_css = '';

                    //custom bubble
                    bubble_live_css += '.comments-link .tc-comment-bubble {border-color:' + to + ';color:' + to + '}';
                    bubble_live_css += '.comments-link .tc-comment-bubble:before {border-color:' + to + '}';
                    $('head').append($style_element.html(bubble_live_css));
                  },
                /******************************************
                * FOOTER
                ******************************************/
                  'tc_sticky_footer' : function( to ) {
                    if ( false !== to )
                      $_body.addClass('tc-sticky-footer').trigger('refresh-sticky-footer');
                    else
                      $_body.removeClass('tc-sticky-footer');
                  },
                  'tc_back_to_top_position' : function( to ) {
                    $_el = $( '#tc-footer-btt-wrapper' );
                    $_el.removeClass( "left right" ).addClass( to );
                  },
                /******************************************
                * CUSTOM CSS
                ******************************************/
                  'tc_custom_css' : function( to ) {
                    $('#option-custom-css').remove();
                    var $style_element = ( 0 === $('#live-custom-css').length ) ? $('<style>' , { id : 'live-custom-css'}) : $('#live-custom-css');
                    //sanitize string => remove html tags
                    to = to.replace(/(<([^>]+)>)/ig,"");

                    if (  0 === $('#live-custom-css').length )
                      $('head').append($style_element.html(to));
                    else
                      $style_element.html(to);
                  }
              };
              /** DYNAMIC CALLBACKS **/
              var _post_metas_context = [
                { _context : 'home', _container : '.home' },
                { _context : 'single_post', _container: '.single'},
                { _context : 'post_lists', _container: 'body:not(.single, .home)'}
              ];

              //add callbacks dynamically
              $.each( _post_metas_context, function() {
                var $_post_metas = $('.entry-header .entry-meta', this._container + ' .article-container' );

                if ( false === $_post_metas.length > 0 )
                  return;

                _settings_cbs['tc_show_post_metas_' + this._context] = function( to ) {
                  if ( false === to ){
                    $_post_metas.hide('slow');
                    $_body.addClass('hide-post-metas');
                  }else{
                    $_post_metas.show('fast');
                    $_body.removeClass('hide-post-metas');
                  }
                };//fn

                return false;
              }); /* end contextual post metas*/

              var _post_nav_context = [
                { _context : 'page', _container : 'body.page' },
                { _context : 'home', _container : 'body.blog.home' },
                { _context : 'single', _container: 'body.single' },
                { _context : 'archive', _container: 'body.archive' }
              ];

              //add callbacks dynamically
              $.each( _post_nav_context, function() {
                var $_post_nav = $('#nav-below', this._container );

                if ( false === $_post_nav.length > 0 )
                  return;

                _settings_cbs[ 'tc_show_post_navigation_' + this._context ] = function( to ) {
                  if ( false === to )
                    $_post_nav.hide('slow').addClass('hide-post-navigation');
                  else
                    $_post_nav.show('fast').removeClass('hide-post-navigation');
                };//fn
                return false;
              });

              $.extend( api.CZR_preview.prototype, {
                  setting_cbs : $.extend( setting_cbs, _settings_cbs )
              });

            /******************************************
            * HELPERS
            ******************************************/
            //EXT LINKS HELPERS
              var _url_comp     = (location.host).split('.'),
                  _nakedDomain  = new RegExp( _url_comp[1] + "." + _url_comp[2] );
            //FONTS HELPER
              var _addGfontLink = function(fonts ) {
                var gfontUrl        = ['//fonts.googleapis.com/css?family='];
                gfontUrl.push(fonts);
                if ( 0 === $('link#gfontlink' ).length ) {
                    $gfontlink = $('<link>' , {
                      id    : 'gfontlink' ,
                      href  : gfontUrl.join(''),
                      rel   : 'stylesheet',
                      type  : 'text/css'
                    });

                    $('link:last').after($gfontlink);
                }
                else {
                  $('link#gfontlink' ).attr('href', gfontUrl.join('') );
                }
              };

              var _toStyle = function( fonts ) {
                var selector_fonts = fonts.split('|');
                $.each( selector_fonts , function( key, single_font ) {
                  var split         = single_font.split(':'),
                      css_properties = {},
                      font_family, font_weight = '',
                      fontSelectors  = CZRPreviewParams.fontSelectors;

                  css_properties = {
                    'font-family' : (split[0]).replace(/[\+|:]/g, ' '),
                    'font-weight' : split[1] ? split[1] : 'inherit'
                  };
                  switch (key) {
                    case 0 : //titles font
                      $(fontSelectors.titles).css( css_properties );
                    break;

                    case 1 ://body font
                      $(fontSelectors.body).css( css_properties );
                    break;
                  }
                });
              };
        }) ( wp.customize, jQuery, _);
      </script>
      <?php
    }




    //hook : wp_footer in the preview
    function czr_fn_add_customize_preview_data() {
      global $wp_query, $wp_customize;

      $_wp_conditionals = array();

      //export only the conditional tags
      foreach( (array)$wp_query as $prop => $val ) {
        if (  false === strpos($prop, 'is_') )
          continue;
        if ( 'is_home' == $prop )
          $val = CZR_utils::$inst->czr_fn_is_home();

        $_wp_conditionals[$prop] = $val;
      }

      ?>
        <script type="text/javascript" id="czr-customizer-data">
          (function ( _export ){
            _export.czr_wp_conditionals = <?php echo wp_json_encode( $_wp_conditionals ) ?>;
          })( _wpCustomizeSettings );
        </script>
      <?php
    }



    //hook : 'customize_controls_enqueue_scripts':10
    function czr_fn_extend_ctrl_dependencies() {
      ?>
      <script id="control-dependencies" type="text/javascript">
        (function (api, $, _) {
          //@return boolean
          var _is_checked = function( to ) {
                  return 0 !== to && '0' !== to && false !== to && 'off' !== to;
          };
          //when a dominus object define both visibility and action callbacks, the visibility can return 'unchanged' for non relevant servi
          //=> when getting the visibility result, the 'unchanged' value will always be checked and resumed to the servus control current active() state
          api.CZR_ctrlDependencies.prototype.dominiDeps = _.extend(
                api.CZR_ctrlDependencies.prototype.dominiDeps,
                [
                    {
                          //we have to show restrict blog/home posts when
                          //1. show page on front and a page of posts is selected
                          //2, show posts on front
                            dominus : 'page_for_posts',
                            servi   : ['tc_blog_restrict_by_cat'],
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'show_on_front',
                            servi   : ['tc_blog_restrict_by_cat', 'tc_show_post_navigation_home'],
                            visibility : function( to, servusShortId ) {
                                  //not sure the cross dependency actually works ... :/
                                  //otherwise this shouldn't be needed ... right?
                                  if ( 'tc_show_post_navigation_home' == servusShortId ) {
                                    return ( 'posts' == to  ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_post_navigation' ) ).get() );
                                  }
                                  if ( 'posts' == to ) {
                                    return true;
                                  }
                                  if ( 'page' == to && 'tc_blog_restrict_by_cat' == servusShortId ) { //show cat picker also if a page for posts is set
                                    return _is_checked( api( api.CZR_Helpers.build_setId( 'page_for_posts' ) ).get() );
                                  }
                                  return false;
                            },
                    },
                    {
                            dominus : 'tc_logo_upload',
                            servi   : ['tc_logo_resize'],
                            visibility : function( to ) {
                                  return _.isNumber( to );
                            },
                    },
                    {
                            dominus : 'tc_show_featured_pages',
                            servi   : serverControlParams.FPControls,
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'tc_front_slider',
                            servi   : [
                              'tc_slider_width',
                              'tc_slider_delay',
                              'tc_slider_default_height',
                              'tc_slider_default_height_apply_all',
                              'tc_slider_change_default_img_size',
                              'tc_posts_slider_number',
                              'tc_posts_slider_stickies',
                              'tc_posts_slider_title',
                              'tc_posts_slider_text',
                              'tc_posts_slider_link',
                              'tc_posts_slider_button_text',
                              'tc_posts_slider_restrict_by_cat' //pro-bundle
                            ],
                            visibility : function( to, servusShortId ) {
                                  //posts slider options must be hidden when the posts slider not choosen
                                  if ( servusShortId.indexOf('tc_posts_slider_') > -1 ) {
                                    return 'tc_posts_slider' == to;
                                  }

                                  return _is_checked( to );
                            },
                            actions : function( to, servusShortId ) {
                                 //if user selects the post slider option, append a notice in the label element
                                 //and hide the notice when no sliders have been created yet
                                 var $_front_slider_container = api.control( api.CZR_Helpers.build_setId('tc_front_slider') ).container,
                                     $_label = $( 'label' , $_front_slider_container ),
                                     $_empty_sliders_notice = $( 'div.czr-notice', $_front_slider_container);

                                  if ( 'tc_posts_slider' == to ) {
                                    if ( 0 !== $_label.length && ! $('.czr-notice' , $_label ).length ) {
                                      var $_notice = $('<span>', { class: 'czr-notice', html : serverControlParams.translatedStrings.postSliderNote || '' } );
                                      $_label.append( $_notice );
                                    }
                                    else {
                                      $('.czr-notice' , $_label ).show();
                                    }

                                    //hide no sliders created notice
                                    if ( 0 !== $_empty_sliders_notice.length ) {
                                      $_empty_sliders_notice.hide();
                                    }
                                  }
                                  else {
                                    if ( 0 !== $( '.czr-notice' , $_label ).length )
                                      $( '.czr-notice' , $_label ).hide();
                                    if ( 0 !== $_empty_sliders_notice.length )
                                      $_empty_sliders_notice.show();
                                  }
                            }
                    },
                    {
                            dominus : 'tc_slider_default_height',
                            servi   : ['tc_slider_default_height_apply_all', 'tc_slider_change_default_img_size'],
                            visibility : function( to ) {
                                  //slider height options must be hidden is height = default height (500px), unchanged by user
                                  var _defaultHeight = serverControlParams.defaultSliderHeight || 500;
                                  return _defaultHeight != to;
                            },
                    },
                    {
                            dominus : 'tc_posts_slider_link',
                            servi   : ['tc_posts_slider_button_text'],
                            visibility : function( to ) {
                                  return to.indexOf('cta') > -1;
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_shape',
                            servi   : ['tc_post_list_thumb_height'],
                            visibility : function( to ) {
                                  return to.indexOf('rectangular') > -1;
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_position',
                            servi   : ['tc_post_list_thumb_alternate'],
                            visibility : function( to ) {
                                  return _.contains( [ 'left', 'right'], to );
                            },
                    },
                    {
                            dominus : 'tc_post_list_show_thumb',
                            servi   : [
                              'tc_post_list_use_attachment_as_thumb',
                              'tc_post_list_default_thumb',
                              'tc_post_list_thumb_shape',
                              'tc_post_list_thumb_alternate',
                              'tc_post_list_thumb_position',
                              'tc_post_list_thumb_height',
                              'tc_grid_thumb_height'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_grid_thumb_height' == servusShortId ) {
                                    //cross
                                    return _is_checked(to)
                                        && $('.tc-grid-toggle-controls').hasClass('open')
                                        && 'grid' == api( api.CZR_Helpers.build_setId( 'tc_post_list_grid' ) ).get();
                                  }
                                  return _is_checked(to) ;
                            },
                    },
                    {
                            dominus : 'tc_post_list_grid',
                            servi   : [
                              'tc_grid_columns',
                              'tc_grid_expand_featured',
                              'tc_grid_in_blog',
                              'tc_grid_in_archive',
                              'tc_grid_in_search',
                              'tc_grid_thumb_height',
                              'tc_grid_bottom_border',
                              'tc_grid_shadow',
                              'tc_grid_icons',
                              'tc_grid_num_words',
                              'tc_post_list_grid',//trick, see the actions
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId )
                                      return true;

                                  if ( _.contains( serverControlParams.gridDesignControls, servusShortId ) ) {
                                      _bool =  $('.tc-grid-toggle-controls').hasClass('open') && 'grid' == to;

                                      if ( 'tc_grid_thumb_height' == servusShortId ) {
                                        //cross
                                          return _bool && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_post_list_show_thumb' ) ).get() );
                                      }
                                      return _bool;
                                  }
                                  return 'grid' == to;
                            },
                            actions : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId ) {
                                      $('.tc-grid-toggle-controls').toggle( 'grid' == to );
                                  }
                            }
                    },
                    {
                            dominus : 'tc_breadcrumb',
                            servi   : [
                              'tc_show_breadcrumb_home',
                              'tc_show_breadcrumb_in_pages',
                              'tc_show_breadcrumb_in_single_posts',
                              'tc_show_breadcrumb_in_post_lists'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_title_icon',
                            servi   : [
                              'tc_show_page_title_icon',
                              'tc_show_post_title_icon',
                              'tc_show_archive_title_icon',
                              'tc_show_post_list_title_icon',
                              'tc_show_sidebar_widget_icon',
                              'tc_show_footer_widget_icon'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas',
                            servi   : [
                              'tc_show_post_metas_home',
                              'tc_post_metas_design',
                              'tc_show_post_metas_single_post',
                              'tc_show_post_metas_post_lists',
                              'tc_show_post_metas_categories',
                              'tc_show_post_metas_tags',
                              'tc_show_post_metas_publication_date',
                              'tc_show_post_metas_update_date',
                              'tc_post_metas_update_notice_text',
                              'tc_post_metas_update_notice_interval',
                              'tc_show_post_metas_author'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas_update_date',
                            servi   : ['tc_post_metas_update_date_format'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_post_metas_update_notice_in_title',
                            servi   : [
                              'tc_post_metas_update_notice_text',
                              'tc_post_metas_update_notice_format',
                              'tc_post_metas_update_notice_interval'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_post_list_length',
                            servi   : ['tc_post_list_excerpt_length'],
                            visibility: function (to) {
                                  return 'excerpt' == to;
                            }
                    },
                    {
                            dominus : 'tc_sticky_show_title_logo',
                            servi   : ['tc_sticky_logo_upload'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_sticky_header',
                            servi   : [
                              'tc_sticky_show_tagline',
                              'tc_sticky_show_title_logo',
                              'tc_sticky_shrink_title_logo',
                              'tc_sticky_show_menu',
                              'tc_sticky_transparent_on_scroll',
                              'tc_sticky_logo_upload',
                              'tc_woocommerce_header_cart_sticky'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_woocommerce_header_cart_sticky' == servusShortId ) {
                                    return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_woocommerce_header_cart' ) ).get() );
                                  }
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_woocommerce_header_cart',
                            servi   : ['tc_woocommerce_header_cart_sticky'],
                            visibility: function (to) {
                                  return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() );
                            }
                    },
                    {
                            dominus : 'tc_comment_bubble_color_type',
                            servi   : ['tc_comment_bubble_color'],
                            visibility: function (to) {
                                  return 'custom' == to;
                            }
                    },
                    {
                            dominus : 'tc_comment_show_bubble',
                            servi   : [
                              'tc_comment_bubble_shape',
                              'tc_comment_bubble_color_type',
                              'tc_comment_bubble_color'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_comment_bubble_color' == servusShortId ) {
                                    return _is_checked(to) && 'custom' == api( api.CZR_Helpers.build_setId( 'tc_comment_bubble_color_type' ) ).get();
                                  }
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_dropcap',
                            servi   : [
                              'tc_dropcap_minwords',
                              'tc_dropcap_design',
                              'tc_post_dropcap',
                              'tc_page_dropcap'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_gallery',
                            servi   : [
                              'tc_gallery_fancybox',
                              'tc_gallery_style',
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_skin_random',
                            servi   : [
                              'tc_skin',
                            ],
                            visibility: function() {
                              return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_skin_select = api.control( api.CZR_Helpers.build_setId(servusShortId) ).container;
                                  $_skin_select.find('select').prop('disabled', '1' == to ? 'disabled' : '' );
                            },
                    },
                    {
                            dominus : 'tc_show_post_navigation',
                            servi   : [
                              'tc_show_post_navigation_page',
                              'tc_show_post_navigation_home',
                              'tc_show_post_navigation_single',
                              'tc_show_post_navigation_archive'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_display_second_menu',
                            servi   : [
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_position',
                              'tc_second_menu_resp_setting',
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect'
                            ],
                            visibility : function( to, servusShortId ) {
                                  var _menu_style_val = api( api.CZR_Helpers.build_setId( 'tc_menu_style' )).get();
                                  if ( _.contains( ['nav_menu_locations[secondary]', 'tc_second_menu_resp_setting'], servusShortId ) )
                                    return _is_checked(to) && 'aside' == _menu_style_val;
                                  //effects common to regular menu and second horizontal menu
                                  if ( _.contains( ['tc_menu_submenu_fade_effect', 'tc_menu_submenu_item_move_effect'], servusShortId ) )
                                    return ( _is_checked(to) && 'aside' == _menu_style_val ) || ( !_is_checked(to) && 'aside' != _menu_style_val );
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_menu_style',
                            servi   : [
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect',
                              'tc_menu_resp_dropdown_limit_to_viewport',
                              'tc_display_menu_label',
                              'tc_display_second_menu',
                              'tc_second_menu_position',
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_resp_setting',
                              'tc_menu_position', /* used to perform actions on menu position */
                              'tc_mc_effect', /* pro */
                            ],
                            //if the second menu is activated, only the tc_menu_resp_dropdown_limit_to_viewport is hidden
                            //otherwise all of them are hidden
                            visibility : function( to, servusShortId ) {
                                  //CASE 1 : regular menu choosen
                                  if ( 'aside' != to ) {
                                    if ( _.contains([
                                        'tc_display_menu_label',
                                        'tc_display_second_menu',
                                        'nav_menu_locations[secondary]',
                                        'tc_second_menu_position',
                                        'tc_second_menu_resp_setting',
                                        'tc_mc_effect'] , servusShortId ) ) {
                                      return false;
                                    } else {
                                      return true;
                                    }
                                  }
                                  //CASE 2 : side menu choosen
                                  else {
                                    if ( _.contains([
                                      'tc_menu_type',
                                      'tc_menu_submenu_fade_effect',
                                      'tc_menu_submenu_item_move_effect',
                                      'nav_menu_locations[secondary]',
                                      'tc_second_menu_position',
                                      'tc_second_menu_resp_setting'],
                                      servusShortId ) ) {
                                        return _is_checked( api( api.CZR_Helpers.build_setId('tc_display_second_menu') ).get() );
                                    }
                                    else if ( 'tc_menu_resp_dropdown_limit_to_viewport' == servusShortId ){
                                      return false;
                                    }
                                    return true;
                                  }
                            },
                            actions : function( to, servusShortId ) {
                                  if ( 'tc_menu_position' == servusShortId ) {
                                      var _header_layout            = api(api.CZR_Helpers.build_setId('tc_header_layout')).get();
                                          wpMenuPositionSettingID   = api.CZR_Helpers.build_setId(servusShortId);

                                      api( wpMenuPositionSettingID ).set( 'right' == _header_layout ? 'pull-menu-left' : 'pull-menu-right' );
                                      //refresh the selecter
                                      api.control(wpMenuPositionSettingID).container.find('select').selecter('destroy').selecter({});
                                  }
                            }
                    },
                    {
                            //when user switches layout, make sure the menu is correctly aligned by default.
                            dominus : 'tc_header_layout',
                            servi   : ['tc_menu_position'],
                            visibility: function (to) {
                                  return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var wpMenuPositionSettingID = api.CZR_Helpers.build_setId(servusShortId);
                                  api( wpMenuPositionSettingID ).set( 'right' == to ? 'pull-menu-left' : 'pull-menu-right' );
                                  //refresh the selecter
                                  api.control(wpMenuPositionSettingID).container.find('select').selecter('destroy').selecter({});
                            }
                    },
                    {
                            //when user switches layout, make sure the menu is correctly aligned by default.
                            dominus : 'tc_hide_all_menus',
                            servi   : ['tc_hide_all_menus'],
                            visibility: function (to) {
                                  return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_nav_section_container = api.section('nav').container,
                                      $_controls = $_nav_section_container.find('li.customize-control').not( api.control(api.CZR_Helpers.build_setId(servusShortId)).container );
                                  $_controls.each( function() {
                                    if ( $(this).is(':visible') )
                                      $(this).fadeTo( 500 , true === to ? 0.5 : 1).css('pointerEvents', true === to ? 'none' : ''); //.fadeTo() duration, opacity, callback
                                  });//$.each()
                            }
                    },
                    {
                            dominus : 'tc_show_back_to_top',
                            servi   : ['tc_back_to_top_position'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                ]//dominiDeps {}
          );//_.extend()

        }) ( wp.customize, jQuery, _);
      </script>
      <?php
    }

    function czr_fn_add_various_dom_ready_actions() {
      ?>
      <script id="control-various-dom-ready" type="text/javascript">
        (function (wp, $) {
            $( function($) {
                /* GRID */
                var _build_control_id = function( _control ) {
                  return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
                };

                var _get_grid_design_controls = function() {
                  return $( serverControlParams.gridDesignControls.map( function( _control ) {
                    return _build_control_id( _control );
                  }).join(',') );
                };

                //hide design controls on load
                $( _get_grid_design_controls() ).addClass('tc-grid-design').hide();

                $('.tc-grid-toggle-controls').on( 'click', function() {
                  $( _get_grid_design_controls() ).slideToggle('fast');
                  $(this).toggleClass('open');
                } );

                /* ADD GOOGLE IN TITLE */
                $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : '//www.google.com/images/logos/google_logo_41.png' , height : 20 });
                $('#accordion-section-fonts_sec').prepend($g_logo);


                //http://ivaynberg.github.io/select2/#documentation
                $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintSkinOptionElement,
                    templateSelection: paintSkinOptionElement,
                    escapeMarkup: function(m) { return m; }
                }).on("select2-highlight", function(e) { //<- doesn't work with recent select2 and it doesn't provide alternatives :(
                  //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
                 $(this).select2("val" , e.val, true );
                });
                //Skins handled with select2
                function paintSkinOptionElement(state) {
                    if (!state.id) return state.text; // optgroup
                    return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
                }

                //FONTS
                $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintFontOptionElement,
                    templateSelection: paintFontOptionElement,
                    escapeMarkup: function(m) { return m; },
                }).on("select2-highlight", function(e) {//<- doesn't work with recent select2 and it doesn't provide alternatives :(
                  //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
                  $(this).select2("val" , e.val, true );
                });

                function paintFontOptionElement(state) {
                    if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
                      return '<img class="tc-google-logo" src="//www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
                    else if ( ! state.id )
                      return state.text;// optgroup different than google font
                    return '<span class="tc-select2-font">' + state.text + '</span>';
                }


//CALL TO ACTIONS
                /* CONTRIBUTION TO CUSTOMIZR */
                var donate_displayed  = false,
                    is_pro            = 'customizr-pro' == serverControlParams.themeName;
                if (  ! serverControlParams.HideDonate && ! is_pro ) {
                  _render_donate_block();
                  donate_displayed = true;
                }

                //Main call to action
                if ( serverControlParams.ShowCTA && ! donate_displayed && ! is_pro ) {
                 _render_main_cta();
                }

                //In controls call to action
                if ( ! is_pro ) {
                  _render_wfc_cta();
                  _render_fpu_cta();
                  _render_footer_cta();
                  _render_gc_cta();
                  _render_mc_cta();
                }
                //_render_rate_czr();

                function _render_rate_czr() {
                  var _cta = _.template(
                      $( "script#rate-czr" ).html()
                  );
                  $('#customize-footer-actions').append( _cta() );
                }

                function _render_donate_block() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var donate_template = _.template(
                      $( "script#donate_template" ).html()
                  );

                  $('#customize-info').after( donate_template() );

                   //BIND EVENTS
                  $('.czr-close-request').click( function(e) {
                    e.preventDefault();
                    $('.donate-alert').slideToggle("fast");
                    $(this).hide();
                  });

                  $('.czr-hide-donate').click( function(e) {
                    _ajax_save();
                    setTimeout(function(){
                        $('#czr-donate-customizer').slideToggle("fast");
                    }, 200);
                  });

                  $('.czr-cancel-hide-donate').click( function(e) {
                    $('.donate-alert').slideToggle("fast");
                    setTimeout(function(){
                        $('.czr-close-request').show();
                    }, 200);
                  });
                }//end of donate block


                function _render_main_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#main_cta" ).html()
                  );
                  $('#customize-info').after( _cta() );
                }

                function _render_wfc_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#wfc_cta" ).html()
                  );
                  $('li[id*="tc_body_font_size"]').append( _cta() );
                }

                function _render_fpu_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#fpu_cta" ).html()
                  );
                  $('li[id*="tc_featured_text_three"]').append( _cta() );
                }

                function _render_gc_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#gc_cta" ).html()
                  );
                  $('li[id*="tc_post_list_show_thumb"] > .czr-customizr-title').before( _cta() );
                }

                function _render_mc_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#mc_cta" ).html()
                  );
                  $('li[id*="tc_theme_options-tc_display_menu_label"]').append( _cta() );
                }

                function _render_footer_cta() {
                  // Grab the HTML out of our template tag and pre-compile it.
                  var _cta = _.template(
                      $( "script#footer_cta" ).html()
                  );
                  $('li[id*="tc_show_back_to_top"]').closest('ul').append( _cta() );
                }

                function _ajax_save() {
                    var AjaxUrl         = serverControlParams.AjaxUrl,
                    query = {
                        action  : 'hide_donate',
                        TCnonce :  serverControlParams.TCNonce,
                        wp_customize : 'on'
                    },
                    request = $.post( AjaxUrl, query );
                    request.done( function( response ) {
                        // Check if the user is logged out.
                        if ( '0' === response ) {
                            return;
                        }
                        // Check for cheaters.
                        if ( '-1' === response ) {
                            return;
                        }
                    });
                }//end of function
//END OF CTA
            });
        }) ( wp, jQuery );
      </script>
      <?php
    }


    function czr_fn_get_translated_strings() {
      return apply_filters('controls_translated_strings',
          array(
                'edit' => __('Edit', 'customizr'),
                'close' => __('Close', 'customizr'),
                'faviconNote' => __( "Your favicon is currently handled with an old method and will not be properly displayed on all devices. You might consider to re-upload your favicon with the new control below." , 'customizr'),
                'notset' => __('Not set', 'customizr'),
                'rss' => __('Rss', 'customizr'),
                'selectSocialIcon' => __('Select a social icon', 'customizr'),
                'followUs' => __('Follow us on', 'customizr'),
                'successMessage' => __('Done !', 'customizr'),
                'socialLinkAdded' => __('New Social Link created ! Scroll down to edit it.', 'customizr'),
                'readDocumentation' => __('Learn more about this in the documentation', 'customizr'),
                //WP TEXT EDITOR MODULE
                'textEditorOpen' => __('Edit', 'customizr'),
                'textEditorClose' => __('Close Editor', 'customizr'),
                //SLIDER MODULE
                'slideAdded'   => __('New Slide created ! Scroll down to edit it.', 'customizr'),
                'slideTitle'   => __( 'Slide', 'customizr'),
                'postSliderNote' => __( "This option generates a home page slider based on your last posts, starting from the most recent or the featured (sticky) post(s) if any.", "customizr" ),
          )
      );
    }

  }
endif;
?><?php
/**
* Add controls to customizer
*
*/
if ( ! class_exists( 'CZR_controls' ) ) :
  class CZR_controls extends WP_Customize_Control  {
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

      static $enqueued_resources;

      public function render_content()  {
        do_action( '__before_setting_control' , $this -> id );

        switch ( $this -> type) {
            case 'hr':
              echo '<hr class="czr-customizer-separator" />';
            break;


            case 'title' :
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php if (isset( $this->notice)) : ?>
              <i class="czr-notice"><?php echo $this -> notice ?></i>
             <?php endif; ?>

            <?php
            break;

            case 'select':
              if ( empty( $this->choices ) )
                return;
              ?>
              <?php if (!empty( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php $this -> czr_fn_print_select_control( in_array( $this->id, array( 'tc_theme_options[tc_fonts]', 'tc_theme_options[tc_skin]' ) ) ? 'select2 no-selecter-js' : '' ) ?>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this -> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              if ( 'tc_theme_options[tc_front_slider]' == $this -> id ) {
                //retrieve all sliders in option array
                $sliders          = CZR_utils::$inst -> czr_fn_opt( 'tc_sliders' );

                if ( empty( $sliders ) ) {
                  printf('<div class="czr-notice" style="width:99%; padding: 5px;"><p class="description">%1$s<br/><a class="button-primary" href="%2$s" target="_blank">%3$s</a><br/><span class="tc-notice">%4$s <a href="%5$s" title="%6$s" target="_blank">%6$s</a></span></p>',
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
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="czr-number-label customize-control-title"><?php echo $this->label ?></span>
                <input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="czr-number-input small-text">
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this-> notice ?></span>
                <?php endif; ?>
              </label>
              <?php
              break;

            case 'checkbox':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <?php
                    printf('<div class="czr-check-label"><label><span class="customize-control-title">%1$s</span></label></div>',
                    $this->label
                  );
              ?>
              <input <?php $this->link(); ?> type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>"  <?php czr_fn_checked( $this->value() ); ?> />

              <?php if(!empty( $this -> notice)) : ?>
               <span class="czr-notice"><?php echo $this-> notice ?></span>
              <?php endif; ?>
              <?php
            break;

            case 'textarea':
              ?>
              <?php if (isset( $this->title)) : ?>
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
              <?php endif; ?>
              <label>
                <span class="customize-control-title"><?php echo $this->label; ?></span>
                <?php if(!empty( $this -> notice)) : ?>
                  <span class="czr-notice"><?php echo $this-> notice; ?></span>
                <?php endif; ?>
                <textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
              </label>
              <?php
              break;

            case 'url':
            case 'email':
              ?>
              <?php if (isset( $this->title)) : ?>
              <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
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
                <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
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
                  <span class="czr-notice"><?php echo $this-> notice; ?></span>
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


    /**
    * Enqueue scripts/styles
    * fired by the parent Control class constructor
    *
    */
    public function enqueue() {
        if ( ! empty( self::$enqueued_resources ) )
          return;

        self::$enqueued_resources = true;

        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_style(
          'font-awesome',
          sprintf('%1$s/css/font-awesome.min.css', TC_BASE_URL . 'assets/shared/fonts/fa' ),
          array(),
          CUSTOMIZR_VER,
          $media = 'all'
        );


        //select2 stylesheet
        //overriden by some specific style in theme-customzer-control.css
        wp_enqueue_style(
          'select2-css',
          sprintf('%1$s/assets/czr/css/lib/select2.min.css', TC_BASE_URL ),
          array( 'customize-controls' ),
          CUSTOMIZR_VER,
          $media = 'all'
        );
    }


  }//end of class
endif;
?><?php
/*
*/
if ( class_exists('WP_Customize_Cropped_Image_Control') && ! class_exists( 'CZR_Customize_Cropped_Image_Control' ) ) :
  class CZR_Customize_Cropped_Image_Control extends WP_Customize_Cropped_Image_Control {
    public $type = 'czr_cropped_image';
    public $title;
    public $notice;
    public $dst_width;
    public $dst_height;


    /**
    * Refresh the parameters passed to the JavaScript via JSON.
    *
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
          <h3 class="czr-customizr-title">{{{ data.title }}}</h3>
        <# } #>
          <?php parent::content_template(); ?>
        <# if ( data.notice ) { #>
          <span class="czr-notice">{{{ data.notice }}}</span>
        <# } #>
      <?php
    }
  }//end class
endif;
?><?php
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
        <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>

      <label>
        <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php echo $dropdown; ?>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="czr-notice"><?php echo $this -> notice ?></span>
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
              'class'              => 'select2 no-selecter-js '.$this->type,
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
?><?php
/*
* @since 4.0
*/
if ( ! class_exists( 'CZR_Customize_Modules' ) ) :
  class CZR_Customize_Modules extends CZR_controls {
    public $module_type;
    public $syncCollection;
    public $syncSektion;

    /**
    * Constructor.
    *
    */
    public function __construct($manager, $id, $args = array()) {
      //let the parent do what it has to
      parent::__construct($manager, $id, $args );

      //hook validation/sanitization callbacks for the $module_type module
      foreach ( $this -> settings as $key => $setting ) {
        foreach ( array( 'validate', 'sanitize', 'sanitize_js' ) as $callback_prefix ) {
          if ( method_exists( $this, "{$callback_prefix}_callback__{$this->module_type}" )  ) {
            add_filter( "customize_{$callback_prefix}_{$setting->id}", array( $this, "{$callback_prefix}_callback__{$this->module_type}" ), 0, 3 );
          }
        }
      }


    }

    public function render_content(){}

    public function to_json() {
      parent::to_json();
      $this->json['syncCollection'] = $this->syncCollection;
      $this->json['syncSektion'] = $this->syncSektion;
      $this->json['module_type'] = $this->module_type;
    }

    /***
    * Social Module sanitization/validation
    **/
    public function sanitize_callback__czr_social_module( $socials ) {
      if ( empty( $socials ) )
        return array();

      //sanitize urls and titles for the db
      foreach ( $socials as $index => &$social ) {
        if ( ! is_array( $social ) || ! ( array_key_exists( 'social-link', $social) &&  array_key_exists( 'title', $social) ) )
          continue;

        $social['social-link']  = esc_url_raw( $social['social-link'] );
        $social['title']        = esc_attr( $social['title'] );
      }
      return $socials;
    }

    public function validate_callback__czr_social_module( $validity, $socials ) {
      $ids_malformed_url = array();
      $malformed_message = __( 'An error occurred: malformed social links', 'customizr' );

      if ( empty( $socials ) )
        return new WP_Error( 'required', $malformed_message );

      //validate urls
      foreach ( $socials as $index => $social ) {
        if ( ! is_array( $social ) || ! ( array_key_exists( 'social-link', $social) &&  array_key_exists( 'id', $social) ) )
          return new WP_Error( 'required', $malformed_message );

        if ( $social['social-link'] != esc_url_raw( $social['social-link'] ) )
          array_push( $ids_malformed_url, $social[ 'id' ] );
      }

      if ( empty( $ids_malformed_url) )
        return null;

      return new WP_Error( 'required', __( 'Please fill the social link inputs with a valid URLs', 'customizr' ), $ids_malformed_url );
    }
  }
endif;
?><?php
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
    public $type    = 'czr_upload';
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
        <h3 class="czr-customizr-title"><?php echo esc_html( $this->title); ?></h3>
      <?php endif; ?>
      <label>
        <?php if ( ! empty( $this->label ) ) : ?>
          <span class="customize-control-title"><?php echo $this->label; ?></span>
        <?php endif;
        if ( ! empty( $this->description ) ) : ?>
          <span class="description customize-control-description"><?php echo $this->description; ?></span>
        <?php endif; ?>
        <div>
          <a href="#" class="button-secondary czr-upload"><?php _e( 'Upload' , 'customizr'  ); ?></a>
          <a href="#" class="remove"><?php _e( 'Remove' , 'customizr'  ); ?></a>
        </div>
        <?php if(!empty( $this -> notice)) : ?>
          <span class="czr-notice"><?php echo $this -> notice; ?></span>
        <?php endif; ?>
      </label>
      <?php
      do_action( '__after_setting_control' , $this -> id );
    }
  }
endif;
?><?php
/***************************************************
* AUGMENTS WP CUSTOMIZE PANELS
***************************************************/
if ( ! class_exists( 'CZR_Customize_Panels') ) :
  class CZR_Customize_Panels extends WP_Customize_Panel {
    public $czr_subtitle = '';

    // function __construct( $manager, $id, $args = array() ) {
    //   $keys = array_keys( get_object_vars( $this ) );
    //   foreach ( $keys as $key ) {
    //     if ( isset( $args[ $key ] ) ) {
    //       $this->$key = $args[ $key ];
    //     }
    //   }
    //   parent::__construct( $manager, $id, $args );


    // }

    public function json() {
      $array = parent::json();
      $array['czr_subtitle'] = html_entity_decode( $this->czr_subtitle, ENT_QUOTES, get_bloginfo( 'charset' ) );
      return $array;
    }


     /**
     * Render the panel's JS templates.
     *
     * This function is only run for panel types that have been registered with
     * WP_Customize_Manager::register_panel_type().
     *
     * @since 4.3.0
     *
     * @see WP_Customize_Manager::register_panel_type()
     */
    public function print_template() {
      ?>
      <script type="text/html" id="tmpl-customize-panel-czr_panel">
        <?php $this->czr_fn_render_template(); ?>
      </script>
          <?php
    }

    /**
     * An Underscore (JS) template for rendering this panel's container.
     *
     * Class variables for this panel class are available in the `data` JS object;
     * export custom variables by overriding WP_Customize_Panel::json().
     *
     * @see WP_Customize_Panel::print_template()
     *
     * @since 4.3.0
     * @access protected
     */
    protected function czr_fn_render_template() {
      ?>
      <li id="accordion-panel-{{ data.id }}" class="accordion-section control-section control-panel control-panel-{{ data.type }}">
        <h3 class="accordion-section-title" tabindex="0">
          {{ data.title }}
          <span class="czr-panel-subtitle">{{ data.czr_subtitle }}</span>
          <span class="screen-reader-text"><?php _e( 'Press return or enter to open this panel', 'customizr' ); ?></span>
        </h3>
        <ul class="accordion-sub-container control-panel-content"></ul>
      </li>
      <?php
    }
  }
endif;
?><?php
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
?><?php
add_filter('czr_js_customizer_control_params', 'czr_fn_add_social_module_data');


function czr_fn_add_social_module_data( $params ) {
  return array_merge(
    $params,
    array(
        'social_el_params' => array(
            //Social Module
            'defaultSocialColor' => 'rgb(90,90,90)',
        )
    )
  );
}
?><?php
/////////////////////////////////////////////////////
/// ALL MODULES TMPL  //////////////////////
/////////////////////////////////////////////////////
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_module_templates' , 1 );
function czr_fn_print_module_templates() {
  $css_attr = CZR_customize::$instance -> css_attr;
  ?>

    <script type="text/html" id="tmpl-czr-crud-module-part">
      <button class="<?php echo $css_attr['open_pre_add_btn']; ?>"><?php _e('Add New', 'customizr'); ?> <span class="fa fa-plus-square"></span></button>
      <div class="<?php echo $css_attr['pre_add_wrapper']; ?>">
        <div class="<?php echo $css_attr['pre_add_success']; ?>"><p></p></div>
        <div class="<?php echo $css_attr['pre_add_item_content']; ?>">

          <span class="<?php echo $css_attr['cancel_pre_add_btn']; ?> button"><?php _e('Cancel', 'customizr'); ?></span> <span class="<?php echo $css_attr['add_new_btn']; ?> button"><?php _e('Add it', 'customizr'); ?></span>
        </div>
      </div>
    </script>


    <script type="text/html" id="tmpl-czr-rud-item-alert-part">
      <p><?php _e('Are you sure you want to remove : <strong>{{ data.title }} ?</strong>', 'customizr'); ?></p>
              <span class="<?php echo $css_attr['remove_view_btn']; ?> button"><?php _e('Yes', 'customizr'); ?></span> <span class="<?php echo $css_attr['cancel_alert_btn']; ?> button"><?php _e('No', 'customizr'); ?></span>
    </script>



    <script type="text/html" id="tmpl-czr-rud-item-part">
        <div class="<?php echo $css_attr['item_header']; ?> czr-custom-model">
          <div class="<?php echo $css_attr['item_title']; ?> <?php echo $css_attr['item_sort_handle']; ?>"><h4>{{ data.title }}</h4></div>
          <div class="<?php echo $css_attr['item_btns']; ?>"><a title="<?php _e('Edit', 'customizr'); ?>" href="javascript:void(0);" class="fa fa-pencil <?php echo $css_attr['edit_view_btn']; ?>"></a>&nbsp;<a title="<?php _e('Remove', 'customizr'); ?>" href="javascript:void(0);" class="fa fa-trash <?php echo $css_attr['display_alert_btn']; ?>"></a></div>
          <div class="<?php echo $css_attr['remove_alert_wrapper']; ?>"></div>
        </div>
    </script>

    <?php
    //Read + Update Item Part (ru), no Delete
    //no remove button
    //no remove alert wrapper
    ?>
    <script type="text/html" id="tmpl-czr-ru-item-part">
        <div class="<?php echo $css_attr['item_header']; ?> czr-custom-model">
          <div class="<?php echo $css_attr['item_title']; ?> <?php echo $css_attr['item_sort_handle']; ?>"><h4>{{ data.title }}</h4></div>
            <div class="<?php echo $css_attr['item_btns']; ?>"><a title="<?php _e('Edit', 'customizr'); ?>" href="javascript:void(0);" class="fa fa-pencil <?php echo $css_attr['edit_view_btn']; ?>"></a></div>
          </div>
        </div>
    </script>

  <?php
}




/////////////////////////////////////////////////////
/// WHEN EMBEDDED IN A CONTROL //////////////////////
/////////////////////////////////////////////////////
//add specific js templates for this control
//this is usually called in the manager for "registered" controls that need to be rendered with js
//for this control, we'll do it another way because we need several js templates
//=> that's why this control has not been "registered" and js templates are printed with the following action
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_module_control_templates' , 1 );
function czr_fn_print_module_control_templates() {
    $css_attr = CZR_customize::$instance -> css_attr;
    //Render the control wrapper for the CRUD types modules
    ?>
      <?php //Render the control wrapper for the CRUD types modules ?>
      <script type="text/html" id="tmpl-customize-control-czr_module-content">
        <label for="{{ data.settings['default'] }}-button">

          <# if ( data.label ) { #>
            <span class="customize-control-title">{{ data.label }}</span>
          <# } #>
          <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
          <# } #>
        </label>
      </script>
    <?php
}




/////////////////////////////////////////////////////
/// WHEN EMBEDDED IN A SEKTION //////////////////////
/////////////////////////////////////////////////////
//this is a the wrapper for a single module
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_sektion_module_templates' , 1 );
function czr_fn_print_sektion_module_templates() {
  $css_attr = CZR_customize::$instance -> css_attr;
  ?>

    <script type="text/html" id="tmpl-czr-single-module-wrapper">
      <li class="czr-single-module" data-module-id="{{ data.id }}">
          <div class="czr-mod-header">
              <div class="czr-mod-title">
                <span class="czr-mod-drag-handler fa fa-arrows-alt"></span>
                <h4>{{ data.id }}</h4>
                <div class="czr-mod-buttons">

                  <a title="<?php _e('Edit', 'customizr'); ?>" href="javascript:void(0);" class="fa fa-pencil czr-edit-mod"></a>&nbsp;<a title="<?php _e('Remove', 'customizr'); ?>" href="javascript:void(0);" class="fa fa-trash czr-remove-mod"></a>
                </div>
              </div>
              <div class="<?php echo $css_attr['remove_alert_wrapper']; ?>"></div>
          </div>
          <div class="czr-mod-content"></div>
      </li>
    </script>


    <script type="text/html" id="tmpl-czr-module-sektion-title-part">
        <div class="czr-module-description-container">
          <div class="czr-module-title">
            <button class="czr-module-back" tabindex="0">
              <span class="screen-reader-text">Back</span>
            </button>
            <h3>
              <span class="customize-action">
                Customizing Module
              </span>
              {{ data.id }}
            </h3>
          </div>
        </div>
    </script>

  <?php
}
?><?php
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_pre_add_view_template' , 1 );
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_item_content_template' , 1 );

function czr_fn_print_social_pre_add_view_template() {
  $css_attr = CZR_customize::$instance -> css_attr;
  ?>

  <script type="text/html" id="tmpl-czr-module-social-pre-add-view-content">
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="select">
      <div class="customize-control-title"><?php _e('Select an icon', 'customizr'); ?></div>
      <div class="czr-input">
        <select data-type="social-icon"></select>
      </div>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Social link url', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-link" type="text" value="" placeholder="<?php _e('http://...,mailto:...,...', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('Enter the full url of your social profile (must be valid url).', 'customizr'); ?>
      </span>
    </div>
  </script>
  <?php
}





function czr_fn_print_social_item_content_template() {
  $css_attr = CZR_customize::$instance -> css_attr;
    //the following template is a "sub view"
    //it's rendered :
    //1) on customizer start, depending on what is fetched from the db
    //2) dynamically when designing from the customizer
    //data looks like : { id : 'sidebar-one', title : 'A Title One' }
  ?>

  <script type="text/html" id="tmpl-czr-module-social-item-content">
    <!-- <div class="czr-sub-set">
      <div class="customize-control-title"><?php _e('Id', 'customizr'); ?></div>
      <div class="czr-input">
        <span data-type="id">{{ data.id }}</span>
      </div>
    </div> -->
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="select">
      <div class="customize-control-title"><?php _e('Social icon', 'customizr'); ?></div>
      <div class="czr-input">
        <select data-type="social-icon"></select>
        <!-- <input type="text" value="{{ data['social-icon'] }}"></input> -->
      </div>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Social link', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-link" type="text" value="{{ data['social-link'] }}" placeholder="<?php _e('http://...,mailto:...,...', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('Enter the full url of your social profile (must be valid url).', 'customizr'); ?></span>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="text">
      <div class="customize-control-title"><?php _e('Title', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="title" type="text" value="{{ data.title }}" placeholder="<?php _e('Enter a title', 'customizr'); ?>"></input>
      </div>
      <span class="czr-notice"><?php _e('This is the text displayed on mouse over.', 'customizr'); ?></span>
    </div>

    <div class="<?php echo $css_attr['sub_set_wrapper']; ?> width-100" data-input-type="color">
      <div class="customize-control-title"><?php _e('Icon color', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-color" type="text" value="{{ data['social-color'] }}"></input>
      </div>
      <span class="czr-notice"><?php _e('Set a unique color for your icon.', 'customizr'); ?></span>
    </div>
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="check">
      <# //the previous hueman option system was storing this option in an array
        data['social-target'] = _.isArray( data['social-target'] ) ? data['social-target'][0] : data['social-target'];
        var _checked = ( false != data['social-target'] ) ? "checked=checked" : '';
      #>
      <div class="customize-control-title"><?php _e('Link target', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-target" type="checkbox" {{ _checked }}></input>
      </div>
      <span class="czr-notice"><?php _e('Check this option to open the link in a another tab of the browser.', 'customizr'); ?></span>
    </div>

  </script>
  <?php
}
?><?php

//print the image uploader template
//used in multi input controls for example
//defined in the parent class
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_image_uploader_template', 1 );

/**
 * Render a JS template for the content of the image control.
 *
 * highly inspired by WP_Customize_Media_Control::content_template() .
 */
function czr_fn_print_image_uploader_template() {
?>
  <script type="text/html" id="tmpl-czr-input-img-uploader-view-content">
    <# if ( data.attachment && data.attachment.id ) { #>
      <div class="attachment-media-view attachment-media-view-{{ data.attachment.type }} {{ data.attachment.orientation }}">
        <div class="thumbnail thumbnail-{{ data.attachment.type }}">
          <# if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.medium ) { #>
            <img class="attachment-thumb" src="{{ data.attachment.sizes.medium.url }}" draggable="false" alt="" />
          <# } else if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.full ) { #>
            <img class="attachment-thumb" src="{{ data.attachment.sizes.full.url }}" draggable="false" alt="" />
          <# } #>
        </div>
        <div class="actions">
          <# if ( data.canUpload ) { #>
          <button type="button" class="button remove-button">{{ data.button_labels.remove }}</button>
          <button type="button" class="button upload-button control-focus" id="{{ data.settings['default'] }}-button">{{ data.button_labels.change }}</button>
          <div style="clear:both"></div>
          <# } #>
        </div>
      </div>
    <# } else { #>
      <div class="attachment-media-view">
        <div class="placeholder">
          {{ data.button_labels.placeholder }}
        </div>
        <div class="actions">
          <# if ( data.canUpload ) { #>
          <button type="button" class="button upload-button" id="{{ data.settings['default'] }}-button">{{ data.button_labels.select }}</button>
          <# } #>
          <div style="clear:both"></div>
        </div>
      </div>
    <# } #>
  </script>
<?php
}


//Add image uploader button_labels to translated strings
add_filter( 'controls_translated_strings', 'czr_fn_add_translated_strings');
function czr_fn_add_translated_strings( $strings) {
      return array_merge( $strings, array(
              'select_image'        => __( 'Select Image', 'hueman' ),
              'change_image'        => __( 'Change Image', 'hueman' ),
              'remove_image'        => __( 'Remove', 'hueman' ),
              'default_image'       => __( 'Default', 'hueman'  ),
              'placeholder_image'   => __( 'No image selected', 'hueman' ),
              'frame_title_image'   => __( 'Select Image', 'hueman' ),
              'frame_button_image'  => __( 'Choose Image', 'hueman' )
      ));
}
?>