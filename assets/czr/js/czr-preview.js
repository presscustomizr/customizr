/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes
 */
( function( api, $, _ ) {

  //HOW DOES THE PREVIEW POST MESSAGE REFRESH WORKS ?
  //the control panel sends message with the postMessage jQuery method
  //@see the send method of the Messenger Class
  //this.previewer.send( 'setting', [ this.id, this() ] );

  //the previewer listens to message send to the preview window
  //@see receive method in the Messenger Class in customize-base
  //$( window ).on( 'message', function(e, o) { console.log(e, o) })
  //$( window ).on( 'message', this.receive );
  //On reception an event is triggered with the setting.id as name and the message.data as args
  //That's why it's possible to use api.bind( setting.id, callback(data) );

  //TEST
  // $( window ).on( 'message', function(e, o) {
  //   console.log('ON MESSAGE', e, o);
  // });

  //TEST => access any setting change with the setting event
  // wp.customize.bind( 'preview-ready', function() {
  //   wp.customize.preview.bind('setting', function(e, o) {
  //     console.log('ON SETTING', e, o);
  //   });
  // });


  /////////////// SEND SKOPES
  $( function() {
        api.preview.bind( 'sync', function( events ) {
              api.preview.send( 'czr-skopes-synced', {
                    czr_skopes : _wpCustomizeSettings.czr_skopes || [],
                    isChangesetDirty : _wpCustomizeSettings.isChangesetDirty || false,
                    skopeGlobalDBOpt : _wpCustomizeSettings.skopeGlobalDBOpt || [],
              } );
        });
  });

  /////////////// SETUP MODULES ACTIONS ON PREVIEW READY
  wp.customize.bind( 'preview-ready', function() {
        //SEKTIONS
        wp.customize.preview.bind('edit_sek', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-sek-id="' + o.id +'"]').length )
                return;
              $('html, body').animate({
                    scrollTop : $('[data-sek-id="' + o.id +'"]').offset().top - 50
              }, 'slow');
        });

        wp.customize.preview.bind('start_hovering_sek', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-sek-id="' + o.id +'"]').length )
                return;
              var $_sek = $('[data-sek-id="' + o.id +'"]'),
                  _width = $_sek.outerWidth(),
                  _height = $_sek.outerHeight();

              //remove all previous hover placeholder from sektion
              $_sek.closest('.czr-sektion').find('.czr-hover-placeholder').each( function(){ $(this).remove(); } );

              //apply placeholder
              $.when( $_sek.append( $( '<div/>', {
                    class : 'czr-hover-placeholder',
                    style : 'width:' + _width +'px;height:' + _height +'px;line-height:' + _height +'px;',
                    html : '<i class="material-icons">create</i>'
                })
              ) ).done( function() {
                    $('.czr-hover-placeholder').css('opacity', 1).fitText( 0.3, { minFontSize: '50px', maxFontSize: '100px' } );
              });
        });

        wp.customize.preview.bind('stop_hovering_sek', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-sek-id="' + o.id +'"]').length )
                return;

              var $_sek = $('[data-sek-id="' + o.id +'"]');
              $.when( $_sek.find('.czr-hover-placeholder').fadeOut(200) ).done( function() {$_sek.find('.czr-hover-placeholder').remove(); });
        });




        //MODULES
        wp.customize.preview.bind('edit_module', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-module-id="' + o.id +'"]').length )
                return;
              $('html, body').animate({
                    scrollTop : $('[data-module-id="' + o.id +'"]').offset().top - 50
              }, 'slow');
        });

        wp.customize.preview.bind('start_hovering_module', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-module-id="' + o.id +'"]').length )
                return;
              var $_module = $('[data-module-id="' + o.id +'"]'),
                  _width = $_module.outerWidth(),
                  _height = $_module.outerHeight();

              //remove all previous hover placeholder from sektion
              $_module.closest('.czr-sektion').find('.czr-hover-placeholder').each( function(){ $(this).remove(); } );

              //apply placeholder
              $.when( $_module.append( $( '<div/>', {
                    class : 'czr-hover-placeholder',
                    style : 'width:' + _width +'px;height:' + _height +'px;line-height:' + _height +'px;',
                    html : '<i class="material-icons">create</i>'
                })
              ) ).done( function() {
                    $('.czr-hover-placeholder').css('opacity', 1).fitText( 0.3, { minFontSize: '50px', maxFontSize: '100px' } );
              });
        });

        wp.customize.preview.bind('stop_hovering_module', function(o) {
              if ( ! _.has( o, 'id') || ! $('[data-module-id="' + o.id +'"]').length )
                return;

              var $_module = $('[data-module-id="' + o.id +'"]');
              $.when( $_module.find('.czr-hover-placeholder').fadeOut(200) ).done( function() {$_module.find('.czr-hover-placeholder').remove(); });
        });
  });







  /////////////// SEND UPDATED SERVER SIDE DATA TO THE PANEL
  /////////////// SET REACTIONS ON PANEL SETTING CHANGES
  //////////////////////////////////////////////////////////
  if ( CZRPreviewParams && ! CZRPreviewParams.preview_ready_event_exists ) {
        api.czr_preview = new api.CZR_preview();
  }
  else {
        api.bind( 'preview-ready', function(){
              //Talk with the panel when he informs us that the current preview frame is 'active'.
              //We could also use the 'sync' event, just before 'active'.
              api.preview.bind( 'active', function() {
                    api.czr_preview = new api.CZR_preview();
              });
        });
  }

  //FIRED ON API 'preview-ready'
  api.CZR_preview = api.Class.extend( {
        setting_cbs : {},
        subsetting_cbs : {},//nested sub settings
        input_cbs : {},
        _wp_sets : CZRPreviewParams.wpBuiltinSettings || [],
        _theme_options_name : CZRPreviewParams.themeOptions,
        initialize: function() {
              var self = this;
              //store the default control dependencies
              this.pre_setting_cbs = _.extend( self.pre_setting_cbs, self.getPreSettingCbs() );
              this.setting_cbs      = _.extend( self.setting_cbs, self.getSettingCbs() );
              this.subsetting_cbs   = _.extend( self.subsetting_cbs, self.getSubSettingCbs() );
              this.input_cbs        = _.extend( self.input_cbs, self.getInputCbs() );

              this.syncData();
              //api.trigger('czr-preview-ready');

              this.addCbs();
              //Remove this class if it's still there
              //=> added since changeset update, WP 4.7
              $( 'body' ).removeClass( 'wp-customizer-unloading' );
        },
        getPreSettingCbs : function() { return {}; },
        getSettingCbs : function() { return {}; },
        getSubSettingCbs : function() { return {}; },
        getInputCbs : function() { return {}; },
        syncData : function() {
          //send infos to panel
            api.preview.send( 'czr-query-data-ready', api.settings.czr_wpQueryInfos );
            api.preview.send( 'houston-widget-settings',
                  _.extend( _wpWidgetCustomizerPreviewSettings,
                        {
                              availableWidgetLocations : _.values( api.settings.availableWidgetLocations )
                        }
                  )
            );
            api.preview.send(
                  'czr-partial-refresh-data',
                  typeof( undefined ) === typeof( _customizePartialRefreshExports ) ? {} : _customizePartialRefreshExports.partials
            );

            //TEST
            //console.log('_wpCustomizeSettings', _wpCustomizeSettings, _wpCustomizeSettings.activeSections );
            //console.log('_wpWidgetCustomizerPreviewSettings', _wpWidgetCustomizerPreviewSettings);
            //console.log(' _customizePartialRefreshExports',  _customizePartialRefreshExports);
            //console.log(' IN PREVIEW : ', _wpCustomizeSettings );
        },

        addCbs : function() {
              var self = this;
              //@param args looks like :
              //{
              //    set_id        : module.control.id,
              //    data          : { module : {}, module_id : 'string'},
              //    value         : to
              //}
              //'pre_setting' is sent before 'setting'
              api.preview.bind( 'pre_setting', function( args ) {
                    args = args || {};
                    var _setId = args.set_id;
                    if ( ! api.has( self._build_setId( _setId ) ) )
                      return;
                    //first get the "nude" option name
                    var _opt_name = self._get_option_name( args.set_id );

                    //do we have custom callbacks for this setting ?
                    if ( ! _.has( self.pre_setting_cbs, _opt_name ) || ! _.isFunction( self.pre_setting_cbs[ _opt_name ] ) )
                      return;

                    //execute the cb
                    self.pre_setting_cbs[ _opt_name ]( args );
              });


              //'setting' event callback
              //=> this is the native WP postMessage event
              _.each( self.setting_cbs, function( _cb, _setId ) {
                    if ( ! api.has( self._build_setId( _setId ) ) )
                      return;
                    if ( _.isFunction( self.setting_cbs[ _setId ] ) ) {
                          api( self._build_setId(_setId) ).bind( self.setting_cbs[ _setId ] );
                    }
              } );


              //@param args looks like :
              //{
              //    set_id : this.id,
              //    model_id : model.id,
              //    changed_prop : _changed,
              //    value : model[_changed]
              //}
              //DEPRECATED ?
              api.preview.bind( 'sub_setting', function( args ) {
                    //first get the "nude" option name
                    var _opt_name = self._get_option_name( args.set_id );

                    //do we have custom callbacks for this subsetting ?
                    if ( ! _.has(self.subsetting_cbs, _opt_name) )
                      return;

                    //do we have a custom callback for this model id ?
                    if ( ! _.has( self.subsetting_cbs[ _opt_name ], args.changed_prop ) )
                      return;

                    //execute the cb
                    self.subsetting_cbs[ _opt_name ][ args.changed_prop ]( args );
              });

              //A module input can get a postMessage transport. This has to be declared in the js tmpl as a data-transport element property in the tmpl.
              //@param args looks like :
              //{
              //    set_id        : module.control.id,
              //    module_id     : module.id,//<= will allow us to target the right dom element on front end
              //    item_id       : input.input_parent.id,//<= can be the mod opt or the item
              //    input_id      : input.id,
              //    value         : to
              //}
              api.preview.bind( 'czr_input', function( args ) {
                    var _defaults = {
                          set_id : '',
                          module_id : '',
                          item_id : '',
                          input_id : '',
                          value : null
                    };

                    //normalizes
                    args = _.extend ( _defaults, args );

                    //first get the "nude" option name
                    var _opt_name = self._get_option_name( args.set_id );

                    //do we have custom callbacks for this subsetting ?
                    if ( ! _.has( self.input_cbs, _opt_name ) )
                      return;

                    //do we have a custom callback for this input id ?
                    if ( ! _.has( self.input_cbs[ _opt_name ], args.input_id ) )
                      return;

                    //execute the cb
                    self.input_cbs[ _opt_name ][ args.input_id ]( args );
              });

              //Inform the panel each time a partial refresh has been done
              //=> this will allow us to execute post partial refresh actions
              api.selectiveRefresh.bind( 'partial-content-rendered', function( params ) {
                      if ( ! _.has( params, 'partial' ) || ! _.has( params.partial, 'id' ) )
                        return;
                      var _shortOptName = params.partial.id;
                      api.preview.send( 'czr-partial-refresh-done', { set_id : self._build_setId( params.partial.id ) } );
              });
        },

        /******************************************
        * HELPERS
        ******************************************/
        /*
        * @return string
        * simple helper to build the setting id name if not a builtin wp setting id
        */
        _build_setId : function ( name ) {
              var self = this;
              //is wp built in ?
              if ( _.contains( self._wp_sets, name ) )
                return name;
              //else
              return -1 == name.indexOf( self._theme_options_name) ? [ self._theme_options_name + '[' , name  , ']' ].join('') : name;
        },

        _get_option_name : function(name) {
              var self = this;
              return name.replace(/\[|\]/g, '').replace(self._theme_options_name, '');
        },



        /*
        * @return boolean
        */
        _is_external : function( _href  ) {
              //EXT LINKS HELPERS
              // var _url_comp     = (location.host).split('.'),
              //   _nakedDomain  = new RegExp( _url_comp[1] + "." + _url_comp[2] );
              //gets main domain and extension, no matter if it is a n level sub domain
              //works also with localhost or numeric urls
              var _thisHref = $.trim( _href ),
                  _main_domain = (location.host).split('.').slice(-2).join('.'),
                  _reg = new RegExp( _main_domain );

              if ( _thisHref !== '' && _thisHref != '#' && _isValidURL( _thisHref ) )
                return ! _reg.test( _thisHref );
              return;
        },

        /*
        * @return boolean
        */
        _isValidURL : function(_url){
              //var _pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
              var _pattern = /(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
              return _pattern.test( _url );
        }
  });//api.Class.extend

} )( wp.customize, jQuery, _ );(function (api, $, _ ) {
      var $_body    = $( 'body' ),
          setting_cbs = api.CZR_preview.prototype.setting_cbs || {},
          input_cbs = api.CZR_preview.prototype.input_cbs || {},
          _settingsCbsExtend = {},
          _inputCbsExtend = {};

    _inputCbsExtend = {
          'tc_social_links' : {
                'social-size' : function( data ) {
                      if ( ! _.isObject( data ) || _.isUndefined( data.value ) || ! $('.social-icon', '.social-block').length )
                        return;
                      $('.social-icon', '.social-block').css( 'font-size', data.value + 'px');
                },
                'social-color' : function( data ) {
                      if ( ! _.isObject( data ) || _.isUndefined( data.value ) || _.isUndefined( data.input_parent_id ) )
                        return;
                      if ( ! $('.social-block').find('.social-icon[data-model-id=' + data.input_parent_id +']').length )
                        return;
                      $('.social-block').find('.social-icon[data-model-id=' + data.input_parent_id +']').css( 'color', data.value );
                }
          }
    };

    _settingsCbsExtend = {
        /******************************************
        * GLOBAL SETTINGS
        ******************************************/
          'blogname' : function(to) {
            $( 'a.site-title' ).text( to );
          },
          'blogdescription' : function(to) {
            //do nothing if this setting has partial refresh
            if ( _customizePartialRefreshExports && 'undefined' !== typeof _customizePartialRefreshExports.partials && 'undefined' !== typeof _customizePartialRefreshExports.partials.blogdescription )
              return;
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
            $( fontSelectors.body ).css( {
              'font-size' : to + 'px',
              'line-height' : '1.6em'
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
          'tc_display_boxed_navbar' : function( to ) {
            if ( false === to )
              $_body.addClass('no-navbar');
            else
              $_body.removeClass('no-navbar');
          },
          'tc_header_layout' : function( to ) {
                //sidenav
                /*
                * move the sidenav from the current position to the new one,
                * this means change the sidenav class sn-left|right(-eventual_effect)
                */
                if (  $( '#tc-sn' ).length > 0 ) {
                  var _refresh            = false,
                      _current_class      = $_body.attr('class').match(/sn-(left|right)(-\w+|$|\s)/),
                      _new_class          = 'right' != to ? 'right' : 'left';

                  if ( ! ( _current_class && _current_class.length > 2 ) )
                    return;

                  $_body.removeClass( _current_class[0] ).
                         addClass( _current_class[0].replace( _current_class[1] , _new_class ) );
                }

          },
          'tc_menu_position' : function( to ) {
            if ( 'aside' != api( api.CZR_preview.prototype._build_setId('tc_menu_style') ).get() ) {
              if ( 'pull-menu-left' == to )
                $('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
              else
                $('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
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
              $_body.removeClass('tc-sticky-header sticky-enabled').trigger('resize');
              $('#tc-reset-margin-top').css('margin-top' , '' );
            }
          },
          'tc_sticky_show_tagline' : function( to ) {
            if ( false !== to )
              $( '.tc-header' ).addClass('tc-tagline-on').removeClass('tc-tagline-off').trigger('resize');
            else
              $( '.tc-header' ).addClass('tc-tagline-off').removeClass('tc-tagline-on').trigger('resize');
          },
          'tc_sticky_show_title_logo' : function( to ) {
            if ( false !== to ) {
              $( '.tc-header' ).addClass('tc-title-logo-on').removeClass('tc-title-logo-off').trigger('resize');
            }
            else {
              $( '.tc-header' ).addClass('tc-title-logo-off').removeClass('tc-title-logo-on').trigger('resize');
            }
          },
          'tc_sticky_shrink_title_logo' : function( to ) {
            if ( false !== to )
              $( '.tc-header' ).addClass('tc-shrink-on').removeClass('tc-shrink-off').trigger('resize');
            else
              $( '.tc-header' ).addClass('tc-shrink-off').removeClass('tc-shrink-on').trigger('resize');
          },
          'tc_sticky_show_menu' : function( to ) {
            if ( false !== to )
              $( '.tc-header' ).addClass('tc-menu-on').removeClass('tc-menu-off').trigger('resize');
            else
              $( '.tc-header' ).addClass('tc-menu-off').removeClass('tc-menu-on').trigger('resize');
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
              $( '.tc-header' ).addClass('tc-wccart-on').removeClass('tc-wccart-off').trigger('resize');
            else
              $( '.tc-header' ).addClass('tc-wccart-off').removeClass('tc-wccart-on').trigger('resize');
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
          'tc_social_in_sidebar_title' : function( to ) {
            $( '.social-block .widget-title' , '.tc-sidebar' ).html( to );
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

        _settingsCbsExtend['tc_show_post_metas_' + this._context] = function( to ) {
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

        _settingsCbsExtend[ 'tc_show_post_navigation_' + this._context ] = function( to ) {
          if ( false === to )
            $_post_nav.hide('slow').addClass('hide-post-navigation');
          else
            $_post_nav.show('fast').removeClass('hide-post-navigation');
        };//fn
        return false;
      });

      $.extend( api.CZR_preview.prototype, {
          setting_cbs : $.extend( setting_cbs, _settingsCbsExtend ),
          input_cbs : $.extend( input_cbs, _inputCbsExtend )
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