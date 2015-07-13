/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes
 * @package Customizr
 * @since Customizr 1.0
 */


( function( $ ) {
    var api       = wp.customize,
        $_body    = $( 'body' ),
        $_brand   = $( '.brand' ),
        $_header  = $( '.tc-header' ),
        $_bmenu   = $_header.find('.btn-toggle-nav'),
        $_sidenav = $( '#tc-sn' );

	api( 'tc_theme_options[tc_skin]' , function( value ) {
		value.bind( function( to ) {
			if ( TCPreviewParams && TCPreviewParams.themeFolder ) {
				//add a new link to the live stylesheet instead of replacing the actual skin link => avoid the flash of unstyle content during the skin load
				var $skin_style_element = ( 0 === $('#live-skin-css').length ) ? $('<link>' , { id : 'live-skin-css' , rel : 'stylesheet'}) : $('#live-skin-css'),
            skinName = to.replace('.css' , '.min.css'),
            skinURL = [ TCPreviewParams.themeFolder , '/inc/assets/css/' , skinName ].join('');

        //check if the customSkin param is filtered
        if ( TCPreviewParams.customSkin && TCPreviewParams.customSkin.skinName && TCPreviewParams.customSkin.fullPath )
          skinURL = to == TCPreviewParams.customSkin.skinName ? TCPreviewParams.customSkin.fullPath : skinURL;

        $skin_style_element.attr('href' , skinURL );
				if (  0 === $('#live-skin-css').length )
					$('head').append($skin_style_element);
			}
		} );
	} );
	// Site title and description.
	api( 'blogname' , function( value ) {
		value.bind( function( to ) {
			$( 'a.site-title' ).html( to );
		} );
	} );
	api( 'blogdescription' , function( value ) {
		value.bind( function( to ) {
			$( 'h2.site-description' ).html( to );
		} );
	} );

	//featured page one text
	api( 'tc_theme_options[tc_featured_text_one]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-one' ).html( to );
		} );
	} );

	//featured page two text
	api( 'tc_theme_options[tc_featured_text_two]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-two' ).html( to );
		} );
	} );

	//featured page three text
	api( 'tc_theme_options[tc_featured_text_three]' , function( value ) {
		value.bind( function( to ) {
			$( '.widget-front p.fp-text-three' ).html( to );
		} );
	} );

	//featured page button text
	api( 'tc_theme_options[tc_featured_page_button_text]' , function( value ) {
		value.bind( function( to ) {
            if ( to )
                $( '.fp-button' ).html( to ).removeClass( 'hidden');
            else
                $( '.fp-button' ).addClass( 'hidden' );
		} );
	} );

	// Hook into background color change and adjust body class value as needed.
	api( 'background_color' , function( value ) {
		value.bind( function( to ) {
			if ( '#ffffff' == to || '#fff' == to )
				$_body.addClass( 'custom-background-white' );
			else if ( '' === to )
				$_body.addClass( 'custom-background-empty' );
			else
				$_body.removeClass( 'custom-background-empty custom-background-white' );
		} );
	} );

	//All icons
	api( 'tc_theme_options[tc_show_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-title').add('h1').add('h2').removeClass('format-icon');
				$('.tc-sidebar').add('.footer-widgets').addClass('no-widget-icons');
			}
			else {
				$('.entry-title').add('h1').add('h2').addClass('format-icon');
				$('.tc-sidebar').add('.footer-widgets').removeClass('no-widget-icons');
			}
		} );
	} );

	//Icons : page
	api( 'tc_theme_options[tc_show_page_title_icon]' , function( value ) {
		value.bind( function( to ) {
      //disable if grid customizer on
      if ( $('.tc-gc').length )
        return;

			if ( false === to ) {
				$('.entry-title' , '.page').removeClass('format-icon');
			}
			else {
				$('.entry-title' , '.page').addClass('format-icon');
			}
		} );
	} );

	//Icons : single post
	api( 'tc_theme_options[tc_show_post_title_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.entry-title' , '.single').removeClass('format-icon');
			}
			else {
				$('.entry-title' , '.single').addClass('format-icon');
			}
		} );
	} );

	//Icons : Archive title
	api( 'tc_theme_options[tc_show_archive_title_icon]' , function( value ) {
		value.bind( function( to ) {
      //disable if grid customizer on
      if ( $('.tc-gc').length )
        return;
			if ( false === to ) {
				$('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').removeClass('format-icon');
			}
			else {
				$('archive h1.entry-title, .blog h1.entry-title, .search h1, .author h1').addClass('format-icon');
			}
		} );
	} );

	//Icons : Posts in lists titles
	api( 'tc_theme_options[tc_show_post_list_title_icon]' , function( value ) {
		value.bind( function( to ) {
      //disable if grid customizer on
      if ( $('.tc-gc').length )
        return;

			if ( false === to ) {
				$('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').removeClass('format-icon');
			}
			else {
				$('.archive article .entry-title, .blog article .entry-title, .search article .entry-title, .author article .entry-title').addClass('format-icon');
			}
		} );
	} );



	//Widget Icons
	api( 'tc_theme_options[tc_show_sidebar_widget_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.tc-sidebar').addClass('no-widget-icons');
			else
				$('.tc-sidebar').removeClass('no-widget-icons');
		} );
	} );
	api( 'tc_theme_options[tc_show_footer_widget_icon]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$('.footer-widgets').addClass('no-widget-icons');
			else
				$('.footer-widgets').removeClass('no-widget-icons');
		} );
	});

    //Post metas
    var _post_metas_context = [
          { _context : 'home', _container : '.home' },
          { _context : 'single_post', _container: '.single'},
          { _context : 'post_lists', _container: 'body:not(.single, .home)'}
        ];

	api( 'tc_theme_options[tc_show_post_metas]' , function( value ) {
		value.bind( function( to ) {
            var $_entry_meta = $('.entry-header .entry-meta', '.article-container');
			if ( false === to )
				$_entry_meta.hide('slow');
            else if (! $_body.hasClass('hide-post-metas') ){
				$_entry_meta.show('fast');
                $_body.removeClass('hide-all-post-metas');
            }
		} );
	} );

    $.each( _post_metas_context, function() {
        var $_post_metas = $('.entry-header .entry-meta', this._container + ' .article-container' );
        if ( $_post_metas.length > 0 ){
            api( 'tc_theme_options[tc_show_post_metas_' + this._context + ']' , function( value ) {
                value.bind( function( to ) {
                    if ( false === to ){
                      $_post_metas.hide('slow');
                      $_body.addClass('hide-post-metas');
                    }else{
                      $_post_metas.show('fast');
                      $_body.removeClass('hide-post-metas');
                    }
                } );
            });
            // if we matched a context break
            return false;
        }
    }); /* end contextual post metas*/

    // Link hover effect
	api( 'tc_theme_options[tc_link_hover_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$_body.removeClass('tc-fade-hover-links');
			else
				$_body.addClass('tc-fade-hover-links');
		} );
	});

    //Posts navigation
    var _post_nav_context = [
          { _context : 'page', _container : 'body.page' },
          { _context : 'single', _container: 'body.single'},
          { _context : 'archive', _container: 'body.archive, body.blog'}
        ];

	api( 'tc_theme_options[tc_show_post_navigation]' , function( value ) {
        var $_post_nav = $('#nav-below');
		value.bind( function( to ) {
			if ( false === to )
				$_post_nav.hide('slow');
            else if ( ! $_post_nav.hasClass('hide-post-navigation') )
				$_post_nav.removeClass('hide-all-post-navigation').show('fast');
		} );
	  } );

    $.each( _post_nav_context, function() {
        var $_post_nav = $('#nav-below', this._container );
        if ( $_post_nav.length > 0 ){
            api( 'tc_theme_options[tc_show_post_navigation_' + this._context + ']' , function( value ) {
                value.bind( function( to ) {
                    if ( false === to )
                      $_post_nav.hide('slow').addClass('hide-post-navigation');
                    else
                      $_post_nav.show('fast').removeClass('hide-post-navigation');
                } );
            });
            // if we matched a context break
            return false;
        }
    }); /* end contextual post nav*/

    //Post thumbnails
	api( 'tc_theme_options[tc_post_list_thumb_height]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-rectangular-thumb').css('max-height' , to + 'px');
      if ( 0 !== $('.tc-rectangular-thumb').find('img').length )
        $('.tc-rectangular-thumb').find('img').trigger('refresh-height');//listened by the jsimgcentering $ plugin
		} );
	});
	api( 'tc_theme_options[tc_single_post_thumb_height]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-rectangular-thumb').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
		} );
	});
	api( 'tc_theme_options[tc_show_tagline]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.site-description').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.site-description').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_display_boxed_navbar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to )
				$_body.addClass('no-navbar');
			else
				$_body.removeClass('no-navbar');
		} );
	});
	api( 'tc_theme_options[tc_header_layout]' , function( value ) {
		value.bind( function( to ) {
            
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
		} );
	});
	api( 'tc_theme_options[tc_social_in_header]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.tc-header .social-block').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.tc-header .social-block').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_social_in_footer]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('.tc-footer-social-links-wrapper' , '#footer').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('.tc-footer-social-links-wrapper' , '#footer').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_social_in_left-sidebar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('#left .social-block' , '.tc-sidebar').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('#left .social-block' , '.tc-sidebar').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_social_in_right-sidebar]' , function( value ) {
		value.bind( function( to ) {
			if ( false === to ) {
				$('#right .social-block' , '.tc-sidebar').hide('slow');
				$(window).trigger('resize');
			}
			else {
				$('#right .social-block' , '.tc-sidebar').show('fast');
				$(window).trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_social_in_sidebar_title]' , function( value ) {
		value.bind( function( to ) {
			$( '.social-block .widget-title' , '.tc-sidebar' ).html( to );
			if ( ! to )
				$('.social-block' , '.tc-sidebar').hide('slow');
			else
				$('.social-block' , '.tc-sidebar').show('fast');
		} );
	});
	api( 'tc_theme_options[tc_menu_position]' , function( value ) {
		value.bind( function( to ) {
			if ( 'pull-menu-left' == to )
				$('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
			else
				$('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
            
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
		} );
	});
	api( 'tc_theme_options[tc_menu_submenu_fade_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.navbar-wrapper').addClass('tc-submenu-fade');
			else
				$('.navbar-wrapper').removeClass('tc-submenu-fade');
		} );
	});
	api( 'tc_theme_options[tc_menu_submenu_item_move_effect]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$('.navbar-wrapper').addClass('tc-submenu-move');
			else
				$('.navbar-wrapper').removeClass('tc-submenu-move');
		} );
	});
    api( 'tc_theme_options[tc_sticky_header]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$_body.addClass('tc-sticky-header').trigger('resize');
				//$('#tc-reset-margin-top').css('margin-top' , '');
			}
			else {
				$_body.removeClass('tc-sticky-header').trigger('resize');
				$('#tc-reset-margin-top').css('margin-top' , '' );
			}
		} );
	});
	api( 'tc_theme_options[tc_sticky_show_tagline]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$_header.addClass('tc-tagline-on').removeClass('tc-tagline-off').trigger('resize');
			else
				$_header.addClass('tc-tagline-off').removeClass('tc-tagline-on').trigger('resize');
		} );
	});
	api( 'tc_theme_options[tc_sticky_show_title_logo]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$_header.addClass('tc-title-logo-on').removeClass('tc-title-logo-off').trigger('resize');
			}
			else {
				$_header.addClass('tc-title-logo-off').removeClass('tc-title-logo-on').trigger('resize');
			}
		} );
	});
	api( 'tc_theme_options[tc_sticky_shrink_title_logo]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$_header.addClass('tc-shrink-on').removeClass('tc-shrink-off').trigger('resize');
			else
				$_header.addClass('tc-shrink-off').removeClass('tc-shrink-on').trigger('resize');
		} );
	});
	api( 'tc_theme_options[tc_sticky_show_menu]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to )
				$_header.addClass('tc-menu-on').removeClass('tc-menu-off').trigger('resize');
			else
				$_header.addClass('tc-menu-off').removeClass('tc-menu-on').trigger('resize');
		} );
	});
	api( 'tc_theme_options[tc_sticky_z_index]' , function( value ) {
		value.bind( function( to ) {
			$('.tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header').css('z-index' , to);
		} );
	});
	api( 'tc_theme_options[tc_custom_css]' , function( value ) {
		value.bind( function( to ) {
			$('#option-custom-css').remove();
			var $style_element = ( 0 === $('#live-custom-css').length ) ? $('<style>' , { id : 'live-custom-css'}) : $('#live-custom-css');
			//sanitize string => remove html tags
      to = to.replace(/(<([^>]+)>)/ig,"");

      if (  0 === $('#live-custom-css').length )
				$('head').append($style_element.html(to));
			else
				$style_element.html(to);
		} );
	} );
	api( 'tc_theme_options[tc_slider_default_height]' , function( value ) {
		value.bind( function( to ) {
			$('#customizr-slider').addClass('custom-slider-height');
			$('.carousel .item').css('line-height' , to + 'px').css('max-height', to + 'px').css('min-height', to + 'px').trigger('resize');
			$('.tc-slider-controls').css('line-height' , to + 'px').css('max-height', to + 'px').trigger('resize');
		} );
	});
	api( 'tc_theme_options[tc_sticky_transparent_on_scroll]' , function( value ) {
		value.bind( function( to ) {
			if ( false !== to ) {
				$_body.addClass('tc-transparent-on-scroll');
				$_body.removeClass('tc-solid-color-on-scroll');
			}
			else {
				$_body.removeClass('tc-transparent-on-scroll');
				$_body.addClass('tc-solid-color-on-scroll');
			}
		} );
	});
	api( 'tc_theme_options[tc_post_metas_update_notice_text]' , function( value ) {
		value.bind( function( to ) {
			$( '.tc-update-notice' ).html( to );
		} );
	} );

	api( 'tc_theme_options[tc_comment_bubble_color]' , function( value ) {
		value.bind( function( to ) {
			$('#custom-bubble-color').remove();
			var $style_element	= $('<style>' , { id : 'custom-bubble-color'}),
				bubble_live_css = '';

			//custom bubble
			bubble_live_css += '.comments-link .tc-comment-bubble {border-color:' + to + ';color:' + to + '}';
			bubble_live_css += '.comments-link .tc-comment-bubble:before {border-color:' + to + '}';
			$('head').append($style_element.html(bubble_live_css));
		} );
	} );

	api( 'tc_theme_options[tc_post_metas_update_notice_format]' , function( value ) {
		value.bind( function( to ) {
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
		} );
	} );

  api( 'tc_theme_options[tc_fonts]' , function( value ) {
    value.bind( function( to ) {
      var font_groups = TCPreviewParams.fontPairs;
      $.each( font_groups , function( key, group ) {
        if ( group.list[to]) {
          if ( -1 != to.indexOf('_g_') )
            addGfontLink( group.list[to][1] );
          toStyle( group.list[to][1] );
        }
      });
    } );
  } );

  function addGfontLink (fonts ) {
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
  }

  function toStyle( fonts ) {
    var selector_fonts = fonts.split('|');
    $.each( selector_fonts , function( key, single_font ) {
      var split         = single_font.split(':'),
          css_properties = {},
          font_family, font_weight = '',
          fontSelectors  = TCPreviewParams.fontSelectors;

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
  }

  api( 'tc_theme_options[tc_body_font_size]' , function( value ) {
    value.bind( function( to ) {
      var fontSelectors  = TCPreviewParams.fontSelectors;
      $( fontSelectors.body ).not('.social-icon').css( {
        'font-size' : to + 'px',
        'line-height' : Number((to * 19 / 14).toFixed()) + 'px'
      });
    } );
  } );

  var _url_comp     = (location.host).split('.'),
      _nakedDomain  = new RegExp( _url_comp[1] + "." + _url_comp[2] );

  function _is_external( _href  ) {
    var _thisHref = $.trim( _href );
    if ( _thisHref !== '' && _thisHref != '#' && _isValidURL(_thisHref) )
        return ! _nakedDomain.test(_thisHref) ? true : false;
  }

  function _isValidURL(url){
      var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      if (pattern.test(url)){
          return true;
      }
      return false;
  }

  api( 'tc_theme_options[tc_ext_link_style]' , function( value ) {
    value.bind( function( to ) {
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
    } );
  } );
  api( 'tc_theme_options[tc_ext_link_target]' , function( value ) {
    value.bind( function( to ) {
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
    } );
  } );

  //GRID
  api( 'tc_theme_options[tc_grid_shadow]' , function( value ) {
    value.bind( function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-grid-shadow');
      else
        $('.article-container').removeClass('tc-grid-shadow');
    } );
  });
  api( 'tc_theme_options[tc_grid_bottom_border]' , function( value ) {
    value.bind( function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-grid-border');
      else
        $('.article-container').removeClass('tc-grid-border');
    } );
  });
  api( 'tc_theme_options[tc_grid_icons]' , function( value ) {
    value.bind( function( to ) {
      if ( false === to )
        $('.tc-grid-icon').each( function() { $(this).fadeOut(); } );
      else
        $('.tc-grid-icon').each( function() { $(this).fadeIn(); } );
    } );
  });

  //GALLERY
  api( 'tc_theme_options[tc_gallery_style]' , function( value ) {
    value.bind( function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-gallery-style');
      else
        $('.article-container').removeClass('tc-gallery-style');
    } );
  });

} )( jQuery );
