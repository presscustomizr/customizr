
var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {

    initOnCzrReady : function() {
      this.DATA_KEY  = 'czr.czrDropdown';
      this.EVENT_KEY = '.' + this.DATA_KEY;
      this.Event     = {
        PLACE_ME  : 'placeme'+ this.EVENT_KEY,
        PLACE_ALL : 'placeall' + this.EVENT_KEY,
        SHOWN     : 'shown' + this.EVENT_KEY,
        SHOW      : 'show' + this.EVENT_KEY,
        HIDDEN    : 'hidden' + this.EVENT_KEY,
        HIDE      : 'hide' + this.EVENT_KEY,
        CLICK     : 'click' + this.EVENT_KEY,
        TAP       : 'tap' + this.EVENT_KEY,
      };
      this.ClassName = {
        DROPDOWN         : 'czr-dropdown-menu',
        SHOW             : 'show',
        PARENTS          : 'menu-item-has-children'
      };

      this.Selector = {
        DATA_TOGGLE              : '[data-toggle="czr-dropdown"]',
        DATA_SHOWN_TOGGLE        : '.' +this.ClassName.SHOW+ '> [data-toggle="czr-dropdown"]',
        DATA_HOVER_PARENT        : '.czr-open-on-hover .menu-item-has-children, .primary-nav__woocart',
        DATA_CLICK_PARENT        : '.czr-open-on-click .menu-item-has-children',
        DATA_PARENTS             : '.tc-header .menu-item-has-children'
      };
    },


    //Handle dropdown on hover via js
    //TODO: find a way to unify this with czrDropdown
    dropdownMenuOnHover : function() {
      var _dropdown_selector = this.Selector.DATA_HOVER_PARENT,
          self               = this;

      enableDropdownOnHover();

      function _addOpenClass ( evt ) {

        var $_el = $(this);

        _debounced_addOpenClass = _.debounce( function() {

          //do nothing if menu is mobile
          if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
            return false;

          if ( ! $_el.hasClass(self.ClassName.SHOW) ) {
            $_el.addClass(self.ClassName.SHOW)
                .trigger(self.Event.SHOWN);

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'true');
          }

        }, 150);

        _debounced_addOpenClass();
      }

      //a little delay before closing to avoid closing a parent before accessing the child
      function _removeOpenClass () {

        var $_el = $(this);

        _debounced_removeOpenClass = _.debounce( function() {

          if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
            $_el.removeClass(self.ClassName.SHOW)
                .trigger( self.Event.HIDDEN );

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'false');
          }

        }, 150);

        _debounced_removeOpenClass();
      }

      function enableDropdownOnHover() {

        czrapp.$_body.on( 'mouseenter', _dropdown_selector, _addOpenClass );
        czrapp.$_body.on( 'mouseleave', _dropdown_selector , _removeOpenClass );

      }

      function disableDropdownOnHover() {

        czrapp.$_body.off( 'mouseenter', _dropdown_selector, _addOpenClass );
        czrapp.$_body.off( 'mouseleave', _dropdown_selector , _removeOpenClass );

      }

    },

    dropdownOpenGoToLinkOnClick : function() {
      var self = this;

      //go to the link if submenu is already opened
      //This happens before the closing occurs when dropdown on click and the dropdown on hover (see the debounce in this case)
      czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE, function(evt) {

            var $_el = $(this);

            //do nothing if menu is mobile
            if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
              return false;

            evt.preventDefault();


            if ( '#' != $_el.attr( 'href' ) ) {
              window.location = $_el.attr('href');
            }

            else {
              return true;
            }

      });//.on()

    },



    /*
    * Snake Prototype
    */
    dropdownPlacement : function() {
      var self = this,
          doingAnimation = false;

      czrapp.$_window
          //on resize trigger Event.PLACE on active dropdowns
          .on( 'resize', function() {
                  if ( ! doingAnimation ) {
                        doingAnimation = true;
                        window.requestAnimationFrame(function() {
                          //trigger a placement on the open dropdowns
                          $( '.'+self.ClassName.PARENTS+'.'+self.ClassName.SHOW)
                              .trigger(self.Event.PLACE_ME);
                          doingAnimation = false;
                        });
                  }

          });

      czrapp.$_body
          .on( this.Event.PLACE_ALL, function() {
                      //trigger a placement on all
                      $( '.'+self.ClassName.PARENTS )
                          .trigger(self.Event.PLACE_ME);
          })
          //snake bound on menu-item shown and place
          .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.DATA_PARENTS, function(evt) {
            evt.stopPropagation();
            _do_snake( $(this), evt );
          });


      //snake
      function _do_snake( $_el, evt ) {

        if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) )
          return;

        var $_this       = $_el,
            $_dropdown   = $_this.children( '.'+self.ClassName.DROPDOWN );

        if ( !$_dropdown.length )
          return;

        $_dropdown.css( 'zIndex', '-100' ).css('display', 'block');

        _maybe_move( $_dropdown );

        //unstage if staged
        $_dropdown.css( 'zIndex', '').css('display', '');

      }


      function _maybe_move( $_dropdown ) {
        //snake inheritance
        if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( 'open-left' ) ) {
          $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );
        }
        else {
          $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );
        }

        //let's compute on which side open the dropdown
        if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {

          $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );

        }
        else if ( $_dropdown.offset().left < 0 ) {

          $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );

        }

      }

    }


  };//_methods{}

  czrapp.methods.Dropdowns = {};
  $.extend( czrapp.methods.Dropdowns , _methods );




  /**
   * --------------------------------------------------------------------------
   * Inspired by Bootstrap (v4.0.0-alpha.6): dropdown.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * updated to : https://github.com/twbs/bootstrap/commit/1f37c536b2691e4a98310982f9b58ede506f11d8#diff-bfe9dc603f82b0c51ba7430c1fe4c558
   * 20/05/2017
   * --------------------------------------------------------------------------
   */


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

    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */

    var NAME = 'czrDropdown';
    var VERSION = '1'; // '4.0.0-alpha.6';
    var DATA_KEY = 'czr.czrDropdown';
    var EVENT_KEY = '.' + DATA_KEY;
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var ESCAPE_KEYCODE = 27; // KeyboardEvent.which value for Escape (Esc) key
    var SPACE_KEYCODE = 32; // KeyboardEvent.which value for space key
    var TAB_KEYCODE  = 9; // KeyboardEvent.which value for tab key
    var ARROW_UP_KEYCODE = 38; // KeyboardEvent.which value for up arrow key
    var ARROW_DOWN_KEYCODE = 40; // KeyboardEvent.which value for down arrow key
    var RIGHT_MOUSE_BUTTON_WHICH = 3; // MouseEvent.which value for the right button (assuming a right-handed mouse)
    var REGEXP_KEYDOWN = new RegExp(ARROW_UP_KEYCODE + '|' + ARROW_DOWN_KEYCODE + '|' + ESCAPE_KEYCODE );

    var Event = {
      HIDE: 'hide' + EVENT_KEY,
      HIDDEN: 'hidden' + EVENT_KEY,
      SHOW: 'show' + EVENT_KEY,
      SHOWN: 'shown' + EVENT_KEY,
      CLICK: 'click' + EVENT_KEY,
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY,
      KEYDOWN_DATA_API: 'keydown' + EVENT_KEY + DATA_API_KEY,
      KEYUP_DATA_API: 'keyup' + EVENT_KEY + DATA_API_KEY
    };

    var ClassName = {
      DISABLED: 'disabled',
      SHOW: 'show'
    };

    var Selector = {
      DATA_TOGGLE: '[data-toggle="czr-dropdown"]',
      FORM_CHILD: '.czr-dropdown form',
      MENU: '.dropdown-menu',
      NAVBAR_NAV: '.navbar-nav',
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)'
    };

    var czrDropdown = function ($) {



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

          //do nothing if menu is mobile
          if( 'static' == $(this).next( Selector.MENU ).css( 'position' ) )
            return true;

          var parent = czrDropdown._getParentFromElement(this);
          var isActive = $(parent).hasClass(ClassName.SHOW);
          var _parentsToNotClear = $.makeArray( $(parent).parents(Selector.PARENTS) );

          czrDropdown._clearMenus('', _parentsToNotClear );

          if (isActive) {
            return false;
          }

          var relatedTarget = {
            relatedTarget: this
          };
          var showEvent = $.Event(Event.SHOW, relatedTarget);

          $(parent).trigger(showEvent);

          if (showEvent.isDefaultPrevented()) {
            return false;
          }

          // if this is a touch-enabled device we add extra
          // empty mouseover listeners to the body's immediate children;
          // only needed because of broken event delegation on iOS
          // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
          if ('ontouchstart' in document.documentElement && !$(parent).closest(Selector.NAVBAR_NAV).length) {
            $('body').children().on('mouseover', null, $.noop);
          }

          this.focus();
          this.setAttribute('aria-expanded', 'true');

          $(parent).toggleClass(ClassName.SHOW);
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
              data = new czrDropdown(this);
              $(this).data(DATA_KEY, data);
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

          if (event && (event.which === RIGHT_MOUSE_BUTTON_WHICH || event.type === 'keyup' && event.which !== TAB_KEYCODE)) {
            return;
          }


          var toggles = $.makeArray($(Selector.DATA_TOGGLE));


          for (var i = 0; i < toggles.length; i++) {
            var parent = czrDropdown._getParentFromElement(toggles[i]);
            var relatedTarget = { relatedTarget: toggles[i] };

            if (!$(parent).hasClass(ClassName.SHOW) || $.inArray(parent, _parentsToNotClear ) > -1 ){
              continue;
            }

            if (event && ( event.type === 'click' &&
                /input|textarea/i.test(event.target.tagName) || event.type === 'keyup' && event.which === TAB_KEYCODE) && $.contains(parent, event.target)) {
              continue;
            }

            var hideEvent = $.Event(Event.HIDE, relatedTarget);
            $(parent).trigger(hideEvent);
            if (hideEvent.isDefaultPrevented()) {
              continue;
            }

            // if this is a touch-enabled device we remove the extra
            // empty mouseover listeners we added for iOS support
            if ('ontouchstart' in document.documentElement) {
              $('body').children().off('mouseover', null, $.noop);
            }


            toggles[i].setAttribute('aria-expanded', 'false');

            $(parent).removeClass(ClassName.SHOW).trigger($.Event(Event.HIDDEN, relatedTarget));
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
          if (!REGEXP_KEYDOWN.test(event.which) || /button/i.test(event.target.tagName) && event.which === SPACE_KEYCODE ||
             /input|textarea/i.test(event.target.tagName)) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();

          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
            return;
          }

          var parent = czrDropdown._getParentFromElement(this);
          var isActive = $(parent).hasClass(ClassName.SHOW);

          if (!isActive && ( event.which !== ESCAPE_KEYCODE || event.which !== SPACE_KEYCODE ) ||
               isActive && ( event.which !== ESCAPE_KEYCODE || event.which !== SPACE_KEYCODE ) ) {

            if (event.which === ESCAPE_KEYCODE) {
              var toggle = $(parent).find(Selector.DATA_TOGGLE)[0];
              $(toggle).trigger('focus');
            }

            $(this).trigger('click');
            return;
          }

         /* var items = $.makeArray($(Selector.VISIBLE_ITEMS));

          items = items.filter(function (item) {
            return item.offsetWidth || item.offsetHeight;
          });*/
          var items = $(parent).find(Selector.VISIBLE_ITEMS).get();

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

      $(document)
        .on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler)
        .on(Event.KEYDOWN_DATA_API, Selector.MENU, czrDropdown._dataApiKeydownHandler)
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API, czrDropdown._clearMenus)
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle)
        .on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
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

})(jQuery, czrapp);