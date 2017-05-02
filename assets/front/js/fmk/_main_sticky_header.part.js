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
  * case the trigger point dynamic computation cannot rely the sticky candidate as its position is
  * fixed or absolute
  * (see _toggleStickyAt() )
  */
(function($, czrapp) {
  var _methods =  {
    init : function() {
      this.namespace                  = 'tc-sticky-header';
      this._sticky_candidate_sel      = '.navbar-to-stick';
      //cache jQuery el
      this._cache_els();

      //additional state props
      this._didScroll                 = false;
      this._lastScroll                = 0;
      this._sticky_trigger            = false;
      this._mobile_viewport           = 991;

      this.collapse_show_selector     = "show";

      this.debounced_resize_actions   = _.debounce( function() {
          var self = this;
          this._maybe_move_utils();
          this.stickyHeaderMaybeToggling();
          $.each( $(self._sticky_navbar_toggleable_selector+'.'+self.collapse_show_selector), function(){
            self._stickyHeaderLimitMobileMenu( 'resize', $(this) );
          })
        }, 300
      );

      /* Cross browser support for CSS "transition end" event */
      this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';

      //set some props depending on the desired sticky behavior
      //this._sticky_trigger  = 300;
    },

    _cache_els : function() {
      //cache jQuery el
      this.$_sticky_candidate         = czrapp.$_tcHeader.find( this._sticky_candidate_sel );
      this._d_sticky_candidate        = czrapp.$_tcHeader.hasClass( 'header-absolute' ) ? 'overlaps' : 'pushes';
      this._sticky_candidate          = this._d_sticky_candidate;

      this._sticky_mobile_class       = 'czr-sticky-mobile';
      this.is_sticky_mobile           = czrapp.$_tcHeader.hasClass( this._sticky_mobile_class );

      this._fixed_classes             = 'navbar-sticky';

      this._lastScroll                = 0;
      this.$_sticky_placeholder       = null;
      this._sticky_mobile_selector    = '.'+this._sticky_mobile_class;
      this._sticky_navbar_toggleable_selector = this._sticky_mobile_selector+ ' [class*=navbar-toggleable-] .navbar-collapse';

      this._utils_selector            = '.primary-nav__utils';
      this._branding_selector         = '.branding__container';
      this._main_nav_selector         = '.primary-navbar__wrapper .primary-nav__nav';
      this._to_move_utils_selector    = '.tc-header[class*=v-logo_]';
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

      //ON BOOTSTRAP COLLAPSE SHOW/HIDE limit collapse to viewport
      czrapp.$_body.on( 'show.bs.collapse hidden.bs.collapse', this._sticky_navbar_toggleable_selector, function(evt) {
        self._stickyHeaderLimitMobileMenu(evt);
      });

      czrapp.$_body.on( 'shown.bs.collapse', this._sticky_navbar_toggleable_selector, function() {
        self._initCustomScrollbar( $(this) );
      });

      czrapp.$_body.on( this.transitionEnd , this._sticky_candidate_sel, function(evt) {
        if ( self.$_sticky_candidate[0] == evt.target ) {
          self._stickyHeaderLimitMobileMenu( 'resize', $(self._sticky_navbar_toggleable_selector+'.'+self.collapse_show_selector));
        }
      });
    },

    stickyHeaderMaybeToggling : function () {
      /* the viewport is less 992 pixels wide */
      if ( czrapp.matchMedia(this._mobile_viewport) ) {
        if ( this.is_sticky_mobile ) {
          this._sticky_candidate = 'pushes';
        }else if ( this._isStickyEnabled() ) {
          czrapp.$_body.trigger( 'sticky-disable' )
                       .removeClass( 'tc-sticky-header' ).addClass( 'tc-sticky-suspended' );
        }
      }
      else {
        /* the viewport is at least 992 pixels wide */
        if ( this.is_sticky_mobile ) {
          this._sticky_candidate = this._d_sticky_candidate;

        }else if ( ! this._isStickyEnabled() && czrapp.$_body.hasClass( 'tc-sticky-suspended' ) )
          czrapp.$_body.addClass( 'tc-sticky-header' ).removeClass( 'tc-sticky-suspended' );
        this.stickyHeaderEventHandler('scroll');
      }
    },

    stickyHeaderEventHandler : function( evt ) {
      if ( ! this._isStickyEnabled() ){
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
            //setTimeout( function() {
            window.requestAnimationFrame(function() {
              self._sticky_header_scrolling_actions();
            }
            //, 250
            );
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
    _isStickyEnabled : function() {
      return czrapp.$_body.hasClass('tc-sticky-header') ? true : false;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _toggleStickyAt : function() {
      if ( this._sticky_trigger )
        return this._sticky_trigger;

      var $_trigger_element = this.$_sticky_placeholder && this.$_sticky_placeholder[0].getBoundingClientRect().height ? this.$_sticky_placeholder : this.$_sticky_candidate;
      return $_trigger_element.outerHeight() + $_trigger_element.offset().top + 50;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _isStickyOn : function() {
      return czrapp.$_body.hasClass('sticky-enabled');
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _getScroll : function() {
      return window.pageYOffset || czrapp.$_window.scrollTop();
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _isScrollingDown : function(){
      return this._lastScroll < this._getScroll();
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _sticky_header_scrolling_actions : function () {
      var _scroll = this._getScroll();

      if ( 'overlaps' == this._sticky_candidate && this._isScrollingDown() && ! this._isStickyOn() )
        this._set_sticky_placeholder();
      var _toggleStickyAt = this._toggleStickyAt();

      if ( ! this._isStickyOn() && _scroll >= _toggleStickyAt ) {
        czrapp.$_body.trigger('sticky-enable');
      }
      else if ( this._isStickyOn() && _scroll < _toggleStickyAt ) {

        czrapp.$_body.trigger('sticky-disable');
      }

      this._lastScroll = this._getScroll();
      this._didScroll = false;
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _on_sticky_enable : function() {
      var self = this;
      if ( 'pushes' == this._sticky_candidate )
        this._set_sticky_placeholder();
      this._maybe_move_utils( this._main_nav_selector );
      this.$_sticky_candidate.addClass('animating').addClass( this._fixed_classes );
      czrapp.$_body.addClass( 'sticky-enabled' ).removeClass( 'sticky-disabled' ).trigger('sticky-enabled');

    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _on_sticky_disable : function() {
      var self = this;

      this._reset_sticky_placeholder();
      this._maybe_move_utils( this._branding_selector );
      this.$_sticky_candidate.removeClass( this._fixed_classes );
      czrapp.$_body.removeClass( 'sticky-enabled' ).addClass( 'sticky-disabled' ).trigger('sticky-disabled');

      $.each( $(self._sticky_navbar_toggleable_selector), function(){
        self._stickyHeaderLimitMobileMenu( 'resize', $(this) );
      })

    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _set_sticky_placeholder : function() {

      if ( ! this.$_sticky_placeholder ) {
        czrapp.$_tcHeader.prepend('<div id="sticky-placeholder"></div>');

        this.$_sticky_placeholder = $('#sticky-placeholder');
      }
      this.$_sticky_placeholder.css('height', this.$_sticky_candidate[0].getBoundingClientRect().height );
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _reset_sticky_placeholder: function(){
      if ( this.$_sticky_placeholder && this.$_sticky_placeholder.length )
        this.$_sticky_placeholder.css('height', '0');
    },

    _maybe_move_utils : function( _where ) {
      /* When to do this?
      *  1) on sticky-enabled toggle in desktop with vertical logo
      *  2) on resize in destkop when _where not specified :
      *  a) move in the branding container if sticky-disabled
      *  b) move in the nav if sticky-enabled
      */
      if (  !czrapp.matchMedia(this._mobile_viewport) ) {
        if ( _where ) {
          $(_where, this._to_move_utils_selector ).append($(this._utils_selector));
        } else {
          $( !this._isStickyOn() ? this._branding_selector : this._main_nav_selector , this._to_move_utils_selector ).append($(this._utils_selector));
        }
      }
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _stickyHeaderLimitMobileMenu : function(evt, $_el) {
      var self = this;

      //NEW: allow sticky on mobiles
      if ( !this.is_sticky_mobile )
        return;

      $_el = $_el ? $_el : [];
      $_el = !$_el.length && 'undefined' != typeof evt && evt.target ? $(evt.target) : $_el;

      if ( !$_el.length ) {
        return;
      }

      if ( 'resize' == evt && !$_el.hasClass('show') )
        return;

      if ( evt && 'hidden' == evt.type ) {
        this._resetHeaderLimitMobileMenu( $_el );
        return;
      }

      if ( czrapp.matchMedia(this._mobile_viewport) ) {
        //fallback on jQuery height() if window.innerHeight isn't defined (e.g. ie<9)
        var winHeight    = 'undefined' === typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
            newMaxHeight = winHeight - $_el.closest('div').offset().top + this._getScroll();

        $_el.css('max-height' , newMaxHeight + 'px').addClass('limited-height');

       //update mCustomScrollbar if show
        if ( $_el.is('[class*=mCustomScrollbar]') ) {
          $_el.mCustomScrollbar("update");
        }
        //re-init if temporary destroyed and show
        else if ( $_el.hasClass('show') && $_el.hasClass('mCS_destroyed') ) {
          self._initCustomScrollbar($_el);
        }

      }else {
        this._resetHeaderLimitMobileMenu( $_el );
      }
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _resetHeaderLimitMobileMenu : function( $_el ) {
      $_el = $_el || $(this._sticky_navbar_toggleable_selector);

      if ( !$_el.length ) {
        return;
      }

      $_el.removeClass('limited-height').css({
        'max-height': '',
        'overflow' : '',
      });
      if ( $_el.is('[class*=mCustomScrollbar]') ) {
        $_el.mCustomScrollbar("destroy");
      }
    },

    //STICKY HEADER SUB CLASS HELPER (private like)
    _initCustomScrollbar : function( $_el ) {
      if ( 'function' == typeof $.fn.mCustomScrollbar ) {
        $_el.mCustomScrollbar({
          theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
        });
      }
    },

  };//_methods{}

  czrapp.methods.Czr_StickyHeader = {};
  $.extend( czrapp.methods.Czr_StickyHeader , _methods );

})(jQuery, czrapp);