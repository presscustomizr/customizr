//Target the first letter of the first element found in the wrapper
;(function ( $, window, document, undefined ) {
    //defaults
    var pluginName = 'addDropCap',
        defaults = {
            wrapper : ".entry-content"
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
      this._may_be_add_dc();
    };

    Plugin.prototype._may_be_add_dc = function() {
      var $_target          = $( this.options.wrapper ).find( this.element ).first(),
          _first_p_text     = this._stripHtmlTags ( $_target.text() ),
          _clean_p_text     = this._removeSpecChars( _first_p_text ),
          _to_transform     = _clean_p_text.charAt(0),
          _truncated_text   = _first_p_text.substr(1);

      $_to_prepend = $( '<span>' , { class : 'tc-dropcap' , html : _to_transform } );

      $_target.text( _truncated_text );

      $_target.prepend( $_to_prepend );
    };

    Plugin.prototype._removeSpecChars = function( expr , replaceBy ) {
      replaceBy = replaceBy || '';
      return expr.replace(/[^\w]/g, replaceBy );
      //return ( expr && 'string' == typeof(expr) ) ? expr.replace(/[\+|:|,]/g, '-').replace(/[\.\s]/g, "-").replace(/\s+/g, '-').replace(/[\.|\!]/g,'').replace('&nbsp;' ,'-').toLowerCase() : false;
    };

    Plugin.prototype._stripHtmlTags = function( expr ) {
      return ( expr && 'string' == typeof(expr) ) ? expr.replace(/(<([^>]+)>)/ig,"") : false;
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