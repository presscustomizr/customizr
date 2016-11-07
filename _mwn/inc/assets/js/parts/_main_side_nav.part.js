var czrapp = czrapp || {};
/************************************************
* SIDE NAV SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
    init : function() {
      this.$_sidenav                = $( '#tc-sn' );

      if ( ! this._is_sn_on() )
        return;

      //cache jQuery el
      this.$_page_wrapper           = $('#tc-page-wrap');
      this.$_page_wrapper_node      = this.$_page_wrapper.get(0);
      this.$_page_wrapper_btn       = $('.btn-toggle-nav', '#tc-page-wrap');

      this.$_sidenav_inner          = $( '.tc-sn-inner', this.$_sidenav);

      this._toggle_event            = czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click';

      this._browser_can_translate3d = ! czrapp.$_html.hasClass('no-csstransforms3d');

      /* Cross browser support for CSS "transition end" event */
      this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';

      //fire event listener
      this.sideNavEventListener();

      this._set_offset_height();

    },//init()

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    sideNavEventListener : function() {
      var self = this;

      //BUTTON CLICK/TAP
      czrapp.$_body.on( this._toggle_event, '.sn-toggle', function( evt ) {
        self.sideNavEventHandler( evt, 'toggle' );
      });

      //TRANSITION END
      this.$_page_wrapper.on( this.transitionEnd, function( evt ) {
        self.sideNavEventHandler( evt, 'transitionend' );
      });

      //RESIZING ACTIONS
      czrapp.$_window.on('tc-resize', function( evt ) {
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
           if ( this._is_translating() && evt.target == this.$_page_wrapper_node )
             this._transition_end_callback();
        break;

        case 'scroll' :
        case 'resize' :
          setTimeout( function(){
              self._set_offset_height();
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

      //2 cases translation enabled or disabled.
      //=> if translation3D enabled, the _transition_end_callback is fired at the end of anim by the transitionEnd event
      if ( this._browser_can_translate3d ){
        /* When the toggle menu link is clicked, animation starts */
        czrapp.$_body.addClass( 'animating ' + this._anim_type )
                     .trigger( this._anim_type + '_start' );
        if ( this._is_sticky_header() ){
          /* while animating disable sticky header if not scrolling */
          if ( czrapp.$_body.hasClass('sticky-disabled') )
            czrapp.$_body.removeClass('tc-sticky-header');
        }
      } else {
        czrapp.$_body.toggleClass('tc-sn-visible')
                     .trigger( this._anim_type );
      }

      //handles the page wrapper button fade in / out on click
      var _event = evt || event,
          $_clicked_btn = $( _event.target ),
          _is_opening   = $('#tc-page-wrap').has( $_clicked_btn).length > 0;

      this.$_page_wrapper_btn.each( function(){
        $(this).fadeTo( 500 , _is_opening ? 0 : 1 , function() {
          $(this).css( "visibility", _is_opening ? "hidden" : "visible");
        }); //.fadeTo() duration, opacity, callback
      } );
      return false;
   },

   _transition_end_callback : function() {
     czrapp.$_body.removeClass( 'animating ' +  this._anim_type)
                  .toggleClass( 'tc-sn-visible' )
                  .trigger( this._anim_type + '_end' )
                  .trigger( this._anim_type );

     /* on transition end re-set sticky header */
     if ( this._is_sticky_header() ){
       if ( czrapp.$_body.hasClass('sticky-disabled') )
         czrapp.$_body.addClass('tc-sticky-header');
      }
    },



    /***********************************************
    * HELPERS
    ***********************************************/
    //SIDE NAV SUB CLASS HELPER (private like)
    _is_sn_on : function() {
      return this.$_sidenav.length > 0 ? true : false;
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _get_initial_offset : function() {
      var _initial_offset = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;
      _initial_offset = _initial_offset && czrapp.$_window.scrollTop() && 'absolute' == czrapp.$_wpadminbar.css('position') ? 0 : _initial_offset;

      return _initial_offset; /* add a custom offset ?*/
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _set_offset_height : function() {
      var _offset = this._get_initial_offset();

      this.$_sidenav.css('top', _offset );
      this.$_sidenav_inner.css('max-height', this.$_sidenav.outerHeight() - _offset);
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _is_translating : function() {
      return czrapp.$_body.hasClass('animating');
    },

    //SIDE NAV SUB CLASS HELPER (private like)
    _is_sticky_header : function() {
      this.__is_sticky_header = this.__is_sticky_header || czrapp.$_body.hasClass('tc-sticky-header');
      return this.__is_sticky_header;
    }

  };//_methods{}

  czrapp.methods.Czr_SideNav = {};
  $.extend( czrapp.methods.Czr_SideNav , _methods );

})(jQuery, czrapp);
