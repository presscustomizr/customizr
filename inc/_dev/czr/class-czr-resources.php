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
        sprintf('%1$sassets/czr/js/czr-control-full%2$s.js' , TC_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min' ),
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

            'faviconOptionName' => 'tc_fav_upload'
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

body .select2-container .select2-selection--single .select2-selection__rendered {
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
  z-index: 999;
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
                    $( fontSelectors.body ).not('.social-icon').css( {
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
                    $('.carousel .item').css('line-height' , to + 'px').css('max-height', to + 'px').css('min-height', to + 'px').trigger('resize');
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
                            visibility : function( to ) {
                                  return _is_checked(to);
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
                              'tc_post_list_grid', //trick to fire actions on dominus change
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' != servusShortId )
                                    return 'grid' == to;
                            },
                            actions : function( to, servusShortId ) {
                              if ( 'tc_post_list_grid' == servusShortId ) {
                                  $('.tc-grid-toggle-controls').toggle('grid' == to).removeClass('open');
                              }else {
                                //hide grid-design options
                                $_el = api.control( api.CZR_Helpers.build_setId(servusShortId) ).container;
                                if ( $_el.hasClass('tc-grid-desing') )
                                  $_el.hide();
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
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_woocommerce_header_cart',
                            servi   : ['tc_woocommerce_header_cart_sticky'],
                            visibility: function (to) {
                                  return _is_checked(to);
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
                                      $(this).fadeTo( 500 , true === to ? 0.5 : 1); //.fadeTo() duration, opacity, callback
                                  });//$.each()
                            }
                    },
                    {
                            dominus : 'tc_show_back_to_top',
                            servi   : ['tc_back_to_top_position'],
                            visibility: function (to) {
                                  return 'custom' == to;
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

                var api = wp.customize || api;
                /* GRID */
                var _build_setId = function ( name ) {
                  return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
                };
                var _grid_design_controls = [
                  'tc_grid_in_blog',
                  'tc_grid_in_archive',
                  'tc_grid_in_search',
                  'tc_grid_thumb_height',
                  'tc_grid_shadow',
                  'tc_grid_bottom_border',
                  'tc_grid_icons',
                  'tc_grid_num_words'
                ];

                var _build_control_id = function( _control ) {
                  return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
                };

                var _get_grid_design_controls = function() {
                  return $( _grid_design_controls.map( function( _control ) {
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
                  $('.tc-close-request').click( function(e) {
                    e.preventDefault();
                    $('.donate-alert').slideToggle("fast");
                    $(this).hide();
                  });

                  $('.tc-hide-donate').click( function(e) {
                    _ajax_save();
                    setTimeout(function(){
                        $('#tc-donate-customizer').slideToggle("fast");
                    }, 200);
                  });

                  $('.tc-cancel-hide-donate').click( function(e) {
                    $('.donate-alert').slideToggle("fast");
                    setTimeout(function(){
                        $('.tc-close-request').show();
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
                  $('li[id*="tc_post_list_show_thumb"] > .tc-customizr-title').before( _cta() );
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
                /*'locations' => __('Location(s)', 'customizr'),
                'contexts' => __('Context(s)', 'customizr'),*/
                'notset' => __('Not set', 'customizr'),
                'rss' => __('Rss', 'customizr'),
                'selectSocialIcon' => __('Select a social icon', 'customizr'),
                'followUs' => __('Follow us on', 'customizr'),
                'successMessage' => __('Done !', 'customizr'),
                'socialLinkAdded' => __('New Social Link created ! Scroll down to edit it.', 'customizr'),
                /*'selectBgRepeat'  => __('Select repeat property', 'customizr'),
                'selectBgAttachment'  => __('Select attachment property', 'customizr'),
                'selectBgPosition'  => __('Select position property', 'customizr'),
                'widgetZone' => __('Widget Zone', 'customizr'),
                'widgetZoneAdded' => __('New Widget Zone created ! Scroll down to edit it.', 'customizr'),
                'inactiveWidgetZone' => __('Inactive in current context/location', 'customizr'),
                'unavailableLocation' => __('Unavailable location. Some settings must be changed.', 'customizr'),
                'locationWarning' => __('A selected location is not available with the current settings.', 'customizr'),*/
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

?>