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

    //outline firefox fix, see https://github.com/presscustomizr/customizr/issues/538
    outline: function() {
      if ( czrapp.$_body.hasClass( 'mozilla' ) && 'function' == typeof( tcOutline ) )
          tcOutline();
    },

    //SMOOTH SCROLL
    smoothScroll: function() {
      if ( TCParams.SmoothScroll && TCParams.SmoothScroll.Enabled )
        smoothScroll( TCParams.SmoothScroll.Options );
    },

    //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
    anchorSmoothScroll : function() {
      if ( ! TCParams.anchorSmoothScroll || 'easeOutExpo' != TCParams.anchorSmoothScroll )
            return;

      var _excl_sels = ( TCParams.anchorSmoothScrollExclude && _.isArray( TCParams.anchorSmoothScrollExclude.simple ) ) ? TCParams.anchorSmoothScrollExclude.simple.join(',') : '',
          self = this,
          $_links = $('a[href^="#"]', '#content').not(_excl_sels);

      //Deep exclusion
      //are ids and classes selectors allowed ?
      //all type of selectors (in the array) must pass the filter test
      _deep_excl = _.isObject( TCParams.anchorSmoothScrollExclude.deep ) ? TCParams.anchorSmoothScrollExclude.deep : null ;
      if ( _deep_excl )
        _links = _.toArray($_links).filter( function ( _el ) {
          return ( 2 == ( ['ids', 'classes'].filter(
                        function( sel_type) {
                            return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                        } ) ).length
                );
        });
      $(_links).click( function () {
        var anchor_id = $(this).attr("href");

        //anchor el exists ?
        if ( ! $(anchor_id).length )
          return;

        if ('#' != anchor_id) {
            $('html, body').animate({
                scrollTop: $(anchor_id).offset().top
            }, 700, TCParams.anchorSmoothScroll);
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
          _backToTop = function( evt ) {
            return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
          };

      $(".back-to-top, .tc-btt-wrapper, .btt-arrow").on("click touchstart touchend", function ( evt ) {
        evt.preventDefault();
        evt.stopPropagation();
        $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
        $_html.animate({
            scrollTop: 0
        }, 1e3, function () {
            $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
            //czrapp.$_window.trigger('resize');
        });
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
      that.$_content      = that.$_content || $("#main-wrapper .container .article-container");
      that.$_left         = that.$_left || $("#main-wrapper .container " + LeftSidebarClass);
      that.$_right        = that.$_right || $("#main-wrapper .container " + RightSidebarClass);

      // check if we have iframes
      iframeContainers = that._has_iframe( { 'content' : this.$_content, 'left' : this.$_left } ) ;

      var leftIframe    = $.inArray('left', iframeContainers) > -1,
          contentIframe = $.inArray('content', iframeContainers) > -1;

      //both conain iframes => do nothing
      if ( leftIframe && contentIframe )
        return;

      if ( that.$_left.length ) {
        if ( leftIframe )
          that.$_content[ _sidebarLayout === 'normal' ?  'insertAfter' : 'insertBefore']( that.$_left );
        else
          that.$_left[ _sidebarLayout === 'normal' ?  'insertBefore' : 'insertAfter']( that.$_content );
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

      /* Utils */
      var _cacheElements = function() {
            //cache some $
            that.$_sec_menu_els  = $('.nav > li', '.tc-header .nav-collapse');
            that.$_sn_wrap       = $('.sn-nav', '.sn-nav-wrapper');
            that.$_sec_menu_wrap = $('.nav', '.tc-header .nav-collapse');
          },
          _maybeClean = function() {
            var $_sep = $( '.secondary-menu-separator' );

            if ( $_sep.length ) {

              switch(userOption) {
                  //maybe clean menu items before the separator in sn
                  case 'in-sn-before' :
                    $_sep.prevAll('.menu-item').remove();
                  break;
                  //maybe clean menu items after the separator in sn
                  case 'in-sn-after' :
                    $_sep.nextAll('.menu-item').remove();
                  break;
              }
              //remove separator
              $_sep.remove();
            }
          };
      /* end utils */

      //cache some $
      _cacheElements();

      //fire on DOM READY
      var _locationOnDomReady = 'desktop' == this.getDevice() ? 'navbar' : 'side_nav';

      if ( 'desktop' != this.getDevice() )
        this._manageMenuSeparator( _locationOnDomReady , userOption)._moveSecondMenu( _locationOnDomReady , userOption );

      //fire on custom resize event
      czrapp.$_body.on( 'tc-resize partialRefresh.czr', function( e, param ) {
        var _force = false;

        if ( 'partialRefresh' == e.type && 'czr' === e.namespace && param.container.hasClass('tc-header')  ) {
          //clean old moved elements and separator
          _maybeClean();
          //re-cache elements
          _cacheElements();
          //setup params for the move to
          param   = { to: czrapp.current_device, current: czrapp.current_device };
          //force actions
          _force  = true;
        }

        param = _.isObject(param) ? param : {};
        var _to = 'desktop' != param.to ? 'side_nav' : 'navbar',
            _current = 'desktop' != param.current ? 'side_nav' : 'navbar';

        if ( _current == _to && !_force )
          return;

        that._manageMenuSeparator( _to, userOption)._moveSecondMenu( _to, userOption );
      } );//.on()

    },

    _manageMenuSeparator : function( _to, userOption ) {
      //add/remove a separator between the two menus
      var that = this;
      if ( 'navbar' == _to )
        $( '.secondary-menu-separator', that.$_sn_wrap).remove();
      else {
        $_sep = $( '<li class="menu-item secondary-menu-separator"><hr class="featurette-divider"></hr></li>' );

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

    //Check if the passed element(s) contains an iframe
    //@return list of containers
    //@param $_elements = mixed
    _has_iframe : function ( $_elements ) {
      var that = this,
          to_return = [];
      _.map( $_elements, function( $_el, container ){
        if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
          to_return.push(container);
      });
      return to_return;
    }

  };//_methods{}

  czrapp.methods.Czr_UserExperience = {};
  $.extend( czrapp.methods.Czr_UserExperience , _methods );

})(jQuery, czrapp);
