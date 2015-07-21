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
      //Enable reordering if option is checked in the customizer.
      if ( 1 != TCParams.ReorderBlocks )
        return;

      this.__has_iframe = false;

      //fire on DOM READY and only for responsive devices
      if ( 'desktop' != this.getDevice() )
        this._reorderSidebars( 'responsive' );

      //fire on custom resize event
      var self = this;
      czrapp.$_body.on( 'tc-resize' , function(e, param) {
        param = _.isObject(param) ? param : {};
        var _to = 'desktop' != param.to ? 'responsive' : 'normal',
            _current = 'desktop' != param.current ? 'responsive' : 'normal';

        if ( _current != _to )
          self._reorderSidebars( _to );
      } );
    },


    //Reorder sidebar actions
    _reorderSidebars : function( _sidebarLayout ) {
      _sidebarLayout = _sidebarLayout || 'normal';
      var that = this,
          LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
          RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar',
          $_WindowWidth       = czrapp.$_window.width();

      //cache some $
      that.$_wrapper      = that.$_wrapper || $('#main-wrapper .container[role=main] > .column-content-wrapper');
      that.$_content      = that.$_content || $("#main-wrapper .container .article-container");
      that.$_left         = that.$_left || $("#main-wrapper .container " + LeftSidebarClass);
      that.$_right        = that.$_right || $("#main-wrapper .container " + RightSidebarClass);

      // do nothing if there's at least one iframe
      if ( that._has_iframe( [this.$_content, this.$_left, this.$_right] ) ) 
        return;

      //15 pixels adjustement to avoid replacement before real responsive width
      switch ( _sidebarLayout ) {
        case 'normal' :
          if ( that.$_left.length ) {
            that.$_left.detach();
            that.$_content.detach();
            that.$_wrapper.append(that.$_left).append(that.$_content);
          }
          if ( that.$_right.length ) {
              that.$_right.detach();
              that.$_wrapper.append(that.$_right);
          }
        break;

        case 'responsive' :
          if ( that.$_left.length ) {
             that.$_left.detach();
            that.$_content.detach();
            that.$_wrapper.append(that.$_content).append(that.$_left);
          }
          if ( that.$_right.length ) {
              that.$_right.detach();
              that.$_wrapper.append(that.$_right);
          }
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
    },

    //@return void()
    //simply toggles a "hover" class to the relevant elements
    menuButtonHover : function() {
      var $_menu_btns = $('.btn-toggle-nav');
      //BUTTON HOVER (with handler)
      $_menu_btns.hover(
        function( evt ) {
          $(this).addClass('hover');
        },
        function( evt ) {
          $(this).removeClass('hover');
        }
      );
    },


    //Mobile behaviour for the secondary menu
    secondMenuRespActions : function() {
      if ( ! TCParams.isSecondMenuEnabled )
        return;
      //Enable reordering if option is checked in the customizer.
      var userOption = TCParams.secondMenuRespSet || false,
          that = this;
      //if not a relevant option, abort
      if ( ! userOption || -1 == userOption.indexOf('in-sn') )
        return;

      //cache some $
      this.$_sec_menu_els  = this.$_sec_menu_els || $('.nav > li', '.tc-header .nav-collapse');
      this.$_sn_wrap       = this.$_sn_wrap || $('.sn-nav', '.sn-nav-wrapper');
      this.$_sec_menu_wrap = this.$_sec_menu_wrap || $('.nav', '.tc-header .nav-collapse');

      //fire on DOM READY
      var _locationOnDomReady = 'desktop' == this.getDevice() ? 'navbar' : 'side_nav';

      if ( 'desktop' != this.getDevice() )
        this._manageMenuSeparator( _locationOnDomReady , userOption)._moveSecondMenu( _locationOnDomReady , userOption );

      //fire on custom resize event
      czrapp.$_body.on( 'tc-resize', function( e, param ) {
        param = _.isObject(param) ? param : {};
        var _to = 'desktop' != param.to ? 'side_nav' : 'navbar',
            _current = 'desktop' != param.current ? 'side_nav' : 'navbar';

        if ( _current == _to )
          return;

        that._manageMenuSeparator( _to, userOption)._moveSecondMenu( _to, userOption );
      } );//.on()
    },

    _manageMenuSeparator : function( _to, userOption ) {
      //add/remove a separator between the two menus
      var that = this,
          _separatorContent = function( _pattern, _loop ) {
            var _html = [];
            for(var i = 0; i < ( _loop || 50 ); i++) {
              _html.push( _pattern || '/' );
            }
            return _html.join('');
          };
      if ( 'navbar' == _to )
        $( '.secondary-menu-separator', that.$_sn_wrap).remove();
      else {
        $_sep = $( '<li/>', {
          class : 'menu-item secondary-menu-separator',
          html : '<a href="#"><span class="sep-pattern">' + _separatorContent('/') + '</span></a>'
        } );

        switch(userOption) {
          case 'in-sn-before' :
            this.$_sn_wrap.prepend($_sep);
          break;

          case 'in-sn-after' :
            this.$_sn_wrap.append($_sep);
          break;
        }
      }
      return this;
    },


    //@return void()
    //@param _where = menu items location string 'navbar' or 'side_nav'
    _moveSecondMenu : function( _where, userOption ) {
      _where = _where || 'side_nav';
      var that = this;
      switch( _where ) {
          case 'navbar' :
            that.$_sec_menu_wrap.append(that.$_sec_menu_els);
          break;

          case 'side_nav' :
            if ( 'in-sn-before' == userOption )
              that.$_sn_wrap.prepend(that.$_sec_menu_els);
            else
              that.$_sn_wrap.append(that.$_sec_menu_els);
          break;
        }
    },

    //Helpers
    
    //Check if the passed element contains an iframe
    //@return bool
    //@param $_elements = mixed
    _has_iframe : function ( $_elements ) {
      if ( ! this.__has_iframe ){ 
        var that = this;
        _.each( $_elements, function($_el){
          if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 ){
            that.__has_iframe = true; 
            return;
          }
        });
      }
      return this.__has_iframe;
    }
  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );

})(jQuery, czrapp);
