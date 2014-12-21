/* !
 * Customizr WordPress theme Javascript code
 * Copyright (c) 2014 Nicolas GUILLAUME (@nicguillaume), Themes & Co.
 * GPL2+ Licensed
*/
//ON DOM READY
jQuery(function ($) {
    //fancybox with localized script variables
    var b = TCParams.FancyBoxState,
        c = TCParams.FancyBoxAutoscale;
    if ( 1 == b ) {
            $("a.grouped_elements").fancybox({
            transitionOut: "elastic",
            transitionIn: "elastic",
            speedIn: 200,
            speedOut: 200,
            overlayShow: !1,
            autoScale: 1 == c ? "true" : "false",
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


    //Slider with localized script variables
    var d = TCParams.SliderName,
        e = TCParams.SliderDelay;
        j = TCParams.SliderHover;

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

    //Smooth scroll but not on bootstrap buttons. Checks if php localized option is active first.
    var SmoothScroll = TCParams.SmoothScroll;

    if ('easeOutExpo' == SmoothScroll) {
        $('a[href^="#"]', '#content').not('[class*=edd], .tc-carousel-control, .carousel-control, [data-toggle="modal"], [data-toggle="dropdown"], [data-toggle="tooltip"], [data-toggle="popover"], [data-toggle="collapse"], [data-toggle="tab"]').click(function () {
            var anchor_id = $(this).attr("href");
            if ('#' != anchor_id) {
                $('html, body').animate({
                    scrollTop: $(anchor_id).offset().top
                }, 700, SmoothScroll);
            }
            return false;
        });
    }

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
    //@uses TCParams.timerOnScrollAllBrowsers : boolean set to true by default
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
      if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
          btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      } else if ( $('body').hasClass('ie') ) {
           btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      }
    });//end of window.scroll()



    //Detects browser with CSS
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


    //handle some dynamic hover effects
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

    $("article.attachment img").delay(500).animate({
            opacity: 1
        }, 700, function () {}
    );

    //Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
    if ( TCParams.HasComments ) {
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


    //Detect layout and reorder content divs
    var LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
        RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar',
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
    if ( 1 == TCParams.ReorderBlocks ) {
        //trigger the block positioning only when responsive
        WindowWidth = $(window).width();
        if ( WindowWidth <= 767 - 15 && ! reordered ) {
            BlockPositions();
        }

        $(window).resize(function () {
            setTimeout(BlockPositions, 200);
        });
    }


    function centerImageInContainer(container , images){
        if ( ! $(images).length  )
            return;

        $(images).each(function () {
            var container_width    = $(this).closest(container).width(),
                container_height    = $(container).height(),
                // this will let us know the real img height
                ratio = container_width / $(this).attr("width"),
                real_img_height = ratio * $(this).attr("height");

            // if our image has an height smaller than the container
            // stretch it (h & w) proportionally to reach the container height
            // and center it horizontally
            if ( real_img_height < container_height ){
                // set the image height as the container height
                $(this).css("height", container_height);
                // this will let us know the new image width
                var img_ratio = container_height / real_img_height;
                var new_img_width = img_ratio * container_width;
                // set it
                $(this).css("width", new_img_width).css("max-width", new_img_width);

                // center it horizontally
                var pos_left = ( (container_width - new_img_width ) / 2 );
                $(this).css("left", pos_left);

                // reset v-center flag and margin-top
                if ( $(this).hasClass("v-center") ){
                    $(this).removeClass("v-center")
                    .css("top", "0px");
                }

                // add h-center class flag
                $(this).addClass("h-center");

            } else { // center it vertically
                    // this covers also the case real_img_height == container_height
                    // a differentiation here looks like pratically useless

                // reset margin-left, width, height and h-center flag
                if ( $(this).hasClass("h-center") ){
                    $(this).css("width", "100%")
                    .css("max-width", "100%")
                    .css("left", "0px")
                    .css("height", "auto")
                    .removeClass("h-center");
                }
                // center it vertically
                var pos_top = ( container_height - real_img_height ) / 2 ;
                $(this).css("top", pos_top);
                // add v-center class flag
                $(this).addClass("v-center");
            }// end if-else
        });// end imgs each function

    }// end centerImageInContainer

     //Enable slides centering if option is checked in the customizer.
    if ( 1 == TCParams.CenterSlides ) {
        //adds a specific class to the carousel when automatic centering is enabled
        $('#customizr-slider .carousel-inner').addClass('center-slides-enabled');

        setTimeout( function() {
            centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image > img' );
            $('.tc-slider-loader-wrapper').hide();
        } , 50);

        $(window).resize(function(){
            setTimeout( function() {
                centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image > img' );
            }, 50);
        });
    }//end of center slides

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

    //CENTER RECTANGULAR THUMBNAILS FOR POST LIST AND SINGLE POST VIEWS
    //on load
    setTimeout( function() {
        centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    }, 300 );
    //on resize
    $(window).resize(function(){
            centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    });
    //bind 'refresh-height' event (triggered from the customizer)
    $('.tc-rectangular-thumb').on('refresh-height' , function(){
        centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    });

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

    //Handle dropdown on click for multi-tier menus
    var $dropdown_ahrefs    = $('.tc-open-on-click .menu-item.menu-item-has-children > a'),
        $dropdown_submenus  = $('.tc-open-on-click .dropdown .dropdown-submenu');


    // go to the link if submenu is already opened
    $dropdown_ahrefs.not('a' , $dropdown_submenus).on('tap click', function(evt) {
        var href = $(this).attr('href');
        if ( '#' != href && '' !== href )
          return;
        if ( $(this).next('.dropdown-menu').is(':visible') )
          window.location = href;
    });
    // make sub-submenus dropdown on click work
    $dropdown_submenus.each(function(){
        var $parent = $(this),
            $children = $parent.children('[data-toggle="dropdown"]');
        $children.on('tap click', function(){
            var submenu   = $(this).next('.dropdown-menu'),
                openthis  = false,
                href      = $(this).attr('href');
            if ( ! $parent.hasClass('open') ) {
              openthis = true;
            } else {
              if ( '#' != href && '' !== href )
                window.location = href;
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
    var    $tcHeader        = $('.tc-header'),
            elToHide        = [], //[ '.social-block' , '.site-description' ],
            isUserLogged    = $('body').hasClass('logged-in') || 0 !== $('#wpadminbar').length,
            isCustomizing   = $('body').hasClass('is-customizing'),
            customOffset    = +TCParams.stickyCustomOffset;

    function _is_scrolling() {
        return $('body').hasClass('sticky-enabled') ? true : false;
    }

    function _is_sticky_enabled() {
        return $('body').hasClass('tc-sticky-header') ? true : false;
    }

    function _get_initial_offset() {
        //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
        var initialOffset   = 0;
        if ( 1 == isUserLogged && ! isCustomizing ) {
            if ( 580 < $(window).width() )
                initialOffset = $('#wpadminbar').height();
            else
                initialOffset = ! _is_scrolling() ? $('#wpadminbar').height() : 0;
        }
        return initialOffset + customOffset;
    }

    function _set_sticky_offsets() {
        if ( ! _is_sticky_enabled() )
            return;

        //Reset all values first
        $tcHeader.css('top' , '');
        $('.tc-header').css('height' , 'auto' );
        $('#tc-reset-margin-top').css('margin-top' , '' ).show();

        //What is the initial offset of the header ?
        var headerHeight    = $tcHeader.height();
        //set initial margin-top = initial offset + header's height
        $('#tc-reset-margin-top').css('margin-top' , ( +headerHeight + customOffset ) + 10 + 'px' ); //10 = header bottom border
    }


    function _set_header_top_offset() {
        //set header initial offset
        $tcHeader.css('top' , _get_initial_offset() + 'px');
    }

    function _set_no_title_logo_class() {
        if ( ! $('body').hasClass('sticky-enabled') ) {
            $('.navbar-wrapper').addClass('span9').removeClass('span12');
        } else {
            $('.tc-title-logo-off .navbar-wrapper' , '.sticky-enabled').addClass('span12').removeClass('span9');
            $('.tc-title-logo-on .navbar-wrapper' , '.sticky-enabled').addClass('span9').removeClass('span12');
        }
    }

    //set site logo width and height if exists
    //=> allow the CSS3 transition to be enabled
    if ( _is_sticky_enabled() && 0 !== $('img' , '.site-logo').length ) {
        $.each($('img', '.site-logo'), function(){
            var logoWidth   = $(this).attr('width'),
                logoHeight  = $(this).attr('height');
            $(this).css('height' , logoHeight +'px' ).css('width' , logoWidth +'px' );
        });
    }

    //LOADING ACTIONS
    if ( _is_sticky_enabled() )
        setTimeout( function() { _refresh(); } , 20 );
    if ( _is_sticky_enabled() && ! $('body').hasClass('sticky-enabled') )
        $('body').addClass("sticky-disabled");

    //RESIZING ACTIONS
    $(window).resize(function() {
        if ( ! _is_sticky_enabled() )
            return;
        _set_sticky_offsets();
        _set_header_top_offset();
        _set_no_title_logo_class();
    });

    function _refresh() {
        setTimeout( function() {
            _set_sticky_offsets();
            _set_header_top_offset();
            _set_no_title_logo_class();
        } , 20 );
        $(window).trigger('resize');
    }

    //SCROLLING ACTIONS
    var timer,
        increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer

    //var windowHeight = $(window).height();
    var triggerHeight = 20; //0.5 * windowHeight;

    function _scrolling_actions() {
        _set_header_top_offset();
        _set_no_title_logo_class();
        //process scrolling actions
        if ( $(window).scrollTop() > triggerHeight ) {
            $('body').addClass("sticky-enabled").removeClass("sticky-disabled");
        }
        else {
            $('body').removeClass("sticky-enabled").addClass("sticky-disabled");
            setTimeout( function() { _refresh();} ,
                $('body').hasClass('is-customizing') ? 100 : 20
           );
        }
    }

    $(window).scroll(function() {
        if ( ! _is_sticky_enabled() )
            return;
        //use a timer
        if ( timer) {
            increment++;
            window.clearTimeout(timer);
         }

         if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
            timer = window.setTimeout(function() {
                _scrolling_actions();
             }, increment > 5 ? 50 : 0 );
         } else if ( $('body').hasClass('ie') ) {
             timer = window.setTimeout(function() {
                _scrolling_actions();
             }, increment > 5 ? 50 : 0 );
        }
    });//end of window.scroll()
});
