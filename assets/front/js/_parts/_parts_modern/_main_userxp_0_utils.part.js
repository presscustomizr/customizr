var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {
        setupUIListeners : function() {
              var self = this;
              //declare and store the main user xp properties and obervable values
              this.windowWidth            = new czrapp.Value( czrapp.$_window.width() );
              this.isScrolling            = new czrapp.Value( false );
              this.isResizing             = new czrapp.Value( false );
              this.scrollPosition         = new czrapp.Value( czrapp.$_window.scrollTop() );
              this.scrollDirection        = new czrapp.Value('down');
              self.previewDevice          = new czrapp.Value( 'desktop' );

              //PREVIEWED DEVICE ?
              //Listen to the customizer previewed device
              if ( self._isCustomizing() ) {
                    var _setPreviewedDevice = function() {
                          wp.customize.preview.bind( 'previewed-device', function( device ) {
                                self.previewDevice( device );
                          });
                    };
                    if ( wp.customize.preview ) {
                        _setPreviewedDevice();
                    } else {
                          wp.customize.bind( 'preview-ready', function() {
                                _setPreviewedDevice();
                          });
                    }
              }

              //ABSTRACTION LAYER
              var _resizeReact = function( to, from, params ) {
                    params = params || {};
                    if ( params.emulate ) {
                          self.isResizing( true );
                    } else {
                          //Always bail if is not "real" resize.
                          //=> Resize events can be triggered when scrolling on mobile devices, whitout actually resizing the screen
                          self.isResizing( self._isMobile ? Math.abs( from - to ) > 2 : Math.abs( from - to ) > 0 );
                    }
                    clearTimeout( $.data( this, 'resizeTimer') );
                    $.data( this, 'resizeTimer', setTimeout(function() {
                          self.isResizing( false );
                    }, 50 ) );
              };
              //listen to windowWidth
              self.windowWidth.bind( _resizeReact );

              //introduction of a meta czr-resize event, on top of the abstraction layer
              //=> because using the js 'resize' regular event won't trigger our callbacks
              czrapp.$_window.on( 'czr-resize', function() { _resizeReact( null, null, { emulate : true } ); } );

              //"real" horizontal resize reaction : refreshed every 50 ms
              self.isResizing.bind( function( is_resizing ) {
                    czrapp.$_body.toggleClass( 'is-resizing', is_resizing );
              });

              //react when scrolling status change
              //=> auto set it self to false after a while
              this.isScrolling.bind( function( to) {
                    //self.scrollPosition( czrapp.$_window.scrollTop() );
                    czrapp.$_body.toggleClass( 'is-scrolling', to );
                    if ( ! to ) {
                          czrapp.trigger( 'scrolling-finished' );
                          czrapp.$_body.trigger( 'scrolling-finished' );
                    }
              });


              //scroll position is set when scrolling
              this.scrollPosition.bind( function( to, from ) {
                    //handle scrolling classes
                    czrapp.$_body.toggleClass( 'is-scrolled', to > 100 );
                    if ( to <= 50 ) {
                          czrapp.trigger( 'page-scrolled-top', {} );
                    }
                    self.scrollDirection( to >= from ? 'down' : 'up' );
              });


              //BROWSER LAYER : RESIZE AND SCROLL
              //listen to user DOM actions
              czrapp.$_window.on('resize', _.throttle( function() { self.windowWidth( czrapp.$_window.width() ); }, 10 ) );
              czrapp.$_window.on('scroll', _.throttle( function() {
                    self.isScrolling( true );
                    //self.previousScrollPosition = self.scrollPosition() || czrapp.$_window.scrollTop();
                    self.scrollPosition( czrapp.$_window.scrollTop() );
                    clearTimeout( $.data( this, 'scrollTimer') );
                    $.data( this, 'scrollTimer', setTimeout(function() {
                          self.isScrolling( false );
                    }, 100 ) );
              }, 10 ) );
        }
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);