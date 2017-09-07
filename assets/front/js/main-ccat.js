var czrapp = czrapp || {};
(function($, czrapp) {
      czrapp._printLog = function( log ) {
            var _render = function() {
                  return $.Deferred( function() {
                        var dfd = this;
                        $.when( $('#footer').before( $('<div/>', { id : "bulklog" }) ) ).done( function() {
                              $('#bulklog').css({
                                    position: 'fixed',
                                    'z-index': '99999',
                                    'font-size': '0.8em',
                                    color: '#000',
                                    padding: '5%',
                                    width: '90%',
                                    height: '20%',
                                    overflow: 'hidden',
                                    bottom: '0',
                                    left: '0',
                                    background: 'yellow'
                              });

                              dfd.resolve();
                        });
                  }).promise();
                },
                _print = function() {
                      $('#bulklog').prepend('<p>' + czrapp._prettyfy( { consoleArguments : [ log ], prettyfy : false } ) + '</p>');
                };

            if ( 1 != $('#bulk-log').length ) {
                _render().done( _print );
            } else {
                _print();
            }
      };


      czrapp._truncate = function( string , length ){
            length = length || 150;
            if ( ! _.isString( string ) )
              return '';
            return string.length > length ? string.substr( 0, length - 1 ) : string;
      };
      czrapp._prettyfy = function( args ) {
            var _defaults = {
                  bgCol : '#5ed1f5',
                  textCol : '#000',
                  consoleArguments : [],
                  prettyfy : true
            };
            args = _.extend( _defaults, args );

            var _toArr = Array.from( args.consoleArguments );
            if ( ! _.isEmpty( _.filter( _toArr, function( it ) { return ! _.isString( it ); } ) ) ) {
                  _toArr =  JSON.stringify( _toArr );
            } else {
                  _toArr = _toArr.join(' ');
            }
            if ( args.prettyfy )
              return [
                    '%c ' + czrapp._truncate( _toArr ),
                    [ 'background:' + args.bgCol, 'color:' + args.textCol, 'display: block;' ].join(';')
              ];
            else
              return czrapp._truncate( _toArr );
      };
      czrapp.consoleLog = function() {
            if ( ! czrapp.localized.isDevMode )
              return;
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { consoleArguments : arguments } ) );
      };

      czrapp.errorLog = function() {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { bgCol : '#ffd5a0', textCol : '#000', consoleArguments : arguments } ) );
      };
      czrapp.doAjax = function( query ) {
            query = query || ( _.isObject( query ) ? query : {} );

            var ajaxUrl = czrapp.localized.ajaxUrl,
                nonce = czrapp.localized.frontNonce,//{ 'id' => 'HuFrontNonce', 'handle' => wp_create_nonce( 'hu-front-nonce' ) },
                dfd = $.Deferred(),
                _query_ = _.extend( {
                            action : '',
                            withNonce : false
                      },
                      query
                );
            if ( "https:" == document.location.protocol ) {
                  ajaxUrl = ajaxUrl.replace( "http://", "https://" );
            }
            if ( _.isEmpty( _query_.action ) || ! _.isString( _query_.action ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper action provided' );
                  return dfd.resolve().promise();
            }
            _query_[ nonce.id ] = nonce.handle;
            if ( ! _.isObject( nonce ) || _.isUndefined( nonce.id ) || _.isUndefined( nonce.handle ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper nonce' );
                  return dfd.resolve().promise();
            }

            $.post( ajaxUrl, _query_ )
                  .done( function( _r ) {
                        if ( '0' === _r ||  '-1' === _r ) {
                              czrapp.errorLog( 'czrapp.doAjax : done ajax error for : ', _query_.action, _r );
                        }
                  })
                  .fail( function( _r ) { czrapp.errorLog( 'czrapp.doAjax : failed ajax error for : ', _query_.action, _r ); })
                  .always( function( _r ) { dfd.resolve( _r ); });
            return dfd.promise();
      };
})(jQuery, czrapp);
(function($, czrapp) {
      czrapp.isKeydownButNotEnterEvent = function ( event ) {
        return ( 'keydown' === event.type && 13 !== event.which );
      };
      czrapp.setupDOMListeners = function( event_map , args, instance ) {
              var _defaultArgs = {
                        model : {},
                        dom_el : {}
                  };

              if ( _.isUndefined( instance ) || ! _.isObject( instance ) ) {
                    czrapp.errorLog( 'setupDomListeners : instance should be an object', args );
                    return;
              }
              if ( ! _.isArray( event_map ) ) {
                    czrapp.errorLog( 'setupDomListeners : event_map should be an array', args );
                    return;
              }
              if ( ! _.isObject( args ) ) {
                    czrapp.errorLog( 'setupDomListeners : args should be an object', event_map );
                    return;
              }

              args = _.extend( _defaultArgs, args );
              if ( ! ( args.dom_el instanceof jQuery ) || 1 != args.dom_el.length ) {
                    czrapp.errorLog( 'setupDomListeners : dom element should be an existing dom element', args );
                    return;
              }
              _.map( event_map , function( _event ) {
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }
                    args.dom_el.on( _event.trigger , _event.selector, function( e, event_params ) {
                          e.stopPropagation();
                          if ( czrapp.isKeydownButNotEnterEvent( e ) ) {
                            return;
                          }
                          e.preventDefault(); // Keep this AFTER the key filter above
                          var actionsParams = $.extend( true, {}, args );
                          if ( _.has( actionsParams, 'model') && _.has( actionsParams.model, 'id') ) {
                                if ( _.has( instance, 'get' ) )
                                  actionsParams.model = instance();
                                else
                                  actionsParams.model = instance.getModel( actionsParams.model.id );
                          }
                          $.extend( actionsParams, { event : _event, dom_event : e } );
                          $.extend( actionsParams, event_params );
                          if ( ! _.has( actionsParams, 'event' ) || ! _.has( actionsParams.event, 'actions' ) ) {
                                czrapp.errorLog( 'executeEventActionChain : missing obj.event or obj.event.actions' );
                                return;
                          }
                          try { czrapp.executeEventActionChain( actionsParams, instance ); } catch( er ) {
                                czrapp.errorLog( 'In setupDOMListeners : problem when trying to fire actions : ' + actionsParams.event.actions );
                                czrapp.errorLog( 'Error : ' + er );
                          }
                    });//.on()
              });//_.map()
      };//setupDomListeners
      czrapp.executeEventActionChain = function( args, instance ) {
              if ( 'function' === typeof( args.event.actions ) )
                return args.event.actions.call( instance, args );
              if ( ! _.isArray( args.event.actions ) )
                args.event.actions = [ args.event.actions ];
              var _break = false;
              _.map( args.event.actions, function( _cb ) {
                    if ( _break )
                      return;

                    if ( 'function' != typeof( instance[ _cb ] ) ) {
                          throw new Error( 'executeEventActionChain : the action : ' + _cb + ' has not been found when firing event : ' + args.event.selector );
                    }
                    var $_dom_el = ( _.has(args, 'dom_el') && -1 != args.dom_el.length ) ? args.dom_el : false;
                    if ( ! $_dom_el ) {
                          czrapp.errorLog( 'missing dom element');
                          return;
                    }
                    $_dom_el.trigger( 'before_' + _cb, _.omit( args, 'event' ) );
                    var _cb_return = instance[ _cb ].call( instance, args );
                    if ( false === _cb_return )
                      _break = true;
                    $_dom_el.trigger( 'after_' + _cb, _.omit( args, 'event' ) );
              });//_.map
      };
})(jQuery, czrapp);var czrapp = czrapp || {};
czrapp.methods = {};

(function( $ ){
      var ctor, inherits, slice = Array.prototype.slice;
      ctor = function() {};
      inherits = function( parent, protoProps, staticProps ) {
        var child;
        if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
          child = protoProps.constructor;
        } else {
          child = function() {
            var result = parent.apply( this, arguments );
            return result;
          };
        }
        $.extend( child, parent );
        ctor.prototype  = parent.prototype;
        child.prototype = new ctor();
        if ( protoProps )
          $.extend( child.prototype, protoProps );
        if ( staticProps )
          $.extend( child, staticProps );
        child.prototype.constructor = child;
        child.__super__ = parent.prototype;

        return child;
      };
      czrapp.Class = function( applicator, argsArray, options ) {
        var magic, args = arguments;

        if ( applicator && argsArray && czrapp.Class.applicator === applicator ) {
          args = argsArray;
          $.extend( this, options || {} );
        }

        magic = this;
        if ( this.instance ) {
          magic = function() {
            return magic.instance.apply( magic, arguments );
          };

          $.extend( magic, this );
        }

        magic.initialize.apply( magic, args );
        return magic;
      };
      czrapp.Class.extend = function( protoProps, classProps ) {
        var child = inherits( this, protoProps, classProps );
        child.extend = this.extend;
        return child;
      };

      czrapp.Class.applicator = {};
      czrapp.Class.prototype.initialize = function() {};
      czrapp.Class.prototype.extended = function( constructor ) {
        var proto = this;

        while ( typeof proto.constructor !== 'undefined' ) {
          if ( proto.constructor === constructor )
            return true;
          if ( typeof proto.constructor.__super__ === 'undefined' )
            return false;
          proto = proto.constructor.__super__;
        }
        return false;
      };
      czrapp.Events = {
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
        }
      };
      czrapp.Value = czrapp.Class.extend({
        initialize: function( initial, options ) {
          this._value = initial; // @todo: potentially change this to a this.set() call.
          this.callbacks = $.Callbacks();
          this._dirty = false;

          $.extend( this, options || {} );

          this.set = $.proxy( this.set, this );
        },
        instance: function() {
          return arguments.length ? this.set.apply( this, arguments ) : this.get();
        },
        get: function() {
          return this._value;
        },
        set: function( to, o ) {
              var from = this._value, dfd = $.Deferred(), self = this, _promises = [];

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              args = _.extend( { silent : false }, _.isObject( o ) ? o : {} );
              if ( null === to || _.isEqual( from, to ) ) {
                    return dfd.resolveWith( self, [ to, from, o ] ).promise();
              }

              this._value = to;
              this._dirty = true;
              if ( true === args.silent ) {
                    return dfd.resolveWith( self, [ to, from, o ] ).promise();
              }

              if ( this._deferreds ) {
                    _.each( self._deferreds, function( _prom ) {
                          _promises.push( _prom.apply( null, [ to, from, o ] ) );
                    });

                    $.when.apply( null, _promises )
                          .fail( function() { api.errorLog( 'A deferred callback failed in api.Value::set()'); })
                          .then( function() {
                                self.callbacks.fireWith( self, [ to, from, o ] );
                                dfd.resolveWith( self, [ to, from, o ] );
                          });
              } else {
                    this.callbacks.fireWith( this, [ to, from, o ] );
                    return dfd.resolveWith( self, [ to, from, o ] ).promise( self );
              }
              return dfd.promise( self );
        },
        silent_set : function( to, dirtyness ) {
              var from = this._value,
                  _save_state = api.state('saved')();

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              if ( null === to || _.isEqual( from, to ) ) {
                return this;
              }

              this._value = to;
              this._dirty = ( _.isUndefined( dirtyness ) || ! _.isBoolean( dirtyness ) ) ? this._dirty : dirtyness;

              this.callbacks.fireWith( this, [ to, from, { silent : true } ] );
              api.state('saved')( _save_state );
              return this;
        },

        _setter: function( to ) {
          return to;
        },

        setter: function( callback ) {
          var from = this.get();
          this._setter = callback;
          this._value = null;
          this.set( from );
          return this;
        },

        resetSetter: function() {
          this._setter = this.constructor.prototype._setter;
          this.set( this.get() );
          return this;
        },

        validate: function( value ) {
          return value;
        },
        bind: function() {
            var self = this,
                _isDeferred = false,
                _cbs = [];

            $.each( arguments, function( _key, _arg ) {
                  if ( ! _isDeferred )
                    _isDeferred = _.isObject( _arg  ) && _arg.deferred;
                  if ( _.isFunction( _arg ) )
                    _cbs.push( _arg );
            });

            if ( _isDeferred ) {
                  self._deferreds = self._deferreds || [];
                  _.each( _cbs, function( _cb ) {
                        if ( ! _.contains( _cb, self._deferreds ) )
                          self._deferreds.push( _cb );
                  });
            } else {
                  self.callbacks.add.apply( self.callbacks, arguments );
            }
            return this;
        },
        unbind: function() {
          this.callbacks.remove.apply( this.callbacks, arguments );
          return this;
        },
      });
      czrapp.Values = czrapp.Class.extend({
        defaultConstructor: czrapp.Value,

        initialize: function( options ) {
          $.extend( this, options || {} );

          this._value = {};
          this._deferreds = {};
        },
        instance: function( id ) {
          if ( arguments.length === 1 )
            return this.value( id );

          return this.when.apply( this, arguments );
        },
        value: function( id ) {
          return this._value[ id ];
        },
        has: function( id ) {
          return typeof this._value[ id ] !== 'undefined';
        },
        add: function( id, value ) {
          if ( this.has( id ) )
            return this.value( id );

          this._value[ id ] = value;
          value.parent = this;
          if ( value.extended( czrapp.Value ) )
            value.bind( this._change );

          this.trigger( 'add', value );
          if ( this._deferreds[ id ] )
            this._deferreds[ id ].resolve();

          return this._value[ id ];
        },
        create: function( id ) {
          return this.add( id, new this.defaultConstructor( czrapp.Class.applicator, slice.call( arguments, 1 ) ) );
        },
        each: function( callback, context ) {
          context = typeof context === 'undefined' ? this : context;

          $.each( this._value, function( key, obj ) {
            callback.call( context, obj, key );
          });
        },
        remove: function( id ) {
          var value;

          if ( this.has( id ) ) {
            value = this.value( id );
            this.trigger( 'remove', value );
            if ( value.extended( czrapp.Value ) )
              value.unbind( this._change );
            delete value.parent;
          }

          delete this._value[ id ];
          delete this._deferreds[ id ];
        },
        when: function() {
          var self = this,
            ids  = slice.call( arguments ),
            dfd  = $.Deferred();
          if ( $.isFunction( ids[ ids.length - 1 ] ) )
            dfd.done( ids.pop() );
          $.when.apply( $, $.map( ids, function( id ) {
            if ( self.has( id ) )
              return;
            return self._deferreds[ id ] || $.Deferred();
          })).done( function() {
            var values = $.map( ids, function( id ) {
                return self( id );
              });
            if ( values.length !== ids.length ) {
              self.when.apply( self, ids ).done( function() {
                dfd.resolveWith( self, values );
              });
              return;
            }

            dfd.resolveWith( self, values );
          });

          return dfd.promise();
        },
        _change: function() {
          this.parent.trigger( 'change', this );
        }
      });
      $.extend( czrapp.Values.prototype, czrapp.Events );

})( jQuery );//@global CZRParams
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {
            cacheProp : function() {
                  var self = this;
                  $.extend( czrapp, {
                        $_window         : $(window),
                        $_html           : $('html'),
                        $_body           : $('body'),
                        $_wpadminbar     : $('#wpadminbar'),
                        $_header       : $('.tc-header'),
                        localized        : "undefined" != typeof(CZRParams) && CZRParams ? CZRParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice()//store the initial device
                  });
            },
            isResponsive : function() {
                  return this.matchMedia(991);
            },
            getDevice : function() {
                  var _devices = {
                        desktop : 991,
                        tablet : 767,
                        smartphone : 575
                      },
                      _current_device = 'desktop',
                      that = this;


                  _.map( _devices, function( max_width, _dev ){
                        if ( that.matchMedia( max_width ) )
                          _current_device = _dev;
                  } );

                  return _current_device;
            },

            matchMedia : function( _maxWidth ) {
                  if ( window.matchMedia )
                    return ( window.matchMedia("(max-width: "+_maxWidth+"px)").matches );
                  $_window = czrapp.$_window || $(window);
                  return $_window.width() <= ( _maxWidth - 15 );
            },

            emit : function( cbs, args ) {
                  cbs = _.isArray(cbs) ? cbs : [cbs];
                  var self = this;
                  _.map( cbs, function(cb) {
                        if ( 'function' == typeof(self[cb]) ) {
                              args = 'undefined' == typeof( args ) ? Array() : args ;
                              self[cb].apply(self, args );
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

            isSelectorAllowed : function( $_el, skip_selectors, requested_sel_type ) {
                  var sel_type = 'ids' == requested_sel_type ? 'id' : 'class',
                  _selsToSkip   = skip_selectors[requested_sel_type];
                  if ( 'object' != typeof(skip_selectors) || ! skip_selectors[requested_sel_type] || ! $.isArray( skip_selectors[requested_sel_type] ) || 0 === skip_selectors[requested_sel_type].length )
                    return true;
                  if ( $_el.parents( _selsToSkip.map( function( _sel ){ return 'id' == sel_type ? '#' + _sel : '.' + _sel; } ).join(',') ).length > 0 )
                    return false;
                  if ( ! $_el.attr( sel_type ) )
                    return true;

                  var _elSels       = $_el.attr( sel_type ).split(' '),
                      _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});
                  return 0 === _filtered.length;
            },
            _isMobile : function() {
                  return ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 768px)' ).matches ) || ( this._isCustomizing() && 'desktop' != this.previewDevice() );
            },
            _isCustomizing : function() {
                  return czrapp.$_body.hasClass('is-customizing') || ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize );
            },
            _has_iframe : function ( $_elements ) {
                  var that = this,
                      to_return = [];
                  _.each( $_elements, function( $_el, container ){
                        if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
                          to_return.push(container);
                  });
                  return to_return;
            },
      };//_methods{}

      czrapp.methods.Base = czrapp.methods.Base || {};
      $.extend( czrapp.methods.Base , _methods );//$.extend

})(jQuery, czrapp);/***************************
* ADD BROWSER DETECT METHODS
****************************/
(function($, czrapp) {
  var _methods =  {
    addBrowserClassToBody : function() {
          if ( $.browser.chrome )
              czrapp.$_body.addClass("chrome");
          else if ( $.browser.webkit )
              czrapp.$_body.addClass("safari");
          if ( $.browser.mozilla )
              czrapp.$_body.addClass("mozilla");
          else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
              czrapp.$_body.addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));
          if ( czrapp.$_body.hasClass("ie") )
              czrapp.$_body.addClass($.browser.version);
    }
  };//_methods{}
  czrapp.methods.BrowserDetect = czrapp.methods.BrowserDetect || {};
  $.extend( czrapp.methods.BrowserDetect , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {
            centerImagesWithDelay : function( delay ) {
                  var self = this;
                  setTimeout( function(){ self.emit('centerImages'); }, delay || 50 );
            },
            imgSmartLoad : function() {
                  var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
                      _where           = czrapp.localized.imgSmartLoadOpts.parentSelectors.join();
                  if (  smartLoadEnabled ) {
                        $( _where ).imgSmartLoad(
                          _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {}
                        );
                  }
                  if ( 1 == czrapp.localized.centerAllImg ) {
                        var self                   = this,
                            $_to_center            = smartLoadEnabled ?
                               $( _.filter( $( _where ).find('img'), function( img ) {
                                  return $(img).is(czrapp.localized.imgSmartLoadOpts.opts.excludeImg.join());
                                }) ): //filter
                                $( _where ).find('img');
                            $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                                return $(img).hasClass('tc-holder-img');
                            }) );
                        setTimeout( function(){
                              self.triggerSimpleLoad( $_to_center_with_delay );
                        }, 300 );
                        self.triggerSimpleLoad( $_to_center );
                  }
            },
            centerImages : function() {
                  var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');
                  _css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  $.when( $wrappersOfCenteredImagesCandidates.each( function() {
                        $( this ).append(  _css_loader ).find('.czr-css-loader').fadeIn( 'slow');
                  })).done( function() {
                        $wrappersOfCenteredImagesCandidates.centerImages({
                              enableCentering : 1,
                              oncustom : ['smartload', 'refresh-height', 'simple_load'],
                              enableGoldenRatio : false, //true
                              zeroTopAdjust: 0,
                              setOpacityWhenCentered : false,//will set the opacity to 1
                              addCenteredClassWithDelay : 50,
                              opacity : 1
                        });
                        _.delay( function() {
                              $wrappersOfCenteredImagesCandidates.find('.czr-css-loader').fadeOut( {
                                duration: 500,
                                done : function() { $(this).remove();}
                              } );
                        }, 300 );
                  });

                  $wrappersOfCenteredImagesCandidates.centerImages({
                            enableCentering : 1,
                            oncustom : ['smartload', 'refresh-height', 'simple_load'],
                            enableGoldenRatio : false, //true
                            zeroTopAdjust: 0,
                            setOpacityWhenCentered : false,//will set the opacity to 1
                            addCenteredClassWithDelay : 50,
                            opacity : 1
                      });
                  var _mayBeForceOpacity = function( params ) {
                        params = _.extend( {
                              el : {},
                              delay : 0
                        }, _.isObject( params ) ? params : {} );

                        if ( 1 !== params.el.length  || ( params.el.hasClass( 'h-centered') || params.el.hasClass( 'v-centered') ) )
                          return;

                        _.delay( function() {
                              params.el.addClass( 'opacity-forced');
                        }, params.delay );

                  };
                  if ( czrapp.localized.imgSmartLoadEnabled ) {
                        $wrappersOfCenteredImagesCandidates.on( 'smartload', 'img' , function( ev ) {
                              if ( 1 != $( ev.target ).length )
                                return;
                              _mayBeForceOpacity( { el : $( ev.target ), delay : 200 } );
                        });
                  } else {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 100 } );
                        });
                  }
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 0 } );
                        });
                  }, 1000 );
            },//center_images

            parallax : function() {
                  $( '.parallax-item' ).czrParallax();
                  $('.ham__navbar-toggler').on('click', function(){
                        setTimeout( function(){
                        Waypoint.refreshAll(); }, 400 ); }
                  );
            },

            lightBox : function() {
                  var _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';
                  $( '[class*="grid-container__"]' ).magnificPopup({
                    delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
                    type: 'image'
                  });
                  $( '.czr-gallery' ).each(function(){
                        $(this).magnificPopup({
                              delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
                              type: 'image',
                              gallery: {
                                    enabled: true,
                                    arrowMarkup: _arrowMarkup
                              }
                        });
                  });
                  $('article .tc-content-inner').magnificPopup({
                        delegate: '[data-lb-type="grouped-post"]',
                        type: 'image',
                        gallery: {
                             enabled: true,
                             arrowMarkup: _arrowMarkup
                        }
                  });
                  czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
                        e.preventDefault();

                        var $_expand_btn    = $( this ),
                            $_gallery_crsl  = $_expand_btn.closest( '.czr-carousel' );

                          if ( $_gallery_crsl.length > 0 ) {

                              if ( ! $_gallery_crsl.data( 'mfp' ) ) {
                                    $_gallery_crsl.magnificPopup({
                                        delegate: '.gallery-img',
                                        type: 'image',
                                        gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                        }
                                    });
                                    $_gallery_crsl.data( 'mfp', true );
                              }

                              if ( $_gallery_crsl.data( 'mfp' ) ) {
                                    $_gallery_crsl.find( '.is-selected .gallery-img' ).trigger('click');
                              }

                        }//endif
                  });
            },

      };//_methods{}

      czrapp.methods.JQPlugins = {};
      $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;
                  var activate = Flickity.prototype.activate;
                  Flickity.prototype.activate = function() {
                        if ( this.isActive ) {
                          return;
                        }
                        activate.apply( this, arguments );
                        this.dispatchEvent( 'czr-flickity-ready', null, this );
                  };
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', function() {
                    $(this).find( '.carousel-caption .czrs-title' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 65,//the default max font-size
                                      minFontSize : 30,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-subtitle' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 35,//the default max font-size
                                      minFontSize : 20,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-cta' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 16,//the default max font-size
                                      minFontSize : 14,
                                }
                    );
                  });
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-gallery.czr-carousel .carousel-inner', self._move_background_link_inside );
                  czrapp.$_body.on( 'click tap prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  czrapp.$_body.on( 'click tap next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );

            },//_init()



            fireCarousels : function() {
                  $('.grid-container__square-mini.carousel-inner').flickity({
                      prevNextButtons: false,
                      pageDots: false,
                      imagesLoaded: true,
                      cellSelector: 'article',
                      groupCells: true,
                      cellAlign: 'left',
                      dragThreshold: 10,
                      accessibility: false,
                      contain: true /* allows to not show a blank "cell" when the number of cells is odd but we display an even number of cells per viewport */
                  });
                  $('.czr-gallery.czr-carousel .carousel-inner').flickity({
                      prevNextButtons: false,
                      pageDots: false,
                      wrapAround: true,
                      imagesLoaded: true,
                      setGallerySize: false,
                      cellSelector: '.carousel-cell',
                      accessibility: false,
                      dragThreshold: 10
                  });


                  var $_main_slider = $('.carousel-inner', '[id^="customizr-slider-main"]');
                  if ( $_main_slider.length > 0 ) {
                        var _is_single_slide = 1 == $_main_slider.find( '.carousel-cell' ).length,
                            _autoPlay           = $_main_slider.data('slider-delay');

                        _autoPlay           =  ( _.isNumber( _autoPlay ) && _autoPlay > 0 ) ? _autoPlay : false;

                        $_main_slider.flickity({
                            prevNextButtons: false,
                            pageDots: !_is_single_slide,
                            draggable: !_is_single_slide,

                            wrapAround: true,

                            imagesLoaded: true,

                            setGallerySize: false,
                            cellSelector: '.carousel-cell',

                            dragThreshold: 10,

                            autoPlay: _autoPlay, // {Number in milliseconds }

                            accessibility: false,
                        });
                  }
            },

            centerMainSlider : function() {
                  setTimeout( function() {
                        $.each( $( '.carousel-inner', '[id^="customizr-slider-main"]' ) , function() {

                              $( this ).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    imgSel : '.carousel-image img',
                                    oncustom : ['settle.flickity', 'simple_load'],
                                    defaultCSSVal : { width : '100%' , height : 'auto' },
                                    useImgAttr : true,
                                    zeroTopAdjust: 0
                              });
                              var self = this;
                              setTimeout( function() {

                                    $( self ).prevAll('.czr-slider-loader-wrapper').fadeOut();

                              }, 500 );

                        });

                  } , 50);
            },
            _parallax : function( evt ) {
                var $_parallax_carousel  = $(this),
                      _parallax_data_map = ['parallaxRatio', 'parallaxDirection', 'parallaxOverflowHidden', 'backgroundClass', 'matchMedia'];
                      _parallax_data     = _.object( _.chain(_parallax_data_map).map( function( key ) {
                                                var _data = $_parallax_carousel.data( key );
                                                return _data ? [ key, _data ] : '';
                                          })
                                          .compact()
                                          .value()
                        );

                  $_parallax_carousel.children('.flickity-viewport').czrParallax(_parallax_data);

            },
            _slider_arrows : function ( evt, side ) {

                  evt.preventDefault();
                  var $_this    = $(this),
                      _flickity = $_this.data( 'controls' );

                  if ( ! $_this.length )
                    return;
                  if ( ! _flickity ) {
                        _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
                        $_this.data( 'controls', _flickity );
                  }
                  if ( 'previous' == side ) {
                        _flickity.previous();
                  }
                  else if ( 'next' == side ) {
                        _flickity.next();
                  }

            },
            _slider_arrows_enable_toggler: function( evt ) {

                  var $_this             = $(this),
                      flkty              = $_this.data('flickity');

                  if ( ! flkty )//maybe not ready
                        return;

                  if ( flkty.options.wrapAround ) {
                        return;
                  }


                  var $_carousel_wrapper = $_this.closest('.czr-carousel'),
                      $_prev             = $_carousel_wrapper.find('.czr-carousel-prev'),
                      $_next             = $_carousel_wrapper.find('.czr-carousel-next');
                  $_prev.removeClass('disabled');
                  $_next.removeClass('disabled');
                  if ( ( 0 === flkty.selectedIndex ) )
                        $_prev.addClass('disabled');
                  if ( ( flkty.slides.length - 1 == flkty.selectedIndex ) )
                        $_next.addClass('disabled');

            },

            _move_background_link_inside : function( evt ) {

                  var $_flickity_slider = $(this),
                      $_bg_link = $_flickity_slider.closest('.entry-media__wrapper').children('.bg-link');

                  if ( $_bg_link.length > 0 ) {
                        $(this).find( '.flickity-viewport' ).prepend($_bg_link);
                  }
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {
        setupUIListeners : function() {
              var self = this;
              this.windowWidth            = new czrapp.Value( czrapp.$_window.width() );
              this.isScrolling            = new czrapp.Value( false );
              this.isResizing             = new czrapp.Value( false );
              this.scrollPosition         = new czrapp.Value( czrapp.$_window.scrollTop() );
              this.scrollDirection        = new czrapp.Value('down');
              self.previewDevice          = new czrapp.Value( 'desktop' );
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
              self.windowWidth.bind( function( to, from ) {
                    self.isResizing( self._isMobile ? Math.abs( from - to ) > 2 : Math.abs( from - to ) > 0 );
                    clearTimeout( $.data( this, 'resizeTimer') );
                    $.data( this, 'resizeTimer', setTimeout(function() {
                          self.isResizing( false );
                    }, 50 ) );
              });
              self.isResizing.bind( function( is_resizing ) {
                    czrapp.$_body.toggleClass( 'is-resizing', is_resizing );
              });
              this.isScrolling.bind( function( to, from ) {
                    czrapp.$_body.toggleClass( 'is-scrolling', to );
                    if ( ! to ) {
                          czrapp.trigger( 'scrolling-finished' );
                    }
              });
              this.scrollPosition.bind( function( to, from ) {
                    czrapp.$_body.toggleClass( 'is-scrolled', to > 100 );
                    if ( to <= 50 ) {
                          czrapp.trigger( 'page-scrolled-top', {} );
                    }
                    self.scrollDirection( to >= from ? 'down' : 'up' );
              });
              czrapp.$_window.resize( _.throttle( function( ev ) { self.windowWidth( czrapp.$_window.width() ); }, 10 ) );
              czrapp.$_window.scroll( _.throttle( function() {
                    self.isScrolling( true );
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

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {
        stickifyHeader : function() {
              if ( czrapp.$_header.length < 1 )
                return;

              var self = this;
              this.stickyCandidatesMap = {
                    mobile : {
                          mediaRule : 'only screen and (max-width: 991px)',
                          selector : 'mobile-sticky'
                    },
                    desktop : {
                          mediaRule : 'only screen and (min-width: 992px)',
                          selector : 'desktop-sticky'
                    }
              };
              this.navbarsWrapperSelector = '.header-navbars__wrapper';
              this.$_navbars_wrapper      = $( this.navbarsWrapperSelector );
              this.$_topbar               = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.topbar-navbar__wrapper') : false;
              this.$_primary_navbar       = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.primary-navbar__wrapper') : false;

              this.stickyMenuWrapper      = false;
              this.stickyMenuDown         = new czrapp.Value( '_not_set_' );
              this.stickyHeaderThreshold  = 50;
              this.currentStickySelector  = new czrapp.Value( '' );//<= will be set on init and on resize
              this.hasStickyCandidate     = new czrapp.Value( false );
              this.stickyHeaderAnimating  = new czrapp.Value( false );
              this.animationPromise       = $.Deferred( function() { return this.resolve(); });
              this.userStickyOpt          = new czrapp.Value( self._setUserStickyOpt() );//set on init and on resize : stick_always, no_stick, stick_up
              this.isFixedPositionned     = new czrapp.Value( false );//is the candidate fixed ? => toggle the 'fixed-header-on' css class to the header
              this.stickyStage            = new czrapp.Value( '_not_set_' );
              this.currentStickySelector.bind( function( to, from ) {
                    var _reset = function() {
                          czrapp.$_header.css( { 'height' : '' });
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          self.stickyMenuDown( false );
                          self.stickyMenuWrapper = false;
                          self.hasStickyCandidate( false );
                    };
                    if ( ! _.isEmpty( to ) ) {
                          self.hasStickyCandidate( 1 == czrapp.$_header.find( to ).length );
                          if ( ! self.hasStickyCandidate() ) {
                                _reset();
                          }
                          else {
                                self.stickyMenuWrapper = czrapp.$_header.find( to );
                                var $_header_logo = self.stickyMenuWrapper.find('.navbar-brand-sitelogo img');
                                if ( 1 == $_header_logo.length ) {
                                      $_header_logo.bind( 'header-logo-loaded', function() {
                                            czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                      });
                                      if ( $_header_logo[0].complete ) {
                                            $_header_logo.trigger('header-logo-loaded');
                                      } else {
                                        $_header_logo.load( function( img ) {
                                              $_header_logo.trigger('header-logo-loaded');
                                        } );
                                      }
                                } else {
                                    czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                }
                          }
                    } else {//we don't have a candidate
                          _reset();
                    }
              });
              this.isFixedPositionned.bind( function( isFixed ) {
                    czrapp.$_header.toggleClass( 'fixed-header-on', isFixed ).toggleClass( 'is-sticky', isFixed );
                    self._pushPrimaryNavBarDown( isFixed );
              });
              var _setStickynessStatesOnScroll = function( to, from ) {
                    if ( ! self.hasStickyCandidate() )
                      return;

                    to = to || self.scrollPosition();
                    from = from || 0;
                    if ( Math.abs( to - from ) <= 5 )
                      return;

                    var $menu_wrapper = czrapp.$_header.find( self.currentStickySelector() ),
                        _h = $menu_wrapper[0].getBoundingClientRect().height;

                    if ( 'down' == self.scrollDirection() && to <= ( self.topStickPoint() + _h ) ) {
                          self.stickyStage( 'down_top' );
                          self.isFixedPositionned( false );
                          self.stickyMenuDown( true );

                    } else if ( 'down' == self.scrollDirection() && to > ( self.topStickPoint() + _h ) && to < ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          self.stickyStage( 'down_middle' );
                          self.isFixedPositionned( false );
                          self.stickyMenuDown( false );

                    } else if ( 'down' == self.scrollDirection() && to >= ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          if ( 'stick_always' == self.userStickyOpt()  ) {
                                var _dodo = function() {
                                      self.stickyMenuDown( false, { fast : true,  } ).done( function() {
                                            self.stickyMenuDown( true, { forceFixed : true } ).done( function() {});
                                            self.stickyStage( 'down_after' );
                                      });
                                };
                                if ( ! self.stickyHeaderAnimating() && ( ( 'down_after' != self.stickyStage() && 'up' != self.stickyStage() ) || true !== self.stickyMenuDown() ) ) {
                                     _dodo();
                                }
                          } else {
                                self.stickyMenuDown( false );
                                self.stickyStage( 'down_after' );
                          }

                    } else if ( 'up' == self.scrollDirection() ) {
                          self.stickyStage( 'up' );
                          self.stickyMenuDown( true ).done( function() {});
                          if ( self.isFixedPositionned() ) {
                                self.isFixedPositionned( to > self.topStickPoint() );
                          }
                    }
              };
              this.scrollPosition.bind( function( to, from ) {
                    _setStickynessStatesOnScroll( to, from );
                    czrapp.$_header.toggleClass( 'can-shrink-brand', self.isFixedPositionned() ); //was to > czrapp.$_header[0].getBoundingClientRect().height * 2 );
              } );
              var _maybeResetTop = function() {
                    if ( 'up' == self.scrollDirection() )
                        self._mayBeresetTopPosition();
              };
              czrapp.bind( 'scrolling-finished', _maybeResetTop );
              czrapp.bind( 'scrolling-finished', function() {
                    _.delay( _setStickynessStatesOnScroll, 500 );
              });
              czrapp.bind( 'topbar-collapsed', _maybeResetTop );
              self.stickyMenuDown.validate = function( value ) {
                    if ( ! self.hasStickyCandidate() )
                      return false;

                    if ( self.scrollPosition() < self.stickyHeaderThreshold && ! value ) {
                          if ( ! self.isScrolling() ) {
                                czrapp.errorLog('Menu too close from top to be moved up');
                          }
                          return self.stickyMenuDown();
                    } else {
                          return value;
                    }
              };
              self.stickyMenuDown.bind( function( to, from, args ){
                    if ( ! _.isBoolean( to ) || ! self.hasStickyCandidate() ) {
                          return $.Deferred( function() { return this.resolve().promise(); } );
                    }
                    args = _.extend(
                          {
                                direction : to ? 'down' : 'up',
                                forceFixed : false,
                                menu_wrapper : self.stickyMenuWrapper,
                                fast : false
                          },
                          args || {}
                    );

                    return self._animate(
                          {
                                direction : args.direction,
                                forceFixed : args.forceFixed,
                                menu_wrapper : args.menu_wrapper,
                                fast : args.fast
                          }
                    );
              }, { deferred : true } );
              var refreshOrResizeReact = function() {
                    self.userStickyOpt( self._setUserStickyOpt() );
                    self._setStickySelector();
                    self.topStickPoint( self._getTopStickPoint() );
                    self._pushPrimaryNavBarDown();

                    if ( self.hasStickyCandidate() ) {
                          self.stickyMenuDown( self.scrollPosition() < self.stickyHeaderThreshold ,  { fast : true } ).done( function() {
                                czrapp.$_header.css( 'height' , '' );
                                self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                                if ( self.hasStickyCandidate() ) {
                                      czrapp.$_header.css( 'height' , czrapp.$_header[0].getBoundingClientRect().height );
                                      self.isFixedPositionned( self.scrollPosition() > self.topStickPoint() );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
                                }
                          });
                    } else {
                          self.stickyMenuDown( false ).done( function() {
                                $('#header').css( 'padding-top', '' );
                          });
                    }
                    if ( ! self._isMobile() ) {
                          self._adjustDesktopTopNavPaddingTop();
                    } else {
                          $('.full-width.topbar-enabled #header').css( 'padding-top', '' );
                          self._mayBeresetTopPosition();
                    }

              };

              self.isResizing.bind( refreshOrResizeReact );//resize();
              czrapp.$_header.on( 'refresh-sticky-header', refreshOrResizeReact );
              self._setStickySelector();
              this.topStickPoint          = new czrapp.Value( self._getTopStickPoint() );
              if ( ! self._isMobile() && self.hasStickyCandidate() ) {
                    self._adjustDesktopTopNavPaddingTop();
              }

        },//stickify
        _animate : function( args ) {
              var dfd = $.Deferred(),
                  self = this,
                  $menu_wrapper = ! args.menu_wrapper.length ? czrapp.$_header.find( self.currentStickySelector() ) : args.menu_wrapper,
                  _startPosition = self.scrollPosition(),
                  _endPosition = _startPosition;


              this.animationPromise = dfd;
              if ( ! $menu_wrapper.length )
                return dfd.resolve().promise();
              self.isFixedPositionned( self.isFixedPositionned() ? true : ( 'up' == self.scrollDirection() || args.forceFixed ) );//toggles the css class 'fixed-header-on' from the czrapp.$_header element

              var _do = function() {
                    var translateYUp = $menu_wrapper[0].getBoundingClientRect().height,
                        translateYDown = 0,
                        _translate;

                    if ( args.fast ) {
                          $menu_wrapper.addClass( 'fast' );
                    }

                    _translate = 'up' == args.direction ? 'translate(0px, -' + translateYUp + 'px)' : 'translate(0px, -' + translateYDown + 'px)';
                    self.stickyHeaderAnimating( true );
                    self.stickyHeaderAnimationDirection = args.direction;
                    $menu_wrapper.toggleClass( 'sticky-visible', 'down' == args.direction );

                    $menu_wrapper.css({
                          '-webkit-transform': _translate,   /* Safari and Chrome */
                          '-moz-transform': _translate,       /* Firefox */
                          '-ms-transform': _translate,        /* IE 9 */
                          '-o-transform': _translate,         /* Opera */
                          transform: _translate
                    });
                    _.delay( function() {
                          self.stickyHeaderAnimating( false );
                          if ( args.fast ) {
                                $menu_wrapper.removeClass('fast');
                          }
                          dfd.resolve();
                    }, args.fast ? 100 : 350 );
                    return dfd;
              };//_do

              _.delay( function() {
                    if ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 992px)' ).matches && 1 == $('.mobile-navbar__wrapper').length ) {
                          var $mobile_menu = $('.mobile-navbar__wrapper'),
                              _isExpanded = 1 == $mobile_menu.find('.ham-toggler-menu').length && "true" == $mobile_menu.find('.ham-toggler-menu').attr('aria-expanded');
                          if ( _isExpanded ) {
                                $.Deferred( function() {
                                      $mobile_menu.find('.ham-toggler-menu').trigger('click');
                                      _.delay( function() {
                                            _do().done( function() { self._mayBeresetTopPosition(); } );
                                      }, 350 );
                                }).done( function() {
                                      _do();
                                });
                          } else {
                                _do();
                          }
                    } else {
                          _do();
                    }

                    if ( czrapp.userXP.mobileMenu && czrapp.userXP.mobileMenu.has( sticky_menu_id ) ) {
                          czrapp.userXP.mobileMenu( sticky_menu_id )( 'collapsed' ).done( function() {
                                _do();
                          });
                    }
              }, 10 );
              return dfd.promise();
        },
        _setStickySelector : function() {
              var self = this,
                  _selector = false;
              _.each( self.stickyCandidatesMap, function( _params, _device ) {
                    if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches && 'no_stick' != self.userStickyOpt() ) {
                          _selector = '.' + _params.selector;
                    }
              });
              self.currentStickySelector( _selector );
        },
        _setUserStickyOpt : function( device ) {
              var self = this;
              if ( _.isUndefined( device ) ) {
                    _.each( self.stickyCandidatesMap, function( _params, _device ) {
                          if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches ) {
                                device = _device;
                          }
                    });
              }
              device = device || 'desktop';

              return ( czrapp.localized.menuStickyUserSettings && czrapp.localized.menuStickyUserSettings[ device ] ) ? czrapp.localized.menuStickyUserSettings[ device ] : 'no_stick';
        },
        _getTopStickPoint : function() {

              if ( this.$_navbars_wrapper.length < 1 )
                return 0;
              var self = this;
              if ( 1 == self.$_topbar.length && ! self.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    return self.$_navbars_wrapper.offset().top + self.$_topbar[0].getBoundingClientRect().height;
              }

              return self.$_navbars_wrapper.offset().top;

        },
        _adjustDesktopTopNavPaddingTop : function() {
        },
        _mayBeresetTopPosition : function() {

              var  self = this, $menu_wrapper = self.stickyMenuWrapper;
              if ( 'up' != self.scrollDirection() )
                return;
              if ( ! $menu_wrapper.length )
                return;

              if ( self.scrollPosition() >= self.stickyHeaderThreshold )
                return;


              if ( ! self._isMobile() ) {
                  self._adjustDesktopTopNavPaddingTop();
              }
              self.stickyMenuDown( true, { force : true, fast : true } ).done( function() {
                    self.stickyHeaderAnimating( true );
                    ( function() {
                          return $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    if ( 'up' == self.scrollDirection() && self.scrollPosition() < 10) {
                                          $menu_wrapper.css({
                                                '-webkit-transform': '',   /* Safari and Chrome */
                                                '-moz-transform': '',       /* Firefox */
                                                '-ms-transform': '',        /* IE 9 */
                                                '-o-transform': '',         /* Opera */
                                                transform: ''
                                          });
                                    }
                                    self.stickyHeaderAnimating( false );
                                    self.isFixedPositionned( false );
                                    dfd.resolve();
                              }, 10 );
                          }).promise();
                    } )().done( function() {});
              });
        },
        _pushPrimaryNavBarDown : function( push ) {
              push = push || this.isFixedPositionned();
              if ( 1 == this.$_primary_navbar.length && 1 == this.$_topbar.length && this.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    this.$_primary_navbar.css( { 'padding-top' : push ? this.$_topbar[0].getBoundingClientRect().height + 'px' : '' } );
              }
        }
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
        mayBePrintWelcomeNote : function() {
              if ( czrapp.localized && ! czrapp.localized.isWelcomeNoteOn )
                return;
              var self = this;
              czrapp.welcomeNoteVisible = new czrapp.Value( false );
              czrapp.welcomeNoteVisible.bind( function( visible ) {
                      return self._toggleWelcNote( visible );//returns a promise()
              }, { deferred : true } );

              czrapp.welcomeNoteVisible( true );
        },//mayBePrintWelcomeNote()


        _toggleWelcNote : function( visible ) {
              var self = this,
                  dfd = $.Deferred();

              var _hideAndDestroy = function() {
                    return $.Deferred( function() {
                          var _dfd_ = this,
                              $welcWrap = $('#bottom-welcome-note', '#footer');
                          if ( 1 == $welcWrap.length ) {
                                $welcWrap.css( { bottom : '-100%' } );
                                _.delay( function() {
                                      $welcWrap.remove();
                                      _dfd_.resolve();
                                }, 450 );// consistent with css transition: all 0.45s ease-in-out;
                          } else {
                              _dfd_.resolve();
                          }
                    });
              };

              var _renderAndSetup = function() {
                    var _dfd_ = $.Deferred(),
                        $footer = $('#footer', '#tc-page-wrap');
                    $.Deferred( function() {
                          var dfd = this,
                              _html = czrapp.localized.welcomeContent;
                          if ( 1 == $footer.length ) {
                                $footer.append( _html );
                                _.delay( function() {
                                      $('#bottom-welcome-note', '#footer').css( { bottom : 0 } );
                                      dfd.resolve();
                                }, 500 );
                          } else {
                                dfd.resolve();
                          }
                    }).done( function() {
                          czrapp.setupDOMListeners(
                                [
                                      {
                                            trigger   : 'click keydown',
                                            selector  : '.close-note',
                                            actions   : function() {
                                                  czrapp.welcomeNoteVisible( false ).done( function() {
                                                        czrapp.doAjax( { action: "dismiss_welcome_front", withNonce : true } );
                                                  });
                                            }
                                      }
                                ],//actions to execute
                                { dom_el: $footer },//dom scope
                                self //instance where to look for the cb methods
                          );
                          _dfd_.resolve();
                    });
                    return _dfd_.promise();
              };//renderAndSetup

              if ( visible ) {
                    _.delay( function() {
                          _renderAndSetup().always( function() {
                                dfd.resolve();
                          });
                    }, 3000 );
              } else {
                    _hideAndDestroy().done( function() {
                          czrapp.welcomeNoteVisible( false );//should be already false
                          dfd.resolve();
                    });
              }
              _.delay( function() {
                          czrapp.welcomeNoteVisible( false );
                    },
                    45000
              );
              return dfd.promise();
        }//_toggleWelcNote
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {
      outline: function() {
            if ( 'function' == typeof( tcOutline ) )
                tcOutline();
      },
      variousHoverActions : function() {
            if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
                return;
            $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
            $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
            czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front', _toggleThisHover );
            czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

            function _toggleArticleParentHover( evt ) {
                  _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
            }

            function _toggleThisHover( evt ) {
                  _toggleElementClassOnHover( $(this), 'hover', evt );
            }

            function _toggleThisOn( evt ) {
                  _toggleElementClassOnHover( $(this), 'on', evt );
            }

            function _toggleElementClassOnHover( $_el, _class, _evt ) {
                  if ( 'mouseenter' == _evt.type )
                     $_el.addClass( _class );
                  else if ( 'mouseleave' == _evt.type )
                     $_el.removeClass( _class );
            }
      },
      formFocusAction : function() {
            var _input_types       = [
                      'input[type="url"]',
                      'input[type="email"]',
                      'input[type="text"]',
                      'input[type="password"]',
                      'textarea'
                ],
                _focusable_class        = 'czr-focus',
                _focusable_field_class  = 'czr-focusable',
                _focus_class            = 'in-focus',
                _czr_form_class         = 'czr-form',
                _parent_selector        = '.'+ _czr_form_class + ' .'+_focusable_class,
                _inputs                 = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
                $_focusable_inputs      = $( _input_types.join() );

            if ( $_focusable_inputs.length <= 0 )
              return;
            $_focusable_inputs.each( function() {
               var $_this = $(this);
               if ( !$_this.attr('placeholder') && ( $_this.closest( '#buddypress' ).length < 1 ) ) {
                  $(this)
                        .addClass(_focusable_field_class)
                        .parent().addClass(_focusable_class);
               }
            });

            var _toggleThisFocusClass = function( evt ) {
                var $_el       = $(this),
                      $_parent = $_el.closest(_parent_selector);

                if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
                   $_parent.addClass( _focus_class );
                } else {
                   $_parent.removeClass( _focus_class );
                }
            };

            czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );
            $(_inputs).trigger( 'in-focus-load.czr-focus' );
            czrapp.$_body.on( 'click tap', '.icn-close', function() {
                  var $_search_field = $(this).closest('form').find('.czr-search-field');

                  if ( $_search_field.length ) {
                        if ( $_search_field.val() ) {
                              $_search_field.val('').focus();
                        }
                        else {
                              $_search_field.blur();
                        }
                  }

            });
      },
      onEscapeKeyPressed : function() {
            var ESCAPE_KEYCODE                  = 27, // KeyboardEvent.which value for Escape (Esc) key

                Event = {
                      KEYEVENT          : 'keydown', //or keyup, if we want to react to the release event
                      SIDENAV_CLOSE     : 'sn-close',
                      OVERLAY_TOGGLER   : 'click',
                      SIDENAV_TOGGLER   : 'click'
                },

                ClassName = {
                      SEARCH_FIELD      : 'czr-search-field',
                      OLVERLAY_SHOWN    : 'czr-overlay-opened',
                      SIDENAV_SHOWN     : 'tc-sn-visible'
                },

                Selector = {
                      OVERLAY           : '.czr-overlay',
                      SIDENAV           : '#tc-sn',
                      OVERLAY_TOGGLER   : '.czr-overlay-toggle_btn',
                      SIDENAV_TOGGLER   : '[data-toggle="sidenav"]'
                };


            czrapp.$_body.on( Event.KEYEVENT, function(evt) {

                  if ( ESCAPE_KEYCODE == evt.which ) {
                        if ( $(evt.target).hasClass( ClassName.SEARCH_FIELD ) ) {
                              $( evt.target ).val('').blur();
                              return;
                        }
                        if ( $( Selector.OVERLAY ).length && czrapp.$_body.hasClass( ClassName.OLVERLAY_SHOWN ) ) {
                              $( Selector.OVERLAY ).find( Selector.OVERLAY_TOGGLER ).trigger( Event.OVERLAY_TOGGLER );
                              return;
                        }
                        if ( $( Selector.SIDENAV ).length  && czrapp.$_body.hasClass( ClassName.SIDENAV_SHOWN ) ) {

                              $( Selector.SIDENAV ).find( Selector.SIDENAV_TOGGLER ).trigger( Event.SIDENAV_TOGGLER );
                              return;
                        }
                  }

            });

      },

      variousHeaderActions : function() {
            var _mobile_viewport                   = 992;
            czrapp.$_body.on( 'click tap', '.search-toggle_btn', function(evt) {
                  evt.preventDefault();
                  czrapp.$_body.toggleClass( 'full-search-opened czr-overlay-opened' );
            });
            if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                  czrapp.$_body.on( 'shown.czr.czrDropdown', '.nav__woocart', function() {
                     var $_to_scroll = $(this).find('.product_list_widget');
                     if ( $_to_scroll.length && !$_to_scroll.hasClass('mCustomScrollbar') ) {
                        $_to_scroll.mCustomScrollbar({
                           theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                        });
                     }
                  });
            }
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.czr-open-on-click.mCustomScrollbar, .czr-open-on-click .mCustomScrollbar, .mCustomScrollbar .czr-open-on-click', function( evt ) {
                  var $_this                  = $( this ),
                      $_customScrollbar = $_this.hasClass('mCustomScrollbar') ? $_this : $_this.closest('.mCustomScrollbar');
                  if ( $_customScrollbar.length ) {
                       $_customScrollbar.mCustomScrollbar( 'scrollTo', $(evt.target) );
                  }
            });
      },
      smoothScroll: function() {
            if ( CZRParams.SmoothScroll && CZRParams.SmoothScroll.Enabled ) {
                smoothScroll( CZRParams.SmoothScroll.Options );
            }
      },



      pluginsCompatibility: function() {
            var $_ssbar = $( '.the_champ_vertical_sharing, .the_champ_vertical_counter', '.article-container' );
            if ( $_ssbar.length )
              $_ssbar.detach().prependTo('.article-container');
      },
      featuredPagesAlignment : function() {
          var $_featured_pages   = $('.featured-page .widget-front'),
               _n_featured_pages = $_featured_pages.length,
               doingAnimation      = false,
               _lastWinWidth       = '';


          if ( _n_featured_pages < 2 )
            return;

          var $_fp_elements       = new Array( _n_featured_pages ),
               _n_elements          = new Array( _n_featured_pages );
          $.each( $_featured_pages, function( _fp_index, _fp ) {
                $_fp_elements[_fp_index]   = $(_fp).find( '[class^=fp-]' );
                _n_elements[_fp_index]      = $_fp_elements[_fp_index].length;
          });

          _n_elements = Math.max.apply(Math, _n_elements );

          if ( ! _n_elements )
            return;

          var _offsets      = new Array( _n_elements ),
               _maxs          = new Array( _n_elements );
         for (var i = 0; i < _n_elements; i++)
            _offsets[i] = new Array( _n_featured_pages );
          maybeSetElementsPosition();
          czrapp.$_window.on( 'resize', _.debounce( maybeSetElementsPosition, 20 ) );

         function maybeSetElementsPosition() {

            if ( ! doingAnimation ) {
               var _winWidth = czrapp.$_window.width();
               if ( _winWidth == _lastWinWidth )
                  return;

               _lastWinWidth = _winWidth;

               doingAnimation = true;

               window.requestAnimationFrame(function() {
                  setElementsPosition();
                  doingAnimation = false;
               });

            }
         }
        function setElementsPosition() {
              var _fp_offsets = [];
              for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {
                  for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    var $_el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          _offset = 0,
                          $_fp      = $($_featured_pages[_fp_index]);

                    if ( $_el.length > 0 ) {
                       $_el.css( 'paddingTop', '' );
                       _offset = $_el.offset().top;

                    }
                    _offsets[_element_index][_fp_index] = _offset;
                    if ( _fp_offsets.length < _n_featured_pages )
                       _fp_offsets[_fp_index] = parseFloat( $_fp.offset().top);
                 }//endfor
                 if ( 1 != _.uniq(_fp_offsets).length )
                    continue;
                 _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );
                 for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    var $__el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          __offset;

                    if ( $__el.length > 0 ) {
                       __offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];
                       if ( __offset )
                          $__el.css( 'paddingTop', parseFloat($__el.css('paddingTop')) + __offset );
                    }
                 }//endfor
              }//endfor
          }//endfunction
      },//endmethod
      bttArrow : function() {
            var doingAnimation = false,
                $_btt_arrow = $( '.czr-btta' );

            if ( 0 === $_btt_arrow.length )
                return;
            var bttArrowVisibility = function() {
                  if ( ! doingAnimation ) {
                     doingAnimation = true;

                     window.requestAnimationFrame( function() {
                          $_btt_arrow.toggleClass( 'show', czrapp.$_window.scrollTop() > ( czrapp.$_window.height() ) );
                          doingAnimation = false;
                     });
                  }
            };//bttArrowVisibility

            czrapp.$_window.on( 'scroll', _.throttle( bttArrowVisibility, 20 ) );
            bttArrowVisibility();
      },//bttArrow
      backToTop : function() {
            var $_html = $("html, body"),
                 _backToTop = function( evt ) {
                      return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                 };

            czrapp.$_body.on( 'click touchstart touchend czr-btt', '.czr-btt', function ( evt ) {
                  evt.preventDefault();
                  evt.stopPropagation();
                  $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  $_html.animate({
                        scrollTop: 0
                  }, 1e3, function () {
                        $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  });
           });
      },
      anchorSmoothScroll : function() {
            if ( ! czrapp.localized.anchorSmoothScroll || 'easeOutExpo' != czrapp.localized.anchorSmoothScroll )
                return;

            var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
                self = this,
                $_links = $('a[href^="#"]', '#content').not(_excl_sels);
            _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null ;
            if ( _deep_excl ) {
                  _links = _.toArray($_links).filter( function ( _el ) {
                    return ( 2 == ( ['ids', 'classes'].filter(
                                  function( sel_type) {
                                      return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                                  } ) ).length
                          );
                  });
            }
            $(_links).click( function () {
                  var anchor_id = $(this).attr("href");
                  if ( ! $(anchor_id).length )
                    return;

                  if ('#' != anchor_id) {
                      $('html, body').animate({
                          scrollTop: $(anchor_id).offset().top
                      }, 700, czrapp.localized.anchorSmoothScroll);
                  }
                  return false;
            });//click
          },

   };//_methods{}

   czrapp.methods.UserXP = czrapp.methods.UserXP || {};
   $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
    initOnDomReady : function() {
      this.$_push         = $('#czr-push-footer');
      this._class         = 'sticky-footer-enabled';
      this.$_page         = $('#tc-page-wrap');
      this.doingAnimation = false;

      setTimeout( function() {
        czrapp.$_body.trigger('refresh-sticky-footer');
      }, 50 );
    },
    stickyFooterEventListener : function() {
      var self = this;
      czrapp.$_window.on( 'resize', function() {
        self.stickyFooterEventHandler('resize');
      });
      czrapp.$_window.on( 'golden-ratio-applied', function() {
        self.stickyFooterEventHandler('refresh');
      });
      czrapp.$_body.on( 'refresh-sticky-footer', function() {
        self.stickyFooterEventHandler('refresh');
      });

    },

    stickyFooterEventHandler : function( evt ) {
      var self = this;

      if ( ! this._is_sticky_footer_enabled() )
        return;

      switch ( evt ) {
        case 'resize':
          if ( !self.doingAnimation ) {
              self.doingAnimation = true;
              window.requestAnimationFrame(function() {
                  self._apply_sticky_footer();
                  self.doingAnimation = false;
              });
          }
        break;
        case 'refresh':
          this._apply_sticky_footer();
        break;
      }
    },
    _apply_sticky_footer : function() {

      var  _f_height     = this._get_full_height(),
           _w_height     = czrapp.$_window.height(),
           _push_height  = _w_height - _f_height,
           _event        = false;

      if ( _push_height > 0 ) {

        this.$_push.css('height', _push_height).addClass(this._class);
        _event = 'sticky-footer-on';

      }
      else if ( this.$_push.hasClass(this._class) ) {

        this.$_push.removeClass(this._class);
        _event = 'sticky-footer-off';

      }
      if ( _event )
        czrapp.$_body.trigger(_event);
    },
    _is_sticky_footer_enabled : function() {
      return czrapp.$_body.hasClass('czr-sticky-footer');
    },
    _get_full_height : function() {
      var _full_height = this.$_page.outerHeight(true) + this.$_page.offset().top,
          _push_height = 'block' == this.$_push.css('display') ? this.$_push.outerHeight() : 0;

      return _full_height - _push_height;
    }
  };//_methods{}

  czrapp.methods.StickyFooter = {};
  $.extend( czrapp.methods.StickyFooter , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
    initOnDomReady : function() {
      this._sidenav_selector        = '#tc-sn';

      if ( ! this._is_sn_on() )
        return;
      this._doingWindowAnimation    = false;

      this._sidenav_inner_class     = 'tc-sn-inner';
      this._sidenav_menu_class      = 'nav__menu-wrapper';

      this._toggle_event            = 'click';
      this._toggler_selector        = '[data-toggle="sidenav"]';
      this._active_class            = 'show';

      this._browser_can_translate3d = ! czrapp.$_html.hasClass('no-csstransforms3d');
      this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';
      this.sideNavEventListener();

      this._set_offset_height();
      this._init_scrollbar();

    },//init()
    sideNavEventListener : function() {
      var self = this;
      czrapp.$_body.on( this._toggle_event, '[data-toggle="sidenav"]', function( evt ) {
        self.sideNavEventHandler( evt, 'toggle' );
      });
      czrapp.$_body.on( this.transitionEnd, '#tc-sn', function( evt ) {
        self.sideNavEventHandler( evt, 'transitionend' );
      });
      czrapp.$_body.on( 'sn-close sn-open', function( evt ) {
        self.sideNavEventHandler( evt, evt.type );
      });
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
          if ( ! this._is_translating() )
            this._toggle_callback( evt );
        break;

        case 'transitionend' :
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
      var _aria_expanded_attr = 'sn-open' == this._anim_type; //boolean
      $( this._toggler_selector ).attr('aria-expanded', _aria_expanded_attr );
      $( this._sidenav_selector ).attr('aria-expanded', _aria_expanded_attr );
      if ( this._browser_can_translate3d ){
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
      $( this._toggler_selector ).toggleClass( 'collapsed' );
      $( this._sidenav_selector ).toggleClass( this._active_class );

    },
    _is_sn_on : function() {
      return $( this._sidenav_selector ).length > 0 ? true : false;
    },
    _get_initial_offset : function() {
      var _initial_offset = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;
      _initial_offset = _initial_offset && czrapp.$_window.scrollTop() && 'absolute' == czrapp.$_wpadminbar.css('position') ? 0 : _initial_offset;

      return _initial_offset; /* add a custom offset ?*/
    },
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
    _init_scrollbar : function() {

      if ( 'function' == typeof $.fn.mCustomScrollbar ) {

        $( '.' + this._sidenav_menu_class, this._sidenav_selector ).mCustomScrollbar({

            theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',

        });

      }

    },
    _is_translating : function() {

      return czrapp.$_body.hasClass('animating');

    },

  };//_methods{}

  czrapp.methods.SideNav = {};
  $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {

    initOnCzrReady : function() {
      this.DATA_KEY  = 'czr.czrDropdown';
      this.EVENT_KEY = '.' + this.DATA_KEY;
      this.Event     = {
        PLACE_ME  : 'placeme'+ this.EVENT_KEY,
        PLACE_ALL : 'placeall' + this.EVENT_KEY,
        SHOWN     : 'shown' + this.EVENT_KEY,
        SHOW      : 'show' + this.EVENT_KEY,
        HIDDEN    : 'hidden' + this.EVENT_KEY,
        HIDE      : 'hide' + this.EVENT_KEY,
        CLICK     : 'click' + this.EVENT_KEY,
        TAP       : 'tap' + this.EVENT_KEY,
      };
      this.ClassName = {
        DROPDOWN         : 'czr-dropdown-menu',
        SHOW             : 'show',
        PARENTS          : 'menu-item-has-children'
      };

      this.Selector = {
        DATA_TOGGLE              : '[data-toggle="czr-dropdown"]',
        DATA_SHOWN_TOGGLE        : '.' +this.ClassName.SHOW+ '> [data-toggle="czr-dropdown"]',
        DATA_HOVER_PARENT        : '.czr-open-on-hover .menu-item-has-children, .nav__woocart',
        DATA_CLICK_PARENT        : '.czr-open-on-click .menu-item-has-children',
        DATA_PARENTS             : '.tc-header .menu-item-has-children'
      };
    },
    dropdownMenuOnHover : function() {
      var _dropdown_selector = this.Selector.DATA_HOVER_PARENT,
          self               = this;

      enableDropdownOnHover();

      function _addOpenClass ( evt ) {

        var $_el = $(this);
        _debounced_addOpenClass = _.debounce( function() {
          if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
            return false;

          if ( ! $_el.hasClass(self.ClassName.SHOW) ) {
            $_el.addClass(self.ClassName.SHOW)
                .trigger(self.Event.SHOWN);

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'true');
          }

        }, 30);

        _debounced_addOpenClass();
      }
      
      function _removeOpenClass () {

        var $_el = $(this);
        _debounced_removeOpenClass = _.debounce( function() {

          if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
            $_el.removeClass(self.ClassName.SHOW)
                .trigger( self.Event.HIDDEN );

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'false');
          }

        }, 30);

        _debounced_removeOpenClass();
      }

      function enableDropdownOnHover() {

        czrapp.$_body.on( 'mouseenter', _dropdown_selector, _addOpenClass );
        czrapp.$_body.on( 'mouseleave', _dropdown_selector , _removeOpenClass );

      }

      function disableDropdownOnHover() {

        czrapp.$_body.off( 'mouseenter', _dropdown_selector, _addOpenClass );
        czrapp.$_body.off( 'mouseleave', _dropdown_selector , _removeOpenClass );

      }

    },

    dropdownOpenGoToLinkOnClick : function() {
      var self = this;
      czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE, function(evt) {

            var $_el = $(this);
            if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
              return false;

            evt.preventDefault();


            if ( '#' != $_el.attr( 'href' ) ) {
              window.location = $_el.attr('href');
            }

            else {
              return true;
            }

      });//.on()

    },
    dropdownPlacement : function() {
      var self = this,
          doingAnimation = false;

      czrapp.$_window
          .on( 'resize', function() {
                  if ( ! doingAnimation ) {
                        doingAnimation = true;
                        window.requestAnimationFrame(function() {
                          $( '.'+self.ClassName.PARENTS+'.'+self.ClassName.SHOW)
                              .trigger(self.Event.PLACE_ME);
                          doingAnimation = false;
                        });
                  }

          });

      czrapp.$_body
          .on( this.Event.PLACE_ALL, function() {
                      $( '.'+self.ClassName.PARENTS )
                          .trigger(self.Event.PLACE_ME);
          })
          .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.DATA_PARENTS, function(evt) {
            evt.stopPropagation();
            _do_snake( $(this), evt );
          });
      function _do_snake( $_el, evt ) {

        if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) )
          return;

        var $_this       = $_el,
            $_dropdown   = $_this.children( '.'+self.ClassName.DROPDOWN );

        if ( !$_dropdown.length )
          return;

        $_dropdown.css( 'zIndex', '-100' ).css('display', 'block');

        _maybe_move( $_dropdown );
        $_dropdown.css( 'zIndex', '').css('display', '');

      }


      function _maybe_move( $_dropdown ) {
        if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( 'open-left' ) ) {
          $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );
        }
        else {
          $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );
        }
        if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {

          $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );

        }
        else if ( $_dropdown.offset().left < 0 ) {

          $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );

        }

      }

    }


  };//_methods{}

  czrapp.methods.Dropdowns = {};
  $.extend( czrapp.methods.Dropdowns , _methods );


    var _createClass = function () {
     function defineProperties(target, props) {
       for (var i = 0; i < props.length; i++) {
         var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ("value" in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
       }
     }return function (Constructor, protoProps, staticProps) {
       if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
     };
    }();

    function _classCallCheck(instance, Constructor) {
     if (!(instance instanceof Constructor)) {
       throw new TypeError("Cannot call a class as a function");
     }
    }

    var NAME = 'czrDropdown';
    var VERSION = '1'; // '4.0.0-alpha.6';
    var DATA_KEY = 'czr.czrDropdown';
    var EVENT_KEY = '.' + DATA_KEY;
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key
    var SPACE_KEYCODE = 32; // KeyboardEvent.which value for space key
    var TAB_KEYCODE  = 9; // KeyboardEvent.which value for tab key
    var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key
    var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key
    var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)
    var REGEXP_KEYDOWN = new RegExp(ARROW_UP_KEYCODE + '|' + ARROW_DOWN_KEYCODE + '|' + ESCAPE_KEYCODE );

    var Event = {
      HIDE: 'hide' + EVENT_KEY,
      HIDDEN: 'hidden' + EVENT_KEY,
      SHOW: 'show' + EVENT_KEY,
      SHOWN: 'shown' + EVENT_KEY,
      CLICK: 'click' + EVENT_KEY,
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY,
      KEYDOWN_DATA_API: 'keydown' + EVENT_KEY + DATA_API_KEY,
      KEYUP_DATA_API: 'keyup' + EVENT_KEY + DATA_API_KEY
    };

    var ClassName = {
      DISABLED: 'disabled',
      SHOW: 'show'
    };

    var Selector = {
      DATA_TOGGLE: '[data-toggle="czr-dropdown"]',
      FORM_CHILD: '.czr-dropdown form',
      MENU: '.dropdown-menu',
      NAVBAR_NAV: '.regular-nav',
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)'
    };

    var czrDropdown = function ($) {

      var czrDropdown = function () {
        function czrDropdown(element) {
          _classCallCheck(this, czrDropdown);

          this._element = element;

          this._addEventListeners();
        }

        czrDropdown.prototype.toggle = function toggle() {
          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
            return false;
          }
          if( 'static' == $(this).next( Selector.MENU ).css( 'position' ) )
            return true;

          var parent = czrDropdown._getParentFromElement(this);
          var isActive = $(parent).hasClass(ClassName.SHOW);
          var _parentsToNotClear = $.makeArray( $(parent).parents(Selector.PARENTS) );

          czrDropdown._clearMenus('', _parentsToNotClear );

          if (isActive) {
            return false;
          }

          var relatedTarget = {
            relatedTarget: this
          };
          var showEvent = $.Event(Event.SHOW, relatedTarget);

          $(parent).trigger(showEvent);

          if (showEvent.isDefaultPrevented()) {
            return false;
          }
          if ('ontouchstart' in document.documentElement && !$(parent).closest(Selector.NAVBAR_NAV).length) {
            $('body').children().on('mouseover', null, $.noop);
          }

          this.focus();
          this.setAttribute('aria-expanded', 'true');

          $(parent).toggleClass(ClassName.SHOW);
          $(parent).trigger($.Event(Event.SHOWN, relatedTarget));

          return false;
        };

        czrDropdown.prototype.dispose = function dispose() {
          $.removeData(this._element, DATA_KEY);
          $(this._element).off(EVENT_KEY);
          this._element = null;
        };

        czrDropdown.prototype._addEventListeners = function _addEventListeners() {
          $(this._element).on(Event.CLICK, this.toggle);
        };

        czrDropdown._jQueryInterface = function _jQueryInterface(config) {
          return this.each(function () {
            var data = $(this).data(DATA_KEY);

            if (!data) {
              data = new czrDropdown(this);
              $(this).data(DATA_KEY, data);
            }

            if (typeof config === 'string') {
              if (data[config] === undefined) {
                throw new Error('No method named "' + config + '"');
              }
              data[config].call(this);
            }
          });
        };

        czrDropdown._clearMenus = function _clearMenus(event, _parentsToNotClear ) {

          if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH || event.type === 'keyup' && event.which !== TAB_KEYCODE)) {
            return;
          }


          var toggles = $.makeArray($(Selector.DATA_TOGGLE));


          for (var i = 0; i < toggles.length; i++) {
            var parent = czrDropdown._getParentFromElement(toggles[i]);
            var relatedTarget = { relatedTarget: toggles[i] };

            if (!$(parent).hasClass(ClassName.SHOW) || $.inArray(parent, _parentsToNotClear ) > -1 ){
              continue;
            }

            if (event && ( event.type === 'click' &&
                /input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) && $.contains(parent, event.target)) {
              continue;
            }

            var hideEvent = $.Event(Event.HIDE, relatedTarget);
            $(parent).trigger(hideEvent);
            if (hideEvent.isDefaultPrevented()) {
              continue;
            }
            if ('ontouchstart' in document.documentElement) {
              $('body').children().off('mouseover', null, $.noop);
            }


            toggles[i].setAttribute('aria-expanded', 'false');

            $(parent).removeClass(ClassName.SHOW).trigger($.Event(Event.HIDDEN, relatedTarget));
          }
        };

        czrDropdown._getParentFromElement = function _getParentFromElement(element) {
          var _parentNode = void 0;
          var $_parent = $(element).closest(Selector.PARENTS);

          if ( $_parent.length ) {
            _parentNode = $_parent[0];
          }

          return _parentNode || element.parentNode;
        };

        czrDropdown._dataApiKeydownHandler = function _dataApiKeydownHandler(event) {
          if (!REGEXP_KEYDOWN.test(event.which) || /button/i.test(event.target.tagName) && event.which === SPACE_KEYCODE ||
             /input|textarea/i.test(event.target.tagName)) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();

          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
            return;
          }

          var parent = czrDropdown._getParentFromElement(this);
          var isActive = $(parent).hasClass(ClassName.SHOW);

          if (!isActive && ( event.which !== ESCAPE_KEYCODE || event.which !== SPACE_KEYCODE ) ||
               isActive && ( event.which !== ESCAPE_KEYCODE || event.which !== SPACE_KEYCODE ) ) {

            if (event.which === ESCAPE_KEYCODE) {
              var toggle = $(parent).find(Selector.DATA_TOGGLE)[0];
              $(toggle).trigger('focus');
            }

            $(this).trigger('click');
            return;
          }
          var items = $(parent).find(Selector.VISIBLE_ITEMS).get();

          if (!items.length) {
            return;
          }

          var index = items.indexOf(event.target);

          if (event.which === ARROW_UP_KEYCODE && index > 0) {
            index--;
          }

          if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
            index++;
          }

          if (index < 0) {
            index = 0;
          }

          items[index].focus();
        };

        _createClass(czrDropdown, null, [{
          key: 'VERSION',
          get: function get() {
            return VERSION;
          }
        }]);

        return czrDropdown;
      }();

      $(document)
        .on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler)
        .on(Event.KEYDOWN_DATA_API, Selector.MENU, czrDropdown._dataApiKeydownHandler)
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API, czrDropdown._clearMenus)
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle)
        .on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
          e.stopPropagation();
      });

      $.fn[NAME] = czrDropdown._jQueryInterface;
      $.fn[NAME].Constructor = czrDropdown;
      $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return czrDropdown._jQueryInterface;
      };

      return czrDropdown;

  }(jQuery);

})(jQuery, czrapp);var czrapp = czrapp || {};

( function ( czrapp, $, _ ) {
      $.extend( czrapp, czrapp.Events );
      czrapp.Root           = czrapp.Class.extend( {
            initialize : function( options ) {
                  $.extend( this, options || {} );
                  this.isReady = $.Deferred();
            },
            ready : function() {
                  var self = this;
                  if ( self.dom_ready && _.isArray( self.dom_ready ) ) {
                        czrapp.status = czrapp.status || [];
                        _.each( self.dom_ready , function( _m_ ) {
                              if ( ! _.isFunction( _m_ ) && ! _.isFunction( self[_m_]) ) {
                                    czrapp.status.push( 'Method ' + _m_ + ' was not found and could not be fired on DOM ready.');
                                    return;
                              }
                              try { ( _.isFunction( _m_ ) ? _m_ : self[_m_] ).call( self ); } catch( er ){
                                    czrapp.status.push( [ 'NOK', self.id + '::' + _m_, _.isString( er ) ? czrapp._truncate( er ) : er ].join( ' => ') );
                                    return;
                              }
                        });
                  }
                  this.isReady.resolve();
            }
      });

      czrapp.Base           = czrapp.Root.extend( czrapp.methods.Base );
      czrapp.ready          = $.Deferred();
      czrapp.bind( 'czrapp-ready', function() {
            czrapp.ready.resolve();
      });
      var _instantianteAndFireOnDomReady = function( newMap, previousMap, isInitial ) {
            if ( ! _.isObject( newMap ) )
              return;
            _.each( newMap, function( params, name ) {
                  if ( czrapp[ name ] || ! _.isObject( params ) )
                    return;

                  params = _.extend(
                        {
                              ctor : {},//should extend czrapp.Base with custom methods
                              ready : [],//a list of method to execute on dom ready,
                              options : {}//can be used to pass a set of initial params to set to the constructors
                        },
                        params
                  );
                  var ctorOptions = _.extend(
                      {
                          id : name,
                          dom_ready : params.ready || []
                      },
                      params.options
                  );

                  try { czrapp[ name ] = new params.ctor( ctorOptions ); }
                  catch( er ) {
                        czrapp.errorLog( 'Error when loading ' + name + ' | ' + er );
                  }
            });
            $(function ($) {
                  _.each( newMap, function( params, name ) {
                        if ( czrapp[ name ] && czrapp[ name ].isReady && 'resolved' == czrapp[ name ].isReady.state() )
                          return;
                        if ( _.isObject( czrapp[ name ] ) && _.isFunction( czrapp[ name ].ready ) ) {
                              czrapp[ name ].ready();
                        }
                  });
                  czrapp.status = czrapp.status || 'OK';
                  if ( _.isArray( czrapp.status ) ) {
                        _.each( czrapp.status, function( error ) {
                              czrapp.errorLog( error );
                        });
                  }
                  czrapp.trigger( isInitial ? 'czrapp-ready' : 'czrapp-updated' );
            });
      };//_instantianteAndFireOnDomReady()
      czrapp.appMap = new czrapp.Value( {} );
      czrapp.appMap.bind( _instantianteAndFireOnDomReady );//<=THE MAP IS LISTENED TO HERE
      czrapp.customMap = new czrapp.Value( {} );
      czrapp.customMap.bind( _instantianteAndFireOnDomReady );//<=THE CUSTOM MAP IS LISTENED TO HERE


})( czrapp, jQuery, _ );/****************************************************************
* FORMER HARD CODED SCRIPTS MADE ENQUEUABLE WITH LOCALIZED PARAMS
*****************************************************************/
(function($, czrapp, _ ) {
    czrapp.ready.then( function() {
          if ( czrapp.localized.frontHelpNoticesOn && ! _.isEmpty( frontHelpNoticeParams ) ) {
                var _doAjax = function( _query_ ) {
                          var ajaxUrl = czrapp.localized.adminAjaxUrl, dfd = $.Deferred();
                          $.post( ajaxUrl, _query_ )
                                .done( function( _r ) {
                                      if ( '0' === _r ||  '-1' === _r )
                                        czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                                })
                                .fail( function( _r ) {
                                      czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                                })
                                .always( function() {
                                      dfd.resolve();
                                });
                          return dfd.promise();
                    },
                    _ajaxDismiss = function( _params_ ) {
                          var _query = {},
                              dfd = $.Deferred();

                          if ( ! _.isObject( _params_ ) ) {
                                czrapp.errorLog( 'placeHolder dismiss : wrong params' );
                                return;
                          }
                          _params_ = _.extend( {
                                action : '',
                                nonce : { 'id' : '', 'handle' : '' },
                                class : '',
                                remove_action : null,//for slider and fp
                                position : null,//for widgets
                          }, _params_ );
                          _query.action = _params_.action;
                          if ( ! _.isNull( _params_.remove_action ) )
                            _query.remove_action = _params_.remove_action;
                          if ( ! _.isNull( _params_.position ) )
                            _query.position = _params_.position;

                          _query[ _params_.nonce.id ] = _params_.nonce.handle;
                          _doAjax( _query ).done( function() { dfd.resolve(); });
                          return dfd.promise();
                    };
                _.each( frontHelpNoticeParams, function( _params_, _id_ ) {
                      _params_ = _.extend( {
                            active : false,
                            args : {
                                  action : '',
                                  nonce : { 'id' : '', 'handle' : '' },
                                  class : '',
                                  remove_action : null,//for slider and fp
                                  position : null,//for widgets
                            }
                      }, _params_ );

                      switch( _id_ ) {
                            case 'thumbnail' :
                            case 'smartload' :
                            case 'sidenav' :
                            case 'secondMenu' :
                            case 'mainMenu' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $( '.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $_el.closest('.' + _params_.args.class ).slideToggle( 'fast' );
                                                    });
                                              } );
                                        } );
                                  }
                            break;
                            case 'slider' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _params_.args.remove_action = 'remove_notice';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $_el.closest( '.' + _params_.args.class ).slideToggle('fast');
                                                    });
                                              } );
                                              $('.tc-inline-remove', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    _params_.args.remove_action = 'remove_slider';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $( 'div[id*="customizr-slider"]' ).fadeOut('slow');
                                                    });

                                              } );
                                        } );
                                  }
                            break;
                            case 'fp' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _params_.args.remove_action = 'remove_notice';
                                                    _ajaxDismiss(  _params_.args ).done( function() {
                                                          $_el.closest( '.' + _params_.args.class ).slideToggle('fast');
                                                    });
                                              } );
                                              $('.tc-inline-remove', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    _params_.args.remove_action = 'remove_fp';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $('#main-wrapper > .marketing').fadeOut('slow');
                                                    });

                                              } );
                                        } );
                                  }
                            break;
                            case 'widget' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice, .tc-inline-dismiss-notice').click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    var _position = $_el.attr('data-position');
                                                    if ( ! _position || ! _position.length )
                                                      return;

                                                     _params_.args.position = _position;
                                                    _ajaxDismiss(  _params_.args ).done( function() {
                                                          if ( 'sidebar' == _position )
                                                            $('.tc-widget-placeholder' , '.tc-sidebar').slideToggle('fast');
                                                          else
                                                            $_el.closest('.tc-widget-placeholder').slideToggle('fast');
                                                    });
                                              } );
                                        } );
                                  }
                            break;
                      }//switch
                });//_.each()
          }//if czrapp.localized.frontHelpNoticesOn && ! _.isEmpty( frontHelpNoticeParams
    });

})(jQuery, czrapp, _ );var czrapp = czrapp || {};
( function ( czrapp, $, _ ) {
      czrapp.localized = CZRParams || {};
      var appMap = {
                base : {
                      ctor : czrapp.Base,
                      ready : [
                            'cacheProp'
                      ]
                },
                browserDetect : {
                      ctor : czrapp.Base.extend( czrapp.methods.BrowserDetect ),
                      ready : [ 'addBrowserClassToBody' ]
                },
                jqPlugins : {
                      ctor : czrapp.Base.extend( czrapp.methods.JQPlugins ),
                      ready : [
                            'centerImagesWithDelay',
                            'imgSmartLoad',
                            'lightBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnCzrReady',
                            'fireCarousels',
                            'centerMainSlider'
                      ]
                },
                dropdowns : {
                      ctor  : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [
                            'initOnCzrReady',
                            'dropdownMenuOnHover',
                            'dropdownOpenGoToLinkOnClick',
                            'dropdownPlacement'//snake
                      ]
                },
                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'setupUIListeners',//<= setup various observable values like this.isScrolling, this.scrollPosition, ...

                            'stickifyHeader',

                            'outline',

                            'variousHoverActions',
                            'formFocusAction',
                            'variousHeaderActions',
                            'smoothScroll',
                            'onEscapeKeyPressed',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',

                            'mayBePrintWelcomeNote',
                      ]
                },
                stickyFooter : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyFooter ),
                      ready : [
                            'initOnDomReady',
                            'stickyFooterEventListener'
                      ]
                },
                sideNav : {
                      ctor : czrapp.Base.extend( czrapp.methods.SideNav ),
                      ready : [
                            'initOnDomReady'
                      ]
                }
      };//map
      czrapp.appMap( appMap , true );//true for isInitial map

})( czrapp, jQuery, _ );