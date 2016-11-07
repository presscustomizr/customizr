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
        enableCentering : true,
        onresize : true,
        oncustom : [],//list of event here
        imgSel : 'img',
        defaultCSSVal : { width : 'auto' , height : 'auto' },
        leftAdjust : 0,
        zeroLeftAdjust : 0,
        topAdjust : 0,
        zeroTopAdjust : -2,//<= top ajustement for h-centered
        enableGoldenRatio : false,
        goldenRatioLimitHeightTo : 350,
        goldenRatioVal : 1.618,
        skipGoldenRatioClasses : ['no-gold-ratio'],
        disableGRUnder : 767,//in pixels
        useImgAttr:false//uses the img height and width attributes if not visible (typically used for the customizr slider hidden images)
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
    var self = this;

    //applies golden ratio to all containers ( even if there are no images in container )
    this._maybe_apply_golden_r();

    //parses imgs ( if any ) in current container
    var $_imgs = $( this.options.imgSel , this.container );

    //if no images or centering is not active, only handle the golden ratio on resize event
    if ( ! $_imgs.length || ! this.options.enableCentering ) {
      //creates a golden ratio fn on resize
      $(window).bind( 'resize' , {} , function( evt ) { self._maybe_apply_golden_r( evt ); });
    } else {
      this._parse_imgs($_imgs);
    }
  };


  //@return void
  Plugin.prototype._maybe_apply_golden_r = function( evt ) {
    //check if options are valids
    if ( ! this.options.enableGoldenRatio || ! this.options.goldenRatioVal || 0 === this.options.goldenRatioVal )
      return;

    //make sure the container has not a forbidden class
    if ( ! this._is_selector_allowed() )
      return;
    //check if golden ratio can be applied under custom window width
    if ( ! this._is_window_width_allowed() ) {
      //reset inline style for the container
      $(this.container).attr('style' , '');
      return;
    }

    var new_height = Math.round( $(this.container).width() / this.options.goldenRatioVal );
    //check if the new height does not exceed the goldenRatioLimitHeightTo option
    new_height = new_height > this.options.goldenRatioLimitHeightTo ? this.options.goldenRatioLimitHeightTo : new_height;
    $(this.container).css( {'line-height' : new_height + 'px' , 'height' : new_height + 'px' } ).trigger('golden-ratio-applied');
  };


  /*
  * @params string : ids or classes
  * @return boolean
  */
  Plugin.prototype._is_window_width_allowed = function() {
    return $(window).width() > this.options.disableGRUnder - 15;
  };


  //@return void
  Plugin.prototype._parse_imgs = function( $_imgs ) {
    var self = this;

    $_imgs.each(function ( ind, img ) {
      self._pre_img_cent( $(img) );
      self._bind_evt ( $(img) );
    });
  };


  //@return void
  //map custom events if any
  Plugin.prototype._bind_evt = function( $_img ) {
    var self = this,
        _customEvt = $.isArray(this.options.oncustom) ? this.options.oncustom : this.options.oncustom.split(' ');

    //WINDOW RESIZE EVENT ACTIONS
    //GOLDEN RATIO (before image centering)
    $(window).bind( 'resize' , {} , function( evt ) { self._maybe_apply_golden_r( evt ); });

    //IMG CENTERING FN
    if ( this.options.onresize )
      $(window).resize(function() {
        self._pre_img_cent( $_img );
      });

    //CUSTOM EVENTS ACTIONS
    _customEvt.map( function( evt ) {
      $_img.bind( evt, {} , function( evt ) {
        self._pre_img_cent( $_img );
      } );
    } );
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
        i_x     = this._get_img_dim( $_img , 'x'),
        i_y     = this._get_img_dim( $_img , 'y'),
        up_i_x  = i_y * c_y !== 0 ? Math.round( i_x / i_y * c_y ) : c_x,
        up_i_y  = i_x * c_x !== 0 ? Math.round( i_y / i_x * c_x ) : c_y,
        current = 'h';
    //avoid dividing by zero if c_x or i_x === 0
    if ( 0 !== c_x * i_x )
      current = ( c_y / c_x ) >= ( i_y / i_x ) ? 'h' : 'v';

    var prop    = {
      h : {
        dim : { name : 'height', val : c_y },
        dir : { name : 'left', val : ( c_x - up_i_x ) / 2 + ( this.options.leftAdjust || 0 ) },
        _class : 'h-centered'
      },
      v : {
        dim : { name : 'width', val : c_x },
        dir : { name : 'top', val : ( c_y - up_i_y ) / 2 + ( this.options.topAdjust || 0 ) },
        _class : 'v-centered'
      }
    };

    return { current : current , prop : prop };
  };


  //@return img height or width
  //uses the img height and width if not visible and set in options
  Plugin.prototype._get_img_dim = function( $_img, _dim ) {
    if ( ! this.options.useImgAttr )
      return 'x' == _dim ? $_img.outerWidth() : $_img.outerHeight();

    if ( $_img.is(":visible") )
      return 'x' == _dim ? $_img.outerWidth() : $_img.outerHeight();
    else {
      if ( 'x' == _dim ){
        var _width = $_img.originalWidth();
        return typeof _width === undefined ? 0 : _width;
      }if ( 'y' == _dim ){
        var _height = $_img.originalHeight();
        return typeof _height === undefined ? 0 : _height;
      }
    }
  };


  //@return void
  Plugin.prototype._maybe_center_img = function( $_img, _state ) {
    var _case  = _state.current,
        _p     = _state.prop[_case],
        _not_p = _state.prop[ 'h' == _case ? 'v' : 'h'],
        _not_p_dir_val = 'h' == _case ? ( this.options.zeroTopAdjust || 0 ) : ( this.options.zeroLeftAdjust || 0 );

    $_img.css( _p.dim.name , _p.dim.val ).css( _not_p.dim.name , this.options.defaultCSSVal[_not_p.dim.name] || 'auto' )
        .addClass( _p._class ).removeClass( _not_p._class )
        .css( _p.dir.name, _p.dir.val ).css( _not_p.dir.name, _not_p_dir_val );
  };

  /********
  * HELPERS
  *********/
  /*
  * @params string : ids or classes
  * @return boolean
  */
  Plugin.prototype._is_selector_allowed = function() {
    //has requested sel ?
    if ( ! $(this.container).attr( 'class' ) )
      return true;

    //check if option is well formed
    if ( ! this.options.skipGoldenRatioClasses || ! $.isArray( this.options.skipGoldenRatioClasses )  )
      return true;

    var _elSels       = $(this.container).attr( 'class' ).split(' '),
        _selsToSkip   = this.options.skipGoldenRatioClasses,
        _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});

    //check if the filtered selectors array with the non authorized selectors is empty or not
    //if empty => all selectors are allowed
    //if not, at least one is not allowed
    return 0 === _filtered.length;
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
