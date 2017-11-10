
var CZRParams = CZRParams || {
  _disabled : [],
  DisabledFeatures : {},
  centerAllImg: 1,
  FancyBoxAutoscale: 1,
  FancyBoxState: 1,
  HasComments: "",
  LoadBootstrap: 1,
  LoadModernizr: 1,
  SliderDelay: +5000,
  SliderHover: 1,
  SliderName: "demo",
  centerSliderImg : 1,
  SmoothScroll: { Enabled : 1 , Options : {} },
  anchorSmoothScroll: "linear",
  anchorSmoothScrollExclude : {
      simple : ['[class*=edd]', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]', '[class*=um-]'],
      deep : { classes : [], ids : [] }
    },
  stickyCustomOffset: { _initial : 0, _scrolling : 0, options : { _static : true, _element : "" } },
  stickyHeader: 1,
  dropdowntoViewport: 1,
  timerOnScrollAllBrowsers:1,
  extLinksStyle :1,
  extLinksTargetExt:1,
  extLinksSkipSelectors: {
    classes : ['btn', 'button'],
    ids:[]
  },
  dropcapEnabled:1,
  dropcapWhere:{ post : 0, page : 1 },
  dropcapMinWords:50,
  dropcapSkipSelectors: {
    tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'],
    classes : ['btn'],
    ids : []
  },
  imgSmartLoadEnabled:0,
  imgSmartLoadOpts: {
    parentSelectors: ['.article-container', '.__before_main_wrapper', '.widget-front'],
    opts : { excludeImg: ['.tc-holder-img'] }
  },
  goldenRatio : 1.618,
  gridGoldenRatioLimit : 350,
  isSecondMenuEnabled : 0,
  secondMenuRespSet : 'in-sn-before'
};// addEventListener Polyfill ie9- http://stackoverflow.com/a/27790212
window.addEventListener = window.addEventListener || function (e, f) { window.attachEvent('on' + e, f); };
if (!Date.now) {
  Date.now = function now() {
    return new Date().getTime();
  };
}
if ( ! Object.create ) {
  Object.create = function(proto, props) {
    if (typeof props !== "undefined") {
      throw "The multiple-argument version of Object.create is not provided by this browser and cannot be shimmed.";
    }
    function ctor() { }

    ctor.prototype = proto;
    return new ctor();
  };
}
if ( ! Array.prototype.filter ) {
  Array.prototype.filter = function(fun/*, thisArg*/) {
    'use strict';

    if (this === void 0 || this === null) {
      throw new TypeError();
    }

    var t = Object(this);
    var len = t.length >>> 0;
    if (typeof fun !== 'function') {
      throw new TypeError();
    }

    var res = [];
    var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
    for (var i = 0; i < len; i++) {
      if (i in t) {
        var val = t[i];
        if (fun.call(thisArg, val, i, t)) {
          res.push(val);
        }
      }
    }

    return res;
  };
}
if (!Array.prototype.map) {

  Array.prototype.map = function(callback, thisArg) {

    var T, A, k;

    if (this == null) {
      throw new TypeError(' this is null or not defined');
    }
    var O = Object(this);
    var len = O.length >>> 0;
    if (typeof callback !== 'function') {
      throw new TypeError(callback + ' is not a function');
    }
    if (arguments.length > 1) {
      T = thisArg;
    }
    A = new Array(len);
    k = 0;
    while (k < len) {

      var kValue, mappedValue;
      if (k in O) {
        kValue = O[k];
        mappedValue = callback.call(T, kValue, k, O);
        A[k] = mappedValue;
      }
      k++;
    }
    return A;
  };
}
/*!
 * Bootstrap v4.0.0-alpha.6 (https://getbootstrap.com)
 * Copyright 2011-2017 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

if (typeof jQuery === 'undefined') {
  throw new Error('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.')
}

+function ($) {
  var version = $.fn.jquery.split(' ')[0].split('.')
  if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] >= 4)) {
    throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0')
  }
}(jQuery);


+function () {

var _typeof2 = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var Util = function ($) {

  var transition = false;

  var MAX_UID = 1000000;

  var TransitionEndEvent = {
    WebkitTransition: 'webkitTransitionEnd',
    MozTransition: 'transitionend',
    OTransition: 'oTransitionEnd otransitionend',
    transition: 'transitionend'
  };
  function toType(obj) {
    return {}.toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase();
  }

  function isElement(obj) {
    return (obj[0] || obj).nodeType;
  }

  function getSpecialTransitionEndEvent() {
    return {
      bindType: transition.end,
      delegateType: transition.end,
      handle: function handle(event) {
        if ($(event.target).is(this)) {
          return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
        }
        return undefined;
      }
    };
  }

  function transitionEndTest() {
    if (window.QUnit) {
      return false;
    }

    var el = document.createElement('bootstrap');

    for (var name in TransitionEndEvent) {
      if (el.style[name] !== undefined) {
        return {
          end: TransitionEndEvent[name]
        };
      }
    }

    return false;
  }

  function transitionEndEmulator(duration) {
    var _this = this;

    var called = false;

    $(this).one(Util.TRANSITION_END, function () {
      called = true;
    });

    setTimeout(function () {
      if (!called) {
        Util.triggerTransitionEnd(_this);
      }
    }, duration);

    return this;
  }

  function setTransitionEndSupport() {
    transition = transitionEndTest();

    $.fn.emulateTransitionEnd = transitionEndEmulator;

    if (Util.supportsTransitionEnd()) {
      $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
    }
  }

  var Util = {

    TRANSITION_END: 'bsTransitionEnd',

    getUID: function getUID(prefix) {
      do {
        prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
      } while (document.getElementById(prefix));
      return prefix;
    },
    getSelectorFromElement: function getSelectorFromElement(element) {
      var selector = element.getAttribute('data-target');

      if (!selector) {
        selector = element.getAttribute('href') || '';
        selector = /^#[a-z]/i.test(selector) ? selector : null;
      }

      return selector;
    },
    reflow: function reflow(element) {
      return element.offsetHeight;
    },
    triggerTransitionEnd: function triggerTransitionEnd(element) {
      $(element).trigger(transition.end);
    },
    supportsTransitionEnd: function supportsTransitionEnd() {
      return Boolean(transition);
    },
    typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
      for (var property in configTypes) {
        if (configTypes.hasOwnProperty(property)) {
          var expectedTypes = configTypes[property];
          var value = config[property];
          var valueType = value && isElement(value) ? 'element' : toType(value);

          if (!new RegExp(expectedTypes).test(valueType)) {
            throw new Error(componentName.toUpperCase() + ': ' + ('Option "' + property + '" provided type "' + valueType + '" ') + ('but expected type "' + expectedTypes + '".'));
          }
        }
      }
    }
  };

  setTransitionEndSupport();

  return Util;
}(jQuery);

var _typeof = typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol" ? function (obj) {
  return typeof obj === 'undefined' ? 'undefined' : _typeof2(obj);
} : function (obj) {
  return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj === 'undefined' ? 'undefined' : _typeof2(obj);
};

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

var Collapse = function ($) {

  var NAME = 'collapse';
  var VERSION = '4.0.0-alpha.6';
  var DATA_KEY = 'bs.collapse';
  var EVENT_KEY = '.' + DATA_KEY;
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = $.fn[NAME];
  var TRANSITION_DURATION = 600;

  var Default = {
    toggle: true,
    parent: ''
  };

  var DefaultType = {
    toggle: 'boolean',
    parent: 'string'
  };

  var Event = {
    SHOW: 'show' + EVENT_KEY,
    SHOWN: 'shown' + EVENT_KEY,
    HIDE: 'hide' + EVENT_KEY,
    HIDDEN: 'hidden' + EVENT_KEY,
    CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
  };

  var ClassName = {
    SHOW: 'show',
    COLLAPSE: 'collapse',
    COLLAPSING: 'collapsing',
    COLLAPSED: 'collapsed'
  };

  var Dimension = {
    WIDTH: 'width',
    HEIGHT: 'height'
  };

  var Selector = {
    ACTIVES: '.card > .show, .card > .collapsing',
    DATA_TOGGLE: '[data-toggle="collapse"]'
  };

  var Collapse = function () {
    function Collapse(element, config) {
      _classCallCheck(this, Collapse);

      this._isTransitioning = false;
      this._element = element;
      this._config = this._getConfig(config);
      this._triggerArray = $.makeArray($('[data-toggle="collapse"][href="#' + element.id + '"],' + ('[data-toggle="collapse"][data-target="#' + element.id + '"]')));

      this._parent = this._config.parent ? this._getParent() : null;

      if (!this._config.parent) {
        this._addAriaAndCollapsedClass(this._element, this._triggerArray);
      }

      if (this._config.toggle) {
        this.toggle();
      }
    }

    Collapse.prototype.toggle = function toggle() {
      if ($(this._element).hasClass(ClassName.SHOW)) {
        this.hide();
      } else {
        this.show();
      }
    };

    Collapse.prototype.show = function show() {
      var _this = this;

      if ($(this._element).hasClass(ClassName.SHOW)) {
        return;
      }

      var actives = void 0;
      var activesData = void 0;

      if (this._parent) {
        actives = $.makeArray($(this._parent).find(Selector.ACTIVES));
        if (!actives.length) {
          actives = null;
        }
      }

      if (actives) {
        activesData = $(actives).data(DATA_KEY);
        if (activesData && activesData._isTransitioning) {
          return;
        }
      }

      var startEvent = $.Event(Event.SHOW);
      $(this._element).trigger(startEvent);
      if (startEvent.isDefaultPrevented()) {
        return;
      }

      if (actives) {
        Collapse._jQueryInterface.call($(actives), 'hide');
        if (!activesData) {
          $(actives).data(DATA_KEY, null);
        }
      }

      var dimension = this._getDimension();

      $(this._element).removeClass(ClassName.COLLAPSE).addClass(ClassName.COLLAPSING);

      this._element.style[dimension] = 0;
      this._element.setAttribute('aria-expanded', true);

      if (this._triggerArray.length) {
        $(this._triggerArray).removeClass(ClassName.COLLAPSED).attr('aria-expanded', true);
      }

      this.setTransitioning(true);

      var complete = function complete() {
        $(_this._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).addClass(ClassName.SHOW);

        _this._element.style[dimension] = '';

        _this.setTransitioning(false);

        $(_this._element).trigger(Event.SHOWN);
      };

      if (!Util.supportsTransitionEnd()) {
        complete();
        return;
      }

      var capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
      var scrollSize = 'scroll' + capitalizedDimension;

      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);

      this._element.style[dimension] = this._element[scrollSize] + 'px';
    };

    Collapse.prototype.hide = function hide() {
      var _this2 = this;

      if (this._isTransitioning) {
        throw new Error('Collapse is transitioning');
      }

      if (!$(this._element).hasClass(ClassName.SHOW)) {
        return;
      }

      var startEvent = $.Event(Event.HIDE);
      $(this._element).trigger(startEvent);
      if (startEvent.isDefaultPrevented()) {
        return;
      }

      var dimension = this._getDimension();
      var offsetDimension = dimension === Dimension.WIDTH ? 'offsetWidth' : 'offsetHeight';

      this._element.style[dimension] = this._element[offsetDimension] + 'px';

      Util.reflow(this._element);

      $(this._element).addClass(ClassName.COLLAPSING).removeClass(ClassName.COLLAPSE).removeClass(ClassName.SHOW);

      this._element.setAttribute('aria-expanded', false);

      if (this._triggerArray.length) {
        $(this._triggerArray).addClass(ClassName.COLLAPSED).attr('aria-expanded', false);
      }

      this.setTransitioning(true);

      var complete = function complete() {
        _this2.setTransitioning(false);
        $(_this2._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).trigger(Event.HIDDEN);
      };

      this._element.style[dimension] = '';

      if (!Util.supportsTransitionEnd()) {
        complete();
        return;
      }

      $(this._element).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
    };

    Collapse.prototype.setTransitioning = function setTransitioning(isTransitioning) {
      this._isTransitioning = isTransitioning;
    };

    Collapse.prototype.dispose = function dispose() {
      $.removeData(this._element, DATA_KEY);

      this._config = null;
      this._parent = null;
      this._element = null;
      this._triggerArray = null;
      this._isTransitioning = null;
    };

    Collapse.prototype._getConfig = function _getConfig(config) {
      config = $.extend({}, Default, config);
      config.toggle = Boolean(config.toggle); // coerce string values
      Util.typeCheckConfig(NAME, config, DefaultType);
      return config;
    };

    Collapse.prototype._getDimension = function _getDimension() {
      var hasWidth = $(this._element).hasClass(Dimension.WIDTH);
      return hasWidth ? Dimension.WIDTH : Dimension.HEIGHT;
    };

    Collapse.prototype._getParent = function _getParent() {
      var _this3 = this;

      var parent = $(this._config.parent)[0];
      var selector = '[data-toggle="collapse"][data-parent="' + this._config.parent + '"]';

      $(parent).find(selector).each(function (i, element) {
        _this3._addAriaAndCollapsedClass(Collapse._getTargetFromElement(element), [element]);
      });

      return parent;
    };

    Collapse.prototype._addAriaAndCollapsedClass = function _addAriaAndCollapsedClass(element, triggerArray) {
      if (element) {
        var isOpen = $(element).hasClass(ClassName.SHOW);
        element.setAttribute('aria-expanded', isOpen);

        if (triggerArray.length) {
          $(triggerArray).toggleClass(ClassName.COLLAPSED, !isOpen).attr('aria-expanded', isOpen);
        }
      }
    };

    Collapse._getTargetFromElement = function _getTargetFromElement(element) {
      var selector = Util.getSelectorFromElement(element);
      return selector ? $(selector)[0] : null;
    };

    Collapse._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY);
        var _config = $.extend({}, Default, $this.data(), (typeof config === 'undefined' ? 'undefined' : _typeof(config)) === 'object' && config);

        if (!data && _config.toggle && /show|hide/.test(config)) {
          _config.toggle = false;
        }

        if (!data) {
          data = new Collapse(this, _config);
          $this.data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (data[config] === undefined) {
            throw new Error('No method named "' + config + '"');
          }
          data[config]();
        }
      });
    };

    _createClass(Collapse, null, [{
      key: 'VERSION',
      get: function get() {
        return VERSION;
      }
    }, {
      key: 'Default',
      get: function get() {
        return Default;
      }
    }]);

    return Collapse;
  }();

  $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
    event.preventDefault();

    var target = Collapse._getTargetFromElement(this);
    var data = $(target).data(DATA_KEY);
    var config = data ? 'toggle' : $(this).data();

    Collapse._jQueryInterface.call($(target), config);
  });

  $.fn[NAME] = Collapse._jQueryInterface;
  $.fn[NAME].Constructor = Collapse;
  $.fn[NAME].noConflict = function () {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Collapse._jQueryInterface;
  };

  return Collapse;
}(jQuery);

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

var Tab = function ($) {

  var NAME = 'tab';
  var VERSION = '4.0.0-alpha.6';
  var DATA_KEY = 'bs.tab';
  var EVENT_KEY = '.' + DATA_KEY;
  var DATA_API_KEY = '.data-api';
  var JQUERY_NO_CONFLICT = $.fn[NAME];
  var TRANSITION_DURATION = 150;

  var Event = {
    HIDE: 'hide' + EVENT_KEY,
    HIDDEN: 'hidden' + EVENT_KEY,
    SHOW: 'show' + EVENT_KEY,
    SHOWN: 'shown' + EVENT_KEY,
    CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
  };

  var ClassName = {
    DROPDOWN_MENU: 'dropdown-menu',
    ACTIVE: 'active',
    DISABLED: 'disabled',
    FADE: 'fade',
    SHOW: 'show'
  };

  var Selector = {
    A: 'a',
    LI: 'li',
    DROPDOWN: '.dropdown',
    LIST: 'ul:not(.dropdown-menu), ol:not(.dropdown-menu), nav:not(.dropdown-menu)',
    FADE_CHILD: '> .nav-item .fade, > .fade',
    ACTIVE: '.active',
    ACTIVE_CHILD: '> .nav-item > .active, > .active',
    DATA_TOGGLE: '[data-toggle="tab"], [data-toggle="pill"]',
    DROPDOWN_TOGGLE: '.dropdown-toggle',
    DROPDOWN_ACTIVE_CHILD: '> .dropdown-menu .active'
  };

  var Tab = function () {
    function Tab(element) {
      _classCallCheck(this, Tab);

      this._element = element;
    }

    Tab.prototype.show = function show() {
      var _this = this;

      if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && $(this._element).hasClass(ClassName.ACTIVE) || $(this._element).hasClass(ClassName.DISABLED)) {
        return;
      }

      var target = void 0;
      var previous = void 0;
      var listElement = $(this._element).closest(Selector.LIST)[0];
      var selector = Util.getSelectorFromElement(this._element);

      if (listElement) {
        previous = $.makeArray($(listElement).find(Selector.ACTIVE));
        previous = previous[previous.length - 1];
      }

      var hideEvent = $.Event(Event.HIDE, {
        relatedTarget: this._element
      });

      var showEvent = $.Event(Event.SHOW, {
        relatedTarget: previous
      });

      if (previous) {
        $(previous).trigger(hideEvent);
      }

      $(this._element).trigger(showEvent);

      if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
        return;
      }

      if (selector) {
        target = $(selector)[0];
      }

      this._activate(this._element, listElement);

      var complete = function complete() {
        var hiddenEvent = $.Event(Event.HIDDEN, {
          relatedTarget: _this._element
        });

        var shownEvent = $.Event(Event.SHOWN, {
          relatedTarget: previous
        });

        $(previous).trigger(hiddenEvent);
        $(_this._element).trigger(shownEvent);
      };

      if (target) {
        this._activate(target, target.parentNode, complete);
      } else {
        complete();
      }
    };

    Tab.prototype.dispose = function dispose() {
      $.removeClass(this._element, DATA_KEY);
      this._element = null;
    };

    Tab.prototype._activate = function _activate(element, container, callback) {
      var _this2 = this;

      var active = $(container).find(Selector.ACTIVE_CHILD)[0];
      var isTransitioning = callback && Util.supportsTransitionEnd() && (active && $(active).hasClass(ClassName.FADE) || Boolean($(container).find(Selector.FADE_CHILD)[0]));

      var complete = function complete() {
        return _this2._transitionComplete(element, active, isTransitioning, callback);
      };

      if (active && isTransitioning) {
        $(active).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
      } else {
        complete();
      }

      if (active) {
        $(active).removeClass(ClassName.SHOW);
      }
    };

    Tab.prototype._transitionComplete = function _transitionComplete(element, active, isTransitioning, callback) {
      if (active) {
        $(active).removeClass(ClassName.ACTIVE);

        var dropdownChild = $(active.parentNode).find(Selector.DROPDOWN_ACTIVE_CHILD)[0];

        if (dropdownChild) {
          $(dropdownChild).removeClass(ClassName.ACTIVE);
        }

        active.setAttribute('aria-expanded', false);
      }

      $(element).addClass(ClassName.ACTIVE);
      element.setAttribute('aria-expanded', true);

      if (isTransitioning) {
        Util.reflow(element);
        $(element).addClass(ClassName.SHOW);
      } else {
        $(element).removeClass(ClassName.FADE);
      }

      if (element.parentNode && $(element.parentNode).hasClass(ClassName.DROPDOWN_MENU)) {

        var dropdownElement = $(element).closest(Selector.DROPDOWN)[0];
        if (dropdownElement) {
          $(dropdownElement).find(Selector.DROPDOWN_TOGGLE).addClass(ClassName.ACTIVE);
        }

        element.setAttribute('aria-expanded', true);
      }

      if (callback) {
        callback();
      }
    };

    Tab._jQueryInterface = function _jQueryInterface(config) {
      return this.each(function () {
        var $this = $(this);
        var data = $this.data(DATA_KEY);

        if (!data) {
          data = new Tab(this);
          $this.data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (data[config] === undefined) {
            throw new Error('No method named "' + config + '"');
          }
          data[config]();
        }
      });
    };

    _createClass(Tab, null, [{
      key: 'VERSION',
      get: function get() {
        return VERSION;
      }
    }]);

    return Tab;
  }();

  $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
    event.preventDefault();
    Tab._jQueryInterface.call($(this), 'show');
  });

  $.fn[NAME] = Tab._jQueryInterface;
  $.fn[NAME].Constructor = Tab;
  $.fn[NAME].noConflict = function () {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Tab._jQueryInterface;
  };

  return Tab;
}(jQuery);

}();
(function(){function n(n){function t(t,r,e,u,i,o){for(;i>=0&&o>i;i+=n){var a=u?u[i]:i;e=r(e,t[a],a,t)}return e}return function(r,e,u,i){e=b(e,i,4);var o=!k(r)&&m.keys(r),a=(o||r).length,c=n>0?0:a-1;return arguments.length<3&&(u=r[o?o[c]:c],c+=n),t(r,e,u,o,c,a)}}function t(n){return function(t,r,e){r=x(r,e);for(var u=O(t),i=n>0?0:u-1;i>=0&&u>i;i+=n)if(r(t[i],i,t))return i;return-1}}function r(n,t,r){return function(e,u,i){var o=0,a=O(e);if("number"==typeof i)n>0?o=i>=0?i:Math.max(i+a,o):a=i>=0?Math.min(i+1,a):i+a+1;else if(r&&i&&a)return i=r(e,u),e[i]===u?i:-1;if(u!==u)return i=t(l.call(e,o,a),m.isNaN),i>=0?i+o:-1;for(i=n>0?o:a-1;i>=0&&a>i;i+=n)if(e[i]===u)return i;return-1}}function e(n,t){var r=I.length,e=n.constructor,u=m.isFunction(e)&&e.prototype||a,i="constructor";for(m.has(n,i)&&!m.contains(t,i)&&t.push(i);r--;)i=I[r],i in n&&n[i]!==u[i]&&!m.contains(t,i)&&t.push(i)}var u=this,i=u._,o=Array.prototype,a=Object.prototype,c=Function.prototype,f=o.push,l=o.slice,s=a.toString,p=a.hasOwnProperty,h=Array.isArray,v=Object.keys,g=c.bind,y=Object.create,d=function(){},m=function(n){return n instanceof m?n:this instanceof m?void(this._wrapped=n):new m(n)};"undefined"!=typeof exports?("undefined"!=typeof module&&module.exports&&(exports=module.exports=m),exports._=m):u._=m,m.VERSION="1.8.3";var b=function(n,t,r){if(t===void 0)return n;switch(null==r?3:r){case 1:return function(r){return n.call(t,r)};case 2:return function(r,e){return n.call(t,r,e)};case 3:return function(r,e,u){return n.call(t,r,e,u)};case 4:return function(r,e,u,i){return n.call(t,r,e,u,i)}}return function(){return n.apply(t,arguments)}},x=function(n,t,r){return null==n?m.identity:m.isFunction(n)?b(n,t,r):m.isObject(n)?m.matcher(n):m.property(n)};m.iteratee=function(n,t){return x(n,t,1/0)};var _=function(n,t){return function(r){var e=arguments.length;if(2>e||null==r)return r;for(var u=1;e>u;u++)for(var i=arguments[u],o=n(i),a=o.length,c=0;a>c;c++){var f=o[c];t&&r[f]!==void 0||(r[f]=i[f])}return r}},j=function(n){if(!m.isObject(n))return{};if(y)return y(n);d.prototype=n;var t=new d;return d.prototype=null,t},w=function(n){return function(t){return null==t?void 0:t[n]}},A=Math.pow(2,53)-1,O=w("length"),k=function(n){var t=O(n);return"number"==typeof t&&t>=0&&A>=t};m.each=m.forEach=function(n,t,r){t=b(t,r);var e,u;if(k(n))for(e=0,u=n.length;u>e;e++)t(n[e],e,n);else{var i=m.keys(n);for(e=0,u=i.length;u>e;e++)t(n[i[e]],i[e],n)}return n},m.map=m.collect=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=Array(u),o=0;u>o;o++){var a=e?e[o]:o;i[o]=t(n[a],a,n)}return i},m.reduce=m.foldl=m.inject=n(1),m.reduceRight=m.foldr=n(-1),m.find=m.detect=function(n,t,r){var e;return e=k(n)?m.findIndex(n,t,r):m.findKey(n,t,r),e!==void 0&&e!==-1?n[e]:void 0},m.filter=m.select=function(n,t,r){var e=[];return t=x(t,r),m.each(n,function(n,r,u){t(n,r,u)&&e.push(n)}),e},m.reject=function(n,t,r){return m.filter(n,m.negate(x(t)),r)},m.every=m.all=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=0;u>i;i++){var o=e?e[i]:i;if(!t(n[o],o,n))return!1}return!0},m.some=m.any=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=0;u>i;i++){var o=e?e[i]:i;if(t(n[o],o,n))return!0}return!1},m.contains=m.includes=m.include=function(n,t,r,e){return k(n)||(n=m.values(n)),("number"!=typeof r||e)&&(r=0),m.indexOf(n,t,r)>=0},m.invoke=function(n,t){var r=l.call(arguments,2),e=m.isFunction(t);return m.map(n,function(n){var u=e?t:n[t];return null==u?u:u.apply(n,r)})},m.pluck=function(n,t){return m.map(n,m.property(t))},m.where=function(n,t){return m.filter(n,m.matcher(t))},m.findWhere=function(n,t){return m.find(n,m.matcher(t))},m.max=function(n,t,r){var e,u,i=-1/0,o=-1/0;if(null==t&&null!=n){n=k(n)?n:m.values(n);for(var a=0,c=n.length;c>a;a++)e=n[a],e>i&&(i=e)}else t=x(t,r),m.each(n,function(n,r,e){u=t(n,r,e),(u>o||u===-1/0&&i===-1/0)&&(i=n,o=u)});return i},m.min=function(n,t,r){var e,u,i=1/0,o=1/0;if(null==t&&null!=n){n=k(n)?n:m.values(n);for(var a=0,c=n.length;c>a;a++)e=n[a],i>e&&(i=e)}else t=x(t,r),m.each(n,function(n,r,e){u=t(n,r,e),(o>u||1/0===u&&1/0===i)&&(i=n,o=u)});return i},m.shuffle=function(n){for(var t,r=k(n)?n:m.values(n),e=r.length,u=Array(e),i=0;e>i;i++)t=m.random(0,i),t!==i&&(u[i]=u[t]),u[t]=r[i];return u},m.sample=function(n,t,r){return null==t||r?(k(n)||(n=m.values(n)),n[m.random(n.length-1)]):m.shuffle(n).slice(0,Math.max(0,t))},m.sortBy=function(n,t,r){return t=x(t,r),m.pluck(m.map(n,function(n,r,e){return{value:n,index:r,criteria:t(n,r,e)}}).sort(function(n,t){var r=n.criteria,e=t.criteria;if(r!==e){if(r>e||r===void 0)return 1;if(e>r||e===void 0)return-1}return n.index-t.index}),"value")};var F=function(n){return function(t,r,e){var u={};return r=x(r,e),m.each(t,function(e,i){var o=r(e,i,t);n(u,e,o)}),u}};m.groupBy=F(function(n,t,r){m.has(n,r)?n[r].push(t):n[r]=[t]}),m.indexBy=F(function(n,t,r){n[r]=t}),m.countBy=F(function(n,t,r){m.has(n,r)?n[r]++:n[r]=1}),m.toArray=function(n){return n?m.isArray(n)?l.call(n):k(n)?m.map(n,m.identity):m.values(n):[]},m.size=function(n){return null==n?0:k(n)?n.length:m.keys(n).length},m.partition=function(n,t,r){t=x(t,r);var e=[],u=[];return m.each(n,function(n,r,i){(t(n,r,i)?e:u).push(n)}),[e,u]},m.first=m.head=m.take=function(n,t,r){return null==n?void 0:null==t||r?n[0]:m.initial(n,n.length-t)},m.initial=function(n,t,r){return l.call(n,0,Math.max(0,n.length-(null==t||r?1:t)))},m.last=function(n,t,r){return null==n?void 0:null==t||r?n[n.length-1]:m.rest(n,Math.max(0,n.length-t))},m.rest=m.tail=m.drop=function(n,t,r){return l.call(n,null==t||r?1:t)},m.compact=function(n){return m.filter(n,m.identity)};var S=function(n,t,r,e){for(var u=[],i=0,o=e||0,a=O(n);a>o;o++){var c=n[o];if(k(c)&&(m.isArray(c)||m.isArguments(c))){t||(c=S(c,t,r));var f=0,l=c.length;for(u.length+=l;l>f;)u[i++]=c[f++]}else r||(u[i++]=c)}return u};m.flatten=function(n,t){return S(n,t,!1)},m.without=function(n){return m.difference(n,l.call(arguments,1))},m.uniq=m.unique=function(n,t,r,e){m.isBoolean(t)||(e=r,r=t,t=!1),null!=r&&(r=x(r,e));for(var u=[],i=[],o=0,a=O(n);a>o;o++){var c=n[o],f=r?r(c,o,n):c;t?(o&&i===f||u.push(c),i=f):r?m.contains(i,f)||(i.push(f),u.push(c)):m.contains(u,c)||u.push(c)}return u},m.union=function(){return m.uniq(S(arguments,!0,!0))},m.intersection=function(n){for(var t=[],r=arguments.length,e=0,u=O(n);u>e;e++){var i=n[e];if(!m.contains(t,i)){for(var o=1;r>o&&m.contains(arguments[o],i);o++);o===r&&t.push(i)}}return t},m.difference=function(n){var t=S(arguments,!0,!0,1);return m.filter(n,function(n){return!m.contains(t,n)})},m.zip=function(){return m.unzip(arguments)},m.unzip=function(n){for(var t=n&&m.max(n,O).length||0,r=Array(t),e=0;t>e;e++)r[e]=m.pluck(n,e);return r},m.object=function(n,t){for(var r={},e=0,u=O(n);u>e;e++)t?r[n[e]]=t[e]:r[n[e][0]]=n[e][1];return r},m.findIndex=t(1),m.findLastIndex=t(-1),m.sortedIndex=function(n,t,r,e){r=x(r,e,1);for(var u=r(t),i=0,o=O(n);o>i;){var a=Math.floor((i+o)/2);r(n[a])<u?i=a+1:o=a}return i},m.indexOf=r(1,m.findIndex,m.sortedIndex),m.lastIndexOf=r(-1,m.findLastIndex),m.range=function(n,t,r){null==t&&(t=n||0,n=0),r=r||1;for(var e=Math.max(Math.ceil((t-n)/r),0),u=Array(e),i=0;e>i;i++,n+=r)u[i]=n;return u};var E=function(n,t,r,e,u){if(!(e instanceof t))return n.apply(r,u);var i=j(n.prototype),o=n.apply(i,u);return m.isObject(o)?o:i};m.bind=function(n,t){if(g&&n.bind===g)return g.apply(n,l.call(arguments,1));if(!m.isFunction(n))throw new TypeError("Bind must be called on a function");var r=l.call(arguments,2),e=function(){return E(n,e,t,this,r.concat(l.call(arguments)))};return e},m.partial=function(n){var t=l.call(arguments,1),r=function(){for(var e=0,u=t.length,i=Array(u),o=0;u>o;o++)i[o]=t[o]===m?arguments[e++]:t[o];for(;e<arguments.length;)i.push(arguments[e++]);return E(n,r,this,this,i)};return r},m.bindAll=function(n){var t,r,e=arguments.length;if(1>=e)throw new Error("bindAll must be passed function names");for(t=1;e>t;t++)r=arguments[t],n[r]=m.bind(n[r],n);return n},m.memoize=function(n,t){var r=function(e){var u=r.cache,i=""+(t?t.apply(this,arguments):e);return m.has(u,i)||(u[i]=n.apply(this,arguments)),u[i]};return r.cache={},r},m.delay=function(n,t){var r=l.call(arguments,2);return setTimeout(function(){return n.apply(null,r)},t)},m.defer=m.partial(m.delay,m,1),m.throttle=function(n,t,r){var e,u,i,o=null,a=0;r||(r={});var c=function(){a=r.leading===!1?0:m.now(),o=null,i=n.apply(e,u),o||(e=u=null)};return function(){var f=m.now();a||r.leading!==!1||(a=f);var l=t-(f-a);return e=this,u=arguments,0>=l||l>t?(o&&(clearTimeout(o),o=null),a=f,i=n.apply(e,u),o||(e=u=null)):o||r.trailing===!1||(o=setTimeout(c,l)),i}},m.debounce=function(n,t,r){var e,u,i,o,a,c=function(){var f=m.now()-o;t>f&&f>=0?e=setTimeout(c,t-f):(e=null,r||(a=n.apply(i,u),e||(i=u=null)))};return function(){i=this,u=arguments,o=m.now();var f=r&&!e;return e||(e=setTimeout(c,t)),f&&(a=n.apply(i,u),i=u=null),a}},m.wrap=function(n,t){return m.partial(t,n)},m.negate=function(n){return function(){return!n.apply(this,arguments)}},m.compose=function(){var n=arguments,t=n.length-1;return function(){for(var r=t,e=n[t].apply(this,arguments);r--;)e=n[r].call(this,e);return e}},m.after=function(n,t){return function(){return--n<1?t.apply(this,arguments):void 0}},m.before=function(n,t){var r;return function(){return--n>0&&(r=t.apply(this,arguments)),1>=n&&(t=null),r}},m.once=m.partial(m.before,2);var M=!{toString:null}.propertyIsEnumerable("toString"),I=["valueOf","isPrototypeOf","toString","propertyIsEnumerable","hasOwnProperty","toLocaleString"];m.keys=function(n){if(!m.isObject(n))return[];if(v)return v(n);var t=[];for(var r in n)m.has(n,r)&&t.push(r);return M&&e(n,t),t},m.allKeys=function(n){if(!m.isObject(n))return[];var t=[];for(var r in n)t.push(r);return M&&e(n,t),t},m.values=function(n){for(var t=m.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=n[t[u]];return e},m.mapObject=function(n,t,r){t=x(t,r);for(var e,u=m.keys(n),i=u.length,o={},a=0;i>a;a++)e=u[a],o[e]=t(n[e],e,n);return o},m.pairs=function(n){for(var t=m.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=[t[u],n[t[u]]];return e},m.invert=function(n){for(var t={},r=m.keys(n),e=0,u=r.length;u>e;e++)t[n[r[e]]]=r[e];return t},m.functions=m.methods=function(n){var t=[];for(var r in n)m.isFunction(n[r])&&t.push(r);return t.sort()},m.extend=_(m.allKeys),m.extendOwn=m.assign=_(m.keys),m.findKey=function(n,t,r){t=x(t,r);for(var e,u=m.keys(n),i=0,o=u.length;o>i;i++)if(e=u[i],t(n[e],e,n))return e},m.pick=function(n,t,r){var e,u,i={},o=n;if(null==o)return i;m.isFunction(t)?(u=m.allKeys(o),e=b(t,r)):(u=S(arguments,!1,!1,1),e=function(n,t,r){return t in r},o=Object(o));for(var a=0,c=u.length;c>a;a++){var f=u[a],l=o[f];e(l,f,o)&&(i[f]=l)}return i},m.omit=function(n,t,r){if(m.isFunction(t))t=m.negate(t);else{var e=m.map(S(arguments,!1,!1,1),String);t=function(n,t){return!m.contains(e,t)}}return m.pick(n,t,r)},m.defaults=_(m.allKeys,!0),m.create=function(n,t){var r=j(n);return t&&m.extendOwn(r,t),r},m.clone=function(n){return m.isObject(n)?m.isArray(n)?n.slice():m.extend({},n):n},m.tap=function(n,t){return t(n),n},m.isMatch=function(n,t){var r=m.keys(t),e=r.length;if(null==n)return!e;for(var u=Object(n),i=0;e>i;i++){var o=r[i];if(t[o]!==u[o]||!(o in u))return!1}return!0};var N=function(n,t,r,e){if(n===t)return 0!==n||1/n===1/t;if(null==n||null==t)return n===t;n instanceof m&&(n=n._wrapped),t instanceof m&&(t=t._wrapped);var u=s.call(n);if(u!==s.call(t))return!1;switch(u){case"[object RegExp]":case"[object String]":return""+n==""+t;case"[object Number]":return+n!==+n?+t!==+t:0===+n?1/+n===1/t:+n===+t;case"[object Date]":case"[object Boolean]":return+n===+t}var i="[object Array]"===u;if(!i){if("object"!=typeof n||"object"!=typeof t)return!1;var o=n.constructor,a=t.constructor;if(o!==a&&!(m.isFunction(o)&&o instanceof o&&m.isFunction(a)&&a instanceof a)&&"constructor"in n&&"constructor"in t)return!1}r=r||[],e=e||[];for(var c=r.length;c--;)if(r[c]===n)return e[c]===t;if(r.push(n),e.push(t),i){if(c=n.length,c!==t.length)return!1;for(;c--;)if(!N(n[c],t[c],r,e))return!1}else{var f,l=m.keys(n);if(c=l.length,m.keys(t).length!==c)return!1;for(;c--;)if(f=l[c],!m.has(t,f)||!N(n[f],t[f],r,e))return!1}return r.pop(),e.pop(),!0};m.isEqual=function(n,t){return N(n,t)},m.isEmpty=function(n){return null==n?!0:k(n)&&(m.isArray(n)||m.isString(n)||m.isArguments(n))?0===n.length:0===m.keys(n).length},m.isElement=function(n){return!(!n||1!==n.nodeType)},m.isArray=h||function(n){return"[object Array]"===s.call(n)},m.isObject=function(n){var t=typeof n;return"function"===t||"object"===t&&!!n},m.each(["Arguments","Function","String","Number","Date","RegExp","Error"],function(n){m["is"+n]=function(t){return s.call(t)==="[object "+n+"]"}}),m.isArguments(arguments)||(m.isArguments=function(n){return m.has(n,"callee")}),"function"!=typeof/./&&"object"!=typeof Int8Array&&(m.isFunction=function(n){return"function"==typeof n||!1}),m.isFinite=function(n){return isFinite(n)&&!isNaN(parseFloat(n))},m.isNaN=function(n){return m.isNumber(n)&&n!==+n},m.isBoolean=function(n){return n===!0||n===!1||"[object Boolean]"===s.call(n)},m.isNull=function(n){return null===n},m.isUndefined=function(n){return n===void 0},m.has=function(n,t){return null!=n&&p.call(n,t)},m.noConflict=function(){return u._=i,this},m.identity=function(n){return n},m.constant=function(n){return function(){return n}},m.noop=function(){},m.property=w,m.propertyOf=function(n){return null==n?function(){}:function(t){return n[t]}},m.matcher=m.matches=function(n){return n=m.extendOwn({},n),function(t){return m.isMatch(t,n)}},m.times=function(n,t,r){var e=Array(Math.max(0,n));t=b(t,r,1);for(var u=0;n>u;u++)e[u]=t(u);return e},m.random=function(n,t){return null==t&&(t=n,n=0),n+Math.floor(Math.random()*(t-n+1))},m.now=Date.now||function(){return(new Date).getTime()};var B={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},T=m.invert(B),R=function(n){var t=function(t){return n[t]},r="(?:"+m.keys(n).join("|")+")",e=RegExp(r),u=RegExp(r,"g");return function(n){return n=null==n?"":""+n,e.test(n)?n.replace(u,t):n}};m.escape=R(B),m.unescape=R(T),m.result=function(n,t,r){var e=null==n?void 0:n[t];return e===void 0&&(e=r),m.isFunction(e)?e.call(n):e};var q=0;m.uniqueId=function(n){var t=++q+"";return n?n+t:t},m.templateSettings={evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g};var K=/(.)^/,z={"'":"'","\\":"\\","\r":"r","\n":"n","\u2028":"u2028","\u2029":"u2029"},D=/\\|'|\r|\n|\u2028|\u2029/g,L=function(n){return"\\"+z[n]};m.template=function(n,t,r){!t&&r&&(t=r),t=m.defaults({},t,m.templateSettings);var e=RegExp([(t.escape||K).source,(t.interpolate||K).source,(t.evaluate||K).source].join("|")+"|$","g"),u=0,i="__p+='";n.replace(e,function(t,r,e,o,a){return i+=n.slice(u,a).replace(D,L),u=a+t.length,r?i+="'+\n((__t=("+r+"))==null?'':_.escape(__t))+\n'":e?i+="'+\n((__t=("+e+"))==null?'':__t)+\n'":o&&(i+="';\n"+o+"\n__p+='"),t}),i+="';\n",t.variable||(i="with(obj||{}){\n"+i+"}\n"),i="var __t,__p='',__j=Array.prototype.join,"+"print=function(){__p+=__j.call(arguments,'');};\n"+i+"return __p;\n";try{var o=new Function(t.variable||"obj","_",i)}catch(a){throw a.source=i,a}var c=function(n){return o.call(this,n,m)},f=t.variable||"obj";return c.source="function("+f+"){\n"+i+"}",c},m.chain=function(n){var t=m(n);return t._chain=!0,t};var P=function(n,t){return n._chain?m(t).chain():t};m.mixin=function(n){m.each(m.functions(n),function(t){var r=m[t]=n[t];m.prototype[t]=function(){var n=[this._wrapped];return f.apply(n,arguments),P(this,r.apply(m,n))}})},m.mixin(m),m.each(["pop","push","reverse","shift","sort","splice","unshift"],function(n){var t=o[n];m.prototype[n]=function(){var r=this._wrapped;return t.apply(r,arguments),"shift"!==n&&"splice"!==n||0!==r.length||delete r[0],P(this,r)}}),m.each(["concat","join","slice"],function(n){var t=o[n];m.prototype[n]=function(){return P(this,t.apply(this._wrapped,arguments))}}),m.prototype.value=function(){return this._wrapped},m.prototype.valueOf=m.prototype.toJSON=m.prototype.value,m.prototype.toString=function(){return""+this._wrapped},"function"==typeof define&&define.amd&&define("underscore",[],function(){return m})}).call(this);/* ===================================================
 * jqueryimgOriginalSizes.js v1.0.0
 * ===================================================
 * (c) 2015 Nicolas Guillaume, Nice, France - Rocco Aliberti, Salerno, Italy
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * Heavily based on http://www.jacklmoore.com/notes/naturalwidth-and-naturalheight-in-ie/
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Retrieves the original imgs width and height, cross-browser
 *
 * Example usage
 * var imgHeight = $('img#my-img').originalHeight(),
 *     imgWidth  = $('img#my-img').originalWidth()
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {

  var pluginPrefix = 'original',
      _props       = ['Width', 'Height'];

  _props.map( function(_prop) {
    var _lprop = _prop.toLowerCase();
    $.fn[ pluginPrefix + _prop ] = ('natural' + _prop in new Image()) ?
      function () {
        return this[0][ 'natural' + _prop ];
      } :
      function () {
        var _size = _getAttr( this, _lprop );

        if ( _size )
          return _size;

        var _node = this[0],
            _img;

        if (_node.tagName.toLowerCase() === 'img') {
          _img = new Image();
          _img.src = _node.src;
          _size = _img[ _lprop ];
        }
        return _size;
      };
  } );//map()

  function _getAttr( _el, prop ){
    var _img_size = $(_el).attr( prop );
    return ( typeof _img_size === undefined ) ? false : _img_size;
  }

})( jQuery, window, document );
;(function ( $, window, document, undefined ) {
  var pluginName = 'addDropCap',
      defaults = {
          wrapper : ".entry-content",
          minwords : 50,
          skipSelectors : { //defines the selector to skip when parsing the wrapper also if they are children of an element
            tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE'],
            classes : [],
            ids : []
          }
      };

  function Plugin( element, options ) {
    this.element = element;
    this.options = $.extend( {}, defaults, options) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }
  Plugin.prototype.init = function () {
    var $_target = this._get_dropcap_el();
    if ( $_target && this.options.minwords <= this._countWords( $_target.text() ) )
      this._may_be_add_dc( $_target );
  };
  Plugin.prototype._get_dropcap_el = function( _requested_el ) {
    var $_first_el      = _requested_el || $( this.options.wrapper ).find( this.element ).first(),
        _first_el_text  = this._get_real_text( $_first_el.text() );

    if ( ! this._is_authorized( $_first_el ) && $_first_el.next().length )
      return this._get_dropcap_el( $_first_el.next() );
    else if ( this._is_authorized( $_first_el ) && _first_el_text )
      return $_first_el;
    else if ( $_first_el.next().length )
      return this._get_dropcap_el( $_first_el.next() );
    return;
  };
  Plugin.prototype._get_real_text = function( _text ) {
    _text.replace(/&nbsp;/g, '').replace(/ /g, '');
    return this._removeSpecChars( _text );
  };
  Plugin.prototype._is_authorized = function( $_el ) {
    if ( 'function' != typeof(_) )
      return true;
    if ( ! $_el[0] || ! $_el[0].tagName )
      return;
    if ( ! this._is_tag_allowed( $_el ) )
      return;
    if ( ! this._are_children_tag_allowed( $_el ) )
      return;
    if ( ! this._is_selector_allowed( $_el, ['ids' , 'classes'] ) )
      return;

    return true;
  };
  Plugin.prototype._may_be_add_dc = function( $_target ) {
    var _first_el_text    = $_target.text(),
        _first_word       = '',
        _split_text       = $_target.text().replace(/ /g , '&nbsp;').split('&nbsp;');
    if ( ! _.isArray(_split_text) )
      return;
    if ( _split_text.length )
      _first_word = _split_text[0];
    _first_word = this._removeSpecChars( _first_word );

    if ( ! _first_word.charAt(0) )
      return;

    var _first_letter     = _first_word.charAt(0),
        _rest_of_word     = _first_word.substr(1),
        _drop_capped      = '',
        _html             = '';

    _first_letter = ['<span class="tc-dropcap">' , _first_letter, '</span>'].join('');
    _drop_capped = [ _first_letter , _rest_of_word ].join( '' );
    _html = $_target.html().replace( _first_word , _drop_capped );
    $_target.html(_html);
  };
  Plugin.prototype._is_selector_allowed = function( $_el , sel_types ) {
    if ( 'object' != typeof( this.options.skipSelectors ) )
      return true;
    var self = this;
        _filtered = sel_types.filter( function( sel_typ ) { return false === self._is_sel_type_allowed( $_el, sel_typ ); } );
    return 0 === _filtered.length;
  };
  Plugin.prototype._is_sel_type_allowed = function( $_el, sel_typ ) {
    if ( ! this.options.skipSelectors[sel_typ] || ! $.isArray( this.options.skipSelectors[sel_typ] ) )
      return true;

    var _attr = 'ids' == sel_typ ? 'id' : 'class';
    if ( 'object' != typeof(this.options.skipSelectors) || ! this.options.skipSelectors[sel_typ] || ! $.isArray( this.options.skipSelectors[sel_typ] )  )
      return true;

    var _elSels       = ! $_el.attr( _attr ) ? [] : $_el.attr( _attr ).split(' '),
        _selsToSkip   = this.options.skipSelectors[sel_typ],
        _current_filtered     = _elSels.filter( function( name ) { return -1 != $.inArray( name , _selsToSkip ) ;});

    var _pref = 'ids' == sel_typ ? '#' : '.',
        _children_filtered = _selsToSkip.filter( function( name ) {
          return 0 !== $_el.find(_pref + name).length;
        } );

    return 0 === $.merge( _current_filtered , _children_filtered ).length;
  };
  Plugin.prototype._is_tag_allowed = function( $_el ) {
    if ( 'object' != typeof(this.options.skipSelectors) || ! _.isArray( this.options.skipSelectors.tags ) )
      return true;
    return -1 == _.indexOf( _.map( this.options.skipSelectors.tags , function(_tag) { return _tag.toUpperCase(); } ), $_el[0].tagName );
  };
  Plugin.prototype._are_children_tag_allowed = function( $_el ) {
    if ( 'object' != typeof(this.options.skipSelectors) || ! _.isArray( this.options.skipSelectors.tags ) )
      return true;
    if ( 0 === $_el.children().length )
      return true;

    var childTagName  = $_el.children().first()[0].tagName,
        _tagToSkip    = this.options.skipSelectors.tags,
        _filtered     = _tagToSkip.filter( function(_tag) { return 0 !== $_el.find(_tag).length;} );

    return 0 === _filtered.length;
  };
  Plugin.prototype._countWords = function( _expr ) {
    if ( 'string' != typeof( _expr ) )
      return 0;
    _expr = _expr.replace('&nbsp;' , ' ');
    return (_expr.split(' ')).length;
  };
  Plugin.prototype._removeSpecChars = function( _expr , _replaceBy ) {
    _replaceBy = _replaceBy || '';
    return 'string' == typeof(_expr) ? _expr.replace(/[^\w-\?!\u00bf-\u00ff]/g, _replaceBy ) : '';
  };
  Plugin.prototype._stripHtmlTags = function( expr ) {
    return ( expr && 'string' == typeof(expr) ) ? expr.replace(/(<([^>]+)>)/ig,"") : false;
  };
  $.fn[pluginName] = function ( options ) {
      return this.each(function () {
          if (!$.data(this, 'plugin_' + pluginName)) {
              $.data(this, 'plugin_' + pluginName,
              new Plugin( this, options ));
          }
      });
  };
})( jQuery, window, document );
;(function ( $, window, document, undefined ) {
      var pluginName = 'imgSmartLoad',
          defaults = {
                load_all_images_on_first_scroll : false,
                attribute : [ 'data-src', 'data-srcset', 'data-sizes' ],
                excludeImg : [],
                threshold : 200,
                fadeIn_options : { duration : 400 },
                delaySmartLoadEvent : 0,

          },
          skipImgClass = 'tc-smart-load-skip';


      function Plugin( element, options ) {
            this.element = element;
            this.options = $.extend( {}, defaults, options) ;
            if ( _.isArray( this.options.excludeImg ) ) {
                  this.options.excludeImg.push( '.'+skipImgClass );
            } else {
                  this.options.excludeImg = [ '.'+skipImgClass ];
            }

            this._defaults = defaults;
            this._name = pluginName;
            this.init();
      }
      Plugin.prototype.init = function () {
            var self        = this,
                $_imgs   = $( 'img[' + this.options.attribute[0] + ']:not('+ this.options.excludeImg.join() +')' , this.element );

            this.increment  = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
            this.timer      = 0;


            $_imgs
                  .addClass( skipImgClass )
                  .bind( 'load_img', {}, function() {
                        self._load_img(this);
                  });
            $(window).scroll( function( _evt ) { self._better_scroll_event_handler( $_imgs, _evt ); } );
            $(window).resize( _.debounce( function( _evt ) { self._maybe_trigger_load( $_imgs, _evt ); }, 100 ) );
            this._maybe_trigger_load( $_imgs );
      };
      Plugin.prototype._better_scroll_event_handler = function( $_imgs , _evt ) {
            var self = this;
            if ( ! this.doingAnimation ) {
                  this.doingAnimation = true;
                  window.requestAnimationFrame(function() {
                        self._maybe_trigger_load( $_imgs , _evt );
                        self.doingAnimation = false;
                  });
            }
      };
      Plugin.prototype._maybe_trigger_load = function( $_imgs , _evt ) {
            var self = this;
                _visible_list = $_imgs.filter( function( ind, _img ) { return self._is_visible( _img ,  _evt ); } );
            _visible_list.map( function( ind, _img ) {
                  $(_img).trigger( 'load_img' );
            });
      };
      Plugin.prototype._is_visible = function( _img, _evt ) {
            var $_img       = $(_img),
                wt = $(window).scrollTop(),
                wb = wt + $(window).height(),
                it  = $_img.offset().top,
                ib  = it + $_img.height(),
                th = this.options.threshold;
            if ( _evt && 'scroll' == _evt.type && this.options.load_all_images_on_first_scroll )
              return true;

            return ib >= wt - th && it <= wb + th;
      };
      Plugin.prototype._load_img = function( _img ) {
            var $_img    = $(_img),
                _src     = $_img.attr( this.options.attribute[0] ),
                _src_set = $_img.attr( this.options.attribute[1] ),
                _sizes   = $_img.attr( this.options.attribute[2] ),
                self = this;

            $_img.parent().addClass('smart-loading');

            $_img.unbind('load_img')
                  .hide()
                  .removeAttr( this.options.attribute.join(' ') )
                  .attr( 'sizes' , _sizes )
                  .attr( 'srcset' , _src_set )
                  .attr( 'src', _src )
                  .load( function () {
                        if ( ! $_img.hasClass('tc-smart-loaded') ) {
                              $_img.fadeIn(self.options.fadeIn_options).addClass('tc-smart-loaded');
                        }
                        if ( ( 'undefined' !== typeof $_img.attr('data-tcjp-recalc-dims')  ) && ( false !== $_img.attr('data-tcjp-recalc-dims') ) ) {
                              var _width  = $_img.originalWidth();
                                  _height = $_img.originalHeight();

                              if ( 2 != _.size( _.filter( [ _width, _height ], function(num){ return _.isNumber( parseInt(num, 10) ) && num > 1; } ) ) )
                                return;
                              $_img.removeAttr( 'data-tcjp-recalc-dims scale' );

                              $_img.attr( 'width', _width );
                              $_img.attr( 'height', _height );
                        }

                        $_img.trigger('smartload');
                        $_img.data('czr-smart-loaded', true );
                  });//<= create a load() fn
            if ( $_img[0].complete ) {
                  $_img.load();
            }
            $_img.parent().removeClass('smart-loading');
      };
      $.fn[pluginName] = function ( options ) {
            return this.each(function () {
                  if (!$.data(this, 'plugin_' + pluginName)) {
                        $.data(this, 'plugin_' + pluginName,
                        new Plugin( this, options ));
                  }
            });
      };
})( jQuery, window, document );
;(function ( $, window, document, undefined ) {
    var pluginName = 'extLinks',
        defaults = {
          addIcon : true,
          iconClassName : 'tc-external',
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
        this.init();
    }


    Plugin.prototype.init = function() {
      var self = this,
          $_external_icon = this.$_el.next( '.' + self.options.iconClassName );
      if ( ! this._is_eligible() ) {
        if ( $_external_icon.length )
          $_external_icon.remove();
        return;
      }
      if ( this.options.addIcon && 0 === $_external_icon.length ) {
        this.$_el.after('<span class="' + self.options.iconClassName + '">');
      }
      if ( this.options.newTab && '_blank' != this.$_el.attr('target') )
        this.$_el.attr('target' , '_blank');
    };
    Plugin.prototype._is_eligible = function() {
      var self = this;
      if ( ! this._is_external( this._href ) )
        return;
      if ( ! this._is_first_child_tag_allowed () )
        return;
      if ( 2 != ( ['ids', 'classes'].filter( function( sel_type) { return self._is_selector_allowed(sel_type); } ) ).length )
        return;

      var _is_eligible = true;
      $.each( this.$_el.parents(), function() {
        if ( 'underline' == $(this).css('textDecoration') ){
          _is_eligible = false;
          return false;
        }
      });

      return true && _is_eligible;
    };
    Plugin.prototype._is_selector_allowed = function( requested_sel_type ) {
      if ( czrapp && czrapp.userXP && czrapp.userXP.isSelectorAllowed )
        return czrapp.userXP.isSelectorAllowed( this.$_el, this.options.skipSelectors, requested_sel_type);

      var sel_type = 'ids' == requested_sel_type ? 'id' : 'class',
          _selsToSkip   = this.options.skipSelectors[requested_sel_type];
      if ( 'object' != typeof(this.options.skipSelectors) || ! this.options.skipSelectors[requested_sel_type] || ! $.isArray( this.options.skipSelectors[requested_sel_type] ) || 0 === this.options.skipSelectors[requested_sel_type].length )
        return true;
      if ( this.$_el.parents( _selsToSkip.map( function( _sel ){ return 'id' == sel_type ? '#' + _sel : '.' + _sel; } ).join(',') ).length > 0 )
        return false;
      if ( ! this.$_el.attr( sel_type ) )
        return true;

      var _elSels       = this.$_el.attr( sel_type ).split(' '),
          _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});
      return 0 === _filtered.length;
    };
    Plugin.prototype._is_first_child_tag_allowed = function() {
      if ( 0 === this.$_el.children().length )
        return true;

      var tagName     = this.$_el.children().first()[0].tagName,
          _tagToSkip  = this.options.skipChildTags;
      if ( ! $.isArray( _tagToSkip ) )
        return true;
      _tagToSkip = _tagToSkip.map( function( _tag ) { return _tag.toUpperCase(); });
      return -1 == $.inArray( tagName , _tagToSkip );
    };
    Plugin.prototype._is_external = function( _href  ) {
      var _main_domain = (location.host).split('.').slice(-2).join('.'),
          _reg = new RegExp( _main_domain );

      _href = $.trim( _href );

      if ( _href !== '' && _href != '#' && this._isValidURL( _href ) )
        return ! _reg.test( _href );
      return;
    };
    Plugin.prototype._isValidURL = function( _url ){
      var _pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      return _pattern.test( _url );
    };
    $.fn[pluginName] = function ( options ) {
      return this.each(function () {
        if (!$.data(this, 'plugin_' + pluginName)) {
            $.data(this, 'plugin_' + pluginName,
            new Plugin( this, options ));
        }
      });
    };

})( jQuery, window, document );
;(function ( $, window, document, undefined ) {
      var pluginName = 'centerImages',
          defaults = {
                enableCentering : true,
                onresize : true,
                onInit : true,//<= shall we smartload on init or wait for a custom event, typically smartload ?
                oncustom : [],//list of event here
                $containerToListen : null,//<= we might want to listen to custom event trigger to a parent container.Should be a jQuery obj
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
                useImgAttr:false,//uses the img height and width attributes if not visible (typically used for the customizr slider hidden images)
                setOpacityWhenCentered : false,//this can be used to hide the image during the time it is centered
                addCenteredClassWithDelay : 0,//<= a small delay can be required when we rely on the v-centered or h-centered css classes to set the opacity for example
                opacity : 1
          };

      function Plugin( element, options ) {
            var self = this;
            this.container  = element;
            this.options    = $.extend( {}, defaults, options) ;
            this._defaults  = defaults;
            this._name      = pluginName;
            this._customEvt = $.isArray(self.options.oncustom) ? self.options.oncustom : self.options.oncustom.split(' ');
            this.init();
      }
      Plugin.prototype.init = function () {
            var self = this,
                _do = function( _event_ ) {
                    _event_ = _event_ || 'init';
                    self._maybe_apply_golden_r();
                    var $_imgs = $( self.options.imgSel , self.container );
                    if ( self.options.enableGoldenRatio ) {
                          $(window).bind(
                                'resize',
                                {},
                                _.debounce( function( evt ) { self._maybe_apply_golden_r( evt ); }, 200 )
                          );
                    }
                    if ( 1 <= $_imgs.length && self.options.enableCentering ) {
                          self._parse_imgs( $_imgs, _event_ );
                    }
                };
            if ( self.options.onInit ) {
                  _do();
            }
            if ( $.isArray( self._customEvt ) ) {
                  self._customEvt.map( function( evt ) {
                        var $_containerToListen = ( self.options.$containerToListen instanceof $ && 1 < self.options.$containerToListen.length ) ? self.options.$containerToListen : $( self.container );
                        $_containerToListen.bind( evt, {} , function() {
                              _do( evt );
                        });
                  } );
            }
      };
      Plugin.prototype._maybe_apply_golden_r = function( evt ) {
            if ( ! this.options.enableGoldenRatio || ! this.options.goldenRatioVal || 0 === this.options.goldenRatioVal )
              return;
            if ( ! this._is_selector_allowed() )
              return;
            if ( ! this._is_window_width_allowed() ) {
                  $(this.container).attr('style' , '');
                  return;
            }

            var new_height = Math.round( $(this.container).width() / this.options.goldenRatioVal );
            new_height = new_height > this.options.goldenRatioLimitHeightTo ? this.options.goldenRatioLimitHeightTo : new_height;
            $(this.container)
                  .css({
                        'line-height' : new_height + 'px',
                        height : new_height + 'px'
                  })
                  .trigger('golden-ratio-applied');
      };
      Plugin.prototype._is_window_width_allowed = function() {
            return $(window).width() > this.options.disableGRUnder - 15;
      };
      Plugin.prototype._parse_imgs = function( $_imgs, _event_ ) {
            var self = this;
            $_imgs.each(function ( ind, img ) {
                  var $_img = $(img);
                  self._pre_img_cent( $_img, _event_ );
                  if ( self.options.onresize && ! $_img.data('resize-react-bound' ) ) {
                        $_img.data('resize-react-bound', true );
                        $(window).resize( _.debounce( function() {
                              self._pre_img_cent( $_img, 'resize');
                        }, 100 ) );
                  }

            });//$_imgs.each()
            if ( $(self.container).attr('data-img-centered-in-container') ) {
                  var _n = parseInt( $(self.container).attr('data-img-centered-in-container'), 10 ) + 1;
                  $(self.container).attr('data-img-centered-in-container', _n );
            } else {
                  $(self.container).attr('data-img-centered-in-container', 1 );
            }
      };
      Plugin.prototype._pre_img_cent = function( $_img, _event_ ) {

            var _state = this._get_current_state( $_img ),
                self = this,
                _case  = _state.current,
                _p     = _state.prop[_case],
                _not_p = _state.prop[ 'h' == _case ? 'v' : 'h'],
                _not_p_dir_val = 'h' == _case ? ( this.options.zeroTopAdjust || 0 ) : ( this.options.zeroLeftAdjust || 0 );

            var _centerImg = function( $_img ) {
                  $_img
                      .css( _p.dim.name , _p.dim.val )
                      .css( _not_p.dim.name , self.options.defaultCSSVal[ _not_p.dim.name ] || 'auto' )
                      .css( _p.dir.name, _p.dir.val ).css( _not_p.dir.name, _not_p_dir_val );

                  if ( 0 !== self.options.addCenteredClassWithDelay && _.isNumber( self.options.addCenteredClassWithDelay ) ) {
                        _.delay( function() {
                              $_img.addClass( _p._class ).removeClass( _not_p._class );
                        }, self.options.addCenteredClassWithDelay );
                  } else {
                        $_img.addClass( _p._class ).removeClass( _not_p._class );
                  }
                  if ( $_img.attr('data-img-centered') ) {
                        var _n = parseInt( $_img.attr('data-img-centered'), 10 ) + 1;
                        $_img.attr('data-img-centered', _n );
                  } else {
                        $_img.attr('data-img-centered', 1 );
                  }
                  return $_img;
            };
            if ( this.options.setOpacityWhenCentered ) {
                  $.when( _centerImg( $_img ) ).done( function( $_img ) {
                        $_img.css( 'opacity', self.options.opacity );
                  });
            } else {
                  _.delay(function() { _centerImg( $_img ); }, 0 );
            }
      };
      Plugin.prototype._get_current_state = function( $_img ) {
            var c_x     = $_img.closest(this.container).outerWidth(),
                c_y     = $(this.container).outerHeight(),
                i_x     = this._get_img_dim( $_img , 'x'),
                i_y     = this._get_img_dim( $_img , 'y'),
                up_i_x  = i_y * c_y !== 0 ? Math.round( i_x / i_y * c_y ) : c_x,
                up_i_y  = i_x * c_x !== 0 ? Math.round( i_y / i_x * c_x ) : c_y,
                current = 'h';
            if ( 0 !== c_x * i_x ) {
                  current = ( c_y / c_x ) >= ( i_y / i_x ) ? 'h' : 'v';
            }

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
      Plugin.prototype._get_img_dim = function( $_img, _dim ) {
            if ( ! this.options.useImgAttr )
              return 'x' == _dim ? $_img.outerWidth() : $_img.outerHeight();

            if ( $_img.is(":visible") ) {
                  return 'x' == _dim ? $_img.outerWidth() : $_img.outerHeight();
            } else {
                  if ( 'x' == _dim ){
                        var _width = $_img.originalWidth();
                        return typeof _width === undefined ? 0 : _width;
                  }
                  if ( 'y' == _dim ){
                        var _height = $_img.originalHeight();
                        return typeof _height === undefined ? 0 : _height;
                  }
            }
      };
      Plugin.prototype._is_selector_allowed = function() {
            if ( ! $(this.container).attr( 'class' ) )
              return true;
            if ( ! this.options.skipGoldenRatioClasses || ! $.isArray( this.options.skipGoldenRatioClasses )  )
              return true;

            var _elSels       = $(this.container).attr( 'class' ).split(' '),
                _selsToSkip   = this.options.skipGoldenRatioClasses,
                _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});
            return 0 === _filtered.length;
      };
      $.fn[pluginName] = function ( options ) {
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName,
                    new Plugin( this, options ));
                }
            });
      };

})( jQuery, window, document );/* ===================================================
 * jqueryParallax.js v1.0.0
 * ===================================================
 * (c) 2016 Nicolas Guillaume - Rocco Aliberti, Nice, France
 * CenterImages plugin may be freely distributed under the terms of the GNU GPL v2.0 or later license.
 *
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 *
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
        var pluginName = 'czrParallax',
            defaults = {
                  parallaxRatio : 0.5,
                  parallaxDirection : 1,
                  parallaxOverflowHidden : true,
                  oncustom : [],//list of event here
                  backgroundClass : 'image',
                  matchMedia : 'only screen and (max-width: 768px)'
            };

        function Plugin( element, options ) {
              this.element         = $(element);
              this.element_wrapper = this.element.closest( '.parallax-wrapper' );
              this.options         = $.extend( {}, defaults, options, this.parseElementDataOptions() ) ;
              this._defaults       = defaults;
              this._name           = pluginName;
              this.init();
        }

        Plugin.prototype.parseElementDataOptions = function () {
              return this.element.data();
        };
        Plugin.prototype.init = function () {
              this.$_document   = $(document);
              this.$_window     = czrapp ? czrapp.$_window : $(window);
              this.doingAnimation = false;

              this.initWaypoints();
              this.stageParallaxElements();
              this._bind_evt();
        };
        Plugin.prototype._bind_evt = function() {
              var self = this,
                  _customEvt = $.isArray(this.options.oncustom) ? this.options.oncustom : this.options.oncustom.split(' ');

              _.bindAll( this, 'maybeParallaxMe', 'parallaxMe' );
        };

        Plugin.prototype.stageParallaxElements = function() {

              this.element.css({
                    'position': this.element.hasClass( this.options.backgroundClass ) ? 'absolute' : 'relative',
                    'will-change': 'transform'
              });

              if ( this.options.parallaxOverflowHidden ){
                    var $_wrapper = this.element_wrapper;
                    if ( $_wrapper.length )
                      $_wrapper.css( 'overflow', 'hidden' );
              }
        };

        Plugin.prototype.initWaypoints = function() {
              var self = this;

              this.way_start = new Waypoint({
                    element: self.element_wrapper.length ? self.element_wrapper : self.element,
                    handler: function() {
                          self.maybeParallaxMe();
                          if ( ! self.element.hasClass('parallaxing') ){
                                self.$_window.on('scroll', self.maybeParallaxMe );
                                self.element.addClass('parallaxing');
                          } else{
                                self.element.removeClass('parallaxing');
                                self.$_window.off('scroll', self.maybeParallaxMe );
                                self.doingAnimation = false;
                                self.element.css('top', 0 );
                          }
                    }
              });

              this.way_stop = new Waypoint({
                    element: self.element_wrapper.length ? self.element_wrapper : self.element,
                    handler: function() {
                          self.maybeParallaxMe();
                          if ( ! self.element.hasClass('parallaxing') ) {
                                self.$_window.on('scroll', self.maybeParallaxMe );
                                self.element.addClass('parallaxing');
                          }else {
                                self.element.removeClass('parallaxing');
                                self.$_window.off('scroll', self.maybeParallaxMe );
                                self.doingAnimation = false;
                          }
                    },
                    offset: function(){
                          return - this.adapter.outerHeight();
                    }
              });
        };
        Plugin.prototype.maybeParallaxMe = function() {
              var self = this;
              if ( _.isFunction( window.matchMedia ) && matchMedia( self.options.matchMedia ).matches )
                return this.setTopPosition();

              if ( ! this.doingAnimation ) {
                    this.doingAnimation = true;
                    window.requestAnimationFrame(function() {
                          self.parallaxMe();
                          self.doingAnimation = false;
                    });
              }
        };
        Plugin.prototype.setTopPosition = function( _top_ ) {
              _top_ = _top_ || 0;
              this.element.css({
                    'transform' : 'translate3d(0px, ' + _top_  + 'px, .01px)',
                    '-webkit-transform' : 'translate3d(0px, ' + _top_  + 'px, .01px)'
              });
        };

        Plugin.prototype.parallaxMe = function() {

              var ratio = this.options.parallaxRatio,
                  parallaxDirection = this.options.parallaxDirection,
                  value = ratio * parallaxDirection * ( this.$_document.scrollTop() - this.way_start.triggerPoint );
              this.setTopPosition( parallaxDirection * value < 0 ? 0 : value );
        };
        $.fn[pluginName] = function ( options ) {
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName,
                    new Plugin( this, options ));
                }
            });
        };
})( jQuery, window, document );/* ===================================================
 * jqueryAnimateSvg.js v1.0.0
 * @dependency : Vivus.js (MIT licensed)
 * ===================================================
 * (c) 2016 Nicolas Guillaume, Nice, France
 * Animates an svg icon with Vivus given its #id
 * =================================================== */
;(function ( $, window, document, _ ) {
  var pluginName = 'animateSvg',
      defaults = {
        filter_opacity : 0.8,
        svg_opacity : 0.8,
        animation_duration : 400
      },
      _drawSvgIcon = function(options) {
          var id = $(this).attr('id');
          if ( _.isUndefined(id) || _.isEmpty(id) || 'function' != typeof( Vivus ) ) {
            if ( window.czrapp )
              czrapp.consoleLog( 'An svg icon could not be animated with Vivus.');
            return;
          }
          if ( $('[id=' + id + ']').length > 1 ) {
            if ( window.czrapp )
              czrapp.consoleLog( 'Svg icons must have a unique css #id to be animated. Multiple id found for : ' + id );
          }
          var set_opacity = function() {
            if ( $('#' + id ).siblings('.filter-placeholder').length )
              return $('#' + id ).css('opacity', options.svg_opacity ).siblings('.filter-placeholder').css('opacity', options.filter_opacity);
            else
              return $('#' + id ).css('opacity', options.svg_opacity );
          };
          $.when( set_opacity() ).done( function() {
              new Vivus( id, {type: 'delayed', duration: options.animation_duration } );
          });
      };
  $.fn[pluginName] = function ( options ) {
      options  = $.extend( {}, defaults, options) ;
      return this.each(function () {
          if ( ! $.data(this, 'plugin_' + pluginName) ) {
              $.data(
                this,
                'plugin_' + pluginName,
                _drawSvgIcon.call( this, options )
              );
          }
      });
  };
})( jQuery, window, document, _ );// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
                                   || window[vendors[x]+'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            lastTime = currTime + timeToCall;
            return window.setTimeout(function() { callback(currTime + timeToCall); },
              timeToCall);
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());/*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas, David Knight. Dual MIT/BSD license */

window.matchMedia || (window.matchMedia = function() {
    "use strict";
    var styleMedia = (window.styleMedia || window.media);
    if (!styleMedia) {
        var style       = document.createElement('style'),
            script      = document.getElementsByTagName('script')[0],
            info        = null;

        style.type  = 'text/css';
        style.id    = 'matchmediajs-test';

        if (!script) {
          document.head.appendChild(style);
        } else {
          script.parentNode.insertBefore(style, script);
        }
        info = ('getComputedStyle' in window) && window.getComputedStyle(style, null) || style.currentStyle;

        styleMedia = {
            matchMedium: function(media) {
                var text = '@media ' + media + '{ #matchmediajs-test { width: 1px; } }';
                if (style.styleSheet) {
                    style.styleSheet.cssText = text;
                } else {
                    style.textContent = text;
                }
                return info.width === '1px';
            }
        };
    }

    return function(media) {
        return {
            matches: styleMedia.matchMedium(media || 'all'),
            media: media || 'all'
        };
    };
}());/*global jQuery */
/*!
* This is an adapted version of fit text
* 1) a user defined caption font ratio has been added
* 2) the resizer takes into account not only the element width, but the specified parent's height. => this solves the problem of fonts not properly resized on landscape mobile devices, or slider too short (user can set the slider's height)
* @return void()
* FitText.js 1.2
*
* Copyright 2011, Dave Rupert http://daverupert.com
* Released under the WTFPL license
* http://sam.zoy.org/wtfpl/
*
* Date: Thu May 05 14:23:00 2011 -0600
*/

(function( $ ){

  $.fn.czrFitText = function( kompressor, options ) {
    var compressor = kompressor || 1,
        settings = $.extend({
          'minFontSize'            : Number.NEGATIVE_INFINITY,
          'maxFontSize'            : Number.POSITIVE_INFINITY,
          'fontRatio'              : 1,
          'refParentSel'           : '.fittext-p',
          'parentCompressorFactor' : 8,
        }, options);

    return this.each(function(){
      var $this      = $(this),
          $refParent = $this.closest(settings.refParentSel),
          _font_size;
      var resizer = function () {

        _font_size = Math.max(
            Math.min(
              $this.width() / (compressor*10),
              ( $refParent.length >= 1 ) ? $refParent.height() / (compressor*settings.parentCompressorFactor) : $this.width() / (compressor*10),
              parseFloat(settings.maxFontSize)
            ),
            parseFloat(settings.minFontSize)
        );

        _font_size = Math.max( _font_size * settings.fontRatio, parseFloat( settings.minFontSize ) );

        $this.css('font-size', _font_size  );
        $this.css('line-height', ( _font_size  * 1.45 ) + 'px');
      };
      resizer();
      $(window).on('resize.czrFittext orientationchange.czrFittext', resizer);

    });

  };

})( jQuery );// Customizr version of Galambosi's SmoothScroll

(function () {
var defaultOptions = {
    frameRate        : 150, // [Hz]
    animationTime    : 400, // [px]
    stepSize         : 120, // [px]
    pulseAlgorithm   : true,
    pulseScale       : 4,
    pulseNormalize   : 1,
    accelerationDelta : 20,  // 20
    accelerationMax   : 1,   // 1
    keyboardSupport   : true,  // option
    arrowScroll       : 50,     // [px]
    touchpadSupport   : true,
    fixedBackground   : true, 
    excluded          : ''    
};

var options = defaultOptions;
var isExcluded = false;
var isFrame = false;
var direction = { x: 0, y: 0 };
var initDone  = false;
var root = document.documentElement;
var activeElement;
var observer;
var deltaBuffer = [];
var isMac = /^Mac/.test(navigator.platform);

var key = { left: 37, up: 38, right: 39, down: 40, spacebar: 32, 
            pageup: 33, pagedown: 34, end: 35, home: 36 };

var options = defaultOptions;
function initTest() {
    if (options.keyboardSupport) {
        addEvent('keydown', keydown);
    }
}
function init() {
  
    if (initDone || !document.body) return;

    initDone = true;

    var body = document.body;
    var html = document.documentElement;
    var windowHeight = window.innerHeight; 
    var scrollHeight = body.scrollHeight;
    root = (document.compatMode.indexOf('CSS') >= 0) ? html : body;
    activeElement = body;
    
    initTest();
    if (top != self) {
        isFrame = true;
    }
    else if (scrollHeight > windowHeight &&
            (body.offsetHeight <= windowHeight || 
             html.offsetHeight <= windowHeight)) {

        var fullPageElem = document.createElement('div');
        fullPageElem.style.cssText = 'position:absolute; z-index:-10000; ' +
                                     'top:0; left:0; right:0; height:' + 
                                      root.scrollHeight + 'px';
        document.body.appendChild(fullPageElem);
        var pendingRefresh;
        var refresh = function () {
            if (pendingRefresh) return; // could also be: clearTimeout(pendingRefresh);
            pendingRefresh = setTimeout(function () {
                if (isExcluded) return; // could be running after cleanup
                fullPageElem.style.height = '0';
                fullPageElem.style.height = root.scrollHeight + 'px';
                pendingRefresh = null;
            }, 500); // act rarely to stay fast
        };
  
        setTimeout(refresh, 10);
        var config = {
            attributes: true, 
            childList: true, 
            characterData: false 
        };

        observer = new MutationObserver(refresh);
        observer.observe(body, config);

        if (root.offsetHeight <= windowHeight) {
            var clearfix = document.createElement('div');   
            clearfix.style.clear = 'both';
            body.appendChild(clearfix);
        }
    }
    if (!options.fixedBackground && !isExcluded) {
        body.style.backgroundAttachment = 'scroll';
        html.style.backgroundAttachment = 'scroll';
    }
}
function cleanup() {
    observer && observer.disconnect();
    removeEvent(wheelEvent, wheel);
    removeEvent('mousedown', mousedown);
    removeEvent('keydown', keydown);
}
var que = [];
var pending = false;
var lastScroll = Date.now();
function scrollArray(elem, left, top) {
    
    directionCheck(left, top);

    if (options.accelerationMax != 1) {
        var now = Date.now();
        var elapsed = now - lastScroll;
        if (elapsed < options.accelerationDelta) {
            var factor = (1 + (50 / elapsed)) / 2;
            if (factor > 1) {
                factor = Math.min(factor, options.accelerationMax);
                left *= factor;
                top  *= factor;
            }
        }
        lastScroll = Date.now();
    }          
    que.push({
        x: left, 
        y: top, 
        lastX: (left < 0) ? 0.99 : -0.99,
        lastY: (top  < 0) ? 0.99 : -0.99, 
        start: Date.now()
    });
    if (pending) {
        return;
    }  

    var scrollWindow = (elem === document.body);
    
    var step = function (time) {
        
        var now = Date.now();
        var scrollX = 0;
        var scrollY = 0; 
    
        for (var i = 0; i < que.length; i++) {
            
            var item = que[i];
            var elapsed  = now - item.start;
            var finished = (elapsed >= options.animationTime);
            var position = (finished) ? 1 : elapsed / options.animationTime;
            if (options.pulseAlgorithm) {
                position = pulse(position);
            }
            var x = (item.x * position - item.lastX) >> 0;
            var y = (item.y * position - item.lastY) >> 0;
            scrollX += x;
            scrollY += y;            
            item.lastX += x;
            item.lastY += y;
            if (finished) {
                que.splice(i, 1); i--;
            }           
        }
        if (scrollWindow) {
            window.scrollBy(scrollX, scrollY);
        } 
        else {
            if (scrollX) elem.scrollLeft += scrollX;
            if (scrollY) elem.scrollTop  += scrollY;                    
        }
        if (!left && !top) {
            que = [];
        }
        
        if (que.length) { 
            requestFrame(step, elem, (1000 / options.frameRate + 1)); 
        } else { 
            pending = false;
        }
    };
    requestFrame(step, elem, 0);
    pending = true;
}
function wheel(event) {

    if (!initDone) {
        init();
    }
    
    var target = event.target;
    var overflowing = overflowingAncestor(target);
    if (!overflowing || event.defaultPrevented || event.ctrlKey) {
        return true;
    }
    if (isNodeName(activeElement, 'embed') || 
       (isNodeName(target, 'embed') && /\.pdf/i.test(target.src)) ||
       isNodeName(activeElement, 'object')) {
        return true;
    }

    var deltaX = -event.wheelDeltaX || event.deltaX || 0;
    var deltaY = -event.wheelDeltaY || event.deltaY || 0;
    
    if (isMac) {
        if (event.wheelDeltaX && isDivisible(event.wheelDeltaX, 120)) {
            deltaX = -120 * (event.wheelDeltaX / Math.abs(event.wheelDeltaX));
        }
        if (event.wheelDeltaY && isDivisible(event.wheelDeltaY, 120)) {
            deltaY = -120 * (event.wheelDeltaY / Math.abs(event.wheelDeltaY));
        }
    }
    if (!deltaX && !deltaY) {
        deltaY = -event.wheelDelta || 0;
    }
    if (event.deltaMode === 1) {
        deltaX *= 40;
        deltaY *= 40;
    }
    if (!options.touchpadSupport && isTouchpad(deltaY)) {
        return true;
    }
    if (Math.abs(deltaX) > 1.2) {
        deltaX *= options.stepSize / 120;
    }
    if (Math.abs(deltaY) > 1.2) {
        deltaY *= options.stepSize / 120;
    }
    
    scrollArray(overflowing, deltaX, deltaY);
    event.preventDefault();
    scheduleClearCache();
}
function keydown(event) {

    var target   = event.target;
    var modifier = event.ctrlKey || event.altKey || event.metaKey || 
                  (event.shiftKey && event.keyCode !== key.spacebar);
    if (!document.contains(activeElement)) {
        activeElement = document.activeElement;
    }
    var inputNodeNames = /^(textarea|select|embed|object)$/i;
    var buttonTypes = /^(button|submit|radio|checkbox|file|color|image)$/i;
    if ( inputNodeNames.test(target.nodeName) ||
         isNodeName(target, 'input') && !buttonTypes.test(target.type) ||
         isNodeName(activeElement, 'video') ||
         isInsideYoutubeVideo(event) ||
         target.isContentEditable || 
         event.defaultPrevented   ||
         modifier ) {
      return true;
    }
    if ((isNodeName(target, 'button') ||
         isNodeName(target, 'input') && buttonTypes.test(target.type)) &&
        event.keyCode === key.spacebar) {
      return true;
    }
    
    var shift, x = 0, y = 0;
    var elem = overflowingAncestor(activeElement);
    var clientHeight = elem.clientHeight;

    if (elem == document.body) {
        clientHeight = window.innerHeight;
    }

    switch (event.keyCode) {
        case key.up:
            y = -options.arrowScroll;
            break;
        case key.down:
            y = options.arrowScroll;
            break;         
        case key.spacebar: // (+ shift)
            shift = event.shiftKey ? 1 : -1;
            y = -shift * clientHeight * 0.9;
            break;
        case key.pageup:
            y = -clientHeight * 0.9;
            break;
        case key.pagedown:
            y = clientHeight * 0.9;
            break;
        case key.home:
            y = -elem.scrollTop;
            break;
        case key.end:
            var damt = elem.scrollHeight - elem.scrollTop - clientHeight;
            y = (damt > 0) ? damt+10 : 0;
            break;
        case key.left:
            x = -options.arrowScroll;
            break;
        case key.right:
            x = options.arrowScroll;
            break;            
        default:
            return true; // a key we don't care about
    }

    scrollArray(elem, x, y);
    event.preventDefault();
    scheduleClearCache();
}
function mousedown(event) {
    activeElement = event.target;
}

var uniqueID = (function () {
    var i = 0;
    return function (el) {
        return el.uniqueID || (el.uniqueID = i++);
    };
})();

var cache = {}; // cleared out after a scrolling session
var clearCacheTimer;

function scheduleClearCache() {
    clearTimeout(clearCacheTimer);
    clearCacheTimer = setInterval(function () { cache = {}; }, 1*1000);
}

function setCache(elems, overflowing) {
    for (var i = elems.length; i--;)
        cache[uniqueID(elems[i])] = overflowing;
    return overflowing;
}

function overflowingAncestor(el) {
    var elems = [];
    var body = document.body;
    var rootScrollHeight = root.scrollHeight;
    do {
        var cached = cache[uniqueID(el)];
        if (cached) {
            return setCache(elems, cached);
        }
        elems.push(el);
        if (rootScrollHeight === el.scrollHeight) {
            var topOverflowsNotHidden = overflowNotHidden(root) && overflowNotHidden(body);
            var isOverflowCSS = topOverflowsNotHidden || overflowAutoOrScroll(root);
            if (isFrame && isContentOverflowing(root) || 
               !isFrame && isOverflowCSS) {
                return setCache(elems, getScrollRoot()); 
            }
        } else if (isContentOverflowing(el) && overflowAutoOrScroll(el)) {
            return setCache(elems, el);
        }
    } while (el = el.parentElement);
}

function isContentOverflowing(el) {
    return (el.clientHeight + 10 < el.scrollHeight);
}
function overflowNotHidden(el) {
    var overflow = getComputedStyle(el, '').getPropertyValue('overflow-y');
    return (overflow !== 'hidden');
}
function overflowAutoOrScroll(el) {
    var overflow = getComputedStyle(el, '').getPropertyValue('overflow-y');
    return (overflow === 'scroll' || overflow === 'auto');
}

function addEvent(type, fn) {
    window.addEventListener(type, fn, false);
}

function removeEvent(type, fn) {
    window.removeEventListener(type, fn, false);  
}

function isNodeName(el, tag) {
    return (el.nodeName||'').toLowerCase() === tag.toLowerCase();
}

function directionCheck(x, y) {
    x = (x > 0) ? 1 : -1;
    y = (y > 0) ? 1 : -1;
    if (direction.x !== x || direction.y !== y) {
        direction.x = x;
        direction.y = y;
        que = [];
        lastScroll = 0;
    }
}

var deltaBufferTimer;

if (window.localStorage && localStorage.SS_deltaBuffer) {
    deltaBuffer = localStorage.SS_deltaBuffer.split(',');
}

function isTouchpad(deltaY) {
    if (!deltaY) return;
    if (!deltaBuffer.length) {
        deltaBuffer = [deltaY, deltaY, deltaY];
    }
    deltaY = Math.abs(deltaY)
    deltaBuffer.push(deltaY);
    deltaBuffer.shift();
    clearTimeout(deltaBufferTimer);
    deltaBufferTimer = setTimeout(function () {
        if (window.localStorage) {
            localStorage.SS_deltaBuffer = deltaBuffer.join(',');
        }
    }, 1000);
    return !allDeltasDivisableBy(120) && !allDeltasDivisableBy(100);
} 

function isDivisible(n, divisor) {
    return (Math.floor(n / divisor) == n / divisor);
}

function allDeltasDivisableBy(divisor) {
    return (isDivisible(deltaBuffer[0], divisor) &&
            isDivisible(deltaBuffer[1], divisor) &&
            isDivisible(deltaBuffer[2], divisor));
}

function isInsideYoutubeVideo(event) {
    var elem = event.target;
    var isControl = false;
    if (document.URL.indexOf ('www.youtube.com/watch') != -1) {
        do {
            isControl = (elem.classList && 
                         elem.classList.contains('html5-video-controls'));
            if (isControl) break;
        } while (elem = elem.parentNode);
    }
    return isControl;
}

var requestFrame = (function () {
      return (window.requestAnimationFrame       || 
              window.webkitRequestAnimationFrame || 
              window.mozRequestAnimationFrame    ||
              function (callback, element, delay) {
                 window.setTimeout(callback, delay || (1000/60));
             });
})();

var MutationObserver = (window.MutationObserver || 
                        window.WebKitMutationObserver ||
                        window.MozMutationObserver);  

var getScrollRoot = (function() {
  var SCROLL_ROOT;
  return function() {
    if (!SCROLL_ROOT) {
      var dummy = document.createElement('div');
      dummy.style.cssText = 'height:10000px;width:1px;';
      document.body.appendChild(dummy);
      var bodyScrollTop  = document.body.scrollTop;
      var docElScrollTop = document.documentElement.scrollTop;
      window.scrollBy(0, 1);
      if (document.body.scrollTop != bodyScrollTop)
        (SCROLL_ROOT = document.body);
      else 
        (SCROLL_ROOT = document.documentElement);
      window.scrollBy(0, -1);
      document.body.removeChild(dummy);
    }
    return SCROLL_ROOT;
  };
})();
function pulse_(x) {
    var val, start, expx;
    x = x * options.pulseScale;
    if (x < 1) { // acceleartion
        val = x - (1 - Math.exp(-x));
    } else {     // tail
        start = Math.exp(-1);
        x -= 1;
        expx = 1 - Math.exp(-x);
        val = start + (expx * (1 - start));
    }
    return val * options.pulseNormalize;
}

function pulse(x) {
    if (x >= 1) return 1;
    if (x <= 0) return 0;

    if (options.pulseNormalize == 1) {
        options.pulseNormalize /= pulse_(1);
    }
    return pulse_(x);
}

var wheelEvent;
if ('onwheel' in document.createElement('div'))
    wheelEvent = 'wheel';
else if ('onmousewheel' in document.createElement('div'))
    wheelEvent = 'mousewheel';
function _maybeInit( fire ){
  if (wheelEvent) {
    addEvent(wheelEvent, wheel);
    addEvent('mousedown', mousedown);
    if ( ! fire ) addEvent('load', init);
    else init();
  }
  return wheelEvent ? true : false;
}
smoothScroll = function ( _options ) {
  smoothScroll._setCustomOptions( _options );
  _maybeInit() && czrapp.$_body.addClass('tc-smoothscroll');
}
smoothScroll._cleanUp = function(){
  cleanup();    
  czrapp.$_body.removeClass('tc-smoothscroll');
}
smoothScroll._maybeFire = function(){
  _maybeInit(true) && czrapp.$_body.addClass('tc-smoothscroll');
}
smoothScroll._setCustomOptions = function( _options ){
  options  =  _options ? _.extend( options, _options) : options;
}
})();

var smoothScroll;
var tcOutline;
(function(d){
  tcOutline = function() {
  var style_element = d.createElement('STYLE'),
      dom_events = 'addEventListener' in d,
      add_event_listener = function(type, callback){
      if(dom_events){
        d.addEventListener(type, callback);
      }else{
        d.attachEvent('on' + type, callback);
      }
    },
      set_css = function(css_text){
      if ( !!style_element.styleSheet )
                style_element.styleSheet.cssText = css_text;
            else
                style_element.innerHTML = css_text;
    }
  ;

  d.getElementsByTagName('HEAD')[0].appendChild(style_element);
  add_event_listener('mousedown', function(){
    set_css('input[type=file]:focus,input[type=radio]:focus,input[type=checkbox]:focus,select:focus,span:focus,a:focus{outline:none!important;-webkit-box-shadow:none!important;box-shadow:none!important;}input[type=file]::-moz-focus-inner,input[type=radio]::-moz-focus-inner,input[type=checkbox]::-moz-focus-inner,select::-moz-focus-inner,a::-moz-focus-inner{border:0;}');
  });

  add_event_listener('keydown', function(){
    set_css('');
  });
  }
})(document);/*!
Waypoints - 4.0.0
Copyright © 2011-2015 Caleb Troughton
Licensed under the MIT license.
https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
*/
(function() {
  'use strict'

  var keyCounter = 0
  var allWaypoints = {}
  function Waypoint(options) {
    if (!options) {
      throw new Error('No options passed to Waypoint constructor')
    }
    if (!options.element) {
      throw new Error('No element option passed to Waypoint constructor')
    }
    if (!options.handler) {
      throw new Error('No handler option passed to Waypoint constructor')
    }

    this.key = 'waypoint-' + keyCounter
    this.options = Waypoint.Adapter.extend({}, Waypoint.defaults, options)
    this.element = this.options.element
    this.adapter = new Waypoint.Adapter(this.element)
    this.callback = options.handler
    this.axis = this.options.horizontal ? 'horizontal' : 'vertical'
    this.enabled = this.options.enabled
    this.triggerPoint = null
    this.group = Waypoint.Group.findOrCreate({
      name: this.options.group,
      axis: this.axis
    })
    this.context = Waypoint.Context.findOrCreateByElement(this.options.context)

    if (Waypoint.offsetAliases[this.options.offset]) {
      this.options.offset = Waypoint.offsetAliases[this.options.offset]
    }
    this.group.add(this)
    this.context.add(this)
    allWaypoints[this.key] = this
    keyCounter += 1
  }
  Waypoint.prototype.queueTrigger = function(direction) {
    this.group.queueTrigger(this, direction)
  }
  Waypoint.prototype.trigger = function(args) {
    if (!this.enabled) {
      return
    }
    if (this.callback) {
      this.callback.apply(this, args)
    }
  }
  Waypoint.prototype.destroy = function() {
    this.context.remove(this)
    this.group.remove(this)
    delete allWaypoints[this.key]
  }
  Waypoint.prototype.disable = function() {
    this.enabled = false
    return this
  }
  Waypoint.prototype.enable = function() {
    this.context.refresh()
    this.enabled = true
    return this
  }
  Waypoint.prototype.next = function() {
    return this.group.next(this)
  }
  Waypoint.prototype.previous = function() {
    return this.group.previous(this)
  }
  Waypoint.invokeAll = function(method) {
    var allWaypointsArray = []
    for (var waypointKey in allWaypoints) {
      allWaypointsArray.push(allWaypoints[waypointKey])
    }
    for (var i = 0, end = allWaypointsArray.length; i < end; i++) {
      allWaypointsArray[i][method]()
    }
  }
  Waypoint.destroyAll = function() {
    Waypoint.invokeAll('destroy')
  }
  Waypoint.disableAll = function() {
    Waypoint.invokeAll('disable')
  }
  Waypoint.enableAll = function() {
    Waypoint.invokeAll('enable')
  }
  Waypoint.refreshAll = function() {
    Waypoint.Context.refreshAll()
  }
  Waypoint.viewportHeight = function() {
    return window.innerHeight || document.documentElement.clientHeight
  }
  Waypoint.viewportWidth = function() {
    return document.documentElement.clientWidth
  }

  Waypoint.adapters = []

  Waypoint.defaults = {
    context: window,
    continuous: true,
    enabled: true,
    group: 'default',
    horizontal: false,
    offset: 0
  }

  Waypoint.offsetAliases = {
    'bottom-in-view': function() {
      return this.context.innerHeight() - this.adapter.outerHeight()
    },
    'right-in-view': function() {
      return this.context.innerWidth() - this.adapter.outerWidth()
    }
  }

  window.Waypoint = Waypoint
}())
;(function() {
  'use strict'

  function requestAnimationFrameShim(callback) {
    window.setTimeout(callback, 1000 / 60)
  }

  var keyCounter = 0
  var contexts = {}
  var Waypoint = window.Waypoint
  var oldWindowLoad = window.onload
  function Context(element) {
    this.element = element
    this.Adapter = Waypoint.Adapter
    this.adapter = new this.Adapter(element)
    this.key = 'waypoint-context-' + keyCounter
    this.didScroll = false
    this.didResize = false
    this.oldScroll = {
      x: this.adapter.scrollLeft(),
      y: this.adapter.scrollTop()
    }
    this.waypoints = {
      vertical: {},
      horizontal: {}
    }

    element.waypointContextKey = this.key
    contexts[element.waypointContextKey] = this
    keyCounter += 1

    this.createThrottledScrollHandler()
    this.createThrottledResizeHandler()
  }
  Context.prototype.add = function(waypoint) {
    var axis = waypoint.options.horizontal ? 'horizontal' : 'vertical'
    this.waypoints[axis][waypoint.key] = waypoint
    this.refresh()
  }
  Context.prototype.checkEmpty = function() {
    var horizontalEmpty = this.Adapter.isEmptyObject(this.waypoints.horizontal)
    var verticalEmpty = this.Adapter.isEmptyObject(this.waypoints.vertical)
    if (horizontalEmpty && verticalEmpty) {
      this.adapter.off('.waypoints')
      delete contexts[this.key]
    }
  }
  Context.prototype.createThrottledResizeHandler = function() {
    var self = this

    function resizeHandler() {
      self.handleResize()
      self.didResize = false
    }

    this.adapter.on('resize.waypoints', function() {
      if (!self.didResize) {
        self.didResize = true
        Waypoint.requestAnimationFrame(resizeHandler)
      }
    })
  }
  Context.prototype.createThrottledScrollHandler = function() {
    var self = this
    function scrollHandler() {
      self.handleScroll()
      self.didScroll = false
    }

    this.adapter.on('scroll.waypoints', function() {
      if (!self.didScroll || Waypoint.isTouch) {
        self.didScroll = true
        Waypoint.requestAnimationFrame(scrollHandler)
      }
    })
  }
  Context.prototype.handleResize = function() {
    Waypoint.Context.refreshAll()
  }
  Context.prototype.handleScroll = function() {
    var triggeredGroups = {}
    var axes = {
      horizontal: {
        newScroll: this.adapter.scrollLeft(),
        oldScroll: this.oldScroll.x,
        forward: 'right',
        backward: 'left'
      },
      vertical: {
        newScroll: this.adapter.scrollTop(),
        oldScroll: this.oldScroll.y,
        forward: 'down',
        backward: 'up'
      }
    }

    for (var axisKey in axes) {
      var axis = axes[axisKey]
      var isForward = axis.newScroll > axis.oldScroll
      var direction = isForward ? axis.forward : axis.backward

      for (var waypointKey in this.waypoints[axisKey]) {
        var waypoint = this.waypoints[axisKey][waypointKey]
        var wasBeforeTriggerPoint = axis.oldScroll < waypoint.triggerPoint
        var nowAfterTriggerPoint = axis.newScroll >= waypoint.triggerPoint
        var crossedForward = wasBeforeTriggerPoint && nowAfterTriggerPoint
        var crossedBackward = !wasBeforeTriggerPoint && !nowAfterTriggerPoint
        if (crossedForward || crossedBackward) {
          waypoint.queueTrigger(direction)
          triggeredGroups[waypoint.group.id] = waypoint.group
        }
      }
    }

    for (var groupKey in triggeredGroups) {
      triggeredGroups[groupKey].flushTriggers()
    }

    this.oldScroll = {
      x: axes.horizontal.newScroll,
      y: axes.vertical.newScroll
    }
  }
  Context.prototype.innerHeight = function() {
    if (this.element == this.element.window) {
      return Waypoint.viewportHeight()
    }
    return this.adapter.innerHeight()
  }
  Context.prototype.remove = function(waypoint) {
    delete this.waypoints[waypoint.axis][waypoint.key]
    this.checkEmpty()
  }
  Context.prototype.innerWidth = function() {
    if (this.element == this.element.window) {
      return Waypoint.viewportWidth()
    }
    return this.adapter.innerWidth()
  }
  Context.prototype.destroy = function() {
    var allWaypoints = []
    for (var axis in this.waypoints) {
      for (var waypointKey in this.waypoints[axis]) {
        allWaypoints.push(this.waypoints[axis][waypointKey])
      }
    }
    for (var i = 0, end = allWaypoints.length; i < end; i++) {
      allWaypoints[i].destroy()
    }
  }
  Context.prototype.refresh = function() {
    var isWindow = this.element == this.element.window
    var contextOffset = isWindow ? undefined : this.adapter.offset()
    var triggeredGroups = {}
    var axes

    this.handleScroll()
    axes = {
      horizontal: {
        contextOffset: isWindow ? 0 : contextOffset.left,
        contextScroll: isWindow ? 0 : this.oldScroll.x,
        contextDimension: this.innerWidth(),
        oldScroll: this.oldScroll.x,
        forward: 'right',
        backward: 'left',
        offsetProp: 'left'
      },
      vertical: {
        contextOffset: isWindow ? 0 : contextOffset.top,
        contextScroll: isWindow ? 0 : this.oldScroll.y,
        contextDimension: this.innerHeight(),
        oldScroll: this.oldScroll.y,
        forward: 'down',
        backward: 'up',
        offsetProp: 'top'
      }
    }

    for (var axisKey in axes) {
      var axis = axes[axisKey]
      for (var waypointKey in this.waypoints[axisKey]) {
        var waypoint = this.waypoints[axisKey][waypointKey]
        var adjustment = waypoint.options.offset
        var oldTriggerPoint = waypoint.triggerPoint
        var elementOffset = 0
        var freshWaypoint = oldTriggerPoint == null
        var contextModifier, wasBeforeScroll, nowAfterScroll
        var triggeredBackward, triggeredForward

        if (waypoint.element !== waypoint.element.window) {
          elementOffset = waypoint.adapter.offset()[axis.offsetProp]
        }

        if (typeof adjustment === 'function') {
          adjustment = adjustment.apply(waypoint)
        }
        else if (typeof adjustment === 'string') {
          adjustment = parseFloat(adjustment)
          if (waypoint.options.offset.indexOf('%') > - 1) {
            adjustment = Math.ceil(axis.contextDimension * adjustment / 100)
          }
        }

        contextModifier = axis.contextScroll - axis.contextOffset
        waypoint.triggerPoint = elementOffset + contextModifier - adjustment
        wasBeforeScroll = oldTriggerPoint < axis.oldScroll
        nowAfterScroll = waypoint.triggerPoint >= axis.oldScroll
        triggeredBackward = wasBeforeScroll && nowAfterScroll
        triggeredForward = !wasBeforeScroll && !nowAfterScroll

        if (!freshWaypoint && triggeredBackward) {
          waypoint.queueTrigger(axis.backward)
          triggeredGroups[waypoint.group.id] = waypoint.group
        }
        else if (!freshWaypoint && triggeredForward) {
          waypoint.queueTrigger(axis.forward)
          triggeredGroups[waypoint.group.id] = waypoint.group
        }
        else if (freshWaypoint && axis.oldScroll >= waypoint.triggerPoint) {
          waypoint.queueTrigger(axis.forward)
          triggeredGroups[waypoint.group.id] = waypoint.group
        }
      }
    }

    Waypoint.requestAnimationFrame(function() {
      for (var groupKey in triggeredGroups) {
        triggeredGroups[groupKey].flushTriggers()
      }
    })

    return this
  }
  Context.findOrCreateByElement = function(element) {
    return Context.findByElement(element) || new Context(element)
  }
  Context.refreshAll = function() {
    for (var contextId in contexts) {
      contexts[contextId].refresh()
    }
  }
  Context.findByElement = function(element) {
    return contexts[element.waypointContextKey]
  }

  window.onload = function() {
    if (oldWindowLoad) {
      oldWindowLoad()
    }
    Context.refreshAll()
  }

  Waypoint.requestAnimationFrame = function(callback) {
    var requestFn = window.requestAnimationFrame ||
      window.mozRequestAnimationFrame ||
      window.webkitRequestAnimationFrame ||
      requestAnimationFrameShim
    requestFn.call(window, callback)
  }
  Waypoint.Context = Context
}())
;(function() {
  'use strict'

  function byTriggerPoint(a, b) {
    return a.triggerPoint - b.triggerPoint
  }

  function byReverseTriggerPoint(a, b) {
    return b.triggerPoint - a.triggerPoint
  }

  var groups = {
    vertical: {},
    horizontal: {}
  }
  var Waypoint = window.Waypoint
  function Group(options) {
    this.name = options.name
    this.axis = options.axis
    this.id = this.name + '-' + this.axis
    this.waypoints = []
    this.clearTriggerQueues()
    groups[this.axis][this.name] = this
  }
  Group.prototype.add = function(waypoint) {
    this.waypoints.push(waypoint)
  }
  Group.prototype.clearTriggerQueues = function() {
    this.triggerQueues = {
      up: [],
      down: [],
      left: [],
      right: []
    }
  }
  Group.prototype.flushTriggers = function() {
    for (var direction in this.triggerQueues) {
      var waypoints = this.triggerQueues[direction]
      var reverse = direction === 'up' || direction === 'left'
      waypoints.sort(reverse ? byReverseTriggerPoint : byTriggerPoint)
      for (var i = 0, end = waypoints.length; i < end; i += 1) {
        var waypoint = waypoints[i]
        if (waypoint.options.continuous || i === waypoints.length - 1) {
          waypoint.trigger([direction])
        }
      }
    }
    this.clearTriggerQueues()
  }
  Group.prototype.next = function(waypoint) {
    this.waypoints.sort(byTriggerPoint)
    var index = Waypoint.Adapter.inArray(waypoint, this.waypoints)
    var isLast = index === this.waypoints.length - 1
    return isLast ? null : this.waypoints[index + 1]
  }
  Group.prototype.previous = function(waypoint) {
    this.waypoints.sort(byTriggerPoint)
    var index = Waypoint.Adapter.inArray(waypoint, this.waypoints)
    return index ? this.waypoints[index - 1] : null
  }
  Group.prototype.queueTrigger = function(waypoint, direction) {
    this.triggerQueues[direction].push(waypoint)
  }
  Group.prototype.remove = function(waypoint) {
    var index = Waypoint.Adapter.inArray(waypoint, this.waypoints)
    if (index > -1) {
      this.waypoints.splice(index, 1)
    }
  }
  Group.prototype.first = function() {
    return this.waypoints[0]
  }
  Group.prototype.last = function() {
    return this.waypoints[this.waypoints.length - 1]
  }
  Group.findOrCreate = function(options) {
    return groups[options.axis][options.name] || new Group(options)
  }

  Waypoint.Group = Group
}())
;(function() {
  'use strict'

  var $ = window.jQuery
  var Waypoint = window.Waypoint

  function JQueryAdapter(element) {
    this.$element = $(element)
  }

  $.each([
    'innerHeight',
    'innerWidth',
    'off',
    'offset',
    'on',
    'outerHeight',
    'outerWidth',
    'scrollLeft',
    'scrollTop'
  ], function(i, method) {
    JQueryAdapter.prototype[method] = function() {
      var args = Array.prototype.slice.call(arguments)
      return this.$element[method].apply(this.$element, args)
    }
  })

  $.each([
    'extend',
    'inArray',
    'isEmptyObject'
  ], function(i, method) {
    JQueryAdapter[method] = $[method]
  })

  Waypoint.adapters.push({
    name: 'jquery',
    Adapter: JQueryAdapter
  })
  Waypoint.Adapter = JQueryAdapter
}())
;(function() {
  'use strict'

  var Waypoint = window.Waypoint

  function createExtension(framework) {
    return function() {
      var waypoints = []
      var overrides = arguments[0]

      if (framework.isFunction(arguments[0])) {
        overrides = framework.extend({}, arguments[1])
        overrides.handler = arguments[0]
      }

      this.each(function() {
        var options = framework.extend({}, overrides, {
          element: this
        })
        if (typeof options.context === 'string') {
          options.context = framework(this).closest(options.context)[0]
        }
        waypoints.push(new Waypoint(options))
      })

      return waypoints
    }
  }

  if (window.jQuery) {
    window.jQuery.fn.waypoint = createExtension(window.jQuery)
  }
  if (window.Zepto) {
    window.Zepto.fn.waypoint = createExtension(window.Zepto)
  }
}())
;/**
 * vivus - JavaScript library to make drawing animation on SVG
 * @version v0.3.1
 * @link https://github.com/maxwellito/vivus
 * @license MIT
 */
"use strict";!function(t,e){function r(r){if("undefined"==typeof r)throw new Error('Pathformer [constructor]: "element" parameter is required');if(r.constructor===String&&(r=e.getElementById(r),!r))throw new Error('Pathformer [constructor]: "element" parameter is not related to an existing ID');if(!(r.constructor instanceof t.SVGElement||/^svg$/i.test(r.nodeName)))throw new Error('Pathformer [constructor]: "element" parameter must be a string or a SVGelement');this.el=r,this.scan(r)}function n(t,e,r){this.isReady=!1,this.setElement(t,e),this.setOptions(e),this.setCallback(r),this.isReady&&this.init()}r.prototype.TYPES=["line","ellipse","circle","polygon","polyline","rect"],r.prototype.ATTR_WATCH=["cx","cy","points","r","rx","ry","x","x1","x2","y","y1","y2"],r.prototype.scan=function(t){for(var e,r,n,i,a=t.querySelectorAll(this.TYPES.join(",")),o=0;o<a.length;o++)r=a[o],e=this[r.tagName.toLowerCase()+"ToPath"],n=e(this.parseAttr(r.attributes)),i=this.pathMaker(r,n),r.parentNode.replaceChild(i,r)},r.prototype.lineToPath=function(t){var e={};return e.d="M"+t.x1+","+t.y1+"L"+t.x2+","+t.y2,e},r.prototype.rectToPath=function(t){var e={},r=parseFloat(t.x)||0,n=parseFloat(t.y)||0,i=parseFloat(t.width)||0,a=parseFloat(t.height)||0;return e.d="M"+r+" "+n+" ",e.d+="L"+(r+i)+" "+n+" ",e.d+="L"+(r+i)+" "+(n+a)+" ",e.d+="L"+r+" "+(n+a)+" Z",e},r.prototype.polylineToPath=function(t){var e,r,n={},i=t.points.trim().split(" ");if(-1===t.points.indexOf(",")){var a=[];for(e=0;e<i.length;e+=2)a.push(i[e]+","+i[e+1]);i=a}for(r="M"+i[0],e=1;e<i.length;e++)-1!==i[e].indexOf(",")&&(r+="L"+i[e]);return n.d=r,n},r.prototype.polygonToPath=function(t){var e=r.prototype.polylineToPath(t);return e.d+="Z",e},r.prototype.ellipseToPath=function(t){var e=t.cx-t.rx,r=t.cy,n=parseFloat(t.cx)+parseFloat(t.rx),i=t.cy,a={};return a.d="M"+e+","+r+"A"+t.rx+","+t.ry+" 0,1,1 "+n+","+i+"A"+t.rx+","+t.ry+" 0,1,1 "+e+","+i,a},r.prototype.circleToPath=function(t){var e={},r=t.cx-t.r,n=t.cy,i=parseFloat(t.cx)+parseFloat(t.r),a=t.cy;return e.d="M"+r+","+n+"A"+t.r+","+t.r+" 0,1,1 "+i+","+a+"A"+t.r+","+t.r+" 0,1,1 "+r+","+a,e},r.prototype.pathMaker=function(t,r){var n,i,a=e.createElementNS("http://www.w3.org/2000/svg","path");for(n=0;n<t.attributes.length;n++)i=t.attributes[n],-1===this.ATTR_WATCH.indexOf(i.name)&&a.setAttribute(i.name,i.value);for(n in r)a.setAttribute(n,r[n]);return a},r.prototype.parseAttr=function(t){for(var e,r={},n=0;n<t.length;n++){if(e=t[n],-1!==this.ATTR_WATCH.indexOf(e.name)&&-1!==e.value.indexOf("%"))throw new Error("Pathformer [parseAttr]: a SVG shape got values in percentage. This cannot be transformed into 'path' tags. Please use 'viewBox'.");r[e.name]=e.value}return r};var i,a,o;n.LINEAR=function(t){return t},n.EASE=function(t){return-Math.cos(t*Math.PI)/2+.5},n.EASE_OUT=function(t){return 1-Math.pow(1-t,3)},n.EASE_IN=function(t){return Math.pow(t,3)},n.EASE_OUT_BOUNCE=function(t){var e=-Math.cos(.5*t*Math.PI)+1,r=Math.pow(e,1.5),n=Math.pow(1-t,2),i=-Math.abs(Math.cos(2.5*r*Math.PI))+1;return 1-n+i*n},n.prototype.setElement=function(r,n){if("undefined"==typeof r)throw new Error('Vivus [constructor]: "element" parameter is required');if(r.constructor===String&&(r=e.getElementById(r),!r))throw new Error('Vivus [constructor]: "element" parameter is not related to an existing ID');if(this.parentEl=r,n&&n.file){var i=e.createElement("object");i.setAttribute("type","image/svg+xml"),i.setAttribute("data",n.file),i.setAttribute("built-by-vivus","true"),r.appendChild(i),r=i}switch(r.constructor){case t.SVGSVGElement:case t.SVGElement:this.el=r,this.isReady=!0;break;case t.HTMLObjectElement:var a,o;o=this,a=function(t){if(!o.isReady){if(o.el=r.contentDocument&&r.contentDocument.querySelector("svg"),!o.el&&t)throw new Error("Vivus [constructor]: object loaded does not contain any SVG");return o.el?(r.getAttribute("built-by-vivus")&&(o.parentEl.insertBefore(o.el,r),o.parentEl.removeChild(r),o.el.setAttribute("width","100%"),o.el.setAttribute("height","100%")),o.isReady=!0,o.init(),!0):void 0}},a()||r.addEventListener("load",a);break;default:throw new Error('Vivus [constructor]: "element" parameter is not valid (or miss the "file" attribute)')}},n.prototype.setOptions=function(e){var r=["delayed","async","oneByOne","scenario","scenario-sync"],i=["inViewport","manual","autostart"];if(void 0!==e&&e.constructor!==Object)throw new Error('Vivus [constructor]: "options" parameter must be an object');if(e=e||{},e.type&&-1===r.indexOf(e.type))throw new Error("Vivus [constructor]: "+e.type+" is not an existing animation `type`");if(this.type=e.type||r[0],e.start&&-1===i.indexOf(e.start))throw new Error("Vivus [constructor]: "+e.start+" is not an existing `start` option");if(this.start=e.start||i[0],this.isIE=-1!==t.navigator.userAgent.indexOf("MSIE")||-1!==t.navigator.userAgent.indexOf("Trident/")||-1!==t.navigator.userAgent.indexOf("Edge/"),this.duration=o(e.duration,120),this.delay=o(e.delay,null),this.dashGap=o(e.dashGap,1),this.forceRender=e.hasOwnProperty("forceRender")?!!e.forceRender:this.isIE,this.selfDestroy=!!e.selfDestroy,this.onReady=e.onReady,this.frameLength=this.currentFrame=this.map=this.delayUnit=this.speed=this.handle=null,this.ignoreInvisible=e.hasOwnProperty("ignoreInvisible")?!!e.ignoreInvisible:!1,this.animTimingFunction=e.animTimingFunction||n.LINEAR,this.pathTimingFunction=e.pathTimingFunction||n.LINEAR,this.delay>=this.duration)throw new Error("Vivus [constructor]: delay must be shorter than duration")},n.prototype.setCallback=function(t){if(t&&t.constructor!==Function)throw new Error('Vivus [constructor]: "callback" parameter must be a function');this.callback=t||function(){}},n.prototype.mapping=function(){var e,r,n,i,a,s,h,u;for(u=s=h=0,r=this.el.querySelectorAll("path"),e=0;e<r.length;e++)n=r[e],this.isInvisible(n)||(a={el:n,length:Math.ceil(n.getTotalLength())},isNaN(a.length)?t.console&&console.warn&&console.warn("Vivus [mapping]: cannot retrieve a path element length",n):(this.map.push(a),n.style.strokeDasharray=a.length+" "+(a.length+2*this.dashGap),n.style.strokeDashoffset=a.length+this.dashGap,a.length+=this.dashGap,s+=a.length,this.renderPath(e)));for(s=0===s?1:s,this.delay=null===this.delay?this.duration/3:this.delay,this.delayUnit=this.delay/(r.length>1?r.length-1:1),e=0;e<this.map.length;e++){switch(a=this.map[e],this.type){case"delayed":a.startAt=this.delayUnit*e,a.duration=this.duration-this.delay;break;case"oneByOne":a.startAt=h/s*this.duration,a.duration=a.length/s*this.duration;break;case"async":a.startAt=0,a.duration=this.duration;break;case"scenario-sync":n=a.el,i=this.parseAttr(n),a.startAt=u+(o(i["data-delay"],this.delayUnit)||0),a.duration=o(i["data-duration"],this.duration),u=void 0!==i["data-async"]?a.startAt:a.startAt+a.duration,this.frameLength=Math.max(this.frameLength,a.startAt+a.duration);break;case"scenario":n=a.el,i=this.parseAttr(n),a.startAt=o(i["data-start"],this.delayUnit)||0,a.duration=o(i["data-duration"],this.duration),this.frameLength=Math.max(this.frameLength,a.startAt+a.duration)}h+=a.length,this.frameLength=this.frameLength||this.duration}},n.prototype.drawer=function(){var t=this;this.currentFrame+=this.speed,this.currentFrame<=0?(this.stop(),this.reset(),this.callback(this)):this.currentFrame>=this.frameLength?(this.stop(),this.currentFrame=this.frameLength,this.trace(),this.selfDestroy&&this.destroy(),this.callback(this)):(this.trace(),this.handle=i(function(){t.drawer()}))},n.prototype.trace=function(){var t,e,r,n;for(n=this.animTimingFunction(this.currentFrame/this.frameLength)*this.frameLength,t=0;t<this.map.length;t++)r=this.map[t],e=(n-r.startAt)/r.duration,e=this.pathTimingFunction(Math.max(0,Math.min(1,e))),r.progress!==e&&(r.progress=e,r.el.style.strokeDashoffset=Math.floor(r.length*(1-e)),this.renderPath(t))},n.prototype.renderPath=function(t){if(this.forceRender&&this.map&&this.map[t]){var e=this.map[t],r=e.el.cloneNode(!0);e.el.parentNode.replaceChild(r,e.el),e.el=r}},n.prototype.init=function(){this.frameLength=0,this.currentFrame=0,this.map=[],new r(this.el),this.mapping(),this.starter(),this.onReady&&this.onReady(this)},n.prototype.starter=function(){switch(this.start){case"manual":return;case"autostart":this.play();break;case"inViewport":var e=this,r=function(){e.isInViewport(e.parentEl,1)&&(e.play(),t.removeEventListener("scroll",r))};t.addEventListener("scroll",r),r()}},n.prototype.getStatus=function(){return 0===this.currentFrame?"start":this.currentFrame===this.frameLength?"end":"progress"},n.prototype.reset=function(){return this.setFrameProgress(0)},n.prototype.finish=function(){return this.setFrameProgress(1)},n.prototype.setFrameProgress=function(t){return t=Math.min(1,Math.max(0,t)),this.currentFrame=Math.round(this.frameLength*t),this.trace(),this},n.prototype.play=function(t){if(t&&"number"!=typeof t)throw new Error("Vivus [play]: invalid speed");return this.speed=t||1,this.handle||this.drawer(),this},n.prototype.stop=function(){return this.handle&&(a(this.handle),this.handle=null),this},n.prototype.destroy=function(){this.stop();var t,e;for(t=0;t<this.map.length;t++)e=this.map[t],e.el.style.strokeDashoffset=null,e.el.style.strokeDasharray=null,this.renderPath(t)},n.prototype.isInvisible=function(t){var e,r=t.getAttribute("data-ignore");return null!==r?"false"!==r:this.ignoreInvisible?(e=t.getBoundingClientRect(),!e.width&&!e.height):!1},n.prototype.parseAttr=function(t){var e,r={};if(t&&t.attributes)for(var n=0;n<t.attributes.length;n++)e=t.attributes[n],r[e.name]=e.value;return r},n.prototype.isInViewport=function(t,e){var r=this.scrollY(),n=r+this.getViewportH(),i=t.getBoundingClientRect(),a=i.height,o=r+i.top,s=o+a;return e=e||0,n>=o+a*e&&s>=r},n.prototype.docElem=t.document.documentElement,n.prototype.getViewportH=function(){var e=this.docElem.clientHeight,r=t.innerHeight;return r>e?r:e},n.prototype.scrollY=function(){return t.pageYOffset||this.docElem.scrollTop},i=function(){return t.requestAnimationFrame||t.webkitRequestAnimationFrame||t.mozRequestAnimationFrame||t.oRequestAnimationFrame||t.msRequestAnimationFrame||function(e){return t.setTimeout(e,1e3/60)}}(),a=function(){return t.cancelAnimationFrame||t.webkitCancelAnimationFrame||t.mozCancelAnimationFrame||t.oCancelAnimationFrame||t.msCancelAnimationFrame||function(e){return t.clearTimeout(e)}}(),o=function(t,e){var r=parseInt(t,10);return r>=0?r:e},"function"==typeof define&&define.amd?define([],function(){return n}):"object"==typeof exports?module.exports=n:t.Vivus=n}(window,document);var czrapp = czrapp || {};
(function($, czrapp) {
      czrapp._printLog = function( log ) {
            var _render = function() {
                  return $.Deferred( function() {
                        var dfd = this;
                        $.when( $('#footer').before( $('<div/>', { id : "bulklog" }) ) ).done( function() {
                              $('#bulklog').css({
                                    position: 'fixed',
                                    'z-index': '99999',
                                    'font-size': '0.8em',
                                    color: '#000',
                                    padding: '5%',
                                    width: '90%',
                                    height: '20%',
                                    overflow: 'hidden',
                                    bottom: '0',
                                    left: '0',
                                    background: 'yellow'
                              });

                              dfd.resolve();
                        });
                  }).promise();
                },
                _print = function() {
                      $('#bulklog').prepend('<p>' + czrapp._prettyfy( { consoleArguments : [ log ], prettyfy : false } ) + '</p>');
                };

            if ( 1 != $('#bulk-log').length ) {
                _render().done( _print );
            } else {
                _print();
            }
      };


      czrapp._truncate = function( string , length ){
            length = length || 150;
            if ( ! _.isString( string ) )
              return '';
            return string.length > length ? string.substr( 0, length - 1 ) : string;
      };
      czrapp._prettyfy = function( args ) {
            var _defaults = {
                  bgCol : '#5ed1f5',
                  textCol : '#000',
                  consoleArguments : [],
                  prettyfy : true
            };
            args = _.extend( _defaults, args );

            var _toArr = Array.from( args.consoleArguments );
            if ( ! _.isEmpty( _.filter( _toArr, function( it ) { return ! _.isString( it ); } ) ) ) {
                  _toArr =  JSON.stringify( _toArr );
            } else {
                  _toArr = _toArr.join(' ');
            }
            if ( args.prettyfy )
              return [
                    '%c ' + czrapp._truncate( _toArr ),
                    [ 'background:' + args.bgCol, 'color:' + args.textCol, 'display: block;' ].join(';')
              ];
            else
              return czrapp._truncate( _toArr );
      };
      czrapp.consoleLog = function() {
            if ( ! czrapp.localized.isDevMode )
              return;
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { consoleArguments : arguments } ) );
      };

      czrapp.errorLog = function() {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, czrapp._prettyfy( { bgCol : '#ffd5a0', textCol : '#000', consoleArguments : arguments } ) );
      };
      czrapp.doAjax = function( queryParams ) {
            queryParams = queryParams || ( _.isObject( queryParams ) ? queryParams : {} );

            var ajaxUrl = queryParams.ajaxUrl || czrapp.localized.ajaxUrl,//the ajaxUrl can be specified when invoking doAjax
                nonce = czrapp.localized.frontNonce,//{ 'id' => 'HuFrontNonce', 'handle' => wp_create_nonce( 'hu-front-nonce' ) },
                dfd = $.Deferred(),
                _query_ = _.extend( {
                            action : '',
                            withNonce : false
                      },
                      queryParams
                );
            if ( "https:" == document.location.protocol ) {
                  ajaxUrl = ajaxUrl.replace( "http://", "https://" );
            }
            if ( _.isEmpty( _query_.action ) || ! _.isString( _query_.action ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper action provided' );
                  return dfd.resolve().promise();
            }
            _query_[ nonce.id ] = nonce.handle;
            if ( ! _.isObject( nonce ) || _.isUndefined( nonce.id ) || _.isUndefined( nonce.handle ) ) {
                  czrapp.errorLog( 'czrapp.doAjax : unproper nonce' );
                  return dfd.resolve().promise();
            }

            $.post( ajaxUrl, _query_ )
                  .done( function( _r ) {
                        if ( '0' === _r ||  '-1' === _r ) {
                              czrapp.errorLog( 'czrapp.doAjax : done ajax error for : ', _query_.action, _r );
                        }
                  })
                  .fail( function( _r ) { czrapp.errorLog( 'czrapp.doAjax : failed ajax error for : ', _query_.action, _r ); })
                  .always( function( _r ) { dfd.resolve( _r ); });
            return dfd.promise();
      };
})(jQuery, czrapp);
(function($, czrapp) {
      czrapp.isKeydownButNotEnterEvent = function ( event ) {
        return ( 'keydown' === event.type && 13 !== event.which );
      };
      czrapp.setupDOMListeners = function( event_map , args, instance ) {
              var _defaultArgs = {
                        model : {},
                        dom_el : {}
                  };

              if ( _.isUndefined( instance ) || ! _.isObject( instance ) ) {
                    czrapp.errorLog( 'setupDomListeners : instance should be an object', args );
                    return;
              }
              if ( ! _.isArray( event_map ) ) {
                    czrapp.errorLog( 'setupDomListeners : event_map should be an array', args );
                    return;
              }
              if ( ! _.isObject( args ) ) {
                    czrapp.errorLog( 'setupDomListeners : args should be an object', event_map );
                    return;
              }

              args = _.extend( _defaultArgs, args );
              if ( ! ( args.dom_el instanceof jQuery ) || 1 != args.dom_el.length ) {
                    czrapp.errorLog( 'setupDomListeners : dom element should be an existing dom element', args );
                    return;
              }
              _.map( event_map , function( _event ) {
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }
                    if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                          czrapp.errorLog( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                          return;
                    }
                    args.dom_el.on( _event.trigger , _event.selector, function( e, event_params ) {
                          e.stopPropagation();
                          if ( czrapp.isKeydownButNotEnterEvent( e ) ) {
                            return;
                          }
                          e.preventDefault(); // Keep this AFTER the key filter above
                          var actionsParams = $.extend( true, {}, args );
                          if ( _.has( actionsParams, 'model') && _.has( actionsParams.model, 'id') ) {
                                if ( _.has( instance, 'get' ) )
                                  actionsParams.model = instance();
                                else
                                  actionsParams.model = instance.getModel( actionsParams.model.id );
                          }
                          $.extend( actionsParams, { event : _event, dom_event : e } );
                          $.extend( actionsParams, event_params );
                          if ( ! _.has( actionsParams, 'event' ) || ! _.has( actionsParams.event, 'actions' ) ) {
                                czrapp.errorLog( 'executeEventActionChain : missing obj.event or obj.event.actions' );
                                return;
                          }
                          try { czrapp.executeEventActionChain( actionsParams, instance ); } catch( er ) {
                                czrapp.errorLog( 'In setupDOMListeners : problem when trying to fire actions : ' + actionsParams.event.actions );
                                czrapp.errorLog( 'Error : ' + er );
                          }
                    });//.on()
              });//_.map()
      };//setupDomListeners
      czrapp.executeEventActionChain = function( args, instance ) {
              if ( 'function' === typeof( args.event.actions ) )
                return args.event.actions.call( instance, args );
              if ( ! _.isArray( args.event.actions ) )
                args.event.actions = [ args.event.actions ];
              var _break = false;
              _.map( args.event.actions, function( _cb ) {
                    if ( _break )
                      return;

                    if ( 'function' != typeof( instance[ _cb ] ) ) {
                          throw new Error( 'executeEventActionChain : the action : ' + _cb + ' has not been found when firing event : ' + args.event.selector );
                    }
                    var $_dom_el = ( _.has(args, 'dom_el') && -1 != args.dom_el.length ) ? args.dom_el : false;
                    if ( ! $_dom_el ) {
                          czrapp.errorLog( 'missing dom element');
                          return;
                    }
                    $_dom_el.trigger( 'before_' + _cb, _.omit( args, 'event' ) );
                    var _cb_return = instance[ _cb ].call( instance, args );
                    if ( false === _cb_return )
                      _break = true;
                    $_dom_el.trigger( 'after_' + _cb, _.omit( args, 'event' ) );
              });//_.map
      };
})(jQuery, czrapp);var czrapp = czrapp || {};
czrapp.methods = {};

(function( $ ){
      var ctor, inherits, slice = Array.prototype.slice;
      ctor = function() {};
      inherits = function( parent, protoProps, staticProps ) {
        var child;
        if ( protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
          child = protoProps.constructor;
        } else {
          child = function() {
            var result = parent.apply( this, arguments );
            return result;
          };
        }
        $.extend( child, parent );
        ctor.prototype  = parent.prototype;
        child.prototype = new ctor();
        if ( protoProps )
          $.extend( child.prototype, protoProps );
        if ( staticProps )
          $.extend( child, staticProps );
        child.prototype.constructor = child;
        child.__super__ = parent.prototype;

        return child;
      };
      czrapp.Class = function( applicator, argsArray, options ) {
        var magic, args = arguments;

        if ( applicator && argsArray && czrapp.Class.applicator === applicator ) {
          args = argsArray;
          $.extend( this, options || {} );
        }

        magic = this;
        if ( this.instance ) {
          magic = function() {
            return magic.instance.apply( magic, arguments );
          };

          $.extend( magic, this );
        }

        magic.initialize.apply( magic, args );
        return magic;
      };
      czrapp.Class.extend = function( protoProps, classProps ) {
        var child = inherits( this, protoProps, classProps );
        child.extend = this.extend;
        return child;
      };

      czrapp.Class.applicator = {};
      czrapp.Class.prototype.initialize = function() {};
      czrapp.Class.prototype.extended = function( constructor ) {
        var proto = this;

        while ( typeof proto.constructor !== 'undefined' ) {
          if ( proto.constructor === constructor )
            return true;
          if ( typeof proto.constructor.__super__ === 'undefined' )
            return false;
          proto = proto.constructor.__super__;
        }
        return false;
      };
      czrapp.Events = {
        trigger: function( id ) {
          if ( this.topics && this.topics[ id ] )
            this.topics[ id ].fireWith( this, slice.call( arguments, 1 ) );
          return this;
        },

        bind: function( id ) {
          this.topics = this.topics || {};
          this.topics[ id ] = this.topics[ id ] || $.Callbacks();
          this.topics[ id ].add.apply( this.topics[ id ], slice.call( arguments, 1 ) );
          return this;
        },

        unbind: function( id ) {
          if ( this.topics && this.topics[ id ] )
            this.topics[ id ].remove.apply( this.topics[ id ], slice.call( arguments, 1 ) );
          return this;
        }
      };
      czrapp.Value = czrapp.Class.extend({
        initialize: function( initial, options ) {
          this._value = initial; // @todo: potentially change this to a this.set() call.
          this.callbacks = $.Callbacks();
          this._dirty = false;

          $.extend( this, options || {} );

          this.set = $.proxy( this.set, this );
        },
        instance: function() {
          return arguments.length ? this.set.apply( this, arguments ) : this.get();
        },
        get: function() {
          return this._value;
        },
        set: function( to, o ) {
              var from = this._value, dfd = $.Deferred(), self = this, _promises = [];

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              args = _.extend( { silent : false }, _.isObject( o ) ? o : {} );
              if ( null === to || _.isEqual( from, to ) ) {
                    return dfd.resolveWith( self, [ to, from, o ] ).promise();
              }

              this._value = to;
              this._dirty = true;
              if ( true === args.silent ) {
                    return dfd.resolveWith( self, [ to, from, o ] ).promise();
              }

              if ( this._deferreds ) {
                    _.each( self._deferreds, function( _prom ) {
                          _promises.push( _prom.apply( null, [ to, from, o ] ) );
                    });

                    $.when.apply( null, _promises )
                          .fail( function() { api.errorLog( 'A deferred callback failed in api.Value::set()'); })
                          .then( function() {
                                self.callbacks.fireWith( self, [ to, from, o ] );
                                dfd.resolveWith( self, [ to, from, o ] );
                          });
              } else {
                    this.callbacks.fireWith( this, [ to, from, o ] );
                    return dfd.resolveWith( self, [ to, from, o ] ).promise( self );
              }
              return dfd.promise( self );
        },
        silent_set : function( to, dirtyness ) {
              var from = this._value,
                  _save_state = api.state('saved')();

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              if ( null === to || _.isEqual( from, to ) ) {
                return this;
              }

              this._value = to;
              this._dirty = ( _.isUndefined( dirtyness ) || ! _.isBoolean( dirtyness ) ) ? this._dirty : dirtyness;

              this.callbacks.fireWith( this, [ to, from, { silent : true } ] );
              api.state('saved')( _save_state );
              return this;
        },

        _setter: function( to ) {
          return to;
        },

        setter: function( callback ) {
          var from = this.get();
          this._setter = callback;
          this._value = null;
          this.set( from );
          return this;
        },

        resetSetter: function() {
          this._setter = this.constructor.prototype._setter;
          this.set( this.get() );
          return this;
        },

        validate: function( value ) {
          return value;
        },
        bind: function() {
            var self = this,
                _isDeferred = false,
                _cbs = [];

            $.each( arguments, function( _key, _arg ) {
                  if ( ! _isDeferred )
                    _isDeferred = _.isObject( _arg  ) && _arg.deferred;
                  if ( _.isFunction( _arg ) )
                    _cbs.push( _arg );
            });

            if ( _isDeferred ) {
                  self._deferreds = self._deferreds || [];
                  _.each( _cbs, function( _cb ) {
                        if ( ! _.contains( _cb, self._deferreds ) )
                          self._deferreds.push( _cb );
                  });
            } else {
                  self.callbacks.add.apply( self.callbacks, arguments );
            }
            return this;
        },
        unbind: function() {
          this.callbacks.remove.apply( this.callbacks, arguments );
          return this;
        },
      });
      czrapp.Values = czrapp.Class.extend({
        defaultConstructor: czrapp.Value,

        initialize: function( options ) {
          $.extend( this, options || {} );

          this._value = {};
          this._deferreds = {};
        },
        instance: function( id ) {
          if ( arguments.length === 1 )
            return this.value( id );

          return this.when.apply( this, arguments );
        },
        value: function( id ) {
          return this._value[ id ];
        },
        has: function( id ) {
          return typeof this._value[ id ] !== 'undefined';
        },
        add: function( id, value ) {
          if ( this.has( id ) )
            return this.value( id );

          this._value[ id ] = value;
          value.parent = this;
          if ( value.extended( czrapp.Value ) )
            value.bind( this._change );

          this.trigger( 'add', value );
          if ( this._deferreds[ id ] )
            this._deferreds[ id ].resolve();

          return this._value[ id ];
        },
        create: function( id ) {
          return this.add( id, new this.defaultConstructor( czrapp.Class.applicator, slice.call( arguments, 1 ) ) );
        },
        each: function( callback, context ) {
          context = typeof context === 'undefined' ? this : context;

          $.each( this._value, function( key, obj ) {
            callback.call( context, obj, key );
          });
        },
        remove: function( id ) {
          var value;

          if ( this.has( id ) ) {
            value = this.value( id );
            this.trigger( 'remove', value );
            if ( value.extended( czrapp.Value ) )
              value.unbind( this._change );
            delete value.parent;
          }

          delete this._value[ id ];
          delete this._deferreds[ id ];
        },
        when: function() {
          var self = this,
            ids  = slice.call( arguments ),
            dfd  = $.Deferred();
          if ( $.isFunction( ids[ ids.length - 1 ] ) )
            dfd.done( ids.pop() );
          $.when.apply( $, $.map( ids, function( id ) {
            if ( self.has( id ) )
              return;
            return self._deferreds[ id ] || $.Deferred();
          })).done( function() {
            var values = $.map( ids, function( id ) {
                return self( id );
              });
            if ( values.length !== ids.length ) {
              self.when.apply( self, ids ).done( function() {
                dfd.resolveWith( self, values );
              });
              return;
            }

            dfd.resolveWith( self, values );
          });

          return dfd.promise();
        },
        _change: function() {
          this.parent.trigger( 'change', this );
        }
      });
      $.extend( czrapp.Values.prototype, czrapp.Events );

})( jQuery );//@global CZRParams
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {
            cacheProp : function() {
                  var self = this;
                  $.extend( czrapp, {
                        $_window         : $(window),
                        $_html           : $('html'),
                        $_body           : $('body'),
                        $_wpadminbar     : $('#wpadminbar'),
                        $_header       : $('.tc-header'),
                        localized        : "undefined" != typeof(CZRParams) && CZRParams ? CZRParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice()//store the initial device
                  });
            },
            isResponsive : function() {
                  return this.matchMedia(991);
            },
            getDevice : function() {
                  var _devices = {
                        desktop : 991,
                        tablet : 767,
                        smartphone : 575
                      },
                      _current_device = 'desktop',
                      that = this;


                  _.map( _devices, function( max_width, _dev ){
                        if ( that.matchMedia( max_width ) )
                          _current_device = _dev;
                  } );

                  return _current_device;
            },

            matchMedia : function( _maxWidth ) {
                  if ( window.matchMedia )
                    return ( window.matchMedia("(max-width: "+_maxWidth+"px)").matches );
                  $_window = czrapp.$_window || $(window);
                  return $_window.width() <= ( _maxWidth - 15 );
            },

            emit : function( cbs, args ) {
                  cbs = _.isArray(cbs) ? cbs : [cbs];
                  var self = this;
                  _.map( cbs, function(cb) {
                        if ( 'function' == typeof(self[cb]) ) {
                              args = 'undefined' == typeof( args ) ? Array() : args ;
                              self[cb].apply(self, args );
                              czrapp.trigger( cb, _.object( _.keys(args), args ) );
                        }
                  });//_.map
            },

            triggerSimpleLoad : function( $_imgs ) {
                  if ( 0 === $_imgs.length )
                    return;

                  $_imgs.map( function( _ind, _img ) {
                    $(_img).load( function () {
                      $(_img).trigger('simple_load');
                    });//end load
                    if ( $(_img)[0] && $(_img)[0].complete )
                      $(_img).load();
                  } );//end map
            },//end of fn

            isUserLogged     : function() {
                  return czrapp.$_body.hasClass('logged-in') || 0 !== czrapp.$_wpadminbar.length;
            },

            isSelectorAllowed : function( $_el, skip_selectors, requested_sel_type ) {
                  var sel_type = 'ids' == requested_sel_type ? 'id' : 'class',
                  _selsToSkip   = skip_selectors[requested_sel_type];
                  if ( 'object' != typeof(skip_selectors) || ! skip_selectors[requested_sel_type] || ! $.isArray( skip_selectors[requested_sel_type] ) || 0 === skip_selectors[requested_sel_type].length )
                    return true;
                  if ( $_el.parents( _selsToSkip.map( function( _sel ){ return 'id' == sel_type ? '#' + _sel : '.' + _sel; } ).join(',') ).length > 0 )
                    return false;
                  if ( ! $_el.attr( sel_type ) )
                    return true;

                  var _elSels       = $_el.attr( sel_type ).split(' '),
                      _filtered     = _elSels.filter( function(classe) { return -1 != $.inArray( classe , _selsToSkip ) ;});
                  return 0 === _filtered.length;
            },
            _isMobile : function() {
                  return ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 768px)' ).matches ) || ( this._isCustomizing() && 'desktop' != this.previewDevice() );
            },
            _isCustomizing : function() {
                  return czrapp.$_body.hasClass('is-customizing') || ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize );
            },
            _has_iframe : function ( $_elements ) {
                  var that = this,
                      to_return = [];
                  _.each( $_elements, function( $_el, container ){
                        if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
                          to_return.push(container);
                  });
                  return to_return;
            },
            isInWindow : function( $_el, threshold ) {
                  if ( ! ( $_el instanceof $ ) )
                    return;
                  if ( threshold && ! _.isNumber( threshold ) )
                    return;

                  var wt = $(window).scrollTop(),
                      wb = wt + $(window).height(),
                      it  = $_el.offset().top,
                      ib  = it + $_el.height(),
                      th = threshold || 0;

                  return ib >= wt - th && it <= wb + th;
            },
            fireMeWhenStoppedScrolling : function( params ) {
                  params = _.extend( {
                      delay : 3000,
                      func : '',
                      instance : {},
                      args : []
                  }, params );

                  if ( ! _.isFunction( params.func ) )
                    return;
                  var _timer_ = function() {
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                  dfd.resolve();
                              }, params.delay );
                        }).done( function() {
                              if ( czrapp.userXP.isScrolling() ) {
                                    _timer_();
                              } else {
                                    params.func.apply( params.instance, params.args );
                              }
                        });
                  };
                  _timer_();
            },
            scriptLoadingStatus : {},
      };//_methods{}

      czrapp.methods.Base = czrapp.methods.Base || {};
      $.extend( czrapp.methods.Base , _methods );//$.extend

})(jQuery, czrapp);/***************************
* ADD BROWSER DETECT METHODS
****************************/
(function($, czrapp) {
  var _methods =  {
    addBrowserClassToBody : function() {
          if ( $.browser.chrome )
              czrapp.$_body.addClass("chrome");
          else if ( $.browser.webkit )
              czrapp.$_body.addClass("safari");
          if ( $.browser.mozilla )
              czrapp.$_body.addClass("mozilla");
          else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
              czrapp.$_body.addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));
          if ( czrapp.$_body.hasClass("ie") )
              czrapp.$_body.addClass($.browser.version);
    }
  };//_methods{}
  czrapp.methods.BrowserDetect = czrapp.methods.BrowserDetect || {};
  $.extend( czrapp.methods.BrowserDetect , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {
            centerImagesWithDelay : function( delay ) {
                  var self = this;
                  setTimeout( function(){ self.emit('centerImages'); }, delay || 50 );
            },


            centerInfinity : function() {

                  var centerInfiniteImagesModernStyle = function ( collection, _container ) {
                      var $_container  = $(_container);

                      if ( 'object' !== typeof collection || 1 > $_container.length )
                        return;
                      _.each( collection, function( elementSelector ) {

                            var $_imgsToSimpleLoad = $(  elementSelector + ' .js-centering', $_container ).centerImages( {
                                  enableCentering : 1,
                                  enableGoldenRatio : false,
                                  disableGRUnder : 0,//<= don't disable golden ratio when responsive,
                                  zeroTopAdjust: 0,
                                  setOpacityWhenCentered : false,//will set the opacity to 1
                                  oncustom : [ 'simple_load', 'smartload' ]
                            })
                            .find( 'img:not([src^="data"])' );
                            czrapp.methods.Base.triggerSimpleLoad( $_imgsToSimpleLoad );
                      });

                  };
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( 'success' == response.type && response.collection && response.container ) {
                              centerInfiniteImagesModernStyle(
                                  response.collection,
                                  '#'+response.container //_container
                              );
                        }
                  } );
            },
            imgSmartLoad : function() {
                  var smartLoadEnabled = 1 == czrapp.localized.imgSmartLoadEnabled,
                      _where           = czrapp.localized.imgSmartLoadOpts.parentSelectors.join();
                  if (  smartLoadEnabled ) {
                        $( _where ).imgSmartLoad(
                            _.size( czrapp.localized.imgSmartLoadOpts.opts ) > 0 ? czrapp.localized.imgSmartLoadOpts.opts : {}
                        );
                  }
                  if ( 1 == czrapp.localized.centerAllImg ) {
                        var self                   = this,
                            $_to_center;
                        if ( smartLoadEnabled ) {
                              $_to_center = $( _.filter( $( _where ).find('img'), function( img ) {
                                  return $(img).is(czrapp.localized.imgSmartLoadOpts.opts.excludeImg.join());
                                }) );
                        } else { //filter
                              $_to_center = $( _where ).find('img');
                        }

                        $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                                return $(img).hasClass('tc-holder-img');
                        }) );
                        setTimeout( function(){
                              self.triggerSimpleLoad( $_to_center_with_delay );
                        }, 800 );
                        self.triggerSimpleLoad( $_to_center );
                  }
            },
            centerImages : function() {
                  var $wrappersOfCenteredImagesCandidates = $('.widget-front .tc-thumbnail, .js-centering.entry-media__holder, .js-centering.entry-media__wrapper');
                  _css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  $wrappersOfCenteredImagesCandidates.each( function() {
                        $( this ).append(  _css_loader ).find('.czr-css-loader').fadeIn( 'slow');
                  });
                  $wrappersOfCenteredImagesCandidates.centerImages({
                        onInit : true,
                        enableCentering : 1,
                        oncustom : ['smartload', 'refresh-height', 'simple_load'],
                        enableGoldenRatio : false, //true
                        zeroTopAdjust: 0,
                        setOpacityWhenCentered : false,//will set the opacity to 1
                        addCenteredClassWithDelay : 50,
                        opacity : 1
                  });
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('.czr-css-loader').fadeOut( {
                          duration: 500,
                          done : function() { $(this).remove();}
                        } );
                  }, 300 );
                  var _mayBeForceOpacity = function( params ) {
                        params = _.extend( {
                              el : {},
                              delay : 0
                        }, _.isObject( params ) ? params : {} );

                        if ( 1 !== params.el.length  || ( params.el.hasClass( 'h-centered') || params.el.hasClass( 'v-centered') ) )
                          return;

                        _.delay( function() {
                              params.el.addClass( 'opacity-forced');
                        }, params.delay );

                  };
                  if ( czrapp.localized.imgSmartLoadEnabled ) {
                        $wrappersOfCenteredImagesCandidates.on( 'smartload', 'img' , function( ev ) {
                              if ( 1 != $( ev.target ).length )
                                return;
                              _mayBeForceOpacity( { el : $( ev.target ), delay : 200 } );
                        });
                  } else {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 100 } );
                        });
                  }
                  _.delay( function() {
                        $wrappersOfCenteredImagesCandidates.find('img').each( function() {
                              _mayBeForceOpacity( { el : $(this), delay : 0 } );
                        });
                  }, 1000 );
                  var $_fpuEl = $('.fpc-widget-front .fp-thumb-wrapper');
                  if ( 1 < $_fpuEl.length ) {
                        var _isFPUimgCentered = _.isUndefined( czrapp.localized.FPUImgCentered ) ? 1 == czrapp.localized.centerAllImg : 1 == czrapp.localized.FPUImgCentered;
                        $_fpuEl.centerImages( {
                            onInit : false,
                            enableCentering : _isFPUimgCentered,
                            enableGoldenRatio : false,
                            disableGRUnder : 0,//<= don't disable golden ratio when responsive
                            zeroTopAdjust : 0,
                            oncustom : ['smartload', 'simple_load', 'block_resized', 'fpu-recenter']
                        });
                        if ( ! czrapp.localized.imgSmartLoadEnabled ) {
                            czrapp.base.triggerSimpleLoad( $_fpuEl.find("img:not(.tc-holder-img)") );
                        } else {
                            $_fpuEl.find("img:not(.tc-holder-img)").each( function() {
                                    if ( $(this).data( 'czr-smart-loaded') ) {
                                        czrapp.base.triggerSimpleLoad( $(this) );
                                    }
                            });
                        }
                        if ( _isFPUimgCentered && ! czrapp.localized.imgSmartLoadEnabled ) {
                              var $_holder_img = $_fpuEl.find("img.tc-holder-img");
                              if ( 0 < $_holder_img.length ) {
                                  czrapp.base.triggerSimpleLoad( $_holder_img );
                                  setTimeout( function(){
                                        czrapp.base.triggerSimpleLoad( $_holder_img );
                                  }, 100 );
                              }
                        }
                  }//if ( 1 < $_fpuEl.length )
            },//center_images

            parallax : function() {
                  $( '.parallax-item' ).czrParallax();
                  $('.ham__navbar-toggler').on('click', function(){
                        setTimeout( function(){
                        Waypoint.refreshAll(); }, 400 ); }
                  );
            },
            czrMagnificPopup : function( $lightBoxCandidate, params ) {
                  if ( 1 > $lightBoxCandidate.length )
                    return;

                  var _scrollHandle = function() {},//abstract that we can unbind
                      _do = function() {
                        czrapp.$_window.unbind( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.magnificPopup ) {
                                $lightBoxCandidate.magnificPopup( params );
                        } else {
                              if ( czrapp.base.scriptLoadingStatus.czrMagnificPopup && 'pending' == czrapp.base.scriptLoadingStatus.czrMagnificPopup.state() ) {
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.done( function() {
                                          $lightBoxCandidate.magnificPopup( params );
                                    });
                                    return;
                              }
                              czrapp.base.scriptLoadingStatus.czrMagnificPopup = czrapp.base.scriptLoadingStatus.czrMagnificPopup || $.Deferred();
                              if ( $('head').find( '#czr-magnific-popup' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-magnific-popup',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/magnific-popup.min.css'
                                    }) );
                              }

                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/jquery-magnific-popup.min.js'),
                                    cache : true,// use the browser cached version when available
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.magnificPopup ) )
                                      return;
                                    czrapp.base.scriptLoadingStatus.czrMagnificPopup.resolve();
                                      $lightBoxCandidate.magnificPopup( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Magnific popup instantiation failed for candidate : '  + $lightBoxCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()
                  if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $lightBoxCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },

            lightBox : function() {
                  var self = this,
                      _arrowMarkup = '<span class="czr-carousel-control btn btn-skin-dark-shaded inverted mfp-arrow-%dir% icn-%dir%-open-big"></span>';
                  this.czrMagnificPopup( $( '[class*="grid-container__"]' ), {
                    delegate: 'a.expand-img', // child items selector, by clicking on it popup will open
                    type: 'image'
                  });
                  $( '.czr-gallery' ).each( function(){
                        self.czrMagnificPopup( $(this), {
                              delegate: '[data-lb-type="grouped-gallery"]', // child items selector, by clicking on it popup will open
                              type: 'image',
                              gallery: {
                                    enabled: true,
                                    arrowMarkup: _arrowMarkup
                              }
                        });
                  });
                  this.czrMagnificPopup( $('article .tc-content-inner'), {
                        delegate: '[data-lb-type="grouped-post"]',
                        type: 'image',
                        gallery: {
                             enabled: true,
                             arrowMarkup: _arrowMarkup
                        }
                  });
                  czrapp.$_body.on( 'click', '[class*="grid-container__"] .expand-img-gallery', function(e) {
                        e.preventDefault();

                        var $_expand_btn    = $( this ),
                            $_gallery_crsl  = $_expand_btn.closest( '.czr-carousel' );


                        if ( $_gallery_crsl.length < 1 )
                          return;

                        var _do = function() {
                              if ( ! $_gallery_crsl.data( 'mfp' ) ) {

                                    self.czrMagnificPopup( $_gallery_crsl, {
                                        delegate: '.carousel-cell img',
                                        type: 'image',
                                        gallery: {
                                          enabled: true,
                                          arrowMarkup: _arrowMarkup
                                        }
                                    });
                                    $_gallery_crsl.data( 'mfp', true );
                              }

                              if ( $_gallery_crsl.data( 'mfp' ) ) {
                                    $_gallery_crsl.find( '.is-selected img' ).trigger('click');
                              }
                        };
                        if ( 0 < $_gallery_crsl.find( '.flickity-slider').length ) {
                              _do();
                        } else {
                              _.delay( function() {
                                    _do();
                              }, 500 );//<= let the flickity slider be setup, because the slider is setup on click
                        }

                  });
            },

      };//_methods{}

      czrapp.methods.JQPlugins = {};
      $.extend( czrapp.methods.JQPlugins , _methods );


})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {

            initOnCzrReady : function() {
                  var self = this;

                  this.slidersSelectorMap = {
                        mainSlider : '[id^="customizr-slider-main"] .carousel-inner',
                        galleries : '.czr-gallery.czr-carousel .carousel-inner',
                        relatedPosts : '.grid-container__square-mini.carousel-inner'
                  };
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', '.czr-parallax-slider', self._parallax );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.mainSlider, function() {
                    $(this).find( '.carousel-caption .czrs-title' ).czrFitText(
                                1.5,//<=kompressor
                                {
                                      maxFontSize : 65,//the default max font-size
                                      minFontSize : 18,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-subtitle' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 35,//the default max font-size
                                      minFontSize : 15,
                                }
                    );
                    $(this).find( '.carousel-caption .czrs-cta-wrapper' ).czrFitText(
                                2,//<=kompressor
                                {
                                      maxFontSize : 18,//the default max font-size
                                      minFontSize : 12,
                                }
                    );
                  });
                  czrapp.$_body.on( 'select.flickity', '.czr-carousel .carousel-inner', self._slider_arrows_enable_toggler );
                  czrapp.$_body.on( 'czr-flickity-ready.flickity', self.slidersSelectorMap.galleries, self._move_background_link_inside );
                  czrapp.$_body.on( 'click prev.czr-carousel', '.czr-carousel-prev', function(e) { self._slider_arrows.apply( this , [ e, 'previous' ] );} );
                  czrapp.$_body.on( 'click next.czr-carousel', '.czr-carousel-next', function(e) { self._slider_arrows.apply( this , [ e, 'next' ] );} );
                  this.fireRelatedPostsCarousel();
                  this.scheduleGalleryCarousels();
                  this.fireMainSlider();
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( 'success' == response.type && response.collection && response.container ) {
                              if ( ! response.html || -1 === response.html.indexOf( 'czr-gallery' ) || -1 === response.html.indexOf( 'czr-carousel' ) ) {
                                    return;
                              }
                              self.scheduleGalleryCarousels();
                        }
                  } );
                  czrapp.$_body.on( 'before-endlessly-caching', function( e, params ) {
                        if ( ! _.isObject( params ) || _.isUndefined( params.candidates_for_caching || ! ( params.candidates_for_caching instanceof $ ) ) )
                          return;

                        params.candidates_for_caching.find( self.slidersSelectorMap.galleries ).each( function() {
                              if ( $(this).data('flickity') ) {
                                    var $_bg_link = $(this).find('.bg-link');
                                    $(this).closest('.entry-media__wrapper').prepend( $_bg_link );

                                    $(this).flickity( 'destroy' );
                                    $(this).find('.czr-css-loader').remove();
                              }
                        });
                  });
                  self._css_loader = '<div class="czr-css-loader czr-mr-loader" style="display:none"><div></div><div></div><div></div></div>';
                  czrapp.$_window.scroll( _.throttle( function() {
                        $( self.slidersSelectorMap.galleries ).each( function() {
                              if ( czrapp.base.isInWindow( $(this) ) ){
                                    $(this).trigger( 'czr-is-in-window', { el : $(this) } );
                              }
                        });
                  }, 50 ) );
            },//_init()
            czrFlickity : function( $_sliderCandidate, params ) {
                  if ( 1 > $_sliderCandidate.length )
                    return;

                  var _scrollHandle = function() {};//abstract that we can unbind
                  var _do = function() {
                        czrapp.$_window.unbind( 'scroll', _scrollHandle );

                        if ( 'function' == typeof $.fn.flickity ) {
                              if ( ! $_sliderCandidate.data( 'flickity' ) )
                                $_sliderCandidate.flickity( params );
                        } else {
                              if ( czrapp.base.scriptLoadingStatus.flickity && 'pending' == czrapp.base.scriptLoadingStatus.flickity.state() ) {
                                    czrapp.base.scriptLoadingStatus.flickity.done( function() {
                                          $_sliderCandidate.flickity( params );
                                    });
                                    return;
                              }
                              czrapp.base.scriptLoadingStatus.flickity = czrapp.base.scriptLoadingStatus.flickity || $.Deferred();
                              if ( $('head').find( '#czr-flickity' ).length < 1 ) {
                                    $('head').append( $('<link/>' , {
                                          rel : 'stylesheet',
                                          id : 'czr-flickity',
                                          type : 'text/css',
                                          href : czrapp.localized.assetsPath + 'css/flickity.min.css'
                                    }) );
                              }
                              $.ajax( {
                                    url : ( czrapp.localized.assetsPath + 'js/libs/flickity-pkgd.min.js'),
                                    cache : true,// use the browser cached version when availabl
                                    dataType: "script"
                              }).done(function() {
                                    if ( 'function' != typeof( $.fn.flickity ) )
                                      return;
                                    czrapp.base.scriptLoadingStatus.flickity.resolve();
                                    var activate = Flickity.prototype.activate;
                                    Flickity.prototype.activate = function() {
                                          if ( this.isActive ) {
                                            return;
                                          }
                                          activate.apply( this, arguments );
                                          this.dispatchEvent( 'czr-flickity-ready', null, this );
                                    };
                                    if ( ! $_sliderCandidate.data( 'flickity' ) )
                                      $_sliderCandidate.flickity( params );
                              }).fail( function() {
                                    czrapp.errorLog( 'Flickity instantiation failed for slider candidate : '  + $_sliderCandidate.attr( 'class' ) );
                              });
                        }
                  };//_do()
                  if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                        _do();
                  } else {
                        _scrollHandle = _.throttle( function() {
                              if ( czrapp.base.isInWindow( $_sliderCandidate ) ) {
                                    _do();
                              }
                        }, 100 );
                        czrapp.$_window.on( 'scroll', _scrollHandle );
                  }
            },
            scheduleGalleryCarousels : function( $_gallery_container ) {
                  var $_galleries,
                      _cellSelector = '.carousel-cell',
                      self = this;

                  if ( ! _.isUndefined( $_gallery_container ) && 0 < $_gallery_container.length ) {
                        $_galleries = $_gallery_container.find( self.slidersSelectorMap.galleries );
                  } else {
                        $_galleries = $(self.slidersSelectorMap.galleries);
                  }
                  $_galleries.each( function() {
                        var $_gal = $(this),
                            $_firstcell = $_gal.find( '.carousel-cell' ).first(),
                            $_parentGridItem = $_gal.closest('.grid-item');

                        if ( 1 > $_firstcell.length )
                          return;
                        var _isSmartLoadCandidateImg = 0 < $_firstcell.find('img').length && 0 === $_firstcell.find('img').attr('src').indexOf('data');

                        $_firstcell.centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : ['smartload']
                        } );
                        if ( czrapp.localized.imgSmartLoadsForSliders ) {
                              if ( ! $_firstcell.data('czr_smartLoaded') ) {
                                    $_firstcell.find('img').removeClass('tc-smart-load-skip');
                                    $_firstcell.on( 'smartload', function() {
                                          self._maybeRemoveLoader.call( $_firstcell );
                                    });
                                    self._smartLoadCellImg( { el : $_firstcell, ev : 'czr-smartloaded-on-init', delay : 800 } );
                              }
                        }
                        $_parentGridItem.one( 'click', function() {
                              self._fireGalleryCarousel( $_gal );
                        });
                        $_parentGridItem.one( 'smartload czr-is-in-window', function(e, o) {
                              if ( czrapp.base.matchMedia( 1024 ) )//<= tablets in landscape mode
                                return;

                              if ( czrapp.userXP.isScrolling() ) {
                                    czrapp.$_body.one( 'scrolling-finished', function() {
                                          self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                                    });
                              } else {
                                    self.fireMeWhenStoppedScrolling( { delay : 4000, func : self._fireGalleryCarousel, instance : self, args : [ $_gal ] } );
                              }
                        });
                  });
            },
            _fireGalleryCarousel : function( $_gallery ) {
                  var _cellSelector = '.carousel-cell',
                      self = this;
                  if ( ! ( $_gallery instanceof $ ) || 1 > $_gallery.length ) {
                        czrapp.errorLog( '_fireGalleryCarousel : the passed element is not printed in the DOM');
                        return;
                  }
                  if ( $_gallery.data( 'czr-gallery-setup' ) )
                    return;

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        self._smartLoadFlickityImg({
                              sliderEl : $_gallery,
                              cellSelector : _cellSelector,
                              scheduleLoading : false
                        });
                  }
                  if ( _.isUndefined( $_gallery.data('flickity') ) ) {
                        var _is_single_slide = 1 == $_gallery.find( _cellSelector ).length,
                            _hasPageDots    = ! _is_single_slide && $_gallery.data( 'has-dots' );

                        self.czrFlickity( $_gallery, {
                              prevNextButtons: false,
                              wrapAround: true,
                              imagesLoaded: true,
                              setGallerySize: false,
                              cellSelector: _cellSelector,
                              accessibility: false,
                              dragThreshold: 10,
                              lazyLoad: false,
                              freeScroll: false,
                              pageDots: _hasPageDots,
                              draggable: ! _is_single_slide,
                        });
                        $_gallery.find( _cellSelector ).each( function() {
                              $(this).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerSliderImg,
                                    onInit : ! czrapp.localized.imgSmartLoadsForSliders,
                                    oncustom : ['smartload']
                              } );
                        });
                  }
                  $_gallery.data( 'czr-gallery-setup', true );
            },


            fireRelatedPostsCarousel : function() {
                  var self = this;
                  self.czrFlickity( $( self.slidersSelectorMap.relatedPosts ), {
                        prevNextButtons: false,
                        pageDots: false,
                        imagesLoaded: true,
                        cellSelector: 'article',
                        groupCells: true,
                        cellAlign: 'left',
                        dragThreshold: 10,
                        accessibility: false,
                        contain: true /* allows to not show a blank "cell" when the number of cells is odd but we display an even number of cells per viewport */
                  });
            },


            fireMainSlider : function() {
                  var self = this,
                      $_main_slider = $(self.slidersSelectorMap.mainSlider),
                      _cellSelector = '.carousel-cell',
                      $_firstcell = $_main_slider.find( _cellSelector ).first();

                  if ( 1 > $_firstcell.length )
                    return;
                  $_main_slider.find( _cellSelector ).each( function() {
                        var _isSmartLoadCandidateImg = 0 < $(this).find('img').length && 0 === $(this).find('img').attr('src').indexOf('data');
                        $(this).centerImages( {
                              enableCentering : 1 == czrapp.localized.centerSliderImg,
                              onInit : ! czrapp.localized.imgSmartLoadsForSliders || ( czrapp.localized.imgSmartLoadsForSliders && ! _isSmartLoadCandidateImg ),
                              oncustom : [ 'simple_load', 'smartload', 'refresh-centering-on-select' ],
                              defaultCSSVal : { width : '100%' , height : 'auto' },
                              useImgAttr : true,
                              zeroTopAdjust: 0
                        } );
                  });
                  $_main_slider.on( 'czr-flickity-ready.flickity', function() {
                        _.delay( function() {
                              $(this).on( 'select.flickity', function() {
                                    if ( $_main_slider.data('flickity').selectedElement && 1 == $( $_main_slider.data('flickity').selectedElement ).length ) {
                                          $( $_main_slider.data('flickity').selectedElement ).trigger( 'refresh-centering-on-select');
                                    }
                              });
                        }, 500 );
                  });

                  if ( czrapp.localized.imgSmartLoadsForSliders ) {
                        this._smartLoadFlickityImg( { sliderEl : $_main_slider, cellSelector : _cellSelector });
                  }
                  setTimeout( function() {
                        $_main_slider.prevAll('.czr-slider-loader-wrapper').fadeOut();
                  }, 300 );
                  if ( $_main_slider.length > 0 ) {
                        var _is_single_slide = 1 == $_main_slider.find( _cellSelector ).length,
                            _autoPlay        = $_main_slider.data('slider-delay'),
                            _hasPageDots    = !_is_single_slide && $_main_slider.data( 'has-dots' );

                        _autoPlay           =  ( _.isNumber( _autoPlay ) && _autoPlay > 0 ) ? _autoPlay : false;

                        self.czrFlickity( $_main_slider, {
                            prevNextButtons: false,
                            pageDots: _hasPageDots,
                            draggable: !_is_single_slide,

                            wrapAround: true,

                            imagesLoaded: true,

                            setGallerySize: false,
                            cellSelector: _cellSelector,

                            dragThreshold: 10,

                            autoPlay: _autoPlay, // {Number in milliseconds }

                            accessibility: false,
                        });
                  }
                  return this;
            },
            _smartLoadFlickityImg : function( params ) {
                  var self = this;
                  if ( ! _.isObject( params )  ) {
                        czrapp.errorLog( '_smartLoadFlickityImg params should be an object' );
                        return;
                  }
                  params = _.extend( {
                      sliderEl : {},
                      cellSelector : '.carousel-cell',
                      scheduleLoading : true
                  }, params );

                  if ( ! ( params.sliderEl instanceof $ ) || 1 > params.sliderEl.length )
                    return;

                  params.sliderEl.on( 'czr-flickity-ready.flickity', function() {
                        var _getSelectedCell = function() {
                              return $( params.sliderEl.data('flickity').selectedCell.element );
                            };
                        params.sliderEl.find( params.cellSelector ).each( function() {
                              if ( ! $(this).data('czr_smartLoaded') ) {
                                    $(this).find('img').removeClass('tc-smart-load-skip');
                              }
                              if ( $(this).hasClass( 'is-selected') && ! $(this).data('czr_smartLoaded') ) {
                                    $(this).imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( 'czr-smartloaded-on-init');
                              }
                        });

                        if ( ! params.scheduleLoading ) {
                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-init' } );
                              });
                        } else {
                              self._scheduleLoadingScenarios( params );
                        }
                  });//on flickity ready
                  params.sliderEl.on( 'smartload', params.cellSelector , function() {
                        self._maybeRemoveLoader.call( $(this) );
                  });
            },//_smartLoadFlickityImg
            _scheduleLoadingScenarios : function( params ) {
                  var self = this;
                  params.sliderEl.data( 'czr_smartload_scheduled', $.Deferred().done( function() {
                        params.sliderEl.addClass('czr-smartload-scheduled');
                  }) );
                  var _isSliderDataSetup = function() {
                        return 1 <= params.sliderEl.length && ! _.isUndefined( params.sliderEl.data( 'czr_smartload_scheduled' ) );
                  };
                  var _resolveHandler = function( dfd ) {
                        dfd.resolve();
                  };
                  params.sliderEl.data( 'czr_schedule_select',
                        $.Deferred( function() {
                              var dfd = this;
                              params.sliderEl.parent().one( 'click staticClick.flickity pointerDown.flickity dragMove.flickity', function() {
                                    dfd.resolve();
                              } );
                              _.delay( function() {
                                    if ( 'pending' == dfd.state() ) {
                                          params.sliderEl.one( 'select.flickity' , function() {
                                                dfd.resolve();
                                          } );
                                    }
                              }, 2000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-select' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_select' )
                  params.sliderEl.data( 'czr_schedule_scroll_resize',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    czrapp.$_window.one( 'scroll resize', function() {
                                          dfd.resolve();
                                    });
                              }, 5000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-smartloaded-on-scroll' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );//data( 'czr_schedule_scroll_resize' )
                  params.sliderEl.data( 'czr_schedule_autoload',
                        $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() { dfd.resolve(); }, 10000 );
                        }).done( function() {
                              if ( ! _isSliderDataSetup() || 'resolved' == params.sliderEl.data( 'czr_smartload_scheduled' ).state() )
                                return;

                              params.sliderEl.find( params.cellSelector ).each( function() {
                                    self._smartLoadCellImg( { el : $(this), ev : 'czr-auto-smartloaded' } );
                              });
                              params.sliderEl.data( 'czr_smartload_scheduled').resolve();
                        })
                  );
            },
            _smartLoadCellImg : function( params ) {
                  params = _.extend( {
                     el : {},
                     ev : 'czr-smartloaded',
                     delay : 0
                  }, params || {} );

                  var _event_ = params.ev,
                      self = this,
                      $_cell = params.el;
                  if ( ! ( $_cell instanceof $ ) || 1 > $_cell.find('img[data-src], img[data-smartload]').length )
                    return;
                  if ( ! $_cell.data( 'czr_smartLoaded' ) ) {
                        if ( 1 > $_cell.find('.czr-css-loader').length ) {
                              $_cell.append( self._css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                        }
                        _.delay( function() {
                              $_cell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( _event_ );
                        }, params.delay );

                        $_cell.data( 'czr_loader_timer' , $.Deferred( function() {
                              var self = this;
                              _.delay( function() {
                                    self.resolve();
                              }, 2000 );
                              return this.promise();
                        }) );
                        $_cell.data( 'czr_loader_timer' ).done( function() {
                              self._maybeRemoveLoader.call( $_cell );
                        });
                  }
            },
            _maybeRemoveLoader : function() {
                  if ( ! ( $(this) instanceof $ ) )
                    return;

                  $(this).find('.czr-css-loader').fadeOut( {
                        duration: 'fast',
                        done : function() { $(this).remove();}
                  } );
            },
            _parallax : function( evt ) {
                var $_parallax_carousel  = $(this),
                      _parallax_data_map = ['parallaxRatio', 'parallaxDirection', 'parallaxOverflowHidden', 'backgroundClass', 'matchMedia'];
                      _parallax_data     = _.object( _.chain(_parallax_data_map).map( function( key ) {
                                                var _data = $_parallax_carousel.data( key );
                                                return _data ? [ key, _data ] : '';
                                          })
                                          .compact()
                                          .value()
                        );

                  $_parallax_carousel.children('.flickity-viewport').czrParallax(_parallax_data);

            },
            _slider_arrows : function ( evt, side ) {

                  evt.preventDefault();
                  var $_this    = $(this),
                      _flickity = $_this.data( 'controls' );

                  if ( ! $_this.length )
                    return;
                  if ( ! _flickity ) {
                        _flickity   = $_this.closest('.czr-carousel').find('.flickity-enabled').data('flickity');
                        $_this.data( 'controls', _flickity );
                  }
                  if ( ! _flickity )
                    return;

                  if ( 'previous' == side ) {
                        _flickity.previous();
                  }
                  else if ( 'next' == side ) {
                        _flickity.next();
                  }

            },
            _slider_arrows_enable_toggler: function( evt ) {

                  var $_this             = $(this),
                      flkty              = $_this.data('flickity');

                  if ( ! flkty )//maybe not ready
                        return;

                  if ( flkty.options.wrapAround ) {
                        return;
                  }


                  var $_carousel_wrapper = $_this.closest('.czr-carousel'),
                      $_prev             = $_carousel_wrapper.find('.czr-carousel-prev'),
                      $_next             = $_carousel_wrapper.find('.czr-carousel-next');
                  $_prev.removeClass('disabled');
                  $_next.removeClass('disabled');
                  if ( ( 0 === flkty.selectedIndex ) )
                        $_prev.addClass('disabled');
                  if ( ( flkty.slides.length - 1 == flkty.selectedIndex ) )
                        $_next.addClass('disabled');

            },
            _move_background_link_inside : function( evt ) {
                  var $_flickity_slider = $(this),
                      $_bg_link = $_flickity_slider.closest('.entry-media__wrapper').children('.bg-link');

                  if ( $_bg_link.length > 0 ) {
                        $(this).find( '.flickity-viewport' ).prepend( $_bg_link );
                  }
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {
        setupUIListeners : function() {
              var self = this;
              this.windowWidth            = new czrapp.Value( czrapp.$_window.width() );
              this.isScrolling            = new czrapp.Value( false );
              this.isResizing             = new czrapp.Value( false );
              this.scrollPosition         = new czrapp.Value( czrapp.$_window.scrollTop() );
              this.scrollDirection        = new czrapp.Value('down');
              self.previewDevice          = new czrapp.Value( 'desktop' );
              if ( self._isCustomizing() ) {
                    var _setPreviewedDevice = function() {
                          wp.customize.preview.bind( 'previewed-device', function( device ) {
                                self.previewDevice( device );
                          });
                    };
                    if ( wp.customize.preview ) {
                        _setPreviewedDevice();
                    } else {
                          wp.customize.bind( 'preview-ready', function() {
                                _setPreviewedDevice();
                          });
                    }
              }
              var _resizeReact = function( to, from, params ) {
                    params = params || {};
                    if ( params.emulate ) {
                          self.isResizing( true );
                    } else {
                          self.isResizing( self._isMobile ? Math.abs( from - to ) > 2 : Math.abs( from - to ) > 0 );
                    }
                    clearTimeout( $.data( this, 'resizeTimer') );
                    $.data( this, 'resizeTimer', setTimeout(function() {
                          self.isResizing( false );
                    }, 50 ) );
              };
              self.windowWidth.bind( _resizeReact );
              czrapp.$_window.on( 'czr-resize', function() { _resizeReact( null, null, { emulate : true } ); } );
              self.isResizing.bind( function( is_resizing ) {
                    czrapp.$_body.toggleClass( 'is-resizing', is_resizing );
              });
              this.isScrolling.bind( function( to, from ) {
                    czrapp.$_body.toggleClass( 'is-scrolling', to );
                    if ( ! to ) {
                          czrapp.trigger( 'scrolling-finished' );
                          czrapp.$_body.trigger( 'scrolling-finished' );
                    }
              });
              this.scrollPosition.bind( function( to, from ) {
                    czrapp.$_body.toggleClass( 'is-scrolled', to > 100 );
                    if ( to <= 50 ) {
                          czrapp.trigger( 'page-scrolled-top', {} );
                    }
                    self.scrollDirection( to >= from ? 'down' : 'up' );
              });
              czrapp.$_window.resize( _.throttle( function( ev ) { self.windowWidth( czrapp.$_window.width() ); }, 10 ) );
              czrapp.$_window.scroll( _.throttle( function() {
                    self.isScrolling( true );
                    self.scrollPosition( czrapp.$_window.scrollTop() );
                    clearTimeout( $.data( this, 'scrollTimer') );
                    $.data( this, 'scrollTimer', setTimeout(function() {
                          self.isScrolling( false );
                    }, 100 ) );
              }, 10 ) );
        }
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
  var _methods =  {
        stickifyHeader : function() {
              if ( czrapp.$_header.length < 1 )
                return;

              var self = this;
              this.stickyCandidatesMap = {
                    mobile : {
                          mediaRule : 'only screen and (max-width: 991px)',
                          selector : 'mobile-sticky'
                    },
                    desktop : {
                          mediaRule : 'only screen and (min-width: 992px)',
                          selector : 'desktop-sticky'
                    }
              };
              this.navbarsWrapperSelector = '.header-navbars__wrapper';
              this.$_navbars_wrapper      = $( this.navbarsWrapperSelector );
              this.$_topbar               = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.topbar-navbar__wrapper') : false;
              this.$_primary_navbar       = 1 == this.$_navbars_wrapper.length ? this.$_navbars_wrapper.find( '.primary-navbar__wrapper') : false;

              this.stickyMenuWrapper      = false;
              this.stickyMenuDown         = new czrapp.Value( '_not_set_' );
              this.stickyHeaderThreshold  = 50;
              this.currentStickySelector  = new czrapp.Value( '' );//<= will be set on init and on resize
              this.hasStickyCandidate     = new czrapp.Value( false );
              this.stickyHeaderAnimating  = new czrapp.Value( false );
              this.animationPromise       = $.Deferred( function() { return this.resolve(); });
              this.userStickyOpt          = new czrapp.Value( self._setUserStickyOpt() );//set on init and on resize : stick_always, no_stick, stick_up
              this.isFixedPositionned     = new czrapp.Value( false );//is the candidate fixed ? => toggle the 'fixed-header-on' css class to the header
              this.stickyStage            = new czrapp.Value( '_not_set_' );
              this.shrinkBrand            = new czrapp.Value( false );//Toggle a class to maybe shrink the title or logo if the option is on
              this.currentStickySelector.bind( function( to, from ) {
                    var _reset = function() {
                          czrapp.$_header.css( { 'height' : '' });
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          self.stickyMenuDown( false );
                          self.stickyMenuWrapper = false;
                          self.hasStickyCandidate( false );
                    };
                    if ( ! _.isEmpty( to ) ) {
                          self.hasStickyCandidate( 1 == czrapp.$_header.find( to ).length );
                          if ( ! self.hasStickyCandidate() ) {
                                _reset();
                          }
                          else {
                                self.stickyMenuWrapper = czrapp.$_header.find( to );
                                var $_header_logo = self.stickyMenuWrapper.find('.navbar-brand-sitelogo img');
                                if ( 1 == $_header_logo.length ) {
                                      $_header_logo.bind( 'header-logo-loaded', function() {
                                            czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                      });
                                      if ( $_header_logo[0].complete ) {
                                            $_header_logo.trigger('header-logo-loaded');
                                      } else {
                                        $_header_logo.load( function( img ) {
                                              $_header_logo.trigger('header-logo-loaded');
                                        } );
                                      }
                                } else {
                                    czrapp.$_header.css( { 'height' : czrapp.$_header[0].getBoundingClientRect().height });
                                }
                          }
                    } else {//we don't have a candidate
                          _reset();
                    }
              });
              this.isFixedPositionned.bind( function( isFixed ) {
                    czrapp.$_header.toggleClass( 'fixed-header-on', isFixed ).toggleClass( 'is-sticky', isFixed );
                    self._pushPrimaryNavBarDown( isFixed );
                    self.shrinkBrand( isFixed );
              });
              this.shrinkBrand.bind( function( isShrinked ) {
                    czrapp.$_header.toggleClass( 'can-shrink-brand', isShrinked );
                    if ( ! isShrinked ) {
                          _.delay( function() {
                                if ( self.scrollPosition() < self.stickyHeaderThreshold ) {
                                      czrapp.$_header.trigger( 'czr-resize');
                                }
                          }, 400 );//<=400ms gives us enough room to finish the title or logo unshrinking animation
                    }
              });
              var _setStickynessStatesOnScroll = function( to, from ) {
                    if ( ! self.hasStickyCandidate() )
                      return;

                    to = to || self.scrollPosition();
                    from = from || self.scrollPosition();

                    var reachedTheTop = ( to == from ) && 0 === to;
                    if ( ! reachedTheTop ) {
                          if ( Math.abs( to - from ) <= 5 ) {
                            return;
                          }
                    }
                    var $menu_wrapper = czrapp.$_header.find( self.currentStickySelector() ),
                        _h = $menu_wrapper[0].getBoundingClientRect().height;

                    if ( 'down' == self.scrollDirection() && to <= ( self.topStickPoint() + _h ) ) {
                          self.stickyStage( 'down_top' );
                          self.isFixedPositionned( false );
                          self.stickyMenuDown( true );

                    } else if ( 'down' == self.scrollDirection() && to > ( self.topStickPoint() + _h ) && to < ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          self.stickyStage( 'down_middle' );
                          self.isFixedPositionned( false );
                          self.stickyMenuDown( false );

                    } else if ( 'down' == self.scrollDirection() && to >= ( self.topStickPoint() + ( _h * 2 ) ) ) {
                          if ( 'stick_always' == self.userStickyOpt()  ) {
                                var _dodo = function() {
                                      self.stickyMenuDown( false, { fast : true,  } ).done( function() {
                                            self.stickyMenuDown( true, { forceFixed : true } ).done( function() {});
                                            self.stickyStage( 'down_after' );
                                      });
                                };
                                if ( ! self.stickyHeaderAnimating() && ( ( 'down_after' != self.stickyStage() && 'up' != self.stickyStage() ) || true !== self.stickyMenuDown() ) ) {
                                     _dodo();
                                }
                          } else {
                                self.stickyMenuDown( false );
                                self.stickyStage( 'down_after' );
                          }

                    } else if ( 'up' == self.scrollDirection() ) {
                          self.stickyStage( 'up' );
                          self.stickyMenuDown( true ).done( function() {});
                          if ( self.isFixedPositionned() ) {
                                self.isFixedPositionned( to > self.topStickPoint() );
                          }
                    }
              };
              this.scrollPosition.bind( function( to, from ) {
                    _setStickynessStatesOnScroll( to, from );
                    self.shrinkBrand( self.isFixedPositionned() );
              } );
              var _maybeResetTop = function() {
                    if ( 'up' == self.scrollDirection() ) {
                          self._mayBeresetTopPosition();
                    }
              };
              czrapp.bind( 'scrolling-finished', _maybeResetTop );
              czrapp.bind( 'scrolling-finished', function() {
                    _.delay( function() {
                          _setStickynessStatesOnScroll();
                    }, 400);
              });
              czrapp.bind( 'topbar-collapsed', _maybeResetTop );
              self.stickyMenuDown.validate = function( newValue ) {
                    if ( ! self.hasStickyCandidate() )
                      return false;
                    if ( self._isMobileMenuExpanded() )
                      return this._value;

                    if ( self.scrollPosition() < self.stickyHeaderThreshold && ! newValue ) {
                          if ( ! self.isScrolling() ) {
                                czrapp.errorLog('Menu too close from top to be moved up');
                          }
                          return self.stickyMenuDown();
                    } else {
                          return newValue;
                    }
              };
              self.stickyMenuDown.bind( function( to, from, args ){
                    if ( ! _.isBoolean( to ) || ! self.hasStickyCandidate() ) {
                          return $.Deferred( function() { return this.resolve().promise(); } );
                    }
                    args = _.extend(
                          {
                                direction : to ? 'down' : 'up',
                                forceFixed : false,
                                menu_wrapper : self.stickyMenuWrapper,
                                fast : false
                          },
                          args || {}
                    );

                    return self._animate(
                          {
                                direction : args.direction,
                                forceFixed : args.forceFixed,
                                menu_wrapper : args.menu_wrapper,
                                fast : args.fast
                          }
                    );
              }, { deferred : true } );
              self.isResizing.bind( function() {self._refreshOrResizeReact(); } );//resize();
              czrapp.$_header.on( 'refresh-sticky-header', function() { self._refreshOrResizeReact(); } );
              self._setStickySelector();
              this.topStickPoint          = new czrapp.Value( self._getTopStickPoint() );
              if ( ! self._isMobile() && self.hasStickyCandidate() ) {
                    self._adjustDesktopTopNavPaddingTop();
              }

        },//stickify
        _animate : function( args ) {
              var dfd = $.Deferred(),
                  self = this,
                  $menu_wrapper = ! args.menu_wrapper.length ? czrapp.$_header.find( self.currentStickySelector() ) : args.menu_wrapper,
                  _startPosition = self.scrollPosition(),
                  _endPosition = _startPosition;


              this.animationPromise = dfd;
              if ( ! $menu_wrapper.length )
                return dfd.resolve().promise();
              self.isFixedPositionned( self.isFixedPositionned() ? true : ( 'up' == self.scrollDirection() || args.forceFixed ) );//toggles the css class 'fixed-header-on' from the czrapp.$_header element

              var _do = function() {
                    var translateYUp = $menu_wrapper[0].getBoundingClientRect().height,
                        translateYDown = 0,
                        _translate;

                    if ( args.fast ) {
                          $menu_wrapper.addClass( 'fast' );
                    }

                    _translate = 'up' == args.direction ? 'translate(0px, -' + translateYUp + 'px)' : 'translate(0px, -' + translateYDown + 'px)';
                    self.stickyHeaderAnimating( true );
                    self.stickyHeaderAnimationDirection = args.direction;
                    $menu_wrapper.toggleClass( 'sticky-visible', 'down' == args.direction );

                    $menu_wrapper.css({
                          '-webkit-transform': _translate,   /* Safari and Chrome */
                          '-moz-transform': _translate,       /* Firefox */
                          '-ms-transform': _translate,        /* IE 9 */
                          '-o-transform': _translate,         /* Opera */
                          transform: _translate
                    });
                    _.delay( function() {
                          self.stickyHeaderAnimating( false );
                          if ( args.fast ) {
                                $menu_wrapper.removeClass('fast');
                          }
                          dfd.resolve();
                    }, args.fast ? 100 : 350 );
                    return dfd;
              };//_do

              _.delay( function() {
                    if ( czrapp.userXP.isResponsive() && 1 == $('.mobile-navbar__wrapper').length ) {
                          if ( self._isMobileMenuExpanded() ) {
                                self._toggleMobileMenu().done( function() {
                                      _do().done( function() { self._mayBeresetTopPosition(); } );
                                });
                          } else {
                                _do();
                          }
                    } else {
                          _do();
                    }

                    if ( czrapp.userXP.mobileMenu && czrapp.userXP.mobileMenu.has( sticky_menu_id ) ) {
                          czrapp.userXP.mobileMenu( sticky_menu_id )( 'collapsed' ).done( function() {
                                _do();
                          });
                    }
              }, 10 );
              return dfd.promise();
        },
        _isMobileMenuExpanded : function() {
              var $mobile_menu = $('.mobile-navbar__wrapper');
              if ( 1 != $('.mobile-navbar__wrapper').length )
                return false;

              return 1 == $mobile_menu.find('.ham-toggler-menu').length && "true" == $mobile_menu.find('.ham-toggler-menu').attr('aria-expanded');
        },
        _toggleMobileMenu : function() {
            return $.Deferred( function() {
                  var $mobile_menu = $('.mobile-navbar__wrapper'),
                      _dfd_ = this;
                  if ( 1 == $mobile_menu.length ) {
                        $mobile_menu.find('.ham-toggler-menu').trigger('click');
                        _.delay( function() {
                              _dfd_.resolve();
                        }, 350 );
                  } else {
                        _dfd_.resolve();
                  }
            }).promise();
        },
        _setStickySelector : function() {
              var self = this,
                  _selector = false;
              _.each( self.stickyCandidatesMap, function( _params, _device ) {
                    if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches && 'no_stick' != self.userStickyOpt() ) {
                          _selector = '.' + _params.selector;
                    }
              });
              self.currentStickySelector( _selector );
        },
        _setUserStickyOpt : function( device ) {
              var self = this;
              if ( _.isUndefined( device ) ) {
                    _.each( self.stickyCandidatesMap, function( _params, _device ) {
                          if ( _.isFunction( window.matchMedia ) && matchMedia( _params.mediaRule ).matches ) {
                                device = _device;
                          }
                    });
              }
              device = device || 'desktop';

              return ( czrapp.localized.menuStickyUserSettings && czrapp.localized.menuStickyUserSettings[ device ] ) ? czrapp.localized.menuStickyUserSettings[ device ] : 'no_stick';
        },
        _getTopStickPoint : function() {

              if ( this.$_navbars_wrapper.length < 1 )
                return 0;
              var self = this;
              if ( 1 == self.$_topbar.length && ! self.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    return self.$_navbars_wrapper.offset().top + self.$_topbar[0].getBoundingClientRect().height;
              }

              return self.$_navbars_wrapper.offset().top;

        },
        _adjustDesktopTopNavPaddingTop : function() {
        },
        _mayBeresetTopPosition : function() {

              var  self = this, $menu_wrapper = self.stickyMenuWrapper;
              if ( 'up' != self.scrollDirection() )
                return;
              if ( ! $menu_wrapper.length )
                return;

              if ( self.scrollPosition() >= self.stickyHeaderThreshold )
                return;


              if ( ! self._isMobile() ) {
                  self._adjustDesktopTopNavPaddingTop();
              }
              self.stickyMenuDown( true, { force : true, fast : true } ).done( function() {
                    self.stickyHeaderAnimating( true );
                    ( function() {
                          return $.Deferred( function() {
                              var dfd = this;
                              _.delay( function() {
                                    if ( 'up' == self.scrollDirection() && self.scrollPosition() < 10) {
                                          $menu_wrapper.css({
                                                '-webkit-transform': '',   /* Safari and Chrome */
                                                '-moz-transform': '',       /* Firefox */
                                                '-ms-transform': '',        /* IE 9 */
                                                '-o-transform': '',         /* Opera */
                                                transform: ''
                                          });
                                    }
                                    self.stickyHeaderAnimating( false );
                                    self.isFixedPositionned( false );
                                    dfd.resolve();
                              }, 10 );
                          }).promise();
                    } )().done( function() {});
              });
        },
        _pushPrimaryNavBarDown : function( push ) {
              push = push || this.isFixedPositionned();
              if ( 1 == this.$_primary_navbar.length && 1 == this.$_topbar.length && this.$_topbar.is( $( this.currentStickySelector() ) ) ) {
                    this.$_primary_navbar.css( { 'padding-top' : push ? this.$_topbar[0].getBoundingClientRect().height + 'px' : '' } );
              }
        },

        _refreshOrResizeReact : function() {
              var  self = this;
              self.userStickyOpt( self._setUserStickyOpt() );
              self._setStickySelector();
              self.topStickPoint( self._getTopStickPoint() );
              self._pushPrimaryNavBarDown();
              if ( self._isMobileMenuExpanded() ) {
                    self._toggleMobileMenu();
              }

              if ( self.hasStickyCandidate() ) {
                    self.stickyMenuDown( self.scrollPosition() < self.stickyHeaderThreshold ,  { fast : true } ).done( function() {
                          czrapp.$_header.css( 'height' , '' );
                          self.isFixedPositionned( false );//removes css class 'fixed-header-on' from the czrapp.$_header element
                          if ( self.hasStickyCandidate() ) {
                                czrapp.$_header.css( 'height' , czrapp.$_header[0].getBoundingClientRect().height );
                                self.isFixedPositionned( self.scrollPosition() > self.topStickPoint() );//toggles the css class 'fixed-header-on' from the czrapp.$_header element
                          }
                    });
              } else {
                    self.stickyMenuDown( false ).done( function() {
                          $('#header').css( 'padding-top', '' );
                    });
              }
              if ( ! self._isMobile() ) {
                    self._adjustDesktopTopNavPaddingTop();
              } else {
                    $('.full-width.topbar-enabled #header').css( 'padding-top', '' );
                    self._mayBeresetTopPosition();
              }
        }

  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
        mayBePrintFrontNote : function() {
              if ( czrapp.localized && _.isUndefined( czrapp.localized.frontNotifications ) )
                return;
              if ( _.isEmpty( czrapp.localized.frontNotifications ) || ! _.isObject( czrapp.localized.frontNotifications ) )
                return;

              var self = this,
                  _hasCandidate = false;
              czrapp.frontNotificationVisible = new czrapp.Value( false );
              czrapp.frontNotificationRendered = false;
              _.each( czrapp.localized.frontNotifications, function( _notification, _id ) {
                    if ( ! _.isUndefined( czrapp.frontNotification ) )
                      return;

                    if ( ! _.isObject( _notification ) )
                      return;
                    _notification = _.extend( {
                          enabled : false,
                          content : '',
                          dismissAction : '',
                          ajaxUrl : czrapp.localized.ajaxUrl
                    }, _notification );
                    if ( _notification.enabled ) {
                          czrapp.frontNotification = new czrapp.Value( _notification );
                    }

              });
              czrapp.frontNotificationVisible.bind( function( visible ) {
                      return self._toggleNotification( visible );//returns a promise()
              }, { deferred : true } );

              czrapp.frontNotificationVisible( true );
        },//mayBePrintFrontNote()


        _toggleNotification : function( visible ) {
              var self = this,
                  dfd = $.Deferred();

              if ( czrapp.frontNotificationRendered && czrapp.frontNotificationVisible() )
                return dfd.resolve().promise();

              var _hideAndDestroy = function() {
                    return $.Deferred( function() {
                          var _dfd_ = this,
                              $notifWrap = $('#bottom-front-notification', '#footer');
                          if ( 1 == $notifWrap.length ) {
                                $notifWrap.css( { bottom : '-100%' } );
                                _.delay( function() {
                                      $notifWrap.remove();
                                      czrapp.$_body.find('#tc-footer-btt-wrapper').fadeIn('slow');
                                      czrapp.frontNotificationRendered = false;
                                      _dfd_.resolve();
                                }, 450 );// consistent with css transition: all 0.45s ease-in-out;
                          } else {
                              _dfd_.resolve();
                          }
                    });
              };

              var _renderAndSetup = function() {
                    var _dfd_ = $.Deferred(),
                        $footer = $('#footer', '#tc-page-wrap');
                    if ( _.isUndefined( czrapp.frontNotification ) || ! _.isFunction( czrapp.frontNotification ) || ! _.isObject( czrapp.frontNotification() ) )
                        return _dfd_.resolve().promise();
                    $.Deferred( function() {
                          var dfd = this,
                              _notifHtml = czrapp.frontNotification().content,
                              _wrapHtml = [
                                    '<div id="bottom-front-notification">',
                                      '<div class="note-content">',
                                        '<span class="fa fa-times close-note" title="' + czrapp.localized.i18n['Permanently dismiss'] + '"></span>',
                                      '</div>',
                                    '</div>'
                              ].join('');

                          if ( 1 == $footer.length && ! _.isEmpty( _notifHtml ) ) {
                                $.when( $footer.append( _wrapHtml ) ).done( function() {
                                    $(this).find( '.note-content').prepend( _notifHtml );
                                    czrapp.$_body.find('#tc-footer-btt-wrapper').fadeOut('slow');
                                    czrapp.frontNotificationRendered = true;
                                });

                                _.delay( function() {
                                      $('#bottom-front-notification', '#footer').css( { bottom : 0 } );
                                      dfd.resolve();
                                }, 500 );
                          } else {
                                dfd.resolve();
                          }
                    }).done( function() {
                          czrapp.setupDOMListeners(
                                [
                                      {
                                            trigger   : 'click keydown',
                                            selector  : '.close-note',
                                            actions   : function() {
                                                  czrapp.frontNotificationVisible( false ).done( function() {
                                                        czrapp.doAjax( {
                                                              action: czrapp.frontNotification().dismissAction,
                                                              withNonce : true,
                                                              ajaxUrl : czrapp.frontNotification().ajaxUrl
                                                        });
                                                  });
                                            }
                                      }
                                ],//actions to execute
                                { dom_el: $footer },//dom scope
                                self //instance where to look for the cb methods
                          );
                          _dfd_.resolve();
                    });
                    return _dfd_.promise();
              };//renderAndSetup

              if ( visible ) {
                    _.delay( function() {
                          _renderAndSetup().always( function() {
                                dfd.resolve();
                          });
                    }, 3000 );
              } else {
                    _hideAndDestroy().done( function() {
                          czrapp.frontNotificationVisible( false );//should be already false
                          dfd.resolve();
                    });
              }
              _.delay( function() {
                          czrapp.frontNotificationVisible( false );
                    },
                    45000
              );
              return dfd.promise();
        }//_toggleNotification
  };//_methods{}

  czrapp.methods.UserXP = czrapp.methods.UserXP || {};
  $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
   var _methods =   {
      outline: function() {
            if ( 'function' == typeof( tcOutline ) )
                tcOutline();
      },
      variousHoverActions : function() {
            if ( czrapp.$_body.hasClass( 'czr-is-mobile' ) )
                return;
            $( '.grid-container__alternate, .grid-container__square-mini, .grid-container__plain' ).on( 'mouseenter mouseleave', '.entry-media__holder, article.full-image .tc-content', _toggleArticleParentHover );
            $( '.grid-container__masonry, .grid-container__classic').on( 'mouseenter mouseleave', '.grid__item', _toggleArticleParentHover );
            czrapp.$_body.on( 'mouseenter mouseleave', '.gallery-item, .widget-front, .fpc-widget-front', _toggleThisHover );
            czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOn );

            function _toggleArticleParentHover( evt ) {
                  _toggleElementClassOnHover( $(this).closest('article'), 'hover', evt );
            }

            function _toggleThisHover( evt ) {
                  _toggleElementClassOnHover( $(this), 'hover', evt );
            }

            function _toggleThisOn( evt ) {
                  _toggleElementClassOnHover( $(this), 'on', evt );
            }

            function _toggleElementClassOnHover( $_el, _class, _evt ) {
                  if ( 'mouseenter' == _evt.type )
                     $_el.addClass( _class );
                  else if ( 'mouseleave' == _evt.type )
                     $_el.removeClass( _class );
            }
      },
      formFocusAction : function() {
            var _input_types       = [
                      'input[type="url"]',
                      'input[type="email"]',
                      'input[type="text"]',
                      'input[type="password"]',
                      'textarea'
                ],
                _focusable_class        = 'czr-focus',
                _focusable_field_class  = 'czr-focusable',
                _focus_class            = 'in-focus',
                _czr_form_class         = 'czr-form',
                _parent_selector        = '.'+ _czr_form_class + ' .'+_focusable_class,
                _inputs                 = _.map( _input_types, function( _input_type ){ return _parent_selector + ' ' + _input_type ; } ).join(),
                $_focusable_inputs      = $( _input_types.join() );

            if ( $_focusable_inputs.length <= 0 )
              return;
            $_focusable_inputs.each( function() {
               var $_this = $(this);
               if ( !$_this.attr('placeholder') && ( $_this.closest( '#buddypress' ).length < 1 ) ) {
                  $(this)
                        .addClass(_focusable_field_class)
                        .parent().addClass(_focusable_class);
               }
            });

            var _toggleThisFocusClass = function( evt ) {
                var $_el       = $(this),
                      $_parent = $_el.closest(_parent_selector);

                if ( $_el.val() || ( evt && 'focusin' == evt.type ) ) {
                   $_parent.addClass( _focus_class );
                } else {
                   $_parent.removeClass( _focus_class );
                }
            };

            czrapp.$_body.on( 'in-focus-load.czr-focus focusin focusout', _inputs, _toggleThisFocusClass );
            $(_inputs).trigger( 'in-focus-load.czr-focus' );
            czrapp.$_body.on( 'click', '.icn-close', function() {
                  var $_search_field = $(this).closest('form').find('.czr-search-field');

                  if ( $_search_field.length ) {
                        if ( $_search_field.val() ) {
                              $_search_field.val('').focus();
                        }
                        else {
                              $_search_field.blur();
                        }
                  }

            });
      },
      onEscapeKeyPressed : function() {
            var ESCAPE_KEYCODE                  = 27, // KeyboardEvent.which value for Escape (Esc) key

                Event = {
                      KEYEVENT          : 'keydown', //or keyup, if we want to react to the release event
                      SIDENAV_CLOSE     : 'sn-close',
                      OVERLAY_TOGGLER   : 'click',
                      SIDENAV_TOGGLER   : 'click'
                },

                ClassName = {
                      SEARCH_FIELD      : 'czr-search-field',
                      OLVERLAY_SHOWN    : 'czr-overlay-opened',
                      SIDENAV_SHOWN     : 'tc-sn-visible'
                },

                Selector = {
                      OVERLAY           : '.czr-overlay',
                      SIDENAV           : '#tc-sn',
                      OVERLAY_TOGGLER   : '.czr-overlay-toggle_btn',
                      SIDENAV_TOGGLER   : '[data-toggle="sidenav"]'
                };


            czrapp.$_body.on( Event.KEYEVENT, function(evt) {

                  if ( ESCAPE_KEYCODE == evt.which ) {
                        if ( $(evt.target).hasClass( ClassName.SEARCH_FIELD ) ) {
                              $( evt.target ).val('').blur();
                              return;
                        }
                        if ( $( Selector.OVERLAY ).length && czrapp.$_body.hasClass( ClassName.OLVERLAY_SHOWN ) ) {
                              $( Selector.OVERLAY ).find( Selector.OVERLAY_TOGGLER ).trigger( Event.OVERLAY_TOGGLER );
                              return;
                        }
                        if ( $( Selector.SIDENAV ).length  && czrapp.$_body.hasClass( ClassName.SIDENAV_SHOWN ) ) {
                              $( Selector.SIDENAV ).find( Selector.SIDENAV_TOGGLER ).trigger( Event.SIDENAV_TOGGLER );
                              return;
                        }
                  }

            });

      },

      variousHeaderActions : function() {
            var _mobile_viewport = 992,
                self = this;
            czrapp.$_body.on( 'shown.czr.czrDropdown', '.nav__woocart', function() {
                  var $_el = $(this);
                  var _do = function() {
                        var $_to_scroll = $_el.find('.product_list_widget');
                        if ( $_to_scroll.length && ! $_to_scroll.hasClass('mCustomScrollbar') ) {
                              $_to_scroll.mCustomScrollbar({
                                    theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                              });
                        }
                  };
                  if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                        _do();
                  } else {
                        self.maybeLoadCustomScrollAssets().done( function() {
                            _do();
                       });
                  }
            });
            if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                  czrapp.$_body.on( 'shown.czr.czrDropdown', '.czr-open-on-click.mCustomScrollbar, .czr-open-on-click .mCustomScrollbar, .mCustomScrollbar .czr-open-on-click', function( evt ) {
                        var $_this                  = $( this ),
                            $_customScrollbar = $_this.hasClass('mCustomScrollbar') ? $_this : $_this.closest('.mCustomScrollbar');
                        if ( $_customScrollbar.length ) {
                             $_customScrollbar.mCustomScrollbar( 'scrollTo', $(evt.target) );
                        }
                  });
            }
      },
      headerSearchToLife : function() {
            var self = this,

                _search_toggle_event            = 'click',

                _search_overlay_toggler_sel     = '.search-toggle_btn.czr-overlay-toggle_btn',
                _search_overlay_toggle_class    = 'full-search-opened czr-overlay-opened',

                _search_dropdown_wrapper_sel    = '.mobile-utils__wrapper',
                _search_dropdown_button_sel     = '.search-toggle_btn.czr-dropdown',
                _search_dropdown_menu_sel       = '.nav__search .czr-dropdown-menu',
                _search_dropdown_menu_input_sel = '.czr-search-field',
                _search_dropdown_expanded_class = 'show',

                _mobile_menu_to_close_sel       = '.ham-toggler-menu:not(.collapsed)',
                _mobile_menu_close_event        = 'click.bs.collapse',
                _mobile_menu_opened_event       = 'show.bs.collapse', //('show' : start of the uncollapsing animation; 'shown' : end of the uncollapsing animation)
                _mobile_menu_sel                = '.mobile-nav__nav';
            czrapp.$_body.on( _search_toggle_event, _search_overlay_toggler_sel, function(evt) {
                  evt.preventDefault();
                  czrapp.$_body.toggleClass( _search_overlay_toggle_class );
            });
            self.headerSearchExpanded = new czrapp.Value( false );
            self.headerSearchExpanded.bind( function( exp ) {
                  return $.Deferred( function() {
                        var _dfd = this;
                        $(  _search_dropdown_button_sel, _search_dropdown_wrapper_sel )
                                  .toggleClass( _search_dropdown_expanded_class, exp )
                                  .attr('aria-expanded', exp );
                        if ( exp ) {
                              $( _mobile_menu_to_close_sel ).trigger( _mobile_menu_close_event );
                        }

                        $(  _search_dropdown_menu_sel, _search_dropdown_wrapper_sel )
                            .attr('aria-expanded', exp )
                            .stop()[ ! exp ? 'slideUp' : 'slideDown' ]( {
                                  duration : 250,
                                  complete : function() {
                                    if ( exp ) {
                                          $( _search_dropdown_menu_input_sel, $(this) ).focus();
                                    }
                                    _dfd.resolve();
                                  }
                            } );
                  }).promise();
            }, { deferred : true } );
            czrapp.setupDOMListeners(
                  [
                        {
                              trigger   : _search_toggle_event,
                              selector  : _search_dropdown_button_sel,
                              actions   : function(evt) {
                                    czrapp.userXP.headerSearchExpanded( ! czrapp.userXP.headerSearchExpanded() );
                              }
                        },
                  ],//actions to execute
                  { dom_el: $( _search_dropdown_wrapper_sel ) },//dom scope
                  czrapp.userXP //instance where to look for the cb methods
            );
            czrapp.userXP.windowWidth.bind( function() {
                  self.headerSearchExpanded( false );
            });
            czrapp.$_body.on( _mobile_menu_opened_event, _mobile_menu_sel, function( evt ) {
                  self.headerSearchExpanded( false );
            });
            if ( czrapp.userXP.stickyHeaderAnimating ) {
                  czrapp.userXP.stickyHeaderAnimating.bind( function( animating ) {
                        self.headerSearchExpanded( false );
                  });
            }
      },//toggleHeaderSearch
      maybeLoadCustomScrollAssets : function() {
            var dfd = $.Deferred();
            if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                  return dfd.resolve().promise();
            } else {
                  $('head').append( $('<link/>' , {
                              rel : 'stylesheet',
                              id : 'czr-custom-scroll-bar',
                              type : 'text/css',
                              href : czrapp.localized.assetsPath + 'css/jquery.mCustomScrollbar.min.css'
                        }) );
                  $.ajax( {
                        url : ( czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.min.js'),
                        cache : true,
                        dataType: "script"
                  }).done(function() {
                        if ( 'function' != typeof $.fn.mCustomScrollbar )
                          return dfd.rejected();
                        dfd.resolve();
                  }).fail( function() {
                        czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                  });
            }
            return dfd.promise();
      },
      smoothScroll: function() {
            if ( $('body').hasClass( 'czr-infinite-scroll-on' ) || ( czrapp.localized.SmoothScroll && czrapp.localized.SmoothScroll.Enabled && ! czrapp.base.matchMedia( 1024 ) ) ) {
                  smoothScroll( czrapp.localized.SmoothScroll.Options );
            }
      },

      magnificPopup : function() {},
      attachmentsFadeEffect : function() {
            $( '.attachment-image-figure img' ).delay(500).addClass( 'opacity-forced' );
      },

      pluginsCompatibility: function() {
            var $_ssbar = $( '.the_champ_vertical_sharing, .the_champ_vertical_counter', '.article-container' );
            if ( $_ssbar.length )
              $_ssbar.detach().prependTo('.article-container');
      },
      featuredPagesAlignment : function() {
          var $_featured_pages   = $('.featured-page .widget-front'),
               _n_featured_pages = $_featured_pages.length,
               doingAnimation      = false,
               _lastWinWidth       = '';


          if ( _n_featured_pages < 2 )
            return;

          var $_fp_elements       = new Array( _n_featured_pages ),
               _n_elements          = new Array( _n_featured_pages );
          $.each( $_featured_pages, function( _fp_index, _fp ) {
                $_fp_elements[_fp_index]   = $(_fp).find( '[class^=fp-]' );
                _n_elements[_fp_index]      = $_fp_elements[_fp_index].length;
          });

          _n_elements = Math.max.apply(Math, _n_elements );

          if ( ! _n_elements )
            return;

          var _offsets      = new Array( _n_elements ),
               _maxs          = new Array( _n_elements );
         for (var i = 0; i < _n_elements; i++)
            _offsets[i] = new Array( _n_featured_pages );
          maybeSetElementsPosition();
          czrapp.$_window.on( 'resize', _.debounce( maybeSetElementsPosition, 20 ) );

         function maybeSetElementsPosition() {

            if ( ! doingAnimation ) {
               var _winWidth = czrapp.$_window.width();
               if ( _winWidth == _lastWinWidth )
                  return;

               _lastWinWidth = _winWidth;

               doingAnimation = true;

               window.requestAnimationFrame(function() {
                  setElementsPosition();
                  doingAnimation = false;
               });

            }
         }
        function setElementsPosition() {
              var _fp_offsets = [];
              for ( _element_index = 0; _element_index < _n_elements; _element_index++ ) {
                  for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    var $_el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          _offset = 0,
                          $_fp      = $($_featured_pages[_fp_index]);

                    if ( $_el.length > 0 ) {
                       $_el.css( 'paddingTop', '' );
                       _offset = $_el.offset().top;

                    }
                    _offsets[_element_index][_fp_index] = _offset;
                    if ( _fp_offsets.length < _n_featured_pages )
                       _fp_offsets[_fp_index] = parseFloat( $_fp.offset().top);
                 }//endfor
                 if ( 1 != _.uniq(_fp_offsets).length )
                    continue;
                 _maxs[_element_index] = Math.max.apply(Math, _offsets[_element_index] );
                 for ( _fp_index = 0; _fp_index < _n_featured_pages; _fp_index++ ) {
                    var $__el      = $( $_fp_elements[ _fp_index ][ _element_index ] ),
                          __offset;

                    if ( $__el.length > 0 ) {
                       __offset = +_maxs[_element_index] - _offsets[_element_index][_fp_index];
                       if ( __offset )
                          $__el.css( 'paddingTop', parseFloat($__el.css('paddingTop')) + __offset );
                    }
                 }//endfor
              }//endfor
          }//endfunction
      },//endmethod
      bttArrow : function() {
            var doingAnimation = false,
                $_btt_arrow = $( '.czr-btta' );

            if ( 0 === $_btt_arrow.length )
                return;
            var bttArrowVisibility = function() {
                  if ( ! doingAnimation ) {
                     doingAnimation = true;

                     window.requestAnimationFrame( function() {
                          $_btt_arrow.toggleClass( 'show', czrapp.$_window.scrollTop() > ( czrapp.$_window.height() ) );
                          doingAnimation = false;
                     });
                  }
            };//bttArrowVisibility

            czrapp.$_window.on( 'scroll', _.throttle( bttArrowVisibility, 20 ) );
            bttArrowVisibility();
      },//bttArrow
      backToTop : function() {
            var $_html = $("html, body"),
                 _backToTop = function( evt ) {
                      return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                 };

            czrapp.$_body.on( 'click touchstart touchend czr-btt', '.czr-btt', function ( evt ) {
                  evt.preventDefault();
                  evt.stopPropagation();
                  $_html.on( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  $_html.animate({
                        scrollTop: 0
                  }, 1e3, function () {
                        $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  });
           });
      },
      anchorSmoothScroll : function( selector ) {
            var _excl_sels = ( czrapp.localized.anchorSmoothScrollExclude && _.isArray( czrapp.localized.anchorSmoothScrollExclude.simple ) ) ? czrapp.localized.anchorSmoothScrollExclude.simple.join(',') : '',
                self = this,
                $_links = $('a[data-anchor-scroll="true"][href^="#"]').not( _excl_sels );
            if ( czrapp.localized.isAnchorScrollEnabled ) {
                $_links = $_links.add( '#content a[href^="#"]').not( _excl_sels );
            }
            _deep_excl = _.isObject( czrapp.localized.anchorSmoothScrollExclude.deep ) ? czrapp.localized.anchorSmoothScrollExclude.deep : null ;
            if ( _deep_excl ) {
                  _links = _.toArray($_links).filter( function ( _el ) {
                    return ( 2 == ( ['ids', 'classes'].filter(
                                  function( sel_type) {
                                      return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                                  } ) ).length
                          );
                  });
            }

            $(_links).click( function () {
                  var anchor_id = $(this).attr("href");
                  if ( ! $(anchor_id).length )
                    return;

                  if ('#' != anchor_id) {
                      $('html, body').animate({
                          scrollTop: $(anchor_id).offset().top
                      }, 700, czrapp.localized.isAnchorScrollEnabled ? 'easeOutExpo' : 'linear' ); //<= the jquery effect library ( for the easeOutExpo effect ) is only available when czr_fn_is_checked( czr_fn_opt( 'tc_link_scroll' ) ),
                  }
                  return false;
            });//click
          },

   };//_methods{}

   czrapp.methods.UserXP = czrapp.methods.UserXP || {};
   $.extend( czrapp.methods.UserXP , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
    initOnDomReady : function() {
      this.$_push         = $('#czr-push-footer');
      this._class         = 'sticky-footer-enabled';
      this.$_page         = $('#tc-page-wrap');
      this.doingAnimation = false;

      setTimeout( function() {
        czrapp.$_body.trigger('refresh-sticky-footer');
      }, 50 );
    },
    stickyFooterEventListener : function() {
      var self = this;
      czrapp.$_window.on( 'resize', function() {
        self.stickyFooterEventHandler('resize');
      });
      czrapp.$_window.on( 'golden-ratio-applied', function() {
        self.stickyFooterEventHandler('refresh');
      });
      czrapp.$_body.on( 'refresh-sticky-footer', function() {
        self.stickyFooterEventHandler('refresh');
      });

    },

    stickyFooterEventHandler : function( evt ) {
      var self = this;

      if ( ! this._is_sticky_footer_enabled() )
        return;

      switch ( evt ) {
        case 'resize':
          if ( !self.doingAnimation ) {
              self.doingAnimation = true;
              window.requestAnimationFrame(function() {
                  self._apply_sticky_footer();
                  self.doingAnimation = false;
              });
          }
        break;
        case 'refresh':
          this._apply_sticky_footer();
        break;
      }
    },
    _apply_sticky_footer : function() {

      var  _f_height     = this._get_full_height(),
           _w_height     = czrapp.$_window.height(),
           _push_height  = _w_height - _f_height,
           _event        = false;

      if ( _push_height > 0 ) {

        this.$_push.css('height', _push_height).addClass(this._class);
        _event = 'sticky-footer-on';

      }
      else if ( this.$_push.hasClass(this._class) ) {

        this.$_push.removeClass(this._class);
        _event = 'sticky-footer-off';

      }
      if ( _event )
        czrapp.$_body.trigger(_event);
    },
    _is_sticky_footer_enabled : function() {
      return czrapp.$_body.hasClass('czr-sticky-footer');
    },
    _get_full_height : function() {
      var _full_height = this.$_page.outerHeight(true) + this.$_page.offset().top,
          _push_height = 'block' == this.$_push.css('display') ? this.$_push.outerHeight() : 0;

      return _full_height - _push_height;
    }
  };//_methods{}

  czrapp.methods.StickyFooter = {};
  $.extend( czrapp.methods.StickyFooter , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
    initOnDomReady : function() {
      this._sidenav_selector        = '#tc-sn';

      if ( ! this._is_sn_on() )
        return;
      this._doingWindowAnimation    = false;

      this._sidenav_inner_class     = 'tc-sn-inner';
      this._sidenav_menu_class      = 'nav__menu-wrapper';

      this._toggle_event            = 'click';
      this._toggler_selector        = '[data-toggle="sidenav"]';
      this._active_class            = 'show';

      this._browser_can_translate3d = ! czrapp.$_html.hasClass('no-csstransforms3d');
      this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';
      this.sideNavEventListener();

      this._set_offset_height();

    },//init()
    sideNavEventListener : function() {
      var self = this;
      czrapp.$_body.on( this._toggle_event, '[data-toggle="sidenav"]', function( evt ) {
        self.sideNavEventHandler( evt, 'toggle' );
      });
      czrapp.$_body.on( this.transitionEnd, '#tc-sn', function( evt ) {
        self.sideNavEventHandler( evt, 'transitionend' );
      });
      czrapp.$_body.on( 'sn-close sn-open', function( evt ) {
        self.sideNavEventHandler( evt, evt.type );
      });
      czrapp.$_window.on('resize', function( evt ) {
        self.sideNavEventHandler( evt, 'resize');
      });

      czrapp.$_window.scroll( function( evt ) {
        self.sideNavEventHandler( evt, 'scroll');
      });

    },
    maybeLoadScript : function() {
          var dfd = $.Deferred();
          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                return dfd.resolve().promise();
          } else {
                if ( czrapp.base.scriptLoadingStatus.mCustomScrollbar && 'pending' == czrapp.base.scriptLoadingStatus.mCustomScrollbar.state() ) {
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.done( function() {
                            dfd.resolve();
                      });
                      return dfd.promise();
                }
                czrapp.base.scriptLoadingStatus.mCustomScrollbar = czrapp.base.scriptLoadingStatus.mCustomScrollbar || $.Deferred();
                if ( $('head').find( '#czr-custom-scroll-bar' ).length < 1 ) {
                      $('head').append( $('<link/>' , {
                            rel : 'stylesheet',
                            id : 'czr-custom-scroll-bar',
                            type : 'text/css',
                            href : czrapp.localized.assetsPath + 'css/jquery.mCustomScrollbar.min.css'
                      }) );
                }
                $.ajax( {
                      url : ( czrapp.localized.assetsPath + 'js/libs/jquery-mCustomScrollbar.min.js'),
                      cache : true,// use the browser cached version when availabl
                      dataType: "script"
                }).done(function() {
                      if ( 'function' != typeof $.fn.mCustomScrollbar )
                        return dfd.rejected();
                      czrapp.base.scriptLoadingStatus.mCustomScrollbar.resolve();

                      dfd.resolve();
                }).fail( function() {
                      czrapp.errorLog( 'mCustomScrollbar instantiation failed' );
                });
          }
          return dfd.promise();
    },
    sideNavEventHandler : function( evt, evt_name ) {
          var self = this;
          var _do = function() {
                switch ( evt_name ) {
                      case 'toggle':
                        if ( ! self._is_translating() )
                          self._toggle_callback( evt );
                      break;

                      case 'transitionend' :
                         if ( self._is_translating() && evt.target == $( self._sidenav_selector ).get()[0] )
                           self._transition_end_callback();
                      break;

                      case 'sn-open'  :
                          self._end_visibility_toggle();
                      break;

                      case 'sn-close' :
                          self._end_visibility_toggle();
                          self._set_offset_height();
                      break;

                      case 'scroll' :
                      case 'resize' :
                        setTimeout( function() {
                          if ( ! self._doingWindowAnimation  ) {
                            self._doingWindowAnimation  = true;
                            window.requestAnimationFrame( function() {
                              self._set_offset_height();
                              self._doingWindowAnimation  = false;
                            });
                          }
                        }, 200);

                      break;
                }
          };

          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                if (  ! $( '.' + self._sidenav_menu_class, self._sidenav_selector).data( 'mCustomScrollbar' ) ) {
                      self._init_scrollbar();
                }
                _do();
          } else {
                if ( 0 < $( '.' + self._sidenav_menu_class, self._sidenav_selector ).length ) {
                      if ( 'toggle' == evt_name ) {
                            self.maybeLoadScript().done( function() {
                                  self._init_scrollbar();
                                  _do();
                            });
                      }
                }
          }
    },
    _init_scrollbar : function() {
          var self = this;
          var _init = function() {
                $( '.' + self._sidenav_menu_class, self._sidenav_selector ).mCustomScrollbar({
                      theme: czrapp.$_body.hasClass('header-skin-light') ? 'minimal-dark' : 'minimal',
                });
                $( '.' + self._sidenav_menu_class, self._sidenav_selector).data( 'mCustomScrollbar', true );
          };

          if ( 'function' == typeof $.fn.mCustomScrollbar ) {
                _init();
          } else {
                self.maybeLoadScript().done( function() {
                      _init();
                });
          }
    },

    _toggle_callback : function ( evt ){
      evt.preventDefault();

      if ( czrapp.$_body.hasClass( 'tc-sn-visible' ) )
        this._anim_type = 'sn-close';
      else
        this._anim_type = 'sn-open';
      var _aria_expanded_attr = 'sn-open' == this._anim_type; //boolean
      $( this._toggler_selector ).attr('aria-expanded', _aria_expanded_attr );
      $( this._sidenav_selector ).attr('aria-expanded', _aria_expanded_attr );
      if ( this._browser_can_translate3d ){
        czrapp.$_body.addClass( 'animating ' + this._anim_type )
                     .trigger( this._anim_type + '_start' );
      } else {
        czrapp.$_body.toggleClass('tc-sn-visible')
                     .trigger( this._anim_type );
      }

      return false;
   },

    _transition_end_callback : function() {
      czrapp.$_body.removeClass( 'animating ' +  this._anim_type)
                   .toggleClass( 'tc-sn-visible' )
                   .trigger( this._anim_type + '_end' )
                   .trigger( this._anim_type );

    },

    _end_visibility_toggle : function() {
      $( this._toggler_selector ).toggleClass( 'collapsed' );
      $( this._sidenav_selector ).toggleClass( this._active_class );

    },
    _is_sn_on : function() {
      return $( this._sidenav_selector ).length > 0 ? true : false;
    },
    _get_initial_offset : function() {
      var _initial_offset = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;
      _initial_offset = _initial_offset && czrapp.$_window.scrollTop() && 'absolute' == czrapp.$_wpadminbar.css('position') ? 0 : _initial_offset;

      return _initial_offset; /* add a custom offset ?*/
    },
    _set_offset_height : function() {
      var _offset         = this._get_initial_offset(),
          $_sidenav_menu  = $( '.' + this._sidenav_menu_class, this._sidenav_selector ),
          $_sidenav       = $( this._sidenav_selector );

      if ( ! ( $_sidenav_menu.length && $_sidenav.length ) )
        return;

      var winHeight       = 'undefined' === typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
          newMaxHeight    = winHeight - $_sidenav_menu.offset().top + czrapp.$_window.scrollTop();

      $_sidenav_menu.css('height' , newMaxHeight + 'px');
      $_sidenav.css('top', _offset );

    },
    _is_translating : function() {

      return czrapp.$_body.hasClass('animating');

    },

  };//_methods{}

  czrapp.methods.SideNav = {};
  $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
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
      };
      this.ClassName = {
        DROPDOWN         : 'czr-dropdown-menu',
        SHOW             : 'show',
        PARENTS          : 'menu-item-has-children'
      };

      this.Selector = {
        DATA_TOGGLE              : '[data-toggle="czr-dropdown"]',
        DATA_SHOWN_TOGGLE        : '.' +this.ClassName.SHOW+ '> [data-toggle="czr-dropdown"]',
        DATA_HOVER_PARENT        : '.czr-open-on-hover .menu-item-has-children, .nav__woocart',
        DATA_CLICK_PARENT        : '.czr-open-on-click .menu-item-has-children',
        DATA_PARENTS             : '.tc-header .menu-item-has-children'
      };
    },
    dropdownMenuOnHover : function() {
      var _dropdown_selector = this.Selector.DATA_HOVER_PARENT,
          self               = this;

      enableDropdownOnHover();

      function _addOpenClass ( evt ) {

        var $_el = $(this);
        _debounced_addOpenClass = _.debounce( function() {
          if( 'static' == $_el.find( '.'+self.ClassName.DROPDOWN ).css( 'position' ) )
            return false;

          if ( ! $_el.hasClass(self.ClassName.SHOW) ) {
            $_el.addClass(self.ClassName.SHOW)
                .trigger(self.Event.SHOWN);

            var $_data_toggle = $_el.children( self.Selector.DATA_TOGGLE );

            if ( $_data_toggle.length )
                $_data_toggle[0].setAttribute('aria-expanded', 'true');
          }

        }, 30);

        _debounced_addOpenClass();
      }

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

        }, 30);

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
      czrapp.$_body.on( this.Event.CLICK, this.Selector.DATA_SHOWN_TOGGLE, function(evt) {

            var $_el = $(this);
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
    dropdownPlacement : function() {
      var self = this,
          doingAnimation = false;

      czrapp.$_window
          .on( 'resize', function() {
                  if ( ! doingAnimation ) {
                        doingAnimation = true;
                        window.requestAnimationFrame(function() {
                          $( '.'+self.ClassName.PARENTS+'.'+self.ClassName.SHOW)
                              .trigger(self.Event.PLACE_ME);
                          doingAnimation = false;
                        });
                  }

          });

      czrapp.$_body
          .on( this.Event.PLACE_ALL, function() {
                      $( '.'+self.ClassName.PARENTS )
                          .trigger(self.Event.PLACE_ME);
          })
          .on( this.Event.SHOWN+' '+this.Event.PLACE_ME, this.Selector.DATA_PARENTS, function(evt) {
            evt.stopPropagation();
            _do_snake( $(this), evt );
          });
      function _do_snake( $_el, evt ) {

        if ( !( evt && evt.namespace && self.DATA_KEY === evt.namespace ) )
          return;

        var $_this       = $_el,
            $_dropdown   = $_this.children( '.'+self.ClassName.DROPDOWN );

        if ( !$_dropdown.length )
          return;

        $_dropdown.css( 'zIndex', '-100' ).css('display', 'block');

        _maybe_move( $_dropdown, $_el );
        $_dropdown.css( 'zIndex', '').css('display', '');

      }


      function _maybe_move( $_dropdown, $_el ) {
        var $_a = $_el.find( self.Selector.DATA_TOGGLE ).first(),
            $_caret = $_el.find('.caret__dropdown-toggler').first(),
            _openLeft = function() {
                $_dropdown.removeClass( 'open-right' ).addClass( 'open-left' );
                if ( 1 == $_caret.length ) {
                    $_caret.removeClass( 'open-right' ).addClass( 'open-left' );
                    if ( 1 == $_a.length )
                      $_a.addClass('flex-row-reverse');
                }
            },
            _openRight = function() {
                $_dropdown.removeClass( 'open-left' ).addClass( 'open-right' );
                if ( 1 == $_caret.length ) {
                    $_caret.removeClass( 'open-left' ).addClass( 'open-right' );
                    if ( 1 == $_a.length )
                      $_a.removeClass('flex-row-reverse');
                }
            };
        if ( $_dropdown.parent().closest( '.'+self.ClassName.DROPDOWN ).hasClass( 'open-left' ) ) {
            _openLeft();
        } else {
          _openRight();
        }
        if ( $_dropdown.offset().left + $_dropdown.width() > czrapp.$_window.width() ) {
          _openLeft();
        } else if ( $_dropdown.offset().left < 0 ) {
          _openRight();
        }
      }
    }


  };//_methods{}

  czrapp.methods.Dropdowns = {};
  $.extend( czrapp.methods.Dropdowns , _methods );


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
      NAVBAR_NAV: '.regular-nav',
      VISIBLE_ITEMS: '.dropdown-menu .dropdown-item:not(.disabled)'
    };

    var czrDropdown = function ($) {

      var czrDropdown = function () {
        function czrDropdown(element) {
          _classCallCheck(this, czrDropdown);

          this._element = element;

          this._addEventListeners();
        }

        czrDropdown.prototype.toggle = function toggle() {
          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
            return false;
          }
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

        czrDropdown.prototype._addEventListeners = function _addEventListeners() {
          $(this._element).on(Event.CLICK, this.toggle);
        };

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
            if ('ontouchstart' in document.documentElement) {
              $('body').children().off('mouseover', null, $.noop);
            }


            toggles[i].setAttribute('aria-expanded', 'false');

            $(parent).removeClass(ClassName.SHOW).trigger($.Event(Event.HIDDEN, relatedTarget));
          }
        };

        czrDropdown._getParentFromElement = function _getParentFromElement(element) {
          var _parentNode = void 0;
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
          var items = $(parent).find(Selector.VISIBLE_ITEMS).get();

          if (!items.length) {
            return;
          }

          var index = items.indexOf(event.target);

          if (event.which === ARROW_UP_KEYCODE && index > 0) {
            index--;
          }

          if (event.which === ARROW_DOWN_KEYCODE && index < items.length - 1) {
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

      $(document)
        .on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, czrDropdown._dataApiKeydownHandler)
        .on(Event.KEYDOWN_DATA_API, Selector.MENU, czrDropdown._dataApiKeydownHandler)
        .on(Event.CLICK_DATA_API + ' ' + Event.KEYUP_DATA_API, czrDropdown._clearMenus)
        .on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, czrDropdown.prototype.toggle)
        .on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
          e.stopPropagation();
      });

      $.fn[NAME] = czrDropdown._jQueryInterface;
      $.fn[NAME].Constructor = czrDropdown;
      $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return czrDropdown._jQueryInterface;
      };

      return czrDropdown;

  }(jQuery);

})(jQuery, czrapp);var czrapp = czrapp || {};

( function ( czrapp, $, _ ) {
      $.extend( czrapp, czrapp.Events );
      czrapp.Root           = czrapp.Class.extend( {
            initialize : function( options ) {
                  $.extend( this, options || {} );
                  this.isReady = $.Deferred();
            },
            ready : function() {
                  var self = this;
                  if ( self.dom_ready && _.isArray( self.dom_ready ) ) {
                        czrapp.status = czrapp.status || [];
                        _.each( self.dom_ready , function( _m_ ) {
                              if ( ! _.isFunction( _m_ ) && ! _.isFunction( self[_m_]) ) {
                                    czrapp.status.push( 'Method ' + _m_ + ' was not found and could not be fired on DOM ready.');
                                    return;
                              }
                              try { ( _.isFunction( _m_ ) ? _m_ : self[_m_] ).call( self ); } catch( er ){
                                    czrapp.status.push( [ 'NOK', self.id + '::' + _m_, _.isString( er ) ? czrapp._truncate( er ) : er ].join( ' => ') );
                                    return;
                              }
                        });
                  }
                  this.isReady.resolve();
            }
      });

      czrapp.Base           = czrapp.Root.extend( czrapp.methods.Base );
      czrapp.ready          = $.Deferred();
      czrapp.bind( 'czrapp-ready', function() {
            czrapp.ready.resolve();
      });
      var _instantianteAndFireOnDomReady = function( newMap, previousMap, isInitial ) {
            if ( ! _.isObject( newMap ) )
              return;
            _.each( newMap, function( params, name ) {
                  if ( czrapp[ name ] || ! _.isObject( params ) )
                    return;

                  params = _.extend(
                        {
                              ctor : {},//should extend czrapp.Base with custom methods
                              ready : [],//a list of method to execute on dom ready,
                              options : {}//can be used to pass a set of initial params to set to the constructors
                        },
                        params
                  );
                  var ctorOptions = _.extend(
                      {
                          id : name,
                          dom_ready : params.ready || []
                      },
                      params.options
                  );

                  try { czrapp[ name ] = new params.ctor( ctorOptions ); }
                  catch( er ) {
                        czrapp.errorLog( 'Error when loading ' + name + ' | ' + er );
                  }
            });
            $(function ($) {
                  _.each( newMap, function( params, name ) {
                        if ( czrapp[ name ] && czrapp[ name ].isReady && 'resolved' == czrapp[ name ].isReady.state() )
                          return;
                        if ( _.isObject( czrapp[ name ] ) && _.isFunction( czrapp[ name ].ready ) ) {
                              czrapp[ name ].ready();
                        }
                  });
                  czrapp.status = czrapp.status || 'OK';
                  if ( _.isArray( czrapp.status ) ) {
                        _.each( czrapp.status, function( error ) {
                              czrapp.errorLog( error );
                        });
                  }
                  czrapp.trigger( isInitial ? 'czrapp-ready' : 'czrapp-updated' );
            });
      };//_instantianteAndFireOnDomReady()
      czrapp.appMap = new czrapp.Value( {} );
      czrapp.appMap.bind( _instantianteAndFireOnDomReady );//<=THE MAP IS LISTENED TO HERE
      czrapp.customMap = new czrapp.Value( {} );
      czrapp.customMap.bind( _instantianteAndFireOnDomReady );//<=THE CUSTOM MAP IS LISTENED TO HERE


})( czrapp, jQuery, _ );/****************************************************************
* FORMER HARD CODED SCRIPTS MADE ENQUEUABLE WITH LOCALIZED PARAMS
*****************************************************************/
(function($, czrapp, _ ) {
    czrapp.ready.then( function() {
          if ( czrapp.localized.frontHelpNoticesOn && ! _.isEmpty( frontHelpNoticeParams ) ) {
                var _doAjax = function( _query_ ) {
                          var ajaxUrl = czrapp.localized.adminAjaxUrl, dfd = $.Deferred();
                          $.post( ajaxUrl, _query_ )
                                .done( function( _r ) {
                                      if ( '0' === _r ||  '-1' === _r )
                                        czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                                })
                                .fail( function( _r ) {
                                      czrapp.errorLog( 'placeHolder dismiss : ajax error for : ', _query_.action, _r );
                                })
                                .always( function() {
                                      dfd.resolve();
                                });
                          return dfd.promise();
                    },
                    _ajaxDismiss = function( _params_ ) {
                          var _query = {},
                              dfd = $.Deferred();

                          if ( ! _.isObject( _params_ ) ) {
                                czrapp.errorLog( 'placeHolder dismiss : wrong params' );
                                return;
                          }
                          _params_ = _.extend( {
                                action : '',
                                nonce : { 'id' : '', 'handle' : '' },
                                class : '',
                                remove_action : null,//for slider and fp
                                position : null,//for widgets
                          }, _params_ );
                          _query.action = _params_.action;
                          if ( ! _.isNull( _params_.remove_action ) )
                            _query.remove_action = _params_.remove_action;
                          if ( ! _.isNull( _params_.position ) )
                            _query.position = _params_.position;

                          _query[ _params_.nonce.id ] = _params_.nonce.handle;
                          _doAjax( _query ).done( function() { dfd.resolve(); });
                          return dfd.promise();
                    };
                _.each( frontHelpNoticeParams, function( _params_, _id_ ) {
                      _params_ = _.extend( {
                            active : false,
                            args : {
                                  action : '',
                                  nonce : { 'id' : '', 'handle' : '' },
                                  class : '',
                                  remove_action : null,//for slider and fp
                                  position : null,//for widgets
                            }
                      }, _params_ );

                      switch( _id_ ) {
                            case 'thumbnail' :
                            case 'smartload' :
                            case 'sidenav' :
                            case 'secondMenu' :
                            case 'mainMenu' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $( '.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $_el.closest('.' + _params_.args.class ).slideToggle( 'fast' );
                                                    });
                                              } );
                                        } );
                                  }
                            break;
                            case 'slider' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _params_.args.remove_action = 'remove_notice';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $_el.closest( '.' + _params_.args.class ).slideToggle('fast');
                                                    });
                                              } );
                                              $('.tc-inline-remove', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    _params_.args.remove_action = 'remove_slider';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $( 'div[id*="customizr-slider"]' ).fadeOut('slow');
                                                    });

                                              } );
                                        } );
                                  }
                            break;
                            case 'fp' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    _params_.args.remove_action = 'remove_notice';
                                                    _ajaxDismiss(  _params_.args ).done( function() {
                                                          $_el.closest( '.' + _params_.args.class ).slideToggle('fast');
                                                    });
                                              } );
                                              $('.tc-inline-remove', '.' + _params_.args.class ).click( function( ev ) {
                                                    ev.preventDefault();
                                                    _params_.args.remove_action = 'remove_fp';
                                                    _ajaxDismiss( _params_.args ).done( function() {
                                                          $('#main-wrapper > .marketing').fadeOut('slow');
                                                    });

                                              } );
                                        } );
                                  }
                            break;
                            case 'widget' :
                                  if ( _params_.active ) {
                                        $( function($) {
                                              $('.tc-dismiss-notice, .tc-inline-dismiss-notice').click( function( ev ) {
                                                    ev.preventDefault();
                                                    var $_el = $(this);
                                                    var _position = $_el.attr('data-position');
                                                    if ( ! _position || ! _position.length )
                                                      return;

                                                     _params_.args.position = _position;
                                                    _ajaxDismiss(  _params_.args ).done( function() {
                                                          if ( 'sidebar' == _position )
                                                            $('.tc-widget-placeholder' , '.tc-sidebar').slideToggle('fast');
                                                          else
                                                            $_el.closest('.tc-widget-placeholder').slideToggle('fast');
                                                    });
                                              } );
                                        } );
                                  }
                            break;
                      }//switch
                });//_.each()
          }//if czrapp.localized.frontHelpNoticesOn && ! _.isEmpty( frontHelpNoticeParams
    });

})(jQuery, czrapp, _ );var czrapp = czrapp || {};
( function ( czrapp, $, _ ) {
      czrapp.localized = CZRParams || {};
      var appMap = {
                base : {
                      ctor : czrapp.Base,
                      ready : [
                            'cacheProp'
                      ]
                },
                browserDetect : {
                      ctor : czrapp.Base.extend( czrapp.methods.BrowserDetect ),
                      ready : [ 'addBrowserClassToBody' ]
                },
                jqPlugins : {
                      ctor : czrapp.Base.extend( czrapp.methods.JQPlugins ),
                      ready : [
                            'centerImagesWithDelay',
                            'centerInfinity',
                            'imgSmartLoad',
                            'lightBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnCzrReady',//<= fires all carousels : main, galleries, related posts + center images
                      ]
                },
                dropdowns : {
                      ctor  : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [
                            'initOnCzrReady',
                            'dropdownMenuOnHover',
                            'dropdownOpenGoToLinkOnClick',
                            'dropdownPlacement'//snake
                      ]
                },
                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'setupUIListeners',//<= setup various observable values like this.isScrolling, this.scrollPosition, ...

                            'stickifyHeader',

                            'outline',

                            'variousHoverActions',
                            'formFocusAction',
                            'variousHeaderActions',
                            'headerSearchToLife',

                            'smoothScroll',

                            'attachmentsFadeEffect',

                            'onEscapeKeyPressed',

                            'featuredPagesAlignment',
                            'bttArrow',
                            'backToTop',

                            'anchorSmoothScroll',

                            'mayBePrintFrontNote',
                      ]
                },
                stickyFooter : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyFooter ),
                      ready : [
                            'initOnDomReady',
                            'stickyFooterEventListener'
                      ]
                },
                sideNav : {
                      ctor : czrapp.Base.extend( czrapp.methods.SideNav ),
                      ready : [
                            'initOnDomReady'
                      ]
                }
      };//map
      czrapp.appMap( appMap , true );//true for isInitial map

})( czrapp, jQuery, _ );