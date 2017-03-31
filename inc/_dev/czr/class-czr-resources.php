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
      $this->_is_dev_mode   = ( defined('CZR_DEV') && true === CZR_DEV );

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
                'isRTL'           => is_rtl()
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
            'i18n'   => $this -> czr_fn_get_translated_strings(),

            'themeOptions'     => CZR_THEME_OPTIONS,

            'isDevMode'        => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('CZR_DEV') && true === CZR_DEV ),

            'wpBuiltinSettings'=> CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
            'css_attr'         => CZR_customize::$instance -> czr_fn_get_controls_css_attr(),
            'isThemeSwitchOn'  => ! CZR___::czr_fn_is_pro(),
            'themeSettingList' => CZR_utils::$_theme_setting_list,

            'faviconOptionName' => 'tc_fav_upload',

            'gridDesignControls' => CZR_customize::$instance -> czr_fn_get_grid_design_controls(),
            'isRTL'           => is_rtl()
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
                                  ),

                'readDocumentation' => __('Learn more about this in the documentation', 'customizr'),
                'Settings' => __('Settings', 'customizr'),
                'Options for' => __('Options for', 'customizr')
          )
      );
    }

  }
endif;
?>