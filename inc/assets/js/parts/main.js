/* !
 * Customizr WordPress theme Javascript code
 * Copyright (c) 2014-2015 Nicolas GUILLAUME (@nicguillaume), Press Customizr.
 * GPL2+ Licensed
*/
//ON DOM READY
jQuery(function ($) {
    var _p = TCParams;

    //helper to trigger a simple load
    //=> allow centering when smart load not triggered by smartload
    var _trigger_simple_load = function( $_imgs ) {
      if ( 0 === $_imgs.length )
        return;

      $_imgs.map( function( _ind, _img ) {
        $(_img).load( function () {
            $(_img).trigger('simple_load');
        });//end load
        if ( $(_img)[0] && $(_img)[0].complete )
          $(_img).load();
      } );//end map
    };//end of fn


    //CENTER VARIOUS IMAGES
    setTimeout( function() {
      //Featured Pages
      $('.widget-front .thumb-wrapper').centerImages( {
        enableCentering : 1 == _p.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        zeroTopAdjust : 1,
        leftAdjust : 2.5,
        oncustom : ['smartload', 'simple_load']
      });
      //POST LIST THUMBNAILS + FEATURED PAGES
      //Squared, rounded
      $('.thumb-wrapper', '.hentry' ).centerImages( {
        enableCentering : 1 == _p.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'simple_load']
      });

      //rectangulars
      $('.tc-rectangular-thumb').centerImages( {
        enableCentering : 1 == _p.centerAllImg,
        enableGoldenRatio : true,
        goldenRatioVal : _p.goldenRatio || 1.618,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //SINGLE POST THUMBNAILS
      $('.tc-rectangular-thumb' , '.single').centerImages( {
        enableCentering : 1 == _p.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //POST GRID IMAGES
      $('.tc-grid-figure').centerImages( {
        enableCentering : 1 == _p.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : true,
        goldenRatioVal : _p.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : _p.gridGoldenRatioLimit || 350
      } );
    }, 300 );


    //SLIDER
    //Slider with localized script variables
    var d = _p.SliderName,
        e = _p.SliderDelay;
        j = _p.SliderHover;

    if (0 !== d.length) {
        if (0 !== e.length && !j) {
            $("#customizr-slider").carousel({
                interval: e,
                pause: "false"
            });
        } else if (0 !== e.length) {
            $("#customizr-slider").carousel({
                interval: e
            });
        } else {
            $("#customizr-slider").carousel();
        }
    }

    //add a class to the slider on hover => used to display the navigation arrow
    $(".carousel").hover( function() {
            $(this).addClass('tc-slid-hover');
        },
        function() {
            $(this).removeClass('tc-slid-hover');
        }
    );

    //SLIDER ARROWS
    function _center_slider_arrows() {
        if ( 0 === $('.carousel').length )
            return;
        $('.carousel').each( function() {
            var _slider_height = $( '.carousel-inner' , $(this) ).height();
            $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
        });
    }
    //Recenter the slider arrows
    $(window).resize(function(){
        _center_slider_arrows();
    });
    _center_slider_arrows();


    //Slider swipe support with hammer.js
    if ( 'function' == typeof($.fn.hammer) ) {

        //prevent propagation event from sensible children
        $(".carousel input, .carousel button, .carousel textarea, .carousel select, .carousel a").
            on("touchstart touchmove", function(ev) {
                ev.stopPropagation();
        });

        $('.carousel' ).each( function() {
            $(this).hammer().on('swipeleft tap', function() {
                $(this).carousel('next');
            });
            $(this).hammer().on('swiperight', function(){
                $(this).carousel('prev');
            });
        });
    }

    //SLIDER IMG + VARIOUS
    setTimeout( function() {
      $( '.carousel .carousel-inner').centerImages( {
        enableCentering : 1 == _p.centerSliderImg,
        imgSel : '.item .carousel-image img',
        oncustom : ['slid', 'simple_load'],
        defaultCSSVal : { width : '100%' , height : 'auto' },
        useImgAttr : true
      } );
      $('.tc-slider-loader-wrapper').hide();
    } , 50);

    _trigger_simple_load( $( '.carousel .carousel-inner').find('img') );




    //IMG SMART LOAD
    //.article-container covers all post / page content : single and list
    //__before_main_wrapper covers the single post thumbnail case
    //.widget-front handles the featured pages
    if ( 1 == _p.imgSmartLoadEnabled )
      $( '.article-container, .__before_main_wrapper, .widget-front' ).imgSmartLoad( _.size( _p.imgSmartLoadOpts ) > 0 ? _p.imgSmartLoadOpts : {} );
    else {
      //if smart load not enabled => trigger the load event on img load
      var $_to_center = $( '.article-container, .__before_main_wrapper, .widget-front' ).find('img');
      _trigger_simple_load($_to_center);
    }//end else




    //DROP CAPS
    if ( _p.dropcapEnabled && 'object' == typeof( _p.dropcapWhere ) ) {
      $.each( _p.dropcapWhere , function( ind, val ) {
        if ( 1 == val ) {
          $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
            minwords : _p.dropcapMinWords,//@todo check if number
            skipSelectors : _.isObject(_p.dropcapSkipSelectors) ? _p.dropcapSkipSelectors : {}
          });
        }
      });
    }



    //EXT LINKS
    //May be add (check if activated by user) external class + target="_blank" to relevant links
    //images are excluded by default
    //links inside post/page content
    if ( _p.extLinksStyle || _p.extLinksTargetExt ) {
      $('a' , '.entry-content').extLinks({
        addIcon : _p.extLinksStyle,
        newTab : _p.extLinksTargetExt,
        skipSelectors : _.isObject(_p.extLinksSkipSelectors) ? _p.extLinksSkipSelectors : {}
      });
    }


    //FANCYBOX
    //Fancybox with localized script variables
    if ( 1 == _p.FancyBoxState && 'function' === typeof($.fn.fancybox) ) {
      $("a.grouped_elements").fancybox({
        transitionOut: "elastic",
        transitionIn: "elastic",
        speedIn: 200,
        speedOut: 200,
        overlayShow: !1,
        autoScale: 1 == _p.FancyBoxAutoscale ? "true" : "false",
        changeFade: "fast",
        enableEscapeButton: !0
      });
      //replace title by img alt field
      $('a[rel*=tc-fancybox-group]').each( function() {
        var title = $(this).find('img').prop('title');
        var alt = $(this).find('img').prop('alt');
        if (typeof title !== 'undefined' && 0 !== title.length) {
            $(this).attr('title',title);
        }
        else if (typeof alt !== 'undefined' &&  0 !== alt.length) {
            $(this).attr('title',alt);
        }
      });
    }



    //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
    var _maybe_apply_smooth_scroll = function() {
      if ( ! _p.SmoothScroll || 'easeOutExpo' != _p.SmoothScroll )
        return;

      var _excl_sels = ( _p.SmoothScrollExclude && _.isArray( _p.SmoothScrollExclude ) ) ? _p.SmoothScrollExclude.join(',') : '';
      $('a[href^="#"]', '#content').not( _excl_sels ).click(function () {
          var anchor_id = $(this).attr("href");

          //anchor el exists ?
          if ( ! $(anchor_id).length )
            return;

          if ('#' != anchor_id) {
              $('html, body').animate({
                  scrollTop: $(anchor_id).offset().top
              }, 700, _p.SmoothScroll);
          }
          return false;
      });//end click
    };
    //Fire smooth scroll
    _maybe_apply_smooth_scroll();



    //BACK TO TOP
    function g($) {
        return ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
    }
    //Stop the viewport animation if user interaction is detected
    var f = $("html, body");
    $(".back-to-top, .tc-btt-wrapper, .btt-arrow").on("click touchstart touchend", function ($) {
            f.on("scroll mousedown DOMMouseScroll mousewheel keyup", g);
            f.animate({
                scrollTop: 0
            }, 1e3, function () {
                f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
                //$(window).trigger('resize');
            });
            $.preventDefault();
    });


    //DISPLAY BACK TO TOP BUTTON ON SCROLL
    function btt_scrolling_actions() {
      if ( $(window).scrollTop() > 100 )
        $('.tc-btt-wrapper').addClass('show');
      else
        $('.tc-btt-wrapper').removeClass('show');
    }
    //use of a timer instead of attaching handler directly to the window scroll event
    //@uses _p.timerOnScrollAllBrowsers : boolean set to true by default
    //http://ejohn.org/blog/learning-from-twitter/
    //https://dannyvankooten.com/delay-scroll-handlers-javascript/
    var btt_timer,
        btt_increment = 1,//used to wait a little bit after the first user scroll actions to trigger the timer
        btt_triggerHeight = 20; //0.5 * windowHeight;

    $(window).scroll(function() {
      if ( btt_timer) {
          btt_increment++;
          window.clearTimeout(btt_timer);
      }
      if ( 1 == _p.timerOnScrollAllBrowsers ) {
          btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      } else if ( $('body').hasClass('ie') ) {
           btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      }
    });//end of window.scroll()



    //BROWSER DETECTION
    // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
    if ( $.browser.chrome )
        $("body").addClass("chrome");
    else if ( $.browser.webkit )
        $("body").addClass("safari");
    else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
        $("body").addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));

    //Adds version if browser = ie
    if ( $("body").hasClass("ie") )
        $("body").addClass($.browser.version);


    //VARIOUS HOVER ACTION
    $(".widget-front, article").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    });

    $(".widget li").hover(function () {
        $(this).addClass("on");
    }, function () {
        $(this).removeClass("on");
    });



    //ATTACHMENT FADE EFFECT
    $("article.attachment img").delay(500).animate({
            opacity: 1
        }, 700, function () {}
    );



    //COMMENTS
    //Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
    if ( _p.HasComments ) {
       //edit
       $('cite p.edit-link').each(function() {
            $(this).removeClass('btn btn-success btn-mini');
       });
       $('cite p.edit-link > a').each(function() {
            $(this).addClass('btn btn-success btn-mini');
       });
       //reply
       $('.comment .reply').each(function() {
            $(this).removeClass('btn btn-small');
       });
       $('.comment .reply .comment-reply-link').each(function() {
            $(this).addClass('btn btn-small');
       });
    }


    //DYNAMIC REORDERING
    //Detect layout and reorder content divs
    var LeftSidebarClass    = _p.LeftSidebarClass || '.span3.left.tc-sidebar',
        RightSidebarClass   = _p.RightSidebarClass || '.span3.right.tc-sidebar',
        wrapper             = $('#main-wrapper .container[role=main] > .column-content-wrapper'),
        content             = $("#main-wrapper .container .article-container"),
        left                = $("#main-wrapper .container " + LeftSidebarClass),
        right               = $("#main-wrapper .container " + RightSidebarClass),
        reordered           = false;

    function BlockPositions() {
        //15 pixels adjustement to avoid replacement before real responsive width
        WindowWidth = $(window).width();
        if ( WindowWidth > 767 - 15 && reordered ) {
            //$(window).width();
            if ( $(left).length ) {
                $(left).detach();
                $(content).detach();
                $(wrapper).append($(left)).append($(content));
            }
            if ( $(right).length ) {
                $(right).detach();
                $(wrapper).append($(right));
            }
            reordered = false; //this could stay in both if blocks instead
        } else if ( ( WindowWidth <= 767 - 15 ) && ! reordered ) {
            if ( $(left).length ) {
                 $(left).detach();
                $(content).detach();
                $(wrapper).append($(content)).append( $(left) );
            }
            if ( $(right).length ) {
                $(right).detach();
                $(wrapper).append($(right));
            }
            reordered = true; //this could stay in both if blocks instead
        }
    }//end function
    //Enable reordering if option is checked in the customizer.
    if ( 1 == _p.ReorderBlocks ) {
        //trigger the block positioning only when responsive
        WindowWidth = $(window).width();
        if ( WindowWidth <= 767 - 15 && ! reordered ) {
            BlockPositions();
        }

        $(window).resize(function () {
            setTimeout(BlockPositions, 200);
        });
    }




    //Handle dropdown on click for multi-tier menus
    var $dropdown_ahrefs    = $('.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'),
        $dropdown_submenus  = $('.tc-open-on-click .dropdown .dropdown-submenu');


    // go to the link if submenu is already opened
    $dropdown_ahrefs.on('tap click', function(evt) {
        if ( ( $(this).next('.dropdown-menu').css('visibility') != 'hidden' &&
                $(this).next('.dropdown-menu').is(':visible')  &&
                ! $(this).parent().hasClass('dropdown-submenu') ) ||
             ( $(this).next('.dropdown-menu').is(':visible') &&
                $(this).parent().hasClass('dropdown-submenu') ) )
            window.location = $(this).attr('href');
    });
    // make sub-submenus dropdown on click work
    $dropdown_submenus.each(function(){
        var $parent = $(this),
            $children = $parent.children('[data-toggle="dropdown"]');
        $children.on('tap click', function(){
            var submenu   = $(this).next('.dropdown-menu'),
                openthis  = false;
            if ( ! $parent.hasClass('open') ) {
              openthis = true;
            }
            // close opened submenus
            $($parent.parent()).children('.dropdown-submenu').each(function(){
                $(this).removeClass('open');
            });
            if ( openthis )
                $parent.addClass('open');

            return false;
        });
    });
});



/* Sticky header since v3.2.0 */
jQuery(function ($) {
    var   _p              = TCParams,
          $tcHeader       = $('.tc-header'),
          elToHide        = [], //[ '.social-block' , '.site-description' ],
          $_window        = $(window),
          $_body          = $('body'),
          $wpadminbar     = $('#wpadminbar'),
          isUserLogged    = $_body.hasClass('logged-in') || 0 !== $wpadminbar.length,
          isCustomizing   = $_body.hasClass('is-customizing'),
          customOffset    = +_p.stickyCustomOffset,
          $sticky_logo    = $('img.sticky', '.site-logo'),
          $resetMarginTop = $('#tc-reset-margin-top'),
          logo            = 0 === $sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;

    function _is_scrolling() {
        return $_body.hasClass('sticky-enabled') ? true : false;
    }

    function _is_sticky_enabled() {
        return $_body.hasClass('tc-sticky-header') ? true : false;
    }

    function _get_initial_offset() {
        //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
        var initialOffset   = 0;
        if ( 1 == isUserLogged && ! isCustomizing ) {
            if ( 580 < $_window.width() )
                initialOffset = $wpadminbar.height();
            else
                initialOffset = ! _is_scrolling() ? $wpadminbar.height() : 0;
        }
        return initialOffset + customOffset;
    }

    function _set_sticky_offsets() {
        if ( ! _is_sticky_enabled() )
            return;

        //Reset all values first
        $tcHeader.css('top' , '');
        $tcHeader.css('height' , 'auto' );
        $resetMarginTop.css('margin-top' , '' ).show();

        //What is the initial offset of the header ?
        var headerHeight    = $tcHeader.outerHeight(true); /* include borders and eventual margins (true param)*/
        //set initial margin-top = initial offset + header's height
        $resetMarginTop.css('margin-top' , ( +headerHeight + customOffset ) + 'px');
    }


    function _set_header_top_offset() {
        //set header initial offset
        $tcHeader.css('top' , _get_initial_offset() );
    }


    function _set_logo_height(){
        if ( logo && 0 === logo._logo.length || ! logo._ratio )
            return;

        logo._logo.css('height' , logo._logo.width() / logo._ratio );

        setTimeout( function() {
            _set_sticky_offsets();
            _set_header_top_offset();
        } , 200 );
    }


    //set site logo width and height if exists
    //=> allow the CSS3 transition to be enabled
    if ( _is_sticky_enabled() && logo && 0 !== logo._logo.length ) {
        var logoW = logo._logo.attr('width'),
            logoH = logo._logo.attr('height');

        //check that all numbers are valid before using division
        if ( 0 === _.size( _.filter( [ logoW, logoH ], function(num){ return _.isNumber( parseInt(num, 10) ) && 0 !== num; } ) ) )
          return;

        logo._ratio  = logoW / logoH;
        logo._logo.css('height' , logoH  ).css('width' , logoW );
    }

    //LOADING ACTIONS
    if ( _is_sticky_enabled() )
        setTimeout( function() { _sticky_refresh(); _sticky_header_scrolling_actions(); } , 20 );

    //RESIZING ACTIONS
    $_window.resize(function() {
        if ( ! _is_sticky_enabled() )
            return;
        _set_sticky_offsets();
        _set_header_top_offset();
        _set_logo_height();
    });

    function _sticky_refresh() {
        setTimeout( function() {
            _set_sticky_offsets();
            _set_header_top_offset();
        } , 20 );
        $_window.trigger('resize');
    }

    //SCROLLING ACTIONS
    var timer,
        increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer

    //var windowHeight = $(window).height();
    var triggerHeight = 20; //0.5 * windowHeight;

    function _sticky_header_scrolling_actions() {
        _set_header_top_offset();
        //process scrolling actions
        if ( $_window.scrollTop() > triggerHeight ) {
            if ( ! _is_scrolling() )
                $_body.addClass("sticky-enabled").removeClass("sticky-disabled");
        }
        else if ( _is_scrolling() ){
            $_body.removeClass("sticky-enabled").addClass("sticky-disabled");
            setTimeout( function() { _sticky_refresh(); } ,
              isCustomizing ? 100 : 20
            );
            //additional refresh for some edge cases like big logos
            setTimeout( function() { _sticky_refresh(); } , 200 );
        }
    }//end of fn

    $_window.scroll(function() {
        if ( ! _is_sticky_enabled() )
            return;
        //use a timer
        if ( timer) {
            increment++;
            window.clearTimeout(timer);
         }

         if ( 1 == _p.timerOnScrollAllBrowsers ) {
            timer = window.setTimeout(function() {
                _sticky_header_scrolling_actions();
             }, increment > 5 ? 50 : 0 );
         } else if ( $_body.hasClass('ie') ) {
             timer = window.setTimeout(function() {
                _sticky_header_scrolling_actions();
             }, increment > 5 ? 50 : 0 );
        }
    });//end of window.scroll()
});
