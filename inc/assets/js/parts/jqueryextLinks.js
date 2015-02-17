//Target the first letter of the first element found in the wrapper
;(function ( $, window, document, undefined ) {
    //defaults
    var pluginName = 'extLinks',
        defaults = {
          addIcon : true,
          newTab: true,
          skipSelectors : { //defines the selector to skip when parsing the wrapper
            classes : [],
            ids : []
          },
          skipChildTags : ['IMG']//skip those tags if they are direct children of the current link element
        };

    function Plugin( element, options ) {
      this.$_el     = $(element);
      this.options  = $.extend( {}, defaults, options) ;
      this._href    = $.trim( this.$_el.attr( 'href' ) );

      console.log('OPTS IN PLUGIN : ' , this.options );

      this.init();
    }

    Plugin.prototype.init = function() {
      console.log( this._href, this._is_external( this._href ), this._is_eligible() );
      console.log( 'CHILD ?' , this.$_el.children() );
      if ( this.$_el.children().first().length ) {
          console.log( 'CHILDREN : ' , this.$_el.children().first()[0].tagName, this.$_el.children().first()[0]);
      }
      if ( ! this._is_eligible() )
        return;

      if ( this.options.addIcon )
        this.$_el.after('<span class="tc-external">');
      if ( this.options.newTab )
        this.$_el.attr('target' , '_blank');
    };


    /*
    * @return boolean
    */
    Plugin.prototype._is_eligible = function() {
      if ( ! this._is_external( this._href ) )
        return;

      //is first child tag allowed ?
      //'IMG' != this.$_el.children().first().prop("tagName")
      if ( ! this._is_first_child_tag_allowed ( this.$_el, this.options.skipChildTags) )
        return;
      // //is class allowed ?
      // if ( _.isArray(this.options.skipSelectors.classes) && this.$_el.attr('class') && 0 !== _.intersection( this.$_el.attr('class').split(' '), this.options.skipSelectors.classes ).length )
      //   return;
      // //is id allowed ?
      // if ( _.isArray(this.options.skipSelectors.classes) && this.$_el.attr('id') && -1 != $.inArray( this.$_el.attr('id').split(' ') , this.options.skipSelectors.ids ) )
      //   return;
    };


    /********
    * HELPERS
    *********/
    /*
    * @params $(element)
    * @params array of non authorized tags
    */
    Plugin.prototype._is_first_child_tag_allowed = function( $_el, _tags_to_skip ) {
      var self = this;
      // console.log( 'in _is_first_child_tag_allowed' , $.isArray( this.options.skipChildTags ) );
      // console.log( 'FILTER TEST' , this.options.skipChildTags.filter( function( _tag , _ind ){
      //   console.log( 'tag et ind' , _tag , _ind  );
      // } ) );


      //is first child tag allowed ?
      // if ( $.isArray( this.options.skipChildTags ) && this.$_el.children().first().length && -1 != _.indexOf( _.map( this.options.skipChildTags , function(_tag) { return _tag.toUpperCase(); } ), this.$_el.children().first()[0].tagName ) )
      //   return;
    };

    //@return : number
    Plugin.prototype._is_external = function( _href  ) {
      var _url_comp     = (location.host.replace('www.' , '')).split('.'),
          _nakedDomain  = new RegExp( _url_comp[0] + "." + _url_comp[1] );

      if ( _href !== '' && _href != '#' && this._isValidURL( _href ) )
        return ! _nakedDomain.test( _href ) ? true : false;
    };


    Plugin.prototype._isValidURL = function( _url ){
      var _pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      if ( _pattern.test( _url ) )
        return true;
      return false;
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