//Falls back to default params
var TCParams = TCParams || {
	CenterSlides: 1,
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
	SmoothScroll: "linear",
	stickyCustomOffset: 0,
	stickyHeader: 1,
	dropdowntoViewport: 1,
	timerOnScrollAllBrowsers:1
};/* ===================================================
 * bootstrap-transition.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#transitions
 * ===================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
//@tc addon
var TCParams = TCParams || {};

!function ($) {

  "use strict"; // jshint ;_;


  /* CSS TRANSITION SUPPORT (http://www.modernizr.com/)
   * ======================================================= */

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
/* =========================================================
 * bootstrap-modal.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#modals
 * =========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */


!function ($) {

  "use strict"; // jshint ;_;


 /* MODAL CLASS DEFINITION
  * ====================== */

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


 /* MODAL PLUGIN DEFINITION
  * ======================= */

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


 /* MODAL NO CONFLICT
  * ================= */

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


 /* MODAL DATA-API
  * ============== */

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

/* ============================================================
 * bootstrap-dropdown.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#dropdowns
 * ============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* DROPDOWN CLASS DEFINITION
  * ========================= */

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

      isActive = $parent.hasClass('open')

      clearMenus()

      if (!isActive) {
        if ('ontouchstart' in document.documentElement) {
          // if mobile we we use a backdrop because click events don't delegate
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


  /* DROPDOWN PLUGIN DEFINITION
   * ========================== */

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


 /* DROPDOWN NO CONFLICT
  * ==================== */

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  /* APPLY TO STANDARD DROPDOWN ELEMENTS
   * =================================== */

  $(document)
    .on('click.dropdown.data-api', clearMenus)
    .on('click.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
    .on('keydown.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)

}(window.jQuery);

/* ========================================================================
 * Bootstrap: scrollspy.js v3.0.0
 * http://getbootstrap.com/javascript/#scrollspy
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */


+function ($) { "use strict";

  // SCROLLSPY CLASS DEFINITION
  // ==========================

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


  // SCROLLSPY PLUGIN DEFINITION
  // ===========================

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


  // SCROLLSPY NO CONFLICT
  // =====================

  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old
    return this
  }


  // SCROLLSPY DATA-API
  // ==================

  $(window).on('load', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      $spy.scrollspy($spy.data())
    })
  })

}(window.jQuery);

/* ========================================================
 * bootstrap-tab.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#tabs
 * ========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TAB CLASS DEFINITION
  * ==================== */

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


 /* TAB PLUGIN DEFINITION
  * ===================== */

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


 /* TAB NO CONFLICT
  * =============== */

  $.fn.tab.noConflict = function () {
    $.fn.tab = old
    return this
  }


 /* TAB DATA-API
  * ============ */

  $(document).on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

}(window.jQuery);
/* ===========================================================
 * bootstrap-tooltip.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TOOLTIP PUBLIC CLASS DEFINITION
  * =============================== */

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


 /* TOOLTIP PLUGIN DEFINITION
  * ========================= */

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


 /* TOOLTIP NO CONFLICT
  * =================== */

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(window.jQuery);

/* ===========================================================
 * bootstrap-popover.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#popovers
 * ===========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * =========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* POPOVER PUBLIC CLASS DEFINITION
  * =============================== */

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }


  /* NOTE: POPOVER EXTENDS BOOTSTRAP-TOOLTIP.js
     ========================================== */

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


 /* POPOVER PLUGIN DEFINITION
  * ======================= */

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


 /* POPOVER NO CONFLICT
  * =================== */

  $.fn.popover.noConflict = function () {
    $.fn.popover = old
    return this
  }

}(window.jQuery);

/* ==========================================================
 * bootstrap-affix.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#affix
 * ==========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* AFFIX CLASS DEFINITION
  * ====================== */

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


 /* AFFIX PLUGIN DEFINITION
  * ======================= */

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


 /* AFFIX NO CONFLICT
  * ================= */

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


 /* AFFIX DATA-API
  * ============== */

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
/* ==========================================================
 * bootstrap-alert.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#alerts
 * ==========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* ALERT CLASS DEFINITION
  * ====================== */

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


 /* ALERT PLUGIN DEFINITION
  * ======================= */

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


 /* ALERT NO CONFLICT
  * ================= */

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


 /* ALERT DATA-API
  * ============== */

  $(document).on('click.alert.data-api', dismiss, Alert.prototype.close)

}(window.jQuery);
/* ============================================================
 * bootstrap-button.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#buttons
 * ============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* BUTTON PUBLIC CLASS DEFINITION
  * ============================== */

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

    // push to event loop to allow forms to submit
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


 /* BUTTON PLUGIN DEFINITION
  * ======================== */

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


 /* BUTTON NO CONFLICT
  * ================== */

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


 /* BUTTON DATA-API
  * =============== */

  $(document).on('click.button.data-api', '[data-toggle^=button]', function (e) {
    var $btn = $(e.target)
    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
    $btn.button('toggle')
  })

}(window.jQuery);
/* =============================================================
 * bootstrap-collapse.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#collapse
 * =============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* COLLAPSE PUBLIC CLASS DEFINITION
  * ================================ */

  var Collapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.collapse.defaults, options)

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

      if (this.transitioning || this.$element.hasClass('in')) return

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
      this.transition('addClass', $.Event('show'), 'shown')
      $.support.transition && this.$element[dimension](this.$element[0][scroll])

      //@tc adddon
      //give the revealed sub menu the height of the visible viewport
      if ( 1 == TCParams.dropdowntoViewport ) 
      {
        var tcVisible = $('body').hasClass('sticky-enabled') ? $(window).height() : ($(window).height() - $('.navbar-wrapper').offset().top);
        tcVisible = ( tcVisible - 90 ) > 80 ? tcVisible - 90 : 300;
        this.$element.css('max-height' , tcVisible + 'px');
      } 
      else if ( 1 != TCParams.dropdowntoViewport && 1 == TCParams.stickyHeader ) 
      {
        //trigger click on back to top if sticky enabled
        if ( 0 != $('.back-to-top').length ) {
          $('.back-to-top').trigger('click');
        }
        else {
          ('html, body').animate({
                  scrollTop: $(anchor_id).offset().top
              }, 700);
        }
        $('body').removeClass('sticky-enabled').removeClass('tc-sticky-header');
      }

    }//end of show:

  , hide: function () {
      var dimension
      if (this.transitioning || !this.$element.hasClass('in')) return
      dimension = this.dimension()
      this.reset(this.$element[dimension]())
      this.transition('removeClass', $.Event('hide'), 'hidden')
      this.$element[dimension](0)
      
      //@tc adddon
      if ( 1 != TCParams.dropdowntoViewport && 1 == TCParams.stickyHeader ) {
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
      this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

  }


 /* COLLAPSE PLUGIN DEFINITION
  * ========================== */

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


 /* COLLAPSE NO CONFLICT
  * ==================== */

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


 /* COLLAPSE DATA-API
  * ================= */
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
/* ==========================================================
 * bootstrap-carousel.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#carousel
 * ==========================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* CAROUSEL CLASS DEFINITION
  * ========================= */

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
      this.$active = this.$element.find('.item.active')
      this.$items = this.$active.parent().children()
      return this.$items.index(this.$active)
    }

  , to: function (pos) {
      var activeIndex = this.getActiveIndex()
        , that = this

      if (pos > (this.$items.length - 1) || pos < 0) return

      if (this.sliding) {
        return this.$element.one('slid', function () {
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
      if(!$.support.transition && this.$element.hasClass('slide')) {
         this.$element.find('.item').stop(true, true); //Finish animation and jump to end.
      }
      var $active = this.$element.find('.item.active')
        , $next = next || $active[type]()
        , isCycling = this.interval
        , direction = type == 'next' ? 'left' : 'right'
        , fallback  = type == 'next' ? 'first' : 'last'
        , that = this
        , e

      this.sliding = true

      isCycling && this.pause()

      $next = $next.length ? $next : this.$element.find('.item')[fallback]()

      e = $.Event('slide', {
        relatedTarget: $next[0]
      , direction: direction
      })

      if ($next.hasClass('active')) return

      if (this.$indicators.length) {
        this.$indicators.find('.active').removeClass('active')
        this.$element.one('slid', function () {
          var $nextIndicator = $(that.$indicators.children()[that.getActiveIndex()])
          $nextIndicator && $nextIndicator.addClass('active')
        })
      }

      if ($.support.transition && this.$element.hasClass('slide')) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $next.addClass(type)
        $next[0].offsetWidth // force reflow
        $active.addClass(direction)
        $next.addClass(direction)
        this.$element.one($.support.transition.end, function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () { that.$element.trigger('slid') }, 0)
        })
      } else if(!$.support.transition && this.$element.hasClass('slide')) {
          this.$element.trigger(e)
          if (e.isDefaultPrevented()) return
          $active.animate({left: (direction == 'right' ? '100%' : '-100%')}, 600, function(){
             $active.removeClass('active')
              that.sliding = false
              setTimeout(function () { that.$element.trigger('slid') }, 0)
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
        this.$element.trigger('slid')
      }

      isCycling && this.cycle()

      return this
    }

  }


 /* CAROUSEL PLUGIN DEFINITION
  * ========================== */

  var old = $.fn.carousel

  $.fn.carousel = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('carousel')
        , options = $.extend({}, $.fn.carousel.defaults, typeof option == 'object' && option)
        , action = typeof option == 'string' ? option : options.slide
      if (!data) $this.data('carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.pause().cycle()
    })
  }

  $.fn.carousel.defaults = {
    interval: 5000
  , pause: 'hover'
  }

  $.fn.carousel.Constructor = Carousel


 /* CAROUSEL NO CONFLICT
  * ==================== */

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }

 /* CAROUSEL DATA-API
  * ================= */

  $(document).on('click.carousel.data-api', '[data-slide], [data-slide-to]', function (e) {
    var $this = $(this), href
      , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      , options = $.extend({}, $target.data(), $this.data())
      , slideIndex

    $target.carousel(options)

    if (slideIndex = $this.attr('data-slide-to')) {
      $target.data('carousel').pause().to(slideIndex).cycle()
    }

    e.preventDefault()
  })

}(window.jQuery);
/* =============================================================
 * bootstrap-typeahead.js v2.3.2
 * http://getbootstrap.com/2.3.2/javascript.html#typeahead
 * =============================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function($){

  "use strict"; // jshint ;_;


 /* TYPEAHEAD PUBLIC CLASS DEFINITION
  * ================================= */

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


  /* TYPEAHEAD PLUGIN DEFINITION
   * =========================== */

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


 /* TYPEAHEAD NO CONFLICT
  * =================== */

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old
    return this
  }


 /* TYPEAHEAD DATA-API
  * ================== */

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this)
    if ($this.data('typeahead')) return
    $this.typeahead($this.data())
  })

}(window.jQuery);
;!function(a){var b,c,d,e,f,g,h,i,j,k,v,z,A,l=0,m={},n=[],o=0,p={},q=[],r=null,s=new Image,t=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,u=/[^\.]\.(swf)\s*$/i,w=1,x=0,y="",B=!1,C=a.extend(a("<div/>")[0],{prop:0}),D=a.browser.msie&&a.browser.version<7&&!window.XMLHttpRequest,E=function(){c.hide(),s.onerror=s.onload=null,r&&r.abort(),b.empty()},F=function(){return!1===m.onError(n,l,m)?(c.hide(),B=!1,void 0):(m.titleShow=!1,m.width="auto",m.height="auto",b.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>'),H(),void 0)},G=function(){var e,f,h,i,j,k,d=n[l];if(E(),m=a.extend({},a.fn.fancybox.defaults,"undefined"==typeof a(d).data("fancybox")?m:a(d).data("fancybox")),k=m.onStart(n,l,m),k===!1)return B=!1,void 0;if("object"==typeof k&&(m=a.extend(m,k)),h=m.title||(d.nodeName?a(d).attr("title"):d.title)||"",d.nodeName&&!m.orig&&(m.orig=a(d).children("img:first").length?a(d).children("img:first"):a(d)),""===h&&m.orig&&m.titleFromAlt&&(h=m.orig.attr("alt")),e=m.href||(d.nodeName?a(d).attr("href"):d.href)||null,(/^(?:javascript)/i.test(e)||"#"==e)&&(e=null),m.type?(f=m.type,e||(e=m.content)):m.content?f="html":e&&(f=e.match(t)?"image":e.match(u)?"swf":a(d).hasClass("iframe")?"iframe":0===e.indexOf("#")?"inline":"ajax"),!f)return F(),void 0;switch("inline"==f&&(d=e.substr(e.indexOf("#")),f=a(d).length>0?"inline":"ajax"),m.type=f,m.href=e,m.title=h,m.autoDimensions&&("html"==m.type||"inline"==m.type||"ajax"==m.type?(m.width="auto",m.height="auto"):m.autoDimensions=!1),m.modal&&(m.overlayShow=!0,m.hideOnOverlayClick=!1,m.hideOnContentClick=!1,m.enableEscapeButton=!1,m.showCloseButton=!1),m.padding=parseInt(m.padding,10),m.margin=parseInt(m.margin,10),b.css("padding",m.padding+m.margin),a(".fancybox-inline-tmp").unbind("fancybox-cancel").bind("fancybox-change",function(){a(this).replaceWith(g.children())}),f){case"html":b.html(m.content),H();break;case"inline":if(a(d).parent().is("#fancybox-content")===!0)return B=!1,void 0;a('<div class="fancybox-inline-tmp" />').hide().insertBefore(a(d)).bind("fancybox-cleanup",function(){a(this).replaceWith(g.children())}).bind("fancybox-cancel",function(){a(this).replaceWith(b.children())}),a(d).appendTo(b),H();break;case"image":B=!1,a.fancybox.showActivity(),s=new Image,s.onerror=function(){F()},s.onload=function(){B=!0,s.onerror=s.onload=null,I()},s.src=e;break;case"swf":m.scrolling="no",i='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+m.width+'" height="'+m.height+'"><param name="movie" value="'+e+'"></param>',j="",a.each(m.swf,function(a,b){i+='<param name="'+a+'" value="'+b+'"></param>',j+=" "+a+'="'+b+'"'}),i+='<embed src="'+e+'" type="application/x-shockwave-flash" width="'+m.width+'" height="'+m.height+'"'+j+"></embed></object>",b.html(i),H();break;case"ajax":B=!1,a.fancybox.showActivity(),m.ajax.win=m.ajax.success,r=a.ajax(a.extend({},m.ajax,{url:e,data:m.ajax.data||{},error:function(a){a.status>0&&F()},success:function(a,d,f){var g="object"==typeof f?f:r;if(200==g.status){if("function"==typeof m.ajax.win){if(k=m.ajax.win(e,a,d,f),k===!1)return c.hide(),void 0;("string"==typeof k||"object"==typeof k)&&(a=k)}b.html(a),H()}}}));break;case"iframe":J()}},H=function(){var c=m.width,d=m.height;c=c.toString().indexOf("%")>-1?parseInt((a(window).width()-2*m.margin)*parseFloat(c)/100,10)+"px":"auto"==c?"auto":c+"px",d=d.toString().indexOf("%")>-1?parseInt((a(window).height()-2*m.margin)*parseFloat(d)/100,10)+"px":"auto"==d?"auto":d+"px",b.wrapInner('<div style="width:'+c+";height:"+d+";overflow: "+("auto"==m.scrolling?"auto":"yes"==m.scrolling?"scroll":"hidden")+';position:relative;"></div>'),m.width=b.width(),m.height=b.height(),J()},I=function(){m.width=s.width,m.height=s.height,a("<img />").attr({id:"fancybox-img",src:s.src,alt:m.title}).appendTo(b),J()},J=function(){var f,r;return c.hide(),e.is(":visible")&&!1===p.onCleanup(q,o,p)?(a.event.trigger("fancybox-cancel"),B=!1,void 0):(B=!0,a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),e.is(":visible")&&"outside"!==p.titlePosition&&e.css("height",e.height()),q=n,o=l,p=m,p.overlayShow?(d.css({"background-color":p.overlayColor,opacity:p.overlayOpacity,cursor:p.hideOnOverlayClick?"pointer":"auto",height:a(document).height()}),d.is(":visible")||(D&&a("select:not(#fancybox-tmp select)").filter(function(){return"hidden"!==this.style.visibility}).css({visibility:"hidden"}).one("fancybox-cleanup",function(){this.style.visibility="inherit"}),d.show())):d.hide(),A=R(),L(),e.is(":visible")?(a(h.add(j).add(k)).hide(),f=e.position(),z={top:f.top,left:f.left,width:e.width(),height:e.height()},r=z.width==A.width&&z.height==A.height,g.fadeTo(p.changeFade,.3,function(){var c=function(){g.html(b.contents()).fadeTo(p.changeFade,1,N)};a.event.trigger("fancybox-change"),g.empty().removeAttr("filter").css({"border-width":p.padding,width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}),r?c():(C.prop=0,a(C).animate({prop:1},{duration:p.changeSpeed,easing:p.easingChange,step:P,complete:c}))}),void 0):(e.removeAttr("style"),g.css("border-width",p.padding),"elastic"==p.transitionIn?(z=T(),g.html(b.contents()),e.show(),p.opacity&&(A.opacity=0),C.prop=0,a(C).animate({prop:1},{duration:p.speedIn,easing:p.easingIn,step:P,complete:N}),void 0):("inside"==p.titlePosition&&x>0&&i.show(),g.css({width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}).html(b.contents()),e.css(A).fadeIn("none"==p.transitionIn?0:p.speedIn,N),void 0)))},K=function(a){return a&&a.length?"float"==p.titlePosition?'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+a+'</td><td id="fancybox-title-float-right"></td></tr></table>':'<div id="fancybox-title-'+p.titlePosition+'">'+a+"</div>":!1},L=function(){if(y=p.title||"",x=0,i.empty().removeAttr("style").removeClass(),p.titleShow===!1)return i.hide(),void 0;if(y=a.isFunction(p.titleFormat)?p.titleFormat(y,q,o,p):K(y),!y||""===y)return i.hide(),void 0;switch(i.addClass("fancybox-title-"+p.titlePosition).html(y).appendTo("body").show(),p.titlePosition){case"inside":i.css({width:A.width-2*p.padding,marginLeft:p.padding,marginRight:p.padding}),x=i.outerHeight(!0),i.appendTo(f),A.height+=x;break;case"over":i.css({marginLeft:p.padding,width:A.width-2*p.padding,bottom:p.padding}).appendTo(f);break;case"float":i.css("left",-1*parseInt((i.width()-A.width-40)/2,10)).appendTo(e);break;default:i.css({width:A.width-2*p.padding,paddingLeft:p.padding,paddingRight:p.padding}).appendTo(e)}i.hide()},M=function(){return(p.enableEscapeButton||p.enableKeyboardNav)&&a(document).bind("keydown.fb",function(b){27==b.keyCode&&p.enableEscapeButton?(b.preventDefault(),a.fancybox.close()):37!=b.keyCode&&39!=b.keyCode||!p.enableKeyboardNav||"INPUT"===b.target.tagName||"TEXTAREA"===b.target.tagName||"SELECT"===b.target.tagName||(b.preventDefault(),a.fancybox[37==b.keyCode?"prev":"next"]())}),p.showNavArrows?((p.cyclic&&q.length>1||0!==o)&&j.show(),(p.cyclic&&q.length>1||o!=q.length-1)&&k.show(),void 0):(j.hide(),k.hide(),void 0)},N=function(){a.support.opacity||(g.get(0).style.removeAttribute("filter"),e.get(0).style.removeAttribute("filter")),m.autoDimensions&&g.css("height","auto"),e.css("height","auto"),y&&y.length&&i.show(),p.showCloseButton&&h.show(),M(),p.hideOnContentClick&&g.bind("click",a.fancybox.close),p.hideOnOverlayClick&&d.bind("click",a.fancybox.close),a(window).bind("resize.fb",a.fancybox.resize),p.centerOnScroll&&a(window).bind("scroll.fb",a.fancybox.center),"iframe"==p.type&&a('<iframe id="fancybox-frame" name="fancybox-frame'+(new Date).getTime()+'" frameborder="0" hspace="0" '+(a.browser.msie?'allowtransparency="true""':"")+' scrolling="'+m.scrolling+'" src="'+p.href+'"></iframe>').appendTo(g),e.show(),B=!1,a.fancybox.center(),p.onComplete(q,o,p),O()},O=function(){var a,b;q.length-1>o&&(a=q[o+1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a)),o>0&&(a=q[o-1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a))},P=function(a){var b={width:parseInt(z.width+(A.width-z.width)*a,10),height:parseInt(z.height+(A.height-z.height)*a,10),top:parseInt(z.top+(A.top-z.top)*a,10),left:parseInt(z.left+(A.left-z.left)*a,10)};"undefined"!=typeof A.opacity&&(b.opacity=.5>a?.5:a),e.css(b),g.css({width:b.width-2*p.padding,height:b.height-x*a-2*p.padding})},Q=function(){return[a(window).width()-2*p.margin,a(window).height()-2*p.margin,a(document).scrollLeft()+p.margin,a(document).scrollTop()+p.margin]},R=function(){var e,a=Q(),b={},c=p.autoScale,d=2*p.padding;return b.width=p.width.toString().indexOf("%")>-1?parseInt(a[0]*parseFloat(p.width)/100,10):p.width+d,b.height=p.height.toString().indexOf("%")>-1?parseInt(a[1]*parseFloat(p.height)/100,10):p.height+d,c&&(b.width>a[0]||b.height>a[1])&&("image"==m.type||"swf"==m.type?(e=p.width/p.height,b.width>a[0]&&(b.width=a[0],b.height=parseInt((b.width-d)/e+d,10)),b.height>a[1]&&(b.height=a[1],b.width=parseInt((b.height-d)*e+d,10))):(b.width=Math.min(b.width,a[0]),b.height=Math.min(b.height,a[1]))),b.top=parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-b.height-40)),10),b.left=parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-b.width-40)),10),b},S=function(a){var b=a.offset();return b.top+=parseInt(a.css("paddingTop"),10)||0,b.left+=parseInt(a.css("paddingLeft"),10)||0,b.top+=parseInt(a.css("border-top-width"),10)||0,b.left+=parseInt(a.css("border-left-width"),10)||0,b.width=a.width(),b.height=a.height(),b},T=function(){var d,e,b=m.orig?a(m.orig):!1,c={};return b&&b.length?(d=S(b),c={width:d.width+2*p.padding,height:d.height+2*p.padding,top:d.top-p.padding-20,left:d.left-p.padding-20}):(e=Q(),c={width:2*p.padding,height:2*p.padding,top:parseInt(e[3]+.5*e[1],10),left:parseInt(e[2]+.5*e[0],10)}),c},U=function(){return c.is(":visible")?(a("div",c).css("top",-40*w+"px"),w=(w+1)%12,void 0):(clearInterval(v),void 0)};a.fn.fancybox=function(b){return a(this).length?(a(this).data("fancybox",a.extend({},b,a.metadata?a(this).metadata():{})).unbind("click.fb").bind("click.fb",function(b){if(b.preventDefault(),!B){B=!0,a(this).blur(),n=[],l=0;var c=a(this).attr("rel")||"";c&&""!=c&&"nofollow"!==c?(n=a("a[rel="+c+"], area[rel="+c+"]"),l=n.index(this)):n.push(this),G()}}),this):this},a.fancybox=function(b){var c;if(!B){if(B=!0,c="undefined"!=typeof arguments[1]?arguments[1]:{},n=[],l=parseInt(c.index,10)||0,a.isArray(b)){for(var d=0,e=b.length;e>d;d++)"object"==typeof b[d]?a(b[d]).data("fancybox",a.extend({},c,b[d])):b[d]=a({}).data("fancybox",a.extend({content:b[d]},c));n=jQuery.merge(n,b)}else"object"==typeof b?a(b).data("fancybox",a.extend({},c,b)):b=a({}).data("fancybox",a.extend({content:b},c)),n.push(b);(l>n.length||0>l)&&(l=0),G()}},a.fancybox.showActivity=function(){clearInterval(v),c.show(),v=setInterval(U,66)},a.fancybox.hideActivity=function(){c.hide()},a.fancybox.next=function(){return a.fancybox.pos(o+1)},a.fancybox.prev=function(){return a.fancybox.pos(o-1)},a.fancybox.pos=function(a){B||(a=parseInt(a),n=q,a>-1&&a<q.length?(l=a,G()):p.cyclic&&q.length>1&&(l=a>=q.length?0:q.length-1,G()))},a.fancybox.cancel=function(){B||(B=!0,a.event.trigger("fancybox-cancel"),E(),m.onCancel(n,l,m),B=!1)},a.fancybox.close=function(){function b(){d.fadeOut("fast"),i.empty().hide(),e.hide(),a.event.trigger("fancybox-cleanup"),g.empty(),p.onClosed(q,o,p),q=m=[],o=l=0,p=m={},B=!1}if(!B&&!e.is(":hidden")){if(B=!0,p&&!1===p.onCleanup(q,o,p))return B=!1,void 0;if(E(),a(h.add(j).add(k)).hide(),a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),g.find("iframe").attr("src",D&&/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank"),"inside"!==p.titlePosition&&i.empty(),e.stop(),"elastic"==p.transitionOut){z=T();var c=e.position();A={top:c.top,left:c.left,width:e.width(),height:e.height()},p.opacity&&(A.opacity=1),i.empty().hide(),C.prop=1,a(C).animate({prop:0},{duration:p.speedOut,easing:p.easingOut,step:P,complete:b})}else e.fadeOut("none"==p.transitionOut?0:p.speedOut,b)}},a.fancybox.resize=function(){d.is(":visible")&&d.css("height",a(document).height()),a.fancybox.center(!0)},a.fancybox.center=function(){var a,b;B||(b=arguments[0]===!0?1:0,a=Q(),(b||!(e.width()>a[0]||e.height()>a[1]))&&e.stop().animate({top:parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-g.height()-40)-p.padding)),left:parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-g.width()-40)-p.padding))},"number"==typeof arguments[0]?arguments[0]:200))},a.fancybox.init=function(){a("#fancybox-wrap").length||(a("body").append(b=a('<div id="fancybox-tmp"></div>'),c=a('<div id="fancybox-loading"><div></div></div>'),d=a('<div id="fancybox-overlay"></div>'),e=a('<div id="fancybox-wrap"></div>')),f=a('<div id="fancybox-outer"></div>').append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>').appendTo(e),f.append(g=a('<div id="fancybox-content"></div>'),h=a('<a id="fancybox-close"></a>'),i=a('<div id="fancybox-title"></div>'),j=a('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),k=a('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>')),h.click(a.fancybox.close),c.click(a.fancybox.cancel),j.click(function(b){b.preventDefault(),a.fancybox.prev()}),k.click(function(b){b.preventDefault(),a.fancybox.next()}),a.fn.mousewheel&&e.bind("mousewheel.fb",function(b,c){B?b.preventDefault():(0==a(b.target).get(0).clientHeight||a(b.target).get(0).scrollHeight===a(b.target).get(0).clientHeight)&&(b.preventDefault(),a.fancybox[c>0?"prev":"next"]())}),a.support.opacity||e.addClass("fancybox-ie"),D&&(c.addClass("fancybox-ie6"),e.addClass("fancybox-ie6"),a('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank")+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(f)))},a.fn.fancybox.defaults={padding:10,margin:40,opacity:!1,modal:!1,cyclic:!1,scrolling:"auto",width:560,height:340,autoScale:!0,autoDimensions:!0,centerOnScroll:!1,ajax:{},swf:{wmode:"transparent"},hideOnOverlayClick:!0,hideOnContentClick:!1,overlayShow:!0,overlayOpacity:.7,overlayColor:"#777",titleShow:!0,titlePosition:"float",titleFormat:null,titleFromAlt:!1,transitionIn:"fade",transitionOut:"fade",speedIn:300,speedOut:300,changeSpeed:300,changeFade:"fast",easingIn:"swing",easingOut:"swing",showCloseButton:!0,showNavArrows:!0,enableEscapeButton:!0,enableKeyboardNav:!0,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}},a(document).ready(function(){a.fancybox.init()})}(jQuery);;/* !
 * Customizr WordPress theme Javascript code
 * Copyright (c) 2014 Nicolas GUILLAUME (@nicguillaume), Themes & Co.
 * GPL2+ Licensed
*/
//ON DOM READY
jQuery(function ($) {
    function g($) {
        return ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
    }

    //fancybox with localized script variables
    var b = TCParams.FancyBoxState,
        c = TCParams.FancyBoxAutoscale;
    if ( 1 == b ) {
            $("a.grouped_elements").fancybox({
            transitionIn: "elastic",
            transitionOut: "elastic",
            speedIn: 200,
            speedOut: 200,
            overlayShow: !1,
            autoScale: 1 == c ? "true" : "false",
            changeFade: "fast",
            enableEscapeButton: !0
         });
         //replace title by img alt field
         $('a[rel*=tc-fancybox-group]').each( function() {
            var title = $(this).find('img').prop('title');
            var alt = $(this).find('img').prop('alt');
            if (typeof title !== 'undefined' && 0 !== title.length) {
                $(this).attr('title',title);
            }
            else if (typeof alt !== 'undefined' &&  0 !== alt.length) {
                $(this).attr('title',alt);
            }
         });
	}


    //Slider with localized script variables
    var d = TCParams.SliderName,
        e = TCParams.SliderDelay;
        j = TCParams.SliderHover;

    if (0 !== d.length) {
        if (0 !== e.length && !j) {
            $("#customizr-slider").carousel({
                interval: e,
                pause: "false"
            });
        } else if (0 !== e.length) {
            $("#customizr-slider").carousel({
                interval: e
            });
        } else {
            $("#customizr-slider").carousel();
        }
    }

    //add a class to the slider on hover => used to display the navigation arrow
    $(".carousel").hover( function() {
            $(this).addClass('tc-slid-hover');
        },
        function() {
            $(this).removeClass('tc-slid-hover');
        }
    );

    //Smooth scroll but not on bootstrap buttons. Checks if php localized option is active first.
    var SmoothScroll = TCParams.SmoothScroll;

    if ('easeOutExpo' == SmoothScroll) {
        $('a[href^="#"]', '#content').not('[class*=edd], .tc-carousel-control, .carousel-control, [data-toggle="modal"], [data-toggle="dropdown"], [data-toggle="tooltip"], [data-toggle="popover"], [data-toggle="collapse"], [data-toggle="tab"]').click(function () {
            var anchor_id = $(this).attr("href");
            if ('#' != anchor_id) {
                $('html, body').animate({
                    scrollTop: $(anchor_id).offset().top
                }, 700, SmoothScroll);
            }
            return false;
        });
    }

    //BACK TO TOP
    //Stop the viewport animation if user interaction is detected
    var f = $("html, body");
    $(".back-to-top, .tc-btt-wrapper, .btt-arrow").on("click touchstart touchend", function ($) {
            f.on("scroll mousedown DOMMouseScroll mousewheel keyup", g);
            f.animate({
                scrollTop: 0
            }, 1e3, function () {
                f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
                //$(window).trigger('resize');
            });
            $.preventDefault();
    });

    //displays the button on scroll
    $(document).on( 'scroll', function() {
        //console.log($(window));
            if ( $(window).scrollTop() > 100 ) {
                $('.tc-btt-wrapper').addClass('show');
            } else {
                $('.tc-btt-wrapper').removeClass('show');
            }
        }
    );

    //Detects browser with CSS
    // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
    if ( $.browser.chrome )
        $("body").addClass("chrome");
    else if ( $.browser.webkit )
        $("body").addClass("safari");
    else if ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version )
        $("body").addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, ''));
    
    //Adds version if browser = ie
    if ( $("body").hasClass("ie") )
        $("body").addClass($.browser.version);

    
    //handle some dynamic hover effects
    $(".widget-front, article").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    });

    $(".widget li").hover(function () {
        $(this).addClass("on");
    }, function () {
        $(this).removeClass("on");
    });

    $("article.attachment img").delay(500).animate({
            opacity: 1
        }, 700, function () {}
    );

    //Change classes of the comment reply and edit to make the whole button clickable (no filters offered in WP to do that)
    if ( TCParams.HasComments ) {
       //edit
       $('cite p.edit-link').each(function() {
            $(this).removeClass('btn btn-success btn-mini');
       });
       $('cite p.edit-link > a').each(function() {
            $(this).addClass('btn btn-success btn-mini');
       });
       //reply
       $('.comment .reply').each(function() {
            $(this).removeClass('btn btn-small');
       });
       $('.comment .reply .comment-reply-link').each(function() {
            $(this).addClass('btn btn-small');
       });
    }


    //Detect layout and reorder content divs
    var LeftSidebarClass    = TCParams.LeftSidebarClass || '.span3.left.tc-sidebar',
        RightSidebarClass   = TCParams.RightSidebarClass || '.span3.right.tc-sidebar',
        wrapper             = $('#main-wrapper .container[role=main] > .column-content-wrapper'),
        content             = $("#main-wrapper .container .article-container"),
        left                = $("#main-wrapper .container " + LeftSidebarClass),
        right               = $("#main-wrapper .container " + RightSidebarClass);

    function BlockPositions() {
        //15 pixels adjustement to avoid replacement before real responsive width
        WindowWidth = $(window).width();
        if ( WindowWidth > 767 - 15 ) {
            //$(window).width();
            if ( $(left).length ) {
                $(left).detach();
                $(content).detach();
                $(wrapper).append($(left)).append($(content));
            }
            if ( $(right).length ) {
                $(right).detach();
                $(wrapper).append($(right));
            }
        } else {
            if ( $(left).length ) {
                 $(left).detach();
                $(content).detach();
                $(wrapper).append($(content)).append( $(left) );
            }
            if ( $(right).length ) {
                $(right).detach();
                $(wrapper).append($(right));
            }
        }
    }//end function*/

    //Enable reordering if option is checked in the customizer.
    if ( 1 == TCParams.ReorderBlocks ) {
        //trigger the block positioning only when responsive
        WindowWidth = $(window).width();
        if ( WindowWidth <= 767 - 15 ) {
            BlockPositions();
        }

        $(window).resize(function () {
            setTimeout(BlockPositions, 200);
        });
    }
    

    function centerImageInContainer(container , images){
        if ( ! $(images).length  )
            return;

        $(images).each(function () {
            var container_width    = $(this).closest(container).width(),
                container_height	= $(container).height(),
                // this will let us know the real img height
                ratio = container_width / $(this).attr("width"),
                real_img_height = ratio * $(this).attr("height");
            
            // if our image has an height smaller than the container
            // stretch it (h & w) proportionally to reach the container height
            // and center it horizontally
            if ( real_img_height < container_height ){
                // set the image height as the container height
                $(this).css("height", container_height);
                // this will let us know the new image width
                var img_ratio = container_height / real_img_height;
                var new_img_width = img_ratio * container_width;
                // set it
                $(this).css("width", new_img_width).css("max-width", new_img_width);

                // center it horizontally
                var pos_left = ( (container_width - new_img_width ) / 2 );
                $(this).css("left", pos_left);
                
                // reset v-center flag and margin-top
                if ( $(this).hasClass("v-center") ){
                    $(this).removeClass("v-center")
                    .css("top", "0px");
                }

                // add h-center class flag
                $(this).addClass("h-center");

            } else { // center it vertically
                    // this covers also the case real_img_height == container_height
                    // a differentiation here looks like pratically useless

                // reset margin-left, width, height and h-center flag
                if ( $(this).hasClass("h-center") ){
                    $(this).css("width", "100%")
                    .css("max-width", "100%")
                    .css("left", "0px")
                    .css("height", "auto")
                    .removeClass("h-center");
                }
                // center it vertically
                var pos_top = ( container_height - real_img_height ) / 2 ;
                $(this).css("top", pos_top);
                // add v-center class flag
                $(this).addClass("v-center");
            }// end if-else                                      
        });// end imgs each function
        
    }// end centerImageInContainer

     //Enable slides centering if option is checked in the customizer.
    if ( 1 == TCParams.CenterSlides ) {
        //adds a specific class to the carousel when automatic centering is enabled
        $('#customizr-slider .carousel-inner').addClass('center-slides-enabled');

        setTimeout( function() {
            centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image > img' );
            $('.tc-slider-loader-wrapper').hide();
        } , 50);

        $(window).resize(function(){
            setTimeout( function() {
                centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image > img' );
            }, 50);
        });
    }//end of center slides

    function _center_slider_arrows() {
        if ( 0 === $('.carousel').length )
            return;
        $('.carousel').each( function() {
            var _slider_height = $( '.carousel-inner' , $(this) ).height();
            $('.tc-slider-controls', $(this) ).css("line-height", _slider_height +'px').css("max-height", _slider_height +'px');
        });
    }
    //Recenter the slider arrows
    $(window).resize(function(){
    	_center_slider_arrows();
    });
    _center_slider_arrows();

    //CENTER RECTANGULAR THUMBNAILS FOR POST LIST AND SINGLE POST VIEWS
    //on load
    setTimeout( function() {
        centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    }, 300 );
    //on resize
    $(window).resize(function(){
            centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    });
    //bind 'refresh-height' event (triggered from the customizer)
    $('.tc-rectangular-thumb').on('refresh-height' , function(){
        centerImageInContainer( '.tc-rectangular-thumb' , '.tc-rectangular-thumb > img' );
    });

    //Slider swipe support with hammer.js
	if ( 'function' == typeof($.fn.hammer) ) {
		$('.carousel' ).each( function() {
			$(this).hammer().on('swipeleft tap', function() {
				$(this).carousel('next');
			});
			$(this).hammer().on('swiperight', function(){
                $(this).carousel('prev');
            });
		});
	}
});


/* Sticky header since v3.2.0 */
jQuery(function ($) {
	var	   $tcHeader		= $('.tc-header'),
			elToHide		= [], //[ '.social-block' , '.site-description' ],
			isUserLogged	= $('body').hasClass('logged-in') || 0 !== $('#wpadminbar').length,
			isCustomizing	= $('body').hasClass('is-customizing'),
			customOffset	= +TCParams.stickyCustomOffset;

	function _is_scrolling() {
		return $('body').hasClass('sticky-enabled') ? true : false;
	}

	function _is_sticky_enabled() {
		return $('body').hasClass('tc-sticky-header') ? true : false;
	}

	function _get_initial_offset() {
		//initialOffset 	= ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
		var initialOffset 	= 0;
		if ( 1 == isUserLogged && ! isCustomizing ) {
			if ( 580 < $(window).width() )
				initialOffset = $('#wpadminbar').height();
			else
				initialOffset = ! _is_scrolling() ? $('#wpadminbar').height() : 0;
		}
		return initialOffset + customOffset;
	}

	function _set_sticky_offsets() {
		if ( ! _is_sticky_enabled() )
			return;

		//Reset all values first
		$tcHeader.css('top' , '');
		$('.tc-header').css('height' , 'auto' );
		$('#tc-reset-margin-top').css('margin-top' , '' ).show();

		//What is the initial offset of the header ?
		var	headerHeight 	= $tcHeader.height();
		//set initial margin-top = initial offset + header's height
		$('#tc-reset-margin-top').css('margin-top' , ( +headerHeight + customOffset ) + 10 + 'px' ); //10 = header bottom border
	}


	function _set_header_top_offset() {
		//set header initial offset
		$tcHeader.css('top' , _get_initial_offset() + 'px');
	}

	function _set_no_title_logo_class() {
		if ( ! $('body').hasClass('sticky-enabled') ) {
			$('.navbar-wrapper').addClass('span9').removeClass('span12');
		} else {
			$('.tc-title-logo-off .navbar-wrapper' , '.sticky-enabled').addClass('span12').removeClass('span9');
			$('.tc-title-logo-on .navbar-wrapper' , '.sticky-enabled').addClass('span9').removeClass('span12');
		}
	}	

	//set site logo width and height if exists
	//=> allow the CSS3 transition to be enabled
	if ( _is_sticky_enabled() && 0 !== $('img' , '.site-logo').length ) {
		var logoWidth 	= $('img' , '.site-logo').attr('width'),
			logoHeight 	= $('img' , '.site-logo').attr('height');
		$('img' , '.site-logo').css('height' , logoHeight +'px' ).css('width' , logoWidth +'px' );
	}

	//LOADING ACTIONS
	if ( _is_sticky_enabled() )
        setTimeout( function() { _refresh(); } , 20 );
	if ( _is_sticky_enabled() && ! $('body').hasClass('sticky-enabled') )
        $('body').addClass("sticky-disabled");

	//RESIZING ACTIONS
	$(window).resize(function() {
		if ( ! _is_sticky_enabled() )
			return;
		_set_sticky_offsets();
		_set_header_top_offset();
		_set_no_title_logo_class();
	});

	function _refresh() {
		setTimeout( function() {
			_set_sticky_offsets();
			_set_header_top_offset();
			_set_no_title_logo_class();
		} , 20 );
		$(window).trigger('resize');
	}

	//SCROLLING ACTIONS
	var timer,
		increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer

	//var windowHeight = $(window).height();  
	var triggerHeight = 20; //0.5 * windowHeight;

	function _scrolling_actions() {
		_set_header_top_offset();
		_set_no_title_logo_class();
		//process scrolling actions
		if ( $(window).scrollTop() > triggerHeight ) {
            $('body').addClass("sticky-enabled").removeClass("sticky-disabled");
		}
		else {
            $('body').removeClass("sticky-enabled").addClass("sticky-disabled");
            setTimeout( function() { _refresh();} ,
                $('body').hasClass('is-customizing') ? 100 : 20
	       );
		}
	}

	$(window).scroll(function() {
		if ( ! _is_sticky_enabled() )
			return;
		//use a timer
		if ( timer) {
			increment++;
            window.clearTimeout(timer);
         }
         
         if ( 1 == TCParams.timerOnScrollAllBrowsers ) {
            timer = window.setTimeout(function() {
                _scrolling_actions();
	         }, increment > 5 ? 50 : 0 );
         } else if ( $('body').hasClass('ie') ) {
	         timer = window.setTimeout(function() {
                _scrolling_actions();
	         }, increment > 5 ? 50 : 0 );
		}
	});//end of window.scroll()
});