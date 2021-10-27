<?php
/**
* Customizer actions and filters
*
*/
if ( ! class_exists( 'CZR_customize_resources' ) ) :
  class CZR_customize_resources {
    static $instance;
    private $_is_dev_mode           = false;
    private $_is_debug_mode         = false;

    private $_style_version_suffix  = false;

    function __construct () {
      self::$instance =& $this;

      $this->_is_debug_mode         = ( defined('WP_DEBUG') && true === WP_DEBUG );
      $this->_is_dev_mode           = ( defined('CZR_DEV') && true === CZR_DEV );
      $this->_style_version_suffix  = defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ? '-modern' : '';

      //control scripts and style
      add_action( 'customize_controls_enqueue_scripts'        , array( $this, 'czr_fn_customize_controls_js_css' ), 20 );

      //preview scripts
      //set with priority 20 to be fired after czr_fn_customize_store_db_opt
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_customize_preview_js_css' ), 20 );

      //exports some wp_query informations. Updated on each preview refresh.
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_add_preview_footer_action' ), 20 );


    }



    //only for the classic
    //adds specific preview style for partial refresh to the user option style
    //hook : 'tc_user_options_style'
    function czr_fn_write_preview_style_classic( $_css ) {
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

        wp_enqueue_style(
            'tc-customizer-controls-theme-style',
            sprintf('%1$sassets/czr/css/czr-control-theme.css', CZR_BASE_URL),
            array( 'czr-fmk-controls-style' ),
            $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
            $media = 'all'
        );


        // czr-control-deps-modern.js / czr-control-deps.js
        wp_enqueue_script(
            'tc-customizer-controls-deps',
            sprintf('%1$sassets/czr/js/czr-control-deps%2$s.js' , CZR_BASE_URL, $this->_style_version_suffix ),
            array( 'czr-theme-customizer-fmk' ),
            $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
            true
        );

        wp_enqueue_script(
            'tc-customizer-controls-vdr',
            sprintf('%1$sassets/czr/js/czr-control-dom_ready.js', CZR_BASE_URL ),
            array( 'czr-theme-customizer-fmk' ),
            $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
            true
        );

        $this->czr_fn_customize_controls_localize();
    }



    //shared
    function czr_fn_customize_controls_localize() {

      //gets the featured pages id from init
      $fp_ids       = apply_filters( 'tc_featured_pages_ids' , CZR___::$instance -> fp_ids);

      //declares the common fp control fields and the dynamic arrays
      $fp_controls      = array(
        CZR_THEME_OPTIONS.'[tc_show_featured_pages_img]',
        CZR_THEME_OPTIONS.'[tc_featured_page_button_text]'
      );
      $page_dropdowns     = array();
      $text_fields      = array();

      //adds filtered page dropdown fields
      foreach ( $fp_ids as $id ) {
        $page_dropdowns[]   = CZR_THEME_OPTIONS.'[tc_featured_page_'. $id.']';
        $text_fields[]    = CZR_THEME_OPTIONS.'[tc_featured_text_'. $id.']';
      }


      //localizes
      wp_localize_script(
        'tc-customizer-controls-deps',
        'themeServerControlParams',
        array(
            //should be included in all themes
            'wpBuiltinSettings'=> CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
            'isThemeSwitchOn'  => ! CZR_IS_PRO,
            'themeSettingList' => CZR_BASE::$theme_setting_list,
            'themeOptions'     => CZR_THEME_OPTIONS,

            // Customizr theme specifics
            'FPControls'      => array_merge( $fp_controls , $page_dropdowns , $text_fields ),
            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here

            'i18n'   => $this -> czr_fn_get_translated_strings(),

            //not used by the new
            'faviconOptionName' => 'tc_fav_upload',

            'gridDesignControls' => CZR_customize::$instance -> czr_fn_get_grid_design_controls(),
            'isRTL'           => is_rtl(),
            'isChildTheme'    => is_child_theme(),
            'isModernStyle'   => czr_fn_is_ms(),
            'isPro'           => czr_fn_is_pro()
        )
      );

    }

    //hook : customize_preview_init
    function czr_fn_customize_preview_js_css() {
        global $wp_version;

        // loads czr-preview-post_message.js / czr-preview-post_message-modern.js
        wp_enqueue_script(
            'czr-customizr-theme-preview-js' ,
            sprintf('%1$s/assets/czr/js/czr-preview-post_message%2$s.js' , CZR_BASE_URL, $this->_style_version_suffix ),
            array( 'czr-customizer-preview' ),//<= czr-preview-base.js, loaded from the czr-base-fmk
            $this->_is_debug_mode ? time() : CUSTOMIZR_VER,
            true
        );

        //localizes
        wp_localize_script(
              'czr-customizr-theme-preview-js',
              'themeServerPreviewParams',// 'CZRPreviewParams',
              apply_filters('tc_js_customizer_preview_params' ,
                  array(
                      //czr4 won't use this
                      'customSkin'      => apply_filters( 'tc_custom_skin_preview_params' , array( 'skinName' => '', 'fullPath' => '' ) ),
                      'fontPairs'       => czr_fn_get_font( 'list' ),
                      'fontSelectors'   => CZR_init::$instance -> font_selectors,

                      'wpBuiltinSettings' => CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
                      'themeOptionsPrefix'  => CZR_THEME_OPTIONS,
                  )
              )
        );

        if ( 'modern' != $this->_style_version_suffix ) {
            add_filter( 'tc_user_options_style', array( $this, 'czr_fn_write_preview_style_classic' ) );
        }
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
          $val = czr_fn_is_real_home();

        $_wp_conditionals[$prop] = $val;
      }

      ?>
        <script id="czr-customizer-data">
          (function ( _export ){
            _export.czr_wp_conditionals = <?php echo wp_json_encode( $_wp_conditionals ) ?>;
          })( _wpCustomizeSettings );
        </script>
      <?php
    }


    // the localized translated strings property of the global themeServerControlParams
    function czr_fn_get_translated_strings() {
      return apply_filters('controls_translated_strings',
          array(
                'edit' => __('Edit', 'customizr'),
                'close' => __('Close', 'customizr'),
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