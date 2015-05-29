var $ = jQuery,
    Czr_Base = function() {
      //cache various jQuery el in base constructor
      this.$_body           = $('body');
      this.$_tcHeader       = $('.tc-header');
      this.$_wpadminbar     = $('#wpadminbar');

      //various properties definition
      this.joie             = 'property defined in Czr_Base';
      this.localized        = TCParams;
      this.isUserLogged     = this.$_body.hasClass('logged-in') || 0 !== this.$_wpadminbar.length;
      this.isCustomizing    = this.$_body.hasClass('is-customizing');
      this.reordered_blocks = false;//store the states of the sidebar layout
    },//parent class constructor
    _czr_ = Czr_Base.prototype;


/************************************************
* HELPERS
*************************************************/
/**
 * Fire a method and emit an event with the callback name on the body element
 * @param  cbs  : callback method or array of callbacks to fire in its subclass this context
 * @param  args : array of args to pass to the callback
 * @return void
 */
_czr_.emit = function( cbs, args ) {
  cbs = _.isArray(cbs) ? cbs : [cbs];
  var self = this;
  _.map( cbs, function(cb) {
    if ( 'function' == typeof(self[cb]) ) {
      self[cb].apply(self, args);
      self.$_body.trigger( cb, _.object( _.keys(args), args ) );
    }
  });//_.map
};


//helper to trigger a simple load
//=> allow centering when smart load not triggered by smartload
_czr_.triggerSimpleLoad = function( $_imgs ) {
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







/************************************************
* BROWSER DETECT SUB CLASS
*************************************************/
function Czr_BrowserDetect() {
  Czr_Base.call(this);//sets the parent this
  this.init();
}//constructor

//set the classical prototype chaining with inheritance
Czr_BrowserDetect.prototype = Object.create( Czr_Base.prototype );
Czr_BrowserDetect.prototype.constructor = Czr_BrowserDetect;
var _browser_detect_ = Czr_BrowserDetect.prototype;



//BROWSER DETECTION
_browser_detect_.init = function() {
  // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
  if ( $.browser.chrome )
      this.$_body.addClass("chrome");
  else if ( $.browser.webkit )
      this.$_body.addClass("safari");
  else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
      this.$_body.addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));

  //Adds version if browser = ie
  if ( this.$_body.hasClass("ie") )
      this.$_body.addClass($.browser.version);
};





/************************************************
* JQUERY PLUGINS SUB CLASS
*************************************************/
function Czr_Plugins() {
  Czr_Base.call(this);//sets the parent this
  this.init();
}//constructor

//set the classical prototype chaining with inheritance
Czr_Plugins.prototype = Object.create( Czr_Base.prototype );
Czr_Plugins.prototype.constructor = Czr_Plugins;
var _plugs_ = Czr_Plugins.prototype;

_plugs_.init = function() {
  //fire the center images plugin
  setTimeout( this.emit('CenterImages'), 300 );

  this.emit( ['ImgSmartLoad' , 'DropCaps', 'ExtLinks' , 'FancyBox'] );
};


//IMG SMART LOAD
//.article-container covers all post / page content : single and list
//__before_main_wrapper covers the single post thumbnail case
//.widget-front handles the featured pages
_plugs_.ImgSmartLoad = function() {
  if ( 1 == TCParams.imgSmartLoadEnabled )
    $( '.article-container, .__before_main_wrapper, .widget-front' ).imgSmartLoad( _.size( TCParams.imgSmartLoadOpts ) > 0 ? TCParams.imgSmartLoadOpts : {} );
  else {
    //if smart load not enabled => trigger the load event on img load
    var $_to_center = $( '.article-container, .__before_main_wrapper, .widget-front' ).find('img');
    this.triggerSimpleLoad($_to_center);
  }//end else
};


//FIRE DROP CAP PLUGIN
_plugs_.DropCaps = function() {
  if ( ! TCParams.dropcapEnabled || _.isObject( TCParams.dropcapWhere ) )
    return;

  $.each( TCParams.dropcapWhere , function( ind, val ) {
    if ( 1 == val ) {
      $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
        minwords : TCParams.dropcapMinWords,//@todo check if number
        skipSelectors : _.isObject(TCParams.dropcapSkipSelectors) ? TCParams.dropcapSkipSelectors : {}
      });
    }
  });//each
};


//FIRE EXT LINKS PLUGIN
//May be add (check if activated by user) external class + target="_blank" to relevant links
//images are excluded by default
//links inside post/page content
_plugs_.ExtLinks = function() {
  if ( ! TCParams.extLinksStyle && ! TCParams.extLinksTargetExt )
    return;
  $('a' , '.entry-content').extLinks({
    addIcon : TCParams.extLinksStyle,
    newTab : TCParams.extLinksTargetExt,
    skipSelectors : _.isObject(TCParams.extLinksSkipSelectors) ? TCParams.extLinksSkipSelectors : {}
  });
};

//FIRE FANCYBOX PLUGIN
//Fancybox with localized script variables
_plugs_.FancyBox = function() {
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
};


/**
* CENTER VARIOUS IMAGES
* @return {void}
*/
_plugs_.CenterImages = function() {
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
};//center_images







/************************************************
* SLIDER ACTIONS SUB CLASS
*************************************************/
function Czr_Slider() {
  Czr_Base.call(this);//set the parent this
  this.init();
}//constructor

//set the classical prototype chaining with inheritance
Czr_Slider.prototype = Object.create( Czr_Base.prototype );
Czr_Slider.prototype.constructor = Czr_Slider;
var _slider_ = Czr_Slider.prototype;


//INIT
_slider_.init = function() {
  this.emit( ['fireSliders', 'manageHoverClass', 'CenterSliderArrows', 'addSwipeSupport'] );

  this.triggerSimpleLoad( $( '.carousel .carousel-inner').find('img') );
  var self = this;

  //@todo EVENT
  //Recenter the slider arrows on resize
  $(window).resize( function(){
      self.CenterSliderArrows();
  });
};



_slider_.fireSliders = function(name, delay, hover) {
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
};

_slider_.manageHoverClass = function() {
  //add a class to the slider on hover => used to display the navigation arrow
  $(".carousel").hover( function() {
      $(this).addClass('tc-slid-hover');
    },
    function() {
      $(this).removeClass('tc-slid-hover');
    }
  );
};

//SLIDER ARROWS
_slider_.CenterSliderArrows = function() {
  if ( 0 === $('.carousel').length )
      return;
  $('.carousel').each( function() {
      var _slider_height = $( '.carousel-inner' , $(this) ).height();
      $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
  });
};


//Slider swipe support with hammer.js
_slider_.addSwipeSupport = function() {
  if ( 'function' != typeof($.fn.hammer) )
    return;
  //prevent propagation event from sensible children
  $(".carousel input, .carousel button, .carousel textarea, .carousel select, .carousel a").on("touchstart touchmove", function(ev) {
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
};





/************************************************
* USER EXPERIENCE SUB CLASS
*************************************************/
function Czr_UserExperience() {
  Czr_Base.call(this);//set the parent this
  this.init();
}//constructor

//set the classical prototype chaining with inheritance
Czr_UserExperience.prototype = Object.create( Czr_Base.prototype );
Czr_UserExperience.prototype.constructor = Czr_UserExperience;
var _userxp_ = Czr_UserExperience.prototype;

_userxp_.init = function() {
  //on dom ready
  this.emit( ['anchorSmoothScroll', 'backToTop', 'widgetsHoverActions', 'attachmentsFadeEffect', 'clickableCommentButton', 'dynSidebarReorder', 'dropdownMenuEventsHandler' ] );

  //@todo EVENT
  var self = this;
  $(window).resize(function () {
    setTimeout( self.emit( 'emitSidebarReorderEvent' ), 200 );
  });

};



//SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
_userxp_.anchorSmoothScroll = function() {
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
};


//BACK TO TOP
_userxp_.backToTop = function() {
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
        //$(window).trigger('resize');
    });
    $.preventDefault();
  });
};


//VARIOUS HOVER ACTION
_userxp_.widgetsHoverActions = function() {
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
};


//ATTACHMENT FADE EFFECT
_userxp_.attachmentsFadeEffect = function() {
  $("article.attachment img").delay(500).animate({
        opacity: 1
    }, 700, function () {}
  );
};


//COMMENTS
//Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
_userxp_.clickableCommentButton = function() {
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
};


//DYNAMIC REORDERING
//Detect layout and reorder content divs
_userxp_.dynSidebarReorder = function() {
  //custom sidebar reorder event listener setup
  var self = this;
  this.$_body.on( 'reorder-sidebars' , function(e, param) { self.listenToSidebarReorderEvent(e, param); } );

  //fire on dom ready
  this.emit('emitSidebarReorderEvent');
};


//DYNAMIC REORDERING
//Emit an event on the body el
_userxp_.emitSidebarReorderEvent = function() {
  //Enable reordering if option is checked in the customizer.
  if ( 1 != TCParams.ReorderBlocks )
    return;

  var $_windowWidth         = $(window).width();

  //15 pixels adjustement to avoid replacement before real responsive width
  if ( $_windowWidth  > 767 - 15 && this.reordered_blocks )
    this.$_body.trigger( 'reorder-sidebars', { to : 'normal' } );
  else if ( ( $_windowWidth  <= 767 - 15 ) && ! this.reordered_blocks )
    this.$_body.trigger( 'reorder-sidebars', { to : 'responsive' } );
};


//DYNAMIC REORDERING
//Listen to event on body el
_userxp_.listenToSidebarReorderEvent = function( e, param ) {
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
      $_WindowWidth       = $(window).width();

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
      this.reordered_blocks = false; //this could stay in both if blocks instead
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
      this.reordered_blocks = true; //this could stay in both if blocks instead
    break;
  }
};



//Handle dropdown on click for multi-tier menus
_userxp_.dropdownMenuEventsHandler = function() {
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
};




/************************************************
* STICKY HEADER SUB CLASS
*************************************************/
function Czr_StickyHeader() {
  Czr_Base.call(this);//set the parent this
  this.init();
}//constructor

//set the classical prototype chaining with inheritance
Czr_StickyHeader.prototype = Object.create( Czr_Base.prototype );
Czr_StickyHeader.prototype.constructor = Czr_StickyHeader;
var _stickyheader_ = Czr_StickyHeader.prototype;


_stickyheader_.init = function() {
  //cache jQuery el
  this.$_window         = $(window);
  this.$_sticky_logo    = $('img.sticky', '.site-logo');
  this.$_resetMarginTop = $('#tc-reset-margin-top');

  //subclass properties
  this.elToHide         = []; //[ '.social-block' , '.site-description' ],
  this.customOffset     = +TCParams.stickyCustomOffset;
  this.logo             = 0 === this.$_sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;
  this.timer            = 0;
  this.increment        = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
  this.triggerHeight    = 20; //0.5 * windowHeight;

  //on dom ready
  this.emit( ['triggerStickyHeaderLoad', 'stickyHeaderEventListener'] );
};//init()






_stickyheader_.triggerStickyHeaderLoad = function() {
  if ( ! this._is_sticky_enabled() )
    return;

  //LOADING ACTIONS
  this.$_body.trigger( 'sticky-enabled-on-load' , { on : 'load' } );
};


_stickyheader_.stickyHeaderEventListener = function() {
  //LOADING ACTIONS
  var self = this;
  this.$_body.on( 'sticky-enabled-on-load' , function() {
    self.stickyHeaderEventHandler('on-load');
  });//.on()

  //RESIZING ACTIONS
  this.$_window.resize( function() {
    self.stickyHeaderEventHandler('resize');
  });

  this.$_window.scroll( function() {
    self.stickyHeaderEventHandler('scroll');
  });
};



_stickyheader_.stickyHeaderEventHandler = function(param ) {
  if ( ! this._is_sticky_enabled() )
    return;

  var self = this;

  switch ( param ) {
    case 'on-load' :
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
        timer = setTimeout( function() {
          self._sticky_header_scrolling_actions();
        }, self.increment > 5 ? 50 : 0 );
      } else if ( this.$_body.hasClass('ie') ) {
        timer = setTimeout( function() {
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
};




//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._is_scrolling = function() {
  return this.$_body.hasClass('sticky-enabled') ? true : false;
};

//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._is_sticky_enabled = function() {
  return this.$_body.hasClass('tc-sticky-header') ? true : false;
};

//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._get_initial_offset = function() {
  //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
  var initialOffset   = 0;
  if ( 1 == this.isUserLogged && ! this.isCustomizing ) {
    if ( 580 < this.$_window.width() )
      initialOffset = this.$_wpadminbar.height();
    else
      initialOffset = ! this._is_scrolling() ? this.$_wpadminbar.height() : 0;
  }
  return initialOffset + this.customOffset;
};

//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._set_sticky_offsets = function() {
  var self = this;

  //Reset all values first
  this.$_tcHeader.css('top' , '');
  this.$_tcHeader.css('height' , 'auto' );
  this.$_resetMarginTop.css('margin-top' , '' ).show();

  //What is the initial offset of the header ?
  var headerHeight    = this.$_tcHeader.outerHeight(true); /* include borders and eventual margins (true param)*/
  //set initial margin-top = initial offset + header's height
  this.$_resetMarginTop.css('margin-top' , ( + self.headerHeight + self.customOffset ) + 'px');
};

//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._set_header_top_offset = function() {
  var self = this;
  //set header initial offset
  this.$_tcHeader.css('top' , self._get_initial_offset() );
};

//STICKY HEADER SUB CLASS HELPER (private like)
_stickyheader_._set_logo_height = function(){
  if ( this.logo && 0 === this.logo._logo.length || ! this.logo._ratio )
    return;
  var self = this;
  this.logo._logo.css('height' , self.logo._logo.width() / self.logo._ratio );

  setTimeout( function() {
      self._set_sticky_offsets();
      self._set_header_top_offset();
  } , 200 );
};

_stickyheader_._sticky_refresh = function() {
  var self = this;
  setTimeout( function() {
      self._set_sticky_offsets();
      self._set_header_top_offset();
  } , 20 );
  this.$_window.trigger('resize');
};


//SCROLLING ACTIONS
_stickyheader_._sticky_header_scrolling_actions = function() {
  this._set_header_top_offset();

  var self = this;
  //process scrolling actions
  if ( this.$_window.scrollTop() > this.triggerHeight ) {
    if ( ! this._is_scrolling() )
        this.$_body.addClass("sticky-enabled").removeClass("sticky-disabled");
  }
  else if ( this._is_scrolling() ){
    this.$_body.removeClass("sticky-enabled").addClass("sticky-disabled");
    setTimeout( function() { self._sticky_refresh(); } ,
      self.isCustomizing ? 100 : 20
    );
    //additional refresh for some edge cases like big logos
    setTimeout( function() { self._sticky_refresh(); } , 200 );
  }
};




/************************************************
* LET'S DANCE
*************************************************/
jQuery(function ($) {
  if ( ! TCParams || _.isEmpty(TCParams) )
    return;
  //init constructors
  new Czr_BrowserDetect();
  new Czr_Plugins();
  new Czr_Slider();
  new Czr_UserExperience();
  new Czr_StickyHeader();
});