// addEventListener Polyfill ie9- http://stackoverflow.com/a/27790212
window.addEventListener = window.addEventListener || function (e, f) { window.attachEvent('on' + e, f); };


// Datenow Polyfill ie9- https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now
if (!Date.now) {
  Date.now = function now() {
    return new Date().getTime();
  };
}


// Object.create monkey patch ie8 http://stackoverflow.com/a/18020326
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


//https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/filter
// filter() was added to the ECMA-262 standard in the 5th edition; as such it may not be present in all implementations of the standard.
// You can work around this by inserting the following code at the beginning of your scripts, allowing use of filter() in ECMA-262 implementations which do not natively support it.
// This algorithm is exactly the one specified in ECMA-262, 5th edition, assuming that fn.call evaluates to the original value of Function.prototype.call(), and that Array.prototype.push() has its original value.
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

        // NOTE: Technically this should Object.defineProperty at
        //       the next index, as push can be affected by
        //       properties on Object.prototype and Array.prototype.
        //       But that method's new, and collisions should be
        //       rare, so use the more-compatible alternative.
        if (fun.call(thisArg, val, i, t)) {
          res.push(val);
        }
      }
    }

    return res;
  };
}



//map was added to the ECMA-262 standard in the 5th edition; as such it may not be present in all implementations of the standard. You can work around this by inserting the following code at the beginning of your scripts, allowing use of map in implementations which do not natively support it. This algorithm is exactly the one specified in ECMA-262, 5th edition, assuming Object, TypeError, and Array have their original values and that callback.call evaluates to the original value of Function.prototype.call.
// Production steps of ECMA-262, Edition 5, 15.4.4.19
// Reference: http://es5.github.io/#x15.4.4.19
if (!Array.prototype.map) {

  Array.prototype.map = function(callback, thisArg) {

    var T, A, k;

    if (this == null) {
      throw new TypeError(' this is null or not defined');
    }

    // 1. Let O be the result of calling ToObject passing the |this|
    //    value as the argument.
    var O = Object(this);

    // 2. Let lenValue be the result of calling the Get internal
    //    method of O with the argument "length".
    // 3. Let len be ToUint32(lenValue).
    var len = O.length >>> 0;

    // 4. If IsCallable(callback) is false, throw a TypeError exception.
    // See: http://es5.github.com/#x9.11
    if (typeof callback !== 'function') {
      throw new TypeError(callback + ' is not a function');
    }

    // 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
    if (arguments.length > 1) {
      T = thisArg;
    }

    // 6. Let A be a new array created as if by the expression new Array(len)
    //    where Array is the standard built-in constructor with that name and
    //    len is the value of len.
    A = new Array(len);

    // 7. Let k be 0
    k = 0;

    // 8. Repeat, while k < len
    while (k < len) {

      var kValue, mappedValue;

      // a. Let Pk be ToString(k).
      //   This is implicit for LHS operands of the in operator
      // b. Let kPresent be the result of calling the HasProperty internal
      //    method of O with argument Pk.
      //   This step can be combined with c
      // c. If kPresent is true, then
      if (k in O) {

        // i. Let kValue be the result of calling the Get internal
        //    method of O with argument Pk.
        kValue = O[k];

        // ii. Let mappedValue be the result of calling the Call internal
        //     method of callback with T as the this value and argument
        //     list containing kValue, k, and O.
        mappedValue = callback.call(T, kValue, k, O);

        // iii. Call the DefineOwnProperty internal method of A with arguments
        // Pk, Property Descriptor
        // { Value: mappedValue,
        //   Writable: true,
        //   Enumerable: true,
        //   Configurable: true },
        // and false.

        // In browsers that support Object.defineProperty, use the following:
        // Object.defineProperty(A, k, {
        //   value: mappedValue,
        //   writable: true,
        //   enumerable: true,
        //   configurable: true
        // });

        // For best browser support, use the following:
        A[k] = mappedValue;
      }
      // d. Increase k by 1.
      k++;
    }

    // 9. return A
    return A;
  };
}
/*! iCheck v1.0.1 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
if ( 'function' != typeof(jQuery.fn.iCheck) ) {
  !function(a){function b(a,b,e){var f=a[0],g=/er/.test(e)?p:/bl/.test(e)?n:l,h=e==q?{checked:f[l],disabled:f[n],indeterminate:"true"==a.attr(p)||"false"==a.attr(o)}:f[g];if(/^(ch|di|in)/.test(e)&&!h)c(a,g);else if(/^(un|en|de)/.test(e)&&h)d(a,g);else if(e==q)for(g in h)h[g]?c(a,g,!0):d(a,g,!0);else b&&"toggle"!=e||(b||a[u]("ifClicked"),h?f[r]!==k&&d(a,g):c(a,g))}function c(b,c,e){var q=b[0],u=b.parent(),v=c==l,x=c==p,y=c==n,z=x?o:v?m:"enabled",A=f(b,z+g(q[r])),B=f(b,c+g(q[r]));if(!0!==q[c]){if(!e&&c==l&&q[r]==k&&q.name){var C=b.closest("form"),D='input[name="'+q.name+'"]',D=C.length?C.find(D):a(D);D.each(function(){this!==q&&a(this).data(i)&&d(a(this),c)})}x?(q[c]=!0,q[l]&&d(b,l,"force")):(e||(q[c]=!0),v&&q[p]&&d(b,p,!1)),h(b,v,c,e)}q[n]&&f(b,w,!0)&&u.find("."+j).css(w,"default"),u[s](B||f(b,c)||""),y?u.attr("aria-disabled","true"):u.attr("aria-checked",x?"mixed":"true"),u[t](A||f(b,z)||"")}function d(a,b,c){var d=a[0],e=a.parent(),i=b==l,k=b==p,q=b==n,u=k?o:i?m:"enabled",v=f(a,u+g(d[r])),x=f(a,b+g(d[r]));!1!==d[b]&&((k||!c||"force"==c)&&(d[b]=!1),h(a,i,u,c)),!d[n]&&f(a,w,!0)&&e.find("."+j).css(w,"pointer"),e[t](x||f(a,b)||""),q?e.attr("aria-disabled","false"):e.attr("aria-checked","false"),e[s](v||f(a,u)||"")}function e(b,c){b.data(i)&&(b.parent().html(b.attr("style",b.data(i).s||"")),c&&b[u](c),b.off(".i").unwrap(),a(v+'[for="'+b[0].id+'"]').add(b.closest(v)).off(".i"))}function f(a,b,c){return a.data(i)?a.data(i).o[b+(c?"":"Class")]:void 0}function g(a){return a.charAt(0).toUpperCase()+a.slice(1)}function h(a,b,c,d){d||(b&&a[u]("ifToggled"),a[u]("ifChanged")[u]("if"+g(c)))}var i="iCheck",j=i+"-helper",k="radio",l="checked",m="un"+l,n="disabled",o="determinate",p="in"+o,q="update",r="type",s="addClass",t="removeClass",u="trigger",v="label",w="cursor",x=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);a.fn[i]=function(f,g){var h='input[type="checkbox"], input[type="'+k+'"]',m=a(),o=function(b){b.each(function(){var b=a(this);m=b.is(h)?m.add(b):m.add(b.find(h))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(f))return f=f.toLowerCase(),o(this),m.each(function(){var c=a(this);"destroy"==f?e(c,"ifDestroyed"):b(c,!0,f),a.isFunction(g)&&g()});if("object"!=typeof f&&f)return this;var w=a.extend({checkedClass:l,disabledClass:n,indeterminateClass:p,labelHover:!0,aria:!1},f),y=w.handle,z=w.hoverClass||"hover",A=w.focusClass||"focus",B=w.activeClass||"active",C=!!w.labelHover,D=w.labelHoverClass||"hover",E=0|(""+w.increaseArea).replace("%","");return("checkbox"==y||y==k)&&(h='input[type="'+y+'"]'),-50>E&&(E=-50),o(this),m.each(function(){var f=a(this);e(f);var g=this,h=g.id,m=-E+"%",o=100+2*E+"%",o={position:"absolute",top:m,left:m,display:"block",width:o,height:o,margin:0,padding:0,background:"#fff",border:0,opacity:0},m=x?{position:"absolute",visibility:"hidden"}:E?o:{position:"absolute",opacity:0},p="checkbox"==g[r]?w.checkboxClass||"icheckbox":w.radioClass||"i"+k,y=a(v+'[for="'+h+'"]').add(f.closest(v)),F=!!w.aria,G=i+"-"+Math.random().toString(36).substr(2,6),H='<div class="'+p+'" '+(F?'role="'+g[r]+'" ':"");F&&y.each(function(){H+='aria-labelledby="',this.id?H+=this.id:(this.id=G,H+=G),H+='"'}),H=f.wrap(H+"/>")[u]("ifCreated").parent().append(w.insert),o=a('<ins class="'+j+'"/>').css(o).appendTo(H),f.data(i,{o:w,s:f.attr("style")}).css(m),w.inheritClass&&H[s](g.className||""),w.inheritID&&h&&H.attr("id",i+"-"+h),"static"==H.css("position")&&H.css("position","relative"),b(f,!0,q),y.length&&y.on("click.i mouseover.i mouseout.i touchbegin.i touchend.i",function(c){var d=c[r],e=a(this);if(!g[n]){if("click"==d){if(a(c.target).is("a"))return;b(f,!1,!0)}else C&&(/ut|nd/.test(d)?(H[t](z),e[t](D)):(H[s](z),e[s](D)));if(!x)return!1;c.stopPropagation()}}),f.on("click.i focus.i blur.i keyup.i keydown.i keypress.i",function(a){var b=a[r];return a=a.keyCode,"click"==b?!1:"keydown"==b&&32==a?(g[r]==k&&g[l]||(g[l]?d(f,l):c(f,l)),!1):("keyup"==b&&g[r]==k?!g[l]&&c(f,l):/us|ur/.test(b)&&H["blur"==b?t:s](A),void 0)}),o.on("click mousedown mouseup mouseover mouseout touchbegin.i touchend.i",function(a){var c=a[r],d=/wn|up/.test(c)?B:z;if(!g[n]){if("click"==c?b(f,!1,!0):(/wn|er|in/.test(c)?H[s](d):H[t](d+" "+B),y.length&&C&&d==z&&y[/ut|nd/.test(c)?t:s](D)),!x)return!1;a.stopPropagation()}})})}}(window.jQuery||window.Zepto);
}
/* 
 * Selecter v3.0.9 - 2014-02-10 
 * A jQuery plugin for replacing default select elements. Part of the Formstone Library. 
 * http://formstone.it/selecter/ 
 * 
 * Copyright 2014 Ben Plum; MIT Licensed 
 */
if ( 'function' != typeof(jQuery.fn.selecter) ) {
  !function(a,b){"use strict";function c(b){b=a.extend({},x,b||{}),null===w&&(w=a("body"));for(var c=a(this),e=0,f=c.length;f>e;e++)d(c.eq(e),b);return c}function d(b,c){if(!b.hasClass("selecter-element")){c=a.extend({},c,b.data("selecter-options")),c.external&&(c.links=!0);var d=b.find("option, optgroup"),g=d.filter("option"),h=g.filter(":selected"),n=""!==c.label?-1:g.index(h),p=c.links?"nav":"div";c.tabIndex=b[0].tabIndex,b[0].tabIndex=-1,c.multiple=b.prop("multiple"),c.disabled=b.is(":disabled");var q="<"+p+' class="selecter '+c.customClass;v?q+=" mobile":c.cover&&(q+=" cover"),q+=c.multiple?" multiple":" closed",c.disabled&&(q+=" disabled"),q+='" tabindex="'+c.tabIndex+'">',c.multiple||(q+='<span class="selecter-selected'+(""!==c.label?" placeholder":"")+'">',q+=a("<span></span").text(r(""!==c.label?c.label:h.text(),c.trim)).html(),q+="</span>"),q+='<div class="selecter-options">',q+="</div>",q+="</"+p+">",b.addClass("selecter-element").after(q);var s=b.next(".selecter"),u=a.extend({$select:b,$allOptions:d,$options:g,$selecter:s,$selected:s.find(".selecter-selected"),$itemsWrapper:s.find(".selecter-options"),index:-1,guid:t++},c);e(u),o(n,u),void 0!==a.fn.scroller&&u.$itemsWrapper.scroller(),u.$selecter.on("touchstart.selecter click.selecter",".selecter-selected",u,f).on("click.selecter",".selecter-item",u,j).on("close.selecter",u,i).data("selecter",u),u.$select.on("change.selecter",u,k),v||(u.$selecter.on("focus.selecter",u,l).on("blur.selecter",u,m),u.$select.on("focus.selecter",u,function(a){a.data.$selecter.trigger("focus")}))}}function e(b){for(var c="",d=b.links?"a":"span",e=0,f=0,g=b.$allOptions.length;g>f;f++){var h=b.$allOptions.eq(f);if("OPTGROUP"===h[0].tagName)c+='<span class="selecter-group',h.is(":disabled")&&(c+=" disabled"),c+='">'+h.attr("label")+"</span>";else{var i=h.val();h.attr("value")||h.attr("value",i),c+="<"+d+' class="selecter-item',h.is(":selected")&&""===b.label&&(c+=" selected"),h.is(":disabled")&&(c+=" disabled"),c+='" ',c+=b.links?'href="'+i+'"':'data-value="'+i+'"',c+=">"+a("<span></span>").text(r(h.text(),b.trim)).html()+"</"+d+">",e++}}b.$itemsWrapper.html(c),b.$items=b.$selecter.find(".selecter-item")}function f(c){c.preventDefault(),c.stopPropagation();var d=c.data;if(!d.$select.is(":disabled"))if(a(".selecter").not(d.$selecter).trigger("close.selecter",[d]),v){var e=d.$select[0];if(b.document.createEvent){var f=b.document.createEvent("MouseEvents");f.initMouseEvent("mousedown",!1,!0,b,0,0,0,0,0,!1,!1,!1,!1,0,null),e.dispatchEvent(f)}else e.fireEvent&&e.fireEvent("onmousedown")}else d.$selecter.hasClass("closed")?g(c):d.$selecter.hasClass("open")&&i(c)}function g(b){b.preventDefault(),b.stopPropagation();var c=b.data;if(!c.$selecter.hasClass("open")){var d=c.$selecter.offset(),e=w.outerHeight(),f=c.$itemsWrapper.outerHeight(!0),g=c.index>=0?c.$items.eq(c.index).position():{left:0,top:0};d.top+f>e&&c.$selecter.addClass("bottom"),c.$itemsWrapper.show(),c.$selecter.removeClass("closed").addClass("open"),w.on("click.selecter-"+c.guid,":not(.selecter-options)",c,h),void 0!==a.fn.scroller?c.$itemsWrapper.scroller("scroll",c.$itemsWrapper.find(".scroller-content").scrollTop()+g.top,0).scroller("reset"):c.$itemsWrapper.scrollTop(c.$itemsWrapper.scrollTop()+g.top)}}function h(b){b.preventDefault(),b.stopPropagation(),0===a(b.currentTarget).parents(".selecter").length&&i(b)}function i(a){a.preventDefault(),a.stopPropagation();var b=a.data;b.$selecter.hasClass("open")&&(b.$itemsWrapper.hide(),b.$selecter.removeClass("open bottom").addClass("closed"),w.off(".selecter-"+b.guid))}function j(b){b.preventDefault(),b.stopPropagation();var c=a(this),d=b.data;if(!d.$select.is(":disabled")){if(d.$itemsWrapper.is(":visible")){var e=d.$items.index(c);o(e,d),p(d)}d.multiple||i(b)}}function k(b,c){var d=a(this),e=b.data;if(!c&&!e.multiple){var f=e.$options.index(e.$options.filter("[value='"+s(d.val())+"']"));o(f,e),p(e)}}function l(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$select.is(":disabled")||c.multiple||(c.$selecter.addClass("focus").on("keydown.selecter"+c.guid,c,n),a(".selecter").not(c.$selecter).trigger("close.selecter",[c]))}function m(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$selecter.removeClass("focus").off("keydown.selecter"+c.guid+" keyup.selecter"+c.guid),a(".selecter").not(c.$selecter).trigger("close.selecter",[c])}function n(b){var c=b.data;if(13===b.keyCode)c.$selecter.hasClass("open")&&(i(b),o(c.index,c)),p(c);else if(!(9===b.keyCode||b.metaKey||b.altKey||b.ctrlKey||b.shiftKey)){b.preventDefault(),b.stopPropagation();var d=c.$items.length-1,e=c.index<0?0:c.index;if(a.inArray(b.keyCode,u?[38,40,37,39]:[38,40])>-1)e+=38===b.keyCode||u&&37===b.keyCode?-1:1,0>e&&(e=0),e>d&&(e=d);else{var f,g,h=String.fromCharCode(b.keyCode).toUpperCase();for(g=c.index+1;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}if(0>e)for(g=0;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}}e>=0&&o(e,c)}}function o(a,b){var c=b.$items.eq(a),d=c.hasClass("selected"),e=c.hasClass("disabled");if(!e){if(-1===a&&""!==b.label)b.$selected.html(b.label);else if(d)b.multiple&&(b.$options.eq(a).prop("selected",null),c.removeClass("selected"));else{{var f=c.html();c.data("value")}b.multiple?b.$options.eq(a).prop("selected",!0):(b.$selected.html(f).removeClass("placeholder"),b.$items.filter(".selected").removeClass("selected"),b.$select[0].selectedIndex=a),c.addClass("selected")}(!d||b.multiple)&&(b.index=a)}}function p(a){a.links?q(a):(a.callback.call(a.$selecter,a.$select.val(),a.index),a.$select.trigger("change",[!0]))}function q(a){var c=a.$select.val();a.external?b.open(c):b.location.href=c}function r(a,b){return 0===b?a:a.length>b?a.substring(0,b)+"...":a}function s(a){return a.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g,"\\$1")}var t=0,u=b.navigator.userAgent.toLowerCase().indexOf("firefox")>-1,v=/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(b.navigator.userAgent||b.navigator.vendor||b.opera),w=null,x={callback:a.noop,cover:!1,customClass:"",label:"",external:!1,links:!1,trim:0},y={defaults:function(b){return x=a.extend(x,b||{}),a(this)},disable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).addClass("disabled"),e.$options.eq(f).prop("disabled",!0)}else e.$selecter.hasClass("open")&&e.$selecter.find(".selecter-selected").trigger("click.selecter"),e.$selecter.addClass("disabled"),e.$select.prop("disabled",!0)})},enable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).removeClass("disabled"),e.$options.eq(f).prop("disabled",!1)}else e.$selecter.removeClass("disabled"),e.$select.prop("disabled",!1)})},destroy:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");d&&(d.$selecter.hasClass("open")&&d.$selecter.find(".selecter-selected").trigger("click.selecter"),void 0!==a.fn.scroller&&d.$selecter.find(".selecter-options").scroller("destroy"),d.$select[0].tabIndex=d.tabIndex,d.$select.off(".selecter").removeClass("selecter-element").show(),d.$selecter.off(".selecter").remove())})},refresh:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");if(d){var f=d.index;d.$allOptions=d.$select.find("option, optgroup"),d.$options=d.$allOptions.filter("option"),d.index=-1,f=d.$options.index(d.$options.filter(":selected")),e(d),o(f,d)}})}};a.fn.selecter=function(a){return y[a]?y[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:c.apply(this,arguments)},a.selecter=function(a){"defaults"===a&&y.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,window);
}
/* 
 * Stepper v3.0.5 - 2014-02-06 
 * A jQuery plugin for cross browser number inputs. Part of the Formstone Library. 
 * http://formstone.it/stepper/ 
 * 
 * Copyright 2014 Ben Plum; MIT Licensed 
 */
if ( 'function' != typeof(jQuery.fn.stepper) ) {
  !function(a){"use strict";function b(b){b=a.extend({},k,b||{});for(var d=a(this),e=0,f=d.length;f>e;e++)c(d.eq(e),b);return d}function c(b,c){if(!b.hasClass("stepper-input")){c=a.extend({},c,b.data("stepper-options"));var e=parseFloat(b.attr("min")),f=parseFloat(b.attr("max")),g=parseFloat(b.attr("step"))||1;b.addClass("stepper-input").wrap('<div class="stepper '+c.customClass+'" />').after('<span class="stepper-arrow up">'+c.labels.up+'</span><span class="stepper-arrow down">'+c.labels.down+"</span>");var h=b.parent(".stepper"),j=a.extend({$stepper:h,$input:b,$arrow:h.find(".stepper-arrow"),min:void 0===typeof e||isNaN(e)?!1:e,max:void 0===typeof f||isNaN(f)?!1:f,step:void 0===typeof g||isNaN(g)?1:g,timer:null},c);j.digits=i(j.step),b.is(":disabled")&&h.addClass("disabled"),h.on("touchstart.stepper mousedown.stepper",".stepper-arrow",j,d).data("stepper",j)}}function d(b){b.preventDefault(),b.stopPropagation(),e(b);var c=b.data;if(!c.$input.is(":disabled")&&!c.$stepper.hasClass("disabled")){var d=a(b.target).hasClass("up")?c.step:-c.step;c.timer=g(c.timer,125,function(){f(c,d,!1)}),f(c,d),a("body").on("touchend.stepper mouseup.stepper",c,e)}}function e(b){b.preventDefault(),b.stopPropagation();var c=b.data;h(c.timer),a("body").off(".stepper")}function f(a,b){var c=parseFloat(a.$input.val()),d=b;void 0===typeof c||isNaN(c)?d=a.min!==!1?a.min:0:a.min!==!1&&c<a.min?d=a.min:d+=c;var e=(d-a.min)%a.step;0!==e&&(d-=e),a.min!==!1&&d<a.min&&(d=a.min),a.max!==!1&&d>a.max&&(d-=a.step),d!==c&&(d=j(d,a.digits),a.$input.val(d).trigger("change"))}function g(a,b,c){return h(a),setInterval(c,b)}function h(a){a&&(clearInterval(a),a=null)}function i(a){var b=String(a);return b.indexOf(".")>-1?b.length-b.indexOf(".")-1:0}function j(a,b){var c=Math.pow(10,b);return Math.round(a*c)/c}var k={customClass:"",labels:{up:"Up",down:"Down"}},l={defaults:function(b){return k=a.extend(k,b||{}),a(this)},destroy:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$stepper.off(".stepper").find(".stepper-arrow").remove(),b.$input.unwrap().removeClass("stepper-input"))})},disable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled","disabled"),b.$stepper.addClass("disabled"))})},enable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled",null),b.$stepper.removeClass("disabled"))})}};a.fn.stepper=function(a){return l[a]?l[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:b.apply(this,arguments)},a.stepper=function(a){"defaults"===a&&l.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,this);
}/*
Copyright 2012 Igor Vaynberg

Version: @@ver@@ Timestamp: @@timestamp@@

This software is licensed under the Apache License, Version 2.0 (the "Apache License") or the GNU
General Public License version 2 (the "GPL License"). You may choose either license to govern your
use of this software only upon the condition that you accept all of the terms of either the Apache
License or the GPL License.

You may obtain a copy of the Apache License and the GPL License at:

    http://www.apache.org/licenses/LICENSE-2.0
    http://www.gnu.org/licenses/gpl-2.0.html

Unless required by applicable law or agreed to in writing, software distributed under the
Apache License or the GPL License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the Apache License and the GPL License for
the specific language governing permissions and limitations under the Apache License and the GPL License.
*/
!function(a){"undefined"==typeof a.fn.each2&&a.extend(a.fn,{each2:function(b){for(var c=a([0]),d=-1,e=this.length;++d<e&&(c.context=c[0]=this[d])&&b.call(c[0],d,c)!==!1;);return this}})}(jQuery),function(a,b){"use strict";function c(b){var c=a(document.createTextNode(""));b.before(c),c.before(b),c.remove()}function d(a){function b(a){return O[a]||a}return a.replace(/[^\u0000-\u007E]/g,b)}function e(a,b){for(var c=0,d=b.length;d>c;c+=1)if(g(a,b[c]))return c;return-1}function f(){var b=a(N);b.appendTo(document.body);var c={width:b.width()-b[0].clientWidth,height:b.height()-b[0].clientHeight};return b.remove(),c}function g(a,c){return a===c?!0:a===b||c===b?!1:null===a||null===c?!1:a.constructor===String?a+""==c+"":c.constructor===String?c+""==a+"":!1}function h(a,b,c){var d,e,f;if(null===a||a.length<1)return[];for(d=a.split(b),e=0,f=d.length;f>e;e+=1)d[e]=c(d[e]);return d}function i(a){return a.outerWidth(!1)-a.width()}function j(c){var d="keyup-change-value";c.on("keydown",function(){a.data(c,d)===b&&a.data(c,d,c.val())}),c.on("keyup",function(){var e=a.data(c,d);e!==b&&c.val()!==e&&(a.removeData(c,d),c.trigger("keyup-change"))})}function k(c){c.on("mousemove",function(c){var d=L;(d===b||d.x!==c.pageX||d.y!==c.pageY)&&a(c.target).trigger("mousemove-filtered",c)})}function l(a,c,d){d=d||b;var e;return function(){var b=arguments;window.clearTimeout(e),e=window.setTimeout(function(){c.apply(d,b)},a)}}function m(a,b){var c=l(a,function(a){b.trigger("scroll-debounced",a)});b.on("scroll",function(a){e(a.target,b.get())>=0&&c(a)})}function n(a){a[0]!==document.activeElement&&window.setTimeout(function(){var b,c=a[0],d=a.val().length;a.focus();var e=c.offsetWidth>0||c.offsetHeight>0;e&&c===document.activeElement&&(c.setSelectionRange?c.setSelectionRange(d,d):c.createTextRange&&(b=c.createTextRange(),b.collapse(!1),b.select()))},0)}function o(b){b=a(b)[0];var c=0,d=0;if("selectionStart"in b)c=b.selectionStart,d=b.selectionEnd-c;else if("selection"in document){b.focus();var e=document.selection.createRange();d=document.selection.createRange().text.length,e.moveStart("character",-b.value.length),c=e.text.length-d}return{offset:c,length:d}}function p(a){a.preventDefault(),a.stopPropagation()}function q(a){a.preventDefault(),a.stopImmediatePropagation()}function r(b){if(!I){var c=b[0].currentStyle||window.getComputedStyle(b[0],null);I=a(document.createElement("div")).css({position:"absolute",left:"-10000px",top:"-10000px",display:"none",fontSize:c.fontSize,fontFamily:c.fontFamily,fontStyle:c.fontStyle,fontWeight:c.fontWeight,letterSpacing:c.letterSpacing,textTransform:c.textTransform,whiteSpace:"nowrap"}),I.attr("class","select2-sizer"),a(document.body).append(I)}return I.text(b.val()),I.width()}function s(b,c,d){var e,f,g=[];e=a.trim(b.attr("class")),e&&(e=""+e,a(e.split(/\s+/)).each2(function(){0===this.indexOf("select2-")&&g.push(this)})),e=a.trim(c.attr("class")),e&&(e=""+e,a(e.split(/\s+/)).each2(function(){0!==this.indexOf("select2-")&&(f=d(this),f&&g.push(f))})),b.attr("class",g.join(" "))}function t(a,b,c,e){var f=d(a.toUpperCase()).indexOf(d(b.toUpperCase())),g=b.length;return 0>f?void c.push(e(a)):(c.push(e(a.substring(0,f))),c.push("<span class='select2-match'>"),c.push(e(a.substring(f,f+g))),c.push("</span>"),void c.push(e(a.substring(f+g,a.length))))}function u(a){var b={"\\":"&#92;","&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#47;"};return String(a).replace(/[&<>"'\/\\]/g,function(a){return b[a]})}function v(c){var d,e=null,f=c.quietMillis||100,g=c.url,h=this;return function(i){window.clearTimeout(d),d=window.setTimeout(function(){var d=c.data,f=g,j=c.transport||a.fn.select2.ajaxDefaults.transport,k={type:c.type||"GET",cache:c.cache||!1,jsonpCallback:c.jsonpCallback||b,dataType:c.dataType||"json"},l=a.extend({},a.fn.select2.ajaxDefaults.params,k);d=d?d.call(h,i.term,i.page,i.context):null,f="function"==typeof f?f.call(h,i.term,i.page,i.context):f,e&&"function"==typeof e.abort&&e.abort(),c.params&&(a.isFunction(c.params)?a.extend(l,c.params.call(h)):a.extend(l,c.params)),a.extend(l,{url:f,dataType:c.dataType,data:d,success:function(a){var b=c.results(a,i.page,i);i.callback(b)},error:function(a,b,c){var d={hasError:!0,jqXHR:a,textStatus:b,errorThrown:c};i.callback(d)}}),e=j.call(h,l)},f)}}function w(b){var c,d,e=b,f=function(a){return""+a.text};a.isArray(e)&&(d=e,e={results:d}),a.isFunction(e)===!1&&(d=e,e=function(){return d});var g=e();return g.text&&(f=g.text,a.isFunction(f)||(c=g.text,f=function(a){return a[c]})),function(b){var c,d=b.term,g={results:[]};return""===d?void b.callback(e()):(c=function(e,g){var h,i;if(e=e[0],e.children){h={};for(i in e)e.hasOwnProperty(i)&&(h[i]=e[i]);h.children=[],a(e.children).each2(function(a,b){c(b,h.children)}),(h.children.length||b.matcher(d,f(h),e))&&g.push(h)}else b.matcher(d,f(e),e)&&g.push(e)},a(e().results).each2(function(a,b){c(b,g.results)}),void b.callback(g))}}function x(c){var d=a.isFunction(c);return function(e){var f=e.term,g={results:[]},h=d?c(e):c;a.isArray(h)&&(a(h).each(function(){var a=this.text!==b,c=a?this.text:this;(""===f||e.matcher(f,c))&&g.results.push(a?this:{id:this,text:this})}),e.callback(g))}}function y(b,c){if(a.isFunction(b))return!0;if(!b)return!1;if("string"==typeof b)return!0;throw new Error(c+" must be a string, function, or falsy value")}function z(b,c){if(a.isFunction(b)){var d=Array.prototype.slice.call(arguments,2);return b.apply(c,d)}return b}function A(b){var c=0;return a.each(b,function(a,b){b.children?c+=A(b.children):c++}),c}function B(a,c,d,e){var f,h,i,j,k,l=a,m=!1;if(!e.createSearchChoice||!e.tokenSeparators||e.tokenSeparators.length<1)return b;for(;;){for(h=-1,i=0,j=e.tokenSeparators.length;j>i&&(k=e.tokenSeparators[i],h=a.indexOf(k),!(h>=0));i++);if(0>h)break;if(f=a.substring(0,h),a=a.substring(h+k.length),f.length>0&&(f=e.createSearchChoice.call(this,f,c),f!==b&&null!==f&&e.id(f)!==b&&null!==e.id(f))){for(m=!1,i=0,j=c.length;j>i;i++)if(g(e.id(f),e.id(c[i]))){m=!0;break}m||d(f)}}return l!==a?a:void 0}function C(){var b=this;a.each(arguments,function(a,c){b[c].remove(),b[c]=null})}function D(b,c){var d=function(){};return d.prototype=new b,d.prototype.constructor=d,d.prototype.parent=b.prototype,d.prototype=a.extend(d.prototype,c),d}if(window.Select2===b){var E,F,G,H,I,J,K,L={x:0,y:0},M={TAB:9,ENTER:13,ESC:27,SPACE:32,LEFT:37,UP:38,RIGHT:39,DOWN:40,SHIFT:16,CTRL:17,ALT:18,PAGE_UP:33,PAGE_DOWN:34,HOME:36,END:35,BACKSPACE:8,DELETE:46,isArrow:function(a){switch(a=a.which?a.which:a){case M.LEFT:case M.RIGHT:case M.UP:case M.DOWN:return!0}return!1},isControl:function(a){var b=a.which;switch(b){case M.SHIFT:case M.CTRL:case M.ALT:return!0}return a.metaKey?!0:!1},isFunctionKey:function(a){return a=a.which?a.which:a,a>=112&&123>=a}},N="<div class='select2-measure-scrollbar'></div>",O={"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S","Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z","Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n","ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ":"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α","Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ω":"ω","ς":"σ"};J=a(document),H=function(){var a=1;return function(){return a++}}(),E=D(Object,{bind:function(a){var b=this;return function(){a.apply(b,arguments)}},init:function(c){var d,e,g=".select2-results";this.opts=c=this.prepareOpts(c),this.id=c.id,c.element.data("select2")!==b&&null!==c.element.data("select2")&&c.element.data("select2").destroy(),this.container=this.createContainer(),this.liveRegion=a(".select2-hidden-accessible"),0==this.liveRegion.length&&(this.liveRegion=a("<span>",{role:"status","aria-live":"polite"}).addClass("select2-hidden-accessible").appendTo(document.body)),this.containerId="s2id_"+(c.element.attr("id")||"autogen"+H()),this.containerEventName=this.containerId.replace(/([.])/g,"_").replace(/([;&,\-\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g,"\\$1"),this.container.attr("id",this.containerId),this.container.attr("title",c.element.attr("title")),this.body=a(document.body),s(this.container,this.opts.element,this.opts.adaptContainerCssClass),this.container.attr("style",c.element.attr("style")),this.container.css(z(c.containerCss,this.opts.element)),this.container.addClass(z(c.containerCssClass,this.opts.element)),this.elementTabIndex=this.opts.element.attr("tabindex"),this.opts.element.data("select2",this).attr("tabindex","-1").before(this.container).on("click.select2",p),this.container.data("select2",this),this.dropdown=this.container.find(".select2-drop"),s(this.dropdown,this.opts.element,this.opts.adaptDropdownCssClass),this.dropdown.addClass(z(c.dropdownCssClass,this.opts.element)),this.dropdown.data("select2",this),this.dropdown.on("click",p),this.results=d=this.container.find(g),this.search=e=this.container.find("input.select2-input"),this.queryCount=0,this.resultsPage=0,this.context=null,this.initContainer(),this.container.on("click",p),k(this.results),this.dropdown.on("mousemove-filtered",g,this.bind(this.highlightUnderEvent)),this.dropdown.on("touchstart touchmove touchend",g,this.bind(function(a){this._touchEvent=!0,this.highlightUnderEvent(a)})),this.dropdown.on("touchmove",g,this.bind(this.touchMoved)),this.dropdown.on("touchstart touchend",g,this.bind(this.clearTouchMoved)),this.dropdown.on("click",this.bind(function(){this._touchEvent&&(this._touchEvent=!1,this.selectHighlighted())})),m(80,this.results),this.dropdown.on("scroll-debounced",g,this.bind(this.loadMoreIfNeeded)),a(this.container).on("change",".select2-input",function(a){a.stopPropagation()}),a(this.dropdown).on("change",".select2-input",function(a){a.stopPropagation()}),a.fn.mousewheel&&d.mousewheel(function(a,b,c,e){var f=d.scrollTop();e>0&&0>=f-e?(d.scrollTop(0),p(a)):0>e&&d.get(0).scrollHeight-d.scrollTop()+e<=d.height()&&(d.scrollTop(d.get(0).scrollHeight-d.height()),p(a))}),j(e),e.on("keyup-change input paste",this.bind(this.updateResults)),e.on("focus",function(){e.addClass("select2-focused")}),e.on("blur",function(){e.removeClass("select2-focused")}),this.dropdown.on("mouseup",g,this.bind(function(b){a(b.target).closest(".select2-result-selectable").length>0&&(this.highlightUnderEvent(b),this.selectHighlighted(b))})),this.dropdown.on("click mouseup mousedown touchstart touchend focusin",function(a){a.stopPropagation()}),this.lastSearchTerm=b,a.isFunction(this.opts.initSelection)&&(this.initSelection(),this.monitorSource()),null!==c.maximumInputLength&&this.search.attr("maxlength",c.maximumInputLength);var h=c.element.prop("disabled");h===b&&(h=!1),this.enable(!h);var i=c.element.prop("readonly");i===b&&(i=!1),this.readonly(i),K=K||f(),this.autofocus=c.element.prop("autofocus"),c.element.prop("autofocus",!1),this.autofocus&&this.focus(),this.search.attr("placeholder",c.searchInputPlaceholder)},destroy:function(){var a=this.opts.element,c=a.data("select2"),d=this;this.close(),a.length&&a[0].detachEvent&&d._sync&&a.each(function(){d._sync&&this.detachEvent("onpropertychange",d._sync)}),this.propertyObserver&&(this.propertyObserver.disconnect(),this.propertyObserver=null),this._sync=null,c!==b&&(c.container.remove(),c.liveRegion.remove(),c.dropdown.remove(),a.show().removeData("select2").off(".select2").prop("autofocus",this.autofocus||!1),this.elementTabIndex?a.attr({tabindex:this.elementTabIndex}):a.removeAttr("tabindex"),a.show()),C.call(this,"container","liveRegion","dropdown","results","search")},optionToData:function(a){return a.is("option")?{id:a.prop("value"),text:a.text(),element:a.get(),css:a.attr("class"),disabled:a.prop("disabled"),locked:g(a.attr("locked"),"locked")||g(a.data("locked"),!0)}:a.is("optgroup")?{text:a.attr("label"),children:[],element:a.get(),css:a.attr("class")}:void 0},prepareOpts:function(c){var d,e,f,i,j=this;if(d=c.element,"select"===d.get(0).tagName.toLowerCase()&&(this.select=e=c.element),e&&a.each(["id","multiple","ajax","query","createSearchChoice","initSelection","data","tags"],function(){if(this in c)throw new Error("Option '"+this+"' is not allowed for Select2 when attached to a <select> element.")}),c=a.extend({},{populateResults:function(d,e,f){var g,h=this.opts.id,i=this.liveRegion;(g=function(d,e,k){var l,m,n,o,p,q,r,s,t,u;d=c.sortResults(d,e,f);var v=[];for(l=0,m=d.length;m>l;l+=1)n=d[l],p=n.disabled===!0,o=!p&&h(n)!==b,q=n.children&&n.children.length>0,r=a("<li></li>"),r.addClass("select2-results-dept-"+k),r.addClass("select2-result"),r.addClass(o?"select2-result-selectable":"select2-result-unselectable"),p&&r.addClass("select2-disabled"),q&&r.addClass("select2-result-with-children"),r.addClass(j.opts.formatResultCssClass(n)),r.attr("role","presentation"),s=a(document.createElement("div")),s.addClass("select2-result-label"),s.attr("id","select2-result-label-"+H()),s.attr("role","option"),u=c.formatResult(n,s,f,j.opts.escapeMarkup),u!==b&&(s.html(u),r.append(s)),q&&(t=a("<ul></ul>"),t.addClass("select2-result-sub"),g(n.children,t,k+1),r.append(t)),r.data("select2-data",n),v.push(r[0]);e.append(v),i.text(c.formatMatches(d.length))})(e,d,0)}},a.fn.select2.defaults,c),"function"!=typeof c.id&&(f=c.id,c.id=function(a){return a[f]}),a.isArray(c.element.data("select2Tags"))){if("tags"in c)throw"tags specified as both an attribute 'data-select2-tags' and in options of Select2 "+c.element.attr("id");c.tags=c.element.data("select2Tags")}if(e?(c.query=this.bind(function(a){var c,e,f,g={results:[],more:!1},h=a.term;f=function(b,c){var d;b.is("option")?a.matcher(h,b.text(),b)&&c.push(j.optionToData(b)):b.is("optgroup")&&(d=j.optionToData(b),b.children().each2(function(a,b){f(b,d.children)}),d.children.length>0&&c.push(d))},c=d.children(),this.getPlaceholder()!==b&&c.length>0&&(e=this.getPlaceholderOption(),e&&(c=c.not(e))),c.each2(function(a,b){f(b,g.results)}),a.callback(g)}),c.id=function(a){return a.id}):"query"in c||("ajax"in c?(i=c.element.data("ajax-url"),i&&i.length>0&&(c.ajax.url=i),c.query=v.call(c.element,c.ajax)):"data"in c?c.query=w(c.data):"tags"in c&&(c.query=x(c.tags),c.createSearchChoice===b&&(c.createSearchChoice=function(b){return{id:a.trim(b),text:a.trim(b)}}),c.initSelection===b&&(c.initSelection=function(b,d){var e=[];a(h(b.val(),c.separator,c.transformVal)).each(function(){var b={id:this,text:this},d=c.tags;a.isFunction(d)&&(d=d()),a(d).each(function(){return g(this.id,b.id)?(b=this,!1):void 0}),e.push(b)}),d(e)}))),"function"!=typeof c.query)throw"query function not defined for Select2 "+c.element.attr("id");if("top"===c.createSearchChoicePosition)c.createSearchChoicePosition=function(a,b){a.unshift(b)};else if("bottom"===c.createSearchChoicePosition)c.createSearchChoicePosition=function(a,b){a.push(b)};else if("function"!=typeof c.createSearchChoicePosition)throw"invalid createSearchChoicePosition option must be 'top', 'bottom' or a custom function";return c},monitorSource:function(){var c,d=this.opts.element,e=this;d.on("change.select2",this.bind(function(){this.opts.element.data("select2-change-triggered")!==!0&&this.initSelection()})),this._sync=this.bind(function(){var a=d.prop("disabled");a===b&&(a=!1),this.enable(!a);var c=d.prop("readonly");c===b&&(c=!1),this.readonly(c),this.container&&(s(this.container,this.opts.element,this.opts.adaptContainerCssClass),this.container.addClass(z(this.opts.containerCssClass,this.opts.element))),this.dropdown&&(s(this.dropdown,this.opts.element,this.opts.adaptDropdownCssClass),this.dropdown.addClass(z(this.opts.dropdownCssClass,this.opts.element)))}),d.length&&d[0].attachEvent&&d.each(function(){this.attachEvent("onpropertychange",e._sync)}),c=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver,c!==b&&(this.propertyObserver&&(delete this.propertyObserver,this.propertyObserver=null),this.propertyObserver=new c(function(b){a.each(b,e._sync)}),this.propertyObserver.observe(d.get(0),{attributes:!0,subtree:!1}))},triggerSelect:function(b){var c=a.Event("select2-selecting",{val:this.id(b),object:b,choice:b});return this.opts.element.trigger(c),!c.isDefaultPrevented()},triggerChange:function(b){b=b||{},b=a.extend({},b,{type:"change",val:this.val()}),this.opts.element.data("select2-change-triggered",!0),this.opts.element.trigger(b),this.opts.element.data("select2-change-triggered",!1),this.opts.element.click(),this.opts.blurOnChange&&this.opts.element.blur()},isInterfaceEnabled:function(){return this.enabledInterface===!0},enableInterface:function(){var a=this._enabled&&!this._readonly,b=!a;return a===this.enabledInterface?!1:(this.container.toggleClass("select2-container-disabled",b),this.close(),this.enabledInterface=a,!0)},enable:function(a){a===b&&(a=!0),this._enabled!==a&&(this._enabled=a,this.opts.element.prop("disabled",!a),this.enableInterface())},disable:function(){this.enable(!1)},readonly:function(a){a===b&&(a=!1),this._readonly!==a&&(this._readonly=a,this.opts.element.prop("readonly",a),this.enableInterface())},opened:function(){return this.container?this.container.hasClass("select2-dropdown-open"):!1},positionDropdown:function(){var b,c,d,e,f,g=this.dropdown,h=this.container,i=h.offset(),j=h.outerHeight(!1),k=h.outerWidth(!1),l=g.outerHeight(!1),m=a(window),n=m.width(),o=m.height(),p=m.scrollLeft()+n,q=m.scrollTop()+o,r=i.top+j,s=i.left,t=q>=r+l,u=i.top-l>=m.scrollTop(),v=g.outerWidth(!1),w=function(){return p>=s+v},x=function(){return i.left+p+h.outerWidth(!1)>v},y=g.hasClass("select2-drop-above");y?(c=!0,!u&&t&&(d=!0,c=!1)):(c=!1,!t&&u&&(d=!0,c=!0)),d&&(g.hide(),i=this.container.offset(),j=this.container.outerHeight(!1),k=this.container.outerWidth(!1),l=g.outerHeight(!1),p=m.scrollLeft()+n,q=m.scrollTop()+o,r=i.top+j,s=i.left,v=g.outerWidth(!1),g.show(),this.focusSearch()),this.opts.dropdownAutoWidth?(f=a(".select2-results",g)[0],g.addClass("select2-drop-auto-width"),g.css("width",""),v=g.outerWidth(!1)+(f.scrollHeight===f.clientHeight?0:K.width),v>k?k=v:v=k,l=g.outerHeight(!1)):this.container.removeClass("select2-drop-auto-width"),"static"!==this.body.css("position")&&(b=this.body.offset(),r-=b.top,s-=b.left),!w()&&x()&&(s=i.left+this.container.outerWidth(!1)-v),e={left:s,width:k},c?(this.container.addClass("select2-drop-above"),g.addClass("select2-drop-above"),l=g.outerHeight(!1),e.top=i.top-l,e.bottom="auto"):(e.top=r,e.bottom="auto",this.container.removeClass("select2-drop-above"),g.removeClass("select2-drop-above")),e=a.extend(e,z(this.opts.dropdownCss,this.opts.element)),g.css(e)},shouldOpen:function(){var b;return this.opened()?!1:this._enabled===!1||this._readonly===!0?!1:(b=a.Event("select2-opening"),this.opts.element.trigger(b),!b.isDefaultPrevented())},clearDropdownAlignmentPreference:function(){this.container.removeClass("select2-drop-above"),this.dropdown.removeClass("select2-drop-above")},open:function(){return this.shouldOpen()?(this.opening(),J.on("mousemove.select2Event",function(a){L.x=a.pageX,L.y=a.pageY}),!0):!1},opening:function(){var b,d=this.containerEventName,e="scroll."+d,f="resize."+d,g="orientationchange."+d;this.container.addClass("select2-dropdown-open").addClass("select2-container-active"),this.clearDropdownAlignmentPreference(),this.dropdown[0]!==this.body.children().last()[0]&&this.dropdown.detach().appendTo(this.body),b=a("#select2-drop-mask"),0===b.length&&(b=a(document.createElement("div")),b.attr("id","select2-drop-mask").attr("class","select2-drop-mask"),b.hide(),b.appendTo(this.body),b.on("mousedown touchstart click",function(d){c(b);var e,f=a("#select2-drop");f.length>0&&(e=f.data("select2"),e.opts.selectOnBlur&&e.selectHighlighted({noFocus:!0}),e.close(),d.preventDefault(),d.stopPropagation())})),this.dropdown.prev()[0]!==b[0]&&this.dropdown.before(b),a("#select2-drop").removeAttr("id"),this.dropdown.attr("id","select2-drop"),b.show(),this.positionDropdown(),this.dropdown.show(),this.positionDropdown(),this.dropdown.addClass("select2-drop-active");var h=this;this.container.parents().add(window).each(function(){a(this).on(f+" "+e+" "+g,function(){h.opened()&&h.positionDropdown()})})},close:function(){if(this.opened()){var b=this.containerEventName,c="scroll."+b,d="resize."+b,e="orientationchange."+b;this.container.parents().add(window).each(function(){a(this).off(c).off(d).off(e)}),this.clearDropdownAlignmentPreference(),a("#select2-drop-mask").hide(),this.dropdown.removeAttr("id"),this.dropdown.hide(),this.container.removeClass("select2-dropdown-open").removeClass("select2-container-active"),this.results.empty(),J.off("mousemove.select2Event"),this.clearSearch(),this.search.removeClass("select2-active"),this.opts.element.trigger(a.Event("select2-close"))}},externalSearch:function(a){this.open(),this.search.val(a),this.updateResults(!1)},clearSearch:function(){},prefillNextSearchTerm:function(){if(""!==this.search.val())return!1;var a=this.opts.nextSearchTerm(this.data(),this.lastSearchTerm);return a!==b?(this.search.val(a),this.search.select(),!0):!1},getMaximumSelectionSize:function(){return z(this.opts.maximumSelectionSize,this.opts.element)},ensureHighlightVisible:function(){var b,c,d,e,f,g,h,i,j=this.results;if(c=this.highlight(),!(0>c)){if(0==c)return void j.scrollTop(0);b=this.findHighlightableChoices().find(".select2-result-label"),d=a(b[c]),i=(d.offset()||{}).top||0,e=i+d.outerHeight(!0),c===b.length-1&&(h=j.find("li.select2-more-results"),h.length>0&&(e=h.offset().top+h.outerHeight(!0))),f=j.offset().top+j.outerHeight(!1),e>f&&j.scrollTop(j.scrollTop()+(e-f)),g=i-j.offset().top,0>g&&"none"!=d.css("display")&&j.scrollTop(j.scrollTop()+g)}},findHighlightableChoices:function(){return this.results.find(".select2-result-selectable:not(.select2-disabled):not(.select2-selected)")},moveHighlight:function(b){for(var c=this.findHighlightableChoices(),d=this.highlight();d>-1&&d<c.length;){d+=b;var e=a(c[d]);if(e.hasClass("select2-result-selectable")&&!e.hasClass("select2-disabled")&&!e.hasClass("select2-selected")){this.highlight(d);break}}},highlight:function(b){var c,d,f=this.findHighlightableChoices();return 0===arguments.length?e(f.filter(".select2-highlighted")[0],f.get()):(b>=f.length&&(b=f.length-1),0>b&&(b=0),this.removeHighlight(),c=a(f[b]),c.addClass("select2-highlighted"),this.search.attr("aria-activedescendant",c.find(".select2-result-label").attr("id")),this.ensureHighlightVisible(),this.liveRegion.text(c.text()),d=c.data("select2-data"),void(d&&this.opts.element.trigger({type:"select2-highlight",val:this.id(d),choice:d})))},removeHighlight:function(){this.results.find(".select2-highlighted").removeClass("select2-highlighted")},touchMoved:function(){this._touchMoved=!0},clearTouchMoved:function(){this._touchMoved=!1},countSelectableResults:function(){return this.findHighlightableChoices().length},highlightUnderEvent:function(b){var c=a(b.target).closest(".select2-result-selectable");if(c.length>0&&!c.is(".select2-highlighted")){var d=this.findHighlightableChoices();this.highlight(d.index(c))}else 0==c.length&&this.removeHighlight()},loadMoreIfNeeded:function(){var a,b=this.results,c=b.find("li.select2-more-results"),d=this.resultsPage+1,e=this,f=this.search.val(),g=this.context;0!==c.length&&(a=c.offset().top-b.offset().top-b.height(),a<=this.opts.loadMorePadding&&(c.addClass("select2-active"),this.opts.query({element:this.opts.element,term:f,page:d,context:g,matcher:this.opts.matcher,callback:this.bind(function(a){e.opened()&&(e.opts.populateResults.call(this,b,a.results,{term:f,page:d,context:g}),e.postprocessResults(a,!1,!1),a.more===!0?(c.detach().appendTo(b).html(e.opts.escapeMarkup(z(e.opts.formatLoadMore,e.opts.element,d+1))),window.setTimeout(function(){e.loadMoreIfNeeded()},10)):c.remove(),e.positionDropdown(),e.resultsPage=d,e.context=a.context,this.opts.element.trigger({type:"select2-loaded",items:a}))})})))},tokenize:function(){},updateResults:function(c){function d(){j.removeClass("select2-active"),m.positionDropdown(),m.liveRegion.text(k.find(".select2-no-results,.select2-selection-limit,.select2-searching").length?k.text():m.opts.formatMatches(k.find('.select2-result-selectable:not(".select2-selected")').length))}function e(a){k.html(a),d()}var f,h,i,j=this.search,k=this.results,l=this.opts,m=this,n=j.val(),o=a.data(this.container,"select2-last-term");if((c===!0||!o||!g(n,o))&&(a.data(this.container,"select2-last-term",n),c===!0||this.showSearchInput!==!1&&this.opened())){i=++this.queryCount;var p=this.getMaximumSelectionSize();if(p>=1&&(f=this.data(),a.isArray(f)&&f.length>=p&&y(l.formatSelectionTooBig,"formatSelectionTooBig")))return void e("<li class='select2-selection-limit'>"+z(l.formatSelectionTooBig,l.element,p)+"</li>");if(j.val().length<l.minimumInputLength)return e(y(l.formatInputTooShort,"formatInputTooShort")?"<li class='select2-no-results'>"+z(l.formatInputTooShort,l.element,j.val(),l.minimumInputLength)+"</li>":""),void(c&&this.showSearch&&this.showSearch(!0));if(l.maximumInputLength&&j.val().length>l.maximumInputLength)return void e(y(l.formatInputTooLong,"formatInputTooLong")?"<li class='select2-no-results'>"+z(l.formatInputTooLong,l.element,j.val(),l.maximumInputLength)+"</li>":"");l.formatSearching&&0===this.findHighlightableChoices().length&&e("<li class='select2-searching'>"+z(l.formatSearching,l.element)+"</li>"),j.addClass("select2-active"),this.removeHighlight(),h=this.tokenize(),h!=b&&null!=h&&j.val(h),this.resultsPage=1,l.query({element:l.element,term:j.val(),page:this.resultsPage,context:null,matcher:l.matcher,callback:this.bind(function(f){var h;if(i==this.queryCount){if(!this.opened())return void this.search.removeClass("select2-active");if(f.hasError!==b&&y(l.formatAjaxError,"formatAjaxError"))return void e("<li class='select2-ajax-error'>"+z(l.formatAjaxError,l.element,f.jqXHR,f.textStatus,f.errorThrown)+"</li>");
if(this.context=f.context===b?null:f.context,this.opts.createSearchChoice&&""!==j.val()&&(h=this.opts.createSearchChoice.call(m,j.val(),f.results),h!==b&&null!==h&&m.id(h)!==b&&null!==m.id(h)&&0===a(f.results).filter(function(){return g(m.id(this),m.id(h))}).length&&this.opts.createSearchChoicePosition(f.results,h)),0===f.results.length&&y(l.formatNoMatches,"formatNoMatches"))return void e("<li class='select2-no-results'>"+z(l.formatNoMatches,l.element,j.val())+"</li>");k.empty(),m.opts.populateResults.call(this,k,f.results,{term:j.val(),page:this.resultsPage,context:null}),f.more===!0&&y(l.formatLoadMore,"formatLoadMore")&&(k.append("<li class='select2-more-results'>"+l.escapeMarkup(z(l.formatLoadMore,l.element,this.resultsPage))+"</li>"),window.setTimeout(function(){m.loadMoreIfNeeded()},10)),this.postprocessResults(f,c),d(),this.opts.element.trigger({type:"select2-loaded",items:f})}})})}},cancel:function(){this.close()},blur:function(){this.opts.selectOnBlur&&this.selectHighlighted({noFocus:!0}),this.close(),this.container.removeClass("select2-container-active"),this.search[0]===document.activeElement&&this.search.blur(),this.clearSearch(),this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus")},focusSearch:function(){n(this.search)},selectHighlighted:function(a){if(this._touchMoved)return void this.clearTouchMoved();var b=this.highlight(),c=this.results.find(".select2-highlighted"),d=c.closest(".select2-result").data("select2-data");d?(this.highlight(b),this.onSelect(d,a)):a&&a.noFocus&&this.close()},getPlaceholder:function(){var a;return this.opts.element.attr("placeholder")||this.opts.element.attr("data-placeholder")||this.opts.element.data("placeholder")||this.opts.placeholder||((a=this.getPlaceholderOption())!==b?a.text():b)},getPlaceholderOption:function(){if(this.select){var c=this.select.children("option").first();if(this.opts.placeholderOption!==b)return"first"===this.opts.placeholderOption&&c||"function"==typeof this.opts.placeholderOption&&this.opts.placeholderOption(this.select);if(""===a.trim(c.text())&&""===c.val())return c}},initContainerWidth:function(){function c(){var c,d,e,f,g,h;if("off"===this.opts.width)return null;if("element"===this.opts.width)return 0===this.opts.element.outerWidth(!1)?"auto":this.opts.element.outerWidth(!1)+"px";if("copy"===this.opts.width||"resolve"===this.opts.width){if(c=this.opts.element.attr("style"),c!==b)for(d=c.split(";"),f=0,g=d.length;g>f;f+=1)if(h=d[f].replace(/\s/g,""),e=h.match(/^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i),null!==e&&e.length>=1)return e[1];return"resolve"===this.opts.width?(c=this.opts.element.css("width"),c.indexOf("%")>0?c:0===this.opts.element.outerWidth(!1)?"auto":this.opts.element.outerWidth(!1)+"px"):null}return a.isFunction(this.opts.width)?this.opts.width():this.opts.width}var d=c.call(this);null!==d&&this.container.css("width",d)}}),F=D(E,{createContainer:function(){var b=a(document.createElement("div")).attr({"class":"select2-container"}).html(["<a href='javascript:void(0)' class='select2-choice' tabindex='-1'>","   <span class='select2-chosen'>&#160;</span><abbr class='select2-search-choice-close'></abbr>","   <span class='select2-arrow' role='presentation'><b role='presentation'></b></span>","</a>","<label for='' class='select2-offscreen'></label>","<input class='select2-focusser select2-offscreen' type='text' aria-haspopup='true' role='button' />","<div class='select2-drop select2-display-none'>","   <div class='select2-search'>","       <label for='' class='select2-offscreen'></label>","       <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input' role='combobox' aria-expanded='true'","       aria-autocomplete='list' />","   </div>","   <ul class='select2-results' role='listbox'>","   </ul>","</div>"].join(""));return b},enableInterface:function(){this.parent.enableInterface.apply(this,arguments)&&this.focusser.prop("disabled",!this.isInterfaceEnabled())},opening:function(){var b,c,d;this.opts.minimumResultsForSearch>=0&&this.showSearch(!0),this.parent.opening.apply(this,arguments),this.showSearchInput!==!1&&this.search.val(this.focusser.val()),this.opts.shouldFocusInput(this)&&(this.search.focus(),b=this.search.get(0),b.createTextRange?(c=b.createTextRange(),c.collapse(!1),c.select()):b.setSelectionRange&&(d=this.search.val().length,b.setSelectionRange(d,d))),this.prefillNextSearchTerm(),this.focusser.prop("disabled",!0).val(""),this.updateResults(!0),this.opts.element.trigger(a.Event("select2-open"))},close:function(){this.opened()&&(this.parent.close.apply(this,arguments),this.focusser.prop("disabled",!1),this.opts.shouldFocusInput(this)&&this.focusser.focus())},focus:function(){this.opened()?this.close():(this.focusser.prop("disabled",!1),this.opts.shouldFocusInput(this)&&this.focusser.focus())},isFocused:function(){return this.container.hasClass("select2-container-active")},cancel:function(){this.parent.cancel.apply(this,arguments),this.focusser.prop("disabled",!1),this.opts.shouldFocusInput(this)&&this.focusser.focus()},destroy:function(){a("label[for='"+this.focusser.attr("id")+"']").attr("for",this.opts.element.attr("id")),this.parent.destroy.apply(this,arguments),C.call(this,"selection","focusser")},initContainer:function(){var b,d,e=this.container,f=this.dropdown,g=H();this.showSearch(this.opts.minimumResultsForSearch<0?!1:!0),this.selection=b=e.find(".select2-choice"),this.focusser=e.find(".select2-focusser"),b.find(".select2-chosen").attr("id","select2-chosen-"+g),this.focusser.attr("aria-labelledby","select2-chosen-"+g),this.results.attr("id","select2-results-"+g),this.search.attr("aria-owns","select2-results-"+g),this.focusser.attr("id","s2id_autogen"+g),d=a("label[for='"+this.opts.element.attr("id")+"']"),this.opts.element.focus(this.bind(function(){this.focus()})),this.focusser.prev().text(d.text()).attr("for",this.focusser.attr("id"));var h=this.opts.element.attr("title");this.opts.element.attr("title",h||d.text()),this.focusser.attr("tabindex",this.elementTabIndex),this.search.attr("id",this.focusser.attr("id")+"_search"),this.search.prev().text(a("label[for='"+this.focusser.attr("id")+"']").text()).attr("for",this.search.attr("id")),this.search.on("keydown",this.bind(function(a){if(this.isInterfaceEnabled()&&229!=a.keyCode){if(a.which===M.PAGE_UP||a.which===M.PAGE_DOWN)return void p(a);switch(a.which){case M.UP:case M.DOWN:return this.moveHighlight(a.which===M.UP?-1:1),void p(a);case M.ENTER:return this.selectHighlighted(),void p(a);case M.TAB:return void this.selectHighlighted({noFocus:!0});case M.ESC:return this.cancel(a),void p(a)}}})),this.search.on("blur",this.bind(function(){document.activeElement===this.body.get(0)&&window.setTimeout(this.bind(function(){this.opened()&&this.search.focus()}),0)})),this.focusser.on("keydown",this.bind(function(a){if(this.isInterfaceEnabled()&&a.which!==M.TAB&&!M.isControl(a)&&!M.isFunctionKey(a)&&a.which!==M.ESC){if(this.opts.openOnEnter===!1&&a.which===M.ENTER)return void p(a);if(a.which==M.DOWN||a.which==M.UP||a.which==M.ENTER&&this.opts.openOnEnter){if(a.altKey||a.ctrlKey||a.shiftKey||a.metaKey)return;return this.open(),void p(a)}return a.which==M.DELETE||a.which==M.BACKSPACE?(this.opts.allowClear&&this.clear(),void p(a)):void 0}})),j(this.focusser),this.focusser.on("keyup-change input",this.bind(function(a){if(this.opts.minimumResultsForSearch>=0){if(a.stopPropagation(),this.opened())return;this.open()}})),b.on("mousedown touchstart","abbr",this.bind(function(a){this.isInterfaceEnabled()&&(this.clear(),q(a),this.close(),this.selection&&this.selection.focus())})),b.on("mousedown touchstart",this.bind(function(d){c(b),this.container.hasClass("select2-container-active")||this.opts.element.trigger(a.Event("select2-focus")),this.opened()?this.close():this.isInterfaceEnabled()&&this.open(),p(d)})),f.on("mousedown touchstart",this.bind(function(){this.opts.shouldFocusInput(this)&&this.search.focus()})),b.on("focus",this.bind(function(a){p(a)})),this.focusser.on("focus",this.bind(function(){this.container.hasClass("select2-container-active")||this.opts.element.trigger(a.Event("select2-focus")),this.container.addClass("select2-container-active")})).on("blur",this.bind(function(){this.opened()||(this.container.removeClass("select2-container-active"),this.opts.element.trigger(a.Event("select2-blur")))})),this.search.on("focus",this.bind(function(){this.container.hasClass("select2-container-active")||this.opts.element.trigger(a.Event("select2-focus")),this.container.addClass("select2-container-active")})),this.initContainerWidth(),this.opts.element.hide(),this.setPlaceholder()},clear:function(b){var c=this.selection.data("select2-data");if(c){var d=a.Event("select2-clearing");if(this.opts.element.trigger(d),d.isDefaultPrevented())return;var e=this.getPlaceholderOption();this.opts.element.val(e?e.val():""),this.selection.find(".select2-chosen").empty(),this.selection.removeData("select2-data"),this.setPlaceholder(),b!==!1&&(this.opts.element.trigger({type:"select2-removed",val:this.id(c),choice:c}),this.triggerChange({removed:c}))}},initSelection:function(){if(this.isPlaceholderOptionSelected())this.updateSelection(null),this.close(),this.setPlaceholder();else{var a=this;this.opts.initSelection.call(null,this.opts.element,function(c){c!==b&&null!==c&&(a.updateSelection(c),a.close(),a.setPlaceholder(),a.lastSearchTerm=a.search.val())})}},isPlaceholderOptionSelected:function(){var a;return this.getPlaceholder()===b?!1:(a=this.getPlaceholderOption())!==b&&a.prop("selected")||""===this.opts.element.val()||this.opts.element.val()===b||null===this.opts.element.val()},prepareOpts:function(){var b=this.parent.prepareOpts.apply(this,arguments),c=this;return"select"===b.element.get(0).tagName.toLowerCase()?b.initSelection=function(a,b){var d=a.find("option").filter(function(){return this.selected&&!this.disabled});b(c.optionToData(d))}:"data"in b&&(b.initSelection=b.initSelection||function(c,d){var e=c.val(),f=null;b.query({matcher:function(a,c,d){var h=g(e,b.id(d));return h&&(f=d),h},callback:a.isFunction(d)?function(){d(f)}:a.noop})}),b},getPlaceholder:function(){return this.select&&this.getPlaceholderOption()===b?b:this.parent.getPlaceholder.apply(this,arguments)},setPlaceholder:function(){var a=this.getPlaceholder();if(this.isPlaceholderOptionSelected()&&a!==b){if(this.select&&this.getPlaceholderOption()===b)return;this.selection.find(".select2-chosen").html(this.opts.escapeMarkup(a)),this.selection.addClass("select2-default"),this.container.removeClass("select2-allowclear")}},postprocessResults:function(a,b,c){var d=0,e=this;if(this.findHighlightableChoices().each2(function(a,b){return g(e.id(b.data("select2-data")),e.opts.element.val())?(d=a,!1):void 0}),c!==!1&&this.highlight(b===!0&&d>=0?d:0),b===!0){var f=this.opts.minimumResultsForSearch;f>=0&&this.showSearch(A(a.results)>=f)}},showSearch:function(b){this.showSearchInput!==b&&(this.showSearchInput=b,this.dropdown.find(".select2-search").toggleClass("select2-search-hidden",!b),this.dropdown.find(".select2-search").toggleClass("select2-offscreen",!b),a(this.dropdown,this.container).toggleClass("select2-with-searchbox",b))},onSelect:function(a,b){if(this.triggerSelect(a)){var c=this.opts.element.val(),d=this.data();this.opts.element.val(this.id(a)),this.updateSelection(a),this.opts.element.trigger({type:"select2-selected",val:this.id(a),choice:a}),this.lastSearchTerm=this.search.val(),this.close(),b&&b.noFocus||!this.opts.shouldFocusInput(this)||this.focusser.focus(),g(c,this.id(a))||this.triggerChange({added:a,removed:d})}},updateSelection:function(a){var c,d,e=this.selection.find(".select2-chosen");this.selection.data("select2-data",a),e.empty(),null!==a&&(c=this.opts.formatSelection(a,e,this.opts.escapeMarkup)),c!==b&&e.append(c),d=this.opts.formatSelectionCssClass(a,e),d!==b&&e.addClass(d),this.selection.removeClass("select2-default"),this.opts.allowClear&&this.getPlaceholder()!==b&&this.container.addClass("select2-allowclear")},val:function(){var a,c=!1,d=null,e=this,f=this.data();if(0===arguments.length)return this.opts.element.val();if(a=arguments[0],arguments.length>1&&(c=arguments[1]),this.select)this.select.val(a).find("option").filter(function(){return this.selected}).each2(function(a,b){return d=e.optionToData(b),!1}),this.updateSelection(d),this.setPlaceholder(),c&&this.triggerChange({added:d,removed:f});else{if(!a&&0!==a)return void this.clear(c);if(this.opts.initSelection===b)throw new Error("cannot call val() if initSelection() is not defined");this.opts.element.val(a),this.opts.initSelection(this.opts.element,function(a){e.opts.element.val(a?e.id(a):""),e.updateSelection(a),e.setPlaceholder(),c&&e.triggerChange({added:a,removed:f})})}},clearSearch:function(){this.search.val(""),this.focusser.val("")},data:function(a){var c,d=!1;return 0===arguments.length?(c=this.selection.data("select2-data"),c==b&&(c=null),c):(arguments.length>1&&(d=arguments[1]),void(a?(c=this.data(),this.opts.element.val(a?this.id(a):""),this.updateSelection(a),d&&this.triggerChange({added:a,removed:c})):this.clear(d)))}}),G=D(E,{createContainer:function(){var b=a(document.createElement("div")).attr({"class":"select2-container select2-container-multi"}).html(["<ul class='select2-choices'>","  <li class='select2-search-field'>","    <label for='' class='select2-offscreen'></label>","    <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input'>","  </li>","</ul>","<div class='select2-drop select2-drop-multi select2-display-none'>","   <ul class='select2-results'>","   </ul>","</div>"].join(""));return b},prepareOpts:function(){var b=this.parent.prepareOpts.apply(this,arguments),c=this;return"select"===b.element.get(0).tagName.toLowerCase()?b.initSelection=function(a,b){var d=[];a.find("option").filter(function(){return this.selected&&!this.disabled}).each2(function(a,b){d.push(c.optionToData(b))}),b(d)}:"data"in b&&(b.initSelection=b.initSelection||function(c,d){var e=h(c.val(),b.separator,b.transformVal),f=[];b.query({matcher:function(c,d,h){var i=a.grep(e,function(a){return g(a,b.id(h))}).length;return i&&f.push(h),i},callback:a.isFunction(d)?function(){for(var a=[],c=0;c<e.length;c++)for(var h=e[c],i=0;i<f.length;i++){var j=f[i];if(g(h,b.id(j))){a.push(j),f.splice(i,1);break}}d(a)}:a.noop})}),b},selectChoice:function(a){var b=this.container.find(".select2-search-choice-focus");b.length&&a&&a[0]==b[0]||(b.length&&this.opts.element.trigger("choice-deselected",b),b.removeClass("select2-search-choice-focus"),a&&a.length&&(this.close(),a.addClass("select2-search-choice-focus"),this.opts.element.trigger("choice-selected",a)))},destroy:function(){a("label[for='"+this.search.attr("id")+"']").attr("for",this.opts.element.attr("id")),this.parent.destroy.apply(this,arguments),C.call(this,"searchContainer","selection")},initContainer:function(){var b,c=".select2-choices";this.searchContainer=this.container.find(".select2-search-field"),this.selection=b=this.container.find(c);var d=this;this.selection.on("click",".select2-container:not(.select2-container-disabled) .select2-search-choice:not(.select2-locked)",function(){d.search[0].focus(),d.selectChoice(a(this))}),this.search.attr("id","s2id_autogen"+H()),this.search.prev().text(a("label[for='"+this.opts.element.attr("id")+"']").text()).attr("for",this.search.attr("id")),this.opts.element.focus(this.bind(function(){this.focus()})),this.search.on("input paste",this.bind(function(){this.search.attr("placeholder")&&0==this.search.val().length||this.isInterfaceEnabled()&&(this.opened()||this.open())})),this.search.attr("tabindex",this.elementTabIndex),this.keydowns=0,this.search.on("keydown",this.bind(function(a){if(this.isInterfaceEnabled()){++this.keydowns;var c=b.find(".select2-search-choice-focus"),d=c.prev(".select2-search-choice:not(.select2-locked)"),e=c.next(".select2-search-choice:not(.select2-locked)"),f=o(this.search);if(c.length&&(a.which==M.LEFT||a.which==M.RIGHT||a.which==M.BACKSPACE||a.which==M.DELETE||a.which==M.ENTER)){var g=c;return a.which==M.LEFT&&d.length?g=d:a.which==M.RIGHT?g=e.length?e:null:a.which===M.BACKSPACE?this.unselect(c.first())&&(this.search.width(10),g=d.length?d:e):a.which==M.DELETE?this.unselect(c.first())&&(this.search.width(10),g=e.length?e:null):a.which==M.ENTER&&(g=null),this.selectChoice(g),p(a),void(g&&g.length||this.open())}if((a.which===M.BACKSPACE&&1==this.keydowns||a.which==M.LEFT)&&0==f.offset&&!f.length)return this.selectChoice(b.find(".select2-search-choice:not(.select2-locked)").last()),void p(a);if(this.selectChoice(null),this.opened())switch(a.which){case M.UP:case M.DOWN:return this.moveHighlight(a.which===M.UP?-1:1),void p(a);case M.ENTER:return this.selectHighlighted(),void p(a);case M.TAB:return this.selectHighlighted({noFocus:!0}),void this.close();case M.ESC:return this.cancel(a),void p(a)}if(a.which!==M.TAB&&!M.isControl(a)&&!M.isFunctionKey(a)&&a.which!==M.BACKSPACE&&a.which!==M.ESC){if(a.which===M.ENTER){if(this.opts.openOnEnter===!1)return;if(a.altKey||a.ctrlKey||a.shiftKey||a.metaKey)return}this.open(),(a.which===M.PAGE_UP||a.which===M.PAGE_DOWN)&&p(a),a.which===M.ENTER&&p(a)}}})),this.search.on("keyup",this.bind(function(){this.keydowns=0,this.resizeSearch()})),this.search.on("blur",this.bind(function(b){this.container.removeClass("select2-container-active"),this.search.removeClass("select2-focused"),this.selectChoice(null),this.opened()||this.clearSearch(),b.stopImmediatePropagation(),this.opts.element.trigger(a.Event("select2-blur"))})),this.container.on("click",c,this.bind(function(b){this.isInterfaceEnabled()&&(a(b.target).closest(".select2-search-choice").length>0||(this.selectChoice(null),this.clearPlaceholder(),this.container.hasClass("select2-container-active")||this.opts.element.trigger(a.Event("select2-focus")),this.open(),this.focusSearch(),b.preventDefault()))})),this.container.on("focus",c,this.bind(function(){this.isInterfaceEnabled()&&(this.container.hasClass("select2-container-active")||this.opts.element.trigger(a.Event("select2-focus")),this.container.addClass("select2-container-active"),this.dropdown.addClass("select2-drop-active"),this.clearPlaceholder())})),this.initContainerWidth(),this.opts.element.hide(),this.clearSearch()},enableInterface:function(){this.parent.enableInterface.apply(this,arguments)&&this.search.prop("disabled",!this.isInterfaceEnabled())},initSelection:function(){if(""===this.opts.element.val()&&""===this.opts.element.text()&&(this.updateSelection([]),this.close(),this.clearSearch()),this.select||""!==this.opts.element.val()){var a=this;this.opts.initSelection.call(null,this.opts.element,function(c){c!==b&&null!==c&&(a.updateSelection(c),a.close(),a.clearSearch())})}},clearSearch:function(){var a=this.getPlaceholder(),c=this.getMaxSearchWidth();a!==b&&0===this.getVal().length&&this.search.hasClass("select2-focused")===!1?(this.search.val(a).addClass("select2-default"),this.search.width(c>0?c:this.container.css("width"))):this.search.val("").width(10)},clearPlaceholder:function(){this.search.hasClass("select2-default")&&this.search.val("").removeClass("select2-default")},opening:function(){this.clearPlaceholder(),this.resizeSearch(),this.parent.opening.apply(this,arguments),this.focusSearch(),this.prefillNextSearchTerm(),this.updateResults(!0),this.opts.shouldFocusInput(this)&&this.search.focus(),this.opts.element.trigger(a.Event("select2-open"))},close:function(){this.opened()&&this.parent.close.apply(this,arguments)},focus:function(){this.close(),this.search.focus()},isFocused:function(){return this.search.hasClass("select2-focused")},updateSelection:function(b){var c=[],d=[],f=this;a(b).each(function(){e(f.id(this),c)<0&&(c.push(f.id(this)),d.push(this))}),b=d,this.selection.find(".select2-search-choice").remove(),a(b).each(function(){f.addSelectedChoice(this)}),f.postprocessResults()},tokenize:function(){var a=this.search.val();a=this.opts.tokenizer.call(this,a,this.data(),this.bind(this.onSelect),this.opts),null!=a&&a!=b&&(this.search.val(a),a.length>0&&this.open())},onSelect:function(a,b){this.triggerSelect(a)&&""!==a.text&&(this.addSelectedChoice(a),this.opts.element.trigger({type:"selected",val:this.id(a),choice:a}),this.lastSearchTerm=this.search.val(),this.clearSearch(),this.updateResults(),(this.select||!this.opts.closeOnSelect)&&this.postprocessResults(a,!1,this.opts.closeOnSelect===!0),this.opts.closeOnSelect?(this.close(),this.search.width(10)):this.countSelectableResults()>0?(this.search.width(10),this.resizeSearch(),this.getMaximumSelectionSize()>0&&this.val().length>=this.getMaximumSelectionSize()?this.updateResults(!0):this.prefillNextSearchTerm()&&this.updateResults(),this.positionDropdown()):(this.close(),this.search.width(10)),this.triggerChange({added:a}),b&&b.noFocus||this.focusSearch())},cancel:function(){this.close(),this.focusSearch()},addSelectedChoice:function(c){var d,e,f=!c.locked,g=a("<li class='select2-search-choice'>    <div></div>    <a href='#' class='select2-search-choice-close' tabindex='-1'></a></li>"),h=a("<li class='select2-search-choice select2-locked'><div></div></li>"),i=f?g:h,j=this.id(c),k=this.getVal();d=this.opts.formatSelection(c,i.find("div"),this.opts.escapeMarkup),d!=b&&i.find("div").replaceWith(a("<div></div>").html(d)),e=this.opts.formatSelectionCssClass(c,i.find("div")),e!=b&&i.addClass(e),f&&i.find(".select2-search-choice-close").on("mousedown",p).on("click dblclick",this.bind(function(b){this.isInterfaceEnabled()&&(this.unselect(a(b.target)),this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus"),p(b),this.close(),this.focusSearch())})).on("focus",this.bind(function(){this.isInterfaceEnabled()&&(this.container.addClass("select2-container-active"),this.dropdown.addClass("select2-drop-active"))})),i.data("select2-data",c),i.insertBefore(this.searchContainer),k.push(j),this.setVal(k)},unselect:function(b){var c,d,f=this.getVal();if(b=b.closest(".select2-search-choice"),0===b.length)throw"Invalid argument: "+b+". Must be .select2-search-choice";if(c=b.data("select2-data")){var g=a.Event("select2-removing");if(g.val=this.id(c),g.choice=c,this.opts.element.trigger(g),g.isDefaultPrevented())return!1;for(;(d=e(this.id(c),f))>=0;)f.splice(d,1),this.setVal(f),this.select&&this.postprocessResults();return b.remove(),this.opts.element.trigger({type:"select2-removed",val:this.id(c),choice:c}),this.triggerChange({removed:c}),!0}},postprocessResults:function(a,b,c){var d=this.getVal(),f=this.results.find(".select2-result"),g=this.results.find(".select2-result-with-children"),h=this;f.each2(function(a,b){var c=h.id(b.data("select2-data"));e(c,d)>=0&&(b.addClass("select2-selected"),b.find(".select2-result-selectable").addClass("select2-selected"))}),g.each2(function(a,b){b.is(".select2-result-selectable")||0!==b.find(".select2-result-selectable:not(.select2-selected)").length||b.addClass("select2-selected")}),-1==this.highlight()&&c!==!1&&this.opts.closeOnSelect===!0&&h.highlight(0),!this.opts.createSearchChoice&&!f.filter(".select2-result:not(.select2-selected)").length>0&&(!a||a&&!a.more&&0===this.results.find(".select2-no-results").length)&&y(h.opts.formatNoMatches,"formatNoMatches")&&this.results.append("<li class='select2-no-results'>"+z(h.opts.formatNoMatches,h.opts.element,h.search.val())+"</li>")},getMaxSearchWidth:function(){return this.selection.width()-i(this.search)},resizeSearch:function(){var a,b,c,d,e,f=i(this.search);a=r(this.search)+10,b=this.search.offset().left,c=this.selection.width(),d=this.selection.offset().left,e=c-(b-d)-f,a>e&&(e=c-f),40>e&&(e=c-f),0>=e&&(e=a),this.search.width(Math.floor(e))},getVal:function(){var a;return this.select?(a=this.select.val(),null===a?[]:a):(a=this.opts.element.val(),h(a,this.opts.separator,this.opts.transformVal))},setVal:function(b){var c;this.select?this.select.val(b):(c=[],a(b).each(function(){e(this,c)<0&&c.push(this)}),this.opts.element.val(0===c.length?"":c.join(this.opts.separator)))},buildChangeDetails:function(a,b){for(var b=b.slice(0),a=a.slice(0),c=0;c<b.length;c++)for(var d=0;d<a.length;d++)g(this.opts.id(b[c]),this.opts.id(a[d]))&&(b.splice(c,1),c>0&&c--,a.splice(d,1),d--);return{added:b,removed:a}},val:function(c,d){var e,f=this;if(0===arguments.length)return this.getVal();if(e=this.data(),e.length||(e=[]),!c&&0!==c)return this.opts.element.val(""),this.updateSelection([]),this.clearSearch(),void(d&&this.triggerChange({added:this.data(),removed:e}));if(this.setVal(c),this.select)this.opts.initSelection(this.select,this.bind(this.updateSelection)),d&&this.triggerChange(this.buildChangeDetails(e,this.data()));else{if(this.opts.initSelection===b)throw new Error("val() cannot be called if initSelection() is not defined");this.opts.initSelection(this.opts.element,function(b){var c=a.map(b,f.id);f.setVal(c),f.updateSelection(b),f.clearSearch(),d&&f.triggerChange(f.buildChangeDetails(e,f.data()))})}this.clearSearch()},onSortStart:function(){if(this.select)throw new Error("Sorting of elements is not supported when attached to <select>. Attach to <input type='hidden'/> instead.");this.search.width(0),this.searchContainer.hide()},onSortEnd:function(){var b=[],c=this;this.searchContainer.show(),this.searchContainer.appendTo(this.searchContainer.parent()),this.resizeSearch(),this.selection.find(".select2-search-choice").each(function(){b.push(c.opts.id(a(this).data("select2-data")))}),this.setVal(b),this.triggerChange()},data:function(b,c){var d,e,f=this;return 0===arguments.length?this.selection.children(".select2-search-choice").map(function(){return a(this).data("select2-data")}).get():(e=this.data(),b||(b=[]),d=a.map(b,function(a){return f.opts.id(a)}),this.setVal(d),this.updateSelection(b),this.clearSearch(),c&&this.triggerChange(this.buildChangeDetails(e,this.data())),void 0)}}),a.fn.select2=function(){var c,d,f,g,h,i=Array.prototype.slice.call(arguments,0),j=["val","destroy","opened","open","close","focus","isFocused","container","dropdown","onSortStart","onSortEnd","enable","disable","readonly","positionDropdown","data","search"],k=["opened","isFocused","container","dropdown"],l=["val","data"],m={search:"externalSearch"};return this.each(function(){if(0===i.length||"object"==typeof i[0])c=0===i.length?{}:a.extend({},i[0]),c.element=a(this),"select"===c.element.get(0).tagName.toLowerCase()?h=c.element.prop("multiple"):(h=c.multiple||!1,"tags"in c&&(c.multiple=h=!0)),d=h?new window.Select2["class"].multi:new window.Select2["class"].single,d.init(c);else{if("string"!=typeof i[0])throw"Invalid arguments to select2 plugin: "+i;if(e(i[0],j)<0)throw"Unknown method: "+i[0];if(g=b,d=a(this).data("select2"),d===b)return;if(f=i[0],"container"===f?g=d.container:"dropdown"===f?g=d.dropdown:(m[f]&&(f=m[f]),g=d[f].apply(d,i.slice(1))),e(i[0],k)>=0||e(i[0],l)>=0&&1==i.length)return!1}}),g===b?this:g},a.fn.select2.defaults={width:"copy",loadMorePadding:0,closeOnSelect:!0,openOnEnter:!0,containerCss:{},dropdownCss:{},containerCssClass:"",dropdownCssClass:"",formatResult:function(a,b,c,d){var e=[];return t(this.text(a),c.term,e,d),e.join("")},transformVal:function(b){return a.trim(b)},formatSelection:function(a,c,d){return a?d(this.text(a)):b},sortResults:function(a){return a},formatResultCssClass:function(a){return a.css},formatSelectionCssClass:function(){return b},minimumResultsForSearch:0,minimumInputLength:0,maximumInputLength:null,maximumSelectionSize:0,id:function(a){return a==b?null:a.id},text:function(b){return b&&this.data&&this.data.text?a.isFunction(this.data.text)?this.data.text(b):b[this.data.text]:b.text},matcher:function(a,b){return d(""+b).toUpperCase().indexOf(d(""+a).toUpperCase())>=0},separator:",",tokenSeparators:[],tokenizer:B,escapeMarkup:u,blurOnChange:!1,selectOnBlur:!1,adaptContainerCssClass:function(a){return a},adaptDropdownCssClass:function(){return null},nextSearchTerm:function(){return b},searchInputPlaceholder:"",createSearchChoicePosition:"top",shouldFocusInput:function(a){var b="ontouchstart"in window||navigator.msMaxTouchPoints>0;return b&&a.opts.minimumResultsForSearch<0?!1:!0}},a.fn.select2.locales=[],a.fn.select2.locales.en={formatMatches:function(a){return 1===a?"One result is available, press enter to select it.":a+" results are available, use up and down arrow keys to navigate."},formatNoMatches:function(){return"No matches found"},formatAjaxError:function(){return"Loading failed"},formatInputTooShort:function(a,b){var c=b-a.length;return"Please enter "+c+" or more character"+(1==c?"":"s")},formatInputTooLong:function(a,b){var c=a.length-b;return"Please delete "+c+" character"+(1==c?"":"s")},formatSelectionTooBig:function(a){return"You can only select "+a+" item"+(1==a?"":"s")},formatLoadMore:function(){return"Loading more results…"},formatSearching:function(){return"Searching…"}},a.extend(a.fn.select2.defaults,a.fn.select2.locales.en),a.fn.select2.ajaxDefaults={transport:a.ajax,params:{type:"GET",cache:!1,dataType:"json"}},window.Select2={query:{ajax:v,local:w,tags:x},util:{debounce:l,markMatch:t,escapeMarkup:u,stripDiacritics:d},"class":{"abstract":E,single:F,multi:G}}}}(jQuery);/**
 * Theme Customizer enhancements for a better user experience.
 * @package Customizr
 * @since Customizr 1.0
 */
(function (wp, $, _) {
  var api = wp.customize,
      $_nav_section_container,
      translatedStrings = TCControlParams.translatedStrings || {};

  api.bind( 'ready' , function() {
    _setControlVisibilities();
  } );


  //FIX FOR CONTROL VISIBILITY LOST ON PREVIEW REFRESH #1
  //This solves the problem of control visiblity settings being lost on preview refresh since WP 4.3
  //this overrides the wp method only for the control instances
  //it check if there's been a customizations
  //=> args.unchanged is true for all cases, for example when api.previewer.loading and the preview send 'ready'created during the frame synchronisation
  api.Control.prototype.onChangeActive = function ( active, args ) {
    if ( args.unchanged )
      return;
    if ( this.container[0] && ! $.contains( document, this.container[0] ) ) {
      // jQuery.fn.slideUp is not hiding an element if it is not in the DOM
      this.container.toggle( active );
      if ( args.completeCallback ) {
        args.completeCallback();
      }
    } else if ( active ) {
      this.container.slideDown( args.duration, args.completeCallback );
    } else {
      this.container.slideUp( args.duration, args.completeCallback );
    }
  };


  //FIX FOR SECTION CONTENT HIDDEN BY THE FOOTER
  //Problem fixed : since WP4.5, the footer of the customizer includes the device switcher
  //but there's aso the rating link there.
  //Therefore, in sections higher than the viewport, some content might be hidden
  //This is fixed on each section expanded event
  api.bind('ready', function() {
    //wp.customize.Section is not available before wp 4.1
    if ( 'function' != typeof (api.Section) )
      return;
    _.map( api.settings.sections, function( section, id ) {

      var _section = api.section(id);
      _section.expanded.callbacks.add( function( _expanded ) {
          if ( ! _expanded )
            return;
          var $container = _section.container.closest( '.wp-full-overlay-sidebar-content' ),
                $content = _section.container.find( '.accordion-section-content' );
            //content resizing to the container height
            _resizeContentHeight = function() {
              $content.css( 'height', $container.innerHeight() );
          };
          _resizeContentHeight();
          //this is set to off in the original expand callback if 'expanded' is false
          $( window ).on( 'resize.customizer-section', _.debounce( _resizeContentHeight, 110 ) );
        }
      );//add
    });//_.map
  });


  /* Multiple Picker */
  /**
   * @constructor
   * @augments wp.customize.Control
   * @augments wp.customize.Class
   */
  api.TCMultiplePickerControl = api.Control.extend({
    ready: function() {
      var control  = this,
          _select  = this.container.find('select');

      //handle case when all choices become unselected
      _select.on('change', function(e){
        if ( 0 === $(this).find("option:selected").length )
          control.setting.set([]);
      });
    }
  });
  $.extend( api.controlConstructor, {
    tc_multiple_picker : api.TCMultiplePickerControl
  });



  /* IMAGE UPLOADER CONTROL IN THE CUSTOMIZER */
  //CroppedImageControl is not available before wp 4.3
  if ( ('function' == typeof wp.media.controller.Cropper ) && ( 'function' == typeof api.CroppedImageControl ) ) {
    /* TCCustomizeImage Cropper */
    /**
    * Custom version of:
    * wp.media.controller.CustomizeImageCropper (wp-includes/js/media-views.js)
    *
    * In order to use image destination sizes different than the suggested ones
    *
    * A state for cropping an image.
    *
    * @class
    * @augments wp.media.controller.Cropper
    * @augments wp.media.controller.State
    * @augments Backbone.Model
    */
    wp.media.controller.TCCustomizeImageCropper = wp.media.controller.Cropper.extend({
      doCrop: function( attachment ) {
        var cropDetails = attachment.get( 'cropDetails' ),
            control = this.get( 'control' );

        cropDetails.dst_width  = control.params.dst_width;
        cropDetails.dst_height = control.params.dst_height;

        return wp.ajax.post( 'crop-image', {
            wp_customize: 'on',
            nonce: attachment.get( 'nonces' ).edit,
            id: attachment.get( 'id' ),
            context: control.id,
            cropDetails: cropDetails
        } );
      }
    });

    /* TCCroppedImageControl */
    /**
    * @constructor
    * @augments wp.customize.CroppedImageControl
    * @augments wp.customize.Class
    */
    api.TCCroppedImageControl = api.CroppedImageControl.extend({
      /**
      * Create a media modal select frame, and store it so the instance can be reused when needed.
      * TC: We don't want to crop svg (cropping fails), gif (animated gifs become static )
      * @Override
      * We need to override this in order to use our ImageCropper custom extension of wp.media.controller.Cropper
      *
      * See api.CroppedImageControl:initFrame() ( wp-admin/js/customize-controls.js )
      */
      initFrame: function() {

        var l10n = _wpMediaViewsL10n;

        this.frame = wp.media({
            button: {
                text: l10n.select,
                close: false
            },
            states: [
                new wp.media.controller.Library({
                    title: this.params.button_labels.frame_title,
                    library: wp.media.query({ type: 'image' }),
                    multiple: false,
                    date: false,
                    priority: 20,
                    suggestedWidth: this.params.width,
                    suggestedHeight: this.params.height
                }),
                new wp.media.controller.TCCustomizeImageCropper({
                    imgSelectOptions: this.calculateImageSelectOptions,
                    control: this
                })
            ]
        });

        this.frame.on( 'select', this.onSelect, this );
        this.frame.on( 'cropped', this.onCropped, this );
        this.frame.on( 'skippedcrop', this.onSkippedCrop, this );
      },

      /**
      * After an image is selected in the media modal, switch to the cropper
      * state if the image isn't the right size.
      *
      * TC: We don't want to crop svg (cropping fails), gif (animated gifs become static )
      * @Override
      * See api.CroppedImageControl:onSelect() ( wp-admin/js/customize-controls.js )
      */
      onSelect: function() {
        var attachment = this.frame.state().get( 'selection' ).first().toJSON();
        if ( ! ( attachment.mime && attachment.mime.indexOf("image") > -1 ) ){
          //Todo: better error handling, show some message?
          this.frame.trigger( 'content:error' );
          return;
        }
        if ( ( _.contains( ['image/svg+xml', 'image/gif'], attachment.mime ) ) || //do not crop gifs or svgs
                this.params.width === attachment.width && this.params.height === attachment.height && ! this.params.flex_width && ! this.params.flex_height ) {
            this.setImageFromAttachment( attachment );
            this.frame.close();
        } else {
            this.frame.setState( 'cropper' );
        }
      },
    });//end Controller

    $.extend( api.controlConstructor, {
      tc_cropped_image : api.TCCroppedImageControl
    });
  }//endif




  /**
   * @constructor
   * @augments wp.customize.Control
   * @augments wp.customize.Class
   */
  api.TCUploadControl = api.Control.extend({
    ready: function() {
      var control = this;

      this.params.removed = this.params.removed || '';

      this.success = $.proxy( this.success, this );

      this.uploader = $.extend({
        container: this.container,
        browser:   this.container.find('.tc-upload'),
        //dropzone:  this.container.find('.upload-dropzone'),
        success:   this.success,
        plupload:  {},
        params:    {}
      }, this.uploader || {} );

      if ( control.params.extensions ) {
        control.uploader.plupload.filters = [{
          title:      api.l10n.allowedFiles,
          extensions: control.params.extensions
        }];
      }

      if ( control.params.context )
        control.uploader.params['post_data[context]'] = this.params.context;

      if ( api.settings.theme.stylesheet )
        control.uploader.params['post_data[theme]'] = api.settings.theme.stylesheet;

      this.uploader = new wp.Uploader( this.uploader );

      this.remover = this.container.find('.remove');
      this.remover.on( 'click keydown', function( event ) {
        if ( event.type === 'keydown' &&  13 !== event.which ) // enter
          return;
        control.setting.set( control.params.removed );
        event.preventDefault();
      });

      this.removerVisibility = $.proxy( this.removerVisibility, this );
      this.setting.bind( this.removerVisibility );
      this.removerVisibility( this.setting.get() );
    },
    success: function( attachment ) {
      this.setting.set( attachment.get('id') );
    },
    removerVisibility: function( to ) {
      this.remover.toggle( to != this.params.removed );
    }
  });//api.Control.extend()


  $.extend( api.controlConstructor, {
    tc_upload : api.TCUploadControl
  });




  //bind all actions to wp.customize ready event
  //map each setting with its dependencies
  var _setControlVisibilities = function() {
    _.map( _controlDependencies , function( opts , setId ) {
      _prepare_visibilities( setId, opts );
    });

    //additional dependencies
    _handle_grid_dependencies();
    _header_layout_dependency();

    //favicon note on load and on change(since wp 4.3)
    _handleFaviconNote();

    //nav section visibilities
    //=> backward compat if api.section not defined
    if ( 'function' == typeof api.section ) {
      $_nav_section_container = api.section('nav').container;
      //on nav section open
      api.section('nav').expanded.callbacks.add( function() {
        _hideAllmenusActions( api('tc_theme_options[tc_hide_all_menus]').get() );
      });//add()
    } else {
      $_nav_section_container = $('li#accordion-section-nav');
      //on nav section open
      $_nav_section_container.on( 'click keydown', '.accordion-section-title', function(event) {
        //special treatment for click events
        if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
          return;
        }
        event.preventDefault(); // Keep this AFTER the key filter above)

        _hideAllmenusActions( api('tc_theme_options[tc_hide_all_menus]').get() );
      });//on()
    }//else

    //specific callback for the tc_hide_all_menus setting
    api('tc_theme_options[tc_hide_all_menus]').callbacks.add( _hideAllmenusActions );
  };


  /*
  * Main control dependencies object
  */
  var _controlDependencies = {
    //we have to show restrict blog/home posts when
    //1. show page on front and a page of posts is selected
    //2, show posts on front
    'page_for_posts' : {
       controls: [
         'tc_blog_restrict_by_cat',
       ],
       callback : function (to) {
         return '0' !== to;
       },
    },
    'show_on_front' : {
      controls: [
        'tc_blog_restrict_by_cat',
        'tc_show_post_navigation_home'
      ],
      callback : function (to, targetSetId) {
        if ( 'posts' == to )
          return true;
        if ( 'page' == to && 'tc_blog_restrict_by_cat' == targetSetId ) //show cat picker also if a page for posts is set
          return '0' !== api( _build_setId('page_for_posts') ).get() ;
        return false;
      },

    },
    'tc_logo_upload' : {
      controls: [
          'tc_logo_resize'
      ],
      callback : function( to ) {
        return _.isNumber( to );
      }
    },
    'tc_show_featured_pages': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_front_slider': {
      controls: [
        'tc_slider_width',
        'tc_slider_delay',
        'tc_slider_default_height',
        'tc_slider_default_height_apply_all',
        'tc_slider_change_default_img_size',
        'tc_posts_slider_number',
        'tc_posts_slider_stickies',
        'tc_posts_slider_title',
        'tc_posts_slider_text',
        'tc_posts_slider_link',
        'tc_posts_slider_button_text',
        'tc_posts_slider_restrict_by_cat' //tc-pro-bundle
      ],
      callback: function (to, targetSetId) {
        //posts slider options must be hidden when the posts slider not choosen
        if ( targetSetId.indexOf('tc_posts_slider_') > -1 )
          return 'tc_posts_slider' == to;

        //if user select the post slider option, append a notice in the label element
        //and hide the notice when no sliders have been created yet
        var $_front_slider_container = api.control( _build_setId('tc_front_slider') ).container,
            $_label = $( 'label' , $_front_slider_container ),
            $_empty_sliders_notice = $( 'div.tc-notice', $_front_slider_container);

        if ( 'tc_posts_slider' == to ) {
          if ( 0 !== $_label.length && ! $('.tc-notice' , $_label ).length ) {
            var $_notice = $('<span>', { class: 'tc-notice', html : translatedStrings.postSliderNote || '' } );
            $_label.append( $_notice );
          } else {
            $('.tc-notice' , $_label ).show();
          }
          //hide no sliders created notice
          if ( 0 !== $_empty_sliders_notice.length )
            $_empty_sliders_notice.hide();
        } else {
          if ( 0 !== $( '.tc-notice' , $_label ).length )
            $( '.tc-notice' , $_label ).hide();
          if ( 0 !== $_empty_sliders_notice.length )
            $_empty_sliders_notice.show();
        }
        return '0' !== to;
      }//callback
    },
    'tc_slider_default_height' : {
      controls: [
        'tc_slider_default_height_apply_all',
        'tc_slider_change_default_img_size'
      ],
      callback: function (to, targetSetId) {
        //slider height options must be hidden is height = default height (500px), unchanged by user
        var _defaultHeight = TCControlParams.defaultSliderHeight || 500;
        return _defaultHeight != to;
      }
    },
    'tc_posts_slider_link' : {
      controls: [
        'tc_posts_slider_button_text'
      ],
      callback: function (to) {
        return to.indexOf('cta') > -1;
      },
      //display dependant if master setting value == value
      cross: {
        tc_posts_slider_button_text : { master : 'tc_front_slider' , callback : function (to) { return 'tc_posts_slider' == to; } },
      }
    },
    'tc_post_list_grid' : {
      show: {
        controls: [
          'tc_grid_columns',
          'tc_grid_expand_featured',
          'tc_grid_in_blog',
          'tc_grid_in_archive',
          'tc_grid_in_search',
          'tc_grid_thumb_height',
          'tc_grid_bottom_border',
          'tc_grid_shadow',
          'tc_grid_icons',
          'tc_grid_num_words'

        ],
        callback: function (to) {
          return 'grid' == to;
        }
      }
    },
    'tc_post_list_thumb_shape' : {
      controls: [
        'tc_post_list_thumb_height'
      ],
      callback: function (to) {
        return to.indexOf('rectangular') > -1;
      }
    },
    'tc_post_list_thumb_position' : {
      controls: [
        'tc_post_list_thumb_alternate'
      ],
      callback: function (to) {
        return _.contains( [ 'left', 'right'], to );
      }
    },
    'tc_post_list_show_thumb' : {
      controls: [
        'tc_post_list_use_attachment_as_thumb',
        'tc_post_list_default_thumb',
        'tc_post_list_thumb_shape',
        'tc_post_list_thumb_alternate',
        'tc_post_list_thumb_position',
        'tc_post_list_thumb_height',
        'tc_grid_thumb_height'
      ],
      callback: function (to) {
        return '1' == to;
      },
      //display dependant if master setting value == value
      cross: {
        tc_post_list_thumb_height : { master : 'tc_post_list_thumb_shape' , callback : function (to) { return to.indexOf('rectangular') > -1; } },
        tc_post_list_thumb_alternate: { master: 'tc_post_list_thumb_position', callback: function (to) { return _.contains( [ 'left', 'right'], to ); } }
      }
    },
    'tc_breadcrumb' : {
      controls: [
        'tc_show_breadcrumb_home',
        'tc_show_breadcrumb_in_pages',
        'tc_show_breadcrumb_in_single_posts',
        'tc_show_breadcrumb_in_post_lists'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_title_icon' : {
      controls: [
        'tc_show_page_title_icon',
        'tc_show_post_title_icon',
        'tc_show_archive_title_icon',
        'tc_show_post_list_title_icon',
        'tc_show_sidebar_widget_icon',
        'tc_show_footer_widget_icon'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_post_metas' : {
      controls: [
        'tc_show_post_metas_home',
        'tc_post_metas_design',
        'tc_show_post_metas_single_post',
        'tc_show_post_metas_post_lists',
        'tc_show_post_metas_categories',
        'tc_show_post_metas_tags',
        'tc_show_post_metas_publication_date',
        'tc_show_post_metas_update_date',
        'tc_post_metas_update_notice_text',
        'tc_post_metas_update_notice_interval',
        'tc_show_post_metas_author'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_show_post_metas_update_date' : {
      controls: [
        'tc_post_metas_update_date_format',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_post_metas_update_notice_in_title' : {
      controls: [
        'tc_post_metas_update_notice_text',
        'tc_post_metas_update_notice_format',
        'tc_post_metas_update_notice_interval'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_post_list_length' : {
      controls: [
        'tc_post_list_excerpt_length',
      ],
      callback: function (to) {
        return 'excerpt' == to;
      }
    },
    'tc_sticky_show_title_logo' : {
      controls: [
        'tc_sticky_logo_upload',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_sticky_header' : {
      controls: [
        'tc_sticky_show_tagline',
        'tc_sticky_show_title_logo',
        'tc_sticky_shrink_title_logo',
        'tc_sticky_show_menu',
        'tc_sticky_transparent_on_scroll',
        'tc_sticky_logo_upload',
        'tc_woocommerce_header_cart_sticky'
      ],
      callback: function (to, targetSetId) {
        return '1' == to;
      },
      cross: {
        tc_woocommerce_header_cart_sticky : { master : 'tc_woocommerce_header_cart' , callback : function (to, tID, changedSetId ) { 
          return to &&  //api.control.active is available since wp 4.0 as the php active_callback
            //so let's skip this for older wp versions
            ( 'function' == typeof api.control.active ? api.control( _build_setId( changedSetId ) ).active() : true );
        } }
      }
    },
    'tc_comment_bubble_color_type' : {
      controls: [
        'tc_comment_bubble_color',
      ],
      callback: function (to) {
        return 'custom' == to;
      }
    },
    'tc_comment_show_bubble' : {
      controls: [
        'tc_comment_bubble_shape',
        'tc_comment_bubble_color_type',
        'tc_comment_bubble_color'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_enable_dropcap' : {
      controls: [
        'tc_dropcap_minwords',
        'tc_dropcap_design',
        'tc_post_dropcap',
        'tc_page_dropcap'
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_enable_gallery' : {
      controls: [
        'tc_gallery_fancybox',
        'tc_gallery_style',
      ],
      callback: function (to) {
        return '1' == to;
      }
    },
    'tc_skin_random' : { /* hack */
      controls: [
        'tc_skin',
      ],
      callback: function (to) {
        var $_skin_select = $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]');

        $_skin_select.prop('disabled', '1' == to ? 'disabled' : '' );
        return true;
      }
    },
    'tc_show_post_navigation' : {
      controls: [
        'tc_show_post_navigation_page',
        'tc_show_post_navigation_home',
        'tc_show_post_navigation_single',
        'tc_show_post_navigation_archive'
      ],
      callback: function (to) {
        return '1' == to;
      },
      //display dependant if master setting value == value
      cross: {
        tc_show_post_navigation_home : { master : 'show_on_front' , callback : function (to) { return 'posts' == to; } },
      }
    },
    'tc_display_second_menu' : {
      show : {
        controls: [
          'nav_menu_locations[secondary]',
          'tc_second_menu_position',
          'tc_second_menu_resp_setting',
          'tc_menu_type',
          'tc_menu_submenu_fade_effect',
          'tc_menu_submenu_item_move_effect'
        ],
        //the menu style must be aside for secondary menu controls
        callback: function (to, targetSetId, changedSetId) {
          //second menu speicifics
          if ( _.contains( ['nav_menu_locations[secondary]', 'tc_second_menu_resp_setting'], targetSetId ) )
            return '1' == to && 'aside' == api( _build_setId( 'tc_menu_style' )).get();
          //effects common to regular menu and second horizontal menu
          if ( _.contains( ['tc_menu_submenu_fade_effect', 'tc_menu_submenu_item_move_effect'], targetSetId ) )
            return ( '1' == to && 'aside' == api( _build_setId( 'tc_menu_style' )).get() ) || ('1' != to && 'aside' != api( _build_setId( 'tc_menu_style' )).get() );
          return '1' == to;
        }
      }
      // hide : {
      //   controls: [
      //     'tc_display_menu_label'
      //   ],
      //   callback: function (to) {
      //     return 'aside' != to;
      //   }
      // }
    },
    'tc_menu_style' : {
      show : {
        controls: [
          'tc_menu_type',
          'tc_menu_submenu_fade_effect',
          'tc_menu_submenu_item_move_effect',
          'tc_menu_resp_dropdown_limit_to_viewport',
          'tc_display_menu_label',
          'tc_display_second_menu',
          'tc_second_menu_position',
          'nav_menu_locations[secondary]',
          'tc_second_menu_resp_setting',
          'tc_mc_effect'
        ],
        //if the second menu is activated, only the tc_menu_resp_dropdown_limit_to_viewport is hidden
        //otherwise all of them are hidden
        callback: function (to, targetSetId, changedSetId) {
          //CASE 1 : regular menu choosen
          if ( 'aside' != to ) {
            if ( _.contains([
                'tc_display_menu_label',
                'tc_display_second_menu',
                'nav_menu_locations[secondary]',
                'tc_second_menu_position',
                'tc_second_menu_resp_setting',
                'tc_mc_effect'] , targetSetId ) ) {
              return false;
            } else {
              return true;
            }
          }
          //CASE 2 : side menu choosen
          else {
            if ( _.contains([
              'tc_menu_type',
              'tc_menu_submenu_fade_effect',
              'tc_menu_submenu_item_move_effect',
              'nav_menu_locations[secondary]',
              'tc_second_menu_position',
              'tc_second_menu_resp_setting'],
              targetSetId ) ) {
                return true === api( _build_setId('tc_display_second_menu') ).get();
            }
            else if ( 'tc_menu_resp_dropdown_limit_to_viewport' == targetSetId ){
              return false;
            }
            return true;
          }
        }
      }
    },
    'tc_woocommerce_header_cart' : {
      controls: [
        'tc_woocommerce_header_cart_sticky'
      ],
      callback: function (to, tID , changedSetId) {
        return to &&  //api.control.active is available since wp 4.0 as the php active_callback
        //so let's skip this for older wp versions
        ( 'function' == typeof api.control.active ? api.control( _build_setId( changedSetId ) ).active() : true );
      },
      //display dependant if master setting value == value
      cross: {
        tc_woocommerce_header_cart_sticky : { master : 'tc_sticky_header' , callback : function (to) { 
            return to; 
        } },
      }
    }
  };


  /*
  * @return string
  * simple helper to build the setting id name
  */
  var _build_setId = function ( name ) {
    //first check if the current setting id is a customizr one (can be WP built in like nav_menu_locations[{$location}])
    //=> all customizer theme settings starts by "tc_" by convention
    if ( -1 == name.indexOf( 'tc_' ) )
      return name;
    return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
  };


  /*
  * find the setId key in the _controlDependencies object
  * get the controls, merge show and hide if needed
  * return an []
  */
  var _get_dependants = function( setId ) {
    if ( ! _controlDependencies[setId] )
      return [];
    var _dependants = _controlDependencies[setId];

    if ( _dependants.show && _dependants.hide )
      return _.union(_dependants.show.controls , _dependants.hide.controls);
    if ( _dependants.show && ! _dependants.hide )
      return _dependants.show.controls;
    if ( ! _dependants.show && _dependants.hide )
      return _dependants.hide.controls;

    return _dependants.controls;
  };

  /*
  * @return string hide or show. default is hide
  */
  var _get_visibility_action = function ( setId , depSetId ) {
    if ( ! _controlDependencies[setId] )
      return 'both';
    var _dependants = _controlDependencies[setId];
    if ( _dependants.show && -1 != _.indexOf( _dependants.show.controls, depSetId ) )
      return 'show';
    if ( _dependants.hide && -1 != _.indexOf( _dependants.hide.controls, depSetId ) )
      return 'hide';
    return 'both';
  };


  var _get_visibility_cb = function( setId , _action ) {
    if ( ! _controlDependencies[setId] )
      return;
    var _dependants = _controlDependencies[setId];
    if ( ! _dependants[_action] )
      return _dependants.callback;
    return (_dependants[_action]).callback;
  };


  var _check_cross_dependant = function( setId, depSetId ) {
    if ( ! _controlDependencies[setId] )
      return true;
    var _dependants = _controlDependencies[setId];
    if ( ! _dependants.cross || ! _dependants.cross[depSetId] )
      return true;
    var _cross  = _dependants.cross[depSetId],
        _id     = _cross.master,
        _cb     = _cross.callback;

    _id = _build_setId(_id);
    //if _cb returns true => show
    return _cb( api.instance(_id).get() );
  };

  /*
  * @return void
  * show or hide setting according to the dependency + callback pair
  */
  var _prepare_visibilities = function( setId, o ) {
    api( _build_setId(setId) , function (setting) {
      var _params = {
        setting   : setting,
        setId : setId,
        controls  : _get_dependants(setId),
      };
      _.map( _params.controls , function( depSetId ) {
        _set_single_dependant_control_visibility( depSetId , _params);
      } );
    });
  };


  /*
  *
  */
  var _set_single_dependant_control_visibility = function( depSetId , _params ) {
    api.control( _build_setId(depSetId) , function (control) {
      var _visibility = function (to) {
        var _action   = _get_visibility_action( _params.setId , depSetId ),
            _callback = _get_visibility_cb( _params.setId , _action ),
            _bool     = false;

        if ( 'show' == _action && _callback(to, depSetId, _params.setId ) )
          _bool = true;
        if ( 'hide' == _action && _callback(to, depSetId, _params.setId ) )
          _bool = false;
        if ( 'both' == _action )
          _bool = _callback(to, depSetId, _params.setId );

        //check if there are any cross dependencies to look at
        //_check_cross_dependant return true if there are no cross dependencies.
        //if cross dependency :
        //1) return true if we must show, false if not.
        _bool = _check_cross_dependant( _params.setId, depSetId ) && _bool;
        control.container.toggle( _bool );
      };//_visibility()



      _visibility( _params.setting.get() );
      _params.setting.bind( _visibility );
    });
  };


  /*
  * Specific Grid action : handles the visibility of the "MORE GRID DESIGN OPTIONS" link
  * @to do => find a way to include several callbacks in the _controlDependencies object => include the one below
  */
  var _handle_grid_dependencies = function() {
    //apply visibility on ready
    var _is_grid_enabled = api('tc_theme_options[tc_post_list_grid]') && 'grid' == api('tc_theme_options[tc_post_list_grid]').get();
    $('.tc-grid-toggle-controls').toggle( _is_grid_enabled );

    //bind visibility on setting changes
    api.instance('tc_theme_options[tc_post_list_grid]').bind( function(to) {
      $('.tc-grid-toggle-controls').toggle( 'grid' == to );

      if ( 'grid' == to )
        $('.tc-grid-toggle-controls').trigger('click').toggleClass('open');
    } );
  };


  /**
  * Dependency between the header layout and the menu position, when the menu style is Side Menu
  */
  var _header_layout_dependency = function() {
    //when user switch layout, make sure the menu is correctly aligned by default.
    api('tc_theme_options[tc_header_layout]').callbacks.add( function(to) {
      api('tc_theme_options[tc_menu_position]').set( 'right' == to ? 'pull-menu-left' : 'pull-menu-right' );
      //refresh the selecter
      api.control('tc_theme_options[tc_menu_position]').container.find('select').selecter('destroy').selecter({});
    } );

    //when user changes the menu syle (side or regular), refresh the menu position according to the header layout
    api('tc_theme_options[tc_menu_style]').callbacks.add( function(to) {
      var _header_layout = api('tc_theme_options[tc_header_layout]').get();
      api('tc_theme_options[tc_menu_position]').set( 'left' == _header_layout ? 'pull-menu-right' : 'pull-menu-left' );
      //refresh the selecter
      api.control('tc_theme_options[tc_menu_position]').container.find('select').selecter('destroy').selecter({});
    } );
  };


  //change the 'nav' section controls opacity based on the booleand value of a setting (tc_theme_options[tc_hide_all_menus])
  var _hideAllmenusActions = function(to, from, setId) {
    setId = setId ||'tc_theme_options[tc_hide_all_menus]';
    var $_controls = $_nav_section_container.find('li.customize-control').not( api.control(setId).container );
    $_controls.each( function() {
      if ( $(this).is(':visible') )
        $(this).fadeTo( 500 , true === to ? 0.5 : 1); //.fadeTo() duration, opacity, callback
    });//$.each()
  };


  /**
  * Fired on api ready
  * May change the site_icon description on load
  * May add a callback to site_icon
  * @return void()
  */
  var _handleFaviconNote = function() {
    //do nothing if (||)
    //1) WP version < 4.3 where site icon has been introduced
    //2) User had not defined a Customizr favicon
    //3) User has already set WP site icon
    if ( ! api.has('site_icon') || 0 === + api( _build_setId('tc_fav_upload') ).get() || + api('site_icon').get() > 0 )
      return;

    var _oldDes     = api.control('site_icon').params.description;
        _newDes     = ['<strong>' , translatedStrings.faviconNote || '' , '</strong><br/><br/>' ].join('') + _oldDes;

    //on api ready
    _printFaviconNote(_newDes );

    //on site icon change
    api('site_icon').callbacks.add( function(to) {
      if ( +to > 0 ) {
        //reset the description to default
        api.control('site_icon').container.find('.description').text(_oldDes);
        //reset the previous customizr favicon setting
        api( _build_setId('tc_fav_upload') ).set("");
      }
      else {
        _printFaviconNote(_newDes );
      }
    });
  };

  //Add a note to the WP control description if user has already defined a favicon with Customizr
  var _printFaviconNote = function( _newDes ) {
    api.control('site_icon').container.find('.description').html(_newDes);
  };

})( wp, jQuery, _);
/**
 * Call to actions
 */
jQuery(function ($) {

  /* CONTRIBUTION TO CUSTOMIZR */
  var donate_displayed  = false,
      is_pro            = 'customizr-pro' == TCControlParams.themeName;
  if (  ! TCControlParams.HideDonate && ! is_pro ) {
    _render_donate_block();
    donate_displayed = true;
  }

  //Main call to action
  if ( TCControlParams.ShowCTA && ! donate_displayed && ! is_pro ) {
   _render_main_cta();
  }

  //In controls call to action
  if ( ! is_pro ) {
    _render_wfc_cta();
    _render_fpu_cta();
    _render_footer_cta();
    _render_gc_cta();
    _render_mc_cta();
  }
  _render_rate_czr();

  function _render_rate_czr() {
    var _cta = _.template(
        $( "script#rate-czr" ).html()
    );
    $('#customize-footer-actions').append( _cta() );
  }

  function _render_donate_block() {
    // Grab the HTML out of our template tag and pre-compile it.
    var donate_template = _.template(
        $( "script#donate_template" ).html()
    );

    $('#customize-info').after( donate_template() );

     //BIND EVENTS
    $('.tc-close-request').click( function(e) {
      e.preventDefault();
      $('.donate-alert').slideToggle("fast");
      $(this).hide();
    });

    $('.tc-hide-donate').click( function(e) {
      _ajax_save();
      setTimeout(function(){
          $('#tc-donate-customizer').slideToggle("fast");
      }, 200);
    });

    $('.tc-cancel-hide-donate').click( function(e) {
      $('.donate-alert').slideToggle("fast");
      setTimeout(function(){
          $('.tc-close-request').show();
      }, 200);
    });
  }//end of donate block


  function _render_main_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#main_cta" ).html()
    );
    $('#customize-info').after( _cta() );
  }

  function _render_wfc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#wfc_cta" ).html()
    );
    $('li[id*="tc_body_font_size"]').append( _cta() );
  }

  function _render_fpu_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#fpu_cta" ).html()
    );
    $('li[id*="tc_featured_text_three"]').append( _cta() );
  }

  function _render_gc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#gc_cta" ).html()
    );
    $('li[id*="tc_post_list_show_thumb"] > .tc-customizr-title').before( _cta() );
  }

  function _render_mc_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#mc_cta" ).html()
    );
    $('li[id*="tc_theme_options-tc_display_menu_label"]').append( _cta() );
  }

  function _render_footer_cta() {
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#footer_cta" ).html()
    );
    $('li[id*="tc_show_back_to_top"]').append( _cta() );
  }

  function _ajax_save() {
      var AjaxUrl         = TCControlParams.AjaxUrl,
      query = {
          action  : 'hide_donate',
          TCnonce :  TCControlParams.TCNonce,
          wp_customize : 'on'
      },
      request = $.post( AjaxUrl, query );
      request.done( function( response ) {
          // Check if the user is logged out.
          if ( '0' === response ) {
              return;
          }
          // Check for cheaters.
          if ( '-1' === response ) {
              return;
          }
      });
  }//end of function
});
//DOM READY :
//1) FIRE SPECIFIC INPUT PLUGINS
//2) ADD SOME COOL STUFFS
//3) SPECIFIC CONTROLS ACTIONS
(function (wp, $) {
  $( function($) {
    var api = wp.customize || api;
    /* GRID */
    var _build_setId = function ( name ) {
      return -1 == name.indexOf( 'tc_theme_options') ? [ 'tc_theme_options[' , name  , ']' ].join('') : name;
    };
    var _grid_design_controls = [
      'tc_grid_in_blog',
      'tc_grid_in_archive',
      'tc_grid_in_search',
      'tc_grid_thumb_height',
      'tc_grid_shadow',
      'tc_grid_bottom_border',
      'tc_grid_icons',
      'tc_grid_num_words'
    ];

    var _build_control_id = function( _control ) {
      return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
    };

    var _get_grid_design_controls = function() {
      return $( _grid_design_controls.map( function( _control ) {
        return _build_control_id( _control );
      }).join(',') );
    };

    //hide design controls on load
    $( _get_grid_design_controls() ).addClass('tc-grid-design').hide();

    $('.tc-grid-toggle-controls').click( function() {
      $( _get_grid_design_controls() ).slideToggle('fast');
      $(this).toggleClass('open');
    } );

    /* RECENTER CURRENT SECTIONS */
    $('.accordion-section').not('.control-panel').click( function () {
      _recenter_current_section($(this));
    });

    function _recenter_current_section( section ) {
      var $siblings               = section.siblings( '.open' );
      //check if clicked element is above or below sibling with offset.top
      if ( 0 !== $siblings.length &&  $siblings.offset().top < 0 ) {
        $('.wp-full-overlay-sidebar-content').animate({
              scrollTop:  - $('#customize-theme-controls').offset().top - $siblings.height() + section.offset().top + $('.wp-full-overlay-sidebar-content').offset().top
        }, 700);
      }
    }//end of fn

    /* ADD GOOGLE IN TITLE */
    $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : 'http://www.google.com/images/logos/google_logo_41.png' , height : 20 });
    $('#accordion-section-fonts_sec').prepend($g_logo);


    /* CHECK */
    //init icheck only if not already initiated
    //exclude widget inputs
    $('input[type=checkbox]').not('input[id*="widget"]').each( function() {
      if ( 0 === $(this).closest('div[class^="icheckbox"]').length ) {
        $(this).iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        })
        .on( 'ifChanged', function(e){
            $(e.currentTarget).trigger('change');
        });
      }
    });

    /* SELECT */
    //Exclude skin
    $('select[data-customize-setting-link]').not('.select2')
      .each( function() {
        $(this).selecter({
        //triggers a change event on the view, passing the newly selected value + index as parameters.
        // callback : function(value, index) {
        //   self.triggerSettingChange( window.event || {} , value, index); // first param is a null event.
        // }
        });
    });

    //Multipicker
    //http://ivaynberg.github.io/select2/#documentation
    $('select.tc_multiple_picker').select2({
      closeOnSelect: false,
      formatSelection: tcEscapeMarkup
    });
    function tcEscapeMarkup(obj) {
      //trim dashes
      return obj.text.replace(/\u2013|\u2014/g, "");
    }

    //SKINS
    //http://ivaynberg.github.io/select2/#documentation
    $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2({
        minimumResultsForSearch: -1, //no search box needed
        formatResult: paintSkinOptionElement,
        formatSelection: paintSkinOptionElement,
        escapeMarkup: function(m) { return m; }
    }).on("select2-highlight", function(e) {
      //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
      $(this).select2("val" , e.val, true );
    });
    //Skins handled with select2
    function paintSkinOptionElement(state) {
        if (!state.id) return state.text; // optgroup
        return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
    }

    //FONTS
    $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2({
        minimumResultsForSearch: -1, //no search box needed
        formatResult: paintFontOptionElement,
        formatSelection: paintFontOptionElement,
        escapeMarkup: function(m) { return m; }
    }).on("select2-highlight", function(e) {
      //triggerChange = true @see val method doc here http://ivaynberg.github.io/select2/#documentation
      $(this).select2("val" , e.val, true );
    });
    function paintFontOptionElement(state) {
        if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
          return '<img class="tc-google-logo" src="http://www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
        else if ( ! state.id )
          return state.text;// optgroup different than google font
        return '<span class="tc-select2-font">' + state.text + '<span>';
    }
    //Fixes the non closing bug for the select2 dropdown
    $('#customize-controls').on('click' , function() { $('select[data-customize-setting-link]').select2("close"); } );

    /* NUMBER */
    $('input[type="number"]').stepper();

  });//end of $( function($) ) dom ready

})( wp, jQuery);
