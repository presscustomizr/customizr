var czrapp = czrapp || {};
/* Object.create monkey patch ie8 http://stackoverflow.com/a/18020326
 * Shoudl be probablly moved in a different file. 
 * I think we can make an "old-browser-comp" file where to move this, arrayPrototype and further patches of the same kind
 *
 * */
if ( !Object.create ) {
  Object.create = function(proto, props) {
    if (typeof props !== "undefined") {
      throw "The multiple-argument version of Object.create is not provided by this browser and cannot be shimmed.";
    }
    function ctor() { }
    
    ctor.prototype = proto;
    return new ctor();
  };
}

(function($, czrapp) {
  // if ( ! TCParams || _.isEmpty(TCParams) )
  //   return;
  $.extend( czrapp, {
    instances        : {},//will store all subclasses instances
    methods          : {},//will store all subclasses methods

    //parent class constructor
    Base : function() {},

    _inherits : function( classname ) {
      //add the class to the czrapp and sets the parent this to it
      czrapp[classname] = function() {
        czrapp.Base.call(this);
      };

      //set the classical prototype chaining with inheritance
      czrapp[classname].prototype = Object.create( czrapp.Base.prototype );
      czrapp[classname].prototype.constructor = czrapp[classname];
      return czrapp;
    },


    _instanciates : function( classname) {
      czrapp.instances[classname] = czrapp.instances[classname] || new czrapp[classname]();
      return czrapp;
    },


    /**
     * [_init description]
     * @param  {[type]} classname string
     * @param  {[type]} methods   array of methods
     * @return {[type]} czrapp object
     */
    _init : function(classname, methods) {
      var _instance = czrapp.instances[classname] || false;
      if ( ! _instance )
        return;

      //always fire the init method if exists
      if ( _instance.init )
        _instance.init();

      //fire the array of methods on load
      _instance.emit(methods);

      //return czrapp for chaining
      return czrapp;
    },

    //extend a classname prototype with a set of methods
    _addMethods : function(classname) {
      $.extend( czrapp[classname].prototype , czrapp._getMethods(classname) );
      return czrapp;
    },

    _getMethods : function(classname) {
      return czrapp.methods[classname] || {};
    },


    /**
     * Cache properties on Dom Ready
     * @return {[type]} [description]
     */
    cacheProp : function() {
      $.extend( czrapp, {
        //cache various jQuery el in czrapp obj
        $_window         : $(window),
        $_body           : $('body'),
        $_tcHeader       : $('.tc-header'),
        $_wpadminbar     : $('#wpadminbar'),

        //various properties definition
        localized        : TCParams || {},
        reordered_blocks : false,//store the states of the sidebar layout
      });

      return czrapp;
    },


    /**
     * [load description]
     * @param  {[type]} args [description]
     * @return {[type]}      [description]
     */
    loadCzr : function( args ) {
      _.each( args, function( methods, key ) {
        czrapp._inherits(key)._instanciates(key)._addMethods(key)._init(key, methods);
      });//_.each()

      czrapp.$_body.trigger('czrapp-ready');
    }
  });//extend
})(jQuery, czrapp);



/*************************
* ADD BASE CLASS METHODS
*************************/
(function($, czrapp) {
  var _methods = {
    emit : function( cbs, args ) {
      //console.log('in emit :  cbs, args',  cbs, this);
      cbs = _.isArray(cbs) ? cbs : [cbs];
      var self = this;
      _.map( cbs, function(cb) {
        if ( 'function' == typeof(self[cb]) ) {
          self[cb].apply(self, 'undefined' == typeof( args ) ? Array() : args );
          czrapp.$_body.trigger( cb, _.object( _.keys(args), args ) );
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

    isCustomizing    : function() {
      return czrapp.$_body.hasClass('is-customizing');
    }

  };//_methods{}

  $.extend( czrapp.Base.prototype, _methods );//$.extend

})(jQuery, czrapp);
