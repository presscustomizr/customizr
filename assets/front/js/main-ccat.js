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
      var _prettyPrintLog = function( args ) {
            var _defaults = {
                  bgCol : '#5ed1f5',
                  textCol : '#000',
                  consoleArguments : []
            };
            args = _.extend( _defaults, args );

            var _toArr = Array.from( args.consoleArguments ),
                _truncate = function( string ){
                      if ( ! _.isString( string ) )
                        return '';
                      return string.length > 300 ? string.substr( 0, 299 ) + '...' : string;
                };
            if ( ! _.isEmpty( _.filter( _toArr, function( it ) { return ! _.isString( it ); } ) ) ) {
                  _toArr =  JSON.stringify( _toArr.join(' ') );
            } else {
                  _toArr = _toArr.join(' ');
            }
            return [
                  '%c ' + _truncate( _toArr ),
                  [ 'background:' + args.bgCol, 'color:' + args.textCol, 'display: block;' ].join(';')
            ];
      };

      var _wrapLogInsideTags = function( title, msg, bgColor ) {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;
            if ( czrapp.localized.isDevMode ) {
                  if ( _.isUndefined( msg ) ) {
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '<' + title + '>' ] } ) );
                  } else {
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '<' + title + '>' ] } ) );
                        console.log( msg );
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '</' + title + '>' ] } ) );
                  }
            } else {
                  console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ title ] } ) );
            }
      };
      czrapp.consoleLog = function() {
            if ( ! czrapp.localized.isDevMode )
              return;
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;
            console.log.apply( console, _prettyPrintLog( { consoleArguments : arguments } ) );
            console.log( 'Unstyled console message : ', arguments );
      };

      czrapp.errorLog = function() {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, _prettyPrintLog( { bgCol : '#ffd5a0', textCol : '#000', consoleArguments : arguments } ) );
      };


      czrapp.errare = function( title, msg ) { _wrapLogInsideTags( title, msg, '#ffd5a0' ); };
      czrapp.infoLog = function( title, msg ) { _wrapLogInsideTags( title, msg, '#5ed1f5' ); };
      czrapp.doAjax = function( queryParams ) {
            queryParams = queryParams || ( _.isObject( queryParams ) ? queryParams : {} );

            var ajaxUrl = queryParams.ajaxUrl || czrapp.localized.ajaxUrl,//the ajaxUrl can be specified when invoking doAjax
                nonce = czrapp.localized.frontNonce,//{ 'id' => 'HuFrontNonce', 'handle' => wp_create_nonce( 'hu-front-nonce' ) },
                dfd = $.Deferred(),
                _query_ = _.extend( {
                            action : '',
                            withNonce : false
                      },
                      queryParams
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
                        if ( '0' === _r ||  '-1' === _r || false === _r.success ) {
                              czrapp.errare( 'czrapp.doAjax : done ajax error for action : ' + _query_.action , _r );
                              dfd.reject( _r );
                        }
                        dfd.resolve( _r );
                  })
                  .fail( function( _r ) {
                        czrapp.errare( 'czrapp.doAjax : failed ajax error for : ' + _query_.action, _r );
                        dfd.reject( _r );
                  });
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
                    var once = _event.once ? _event.once : false;
                    args.dom_el[ once ? 'one' : 'on' ]( _event.trigger , _event.selector, function( e, event_params ) {
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
              var args = _.extend( { silent : false }, _.isObject( o ) ? o : {} );
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
                          .fail( function() { czrapp.errorLog( 'A deferred callback failed in api.Value::set()'); })
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
              var from = this._value;

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              if ( null === to || _.isEqual( from, to ) ) {
                return this;
              }

              this._value = to;
              this._dirty = ( _.isUndefined( dirtyness ) || ! _.isBoolean( dirtyness ) ) ? this._dirty : dirtyness;

              this.callbacks.fireWith( this, [ to, from, { silent : true } ] );

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
                        $_header         : $('.tc-header'),
                        localized        : "undefined" != typeof(CZRParams) && CZRParams ? CZRParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice(),//store the initial device
                        isRTL            : $('html').attr('dir') == 'rtl'//is rtl?
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
                  var $_window = czrapp.$_window || $(window);
                  return $_window.width() <= ( _maxWidth - 15 );
            },

            emit : function( cbs, args ) {
                  cbs = _.isArray(cbs) ? cbs : [cbs];
                  var self = this;
                  _.map( cbs, function(cb) {
                        if ( 'function' == typeof(self[cb]) ) {
                              args = 'undefined' == typeof( args ) ? [] : args ;
                              self[cb].apply(self, args );
                              czrapp.trigger( cb, _.object( _.keys(args), args ) );
                        }
                  });//_.map
            },

            triggerSimpleLoad : function( $_imgs ) {
                  if ( 0 === $_imgs.length )
                    return;

                  $_imgs.map( function( _ind, _img ) {
                    $(_img).on('load', function () {
                      $(_img).trigger('simple_load');
                    });//end load
                    if ( $(_img)[0] && $(_img)[0].complete )
                      $(_img).trigger('load');
                  } );//end map
            },//end of fn

            isUserLogged     : function() {
                  return czrapp.$_body.hasClass('logged-in') || 0 !== czrapp.$_wpadminbar.length;
            },

            isSelectorAllowed : function( $_el, skip_selectors, requested_sel_type ) {
                  var sel_type = 'ids' == requested_sel_type ? 'id' : 'class',
                  _selsToSkip   = skip_selectors[requested_sel_type];
                  if ( 'object' != typeof(skip_selectors) || ! skip_selectors[requested_sel_type] || ! _.isArray( skip_selectors[requested_sel_type] ) || 0 === skip_selectors[requested_sel_type].length )
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
                  var to_return = [];
                  _.each( $_elements, function( $_el, container ){
                        if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
                          to_return.push(container);
                  });
                  return to_return;
            },
            isInWindow : function( $_el, threshold ) {
                  if ( ! ( $_el instanceof $ ) )
                    return;
                  if ( threshold && ! _.isNumber( threshold ) )
                    return;

                  var wt = $(window).scrollTop(),
                      wb = wt + $(window).height(),
                      it  = $_el.offset().top,
                      ib  = it + $_el.height(),
                      th = threshold || 0;

                  return ib >= wt - th && it <= wb + th;
            },
            fireMeWhenStoppedScrolling : function( params ) {
                  params = _.extend( {
                      delay : 3000,
                      func : '',
                      instance : {},
                      args : []
                  }, params );

                  if ( ! _.isFunction( params.func ) )
                    return;
                  var _timer_ = function() {
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                  dfd.resolve();
                              }, params.delay );
                        }).done( function() {
                              if ( czrapp.userXP.isScrolling() ) {
                                    _timer_();
                              } else {
                                    params.func.apply( params.instance, params.args );
                              }
                        });
                  };
                  _timer_();
            },
            scriptLoadingStatus : {},
            observeAddedNodesOnDom : function(containerSelector, elementSelector, callback) {
                var onMutationsObserved = function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes.length) {
                                var elements = $(mutation.addedNodes).find(elementSelector);
                                for (var i = 0, len = elements.length; i < len; i++) {
                                    callback(elements[i]);
                                }
                            }
                        });
                    },
                    target = $(containerSelector)[0],
                    config = { childList: true, subtree: true },
                    MutationObserver = window.MutationObserver || window.WebKitMutationObserver,
                    observer = new MutationObserver(onMutationsObserved);

                observer.observe(target, config);
          }
      };//_methods{}

      czrapp.methods.Base = czrapp.methods.Base || {};
      $.extend( czrapp.methods.Base , _methods );//$.extend

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
    addBrowserClassToBody : function() {
          if ( !$.browser )
            return;
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
(function($, czrapp, Waypoint ) {
      var _methods = {
            centerImagesWithDelay : function( delay ) {
                  var self = this;
                  setTimeout( function(){ self.emit('centerImages'); }, delay || 50 );
            },


            centerInfinity : function() {

                  var centerInfiniteImagesModernStyle = function ( collection, _container ) {
                      var $_container  = $(_container);

                      if ( 'object' !== typeof collection || 1 > $_container.length )
                        return;
                      _.each( collection, function( elementSelector ) {

                            var $_imgsToSimpleLoad = $(  elementSelector + ' .js-centering', $_container ).centerImages( {
                                  enableCentering : 1,
                                  enableGoldenRatio : false,
                                  disableGRUnder : 0,//<= don't disable golden ratio when responsive,
                                  zeroTopAdjust: 0,
                                  setOpacityWhenCentered : false,//will set the opacity to 1
                                  oncustom : [ 'simple_load', 'smartload' ]
                            })
                            .find( 'img:not([src^="data"])' );
                            czrapp.methods.Base.triggerSimpleLoad( $_imgsToSimpleLoad );
                      });

                  };
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( ( 'undefined' !== typeof response ) && 'success' == response.type && response.collection && response.container ) {
                              centerInfiniteImagesModernStyle(
                                  response.collection,
                                  '#'+response.container //_container
                              );
                        }
                  } );
            },
            imgSmartLoad : function() {
                  var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
                      _where = czrapp.localized.imgSmartLoadOpts.parentSelectors.join(),
                      _params = _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {};
                  var _doLazyLoad = function() {
                        if ( !smartLoadEnabled )
                          return;

                        $(_where).each( function() {
                              if ( !$(this).data('smartLoadDone') ) {
                                    $(this).imgSmartLoad(_params);
                              } else {
                                    $(this).trigger('trigger-smartload');
                              }
                        });
                  };
                  _doLazyLoad();
                  this.observeAddedNodesOnDom('body', 'img', _.debounce( function(element) {
                        _doLazyLoad();
                  }, 50 ));
                  if ( 1 == czrapp.localized.centerAllImg ) {
                        var self                   = this,
                            $_to_center;
                        if ( smartLoadEnabled ) {
                              $_to_center = $( _.filter( $( _where ).find('img'), function( img ) {
                                  return $(img).is(czrapp.localized.imgSmartLoadOpts.opts.excludeImg.join());
                                }) );
                        } else { //filter
                              $_to_center = $( _where ).find('img');
                        }

                        var $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                                return $(img).hasClass('tc-holder-img');
                        }) );
                        setTimeout( function(){
                              self.triggerSimpleLoad( $_to_center_with_delay );
                        }, 800 );
                        self.triggerSimpleLoad( $_to_center );
                  }
            },
            centerImages : function() {
                  var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');
                  var _css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  $wrappersOfCenteredImagesCandidates.each( function() {
                        $( this ).append(  _css_loader ).find('.czr-css-loader').fadeIn( 'slow');
                  });
                  $wrappersOfCenteredImagesCandidates.centerImages({
                        onInit : true,
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
                  var $_fpuEl = $('.fpc-widget-front .fp-thumb-wrapper');
                  if ( 1 < $_fpuEl.length ) {
                        var _isFPUimgCentered = _.isUndefined( czrapp.localized.FPUImgCentered ) ? 1 == czrapp.localized.centerAllImg : 1 == czrapp.localized.FPUImgCentered;
                        $_fpuEl.centerImages( {
                            onInit : false,
                            enableCentering : _isFPUimgCentered,
                            enableGoldenRatio : false,
                            disableGRUnder : 0,//<= don't disable golden ratio when responsive
                            zeroTopAdjust : 0,
                            oncustom : ['smartload', 'simple_load', 'block_resized', 'fpu-recenter']
                        });
                        if ( 1 != czrapp.localized.imgSmartLoadEnabled ) {
                            czrapp.base.triggerSimpleLoad( $_fpuEl.find("img:not(.tc-holder-img)") );
                        } else {
                            $_fpuEl.find("img:not(.tc-holder-img)").each( function() {
                                    if ( $(this).data( 'czr-smart-loaded') ) {
                                        czrapp.base.triggerSimpleLoad( $(this) );
                                    }
                            });
                        }
                        if ( _isFPUimgCentered && 1 != czrapp.localized.imgSmartLoadEnabled ) {
                              var $_holder_img = $_fpuEl.find("img.tc-holder-img");
                              if ( 0 < $_holder_img.length ) {
                                  czrapp.base.triggerSimpleLoad( $_holder_img );
                                  setTimeout( function(){
                                        czrapp.base.triggerSimpleLoad( $_holder_img );
                                  }, 100 );
                              }
                        }
                  }//if ( 1 < $_fpuEl.length )
            },//center_images

            parallax : function() {
                  $( '.parallax-item' ).czrParallax();
                  $('.ham__navbar-toggler').on('click', function(){
                        setTimeout( function(){
                        Waypoint.refreshAll(); }, 400 ); }
                  );
            },
            czrMagnificPopup : function( $lightBoxCandidate, params ) {
                  if ( !CZRParams.isLightBoxEnabled )
                    return;

                  if ( 1 > $lightBoxCandidate.length )
                    return;

                  var _scrollHandle = function() {},//abstract that we can unbind
                      _do = function() {
                        czrapp.$_window.off( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.magnificPopup ) {
                                $lightBoxCandidate.magnificPopup( params );
                        } else {
                              if ( czrapp.base.scriptLoadingStatus.czrMagnificPopup && 'pending' == czrapp.base.scriptLoadingStatus.czrMagnificPopup.state() ) {
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.done( function() {
                                          $lightBoxCandidate.magnificPopup( params );
                                    });
                                    return;
                              }
                              czrapp.base.scriptLoadingStatus.czrMagnificPopup = czrapp.base.scriptLoadingStatus.czrMagnificPopup || $.Deferred();
                              if ( $('head').find( '#czr-magnific-popup' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-magnific-popup',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/magnific-popup.min.css'
                                    }) );
                              }

                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/jquery-magnific-popup.min.js'),
                                    cache : true,// use the browser cached version when available
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.magnificPopup ) )
                                      return;
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.resolve();
                                      $lightBoxCandidate.magnificPopup( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Magnific popup instantiation failed for candidate : '  + $lightBoxCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()
                  if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },

            lightBox : function() {
                  var self = this,
                      _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';
                  if ( $('a.expand-img').length > 0 ) {
                        this.czrMagnificPopup( $( '[class*="grid-container__"]' ), {
                          delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
                          type: 'image'
                        });
                  }
                  if ( $('[data-lb-type="grouped-gallery"]').length > 0 ) {
                        $( '.czr-gallery' ).each( function(){
                              self.czrMagnificPopup( $(this), {
                                    delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
                                    type: 'image',
                                    gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                    }
                              });
                        });
                  }
                  if ( $('[data-lb-type="grouped-post"]').length > 0 ) {
                        this.czrMagnificPopup( $('#content'), {
                              delegate: '[data-lb-type="grouped-post"]',
                              type: 'image',
                              gallery: {
                                   enabled: true,
                                   arrowMarkup: _arrowMarkup
                              }
                        });
                  }
                  czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
                        e.preventDefault();

                        var $_expand_btn    = $( this ),
                            $_gallery_crsl  = $_expand_btn.closest( '.czr-carousel' );


                        if ( $_gallery_crsl.length < 1 )
                          return;

                        var _do = function() {
                              if ( ! $_gallery_crsl.data( 'mfp' ) ) {

                                    self.czrMagnificPopup( $_gallery_crsl, {
                                        delegate: '.carousel-cell img',
                                        type: 'image',
                                        gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                        }
                                    });
                                    $_gallery_crsl.data( 'mfp', true );
                              }

                              if ( $_gallery_crsl.data( 'mfp' ) ) {
                                    $_gallery_crsl.find( '.is-selected img' ).trigger('click');
                              }
                        };
                        if ( 0 < $_gallery_crsl.find( '.flickity-slider').length ) {
                              _do();
                        } else {
                              _.delay( function() {
                                    _do();
                              }, 500 );//<= let the flickity slider be setup, because the slider is setup on click
                        }

                  });
            },

      };//_methods{}

      czrapp.methods.JQPlugins = {};
      $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp, Waypoint);var czrapp = czrapp || {};
(function($, czrapp ) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;

                  this.slidersSelectorMap = {
                        mainSlider : '[id^="customizr-slider-main"] .carousel-inner',
                        galleries : '.czr-gallery.czr-carousel .carousel-inner',
                        relatedPosts : '.grid-container__square-mini.carousel-inner'
                  };
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.mainSlider, function() {
                    $(this).find( '.carousel-caption .czrs-title' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 65,//the default max font-size
                                      minFontSize : 18,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-subtitle' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 35,//the default max font-size
                                      minFontSize : 15,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-cta-wrapper' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 18,//the default max font-size
                                      minFontSize : 12,
                                }
                    );
                  });
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.galleries, self._move_background_link_inside );
                  czrapp.$_body.on( 'click prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  czrapp.$_body.on( 'click next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );
                  this.fireRelatedPostsCarousel();
                  this.scheduleGalleryCarousels();
                  this.fireMainSlider();
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( ( 'undefined' !== typeof response ) && 'success' == response.type && response.collection && response.container ) {
                              if ( ! response.html || -1 === response.html.indexOf( 'czr-gallery' ) || -1 === response.html.indexOf( 'czr-carousel' ) ) {
                                    return;
                              }
                              self.scheduleGalleryCarousels();
                        }
                  } );
                  czrapp.$_body.on( 'before-endlessly-caching', function( e, params ) {
                        if ( ! _.isObject( params ) || _.isUndefined( params.candidates_for_caching || ! ( params.candidates_for_caching instanceof $ ) ) )
                          return;

                        params.candidates_for_caching.find( self.slidersSelectorMap.galleries ).each( function() {
                              if ( $(this).data('flickity') ) {
                                    var $_bg_link = $(this).find('.bg-link');
                                    $(this).closest('.entry-media__wrapper').prepend( $_bg_link );

                                    $(this).flickity( 'destroy' );
                                    $(this).find('.czr-css-loader').remove();
                              }
                        });
                  });
                  self._css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  czrapp.$_window.on('scroll', _.throttle( function() {
                        $( self.slidersSelectorMap.galleries ).each( function() {
                              if ( czrapp.base.isInWindow( $(this) ) ){
                                    $(this).trigger( 'czr-is-in-window', { el : $(this) } );
                              }
                        });
                  }, 50 ) );
            },//_init()
            czrFlickity : function( $_sliderCandidate, params ) {
                  if ( 1 > $_sliderCandidate.length )
                    return;

                  var _scrollHandle = function() {};//abstract that we can unbind
                  var _do = function() {
                        czrapp.$_window.off( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.flickity ) {
                              if ( ! $_sliderCandidate.data( 'flickity' ) )
                                $_sliderCandidate.flickity( params );
                        } else {
                              if ( czrapp.base.scriptLoadingStatus.flickity && 'pending' == czrapp.base.scriptLoadingStatus.flickity.state() ) {
                                    czrapp.base.scriptLoadingStatus.flickity.done( function() {
                                          $_sliderCandidate.flickity( params );
                                    });
                                    return;
                              }
                              czrapp.base.scriptLoadingStatus.flickity = czrapp.base.scriptLoadingStatus.flickity || $.Deferred();
                              if ( $('head').find( '#czr-flickity' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-flickity',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/flickity.min.css'
                                    }) );
                              }
                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/flickity-pkgd.min.js'),
                                    cache : true,// use the browser cached version when availabl
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.flickity ) )
                                      return;
                                    czrapp.base.scriptLoadingStatus.flickity.resolve();
                                    var activate = Flickity.prototype.activate;
                                    Flickity.prototype.activate = function() {
                                          if ( this.isActive ) {
                                            return;
                                          }
                                          activate.apply( this, arguments );
                                          this.dispatchEvent( 'czr-flickity-ready', null, this );
                                    };
                                    if ( ! $_sliderCandidate.data( 'flickity' ) )
                                      $_sliderCandidate.flickity( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Flickity instantiation failed for slider candidate : '  + $_sliderCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()
                  if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },
            scheduleGalleryCarousels : function( $_gallery_container ) {
                  var $_galleries,
                      self = this;

                  if ( ! _.isUndefined( $_gallery_container ) && 0 < $_gallery_container.length ) {
                        $_galleries = $_gallery_container.find( self.slidersSelectorMap.galleries );
                  } else {
                        $_galleries = $(self.slidersSelectorMap.galleries);
                  }
                  $_galleries.each( function() {
                        var $_gal = $(this),
                            $_firstcell = $_gal.find( '.carousel-cell' ).first(),
                            $_parentGridItem = $_gal.closest('.grid-item');

                        if ( 1 > $_firstcell.length )
                          return;
                        var _isSmartLoadCandidateImg = 0 < $_firstcell.find('img').length && 0 === $_firstcell.find('img').attr('src').indexOf('data');

                        $_firstcell.centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : ['smartload']
                        } );
                        if ( czrapp.localized.imgSmartLoadsForSliders ) {
                              if ( ! $_firstcell.data('czr_smartLoaded') ) {
                                    $_firstcell.find('img').removeClass('tc-smart-load-skip');
                                    $_firstcell.on( 'smartload', function() {
                                          self._maybeRemoveLoader.call( $_firstcell );
                                    });
                                    self._smartLoadCellImg( { el : $_firstcell, ev : 'czr-smartloaded-on-init', delay : 800 } );
                              }
                        }
                        $_parentGridItem.one( 'click', function() {
                              self._fireGalleryCarousel( $_gal );
                        });
                        $_parentGridItem.one( 'smartload czr-is-in-window', function() {
                              if ( czrapp.base.matchMedia( 1024 ) )//<= tablets in landscape mode
                                return;

                              if ( czrapp.userXP.isScrolling() ) {
                                    czrapp.$_body.one( 'scrolling-finished', function() {
                                          self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                                    });
                              } else {
                                    self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                              }
                        });
                  });
            },
            _fireGalleryCarousel : function( $_gallery ) {
                  var _cellSelector = '.carousel-cell',
                      self = this;
                  if ( ! ( $_gallery instanceof $ ) || 1 > $_gallery.length ) {
                        czrapp.errorLog( '_fireGalleryCarousel : the passed element is not printed in the DOM');
                        return;
                  }
                  if ( $_gallery.data( 'czr-gallery-setup' ) )
                    return;

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        self._smartLoadFlickityImg({
                              sliderEl : $_gallery,
                              cellSelector : _cellSelector,
                              scheduleLoading : false
                        });
                  }
                  if ( _.isUndefined( $_gallery.data('flickity') ) ) {
                        var _is_single_slide = 1 == $_gallery.find( _cellSelector ).length,
                            _hasPageDots    = ! _is_single_slide && $_gallery.data( 'has-dots' );

                        self.czrFlickity( $_gallery, {
                              prevNextButtons: false,
                              wrapAround: true,
                              imagesLoaded: true,
                              setGallerySize: false,
                              cellSelector: _cellSelector,
                              accessibility: false,
                              dragThreshold: 10,
                              lazyLoad: false,
                              freeScroll: false,
                              pageDots: _hasPageDots,
                              draggable: ! _is_single_slide,
                        });
                        $_gallery.find( _cellSelector ).each( function() {
                              $(this).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    onInit : ! czrapp.localized.imgSmartLoadsForSliders,
                                    oncustom : ['smartload']
                              } );
                        });
                  }
                  $_gallery.data( 'czr-gallery-setup', true );
            },


            fireRelatedPostsCarousel : function() {
                  var self = this;
                  self.czrFlickity( $( self.slidersSelectorMap.relatedPosts ), {
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
            },


            fireMainSlider : function() {
                  var self = this,
                      $_main_slider = $(self.slidersSelectorMap.mainSlider),
                      _cellSelector = '.carousel-cell',
                      $_firstcell = $_main_slider.find( _cellSelector ).first();

                  if ( 1 > $_firstcell.length )
                    return;
                  $_main_slider.find( _cellSelector ).each( function() {
                        var _isSmartLoadCandidateImg = 0 < $(this).find('img').length && 0 === $(this).find('img').attr('src').indexOf('data');
                        $(this).centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : [ 'simple_load', 'smartload', 'refresh-centering-on-select' ],
                              defaultCSSVal : { width : '100%' , height : 'auto' },
                              useImgAttr : true,
                              zeroTopAdjust: 0
                        } );
                  });
                  $_main_slider.on( 'czr-flickity-ready.flickity', function() {
                        _.delay( function() {
                              $(this).on( 'select.flickity', function() {
                                    if ( $_main_slider.data('flickity').selectedElement && 1 == $( $_main_slider.data('flickity').selectedElement ).length ) {
                                          $( $_main_slider.data('flickity').selectedElement ).trigger( 'refresh-centering-on-select');
                                    }
                              });
                        }, 500 );
                  });

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        this._smartLoadFlickityImg( { sliderEl : $_main_slider, cellSelector : _cellSelector });
                  }
                  setTimeout( function() {
                        $_main_slider.prevAll('.czr-slider-loader-wrapper').fadeOut();
                  }, 300 );
                  if ( $_main_slider.length > 0 ) {
                        var _is_single_slide = 1 == $_main_slider.find( _cellSelector ).length,
                            _autoPlay        = $_main_slider.data('slider-delay'),
                            _hasPageDots    = !_is_single_slide && $_main_slider.data( 'has-dots' );

                        _autoPlay           =  ( _.isNumber( _autoPlay ) && _autoPlay > 0 ) ? _autoPlay : false;

                        self.czrFlickity( $_main_slider, {
                            prevNextButtons: false,
                            pageDots: _hasPageDots,
                            draggable: !_is_single_slide,

                            wrapAround: true,

                            imagesLoaded: true,

                            setGallerySize: false,
                            cellSelector: _cellSelector,

                            dragThreshold: 10,

                            autoPlay: _autoPlay, // {Number in milliseconds }

                            accessibility: false,
                        });
                  }
                  return this;
            },
            _smartLoadFlickityImg : function( params ) {
                  var self = this;
                  if ( ! _.isObject( params )  ) {
                        czrapp.errorLog( '_smartLoadFlickityImg params should be an object' );
                        return;
                  }
                  params = _.extend( {
                      sliderEl : {},
                      cellSelector : '.carousel-cell',
                      scheduleLoading : true
                  }, params );

                  if ( ! ( params.sliderEl instanceof $ ) || 1 > params.sliderEl.length )
                    return;

                  params.sliderEl.on( 'czr-flickity-ready.flickity', function() {
                        params.sliderEl.find( params.cellSelector ).each( function() {
                              if ( ! $(this).data('czr_smartLoaded') ) {
                                    $(this).find('img').removeClass('tc-smart-load-skip');
                              }
                              if ( $(this).hasClass( 'is-selected') && ! $(this).data('czr_smartLoaded') ) {
                                    $(this).imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( 'czr-smartloaded-on-init');
                              }
                        });

                        if ( ! params.scheduleLoading ) {
                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-init' } );
                              });
                        } else {
                              self._scheduleLoadingScenarios( params );
                        }
                  });//on flickity ready
                  params.sliderEl.on( 'smartload', params.cellSelector , function() {
                        self._maybeRemoveLoader.call( $(this) );
                  });
            },//_smartLoadFlickityImg
            _scheduleLoadingScenarios : function( params ) {
                  var self = this;
                  params.sliderEl.data( 'czr_smartload_scheduled', $.Deferred().done( function() {
                        params.sliderEl.addClass('czr-smartload-scheduled');
                  }) );
                  var _isSliderDataSetup = function() {
                        return 1 <= params.sliderEl.length && ! _.isUndefined( params.sliderEl.data( 'czr_smartload_scheduled' ) );
                  };
                  params.sliderEl.data( 'czr_schedule_select',
                        $.Deferred( function() {
                              var dfd = this;
                              params.sliderEl.parent().one( 'click staticClick.flickity pointerDown.flickity dragMove.flickity', function() {
                                    dfd.resolve();
                              } );
                              _.delay( function() {
                                    if ( 'pending' == dfd.state() ) {
                                          params.sliderEl.one( 'select.flickity' , function() {
                                                dfd.resolve();
                                          } );
                                    }
                              }, 2000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-select' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_select' )
                  params.sliderEl.data( 'czr_schedule_scroll_resize',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    czrapp.$_window.one( 'scroll resize', function() {
                                          dfd.resolve();
                                    });
                              }, 5000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-scroll' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_scroll_resize' )
                  params.sliderEl.data( 'czr_schedule_autoload',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() { dfd.resolve(); }, 10000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-auto-smartloaded' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );
            },
            _smartLoadCellImg : function( params ) {
                  params = _.extend( {
                     el : {},
                     ev : 'czr-smartloaded',
                     delay : 0
                  }, params || {} );

                  var _event_ = params.ev,
                      self = this,
                      $_cell = params.el;
                  if ( ! ( $_cell instanceof $ ) || 1 > $_cell.find('img[data-src], img[data-smartload]').length )
                    return;
                  if ( ! $_cell.data( 'czr_smartLoaded' ) ) {
                        if ( 1 > $_cell.find('.czr-css-loader').length ) {
                              $_cell.append( self._css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                        }
                        _.delay( function() {
                              $_cell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( _event_ );
                        }, params.delay );

                        $_cell.data( 'czr_loader_timer' , $.Deferred( function() {
                              var self = this;
                              _.delay( function() {
                                    self.resolve();
                              }, 2000 );
                              return this.promise();
                        }) );
                        $_cell.data( 'czr_loader_timer' ).done( function() {
                              self._maybeRemoveLoader.call( $_cell );
                        });
                  }
            },
            _maybeRemoveLoader : function() {
                  if ( ! ( $(this) instanceof $ ) )
                    return;

                  $(this).find('.czr-css-loader').fadeOut( {
                        duration: 'fast',
                        done : function() { $(this).remove();}
                  } );
            },
            _parallax : function() {
                  var $_parallax_carousel  = $(this),
                        _parallax_data_map = ['parallaxRatio', 'parallaxDirection', 'parallaxOverflowHidden', 'backgroundClass', 'matchMedia'],
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
                  if ( ! _flickity )
                    return;

                  if ( 'previous' == side ) {
                        _flickity.previous();
                  }
                  else if ( 'next' == side ) {
                        _flickity.next();
                  }

            },
            _slider_arrows_enable_toggler: function() {

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
            _move_background_link_inside : function() {
                  var $_flickity_slider = $(this),
                      $_bg_link = $_flickity_slider.closest('.entry-media__wrapper').children('.bg-link');

                  if ( $_bg_link.length > 0 ) {
                        $(this).find( '.flickity-viewport' ).prepend( $_bg_link );
                  }
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp );var czrapp = czrapp || {};

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
              var _resizeReact = function( to, from, params ) {
                    params = params || {};
                    if ( params.emulate ) {
                          self.isResizing( true );
                    } else {
                          self.isResizing( self._isMobile ? Math.abs( from - to ) > 2 : Math.abs( from - to ) > 0 );
                    }
                    clearTimeout( $.data( this, 'resizeTimer') );
                    $.data( this, 'resizeTimer', setTimeout(function() {
                          self.isResizing( false );
                    }, 50 ) );
              };
              self.windowWidth.bind( _resizeReact );
              czrapp.$_window.on( 'czr-resize', function() { _resizeReact( null, null, { emulate : true } ); } );
              self.isResizing.bind( function( is_resizing ) {
                    czrapp.$_body.toggleClass( 'is-resizing', is_resizing );
              });
              this.isScrolling.bind( function( to) {
                    czrapp.$_body.toggleClass( 'is-scrolling', to );
                    if ( ! to ) {
                          czrapp.trigger( 'scrolling-finished' );
                          czrapp.$_body.trigger( 'scrolling-finished' );
                    }
              });
              this.scrollPosition.bind( function( to, from ) {
                    czrapp.$_body.toggleClass( 'is-scrolled', to > 100 );
                    if ( to <= 50 ) {
                          czrapp.trigger( 'page-scrolled-top', {} );
                    }
                    self.scrollDirection( to >= from ? 'down' : 'up' );
              });
              czrapp.$_window.on('resize', _.throttle( function() { self.windowWidth( czrapp.$_window.width() ); }, 10 ) );
              czrapp.$_window.on('scroll', _.throttle( function() {
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
              this.navbarsWrapperSelector   = '.tc-header';
              this.$_navbars_wrapper        = $( this.navbarsWrapperSelector );
              this.$_topbar                 = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.topbar-navbar__wrapper') : false;
              this.$_primary_navbar         = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.primary-navbar__wrapper') : false;

              this.mobileMenuOpenedEvent    = 'show.czr.czrCollapse'; //('show' : start of the uncollapsing animation; 'shown' : end of the uncollapsing animation)
              this.mobileMenuStickySelector = '.mobile-sticky .mobile-nav__nav';

              this.stickyMenuWrapper        = false;
              this.stickyMenuDown           = new czrapp.Value( '_not_set_' );
              this.stickyHeaderThreshold    = 50;
              this.currentStickySelector    = new czrapp.Value( '' );//<= will be set on init and on resize
              this.hasStickyCandidate       = new czrapp.Value( false );
              this.stickyHeaderAnimating    = new czrapp.Value( false );
              this.animationPromise         = $.Deferred( function() { return this.resolve(); });
              this.userStickyOpt            = new czrapp.Value( self._setUserStickyOpt() );//set on init and on resize : stick_always, no_stick, stick_up
              this.isFixedPositionned       = new czrapp.Value( false );//is the candidate fixed ? => toggle the 'fixed-header-on' css class to the header
              this.stickyStage              = new czrapp.Value( '_not_set_' );
              this.shrinkBrand              = new czrapp.Value( false );//Toggle a class to maybe shrink the title or logo if the option is on
              this.currentStickySelector.bind( function( to ) {
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
                                        $_header_logo.on('load', function() {
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
                    self.shrinkBrand( isFixed );
              });
              this.shrinkBrand.bind( function( isShrinked ) {
                    czrapp.$_header.toggleClass( 'can-shrink-brand', isShrinked );
                    if ( ! isShrinked ) {
                          _.delay( function() {
                                if ( self.scrollPosition() < self.stickyHeaderThreshold ) {
                                      czrapp.$_header.trigger( 'czr-resize');
                                }
                          }, 400 );//<=400ms gives us enough room to finish the title or logo unshrinking animation
                    }
              });
              var _setStickynessStatesOnScroll = function( to, from ) {
                    if ( ! self.hasStickyCandidate() )
                      return;

                    to = to || self.scrollPosition();
                    from = from || self.scrollPosition();

                    var reachedTheTop = ( to == from ) && 0 === to;
                    if ( ! reachedTheTop ) {
                          if ( Math.abs( to - from ) <= 5 ) {
                            return;
                          }
                    }
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
                    self.shrinkBrand( self.isFixedPositionned() );
              } );
              var _maybeResetTop = function() {
                    if ( 'up' == self.scrollDirection() ) {
                          self._mayBeresetTopPosition();
                    }
              };
              czrapp.bind( 'scrolling-finished', _maybeResetTop );
              czrapp.bind( 'scrolling-finished', function() {
                    _.delay( function() {
                          _setStickynessStatesOnScroll();
                    }, 400);
              });
              czrapp.bind( 'topbar-collapsed', _maybeResetTop );
              self.stickyMenuDown.validate = function( newValue ) {
                    if ( ! self.hasStickyCandidate() )
                      return false;
                    if ( self._isMobileMenuExpanded() )
                      return this._value;

                    if ( self.scrollPosition() < self.stickyHeaderThreshold && ! newValue ) {
                          if ( ! self.isScrolling() ) {
                                czrapp.errorLog('Menu too close from top to be moved up');
                          }
                          return self.stickyMenuDown();
                    } else {
                          return newValue;
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
              self.isResizing.bind( function() {self._refreshOrResizeReact(); } );//resize();
              czrapp.$_header.on( 'refresh-sticky-header', function() { self._refreshOrResizeReact(); } );
              czrapp.$_body.on( self.mobileMenuOpenedEvent, self.mobileMenuStickySelector, function() {
                    var $_mobileMenu         = $(this),
                        $_mobileMenuNavInner = $_mobileMenu.find( '.mobile-nav__inner' );

                    if ( $_mobileMenu.length > 0 ) {
                          var _winHeight = 'undefined' !== typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
                              _maxHeight = _winHeight - $_mobileMenu.closest( '.mobile-nav__container' ).offset().top + czrapp.$_window.scrollTop();

                          $_mobileMenuNavInner.css( 'max-height', _maxHeight + 'px'  );
                    }
              });
              self._setStickySelector();
              this.topStickPoint          = new czrapp.Value( self._getTopStickPoint() );
              if ( ! self._isMobile() && self.hasStickyCandidate() ) {
                    self._adjustDesktopTopNavPaddingTop();
              }

        },//stickify
        _animate : function( args ) {
              var dfd = $.Deferred(),
                  self = this,
                  $menu_wrapper = ! args.menu_wrapper.length ? czrapp.$_header.find( self.currentStickySelector() ) : args.menu_wrapper;


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
                    var sticky_menu_id = _.isString( $menu_wrapper.attr('data-menu-id') ) ? $menu_wrapper.attr('data-menu-id') : '';
                    if ( czrapp.userXP.isResponsive() && 1 == $('.mobile-navbar__wrapper').length ) {
                          if ( self._isMobileMenuExpanded() ) {
                                self._toggleMobileMenu().done( function() {
                                      _do().done( function() { self._mayBeresetTopPosition(); } );
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
        _isMobileMenuExpanded : function() {
              var $mobile_menu = $('.mobile-navbar__wrapper');
              if ( 1 != $('.mobile-navbar__wrapper').length )
                return false;

              return 1 == $mobile_menu.find('.ham-toggler-menu').length && "true" == $mobile_menu.find('.ham-toggler-menu').attr('aria-expanded');
        },
        _toggleMobileMenu : function() {
            return $.Deferred( function() {
                  var $mobile_menu = $('.mobile-navbar__wrapper'),
                      _dfd_ = this;
                  if ( 1 == $mobile_menu.length ) {
                        $mobile_menu.find('.ham-toggler-menu').trigger('click');
                        _.delay( function() {
                              _dfd_.resolve();
                        }, 350 );
                  } else {
                        _dfd_.resolve();
                  }
            }).promise();
        },
        _setStickySelector : function() {
              var self = this,
                  _selector = false;
              _.each( self.stickyCandidatesMap, function( _params ) {
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
        },

        _refreshOrResizeReact : function() {
              var  self = this;
              self.userStickyOpt( self._setUserStickyOpt() );
              self._setStickySelector();
              self.topStickPoint( self._getTopStickPoint() );
              self._pushPrimaryNavBarDown();
              if ( self._isMobileMenuExpanded() ) {
                    self._toggleMobileMenu();
              }

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
        }

  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
        mayBePrintFrontNote : function() {
              if ( czrapp.localized && _.isUndefined( czrapp.localized.frontNotifications ) )
                return;
              if ( _.isEmpty( czrapp.localized.frontNotifications ) || ! _.isObject( czrapp.localized.frontNotifications ) )
                return;

              var self = this;
              czrapp.frontNotificationVisible = new czrapp.Value( false );
              czrapp.frontNotificationRendered = false;
              _.each( czrapp.localized.frontNotifications, function( _notification ) {
                    if ( ! _.isUndefined( czrapp.frontNotification ) )
                      return;

                    if ( ! _.isObject( _notification ) )
                      return;
                    _notification = _.extend( {
                          enabled : false,
                          content : '',
                          dismissAction : '',
                          ajaxUrl : czrapp.localized.ajaxUrl
                    }, _notification );
                    if ( _notification.enabled ) {
                          czrapp.frontNotification = new czrapp.Value( _notification );
                    }

              });
              czrapp.frontNotificationVisible.bind( function( visible ) {
                      return self._toggleNotification( visible );//returns a promise()
              }, { deferred : true } );

              czrapp.frontNotificationVisible( true );
        },//mayBePrintFrontNote()


        _toggleNotification : function( visible ) {
              var self = this,
                  dfd = $.Deferred();

              if ( czrapp.frontNotificationRendered && czrapp.frontNotificationVisible() )
                return dfd.resolve().promise();

              var _hideAndDestroy = function() {
                    return $.Deferred( function() {
                          var _dfd_ = this,
                              $notifWrap = $('#bottom-front-notification', '#footer');
                          if ( 1 == $notifWrap.length ) {
                                $notifWrap.css( { bottom : '-100%' } );
                                _.delay( function() {
                                      $notifWrap.remove();
                                      czrapp.$_body.find('.czr-btt.czr-btta').fadeIn('slow');
                                      czrapp.frontNotificationRendered = false;
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
                    if ( _.isUndefined( czrapp.frontNotification ) || ! _.isFunction( czrapp.frontNotification ) || ! _.isObject( czrapp.frontNotification() ) )
                        return _dfd_.resolve().promise();
                    $.Deferred( function() {
                          var dfd = this,
                              _notifHtml = czrapp.frontNotification().content,
                              _wrapHtml = [
                                    '<div id="bottom-front-notification">',
                                      '<div class="note-content">',
                                        '<span class="fas fa-times close-note" title="' + czrapp.localized.i18n['Permanently dismiss'] + '"></span>',
                                      '</div>',
                                    '</div>'
                              ].join('');

                          if ( 1 == $footer.length && ! _.isEmpty( _notifHtml ) ) {
                                $.when( $footer.append( _wrapHtml ) ).done( function() {
                                    $(this).find( '.note-content').prepend( _notifHtml );
                                    czrapp.$_body.find('.czr-btt.czr-btta').fadeOut('slow');
                                    czrapp.frontNotificationRendered = true;
                                });

                                _.delay( function() {
                                      $('#bottom-front-notification', '#footer').css( { bottom : 0 } );
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
                                                  czrapp.frontNotificationVisible( false ).done( function() {
                                                        czrapp.doAjax( {
                                                              action: czrapp.frontNotification().dismissAction,
                                                              withNonce : true,
                                                              ajaxUrl : czrapp.frontNotification().ajaxUrl
                                                        });
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
                          czrapp.frontNotificationVisible( false );//should be already false
                          dfd.resolve();
                    });
              }
              _.delay( function() {
                          czrapp.frontNotificationVisible( false );
                    },
                    45000
              );
              return dfd.promise();
        }//_toggleNotification
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);// global CZRParams
var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {
      outline: function() {
            if ( 'function' == typeof( tcOutline ) ) {
                tcOutline();
            }
      },
      variousHoverActions : function() {
            if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
                return;
            $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
            $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
            czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front, .fpc-widget-front', _toggleThisHover );
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
                  setTimeout(
                        function(){
                            if ( $_el.val() || ( evt && ( 'focusin' == evt.type || 'focus' == evt.type ) ) ) {
                                  $_parent.addClass( _focus_class );
                            } else {
                                  $_parent.removeClass( _focus_class );
                            }
                        },
                        50
                  );
            };

            czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );
            $(_inputs).trigger( 'in-focus-load.czr-focus' );
            czrapp.$_body.on( 'click', '.' + _focusable_class + ' .icn-close', function(e) {
                  e.preventDefault();
                  e.stopPropagation();

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
            var //_mobile_viewport = 992,
                self = this;
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.nav__woocart', function() {
                  var $_el = $(this);
                  var _do = function() {
                        var $_to_scroll = $_el.find('.product_list_widget');
                        if ( $_to_scroll.length && ! $_to_scroll.hasClass('mCustomScrollbar') ) {
                              $_to_scroll.mCustomScrollbar({
                                    theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                              });
                        }
                  };
                  if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                        _do();
                  } else {
                        self.maybeLoadCustomScrollAssets().done( function() {
                            _do();
                       });
                  }
            });

      },
      headerSearchToLife : function() {
            var self = this,

                _search_toggle_event            = 'click',

                _search_overlay_toggler_sel     = '.search-toggle_btn.czr-overlay-toggle_btn',
                _search_overlay_toggle_class    = 'full-search-opened czr-overlay-opened',

                transitionEnd                   = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd',
                _transitioning_el_sel           = '.czr-overlay .overlay-content',
                _search_input_sel               = '.czr-search-field',
                _search_overlay_open_class      = 'full-search-opened',

                _search_dropdown_wrapper_sel    = '.mobile-utils__wrapper',
                _search_dropdown_button_sel     = '.search-toggle_btn.czr-dropdown',
                _search_dropdown_menu_sel       = '.nav__search .czr-dropdown-menu',
                _search_dropdown_menu_input_sel = '.czr-search-field',
                _search_dropdown_expanded_class = 'show',

                _mobile_menu_to_close_sel       = '.ham-toggler-menu:not(.czr-collapsed)',
                _mobile_menu_close_event        = 'click.czr.czrCollapse',
                _mobile_menu_opened_event       = 'show.czr.czrCollapse', //('show' : start of the uncollapsing animation; 'shown' : end of the uncollapsing animation)
                _mobile_menu_sel                = '.mobile-nav__nav';
            czrapp.$_body.on( _search_toggle_event, _search_overlay_toggler_sel, function(evt) {
                  evt.preventDefault();
                  var search_icon = $(_search_overlay_toggler_sel),
                      rect = search_icon[0].getBoundingClientRect(),
                      winWidth = $(window).width(),
                      isLeftSide = rect.left < winWidth/2;

                  czrapp.$_body.removeClass( 'search-icon-left').removeClass('search-icon-right');
                  czrapp.$_body.toggleClass( isLeftSide ? 'search-icon-left' : 'search-icon-right' );
                  czrapp.$_body.toggleClass( _search_overlay_toggle_class );
            });
            czrapp.$_body.on( transitionEnd, _transitioning_el_sel, function( evt ) {
                  if ( $( _transitioning_el_sel ).get()[0]  != evt.target )
                        return;

                  if ( czrapp.$_body.hasClass( _search_overlay_open_class ) ) {
                        $(this).find(  _search_input_sel ).focus();
                  }
                  else {
                        $(this).find(  _search_input_sel ).blur();
                  }
            });
            self.headerSearchExpanded = new czrapp.Value( false );
            self.headerSearchExpanded.bind( function( exp ) {
                  return $.Deferred( function() {
                        var _dfd = this;
                        $(  _search_dropdown_button_sel, _search_dropdown_wrapper_sel )
                                  .toggleClass( _search_dropdown_expanded_class, exp )
                                  .attr('aria-expanded', exp );
                        if ( exp ) {
                              $( _mobile_menu_to_close_sel ).trigger( _mobile_menu_close_event );
                        }

                        $(  _search_dropdown_menu_sel, _search_dropdown_wrapper_sel )
                            .attr('aria-expanded', exp )
                            .stop()[ ! exp ? 'slideUp' : 'slideDown' ]( {
                                  duration : 250,
                                  complete : function() {
                                    if ( exp ) {
                                          $( _search_dropdown_menu_input_sel, $(this) ).focus();
                                    }
                                    _dfd.resolve();
                                  }
                            } );
                  }).promise();
            }, { deferred : true } );
            czrapp.setupDOMListeners(
                  [
                        {
                              trigger   : _search_toggle_event,
                              selector  : _search_dropdown_button_sel,
                              actions   : function() {
                                    czrapp.userXP.headerSearchExpanded( ! czrapp.userXP.headerSearchExpanded() );
                              }
                        },
                  ],//actions to execute
                  { dom_el: $( _search_dropdown_wrapper_sel ) },//dom scope
                  czrapp.userXP //instance where to look for the cb methods
            );
            czrapp.userXP.windowWidth.bind( function() {
                  self.headerSearchExpanded( false );
                  _.delay( function() {
                     czrapp.$_body.removeClass( _search_overlay_toggle_class );
                  }, 250 );
            });
            czrapp.$_body.on( _mobile_menu_opened_event, _mobile_menu_sel, function() {
                  self.headerSearchExpanded( false );
                  _.delay( function() {
                     czrapp.$_body.removeClass( _search_overlay_toggle_class );
                  }, 250 );
            });
            if ( czrapp.userXP.stickyHeaderAnimating ) {
                  czrapp.userXP.stickyHeaderAnimating.bind( function() {
                        self.headerSearchExpanded( false );
                        _.delay( function() {
                           czrapp.$_body.removeClass( _search_overlay_toggle_class );
                        }, 250 );
                  });
            }
      },//toggleHeaderSearch
      maybeLoadCustomScrollAssets : function() {
            var dfd = $.Deferred();
            if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                  return dfd.resolve().promise();
            } else {
                  $('head').append( $('<link/>' , {
                              rel : 'stylesheet',
                              id : 'czr-custom-scroll-bar',
                              type : 'text/css',
                              href : czrapp.localized.assetsPath + 'css/jquery.mCustomScrollbar.min.css'
                        }) );
                  $.ajax( {
                        url : ( czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.min.js'),
                        cache : true,
                        dataType: "script"
                  }).done(function() {
                        if ( 'function' != typeof $.fn.mCustomScrollbar )
                          return dfd.rejected();
                        dfd.resolve();
                  }).fail( function() {
                        czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                  });
            }
            return dfd.promise();
      },
      smoothScroll: function() {
            if ( $('body').hasClass( 'czr-infinite-scroll-on' ) || ( czrapp.localized.SmoothScroll && czrapp.localized.SmoothScroll.Enabled && ! czrapp.base.matchMedia( 1024 ) ) ) {
                  smoothScroll( czrapp.localized.SmoothScroll.Options );
            }
      },

      magnificPopup : function() {},
      attachmentsFadeEffect : function() {
            $( '.attachment-image-figure img' ).delay(500).addClass( 'opacity-forced' );
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
              var _fp_offsets = [], _element_index, _fp_index;
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
            var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
                self = this,
                $_links = $('a[data-anchor-scroll="true"][href^="#"]').not( _excl_sels );
            if ( czrapp.localized.isAnchorScrollEnabled ) {
                $_links = $_links.add( '#tc-page-wrap a[href^="#"],#tc-sn a[href^="#"]').not( _excl_sels );
            }
            var   _links,
                  _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null;

            if ( _deep_excl ) {
                  _links = _.toArray($_links).filter( function ( _el ) {
                    return ( 2 == ( ['ids', 'classes'].filter(
                                  function( sel_type) {
                                      return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                                  } ) ).length
                          );
                  });
            }

            $(_links).on('click', function () {
                  var anchor_id = $(this).attr("href");
                  if ( ! $(anchor_id).length )
                    return;

                  if ('#' != anchor_id) {
                      $('html, body').animate({
                          scrollTop: $(anchor_id).offset().top
                      }, 700, czrapp.localized.isAnchorScrollEnabled ? 'easeOutExpo' : 'linear' ); //<= the jquery effect library ( for the easeOutExpo effect ) is only available when czr_fn_is_checked( czr_fn_opt( 'tc_link_scroll' ) ),
                  }
                  return false;
            });//click
      },
      gutenbergAlignfull : function() {
            var _isPage   = czrapp.$_body.hasClass( 'page' ),
                  _isSingle = czrapp.$_body.hasClass( 'single' ),
                  _coverImageSelector = '.czr-full-layout.czr-no-sidebar .entry-content .alignfull[class*=wp-block-cover]',
                  _alignFullSelector  = '.czr-full-layout.czr-no-sidebar .entry-content .alignfull[class*=wp-block]',
                  _alignTableSelector = [
                                    '.czr-boxed-layout .entry-content .wp-block-table.alignfull',
                                    '.czr-boxed-layout .entry-content .wp-block-table.alignwide',
                                    '.czr-full-layout.czr-no-sidebar .entry-content .wp-block-table.alignwide'
                                    ];
            if ( ! ( _isPage || _isSingle ) ) {
                  return;
            }

            if ( _isSingle ) {
                  _coverImageSelector = '.single' + _coverImageSelector;
                  _alignFullSelector  = '.single' + _alignFullSelector;
                  _alignTableSelector = '.single' + _alignTableSelector.join(',.single');
            } else {
                  _coverImageSelector = '.page' + _coverImageSelector;
                  _alignFullSelector  = '.page' + _alignFullSelector;
                  _alignTableSelector = '.page' + _alignTableSelector.join(',.page');
            }


            var _coverWParallaxImageSelector   = _coverImageSelector + '.has-parallax',
                  _classParallaxTreatmentApplied = 'czr-alignfull-p',
                  $_refWidthElement              = $('#tc-page-wrap'),
                  $_refContainedWidthElement     = $( '.container[role="main"]', $_refWidthElement );

            if ( $( _alignFullSelector ).length > 0 ) {
                  _add_alignelement_style( $_refWidthElement, _alignFullSelector, 'czr-gb-alignfull' );
                  if ( $(_coverWParallaxImageSelector).length > 0 ) {
                  _add_parallax_treatment_style();
                  }
                  czrapp.userXP.windowWidth.bind( function() {
                        _add_alignelement_style( $_refWidthElement, _alignFullSelector, 'czr-gb-alignfull' );
                        _add_parallax_treatment_style();
                  });
            }
            if ( $( _alignTableSelector ).length > 0 ) {
                  _add_alignelement_style( $_refContainedWidthElement, _alignTableSelector, 'czr-gb-aligntable' );
                  czrapp.userXP.windowWidth.bind( function() {
                        _add_alignelement_style( $_refContainedWidthElement, _alignTableSelector, 'czr-gb-aligntable' );
                  });
            }
            function _add_parallax_treatment_style() {
                  $( _coverWParallaxImageSelector ).each(function() {
                        $(this)
                              .css( 'left', '' )
                              .css( 'left', -1 * $(this).offset().left )
                              .addClass(_classParallaxTreatmentApplied);
                  });
            }
            function _add_alignelement_style( $_refElement, _selector, _styleId ) {
                  var newElementWidth = $_refElement[0].getBoundingClientRect().width,
                        $_style         = $( 'head #' + _styleId );

                  if ( 1 > $_style.length ) {
                        $_style = $('<style />', { 'id' : _styleId });
                        $( 'head' ).append( $_style );
                        $_style = $( 'head #' + _styleId );
                  }
                  $_style.html( _selector + '{width:'+ newElementWidth +'px}' );
            }
      },

      mayBeLoadFontAwesome : function() {
            jQuery( function() {
                  if ( ! CZRParams.deferFontAwesome )
                    return;
                  var $candidates = $('[class*=fa-]');
                  if ( $candidates.length < 1 )
                    return;
                  var _inject_in_progress = false;
                  var _inject = function(type) {
                      _inject_in_progress = true;
                      if ( $('head').find( '[href*="fontawesome-all.min.css"]' ).length > 0 )
                        return;
                      var link = document.createElement('link');
                      link.setAttribute('href', CZRParams.fontAwesomeUrl ); // assets/shared/fonts/fa/css/fontawesome-all.min.css?
                      link.setAttribute('id', 'czr-font-awesome');
                      link.setAttribute('rel', 'stylesheet' );
                      document.getElementsByTagName('head')[0].appendChild(link);
                  };
                  setTimeout( function() {
                        if ( !_inject_in_progress ) {_inject('timeout'); }
                  }, 3000 );

                  czrapp.$_window
                      .one('scroll', function() {
                          if ( !_inject_in_progress ) {_inject('scroll'); }
                      })
                      .one('mousemove', function() {
                          if ( !_inject_in_progress ) {_inject('mousemove'); }
                      });

            });
      },
      maybePreloadGoogleFonts : function() {
            if ( !window.CZRParams || !CZRParams.preloadGfonts || _.isEmpty(CZRParams.googleFonts) )
              return;
            var _hasPreloadSupport = function( browser ) {
                  var link = document.createElement('link');
                  var relList = link.relList;
                  if (!relList || !relList.supports)
                    return false;
                  return relList.supports('preload');
                },
                headTag = document.getElementsByTagName('head')[0],
                link = document.createElement('link'),
                _injectFinalAsset = function() {
                    var link = this;
                    link.setAttribute('rel', 'stylesheet');
                };

            link.setAttribute('href', '//fonts.googleapis.com/css?family=' + CZRParams.googleFonts + '&display=swap');
            link.setAttribute('rel', _hasPreloadSupport() ? 'preload' : 'stylesheet' );
            link.setAttribute('id', 'czr-gfonts-css-preloaded' );
            link.setAttribute('as', 'style');
            link.onload = function() {
                this.onload=null;
                _injectFinalAsset.call(link);
            };
            link.onerror = function(er) {
                console.log('Customizr preloadAsset error', er );
            };
            headTag.appendChild(link);

      }//maybePreloadGoogleFonts

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
      if ( this.$_page.length < 1 )
        return $(window).outerHeight(true);
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

    },//init()
    sideNavEventListener : function() {
      var self = this;
      czrapp.$_body.on( this._toggle_event, this._toggler_selector, function( evt ) {
        evt.preventDefault(); //<- avoid on link click reaction which adds '#' to the browser history
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

      czrapp.$_window.on('scroll', function( evt ) {
        self.sideNavEventHandler( evt, 'scroll');
      });

    },
    maybeLoadScript : function() {
          var dfd = $.Deferred();
          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                return dfd.resolve().promise();
          } else {
                if ( czrapp.base.scriptLoadingStatus.mCustomScrollbar && 'pending' == czrapp.base.scriptLoadingStatus.mCustomScrollbar.state() ) {
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.done( function() {
                            dfd.resolve();
                      });
                      return dfd.promise();
                }
                czrapp.base.scriptLoadingStatus.mCustomScrollbar = czrapp.base.scriptLoadingStatus.mCustomScrollbar || $.Deferred();
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
                $.ajax( {
                      url : _url,
                      cache : true,// use the browser cached version when availabl
                      dataType: "script"
                }).done(function() {
                      if ( 'function' != typeof $.fn.mCustomScrollbar )
                        return dfd.rejected();
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.resolve();

                      dfd.resolve();
                }).fail( function() {
                      czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                });
          }
          return dfd.promise();
    },
    sideNavEventHandler : function( evt, evt_name ) {
          var self = this;
          var _do = function() {
                switch ( evt_name ) {
                      case 'toggle':
                        if ( ! self._is_translating() )
                          self._toggle_callback( evt );
                      break;

                      case 'transitionend' :
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
      $( this._toggler_selector ).toggleClass( 'czr-collapsed' );
      $( this._sidenav_selector ).toggleClass( this._active_class );

    },
    _is_sn_on : function() {
      return $( this._sidenav_selector ).length > 0;
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
            };
            this.ClassName = {
              DROPDOWN                : 'czr-dropdown-menu',
              SHOW                    : 'show',
              PARENTS                 : 'menu-item-has-children',
              MCUSTOMSB               : 'mCustomScrollbar',
              ALLOW_POINTER_ON_SCROLL : 'allow-pointer-events-on-scroll'
            };

            this.Selector = {
              DATA_TOGGLE              : '[data-toggle="czr-dropdown"]',
              DATA_SHOWN_TOGGLE_LINK   : '.' +this.ClassName.SHOW+ '> a[data-toggle="czr-dropdown"]',
              HOVER_MENU               : '.czr-open-on-hover',
              CLICK_MENU               : '.czr-open-on-click',// selector used on vertical mobile menus
              HOVER_PARENT             : '.czr-open-on-hover .menu-item-has-children, .nav__woocart',
              CLICK_PARENT             : '.czr-open-on-click .menu-item-has-children',// selector used on vertical mobile menus
              HAS_SUBMENU              : '.menu-item-has-children',
              PARENTS                  : '.tc-header .menu-item-has-children',
              SNAKE_PARENTS            : '.regular-nav .menu-item-has-children',
              VERTICAL_NAV_ONCLICK     : '.czr-open-on-click .vertical-nav',
            };
    },
    dropdownMenuOnHover : function() {
            var _dropdown_selector = this.Selector.HOVER_PARENT,
                self               = this;

            enableDropdownOnHover();

            function _addOpenClass( evt ) {
              var $_el = $(this);
              var _debounced_addOpenClass = _.debounce( function() {
                if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
                  return false;

                if ( !$_el.hasClass(self.ClassName.SHOW) ) {
                      czrapp.$_body.addClass( self.ClassName.ALLOW_POINTER_ON_SCROLL );
                      if ( !czrapp.$_body.hasClass('is-touch-device') ) {
                            $_el.trigger( self.Event.SHOW )
                                .addClass(self.ClassName.SHOW)
                                .trigger(self.Event.SHOWN);
                      }
                      var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

                      if ( $_data_toggle.length ) {
                          $_data_toggle[0].setAttribute('aria-expanded', 'true');
                      }
                }

              }, 30);// april 2020 => this delay is important because when on touch device, the "is-touch-device" class must be added before this function is fired

              _debounced_addOpenClass();
            }

            function _removeOpenClass () {

              var $_el = $(this);
              var _debounced_removeOpenClass = _.debounce( function() {
                if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
                      if ( !czrapp.$_body.hasClass('is-touch-device') ) {
                            $_el.trigger( self.Event.HIDE )
                                .removeClass(self.ClassName.SHOW)
                                .trigger( self.Event.HIDDEN );
                      }
                      if ( $_el.closest( self.Selector.HOVER_MENU ).find( '.' + self.ClassName.SHOW ).length < 1 ) {
                        czrapp.$_body.removeClass( self.ClassName.ALLOW_POINTER_ON_SCROLL );
                      }

                      var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

                      if ( $_data_toggle.length ) {
                          $_data_toggle[0].setAttribute('aria-expanded', 'false');
                      }
                }

              }, 30);// april 2020 => this delay is important because when on touch device, the "is-touch-device" class must be added before this function is fired

              _debounced_removeOpenClass();
            }

            function enableDropdownOnHover() {
                  czrapp.$_body.on('touchstart', function() {
                        if ( !$(this).hasClass('is-touch-device') ) {
                              $(this).addClass('is-touch-device');
                        }
                  });
                  czrapp.$_body.on( 'mouseenter', _dropdown_selector, _addOpenClass );
                  czrapp.$_body.on( 'mouseleave', _dropdown_selector , _removeOpenClass );
            }

    },//dropdownMenuOnHover









    dropdownOpenGoToLinkOnClick : function() {
          var self = this;
          czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE_LINK, function(evt) {
                var $_el = $(this);
                if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
                  return false;

                evt.preventDefault();

                var _href = $_el.attr( 'href' );

                if ( _href && '#' != _href ) {
                  window.location = _href;
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
                              $( self.Selector.SNAKE_PARENTS+'.'+self.ClassName.SHOW)
                                  .trigger(self.Event.PLACE_ME);
                              doingAnimation = false;
                            });
                      }

              });

          czrapp.$_body
              .on( this.Event.PLACE_ALL, function() {
                          $( self.Selector.SNAKE_PARENTS )
                              .trigger(self.Event.PLACE_ME);
              })
              .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.SNAKE_PARENTS, function(evt) {
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
            $_el.css( 'overflow', 'hidden' );
            $_dropdown.css( {
              'zIndex'  : '-100',
              'display' : 'block'
            });

            _maybe_move( $_dropdown, $_el );
            $_dropdown.css({
              'zIndex'  : '',
              'display' : ''
            });
            $_el.css( 'overflow', '' );
          }


          function _maybe_move( $_dropdown, $_el ) {
              var Direction          = czrapp.isRTL ? {
                        _DEFAULT          : 'left',
                        _OPPOSITE         : 'right'
                  } : {
                        _DEFAULT          : 'right',
                        _OPPOSITE         : 'left'
                  },
                  ClassName          = {
                        OPEN_PREFIX       : 'open-',
                        DD_SUBMENU        : 'czr-dropdown-submenu',
                        CARET_TITLE_FLIP  : 'flex-row-reverse',
                        CARET             : 'caret__dropdown-toggler'
                  },
                  _caret_title_maybe_flip = function( $_el, _direction, _old_direction ) {
                        $.each( $_el, function() {
                            var $_el               = $(this),
                                $_a                = $_el.find( self.Selector.DATA_TOGGLE ).first(),
                                $_caret            = $_el.find( '.' + ClassName.CARET).first();
                            if ( 1 == $_caret.length ) {
                                  $_caret.removeClass( ClassName.OPEN_PREFIX + _old_direction ).addClass( ClassName.OPEN_PREFIX + _direction );
                                  if ( 1 == $_a.length ) {
                                        $_a.toggleClass( ClassName.CARET_TITLE_FLIP, _direction == Direction._OPPOSITE  );
                                  }
                            }
                        });
                  },
                  _setOpenDirection       = function( _direction ) {
                        var _old_direction = _direction == Direction._OPPOSITE ? Direction._DEFAULT : Direction._OPPOSITE;
                        $_dropdown.removeClass( ClassName.OPEN_PREFIX + _old_direction ).addClass( ClassName.OPEN_PREFIX + _direction );

                        if ( $_el.hasClass( ClassName.DD_SUBMENU ) ) {
                              _caret_title_maybe_flip( $_el, _direction, _old_direction );
                              _caret_title_maybe_flip( $_dropdown.children( '.' + ClassName.DD_SUBMENU ), _direction, _old_direction );
                        }
                  };
              if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( ClassName.OPEN_PREFIX + Direction._OPPOSITE ) ) {
                    _setOpenDirection( Direction._OPPOSITE );
              } else {
                    _setOpenDirection( Direction._DEFAULT );
              }
              if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {
                    _setOpenDirection( 'left' );
              } else if ( $_dropdown.offset().left < 0 ) {
                    _setOpenDirection( 'right' );
              }
          }
    },//dropdownPlacement
    dropdownOnClickVerticalNav : function() {
        var self = this;
        czrapp.$_body
              .on( self.Event.CLICK, [self.Selector.VERTICAL_NAV_ONCLICK, self.Selector.HAS_SUBMENU, 'a'].join(' '), function(evt) {
                    if ( '#' === $(this).attr('href') || !$(this).attr('href') ) {
                          evt.preventDefault();
                          evt.stopPropagation();
                          $(this).closest( '.nav__link-wrapper' ).children(self.Selector.DATA_TOGGLE).trigger( self.Event.CLICK );
                    }
              })
              .on( self.Event.SHOW +' '+ self.Event.HIDE, self.Selector.VERTICAL_NAV_ONCLICK, function(evt) {
                        $(evt.target).children('.'+self.ClassName.DROPDOWN)
                                        .stop()[ 'show' == evt.type ? 'slideDown' : 'slideUp' ]({
                                              duration : 300,
                                              complete: function() {
                                                    if ( 'show' == evt.type ) {
                                                          var $_customScrollbar = $(this).closest(  '.'+self.ClassName.MCUSTOMSB );
                                                          if ( $_customScrollbar.length > 0 ) {
                                                                $_customScrollbar.mCustomScrollbar( 'scrollTo', $(this) );
                                                          }
                                                    }
                                              }
                                        });
              });
    },//dropdownOnClickVerticalNav


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
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY, // 'click.czr.czrDropdown.data-api'
      FOCUSOUT_DATA_API: 'focusout' + EVENT_KEY + DATA_API_KEY,
      FOCUSIN_DATA_API: 'focusin' + EVENT_KEY + DATA_API_KEY,
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
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)',
      PARENTS : '.menu-item-has-children',
    };

    var czrDropdown = function ($) {

      var czrDropdown = function () {
        function czrDropdown(element) {
              _classCallCheck(this, czrDropdown);

              this._element = element;

              this._addEventListeners();
        }
        czrDropdown.prototype.toggle = function(evt) {
              if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
                return false;
              }

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
              $(this).trigger( "focus" );
              this.setAttribute('aria-expanded', 'true');

              $(parent).toggleClass(ClassName.SHOW);
              $(parent).trigger($.Event(Event.SHOWN, relatedTarget));

              return false;
        };//toggle







        czrDropdown.prototype.dispose = function() {
              $.removeData(this._element, DATA_KEY);
              $(this._element).off(EVENT_KEY);
              this._element = null;
        };
        czrDropdown.prototype._addEventListeners = function() {
              $(this._element).on(Event.CLICK, this.toggle);
        };
        czrDropdown._jQueryInterface = function(config) {
              return this.each(function () {
                var data = $(this).data(DATA_KEY);

                if (!data) {
                  data = new czrDropdown(this);
                  $(this).data(DATA_KEY, data);
                }

                if (typeof config === 'string') {
                  if ( _.isUndefined( data[config] ) ) {
                    throw new Error('No method named "' + config + '"');
                  }
                  data[config].call(this);
                }
              });
        };



        czrDropdown._clearMenus = function(event, _parentsToNotClear ) {

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



        czrDropdown._getParentFromElement = function(element) {
              var _parentNode = void 0;
              var $_parent = $(element).closest(Selector.PARENTS);

              if ( $_parent.length ) {
                _parentNode = $_parent[0];
              }

              return _parentNode || element.parentNode;
        };



        czrDropdown._dataApiFocusinHandler = function(evt) {
              var self = this;
              _.delay( function() {
                var parent = czrDropdown._getParentFromElement(self),
                    isActive = $(parent).hasClass(ClassName.SHOW);
                if ( ! isActive ) {
                  $(self).trigger('click');
                }
          }, 150); // a little delay so that we avoid a race condition when both focus and click events are triggered on mouse click.
        };



        czrDropdown._dataApiKeydownHandler = function(event) {
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

              items[index].trigger( "focus" );
        };


        _createClass(czrDropdown, null, [{
          key: 'VERSION',
          get: function() {
            return VERSION;
          }
        }]);

        return czrDropdown;
      }();
      $(document)
        .on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler)
        .on(Event.KEYDOWN_DATA_API, Selector.MENU, czrDropdown._dataApiKeydownHandler)
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API + Event.FOCUSOUT_DATA_API , czrDropdown._clearMenus)
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle) //click on [data-toggle="czr-dropdown"]
        .on(Event.FOCUSIN_DATA_API, Selector.NAVBAR_NAV + ' ' + Selector.DATA_TOGGLE, czrDropdown._dataApiFocusinHandler)
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
            var _evt = document.createEvent('Event');
            _evt.initEvent('czrapp-is-ready', true, true); //can bubble, and is cancellable
            document.dispatchEvent(_evt);
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
            $(function () {
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
})( czrapp, jQuery, _ );var czrapp = czrapp || {};
( function ( czrapp ) {
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
                            'centerInfinity',
                            'imgSmartLoad',
                            'lightBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnCzrReady',//<= fires all carousels : main, galleries, related posts + center images
                      ]
                },
                dropdowns : {
                      ctor  : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [
                            'initOnCzrReady',
                            'dropdownMenuOnHover',
                            'dropdownOpenGoToLinkOnClick',
                            'dropdownPlacement',//snake
                            'dropdownOnClickVerticalNav'
                      ]
                },
                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'setupUIListeners',//<= setup various observable values like this.isScrolling, this.scrollPosition, ...

                            'stickifyHeader',
                            'gutenbergAlignfull',

                            'outline',

                            'variousHoverActions',
                            'formFocusAction',
                            'variousHeaderActions',
                            'headerSearchToLife',

                            'smoothScroll',

                            'attachmentsFadeEffect',

                            'onEscapeKeyPressed',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',

                            'mayBePrintFrontNote',
                            'mayBeLoadFontAwesome',

                            'maybePreloadGoogleFonts'
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

})( czrapp );