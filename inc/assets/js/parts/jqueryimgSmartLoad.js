/* ===================================================
 * jqueryimgSmartLoad.js v1.0.0
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Replace all img src placeholder in the $element by the real src on scroll window event
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
    //defaults
    var pluginName = 'imgSmartLoad',
        defaults = {
          load_all_images_on_first_scroll : false,
          attribute : 'data-src',
          threshold : 200
        };

    function Plugin( element, options ) {
      this.element = element;
      this.options = $.extend( {}, defaults, options) ;
      this._defaults = defaults;
      this._name = pluginName;
      this.init();
    }

    //can access this.element and this.option
    Plugin.prototype.init = function () {
      var self        = this,
          $_imgs   = $( 'img[' + this.options.attribute + ']' , this.element );

      this.increment  = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
      this.timer      = 0;

      $_imgs.bind( 'scrollin', {}, function() { self._load_img(this); });
      $(window).scroll( function( _evt ) { self._better_scroll_event_handler( $_imgs, _evt ); });
      $(window).resize( function( _evt ) { self._event_handler( $_imgs, _evt ); });
      this._event_handler( $_imgs );
    };


    Plugin.prototype._better_scroll_event_handler = function( _evt ) {
      var self = this;
      //use a timer
      if ( 0 !== this.timer ) {
          this.increment++;
          window.clearTimeout( this.timer );
      }

      this.timer = window.setTimeout(function() {
        self._event_handler( _evt );
      }, self.increment > 5 ? 50 : 0 );
    };


    Plugin.prototype._event_handler = function(  $_imgs , _evt ) {
      var self = this;

      var _inViewPort = $_imgs.filter(function() {
          var $e = $(this),
              wt = $(window).scrollTop(),
              wb = wt + $(window).height(),
              et = $e.offset().top,
              eb = et + $e.height(),
              th = self.options.threshold;
          return eb >= wt - th && et <= wb + th;
      });

      if ( _evt && 'scroll' == _evt.type && _load_all_images_on_first_scroll )
        $_imgs = $_imgs.trigger( 'scrollin' );
      else
        $_imgs = $_imgs.not( _inViewPort.trigger('scrollin') );
    };

    Plugin.prototype._load_img = function( _img ) {
      var $_img = $(_img),
          _src  = $_img.attr( this.options.attribute );

      $_img.unbind('scrollin')
      .hide()
      .removeAttr( this.options.attribute )
      .attr('src' , _src )
      .fadeIn()
      .trigger('smartload');
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