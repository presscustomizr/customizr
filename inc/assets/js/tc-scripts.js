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
	timerOnScrollAllBrowsers:1,
  extLinksStyle :1,
  extLinksTargetExt:1,
  dropcapEnabled:1,
  dropcapWhere:{ post : 0, page : 1 },
  dropcapMinWords:50,
  skipSelectors: {
              tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'],
              classes : [],
              ids : []
            },
  imgSmartLoadEnabled:0,
  imgSmartLoadOpts: {}
};;/* ===================================================
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
;!function(a){var b,c,d,e,f,g,h,i,j,k,v,z,A,l=0,m={},n=[],o=0,p={},q=[],r=null,s=new Image,t=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,u=/[^\.]\.(swf)\s*$/i,w=1,x=0,y="",B=!1,C=a.extend(a("<div/>")[0],{prop:0}),D=a.browser.msie&&a.browser.version<7&&!window.XMLHttpRequest,E=function(){c.hide(),s.onerror=s.onload=null,r&&r.abort(),b.empty()},F=function(){return!1===m.onError(n,l,m)?(c.hide(),B=!1,void 0):(m.titleShow=!1,m.width="auto",m.height="auto",b.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>'),H(),void 0)},G=function(){var e,f,h,i,j,k,d=n[l];if(E(),m=a.extend({},a.fn.fancybox.defaults,"undefined"==typeof a(d).data("fancybox")?m:a(d).data("fancybox")),k=m.onStart(n,l,m),k===!1)return B=!1,void 0;if("object"==typeof k&&(m=a.extend(m,k)),h=m.title||(d.nodeName?a(d).attr("title"):d.title)||"",d.nodeName&&!m.orig&&(m.orig=a(d).children("img:first").length?a(d).children("img:first"):a(d)),""===h&&m.orig&&m.titleFromAlt&&(h=m.orig.attr("alt")),e=m.href||(d.nodeName?a(d).attr("href"):d.href)||null,(/^(?:javascript)/i.test(e)||"#"==e)&&(e=null),m.type?(f=m.type,e||(e=m.content)):m.content?f="html":e&&(f=e.match(t)?"image":e.match(u)?"swf":a(d).hasClass("iframe")?"iframe":0===e.indexOf("#")?"inline":"ajax"),!f)return F(),void 0;switch("inline"==f&&(d=e.substr(e.indexOf("#")),f=a(d).length>0?"inline":"ajax"),m.type=f,m.href=e,m.title=h,m.autoDimensions&&("html"==m.type||"inline"==m.type||"ajax"==m.type?(m.width="auto",m.height="auto"):m.autoDimensions=!1),m.modal&&(m.overlayShow=!0,m.hideOnOverlayClick=!1,m.hideOnContentClick=!1,m.enableEscapeButton=!1,m.showCloseButton=!1),m.padding=parseInt(m.padding,10),m.margin=parseInt(m.margin,10),b.css("padding",m.padding+m.margin),a(".fancybox-inline-tmp").unbind("fancybox-cancel").bind("fancybox-change",function(){a(this).replaceWith(g.children())}),f){case"html":b.html(m.content),H();break;case"inline":if(a(d).parent().is("#fancybox-content")===!0)return B=!1,void 0;a('<div class="fancybox-inline-tmp" />').hide().insertBefore(a(d)).bind("fancybox-cleanup",function(){a(this).replaceWith(g.children())}).bind("fancybox-cancel",function(){a(this).replaceWith(b.children())}),a(d).appendTo(b),H();break;case"image":B=!1,a.fancybox.showActivity(),s=new Image,s.onerror=function(){F()},s.onload=function(){B=!0,s.onerror=s.onload=null,I()},s.src=e;break;case"swf":m.scrolling="no",i='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+m.width+'" height="'+m.height+'"><param name="movie" value="'+e+'"></param>',j="",a.each(m.swf,function(a,b){i+='<param name="'+a+'" value="'+b+'"></param>',j+=" "+a+'="'+b+'"'}),i+='<embed src="'+e+'" type="application/x-shockwave-flash" width="'+m.width+'" height="'+m.height+'"'+j+"></embed></object>",b.html(i),H();break;case"ajax":B=!1,a.fancybox.showActivity(),m.ajax.win=m.ajax.success,r=a.ajax(a.extend({},m.ajax,{url:e,data:m.ajax.data||{},error:function(a){a.status>0&&F()},success:function(a,d,f){var g="object"==typeof f?f:r;if(200==g.status){if("function"==typeof m.ajax.win){if(k=m.ajax.win(e,a,d,f),k===!1)return c.hide(),void 0;("string"==typeof k||"object"==typeof k)&&(a=k)}b.html(a),H()}}}));break;case"iframe":J()}},H=function(){var c=m.width,d=m.height;c=c.toString().indexOf("%")>-1?parseInt((a(window).width()-2*m.margin)*parseFloat(c)/100,10)+"px":"auto"==c?"auto":c+"px",d=d.toString().indexOf("%")>-1?parseInt((a(window).height()-2*m.margin)*parseFloat(d)/100,10)+"px":"auto"==d?"auto":d+"px",b.wrapInner('<div style="width:'+c+";height:"+d+";overflow: "+("auto"==m.scrolling?"auto":"yes"==m.scrolling?"scroll":"hidden")+';position:relative;"></div>'),m.width=b.width(),m.height=b.height(),J()},I=function(){m.width=s.width,m.height=s.height,a("<img />").attr({id:"fancybox-img",src:s.src,alt:m.title}).appendTo(b),J()},J=function(){var f,r;return c.hide(),e.is(":visible")&&!1===p.onCleanup(q,o,p)?(a.event.trigger("fancybox-cancel"),B=!1,void 0):(B=!0,a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),e.is(":visible")&&"outside"!==p.titlePosition&&e.css("height",e.height()),q=n,o=l,p=m,p.overlayShow?(d.css({"background-color":p.overlayColor,opacity:p.overlayOpacity,cursor:p.hideOnOverlayClick?"pointer":"auto",height:a(document).height()}),d.is(":visible")||(D&&a("select:not(#fancybox-tmp select)").filter(function(){return"hidden"!==this.style.visibility}).css({visibility:"hidden"}).one("fancybox-cleanup",function(){this.style.visibility="inherit"}),d.show())):d.hide(),A=R(),L(),e.is(":visible")?(a(h.add(j).add(k)).hide(),f=e.position(),z={top:f.top,left:f.left,width:e.width(),height:e.height()},r=z.width==A.width&&z.height==A.height,g.fadeTo(p.changeFade,.3,function(){var c=function(){g.html(b.contents()).fadeTo(p.changeFade,1,N)};a.event.trigger("fancybox-change"),g.empty().removeAttr("filter").css({"border-width":p.padding,width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}),r?c():(C.prop=0,a(C).animate({prop:1},{duration:p.changeSpeed,easing:p.easingChange,step:P,complete:c}))}),void 0):(e.removeAttr("style"),g.css("border-width",p.padding),"elastic"==p.transitionIn?(z=T(),g.html(b.contents()),e.show(),p.opacity&&(A.opacity=0),C.prop=0,a(C).animate({prop:1},{duration:p.speedIn,easing:p.easingIn,step:P,complete:N}),void 0):("inside"==p.titlePosition&&x>0&&i.show(),g.css({width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}).html(b.contents()),e.css(A).fadeIn("none"==p.transitionIn?0:p.speedIn,N),void 0)))},K=function(a){return a&&a.length?"float"==p.titlePosition?'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+a+'</td><td id="fancybox-title-float-right"></td></tr></table>':'<div id="fancybox-title-'+p.titlePosition+'">'+a+"</div>":!1},L=function(){if(y=p.title||"",x=0,i.empty().removeAttr("style").removeClass(),p.titleShow===!1)return i.hide(),void 0;if(y=a.isFunction(p.titleFormat)?p.titleFormat(y,q,o,p):K(y),!y||""===y)return i.hide(),void 0;switch(i.addClass("fancybox-title-"+p.titlePosition).html(y).appendTo("body").show(),p.titlePosition){case"inside":i.css({width:A.width-2*p.padding,marginLeft:p.padding,marginRight:p.padding}),x=i.outerHeight(!0),i.appendTo(f),A.height+=x;break;case"over":i.css({marginLeft:p.padding,width:A.width-2*p.padding,bottom:p.padding}).appendTo(f);break;case"float":i.css("left",-1*parseInt((i.width()-A.width-40)/2,10)).appendTo(e);break;default:i.css({width:A.width-2*p.padding,paddingLeft:p.padding,paddingRight:p.padding}).appendTo(e)}i.hide()},M=function(){return(p.enableEscapeButton||p.enableKeyboardNav)&&a(document).bind("keydown.fb",function(b){27==b.keyCode&&p.enableEscapeButton?(b.preventDefault(),a.fancybox.close()):37!=b.keyCode&&39!=b.keyCode||!p.enableKeyboardNav||"INPUT"===b.target.tagName||"TEXTAREA"===b.target.tagName||"SELECT"===b.target.tagName||(b.preventDefault(),a.fancybox[37==b.keyCode?"prev":"next"]())}),p.showNavArrows?((p.cyclic&&q.length>1||0!==o)&&j.show(),(p.cyclic&&q.length>1||o!=q.length-1)&&k.show(),void 0):(j.hide(),k.hide(),void 0)},N=function(){a.support.opacity||(g.get(0).style.removeAttribute("filter"),e.get(0).style.removeAttribute("filter")),m.autoDimensions&&g.css("height","auto"),e.css("height","auto"),y&&y.length&&i.show(),p.showCloseButton&&h.show(),M(),p.hideOnContentClick&&g.bind("click",a.fancybox.close),p.hideOnOverlayClick&&d.bind("click",a.fancybox.close),a(window).bind("resize.fb",a.fancybox.resize),p.centerOnScroll&&a(window).bind("scroll.fb",a.fancybox.center),"iframe"==p.type&&a('<iframe id="fancybox-frame" name="fancybox-frame'+(new Date).getTime()+'" frameborder="0" hspace="0" '+(a.browser.msie?'allowtransparency="true""':"")+' scrolling="'+m.scrolling+'" src="'+p.href+'"></iframe>').appendTo(g),e.show(),B=!1,a.fancybox.center(),p.onComplete(q,o,p),O()},O=function(){var a,b;q.length-1>o&&(a=q[o+1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a)),o>0&&(a=q[o-1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a))},P=function(a){var b={width:parseInt(z.width+(A.width-z.width)*a,10),height:parseInt(z.height+(A.height-z.height)*a,10),top:parseInt(z.top+(A.top-z.top)*a,10),left:parseInt(z.left+(A.left-z.left)*a,10)};"undefined"!=typeof A.opacity&&(b.opacity=.5>a?.5:a),e.css(b),g.css({width:b.width-2*p.padding,height:b.height-x*a-2*p.padding})},Q=function(){return[a(window).width()-2*p.margin,a(window).height()-2*p.margin,a(document).scrollLeft()+p.margin,a(document).scrollTop()+p.margin]},R=function(){var e,a=Q(),b={},c=p.autoScale,d=2*p.padding;return b.width=p.width.toString().indexOf("%")>-1?parseInt(a[0]*parseFloat(p.width)/100,10):p.width+d,b.height=p.height.toString().indexOf("%")>-1?parseInt(a[1]*parseFloat(p.height)/100,10):p.height+d,c&&(b.width>a[0]||b.height>a[1])&&("image"==m.type||"swf"==m.type?(e=p.width/p.height,b.width>a[0]&&(b.width=a[0],b.height=parseInt((b.width-d)/e+d,10)),b.height>a[1]&&(b.height=a[1],b.width=parseInt((b.height-d)*e+d,10))):(b.width=Math.min(b.width,a[0]),b.height=Math.min(b.height,a[1]))),b.top=parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-b.height-40)),10),b.left=parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-b.width-40)),10),b},S=function(a){var b=a.offset();return b.top+=parseInt(a.css("paddingTop"),10)||0,b.left+=parseInt(a.css("paddingLeft"),10)||0,b.top+=parseInt(a.css("border-top-width"),10)||0,b.left+=parseInt(a.css("border-left-width"),10)||0,b.width=a.width(),b.height=a.height(),b},T=function(){var d,e,b=m.orig?a(m.orig):!1,c={};return b&&b.length?(d=S(b),c={width:d.width+2*p.padding,height:d.height+2*p.padding,top:d.top-p.padding-20,left:d.left-p.padding-20}):(e=Q(),c={width:2*p.padding,height:2*p.padding,top:parseInt(e[3]+.5*e[1],10),left:parseInt(e[2]+.5*e[0],10)}),c},U=function(){return c.is(":visible")?(a("div",c).css("top",-40*w+"px"),w=(w+1)%12,void 0):(clearInterval(v),void 0)};a.fn.fancybox=function(b){return a(this).length?(a(this).data("fancybox",a.extend({},b,a.metadata?a(this).metadata():{})).unbind("click.fb").bind("click.fb",function(b){if(b.preventDefault(),!B){B=!0,a(this).blur(),n=[],l=0;var c=a(this).attr("rel")||"";c&&""!=c&&"nofollow"!==c?(n=a("a[rel="+c+"], area[rel="+c+"]"),l=n.index(this)):n.push(this),G()}}),this):this},a.fancybox=function(b){var c;if(!B){if(B=!0,c="undefined"!=typeof arguments[1]?arguments[1]:{},n=[],l=parseInt(c.index,10)||0,a.isArray(b)){for(var d=0,e=b.length;e>d;d++)"object"==typeof b[d]?a(b[d]).data("fancybox",a.extend({},c,b[d])):b[d]=a({}).data("fancybox",a.extend({content:b[d]},c));n=jQuery.merge(n,b)}else"object"==typeof b?a(b).data("fancybox",a.extend({},c,b)):b=a({}).data("fancybox",a.extend({content:b},c)),n.push(b);(l>n.length||0>l)&&(l=0),G()}},a.fancybox.showActivity=function(){clearInterval(v),c.show(),v=setInterval(U,66)},a.fancybox.hideActivity=function(){c.hide()},a.fancybox.next=function(){return a.fancybox.pos(o+1)},a.fancybox.prev=function(){return a.fancybox.pos(o-1)},a.fancybox.pos=function(a){B||(a=parseInt(a),n=q,a>-1&&a<q.length?(l=a,G()):p.cyclic&&q.length>1&&(l=a>=q.length?0:q.length-1,G()))},a.fancybox.cancel=function(){B||(B=!0,a.event.trigger("fancybox-cancel"),E(),m.onCancel(n,l,m),B=!1)},a.fancybox.close=function(){function b(){d.fadeOut("fast"),i.empty().hide(),e.hide(),a.event.trigger("fancybox-cleanup"),g.empty(),p.onClosed(q,o,p),q=m=[],o=l=0,p=m={},B=!1}if(!B&&!e.is(":hidden")){if(B=!0,p&&!1===p.onCleanup(q,o,p))return B=!1,void 0;if(E(),a(h.add(j).add(k)).hide(),a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),g.find("iframe").attr("src",D&&/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank"),"inside"!==p.titlePosition&&i.empty(),e.stop(),"elastic"==p.transitionOut){z=T();var c=e.position();A={top:c.top,left:c.left,width:e.width(),height:e.height()},p.opacity&&(A.opacity=1),i.empty().hide(),C.prop=1,a(C).animate({prop:0},{duration:p.speedOut,easing:p.easingOut,step:P,complete:b})}else e.fadeOut("none"==p.transitionOut?0:p.speedOut,b)}},a.fancybox.resize=function(){d.is(":visible")&&d.css("height",a(document).height()),a.fancybox.center(!0)},a.fancybox.center=function(){var a,b;B||(b=arguments[0]===!0?1:0,a=Q(),(b||!(e.width()>a[0]||e.height()>a[1]))&&e.stop().animate({top:parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-g.height()-40)-p.padding)),left:parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-g.width()-40)-p.padding))},"number"==typeof arguments[0]?arguments[0]:200))},a.fancybox.init=function(){a("#fancybox-wrap").length||(a("body").append(b=a('<div id="fancybox-tmp"></div>'),c=a('<div id="fancybox-loading"><div></div></div>'),d=a('<div id="fancybox-overlay"></div>'),e=a('<div id="fancybox-wrap"></div>')),f=a('<div id="fancybox-outer"></div>').append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>').appendTo(e),f.append(g=a('<div id="fancybox-content"></div>'),h=a('<a id="fancybox-close"></a>'),i=a('<div id="fancybox-title"></div>'),j=a('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),k=a('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>')),h.click(a.fancybox.close),c.click(a.fancybox.cancel),j.click(function(b){b.preventDefault(),a.fancybox.prev()}),k.click(function(b){b.preventDefault(),a.fancybox.next()}),a.fn.mousewheel&&e.bind("mousewheel.fb",function(b,c){B?b.preventDefault():(0==a(b.target).get(0).clientHeight||a(b.target).get(0).scrollHeight===a(b.target).get(0).clientHeight)&&(b.preventDefault(),a.fancybox[c>0?"prev":"next"]())}),a.support.opacity||e.addClass("fancybox-ie"),D&&(c.addClass("fancybox-ie6"),e.addClass("fancybox-ie6"),a('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank")+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(f)))},a.fn.fancybox.defaults={padding:10,margin:40,opacity:!1,modal:!1,cyclic:!1,scrolling:"auto",width:560,height:340,autoScale:!0,autoDimensions:!0,centerOnScroll:!1,ajax:{},swf:{wmode:"transparent"},hideOnOverlayClick:!0,hideOnContentClick:!1,overlayShow:!0,overlayOpacity:.7,overlayColor:"#777",titleShow:!0,titlePosition:"float",titleFormat:null,titleFromAlt:!1,transitionIn:"fade",transitionOut:"fade",speedIn:300,speedOut:300,changeSpeed:300,changeFade:"fast",easingIn:"swing",easingOut:"swing",showCloseButton:!0,showNavArrows:!0,enableEscapeButton:!0,enableKeyboardNav:!0,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}},a(document).ready(function(){a.fancybox.init()})}(jQuery);;//     Underscore.js 1.7.0
//     http://underscorejs.org
//     (c) 2009-2014 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
(function(){var n=this,t=n._,r=Array.prototype,e=Object.prototype,u=Function.prototype,i=r.push,a=r.slice,o=r.concat,l=e.toString,c=e.hasOwnProperty,f=Array.isArray,s=Object.keys,p=u.bind,h=function(n){return n instanceof h?n:this instanceof h?void(this._wrapped=n):new h(n)};"undefined"!=typeof exports?("undefined"!=typeof module&&module.exports&&(exports=module.exports=h),exports._=h):n._=h,h.VERSION="1.7.0";var g=function(n,t,r){if(t===void 0)return n;switch(null==r?3:r){case 1:return function(r){return n.call(t,r)};case 2:return function(r,e){return n.call(t,r,e)};case 3:return function(r,e,u){return n.call(t,r,e,u)};case 4:return function(r,e,u,i){return n.call(t,r,e,u,i)}}return function(){return n.apply(t,arguments)}};h.iteratee=function(n,t,r){return null==n?h.identity:h.isFunction(n)?g(n,t,r):h.isObject(n)?h.matches(n):h.property(n)},h.each=h.forEach=function(n,t,r){if(null==n)return n;t=g(t,r);var e,u=n.length;if(u===+u)for(e=0;u>e;e++)t(n[e],e,n);else{var i=h.keys(n);for(e=0,u=i.length;u>e;e++)t(n[i[e]],i[e],n)}return n},h.map=h.collect=function(n,t,r){if(null==n)return[];t=h.iteratee(t,r);for(var e,u=n.length!==+n.length&&h.keys(n),i=(u||n).length,a=Array(i),o=0;i>o;o++)e=u?u[o]:o,a[o]=t(n[e],e,n);return a};var v="Reduce of empty array with no initial value";h.reduce=h.foldl=h.inject=function(n,t,r,e){null==n&&(n=[]),t=g(t,e,4);var u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length,o=0;if(arguments.length<3){if(!a)throw new TypeError(v);r=n[i?i[o++]:o++]}for(;a>o;o++)u=i?i[o]:o,r=t(r,n[u],u,n);return r},h.reduceRight=h.foldr=function(n,t,r,e){null==n&&(n=[]),t=g(t,e,4);var u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;if(arguments.length<3){if(!a)throw new TypeError(v);r=n[i?i[--a]:--a]}for(;a--;)u=i?i[a]:a,r=t(r,n[u],u,n);return r},h.find=h.detect=function(n,t,r){var e;return t=h.iteratee(t,r),h.some(n,function(n,r,u){return t(n,r,u)?(e=n,!0):void 0}),e},h.filter=h.select=function(n,t,r){var e=[];return null==n?e:(t=h.iteratee(t,r),h.each(n,function(n,r,u){t(n,r,u)&&e.push(n)}),e)},h.reject=function(n,t,r){return h.filter(n,h.negate(h.iteratee(t)),r)},h.every=h.all=function(n,t,r){if(null==n)return!0;t=h.iteratee(t,r);var e,u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;for(e=0;a>e;e++)if(u=i?i[e]:e,!t(n[u],u,n))return!1;return!0},h.some=h.any=function(n,t,r){if(null==n)return!1;t=h.iteratee(t,r);var e,u,i=n.length!==+n.length&&h.keys(n),a=(i||n).length;for(e=0;a>e;e++)if(u=i?i[e]:e,t(n[u],u,n))return!0;return!1},h.contains=h.include=function(n,t){return null==n?!1:(n.length!==+n.length&&(n=h.values(n)),h.indexOf(n,t)>=0)},h.invoke=function(n,t){var r=a.call(arguments,2),e=h.isFunction(t);return h.map(n,function(n){return(e?t:n[t]).apply(n,r)})},h.pluck=function(n,t){return h.map(n,h.property(t))},h.where=function(n,t){return h.filter(n,h.matches(t))},h.findWhere=function(n,t){return h.find(n,h.matches(t))},h.max=function(n,t,r){var e,u,i=-1/0,a=-1/0;if(null==t&&null!=n){n=n.length===+n.length?n:h.values(n);for(var o=0,l=n.length;l>o;o++)e=n[o],e>i&&(i=e)}else t=h.iteratee(t,r),h.each(n,function(n,r,e){u=t(n,r,e),(u>a||u===-1/0&&i===-1/0)&&(i=n,a=u)});return i},h.min=function(n,t,r){var e,u,i=1/0,a=1/0;if(null==t&&null!=n){n=n.length===+n.length?n:h.values(n);for(var o=0,l=n.length;l>o;o++)e=n[o],i>e&&(i=e)}else t=h.iteratee(t,r),h.each(n,function(n,r,e){u=t(n,r,e),(a>u||1/0===u&&1/0===i)&&(i=n,a=u)});return i},h.shuffle=function(n){for(var t,r=n&&n.length===+n.length?n:h.values(n),e=r.length,u=Array(e),i=0;e>i;i++)t=h.random(0,i),t!==i&&(u[i]=u[t]),u[t]=r[i];return u},h.sample=function(n,t,r){return null==t||r?(n.length!==+n.length&&(n=h.values(n)),n[h.random(n.length-1)]):h.shuffle(n).slice(0,Math.max(0,t))},h.sortBy=function(n,t,r){return t=h.iteratee(t,r),h.pluck(h.map(n,function(n,r,e){return{value:n,index:r,criteria:t(n,r,e)}}).sort(function(n,t){var r=n.criteria,e=t.criteria;if(r!==e){if(r>e||r===void 0)return 1;if(e>r||e===void 0)return-1}return n.index-t.index}),"value")};var m=function(n){return function(t,r,e){var u={};return r=h.iteratee(r,e),h.each(t,function(e,i){var a=r(e,i,t);n(u,e,a)}),u}};h.groupBy=m(function(n,t,r){h.has(n,r)?n[r].push(t):n[r]=[t]}),h.indexBy=m(function(n,t,r){n[r]=t}),h.countBy=m(function(n,t,r){h.has(n,r)?n[r]++:n[r]=1}),h.sortedIndex=function(n,t,r,e){r=h.iteratee(r,e,1);for(var u=r(t),i=0,a=n.length;a>i;){var o=i+a>>>1;r(n[o])<u?i=o+1:a=o}return i},h.toArray=function(n){return n?h.isArray(n)?a.call(n):n.length===+n.length?h.map(n,h.identity):h.values(n):[]},h.size=function(n){return null==n?0:n.length===+n.length?n.length:h.keys(n).length},h.partition=function(n,t,r){t=h.iteratee(t,r);var e=[],u=[];return h.each(n,function(n,r,i){(t(n,r,i)?e:u).push(n)}),[e,u]},h.first=h.head=h.take=function(n,t,r){return null==n?void 0:null==t||r?n[0]:0>t?[]:a.call(n,0,t)},h.initial=function(n,t,r){return a.call(n,0,Math.max(0,n.length-(null==t||r?1:t)))},h.last=function(n,t,r){return null==n?void 0:null==t||r?n[n.length-1]:a.call(n,Math.max(n.length-t,0))},h.rest=h.tail=h.drop=function(n,t,r){return a.call(n,null==t||r?1:t)},h.compact=function(n){return h.filter(n,h.identity)};var y=function(n,t,r,e){if(t&&h.every(n,h.isArray))return o.apply(e,n);for(var u=0,a=n.length;a>u;u++){var l=n[u];h.isArray(l)||h.isArguments(l)?t?i.apply(e,l):y(l,t,r,e):r||e.push(l)}return e};h.flatten=function(n,t){return y(n,t,!1,[])},h.without=function(n){return h.difference(n,a.call(arguments,1))},h.uniq=h.unique=function(n,t,r,e){if(null==n)return[];h.isBoolean(t)||(e=r,r=t,t=!1),null!=r&&(r=h.iteratee(r,e));for(var u=[],i=[],a=0,o=n.length;o>a;a++){var l=n[a];if(t)a&&i===l||u.push(l),i=l;else if(r){var c=r(l,a,n);h.indexOf(i,c)<0&&(i.push(c),u.push(l))}else h.indexOf(u,l)<0&&u.push(l)}return u},h.union=function(){return h.uniq(y(arguments,!0,!0,[]))},h.intersection=function(n){if(null==n)return[];for(var t=[],r=arguments.length,e=0,u=n.length;u>e;e++){var i=n[e];if(!h.contains(t,i)){for(var a=1;r>a&&h.contains(arguments[a],i);a++);a===r&&t.push(i)}}return t},h.difference=function(n){var t=y(a.call(arguments,1),!0,!0,[]);return h.filter(n,function(n){return!h.contains(t,n)})},h.zip=function(n){if(null==n)return[];for(var t=h.max(arguments,"length").length,r=Array(t),e=0;t>e;e++)r[e]=h.pluck(arguments,e);return r},h.object=function(n,t){if(null==n)return{};for(var r={},e=0,u=n.length;u>e;e++)t?r[n[e]]=t[e]:r[n[e][0]]=n[e][1];return r},h.indexOf=function(n,t,r){if(null==n)return-1;var e=0,u=n.length;if(r){if("number"!=typeof r)return e=h.sortedIndex(n,t),n[e]===t?e:-1;e=0>r?Math.max(0,u+r):r}for(;u>e;e++)if(n[e]===t)return e;return-1},h.lastIndexOf=function(n,t,r){if(null==n)return-1;var e=n.length;for("number"==typeof r&&(e=0>r?e+r+1:Math.min(e,r+1));--e>=0;)if(n[e]===t)return e;return-1},h.range=function(n,t,r){arguments.length<=1&&(t=n||0,n=0),r=r||1;for(var e=Math.max(Math.ceil((t-n)/r),0),u=Array(e),i=0;e>i;i++,n+=r)u[i]=n;return u};var d=function(){};h.bind=function(n,t){var r,e;if(p&&n.bind===p)return p.apply(n,a.call(arguments,1));if(!h.isFunction(n))throw new TypeError("Bind must be called on a function");return r=a.call(arguments,2),e=function(){if(!(this instanceof e))return n.apply(t,r.concat(a.call(arguments)));d.prototype=n.prototype;var u=new d;d.prototype=null;var i=n.apply(u,r.concat(a.call(arguments)));return h.isObject(i)?i:u}},h.partial=function(n){var t=a.call(arguments,1);return function(){for(var r=0,e=t.slice(),u=0,i=e.length;i>u;u++)e[u]===h&&(e[u]=arguments[r++]);for(;r<arguments.length;)e.push(arguments[r++]);return n.apply(this,e)}},h.bindAll=function(n){var t,r,e=arguments.length;if(1>=e)throw new Error("bindAll must be passed function names");for(t=1;e>t;t++)r=arguments[t],n[r]=h.bind(n[r],n);return n},h.memoize=function(n,t){var r=function(e){var u=r.cache,i=t?t.apply(this,arguments):e;return h.has(u,i)||(u[i]=n.apply(this,arguments)),u[i]};return r.cache={},r},h.delay=function(n,t){var r=a.call(arguments,2);return setTimeout(function(){return n.apply(null,r)},t)},h.defer=function(n){return h.delay.apply(h,[n,1].concat(a.call(arguments,1)))},h.throttle=function(n,t,r){var e,u,i,a=null,o=0;r||(r={});var l=function(){o=r.leading===!1?0:h.now(),a=null,i=n.apply(e,u),a||(e=u=null)};return function(){var c=h.now();o||r.leading!==!1||(o=c);var f=t-(c-o);return e=this,u=arguments,0>=f||f>t?(clearTimeout(a),a=null,o=c,i=n.apply(e,u),a||(e=u=null)):a||r.trailing===!1||(a=setTimeout(l,f)),i}},h.debounce=function(n,t,r){var e,u,i,a,o,l=function(){var c=h.now()-a;t>c&&c>0?e=setTimeout(l,t-c):(e=null,r||(o=n.apply(i,u),e||(i=u=null)))};return function(){i=this,u=arguments,a=h.now();var c=r&&!e;return e||(e=setTimeout(l,t)),c&&(o=n.apply(i,u),i=u=null),o}},h.wrap=function(n,t){return h.partial(t,n)},h.negate=function(n){return function(){return!n.apply(this,arguments)}},h.compose=function(){var n=arguments,t=n.length-1;return function(){for(var r=t,e=n[t].apply(this,arguments);r--;)e=n[r].call(this,e);return e}},h.after=function(n,t){return function(){return--n<1?t.apply(this,arguments):void 0}},h.before=function(n,t){var r;return function(){return--n>0?r=t.apply(this,arguments):t=null,r}},h.once=h.partial(h.before,2),h.keys=function(n){if(!h.isObject(n))return[];if(s)return s(n);var t=[];for(var r in n)h.has(n,r)&&t.push(r);return t},h.values=function(n){for(var t=h.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=n[t[u]];return e},h.pairs=function(n){for(var t=h.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=[t[u],n[t[u]]];return e},h.invert=function(n){for(var t={},r=h.keys(n),e=0,u=r.length;u>e;e++)t[n[r[e]]]=r[e];return t},h.functions=h.methods=function(n){var t=[];for(var r in n)h.isFunction(n[r])&&t.push(r);return t.sort()},h.extend=function(n){if(!h.isObject(n))return n;for(var t,r,e=1,u=arguments.length;u>e;e++){t=arguments[e];for(r in t)c.call(t,r)&&(n[r]=t[r])}return n},h.pick=function(n,t,r){var e,u={};if(null==n)return u;if(h.isFunction(t)){t=g(t,r);for(e in n){var i=n[e];t(i,e,n)&&(u[e]=i)}}else{var l=o.apply([],a.call(arguments,1));n=new Object(n);for(var c=0,f=l.length;f>c;c++)e=l[c],e in n&&(u[e]=n[e])}return u},h.omit=function(n,t,r){if(h.isFunction(t))t=h.negate(t);else{var e=h.map(o.apply([],a.call(arguments,1)),String);t=function(n,t){return!h.contains(e,t)}}return h.pick(n,t,r)},h.defaults=function(n){if(!h.isObject(n))return n;for(var t=1,r=arguments.length;r>t;t++){var e=arguments[t];for(var u in e)n[u]===void 0&&(n[u]=e[u])}return n},h.clone=function(n){return h.isObject(n)?h.isArray(n)?n.slice():h.extend({},n):n},h.tap=function(n,t){return t(n),n};var b=function(n,t,r,e){if(n===t)return 0!==n||1/n===1/t;if(null==n||null==t)return n===t;n instanceof h&&(n=n._wrapped),t instanceof h&&(t=t._wrapped);var u=l.call(n);if(u!==l.call(t))return!1;switch(u){case"[object RegExp]":case"[object String]":return""+n==""+t;case"[object Number]":return+n!==+n?+t!==+t:0===+n?1/+n===1/t:+n===+t;case"[object Date]":case"[object Boolean]":return+n===+t}if("object"!=typeof n||"object"!=typeof t)return!1;for(var i=r.length;i--;)if(r[i]===n)return e[i]===t;var a=n.constructor,o=t.constructor;if(a!==o&&"constructor"in n&&"constructor"in t&&!(h.isFunction(a)&&a instanceof a&&h.isFunction(o)&&o instanceof o))return!1;r.push(n),e.push(t);var c,f;if("[object Array]"===u){if(c=n.length,f=c===t.length)for(;c--&&(f=b(n[c],t[c],r,e)););}else{var s,p=h.keys(n);if(c=p.length,f=h.keys(t).length===c)for(;c--&&(s=p[c],f=h.has(t,s)&&b(n[s],t[s],r,e)););}return r.pop(),e.pop(),f};h.isEqual=function(n,t){return b(n,t,[],[])},h.isEmpty=function(n){if(null==n)return!0;if(h.isArray(n)||h.isString(n)||h.isArguments(n))return 0===n.length;for(var t in n)if(h.has(n,t))return!1;return!0},h.isElement=function(n){return!(!n||1!==n.nodeType)},h.isArray=f||function(n){return"[object Array]"===l.call(n)},h.isObject=function(n){var t=typeof n;return"function"===t||"object"===t&&!!n},h.each(["Arguments","Function","String","Number","Date","RegExp"],function(n){h["is"+n]=function(t){return l.call(t)==="[object "+n+"]"}}),h.isArguments(arguments)||(h.isArguments=function(n){return h.has(n,"callee")}),"function"!=typeof/./&&(h.isFunction=function(n){return"function"==typeof n||!1}),h.isFinite=function(n){return isFinite(n)&&!isNaN(parseFloat(n))},h.isNaN=function(n){return h.isNumber(n)&&n!==+n},h.isBoolean=function(n){return n===!0||n===!1||"[object Boolean]"===l.call(n)},h.isNull=function(n){return null===n},h.isUndefined=function(n){return n===void 0},h.has=function(n,t){return null!=n&&c.call(n,t)},h.noConflict=function(){return n._=t,this},h.identity=function(n){return n},h.constant=function(n){return function(){return n}},h.noop=function(){},h.property=function(n){return function(t){return t[n]}},h.matches=function(n){var t=h.pairs(n),r=t.length;return function(n){if(null==n)return!r;n=new Object(n);for(var e=0;r>e;e++){var u=t[e],i=u[0];if(u[1]!==n[i]||!(i in n))return!1}return!0}},h.times=function(n,t,r){var e=Array(Math.max(0,n));t=g(t,r,1);for(var u=0;n>u;u++)e[u]=t(u);return e},h.random=function(n,t){return null==t&&(t=n,n=0),n+Math.floor(Math.random()*(t-n+1))},h.now=Date.now||function(){return(new Date).getTime()};var _={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},w=h.invert(_),j=function(n){var t=function(t){return n[t]},r="(?:"+h.keys(n).join("|")+")",e=RegExp(r),u=RegExp(r,"g");return function(n){return n=null==n?"":""+n,e.test(n)?n.replace(u,t):n}};h.escape=j(_),h.unescape=j(w),h.result=function(n,t){if(null==n)return void 0;var r=n[t];return h.isFunction(r)?n[t]():r};var x=0;h.uniqueId=function(n){var t=++x+"";return n?n+t:t},h.templateSettings={evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g};var A=/(.)^/,k={"'":"'","\\":"\\","\r":"r","\n":"n","\u2028":"u2028","\u2029":"u2029"},O=/\\|'|\r|\n|\u2028|\u2029/g,F=function(n){return"\\"+k[n]};h.template=function(n,t,r){!t&&r&&(t=r),t=h.defaults({},t,h.templateSettings);var e=RegExp([(t.escape||A).source,(t.interpolate||A).source,(t.evaluate||A).source].join("|")+"|$","g"),u=0,i="__p+='";n.replace(e,function(t,r,e,a,o){return i+=n.slice(u,o).replace(O,F),u=o+t.length,r?i+="'+\n((__t=("+r+"))==null?'':_.escape(__t))+\n'":e?i+="'+\n((__t=("+e+"))==null?'':__t)+\n'":a&&(i+="';\n"+a+"\n__p+='"),t}),i+="';\n",t.variable||(i="with(obj||{}){\n"+i+"}\n"),i="var __t,__p='',__j=Array.prototype.join,"+"print=function(){__p+=__j.call(arguments,'');};\n"+i+"return __p;\n";try{var a=new Function(t.variable||"obj","_",i)}catch(o){throw o.source=i,o}var l=function(n){return a.call(this,n,h)},c=t.variable||"obj";return l.source="function("+c+"){\n"+i+"}",l},h.chain=function(n){var t=h(n);return t._chain=!0,t};var E=function(n){return this._chain?h(n).chain():n};h.mixin=function(n){h.each(h.functions(n),function(t){var r=h[t]=n[t];h.prototype[t]=function(){var n=[this._wrapped];return i.apply(n,arguments),E.call(this,r.apply(h,n))}})},h.mixin(h),h.each(["pop","push","reverse","shift","sort","splice","unshift"],function(n){var t=r[n];h.prototype[n]=function(){var r=this._wrapped;return t.apply(r,arguments),"shift"!==n&&"splice"!==n||0!==r.length||delete r[0],E.call(this,r)}}),h.each(["concat","join","slice"],function(n){var t=r[n];h.prototype[n]=function(){return E.call(this,t.apply(this._wrapped,arguments))}}),h.prototype.value=function(){return this._wrapped},"function"==typeof define&&define.amd&&define("underscore",[],function(){return h})}).call(this);
;//Target the first letter of the first element found in the wrapper
;(function ( $, window, document, undefined ) {
    //defaults
    var pluginName = 'addDropCap',
        defaults = {
            wrapper : ".entry-content",
            minwords : 50,
            skipSelectors : { //defines the selector to skip when parsing the wrapper
              tags : ['IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE'],
              classes : [],
              ids : []
            },
            skipChildTags : ['iframe']//skip those tags if they are direct children of an element
        };

    function Plugin( element, options ) {
      this.element = element;
      this.options = $.extend( {}, defaults, options) ;
      this._defaults = defaults;
      this._name = pluginName;
      this.init();
    }

    //can access this.element and this.option
    Plugin.prototype.init = function () {
      var $_target = this._get_dropcap_el();
      //if there's text and enough words, then apply a drop cap
      if ( $_target && this.options.minwords <= this._countWords( $_target.text() ) )
        this._may_be_add_dc( $_target );
    };


    //@return : $ element or false
    //recursive function. parse this.wrapper to find the first eligible element with text
    Plugin.prototype._get_dropcap_el = function( _requested_el ) {
      var $_first_el      = _requested_el || $( this.options.wrapper ).find( this.element ).first(),
          _first_el_text  = this._get_real_text( $_first_el.text() );

      if ( ! this._is_authorized( $_first_el ) && $_first_el.next().length )
        return this._get_dropcap_el( $_first_el.next() );
      else if ( this._is_authorized( $_first_el ) && _first_el_text )
        return $_first_el;
      else if ( $_first_el.next().length )
        return this._get_dropcap_el( $_first_el.next() );
      //get me out of here
      return;
    };

    //@return : string
    //clean spaces and special characters
    Plugin.prototype._get_real_text = function( _text ) {
      _text.replace(/&nbsp;/g, '').replace(/ /g, '');
      return this._removeSpecChars( _text );
    };

    //@return :boolean
    //check if the selector is in the 'to skip' list
    Plugin.prototype._is_authorized = function( $_el ) {
      //check if underscore is loaded first
      if ( 'function' != typeof(_) )
        return true;

      if ( ! $_el[0] || ! $_el[0].tagName )
        return;

      //is first child tag allowed ?
      if ( _.isArray(this.options.skipChildTags) && $_el.children().first().length && -1 != _.indexOf( _.map( this.options.skipChildTags , function(_tag) { return _tag.toUpperCase(); } ), $_el.children().first()[0].tagName ) )
        return;
      //is tag allowed ?
      if ( _.isArray(this.options.skipSelectors.tags) && -1 != _.indexOf( _.map( this.options.skipSelectors.tags , function(_tag) { return _tag.toUpperCase(); } ), $_el[0].tagName ) )
        return;
      //is class allowed ?
      if ( _.isArray(this.options.skipSelectors.classes) && $_el.attr('class') && 0 !== _.intersection( $_el.attr('class').split(' '), this.options.skipSelectors.classes ).length )
        return;
      //is id allowed ?
      if ( _.isArray(this.options.skipSelectors.classes) && $_el.attr('id') && -1 != $.inArray( $_el.attr('id').split(' ') , this.options.skipSelectors.ids ) )
        return;

      return true;
    };

    //@return : void
    //at this stage, the target has text, no need to check it
    Plugin.prototype._may_be_add_dc = function( $_target ) {
      var _first_el_text    = $_target.text(),
          _first_word       = '',
          _split_text       = $_target.text().replace(/ /g , '&nbsp;').split('&nbsp;');
      if ( ! _.isArray(_split_text) )
        return;

      //get the first word => it can't be a space due to previous treatment
      if ( _split_text.length )
        _first_word = _split_text[0];

      //cClean it
      _first_word = this._removeSpecChars( _first_word );

      if ( ! _first_word.charAt(0) )
        return;

      var _first_letter     = _first_word.charAt(0),
          _rest_of_word     = _first_word.substr(1),
          _drop_capped      = '',
          _html             = '';

      _first_letter = ['<span class="tc-dropcap">' , _first_letter, '</span>'].join('');
      _drop_capped = [ _first_letter , _rest_of_word ].join( '' );

      //replace the first occurence only
      _html = $_target.html().replace( _first_word , _drop_capped );

      //write
      $_target.html(_html);
    };


    /********
    * HELPERS
    *********/
    //@return : number
    Plugin.prototype._countWords = function( _expr ) {
      if ( 'string' != typeof( _expr ) )
        return 0;
      _expr = _expr.replace('&nbsp;' , ' ');
      return (_expr.split(' ')).length;
    };

    //@return : string
    Plugin.prototype._removeSpecChars = function( _expr , _replaceBy ) {
      _replaceBy = _replaceBy || '';
      return 'string' == typeof(_expr) ? _expr.replace(/[^\w]/g, _replaceBy ) : '';
    };

    //@return : string or false
    Plugin.prototype._stripHtmlTags = function( expr ) {
      return ( expr && 'string' == typeof(expr) ) ? expr.replace(/(<([^>]+)>)/ig,"") : false;
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

})( jQuery, window, document );;/* ===================================================
 * jqueryimgSmartLoad.js v1.0.0
 * ===================================================
 *
 * Replace all img src placeholder in the $element by the real src on scroll window event
 * Bind a 'smartload' event on each transformed img
 *
 * Note : the data-src attr has to be pre-processed before the actual page load
 * Example of regex to pre-process img server side with php :
 * preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', 'regex_callback' , $_html)
 *
 *
 * Example of gif 1px x 1px placeholder :
 * 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
 *
 * inspired by the work of Luís Almeida
 * http://luis-almeida.github.com/unveil
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  //defaults
  var pluginName = 'imgSmartLoad',
      defaults = {
        load_all_images_on_first_scroll : false,
        attribute : 'data-src',
        threshold : 200,
        fadeIn_options : {}
      };


  function Plugin( element, options ) {
    this.element = element;
    this.options = $.extend( {}, defaults, options) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }


  //can access this.element and this.option
  Plugin.prototype.init = function () {
    var self        = this,
        $_imgs   = $( 'img[' + this.options.attribute + ']' , this.element );
    this.increment  = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
    this.timer      = 0;

    //attach action to the load event
    $_imgs.bind( 'load_img', {}, function() { self._load_img(this); });

    $(window).scroll( function( _evt ) { self._better_scroll_event_handler( $_imgs, _evt ); });
    $(window).resize( function( _evt ) { self._maybe_trigger_load( $_imgs, _evt ); });
    //on load
    this._maybe_trigger_load( $_imgs );
  };


  /*
  * @param : array of $img
  * @param : current event
  * @return : void
  * scroll event performance enhancer => avoid browser stack if too much scrolls
  */
  Plugin.prototype._better_scroll_event_handler = function( $_imgs , _evt ) {
    var self = this;
    //use a timer
    if ( 0 !== this.timer ) {
        this.increment++;
        window.clearTimeout( this.timer );
    }

    this.timer = window.setTimeout(function() {
      self._maybe_trigger_load( $_imgs , _evt );
    }, self.increment > 5 ? 50 : 0 );
  };


  /*
  * @param : array of $img
  * @param : current event
  * @return : void
  */
  Plugin.prototype._maybe_trigger_load = function( $_imgs , _evt ) {
    var self = this;
        //get the visible images list
        _visible_list = $_imgs.filter( function( ind, _img ) { return self._is_visible( _img ,  _evt ); } );
    //trigger load_img event for visible images
    _visible_list.map( function( ind, _img ) { $(_img).trigger( 'load_img' );  } );
  };


  /*
  * @param single $img object
  * @param : current event
  * @return bool
  * helper to check if an image is the visible ( viewport + custom option threshold)
  */
  Plugin.prototype._is_visible = function( _img, _evt ) {
    var $_img       = $(_img),
        wt = $(window).scrollTop(),
        wb = wt + $(window).height(),
        it  = $_img.offset().top,
        ib  = it + $_img.height(),
        th = this.options.threshold;

    //force all images to visible if first scroll option enabled
    if ( _evt && 'scroll' == _evt.type && this.options.load_all_images_on_first_scroll )
      return true;

    return ib >= wt - th && it <= wb + th;
  };


  /*
  * @param single $img object
  * @return void
  * replace src place holder by data-src attr val which should include the real src
  */
  Plugin.prototype._load_img = function( _img ) {
    var $_img = $(_img),
        _src  = $_img.attr( this.options.attribute );

    $_img.unbind('load_img')
    .hide()
    .removeAttr( this.options.attribute )
    .attr('src' , _src )
    .fadeIn( this.options.fadeIn_options )
    .trigger('smartload');
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
})( jQuery, window, document );;/* !
 * Customizr WordPress theme Javascript code
 * Copyright (c) 2014 Nicolas GUILLAUME (@nicguillaume), Themes & Co.
 * GPL2+ Licensed
*/
//ON DOM READY
jQuery(function ($) {
    var _p = TCParams;
    //Img Smart Load
    if ( 1 == _p.imgSmartLoadEnabled ) {
      $( '.hentry' ).imgSmartLoad( _.size( _p.imgSmartLoadOpts) > 0 || {} );
    }

    //Drop Caps
    if ( _p.dropcapEnabled && 'object' == typeof( _p.dropcapWhere ) ) {
      $.each( _p.dropcapWhere , function( ind, val ) {
        if ( 1 == val ) {
          $( '.entry-content' , 'body.' + ( 'page' == ind ? 'page' : 'single-post' ) ).children().first().addDropCap( {
            minwords : _p.dropcapMinWords,//@todo check if number
            skipSelectors : _.isObject(_p.skipSelectors) ? _p.skipSelectors : {}
          });
        }
      });
    }


    //May be add (check if activated by user) external class + target="_blank" to relevant links
    //images are excluded
    var _url_comp     = (location.host).split('.'),
      _nakedDomain  = new RegExp( _url_comp[1] + "." + _url_comp[2] );

    function _is_external( _href  ) {
      var _thisHref = $.trim( _href );
      if ( _thisHref !== '' && _thisHref != '#' && _isValidURL(_thisHref) )
          return ! _nakedDomain.test(_thisHref) ? true : false;
    }

    function _isValidURL(url){
        var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        if (pattern.test(url)){
            return true;
        }
        return false;
    }
    //links inside post/page content
    $('a' , '.entry-content').each( function() {
      var _thisHref = $.trim( $(this).attr('href'));
      if( _is_external( _thisHref ) && 'IMG' != $(this).children().first().prop("tagName") ) {
        if ( _p.extLinksStyle )
          $(this).after('<span class="tc-external">');
        if ( _p.extLinksTargetExt )
          $(this).attr('target' , '_blank');
      }
    });


    //fancybox with localized script variables
    var b = _p.FancyBoxState,
        c = _p.FancyBoxAutoscale;
    if ( 1 == b ) {
            $("a.grouped_elements").fancybox({
            transitionOut: "elastic",
            transitionIn: "elastic",
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
    var d = _p.SliderName,
        e = _p.SliderDelay;
        j = _p.SliderHover;

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
    var SmoothScroll = _p.SmoothScroll;

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
    function g($) {
        return ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
    }
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


    //DISPLAY BACK TO TOP BUTTON ON SCROLL
    function btt_scrolling_actions() {
      if ( $(window).scrollTop() > 100 )
        $('.tc-btt-wrapper').addClass('show');
      else
        $('.tc-btt-wrapper').removeClass('show');
    }
    //use of a timer instead of attaching handler directly to the window scroll event
    //@uses _p.timerOnScrollAllBrowsers : boolean set to true by default
    //http://ejohn.org/blog/learning-from-twitter/
    //https://dannyvankooten.com/delay-scroll-handlers-javascript/
    var btt_timer,
        btt_increment = 1,//used to wait a little bit after the first user scroll actions to trigger the timer
        btt_triggerHeight = 20; //0.5 * windowHeight;

    $(window).scroll(function() {
      if ( btt_timer) {
          btt_increment++;
          window.clearTimeout(btt_timer);
      }
      if ( 1 == _p.timerOnScrollAllBrowsers ) {
          btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      } else if ( $('body').hasClass('ie') ) {
           btt_timer = window.setTimeout(function() {
              btt_scrolling_actions();
           }, btt_increment > 5 ? 50 : 0 );
      }
    });//end of window.scroll()



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
    if ( _p.HasComments ) {
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
    var LeftSidebarClass    = _p.LeftSidebarClass || '.span3.left.tc-sidebar',
        RightSidebarClass   = _p.RightSidebarClass || '.span3.right.tc-sidebar',
        wrapper             = $('#main-wrapper .container[role=main] > .column-content-wrapper'),
        content             = $("#main-wrapper .container .article-container"),
        left                = $("#main-wrapper .container " + LeftSidebarClass),
        right               = $("#main-wrapper .container " + RightSidebarClass),
        reordered           = false;

    function BlockPositions() {
        //15 pixels adjustement to avoid replacement before real responsive width
        WindowWidth = $(window).width();
        if ( WindowWidth > 767 - 15 && reordered ) {
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
            reordered = false; //this could stay in both if blocks instead
        } else if ( ( WindowWidth <= 767 - 15 ) && ! reordered ) {
            if ( $(left).length ) {
                 $(left).detach();
                $(content).detach();
                $(wrapper).append($(content)).append( $(left) );
            }
            if ( $(right).length ) {
                $(right).detach();
                $(wrapper).append($(right));
            }
            reordered = true; //this could stay in both if blocks instead
        }
    }//end function

    //Enable reordering if option is checked in the customizer.
    if ( 1 == _p.ReorderBlocks ) {
        //trigger the block positioning only when responsive
        WindowWidth = $(window).width();
        if ( WindowWidth <= 767 - 15 && ! reordered ) {
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
                container_height    = $(container).height(),
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
    if ( 1 == _p.CenterSlides ) {
        //adds a specific class to the carousel when automatic centering is enabled
        $('#customizr-slider .carousel-inner').addClass('center-slides-enabled');

        setTimeout( function() {
            centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image img' );
            $('.tc-slider-loader-wrapper').hide();
        } , 50);

        $(window).resize(function(){
            setTimeout( function() {
                centerImageInContainer( '.carousel .carousel-inner' , '.carousel .item .carousel-image img' );
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

        //prevent propagation event from sensible children
        $(".carousel input, .carousel button, .carousel textarea, .carousel select, .carousel a").
            on("touchstart touchmove", function(ev) {
                ev.stopPropagation();
        });

        $('.carousel' ).each( function() {
            $(this).hammer().on('swipeleft tap', function() {
                $(this).carousel('next');
            });
            $(this).hammer().on('swiperight', function(){
                $(this).carousel('prev');
            });
        });
    }

    //Handle dropdown on click for multi-tier menus
    var $dropdown_ahrefs    = $('.tc-open-on-click .menu-item.menu-item-has-children > a[href!="#"]'),
        $dropdown_submenus  = $('.tc-open-on-click .dropdown .dropdown-submenu');


    // go to the link if submenu is already opened
    $dropdown_ahrefs.on('tap click', function(evt) {
        if ( ( $(this).next('.dropdown-menu').css('visibility') != 'hidden' &&
                $(this).next('.dropdown-menu').is(':visible')  &&
                ! $(this).parent().hasClass('dropdown-submenu') ) ||
             ( $(this).next('.dropdown-menu').is(':visible') &&
                $(this).parent().hasClass('dropdown-submenu') ) )
            window.location = $(this).attr('href');
    });
    // make sub-submenus dropdown on click work
    $dropdown_submenus.each(function(){
        var $parent = $(this),
            $children = $parent.children('[data-toggle="dropdown"]');
        $children.on('tap click', function(){
            var submenu   = $(this).next('.dropdown-menu'),
                openthis  = false;
            if ( ! $parent.hasClass('open') ) {
              openthis = true;
            }
            // close opened submenus
            $($parent.parent()).children('.dropdown-submenu').each(function(){
                $(this).removeClass('open');
            });
            if ( openthis )
                $parent.addClass('open');

            return false;
        });
    });
});


/* Sticky header since v3.2.0 */
jQuery(function ($) {
    var   _p              = TCParams,
          $tcHeader       = $('.tc-header'),
          elToHide        = [], //[ '.social-block' , '.site-description' ],
          isUserLogged    = $('body').hasClass('logged-in') || 0 !== $('#wpadminbar').length,
          isCustomizing   = $('body').hasClass('is-customizing'),
          customOffset    = +_p.stickyCustomOffset,
          logosH     = [],
          logosW      = [],
          logosRatio      = [];

    function _is_scrolling() {
        return $('body').hasClass('sticky-enabled') ? true : false;
    }

    function _is_sticky_enabled() {
        return $('body').hasClass('tc-sticky-header') ? true : false;
    }

    function _get_initial_offset() {
        //initialOffset     = ( 1 == isUserLogged &&  580 < $(window).width() ) ? $('#wpadminbar').height() : 0;
        var initialOffset   = 0;
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
        var headerHeight    = $tcHeader.height();
        //set initial margin-top = initial offset + header's height
        $('#tc-reset-margin-top').css('margin-top' , ( +headerHeight + customOffset ) + 10 + 'px' ); //10 = header bottom border
    }


    function _set_header_top_offset() {
        //set header initial offset
        $tcHeader.css('top' , _get_initial_offset() );
    }


    function _set_logo_height(){
        if ( 0 === $('img' , '.site-logo').length )
            return;
        $.each($('img', '.site-logo'), function( $i ){
            if ( ! logosRatio[$i] )
              return;
            var logoHeight   = $(this).width() / logosRatio[$i];
            $(this).css('height' , logoHeight );
        });
        setTimeout( function() {
            _set_sticky_offsets();
            _set_header_top_offset();
        } , 200 );
    }


    //set site logo width and height if exists
    //=> allow the CSS3 transition to be enabled
    if ( _is_sticky_enabled() && 0 !== $('img' , '.site-logo').length ) {
        $.each($('img', '.site-logo'), function( $i ){
            logosW[$i]  = $(this).attr('width');
            logosH[$i]  = $(this).attr('height');

            //check that all numbers are valid before using division
            if ( 0 === _.size( _.filter( [ logosW[$i], logosH[$i] ], function(num){ return _.isNumber(num) && 0 !== num; } ) ) )
              return;

            logosRatio[$i]  = logosW[$i] / logosH[$i];
            $(this).css('height' , logosH[$i]  ).css('width' , logosW[$i] );
        });
    }

    //LOADING ACTIONS
    if ( _is_sticky_enabled() )
        setTimeout( function() { _refresh(); } , 20 );

    //RESIZING ACTIONS
    $(window).resize(function() {
        if ( ! _is_sticky_enabled() )
            return;
        _set_sticky_offsets();
        _set_header_top_offset();
        _set_logo_height();
    });

    function _refresh() {
        setTimeout( function() {
            _set_sticky_offsets();
            _set_header_top_offset();
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
        //process scrolling actions
        if ( $(window).scrollTop() > triggerHeight ) {
            $('body').addClass("sticky-enabled").removeClass("sticky-disabled");
        }
        else {
            $('body').removeClass("sticky-enabled").addClass("sticky-disabled");
            setTimeout( function() { _refresh();} ,
              $('body').hasClass('is-customizing') ? 100 : 20
            );
            //additional refresh for some edge cases like big logos
            setTimeout( function() { _refresh(); } , 200 );
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

         if ( 1 == _p.timerOnScrollAllBrowsers ) {
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
