//@global TCParams
var czrapp = czrapp || {};

(function($, czrapp) {
  //short name for the slice method from the built-in Array js prototype
  //used to handle the event methods
  var slice = Array.prototype.slice;

  $.extend( czrapp, {
    instances        : {},//will store all subclasses instances
    methods          : {},//will store all subclasses methods

    //parent class constructor
    Base : function() {},

    _inherits : function( classname ) {
      //add the class to the czrapp and sets the parent this to it
      czrapp[classname] = function() {
        czrapp.Base.call(this);
      };

      //set the classical prototype chaining with inheritance
      czrapp[classname].prototype = Object.create( czrapp.Base.prototype );
      czrapp[classname].prototype.constructor = czrapp[classname];
      return czrapp;
    },


    _instanciates : function( classname) {
      czrapp.instances[classname] = czrapp.instances[classname] || new czrapp[classname]();
      return czrapp;
    },


    /**
     * [_init description]
     * @param  {[type]} classname string
     * @param  {[type]} methods   array of methods
     * @return {[type]} czrapp object
     */
    _init : function(classname, methods) {
      var _instance = czrapp.instances[classname] || false;
      if ( ! _instance )
        return;

      //always fire the init method if exists
      if ( _instance.init )
        _instance.init();

      //fire the array of methods on load
      _instance.emit(methods);

      //return czrapp for chaining
      return czrapp;
    },

    //extend a classname prototype with a set of methods
    _addMethods : function(classname) {
      $.extend( czrapp[classname].prototype , czrapp._getMethods(classname) );
      return czrapp;
    },

    _getMethods : function(classname) {
      return czrapp.methods[classname] || {};
    },


    /**
     * Cache properties on Dom Ready
     * @return {[type]} [description]
     */
    cacheProp : function() {
      $.extend( czrapp, {
        //cache various jQuery el in czrapp obj
        $_window         : $(window),
        $_html           : $('html'),
        $_body           : $('body'),
        $_tcHeader       : $('.tc-header'),
        $_wpadminbar     : $('#wpadminbar'),

        //various properties definition
        localized        : TCParams || {},
        reordered_blocks : false,//store the states of the sidebar layout
      });

      return czrapp;
    },


    /***************************************************************************
    * Event methods, offering the ability to bind to and trigger events.
    * Inspired from the customize-base.js event manager object
    * @uses slice method, alias of Array.prototype.slice
    ****************************************************************************/
    trigger: function( id ) {
      if ( this.topics && this.topics[ id ] )
        this.topics[ id ].fireWith( this, slice.call( arguments, 1 ) );
      return this;
    },

    bind: function( id ) {
      this.topics = this.topics || {};
      this.topics[ id ] = this.topics[ id ] || $.Callbacks();
      this.topics[ id ].add.apply( this.topics[ id ], slice.call( arguments, 1 ) );
      return this;
    },

    unbind: function( id ) {
      if ( this.topics && this.topics[ id ] )
        this.topics[ id ].remove.apply( this.topics[ id ], slice.call( arguments, 1 ) );
      return this;
    },


    /**
     * Load the various { constructor [methods] }
     *
     * Constructors and methods can be disabled by passing a localized var TCParams._disabled (with the hook 'tc_disabled_front_js_parts' )
     * Ex : add_filter('tc_disabled_front_js_parts', function() {
     *   return array('Czr_Plugins' => array() , 'Czr_Slider' => array('addSwipeSupport') );
     * });
     * => will disabled all Czr_Plugin class (with all its methods) + will disabled the addSwipeSupport method from the Czr_Slider class
     * @todo : check the classes dependencies and may be add a check if ( ! method_exits() )
     *
     * @param  {[type]} args [description]
     * @return {[type]}      [description]
     */
    loadCzr : function( args ) {
      var that = this,
          _disabled = that.localized._disabled || {};

      _.each( args, function( methods, key ) {
        //normalize methods into an array if string
        methods = 'string' == typeof(methods) ? [methods] : methods;

        //key is the constructor
        //check if the constructor has been disabled => empty array of methods
        if ( that.localized._disabled[key] && _.isEmpty(that.localized._disabled[key]) )
          return;

        if ( that.localized._disabled[key] && ! _.isEmpty(that.localized._disabled[key]) ) {
          var _to_remove = that.localized._disabled[key];
          _to_remove = 'string' == typeof(_to_remove) ? [_to_remove] : _to_remove;
          methods = _.difference( methods, _to_remove );
        }
        //chain various treatments
        czrapp._inherits(key)._instanciates(key)._addMethods(key)._init(key, methods);
      });//_.each()

      czrapp.trigger('czrapp-ready', this);
    }//loadCzr

  });//extend
})(jQuery, czrapp);



/*************************
* ADD BASE CLASS METHODS
*************************/
(function($, czrapp) {
  var _methods = {
    emit : function( cbs, args ) {
      cbs = _.isArray(cbs) ? cbs : [cbs];
      var self = this;
      _.map( cbs, function(cb) {
        if ( 'function' == typeof(self[cb]) ) {
          self[cb].apply(self, 'undefined' == typeof( args ) ? Array() : args );
          czrapp.trigger( cb, _.object( _.keys(args), args ) );
        }
      });//_.map
    },

    triggerSimpleLoad : function( $_imgs ) {
      if ( 0 === $_imgs.length )
        return;

      $_imgs.map( function( _ind, _img ) {
        $(_img).load( function () {
          $(_img).trigger('simple_load');
        });//end load
        if ( $(_img)[0] && $(_img)[0].complete )
          $(_img).load();
      } );//end map
    },//end of fn

    isUserLogged     : function() {
      return czrapp.$_body.hasClass('logged-in') || 0 !== czrapp.$_wpadminbar.length;
    },

    isCustomizing    : function() {
      return czrapp.$_body.hasClass('is-customizing');
    }

  };//_methods{}

  $.extend( czrapp.Base.prototype, _methods );//$.extend

})(jQuery, czrapp);
/***************************
* ADD BROWSER DETECT METHODS
****************************/
(function($, czrapp) {
  var _methods =  {
    init : function() {
      // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
      if ( $.browser.chrome )
          czrapp.$_body.addClass("chrome");
      else if ( $.browser.webkit )
          czrapp.$_body.addClass("safari");
      else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
          czrapp.$_body.addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));

      //Adds version if browser = ie
      if ( czrapp.$_body.hasClass("ie") )
          czrapp.$_body.addClass($.browser.version);
    }
  };//_methods{}

  $.extend( czrapp.methods.BrowserDetect = {} , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
/***************************
* ADD JQUERY PLUGINS METHODS
****************************/
(function($, czrapp) {
  var _methods = {
    centerImagesWithDelay : function( delay ) {
      var self = this;
      //fire the center images plugin
      setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
    },


    //IMG SMART LOAD
    //.article-container covers all post / page content : single and list
    //__before_main_wrapper covers the single post thumbnail case
    //.widget-front handles the featured pages
    imgSmartLoad : function() {
      if ( 1 == TCParams.imgSmartLoadEnabled )
        $( '.article-container, .__before_main_wrapper, .widget-front' ).imgSmartLoad( _.size( TCParams.imgSmartLoadOpts ) > 0 ? TCParams.imgSmartLoadOpts : {} );
      else {
        //if smart load not enabled => trigger the load event on img load
        var $_to_center = $( '.article-container, .__before_main_wrapper, .widget-front' ).find('img');
        this.triggerSimpleLoad($_to_center);
      }//end else
    },


    //FIRE DROP CAP PLUGIN
    dropCaps : function() {
      if ( ! TCParams.dropcapEnabled || ! _.isObject( TCParams.dropcapWhere ) )
        return;

      $.each( TCParams.dropcapWhere , function( ind, val ) {
        if ( 1 == val ) {
          $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
            minwords : TCParams.dropcapMinWords,//@todo check if number
            skipSelectors : _.isObject(TCParams.dropcapSkipSelectors) ? TCParams.dropcapSkipSelectors : {}
          });
        }
      });//each
    },


    //FIRE EXT LINKS PLUGIN
    //May be add (check if activated by user) external class + target="_blank" to relevant links
    //images are excluded by default
    //links inside post/page content
    extLinks : function() {
      if ( ! TCParams.extLinksStyle && ! TCParams.extLinksTargetExt )
        return;
      $('a' , '.entry-content').extLinks({
        addIcon : TCParams.extLinksStyle,
        newTab : TCParams.extLinksTargetExt,
        skipSelectors : _.isObject(TCParams.extLinksSkipSelectors) ? TCParams.extLinksSkipSelectors : {}
      });
    },

    //FIRE FANCYBOX PLUGIN
    //Fancybox with localized script variables
    fancyBox : function() {
      if ( 1 != TCParams.FancyBoxState || 'function' != typeof($.fn.fancybox) )
        return;

      $("a.grouped_elements").fancybox({
        transitionOut: "elastic",
        transitionIn: "elastic",
        speedIn: 200,
        speedOut: 200,
        overlayShow: !1,
        autoScale: 1 == TCParams.FancyBoxAutoscale ? "true" : "false",
        changeFade: "fast",
        enableEscapeButton: !0
      });

      //replace title by img alt field
      $('a[rel*=tc-fancybox-group]').each( function() {
        var title = $(this).find('img').prop('title');
        var alt = $(this).find('img').prop('alt');
        if (typeof title !== 'undefined' && 0 !== title.length)
          $(this).attr('title',title);
        else if (typeof alt !== 'undefined' &&  0 !== alt.length)
          $(this).attr('title',alt);
      });
    },


    /**
    * CENTER VARIOUS IMAGES
    * @return {void}
    */
    centerImages : function() {
      //SLIDER IMG + VARIOUS
      setTimeout( function() {
        $( '.carousel .carousel-inner').centerImages( {
          enableCentering : 1 == TCParams.centerSliderImg,
          imgSel : '.item .carousel-image img',
          oncustom : ['slid', 'simple_load'],
          defaultCSSVal : { width : '100%' , height : 'auto' },
          useImgAttr : true
        });
        $('.tc-slider-loader-wrapper').hide();
      } , 50);

      //Featured Pages
      $('.widget-front .thumb-wrapper').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        zeroTopAdjust : 1,
        leftAdjust : 2.5,
        oncustom : ['smartload', 'simple_load']
      });
      //POST LIST THUMBNAILS + FEATURED PAGES
      //Squared, rounded
      $('.thumb-wrapper', '.hentry' ).centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'simple_load']
      });

      //rectangulars
      $('.tc-rectangular-thumb').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : true,
        goldenRatioVal : TCParams.goldenRatio || 1.618,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //SINGLE POST THUMBNAILS
      $('.tc-rectangular-thumb' , '.single').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        enableGoldenRatio : false,
        disableGRUnder : 0,//<= don't disable golden ratio when responsive
        oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
      });

      //POST GRID IMAGES
      $('.tc-grid-figure').centerImages( {
        enableCentering : 1 == TCParams.centerAllImg,
        oncustom : ['smartload', 'simple_load'],
        enableGoldenRatio : true,
        goldenRatioVal : TCParams.goldenRatio || 1.618,
        goldenRatioLimitHeightTo : TCParams.gridGoldenRatioLimit || 350
      } );
    },//center_images

  };//_methods{}

  $.extend( czrapp.methods.Czr_Plugins = {} , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
  var _methods = {

    //INIT
    init : function() {
      var self = this;
      //@todo EVENT
      //Recenter the slider arrows on resize
      czrapp.$_window.resize( function(){
        self.centerSliderArrows();
      });
    },



    fireSliders : function(name, delay, hover) {
      //Slider with localized script variables
      var _name   = name || TCParams.SliderName,
          _delay  = delay || TCParams.SliderDelay;
          _hover  = hover || TCParams.SliderHover;

      if ( 0 === _name.length )
        return;

      if ( 0 !== _delay.length && ! _hover ) {
        $("#customizr-slider").carousel({
            interval: _delay,
            pause: "false"
        });
      } else if ( 0 !== _delay.length ) {
        $("#customizr-slider").carousel({
            interval: _delay
        });
      } else {
        $("#customizr-slider").carousel();
      }
    },

    manageHoverClass : function() {
      //add a class to the slider on hover => used to display the navigation arrow
      $(".carousel").hover( function() {
          $(this).addClass('tc-slid-hover');
        },
        function() {
          $(this).removeClass('tc-slid-hover');
        }
      );
    },

    //SLIDER ARROWS
    centerSliderArrows : function() {
      if ( 0 === $('.carousel').length )
          return;
      $('.carousel').each( function() {
          var _slider_height = $( '.carousel-inner' , $(this) ).height();
          $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
      });
    },


    //Slider swipe support with hammer.js
    addSwipeSupport : function() {
      if ( 'function' != typeof($.fn.hammer) )
        return;
      //prevent propagation event from sensible children
      $(".carousel input, .carousel button, .carousel textarea, .carousel select, .carousel a").on("touchstart touchmove", function(ev) {
          ev.stopPropagation();
      });

      var _is_rtl = $('body').hasClass('rtl');
      $('.carousel' ).each( function() {
          $(this).hammer().on('swipeleft tap', function() {
              $(this).carousel( ! _is_rtl ? 'next' : 'prev' );
          });
          $(this).hammer().on('swiperight', function(){
              $(this).carousel( ! _is_rtl ? 'prev' : 'next' );
          });
      });
    },

    //Has to be fire on load after all other methods
    //@todo understand why...
    sliderTriggerSimpleLoad : function() {
      this.triggerSimpleLoad( $( '.carousel .carousel-inner').find('img') );
    }
  };//methods {}

  czrapp.methods.Czr_Slider = {};
  $.extend( czrapp.methods.Czr_Slider , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

/************************************************
* USER EXPERIENCE SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
    init : function() {
       this.timer = 0;
       this.increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
    },//init


    //Event Listener
    eventListener : function() {
      var self = this;

      czrapp.$_window.scroll( function() {
        self.eventHandler( 'scroll' );
      });

    },//eventListener


    //Event Handler
    eventHandler : function ( evt ) {
      var self = this;

      switch ( evt ) {
        case 'scroll' :
          //react to window scroll only when we have the btt-arrow element
          //I do this here 'cause I plan to pass the btt-arrow option as postMessage in customize
          if ( 0 === $('.tc-btt-wrapper').length )
            return;

          //use a timer
          if ( this.timer) {
            this.increment++;
            clearTimeout(self.timer);
          }
          if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
            this.timer = setTimeout( function() {
              self.bttArrowVisibility();
            }, self.increment > 5 ? 50 : 0 );
          } else if ( czrapp.$_body.hasClass('ie') ) {
            this.timer = setTimeout( function() {
              self.bttArrowVisibility();
            }, self.increment > 5 ? 50 : 0 );
          }
        break;
      }
    },//eventHandler


    //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
    anchorSmoothScroll : function() {
      if ( ! TCParams.SmoothScroll || 'easeOutExpo' != TCParams.SmoothScroll )
            return;

      var _excl_sels = ( TCParams.SmoothScrollExclude && _.isArray( TCParams.SmoothScrollExclude ) ) ? TCParams.SmoothScrollExclude.join(',') : '';
      $('a[href^="#"]', '#content').not( _excl_sels ).click(function () {
        var anchor_id = $(this).attr("href");

        //anchor el exists ?
        if ( ! $(anchor_id).length )
          return;

        if ('#' != anchor_id) {
            $('html, body').animate({
                scrollTop: $(anchor_id).offset().top
            }, 700, TCParams.SmoothScroll);
        }
        return false;
      });//click
    },

    //Btt arrow visibility
    bttArrowVisibility : function () {
      if ( czrapp.$_window.scrollTop() > 100 )
        $('.tc-btt-wrapper').addClass('show');
      else
        $('.tc-btt-wrapper').removeClass('show');
    },//bttArrowVisibility



    //BACK TO TOP
    backToTop : function() {
      var $_html = $("html, body"),
          _backToTop = function($) {
            return ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
          };

      $(".back-to-top, .tc-btt-wrapper, .btt-arrow").on("click touchstart touchend", function ($) {
        $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
        $_html.animate({
            scrollTop: 0
        }, 1e3, function () {
            $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
            //czrapp.$_window.trigger('resize');
        });
        $.preventDefault();
      });
    },


    //VARIOUS HOVER ACTION
    widgetsHoverActions : function() {
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
    },


    //ATTACHMENT FADE EFFECT
    attachmentsFadeEffect : function() {
      $("article.attachment img").delay(500).animate({
            opacity: 1
        }, 700, function () {}
      );
    },


    //COMMENTS
    //Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
    clickableCommentButton : function() {
      if ( ! TCParams.HasComments )
        return;

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
    },


    //DYNAMIC REORDERING
    //Detect layout and reorder content divs
    dynSidebarReorder : function() {
      //custom sidebar reorder event listener setup
      var self = this;
      czrapp.$_body.on( 'reorder-sidebars' , function(e, param) { self.listenToSidebarReorderEvent(e, param); } );

      //fire on dom ready
      this.emit('emitSidebarReorderEvent');
    },


    //DYNAMIC REORDERING
    //Emit an event on the body el
    emitSidebarReorderEvent : function() {
      //Enable reordering if option is checked in the customizer.
      if ( 1 != TCParams.ReorderBlocks )
        return;

      var $_windowWidth         = czrapp.$_window.width();

      //15 pixels adjustement to avoid replacement before real responsive width
      if ( $_windowWidth  > 767 - 15 && czrapp.reordered_blocks )
        czrapp.$_body.trigger( 'reorder-sidebars', { to : 'normal' } );
      else if ( ( $_windowWidth  <= 767 - 15 ) && ! czrapp.reordered_blocks )
        czrapp.$_body.trigger( 'reorder-sidebars', { to : 'responsive' } );
    },


    //DYNAMIC REORDERING
    //Listen to event on body el
    listenToSidebarReorderEvent : function( e, param ) {
      //Enable reordering if option is checked in the customizer.
      if ( 1 != TCParams.ReorderBlocks )
        return;

      //assign default value to action param
      param               = _.isObject(param) ? param : { to : 'normal' };
      param.to            = param.to || 'normal';

      var LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
          RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar',
          $_wrapper           = $('#main-wrapper .container[role=main] > .column-content-wrapper'),
          $_content           = $("#main-wrapper .container .article-container"),
          $_left              = $("#main-wrapper .container " + LeftSidebarClass),
          $_right             = $("#main-wrapper .container " + RightSidebarClass),
          $_WindowWidth       = czrapp.$_window.width();

      //15 pixels adjustement to avoid replacement before real responsive width
      switch ( param.to ) {
        case 'normal' :
          if ( $_left.length ) {
            $_left.detach();
            $_content.detach();
            $_wrapper.append($_left).append($_content);
          }
          if ( $_right.length ) {
              $_right.detach();
              $_wrapper.append($_right);
          }
          czrapp.reordered_blocks = false; //this could stay in both if blocks instead
        break;

        case 'responsive' :
          if ( $_left.length ) {
             $_left.detach();
            $_content.detach();
            $_wrapper.append($_content).append($_left);
          }
          if ( $_right.length ) {
              $_right.detach();
              $_wrapper.append($_right);
          }
          czrapp.reordered_blocks = true; //this could stay in both if blocks instead
        break;
      }
    },



    //Handle dropdown on click for multi-tier menus
    dropdownMenuEventsHandler : function() {
      var $dropdown_ahrefs    = $('.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'),
          $dropdown_submenus  = $('.tc-open-on-click .dropdown .dropdown-submenu');

      //go to the link if submenu is already opened
      $dropdown_ahrefs.on('tap click', function(evt) {
        if ( ( $(this).next('.dropdown-menu').css('visibility') != 'hidden' &&
                $(this).next('.dropdown-menu').is(':visible')  &&
                ! $(this).parent().hasClass('dropdown-submenu') ) ||
             ( $(this).next('.dropdown-menu').is(':visible') &&
                $(this).parent().hasClass('dropdown-submenu') ) )
            window.location = $(this).attr('href');
      });//.on()

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
        });//.on()
      });//.each()
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
/************************************************
* STICKY HEADER SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
    init : function() {
      //cache jQuery el
      this.$_sticky_logo    = $('img.sticky', '.site-logo');
      this.$_resetMarginTop = $('#tc-reset-margin-top');

      //subclass properties
      this.elToHide         = []; //[ '.social-block' , '.site-description' ],
      this.customOffset     = +TCParams.stickyCustomOffset;
      this.logo             = 0 === this.$_sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;
      this.timer            = 0;
      this.increment        = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
      this.triggerHeight    = 20; //0.5 * windowHeight;
    },//init()


    triggerStickyHeaderLoad : function() {
      if ( ! this._is_sticky_enabled() )
        return;

      //LOADING ACTIONS
      czrapp.$_body.trigger( 'sticky-enabled-on-load' , { on : 'load' } );
    },


    stickyHeaderEventListener : function() {
      //LOADING ACTIONS
      var self = this;
      czrapp.$_body.on( 'sticky-enabled-on-load' , function() {
        self.stickyHeaderEventHandler('on-load');
      });//.on()

      //RESIZING ACTIONS
      czrapp.$_window.resize( function() {
        self.stickyHeaderEventHandler('resize');
      });

      czrapp.$_window.scroll( function() {
        self.stickyHeaderEventHandler('scroll');
      });
    },



    stickyHeaderEventHandler : function( evt ) {
      if ( ! this._is_sticky_enabled() )
        return;

      var self = this;

      switch ( evt ) {
        case 'on-load' :
          self._prepare_logo_transition();
          setTimeout( function() {
            self._sticky_refresh();
            self._sticky_header_scrolling_actions();
          } , 20 );//setTimeout()
        break;

        case 'scroll' :
           //use a timer
          if ( this.timer) {
            this.increment++;
            clearTimeout(self.timer);
          }

          if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
            this.timer = setTimeout( function() {
              self._sticky_header_scrolling_actions();
            }, self.increment > 5 ? 50 : 0 );
          } else if ( czrapp.$_body.hasClass('ie') ) {
            this.timer = setTimeout( function() {
              self._sticky_header_scrolling_actions();
            }, self.increment > 5 ? 50 : 0 );
          }
        break;

        case 'resize' :
          self._set_sticky_offsets();
          self._set_header_top_offset();
          self._set_logo_height();
        break;
      }
    },




    //STICKY HEADER SUB CLASS HELPER (private like)
    _is_scrolling : function() {
      return czrapp.$_body.hasClass('sticky-enabled') ? true : false;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _is_sticky_enabled : function() {
      return czrapp.$_body.hasClass('tc-sticky-header') ? true : false;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _get_initial_offset : function() {
      //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
      var initialOffset   = 0;
      if ( 1 == this.isUserLogged() && ! this.isCustomizing() ) {
        if ( 580 < czrapp.$_window.width() )
          initialOffset = czrapp.$_wpadminbar.height();
        else
          initialOffset = ! this._is_scrolling() ? czrapp.$_wpadminbar.height() : 0;
      }
      return initialOffset + this.customOffset;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _set_sticky_offsets : function() {
      var self = this;

      //Reset all values first
      czrapp.$_tcHeader.css('top' , '');
      czrapp.$_tcHeader.css('height' , 'auto' );
      this.$_resetMarginTop.css('margin-top' , '' ).show();

      //What is the initial offset of the header ?
      var headerHeight    = czrapp.$_tcHeader.outerHeight(true); /* include borders and eventual margins (true param)*/
      //set initial margin-top = initial offset + header's height
      this.$_resetMarginTop.css('margin-top' , ( + headerHeight + self.customOffset ) + 'px');
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _set_header_top_offset : function() {
      var self = this;
      //set header initial offset
      czrapp.$_tcHeader.css('top' , self._get_initial_offset() );
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _prepare_logo_transition : function(){ 
      //do nothing if the browser doesn't support csstransitions (modernizr)
      //or if no logo (includes the case where we have two logos, normal and sticky one)
      if ( ! ( czrapp.$_html.hasClass('csstransitions') && ( this.logo && 0 !== this.logo._logo.length ) ) )
        return;

      var logoW = this.logo._logo.originalWidth(),
          logoH = this.logo._logo.originalHeight();
      
      //check that all numbers are valid before using division
      if ( 0 === _.size( _.filter( [ logoW, logoH ], function(num){ return _.isNumber( parseInt(num, 10) ) && 0 !== num; } ) ) )
        return;
    
      this.logo._ratio = logoW / logoH;
      this.logo._logo.css('width' , logoW );
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _set_logo_height : function(){
      if ( this.logo && 0 === this.logo._logo.length || ! this.logo._ratio )
        return;
      var self = this;
      this.logo._logo.css('height' , self.logo._logo.width() / self.logo._ratio );

      setTimeout( function() {
          self._set_sticky_offsets();
          self._set_header_top_offset();
      } , 200 );
    },

    _sticky_refresh : function() {
      var self = this;
      setTimeout( function() {
          self._set_sticky_offsets();
          self._set_header_top_offset();
      } , 20 );
      czrapp.$_window.trigger('resize');
    },


    //SCROLLING ACTIONS
    _sticky_header_scrolling_actions : function() {
      this._set_header_top_offset();

      var self = this;
      //process scrolling actions
      if ( czrapp.$_window.scrollTop() > this.triggerHeight ) {
        if ( ! this._is_scrolling() )
            czrapp.$_body.addClass("sticky-enabled").removeClass("sticky-disabled");
      }
      else if ( this._is_scrolling() ){
        czrapp.$_body.removeClass("sticky-enabled").addClass("sticky-disabled");
        setTimeout( function() { self._sticky_refresh(); } ,
          self.isCustomizing ? 100 : 20
        );
        //additional refresh for some edge cases like big logos
        setTimeout( function() { self._sticky_refresh(); } , 200 );
      }
    }
  };//_methods{}

  czrapp.methods.Czr_StickyHeader = {};
  $.extend( czrapp.methods.Czr_StickyHeader , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};

/************************************************
* LET'S DANCE
*************************************************/
jQuery(function ($) {
  var toLoad = {
    BrowserDetect : [],
    Czr_Plugins : ['centerImagesWithDelay', 'imgSmartLoad' , 'dropCaps', 'extLinks' , 'fancyBox'],
    Czr_Slider : ['fireSliders', 'manageHoverClass', 'centerSliderArrows', 'addSwipeSupport', 'sliderTriggerSimpleLoad'],
    Czr_UserExperience : ['eventListener','anchorSmoothScroll', 'backToTop', 'widgetsHoverActions', 'attachmentsFadeEffect', 'clickableCommentButton', 'dynSidebarReorder', 'dropdownMenuEventsHandler' ],
    Czr_StickyHeader : ['stickyHeaderEventListener', 'triggerStickyHeaderLoad' ]
  };
  czrapp.cacheProp().loadCzr(toLoad);
});
