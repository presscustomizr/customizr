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

      //fire event listener
      this.sideNavEventListener();

      this._set_offset_height();
      this._init_scrollbar();

    },//init()

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    sideNavEventListener : function() {
      var self = this;

      //BUTTON CLICK/TAP
      czrapp.$_body.on( this._toggle_event, '[data-toggle="sidenav"]', function( evt ) {
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

      czrapp.$_window.scroll( function( evt ) {
        self.sideNavEventHandler( evt, 'scroll');
      });

    },


    sideNavEventHandler : function( evt, evt_name ) {
      var self = this;

      switch ( evt_name ) {
        case 'toggle':
          // prevent multiple firing of the click event
          if ( ! this._is_translating() )
            this._toggle_callback( evt );
        break;

        case 'transitionend' :
           // react to the transitionend just if translating
           if ( this._is_translating() && evt.target == $( this._sidenav_selector ).get()[0] )
             this._transition_end_callback();
        break;

        case 'sn-open'  :
            this._end_visibility_toggle();
        break;

        case 'sn-close' :
            this._end_visibility_toggle();
            this._set_offset_height();
        break;

        case 'scroll' :
        case 'resize' :
          setTimeout( function() {
            if ( ! this._doingWindowAnimation  ) {
              this._doingWindowAnimation  = true;
              window.requestAnimationFrame( function() {
                self._set_offset_height();
                this._doingWindowAnimation  = false;
              });
            }
          }, 200);

        break;
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
      $( this._toggler_selector ).toggleClass( 'collapsed' );

      //Sidenav class toggling
      $( this._sidenav_selector ).toggleClass( this._active_class );

    },

    /***********************************************
    * HELPERS
    ***********************************************/
    //SIDE NAV SUB CLASS HELPER (private like)
    _is_sn_on : function() {
      return $( this._sidenav_selector ).length > 0 ? true : false;
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
    _init_scrollbar : function() {

      if ( 'function' == typeof $.fn.mCustomScrollbar ) {

        $( '.' + this._sidenav_menu_class, this._sidenav_selector ).mCustomScrollbar({

            theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',

        });

      }

    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _is_translating : function() {

      return czrapp.$_body.hasClass('animating');

    },

  };//_methods{}

  czrapp.methods.SideNav = {};
  $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);