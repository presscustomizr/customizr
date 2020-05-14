
var TCParams = TCParams || {
	DisabledFeatures : {},
  centerAllImg: 1,
	FancyBoxAutoscale: 1,
	FancyBoxState: 1,
	HasComments: "",
	LeftSidebarClass: ".span3.left.tc-sidebar",
	LoadBootstrap: 1,
	LoadModernizr: 1,
	ReorderBlocks: 1,
	RightSidebarClass: ".span3.right.tc-sidebar",
	SliderDelay: +5000,
	SliderHover: 1,
	SliderName: "demo",
  centerSliderImg : 1,
	SmoothScroll: { Enabled : 1 , Options : {} },
	anchorSmoothScroll: "linear",
  anchorSmoothScrollExclude : {
      simple : ['[class*=edd]', '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]', '[class*=um-]'],
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
};
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
var TCParams = TCParams || {};

!function ($) {

  "use strict"; // jshint ;_;

  $(function () {

    $.support.transition = (function () {

      var transitionEnd = (function () {

        var el = document.createElement('bootstrap')
          , transEndEventNames = {
               'WebkitTransition' : 'webkitTransitionEnd'
            ,  'MozTransition'    : 'transitionend'
            ,  'OTransition'      : 'oTransitionEnd otransitionend'
            ,  'transition'       : 'transitionend'
            }
          , name

        for (name in transEndEventNames){
          if (el.style[name] !== undefined) {
            return transEndEventNames[name]
          }
        }

      }())

      return transitionEnd && {
        end: transitionEnd
      }

    })()

  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Modal = function (element, options) {
    this.options = options
    this.$element = $(element)
      .delegate('[data-dismiss="modal"]', 'click.dismiss.modal', $.proxy(this.hide, this))
    this.options.remote && this.$element.find('.modal-body').load(this.options.remote)
  }

  Modal.prototype = {

      constructor: Modal

    , toggle: function () {
        return this[!this.isShown ? 'show' : 'hide']()
      }

    , show: function () {
        var that = this
          , e = $.Event('show')

        this.$element.trigger(e)

        if (this.isShown || e.isDefaultPrevented()) return

        this.isShown = true

        this.escape()

        this.backdrop(function () {
          var transition = $.support.transition && that.$element.hasClass('fade')

          if (!that.$element.parent().length) {
            that.$element.appendTo(document.body) //don't move modals dom position
          }

          that.$element.show()

          if (transition) {
            that.$element[0].offsetWidth // force reflow
          }

          that.$element
            .addClass('in')
            .attr('aria-hidden', false)

          that.enforceFocus()

          transition ?
            that.$element.one($.support.transition.end, function () { that.$element.focus().trigger('shown') }) :
            that.$element.focus().trigger('shown')

        })
      }

    , hide: function (e) {
        e && e.preventDefault()

        var that = this

        e = $.Event('hide')

        this.$element.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        this.escape()

        $(document).off('focusin.modal')

        this.$element
          .removeClass('in')
          .attr('aria-hidden', true)

        $.support.transition && this.$element.hasClass('fade') ?
          this.hideWithTransition() :
          this.hideModal()
      }

    , enforceFocus: function () {
        var that = this
        $(document).on('focusin.modal', function (e) {
          if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
            that.$element.focus()
          }
        })
      }

    , escape: function () {
        var that = this
        if (this.isShown && this.options.keyboard) {
          this.$element.on('keyup.dismiss.modal', function ( e ) {
            e.which == 27 && that.hide()
          })
        } else if (!this.isShown) {
          this.$element.off('keyup.dismiss.modal')
        }
      }

    , hideWithTransition: function () {
        var that = this
          , timeout = setTimeout(function () {
              that.$element.off($.support.transition.end)
              that.hideModal()
            }, 500)

        this.$element.one($.support.transition.end, function () {
          clearTimeout(timeout)
          that.hideModal()
        })
      }

    , hideModal: function () {
        var that = this
        this.$element.hide()
        this.backdrop(function () {
          that.removeBackdrop()
          that.$element.trigger('hidden')
        })
      }

    , removeBackdrop: function () {
        this.$backdrop && this.$backdrop.remove()
        this.$backdrop = null
      }

    , backdrop: function (callback) {
        var that = this
          , animate = this.$element.hasClass('fade') ? 'fade' : ''

        if (this.isShown && this.options.backdrop) {
          var doAnimate = $.support.transition && animate

          this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
            .appendTo(document.body)

          this.$backdrop.click(
            this.options.backdrop == 'static' ?
              $.proxy(this.$element[0].focus, this.$element[0])
            : $.proxy(this.hide, this)
          )

          if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

          this.$backdrop.addClass('in')

          if (!callback) return

          doAnimate ?
            this.$backdrop.one($.support.transition.end, callback) :
            callback()

        } else if (!this.isShown && this.$backdrop) {
          this.$backdrop.removeClass('in')

          $.support.transition && this.$element.hasClass('fade')?
            this.$backdrop.one($.support.transition.end, callback) :
            callback()

        } else if (callback) {
          callback()
        }
      }
  }

  var old = $.fn.modal

  $.fn.modal = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('modal')
        , options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option)
      if (!data) $this.data('modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option]()
      else if (options.show) data.show()
    })
  }

  $.fn.modal.defaults = {
      backdrop: true
    , keyboard: true
    , show: true
  }

  $.fn.modal.Constructor = Modal

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }

  $(document).on('click.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this = $(this)
      , href = $this.attr('href')
      , $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
      , option = $target.data('modal') ? 'toggle' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data())

    e.preventDefault()

    $target
      .modal(option)
      .one('hide', function () {
        $this.focus()
      })
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var toggle = '[data-toggle=dropdown]'
    , Dropdown = function (element) {
        var $el = $(element).on('click.dropdown.data-api', this.toggle)
        $('html').on('click.dropdown.data-api', function () {
          $el.parent().removeClass('open')
        })
      }

  Dropdown.prototype = {

    constructor: Dropdown

  , toggle: function (e) {
      var $this = $(this)
        , $parent
        , isActive

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)
      isActive = $parent.hasClass('open') && ! $parent.children('ul').is(':visible');

      clearMenus()

      if (!isActive) {
        if ('ontouchstart' in document.documentElement) {
          $('<div class="dropdown-backdrop"/>').insertBefore($(this)).on('click', clearMenus)
        }
        $parent.toggleClass('open')
      }

      $this.focus()

      return false
    }

  , keydown: function (e) {

      var $this
        , $items
        , $active
        , $parent
        , isActive
        , index

      if (!/(38|40|27)/.test(e.keyCode)) return

      $this = $(this)

      e.preventDefault()
      e.stopPropagation()

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      if (!isActive || (isActive && e.keyCode == 27)) {
        if (e.which == 27) $parent.find(toggle).focus()
        return $this.click()
      }

      $items = $('[role=menu] li:not(.divider):visible a', $parent)

      if (!$items.length) return

      index = $items.index($items.filter(':focus'))

      if (e.keyCode == 38 && index > 0) index--                                        // up
      if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
      if (!~index) index = 0

      $items
        .eq(index)
        .focus()
    }

  }

  function clearMenus() {
    $('.dropdown-backdrop').remove()
    $(toggle).each(function () {
      getParent($(this)).removeClass('open')
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = selector && $(selector)

    if (!$parent || !$parent.length) $parent = $this.parent()

    return $parent
  }

  var old = $.fn.dropdown

  $.fn.dropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('dropdown')
      if (!data) $this.data('dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.dropdown.Constructor = Dropdown

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }

  $(document)
    .on('click.dropdown.data-api', clearMenus)
    .on('click.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
    .on('keydown.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)

}(window.jQuery);


+function ($) { "use strict";

  function ScrollSpy(element, options) {
    var href
    var process  = $.proxy(this.process, this)

    this.$element       = $(element).is('body') ? $(window) : $(element)
    this.$body          = $('body')
    this.$scrollElement = this.$element.on('scroll.bs.scroll-spy.data-api', process)
    this.options        = $.extend({}, ScrollSpy.DEFAULTS, options)
    this.selector       = (this.options.target
      || ((href = $(element).attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      || '') + ' .nav li > a'
    this.offsets        = $([])
    this.targets        = $([])
    this.activeTarget   = null

    this.refresh()
    this.process()
  }

  ScrollSpy.DEFAULTS = {
    offset: 10
  }

  ScrollSpy.prototype.refresh = function () {
    var offsetMethod = this.$element[0] == window ? 'offset' : 'position'

    this.offsets = $([])
    this.targets = $([])

    var self     = this
    var $targets = this.$body
      .find(this.selector)
      .map(function () {
        var $el   = $(this)
        var href  = $el.data('target') || $el.attr('href')
        var $href = /^#\w/.test(href) && $(href)

        return ($href
          && $href.length
          && [[ $href[offsetMethod]().top + (!$.isWindow(self.$scrollElement.get(0)) && self.$scrollElement.scrollTop()), href ]]) || null
      })
      .sort(function (a, b) { return a[0] - b[0] })
      .each(function () {
        self.offsets.push(this[0])
        self.targets.push(this[1])
      })
  }

  ScrollSpy.prototype.process = function () {
    var scrollTop    = this.$scrollElement.scrollTop() + this.options.offset
    var scrollHeight = this.$scrollElement[0].scrollHeight || this.$body[0].scrollHeight
    var maxScroll    = scrollHeight - this.$scrollElement.height()
    var offsets      = this.offsets
    var targets      = this.targets
    var activeTarget = this.activeTarget
    var i

    if (scrollTop >= maxScroll) {
      return activeTarget != (i = targets.last()[0]) && this.activate(i)
    }

    for (i = offsets.length; i--;) {
      activeTarget != targets[i]
        && scrollTop >= offsets[i]
        && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
        && this.activate( targets[i] )
    }
  }

  ScrollSpy.prototype.activate = function (target) {
    this.activeTarget = target

    $(this.selector)
      .parents('.active')
      .removeClass('active')

    var selector = this.selector
      + '[data-target="' + target + '"],'
      + this.selector + '[href="' + target + '"]'

    var active = $(selector)
      .parents('li')
      .addClass('active')

    if (active.parent('.dropdown-menu').length)  {
      active = active
        .closest('li.dropdown')
        .addClass('active')
    }

    active.trigger('activate')
  }

  var old = $.fn.scrollspy

  $.fn.scrollspy = function (option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.scrollspy')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.scrollspy', (data = new ScrollSpy(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.scrollspy.Constructor = ScrollSpy

  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old
    return this
  }

  $(window).on('load', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      $spy.scrollspy($spy.data())
    })
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.prototype = {

    constructor: Tab

  , show: function () {
      var $this = this.element
        , $ul = $this.closest('ul:not(.dropdown-menu)')
        , selector = $this.attr('data-target')
        , previous
        , $target
        , e

      if (!selector) {
        selector = $this.attr('href')
        selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
      }

      if ( $this.parent('li').hasClass('active') ) return

      previous = $ul.find('.active:last a')[0]

      e = $.Event('show', {
        relatedTarget: previous
      })

      $this.trigger(e)

      if (e.isDefaultPrevented()) return

      $target = $(selector)

      this.activate($this.parent('li'), $ul)
      this.activate($target, $target.parent(), function () {
        $this.trigger({
          type: 'shown'
        , relatedTarget: previous
        })
      })
    }

  , activate: function ( element, container, callback) {
      var $active = container.find('> .active')
        , transition = callback
            && $.support.transition
            && $active.hasClass('fade')

      function next() {
        $active
          .removeClass('active')
          .find('> .dropdown-menu > .active')
          .removeClass('active')

        element.addClass('active')

        if (transition) {
          element[0].offsetWidth // reflow for transition
          element.addClass('in')
        } else {
          element.removeClass('fade')
        }

        if ( element.parent('.dropdown-menu') ) {
          element.closest('li.dropdown').addClass('active')
        }

        callback && callback()
      }

      transition ?
        $active.one($.support.transition.end, next) :
        next()

      $active.removeClass('in')
    }
  }

  var old = $.fn.tab

  $.fn.tab = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tab')
      if (!data) $this.data('tab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tab.Constructor = Tab

  $.fn.tab.noConflict = function () {
    $.fn.tab = old
    return this
  }

  $(document).on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Tooltip = function (element, options) {
    this.init('tooltip', element, options)
  }

  Tooltip.prototype = {

    constructor: Tooltip

  , init: function (type, element, options) {
      var eventIn
        , eventOut
        , triggers
        , trigger
        , i

      this.type = type
      this.$element = $(element)
      this.options = this.getOptions(options)
      this.enabled = true

      triggers = this.options.trigger.split(' ')

      for (i = triggers.length; i--;) {
        trigger = triggers[i]
        if (trigger == 'click') {
          this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
        } else if (trigger != 'manual') {
          eventIn = trigger == 'hover' ? 'mouseenter' : 'focus'
          eventOut = trigger == 'hover' ? 'mouseleave' : 'blur'
          this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
          this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
        }
      }

      this.options.selector ?
        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
        this.fixTitle()
    }

  , getOptions: function (options) {
      options = $.extend({}, $.fn[this.type].defaults, this.$element.data(), options)

      if (options.delay && typeof options.delay == 'number') {
        options.delay = {
          show: options.delay
        , hide: options.delay
        }
      }

      return options
    }

  , enter: function (e) {
      var defaults = $.fn[this.type].defaults
        , options = {}
        , self

      this._options && $.each(this._options, function (key, value) {
        if (defaults[key] != value) options[key] = value
      }, this)

      self = $(e.currentTarget)[this.type](options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout)
      self.hoverState = 'in'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'in') self.show()
      }, self.options.delay.show)
    }

  , leave: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (this.timeout) clearTimeout(this.timeout)
      if (!self.options.delay || !self.options.delay.hide) return self.hide()

      self.hoverState = 'out'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'out') self.hide()
      }, self.options.delay.hide)
    }

  , show: function () {
      var $tip
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp
        , e = $.Event('show')

      if (this.hasContent() && this.enabled) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $tip = this.tip()
        this.setContent()

        if (this.options.animation) {
          $tip.addClass('fade')
        }

        placement = typeof this.options.placement == 'function' ?
          this.options.placement.call(this, $tip[0], this.$element[0]) :
          this.options.placement

        $tip
          .detach()
          .css({ top: 0, left: 0, display: 'block' })

        this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

        pos = this.getPosition()

        actualWidth = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight

        switch (placement) {
          case 'bottom':
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'top':
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
            break
        }

        this.applyPlacement(tp, placement)
        this.$element.trigger('shown')
      }
    }

  , applyPlacement: function(offset, placement){
      var $tip = this.tip()
        , width = $tip[0].offsetWidth
        , height = $tip[0].offsetHeight
        , actualWidth
        , actualHeight
        , delta
        , replace

      $tip
        .offset(offset)
        .addClass(placement)
        .addClass('in')

      actualWidth = $tip[0].offsetWidth
      actualHeight = $tip[0].offsetHeight

      if (placement == 'top' && actualHeight != height) {
        offset.top = offset.top + height - actualHeight
        replace = true
      }

      if (placement == 'bottom' || placement == 'top') {
        delta = 0

        if (offset.left < 0){
          delta = offset.left * -2
          offset.left = 0
          $tip.offset(offset)
          actualWidth = $tip[0].offsetWidth
          actualHeight = $tip[0].offsetHeight
        }

        this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
      } else {
        this.replaceArrow(actualHeight - height, actualHeight, 'top')
      }

      if (replace) $tip.offset(offset)
    }

  , replaceArrow: function(delta, dimension, position){
      this
        .arrow()
        .css(position, delta ? (50 * (1 - delta / dimension) + "%") : '')
    }

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()

      $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
      $tip.removeClass('fade in top bottom left right')
    }

  , hide: function () {
      var that = this
        , $tip = this.tip()
        , e = $.Event('hide')

      this.$element.trigger(e)
      if (e.isDefaultPrevented()) return

      $tip.removeClass('in')

      function removeWithAnimation() {
        var timeout = setTimeout(function () {
          $tip.off($.support.transition.end).detach()
        }, 500)

        $tip.one($.support.transition.end, function () {
          clearTimeout(timeout)
          $tip.detach()
        })
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        removeWithAnimation() :
        $tip.detach()

      this.$element.trigger('hidden')

      return this
    }

  , fixTitle: function () {
      var $e = this.$element
      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
        $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
      }
    }

  , hasContent: function () {
      return this.getTitle()
    }

  , getPosition: function () {
      var el = this.$element[0]
      return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
        width: el.offsetWidth
      , height: el.offsetHeight
      }, this.$element.offset())
    }

  , getTitle: function () {
      var title
        , $e = this.$element
        , o = this.options

      title = $e.attr('data-original-title')
        || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

      return title
    }

  , tip: function () {
      return this.$tip = this.$tip || $(this.options.template)
    }

  , arrow: function(){
      return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
    }

  , validate: function () {
      if (!this.$element[0].parentNode) {
        this.hide()
        this.$element = null
        this.options = null
      }
    }

  , enable: function () {
      this.enabled = true
    }

  , disable: function () {
      this.enabled = false
    }

  , toggleEnabled: function () {
      this.enabled = !this.enabled
    }

  , toggle: function (e) {
      var self = e ? $(e.currentTarget)[this.type](this._options).data(this.type) : this
      self.tip().hasClass('in') ? self.hide() : self.show()
    }

  , destroy: function () {
      this.hide().$element.off('.' + this.type).removeData(this.type)
    }

  }

  var old = $.fn.tooltip

  $.fn.tooltip = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tooltip')
        , options = typeof option == 'object' && option
      if (!data) $this.data('tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tooltip.Constructor = Tooltip

  $.fn.tooltip.defaults = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
  , trigger: 'hover focus'
  , title: ''
  , delay: 0
  , html: false
  , container: false
  }

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype, {

    constructor: Popover

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()
        , content = this.getContent()

      $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
      $tip.find('.popover-content')[this.options.html ? 'html' : 'text'](content)

      $tip.removeClass('fade top bottom left right in')
    }

  , hasContent: function () {
      return this.getTitle() || this.getContent()
    }

  , getContent: function () {
      var content
        , $e = this.$element
        , o = this.options

      content = (typeof o.content == 'function' ? o.content.call($e[0]) :  o.content)
        || $e.attr('data-content')

      return content
    }

  , tip: function () {
      if (!this.$tip) {
        this.$tip = $(this.options.template)
      }
      return this.$tip
    }

  , destroy: function () {
      this.hide().$element.off('.' + this.type).removeData(this.type)
    }

  })

  var old = $.fn.popover

  $.fn.popover = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('popover')
        , options = typeof option == 'object' && option
      if (!data) $this.data('popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.popover.Constructor = Popover

  $.fn.popover.defaults = $.extend({} , $.fn.tooltip.defaults, {
    placement: 'right'
  , trigger: 'click'
  , content: ''
  , template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
  })

  $.fn.popover.noConflict = function () {
    $.fn.popover = old
    return this
  }

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Affix = function (element, options) {
    this.options = $.extend({}, $.fn.affix.defaults, options)
    this.$window = $(window)
      .on('scroll.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.affix.data-api',  $.proxy(function () { setTimeout($.proxy(this.checkPosition, this), 1) }, this))
    this.$element = $(element)
    this.checkPosition()
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var scrollHeight = $(document).height()
      , scrollTop = this.$window.scrollTop()
      , position = this.$element.offset()
      , offset = this.options.offset
      , offsetBottom = offset.bottom
      , offsetTop = offset.top
      , reset = 'affix affix-top affix-bottom'
      , affix

    if (typeof offset != 'object') offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function') offsetTop = offset.top()
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom()

    affix = this.unpin != null && (scrollTop + this.unpin <= position.top) ?
      false    : offsetBottom != null && (position.top + this.$element.height() >= scrollHeight - offsetBottom) ?
      'bottom' : offsetTop != null && scrollTop <= offsetTop ?
      'top'    : false

    if (this.affixed === affix) return

    this.affixed = affix
    this.unpin = affix == 'bottom' ? position.top - scrollTop : null

    this.$element.removeClass(reset).addClass('affix' + (affix ? '-' + affix : ''))
  }

  var old = $.fn.affix

  $.fn.affix = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('affix')
        , options = typeof option == 'object' && option
      if (!data) $this.data('affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.affix.Constructor = Affix

  $.fn.affix.defaults = {
    offset: 0
  }

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
        , data = $spy.data()

      data.offset = data.offset || {}

      data.offsetBottom && (data.offset.bottom = data.offsetBottom)
      data.offsetTop && (data.offset.top = data.offsetTop)

      $spy.affix(data)
    })
  })


}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var dismiss = '[data-dismiss="alert"]'
    , Alert = function (el) {
        $(el).on('click', dismiss, this.close)
      }

  Alert.prototype.close = function (e) {
    var $this = $(this)
      , selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)

    e && e.preventDefault()

    $parent.length || ($parent = $this.hasClass('alert') ? $this : $this.parent())

    $parent.trigger(e = $.Event('close'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      $parent
        .trigger('closed')
        .remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent.on($.support.transition.end, removeElement) :
      removeElement()
  }

  var old = $.fn.alert

  $.fn.alert = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('alert')
      if (!data) $this.data('alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.alert.Constructor = Alert

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }

  $(document).on('click.alert.data-api', dismiss, Alert.prototype.close)

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Button = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.button.defaults, options)
  }

  Button.prototype.setState = function (state) {
    var d = 'disabled'
      , $el = this.$element
      , data = $el.data()
      , val = $el.is('input') ? 'val' : 'html'

    state = state + 'Text'
    data.resetText || $el.data('resetText', $el[val]())

    $el[val](data[state] || this.options[state])
    setTimeout(function () {
      state == 'loadingText' ?
        $el.addClass(d).attr(d, d) :
        $el.removeClass(d).removeAttr(d)
    }, 0)
  }

  Button.prototype.toggle = function () {
    var $parent = this.$element.closest('[data-toggle="buttons-radio"]')

    $parent && $parent
      .find('.active')
      .removeClass('active')

    this.$element.toggleClass('active')
  }

  var old = $.fn.button

  $.fn.button = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('button')
        , options = typeof option == 'object' && option
      if (!data) $this.data('button', (data = new Button(this, options)))
      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  $.fn.button.defaults = {
    loadingText: 'loading...'
  }

  $.fn.button.Constructor = Button

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }

  $(document).on('click.button.data-api', '[data-toggle^=button]', function (e) {
    var $btn = $(e.target)
    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
    $btn.button('toggle')
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Collapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.collapse.defaults, options)
    this._collapsed = true;

    if (this.options.parent) {
      this.$parent = $(this.options.parent)
    }

    this.options.toggle && this.toggle()
  }

  Collapse.prototype = {

    constructor: Collapse

  , dimension: function () {
      var hasWidth = this.$element.hasClass('width')
      return hasWidth ? 'width' : 'height'
    }

  , show: function () {

      var dimension
        , scroll
        , actives
        , hasData
      if (this.transitioning || this.$element.hasClass('in') ) return

      dimension = this.dimension()
      scroll = $.camelCase(['scroll', dimension].join('-'))
      actives = this.$parent && this.$parent.find('> .accordion-group > .in')

      if (actives && actives.length) {
        hasData = actives.data('collapse')
        if (hasData && hasData.transitioning) return
        actives.collapse('hide')
        hasData || actives.data('collapse', null)
      }

      this.$element[dimension](0)
      this.transition('addClass', $.Event('show.czrCollapse'), 'shown.czrCollapse')
      $.support.transition && this.$element[dimension](this.$element[0][scroll])
      this._collapsed = false;
      if ( ! this.$element.hasClass('nav-collapse') )
          return;

      if ( TCParams && 1 == TCParams.dropdowntoViewport )
      {
        var winHeight = 'undefined' === typeof window.innerHeight ? window.innerHeight : czrapp.$_window.height(),
            tcVisible = winHeight - this.$element.offset().top + czrapp.$_window.scrollTop();
        this.$element.css('max-height' , tcVisible + 'px');
      }
      else if ( TCParams && 1 != TCParams.dropdowntoViewport && 1 == TCParams.stickyHeader )
      {
        if ( 0 != $('.back-to-top').length ) {
          $('.back-to-top').trigger('click');
        }
        else {
          $('html, body').animate({
                  scrollTop: 0
              }, 700);
        }
        $('body').removeClass('sticky-enabled').removeClass('tc-sticky-header');
      }

    }//end of show:

  , hide: function () {
      var dimension
      if (this.transitioning || ( this._collapsed && !this.$element.hasClass('in') ) ) return
      dimension = this.dimension()
      this.reset(this.$element[dimension]())
      this.transition('removeClass', $.Event('hide.czrCollapse'), 'hidden.czrCollapse')
      this.$element[dimension](0)
      this._collapsed = true;
      if ( ! this.$element.hasClass('nav-collapse') )
          return;

      if ( TCParams && 1 != TCParams.dropdowntoViewport && 1 == TCParams.stickyHeader ) {
        $('body').addClass('tc-sticky-header');
      }
    }

  , reset: function (size) {
      var dimension = this.dimension()

      this.$element
        .removeClass('collapse')
        [dimension](size || 'auto')
        [0].offsetWidth

      this.$element[size !== null ? 'addClass' : 'removeClass']('collapse')

      return this
    }

  , transition: function (method, startEvent, completeEvent) {
      var that = this
        , complete = function () {
            if (startEvent.type == 'show') that.reset()
            that.transitioning = 0
            that.$element.trigger(completeEvent)
          }

      this.$element.trigger(startEvent)

      if (startEvent.isDefaultPrevented()) return

      this.transitioning = 1

      this.$element[method]('in')

      $.support.transition && this.$element.hasClass('collapse') ?
        this.$element.one($.support.transition.end, complete) :
        complete()
    }

  , toggle: function () {
      this[this.$element.hasClass('in') || ! this._collapsed ? 'hide' : 'show']();
    }

  }

  var old = $.fn.collapse

  $.fn.collapse = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('collapse')
        , options = $.extend({}, $.fn.collapse.defaults, $this.data(), typeof option == 'object' && option)
      if (!data) $this.data('collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.collapse.defaults = {
    toggle: true
  }

  $.fn.collapse.Constructor = Collapse

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }
  $(document).on('click.collapse.data-api', '[data-toggle=collapse]', function (e) {
    var $this = $(this), href
      , target = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
      , option = $(target).data('collapse') ? 'toggle' : $this.data()
    $this[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
    $(target).collapse(option)
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;

  var Carousel = function (element, options) {
    this.$element = $(element)
    this.$indicators = this.$element.find('.carousel-indicators')
    this.options = options
    this.options.pause == 'hover' && this.$element
      .on('mouseenter', $.proxy(this.pause, this))
      .on('mouseleave', $.proxy(this.cycle, this))
  }

  Carousel.prototype = {
    cycle: function (e) {
      if (!e) this.paused = false
      if (this.interval) clearInterval(this.interval);
      this.options.interval
        && !this.paused
        && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))
      return this
    }

  , getActiveIndex: function () {
      this.$active = this.$element.find('.czr-item.active')
      this.$items = this.$active.parent().children()
      return this.$items.index(this.$active)
    }

  , to: function (pos) {
      var activeIndex = this.getActiveIndex()
        , that = this

      if (pos > (this.$items.length - 1) || pos < 0) return

      if (this.sliding) {
        return this.$element.one('customizr.slid', function () {
          that.to(pos)
        })
      }

      if (activeIndex == pos) {
        return this.pause().cycle()
      }

      return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.$items[pos]))
    }

  , pause: function (e) {
      if (!e) this.paused = true
      if (this.$element.find('.next, .prev').length && $.support.transition.end) {
        this.$element.trigger($.support.transition.end)
        this.cycle(true)
      }
      clearInterval(this.interval)
      this.interval = null
      return this
    }

  , next: function () {
      if (this.sliding) return
      return this.slide('next')
    }

  , prev: function () {
      if (this.sliding) return
      return this.slide('prev')
    }

  , slide: function (type, next) {
      if(!$.support.transition && this.$element.hasClass('customizr-slide')) {
         this.$element.find('.czr-item').stop(true, true); //Finish animation and jump to end.
      }
      var $active = this.$element.find('.czr-item.active')
        , $next = next || $active[type]()
        , isCycling = this.interval
        , direction = type == 'next' ? 'left' : 'right'
        , fallback  = type == 'next' ? 'first' : 'last'
        , that = this
        , e

      this.sliding = true

      isCycling && this.pause()

      $next = $next.length ? $next : this.$element.find('.czr-item')[fallback]()

      e = $.Event('customizr.slide', {
        relatedTarget: $next[0]
      , direction: direction
      })

      if ($next.hasClass('active')) return

      if (this.$indicators.length) {
        this.$indicators.find('.active').removeClass('active')
        this.$element.one('customizr.slid', function () {
          var $nextIndicator = $(that.$indicators.children()[that.getActiveIndex()])
          $nextIndicator && $nextIndicator.addClass('active')
        })
      }

      if ($.support.transition && this.$element.hasClass('customizr-slide')) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        if ( 0 !== $next.find('img').length )
          $next.find('img').trigger('customizr.slide');
        $next.addClass(type)
        $next[0].offsetWidth // force reflow
        $active.addClass(direction)
        $next.addClass(direction)
        this.$element.one($.support.transition.end, function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () {
            that.$element.trigger('customizr.slid');
            if ( 0 !== $next.find('img').length )
              $next.find('img').trigger('customizr.slid');
          }, 0)
        })
      } else if(!$.support.transition && this.$element.hasClass('customizr-slide')) {
          this.$element.trigger(e)
          if (e.isDefaultPrevented()) return
          $active.animate({left: (direction == 'right' ? '100%' : '-100%')}, 600, function(){
             $active.removeClass('active')
              that.sliding = false
              setTimeout(function () { that.$element.trigger('customizr.slid') }, 0)
            })
           $next.addClass(type).css({left: (direction == 'right' ? '-100%' : '100%')}).animate({left: '0'}, 600,  function(){
               $next.removeClass(type).addClass('active')
           })
        } else {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $active.removeClass('active')
        $next.addClass('active')
        this.sliding = false
        this.$element.trigger('customizr.slid')
      }

      isCycling && this.cycle()

      return this
    }

  }

  var old = $.fn.czrCarousel

  $.fn.czrCarousel = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('czr-carousel')
        , options = $.extend({}, $.fn.czrCarousel.defaults, typeof option == 'object' && option)
        , action = typeof option == 'string' ? option : options.slide
      if (!data) $this.data('czr-carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.pause().cycle()
    })
  }

  $.fn.czrCarousel.defaults = {
    interval: 5000
  , pause: 'hover'
  }

  $.fn.czrCarousel.Constructor = Carousel

  $.fn.czrCarousel.noConflict = function () {
    $.fn.czrCarousel = old
    return this
  }

  $(document).on('click.czr-carousel.data-api', '.customizr-slide [data-slide], .customizr-slide [data-slide-to]', function (e) {
    var $this = $(this), href
      , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      , options = $.extend({}, $target.data(), $this.data())
      , slideIndex

    $target.czrCarousel(options);

    if (slideIndex = $this.attr('data-slide-to')) {
      $target.data('czr-carousel').pause().to(slideIndex).cycle()
    }

    e.preventDefault()
  })

}(window.jQuery);


!function($){

  "use strict"; // jshint ;_;

  var Typeahead = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.typeahead.defaults, options)
    this.matcher = this.options.matcher || this.matcher
    this.sorter = this.options.sorter || this.sorter
    this.highlighter = this.options.highlighter || this.highlighter
    this.updater = this.options.updater || this.updater
    this.source = this.options.source
    this.$menu = $(this.options.menu)
    this.shown = false
    this.listen()
  }

  Typeahead.prototype = {

    constructor: Typeahead

  , select: function () {
      var val = this.$menu.find('.active').attr('data-value')
      this.$element
        .val(this.updater(val))
        .change()
      return this.hide()
    }

  , updater: function (item) {
      return item
    }

  , show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      })

      this.$menu
        .insertAfter(this.$element)
        .css({
          top: pos.top + pos.height
        , left: pos.left
        })
        .show()

      this.shown = true
      return this
    }

  , hide: function () {
      this.$menu.hide()
      this.shown = false
      return this
    }

  , lookup: function (event) {
      var items

      this.query = this.$element.val()

      if (!this.query || this.query.length < this.options.minLength) {
        return this.shown ? this.hide() : this
      }

      items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source

      return items ? this.process(items) : this
    }

  , process: function (items) {
      var that = this

      items = $.grep(items, function (item) {
        return that.matcher(item)
      })

      items = this.sorter(items)

      if (!items.length) {
        return this.shown ? this.hide() : this
      }

      return this.render(items.slice(0, this.options.items)).show()
    }

  , matcher: function (item) {
      return ~item.toLowerCase().indexOf(this.query.toLowerCase())
    }

  , sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
        else if (~item.indexOf(this.query)) caseSensitive.push(item)
        else caseInsensitive.push(item)
      }

      return beginswith.concat(caseSensitive, caseInsensitive)
    }

  , highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      })
    }

  , render: function (items) {
      var that = this

      items = $(items).map(function (i, item) {
        i = $(that.options.item).attr('data-value', item)
        i.find('a').html(that.highlighter(item))
        return i[0]
      })

      items.first().addClass('active')
      this.$menu.html(items)
      return this
    }

  , next: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , next = active.next()

      if (!next.length) {
        next = $(this.$menu.find('li')[0])
      }

      next.addClass('active')
    }

  , prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , prev = active.prev()

      if (!prev.length) {
        prev = this.$menu.find('li').last()
      }

      prev.addClass('active')
    }

  , listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this))

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this))
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
        .on('mouseleave', 'li', $.proxy(this.mouseleave, this))
    }

  , eventSupported: function(eventName) {
      var isSupported = eventName in this.$element
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;')
        isSupported = typeof this.$element[eventName] === 'function'
      }
      return isSupported
    }

  , move: function (e) {
      if (!this.shown) return

      switch(e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault()
          break

        case 38: // up arrow
          e.preventDefault()
          this.prev()
          break

        case 40: // down arrow
          e.preventDefault()
          this.next()
          break
      }

      e.stopPropagation()
    }

  , keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27])
      this.move(e)
    }

  , keypress: function (e) {
      if (this.suppressKeyPressRepeat) return
      this.move(e)
    }

  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break

        case 9: // tab
        case 13: // enter
          if (!this.shown) return
          this.select()
          break

        case 27: // escape
          if (!this.shown) return
          this.hide()
          break

        default:
          this.lookup()
      }

      e.stopPropagation()
      e.preventDefault()
  }

  , focus: function (e) {
      this.focused = true
    }

  , blur: function (e) {
      this.focused = false
      if (!this.mousedover && this.shown) this.hide()
    }

  , click: function (e) {
      e.stopPropagation()
      e.preventDefault()
      this.select()
      this.$element.focus()
    }

  , mouseenter: function (e) {
      this.mousedover = true
      this.$menu.find('.active').removeClass('active')
      $(e.currentTarget).addClass('active')
    }

  , mouseleave: function (e) {
      this.mousedover = false
      if (!this.focused && this.shown) this.hide()
    }

  }

  var old = $.fn.typeahead

  $.fn.typeahead = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('typeahead')
        , options = typeof option == 'object' && option
      if (!data) $this.data('typeahead', (data = new Typeahead(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.typeahead.defaults = {
    source: []
  , items: 8
  , menu: '<ul class="typeahead dropdown-menu"></ul>'
  , item: '<li><a href="#"></a></li>'
  , minLength: 1
  }

  $.fn.typeahead.Constructor = Typeahead

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old
    return this
  }

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this)
    if ($this.data('typeahead')) return
    $this.typeahead($this.data())
  })

}(window.jQuery);
(function ( $ ) {

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

})( jQuery );
(function ( $ ) {
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
    var // _first_el_text    = $_target.text(),
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
    var self = this,
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

    var //childTagName  = $_el.children().first()[0].tagName,
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
    return 'string' == typeof(_expr) ? _expr.replace(/[^\w-?!\u00bf-\u00ff]/g, _replaceBy ) : '';
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
})( jQuery);
(function ( $, window ) {
      var pluginName = 'imgSmartLoad',
          defaults = {
                load_all_images_on_first_scroll : false,
                attribute : [ 'data-src', 'data-srcset', 'data-sizes' ],
                excludeImg : [],
                threshold : 200,
                fadeIn_options : { duration : 400 },
                delaySmartLoadEvent : 0,

          },
          skipImgClass = 'tc-smart-loaded';


      function Plugin( element, options ) {
            this.element = element;
            this.options = $.extend( {}, defaults, options);
            if ( _.isArray( this.options.excludeImg ) ) {
                  this.options.excludeImg.push( '.'+skipImgClass );
            } else {
                  this.options.excludeImg = [ '.'+skipImgClass ];
            }
            this.options.excludeImg = _.uniq( this.options.excludeImg );
            this.imgSelectors = 'img[' + this.options.attribute[0] + ']:not('+ this.options.excludeImg.join() +')';

            this._defaults = defaults;
            this._name = pluginName;
            this.init();

            var self = this;
            $(this.element).on('trigger-smartload', function() {
                  self._maybe_trigger_load( 'trigger-smartload' );
            });
      }

      Plugin.prototype._getImgs = function() {
            return $( this.imgSelectors, this.element );
      };
      Plugin.prototype.init = function() {
            var self        = this;

            this.increment  = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
            this.timer      = 0;
            $('body').on( 'load_img', self.imgSelectors , function() {
                    if ( true === $(this).data('czr-smart-loaded' ) )
                      return;
                    self._load_img(this);
            });
            $(window).scroll( function( _evt ) { self._better_scroll_event_handler( _evt ); } );
            $(window).resize( _.debounce( function( _evt ) { self._maybe_trigger_load( _evt ); }, 100 ) );
            this._maybe_trigger_load( 'dom-ready');
            $(this.element).data('smartLoadDone', true );
      };
      Plugin.prototype._better_scroll_event_handler = function( _evt ) {
            var self = this;
            if ( ! this.doingAnimation ) {
                  this.doingAnimation = true;
                  window.requestAnimationFrame(function() {
                        self._maybe_trigger_load( _evt );
                        self.doingAnimation = false;
                  });
            }
      };
      Plugin.prototype._maybe_trigger_load = function(_evt ) {
            var self = this,
                $_imgs = self._getImgs(),
                _visible_list;

            if ( !_.isObject( $_imgs) || _.isEmpty( $_imgs ) )
              return;
            _visible_list = $_imgs.filter( function( ind, _img ) { return self._is_visible( _img ,  _evt ); } );
            _visible_list.map( function( ind, _img ) {
                  if ( true !== $(_img).data( 'czr-smart-loaded' ) ) {
                        $(_img).trigger('load_img');
                  }
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

            if ( $_img.parent().hasClass('smart-loading') )
              return;

            $_img.parent().addClass('smart-loading');

            $_img.unbind('load_img')
                  .removeAttr( this.options.attribute.join(' ') )
                  .attr( 'sizes' , _sizes )
                  .attr( 'srcset' , _src_set )
                  .attr( 'src', _src )
                  .load( function () {
                        if ( !$_img.hasClass(skipImgClass) ) {
                              $_img.fadeIn(self.options.fadeIn_options).addClass(skipImgClass);
                        }
                        if ( ( 'undefined' !== typeof $_img.attr('data-tcjp-recalc-dims')  ) && ( false !== $_img.attr('data-tcjp-recalc-dims') ) ) {
                              var _width  = $_img.originalWidth(),
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
})( jQuery, window );
(function ( $ ) {
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

})( jQuery );
(function ( $, window ) {
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
      Plugin.prototype._maybe_apply_golden_r = function() {
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
      Plugin.prototype._pre_img_cent = function( $_img ) {

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

})( jQuery, window );
(function ( $, window, _ ) {
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
})( jQuery, window, _ );// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
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
}());// Customizr version of Galambosi's SmoothScroll

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
    removeEvent(wheelEvent, wheel, wheelOpt);
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

function addEvent(type, fn, arg ) {
    window.addEventListener(type, fn, arg || false);
}

function removeEvent(type, fn, arg) {
    window.removeEventListener(type, fn, arg || false);
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

var supportsPassive = false;
try {
  window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
    get: function () {
            supportsPassive = true;
        }
    }));
} catch(e) {}

var wheelOpt = supportsPassive ? { passive: false } : false;
var wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';
function _maybeInit( fire ){
  if (wheelEvent) {
    addEvent(wheelEvent, wheel, wheelOpt);
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
    set_css('input[type=file]:focus,input[type=radio]:focus,input[type=checkbox]:focus,select:focus,span:focus,a:focus,button{outline:none!important;-webkit-box-shadow:none!important;box-shadow:none!important;}input[type=file]::-moz-focus-inner,input[type=radio]::-moz-focus-inner,input[type=checkbox]::-moz-focus-inner,select::-moz-focus-inner,a::-moz-focus-inner{border:0;}');
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
;var czrapp = czrapp || {};
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
      var _prettyPrintLog = function( args ) {
            var _defaults = {
                  bgCol : '#5ed1f5',
                  textCol : '#000',
                  consoleArguments : []
            };
            args = _.extend( _defaults, args );

            var _toArr = Array.from( args.consoleArguments ),
                _truncate = function( string ){
                      if ( ! _.isString( string ) )
                        return '';
                      return string.length > 300 ? string.substr( 0, 299 ) + '...' : string;
                };
            if ( ! _.isEmpty( _.filter( _toArr, function( it ) { return ! _.isString( it ); } ) ) ) {
                  _toArr =  JSON.stringify( _toArr.join(' ') );
            } else {
                  _toArr = _toArr.join(' ');
            }
            return [
                  '%c ' + _truncate( _toArr ),
                  [ 'background:' + args.bgCol, 'color:' + args.textCol, 'display: block;' ].join(';')
            ];
      };

      var _wrapLogInsideTags = function( title, msg, bgColor ) {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;
            if ( czrapp.localized.isDevMode ) {
                  if ( _.isUndefined( msg ) ) {
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '<' + title + '>' ] } ) );
                  } else {
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '<' + title + '>' ] } ) );
                        console.log( msg );
                        console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ '</' + title + '>' ] } ) );
                  }
            } else {
                  console.log.apply( console, _prettyPrintLog( { bgCol : bgColor, textCol : '#000', consoleArguments : [ title ] } ) );
            }
      };
      czrapp.consoleLog = function() {
            if ( ! czrapp.localized.isDevMode )
              return;
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;
            console.log.apply( console, _prettyPrintLog( { consoleArguments : arguments } ) );
            console.log( 'Unstyled console message : ', arguments );
      };

      czrapp.errorLog = function() {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            console.log.apply( console, _prettyPrintLog( { bgCol : '#ffd5a0', textCol : '#000', consoleArguments : arguments } ) );
      };


      czrapp.errare = function( title, msg ) { _wrapLogInsideTags( title, msg, '#ffd5a0' ); };
      czrapp.infoLog = function( title, msg ) { _wrapLogInsideTags( title, msg, '#5ed1f5' ); };
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
                        if ( '0' === _r ||  '-1' === _r || false === _r.success ) {
                              czrapp.errare( 'czrapp.doAjax : done ajax error for action : ' + _query_.action , _r );
                              dfd.reject( _r );
                        }
                        dfd.resolve( _r );
                  })
                  .fail( function( _r ) {
                        czrapp.errare( 'czrapp.doAjax : failed ajax error for : ' + _query_.action, _r );
                        dfd.reject( _r );
                  });
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
                    var once = _event.once ? _event.once : false;
                    args.dom_el[ once ? 'one' : 'on' ]( _event.trigger , _event.selector, function( e, event_params ) {
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
              var args = _.extend( { silent : false }, _.isObject( o ) ? o : {} );
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
                          .fail( function() { czrapp.errorLog( 'A deferred callback failed in api.Value::set()'); })
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
              var from = this._value;

              to = this._setter.apply( this, arguments );
              to = this.validate( to );
              if ( null === to || _.isEqual( from, to ) ) {
                return this;
              }

              this._value = to;
              this._dirty = ( _.isUndefined( dirtyness ) || ! _.isBoolean( dirtyness ) ) ? this._dirty : dirtyness;

              this.callbacks.fireWith( this, [ to, from, { silent : true } ] );

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

})( jQuery );//@global TCParams
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
                        localized        : "undefined" != typeof(TCParams) && TCParams ? TCParams : { _disabled: [] },
                        is_responsive    : self.isResponsive(),//store the initial responsive state of the window
                        current_device   : self.getDevice()//store the initial device
                  });
            },
            isResponsive : function() {
                  return this.matchMedia(979);
            },
            getDevice : function() {
                  var _devices = {
                        desktop : 979,
                        tablet : 767,
                        smartphone : 480
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
                  var $_window = czrapp.$_window || $(window);
                  return $_window.width() <= ( _maxWidth - 15 );
            },
            emitCustomEvents : function() {

                  var that = this;
                  czrapp.$_window.resize( function() {
                        var //$_windowWidth     = czrapp.$_window.width(),
                            _current          = czrapp.current_device,//<= stored on last resize event or on load
                            _to               = that.getDevice();
                        czrapp.is_responsive  = that.isResponsive();
                        czrapp.current_device = _to;
                        czrapp.$_body.trigger( 'tc-resize', { current : _current, to : _to} );
                  } );//resize();
                  if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize && 'undefined' !== typeof wp.customize.selectiveRefresh ) {
                        wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function( placement ) {
                              czrapp.$_header = $('.tc-header');
                              czrapp.$_body.trigger( 'partialRefresh.czr', placement );
                        });
                  }

            },

            emit : function( cbs, args ) {
                  cbs = _.isArray(cbs) ? cbs : [cbs];
                  var self = this;
                  _.map( cbs, function(cb) {
                        if ( 'function' == typeof(self[cb]) ) {
                              args = 'undefined' == typeof( args ) ? [] : args ;
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
                  return ( _.isFunction( window.matchMedia ) && matchMedia( 'only screen and (max-width: 720px)' ).matches ) || ( this._isCustomizing() && 'desktop' != this.previewDevice() );
            },
            _isCustomizing : function() {
                  return czrapp.$_body.hasClass('is-customizing') || ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize );
            },
            _has_iframe : function ( $_elements ) {
                  var //that = this,
                      to_return = [];
                  _.each( $_elements, function( $_el, container ){
                        if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
                          to_return.push(container);
                  });
                  return to_return;
            },
      };//_methods{}

      czrapp.methods.Base = czrapp.methods.Base || {};
      $.extend( czrapp.methods.Base , _methods );//$.extend

})(jQuery, czrapp);/***************************
* ADD BROWSER DETECT METHODS
****************************/
(function($, czrapp) {
  var _methods =  {
    addBrowserClassToBody : function() {
          if ( !$.browser )
            return;
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
              setTimeout( function(){ self.emit('centerImages'); }, delay || 300 );
            },

            centerInfinity : function() {
                  var centerInfiniteImagesClassicStyle = function( collection, _container ) {
                        var   $_container = $(_container);

                        if ( 'object' !== typeof collection || 1 > $_container.length) {
                              return;
                        }
                        _.each( collection, function( elementSelector ) {
                              var $_img = $(  elementSelector + ' .thumb-wrapper', $_container ).centerImages( {
                                    enableCentering : 1 == czrapp.localized.centerAllImg,
                                    enableGoldenRatio : false,
                                    disableGRUnder : 0,//<= don't disable golden ratio when responsive
                                    oncustom : [ 'simple_load']
                              }).find( 'img' );

                              if ( $_img.length < 1 ) {
                                    $_img = $( elementSelector + ' .tc-rectangular-thumb',  $_container ).centerImages( {
                                          enableCentering : 1 == czrapp.localized.centerAllImg,
                                          enableGoldenRatio : true,
                                          goldenRatioVal : czrapp.localized.goldenRatio || 1.618,
                                          disableGRUnder : 0,//<= don't disable golden ratio when responsive
                                          oncustom : [ 'simple_load']
                                    }).find( 'img' );
                              }
                              if ( $_img.length < 1 ) {
                                    $_img = $( elementSelector + ' .tc-grid-figure', $_container ).centerImages( {
                                          enableCentering : 1 == czrapp.localized.centerAllImg,
                                          oncustom : [ 'simple_load'],
                                          enableGoldenRatio : true,
                                          goldenRatioVal : czrapp.localized.goldenRatio || 1.618,
                                          goldenRatioLimitHeightTo : czrapp.localized.gridGoldenRatioLimit || 350
                                    }).find( 'img' );
                              }
                              czrapp.methods.Base.triggerSimpleLoad( $_img );
                        });
                  };//end centerInfiniteImagesClassicStyle
                  czrapp.$_body.on( 'post-load', function( e, response ) {
                        if ( ( 'undefined' !== typeof response ) && 'success' == response.type && response.collection && response.container ) {
                              centerInfiniteImagesClassicStyle(
                                  response.collection,
                                  '#'+response.container //_container
                              );
                        }
                  } );
            },
            imgSmartLoad : function() {
              var smartLoadEnabled = 1 == TCParams.imgSmartLoadEnabled,
                  _where           = TCParams.imgSmartLoadOpts.parentSelectors.join();
              if (  smartLoadEnabled )
                $( _where ).imgSmartLoad(
                  _.size( TCParams.imgSmartLoadOpts.opts ) > 0 ? TCParams.imgSmartLoadOpts.opts : {}
                );
              if ( 1 == TCParams.centerAllImg ) {
                var self                   = this,
                    $_to_center            = smartLoadEnabled ?
                       $( _.filter( $( _where ).find('img'), function( img ) {
                          return $(img).is(TCParams.imgSmartLoadOpts.opts.excludeImg.join());
                        }) ): //filter
                        $( _where ).find('img');
                    var $_to_center_with_delay = $( _.filter( $_to_center, function( img ) {
                        return $(img).hasClass('tc-holder-img');
                    }) );
                setTimeout( function(){
                  self.triggerSimpleLoad( $_to_center_with_delay );
                }, 300 );
                self.triggerSimpleLoad( $_to_center );
              }
            },
            dropCaps : function() {
              if ( ! TCParams.dropcapEnabled || ! _.isObject( TCParams.dropcapWhere ) )
                return;

              $.each( TCParams.dropcapWhere , function( ind, val ) {
                if ( 1 == val ) {
                  $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
                    minwords : TCParams.dropcapMinWords,//@todo check if number
                    skipSelectors : _.isObject(TCParams.dropcapSkipSelectors) ? TCParams.dropcapSkipSelectors : {}
                  });
                }
              });//each
            },
            extLinks : function() {
              if ( ! TCParams.extLinksStyle && ! TCParams.extLinksTargetExt )
                return;
              var _isValidURL = function( _url ){
                    var _pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                    return _pattern.test( _url );
              };

              $('a' , '.entry-content').each( function() {
                    if ( $(this).attr('href') && _isValidURL( $(this).attr('href') ) ) {
                          $(this).extLinks({
                                addIcon : TCParams.extLinksStyle,
                                newTab : TCParams.extLinksTargetExt,
                                skipSelectors : _.isObject(TCParams.extLinksSkipSelectors) ? TCParams.extLinksSkipSelectors : {}
                          });
                    }
              });
            },
            fancyBox : function() {
              if ( 1 != TCParams.FancyBoxState || 'function' != typeof($.fn.fancybox) )
                return;

              $("a.grouped_elements").fancybox({
                transitionOut: "elastic",
                transitionIn: "elastic",
                speedIn: 200,
                speedOut: 200,
                overlayShow: !1,
                autoScale: 1 == TCParams.FancyBoxAutoscale ? "true" : "false",
                changeFade: "fast",
                enableEscapeButton: !0
              });
              $('a[rel*=tc-fancybox-group]').each( function() {
                var title = $(this).find('img').prop('title');
                var alt = $(this).find('img').prop('alt');
                if (typeof title !== 'undefined' && 0 !== title.length)
                  $(this).attr('title',title);
                else if (typeof alt !== 'undefined' &&  0 !== alt.length)
                  $(this).attr('title',alt);
              });
            },
            centerImages : function() {
              setTimeout( function() {
                $.each( $( '.carousel .carousel-inner') , function() {
                  $( this ).centerImages( {
                    enableCentering : 1 == TCParams.centerSliderImg,
                    imgSel : '.czr-item .carousel-image img',
                    oncustom : ['customizr.slid', 'simple_load', 'smartload'],
                    defaultCSSVal : { width : '100%' , height : 'auto' },
                    useImgAttr : true
                  });
                  var self = this;
                  setTimeout( function() {
                      $( self ).prevAll('.tc-slider-loader-wrapper').fadeOut();
                  }, 500 );
                });
              } , 50);
              $('.widget-front .thumb-wrapper').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                zeroTopAdjust : 1,
                leftAdjust : 2.5,
                oncustom : ['smartload', 'simple_load']
              });
              $('.thumb-wrapper', '.czr-hentry' ).centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'simple_load']
              });
              $('.tc-rectangular-thumb', '.tc-post-list-context' ).centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : true,
                goldenRatioVal : TCParams.goldenRatio || 1.618,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'refresh-height', 'simple_load'] //bind 'refresh-height' event (triggered to the the customizer preview frame)
              });
              $('.tc-rectangular-thumb' , '.tc-singular-thumbnail-wrapper').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                enableGoldenRatio : false,
                disableGRUnder : 0,//<= don't disable golden ratio when responsive
                oncustom : ['smartload', 'refresh-height', 'simple_load'], //bind 'refresh-height' event (triggered to the the customizer preview frame)
                setOpacityWhenCentered : true,//will set the opacity to 1
                opacity : 1
              });
              $('.tc-grid-figure').centerImages( {
                enableCentering : 1 == TCParams.centerAllImg,
                oncustom : ['smartload', 'simple_load'],
                enableGoldenRatio : true,
                goldenRatioVal : TCParams.goldenRatio || 1.618,
                goldenRatioLimitHeightTo : TCParams.gridGoldenRatioLimit || 350
              } );
            },//center_images
            parallax : function() {
              $( '.parallax-item' ).czrParallax(
              {
                parallaxRatio : 0.55
              }
              );
            }
      };//_methods{}

      czrapp.methods.JQPlugins = czrapp.methods.JQPlugins || {};
      $.extend( czrapp.methods.JQPlugins = {} , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods = {
            initOnDomReady : function() {
                  var self = this;
                  this.$_sliders = $( 'div[id*="customizr-slider"]' );
                  czrapp.$_window.resize( function(){
                    self.centerSliderArrows();
                  });
            },



            fireSliders : function(name, delay, hover) {
              var self = this,
                  _name   = name || TCParams.SliderName,
                  _delay  = delay || TCParams.SliderDelay,
                  _hover  = hover || TCParams.SliderHover,
                  _cellSelector = '.czr-item',
                  _cssLoaderClass = 'tc-css-loader',
                  _css_loader = '<div class="' + _cssLoaderClass + ' tc-mr-loader" style="display:none"><div></div><div></div><div></div></div>';

              if ( 0 === _name.length || 1 > self.$_sliders.length )
                return;
              if ( czrapp.localized.imgSmartLoadsForSliders ) {
                    self.$_sliders.addClass('disable-transitions-for-smartload');
                    self.$_sliders.find( _cellSelector + '.active').imgSmartLoad().data( 'czr_smartLoaded', true );

                    var _maybeRemoveLoader = function( $_cell ) {
                          $_cell.find('.czr-css-loader').fadeOut( {
                                duration: 'fast',
                                done : function() { $(this).remove();}
                          } );
                    };


                    var _smartLoadCellImg = function( _event_ ) {
                          _event_ = _event_ || 'czr-smartloaded';
                          var $_cell = this;
                          if ( 1 > $_cell.find('img[data-src], img[data-smartload]').length )
                            return;
                          if ( ! $_cell.data( 'czr_smartLoaded' ) ) {
                                if ( 1 > $_cell.find('.czr-css-loader').length ) {
                                      $_cell.append( _css_loader ).find('.czr-css-loader').fadeIn( 'slow' );
                                }
                                $_cell.imgSmartLoad().data( 'czr_smartLoaded', true ).addClass( _event_ );
                                $_cell.data( 'czr_loader_timer' , $.Deferred( function() {
                                      var self = this;
                                      _.delay( function() {
                                            self.resolve();
                                      }, 2000 );
                                      return this.promise();
                                }) );
                                $_cell.data( 'czr_loader_timer' ).done( function() {
                                      _maybeRemoveLoader( $_cell );
                                });
                          }
                    };
                    self.$_sliders.data( 'czr_smartload_scheduled', $.Deferred().done( function() {
                          self.$_sliders.addClass('czr-smartload-scheduled');
                    }) );
                    var _isSliderDataSetup = function() {
                          return 1 <= self.$_sliders.length && ! _.isUndefined( self.$_sliders.data( 'czr_smartload_scheduled' ) );
                    };
                    self.$_sliders.data( 'czr_schedule_select',
                          $.Deferred( function() {
                                var dfd = this;
                                self.$_sliders.parent().one( 'customizr.slide click' , function() {
                                      dfd.resolve();
                                } );
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-smartloaded-on-select' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );//data( 'czr_schedule_select' )
                    self.$_sliders.data( 'czr_schedule_scroll_resize',
                          $.Deferred( function() {
                                var dfd = this;
                                czrapp.$_window.one( 'scroll resize', function() {
                                      _.delay( function() { dfd.resolve(); }, 5000 );
                                });
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-smartloaded-on-scroll' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );//data( 'czr_schedule_scroll_resize' )
                    self.$_sliders.data( 'czr_schedule_autoload',
                          $.Deferred( function() {
                                var dfd = this;
                                _.delay( function() { dfd.resolve(); }, 10000 );
                          }).done( function() {
                                if ( ! _isSliderDataSetup() || 'resolved' == self.$_sliders.data( 'czr_smartload_scheduled' ).state() )
                                    return;

                                self.$_sliders.find( _cellSelector ).each( function() {
                                      _smartLoadCellImg.call( $(this), 'czr-auto-smartloaded' );
                                });
                                self.$_sliders.data( 'czr_smartload_scheduled').resolve();
                          })
                    );
                    self.$_sliders.on( 'smartload', _cellSelector , function() {
                          _maybeRemoveLoader( $(this) );
                    });
              }//if czrapp.localized.imgSmartLoadsForSliders

              if ( 0 !== _delay.length && ! _hover ) {
                this.$_sliders.czrCarousel({
                    interval: _delay,
                    pause: "false"
                });
              } else if ( 0 !== _delay.length ) {
                this.$_sliders.czrCarousel({
                    interval: _delay
                });
              } else {
                this.$_sliders.czrCarousel();
              }
            },

            parallaxSliders : function() {
              if ( 'function' == typeof $.fn.czrParallax ) {
                $( '.czr-parallax-slider' ).czrParallax();
              }
            },

            manageHoverClass : function() {
              this.$_sliders.hover( function() {
                  $(this).addClass('tc-slid-hover');
                },
                function() {
                  $(this).removeClass('tc-slid-hover');
                }
              );
            },
            centerSliderArrows : function() {
              if ( 0 === this.$_sliders.length )
                  return;
              this.$_sliders.each( function() {
                  var _slider_height = $( '.carousel-inner' , $(this) ).height();
                  $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
              });
            },
            addSwipeSupport : function() {
              if ( 'function' != typeof($.fn.hammer) || 0 === this.$_sliders.length )
                return;
              this.$_sliders.on('touchstart touchmove', 'input, button, textarea, select, a:not(".tc-slide-link")', function(ev) {
                  ev.stopPropagation();
              });

              var _is_rtl = czrapp.$_body.hasClass('rtl');
              this.$_sliders.each( function() {
                  $(this).hammer().on('swipeleft', function() {
                      $(this).czrCarousel( ! _is_rtl ? 'next' : 'prev' );
                  });
                  $(this).hammer().on('swiperight', function(){
                      $(this).czrCarousel( ! _is_rtl ? 'prev' : 'next' );
                  });
              });
            },
            sliderTriggerSimpleLoad : function() {
              this.triggerSimpleLoad( this.$_sliders.find('.carousel-inner img') );
            }
      };//methods {}

      czrapp.methods.Slider = {};
      $.extend( czrapp.methods.Slider , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};

(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
                this.timer = 0;
                this.increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
                $('.menu-item').on('focusin', 'a', function( evt ) {
                      $(this).closest( '.menu-item' ).addClass('czr-focusin');
                });

            },//init
            eventListener : function() {
                  var self = this;

                  czrapp.$_window.scroll( _.throttle( function() {
                        self.eventHandler( 'scroll' );
                  }, 50 ) );
            },//eventListener
            eventHandler : function ( evt ) {
              var self = this;

              switch ( evt ) {
                case 'scroll' :
                  if ( 0 === $('.tc-btt-wrapper').length )
                    return;
                  if ( this.timer) {
                    this.increment++;
                    clearTimeout(self.timer);
                  }
                  if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
                    this.timer = setTimeout( function() {
                      self.bttArrowVisibility();
                    }, self.increment > 5 ? 50 : 0 );
                  } else if ( czrapp.$_body.hasClass('ie') ) {
                    this.timer = setTimeout( function() {
                      self.bttArrowVisibility();
                    }, self.increment > 5 ? 50 : 0 );
                  }
                break;
              }
            },//eventHandler
            outline: function() {
              if ( czrapp.$_body.hasClass( 'mozilla' ) && 'function' == typeof( tcOutline ) )
                  tcOutline();
            },
            smoothScroll: function() {
              if ( TCParams.SmoothScroll && TCParams.SmoothScroll.Enabled )
                smoothScroll( TCParams.SmoothScroll.Options );
            },
            anchorSmoothScroll : function() {
              if ( ! TCParams.anchorSmoothScroll || 'easeOutExpo' != TCParams.anchorSmoothScroll )
                    return;

              var _excl_sels = ( TCParams.anchorSmoothScrollExclude && _.isArray( TCParams.anchorSmoothScrollExclude.simple ) ) ? TCParams.anchorSmoothScrollExclude.simple.join(',') : '',
                  self = this,
                  $_links = $('#tc-page-wrap a[href^="#"],#tc-sn a[href^="#"]').not(_excl_sels);
              var _links, _deep_excl = _.isObject( TCParams.anchorSmoothScrollExclude.deep ) ? TCParams.anchorSmoothScrollExclude.deep : null ;
              if ( _deep_excl )
                _links = _.toArray($_links).filter( function ( _el ) {
                  return ( 2 == ( ['ids', 'classes'].filter(
                                function( sel_type) {
                                    return self.isSelectorAllowed( $(_el), _deep_excl, sel_type);
                                } ) ).length
                        );
                });
              $(_links).click( function () {
                var anchor_id = $(this).attr("href");
                if ( ! $(anchor_id).length )
                  return;

                if ('#' != anchor_id) {
                    $('html, body').animate({
                        scrollTop: $(anchor_id).offset().top
                    }, 700, TCParams.anchorSmoothScroll);
                }
                return false;
              });//click
            },
            bttArrowVisibility : function () {
              if ( czrapp.$_window.scrollTop() > 100 )
                $('.tc-btt-wrapper').addClass('show');
              else
                $('.tc-btt-wrapper').removeClass('show');
            },//bttArrowVisibility
            backToTop : function() {
              var $_html = $("html, body"),
                  _backToTop = function( evt ) {
                    return ( evt.which > 0 || "mousedown" === evt.type || "mousewheel" === evt.type) && $_html.stop().off( "scroll mousedown DOMMouseScroll mousewheel keyup", _backToTop );
                  };

              $(".back-to-top, .tc-btt-wrapper, .btt-arrow").on("click touchstart touchend", function ( evt ) {
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
            widgetsHoverActions : function() {
              czrapp.$_body.on( 'mouseenter mouseleave', '.widget-front, article', _toggleThisHoverClass );

              czrapp.$_body.on( 'mouseenter mouseleave', '.widget li', _toggleThisOnClass );

              function _toggleThisHoverClass( evt ) {
                    _toggleElementClassOnHover( $(this), 'hover', evt );
              }

              function _toggleThisOnClass( evt ) {
                    _toggleElementClassOnHover( $(this), 'on', evt );
              }

              function _toggleElementClassOnHover( $_el, _class, _evt ) {
                    if ( 'mouseenter' == _evt.type )
                       $_el.addClass( _class );
                    else if ( 'mouseleave' == _evt.type )
                       $_el.removeClass( _class );
              }

            },
            attachmentsFadeEffect : function() {
              $("article.attachment img").delay(500).animate({
                    opacity: 1
                }, 700, function () {}
              );
            },
            clickableCommentButton : function() {
              if ( ! TCParams.HasComments )
                return;
              $('cite p.edit-link').each(function() {
                $(this).removeClass('btn btn-success btn-mini');
              });
              $('cite p.edit-link > a').each(function() {
                $(this).addClass('btn btn-success btn-mini');
              });
              $('.comment .reply').each(function() {
                $(this).removeClass('btn btn-small');
              });
              $('.comment .reply .comment-reply-link').each(function() {
                $(this).addClass('btn btn-small');
              });
            },
            dynSidebarReorder : function() {
              if ( 1 != TCParams.ReorderBlocks )
                return;
              if ( 'desktop' != this.getDevice() )
                this._reorderSidebars( 'responsive' );
              var self = this;
              czrapp.$_body.on( 'tc-resize' , function(e, param) {
                param = _.isObject(param) ? param : {};
                var _to = 'desktop' != param.to ? 'responsive' : 'normal',
                    _current = 'desktop' != param.current ? 'responsive' : 'normal';

                if ( _current != _to )
                  self._reorderSidebars( _to );
              } );
            },
            _reorderSidebars : function( _sidebarLayout ) {
              _sidebarLayout = _sidebarLayout || 'normal';
              var that = this,
                  LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
                  RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar';
              that.$_content      = that.$_content || $("#main-wrapper .container .article-container");
              that.$_left         = that.$_left || $("#main-wrapper .container " + LeftSidebarClass);
              that.$_right        = that.$_right || $("#main-wrapper .container " + RightSidebarClass);
              var iframeContainers = that._has_iframe( { 'content' : this.$_content, 'left' : this.$_left } ) ;

              var leftIframe    = $.inArray('left', iframeContainers) > -1,
                  contentIframe = $.inArray('content', iframeContainers) > -1;
              if ( leftIframe && contentIframe )
                return;

              if ( that.$_left.length ) {
                if ( leftIframe )
                  that.$_content[ _sidebarLayout === 'normal' ?  'insertAfter' : 'insertBefore']( that.$_left );
                else
                  that.$_left[ _sidebarLayout === 'normal' ?  'insertBefore' : 'insertAfter']( that.$_content );
              }
            },
            dropdownMenuEventsHandler : function() {
              var $dropdown_ahrefs    = $('.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'),
                  $dropdown_submenus  = $('.tc-open-on-click .dropdown .dropdown-submenu');
              $dropdown_ahrefs.on('click', function() {
                if ( ( $(this).next('.dropdown-menu').css('visibility') != 'hidden' &&
                        $(this).next('.dropdown-menu').is(':visible')  &&
                        ! $(this).parent().hasClass('dropdown-submenu') ) ||
                     ( $(this).next('.dropdown-menu').is(':visible') &&
                        $(this).parent().hasClass('dropdown-submenu') ) )
                    window.location = $(this).attr('href');
              });//.on()
              $dropdown_submenus.each(function(){
                var $parent = $(this),
                    $children = $parent.children('[data-toggle="dropdown"]');
                $children.on('click', function(){
                    var //submenu   = $(this).next('.dropdown-menu'),
                        openthis  = false;
                    if ( ! $parent.hasClass('open') ) {
                      openthis = true;
                    }
                    $($parent.parent()).children('.dropdown-submenu').each(function(){
                        $(this).removeClass('open');
                    });
                    if ( openthis )
                        $parent.addClass('open');

                    return false;
                });//.on()
              });//.each()
            },
            menuButtonHover : function() {
              var $_menu_btns = $('.btn-toggle-nav');
              $_menu_btns.hover(
                function() {
                  $(this).addClass('hover');
                },
                function() {
                  $(this).removeClass('hover');
                }
              );
            },
            secondMenuRespActions : function() {
              if ( ! TCParams.isSecondMenuEnabled )
                return;
              var userOption = TCParams.secondMenuRespSet || false,
                  that = this;
              if ( ! userOption || -1 == userOption.indexOf('in-sn') )
                return;
              var _cacheElements = function() {
                    that.$_sec_menu_els  = $('.nav > li', '.tc-header .nav-collapse');
                    that.$_sn_wrap       = $('.sn-nav', '.sn-nav-wrapper');
                    that.$_sec_menu_wrap = $('.nav', '.tc-header .nav-collapse');
                  },
                  _maybeClean = function() {
                    var $_sep = $( '.secondary-menu-separator' );

                    if ( $_sep.length ) {

                      switch(userOption) {
                          case 'in-sn-before' :
                            $_sep.prevAll('.menu-item').remove();
                          break;
                          case 'in-sn-after' :
                            $_sep.nextAll('.menu-item').remove();
                          break;
                      }
                      $_sep.remove();
                    }
                  };
              _cacheElements();
              var _locationOnDomReady = 'desktop' == this.getDevice() ? 'navbar' : 'side_nav';

              if ( 'desktop' != this.getDevice() )
                this._manageMenuSeparator( _locationOnDomReady , userOption)._moveSecondMenu( _locationOnDomReady , userOption );
              czrapp.$_body.on( 'tc-resize partialRefresh.czr', function( ev, param ) {
                    var _force = false;

                    if ( 'partialRefresh' == ev.type && 'czr' === ev.namespace && param.container && param.container.hasClass('tc-header')  ) {
                          _maybeClean();
                          _cacheElements();
                          param   = { to: czrapp.current_device, current: czrapp.current_device };
                          _force  = true;
                    }

                    param = _.isObject(param) ? param : {};
                    var _to = 'desktop' != param.to ? 'side_nav' : 'navbar',
                        _current = 'desktop' != param.current ? 'side_nav' : 'navbar';

                    if ( _current == _to && !_force )
                      return;

                    that._manageMenuSeparator( _to, userOption)._moveSecondMenu( _to, userOption );
              } );//.on()

            },

            _manageMenuSeparator : function( _to, userOption ) {
              var that = this;
              if ( 'navbar' == _to ) {
                $( '.secondary-menu-separator', that.$_sn_wrap).remove();
              }
              else {
                var $_sep = $( '<li class="menu-item secondary-menu-separator"><hr class="featurette-divider"></hr></li>' );

                switch(userOption) {
                  case 'in-sn-before' :
                    this.$_sn_wrap.prepend($_sep);
                  break;

                  case 'in-sn-after' :
                    this.$_sn_wrap.append($_sep);
                  break;
                }
              }
              return this;
            },
            _moveSecondMenu : function( _where, userOption ) {
              _where = _where || 'side_nav';
              var that = this;
              switch( _where ) {
                  case 'navbar' :
                    that.$_sec_menu_wrap.append(that.$_sec_menu_els);
                  break;

                  case 'side_nav' :
                    if ( 'in-sn-before' == userOption )
                      that.$_sn_wrap.prepend(that.$_sec_menu_els);
                    else
                      that.$_sn_wrap.append(that.$_sec_menu_els);
                  break;
                }
            },
            _has_iframe : function ( $_elements ) {
              var to_return = [];
              _.map( $_elements, function( $_el, container ){
                if ( $_el.length > 0 && $_el.find('IFRAME').length > 0 )
                  to_return.push(container);
              });
              return to_return;
            }

      };//_methods{}


      czrapp.methods.UserXP = {};
      $.extend( czrapp.methods.UserXP , _methods );
})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
  var _methods =  {
        mayBePrintFrontNote : function() {
              if ( czrapp.localized && _.isUndefined( czrapp.localized.frontNotifications ) )
                return;
              if ( _.isEmpty( czrapp.localized.frontNotifications ) || ! _.isObject( czrapp.localized.frontNotifications ) )
                return;

              var self = this;
              czrapp.frontNotificationVisible = new czrapp.Value( false );
              czrapp.frontNotificationRendered = false;
              _.each( czrapp.localized.frontNotifications, function( _notification ) {
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
                                        '<span class="fas fa-times close-note" title="' + czrapp.localized.i18n['Permanently dismiss'] + '"></span>',
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
      var _methods =  {
          initOnDomReady : function() {
                var self = this;
                this.stickyHeaderCacheElements();
                this.elToHide         = []; //[ '.social-block' , '.site-description' ],
                this.customOffset     = TCParams.stickyCustomOffset || {};// defaults : { _initial : 0, _scrolling : 0 }

                this.triggerHeight    = 20; //0.5 * windowHeight;

                this.scrollingDelay   = 1 != TCParams.timerOnScrollAllBrowsers && czrapp.$_body.hasClass('ie') ? 50 : 5;
                this.isHeaderSticky   = new czrapp.Value( false );
                this.isHeaderSticky.bind( function( isSticky ) { self._isHeaderStickyReact( isSticky ); } );
                this.stickyHeaderEventListener();
                this.triggerStickyHeaderLoad();
          },//init()
          stickyHeaderCacheElements : function() {
                this.$_resetMarginTop = $('#tc-reset-margin-top');
                this.$_sticky_logo    = $('img.sticky', '.site-logo');
                this.logo             = 0 === this.$_sticky_logo.length ? { _logo: $('img:not(".sticky")', '.site-logo') , _ratio: '' }: false;
          },
          stickyHeaderEventListener : function() {
                var self = this;
                czrapp.$_body.on( 'sticky-enabled-on-load' , function() {
                      self.stickyHeaderEventHandler( 'on-load' );
                });//.on()
                czrapp.$_window.on( 'tc-resize', function() {
                      self.stickyHeaderEventHandler( 'resize' );
                });
                czrapp.$_body.on( 'partialRefresh.czr', function( e, placement ) {
                      if ( placement.container && placement.container.hasClass( 'tc-header' )  ) {
                            self.stickyHeaderCacheElements();
                            self.stickyHeaderEventHandler( 'resize' );
                      }
                });
                czrapp.$_window.scroll( _.throttle( function() {
                      self.stickyHeaderEventHandler( 'scroll' );
                }, ! ( czrapp.$_body.hasClass('tc-smoothscroll') && ! self.isHeaderSticky() ) ? self.scrollingDelay : 15 ) );
                czrapp.$_body.on( czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click' , '.sn-toggle', function() {
                      self.stickyHeaderEventHandler( 'sidenav-toggle' );
                });
          },
          triggerStickyHeaderLoad : function() {
                if ( ! this._is_sticky_enabled() )
                  return;
                czrapp.$_body.trigger( 'sticky-enabled-on-load' , { on : 'load' } );
          },
          stickyHeaderEventHandler : function( evt ) {
                if ( ! this._is_sticky_enabled() )
                  return;

                var self = this;

                switch ( evt ) {
                      case 'on-load' :
                            self._prepare_logo_transition();
                            setTimeout( function() {
                                  self._sticky_refresh();
                                  self._set_header_top_offset();
                                  self.isHeaderSticky( czrapp.$_window.scrollTop() > self.triggerHeight );
                            } , 20 );//setTimeout()
                      break;

                      case 'scroll' :
                            self._set_header_top_offset();
                            self.isHeaderSticky( czrapp.$_window.scrollTop() > self.triggerHeight );
                      break;

                      case 'resize' :
                      case 'sidenav-toggle' :
                            self._set_sticky_offsets();
                            self._set_header_top_offset();
                            self._set_logo_height();
                      break;
                }
          },
          _isHeaderStickyReact : function( isSticky ) {
                var self = this;
                if ( isSticky ) {
                      czrapp.$_body
                            .addClass("sticky-enabled")
                            .removeClass("sticky-disabled")
                            .trigger('tc-sticky-enabled');
                      if ( ! czrapp.$_header.hasClass('tc-shrink-on') ) {
                            self._set_logo_height();
                      }
                } else {
                      czrapp.$_body
                            .removeClass("sticky-enabled")
                            .addClass("sticky-disabled")
                            .trigger('tc-sticky-disabled');
                      setTimeout( function() { self._sticky_refresh(); } ,
                            self._isCustomizing ? 100 : 20
                      );
                      setTimeout( function() { self._sticky_refresh(); } , 200 );
                }
          },
          _is_sticky_enabled : function() {
                return czrapp.$_body.hasClass('tc-sticky-header');
          },
          _get_top_offset : function() {
                var initialOffset   = 0,
                    that            = this,
                    customOffset    = +this._get_custom_offset( that.isHeaderSticky() ? '_scrolling' : '_initial' );

                if ( 1 == this.isUserLogged() && ! this._isCustomizing() ) {
                      if ( 580 < czrapp.$_window.width() )
                        initialOffset = czrapp.$_wpadminbar.height();
                      else
                        initialOffset = ! this.isHeaderSticky() ? czrapp.$_wpadminbar.height() : 0;
                }
                return initialOffset + customOffset ;
          },
          _get_custom_offset : function( _context ) {
                if ( _.isEmpty( this.customOffset ) )
                  return 0;
                if ( ! this.customOffset[_context] )
                  return 0;
                if ( ! this.customOffset.options )
                  return this.customOffset[_context];
                if ( '_scrolling' == _context )
                  return +this.customOffset[_context] || 0;
                if ( this.customOffset.options._static )
                  return +this.customOffset[_context] || 0;

                var that = this,
                    $_el = $(that.customOffset.options._element);
                if ( ! $_el.length )
                  return 0;
                else {
                  return $_el.outerHeight() || +this.customOffset[_context] || 0;
                }
          },
          _set_sticky_offsets : function() {
                czrapp.$_header.css('top' , '');
                czrapp.$_header.css('height' , 'auto' );
                this.$_resetMarginTop.css('margin-top' , '' ).show();
                var headerHeight    = czrapp.$_header.outerHeight(true); /* include borders and eventual margins (true param)*/
                this.$_resetMarginTop.css('margin-top' , + headerHeight  + 'px');
          },
          _set_header_top_offset : function() {
                var self = this;
                czrapp.$_header.css('top' , self._get_top_offset() );
          },
          _prepare_logo_transition : function(){
                if ( ! ( czrapp.$_html.hasClass('csstransitions') && ( this.logo && 0 !== this.logo._logo.length ) ) )
                  return;

                var logoW = this.logo._logo.originalWidth(),
                    logoH = this.logo._logo.originalHeight();
                if ( 2 != _.size( _.filter( [ logoW, logoH ], function(num){ return _.isNumber( parseInt(num, 10) ) && 0 !== num; } ) ) )
                  return;

                this.logo._ratio = logoW / logoH;
                this.logo._logo.css('width' , logoW );
          },
          _set_logo_height : function(){
                if ( this.logo && 0 === this.logo._logo.length || ! this.logo._ratio )
                  return;
                var self = this;
                this.logo._logo.css('height' , self.logo._logo.width() / self.logo._ratio );

                setTimeout( function() {
                      self._set_sticky_offsets();
                      self._set_header_top_offset();
                } , 200 );
          },

          _sticky_refresh : function() {
                var self = this;
                setTimeout( function() {
                      self._set_sticky_offsets();
                      self._set_header_top_offset();
                } , 20 );
                czrapp.$_window.trigger('resize');
          }
    };//_methods{}

    czrapp.methods.StickyHeader = {};
    $.extend( czrapp.methods.StickyHeader , _methods );

})(jQuery, czrapp);var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
              this.$_push   = $('#tc-push-footer');
              this._class   = 'sticky-footer-enabled';
              this.$_page   = $('#tc-page-wrap');

              if ( 1 != TCParams.stickyHeader ) {//sticky header fires a resize
                var self = this;
                setTimeout( function() {
                        self._apply_sticky_footer(); }, 50
                );
              }
            },
            stickyFooterEventListener : function() {
              var self = this;
              czrapp.$_window.on( 'tc-resize', function() {
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
                  var func = function() { return self._apply_sticky_footer() ;};
                  for ( var i = 0; i<5; i++ ) /* I've seen something like that in twentyfifteen */
                    setTimeout( func, 50 * i);
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
              }else if ( this.$_push.hasClass(this._class) ) {
                this.$_push.removeClass(this._class);
                _event = 'sticky-footer-off';
              }
              if ( _event )
                czrapp.$_body.trigger(_event);
            },
            _is_sticky_footer_enabled : function() {
              return czrapp.$_body.hasClass('tc-sticky-footer');
            },
            _get_full_height : function() {
              if ( this.$_page.length < 1 )
                return $(window).outerHeight(true);
              var _full_height = this.$_page.outerHeight(true) + this.$_page.offset().top,
                  _push_height = 'block' == this.$_push.css('display') ? this.$_push.outerHeight() : 0;

              return _full_height - _push_height;
            }
      };//_methods{}

      czrapp.methods.StickyFooter = {};
      $.extend( czrapp.methods.StickyFooter , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods =  {
            initOnDomReady : function() {
              this.$_sidenav                = $( '#tc-sn' );

              if ( ! this._is_sn_on() )
                return;
              this.$_page_wrapper           = $('#tc-page-wrap');
              this.$_page_wrapper_node      = this.$_page_wrapper.get(0);
              this.$_page_wrapper_btn       = $('.btn-toggle-nav', '#tc-page-wrap');

              this.$_sidenav_inner          = $( '.tc-sn-inner', this.$_sidenav);

              this._toggle_event            = 'click';// before c4, was czrapp.$_body.hasClass('tc-is-mobile') ? 'touchstart' : 'click';

              this._browser_can_translate3d = ! czrapp.$_html.hasClass('no-csstransforms3d');
              this.transitionEnd            = 'transitionend webkitTransitionEnd otransitionend oTransitionEnd MSTransitionEnd';
              this.sideNavEventListener();

              this._set_offset_height();

            },//init()
            sideNavEventListener : function() {
              var self = this;
              czrapp.$_body.on( this._toggle_event, '.sn-toggle', function( evt ) {
                self.sideNavEventHandler( evt, 'toggle' );
              });
              this.$_page_wrapper.on( this.transitionEnd, function( evt ) {
                self.sideNavEventHandler( evt, 'transitionend' );
              });
              czrapp.$_window.on('tc-resize', function( evt ) {
                self.sideNavEventHandler( evt, 'resize');
              });

              czrapp.$_window.scroll( function( evt ) {
                self.sideNavEventHandler( evt, 'scroll');
              });
            },


            sideNavEventHandler : function( evt, evt_name ) {
              var self = this;

              switch ( evt_name ) {
                case 'toggle':
                  if ( ! this._is_translating() )
                    this._toggle_callback( evt );
                break;

                case 'transitionend' :
                   if ( this._is_translating() && evt.target == this.$_page_wrapper_node )
                     this._transition_end_callback();
                break;

                case 'scroll' :
                case 'resize' :
                  setTimeout( function(){
                      self._set_offset_height();
                  }, 200);
                break;
              }
            },


            _toggle_callback : function ( evt ){
              evt.preventDefault();

              if ( czrapp.$_body.hasClass( 'tc-sn-visible' ) )
                this._anim_type = 'sn-close';
              else
                this._anim_type = 'sn-open';
              if ( this._browser_can_translate3d ){
                czrapp.$_body.addClass( 'animating ' + this._anim_type )
                             .trigger( this._anim_type + '_start' );
                if ( this._is_sticky_header() ){
                  if ( czrapp.$_body.hasClass('sticky-disabled') )
                    czrapp.$_body.removeClass('tc-sticky-header');
                }
              } else {
                czrapp.$_body.toggleClass('tc-sn-visible')
                             .trigger( this._anim_type );
              }
              var _event = evt || event,
                  $_clicked_btn = $( _event.target ),
                  _is_opening   = $('#tc-page-wrap').has( $_clicked_btn).length > 0;

              this.$_page_wrapper_btn.each( function(){
                $(this).fadeTo( 500 , _is_opening ? 0 : 1 , function() {
                  $(this).css( "visibility", _is_opening ? "hidden" : "visible");
                }); //.fadeTo() duration, opacity, callback
              } );
              return false;
           },

           _transition_end_callback : function() {
             czrapp.$_body.removeClass( 'animating ' +  this._anim_type)
                          .toggleClass( 'tc-sn-visible' )
                          .trigger( this._anim_type + '_end' )
                          .trigger( this._anim_type );
             if ( this._is_sticky_header() ){
               if ( czrapp.$_body.hasClass('sticky-disabled') )
                 czrapp.$_body.addClass('tc-sticky-header');
              }
            },
            _is_sn_on : function() {
              return this.$_sidenav.length > 0;
            },
            _get_initial_offset : function() {
              var _initial_offset = czrapp.$_wpadminbar.length > 0 ? czrapp.$_wpadminbar.height() : 0;
              _initial_offset = _initial_offset && czrapp.$_window.scrollTop() && 'absolute' == czrapp.$_wpadminbar.css('position') ? 0 : _initial_offset;

              return _initial_offset; /* add a custom offset ?*/
            },
            _set_offset_height : function() {
              var _offset = this._get_initial_offset();

              this.$_sidenav.css('top', _offset );
              this.$_sidenav_inner.css('max-height', this.$_sidenav.outerHeight() - _offset);
            },
            _is_translating : function() {
              return czrapp.$_body.hasClass('animating');
            },
            _is_sticky_header : function() {
              this.__is_sticky_header = this.__is_sticky_header || czrapp.$_body.hasClass('tc-sticky-header');
              return this.__is_sticky_header;
            }

      };//_methods{}

      czrapp.methods.SideNav = {};
      $.extend( czrapp.methods.SideNav , _methods );

})(jQuery, czrapp);
var czrapp = czrapp || {};
(function($, czrapp) {
      var _methods =  {
            fireDropDown : function() {
              this.$_sidenav                = $( '#tc-sn' );
              this._dd_first_selector       = '.menu-item-has-children.dropdown > .dropdown-menu' ;
              this.$_nav_collapse           = czrapp.$_header.length > 0 ? czrapp.$_header.find( '.navbar-wrapper .nav-collapse' ) : [];
              this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];

              if ( ! this._has_dd_to_move() )
                return;
              this.$_navbar_wrapper         = this.$_nav_collapse.closest( '.navbar-wrapper' );
              this.$_nav                    = this.$_nav_collapse.find( '.nav' );
              this.$_head                   = $( 'head' );
              this._dyn_style_id            = 'tc-dropdown-dyn-style';
              this._prop                    = czrapp.$_body.hasClass('rtl') ? 'right' : 'left';
              this.dropdownPlaceEventListener();
              this._place_dropdowns();
            },//init()


            dropdownPlaceCacheElements : function() {
              this.$_nav_collapse           = czrapp.$_header.length > 0 ? czrapp.$_header.find( '.navbar-wrapper .nav-collapse' ) : [];
              this.$_nav                    = this.$_nav_collapse.length ? this.$_nav_collapse.find( '.nav' ) : [];
              this.$_navbar_wrapper         = this.$_nav_collapse.length ? this.$_nav_collapse.closest( '.navbar-wrapper' ) : [];
            },
            dropdownPlaceEventListener : function() {
                  var self    = this,
                      _events = 'tc-resize sn-open sn-close tc-sticky-enabled tc-place-dropdowns partialRefresh.czr';
                  czrapp.$_body.on( _events, function( evt, data ) {
                        if ( 'partialRefresh' === evt.type && 'czr' === evt.namespace && data.container && data.container.hasClass( 'tc-header' )  ) {
                              self.dropdownPlaceCacheElements();
                        }
                        self.dropdownPlaceEventHandler( evt, 'resize' );
                  });
            },


            dropdownPlaceEventHandler : function( evt, evt_name ) {
              var self = this;

              switch ( evt_name ) {
                case 'resize' :
                  setTimeout( function(){
                    self._place_dropdowns();
                  }, 250);
                break;
              }
            },


            _place_dropdowns : function () {
              var _dd = this._get_dd_to_move();
              if ( ! _dd.length )
                return;

              this._staging();
              this._move_dropdown( _dd );
              this._write_dyn_style();
              this._unstaging();
            },
            _has_dd_to_move : function() {
              if ( this.$_nav_collapse.length < 1 )
                return false;
              if ( this.$_nav.length && this.$_nav.find( this._dd_first_selector ) < 1 )
                return false;

              return true;
            },
            _get_dd_to_move : function() {
              if ( 'absolute' == this.$_nav_collapse.css('position') )
                return {};
              if ( ! this.$_nav.is(':visible') )
                return {};
              return this.$_nav.find( this._dd_first_selector );
            },
            _staging : function() {
              this._window_width = czrapp.$_window.width();
              if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade') )
                this.$_navbar_wrapper.removeClass('tc-submenu-fade').addClass('tc-submenu-fade-susp');
              var _max_width            = this._window_width - 40,
                  _dyn_style_css_prefix = '.tc-header .nav-collapse .dropdown-menu';
              this._dyn_style  = _dyn_style_css_prefix + ' {max-width: ' + _max_width + 'px;}';
              this._dyn_style += _dyn_style_css_prefix + ' > li > a { word-wrap: break-word; white-space: pre; }';
              this._write_dyn_style();
            },
            _unstaging : function() {
              if ( this.$_navbar_wrapper.hasClass('tc-submenu-fade-susp') )
                this.$_navbar_wrapper.removeClass('tc-submenu-fade-susp').addClass('tc-submenu-fade');
            },
            _write_dyn_style : function() {
              var $_dyn_style_el = this.$_head.find('#' + this._dyn_style_id);
              if ( $_dyn_style_el.length > 0 )
                $_dyn_style_el.remove();
              if ( this._dyn_style )
                $("<style type='text/css' id='" + this._dyn_style_id +"'>" + this._dyn_style + "</style>")
                  .appendTo( this.$_head );
            },
            _move_dropdown : function( $dropdown_menu ) {
              if ( $dropdown_menu && $dropdown_menu.length ) {
                if ( $dropdown_menu.length > 1 ) {
                  var self = this;
                  $.each( $dropdown_menu, function(){
                    self._move_dropdown( $(this) );
                  });
                  return;
                }//end array of dropdown case
              }else //no dropdown
                return;
              var _is_dropdown_visible = $dropdown_menu.is(':visible');
              if ( ! _is_dropdown_visible )
                $dropdown_menu.css('display', 'block').css('visibility', 'hidden');
              this._set_dropdown_offset( $dropdown_menu, '' );
              var _overflow     = this._get_dropdown_overflow( $dropdown_menu );

              if ( _overflow )
                this._set_dropdown_offset( $dropdown_menu, _overflow );
              var $_children_dropdowns = $dropdown_menu.children('li.dropdown-submenu');
                if ( $_children_dropdowns.length )
                  this._move_dropdown( $_children_dropdowns.children('ul.dropdown-menu') );
              if ( ! _is_dropdown_visible )
                $dropdown_menu.css('display', '').css('visibility', '');
            },
            _set_dropdown_offset : function( $dropdown_menu, _dropdown_overflow ) {
              var _offset = '';

              if ( _dropdown_overflow ) {
                var $_parent_dropdown  = $dropdown_menu.parent('.menu-item-has-children'),
                    _is_dropdown_submenu = $_parent_dropdown.hasClass('dropdown-submenu');
                if ( _is_dropdown_submenu ) {
                  _offset = parseFloat( $dropdown_menu.css( this._prop ) ) - _dropdown_overflow - 5;
                  if ( $_parent_dropdown.next('.menu-item').length ) {
                    var _submenu_overflows_parent = this._get_element_overflow( $dropdown_menu, _offset, $_parent_dropdown );
                    if ( _offset < 30  || _submenu_overflows_parent < 30 )
                      _offset = _offset - _submenu_overflows_parent - 30;
                  }
                } else {
                  _offset = -20 - _dropdown_overflow; //add some space (20px) on the right(rtl-> left)
                  var _menu_id = $_parent_dropdown.attr('class').match(/(menu|page)-item-\d+/);
                  _menu_id = _menu_id ? _menu_id[0] : null;
                  if ( _menu_id )
                    this._set_dropdown_arrow_style( _menu_id, _offset );
                }
              }
              $dropdown_menu.css( this._prop, _offset );
            },
            _get_dropdown_overflow : function ( $dropdown_menu ) {
              var overflow = null,
                  _t_overflow;
               if ( 'left' == this._prop ) {
                 _t_overflow = this._get_element_overflow( $dropdown_menu, $dropdown_menu.offset().left, {}, this._window_width );
                overflow = _t_overflow > -5 ? _t_overflow : overflow ;
              }else { // rtl
                _t_overflow = $dropdown_menu.offset().left;
                overflow  = _t_overflow < 5 ? -1 * _t_overflow : overflow;
              }
                return overflow;
            },
            _get_element_overflow : function ( $_el, _offset, $_parent, _parent_width ) {
              _parent_width = $_parent.length ? $_parent.width() : _parent_width;
              return $_el.width() + _offset - _parent_width;
            },
            _set_dropdown_arrow_style : function( _menu_id, _offset ) {
              var _arrow_before_offset    = +9 - _offset,
                  _arrow_after_offset     = _arrow_before_offset + 1,
                  _arrow_css_rule_prefix  = '.tc-header .navbar .nav > .' + _menu_id + ' > .dropdown-menu',

                 _arrow_before_css_rule  = _arrow_css_rule_prefix + ":before { " + this._prop + ": " + _arrow_before_offset + "px;}",
                 _arrow_after_css_rule   = _arrow_css_rule_prefix + ":after { " + this._prop + ": " + _arrow_after_offset + "px;}";

              this._dyn_style += "\n" + _arrow_before_css_rule + "\n" + _arrow_after_css_rule;
            }
      };//_methods{}

      czrapp.methods.Dropdowns = {};
      $.extend( czrapp.methods.Dropdowns , _methods );

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
            var _evt = document.createEvent('Event');
            _evt.initEvent('czrapp-is-ready', true, true); //can bubble, and is cancellable
            document.dispatchEvent(_evt);
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
            $(function () {
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
(function($) {
    var _doWhenCzrappIsReady = function() {
          var pluginCompatParams = ( czrapp.localized && czrapp.localized.pluginCompats ) ? czrapp.localized.pluginCompats : {},
              frontHelpNoticeParams = ( czrapp.localized && czrapp.localized.frontHelpNoticeParams ) ? czrapp.localized.frontHelpNoticeParams : {};
          $( function( $ ) {
                if ( czrapp.localized.isParallaxOn ) {
                      $( '.czr-parallax-slider' ).czrParallax( { parallaxRatio : czrapp.localized.parallaxRatio || 0.55 } );
                }
          });
          if ( pluginCompatParams.optimizepress_compat && pluginCompatParams.optimizepress_compat.remove_fancybox_loading ) {
                  var opjq = opjq || 'undefined';
                  if ( ! _.isUndefined( opjq ) ) {
                      opjq(document).ready( function() {
                          opjq('#fancybox-loading').remove();
                      } );
                }
          }
    };
    if ( window.czrapp && czrapp.ready && 'resolved' == czrapp.ready.state() ) {
        _doWhenCzrappIsReady();
    } else {
        document.addEventListener('czrapp-is-ready', function() {
            _doWhenCzrappIsReady();
        });
    }
})(jQuery);var czrapp = czrapp || {};
( function ( czrapp ) {
      czrapp.localized = TCParams || {};
      var appMap = {
                base : {
                      ctor : czrapp.Base,
                      ready : [
                            'cacheProp',
                            'emitCustomEvents'
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
                            'dropCaps',
                            'extLinks',
                            'fancyBox',
                            'parallax'
                      ]
                },
                slider : {
                      ctor : czrapp.Base.extend( czrapp.methods.Slider ),
                      ready : [
                            'initOnDomReady',
                            'fireSliders',
                            'parallaxSliders',
                            'manageHoverClass',
                            'centerSliderArrows',
                            'addSwipeSupport',
                            'sliderTriggerSimpleLoad'
                      ]
                },
                dropdowns : {
                      ctor : czrapp.Base.extend( czrapp.methods.Dropdowns ),
                      ready : [ 'fireDropDown' ]
                },

                userXP : {
                      ctor : czrapp.Base.extend( czrapp.methods.UserXP ),
                      ready : [
                            'initOnDomReady',
                            'eventListener',
                            'outline',
                            'smoothScroll',
                            'anchorSmoothScroll',
                            'backToTop',
                            'widgetsHoverActions',
                            'attachmentsFadeEffect',
                            'clickableCommentButton',
                            'dynSidebarReorder',
                            'dropdownMenuEventsHandler',
                            'menuButtonHover',
                            'secondMenuRespActions',

                            'mayBePrintFrontNote'
                      ]
                },
                stickyHeader : {
                      ctor : czrapp.Base.extend( czrapp.methods.StickyHeader ),
                      ready : [
                            'initOnDomReady',
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

})( czrapp );