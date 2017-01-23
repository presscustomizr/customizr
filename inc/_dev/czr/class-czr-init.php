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
  /*
      $_classes = array(
        'controls/class-base-control.php',
        //'controls/class-background-control.php',
        'controls/class-cropped-image-control.php',

        'controls/class-layout-control.php',
        'controls/class-multipicker-control.php',
        'controls/class-modules-control.php',

        'controls/class-upload-control.php',

        'panels/class-panels.php',

        'sections/class-widgets-section.php',

        'settings/class-settings.php'
      );
      foreach ($_classes as $_path) {
        locate_template( 'functions/czr/' . $_path , $load = true, $require_once = true );
      }
*/
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
              printf('<div class="donate-alert"><p class="czr-notice">%1$s</p><span class="czr-hide-donate button">%2$s</span><span class="tc-cancel-hide-donate button">%3$s</span></div>',
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
            printf('<span class="czr-notice">%1$s %2$s</span><a class="czr-cta-btn" href="%3$s" title="%4$s" target="_blank">%4$s &raquo;</a>',
              __( "Rediscover the beauty of your blog posts and increase your visitors engagement with the Grid Customizer." , 'customizr' ),
               sprintf('<a href="%1$s" class="czr-notice-inline-link" title="%2$s" target="_blank">%2$s<span class="czr-notice-ext-icon dashicons dashicons-external"></span></a>' , esc_url('demo.presscustomizr.com/?design=demo_grid_customizer'), __("Try it in the demo" , "customizr" )
              ),
              sprintf('%scustomizr-pro/', CZR_WEBSITE ),
              __( "Upgrade to Customizr Pro" , 'customizr' )
            );
          ?>
        </div>
      </script>

       <script type="text/template" id="mc_cta">
        <div class="czr-cta czr-in-control-cta-wrap">
          <?php
            printf('<span class="czr-notice">%1$s %2$s</span><a class="czr-cta-btn" href="%3$s" title="%4$s" target="_blank">%4$s &raquo;</a>',
              __( "Add creative and engaging reveal animations to your side menu." , 'customizr' ),
              sprintf('<a href="%1$s" class="czr-notice-inline-link" title="%2$s" target="_blank">%2$s<span class="czr-notice-ext-icon dashicons dashicons-external"></span></a>' , esc_url('demo.presscustomizr.com/?design=nav'), __("Side menu animation demo" , "customizr" )
              ),
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

?>
