var czrapp = czrapp || {};

/*************************
* JS LOG VARIOUS UTILITIES
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
      //@param queryParams = {}
      czrapp.doAjax = function( queryParams ) {
            //do we have a queryParams ?
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
            //Note : the nonce might be checked server side ( not in all cases, only when writing in db )  with check_ajax_referer( 'hu-front-nonce', 'HuFrontNonce' )
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
})(jQuery, czrapp);var czrapp = czrapp || {};
czrapp.methods = {};

(function( $ ){
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

})( jQuery );//@global CZRParams
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
                        $_header       : $('.tc-header'),

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
                  return ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 768px)' ).matches ) || ( this._isCustomizing() && 'desktop' != this.previewDevice() );
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
                  setTimeout( function(){ self.emit('centerImages'); }, delay || 50 );
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
                  // $smart_load_opts       = apply_filters( 'tc_img_smart_load_options' , array(

                  //          'parentSelectors' => array(
                  //              '[class*=grid-container], .article-container',
                  //              '.__before_main_wrapper',
                  //              '.widget-front',
                  //              '.post-related-articles',
                  //              '.tc-singular-thumbnail-wrapper',
                  //          ),
                  //          'opts'     => array(
                  //              'excludeImg' => array( '.tc-holder-img' )
                  //          )

                  //  ));
                  if (  smartLoadEnabled ) {
                        $( _where ).imgSmartLoad(
                          _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {}
                        );
                  }

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
                  var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');

                  //Featured pages and classical grid are always centered
                  // $('.tc-grid-figure, .widget-front .tc-thumbnail').centerImages( {
                  //       enableCentering : 1,
                  //       oncustom : ['smartload', 'refresh-height', 'simple_load'],
                  //       zeroTopAdjust: 0,
                  //       enableGoldenRatio : false,
                  // } );
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



                  // if for any reasons, the centering did not happen, the imgs will not be displayed because opacity will stay at 0
                  // => the opacity is set to 1 as soon as v-centered or h-centered has been added to a img element candidate for centering
                  // @see css
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
                  //For smartloaded image, let's wait for the smart load to happen, for the others, let's do it now without delay
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

                  //then last check
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 0 } );
                        });
                  }, 1000 );
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
                                    //open the selected carousel gallery item
                                    $_gallery_crsl.find( '.is-selected img' ).trigger('click');
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

                  /* Fire fittext */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '[id^="customizr-slider-main"] .carousel-inner', function() {
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


                  /* Disable controllers when the first or the latest slide is in the viewport (for the related posts) */
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );

                  /* for gallery carousels to preserve the dragging we have to move the possible background gallery link inside the flickity viewport */
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-gallery.czr-carousel .carousel-inner', self._move_background_link_inside );
                  /*Handle custom nav */
                  // previous
                  czrapp.$_body.on( 'click tap prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  // next
                  czrapp.$_body.on( 'click tap next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );

                  //FIRE CAROUSELS
                  this.fireRelatedPostsCarousel();
                  this.fireGalleriesCarousel();

                  //If the user has enabled the smartload for slider ( enabled by default )
                  //1) the first (is-selected slide is smartloaded)
                  //2) then we react on 'select.flickity' to smartload the other => append css loader + smartload
                  //3) and on 'smartload' ( triggered by our smartload plugin ) => remove css loader
                  this.fireMainSlider().centerMainSlider();

            },//_init()

            fireRelatedPostsCarousel : function() {
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

            },

            fireGalleriesCarousel : function() {
                  $('.czr-gallery.czr-carousel .carousel-inner').flickity({
                      prevNextButtons: false,
                      pageDots: false,
                      wrapAround: true,
                      imagesLoaded: true,
                      setGallerySize: false,
                      cellSelector: '.carousel-cell',
                      accessibility: false,
                      dragThreshold: 10,
                      lazyLoad: true,
                      freeScroll: false
                  });

            },

            fireMainSlider : function() {
                  var $_main_slider = $('.carousel-inner', '[id^="customizr-slider-main"]'),
                      _css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>',
                      _cellSelector = '.carousel-cell';

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        $_main_slider.on('czr-flickity-ready.flickity', function() {
                              var _getSelectedCell = function() {
                                    return $( $_main_slider.data('flickity').selectedCell.element );
                              };
                              //always smartload the first slide
                              $(this).find( _cellSelector + '.is-selected').imgSmartLoad().data( 'czr_smartLoaded', true );

                              //then each time an image is selected
                              //1) append the css loader
                              //2) smartload
                              $(this).on('select.flickity', function() {
                                  if ( ! _getSelectedCell().data('czr_smartLoaded') ) {
                                        _getSelectedCell().append( _css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                                        _getSelectedCell().imgSmartLoad().data( 'czr_smartLoaded', true );
                                  }
                              });

                              $(this).on( 'smartload', _cellSelector , function() {
                                      _getSelectedCell().find('.czr-css-loader').fadeOut( {
                                            duration: 'fast',
                                            done : function() { $(this).remove();}
                                      } );
                              });
                        });
                  }

                  //FIRE THE SLIDER
                  if ( $_main_slider.length > 0 ) {
                        //number of slides
                        //check if has one single slide
                        //in this case we don't allow pageDots, nor the slide will be draggable
                        var _is_single_slide = 1 == $_main_slider.find( _cellSelector ).length,
                            _autoPlay           = $_main_slider.data('slider-delay');

                        _autoPlay           =  ( _.isNumber( _autoPlay ) && _autoPlay > 0 ) ? _autoPlay : false;

                        $_main_slider.flickity({
                            prevNextButtons: false,
                            pageDots: !_is_single_slide,
                            draggable: !_is_single_slide,

                            wrapAround: true,

                            imagesLoaded: true,

                            //lazyLoad: true,

                            setGallerySize: false,
                            cellSelector: _cellSelector,

                            dragThreshold: 10,

                            autoPlay: _autoPlay, // {Number in milliseconds }

                            accessibility: false,
                        });
                  }
                  return this;
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
                                    oncustom : ['settle.flickity', 'simple_load', 'smartload'],
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
                              }, 300 );

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
                  //bail if we still don't have a flickity instance at this point
                  if ( ! _flickity )
                    return;

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
              this.isScrolling.bind( function( to, from ) {
                    //self.scrollPosition( czrapp.$_window.scrollTop() );
                    czrapp.$_body.toggleClass( 'is-scrolling', to );
                    if ( ! to ) {
                          czrapp.trigger( 'scrolling-finished' );
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
              czrapp.$_window.resize( _.throttle( function( ev ) { self.windowWidth( czrapp.$_window.width() ); }, 10 ) );
              czrapp.$_window.scroll( _.throttle( function() {
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

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {

        /*-----------------------------------------------------
        * MAIN
        ------------------------------------------------------*/
        stickifyHeader : function() {
              //Do nothing if there's no header => plugins compatibility
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
              //this.stickyHeaderClass      = 'primary-navbar__wrapper';
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
              //this.isSticky               = new czrapp.Value( false );
              this.stickyStage            = new czrapp.Value( '_not_set_' );
              this.shrinkBrand            = new czrapp.Value( false );//Toggle a class to maybe shrink the title or logo if the option is on


              //// SETUP LISTENERS ////
              //react to current sticky selector
              //will be set on init and reset on resize
              this.currentStickySelector.bind( function( to, from ) {
                    var _reset = function() {
                          czrapp.$_header.css( { 'height' : '' });
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          self.stickyMenuDown( false );
                          self.stickyMenuWrapper = false;
                          self.hasStickyCandidate( false );
                    };

                    //we have a candidate
                    if ( ! _.isEmpty( to ) ) {
                          self.hasStickyCandidate( 1 == czrapp.$_header.find( to ).length );
                          //Does this selector actually exists ?
                          if ( ! self.hasStickyCandidate() ) {
                                _reset();
                          }
                          else {
                                //cache the menu wrapper now
                                self.stickyMenuWrapper = czrapp.$_header.find( to );
                                //make sure we have the transition class in any cases
                                // + Always set the header height on dom ready
                                //=> will prevent any wrong value being assigned if menu is expanded before scrolling
                                //If the header has an image, defer setting the height when the .site-image is loaded
                                //=> otherwise the header height might be wrong because based on an empty img
                                var $_header_logo = self.stickyMenuWrapper.find('.navbar-brand-sitelogo img');
                                if ( 1 == $_header_logo.length ) {

                                      //We introduce a custom event here
                                      //=> because we need to handle the case when the image is already loaded. This way we don't need to fire the load() function twice.
                                      //=> This fixes : https://wordpress.org/support/topic/navbar-not-full-width-until-scroll/
                                      // reported here : https://github.com/presscustomizr/hueman/issues/508
                                      $_header_logo.bind( 'header-logo-loaded', function() {
                                            czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                      });

                                      //If the image status is "complete", then trigger the custom event right away, else bind the "load" event
                                      //http://stackoverflow.com/questions/1948672/how-to-tell-if-an-image-is-loaded-or-cached-in-jquery
                                      if ( $_header_logo[0].complete ) {
                                            $_header_logo.trigger('header-logo-loaded');
                                      } else {
                                        $_header_logo.load( function( img ) {
                                              $_header_logo.trigger('header-logo-loaded');
                                        } );
                                      }
                                } else {
                                    // + Always set the header height on dom ready
                                    //=> will prevent any wrong value being assigned if menu is expanded before scrolling

                                    //To avoid decimal roundings use element pure js element.getBoundingClientRect().height
                                    //instead of $.height()
                                    //https://developer.mozilla.org/it/docs/Web/API/Element/getBoundingClientRect
                                    czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                    //Note: actually we should not really set the header height on init, since we fix the sticky candidate
                                    //on fly...
                                }
                          }
                    } else {//we don't have a candidate
                          _reset();
                    }
              });

              //STICKY STATE LISTENER
              // this.isSticky.bind( function( _isSticky ) {
              //       czrapp.$_header.toggleClass( 'is-sticky', _isSticky );
              // });


              //FIXED POSITION LISTENER
              //adding the 'fixed-header-on' class makes the sticky candidate position:fixed
              this.isFixedPositionned.bind( function( isFixed ) {
                    czrapp.$_header.toggleClass( 'fixed-header-on', isFixed ).toggleClass( 'is-sticky', isFixed );
                    self._pushPrimaryNavBarDown( isFixed );
                    //set the skink state
                    self.shrinkBrand( isFixed );
              });

              //SHRINK BRAND LISTENER
              this.shrinkBrand.bind( function( isShrinked ) {
                    //Toggle a class to maybe shrink the title or logo if the option is on
                    czrapp.$_header.toggleClass( 'can-shrink-brand', isShrinked );
                    //let's emulate a resize when unshrinking
                    //=> this fixes a potential issue when
                    //1) user has scrolled
                    //2) changed a tabled layout from portrait to landscape
                    //3) scrolled up to top
                    //=> might lead to a wrong header's height
                    //=> emulating a 'resize' will fire the isResizing callbacks and reset the header's height
                    if ( ! isShrinked ) {
                          _.delay( function() {
                                if ( self.scrollPosition() < self.stickyHeaderThreshold ) {
                                      czrapp.$_header.trigger( 'czr-resize');
                                }
                          }, 400 );//<=400ms gives us enough room to finish the title or logo unshrinking animation
                    }
              });


              //SCROLL POSITION LISTENER
              //Animate based on scroll position.
              //Must have a sticky candidate
              var _setStickynessStatesOnScroll = function( to, from ) {
                    if ( ! self.hasStickyCandidate() )
                      return;

                    to = to || self.scrollPosition();
                    from = from || self.scrollPosition();

                    var reachedTheTop = ( to == from ) && 0 === to;
                    //Set up only when
                    //1) We've reached the top <=> to = from = 0 ( => always fired when scroll stopped after 500 ms )
                    //2) we're not on top and scroll up is significant => avoid revealing the menu for minor scroll up actions on mobile devices
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
                          //animate up but don't set to position fixed too early
                          self.stickyMenuDown( true );

                    } else if ( 'down' == self.scrollDirection() && to > ( self.topStickPoint() + _h ) && to < ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          self.stickyStage( 'down_middle' );
                          self.isFixedPositionned( false );
                          //animate up but don't set to position fixed too early
                          self.stickyMenuDown( false );

                    } else if ( 'down' == self.scrollDirection() && to >= ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          if ( 'stick_always' == self.userStickyOpt()  ) {
                                var _dodo = function() {
                                      self.stickyMenuDown( false, { fast : true,  } ).done( function() {
                                            self.stickyMenuDown( true, { forceFixed : true } ).done( function() {});
                                            self.stickyStage( 'down_after' );
                                      });
                                };
                                // if ( 'up' == self.stickyStage() ) {
                                //       _dodo();
                                // }
                                //When stick_always, we need to make sure that the sticky candidate is going up and the down.
                                //No need to do this if it's already down
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
                          //self.isFixedPositionned( to > self.topStickPoint() );

                          //on scroll up maybe update isFixedPositionned only if already sticky
                          if ( self.isFixedPositionned() ) {
                                self.isFixedPositionned( to > self.topStickPoint() );
                          }
                    }
              };


              //SCROLL POSITION LISTENER
              this.scrollPosition.bind( function( to, from ) {
                    _setStickynessStatesOnScroll( to, from );
                    self.shrinkBrand( self.isFixedPositionned() );
              } );


              //czrapp.bind( 'page-scrolled-top', _mayBeresetTopPosition );
              var _maybeResetTop = function() {
                    if ( 'up' == self.scrollDirection() ) {
                          self._mayBeresetTopPosition();
                    }
              };
              //react on scrolling finished <=> after the timer
              czrapp.bind( 'scrolling-finished', _maybeResetTop );
              //always make sure that we didn't miss a sticky stage while scrolling fast
              czrapp.bind( 'scrolling-finished', function() {
                    _.delay( function() {
                          _setStickynessStatesOnScroll();
                    }, 400);
              });
              //react on topbar collapsed, @see topNavToLife
              czrapp.bind( 'topbar-collapsed', _maybeResetTop );


              //STICKY MENU DOWN LISTENER
              //animate : make sure we don't hide the menu when too close from top
              //only animate when user option is set to stick_up
              self.stickyMenuDown.validate = function( newValue ) {
                    if ( ! self.hasStickyCandidate() )
                      return false;
                    // fixes Mobile menu : Don’t collapse long menus when scrolling down https://github.com/presscustomizr/customizr/issues/1226
                    if ( self._isMobileMenuExpanded() )
                      return this._value;

                    if ( self.scrollPosition() < self.stickyHeaderThreshold && ! newValue ) {
                          if ( ! self.isScrolling() ) {
                                //print a message when attempt to programmatically hide the menu
                                czrapp.errorLog('Menu too close from top to be moved up');
                          }
                          return self.stickyMenuDown();
                    } else {
                          return newValue;
                    }
              };

              //This czrapp.Value() is bound with a callback returning a promise ( => in this case an animation lasting a few ms )
              //This allows to write this type of code :
              //self.stickyMenuDown( false ).done( function() {
              //    $('#header').css( 'padding-top', '' );
              //});
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


              /*-----------------------------------------------------
              * (real) RESIZE ( refreshed every 50 ms ) OR REFRESH EVENT
              ------------------------------------------------------*/
              self.isResizing.bind( function() {self._refreshOrResizeReact(); } );//resize();
              czrapp.$_header.on( 'refresh-sticky-header', function() { self._refreshOrResizeReact(); } );

              /*-----------------------------------------------------
              * INITIAL ACTIONS
              ------------------------------------------------------*/
              //Set initial sticky selector
              self._setStickySelector();

              //distance from the top where we should decide if fixed or not. => function of topbar height + admin bar height
              //=> set on instantiation and reset on resize
              this.topStickPoint          = new czrapp.Value( self._getTopStickPoint() );

              //set fixed-header-on if is desktop because menu is already set to fixed position, we want to have the animation from the start
              // + Adjust padding top if desktop sticky
              if ( ! self._isMobile() && self.hasStickyCandidate() ) {
                    self._adjustDesktopTopNavPaddingTop();
              }

        },//stickify




        /*-----------------------------------------------------
        * ANIMATE
        ------------------------------------------------------*/
        //args = { direction : up / down , forceFixed : false, menu_wrapper : $ element, fast : false }
        _animate : function( args ) {
              // args = _.extend(
              //       {
              //             direction : 'down',
              //             forceFixed : false,
              //             menu_wrapper : {},
              //             fast : false,
              //       },
              //       args || {}
              // );
              var dfd = $.Deferred(),
                  self = this,
                  $menu_wrapper = ! args.menu_wrapper.length ? czrapp.$_header.find( self.currentStickySelector() ) : args.menu_wrapper,
                  _startPosition = self.scrollPosition(),
                  _endPosition = _startPosition;


              this.animationPromise = dfd;

              //Bail here if  we don't have a menu element
              if ( ! $menu_wrapper.length )
                return dfd.resolve().promise();

              //Make sure we are position:fixed with the 'fixed-header-on' css class before doing anything
              //If the position is already fixed, don't move,
              //if not, make sure we are currently scrolling up before setting it, otherwise the sticky candidate will appear for a few ms before being animated up
              self.isFixedPositionned( self.isFixedPositionned() ? true : ( 'up' == self.scrollDirection() || args.forceFixed ) );//toggles the css class 'fixed-header-on' from the czrapp.$_header element

              var _do = function() {
                    var translateYUp = $menu_wrapper[0].getBoundingClientRect().height,
                        translateYDown = 0,
                        _translate;

                    if ( args.fast ) {
                          $menu_wrapper.addClass( 'fast' );
                    }
                    // Handled via CSS
                    // //Handle the specific case of user logged in ( wpadmin bar length not false ) and previewing website with a mobile device < 600 px
                    // //=> @media screen and (max-width: 600px)
                    // // admin-bar.css?ver=4.7.3:1097
                    // // #wpadminbar {
                    // //     position: absolute;
                    // // }
                    // if ( _.isFunction( window.matchMedia ) && matchMedia( 'screen and (max-width: 600px)' ).matches && 1 == czrapp.$_wpadminbar.length ) {
                    //       //translateYUp = translateYUp + czrapp.$_wpadminbar.outerHeight();
                    //       translateYDown = translateYDown - $menu_wrapper.outerHeight();
                    // }

                    _translate = 'up' == args.direction ? 'translate(0px, -' + translateYUp + 'px)' : 'translate(0px, -' + translateYDown + 'px)';
                    self.stickyHeaderAnimating( true );
                    self.stickyHeaderAnimationDirection = args.direction;
                    $menu_wrapper.toggleClass( 'sticky-visible', 'down' == args.direction );

                    $menu_wrapper.css({
                          //transform: 'up' == args.direction ? 'translate3d(0px, -' + _height + 'px, 0px)' : 'translate3d(0px, 0px, 0px)'
                          '-webkit-transform': _translate,   /* Safari and Chrome */
                          '-moz-transform': _translate,       /* Firefox */
                          '-ms-transform': _translate,        /* IE 9 */
                          '-o-transform': _translate,         /* Opera */
                          transform: _translate
                    });

                    //this delay is tied to hardcoded css animation delay
                    _.delay( function() {
                          //Say it ain't so
                          self.stickyHeaderAnimating( false );
                          if ( args.fast ) {
                                $menu_wrapper.removeClass('fast');
                          }
                          dfd.resolve();
                    }, args.fast ? 100 : 350 );
                    return dfd;
              };//_do

              _.delay( function() {
                    //TO IMPLEMENT
                    //var sticky_menu_id = _.isString( $menu_wrapper.attr('data-menu-id') ) ? $menu_wrapper.attr('data-menu-id') : '';
                    // if ( czrapp.userXP.mobileMenu && czrapp.userXP.mobileMenu.has( sticky_menu_id ) ) {
                    //       czrapp.userXP.mobileMenu( sticky_menu_id )( 'collapsed' ).done( function() {
                    //             _do();
                    //       });
                    // }
                    //Is the mobile menu expanded ?
                    if ( czrapp.userXP.isResponsive() && 1 == $('.mobile-navbar__wrapper').length ) {
                          //var $mobile_menu = $('.mobile-navbar__wrapper');
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




        /*-----------------------------------------------------
        * HELPERS
        ------------------------------------------------------*/
        _isMobileMenuExpanded : function() {
              var $mobile_menu = $('.mobile-navbar__wrapper');
              if ( 1 != $('.mobile-navbar__wrapper').length )
                return false;

              return 1 == $mobile_menu.find('.ham-toggler-menu').length && "true" == $mobile_menu.find('.ham-toggler-menu').attr('aria-expanded');
        },


        //MOBILE MENU STATE LISTENER
        _toggleMobileMenu : function() {
            return $.Deferred( function() {
                  var $mobile_menu = $('.mobile-navbar__wrapper'),
                      _dfd_ = this;
                  if ( 1 == $mobile_menu.length ) {
                        //close the mobile menu
                        $mobile_menu.find('.ham-toggler-menu').trigger('click');
                        _.delay( function() {
                              _dfd_.resolve();
                        }, 350 );
                  } else {
                        _dfd_.resolve();
                  }
            }).promise();
        },


        //@return void()
        //Is fired on first load and on resize
        //set the currentStickySelector observable Value()
        // this.stickyCandidatesMap = {
        //       mobile : {
        //             mediaRule : 'only screen and (max-width: 719px)',
        //             selector : 'mobile-sticky'
        //       },
        //       desktop : {
        //             mediaRule : 'only screen and (min-width: 720px)',
        //             selector : 'desktop-sticky'
        //       }
        // };
        _setStickySelector : function() {
              var self = this,
                  _selector = false;

              // self.currentStickySelector = self.currentStickySelector || new czrapp.Value('');
              _.each( self.stickyCandidatesMap, function( _params, _device ) {
                    if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches && 'no_stick' != self.userStickyOpt() ) {
                          _selector = '.' + _params.selector;
                    }
              });
              self.currentStickySelector( _selector );
        },

        //@return string : no_stick, stick_up, stick_always
        //falls back on no_stick
        _setUserStickyOpt : function( device ) {
              var self = this;
              if ( _.isUndefined( device ) ) {
                    // self.currentStickySelector = self.currentStickySelector || new czrapp.Value('');
                    _.each( self.stickyCandidatesMap, function( _params, _device ) {
                          if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches ) {
                                device = _device;
                          }
                    });
              }
              device = device || 'desktop';

              return ( czrapp.localized.menuStickyUserSettings && czrapp.localized.menuStickyUserSettings[ device ] ) ? czrapp.localized.menuStickyUserSettings[ device ] : 'no_stick';
        },


        //@return a number
        //used to set the topStickPoint Value
        //the question is, if the current sticky candidate is not the topbar AND that there is a topbar, let's return this topbar's height
        _getTopStickPoint : function() {
              // //Do we have a topbar ?
              // if ( 1 !== czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).length && 1 !== czrapp.$_header.find( '[data-czr-template="header/topbar"]' ).length )
              //   return 0;
              // //if there's a topbar, is this topbar the current sticky candidate ?
              // if ( czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).hasClass( 'desktop-sticky') )
              //   return 0;
              // return czrapp.$_header.find( '[data-czr-model_id="topbar"]' ).height();

              if ( this.$_navbars_wrapper.length < 1 )
                return 0;

              //Do we have a topbar
              //todo: refer to a common jQuery selector (class or id)
              var self = this;

              //if there's a topbar, is this topbar the current sticky candidate ?
              if ( 1 == self.$_topbar.length && ! self.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    //$_topbar[0].getBoundingClientRect().height => returns 0 in mobiles as the topbar is  hidden in mobiles
                    return self.$_navbars_wrapper.offset().top + self.$_topbar[0].getBoundingClientRect().height;
                  //when the topbar is the sticky candidate we should also add a padding top to the $_navbars_wrapper
                  //top "push down" the primay navbar
              }

              return self.$_navbars_wrapper.offset().top;

        },

        //This is specific to Hueman
        _adjustDesktopTopNavPaddingTop : function() {
              // var self = this;
              // if ( ! self._isMobile() && self.hasStickyCandidate() ) {
              //       $('.full-width.topbar-enabled #header').css( 'padding-top', czrapp.$_header.find( self.currentStickySelector() ).outerHeight() );
              // } else {
              //       $('#header').css( 'padding-top', '' );
              // }
        },

        //RESET MOBILE HEADER TOP POSITION
        //@return void()
        //Make sure the header is visible when close to the top
        //Fired on each 'scrolling-finished' <=> user has not scrolled during 250 ms
        //+ 'up' == self.scrollDirection()
        _mayBeresetTopPosition : function() {

              var  self = this, $menu_wrapper = self.stickyMenuWrapper;
              //Bail if we are scrolling up
              if ( 'up' != self.scrollDirection() )
                return;
              //Bail if no menu wrapper
              //Or if we are already after the threshold
              //Or if we are scrolling down
              if ( ! $menu_wrapper.length )
                return;

              if ( self.scrollPosition() >= self.stickyHeaderThreshold )
                return;


              if ( ! self._isMobile() ) {
                  self._adjustDesktopTopNavPaddingTop();
              }

              //Always add this class => make sure the transition is smooth
              //self.isFixedPositionned( true );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
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

        //@return void()
        //@param push = boolean
        _pushPrimaryNavBarDown : function( push ) {
              push = push || this.isFixedPositionned();
              if ( 1 == this.$_primary_navbar.length && 1 == this.$_topbar.length && this.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    this.$_primary_navbar.css( { 'padding-top' : push ? this.$_topbar[0].getBoundingClientRect().height + 'px' : '' } );
              }
        },

        _refreshOrResizeReact : function() {
              var  self = this;
              //always reset the userStickyOpt ( czrapp.Value() ) when resizing
              //=> desktop and mobile sticky user option can be different
              self.userStickyOpt( self._setUserStickyOpt() );

              //reset the current sticky selector
              self._setStickySelector();

              //reset the topStickPoint Value
              self.topStickPoint( self._getTopStickPoint() );

              //maybe push the primary nav bar if the topbar is the current sticky candidate
              self._pushPrimaryNavBarDown();

              //Always close the mobile menu expanded when resizing
              if ( self._isMobileMenuExpanded() ) {
                    self._toggleMobileMenu();
              }

              if ( self.hasStickyCandidate() ) {
                    self.stickyMenuDown( self.scrollPosition() < self.stickyHeaderThreshold ,  { fast : true } ).done( function() {
                          czrapp.$_header.css( 'height' , '' );
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          if ( self.hasStickyCandidate() ) {
                                czrapp.$_header.css( 'height' , czrapp.$_header[0].getBoundingClientRect().height );
                                //make sure we don't set the position to fixed if not scrolled enough : self.scrollPosition() must be > self.topStickPoint()
                                self.isFixedPositionned( self.scrollPosition() > self.topStickPoint() );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
                          }
                    });
              } else {
                    self.stickyMenuDown( false ).done( function() {
                          $('#header').css( 'padding-top', '' );
                    });
              }

              //Adjust padding top if desktop sticky
              if ( ! self._isMobile() ) {
                    self._adjustDesktopTopNavPaddingTop();
              } else {
                    $('.full-width.topbar-enabled #header').css( 'padding-top', '' );
                    //Make sure the transform property is reset when swithing from desktop to mobile on resize
                    self._mayBeresetTopPosition();
              }
        }

  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
/************************************************
* USER EXPERIENCE SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {
        //the front notifications are populated in a localized array
        //'frontNotifications' => array(
        //       'welcome' => array(
        //           'enabled' => $is_welcome_note_on,
        //           'content' => $welcome_note_content,
        //           'dismissAction' => 'dismiss_welcome_note_front'
        //       ),
        //       'styleSwitcher' => array(
        //           'enabled' => $is_style_switch_note_on,
        //           'content' => $style_switcher_note_content,
        //           'dismissAction' => 'dismiss_style_switcher_note_front',
        //           'ajaxUrl' => admin_url( 'admin-ajax.php' )
        //       )
        // ),
        // let's loop on it and print the first eligible candidate
        mayBePrintFrontNote : function() {
              if ( czrapp.localized && _.isUndefined( czrapp.localized.frontNotifications ) )
                return;
              if ( _.isEmpty( czrapp.localized.frontNotifications ) || ! _.isObject( czrapp.localized.frontNotifications ) )
                return;

              var self = this,
                  _hasCandidate = false;
              czrapp.frontNotificationVisible = new czrapp.Value( false );

              //Set the current front notification
              _.each( czrapp.localized.frontNotifications, function( _notification, _id ) {
                    //do we have a candidate already ?
                    if ( ! _.isUndefined( czrapp.frontNotification ) )
                      return;

                    if ( ! _.isObject( _notification ) )
                      return;

                    //normalizes
                    _notification = _.extend( {
                          enabled : false,
                          content : '',
                          dismissAction : '',
                          ajaxUrl : czrapp.localized.ajaxUrl
                    }, _notification );

                    //set the candidate
                    if ( _notification.enabled ) {
                          czrapp.frontNotification = new czrapp.Value( _notification );
                    }

              });

              //Listen to changes
              czrapp.frontNotificationVisible.bind( function( visible ) {
                      return self._toggleNotification( visible );//returns a promise()
              }, { deferred : true } );

              czrapp.frontNotificationVisible( true );
        },//mayBePrintFrontNote()


        _toggleNotification : function( visible ) {
              var self = this,
                  dfd = $.Deferred();

              var _hideAndDestroy = function() {
                    return $.Deferred( function() {
                          var _dfd_ = this,
                              $notifWrap = $('#bottom-front-notification', '#footer');
                          if ( 1 == $notifWrap.length ) {
                                $notifWrap.css( { bottom : '-100%' } );
                                //remove and reset
                                _.delay( function() {
                                      $notifWrap.remove();
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
                    //Abort here if we don't have any notification candidate
                    if ( _.isUndefined( czrapp.frontNotification ) || ! _.isFunction( czrapp.frontNotification ) || ! _.isObject( czrapp.frontNotification() ) )
                        return _dfd_.resolve().promise();

                    //Render
                    $.Deferred( function() {
                          var dfd = this,
                              _notifHtml = czrapp.frontNotification().content,
                              _wrapHtml = [
                                    '<div id="bottom-front-notification">',
                                      '<div class="note-content">',
                                        '<span class="fa fa-times close-note" title="' + czrapp.localized.i18n['Permanently dismiss'] + '"></span>',
                                      '</div>',
                                    '</div>'
                              ].join('');

                          if ( 1 == $footer.length && ! _.isEmpty( _notifHtml ) ) {
                                $.when( $footer.append( _wrapHtml ) ).done( function() {
                                    $(this).find( '.note-content').prepend( _notifHtml );
                                });

                                _.delay( function() {
                                      $('#bottom-front-notification', '#footer').css( { bottom : 0 } );
                                      dfd.resolve();
                                }, 500 );
                          } else {
                                dfd.resolve();
                          }
                    }).done( function() {
                          //Listen to user actions
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

              //Always auto-collapse the infos block
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

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {

      //outline firefox fix, see https://github.com/presscustomizr/customizr/issues/538
      outline: function() {
            if ( 'function' == typeof( tcOutline ) )
                tcOutline();
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
                _focusable_class        = 'czr-focus',
                _focusable_field_class  = 'czr-focusable',
                _focus_class            = 'in-focus',
                _czr_form_class         = 'czr-form',
                _parent_selector        = '.'+ _czr_form_class + ' .'+_focusable_class,
                _inputs                 = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
                $_focusable_inputs      = $( _input_types.join() );

            if ( $_focusable_inputs.length <= 0 )
              return;


            //This is needed to add a class to the input parent (label parent) so that
            //we can limit absolute positioning + translations only to relevant ones ( defined in _input_types )
            //consider the exclude?!
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

            //on ready :   think about search forms in search pages
            $(_inputs).trigger( 'in-focus-load.czr-focus' );

            //search form clean on .icn-close click
            czrapp.$_body.on( 'click tap', '.icn-close', function() {
                  var $_search_field = $(this).closest('form').find('.czr-search-field');

                  if ( $_search_field.length ) {
                        //empty search field if needed and keep the focus
                        if ( $_search_field.val() ) {
                              $_search_field.val('').focus();
                        }
                        //otherwise (search field alredy empty) release th focus
                        else {
                              $_search_field.blur();
                        }
                  }

            });
      },

      //React on Esc key pressed
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

                        //search field clean and collapse (release the focus)
                        if ( $(evt.target).hasClass( ClassName.SEARCH_FIELD ) ) {
                              $( evt.target ).val('').blur();
                              return;
                        }

                        //else close the overlay if exists and opened
                        //might be the search full page or the full page menu
                        if ( $( Selector.OVERLAY ).length && czrapp.$_body.hasClass( ClassName.OLVERLAY_SHOWN ) ) {
                              $( Selector.OVERLAY ).find( Selector.OVERLAY_TOGGLER ).trigger( Event.OVERLAY_TOGGLER );
                              return;
                        }

                        //else close the sidenav if exists and opened
                        if ( $( Selector.SIDENAV ).length  && czrapp.$_body.hasClass( ClassName.SIDENAV_SHOWN ) ) {

                              $( Selector.SIDENAV ).find( Selector.SIDENAV_TOGGLER ).trigger( Event.SIDENAV_TOGGLER );
                              return;
                        }
                  }

            });

      },

      variousHeaderActions : function() {
            var _mobile_viewport                   = 992;

            /* header search button */
            czrapp.$_body.on( 'click tap', '.search-toggle_btn', function(evt) {
                  evt.preventDefault();
                  czrapp.$_body.toggleClass( 'full-search-opened czr-overlay-opened' );
            });

            //custom scrollbar for woocommerce list
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

            //go to opened on click element when mCustomScroll active
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.czr-open-on-click.mCustomScrollbar, .czr-open-on-click .mCustomScrollbar, .mCustomScrollbar .czr-open-on-click', function( evt ) {
                  var $_this                  = $( this ),
                      $_customScrollbar = $_this.hasClass('mCustomScrollbar') ? $_this : $_this.closest('.mCustomScrollbar');
                  if ( $_customScrollbar.length ) {
                       //http://manos.malihu.gr/jquery-custom-content-scroller/
                       $_customScrollbar.mCustomScrollbar( 'scrollTo', $(evt.target) );
                  }
            });
      },

      //SMOOTH SCROLL
      smoothScroll: function() {
            if ( CZRParams.SmoothScroll && CZRParams.SmoothScroll.Enabled ) {
                smoothScroll( CZRParams.SmoothScroll.Options );
            }
      },

      //ATTACHMENT FADE EFFECT
      attachmentsFadeEffect : function() {
            $( '.attachment-image-figure img' ).delay(500).addClass( 'opacity-forced' );
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
      //The job of this method is to align horizontally the various elements of the featured pages for a given row of featured pages
      //=>
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
            _offsets[i] = new Array( _n_featured_pages );


          //fire
          maybeSetElementsPosition();
          //bind
          czrapp.$_window.on( 'resize', _.debounce( maybeSetElementsPosition, 20 ) );

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

         //All elements are aligned using padding top.
         //This means that the aligned elements are dependant on each others and we can't do it on only one pass
        function setElementsPosition() {
              /*
              * this array will store the
              */
              var _fp_offsets = [];
              //Loop on each single feature page block
              for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {
                  //loop on each featured page blocks
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

   czrapp.methods.UserXP = czrapp.methods.UserXP || {};
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
        DATA_HOVER_PARENT        : '.czr-open-on-hover .menu-item-has-children, .nav__woocart',
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

        //a little delay to balance the one added in removing the open class
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

        }, 30);

        _debounced_addOpenClass();
      }

      function _removeOpenClass () {

        var $_el = $(this);

        //a little delay before closing to avoid closing a parent before accessing the child
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

        _maybe_move( $_dropdown, $_el );

        //unstage if staged
        $_dropdown.css( 'zIndex', '').css('display', '');

      }


      function _maybe_move( $_dropdown, $_el ) {
        var $_a = $_el.find( self.Selector.DATA_TOGGLE ).first(),
            $_caret = $_el.find('.caret__dropdown-toggler').first(),
            _openLeft = function() {
                $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );
                if ( 1 == $_caret.length ) {
                    $_caret.removeClass( 'open-right' ).addClass( 'open-left' );
                    if ( 1 == $_a.length )
                      $_a.addClass('flex-row-reverse');
                }
            },
            _openRight = function() {
                $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );
                if ( 1 == $_caret.length ) {
                    $_caret.removeClass( 'open-left' ).addClass( 'open-right' );
                    if ( 1 == $_a.length )
                      $_a.removeClass('flex-row-reverse');
                }
            };

        //snake inheritance
        if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( 'open-left' ) ) {
            _openLeft();
        } else {
          _openRight();
        }

        //let's compute on which side open the dropdown
        if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {
          _openLeft();
        } else if ( $_dropdown.offset().left < 0 ) {
          _openRight();
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
      NAVBAR_NAV: '.regular-nav',
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

( function ( czrapp, $, _ ) {
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



      //Instantiates
      //@param newMap {}
      //@param previousMap {}
      //@param isInitial bool
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



      //This Value is set with theme specific map
      czrapp.appMap = new czrapp.Value( {} );
      czrapp.appMap.bind( _instantianteAndFireOnDomReady );//<=THE MAP IS LISTENED TO HERE

      //instantiates the default map
      //@param : new map, previous map, isInitial bool
      //_instantianteAndFireOnDomReady( appMap, null, true );

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

})(jQuery, czrapp, _ );var czrapp = czrapp || {};
//@global CZRParams
/************************************************
* LET'S DANCE
*************************************************/
( function ( czrapp, $, _ ) {
      //adds the server params to the app now
      czrapp.localized = CZRParams || {};

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
                            'initOnCzrReady',//<= fires all carousels : main, galleries, related posts + center images
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

                            'attachmentsFadeEffect',

                            'onEscapeKeyPressed',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',

                            'mayBePrintFrontNote',
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

      //set the observable value
      //listened to by _instantianteAndFireOnDomReady = function( newMap, previousMap, isInitial )
      czrapp.appMap( appMap , true );//true for isInitial map

})( czrapp, jQuery, _ );