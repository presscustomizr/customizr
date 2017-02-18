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
        add_action( 'admin_menu'                             , array( $this , 'czr_fn_add_fallback_page' ));
        return;
      }

      self::$instance =& $this;

      //add control class
      add_action( 'customize_register'                       , array( $this , 'czr_fn_augment_customizer' ),10, 1);

      //Partial refreshs
      add_action( 'customize_register'                       , array( $this,  'czr_fn_register_partials' ) );

      //add the customizer built with the builder below
      add_action( 'customize_register'                       , array( $this , 'czr_fn_customize_register' ), 20, 1 );

      //modify some WP built-in settings / controls / sections
      add_action( 'customize_register'                       , array( $this , 'czr_fn_alter_wp_customizer_settings' ), 1000, 1 );


      //add grid/post list buttons in the control views
      add_action( '__before_setting_control'                 , array( $this , 'czr_fn_render_grid_control_link') );

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

      if ( ! CZR___::czr_fn_is_pro() && class_exists('CZR_Customize_Section_Pro') ) {
        $manager -> register_section_type( 'CZR_Customize_Section_Pro');
      }
    }


    /* ------------------------------------------------------------------------- *
     *  PARTIALS
    /* ------------------------------------------------------------------------- */
    //hook : customize_register
    function czr_fn_register_partials( WP_Customize_Manager $wp_customize ) {
        //Bail if selective refresh is not available (old versions) or disabled (for skope for example)
        if ( ! isset( $wp_customize->selective_refresh ) || ! czr_fn_is_partial_refreshed_on() ) {
            return;
        }
        /* Header */
        $wp_customize->selective_refresh->add_partial( 'main_header', array(
            'selector'            => 'header.tc-header',
            'settings'            => array(
              CZR_THEME_OPTIONS . '[tc_header_layout]',
              CZR_THEME_OPTIONS . '[tc_show_tagline]',
              CZR_THEME_OPTIONS . '[tc_social_in_header]',
            ),
            'container_inclusive' => true,
            'render_callback'     => 'czr_fn_render_main_header',
            'fallback_refresh'    => false,
        ) );
        /* Tagline text */
        $wp_customize->selective_refresh->add_partial( 'blogdescription', array(
            'selector'            => '.site-description',
            'settings'            => array( 'blogdescription' ),
            'container_inclusive' => false,
            'render_callback'     => 'czr_fn_get_tagline_text',
            'fallback_refresh'    => false,
        ) );
        /* Social links*/
        $wp_customize->selective_refresh->add_partial( 'social_links', array(
            'selector'            => '.social-links',
            'settings'            => array( CZR_THEME_OPTIONS . '[tc_social_links]' ),
            'render_callback'     => 'czr_fn_print_social_links',
            'fallback_refresh'    => false,
        ) );
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
          'priority' => isset($_priorities[$location]) ? $_priorities[$location] : $_priority,
          'notice' => __('If your freshly created menu is not listed, please refresh the customizer panel.', 'customizr')
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
                'active_callback',
                'pro_text',
                'pro_url'
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
                'dst_height',

                'ubq_section'

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

            'edit_modopt_icon'    => 'czr-toggle-modopt',
            'close_modopt_icon'   => 'czr-close-modopt',
            'mod_opt_wrapper'     => 'czr-mod-opt-wrapper',


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
<?php
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
    private $_is_dev_mode   = false;
    private $_is_debug_mode = false;

    function __construct () {
      self::$instance =& $this;

      $this->_is_debug_mode = ( defined('WP_DEBUG') && true === WP_DEBUG );
      $this->_is_dev_mode   = ( defined('TC_DEV') && true === TC_DEV );

      //control scripts and style
      add_action( 'customize_controls_enqueue_scripts'        , array( $this, 'czr_fn_customize_controls_js_css' ), 10 );

      //preview scripts
      //set with priority 20 to be fired after czr_fn_customize_store_db_opt in HU_utils
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_customize_preview_js_css' ), 20 );
      //exports some wp_query informations. Updated on each preview refresh.
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_add_preview_footer_action' ), 20 );


    }


    //hook : customize_preview_init
    function czr_fn_customize_preview_js_css() {
      global $wp_version;

      //DEV MODE
      if ( $this->_is_dev_mode ) {
        wp_enqueue_script(
        'czr-customizer-preview' ,
          sprintf('%1$s/assets/czr/_dev/js/czr-preview-base.js' , get_template_directory_uri() ),
          array( 'customize-preview', 'underscore'),
          time(),
          true
        );
        wp_enqueue_script(
        'czr-customizer-preview-pm' ,
          sprintf('%1$s/assets/czr/_dev/js/czr-preview-post_message.js' , get_template_directory_uri() ),
          array( 'czr-customizer-preview' ),
          time(),
          true
        );
      }
      //PRODUCTION
      else {
        wp_enqueue_script(
          'czr-customizer-preview' ,
          sprintf('%1$s/assets/czr/js/czr-preview%2$s.js' , get_template_directory_uri(), $this->_is_debug_mode ? '' : '.min' ),
          array( 'customize-preview', 'underscore'),
          $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
          true
        );
      }


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

      add_filter( 'tc_user_options_style', array( $this, 'czr_fn_write_preview_style' ) );
    }


    function czr_fn_write_preview_style( $_css ) {
      //specific preview style
      return sprintf( "%s\n%s",
          $_css,
          '/* Fix partial edit shortcut conflict with bootstrap .span first child of a .row */
.row [class*=customize-partial-edit-shortcut]:first-child + [class*=span],
.row-fluid [class*=customize-partial-edit-shortcut]:first-child + [class*=span] {
  margin-left: 0;
  margin-right: 0;
}
/* Fine tune pencil icon in the header */
.tc-header > .customize-partial-edit-shortcut > button {
  left: 0
}'
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

      //DEV MODE
      if ( $this->_is_dev_mode ) {
        //CSS
        wp_enqueue_style(
          'tc-customizer-controls-style',
          sprintf('%1$sassets/czr/_dev/css/czr-control-base.css', TC_BASE_URL),
          array( 'customize-controls' ),
          time(),
          $media = 'all'
        );

        wp_enqueue_style(
          'tc-customizer-controls-theme-style',
          sprintf('%1$sassets/czr/_dev/css/czr-control-theme.css', TC_BASE_URL),
          array( 'tc-customizer-controls-style' ),
          time(),
          $media = 'all'
        );

        //JS
        wp_enqueue_script(
          'tc-customizer-controls',
          sprintf('%1$sassets/czr/_dev/js/czr-control-base.js' , TC_BASE_URL),
          array( 'customize-controls' , 'underscore'),
          time(),
          true
        );

        wp_enqueue_script(
          'tc-customizer-controls-deps',
          sprintf('%1$sassets/czr/_dev/js/czr-control-deps.js' , TC_BASE_URL),
          array( 'tc-customizer-controls' ),
          time(),
          true
        );

        wp_enqueue_script(
          'tc-customizer-controls-deps',
          sprintf('%1$sassets/czr/_dev/js/czr-control-deps.js' , TC_BASE_URL),
          array( 'tc-customizer-controls' ),
          time(),
          true
        );

        wp_enqueue_script(
          'tc-customizer-controls-vdr',
          sprintf('%1$sassets/czr/_dev/js/czr-control-dom_ready.js' , TC_BASE_URL),
          array( 'tc-customizer-controls' ),
          time(),
          true
        );
      }
      //PRODUCTION
      else {
        //CSS
        wp_enqueue_style(
          'tc-customizer-controls-style',
          sprintf('%1$sassets/czr/css/czr-control%2$s.css' , TC_BASE_URL, $this->_is_debug_mode ? '' : '.min' ),
          array( 'customize-controls' ),
          $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
          $media = 'all'
        );


        //JS
        wp_enqueue_script(
          'tc-customizer-controls',
          sprintf('%1$sassets/czr/js/czr-control%2$s.js' , TC_BASE_URL, $this->_is_debug_mode ? '' : '.min' ),
          array( 'customize-controls' , 'underscore'),
          $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
          true
        );
      }

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

            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'   => $this -> czr_fn_get_translated_strings(),

            'themeOptions'     => CZR_THEME_OPTIONS,

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


    //hook : customize_preview_init
    function czr_fn_add_preview_footer_action() {
      add_action( 'wp_footer', array( $this, 'czr_fn_add_customize_preview_data' ) , 20 );
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
                'sidenavNote'  => sprintf( '%1$s<br/>%2$s',
                                    __( 'The side on which the menu is revealed depends on the choosen header layout.', 'customizr'),
                                    sprintf( __("To change the global header layout, %s" , "customizr"),
                                      sprintf( '<a href="%1$s" title="%3$s">%2$s &raquo;</a>',
                                        "javascript:wp.customize.section('header_layout_sec').focus();",
                                        __("jump to the Design and Layout section" , "customizr"),
                                        __("Change the header layout", "customizr")
                                      )
                                    )
                                  )
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

      public $ubq_section;

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

    /**
    * Refresh the parameters passed to the JavaScript via JSON.
    *
    *
    * @Override
    * @see WP_Customize_Control::to_json()
    */
    public function to_json() {
      parent::to_json();
      if ( is_array( $this->ubq_section ) && array_key_exists( 'section', $this->ubq_section ) )
        $this->json['ubq_section'] = $this->ubq_section;
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
        return array();


      //(
      //     [0] => Array
      //         (
      //             [is_mod_opt] => 1
      //             [module_id] => tc_social_links_czr_module
      //             [social-size] => 15
      //         )

      //     [1] => Array
      //         (
      //             [id] => czr_social_module_0
      //             [title] => Follow us on Renren
      //             [social-icon] => fa-renren
      //             [social-link] => http://customizr-dev.dev/feed/rss/
      //             [social-color] => #6d4c8e
      //             [social-target] => 1
      //         )
      // )
      //validate urls
      foreach ( $socials as $index => $item_or_modopt ) {
        if ( ! is_array( $item_or_modopt ) )
          return new WP_Error( 'required', $malformed_message );

        //should be an item or a mod opt
        if ( ! array_key_exists( 'is_mod_opt', $item_or_modopt ) && ! array_key_exists( 'id', $item_or_modopt ) )
          return new WP_Error( 'required', $malformed_message );

        //if modopt case, skip
        if ( array_key_exists( 'is_mod_opt', $item_or_modopt ) )
          continue;

        if ( $item_or_modopt['social-link'] != esc_url_raw( $item_or_modopt['social-link'] ) )
          array_push( $ids_malformed_url, $item_or_modopt[ 'id' ] );
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
/**
 * Pro customizer section.
 * highly based on
 * https://github.com/justintadlock/trt-customizer-pro/blob/master/example-1/section-pro.php
 */
class CZR_Customize_Section_Pro extends WP_Customize_Section {

    /**
     * The type of customize section being rendered.
     *
     * @var    string
     */
    public $type ='czr-customize-section-pro';

    /**
     * Custom button text to output.
     *
     * @var    string
     */

    public $pro_text = '';
    /**
     *
     * @var    string
     */
    public $pro_url = '';


    /**
     * Add custom parameters to pass to the JS via JSON.
     *
     * @return void
     * @override
     */
    public function json() {
      $json = parent::json();
      $json['pro_text'] = $this->pro_text;
      $json['pro_url']  = esc_url( $this->pro_url );
      return $json;
    }

    //overrides the default template
    protected function render_template() { ?>
      <li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">
          <h3 class="accordion-section-title">
            {{ data.title }}
            <# if ( data.pro_text && data.pro_url ) { #>
              <a href="{{ data.pro_url }}" title="{{ data.title }}" class="button button-secondary alignright" target="_blank">{{ data.pro_text }}</a>
            <# } #>
          </h3>
        </li>
    <?php }
}
?>
<?php
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
add_action( 'customize_controls_print_footer_scripts', 'czr_fn_print_social_item_mod_opt_template' , 1 );

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





function czr_fn_print_social_item_mod_opt_template() {
  $css_attr = CZR_customize::$instance -> css_attr;
    //the following template is a "sub view"
    //it's rendered :
    //1) on customizer start, depending on what is fetched from the db
    //2) dynamically when designing from the customizer
    //data looks like : { id : 'sidebar-one', title : 'A Title One' }
  ?>
  <script type="text/html" id="tmpl-czr-module-social-mod-opt">
    <div class="<?php echo $css_attr['sub_set_wrapper']; ?>" data-input-type="number" data-transport="postMessage">
      <div class="customize-control-title"><?php _e('Size in px', 'customizr'); ?></div>
      <div class="czr-input">
        <input data-type="social-size" type="number" step="1" min="5" value="{{ data['social-size'] }}" />
      </div>
    </div>
  </script>

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

    <div class="<?php echo $css_attr['sub_set_wrapper']; ?> width-100" data-input-type="color" data-transport="postMessage">
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
              'select_image'        => __( 'Select Image', 'customizr' ),
              'change_image'        => __( 'Change Image', 'customizr' ),
              'remove_image'        => __( 'Remove', 'customizr' ),
              'default_image'       => __( 'Default', 'customizr'  ),
              'placeholder_image'   => __( 'No image selected', 'customizr' ),
              'frame_title_image'   => __( 'Select Image', 'customizr' ),
              'frame_button_image'  => __( 'Choose Image', 'customizr' )
      ));
}
?>