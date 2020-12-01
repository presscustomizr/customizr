var czrapp = czrapp || {};
/************************************************
* SIDE NAV SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
    initOnDomReady : function() {
      this._sidenav_selector        = '#tc-sn';

      if ( ! this._is_sn_on() )
        return;

      //variable definition
      this._doingWindowAnimation    = false;

      this._sidenav_inner_class     = 'tc-sn-inner';
      this._sidenav_menu_class      = 'nav__menu-wrapper';

      this._toggle_event            = 'click';
      this._toggler_selector        = '[data-toggle="sidenav"]';
      this._active_class            = 'show';

      this._browser_can_translate3d = ! czrapp.$_html.hasClass('no-csstransforms3d');

      /* Cross browser support for CSS "transition end" event */
      this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';

      // fire event listener
      // => the first toggle event will load and instantiate the plugin if not already done
      this.sideNavEventListener();

      this._set_offset_height();
      //this._init_scrollbar();

    },//init()

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    sideNavEventListener : function() {
      var self = this;

      //BUTTON CLICK/TAP
      //// => the first toggle event will load and instantiate the plugin if not already done
      czrapp.$_body.on( this._toggle_event, this._toggler_selector, function( evt ) {
        evt.preventDefault(); //<- avoid on link click reaction which adds '#' to the browser history
        self.sideNavEventHandler( evt, 'toggle' );
      });

      //TRANSITION END
      czrapp.$_body.on( this.transitionEnd, '#tc-sn', function( evt ) {
        self.sideNavEventHandler( evt, 'transitionend' );
      });

      //END TOGGLING
      czrapp.$_body.on( 'sn-close sn-open', function( evt ) {
        self.sideNavEventHandler( evt, evt.type );
      });

      //RESIZING ACTIONS
      czrapp.$_window.on('resize', function( evt ) {
        self.sideNavEventHandler( evt, 'resize');
      });

      czrapp.$_window.on('scroll', function( evt ) {
        self.sideNavEventHandler( evt, 'scroll');
      });

    },

    // @return promise()
    maybeLoadScript : function() {
          var dfd = $.Deferred();
          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                return dfd.resolve().promise();
          } else {
                // Check if the load request has already been made, but not yet finished.
                if ( czrapp.base.scriptLoadingStatus.mCustomScrollbar && 'pending' == czrapp.base.scriptLoadingStatus.mCustomScrollbar.state() ) {
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.done( function() {
                            dfd.resolve();
                      });
                      return dfd.promise();
                }

                // set the script loading status now to avoid several calls
                czrapp.base.scriptLoadingStatus.mCustomScrollbar = czrapp.base.scriptLoadingStatus.mCustomScrollbar || $.Deferred();

                // Load style
                if ( $('head').find( '#czr-custom-scroll-bar' ).length < 1 ) {
                      $('head').append( $('<link/>' , {
                            rel : 'stylesheet',
                            id : 'czr-custom-scroll-bar',
                            type : 'text/css',
                            href : czrapp.localized.assetsPath + 'css/jquery.mCustomScrollbar.min.css'
                      }) );
                }

                var _url = czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.min.js?v=' + czrapp.localized.version;
                if ( czrapp.localized.isDevMode ) {
                    _url = czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.js?v=' + czrapp.localized.version;
                }
                // Load js
                $.ajax( {
                      url : _url,
                      cache : true,// use the browser cached version when availabl
                      dataType: "script"
                }).done(function() {
                      if ( 'function' != typeof $.fn.mCustomScrollbar )
                        return dfd.rejected();

                      // The script is loaded. Say it Globally
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.resolve();

                      dfd.resolve();
                }).fail( function() {
                      czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                });
          }
          return dfd.promise();
    },


    // load script if needed ( // => the first toggle event will load and instantiate the plugin if not already done )
    // init customScroll on first toggle event
    // bind events callbacks
    sideNavEventHandler : function( evt, evt_name ) {
          var self = this;
          var _do = function() {
                switch ( evt_name ) {
                      case 'toggle':
                        // prevent multiple firing of the click event
                        if ( ! self._is_translating() )
                          self._toggle_callback( evt );
                      break;

                      case 'transitionend' :
                         // react to the transitionend just if translating
                         if ( self._is_translating() && evt.target == $( self._sidenav_selector ).get()[0] )
                           self._transition_end_callback();
                      break;

                      case 'sn-open'  :
                          self._end_visibility_toggle();
                          $( self._toggler_selector, self._sidenav_selector ).trigger( "focus" );
                      break;

                      case 'sn-close' :
                          self._end_visibility_toggle();
                          self._set_offset_height();
                      break;

                      case 'scroll' :
                      case 'resize' :
                        setTimeout( function() {
                          if ( ! self._doingWindowAnimation  ) {
                            self._doingWindowAnimation  = true;
                            window.requestAnimationFrame( function() {
                              self._set_offset_height();
                              self._doingWindowAnimation  = false;
                            });
                          }
                        }, 200);

                      break;
                }
          };

          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                if (  ! $( '.' + self._sidenav_menu_class, self._sidenav_selector).data( 'mCustomScrollbar' ) ) {
                      self._init_scrollbar();
                }
                _do();
          } else {
                if ( 0 < $( '.' + self._sidenav_menu_class, self._sidenav_selector ).length ) {
                      if ( 'toggle' == evt_name ) {
                            self.maybeLoadScript().done( function() {
                                  self._init_scrollbar();
                                  _do();
                            });
                      }
                }
          }
    },


    //SIDE NAV SUB CLASS HELPER (private like)
    _init_scrollbar : function() {
          var self = this;
          var _init = function() {
                $( '.' + self._sidenav_menu_class, self._sidenav_selector ).mCustomScrollbar({
                      theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                });
                $( '.' + self._sidenav_menu_class, self._sidenav_selector).data( 'mCustomScrollbar', true );
          };

          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                _init();
          } else {
                self.maybeLoadScript().done( function() {
                      _init();
                });
          }
    },

    _toggle_callback : function ( evt ){
      evt.preventDefault();

      if ( czrapp.$_body.hasClass( 'tc-sn-visible' ) )
        this._anim_type = 'sn-close';
      else
        this._anim_type = 'sn-open';

      //aria attribute toggling
      var _aria_expanded_attr = 'sn-open' == this._anim_type; //boolean
      $( this._toggler_selector ).attr('aria-expanded', _aria_expanded_attr );
      $( this._sidenav_selector ).attr('aria-expanded', _aria_expanded_attr );

      //2 cases translation enabled or disabled.
      //=> if translation3D enabled, the _transition_end_callback is fired at the end of anim by the transitionEnd event
      if ( this._browser_can_translate3d ){
        /* When the toggle menu link is clicked, animation starts */
        czrapp.$_body.addClass( 'animating ' + this._anim_type )
                     .trigger( this._anim_type + '_start' );
      } else {
        czrapp.$_body.toggleClass('tc-sn-visible')
                     .trigger( this._anim_type );
      }

      return false;
   },

    _transition_end_callback : function() {
      czrapp.$_body.removeClass( 'animating ' +  this._anim_type)
                   .toggleClass( 'tc-sn-visible' )
                   .trigger( this._anim_type + '_end' )
                   .trigger( this._anim_type );

    },

    _end_visibility_toggle : function() {

      //Toggler buttons class toggling
      $( this._toggler_selector ).toggleClass( 'czr-collapsed' );

      //Sidenav class toggling
      $( this._sidenav_selector ).toggleClass( this._active_class );

    },

    /***********************************************
    * HELPERS
    ***********************************************/
    //SIDE NAV SUB CLASS HELPER (private like)
    _is_sn_on : function() {
      return $( this._sidenav_selector ).length > 0;
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _get_initial_offset : function() {
      var _initial_offset = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;
      _initial_offset = _initial_offset && czrapp.$_window.scrollTop() && 'absolute' == czrapp.$_wpadminbar.css('position') ? 0 : _initial_offset;

      return _initial_offset; /* add a custom offset ?*/
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _set_offset_height : function() {
      var _offset         = this._get_initial_offset(),
          $_sidenav_menu  = $( '.' + this._sidenav_menu_class, this._sidenav_selector ),
          $_sidenav       = $( this._sidenav_selector );

      if ( ! ( $_sidenav_menu.length && $_sidenav.length ) )
        return;

      var winHeight       = 'undefined' === typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
          newMaxHeight    = winHeight - $_sidenav_menu.offset().top + czrapp.$_window.scrollTop();

      $_sidenav_menu.css('height' , newMaxHeight + 'px');
      $_sidenav.css('top', _offset );

    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _is_translating : function() {

      return czrapp.$_body.hasClass('animating');

    },

  };//_methods{}

  czrapp.methods.SideNav = {};
  $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);