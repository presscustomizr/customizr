<?php
/**
* Customizer actions and filters
*
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
      add_action( 'customize_register'                       , array( $this, 'czr_fn_augment_customizer' ),10, 1);

      //Partial refreshs
      add_action( 'customize_register'                       , array( $this, 'czr_fn_register_partials' ) );

      //add the customizer built with the builder below
      add_action( 'customize_register'                       , array( $this, 'czr_fn_customize_register' ), 20, 1 );

      //modify some WP built-in settings / controls / sections
      add_action( 'customize_register'                       , array( $this, 'czr_fn_alter_wp_customizer_settings' ), 1000, 1 );

      //add grid/post list buttons in the control views
      add_action( '__before_setting_control'                 , array( $this, 'czr_fn_render_grid_control_link' ) );

      //remove old logo setting if the wp custom logo option has been set
      add_action( 'customize_save_custom_logo'               , array( $this, 'czr_fn_remove_old_tc_logo_upload' ) );

      add_action( 'customize_save_after',  array( $this, 'czr_fn_flush_wp_cache' ) );

      //load resources class
      $this -> czr_fn_fire_czr_resources();
    }

    //@hook 'customize_save_after'
    function czr_fn_flush_wp_cache() {
        // Make sure cached objects are cleaned
        // for https://github.com/presscustomizr/customizr/issues/1898
        wp_cache_flush();
    }



    function czr_fn_fire_czr_resources() {
      if (  ! is_object(CZR_customize_resources::$instance) )
        new CZR_customize_resources();
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

      if ( class_exists('CZR_Customize_Code_Editor_Control') )
        $manager -> register_control_type( 'CZR_Customize_Code_Editor_Control' );

      if ( class_exists('CZR_Customize_Panels') )
        $manager -> register_panel_type( 'CZR_Customize_Panels');

      if ( class_exists('CZR_Customize_Sections') )
        $manager -> register_panel_type( 'CZR_Customize_Sections');

      if ( czr_fn_is_pro_section_on() ) {
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

        //SOCIALS
        $wp_customize->selective_refresh->add_partial( 'social_links', array(
            'selector'            => '.social-links',
            'settings'            => array( CZR_THEME_OPTIONS . '[tc_social_links]' ),
            'render_callback'     => 'czr_fn_print_social_links',
            'fallback_refresh'    => false,
        ) );

        //ONLY FOR OLD CZR at the moment
        if ( ! czr_fn_is_ms() ) {
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
        }
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
        $tc_option_group = CZR_THEME_OPTIONS;

        //ONLY FOR OLD CUSTOMIZR
        if ( ! ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) ) {
          $wp_customize -> remove_control( "{$tc_option_group}[tc_fav_upload]" );
        }
        //note : the setting is kept because used in the customizer js api to handle the transition between Customizr favicon to WP site icon.
        $wp_customize -> get_control( 'site_icon' )->section = 'title_tagline';
      }
      //end ALTER SITE ICON



      //CHANGE MENUS PROPERTIES
      // $locations    = get_registered_nav_menus();
      // $menus        = wp_get_nav_menus();
      // $choices      = array( '' => __( '&mdash; Select &mdash;', 'customizr' ) );
      // foreach ( $menus as $menu ) {
      //   $choices[ $menu->term_id ] = wp_html_excerpt( $menu->name, 40, '&hellip;' );
      // }
      // $_priorities  = array(
      //   'main' => 10,
      //   'secondary' => 20,
      //   'topbar'    => 30,
      // );

      // //WP only adds the menu(s) settings and controls if the user has created at least one menu.
      // //1) if no menus yet, we still want to display the menu picker + add a notice with a link to the admin menu creation panel
      // //=> add_setting and add_control for each menu location. Check if they are set first by security
      // //2) if user has already created a menu, the settings are already created, we just need to update the controls.
      // $_priority = 0;
      // //assign new priorities to the menus controls
      // foreach ( $locations as $location => $description ) {
      //   $menu_setting_id = "nav_menu_locations[{$location}]";

      //   //create the settings if they don't exist
      //   //=> in the condition, make sure that the setting has really not been created yet (maybe over secured)
      //   if ( ! $menus && ! is_object( $wp_customize->get_setting($menu_setting_id ) ) ) {
      //     $wp_customize -> add_setting( $menu_setting_id, array(
      //       'sanitize_callback' => 'absint',
      //       'theme_supports'    => 'menus',
      //     ) );
      //   }

      //   //remove the controls if they exists
      //   if ( is_object( $wp_customize->get_control( $menu_setting_id ) ) ) {
      //     $wp_customize -> remove_control( $menu_setting_id );
      //   }

      //   //replace the controls by our custom controls
      //   $_control_properties = array(
      //     'label'   => $description,
      //     'section' => 'nav',
      //     'title'   => "main" == $location ? __( 'Assign menus to locations' , 'customizr') : false,
      //     'type'    => 'select',
      //     'choices' => $choices,
      //     'priority' => isset($_priorities[$location]) ? $_priorities[$location] : $_priority,
      //     'notice' => __('If your freshly created menu is not listed, please refresh the customizer panel.', 'customizr')
      //   );

      //   //add a notice property if no menu created yet.
      //   if ( ! $menus ) {
      //     //adapt the nav section description for v4.3 (menu in the customizer from now on)
      //     $_create_menu_link =  version_compare( $GLOBALS['wp_version'], '4.3', '<' ) ? admin_url('nav-menus.php') : "javascript:wp.customize.section('nav').container.find('.customize-section-back').trigger('click'); wp.customize.panel('nav_menus').focus();";
      //     $_control_properties['notice'] = sprintf( __("You haven't created any menu yet. %s or check the %s to learn more about menus.", "customizr"),
      //       sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', $_create_menu_link, __("Create a new menu now" , "customizr") ),
      //       sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('codex.wordpress.org/WordPress_Menu_User_Guide'),  __("WordPress documentation" , "customizr") )
      //     );
      //   }

      //   $wp_customize -> add_control( new CZR_controls( $wp_customize, $menu_setting_id, $_control_properties ) );

      //   $_priority = $_priority + 10;
      // }//foreach



      //MOVE THE CUSTOM CSS SECTION (introduced in 4.7) INTO THE ADVANCED PANEL
      if ( is_object( $wp_customize->get_section( 'custom_css' ) ) ) {
          $wp_customize -> get_section( 'custom_css' ) -> panel = 'tc-advanced-panel';
          $wp_customize -> get_section( 'custom_css' ) -> priority = 10;
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
        czr_fn_get_customizer_map()
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

                'pro_subtitle',
                'pro_doc_url',
                'pro_text',
                'pro_url',

                'ubq_panel'
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

                'ubq_section',

                //for the code editor
                'code_type',
                'input_attrs'

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
          if ( class_exists( 'CZR_Customize_Panels' ) ) {
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
          if ( czr_fn_is_customizr_option( $key ) )
            $add_prefix = true;
          $_opt_name = $add_prefix ? "{$czr_option_group}[{$key}]" : $key;

          //declares settings array
          $option_settings = array();

          // bail here if the setting is registered dynamically
          if ( array_key_exists( 'registered_dynamically', $options ) && true === $options[ 'registered_dynamically' ] )
            continue;

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

          //The post message callbacks and css are not implement yet as of July 2017 for the modern style.
          //=> let's force the "refresh" for all transport. => this will also disable any partial refreshs added to the controls
          //$option_settings['transport'] = CZR_IS_MODERN_STYLE ? 'refresh' : $option_settings['transport'];

          //add setting
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



    //hook: customize_save_custom_logo
    function czr_fn_remove_old_tc_logo_upload( $setting ) {
      //make sure the custom_logo option is a theme mod
      if ( 'theme_mod' !== $setting->type ) {
        return;
      }
      $theme_options = czr_fn_get_unfiltered_theme_options();
      unset( $theme_options['tc_logo_upload'] );
      if ( is_array( $theme_options ) && ! empty( $theme_options ) ) {
        update_option( CZR_THEME_OPTIONS, $theme_options );
      }
    }




    //ONLY FOR OLD CUSTOMIZR
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
          <img src="<?php echo CZR_BASE_URL . 'screenshot.png' ?>" alt="Customizr" />
          </p>
        </div>
      <?php
    }
  }//end class
endif;
?>