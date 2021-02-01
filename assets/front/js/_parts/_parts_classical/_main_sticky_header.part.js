var czrapp = czrapp || {};
/************************************************
* STICKY HEADER SUB CLASS
*************************************************/
  /*
  * The script uses a placeholder for the sticky-element:
  * - when the "sticky candidate" overlaps the content (e.g. a slider) at the first first scroll
  * a placeholder will be injected. The placeholder has position absolute and its height is set equal to the
  * sticky candidate height when injected, reset to 0 when the fixed element backs to normal state, set again
  * when scrolling down.
  * - when the "sticky candidate" pushes the content, the placeholder is staticaly positioned,
  * it's injected each time the candidate sticks, its height is set equal to the sticky candidate height,
  * and reset when it unsticks.
  *
  * In both cases the placeholder will be our reference to switch from sticky to un-sticky mode
  * and the reference to switch from un-sticky to sticky ONLY in the "overlapping" case. 'Cause in that
  * case the trigger point dynamic computation cannto rely the sticky candidate as its position is
  * fixed or absolute
  * (see _toggleStickyAt() )
  */
(function($, czrapp) {
      var _methods =  {
          initOnDomReady : function() {
                var self = this;
                //cache jQuery el
                this.stickyHeaderCacheElements();

                //subclass properties
                this.elToHide         = []; //[ '.social-block' , '.site-description' ],
                this.customOffset     = TCParams.stickyCustomOffset || {};// defaults : { _initial : 0, _scrolling : 0 }

                this.triggerHeight    = 20; //0.5 * windowHeight;

                this.scrollingDelay   = 1 != TCParams.timerOnScrollAllBrowsers && czrapp.$_body.hasClass('ie') ? 50 : 5;

                //isHeaderSticky is set on-load and on scroll the following way :
                //self.isHeaderSticky( czrapp.$_window.scrollTop() > self.triggerHeight );
                //Then, it is listened to by isHeaderStickyReact() to set the relevant css classes and fire the relevant actions
                this.isHeaderSticky   = new czrapp.Value( false );
                this.isHeaderSticky.bind( function( isSticky ) { self._isHeaderStickyReact( isSticky ); } );

                //Fire
                this.stickyHeaderEventListener();
                this.triggerStickyHeaderLoad();
          },//init()

          //fired in initOnDomReady()
          stickyHeaderCacheElements : function() {
                //cache jQuery el
                this.$_resetMarginTop = $('#tc-reset-margin-top');
                this.$_sticky_logo    = $('img.sticky', '.site-logo');
                this.logo             = 0 === this.$_sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;
          },

          //fired in initOnDomReady()
          stickyHeaderEventListener : function() {
                //LOADING ACTIONS
                var self = this;
                czrapp.$_body.on( 'sticky-enabled-on-load' , function() {
                      self.stickyHeaderEventHandler( 'on-load' );
                });//.on()

                //RESIZING ACTIONS
                czrapp.$_window.on( 'tc-resize', function() {
                      self.stickyHeaderEventHandler( 'resize' );
                });

                //PARTIAL REFRESH ACTIONS
                czrapp.$_body.on( 'partialRefresh.czr', function( e, placement ) {
                      if ( placement.container && placement.container.hasClass( 'tc-header' )  ) {
                            self.stickyHeaderCacheElements();
                            self.stickyHeaderEventHandler( 'resize' );
                      }
                });

                //SCROLLING ACTIONS
                //decrease the scrolling trigger delay when smoothscroll on to avoid not catching the scroll when scrolling fast and sticky header not already triggered
                czrapp.$_window.on('scroll', _.throttle( function() {
                      self.stickyHeaderEventHandler( 'scroll' );
                }, ! ( czrapp.$_body.hasClass('tc-smoothscroll') && ! self.isHeaderSticky() ) ? self.scrollingDelay : 15 ) );

                //SIDENAV ACTIONS => recalculate the top offset on sidenav toggle
                czrapp.$_body.on( czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click' , '.sn-toggle', function() {
                      self.stickyHeaderEventHandler( 'sidenav-toggle' );
                });
          },

          //fired in initOnDomReady()
          triggerStickyHeaderLoad : function() {
                if ( ! this._is_sticky_enabled() )
                  return;

                //LOADING ACTIONS
                czrapp.$_body.trigger( 'sticky-enabled-on-load' , { on : 'load' } );
          },

          // Swiss Army Knife Callback for the various user or api events :
          // - on-load
          // - resize
          // - scroll
          // - sidenav-toggle
          stickyHeaderEventHandler : function( evt ) {
                if ( ! this._is_sticky_enabled() )
                  return;

                var self = this;

                switch ( evt ) {
                      case 'on-load' :
                            self._prepare_logo_transition();
                            setTimeout( function() {
                                  self._sticky_refresh();
                                  self._set_header_top_offset();
                                  self.isHeaderSticky( czrapp.$_window.scrollTop() > self.triggerHeight );
                            } , 20 );//setTimeout()
                      break;

                      case 'scroll' :
                            self._set_header_top_offset();
                            self.isHeaderSticky( czrapp.$_window.scrollTop() > self.triggerHeight );
                      break;

                      case 'resize' :
                      case 'sidenav-toggle' :
                            self._set_sticky_offsets();
                            self._set_header_top_offset();
                            self._set_logo_height();
                      break;
                }
          },

          //Callback of isHeaderSticky obervable Value()
          //@param isSticky = bool
          _isHeaderStickyReact : function( isSticky ) {
                var self = this;
                if ( isSticky ) {
                      czrapp.$_body
                            .addClass("sticky-enabled")
                            .removeClass("sticky-disabled")
                            .trigger('tc-sticky-enabled');
                      // set the logo height, makes sense just when the logo isn't shrinked
                      if ( ! czrapp.$_header.hasClass('tc-shrink-on') ) {
                            self._set_logo_height();
                      }
                } else {
                      czrapp.$_body
                            .removeClass("sticky-enabled")
                            .addClass("sticky-disabled")
                            .trigger('tc-sticky-disabled');
                      setTimeout( function() { self._sticky_refresh(); } ,
                            self._isCustomizing ? 100 : 20
                      );
                      //additional refresh for some edge cases like big logos
                      setTimeout( function() { self._sticky_refresh(); } , 200 );
                }
          },


          //STICKY HEADER SUB CLASS HELPER (private like)
          _is_sticky_enabled : function() {
                return czrapp.$_body.hasClass('tc-sticky-header');
          },

          //STICKY HEADER SUB CLASS HELPER (private like)
          _get_top_offset : function() {
                //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
                //custom offset : are we scrolling ? => 2 custom top offset values can be defined by users : initial and scrolling
                //make sure custom offset are set and numbers
                var initialOffset   = 0,
                    that            = this,
                    customOffset    = +this._get_custom_offset( that.isHeaderSticky() ? '_scrolling' : '_initial' ),
                    adminBarHeight  = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;

                if ( 1 == this.isUserLogged() && !this._isCustomizing() ) {
                      if ( 580 < czrapp.$_window.width() ) {
                              initialOffset = adminBarHeight;
                      } else {
                              initialOffset = ! this.isHeaderSticky() ? adminBarHeight : 0;
                      }
                }
                return initialOffset + customOffset ;
          },


          //CUSTOM TOP OFFSET
          //return the user defined dynamic or static custom offset
          //custom offset is a localized param that can be passed with the wp filter : tc_sticky_custom_offset
          //its default value is an object : { _initial : 0, _scrolling : 0, options : { _static : true, _element : "" }
          //if _static is set to false and a dom element is provided, then the custom offset will be the calculated height of the element
          _get_custom_offset : function( _context ) {
                //Always check if this.customOffset is well formed
                if ( _.isEmpty( this.customOffset ) )
                  return 0;
                if ( ! this.customOffset[_context] )
                  return 0;
                if ( ! this.customOffset.options )
                  return this.customOffset[_context];

                //always return a static value for the scrolling context;
                if ( '_scrolling' == _context )
                  return +this.customOffset[_context] || 0;

                //INITIAL CONTEXT
                //CASE 1 : STATIC
                if ( this.customOffset.options._static )
                  return +this.customOffset[_context] || 0;

                var that = this,
                    $_el = $(that.customOffset.options._element);

                //CASE 2 : DYNAMIC : based on an element's height
                //does the element exists?
                if ( ! $_el.length )
                  return 0;
                else {
                  return $_el.outerHeight() || +this.customOffset[_context] || 0;
                }
          },




          //STICKY HEADER SUB CLASS HELPER (private like)
          _set_sticky_offsets : function() {
                //Reset all values first
                czrapp.$_header.css('top' , '');
                czrapp.$_header.css('height' , 'auto' );
                this.$_resetMarginTop.css('margin-top' , '' ).show();

                //What is the initial offset of the header ?
                var headerHeight    = czrapp.$_header.outerHeight(true); /* include borders and eventual margins (true param)*/
                //set initial margin-top = initial offset + header's height
                this.$_resetMarginTop.css('margin-top' , + headerHeight  + 'px');
          },

          //STICKY HEADER SUB CLASS HELPER (private like)
          _set_header_top_offset : function() {
                var self = this;
                //set header initial offset
                czrapp.$_header.css('top' , self._get_top_offset() );
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
                if ( 2 != _.size( _.filter( [ logoW, logoH ], function(num){ return _.isNumber( parseInt(num, 10) ) && 0 !== num; } ) ) )
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
          }
    };//_methods{}

    czrapp.methods.StickyHeader = {};
    $.extend( czrapp.methods.StickyHeader , _methods );

})(jQuery, czrapp);