var czrapp = czrapp || {};

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
