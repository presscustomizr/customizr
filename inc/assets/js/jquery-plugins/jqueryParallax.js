/* ===================================================
 * jqueryParallax.js v1.0.0
 * ===================================================
 * (c) 2016 Nicolas Guillaume - Rocco Aliberti, Nice, France
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  /*
  * In order to handle a smooth scroll
  * ( inspired by jquery.waypoints and smoothScroll.js )
  * Maybe use this -> https://gist.github.com/paulirish/1579671
  */
  var czrParallaxRequestAnimationFrame = function(callback) {
    var requestFn = ( czrapp && czrapp.requestAnimationFrame) ||
      window.requestAnimationFrame ||
      window.mozRequestAnimationFrame ||
      window.webkitRequestAnimationFrame ||
      function( callback ) { window.setTimeout(callback, 1000 / 60); };

    requestFn.call(window, callback);
  };

  //defaults
  var pluginName = 'czrParallax',
      defaults = {
        parallaxRatio : 0.5,
        parallaxDirection : 1,
        parallaxOverflowHidden : true,
        oncustom : [],//list of event here
        backgroundClass : 'image'
      };

  function Plugin( element, options ) {
    this.element = $(element);
    this.options = $.extend( {}, defaults, options, this.parseElementDataOptions() ) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }

  Plugin.prototype.parseElementDataOptions = function () {
    return this.element.data();
  };

  //can access this.element and this.option
  //@return void
  Plugin.prototype.init = function () {
    //cache some element
    this.$_document   = $(document);
    this.$_window     = czrapp ? czrapp.$_window : $(window);
    this.doingAnimation = false;

    this.initWaypoints();
    this.stageParallaxElements();
    this._bind_evt();
  };

  //@return void
  //map custom events if any
  Plugin.prototype._bind_evt = function() {
    var self = this,
        _customEvt = $.isArray(this.options.oncustom) ? this.options.oncustom : this.options.oncustom.split(' ');

    _.bindAll( this, 'maybeParallaxMe', 'parallaxMe' );
    /* TODO: custom events? */
  };

  Plugin.prototype.stageParallaxElements = function() {

    this.element.css( 'position', this.element.hasClass( this.options.backgroundClass ) ? 'absolute' : 'relative' );
    if ( this.options.parallaxOverflowHidden ){
      var $_wrapper = this.element.closest( '.parallax-wrapper' );
      if ( $_wrapper.length )
        $_wrapper.css( 'overflow', 'hidden' );
    }
  };

  Plugin.prototype.initWaypoints = function() {
    var self = this;

      this.way_start = new Waypoint({
        element: self.element,
        handler: function() {
          self.maybeParallaxMe();
          if ( ! self.element.hasClass('parallaxing') ){
            self.$_window.on('scroll', self.maybeParallaxMe );
            self.element.addClass('parallaxing');
          }else{
            self.element.removeClass('parallaxing');
            self.$_window.off('scroll', self.maybeParallaxMe );
            self.doingAnimation = false;
            self.element.css('top', 0 );
          }
        }
      });

      this.way_stop = new Waypoint({
        element: self.element,
        handler: function() {
          self.maybeParallaxMe();
          if ( ! self.element.hasClass('parallaxing') ) {
            self.$_window.on('scroll', self.maybeParallaxMe );
            self.element.addClass('parallaxing');
          }else {
            self.element.removeClass('parallaxing');
            self.$_window.off('scroll', self.maybeParallaxMe );
            self.doingAnimation = false;
          }
        },
        offset: function(){
          //offset = this.context.innerHeight() - this.adapter.outerHeight();
          //return - (  offset > 20 /* possible wrong h scrollbar */ ? offset : this.context.innerHeight() );
          return - this.adapter.outerHeight();
        }
      });
  };

  /*
  * In order to handle a smooth scroll
  */
  Plugin.prototype.maybeParallaxMe = function() {
      var self = this;

      if ( !this.doingAnimation ) {
        this.doingAnimation = true;
        window.requestAnimationFrame(function() {
          self.parallaxMe();
          self.doingAnimation = false;
        });
      }
  };

  Plugin.prototype.parallaxMe = function() {
      //parallax only the current slide if in slider context?
      /*
      if ( ! ( this.element.hasClass( 'is-selected' ) || this.element.parent( '.is-selected' ).length ) )
        return;
      */

      var ratio = this.options.parallaxRatio,
          parallaxDirection = this.options.parallaxDirection,

          value = ratio * parallaxDirection * ( this.$_document.scrollTop() - this.way_start.triggerPoint );

       this.element.css('top', parallaxDirection * value < 0 ? 0 : value );
  };


  // prevents against multiple instantiations
  $.fn[pluginName] = function ( options ) {
      return this.each(function () {
          if (!$.data(this, 'plugin_' + pluginName)) {
              $.data(this, 'plugin_' + pluginName,
              new Plugin( this, options ));
          }
      });
  };

})( jQuery, window, document );