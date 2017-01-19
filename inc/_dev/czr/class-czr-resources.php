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
      //preview scripts
      //set with priority 20 to be fired after czr_fn_customize_store_db_opt in HU_utils
      add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_customize_preview_js' ), 20 );
      //exports some wp_query informations. Updated on each preview refresh.
      //add_action( 'customize_preview_init'                    , array( $this, 'czr_fn_add_preview_footer_action' ), 20 );
      //Add the control dependencies
      //add_action( 'customize_controls_print_footer_scripts'   , array( $this, 'czr_fn_extend_ctrl_dependencies' ), 10 );

      //Add various dom ready
      //add_action( 'customize_controls_print_footer_scripts'   , array( $this, 'czr_fn_add_various_dom_ready_actions' ), 10 );

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
            apply_filters('czr_fn_js_customizer_preview_params' ,
              array(
                'themeFolder'     => get_template_directory_uri(),
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
            'FPControls' => array_merge( $fp_controls , $page_dropdowns , $text_fields ),
            'AjaxUrl'       => admin_url( 'admin-ajax.php' ),
            'TCNonce'       => wp_create_nonce( 'tc-customizer-nonce' ),
            'themeName'     => CZR___::$theme_name,
            'HideDonate'    => CZR_customize::$instance -> czr_fn_get_hide_donate_status(),
            'ShowCTA'       => ( true == CZR_utils::$inst->czr_fn_opt('tc_hide_donate') && ! get_transient ('tc_cta') ) ? true : false,
            'defaultSliderHeight' => 500,//500px, @todo make sure we can hard code it here
            'translatedStrings'    => $this -> czr_fn_get_translated_strings(),

            'themeOptions'     => CZR_THEME_OPTIONS,
            'optionAjaxAction' => CZR_OPT_AJAX_ACTION,

            'isDevMode' => ( defined('WP_DEBUG') && true === WP_DEBUG ) || ( defined('TC_DEV') && true === TC_DEV ),
            'themeSettingList' => CZR_utils::$_theme_setting_list,
            'wpBuiltinSettings' => CZR_customize::$instance -> czr_fn_get_wp_builtin_settings(),
            'css_attr'        => CZR_customize::$instance -> czr_fn_get_controls_css_attr(),
            'isThemeSwitchOn' => isset( $_GET['theme'])
          )
        )
      );

    }



    //hook : customize_preview_init
    function czr_fn_add_preview_footer_action() {
      //Add the postMessages actions
      add_action( 'wp_footer', 'czr_fn_extend_postmessage_cb', 1000 );
      add_action( 'wp_footer', 'czr_fn_add_customize_preview_data' , 20 );

    }

    //hook : wp_footer in the preview
    function czr_fn_extend_postmessage_cb() {
      ?>
      <script id="preview-settings-cb" type="text/javascript">
        (function (api, $, _ ) {
              var $_body    = $( 'body' ),
                setting_cbs = api.CZR_preview.prototype.setting_cbs || {},
                subsetting_cbs = api.CZR_preview.prototype.subsetting_cbs || {};

              $.extend( api.CZR_preview.prototype, {
                  setting_cbs : $.extend( setting_cbs, {
                        blogname : function(to) {
                          var self = this,
                              _proto_ = api.CZR_preview.prototype,
                              _hasLogo,
                              _logoSet;
                          //the logo was previously set with a custom hueman theme option => custom-logo
                          if ( api.has( _proto_._build_setId('custom-logo') ) )
                            _logoSet ? api( _proto_._build_setId('custom-logo') ).get() : '';
                          else if ( api.has( _proto_._build_setId('custom_logo') ) )
                             _logoSet ? api( _proto_._build_setId('custom_logo') ).get() : '';

                          _hasLogo = ( _.isNumber(_logoSet) && _logoSet > 0 ) || ( ! _.isEmpty(_logoSet) && ( false !== _logoSet ) );

                          if ( _hasLogo )
                            return;
                          $( '.site-title a' ).text( to );
                        },
                        blogdescription : function(to) {
                          $( '.site-description' ).text( to );
                        },
                        'body-background' :  function(to) {
                          $('body').css('background-color', to);
                        },
                        'color-topbar' : function(to) {
                          $('.search-expand, #nav-topbar.nav-container, #nav-topbar .nav ul').css('background-color', to);
                        },
                        'color-header': function(to) {
                          $('#header').css('background-color', to);
                        },
                        'color-header-menu' : function(to) {
                          $('#nav-header.nav-container, #nav-header .nav ul').css('background-color', to);
                        },
                        'color-footer' : function(to) {
                          $('#footer-bottom').css('background-color', to);
                        },
                        credit : function(to) {
                          $( '#footer-bottom #credit' ).slideToggle();
                        }
                  }),//_.extend()



                  subsetting_cbs : $.extend( subsetting_cbs, {
                      'social-links' : {
                          'title' : function( obj ) {
                            $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).attr('title', obj.value );
                          },
                          'social-color' : function( obj ) {
                            $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).css('color', obj.value );
                          },
                          'social-icon' : function( obj ) {
                            var $_el = $( '#'+ obj.model_id, '.social-links' ).find('i'),
                                _classes = ! _.isUndefined( $_el.attr('class') ) ? $_el.attr('class').split(' ') : [],
                                _prev = '';

                            //find the previous class
                            _.filter(_classes, function(_c){
                              if ( -1 != _c.indexOf('fa-') )
                                _prev = _c;
                            });

                            $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).find('i').removeClass(_prev).addClass( obj.value );
                          },
                          'social-link' : function( obj ) {
                            var self = this;
                            $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).attr('href', ! self._isValidURL(obj.value) ? 'javascript:void(0);' : obj.value );
                          },
                          'social-target' : function( obj ) {
                            if ( 0 !== ( obj.value * 1 ) )
                              $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).attr('target', "_blank");
                            else
                              $( '[data-model-id="'+ obj.model_id +'"]', '.social-links' ).removeAttr('target');
                          }
                      }
                  })
              });
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
          $val = czr_fn_is_home();

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

                /* WIDGET PANEL ICON */
                if ( $('.control-panel-widgets').find('.accordion-section-title').first().length ) {
                      $('.control-panel-widgets').find('.accordion-section-title')
                            .first()
                            .prepend( $('<span/>', {class:'fa fa-magic'} ) );
                }
            });//end of $( function($) ) dom ready
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
                'locations' => __('Location(s)', 'customizr'),
                'contexts' => __('Context(s)', 'customizr'),
                'notset' => __('Not set', 'customizr'),
                'rss' => __('Rss', 'customizr'),
                'selectSocialIcon' => __('Select a social icon', 'customizr'),
                'followUs' => __('Follow us on', 'customizr'),
                'successMessage' => __('Done !', 'customizr'),
                'socialLinkAdded' => __('New Social Link created ! Scroll down to edit it.', 'customizr'),
                'selectBgRepeat'  => __('Select repeat property', 'customizr'),
                'selectBgAttachment'  => __('Select attachment property', 'customizr'),
                'selectBgPosition'  => __('Select position property', 'customizr'),
                'widgetZone' => __('Widget Zone', 'customizr'),
                'widgetZoneAdded' => __('New Widget Zone created ! Scroll down to edit it.', 'customizr'),
                'inactiveWidgetZone' => __('Inactive in current context/location', 'customizr'),
                'unavailableLocation' => __('Unavailable location. Some settings must be changed.', 'customizr'),
                'locationWarning' => __('A selected location is not available with the current settings.', 'customizr'),
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