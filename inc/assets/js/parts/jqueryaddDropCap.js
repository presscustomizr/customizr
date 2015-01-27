//Target the first letter of the first element found in the wrapper
;(function ( $, window, document, undefined ) {
    //defaults
    var pluginName = 'addDropCap',
        defaults = {
            wrapper : ".entry-content",
            minwords : 50,
            skipSelectors : { //defines the selector to skip when parsing the wrapper
              tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE'],
              classes : [],
              ids : []
            },
            skipChildTags : ['iframe']//skip those tags if they are direct children of an element
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
      var $_target = this._get_dropcap_el();
      //if there's text and enough words, then apply a drop cap
      if ( $_target && this.options.minwords <= this._countWords( $_target.text() ) )
        this._may_be_add_dc( $_target );
    };


    //@return : $ element or false
    //recursive function. parse this.wrapper to find the first eligible element with text
    Plugin.prototype._get_dropcap_el = function( _requested_el ) {
      var $_first_el      = _requested_el || $( this.options.wrapper ).find( this.element ).first(),
          _first_el_text  = this._get_real_text( $_first_el.text() );

      if ( ! this._is_authorized( $_first_el ) && $_first_el.next().length )
        return this._get_dropcap_el( $_first_el.next() );
      else if ( this._is_authorized( $_first_el ) && _first_el_text )
        return $_first_el;
      else if ( $_first_el.next().length )
        return this._get_dropcap_el( $_first_el.next() );
      //get me out of here
      return;
    };

    //@return : string
    //clean spaces and special characters
    Plugin.prototype._get_real_text = function( _text ) {
      _text.replace(/&nbsp;/g, '').replace(/ /g, '');
      return this._removeSpecChars( _text );
    };

    //@return :boolean
    //check if the selector is in the 'to skip' list
    Plugin.prototype._is_authorized = function( $_el ) {
      //check if underscore is loaded first
      if ( 'function' != typeof(_) )
        return true;

      if ( ! $_el[0] || ! $_el[0].tagName )
        return;

      //is first child tag allowed ?
      if ( _.isArray(this.options.skipChildTags) && $_el.children().first().length && -1 != _.indexOf( _.map( this.options.skipChildTags , function(_tag) { return _tag.toUpperCase(); } ), $_el.children().first()[0].tagName ) )
        return;
      //is tag allowed ?
      if ( _.isArray(this.options.skipSelectors.tags) && -1 != _.indexOf( _.map( this.options.skipSelectors.tags , function(_tag) { return _tag.toUpperCase(); } ), $_el[0].tagName ) )
        return;
      //is class allowed ?
      if ( _.isArray(this.options.skipSelectors.classes) && $_el.attr('class') && 0 !== _.intersection( $_el.attr('class').split(' '), this.options.skipSelectors.classes ).length )
        return;
      //is id allowed ?
      if ( _.isArray(this.options.skipSelectors.classes) && $_el.attr('id') && -1 != $.inArray( $_el.attr('id').split(' ') , this.options.skipSelectors.ids ) )
        return;

      return true;
    };

    //@return : void
    //at this stage, the target has text, no need to check it
    Plugin.prototype._may_be_add_dc = function( $_target ) {
      var _first_el_text    = $_target.text(),
          _first_word       = '',
          _split_text       = $_target.text().replace(/ /g , '&nbsp;').split('&nbsp;');
      if ( ! _.isArray(_split_text) )
        return;

      //get the first word => it can't be a space due to previous treatment
      if ( _split_text.length )
        _first_word = _split_text[0];

      //cClean it
      _first_word = this._removeSpecChars( _first_word );

      if ( ! _first_word.charAt(0) )
        return;

      var _first_letter     = _first_word.charAt(0),
          _rest_of_word     = _first_word.substr(1),
          _drop_capped      = '',
          _html             = '';

      _first_letter = ['<span class="tc-dropcap">' , _first_letter, '</span>'].join('');
      _drop_capped = [ _first_letter , _rest_of_word ].join( '' );

      //replace the first occurence only
      _html = $_target.html().replace( _first_word , _drop_capped );

      //write
      $_target.html(_html);
    };


    /********
    * HELPERS
    *********/
    //@return : number
    Plugin.prototype._countWords = function( _expr ) {
      if ( 'string' != typeof( _expr ) )
        return 0;
      _expr = _expr.replace('&nbsp;' , ' ');
      return (_expr.split(' ')).length;
    };

    //@return : string
    Plugin.prototype._removeSpecChars = function( _expr , _replaceBy ) {
      _replaceBy = _replaceBy || '';
      return 'string' == typeof(_expr) ? _expr.replace(/[^\w]/g, _replaceBy ) : '';
    };

    //@return : string or false
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