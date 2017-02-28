/**
 * --------------------------------------------------------------------------
 * Inspired by Bootstrap (v4.0.0-alpha.5): dropdown.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * --------------------------------------------------------------------------
 */
+function () {
  var _createClass = function () {
   function defineProperties(target, props) {
     for (var i = 0; i < props.length; i++) {
       var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ("value" in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
     }
   }return function (Constructor, protoProps, staticProps) {
     if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
   };
  }();

  function _classCallCheck(instance, Constructor) {
   if (!(instance instanceof Constructor)) {
     throw new TypeError("Cannot call a class as a function");
   }
  }

  var czrDropdown = function ($) {

    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */

    var NAME = 'czrDropdown';
    var VERSION = '1';
    var DATA_KEY = 'czr.czrDropdown';
    var EVENT_KEY = '.' + DATA_KEY;
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key
    var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key
    var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key
    var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)

    var Event = {
      HIDE: 'hide' + EVENT_KEY,
      HIDDEN: 'hidden' + EVENT_KEY,
      SHOW: 'show' + EVENT_KEY,
      SHOWN: 'shown' + EVENT_KEY,
      CLICK: 'click' + EVENT_KEY,
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY,
      KEYDOWN_DATA_API: 'keydown' + EVENT_KEY + DATA_API_KEY
    };

    var ClassName = {
      BACKDROP: 'czr-dropdown-backdrop',
      DISABLED: 'disabled',
      ACTIVE: 'active'
    };

    var Selector = {
      BACKDROP: '.czr-dropdown-backdrop',
      DATA_TOGGLE: '[data-toggle="czr-dropdown"]',
      FORM_CHILD: '.czr-dropdown form',
      ROLE_MENU: '[role="menu"]',
      ROLE_LISTBOX: '[role="listbox"]',
      NAVBAR_NAV: '.navbar-nav',
      PARENTS:  '.menu-item-has-children',
      VISIBLE_ITEMS: '[role="menu"] li:not(.disabled) a, ' + '[role="listbox"] li:not(.disabled) a'
    };

    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

    var czrDropdown = function () {
      function czrDropdown(element) {
        _classCallCheck(this, czrDropdown);

        this._element = element;

        this._addEventListeners();
      }

      // getters

      // public

      czrDropdown.prototype.toggle = function toggle() {
        if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
          return false;
        }

        var parent = czrDropdown._getParentFromElement(this);
        var isActive = $(parent).hasClass(ClassName.ACTIVE);
        var _parentsToNotClear = $.makeArray( $(parent).parents(Selector.PARENTS) );

        czrDropdown._clearMenus('', _parentsToNotClear );

        if (isActive) {
          return false;
        }

        if ('ontouchstart' in document.documentElement && !$(parent).closest(Selector.NAVBAR_NAV).length) {

          // if mobile we use a backdrop because click events don't delegate
          var dropdown = document.createElement('div');
          dropdown.className = ClassName.BACKDROP;
          $(dropdown).insertBefore(this);
          $(dropdown).on('click', function() { czrDropdown._clearMenus( '', _parentsToNotClear  ) } );
        }

        var relatedTarget = { relatedTarget: this };
        var showEvent = $.Event(Event.SHOW, relatedTarget);

        $(parent).trigger(showEvent);

        if (showEvent.isDefaultPrevented()) {
          return false;
        }

        this.focus();
        this.setAttribute('aria-expanded', 'true');

        $(parent).toggleClass(ClassName.ACTIVE);
        $(parent).trigger($.Event(Event.SHOWN, relatedTarget));

        return false;
      };

      czrDropdown.prototype.dispose = function dispose() {
        $.removeData(this._element, DATA_KEY);
        $(this._element).off(EVENT_KEY);
        this._element = null;
      };

      // private

      czrDropdown.prototype._addEventListeners = function _addEventListeners() {
        $(this._element).on(Event.CLICK, this.toggle);
      };

      // static

      czrDropdown._jQueryInterface = function _jQueryInterface(config) {
        return this.each(function () {
          var data = $(this).data(DATA_KEY);

          if (!data) {
            $(this).data(DATA_KEY, data = new czrDropdown(this));
          }

          if (typeof config === 'string') {
            if (data[config] === undefined) {
              throw new Error('No method named "' + config + '"');
            }
            data[config].call(this);
          }
        });
      };

      czrDropdown._clearMenus = function _clearMenus(event, _parentsToNotClear ) {
        if (event && event.which === RIGHT_MOUSE_BUTTON_WHICH) {
          return;
        }
        //TODO
        var backdrop = $(Selector.BACKDROP)[0];
        if (backdrop) {
          backdrop.parentNode.removeChild(backdrop);
        }

        var toggles = $.makeArray($(Selector.DATA_TOGGLE));


        for (var i = 0; i < toggles.length; i++) {
          var parent = czrDropdown._getParentFromElement(toggles[i]);
          var relatedTarget = { relatedTarget: toggles[i] };

          if (!$(parent).hasClass(ClassName.ACTIVE) || $.inArray(parent, _parentsToNotClear ) > -1 ){
            continue;
          }

          if (event && event.type === 'click' && /input|textarea/i.test(event.target.tagName) && $.contains(parent, event.target)) {
            continue;
          }

          var hideEvent = $.Event(Event.HIDE, relatedTarget);
          $(parent).trigger(hideEvent);
          if (hideEvent.isDefaultPrevented()) {
            continue;
          }

          toggles[i].setAttribute('aria-expanded', 'false');

          $(parent).removeClass(ClassName.ACTIVE).trigger($.Event(Event.HIDDEN, relatedTarget));
        }
      };

      czrDropdown._getParentFromElement = function _getParentFromElement(element) {
        var _parentNode = void 0;
        /* get the closest dropdown parent */
        var $_parent = $(element).closest(Selector.PARENTS);

        if ( $_parent.length ) {
          _parentNode = $_parent[0];
        }

        return _parentNode || element.parentNode;
      };

      czrDropdown._dataApiKeydownHandler = function _dataApiKeydownHandler(event) {
        if (!/(38|40|27|32)/.test(event.which) || /input|textarea/i.test(event.target.tagName)) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
          return;
        }

        var parent = czrDropdown._getParentFromElement(this);
        var isActive = $(parent).hasClass(ClassName.ACTIVE);

        if (!isActive && event.which !== ESCAPE_KEYCODE || isActive && event.which === ESCAPE_KEYCODE) {

          if (event.which === ESCAPE_KEYCODE) {
            var toggle = $(parent).find(Selector.DATA_TOGGLE)[0];
            $(toggle).trigger('focus');
          }

          $(this).trigger('click');
          return;
        }

        var items = $.makeArray($(Selector.VISIBLE_ITEMS));

        items = items.filter(function (item) {
          return item.offsetWidth || item.offsetHeight;
        });

        if (!items.length) {
          return;
        }

        var index = items.indexOf(event.target);

        if (event.which === ARROW_UP_KEYCODE && index > 0) {
          // up
          index--;
        }

        if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
          // down
          index++;
        }

        if (index < 0) {
          index = 0;
        }

        items[index].focus();
      };

      _createClass(czrDropdown, null, [{
        key: 'VERSION',
        get: function get() {
          return VERSION;
        }
      }]);

      return czrDropdown;
    }();

    /**
     * ------------------------------------------------------------------------
     * Data Api implementation
     * ------------------------------------------------------------------------
     */

    $(document).on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler).on(Event.KEYDOWN_DATA_API, Selector.ROLE_MENU, czrDropdown._dataApiKeydownHandler).on(Event.KEYDOWN_DATA_API, Selector.ROLE_LISTBOX, czrDropdown._dataApiKeydownHandler).on(Event.CLICK_DATA_API, czrDropdown._clearMenus).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle).on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
      e.stopPropagation();
    });

    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn[NAME] = czrDropdown._jQueryInterface;
    $.fn[NAME].Constructor = czrDropdown;
    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return czrDropdown._jQueryInterface;
    };

    return czrDropdown;
  }(jQuery);
}();

var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {

    init : function() {
      this.DATA_KEY  = 'czr.czrDropdown';
      this.EVENT_KEY = '.' + this.DATA_KEY;
      this.Event     = {
        PLACE     : 'place'+ this.EVENT_KEY,
        SHOWN     : 'shown' + this.EVENT_KEY,
        HIDDEN    : 'hidden' + this.EVENT_KEY,
      }
      this.Selector = {
        DATA_TOGGLE        : '[data-toggle="czr-dropdown"]',
        DATA_HOVER_PARENT  : '.czr-open-on-hover .menu-item-has-children, .primary-nav__woocart',
        DATA_CLICK_PARENT  : '.czr-open-on-click .menu-item-has-children, .primary-nav__woocart',
        DATA_PARENTS       : '.tc-header .menu-item-has-children'
      }

      this.ClassName = {
        DROPDOWN         : 'dropdown-menu',
        DROPDOWN_WRAPPER : 'czr-dropdown-menu',
        ACTIVE           : 'active',
        PARENTS          : 'menu-item-has-children'
      };

      //Integrated
      //this.dropdownMenuOnClick();
      this.dropdownMenuOnHover();
      this.dropdownPlacement();
    },


    dropdownTrigger : function( $_el, evt, data ) {
      $_el.trigger( evt+'.'+this.namespace, data );
    },

    //Handle dropdown on hover via js
    dropdownMenuOnHover : function() {
      var _dropdown_selector = this.Selector.DATA_HOVER_PARENT,
          self               = this;

      function _addOpenClass () {
        $_el = $(this);
        if ( ! $_el.hasClass(self.ClassName.ACTIVE) ) {
          $_el.addClass(self.ClassName.ACTIVE);
          $_el.trigger(self.Event.SHOWN);
        }
      };

      //a little delay before closing to avoid closing a parent before accessing the child
      function _removeOpenClass () {

        var $_el = $(this);

        _debounced_removeOpenClass = _.debounce( function() {
          if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
            $_el.removeClass(self.ClassName.ACTIVE);
            $_el.trigger( self.Event.HIDDEN );
          }

        }, 150);

        _debounced_removeOpenClass();
      };

      czrapp.$_body.on('mouseenter', _dropdown_selector, _addOpenClass );
      czrapp.$_body.on('mouseleave', _dropdown_selector , _removeOpenClass );
    },


    dropdownPlacement : function() {
      var self = this,
          doingAnimation = false;
          _is_visible    = function( _el ) {
            var $_this        = $(_el),
                _this_offset  = $_this.offset();

            if( _this_offset.left + $_this.outerWidth() > czrapp.$_window.width() ||
                _this_offset.top >= $_this.closest('.nav__menu').offset().top + $_this.closest('.nav__menu').outerHeight() ) {
              return false;
            }
            return true;
          }
      /*
      * Snake Prototype
      */
      czrapp.$_body.on( 'tc-resize', function() {
        if ( ! doingAnimation ) {
          doingAnimation = true;
          window.requestAnimationFrame(function() {
            var $_active_menu_items       = $( '.'+self.ClassName.PARENTS+'.'+self.ClassName.ACTIVE),
                $_active_first_menu_items = $( '[class*=sl-] .primary-nav__menu > .'+self.ClassName.PARENTS+'.'+self.ClassName.ACTIVE );

            //close the non visible
            //Needed only when single line
            $_active_first_menu_items.each( function() {
              var $_this = $(this);
              if ( !_is_visible( this ) ) {
                $_this.removeClass(self.ClassName.ACTIVE);
              }
            });

            $_active_menu_items.trigger(self.Event.PLACE);
            doingAnimation = false;
          });
        }
      });

      czrapp.$_body.on( this.Event.SHOWN+' '+this.Event.PLACE, this.Selector.DATA_PARENTS, function(evt) {
        var $_this       = $(this);

        if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) || !$_this.hasClass(self.ClassName.ACTIVE) )
          return;

        var $_dropdown_wrapper = $_this.children( '.'+self.ClassName.DROPDOWN_WRAPPER ).not('.'+self.ClassName.DROPDOWN);
            $_dropdown         = $_dropdown_wrapper.length ? $_dropdown_wrapper.children('.'+self.ClassName.DROPDOWN ) : $_this.children( '.'+self.ClassName.DROPDOWN );

        if ( !$_dropdown.length )
          return;

        //wrapper's (first-level) width must be at maximum the li width, and not in mobiles
         if (  $_dropdown_wrapper.length && $_dropdown_wrapper.closest('.menu-item-has-children').not('.czr-dropdown-submenu').length ) {
          if ( 'static' != $_dropdown.css('position') ) {
            //use of getBoundingClientRect because outerWidth rounds
            $_dropdown_wrapper.css( 'width', $_this[0].getBoundingClientRect().width );
          }else
            $_dropdown_wrapper.css( 'width', '');
         }

        //stage: if not visible $ isn't able to get width, offset
        $_dropdown.css( 'zIndex', '-100' ).css('display', 'block');

        _maybe_move( $_dropdown );

        //unstage if staged
        $_dropdown.css( 'zIndex', '').css('display', '');

      } );

      function _maybe_move( $_dropdown ){
        //reset
        $_dropdown.removeClass( 'open-left open-right' );
        if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {
          $_dropdown.addClass( 'open-left' );
        } if ( $_dropdown.offset().left < 0 ) {
          $_dropdown.addClass( 'open-right' );
        }
      }
    }
  };//_methods{}

  czrapp.methods.Czr_Dropdowns = {};
  $.extend( czrapp.methods.Czr_Dropdowns , _methods );

})(jQuery, czrapp);