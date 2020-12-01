
var czrapp = czrapp || {};
/************************************************
* DROPDOWNS SUB CLASS
*************************************************/
(function($, czrapp) {
  var _methods =  {

    initOnCzrReady : function() {
            // PRINT A YELLOW TEST BLOCK FOR TEST PURPOSES
            // if ( -1 !== window.location.href.indexOf('nimble-formatting-tests') ) {
            //     czrapp.$test = $('<div/>', { id : "test_wrapper", style : "width: 100%;height: 200px;padding: 10px;background: yellow;position: fixed;bottom: 0;z-index: 1000;"});
            //     $("body").append(czrapp.$test);
            // }

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
            };
            this.ClassName = {
              DROPDOWN                : 'czr-dropdown-menu',
              SHOW                    : 'show',
              PARENTS                 : 'menu-item-has-children',
              MCUSTOMSB               : 'mCustomScrollbar',
              ALLOW_POINTER_ON_SCROLL : 'allow-pointer-events-on-scroll'
            };

            this.Selector = {
              DATA_TOGGLE              : '[data-toggle="czr-dropdown"]',
              DATA_SHOWN_TOGGLE_LINK   : '.' +this.ClassName.SHOW+ '> a[data-toggle="czr-dropdown"]',
              HOVER_MENU               : '.czr-open-on-hover',
              CLICK_MENU               : '.czr-open-on-click',// selector used on vertical mobile menus
              HOVER_PARENT             : '.czr-open-on-hover .menu-item-has-children, .nav__woocart',
              CLICK_PARENT             : '.czr-open-on-click .menu-item-has-children',// selector used on vertical mobile menus
              HAS_SUBMENU              : '.menu-item-has-children',
              PARENTS                  : '.tc-header .menu-item-has-children',
              SNAKE_PARENTS            : '.regular-nav .menu-item-has-children',
              VERTICAL_NAV_ONCLICK     : '.czr-open-on-click .vertical-nav',
            };
    },












    //Handle dropdown on hover via js
    //TODO: find a way to unify this with czrDropdown
    dropdownMenuOnHover : function() {
            var _dropdown_selector = this.Selector.HOVER_PARENT,
                self               = this;

            enableDropdownOnHover();

            function _addOpenClass( evt ) {
              var $_el = $(this);

              //a little delay to balance the one added in removing the open class
              var _debounced_addOpenClass = _.debounce( function() {
                //do nothing if menu is mobile
                if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
                  return false;

                if ( !$_el.hasClass(self.ClassName.SHOW) ) {
                      czrapp.$_body.addClass( self.ClassName.ALLOW_POINTER_ON_SCROLL );
                      // april 2020 => some actions should be only done when not on a "touch" device
                      // otherwise we have a bug on submenu expansion
                      // see : https://github.com/presscustomizr/customizr/issues/1824
                      if ( !czrapp.$_body.hasClass('is-touch-device') ) {
                            $_el.trigger( self.Event.SHOW )
                                .addClass(self.ClassName.SHOW)
                                .trigger(self.Event.SHOWN);
                      }
                      var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

                      if ( $_data_toggle.length ) {
                          $_data_toggle[0].setAttribute('aria-expanded', 'true');
                      }
                }

              }, 30);// april 2020 => this delay is important because when on touch device, the "is-touch-device" class must be added before this function is fired

              _debounced_addOpenClass();
            }

            function _removeOpenClass () {

              var $_el = $(this);

              //a little delay before closing to avoid closing a parent before accessing the child
              var _debounced_removeOpenClass = _.debounce( function() {
                if ( $_el.find("ul li:hover").length < 1 && ! $_el.closest('ul').find('li:hover').is( $_el ) ) {
                      // april 2020 => some actions should be only done when not on a "touch" device
                      // otherwise we have a bug on submenu expansion
                      // see : https://github.com/presscustomizr/customizr/issues/1824
                      if ( !czrapp.$_body.hasClass('is-touch-device') ) {
                            $_el.trigger( self.Event.HIDE )
                                .removeClass(self.ClassName.SHOW)
                                .trigger( self.Event.HIDDEN );
                      }

                      //make sure pointer events on scroll are still allowed if there's at least one submenu opened
                      if ( $_el.closest( self.Selector.HOVER_MENU ).find( '.' + self.ClassName.SHOW ).length < 1 ) {
                        czrapp.$_body.removeClass( self.ClassName.ALLOW_POINTER_ON_SCROLL );
                      }

                      var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

                      if ( $_data_toggle.length ) {
                          $_data_toggle[0].setAttribute('aria-expanded', 'false');
                      }
                }

              }, 30);// april 2020 => this delay is important because when on touch device, the "is-touch-device" class must be added before this function is fired

              _debounced_removeOpenClass();
            }

            function enableDropdownOnHover() {
                  // april 2020 : is-touch-device class is added on body on the first touch
                  // This way, we can prevent the problem reported on https://github.com/presscustomizr/customizr/issues/1824
                  // ( two touches needed to reveal submenus on touch devices )
                  czrapp.$_body.on('touchstart', function() {
                        if ( !$(this).hasClass('is-touch-device') ) {
                              $(this).addClass('is-touch-device');
                        }
                  });
                  czrapp.$_body.on( 'mouseenter', _dropdown_selector, _addOpenClass );
                  czrapp.$_body.on( 'mouseleave', _dropdown_selector , _removeOpenClass );
            }

            // function disableDropdownOnHover() {
            //   czrapp.$_body.off( 'mouseenter', _dropdown_selector, _addOpenClass );
            //   czrapp.$_body.off( 'mouseleave', _dropdown_selector , _removeOpenClass );
            // }

    },//dropdownMenuOnHover









    dropdownOpenGoToLinkOnClick : function() {
          var self = this;

          //go to the link if submenu is already opened
          //This happens before the closing occurs when dropdown on click and the dropdown on hover (see the debounce in this case)
          czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE_LINK, function(evt) {
                var $_el = $(this);

                //do nothing if menu is mobile
                if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
                  return false;

                evt.preventDefault();

                var _href = $_el.attr( 'href' );

                if ( _href && '#' != _href ) {
                  window.location = _href;
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
                              $( self.Selector.SNAKE_PARENTS+'.'+self.ClassName.SHOW)
                                  .trigger(self.Event.PLACE_ME);
                              doingAnimation = false;
                            });
                      }

              });

          czrapp.$_body
              .on( this.Event.PLACE_ALL, function() {
                          //trigger a placement on all
                          $( self.Selector.SNAKE_PARENTS )
                              .trigger(self.Event.PLACE_ME);
              })
              //snake bound on menu-item shown and place
              .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.SNAKE_PARENTS, function(evt) {
                evt.stopPropagation();
                _do_snake( $(this), evt );
              });


          //snake
          //$_el is the menu item with children whose submenu will be 'snaked'
          function _do_snake( $_el, evt ) {

            if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) )
              return;

            var $_this       = $_el,
                $_dropdown   = $_this.children( '.'+self.ClassName.DROPDOWN );

            if ( !$_dropdown.length )
              return;

            //stage
            /*
            * we display the dropdown so that jQuery is able to retrieve exact size and positioning
            * we also hide whatever overflows the menu item with children whose submenu will be 'snaked'
            * this to avoid some glitches that would made it lose the focus:
            * During RTL testing when a menu item with children reached the left edge of the window
            * it happened that while the submenu was showing (because of the show class added, so not depending on the snake)
            * this submenu (ul) stole the focus and then released it in a very short time making the mouseleave callback
            * defined in dropdownMenuOnHover react, hence closing the whole submenu tree.
            * This might be a false positive, as we don't really test RTL with RTL browsers (only the html direction changes),
            * but since the 'cure' has no side effects, let's be pedantic!
            */
            $_el.css( 'overflow', 'hidden' );
            $_dropdown.css( {
              'zIndex'  : '-100',
              'display' : 'block'
            });

            _maybe_move( $_dropdown, $_el );

            //unstage
            $_dropdown.css({
              'zIndex'  : '',
              'display' : ''
            });
            $_el.css( 'overflow', '' );
          }


          function _maybe_move( $_dropdown, $_el ) {
              var Direction          = czrapp.isRTL ? {
                        //when in RTL we open the submenu by default on the left side
                        _DEFAULT          : 'left',
                        _OPPOSITE         : 'right'
                  } : {
                        //when in LTR we open the submenu by default on the right side
                        _DEFAULT          : 'right',
                        _OPPOSITE         : 'left'
                  },
                  ClassName          = {
                        OPEN_PREFIX       : 'open-',
                        DD_SUBMENU        : 'czr-dropdown-submenu',
                        CARET_TITLE_FLIP  : 'flex-row-reverse',
                        CARET             : 'caret__dropdown-toggler'
                  },
                  _caret_title_maybe_flip = function( $_el, _direction, _old_direction ) {
                        $.each( $_el, function() {
                            var $_el               = $(this),
                                $_a                = $_el.find( self.Selector.DATA_TOGGLE ).first(),
                                $_caret            = $_el.find( '.' + ClassName.CARET).first();

                            //caret flip
                            if ( 1 == $_caret.length ) {
                                  //tell the caret that the dropdown will open on the _direction (hence remove the old direction class)
                                  $_caret.removeClass( ClassName.OPEN_PREFIX + _old_direction ).addClass( ClassName.OPEN_PREFIX + _direction );
                                  if ( 1 == $_a.length ) {
                                        $_a.toggleClass( ClassName.CARET_TITLE_FLIP, _direction == Direction._OPPOSITE  );
                                  }
                            }
                        });
                  },
                  _setOpenDirection       = function( _direction ) {
                        //retrieve the old direction => used to remove the old direction class
                        var _old_direction = _direction == Direction._OPPOSITE ? Direction._DEFAULT : Direction._OPPOSITE;

                        //tell the dropdown to open on the direction _direction (hence remove the old direction class)
                        $_dropdown.removeClass( ClassName.OPEN_PREFIX + _old_direction ).addClass( ClassName.OPEN_PREFIX + _direction );

                        if ( $_el.hasClass( ClassName.DD_SUBMENU ) ) {
                              _caret_title_maybe_flip( $_el, _direction, _old_direction );
                              //make the first level submenus caret inherit this
                              _caret_title_maybe_flip( $_dropdown.children( '.' + ClassName.DD_SUBMENU ), _direction, _old_direction );
                        }
                  };

              //snake inheritance
              if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( ClassName.OPEN_PREFIX + Direction._OPPOSITE ) ) {
                    //open on the opposite direction
                    _setOpenDirection( Direction._OPPOSITE );
              } else {
                    //open on the default direction
                    _setOpenDirection( Direction._DEFAULT );
              }

              //let's compute on which side open the dropdown
              if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {
                    //open on the left
                    _setOpenDirection( 'left' );
              } else if ( $_dropdown.offset().left < 0 ) {
                    //open on the right
                    _setOpenDirection( 'right' );
              }
          }
    },//dropdownPlacement










    //handle on click submenu expansion for vertical navs (sidenav and mobile menu)
    dropdownOnClickVerticalNav : function() {
        var self = this;
        czrapp.$_body
              // when clicking on a parent menu item whose href is just a "#" or that has no href attribute, let's emulate a click on the caret dropdown
              // selector : .czr-open-on-click .vertical-nav .menu-item-has-children  a
              .on( self.Event.CLICK, [self.Selector.VERTICAL_NAV_ONCLICK, self.Selector.HAS_SUBMENU, 'a'].join(' '), function(evt) {
                    // April 2020 => handle the case when there's no href attribute
                    // see https://github.com/presscustomizr/customizr/issues/1824
                    if ( '#' === $(this).attr('href') || !$(this).attr('href') ) {
                          evt.preventDefault();
                          evt.stopPropagation();
                          $(this).closest( '.nav__link-wrapper' ).children(self.Selector.DATA_TOGGLE).trigger( self.Event.CLICK );
                    }
              })
              //collapse/uncollapse
              .on( self.Event.SHOW +' '+ self.Event.HIDE, self.Selector.VERTICAL_NAV_ONCLICK, function(evt) {
                        $(evt.target).children('.'+self.ClassName.DROPDOWN)
                                        .stop()[ 'show' == evt.type ? 'slideDown' : 'slideUp' ]({
                                              duration : 300,
                                              complete: function() {
                                                    //go to opened on click submenu when mCustomScroll active (sidenav).
                                                    //N.B. at the moment this doesn't work on the mobile menu because we don't apply mcustomscrollbar to it
                                                    if ( 'show' == evt.type ) {
                                                          var $_customScrollbar = $(this).closest(  '.'+self.ClassName.MCUSTOMSB );
                                                          if ( $_customScrollbar.length > 0 ) {
                                                                $_customScrollbar.mCustomScrollbar( 'scrollTo', $(this) );
                                                          }
                                                    }
                                              }
                                        });
              });
    },//dropdownOnClickVerticalNav


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
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY, // 'click.czr.czrDropdown.data-api'
      FOCUSOUT_DATA_API: 'focusout' + EVENT_KEY + DATA_API_KEY,
      FOCUSIN_DATA_API: 'focusin' + EVENT_KEY + DATA_API_KEY,
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
      NAVBAR_NAV: '.regular-nav',
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)',
      PARENTS : '.menu-item-has-children',
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
        czrDropdown.prototype.toggle = function(evt) {
              if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
                return false;
              }

              //KEEP THIS IN CASE WE CHANGE IDEA
              //at the moment vertical navs with already expanded submenus will just never trigger any event that will call this toggle method
              //do nothing if menu is the vertical one
              //if( 'static' == $(this).next( Selector.MENU ).css( 'position' ) )
              //  return true;

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
              $(this).trigger( "focus" );
              this.setAttribute('aria-expanded', 'true');

              $(parent).toggleClass(ClassName.SHOW);
              $(parent).trigger($.Event(Event.SHOWN, relatedTarget));

              return false;
        };//toggle







        czrDropdown.prototype.dispose = function() {
              $.removeData(this._element, DATA_KEY);
              $(this._element).off(EVENT_KEY);
              this._element = null;
        };



        // private
        czrDropdown.prototype._addEventListeners = function() {
              $(this._element).on(Event.CLICK, this.toggle);
        };



        // static
        czrDropdown._jQueryInterface = function(config) {
              return this.each(function () {
                var data = $(this).data(DATA_KEY);

                if (!data) {
                  data = new czrDropdown(this);
                  $(this).data(DATA_KEY, data);
                }

                if (typeof config === 'string') {
                  if ( _.isUndefined( data[config] ) ) {
                    throw new Error('No method named "' + config + '"');
                  }
                  data[config].call(this);
                }
              });
        };



        czrDropdown._clearMenus = function(event, _parentsToNotClear ) {

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



        czrDropdown._getParentFromElement = function(element) {
              var _parentNode = void 0;
              /* get the closest dropdown parent */
              var $_parent = $(element).closest(Selector.PARENTS);

              if ( $_parent.length ) {
                _parentNode = $_parent[0];
              }

              return _parentNode || element.parentNode;
        };



        czrDropdown._dataApiFocusinHandler = function(evt) {
              var self = this;
              _.delay( function() {
                var parent = czrDropdown._getParentFromElement(self),
                    isActive = $(parent).hasClass(ClassName.SHOW);
                if ( ! isActive ) {
                  $(self).trigger('click');
                }
          }, 150); // a little delay so that we avoid a race condition when both focus and click events are triggered on mouse click.
        };



        czrDropdown._dataApiKeydownHandler = function(event) {
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
    /*
                if (event.which === ESCAPE_KEYCODE) {
                  var toggle = $(parent).find(Selector.DATA_TOGGLE)[0];
                  $(toggle).trigger('focus');
                }
    */
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

              items[index].trigger( "focus" );
        };


        _createClass(czrDropdown, null, [{
          key: 'VERSION',
          get: function() {
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
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API + Event.FOCUSOUT_DATA_API , czrDropdown._clearMenus)
        // click.czr.czrDropdown.data-api, [data-toggle="czr-dropdown"]
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle) //click on [data-toggle="czr-dropdown"]
        .on(Event.FOCUSIN_DATA_API, Selector.NAVBAR_NAV + ' ' + Selector.DATA_TOGGLE, czrDropdown._dataApiFocusinHandler)
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