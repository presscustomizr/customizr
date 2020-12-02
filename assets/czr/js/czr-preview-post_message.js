// global vars :
// serverPreviewParams <= printed with the base fmk
// themeServerPreviewParams <= printed from the theme
(function (api, $, _ ) {
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
            if ( serverPreviewParams && serverPreviewParams.themeFolder ) {
              //add a new link to the live stylesheet instead of replacing the actual skin link => avoid the flash of unstyle content during the skin load
              var $skin_style_element = ( 0 === $('#live-skin-css').length ) ? $('<link>' , { id : 'live-skin-css' , rel : 'stylesheet'}) : $('#live-skin-css'),
                  skinName = to.replace('.css' , '.min.css'),
                  skinURL = [ serverPreviewParams.themeFolder , '/inc/assets/css/' , skinName ].join('');

              //check if the customSkin param is filtered
              if ( themeServerPreviewParams.customSkin && themeServerPreviewParams.customSkin.skinName && themeServerPreviewParams.customSkin.fullPath )
                skinURL = to == themeServerPreviewParams.customSkin.skinName ? themeServerPreviewParams.customSkin.fullPath : skinURL;

              $skin_style_element.attr('href' , skinURL );
              if (  0 === $('#live-skin-css').length )
                $('head').append($skin_style_element);
            }
          },
          'tc_fonts' : function( to ) {
            var font_groups = themeServerPreviewParams.fontPairs;
            $.each( font_groups , function( key, group ) {
              if ( group.list[to]) {
                if ( -1 != to.indexOf('_g_') )
                  _addGfontLink( group.list[to][1] );
                _toStyle( group.list[to][1] );
              }
            });
          },
          'tc_body_font_size' : function( to ) {
            var fontSelectors  = themeServerPreviewParams.fontSelectors;
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
              if ( 'pull-menu-center' == to ) {
                to += serverPreviewParams.isRTL ?  ' pull-menu-left' : ' pull-menu-right';
              }
              $('.navbar-wrapper').removeClass('pull-menu-right pull-menu-left pull-menu-center').addClass(to);

            }
          },
          'tc_second_menu_position' : function(to) {
            if ( 'pull-menu-center' == to ) {
              to += serverPreviewParams.isRTL ?  ' pull-menu-left' : ' pull-menu-right';
            }
            $('.navbar-wrapper').removeClass('pull-menu-right pull-menu-left pull-menu-center').addClass(to);
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
            $('.tc-rectangular-thumb', '.tc-single-post-thumbnail-wrapper').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
          },
          'tc_single_page_thumb_height' : function( to ) {
            $('.tc-rectangular-thumb', '.tc-single-page-thumbnail-wrapper').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
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
        { _context : 'home', _container : 'body.home' },
        { _context : 'page', _container : 'body.page' },
        { _context : 'single', _container: 'body.single' },
        { _context : 'archive', _container: 'body.archive, body.blog:not(.home)' }
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
              fontSelectors  = themeServerPreviewParams.fontSelectors;

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
