/* ===================================================
 * jqueryCenterImages.js v1.0.0
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Center images in a specified container
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  //defaults
  var pluginName = 'centerImages',
      defaults = {
        onresize : true,
        oncustom : '',
      };

  function Plugin( element, options ) {
    this.container = element;
    this.options = $.extend( {}, defaults, options) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }

  //can access this.element and this.option
  //@return void
  Plugin.prototype.init = function () {
    var $_imgs = ! this.options.imgclass ? $( 'img' , this.container ) : $( ['.', this.options.imgclass].join('') , this.container );
    if ( ! $_imgs.length  )
      return;

    this._parse_imgs($_imgs);
    var self = this;
    if ( this.options.onresize )
      $(window).resize(function(){ self._parse_imgs($_imgs); });
  };


  //@return void
  Plugin.prototype._parse_imgs = function( $_imgs ) {
    var self = this;
    $_imgs.each(function ( ind, img ) { self._pre_img_cent( $(img) ); });
  };


  //@return void
  Plugin.prototype._pre_img_cent = function( $_img ) {
    var _state = this._get_current_state($_img);
    this._maybe_center_img( $_img, _state );
  };


  //@return object with initial conditions
  Plugin.prototype._get_current_state = function( $_img ) {
    var c_x     = $_img.closest(this.container).outerWidth(),
        c_y     = $(this.container).outerHeight(),
        i_x     = $_img.outerWidth(),
        i_y     = $_img.outerHeight(),
        up_i_x  = Math.round( i_x / i_y * c_y ),
        up_i_y  = Math.round( i_y / i_x * c_x ),
        current = ( c_y / c_x ) >= ( i_y / i_x ) ? 'h' : 'v',
        prop    = {
          h : {
            dim : { name : 'height', val : c_y },
            dir : { name : 'left', val : ( c_x - up_i_x ) / 2 },
            class : 'h-centered'
          },
          v : {
            dim : { name : 'width', val : c_x },
            dir : { name : 'top', val : ( c_y - up_i_y ) / 2 },
            class : 'v-centered'
          }
        };
    return { current : current , prop : prop };
  };


  //@return void
  Plugin.prototype._maybe_center_img = function( $_img, _state ) {
    var _case  = _state.current,
        _p     = _state.prop[_case],
        _not_p = _state.prop[ 'h' == _case ? 'v' : 'h'];

    $_img.css( _p.dim.name , _p.dim.val ).css( _not_p.dim.name , 'auto' )
        .addClass( _p.class ).removeClass( _not_p.class )
        .css( _p.dir.name, _p.dir.val ).css( _not_p.dir.name, 0 );
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