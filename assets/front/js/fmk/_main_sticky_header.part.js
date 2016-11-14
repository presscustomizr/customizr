var czrapp = czrapp || {};
/************************************************
* STICKY HEADER SUB CLASS
*************************************************/
  /*
  * The script uses a placeholder for the sticky-element:
  * - when the "sticky candidate" overlaps the content (e.g. a slider) at the first first scroll
  * a placeholder will be injected. The placeholder has position absolute and its height is set equal to the
  * sticky candidate height when injected, reset to 0 when the fixed element backs to normal state, set again
  * when scrolling down.
  * - when the "sticky candidate" pushes the content, the placeholder is staticaly positioned,
  * it's injected each time the candidate sticks, its height is set equal to the sticky candidate height,
  * and reset when it unsticks.
  *
  * In both cases the placeholder will be our reference to switch from sticky to un-sticky mode
  * and the reference to switch from un-sticky to sticky ONLY in the "overlapping" case. 'Cause in that
  * case the trigger point dynamic computation cannto rely the sticky candidate as its position is
  * fixed or absolute
  * (see _toggleStickyAt() )
  */
(function($, czrapp) {
  var _methods =  {
    init : function() {
      this.namespace                  = 'tc-sticky-header';
      //cache jQuery el
      this.$_sticky_candidate         = czrapp.$_tcHeader.find('.navbar-to-stick');

      //additional state props
      this._didScroll                 = false;
      this._sticky_candidate_overlap  = czrapp.$_tcHeader.hasClass( 'header-absolute' );
      this._sticky_candidate_push     = !  this._sticky_candidate_overlap;
      this._fixed_classes             = 'navbar-sticky';
      this._lastScroll                = 0;
      this.$_sticky_placeholder       = null;
      this._sticky_trigger            = false;

      this.debounced_resize_actions   = _.debounce( this.stickyHeaderMaybeToggling, 300 );

      //set some props depending on the desired sticky behavior
      this._sticky_trigger  = 300;
    },

    triggerStickyHeaderLoad : function() {
      if ( ! CZRParams.stickyHeader )
        return;

      //LOADING ACTIONS
      czrapp.$_body.addClass( 'tc-sticky-header' )
                   .trigger( 'sticky-enabled-on-load' );
    },

    stickyHeaderEventListener : function() {
      //LOADING ACTIONS
      var self = this;

      czrapp.$_body.on( 'sticky-enabled-on-load' , function() {
        self.stickyHeaderEventHandler('on-load');
      });//.on()

      czrapp.$_body.on( 'sticky-enable', function() {
        self.stickyHeaderEventHandler('on-stick');
      })
      .on( 'sticky-disable', function() {
        self.stickyHeaderEventHandler('on-unstick');
      })
      /* Remove animating class */
      .on( 'sticky-enabled', function() {
        self.stickyHeaderEventHandler('on-enabled');
      })


      czrapp.$_window.scroll( function() {
        self.stickyHeaderEventHandler('scroll');
      });

     //RESIZING ACTIONS
      czrapp.$_window.on( 'tc-resize', function() {
        self.debounced_resize_actions();
      });
    },

    stickyHeaderMaybeToggling : function () {

      /* We meed an alternative for ie<10*/
      if ( window.matchMedia("(max-width: 991px)").matches) {
        /* the viewport is less 992 pixels wide */
        if ( this._is_sticky_enabled() )
          czrapp.$_body.trigger( 'sticky-disable' )
                       .removeClass( 'tc-sticky-header' );
      } else {
        /* the viewport is at least 992 pixels wide */
        if ( ! this._is_sticky_enabled() )
          czrapp.$_body.addClass( 'tc-sticky-header' );
        this.stickyHeaderEventHandler('scroll');
      }
    },

    stickyHeaderEventHandler : function( evt ) {
      if ( ! this._is_sticky_enabled() ){
        return;
      }

      var self = this;

      switch ( evt ) {
        case 'on-load' :
          this.stickyHeaderMaybeToggling();
        break;

        case 'scroll' :
          if( ! this._didScroll ) {
            this._didScroll = true;
            setTimeout( function() {
              self._sticky_header_scrolling_actions();
            }, 250 );
          }
        break;

        case 'on-stick' :
          this._on_sticky_enable();
        break;

        case 'on-unstick' :
          this._on_sticky_disable();
        break;

        case 'on-enabled' :
          setTimeout( function(){
            self.$_sticky_candidate.removeClass('animating');
          }, 10);
      }
    },


    //STICKY HEADER SUB CLASS HELPER (private like)
    _is_sticky_enabled : function() {
      return czrapp.$_body.hasClass('tc-sticky-header') ? true : false;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _toggleStickyAt : function() {
      if ( this._sticky_trigger )
        return this._sticky_trigger;

      var $_trigger_element = this.$_sticky_placeholder && this.$_sticky_placeholder.height() ? this.$_sticky_placeholder : this.$_sticky_candidate;
      console.log($_trigger_element.outerHeight() + $_trigger_element.offset().top + 50);
      return $_trigger_element.outerHeight() + $_trigger_element.offset().top + 50;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _isStickyOn : function() {
      return czrapp.$_body.hasClass('sticky-enabled');
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _get_scroll : function() {
      return window.pageYOffset || czrapp.$_window.scrollTop();
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _isScrollingDown : function(){
      return this._lastScroll < this._get_scroll();
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _sticky_header_scrolling_actions : function () {
      var _scroll = this._get_scroll();

      if ( this._sticky_candidate_overlap && this._isScrollingDown() && ! this._isStickyOn() )
        this._set_sticky_placeholder();

      if ( ! this._isStickyOn() && _scroll >= this._toggleStickyAt() )
        czrapp.$_body.trigger('sticky-enable');

      else if ( this._isStickyOn() && _scroll < this._toggleStickyAt() )
        czrapp.$_body.trigger('sticky-disable');

      this._didScroll = false;
      this._lastScroll = this._get_scroll();
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _on_sticky_enable : function() {
      if ( this._sticky_candidate_push )
        this._set_sticky_placeholder();

      this.$_sticky_candidate.addClass('animating').addClass( this._fixed_classes );
      czrapp.$_body.addClass( 'sticky-enabled' ).removeClass( 'sticky-disabled' ).trigger('sticky-enabled');
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _on_sticky_disable : function() {
      this._reset_sticky_placeholder();
      this.$_sticky_candidate.removeClass( this._fixed_classes );
      czrapp.$_body.removeClass( 'sticky-enabled' ).addClass( 'sticky-disabled' ).trigger('sticky-disabled');
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _set_sticky_placeholder : function() {
      if ( ! this.$_sticky_placeholder ) {
        czrapp.$_tcHeader.append('<div id="sticky-placeholder"></div>');
        this.$_sticky_placeholder = $('#sticky-placeholder');
      }
      this.$_sticky_placeholder.css('height', this.$_sticky_candidate.outerHeight() );
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _reset_sticky_placeholder: function(){
      if ( this.$_sticky_placeholder && this.$_sticky_placeholder.length )
        this.$_sticky_placeholder.css('height', '0');
    }
  };//_methods{}

  czrapp.methods.Czr_StickyHeader = {};
  $.extend( czrapp.methods.Czr_StickyHeader , _methods );

})(jQuery, czrapp);