//@global TCParams
var czrapp = czrapp || {};

/*************************
* JS LOG UTILITIES
*************************/
(function($, czrapp) {
      //Utility : print a js log on front
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

      //CONSOLE / ERROR LOG
      //@return [] for console method
      //@bgCol @textCol are hex colors
      //@arguments : the original console arguments
      czrapp._prettyfy = function( args ) {
            var _defaults = {
                  bgCol : '#5ed1f5',
                  textCol : '#000',
                  consoleArguments : [],
                  prettyfy : true
            };
            args = _.extend( _defaults, args );

            var _toArr = Array.from( args.consoleArguments );

            //if the array to print is not composed exclusively of strings, then let's stringify it
            //else join()
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

      //Dev mode aware and IE compatible api.consoleLog()
      czrapp.consoleLog = function() {
            if ( ! czrapp.localized.isDevMode )
              return;
            //fix for IE, because console is only defined when in F12 debugging mode in IE
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { consoleArguments : arguments } ) );
      };

      czrapp.errorLog = function() {
            //fix for IE, because console is only defined when in F12 debugging mode in IE
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { bgCol : '#ffd5a0', textCol : '#000', consoleArguments : arguments } ) );
      };

      //encapsulates a WordPress ajax request in a normalize method
      //@param query = { ... }
      czrapp.doAjax = function( query ) {
            //do we have a query ?
            query = query || ( _.isObject( query ) ? query : {} );

            var ajaxUrl = czrapp.localized.ajaxUrl,
                nonce = czrapp.localized.czrFrontNonce,//{ 'id' : '', 'handle' : '' }
                dfd = $.Deferred(),
                _query_ = _.extend( {
                            action : ''
                      },
                      query
                );

            // HTTP ajaxurl when site is HTTPS causes Access-Control-Allow-Origin failure in Desktop and iOS Safari
            if ( "https:" == document.location.protocol ) {
                  ajaxUrl = ajaxUrl.replace( "http://", "https://" );
            }

            //check if we're good
            if ( _.isEmpty( _query_.action ) || ! _.isString( _query_.action ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper action provided' );
                  return dfd.resolve().promise();
            }
            //setup nonce
            _query_[ nonce.id ] = nonce.handle;
            if ( ! _.isObject( nonce ) || _.isUndefined( nonce.id ) || _.isUndefined( nonce.handle ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper nonce' );
                  return dfd.resolve().promise();
            }

            $.post( ajaxUrl, _query_ )
                  .done( function( _r ) {
                        // Check if the user is logged out.
                        if ( '0' === _r ||  '-1' === _r ) {
                              czrapp.errorLog( 'czrapp.doAjax : done ajax error for : ', _query_.action, _r );
                        }
                  })
                  .fail( function( _r ) { czrapp.errorLog( 'czrapp.doAjax : failed ajax error for : ', _query_.action, _r ); })
                  .always( function( _r ) { dfd.resolve( _r ); });
            return dfd.promise();
      };
})(jQuery, czrapp);








/*************************
* ADD DOM LISTENER UTILITY
*************************/
(function($, czrapp) {

      /**
       * Return whether the supplied Event object is for a keydown event but not the Enter key.
       *
       * @since 4.1.0
       *
       * @param {jQuery.Event} event
       * @returns {boolean}
       */
      czrapp.isKeydownButNotEnterEvent = function ( event ) {
        return ( 'keydown' === event.type && 13 !== event.which );
      };

      //@args = {model : model, dom_el : $_view_el, refreshed : _refreshed }
      czrapp.setupDOMListeners = function( event_map , args, instance ) {
              var _defaultArgs = {
                        model : {},
                        dom_el : {}
                  };

              if ( _.isUndefined( instance ) || ! _.isObject( instance ) ) {
                    czrapp.errorLog( 'setupDomListeners : instance should be an object', args );
                    return;
              }
              //event_map : are we good ?
              if ( ! _.isArray( event_map ) ) {
                    czrapp.errorLog( 'setupDomListeners : event_map should be an array', args );
                    return;
              }

              //args : are we good ?
              if ( ! _.isObject( args ) ) {
                    czrapp.errorLog( 'setupDomListeners : args should be an object', event_map );
                    return;
              }

              args = _.extend( _defaultArgs, args );
              // => we need an existing dom element
              if ( ! ( args.dom_el instanceof jQuery ) || 1 != args.dom_el.length ) {
                    czrapp.errorLog( 'setupDomListeners : dom element should be an existing dom element', args );
                    return;
              }

              //loop on the event map and map the relevant callbacks by event name
              // @param _event :
              //{
              //       trigger : '',
              //       selector : '',
              //       name : '',
              //       actions : ''
              // },
              _.map( event_map , function( _event ) {
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }

                    //Are we good ?
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }

                    //LISTEN TO THE DOM => USES EVENT DELEGATION
                    args.dom_el.on( _event.trigger , _event.selector, function( e, event_params ) {
                          //stop propagation to ancestors modules, typically a sektion
                          e.stopPropagation();
                          //particular treatment
                          if ( czrapp.isKeydownButNotEnterEvent( e ) ) {
                            return;
                          }
                          e.preventDefault(); // Keep this AFTER the key filter above

                          //It is important to deconnect the original object from its source
                          //=> because we will extend it when used as params for the action chain execution
                          var actionsParams = $.extend( true, {}, args );

                          //always get the latest model from the collection
                          if ( _.has( actionsParams, 'model') && _.has( actionsParams.model, 'id') ) {
                                if ( _.has( instance, 'get' ) )
                                  actionsParams.model = instance();
                                else
                                  actionsParams.model = instance.getModel( actionsParams.model.id );
                          }

                          //always add the event obj to the passed args
                          //+ the dom event
                          $.extend( actionsParams, { event : _event, dom_event : e } );

                          //add the event param => useful for triggered event
                          $.extend( actionsParams, event_params );

                          //SETUP THE EMITTERS
                          //inform the container that something has happened
                          //pass the model and the current dom_el
                          //the model is always passed as parameter
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



      //GENERIC METHOD TO SETUP EVENT LISTENER
      //NOTE : the args.event must alway be defined
      czrapp.executeEventActionChain = function( args, instance ) {
              //if the actions param is a anonymous function, fire it and stop there
              if ( 'function' === typeof( args.event.actions ) )
                return args.event.actions.call( instance, args );

              //execute the various actions required
              //first normalizes the provided actions into an array of callback methods
              //then loop on the array and fire each cb if exists
              if ( ! _.isArray( args.event.actions ) )
                args.event.actions = [ args.event.actions ];

              //if one of the callbacks returns false, then we break the loop
              //=> allows us to stop a chain of callbacks if a condition is not met
              var _break = false;
              _.map( args.event.actions, function( _cb ) {
                    if ( _break )
                      return;

                    if ( 'function' != typeof( instance[ _cb ] ) ) {
                          throw new Error( 'executeEventActionChain : the action : ' + _cb + ' has not been found when firing event : ' + args.event.selector );
                    }

                    //Allow other actions to be bound before action and after
                    //
                    //=> we don't want the event in the object here => we use the one in the event map if set
                    //=> otherwise will loop infinitely because triggering always the same cb from args.event.actions[_cb]
                    //=> the dom element shall be get from the passed args and fall back to the controler container.
                    var $_dom_el = ( _.has(args, 'dom_el') && -1 != args.dom_el.length ) ? args.dom_el : false;
                    if ( ! $_dom_el ) {
                          czrapp.errorLog( 'missing dom element');
                          return;
                    }
                    $_dom_el.trigger( 'before_' + _cb, _.omit( args, 'event' ) );

                    //executes the _cb and stores the result in a local var
                    var _cb_return = instance[ _cb ].call( instance, args );
                    //shall we stop the action chain here ?
                    if ( false === _cb_return )
                      _break = true;

                    //allow other actions to be bound after
                    $_dom_el.trigger( 'after_' + _cb, _.omit( args, 'event' ) );
              });//_.map
      };
})(jQuery, czrapp);//@global TCParams
var czrapp = czrapp || {};
czrapp.methods = {};

(function( CZRParams, $ ){
      var ctor, inherits, slice = Array.prototype.slice;

      // Shared empty constructor function to aid in prototype-chain creation.
      ctor = function() {};

      /**
       * Helper function to correctly set up the prototype chain, for subclasses.
       * Similar to `goog.inherits`, but uses a hash of prototype properties and
       * class properties to be extended.
       *
       * @param  object parent      Parent class constructor to inherit from.
       * @param  object protoProps  Properties to apply to the prototype for use as class instance properties.
       * @param  object staticProps Properties to apply directly to the class constructor.
       * @return child              The subclassed constructor.
       */
      inherits = function( parent, protoProps, staticProps ) {
        var child;

        // The constructor function for the new subclass is either defined by you
        // (the "constructor" property in your `extend` definition), or defaulted
        // by us to simply call `super()`.
        if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
          child = protoProps.constructor;
        } else {
          child = function() {
            // Storing the result `super()` before returning the value
            // prevents a bug in Opera where, if the constructor returns
            // a function, Opera will reject the return value in favor of
            // the original object. This causes all sorts of trouble.
            var result = parent.apply( this, arguments );
            return result;
          };
        }

        // Inherit class (static) properties from parent.
        $.extend( child, parent );

        // Set the prototype chain to inherit from `parent`, without calling
        // `parent`'s constructor function.
        ctor.prototype  = parent.prototype;
        child.prototype = new ctor();

        // Add prototype properties (instance properties) to the subclass,
        // if supplied.
        if ( protoProps )
          $.extend( child.prototype, protoProps );

        // Add static properties to the constructor function, if supplied.
        if ( staticProps )
          $.extend( child, staticProps );

        // Correctly set child's `prototype.constructor`.
        child.prototype.constructor = child;

        // Set a convenience property in case the parent's prototype is needed later.
        child.__super__ = parent.prototype;

        return child;
      };

      /**
       * Base class for object inheritance.
       */
      czrapp.Class = function( applicator, argsArray, options ) {
        var magic, args = arguments;

        if ( applicator && argsArray && czrapp.Class.applicator === applicator ) {
          args = argsArray;
          $.extend( this, options || {} );
        }

        magic = this;

        /*
         * If the class has a method called "instance",
         * the return value from the class' constructor will be a function that
         * calls the "instance" method.
         *
         * It is also an object that has properties and methods inside it.
         */
        if ( this.instance ) {
          magic = function() {
            return magic.instance.apply( magic, arguments );
          };

          $.extend( magic, this );
        }

        magic.initialize.apply( magic, args );
        return magic;
      };

      /**
       * Creates a subclass of the class.
       *
       * @param  object protoProps  Properties to apply to the prototype.
       * @param  object staticProps Properties to apply directly to the class.
       * @return child              The subclass.
       */
      czrapp.Class.extend = function( protoProps, classProps ) {
        var child = inherits( this, protoProps, classProps );
        child.extend = this.extend;
        return child;
      };

      czrapp.Class.applicator = {};

      /**
       * Initialize a class instance.
       *
       * Override this function in a subclass as needed.
       */
      czrapp.Class.prototype.initialize = function() {};

      /*
       * Checks whether a given instance extended a constructor.
       *
       * The magic surrounding the instance parameter causes the instanceof
       * keyword to return inaccurate results; it defaults to the function's
       * prototype instead of the constructor chain. Hence this function.
       */
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

      /**
       * An events manager object, offering the ability to bind to and trigger events.
       *
       * Used as a mixin.
       */
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

      /**
       * Observable values that support two-way binding.
       *
       * @constructor
       */
      czrapp.Value = czrapp.Class.extend({
        /**
         * @param {mixed}  initial The initial value.
         * @param {object} options
         */
        initialize: function( initial, options ) {
          this._value = initial; // @todo: potentially change this to a this.set() call.
          this.callbacks = $.Callbacks();
          this._dirty = false;

          $.extend( this, options || {} );

          this.set = $.proxy( this.set, this );
        },

        /*
         * Magic. Returns a function that will become the instance.
         * Set to null to prevent the instance from extending a function.
         */
        instance: function() {
          return arguments.length ? this.set.apply( this, arguments ) : this.get();
        },

        /**
         * Get the value.
         *
         * @return {mixed}
         */
        get: function() {
          return this._value;
        },

        /**
         * Set the value and trigger all bound callbacks.
         *
         * @param {object} to New value.
         */
        set: function( to, o ) {
              var from = this._value, dfd = $.Deferred(), self = this, _promises = [];

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              args = _.extend( { silent : false }, _.isObject( o ) ? o : {} );

              // Bail if the sanitized value is null or unchanged.
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

        /*****************************************************************************
        * A SILENT SET METHOD :
        * => keep the dirtyness param unchanged
        * => stores the api state before callback calls, and reset it after
        * => add an object param to the callback to inform that this is a silent process
        * , this is typically used in the overridden api.Setting.preview method
        *****************************************************************************/
        //@param to : the new value to set
        //@param dirtyness : the current dirtyness status of this setting in the skope
        silent_set : function( to, dirtyness ) {
              var from = this._value,
                  _save_state = api.state('saved')();

              to = this._setter.apply( this, arguments );
              to = this.validate( to );

              // Bail if the sanitized value is null or unchanged.
              if ( null === to || _.isEqual( from, to ) ) {
                return this;
              }

              this._value = to;
              this._dirty = ( _.isUndefined( dirtyness ) || ! _.isBoolean( dirtyness ) ) ? this._dirty : dirtyness;

              this.callbacks.fireWith( this, [ to, from, { silent : true } ] );
              //reset the api state to its value before the callback call
              api.state('saved')( _save_state );
              return this;
        },

        _setter: function( to ) {
          return to;
        },

        setter: function( callback ) {
          var from = this.get();
          this._setter = callback;
          // Temporarily clear value so setter can decide if it's valid.
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

        /**
         * Bind a function to be invoked whenever the value changes.
         *
         * @param {...Function} A function, or multiple functions, to add to the callback stack.
         */
        //allows us to specify a list of callbacks + a { deferred : true } param
        //if deferred is found and true, then the callback(s) are added in a list of deferred
        //@see how this deferred list is used in api.Value.prototype.set()
        bind: function() {
            //find an object in the argument
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
                  //original method
                  self.callbacks.add.apply( self.callbacks, arguments );
            }
            return this;
        },

        /**
         * Unbind a previously bound function.
         *
         * @param {...Function} A function, or multiple functions, to remove from the callback stack.
         */
        unbind: function() {
          this.callbacks.remove.apply( this.callbacks, arguments );
          return this;
        },

        // link: function() { // values*
        //   var set = this.set;
        //   $.each( arguments, function() {
        //     this.bind( set );
        //   });
        //   return this;
        // },

        // unlink: function() { // values*
        //   var set = this.set;
        //   $.each( arguments, function() {
        //     this.unbind( set );
        //   });
        //   return this;
        // },

        // sync: function() { // values*
        //   var that = this;
        //   $.each( arguments, function() {
        //     that.link( this );
        //     this.link( that );
        //   });
        //   return this;
        // },

        // unsync: function() { // values*
        //   var that = this;
        //   $.each( arguments, function() {
        //     that.unlink( this );
        //     this.unlink( that );
        //   });
        //   return this;
        // }
      });

      /**
       * A collection of observable values.
       *
       * @constructor
       */
      czrapp.Values = czrapp.Class.extend({

        /**
         * The default constructor for items of the collection.
         *
         * @type {object}
         */
        defaultConstructor: czrapp.Value,

        initialize: function( options ) {
          $.extend( this, options || {} );

          this._value = {};
          this._deferreds = {};
        },

        /**
         * Get the instance of an item from the collection if only ID is specified.
         *
         * If more than one argument is supplied, all are expected to be IDs and
         * the last to be a function callback that will be invoked when the requested
         * items are available.
         *
         * @see {czrapp.Values.when}
         *
         * @param  {string}   id ID of the item.
         * @param  {...}         Zero or more IDs of items to wait for and a callback
         *                       function to invoke when they're available. Optional.
         * @return {mixed}    The item instance if only one ID was supplied.
         *                    A Deferred Promise object if a callback function is supplied.
         */
        instance: function( id ) {
          if ( arguments.length === 1 )
            return this.value( id );

          return this.when.apply( this, arguments );
        },

        /**
         * Get the instance of an item.
         *
         * @param  {string} id The ID of the item.
         * @return {[type]}    [description]
         */
        value: function( id ) {
          return this._value[ id ];
        },

        /**
         * Whether the collection has an item with the given ID.
         *
         * @param  {string}  id The ID of the item to look for.
         * @return {Boolean}
         */
        has: function( id ) {
          return typeof this._value[ id ] !== 'undefined';
        },

        /**
         * Add an item to the collection.
         *
         * @param {string} id    The ID of the item.
         * @param {mixed}  value The item instance.
         * @return {mixed} The new item's instance.
         */
        add: function( id, value ) {
          if ( this.has( id ) )
            return this.value( id );

          this._value[ id ] = value;
          value.parent = this;

          // Propagate a 'change' event on an item up to the collection.
          if ( value.extended( czrapp.Value ) )
            value.bind( this._change );

          this.trigger( 'add', value );

          // If a deferred object exists for this item,
          // resolve it.
          if ( this._deferreds[ id ] )
            this._deferreds[ id ].resolve();

          return this._value[ id ];
        },

        /**
         * Create a new item of the collection using the collection's default constructor
         * and store it in the collection.
         *
         * @param  {string} id    The ID of the item.
         * @param  {mixed}  value Any extra arguments are passed into the item's initialize method.
         * @return {mixed}  The new item's instance.
         */
        create: function( id ) {
          return this.add( id, new this.defaultConstructor( czrapp.Class.applicator, slice.call( arguments, 1 ) ) );
        },

        /**
         * Iterate over all items in the collection invoking the provided callback.
         *
         * @param  {Function} callback Function to invoke.
         * @param  {object}   context  Object context to invoke the function with. Optional.
         */
        each: function( callback, context ) {
          context = typeof context === 'undefined' ? this : context;

          $.each( this._value, function( key, obj ) {
            callback.call( context, obj, key );
          });
        },

        /**
         * Remove an item from the collection.
         *
         * @param  {string} id The ID of the item to remove.
         */
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

        /**
         * Runs a callback once all requested values exist.
         *
         * when( ids*, [callback] );
         *
         * For example:
         *     when( id1, id2, id3, function( value1, value2, value3 ) {} );
         *
         * @returns $.Deferred.promise();
         */
        when: function() {
          var self = this,
            ids  = slice.call( arguments ),
            dfd  = $.Deferred();

          // If the last argument is a callback, bind it to .done()
          if ( $.isFunction( ids[ ids.length - 1 ] ) )
            dfd.done( ids.pop() );

          /*
           * Create a stack of deferred objects for each item that is not
           * yet available, and invoke the supplied callback when they are.
           */
          $.when.apply( $, $.map( ids, function( id ) {
            if ( self.has( id ) )
              return;

            /*
             * The requested item is not available yet, create a deferred
             * object to resolve when it becomes available.
             */
            return self._deferreds[ id ] || $.Deferred();
          })).done( function() {
            var values = $.map( ids, function( id ) {
                return self( id );
              });

            // If a value is missing, we've used at least one expired deferred.
            // Call Values.when again to generate a new deferred.
            if ( values.length !== ids.length ) {
              // ids.push( callback );
              self.when.apply( self, ids ).done( function() {
                dfd.resolveWith( self, values );
              });
              return;
            }

            dfd.resolveWith( self, values );
          });

          return dfd.promise();
        },

        /**
         * A helper function to propagate a 'change' event from an item
         * to the collection itself.
         */
        _change: function() {
          this.parent.trigger( 'change', this );
        }
      });

      // Create a global events bus
      $.extend( czrapp.Values.prototype, czrapp.Events );

})( CZRParams, jQuery );//@global CZRParams
var czrapp = czrapp || {};
/*************************
* ADD BASE CLASS METHODS
*************************/
(function($, czrapp) {
      var _methods = {
            /**
            * Cache properties on Dom Ready
            * @return {[type]} [description]
            */
            cacheProp : function() {
                  var self = this;
                  $.extend( czrapp, {
                        //cache various jQuery el in czrapp obj
                        $_window         : $(window),
                        $_html           : $('html'),
                        $_body           : $('body'),
                        $_wpadminbar     : $('#wpadminbar'),

                        //cache various jQuery body inner el in czrapp obj
                        $_tcHeader       : $('.tc-header'),

                        //various properties definition
                        localized        : "undefined" != typeof(CZRParams) && CZRParams ? CZRParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice()//store the initial device
                  });
            },

            //bool
            isResponsive : function() {
                  return this.matchMedia(991);
            },

            //@return string of current device
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

                  //old browsers compatibility
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

                  //check if option is well formed
                  if ( 'object' != typeof(skip_selectors) || ! skip_selectors[requested_sel_type] || ! $.isArray( skip_selectors[requested_sel_type] ) || 0 === skip_selectors[requested_sel_type].length )
                    return true;

                  //has a forbidden parent?
                  if ( $_el.parents( _selsToSkip.map( function( _sel ){ return 'id' == sel_type ? '#' + _sel : '.' + _sel; } ).join(',') ).length > 0 )
                    return false;

                  //has requested sel ?
                  if ( ! $_el.attr( sel_type ) )
                    return true;

                  var _elSels       = $_el.attr( sel_type ).split(' '),
                      _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});

                  //check if the filtered selectors array with the non authorized selectors is empty or not
                  //if empty => all selectors are allowed
                  //if not, at least one is not allowed
                  return 0 === _filtered.length;
            },


            //@return bool
            _isMobile : function() {
                  return ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 720px)' ).matches ) || ( this._isCustomizing() && 'desktop' != this.previewDevice() );
            },

            //@return bool
            _isCustomizing : function() {
                  return czrapp.$_body.hasClass('is-customizing') || ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize );
            },

            //Helpers
            //Check if the passed element(s) contains an iframe
            //@return list of containers
            //@param $_elements = mixed
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
          // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
          if ( $.browser.chrome )
              czrapp.$_body.addClass("chrome");
          else if ( $.browser.webkit )
              czrapp.$_body.addClass("safari");
          if ( $.browser.mozilla )
              czrapp.$_body.addClass("mozilla");
          else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
              czrapp.$_body.addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));

          //Adds version if browser = ie
          if ( czrapp.$_body.hasClass("ie") )
              czrapp.$_body.addClass($.browser.version);
    }
  };//_methods{}
  czrapp.methods.BrowserDetect = czrapp.methods.BrowserDetect || {};
  $.extend( czrapp.methods.BrowserDetect , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
/***************************
* ADD JQUERY PLUGINS METHODS
****************************/
(function($, czrapp) {
  var _methods = {

    centerImagesWithDelay : function( delay ) {
      var self = this;
      //fire the center images plugin
      //setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
      setTimeout( function(){ self.emit('centerImages'); }, delay || 100 );
    },


    //IMG SMART LOAD
    //.article-container covers all post / page content : single and list
    //__before_main_wrapper covers the single post thumbnail case
    //.widget-front handles the featured pages
    //.post-related-articles handles the related posts
    imgSmartLoad : function() {
      var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
          //Default selectors for where are : $( '[class*=grid-container], .article-container', '.__before_main_wrapper', '.widget-front', '.post-related-articles' ).find('img');
          _where           = czrapp.localized.imgSmartLoadOpts.parentSelectors.join();

      //Smart-Load images
      //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
      //the centerImages plugin will react to this event centering them
      if (  smartLoadEnabled )
        $( _where ).imgSmartLoad(
          _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {}
        );

      //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
      //for this purpose we have to trigger the simple-load on:
      //1) imgs which have been excluded from the smartloading if enabled
      //2) all the images in the default 'where' if the smartloading isn't enaled
      //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
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

        //imgs to center with delay
        setTimeout( function(){
          self.triggerSimpleLoad( $_to_center_with_delay );
        }, 300 );
        //all other imgs to center
        self.triggerSimpleLoad( $_to_center );
      }
    },


    /**
    * CENTER VARIOUS IMAGES
    * @return {void}
    */
    centerImages : function() {
      //POST CLASSIC GRID IMAGES
      $('.tc-grid-figure, .widget-front .tc-thumbnail').centerImages( {
        enableCentering : czrapp.localized.centerAllImg,
        oncustom : ['smartload', 'refresh-height', 'simple_load'],
        zeroTopAdjust: 0,
        enableGoldenRatio : false,
      } );

      $('.js-centering.entry-media__holder, .js-centering.entry-media__wrapper').centerImages({
        enableCentering : 1,
        oncustom : ['smartload', 'refresh-height', 'simple_load'],
        enableGoldenRatio : false, //true
        zeroTopAdjust: 0,
        setOpacityWhenCentered : true,//will set the opacity to 1
        opacity : 1
      });


    },//center_images

    parallax : function() {
      $( '.parallax-item' ).czrParallax();
      /* Refresh waypoints when mobile menu button is toggled as
      *  the static/relative menu will push the content
      */
      $('.ham__navbar-toggler').on('click', function(){
        setTimeout( function(){
        Waypoint.refreshAll(); }, 400 ); }
      );
    },

    lightBox : function() {
      var _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';

      /* The magnificPopup delegation is very good
      * it works when clicking on a dynamically added a.expand-img
      * but also when clicking on an another a.expand-img the image speficified in the
      * dynamically added a.expang-img href is added to the gallery
      */
      $( '[class*="grid-container__"]' ).magnificPopup({
        delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
        type: 'image'
        // other options
      });

      /* galleries in singles Create grouped galleries */
      $( '.czr-gallery' ).each(function(){
        $(this).magnificPopup({
          delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
          type: 'image',
          gallery: {
           enabled: true,
           arrowMarkup: _arrowMarkup
          }
          // other options
        });
      });

      /*
      * in singles when former tc_fancybox enabled
      */
      $('article .tc-content-inner').magnificPopup({
        delegate: '[data-lb-type="grouped-post"]',
        type: 'image',
        gallery: {
         enabled: true,
         arrowMarkup: _arrowMarkup
        }
      });

      //in post lists galleries post formats
      //only one button for each gallery
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
                        //open the selected carousel gallery item
                        $_gallery_crsl.find( '.is-selected .gallery-img' ).trigger('click');
                  }

            }//endif
      });
    },

  };//_methods{}

  czrapp.methods.JQPlugins = {};
  $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp);var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;

                  /* Flickity ready
                  * see https://github.com/metafizzy/flickity/issues/493#issuecomment-262658287
                  */
                  var activate = Flickity.prototype.activate;
                  Flickity.prototype.activate = function() {
                        if ( this.isActive ) {
                          return;
                        }
                        activate.apply( this, arguments );
                        this.dispatchEvent( 'czr-flickity-ready', null, this );
                  };


                  /* Allow parallax */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );

                  /* Enable page dots on fly (for the main slider only, for the moment, consider to make it dependend to data-flickity-dots)*/
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', self._slider_dots );

                  /* Fire fittext */
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


                  /* Disable controllers when the first or the latest slide is in the viewport (for the related posts) */
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );

                  /* for gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-gallery.czr-carousel .carousel-inner', self._move_background_link_inside );
                  /*Handle custom nav */
                  // previous
                  czrapp.$_body.on( 'click tap prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  // next
                  czrapp.$_body.on( 'click tap next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );

            },//_init()



            fireCarousels : function() {
                  //TODO BETTER

                  /* Test only RELATED POSTS !!!!!! */
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


                  /* Test only GALLERY SLIDER IN POST LISTS !!!!!! */
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

                  $('.carousel-inner', '[id^="customizr-slider-main"]').flickity({
                      prevNextButtons: false,
                      pageDots: false,

                      wrapAround: true,
                      imagesLoaded: true,
                      //lazyLoad ?

                      setGallerySize: false,
                      cellSelector: '.carousel-cell',

                      dragThreshold: 10,

                      autoPlay: true, // {Number in milliseconds }

                      accessibility: false,
                  });
            },

            centerMainSlider : function() {
                  //SLIDER IMG
                  setTimeout( function() {

                        //centering per carousel
                        $.each( $( '.carousel-inner', '[id^="customizr-slider-main"]' ) , function() {

                              $( this ).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    imgSel : '.carousel-image img',
                                    /* To check settle.flickity is working, it should according to the docs */
                                    oncustom : ['settle.flickity', 'simple_load'],
                                    defaultCSSVal : { width : '100%' , height : 'auto' },
                                    useImgAttr : true,
                                    zeroTopAdjust: 0
                              });

                              //fade out the loading icon per carousel with a little delay
                              //mostly for retina devices (the retina image will be downloaded afterwards
                              //and this may cause the re-centering of the image)
                              var self = this;
                              setTimeout( function() {

                                    $( self ).prevAll('.czr-slider-loader-wrapper').fadeOut();

                              }, 500 );

                        });

                  } , 50);
            },
            /*
            * carousel parallax on flickity ready
            * we parallax only the flickity-viewport, so that we don't parallax the carouasel-dots
            */
            _parallax : function( evt ) {
                var $_parallax_carousel  = $(this),
                  //extrapolate data from the parallax carousel and pass them to the flickity viewport
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



            //Enable page dots on fly
            _slider_dots : function( evt, _flickity ) {

                  if ( $(evt.target).find('.carousel-cell').length > 1 ) {
                    _flickity.options.pageDots = true;
                    _flickity._createPageDots();
                    _flickity.activatePageDots();
                  }

            },


            //SLIDER ARROW UTILITY
            //@return void()
            _slider_arrows : function ( evt, side ) {

                  evt.preventDefault();
                  var $_this    = $(this),
                      _flickity = $_this.data( 'controls' );

                  if ( ! $_this.length )
                    return;

                  //if not already done, cache the slider this control controls as data-controls attribute
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


            /* Handle carousels nav */
            /*
            * Disable controllers when the first or the latest slide is in the viewport and no wraparound selected
            * when wrapAround //off
            */
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

                  //Reset
                  $_prev.removeClass('disabled');
                  $_next.removeClass('disabled');

                  //selected index is 0, disable prev or
                  //first slide shown but not selected
                  if ( ( 0 === flkty.selectedIndex ) )
                        $_prev.addClass('disabled');

                  //console.log(Math.abs( flkty.slidesWidth + flkty.x ) );
                  //selected index is latest, disable next or
                  //latest slide shown but not selected
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
   var _methods =   {

      //outline firefox fix, see https://github.com/presscustomizr/customizr/issues/538
      outline: function() {
         if ( 'function' == typeof( tcOutline ) )
            tcOutline();
      },

      disableHoverOnScroll: function() {
         //While scrolling we don' want to trigger hover actions

         //https://www.thecssninja.com/javascript/pointer-events-60fps
         //pure javascript approach
         var body = document.body,
             timer;

         window.addEventListener( 'scroll', function() {

            clearTimeout(timer);

            if( !body.classList.contains( 'no-hover' ) ) {
               body.classList.add( 'no-hover' );
            }

            timer = setTimeout( function(){
               body.classList.remove('no-hover');
            }, 100);

         }, false );
      },

      //VARIOUS HOVERACTION
      variousHoverActions : function() {
         if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
            return;

         /* Grid */
         $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
         $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
         czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front', _toggleThisHover );

         /* end Grid */

         /* Widget li */
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
      //FORM FOCUS ACTION
      formFocusAction : function() {
         var _input_types       = [
                  'input[type="url"]',
                  'input[type="email"]',
                  'input[type="text"]',
                  'input[type="password"]',
                  'textarea'
            ],
            _focusable_class    = 'czr-focus',
            _parent_selector    = '.'+_focusable_class,
            _focus_class        = 'in-focus',
            _czr_form_class     = 'czr-form',
            _inputs             = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
            $_focusable_inputs  = $( _input_types.join() );
            _maybe_fire         = $_focusable_inputs.length > 0;

         //This is needed to add a class to the input parent (label parent) so that
         //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
         //consider the exclude?!
         if ( _maybe_fire ) {
            $_focusable_inputs.each( function() {
               var $_this = $(this);
               if ( !$_this.attr('placeholder') && ( $_this.closest( '#buddypress' ).length < 1 ) ) {
                  $(this)
                        .addClass('czr-focusable')
                        .parent().addClass(_focusable_class)
                        .closest('form').addClass(_czr_form_class);
               }
            });
         }else
            return;

         czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );

         function _toggleThisFocusClass( evt ) {
            var $_el       = $(this),
                  $_parent = $_el.closest(_parent_selector);

            if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
               $_parent.addClass( _focus_class );
            } else {
               $_parent.removeClass( _focus_class );
            }
         }

         //on ready :   think about search forms in search pages
         $(_inputs).trigger( 'in-focus-load.czr-focus' );

         //search form clean on .icn-close click
         czrapp.$_body.on( 'click tap', '.icn-close', function() {
            $(this).closest('form').find('.czr-search-field').val('').focus();
         });
      },

      variousHeaderActions : function() {
         var _mobile_viewport                   = 992;

         /* header search button */
         czrapp.$_body.on( 'click tap', '.desktop_search__link', function(evt) {
            evt.preventDefault();
            czrapp.$_body.toggleClass('full-search-opened');
         });
         czrapp.$_body.on( 'click tap', '.search-close_btn', function(evt) {
            evt.preventDefault();
            czrapp.$_body.removeClass('full-search-opened');
         });

         //custom scrollbar for woocommerce list
         if ( 'function' == typeof $.fn.mCustomScrollbar ) {
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.primary-nav__woocart', function() {
               var $_to_scroll = $(this).find('.product_list_widget');
               if ( $_to_scroll.length && !$_to_scroll.hasClass('mCustomScrollbar') ) {
                  $_to_scroll.mCustomScrollbar({
                     theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                  });
               }
            });
         }

         //go to opened on click element when mCustomScroll active
         czrapp.$_body.on( 'shown.czr.czrDropdown', '.czr-open-on-click.mCustomScrollbar, .czr-open-on-click .mCustomScrollbar, .mCustomScrollbar .czr-open-on-click', function( evt ) {
            var $_this                  = $( this ),
                  $_customScrollbar = $_this.hasClass('mCustomScrollbar') ? $_this : $_this.closest('.mCustomScrollbar');
            if ( $_customScrollbar.length )
               //http://manos.malihu.gr/jquery-custom-content-scroller/
               $_customScrollbar.mCustomScrollbar( 'scrollTo', $(evt.target) );
         });


      },

      //SMOOTH SCROLL
      smoothScroll: function() {
         if ( CZRParams.SmoothScroll && CZRParams.SmoothScroll.Enabled )
            smoothScroll( CZRParams.SmoothScroll.Options );
      },

      pluginsCompatibility: function() {
         /*
         * Super socializer
         * it prints the socializer vertical bar filtering the excerpt
         * so as child of .entry-content__holder.
         * In alternate layouts, when centering sections, the use of the translate property
         * changed the fixed behavior (of the aforementioned bar) to an absoluted behavior
         * with the following core we move the bar outside the section
         * ( different but still problems occurr with the masonry )
         */
         var $_ssbar = $( '.the_champ_vertical_sharing, .the_champ_vertical_counter', '.article-container' );
         if ( $_ssbar.length )
            $_ssbar.detach().prependTo('.article-container');
      },


      /* Find a way to make this smaller but still effective */
      featuredPagesAlignment : function() {

         var $_featured_pages   = $('.featured-page .widget-front'),
               _n_featured_pages = $_featured_pages.length,
               doingAnimation      = false,
               _lastWinWidth       = '';


         if ( _n_featured_pages < 2 )
            return;

         var $_fp_elements       = new Array( _n_featured_pages ),
               _n_elements          = new Array( _n_featured_pages );

         //Grab all subelements having class starting with fp-
         //Requires all fps having same html structure...
         $.each( $_featured_pages, function( _fp_index, _fp ) {
            $_fp_elements[_fp_index]   = $(_fp).find( '[class^=fp-]' );
            _n_elements[_fp_index]      = $_fp_elements[_fp_index].length;
         });

         _n_elements = Math.max.apply(Math, _n_elements );

         if ( ! _n_elements )
            return;

         var _offsets      = new Array( _n_elements ),
               _maxs          = new Array( _n_elements );

         /*
         * Build the _offsets matrix
         * Row => element (order given by _elements array)
         * Col => fp
         */
         for (var i = 0; i < _n_elements; i++)
            _offsets[i] = new Array( _n_featured_pages);


         //fire
         maybeSetElementsPosition();
         //bind
         czrapp.$_window.on('resize', maybeSetElementsPosition );

         function maybeSetElementsPosition() {

            if ( ! doingAnimation ) {
               var _winWidth = czrapp.$_window.width();
               /*
               * we're not interested in win height resizing
               */
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
            /*
            * this array will store the
            */
            var _fp_offsets = [];

            for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {

               for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                  //Reset and grab the the top offset for each element
                  var $_el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                        _offset = 0,
                        $_fp      = $($_featured_pages[_fp_index]);

                  if ( $_el.length > 0 ) {
                     //reset maybe added paddingTop
                     $_el.css( 'paddingTop', '' );
                     //retrieve the top position
                     _offset = $_el.offset().top;

                  }
                  _offsets[_element_index][_fp_index] = _offset;

                  /*
                  * Build the array of fp offset once (first loop on elements)
                  */
                  if ( _fp_offsets.length < _n_featured_pages )
                     _fp_offsets[_fp_index] = parseFloat( $_fp.offset().top);
               }//endfor


               /*
               * Break this only loop when featured pages are one on top of each other
               * featured pages top offset differs
               * We continue over other elements as we need to reset other marginTop
               */
               if ( 1 != _.uniq(_fp_offsets).length )
                  continue;

               /*
               * for each type of element store the max offset value
               */
               _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );

               /*
               * apply the needed offset for each featured page element
               */
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

      //Btt arrow visibility
      bttArrow : function() {
         var doingAnimation = false,
            $_btt_arrow         = $('.czr-btta');

         if ( 0 === $_btt_arrow.length )
            return;

         czrapp.$_window.on( 'scroll', bttArrowVisibility );
         bttArrowVisibility();

         function bttArrowVisibility() {
            if ( ! doingAnimation ) {
               doingAnimation = true;

               window.requestAnimationFrame( function() {
                  if ( czrapp.$_window.scrollTop() > 100 )
                     $_btt_arrow.addClass('show');
                  else
                     $_btt_arrow.removeClass('show');

                  doingAnimation = false;
               });
            }
         }//bttArrowVisibility

      },//bttArrow

      //BACK TO TOP
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

      //SMOOTH SCROLL FOR AUTHORIZED LINK SELECTORS
      anchorSmoothScroll : function() {
        if ( ! czrapp.localized.anchorSmoothScroll || 'easeOutExpo' != czrapp.localized.anchorSmoothScroll )
              return;

        var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
            self = this,
            $_links = $('a[href^="#"]', '#content').not(_excl_sels);

        //Deep exclusion
        //are ids and classes selectors allowed ?
        //all type of selectors (in the array) must pass the filter test
        _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null ;
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
                }, 700, czrapp.localized.anchorSmoothScroll);
            }
            return false;
        });//click
      },
   };//_methods{}

   czrapp.methods.UserXP = {};
   $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
/************************************************
* STICKY FOOTER SUB CLASS
*************************************************/
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

    /***********************************************
    * DOM EVENT LISTENERS AND HANDLERS
    ***********************************************/
    stickyFooterEventListener : function() {
      var self = this;

      // maybe apply sticky footer on window resize
      czrapp.$_window.on( 'resize', function() {
        self.stickyFooterEventHandler('resize');
      });

      // maybe apply sticky footer on golden ratio applied
      czrapp.$_window.on( 'golden-ratio-applied', function() {
        self.stickyFooterEventHandler('refresh');
      });

      /* can be useful without exposing methods make it react to this event which could be externally fired, used in the preview atm */
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
    /* We apply the "sticky" footer by setting the height of the push div, and adding the proper class to show it */
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

      /* Fire an event which something might listen to */
      if ( _event )
        czrapp.$_body.trigger(_event);
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    /*
    * return @bool: whether apply or not the sticky-footer
    */
    _is_sticky_footer_enabled : function() {
      return czrapp.$_body.hasClass('czr-sticky-footer');
    },


    //STICKY HEADER SUB CLASS HELPER (private like)
    /*
    * return @int: the potential height value of the page
    */
    _get_full_height : function() {
      var _full_height = this.$_page.outerHeight(true) + this.$_page.offset().top,
          _push_height = 'block' == this.$_push.css('display') ? this.$_push.outerHeight() : 0;

      return _full_height - _push_height;
    }
  };//_methods{}

  czrapp.methods.StickyFooter = {};
  $.extend( czrapp.methods.StickyFooter , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
/************************************************
* MASONRY GRID SUB CLASS
*************************************************/
/*
* In this script we fire the grid masonry on the grid only when all the images
* therein are fully loaded in case we're not using the images on scroll loading
* Imho would be better use a reliable plugin like imagesLoaded (from the same masonry's author)
* which addresses various cases, failing etc, as it is not very big. Or at least dive into it
* to see if it really suits our needs.
*
* We can use different approaches while the images are loaded:
* 1) loading animation
* 2) display the grid in a standard way (organized in rows) and modify che html once the masonry is fired.
* 3) use namespaced events
* This way we "ensure" a compatibility with browsers not running js
*
* Or we can also fire the masonry at the start and re-fire it once the images are loaded
*/
(function($, czrapp) {
  var _methods =  {

    init : function() {
      /*
      * TODO:
      * - use delegation for images (think about infinite scroll)
      * - use jQuery deferred (think about infinite scroll)
      */
      this.$_grid = $('.masonry__wrapper' );

      if ( !this.$_grid.length )
        return;

      this.$_images = this.$_grid.find('img');

      this._loaded_counter = 0;
      this._n_images = this.$_images.length;

      if ( ! this._n_images )
        this._czrFireMasonry();

    },
    masonryGridEventListener : function() {
      //LOADING ACTIONS
      var self = this;

      this.$_grid.on( 'images_loaded', function(){ self._czrFireMasonry(); });

      if ( ! this._n_images )
        return;
      this.$_images.on( 'simple_load', function(){ self._czrMaybeTriggerImagesLoaded(); });

      //Dummy, for testing purpose only
      this.triggerSimpleLoad( this.$_images );
    },

    _czrFireMasonry : function() {
      this.$_grid.masonry({
          itemSelector: '.grid-item',
          percentPosition: true
      });
    },

    _czrMaybeTriggerImagesLoaded : function() {
      var self = this;
      this._loaded_counter++;
      if ( this._loaded_counter == this._n_images )
        setTimeout( function(){
          self.$_grid.trigger('images_loaded');
        }, 200);
    }
  };//_methods{}

  czrapp.methods.Czr_MasonryGrid = {};
  $.extend( czrapp.methods.Czr_MasonryGrid , _methods );
})(jQuery, czrapp);var czrapp = czrapp || {};
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
var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
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
        DATA_HOVER_PARENT        : '.czr-open-on-hover .menu-item-has-children, .primary-nav__woocart',
        DATA_CLICK_PARENT        : '.czr-open-on-click .menu-item-has-children',
        DATA_PARENTS             : '.tc-header .menu-item-has-children'
      };
    },


    //Handle dropdown on hover via js
    //TODO: find a way to unify this with czrDropdown
    dropdownMenuOnHover : function() {
      var _dropdown_selector = this.Selector.DATA_HOVER_PARENT,
          self               = this;

      enableDropdownOnHover();

      function _addOpenClass ( evt ) {

        var $_el = $(this);

        _debounced_addOpenClass = _.debounce( function() {

          //do nothing if menu is mobile
          if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
            return false;

          if ( ! $_el.hasClass(self.ClassName.SHOW) ) {
            $_el.addClass(self.ClassName.SHOW)
                .trigger(self.Event.SHOWN);

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'true');
          }

        }, 150);

        _debounced_addOpenClass();
      }

      //a little delay before closing to avoid closing a parent before accessing the child
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

        }, 150);

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

      //go to the link if submenu is already opened
      //This happens before the closing occurs when dropdown on click and the dropdown on hover (see the debounce in this case)
      czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE, function(evt) {

            var $_el = $(this);

            //do nothing if menu is mobile
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



    /*
    * Snake Prototype
    */
    dropdownPlacement : function() {
      var self = this,
          doingAnimation = false;

      czrapp.$_window
          //on resize trigger Event.PLACE on active dropdowns
          .on( 'resize', function() {
                  if ( ! doingAnimation ) {
                        doingAnimation = true;
                        window.requestAnimationFrame(function() {
                          //trigger a placement on the open dropdowns
                          $( '.'+self.ClassName.PARENTS+'.'+self.ClassName.SHOW)
                              .trigger(self.Event.PLACE_ME);
                          doingAnimation = false;
                        });
                  }

          });

      czrapp.$_body
          .on( this.Event.PLACE_ALL, function() {
                      //trigger a placement on all
                      $( '.'+self.ClassName.PARENTS )
                          .trigger(self.Event.PLACE_ME);
          })
          //snake bound on menu-item shown and place
          .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.DATA_PARENTS, function(evt) {
            evt.stopPropagation();
            _do_snake( $(this), evt );
          });


      //snake
      function _do_snake( $_el, evt ) {

        if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) )
          return;

        var $_this       = $_el,
            $_dropdown   = $_this.children( '.'+self.ClassName.DROPDOWN );

        if ( !$_dropdown.length )
          return;

        $_dropdown.css( 'zIndex', '-100' ).css('display', 'block');

        _maybe_move( $_dropdown );

        //unstage if staged
        $_dropdown.css( 'zIndex', '').css('display', '');

      }


      function _maybe_move( $_dropdown ) {
        //snake inheritance
        if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( 'open-left' ) ) {
          $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );
        }
        else {
          $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );
        }

        //let's compute on which side open the dropdown
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




  /**
   * --------------------------------------------------------------------------
   * Inspired by Bootstrap (v4.0.0-alpha.6): dropdown.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * updated to : https://github.com/twbs/bootstrap/commit/1f37c536b2691e4a98310982f9b58ede506f11d8#diff-bfe9dc603f82b0c51ba7430c1fe4c558
   * 20/05/2017
   * --------------------------------------------------------------------------
   */


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

    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */

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
      NAVBAR_NAV: '.navbar-nav',
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)'
    };

    var czrDropdown = function ($) {



      /**
       * ------------------------------------------------------------------------
       * Class Definition
       * ------------------------------------------------------------------------
       */

      var czrDropdown = function () {
        function czrDropdown(element) {
          _classCallCheck(this, czrDropdown);

          this._element = element;

          this._addEventListeners();
        }

        // getters

        // public

        czrDropdown.prototype.toggle = function toggle() {
          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
            return false;
          }

          //do nothing if menu is mobile
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

          // if this is a touch-enabled device we add extra
          // empty mouseover listeners to the body's immediate children;
          // only needed because of broken event delegation on iOS
          // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
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

        // private

        czrDropdown.prototype._addEventListeners = function _addEventListeners() {
          $(this._element).on(Event.CLICK, this.toggle);
        };

        // static

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

            // if this is a touch-enabled device we remove the extra
            // empty mouseover listeners we added for iOS support
            if ('ontouchstart' in document.documentElement) {
              $('body').children().off('mouseover', null, $.noop);
            }


            toggles[i].setAttribute('aria-expanded', 'false');

            $(parent).removeClass(ClassName.SHOW).trigger($.Event(Event.HIDDEN, relatedTarget));
          }
        };

        czrDropdown._getParentFromElement = function _getParentFromElement(element) {
          var _parentNode = void 0;
          /* get the closest dropdown parent */
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

         /* var items = $.makeArray($(Selector.VISIBLE_ITEMS));

          items = items.filter(function (item) {
            return item.offsetWidth || item.offsetHeight;
          });*/
          var items = $(parent).find(Selector.VISIBLE_ITEMS).get();

          if (!items.length) {
            return;
          }

          var index = items.indexOf(event.target);

          if (event.which === ARROW_UP_KEYCODE && index > 0) {
            // up
            index--;
          }

          if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
            // down
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

      /**
       * ------------------------------------------------------------------------
       * Data Api implementation
       * ------------------------------------------------------------------------
       */

      $(document)
        .on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler)
        .on(Event.KEYDOWN_DATA_API, Selector.MENU, czrDropdown._dataApiKeydownHandler)
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API, czrDropdown._clearMenus)
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle)
        .on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
          e.stopPropagation();
      });

      /**
       * ------------------------------------------------------------------------
       * jQuery
       * ------------------------------------------------------------------------
       */

      $.fn[NAME] = czrDropdown._jQueryInterface;
      $.fn[NAME].Constructor = czrDropdown;
      $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return czrDropdown._jQueryInterface;
      };

      return czrDropdown;

  }(jQuery);

})(jQuery, czrapp);var czrapp = czrapp || {};
//@global TCParams
/************************************************
* LET'S DANCE
*************************************************/
( function ( czrapp, $, _ ) {
      //adds the server params to the app now
      czrapp.localized = CZRParams || {};

      //add the events manager object to the root
      $.extend( czrapp, czrapp.Events );

      //defines a Root class
      //=> adds the constructor options : { id : ctor name, dom_ready : params.ready || [] }
      //=> declares a ready() methods, fired on dom ready
      czrapp.Root           = czrapp.Class.extend( {
            initialize : function( options ) {
                  $.extend( this, options || {} );
                  this.isReady = $.Deferred();
            },

            //On DOM ready, fires the methods passed to the constructor
            //Populates a czrapp.status array allowing us to remotely check the current app state
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

      //is resolved on 'czrapp-ready', which is triggered when
      //1) the initial map method has been instantiated
      //2) all methods have been fired on DOM ready;
      czrapp.ready          = $.Deferred();
      czrapp.bind( 'czrapp-ready', function() {
            czrapp.ready.resolve();
      });

      //SERVER MOBILE USER AGENT
      czrapp.isMobileUserAgent = new czrapp.Value( false );
      //This ajax requests solves the problem of knowing if wp_is_mobile() in a front js script, when the website is using a cache plugin
      //without a cache plugin, we could localize the wp_is_mobile() boolean
      //with a cache plugin, we need to always get a fresh answer from the server
      //falls back on CZRParams.isWPMobile ( which can be cached, so not fully reliable )
      // czrapp.browserAgentSet = $.Deferred( function() {
      //       var _dfd = this;
      //       czrapp.doAjax( { action: "hu_wp_is_mobile" } )
      //             .always( function( _r_ ) {
      //                   czrapp.isMobileUserAgent( ( ! _r_.success || _.isUndefined( _r_.data.is_mobile ) ) ? ( '1' == TCParams.isWPMobile ) : _r_.data.is_mobile );
      //                   _dfd.resolve( czrapp.isMobileUserAgent() );
      //             });
      //       //always auto resolve after 1.5s if the server is too slow.
      //       _.delay( function() {
      //           if ( 'pending' == _dfd.state() )
      //             _dfd.resolve( false );
      //       }, 1500 );
      // });

      //THE DEFAULT MAP
      //Other methods can be hooked. @see czrapp.customMap
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
                            //'dropCaps',
                            //'extLinks',
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
                            'outline',

                            'disableHoverOnScroll',
                            'variousHoverActions',
                            'formFocusAction',
                            'variousHeaderActions',
                            'smoothScroll',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',
                      ]
                },
                /*stickyHeader : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyHeader ),
                      ready : [
                            'initOnDomReady',
                            'stickyHeaderEventListener',
                            'triggerStickyHeaderLoad'
                      ]
                },*/
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

      //Instantiates
      var _instantianteAndFireOnDomReady = function( newMap, previousMap, isInitial ) {
            if ( ! _.isObject( newMap ) )
              return;
            _.each( newMap, function( params, name ) {
                  //skip if already instantiated or invalid params
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

                  //the constructor has 2 mandatory params : id and dom_ready methods
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

            //Fire on DOM ready
            $(function ($) {
                  _.each( newMap, function( params, name ) {
                        //bail if already fired
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
                  //trigger czrapp-ready when the default map has been instantiated
                  czrapp.trigger( isInitial ? 'czrapp-ready' : 'czrapp-updated' );
            });
      };//_instantianteAndFireOnDomReady()

      //instantiates the default map
      //@param : new map, previous map, isInitial bool
      _instantianteAndFireOnDomReady( appMap, null, true );

      //instantiate additional classes on demand
      //EXAMPLE IN THE PRO HEADER SLIDER PHP TMPL :
      //instantiate on first run, then on the following runs, call fire statically
      // var _do = function() {
      //       if ( czrapp.proHeaderSlid ) {
      //             czrapp.proHeaderSlid.fire( args );
      //       } else {
      //             var _map = $.extend( true, {}, czrapp.customMap() );
      //             _map = $.extend( _map, {
      //                   proHeaderSlid : {
      //                         ctor : czrapp.Base.extend( czrapp.methods.ProHeaderSlid ),
      //                         ready : [ 'fire' ],
      //                         options : args
      //                   }
      //             });
      //             //this is listened to in xfire.js
      //             czrapp.customMap( _map );
      //       }
      // };
      // if ( ! _.isUndefined( czrapp ) && czrapp.ready ) {
      //       if ( 'resolved' == czrapp.ready.state() ) {
      //             _do();
      //       } else {
      //             czrapp.ready.done( _do );
      //       }
      // }
      czrapp.customMap = new czrapp.Value( {} );
      czrapp.customMap.bind( _instantianteAndFireOnDomReady );//<=THE CUSTOM MAP IS LISTENED TO HERE

})( czrapp, jQuery, _ );/****************************************************************
* FORMER HARD CODED SCRIPTS MADE ENQUEUABLE WITH LOCALIZED PARAMS
*****************************************************************/
(function($, czrapp, _ ) {
    //czrapp.localized = CZRParams
    czrapp.ready.then( function() {
          //PLACEHOLDER NOTICES
          //two types of notices here :
          //=> the ones that remove the notice only : thumbnails, smartload, sidenav, secondMenu, mainMenu
          //=> and others that removes notices + an html block ( slider, fp ) or have additional treatments ( widget )
          // each placeholder item looks like :
          // {
          // 'thumbnail' => array(
          //        'active'    => true,
          //        'args'  => array(
          //            'action' => 'dismiss_thumbnail_help',
          //            'nonce' => array( 'id' => 'thumbnailNonce', 'handle' => 'tc-thumbnail-help-nonce' ),
          //            'class' => 'tc-thumbnail-help'
          //        )
          //    ),
          // }
          if ( czrapp.localized.frontHelpNoticesOn && ! _.isEmpty( frontHelpNoticeParams ) ) {
                // @_el : dom el
                // @_params_ looks like :
                // {
                //       action : '',
                //       nonce : { 'id' : '', 'handle' : '' },
                //       class : '',
                // }
                // Fired on click
                // Attempt to fire an ajax call
                var _doAjax = function( _query_ ) {
                          var ajaxUrl = czrapp.localized.adminAjaxUrl, dfd = $.Deferred();
                          $.post( ajaxUrl, _query_ )
                                .done( function( _r ) {
                                      // Check if the user is logged out.
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
                    //@remove_action optional removal action server side. Ex : 'remove_slider'
                    _ajaxDismiss = function( _params_ ) {
                          var _query = {},
                              dfd = $.Deferred();

                          if ( ! _.isObject( _params_ ) ) {
                                czrapp.errorLog( 'placeHolder dismiss : wrong params' );
                                return;
                          }

                          //normalizes
                          _params_ = _.extend( {
                                action : '',
                                nonce : { 'id' : '', 'handle' : '' },
                                class : '',
                                remove_action : null,//for slider and fp
                                position : null,//for widgets
                          }, _params_ );

                          //set query params
                          _query.action = _params_.action;

                          //for slider and fp
                          if ( ! _.isNull( _params_.remove_action ) )
                            _query.remove_action = _params_.remove_action;

                          //for widgets
                          if ( ! _.isNull( _params_.position ) )
                            _query.position = _params_.position;

                          _query[ _params_.nonce.id ] = _params_.nonce.handle;

                          //fires and resolve promise
                          _doAjax( _query ).done( function() { dfd.resolve(); });
                          return dfd.promise();
                    };


                //loop on the front help notice params sent by server
                _.each( frontHelpNoticeParams, function( _params_, _id_ ) {
                      //normalizes
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
                            //simple dismiss
                            case 'thumbnail' :
                            case 'smartload' :
                            case 'sidenav' :
                            case 'secondMenu' :
                            case 'mainMenu' :
                                  if ( _params_.active ) {
                                        //DOM READY
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

                            //specific dismiss
                            case 'slider' :
                                  if ( _params_.active ) {
                                        //DOM READY
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
                                        //DOM READY
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
                                        //DOM READY
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

})(jQuery, czrapp, _ );