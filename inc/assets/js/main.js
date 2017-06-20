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

(function( TCParams, $ ){
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

})( TCParams, jQuery );//@global TCParams
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
                        localized        : "undefined" != typeof(TCParams) && TCParams ? TCParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice()//store the initial device
                  });
            },

            //bool
            isResponsive : function() {
                  return this.matchMedia(979);
            },

            //@return string of current device
            getDevice : function() {
                  var _devices = {
                        desktop : 979,
                        tablet : 767,
                        smartphone : 480
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
              setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
            },


            //IMG SMART LOAD
            //.article-container covers all post / page content : single and list
            //__before_main_wrapper covers the single post thumbnail case
            //.widget-front handles the featured pages
            imgSmartLoad : function() {
              var smartLoadEnabled = 1 == TCParams.imgSmartLoadEnabled,
                  //Default selectors for where are : $( '.article-container, .__before_main_wrapper, .widget-front' ).find('img');
                  _where           = TCParams.imgSmartLoadOpts.parentSelectors.join();

              //Smart-Load images
              //imgSmartLoad plugin will trigger the smartload event when the img will be loaded
              //the centerImages plugin will react to this event centering them
              if (  smartLoadEnabled )
                $( _where ).imgSmartLoad(
                  _.size( TCParams.imgSmartLoadOpts.opts ) > 0 ? TCParams.imgSmartLoadOpts.opts : {}
                );

              //If the centerAllImg is on we have to ensure imgs will be centered when simple loaded,
              //for this purpose we have to trigger the simple-load on:
              //1) imgs which have been excluded from the smartloading if enabled
              //2) all the images in the default 'where' if the smartloading isn't enaled
              //simple-load event on holders needs to be triggered with a certain delay otherwise holders will be misplaced (centering)
              if ( 1 == TCParams.centerAllImg ) {
                var self                   = this,
                    $_to_center            = smartLoadEnabled ?
                       $( _.filter( $( _where ).find('img'), function( img ) {
                          return $(img).is(TCParams.imgSmartLoadOpts.opts.excludeImg.join());
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


            //FIRE DROP CAP PLUGIN
            dropCaps : function() {
              if ( ! TCParams.dropcapEnabled || ! _.isObject( TCParams.dropcapWhere ) )
                return;

              $.each( TCParams.dropcapWhere , function( ind, val ) {
                if ( 1 == val ) {
                  $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
                    minwords : TCParams.dropcapMinWords,//@todo check if number
                    skipSelectors : _.isObject(TCParams.dropcapSkipSelectors) ? TCParams.dropcapSkipSelectors : {}
                  });
                }
              });//each
            },


            //FIRE EXT LINKS PLUGIN
            //May be add (check if activated by user) external class + target="_blank" to relevant links
            //images are excluded by default
            //links inside post/page content
            extLinks : function() {
              if ( ! TCParams.extLinksStyle && ! TCParams.extLinksTargetExt )
                return;
              $('a' , '.entry-content').extLinks({
                addIcon : TCParams.extLinksStyle,
                newTab : TCParams.extLinksTargetExt,
                skipSelectors : _.isObject(TCParams.extLinksSkipSelectors) ? TCParams.extLinksSkipSelectors : {}
              });
            },

            //FIRE FANCYBOX PLUGIN
            //Fancybox with localized script variables
            fancyBox : function() {
              if ( 1 != TCParams.FancyBoxState || 'function' != typeof($.fn.fancybox) )
                return;

              $("a.grouped_elements").fancybox({
                transitionOut: "elastic",
                transitionIn: "elastic",
                speedIn: 200,
                speedOut: 200,
                overlayShow: !1,
                autoScale: 1 == TCParams.FancyBoxAutoscale ? "true" : "false",
                changeFade: "fast",
                enableEscapeButton: !0
              });

              //replace title by img alt field
              $('a[rel*=tc-fancybox-group]').each( function() {
                var title = $(this).find('img').prop('title');
                var alt = $(this).find('img').prop('alt');
                if (typeof title !== 'undefined' && 0 !== title.length)
                  $(this).attr('title',title);
                else if (typeof alt !== 'undefined' &&  0 !== alt.length)
                  $(this).attr('title',alt);
              });
            },


            /**
            * CENTER VARIOUS IMAGES
            * @return {void}
            */
            centerImages : function() {
              //SLIDER IMG + VARIOUS
              setTimeout( function() {
                //centering per slider
                $.each( $( '.carousel .carousel-inner') , function() {
                  $( this ).centerImages( {
                    enableCentering : 1 == TCParams.centerSliderImg,
                    imgSel : '.czr-item .carousel-image img',
                    oncustom : ['customizr.slid', 'simple_load'],
                    defaultCSSVal : { width : '100%' , height : 'auto' },
                    useImgAttr : true
                  });
                  //fade out the loading icon per slider with a little delay
                  //mostly for retina devices (the retina image will be downloaded afterwards
                  //and this may cause the re-centering of the image)
                  var self = this;
                  setTimeout( function() {
                      $( self ).prevAll('.tc-slider-loader-wrapper').fadeOut();
                  }, 500 );
                });
              } , 50);

              //Featured Pages
              $('.widget-front .thumb-wrapper').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                zeroTopAdjust : 1,
                leftAdjust : 2.5,
                oncustom : ['smartload', 'simple_load']
              });
              //POST LIST THUMBNAILS + FEATURED PAGES
              //Squared, rounded
              $('.thumb-wrapper', '.hentry' ).centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'simple_load']
              });

              //rectangulars in post lists
              $('.tc-rectangular-thumb', '.tc-post-list-context' ).centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : true,
                goldenRatioVal : TCParams.goldenRatio || 1.618,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
              });

              //SINGLE POST/PAGE THUMBNAILS
              $('.tc-rectangular-thumb' , '.tc-singular-thumbnail-wrapper').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'refresh-height', 'simple_load'], //bind 'refresh-height' event (triggered to the the customizer preview frame)
                setOpacityWhenCentered : true,//will set the opacity to 1
                opacity : 1
              });

              //POST GRID IMAGES
              $('.tc-grid-figure').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                oncustom : ['smartload', 'simple_load'],
                enableGoldenRatio : true,
                goldenRatioVal : TCParams.goldenRatio || 1.618,
                goldenRatioLimitHeightTo : TCParams.gridGoldenRatioLimit || 350
              } );
            },//center_images

            /**
            * PARALLAX
            * @return {void}
            */
            parallax : function() {
              $( '.parallax-item' ).czrParallax(
              {
                parallaxRatio : 0.55
              }
              );
            }
      };//_methods{}

      czrapp.methods.JQPlugins = czrapp.methods.JQPlugins || {};
      $.extend( czrapp.methods.JQPlugins = {} , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

/************************************************
* ADD SLIDER METHODS
*************************************************/
(function($, czrapp) {
      var _methods = {

            //INIT
            initOnDomReady : function() {
                  var self = this;

                  // cache jQuery el
                  this.$_sliders = $( 'div[id*="customizr-slider"]' );

                  //@todo EVENT
                  //Recenter the slider arrows on resize
                  czrapp.$_window.resize( function(){
                    self.centerSliderArrows();
                  });
            },



            fireSliders : function(name, delay, hover) {
              //Slider with localized script variables
              var _name   = name || TCParams.SliderName,
                  _delay  = delay || TCParams.SliderDelay;
                  _hover  = hover || TCParams.SliderHover;

              if ( 0 === _name.length )
                return;

              if ( 0 !== _delay.length && ! _hover ) {
                this.$_sliders.czrCarousel({
                    interval: _delay,
                    pause: "false"
                });
              } else if ( 0 !== _delay.length ) {
                this.$_sliders.czrCarousel({
                    interval: _delay
                });
              } else {
                this.$_sliders.czrCarousel();
              }
            },

            parallaxSliders : function() {
              if ( 'function' == typeof $.fn.czrParallax ) {
                $( '.czr-parallax-slider' ).czrParallax();
              }
            },

            manageHoverClass : function() {
              //add a class to the slider on hover => used to display the navigation arrow
              this.$_sliders.hover( function() {
                  $(this).addClass('tc-slid-hover');
                },
                function() {
                  $(this).removeClass('tc-slid-hover');
                }
              );
            },

            //SLIDER ARROWS
            centerSliderArrows : function() {
              if ( 0 === this.$_sliders.length )
                  return;
              this.$_sliders.each( function() {
                  var _slider_height = $( '.carousel-inner' , $(this) ).height();
                  $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
              });
            },


            //Slider swipe support with hammer.js
            addSwipeSupport : function() {
              if ( 'function' != typeof($.fn.hammer) || 0 === this.$_sliders.length )
                return;

              //prevent propagation event from sensible children
              this.$_sliders.on('touchstart touchmove', 'input, button, textarea, select, a:not(".tc-slide-link")', function(ev) {
                  ev.stopPropagation();
              });

              var _is_rtl = czrapp.$_body.hasClass('rtl');
              this.$_sliders.each( function() {
                  $(this).hammer().on('swipeleft', function() {
                      $(this).czrCarousel( ! _is_rtl ? 'next' : 'prev' );
                  });
                  $(this).hammer().on('swiperight', function(){
                      $(this).czrCarousel( ! _is_rtl ? 'prev' : 'next' );
                  });
              });
            },

            //Has to be fire on load after all other methods
            //@todo understand why...
            sliderTriggerSimpleLoad : function() {
              this.triggerSimpleLoad( this.$_sliders.find('.carousel-inner img') );
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
                this.timer = 0;
                this.increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
            },//init

            //Event Listener
            eventListener : function() {
                  var self = this;

                  czrapp.$_window.scroll( _.throttle( function() {
                        self.eventHandler( 'scroll' );
                  }, 50 ) );
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
              czrapp.$_body.on( 'tc-resize partialRefresh.czr', function( ev, param ) {
                    var _force = false;

                    if ( 'partialRefresh' == ev.type && 'czr' === ev.namespace && param.container && param.container.hasClass('tc-header')  ) {
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


      czrapp.methods.UserXP = {};
      $.extend( czrapp.methods.UserXP , _methods );
})(jQuery, czrapp);
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
            //cache jQuery el
            this.stickyHeaderCacheElements();

            //subclass properties
            this.elToHide         = []; //[ '.social-block' , '.site-description' ],
            this.customOffset     = TCParams.stickyCustomOffset || {};// defaults : { _initial : 0, _scrolling : 0 }

            this.timer            = 0;
            this.increment        = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
            this.triggerHeight    = 20; //0.5 * windowHeight;

            this.scrollingDelay   = 1 != TCParams.timerOnScrollAllBrowsers && czrapp.$_body.hasClass('ie') ? 50 : 5;
          },//init()


          triggerStickyHeaderLoad : function() {
            if ( ! this._is_sticky_enabled() )
              return;

            //LOADING ACTIONS
            czrapp.$_body.trigger( 'sticky-enabled-on-load' , { on : 'load' } );
          },

          stickyHeaderCacheElements : function() {
            //cache jQuery el
            this.$_resetMarginTop = $('#tc-reset-margin-top');
            this.$_sticky_logo    = $('img.sticky', '.site-logo');
            this.logo             = 0 === this.$_sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;
          },

          stickyHeaderEventListener : function() {
            //LOADING ACTIONS
            var self = this;
            czrapp.$_body.on( 'sticky-enabled-on-load' , function() {
              self.stickyHeaderEventHandler('on-load');
            });//.on()

            //RESIZING ACTIONS
            czrapp.$_window.on( 'tc-resize', function() {
              self.stickyHeaderEventHandler('resize');
            });

            //PARTIAL REFRESH ACTIONS
            czrapp.$_body.on( 'partialRefresh.czr', function( e, placement ) {
                  if ( placement.container && placement.container.hasClass( 'tc-header' )  ) {
                        self.stickyHeaderCacheElements();
                        self.stickyHeaderEventHandler('resize');
                  }
            });

            //SCROLLING ACTIONS
            czrapp.$_window.scroll( function() {
              self.stickyHeaderEventHandler('scroll');
            });

            //SIDENAV ACTIONS => recalculate the top offset on sidenav toggle
            czrapp.$_body.on( czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click' , '.sn-toggle', function() {
              self.stickyHeaderEventHandler('sidenav-toggle');
            });
          },



          stickyHeaderEventHandler : function( evt ) {
            if ( ! this._is_sticky_enabled() )
              return;

            var self = this;

            switch ( evt ) {
              case 'on-load' :
                self._prepare_logo_transition();
                setTimeout( function() {
                  self._sticky_refresh();
                  self._sticky_header_scrolling_actions();
                } , 20 );//setTimeout()
              break;

              case 'scroll' :
                var _delay = 0;

                 //use a timer
                if ( this.timer) {
                  this.increment++;
                  clearTimeout(self.timer);
                }

                if ( this.increment > 5 )
                  //decrease the scrolling trigger delay when smoothscroll on to avoid not catching the scroll when scrolling fast and sticky header not already triggered
                  _delay = ! ( czrapp.$_body.hasClass('tc-smoothscroll') && ! this._is_scrolling() ) ? this.scrollingDelay : 15;

                this.timer = setTimeout( function() {
                    self._sticky_header_scrolling_actions();
                }, _delay );
              break;

              case 'resize' :
              case 'sidenav-toggle' :
                self._set_sticky_offsets();
                self._set_header_top_offset();
                self._set_logo_height();
              break;
            }
          },




          //STICKY HEADER SUB CLASS HELPER (private like)
          _is_scrolling : function() {
            return czrapp.$_body.hasClass('sticky-enabled') ? true : false;
          },

          //STICKY HEADER SUB CLASS HELPER (private like)
          _is_sticky_enabled : function() {
            return czrapp.$_body.hasClass('tc-sticky-header') ? true : false;
          },

          //STICKY HEADER SUB CLASS HELPER (private like)
          _get_top_offset : function() {
            //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
            //custom offset : are we scrolling ? => 2 custom top offset values can be defined by users : initial and scrolling
            //make sure custom offset are set and numbers
            var initialOffset   = 0,
                that            = this,
                customOffset    = +this._get_custom_offset( that._is_scrolling() ? '_scrolling' : '_initial' );

            if ( 1 == this.isUserLogged() && ! this._isCustomizing() ) {
              if ( 580 < czrapp.$_window.width() )
                initialOffset = czrapp.$_wpadminbar.height();
              else
                initialOffset = ! this._is_scrolling() ? czrapp.$_wpadminbar.height() : 0;
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
            return;
          },




          //STICKY HEADER SUB CLASS HELPER (private like)
          _set_sticky_offsets : function() {
            var self = this;

            //Reset all values first
            czrapp.$_tcHeader.css('top' , '');
            czrapp.$_tcHeader.css('height' , 'auto' );
            this.$_resetMarginTop.css('margin-top' , '' ).show();

            //What is the initial offset of the header ?
            var headerHeight    = czrapp.$_tcHeader.outerHeight(true); /* include borders and eventual margins (true param)*/
            //set initial margin-top = initial offset + header's height
            this.$_resetMarginTop.css('margin-top' , + headerHeight  + 'px');
          },

          //STICKY HEADER SUB CLASS HELPER (private like)
          _set_header_top_offset : function() {
            var self = this;
            //set header initial offset
            czrapp.$_tcHeader.css('top' , self._get_top_offset() );
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
          },


          //SCROLLING ACTIONS
          _sticky_header_scrolling_actions : function() {
            this._set_header_top_offset();

            var self = this;
            //process scrolling actions
            if ( czrapp.$_window.scrollTop() > this.triggerHeight ) {
              if ( ! this._is_scrolling() ) {
                czrapp.$_body.addClass("sticky-enabled").removeClass("sticky-disabled")
                             .trigger('tc-sticky-enabled');
                // set the logo height, makes sense just when the logo isn't shrinked
                if ( ! czrapp.$_tcHeader.hasClass('tc-shrink-on') )
                  self._set_logo_height();
              }
            }
            else if ( this._is_scrolling() ){
              czrapp.$_body.removeClass("sticky-enabled").addClass("sticky-disabled")
                           .trigger('tc-sticky-disabled');
              setTimeout( function() { self._sticky_refresh(); } ,
                self._isCustomizing ? 100 : 20
              );
              //additional refresh for some edge cases like big logos
              setTimeout( function() { self._sticky_refresh(); } , 200 );
            }
          }
    };//_methods{}

    czrapp.methods.StickyHeader = {};
    $.extend( czrapp.methods.StickyHeader , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
/************************************************
* STICKY FOOTER SUB CLASS
*************************************************/
(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
              this.$_push   = $('#tc-push-footer');
              this._class   = 'sticky-footer-enabled';
              this.$_page   = $('#tc-page-wrap');

              if ( 1 != TCParams.stickyHeader ) {//sticky header fires a resize
                var self = this;
                setTimeout( function() {
                        self._apply_sticky_footer(); }, 50
                );
              }
            },

            /***********************************************
            * DOM EVENT LISTENERS AND HANDLERS
            ***********************************************/
            stickyFooterEventListener : function() {
              var self = this;

              // maybe apply sticky footer on window resize
              czrapp.$_window.on( 'tc-resize', function() {
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
                  //to avoid the creation of a function inside a loop
                  //but still allow the access to "this"
                  var func = function() { return self._apply_sticky_footer() ;};
                  for ( var i = 0; i<5; i++ ) /* I've seen something like that in twentyfifteen */
                    setTimeout( func, 50 * i);
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
              }else if ( this.$_push.hasClass(this._class) ) {
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
              return czrapp.$_body.hasClass('tc-sticky-footer');
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

})(jQuery, czrapp);
var czrapp = czrapp || {};
/************************************************
* SIDE NAV SUB CLASS
*************************************************/
(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
              this.$_sidenav                = $( '#tc-sn' );

              if ( ! this._is_sn_on() )
                return;

              //cache jQuery el
              this.$_page_wrapper           = $('#tc-page-wrap');
              this.$_page_wrapper_node      = this.$_page_wrapper.get(0);
              this.$_page_wrapper_btn       = $('.btn-toggle-nav', '#tc-page-wrap');

              this.$_sidenav_inner          = $( '.tc-sn-inner', this.$_sidenav);

              this._toggle_event            = 'click';// before c4, was czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click';

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

      czrapp.methods.SideNav = {};
      $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
      var _methods =  {
            fireDropDown : function() {
              this.$_sidenav                = $( '#tc-sn' );
              this._dd_first_selector       = '.menu-item-has-children.dropdown > .dropdown-menu' ;
              this.$_nav_collapse           = czrapp.$_tcHeader.length > 0 ? czrapp.$_tcHeader.find( '.navbar-wrapper .nav-collapse' ) : [];
              this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];

              if ( ! this._has_dd_to_move() )
                return;

              //cache jQuery el
              this.$_navbar_wrapper         = this.$_nav_collapse.closest( '.navbar-wrapper' );
              this.$_nav                    = this.$_nav_collapse.find( '.nav' );
              this.$_head                   = $( 'head' );

              //other useful vars
              this._dyn_style_id            = 'tc-dropdown-dyn-style';
              this._prop                    = czrapp.$_body.hasClass('rtl') ? 'right' : 'left';

              //fire event listener
              this.dropdownPlaceEventListener();

              //place dropdowns on init
              this._place_dropdowns();
            },//init()


            dropdownPlaceCacheElements : function() {
              //cache jQuery el
              this.$_nav_collapse           = czrapp.$_tcHeader.length > 0 ? czrapp.$_tcHeader.find( '.navbar-wrapper .nav-collapse' ) : [];
              this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];
              this.$_navbar_wrapper         = this.$_nav_collapse.length ? this.$_nav_collapse.closest( '.navbar-wrapper' ) : [];
            },

            /***********************************************
            * DOM EVENT LISTENERS AND HANDLERS
            ***********************************************/
            dropdownPlaceEventListener : function() {
                  var self    = this,
                      _events = 'tc-resize sn-open sn-close tc-sticky-enabled tc-place-dropdowns partialRefresh.czr';

                  //Any event which may have resized the header
                  czrapp.$_body.on( _events, function( evt, data ) {
                        if ( 'partialRefresh' === evt.type && 'czr' === evt.namespace && data.container && data.container.hasClass( 'tc-header' )  ) {
                              self.dropdownPlaceCacheElements();
                        }
                        self.dropdownPlaceEventHandler( evt, 'resize' );
                  });
            },


            dropdownPlaceEventHandler : function( evt, evt_name ) {
              var self = this;

              switch ( evt_name ) {
                case 'resize' :
                  setTimeout( function(){
                    self._place_dropdowns();
                  }, 250);
                break;
              }
            },


            _place_dropdowns : function () {
              var _dd = this._get_dd_to_move();
              if ( ! _dd.length )
                return;

              this._staging();
              this._move_dropdown( _dd );
              this._write_dyn_style();
              this._unstaging();
            },



            /***********************************************
            * HELPERS
            ***********************************************/
            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //When checking if there's something to move does not make sense at the start
            //1) there's no navbar collapse in the header
            //2) there are no dropdowns to move in the header
            _has_dd_to_move : function() {
              if ( this.$_nav_collapse.length < 1 )
                return false;
              if ( this.$_nav.length && this.$_nav.find( this._dd_first_selector ) < 1 )
                return false;

              return true;
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //returns the dropdowns to move on resize?
            //a) when the nav-collapse is not absolute => we're not in mobile menu case => no dd to move
            //b) .tc-header .nav is hidden (case: second menu hidden in mobiles ) => no dd to move
            //c) return the .tc-header .nav dropdown children
            _get_dd_to_move : function() {
              if ( 'absolute' == this.$_nav_collapse.css('position') )
                return {};
              if ( ! this.$_nav.is(':visible') )
                return {};
              return this.$_nav.find( this._dd_first_selector );
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //Prepare the environment
            //What we do here:
            //1) we 'suspend' the transitions on submenus
            //2) we add a dynamic style which:
            // a) sets the max width of the dropdown to the window's width
            // b) allows braking words for submenus label
            _staging : function() {
              this._window_width = czrapp.$_window.width();
              //remove submenu fade, transitions corrupt the offset computing
              if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade') )
                // tc-submenu-fade-susp(ended) is a dummy class we add for the future check in _unstaging
                this.$_navbar_wrapper.removeClass('tc-submenu-fade').addClass('tc-submenu-fade-susp');
              var _max_width            = this._window_width - 40,
                  _dyn_style_css_prefix = '.tc-header .nav-collapse .dropdown-menu';

              //the max width of a drodpdown must be the window's width (- 40px aesthetical )
              this._dyn_style  = _dyn_style_css_prefix + ' {max-width: ' + _max_width + 'px;}';
              //following is to ensure that big labels are broken in more lines if they exceed the max width
              //probably due to a bug, white-space: pre; doesn't work fine in recent firefox.
              //Anyway this just means that the following rule (hence the prev) for them is useless => doesn't introduce a bug
              //p.s. this could be moved in our main CSS
              this._dyn_style += _dyn_style_css_prefix + ' > li > a { word-wrap: break-word; white-space: pre; }';
              this._write_dyn_style();
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //Reset temporary changes to the environment performed in the staging phase
            //What we do here:
            //1) Re-add the transitions on submenus if needed
            _unstaging : function() {
              //re-add submenu fade, transitions corrupt the offset computing
              if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade-susp') )
                this.$_navbar_wrapper.removeClass('tc-submenu-fade-susp').addClass('tc-submenu-fade');
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //Write the dynamic style into the HEAD
            _write_dyn_style : function() {
              var $_dyn_style_el = this.$_head.find('#' + this._dyn_style_id);

              //there's already a _dyn_style_el, so remove it
              //I thought that remove/create a new element every time is worse than just have an empty style, but looks like that $_dyn_style_el.html( _dyn_style ) isnt' cross-browser, gives me errors in ie8
              if ( $_dyn_style_el.length > 0 )
                $_dyn_style_el.remove();
              if ( this._dyn_style )
                // I would have loved ot use getOverrideStyle, but couldn't get it to work -> Error: getOverrideStyle is not a function
                // I'm probabably missing something. Ref: http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSStyleDeclaration
                // probably not very supported by browsers?
                // getOverrideStyle($_dropdown[0], ':before');
                $("<style type='text/css' id='" + this._dyn_style_id +"'>" + this._dyn_style + "</style>")
                  .appendTo( this.$_head );
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            // Moving dropdown core
            _move_dropdown : function( $dropdown_menu ) {
              // does dropdown_menu element exists?
              if ( $dropdown_menu && $dropdown_menu.length ) {
                if ( $dropdown_menu.length > 1 ) {
                  var self = this;
                  // is $dropdown_menu an array of elements ? if yes call this function over them
                  $.each( $dropdown_menu, function(){
                    self._move_dropdown( $(this) );
                  });
                  return;
                }//end array of dropdown case
              }else //no dropdown
                return;
              // Moving core
              var _is_dropdown_visible = $dropdown_menu.is(':visible');
              if ( ! _is_dropdown_visible )
                $dropdown_menu.css('display', 'block').css('visibility', 'hidden');

              //first thing to do; reset all changes why?
              //example, say the last menu item has a submenu which has been moved when window's width == 1200px,
              //then the window is shrinked to 1000px and the last menu item drops on a new line. In this case :
              //a) the "moving" might not be needed anymore 'cause it might not overflows the window
              //b) even worse, the "moving" might have made it overflow on the opposite side.
              this._set_dropdown_offset( $dropdown_menu, '' );
              //get the current overflow
              var _overflow     = this._get_dropdown_overflow( $dropdown_menu );

              if ( _overflow )
                this._set_dropdown_offset( $dropdown_menu, _overflow );

              //move all the childrens (1st level of children ) which are dropdowns
              var $_children_dropdowns = $dropdown_menu.children('li.dropdown-submenu');
                if ( $_children_dropdowns.length )
                  this._move_dropdown( $_children_dropdowns.children('ul.dropdown-menu') );

              //reset 'visibility-manipulation'
              if ( ! _is_dropdown_visible )
                $dropdown_menu.css('display', '').css('visibility', '');
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //Set dropdown offset + first dropdown level top arrow offset accordingly
            _set_dropdown_offset : function( $dropdown_menu, _dropdown_overflow ) {
              var _offset = '';

              if ( _dropdown_overflow ) {
                var $_parent_dropdown  = $dropdown_menu.parent('.menu-item-has-children'),
                    _is_dropdown_submenu = $_parent_dropdown.hasClass('dropdown-submenu');

                //is submenu 2nd level?
                if ( _is_dropdown_submenu ) {
                  _offset = parseFloat( $dropdown_menu.css( this._prop ) ) - _dropdown_overflow - 5;
                  //does the parent menu item have "brothers" after it? in this case be sure the new position will
                  //not make it completely overlap parent menu item sibling. We can left 30px of space so
                  //the user can access the sibling menu item.
                  //So the condition are:
                  //1) the parent menu item has siblings
                  //and
                  //2) there's a space < 30px between the starting edges of the parent and child dropdown
                  //or
                  //2.1) there's a space < 30px between the ending edges of the parent and child dropdown
                  if ( $_parent_dropdown.next('.menu-item').length ) {
                    var _submenu_overflows_parent = this._get_element_overflow( $dropdown_menu, _offset, $_parent_dropdown );
                    if ( _offset < 30  || _submenu_overflows_parent < 30 )
                      //the new offset is then the old one minus the amount of overflow (ex. in ltr align parent and child right edge ) minus 30px
                      _offset = _offset - _submenu_overflows_parent - 30;
                  }
                } else {
                  _offset = -20 - _dropdown_overflow; //add some space (20px) on the right(rtl-> left)
                  // when is dropdown first level we need to move the top arrow
                  // we need the menu-item-{id} class to build the css rule
                  var _menu_id = $_parent_dropdown.attr('class').match(/(menu|page)-item-\d+/);
                  _menu_id = _menu_id ? _menu_id[0] : null;
                  if ( _menu_id )
                    this._set_dropdown_arrow_style( _menu_id, _offset );
                }
              }
              //in any case write the dropdown offset css:
              //a dropdown which doesn't have to be moved will not be passed to this function, so no problem. The only case when this is needed is when we reset the dropdowns offset before checking whether or not we have to move it, Maybe we can fine tune this adding a css class to the moved dropdowns so we'll reset just them.
              $dropdown_menu.css( this._prop, _offset );
            },

            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //compute the dropdown overflow
            _get_dropdown_overflow : function ( $dropdown_menu ) {
              var overflow = null,
                  _t_overflow;
               // how we compute the overflow
               // ltr
               if ( 'left' == this._prop ) {
                 // the overlfow is: the absolute position left/right of the elemnt + its width - the window's width
                 // so it represents the amount of "width" which overflows the window
                 _t_overflow = this._get_element_overflow( $dropdown_menu, $dropdown_menu.offset().left, {}, this._window_width );
                 // a positive overflow means that the dropdown goes off the window
                 // anyways I decided to adjust its position even if the gap between the end of the dropdown
                // and the window's width is < 5 (6), just to avoid dropdown edges so close to the end of the window
                overflow = _t_overflow > -5 ? _t_overflow : overflow ;
              }else { // rtl
                //the overflow is: the left offset * -1 if less than 5px
                //note: jQuery.offset() gives just top and left properties.
                _t_overflow = $dropdown_menu.offset().left;
                overflow  = _t_overflow < 5 ? -1 * _t_overflow : overflow;
              }
                return overflow;
            },
            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //compute the overflow of an element given a parent an an initial left offset
            _get_element_overflow : function ( $_el, _offset, $_parent, _parent_width ) {
              _parent_width = $_parent.length ? $_parent.width() : _parent_width;
              return $_el.width() + _offset - _parent_width;
            },
            //DROPDOWN PLACE SUB CLASS HELPER (private like)
            //compute and set the dropdown first level top arrow offset
            //which is the original offset for the pseudo element before and after minus the
            //shift amount applied to the dropdown
            _set_dropdown_arrow_style : function( _menu_id, _offset ) {
              //9px is static to avoid using the following via javascript
              //window.getComputedStyle($_dropdown[0], ':before').left ;
              var _arrow_before_offset    = +9 - _offset,
                  _arrow_after_offset     = _arrow_before_offset + 1,
                  _arrow_css_rule_prefix  = '.tc-header .navbar .nav > .' + _menu_id + ' > .dropdown-menu',

                 _arrow_before_css_rule  = _arrow_css_rule_prefix + ":before { " + this._prop + ": " + _arrow_before_offset + "px;}",
                 _arrow_after_css_rule   = _arrow_css_rule_prefix + ":after { " + this._prop + ": " + _arrow_after_offset + "px;}";

              this._dyn_style += "\n" + _arrow_before_css_rule + "\n" + _arrow_after_css_rule;
            }
      };//_methods{}

      czrapp.methods.Dropdowns = {};
      $.extend( czrapp.methods.Dropdowns , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
//@global TCParams
/************************************************
* LET'S DANCE
*************************************************/
( function ( czrapp, $, _ ) {
      //adds the server params to the app now
      czrapp.localized = TCParams || {};

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
      //falls back on TCParams.isWPMobile ( which can be cached, so not fully reliable )
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
                            'dropCaps',
                            'extLinks',
                            'fancyBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnDomReady',
                            'fireSliders',
                            'parallaxSliders',
                            'manageHoverClass',
                            'centerSliderArrows',
                            'addSwipeSupport',
                            'sliderTriggerSimpleLoad'
                      ]
                },
                dropdowns : {
                      ctor : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [ 'fireDropDown' ]
                },

                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'initOnDomReady',
                            'eventListener',
                            'outline',
                            'smoothScroll',
                            'anchorSmoothScroll',
                            'backToTop',
                            'widgetsHoverActions',
                            'attachmentsFadeEffect',
                            'clickableCommentButton',
                            'dynSidebarReorder',
                            'dropdownMenuEventsHandler',
                            'menuButtonHover',
                            'secondMenuRespActions'
                      ]
                },
                stickyHeader : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyHeader ),
                      ready : [
                            'initOnDomReady',
                            'stickyHeaderEventListener',
                            'triggerStickyHeaderLoad'
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
    //czrapp.localized = TCParams
    czrapp.ready.then( function() {
          var pluginCompatParams = ( czrapp.localized && czrapp.localized.pluginCompats ) ? czrapp.localized.pluginCompats : {},
              frontHelpNoticeParams = ( czrapp.localized && czrapp.localized.frontHelpNoticeParams ) ? czrapp.localized.frontHelpNoticeParams : {};

          //PARALLAX SLIDER
          $( function( $ ) {
                if ( czrapp.localized.isParallaxOn ) {
                      $( '.czr-parallax-slider' ).czrParallax( { parallaxRatio : czrapp.localized.parallaxRatio || 0.55 } );
                }
          });

          //PLUGIN COMPAT
          //Optimize Press
          if ( pluginCompatParams.optimizepress_compat && pluginCompatParams.optimizepress_compat.remove_fancybox_loading ) {
                if (typeof(opjq) !== 'undefined') {
                      opjq(document).ready( function() {
                          opjq('#fancybox-loading').remove();
                      } );
                }
          }

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