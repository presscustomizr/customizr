TCParams = TCParams || {
	CenterSlides: 1,
	FancyBoxAutoscale: 1,
	FancyBoxState: 1,
	HasComments: "",
	LeftSidebarClass: ".span3.left.tc-sidebar",
	LoadBootstrap: 1,
	LoadCustomizrScript: 1,
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
};

/* ! ===================================================
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
if ( 1 == TCParams.LoadBootstrap ) {
	!function(a){"use strict";a(function(){a.support.transition=function(){var a=function(){var c,a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(c in b)if(void 0!==a.style[c])return b[c]}();return a&&{end:a}}()})}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.options=c,this.$element=a(b).delegate('[data-dismiss="modal"]',"click.dismiss.modal",a.proxy(this.hide,this)),this.options.remote&&this.$element.find(".modal-body").load(this.options.remote)};b.prototype={constructor:b,toggle:function(){return this[this.isShown?"hide":"show"]()},show:function(){var b=this,c=a.Event("show");this.$element.trigger(c),this.isShown||c.isDefaultPrevented()||(this.isShown=!0,this.escape(),this.backdrop(function(){var c=a.support.transition&&b.$element.hasClass("fade");b.$element.parent().length||b.$element.appendTo(document.body),b.$element.show(),c&&b.$element[0].offsetWidth,b.$element.addClass("in").attr("aria-hidden",!1),b.enforceFocus(),c?b.$element.one(a.support.transition.end,function(){b.$element.focus().trigger("shown")}):b.$element.focus().trigger("shown")}))},hide:function(b){b&&b.preventDefault(),b=a.Event("hide"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),a(document).off("focusin.modal"),this.$element.removeClass("in").attr("aria-hidden",!0),a.support.transition&&this.$element.hasClass("fade")?this.hideWithTransition():this.hideModal())},enforceFocus:function(){var b=this;a(document).on("focusin.modal",function(a){b.$element[0]===a.target||b.$element.has(a.target).length||b.$element.focus()})},escape:function(){var a=this;this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.modal",function(b){27==b.which&&a.hide()}):this.isShown||this.$element.off("keyup.dismiss.modal")},hideWithTransition:function(){var b=this,c=setTimeout(function(){b.$element.off(a.support.transition.end),b.hideModal()},500);this.$element.one(a.support.transition.end,function(){clearTimeout(c),b.hideModal()})},hideModal:function(){var a=this;this.$element.hide(),this.backdrop(function(){a.removeBackdrop(),a.$element.trigger("hidden")})},removeBackdrop:function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},backdrop:function(b){var d=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var e=a.support.transition&&d;if(this.$backdrop=a('<div class="modal-backdrop '+d+'" />').appendTo(document.body),this.$backdrop.click("static"==this.options.backdrop?a.proxy(this.$element[0].focus,this.$element[0]):a.proxy(this.hide,this)),e&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;e?this.$backdrop.one(a.support.transition.end,b):b()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("in"),a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(a.support.transition.end,b):b()):b&&b()}};var c=a.fn.modal;a.fn.modal=function(c){return this.each(function(){var d=a(this),e=d.data("modal"),f=a.extend({},a.fn.modal.defaults,d.data(),"object"==typeof c&&c);e||d.data("modal",e=new b(this,f)),"string"==typeof c?e[c]():f.show&&e.show()})},a.fn.modal.defaults={backdrop:!0,keyboard:!0,show:!0},a.fn.modal.Constructor=b,a.fn.modal.noConflict=function(){return a.fn.modal=c,this},a(document).on("click.modal.data-api",'[data-toggle="modal"]',function(b){var c=a(this),d=c.attr("href"),e=a(c.attr("data-target")||d&&d.replace(/.*(?=#[^\s]+$)/,"")),f=e.data("modal")?"toggle":a.extend({remote:!/#/.test(d)&&d},e.data(),c.data());b.preventDefault(),e.modal(f).one("hide",function(){c.focus()})})}(window.jQuery),!function(a){"use strict";function d(){a(".dropdown-backdrop").remove(),a(b).each(function(){e(a(this)).removeClass("open")})}function e(b){var d,c=b.attr("data-target");return c||(c=b.attr("href"),c=c&&/#/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,"")),d=c&&a(c),d&&d.length||(d=b.parent()),d}var b="[data-toggle=dropdown]",c=function(b){var c=a(b).on("click.dropdown.data-api",this.toggle);a("html").on("click.dropdown.data-api",function(){c.parent().removeClass("open")})};c.prototype={constructor:c,toggle:function(){var f,g,c=a(this);if(!c.is(".disabled, :disabled"))return f=e(c),g=f.hasClass("open"),d(),g||("ontouchstart"in document.documentElement&&a('<div class="dropdown-backdrop"/>').insertBefore(a(this)).on("click",d),f.toggleClass("open")),c.focus(),!1},keydown:function(c){var d,f,h,i,j;if(/(38|40|27)/.test(c.keyCode)&&(d=a(this),c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled"))){if(h=e(d),i=h.hasClass("open"),!i||i&&27==c.keyCode)return 27==c.which&&h.find(b).focus(),d.click();f=a("[role=menu] li:not(.divider):visible a",h),f.length&&(j=f.index(f.filter(":focus")),38==c.keyCode&&j>0&&j--,40==c.keyCode&&j<f.length-1&&j++,~j||(j=0),f.eq(j).focus())}}};var f=a.fn.dropdown;a.fn.dropdown=function(b){return this.each(function(){var d=a(this),e=d.data("dropdown");e||d.data("dropdown",e=new c(this)),"string"==typeof b&&e[b].call(d)})},a.fn.dropdown.Constructor=c,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=f,this},a(document).on("click.dropdown.data-api",d).on("click.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.dropdown.data-api",b,c.prototype.toggle).on("keydown.dropdown.data-api",b+", [role=menu]",c.prototype.keydown)}(window.jQuery),+function(a){"use strict";function b(c,d){var e,f=a.proxy(this.process,this);this.$element=a(c).is("body")?a(window):a(c),this.$body=a("body"),this.$scrollElement=this.$element.on("scroll.bs.scroll-spy.data-api",f),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||(e=a(c).attr("href"))&&e.replace(/.*(?=#[^\s]+$)/,"")||"")+" .nav li > a",this.offsets=a([]),this.targets=a([]),this.activeTarget=null,this.refresh(),this.process()}b.DEFAULTS={offset:10},b.prototype.refresh=function(){var b=this.$element[0]==window?"offset":"position";this.offsets=a([]),this.targets=a([]);var c=this;this.$body.find(this.selector).map(function(){var d=a(this),e=d.data("target")||d.attr("href"),f=/^#\w/.test(e)&&a(e);return f&&f.length&&[[f[b]().top+(!a.isWindow(c.$scrollElement.get(0))&&c.$scrollElement.scrollTop()),e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){c.offsets.push(this[0]),c.targets.push(this[1])})},b.prototype.process=function(){var g,a=this.$scrollElement.scrollTop()+this.options.offset,b=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,c=b-this.$scrollElement.height(),d=this.offsets,e=this.targets,f=this.activeTarget;if(a>=c)return f!=(g=e.last()[0])&&this.activate(g);for(g=d.length;g--;)f!=e[g]&&a>=d[g]&&(!d[g+1]||a<=d[g+1])&&this.activate(e[g])},b.prototype.activate=function(b){this.activeTarget=b,a(this.selector).parents(".active").removeClass("active");var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate")};var c=a.fn.scrollspy;a.fn.scrollspy=function(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=c,this},a(window).on("load",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);b.scrollspy(b.data())})})}(window.jQuery),!function(a){"use strict";var b=function(b){this.element=a(b)};b.prototype={constructor:b,show:function(){var e,f,g,b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.attr("data-target");d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),b.parent("li").hasClass("active")||(e=c.find(".active:last a")[0],g=a.Event("show",{relatedTarget:e}),b.trigger(g),g.isDefaultPrevented()||(f=a(d),this.activate(b.parent("li"),c),this.activate(f,f.parent(),function(){b.trigger({type:"shown",relatedTarget:e})})))},activate:function(b,c,d){function g(){e.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"),b.addClass("active"),f?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu")&&b.closest("li.dropdown").addClass("active"),d&&d()}var e=c.find("> .active"),f=d&&a.support.transition&&e.hasClass("fade");f?e.one(a.support.transition.end,g):g(),e.removeClass("in")}};var c=a.fn.tab;a.fn.tab=function(c){return this.each(function(){var d=a(this),e=d.data("tab");e||d.data("tab",e=new b(this)),"string"==typeof c&&e[c]()})},a.fn.tab.Constructor=b,a.fn.tab.noConflict=function(){return a.fn.tab=c,this},a(document).on("click.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"]',function(b){b.preventDefault(),a(this).tab("show")})}(window.jQuery),!function(a){"use strict";var b=function(a,b){this.init("tooltip",a,b)};b.prototype={constructor:b,init:function(b,c,d){var e,f,g,h,i;for(this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.enabled=!0,g=this.options.trigger.split(" "),i=g.length;i--;)h=g[i],"click"==h?this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this)):"manual"!=h&&(e="hover"==h?"mouseenter":"focus",f="hover"==h?"mouseleave":"blur",this.$element.on(e+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(f+"."+this.type,this.options.selector,a.proxy(this.leave,this)));this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},getOptions:function(b){return b=a.extend({},a.fn[this.type].defaults,this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},enter:function(b){var e,c=a.fn[this.type].defaults,d={};return this._options&&a.each(this._options,function(a,b){c[a]!=b&&(d[a]=b)},this),e=a(b.currentTarget)[this.type](d).data(this.type),e.options.delay&&e.options.delay.show?(clearTimeout(this.timeout),e.hoverState="in",this.timeout=setTimeout(function(){"in"==e.hoverState&&e.show()},e.options.delay.show),void 0):e.show()},leave:function(b){var c=a(b.currentTarget)[this.type](this._options).data(this.type);return this.timeout&&clearTimeout(this.timeout),c.options.delay&&c.options.delay.hide?(c.hoverState="out",this.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide),void 0):c.hide()},show:function(){var b,c,d,e,f,g,h=a.Event("show");if(this.hasContent()&&this.enabled){if(this.$element.trigger(h),h.isDefaultPrevented())return;switch(b=this.tip(),this.setContent(),this.options.animation&&b.addClass("fade"),f="function"==typeof this.options.placement?this.options.placement.call(this,b[0],this.$element[0]):this.options.placement,b.detach().css({top:0,left:0,display:"block"}),this.options.container?b.appendTo(this.options.container):b.insertAfter(this.$element),c=this.getPosition(),d=b[0].offsetWidth,e=b[0].offsetHeight,f){case"bottom":g={top:c.top+c.height,left:c.left+c.width/2-d/2};break;case"top":g={top:c.top-e,left:c.left+c.width/2-d/2};break;case"left":g={top:c.top+c.height/2-e/2,left:c.left-d};break;case"right":g={top:c.top+c.height/2-e/2,left:c.left+c.width}}this.applyPlacement(g,f),this.$element.trigger("shown")}},applyPlacement:function(a,b){var f,g,h,i,c=this.tip(),d=c[0].offsetWidth,e=c[0].offsetHeight;c.offset(a).addClass(b).addClass("in"),f=c[0].offsetWidth,g=c[0].offsetHeight,"top"==b&&g!=e&&(a.top=a.top+e-g,i=!0),"bottom"==b||"top"==b?(h=0,a.left<0&&(h=-2*a.left,a.left=0,c.offset(a),f=c[0].offsetWidth,g=c[0].offsetHeight),this.replaceArrow(h-d+f,f,"left")):this.replaceArrow(g-e,g,"top"),i&&c.offset(a)},replaceArrow:function(a,b,c){this.arrow().css(c,a?50*(1-a/b)+"%":"")},setContent:function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},hide:function(){function e(){var b=setTimeout(function(){c.off(a.support.transition.end).detach()},500);c.one(a.support.transition.end,function(){clearTimeout(b),c.detach()})}var c=this.tip(),d=a.Event("hide");return this.$element.trigger(d),d.isDefaultPrevented()?void 0:(c.removeClass("in"),a.support.transition&&this.$tip.hasClass("fade")?e():c.detach(),this.$element.trigger("hidden"),this)},fixTitle:function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},hasContent:function(){return this.getTitle()},getPosition:function(){var b=this.$element[0];return a.extend({},"function"==typeof b.getBoundingClientRect?b.getBoundingClientRect():{width:b.offsetWidth,height:b.offsetHeight},this.$element.offset())},getTitle:function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},tip:function(){return this.$tip=this.$tip||a(this.options.template)},arrow:function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},validate:function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},enable:function(){this.enabled=!0},disable:function(){this.enabled=!1},toggleEnabled:function(){this.enabled=!this.enabled},toggle:function(b){var c=b?a(b.currentTarget)[this.type](this._options).data(this.type):this;c.tip().hasClass("in")?c.hide():c.show()},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}};var c=a.fn.tooltip;a.fn.tooltip=function(c){return this.each(function(){var d=a(this),e=d.data("tooltip"),f="object"==typeof c&&c;e||d.data("tooltip",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.tooltip.Constructor=b,a.fn.tooltip.defaults={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1},a.fn.tooltip.noConflict=function(){return a.fn.tooltip=c,this}}(window.jQuery),!function(a){"use strict";var b=function(a,b){this.init("popover",a,b)};b.prototype=a.extend({},a.fn.tooltip.Constructor.prototype,{constructor:b,setContent:function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content")[this.options.html?"html":"text"](c),a.removeClass("fade top bottom left right in")},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var a,b=this.$element,c=this.options;return a=("function"==typeof c.content?c.content.call(b[0]):c.content)||b.attr("data-content")},tip:function(){return this.$tip||(this.$tip=a(this.options.template)),this.$tip},destroy:function(){this.hide().$element.off("."+this.type).removeData(this.type)}});var c=a.fn.popover;a.fn.popover=function(c){return this.each(function(){var d=a(this),e=d.data("popover"),f="object"==typeof c&&c;e||d.data("popover",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.popover.Constructor=b,a.fn.popover.defaults=a.extend({},a.fn.tooltip.defaults,{placement:"right",trigger:"click",content:"",template:'<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),a.fn.popover.noConflict=function(){return a.fn.popover=c,this}}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.options=a.extend({},a.fn.affix.defaults,c),this.$window=a(window).on("scroll.affix.data-api",a.proxy(this.checkPosition,this)).on("click.affix.data-api",a.proxy(function(){setTimeout(a.proxy(this.checkPosition,this),1)},this)),this.$element=a(b),this.checkPosition()};b.prototype.checkPosition=function(){if(this.$element.is(":visible")){var i,b=a(document).height(),c=this.$window.scrollTop(),d=this.$element.offset(),e=this.options.offset,f=e.bottom,g=e.top,h="affix affix-top affix-bottom";"object"!=typeof e&&(f=g=e),"function"==typeof g&&(g=e.top()),"function"==typeof f&&(f=e.bottom()),i=null!=this.unpin&&c+this.unpin<=d.top?!1:null!=f&&d.top+this.$element.height()>=b-f?"bottom":null!=g&&g>=c?"top":!1,this.affixed!==i&&(this.affixed=i,this.unpin="bottom"==i?d.top-c:null,this.$element.removeClass(h).addClass("affix"+(i?"-"+i:"")))}};var c=a.fn.affix;a.fn.affix=function(c){return this.each(function(){var d=a(this),e=d.data("affix"),f="object"==typeof c&&c;e||d.data("affix",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.affix.Constructor=b,a.fn.affix.defaults={offset:0},a.fn.affix.noConflict=function(){return a.fn.affix=c,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var b=a(this),c=b.data();c.offset=c.offset||{},c.offsetBottom&&(c.offset.bottom=c.offsetBottom),c.offsetTop&&(c.offset.top=c.offsetTop),b.affix(c)})})}(window.jQuery),!function(a){"use strict";var b='[data-dismiss="alert"]',c=function(c){a(c).on("click",b,this.close)};c.prototype.close=function(b){function f(){e.trigger("closed").remove()}var e,c=a(this),d=c.attr("data-target");d||(d=c.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),e=a(d),b&&b.preventDefault(),e.length||(e=c.hasClass("alert")?c:c.parent()),e.trigger(b=a.Event("close")),b.isDefaultPrevented()||(e.removeClass("in"),a.support.transition&&e.hasClass("fade")?e.on(a.support.transition.end,f):f())};var d=a.fn.alert;a.fn.alert=function(b){return this.each(function(){var d=a(this),e=d.data("alert");e||d.data("alert",e=new c(this)),"string"==typeof b&&e[b].call(d)})},a.fn.alert.Constructor=c,a.fn.alert.noConflict=function(){return a.fn.alert=d,this},a(document).on("click.alert.data-api",b,c.prototype.close)}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.$element=a(b),this.options=a.extend({},a.fn.button.defaults,c)};b.prototype.setState=function(a){var b="disabled",c=this.$element,d=c.data(),e=c.is("input")?"val":"html";a+="Text",d.resetText||c.data("resetText",c[e]()),c[e](d[a]||this.options[a]),setTimeout(function(){"loadingText"==a?c.addClass(b).attr(b,b):c.removeClass(b).removeAttr(b)},0)},b.prototype.toggle=function(){var a=this.$element.closest('[data-toggle="buttons-radio"]');a&&a.find(".active").removeClass("active"),this.$element.toggleClass("active")};var c=a.fn.button;a.fn.button=function(c){return this.each(function(){var d=a(this),e=d.data("button"),f="object"==typeof c&&c;e||d.data("button",e=new b(this,f)),"toggle"==c?e.toggle():c&&e.setState(c)})},a.fn.button.defaults={loadingText:"loading..."},a.fn.button.Constructor=b,a.fn.button.noConflict=function(){return a.fn.button=c,this},a(document).on("click.button.data-api","[data-toggle^=button]",function(b){var c=a(b.target);c.hasClass("btn")||(c=c.closest(".btn")),c.button("toggle")})}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.$element=a(b),this.options=a.extend({},a.fn.collapse.defaults,c),this.options.parent&&(this.$parent=a(this.options.parent)),this.options.toggle&&this.toggle()};b.prototype={constructor:b,dimension:function(){var a=this.$element.hasClass("width");return a?"width":"height"},show:function(){var b,c,d,e;if(!this.transitioning&&!this.$element.hasClass("in")){if(b=this.dimension(),c=a.camelCase(["scroll",b].join("-")),d=this.$parent&&this.$parent.find("> .accordion-group > .in"),d&&d.length){if(e=d.data("collapse"),e&&e.transitioning)return;d.collapse("hide"),e||d.data("collapse",null)}this.$element[b](0),this.transition("addClass",a.Event("show"),"shown"),a.support.transition&&this.$element[b](this.$element[0][c]);var f=a("body").hasClass("sticky-enabled")?a(window).height():a(window).height()-a(".navbar-wrapper").offset().top;f=f-90>80?f-90:300,this.$element.css("max-height",f+"px")}},hide:function(){var b;!this.transitioning&&this.$element.hasClass("in")&&(b=this.dimension(),this.reset(this.$element[b]()),this.transition("removeClass",a.Event("hide"),"hidden"),this.$element[b](0))},reset:function(a){var b=this.dimension();return this.$element.removeClass("collapse")[b](a||"auto")[0].offsetWidth,this.$element[null!==a?"addClass":"removeClass"]("collapse"),this},transition:function(b,c,d){var e=this,f=function(){"show"==c.type&&e.reset(),e.transitioning=0,e.$element.trigger(d)};this.$element.trigger(c),c.isDefaultPrevented()||(this.transitioning=1,this.$element[b]("in"),a.support.transition&&this.$element.hasClass("collapse")?this.$element.one(a.support.transition.end,f):f())},toggle:function(){this[this.$element.hasClass("in")?"hide":"show"]()}};var c=a.fn.collapse;a.fn.collapse=function(c){return this.each(function(){var d=a(this),e=d.data("collapse"),f=a.extend({},a.fn.collapse.defaults,d.data(),"object"==typeof c&&c);e||d.data("collapse",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.collapse.defaults={toggle:!0},a.fn.collapse.Constructor=b,a.fn.collapse.noConflict=function(){return a.fn.collapse=c,this},a(document).on("click.collapse.data-api","[data-toggle=collapse]",function(b){var d,c=a(this),e=c.attr("data-target")||b.preventDefault()||(d=c.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""),f=a(e).data("collapse")?"toggle":c.data();c[a(e).hasClass("in")?"addClass":"removeClass"]("collapsed"),a(e).collapse(f)})}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,"hover"==this.options.pause&&this.$element.on("mouseenter",a.proxy(this.pause,this)).on("mouseleave",a.proxy(this.cycle,this))};b.prototype={cycle:function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},getActiveIndex:function(){return this.$active=this.$element.find(".item.active"),this.$items=this.$active.parent().children(),this.$items.index(this.$active)},to:function(b){var c=this.getActiveIndex(),d=this;if(!(b>this.$items.length-1||0>b))return this.sliding?this.$element.one("slid",function(){d.to(b)}):c==b?this.pause().cycle():this.slide(b>c?"next":"prev",a(this.$items[b]))},pause:function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition.end&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),clearInterval(this.interval),this.interval=null,this},next:function(){return this.sliding?void 0:this.slide("next")},prev:function(){return this.sliding?void 0:this.slide("prev")},slide:function(b,c){!a.support.transition&&this.$element.hasClass("slide")&&this.$element.find(".item").stop(!0,!0);var j,d=this.$element.find(".item.active"),e=c||d[b](),f=this.interval,g="next"==b?"left":"right",h="next"==b?"first":"last",i=this;if(this.sliding=!0,f&&this.pause(),e=e.length?e:this.$element.find(".item")[h](),j=a.Event("slide",{relatedTarget:e[0],direction:g}),!e.hasClass("active")){if(this.$indicators.length&&(this.$indicators.find(".active").removeClass("active"),this.$element.one("slid",function(){var b=a(i.$indicators.children()[i.getActiveIndex()]);b&&b.addClass("active")})),a.support.transition&&this.$element.hasClass("slide")){if(this.$element.trigger(j),j.isDefaultPrevented())return;e.addClass(b),e[0].offsetWidth,d.addClass(g),e.addClass(g),this.$element.one(a.support.transition.end,function(){e.removeClass([b,g].join(" ")).addClass("active"),d.removeClass(["active",g].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger("slid")},0)})}else if(!a.support.transition&&this.$element.hasClass("slide")){if(this.$element.trigger(j),j.isDefaultPrevented())return;d.animate({left:"right"==g?"100%":"-100%"},600,function(){d.removeClass("active"),i.sliding=!1,setTimeout(function(){i.$element.trigger("slid")},0)}),e.addClass(b).css({left:"right"==g?"-100%":"100%"}).animate({left:"0"},600,function(){e.removeClass(b).addClass("active")})}else{if(this.$element.trigger(j),j.isDefaultPrevented())return;d.removeClass("active"),e.addClass("active"),this.sliding=!1,this.$element.trigger("slid")}return f&&this.cycle(),this}}};var c=a.fn.carousel;a.fn.carousel=function(c){return this.each(function(){var d=a(this),e=d.data("carousel"),f=a.extend({},a.fn.carousel.defaults,"object"==typeof c&&c),g="string"==typeof c?c:f.slide;e||d.data("carousel",e=new b(this,f)),"number"==typeof c?e.to(c):g?e[g]():f.interval&&e.pause().cycle()})},a.fn.carousel.defaults={interval:5e3,pause:"hover"},a.fn.carousel.Constructor=b,a.fn.carousel.noConflict=function(){return a.fn.carousel=c,this},a(document).on("click.carousel.data-api","[data-slide], [data-slide-to]",function(b){var d,g,c=a(this),e=a(c.attr("data-target")||(d=c.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,"")),f=a.extend({},e.data(),c.data());e.carousel(f),(g=c.attr("data-slide-to"))&&e.data("carousel").pause().to(g).cycle(),b.preventDefault()})}(window.jQuery),!function(a){"use strict";var b=function(b,c){this.$element=a(b),this.options=a.extend({},a.fn.typeahead.defaults,c),this.matcher=this.options.matcher||this.matcher,this.sorter=this.options.sorter||this.sorter,this.highlighter=this.options.highlighter||this.highlighter,this.updater=this.options.updater||this.updater,this.source=this.options.source,this.$menu=a(this.options.menu),this.shown=!1,this.listen()};b.prototype={constructor:b,select:function(){var a=this.$menu.find(".active").attr("data-value");return this.$element.val(this.updater(a)).change(),this.hide()},updater:function(a){return a},show:function(){var b=a.extend({},this.$element.position(),{height:this.$element[0].offsetHeight});return this.$menu.insertAfter(this.$element).css({top:b.top+b.height,left:b.left}).show(),this.shown=!0,this},hide:function(){return this.$menu.hide(),this.shown=!1,this},lookup:function(){var c;return this.query=this.$element.val(),!this.query||this.query.length<this.options.minLength?this.shown?this.hide():this:(c=a.isFunction(this.source)?this.source(this.query,a.proxy(this.process,this)):this.source,c?this.process(c):this)},process:function(b){var c=this;return b=a.grep(b,function(a){return c.matcher(a)}),b=this.sorter(b),b.length?this.render(b.slice(0,this.options.items)).show():this.shown?this.hide():this},matcher:function(a){return~a.toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(a){for(var e,b=[],c=[],d=[];e=a.shift();)e.toLowerCase().indexOf(this.query.toLowerCase())?~e.indexOf(this.query)?c.push(e):d.push(e):b.push(e);return b.concat(c,d)},highlighter:function(a){var b=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");return a.replace(new RegExp("("+b+")","ig"),function(a,b){return"<strong>"+b+"</strong>"})},render:function(b){var c=this;return b=a(b).map(function(b,d){return b=a(c.options.item).attr("data-value",d),b.find("a").html(c.highlighter(d)),b[0]}),b.first().addClass("active"),this.$menu.html(b),this},next:function(){var c=this.$menu.find(".active").removeClass("active"),d=c.next();d.length||(d=a(this.$menu.find("li")[0])),d.addClass("active")},prev:function(){var b=this.$menu.find(".active").removeClass("active"),c=b.prev();c.length||(c=this.$menu.find("li").last()),c.addClass("active")},listen:function(){this.$element.on("focus",a.proxy(this.focus,this)).on("blur",a.proxy(this.blur,this)).on("keypress",a.proxy(this.keypress,this)).on("keyup",a.proxy(this.keyup,this)),this.eventSupported("keydown")&&this.$element.on("keydown",a.proxy(this.keydown,this)),this.$menu.on("click",a.proxy(this.click,this)).on("mouseenter","li",a.proxy(this.mouseenter,this)).on("mouseleave","li",a.proxy(this.mouseleave,this))},eventSupported:function(a){var b=a in this.$element;return b||(this.$element.setAttribute(a,"return;"),b="function"==typeof this.$element[a]),b},move:function(a){if(this.shown){switch(a.keyCode){case 9:case 13:case 27:a.preventDefault();break;case 38:a.preventDefault(),this.prev();break;case 40:a.preventDefault(),this.next()}a.stopPropagation()}},keydown:function(b){this.suppressKeyPressRepeat=~a.inArray(b.keyCode,[40,38,9,13,27]),this.move(b)},keypress:function(a){this.suppressKeyPressRepeat||this.move(a)},keyup:function(a){switch(a.keyCode){case 40:case 38:case 16:case 17:case 18:break;case 9:case 13:if(!this.shown)return;this.select();break;case 27:if(!this.shown)return;this.hide();break;default:this.lookup()}a.stopPropagation(),a.preventDefault()},focus:function(){this.focused=!0},blur:function(){this.focused=!1,!this.mousedover&&this.shown&&this.hide()},click:function(a){a.stopPropagation(),a.preventDefault(),this.select(),this.$element.focus()},mouseenter:function(b){this.mousedover=!0,this.$menu.find(".active").removeClass("active"),a(b.currentTarget).addClass("active")},mouseleave:function(){this.mousedover=!1,!this.focused&&this.shown&&this.hide()}};var c=a.fn.typeahead;a.fn.typeahead=function(c){return this.each(function(){var d=a(this),e=d.data("typeahead"),f="object"==typeof c&&c;e||d.data("typeahead",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.typeahead.defaults={source:[],items:8,menu:'<ul class="typeahead dropdown-menu"></ul>',item:'<li><a href="#"></a></li>',minLength:1},a.fn.typeahead.Constructor=b,a.fn.typeahead.noConflict=function(){return a.fn.typeahead=c,this},a(document).on("focus.typeahead.data-api",'[data-provide="typeahead"]',function(){var c=a(this);c.data("typeahead")||c.typeahead(c.data())})}(window.jQuery);
} // end if load Twitter Bootstrap == true


/* !
 * FancyBox - jQuery Plugin
 * Simple and fancy lightbox alternative
 *
 * Examples and documentation at: http://fancybox.net
 *
 * Copyright (c) 2008 - 2010 Janis Skarnelis
 * That said, it is hardly a one-person project. Many people have submitted bugs, code, and offered their advice freely. Their support is greatly appreciated.
 *
 * Version: 1.3.4 (11/11/2010)
 * Requires: jQuery v1.3+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
if ( 1 == TCParams.FancyBoxState ) {
    !function(a){var b,c,d,e,f,g,h,i,j,k,v,z,A,l=0,m={},n=[],o=0,p={},q=[],r=null,s=new Image,t=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,u=/[^\.]\.(swf)\s*$/i,w=1,x=0,y="",B=!1,C=a.extend(a("<div/>")[0],{prop:0}),D=a.browser.msie&&a.browser.version<7&&!window.XMLHttpRequest,E=function(){c.hide(),s.onerror=s.onload=null,r&&r.abort(),b.empty()},F=function(){return!1===m.onError(n,l,m)?(c.hide(),B=!1,void 0):(m.titleShow=!1,m.width="auto",m.height="auto",b.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>'),H(),void 0)},G=function(){var e,f,h,i,j,k,d=n[l];if(E(),m=a.extend({},a.fn.fancybox.defaults,"undefined"==typeof a(d).data("fancybox")?m:a(d).data("fancybox")),k=m.onStart(n,l,m),k===!1)return B=!1,void 0;if("object"==typeof k&&(m=a.extend(m,k)),h=m.title||(d.nodeName?a(d).attr("title"):d.title)||"",d.nodeName&&!m.orig&&(m.orig=a(d).children("img:first").length?a(d).children("img:first"):a(d)),""===h&&m.orig&&m.titleFromAlt&&(h=m.orig.attr("alt")),e=m.href||(d.nodeName?a(d).attr("href"):d.href)||null,(/^(?:javascript)/i.test(e)||"#"==e)&&(e=null),m.type?(f=m.type,e||(e=m.content)):m.content?f="html":e&&(f=e.match(t)?"image":e.match(u)?"swf":a(d).hasClass("iframe")?"iframe":0===e.indexOf("#")?"inline":"ajax"),!f)return F(),void 0;switch("inline"==f&&(d=e.substr(e.indexOf("#")),f=a(d).length>0?"inline":"ajax"),m.type=f,m.href=e,m.title=h,m.autoDimensions&&("html"==m.type||"inline"==m.type||"ajax"==m.type?(m.width="auto",m.height="auto"):m.autoDimensions=!1),m.modal&&(m.overlayShow=!0,m.hideOnOverlayClick=!1,m.hideOnContentClick=!1,m.enableEscapeButton=!1,m.showCloseButton=!1),m.padding=parseInt(m.padding,10),m.margin=parseInt(m.margin,10),b.css("padding",m.padding+m.margin),a(".fancybox-inline-tmp").unbind("fancybox-cancel").bind("fancybox-change",function(){a(this).replaceWith(g.children())}),f){case"html":b.html(m.content),H();break;case"inline":if(a(d).parent().is("#fancybox-content")===!0)return B=!1,void 0;a('<div class="fancybox-inline-tmp" />').hide().insertBefore(a(d)).bind("fancybox-cleanup",function(){a(this).replaceWith(g.children())}).bind("fancybox-cancel",function(){a(this).replaceWith(b.children())}),a(d).appendTo(b),H();break;case"image":B=!1,a.fancybox.showActivity(),s=new Image,s.onerror=function(){F()},s.onload=function(){B=!0,s.onerror=s.onload=null,I()},s.src=e;break;case"swf":m.scrolling="no",i='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+m.width+'" height="'+m.height+'"><param name="movie" value="'+e+'"></param>',j="",a.each(m.swf,function(a,b){i+='<param name="'+a+'" value="'+b+'"></param>',j+=" "+a+'="'+b+'"'}),i+='<embed src="'+e+'" type="application/x-shockwave-flash" width="'+m.width+'" height="'+m.height+'"'+j+"></embed></object>",b.html(i),H();break;case"ajax":B=!1,a.fancybox.showActivity(),m.ajax.win=m.ajax.success,r=a.ajax(a.extend({},m.ajax,{url:e,data:m.ajax.data||{},error:function(a){a.status>0&&F()},success:function(a,d,f){var g="object"==typeof f?f:r;if(200==g.status){if("function"==typeof m.ajax.win){if(k=m.ajax.win(e,a,d,f),k===!1)return c.hide(),void 0;("string"==typeof k||"object"==typeof k)&&(a=k)}b.html(a),H()}}}));break;case"iframe":J()}},H=function(){var c=m.width,d=m.height;c=c.toString().indexOf("%")>-1?parseInt((a(window).width()-2*m.margin)*parseFloat(c)/100,10)+"px":"auto"==c?"auto":c+"px",d=d.toString().indexOf("%")>-1?parseInt((a(window).height()-2*m.margin)*parseFloat(d)/100,10)+"px":"auto"==d?"auto":d+"px",b.wrapInner('<div style="width:'+c+";height:"+d+";overflow: "+("auto"==m.scrolling?"auto":"yes"==m.scrolling?"scroll":"hidden")+';position:relative;"></div>'),m.width=b.width(),m.height=b.height(),J()},I=function(){m.width=s.width,m.height=s.height,a("<img />").attr({id:"fancybox-img",src:s.src,alt:m.title}).appendTo(b),J()},J=function(){var f,r;return c.hide(),e.is(":visible")&&!1===p.onCleanup(q,o,p)?(a.event.trigger("fancybox-cancel"),B=!1,void 0):(B=!0,a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),e.is(":visible")&&"outside"!==p.titlePosition&&e.css("height",e.height()),q=n,o=l,p=m,p.overlayShow?(d.css({"background-color":p.overlayColor,opacity:p.overlayOpacity,cursor:p.hideOnOverlayClick?"pointer":"auto",height:a(document).height()}),d.is(":visible")||(D&&a("select:not(#fancybox-tmp select)").filter(function(){return"hidden"!==this.style.visibility}).css({visibility:"hidden"}).one("fancybox-cleanup",function(){this.style.visibility="inherit"}),d.show())):d.hide(),A=R(),L(),e.is(":visible")?(a(h.add(j).add(k)).hide(),f=e.position(),z={top:f.top,left:f.left,width:e.width(),height:e.height()},r=z.width==A.width&&z.height==A.height,g.fadeTo(p.changeFade,.3,function(){var c=function(){g.html(b.contents()).fadeTo(p.changeFade,1,N)};a.event.trigger("fancybox-change"),g.empty().removeAttr("filter").css({"border-width":p.padding,width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}),r?c():(C.prop=0,a(C).animate({prop:1},{duration:p.changeSpeed,easing:p.easingChange,step:P,complete:c}))}),void 0):(e.removeAttr("style"),g.css("border-width",p.padding),"elastic"==p.transitionIn?(z=T(),g.html(b.contents()),e.show(),p.opacity&&(A.opacity=0),C.prop=0,a(C).animate({prop:1},{duration:p.speedIn,easing:p.easingIn,step:P,complete:N}),void 0):("inside"==p.titlePosition&&x>0&&i.show(),g.css({width:A.width-2*p.padding,height:m.autoDimensions?"auto":A.height-x-2*p.padding}).html(b.contents()),e.css(A).fadeIn("none"==p.transitionIn?0:p.speedIn,N),void 0)))},K=function(a){return a&&a.length?"float"==p.titlePosition?'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+a+'</td><td id="fancybox-title-float-right"></td></tr></table>':'<div id="fancybox-title-'+p.titlePosition+'">'+a+"</div>":!1},L=function(){if(y=p.title||"",x=0,i.empty().removeAttr("style").removeClass(),p.titleShow===!1)return i.hide(),void 0;if(y=a.isFunction(p.titleFormat)?p.titleFormat(y,q,o,p):K(y),!y||""===y)return i.hide(),void 0;switch(i.addClass("fancybox-title-"+p.titlePosition).html(y).appendTo("body").show(),p.titlePosition){case"inside":i.css({width:A.width-2*p.padding,marginLeft:p.padding,marginRight:p.padding}),x=i.outerHeight(!0),i.appendTo(f),A.height+=x;break;case"over":i.css({marginLeft:p.padding,width:A.width-2*p.padding,bottom:p.padding}).appendTo(f);break;case"float":i.css("left",-1*parseInt((i.width()-A.width-40)/2,10)).appendTo(e);break;default:i.css({width:A.width-2*p.padding,paddingLeft:p.padding,paddingRight:p.padding}).appendTo(e)}i.hide()},M=function(){return(p.enableEscapeButton||p.enableKeyboardNav)&&a(document).bind("keydown.fb",function(b){27==b.keyCode&&p.enableEscapeButton?(b.preventDefault(),a.fancybox.close()):37!=b.keyCode&&39!=b.keyCode||!p.enableKeyboardNav||"INPUT"===b.target.tagName||"TEXTAREA"===b.target.tagName||"SELECT"===b.target.tagName||(b.preventDefault(),a.fancybox[37==b.keyCode?"prev":"next"]())}),p.showNavArrows?((p.cyclic&&q.length>1||0!==o)&&j.show(),(p.cyclic&&q.length>1||o!=q.length-1)&&k.show(),void 0):(j.hide(),k.hide(),void 0)},N=function(){a.support.opacity||(g.get(0).style.removeAttribute("filter"),e.get(0).style.removeAttribute("filter")),m.autoDimensions&&g.css("height","auto"),e.css("height","auto"),y&&y.length&&i.show(),p.showCloseButton&&h.show(),M(),p.hideOnContentClick&&g.bind("click",a.fancybox.close),p.hideOnOverlayClick&&d.bind("click",a.fancybox.close),a(window).bind("resize.fb",a.fancybox.resize),p.centerOnScroll&&a(window).bind("scroll.fb",a.fancybox.center),"iframe"==p.type&&a('<iframe id="fancybox-frame" name="fancybox-frame'+(new Date).getTime()+'" frameborder="0" hspace="0" '+(a.browser.msie?'allowtransparency="true""':"")+' scrolling="'+m.scrolling+'" src="'+p.href+'"></iframe>').appendTo(g),e.show(),B=!1,a.fancybox.center(),p.onComplete(q,o,p),O()},O=function(){var a,b;q.length-1>o&&(a=q[o+1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a)),o>0&&(a=q[o-1].href,"undefined"!=typeof a&&a.match(t)&&(b=new Image,b.src=a))},P=function(a){var b={width:parseInt(z.width+(A.width-z.width)*a,10),height:parseInt(z.height+(A.height-z.height)*a,10),top:parseInt(z.top+(A.top-z.top)*a,10),left:parseInt(z.left+(A.left-z.left)*a,10)};"undefined"!=typeof A.opacity&&(b.opacity=.5>a?.5:a),e.css(b),g.css({width:b.width-2*p.padding,height:b.height-x*a-2*p.padding})},Q=function(){return[a(window).width()-2*p.margin,a(window).height()-2*p.margin,a(document).scrollLeft()+p.margin,a(document).scrollTop()+p.margin]},R=function(){var e,a=Q(),b={},c=p.autoScale,d=2*p.padding;return b.width=p.width.toString().indexOf("%")>-1?parseInt(a[0]*parseFloat(p.width)/100,10):p.width+d,b.height=p.height.toString().indexOf("%")>-1?parseInt(a[1]*parseFloat(p.height)/100,10):p.height+d,c&&(b.width>a[0]||b.height>a[1])&&("image"==m.type||"swf"==m.type?(e=p.width/p.height,b.width>a[0]&&(b.width=a[0],b.height=parseInt((b.width-d)/e+d,10)),b.height>a[1]&&(b.height=a[1],b.width=parseInt((b.height-d)*e+d,10))):(b.width=Math.min(b.width,a[0]),b.height=Math.min(b.height,a[1]))),b.top=parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-b.height-40)),10),b.left=parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-b.width-40)),10),b},S=function(a){var b=a.offset();return b.top+=parseInt(a.css("paddingTop"),10)||0,b.left+=parseInt(a.css("paddingLeft"),10)||0,b.top+=parseInt(a.css("border-top-width"),10)||0,b.left+=parseInt(a.css("border-left-width"),10)||0,b.width=a.width(),b.height=a.height(),b},T=function(){var d,e,b=m.orig?a(m.orig):!1,c={};return b&&b.length?(d=S(b),c={width:d.width+2*p.padding,height:d.height+2*p.padding,top:d.top-p.padding-20,left:d.left-p.padding-20}):(e=Q(),c={width:2*p.padding,height:2*p.padding,top:parseInt(e[3]+.5*e[1],10),left:parseInt(e[2]+.5*e[0],10)}),c},U=function(){return c.is(":visible")?(a("div",c).css("top",-40*w+"px"),w=(w+1)%12,void 0):(clearInterval(v),void 0)};a.fn.fancybox=function(b){return a(this).length?(a(this).data("fancybox",a.extend({},b,a.metadata?a(this).metadata():{})).unbind("click.fb").bind("click.fb",function(b){if(b.preventDefault(),!B){B=!0,a(this).blur(),n=[],l=0;var c=a(this).attr("rel")||"";c&&""!=c&&"nofollow"!==c?(n=a("a[rel="+c+"], area[rel="+c+"]"),l=n.index(this)):n.push(this),G()}}),this):this},a.fancybox=function(b){var c;if(!B){if(B=!0,c="undefined"!=typeof arguments[1]?arguments[1]:{},n=[],l=parseInt(c.index,10)||0,a.isArray(b)){for(var d=0,e=b.length;e>d;d++)"object"==typeof b[d]?a(b[d]).data("fancybox",a.extend({},c,b[d])):b[d]=a({}).data("fancybox",a.extend({content:b[d]},c));n=jQuery.merge(n,b)}else"object"==typeof b?a(b).data("fancybox",a.extend({},c,b)):b=a({}).data("fancybox",a.extend({content:b},c)),n.push(b);(l>n.length||0>l)&&(l=0),G()}},a.fancybox.showActivity=function(){clearInterval(v),c.show(),v=setInterval(U,66)},a.fancybox.hideActivity=function(){c.hide()},a.fancybox.next=function(){return a.fancybox.pos(o+1)},a.fancybox.prev=function(){return a.fancybox.pos(o-1)},a.fancybox.pos=function(a){B||(a=parseInt(a),n=q,a>-1&&a<q.length?(l=a,G()):p.cyclic&&q.length>1&&(l=a>=q.length?0:q.length-1,G()))},a.fancybox.cancel=function(){B||(B=!0,a.event.trigger("fancybox-cancel"),E(),m.onCancel(n,l,m),B=!1)},a.fancybox.close=function(){function b(){d.fadeOut("fast"),i.empty().hide(),e.hide(),a.event.trigger("fancybox-cleanup"),g.empty(),p.onClosed(q,o,p),q=m=[],o=l=0,p=m={},B=!1}if(!B&&!e.is(":hidden")){if(B=!0,p&&!1===p.onCleanup(q,o,p))return B=!1,void 0;if(E(),a(h.add(j).add(k)).hide(),a(g.add(d)).unbind(),a(window).unbind("resize.fb scroll.fb"),a(document).unbind("keydown.fb"),g.find("iframe").attr("src",D&&/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank"),"inside"!==p.titlePosition&&i.empty(),e.stop(),"elastic"==p.transitionOut){z=T();var c=e.position();A={top:c.top,left:c.left,width:e.width(),height:e.height()},p.opacity&&(A.opacity=1),i.empty().hide(),C.prop=1,a(C).animate({prop:0},{duration:p.speedOut,easing:p.easingOut,step:P,complete:b})}else e.fadeOut("none"==p.transitionOut?0:p.speedOut,b)}},a.fancybox.resize=function(){d.is(":visible")&&d.css("height",a(document).height()),a.fancybox.center(!0)},a.fancybox.center=function(){var a,b;B||(b=arguments[0]===!0?1:0,a=Q(),(b||!(e.width()>a[0]||e.height()>a[1]))&&e.stop().animate({top:parseInt(Math.max(a[3]-20,a[3]+.5*(a[1]-g.height()-40)-p.padding)),left:parseInt(Math.max(a[2]-20,a[2]+.5*(a[0]-g.width()-40)-p.padding))},"number"==typeof arguments[0]?arguments[0]:200))},a.fancybox.init=function(){a("#fancybox-wrap").length||(a("body").append(b=a('<div id="fancybox-tmp"></div>'),c=a('<div id="fancybox-loading"><div></div></div>'),d=a('<div id="fancybox-overlay"></div>'),e=a('<div id="fancybox-wrap"></div>')),f=a('<div id="fancybox-outer"></div>').append('<div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div>').appendTo(e),f.append(g=a('<div id="fancybox-content"></div>'),h=a('<a id="fancybox-close"></a>'),i=a('<div id="fancybox-title"></div>'),j=a('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),k=a('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>')),h.click(a.fancybox.close),c.click(a.fancybox.cancel),j.click(function(b){b.preventDefault(),a.fancybox.prev()}),k.click(function(b){b.preventDefault(),a.fancybox.next()}),a.fn.mousewheel&&e.bind("mousewheel.fb",function(b,c){B?b.preventDefault():(0==a(b.target).get(0).clientHeight||a(b.target).get(0).scrollHeight===a(b.target).get(0).clientHeight)&&(b.preventDefault(),a.fancybox[c>0?"prev":"next"]())}),a.support.opacity||e.addClass("fancybox-ie"),D&&(c.addClass("fancybox-ie6"),e.addClass("fancybox-ie6"),a('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||"")?"javascript:void(false)":"about:blank")+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(f)))},a.fn.fancybox.defaults={padding:10,margin:40,opacity:!1,modal:!1,cyclic:!1,scrolling:"auto",width:560,height:340,autoScale:!0,autoDimensions:!0,centerOnScroll:!1,ajax:{},swf:{wmode:"transparent"},hideOnOverlayClick:!0,hideOnContentClick:!1,overlayShow:!0,overlayOpacity:.7,overlayColor:"#777",titleShow:!0,titlePosition:"float",titleFormat:null,titleFromAlt:!1,transitionIn:"fade",transitionOut:"fade",speedIn:300,speedOut:300,changeSpeed:300,changeFade:"fast",easingIn:"swing",easingOut:"swing",showCloseButton:!0,showNavArrows:!0,enableEscapeButton:!0,enableKeyboardNav:!0,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}},a(document).ready(function(){a.fancybox.init()})}(jQuery);
} //end if load Fancybox == true


/* !
 * Customizr WordPress theme Javascript code
 * Copyright (c) 2014 Nicolas GUILLAUME (@nicguillaume), Themes & Co.
 * GPL2+ Licensed
*/
//ON DOM READY
1 == TCParams.LoadCustomizrScript && jQuery(function ($) {
    function g($) {
        ($.which > 0 || "mousedown" === $.type || "mousewheel" === $.type) && f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g)
    }

    //fancybox with localized script variables
    var b = TCParams.FancyBoxState,
        c = TCParams.FancyBoxAutoscale;
    1 == b && $("a.grouped_elements").fancybox({
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
    1 == b && $('a[rel*=tc-fancybox-group]').each( function() {
        var title = $(this).find('img').prop('title');
        var alt = $(this).find('img').prop('alt');
        if (typeof title !== 'undefined' && 0 != title.length) {
            $(this).attr('title',title);
        }
        else if (typeof alt !== 'undefined' &&  0 != alt.length) {
            $(this).attr('title',alt);
        }
    });

    //Slider with localized script variables
    var d = TCParams.SliderName,
        e = TCParams.SliderDelay;
        j = TCParams.SliderHover;

    if (0 != d.length) {
        if (0 != e.length && !j) {
            $("#customizr-slider").carousel({
                interval: e,
                pause: "false"
            });
        } else if (0 != e.length) {
            $("#customizr-slider").carousel({
                interval: e
            });
        } else {
            $("#customizr-slider").carousel();
        }
    }

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
        f.on("scroll mousedown DOMMouseScroll mousewheel keyup", g), f.animate({
            scrollTop: 0
        }, 1e3, function () {
            f.stop().off("scroll mousedown DOMMouseScroll mousewheel keyup", g);
            //$(window).trigger('resize');
        }), $.preventDefault()
    }),

    //displays the button on scroll
	$(document).on( 'scroll', function(){
		if ($(window).scrollTop() > 100) {
			$('.tc-btt-wrapper').addClass('show');
		} else {
			$('.tc-btt-wrapper').removeClass('show');
		}
	});


    //Detects browser with CSS
    // Chrome is Webkit, but Webkit is also Safari. If browser = ie + strips out the .0 suffix
    $.browser.chrome ? $("body").addClass("chrome") : $.browser.webkit ? $("body").addClass("safari") : ( $.browser.msie || '8.0' === $.browser.version || '9.0' === $.browser.version || '10.0' === $.browser.version || '11.0' === $.browser.version ) && $("body").addClass("ie").addClass("ie" + $.browser.version.replace(/[.0]/g, '')),
    
    //Adds version if browser = ie
    $("body").hasClass("ie") && $("body").addClass($.browser.version);

    
    //handle some dynamic hover effects
    $(".widget-front, article").hover(function () {
        $(this).addClass("hover")
    }, function () {
        $(this).removeClass("hover")
    });

    $(".widget li").hover(function () {
        $(this).addClass("on")
    }, function () {
        $(this).removeClass("on")
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
        	var container_width 	= $(this).closest(container).width(),
            	container_height 	= $(container).height(),
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
                    .removeClass("h-center")
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
    	if ( 0 == $('.carousel').length )
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
			})
		});
	}
});

/* Sticky header since v3.2.0 */
jQuery(function ($) {
	var 	$tcHeader  		= $('.tc-header'),
			elToHide 		= [], //[ '.social-block' , '.site-description' ],
			isUserLogged 	= $('body').hasClass('logged-in') || 0 != $('#wpadminbar').length,
			isCustomizing 	= $('body').hasClass('is-customizing'),
			customOffset 	= +TCParams.stickyCustomOffset;

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
		var	headerHeight 	= $tcHeader.height();
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
	if ( _is_sticky_enabled() && 0 != $('img' , '.site-logo').length ) {
		var logoWidth 	= $('img' , '.site-logo').attr('width'),
			logoHeight 	= $('img' , '.site-logo').attr('height');
		$('img' , '.site-logo').css('height' , logoHeight +'px' ).css('width' , logoWidth +'px' );
	}

	//LOADING ACTIONS
	_is_sticky_enabled() && setTimeout( function() { _refresh() } , 20 );
	_is_sticky_enabled() && ! $('body').hasClass('sticky-enabled') && $('body').addClass("sticky-disabled");

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
		} , 20 )
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
		    	_scrolling_actions()
		    }, increment > 5 ? 50 : 0 );
	    } else if ( $('body').hasClass('ie') ) {
		    timer = window.setTimeout(function() {
		    	_scrolling_actions()
		    }, increment > 5 ? 50 : 0 );
		}
	});//end of window.scroll()
});