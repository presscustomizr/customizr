/* ===================================================
 * contentPicker.js v1.0.0
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France - Rocco Aliberti, Salerno, Italy
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Content Picker
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  //defaults
  var pluginName = 'contentPicker',
      defaults = {
        timeToTriggerRiver: 150,
        minRiverAJAXDuration: 200,
        riverBottomThreshold: 5,
        template : '#TCContentPicker',
        list_type : 'page'
      },
      River, Query, searchTimer
      recents = {}; /* cache */

  function Plugin( element, options ) {
    this.el = element;
    this.options = $.extend( {}, defaults, options) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }
  
  //can access this.element and this.option
  //@return void
  Plugin.prototype.init = function () {
    var self = this;

    this.lastSearch = '';
    this.inputs = {};
    this.rivers = {};

    //render the template
    this._render();

    // options
    this.list_type  = this.options.list_type;

    //inputs
    this.inputs.$wrap = $( '.tc-cp-link-wrap' , this.el );
    this.inputs.$dialog = $( '.tc-cp-link', this.el );


    //scrollable divs
    this.inputs.$scrollable = $( '.tc-cp-link .query-results, .tc-cp-link .link-selector' , this.el );
    // ID
    this.inputs.$id = $( '#id-field' , this.el  );
    
    this.inputs.$search = $( '.link-search-field', this.el );
    
    // Get search notice text
    this.inputs.queryNotice        = $( '.query-notice-message', this.el );
    this.inputs.queryNoticeTextDefault   = this.inputs.queryNotice.find( '.query-notice-default', this.el );
    this.inputs.queryNoticeTextHint    = this.inputs.queryNotice.find( '.query-notice-hint', this.el );
        
    // Build Rivers
    this.rivers.search = new River( $( '.search-results' , this.el ), this.lastSearch, 'search' , this );
    this.rivers.recent = new River( $( '.most-recent-results', this.el), this.lastSearch, 'recent' , this );
    this.rivers.elements = this.inputs.$dialog.find( '.query-results', this.el );

    // events 
    this._bind_events();

    //@nikeo addon opens on init
    this.open();

  }; //init end
  

  Plugin.prototype._render = function() {
    var contentPickerTemplate = _.template($(this.options.template).html());
    $(this.el).append(contentPickerTemplate);
  }

  Plugin.prototype._bind_events = function() {
    var self = this;
    // bind various clicks

    this.rivers.elements.on( 'river-select', this.updateFields );

    // Display 'hint' message when search field or 'query-results' box are focused
    this.inputs.$search.on( 'focus.link-search-field', function() {
      self.inputs.queryNoticeTextDefault.hide();
      self.inputs.queryNoticeTextHint.removeClass( 'screen-reader-text' ).show();
    } ).on( 'blur.link-search-field', function() {
      self.inputs.queryNoticeTextDefault.show();
      self.inputs.queryNoticeTextHint.addClass( 'screen-reader-text' ).hide();
    } );

    this.inputs.$search.keyup( function() {
      var search = this;
      
      window.clearTimeout( searchTimer );
      searchTimer = window.setTimeout( function() {
        self.searchInternalLinks( search );
      }, 500 );
    });
  }

  Plugin.prototype.toggle = function() {
    if ( ! this.inputs.$wrap.hasClass('open') ){
      this.open();
    }else{
      this.close();
    }
  };

 
  Plugin.prototype.open = function() {
    this.inputs.$wrap.addClass('open').show();
    this.refresh();
    $( document ).trigger( 'contentpicker-open', this.inputs.$wrap );
  };

  Plugin.prototype.close = function() {
    this.inputs.$wrap.removeClass('open').hide();
    $( document ).trigger( 'contentpicker-close', this.inputs.$wrap );
  };

  Plugin.prototype.refresh = function() {
    // Refresh this.rivers (clear links, check visibility)
    this.rivers.search.refresh();
    this.rivers.recent.refresh();

    // Load the most recent results if this is the first time opening the panel,
    // or if the cached results are more than the shown ones 
    // (usually when another instance has updated them)
    var res = this.rivers.recent.ul.children();
    if ( ! res.length || ( recents[this.list_type] && res.length < recents[this.list_type].r.length ) )
      this.rivers.recent.ajax( false , 'recent');
  };

  Plugin.prototype.updateFields = function( e, li, ev, river ) {
    // packet
    var data = {
        id    : li.children('.item-id').val(),
        title : li.children('.item-title').text(),
        info  : li.children('.item-info').text(),
        thumb : li.children('.item-thumbnail').find('img'),
        type  : river.picker.list_type,
    }
    river.picker.inputs.$id.val(data.id);  
    $(river.picker.el).trigger( 'content-select', [data, ev]);
  };


  Plugin.prototype.searchInternalLinks = function( el ) {
    var t = $( el ), waiting,
        search = t.val();

    if ( search.length > 2 ) {
      this.rivers.recent.hide();
      this.rivers.search.show();

      // Don't search if the keypress didn't change the title.
      if ( this.lastSearch == search )
        return;

      this.lastSearch = search;
      waiting = t.parent().find('.spinner').show();

      this.rivers.search.change( search );
      this.rivers.search.ajax( function() {
        waiting.hide();
      } , 'search' );
    } else {
      this.rivers.search.hide();
      this.rivers.recent.show();
    }
  };

  // The River @todo -> plugin
  	River = function( element, search, type, picker ) {
		var self 			= this;
		this.type 			= type || 'recent';
		this.element 		= element;
		this.ul 			= element.children( 'ul' );
		this.contentHeight 	= element.children( '#link-selector-height' );
		this.waiting 		= element.find('.river-waiting');
        
        this.picker         = picker;

		this.change( search );
		this.refresh();

		this.picker.inputs.$scrollable.scroll( function() {
			self.maybeLoad();
		});
		element.on( 'click', 'li', function( event ) {
			self.select( $( this ), event );
		});
	};

	$.extend( River.prototype, {
		refresh: function() {
			this.deselect();
			this.visible = this.element.is( ':visible' );
		},
		show: function() {
			if ( ! this.visible ) {
				this.deselect();
				this.element.show();
				this.visible = true;
			}
		},
		hide: function() {
			this.element.hide();
			this.visible = false;
		},
		// Selects a list item and triggers the river-select event.
		select: function( li, event ) {
			var liHeight, elHeight, liTop, elTop;

			if ( li.hasClass( 'unselectable' ) || li == this.selected )
				return;

			this.deselect();
			this.selected = li.addClass( 'selected' );
			// Make sure the element is visible
			liHeight = li.outerHeight();
			elHeight = this.element.height();
			liTop = li.position().top;
			elTop = this.element.scrollTop();

			if ( liTop < 0 ) // Make first visible element
				this.element.scrollTop( elTop + liTop );
			else if ( liTop + liHeight > elHeight ) // Make last visible element
				this.element.scrollTop( elTop + liTop - elHeight + liHeight );
			
            // Trigger the river-select event
			this.element.trigger( 'river-select', [ li, event, this ] );
		},
		deselect: function() {
			if ( this.selected )
				this.selected.removeClass( 'selected' );
			this.selected = false;
		},
		prev: function() {
			if ( ! this.visible )
				return;

			var to;
			if ( this.selected ) {
				to = this.selected.prev( 'li' );
				if ( to.length )
					this.select( to );
			}
		},
		next: function() {
			if ( ! this.visible )
				return;

			var to = this.selected ? this.selected.next( 'li' ) : $( 'li:not(.unselectable):first', this.element );
			if ( to.length )
				this.select( to );
		},
		ajax: function( callback, type ) {
			var self = this,
				delay = this.query.page == 1 ? 0 : this.picker.options.minRiverAJAXDuration,
				response = this.picker.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, delay );
            // load cached results per list type
            if ( 'recent' == type && recents[this.picker.list_type] )
              self.process(recents[this.picker.list_type].r, { page: 1 });
            else { 
               // init our per type cache  
              if ( ! recents[this.picker.list_type] && 'recent' == type )
                recents[this.picker.list_type] = { r: [], page: '', allLoaded:''};
			  
              this.query.ajax( response, type );
            }
		},
		change: function( search ) {
			if ( this.query && this._search == search )
				return;

			this._search = search;
			this.query = new Query( search, this.picker );
			this.element.scrollTop( 0 );
		},
		process: function( results, params ) {
			var list = '', alt = true, classes = '',
				firstPage = params.page == 1;
			
            if ( ! results ) {
				if ( firstPage ) {
					list += '<li class="unselectable no-matches-found"><span class="item-title"><em>' +
						TCCPParams.TCCPL10n.noMatchesFound + '</em></span></li>';
				}
            }
            if ( firstPage )
              list += '<li><input type="hidden" class="item-id" value=""><span class="item-title"> - ' + TCCPParams.TCCPL10n.select + ' - </span></li>';
			if ( results ) {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-id" value="' + this.ID + '" />';
					list += this.thumbnail ? '<span class="item-thumbnail">' + this.thumbnail + '</span>' : '';
					list += '<span class="item-title">';
					list += this.title ? this.title : TCCPParams.TCCPL10n.noTitle;
					list += '</span><span class="item-info">' + this.info + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - self.picker.options.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - self.picker.options.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.hide();
				}, this.type );
			}, self.picker.options.timeToTriggerRiver );
		}
	});

	Query = function( search, picker) {
        this.list_type  = picker.list_type;
        /* do we have cached results for the current list type?*/
        /* start next query from the last retrieved page, if needed */
        if ( recents[this.list_type] && ! search ){
          this.page = recents[this.list_type].page;
          this.allLoaded = recents[this.list_type].allLoaded;
        }else{
		  this.page = 1;
		  this.allLoaded = false;
        }

		this.querying    = false;
		this.search      = search;
        this.nonce       = TCCPParams.TCCPNonce;
        this.nonce_name  = TCCPParams.TCCPNonceName;
        this.action      = TCCPParams.TCCPaction;
	};

	$.extend( Query.prototype, {
		ready: function() {
			return ! ( this.querying || this.allLoaded );
		},
		ajax: function( callback , type ) {
			var self = this,
				query = {
					action 		   : this.action,
					page 		   : this.page,
                    ListType       : this.list_type,
					TCCPnonce      : this.nonce,
					TCCPnonce_name : this.nonce_name
				};

            if ( this.search )
				query.search = this.search;

			this.querying = true;
			
            $.post( ajaxurl, query, function( r ) {
				self.page++;
				self.querying = false;
				self.allLoaded = ! r;
				callback( r, query );
                // cache recent results if not search 
                // if not all results were loaded
                // and if the page is greater than the latest cached (race condition)
                if ( ! self.search && ! recents[self.list_type].allLoaded && self.page > recents[self.list_type].page){
                    recents[self.list_type] = {
                        r : $.merge( recents[self.list_type].r, r ), 
                        allLoaded: !r, 
                        page : self.page 
                    };
                }
			}, 'json' );
		}
	});


  /********
  * HELPERS
  *********/
  Plugin.prototype.delayedCallback = function( func, delay ) {
    var timeoutTriggered, funcTriggered, funcArgs, funcContext;

    if ( ! delay )
      return func;

    setTimeout( function() {
      if ( funcTriggered )
        return func.apply( funcContext, funcArgs );
      // Otherwise, wait.
      timeoutTriggered = true;
    }, delay );

    return function() {
      if ( timeoutTriggered )
        return func.apply( this, arguments );
      // Otherwise, wait.
      funcArgs = arguments;
      funcContext = this;
      funcTriggered = true;
    };
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

/* ===================================================
 *
 * Content Picker Control: SELECT
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  //defaults
  var pluginName = 'selectContentPicker',
    defaults = {
        template      : '#TCContentPicker',
        display_title : '- Select -',
        display_type  : ''
    }
    
  function Plugin( element, options ) {
    this.element = element;
    //jquery cached element.
    this.$element = $(element);
    this.options = this.$element.data() || $.extend( {}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }

  Plugin.prototype.init = function(){
    this.contentPicker = '';
    this._render();
    this._bind_events();
  }

  Plugin.prototype._bind_events = function(){
    var self = this;  
    this.$container.on('click', '.tc-cp-ui', function(e){
        e.preventDefault();
        e.stopPropagation();
        self._render_content_picker(e, this);
        $( document ).trigger( self._name + '-click', self);
    }).on('content-select', function(e, data){
        // update the input
        self.$element.val(data.id);
        self.$element.trigger('change', [true]);
        self.$container.find('.tc-cp-ui').html( self._render_display_value(data) );
    });
  }
  
  Plugin.prototype._render = function(){
    var select = '<div class="tc-cp-select">';
    select += '<span class="tc-cp-ui">' + this._render_display_value( { title : this.options.display_title, type : this.options.display_type}) + '</span>';
    select += '</div';

    this.$container = $(select);
    this.$container.insertAfter(this.$element.addClass('tc-cp-selecter'));

  }

  /* This method takes care of init/render the picker, if not already rendered, or toggle it */
  Plugin.prototype._render_content_picker = function( e, el ) {
    if ( ! this.contentPicker ){
      this.$container.contentPicker( this.options );
      this.contentPicker = this.$container.data('plugin_contentPicker');
      this.$container.addClass('open');
    }else {
      this.contentPicker.toggle();
      this.$container.toggleClass('open');
    }
  }


  Plugin.prototype._render_display_value = function ( _data ){
    var _display = '<span class="item-title">' + _data.title + '</span>';
    if ( _data.type && 'page' != _data.type )
       _display += '</span><span class="item-info">' + _data.info + '</span></li>';
    return _display;
  }

  // prevents against multiple instantiations
  $.fn[pluginName] = function ( options ) {
      return this.each(function () {
          if (!$.data(this, 'plugin_' + pluginName)) {
              $.data(this, 'plugin_' + pluginName,
              new Plugin( this, options ));
          }
      });
  };
})(jQuery);
