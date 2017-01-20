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
      //add_action( 'customize_controls_print_footer_scripts'   , array( $this, 'czr_fn_extend_ctrl_dependencies' ), 10 );

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
            'TCNonce'         => wp_create_nonce( 'tc-customizer-nonce' ),
            'themeName'       => CZR___::$theme_name,
            'HideDonate'      => CZR_customize::$instance -> czr_fn_get_hide_donate_status(),
            'ShowCTA'         => ( true == CZR_utils::$inst->czr_fn_opt('tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false,
            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'   => $this -> czr_fn_get_translated_strings(),

            'themeOptions'     => CZR_THEME_OPTIONS,
            'optionAjaxAction' => CZR_OPT_AJAX_ACTION,

            'isDevMode'        => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('TC_DEV') && true === TC_DEV ),
            'themeSettingList' => CZR_utils::$_theme_setting_list,
            'wpBuiltinSettings'=> CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
            'css_attr'         => CZR_customize::$instance -> czr_fn_get_controls_css_attr(),
            'isThemeSwitchOn'  => isset( $_GET['theme'])
          )
        )
      );

    }

    function czr_fn_get_inline_control_css() {
      return "
/* SELECT 2 SPECIFICS */
body .select2-dropdown {
  z-index: 999999;
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

/* ROOT PANEL : SEPARATE MENUS, WIDGETS AND ADVANCED OPTIONS */
.control-panel-nav_menus > .accordion-section-title, .control-panel-widgets > .accordion-section-title {
  margin: 0 0 10px;
}
      ";
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
                    if ( 'aside' != api( _build_setId('tc_menu_style') ).get() ) {
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
      $_header_img_notice = esc_js( sprintf( __( "When the %s, this element will not be displayed in your header.", 'hueman'),
          sprintf('<a href="%1$s" title="%2$s">%2$s</a>',
            "javascript:wp.customize.section(\'header_design_sec\').focus();",
            __('header image is enabled', 'hueman')
          )
      ) );
      $_front_page_content_notice = esc_js( sprintf( __( "Jump to the %s.", 'hueman'),
          sprintf('<a href="%1$s" title="%2$s">%2$s</a>',
            "javascript:wp.customize.section(\'content_blog_sec\').focus();",
            __('blog design panel', 'hueman')
          )
      ) );
      $_header_menu_notice = esc_js( sprintf( __( "The menu currently displayed in your header is a default page menu, you can disable it in the %s.", 'hueman'),
          sprintf('<a href="%1$s" title="%2$s">%2$s</a>',
            "javascript:wp.customize.section(\'header_menu_sec\').focus();",
            __('Header Panel', 'hueman')
          )
      ) );
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
                            dominus : 'show_on_front',
                            servi : ['show_on_front', 'page_for_posts' ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'show_on_front' == servusShortId )
                                    return 'unchanged';
                                  return 'page' == to;
                            },
                            actions : function( to, servusShortId ) {
                                  var wpServusId = api.CZR_Helpers.build_setId( servusShortId ),
                                        _class = 'hu-front-posts-notice',
                                        _maybe_print_html = function() {
                                            if ( $( '.' + _class , api.control(wpServusId).container ).length )
                                              return;
                                            var _html = '<span class="description customize-control-description ' + _class +'"><?php echo html_entity_decode( $_front_page_content_notice ); ?></span>';
                                            api.control(wpServusId).container.find('.customize-control-title').after( $.parseHTML( _html ) );
                                        };

                                  if ( 'show_on_front' == servusShortId ) {
                                        if ( 'posts' != to && $( '.' + _class , api.control(wpServusId).container ).length ) {
                                              $('.' + _class, api.control(wpServusId).container ).remove();
                                        } else if ( 'posts' == to ) {
                                              _maybe_print_html();
                                        }
                                  } else if ( 'page_for_posts' == servusShortId ) {
                                        if ( 'page' != to && $( '.' + _class , api.control(wpServusId).container ).length ) {
                                              $('.' + _class, api.control(wpServusId).container ).remove();
                                        } else if ( 'page' == to ) {
                                              _maybe_print_html();
                                        }
                                  }
                            }
                    },
                    {
                            dominus : 'display-header-logo',
                            servi : ['logo-max-height', 'custom_logo', 'custom-logo' ],//depending on the WP version, the custom logo option is different.
                            visibility : function( to ) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'use-header-image',
                            onSectionExpand : false,
                            servi : ['header_image', 'display-header-logo', 'custom_logo', 'custom-logo', 'logo-max-height', 'blogname', 'blogdescription', 'header-ads'],
                            visibility : function( to, servusShortId ) {
                                  if ( 'header_image' != servusShortId )
                                    return 'unchanged';
                                  return _is_checked(to);
                            },
                            actions : function( to, servusShortId ) {
                                  var wpServusId = api.CZR_Helpers.build_setId( servusShortId ),
                                      shortServusId = api.CZR_Helpers.getOptionName( servusShortId ),
                                      _return = api.control(wpServusId).active();

                                  //print a notice
                                  switch( shortServusId ) {
                                        case 'display-header-logo' :
                                        case 'custom_logo' :
                                        case 'blogname' :
                                        case 'blogdescription' :
                                        case 'custom-logo' :
                                        case 'header-ads' :
                                            if ( ! api.control.has(wpServusId) )
                                              return;

                                            if ( ! _is_checked(to) && $( '.hu-header-image-notice', api.control(wpServusId).container ).length ) {
                                                  $('.hu-header-image-notice', api.control(wpServusId).container ).remove();
                                            } else if ( _is_checked(to) ) {
                                                  if ( $( '.hu-header-image-notice', api.control(wpServusId).container ).length )
                                                    return;
                                                  var _html = [
                                                        '<span class="description customize-control-description hu-header-image-notice">',
                                                        '<?php echo html_entity_decode( $_header_img_notice ); ?>',
                                                        '</span>'
                                                  ].join('');
                                                  api.control(wpServusId).container.find('.customize-control-title').after( $.parseHTML( _html ) );
                                            }
                                        break;
                                  }

                                  //change opacity
                                  switch( shortServusId ) {
                                        case 'display-header-logo' :
                                        case 'logo-max-height' :
                                        case 'custom_logo' :
                                        case 'custom-logo' :
                                        case 'header-ads' :
                                            if ( ! api.control.has(wpServusId) )
                                              return;
                                            if ( ! _is_checked(to) ) {
                                                  $(api.control(wpServusId).container ).css('opacity', 1);
                                            } else {
                                                  $(api.control(wpServusId).container ).css('opacity', 0.6);
                                            }
                                        break;
                                  }
                            }//actions()
                      },
                      {
                            dominus : 'dynamic-styles',
                            servi : [
                                  'boxed',
                                  'font',
                                  'container-width',
                                  'sidebar-padding',
                                  'color-1',
                                  'color-2',
                                  'color-topbar',
                                  'color-header',
                                  'color-header-menu',
                                  'image-border-radius',
                                  'body-background',
                                  'color-footer'
                            ],
                            visibility : function ( to ) {
                                  return _is_checked(to);
                            }
                      },
                      {
                            dominus : 'blog-heading-enabled',
                            servi : [ 'blog-heading', 'blog-subheading' ],
                            visibility : function ( to ) {
                                  return _is_checked(to);
                            }
                      },
                      {
                            dominus : 'featured-posts-enabled',
                            servi : [
                                  'featured-category',
                                  'featured-posts-count',
                                  'featured-posts-full-content',
                                  'featured-slideshow',
                                  'featured-slideshow-speed',
                                  'featured-posts-include'
                            ],
                            visibility : function ( to ) {
                                  return _is_checked(to);
                            }
                      },
                      {
                            dominus : 'featured-slideshow',
                            servi : [ 'featured-slideshow-speed' ],
                            visibility : function ( to ) {
                                  return _is_checked(to);
                            }
                      },
                      {
                            dominus : 'about-page',
                            servi : [ 'help-button' ],
                            visibility : function ( to ) {
                                  return _is_checked( to );
                            }
                      }
                ]//dominiDeps {}
          );//_.extend()


          //add a notice in the Menus panel to easily disable the default page menu in the header
          <?php if ( ! is_multisite() ) : //no default menu for multisite installs ?>
            api.when('nav_menu_locations[header]', function( header_menu_loc_settting ) {
                  //bail for old version of WP
                  if ( ! _.has( api, 'section' ) || ! _.has( api, 'panel') )
                    return;

                  var _notice_selector = 'hu-menu-notice',
                      _toggle_menu_notice = function( show ) {
                        var $menu_panel_content = api.panel('nav_menus').container.find('.control-panel-content'),
                            notice_rendered = 0 !== $menu_panel_content.find( '.' + _notice_selector ).length,
                            _html = '<p class="description customize-control-description ' + _notice_selector +'"><?php echo html_entity_decode( $_header_menu_notice ); ?></p>',
                            _render_notice = function() {
                                  //defer the rendering when all sections of this panel have been embedded
                                  $.when.apply(
                                        null,
                                        ( function() {
                                              var _promises = [];
                                              //build the promises array
                                              api.section.each( function( _sec ){
                                                    if ( 'nav_menus' == _sec.panel() ) {
                                                          _promises.push( _sec.deferred.embedded );
                                                    }
                                              });
                                              return _promises;
                                        })
                                        )
                                  .then( function() {
                                        $menu_panel_content.append( $.parseHTML( _html ) );
                                  });
                            },
                            _toggle_notice = function() {
                                  if ( ! notice_rendered ) {
                                    _render_notice();
                                  };
                                  $('.' + _notice_selector, $menu_panel_content).toggle( show );
                            };

                        //bail if the menu panel is still not yet rendered
                        if ( ! $menu_panel_content.length )
                          return;

                        if ( api.topics && api.topics.ready && api.topics.ready.fired() ) {
                              _toggle_notice();
                        } else {
                              api.bind('ready', _toggle_notice );
                        }
                  };//_toggle_menu_notice

                  //API based toggling : maybe toggle the notice when nav_menu panel has been registered AND embedded
                  api.panel.when('nav_menus', function( panel_instance ){
                        panel_instance.deferred.embedded.then( function() {
                              _toggle_menu_notice( 0 == header_menu_loc_settting() );
                        });
                  });

                  //User action based toggling : Maybe toggle the notice when user changes the related settings
                  api.bind('ready', function() {
                        //bail if the [default-menu-header] has been removed
                        if ( ! api.has('czr_fn_theme_options[default-menu-header]') )
                          return;

                        //react to header menu location changes
                        header_menu_loc_settting.bind( function( to, from ) {
                              _toggle_menu_notice( 0 == to && _is_checked( api('czr_fn_theme_options[default-menu-header]')() ) );
                        } );
                        //react to czr_fn_theme_options[default-menu-header]
                        api('czr_fn_theme_options[default-menu-header]').bind( function( to ) {
                              _toggle_menu_notice( _is_checked( to ) && 0 == header_menu_loc_settting() );
                        });
                  });

            });
          <?php endif; ?>

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

                $('.tc-grid-toggle-controls').click( function() {
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
                  console.log('ever-here');
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