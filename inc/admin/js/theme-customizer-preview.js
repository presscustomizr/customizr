/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title, description, and background color changes
 * @package Customizr
 * @since Customizr 1.0
 */
( function( $, _ ) {
  var api       = wp.customize,
      $_body    = $( 'body' ),
      $_brand   = $( '.brand' ),
      $_header  = $( '.tc-header' ),
      $_bmenu   = $_header.find('.btn-toggle-nav'),
      $_sidenav = $( '#tc-sn' ),
      _wp_sets  = ['blogname', 'blogdescription', 'background_color'],
      _preview_cbs = {},
      fireCzrPrev  = function(){
        _.map( _preview_cbs, function( _cb, _setId ) {
          if ( ! api.has( _build_setId(_setId) ) )
            return;
          api( _build_setId(_setId) ).bind( _preview_cbs[_setId] );
        } );
      };

  //Patch for wp versions before 4.1 => preview-ready signal isn't triggered
  if ( TCPreviewParams && ! TCPreviewParams.preview_ready_event_exists )
    $(document).ready(fireCzrPrev);
  else
    api.bind( 'preview-ready', fireCzrPrev );

  /******************************************
  * GLOBAL SETTINGS
  ******************************************/
  $.extend( _preview_cbs, {
    blogname : function(to) {
        $( 'a.site-title' ).html( to );
    },
    blogdescription : function(to) {
        $( 'h2.site-description' ).html( to );
    },
    background_color : function( to ) {
      if ( '#ffffff' == to || '#fff' == to )
        $_body.addClass( 'custom-background-white' );
      else if ( '' === to )
        $_body.addClass( 'custom-background-empty' );
      else
        $_body.removeClass( 'custom-background-empty custom-background-white' );
    },
    tc_skin : function( to ) {
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
    },
    tc_fonts : function( to ) {
      var font_groups = TCPreviewParams.fontPairs;
      $.each( font_groups , function( key, group ) {
        if ( group.list[to]) {
          if ( -1 != to.indexOf('_g_') )
            _addGfontLink( group.list[to][1] );
          _toStyle( group.list[to][1] );
        }
      });
    },
    tc_body_font_size : function( to ) {
      var fontSelectors  = TCPreviewParams.fontSelectors;
      $( fontSelectors.body ).not('.social-icon').css( {
        'font-size' : to + 'px',
        'line-height' : Number((to * 19 / 14).toFixed()) + 'px'
      });
    },
    tc_link_hover_effect : function( to ) {
      if ( false === to )
        $_body.removeClass('tc-fade-hover-links');
      else
        $_body.addClass('tc-fade-hover-links');
    },
    tc_ext_link_style : function( to ) {
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
    tc_ext_link_target : function( to ) {
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
    tc_show_title_icon :  function( to ) {
      if ( false === to ) {
        $('.entry-title').add('h1').add('h2').removeClass('format-icon');
        $('.tc-sidebar').add('.footer-widgets').addClass('no-widget-icons');
      }
      else {
        $('.entry-title').add('h1').add('h2').addClass('format-icon');
        $('.tc-sidebar').add('.footer-widgets').removeClass('no-widget-icons');
      }
    },
    tc_show_page_title_icon : function( to ) {
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
    tc_show_post_title_icon : function( to ) {
      if ( false === to ) {
        $('.entry-title' , '.single').removeClass('format-icon');
      }
      else {
        $('.entry-title' , '.single').addClass('format-icon');
      }
    },
    tc_show_archive_title_icon : function( to ) {
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
    tc_show_post_list_title_icon : function( to ) {
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
    tc_show_sidebar_widget_icon : function( to ) {
      if ( false === to )
        $('.tc-sidebar').addClass('no-widget-icons');
      else
        $('.tc-sidebar').removeClass('no-widget-icons');
    },
    tc_show_footer_widget_icon : function( to ) {
      if ( false === to )
        $('.footer-widgets').addClass('no-widget-icons');
      else
        $('.footer-widgets').removeClass('no-widget-icons');
    },
    //Smooth Scroll
    tc_smoothscroll : function(to) {
      if ( false === to )
        smoothScroll._cleanUp();
      else
        smoothScroll._maybeFire();
    }
  });//$.extend()



  /******************************************
  * HEADER
  ******************************************/
  $.extend( _preview_cbs, {
    tc_show_tagline : function( to ) {
      if ( false === to ) {
        $('.site-description').hide('slow');
        $(window).trigger('resize');
      }
      else {
        $('.site-description').show('fast');
        $(window).trigger('resize');
      }
    },
    tc_display_boxed_navbar : function( to ) {
      if ( false === to )
        $_body.addClass('no-navbar');
      else
        $_body.removeClass('no-navbar');
    },
    tc_header_layout : function( to ) {

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
    tc_menu_position : function( to ) {
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
    tc_second_menu_position : function(to) {
      if ( 'pull-menu-left' == to )
        $('.navbar-wrapper').addClass(to).removeClass('pull-menu-right');
      else
        $('.navbar-wrapper').addClass(to).removeClass('pull-menu-left');
    },
    tc_menu_submenu_fade_effect : function( to ) {
      if ( false !== to )
        $('.navbar-wrapper').addClass('tc-submenu-fade');
      else
        $('.navbar-wrapper').removeClass('tc-submenu-fade');
    },
    tc_menu_submenu_item_move_effect : function( to ) {
      if ( false !== to )
        $('.navbar-wrapper').addClass('tc-submenu-move');
      else
        $('.navbar-wrapper').removeClass('tc-submenu-move');
    },
    tc_sticky_header : function( to ) {
      if ( false !== to ) {
        $_body.addClass('tc-sticky-header').trigger('resize');
        //$('#tc-reset-margin-top').css('margin-top' , '');
      }
      else {
        $_body.removeClass('tc-sticky-header').trigger('resize');
        $('#tc-reset-margin-top').css('margin-top' , '' );
      }
    },
    tc_sticky_show_tagline : function( to ) {
      if ( false !== to )
        $_header.addClass('tc-tagline-on').removeClass('tc-tagline-off').trigger('resize');
      else
        $_header.addClass('tc-tagline-off').removeClass('tc-tagline-on').trigger('resize');
    },
    tc_sticky_show_title_logo : function( to ) {
      if ( false !== to ) {
        $_header.addClass('tc-title-logo-on').removeClass('tc-title-logo-off').trigger('resize');
      }
      else {
        $_header.addClass('tc-title-logo-off').removeClass('tc-title-logo-on').trigger('resize');
      }
    },
    tc_sticky_shrink_title_logo : function( to ) {
      if ( false !== to )
        $_header.addClass('tc-shrink-on').removeClass('tc-shrink-off').trigger('resize');
      else
        $_header.addClass('tc-shrink-off').removeClass('tc-shrink-on').trigger('resize');
    },
    tc_sticky_show_menu : function( to ) {
      if ( false !== to )
        $_header.addClass('tc-menu-on').removeClass('tc-menu-off').trigger('resize');
      else
        $_header.addClass('tc-menu-off').removeClass('tc-menu-on').trigger('resize');
    },
    tc_sticky_z_index : function( to ) {
      $('.tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header').css('z-index' , to);
    },
    tc_sticky_transparent_on_scroll : function( to ) {
      if ( false !== to ) {
        $_body.addClass('tc-transparent-on-scroll');
        $_body.removeClass('tc-solid-color-on-scroll');
      }
      else {
        $_body.removeClass('tc-transparent-on-scroll');
        $_body.addClass('tc-solid-color-on-scroll');
      }
    },
    tc_woocommerce_header_cart_sticky : function( to ) {
      if ( false !== to )
        $_header.addClass('tc-wccart-on').removeClass('tc-wccart-off').trigger('resize');
      else
        $_header.addClass('tc-wccart-off').removeClass('tc-wccart-on').trigger('resize');
    }
  } );//$.extend()



  /******************************************
  * SLIDER
  ******************************************/
  $.extend( _preview_cbs, {
    tc_slider_default_height : function( to ) {
      $('#customizr-slider').addClass('custom-slider-height');
      $('.carousel .item').css('line-height' , to + 'px').css('max-height', to + 'px').css('min-height', to + 'px').trigger('resize');
      $('.tc-slider-controls').css('line-height' , to + 'px').css('max-height', to + 'px').trigger('resize');
    }
  } );//$.extend()



  /******************************************
  * FEATURED PAGES
  ******************************************/
  $.extend( _preview_cbs, {
    tc_featured_text_one : function( to ) {
      $( '.widget-front p.fp-text-one' ).html( to );
    },
    tc_featured_text_two : function( to ) {
      $( '.widget-front p.fp-text-two' ).html( to );
    },
    tc_featured_text_three : function( to ) {
      $( '.widget-front p.fp-text-three' ).html( to );
    },
    tc_featured_page_button_text : function( to ) {
      if ( to )
          $( '.fp-button' ).html( to ).removeClass( 'hidden');
      else
          $( '.fp-button' ).addClass( 'hidden' );
    }
  });//$.extend()



  /******************************************
  * POST METAS
  ******************************************/
  var _post_metas_context = [
    { _context : 'home', _container : '.home' },
    { _context : 'single_post', _container: '.single'},
    { _context : 'post_lists', _container: 'body:not(.single, .home)'}
  ];

  $.extend( _preview_cbs, {
    tc_show_post_metas : function( to ) {
            var $_entry_meta = $('.entry-header .entry-meta', '.article-container');
      if ( false === to )
        $_entry_meta.hide('slow');
            else if (! $_body.hasClass('hide-post-metas') ){
        $_entry_meta.show('fast');
                $_body.removeClass('hide-all-post-metas');
            }
    },
    tc_post_metas_update_notice_text : function( to ) {
      $( '.tc-update-notice' ).html( to );
    },
    tc_post_metas_update_notice_format : function( to ) {
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
    }
  } );//$.extend()


  //add callbacks dynamically
  $.each( _post_metas_context, function() {
    var $_post_metas = $('.entry-header .entry-meta', this._container + ' .article-container' );

    if ( false === $_post_metas.length > 0 )
      return;

    _preview_cbs['tc_show_post_metas_' + this._context] = function( to ) {
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



  /******************************************
  * POST NAVIGATION
  ******************************************/
  $.extend( _preview_cbs, {
    tc_show_post_navigation : function( to ) {
      var $_post_nav = $( '#nav-below' );  
      if ( false === to )
        $_post_nav.hide('slow');
            else if ( ! $_post_nav.hasClass('hide-post-navigation') )
        $_post_nav.removeClass('hide-all-post-navigation').show('fast');
    }
  } );//$.extend()

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

    _preview_cbs[ 'tc_show_post_navigation_' + this._context ] = function( to ) {
      if ( false === to )
        $_post_nav.hide('slow').addClass('hide-post-navigation');
      else
        $_post_nav.show('fast').removeClass('hide-post-navigation');
    };//fn
    return false;
  });



  /******************************************
  * POST THUMBNAILS
  ******************************************/
  $.extend( _preview_cbs, {
    tc_post_list_thumb_height : function( to ) {
      $('.tc-rectangular-thumb').css('max-height' , to + 'px');
      if ( 0 !== $('.tc-rectangular-thumb').find('img').length )
        $('.tc-rectangular-thumb').find('img').trigger('refresh-height');//listened by the jsimgcentering $ plugin
    },
    tc_single_post_thumb_height : function( to ) {
      $('.tc-rectangular-thumb').css('height' , to + 'px').css('max-height' , to + 'px').trigger('refresh-height');
    }
  } );



  /******************************************
  * SOCIALS
  ******************************************/
  $.extend( _preview_cbs, {
    tc_social_in_header : function( to ) {
      if ( false === to ) {
        $('.tc-header .social-block').hide('slow');
        $(window).trigger('resize');
      }
      else {
        $('.tc-header .social-block').show('fast');
        $(window).trigger('resize');
      }
    },
    tc_social_in_footer : function( to ) {
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
    tc_social_in_sidebar_title : function( to ) {
      $( '.social-block .widget-title' , '.tc-sidebar' ).html( to );
      if ( ! to )
        $('.social-block' , '.tc-sidebar').hide('slow');
      else
        $('.social-block' , '.tc-sidebar').show('fast');
    }
  } );//$.extend()



  /******************************************
  * GRID
  ******************************************/
  $.extend( _preview_cbs, {
    tc_grid_shadow : function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-grid-shadow');
      else
        $('.article-container').removeClass('tc-grid-shadow');
    },
    tc_grid_bottom_border : function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-grid-border');
      else
        $('.article-container').removeClass('tc-grid-border');
    },
    tc_grid_icons : function( to ) {
      if ( false === to )
        $('.tc-grid-icon').each( function() { $(this).fadeOut(); } );
      else
        $('.tc-grid-icon').each( function() { $(this).fadeIn(); } );
    }
  } );//$.extend()


  /******************************************
  * GALLERY
  ******************************************/
  $.extend( _preview_cbs, {
    tc_gallery_style : function( to ) {
      if ( false !== to )
        $('.article-container').addClass('tc-gallery-style');
      else
        $('.article-container').removeClass('tc-gallery-style');
    }
  } );



   /******************************************
  * COMMENTS
  ******************************************/
  $.extend( _preview_cbs, {
    tc_comment_bubble_color : function( to ) {
      $('#custom-bubble-color').remove();
      var $style_element  = $('<style>' , { id : 'custom-bubble-color'}),
        bubble_live_css = '';

      //custom bubble
      bubble_live_css += '.comments-link .tc-comment-bubble {border-color:' + to + ';color:' + to + '}';
      bubble_live_css += '.comments-link .tc-comment-bubble:before {border-color:' + to + '}';
      $('head').append($style_element.html(bubble_live_css));
    }
  } );//$.extend()

  /******************************************
  * FOOTER
  ******************************************/
  $.extend( _preview_cbs, {
    tc_sticky_footer : function( to ) {
      if ( false !== to )
        $_body.addClass('tc-sticky-footer').trigger('refresh-sticky-footer');
      else
        $_body.removeClass('tc-sticky-footer');
    }
  } );//$.extend()



  /******************************************
  * CUSTOM CSS
  ******************************************/
  $.extend( _preview_cbs, {
    tc_custom_css : function( to ) {
      $('#option-custom-css').remove();
      var $style_element = ( 0 === $('#live-custom-css').length ) ? $('<style>' , { id : 'live-custom-css'}) : $('#live-custom-css');
      //sanitize string => remove html tags
      to = to.replace(/(<([^>]+)>)/ig,"");

      if (  0 === $('#live-custom-css').length )
        $('head').append($style_element.html(to));
      else
        $style_element.html(to);
    }
  } );//$.extend()



  /******************************************
  * HELPERS
  ******************************************/
  /*
  * @return string
  * simple helper to build the setting id name if not a builtin wp setting id
  */
  var _build_setId = function ( name ) {
    //is wp built in ?
    if ( _.contains(_wp_sets, name ) )
      return name;
    //else
    return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
  };


  //EXT LINKS HELPERS
  var _url_comp     = (location.host).split('.'),
      _nakedDomain  = new RegExp( _url_comp[1] + "." + _url_comp[2] );

  /*
  * @return boolean
  */
  var _is_external = function( _href  ) {
    //gets main domain and extension, no matter if it is a n level sub domain
    //works also with localhost or numeric urls
    var _thisHref = $.trim( _href ),
        _main_domain = (location.host).split('.').slice(-2).join('.'),
        _reg = new RegExp( _main_domain );

    if ( _thisHref !== '' && _thisHref != '#' && _isValidURL( _thisHref ) )
      return ! _reg.test( _thisHref );
    return;
  };

  /*
  * @return boolean
  */
  var _isValidURL = function(_url){
    var _pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return _pattern.test( _url );
  };


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
  };
} )( jQuery, _ );
