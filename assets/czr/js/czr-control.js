/*! addEventListener Polyfill ie9- http://stackoverflow.com/a/27790212*/
window.addEventListener=window.addEventListener||function(a,b){window.attachEvent("on"+a,b)},/*!  Datenow Polyfill ie9- https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now */
Date.now||(Date.now=function(){return(new Date).getTime()}),/*! Object.create monkey patch ie8 http://stackoverflow.com/a/18020326 */
Object.create||(Object.create=function(a,b){function c(){}if("undefined"!=typeof b)throw"The multiple-argument version of Object.create is not provided by this browser and cannot be shimmed.";return c.prototype=a,new c}),/*! https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/filter */
Array.prototype.filter||(Array.prototype.filter=function(a){"use strict";if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];a.call(e,g,f,b)&&d.push(g)}return d}),/*! map was added to the ECMA-262 standard in the 5th edition */
Array.prototype.map||(Array.prototype.map=function(a,b){var c,d,e;if(null===this)throw new TypeError(" this is null or not defined");var f=Object(this),g=f.length>>>0;if("function"!=typeof a)throw new TypeError(a+" is not a function");for(arguments.length>1&&(c=b),d=new Array(g),e=0;e<g;){var h,i;e in f&&(h=f[e],i=a.call(c,h,e,f),d[e]=i),e++}return d});/*! iCheck v1.0.1 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
if ( 'function' != typeof(jQuery.fn.iCheck) ) {
  !function(a){function b(a,b,e){var f=a[0],g=/er/.test(e)?p:/bl/.test(e)?n:l,h=e==q?{checked:f[l],disabled:f[n],indeterminate:"true"==a.attr(p)||"false"==a.attr(o)}:f[g];if(/^(ch|di|in)/.test(e)&&!h)c(a,g);else if(/^(un|en|de)/.test(e)&&h)d(a,g);else if(e==q)for(g in h)h[g]?c(a,g,!0):d(a,g,!0);else b&&"toggle"!=e||(b||a[u]("ifClicked"),h?f[r]!==k&&d(a,g):c(a,g))}function c(b,c,e){var q=b[0],u=b.parent(),v=c==l,x=c==p,y=c==n,z=x?o:v?m:"enabled",A=f(b,z+g(q[r])),B=f(b,c+g(q[r]));if(!0!==q[c]){if(!e&&c==l&&q[r]==k&&q.name){var C=b.closest("form"),D='input[name="'+q.name+'"]',D=C.length?C.find(D):a(D);D.each(function(){this!==q&&a(this).data(i)&&d(a(this),c)})}x?(q[c]=!0,q[l]&&d(b,l,"force")):(e||(q[c]=!0),v&&q[p]&&d(b,p,!1)),h(b,v,c,e)}q[n]&&f(b,w,!0)&&u.find("."+j).css(w,"default"),u[s](B||f(b,c)||""),y?u.attr("aria-disabled","true"):u.attr("aria-checked",x?"mixed":"true"),u[t](A||f(b,z)||"")}function d(a,b,c){var d=a[0],e=a.parent(),i=b==l,k=b==p,q=b==n,u=k?o:i?m:"enabled",v=f(a,u+g(d[r])),x=f(a,b+g(d[r]));!1!==d[b]&&((k||!c||"force"==c)&&(d[b]=!1),h(a,i,u,c)),!d[n]&&f(a,w,!0)&&e.find("."+j).css(w,"pointer"),e[t](x||f(a,b)||""),q?e.attr("aria-disabled","false"):e.attr("aria-checked","false"),e[s](v||f(a,u)||"")}function e(b,c){b.data(i)&&(b.parent().html(b.attr("style",b.data(i).s||"")),c&&b[u](c),b.off(".i").unwrap(),a(v+'[for="'+b[0].id+'"]').add(b.closest(v)).off(".i"))}function f(a,b,c){return a.data(i)?a.data(i).o[b+(c?"":"Class")]:void 0}function g(a){return a.charAt(0).toUpperCase()+a.slice(1)}function h(a,b,c,d){d||(b&&a[u]("ifToggled"),a[u]("ifChanged")[u]("if"+g(c)))}var i="iCheck",j=i+"-helper",k="radio",l="checked",m="un"+l,n="disabled",o="determinate",p="in"+o,q="update",r="type",s="addClass",t="removeClass",u="trigger",v="label",w="cursor",x=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);a.fn[i]=function(f,g){var h='input[type="checkbox"], input[type="'+k+'"]',m=a(),o=function(b){b.each(function(){var b=a(this);m=b.is(h)?m.add(b):m.add(b.find(h))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(f))return f=f.toLowerCase(),o(this),m.each(function(){var c=a(this);"destroy"==f?e(c,"ifDestroyed"):b(c,!0,f),a.isFunction(g)&&g()});if("object"!=typeof f&&f)return this;var w=a.extend({checkedClass:l,disabledClass:n,indeterminateClass:p,labelHover:!0,aria:!1},f),y=w.handle,z=w.hoverClass||"hover",A=w.focusClass||"focus",B=w.activeClass||"active",C=!!w.labelHover,D=w.labelHoverClass||"hover",E=0|(""+w.increaseArea).replace("%","");return("checkbox"==y||y==k)&&(h='input[type="'+y+'"]'),-50>E&&(E=-50),o(this),m.each(function(){var f=a(this);e(f);var g=this,h=g.id,m=-E+"%",o=100+2*E+"%",o={position:"absolute",top:m,left:m,display:"block",width:o,height:o,margin:0,padding:0,background:"#fff",border:0,opacity:0},m=x?{position:"absolute",visibility:"hidden"}:E?o:{position:"absolute",opacity:0},p="checkbox"==g[r]?w.checkboxClass||"icheckbox":w.radioClass||"i"+k,y=a(v+'[for="'+h+'"]').add(f.closest(v)),F=!!w.aria,G=i+"-"+Math.random().toString(36).substr(2,6),H='<div class="'+p+'" '+(F?'role="'+g[r]+'" ':"");F&&y.each(function(){H+='aria-labelledby="',this.id?H+=this.id:(this.id=G,H+=G),H+='"'}),H=f.wrap(H+"/>")[u]("ifCreated").parent().append(w.insert),o=a('<ins class="'+j+'"/>').css(o).appendTo(H),f.data(i,{o:w,s:f.attr("style")}).css(m),w.inheritClass&&H[s](g.className||""),w.inheritID&&h&&H.attr("id",i+"-"+h),"static"==H.css("position")&&H.css("position","relative"),b(f,!0,q),y.length&&y.on("click.i mouseover.i mouseout.i touchbegin.i touchend.i",function(c){var d=c[r],e=a(this);if(!g[n]){if("click"==d){if(a(c.target).is("a"))return;b(f,!1,!0)}else C&&(/ut|nd/.test(d)?(H[t](z),e[t](D)):(H[s](z),e[s](D)));if(!x)return!1;c.stopPropagation()}}),f.on("click.i focus.i blur.i keyup.i keydown.i keypress.i",function(a){var b=a[r];return a=a.keyCode,"click"==b?!1:"keydown"==b&&32==a?(g[r]==k&&g[l]||(g[l]?d(f,l):c(f,l)),!1):("keyup"==b&&g[r]==k?!g[l]&&c(f,l):/us|ur/.test(b)&&H["blur"==b?t:s](A),void 0)}),o.on("click mousedown mouseup mouseover mouseout touchbegin.i touchend.i",function(a){var c=a[r],d=/wn|up/.test(c)?B:z;if(!g[n]){if("click"==c?b(f,!1,!0):(/wn|er|in/.test(c)?H[s](d):H[t](d+" "+B),y.length&&C&&d==z&&y[/ut|nd/.test(c)?t:s](D)),!x)return!1;a.stopPropagation()}})})}}(window.jQuery||window.Zepto);
}
if ( 'function' != typeof(jQuery.fn.selecter) ) {
  !function(a,b){"use strict";function c(b){b=a.extend({},x,b||{}),null===w&&(w=a("body"));for(var c=a(this),e=0,f=c.length;f>e;e++)d(c.eq(e),b);return c}function d(b,c){if(!b.hasClass("selecter-element")){c=a.extend({},c,b.data("selecter-options")),c.external&&(c.links=!0);var d=b.find("option, optgroup"),g=d.filter("option"),h=g.filter(":selected"),n=""!==c.label?-1:g.index(h),p=c.links?"nav":"div";c.tabIndex=b[0].tabIndex,b[0].tabIndex=-1,c.multiple=b.prop("multiple"),c.disabled=b.is(":disabled");var q="<"+p+' class="selecter '+c.customClass;v?q+=" mobile":c.cover&&(q+=" cover"),q+=c.multiple?" multiple":" closed",c.disabled&&(q+=" disabled"),q+='" tabindex="'+c.tabIndex+'">',c.multiple||(q+='<span class="selecter-selected'+(""!==c.label?" placeholder":"")+'">',q+=a("<span></span").text(r(""!==c.label?c.label:h.text(),c.trim)).html(),q+="</span>"),q+='<div class="selecter-options">',q+="</div>",q+="</"+p+">",b.addClass("selecter-element").after(q);var s=b.next(".selecter"),u=a.extend({$select:b,$allOptions:d,$options:g,$selecter:s,$selected:s.find(".selecter-selected"),$itemsWrapper:s.find(".selecter-options"),index:-1,guid:t++},c);e(u),o(n,u),void 0!==a.fn.scroller&&u.$itemsWrapper.scroller(),u.$selecter.on("touchstart.selecter click.selecter",".selecter-selected",u,f).on("click.selecter",".selecter-item",u,j).on("close.selecter",u,i).data("selecter",u),u.$select.on("change.selecter",u,k),v||(u.$selecter.on("focus.selecter",u,l).on("blur.selecter",u,m),u.$select.on("focus.selecter",u,function(a){a.data.$selecter.trigger("focus")}))}}function e(b){for(var c="",d=b.links?"a":"span",e=0,f=0,g=b.$allOptions.length;g>f;f++){var h=b.$allOptions.eq(f);if("OPTGROUP"===h[0].tagName)c+='<span class="selecter-group',h.is(":disabled")&&(c+=" disabled"),c+='">'+h.attr("label")+"</span>";else{var i=h.val();h.attr("value")||h.attr("value",i),c+="<"+d+' class="selecter-item',h.is(":selected")&&""===b.label&&(c+=" selected"),h.is(":disabled")&&(c+=" disabled"),c+='" ',c+=b.links?'href="'+i+'"':'data-value="'+i+'"',c+=">"+a("<span></span>").text(r(h.text(),b.trim)).html()+"</"+d+">",e++}}b.$itemsWrapper.html(c),b.$items=b.$selecter.find(".selecter-item")}function f(c){c.preventDefault(),c.stopPropagation();var d=c.data;if(!d.$select.is(":disabled"))if(a(".selecter").not(d.$selecter).trigger("close.selecter",[d]),v){var e=d.$select[0];if(b.document.createEvent){var f=b.document.createEvent("MouseEvents");f.initMouseEvent("mousedown",!1,!0,b,0,0,0,0,0,!1,!1,!1,!1,0,null),e.dispatchEvent(f)}else e.fireEvent&&e.fireEvent("onmousedown")}else d.$selecter.hasClass("closed")?g(c):d.$selecter.hasClass("open")&&i(c)}function g(b){b.preventDefault(),b.stopPropagation();var c=b.data;if(!c.$selecter.hasClass("open")){var d=c.$selecter.offset(),e=w.outerHeight(),f=c.$itemsWrapper.outerHeight(!0),g=c.index>=0?c.$items.eq(c.index).position():{left:0,top:0};d.top+f>e&&c.$selecter.addClass("bottom"),c.$itemsWrapper.show(),c.$selecter.removeClass("closed").addClass("open"),w.on("click.selecter-"+c.guid,":not(.selecter-options)",c,h),void 0!==a.fn.scroller?c.$itemsWrapper.scroller("scroll",c.$itemsWrapper.find(".scroller-content").scrollTop()+g.top,0).scroller("reset"):c.$itemsWrapper.scrollTop(c.$itemsWrapper.scrollTop()+g.top)}}function h(b){b.preventDefault(),b.stopPropagation(),0===a(b.currentTarget).parents(".selecter").length&&i(b)}function i(a){a.preventDefault(),a.stopPropagation();var b=a.data;b.$selecter.hasClass("open")&&(b.$itemsWrapper.hide(),b.$selecter.removeClass("open bottom").addClass("closed"),w.off(".selecter-"+b.guid))}function j(b){b.preventDefault(),b.stopPropagation();var c=a(this),d=b.data;if(!d.$select.is(":disabled")){if(d.$itemsWrapper.is(":visible")){var e=d.$items.index(c);o(e,d),p(d)}d.multiple||i(b)}}function k(b,c){var d=a(this),e=b.data;if(!c&&!e.multiple){var f=e.$options.index(e.$options.filter("[value='"+s(d.val())+"']"));o(f,e),p(e)}}function l(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$select.is(":disabled")||c.multiple||(c.$selecter.addClass("focus").on("keydown.selecter"+c.guid,c,n),a(".selecter").not(c.$selecter).trigger("close.selecter",[c]))}function m(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$selecter.removeClass("focus").off("keydown.selecter"+c.guid+" keyup.selecter"+c.guid),a(".selecter").not(c.$selecter).trigger("close.selecter",[c])}function n(b){var c=b.data;if(13===b.keyCode)c.$selecter.hasClass("open")&&(i(b),o(c.index,c)),p(c);else if(!(9===b.keyCode||b.metaKey||b.altKey||b.ctrlKey||b.shiftKey)){b.preventDefault(),b.stopPropagation();var d=c.$items.length-1,e=c.index<0?0:c.index;if(a.inArray(b.keyCode,u?[38,40,37,39]:[38,40])>-1)e+=38===b.keyCode||u&&37===b.keyCode?-1:1,0>e&&(e=0),e>d&&(e=d);else{var f,g,h=String.fromCharCode(b.keyCode).toUpperCase();for(g=c.index+1;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}if(0>e)for(g=0;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}}e>=0&&o(e,c)}}function o(a,b){var c=b.$items.eq(a),d=c.hasClass("selected"),e=c.hasClass("disabled");if(!e){if(-1===a&&""!==b.label)b.$selected.html(b.label);else if(d)b.multiple&&(b.$options.eq(a).prop("selected",null),c.removeClass("selected"));else{{var f=c.html();c.data("value")}b.multiple?b.$options.eq(a).prop("selected",!0):(b.$selected.html(f).removeClass("placeholder"),b.$items.filter(".selected").removeClass("selected"),b.$select[0].selectedIndex=a),c.addClass("selected")}(!d||b.multiple)&&(b.index=a)}}function p(a){a.links?q(a):(a.callback.call(a.$selecter,a.$select.val(),a.index),a.$select.trigger("change",[!0]))}function q(a){var c=a.$select.val();a.external?b.open(c):b.location.href=c}function r(a,b){return 0===b?a:a.length>b?a.substring(0,b)+"...":a}function s(a){return a.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g,"\\$1")}var t=0,u=b.navigator.userAgent.toLowerCase().indexOf("firefox")>-1,v=/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(b.navigator.userAgent||b.navigator.vendor||b.opera),w=null,x={callback:a.noop,cover:!1,customClass:"",label:"",external:!1,links:!1,trim:0},y={defaults:function(b){return x=a.extend(x,b||{}),a(this)},disable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).addClass("disabled"),e.$options.eq(f).prop("disabled",!0)}else e.$selecter.hasClass("open")&&e.$selecter.find(".selecter-selected").trigger("click.selecter"),e.$selecter.addClass("disabled"),e.$select.prop("disabled",!0)})},enable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).removeClass("disabled"),e.$options.eq(f).prop("disabled",!1)}else e.$selecter.removeClass("disabled"),e.$select.prop("disabled",!1)})},destroy:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");d&&(d.$selecter.hasClass("open")&&d.$selecter.find(".selecter-selected").trigger("click.selecter"),void 0!==a.fn.scroller&&d.$selecter.find(".selecter-options").scroller("destroy"),d.$select[0].tabIndex=d.tabIndex,d.$select.off(".selecter").removeClass("selecter-element").show(),d.$selecter.off(".selecter").remove())})},refresh:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");if(d){var f=d.index;d.$allOptions=d.$select.find("option, optgroup"),d.$options=d.$allOptions.filter("option"),d.index=-1,f=d.$options.index(d.$options.filter(":selected")),e(d),o(f,d)}})}};a.fn.selecter=function(a){return y[a]?y[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:c.apply(this,arguments)},a.selecter=function(a){"defaults"===a&&y.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,window);
}
if ( 'function' != typeof(jQuery.fn.stepper) ) {
  !function(a){"use strict";function b(b){b=a.extend({},k,b||{});for(var d=a(this),e=0,f=d.length;f>e;e++)c(d.eq(e),b);return d}function c(b,c){if(!b.hasClass("stepper-input")){c=a.extend({},c,b.data("stepper-options"));var e=parseFloat(b.attr("min")),f=parseFloat(b.attr("max")),g=parseFloat(b.attr("step"))||1;b.addClass("stepper-input").wrap('<div class="stepper '+c.customClass+'" />').after('<span class="stepper-arrow up">'+c.labels.up+'</span><span class="stepper-arrow down">'+c.labels.down+"</span>");var h=b.parent(".stepper"),j=a.extend({$stepper:h,$input:b,$arrow:h.find(".stepper-arrow"),min:void 0===typeof e||isNaN(e)?!1:e,max:void 0===typeof f||isNaN(f)?!1:f,step:void 0===typeof g||isNaN(g)?1:g,timer:null},c);j.digits=i(j.step),b.is(":disabled")&&h.addClass("disabled"),h.on("touchstart.stepper mousedown.stepper",".stepper-arrow",j,d).data("stepper",j)}}function d(b){b.preventDefault(),b.stopPropagation(),e(b);var c=b.data;if(!c.$input.is(":disabled")&&!c.$stepper.hasClass("disabled")){var d=a(b.target).hasClass("up")?c.step:-c.step;c.timer=g(c.timer,125,function(){f(c,d,!1)}),f(c,d),a("body").on("touchend.stepper mouseup.stepper",c,e)}}function e(b){b.preventDefault(),b.stopPropagation();var c=b.data;h(c.timer),a("body").off(".stepper")}function f(a,b){var c=parseFloat(a.$input.val()),d=b;void 0===typeof c||isNaN(c)?d=a.min!==!1?a.min:0:a.min!==!1&&c<a.min?d=a.min:d+=c;var e=(d-a.min)%a.step;0!==e&&(d-=e),a.min!==!1&&d<a.min&&(d=a.min),a.max!==!1&&d>a.max&&(d-=a.step),d!==c&&(d=j(d,a.digits),a.$input.val(d).trigger("change"))}function g(a,b,c){return h(a),setInterval(c,b)}function h(a){a&&(clearInterval(a),a=null)}function i(a){var b=String(a);return b.indexOf(".")>-1?b.length-b.indexOf(".")-1:0}function j(a,b){var c=Math.pow(10,b);return Math.round(a*c)/c}var k={customClass:"",labels:{up:"Up",down:"Down"}},l={defaults:function(b){return k=a.extend(k,b||{}),a(this)},destroy:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$stepper.off(".stepper").find(".stepper-arrow").remove(),b.$input.unwrap().removeClass("stepper-input"))})},disable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled","disabled"),b.$stepper.addClass("disabled"))})},enable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled",null),b.$stepper.removeClass("disabled"))})}};a.fn.stepper=function(a){return l[a]?l[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:b.apply(this,arguments)},a.stepper=function(a){"defaults"===a&&l.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,this);
}/*! Select2 4.0.3 | https://github.com/select2/select2/blob/master/LICENSE.md */!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){var b=function(){if(a&&a.fn&&a.fn.select2&&a.fn.select2.amd)var b=a.fn.select2.amd;var b;return function(){if(!b||!b.requirejs){b?c=b:b={};var a,c,d;!function(b){function e(a,b){return u.call(a,b)}function f(a,b){var c,d,e,f,g,h,i,j,k,l,m,n=b&&b.split("/"),o=s.map,p=o&&o["*"]||{};if(a&&"."===a.charAt(0))if(b){for(a=a.split("/"),g=a.length-1,s.nodeIdCompat&&w.test(a[g])&&(a[g]=a[g].replace(w,"")),a=n.slice(0,n.length-1).concat(a),k=0;k<a.length;k+=1)if(m=a[k],"."===m)a.splice(k,1),k-=1;else if(".."===m){if(1===k&&(".."===a[2]||".."===a[0]))break;k>0&&(a.splice(k-1,2),k-=2)}a=a.join("/")}else 0===a.indexOf("./")&&(a=a.substring(2));if((n||p)&&o){for(c=a.split("/"),k=c.length;k>0;k-=1){if(d=c.slice(0,k).join("/"),n)for(l=n.length;l>0;l-=1)if(e=o[n.slice(0,l).join("/")],e&&(e=e[d])){f=e,h=k;break}if(f)break;!i&&p&&p[d]&&(i=p[d],j=k)}!f&&i&&(f=i,h=j),f&&(c.splice(0,h,f),a=c.join("/"))}return a}function g(a,c){return function(){var d=v.call(arguments,0);return"string"!=typeof d[0]&&1===d.length&&d.push(null),n.apply(b,d.concat([a,c]))}}function h(a){return function(b){return f(b,a)}}function i(a){return function(b){q[a]=b}}function j(a){if(e(r,a)){var c=r[a];delete r[a],t[a]=!0,m.apply(b,c)}if(!e(q,a)&&!e(t,a))throw new Error("No "+a);return q[a]}function k(a){var b,c=a?a.indexOf("!"):-1;return c>-1&&(b=a.substring(0,c),a=a.substring(c+1,a.length)),[b,a]}function l(a){return function(){return s&&s.config&&s.config[a]||{}}}var m,n,o,p,q={},r={},s={},t={},u=Object.prototype.hasOwnProperty,v=[].slice,w=/\.js$/;o=function(a,b){var c,d=k(a),e=d[0];return a=d[1],e&&(e=f(e,b),c=j(e)),e?a=c&&c.normalize?c.normalize(a,h(b)):f(a,b):(a=f(a,b),d=k(a),e=d[0],a=d[1],e&&(c=j(e))),{f:e?e+"!"+a:a,n:a,pr:e,p:c}},p={require:function(a){return g(a)},exports:function(a){var b=q[a];return"undefined"!=typeof b?b:q[a]={}},module:function(a){return{id:a,uri:"",exports:q[a],config:l(a)}}},m=function(a,c,d,f){var h,k,l,m,n,s,u=[],v=typeof d;if(f=f||a,"undefined"===v||"function"===v){for(c=!c.length&&d.length?["require","exports","module"]:c,n=0;n<c.length;n+=1)if(m=o(c[n],f),k=m.f,"require"===k)u[n]=p.require(a);else if("exports"===k)u[n]=p.exports(a),s=!0;else if("module"===k)h=u[n]=p.module(a);else if(e(q,k)||e(r,k)||e(t,k))u[n]=j(k);else{if(!m.p)throw new Error(a+" missing "+k);m.p.load(m.n,g(f,!0),i(k),{}),u[n]=q[k]}l=d?d.apply(q[a],u):void 0,a&&(h&&h.exports!==b&&h.exports!==q[a]?q[a]=h.exports:l===b&&s||(q[a]=l))}else a&&(q[a]=d)},a=c=n=function(a,c,d,e,f){if("string"==typeof a)return p[a]?p[a](c):j(o(a,c).f);if(!a.splice){if(s=a,s.deps&&n(s.deps,s.callback),!c)return;c.splice?(a=c,c=d,d=null):a=b}return c=c||function(){},"function"==typeof d&&(d=e,e=f),e?m(b,a,c,d):setTimeout(function(){m(b,a,c,d)},4),n},n.config=function(a){return n(a)},a._defined=q,d=function(a,b,c){if("string"!=typeof a)throw new Error("See almond README: incorrect module build, no module name");b.splice||(c=b,b=[]),e(q,a)||e(r,a)||(r[a]=[a,b,c])},d.amd={jQuery:!0}}(),b.requirejs=a,b.require=c,b.define=d}}(),b.define("almond",function(){}),b.define("jquery",[],function(){var b=a||$;return null==b&&console&&console.error&&console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page."),b}),b.define("select2/utils",["jquery"],function(a){function b(a){var b=a.prototype,c=[];for(var d in b){var e=b[d];"function"==typeof e&&"constructor"!==d&&c.push(d)}return c}var c={};c.Extend=function(a,b){function c(){this.constructor=a}var d={}.hasOwnProperty;for(var e in b)d.call(b,e)&&(a[e]=b[e]);return c.prototype=b.prototype,a.prototype=new c,a.__super__=b.prototype,a},c.Decorate=function(a,c){function d(){var b=Array.prototype.unshift,d=c.prototype.constructor.length,e=a.prototype.constructor;d>0&&(b.call(arguments,a.prototype.constructor),e=c.prototype.constructor),e.apply(this,arguments)}function e(){this.constructor=d}var f=b(c),g=b(a);c.displayName=a.displayName,d.prototype=new e;for(var h=0;h<g.length;h++){var i=g[h];d.prototype[i]=a.prototype[i]}for(var j=(function(a){var b=function(){};a in d.prototype&&(b=d.prototype[a]);var e=c.prototype[a];return function(){var a=Array.prototype.unshift;return a.call(arguments,b),e.apply(this,arguments)}}),k=0;k<f.length;k++){var l=f[k];d.prototype[l]=j(l)}return d};var d=function(){this.listeners={}};return d.prototype.on=function(a,b){this.listeners=this.listeners||{},a in this.listeners?this.listeners[a].push(b):this.listeners[a]=[b]},d.prototype.trigger=function(a){var b=Array.prototype.slice,c=b.call(arguments,1);this.listeners=this.listeners||{},null==c&&(c=[]),0===c.length&&c.push({}),c[0]._type=a,a in this.listeners&&this.invoke(this.listeners[a],b.call(arguments,1)),"*"in this.listeners&&this.invoke(this.listeners["*"],arguments)},d.prototype.invoke=function(a,b){for(var c=0,d=a.length;d>c;c++)a[c].apply(this,b)},c.Observable=d,c.generateChars=function(a){for(var b="",c=0;a>c;c++){var d=Math.floor(36*Math.random());b+=d.toString(36)}return b},c.bind=function(a,b){return function(){a.apply(b,arguments)}},c._convertData=function(a){for(var b in a){var c=b.split("-"),d=a;if(1!==c.length){for(var e=0;e<c.length;e++){var f=c[e];f=f.substring(0,1).toLowerCase()+f.substring(1),f in d||(d[f]={}),e==c.length-1&&(d[f]=a[b]),d=d[f]}delete a[b]}}return a},c.hasScroll=function(b,c){var d=a(c),e=c.style.overflowX,f=c.style.overflowY;return e!==f||"hidden"!==f&&"visible"!==f?"scroll"===e||"scroll"===f?!0:d.innerHeight()<c.scrollHeight||d.innerWidth()<c.scrollWidth:!1},c.escapeMarkup=function(a){var b={"\\":"&#92;","&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#47;"};return"string"!=typeof a?a:String(a).replace(/[&<>"'\/\\]/g,function(a){return b[a]})},c.appendMany=function(b,c){if("1.7"===a.fn.jquery.substr(0,3)){var d=a();a.map(c,function(a){d=d.add(a)}),c=d}b.append(c)},c}),b.define("select2/results",["jquery","./utils"],function(a,b){function c(a,b,d){this.$element=a,this.data=d,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<ul class="select2-results__options" role="tree"></ul>');return this.options.get("multiple")&&b.attr("aria-multiselectable","true"),this.$results=b,b},c.prototype.clear=function(){this.$results.empty()},c.prototype.displayMessage=function(b){var c=this.options.get("escapeMarkup");this.clear(),this.hideLoading();var d=a('<li role="treeitem" aria-live="assertive" class="select2-results__option"></li>'),e=this.options.get("translations").get(b.message);d.append(c(e(b.args))),d[0].className+=" select2-results__message",this.$results.append(d)},c.prototype.hideMessages=function(){this.$results.find(".select2-results__message").remove()},c.prototype.append=function(a){this.hideLoading();var b=[];if(null==a.results||0===a.results.length)return void(0===this.$results.children().length&&this.trigger("results:message",{message:"noResults"}));a.results=this.sort(a.results);for(var c=0;c<a.results.length;c++){var d=a.results[c],e=this.option(d);b.push(e)}this.$results.append(b)},c.prototype.position=function(a,b){var c=b.find(".select2-results");c.append(a)},c.prototype.sort=function(a){var b=this.options.get("sorter");return b(a)},c.prototype.highlightFirstItem=function(){var a=this.$results.find(".select2-results__option[aria-selected]"),b=a.filter("[aria-selected=true]");b.length>0?b.first().trigger("mouseenter"):a.first().trigger("mouseenter"),this.ensureHighlightVisible()},c.prototype.setClasses=function(){var b=this;this.data.current(function(c){var d=a.map(c,function(a){return a.id.toString()}),e=b.$results.find(".select2-results__option[aria-selected]");e.each(function(){var b=a(this),c=a.data(this,"data"),e=""+c.id;null!=c.element&&c.element.selected||null==c.element&&a.inArray(e,d)>-1?b.attr("aria-selected","true"):b.attr("aria-selected","false")})})},c.prototype.showLoading=function(a){this.hideLoading();var b=this.options.get("translations").get("searching"),c={disabled:!0,loading:!0,text:b(a)},d=this.option(c);d.className+=" loading-results",this.$results.prepend(d)},c.prototype.hideLoading=function(){this.$results.find(".loading-results").remove()},c.prototype.option=function(b){var c=document.createElement("li");c.className="select2-results__option";var d={role:"treeitem","aria-selected":"false"};b.disabled&&(delete d["aria-selected"],d["aria-disabled"]="true"),null==b.id&&delete d["aria-selected"],null!=b._resultId&&(c.id=b._resultId),b.title&&(c.title=b.title),b.children&&(d.role="group",d["aria-label"]=b.text,delete d["aria-selected"]);for(var e in d){var f=d[e];c.setAttribute(e,f)}if(b.children){var g=a(c),h=document.createElement("strong");h.className="select2-results__group";a(h);this.template(b,h);for(var i=[],j=0;j<b.children.length;j++){var k=b.children[j],l=this.option(k);i.push(l)}var m=a("<ul></ul>",{"class":"select2-results__options select2-results__options--nested"});m.append(i),g.append(h),g.append(m)}else this.template(b,c);return a.data(c,"data",b),c},c.prototype.bind=function(b,c){var d=this,e=b.id+"-results";this.$results.attr("id",e),b.on("results:all",function(a){d.clear(),d.append(a.data),b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("results:append",function(a){d.append(a.data),b.isOpen()&&d.setClasses()}),b.on("query",function(a){d.hideMessages(),d.showLoading(a)}),b.on("select",function(){b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("unselect",function(){b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("open",function(){d.$results.attr("aria-expanded","true"),d.$results.attr("aria-hidden","false"),d.setClasses(),d.ensureHighlightVisible()}),b.on("close",function(){d.$results.attr("aria-expanded","false"),d.$results.attr("aria-hidden","true"),d.$results.removeAttr("aria-activedescendant")}),b.on("results:toggle",function(){var a=d.getHighlightedResults();0!==a.length&&a.trigger("mouseup")}),b.on("results:select",function(){var a=d.getHighlightedResults();if(0!==a.length){var b=a.data("data");"true"==a.attr("aria-selected")?d.trigger("close",{}):d.trigger("select",{data:b})}}),b.on("results:previous",function(){var a=d.getHighlightedResults(),b=d.$results.find("[aria-selected]"),c=b.index(a);if(0!==c){var e=c-1;0===a.length&&(e=0);var f=b.eq(e);f.trigger("mouseenter");var g=d.$results.offset().top,h=f.offset().top,i=d.$results.scrollTop()+(h-g);0===e?d.$results.scrollTop(0):0>h-g&&d.$results.scrollTop(i)}}),b.on("results:next",function(){var a=d.getHighlightedResults(),b=d.$results.find("[aria-selected]"),c=b.index(a),e=c+1;if(!(e>=b.length)){var f=b.eq(e);f.trigger("mouseenter");var g=d.$results.offset().top+d.$results.outerHeight(!1),h=f.offset().top+f.outerHeight(!1),i=d.$results.scrollTop()+h-g;0===e?d.$results.scrollTop(0):h>g&&d.$results.scrollTop(i)}}),b.on("results:focus",function(a){a.element.addClass("select2-results__option--highlighted")}),b.on("results:message",function(a){d.displayMessage(a)}),a.fn.mousewheel&&this.$results.on("mousewheel",function(a){var b=d.$results.scrollTop(),c=d.$results.get(0).scrollHeight-b+a.deltaY,e=a.deltaY>0&&b-a.deltaY<=0,f=a.deltaY<0&&c<=d.$results.height();e?(d.$results.scrollTop(0),a.preventDefault(),a.stopPropagation()):f&&(d.$results.scrollTop(d.$results.get(0).scrollHeight-d.$results.height()),a.preventDefault(),a.stopPropagation())}),this.$results.on("mouseup",".select2-results__option[aria-selected]",function(b){var c=a(this),e=c.data("data");return"true"===c.attr("aria-selected")?void(d.options.get("multiple")?d.trigger("unselect",{originalEvent:b,data:e}):d.trigger("close",{})):void d.trigger("select",{originalEvent:b,data:e})}),this.$results.on("mouseenter",".select2-results__option[aria-selected]",function(b){var c=a(this).data("data");d.getHighlightedResults().removeClass("select2-results__option--highlighted"),d.trigger("results:focus",{data:c,element:a(this)})})},c.prototype.getHighlightedResults=function(){var a=this.$results.find(".select2-results__option--highlighted");return a},c.prototype.destroy=function(){this.$results.remove()},c.prototype.ensureHighlightVisible=function(){var a=this.getHighlightedResults();if(0!==a.length){var b=this.$results.find("[aria-selected]"),c=b.index(a),d=this.$results.offset().top,e=a.offset().top,f=this.$results.scrollTop()+(e-d),g=e-d;f-=2*a.outerHeight(!1),2>=c?this.$results.scrollTop(0):(g>this.$results.outerHeight()||0>g)&&this.$results.scrollTop(f)}},c.prototype.template=function(b,c){var d=this.options.get("templateResult"),e=this.options.get("escapeMarkup"),f=d(b,c);null==f?c.style.display="none":"string"==typeof f?c.innerHTML=e(f):a(c).append(f)},c}),b.define("select2/keys",[],function(){var a={BACKSPACE:8,TAB:9,ENTER:13,SHIFT:16,CTRL:17,ALT:18,ESC:27,SPACE:32,PAGE_UP:33,PAGE_DOWN:34,END:35,HOME:36,LEFT:37,UP:38,RIGHT:39,DOWN:40,DELETE:46};return a}),b.define("select2/selection/base",["jquery","../utils","../keys"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,b.Observable),d.prototype.render=function(){var b=a('<span class="select2-selection" role="combobox"  aria-haspopup="true" aria-expanded="false"></span>');return this._tabindex=0,null!=this.$element.data("old-tabindex")?this._tabindex=this.$element.data("old-tabindex"):null!=this.$element.attr("tabindex")&&(this._tabindex=this.$element.attr("tabindex")),b.attr("title",this.$element.attr("title")),b.attr("tabindex",this._tabindex),this.$selection=b,b},d.prototype.bind=function(a,b){var d=this,e=(a.id+"-container",a.id+"-results");this.container=a,this.$selection.on("focus",function(a){d.trigger("focus",a)}),this.$selection.on("blur",function(a){d._handleBlur(a)}),this.$selection.on("keydown",function(a){d.trigger("keypress",a),a.which===c.SPACE&&a.preventDefault()}),a.on("results:focus",function(a){d.$selection.attr("aria-activedescendant",a.data._resultId)}),a.on("selection:update",function(a){d.update(a.data)}),a.on("open",function(){d.$selection.attr("aria-expanded","true"),d.$selection.attr("aria-owns",e),d._attachCloseHandler(a)}),a.on("close",function(){d.$selection.attr("aria-expanded","false"),d.$selection.removeAttr("aria-activedescendant"),d.$selection.removeAttr("aria-owns"),d.$selection.focus(),d._detachCloseHandler(a)}),a.on("enable",function(){d.$selection.attr("tabindex",d._tabindex)}),a.on("disable",function(){d.$selection.attr("tabindex","-1")})},d.prototype._handleBlur=function(b){var c=this;window.setTimeout(function(){document.activeElement==c.$selection[0]||a.contains(c.$selection[0],document.activeElement)||c.trigger("blur",b)},1)},d.prototype._attachCloseHandler=function(b){a(document.body).on("mousedown.select2."+b.id,function(b){var c=a(b.target),d=c.closest(".select2"),e=a(".select2.select2-container--open");e.each(function(){var b=a(this);if(this!=d[0]){var c=b.data("element");c.select2("close")}})})},d.prototype._detachCloseHandler=function(b){a(document.body).off("mousedown.select2."+b.id)},d.prototype.position=function(a,b){var c=b.find(".selection");c.append(a)},d.prototype.destroy=function(){this._detachCloseHandler(this.container)},d.prototype.update=function(a){throw new Error("The `update` method must be defined in child classes.")},d}),b.define("select2/selection/single",["jquery","./base","../utils","../keys"],function(a,b,c,d){function e(){e.__super__.constructor.apply(this,arguments)}return c.Extend(e,b),e.prototype.render=function(){var a=e.__super__.render.call(this);return a.addClass("select2-selection--single"),a.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'),a},e.prototype.bind=function(a,b){var c=this;e.__super__.bind.apply(this,arguments);var d=a.id+"-container";this.$selection.find(".select2-selection__rendered").attr("id",d),this.$selection.attr("aria-labelledby",d),this.$selection.on("mousedown",function(a){1===a.which&&c.trigger("toggle",{originalEvent:a})}),this.$selection.on("focus",function(a){}),this.$selection.on("blur",function(a){}),a.on("focus",function(b){a.isOpen()||c.$selection.focus()}),a.on("selection:update",function(a){c.update(a.data)})},e.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},e.prototype.display=function(a,b){var c=this.options.get("templateSelection"),d=this.options.get("escapeMarkup");return d(c(a,b))},e.prototype.selectionContainer=function(){return a("<span></span>")},e.prototype.update=function(a){if(0===a.length)return void this.clear();var b=a[0],c=this.$selection.find(".select2-selection__rendered"),d=this.display(b,c);c.empty().append(d),c.prop("title",b.title||b.text)},e}),b.define("select2/selection/multiple",["jquery","./base","../utils"],function(a,b,c){function d(a,b){d.__super__.constructor.apply(this,arguments)}return c.Extend(d,b),d.prototype.render=function(){var a=d.__super__.render.call(this);return a.addClass("select2-selection--multiple"),a.html('<ul class="select2-selection__rendered"></ul>'),a},d.prototype.bind=function(b,c){var e=this;d.__super__.bind.apply(this,arguments),this.$selection.on("click",function(a){e.trigger("toggle",{originalEvent:a})}),this.$selection.on("click",".select2-selection__choice__remove",function(b){if(!e.options.get("disabled")){var c=a(this),d=c.parent(),f=d.data("data");e.trigger("unselect",{originalEvent:b,data:f})}})},d.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},d.prototype.display=function(a,b){var c=this.options.get("templateSelection"),d=this.options.get("escapeMarkup");return d(c(a,b))},d.prototype.selectionContainer=function(){var b=a('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>');return b},d.prototype.update=function(a){if(this.clear(),0!==a.length){for(var b=[],d=0;d<a.length;d++){var e=a[d],f=this.selectionContainer(),g=this.display(e,f);f.append(g),f.prop("title",e.title||e.text),f.data("data",e),b.push(f)}var h=this.$selection.find(".select2-selection__rendered");c.appendMany(h,b)}},d}),b.define("select2/selection/placeholder",["../utils"],function(a){function b(a,b,c){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c)}return b.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},b.prototype.createPlaceholder=function(a,b){var c=this.selectionContainer();return c.html(this.display(b)),c.addClass("select2-selection__placeholder").removeClass("select2-selection__choice"),c},b.prototype.update=function(a,b){var c=1==b.length&&b[0].id!=this.placeholder.id,d=b.length>1;if(d||c)return a.call(this,b);this.clear();var e=this.createPlaceholder(this.placeholder);this.$selection.find(".select2-selection__rendered").append(e)},b}),b.define("select2/selection/allowClear",["jquery","../keys"],function(a,b){function c(){}return c.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),null==this.placeholder&&this.options.get("debug")&&window.console&&console.error&&console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option."),this.$selection.on("mousedown",".select2-selection__clear",function(a){d._handleClear(a)}),b.on("keypress",function(a){d._handleKeyboardClear(a,b)})},c.prototype._handleClear=function(a,b){if(!this.options.get("disabled")){var c=this.$selection.find(".select2-selection__clear");if(0!==c.length){b.stopPropagation();for(var d=c.data("data"),e=0;e<d.length;e++){var f={data:d[e]};if(this.trigger("unselect",f),f.prevented)return}this.$element.val(this.placeholder.id).trigger("change"),this.trigger("toggle",{})}}},c.prototype._handleKeyboardClear=function(a,c,d){d.isOpen()||(c.which==b.DELETE||c.which==b.BACKSPACE)&&this._handleClear(c)},c.prototype.update=function(b,c){if(b.call(this,c),!(this.$selection.find(".select2-selection__placeholder").length>0||0===c.length)){var d=a('<span class="select2-selection__clear">&times;</span>');d.data("data",c),this.$selection.find(".select2-selection__rendered").prepend(d)}},c}),b.define("select2/selection/search",["jquery","../utils","../keys"],function(a,b,c){function d(a,b,c){a.call(this,b,c)}return d.prototype.render=function(b){var c=a('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" /></li>');this.$searchContainer=c,this.$search=c.find("input");var d=b.call(this);return this._transferTabIndex(),d},d.prototype.bind=function(a,b,d){var e=this;a.call(this,b,d),b.on("open",function(){e.$search.trigger("focus")}),b.on("close",function(){e.$search.val(""),e.$search.removeAttr("aria-activedescendant"),e.$search.trigger("focus")}),b.on("enable",function(){e.$search.prop("disabled",!1),e._transferTabIndex()}),b.on("disable",function(){e.$search.prop("disabled",!0)}),b.on("focus",function(a){e.$search.trigger("focus")}),b.on("results:focus",function(a){e.$search.attr("aria-activedescendant",a.id)}),this.$selection.on("focusin",".select2-search--inline",function(a){e.trigger("focus",a)}),this.$selection.on("focusout",".select2-search--inline",function(a){e._handleBlur(a)}),this.$selection.on("keydown",".select2-search--inline",function(a){a.stopPropagation(),e.trigger("keypress",a),e._keyUpPrevented=a.isDefaultPrevented();var b=a.which;if(b===c.BACKSPACE&&""===e.$search.val()){var d=e.$searchContainer.prev(".select2-selection__choice");if(d.length>0){var f=d.data("data");e.searchRemoveChoice(f),a.preventDefault()}}});var f=document.documentMode,g=f&&11>=f;this.$selection.on("input.searchcheck",".select2-search--inline",function(a){return g?void e.$selection.off("input.search input.searchcheck"):void e.$selection.off("keyup.search")}),this.$selection.on("keyup.search input.search",".select2-search--inline",function(a){if(g&&"input"===a.type)return void e.$selection.off("input.search input.searchcheck");var b=a.which;b!=c.SHIFT&&b!=c.CTRL&&b!=c.ALT&&b!=c.TAB&&e.handleSearch(a)})},d.prototype._transferTabIndex=function(a){this.$search.attr("tabindex",this.$selection.attr("tabindex")),this.$selection.attr("tabindex","-1")},d.prototype.createPlaceholder=function(a,b){this.$search.attr("placeholder",b.text)},d.prototype.update=function(a,b){var c=this.$search[0]==document.activeElement;this.$search.attr("placeholder",""),a.call(this,b),this.$selection.find(".select2-selection__rendered").append(this.$searchContainer),this.resizeSearch(),c&&this.$search.focus()},d.prototype.handleSearch=function(){if(this.resizeSearch(),!this._keyUpPrevented){var a=this.$search.val();this.trigger("query",{term:a})}this._keyUpPrevented=!1},d.prototype.searchRemoveChoice=function(a,b){this.trigger("unselect",{data:b}),this.$search.val(b.text),this.handleSearch()},d.prototype.resizeSearch=function(){this.$search.css("width","25px");var a="";if(""!==this.$search.attr("placeholder"))a=this.$selection.find(".select2-selection__rendered").innerWidth();else{var b=this.$search.val().length+1;a=.75*b+"em"}this.$search.css("width",a)},d}),b.define("select2/selection/eventRelay",["jquery"],function(a){function b(){}return b.prototype.bind=function(b,c,d){var e=this,f=["open","opening","close","closing","select","selecting","unselect","unselecting"],g=["opening","closing","selecting","unselecting"];b.call(this,c,d),c.on("*",function(b,c){if(-1!==a.inArray(b,f)){c=c||{};var d=a.Event("select2:"+b,{params:c});e.$element.trigger(d),-1!==a.inArray(b,g)&&(c.prevented=d.isDefaultPrevented())}})},b}),b.define("select2/translation",["jquery","require"],function(a,b){function c(a){this.dict=a||{}}return c.prototype.all=function(){return this.dict},c.prototype.get=function(a){return this.dict[a]},c.prototype.extend=function(b){this.dict=a.extend({},b.all(),this.dict)},c._cache={},c.loadPath=function(a){if(!(a in c._cache)){var d=b(a);c._cache[a]=d}return new c(c._cache[a])},c}),b.define("select2/diacritics",[],function(){var a={"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S","Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z","Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n","ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ":"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α","Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ω":"ω","ς":"σ"};return a}),b.define("select2/data/base",["../utils"],function(a){function b(a,c){b.__super__.constructor.call(this)}return a.Extend(b,a.Observable),b.prototype.current=function(a){throw new Error("The `current` method must be defined in child classes.")},b.prototype.query=function(a,b){throw new Error("The `query` method must be defined in child classes.")},b.prototype.bind=function(a,b){},b.prototype.destroy=function(){},b.prototype.generateResultId=function(b,c){var d=b.id+"-result-";return d+=a.generateChars(4),d+=null!=c.id?"-"+c.id.toString():"-"+a.generateChars(4)},b}),b.define("select2/data/select",["./base","../utils","jquery"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,a),d.prototype.current=function(a){var b=[],d=this;this.$element.find(":selected").each(function(){var a=c(this),e=d.item(a);b.push(e)}),a(b)},d.prototype.select=function(a){var b=this;if(a.selected=!0,c(a.element).is("option"))return a.element.selected=!0,void this.$element.trigger("change");
if(this.$element.prop("multiple"))this.current(function(d){var e=[];a=[a],a.push.apply(a,d);for(var f=0;f<a.length;f++){var g=a[f].id;-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")});else{var d=a.id;this.$element.val(d),this.$element.trigger("change")}},d.prototype.unselect=function(a){var b=this;if(this.$element.prop("multiple"))return a.selected=!1,c(a.element).is("option")?(a.element.selected=!1,void this.$element.trigger("change")):void this.current(function(d){for(var e=[],f=0;f<d.length;f++){var g=d[f].id;g!==a.id&&-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")})},d.prototype.bind=function(a,b){var c=this;this.container=a,a.on("select",function(a){c.select(a.data)}),a.on("unselect",function(a){c.unselect(a.data)})},d.prototype.destroy=function(){this.$element.find("*").each(function(){c.removeData(this,"data")})},d.prototype.query=function(a,b){var d=[],e=this,f=this.$element.children();f.each(function(){var b=c(this);if(b.is("option")||b.is("optgroup")){var f=e.item(b),g=e.matches(a,f);null!==g&&d.push(g)}}),b({results:d})},d.prototype.addOptions=function(a){b.appendMany(this.$element,a)},d.prototype.option=function(a){var b;a.children?(b=document.createElement("optgroup"),b.label=a.text):(b=document.createElement("option"),void 0!==b.textContent?b.textContent=a.text:b.innerText=a.text),a.id&&(b.value=a.id),a.disabled&&(b.disabled=!0),a.selected&&(b.selected=!0),a.title&&(b.title=a.title);var d=c(b),e=this._normalizeItem(a);return e.element=b,c.data(b,"data",e),d},d.prototype.item=function(a){var b={};if(b=c.data(a[0],"data"),null!=b)return b;if(a.is("option"))b={id:a.val(),text:a.text(),disabled:a.prop("disabled"),selected:a.prop("selected"),title:a.prop("title")};else if(a.is("optgroup")){b={text:a.prop("label"),children:[],title:a.prop("title")};for(var d=a.children("option"),e=[],f=0;f<d.length;f++){var g=c(d[f]),h=this.item(g);e.push(h)}b.children=e}return b=this._normalizeItem(b),b.element=a[0],c.data(a[0],"data",b),b},d.prototype._normalizeItem=function(a){c.isPlainObject(a)||(a={id:a,text:a}),a=c.extend({},{text:""},a);var b={selected:!1,disabled:!1};return null!=a.id&&(a.id=a.id.toString()),null!=a.text&&(a.text=a.text.toString()),null==a._resultId&&a.id&&null!=this.container&&(a._resultId=this.generateResultId(this.container,a)),c.extend({},b,a)},d.prototype.matches=function(a,b){var c=this.options.get("matcher");return c(a,b)},d}),b.define("select2/data/array",["./select","../utils","jquery"],function(a,b,c){function d(a,b){var c=b.get("data")||[];d.__super__.constructor.call(this,a,b),this.addOptions(this.convertToOptions(c))}return b.Extend(d,a),d.prototype.select=function(a){var b=this.$element.find("option").filter(function(b,c){return c.value==a.id.toString()});0===b.length&&(b=this.option(a),this.addOptions(b)),d.__super__.select.call(this,a)},d.prototype.convertToOptions=function(a){function d(a){return function(){return c(this).val()==a.id}}for(var e=this,f=this.$element.find("option"),g=f.map(function(){return e.item(c(this)).id}).get(),h=[],i=0;i<a.length;i++){var j=this._normalizeItem(a[i]);if(c.inArray(j.id,g)>=0){var k=f.filter(d(j)),l=this.item(k),m=c.extend(!0,{},j,l),n=this.option(m);k.replaceWith(n)}else{var o=this.option(j);if(j.children){var p=this.convertToOptions(j.children);b.appendMany(o,p)}h.push(o)}}return h},d}),b.define("select2/data/ajax",["./array","../utils","jquery"],function(a,b,c){function d(a,b){this.ajaxOptions=this._applyDefaults(b.get("ajax")),null!=this.ajaxOptions.processResults&&(this.processResults=this.ajaxOptions.processResults),d.__super__.constructor.call(this,a,b)}return b.Extend(d,a),d.prototype._applyDefaults=function(a){var b={data:function(a){return c.extend({},a,{q:a.term})},transport:function(a,b,d){var e=c.ajax(a);return e.then(b),e.fail(d),e}};return c.extend({},b,a,!0)},d.prototype.processResults=function(a){return a},d.prototype.query=function(a,b){function d(){var d=f.transport(f,function(d){var f=e.processResults(d,a);e.options.get("debug")&&window.console&&console.error&&(f&&f.results&&c.isArray(f.results)||console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")),b(f)},function(){d.status&&"0"===d.status||e.trigger("results:message",{message:"errorLoading"})});e._request=d}var e=this;null!=this._request&&(c.isFunction(this._request.abort)&&this._request.abort(),this._request=null);var f=c.extend({type:"GET"},this.ajaxOptions);"function"==typeof f.url&&(f.url=f.url.call(this.$element,a)),"function"==typeof f.data&&(f.data=f.data.call(this.$element,a)),this.ajaxOptions.delay&&null!=a.term?(this._queryTimeout&&window.clearTimeout(this._queryTimeout),this._queryTimeout=window.setTimeout(d,this.ajaxOptions.delay)):d()},d}),b.define("select2/data/tags",["jquery"],function(a){function b(b,c,d){var e=d.get("tags"),f=d.get("createTag");void 0!==f&&(this.createTag=f);var g=d.get("insertTag");if(void 0!==g&&(this.insertTag=g),b.call(this,c,d),a.isArray(e))for(var h=0;h<e.length;h++){var i=e[h],j=this._normalizeItem(i),k=this.option(j);this.$element.append(k)}}return b.prototype.query=function(a,b,c){function d(a,f){for(var g=a.results,h=0;h<g.length;h++){var i=g[h],j=null!=i.children&&!d({results:i.children},!0),k=i.text===b.term;if(k||j)return f?!1:(a.data=g,void c(a))}if(f)return!0;var l=e.createTag(b);if(null!=l){var m=e.option(l);m.attr("data-select2-tag",!0),e.addOptions([m]),e.insertTag(g,l)}a.results=g,c(a)}var e=this;return this._removeOldTags(),null==b.term||null!=b.page?void a.call(this,b,c):void a.call(this,b,d)},b.prototype.createTag=function(b,c){var d=a.trim(c.term);return""===d?null:{id:d,text:d}},b.prototype.insertTag=function(a,b,c){b.unshift(c)},b.prototype._removeOldTags=function(b){var c=(this._lastTag,this.$element.find("option[data-select2-tag]"));c.each(function(){this.selected||a(this).remove()})},b}),b.define("select2/data/tokenizer",["jquery"],function(a){function b(a,b,c){var d=c.get("tokenizer");void 0!==d&&(this.tokenizer=d),a.call(this,b,c)}return b.prototype.bind=function(a,b,c){a.call(this,b,c),this.$search=b.dropdown.$search||b.selection.$search||c.find(".select2-search__field")},b.prototype.query=function(b,c,d){function e(b){var c=g._normalizeItem(b),d=g.$element.find("option").filter(function(){return a(this).val()===c.id});if(!d.length){var e=g.option(c);e.attr("data-select2-tag",!0),g._removeOldTags(),g.addOptions([e])}f(c)}function f(a){g.trigger("select",{data:a})}var g=this;c.term=c.term||"";var h=this.tokenizer(c,this.options,e);h.term!==c.term&&(this.$search.length&&(this.$search.val(h.term),this.$search.focus()),c.term=h.term),b.call(this,c,d)},b.prototype.tokenizer=function(b,c,d,e){for(var f=d.get("tokenSeparators")||[],g=c.term,h=0,i=this.createTag||function(a){return{id:a.term,text:a.term}};h<g.length;){var j=g[h];if(-1!==a.inArray(j,f)){var k=g.substr(0,h),l=a.extend({},c,{term:k}),m=i(l);null!=m?(e(m),g=g.substr(h+1)||"",h=0):h++}else h++}return{term:g}},b}),b.define("select2/data/minimumInputLength",[],function(){function a(a,b,c){this.minimumInputLength=c.get("minimumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",b.term.length<this.minimumInputLength?void this.trigger("results:message",{message:"inputTooShort",args:{minimum:this.minimumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),b.define("select2/data/maximumInputLength",[],function(){function a(a,b,c){this.maximumInputLength=c.get("maximumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",this.maximumInputLength>0&&b.term.length>this.maximumInputLength?void this.trigger("results:message",{message:"inputTooLong",args:{maximum:this.maximumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),b.define("select2/data/maximumSelectionLength",[],function(){function a(a,b,c){this.maximumSelectionLength=c.get("maximumSelectionLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){var d=this;this.current(function(e){var f=null!=e?e.length:0;return d.maximumSelectionLength>0&&f>=d.maximumSelectionLength?void d.trigger("results:message",{message:"maximumSelected",args:{maximum:d.maximumSelectionLength}}):void a.call(d,b,c)})},a}),b.define("select2/dropdown",["jquery","./utils"],function(a,b){function c(a,b){this.$element=a,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<span class="select2-dropdown"><span class="select2-results"></span></span>');return b.attr("dir",this.options.get("dir")),this.$dropdown=b,b},c.prototype.bind=function(){},c.prototype.position=function(a,b){},c.prototype.destroy=function(){this.$dropdown.remove()},c}),b.define("select2/dropdown/search",["jquery","../utils"],function(a,b){function c(){}return c.prototype.render=function(b){var c=b.call(this),d=a('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></span>');return this.$searchContainer=d,this.$search=d.find("input"),c.prepend(d),c},c.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),this.$search.on("keydown",function(a){e.trigger("keypress",a),e._keyUpPrevented=a.isDefaultPrevented()}),this.$search.on("input",function(b){a(this).off("keyup")}),this.$search.on("keyup input",function(a){e.handleSearch(a)}),c.on("open",function(){e.$search.attr("tabindex",0),e.$search.focus(),window.setTimeout(function(){e.$search.focus()},0)}),c.on("close",function(){e.$search.attr("tabindex",-1),e.$search.val("")}),c.on("focus",function(){c.isOpen()&&e.$search.focus()}),c.on("results:all",function(a){if(null==a.query.term||""===a.query.term){var b=e.showSearch(a);b?e.$searchContainer.removeClass("select2-search--hide"):e.$searchContainer.addClass("select2-search--hide")}})},c.prototype.handleSearch=function(a){if(!this._keyUpPrevented){var b=this.$search.val();this.trigger("query",{term:b})}this._keyUpPrevented=!1},c.prototype.showSearch=function(a,b){return!0},c}),b.define("select2/dropdown/hidePlaceholder",[],function(){function a(a,b,c,d){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c,d)}return a.prototype.append=function(a,b){b.results=this.removePlaceholder(b.results),a.call(this,b)},a.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},a.prototype.removePlaceholder=function(a,b){for(var c=b.slice(0),d=b.length-1;d>=0;d--){var e=b[d];this.placeholder.id===e.id&&c.splice(d,1)}return c},a}),b.define("select2/dropdown/infiniteScroll",["jquery"],function(a){function b(a,b,c,d){this.lastParams={},a.call(this,b,c,d),this.$loadingMore=this.createLoadingMore(),this.loading=!1}return b.prototype.append=function(a,b){this.$loadingMore.remove(),this.loading=!1,a.call(this,b),this.showLoadingMore(b)&&this.$results.append(this.$loadingMore)},b.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),c.on("query",function(a){e.lastParams=a,e.loading=!0}),c.on("query:append",function(a){e.lastParams=a,e.loading=!0}),this.$results.on("scroll",function(){var b=a.contains(document.documentElement,e.$loadingMore[0]);if(!e.loading&&b){var c=e.$results.offset().top+e.$results.outerHeight(!1),d=e.$loadingMore.offset().top+e.$loadingMore.outerHeight(!1);c+50>=d&&e.loadMore()}})},b.prototype.loadMore=function(){this.loading=!0;var b=a.extend({},{page:1},this.lastParams);b.page++,this.trigger("query:append",b)},b.prototype.showLoadingMore=function(a,b){return b.pagination&&b.pagination.more},b.prototype.createLoadingMore=function(){var b=a('<li class="select2-results__option select2-results__option--load-more"role="treeitem" aria-disabled="true"></li>'),c=this.options.get("translations").get("loadingMore");return b.html(c(this.lastParams)),b},b}),b.define("select2/dropdown/attachBody",["jquery","../utils"],function(a,b){function c(b,c,d){this.$dropdownParent=d.get("dropdownParent")||a(document.body),b.call(this,c,d)}return c.prototype.bind=function(a,b,c){var d=this,e=!1;a.call(this,b,c),b.on("open",function(){d._showDropdown(),d._attachPositioningHandler(b),e||(e=!0,b.on("results:all",function(){d._positionDropdown(),d._resizeDropdown()}),b.on("results:append",function(){d._positionDropdown(),d._resizeDropdown()}))}),b.on("close",function(){d._hideDropdown(),d._detachPositioningHandler(b)}),this.$dropdownContainer.on("mousedown",function(a){a.stopPropagation()})},c.prototype.destroy=function(a){a.call(this),this.$dropdownContainer.remove()},c.prototype.position=function(a,b,c){b.attr("class",c.attr("class")),b.removeClass("select2"),b.addClass("select2-container--open"),b.css({position:"absolute",top:-999999}),this.$container=c},c.prototype.render=function(b){var c=a("<span></span>"),d=b.call(this);return c.append(d),this.$dropdownContainer=c,c},c.prototype._hideDropdown=function(a){this.$dropdownContainer.detach()},c.prototype._attachPositioningHandler=function(c,d){var e=this,f="scroll.select2."+d.id,g="resize.select2."+d.id,h="orientationchange.select2."+d.id,i=this.$container.parents().filter(b.hasScroll);i.each(function(){a(this).data("select2-scroll-position",{x:a(this).scrollLeft(),y:a(this).scrollTop()})}),i.on(f,function(b){var c=a(this).data("select2-scroll-position");a(this).scrollTop(c.y)}),a(window).on(f+" "+g+" "+h,function(a){e._positionDropdown(),e._resizeDropdown()})},c.prototype._detachPositioningHandler=function(c,d){var e="scroll.select2."+d.id,f="resize.select2."+d.id,g="orientationchange.select2."+d.id,h=this.$container.parents().filter(b.hasScroll);h.off(e),a(window).off(e+" "+f+" "+g)},c.prototype._positionDropdown=function(){var b=a(window),c=this.$dropdown.hasClass("select2-dropdown--above"),d=this.$dropdown.hasClass("select2-dropdown--below"),e=null,f=this.$container.offset();f.bottom=f.top+this.$container.outerHeight(!1);var g={height:this.$container.outerHeight(!1)};g.top=f.top,g.bottom=f.top+g.height;var h={height:this.$dropdown.outerHeight(!1)},i={top:b.scrollTop(),bottom:b.scrollTop()+b.height()},j=i.top<f.top-h.height,k=i.bottom>f.bottom+h.height,l={left:f.left,top:g.bottom},m=this.$dropdownParent;"static"===m.css("position")&&(m=m.offsetParent());var n=m.offset();l.top-=n.top,l.left-=n.left,c||d||(e="below"),k||!j||c?!j&&k&&c&&(e="below"):e="above",("above"==e||c&&"below"!==e)&&(l.top=g.top-n.top-h.height),null!=e&&(this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--"+e),this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--"+e)),this.$dropdownContainer.css(l)},c.prototype._resizeDropdown=function(){var a={width:this.$container.outerWidth(!1)+"px"};this.options.get("dropdownAutoWidth")&&(a.minWidth=a.width,a.position="relative",a.width="auto"),this.$dropdown.css(a)},c.prototype._showDropdown=function(a){this.$dropdownContainer.appendTo(this.$dropdownParent),this._positionDropdown(),this._resizeDropdown()},c}),b.define("select2/dropdown/minimumResultsForSearch",[],function(){function a(b){for(var c=0,d=0;d<b.length;d++){var e=b[d];e.children?c+=a(e.children):c++}return c}function b(a,b,c,d){this.minimumResultsForSearch=c.get("minimumResultsForSearch"),this.minimumResultsForSearch<0&&(this.minimumResultsForSearch=1/0),a.call(this,b,c,d)}return b.prototype.showSearch=function(b,c){return a(c.data.results)<this.minimumResultsForSearch?!1:b.call(this,c)},b}),b.define("select2/dropdown/selectOnClose",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("close",function(a){d._handleSelectOnClose(a)})},a.prototype._handleSelectOnClose=function(a,b){if(b&&null!=b.originalSelect2Event){var c=b.originalSelect2Event;if("select"===c._type||"unselect"===c._type)return}var d=this.getHighlightedResults();if(!(d.length<1)){var e=d.data("data");null!=e.element&&e.element.selected||null==e.element&&e.selected||this.trigger("select",{data:e})}},a}),b.define("select2/dropdown/closeOnSelect",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("select",function(a){d._selectTriggered(a)}),b.on("unselect",function(a){d._selectTriggered(a)})},a.prototype._selectTriggered=function(a,b){var c=b.originalEvent;c&&c.ctrlKey||this.trigger("close",{originalEvent:c,originalSelect2Event:b})},a}),b.define("select2/i18n/en",[],function(){return{errorLoading:function(){return"The results could not be loaded."},inputTooLong:function(a){var b=a.input.length-a.maximum,c="Please delete "+b+" character";return 1!=b&&(c+="s"),c},inputTooShort:function(a){var b=a.minimum-a.input.length,c="Please enter "+b+" or more characters";return c},loadingMore:function(){return"Loading more results…"},maximumSelected:function(a){var b="You can only select "+a.maximum+" item";return 1!=a.maximum&&(b+="s"),b},noResults:function(){return"No results found"},searching:function(){return"Searching…"}}}),b.define("select2/defaults",["jquery","require","./results","./selection/single","./selection/multiple","./selection/placeholder","./selection/allowClear","./selection/search","./selection/eventRelay","./utils","./translation","./diacritics","./data/select","./data/array","./data/ajax","./data/tags","./data/tokenizer","./data/minimumInputLength","./data/maximumInputLength","./data/maximumSelectionLength","./dropdown","./dropdown/search","./dropdown/hidePlaceholder","./dropdown/infiniteScroll","./dropdown/attachBody","./dropdown/minimumResultsForSearch","./dropdown/selectOnClose","./dropdown/closeOnSelect","./i18n/en"],function(a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C){function D(){this.reset()}D.prototype.apply=function(l){if(l=a.extend(!0,{},this.defaults,l),null==l.dataAdapter){if(null!=l.ajax?l.dataAdapter=o:null!=l.data?l.dataAdapter=n:l.dataAdapter=m,l.minimumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,r)),l.maximumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,s)),l.maximumSelectionLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,t)),l.tags&&(l.dataAdapter=j.Decorate(l.dataAdapter,p)),(null!=l.tokenSeparators||null!=l.tokenizer)&&(l.dataAdapter=j.Decorate(l.dataAdapter,q)),null!=l.query){var C=b(l.amdBase+"compat/query");l.dataAdapter=j.Decorate(l.dataAdapter,C)}if(null!=l.initSelection){var D=b(l.amdBase+"compat/initSelection");l.dataAdapter=j.Decorate(l.dataAdapter,D)}}if(null==l.resultsAdapter&&(l.resultsAdapter=c,null!=l.ajax&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,x)),null!=l.placeholder&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,w)),l.selectOnClose&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,A))),null==l.dropdownAdapter){if(l.multiple)l.dropdownAdapter=u;else{var E=j.Decorate(u,v);l.dropdownAdapter=E}if(0!==l.minimumResultsForSearch&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,z)),l.closeOnSelect&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,B)),null!=l.dropdownCssClass||null!=l.dropdownCss||null!=l.adaptDropdownCssClass){var F=b(l.amdBase+"compat/dropdownCss");l.dropdownAdapter=j.Decorate(l.dropdownAdapter,F)}l.dropdownAdapter=j.Decorate(l.dropdownAdapter,y)}if(null==l.selectionAdapter){if(l.multiple?l.selectionAdapter=e:l.selectionAdapter=d,null!=l.placeholder&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,f)),l.allowClear&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,g)),l.multiple&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,h)),null!=l.containerCssClass||null!=l.containerCss||null!=l.adaptContainerCssClass){var G=b(l.amdBase+"compat/containerCss");l.selectionAdapter=j.Decorate(l.selectionAdapter,G)}l.selectionAdapter=j.Decorate(l.selectionAdapter,i)}if("string"==typeof l.language)if(l.language.indexOf("-")>0){var H=l.language.split("-"),I=H[0];l.language=[l.language,I]}else l.language=[l.language];if(a.isArray(l.language)){var J=new k;l.language.push("en");for(var K=l.language,L=0;L<K.length;L++){var M=K[L],N={};try{N=k.loadPath(M)}catch(O){try{M=this.defaults.amdLanguageBase+M,N=k.loadPath(M)}catch(P){l.debug&&window.console&&console.warn&&console.warn('Select2: The language file for "'+M+'" could not be automatically loaded. A fallback will be used instead.');continue}}J.extend(N)}l.translations=J}else{var Q=k.loadPath(this.defaults.amdLanguageBase+"en"),R=new k(l.language);R.extend(Q),l.translations=R}return l},D.prototype.reset=function(){function b(a){function b(a){return l[a]||a}return a.replace(/[^\u0000-\u007E]/g,b)}function c(d,e){if(""===a.trim(d.term))return e;if(e.children&&e.children.length>0){for(var f=a.extend(!0,{},e),g=e.children.length-1;g>=0;g--){var h=e.children[g],i=c(d,h);null==i&&f.children.splice(g,1)}return f.children.length>0?f:c(d,f)}var j=b(e.text).toUpperCase(),k=b(d.term).toUpperCase();return j.indexOf(k)>-1?e:null}this.defaults={amdBase:"./",amdLanguageBase:"./i18n/",closeOnSelect:!0,debug:!1,dropdownAutoWidth:!1,escapeMarkup:j.escapeMarkup,language:C,matcher:c,minimumInputLength:0,maximumInputLength:0,maximumSelectionLength:0,minimumResultsForSearch:0,selectOnClose:!1,sorter:function(a){return a},templateResult:function(a){return a.text},templateSelection:function(a){return a.text},theme:"default",width:"resolve"}},D.prototype.set=function(b,c){var d=a.camelCase(b),e={};e[d]=c;var f=j._convertData(e);a.extend(this.defaults,f)};var E=new D;return E}),b.define("select2/options",["require","jquery","./defaults","./utils"],function(a,b,c,d){function e(b,e){if(this.options=b,null!=e&&this.fromElement(e),this.options=c.apply(this.options),e&&e.is("input")){var f=a(this.get("amdBase")+"compat/inputData");this.options.dataAdapter=d.Decorate(this.options.dataAdapter,f)}}return e.prototype.fromElement=function(a){var c=["select2"];null==this.options.multiple&&(this.options.multiple=a.prop("multiple")),null==this.options.disabled&&(this.options.disabled=a.prop("disabled")),null==this.options.language&&(a.prop("lang")?this.options.language=a.prop("lang").toLowerCase():a.closest("[lang]").prop("lang")&&(this.options.language=a.closest("[lang]").prop("lang"))),null==this.options.dir&&(a.prop("dir")?this.options.dir=a.prop("dir"):a.closest("[dir]").prop("dir")?this.options.dir=a.closest("[dir]").prop("dir"):this.options.dir="ltr"),a.prop("disabled",this.options.disabled),a.prop("multiple",this.options.multiple),a.data("select2Tags")&&(this.options.debug&&window.console&&console.warn&&console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.'),a.data("data",a.data("select2Tags")),a.data("tags",!0)),a.data("ajaxUrl")&&(this.options.debug&&window.console&&console.warn&&console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2."),a.attr("ajax--url",a.data("ajaxUrl")),a.data("ajax--url",a.data("ajaxUrl")));var e={};e=b.fn.jquery&&"1."==b.fn.jquery.substr(0,2)&&a[0].dataset?b.extend(!0,{},a[0].dataset,a.data()):a.data();var f=b.extend(!0,{},e);f=d._convertData(f);for(var g in f)b.inArray(g,c)>-1||(b.isPlainObject(this.options[g])?b.extend(this.options[g],f[g]):this.options[g]=f[g]);return this},e.prototype.get=function(a){return this.options[a]},e.prototype.set=function(a,b){this.options[a]=b},e}),b.define("select2/core",["jquery","./options","./utils","./keys"],function(a,b,c,d){var e=function(a,c){null!=a.data("select2")&&a.data("select2").destroy(),this.$element=a,this.id=this._generateId(a),c=c||{},this.options=new b(c,a),e.__super__.constructor.call(this);var d=a.attr("tabindex")||0;a.data("old-tabindex",d),a.attr("tabindex","-1");var f=this.options.get("dataAdapter");this.dataAdapter=new f(a,this.options);var g=this.render();this._placeContainer(g);var h=this.options.get("selectionAdapter");this.selection=new h(a,this.options),this.$selection=this.selection.render(),this.selection.position(this.$selection,g);var i=this.options.get("dropdownAdapter");this.dropdown=new i(a,this.options),this.$dropdown=this.dropdown.render(),this.dropdown.position(this.$dropdown,g);var j=this.options.get("resultsAdapter");this.results=new j(a,this.options,this.dataAdapter),this.$results=this.results.render(),this.results.position(this.$results,this.$dropdown);var k=this;this._bindAdapters(),this._registerDomEvents(),this._registerDataEvents(),this._registerSelectionEvents(),this._registerDropdownEvents(),this._registerResultsEvents(),this._registerEvents(),this.dataAdapter.current(function(a){k.trigger("selection:update",{data:a})}),a.addClass("select2-hidden-accessible"),a.attr("aria-hidden","true"),this._syncAttributes(),a.data("select2",this)};return c.Extend(e,c.Observable),e.prototype._generateId=function(a){var b="";return b=null!=a.attr("id")?a.attr("id"):null!=a.attr("name")?a.attr("name")+"-"+c.generateChars(2):c.generateChars(4),b=b.replace(/(:|\.|\[|\]|,)/g,""),b="select2-"+b},e.prototype._placeContainer=function(a){a.insertAfter(this.$element);var b=this._resolveWidth(this.$element,this.options.get("width"));null!=b&&a.css("width",b)},e.prototype._resolveWidth=function(a,b){var c=/^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;if("resolve"==b){var d=this._resolveWidth(a,"style");return null!=d?d:this._resolveWidth(a,"element")}if("element"==b){var e=a.outerWidth(!1);return 0>=e?"auto":e+"px"}if("style"==b){var f=a.attr("style");if("string"!=typeof f)return null;for(var g=f.split(";"),h=0,i=g.length;i>h;h+=1){var j=g[h].replace(/\s/g,""),k=j.match(c);if(null!==k&&k.length>=1)return k[1]}return null}return b},e.prototype._bindAdapters=function(){this.dataAdapter.bind(this,this.$container),this.selection.bind(this,this.$container),this.dropdown.bind(this,this.$container),this.results.bind(this,this.$container)},e.prototype._registerDomEvents=function(){var b=this;this.$element.on("change.select2",function(){b.dataAdapter.current(function(a){b.trigger("selection:update",{data:a})})}),this.$element.on("focus.select2",function(a){b.trigger("focus",a)}),this._syncA=c.bind(this._syncAttributes,this),this._syncS=c.bind(this._syncSubtree,this),this.$element[0].attachEvent&&this.$element[0].attachEvent("onpropertychange",this._syncA);var d=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;null!=d?(this._observer=new d(function(c){a.each(c,b._syncA),a.each(c,b._syncS)}),this._observer.observe(this.$element[0],{attributes:!0,childList:!0,subtree:!1})):this.$element[0].addEventListener&&(this.$element[0].addEventListener("DOMAttrModified",b._syncA,!1),this.$element[0].addEventListener("DOMNodeInserted",b._syncS,!1),this.$element[0].addEventListener("DOMNodeRemoved",b._syncS,!1))},e.prototype._registerDataEvents=function(){var a=this;this.dataAdapter.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerSelectionEvents=function(){var b=this,c=["toggle","focus"];this.selection.on("toggle",function(){b.toggleDropdown()}),this.selection.on("focus",function(a){b.focus(a)}),this.selection.on("*",function(d,e){-1===a.inArray(d,c)&&b.trigger(d,e)})},e.prototype._registerDropdownEvents=function(){var a=this;this.dropdown.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerResultsEvents=function(){var a=this;this.results.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerEvents=function(){var a=this;this.on("open",function(){a.$container.addClass("select2-container--open")}),this.on("close",function(){a.$container.removeClass("select2-container--open")}),this.on("enable",function(){a.$container.removeClass("select2-container--disabled")}),this.on("disable",function(){a.$container.addClass("select2-container--disabled")}),this.on("blur",function(){a.$container.removeClass("select2-container--focus")}),this.on("query",function(b){a.isOpen()||a.trigger("open",{}),this.dataAdapter.query(b,function(c){a.trigger("results:all",{data:c,query:b})})}),this.on("query:append",function(b){this.dataAdapter.query(b,function(c){a.trigger("results:append",{data:c,query:b})})}),this.on("keypress",function(b){var c=b.which;a.isOpen()?c===d.ESC||c===d.TAB||c===d.UP&&b.altKey?(a.close(),b.preventDefault()):c===d.ENTER?(a.trigger("results:select",{}),b.preventDefault()):c===d.SPACE&&b.ctrlKey?(a.trigger("results:toggle",{}),b.preventDefault()):c===d.UP?(a.trigger("results:previous",{}),b.preventDefault()):c===d.DOWN&&(a.trigger("results:next",{}),b.preventDefault()):(c===d.ENTER||c===d.SPACE||c===d.DOWN&&b.altKey)&&(a.open(),b.preventDefault())})},e.prototype._syncAttributes=function(){this.options.set("disabled",this.$element.prop("disabled")),this.options.get("disabled")?(this.isOpen()&&this.close(),this.trigger("disable",{})):this.trigger("enable",{})},e.prototype._syncSubtree=function(a,b){var c=!1,d=this;if(!a||!a.target||"OPTION"===a.target.nodeName||"OPTGROUP"===a.target.nodeName){if(b)if(b.addedNodes&&b.addedNodes.length>0)for(var e=0;e<b.addedNodes.length;e++){var f=b.addedNodes[e];f.selected&&(c=!0)}else b.removedNodes&&b.removedNodes.length>0&&(c=!0);else c=!0;c&&this.dataAdapter.current(function(a){d.trigger("selection:update",{data:a})})}},e.prototype.trigger=function(a,b){var c=e.__super__.trigger,d={open:"opening",close:"closing",select:"selecting",unselect:"unselecting"};if(void 0===b&&(b={}),a in d){var f=d[a],g={prevented:!1,name:a,args:b};if(c.call(this,f,g),g.prevented)return void(b.prevented=!0)}c.call(this,a,b)},e.prototype.toggleDropdown=function(){this.options.get("disabled")||(this.isOpen()?this.close():this.open())},e.prototype.open=function(){this.isOpen()||this.trigger("query",{})},e.prototype.close=function(){this.isOpen()&&this.trigger("close",{})},e.prototype.isOpen=function(){return this.$container.hasClass("select2-container--open")},e.prototype.hasFocus=function(){return this.$container.hasClass("select2-container--focus")},e.prototype.focus=function(a){this.hasFocus()||(this.$container.addClass("select2-container--focus"),this.trigger("focus",{}))},e.prototype.enable=function(a){this.options.get("debug")&&window.console&&console.warn&&console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.'),(null==a||0===a.length)&&(a=[!0]);var b=!a[0];this.$element.prop("disabled",b)},e.prototype.data=function(){this.options.get("debug")&&arguments.length>0&&window.console&&console.warn&&console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.');var a=[];return this.dataAdapter.current(function(b){a=b}),a},e.prototype.val=function(b){if(this.options.get("debug")&&window.console&&console.warn&&console.warn('Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.'),null==b||0===b.length)return this.$element.val();var c=b[0];a.isArray(c)&&(c=a.map(c,function(a){return a.toString()})),this.$element.val(c).trigger("change")},e.prototype.destroy=function(){this.$container.remove(),this.$element[0].detachEvent&&this.$element[0].detachEvent("onpropertychange",this._syncA),null!=this._observer?(this._observer.disconnect(),this._observer=null):this.$element[0].removeEventListener&&(this.$element[0].removeEventListener("DOMAttrModified",this._syncA,!1),this.$element[0].removeEventListener("DOMNodeInserted",this._syncS,!1),this.$element[0].removeEventListener("DOMNodeRemoved",this._syncS,!1)),this._syncA=null,this._syncS=null,this.$element.off(".select2"),this.$element.attr("tabindex",this.$element.data("old-tabindex")),this.$element.removeClass("select2-hidden-accessible"),this.$element.attr("aria-hidden","false"),this.$element.removeData("select2"),this.dataAdapter.destroy(),this.selection.destroy(),this.dropdown.destroy(),this.results.destroy(),this.dataAdapter=null,this.selection=null,this.dropdown=null,this.results=null;
},e.prototype.render=function(){var b=a('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');return b.attr("dir",this.options.get("dir")),this.$container=b,this.$container.addClass("select2-container--"+this.options.get("theme")),b.data("element",this.$element),b},e}),b.define("jquery-mousewheel",["jquery"],function(a){return a}),b.define("jquery.select2",["jquery","jquery-mousewheel","./select2/core","./select2/defaults"],function(a,b,c,d){if(null==a.fn.select2){var e=["open","close","destroy"];a.fn.select2=function(b){if(b=b||{},"object"==typeof b)return this.each(function(){var d=a.extend(!0,{},b);new c(a(this),d)}),this;if("string"==typeof b){var d,f=Array.prototype.slice.call(arguments,1);return this.each(function(){var c=a(this).data("select2");null==c&&window.console&&console.error&&console.error("The select2('"+b+"') method was called on an element that is not using Select2."),d=c[b].apply(c,f)}),a.inArray(b,e)>-1?this:d}throw new Error("Invalid arguments for Select2: "+b)}}return null==a.fn.select2.defaults&&(a.fn.select2.defaults=d),c}),{define:b.define,require:b.require}}(),c=b.require("jquery.select2");return a.fn.select2.amd=b,c});/*! rangeslider.js - v2.3.0 | (c) 2016 @andreruffert | MIT license | https://github.com/andreruffert/rangeslider.js */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){"use strict";function b(){var a=document.createElement("input");return a.setAttribute("type","range"),"text"!==a.type}function c(a,b){var c=Array.prototype.slice.call(arguments,2);return setTimeout(function(){return a.apply(null,c)},b)}function d(a,b){return b=b||100,function(){if(!a.debouncing){var c=Array.prototype.slice.apply(arguments);a.lastReturnVal=a.apply(window,c),a.debouncing=!0}return clearTimeout(a.debounceTimeout),a.debounceTimeout=setTimeout(function(){a.debouncing=!1},b),a.lastReturnVal}}function e(a){return a&&(0===a.offsetWidth||0===a.offsetHeight||a.open===!1)}function f(a){for(var b=[],c=a.parentNode;e(c);)b.push(c),c=c.parentNode;return b}function g(a,b){function c(a){"undefined"!=typeof a.open&&(a.open=!a.open)}var d=f(a),e=d.length,g=[],h=a[b];if(e){for(var i=0;i<e;i++)g[i]=d[i].style.cssText,d[i].style.setProperty?d[i].style.setProperty("display","block","important"):d[i].style.cssText+=";display: block !important",d[i].style.height="0",d[i].style.overflow="hidden",d[i].style.visibility="hidden",c(d[i]);h=a[b];for(var j=0;j<e;j++)d[j].style.cssText=g[j],c(d[j])}return h}function h(a,b){var c=parseFloat(a);return Number.isNaN(c)?b:c}function i(a){return a.charAt(0).toUpperCase()+a.substr(1)}function j(b,e){if(this.$window=a(window),this.$document=a(document),this.$element=a(b),this.options=a.extend({},n,e),this.polyfill=this.options.polyfill,this.orientation=this.$element[0].getAttribute("data-orientation")||this.options.orientation,this.onInit=this.options.onInit,this.onSlide=this.options.onSlide,this.onSlideEnd=this.options.onSlideEnd,this.DIMENSION=o.orientation[this.orientation].dimension,this.DIRECTION=o.orientation[this.orientation].direction,this.DIRECTION_STYLE=o.orientation[this.orientation].directionStyle,this.COORDINATE=o.orientation[this.orientation].coordinate,this.polyfill&&m)return!1;this.identifier="js-"+k+"-"+l++,this.startEvent=this.options.startEvent.join("."+this.identifier+" ")+"."+this.identifier,this.moveEvent=this.options.moveEvent.join("."+this.identifier+" ")+"."+this.identifier,this.endEvent=this.options.endEvent.join("."+this.identifier+" ")+"."+this.identifier,this.toFixed=(this.step+"").replace(".","").length-1,this.$fill=a('<div class="'+this.options.fillClass+'" />'),this.$handle=a('<div class="'+this.options.handleClass+'" />'),this.$range=a('<div class="'+this.options.rangeClass+" "+this.options[this.orientation+"Class"]+'" id="'+this.identifier+'" />').insertAfter(this.$element).prepend(this.$fill,this.$handle),this.$element.css({position:"absolute",width:"1px",height:"1px",overflow:"hidden",opacity:"0"}),this.handleDown=a.proxy(this.handleDown,this),this.handleMove=a.proxy(this.handleMove,this),this.handleEnd=a.proxy(this.handleEnd,this),this.init();var f=this;this.$window.on("resize."+this.identifier,d(function(){c(function(){f.update(!1,!1)},300)},20)),this.$document.on(this.startEvent,"#"+this.identifier+":not(."+this.options.disabledClass+")",this.handleDown),this.$element.on("change."+this.identifier,function(a,b){if(!b||b.origin!==f.identifier){var c=a.target.value,d=f.getPositionFromValue(c);f.setPosition(d)}})}Number.isNaN=Number.isNaN||function(a){return"number"==typeof a&&a!==a};var k="rangeslider",l=0,m=b(),n={polyfill:!0,orientation:"horizontal",rangeClass:"rangeslider",disabledClass:"rangeslider--disabled",activeClass:"rangeslider--active",horizontalClass:"rangeslider--horizontal",verticalClass:"rangeslider--vertical",fillClass:"rangeslider__fill",handleClass:"rangeslider__handle",startEvent:["mousedown","touchstart","pointerdown"],moveEvent:["mousemove","touchmove","pointermove"],endEvent:["mouseup","touchend","pointerup"]},o={orientation:{horizontal:{dimension:"width",direction:"left",directionStyle:"left",coordinate:"x"},vertical:{dimension:"height",direction:"top",directionStyle:"bottom",coordinate:"y"}}};return j.prototype.init=function(){this.update(!0,!1),this.onInit&&"function"==typeof this.onInit&&this.onInit()},j.prototype.update=function(a,b){a=a||!1,a&&(this.min=h(this.$element[0].getAttribute("min"),0),this.max=h(this.$element[0].getAttribute("max"),100),this.value=h(this.$element[0].value,Math.round(this.min+(this.max-this.min)/2)),this.step=h(this.$element[0].getAttribute("step"),1)),this.handleDimension=g(this.$handle[0],"offset"+i(this.DIMENSION)),this.rangeDimension=g(this.$range[0],"offset"+i(this.DIMENSION)),this.maxHandlePos=this.rangeDimension-this.handleDimension,this.grabPos=this.handleDimension/2,this.position=this.getPositionFromValue(this.value),this.$element[0].disabled?this.$range.addClass(this.options.disabledClass):this.$range.removeClass(this.options.disabledClass),this.setPosition(this.position,b)},j.prototype.handleDown=function(a){if(a.preventDefault(),this.$document.on(this.moveEvent,this.handleMove),this.$document.on(this.endEvent,this.handleEnd),this.$range.addClass(this.options.activeClass),!((" "+a.target.className+" ").replace(/[\n\t]/g," ").indexOf(this.options.handleClass)>-1)){var b=this.getRelativePosition(a),c=this.$range[0].getBoundingClientRect()[this.DIRECTION],d=this.getPositionFromNode(this.$handle[0])-c,e="vertical"===this.orientation?this.maxHandlePos-(b-this.grabPos):b-this.grabPos;this.setPosition(e),b>=d&&b<d+this.handleDimension&&(this.grabPos=b-d)}},j.prototype.handleMove=function(a){a.preventDefault();var b=this.getRelativePosition(a),c="vertical"===this.orientation?this.maxHandlePos-(b-this.grabPos):b-this.grabPos;this.setPosition(c)},j.prototype.handleEnd=function(a){a.preventDefault(),this.$document.off(this.moveEvent,this.handleMove),this.$document.off(this.endEvent,this.handleEnd),this.$range.removeClass(this.options.activeClass),this.$element.trigger("change",{origin:this.identifier}),this.onSlideEnd&&"function"==typeof this.onSlideEnd&&this.onSlideEnd(this.position,this.value)},j.prototype.cap=function(a,b,c){return a<b?b:a>c?c:a},j.prototype.setPosition=function(a,b){var c,d;void 0===b&&(b=!0),c=this.getValueFromPosition(this.cap(a,0,this.maxHandlePos)),d=this.getPositionFromValue(c),this.$fill[0].style[this.DIMENSION]=d+this.grabPos+"px",this.$handle[0].style[this.DIRECTION_STYLE]=d+"px",this.setValue(c),this.position=d,this.value=c,b&&this.onSlide&&"function"==typeof this.onSlide&&this.onSlide(d,c)},j.prototype.getPositionFromNode=function(a){for(var b=0;null!==a;)b+=a.offsetLeft,a=a.offsetParent;return b},j.prototype.getRelativePosition=function(a){var b=i(this.COORDINATE),c=this.$range[0].getBoundingClientRect()[this.DIRECTION],d=0;return"undefined"!=typeof a.originalEvent["client"+b]?d=a.originalEvent["client"+b]:a.originalEvent.touches&&a.originalEvent.touches[0]&&"undefined"!=typeof a.originalEvent.touches[0]["client"+b]?d=a.originalEvent.touches[0]["client"+b]:a.currentPoint&&"undefined"!=typeof a.currentPoint[this.COORDINATE]&&(d=a.currentPoint[this.COORDINATE]),d-c},j.prototype.getPositionFromValue=function(a){var b,c;return b=(a-this.min)/(this.max-this.min),c=Number.isNaN(b)?0:b*this.maxHandlePos},j.prototype.getValueFromPosition=function(a){var b,c;return b=a/(this.maxHandlePos||1),c=this.step*Math.round(b*(this.max-this.min)/this.step)+this.min,Number(c.toFixed(this.toFixed))},j.prototype.setValue=function(a){a===this.value&&""!==this.$element[0].value||this.$element.val(a).trigger("input",{origin:this.identifier})},j.prototype.destroy=function(){this.$document.off("."+this.identifier),this.$window.off("."+this.identifier),this.$element.off("."+this.identifier).removeAttr("style").removeData("plugin_"+k),this.$range&&this.$range.length&&this.$range[0].parentNode.removeChild(this.$range[0])},a.fn[k]=function(b){var c=Array.prototype.slice.call(arguments,1);return this.each(function(){var d=a(this),e=d.data("plugin_"+k);e||d.data("plugin_"+k,e=new j(this,b)),"string"==typeof b&&e[b].apply(e,c)})},"rangeslider.js is available in jQuery context e.g $(selector).rangeslider(options);"});var czr_debug = {
      log: function(o) {debug.queue.push(['log', arguments, debug.stack.slice(0)]); if (window.console && typeof window.console.log == 'function') {window.console.log(o);}},
      error: function(o) {debug.queue.push(['error', arguments, debug.stack.slice(0)]); if (window.console && typeof window.console.error == 'function') {window.console.error(o);}},
      queue: [],
      stack: []
};

var api = api || wp.customize, $ = $ || jQuery;
(function (api, $, _) {
      api.consoleLog = function( isError ) {
            if ( ( _.isUndefined( console ) && typeof window.console.log != 'function' ) )
              return;

            var _toArr = Array.from( arguments );
            _toArr = _toArr.join(' ');
            var _styled = [
                '%c ' + _toArr,
                'background: #008ec2; color: white; display: block;',
            ];
            console.log.apply( console, _styled );
      };

      api.czr_isSkopOn = function() {
            return serverControlParams.isSkopOn && _.has( api, 'czr_skopeBase' );
      };

      api.czr_isChangeSetOn = function() {
            return serverControlParams.isChangeSetOn && true === true;//&& true === true is just there to hackily cast the returned value as boolean.
      };
      api.czr_activeSectionId = new api.Value('');
      api.czr_activePanelId = new api.Value('');
      if ( 'function' === typeof api.Section ) {
            api.control.bind( 'add', function( _ctrl ) {
                  if ( _ctrl.params.ubq_section && _ctrl.params.ubq_section.section ) {
                        _ctrl.params.original_priority = _ctrl.params.priority;
                        _ctrl.params.original_section  = _ctrl.params.section;

                        api.section.when( _ctrl.params.ubq_section.section, function( _section_instance ) {
                                _section_instance.expanded.bind( function( expanded ) {
                                      if ( expanded ) {
                                            if ( _ctrl.params.ubq_section.priority ) {
                                                  _ctrl.priority( _ctrl.params.ubq_section.priority );
                                            }
                                            _ctrl.section( _ctrl.params.ubq_section.section );
                                      }
                                      else {
                                            _ctrl.priority( _ctrl.params.original_priority );
                                            _ctrl.section( _ctrl.params.original_section );
                                      }
                                });

                        } );
                  }
            });
      }
      var _closeModOpt = function() {
            if ( ! _.has( api, 'czr_ModOptVisible') )
              return;
            api.czr_ModOptVisible(false);
      };
      api.czr_activeSectionId.bind( _closeModOpt );
      api.czr_activePanelId.bind( _closeModOpt );
      api.bind('ready', function() {
            if ( 'function' != typeof api.Section ) {
              throw new Error( 'Your current version of WordPress does not support the customizer sections needed for this theme. Please upgrade WordPress to the latest version.' );
            }
            var _storeCurrentSection = function( expanded, section_id ) {
                  api.czr_activeSectionId( expanded ? section_id : '' );
            };
            api.section.each( function( _sec ) {
                  _sec.expanded.bind( function( expanded ) { _storeCurrentSection( expanded, _sec.id ); } );
            });
            api.section.bind( 'add', function( section_instance ) {
                  api.trigger('czr-paint', { active_panel_id : section_instance.panel() } );
                  section_instance.expanded.bind( function( expanded ) { _storeCurrentSection( expanded, section_instance.id ); } );
            });

            var _storeCurrentPanel = function( expanded, panel_id ) {
                  api.czr_activePanelId( expanded ? panel_id : '' );
                  if ( _.isEmpty( api.czr_activePanelId() ) ) {
                        api.czr_activeSectionId( '' );
                  }
            };
            api.panel.each( function( _panel ) {
                  _panel.expanded.bind( function( expanded ) { _storeCurrentPanel( expanded, _panel.id ); } );
            });
            api.panel.bind( 'add', function( panel_instance ) {
                  panel_instance.expanded.bind( function( expanded ) { _storeCurrentPanel( expanded, panel_instance.id ); } );
            });
      });
      api.bind('ready', function() {
            var _do = function() {
                  api.section('themes').active.bind( function( active ) {
                        if ( ! _.has( serverControlParams, 'isThemeSwitchOn' ) || ! _.isEmpty( serverControlParams.isThemeSwitchOn ) )
                          return;
                        api.section('themes').active(false);
                        api.section('themes').active.callbacks = $.Callbacks();
                  });
            };
            if ( api.section.has( 'themes') )
                _do();
            else
                api.section.when( 'themes', function( _s ) {
                      _do();
                });
      });
      api.czr_skopeReady = $.Deferred();
      api.bind( 'ready' , function() {
            if ( serverControlParams.isSkopOn ) {
                  api.czr_isLoadingSkope  = new api.Value( false );
                  api.czr_isLoadingSkope.bind( function( loading ) {
                        toggleSkopeLoadPane( loading );
                  });
                  api.czr_skopeBase   = new api.CZR_skopeBase();
                  api.czr_skopeSave   = new api.CZR_skopeSave();
                  api.czr_skopeReset  = new api.CZR_skopeReset();
                  api.trigger('czr-skope-started');
                  api.czr_skopeReady.done( function() {
                        api.trigger('czr-skope-ready');
                  });
                  setTimeout( function() {
                      if ( 'pending' == api.czr_skopeReady.state() )  {
                            api.czr_skopeBase.toggleTopNote( true, {
                                  title : 'There was a problem when trying to load the customizer.',//@to_translate
                                  message : 'Please open your <a href="http://docs.presscustomizr.com/article/272-inspect-your-webpages-in-your-browser-with-the-development-tools" target="_blank">browser debug tool</a>, and report any error message (in red) printed in the javascript console in the <a href="https://wordpress.org/support/theme/hueman" target="_blank">Hueman theme forum</a>.',//@to_translate
                                  selfCloseAfter : 40000
                            });

                            api.czr_isLoadingSkope( false );
                      }
                  }, 30000);
            }
            if ( serverControlParams.isChangeSetOn ) {
                  api.settings.timeouts.changesetAutoSave = 10000;
            }
      } );
      if ( ! _.has( api, '_latestRevision') ) {
            api._latestRevision = 0;
            api._latestSettingRevisions = {};
            api.bind( 'change', function incrementChangedSettingRevision( setting ) {
                  api._latestRevision += 1;
                  api._latestSettingRevisions[ setting.id ] = api._latestRevision;
            } );
            api.bind( 'ready', function() {
                  api.bind( 'add', function incrementCreatedSettingRevision( setting ) {
                        if ( setting._dirty ) {
                              api._latestRevision += 1;
                              api._latestSettingRevisions[ setting.id ] = api._latestRevision;
                        }
                  } );
            } );
      }
      var toggleSkopeLoadPane = function( loading ) {
            loading = _.isUndefined( loading ) ? true : loading;
            var self = this, $skopeLoadingPanel,
                _render = function() {
                      var dfd = $.Deferred();
                      try {
                          _tmpl =  wp.template( 'czr-skope-pane' )({ is_skope_loading : true });
                      }
                      catch(e) {
                          throw new Error('Error when parsing the the reset skope template : ' + e );//@to_translate
                      }
                      $.when( $('#customize-preview').after( $( _tmpl ) ) )
                            .always( function() {
                                  dfd.resolve( $( '#czr-skope-pane' ) );
                            });

                      return dfd.promise();
                },
                _destroy = function() {
                      _.delay( function() {
                            $.when( $('body').removeClass('czr-skope-pane-open') ).done( function() {
                                  _.delay( function() {
                                        $.when( $('body').removeClass('czr-skop-loading') ).done( function() {
                                              if ( false !== $( '#czr-skope-pane' ).length ) {
                                                    setTimeout( function() {
                                                          $( '#czr-skope-pane' ).remove();
                                                    }, 400 );
                                              }
                                        });
                                  }, 200);
                            });
                      }, 50);
                };
            if ( 'pending' == api.czr_skopeReady.state() && loading ) {
                  $('body').addClass('czr-skop-loading');
                  _render()
                        .done( function( $_el ) {
                              $skopeLoadingPanel = $_el;
                        })
                        .then( function() {
                              if ( ! $skopeLoadingPanel.length )
                                return;

                              _.delay( function() {
                                    var _height = $('#customize-preview').height();
                                    $skopeLoadingPanel.css( 'line-height', _height +'px' ).css( 'height', _height + 'px' );
                                    $('body').addClass('czr-skope-pane-open');
                              }, 50 );
                        });
            }

            api.czr_skopeReady.done( function() {
                  _destroy();
            });
            if ( ! loading ) {
                  _destroy();
            }
      };//toggleSkopeLoadPane



})( wp.customize , jQuery, _);
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {

    globalSettingVal : {},//will store the global setting val. Populated on init.

    initialize: function() {
          var self = this;
          self.skope_colors = {
                global : 'rgb(255, 255, 255)',
                special_group : 'rgba(173, 213, 247, 0.55)',
                group  : 'rgba(173, 213, 247, 0.55)',
                local  : 'rgba(78, 122, 199, 0.35)'
          };
          api.czr_isPreviewerSkopeAware   = $.Deferred();
          api.czr_initialSkopeCollectionPopulated = $.Deferred();
          self.skopeWrapperEmbedded       = $.Deferred();
          api.czr_skope                   = new api.Values();
          api.czr_skopeCollection         = new api.Value([]);//all available skope, including the current skopes
          api.czr_currentSkopesCollection = new api.Value([]);
          api.czr_activeSkopeId           = new api.Value();
          api.czr_dirtyness               = new api.Value( false );
          api.czr_isResettingSkope        = new api.Value( false );
          api.state.create('switching-skope')(false);
          api.czr_dirtyness.callbacks.add( function() { return self.apiDirtynessReact.apply(self, arguments ); } );
          api.czr_isLoadingSkope( true );
          self.bindAPISettings();
          api.state.bind( 'change', function() {
                self.setSaveButtonStates();
          });
          if ( 'pending' == self.skopeWrapperEmbedded.state() ) {
                $.when( self.embedSkopeWrapper() ).done( function() {
                      self.skopeWrapperEmbedded.resolve();
                });
          }
          api.previewer.bind( 'czr-skopes-synced', function( data ) {
                if ( ! serverControlParams.isSkopOn )
                  return;
                var preview = this,
                    previousSkopeCollection = api.czr_currentSkopesCollection();
                if ( ! _.has( data, 'czr_skopes') ) {
                      throw new Error('Missing skopes in the server data');
                }
                api.czr_skopeBase.updateSkopeCollection( data.czr_skopes , preview.channel() );
                api.czr_initialSkopeCollectionPopulated.then( function() {
                      var refreshActiveSkope = _.isUndefined( _.findWhere( api.czr_currentSkopesCollection(), {id : api.czr_activeSkopeId() } ) );
                      api.czr_skopeBase.reactWhenSkopeSyncedDone( data ).done( function() {
                            if ( refreshActiveSkope ) {
                                  api.czr_activeSkopeId( self.getActiveSkopeId() )
                                        .done( function() {
                                              api.consoleLog('INITIAL ACTIVE SKOPE SET : ' + arguments[1] + ' => ' + arguments[0] );
                                              if ( 'pending' == api.czr_skopeReady.state() ) {
                                                    api.czr_skopeReady.resolve( self.getActiveSkopeId() );
                                              }
                                              self._writeCurrentSkopeTitle();
                                        });
                            } else if ( ! _.isEmpty( previousSkopeCollection ) ) { //Rewrite the title when the local skope has changed
                                  var _prevLoc = _.findWhere( previousSkopeCollection , { skope : 'local' } ).opt_name,
                                      _newLoc  =_.findWhere( data.czr_skopes, { skope : 'local' } ).opt_name;

                                  if ( _newLoc !== _prevLoc && 'resolved' == api.czr_skopeReady.state() ) {
                                        self._writeCurrentSkopeTitle();
                                  }
                            }
                      });
                });
          });
          api.czr_currentSkopesCollection.bind( function( to, from ) {
                return self.currentSkopesCollectionReact( to, from );
          }, { deferred : true });
          api.czr_initialSkopeCollectionPopulated.done( function() {
                api.czr_activeSkopeId.bind( function( to, from ) {
                        if ( _.has( api, 'czr_ModOptVisible') ) {
                              api.czr_ModOptVisible( false );
                        }
                        return self.activeSkopeReact( to, from );
                }, { deferred : true } );
                api.czr_activeSectionId.callbacks.add( function() { return self.activeSectionReact.apply(self, arguments ); } );
                api.czr_activePanelId.callbacks.add( function() { return self.activePanelReact.apply(self, arguments ); } );
          });
          api.bind( 'skope-switched', function( skope_id, previous_id ) {
                api.czr_skopeReady.then( function() {
                      api.czr_CrtlDependenciesReady.then( function() {
                            if ( ! _.isUndefined( api.czr_activeSectionId() ) && ! _.isEmpty( api.czr_activeSectionId() ) ) {
                                  api.czr_ctrlDependencies.setServiDependencies( api.czr_activeSectionId(), null, true );//target sec id, source sec id, refresh
                            }
                      });
                      self.updateCtrlSkpNot( api.CZR_Helpers.getSectionControlIds() );
                      if ( api.czr_skope.has( previous_id ) ) {
                            $('#customize-controls').removeClass( [ 'czr-', api.czr_skope( previous_id )().skope, '-skope-level'].join('') );
                      }
                      if ( api.czr_skope.has( skope_id ) ) {
                            $('#customize-controls').addClass( [ 'czr-', api.czr_skope( skope_id )().skope, '-skope-level'].join('') );
                      }
                });
          });
          api.czr_serverNotification   = new api.Value( {status : 'success', message : '', expanded : true} );
          api.czr_serverNotification.bind( function( to, from ) {
                  self.toggleServerNotice( to );
          });
          api.czr_topNoteVisible = new api.Value( false );
          api.czr_skopeReady.then( function() {
                api.czr_topNoteVisible.bind( function( visible ) {
                        var noteParams = {},
                            _defaultParams = {
                                  title : '',
                                  message : '',
                                  actions : '',
                                  selfCloseAfter : 20000
                            };
                        noteParams = $.extend( _defaultParams , serverControlParams.topNoteParams );
                        noteParams.actions = function() {
                              var _query = $.extend(
                                    api.previewer.query(),
                                    { nonce:  api.previewer.nonce.save }
                              );
                              wp.ajax.post( 'czr_dismiss_top_note' , _query )
                                  .always( function () {})
                                  .fail( function ( response ) { api.consoleLog( 'czr_dismiss_top_note failed', _query, response ); })
                                  .done( function( response ) {});
                        };

                        self.toggleTopNote( visible, noteParams );
                });
                _.delay( function() {
                      api.czr_topNoteVisible( ! _.isEmpty( serverControlParams.isTopNoteOn ) || 1 == serverControlParams.isTopNoteOn );
                }, 2000 );
          });
          self.scopeSwitcherEventMap = [
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-dismiss-notification',
                      name      : 'dismiss-notification',
                      actions   : function() {
                            api.czr_serverNotification( { expanded : false } );
                      }
                },
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-toggle-title-notice',
                      name      : 'toggle-title-notice',
                      actions   : function( params ) {
                            if ( _.isUndefined( self.skopeTitleNoticeVisible ) ) {
                                  self.skopeTitleNoticeVisible = new api.Value( false );
                                  self.skopeTitleNoticeVisible.bind( function( to ) {
                                        params.dom_el.find( '.czr-skope-title')
                                              .toggleClass( 'notice-visible', to );
                                  });
                            }

                            self.skopeTitleNoticeVisible( ! self.skopeTitleNoticeVisible() );
                      }
                }
          ];
          api.CZR_Helpers.setupDOMListeners( self.scopeSwitcherEventMap , { dom_el : $('.czr-scope-switcher') }, self );
          self.refreshedControls = [ 'czr_cropped_image'];// [ 'czr_cropped_image', 'czr_multi_module', 'czr_module' ];
          self.initWidgetSidebarSpecifics();
          api.bind( 'czr-paint', function( params ) {
                api.czr_skopeReady.then( function() {
                      self.wash( params ).paint( params );
                });
          });
    },//initialize
    embedSkopeWrapper : function() {
          var self = this;
          $('#customize-header-actions').append( $('<div/>', {class:'czr-scope-switcher', html:'<div class="czr-skopes-wrapper"></div>'}) );
          $('body').addClass('czr-skop-on');
          var _eventMap = [
              {
                    trigger   : 'click keydown',
                    selector  : '.czr-skope-switch',
                    name      : 'control_skope_switch',
                    actions   : function( params ) {
                          var _skopeIdToSwithTo = $( params.dom_event.currentTarget, params.dom_el ).attr('data-skope-id');
                          if ( ! _.isEmpty( _skopeIdToSwithTo ) && api.czr_skope.has( _skopeIdToSwithTo ) )
                            api.czr_activeSkopeId( _skopeIdToSwithTo );
                    }
              }
          ];
          api.CZR_Helpers.setupDOMListeners( _eventMap , { dom_el : $('.czr-scope-switcher') }, self );
    },
    apiDirtynessReact : function( is_dirty ) {
          $('body').toggleClass('czr-api-dirty', is_dirty );
          api.state( 'saved')( ! is_dirty );
    },
    setSaveButtonStates : function() {
          if ( ! api.state.has('saving') ) {
                api.state.create('saving');
                api.state('saving').bind( function( isSaving ) {
                      $( document.body ).toggleClass( 'saving', isSaving );
                } );
          }
          var saveBtn   = $( '#save' ),
              closeBtn  = $( '.customize-controls-close' ),
              saved     = api.state( 'saved'),
              saving    = api.state( 'saving'),
              activated = api.state( 'activated' ),
              changesetStatus = api.state.has('changesetStatus' ) ? api.state( 'changesetStatus' )() : 'auto-draft';

          if ( api.czr_dirtyness() || ! saved() ) {
                saveBtn.val( api.l10n.save );
                closeBtn.find( '.screen-reader-text' ).text( api.l10n.cancel );
          } else {
                saveBtn.val( api.l10n.saved );
                closeBtn.find( '.screen-reader-text' ).text( api.l10n.close );
          }
          var canSave = ! saving() && ( ! activated() || ! saved() ) && 'publish' !== changesetStatus;
          saveBtn.prop( 'disabled', ! canSave );
    }
});//$.extend()

var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
      toggleServerNotice : function( notice ) {
            notice = _.isObject( notice ) ? notice : {};
            notice = _.extend( {
                  status : 'success',
                  expanded : true,
                  message : '',
                  auto_collapse : false
            }, notice );
            if ( 'changeset_already_published' == notice.message )
              return;
            if ( ! serverControlParams.isDevMode )
              return;

            this.serverNoticeEmbedded = this.serverNoticeEmbedded || $.Deferred();

            var self = this,
                _embed = function() {
                      $('.czr-scope-switcher').prepend(
                            $( '<div/>', {
                                  class:'czr-server-notice',
                                  html:'<span class="czr-server-message"></span><span class="fa fa-times-circle czr-dismiss-notification"></span>'
                            } )
                      );
                },
                _toggleNotice = function() {
                      var $notif_wrap         = $( '.czr-server-notice', '.czr-scope-switcher' ),
                          $header             = $('.wp-full-overlay-header'),
                          $sidebar            = $('.wp-full-overlay-sidebar .wp-full-overlay-sidebar-content'),
                          _header_height,
                          _notif_wrap_height,
                          _set_height = function( _h ) {
                                return true;
                          };
                      if ( self.skopeTitleNoticeVisible )
                          self.skopeTitleNoticeVisible( false );

                      if ( ! notice.expanded ) {
                            $notif_wrap
                                  .fadeOut( {
                                        duration : 200,
                                        complete : function() {
                                  } } );
                            setTimeout( function() {
                                  _set_height();
                            } , 200 );

                      } else {
                            $notif_wrap.toggleClass( 'czr-server-error', 'error' == notice.status );
                            if ( 'error' == notice.status ) {
                                  $('.czr-server-message', $notif_wrap )
                                        .html( _.isEmpty( notice.message ) ? 'Server Problem.' : notice.message );
                            } else {
                                  $('.czr-server-message', $notif_wrap )
                                        .html( _.isEmpty( notice.message ) ? 'Success.' : notice.message );
                            }
                            _notif_wrap_height  = $( '.czr-server-notice', '.czr-scope-switcher' ).outerHeight();
                            _header_height  = $header.outerHeight() + _notif_wrap_height;

                            setTimeout( function() {
                                  $.when( _set_height( _header_height ) ).done( function() {
                                        $notif_wrap
                                        .fadeIn( {
                                              duration : 200,
                                              complete : function() {
                                                    $( this ).css( 'height', 'auto' );
                                        } } );
                                  } );
                            }, 400 );
                      }
                };
            if ( 'pending' == self.serverNoticeEmbedded.state() ) {
                  $.when( _embed() ).done( function() {
                        setTimeout( function() {
                              self.serverNoticeEmbedded.resolve();
                              _toggleNotice();
                        }, 200 );
                  });
            } else {
                  _toggleNotice();
            }
            _.delay( function() {
                        api.czr_serverNotification( { expanded : false } );
                  },
                  ( 'success' == notice.status || false !== notice.auto_collapse ) ? 4000 : 5000
            );
      },
      buildServerResponse : function( _r ) {
            var resp = false;
            if ( _.isObject( _r ) ) {
                  if ( _.has( _r, 'responseJSON') && ! _.isUndefined( _r.responseJSON.data ) && ! _.isEmpty( _r.responseJSON.data ) ) {
                        resp = _r.responseJSON.data;
                  }
                  else if ( _.has( _r , 'statusText' ) && ! _.isEmpty( _r.statusText ) ) {
                        resp = _r.statusText;
                  }
            }
            if ( _.isObject( _r ) && ! resp ) {
                  try {
                        JSON.stringify( _r );
                  } catch( e ) {
                        resp = 'Server Error';
                  }
            } else if ( ! resp ) {
                  resp = '0' === _r ? 'Not logged in.' : _r;//@to_translate
            } else if ( '-1' === _r ) {
                  resp = 'Identification issue detected, please refresh your page.';//@to_translate
            }
            return resp;
      }
});//$.extend()

var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
      toggleTopNote : function( visible, noteParams ) {
            noteParams = _.isObject( noteParams ) ? noteParams : {};
            var self = this,
                _defaultParams = {
                      title : '',
                      message : '',
                      actions : '',
                      selfCloseAfter : 20000
                },
                _renderAndSetup = function() {
                      $.when( self.renderTopNoteTmpl( noteParams ) ).done( function( $_el ) {
                            self.welcomeNote = $_el;
                            _.delay( function() {
                                $('body').addClass('czr-top-note-open');
                            }, 200 );
                            api.CZR_Helpers.setupDOMListeners(
                                  [ {
                                        trigger   : 'click keydown',
                                        selector  : '.czr-top-note-close',
                                        name      : 'close-top-note',
                                        actions   : function() {
                                              _destroy().done( function() {
                                                    if ( _.isFunction( noteParams.actions ) ) {
                                                          noteParams.actions();
                                                    }
                                              });
                                        }
                                  } ] ,
                                  { dom_el : self.welcomeNote },
                                  self
                            );
                      });
                },
                _destroy = function() {
                      var dfd = $.Deferred();
                      $('body').removeClass('czr-top-note-open');
                      if ( self.welcomeNote.length ) {
                            _.delay( function() {
                                  self.welcomeNote.remove();
                                  dfd.resolve();
                            }, 300 );
                      } else {
                          dfd.resolve();
                      }
                      return dfd.promise();
                };

            noteParams = $.extend( _defaultParams , noteParams);

            if ( visible ) {
                  _renderAndSetup();
            } else {
                  _destroy();
            }
            _.delay( function() {
                        _destroy();
                  },
                  noteParams.selfCloseAfter || 20000
            );
      },
      renderTopNoteTmpl : function( params ) {
            if ( $( '#czr-top-note' ).length )
              return $( '#czr-top-note' );

            var self = this,
                _tmpl = '',
                _title = params.title || '',
                _message = params.message || '';

            try {
                  _tmpl =  wp.template( 'czr-top-note' )( { title : _title } );
            }
            catch(e) {
                  throw new Error('Error when parsing the the top note template : ' + e );//@to_translate
            }
            $('#customize-preview').after( $( _tmpl ) );
            $('.czr-note-message', '#czr-top-note').html( _message );
            return $( '#czr-top-note' );
      }
});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    bindAPISettings : function( requestedSetId ) {
          var self = this,
              _settingChangeReact = function( new_val, old_val, o ) {
                    var setId = this.id,
                        skope_id;

                    if ( ! _.has( api, 'czr_activeSkopeId') || _.isUndefined( api.czr_activeSkopeId() ) ) {
                      api.consoleLog( 'The api.czr_activeSkopeId() is undefined in the api.previewer._new_refresh() method.');
                    }
                    if ( api( setId )._dirty ) {
                          skope_id = self.isSettingSkopeEligible( setId ) ? api.czr_activeSkopeId() : self.getGlobalSkopeId();
                          api.czr_skope( skope_id ).updateSkopeDirties( setId, new_val );
                    }
                    if ( _.has( api.control(setId), 'czr_states' ) && ! api.control(setId).czr_states( 'isResetting' )() ) {
                          api.control(setId).czr_states( 'resetVisible' )( false );
                    }
                    if ( self.isSettingSkopeEligible( setId ) ) {
                          self.updateCtrlSkpNot( setId );
                    }
              };//bindListener()
          if ( ! _.isUndefined( requestedSetId ) ) {
                api( requestedSetId ).bind( _settingChangeReact );
          }
          else {
                api.each( function ( _setting ) {
                    _setting.bind( _settingChangeReact );
                });
          }
          var _dynamicallyAddedSettingsReact = function( setting_instance ) {
                if ( setting_instance.callbacks.has( _settingChangeReact ) )
                  return;
                setting_instance.bind( _settingChangeReact );
          };

          if ( ! api.topics.change.has( _dynamicallyAddedSettingsReact ) ) {
                api.bind( 'change', _dynamicallyAddedSettingsReact );
          }
    }
});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    reactWhenSkopeSyncedDone : function( server_params ) {
          var self = this, dfd = $.Deferred();
          if ( ! _.has( server_params, 'czr_skopes' ) || _.isEmpty( server_params.czr_skopes ) ) {
                throw new Error( 'Missing skope data after refresh', server_params );
          }
          if ( ! api.czr_dirtyness() ) {
                api.czr_dirtyness( _.isBoolean( server_params.isChangesetDirty ) ? server_params.isChangesetDirty : false );
          }

          var _sentSkopeCollection = server_params.czr_skopes;
          _.each( api.czr_skopeCollection(), function( _skp ) {
                var _sent_skope = _.findWhere( _sentSkopeCollection, { opt_name : _skp.opt_name } );
                if ( _.isUndefined( _sent_skope ) )
                  return;
                var _changeset_candidate = _.isEmpty( _sent_skope.changeset || {} ) ? {} : _sent_skope.changeset,
                    _api_ready_chgset = {};
                _.each( _changeset_candidate, function( _val, _setId ) {
                      if ( ! api.has( _setId ) ) {
                            api.consoleLog( 'In reactWhenSkopeSyncedDone : attempting to update the changeset with a non registered setting : ' + _setId );
                      }
                      _api_ready_chgset[_setId] = _val;
                });
                api.czr_skope( _skp.id ).changesetValues( _api_ready_chgset );
          });
          _.each( api.czr_skopeCollection(), function( _skp ) {
                var _sent_skope = _.findWhere( _sentSkopeCollection, { opt_name : _skp.opt_name } );
                if ( _.isUndefined( _sent_skope ) )
                  return;
                var _current_db_vals  = $.extend( true, {}, api.czr_skope( _skp.id ).dbValues() ),
                    _dbVals_candidate = $.extend( _current_db_vals , _sent_skope.db || {} ),
                    _api_ready_dbvals = {};
                _.each( _dbVals_candidate, function( _val, _setId ) {
                      if ( ! api.has( _setId ) ) {
                            api.consoleLog( 'In reactWhenSkopeSyncedDone : attempting to update the db values with a non registered setting : ' + _setId );
                      }
                      _api_ready_dbvals[_setId] = _val;
                });


                api.czr_skope( _skp.id ).dbValues( _api_ready_dbvals );
          });
          _.delay( function() {
              dfd.resolve();
          }, 500 );
          return dfd.promise();
    }
});//$.extend()

var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    _maybeSetupAssignedMenuLocations : function( active_section ) {
          if ( _.isUndefined( active_section ) || _.isEmpty( active_section ) || ! api.section.has( active_section.id ) ) {
                api.consoleLog( 'In _maybeSetupAssignedMenuLocations : no valid section_id provided.');
          }
          var self = this;
          if ( ! active_section.assignedLocations )
            return;
          var _assignedLocReact = function( locations ) {};

          if ( ! active_section.assignedLocations.callbacks.has( _assignedLocReact ) ) {
                active_section.assignedLocations.bind( _assignedLocReact );
          }
    },
    activeSectionReact : function( active_sec_id , previous_sec_id ) {
          if ( 'add_menu' != active_sec_id ) {
                api.trigger('czr-paint', { active_section_id : active_sec_id } );
          }

          var self = this,
              _doReactPrevious = function( previous_sec_id ) {
                    var controls = api.CZR_Helpers.getSectionControlIds( previous_sec_id  );
                    _.each( controls, function( ctrlId ) {
                          if ( ! api.has( ctrlId ) || _.isUndefined( api.control( ctrlId ) ) )
                            return;
                          var ctrl = api.control( ctrlId );
                          if ( ! _.has( ctrl, 'czr_states' ) )
                            return;
                          ctrl.czr_states( 'noticeVisible' )( false );
                          ctrl.czr_states( 'resetVisible' )( false );
                    });
              },
              _doReactActive = function( active_section, active_sec_id ) {
                    self.setupActiveSkopedControls( {
                          section_id : active_sec_id
                    });
                    self.processSilentUpdates( { section_id : active_sec_id  } )
                          .fail( function() {
                                throw new Error( 'Fail to process silent updates after initial skope collection has been populated' );
                          })
                          .done( function() {
                                if ( ! self.isExcludedSidebarsWidgets() ) {
                                      self.forceSidebarDirtyRefresh( active_sec_id , api.czr_activeSkopeId() );
                                }
                          });
                    if ( ! _.has( api.topics, 'active-section-setup' ) ) {
                          api.bind( 'active-section-setup', function( params ) {
                                var defaults = {
                                      controls : [],
                                      section_id : ''
                                };
                                params = _.extend( defaults, params );
                                self._maybeSetupAssignedMenuLocations( params );
                          });
                    }
                    api.czr_skopeReady.then( function() {
                          var _switchBack = function( _title ) {
                                api.czr_serverNotification({
                                      status:'success',
                                      message : [ _title, 'can only be customized site wide.' ].join(' ')//@to_translate
                                });
                                api.czr_activeSkopeId( self.getGlobalSkopeId() );
                          };
                          if ( 'global' != api.czr_skope( api.czr_activeSkopeId() )().skope ) {
                                if (
                                  self.isExcludedWPCustomCss() &&
                                  ( 'custom_css' == active_sec_id || 'admin_sec' == active_sec_id )
                                ) {
                                      _switchBack( api.section( active_sec_id ).params.title );
                                }

                                if ( 'nav_menu[' == active_sec_id.substring( 0, 'nav_menu['.length ) || 'add_menu' == active_sec_id ) {
                                      api.czr_serverNotification({
                                            status:'success',
                                            message : [
                                                  'Menus are created site wide.'//@to_translate
                                            ].join(' ')
                                      });
                                }
                          }
                    });
                    api.trigger('active-section-setup', active_section );
              };
          api.czr_initialSkopeCollectionPopulated.then( function() {
                api.section.when( active_sec_id , function( active_section ) {
                      active_section.deferred.embedded.then( function() {
                            _doReactActive( active_section, active_sec_id );
                      });
                });
                if ( ! _.isEmpty( previous_sec_id ) && api.section.has( previous_sec_id ) ) {
                      _doReactPrevious( previous_sec_id );
                }
          });
    },
    activePanelReact : function( active_panel_id , previous_panel_id ) {
          var self = this;
          api.czr_initialSkopeCollectionPopulated.then( function() {
                api.trigger('czr-paint', { active_panel_id : active_panel_id } );
                var _switchBack = function( _title ) {
                      api.czr_serverNotification({
                            status:'success',
                            message : [ _title, 'can only be customized site wide.' ].join(' ')
                      });
                      api.czr_activeSkopeId( self.getGlobalSkopeId() );
                };
                api.czr_skopeReady.then( function() {
                      if ( 'global' != api.czr_skope( api.czr_activeSkopeId() )().skope ) {
                            if ( self.isExcludedSidebarsWidgets() && 'widgets' == active_panel_id ) {
                                  api.czr_serverNotification({
                                        status:'success',
                                        message : [
                                              'Widgets are created site wide.'//@to_translate
                                        ].join(' ')
                                  });
                            }
                      }
                });
                api.czr_skopeReady.then( function() {
                      if ( 'nav_menus' == active_panel_id ) {
                            _.each( api.panel( active_panel_id ).sections(), function( _sec ) {
                                  self.processSilentUpdates( { section_id : _sec.id, awake_if_not_active : true } );
                            });
                      }
                });
          });
    }
});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    wash : function( params ) {
          var self = this,
              _do_wash = function( element ) {
                    if ( ! _.has( element, 'el') || ! element.el.length )
                      return;
                    $.when( element.el.removeClass('czr-painted') ).done( function() {
                          $(this).css( 'background', '' ).css('color', '');
                    });
              };
          if ( api.czr_skopeBase.paintedElements ) {
                _.each( api.czr_skopeBase.paintedElements(), function( _el ) { _do_wash( _el ); } );
                api.czr_skopeBase.paintedElements( [] );
          }
          return this;
    },
    paint : function( params ) {
          var _bgColor = 'inherit',
              defaults = {
                    active_panel_id : api.czr_activePanelId(),
                    active_section_id : api.czr_activeSectionId(),
                    is_skope_switch : false
              },
              _paint_candidates = [];
          params = $.extend( defaults, params );

          if ( ! _.isUndefined( api.czr_activeSkopeId() ) && api.czr_skope.has( api.czr_activeSkopeId() ) ) {
                  _bgColor = api.czr_skope( api.czr_activeSkopeId() ).color;
          }
          var _do_paint = function( element ) {
                if ( ! _.has( element, 'el') || ! element.el.length )
                  return;
                if ( params.is_skope_switch ) {
                      $.when( element.el.addClass('czr-painted') ).done( function() {
                            $(this).css( 'background', element.bgColor || _bgColor );
                      });
                } else {
                      element.el.css( 'background', element.bgColor || _bgColor );
                }
                if ( 'global' != api.czr_skope( api.czr_activeSkopeId() )().skope ) {
                       element.el.css( 'color', '#000');
                }

          };

          api.czr_skopeBase.paintedElements = api.czr_skopeBase.paintedElements || new api.Value( [] );
          if ( _.isEmpty( params.active_panel_id ) && _.isEmpty( params.active_section_id ) ) {
                _paint_candidates.push( {
                      el : $( '#customize-info' ).find('.accordion-section-title').first()
                });
                api.panel.each( function( _panel ) {
                      _paint_candidates.push( {
                            el : _panel.container.find( '.accordion-section-title').first()
                      });
                });
                api.section.each( function( _section ) {
                      if ( ! _.isEmpty( _section.panel() ) )
                        return;
                      _paint_candidates.push( {
                            el : _section.container.find( '.accordion-section-title').first()
                      });
                });
          }
          if ( ! _.isEmpty( params.active_panel_id ) && _.isEmpty( params.active_section_id ) ) {
                api.panel.when( params.active_panel_id , function( active_panel ) {
                      active_panel.deferred.embedded.then( function() {
                            _paint_candidates.push( {
                                  el : active_panel.container.find( '.accordion-section-title, .customize-panel-back' )
                            });
                      });
                });
          }
          if ( ! _.isEmpty( params.active_section_id ) ) {
                api.section.when( params.active_section_id , function( active_section ) {
                      active_section.deferred.embedded.then( function() {
                            _paint_candidates.push(
                                  {
                                        el : active_section.container.find( '.customize-section-title, .customize-section-back' ),
                                        bgColor : 'inherit'
                                  },
                                  {
                                        el : active_section.container
                                  }
                            );
                            if ( ! api.czr_isChangeSetOn() ) {
                                  _paint_candidates.push(
                                        {
                                              el : active_section.container.find('.accordion-section-content')
                                        }
                                  );
                            }
                      });
                });
          }
          _.each( _paint_candidates, function( _el ) { _do_paint( _el ); } );
          api.czr_skopeBase.paintedElements( _paint_candidates );
          return this;
    }
});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    isSkopeRegisteredInCollection : function( skope_id, collection ) {
          var self = this;
          collection = collection || api.czr_skopeCollection();
          return ! _.isUndefined( _.findWhere( collection, { id : skope_id } ) );
    },
    isSkopeRegisteredInCurrentCollection : function( skope_id, collection ) {
          var self = this;
          collection = collection || api.czr_currentSkopesCollection();
          return ! _.isUndefined( _.findWhere( collection, { id : skope_id } ) );
    },
    isGlobalSkopeRegistered : function() {
          var _model = _.findWhere( api.czr_currentSkopesCollection(), { skope : 'global'} );
          return _.isObject( _model ) && _.has( _model, 'id' );
    },
    getGlobalSkopeId : function() {
          if ( ! _.has(api, 'czr_skope') )
            return '';
          var id = '';
          api.czr_skope.each( function(skp){
              if ( 'global' == skp().skope )
                id = skp().id;
          });
          return id;
    },
    getChangedGlobalDBSettingValues : function( serverGlobalDBValues ) {
          var _changedDbVal = {};

          _.each( serverGlobalDBValues, function( _val, _setId ){
              _wpSetId = api.CZR_Helpers.build_setId( _setId);

              if ( ! _.has( api.settings.settings, _wpSetId ) )
                return;
              if ( _.isEqual( _val , api.settings.settings[ _wpSetId ].value ) )
                return;
              _changedDbVal[_setId] = _val;
          });
          return _changedDbVal;
    },
    getActiveSkopeId : function( _current_skope_collection ) {
          _current_skope_collection = _current_skope_collection || api.czr_currentSkopesCollection();

          var _currentSkopeLevel = ( ! _.isEmpty( api.czr_activeSkopeId() ) && api.czr_skope.has( api.czr_activeSkopeId() ) ) ? api.czr_skope( api.czr_activeSkopeId() )().skope : serverControlParams.isLocalSkope ? 'local' : 'global',
              _newSkopeCandidate = _.findWhere( _current_skope_collection, { skope : _currentSkopeLevel } );

          _skpId = ! _.isUndefined( _newSkopeCandidate ) ? _newSkopeCandidate.id : _.findWhere( _current_skope_collection, { skope : 'global' } ).id;

          if ( _.isUndefined( _skpId ) ) {
            throw new Error( 'No default skope was found in getActiveSkopeId ', _current_skope_collection );
          }
          return _skpId;
    },
    getActiveSkopeName : function() {
          if ( ! api.czr_skope.has( api.czr_activeSkopeId() ) )
            return 'global';
          return api.czr_skope( api.czr_activeSkopeId() )().skope;
    },
    isSettingSkopeEligible : function( setId ) {
          var self = this,
              shortSetId = api.CZR_Helpers.getOptionName( setId );

          if( _.isUndefined( setId ) || ! api.has( setId ) ) {
            api.consoleLog( 'THE SETTING ' + setId + ' IS NOT ELIGIBLE TO SKOPE BECAUSE UNDEFINED OR NOT REGISTERED IN THE API.' );
            return false;
          }
          if ( self.isExcludedWPBuiltinSetting( setId ) )
            return false;
          if ( _.contains( serverControlParams.skopeExcludedSettings, shortSetId ) ) {
            return false;
          } else if ( self.isThemeSetting( setId ) ) {
            return true;
          } else
           return true;
    },
    isSettingResetEligible : function( setId ) {
          var self = this,
              shortSetId = api.CZR_Helpers.getOptionName( setId );

          if( _.isUndefined( setId ) || ! api.has( setId ) ) {
            api.consoleLog( 'THE SETTING ' + setId + ' IS NOT ELIGIBLE TO RESET BECAUSE UNDEFINED OR NOT REGISTERED IN THE API.' );
            return;
          }
          if ( self.isExcludedWPBuiltinSetting( setId ) )
            return;
          if ( ! self.isThemeSetting( setId ) && ! self.isWPAuthorizedSetting( setId ) ) {
            api.consoleLog( 'THE SETTING ' + setId + ' IS NOT ELIGIBLE TO RESET BECAUSE NOT PART OF THE THEME OPTIONS AND NOT WP AUTHORIZED BUILT IN OPTIONS' );
          } else
           return true;
    },
    isThemeSetting : function( setId ) {
          return _.isString( setId ) && -1 !== setId.indexOf( serverControlParams.themeOptions );
    },
    isWPAuthorizedSetting : function( setId ) {
          return _.isString( setId ) && _.contains( serverControlParams.wpBuiltinSettings, setId );
    },
    isExcludedWPBuiltinSetting : function( setId ) {
          var self = this;
          if ( _.isUndefined(setId) )
            return true;
          if ( 'active_theme' == setId )
            return true;
          if ( _.contains( serverControlParams.wpBuiltinSettings, setId ) )
            return false;
          var _patterns = [ 'widget_', 'nav_menu', 'sidebars_', 'custom_css', 'nav_menu[', 'nav_menu_item', 'nav_menus_created_posts', 'nav_menu_locations' ],
              _isExcld = false;
          _.each( _patterns, function( _ptrn ) {
                switch( _ptrn ) {
                      case 'widget_' :
                      case 'sidebars_' :
                            if ( _ptrn == setId.substring( 0, _ptrn.length ) ) {
                                  _isExcld = self.isExcludedSidebarsWidgets();
                            }
                      break;

                      case 'nav_menu[' :
                      case 'nav_menu_item' :
                      case 'nav_menus_created_posts' :
                            if ( _ptrn == setId.substring( 0, _ptrn.length ) ) {
                                  _isExcld = true;
                            }
                      break;

                      case 'nav_menu_locations' :
                            if ( _ptrn == setId.substring( 0, _ptrn.length ) ) {
                                  _isExcld = self.isExcludedNavMenuLocations();
                            }
                      break;

                      case 'custom_css' :
                            if ( _ptrn == setId.substring( 0, _ptrn.length ) ) {
                                  _isExcld = self.isExcludedWPCustomCss();
                            }
                      break;


                }
          });
          return _isExcld;
    },
    isExcludedSidebarsWidgets : function() {
          var _servParam = serverControlParams.isSidebarsWigetsSkoped;//can be a boolean or a string "" for false, "1" for true
          return ! ( ! _.isUndefined( _servParam ) && ! _.isEmpty( _servParam ) && false !== _servParam );
    },
    isExcludedNavMenuLocations : function() {
          if ( ! api.czr_isChangeSetOn() )
            return true;
          var _servParam = serverControlParams.isNavMenuLocationsSkoped;//can be a boolean or a string "" for false, "1" for true
          return ! ( ! _.isUndefined( _servParam ) && ! _.isEmpty( _servParam ) && false !== _servParam );
    },
    isExcludedWPCustomCss : function() {
          var _servParam = serverControlParams.isWPCustomCssSkoped;//can be a boolean or a string "" for false, "1" for true
          return ! ( ! _.isUndefined( _servParam ) && ! _.isEmpty( _servParam ) && false !== _servParam );
    },
    _getDBSettingVal : function( setId, skope_id  ) {
          var shortSetId = api.CZR_Helpers.getOptionName(setId),
              wpSetId = api.CZR_Helpers.build_setId(setId);
          if ( ! api.czr_skope.has( skope_id ) ) {
                api.consoleLog( '_getDBSettingVal : the requested skope id is not registered : ' + skope_id );
                return '_no_db_val';
          }
          if ( _.has( api.czr_skope( skope_id ).dbValues(), wpSetId ) ) {
                return api.czr_skope( skope_id ).dbValues()[wpSetId];
          } else if ( _.has( api.czr_skope( skope_id ).dbValues(), shortSetId ) ) {
                return api.czr_skope( skope_id ).dbValues()[shortSetId];
          } else {
                return '_no_db_val';
          }
    },
    getSkopeDirties : function( skope_id, options ) {
          if ( ! api.czr_skope.has( skope_id ) )
            return {};
          options = options || {};
          options = _.extend( { unsaved : true }, options );

          var values = {};
          _.each( api.czr_skope( skope_id ).dirtyValues(), function( _val, _setId ) {
                var settingRevision;
                if ( api.czr_isChangeSetOn() ) {
                      settingRevision = api._latestSettingRevisions[ _setId ];
                      if ( api.state( 'changesetStatus' ).get() && ( options && options.unsaved ) && ( _.isUndefined( settingRevision ) || settingRevision <= api._lastSavedRevision ) ) {
                            return;
                      }
                }
                values[ _setId ] = _val;
          } );
          return values;
    },

    getSkopeExcludedDirties : function() {
          var self = this,
              _wpDirties = {};
          api.each( function ( value, setId ) {
                if ( value._dirty ) {
                  _wpDirties[ setId ] = value();
                }
          } );
          var _globalSkopeId = self.getGlobalSkopeId(),
              _globalSkpDirties = self.getSkopeDirties( _globalSkopeId );
          return _.omit( _wpDirties, function( _value, setId ) {
              return self.isSettingSkopeEligible( setId );
          } );
    },
    parseWidgetId : function( widgetId, prefixToRemove ) {
        var matches, parsed = {
          number: null,
          id_base: null
        };

        matches = widgetId.match( /^(.+)-(\d+)$/ );
        if ( matches ) {
          parsed.id_base = matches[1];
          parsed.number = parseInt( matches[2], 10 );
        } else {
          parsed.id_base = widgetId;
        }

        if ( ! _.isUndefined( prefixToRemove ) )
          parsed.id_base = parsed.id_base.replace( prefixToRemove , '');
        return parsed;
    },
    widgetIdToSettingId: function( widgetId , prefixToRemove ) {
        var parsed = this.parseWidgetId( widgetId, prefixToRemove ), settingId;

        settingId = parsed.id_base;
        if ( parsed.number ) {
          settingId += '[' + parsed.number + ']';
        }
        return settingId;
    },




    isWidgetRegisteredGlobally : function( widgetId ) {
        var self = this;
            registered = false;
        _.each( _wpCustomizeWidgetsSettings.registeredWidgets, function( _val, _short_id ) {
            if ( ! registered && 'widget_' + self.widgetIdToSettingId(_short_id) == widgetId )
              registered = true;
        } );
        return registered;
    }
});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {

    getAppliedPrioritySkopeId : function( setId, skope_id ) {
          if ( ! api.has( api.CZR_Helpers.build_setId(setId) ) ) {
              throw new Error('getAppliedPrioritySkopeId : the requested setting id does not exist in the api : ' + api.CZR_Helpers.build_setId(setId) );
          }
          if ( ! api.czr_skope.has( skope_id ) ) {
              throw new Error('getAppliedPrioritySkopeId : the requested skope id is not registered : ' + skope_id );
          }
          var self = this,
              _local_skope_id = _.findWhere( api.czr_currentSkopesCollection(), { skope : 'local' } ).id;

          if ( _.isUndefined( _local_skope_id ) || skope_id == _local_skope_id )
            return skope_id;
          var _salmonToMatch = function( _skp_id ) {
                var wpSetId = api.CZR_Helpers.build_setId( setId ),
                    val_candidate = '___',
                    skope_model = api.czr_skope( _skp_id )(),
                    initial_val;

                if ( _skp_id == skope_id )
                  return skope_id;
                if ( api.czr_skope( _skp_id ).getSkopeSettingAPIDirtyness( wpSetId ) )
                  return skope_model.id;
                if ( api.czr_isChangeSetOn() ) {
                      if ( api.czr_skope( _skp_id ).getSkopeSettingChangesetDirtyness( wpSetId ) )
                        return skope_model.id;
                }
                var _skope_db_val = self._getDBSettingVal( setId, _skp_id);
                if ( _skope_db_val != '_no_db_val' ) {
                  return skope_model.id;
                }
                else if( 'global' == skope_model.skope ) {
                  return skope_model.id;
                }
                else {
                  return '___' != val_candidate ? skope_model.title : _salmonToMatch( self._getParentSkopeId( skope_model ) );
                }
          };
          return _salmonToMatch( _local_skope_id );
    },
    getOverridenSkopeTitles : function() {
          var skope_id = skope_id || api.czr_activeSkopeId();
          if ( ! api.czr_skope.has( skope_id ) ) {
                throw new Error('getInheritedSkopeTitles : the requested skope id is not registered : ' + skope_id );
          }
          var self = this,
              _local_skope_id = _.findWhere( api.czr_currentSkopesCollection(), { skope : 'local' } ).id;

          if ( _.isUndefined( _local_skope_id ) || skope_id == _local_skope_id )
            return;
          var _salmonToMatch = function( _skp_id, _skp_ids ) {
                _skp_ids = _skp_ids || [];
                var skope_model = api.czr_skope( _skp_id )();

                if ( _skp_id == skope_id )
                  return _skp_ids;
                _skp_ids.unshift( _skp_id );
                return _salmonToMatch( self._getParentSkopeId( skope_model ), _skp_ids );
          };

          return _.map( _salmonToMatch( _local_skope_id ), function( id ) {
                return self.buildSkopeLink( id );
          }).join(' and ');//@to_translate
    },
    getInheritedSkopeId : function( setId, skope_id ) {
          if ( ! api.has( api.CZR_Helpers.build_setId(setId) ) ) {
              throw new Error('getInheritedSkopeId : the requested setting id does not exist in the api : ' + api.CZR_Helpers.build_setId(setId) );
          }
          if ( ! api.czr_skope.has( skope_id ) ) {
              throw new Error('getInheritedSkopeId : the requested skope id is not registered : ' + skope_id );
          }

          var self = this,
              wpSetId = api.CZR_Helpers.build_setId( setId ),
              val_candidate = '___',
              skope_model = api.czr_skope( skope_id )(),
              initial_val;
          if ( _.has( api.settings.settings, wpSetId ) )
            initial_val = api.settings.settings[wpSetId].value;
          else
            initial_val = null;
          if ( api.czr_skope( skope_id ).getSkopeSettingAPIDirtyness( wpSetId ) )
            return skope_id;
          if ( api.czr_isChangeSetOn() ) {
                if ( api.czr_skope( skope_id ).getSkopeSettingChangesetDirtyness( wpSetId ) )
                  return skope_id;
          }
          var _skope_db_val = self._getDBSettingVal( setId, skope_id );
          if ( _skope_db_val != '_no_db_val' )
            return skope_id;
          else if( 'global' == skope_model.skope ) {
            return skope_id;
          }
          else
            return '___' != val_candidate ?skope_id : self.getInheritedSkopeId( setId, self._getParentSkopeId( skope_model ) );
    },
    getInheritedSkopeTitles : function( skope_id, skope_ids ) {
          skope_id = skope_id || api.czr_activeSkopeId();
          if ( ! api.czr_skope.has( skope_id ) ) {
                throw new Error('getInheritedSkopeTitles : the requested skope id is not registered : ' + skope_id );
          }
          skope_ids = skope_ids || [];
          var self = this,
              skope_model = api.czr_skope( skope_id )();

          if ( skope_id !== api.czr_activeSkopeId() )
              skope_ids.unshift( skope_id );

          if ( 'global' !== skope_model.skope )
              return self.getInheritedSkopeTitles( self._getParentSkopeId( skope_model ), skope_ids );

          return _.map( skope_ids, function( id ) {
                return self.buildSkopeLink( id );
          }).join(' and ');//@to_translate
    },


    buildSkopeLink : function( skope_id ) {
          if ( ! api.czr_skope.has( skope_id ) ) {
                throw new Error('buildSkopeLink : the requested skope id is not registered : ' + skope_id );
          }
          var _link_title = "Switch to scope : " + api.czr_skope( skope_id )().title;//@to_translate
          return [
                '<span class="czr-skope-switch" title=" ' + _link_title + '" data-skope-id="' + skope_id + '">',
                api.czr_skope( skope_id )().title,
                '</span>'
          ].join( '' );
    },
    getSkopeSettingVal : function( setId, skope_id ) {
          if ( ! api.has( api.CZR_Helpers.build_setId(setId) ) ) {
              throw new Error('getSkopeSettingVal : the requested setting id does not exist in the api : ' + api.CZR_Helpers.build_setId(setId) );
          }
          if ( ! api.czr_skope.has( skope_id ) ) {
              throw new Error('getSkopeSettingVal : the requested skope id is not registered : ' + skope_id );
          }

          var self = this,
              wpSetId = api.CZR_Helpers.build_setId( setId ),
              val_candidate = '___',
              skope_model = api.czr_skope( skope_id )(),
              initial_val;
          if ( _.has( api.settings.settings, wpSetId ) )
            initial_val = api.settings.settings[wpSetId].value;
          else
            initial_val = null;
          if ( api.czr_skope( skope_id ).getSkopeSettingAPIDirtyness( wpSetId ) )
            return api.czr_skope( skope_id ).dirtyValues()[ wpSetId ];
          if ( api.czr_isChangeSetOn() ) {
                if ( api.czr_skope( skope_id ).getSkopeSettingChangesetDirtyness( wpSetId ) )
                  return api.czr_skope( skope_id ).changesetValues()[ wpSetId ];
          }
          var _skope_db_val = self._getDBSettingVal( setId, skope_id );
          if ( _skope_db_val != '_no_db_val' )
            return _skope_db_val;
          else if( 'global' == skope_model.skope ) {
            return '___' == val_candidate ? initial_val : val_candidate;
          }
          else
            return '___' != val_candidate ? val_candidate : self.getSkopeSettingVal( setId, self._getParentSkopeId( skope_model ) );
    },
    applyDirtyCustomizedInheritance : function( dirtyCustomized, skope_id ) {
          skope_id = skope_id || api.czr_activeSkopeId() || api.czr_skopeBase.getGlobalSkopeId();
          dirtyCustomized = dirtyCustomized || {};

          var self = this,
              skope_model = api.czr_skope( skope_id )();

          if ( 'global' == skope_model.skope )
            return dirtyCustomized;

          var parent_skope_id = self._getParentSkopeId( skope_model ),
              parent_dirties = api.czr_skope( parent_skope_id ).dirtyValues();
          _.each( parent_dirties, function( _val, wpSetId ){
                var shortSetId = api.CZR_Helpers.getOptionName( wpSetId );
                if ( _.isUndefined( dirtyCustomized[wpSetId] ) && _.isUndefined( api.czr_skope( skope_model.id ).dbValues()[shortSetId] ) )
                    dirtyCustomized[wpSetId] = _val;
          });
          return 'global' == api.czr_skope( parent_skope_id )().skope ? dirtyCustomized : self.applyDirtyCustomizedInheritance( dirtyCustomized, parent_skope_id );
    },
    _getParentSkopeId : function( skope_model, _index ) {
          var self = this,
              hierark = ['local', 'group', 'special_group', 'global'],
              parent_skope_ind = _index || ( _.findIndex( hierark, function( _skp ) { return skope_model.skope == _skp; } ) + 1 ) * 1,
              parent_skope_skope = hierark[ parent_skope_ind ];

          if ( _.isUndefined( parent_skope_skope ) ) {
              return _.findWhere( api.czr_currentSkopesCollection(), { skope : 'global' } ).id;
          }
          if ( _.isUndefined( _.findWhere( api.czr_currentSkopesCollection(), { skope : parent_skope_skope } ) ) ) {
              return self._getParentSkopeId( skope_model, parent_skope_ind + 1 );
          }
          return _.findWhere( api.czr_currentSkopesCollection(), { skope : parent_skope_skope } ).id;
    },
    _getChildSkopeId : function( skope_model, _index ) {
          var self = this,
              hierark = ['local', 'group', 'special_group', 'global'],
              child_skope_ind = _index || ( _.findIndex( hierark, function( _skp ) { return skope_model.skope == _skp; } ) - 1 ) * 1,
              child_skope_skope = hierark[ child_skope_ind ];

          if ( _.isUndefined( child_skope_skope ) ) {
              return _.findWhere( api.czr_currentSkopesCollection(), { skope : 'local' } ).id;
          }
          if ( _.isUndefined( _.findWhere( api.czr_currentSkopesCollection(), { skope : child_skope_skope } ) ) ) {
              return self._getParentSkopeId( skope_model, child_skope_ind - 1 );
          }
          return _.findWhere( api.czr_currentSkopesCollection(), { skope : child_skope_skope } ).id;
    }

});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    updateSkopeCollection : function( sent_collection, sent_channel ) {
          var self = this;
              _api_ready_collection = [];
          _.each( sent_collection, function( _skope, _key ) {
                var skope_candidate = $.extend( true, {}, _skope );//deep clone to avoid any shared references
                _api_ready_collection.push( self.prepareSkopeForAPI( skope_candidate ) );
          });
          if ( self.isGlobalSkopeRegistered() ) {
                var _updated_api_ready_collection = [],
                    _global_skp_model = $.extend( true, {}, api.czr_skope( self.getGlobalSkopeId() )() );

                _.each( _api_ready_collection, function( _skp, _k ) {
                      if ( 'global' == _skp.skope )
                        _updated_api_ready_collection.push( _global_skp_model );
                      else
                        _updated_api_ready_collection.push( _skp );
                });
                _api_ready_collection = _updated_api_ready_collection;
          }
          api.czr_currentSkopesCollection( _api_ready_collection );
    },



    prepareSkopeForAPI : function( skope_candidate ) {
          if ( ! _.isObject( skope_candidate ) ) {
              throw new Error('prepareSkopeForAPI : a skope must be an object to be API ready');
          }
          var self = this,
              api_ready_skope = {};

          _.each( serverControlParams.defaultSkopeModel , function( _value, _key ) {
                var _candidate_val = skope_candidate[_key];
                switch( _key ) {
                      case 'title' :
                            if ( ! _.isString( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : a skope title property must a string');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                        case 'long_title' :
                            if ( ! _.isString( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : a skope title property must a string');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case 'skope' :
                            if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : a skope "skope" property must a string not empty');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case 'level' :
                            if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : a skope level must a string not empty for skope ' + _candidate_val.skope );
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case 'dyn_type' :
                            if ( ! _.isString( _candidate_val ) || ! _.contains( serverControlParams.skopeDynTypes, _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : missing or invalid dyn type for skope ' + skope_candidate );
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case 'opt_name' :
                            if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : invalid "opt_name" property for skope ' + _candidate_val.skope );
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case 'obj_id' :
                            if ( ! _.isString( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : invalid "obj_id" for skope ' + _candidate_val.skope );
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case  'is_winner' :
                            if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareSkopeForAPI : skope property "is_winner" must be a boolean');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case  'is_forced' :
                            if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareSkopeForAPI : skope property "is_primary" must be a boolean');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case  'db' :
                            if ( _.isArray( _candidate_val ) || _.isEmpty( _candidate_val ) )
                              _candidate_val = {};
                            if ( _.isUndefined( _candidate_val) || ! _.isObject( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : skope property "db" must be an object');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case  'changeset' :
                            if ( _.isArray( _candidate_val ) || _.isEmpty( _candidate_val ) )
                              _candidate_val = {};
                            if ( _.isUndefined( _candidate_val) || ! _.isObject( _candidate_val ) ) {
                                throw new Error('prepareSkopeForAPI : skope property "changeset" must be an object');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                      case  'has_db_val' :
                            if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareSkopeForAPI : skope property "has_db_val" must be a boolean');
                            }
                            api_ready_skope[_key] = _candidate_val;
                      break;
                }//switch
          });
          api_ready_skope.color = self.skope_colors[ api_ready_skope.skope ] || 'rgb(255, 255, 255)';
          api_ready_skope.id = api_ready_skope.skope + '_' + api_ready_skope.level;
          if ( ! _.isString( api_ready_skope.id ) || _.isEmpty( api_ready_skope.id ) ) {
                throw new Error('prepareSkopeForAPI : a skope id must a string not empty');
          }
          if ( ! _.isString( api_ready_skope.title ) || _.isEmpty( api_ready_skope.title ) ) {
                api_ready_skope.title = id;
                api_ready_skope.long_title = id;
          }
          return api_ready_skope;
    },
    currentSkopesCollectionReact : function( to, from ) {
          var self = this,
              _new_collection = $.extend( true, [], to ) || [],
              _old_collection = $.extend( true, [], from ) || [],
              dfd = $.Deferred();
          var _to_instantiate = [];
          var _to_remove = [];
          var _to_update = [];
          _.each( _new_collection, function( _sent_skope ) {
                if ( ! api.czr_skope.has( _sent_skope.id  ) )
                  _to_instantiate.push( _sent_skope );
          });
          _.each( _to_instantiate, function( _skope ) {
                _skope = $.extend( true, {}, _skope );//use a cloned skop to instantiate : @todo : do we still need that ?
                api.czr_skope.add( _skope.id , new api.CZR_skope( _skope.id , _skope ) );
          });
          _.each( _to_instantiate, function( _skope ) {
                if ( ! api.czr_skope.has( _skope.id ) ) {
                    throw new Error( 'Skope id : ' + _skope.id + ' has not been instantiated.');
                }
                if ( 'pending' == api.czr_skope( _skope.id ).isReady.state() ) {
                      api.czr_skope( _skope.id ).ready();
                }
          });
          var _activeSkopeNum = _.size( _new_collection ),
              _setLayoutClass = function( _skp_instance ) {
                    var _newClasses = _skp_instance.container.attr('class').split(' ');
                    _.each( _skp_instance.container.attr('class').split(' '), function( _c ) {
                          if ( 'width-' == _c.substring( 0, 6) ) {
                                _newClasses = _.without( _newClasses, _c );
                          }
                    });
                    $.when( _skp_instance.container.attr('class', _newClasses.join(' ') ) )
                          .done( function() {
                                _skp_instance.container.addClass( 'width-' + ( Math.round( 100 / _activeSkopeNum ) ) );
                          });
              };
          api.czr_skope.each( function( _skp_instance ){
                if ( _.isUndefined( _.findWhere( _new_collection, { id : _skp_instance().id } ) ) ) {
                      _skp_instance.visible( false );
                      _skp_instance.isReady.then( function() {
                            _skp_instance.container.toggleClass( 'active-collection', false );
                      });
                }
                else {
                      _skp_instance.visible( true );
                      var _activeSkpDomPostProcess = function() {
                            _setLayoutClass( _skp_instance );
                            _skp_instance.container.toggleClass( 'active-collection', true );
                      };
                      if ( 'pending' == _skp_instance.isReady.state() ) {
                            _skp_instance.isReady.then( function() {
                                  _activeSkpDomPostProcess();
                            });
                      } else {
                            _activeSkpDomPostProcess();
                      }
                }
          } );
          if ( _.isEmpty(from) && ! _.isEmpty(to) )
            api.czr_initialSkopeCollectionPopulated.resolve();
          self.maybeSynchronizeGlobalSkope();

          return dfd.resolve( 'changed' ).promise();
    },//listenToSkopeCollection()
    maybeSynchronizeGlobalSkope : function( args ) {
          args = args || {};
          if ( ! _.isObject( args ) ) {
              throw new Error('maybeSynchronizeGlobalSkope : args must be an object');
          }
          var self = this,
              dfd = $.Deferred(),
              defaults = _.extend({
                        isGlobalReset : false,
                        isSetting : false,
                        settingIdToReset : '',
                        isSkope : false,
                        skopeIdToReset : ''
                    },
                    args
              ),
              _setIdToReset,
              shortSetId,
              defaultVal;

          if ( self.isGlobalSkopeRegistered() ) {
                var _global_skp_db_values = api.czr_skope( self.getGlobalSkopeId() ).dbValues();
                _.each( _global_skp_db_values, function( _val, setId ){
                      if ( api.has( setId ) && ! _.isEqual( api.settings.settings[setId].value, _val ) ) {
                            api.settings.settings[setId].value = _val;
                      }
                });
                if ( args.isGlobalReset && args.isSetting ) {
                      _setIdToReset = args.settingIdToReset;
                      shortSetId    = api.CZR_Helpers.getOptionName( _setIdToReset );
                      defaultVal    = serverControlParams.defaultOptionsValues[ shortSetId ];

                      if ( _.isUndefined( api.settings.settings[ _setIdToReset ] ) || _.isUndefined( defaultVal ) )
                        return;
                      if ( defaultVal != api.settings.settings[ _setIdToReset ].value ) {
                            api.settings.settings[ _setIdToReset ].value = defaultVal;
                      }
                }
                if ( args.isGlobalReset && args.isSkope ) {
                      _.each( api.settings.settings, function( _params, _setId ) {
                            if ( ! self.isThemeSetting( _setId ) )
                              return;

                            shortSetId = api.CZR_Helpers.getOptionName( _setId );
                            if ( ! _.has( serverControlParams.defaultOptionsValues, shortSetId ) )
                              return;
                            api.settings.settings[_setId].value = serverControlParams.defaultOptionsValues[ shortSetId ];
                      });
                }
          }
          return dfd.resolve().promise();
    }
});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    activeSkopeReact : function( to, from ) {
          var self = this, dfd = $.Deferred();
          if ( ! _.isUndefined(from) && api.czr_skope.has(from) )
            api.czr_skope(from).active(false);
          else if ( ! _.isUndefined(from) )
            throw new Error('listenToActiveSkope : previous scope does not exist in the collection', from );

          if ( ! _.isUndefined(to) && api.czr_skope.has(to) )
            api.czr_skope(to).active(true);
          else
            throw new Error('listenToActiveSkope : requested scope ' + to + ' does not exist in the collection');
          var _switchBack = function( _title ) {
                api.czr_activeSkopeId( self.getGlobalSkopeId() );
                api.czr_serverNotification({
                      status:'success',
                      message : [ _title , 'can only be customized site wide.' ].join(' ')
                });
                return dfd.resolve().promise();
          };
          if ( self.isExcludedSidebarsWidgets() && 'widgets' == api.czr_activePanelId() && to != self.getGlobalSkopeId() ) {
                api.czr_serverNotification({
                      status:'success',
                      message : [
                            'Widgets are created site wide.'//@to_translate
                      ].join(' ')
                });
          }
          if ( self.isExcludedWPCustomCss() && 'custom_css' == api.czr_activeSectionId() && to != self.getGlobalSkopeId() ) {
                return _switchBack( api.section( api.czr_activeSectionId() ).params.title );
          }
          if ( 'admin_sec' == api.czr_activeSectionId() && to != self.getGlobalSkopeId() ) {
                return _switchBack( api.section( api.czr_activeSectionId() ).params.title );
          }
          if ( ( 'nav_menu' == api.czr_activeSectionId().substring( 0, 'nav_menu'.length ) || 'add_menu' == api.czr_activeSectionId() ) && to != self.getGlobalSkopeId() )  {
                api.czr_serverNotification({
                      status:'success',
                      message : [
                            'Menus are created site wide.'//@to_translate
                      ].join(' ')
                });
          }
          if ( 'nav_menus' == api.czr_activePanelId() ) {
                _.each( api.panel( api.czr_activePanelId() ).sections(), function( _sec ) {
                      self.processSilentUpdates( { section_id : _sec.id, awake_if_not_active : true } );
                });
          }
          api.state('switching-skope')( true );
          self._writeCurrentSkopeTitle( to );
          api.trigger( 'czr-paint', { is_skope_switch : true } );
          if ( _.isUndefined( api.czr_activeSectionId() ) ) {
                api.state('switching-skope')( false );
                api.previewer.refresh();
                return dfd.resolve().promise();
          }
          if ( _.has( api, 'czrModulePanelState') )
            api.czrModulePanelState(false);
          var _silentUpdateCands = self._getSilentUpdateCandidates();
          if ( ! _.isUndefined( from ) ) {
            _.each( api.czr_skope( from ).dirtyValues(), function( val, _setId ) {
                  if ( ! _.contains( _silentUpdateCands, _setId ) )
                      _silentUpdateCands.push( _setId );
            } );
          }
          if ( ! _.isUndefined( to ) ) {
            _.each( api.czr_skope( to ).dirtyValues(), function( val, _setId ) {
                  if ( ! _.contains( _silentUpdateCands, _setId ) )
                      _silentUpdateCands.push( _setId );
            } );
          }
          var _debouncedProcessSilentUpdates = function() {
                self.processSilentUpdates( {
                            candidates : _silentUpdateCands,
                            section_id : null,
                            refresh : false//will be done on done()
                      })
                      .fail( function() {
                            dfd.reject();
                            api.state('switching-skope')( false );
                            throw new Error( 'Fail to process silent updates in _debouncedProcessSilentUpdates');
                      })
                      .done( function( _updatedSetIds ) {
                            api.previewer.refresh()
                                  .always( function() {
                                        api.trigger( 'skope-switched', to, from );
                                        dfd.resolve();
                                        api.state('switching-skope')( false );
                                  });
                      });
          };
          if ( _.has(api, 'czr_isModuleExpanded') && false !== api.czr_isModuleExpanded() ) {
                api.czr_isModuleExpanded().setupModuleViewStateListeners(false);
                _debouncedProcessSilentUpdates = _.debounce( _debouncedProcessSilentUpdates, 400 );
                _debouncedProcessSilentUpdates();
          } else {
                _debouncedProcessSilentUpdates();
          }
          return dfd.promise();
    },//activeSkopeReact
    _writeCurrentSkopeTitle : function( skope_id ) {
          var self = this,
              current_title = api.czr_skope( skope_id || api.czr_activeSkopeId() )().long_title,
              _buildTitleHtml = function() {
                    var _inheritedFrom = self.getInheritedSkopeTitles(),
                        _overrides = self.getOverridenSkopeTitles();

                    return $.trim( [
                          '<span class="czr-main-title"><span class="czr-toggle-title-notice fa fa-info-circle"></span>',
                          'global' == api.czr_skope( skope_id || api.czr_activeSkopeId() )().skope ? current_title : ['Customizing', current_title ].join(' '),
                          '</span>',
                          '<span class="czr-skope-inherits-from">',
                          'In this context :',//@to_translate
                          _.isEmpty( _inheritedFrom ) ? ' ' : 'inherits from',//@to_translate
                          _inheritedFrom,
                          _.isEmpty( _inheritedFrom ) ? '' : _.isEmpty( _overrides ) ? '.' : ', and',//@to_translate
                          _.isEmpty( _overrides ) ? ' ' : 'overridden by',//@to_translate
                          _overrides,
                          _.isEmpty( _overrides ) ? '' : '.',
                          '</span>'
                    ].join(' ') );
              },
              _toggle_spinner = function( visible ) {
                    if ( visible ) {
                          $('.czr-scope-switcher').find('.spinner').fadeIn();
                    } else {
                          $('.czr-scope-switcher').find('.spinner').fadeOut();
                    }
              };
          self.skopeWrapperEmbedded
                .then( function() {
                      if ( ! $('.czr-scope-switcher').find('.czr-current-skope-title').length ) {
                            $('.czr-scope-switcher').prepend(
                                  $( '<h2/>', {
                                        class : 'czr-current-skope-title',
                                        html : [
                                              '<span class="czr-skope-title">',
                                              '<span class="spinner">',
                                              _buildTitleHtml(),
                                              '</span>',
                                              '</span>'
                                        ].join('')
                                  })
                            );
                      } else {
                            $.when( $('.czr-scope-switcher').find('.czr-skope-title').fadeOut(200) ).done( function() {
                                  $(this)
                                        .html( _buildTitleHtml() )
                                        .fadeIn(200);
                            });
                      }

                      if ( _.isUndefined( api.state( 'switching-skope' ).isBound ) ) {
                            api.state('switching-skope').bind( _toggle_spinner );
                            api.state( 'switching-skope' ).isBound = true;
                      }
          });
    }
});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    processSilentUpdates : function( params ) {
          if ( _.isString( params ) )
            params = { candidates : [ params ] };
          else
            params = params || {};

          var self = this,
              defaultParams = {
                  candidates : [],
                  section_id : api.czr_activeSectionId(),
                  refresh : true,
                  awake_if_not_active : false
              },
              dfd = $.Deferred();

          params = $.extend( defaultParams, params );
          if ( _.isString( params.candidates ) ) {
            params.candidates = [ params.candidates ];
          }
          if ( _.isEmpty( params.candidates ) )
                params.candidates = self._getSilentUpdateCandidates( params.section_id, params.awake_if_not_active );
          if ( ! _.isArray( params.candidates ) ) {
                throw new Error('processSilentUpdates : the update candidates must be an array.');
          }
          if ( _.isEmpty( params.candidates ) )
            return dfd.resolve( [] ).promise();


          var _enjoyTheSilence = function() {
                self.silentlyUpdateSettings( params.candidates, params.refresh )
                      .fail( function() {
                            dfd.reject();
                      })
                      .done( function( updated_settings ) {
                            _.delay( function() {
                                  self.setupActiveSkopedControls( {
                                        section_id : params.section_id
                                  });
                            }, 1000 );
                            dfd.resolve( updated_settings );
                      });
          };
          if ( 'pending' == api.czr_skopeReady.state() ) {
                dfd.resolve( [] );
                api.czr_skopeReady.done( function() {
                      _enjoyTheSilence();
                });
          } else {
                _enjoyTheSilence();
          }

          return dfd.promise();
    },
    silentlyUpdateSettings : function( _silentUpdateCands, refresh ) {
          if ( ! api.state.has( 'silent-update-processing') )
            api.state.create( 'silent-update-processing' )( false );

          api.state( 'silent-update-processing' )(true);
          var self = this,
              _silentUpdatePromises = {},
              dfd = $.Deferred();

          refresh = _.isUndefined( refresh ) ? true : refresh;

          if ( _.isUndefined( _silentUpdateCands ) || _.isEmpty( _silentUpdateCands ) ) {
            _silentUpdateCands = self._getSilentUpdateCandidates();
          }

          if ( _.isString( _silentUpdateCands ) ) {
            _silentUpdateCands = [ _silentUpdateCands ];
          }
          _.each( _silentUpdateCands, function( setId ) {
                if ( api.control.has( setId ) &&  'czr_multi_module' == api.control(setId).params.type )
                  return;
                _silentUpdatePromises[setId] = self.getSettingUpdatePromise( setId );
          });


          var _deferred = [],
              _updatedSetIds = [];
          _.each( _silentUpdatePromises, function( _promise_ , setId ) {
                _promise_.done( function( _new_setting_val_ ) {
                      var wpSetId = api.CZR_Helpers.build_setId( setId ),
                          _skopeDirtyness = api.czr_skope( api.czr_activeSkopeId() ).getSkopeSettingDirtyness( setId );
                      if ( ! _.isEqual( api( wpSetId )(), _new_setting_val_ ) ) {
                            _updatedSetIds.push( setId );
                      }
                      api( wpSetId ).silent_set( _new_setting_val_ , _skopeDirtyness );
                });

                _deferred.push( _promise_ );
          });
          $.when.apply( null, _deferred )
          .fail( function() {
                dfd.reject();
                throw new Error( 'silentlyUpdateSettings FAILED. Candidates : ' + _silentUpdateCands );
          })
          .always( function() {
                api.state( 'silent-update-processing' )( false );
          })
          .then( function() {
                _.each( _deferred, function( prom ){
                      if ( _.isObject( prom ) && 'resolved' !== prom.state() ) {
                            throw new Error( 'a silent update promise is unresolved : ' + _silentUpdateCands );
                      }
                });
                if ( refresh && ! _.isEmpty( _updatedSetIds ) ) {
                      api.previewer.refresh()
                            .always( function() {
                                  dfd.resolve( _updatedSetIds );
                            });
                } else {
                      dfd.resolve( _updatedSetIds );
                }
          });
          return dfd.promise();
    },
    getSettingUpdatePromise : function( setId ) {
          if ( _.isUndefined( setId ) ) {
              throw new Error('getSettingUpdatePromise : the provided setId is not defined');
          }
          if ( ! api.has( api.CZR_Helpers.build_setId( setId ) ) ) {
              throw new Error('getSettingUpdatePromise : the provided wpSetId is not registered : ' + api.CZR_Helpers.build_setId( setId ) );
          }

          var self = this,
              wpSetId = api.CZR_Helpers.build_setId( setId ),
              current_setting_val = api( wpSetId )(),//typically the previous skope val
              dfd = $.Deferred(),
              _promise = false,
              skope_id = api.czr_activeSkopeId(),
              val = api.czr_skopeBase.getSkopeSettingVal( setId, skope_id );
          if ( _.isEqual( current_setting_val, val ) ) {
                return dfd.resolve( val ).promise();
          }
          if ( api.control.has( wpSetId ) ) {
                var control_type = api.control( wpSetId ).params.type,
                    _control_data = api.settings.controls[wpSetId],
                    _constructor;

                switch ( control_type ) {
                      case 'czr_cropped_image' :
                            _promise = self._getCzrCroppedImagePromise( wpSetId, _control_data );
                      break;

                      case 'czr_module' :
                            self._processCzrModuleSilentActions( wpSetId, control_type, skope_id , _control_data);
                      break;
                }//switch
          }//end if api.control.has( wpSetId )
          if ( _.has(api.settings.controls, 'header_image') && 'header_image' == wpSetId  ) {
                _promise = self._getHeaderImagePromise( wpSetId, skope_id );
          }
          if ( ! _promise || ! _.isObject( _promise ) ) {
                dfd.resolve( val );
          } else {
                _promise.always( function() {
                      dfd.resolve( val );
                });
          }

          return dfd.promise();
    },//getSettingUpdatePromise()
    _getSilentUpdateCandidates : function( section_id, awake_if_not_active ) {
          var self = this,
              SilentUpdateCands = [];
          section_id = ( _.isUndefined( section_id ) || _.isNull( section_id ) ) ? api.czr_activeSectionId() : section_id;
          if ( _.isEmpty( api.czr_activeSectionId() ) && ! awake_if_not_active ) {
                return [];
          }
          if ( _.isUndefined( section_id ) ) {
                api.consoleLog( '_getSilentUpdateCandidates : No active section provided');
                return [];
          }
          if ( ! api.section.has( section_id ) ) {
                throw new Error( '_getSilentUpdateCandidates : The section ' + section_id + ' is not registered in the API.');
          }
          var section_settings = api.CZR_Helpers.getSectionSettingIds( section_id );
          section_settings = _.filter( section_settings, function( setId ) {
              return self.isSettingSkopeEligible( setId );
          });
          _.each( section_settings, function( setId ) {
                SilentUpdateCands.push( setId );
          });

          return SilentUpdateCands;
    }

});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    _processCzrModuleSilentActions : function( wpSetId, control_type, skope_id, _control_data) {
          var _synced_control_id, _synced_control_val, _synced_control_data, _synced_control_constructor, _syncSektionModuleId,
              _synced_short_id = _.has( api.control( wpSetId ).params, 'syncCollection' ) ? api.control( wpSetId ).params.syncCollection : '',
              _shortSetId =  api.CZR_Helpers.build_setId(wpSetId),
              _val = api.czr_skopeBase.getSkopeSettingVal( _shortSetId, skope_id ),
              current_skope_instance = api.czr_skope( api.czr_activeSkopeId() );
          if ( ! _.isEmpty( _synced_short_id ) && ! _.isUndefined( _synced_short_id ) ) {
                _synced_control_id = api.CZR_Helpers.build_setId( _synced_short_id );
                _synced_control_val = api.czr_skopeBase.getSkopeSettingVal( _synced_control_id, skope_id );
                _synced_control_data = api.settings.controls[_synced_control_id];
                _synced_control_constructor = api.controlConstructor.czr_multi_module;
                _syncSektionModuleId =  api.control( _synced_control_id ).syncSektionModule()().id;
                api.control( _synced_control_id ).container.remove();
                api.control.remove(_synced_control_id );
                api( _synced_control_id ).silent_set( _synced_control_val, current_skope_instance.getSkopeSettingDirtyness( _synced_control_id ) );
                $.extend( _synced_control_data, { czr_skope : skope_id });
                api.control.add( _synced_control_id,  new _synced_control_constructor( _synced_control_id, { params : _synced_control_data, previewer : api.previewer }) );
          }

          _constructor = api.controlConstructor[control_type];
          api.control( wpSetId ).container.remove();
          api.control.remove( wpSetId );
          api( wpSetId ).silent_set( _val, current_skope_instance.getSkopeSettingDirtyness( _shortSetId ) );
          $.extend( _control_data, { czr_skope : skope_id });
          api.control.add( wpSetId,  new _constructor( wpSetId, { params : _control_data, previewer : api.previewer }) );
          if ( ! _.isEmpty( _synced_short_id ) && ! _.isUndefined( _synced_short_id ) ) {
                api.consoleLog('FIRE SEKTION MODULE?', _syncSektionModuleId, api.control( wpSetId ).czr_Module( _syncSektionModuleId ).isReady.state() );
                api.control( wpSetId ).czr_Module( _syncSektionModuleId ).fireSektionModule();
          }
    },
    _getCzrCroppedImagePromise : function( wpSetId, _control_data ) {
          var _constructor = api.controlConstructor.czr_cropped_image, dfd = $.Deferred(),
              val = api.has(wpSetId) ? api(wpSetId)() : null;
          val = null === val ? "" : val;
          wp.media.attachment( val ).fetch().done( function() {
                api.control( wpSetId ).container.remove();
                api.control.remove( wpSetId );
                _control_data.attachment = this.attributes;
                api.control.add( wpSetId,  new _constructor( wpSetId, { params : _control_data, previewer : api.previewer }) );
                dfd.resolve();
          } ).fail( function() {
                api.control( wpSetId ).container.remove();
                api.control.remove( wpSetId );
                _control_data = _.omit( _control_data, 'attachment' );
                api.control.add( wpSetId,  new _constructor( wpSetId, { params : _control_data, previewer : api.previewer }) );
                dfd.reject();
          });
          return dfd.promise();
    },
    _getHeaderImagePromise : function( wpSetId, skope_id ) {
          var dfd = $.Deferred();
          if ( ! _.has(api.settings.controls, 'header_image') || 'header_image' != wpSetId  ) {
            return dfd.resolve().promise();
          }

          var _header_constructor = api.controlConstructor.header,
              _header_control_data = $.extend( true, {}, api.settings.controls.header_image );
          header_image_data = null === api.czr_skopeBase.getSkopeSettingVal( 'header_image_data', skope_id ) ? "" : api.czr_skopeBase.getSkopeSettingVal( 'header_image_data', skope_id );

          var attachment_id;
          var _reset_header_image_crtl = function( _updated_header_control_data ) {
                _updated_header_control_data = _updated_header_control_data || _header_control_data;
                api.control( 'header_image' ).container.remove();
                api.control.remove( 'header_image' );
                api.HeaderTool.UploadsList = api.czr_HeaderTool.UploadsList;
                api.HeaderTool.DefaultsList = api.czr_HeaderTool.DefaultsList;
                api.HeaderTool.CombinedList = api.czr_HeaderTool.CombinedList;
                var _render_control = function() {
                      api.control.add( 'header_image',  new _header_constructor( 'header_image', { params : _updated_header_control_data, previewer : api.previewer }) );
                };
                _render_control = _.debounce( _render_control, 800 );
                _render_control();
          };


          if ( ! _.has( header_image_data, 'attachment_id' ) ) {
                _reset_header_image_crtl();
                dfd.resolve();
          } else {
                attachment_id = header_image_data.attachment_id;
                wp.media.attachment( attachment_id ).fetch().done( function() {
                      _header_control_data.attachment = this.attributes;
                      _reset_header_image_crtl( _header_control_data );
                      dfd.resolve();
                } ).fail( function() {
                      _header_control_data = _.omit( _header_control_data, 'attachment' );
                      api.control( 'header_image' ).container.remove();
                      api.control.remove( 'header_image' );
                      api.HeaderTool.UploadsList = api.czr_HeaderTool.UploadsList;
                      api.HeaderTool.DefaultsList = api.czr_HeaderTool.DefaultsList;
                      api.HeaderTool.CombinedList = api.czr_HeaderTool.CombinedList;
                      api.control.add( 'header_image',  new _header_constructor( 'header_image', { params : _header_control_data, previewer : api.previewer }) );
                      dfd.reject();
                });
          }//else
          return dfd.promise();
    }
});//$.extend
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    setupActiveSkopedControls : function( obj ) {
          var self = this, section_id, controls, setupParams, eligibleCtrls;
              defaultSetupParams = {
                    controls : [],
                    section_id : api.czr_activeSectionId()
              };
          setupParams = $.extend( defaultSetupParams, obj );

          if ( ! _.isObject( setupParams ) || ! _.has( setupParams, 'controls' ) || ! _.has( setupParams, 'section_id' ) ) {
                throw new Error( 'SetupControlsReset : the setupParams param must be an object with properties controls and section_id.');
          }

          section_id  = setupParams.section_id;
          controls    = setupParams.controls;
          eligibleCtrls = [];

          if ( _.isEmpty( section_id ) || ! _.isString( section_id ) ) {
                section_id = api.czr_activeSectionId();
          }
          if ( _.isEmpty( controls ) ) {
                controls = api.CZR_Helpers.getSectionControlIds( section_id  );
          }

          controls = _.isString( controls ) ? [controls] : controls;
          eligibleCtrls = _.filter( controls, function( ctrlId ) {
                var setId = api.CZR_Helpers.getControlSettingId( ctrlId );
                if ( setId && ! self.isSettingSkopeEligible( setId ) ) {
                      api.control( ctrlId ).container.addClass('czr-not-skoped');
                }
                if ( setId && self.isWPAuthorizedSetting( setId ) ) {
                      api.control( ctrlId ).container.addClass('is-wp-authorized-setting');
                }
                return setId && self.isSettingSkopeEligible( setId );
          });
          if ( 'nav_menu[' == section_id.substring( 0, 'nav_menu['.length ) )
            return;
          if ( ! _.isEmpty( controls ) ) {
                api.czr_skopeReady.then( function() {
                      $.when( self.renderControlsSingleReset( eligibleCtrls ) ).done( function() {
                            self.listenSkopedControls( controls );
                      });
                });
          }
          self.renderCtrlSkpNotIcon( controls );
    },
    listenSkopedControls : function( controls ) {
          var self = this;
          _.each( controls, function( ctrlId ) {
                if ( ! api.has( ctrlId ) || _.isUndefined( api.control( ctrlId ) ) )
                  return;
                var ctrl        = api.control( ctrlId ),
                    setId       = api.CZR_Helpers.getControlSettingId( ctrlId ),
                    shortSetId  = api.CZR_Helpers.getOptionName( setId ),
                    defaults    = {
                          hasDBVal : false,
                          isDirty : false,
                          noticeVisible : false,
                          resetVisible : false,
                          isResetting : false
                    },
                    initial_states = {};
                if ( ! _.has( ctrl, 'czr_states' ) ) {
                      ctrl.czr_states = new api.Values();
                      _.each( defaults, function( _state_val, _state_name ) {
                            ctrl.czr_states.create( _state_name );
                      });

                      self.bindControlStates( ctrl );
                }
                ctrl.czr_states( 'hasDBVal' )( api.czr_skope( api.czr_activeSkopeId() ).hasSkopeSettingDBValues( setId ) );
                ctrl.czr_states( 'isDirty' )( api.czr_skope( api.czr_activeSkopeId() ).getSkopeSettingDirtyness( setId ) );


                if ( ! _.has( ctrl, 'userEventMap' ) ) {
                      ctrl.userEventMap = [
                            {
                                  trigger   : 'click keydown',
                                  selector  : '.czr-setting-reset, .czr-cancel-button',
                                  name      : 'control_reset_warning',
                                  actions   : function() {
                                        if ( ! ctrl.czr_states('isDirty')() && ! ctrl.czr_states( 'hasDBVal' )() )
                                          return;
                                        _.each( _.without( api.CZR_Helpers.getSectionControlIds( ctrl.section() ), ctrlId ) , function( _id ) {
                                              if ( _.has( api.control(_id), 'czr_states') ) {
                                                    api.control(_id).czr_states( 'resetVisible' )( false );
                                              }
                                        });
                                        ctrl.czr_states( 'resetVisible' )( ! ctrl.czr_states( 'resetVisible' )() );
                                        if ( ctrl.czr_states( 'resetVisible' )() ) {
                                              ctrl.czr_states( 'noticeVisible' )( false );
                                        }
                                  }
                            },
                            {
                                  trigger   : 'click keydown',
                                  selector  : '.czr-control-do-reset',
                                  name      : 'control_do_reset',
                                  actions   : function() {
                                        self.doResetSetting( ctrlId );
                                  }
                            },
                            {
                                  trigger   : 'click keydown',
                                  selector  : '.czr-skope-switch',
                                  name      : 'control_skope_switch',
                                  actions   : function( params ) {
                                        var _skopeIdToSwithTo = $( params.dom_event.currentTarget, params.dom_el ).attr('data-skope-id');
                                        if ( ! _.isEmpty( _skopeIdToSwithTo ) && api.czr_skope.has( _skopeIdToSwithTo ) )
                                          api.czr_activeSkopeId( _skopeIdToSwithTo );
                                  }
                            },
                            {
                                  trigger   : 'click keydown',
                                  selector  : '.czr-toggle-notice',
                                  name      : 'control_toggle_notice',
                                  actions   : function( params ) {
                                        ctrl.czr_states( 'noticeVisible' )( ! ctrl.czr_states( 'noticeVisible' )() );
                                        if ( ctrl.czr_states( 'noticeVisible' )() ) {
                                              ctrl.czr_states( 'resetVisible' )( false );
                                        }
                                  }
                            }
                      ];
                      api.CZR_Helpers.setupDOMListeners( ctrl.userEventMap , { dom_el : ctrl.container }, self );
                }
          });
    },
    bindControlStates : function( ctrl ) {
          if ( ! api.control.has( ctrl.id ) ) {
                throw new Error( 'in bindControlStates, the provided ctrl id is not registered in the api : ' + ctrl.id );
          }
          var self = this,
              setId = api.CZR_Helpers.getControlSettingId( ctrl.id );
          ctrl.czr_states('hasDBVal').bind( function( bool ) {
                ctrl.container.toggleClass( 'has-db-val', bool );
                if ( bool ) {
                      _title = 'Reset your customized ( and published ) value';//@to_translate
                } else if ( ctrl.czr_states('isDirty')() ) {
                      _title = 'Reset your customized ( but not yet published ) value';//@to_translate
                } else {
                      _title = 'Not customized yet, nothing to reset';//@to_translate;
                }
                ctrl.container.find('.czr-setting-reset').attr( 'title', _title );
          });
          ctrl.czr_states('isDirty').bind( function( bool ) {
                ctrl.container.toggleClass( 'is-dirty', bool );
                var _title;
                if ( bool ) {
                      _title = 'Reset your customized ( but not yet published ) value';//@to_translate
                } else if ( ctrl.czr_states('hasDBVal')() ) {
                      _title = 'Reset your customized ( and published ) value';//@to_translate
                } else {
                      _title = 'Not customized yet, nothing to reset';//@to_translate;
                }
                ctrl.container.find('.czr-setting-reset').attr( 'title', _title );
          });
          ctrl.czr_states('noticeVisible').bind( function( visible ) {
                ctrl.container.toggleClass( 'czr-notice-visible', visible );
                var $noticeContainer = ctrl.getNotificationsContainerElement();
                if ( false !== $noticeContainer && false !== $noticeContainer.length ) {
                      if ( ! visible ) {
                            $.when( $noticeContainer
                                  .stop()
                                  .slideUp( 'fast', null, function() {
                                        $( this ).css( 'height', 'auto' );
                                  } ) ).done( function() {
                                        self.removeCtrlSkpNot( ctrl.id );
                                  });
                      } else {
                            self.updateCtrlSkpNot( ctrl.id );
                            $noticeContainer
                                  .stop()
                                  .slideDown( 'fast', null, function() {
                                        $( this ).css( 'height', 'auto' );
                                  } );
                      }
                }
          });
          ctrl.czr_states('resetVisible').bind( function( visible ) {
                var section_id = ctrl.section() || api.czr_activeSectionId();
                if ( visible ) {
                      $.when( self.renderControlResetWarningTmpl( ctrl.id ) ).done( function( _params ) {
                            if ( _.isEmpty( _params ) )
                              return;
                            ctrl.czr_resetDialogContainer = _params.container;
                            _params.container.slideToggle('fast');
                            if ( ! _params.is_authorized ) {
                                  _.delay( function() {
                                        $.when( ctrl.czr_resetDialogContainer.slideToggle('fast') ).done( function() {
                                              ctrl.czr_resetDialogContainer.remove();
                                        });
                                  }, 3000 );
                            }
                      });
                } else {
                      if ( _.has( ctrl, 'czr_resetDialogContainer' ) && ctrl.czr_resetDialogContainer.length )
                            $.when( ctrl.czr_resetDialogContainer.slideToggle('fast') ).done( function() {
                                  ctrl.czr_resetDialogContainer.remove();
                            });
                }
          });
    }
});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    renderControlsSingleReset : function( controls ) {
          var self = this, dfd = $.Deferred();
          if ( _.isUndefined( controls ) || _.isEmpty( controls ) ) {
                controls = api.CZR_Helpers.getSectionControlIds( api.czr_activeSectionId() );
                controls = _.filter( controls, function( _id ) {
                      var setId = api.CZR_Helpers.getControlSettingId( _id );
                      return setId && self.isSettingSkopeEligible( setId );
                });
          }

          var controlIds = _.isArray(controls) ? controls : [controls],
              render_reset_icons = function( ctrlIds ) {
                    if ( _.isEmpty( ctrlIds ) ) {
                          dfd.resolve();
                          return;
                    }
                    _.each( ctrlIds, function( _id ) {
                          api.control.when( _id, function() {
                                var ctrl  = api.control( _id ),
                                    setId = api.CZR_Helpers.getControlSettingId( _id );

                                if( $('.czr-setting-reset', ctrl.container ).length ) {
                                      dfd.resolve();
                                      return;
                                }

                                ctrl.deferred.embedded.then( function() {
                                      $.when(
                                            ctrl.container
                                                  .find('.customize-control-title').first()//was.find('.customize-control-title')
                                                  .prepend( $( '<span/>', {
                                                        class : 'czr-setting-reset fa fa-refresh',
                                                        title : ''
                                                  } ) ) )
                                      .done( function(){
                                            ctrl.container.addClass('czr-skoped');
                                            $('.czr-setting-reset', ctrl.container).fadeIn( 400 );
                                            dfd.resolve();
                                      });
                                });//then()
                          });//when()
                    });//_each
              };
          render_reset_icons = _.debounce( render_reset_icons , 200 );
          render_reset_icons( controlIds );
          return dfd.promise();
    },
    renderControlResetWarningTmpl : function( ctrlId ) {
          if ( ! api.control.has( ctrlId ) )
            return {};

          var self = this,
              ctrl = api.control( ctrlId ),
              setId = api.CZR_Helpers.getControlSettingId( ctrlId ),

              _tmpl = '',
              warning_message,
              success_message,
              isWPSetting = ( function() {
                    if ( _.contains( serverControlParams.wpBuiltinSettings, api.CZR_Helpers.getOptionName( setId ) ) )
                      return true;
                    if ( ! _.contains( serverControlParams.themeSettingList, api.CZR_Helpers.getOptionName( setId ) ) )
                      return true;
                    return false;
              })();

          if ( ctrl.czr_states( 'isDirty' )() ) {
                warning_message = [
                    'Please confirm that you want to reset your current customizations for this option in ',//@to_translate
                    api.czr_skope( api.czr_activeSkopeId() )().title,
                    '.',
                ].join('');
                success_message = 'Your customizations have been reset.';//@to_translate
          } else {
                if ( isWPSetting && 'global' == api.czr_skope( api.czr_activeSkopeId() )().skope ) {
                      warning_message = 'This WordPress setting can not be reset site wide.';//@to_translate
                } else {
                      warning_message = [
                          'Please confirm that you want to reset this option in ',//@to_translate
                          api.czr_skope( api.czr_activeSkopeId() )().title,
                          '.'
                      ].join('');//@to_translate
                      success_message = 'The options have been reset.';//@to_translate
                }
          }
          var is_authorized = ! ( isWPSetting && 'global' == api.czr_skope( api.czr_activeSkopeId() )().skope && ! ctrl.czr_states( 'isDirty' )() ),
              _tmpl_data = {
                    warning_message : warning_message,
                    success_message : success_message,
                    is_authorized : is_authorized
              };
          try {
                _tmpl =  wp.template('czr-reset-control')( _tmpl_data );
          } catch(e) {
                throw new Error('Error when parsing the the reset control template : ' + e );//@to_translate
          }

          $('.customize-control-title', ctrl.container).first().after( $( _tmpl ) );

          return { container : $( '.czr-ctrl-reset-warning', ctrl.container ), is_authorized : is_authorized };
    },
    doResetSetting : function( ctrlId ) {
          var self = this,
              setId = api.CZR_Helpers.getControlSettingId( ctrlId ),
              ctrl = api.control( ctrlId ),
              skope_id = api.czr_activeSkopeId(),
              reset_method = ctrl.czr_states( 'isDirty' )() ? '_resetControlDirtyness' : '_resetControlAPIVal',
              _setResetDialogVisibility = function( ctrl, val ) {
                    val = _.isUndefined( val ) ? false : val;//@todo why this ?
                    ctrl.czr_states( 'resetVisible' )( false );
                    ctrl.czr_states( 'isResetting' )( false);
                    ctrl.container.removeClass('czr-resetting-control');
              },
              _updateAPI = function( ctrlId ) {
                    var _silentUpdate = function() {
                              api.czr_skopeBase.processSilentUpdates( { candidates : ctrlId, refresh : false } )
                                    .fail( function() { api.consoleLog( 'Silent update failed after resetting control : ' + ctrlId ); } )
                                    .done( function() {
                                          $.when( $('.czr-crtl-reset-dialog', ctrl.container ).fadeOut('300') ).done( function() {
                                                $.when( $('.czr-reset-success', ctrl.container ).fadeIn('300') ).done( function( $_el ) {
                                                      _.delay( function() {
                                                            $.when( $_el.fadeOut('300') ).done( function() {
                                                                  _setResetDialogVisibility( ctrl );
                                                                  self.setupActiveSkopedControls( { controls : [ ctrlId ] } );
                                                                 _.delay( function() {
                                                                        ctrl.czr_states( 'noticeVisible' )(true);
                                                                  }, 300 );
                                                                  _.delay( function() {
                                                                        ctrl.czr_states( 'noticeVisible' )(false);
                                                                  }, 4000 );
                                                            });
                                                      }, 1000 );
                                                });
                                          });
                                    });
                    };
                    self[reset_method](ctrlId)
                          .done( function() {
                                api.consoleLog('REFRESH AFTER A SETTING RESET');
                                api.previewer.refresh()
                                      .fail( function( refresh_data ) {
                                            api.consoleLog('SETTING RESET REFRESH FAILED', refresh_data );
                                      })
                                      .done( function( refresh_data ) {
                                            if ( 'global' == api.czr_skope( skope_id )().skope && '_resetControlAPIVal' == reset_method ) {
                                                  var _sentSkopeCollection,
                                                      _serverGlobalDbValues = {},
                                                      _skope_opt_name = api.czr_skope( skope_id )().opt_name;

                                                  if ( ! _.isUndefined( refresh_data.skopesServerData ) && _.has( refresh_data.skopesServerData, 'czr_skopes' ) ) {
                                                        _sentSkopeCollection = refresh_data.skopesServerData.czr_skopes;
                                                        if ( _.isUndefined( _.findWhere( _sentSkopeCollection, { opt_name : _skope_opt_name } ) ) ) {
                                                              _serverGlobalDbValues = _.findWhere( _sentSkopeCollection, { opt_name : _skope_opt_name } ).db || {};
                                                        }
                                                  }
                                                  api.czr_skopeBase.maybeSynchronizeGlobalSkope( { isGlobalReset : true, isSetting : true, settingIdToReset : setId } )
                                                        .done( function() {
                                                              _silentUpdate();
                                                        });
                                            } else {
                                                  _silentUpdate();
                                            }
                                      });
                          });
              };//_updateAPI


          ctrl.czr_states( 'isResetting' )( true );
          ctrl.container.addClass('czr-resetting-control');

          api.czr_skopeReset[ ctrl.czr_states( 'isDirty' )() ? 'resetChangeset' : 'resetPublished' ](
                      { skope_id : skope_id, setId : setId, is_setting : true } )
                      .done( function( r ) {
                            _updateAPI( ctrlId );
                      })
                      .fail( function( r ) {
                              $.when( $('.czr-crtl-reset-dialog', ctrl.container ).fadeOut('300') ).done( function() {
                                    $.when( $('.czr-reset-fail', ctrl.container ).fadeIn('300') ).done( function() {
                                          $('.czr-reset-fail', ctrl.container ).append('<p>' + r + '</p>');
                                          _.delay( function() {
                                                _setResetDialogVisibility( ctrl );
                                                self.setupActiveSkopedControls( { controls : [ ctrlId ] } );
                                          }, 2000 );
                                    });
                              });
                      });

    },
    _resetControlDirtyness : function( ctrlId ) {
          var setId           = api.CZR_Helpers.getControlSettingId( ctrlId ),
              skope_instance  = api.czr_skope( api.czr_activeSkopeId() ),
              current_dirties = $.extend( true, {}, skope_instance.dirtyValues() ),
              new_dirties     = {},
              current_changeset = $.extend( true, {}, skope_instance.changesetValues() ),
              new_changeset     = {},
              dfd             = $.Deferred();

          new_dirties   = _.omit( current_dirties, setId );
          new_changeset = _.omit( current_changeset, setId );
          skope_instance.dirtyValues( new_dirties );
          skope_instance.changesetValues( new_dirties );

          return dfd.resolve().promise();
    },
    _resetControlAPIVal : function( ctrlId ) {
          var setId = api.CZR_Helpers.getControlSettingId( ctrlId ),
              current_skope_db  = api.czr_skope( api.czr_activeSkopeId() ).dbValues(),
              new_skope_db      = $.extend( true, {}, current_skope_db ),
              dfd = $.Deferred();

          if ( _.has( api.control( ctrlId ), 'czr_states') ) {
                api.control(ctrlId).czr_states( 'hasDBVal' )( false );
                api.czr_skope( api.czr_activeSkopeId() ).dbValues( _.omit( new_skope_db, setId ) );
          }
          return dfd.resolve().promise();
    }

});//$.extend()
var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
    renderCtrlSkpNotIcon : function( controlIdCandidates ) {
          var self = this,
              controlIds = _.isArray(controlIdCandidates) ? controlIdCandidates : [controlIdCandidates];

          _.each( controlIds, function( _id ) {
                api.control.when( _id, function() {
                      var ctrl = api.control( _id );
                      ctrl.deferred.embedded.then( function() {
                            if( $('.czr-toggle-notice', ctrl.container ).length )
                              return;

                            $.when( ctrl.container
                                  .find('.customize-control-title').first()//was.find('.customize-control-title')
                                  .append( $( '<span/>', {
                                        class : 'czr-toggle-notice fa fa-info-circle',
                                        title : 'Display informations about the scope of this option.'//@to_translate
                                  } ) ) )
                            .done( function(){
                                  $('.czr-toggle-notice', ctrl.container).fadeIn( 400 );
                            });
                      });

                });

          });
    },
    updateCtrlSkpNot : function( controlIdCandidates ) {
           var self = this,
              controlIds = _.isArray(controlIdCandidates) ? controlIdCandidates : [controlIdCandidates],
              _isSkoped = function( setId ) {
                    return setId && self.isSettingSkopeEligible( setId );
              },//filter only eligible ctrlIds
              _generateControlNotice = function( setId, _localSkopeId ) {
                    var _currentSkopeId         = api.czr_activeSkopeId(),
                        _inheritedFromSkopeId   = self.getInheritedSkopeId( setId, _currentSkopeId ),
                        _overridedBySkopeId     = self.getAppliedPrioritySkopeId( setId, _currentSkopeId ),
                        _html = [],
                        _isCustomized,
                        _hasDBVal;
                    if ( ! _isSkoped( setId ) ) {
                          _html.push( [
                                "This option is always customized site wide and can't be reset.",//@to_translate
                          ].join(' ') );
                          return _html.join(' | ');
                    }
                    if ( _inheritedFromSkopeId == _overridedBySkopeId && api.czr_skope.has( _inheritedFromSkopeId ) && _currentSkopeId == _inheritedFromSkopeId ) {
                          _isCustomized = ! _.isUndefined( api.czr_skope( _currentSkopeId ).dirtyValues()[setId] );
                          _hasDBVal     = ! _.isUndefined( api.czr_skope( _currentSkopeId ).dbValues()[setId] );

                          if ( _isCustomized ) {
                                if ( 'global' == api.czr_skope( _inheritedFromSkopeId )().skope ) {
                                      _html.push( [
                                            'Customized. Will be published site wide.',//@to_translate
                                      ].join(' ') );
                                } else {
                                    _html.push( [
                                          'Customized. Will be published for :',//@to_translate
                                          api.czr_skope( _inheritedFromSkopeId )().title
                                    ].join(' ') );
                                }
                          } else {
                                if ( _hasDBVal ) {
                                      if ( 'global' == api.czr_skope( _inheritedFromSkopeId )().skope ) {
                                            _html.push( [
                                                  'Customized and published site wide.',//@to_translate
                                            ].join(' ') );
                                      } else {
                                            _html.push( [
                                                  'Customized and published for :',//@to_translate
                                                  api.czr_skope( _inheritedFromSkopeId )().title
                                            ].join(' ') );
                                      }
                                } else {
                                      _html.push( 'Default website value published site wide.' );//@to_translate
                                }
                          }
                    }
                    if ( _inheritedFromSkopeId !== _currentSkopeId && api.czr_skope.has( _inheritedFromSkopeId ) ) {
                          _isCustomized = ! _.isUndefined( api.czr_skope( _inheritedFromSkopeId ).dirtyValues()[setId] );
                          _hasDBVal     = ! _.isUndefined( api.czr_skope( _inheritedFromSkopeId ).dbValues()[setId] );
                          if ( ! _isCustomized && ! _hasDBVal ) {
                                _html.push( 'Default website value' );//@to_translate
                          } else {
                                _html.push( 'Inherited from : ' + self.buildSkopeLink( _inheritedFromSkopeId ) );//@to_translate
                          }
                    }
                    if ( _overridedBySkopeId !== _currentSkopeId && api.czr_skope.has( _overridedBySkopeId ) ) {
                          _isCustomized = ! _.isUndefined( api.czr_skope( _overridedBySkopeId ).dirtyValues()[setId] );

                          _html.push( [
                                ! _isCustomized ? 'The value currently published for' : 'The value that will be published for',//@to_translate
                                api.czr_skope( _localSkopeId )().title,
                                ! _isCustomized ? 'is set in scope :' : 'is customized in scope :',//@to_translate
                                self.buildSkopeLink( _overridedBySkopeId ),
                                ! _isCustomized ? ', because it has a higher priority than this one.' : ', and will override this one once published because it has a higher priority.',//@to_translate
                          ].join(' ') );
                    }

                    return _html.join(' | ');
              };

          _.each( controlIds, function( _id ) {
                api.control.when( _id, function() {
                      var ctrl = api.control( _id ),
                          setId = api.CZR_Helpers.getControlSettingId( _id );//get the relevant setting_id for this control
                      if ( ! _.has( ctrl, 'czr_states' ) || ! ctrl.czr_states('noticeVisible')() )
                        return;

                      ctrl.deferred.embedded.then( function() {
                            var _localSkopeId = _.findWhere( api.czr_currentSkopesCollection(), { skope : 'local' } ).id,
                                $noticeContainer = ctrl.getNotificationsContainerElement();

                            if ( ! $noticeContainer || ! $noticeContainer.length || _.isUndefined( _localSkopeId ) )
                              return;

                            _html = _generateControlNotice( setId, _localSkopeId );

                            var $skopeNoticeEl = $( '.czr-skope-notice', $noticeContainer );
                            if ( $skopeNoticeEl.length ) {
                                  $skopeNoticeEl.html( _html );
                            } else {
                                  $noticeContainer.append(
                                        [ '<span class="czr-notice czr-skope-notice">', _html ,'</span>' ].join('')
                                  );
                            }
                      });
                });
          });
    },
    removeCtrlSkpNot : function( controlIdCandidates ) {
          var self = this,
              controlIds = _.isArray(controlIdCandidates) ? controlIdCandidates : [controlIdCandidates];

          _.each( controlIds, function( _id ) {
                api.control.when( _id, function() {
                      var ctrl = api.control( _id );

                      ctrl.deferred.embedded.then( function() {
                            var $noticeContainer = ctrl.getNotificationsContainerElement();

                            if ( ! $noticeContainer || ! $noticeContainer.length )
                              return;

                            var $skopeNoticeEl = $( '.czr-skope-notice', $noticeContainer );
                            if ( $skopeNoticeEl.length )
                                  $skopeNoticeEl.remove();
                      });
                });
          });
    }
});//$.extend()
var CZRSkopeSaveMths = CZRSkopeSaveMths || {};
$.extend( CZRSkopeSaveMths, {
      initialize: function() {
            var self = this;
            this.changesetStatus    = 'publish';
            this.saveBtn            = $( '#save' );
      },


      save: function( args ) {
            var self        = this,
                processing  = api.state( 'processing' ),
                submitWhenDoneProcessing,
                parent      = new api.Messenger({
                      url: api.settings.url.parent,
                      channel: 'loader',
                });//this has to be reinstantiated because not accessible from core
            self.globalSaveDeferred = $.Deferred();
            self.previewer          = api.previewer;
            self.globalSkopeId      = api.czr_skopeBase.getGlobalSkopeId();
            self.saveArgs           = args;

            if ( args && args.status ) {
                  self.changesetStatus = args.status;
            }

            if ( api.state( 'saving' )() ) {
                  self.globalSaveDeferred.reject( 'already_saving' );//@to_translate
            }
            var alwaysAfterSubmission = function( response, state ) {
                      api.state( 'saving' )( false );
                      api.state( 'processing' ).set( 0 );
                      self.saveBtn.prop( 'disabled', false );
                      if ( ! _.isUndefined( response ) && response.setting_validities ) {
                            api._handleSettingValidities( {
                                  settingValidities: response.setting_validities,
                                  focusInvalidControl: true
                            } );
                      }
                      if ( 'pending' == state ) {
                            api.czr_serverNotification( { message: response, status : 'error' } );
                      } else {
                      }
                },
                resolveSave = function( params ) {
                      var response, resolveSaveDfd = $.Deferred();
                      api.state( 'saving' )( true );
                      self.fireAllSubmission( params )
                            .always( function( _response_ ) {
                                  response = _response_.response;
                                  alwaysAfterSubmission( response , this.state() );
                            })
                            .fail( function( _response_ ) {
                                  response = _response_.response;
                                  api.consoleLog('ALL SUBMISSIONS FAILED', response );
                                  self.globalSaveDeferred.reject( response );
                                  api.trigger( 'error', response );
                                  resolveSaveDfd.resolve( _response_.hasNewMenu );
                            })
                            .done( function( _response_ ) {
                                  response = _response_.response;
                                  api.previewer.refresh( { waitSkopeSynced : true } )
                                        .fail( function( refresh_data ) {
                                              self.globalSaveDeferred.reject( self.previewer, [ response ] );
                                              api.consoleLog('SAVE REFRESH FAIL', refresh_data );
                                        })
                                        .done( function( refresh_data ) {
                                              api.previewer.send( 'saved', response );
                                              response = _.extend( { changeset_status : 'publish' },  response || {} );
                                              if ( api.czr_isChangeSetOn() ) {
                                                    var latestRevision = api._latestRevision;
                                                    api.state( 'changesetStatus' ).set( response.changeset_status );
                                                    if ( 'publish' === response.changeset_status ) {
                                                          api.each( function( setting ) {
                                                                if ( setting._dirty && ( _.isUndefined( api._latestSettingRevisions[ setting.id ] ) || api._latestSettingRevisions[ setting.id ] <= latestRevision ) ) {
                                                                      setting._dirty = false;
                                                                }
                                                          } );

                                                          api.state( 'changesetStatus' ).set( '' );
                                                          api.settings.changeset.uuid = response.next_changeset_uuid;
                                                          parent.send( 'changeset-uuid', api.settings.changeset.uuid );
                                                    }
                                              } else {
                                                    api.each( function ( value ) {
                                                          value._dirty = false;
                                                    } );
                                              }
                                              refresh_data = _.extend( {
                                                          previewer : refresh_data.previewer || self.previewer,
                                                          skopesServerData : refresh_data.skopesServerData || {},
                                                    },
                                                    refresh_data
                                              );
                                              self.reactWhenSaveDone( refresh_data.skopesServerData );
                                              self.globalSaveDeferred.resolveWith( self.previewer, [ response ] );

                                              api.trigger( 'saved', response || {} );
                                              resolveSaveDfd.resolve( _response_.hasNewMenu );
                                        });
                            });
                return resolveSaveDfd.promise();
            };//resolveSave

            if ( 0 === processing() ) {
                  resolveSave().done( function( hasNewMenu ) {
                        if ( hasNewMenu ) {
                              resolveSave( { saveGlobal :false, saveSkopes : true } );
                        }
                  } );
            } else {
                  submitWhenDoneProcessing = function () {
                        if ( 0 === processing() ) {
                              api.state.unbind( 'change', submitWhenDoneProcessing );
                              resolveSave();
                        }
                  };
                  api.state.bind( 'change', submitWhenDoneProcessing );
            }
            return self.globalSaveDeferred.promise();
      }//save
});//$.extend
var CZRSkopeSaveMths = CZRSkopeSaveMths || {};
$.extend( CZRSkopeSaveMths, {
      getSubmitPromise : function( skope_id ) {
            var self = this,
                dfd = $.Deferred(),
                submittedChanges = {};

            if ( _.isEmpty( skope_id ) || ! api.czr_skope.has( skope_id ) ) {
                  api.consoleLog( 'getSubmitPromise : no skope id requested OR skope_id not registered : ' + skope_id );
                  return dfd.resolve().promise();
            }

            var skopeObjectToSubmit = api.czr_skope( skope_id )();
            if ( ! api.czr_skope( skope_id ).dirtyness() && skope_id !== self.globalSkopeId ) {
                return dfd.resolve().promise();
            }
            _.each( api.czr_skopeBase.getSkopeDirties( skope_id ) , function( dirtyValue, settingId ) {
                  submittedChanges[ settingId ] = _.extend(
                        { value: dirtyValue }
                  );
            } );

            this.submit(
                  {
                        skope_id : skope_id,
                        customize_changeset_data : submittedChanges,//{}
                        dyn_type : skopeObjectToSubmit.dyn_type
                  })
                  .done( function(_resp) {
                        dfd.resolve( _resp );
                  } )
                  .fail( function( _resp ) {
                        api.consoleLog('GETSUBMIT FAILED PROMISE FOR SKOPE : ', skope_id, _resp );
                        dfd.reject( _resp );
                  } );

            return dfd.promise();
      },//getSubmitPromise




      submit : function( params ) {
            var self = this,
                default_params = {
                      skope_id : null,
                      the_dirties : {},
                      customize_changeset_data : {},
                      dyn_type : null,
                      opt_name : null
                },
                invalidSettings = [],
                modifiedWhileSaving = {},
                invalidControls,
                submit_dfd = $.Deferred();


            params = $.extend( default_params, params );

            if ( _.isNull( params.skope_id ) ) {
                  throw new Error( 'OVERRIDEN SAVE::submit : MISSING skope_id');
            }
            if ( _.isNull( params.the_dirties ) ) {
                  throw new Error( 'OVERRIDEN SAVE::submit : MISSING the_dirties');
            }
            if ( _.has( api, 'Notification') ) {
                  api.each( function( setting ) {
                        setting.notifications.each( function( notification ) {
                              if ( 'error' === notification.type ) {
                                    api.consoleLog('NOTIFICATION ERROR on SUBMIT SAVE' , notification );
                              }
                              if ( 'error' === notification.type && ( ! notification.data || ! notification.data.from_server ) ) {
                                    invalidSettings.push( setting.id );
                                    if ( ! settingInvalidities[ setting.id ] ) {
                                          settingInvalidities[ setting.id ] = {};
                                    }
                                    settingInvalidities[ setting.id ][ notification.code ] = notification;
                              }
                        } );
                  } );
                  invalidControls = api.findControlsForSettings( invalidSettings );
                  if ( ! _.isEmpty( invalidControls ) ) {
                        _.values( invalidControls )[0][0].focus();
                        return submit_dfd.rejectWith( self.previewer, [
                              { setting_invalidities: settingInvalidities }
                        ] ).promise();
                  }
            }
            var query_params = {
                  skope_id : params.skope_id,
                  action : 'save',
                  the_dirties : params.the_dirties,
                  dyn_type : params.dyn_type,
                  opt_name : params.opt_name
            };
            if ( api.czr_isChangeSetOn() ) {
                  $.extend( query_params, { excludeCustomizedSaved: false } );
            }
            var query = $.extend( self.previewer.query( query_params ), {
                  nonce:  self.previewer.nonce.save,
                  customize_changeset_status: self.changesetStatus,
                  customize_changeset_data : JSON.stringify( params.customize_changeset_data )
            } );
            if ( api.czr_isChangeSetOn() ) {
                  if ( self.saveArgs && self.saveArgs.date ) {
                    query.customize_changeset_date = self.saveArgs.date;
                  }
                  if ( self.saveArgs && self.saveArgs.title ) {
                    query.customize_changeset_title = self.saveArgs.title;
                  }
            }
            var request = wp.ajax.post(
                  'global' !== query.skope ? 'customize_skope_changeset_save' : 'customize_save',
                  query
            );
            self.saveBtn.prop( 'disabled', true );

            api.trigger( 'save', request );

            request.fail( function ( response ) {
                  api.consoleLog('SUBMIT REQUEST FAIL', params.skope_id, response );
                  if ( '0' === response ) {
                        response = 'not_logged_in';
                  } else if ( '-1' === response ) {
                        response = 'invalid_nonce';
                  }

                  if ( 'invalid_nonce' === response ) {
                        self.previewer.cheatin();
                  } else if ( 'not_logged_in' === response ) {
                        self.previewer.preview.iframe.hide();
                        self.previewer.login().done( function() {
                              self.previewer.save();
                              self.previewer.preview.iframe.show();
                        } );
                  }
                  api.trigger( 'error', response );
                  submit_dfd.reject( response );
            } );

            request.done( function( response ) {
                  submit_dfd.resolve( response );
            } );
            return submit_dfd.promise();
      }//submit()
});//$.extend
var CZRSkopeSaveMths = CZRSkopeSaveMths || {};
$.extend( CZRSkopeSaveMths, {
      fireAllSubmission : function( params ) {
            var self = this,
                dfd = $.Deferred(),
                skopesToSave = [],
                _recursiveCallDeferred = $.Deferred(),
                _responses_ = {},
                _promises  = [],
                failedPromises = [],
                _defaultParams = {
                    saveGlobal : true,
                    saveSkopes : true
                };
            params = $.extend( _defaultParams, params );
            _.each( api.czr_skopeCollection(), function( _skp_ ) {
                  if ( 'global' !== _skp_.skope ) {
                        skopesToSave.push( _skp_.id );
                  }
            });

            var _mayBeresolve = function( _index ) {
                  if ( ! _.isUndefined( skopesToSave[ _index + 1 ] ) || _promises.length != skopesToSave.length )
                    return;

                  if ( _.isEmpty( failedPromises ) ) {
                        _recursiveCallDeferred.resolve( _responses_ );
                  } else {
                        var _buildResponse = function() {
                                  var _failedResponse = [];
                                  _.each( failedPromises, function( _r ) {
                                        _failedResponse.push( api.czr_skopeBase.buildServerResponse( _r ) );
                                  } );
                                  return $.trim( _failedResponse.join( ' | ') );
                        };
                        _recursiveCallDeferred.reject( _buildResponse() );
                  }
                  return true;
            };
            var recursiveCall = function( _index ) {
                  _index = _index || 0;
                  if ( _.isUndefined( skopesToSave[_index] ) ) {
                        api.consoleLog( 'Undefined Skope in Save recursive call ', _index, _skopesToUpdate, _skopesToUpdate[_index] );
                        _recursiveCallDeferred.resolve( _responses_ );
                  }
                  self.getSubmitPromise( skopesToSave[ _index ] )
                        .always( function() { _promises.push( _index ); } )
                        .fail( function( response ) {
                              failedPromises.push( response );
                              api.consoleLog('RECURSIVE PUSH FAIL FOR SKOPE : ', skopesToSave[_index] );
                              if (  ! _mayBeresolve( _index ) )
                                recursiveCall( _index + 1 );
                        } )
                        .done( function( response ) {
                              response = response || {};
                              if ( _.isEmpty( _responses_ ) ) {
                                    _responses_ = response || {};
                              } else {
                                    _responses_ = $.extend( _responses_ , response );
                              }
                              if (  ! _mayBeresolve( _index ) )
                                recursiveCall( _index + 1 );
                        } );

                  return _recursiveCallDeferred.promise();
            };
            var _globalHasNewMenu = false;
            _.each( api.czr_skope('global__all_').dirtyValues(), function( _setDirtVal , _setId ) {
                  if ( 'nav_menu[' != _setId.substring( 0, 'nav_menu['.length ) )
                    return;
                  _globalHasNewMenu = true;
            } );

            var _submitGlobal = function() {
                  self.getSubmitPromise( self.globalSkopeId )
                        .fail( function( r ) {
                              api.consoleLog('GLOBAL SAVE SUBMIT FAIL', r );
                              r = api.czr_skopeBase.buildServerResponse( r );
                              dfd.reject( r );
                        })
                        .done( function( r ) {
                              if ( _.isEmpty( _responses_ ) ) {
                                    _responses_ = r || {};
                              } else {
                                    _responses_ = $.extend( _responses_ , r );
                              }
                              dfd.resolve( { response : _responses_, hasNewMenu : _globalHasNewMenu } );
                        });
            };


            if ( _globalHasNewMenu && params.saveGlobal ) {
                  _submitGlobal();
            } else {
                  if ( params.saveGlobal && params.saveSkopes ) {
                        recursiveCall()
                              .fail( function( r ) {
                                    api.consoleLog('RECURSIVE SAVE CALL FAIL', r );
                                    dfd.reject( r );
                              })
                              .done( function( r ) {
                                    self.cleanSkopeChangesetMetas().always( function() { _submitGlobal(); } );
                              });
                  } else if ( params.saveGlobal && ! params.saveSkopes ) {
                          _submitGlobal();
                  } else if ( ! params.saveGlobal && params.saveSkopes ) {
                          recursiveCall()
                              .fail( function( r ) {
                                    api.consoleLog('RECURSIVE SAVE CALL FAIL', r );
                                    dfd.reject( r );
                              })
                              .done( function( r ) {
                                    if ( _.isEmpty( _responses_ ) ) {
                                          _responses_ = r || {};
                                    } else {
                                          _responses_ = $.extend( _responses_ , r );
                                    }
                                    self.cleanSkopeChangesetMetas().always( function() {
                                          dfd.resolve( { response : _responses_, hasNewMenu : _globalHasNewMenu } );
                                    });
                              });
                  }
            }//else

            return dfd.promise();
      },//fireAllSubmissions
      cleanSkopeChangesetMetas : function() {
            var self = this,
                dfd = $.Deferred();
                _query = $.extend(
                      api.previewer.query(),
                      { nonce:  api.previewer.nonce.save }
                );
            wp.ajax.post( 'czr_clean_skope_changeset_metas_after_publish' , _query )
                  .always( function () { dfd.resolve(); })
                  .fail( function ( response ) { api.consoleLog( 'cleanSkopeChangesetMetas failed', _query, response ); })
                  .done( function( response ) { api.consoleLog( 'cleanSkopeChangesetMetas done', _query, response ); });

            return dfd.promise();
      }
});//$.extend
var CZRSkopeSaveMths = CZRSkopeSaveMths || {};
$.extend( CZRSkopeSaveMths, {
      reactWhenSaveDone : function( skopesServerData ) {
            var saved_dirties = {};
            skopesServerData = _.extend(
                {
                      czr_skopes : [],
                      isChangesetDirty : false
                },
                skopesServerData
            );
            _.each( api.czr_skopeCollection(), function( _skp_ ) {
                  saved_dirties[ _skp_.opt_name ] = api.czr_skopeBase.getSkopeDirties( _skp_.id );
                  api.czr_skope( _skp_.id ).dirtyValues( {} );
                  api.czr_skope( _skp_.id ).changesetValues( {} );
            });
            var _notSyncedSettings    = [],
                _sentSkopeCollection  = skopesServerData.czr_skopes;

            api.consoleLog('REACT WHEN SAVE DONE', saved_dirties, _sentSkopeCollection );

            _.each( saved_dirties, function( skp_data, _saved_opt_name ) {
                  _.each( skp_data, function( _val, _setId ) {
                        if ( _.isUndefined( _.findWhere( _sentSkopeCollection, { opt_name : _saved_opt_name } ) ) )
                          return;
                        if ( ! api.czr_skopeBase.isSettingSkopeEligible( _setId ) )
                          return;

                        var sent_skope_db_values  = _.findWhere( _sentSkopeCollection, { opt_name : _saved_opt_name } ).db,
                            sent_skope_level      = _.findWhere( _sentSkopeCollection, { opt_name : _saved_opt_name } ).skope,
                            wpSetId               = api.CZR_Helpers.build_setId( _setId ),
                            shortSetId            = api.CZR_Helpers.getOptionName( _setId ),
                            sent_set_val          = sent_skope_db_values[wpSetId];
                        if ( _.isUndefined( sent_set_val ) && 'global' == sent_skope_level && _val === serverControlParams.defaultOptionsValues[shortSetId] )
                          return;

                        if ( _.isUndefined( sent_set_val ) || ! _.isEqual( sent_set_val, _val ) ) {
                              _notSyncedSettings.push( { opt_name : _saved_opt_name, setId : wpSetId, server_val : sent_set_val, api_val : _val } );
                        }
                  });
            });

            if ( ! _.isEmpty( _notSyncedSettings ) ) {
                  api.consoleLog('SOME SETTINGS HAVE NOT BEEN PROPERLY SAVED : ', _notSyncedSettings );
            } else {
                  api.consoleLog('ALL RIGHT, SERVER AND API ARE SYNCHRONIZED AFTER SAVE' );
            }
            api.czr_skopeBase.maybeSynchronizeGlobalSkope();
            api.czr_skopeBase.updateCtrlSkpNot( api.CZR_Helpers.getSectionControlIds() );
      }
});//$.extend
var CZRSkopeResetMths = CZRSkopeResetMths || {};
$.extend( CZRSkopeResetMths, {
      initialize: function() {
            var self = this;
            self.previewer = api.previewer;
            api.state.create('czr-resetting')(false);
            api.state('czr-resetting').bind( function( state ) {
                  $( document.body ).toggleClass( 'czr-resetting', false !== state );
            });
      },
      resetChangeset : function( args ) {
            var dfd = $.Deferred(),
                self = this,
                processing = api.state( 'processing' ),
                submitWhenPossible,
                submit_reset,
                request,
                requestAjaxAction,
                query_params,
                query,
                defaults = {
                      is_setting  : false,
                      is_skope    : false,
                      skope_id    : api.czr_activeSkopeId() || '',
                      setId       : ''
                };

            args = _.extend( defaults, args );
            var skope_id = args.skope_id,
                setId = args.setId;

            if( ! api.czr_isChangeSetOn() )
              return dfd.resolve().promise();
            submit_reset = function( skope_id, setId ) {
                  if ( _.isUndefined( skope_id ) ) {
                      throw new Error( 'RESET: MISSING skope_id');
                  }
                  api.state( 'czr-resetting' )( true );
                  query_params = {
                        skope_id : skope_id,
                        action : 'reset'
                  };
                  query = $.extend(
                        self.previewer.query( query_params ),
                        { nonce:  self.previewer.nonce.save }
                  );
                  if ( args.is_setting ) {
                        $.extend( query , { setting_id : setId } );
                        requestAjaxAction = 'czr_changeset_setting_reset';
                  } else if ( args.is_skope ) {
                        requestAjaxAction = 'czr_changeset_skope_reset';
                  } else {
                        return dfd.reject( 'reset_ajax_action_not_specified' ).promise();
                  }

                  wp.ajax.post( requestAjaxAction , query )
                        .always( function () {
                              api.state( 'czr-resetting' )( false );
                        })
                        .fail( function ( response ) {
                              if ( '0' === response ) {
                                  response = 'not_logged_in';
                              } else if ( '-1' === response ) {
                                  response = 'invalid_nonce';
                              }

                              if ( 'invalid_nonce' === response ) {
                                  self.previewer.cheatin();
                              } else if ( 'not_logged_in' === response ) {
                                    self.previewer.preview.iframe.hide();
                                    self.previewer.login().done( function() {
                                          self.resetChangeset( args );
                                          self.previewer.preview.iframe.show();
                                    } );
                              }
                              api.consoleLog( requestAjaxAction + ' failed ', query, response );
                              response = api.czr_skopeBase.buildServerResponse( response );
                              api.trigger( 'error', response );

                              api.czr_serverNotification( { message: response, status : 'error' } );
                              dfd.reject( response );
                        })
                        .done( function( response ) {
                              dfd.resolve( response );
                        });
            };//submit_reset()

            if ( 0 === processing() && false === api.state( 'czr-resetting' )() ) {
                  submit_reset( skope_id, setId );
            } else {
                  submitWhenPossible = function () {
                        if ( 0 === processing() && false === api.state( 'czr-resetting' )() ) {
                              api.state.unbind( 'change', submitWhenPossible );
                              submit_reset( skope_id, setId );
                        }
                  };
                  api.state.bind( 'change', submitWhenPossible );
            }

            return dfd.promise();
      },
      resetPublished : function( args ) {
            var dfd = $.Deferred(),
                self = this,
                processing = api.state( 'processing' ),
                submitWhenPossible,
                submit_reset,
                request,
                requestAjaxAction,
                query_params,
                query,
                defaults = {
                      is_setting  : false,
                      is_skope    : false,
                      skope_id    : api.czr_activeSkopeId() || '',
                      setId       : ''
                };

            args = _.extend( defaults, args );
            var skope_id = args.skope_id,
                setId = args.setId;
            submit_reset = function( skope_id, setId ) {
                  if ( _.isUndefined( skope_id ) ) {
                      throw new Error( 'RESET: MISSING skope_id');
                  }
                  api.state( 'czr-resetting' )( true );
                  query_params = {
                        skope_id : skope_id,
                        action : 'reset'
                  };
                  query = $.extend(
                        self.previewer.query( query_params ),
                        { nonce:  self.previewer.nonce.save }
                  );
                  if ( args.is_setting ) {
                      $.extend( query , { setting_id : setId } );
                      requestAjaxAction = 'czr_published_setting_reset';
                  } else if ( args.is_skope ) {
                      requestAjaxAction = 'czr_published_skope_reset';
                  } else {
                      return dfd.reject( 'reset_ajax_action_not_specified' ).promise();
                  }

                  api.consoleLog('in czr_reset submit : ', skope_id, query );

                  wp.ajax.post( requestAjaxAction , query )
                        .always( function () {
                              api.state( 'czr-resetting' )( false );
                        })
                        .fail( function ( response ) {
                              if ( '0' === response ) {
                                  response = 'not_logged_in';
                              } else if ( '-1' === response ) {
                                  response = 'invalid_nonce';
                              }

                              if ( 'invalid_nonce' === response ) {
                                  self.previewer.cheatin();
                              } else if ( 'not_logged_in' === response ) {
                                    self.previewer.preview.iframe.hide();
                                    self.previewer.login().done( function() {
                                          self.resetChangeset( args );
                                          self.previewer.preview.iframe.show();
                                    } );
                              }
                              api.consoleLog( requestAjaxAction + ' failed ', query, response );
                              response = api.czr_skopeBase.buildServerResponse( response );
                              api.trigger( 'error', response );

                              api.czr_serverNotification( { message: response, status : 'error' } );
                              dfd.reject( response );
                        })
                        .done( function( response ) {
                              dfd.resolve( response );
                        });

            };//submit_reset()

            if ( 0 === processing() && false === api.state( 'czr-resetting' )() ) {
                  submit_reset( skope_id, setId );
            } else {
                  submitWhenPossible = function () {
                        if ( 0 === processing() && false === api.state( 'czr-resetting' )() ) {
                              api.state.unbind( 'change', submitWhenPossible );
                              submit_reset( skope_id, setId );
                        }
                  };
                  api.state.bind( 'change', submitWhenPossible );
            }

            return dfd.promise();
      }
});//$.extend

var CZRSkopeBaseMths = CZRSkopeBaseMths || {};
$.extend( CZRSkopeBaseMths, {
      initWidgetSidebarSpecifics : function() {
            var self = this;
            if ( ! self.isExcludedSidebarsWidgets() ) {
                api.czr_activeSkopeId.bind( function( active_skope ) {
                    self.forceSidebarDirtyRefresh( api.czr_activeSectionId(), active_skope );
                });
            }
          $( document ).bind( 'widget-added', function( e, $o ) {
              if ( self.isExcludedSidebarsWidgets() )
                  return;

              var wgtIdAttr = $o.closest('.customize-control').attr('id'),
                  wdgtSetId = api.czr_skopeBase.widgetIdToSettingId( wgtIdAttr, 'customize-control-' );
              if ( ! api.has( wdgtSetId ) ) {
                  throw new Error( 'AN ADDED WIDGET COULD NOT BE BOUND IN SKOPE. ' +  wdgtSetId);
              } else {
                  self.listenAPISettings( wdgtSetId );
              }
          });
      },


      forceSidebarDirtyRefresh : function( active_section, active_skope ) {
            var self = this;
            if ( self.isExcludedSidebarsWidgets() )
              return;
            var _save_state = api.state('saved')();
            var _debounced = function() {
                if ( api.section.has( active_section ) && "sidebar" == api.section(active_section).params.type ) {
                    var active_skope = active_skope || api.czr_activeSkopeId(),
                        related_setting_name = 'sidebars_widgets[' + api.section(active_section).params.sidebarId + ']',
                        related_setting_val = self.getSkopeSettingVal( related_setting_name, active_skope );
                    api.czr_skope( active_skope ).updateSkopeDirties( related_setting_name, related_setting_val );

                    api.previewer.refresh( { the_dirties : api.czr_skope( active_skope ).dirtyValues() } )
                          .done( function() {
                                api.state('saved')( _save_state );
                          });
                }
            };
            _debounced = _.debounce( _debounced, 500 );
            _debounced();
      }
} );//$.extend(

var CZRSkopeMths = CZRSkopeMths || {};

$.extend( CZRSkopeMths, {
    initialize: function( skope_id, constructor_options ) {
          var skope = this;
          api.Value.prototype.initialize.call( skope, null, constructor_options );

          skope.isReady = $.Deferred();
          skope.embedded = $.Deferred();
          skope.el = 'czr-scope-' + skope_id;//@todo replace with a css selector based on the scope name
          $.extend( skope, constructor_options || {} );
          skope.visible     = new api.Value( true );
          skope.winner      = new api.Value( false ); //is this skope the one that will be applied on front end in the current context?
          skope.priority    = new api.Value(); //shall this skope always win or respect the default skopes priority
          skope.active      = new api.Value( false ); //active, inactive. Are we currently customizing this skope ?
          skope.dirtyness   = new api.Value( false ); //true or false : has this skope been customized ?
          skope.skopeResetDialogVisibility = new api.Value( false );
          skope.hasDBValues = new api.Value( false );
          skope.dirtyValues = new api.Value({});//stores the current customized value.
          skope.dbValues    = new api.Value({});//stores the latest db values => will be updated on each skope synced event
          skope.changesetValues = new api.Value({});//stores the latest changeset values => will be updated on each skope synced eventsynced event
          skope.userEventMap = new api.Value( [
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-scope-switch, .czr-skp-switch-link',
                      name      : 'skope_switch',
                      actions   : function() {
                            api.czr_activeSkopeId( skope().id );
                      }
                },
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-scope-reset',
                      name      : 'skope_reset_warning',
                      actions   : 'reactOnSkopeResetUserRequest'
                }
          ]);//module.userEventMap
          skope.skopeResetDialogVisibility.bind( function( to, from ) {
                return skope.skopeResetDialogReact( to );
          }, { deferred : true } );
          skope.dirtyValues.callbacks.add(function() { return skope.dirtyValuesReact.apply(skope, arguments ); } );
          skope.changesetValues.callbacks.add(function() { return skope.changesetValuesReact.apply(skope, arguments ); } );
          skope.dbValues.callbacks.add(function() { return skope.dbValuesReact.apply(skope, arguments ); } );
          skope.callbacks.add(function() { return skope.skopeReact.apply( skope, arguments ); } );
          skope.set( _.omit( constructor_options, function( _v, _key ) {
                return _.contains( [ 'db', 'changeset', 'has_db_val' ], _key );
          } ) );
          skope.embedded
                .fail( function() {
                      throw new Error('The container of skope ' + skope().id + ' has not been embededd');
                })
                .done( function() {
                      skope.setupDOMListeners( skope.userEventMap() , { dom_el : skope.container } );
                      skope.visible.bind( function( is_visible ){
                            skope.container.toggle( is_visible );
                      });
                      skope.active.callbacks.add(function() { return skope.activeStateReact.apply(skope, arguments ); } );
                      skope.dirtyness.callbacks.add(function() { return skope.dirtynessReact.apply(skope, arguments ); } );
                      skope.hasDBValues.callbacks.add(function() { return skope.hasDBValuesReact.apply(skope, arguments ); } );
                      skope.winner.callbacks.add(function() { return skope.winnerReact.apply(skope, arguments ); } );


                      skope.dirtyness( ! _.isEmpty( constructor_options.changeset ) );
                      skope.hasDBValues( ! _.isEmpty( constructor_options.db ) );
                      skope.winner( constructor_options.is_winner );

                      skope.isReady.resolve();
                });
    },
    ready : function() {
          var skope = this;
          $.when( skope.embedSkopeDialogBox() ).done( function( $_container ){
              if ( false !== $_container.length ) {
                  $_container.css('background-color', skope.color );
                  skope.container = $_container;
                  skope.embedded.resolve( $_container );
              } else {
                  skope.embedded.reject();
              }
          });
    },
    dirtyValuesReact : function( to, from ) {
          var skope = this;
          skope.dirtyness( ! _.isEmpty( to ) );
          api.czr_dirtyness( ! _.isEmpty(to) );
          var ctrlIdDirtynessToClean = [];
          _.each( from, function( _val, _id ) {
              if ( _.has( to, _id ) )
                return;
              ctrlIdDirtynessToClean.push( _id );
          });
          if ( skope().id == api.czr_activeSkopeId() ) {
                _.each( ctrlIdDirtynessToClean , function( setId ) {
                      if ( ! _.has( api.control( setId ), 'czr_states') )
                        return;
                      api.control( setId ).czr_states( 'isDirty' )( false );
                });
                _.each( to, function( _val, _setId ) {
                      if ( ! _.has( api.control( _setId ), 'czr_states') )
                        return;
                      api.control( _setId ).czr_states( 'isDirty' )( true );
                });
          }
    },
    changesetValuesReact : function( to, from ) {
          var skope = this,
              _currentServerDirties = $.extend( true, {}, skope.dirtyValues() );
          skope.dirtyValues( $.extend( _currentServerDirties, to ) );
    },
    dbValuesReact : function( to, from ) {
          var skope = this;
          skope.hasDBValues(
                ! _.isEmpty(
                      'global' != skope().skope ?
                      to :
                      _.omit( to, function( _val, _id ) {
                            return ! api.czr_skopeBase.isThemeSetting( _id );
                      })
                )
          );
          var ctrlIdDbToReset = [];
          _.each( from, function( _val, _id ) {
              if ( _.has( to, _id ) )
                return;
              ctrlIdDbToReset.push( _id );
          });
          if ( skope().id == api.czr_activeSkopeId() ) {
                _.each( ctrlIdDbToReset , function( setId ) {
                      if ( ! _.has( api.control( setId ), 'czr_states') )
                        return;
                      api.control( setId ).czr_states( 'hasDBVal' )( false );
                });
                _.each( to, function( _val, _setId ) {
                      if ( ! _.has( api.control( _setId ), 'czr_states') )
                        return;

                      api.control( _setId ).czr_states( 'hasDBVal' )( true );
                });
          }
    },
    skopeReact : function( to, from ) {
          var skope = this,
              _current_collection = [],
              _new_collection = [];
          if ( ! api.czr_skopeBase.isSkopeRegisteredInCollection( to.id ) ) {
                _current_collection = $.extend( true, [], api.czr_skopeCollection() );
                _current_collection.push( to );
                api.czr_skopeCollection( _current_collection );
          }
          else {
                _current_collection = $.extend( true, [], api.czr_skopeCollection() );
                _new_collection = _current_collection;
                _.each( _current_collection, function( _skope, _key ) {
                    if ( _skope.id != skope().id )
                      return;
                    _new_collection[_key] = to;
                });
                api.czr_skopeCollection( _new_collection );
          }
    },
    activeStateReact : function(to, from){
          var skope = this;
          skope.container.toggleClass('inactive').toggleClass('active', to);
          $('.czr-scope-switch', skope.container).toggleClass('fa-toggle-on', to).toggleClass('fa-toggle-off', !to);
    },
    dirtynessReact : function(to, from) {
          var skope = this;
          $.when( this.container.toggleClass( 'dirty', to) ).done( function() {
              if ( to )
                $( '.czr-scope-reset', skope.container).fadeIn('slow').attr('title', [ 'Reset the current customizations for', skope().title ].join(' ') );//@to_translate
              else if ( ! skope.hasDBValues() )
                $( '.czr-scope-reset', skope.container).fadeOut('fast');
          });
    },
    hasDBValuesReact : function( to, from ) {
          var skope = this;
          $.when( skope.container.toggleClass('has-db-val', to ) ).done( function() {
              if ( to ) {
                    $( '.czr-scope-reset', skope.container)
                          .fadeIn( 'slow')
                          .attr( 'title', [
                                'global' == skope().skope ? 'Reset the theme options published site wide' : 'Reset your website published options for',//@to_translate
                                'global' == skope().skope ? '' : skope().title
                          ].join(' ') );//@to_translate
              }
              else if ( ! skope.dirtyness() ) {
                    $( '.czr-scope-reset', skope.container).fadeOut('fast');
              }
          });
    },
    winnerReact : function( is_winner ) {
          var skope = this;
          this.container.toggleClass('is_winner', is_winner );

          if ( is_winner ) {
                _.each( api.czr_currentSkopesCollection(), function( _skope ) {
                      if ( _skope.id == skope().id )
                        return;
                      var _current_model = $.extend( true, {}, _skope );
                      $.extend( _current_model, { is_winner : false } );
                      api.czr_skope( _skope.id )( _current_model );
                });
          }
    },
    updateSkopeDirties : function( setId, new_val ) {
          var skope = this,
              shortSetId = api.CZR_Helpers.getOptionName( setId );
          if ( ! api.czr_skopeBase.isSettingSkopeEligible( setId ) && 'global' != skope().skope )
            return api.czr_skope( api.czr_skopeBase.getGlobalSkopeId() ).updateSkopeDirties( setId, new_val );

          var current_dirties = $.extend( true, {}, skope.dirtyValues() ),
              _dirtyCustomized = {};

          _dirtyCustomized[ setId ] = new_val;
          skope.dirtyValues.set( $.extend( current_dirties , _dirtyCustomized ) );
          return skope.dirtyValues();
    },
    getSkopeSettingDirtyness : function( setId ) {
          var skope = this;
          return skope.getSkopeSettingAPIDirtyness( setId ) || skope.getSkopeSettingChangesetDirtyness( setId );
    },
    getSkopeSettingAPIDirtyness : function( setId ) {
          var skope = this;
          return _.has( skope.dirtyValues(), api.CZR_Helpers.build_setId( setId ) );
    },
    getSkopeSettingChangesetDirtyness : function( setId ) {
          var skope = this;
          if ( ! api.czr_isChangeSetOn() )
            return skope.getSkopeSettingAPIDirtyness( setId );
          return _.has( skope.changesetValues(), api.CZR_Helpers.build_setId( setId ) );
    },
    hasSkopeSettingDBValues : function( setId ) {
          var skope = this,
              _setId = api.CZR_Helpers.build_setId(setId);

          return ! _.isUndefined( api.czr_skope( api.czr_activeSkopeId() ).dbValues()[_setId] );
    }
  } );//$.extend(
var CZRSkopeMths = CZRSkopeMths || {};
$.extend( CZRSkopeMths, {
    embedSkopeDialogBox : function() {
          var skope = this,
              skope_model = $.extend( true, {}, skope() ),
              _tmpl = '';
          if ( ! $('#customize-header-actions').find('.czr-scope-switcher').length ) {
              throw new Error('The skope switcher wrapper is not printed, the skope can not be embedded.');
          }
          try {
            _tmpl =  wp.template('czr-skope')( _.extend( skope_model, { el : skope.el } ) );
          }
          catch(e) {
            throw new Error('Error when parsing the template of a skope' + e );
          }

          $('.czr-skopes-wrapper', '#customize-header-actions').append( $( _tmpl ) );
          return $( '.' + skope.el , '.czr-skopes-wrapper' );
    },
    renderResetWarningTmpl : function() {
          var skope = this,
              skope_model = $.extend( true, {}, skope() ),
              _tmpl = '',
              warning_message,
              success_message;

          if ( skope.dirtyness() ) {
              warning_message = [
                    'Please confirm that you want to reset your current customizations for : ',//@to_translate
                    skope().title,
                    '.'
              ].join('');
              success_message = [
                    'Your customizations have been reset for ',//@to_translate
                    skope().title,
                    '.'
              ].join('');
          } else {
              warning_message = [
                    'Please confirm that you want to reset your published customizations to defaults for : ',//@to_translate
                    skope().title,
                    '.'
              ].join('');
              success_message = [
                    'The options have been reset to defaults for ',//@to_translate
                    skope().title,
                    '.'
              ].join('');
          }

          try {
            _tmpl =  wp.template( 'czr-skope-pane' )(
                _.extend( skope_model, {
                      el : skope.el,
                      warning_message : warning_message,
                      success_message : success_message
                } )
            );
          }
          catch(e) {
            throw new Error('Error when parsing the the reset skope template : ' + e );//@to_translate
          }

          $('#customize-preview').after( $( _tmpl ) );

          return $( '#czr-skope-pane' );
    },
    getEl : function() {
          var skope = this;
          return $( skope.el, '#customize-header-actions');
    }
});//$.extend()

var CZRSkopeMths = CZRSkopeMths || {};
$.extend( CZRSkopeMths, {
    reactOnSkopeResetUserRequest : function() {
          var skope = this,
              _fireReaction = function() {
                    api.state( 'czr-resetting')( true );
                    if ( api.czr_activeSkopeId() != skope().id ) {
                          api.czr_activeSkopeId( skope().id )
                                .done( function() {
                                      skope.skopeResetDialogVisibility( ! skope.skopeResetDialogVisibility() ).done( function() {
                                            api.state( 'czr-resetting')( false );
                                      });

                                });
                    } else {
                          skope.skopeResetDialogVisibility( ! skope.skopeResetDialogVisibility() ).done( function() {
                                api.state( 'czr-resetting')( false );
                          });
                    }
              };
          if ( ( api.state( 'czr-resetting')() || 0 !== api.state( 'processing' )() ) ) {
                  api.czr_serverNotification( {
                        message: 'Slow down, you move too fast !',//@to_translate
                        status : 'success',
                        auto_collapse : true
                  });
                  return;
          }
          if ( api.czr_activeSkopeId() != skope().id && api.czr_skope( api.czr_activeSkopeId() ).skopeResetDialogVisibility() ) {
                api.czr_skope( api.czr_activeSkopeId() ).skopeResetDialogVisibility( false ).done( function() {
                      _fireReaction();
                });
          } else {
                _fireReaction();
          }
    },
    skopeResetDialogReact : function( visible ) {
          var skope = this, dfd = $.Deferred();
          skope.userResetEventMap = skope.userResetEventMap || new api.Value( [
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-scope-reset-cancel',
                      name      : 'skope_reset_cancel',
                      actions   : function() {
                          skope.skopeResetDialogVisibility( ! skope.skopeResetDialogVisibility() );
                      }
                },
                {
                      trigger   : 'click keydown',
                      selector  : '.czr-scope-do-reset',
                      name      : 'skope_do_reset',
                      actions   : 'doResetSkopeValues'
                }
            ]
          );

          if ( visible ) {
                api.czr_isResettingSkope( skope().id );
                $.when( skope.renderResetWarningTmpl() ).done( function( $_container ) {
                      skope.resetPanel = $_container;
                      skope.resetPanel.addClass( skope.dirtyness() ? 'dirty-reset' : 'db-reset' );
                      skope.setupDOMListeners( skope.userResetEventMap() , { dom_el : skope.resetPanel } );
                }).then( function() {
                      setTimeout( function() {
                            var _height = $('#customize-preview').height();
                            skope.resetPanel.css( 'line-height', _height +'px' ).css( 'height', _height + 'px' );
                            $('body').addClass('czr-skope-pane-open');
                      }, 50 );
                });
          } else {
                $.when( $('body').removeClass('czr-skope-pane-open') ).done( function() {
                      if ( _.has( skope, 'resetPanel') && false !== skope.resetPanel.length ) {
                            setTimeout( function() {
                                  skope.resetPanel.remove();
                                  api.czr_isResettingSkope( false );
                            }, 300 );
                      }
                });
          }
          _.delay( function() {
                dfd.resolve();
          }, 350 );

          return dfd.promise();
    },
    doResetSkopeValues : function() {
          var skope = this,
              skope_id = skope().id,
              reset_method = skope.dirtyness() ? '_resetSkopeDirties' : '_resetSkopeAPIValues',
              _updateAPI = function() {
                    var _silentUpdate = function() {
                          api.czr_skopeBase.processSilentUpdates( { refresh : false } )
                                .fail( function() { api.consoleLog( 'Silent update failed after resetting skope : ' + skope_id ); } )
                                .done( function() {
                                      $.when( $('.czr-reset-warning', skope.resetPanel ).fadeOut('300') ).done( function() {
                                            $.when( $('.czr-reset-success', skope.resetPanel ).fadeIn('300') ).done( function() {
                                                  _.delay( function() {
                                                        api.czr_isResettingSkope( false );
                                                        skope.skopeResetDialogVisibility( false );
                                                  }, 2000 );
                                            });
                                      });
                                });
                    };

                    skope[reset_method]()
                          .done( function() {
                                api.previewer.refresh()
                                      .fail( function( refresh_data ) {
                                            api.consoleLog('SKOPE RESET REFRESH FAILED', refresh_data );
                                      })
                                      .done( function( refresh_data ) {
                                            if ( 'global' == api.czr_skope( skope_id )().skope && '_resetSkopeAPIValues' == reset_method ) {
                                                  var _sentSkopeCollection,
                                                      _serverGlobalDbValues = {},
                                                      _skope_opt_name = api.czr_skope( skope_id )().opt_name;

                                                  if ( ! _.isUndefined( refresh_data.skopesServerData ) && _.has( refresh_data.skopesServerData, 'czr_skopes' ) ) {
                                                        _sentSkopeCollection = refresh_data.skopesServerData.czr_skopes;
                                                        if ( _.isUndefined( _.findWhere( _sentSkopeCollection, { opt_name : _skope_opt_name } ) ) ) {
                                                              _serverGlobalDbValues = _.findWhere( _sentSkopeCollection, { opt_name : _skope_opt_name } ).db || {};
                                                        }
                                                  }
                                                  api.czr_skopeBase.maybeSynchronizeGlobalSkope( { isGlobalReset : true, isSkope : true, skopeIdToReset : skope_id } )
                                                        .done( function() {
                                                              _silentUpdate();
                                                        });
                                            } else {
                                                  _silentUpdate();
                                            }
                                      });

                          });
              };//_updateAPI

          $('body').addClass('czr-resetting-skope');
          api.czr_skopeReset[ skope.dirtyness() ? 'resetChangeset' : 'resetPublished' ](
                      { skope_id : skope().id, is_skope : true } )
                      .always( function() {
                            $('body').removeClass('czr-resetting-skope');//hides the spinner
                      })
                      .done( function( r ) {
                            _updateAPI();
                      })
                      .fail( function( r ) {
                              skope.skopeResetDialogVisibility( false );
                              api.consoleLog('Skope reset failed', r );
                      });
    },
    _resetSkopeDirties : function() {
          var skope = this, dfd = $.Deferred();
          skope.dirtyValues({});
          skope.changesetValues({});
          return dfd.resolve().promise();
    },
    _resetSkopeAPIValues : function() {
          var skope = this, dfd = $.Deferred();
          skope.dbValues( {} );
          return dfd.resolve().promise();
    }
  } );//$.extend(
(function (api, $, _) {
  if ( ! serverControlParams.isSkopOn )
    return;
  api.Value.prototype.set = function( to, o ) {
        var from = this._value, dfd = $.Deferred(), self = this, _promises = [];

        to = this._setter.apply( this, arguments );
        to = this.validate( to );
        if ( null === to || _.isEqual( from, to ) ) {
          return this;
        }

        this._value = to;
        this._dirty = true;

        if ( this._deferreds ) {
              _.each( self._deferreds, function( _prom ) {
                    _promises.push( _prom.apply( null, [ to, from, o ] ) );
              });

              $.when.apply( null, _promises )
                    .fail( function() { api.consoleLog( 'A deferred callback failed in api.Value::set()'); })
                    .then( function() {
                          self.callbacks.fireWith( self, [ to, from, o ] );
                          dfd.resolveWith( self, [ to, from, o ] );
                    });
        } else {
              this.callbacks.fireWith( this, [ to, from, o ] );
              return dfd.resolveWith( self, [ to, from, o ] ).promise( self );
        }
        return dfd.promise( self );
  };
  api.Value.prototype.bind = function() {
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
  };
  api.Setting.prototype.silent_set =function( to, dirtyness ) {
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
  };
})( wp.customize , jQuery, _ );
(function (api, $, _) {

  api.bind('ready', function() {
        if ( ! serverControlParams.isSkopOn )
          return;
        var _coreQuery = api.previewer.query;
        api.previewer.query =  function( queryVars ) {
              if ( ! _.has( api, 'czr_skope') ) {
                    api.consoleLog('QUERY : SKOPE IS NOT ON. FALLING BACK ON CORE QUERY');
                    return _coreQuery.apply( this );
              }
              if ( 'pending' == api.czr_initialSkopeCollectionPopulated.state() ) {
                    api.consoleLog('QUERY : INITIAL SKOPE COLLECTION NOT POPULATED YET. FALLING BACK ON CORE QUERY');
                    return _coreQuery.apply( this );
              }
              if ( 'pending' == api.czr_isPreviewerSkopeAware.state() ) {
                    api.czr_isPreviewerSkopeAware.resolve();
              }
              if ( ! _.isObject( queryVars ) && 'resolved' == api.czr_initialSkopeCollectionPopulated.state() && 'resolved' == api.czr_initialSkopeCollectionPopulated.state() ) {
                    return _coreQuery.apply( this );
              }
              if ( _.isUndefined( queryVars.skope_id ) || ! _.isString( queryVars.skope_id ) ) {
                    queryVars.skope_id = api.czr_activeSkopeId() || api.czr_skopeBase.getGlobalSkopeId();
              }

              var globalCustomized = {},
                  skopeCustomized = {},
                  _defaults = {
                        skope_id : null,
                        action : null,
                        the_dirties : {},
                        dyn_type : null,
                        opt_name : null
                  },
                  _to_return;

              queryVars = $.extend( _defaults, queryVars );
              if ( ! _.isObject( queryVars.the_dirties ) ) {
                    api.consoleLog('QUERY PARAMS : ', queryVars );
                    throw new Error( 'QUERY DIRTIES MUST BE AN OBJECT. Requested action : ' + queryVars.action );
              }
              if ( 'pending' != api.czr_isPreviewerSkopeAware.state() && _.isNull( queryVars.skope_id ) ) {
                    api.consoleLog('QUERY PARAMS : ', queryVars );
                    throw new Error( 'OVERRIDEN QUERY : NO SKOPE ID. FALLING BACK ON CORE QUERY. Requested action : ' + queryVars.action );
              }
              if ( ! _.contains( [ null, 'refresh', 'save', 'reset', 'changeset_update' ], queryVars.action ) ) {
                    api.consoleLog('QUERY PARAMS : ', queryVars );
                    throw new Error( 'A REQUESTED QUERY HAS NO AUTHORIZED ACTION. Requested action : ' + queryVars.action );
              }
              var _getSkopesCustomized = function() {
                    if ( 'pending' == api.czr_initialSkopeCollectionPopulated.state() )
                      return {};
                    var _skpCust = {};
                    _.each( api.czr_currentSkopesCollection(), function( _skp ) {
                          if ( 'global' == _skp.skope )
                            return;
                          _skpCust[_skp.id] = api.czr_skopeBase.getSkopeDirties( _skp.id );
                    } );
                    return _skpCust;
              };
              if ( _.isNull( queryVars.the_dirties ) || _.isEmpty( queryVars.the_dirties ) ) {
                    globalCustomized = api.dirtyValues( { unsaved:  queryVars.excludeCustomizedSaved || false } );
                    skopeCustomized = _getSkopesCustomized();
              } else {
                    if ( 'global' == api.czr_skopeBase.getActiveSkopeName() )
                      globalCustomized = queryVars.the_dirties;
                    else
                      skopeCustomized[ api.czr_activeSkopeId() ] = queryVars.the_dirties;
              }
              switch( queryVars.action ) {
                    case null :
                    case 'refresh' :
                    break;

                    case 'changeset_update' :
                          if ( _.isUndefined( queryVars.opt_name ) ) {
                                throw new Error('Missing opt_name param in the changeset_update query for skope : ' + queryVars.skope_id );
                          }
                    break;


                    case 'save' :
                          if ( _.isNull( queryVars.dyn_type ) )
                                queryVars.dyn_type = api.czr_skope( queryVars.skope_id )().dyn_type;//post_meta, term_meta, user_meta, trans, option
                          if ( _.isNull( queryVars.dyn_type ) || _.isUndefined( queryVars.dyn_type ) ) {
                                throw new Error( 'QUERY : A SAVE QUERY MUST HAVE A VALID DYN TYPE.' + queryVars.skope_id );
                          }
                    break;

                    case 'reset' :
                          if ( _.isNull( queryVars.dyn_type ) )
                                queryVars.dyn_type = api.czr_skope( queryVars.skope_id )().dyn_type;//post_meta, term_meta, user_meta, trans, option
                          if ( _.isNull( queryVars.dyn_type ) || _.isUndefined( queryVars.dyn_type ) ) {
                                throw new Error( 'QUERY : A RESET QUERY MUST HAVE A VALID DYN TYPE.' + queryVars.skope_id );
                          }
                    break;
              }
              var _current_skopes = {};
              _.each( api.czr_currentSkopesCollection(), function( _skp ) {
                    _current_skopes[_skp.skope] = { id : _skp.id, opt_name : _skp.opt_name };
              });
              _to_return = {
                    wp_customize: 'on',
                    customized:      '{}' == JSON.stringify( globalCustomized ) ? '{\"__not_customized__\"}' : JSON.stringify( globalCustomized ),
                    skopeCustomized:  JSON.stringify( skopeCustomized ),
                    nonce:            this.nonce.preview,
                    skope:            api.czr_skope( queryVars.skope_id )().skope,
                    level_id:          api.czr_skope( queryVars.skope_id )().level,
                    skope_id:         queryVars.skope_id,
                    dyn_type:         queryVars.dyn_type,
                    opt_name:         ! _.isNull( queryVars.opt_name ) ? queryVars.opt_name : api.czr_skope( queryVars.skope_id )().opt_name,
                    obj_id:           api.czr_skope( queryVars.skope_id )().obj_id,
                    current_skopes:   JSON.stringify( _current_skopes ) || {},
                    channel:          this.channel(),
                    revisionIndex:    api._latestRevision
              };
              if ( api.czr_isChangeSetOn() ) {
                    _to_return = $.extend( _to_return , {
                          customize_theme: api.settings.theme.stylesheet,
                          customize_changeset_uuid: api.settings.changeset.uuid
                    });
              }
              else {
                    _to_return = $.extend( _to_return , {
                          theme: api.settings.theme.stylesheet
                    });
              }
              return _to_return;

        };//api.previewer.query
  });//api.bind('ready')

})( wp.customize , jQuery, _ );
(function (api, $, _) {
      api.bind( 'czr-skope-started', function() {
            api.previewer.save = function( args ) {
                  return api.czr_skopeSave.save();
            };

      });//api.bind('ready')
})( wp.customize , jQuery, _ );
(function (api, $, _) {

  if ( ! serverControlParams.isSkopOn )
    return;
  api.Element.synchronizer.checkbox.update = function( to ) {
        this.element.prop( 'checked', to );
        this.element.iCheck('update');
  };

  var _original = api.Element.synchronizer.val.update;
  api.Element.synchronizer.val.update = function(to) {
        var self = this,
            _modifySynchronizer = function() {
                  if ( self.element.is('select') ) {
                        self.element.val(to).trigger('change');
                  } else if ( self.element.hasClass('wp-color-picker') ) {
                        self.element.val(to).trigger('change');
                  }
                  else {
                        self.element.val( to );
                  }
            };
        if ( serverControlParams.isSkopOn ) {
              if ( 'pending' == api.czr_skopeReady.state() ) {
                    return _original.call( self, to );
              } else {
                    api.czr_skopeReady.then( function () {
                          _modifySynchronizer();
                    });
              }
        } else {
              _modifySynchronizer();
        }
  };

  api.Element.synchronizer.val.refresh = function() {
        var syncApiInstance = this;
        if ( this.element.is('select') && _.isNull( this.element.val() ) ) {
              if ( _.isArray( syncApiInstance() ) )
                return [];
              else if ( _.isObject( syncApiInstance() ) )
                return {};
              else
                return '';
        } else {
              return  this.element.val();
        }
  };
})( wp.customize , jQuery, _ );
(function (api, $, _) {
    var coreRefresh = api.Previewer.prototype.refresh;
    var _new_refresh = function( params ) {
          params = _.extend({
                      waitSkopeSynced : true,
                      the_dirties : {}
                },
                params
          );

          if ( ! _.has( api, 'czr_activeSkopeId') || _.isUndefined( api.czr_activeSkopeId() ) ) {
                api.consoleLog( 'The api.czr_activeSkopeId() is undefined in the api.previewer._new_refresh() method.');
          }
          var previewer = this, dfd = $.Deferred();
          if ( ! _.has( api, 'czr_activeSkopeId') ) {
                if ( 'pending' == api.czr_skopeReady.state() ) {
                      api.czr_skopeReady.done( function() {
                            _new_refresh.apply( api.previewer, params );
                      });
                      coreRefresh.apply( previewer );
                      return dfd.resolve().promise();
                }
          }
          previewer.send( 'loading-initiated' );

          previewer.abort();

          var query_params = api.czr_getSkopeQueryParams({
                    skope_id : api.czr_activeSkopeId(),
                    action : 'refresh',
                    the_dirties : params.the_dirties || {}
              });

          previewer.loading = new api.PreviewFrame({
                url:        previewer.url(),
                previewUrl: previewer.previewUrl(),
                query:      previewer.query( query_params ) || {},
                container:  previewer.container,
                signature:  'WP_CUSTOMIZER_SIGNATURE'//will be deprecated in 4.7
          });


          previewer.settingsModifiedWhileLoading = {};
          onSettingChange = function( setting ) {
                previewer.settingsModifiedWhileLoading[ setting.id ] = true;
          };
          api.bind( 'change', onSettingChange );

          previewer.loading.always( function() {
                api.unbind( 'change', onSettingChange );
          } );
          if ( ! api.czr_isChangeSetOn() ) {
                previewer._previousPreview = previewer._previousPreview || previewer.preview;
          }

          previewer.loading.done( function( readyData ) {
                var loadingFrame = this, onceSynced;

                previewer.preview = loadingFrame;
                previewer.targetWindow( loadingFrame.targetWindow() );
                previewer.channel( loadingFrame.channel() );
                onceSynced = function( skopesServerData ) {
                      loadingFrame.unbind( 'synced', onceSynced );
                      loadingFrame.unbind( 'czr-skopes-synced', onceSynced );

                      if ( previewer._previousPreview ) {
                            previewer._previousPreview.destroy();
                      } //before WP 4.7
                      else {
                          if ( previewer.preview )
                            previewer.preview.destroy();
                      }

                      previewer._previousPreview = previewer.preview;
                      previewer.deferred.active.resolve();
                      delete previewer.loading;

                      api.trigger( 'pre_refresh_done', { previewer : previewer, skopesServerData : skopesServerData || {} } );
                      dfd.resolve( { previewer : previewer, skopesServerData : skopesServerData || {} } );
                };
                if ( ! api.czr_isChangeSetOn() ) {
                    previewer.send( 'sync', {
                          scroll:   previewer.scroll,
                          settings: api.get()
                    });
                }

                if ( params.waitSkopeSynced ) {
                      loadingFrame.bind( 'czr-skopes-synced', onceSynced );
                } else {
                      loadingFrame.bind( 'synced', onceSynced );
                }
                previewer.trigger( 'ready', readyData );
          });
          previewer.loading.fail( function( reason, location ) {
                api.consoleLog('LOADING FAILED : ' , arguments );
                previewer.send( 'loading-failed' );
                if ( ! api.czr_isChangeSetOn() ) {
                    if ( 'redirect' === reason && location ) {
                          previewer.previewUrl( location );
                    }
                }

                if ( 'logged out' === reason ) {
                      if ( previewer.preview ) {
                            previewer.preview.destroy();
                            delete previewer.preview;
                      }

                      previewer.login().done( previewer.refresh );
                }

                if ( 'cheatin' === reason ) {
                      previewer.cheatin();
                }
                dfd.reject( reason );
          });

          return dfd.promise();
    };//_new_refresh()
    api.bind( 'czr-skope-started' , function() {
          czr_override_refresh_for_skope();
          api.Previewer.prototype.refresh = _new_refresh;
    });
    api.czr_getSkopeQueryParams = function( params ) {
          if ( ! api.czr_isChangeSetOn() )
            return params;
          params = ! _.isObject(params) ? {} : params;
          var _action = params.action || 'refresh';
          switch( _action ) {
                case 'refresh' :
                    params = $.extend( params, { excludeCustomizedSaved: true } );
                break;
          }
          return params;
    };
    czr_override_refresh_for_skope = function() {
          if ( ! serverControlParams.isSkopOn )
            return;
          api.previewer.refresh = function( _params_ ) {
                var dfd = $.Deferred();
                var _refresh_ = function( params ) {
                      var refreshOnceProcessingComplete,
                          isProcessingComplete = function() {
                            return 0 === api.state( 'processing' ).get();
                          },
                          resolveRefresh = function() {
                                _new_refresh.call( api.previewer, params ).done( function( refresh_data ) {
                                      dfd.resolve( refresh_data );
                                });
                          };
                      if ( isProcessingComplete() ) {
                            resolveRefresh();
                      } else {
                            refreshOnceProcessingComplete = function() {
                                  if ( isProcessingComplete() ) {
                                        resolveRefresh();
                                        api.state( 'processing' ).unbind( refreshOnceProcessingComplete );
                                  }
                            };
                            api.state( 'processing' ).bind( refreshOnceProcessingComplete );
                      }
                };
                _refresh_ = _.debounce( _refresh_, api.previewer.refreshBuffer );
                _refresh_( _params_ );
                return dfd.promise();
          };

  };//czr_override_refresh_for_skope

})( wp.customize , jQuery, _ );
(function (api, $, _) {
      if ( ! serverControlParams.isSkopOn )
        return;
      api.dirtyValues = function dirtyValues( options ) {
            return api.czr_skopeBase.getSkopeDirties( api.czr_skopeBase.getGlobalSkopeId(), options );
      };

})( wp.customize , jQuery, _ );
(function (api, $, _) {
      if ( ! serverControlParams.isSkopOn || ! api.czr_isChangeSetOn() )
        return;
      var _original_requestChangesetUpdate = api.requestChangesetUpdate;
      api.requestChangesetUpdate = function( changes ) {
            var self = this,
                dfd = $.Deferred(),
                data,
                _skopesToUpdate = [],
                _promises = [],
                _global_skope_changes = changes || {},
                failedPromises = [],
                _all_skopes_data_ = [],
                _recursiveCallDeferred = $.Deferred();
            if ( 0 === api._lastSavedRevision || _.isEmpty( api.state( 'changesetStatus' )() ) ) {
                  _global_skope_changes = _.extend( _global_skope_changes, {
                        blogname : { dummy_change : 'dummy_change' }
                  } );
            }
            _.each( api.czr_currentSkopesCollection(), function( _skp ) {
                  if ( 'global' == _skp.skope )
                    return;
                  _skopesToUpdate.push( _skp.id );
            } );

            var _mayBeresolve = function( _index ) {
                  if ( ! _.isUndefined( _skopesToUpdate[ _index + 1 ] ) || _promises.length != _skopesToUpdate.length )
                    return;

                  if ( _.isEmpty( failedPromises ) ) {
                        _recursiveCallDeferred.resolve( _all_skopes_data_ );
                  } else {
                        var _buildResponse = function() {
                                  var _failedResponse = [];
                                  _.each( failedPromises, function( _r ) {
                                        _failedResponse.push( api.czr_skopeBase.buildServerResponse( _r ) );
                                  } );
                                  return $.trim( _failedResponse.join( ' | ') );
                        };
                        _recursiveCallDeferred.reject( _buildResponse() );
                  }
                  return true;
            };
            var recursiveCall = function( _index ) {
                  if ( _.isUndefined( _index ) || ( ( 0 * 0 ) == _index ) ) {
                      api.state( 'processing' ).set( 1 );
                  }

                  _index = _index || 0;
                  if ( _.isUndefined( _skopesToUpdate[_index] ) ) {
                        api.consoleLog( 'Undefined Skope in changeset recursive call ', _index, _skopesToUpdate, _skopesToUpdate[_index] );
                        return _recursiveCallDeferred.resolve( _all_skopes_data_ ).promise();
                  }
                  api._requestSkopeChangetsetUpdate( changes, _skopesToUpdate[_index] )
                        .always( function() { _promises.push( _index ); } )
                        .fail( function( response ) {
                              failedPromises.push( response );
                              api.consoleLog('CHANGESET UPDATE RECURSIVE FAIL FOR SKOPE : ', _skopesToUpdate[_index] );
                              if (  ! _mayBeresolve( _index ) )
                                recursiveCall( _index + 1 );
                        } )
                        .done( function( _skope_data_ ) {
                              _all_skopes_data_.push( _skope_data_ );
                              if (  ! _mayBeresolve( _index ) )
                                recursiveCall( _index + 1 );
                        } );

                  return _recursiveCallDeferred.promise();
            };
            var _lastSavedRevisionBefore = api._lastSavedRevision;
            _original_requestChangesetUpdate( _global_skope_changes )
                  .fail( function( r ) {
                        api.consoleLog( 'WP requestChangesetUpdateFail', r, api.czr_skopeBase.buildServerResponse(r) );
                        api._lastSavedRevision = Math.max( api._latestRevision, api._lastSavedRevision );
                        api.state( 'processing' ).set( 0 );

                        dfd.reject( r );
                        r = api.czr_skopeBase.buildServerResponse(r);
                        api.czr_serverNotification( { message: r, status : 'error' } );
                  })
                  .done( function( wp_original_response ) {
                        if ( 'pending' == api.czr_initialSkopeCollectionPopulated.state() )
                          dfd.resolve( wp_original_response );

                        api._lastSavedRevision = _lastSavedRevisionBefore;
                        recursiveCall()
                              .always( function() {
                                    api._lastSavedRevision = Math.max( api._latestRevision, api._lastSavedRevision );
                                    api.state( 'processing' ).set( 0 );
                              })
                              .fail( function( r ) {
                                    dfd.reject( r );
                                    api.consoleLog( 'CHANGESET UPDATE RECURSIVE PUSH FAIL', r , _all_skopes_data_ );
                                    api.trigger( 'changeset-error', r );
                                    api.czr_serverNotification( { message: r, status : 'error' } );
                              } )
                              .done( function() {
                                    dfd.resolve( wp_original_response );
                              });
                  });

            return dfd.promise();
      };
      api._requestSkopeChangetsetUpdate = function( changes, skope_id ) {
            if ( _.isUndefined( skope_id ) || ! api.czr_skope.has( skope_id ) ) {
                  throw new Error( 'In api._requestSkopeChangetsetUpdate() : a valid and registered skope_id must be provided' );
            }

            var deferred = new $.Deferred(),
                request,
                submittedChanges = {},
                data;
            skope_id = skope_id || api.czr_activeSkopeId();

            if ( changes ) {
                  _.extend( submittedChanges, changes );
            }
            _.each( api.czr_skopeBase.getSkopeDirties( skope_id ) , function( dirtyValue, settingId ) {
                  if ( ! changes || null !== changes[ settingId ] ) {
                        submittedChanges[ settingId ] = _.extend(
                              {},
                              submittedChanges[ settingId ] || {},
                              { value: dirtyValue }
                        );
                  }
            } );
            if ( _.isEmpty( submittedChanges ) ) {
                  deferred.resolve( {} );
                  return deferred.promise();
            }

            if ( api._latestRevision <= api._lastSavedRevision ) {
                  deferred.resolve( {} );
                  return deferred.promise();
            }
            api.trigger( 'skope-changeset-save', submittedChanges );

            var queryVars = {
                  skope_id : skope_id,
                  action : 'changeset_update',
                  opt_name : api.czr_skope( skope_id ).opt_name
            };
            data = api.previewer.query( _.extend( queryVars, { excludeCustomizedSaved: true } ) );
            delete data.customized; // Being sent in customize_changeset_data instead.
            _.extend( data, {
                  nonce: api.settings.nonce.save,
                  customize_changeset_data: JSON.stringify( submittedChanges )
            } );
            wp.ajax.post( 'customize_skope_changeset_save', data )
                  .done( function requestChangesetUpdateDone( _data_ ) {
                        deferred.resolve( _data_ );
                  } )
                  .fail( function requestChangesetUpdateFail( _data_ ) {
                        api.consoleLog('SKOPE CHANGESET FAIL FOR SKOPE ' + _data_.skope_id, _data_ );
                        deferred.reject( _data_ );
                  } )
                  .always( function( _data_ ) {
                        if ( _data_.setting_validities ) {
                              api._handleSettingValidities( {
                                    settingValidities: _data_.setting_validities
                              } );
                        }
                  } );

            return deferred.promise();
      };

})( wp.customize , jQuery, _ );
(function (api, $, _) {
  if ( serverControlParams.isSkopOn ) {
        api.Setting.prototype.preview = function( to, from , data ) {
              var setting = this, transport;
                  transport = setting.transport;

              if ( _.has( api, 'czr_isPreviewerSkopeAware' ) && 'pending' == api.czr_isPreviewerSkopeAware.state() )
                this.previewer.refresh();
              if ( _.isObject( data ) && true === data.not_preview_sent ) {
                    return;
              }

              if ( ! _.has( data, 'silent' ) || false === data.silent ) {
                    if ( 'postMessage' === transport && ! api.state( 'previewerAlive' ).get() ) {
                          transport = 'refresh';
                    }

                    if ( 'postMessage' === transport ) {
                          setting.previewer.send( 'setting', [ setting.id, setting() ] );
                    } else if ( 'refresh' === transport ) {
                          setting.previewer.refresh();
                    }
              }
        };
  }

})( wp.customize , jQuery, _ );
(function (api, $, _) {
  if ( 'function' == typeof api.Section ) {
    var _original_section_initialize = api.Section.prototype.initialize;
    api.Section.prototype.initialize = function( id, options ) {
          _original_section_initialize.apply( this, [id, options] );
          var section = this;

          this.expanded.callbacks.add( function( _expanded ) {
            if ( ! _expanded )
              return;

          var container = section.container.closest( '.wp-full-overlay-sidebar-content' ),
                content = section.container.find( '.accordion-section-content' );
            _resizeContentHeight = function() {
              content.css( 'height', container.innerHeight() );
          };
            _resizeContentHeight();
            $( window ).on( 'resize.customizer-section', _.debounce( _resizeContentHeight, 110 ) );
          });
        };
  }
})( wp.customize , jQuery, _ );
(function (api, $, _) {
  api.CZR_Helpers = api.CZR_Helpers || {};
  api.CZR_Helpers = $.extend( api.CZR_Helpers, {
        getControlSettingId : function( control_id, setting_type ) {
              setting_type = 'default' || setting_type;
              if ( ! api.control.has( control_id ) ) {
                    throw new Error( 'The requested control_id is not registered in the api yet : ' + control_id );
              }
              if ( ! _.has( api.control( control_id ), 'settings' ) || _.isEmpty( api.control( control_id ).settings ) )
                return;

              if ( ! _.has( api.control( control_id ).settings, setting_type ) ) {
                    throw new Error( 'The requested control_id does not have the requested setting type : ' + control_id + ' , ' + setting_type );
              }
              if ( _.isUndefined( api.control( control_id ).settings[setting_type].id ) ) {
                    throw new Error( 'The requested control_id has no setting id assigned : ' + control_id );
              }
              return api.control( control_id ).settings[setting_type].id;
        },



        getDocSearchLink : function( text ) {
              text = ! _.isString(text) ? '' : text;
              var _searchtext = text.replace( / /g, '+'),
                  _url = [ serverControlParams.docURL, 'search?query=', _searchtext ].join('');
              return [
                '<a href="' + _url + '" title="' + serverControlParams.translatedStrings.readDocumentation + '" target="_blank">',
                ' ',
                '<span class="fa fa-question-circle-o"></span>'
              ].join('');
        },
        build_setId : function ( setId ) {
              if ( _.contains( serverControlParams.wpBuiltinSettings, setId ) )
                return setId;
              if ( ! _.contains( serverControlParams.themeSettingList, setId ) )
                return setId;

              return -1 == setId.indexOf( serverControlParams.themeOptions ) ? [ serverControlParams.themeOptions +'[' , setId  , ']' ].join('') : setId;
      },
        getOptionName : function(name) {
              var self = this;
              if ( -1 == name.indexOf(serverControlParams.themeOptions) )
                return name;
              return name.replace(/\[|\]/g, '').replace(serverControlParams.themeOptions, '');
        },
        hasPartRefresh : function( setId ) {
              if ( ! _.has( api, 'czr_partials')  )
                return;
              return  _.contains( _.map( api.czr_partials(), function( partial, key ) {
                    return _.contains( partial.settings, setId );
              }), true );
        },
        getSectionControlIds : function( section_id ) {
              section_id = section_id || api.czr_activeSectionId();
              return ! api.section.has( section_id ) ?
              [] :
              _.map( api.section( section_id ).controls(), function( _ctrl ) {
                    return _ctrl.id;
              });
        },
        getSectionSettingIds : function( section_id ) {
              section_id = section_id || api.czr_activeSectionId();
              if ( ! api.section.has( section_id) )
                return;
              var self = this,
                  _sec_settings = [],
                  _sec_controls = self.getSectionControlIds( section_id );

              _.each( _sec_controls, function( ctrlId ) {
                    _.each( api.control(ctrlId).settings, function( _instance, _k ) {
                          _sec_settings.push( _instance.id );
                    });
              });
              return _sec_settings;
        },
        capitalize : function( string ) {
              if( ! _.isString(string) )
                return string;
              return string.charAt(0).toUpperCase() + string.slice(1);
        },

        truncate : function( string, n, useWordBoundary ){
              if ( _.isUndefined(string) )
                return '';
              var isTooLong = string.length > n,
                  s_ = isTooLong ? string.substr(0,n-1) : string;
                  s_ = (useWordBoundary && isTooLong) ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
              return  isTooLong ? s_ + '...' : s_;
        },
        isMultiItemModule : function( module_type, moduleInst ) {
              if ( _.isUndefined( module_type ) && ! _.isObject( moduleInst ) )
                return;
              if ( _.isObject( moduleInst ) && _.has( moduleInst, 'module_type' ) )
                module_type = moduleInst.module_type;
              else if ( _.isUndefined( module_type ) || _.isNull( module_type ) )
                return;
              if ( ! _.has( api.czrModuleMap, module_type ) )
                return;

              return api.czrModuleMap[module_type].crud || api.czrModuleMap[module_type].multi_item || false;
        },
        isCrudModule : function( module_type, moduleInst ) {
              if ( _.isUndefined( module_type ) && ! _.isObject( moduleInst ) )
                return;
              if ( _.isObject( moduleInst ) && _.has( moduleInst, 'module_type' ) )
                module_type = moduleInst.module_type;
              else if ( _.isUndefined( module_type ) || _.isNull( module_type ) )
                return;
              if ( ! _.has( api.czrModuleMap, module_type ) )
                return;

              return api.czrModuleMap[module_type].crud || false;
        },
        hasModuleModOpt : function( module_type, moduleInst ) {
              if ( _.isUndefined( module_type ) && ! _.isObject( moduleInst ) )
                return;
              if ( _.isObject( moduleInst ) && _.has( moduleInst, 'module_type' ) )
                module_type = moduleInst.module_type;
              else if ( _.isUndefined( module_type ) || _.isNull( module_type ) )
                return;
              if ( ! _.has( api.czrModuleMap, module_type ) )
                return;

              return api.czrModuleMap[module_type].has_mod_opt || false;
        },
        setupInputCollectionFromDOM : function() {
              var inputParentInst = this;//<= because fired with .call( inputParentInst )
              if ( ! _.isFunction( inputParentInst ) ) {
                    throw new Error( 'setupInputCollectionFromDOM : inputParentInst is not valid.' );
              }
              var module = inputParentInst.module,
                  is_mod_opt = _.has( inputParentInst() , 'is_mod_opt' );
              if ( _.has( inputParentInst, 'czr_Input') && ! _.isEmpty( inputParentInst.inputCollection() ) )
                return;
              inputParentInst.czr_Input = new api.Values();
              inputParentInst.inputConstructor = is_mod_opt ? module.inputModOptConstructor : module.inputConstructor;

              var _defaultInputParentModel = is_mod_opt ? inputParentInst.defaultModOptModel : inputParentInst.defaultItemModel;

              if ( _.isEmpty( _defaultInputParentModel ) || _.isUndefined( _defaultInputParentModel ) ) {
                throw new Error( 'No default model found in item or mod opt ' + inputParentInst.id + '.' );
              }
              var inputParentInst_model = $.extend( true, {}, inputParentInst() );

              if ( ! _.isObject( inputParentInst_model ) )
                inputParentInst_model = _defaultInputParentModel;
              else
                inputParentInst_model = $.extend( _defaultInputParentModel, inputParentInst_model );

              var dom_inputParentInst_model = {};
              $( '.' + module.control.css_attr.sub_set_wrapper, inputParentInst.container).each( function( _index ) {
                    var _id = $(this).find('[data-type]').attr( 'data-type' ),
                        _value = _.has( inputParentInst_model, _id ) ? inputParentInst_model[ _id ] : '';
                    if ( _.isUndefined( _id ) || _.isEmpty( _id ) ) {
                          api.consoleLog( 'setupInputCollectionFromDOM : missing data-type for ' + module.id );
                          return;
                    }
                    if ( ! _.has( inputParentInst_model, _id ) ) {
                          throw new Error('The item or mod opt property : ' + _id + ' has been found in the DOM but not in the item or mod opt model : '+ inputParentInst.id + '. The input can not be instantiated.');
                    }
                    var _inputType      = $(this).attr( 'data-input-type' ),
                        _inputTransport = $(this).attr( 'data-transport' ) || 'inherit',//<= if no specific transport ( refresh or postMessage ) has been defined in the template, inherits the control transport
                        _inputOptions   = _.has( module.inputOptions, _inputType ) ? module.inputOptions[ _inputType ] : {};
                    inputParentInst.czr_Input.add( _id, new inputParentInst.inputConstructor( _id, {
                          id            : _id,
                          type          : _inputType,
                          transport     : _inputTransport,
                          input_value   : _value,
                          input_options : _inputOptions,//<= a module can define a specific set of option
                          container     : $(this),
                          input_parent  : inputParentInst,
                          is_mod_opt    : is_mod_opt,
                          module        : module
                    } ) );
                    inputParentInst.czr_Input( _id ).ready();
                    dom_inputParentInst_model[ _id ] = _value;
              });//each
              inputParentInst.inputCollection( dom_inputParentInst_model );
              return inputParentInst;
        },
        removeInputCollection : function() {
              var inputParentInst = this;//<= because fired with .call( inputParentInst )
              if ( ! _.isFunction( inputParentInst ) ) {
                    throw new Error( 'removeInputCollection : inputParentInst is not valid.' );
              }
              if ( ! _.has( inputParentInst, 'czr_Input') )
                return;
              inputParentInst.czr_Input.each( function( _input ) {
                    inputParentInst.czr_Input.remove( _input.id );
              });
              inputParentInst.inputCollection({});
        },
        refreshModuleControl : function( wpSetId ) {
              var _constructor = api.controlConstructor.czr_module,
                  _control_type = api.control( wpSetId ).params.type,
                  _control_data = api.settings.controls[wpSetId];
              $.when( api.control( wpSetId ).container.remove() ).done( function() {
                    api.control.remove( wpSetId );
                    api.control.add( wpSetId,  new _constructor( wpSetId, { params : _control_data, previewer : api.previewer }) );
              });

        }
  });//$.extend
})( wp.customize , jQuery, _);
(function (api, $, _) {
  api.CZR_Helpers = api.CZR_Helpers || {};
  api.CZR_Helpers = $.extend( api.CZR_Helpers, {
        addActions : function( event_map, new_events, instance ) {
                var control = this;
                instance = instance || control;
                instance[event_map] = instance[event_map] || [];
                new_event_map = _.clone( instance[event_map] );
                instance[event_map] = _.union( new_event_map, ! _.isArray(new_events) ? [new_events] : new_events );
        },

        doActions : function( action, $dom_el, obj ) {
                $dom_el.trigger( action, obj );
        },
        setupDOMListeners : function( event_map , obj, instance ) {
                var control = this;
                instance = instance || control;
                _.map( event_map , function( _event ) {
                      if ( ! _.isString( _event.selector ) || _.isEmpty( _event.selector ) ) {
                        throw new Error( 'setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : ' + _event.actions.join(',') );
                      }
                      obj.dom_el.on( _event.trigger , _event.selector, function( e, event_params ) {
                            e.stopPropagation();
                            if ( api.utils.isKeydownButNotEnterEvent( e ) ) {
                              return;
                            }
                            e.preventDefault(); // Keep this AFTER the key filter above
                            var _obj = _.clone(obj);
                            if ( _.has(_obj, 'model') && _.has( _obj.model, 'id') ) {
                              if ( _.has(instance, 'get') )
                                _obj.model = instance();
                              else
                                _obj.model = instance.getModel( _obj.model.id );
                            }
                            $.extend( _obj, { event : _event, dom_event : e } );
                            $.extend( _obj, event_params );
                            control.executeEventActionChain( _obj, instance );
                      });//.on()

                });//_.map()
        },
        executeEventActionChain : function( obj, instance ) {
                var control = this;
                if ( ! _.has( obj, 'event' ) || ! _.has( obj.event, 'actions' ) ) {
                  throw new Error('executeEventActionChain : No obj.event or no obj.event.actions properties found');
                }
                if ( 'function' === typeof(obj.event.actions) )
                  return obj.event.actions(obj);
                if ( ! _.isArray(obj.event.actions) )
                  obj.event.actions = [ obj.event.actions ];
                var _break = false;
                _.map( obj.event.actions, function( _cb ) {

                  if ( _break )
                    return;

                  if ( 'function' != typeof( instance[_cb] ) ) {
                    throw new Error( 'executeEventActionChain : the action : ' + _cb + ' has not been found when firing event : ' + obj.event.selector );
                  }
                  var $_dom_el = ( _.has(obj, 'dom_el') && -1 != obj.dom_el.length ) ? obj.dom_el : control.container;

                  $_dom_el.trigger('before_' + _cb, _.omit( obj, 'event') );
                    var _cb_return = instance[_cb](obj);
                    if ( false === _cb_return )
                      _break = true;
                  $_dom_el.trigger('after_' + _cb, _.omit( obj, 'event') );

                });//_.map
        }
  });//$.extend
})( wp.customize , jQuery, _);
(function (api, $, _) {

  api.czr_wpQueryDataReady = $.Deferred();
  api.czr_wpQueryInfos = api.czr_wpQueryInfos || new api.Value();
  api.czr_partials = api.czr_partials || new api.Value();
  api.bind( 'ready', function() {
        api.previewer.bind('houston-widget-settings', function(data) {
              var _candidates = _.filter( data.registeredSidebars, function( sb ) {
                return ! _.findWhere( _wpCustomizeWidgetsSettings.registeredSidebars, { id: sb.id } );
              });

              var _inactives = _.filter( data.registeredSidebars, function( sb ) {
                return ! _.has( data.renderedSidebars, sb.id );
              });

              _inactives = _.map( _inactives, function(obj) {
                return obj.id;
              });

              var _registered = _.map( data.registeredSidebars, function(obj) {
                return obj.id;
              });
              api.czr_widgetZoneSettings = api.czr_widgetZoneSettings || new api.Value();//will store all widget zones data sent by preview as an observable object
              api.czr_widgetZoneSettings.set( {
                    actives :  data.renderedSidebars,
                    inactives :  _inactives,
                    registered :  _registered,
                    candidates :  _candidates,
                    available_locations :  data.availableWidgetLocations//built server side
              } );

        });
        api.previewer.bind( 'czr-query-data-ready', function( data ) {
              api.czr_wpQueryInfos( data );
              if ( 'pending' == api.czr_wpQueryDataReady.state() ) {
                    api.czr_wpQueryDataReady.resolve( data );
              }
        });
        api.previewer.bind( 'czr-partial-refresh', function( data ) {
              api.czr_partials.set( data );
        });
  });//api.bind('ready')
})( wp.customize , jQuery, _ );var CZRInputMths = CZRInputMths || {};
$.extend( CZRInputMths , {
    initialize: function( name, options ) {
          if ( _.isUndefined( options.input_parent ) || _.isEmpty(options.input_parent) ) {
            throw new Error('No input_parent assigned to input ' + options.id + '. Aborting');
          }
          if ( _.isUndefined(options.module ) ) {
            throw new Error('No module assigned to input ' + options.id + '. Aborting');
          }

          api.Value.prototype.initialize.call( this, null, options );

          var input = this;
          $.extend( input, options || {} );
          input.isReady = $.Deferred();
          if ( ! _.isUndefined(options.input_value) ) {
                input.set( options.input_value );
          }
          if ( api.czrInputMap && _.has( api.czrInputMap, input.type ) ) {
                var _meth = api.czrInputMap[ input.type ];
                if ( _.isFunction( input[_meth]) ) {
                      input[_meth]( options.input_options || null );
                }
          } else {
                api.consoleLog('Warning an input : ' + input.id + ' has no corresponding method defined in api.czrInputMap.');
          }

          var trigger_map = {
                text : 'keyup',
                textarea : 'keyup',
                password : 'keyup',
                color : 'colorpickerchange',
                range : 'input propertychange'
          };
          input.input_event_map = [
                  {
                    trigger   : $.trim( ['change', trigger_map[input.type] || '' ].join(' ') ),//was 'propertychange change click keyup input',//colorpickerchange is a custom colorpicker event @see method setupColorPicker => otherwise we don't
                    selector  : 'input[data-type], select[data-type], textarea[data-type]',
                    name      : 'set_input_value',
                    actions   : function( obj ) {
                        if ( ! _.has( input.input_parent, 'syncElements') || ! _.has( input.input_parent.syncElements, input.id ) ) {
                            throw new Error('WARNING : THE INPUT ' + input.id + ' HAS NO SYNCED ELEMENT.');
                        }
                    }//was 'updateInput'
                  }
          ];
    },
    ready : function() {
            var input = this;
            input.setupDOMListeners( input.input_event_map , { dom_el : input.container }, input );
            input.callbacks.add( function() { return input.inputReact.apply( input, arguments ); } );
            $.when( input.setupSynchronizer() ).done( function() {
                  input.isReady.resolve( input );
            } );

    },
    setupSynchronizer: function() {
          var input       = this,
              input_parent        = input.input_parent,
              $_input_el  = input.container.find('[data-type]'),
              is_textarea = input.container.find('[data-type]').is('textarea');
          if ( is_textarea ) {
            throw new Error('TO DO : THE TEXTAREA INPUT ARE NOT READY IN THE SYNCHRONIZER!');
          }

          var syncElement = new api.Element( $_input_el );
          input_parent.syncElements = input_parent.syncElements || {};
          input_parent.syncElements[input.id] = syncElement;//adds the input syncElement to the collection
          syncElement.sync( input );//sync with the input instance
          syncElement.set( input() );
    },
    inputReact : function( to, from, data ) {
            var input = this,
                _current_input_parent = input.input_parent(),
                _new_model        = _.clone( _current_input_parent );//initialize it to the current value
            _new_model =  ( ! _.isObject(_new_model) || _.isEmpty(_new_model) ) ? {} : _new_model;
            _new_model[input.id] = to;
            input.input_parent.set( _new_model, {
                  input_changed     : input.id,
                  input_transport   : input.transport,
                  not_preview_sent  : 'postMessage' === input.transport//<= this parameter set to true will prevent the setting to be sent to the preview ( @see api.Setting.prototype.preview override ). This is useful to decide if a specific input should refresh or not the preview.
            } );

            if ( ! _.has( input, 'is_preItemInput' ) ) {
                  input.input_parent.trigger( input.id + ':changed', to );
            }
            if ( ! _.isEmpty( from ) || ! _.isUndefined( from ) && 'postMessage' === input.transport ) {
                  input._sendInput( to, from );
            }
    },
    _sendInput : function( to, from ) {
          var input = this,
              module = input.module;

          if ( _.isEqual( to, from ) )
            return;

          module.control.previewer.send( 'czr_input', {
                set_id        : module.control.id,
                module_id     : module.id,//<= will allow us to target the right dom element on front end
                input_id      : input.id,
                value         : to
          });
          module.trigger( 'input_sent', { input : to , dom_el: input.container } );
    },
    setupColorPicker : function() {
        var input  = this;

        input.container.find('input').wpColorPicker( {
            change : function( e, o ) {
                  $(this).val( $(this).wpColorPicker('color') ).trigger('colorpickerchange').trigger('change');
            }
        });
    },

    setupSelect : function() {
        var input = this;
        $('select', input.container ).not('.no-selecter-js')
              .each( function() {
                    $(this).selecter({
                    });
        });
    },

    setupIcheck : function( obj ) {
            var input      = this;

            $( 'input[type=checkbox]', input.container ).each( function(e) {
                  if ( 0 !== $(this).closest('div[class^="icheckbox"]').length )
                    return;

                  $(this).iCheck({
                        checkboxClass: 'icheckbox_flat-grey',
                        checkedClass: 'checked',
                        radioClass: 'iradio_flat-grey',
                  })
                  .on( 'ifChanged', function(e){
                        $(this).val( false === $(this).is(':checked') ? 0 : 1 );
                        $(e.currentTarget).trigger('change');
                  });
            });
    },

    setupStepper : function( obj ) {
          var input      = this;
          $('input[type="number"]',input.container ).each( function( e ) {
                $(this).stepper();
          });
    },
    setupRangeSlider : function( options ) {
              var input = this,
                  $handle,
                  _updateHandle = function(el, val) {
                        el.textContent = val + "%";
                  };

              $( input.container ).find('input').rangeslider( {
                    polyfill: false,
                    rangeClass: 'rangeslider',
                    disabledClass: 'rangeslider--disabled',
                    horizontalClass: 'rangeslider--horizontal',
                    verticalClass: 'rangeslider--vertical',
                    fillClass: 'rangeslider__fill',
                    handleClass: 'rangeslider__handle',
                    onInit: function() {
                          $handle = $('.rangeslider__handle', this.$range);
                          $('.rangeslider__handle', this.$range);
                          _updateHandle( $handle[0], this.value );
                    },
              } ).on('input', function() {
                    _updateHandle( $handle[0], this.value );
              });
        }
});//$.extendvar CZRInputMths = CZRInputMths || {};
$.extend( CZRInputMths , {
    setupImageUploader : function() {
          var input        = this,
              _model       = input();
          input.attachment   = {};
          if ( ! input.container )
            return this;

          this.tmplRendered = $.Deferred();
          this.setupContentRendering( _model, {} );
          this.tmplRendered.done( function(){
            input.czrImgUploaderBinding();
          });
  },

  setupContentRendering : function( to, from) {
        var input = this, _attachment;
        if ( ( input.attachment.id != to ) && from !== to ) {
              if ( ! to ) {
                    input.attachment = {};
                    input.renderImageUploaderTemplate();
              }
              _attachment = wp.media.attachment( to );
              if ( _.isObject( _attachment ) && _.has( _attachment, 'attributes' ) && _.has( _attachment.attributes, 'sizes' ) ) {
                    input.attachment       = _attachment.attributes;
                    input.renderImageUploaderTemplate();
              } else {
                    wp.media.attachment( to ).fetch().done( function() {
                          input.attachment       = this.attributes;
                          input.renderImageUploaderTemplate();
                    });
              }
        }//Standard reaction, the image has been updated by the user or init
        else if (  ! input.attachment.id || input.attachment.id === to ) {
              input.renderImageUploaderTemplate();
        }
  },

  czrImgUploaderBinding : function() {
        var input = this;
        _.bindAll( input, 'czrImgUploadRemoveFile', 'czrImgUploadOpenFrame', 'czrImgUploadSelect');
        input.container.on( 'click keydown', '.upload-button', input.czrImgUploadOpenFrame );
        input.container.on( 'click keydown', '.thumbnail-image img', input.czrImgUploadOpenFrame );
        input.container.on( 'click keydown', '.remove-button', input.czrImgUploadRemoveFile );

        input.bind( input.id + ':changed', function( to, from ){
              input.tmplRendered = $.Deferred();
              input.setupContentRendering(to,from);
        });
  },
  czrImgUploadOpenFrame: function( event ) {
        if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
          return;
        }

        event.preventDefault();

        if ( ! this.frame ) {
          this.czrImgUploadInitFrame();
        }

        this.frame.open();
  },
  czrImgUploadInitFrame: function() {
        var input = this;

        var button_labels = this.getUploaderLabels();

         input.frame = wp.media({
               button: {
                   text: button_labels.frame_button
               },
               states: [
                   new wp.media.controller.Library({
                     title:     button_labels.frame_title,
                     library:   wp.media.query({ type: 'image' }),
                     multiple:  false,
                     date:      false
                   })
               ]
         });
         input.frame.on( 'select', input.czrImgUploadSelect );
  },
  czrImgUploadRemoveFile: function( event ) {
        var input = this;

        if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
          return;
        }
        event.preventDefault();
        input.attachment = {};
        input.set('');
  },
  czrImgUploadSelect: function() {
        var node,
            input = this,
            attachment   = input.frame.state().get( 'selection' ).first().toJSON(),  // Get the attachment from the modal frame.
            mejsSettings = window._wpmejsSettings || {};
        input.attachment = attachment;
        input.set(attachment.id);
  },
  renderImageUploaderTemplate: function() {
        var input  = this;
        if ( 0 === $( '#tmpl-czr-input-img-uploader-view-content' ).length )
          return;

        var view_template = wp.template('czr-input-img-uploader-view-content');
        if ( ! view_template  || ! input.container )
         return;

        var $_view_el    = input.container.find('.' + input.module.control.css_attr.img_upload_container );

        if ( ! $_view_el.length )
          return;

        var _template_params = {
          button_labels : input.getUploaderLabels(),
          settings      : input.id,
          attachment    : input.attachment,
          canUpload     : true
        };

        $_view_el.html( view_template( _template_params) );

        input.tmplRendered.resolve();
        input.container.trigger( input.id + ':content_rendered' );

        return true;
  },

  getUploaderLabels : function() {
        var _ts = serverControlParams.translatedStrings,
            _map = {
            'select'      : _ts.select_image,
            'change'      : _ts.change_image,
            'remove'      : _ts.remove_image,
            'default'     : _ts.default_image,
            'placeholder' : _ts.placeholder_image,
            'frame_title' : _ts.frame_title_image,
            'frame_button': _ts.frame_button_image
        };
        _.each( _map, function( ts_string, key ) {
          if ( _.isUndefined( ts_string ) ) {
            var input = this;
            throw new Error( 'A translated string is missing ( ' + key + ' ) for the image uploader input in module : ' + input.module.id );
          }
        } );

        return _map;
  }
});//$.extend/* Fix caching, select2 default one seems to not correctly work, or it doesn't what I think it should */
var CZRInputMths = CZRInputMths || {};
$.extend( CZRInputMths , {
  setupContentPicker: function( wpObjectTypes ) {
          var input  = this,
          _event_map = [];
          $.extend( {
                post : '',
                taxonomy : ''
          }, _.isObject( wpObjectTypes ) ? wpObjectTypes : {} );

          input.wpObjectTypes = wpObjectTypes;
          input.container.find('.czr-input').append('<select data-select-type="content-picker-select" class="js-example-basic-simple"></select>');
          _event_map = [
                {
                      trigger   : 'change',
                      selector  : 'select[data-select-type]',
                      name      : 'set_input_value',
                      actions   : function( obj ){
                            var $_changed_input   = $(obj.dom_event.currentTarget, obj.dom_el ),
                                _raw_val          = $( $_changed_input, obj.dom_el ).select2( 'data' ),
                                _val_candidate    = {},
                                _default          = {
                                      id          : '',
                                      type_label  : '',
                                      title       : '',
                                      object_type : '',
                                      url         : ''
                                };

                            _raw_val = _.isArray( _raw_val ) ? _raw_val[0] : _raw_val;
                            if ( ! _.isObject( _raw_val ) || _.isEmpty( _raw_val ) ) {
                                api.consoleLog( 'Content Picker Input : the picked value should be an object not empty.');
                                return;
                            }
                            _.each( _default, function( val, k ){
                                  if ( ! _.has( _raw_val, k ) || _.isEmpty( _raw_val[k] ) ) {
                                        api.consoleLog( 'content_picker : missing input param : ' + k );
                                        return;
                                  }
                                  _val_candidate[k] = _raw_val[k];
                            } );
                            input.set( _val_candidate );
                      }
                }
          ];

          input.setupDOMListeners( _event_map , { dom_el : input.container }, input );
          input.setupContentSelecter();
  },

  setupContentSelecter : function() {
          var input = this;

          input.container.find('select').select2( {
                placeholder: {
                      id: '-1', // the value of the option
                      title: 'Select'
                },
                data : input.setupSelectedContents(),
                ajax: {
                      url: serverControlParams.AjaxUrl,
                      type: 'POST',
                      dataType: 'json',
                      delay: 250,
                      debug: true,
                      data: function ( params ) {
                            var page = params.page ? params.page - 1 : 0;
                            page = params.term ? params.page : page;
                            return {
                                  action          : params.term ? "search-available-content-items-customizer" : "load-available-content-items-customizer",
                                  search          : params.term,
                                  wp_customize    : 'on',
                                  page            : page,
                                  wp_object_types : JSON.stringify( input.wpObjectTypes ),
                                  CZRCpNonce      : serverControlParams.CZRCpNonce
                            };
                      },
                      processResults: function ( data, params ) {
                            if ( ! data.success )
                              return { results: [] };

                            var items   = data.data.items,
                                _results = [];

                            _.each( items, function( item ) {
                                  _results.push({
                                        id          : item.id,
                                        title       : item.title,
                                        type_label  : item.type_label,
                                        object_type : item.object,
                                        url         : item.url
                                  });
                            });
                            return {
                                  results: _results,
                                  pagination: { more: data.data.items.length == 10 }
                            };
                      },
                },//ajax
                templateSelection: input.czrFormatContentSelected,
                templateResult: input.czrFormatContentSelected,
                escapeMarkup: function (markup) { return markup; },
         });//select2 setup
  },


  czrFormatContentSelected: function (item) {
          if ( item.loading ) return item.text;
          var markup = "<div class='content-picker-item clearfix'>" +
            "<div class='content-item-bar'>" +
              "<span class='item-title'>" + item.title + "</span>";

          if ( item.type_label ) {
            markup += "<span class='item-type'>" + item.type_label + "</span>";
          }

          markup += "</div></div>";

          return markup;
  },

  setupSelectedContents : function() {
        var input = this,
           _model = input();

        return _model;
  }
});//$.extend
var CZRInputMths = CZRInputMths || {};
$.extend( CZRInputMths , {
    setupTextEditor : function() {
          var input        = this,
              _model       = input();
          if ( ! input.container ) {
              throw new Error( 'The input container is not set for WP text editor in module.' + input.module.id );
          }

          if ( ! input.czrRenderInputTextEditorTemplate() )
            return;

          input.editor       = tinyMCE( 'czr-customize-content_editor' );
          input.textarea     = $( '#czr-customize-content_editor' );
          input.editorPane   = $( '#czr-customize-content_editor-pane' );
          input.dragbar      = $( '#czr-customize-content_editor-dragbar' );
          input.editorFrame  = $( '#czr-customize-content_editor_ifr' );
          input.mceTools     = $( '#wp-czr-customize-content_editor-tools' );
          input.mceToolbar   = input.editorPane.find( '.mce-toolbar-grp' );
          input.mceStatusbar = input.editorPane.find( '.mce-statusbar' );

          input.preview      = $( '#customize-preview' );
          input.collapse     = $( '.collapse-sidebar' );

          input.textpreview  = input.container.find('textarea');
          input.toggleButton = input.container.find('button.text_editor-button');
          input.editorExpanded   = new api.Value( false );
          input.czrUpdateTextPreview();
          input.czrSetToggleButtonText( input.editorExpanded() );

          input.czrTextEditorBinding();

          input.czrResizeEditorOnUserRequest();
  },

  czrTextEditorBinding : function() {
          var input = this,
              editor = input.editor,
              textarea = input.textarea,
              toggleButton = input.toggleButton,
              editorExpanded = input.editorExpanded,
              editorPane   = input.editorPane;


          input.bind( input.id + ':changed', input.czrUpdateTextPreview );

          _.bindAll( input, 'czrOnVisualEditorChange', 'czrOnTextEditorChange', 'czrResizeEditorOnWindowResize' );

          toggleButton.on( 'click', function() {
                input.editorExpanded.set( ! input.editorExpanded() );
                if ( input.editorExpanded() ) {
                  editor.focus();
                }
          });
          input.module.czr_ModuleState.bind(
            function( state ) {
              if ( 'expanded' != state )
                input.editorExpanded.set( false );
          });

          input.editorExpanded.bind( function (expanded) {

                api.consoleLog('in input.editorExpanded', expanded, input() );
                if ( editor.locker && editor.locker !== input ) {
                    editor.locker.editorExpanded.set(false);
                    editor.locker = null;
                }if ( ! editor.locker || editor.locker === input ) {
                    $(document.body).toggleClass('czr-customize-content_editor-pane-open', expanded);
                    editor.locker = input;
                }
                input.czrSetToggleButtonText( expanded );

                if ( expanded ) {
                    editor.setContent( wp.editor.autop( input() ) );
                    editor.on( 'input change keyup', input.czrOnVisualEditorChange );
                    textarea.on( 'input', input.czrOnTextEditorChange );
                    input.czrResizeEditor( window.innerHeight - editorPane.height() );
                    $( window ).on('resize', input.czrResizeEditorOnWindowResize );

                } else {
                    editor.off( 'input change keyup', input.czrOnVisualEditorChange );
                    textarea.off( 'input', input.czrOnTextEditorChange );
                    $( window ).off('resize', input.czrResizeEditorOnWindowResize );
                    input.czrResizeReset();
                }
          } );
  },

  czrOnVisualEditorChange : function() {
          var input = this,
              editor = input.editor,
              value;

          value = wp.editor.removep( editor.getContent() );
          input.set(value);
  },

  czrOnTextEditorChange : function() {
          var input = this,
              textarea = input.textarea,
              value;

          value = textarea.val();
          input.set(value);
  },
  czrUpdateTextPreview: function() {
          var input   = this,
              input_model = input(),
              value;
          value = input_model.replace(/(<([^>]+)>)/ig,"");
          if ( value.length > 30 )
            value = value.substring(0, 34) + '...';

          input.textpreview.val( value );
  },
  czrRenderInputTextEditorTemplate: function() {
          var input  = this;
          if ( 0 === $( '#tmpl-czr-input-text_editor-view-content' ).length ) {
              throw new Error('Missing js template for text editor input in module : ' + input.module.id );
          }

          var view_template = wp.template('czr-input-text_editor-view-content'),
                  $_view_el = input.container.find('input');
          if ( ! view_template  || ! input.container )
            return;

          api.consoleLog('Model injected in text editor tmpl : ', input() );

          $_view_el.after( view_template( input() ) );

          return true;
  },
  czrIsEditorExpanded : function() {
          return $( document.body ).hasClass('czr-customize-content_editor-pane-open');
  },
  czrResizeReset  : function() {
          var input = this,
              preview = input.preview,
              collapse = input.collapse,
              sectionContent = input.container.closest('ul.accordion-section-content');

          sectionContent.css( 'padding-bottom', '' );
          preview.css( 'bottom', '' );
          collapse.css( 'bottom', '' );
  },
  czrResizeEditor : function( position ) {
          var windowHeight = window.innerHeight,
              windowWidth = window.innerWidth,
              minScroll = 40,
              maxScroll = 1,
              mobileWidth = 782,
              collapseMinSpacing = 56,
              collapseBottomOutsideEditor = 8,
              collapseBottomInsideEditor = 4,
              args = {},
              input = this,
              sectionContent = input.container.closest('ul.accordion-section-content'),
              mceTools = input.mceTools,
              mceToolbar = input.mceToolbar,
              mceStatusbar = input.mceStatusbar,
              preview      = input.preview,
              collapse     = input.collapse,
              editorPane   = input.editorPane,
              editorFrame  = input.editorFrame;

          if ( ! input.editorExpanded() ) {
            return;
          }

          if ( ! _.isNaN( position ) ) {
            resizeHeight = windowHeight - position;
          }

          args.height = resizeHeight;
          args.components = mceTools.outerHeight() + mceToolbar.outerHeight() + mceStatusbar.outerHeight();

          if ( resizeHeight < minScroll ) {
            args.height = minScroll;
          }

          if ( resizeHeight > windowHeight - maxScroll ) {
            args.height = windowHeight - maxScroll;
          }

          if ( windowHeight < editorPane.outerHeight() ) {
            args.height = windowHeight;
          }

          preview.css( 'bottom', args.height );
          editorPane.css( 'height', args.height );
          editorFrame.css( 'height', args.height - args.components );
          collapse.css( 'bottom', args.height + collapseBottomOutsideEditor );

          if ( collapseMinSpacing > windowHeight - args.height ) {
            collapse.css( 'bottom', mceStatusbar.outerHeight() + collapseBottomInsideEditor );
          }

          if ( windowWidth <= mobileWidth ) {
            sectionContent.css( 'padding-bottom', args.height );
          } else {
            sectionContent.css( 'padding-bottom', '' );
          }
  },
  czrResizeEditorOnWindowResize : function() {
          var input = this,
              resizeDelay = 50,
              editorPane   = input.editorPane;

          if ( ! input.editorExpanded() ) {
            return;
          }

          _.delay( function() {
            input.czrResizeEditor( window.innerHeight - editorPane.height() );
          }, resizeDelay );

  },
  czrResizeEditorOnUserRequest : function() {
          var input = this,
              dragbar = input.dragbar,
              editorFrame = input.editorFrame;

          dragbar.on( 'mousedown', function() {
            if ( ! input.editorExpanded() )
              return;

            $( document ).on( 'mousemove.czr-customize-content_editor', function( event ) {
                event.preventDefault();
                $( document.body ).addClass( 'czr-customize-content_editor-pane-resize' );
                editorFrame.css( 'pointer-events', 'none' );
                input.czrResizeEditor( event.pageY );
              } );
            } );

          dragbar.on( 'mouseup', function() {
            if ( ! input.editorExpanded() )
              return;

            $( document ).off( 'mousemove.czr-customize-content_editor' );
            $( document.body ).removeClass( 'czr-customize-content_editor-pane-resize' );
            editorFrame.css( 'pointer-events', '' );
          } );

  },
  czrSetToggleButtonText : function( $_expanded ) {
          var input = this;

          input.toggleButton.text( serverControlParams.translatedStrings[ ! $_expanded ? 'textEditorOpen' : 'textEditorClose' ] );
  }
});//$.extend//extends api.Value
var CZRItemMths = CZRItemMths || {};
$.extend( CZRItemMths , {
  initialize: function( id, options ) {
        if ( _.isUndefined(options.module) || _.isEmpty(options.module) ) {
          throw new Error('No module assigned to item ' + id + '. Aborting');
        }

        var item = this;
        api.Value.prototype.initialize.call( item, null, options );
        item.isReady = $.Deferred();
        item.embedded = $.Deferred();
        item.container = null;//will store the item $ dom element
        item.contentContainer = null;//will store the item content $ dom element
        item.inputCollection = new api.Value({});
        $.extend( item, options || {} );
        item.defaultItemModel = _.clone( options.defaultItemModel ) || { id : '', title : '' };
        var _initial_model = $.extend( item.defaultItemModel, options.initial_item_model );
        item.set( _initial_model );
        item.userEventMap = new api.Value( [
              {
                trigger   : 'click keydown',
                selector  : [ '.' + item.module.control.css_attr.display_alert_btn, '.' + item.module.control.css_attr.cancel_alert_btn ].join(','),
                name      : 'toggle_remove_alert',
                actions   : ['toggleRemoveAlertVisibility']
              },
              {
                trigger   : 'click keydown',
                selector  : '.' + item.module.control.css_attr.remove_view_btn,
                name      : 'remove_item',
                actions   : ['removeItem']
              },
              {
                trigger   : 'click keydown',
                selector  : [ '.' + item.module.control.css_attr.edit_view_btn, '.' + item.module.control.css_attr.item_title ].join(','),
                name      : 'edit_view',
                actions   : ['setViewVisibility']
              }
        ]);
        item.isReady.done( function() {
              item.module.updateItemsCollection( { item : item() } );
              item.callbacks.add( function() { return item.itemReact.apply(item, arguments ); } );
              item.mayBeRenderItemWrapper();
              item.embedded.done( function() {
                    item.itemWrapperViewSetup( _initial_model );
              });
              item.bind( 'contentRendered', function() {
                    if ( ! _.has( item, 'czr_Input' ) || _.isEmpty( item.inputCollection() ) ) {
                          try { api.CZR_Helpers.setupInputCollectionFromDOM.call( item ); } catch(e) {
                                api.consoleLog( e );
                          }
                    }
              });
              item.bind( 'contentRemoved', function() {
                    if ( _.has(item, 'czr_Input') )
                      api.CZR_Helpers.removeInputCollection.call( item );
              });

        });//item.isReady.done()

  },//initialize
  ready : function() {
        this.isReady.resolve();
  },
  itemReact : function( to, from ) {
        var item = this,
            module = item.module;
        module.updateItemsCollection( {item : to });
        item.writeItemViewTitle(to);
  },


});//$.extend//extends api.CZRBaseControl
var CZRItemMths = CZRItemMths || {};

  $.extend( CZRItemMths , {
    _sendItem : function( to, from ) {
          var item = this,
              module = item.module,
              _changed_props = [];
          _.each( from, function( _val, _key ) {
                if ( _val != to[_key] )
                  _changed_props.push(_key);
          });

          _.each( _changed_props, function( _prop ) {
                module.control.previewer.send( 'sub_setting', {
                      set_id : module.control.id,
                      id : to.id,
                      changed_prop : _prop,
                      value : to[_prop]
                });
                module.trigger('item_sent', { item : to , dom_el: item.container, changed_prop : _prop } );
          });
    },
    removeItem : function() {
            var item = this,
                module = this.module,
                _new_collection = _.clone( module.itemCollection() );
            module.trigger('pre_item_dom_remove', item() );
            item._destroyView();
            _new_collection = _.without( _new_collection, _.findWhere( _new_collection, {id: item.id }) );
            module.itemCollection.set( _new_collection );
            module.trigger('pre_item_api_remove', item() );
            module.czr_Item.remove(item.id);
    },
    getModel : function(id) {
            return this();
    }

  });//$.extend
var CZRItemMths = CZRItemMths || {};

$.extend( CZRItemMths , {
  mayBeRenderItemWrapper : function() {
        var item = this;

        if ( 'pending' != item.embedded.state() )
          return;

        $.when( item.renderItemWrapper() ).done( function( $_container ) {
              item.container = $_container;
              if ( _.isUndefined(item.container) || ! item.container.length ) {
                  throw new Error( 'In mayBeRenderItemWrapper the Item view has not been rendered : ' + item.id );
              } else {
                  item.embedded.resolve();
              }
        });
  },
  itemWrapperViewSetup : function( item_model ) {
          var item = this,
              module = this.module;

          item_model = item() || item.initial_item_model;//could not be set yet
          item.czr_ItemState = new api.Value( 'closed');
          item.writeItemViewTitle();
          var _updateItemContentDeferred = function( $_content, to, from ) {
                if ( ! _.isUndefined( $_content ) && false !== $_content.length ) {
                    item.trigger( 'contentRendered' );
                    item.contentContainer = $_content;
                    item.toggleItemExpansion( to, from );
                }
                else {
                    throw new Error( 'Module : ' + item.module.id + ', the item content has not been rendered for ' + item.id );
                }
          };

          if ( item.module.isMultiItem() ) {
                item.czr_ItemState.callbacks.add( function( to, from ) {
                      var _isExpanded = -1 !== to.indexOf('expanded');
                      if ( _isExpanded ) {
                            if ( _.isObject( item.contentContainer ) && false !== item.contentContainer.length ) {
                                  item.toggleItemExpansion(to, from );
                            } else {
                                  $.when( item.renderItemContent( item() || item.initial_item_model ) ).done( function( $_item_content ) {
                                        _updateItemContentDeferred = _.debounce(_updateItemContentDeferred, 50 );
                                        _updateItemContentDeferred( $_item_content, to, from );
                                  });
                            }
                      } else {
                            item.toggleItemExpansion( to, from ).done( function() {
                                  if ( _.isObject( item.contentContainer ) && false !== item.contentContainer.length ) {
                                        item.trigger( 'beforeContenRemoved' );
                                        $( '.' + module.control.css_attr.item_content, item.container ).children().each( function() {
                                              $(this).remove();
                                        });
                                        $( '.' + module.control.css_attr.item_content, item.container ).html('');
                                        item.contentContainer = null;
                                        item.trigger( 'contentRemoved' );
                                  }
                            });
                      }
                });
          } else {
                item.czr_ItemState.callbacks.add( function( to, from ) {
                    item.toggleItemExpansion.apply(item, arguments );
                });
                $.when( item.renderItemContent( item_model ) ).done( function( $_item_content ) {
                      _updateItemContentDeferred( $_item_content, true );
                });
          }
          api.CZR_Helpers.setupDOMListeners(
                item.userEventMap(),//actions to execute
                { model:item_model, dom_el:item.container },//model + dom scope
                item //instance where to look for the cb methods
          );
  },
  renderItemWrapper : function( item_model ) {
        var item = this,
            module = item.module;

        item_model = item_model || item();
        $_view_el = $('<li>', { class : module.control.css_attr.single_item, 'data-id' : item_model.id,  id : item_model.id } );
        module.itemsWrapper.append( $_view_el );
        if ( module.isMultiItem() ) {
              var _template_selector = module.getTemplateEl( 'rudItemPart', item_model );
              if ( 0 === $( '#tmpl-' + _template_selector ).length ) {
                  throw new Error('Missing template for item ' + item.id + '. The provided template script has no been found : #tmpl-' + module.getTemplateEl( 'rudItemPart', item_model ) );
              }
              $_view_el.append( $( wp.template( _template_selector )( item_model ) ) );
        }
        $_view_el.append( $( '<div/>', { class: module.control.css_attr.item_content } ) );

        return $_view_el;
  },
  renderItemContent : function( item_model ) {
          var item = this,
              module = this.module;

          item_model = item_model || item();
          if ( 0 === $( '#tmpl-' + module.getTemplateEl( 'itemInputList', item_model ) ).length ) {
              throw new Error('No item content template defined for module ' + module.id + '. The template script id should be : #tmpl-' + module.getTemplateEl( 'itemInputList', item_model ) );
          }

          var  item_content_template = wp.template( module.getTemplateEl( 'itemInputList', item_model ) );
          if ( ! item_content_template )
            return this;
          $( item_content_template( item_model )).appendTo( $('.' + module.control.css_attr.item_content, item.container ) );

          return $( $( item_content_template( item_model )), item.container );
  },
  writeItemViewTitle : function( item_model ) {
        var item = this,
            module = item.module,
            _model = item_model || item(),
            _title = _.has( _model, 'title')? api.CZR_Helpers.capitalize( _model.title ) : _model.id;

        _title = api.CZR_Helpers.truncate(_title, 20);
        $( '.' + module.control.css_attr.item_title , item.container ).text(_title );
        api.CZR_Helpers.doActions('after_writeViewTitle', item.container , _model, item );
  },
  setViewVisibility : function( obj, is_added_by_user ) {
          var item = this,
              module = this.module;
          if ( is_added_by_user ) {
                item.czr_ItemState.set( 'expanded_noscroll' );
          } else {
                module.closeAllItems( item.id );
                if ( _.has(module, 'preItem') ) {
                  module.preItemExpanded.set(false);
                }
                item.czr_ItemState.set( 'expanded' == item._getViewState() ? 'closed' : 'expanded' );
          }
  },


  _getViewState : function() {
          return -1 == this.czr_ItemState().indexOf('expanded') ? 'closed' : 'expanded';
  },
  toggleItemExpansion : function( status, from, duration ) {
          var item = this,
              module = this.module,
              dfd = $.Deferred();
          $( '.' + module.control.css_attr.item_content , item.container ).first().slideToggle( {
                duration : duration || 200,
                done : function() {
                      var _is_expanded = 'closed' != status;
                      item.container.toggleClass('open' , _is_expanded );
                      module.closeAllAlerts();
                      var $_edit_icon = $(this).siblings().find('.' + module.control.css_attr.edit_view_btn );

                      $_edit_icon.toggleClass('active' , _is_expanded );
                      if ( _is_expanded )
                        $_edit_icon.removeClass('fa-pencil').addClass('fa-minus-square').attr('title', serverControlParams.translatedStrings.close );
                      else
                        $_edit_icon.removeClass('fa-minus-square').addClass('fa-pencil').attr('title', serverControlParams.translatedStrings.edit );
                      if ( 'expanded' == status )
                        module._adjustScrollExpandedBlock( item.container );
                      dfd.resolve();
                }//done callback
        } );
        return dfd.promise();
  },
  toggleRemoveAlertVisibility : function(obj) {
          var item = this,
              module = this.module,
              $_alert_el = $( '.' + module.control.css_attr.remove_alert_wrapper, item.container ).first(),
              $_clicked = obj.dom_event;
          module.closeAllItems();

          if ( _.has(module, 'preItem') ) {
              module.preItemExpanded(false);
          }
          $('.' + module.control.css_attr.remove_alert_wrapper, item.container ).not($_alert_el).each( function() {
                if ( $(this).hasClass('open') ) {
                      $(this).slideToggle( {
                            duration : 200,
                            done : function() {
                                  $(this).toggleClass('open' , false );
                                  $(this).siblings().find('.' + module.control.css_attr.display_alert_btn).toggleClass('active' , false );
                            }
                      } );
                }
          });
          if ( ! wp.template( module.AlertPart )  || ! item.container ) {
              throw new Error( 'No removal alert template available for items in module :' + module.id );
          }

          $_alert_el.html( wp.template( module.AlertPart )( { title : ( item().title || item.id ) } ) );
          $_alert_el.slideToggle( {
                duration : 200,
                done : function() {
                      var _is_open = ! $(this).hasClass('open') && $(this).is(':visible');
                      $(this).toggleClass('open' , _is_open );
                      $( obj.dom_el ).find('.' + module.control.css_attr.display_alert_btn).toggleClass( 'active', _is_open );
                      if ( _is_open )
                        module._adjustScrollExpandedBlock( item.container );
                }
          } );
  },
  _destroyView : function ( duration ) {
          this.container.fadeOut( {
              duration : duration ||400,
              done : function() {
                $(this).remove();
              }
          });
  },
});//$.extend
var CZRModOptMths = CZRModOptMths || {};
$.extend( CZRModOptMths , {
  initialize: function( options ) {
        if ( _.isUndefined(options.module) || _.isEmpty(options.module) ) {
          throw new Error('No module assigned to modOpt.');
        }

        var modOpt = this;
        api.Value.prototype.initialize.call( modOpt, null, options );
        modOpt.isReady = $.Deferred();
        modOpt.container = null;//will store the modOpt $ dom element
        modOpt.inputCollection = new api.Value({});
        $.extend( modOpt, options || {} );
        modOpt.defaultModOptModel = _.clone( options.defaultModOptModel ) || { is_mod_opt : true };
        var _initial_model = $.extend( modOpt.defaultModOptModel, options.initial_modOpt_model );
        var ctrl = modOpt.module.control;
        modOpt.set( _initial_model );
        api.czr_ModOptVisible = new api.Value( false );
        api.czr_ModOptVisible.bind( function( visible ) {
              if ( visible ) {
                    modOpt.modOptWrapperViewSetup( _initial_model ).done( function( $_container ) {
                          modOpt.container = $_container;
                          try {
                                api.CZR_Helpers.setupInputCollectionFromDOM.call( modOpt ).toggleModPanelView( visible );
                          } catch(e) {
                                api.consoleLog(e);
                          }
                    });

              } else {
                    modOpt.toggleModPanelView( visible ).done( function() {
                          if ( false !== modOpt.container.length ) {
                                $.when( modOpt.container.remove() ).done( function() {
                                      api.CZR_Helpers.removeInputCollection.call( modOpt );
                                });
                          } else {
                                api.CZR_Helpers.removeInputCollection.call( modOpt );
                          }
                          modOpt.container = null;
                    });
              }
        } );
        modOpt.isReady.done( function() {
              if( ! $( '.' + ctrl.css_attr.edit_modopt_icon, ctrl.container ).length ) {
                    $.when( ctrl.container
                          .find('.customize-control-title').first()//was.find('.customize-control-title')
                          .append( $( '<span/>', {
                                class : [ ctrl.css_attr.edit_modopt_icon, 'fa fa-cog' ].join(' '),
                                title : 'Settings'//@to_translate
                          } ) ) )
                    .done( function(){
                          $( '.' + ctrl.css_attr.edit_modopt_icon, ctrl.container ).fadeIn( 400 );
                    });
              }
              api.CZR_Helpers.setupDOMListeners(
                    [
                          {
                                trigger   : 'click keydown',
                                selector  : '.' + ctrl.css_attr.edit_modopt_icon,
                                name      : 'toggle_mod_option',
                                actions   : function() {
                                      api.czr_ModOptVisible( ! api.czr_ModOptVisible() );
                                }
                          }
                    ],//actions to execute
                    { dom_el: ctrl.container },//dom scope
                    modOpt //instance where to look for the cb methods
              );
        });//modOpt.isReady.done()

  },//initialize
  ready : function() {
        this.isReady.resolve();
  },

});//$.extend//extends api.CZRBaseControl
var CZRModOptMths = CZRModOptMths || {};

$.extend( CZRModOptMths , {
  modOptWrapperViewSetup : function( modOpt_model ) {
          var modOpt = this,
              module = this.module,
              dfd = $.Deferred(),
              _setupDOMListeners = function( $_container ) {
                    api.CZR_Helpers.setupDOMListeners(
                         [
                                {
                                      trigger   : 'click keydown',
                                      selector  : '.' + module.control.css_attr.close_modopt_icon,
                                      name      : 'close_mod_option',
                                      actions   : function() {
                                            api.czr_ModOptVisible( false );
                                      }
                                }
                          ],//actions to execute
                          { dom_el: $_container },//model + dom scope
                          modOpt //instance where to look for the cb methods
                    );
              };

          modOpt_model = modOpt() || modOpt.initial_modOpt_model;//could not be set yet
          $.when( modOpt.renderModOptContent( modOpt_model ) ).done( function( $_container ) {
                if ( ! _.isUndefined( $_container ) && false !== $_container.length ) {
                      _setupDOMListeners( $_container );
                      dfd.resolve( $_container );
                }
                else {
                      throw new Error( 'Module : ' + modOpt.module.id + ', the modOpt content has not been rendered' );
                }
          });
          return dfd.promise();
  },
  renderModOptContent : function( modOpt_model ) {
          var modOpt = this,
              module = this.module;

          modOpt_model = modOpt_model || modOpt();
          if ( 0 === $( '#tmpl-' + module.getTemplateEl( 'modOptInputList', modOpt_model ) ).length ) {
              throw new Error('No modOpt content template defined for module ' + module.id + '. The template script id should be : #tmpl-' + module.getTemplateEl( 'modOptInputList', modOpt_model ) );
          }
          var  modOpt_content_template = wp.template( module.getTemplateEl( 'modOptInputList', modOpt_model ) );
          if ( ! modOpt_content_template )
            return this;

          var _ctrlLabel = '';
          try {
                    _ctrlLabel = 'Options for ' + module.control.params.label;//@to_translate
              } catch(e) {
                    _ctrlLabel = 'Settings';//@to_translate
              }

          $('#widgets-left').after( $( '<div/>', {
                class : module.control.css_attr.mod_opt_wrapper,
                html : [
                      [ '<h2 class="mod-opt-title">', _ctrlLabel , '</h2>' ].join(''),
                      '<span class="fa fa-times ' + module.control.css_attr.close_modopt_icon + '" title="close"></span>'
                ].join('')
          } ) );
          $( '.' + module.control.css_attr.mod_opt_wrapper ).append( $( modOpt_content_template( modOpt_model ) ) );

          return $( '.' + module.control.css_attr.mod_opt_wrapper );
  },



  toggleModPanelView : function( visible ) {
        var modOpt = this,
            module = this.module,
            ctrl = module.control,
            dfd = $.Deferred();

        module.control.container.toggleClass( 'czr-modopt-visible', visible );
        $('body').toggleClass('czr-editing-modopt', visible );
        _.delay( function() {
              dfd.resolve();
        }, 200 );
        return dfd.promise();
  }

});//$.extend//MULTI CONTROL CLASS
var CZRModuleMths = CZRModuleMths || {};

$.extend( CZRModuleMths, {

  initialize: function( id, constructorOptions ) {
        if ( _.isUndefined(constructorOptions.control) || _.isEmpty(constructorOptions.control) ) {
            throw new Error('No control assigned to module ' + id );
        }
        var module = this;
        api.Value.prototype.initialize.call( this, null, constructorOptions );
        module.isReady = $.Deferred();
        $.extend( module, constructorOptions || {} );
        $.extend( module, {
              crudModulePart : 'czr-crud-module-part',//create, read, update, delete
              rudItemPart : 'czr-rud-item-part',//read, update, delete
              ruItemPart : 'czr-ru-item-part',//read, update
              itemInputList : '',//is specific for each crud module
              modOptInputList : '',//is specific for each module
              AlertPart : 'czr-rud-item-alert-part',//used both for items and modules removal

        } );
        module.embedded = $.Deferred();
        if ( ! module.isInSektion() ) {
              module.container = $( module.control.selector );
              module.embedded.resolve();
        }
        module.embedded.done( function() {
              $.when( module.renderModuleParts() ).done(function( $_module_items_wrapper ){
                    if ( false === $_module_items_wrapper.length ) {
                        throw new Error( 'The items wrapper has not been rendered for module : ' + module.id );
                    }
                    module.itemsWrapper = $_module_items_wrapper;
              });
        });
        module.defaultAPImodOptModel = {
              initial_modOpt_model : {},
              defaultModOptModel : {},
              control : {},//control instance
              module : {}//module instance
        };
        module.defaultModOptModel = {};
        module.modOptConstructor = api.CZRModOpt;
        module.itemCollection = new api.Value( [] );
        module.defaultAPIitemModel = {
              id : '',
              initial_item_model : {},
              defaultItemModel : {},
              control : {},//control instance
              module : {},//module instance
              is_added_by_user : false
        };
        module.defaultItemModel = { id : '', title : '' };
        module.itemConstructor = api.CZRItem;
        module.czr_Item = new api.Values();
        module.inputConstructor = api.CZRInput;//constructor for the items input
        if ( module.hasModOpt() ) {
              module.inputModOptConstructor = api.CZRInput;//constructor for the modOpt input
        }
        module.inputOptions = {};//<= can be set by each module specifically
        module.isReady.done( function() {
              module.isDirty = new api.Value( constructorOptions.dirty || false );
              module.initializeModuleModel( constructorOptions )
                    .done( function( initialModuleValue ) {
                          module.set( initialModuleValue );
                    })
                    .fail( function( response ){ api.consoleLog( 'Module : ' + module.id + ' initialize module model failed : ', response ); })
                    .always( function( initialModuleValue ) {
                          module.callbacks.add( function() { return module.moduleReact.apply(module, arguments ); } );
                          if (  ! module.control.isModuleRegistered( module.id ) ) {
                              module.control.updateModulesCollection( { module : constructorOptions, is_registered : false } );
                          }

                          module.bind('items-collection-populated', function( collection ) {
                                module.itemCollection.callbacks.add( function() { return module.itemCollectionReact.apply(module, arguments ); } );
                                if ( module.isMultiItem() ) {
                                      module._makeItemsSortable();
                                }
                          });
                          if ( ! module.isInSektion() )
                            module.populateSavedItemCollection();
                          if ( module.hasModOpt() ) {
                              module.instantiateModOpt();
                          }
                    });
        });//module.isReady.done()
  },
  ready : function() {
        var module = this;
        module.isReady.resolve();
        api.consoleLog('MODULE READY IN BASE MODULE CLASS : ', module.id );
  },
  initializeModuleModel : function( constructorOptions ) {
        var module = this, dfd = $.Deferred();
        if ( ! module.isMultiItem() && ! module.isCrud() ) {
              if ( _.isEmpty( constructorOptions.items ) ) {
                    var def = _.clone( module.defaultItemModel );
                    constructorOptions.items = [ $.extend( def, { id : module.id } ) ];
              }
        }
        return dfd.resolve( constructorOptions ).promise();
  },
  itemCollectionReact : function( to, from, o ) {
        var module = this,
            _current_model = module(),
            _new_model = $.extend( true, {}, _current_model );
        _new_model.items = to;
        module.isDirty.set(true);
        module.set( _new_model, o || {} );
  },
  moduleReact : function( to, from, o ) {
        var module            = this,
            control           = module.control,
            isItemUpdate    = ( _.size(from.items) == _.size(to.items) ) && ! _.isEmpty( _.difference(to.items, from.items) ),
            isColumnUpdate  = to.column_id != from.column_id,
            refreshPreview    = function() {
                  module.control.previewer.refresh();
            };
        control.updateModulesCollection( {
              module : $.extend( true, {}, to ),
              data : o//useful to pass contextual info when a change happens
        } );
  },
  getModuleSection : function() {
        return this.section;
  },
  isInSektion : function() {
        var module = this;
        return _.has( module, 'sektion_id' );
  },
  isMultiItem : function() {
        return api.CZR_Helpers.isMultiItemModule( null, this );
  },
  isCrud : function() {
        return api.CZR_Helpers.isCrudModule( null, this );
  },

  hasModOpt : function() {
        return api.CZR_Helpers.hasModuleModOpt( null, this );
  },
  instantiateModOpt : function() {
        var module = this;
        var modOpt_candidate = module.prepareModOptForAPI( module().modOpt || {} );
        module.czr_ModOpt = new module.modOptConstructor( modOpt_candidate );
        module.czr_ModOpt.ready();
        module.czr_ModOpt.callbacks.add( function( to, from, data ) {
              var _current_model = module(),
                  _new_model = $.extend( true, {}, _current_model );
              _new_model.modOpt = to;
              module.isDirty(true);
              module( _new_model, data );
        });
  },
  prepareModOptForAPI : function( modOpt_candidate ) {
        var module = this,
            api_ready_modOpt = {};
        modOpt_candidate = _.isObject( modOpt_candidate ) ? modOpt_candidate : {};

        _.each( module.defaultAPImodOptModel, function( _value, _key ) {
              var _candidate_val = modOpt_candidate[_key];
              switch( _key ) {
                    case 'initial_modOpt_model' :
                        _.each( module.getDefaultModOptModel() , function( _value, _property ) {
                              if ( ! _.has( modOpt_candidate, _property) )
                                 modOpt_candidate[_property] = _value;
                        });
                        api_ready_modOpt[_key] = modOpt_candidate;

                    break;
                    case  'defaultModOptModel' :
                        api_ready_modOpt[_key] = _.clone( module.defaultModOptModel );
                    break;
                    case  'control' :
                        api_ready_modOpt[_key] = module.control;
                    break;
                    case  'module' :
                        api_ready_modOpt[_key] = module;
                    break;
              }//switch
        });
        return api_ready_modOpt;
  },
  getDefaultModOptModel : function( id ) {
          var module = this;
          return $.extend( _.clone( module.defaultModOptModel ), { is_mod_opt : true } );
  }
});//$.extend//CZRBaseControlMths//MULTI CONTROL CLASS

var CZRModuleMths = CZRModuleMths || {};

$.extend( CZRModuleMths, {
  populateSavedItemCollection : function() {
          var module = this, _saved_items = [];
          if ( ! _.isArray( module().items ) ) {
              throw new Error( 'populateSavedItemCollection : The saved items collection must be an array in module :' + module.id );
          }
          _.each( module().items, function( item_candidate , key ) {
                if ( _.has( item_candidate, 'id') && ! _.has( item_candidate, 'is_mod_opt' ) ) {
                      _saved_items.push( item_candidate );
                }
          });
          _.each( _saved_items, function( item_candidate , key ) {
                module.instantiateItem( item_candidate ).ready();
          });
          _.each( _saved_items, function( _item ) {
                if ( _.isUndefined( _.findWhere( module.itemCollection(), _item.id ) ) ) {
                      throw new Error( 'populateSavedItemCollection : The saved items have not been properly populated in module : ' + module.id );
                }
          });

          module.trigger('items-collection-populated');
  },


  instantiateItem : function( item, is_added_by_user ) {
          var module = this;
          item_candidate = module.prepareItemForAPI( item );
          if ( ! _.has( item_candidate, 'id' ) ) {
            throw new Error('CZRModule::instantiateItem() : an item has no id and could not be added in the collection of : ' + this.id );
          }
          if ( module.czr_Item.has( item_candidate.id ) ) {
              throw new Error('CZRModule::instantiateItem() : the following item id ' + item_candidate.id + ' already exists in module.czr_Item() for module ' + this.id  );
          }
          module.czr_Item.add( item_candidate.id, new module.itemConstructor( item_candidate.id, item_candidate ) );

          if ( ! module.czr_Item.has( item_candidate.id ) ) {
              throw new Error('CZRModule::instantiateItem() : instantiation failed for item id ' + item_candidate.id + ' for module ' + this.id  );
          }
          return module.czr_Item( item_candidate.id );
  },
  prepareItemForAPI : function( item_candidate ) {
          var module = this,
              api_ready_item = {};
          item_candidate = _.isObject( item_candidate ) ? item_candidate : {};

          _.each( module.defaultAPIitemModel, function( _value, _key ) {
                var _candidate_val = item_candidate[_key];
                switch( _key ) {
                      case 'id' :
                          if ( _.isEmpty( _candidate_val ) ) {
                              api_ready_item[_key] = module.generateItemId( module.module_type );
                          } else {
                              api_ready_item[_key] = _candidate_val;
                          }
                      break;
                      case 'initial_item_model' :
                          _.each( module.getDefaultItemModel() , function( _value, _property ) {
                                if ( ! _.has( item_candidate, _property) )
                                   item_candidate[_property] = _value;
                          });
                          api_ready_item[_key] = item_candidate;

                      break;
                      case  'defaultItemModel' :
                          api_ready_item[_key] = _.clone( module.defaultItemModel );
                      break;
                      case  'control' :
                          api_ready_item[_key] = module.control;
                      break;
                      case  'module' :
                          api_ready_item[_key] = module;
                      break;
                      case 'is_added_by_user' :
                          api_ready_item[_key] =  _.isBoolean( _candidate_val ) ? _candidate_val : false;
                      break;
                }//switch
          });
          if ( ! _.has( api_ready_item, 'id' ) ) {
                api_ready_item.id = module.generateItemId( module.module_type );
          }
          api_ready_item.initial_item_model.id = api_ready_item.id;

          return api_ready_item;
  },
  generateItemId : function( module_type, key, i ) {
          i = i || 1;
          if ( i > 100 ) {
                throw new Error('Infinite loop when generating of a module id.');
          }
          var module = this;
          key = key || module._getNextItemKeyInCollection();
          var id_candidate = module_type + '_' + key;
          if ( ! _.has(module, 'itemCollection') || ! _.isArray( module.itemCollection() ) ) {
                throw new Error('The item collection does not exist or is not properly set in module : ' + module.id );
          }
          if ( module.isItemRegistered( id_candidate ) ) {
            key++; i++;
            return module.generateItemId( module_type, key, i );
          }
          return id_candidate;
  },
  _getNextItemKeyInCollection : function() {
          var module = this,
            _max_mod_key = {},
            _next_key = 0;
          if ( ! _.isEmpty( module.itemCollection() ) ) {
              _max_mod_key = _.max( module.itemCollection(), function( _mod ) {
                  return parseInt( _mod.id.replace(/[^\/\d]/g,''), 10 );
              });
              _next_key = parseInt( _max_mod_key.id.replace(/[^\/\d]/g,''), 10 ) + 1;
          }
          return _next_key;
  },
  isItemRegistered : function( id_candidate ) {
        var module = this;
        return ! _.isUndefined( _.findWhere( module.itemCollection(), { id : id_candidate}) );
  },
  updateItemsCollection : function( obj ) {
          var module = this,
              _current_collection = module.itemCollection();
              _new_collection = _.clone(_current_collection);
          if ( _.has( obj, 'collection' ) ) {
                module.itemCollection.set( obj.collection );
                return;
          }

          if ( ! _.has(obj, 'item') ) {
              throw new Error('updateItemsCollection, no item provided ' + module.control.id + '. Aborting');
          }
          var item = _.clone(obj.item);
          if ( _.findWhere( _new_collection, { id : item.id } ) ) {
                _.each( _current_collection , function( _item, _ind ) {
                      if ( _item.id != item.id )
                        return;
                      _new_collection[_ind] = item;
                });
          }
          else {
              _new_collection.push(item);
          }
          module.itemCollection.set( _new_collection );
  },
  _getSortedDOMItemCollection : function( ) {
          var module = this,
              _old_collection = _.clone( module.itemCollection() ),
              _new_collection = [],
              dfd = $.Deferred();
          $( '.' + module.control.css_attr.single_item, module.container ).each( function( _index ) {
                var _item = _.findWhere( _old_collection, {id: $(this).attr('data-id') });
                if ( ! _item )
                  return;

                _new_collection[_index] = _item;
          });

          if ( _old_collection.length != _new_collection.length ) {
              throw new Error('There was a problem when re-building the item collection from the DOM in module : ' + module.id );
          }
          return dfd.resolve( _new_collection ).promise();
  },
  refreshItemCollection : function() {
        var module = this;
        module.czr_Item.each( function( _itm ) {
              $.when( module.czr_Item( _itm.id ).container.remove() ).done( function() {
                    module.czr_Item.remove( _itm.id );
              });
        });
        module.itemCollection = new api.Value( [] );
        module.populateSavedItemCollection();
  }
});//$.extend//CZRBaseControlMths//MULTI CONTROL CLASS

var CZRModuleMths = CZRModuleMths || {};

$.extend( CZRModuleMths, {
  getDefaultItemModel : function( id ) {
          var module = this;
          return $.extend( _.clone( module.defaultItemModel ), { id : id || '' } );
  },
  _initNewItem : function( _item , _next_key ) {
          var module = this,
              _new_item = { id : '' },
              _id;
          _next_key = 'undefined' != typeof(_next_key) ? _next_key : _.size( module.itemCollection() );

          if ( _.isNumber(_next_key) ) {
            _id = module.module_type + '_' + _next_key;
          }
          else {
            _id = _next_key;
            _next_key = 0;
          }

          if ( _item && ! _.isEmpty( _item) )
            _new_item = $.extend( _item, { id : _id } );
          else
            _new_item = this.getDefaultItemModel( _id );
          if ( _.has(_new_item, 'id') && module._isItemIdPossible(_id) ) {
                _.map( module.getDefaultItemModel() , function( value, property ){
                      if ( ! _.has(_new_item, property) )
                        _new_item[property] = value;
                });

            return _new_item;
          }
          return module._initNewItem( _new_item, _next_key + 1);
  }

});//$.extend//MULTI CONTROL CLASS
var CZRModuleMths = CZRModuleMths || {};
$.extend( CZRModuleMths, {
  renderModuleParts : function() {
          var module = this,
              $_moduleContentEl = module.isInSektion() ? $( module.container ).find('.czr-mod-content') : $( module.container );
          if ( module.isCrud() ) {
                if ( 0 === $( '#tmpl-' + module.crudModulePart ).length ) {
                  throw new Error('No crud Module Part template for module ' + module.id + '. The template script id should be : #tmpl-' + module.crudModulePart );
                }
                $_moduleContentEl.append( $( wp.template( module.crudModulePart )( {} ) ) );
          }
          var $_module_items_wrapper = $(
            '<ul/>',
            {
              class : [
                module.control.css_attr.items_wrapper,
                module.module_type,
                module.isMultiItem() ? 'multi-item-mod' : 'mono-item-mod',
                module.isCrud() ? 'crud-mod' : 'not-crud-mod'
              ].join(' ')
            }
          );

          $_moduleContentEl.append($_module_items_wrapper);

          return $( $_module_items_wrapper, $_moduleContentEl );
  },
  getTemplateEl : function( type, item_model ) {
          var module = this, _el;
          switch(type) {
                case 'rudItemPart' :
                  _el = module.rudItemPart;
                  break;
                case 'ruItemPart' :
                  _el = module.ruItemPart;
                  break;
                case 'modOptInputList' :
                  _el = module.modOptInputList;
                  break;
                case 'itemInputList' :
                  _el = module.itemInputList;
                  break;
          }
          if ( _.isEmpty(_el) ) {
               throw new Error('No valid template has been found in getTemplateEl() ' + module.id + '. Aborting');
          } else {
              return _el;
          }
  },
  getViewEl : function( id ) {
          var module = this;
          return $( '[data-id = "' + id + '"]', module.container );
  },
  closeAllItems : function( id ) {
          var module = this,
              _current_collection = _.clone( module.itemCollection() ),
              _filtered_collection = _.filter( _current_collection , function( mod) { return mod.id != id; } );

          _.each( _filtered_collection, function( _item ) {
                if ( module.czr_Item.has(_item.id) && 'expanded' == module.czr_Item(_item.id)._getViewState(_item.id) )
                  module.czr_Item(_item.id).czr_ItemState.set( 'closed' ); // => will fire the cb toggleItemExpansion
           } );
          return this;
  },
  _adjustScrollExpandedBlock : function( $_block_el, adjust ) {
          if ( ! $_block_el.length || _.isUndefined( this.getModuleSection() ) )
            return;
          var module = this,
               $_moduleSection = $( '.accordion-section-content', module.section.container ),//was api.section( control.section() )
              _currentScrollTopVal = $_moduleSection.scrollTop(),
              _scrollDownVal,
              _adjust = adjust || 90;

          setTimeout( function() {
                if ( ( $_block_el.offset().top + $_block_el.height() + _adjust ) > $(window.top).height() ) {
                    _scrollDownVal = $_block_el.offset().top + $_block_el.height() + _adjust - $(window.top).height();
                    if ( _scrollDownVal > 0 ) {
                        $_moduleSection.animate({
                            scrollTop:  _currentScrollTopVal + _scrollDownVal
                        }, 500);
                    }
                }
          }, 50);
  },
  closeAllAlerts : function() {
          var module = this;
          $('.' + module.control.css_attr.remove_alert_wrapper, module.container ).each( function() {
                if ( $(this).hasClass('open') ) {
                      $(this).slideToggle( {
                            duration : 100,
                            done : function() {
                              $(this).toggleClass('open' , false );
                              $(this).siblings().find('.' + module.control.css_attr.display_alert_btn).toggleClass('active' , false );
                            }
                      } );
                }
          });
          return this;
  },
  _makeItemsSortable : function(obj) {
          if ( wp.media.isTouchDevice || ! $.fn.sortable )
            return;
          var module = this;
          $( '.' + module.control.css_attr.items_wrapper, module.container ).sortable( {
                handle: '.' + module.control.css_attr.item_sort_handle,
                start: function() {
                      if ( _.has(api, 'czrModulePanelState' ) )
                        api.czrModulePanelState.set(false);
                      if ( _.has(api, 'czrSekSettingsPanelState' ) )
                        api.czrSekSettingsPanelState.set(false);
                },
                update: function( event, ui ) {
                      var _sortedCollectionReact = function() {
                            if ( _.has(module, 'preItem') ) {
                                  module.preItemExpanded.set(false);
                            }

                            module.closeAllItems().closeAllAlerts();
                            if ( 'postMessage' == api(module.control.id).transport  && ! api.CZR_Helpers.hasPartRefresh( module.control.id ) ) {
                                  refreshPreview = _.debounce( refreshPreview, 500 );//500ms are enough
                                  refreshPreview();
                            }
                      };
                      module._getSortedDOMItemCollection()
                            .done( function( _collection_ ) {
                                  module.itemCollection.set( _collection_ );
                            })
                            .then( function() {
                                  _sortedCollectionReact();
                            });
                }//update
              }
          );
  }
});//$.extend//MULTI CONTROL CLASS

var CZRDynModuleMths = CZRDynModuleMths || {};

$.extend( CZRDynModuleMths, {
  initialize: function( id, options ) {
          var module = this;
          api.CZRModule.prototype.initialize.call( module, id, options );
          $.extend( module, {
              itemPreAddEl : ''//is specific for each crud module
          } );
          module.itemAddedMessage = serverControlParams.translatedStrings.successMessage;
          module.userEventMap = new api.Value( [
                {
                    trigger   : 'click keydown',
                    selector  : [ '.' + module.control.css_attr.open_pre_add_btn, '.' + module.control.css_attr.cancel_pre_add_btn ].join(','),
                    name      : 'pre_add_item',
                    actions   : [ 'closeAllItems', 'closeAllAlerts', 'renderPreItemView','setPreItemViewVisibility' ],
                },
                {
                    trigger   : 'click keydown',
                    selector  : '.' + module.control.css_attr.add_new_btn, //'.czr-add-new',
                    name      : 'add_item',
                    actions   : [ 'closeAllAlerts', 'closeAllItems', 'addItem' ],
                }
          ]);//module.userEventMap
  },
  ready : function() {
          var module = this;
          api.consoleLog( 'MODULE READY IN DYN MODULE CLASS : ', module.id );
          module.setupDOMListeners( module.userEventMap() , { dom_el : module.container } );
          module.preItem = new api.Value( module.getDefaultItemModel() );
          module.preItemEmbedded = $.Deferred();//was module.czr_preItem.create('item_content');
          module.preItemEmbedded.done( function() {
                module.setupPreItemInputCollection();
          });
          module.preItemExpanded = new api.Value(false);
          module.preItemExpanded.callbacks.add( function( to, from ) {
                module._togglePreItemViewExpansion( to );
          });

          api.CZRModule.prototype.ready.call( module );//fires the parent
  },//ready()
  setupPreItemInputCollection : function() {
          var module = this;
          module.preItem.czr_Input = new api.Values();
          $('.' + module.control.css_attr.pre_add_wrapper, module.container)
                .find( '.' + module.control.css_attr.sub_set_wrapper)
                .each( function( _index ) {
                      var _id = $(this).find('[data-type]').attr('data-type') || 'sub_set_' + _index;
                      module.preItem.czr_Input.add( _id, new module.inputConstructor( _id, {//api.CZRInput;
                            id : _id,
                            type : $(this).attr('data-input-type'),
                            container : $(this),
                            input_parent : module.preItem,
                            module : module,
                            is_preItemInput : true
                      } ) );
                      module.preItem.czr_Input(_id).ready();
                });//each
  },
  addItem : function(obj) {
          var module = this,
              item = module.preItem(),
              collapsePreItem = function() {
                    module.preItemExpanded.set(false);
                    module._resetPreItemInputs();
                    module.toggleSuccessMessage('off');
              };

          if ( _.isEmpty(item) || ! _.isObject(item) ) {
            throw new Error('addItem : an item should be an object and not empty. In : ' + module.id +'. Aborted.' );
          }
          collapsePreItem = _.debounce( collapsePreItem, 2000 );
          module.instantiateItem( item, true ).ready(); //true == Added by user

          module.czr_Item( item.id ).isReady.then( function() {
                module.toggleSuccessMessage('on');
                collapsePreItem();

                module.trigger('item_added', item );
                if ( 'postMessage' == api(module.control.id).transport && _.has( obj, 'dom_event') && ! _.has( obj.dom_event, 'isTrigger' ) && ! api.CZR_Helpers.hasPartRefresh( module.control.id ) ) {
                  module.control.previewer.refresh();
                }
          });



  },

  _resetPreItemInputs : function() {
          var module = this;
          module.preItem.set( module.getDefaultItemModel() );
          module.preItem.czr_Input.each( function( input_instance ) {
                var _input_id = input_instance.id;
                if ( ! _.has( module.getDefaultItemModel(), _input_id ) )
                  return;
                input_instance.set( module.getDefaultItemModel()._input_id );
          });
  }

});//$.extend//MULTI CONTROL CLASS

var CZRDynModuleMths = CZRDynModuleMths || {};

$.extend( CZRDynModuleMths, {
  renderPreItemView : function( obj ) {
          var module = this;
          if ( 'pending' != module.preItemEmbedded.state() ) //was ! _.isEmpty( module.czr_preItem('item_content')() ) )
            return;
          if ( ! _.has(module, 'itemPreAddEl') ||  0 === $( '#tmpl-' + module.itemPreAddEl ).length )
            return this;
          var pre_add_template = wp.template( module.itemPreAddEl );
          if ( ! pre_add_template  || ! module.container )
            return this;

          var $_pre_add_el = $('.' + module.control.css_attr.pre_add_item_content, module.container );
          $_pre_add_el.prepend( pre_add_template() );
          module.preItemEmbedded.resolve();
  },
  _getPreItemView : function() {
          var module = this;
          return $('.' +  module.control.css_attr.pre_add_item_content, module.container );
  },
  setPreItemViewVisibility : function(obj) {
          var module = this;
          module.preItemExpanded.set( ! module.preItemExpanded() );
  },
  _togglePreItemViewExpansion : function( _is_expanded ) {
          var module = this,
            $_pre_add_el = $( '.' +  module.control.css_attr.pre_add_item_content, module.container );
          $_pre_add_el.slideToggle( {
                duration : 200,
                done : function() {
                      var $_btn = $( '.' +  module.control.css_attr.open_pre_add_btn, module.container );

                      $(this).toggleClass('open' , _is_expanded );
                      if ( _is_expanded )
                        $_btn.find('.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
                      else
                        $_btn.find('.fa').removeClass('fa-minus-square').addClass('fa-plus-square');
                      $_btn.toggleClass( 'active', _is_expanded );
                      $( module.container ).toggleClass(  module.control.css_attr.adding_new, _is_expanded );
                      module._adjustScrollExpandedBlock( $(this), 120 );
              }//done
          } );
  },


  toggleSuccessMessage : function( status ) {
          var module = this,
              _message = module.itemAddedMessage,
              $_pre_add_wrapper = $('.' + module.control.css_attr.pre_add_wrapper, module.container );
              $_success_wrapper = $('.' + module.control.css_attr.pre_add_success, module.container );

          if ( 'on' == status ) {
              $_success_wrapper.find('p').text(_message);
              $_success_wrapper.css('z-index', 1000001 )
                .css('height', $_pre_add_wrapper.height() + 'px' )
                .css('line-height', $_pre_add_wrapper.height() + 'px');
          } else {
              $_success_wrapper.attr('style','');
          }
          module.container.toggleClass('czr-model-added', 'on' == status );
          return this;
  }

});//$.extend//CZRBaseControlMths//extends api.CZRDynModule

var CZRSocialModuleMths = CZRSocialModuleMths || {};

$.extend( CZRSocialModuleMths, {
  initialize: function( id, options ) {
          var module = this;
          api.CZRDynModule.prototype.initialize.call( module, id, options );
          $.extend( module, {
                itemPreAddEl : 'czr-module-social-pre-add-view-content',
                itemInputList : 'czr-module-social-item-content',
                modOptInputList : 'czr-module-social-mod-opt'
          } );


          this.social_icons = [
            '500px',
            'adn',
            'amazon',
            'android',
            'angellist',
            'apple',
            'behance',
            'behance-square',
            'bitbucket',
            'bitbucket-square',
            'black-tie',
            'btc',
            'buysellads',
            'chrome',
            'codepen',
            'codiepie',
            'connectdevelop',
            'contao',
            'dashcube',
            'delicious',
            'deviantart',
            'digg',
            'dribbble',
            'dropbox',
            'drupal',
            'edge',
            'empire',
            'envelope',
            'envelope-o',
            'envelope-square',
            'expeditedssl',
            'facebook',
            'facebook-f (alias)',
            'facebook-official',
            'facebook-square',
            'firefox',
            'flickr',
            'fonticons',
            'fort-awesome',
            'forumbee',
            'foursquare',
            'get-pocket',
            'gg',
            'gg-circle',
            'git',
            'github',
            'github-alt',
            'github-square',
            'gitlab',
            'git-square',
            'google',
            'google-plus',
            'google-plus-circle',
            'google-plus-official',
            'google-plus-square',
            'google-wallet',
            'gratipay',
            'hacker-news',
            'houzz',
            'instagram',
            'internet-explorer',
            'ioxhost',
            'joomla',
            'jsfiddle',
            'lastfm',
            'lastfm-square',
            'leanpub',
            'linkedin',
            'linkedin-square',
            'linux',
            'maxcdn',
            'meanpath',
            'medium',
            'mixcloud',
            'mobile',
            'modx',
            'odnoklassniki',
            'odnoklassniki-square',
            'opencart',
            'openid',
            'opera',
            'optin-monster',
            'pagelines',
            'paypal',
            'phone',
            'phone-square',
            'pied-piper',
            'pied-piper-alt',
            'pinterest',
            'pinterest-p',
            'pinterest-square',
            'product-hunt',
            'qq',
            'rebel',
            'reddit',
            'reddit-alien',
            'reddit-square',
            'renren',
            'rss',
            'rss-square',
            'safari',
            'scribd',
            'sellsy',
            'share-alt',
            'share-alt-square',
            'shirtsinbulk',
            'simplybuilt',
            'skyatlas',
            'skype',
            'slack',
            'slideshare',
            'snapchat',
            'soundcloud',
            'spotify',
            'stack-exchange',
            'stack-overflow',
            'steam',
            'steam-square',
            'stumbleupon',
            'stumbleupon-circle',
            'telegram',
            'tencent-weibo',
            'trello',
            'tripadvisor',
            'tumblr',
            'tumblr-square',
            'twitch',
            'twitter',
            'twitter-square',
            'usb',
            'viacoin',
            'vimeo',
            'vimeo-square',
            'vine',
            'vk',
            'weibo',
            'weixin',
            'whatsapp',
            'wikipedia-w',
            'windows',
            'wordpress',
            'xing',
            'xing-square',
            'yahoo',
            'y-combinator',
            'yelp',
            'youtube',
            'youtube-play',
            'youtube-square'
          ];
          module.inputConstructor = api.CZRInput.extend( module.CZRSocialsInputMths || {} );
          module.itemConstructor = api.CZRItem.extend( module.CZRSocialsItem || {} );
          this.defaultModOptModel = {
              is_mod_opt : true,
              module_id : module.id,
              'social-size' : serverControlParams.social_el_params.defaultSocialSize || 14
          };
          this.defaultItemModel = {
                id : '',
                title : '' ,
                'social-icon' : '',
                'social-link' : '',
                'social-color' : serverControlParams.social_el_params.defaultSocialColor,
                'social-target' : 1
          };
          this.itemAddedMessage = serverControlParams.translatedStrings.socialLinkAdded;
          if ( _.has( api, 'czr_activeSectionId' ) && module.control.section() == api.czr_activeSectionId() && 'resolved' != module.isReady.state() ) {
                module.ready();
          }

          api.section( module.control.section() ).expanded.bind(function(to) {
                if ( 'resolved' != module.isReady.state() ) {
                      module.ready();
                }
          });

          module.isReady.then( function() {
                module.preItem.bind( function( to, from ) {
                      if ( ! _.has(to, 'social-icon') )
                        return;
                      if ( _.isEqual( to['social-icon'], from['social-icon'] ) )
                        return;
                      module.updateItemModel( module.preItem, true );
                });
          });
  },//initialize
  updateItemModel : function( item_instance, is_preItem ) {
          var item = item_instance;
          is_preItem = is_preItem || false;
          if ( ! _.has( item(), 'social-icon') || _.isEmpty( item()['social-icon'] ) )
            return;

          var _new_model, _new_title, _new_color;

          _new_model  = $.extend( true, {}, item() );//always safer to deep clone ( alternative to _.clone() ) => we don't know how nested this object might be in the future
          _new_title  = this.getTitleFromIcon( _new_model['social-icon'] );
          _new_color  = serverControlParams.social_el_params.defaultSocialColor;
          if ( ! is_preItem && item.czr_Input.has( 'social-color' ) )
            _new_color = item.czr_Input('social-color')();
          _new_title = [ serverControlParams.translatedStrings.followUs, _new_title].join(' ');

          if ( is_preItem ) {
                _new_model = $.extend( _new_model, { title : _new_title, 'social-color' : _new_color } );
                item.set( _new_model );
          } else {
                item.czr_Input('title').set( _new_title );
                if ( item.czr_Input('social-color') ) { //optional
                  item.czr_Input('social-color').set( _new_color );
                }
          }
  },
  getTitleFromIcon : function( icon ) {
          return api.CZR_Helpers.capitalize( icon.replace('fa-', '').replace('envelope', 'email') );
  },

  getIconFromTitle : function( title ) {
          return  'fa-' . title.toLowerCase().replace('envelope', 'email');
  },







  CZRSocialsInputMths : {
          setupSelect : function() {
                var input        = this,
                    item         = input.input_parent,
                    module       = input.module,
                    socialList   = module.social_icons,
                    _model       = item(),
                    is_preItem   = _.isEmpty(_model.id);
                if ( is_preItem ) {
                      socialList = _.union( [ serverControlParams.translatedStrings.selectSocialIcon ], socialList );
                }
                _.each( socialList , function( icon_name, k ) {
                      var _value = ( is_preItem && 0 === k ) ? '' : 'fa-' + icon_name.toLowerCase(),
                          _attributes = {
                                value : _value,
                                html: module.getTitleFromIcon( icon_name )
                          };
                      if ( _value == _model['social-icon'] )
                        $.extend( _attributes, { selected : "selected" } );

                      $( 'select[data-type="social-icon"]', input.container ).append( $('<option>', _attributes) );
                });

                function addIcon( state ) {
                      if (! state.id) { return state.text; }
                      var $state = $(
                        '<span class="fa ' + state.element.value.toLowerCase() + '">&nbsp;&nbsp;' + state.text + '</span>'
                      );
                      return $state;
                }
                $( 'select[data-type="social-icon"]', input.container ).select2( {
                        templateResult: addIcon,
                        templateSelection: addIcon
                });
        },

        setupColorPicker : function( obj ) {
                var input      = this,
                    item       = input.input_parent,
                    module     = input.module,
                    $el        = $( 'input[data-type="social-color"]', input.container );

                $el.iris( {
                          palettes: true,
                          hide:false,
                          defaultColor : serverControlParams.social_el_params.defaultSocialColor || 'rgba(255,255,255,0.7)',
                          change : function( e, o ) {
                                if ( _.has(o, 'color') && 16777215 == o.color._color )
                                  $(this).val( serverControlParams.social_el_params.defaultSocialColor || 'rgba(255,255,255,0.7)' );
                                else
                                  $(this).val( o.color.toString() );

                                $(this).trigger('colorpickerchange').trigger('change');
                          }
                });
                $el.closest('div').on('click keydown', function() {
                      module._adjustScrollExpandedBlock( input.container );
                });
        }

  },//CZRSocialsInputMths









  CZRSocialsItem : {
          ready : function() {
                var item = this;
                api.CZRItem.prototype.ready.call( item );
                item.bind('social-icon:changed', function(){
                      item.module.updateItemModel( item );
                });
          },


          _buildTitle : function( title, icon, color ) {
                  var item = this,
                      module     = item.module;

                  title = title || ( 'string' === typeof(icon) ? api.CZR_Helpers.capitalize( icon.replace( 'fa-', '') ) : '' );
                  title = api.CZR_Helpers.truncate(title, 20);
                  icon = icon || 'fa-' + module.social_icons[0];
                  color = color || serverControlParams.social_el_params.defaultSocialColor;

                  return '<div><span class="fa ' + icon + '" style="color:' + color + '"></span> ' + title + '</div>';
          },
          writeItemViewTitle : function( model ) {
                  var item = this,
                      module     = item.module,
                      _model = model || item(),
                      _title = module.getTitleFromIcon( _model['social-icon'] );

                  $( '.' + module.control.css_attr.item_title , item.container ).html(
                    item._buildTitle( _title, _model['social-icon'], _model['social-color'] )
                  );
          }
  },//CZRSocialsItem

});

var CZRWidgetAreaModuleMths = CZRWidgetAreaModuleMths || {};

$.extend( CZRWidgetAreaModuleMths, {
  initialize: function( id, constructorOptions ) {
          var module = this;

          api.CZRDynModule.prototype.initialize.call( this, id, constructorOptions );
          $.extend( module, {
                itemPreAddEl : 'czr-module-widgets-pre-add-view-content',
                itemInputList : 'czr-module-widgets-item-input-list',
                itemInputListReduced : 'czr-module-widgets-item-input-list-reduced',
                ruItemPart : 'czr-module-widgets-ru-item-part'
          } );
          module.inputConstructor = api.CZRInput.extend( module.CZRWZonesInputMths || {} );
          module.itemConstructor = api.CZRItem.extend( module.CZRWZonesItem || {} );

          module.serverParams = serverControlParams.widget_area_el_params || {};
          module.contexts = _.has( module.serverParams , 'sidebar_contexts') ? module.serverParams.sidebar_contexts : {};
          module.context_match_map = {
                  is_404 : '404',
                  is_category : 'archive-category',
                  is_home : 'home',
                  is_page : 'page',
                  is_search : 'search',
                  is_single : 'single'
          };


          module.locations = _.has( module.serverParams , 'sidebar_locations') ? module.serverParams.sidebar_locations : {};
          module.defaultItemModel = {
                  id : '',
                  title : serverControlParams.translatedStrings.widgetZone,
                  contexts : _.without( _.keys(module.contexts), '_all_' ),//the server list of contexts is an object, we only need the keys, whitout _all_
                  locations : [ module.serverParams.defaultWidgetLocation ],
                  description : ''
          };
          this.itemAddedMessage = serverControlParams.translatedStrings.widgetZoneAdded;
          if ( ! _.has( api, 'sidebar_insights' ) ) {
                api.sidebar_insights = new api.Values();
                api.sidebar_insights.create('candidates');//will store the sidebar candidates on preview refresh
                api.sidebar_insights.create('actives');//will record the refreshed active list of active sidebars sent from the preview
                api.sidebar_insights.create('inactives');
                api.sidebar_insights.create('registered');
                api.sidebar_insights.create('available_locations');
          }


          this.listenToSidebarInsights();
          api.czr_widgetZoneSettings = api.czr_widgetZoneSettings || new api.Value();
          api.czr_widgetZoneSettings.bind( function( updated_data_sent_from_preview , from ) {
                  module.isReady.then( function() {
                        _.each( updated_data_sent_from_preview, function( _data, _key ) {
                              api.sidebar_insights( _key ).set( _data );
                        });
                  });
          });
          module.preItem_location_alert_view_state = new api.Value( 'closed');
          module.preItem_location_alert_view_state.callbacks.add( function( to, from ) {
                    module._toggleLocationAlertExpansion( module.container, to );
          });
          module.bind( 'item_added', function( model ) {
                  module.addWidgetSidebar( model );
          });

          module.bind( 'pre_item_api_remove' , function(model) {
                  module.removeWidgetSidebar( model );
          });
          var fixTopMargin = new api.Values();
          fixTopMargin.create('fixed_for_current_session');
          fixTopMargin.create('value');

          api.section(module.serverParams.dynWidgetSection).fixTopMargin = fixTopMargin;
          api.section(module.serverParams.dynWidgetSection).fixTopMargin('fixed_for_current_session').set(false);
          api.panel('widgets').expanded.callbacks.add( function(to, from) {
                module.widgetPanelReact();//setup some visual adjustments, must be ran each time panel is closed or expanded
                if ( 'resolved' == module.isReady.state() )
                  return;
                module.ready();
          });
  },//initialize
  ready : function() {
          var module = this;
          api.CZRDynModule.prototype.ready.call( module );
          module.preItemExpanded.callbacks.add( function( to, from ) {
                if ( ! to )
                  return;
                module.preItem.czr_Input('locations')._setupLocationSelect( true );//true for refresh
                module.preItem.czr_Input('locations').mayBeDisplayModelAlert();
          });
  },
  initializeModuleModel : function( constructorOptions ) {
              var module = this, dfd = $.Deferred();
              constructorOptions.items = _.union( _.has( module.serverParams, 'default_zones' ) ? module.serverParams.default_zones : [], constructorOptions.items );
              return dfd.resolve( constructorOptions ).promise();
  },
















  CZRWZonesInputMths : {
          ready : function() {
                  var input = this;

                  input.bind('locations:changed', function(){
                      input.mayBeDisplayModelAlert();
                  });

                  api.CZRInput.prototype.ready.call( input);
          },
          setupSelect : function() {
                  var input      = this;
                  if ( 'locations' == this.id )
                    this._setupLocationSelect();
                  if ( 'contexts' == this.id )
                    this._setupContextSelect();

          },
          _setupContextSelect : function() {
                  var input      = this,
                      input_contexts = input(),
                      item = input.input_parent,
                      module     = input.module;
                  _.each( module.contexts, function( title, key ) {
                        var _attributes = {
                              value : key,
                              html: title
                            };
                        if ( key == input_contexts || _.contains( input_contexts, key ) )
                          $.extend( _attributes, { selected : "selected" } );

                        $( 'select[data-type="contexts"]', input.container ).append( $('<option>', _attributes) );
                  });
                  $( 'select[data-type="contexts"]', input.container ).select2();
          },
          _setupLocationSelect : function(refresh ) {
                  var input      = this,
                      input_locations = input(),
                      item = input.input_parent,
                      module     = input.module,
                      available_locs = api.sidebar_insights('available_locations')();
                  if ( ! $( 'select[data-type="locations"]', input.container ).children().length ) {
                        _.each( module.locations, function( title, key ) {
                              var _attributes = {
                                    value : key,
                                    html: title
                                  };

                              if ( key == input_locations || _.contains( input_locations, key ) )
                                $.extend( _attributes, { selected : "selected" } );

                              $( 'select[data-type="locations"]', input.container ).append( $('<option>', _attributes) );
                        });
                  }//if

                  function setAvailability( state ) {
                        if (! state.id) { return state.text; }
                        if (  _.contains(available_locs, state.element.value) ) { return state.text; }
                        var $state = $(
                          '<span class="czr-unavailable-location fa fa-ban" title="' + serverControlParams.translatedStrings.unavailableLocation + '">&nbsp;&nbsp;' + state.text + '</span>'
                        );
                        return $state;
                  }

                  if ( refresh ) {
                        $( 'select[data-type="locations"]', input.container ).select2( 'destroy' );
                  }
                  $( 'select[data-type="locations"]', input.container ).select2( {
                    templateResult: setAvailability,
                    templateSelection: setAvailability
                  });
          },
          mayBeDisplayModelAlert : function() {
                  var input      = this,
                      item = input.input_parent,
                      module     = input.module;
                  if ( ! _.has( item(), 'locations') || _.isEmpty( item().locations ) )
                    return;

                  var _selected_locations = $('select[data-type="locations"]', input.container ).val(),
                      available_locs = api.sidebar_insights('available_locations')(),
                      _unavailable = _.filter( _selected_locations, function( loc ) {
                        return ! _.contains(available_locs, loc);
                      });
                  if ( ! _.has( item(), 'id' ) || _.isEmpty( item().id ) ) {
                        module.preItem_location_alert_view_state.set( ! _.isEmpty( _unavailable ) ? 'expanded' : 'closed' );
                  } else {
                        item.czr_itemLocationAlert.set( ! _.isEmpty( _unavailable ) ? 'expanded' : 'closed' );
                  }
          }
  },//CZRWZonesInputMths















  CZRWZonesItem : {
          initialize : function( id, options ) {
                  var item = this,
                      module = item.module;
                  item.czr_itemLocationAlert = new api.Value();

                  api.CZRItem.prototype.initialize.call( item, null, options );
          },
          itemWrapperViewSetup : function() {
                  var item = this,
                      module = item.module;

                  api.CZRItem.prototype.itemWrapperViewSetup.call(item);
                  item.czr_itemLocationAlert.set('closed');
                  item.czr_itemLocationAlert.callbacks.add( function( to, from ) {
                        module._toggleLocationAlertExpansion( item.container , to );
                  });
                  item.writeSubtitleInfos(item());
                  item.czr_ItemState.callbacks.add( function( to, from ) {
                        if ( -1 == to.indexOf('expanded') )//can take the expanded_noscroll value !
                          return;
                        item.bind('contentRendered', function() {
                              item.czr_Input('locations')._setupLocationSelect( true );//true for refresh
                              item.czr_Input('locations').mayBeDisplayModelAlert();
                        });

                  });
          },
          itemReact : function(to, from) {
                  var item = this;
                  api.CZRItem.prototype.itemReact.call(item, to, from);

                  item.writeSubtitleInfos(to);
                  item.updateSectionTitle(to).setModelUpdateTimer();
          },
          writeSubtitleInfos : function(model) {
                  var item = this,
                      module = item.module,
                      _model = _.clone( model || item() ),
                      _locations = [],
                      _contexts = [],
                      _html = '';

                  if ( ! item.container.length )
                    return this;
                  _model.locations =_.isString(_model.locations) ? [_model.locations] : _model.locations;
                  _.each( _model.locations, function( loc ) {
                        if ( _.has( module.locations , loc ) )
                          _locations.push(module.locations[loc]);
                        else
                          _locations.push(loc);
                    }
                  );
                  _model.contexts =_.isString(_model.contexts) ? [_model.contexts] : _model.contexts;
                  if ( item._hasModelAllContexts( model ) ) {
                    _contexts.push(module.contexts._all_);
                  } else {
                    _.each( _model.contexts, function( con ) {
                            if ( _.has( module.contexts , con ) )
                              _contexts.push(module.contexts[con]);
                            else
                              _contexts.push(con);
                          }
                    );
                  }
                  var _locationText = serverControlParams.translatedStrings.locations,
                      _contextText = serverControlParams.translatedStrings.contexts,
                      _notsetText = serverControlParams.translatedStrings.notset;

                  _locations = _.isEmpty( _locations ) ? '<span style="font-weight: bold;">' + _notsetText + '</span>' : _locations.join(', ');
                  _contexts = _.isEmpty( _contexts ) ? '<span style="font-weight: bold;">' + _notsetText + '</span>' : _contexts.join(', ');

                  _html = '<u>' + _locationText + '</u> : ' + _locations + ' <strong>|</strong> <u>' + _contextText + '</u> : ' + _contexts;

                  if ( ! $('.czr-zone-infos', item.container ).length ) {
                        var $_zone_infos = $('<div/>', {
                          class : [ 'czr-zone-infos' , module.control.css_attr.item_sort_handle ].join(' '),
                          html : _html
                        });
                        $( '.' + module.control.css_attr.item_btns, item.container ).after($_zone_infos);
                  } else {
                        $('.czr-zone-infos', item.container ).html(_html);
                  }

                  return this;
          },//writeSubtitleInfos
          updateSectionTitle : function(model) {
                  var _sidebar_id = 'sidebar-widgets-' + model.id,
                      _new_title  = model.title;
                  if ( ! api.section.has(_sidebar_id) )
                    return this;
                  $('.accordion-section-title', api.section(_sidebar_id).container ).text(_new_title);
                  $('.customize-section-title h3', api.section(_sidebar_id).container ).html(
                    '<span class="customize-action">' + api.section(_sidebar_id).params.customizeAction + '</span>' + _new_title
                  );
                  return this;
          },
          setModelUpdateTimer : function() {
                  var item = this,
                      module = item.module;

                  clearTimeout( $.data(this, 'modelUpdateTimer') );
                  $.data(
                      this,
                      'modelUpdateTimer',
                      setTimeout( function() {
                          module.control.refreshPreview();
                      } , 1000)
                  );//$.data
          },
          _hasModelAllContexts : function( model ) {
                  var item = this,
                      module = item.module,
                      moduleContexts = _.keys(module.contexts);

                  model = model || this();

                  if ( ! _.has(model, 'contexts') )
                    return;

                  if ( _.contains( model.contexts, '_all_') )
                    return true;
                  return _.isEmpty( _.difference( _.without(moduleContexts, '_all_') , model.contexts ) );
          },
          _getMatchingContexts : function( defaults ) {
                  var module = this,
                      _current = api.czr_wpQueryInfos().conditional_tags || {},
                      _matched = _.filter( module.context_match_map, function( hu, wp ) { return true === _current[wp]; } );

                  return _.isEmpty( _matched ) ? defaults : _matched;
          }
  },//CZRWZonesItem
  addWidgetSidebar : function( model, sidebar_data ) {
          if ( ! _.isObject(model) && _.isEmpty(sidebar_data) ) {
            throw new Error('No valid input were provided to add a new Widget Zone.');
          }
          var module = this,
              _model        = ! _.isEmpty(model) ? _.clone(model) : sidebar_data,
              _new_sidebar  = _.isEmpty(model) ? sidebar_data : $.extend(
                _.clone( _.findWhere( api.Widgets.data.registeredSidebars, { id: module.serverParams.defaultWidgetSidebar } ) ),
                {
                  name : _model.title,
                  id : _model.id
                }
              );
          api.Widgets.registeredSidebars.add( _new_sidebar );
          var _params = $.extend(
                  _.clone( api.section( "sidebar-widgets-" + module.serverParams.defaultWidgetSidebar ).params ),
                  {
                    id : "sidebar-widgets-" + _model.id,
                    instanceNumber: _.max(api.settings.sections, function(sec){ return sec.instanceNumber; }).instanceNumber + 1,
                    sidebarId: _new_sidebar.id,
                    title: _new_sidebar.name,
                    description : 'undefined' != typeof(sidebar_data) ? sidebar_data.description : api.section( "sidebar-widgets-" + module.serverParams.defaultWidgetSidebar ).params.description,
                    priority: _.max( _.omit( api.settings.sections, module.serverParams.dynWidgetSection), function(sec){ return sec.instanceNumber; }).priority + 1,
                  }
          );

          api.section.add( _params.id, new api.sectionConstructor[ _params.type ]( _params.id ,{ params : _params } ) );
          api.settings.sections[ _params.id ] = _params.id;
          var _new_set_id = 'sidebars_widgets['+_model.id+']',
              _new_set    = $.extend(
                _.clone( api.settings.settings['sidebars_widgets[' + module.serverParams.defaultWidgetSidebar + ']'] ),
                {
                  value:[]
                }
              );
          api.settings.settings[ _new_set_id ] = _new_set;
          api.create( _new_set_id, _new_set_id, _new_set.value, {
                  transport: _new_set.transport,
                  previewer: api.previewer,
                  dirty: false
          } );
          var _cloned_control = $.extend(
                    _.clone( api.settings.controls['sidebars_widgets[' + module.serverParams.defaultWidgetSidebar + ']'] ),
                    {
                      settings : { default : _new_set_id }
                }),
              _new_control = {};
          _.each( _cloned_control, function( param, key ) {
                  if ( 'string' == typeof(param) ) {
                    param = param.replace( module.serverParams.defaultWidgetSidebar , _model.id );
                  }
                  _new_control[key] = param;
          });
          _new_control.instanceNumber = _.max(api.settings.controls, function(con){ return con.instanceNumber; }).instanceNumber + 1;
          api.settings.controls[_new_set_id] = _new_control;
          api.control.add( _new_set_id, new api.controlConstructor[ _new_control.type ]( _new_set_id, {
                  params: _new_control,
                  previewer: api.previewer
          } ) );
          if ( _.has(this, 'container') )
            this.container.trigger( 'widget_zone_created', { model : _model, section_id : "sidebar-widgets-" + _model.id , setting_id : _new_set_id });

  },//addWidgetSidebar
  removeWidgetSidebar : function( model ) {
          var module = this;
          if ( ! _.isObject(model) || _.isEmpty(model) ) {
            throw new Error('No valid data were provided to remove a Widget Zone.');
          }
          api.Widgets.registeredSidebars.remove( model.id );
          if ( api.section.has("sidebar-widgets-" + model.id) ) {
                  api.section("sidebar-widgets-" + model.id).container.remove();
                  api.section.remove( "sidebar-widgets-" + model.id );
                  delete api.settings.sections[ "sidebar-widgets-" + model.id ];
          }
          if ( api.has('sidebars_widgets['+model.id+']') ) {
                  api.remove( 'sidebars_widgets['+model.id+']' );
                  delete api.settings.settings['sidebars_widgets['+model.id+']'];
          }
          if ( api.control.has('sidebars_widgets['+model.id+']') ) {
                  api.control( 'sidebars_widgets['+model.id+']' ).container.remove();
                  api.control.remove( 'sidebars_widgets['+model.id+']' );
                  delete api.settings.controls['sidebars_widgets['+model.id+']'];
          }
          var _refresh = function() {
            api.previewer.refresh();
          };
          _refresh = _.debounce( _refresh, 500 );
          $.when( _refresh() ).done( function() {
                module.trigger( 'widget_zone_removed',
                      {
                            model : model,
                            section_id : "sidebar-widgets-" + model.id ,
                            setting_id : 'sidebars_widgets['+model.id+']'
                      }
                );
          });


  },
  widgetPanelReact : function() {
          var module = this;
          var _top_margin = api.panel('widgets').container.find( '.control-panel-content' ).css('margin-top');
          api.section(module.serverParams.dynWidgetSection).fixTopMargin('value').set( _top_margin );

          var _section_content = api.section(module.serverParams.dynWidgetSection).container.find( '.accordion-section-content' ),
            _panel_content = api.panel('widgets').container.find( '.control-panel-content' ),
            _set_margins = function() {
                  _section_content.css( 'margin-top', '' );
                  _panel_content.css('margin-top', api.section(module.serverParams.dynWidgetSection).fixTopMargin('value')() );
            };
          api.bind( 'pane-contents-reflowed', _.debounce( function() {
                  _set_margins();
          }, 150 ) );
          module.closeAllItems().closeAllAlerts();
          if ( _.has( module, 'preItemExpanded' ) )
            module.preItemExpanded.set(false);
  },//widgetPanelReact()
  widgetSectionReact : function( to, from ) {
          var module = this,
              section =  api.section(module.serverParams.dynWidgetSection),
              container = section.container.closest( '.wp-full-overlay-sidebar-content' ),
              content = section.container.find( '.accordion-section-content' ),
              overlay = section.container.closest( '.wp-full-overlay' ),
              backBtn = section.container.find( '.customize-section-back' ),
              sectionTitle = section.container.find( '.accordion-section-title' ).first(),
              headerActionsHeight = $( '#customize-header-actions' ).height(),
              resizeContentHeight, expand, position, scroll;

          if ( to ) {
            overlay.removeClass( 'section-open' );
            content.css( 'height', 'auto' );
            sectionTitle.attr( 'tabindex', '0' );
            content.css( 'margin-top', '' );
            container.scrollTop( 0 );
          }

          module.closeAllItems().closeAllAlerts();

          content.slideToggle();
  },
  listenToSidebarInsights : function() {
          var module = this;
          api.sidebar_insights('registered').callbacks.add( function( _registered_zones ) {
                  var _current_collection = _.clone( module.itemCollection() );
                  _.map(_current_collection, function( _model ) {
                    if ( ! module.getViewEl(_model.id).length )
                      return;

                    module.getViewEl(_model.id).css('display' , _.contains( _registered_zones, _model.id ) ? 'block' : 'none' );
                  });
          });
          api.sidebar_insights('inactives').callbacks.add( function( _inactives_zones ) {
                  var _current_collection = _.clone( module.itemCollection() );
                  _.map(_current_collection, function( _model ) {
                    if ( ! module.getViewEl(_model.id).length )
                      return;

                    if ( _.contains( _inactives_zones, _model.id ) ) {
                      module.getViewEl( _model.id ).addClass('inactive');
                      if ( ! module.getViewEl( _model.id ).find('.czr-inactive-alert').length )
                        module.getViewEl( _model.id ).find('.czr-item-title').append(
                          $('<span/>', {class : "czr-inactive-alert", html : " [ " + serverControlParams.translatedStrings.inactiveWidgetZone + " ]" })
                        );
                    }
                    else {
                      module.getViewEl( _model.id ).removeClass('inactive');
                      if ( module.getViewEl( _model.id ).find('.czr-inactive-alert').length )
                        module.getViewEl( _model.id ).find('.czr-inactive-alert').remove();
                    }
                  });
          });
          api.sidebar_insights('candidates').callbacks.add( function(_candidates) {
                  if ( ! _.isArray(_candidates) )
                    return;
                  _.map( _candidates, function( _sidebar ) {
                    if ( ! _.isObject(_sidebar) )
                      return;
                    if ( api.section.has("sidebar-widgets-" +_sidebar.id ) )
                      return;
                    module.addWidgetSidebar( {}, _sidebar );
                    if ( _.has( api.sidebar_insights('actives')(), _sidebar.id ) && api.section.has("sidebar-widgets-" +_sidebar.id ) )
                      api.section( "sidebar-widgets-" +_sidebar.id ).activate();
                  });
          });
  },//listenToSidebarInsights()
  _adjustScrollExpandedBlock : function( $_block_el, adjust ) {
          if ( ! $_block_el.length )
            return;
          var module = this,
              _currentScrollTopVal = $('.wp-full-overlay-sidebar-content').scrollTop(),
              _scrollDownVal,
              _adjust = adjust || 90;
          setTimeout( function() {
              if ( ( $_block_el.offset().top + $_block_el.height() + _adjust ) > $(window.top).height() ) {
                _scrollDownVal = $_block_el.offset().top + $_block_el.height() + _adjust - $(window.top).height();
                $('.wp-full-overlay-sidebar-content').animate({
                    scrollTop:  _currentScrollTopVal + _scrollDownVal
                }, 600);
              }
          }, 50);
  },
  getDefaultItemModel : function(id) {
          var module = this,
              _current_collection = module.itemCollection(),
              _default = _.clone( module.defaultItemModel ),
              _default_contexts = _default.contexts;
          return $.extend( _default, {
              title : 'Widget Zone ' +  ( _.size(_current_collection)*1 + 1 )
            });
  },
  getTemplateEl : function( type, item_model ) {
          var module = this, _el;
          if ( 'rudItemPart' == type ) {
              type = ( _.has(item_model, 'is_builtin') && item_model.is_builtin ) ? 'ruItemPart' : type;
          } else if ( 'itemInputList' == type ) {
              type = ( _.has(item_model, 'is_builtin') && item_model.is_builtin ) ? 'itemInputListReduced' : type;
          }

          switch(type) {
                case 'rudItemPart' :
                  _el = module.rudItemPart;
                    break;
                case 'ruItemPart' :
                  _el = module.ruItemPart;
                  break;
                case 'itemInputList' :
                  _el = module.itemInputList;
                  break;
                case 'itemInputListReduced' :
                  _el = module.itemInputListReduced;
                  break;
          }

          if ( _.isEmpty(_el) ) {
            throw new Error( 'No valid template has been found in getTemplateEl()' );
          } else {
            return _el;
          }
  },






  _toggleLocationAlertExpansion : function($view, to) {
          var $_alert_el = $view.find('.czr-location-alert');

          if ( ! $_alert_el.length ) {
                var _html = [
                  '<span>' + serverControlParams.translatedStrings.locationWarning + '</span>',
                  api.CZR_Helpers.getDocSearchLink( serverControlParams.translatedStrings.locationWarning ),
                ].join('');

                $_alert_el = $('<div/>', {
                      class:'czr-location-alert',
                      html:_html,
                      style:"display:none"
                });

                $('select[data-type="locations"]', $view ).closest('div').after($_alert_el);
          }
          $_alert_el.toggle( 'expanded' == to);
  }


});//$.extend()//extends api.CZRModule
var CZRBodyBgModuleMths = CZRBodyBgModuleMths || {};

$.extend( CZRBodyBgModuleMths, {
  initialize: function( id, options ) {
          var module = this;
          api.CZRModule.prototype.initialize.call( module, id, options );
          $.extend( module, {
                itemInputList : 'czr-module-bodybg-item-content'
          } );
          module.inputConstructor = api.CZRInput.extend( module.CZRBodyBgInputMths || {} );
          module.itemConstructor = api.CZRItem.extend( module.CZBodyBgItemMths || {} );
          module.defaultItemModel = {
                'background-color' : '#eaeaea',
                'background-image' : '',
                'background-repeat' : 'no-repeat',
                'background-attachment' : 'fixed',
                'background-position' : 'center center',
                'background-size' : 'cover'
          };
          api.consoleLog('module ID', module.id );
          if ( _.has( api, 'czr_activeSectionId' ) && module.control.section() == api.czr_activeSectionId() && 'resolved' != module.isReady.state() ) {
             module.ready();
          }
          api.section( module.control.section() ).expanded.bind(function(to) {
                if ( 'resolved' == module.isReady.state() )
                  return;
                module.ready();
          });
  },//initialize



  CZRBodyBgInputMths : {
          setupSelect : function() {
                  var input         = this,
                      _id_param_map = {
                        'background-repeat' : 'bg_repeat_options',
                        'background-attachment' : 'bg_attachment_options',
                        'background-position' : 'bg_position_options'
                      },
                      item          = input.input_parent,
                      serverParams  = serverControlParams.body_bg_module_params,
                      options       = {},
                      module        = input.module;

                  if ( ! _.has( _id_param_map, input.id ) )
                    return;

                  if ( _.isUndefined( serverParams ) || _.isUndefined( serverParams[ _id_param_map[input.id] ] ) )
                    return;
                  options = serverParams[ _id_param_map[input.id] ];
                  if ( _.isEmpty(options) )
                    return;
                  _.each( options, function( title, key ) {
                        var _attributes = {
                              value : key,
                              html: title
                            };
                        if ( key == input() || _.contains( input(), key ) )
                          $.extend( _attributes, { selected : "selected" } );

                        $( 'select[data-type]', input.container ).append( $('<option>', _attributes) );
                  });
                  $( 'select[data-type]', input.container ).select2();

          }
  },


  CZBodyBgItemMths : {
          ready : function() {
                var item = this;
                api.CZRItem.prototype.ready.call( item );

                item.czr_Input('background-image').isReady.done( function( input_instance ) {
                    var set_visibilities = function( bg_val  ) {
                          var is_bg_img_set = ! _.isEmpty( bg_val ) ||_.isNumber( bg_val);
                          _.each( ['background-repeat', 'background-attachment', 'background-position', 'background-size'], function( dep ) {
                                item.czr_Input(dep).container.toggle( is_bg_img_set || false );
                          });
                    };
                    set_visibilities( input_instance() );
                    item.bind('background-image:changed', function(){
                          set_visibilities( item.czr_Input('background-image')() );
                    });
                });
          },

  }
});
(function ( api, $, _ ) {
      api.czrModuleMap = api.czrModuleMap || {};
      $.extend( api.czrModuleMap, {
            czr_widget_areas_module : {
                  mthds : CZRWidgetAreaModuleMths,
                  crud : true,
                  sektion_allowed : false,
                  name : 'Widget Areas'
            },
            czr_social_module : {
                  mthds : CZRSocialModuleMths,
                  crud : true,
                  sortable : true,
                  name : 'Social Icons',
                  has_mod_opt : true
            },
            czr_background : {
                  mthds : CZRBodyBgModuleMths,
                  crud : false,
                  multi_item : false,
                  name : 'Slider'
            }
      });

})( wp.customize, jQuery, _ );//BASE CONTROL CLASS

var CZRBaseControlMths = CZRBaseControlMths || {};

$.extend( CZRBaseControlMths, {

  initialize: function( id, options ) {
          var control = this;
          control.css_attr = _.has( serverControlParams , 'css_attr') ? serverControlParams.css_attr : {};
          api.Control.prototype.initialize.call( control, id, options );
  },
  refreshPreview : function( obj ) {
          this.previewer.refresh();
  }

});//$.extend//CZRBaseControlMths
var CZRBaseModuleControlMths = CZRBaseModuleControlMths || {};

$.extend( CZRBaseModuleControlMths, {

  initialize: function( id, options ) {
          var control = this;

          control.czr_Module = new api.Values();
          control.czr_moduleCollection = new api.Value();
          control.czr_moduleCollection.set([]);
          control.moduleCollectionReady = $.Deferred();
          control.moduleCollectionReady.done( function( obj ) {
                if ( ! control.isMultiModuleControl( options.params ) ) {
                }
                control.czr_moduleCollection.callbacks.add( function() { return control.moduleCollectionReact.apply( control, arguments ); } );
          } );
          if ( control.isMultiModuleControl( options.params ) ) {
                control.syncSektionModule = new api.Value();
          }

          api.CZRBaseControl.prototype.initialize.call( control, id, options );
          api.section( control.section() ).expanded.bind(function(to) {
                control.czr_Module.each( function( _mod ){
                      _mod.closeAllItems().closeAllAlerts();
                      if ( _.has( _mod, 'preItem' ) ) {
                            _mod.preItemExpanded(false);
                      }
                });
          });

  },
  ready : function() {
          var control = this;
          if ( control.isMultiModuleControl() ) {
                control.syncSektionModule.bind( function( sektion_module_instance, from) {
                      if ( 'resolved' == control.moduleCollectionReady.state() )
                        return;
                      control.registerModulesOnInit( sektion_module_instance );
                      control.moduleCollectionReady.resolve();
                });
          } else {
                var single_module = {};
                _.each( control.getSavedModules() , function( _mod, _key ) {
                      single_module = _mod;
                      control.instantiateModule( _mod, {} );
                      control.container.attr('data-module', _mod.id );
                });
                control.moduleCollectionReady.resolve( single_module );
          }
          control.bind( 'user-module-candidate', function( _module ) {
                control.instantiateModule( _module, {} ).ready( _module.is_added_by_user ); //module, constructor
          });
  },
  getDefaultModuleApiModel : function() {
          var commonAPIModel = {
                id : '',//module.id,
                module_type : '',//module.module_type,
                modOpt : {},//the module modOpt property, typically high level properties that area applied to all items of the module
                items   : [],//$.extend( true, {}, module.items ),
                crud : false,
                multi_item : false,
                sortable : false,//<= a module can be multi-item but not necessarily sortable
                control : {},//control,
          };
          if ( ! this.isMultiModuleControl() ) {
              return $.extend( commonAPIModel, {
                  section : ''//id of the control section
              } );
          } else {
              return $.extend( commonAPIModel, {
                  column_id : '',//a string like col_7
                  sektion : {},// => the sektion instance
                  sektion_id : '',
                  is_added_by_user : false,
                  dirty : false
              } );
          }
  },
  getDefaultModuleDBModel : function() {
          var commonDBModel = {
                items   : [],//$.extend( true, {}, module.items ),
          };
          if ( this.isMultiModuleControl() ) {
              return $.extend( commonDBModel, {
                  id : '',
                  module_type : '',
                  column_id : '',
                  sektion_id : '',
                  dirty : false
              } );
          } else {
              return commonDBModel;
          }
  },
  isMultiModuleControl : function( params ) {
          return 'czr_multi_module' == ( params || this.params ).type;
  },
  getSyncCollectionControl : function() {
        var control = this;
        if ( _.isUndefined( control.params.syncCollection ) ) {
            throw new Error( 'Control ' + control.id + ' has no synchronized sektion control defined.');
        }
        return api.control( api.CZR_Helpers.build_setId( control.params.syncCollection ) );
  },
  getSavedModules : function() {
          var control = this,
              _savedModulesCandidates = [],
              _module_type = control.params.module_type,
              _raw_saved_module_val = [],
              _saved_items = [],
              _saved_modOpt = {};
          if ( control.isMultiModuleControl() ) {
              _savedModulesCandidates = $.extend( true, [], api( control.id )() );//deep clone
          } else {
              if ( api.CZR_Helpers.isMultiItemModule( _module_type ) && ! _.isEmpty( api( control.id )() ) && ! _.isObject( api( control.id )() ) ) {
                  api.consoleLog('Module Control Init for ' + control.id + '  : a mono item module control value should be an object if not empty.');
              }
              _raw_saved_module_val = _.isArray( api( control.id )() ) ? api( control.id )() : [ api( control.id )() ];

              _.each( _raw_saved_module_val, function( item_or_mod_opt_candidate , key ) {
                    if ( api.CZR_Helpers.hasModuleModOpt( _module_type ) && 0*0 === key ) {
                          if ( _.has( item_or_mod_opt_candidate, 'id') ) {
                                api.consoleLog( 'getSavedModules : the module ' + _module_type + ' in control ' + control.id + ' has no mod_opt defined while it should.' );
                          } else {
                                _saved_modOpt = item_or_mod_opt_candidate;
                          }
                    }
                    if ( _.has( item_or_mod_opt_candidate, 'id') && ! _.has( item_or_mod_opt_candidate, 'is_mod_opt' ) ) {
                          _saved_items.push( item_or_mod_opt_candidate );
                    }
              });
              _savedModulesCandidates.push(
                    {
                          id : api.CZR_Helpers.getOptionName( control.id ) + '_' + control.params.type,
                          module_type : control.params.module_type,
                          section : control.section(),
                          modOpt : $.extend( true, {} , _saved_modOpt ),//disconnect with a deep cloning
                          items : $.extend( true, [] , _saved_items )//disconnect with a deep cloning
                    }
              );
          }
          return _savedModulesCandidates;
  },
  isModuleRegistered : function( id_candidate ) {
        var control = this;
        return ! _.isUndefined( _.findWhere( control.czr_moduleCollection(), { id : id_candidate}) );
  },


});//$.extend//CZRBaseControlMths
var CZRBaseModuleControlMths = CZRBaseModuleControlMths || {};

$.extend( CZRBaseModuleControlMths, {
  instantiateModule : function( module, constructor ) {
          if ( ! _.has( module,'id') ) {
            throw new Error('CZRModule::instantiateModule() : a module has no id and could not be added in the collection of : ' + this.id +'. Aborted.' );
          }
          var control = this;
          if ( _.isUndefined(constructor) || _.isEmpty(constructor) ) {
              constructor = control.getModuleConstructor( module );
          }
          if ( ! _.isEmpty( module.id ) && control.czr_Module.has( module.id ) ) {
                throw new Error('The module id already exists in the collection in control : ' + control.id );
          }

          var module_api_ready = control.prepareModuleForAPI( module );
          control.czr_Module.add( module_api_ready.id, new constructor( module_api_ready.id, module_api_ready ) );

          if ( ! control.czr_Module.has( module_api_ready.id ) ) {
              throw new Error('instantiateModule() : instantiation failed for module id ' + module_api_ready.id + ' in control ' + control.id  );
          }
          return control.czr_Module(module_api_ready.id);
  },
  getModuleConstructor : function( module ) {
          var control = this,
              parentConstructor = {},
              constructor = {};

          if ( ! _.has( module, 'module_type' ) ) {
              throw new Error('CZRModule::getModuleConstructor : no module type found for module ' + module.id );
          }
          if ( ! _.has( api.czrModuleMap, module.module_type ) ) {
              throw new Error('Module type ' + module.module_type + ' is not listed in the module map api.czrModuleMap.' );
          }

          var _mthds = api.czrModuleMap[ module.module_type ].mthds,
              _is_crud = api.czrModuleMap[ module.module_type ].crud,
              _base_constructor = _is_crud ? api.CZRDynModule : api.CZRModule;
          if ( ! _.isEmpty( module.sektion_id ) ) {
              parentConstructor = _base_constructor.extend( _mthds );
              constructor = parentConstructor.extend( control.getMultiModuleExtender( parentConstructor ) );
          } else {
              constructor = _base_constructor.extend( _mthds );
          }

          if ( _.isUndefined(constructor) || _.isEmpty(constructor) || ! constructor ) {
              throw new Error('CZRModule::getModuleConstructor : no constructor found for module type : ' + module.module_type +'.' );
          }
          return constructor;
  },
  prepareModuleForAPI : function( module_candidate ) {
        if ( ! _.isObject( module_candidate ) ) {
            throw new Error('prepareModuleForAPI : a module must be an object to be instantiated.');
        }

        var control = this,
            api_ready_module = {};

        _.each( control.getDefaultModuleApiModel() , function( _value, _key ) {
              var _candidate_val = module_candidate[_key];
              switch( _key ) {
                    case 'id' :
                          if ( _.isEmpty( _candidate_val ) ) {
                                api_ready_module[_key] = control.generateModuleId( module_candidate.module_type );
                          } else {
                                api_ready_module[_key] = _candidate_val;
                          }
                    break;
                    case 'module_type' :
                          if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareModuleForAPI : a module type must a string not empty');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case 'items' :
                          if ( ! _.isArray( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : a module item list must be an array');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case 'modOpt' :
                          if ( ! _.isObject( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : a module modOpt property must be an object');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case 'crud' :
                          if ( _.has( api.czrModuleMap, module_candidate.module_type ) ) {
                                _candidate_val = api.czrModuleMap[ module_candidate.module_type ].crud;
                          } else if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : the module param "crud" must be a boolean');
                          }
                          api_ready_module[_key] = _candidate_val || false;
                    break;
                    case 'multi_item' :
                          if ( _.has( api.czrModuleMap, module_candidate.module_type ) ) {
                                _candidate_val = api.czrModuleMap[ module_candidate.module_type ].crud || api.czrModuleMap[ module_candidate.module_type ].multi_item;
                          } else if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : the module param "multi_item" must be a boolean');
                          }
                          api_ready_module[_key] = _candidate_val || false;
                    break;
                    case 'sortable' :
                          if ( _.has( api.czrModuleMap, module_candidate.module_type ) ) {
                                _candidate_val = api.czrModuleMap[ module_candidate.module_type ].sortable || api.czrModuleMap[ module_candidate.module_type ].crud || api.czrModuleMap[ module_candidate.module_type ].multi_item;
                          } else if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : the module param "sortable" must be a boolean');
                          }
                          api_ready_module[_key] = _candidate_val || false;
                    break;
                    case  'control' :
                          api_ready_module[_key] = control;//this
                    break;
                    case  'section' :
                          if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareModuleForAPI : a module section must be a string not empty');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case  'column_id' :
                          if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareModuleForAPI : a module column id must a string not empty');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case  'sektion' :
                          if ( ! _.isObject( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareModuleForAPI : a module sektion must be an object not empty');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case  'sektion_id' :
                          if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                                throw new Error('prepareModuleForAPI : a module sektion id must be a string not empty');
                          }
                          api_ready_module[_key] = _candidate_val;
                    break;
                    case 'is_added_by_user' :
                          if ( ! _.isUndefined( _candidate_val) && ! _.isBoolean( _candidate_val )  ) {
                                throw new Error('prepareModuleForAPI : the module param "is_added_by_user" must be a boolean');
                          }
                        api_ready_module[_key] = _candidate_val || false;
                    break;
                    case 'dirty' :
                          api_ready_module[_key] = _candidate_val || false;
                    break;
              }//switch
        });
        return api_ready_module;
  },
  generateModuleId : function( module_type, key, i ) {
          i = i || 1;
          if ( i > 100 ) {
                throw new Error('Infinite loop when generating of a module id.');
          }
          var control = this;
          key = key || control._getNextModuleKeyInCollection();
          var id_candidate = module_type + '_' + key;
          if ( ! _.has(control, 'czr_moduleCollection') || ! _.isArray( control.czr_moduleCollection() ) ) {
                throw new Error('The module collection does not exist or is not properly set in control : ' + control.id );
          }
          if ( control.isModuleRegistered( id_candidate ) ) {
            key++; i++;
            return control.generateModuleId( module_type, key, i );
          }

          return id_candidate;
  },
  _getNextModuleKeyInCollection : function() {
          var control = this,
            _max_mod_key = {},
            _next_key = 0;
          if ( ! _.isEmpty( control.czr_moduleCollection() ) ) {
              _max_mod_key = _.max( control.czr_moduleCollection(), function( _mod ) {
                  return parseInt( _mod.id.replace(/[^\/\d]/g,''), 10 );
              });
              _next_key = parseInt( _max_mod_key.id.replace(/[^\/\d]/g,''), 10 ) + 1;
          }
          return _next_key;
  }
});//$.extend//CZRBaseControlMths
var CZRBaseModuleControlMths = CZRBaseModuleControlMths || {};

$.extend( CZRBaseModuleControlMths, {
  registerModulesOnInit : function( sektion_module_instance ) {
          var control = this,
              _orphan_mods = [];

          _.each( control.getSavedModules() , function( _mod, _key ) {
                  if ( ! sektion_module_instance.czr_Item.has( _mod.sektion_id ) ) {
                      api.consoleLog('Warning Module ' + _mod.id + ' is orphan : it has no sektion to be embedded to. It Must be removed.');
                      _orphan_mods.push(_mod);
                      return;
                  }

                  var _sektion = sektion_module_instance.czr_Item( _mod.sektion_id );

                  if ( _.isUndefined( _sektion ) ) {
                    throw new Error('sektion instance missing. Impossible to instantiate module : ' + _mod.id );
                  }
                  $.extend( _mod, {sektion : _sektion} );
                  control.updateModulesCollection( {module : _mod } );
          });
          control.moduleCollectionReady.then( function() {
                if ( ! _.isEmpty( _orphan_mods ) ) {
                    control.moduleCollectionReact( control.czr_moduleCollection(), [], { orphans_module_removal : _orphan_mods } );
                }
          });
  },
  updateModulesCollection : function( obj ) {
          var control = this,
              _current_collection = control.czr_moduleCollection(),
              _new_collection = $.extend( true, [], _current_collection);
          if ( _.has( obj, 'collection' ) ) {
                control.czr_moduleCollection.set( obj.collection, obj.data || {} );
                return;
          }

          if ( ! _.has(obj, 'module') ) {
            throw new Error('updateModulesCollection, no module provided ' + control.id + '. Aborting');
          }
          var module_api_ready = control.prepareModuleForAPI( _.clone( obj.module ) );
          if ( _.findWhere( _new_collection, { id : module_api_ready.id } ) ) {
                _.each( _current_collection , function( _elt, _ind ) {
                      if ( _elt.id != module_api_ready.id )
                        return;
                      _new_collection[_ind] = module_api_ready;
                });
          }
          else {
                _new_collection.push(module_api_ready);
          }
          var _params = {};
          if ( _.has( obj, 'data') ) {
              _params = $.extend( true, {}, obj.data );
              $.extend( _params, { module : module_api_ready } );
          }
          control.czr_moduleCollection.set( _new_collection, _params );
  },
  moduleCollectionReact : function( to, from, data ) {
        var control = this,
            is_module_added = _.size(to) > _.size(from),
            is_module_removed = _.size(from) > _.size(to),
            is_module_update = _.size(from) == _.size(to);
            is_collection_sorted = false;
        if ( is_module_removed ) {
              var _to_remove = _.filter( from, function( _mod ){
                  return _.isUndefined( _.findWhere( to, { id : _mod.id } ) );
              });
              _to_remove = _to_remove[0];
              control.czr_Module.remove( _to_remove.id );
        }
        if ( _.isObject( data  ) && _.has( data, 'module' ) ) {
              data.module = control.prepareModuleForDB( $.extend( true, {}, data.module  ) );
        }
        if ( ! control.isMultiModuleControl() && is_module_added ) {
              return;
        }
        else {
              api(this.id).set( control.filterModuleCollectionBeforeAjax( to ), data );
        }
  },
  filterModuleCollectionBeforeAjax : function( collection ) {
          var control = this,
              _filtered_collection = $.extend( true, [], collection ),
              _to_return;

          _.each( collection , function( _mod, _key ) {
                var db_ready_mod = $.extend( true, {}, _mod );
                _filtered_collection[_key] = control.prepareModuleForDB( db_ready_mod );
          });
          if ( control.isMultiModuleControl() ) {
                return _filtered_collection;
          } else {
                if ( _.size(collection) > 1 ) {
                  throw new Error('There should not be several modules in the collection of control : ' + control.id );
                }
                if ( ! _.isArray(collection) || _.isEmpty(collection) || ! _.has( collection[0], 'items' ) ) {
                  throw new Error('The setting value could not be populated in control : ' + control.id );
                }
                var module_id = collection[0].id;

                if ( ! control.czr_Module.has( module_id ) ) {
                   throw new Error('The single module control (' + control.id + ') has no module registered with the id ' + module_id  );
                }
                var module_instance = control.czr_Module( module_id );
                if ( ! _.isArray( module_instance().items ) ) {
                  throw new Error('The module ' + module_id + ' should be an array in control : ' + control.id );
                }
                _to_return = module_instance.isMultiItem() ? module_instance().items : ( module_instance().items[0] || [] );
                return module_instance.hasModOpt() ? _.union( [ module_instance().modOpt ] , _to_return ) : _to_return;
          }
  },
  prepareModuleForDB : function ( module_db_candidate ) {
        if ( ! _.isObject( module_db_candidate ) ) {
            throw new Error('MultiModule Control::prepareModuleForDB : a module must be an object. Aborting.');
        }
        var control = this,
            db_ready_module = {};

        _.each( control.getDefaultModuleDBModel() , function( _value, _key ) {
              if ( ! _.has( module_db_candidate, _key ) ) {
                  throw new Error('MultiModule Control::prepareModuleForDB : a module is missing the property : ' + _key + ' . Aborting.');
              }

              var _candidate_val = module_db_candidate[ _key ];
              switch( _key ) {
                    case 'items' :
                      if ( ! _.isArray( _candidate_val )  ) {
                          throw new Error('prepareModuleForDB : a module item list must be an array');
                      }
                      db_ready_module[ _key ] = _candidate_val;
                    break;
                    case 'id' :
                      if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                          throw new Error('prepareModuleForDB : a module id must a string not empty');
                      }
                      db_ready_module[ _key ] = _candidate_val;
                    break;
                    case 'module_type' :
                      if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                          throw new Error('prepareModuleForDB : a module type must a string not empty');
                      }
                      db_ready_module[ _key ] = _candidate_val;
                    break;
                    case  'column_id' :
                      if ( ! _.isString( _candidate_val ) || _.isEmpty( _candidate_val ) ) {
                          throw new Error('prepareModuleForDB : a module column id must a string not empty');
                      }
                      db_ready_module[ _key ] = _candidate_val;
                    break;
                    case  'sektion_id' :
                      if ( ! _.isObject( module_db_candidate.sektion ) || ! _.has( module_db_candidate.sektion, 'id' ) ) {
                          throw new Error('prepareModuleForDB : a module sektion must be an object with an id.');
                      }
                      db_ready_module[ _key ] = module_db_candidate.sektion.id;
                    break;
                    case 'dirty' :
                      if ( control.czr_Module.has( module_db_candidate.id ) )
                          db_ready_module[ _key ] = control.czr_Module( module_db_candidate.id ).isDirty();
                      else
                          db_ready_module[ _key ] = _candidate_val;
                      if ( ! _.isBoolean( db_ready_module[ _key ] ) ) {
                          throw new Error('prepareModuleForDB : a module dirty state must be a boolean.');
                      }
                    break;
              }//switch
        });
        return db_ready_module;
  }

});//$.extend//CZRBaseControlMths
var CZRMultiModuleControlMths = CZRMultiModuleControlMths || {};

$.extend( CZRMultiModuleControlMths, {

  initialize: function( id, options ) {
          var control = this;
          api.consoleLog('IN MULTI MODULE INITIALIZE ? ', options );
          api(id).callbacks.add( function() { return control.syncColumn.apply( control, arguments ); } );

          api.CZRBaseModuleControl.prototype.initialize.call( control, id, options );

  },


  ready : function() {
      var control = this;
      api.consoleLog('MODULE-COLLECTION CONTROL READY', this.id );
      api.CZRBaseModuleControl.prototype.ready.apply( control, arguments);
  },
  syncColumn : function( to, from, data ) {
        api.consoleLog('IN SYNC COLUMN', to, from, data );
        if ( ! _.isUndefined(data) && data.silent )
          return;
        api.consoleLog('IN SYNXXX', api.control('hu_theme_options[module-collection]').syncSektionModule()(), this.syncSektionModule()(), this.id );
        if ( _.has( data, 'orphans_module_removal' ) )
          return;
        var control = api.control( this.id );
        var added_mod = _.filter( to, function( _mod, _key ){
            return ! _.findWhere( from, { id : _mod.id } );
        } );
        if ( ! _.isEmpty( added_mod ) ) {
              api.consoleLog('ADDED MODULE?', added_mod );
              _.each( added_mod, function( _mod ) {
                      control.syncSektionModule().czr_Column( _mod.column_id ).updateColumnModuleCollection( { module : _mod } );
              });
        }
        var removed_mod = _.filter( from, function( _mod, _key ){
            return ! _.findWhere( to, { id : _mod.id } );
        } );
        if ( ! _.isEmpty( removed_mod ) ) {
              _.each( removed_mod, function( _mod ) {
                      control.syncSektionModule().czr_Column( _mod.column_id ).removeModuleFromColumnCollection( _mod );
              });
        }
        if ( _.size(from) == _.size(to) && _.has( data, 'module') && _.has( data, 'source_column') && _.has( data, 'target_column') ) {
                $.when( control.syncSektionModule().moveModuleFromTo( data.module, data.source_column, data.target_column ) ).done( function() {
                      control.syncSektionModule().control.trigger('module-moved', { module : data.module, source_column: data.source_column, target_column :data.target_column });
                } );
        }
        control.trigger( 'columns-synchronized', to );
  },
  removeModule : function( module ) {
        var control = this;
        if ( control.czr_Module.has( module.id ) && 'resolved' == control.czr_Module( module.id ).embedded.state() )
            control.czr_Module( module.id ).container.remove();
        control.removeModuleFromCollection( module );
  },


  removeModuleFromCollection : function( module ) {
        var control = this,
            _current_collection = control.czr_moduleCollection(),
            _new_collection = $.extend( true, [], _current_collection);

        _new_collection = _.filter( _new_collection, function( _mod ) {
              return _mod.id != module.id;
        } );
        control.czr_moduleCollection.set( _new_collection );
  }

});//$.extend//CZRBaseControlMths
var CZRMultiModuleControlMths = CZRMultiModuleControlMths || {};
$.extend( CZRMultiModuleControlMths, {
  getMultiModuleExtender : function( parentConstructor ) {
        var control = this;
        $.extend( control.CZRModuleExtended, {
              initialize: function( id, constructorOptions ) {
                    var module = this;
                    parentConstructor.prototype.initialize.call( module, id, constructorOptions );

                    api.consoleLog('MODULE INSTANTIATED : ', module.id );
                    $.extend( module, {
                          singleModuleWrapper : 'czr-single-module-wrapper',
                          sektionModuleTitle : 'czr-module-sektion-title-part',
                          ruModuleEl : 'czr-ru-module-sektion-content'
                    } );
                    module.czr_ModuleState = new api.Value( false );
                    module.isReady.done( function() {
                          module.setupModuleView();
                    });
                    module.moduleTitleEmbedded = $.Deferred();
                    module.modColumn = new api.Value();
                    module.modColumn.set( constructorOptions.column_id );
                    module.modColumn.bind( function( to, from ) {
                          api.consoleLog('MODULE ' + module.id + ' HAS BEEN MOVED TO COLUMN', to, module() );
                          var _current_model = module(),
                              _new_model = $.extend( true, {}, _current_model );

                          _new_model.column_id = to;
                          module.set( _new_model, { target_column : to, source_column : from } );
                    } );
              },
              ready : function( is_added_by_user ) {
                      var module = this;
                       api.consoleLog('MODULE READY IN EXTENDED MODULE CLASS : ', module.id );
                      $.when( module.renderModuleWrapper( is_added_by_user ) ).done( function( $_module_container ) {
                            if ( _.isUndefined($_module_container) || false === $_module_container.length ) {
                                throw new Error( 'Module container has not been embedded for module :' + module.id );
                            }
                            module.container = $_module_container;
                            module.embedded.resolve();
                      } );
                      parentConstructor.prototype.ready.call( module );
              }

        });
        return control.CZRModuleExtended;
  },
  CZRModuleExtended  : {
        renderModuleWrapper : function( is_added_by_user ) {
                var module = this;
                if ( 'resolved' == module.embedded.state() )
                  return module.container;
                if ( 0 === $( '#tmpl-' + module.singleModuleWrapper ).length ) {
                  throw new Error('No template for module ' + module.id + '. The template script id should be : #tmpl-' + module.singleModuleWrapper );
                }

                var module_wrapper_tmpl = wp.template( module.singleModuleWrapper ),
                    tmpl_data = {
                        id : module.id,
                        type : module.module_type
                    },
                    $_module_el = $(  module_wrapper_tmpl( tmpl_data ) );
                if ( is_added_by_user ) {
                    $.when( $( '.czr-module-collection-wrapper' , module._getColumn().container ).find( '.czr-module-candidate').after( $_module_el ) ).
                      done( function() {
                        $( '.czr-module-collection-wrapper' , module._getColumn().container ).find( '.czr-module-candidate').remove();
                      });
                } else {
                    $( '.czr-module-collection-wrapper' , module._getColumn().container).append( $_module_el );
                }

                return $_module_el;
        },





        setupModuleView : function() {
                var module = this;

                module.view_event_map = [
                        {
                          trigger   : 'click keydown',
                          selector  : [ '.czr-remove-mod', '.' + module.control.css_attr.cancel_alert_btn ].join(','),
                          name      : 'toggle_remove_alert',
                          actions   : ['toggleModuleRemoveAlert']
                        },
                        {
                          trigger   : 'click keydown',
                          selector  : '.' + module.control.css_attr.remove_view_btn,
                          name      : 'remove_module',
                          actions   : ['removeModule']
                        },
                        {
                          trigger   : 'click keydown',
                          selector  : '.czr-edit-mod',
                          name      : 'edit_module',
                          actions   : ['setModuleViewVisibility', 'sendEditModule']
                        },
                        {
                          trigger   : 'click keydown',
                          selector  : '.czr-module-back',
                          name      : 'back_to_column',
                          actions   : ['setModuleViewVisibility']
                        },
                        {
                          trigger   : 'mouseenter',
                          selector  : '.czr-mod-header',
                          name      : 'hovering_module',
                          actions   : function( obj ) {
                                module.control.previewer.send( 'start_hovering_module', {
                                      id : module.id
                                });
                          }
                        },
                        {
                          trigger   : 'mouseleave',
                          selector  : '.czr-mod-header',
                          name      : 'hovering_module',
                          actions   : function( obj ) {
                              module.control.previewer.send( 'stop_hovering_module', {
                                    id : module.id
                              });
                          }
                        }
                ];
                module.embedded.done( function() {
                      module.czr_ModuleState.callbacks.add( function() { return module.setupModuleViewStateListeners.apply(module, arguments ); } );
                      api.CZR_Helpers.setupDOMListeners(
                            module.view_event_map,//actions to execute
                            { module : { id : module.id } , dom_el:module.container },//model + dom scope
                            module //instance where to look for the cb methods
                      );//listeners for the view wrapper
                });
        },
        setModuleViewVisibility : function( obj, is_added_by_user ) {
              var module = this;

              module.czr_ModuleState( ! module.czr_ModuleState() );
              api.czrModulePanelState.set(false);
              api.czrSekSettingsPanelState.set(false);
              module.control.syncSektionModule().closeAllOtherSektions( $(obj.dom_event.currentTarget, obj.dom_el ) );
        },
        sendEditModule : function( obj ) {
              var module = this;
              module.control.previewer.send( 'edit_module', {
                    id : module.id
              });
        },
        setupModuleViewStateListeners : function( expanded ) {
              var module = this;
              api.czr_isModuleExpanded = api.czr_isModuleExpanded || new api.Value();

              if ( expanded )
                api.czr_isModuleExpanded( module );
              else
                api.czr_isModuleExpanded( false );
              $.when( module.toggleModuleViewExpansion( expanded ) ).done( function() {
                    if ( expanded ) {
                          module.renderModuleTitle();
                          module.populateSavedItemCollection();
                    }
                    else {
                          module.czr_Item.each ( function( item ) {
                                item.czr_ItemState.set('closed');
                                item._destroyView( 0 );
                                module.czr_Item.remove( item.id );
                          } );
                    }
              });
        },


        renderModuleTitle : function() {
              var module = this;
              if( 'resolved' == module.moduleTitleEmbedded.state() )
                return;
              if ( 0 === $( '#tmpl-' + module.sektionModuleTitle ).length ) {
                throw new Error('No sektion title Module Part template for module ' + module.id + '. The template script id should be : #tmpl-' + module.sektionModuleTitle );
              }
              $.when( $( module.container ).find('.czr-mod-content').prepend(
                    $( wp.template( module.sektionModuleTitle )( { id : module.id } ) )
              ) ).done( function() {
                    module.moduleTitleEmbedded.resolve();
              });
        },
        toggleModuleViewExpansion : function( expanded, duration ) {
              var module = this;
              $( '.czr-mod-content' , module.container ).slideToggle( {
                  duration : duration || 200,
                  done : function() {
                        var $_overlay = module.container.closest( '.wp-full-overlay' ),
                            $_backBtn = module.container.find( '.czr-module-back' ),
                            $_modTitle = module.container.find('.czr-module-title');

                        module.container.toggleClass('open' , expanded );
                        $_overlay.toggleClass('czr-module-open', expanded );
                        $_modTitle.attr( 'tabindex', expanded ? '-1' : '0' );
                        $_backBtn.attr( 'tabindex', expanded ? '0' : '-1' );

                        if( expanded ) {
                            $_backBtn.focus();
                        } else {
                            $_modTitle.focus();
                        }
                        if ( expanded )
                          module._adjustScrollExpandedBlock( module.container );
                  }//done callback
                } );
        },









        toggleModuleRemoveAlert : function( obj ) {
                var module = this,
                    control = this.control,
                    $_alert_el = $( '.' + module.control.css_attr.remove_alert_wrapper, module.container ).first(),
                    $_clicked = obj.dom_event,
                    $_column_container = control.syncSektionModule().czr_Column( module.column_id ).container;
                if ( _.has(module, 'preItem') ) {
                    control.syncSektionModule().preItemExpanded.set( false );
                }
                $('.' + module.control.css_attr.remove_alert_wrapper, $_column_container ).not($_alert_el).each( function() {
                      if ( $(this).hasClass('open') ) {
                            $(this).slideToggle( {
                                  duration : 200,
                                  done : function() {
                                        $(this).toggleClass('open' , false );
                                        $(this).siblings().find('.' + module.control.css_attr.display_alert_btn).toggleClass('active' , false );
                                  }
                            } );
                      }
                });
                if ( ! wp.template( module.AlertPart )  || ! module.container ) {
                    throw new Error( 'No removal alert template available for module :' + module.id );
                }

                $_alert_el.html( wp.template( module.AlertPart )( { title : ( module().title || module.id ) } ) );
                $_alert_el.slideToggle( {
                      duration : 200,
                      done : function() {
                            var _is_open = ! $(this).hasClass('open') && $(this).is(':visible');
                            $(this).toggleClass('open' , _is_open );
                            $( obj.dom_el ).find('.' + module.control.css_attr.display_alert_btn).toggleClass( 'active', _is_open );
                            if ( _is_open )
                              module._adjustScrollExpandedBlock( module.container );
                      }
                } );
        },
        removeModule : function( obj ) {
              this.control.removeModule( obj.module );
        },














        _getColumn : function() {
                var module = this;
                return module.control.syncSektionModule().czr_Column( module.modColumn() );
        },

        _getSektion : function() {

        }

  }

});//$.extend//CZRBaseControlMths
var CZRMultiplePickerMths = CZRMultiplePickerMths || {};
$.extend( CZRMultiplePickerMths , {
  ready: function() {
    var control  = this,
        _select  = this.container.find('select');


    _select.select2({
      closeOnSelect: false,
      templateSelection: czrEscapeMarkup
    });

    function czrEscapeMarkup(obj) {
      return obj.text.replace(/\u2013|\u2014/g, "");
    }
    _select.on('change', function(e){
      if ( 0 === $(this).find("option:selected").length )
        control.setting.set([]);
    });
  }
});//$.extend
var CZRCroppedImageMths = CZRCroppedImageMths || {};

(function (api, $, _) {
  if ( 'function' != typeof wp.media.controller.Cropper  || 'function' != typeof api.CroppedImageControl  )
    return;
    wp.media.controller.CZRCustomizeImageCropper = wp.media.controller.Cropper.extend({
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
    $.extend( CZRCroppedImageMths , {
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
                new wp.media.controller.CZRCustomizeImageCropper({
                    imgSelectOptions: this.calculateImageSelectOptions,
                    control: this
                })
            ]
        });

        this.frame.on( 'select', this.onSelect, this );
        this.frame.on( 'cropped', this.onCropped, this );
        this.frame.on( 'skippedcrop', this.onSkippedCrop, this );
      },
      onSelect: function() {
        var attachment = this.frame.state().get( 'selection' ).first().toJSON();
        if ( ! ( attachment.mime && attachment.mime.indexOf("image") > -1 ) ){
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
    });//Method definition

})( wp.customize, jQuery, _);
var CZRUploadMths = CZRUploadMths || {};
$.extend( CZRUploadMths, {
  ready: function() {
    var control = this;

    this.params.removed = this.params.removed || '';

    this.success = $.proxy( this.success, this );

    this.uploader = $.extend({
      container: this.container,
      browser:   this.container.find('.czr-upload'),
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
    this.removerVisibility( this.setting() );
  },


  success: function( attachment ) {
    this.setting.set( attachment.get('id') );
  },
  removerVisibility: function( to ) {
    this.remover.toggle( to != this.params.removed );
  }
});//method definition
var CZRLayoutSelectMths = CZRLayoutSelectMths || {};

$.extend( CZRLayoutSelectMths , {
  ready: function() {
    this.setupSelect();
  },


  setupSelect : function( obj ) {
    var control = this;
        $_select  = this.container.find('select');

    function addImg( state ) {
      if (! state.id) { return state.text; }
      if ( ! _.has( control.params.layouts, state.element.value ) )
        return;

      var _layout_data = control.params.layouts[state.element.value],
          _src = _layout_data.src,
          _title = _layout_data.label,
          $state = $(
        '<img src="' + _src +'" class="czr-layout-img" title="' + _title + '" /><span class="czr-layout-title">' + _title + '</span>'
      );
      return $state;
    }
    $_select.select2( {
        templateResult: addImg,
        templateSelection: addImg,
        minimumResultsForSearch: Infinity
    });
  },
});//$.extend
(function ( api, $, _ ) {
      $.extend( CZRBaseControlMths, api.Events );
      $.extend( CZRModuleMths, api.Events );
      $.extend( CZRItemMths, api.Events );
      $.extend( CZRModOptMths, api.Events );
      $.extend( CZRSkopeBaseMths, api.Events );
      $.extend( CZRSkopeMths, api.Events );
      $.extend( CZRBaseControlMths, api.CZR_Helpers );
      $.extend( CZRInputMths, api.CZR_Helpers );
      $.extend( CZRModuleMths, api.CZR_Helpers );
      $.extend( CZRSkopeMths, api.CZR_Helpers );
      api.CZR_skopeBase             = api.Class.extend( CZRSkopeBaseMths );
      api.CZR_skopeSave             = api.Class.extend( CZRSkopeSaveMths );
      api.CZR_skopeReset            = api.Class.extend( CZRSkopeResetMths );
      api.CZR_skope                 = api.Value.extend( CZRSkopeMths ); //=> used as constructor when creating the collection of skopes
      if ( _.has(api, 'HeaderTool') ) {
            api.czr_HeaderTool = $.extend(  true, {}, api.HeaderTool );
      }
      api.CZRInput                  = api.Value.extend( CZRInputMths );
      api.CZRItem                   = api.Value.extend( CZRItemMths );
      api.CZRModOpt                 = api.Value.extend( CZRModOptMths );
      api.CZRModule                 = api.Value.extend( CZRModuleMths );
      api.CZRDynModule              = api.CZRModule.extend( CZRDynModuleMths );
      if ( ! _.isUndefined( window.CZRColumnMths ) ) {
            api.CZRColumn           = api.Value.extend( CZRColumnMths );
      }
      api.CZRBaseControl            = api.Control.extend( CZRBaseControlMths );
      api.CZRBaseModuleControl      = api.CZRBaseControl.extend( CZRBaseModuleControlMths );
      api.CZRMultiModuleControl     = api.CZRBaseModuleControl.extend( CZRMultiModuleControlMths );

      api.CZRUploadControl          = api.Control.extend( CZRUploadMths );
      api.CZRLayoutControl          = api.Control.extend( CZRLayoutSelectMths );
      api.CZRMultiplePickerControl  = api.Control.extend( CZRMultiplePickerMths );



      $.extend( api.controlConstructor, {
            czr_upload     : api.CZRUploadControl,

            czr_module : api.CZRBaseModuleControl,
            czr_multi_module : api.CZRMultiModuleControl,

            czr_multiple_picker : api.CZRMultiplePickerControl,
            czr_layouts    : api.CZRLayoutControl
      });

      if ( 'function' == typeof api.CroppedImageControl ) {
            api.CZRCroppedImageControl   = api.CroppedImageControl.extend( CZRCroppedImageMths );

            $.extend( api.controlConstructor, {
                  czr_cropped_image : api.CZRCroppedImageControl
            });
      }
      api.czrInputMap = api.czrInputMap || {};
      $.extend( api.czrInputMap, {
            text      : '',
            textarea  : '',
            check     : 'setupIcheck',
            select    : 'setupSelect',
            number    : 'setupStepper',
            upload    : 'setupImageUploader',
            color     : 'setupColorPicker',
            content_picker : 'setupContentPicker',
            text_editor    : 'setupTextEditor',
            password : '',
            range_slider : 'setupRangeSlider'
      });

})( wp.customize, jQuery, _ );
(function (api, $, _) {
  var $_nav_section_container,
      translatedStrings = serverControlParams.translatedStrings || {};

  api.czr_CrtlDependenciesReady = $.Deferred();

  api.bind( 'ready' , function() {
        if ( _.has( api, 'czr_ctrlDependencies') )
          return;
        if ( serverControlParams.isSkopOn ) {
              api.czr_skopeReady.done( function() {
                    api.czr_ctrlDependencies = new api.CZR_ctrlDependencies();
                    api.czr_CrtlDependenciesReady.resolve();
              });
        } else {
              api.czr_ctrlDependencies = new api.CZR_ctrlDependencies();
              api.czr_CrtlDependenciesReady.resolve();
        }

  } );


  api.CZR_ctrlDependencies = api.Class.extend( {
          dominiDeps : [],
          initialize: function() {
                var self = this;

                this.defaultDominusParams = {
                      dominus : '',
                      servi : [],
                      visibility : null,
                      actions : null,
                      onSectionExpand : true
                };
                this.dominiDeps = _.extend( this.dominiDeps, this._getControlDeps() );
                if ( ! _.isArray( self.dominiDeps ) ) {
                    throw new Error('Visibilities : the dominos dependency array is not an array.');
                }
                api.czr_activeSectionId.bind( function( section_id ) {
                      if ( ! _.isEmpty( section_id ) && api.section.has( section_id ) ) {
                            self.setServiDependencies( section_id );
                      }
                });
                api.bind( 'awaken-section', function( target_source ) {
                      if ( serverControlParams.isSkopOn && _.has( api ,'czr_skopeBase' ) ) {
                            api.czr_skopeBase.processSilentUpdates( {
                                  candidates : {},
                                  section_id : target_source.target,
                                  refresh : false
                            } ).then( function() {
                                  self.setServiDependencies( target_source.target, target_source.source );
                            });
                      } else {
                            self.setServiDependencies( target_source.target, target_source.source );
                      }
                });
                this._handleFaviconNote();
          },
          setServiDependencies : function( targetSectionId, sourceSectionId, refresh ) {
                var self = this, params, dfd = $.Deferred();

                refresh = refresh || false;

                if ( _.isUndefined( targetSectionId ) || ! api.section.has( targetSectionId ) ) {
                  throw new Error( 'Control Dependencies : the targetSectionId is missing or not registered : ' + targetSectionId );
                }
                api.section( targetSectionId ).czr_ctrlDependenciesReady = api.section( targetSectionId ).czr_ctrlDependenciesReady || $.Deferred();
                if ( ! refresh && 'resolved' == api.section( targetSectionId ).czr_ctrlDependenciesReady.state() )
                  return dfd.resolve().promise();
                _.each( self.dominiDeps , function( params ) {
                      if ( ! _.has( params, 'dominus' ) || ! _.isString( params.dominus ) || _.isEmpty( params.dominus ) ) {
                            throw new Error( 'Control Dependencies : a dominus control id must be a not empty string.');
                      }

                      var wpDominusId = api.CZR_Helpers.build_setId( params.dominus );
                      if ( ! api.control.has( wpDominusId ) )
                        return;

                      if ( api.control( wpDominusId ).section() != targetSectionId )
                        return;
                      try {
                            params = self._prepareDominusParams( params );
                      } catch( e ) {
                            api.consoleLog( 'prepareDominus Params error : ' + e );
                            return;
                      }


                      self._processDominusCallbacks( params.dominus, params, refresh )
                            .fail( function() {
                                  api.consoleLog( 'self._processDominusCallbacks fail for section ' + targetSectionId );
                                  dfd.reject();
                            })
                            .done( function() {
                                  dfd.resolve();
                            });
                });
                var _secCtrls = api.CZR_Helpers.getSectionControlIds( targetSectionId ),
                    _getServusDomini = function( shortServudId ) {
                          var _dominiIds = [];
                          _.each( self.dominiDeps , function( params ) {
                                if ( ! _.has( params, 'servi' ) || ! _.isArray( params.servi ) || ! _.has( params, 'dominus' ) || _.isEmpty( params.dominus ) ) {
                                      throw new Error( 'Control Dependencies : wrong params in _getServusDomini.');
                                }

                                if ( _.contains( params.servi , shortServudId ) && ! _.contains( _dominiIds , params.dominus ) ) {
                                      try {
                                            params = self._prepareDominusParams( params );
                                      } catch( e ) {
                                            api.consoleLog( 'prepareDominus Params error : ' + e );
                                            return;
                                      }
                                      _dominiIds.push( params.dominus );
                                }
                          });
                          return ! _.isArray( _dominiIds ) ? [] : _dominiIds;
                    },
                    _servusDominiIds = [];
                _.each( _secCtrls, function( servusCandidateId ) {
                      if ( _.isEmpty( _getServusDomini( servusCandidateId ) ) )
                        return;

                      _servusDominiIds = _.union( _servusDominiIds, _getServusDomini( servusCandidateId ) );
                });
                _.each( _servusDominiIds, function( shortDominusId ){

                      var wpDominusId = api.CZR_Helpers.build_setId( shortDominusId );
                      if ( api.control( wpDominusId ).section() == targetSectionId )
                          return;
                      if ( sourceSectionId == api.control( wpDominusId ).section() )
                          return;
                      api.trigger( 'awaken-section', {
                            target : api.control( wpDominusId ).section(),
                            source : targetSectionId
                      } );
                } );
                dfd.always( function() {
                      api.section( targetSectionId ).czr_ctrlDependenciesReady.resolve();
                });
                return dfd.promise();
          },
          _deferCallbackForControl : function( wpCrtlId, callback, args ) {
                var dfd = $.Deferred();
                if ( _.isEmpty(wpCrtlId) || ! _.isString(wpCrtlId) ) {
                    throw new Error( '_deferCallbackForControl : the control id is missing.' );
                }
                if ( ! _.isFunction( callback ) ) {
                    throw new Error( '_deferCallbackForControl : callback must be a funtion.' );
                }
                args = ( _.isUndefined(args) || ! _.isArray( args ) ) ? [] : args;

                if ( api.control.has( wpCrtlId ) ) {
                      if ( 'resolved' == api.control(wpCrtlId ).deferred.embedded.state() ) {
                            $.when( callback.apply( null, args ) )
                                  .fail( function() { dfd.reject(); })
                                  .done( function() { dfd.resolve(); });
                      } else {
                            api.control( wpCrtlId ).deferred.embedded.then( function() {
                                  $.when( callback.apply( null, args ) )
                                        .fail( function() { dfd.reject(); })
                                        .done( function() { dfd.resolve(); });
                            });
                      }
                } else {
                      api.control.when( wpCrtlId, function() {
                            api.control( wpCrtlId ).deferred.embedded.then( function() {
                                  $.when( callback.apply( null, args ) )
                                        .fail( function() { dfd.reject(); })
                                        .done( function() { dfd.resolve(); });
                            });
                      });
                }
                return dfd.promise();
          },
          _processDominusCallbacks : function( shortDominusId, dominusParams, refresh ) {
                var self = this,
                    wpDominusId = api.CZR_Helpers.build_setId( shortDominusId ),
                    dominusSetInst = api( wpDominusId ),
                    dfd = $.Deferred(),
                    hasProcessed = false;
                _.each( dominusParams.servi , function( servusShortSetId ) {
                        if ( ! api.control.has( api.CZR_Helpers.build_setId( servusShortSetId ) ) ) {
                            return;
                        }
                        var _fireDominusCallbacks = function( dominusSetVal, servusShortSetId, dominusParams, refresh ) {
                                  var _toFire = [],
                                      _args = arguments;
                                  _.each( dominusParams, function( _item, _key ) {
                                        switch( _key ) {
                                            case 'visibility' :
                                                self._setVisibility.apply( null, _args );
                                            break;
                                            case 'actions' :
                                                if ( _.isFunction( _item ) )
                                                    _item.apply( null, _args );
                                            break;
                                        }
                                  });
                            },
                            _deferCallbacks = function( dominusSetVal ) {
                                  dominusSetVal = dominusSetVal  || dominusSetInst();
                                  var wpServusSetId = api.CZR_Helpers.build_setId( servusShortSetId );
                                  self._deferCallbackForControl(
                                              wpServusSetId,
                                              _fireDominusCallbacks,
                                              [ dominusSetVal, servusShortSetId, dominusParams ]
                                        )
                                        .always( function() { hasProcessed = true; })
                                        .fail( function() { dfd.reject(); })
                                        .done( function() { dfd.resolve(); });
                            };
                        _deferCallbacks();
                        if ( ! _.has( dominusSetInst, 'czr_visibilityServi' ) )
                            dominusSetInst.czr_visibilityServi = new api.Value( [] );
                        var _currentDependantBound = dominusSetInst.czr_visibilityServi();
                        if ( ! _.contains( _currentDependantBound, servusShortSetId ) ) {
                              dominusSetInst.bind( function( dominusSetVal ) {
                                  _deferCallbacks( dominusSetVal );
                              });
                              dominusSetInst.czr_visibilityServi( _.union( _currentDependantBound, [ servusShortSetId ] ) );
                        }
                } );//_.each
                if ( ! hasProcessed )
                  return dfd.resolve().promise();
                return dfd.promise();
          },
          _setVisibility : function ( dominusSetVal, servusShortSetId, dominusParams, refresh ) {
                var wpServusSetId = api.CZR_Helpers.build_setId( servusShortSetId ),
                    visibility = dominusParams.visibility( dominusSetVal, servusShortSetId, dominusParams.dominus );

                refresh = refresh || false;
                if ( ! _.isBoolean( visibility ) || ( 'unchanged' == visibility && ! refresh ) )
                  return;
                var _doVisibilitiesWhenPossible = function() {
                        if ( api.state.has( 'silent-update-processing' ) && api.state( 'silent-update-processing' )() )
                          return;
                        api.control( wpServusSetId, function( _controlInst ) {
                              var _args = {
                                    duration : 'fast',
                                    completeCallback : function() {},
                                    unchanged : false
                              };

                              if ( _.has( _controlInst, 'active' ) )
                                visibility = visibility && _controlInst.active();

                              if ( _.has( _controlInst, 'defaultActiveArguments' ) )
                                _args = control.defaultActiveArguments;

                              _controlInst.onChangeActive( visibility , _controlInst.defaultActiveArguments );
                        });
                        if ( api.state.has( 'silent-update-processing' ) ) {
                              api.state( 'silent-update-processing' ).unbind( _doVisibilitiesWhenPossible );
                        }
                };

                if ( api.state.has( 'silent-update-processing' ) && api.state( 'silent-update-processing' )() ) {
                      api.state( 'silent-update-processing' ).bind( _doVisibilitiesWhenPossible );
                } else {
                      _doVisibilitiesWhenPossible();
                }

          },
          _getControlDeps : function() {
            return {};
          },
          _prepareDominusParams : function( params_candidate ) {
                var self = this,
                    _ready_params = {};
                if ( ! _.isObject( params_candidate ) ) {
                    throw new Error('Visibilities : a dominus param definition must be an object.');
                }
                if ( ! _.has( params_candidate, 'visibility' ) && ! _.has( params_candidate, 'actions' ) ) {
                    throw new Error('Visibilities : a dominus definition must include a visibility or an actions callback.');
                }
                if ( ! _.has( params_candidate, 'dominus' ) || ! _.isString( params_candidate.dominus ) || _.isEmpty( params_candidate.dominus ) ) {
                      throw new Error( 'Visibilities : a dominus control id must be a not empty string.');
                }
                var wpDominusId = api.CZR_Helpers.build_setId( params_candidate.dominus );
                if ( ! api.control.has( wpDominusId ) ) {
                      throw new Error( 'Visibilities : a dominus control id is not registered : ' + wpDominusId );
                }
                if ( ! _.has( params_candidate, 'servi' ) || _.isUndefined( params_candidate.servi ) || ! _.isArray( params_candidate.servi ) || _.isEmpty( params_candidate.servi ) ) {
                      throw new Error( 'Visibilities : servi must be set as an array not empty.');
                }

                _.each( self.defaultDominusParams , function( _value, _key ) {
                    var _candidate_val = params_candidate[ _key ];

                    switch( _key ) {
                          case 'visibility' :
                              if ( ! _.isUndefined( _candidate_val ) && ! _.isEmpty( _candidate_val ) && ! _.isFunction( _candidate_val ) ) {
                                    throw new Error( 'Visibilities : a dominus visibility callback must be a function : ' + params_candidate.dominus );
                              }
                          break;
                          case 'actions' :
                              if ( ! _.isUndefined( _candidate_val ) && ! _.isEmpty( _candidate_val ) && ! _.isFunction( _candidate_val ) ) {
                                    throw new Error( 'Visibilities : a dominus actions callback must be a function : ' + params_candidate.dominus );
                              }
                          break;
                          case 'onSectionExpand' :
                              if ( ! _.isUndefined( _candidate_val ) && ! _.isEmpty( _candidate_val ) && ! _.isBoolean( _candidate_val ) ) {
                                    throw new Error( 'Visibilities : a dominus onSectionExpand param must be a boolean : ' + params_candidate.dominus );
                              }
                          break;
                    }
                    _ready_params[_key] = _candidate_val;
                });

                return _ready_params;
          },
          _handleFaviconNote : function() {
                var self = this,
                    _fav_setId = api.CZR_Helpers.build_setId( serverControlParams.faviconOptionName );
                if ( ! api.has('site_icon') || ! api.control('site_icon') || ( api.has( _fav_setId ) && 0 === + api( _fav_setId )() ) || + api('site_icon')() > 0 )
                  return;

                var _oldDes     = api.control('site_icon').params.description;
                    _newDes     = ['<strong>' , translatedStrings.faviconNote || '' , '</strong><br/><br/>' ].join('') + _oldDes;
                self._printFaviconNote(_newDes );
                api('site_icon').callbacks.add( function(to) {
                  if ( +to > 0 ) {
                    api.control('site_icon').container.find('.description').text(_oldDes);
                    if ( api.has( _fav_setId ) )
                      api( _fav_setId ).set("");
                  }
                  else {
                    self._printFaviconNote(_newDes );
                  }
                });
          },
          _printFaviconNote : function( _newDes ) {
                api.control('site_icon').container.find('.description').html(_newDes);
          }
    }
  );//api.Class.extend() //api.CZR_ctrlDependencies

})( wp.customize, jQuery, _);//DOM READY :
(function (wp, $) {
  $( function($) {
    var api = wp.customize || api;
    $('.accordion-section').not('.control-panel').click( function () {
      _recenter_current_section($(this));
    });

    function _recenter_current_section( section ) {
      var $siblings               = section.siblings( '.open' );
      if ( 0 !== $siblings.length &&  $siblings.offset().top < 0 ) {
        $('.wp-full-overlay-sidebar-content').animate({
              scrollTop:  - $('#customize-theme-controls').offset().top - $siblings.height() + section.offset().top + $('.wp-full-overlay-sidebar-content').offset().top
        }, 700);
      }
    }//end of fn
    api.czrSetupCheckbox = function( controlId, refresh ) {
      $('input[type=checkbox]', api.control(controlId).container ).each( function() {
        if ( 0 === $(this).val() || '0' == $(this).val() || 'off' == $(this).val() || _.isEmpty($(this).val() ) ) {
          $(this).prop('checked', false);
        } else {
          $(this).prop('checked', true);
        }
        if ( 0 !== $(this).closest('div[class^="icheckbox"]').length )
          return;

        $(this).iCheck({
          checkboxClass: 'icheckbox_flat-grey',
          radioClass: 'iradio_flat-grey',
        })
        .on( 'ifChanged', function(e){
          $(this).val( false === $(this).is(':checked') ? 0 : 1 );
          $(e.currentTarget).trigger('change');
        });
      });
    };//api.czrSetupCheckbox()
    api.czrSetupSelect = function(controlId, refresh) {
      $('select[data-customize-setting-link]', api.control(controlId).container )
            .not('.no-selecter-js')
            .each( function() {
                  $(this).selecter({
                  });
            });
    };//api.czrSetupSelect()
    api.czrSetupStepper = function( controlId, refresh ) {
          $('input[type="number"]', api.control(controlId).container ).each( function() {
                $(this).stepper();
          });
    };//api.czrSetupStepper()

    api.control.each(function(control){
          if ( ! _.has(control,'id') )
            return;
          if ( 'widget_' != control.id.substring(0, 'widget_'.length ) && 'nav_menu' != control.id.substring( 0, 'nav_menu'.length ) ) {
                api.czrSetupCheckbox(control.id);
          }
          if ( 'nav_menu_locations' != control.id.substring( 0, 'nav_menu_locations'.length ) ) {
                api.czrSetupSelect(control.id);
          }
          api.czrSetupStepper(control.id);
    });


    var fireHeaderButtons = function() {
          var $home_button = $('<span/>', { class:'customize-controls-home fa fa-home', html:'<span class="screen-reader-text">Home</span>' } );
          $.when( $('#customize-header-actions').append( $home_button ) )
                .done( function() {
                      $home_button
                            .keydown( function( event ) {
                                  if ( 9 === event.which ) // tab
                                    return;
                                  if ( 13 === event.which ) // enter
                                    this.click();
                                  event.preventDefault();
                            })
                            .on( 'click.customize-controls-home', function() {
                                  if ( api.section.has( api.czr_activeSectionId() ) ) {
                                        api.section( api.czr_activeSectionId() ).expanded( false );
                                  } else {
                                        api.section.each( function( _s ) {
                                            _s.expanded( false );
                                        });
                                  }
                                  api.panel.each( function( _p ) {
                                        _p.expanded( false );
                                  });
                            });
                });
      };
      fireHeaderButtons();
  });//end of $( function($) ) dom ready

})( wp, jQuery);/*! addEventListener Polyfill ie9- http://stackoverflow.com/a/27790212*/
window.addEventListener=window.addEventListener||function(a,b){window.attachEvent("on"+a,b)},/*!  Datenow Polyfill ie9- https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now */
Date.now||(Date.now=function(){return(new Date).getTime()}),/*! Object.create monkey patch ie8 http://stackoverflow.com/a/18020326 */
Object.create||(Object.create=function(a,b){function c(){}if("undefined"!=typeof b)throw"The multiple-argument version of Object.create is not provided by this browser and cannot be shimmed.";return c.prototype=a,new c}),/*! https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/filter */
Array.prototype.filter||(Array.prototype.filter=function(a){"use strict";if(void 0===this||null===this)throw new TypeError;var b=Object(this),c=b.length>>>0;if("function"!=typeof a)throw new TypeError;for(var d=[],e=arguments.length>=2?arguments[1]:void 0,f=0;f<c;f++)if(f in b){var g=b[f];a.call(e,g,f,b)&&d.push(g)}return d}),/*! map was added to the ECMA-262 standard in the 5th edition */
Array.prototype.map||(Array.prototype.map=function(a,b){var c,d,e;if(null===this)throw new TypeError(" this is null or not defined");var f=Object(this),g=f.length>>>0;if("function"!=typeof a)throw new TypeError(a+" is not a function");for(arguments.length>1&&(c=b),d=new Array(g),e=0;e<g;){var h,i;e in f&&(h=f[e],i=a.call(c,h,e,f),d[e]=i),e++}return d}),/*! iCheck v1.0.1 by Damir Sultanov, http://git.io/arlzeA, MIT Licensed */
"function"!=typeof jQuery.fn.iCheck&&!function(a){function b(a,b,e){var f=a[0],g=/er/.test(e)?p:/bl/.test(e)?n:l,h=e==q?{checked:f[l],disabled:f[n],indeterminate:"true"==a.attr(p)||"false"==a.attr(o)}:f[g];if(/^(ch|di|in)/.test(e)&&!h)c(a,g);else if(/^(un|en|de)/.test(e)&&h)d(a,g);else if(e==q)for(g in h)h[g]?c(a,g,!0):d(a,g,!0);else b&&"toggle"!=e||(b||a[u]("ifClicked"),h?f[r]!==k&&d(a,g):c(a,g))}function c(b,c,e){var q=b[0],u=b.parent(),v=c==l,x=c==p,y=c==n,z=x?o:v?m:"enabled",A=f(b,z+g(q[r])),B=f(b,c+g(q[r]));if(!0!==q[c]){if(!e&&c==l&&q[r]==k&&q.name){var C=b.closest("form"),D='input[name="'+q.name+'"]',D=C.length?C.find(D):a(D);D.each(function(){this!==q&&a(this).data(i)&&d(a(this),c)})}x?(q[c]=!0,q[l]&&d(b,l,"force")):(e||(q[c]=!0),v&&q[p]&&d(b,p,!1)),h(b,v,c,e)}q[n]&&f(b,w,!0)&&u.find("."+j).css(w,"default"),u[s](B||f(b,c)||""),y?u.attr("aria-disabled","true"):u.attr("aria-checked",x?"mixed":"true"),u[t](A||f(b,z)||"")}function d(a,b,c){var d=a[0],e=a.parent(),i=b==l,k=b==p,q=b==n,u=k?o:i?m:"enabled",v=f(a,u+g(d[r])),x=f(a,b+g(d[r]));!1!==d[b]&&((k||!c||"force"==c)&&(d[b]=!1),h(a,i,u,c)),!d[n]&&f(a,w,!0)&&e.find("."+j).css(w,"pointer"),e[t](x||f(a,b)||""),q?e.attr("aria-disabled","false"):e.attr("aria-checked","false"),e[s](v||f(a,u)||"")}function e(b,c){b.data(i)&&(b.parent().html(b.attr("style",b.data(i).s||"")),c&&b[u](c),b.off(".i").unwrap(),a(v+'[for="'+b[0].id+'"]').add(b.closest(v)).off(".i"))}function f(a,b,c){return a.data(i)?a.data(i).o[b+(c?"":"Class")]:void 0}function g(a){return a.charAt(0).toUpperCase()+a.slice(1)}function h(a,b,c,d){d||(b&&a[u]("ifToggled"),a[u]("ifChanged")[u]("if"+g(c)))}var i="iCheck",j=i+"-helper",k="radio",l="checked",m="un"+l,n="disabled",o="determinate",p="in"+o,q="update",r="type",s="addClass",t="removeClass",u="trigger",v="label",w="cursor",x=/ipad|iphone|ipod|android|blackberry|windows phone|opera mini|silk/i.test(navigator.userAgent);a.fn[i]=function(f,g){var h='input[type="checkbox"], input[type="'+k+'"]',m=a(),o=function(b){b.each(function(){var b=a(this);m=b.is(h)?m.add(b):m.add(b.find(h))})};if(/^(check|uncheck|toggle|indeterminate|determinate|disable|enable|update|destroy)$/i.test(f))return f=f.toLowerCase(),o(this),m.each(function(){var c=a(this);"destroy"==f?e(c,"ifDestroyed"):b(c,!0,f),a.isFunction(g)&&g()});if("object"!=typeof f&&f)return this;var w=a.extend({checkedClass:l,disabledClass:n,indeterminateClass:p,labelHover:!0,aria:!1},f),y=w.handle,z=w.hoverClass||"hover",A=w.focusClass||"focus",B=w.activeClass||"active",C=!!w.labelHover,D=w.labelHoverClass||"hover",E=0|(""+w.increaseArea).replace("%","");return("checkbox"==y||y==k)&&(h='input[type="'+y+'"]'),-50>E&&(E=-50),o(this),m.each(function(){var f=a(this);e(f);var g=this,h=g.id,m=-E+"%",o=100+2*E+"%",o={position:"absolute",top:m,left:m,display:"block",width:o,height:o,margin:0,padding:0,background:"#fff",border:0,opacity:0},m=x?{position:"absolute",visibility:"hidden"}:E?o:{position:"absolute",opacity:0},p="checkbox"==g[r]?w.checkboxClass||"icheckbox":w.radioClass||"i"+k,y=a(v+'[for="'+h+'"]').add(f.closest(v)),F=!!w.aria,G=i+"-"+Math.random().toString(36).substr(2,6),H='<div class="'+p+'" '+(F?'role="'+g[r]+'" ':"");F&&y.each(function(){H+='aria-labelledby="',this.id?H+=this.id:(this.id=G,H+=G),H+='"'}),H=f.wrap(H+"/>")[u]("ifCreated").parent().append(w.insert),o=a('<ins class="'+j+'"/>').css(o).appendTo(H),f.data(i,{o:w,s:f.attr("style")}).css(m),w.inheritClass&&H[s](g.className||""),w.inheritID&&h&&H.attr("id",i+"-"+h),"static"==H.css("position")&&H.css("position","relative"),b(f,!0,q),y.length&&y.on("click.i mouseover.i mouseout.i touchbegin.i touchend.i",function(c){var d=c[r],e=a(this);if(!g[n]){if("click"==d){if(a(c.target).is("a"))return;b(f,!1,!0)}else C&&(/ut|nd/.test(d)?(H[t](z),e[t](D)):(H[s](z),e[s](D)));if(!x)return!1;c.stopPropagation()}}),f.on("click.i focus.i blur.i keyup.i keydown.i keypress.i",function(a){var b=a[r];return a=a.keyCode,"click"!=b&&("keydown"==b&&32==a?(g[r]==k&&g[l]||(g[l]?d(f,l):c(f,l)),!1):void("keyup"==b&&g[r]==k?!g[l]&&c(f,l):/us|ur/.test(b)&&H["blur"==b?t:s](A)))}),o.on("click mousedown mouseup mouseover mouseout touchbegin.i touchend.i",function(a){var c=a[r],d=/wn|up/.test(c)?B:z;if(!g[n]){if("click"==c?b(f,!1,!0):(/wn|er|in/.test(c)?H[s](d):H[t](d+" "+B),y.length&&C&&d==z&&y[/ut|nd/.test(c)?t:s](D)),!x)return!1;a.stopPropagation()}})})}}(window.jQuery||window.Zepto),"function"!=typeof jQuery.fn.selecter&&!function(a,b){"use strict";function c(b){b=a.extend({},x,b||{}),null===w&&(w=a("body"));for(var c=a(this),e=0,f=c.length;f>e;e++)d(c.eq(e),b);return c}function d(b,c){if(!b.hasClass("selecter-element")){c=a.extend({},c,b.data("selecter-options")),c.external&&(c.links=!0);var d=b.find("option, optgroup"),g=d.filter("option"),h=g.filter(":selected"),n=""!==c.label?-1:g.index(h),p=c.links?"nav":"div";c.tabIndex=b[0].tabIndex,b[0].tabIndex=-1,c.multiple=b.prop("multiple"),c.disabled=b.is(":disabled");var q="<"+p+' class="selecter '+c.customClass;v?q+=" mobile":c.cover&&(q+=" cover"),q+=c.multiple?" multiple":" closed",c.disabled&&(q+=" disabled"),q+='" tabindex="'+c.tabIndex+'">',c.multiple||(q+='<span class="selecter-selected'+(""!==c.label?" placeholder":"")+'">',q+=a("<span></span").text(r(""!==c.label?c.label:h.text(),c.trim)).html(),q+="</span>"),q+='<div class="selecter-options">',q+="</div>",q+="</"+p+">",b.addClass("selecter-element").after(q);var s=b.next(".selecter"),u=a.extend({$select:b,$allOptions:d,$options:g,$selecter:s,$selected:s.find(".selecter-selected"),$itemsWrapper:s.find(".selecter-options"),index:-1,guid:t++},c);e(u),o(n,u),void 0!==a.fn.scroller&&u.$itemsWrapper.scroller(),u.$selecter.on("touchstart.selecter click.selecter",".selecter-selected",u,f).on("click.selecter",".selecter-item",u,j).on("close.selecter",u,i).data("selecter",u),u.$select.on("change.selecter",u,k),v||(u.$selecter.on("focus.selecter",u,l).on("blur.selecter",u,m),u.$select.on("focus.selecter",u,function(a){a.data.$selecter.trigger("focus")}))}}function e(b){for(var c="",d=b.links?"a":"span",e=0,f=0,g=b.$allOptions.length;g>f;f++){var h=b.$allOptions.eq(f);if("OPTGROUP"===h[0].tagName)c+='<span class="selecter-group',h.is(":disabled")&&(c+=" disabled"),c+='">'+h.attr("label")+"</span>";else{var i=h.val();h.attr("value")||h.attr("value",i),c+="<"+d+' class="selecter-item',h.is(":selected")&&""===b.label&&(c+=" selected"),h.is(":disabled")&&(c+=" disabled"),c+='" ',c+=b.links?'href="'+i+'"':'data-value="'+i+'"',c+=">"+a("<span></span>").text(r(h.text(),b.trim)).html()+"</"+d+">",e++}}b.$itemsWrapper.html(c),b.$items=b.$selecter.find(".selecter-item")}function f(c){c.preventDefault(),c.stopPropagation();var d=c.data;if(!d.$select.is(":disabled"))if(a(".selecter").not(d.$selecter).trigger("close.selecter",[d]),v){var e=d.$select[0];if(b.document.createEvent){var f=b.document.createEvent("MouseEvents");f.initMouseEvent("mousedown",!1,!0,b,0,0,0,0,0,!1,!1,!1,!1,0,null),e.dispatchEvent(f)}else e.fireEvent&&e.fireEvent("onmousedown")}else d.$selecter.hasClass("closed")?g(c):d.$selecter.hasClass("open")&&i(c)}function g(b){b.preventDefault(),b.stopPropagation();var c=b.data;if(!c.$selecter.hasClass("open")){var d=c.$selecter.offset(),e=w.outerHeight(),f=c.$itemsWrapper.outerHeight(!0),g=c.index>=0?c.$items.eq(c.index).position():{left:0,top:0};d.top+f>e&&c.$selecter.addClass("bottom"),c.$itemsWrapper.show(),c.$selecter.removeClass("closed").addClass("open"),w.on("click.selecter-"+c.guid,":not(.selecter-options)",c,h),void 0!==a.fn.scroller?c.$itemsWrapper.scroller("scroll",c.$itemsWrapper.find(".scroller-content").scrollTop()+g.top,0).scroller("reset"):c.$itemsWrapper.scrollTop(c.$itemsWrapper.scrollTop()+g.top)}}function h(b){b.preventDefault(),b.stopPropagation(),0===a(b.currentTarget).parents(".selecter").length&&i(b)}function i(a){a.preventDefault(),a.stopPropagation();var b=a.data;b.$selecter.hasClass("open")&&(b.$itemsWrapper.hide(),b.$selecter.removeClass("open bottom").addClass("closed"),w.off(".selecter-"+b.guid))}function j(b){b.preventDefault(),b.stopPropagation();var c=a(this),d=b.data;if(!d.$select.is(":disabled")){if(d.$itemsWrapper.is(":visible")){var e=d.$items.index(c);o(e,d),p(d)}d.multiple||i(b)}}function k(b,c){var d=a(this),e=b.data;if(!c&&!e.multiple){var f=e.$options.index(e.$options.filter("[value='"+s(d.val())+"']"));o(f,e),p(e)}}function l(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$select.is(":disabled")||c.multiple||(c.$selecter.addClass("focus").on("keydown.selecter"+c.guid,c,n),a(".selecter").not(c.$selecter).trigger("close.selecter",[c]))}function m(b){b.preventDefault(),b.stopPropagation();var c=b.data;c.$selecter.removeClass("focus").off("keydown.selecter"+c.guid+" keyup.selecter"+c.guid),a(".selecter").not(c.$selecter).trigger("close.selecter",[c])}function n(b){var c=b.data;if(13===b.keyCode)c.$selecter.hasClass("open")&&(i(b),o(c.index,c)),p(c);else if(!(9===b.keyCode||b.metaKey||b.altKey||b.ctrlKey||b.shiftKey)){b.preventDefault(),b.stopPropagation();var d=c.$items.length-1,e=c.index<0?0:c.index;if(a.inArray(b.keyCode,u?[38,40,37,39]:[38,40])>-1)e+=38===b.keyCode||u&&37===b.keyCode?-1:1,0>e&&(e=0),e>d&&(e=d);else{var f,g,h=String.fromCharCode(b.keyCode).toUpperCase();for(g=c.index+1;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}if(0>e)for(g=0;d>=g;g++)if(f=c.$options.eq(g).text().charAt(0).toUpperCase(),f===h){e=g;break}}e>=0&&o(e,c)}}function o(a,b){var c=b.$items.eq(a),d=c.hasClass("selected"),e=c.hasClass("disabled");if(!e){if(-1===a&&""!==b.label)b.$selected.html(b.label);else if(d)b.multiple&&(b.$options.eq(a).prop("selected",null),c.removeClass("selected"));else{var f=c.html();c.data("value"),b.multiple?b.$options.eq(a).prop("selected",!0):(b.$selected.html(f).removeClass("placeholder"),b.$items.filter(".selected").removeClass("selected"),b.$select[0].selectedIndex=a),c.addClass("selected")}(!d||b.multiple)&&(b.index=a)}}function p(a){a.links?q(a):(a.callback.call(a.$selecter,a.$select.val(),a.index),a.$select.trigger("change",[!0]))}function q(a){var c=a.$select.val();a.external?b.open(c):b.location.href=c}function r(a,b){return 0===b?a:a.length>b?a.substring(0,b)+"...":a}function s(a){return a.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g,"\\$1")}var t=0,u=b.navigator.userAgent.toLowerCase().indexOf("firefox")>-1,v=/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(b.navigator.userAgent||b.navigator.vendor||b.opera),w=null,x={callback:a.noop,cover:!1,customClass:"",label:"",external:!1,links:!1,trim:0},y={defaults:function(b){return x=a.extend(x,b||{}),a(this)},disable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).addClass("disabled"),e.$options.eq(f).prop("disabled",!0)}else e.$selecter.hasClass("open")&&e.$selecter.find(".selecter-selected").trigger("click.selecter"),e.$selecter.addClass("disabled"),e.$select.prop("disabled",!0)})},enable:function(b){return a(this).each(function(c,d){var e=a(d).next(".selecter").data("selecter");if(e)if("undefined"!=typeof b){var f=e.$items.index(e.$items.filter("[data-value="+b+"]"));e.$items.eq(f).removeClass("disabled"),e.$options.eq(f).prop("disabled",!1)}else e.$selecter.removeClass("disabled"),e.$select.prop("disabled",!1)})},destroy:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");d&&(d.$selecter.hasClass("open")&&d.$selecter.find(".selecter-selected").trigger("click.selecter"),void 0!==a.fn.scroller&&d.$selecter.find(".selecter-options").scroller("destroy"),d.$select[0].tabIndex=d.tabIndex,d.$select.off(".selecter").removeClass("selecter-element").show(),d.$selecter.off(".selecter").remove())})},refresh:function(){return a(this).each(function(b,c){var d=a(c).next(".selecter").data("selecter");if(d){var f=d.index;d.$allOptions=d.$select.find("option, optgroup"),d.$options=d.$allOptions.filter("option"),d.index=-1,f=d.$options.index(d.$options.filter(":selected")),e(d),o(f,d)}})}};a.fn.selecter=function(a){return y[a]?y[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:c.apply(this,arguments)},a.selecter=function(a){"defaults"===a&&y.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,window),"function"!=typeof jQuery.fn.stepper&&!function(a){"use strict";function b(b){b=a.extend({},k,b||{});for(var d=a(this),e=0,f=d.length;f>e;e++)c(d.eq(e),b);return d}function c(b,c){if(!b.hasClass("stepper-input")){c=a.extend({},c,b.data("stepper-options"));var e=parseFloat(b.attr("min")),f=parseFloat(b.attr("max")),g=parseFloat(b.attr("step"))||1;b.addClass("stepper-input").wrap('<div class="stepper '+c.customClass+'" />').after('<span class="stepper-arrow up">'+c.labels.up+'</span><span class="stepper-arrow down">'+c.labels.down+"</span>");var h=b.parent(".stepper"),j=a.extend({$stepper:h,$input:b,$arrow:h.find(".stepper-arrow"),min:void 0!==typeof e&&!isNaN(e)&&e,max:void 0!==typeof f&&!isNaN(f)&&f,step:void 0===typeof g||isNaN(g)?1:g,timer:null},c);j.digits=i(j.step),b.is(":disabled")&&h.addClass("disabled"),h.on("touchstart.stepper mousedown.stepper",".stepper-arrow",j,d).data("stepper",j)}}function d(b){b.preventDefault(),b.stopPropagation(),e(b);var c=b.data;if(!c.$input.is(":disabled")&&!c.$stepper.hasClass("disabled")){var d=a(b.target).hasClass("up")?c.step:-c.step;c.timer=g(c.timer,125,function(){f(c,d,!1)}),f(c,d),a("body").on("touchend.stepper mouseup.stepper",c,e)}}function e(b){b.preventDefault(),b.stopPropagation();var c=b.data;h(c.timer),a("body").off(".stepper")}function f(a,b){var c=parseFloat(a.$input.val()),d=b;void 0===typeof c||isNaN(c)?d=a.min!==!1?a.min:0:a.min!==!1&&c<a.min?d=a.min:d+=c;var e=(d-a.min)%a.step;0!==e&&(d-=e),a.min!==!1&&d<a.min&&(d=a.min),a.max!==!1&&d>a.max&&(d-=a.step),d!==c&&(d=j(d,a.digits),a.$input.val(d).trigger("change"))}function g(a,b,c){return h(a),setInterval(c,b)}function h(a){a&&(clearInterval(a),a=null)}function i(a){var b=String(a);return b.indexOf(".")>-1?b.length-b.indexOf(".")-1:0}function j(a,b){var c=Math.pow(10,b);return Math.round(a*c)/c}var k={customClass:"",labels:{up:"Up",down:"Down"}},l={defaults:function(b){return k=a.extend(k,b||{}),a(this)},destroy:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$stepper.off(".stepper").find(".stepper-arrow").remove(),b.$input.unwrap().removeClass("stepper-input"))})},disable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled","disabled"),b.$stepper.addClass("disabled"))})},enable:function(){return a(this).each(function(){var b=a(this).data("stepper");b&&(b.$input.attr("disabled",null),b.$stepper.removeClass("disabled"))})}};a.fn.stepper=function(a){return l[a]?l[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?this:b.apply(this,arguments)},a.stepper=function(a){"defaults"===a&&l.defaults.apply(this,Array.prototype.slice.call(arguments,1))}}(jQuery,this),/*! Select2 4.0.3 | https://github.com/select2/select2/blob/master/LICENSE.md */!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){var b=function(){if(a&&a.fn&&a.fn.select2&&a.fn.select2.amd)var b=a.fn.select2.amd;var b;return function(){if(!b||!b.requirejs){b?c=b:b={};var a,c,d;!function(b){function e(a,b){return u.call(a,b)}function f(a,b){var c,d,e,f,g,h,i,j,k,l,m,n=b&&b.split("/"),o=s.map,p=o&&o["*"]||{};if(a&&"."===a.charAt(0))if(b){for(a=a.split("/"),g=a.length-1,s.nodeIdCompat&&w.test(a[g])&&(a[g]=a[g].replace(w,"")),a=n.slice(0,n.length-1).concat(a),k=0;k<a.length;k+=1)if(m=a[k],"."===m)a.splice(k,1),k-=1;else if(".."===m){if(1===k&&(".."===a[2]||".."===a[0]))break;k>0&&(a.splice(k-1,2),k-=2)}a=a.join("/")}else 0===a.indexOf("./")&&(a=a.substring(2));if((n||p)&&o){for(c=a.split("/"),k=c.length;k>0;k-=1){if(d=c.slice(0,k).join("/"),n)for(l=n.length;l>0;l-=1)if(e=o[n.slice(0,l).join("/")],e&&(e=e[d])){f=e,h=k;break}if(f)break;!i&&p&&p[d]&&(i=p[d],j=k)}!f&&i&&(f=i,h=j),f&&(c.splice(0,h,f),a=c.join("/"))}return a}function g(a,c){return function(){var d=v.call(arguments,0);return"string"!=typeof d[0]&&1===d.length&&d.push(null),n.apply(b,d.concat([a,c]))}}function h(a){return function(b){return f(b,a)}}function i(a){return function(b){q[a]=b}}function j(a){if(e(r,a)){var c=r[a];delete r[a],t[a]=!0,m.apply(b,c)}if(!e(q,a)&&!e(t,a))throw new Error("No "+a);return q[a]}function k(a){var b,c=a?a.indexOf("!"):-1;return c>-1&&(b=a.substring(0,c),a=a.substring(c+1,a.length)),[b,a]}function l(a){return function(){return s&&s.config&&s.config[a]||{}}}var m,n,o,p,q={},r={},s={},t={},u=Object.prototype.hasOwnProperty,v=[].slice,w=/\.js$/;o=function(a,b){var c,d=k(a),e=d[0];return a=d[1],e&&(e=f(e,b),c=j(e)),e?a=c&&c.normalize?c.normalize(a,h(b)):f(a,b):(a=f(a,b),d=k(a),e=d[0],a=d[1],e&&(c=j(e))),{f:e?e+"!"+a:a,n:a,pr:e,p:c}},p={require:function(a){return g(a)},exports:function(a){var b=q[a];return"undefined"!=typeof b?b:q[a]={}},module:function(a){return{id:a,uri:"",exports:q[a],config:l(a)}}},m=function(a,c,d,f){var h,k,l,m,n,s,u=[],v=typeof d;if(f=f||a,"undefined"===v||"function"===v){for(c=!c.length&&d.length?["require","exports","module"]:c,n=0;n<c.length;n+=1)if(m=o(c[n],f),k=m.f,"require"===k)u[n]=p.require(a);else if("exports"===k)u[n]=p.exports(a),s=!0;else if("module"===k)h=u[n]=p.module(a);else if(e(q,k)||e(r,k)||e(t,k))u[n]=j(k);else{if(!m.p)throw new Error(a+" missing "+k);m.p.load(m.n,g(f,!0),i(k),{}),u[n]=q[k]}l=d?d.apply(q[a],u):void 0,a&&(h&&h.exports!==b&&h.exports!==q[a]?q[a]=h.exports:l===b&&s||(q[a]=l))}else a&&(q[a]=d)},a=c=n=function(a,c,d,e,f){if("string"==typeof a)return p[a]?p[a](c):j(o(a,c).f);if(!a.splice){if(s=a,s.deps&&n(s.deps,s.callback),!c)return;c.splice?(a=c,c=d,d=null):a=b}return c=c||function(){},"function"==typeof d&&(d=e,e=f),e?m(b,a,c,d):setTimeout(function(){m(b,a,c,d)},4),n},n.config=function(a){return n(a)},a._defined=q,d=function(a,b,c){if("string"!=typeof a)throw new Error("See almond README: incorrect module build, no module name");b.splice||(c=b,b=[]),e(q,a)||e(r,a)||(r[a]=[a,b,c])},d.amd={jQuery:!0}}(),b.requirejs=a,b.require=c,b.define=d}}(),b.define("almond",function(){}),b.define("jquery",[],function(){var b=a||$;return null==b&&console&&console.error&&console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page."),b}),b.define("select2/utils",["jquery"],function(a){function b(a){var b=a.prototype,c=[];for(var d in b){var e=b[d];"function"==typeof e&&"constructor"!==d&&c.push(d)}return c}var c={};c.Extend=function(a,b){function c(){this.constructor=a}var d={}.hasOwnProperty;for(var e in b)d.call(b,e)&&(a[e]=b[e]);return c.prototype=b.prototype,a.prototype=new c,a.__super__=b.prototype,a},c.Decorate=function(a,c){function d(){var b=Array.prototype.unshift,d=c.prototype.constructor.length,e=a.prototype.constructor;d>0&&(b.call(arguments,a.prototype.constructor),e=c.prototype.constructor),e.apply(this,arguments)}function e(){this.constructor=d}var f=b(c),g=b(a);c.displayName=a.displayName,d.prototype=new e;for(var h=0;h<g.length;h++){var i=g[h];d.prototype[i]=a.prototype[i]}for(var j=(function(a){var b=function(){};a in d.prototype&&(b=d.prototype[a]);var e=c.prototype[a];return function(){var a=Array.prototype.unshift;return a.call(arguments,b),e.apply(this,arguments)}}),k=0;k<f.length;k++){var l=f[k];d.prototype[l]=j(l)}return d};var d=function(){this.listeners={}};return d.prototype.on=function(a,b){this.listeners=this.listeners||{},a in this.listeners?this.listeners[a].push(b):this.listeners[a]=[b]},d.prototype.trigger=function(a){var b=Array.prototype.slice,c=b.call(arguments,1);this.listeners=this.listeners||{},null==c&&(c=[]),0===c.length&&c.push({}),c[0]._type=a,a in this.listeners&&this.invoke(this.listeners[a],b.call(arguments,1)),"*"in this.listeners&&this.invoke(this.listeners["*"],arguments)},d.prototype.invoke=function(a,b){for(var c=0,d=a.length;d>c;c++)a[c].apply(this,b)},c.Observable=d,c.generateChars=function(a){for(var b="",c=0;a>c;c++){var d=Math.floor(36*Math.random());b+=d.toString(36)}return b},c.bind=function(a,b){return function(){a.apply(b,arguments)}},c._convertData=function(a){for(var b in a){var c=b.split("-"),d=a;if(1!==c.length){for(var e=0;e<c.length;e++){var f=c[e];f=f.substring(0,1).toLowerCase()+f.substring(1),f in d||(d[f]={}),e==c.length-1&&(d[f]=a[b]),d=d[f]}delete a[b]}}return a},c.hasScroll=function(b,c){var d=a(c),e=c.style.overflowX,f=c.style.overflowY;return(e!==f||"hidden"!==f&&"visible"!==f)&&("scroll"===e||"scroll"===f||(d.innerHeight()<c.scrollHeight||d.innerWidth()<c.scrollWidth))},c.escapeMarkup=function(a){var b={"\\":"&#92;","&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#47;"};return"string"!=typeof a?a:String(a).replace(/[&<>"'\/\\]/g,function(a){return b[a]})},c.appendMany=function(b,c){if("1.7"===a.fn.jquery.substr(0,3)){var d=a();a.map(c,function(a){d=d.add(a)}),c=d}b.append(c)},c}),b.define("select2/results",["jquery","./utils"],function(a,b){function c(a,b,d){this.$element=a,this.data=d,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<ul class="select2-results__options" role="tree"></ul>');return this.options.get("multiple")&&b.attr("aria-multiselectable","true"),this.$results=b,b},c.prototype.clear=function(){this.$results.empty()},c.prototype.displayMessage=function(b){var c=this.options.get("escapeMarkup");this.clear(),this.hideLoading();var d=a('<li role="treeitem" aria-live="assertive" class="select2-results__option"></li>'),e=this.options.get("translations").get(b.message);d.append(c(e(b.args))),d[0].className+=" select2-results__message",this.$results.append(d)},c.prototype.hideMessages=function(){this.$results.find(".select2-results__message").remove()},c.prototype.append=function(a){this.hideLoading();var b=[];if(null==a.results||0===a.results.length)return void(0===this.$results.children().length&&this.trigger("results:message",{message:"noResults"}));a.results=this.sort(a.results);for(var c=0;c<a.results.length;c++){var d=a.results[c],e=this.option(d);b.push(e)}this.$results.append(b)},c.prototype.position=function(a,b){var c=b.find(".select2-results");c.append(a)},c.prototype.sort=function(a){var b=this.options.get("sorter");return b(a)},c.prototype.highlightFirstItem=function(){var a=this.$results.find(".select2-results__option[aria-selected]"),b=a.filter("[aria-selected=true]");b.length>0?b.first().trigger("mouseenter"):a.first().trigger("mouseenter"),this.ensureHighlightVisible()},c.prototype.setClasses=function(){var b=this;this.data.current(function(c){var d=a.map(c,function(a){return a.id.toString()}),e=b.$results.find(".select2-results__option[aria-selected]");e.each(function(){var b=a(this),c=a.data(this,"data"),e=""+c.id;null!=c.element&&c.element.selected||null==c.element&&a.inArray(e,d)>-1?b.attr("aria-selected","true"):b.attr("aria-selected","false")})})},c.prototype.showLoading=function(a){this.hideLoading();var b=this.options.get("translations").get("searching"),c={disabled:!0,loading:!0,text:b(a)},d=this.option(c);d.className+=" loading-results",this.$results.prepend(d)},c.prototype.hideLoading=function(){this.$results.find(".loading-results").remove()},c.prototype.option=function(b){var c=document.createElement("li");c.className="select2-results__option";var d={role:"treeitem","aria-selected":"false"};b.disabled&&(delete d["aria-selected"],d["aria-disabled"]="true"),null==b.id&&delete d["aria-selected"],null!=b._resultId&&(c.id=b._resultId),b.title&&(c.title=b.title),b.children&&(d.role="group",d["aria-label"]=b.text,delete d["aria-selected"]);for(var e in d){var f=d[e];c.setAttribute(e,f)}if(b.children){var g=a(c),h=document.createElement("strong");h.className="select2-results__group",a(h),this.template(b,h);for(var i=[],j=0;j<b.children.length;j++){var k=b.children[j],l=this.option(k);i.push(l)}var m=a("<ul></ul>",{"class":"select2-results__options select2-results__options--nested"});m.append(i),g.append(h),g.append(m)}else this.template(b,c);return a.data(c,"data",b),c},c.prototype.bind=function(b,c){var d=this,e=b.id+"-results";this.$results.attr("id",e),b.on("results:all",function(a){d.clear(),d.append(a.data),b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("results:append",function(a){d.append(a.data),b.isOpen()&&d.setClasses()}),b.on("query",function(a){d.hideMessages(),d.showLoading(a)}),b.on("select",function(){b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("unselect",function(){b.isOpen()&&(d.setClasses(),d.highlightFirstItem())}),b.on("open",function(){d.$results.attr("aria-expanded","true"),d.$results.attr("aria-hidden","false"),d.setClasses(),d.ensureHighlightVisible()}),b.on("close",function(){d.$results.attr("aria-expanded","false"),d.$results.attr("aria-hidden","true"),d.$results.removeAttr("aria-activedescendant")}),b.on("results:toggle",function(){var a=d.getHighlightedResults();0!==a.length&&a.trigger("mouseup")}),b.on("results:select",function(){var a=d.getHighlightedResults();if(0!==a.length){var b=a.data("data");"true"==a.attr("aria-selected")?d.trigger("close",{}):d.trigger("select",{data:b})}}),b.on("results:previous",function(){var a=d.getHighlightedResults(),b=d.$results.find("[aria-selected]"),c=b.index(a);if(0!==c){var e=c-1;0===a.length&&(e=0);var f=b.eq(e);f.trigger("mouseenter");var g=d.$results.offset().top,h=f.offset().top,i=d.$results.scrollTop()+(h-g);0===e?d.$results.scrollTop(0):0>h-g&&d.$results.scrollTop(i)}}),b.on("results:next",function(){var a=d.getHighlightedResults(),b=d.$results.find("[aria-selected]"),c=b.index(a),e=c+1;if(!(e>=b.length)){var f=b.eq(e);f.trigger("mouseenter");var g=d.$results.offset().top+d.$results.outerHeight(!1),h=f.offset().top+f.outerHeight(!1),i=d.$results.scrollTop()+h-g;0===e?d.$results.scrollTop(0):h>g&&d.$results.scrollTop(i)}}),b.on("results:focus",function(a){a.element.addClass("select2-results__option--highlighted")}),b.on("results:message",function(a){d.displayMessage(a)}),a.fn.mousewheel&&this.$results.on("mousewheel",function(a){var b=d.$results.scrollTop(),c=d.$results.get(0).scrollHeight-b+a.deltaY,e=a.deltaY>0&&b-a.deltaY<=0,f=a.deltaY<0&&c<=d.$results.height();e?(d.$results.scrollTop(0),a.preventDefault(),a.stopPropagation()):f&&(d.$results.scrollTop(d.$results.get(0).scrollHeight-d.$results.height()),a.preventDefault(),a.stopPropagation())}),this.$results.on("mouseup",".select2-results__option[aria-selected]",function(b){var c=a(this),e=c.data("data");return"true"===c.attr("aria-selected")?void(d.options.get("multiple")?d.trigger("unselect",{originalEvent:b,data:e}):d.trigger("close",{})):void d.trigger("select",{originalEvent:b,data:e})}),this.$results.on("mouseenter",".select2-results__option[aria-selected]",function(b){var c=a(this).data("data");d.getHighlightedResults().removeClass("select2-results__option--highlighted"),d.trigger("results:focus",{data:c,element:a(this)})})},c.prototype.getHighlightedResults=function(){var a=this.$results.find(".select2-results__option--highlighted");return a},c.prototype.destroy=function(){this.$results.remove()},c.prototype.ensureHighlightVisible=function(){var a=this.getHighlightedResults();if(0!==a.length){var b=this.$results.find("[aria-selected]"),c=b.index(a),d=this.$results.offset().top,e=a.offset().top,f=this.$results.scrollTop()+(e-d),g=e-d;f-=2*a.outerHeight(!1),2>=c?this.$results.scrollTop(0):(g>this.$results.outerHeight()||0>g)&&this.$results.scrollTop(f)}},c.prototype.template=function(b,c){var d=this.options.get("templateResult"),e=this.options.get("escapeMarkup"),f=d(b,c);null==f?c.style.display="none":"string"==typeof f?c.innerHTML=e(f):a(c).append(f)},c}),b.define("select2/keys",[],function(){var a={BACKSPACE:8,TAB:9,ENTER:13,SHIFT:16,CTRL:17,ALT:18,ESC:27,SPACE:32,PAGE_UP:33,PAGE_DOWN:34,END:35,HOME:36,LEFT:37,UP:38,RIGHT:39,DOWN:40,DELETE:46};return a}),b.define("select2/selection/base",["jquery","../utils","../keys"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,b.Observable),d.prototype.render=function(){var b=a('<span class="select2-selection" role="combobox"  aria-haspopup="true" aria-expanded="false"></span>');return this._tabindex=0,null!=this.$element.data("old-tabindex")?this._tabindex=this.$element.data("old-tabindex"):null!=this.$element.attr("tabindex")&&(this._tabindex=this.$element.attr("tabindex")),b.attr("title",this.$element.attr("title")),b.attr("tabindex",this._tabindex),this.$selection=b,b},d.prototype.bind=function(a,b){var d=this,e=(a.id+"-container",a.id+"-results");this.container=a,this.$selection.on("focus",function(a){d.trigger("focus",a)}),this.$selection.on("blur",function(a){d._handleBlur(a)}),this.$selection.on("keydown",function(a){d.trigger("keypress",a),a.which===c.SPACE&&a.preventDefault()}),a.on("results:focus",function(a){d.$selection.attr("aria-activedescendant",a.data._resultId)}),a.on("selection:update",function(a){d.update(a.data)}),a.on("open",function(){d.$selection.attr("aria-expanded","true"),d.$selection.attr("aria-owns",e),d._attachCloseHandler(a)}),a.on("close",function(){d.$selection.attr("aria-expanded","false"),d.$selection.removeAttr("aria-activedescendant"),d.$selection.removeAttr("aria-owns"),d.$selection.focus(),d._detachCloseHandler(a)}),a.on("enable",function(){d.$selection.attr("tabindex",d._tabindex)}),a.on("disable",function(){d.$selection.attr("tabindex","-1")})},d.prototype._handleBlur=function(b){var c=this;window.setTimeout(function(){document.activeElement==c.$selection[0]||a.contains(c.$selection[0],document.activeElement)||c.trigger("blur",b)},1)},d.prototype._attachCloseHandler=function(b){a(document.body).on("mousedown.select2."+b.id,function(b){var c=a(b.target),d=c.closest(".select2"),e=a(".select2.select2-container--open");e.each(function(){var b=a(this);if(this!=d[0]){var c=b.data("element");c.select2("close")}})})},d.prototype._detachCloseHandler=function(b){a(document.body).off("mousedown.select2."+b.id)},d.prototype.position=function(a,b){var c=b.find(".selection");c.append(a)},d.prototype.destroy=function(){this._detachCloseHandler(this.container)},d.prototype.update=function(a){throw new Error("The `update` method must be defined in child classes.")},d}),b.define("select2/selection/single",["jquery","./base","../utils","../keys"],function(a,b,c,d){function e(){e.__super__.constructor.apply(this,arguments)}return c.Extend(e,b),e.prototype.render=function(){var a=e.__super__.render.call(this);return a.addClass("select2-selection--single"),a.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'),a},e.prototype.bind=function(a,b){var c=this;e.__super__.bind.apply(this,arguments);var d=a.id+"-container";this.$selection.find(".select2-selection__rendered").attr("id",d),this.$selection.attr("aria-labelledby",d),this.$selection.on("mousedown",function(a){1===a.which&&c.trigger("toggle",{originalEvent:a})}),this.$selection.on("focus",function(a){}),this.$selection.on("blur",function(a){}),a.on("focus",function(b){a.isOpen()||c.$selection.focus()}),a.on("selection:update",function(a){c.update(a.data)})},e.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},e.prototype.display=function(a,b){var c=this.options.get("templateSelection"),d=this.options.get("escapeMarkup");return d(c(a,b))},e.prototype.selectionContainer=function(){return a("<span></span>")},e.prototype.update=function(a){if(0===a.length)return void this.clear();var b=a[0],c=this.$selection.find(".select2-selection__rendered"),d=this.display(b,c);c.empty().append(d),c.prop("title",b.title||b.text)},e}),b.define("select2/selection/multiple",["jquery","./base","../utils"],function(a,b,c){
function d(a,b){d.__super__.constructor.apply(this,arguments)}return c.Extend(d,b),d.prototype.render=function(){var a=d.__super__.render.call(this);return a.addClass("select2-selection--multiple"),a.html('<ul class="select2-selection__rendered"></ul>'),a},d.prototype.bind=function(b,c){var e=this;d.__super__.bind.apply(this,arguments),this.$selection.on("click",function(a){e.trigger("toggle",{originalEvent:a})}),this.$selection.on("click",".select2-selection__choice__remove",function(b){if(!e.options.get("disabled")){var c=a(this),d=c.parent(),f=d.data("data");e.trigger("unselect",{originalEvent:b,data:f})}})},d.prototype.clear=function(){this.$selection.find(".select2-selection__rendered").empty()},d.prototype.display=function(a,b){var c=this.options.get("templateSelection"),d=this.options.get("escapeMarkup");return d(c(a,b))},d.prototype.selectionContainer=function(){var b=a('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>');return b},d.prototype.update=function(a){if(this.clear(),0!==a.length){for(var b=[],d=0;d<a.length;d++){var e=a[d],f=this.selectionContainer(),g=this.display(e,f);f.append(g),f.prop("title",e.title||e.text),f.data("data",e),b.push(f)}var h=this.$selection.find(".select2-selection__rendered");c.appendMany(h,b)}},d}),b.define("select2/selection/placeholder",["../utils"],function(a){function b(a,b,c){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c)}return b.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},b.prototype.createPlaceholder=function(a,b){var c=this.selectionContainer();return c.html(this.display(b)),c.addClass("select2-selection__placeholder").removeClass("select2-selection__choice"),c},b.prototype.update=function(a,b){var c=1==b.length&&b[0].id!=this.placeholder.id,d=b.length>1;if(d||c)return a.call(this,b);this.clear();var e=this.createPlaceholder(this.placeholder);this.$selection.find(".select2-selection__rendered").append(e)},b}),b.define("select2/selection/allowClear",["jquery","../keys"],function(a,b){function c(){}return c.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),null==this.placeholder&&this.options.get("debug")&&window.console&&console.error&&console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option."),this.$selection.on("mousedown",".select2-selection__clear",function(a){d._handleClear(a)}),b.on("keypress",function(a){d._handleKeyboardClear(a,b)})},c.prototype._handleClear=function(a,b){if(!this.options.get("disabled")){var c=this.$selection.find(".select2-selection__clear");if(0!==c.length){b.stopPropagation();for(var d=c.data("data"),e=0;e<d.length;e++){var f={data:d[e]};if(this.trigger("unselect",f),f.prevented)return}this.$element.val(this.placeholder.id).trigger("change"),this.trigger("toggle",{})}}},c.prototype._handleKeyboardClear=function(a,c,d){d.isOpen()||(c.which==b.DELETE||c.which==b.BACKSPACE)&&this._handleClear(c)},c.prototype.update=function(b,c){if(b.call(this,c),!(this.$selection.find(".select2-selection__placeholder").length>0||0===c.length)){var d=a('<span class="select2-selection__clear">&times;</span>');d.data("data",c),this.$selection.find(".select2-selection__rendered").prepend(d)}},c}),b.define("select2/selection/search",["jquery","../utils","../keys"],function(a,b,c){function d(a,b,c){a.call(this,b,c)}return d.prototype.render=function(b){var c=a('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" /></li>');this.$searchContainer=c,this.$search=c.find("input");var d=b.call(this);return this._transferTabIndex(),d},d.prototype.bind=function(a,b,d){var e=this;a.call(this,b,d),b.on("open",function(){e.$search.trigger("focus")}),b.on("close",function(){e.$search.val(""),e.$search.removeAttr("aria-activedescendant"),e.$search.trigger("focus")}),b.on("enable",function(){e.$search.prop("disabled",!1),e._transferTabIndex()}),b.on("disable",function(){e.$search.prop("disabled",!0)}),b.on("focus",function(a){e.$search.trigger("focus")}),b.on("results:focus",function(a){e.$search.attr("aria-activedescendant",a.id)}),this.$selection.on("focusin",".select2-search--inline",function(a){e.trigger("focus",a)}),this.$selection.on("focusout",".select2-search--inline",function(a){e._handleBlur(a)}),this.$selection.on("keydown",".select2-search--inline",function(a){a.stopPropagation(),e.trigger("keypress",a),e._keyUpPrevented=a.isDefaultPrevented();var b=a.which;if(b===c.BACKSPACE&&""===e.$search.val()){var d=e.$searchContainer.prev(".select2-selection__choice");if(d.length>0){var f=d.data("data");e.searchRemoveChoice(f),a.preventDefault()}}});var f=document.documentMode,g=f&&11>=f;this.$selection.on("input.searchcheck",".select2-search--inline",function(a){return g?void e.$selection.off("input.search input.searchcheck"):void e.$selection.off("keyup.search")}),this.$selection.on("keyup.search input.search",".select2-search--inline",function(a){if(g&&"input"===a.type)return void e.$selection.off("input.search input.searchcheck");var b=a.which;b!=c.SHIFT&&b!=c.CTRL&&b!=c.ALT&&b!=c.TAB&&e.handleSearch(a)})},d.prototype._transferTabIndex=function(a){this.$search.attr("tabindex",this.$selection.attr("tabindex")),this.$selection.attr("tabindex","-1")},d.prototype.createPlaceholder=function(a,b){this.$search.attr("placeholder",b.text)},d.prototype.update=function(a,b){var c=this.$search[0]==document.activeElement;this.$search.attr("placeholder",""),a.call(this,b),this.$selection.find(".select2-selection__rendered").append(this.$searchContainer),this.resizeSearch(),c&&this.$search.focus()},d.prototype.handleSearch=function(){if(this.resizeSearch(),!this._keyUpPrevented){var a=this.$search.val();this.trigger("query",{term:a})}this._keyUpPrevented=!1},d.prototype.searchRemoveChoice=function(a,b){this.trigger("unselect",{data:b}),this.$search.val(b.text),this.handleSearch()},d.prototype.resizeSearch=function(){this.$search.css("width","25px");var a="";if(""!==this.$search.attr("placeholder"))a=this.$selection.find(".select2-selection__rendered").innerWidth();else{var b=this.$search.val().length+1;a=.75*b+"em"}this.$search.css("width",a)},d}),b.define("select2/selection/eventRelay",["jquery"],function(a){function b(){}return b.prototype.bind=function(b,c,d){var e=this,f=["open","opening","close","closing","select","selecting","unselect","unselecting"],g=["opening","closing","selecting","unselecting"];b.call(this,c,d),c.on("*",function(b,c){if(-1!==a.inArray(b,f)){c=c||{};var d=a.Event("select2:"+b,{params:c});e.$element.trigger(d),-1!==a.inArray(b,g)&&(c.prevented=d.isDefaultPrevented())}})},b}),b.define("select2/translation",["jquery","require"],function(a,b){function c(a){this.dict=a||{}}return c.prototype.all=function(){return this.dict},c.prototype.get=function(a){return this.dict[a]},c.prototype.extend=function(b){this.dict=a.extend({},b.all(),this.dict)},c._cache={},c.loadPath=function(a){if(!(a in c._cache)){var d=b(a);c._cache[a]=d}return new c(c._cache[a])},c}),b.define("select2/diacritics",[],function(){var a={"Ⓐ":"A","Ａ":"A","À":"A","Á":"A","Â":"A","Ầ":"A","Ấ":"A","Ẫ":"A","Ẩ":"A","Ã":"A","Ā":"A","Ă":"A","Ằ":"A","Ắ":"A","Ẵ":"A","Ẳ":"A","Ȧ":"A","Ǡ":"A","Ä":"A","Ǟ":"A","Ả":"A","Å":"A","Ǻ":"A","Ǎ":"A","Ȁ":"A","Ȃ":"A","Ạ":"A","Ậ":"A","Ặ":"A","Ḁ":"A","Ą":"A","Ⱥ":"A","Ɐ":"A","Ꜳ":"AA","Æ":"AE","Ǽ":"AE","Ǣ":"AE","Ꜵ":"AO","Ꜷ":"AU","Ꜹ":"AV","Ꜻ":"AV","Ꜽ":"AY","Ⓑ":"B","Ｂ":"B","Ḃ":"B","Ḅ":"B","Ḇ":"B","Ƀ":"B","Ƃ":"B","Ɓ":"B","Ⓒ":"C","Ｃ":"C","Ć":"C","Ĉ":"C","Ċ":"C","Č":"C","Ç":"C","Ḉ":"C","Ƈ":"C","Ȼ":"C","Ꜿ":"C","Ⓓ":"D","Ｄ":"D","Ḋ":"D","Ď":"D","Ḍ":"D","Ḑ":"D","Ḓ":"D","Ḏ":"D","Đ":"D","Ƌ":"D","Ɗ":"D","Ɖ":"D","Ꝺ":"D","Ǳ":"DZ","Ǆ":"DZ","ǲ":"Dz","ǅ":"Dz","Ⓔ":"E","Ｅ":"E","È":"E","É":"E","Ê":"E","Ề":"E","Ế":"E","Ễ":"E","Ể":"E","Ẽ":"E","Ē":"E","Ḕ":"E","Ḗ":"E","Ĕ":"E","Ė":"E","Ë":"E","Ẻ":"E","Ě":"E","Ȅ":"E","Ȇ":"E","Ẹ":"E","Ệ":"E","Ȩ":"E","Ḝ":"E","Ę":"E","Ḙ":"E","Ḛ":"E","Ɛ":"E","Ǝ":"E","Ⓕ":"F","Ｆ":"F","Ḟ":"F","Ƒ":"F","Ꝼ":"F","Ⓖ":"G","Ｇ":"G","Ǵ":"G","Ĝ":"G","Ḡ":"G","Ğ":"G","Ġ":"G","Ǧ":"G","Ģ":"G","Ǥ":"G","Ɠ":"G","Ꞡ":"G","Ᵹ":"G","Ꝿ":"G","Ⓗ":"H","Ｈ":"H","Ĥ":"H","Ḣ":"H","Ḧ":"H","Ȟ":"H","Ḥ":"H","Ḩ":"H","Ḫ":"H","Ħ":"H","Ⱨ":"H","Ⱶ":"H","Ɥ":"H","Ⓘ":"I","Ｉ":"I","Ì":"I","Í":"I","Î":"I","Ĩ":"I","Ī":"I","Ĭ":"I","İ":"I","Ï":"I","Ḯ":"I","Ỉ":"I","Ǐ":"I","Ȉ":"I","Ȋ":"I","Ị":"I","Į":"I","Ḭ":"I","Ɨ":"I","Ⓙ":"J","Ｊ":"J","Ĵ":"J","Ɉ":"J","Ⓚ":"K","Ｋ":"K","Ḱ":"K","Ǩ":"K","Ḳ":"K","Ķ":"K","Ḵ":"K","Ƙ":"K","Ⱪ":"K","Ꝁ":"K","Ꝃ":"K","Ꝅ":"K","Ꞣ":"K","Ⓛ":"L","Ｌ":"L","Ŀ":"L","Ĺ":"L","Ľ":"L","Ḷ":"L","Ḹ":"L","Ļ":"L","Ḽ":"L","Ḻ":"L","Ł":"L","Ƚ":"L","Ɫ":"L","Ⱡ":"L","Ꝉ":"L","Ꝇ":"L","Ꞁ":"L","Ǉ":"LJ","ǈ":"Lj","Ⓜ":"M","Ｍ":"M","Ḿ":"M","Ṁ":"M","Ṃ":"M","Ɱ":"M","Ɯ":"M","Ⓝ":"N","Ｎ":"N","Ǹ":"N","Ń":"N","Ñ":"N","Ṅ":"N","Ň":"N","Ṇ":"N","Ņ":"N","Ṋ":"N","Ṉ":"N","Ƞ":"N","Ɲ":"N","Ꞑ":"N","Ꞥ":"N","Ǌ":"NJ","ǋ":"Nj","Ⓞ":"O","Ｏ":"O","Ò":"O","Ó":"O","Ô":"O","Ồ":"O","Ố":"O","Ỗ":"O","Ổ":"O","Õ":"O","Ṍ":"O","Ȭ":"O","Ṏ":"O","Ō":"O","Ṑ":"O","Ṓ":"O","Ŏ":"O","Ȯ":"O","Ȱ":"O","Ö":"O","Ȫ":"O","Ỏ":"O","Ő":"O","Ǒ":"O","Ȍ":"O","Ȏ":"O","Ơ":"O","Ờ":"O","Ớ":"O","Ỡ":"O","Ở":"O","Ợ":"O","Ọ":"O","Ộ":"O","Ǫ":"O","Ǭ":"O","Ø":"O","Ǿ":"O","Ɔ":"O","Ɵ":"O","Ꝋ":"O","Ꝍ":"O","Ƣ":"OI","Ꝏ":"OO","Ȣ":"OU","Ⓟ":"P","Ｐ":"P","Ṕ":"P","Ṗ":"P","Ƥ":"P","Ᵽ":"P","Ꝑ":"P","Ꝓ":"P","Ꝕ":"P","Ⓠ":"Q","Ｑ":"Q","Ꝗ":"Q","Ꝙ":"Q","Ɋ":"Q","Ⓡ":"R","Ｒ":"R","Ŕ":"R","Ṙ":"R","Ř":"R","Ȑ":"R","Ȓ":"R","Ṛ":"R","Ṝ":"R","Ŗ":"R","Ṟ":"R","Ɍ":"R","Ɽ":"R","Ꝛ":"R","Ꞧ":"R","Ꞃ":"R","Ⓢ":"S","Ｓ":"S","ẞ":"S","Ś":"S","Ṥ":"S","Ŝ":"S","Ṡ":"S","Š":"S","Ṧ":"S","Ṣ":"S","Ṩ":"S","Ș":"S","Ş":"S","Ȿ":"S","Ꞩ":"S","Ꞅ":"S","Ⓣ":"T","Ｔ":"T","Ṫ":"T","Ť":"T","Ṭ":"T","Ț":"T","Ţ":"T","Ṱ":"T","Ṯ":"T","Ŧ":"T","Ƭ":"T","Ʈ":"T","Ⱦ":"T","Ꞇ":"T","Ꜩ":"TZ","Ⓤ":"U","Ｕ":"U","Ù":"U","Ú":"U","Û":"U","Ũ":"U","Ṹ":"U","Ū":"U","Ṻ":"U","Ŭ":"U","Ü":"U","Ǜ":"U","Ǘ":"U","Ǖ":"U","Ǚ":"U","Ủ":"U","Ů":"U","Ű":"U","Ǔ":"U","Ȕ":"U","Ȗ":"U","Ư":"U","Ừ":"U","Ứ":"U","Ữ":"U","Ử":"U","Ự":"U","Ụ":"U","Ṳ":"U","Ų":"U","Ṷ":"U","Ṵ":"U","Ʉ":"U","Ⓥ":"V","Ｖ":"V","Ṽ":"V","Ṿ":"V","Ʋ":"V","Ꝟ":"V","Ʌ":"V","Ꝡ":"VY","Ⓦ":"W","Ｗ":"W","Ẁ":"W","Ẃ":"W","Ŵ":"W","Ẇ":"W","Ẅ":"W","Ẉ":"W","Ⱳ":"W","Ⓧ":"X","Ｘ":"X","Ẋ":"X","Ẍ":"X","Ⓨ":"Y","Ｙ":"Y","Ỳ":"Y","Ý":"Y","Ŷ":"Y","Ỹ":"Y","Ȳ":"Y","Ẏ":"Y","Ÿ":"Y","Ỷ":"Y","Ỵ":"Y","Ƴ":"Y","Ɏ":"Y","Ỿ":"Y","Ⓩ":"Z","Ｚ":"Z","Ź":"Z","Ẑ":"Z","Ż":"Z","Ž":"Z","Ẓ":"Z","Ẕ":"Z","Ƶ":"Z","Ȥ":"Z","Ɀ":"Z","Ⱬ":"Z","Ꝣ":"Z","ⓐ":"a","ａ":"a","ẚ":"a","à":"a","á":"a","â":"a","ầ":"a","ấ":"a","ẫ":"a","ẩ":"a","ã":"a","ā":"a","ă":"a","ằ":"a","ắ":"a","ẵ":"a","ẳ":"a","ȧ":"a","ǡ":"a","ä":"a","ǟ":"a","ả":"a","å":"a","ǻ":"a","ǎ":"a","ȁ":"a","ȃ":"a","ạ":"a","ậ":"a","ặ":"a","ḁ":"a","ą":"a","ⱥ":"a","ɐ":"a","ꜳ":"aa","æ":"ae","ǽ":"ae","ǣ":"ae","ꜵ":"ao","ꜷ":"au","ꜹ":"av","ꜻ":"av","ꜽ":"ay","ⓑ":"b","ｂ":"b","ḃ":"b","ḅ":"b","ḇ":"b","ƀ":"b","ƃ":"b","ɓ":"b","ⓒ":"c","ｃ":"c","ć":"c","ĉ":"c","ċ":"c","č":"c","ç":"c","ḉ":"c","ƈ":"c","ȼ":"c","ꜿ":"c","ↄ":"c","ⓓ":"d","ｄ":"d","ḋ":"d","ď":"d","ḍ":"d","ḑ":"d","ḓ":"d","ḏ":"d","đ":"d","ƌ":"d","ɖ":"d","ɗ":"d","ꝺ":"d","ǳ":"dz","ǆ":"dz","ⓔ":"e","ｅ":"e","è":"e","é":"e","ê":"e","ề":"e","ế":"e","ễ":"e","ể":"e","ẽ":"e","ē":"e","ḕ":"e","ḗ":"e","ĕ":"e","ė":"e","ë":"e","ẻ":"e","ě":"e","ȅ":"e","ȇ":"e","ẹ":"e","ệ":"e","ȩ":"e","ḝ":"e","ę":"e","ḙ":"e","ḛ":"e","ɇ":"e","ɛ":"e","ǝ":"e","ⓕ":"f","ｆ":"f","ḟ":"f","ƒ":"f","ꝼ":"f","ⓖ":"g","ｇ":"g","ǵ":"g","ĝ":"g","ḡ":"g","ğ":"g","ġ":"g","ǧ":"g","ģ":"g","ǥ":"g","ɠ":"g","ꞡ":"g","ᵹ":"g","ꝿ":"g","ⓗ":"h","ｈ":"h","ĥ":"h","ḣ":"h","ḧ":"h","ȟ":"h","ḥ":"h","ḩ":"h","ḫ":"h","ẖ":"h","ħ":"h","ⱨ":"h","ⱶ":"h","ɥ":"h","ƕ":"hv","ⓘ":"i","ｉ":"i","ì":"i","í":"i","î":"i","ĩ":"i","ī":"i","ĭ":"i","ï":"i","ḯ":"i","ỉ":"i","ǐ":"i","ȉ":"i","ȋ":"i","ị":"i","į":"i","ḭ":"i","ɨ":"i","ı":"i","ⓙ":"j","ｊ":"j","ĵ":"j","ǰ":"j","ɉ":"j","ⓚ":"k","ｋ":"k","ḱ":"k","ǩ":"k","ḳ":"k","ķ":"k","ḵ":"k","ƙ":"k","ⱪ":"k","ꝁ":"k","ꝃ":"k","ꝅ":"k","ꞣ":"k","ⓛ":"l","ｌ":"l","ŀ":"l","ĺ":"l","ľ":"l","ḷ":"l","ḹ":"l","ļ":"l","ḽ":"l","ḻ":"l","ſ":"l","ł":"l","ƚ":"l","ɫ":"l","ⱡ":"l","ꝉ":"l","ꞁ":"l","ꝇ":"l","ǉ":"lj","ⓜ":"m","ｍ":"m","ḿ":"m","ṁ":"m","ṃ":"m","ɱ":"m","ɯ":"m","ⓝ":"n","ｎ":"n","ǹ":"n","ń":"n","ñ":"n","ṅ":"n","ň":"n","ṇ":"n","ņ":"n","ṋ":"n","ṉ":"n","ƞ":"n","ɲ":"n","ŉ":"n","ꞑ":"n","ꞥ":"n","ǌ":"nj","ⓞ":"o","ｏ":"o","ò":"o","ó":"o","ô":"o","ồ":"o","ố":"o","ỗ":"o","ổ":"o","õ":"o","ṍ":"o","ȭ":"o","ṏ":"o","ō":"o","ṑ":"o","ṓ":"o","ŏ":"o","ȯ":"o","ȱ":"o","ö":"o","ȫ":"o","ỏ":"o","ő":"o","ǒ":"o","ȍ":"o","ȏ":"o","ơ":"o","ờ":"o","ớ":"o","ỡ":"o","ở":"o","ợ":"o","ọ":"o","ộ":"o","ǫ":"o","ǭ":"o","ø":"o","ǿ":"o","ɔ":"o","ꝋ":"o","ꝍ":"o","ɵ":"o","ƣ":"oi","ȣ":"ou","ꝏ":"oo","ⓟ":"p","ｐ":"p","ṕ":"p","ṗ":"p","ƥ":"p","ᵽ":"p","ꝑ":"p","ꝓ":"p","ꝕ":"p","ⓠ":"q","ｑ":"q","ɋ":"q","ꝗ":"q","ꝙ":"q","ⓡ":"r","ｒ":"r","ŕ":"r","ṙ":"r","ř":"r","ȑ":"r","ȓ":"r","ṛ":"r","ṝ":"r","ŗ":"r","ṟ":"r","ɍ":"r","ɽ":"r","ꝛ":"r","ꞧ":"r","ꞃ":"r","ⓢ":"s","ｓ":"s","ß":"s","ś":"s","ṥ":"s","ŝ":"s","ṡ":"s","š":"s","ṧ":"s","ṣ":"s","ṩ":"s","ș":"s","ş":"s","ȿ":"s","ꞩ":"s","ꞅ":"s","ẛ":"s","ⓣ":"t","ｔ":"t","ṫ":"t","ẗ":"t","ť":"t","ṭ":"t","ț":"t","ţ":"t","ṱ":"t","ṯ":"t","ŧ":"t","ƭ":"t","ʈ":"t","ⱦ":"t","ꞇ":"t","ꜩ":"tz","ⓤ":"u","ｕ":"u","ù":"u","ú":"u","û":"u","ũ":"u","ṹ":"u","ū":"u","ṻ":"u","ŭ":"u","ü":"u","ǜ":"u","ǘ":"u","ǖ":"u","ǚ":"u","ủ":"u","ů":"u","ű":"u","ǔ":"u","ȕ":"u","ȗ":"u","ư":"u","ừ":"u","ứ":"u","ữ":"u","ử":"u","ự":"u","ụ":"u","ṳ":"u","ų":"u","ṷ":"u","ṵ":"u","ʉ":"u","ⓥ":"v","ｖ":"v","ṽ":"v","ṿ":"v","ʋ":"v","ꝟ":"v","ʌ":"v","ꝡ":"vy","ⓦ":"w","ｗ":"w","ẁ":"w","ẃ":"w","ŵ":"w","ẇ":"w","ẅ":"w","ẘ":"w","ẉ":"w","ⱳ":"w","ⓧ":"x","ｘ":"x","ẋ":"x","ẍ":"x","ⓨ":"y","ｙ":"y","ỳ":"y","ý":"y","ŷ":"y","ỹ":"y","ȳ":"y","ẏ":"y","ÿ":"y","ỷ":"y","ẙ":"y","ỵ":"y","ƴ":"y","ɏ":"y","ỿ":"y","ⓩ":"z","ｚ":"z","ź":"z","ẑ":"z","ż":"z","ž":"z","ẓ":"z","ẕ":"z","ƶ":"z","ȥ":"z","ɀ":"z","ⱬ":"z","ꝣ":"z","Ά":"Α","Έ":"Ε","Ή":"Η","Ί":"Ι","Ϊ":"Ι","Ό":"Ο","Ύ":"Υ","Ϋ":"Υ","Ώ":"Ω","ά":"α","έ":"ε","ή":"η","ί":"ι","ϊ":"ι","ΐ":"ι","ό":"ο","ύ":"υ","ϋ":"υ","ΰ":"υ","ω":"ω","ς":"σ"};return a}),b.define("select2/data/base",["../utils"],function(a){function b(a,c){b.__super__.constructor.call(this)}return a.Extend(b,a.Observable),b.prototype.current=function(a){throw new Error("The `current` method must be defined in child classes.")},b.prototype.query=function(a,b){throw new Error("The `query` method must be defined in child classes.")},b.prototype.bind=function(a,b){},b.prototype.destroy=function(){},b.prototype.generateResultId=function(b,c){var d=b.id+"-result-";return d+=a.generateChars(4),d+=null!=c.id?"-"+c.id.toString():"-"+a.generateChars(4)},b}),b.define("select2/data/select",["./base","../utils","jquery"],function(a,b,c){function d(a,b){this.$element=a,this.options=b,d.__super__.constructor.call(this)}return b.Extend(d,a),d.prototype.current=function(a){var b=[],d=this;this.$element.find(":selected").each(function(){var a=c(this),e=d.item(a);b.push(e)}),a(b)},d.prototype.select=function(a){var b=this;if(a.selected=!0,c(a.element).is("option"))return a.element.selected=!0,void this.$element.trigger("change");if(this.$element.prop("multiple"))this.current(function(d){var e=[];a=[a],a.push.apply(a,d);for(var f=0;f<a.length;f++){var g=a[f].id;-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")});else{var d=a.id;this.$element.val(d),this.$element.trigger("change")}},d.prototype.unselect=function(a){var b=this;if(this.$element.prop("multiple"))return a.selected=!1,c(a.element).is("option")?(a.element.selected=!1,void this.$element.trigger("change")):void this.current(function(d){for(var e=[],f=0;f<d.length;f++){var g=d[f].id;g!==a.id&&-1===c.inArray(g,e)&&e.push(g)}b.$element.val(e),b.$element.trigger("change")})},d.prototype.bind=function(a,b){var c=this;this.container=a,a.on("select",function(a){c.select(a.data)}),a.on("unselect",function(a){c.unselect(a.data)})},d.prototype.destroy=function(){this.$element.find("*").each(function(){c.removeData(this,"data")})},d.prototype.query=function(a,b){var d=[],e=this,f=this.$element.children();f.each(function(){var b=c(this);if(b.is("option")||b.is("optgroup")){var f=e.item(b),g=e.matches(a,f);null!==g&&d.push(g)}}),b({results:d})},d.prototype.addOptions=function(a){b.appendMany(this.$element,a)},d.prototype.option=function(a){var b;a.children?(b=document.createElement("optgroup"),b.label=a.text):(b=document.createElement("option"),void 0!==b.textContent?b.textContent=a.text:b.innerText=a.text),a.id&&(b.value=a.id),a.disabled&&(b.disabled=!0),a.selected&&(b.selected=!0),a.title&&(b.title=a.title);var d=c(b),e=this._normalizeItem(a);return e.element=b,c.data(b,"data",e),d},d.prototype.item=function(a){var b={};if(b=c.data(a[0],"data"),null!=b)return b;if(a.is("option"))b={id:a.val(),text:a.text(),disabled:a.prop("disabled"),selected:a.prop("selected"),title:a.prop("title")};else if(a.is("optgroup")){b={text:a.prop("label"),children:[],title:a.prop("title")};for(var d=a.children("option"),e=[],f=0;f<d.length;f++){var g=c(d[f]),h=this.item(g);e.push(h)}b.children=e}return b=this._normalizeItem(b),b.element=a[0],c.data(a[0],"data",b),b},d.prototype._normalizeItem=function(a){c.isPlainObject(a)||(a={id:a,text:a}),a=c.extend({},{text:""},a);var b={selected:!1,disabled:!1};return null!=a.id&&(a.id=a.id.toString()),null!=a.text&&(a.text=a.text.toString()),null==a._resultId&&a.id&&null!=this.container&&(a._resultId=this.generateResultId(this.container,a)),c.extend({},b,a)},d.prototype.matches=function(a,b){var c=this.options.get("matcher");return c(a,b)},d}),b.define("select2/data/array",["./select","../utils","jquery"],function(a,b,c){function d(a,b){var c=b.get("data")||[];d.__super__.constructor.call(this,a,b),this.addOptions(this.convertToOptions(c))}return b.Extend(d,a),d.prototype.select=function(a){var b=this.$element.find("option").filter(function(b,c){return c.value==a.id.toString()});0===b.length&&(b=this.option(a),this.addOptions(b)),d.__super__.select.call(this,a)},d.prototype.convertToOptions=function(a){function d(a){return function(){return c(this).val()==a.id}}for(var e=this,f=this.$element.find("option"),g=f.map(function(){return e.item(c(this)).id}).get(),h=[],i=0;i<a.length;i++){var j=this._normalizeItem(a[i]);if(c.inArray(j.id,g)>=0){var k=f.filter(d(j)),l=this.item(k),m=c.extend(!0,{},j,l),n=this.option(m);k.replaceWith(n)}else{var o=this.option(j);if(j.children){var p=this.convertToOptions(j.children);b.appendMany(o,p)}h.push(o)}}return h},d}),b.define("select2/data/ajax",["./array","../utils","jquery"],function(a,b,c){function d(a,b){this.ajaxOptions=this._applyDefaults(b.get("ajax")),null!=this.ajaxOptions.processResults&&(this.processResults=this.ajaxOptions.processResults),d.__super__.constructor.call(this,a,b)}return b.Extend(d,a),d.prototype._applyDefaults=function(a){var b={data:function(a){return c.extend({},a,{q:a.term})},transport:function(a,b,d){var e=c.ajax(a);return e.then(b),e.fail(d),e}};return c.extend({},b,a,!0)},d.prototype.processResults=function(a){return a},d.prototype.query=function(a,b){function d(){var d=f.transport(f,function(d){var f=e.processResults(d,a);e.options.get("debug")&&window.console&&console.error&&(f&&f.results&&c.isArray(f.results)||console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")),b(f)},function(){d.status&&"0"===d.status||e.trigger("results:message",{message:"errorLoading"})});e._request=d}var e=this;null!=this._request&&(c.isFunction(this._request.abort)&&this._request.abort(),this._request=null);var f=c.extend({type:"GET"},this.ajaxOptions);"function"==typeof f.url&&(f.url=f.url.call(this.$element,a)),"function"==typeof f.data&&(f.data=f.data.call(this.$element,a)),this.ajaxOptions.delay&&null!=a.term?(this._queryTimeout&&window.clearTimeout(this._queryTimeout),this._queryTimeout=window.setTimeout(d,this.ajaxOptions.delay)):d()},d}),b.define("select2/data/tags",["jquery"],function(a){function b(b,c,d){var e=d.get("tags"),f=d.get("createTag");void 0!==f&&(this.createTag=f);var g=d.get("insertTag");if(void 0!==g&&(this.insertTag=g),b.call(this,c,d),a.isArray(e))for(var h=0;h<e.length;h++){var i=e[h],j=this._normalizeItem(i),k=this.option(j);this.$element.append(k)}}return b.prototype.query=function(a,b,c){function d(a,f){for(var g=a.results,h=0;h<g.length;h++){var i=g[h],j=null!=i.children&&!d({results:i.children},!0),k=i.text===b.term;if(k||j)return!f&&(a.data=g,void c(a))}if(f)return!0;var l=e.createTag(b);if(null!=l){var m=e.option(l);m.attr("data-select2-tag",!0),e.addOptions([m]),e.insertTag(g,l)}a.results=g,c(a)}var e=this;return this._removeOldTags(),null==b.term||null!=b.page?void a.call(this,b,c):void a.call(this,b,d)},b.prototype.createTag=function(b,c){var d=a.trim(c.term);return""===d?null:{id:d,text:d}},b.prototype.insertTag=function(a,b,c){b.unshift(c)},b.prototype._removeOldTags=function(b){var c=(this._lastTag,this.$element.find("option[data-select2-tag]"));c.each(function(){this.selected||a(this).remove()})},b}),b.define("select2/data/tokenizer",["jquery"],function(a){function b(a,b,c){var d=c.get("tokenizer");void 0!==d&&(this.tokenizer=d),a.call(this,b,c)}return b.prototype.bind=function(a,b,c){a.call(this,b,c),this.$search=b.dropdown.$search||b.selection.$search||c.find(".select2-search__field")},b.prototype.query=function(b,c,d){function e(b){var c=g._normalizeItem(b),d=g.$element.find("option").filter(function(){return a(this).val()===c.id});if(!d.length){var e=g.option(c);e.attr("data-select2-tag",!0),g._removeOldTags(),g.addOptions([e])}f(c)}function f(a){g.trigger("select",{data:a})}var g=this;c.term=c.term||"";var h=this.tokenizer(c,this.options,e);h.term!==c.term&&(this.$search.length&&(this.$search.val(h.term),this.$search.focus()),c.term=h.term),b.call(this,c,d)},b.prototype.tokenizer=function(b,c,d,e){for(var f=d.get("tokenSeparators")||[],g=c.term,h=0,i=this.createTag||function(a){return{id:a.term,text:a.term}};h<g.length;){var j=g[h];if(-1!==a.inArray(j,f)){var k=g.substr(0,h),l=a.extend({},c,{term:k}),m=i(l);null!=m?(e(m),g=g.substr(h+1)||"",h=0):h++}else h++}return{term:g}},b}),b.define("select2/data/minimumInputLength",[],function(){function a(a,b,c){this.minimumInputLength=c.get("minimumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",b.term.length<this.minimumInputLength?void this.trigger("results:message",{message:"inputTooShort",args:{minimum:this.minimumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),b.define("select2/data/maximumInputLength",[],function(){function a(a,b,c){this.maximumInputLength=c.get("maximumInputLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){return b.term=b.term||"",this.maximumInputLength>0&&b.term.length>this.maximumInputLength?void this.trigger("results:message",{message:"inputTooLong",args:{maximum:this.maximumInputLength,input:b.term,params:b}}):void a.call(this,b,c)},a}),b.define("select2/data/maximumSelectionLength",[],function(){function a(a,b,c){this.maximumSelectionLength=c.get("maximumSelectionLength"),a.call(this,b,c)}return a.prototype.query=function(a,b,c){var d=this;this.current(function(e){var f=null!=e?e.length:0;return d.maximumSelectionLength>0&&f>=d.maximumSelectionLength?void d.trigger("results:message",{message:"maximumSelected",args:{maximum:d.maximumSelectionLength}}):void a.call(d,b,c)})},a}),b.define("select2/dropdown",["jquery","./utils"],function(a,b){function c(a,b){this.$element=a,this.options=b,c.__super__.constructor.call(this)}return b.Extend(c,b.Observable),c.prototype.render=function(){var b=a('<span class="select2-dropdown"><span class="select2-results"></span></span>');return b.attr("dir",this.options.get("dir")),this.$dropdown=b,b},c.prototype.bind=function(){},c.prototype.position=function(a,b){},c.prototype.destroy=function(){this.$dropdown.remove()},c}),b.define("select2/dropdown/search",["jquery","../utils"],function(a,b){function c(){}return c.prototype.render=function(b){var c=b.call(this),d=a('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></span>');return this.$searchContainer=d,this.$search=d.find("input"),c.prepend(d),c},c.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),this.$search.on("keydown",function(a){e.trigger("keypress",a),e._keyUpPrevented=a.isDefaultPrevented()}),this.$search.on("input",function(b){a(this).off("keyup")}),this.$search.on("keyup input",function(a){e.handleSearch(a)}),c.on("open",function(){e.$search.attr("tabindex",0),e.$search.focus(),window.setTimeout(function(){e.$search.focus()},0)}),c.on("close",function(){e.$search.attr("tabindex",-1),e.$search.val("")}),c.on("focus",function(){c.isOpen()&&e.$search.focus()}),c.on("results:all",function(a){if(null==a.query.term||""===a.query.term){var b=e.showSearch(a);b?e.$searchContainer.removeClass("select2-search--hide"):e.$searchContainer.addClass("select2-search--hide")}})},c.prototype.handleSearch=function(a){if(!this._keyUpPrevented){var b=this.$search.val();this.trigger("query",{term:b})}this._keyUpPrevented=!1},c.prototype.showSearch=function(a,b){return!0},c}),b.define("select2/dropdown/hidePlaceholder",[],function(){function a(a,b,c,d){this.placeholder=this.normalizePlaceholder(c.get("placeholder")),a.call(this,b,c,d)}return a.prototype.append=function(a,b){b.results=this.removePlaceholder(b.results),a.call(this,b)},a.prototype.normalizePlaceholder=function(a,b){return"string"==typeof b&&(b={id:"",text:b}),b},a.prototype.removePlaceholder=function(a,b){for(var c=b.slice(0),d=b.length-1;d>=0;d--){var e=b[d];this.placeholder.id===e.id&&c.splice(d,1)}return c},a}),b.define("select2/dropdown/infiniteScroll",["jquery"],function(a){function b(a,b,c,d){this.lastParams={},a.call(this,b,c,d),this.$loadingMore=this.createLoadingMore(),this.loading=!1}return b.prototype.append=function(a,b){this.$loadingMore.remove(),this.loading=!1,a.call(this,b),this.showLoadingMore(b)&&this.$results.append(this.$loadingMore)},b.prototype.bind=function(b,c,d){var e=this;b.call(this,c,d),c.on("query",function(a){e.lastParams=a,e.loading=!0}),c.on("query:append",function(a){e.lastParams=a,e.loading=!0}),this.$results.on("scroll",function(){var b=a.contains(document.documentElement,e.$loadingMore[0]);if(!e.loading&&b){var c=e.$results.offset().top+e.$results.outerHeight(!1),d=e.$loadingMore.offset().top+e.$loadingMore.outerHeight(!1);c+50>=d&&e.loadMore()}})},b.prototype.loadMore=function(){this.loading=!0;var b=a.extend({},{page:1},this.lastParams);b.page++,this.trigger("query:append",b)},b.prototype.showLoadingMore=function(a,b){return b.pagination&&b.pagination.more},b.prototype.createLoadingMore=function(){var b=a('<li class="select2-results__option select2-results__option--load-more"role="treeitem" aria-disabled="true"></li>'),c=this.options.get("translations").get("loadingMore");return b.html(c(this.lastParams)),b},b}),b.define("select2/dropdown/attachBody",["jquery","../utils"],function(a,b){function c(b,c,d){this.$dropdownParent=d.get("dropdownParent")||a(document.body),b.call(this,c,d)}return c.prototype.bind=function(a,b,c){var d=this,e=!1;a.call(this,b,c),b.on("open",function(){d._showDropdown(),d._attachPositioningHandler(b),e||(e=!0,b.on("results:all",function(){d._positionDropdown(),d._resizeDropdown()}),b.on("results:append",function(){d._positionDropdown(),d._resizeDropdown()}))}),b.on("close",function(){d._hideDropdown(),d._detachPositioningHandler(b)}),this.$dropdownContainer.on("mousedown",function(a){a.stopPropagation()})},c.prototype.destroy=function(a){a.call(this),this.$dropdownContainer.remove()},c.prototype.position=function(a,b,c){b.attr("class",c.attr("class")),b.removeClass("select2"),b.addClass("select2-container--open"),b.css({position:"absolute",top:-999999}),this.$container=c},c.prototype.render=function(b){var c=a("<span></span>"),d=b.call(this);return c.append(d),this.$dropdownContainer=c,c},c.prototype._hideDropdown=function(a){this.$dropdownContainer.detach()},c.prototype._attachPositioningHandler=function(c,d){var e=this,f="scroll.select2."+d.id,g="resize.select2."+d.id,h="orientationchange.select2."+d.id,i=this.$container.parents().filter(b.hasScroll);i.each(function(){a(this).data("select2-scroll-position",{x:a(this).scrollLeft(),y:a(this).scrollTop()})}),i.on(f,function(b){var c=a(this).data("select2-scroll-position");a(this).scrollTop(c.y)}),a(window).on(f+" "+g+" "+h,function(a){e._positionDropdown(),e._resizeDropdown()})},c.prototype._detachPositioningHandler=function(c,d){var e="scroll.select2."+d.id,f="resize.select2."+d.id,g="orientationchange.select2."+d.id,h=this.$container.parents().filter(b.hasScroll);h.off(e),a(window).off(e+" "+f+" "+g)},c.prototype._positionDropdown=function(){var b=a(window),c=this.$dropdown.hasClass("select2-dropdown--above"),d=this.$dropdown.hasClass("select2-dropdown--below"),e=null,f=this.$container.offset();f.bottom=f.top+this.$container.outerHeight(!1);var g={height:this.$container.outerHeight(!1)};g.top=f.top,g.bottom=f.top+g.height;var h={height:this.$dropdown.outerHeight(!1)},i={top:b.scrollTop(),bottom:b.scrollTop()+b.height()},j=i.top<f.top-h.height,k=i.bottom>f.bottom+h.height,l={left:f.left,top:g.bottom},m=this.$dropdownParent;"static"===m.css("position")&&(m=m.offsetParent());var n=m.offset();l.top-=n.top,l.left-=n.left,c||d||(e="below"),k||!j||c?!j&&k&&c&&(e="below"):e="above",("above"==e||c&&"below"!==e)&&(l.top=g.top-n.top-h.height),null!=e&&(this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--"+e),this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--"+e)),this.$dropdownContainer.css(l)},c.prototype._resizeDropdown=function(){var a={width:this.$container.outerWidth(!1)+"px"};this.options.get("dropdownAutoWidth")&&(a.minWidth=a.width,a.position="relative",a.width="auto"),this.$dropdown.css(a)},c.prototype._showDropdown=function(a){this.$dropdownContainer.appendTo(this.$dropdownParent),this._positionDropdown(),this._resizeDropdown()},c}),b.define("select2/dropdown/minimumResultsForSearch",[],function(){function a(b){for(var c=0,d=0;d<b.length;d++){var e=b[d];e.children?c+=a(e.children):c++}return c}function b(a,b,c,d){this.minimumResultsForSearch=c.get("minimumResultsForSearch"),this.minimumResultsForSearch<0&&(this.minimumResultsForSearch=1/0),a.call(this,b,c,d)}return b.prototype.showSearch=function(b,c){return!(a(c.data.results)<this.minimumResultsForSearch)&&b.call(this,c)},b}),b.define("select2/dropdown/selectOnClose",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("close",function(a){d._handleSelectOnClose(a)})},a.prototype._handleSelectOnClose=function(a,b){if(b&&null!=b.originalSelect2Event){var c=b.originalSelect2Event;if("select"===c._type||"unselect"===c._type)return}var d=this.getHighlightedResults();if(!(d.length<1)){var e=d.data("data");null!=e.element&&e.element.selected||null==e.element&&e.selected||this.trigger("select",{data:e})}},a}),b.define("select2/dropdown/closeOnSelect",[],function(){function a(){}return a.prototype.bind=function(a,b,c){var d=this;a.call(this,b,c),b.on("select",function(a){d._selectTriggered(a)}),b.on("unselect",function(a){d._selectTriggered(a)})},a.prototype._selectTriggered=function(a,b){var c=b.originalEvent;c&&c.ctrlKey||this.trigger("close",{originalEvent:c,originalSelect2Event:b})},a}),b.define("select2/i18n/en",[],function(){return{errorLoading:function(){return"The results could not be loaded."},inputTooLong:function(a){var b=a.input.length-a.maximum,c="Please delete "+b+" character";return 1!=b&&(c+="s"),c},inputTooShort:function(a){var b=a.minimum-a.input.length,c="Please enter "+b+" or more characters";return c},loadingMore:function(){return"Loading more results…"},maximumSelected:function(a){var b="You can only select "+a.maximum+" item";
return 1!=a.maximum&&(b+="s"),b},noResults:function(){return"No results found"},searching:function(){return"Searching…"}}}),b.define("select2/defaults",["jquery","require","./results","./selection/single","./selection/multiple","./selection/placeholder","./selection/allowClear","./selection/search","./selection/eventRelay","./utils","./translation","./diacritics","./data/select","./data/array","./data/ajax","./data/tags","./data/tokenizer","./data/minimumInputLength","./data/maximumInputLength","./data/maximumSelectionLength","./dropdown","./dropdown/search","./dropdown/hidePlaceholder","./dropdown/infiniteScroll","./dropdown/attachBody","./dropdown/minimumResultsForSearch","./dropdown/selectOnClose","./dropdown/closeOnSelect","./i18n/en"],function(a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C){function D(){this.reset()}D.prototype.apply=function(l){if(l=a.extend(!0,{},this.defaults,l),null==l.dataAdapter){if(null!=l.ajax?l.dataAdapter=o:null!=l.data?l.dataAdapter=n:l.dataAdapter=m,l.minimumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,r)),l.maximumInputLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,s)),l.maximumSelectionLength>0&&(l.dataAdapter=j.Decorate(l.dataAdapter,t)),l.tags&&(l.dataAdapter=j.Decorate(l.dataAdapter,p)),(null!=l.tokenSeparators||null!=l.tokenizer)&&(l.dataAdapter=j.Decorate(l.dataAdapter,q)),null!=l.query){var C=b(l.amdBase+"compat/query");l.dataAdapter=j.Decorate(l.dataAdapter,C)}if(null!=l.initSelection){var D=b(l.amdBase+"compat/initSelection");l.dataAdapter=j.Decorate(l.dataAdapter,D)}}if(null==l.resultsAdapter&&(l.resultsAdapter=c,null!=l.ajax&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,x)),null!=l.placeholder&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,w)),l.selectOnClose&&(l.resultsAdapter=j.Decorate(l.resultsAdapter,A))),null==l.dropdownAdapter){if(l.multiple)l.dropdownAdapter=u;else{var E=j.Decorate(u,v);l.dropdownAdapter=E}if(0!==l.minimumResultsForSearch&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,z)),l.closeOnSelect&&(l.dropdownAdapter=j.Decorate(l.dropdownAdapter,B)),null!=l.dropdownCssClass||null!=l.dropdownCss||null!=l.adaptDropdownCssClass){var F=b(l.amdBase+"compat/dropdownCss");l.dropdownAdapter=j.Decorate(l.dropdownAdapter,F)}l.dropdownAdapter=j.Decorate(l.dropdownAdapter,y)}if(null==l.selectionAdapter){if(l.multiple?l.selectionAdapter=e:l.selectionAdapter=d,null!=l.placeholder&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,f)),l.allowClear&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,g)),l.multiple&&(l.selectionAdapter=j.Decorate(l.selectionAdapter,h)),null!=l.containerCssClass||null!=l.containerCss||null!=l.adaptContainerCssClass){var G=b(l.amdBase+"compat/containerCss");l.selectionAdapter=j.Decorate(l.selectionAdapter,G)}l.selectionAdapter=j.Decorate(l.selectionAdapter,i)}if("string"==typeof l.language)if(l.language.indexOf("-")>0){var H=l.language.split("-"),I=H[0];l.language=[l.language,I]}else l.language=[l.language];if(a.isArray(l.language)){var J=new k;l.language.push("en");for(var K=l.language,L=0;L<K.length;L++){var M=K[L],N={};try{N=k.loadPath(M)}catch(O){try{M=this.defaults.amdLanguageBase+M,N=k.loadPath(M)}catch(P){l.debug&&window.console&&console.warn&&console.warn('Select2: The language file for "'+M+'" could not be automatically loaded. A fallback will be used instead.');continue}}J.extend(N)}l.translations=J}else{var Q=k.loadPath(this.defaults.amdLanguageBase+"en"),R=new k(l.language);R.extend(Q),l.translations=R}return l},D.prototype.reset=function(){function b(a){function b(a){return l[a]||a}return a.replace(/[^\u0000-\u007E]/g,b)}function c(d,e){if(""===a.trim(d.term))return e;if(e.children&&e.children.length>0){for(var f=a.extend(!0,{},e),g=e.children.length-1;g>=0;g--){var h=e.children[g],i=c(d,h);null==i&&f.children.splice(g,1)}return f.children.length>0?f:c(d,f)}var j=b(e.text).toUpperCase(),k=b(d.term).toUpperCase();return j.indexOf(k)>-1?e:null}this.defaults={amdBase:"./",amdLanguageBase:"./i18n/",closeOnSelect:!0,debug:!1,dropdownAutoWidth:!1,escapeMarkup:j.escapeMarkup,language:C,matcher:c,minimumInputLength:0,maximumInputLength:0,maximumSelectionLength:0,minimumResultsForSearch:0,selectOnClose:!1,sorter:function(a){return a},templateResult:function(a){return a.text},templateSelection:function(a){return a.text},theme:"default",width:"resolve"}},D.prototype.set=function(b,c){var d=a.camelCase(b),e={};e[d]=c;var f=j._convertData(e);a.extend(this.defaults,f)};var E=new D;return E}),b.define("select2/options",["require","jquery","./defaults","./utils"],function(a,b,c,d){function e(b,e){if(this.options=b,null!=e&&this.fromElement(e),this.options=c.apply(this.options),e&&e.is("input")){var f=a(this.get("amdBase")+"compat/inputData");this.options.dataAdapter=d.Decorate(this.options.dataAdapter,f)}}return e.prototype.fromElement=function(a){var c=["select2"];null==this.options.multiple&&(this.options.multiple=a.prop("multiple")),null==this.options.disabled&&(this.options.disabled=a.prop("disabled")),null==this.options.language&&(a.prop("lang")?this.options.language=a.prop("lang").toLowerCase():a.closest("[lang]").prop("lang")&&(this.options.language=a.closest("[lang]").prop("lang"))),null==this.options.dir&&(a.prop("dir")?this.options.dir=a.prop("dir"):a.closest("[dir]").prop("dir")?this.options.dir=a.closest("[dir]").prop("dir"):this.options.dir="ltr"),a.prop("disabled",this.options.disabled),a.prop("multiple",this.options.multiple),a.data("select2Tags")&&(this.options.debug&&window.console&&console.warn&&console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.'),a.data("data",a.data("select2Tags")),a.data("tags",!0)),a.data("ajaxUrl")&&(this.options.debug&&window.console&&console.warn&&console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2."),a.attr("ajax--url",a.data("ajaxUrl")),a.data("ajax--url",a.data("ajaxUrl")));var e={};e=b.fn.jquery&&"1."==b.fn.jquery.substr(0,2)&&a[0].dataset?b.extend(!0,{},a[0].dataset,a.data()):a.data();var f=b.extend(!0,{},e);f=d._convertData(f);for(var g in f)b.inArray(g,c)>-1||(b.isPlainObject(this.options[g])?b.extend(this.options[g],f[g]):this.options[g]=f[g]);return this},e.prototype.get=function(a){return this.options[a]},e.prototype.set=function(a,b){this.options[a]=b},e}),b.define("select2/core",["jquery","./options","./utils","./keys"],function(a,b,c,d){var e=function(a,c){null!=a.data("select2")&&a.data("select2").destroy(),this.$element=a,this.id=this._generateId(a),c=c||{},this.options=new b(c,a),e.__super__.constructor.call(this);var d=a.attr("tabindex")||0;a.data("old-tabindex",d),a.attr("tabindex","-1");var f=this.options.get("dataAdapter");this.dataAdapter=new f(a,this.options);var g=this.render();this._placeContainer(g);var h=this.options.get("selectionAdapter");this.selection=new h(a,this.options),this.$selection=this.selection.render(),this.selection.position(this.$selection,g);var i=this.options.get("dropdownAdapter");this.dropdown=new i(a,this.options),this.$dropdown=this.dropdown.render(),this.dropdown.position(this.$dropdown,g);var j=this.options.get("resultsAdapter");this.results=new j(a,this.options,this.dataAdapter),this.$results=this.results.render(),this.results.position(this.$results,this.$dropdown);var k=this;this._bindAdapters(),this._registerDomEvents(),this._registerDataEvents(),this._registerSelectionEvents(),this._registerDropdownEvents(),this._registerResultsEvents(),this._registerEvents(),this.dataAdapter.current(function(a){k.trigger("selection:update",{data:a})}),a.addClass("select2-hidden-accessible"),a.attr("aria-hidden","true"),this._syncAttributes(),a.data("select2",this)};return c.Extend(e,c.Observable),e.prototype._generateId=function(a){var b="";return b=null!=a.attr("id")?a.attr("id"):null!=a.attr("name")?a.attr("name")+"-"+c.generateChars(2):c.generateChars(4),b=b.replace(/(:|\.|\[|\]|,)/g,""),b="select2-"+b},e.prototype._placeContainer=function(a){a.insertAfter(this.$element);var b=this._resolveWidth(this.$element,this.options.get("width"));null!=b&&a.css("width",b)},e.prototype._resolveWidth=function(a,b){var c=/^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;if("resolve"==b){var d=this._resolveWidth(a,"style");return null!=d?d:this._resolveWidth(a,"element")}if("element"==b){var e=a.outerWidth(!1);return 0>=e?"auto":e+"px"}if("style"==b){var f=a.attr("style");if("string"!=typeof f)return null;for(var g=f.split(";"),h=0,i=g.length;i>h;h+=1){var j=g[h].replace(/\s/g,""),k=j.match(c);if(null!==k&&k.length>=1)return k[1]}return null}return b},e.prototype._bindAdapters=function(){this.dataAdapter.bind(this,this.$container),this.selection.bind(this,this.$container),this.dropdown.bind(this,this.$container),this.results.bind(this,this.$container)},e.prototype._registerDomEvents=function(){var b=this;this.$element.on("change.select2",function(){b.dataAdapter.current(function(a){b.trigger("selection:update",{data:a})})}),this.$element.on("focus.select2",function(a){b.trigger("focus",a)}),this._syncA=c.bind(this._syncAttributes,this),this._syncS=c.bind(this._syncSubtree,this),this.$element[0].attachEvent&&this.$element[0].attachEvent("onpropertychange",this._syncA);var d=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;null!=d?(this._observer=new d(function(c){a.each(c,b._syncA),a.each(c,b._syncS)}),this._observer.observe(this.$element[0],{attributes:!0,childList:!0,subtree:!1})):this.$element[0].addEventListener&&(this.$element[0].addEventListener("DOMAttrModified",b._syncA,!1),this.$element[0].addEventListener("DOMNodeInserted",b._syncS,!1),this.$element[0].addEventListener("DOMNodeRemoved",b._syncS,!1))},e.prototype._registerDataEvents=function(){var a=this;this.dataAdapter.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerSelectionEvents=function(){var b=this,c=["toggle","focus"];this.selection.on("toggle",function(){b.toggleDropdown()}),this.selection.on("focus",function(a){b.focus(a)}),this.selection.on("*",function(d,e){-1===a.inArray(d,c)&&b.trigger(d,e)})},e.prototype._registerDropdownEvents=function(){var a=this;this.dropdown.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerResultsEvents=function(){var a=this;this.results.on("*",function(b,c){a.trigger(b,c)})},e.prototype._registerEvents=function(){var a=this;this.on("open",function(){a.$container.addClass("select2-container--open")}),this.on("close",function(){a.$container.removeClass("select2-container--open")}),this.on("enable",function(){a.$container.removeClass("select2-container--disabled")}),this.on("disable",function(){a.$container.addClass("select2-container--disabled")}),this.on("blur",function(){a.$container.removeClass("select2-container--focus")}),this.on("query",function(b){a.isOpen()||a.trigger("open",{}),this.dataAdapter.query(b,function(c){a.trigger("results:all",{data:c,query:b})})}),this.on("query:append",function(b){this.dataAdapter.query(b,function(c){a.trigger("results:append",{data:c,query:b})})}),this.on("keypress",function(b){var c=b.which;a.isOpen()?c===d.ESC||c===d.TAB||c===d.UP&&b.altKey?(a.close(),b.preventDefault()):c===d.ENTER?(a.trigger("results:select",{}),b.preventDefault()):c===d.SPACE&&b.ctrlKey?(a.trigger("results:toggle",{}),b.preventDefault()):c===d.UP?(a.trigger("results:previous",{}),b.preventDefault()):c===d.DOWN&&(a.trigger("results:next",{}),b.preventDefault()):(c===d.ENTER||c===d.SPACE||c===d.DOWN&&b.altKey)&&(a.open(),b.preventDefault())})},e.prototype._syncAttributes=function(){this.options.set("disabled",this.$element.prop("disabled")),this.options.get("disabled")?(this.isOpen()&&this.close(),this.trigger("disable",{})):this.trigger("enable",{})},e.prototype._syncSubtree=function(a,b){var c=!1,d=this;if(!a||!a.target||"OPTION"===a.target.nodeName||"OPTGROUP"===a.target.nodeName){if(b)if(b.addedNodes&&b.addedNodes.length>0)for(var e=0;e<b.addedNodes.length;e++){var f=b.addedNodes[e];f.selected&&(c=!0)}else b.removedNodes&&b.removedNodes.length>0&&(c=!0);else c=!0;c&&this.dataAdapter.current(function(a){d.trigger("selection:update",{data:a})})}},e.prototype.trigger=function(a,b){var c=e.__super__.trigger,d={open:"opening",close:"closing",select:"selecting",unselect:"unselecting"};if(void 0===b&&(b={}),a in d){var f=d[a],g={prevented:!1,name:a,args:b};if(c.call(this,f,g),g.prevented)return void(b.prevented=!0)}c.call(this,a,b)},e.prototype.toggleDropdown=function(){this.options.get("disabled")||(this.isOpen()?this.close():this.open())},e.prototype.open=function(){this.isOpen()||this.trigger("query",{})},e.prototype.close=function(){this.isOpen()&&this.trigger("close",{})},e.prototype.isOpen=function(){return this.$container.hasClass("select2-container--open")},e.prototype.hasFocus=function(){return this.$container.hasClass("select2-container--focus")},e.prototype.focus=function(a){this.hasFocus()||(this.$container.addClass("select2-container--focus"),this.trigger("focus",{}))},e.prototype.enable=function(a){this.options.get("debug")&&window.console&&console.warn&&console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.'),(null==a||0===a.length)&&(a=[!0]);var b=!a[0];this.$element.prop("disabled",b)},e.prototype.data=function(){this.options.get("debug")&&arguments.length>0&&window.console&&console.warn&&console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.');var a=[];return this.dataAdapter.current(function(b){a=b}),a},e.prototype.val=function(b){if(this.options.get("debug")&&window.console&&console.warn&&console.warn('Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.'),null==b||0===b.length)return this.$element.val();var c=b[0];a.isArray(c)&&(c=a.map(c,function(a){return a.toString()})),this.$element.val(c).trigger("change")},e.prototype.destroy=function(){this.$container.remove(),this.$element[0].detachEvent&&this.$element[0].detachEvent("onpropertychange",this._syncA),null!=this._observer?(this._observer.disconnect(),this._observer=null):this.$element[0].removeEventListener&&(this.$element[0].removeEventListener("DOMAttrModified",this._syncA,!1),this.$element[0].removeEventListener("DOMNodeInserted",this._syncS,!1),this.$element[0].removeEventListener("DOMNodeRemoved",this._syncS,!1)),this._syncA=null,this._syncS=null,this.$element.off(".select2"),this.$element.attr("tabindex",this.$element.data("old-tabindex")),this.$element.removeClass("select2-hidden-accessible"),this.$element.attr("aria-hidden","false"),this.$element.removeData("select2"),this.dataAdapter.destroy(),this.selection.destroy(),this.dropdown.destroy(),this.results.destroy(),this.dataAdapter=null,this.selection=null,this.dropdown=null,this.results=null},e.prototype.render=function(){var b=a('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');return b.attr("dir",this.options.get("dir")),this.$container=b,this.$container.addClass("select2-container--"+this.options.get("theme")),b.data("element",this.$element),b},e}),b.define("jquery-mousewheel",["jquery"],function(a){return a}),b.define("jquery.select2",["jquery","jquery-mousewheel","./select2/core","./select2/defaults"],function(a,b,c,d){if(null==a.fn.select2){var e=["open","close","destroy"];a.fn.select2=function(b){if(b=b||{},"object"==typeof b)return this.each(function(){var d=a.extend(!0,{},b);new c(a(this),d)}),this;if("string"==typeof b){var d,f=Array.prototype.slice.call(arguments,1);return this.each(function(){var c=a(this).data("select2");null==c&&window.console&&console.error&&console.error("The select2('"+b+"') method was called on an element that is not using Select2."),d=c[b].apply(c,f)}),a.inArray(b,e)>-1?this:d}throw new Error("Invalid arguments for Select2: "+b)}}return null==a.fn.select2.defaults&&(a.fn.select2.defaults=d),c}),{define:b.define,require:b.require}}(),c=b.require("jquery.select2");return a.fn.select2.amd=b,c}),/*! rangeslider.js - v2.3.0 | (c) 2016 @andreruffert | MIT license | https://github.com/andreruffert/rangeslider.js */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){"use strict";function b(){var a=document.createElement("input");return a.setAttribute("type","range"),"text"!==a.type}function c(a,b){var c=Array.prototype.slice.call(arguments,2);return setTimeout(function(){return a.apply(null,c)},b)}function d(a,b){return b=b||100,function(){if(!a.debouncing){var c=Array.prototype.slice.apply(arguments);a.lastReturnVal=a.apply(window,c),a.debouncing=!0}return clearTimeout(a.debounceTimeout),a.debounceTimeout=setTimeout(function(){a.debouncing=!1},b),a.lastReturnVal}}function e(a){return a&&(0===a.offsetWidth||0===a.offsetHeight||a.open===!1)}function f(a){for(var b=[],c=a.parentNode;e(c);)b.push(c),c=c.parentNode;return b}function g(a,b){function c(a){"undefined"!=typeof a.open&&(a.open=!a.open)}var d=f(a),e=d.length,g=[],h=a[b];if(e){for(var i=0;i<e;i++)g[i]=d[i].style.cssText,d[i].style.setProperty?d[i].style.setProperty("display","block","important"):d[i].style.cssText+=";display: block !important",d[i].style.height="0",d[i].style.overflow="hidden",d[i].style.visibility="hidden",c(d[i]);h=a[b];for(var j=0;j<e;j++)d[j].style.cssText=g[j],c(d[j])}return h}function h(a,b){var c=parseFloat(a);return Number.isNaN(c)?b:c}function i(a){return a.charAt(0).toUpperCase()+a.substr(1)}function j(b,e){if(this.$window=a(window),this.$document=a(document),this.$element=a(b),this.options=a.extend({},n,e),this.polyfill=this.options.polyfill,this.orientation=this.$element[0].getAttribute("data-orientation")||this.options.orientation,this.onInit=this.options.onInit,this.onSlide=this.options.onSlide,this.onSlideEnd=this.options.onSlideEnd,this.DIMENSION=o.orientation[this.orientation].dimension,this.DIRECTION=o.orientation[this.orientation].direction,this.DIRECTION_STYLE=o.orientation[this.orientation].directionStyle,this.COORDINATE=o.orientation[this.orientation].coordinate,this.polyfill&&m)return!1;this.identifier="js-"+k+"-"+l++,this.startEvent=this.options.startEvent.join("."+this.identifier+" ")+"."+this.identifier,this.moveEvent=this.options.moveEvent.join("."+this.identifier+" ")+"."+this.identifier,this.endEvent=this.options.endEvent.join("."+this.identifier+" ")+"."+this.identifier,this.toFixed=(this.step+"").replace(".","").length-1,this.$fill=a('<div class="'+this.options.fillClass+'" />'),this.$handle=a('<div class="'+this.options.handleClass+'" />'),this.$range=a('<div class="'+this.options.rangeClass+" "+this.options[this.orientation+"Class"]+'" id="'+this.identifier+'" />').insertAfter(this.$element).prepend(this.$fill,this.$handle),this.$element.css({position:"absolute",width:"1px",height:"1px",overflow:"hidden",opacity:"0"}),this.handleDown=a.proxy(this.handleDown,this),this.handleMove=a.proxy(this.handleMove,this),this.handleEnd=a.proxy(this.handleEnd,this),this.init();var f=this;this.$window.on("resize."+this.identifier,d(function(){c(function(){f.update(!1,!1)},300)},20)),this.$document.on(this.startEvent,"#"+this.identifier+":not(."+this.options.disabledClass+")",this.handleDown),this.$element.on("change."+this.identifier,function(a,b){if(!b||b.origin!==f.identifier){var c=a.target.value,d=f.getPositionFromValue(c);f.setPosition(d)}})}Number.isNaN=Number.isNaN||function(a){return"number"==typeof a&&a!==a};var k="rangeslider",l=0,m=b(),n={polyfill:!0,orientation:"horizontal",rangeClass:"rangeslider",disabledClass:"rangeslider--disabled",activeClass:"rangeslider--active",horizontalClass:"rangeslider--horizontal",verticalClass:"rangeslider--vertical",fillClass:"rangeslider__fill",handleClass:"rangeslider__handle",startEvent:["mousedown","touchstart","pointerdown"],moveEvent:["mousemove","touchmove","pointermove"],endEvent:["mouseup","touchend","pointerup"]},o={orientation:{horizontal:{dimension:"width",direction:"left",directionStyle:"left",coordinate:"x"},vertical:{dimension:"height",direction:"top",directionStyle:"bottom",coordinate:"y"}}};return j.prototype.init=function(){this.update(!0,!1),this.onInit&&"function"==typeof this.onInit&&this.onInit()},j.prototype.update=function(a,b){a=a||!1,a&&(this.min=h(this.$element[0].getAttribute("min"),0),this.max=h(this.$element[0].getAttribute("max"),100),this.value=h(this.$element[0].value,Math.round(this.min+(this.max-this.min)/2)),this.step=h(this.$element[0].getAttribute("step"),1)),this.handleDimension=g(this.$handle[0],"offset"+i(this.DIMENSION)),this.rangeDimension=g(this.$range[0],"offset"+i(this.DIMENSION)),this.maxHandlePos=this.rangeDimension-this.handleDimension,this.grabPos=this.handleDimension/2,this.position=this.getPositionFromValue(this.value),this.$element[0].disabled?this.$range.addClass(this.options.disabledClass):this.$range.removeClass(this.options.disabledClass),this.setPosition(this.position,b)},j.prototype.handleDown=function(a){if(a.preventDefault(),this.$document.on(this.moveEvent,this.handleMove),this.$document.on(this.endEvent,this.handleEnd),this.$range.addClass(this.options.activeClass),!((" "+a.target.className+" ").replace(/[\n\t]/g," ").indexOf(this.options.handleClass)>-1)){var b=this.getRelativePosition(a),c=this.$range[0].getBoundingClientRect()[this.DIRECTION],d=this.getPositionFromNode(this.$handle[0])-c,e="vertical"===this.orientation?this.maxHandlePos-(b-this.grabPos):b-this.grabPos;this.setPosition(e),b>=d&&b<d+this.handleDimension&&(this.grabPos=b-d)}},j.prototype.handleMove=function(a){a.preventDefault();var b=this.getRelativePosition(a),c="vertical"===this.orientation?this.maxHandlePos-(b-this.grabPos):b-this.grabPos;this.setPosition(c)},j.prototype.handleEnd=function(a){a.preventDefault(),this.$document.off(this.moveEvent,this.handleMove),this.$document.off(this.endEvent,this.handleEnd),this.$range.removeClass(this.options.activeClass),this.$element.trigger("change",{origin:this.identifier}),this.onSlideEnd&&"function"==typeof this.onSlideEnd&&this.onSlideEnd(this.position,this.value)},j.prototype.cap=function(a,b,c){return a<b?b:a>c?c:a},j.prototype.setPosition=function(a,b){var c,d;void 0===b&&(b=!0),c=this.getValueFromPosition(this.cap(a,0,this.maxHandlePos)),d=this.getPositionFromValue(c),this.$fill[0].style[this.DIMENSION]=d+this.grabPos+"px",this.$handle[0].style[this.DIRECTION_STYLE]=d+"px",this.setValue(c),this.position=d,this.value=c,b&&this.onSlide&&"function"==typeof this.onSlide&&this.onSlide(d,c)},j.prototype.getPositionFromNode=function(a){for(var b=0;null!==a;)b+=a.offsetLeft,a=a.offsetParent;return b},j.prototype.getRelativePosition=function(a){var b=i(this.COORDINATE),c=this.$range[0].getBoundingClientRect()[this.DIRECTION],d=0;return"undefined"!=typeof a.originalEvent["client"+b]?d=a.originalEvent["client"+b]:a.originalEvent.touches&&a.originalEvent.touches[0]&&"undefined"!=typeof a.originalEvent.touches[0]["client"+b]?d=a.originalEvent.touches[0]["client"+b]:a.currentPoint&&"undefined"!=typeof a.currentPoint[this.COORDINATE]&&(d=a.currentPoint[this.COORDINATE]),d-c},j.prototype.getPositionFromValue=function(a){var b,c;return b=(a-this.min)/(this.max-this.min),c=Number.isNaN(b)?0:b*this.maxHandlePos},j.prototype.getValueFromPosition=function(a){var b,c;return b=a/(this.maxHandlePos||1),c=this.step*Math.round(b*(this.max-this.min)/this.step)+this.min,Number(c.toFixed(this.toFixed))},j.prototype.setValue=function(a){a===this.value&&""!==this.$element[0].value||this.$element.val(a).trigger("input",{origin:this.identifier})},j.prototype.destroy=function(){this.$document.off("."+this.identifier),this.$window.off("."+this.identifier),this.$element.off("."+this.identifier).removeAttr("style").removeData("plugin_"+k),this.$range&&this.$range.length&&this.$range[0].parentNode.removeChild(this.$range[0])},a.fn[k]=function(b){var c=Array.prototype.slice.call(arguments,1);return this.each(function(){var d=a(this),e=d.data("plugin_"+k);e||d.data("plugin_"+k,e=new j(this,b)),"string"==typeof b&&e[b].apply(e,c)})},"rangeslider.js is available in jQuery context e.g $(selector).rangeslider(options);"});var czr_debug={log:function(a){debug.queue.push(["log",arguments,debug.stack.slice(0)]),window.console&&"function"==typeof window.console.log&&window.console.log(a)},error:function(a){debug.queue.push(["error",arguments,debug.stack.slice(0)]),window.console&&"function"==typeof window.console.error&&window.console.error(a)},queue:[],stack:[]},api=api||wp.customize,$=$||jQuery;!function(a,b,c){a.consoleLog=function(a){if(!c.isUndefined(console)||"function"==typeof window.console.log){var b=Array.from(arguments);b=b.join(" ");var d=["%c "+b,"background: #008ec2; color: white; display: block;"];console.log.apply(console,d)}},a.czr_isSkopOn=function(){return serverControlParams.isSkopOn&&c.has(a,"czr_skopeBase")},a.czr_isChangeSetOn=function(){return serverControlParams.isChangeSetOn&&!0},a.czr_activeSectionId=new a.Value(""),a.czr_activePanelId=new a.Value("");var d=function(){c.has(a,"czr_ModOptVisible")&&a.czr_ModOptVisible(!1)};a.czr_activeSectionId.bind(d),a.czr_activePanelId.bind(d),a.bind("ready",function(){if("function"!=typeof a.Section)throw new Error("Your current version of WordPress does not support the customizer sections needed for this theme. Please upgrade WordPress to the latest version.");var b=function(b,c){a.czr_activeSectionId(b?c:"")};a.section.each(function(a){a.expanded.bind(function(c){b(c,a.id)})}),a.section.bind("add",function(c){a.trigger("czr-paint",{active_panel_id:c.panel()}),c.expanded.bind(function(a){b(a,c.id)})});var d=function(b,d){a.czr_activePanelId(b?d:""),c.isEmpty(a.czr_activePanelId())&&a.czr_activeSectionId("")};a.panel.each(function(a){a.expanded.bind(function(b){d(b,a.id)})}),a.panel.bind("add",function(a){a.expanded.bind(function(b){d(b,a.id)})})}),a.bind("ready",function(){var d=function(){a.section("themes").active.bind(function(d){c.has(serverControlParams,"isThemeSwitchOn")&&c.isEmpty(serverControlParams.isThemeSwitchOn)&&(a.section("themes").active(!1),a.section("themes").active.callbacks=b.Callbacks())})};a.section.has("themes")?d():a.section.when("themes",function(a){d()})}),a.czr_skopeReady=b.Deferred(),a.bind("ready",function(){serverControlParams.isSkopOn&&(a.czr_isLoadingSkope=new a.Value((!1)),a.czr_isLoadingSkope.bind(function(a){e(a)}),a.czr_skopeBase=new a.CZR_skopeBase,a.czr_skopeSave=new a.CZR_skopeSave,a.czr_skopeReset=new a.CZR_skopeReset,a.trigger("czr-skope-started"),a.czr_skopeReady.done(function(){a.trigger("czr-skope-ready")}),setTimeout(function(){"pending"==a.czr_skopeReady.state()&&(a.czr_skopeBase.toggleTopNote(!0,{title:"There was a problem when trying to load the customizer.",message:'Please open your <a href="http://docs.presscustomizr.com/article/272-inspect-your-webpages-in-your-browser-with-the-development-tools" target="_blank">browser debug tool</a>, and report any error message (in red) printed in the javascript console in the <a href="https://wordpress.org/support/theme/hueman" target="_blank">Hueman theme forum</a>.',selfCloseAfter:4e4}),a.czr_isLoadingSkope(!1))},3e4)),serverControlParams.isChangeSetOn&&(a.settings.timeouts.changesetAutoSave=1e4)}),c.has(a,"_latestRevision")||(a._latestRevision=0,a._latestSettingRevisions={},a.bind("change",function(b){a._latestRevision+=1,a._latestSettingRevisions[b.id]=a._latestRevision}),a.bind("ready",function(){a.bind("add",function(b){b._dirty&&(a._latestRevision+=1,a._latestSettingRevisions[b.id]=a._latestRevision)})}));var e=function(d){d=!!c.isUndefined(d)||d;var e,f=function(){var a=b.Deferred();try{_tmpl=wp.template("czr-skope-pane")({is_skope_loading:!0})}catch(c){throw new Error("Error when parsing the the reset skope template : "+c)}return b.when(b("#customize-preview").after(b(_tmpl))).always(function(){a.resolve(b("#czr-skope-pane"))}),a.promise()},g=function(){c.delay(function(){b.when(b("body").removeClass("czr-skope-pane-open")).done(function(){c.delay(function(){b.when(b("body").removeClass("czr-skop-loading")).done(function(){!1!==b("#czr-skope-pane").length&&setTimeout(function(){b("#czr-skope-pane").remove()},400)})},200)})},50)};"pending"==a.czr_skopeReady.state()&&d&&(b("body").addClass("czr-skop-loading"),f().done(function(a){e=a}).then(function(){e.length&&c.delay(function(){var a=b("#customize-preview").height();e.css("line-height",a+"px").css("height",a+"px"),b("body").addClass("czr-skope-pane-open")},50)})),a.czr_skopeReady.done(function(){g()}),d||g()}}(wp.customize,jQuery,_);var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{globalSettingVal:{},initialize:function(){var a=this;a.skope_colors={global:"rgb(255, 255, 255)",special_group:"rgba(173, 213, 247, 0.55)",group:"rgba(173, 213, 247, 0.55)",local:"rgba(78, 122, 199, 0.35)"},api.czr_isPreviewerSkopeAware=$.Deferred(),api.czr_initialSkopeCollectionPopulated=$.Deferred(),a.skopeWrapperEmbedded=$.Deferred(),api.czr_skope=new api.Values,api.czr_skopeCollection=new api.Value([]),api.czr_currentSkopesCollection=new api.Value([]),api.czr_activeSkopeId=new api.Value,api.czr_dirtyness=new api.Value((!1)),api.czr_isResettingSkope=new api.Value((!1)),api.state.create("switching-skope")(!1),api.czr_dirtyness.callbacks.add(function(){return a.apiDirtynessReact.apply(a,arguments)}),api.czr_isLoadingSkope(!0),a.bindAPISettings(),api.state.bind("change",function(){a.setSaveButtonStates()}),"pending"==a.skopeWrapperEmbedded.state()&&$.when(a.embedSkopeWrapper()).done(function(){a.skopeWrapperEmbedded.resolve()}),api.previewer.bind("czr-skopes-synced",function(b){if(serverControlParams.isSkopOn){var c=this,d=api.czr_currentSkopesCollection();if(!_.has(b,"czr_skopes"))throw new Error("Missing skopes in the server data");api.czr_skopeBase.updateSkopeCollection(b.czr_skopes,c.channel()),api.czr_initialSkopeCollectionPopulated.then(function(){var c=_.isUndefined(_.findWhere(api.czr_currentSkopesCollection(),{id:api.czr_activeSkopeId()}));api.czr_skopeBase.reactWhenSkopeSyncedDone(b).done(function(){if(c)api.czr_activeSkopeId(a.getActiveSkopeId()).done(function(){api.consoleLog("INITIAL ACTIVE SKOPE SET : "+arguments[1]+" => "+arguments[0]),"pending"==api.czr_skopeReady.state()&&api.czr_skopeReady.resolve(a.getActiveSkopeId()),a._writeCurrentSkopeTitle()});else if(!_.isEmpty(d)){var e=_.findWhere(d,{skope:"local"}).opt_name,f=_.findWhere(b.czr_skopes,{skope:"local"}).opt_name;f!==e&&"resolved"==api.czr_skopeReady.state()&&a._writeCurrentSkopeTitle()}})})}}),api.czr_currentSkopesCollection.bind(function(b,c){return a.currentSkopesCollectionReact(b,c)},{deferred:!0}),api.czr_initialSkopeCollectionPopulated.done(function(){api.czr_activeSkopeId.bind(function(b,c){return _.has(api,"czr_ModOptVisible")&&api.czr_ModOptVisible(!1),a.activeSkopeReact(b,c)},{deferred:!0}),api.czr_activeSectionId.callbacks.add(function(){return a.activeSectionReact.apply(a,arguments)}),api.czr_activePanelId.callbacks.add(function(){return a.activePanelReact.apply(a,arguments)})}),api.bind("skope-switched",function(b,c){api.czr_skopeReady.then(function(){api.czr_CrtlDependenciesReady.then(function(){_.isUndefined(api.czr_activeSectionId())||_.isEmpty(api.czr_activeSectionId())||api.czr_ctrlDependencies.setServiDependencies(api.czr_activeSectionId(),null,!0)}),a.updateCtrlSkpNot(api.CZR_Helpers.getSectionControlIds()),api.czr_skope.has(c)&&$("#customize-controls").removeClass(["czr-",api.czr_skope(c)().skope,"-skope-level"].join("")),api.czr_skope.has(b)&&$("#customize-controls").addClass(["czr-",api.czr_skope(b)().skope,"-skope-level"].join(""))})}),api.czr_serverNotification=new api.Value({status:"success",message:"",expanded:!0}),api.czr_serverNotification.bind(function(b,c){a.toggleServerNotice(b)}),api.czr_topNoteVisible=new api.Value((!1)),api.czr_skopeReady.then(function(){api.czr_topNoteVisible.bind(function(b){var c={},d={title:"",message:"",actions:"",selfCloseAfter:2e4};c=$.extend(d,serverControlParams.topNoteParams),c.actions=function(){var a=$.extend(api.previewer.query(),{nonce:api.previewer.nonce.save});wp.ajax.post("czr_dismiss_top_note",a).always(function(){}).fail(function(b){api.consoleLog("czr_dismiss_top_note failed",a,b)}).done(function(a){})},a.toggleTopNote(b,c)}),_.delay(function(){api.czr_topNoteVisible(!_.isEmpty(serverControlParams.isTopNoteOn)||1==serverControlParams.isTopNoteOn)},2e3)}),a.scopeSwitcherEventMap=[{trigger:"click keydown",selector:".czr-dismiss-notification",name:"dismiss-notification",actions:function(){api.czr_serverNotification({expanded:!1})}},{trigger:"click keydown",selector:".czr-toggle-title-notice",name:"toggle-title-notice",actions:function(b){_.isUndefined(a.skopeTitleNoticeVisible)&&(a.skopeTitleNoticeVisible=new api.Value((!1)),a.skopeTitleNoticeVisible.bind(function(a){b.dom_el.find(".czr-skope-title").toggleClass("notice-visible",a)})),a.skopeTitleNoticeVisible(!a.skopeTitleNoticeVisible())}}],api.CZR_Helpers.setupDOMListeners(a.scopeSwitcherEventMap,{dom_el:$(".czr-scope-switcher")},a),a.refreshedControls=["czr_cropped_image"],a.initWidgetSidebarSpecifics(),api.bind("czr-paint",function(b){api.czr_skopeReady.then(function(){a.wash(b).paint(b)})})},embedSkopeWrapper:function(){var a=this;$("#customize-header-actions").append($("<div/>",{"class":"czr-scope-switcher",html:'<div class="czr-skopes-wrapper"></div>'})),$("body").addClass("czr-skop-on");var b=[{trigger:"click keydown",selector:".czr-skope-switch",name:"control_skope_switch",actions:function(a){var b=$(a.dom_event.currentTarget,a.dom_el).attr("data-skope-id");!_.isEmpty(b)&&api.czr_skope.has(b)&&api.czr_activeSkopeId(b)}}];api.CZR_Helpers.setupDOMListeners(b,{dom_el:$(".czr-scope-switcher")},a)},apiDirtynessReact:function(a){$("body").toggleClass("czr-api-dirty",a),api.state("saved")(!a)},setSaveButtonStates:function(){api.state.has("saving")||(api.state.create("saving"),api.state("saving").bind(function(a){$(document.body).toggleClass("saving",a)}));var a=$("#save"),b=$(".customize-controls-close"),c=api.state("saved"),d=api.state("saving"),e=api.state("activated"),f=api.state.has("changesetStatus")?api.state("changesetStatus")():"auto-draft";api.czr_dirtyness()||!c()?(a.val(api.l10n.save),b.find(".screen-reader-text").text(api.l10n.cancel)):(a.val(api.l10n.saved),b.find(".screen-reader-text").text(api.l10n.close));var g=!(d()||e()&&c()||"publish"===f);a.prop("disabled",!g)}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{toggleServerNotice:function(a){if(a=_.isObject(a)?a:{},a=_.extend({status:"success",expanded:!0,message:"",auto_collapse:!1},a),"changeset_already_published"!=a.message&&serverControlParams.isDevMode){this.serverNoticeEmbedded=this.serverNoticeEmbedded||$.Deferred();var b=this,c=function(){$(".czr-scope-switcher").prepend($("<div/>",{"class":"czr-server-notice",html:'<span class="czr-server-message"></span><span class="fa fa-times-circle czr-dismiss-notification"></span>'}))},d=function(){var c,d,e=$(".czr-server-notice",".czr-scope-switcher"),f=$(".wp-full-overlay-header"),g=($(".wp-full-overlay-sidebar .wp-full-overlay-sidebar-content"),function(a){return!0});b.skopeTitleNoticeVisible&&b.skopeTitleNoticeVisible(!1),a.expanded?(e.toggleClass("czr-server-error","error"==a.status),"error"==a.status?$(".czr-server-message",e).html(_.isEmpty(a.message)?"Server Problem.":a.message):$(".czr-server-message",e).html(_.isEmpty(a.message)?"Success.":a.message),d=$(".czr-server-notice",".czr-scope-switcher").outerHeight(),c=f.outerHeight()+d,setTimeout(function(){$.when(g(c)).done(function(){e.fadeIn({duration:200,complete:function(){$(this).css("height","auto")}})})},400)):(e.fadeOut({duration:200,complete:function(){}}),setTimeout(function(){g()},200))};"pending"==b.serverNoticeEmbedded.state()?$.when(c()).done(function(){setTimeout(function(){b.serverNoticeEmbedded.resolve(),d()},200)}):d(),_.delay(function(){api.czr_serverNotification({expanded:!1})},"success"==a.status||!1!==a.auto_collapse?4e3:5e3)}},buildServerResponse:function(a){var b=!1;if(_.isObject(a)&&(!_.has(a,"responseJSON")||_.isUndefined(a.responseJSON.data)||_.isEmpty(a.responseJSON.data)?_.has(a,"statusText")&&!_.isEmpty(a.statusText)&&(b=a.statusText):b=a.responseJSON.data),_.isObject(a)&&!b)try{JSON.stringify(a)}catch(c){b="Server Error"}else b?"-1"===a&&(b="Identification issue detected, please refresh your page."):b="0"===a?"Not logged in.":a;return b}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{toggleTopNote:function(a,b){b=_.isObject(b)?b:{};var c=this,d={title:"",message:"",actions:"",selfCloseAfter:2e4},e=function(){$.when(c.renderTopNoteTmpl(b)).done(function(a){c.welcomeNote=a,_.delay(function(){$("body").addClass("czr-top-note-open")},200),api.CZR_Helpers.setupDOMListeners([{trigger:"click keydown",selector:".czr-top-note-close",name:"close-top-note",actions:function(){f().done(function(){_.isFunction(b.actions)&&b.actions()})}}],{dom_el:c.welcomeNote},c)})},f=function(){var a=$.Deferred();return $("body").removeClass("czr-top-note-open"),c.welcomeNote.length?_.delay(function(){c.welcomeNote.remove(),a.resolve()},300):a.resolve(),a.promise()};b=$.extend(d,b),a?e():f(),_.delay(function(){f()},b.selfCloseAfter||2e4)},renderTopNoteTmpl:function(a){if($("#czr-top-note").length)return $("#czr-top-note");var b="",c=a.title||"",d=a.message||"";try{b=wp.template("czr-top-note")({title:c})}catch(e){throw new Error("Error when parsing the the top note template : "+e)}return $("#customize-preview").after($(b)),$(".czr-note-message","#czr-top-note").html(d),$("#czr-top-note")}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{bindAPISettings:function(a){var b=this,c=function(a,c,d){var e,f=this.id;_.has(api,"czr_activeSkopeId")&&!_.isUndefined(api.czr_activeSkopeId())||api.consoleLog("The api.czr_activeSkopeId() is undefined in the api.previewer._new_refresh() method."),api(f)._dirty&&(e=b.isSettingSkopeEligible(f)?api.czr_activeSkopeId():b.getGlobalSkopeId(),api.czr_skope(e).updateSkopeDirties(f,a)),_.has(api.control(f),"czr_states")&&!api.control(f).czr_states("isResetting")()&&api.control(f).czr_states("resetVisible")(!1),b.isSettingSkopeEligible(f)&&b.updateCtrlSkpNot(f)};_.isUndefined(a)?api.each(function(a){a.bind(c)}):api(a).bind(c);var d=function(a){a.callbacks.has(c)||a.bind(c)};api.topics.change.has(d)||api.bind("change",d)}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{reactWhenSkopeSyncedDone:function(a){var b=$.Deferred();if(!_.has(a,"czr_skopes")||_.isEmpty(a.czr_skopes))throw new Error("Missing skope data after refresh",a);api.czr_dirtyness()||api.czr_dirtyness(!!_.isBoolean(a.isChangesetDirty)&&a.isChangesetDirty);var c=a.czr_skopes;return _.each(api.czr_skopeCollection(),function(a){var b=_.findWhere(c,{opt_name:a.opt_name});if(!_.isUndefined(b)){var d=_.isEmpty(b.changeset||{})?{}:b.changeset,e={};_.each(d,function(a,b){api.has(b)||api.consoleLog("In reactWhenSkopeSyncedDone : attempting to update the changeset with a non registered setting : "+b),e[b]=a}),api.czr_skope(a.id).changesetValues(e)}}),_.each(api.czr_skopeCollection(),function(a){var b=_.findWhere(c,{opt_name:a.opt_name});if(!_.isUndefined(b)){var d=$.extend(!0,{},api.czr_skope(a.id).dbValues()),e=$.extend(d,b.db||{}),f={};_.each(e,function(a,b){api.has(b)||api.consoleLog("In reactWhenSkopeSyncedDone : attempting to update the db values with a non registered setting : "+b),f[b]=a}),api.czr_skope(a.id).dbValues(f)}}),_.delay(function(){b.resolve()},500),b.promise()}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{_maybeSetupAssignedMenuLocations:function(a){(_.isUndefined(a)||_.isEmpty(a)||!api.section.has(a.id))&&api.consoleLog("In _maybeSetupAssignedMenuLocations : no valid section_id provided.");if(a.assignedLocations){var b=function(a){};a.assignedLocations.callbacks.has(b)||a.assignedLocations.bind(b)}},activeSectionReact:function(a,b){"add_menu"!=a&&api.trigger("czr-paint",{active_section_id:a});var c=this,d=function(a){var b=api.CZR_Helpers.getSectionControlIds(a);_.each(b,function(a){if(api.has(a)&&!_.isUndefined(api.control(a))){var b=api.control(a);_.has(b,"czr_states")&&(b.czr_states("noticeVisible")(!1),b.czr_states("resetVisible")(!1))}})},e=function(a,b){c.setupActiveSkopedControls({section_id:b}),c.processSilentUpdates({section_id:b}).fail(function(){throw new Error("Fail to process silent updates after initial skope collection has been populated")}).done(function(){c.isExcludedSidebarsWidgets()||c.forceSidebarDirtyRefresh(b,api.czr_activeSkopeId())}),_.has(api.topics,"active-section-setup")||api.bind("active-section-setup",function(a){var b={controls:[],section_id:""};a=_.extend(b,a),c._maybeSetupAssignedMenuLocations(a)}),api.czr_skopeReady.then(function(){var a=function(a){api.czr_serverNotification({status:"success",message:[a,"can only be customized site wide."].join(" ")}),api.czr_activeSkopeId(c.getGlobalSkopeId())};"global"!=api.czr_skope(api.czr_activeSkopeId())().skope&&(!c.isExcludedWPCustomCss()||"custom_css"!=b&&"admin_sec"!=b||a(api.section(b).params.title),"nav_menu["!=b.substring(0,"nav_menu[".length)&&"add_menu"!=b||api.czr_serverNotification({status:"success",message:["Menus are created site wide."].join(" ")}))}),api.trigger("active-section-setup",a)};api.czr_initialSkopeCollectionPopulated.then(function(){api.section.when(a,function(b){b.deferred.embedded.then(function(){e(b,a)})}),!_.isEmpty(b)&&api.section.has(b)&&d(b)})},activePanelReact:function(a,b){var c=this;api.czr_initialSkopeCollectionPopulated.then(function(){api.trigger("czr-paint",{active_panel_id:a});api.czr_skopeReady.then(function(){"global"!=api.czr_skope(api.czr_activeSkopeId())().skope&&c.isExcludedSidebarsWidgets()&&"widgets"==a&&api.czr_serverNotification({status:"success",message:["Widgets are created site wide."].join(" ")})}),api.czr_skopeReady.then(function(){"nav_menus"==a&&_.each(api.panel(a).sections(),function(a){c.processSilentUpdates({section_id:a.id,awake_if_not_active:!0})})})})}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{wash:function(a){var b=function(a){_.has(a,"el")&&a.el.length&&$.when(a.el.removeClass("czr-painted")).done(function(){$(this).css("background","").css("color","")})};return api.czr_skopeBase.paintedElements&&(_.each(api.czr_skopeBase.paintedElements(),function(a){b(a)}),api.czr_skopeBase.paintedElements([])),this},paint:function(a){var b="inherit",c={active_panel_id:api.czr_activePanelId(),active_section_id:api.czr_activeSectionId(),is_skope_switch:!1},d=[];a=$.extend(c,a),!_.isUndefined(api.czr_activeSkopeId())&&api.czr_skope.has(api.czr_activeSkopeId())&&(b=api.czr_skope(api.czr_activeSkopeId()).color);var e=function(c){_.has(c,"el")&&c.el.length&&(a.is_skope_switch?$.when(c.el.addClass("czr-painted")).done(function(){$(this).css("background",c.bgColor||b)}):c.el.css("background",c.bgColor||b),"global"!=api.czr_skope(api.czr_activeSkopeId())().skope&&c.el.css("color","#000"))};return api.czr_skopeBase.paintedElements=api.czr_skopeBase.paintedElements||new api.Value([]),_.isEmpty(a.active_panel_id)&&_.isEmpty(a.active_section_id)&&(d.push({el:$("#customize-info").find(".accordion-section-title").first()}),api.panel.each(function(a){d.push({el:a.container.find(".accordion-section-title").first()})}),api.section.each(function(a){_.isEmpty(a.panel())&&d.push({el:a.container.find(".accordion-section-title").first()})})),!_.isEmpty(a.active_panel_id)&&_.isEmpty(a.active_section_id)&&api.panel.when(a.active_panel_id,function(a){a.deferred.embedded.then(function(){d.push({el:a.container.find(".accordion-section-title, .customize-panel-back")})})}),_.isEmpty(a.active_section_id)||api.section.when(a.active_section_id,function(a){a.deferred.embedded.then(function(){d.push({el:a.container.find(".customize-section-title, .customize-section-back"),bgColor:"inherit"},{el:a.container}),api.czr_isChangeSetOn()||d.push({el:a.container.find(".accordion-section-content")})})}),_.each(d,function(a){e(a)}),api.czr_skopeBase.paintedElements(d),this}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{isSkopeRegisteredInCollection:function(a,b){return b=b||api.czr_skopeCollection(),!_.isUndefined(_.findWhere(b,{id:a}))},isSkopeRegisteredInCurrentCollection:function(a,b){return b=b||api.czr_currentSkopesCollection(),!_.isUndefined(_.findWhere(b,{id:a}))},isGlobalSkopeRegistered:function(){var a=_.findWhere(api.czr_currentSkopesCollection(),{skope:"global"});return _.isObject(a)&&_.has(a,"id")},getGlobalSkopeId:function(){if(!_.has(api,"czr_skope"))return"";var a="";return api.czr_skope.each(function(b){"global"==b().skope&&(a=b().id)}),a},getChangedGlobalDBSettingValues:function(a){var b={};return _.each(a,function(a,c){_wpSetId=api.CZR_Helpers.build_setId(c),_.has(api.settings.settings,_wpSetId)&&(_.isEqual(a,api.settings.settings[_wpSetId].value)||(b[c]=a))}),b},getActiveSkopeId:function(a){a=a||api.czr_currentSkopesCollection();var b=!_.isEmpty(api.czr_activeSkopeId())&&api.czr_skope.has(api.czr_activeSkopeId())?api.czr_skope(api.czr_activeSkopeId())().skope:serverControlParams.isLocalSkope?"local":"global",c=_.findWhere(a,{skope:b});if(_skpId=_.isUndefined(c)?_.findWhere(a,{skope:"global"}).id:c.id,_.isUndefined(_skpId))throw new Error("No default skope was found in getActiveSkopeId ",a);return _skpId},getActiveSkopeName:function(){return api.czr_skope.has(api.czr_activeSkopeId())?api.czr_skope(api.czr_activeSkopeId())().skope:"global"},isSettingSkopeEligible:function(a){var b=this,c=api.CZR_Helpers.getOptionName(a);return _.isUndefined(a)||!api.has(a)?(api.consoleLog("THE SETTING "+a+" IS NOT ELIGIBLE TO SKOPE BECAUSE UNDEFINED OR NOT REGISTERED IN THE API."),!1):!b.isExcludedWPBuiltinSetting(a)&&(!_.contains(serverControlParams.skopeExcludedSettings,c)&&(b.isThemeSetting(a),!0))},isSettingResetEligible:function(a){var b=this;api.CZR_Helpers.getOptionName(a);if(_.isUndefined(a)||!api.has(a))return void api.consoleLog("THE SETTING "+a+" IS NOT ELIGIBLE TO RESET BECAUSE UNDEFINED OR NOT REGISTERED IN THE API.");if(!b.isExcludedWPBuiltinSetting(a))return!(!b.isThemeSetting(a)&&!b.isWPAuthorizedSetting(a))||void api.consoleLog("THE SETTING "+a+" IS NOT ELIGIBLE TO RESET BECAUSE NOT PART OF THE THEME OPTIONS AND NOT WP AUTHORIZED BUILT IN OPTIONS")},isThemeSetting:function(a){return _.isString(a)&&-1!==a.indexOf(serverControlParams.themeOptions)},isWPAuthorizedSetting:function(a){return _.isString(a)&&_.contains(serverControlParams.wpBuiltinSettings,a)},isExcludedWPBuiltinSetting:function(a){var b=this;if(_.isUndefined(a))return!0;if("active_theme"==a)return!0;if(_.contains(serverControlParams.wpBuiltinSettings,a))return!1;var c=["widget_","nav_menu","sidebars_","custom_css","nav_menu[","nav_menu_item","nav_menus_created_posts","nav_menu_locations"],d=!1;return _.each(c,function(c){switch(c){case"widget_":case"sidebars_":c==a.substring(0,c.length)&&(d=b.isExcludedSidebarsWidgets());break;case"nav_menu[":case"nav_menu_item":case"nav_menus_created_posts":c==a.substring(0,c.length)&&(d=!0);break;case"nav_menu_locations":c==a.substring(0,c.length)&&(d=b.isExcludedNavMenuLocations());break;case"custom_css":c==a.substring(0,c.length)&&(d=b.isExcludedWPCustomCss())}}),d},isExcludedSidebarsWidgets:function(){var a=serverControlParams.isSidebarsWigetsSkoped;return!(!_.isUndefined(a)&&!_.isEmpty(a)&&!1!==a)},isExcludedNavMenuLocations:function(){if(!api.czr_isChangeSetOn())return!0;var a=serverControlParams.isNavMenuLocationsSkoped;return!(!_.isUndefined(a)&&!_.isEmpty(a)&&!1!==a)},isExcludedWPCustomCss:function(){var a=serverControlParams.isWPCustomCssSkoped;return!(!_.isUndefined(a)&&!_.isEmpty(a)&&!1!==a)},_getDBSettingVal:function(a,b){var c=api.CZR_Helpers.getOptionName(a),d=api.CZR_Helpers.build_setId(a);return api.czr_skope.has(b)?_.has(api.czr_skope(b).dbValues(),d)?api.czr_skope(b).dbValues()[d]:_.has(api.czr_skope(b).dbValues(),c)?api.czr_skope(b).dbValues()[c]:"_no_db_val":(api.consoleLog("_getDBSettingVal : the requested skope id is not registered : "+b),"_no_db_val")},getSkopeDirties:function(a,b){
if(!api.czr_skope.has(a))return{};b=b||{},b=_.extend({unsaved:!0},b);var c={};return _.each(api.czr_skope(a).dirtyValues(),function(a,d){var e;api.czr_isChangeSetOn()&&(e=api._latestSettingRevisions[d],api.state("changesetStatus").get()&&b&&b.unsaved&&(_.isUndefined(e)||e<=api._lastSavedRevision))||(c[d]=a)}),c},getSkopeExcludedDirties:function(){var a=this,b={};api.each(function(a,c){a._dirty&&(b[c]=a())});var c=a.getGlobalSkopeId();a.getSkopeDirties(c);return _.omit(b,function(b,c){return a.isSettingSkopeEligible(c)})},parseWidgetId:function(a,b){var c,d={number:null,id_base:null};return c=a.match(/^(.+)-(\d+)$/),c?(d.id_base=c[1],d.number=parseInt(c[2],10)):d.id_base=a,_.isUndefined(b)||(d.id_base=d.id_base.replace(b,"")),d},widgetIdToSettingId:function(a,b){var c,d=this.parseWidgetId(a,b);return c=d.id_base,d.number&&(c+="["+d.number+"]"),c},isWidgetRegisteredGlobally:function(a){var b=this;return registered=!1,_.each(_wpCustomizeWidgetsSettings.registeredWidgets,function(c,d){registered||"widget_"+b.widgetIdToSettingId(d)!=a||(registered=!0)}),registered}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{getAppliedPrioritySkopeId:function(a,b){if(!api.has(api.CZR_Helpers.build_setId(a)))throw new Error("getAppliedPrioritySkopeId : the requested setting id does not exist in the api : "+api.CZR_Helpers.build_setId(a));if(!api.czr_skope.has(b))throw new Error("getAppliedPrioritySkopeId : the requested skope id is not registered : "+b);var c=this,d=_.findWhere(api.czr_currentSkopesCollection(),{skope:"local"}).id;if(_.isUndefined(d)||b==d)return b;var e=function(d){var f=api.CZR_Helpers.build_setId(a),g="___",h=api.czr_skope(d)();if(d==b)return b;if(api.czr_skope(d).getSkopeSettingAPIDirtyness(f))return h.id;if(api.czr_isChangeSetOn()&&api.czr_skope(d).getSkopeSettingChangesetDirtyness(f))return h.id;var i=c._getDBSettingVal(a,d);return"_no_db_val"!=i?h.id:"global"==h.skope?h.id:"___"!=g?h.title:e(c._getParentSkopeId(h))};return e(d)},getOverridenSkopeTitles:function(){var a=a||api.czr_activeSkopeId();if(!api.czr_skope.has(a))throw new Error("getInheritedSkopeTitles : the requested skope id is not registered : "+a);var b=this,c=_.findWhere(api.czr_currentSkopesCollection(),{skope:"local"}).id;if(!_.isUndefined(c)&&a!=c){var d=function(c,e){e=e||[];var f=api.czr_skope(c)();return c==a?e:(e.unshift(c),d(b._getParentSkopeId(f),e))};return _.map(d(c),function(a){return b.buildSkopeLink(a)}).join(" and ")}},getInheritedSkopeId:function(a,b){if(!api.has(api.CZR_Helpers.build_setId(a)))throw new Error("getInheritedSkopeId : the requested setting id does not exist in the api : "+api.CZR_Helpers.build_setId(a));if(!api.czr_skope.has(b))throw new Error("getInheritedSkopeId : the requested skope id is not registered : "+b);var c,d=this,e=api.CZR_Helpers.build_setId(a),f="___",g=api.czr_skope(b)();if(c=_.has(api.settings.settings,e)?api.settings.settings[e].value:null,api.czr_skope(b).getSkopeSettingAPIDirtyness(e))return b;if(api.czr_isChangeSetOn()&&api.czr_skope(b).getSkopeSettingChangesetDirtyness(e))return b;var h=d._getDBSettingVal(a,b);return"_no_db_val"!=h?b:"global"==g.skope?b:"___"!=f?b:d.getInheritedSkopeId(a,d._getParentSkopeId(g))},getInheritedSkopeTitles:function(a,b){if(a=a||api.czr_activeSkopeId(),!api.czr_skope.has(a))throw new Error("getInheritedSkopeTitles : the requested skope id is not registered : "+a);b=b||[];var c=this,d=api.czr_skope(a)();return a!==api.czr_activeSkopeId()&&b.unshift(a),"global"!==d.skope?c.getInheritedSkopeTitles(c._getParentSkopeId(d),b):_.map(b,function(a){return c.buildSkopeLink(a)}).join(" and ")},buildSkopeLink:function(a){if(!api.czr_skope.has(a))throw new Error("buildSkopeLink : the requested skope id is not registered : "+a);var b="Switch to scope : "+api.czr_skope(a)().title;return['<span class="czr-skope-switch" title=" '+b+'" data-skope-id="'+a+'">',api.czr_skope(a)().title,"</span>"].join("")},getSkopeSettingVal:function(a,b){if(!api.has(api.CZR_Helpers.build_setId(a)))throw new Error("getSkopeSettingVal : the requested setting id does not exist in the api : "+api.CZR_Helpers.build_setId(a));if(!api.czr_skope.has(b))throw new Error("getSkopeSettingVal : the requested skope id is not registered : "+b);var c,d=this,e=api.CZR_Helpers.build_setId(a),f="___",g=api.czr_skope(b)();if(c=_.has(api.settings.settings,e)?api.settings.settings[e].value:null,api.czr_skope(b).getSkopeSettingAPIDirtyness(e))return api.czr_skope(b).dirtyValues()[e];if(api.czr_isChangeSetOn()&&api.czr_skope(b).getSkopeSettingChangesetDirtyness(e))return api.czr_skope(b).changesetValues()[e];var h=d._getDBSettingVal(a,b);return"_no_db_val"!=h?h:"global"==g.skope?"___"==f?c:f:"___"!=f?f:d.getSkopeSettingVal(a,d._getParentSkopeId(g))},applyDirtyCustomizedInheritance:function(a,b){b=b||api.czr_activeSkopeId()||api.czr_skopeBase.getGlobalSkopeId(),a=a||{};var c=this,d=api.czr_skope(b)();if("global"==d.skope)return a;var e=c._getParentSkopeId(d),f=api.czr_skope(e).dirtyValues();return _.each(f,function(b,c){var e=api.CZR_Helpers.getOptionName(c);_.isUndefined(a[c])&&_.isUndefined(api.czr_skope(d.id).dbValues()[e])&&(a[c]=b)}),"global"==api.czr_skope(e)().skope?a:c.applyDirtyCustomizedInheritance(a,e)},_getParentSkopeId:function(a,b){var c=this,d=["local","group","special_group","global"],e=b||1*(_.findIndex(d,function(b){return a.skope==b})+1),f=d[e];return _.isUndefined(f)?_.findWhere(api.czr_currentSkopesCollection(),{skope:"global"}).id:_.isUndefined(_.findWhere(api.czr_currentSkopesCollection(),{skope:f}))?c._getParentSkopeId(a,e+1):_.findWhere(api.czr_currentSkopesCollection(),{skope:f}).id},_getChildSkopeId:function(a,b){var c=this,d=["local","group","special_group","global"],e=b||1*(_.findIndex(d,function(b){return a.skope==b})-1),f=d[e];return _.isUndefined(f)?_.findWhere(api.czr_currentSkopesCollection(),{skope:"local"}).id:_.isUndefined(_.findWhere(api.czr_currentSkopesCollection(),{skope:f}))?c._getParentSkopeId(a,e-1):_.findWhere(api.czr_currentSkopesCollection(),{skope:f}).id}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{updateSkopeCollection:function(a,b){var c=this;if(_api_ready_collection=[],_.each(a,function(a,b){var d=$.extend(!0,{},a);_api_ready_collection.push(c.prepareSkopeForAPI(d))}),c.isGlobalSkopeRegistered()){var d=[],e=$.extend(!0,{},api.czr_skope(c.getGlobalSkopeId())());_.each(_api_ready_collection,function(a,b){"global"==a.skope?d.push(e):d.push(a)}),_api_ready_collection=d}api.czr_currentSkopesCollection(_api_ready_collection)},prepareSkopeForAPI:function(a){if(!_.isObject(a))throw new Error("prepareSkopeForAPI : a skope must be an object to be API ready");var b=this,c={};if(_.each(serverControlParams.defaultSkopeModel,function(b,d){var e=a[d];switch(d){case"title":if(!_.isString(e))throw new Error("prepareSkopeForAPI : a skope title property must a string");c[d]=e;break;case"long_title":if(!_.isString(e))throw new Error("prepareSkopeForAPI : a skope title property must a string");c[d]=e;break;case"skope":if(!_.isString(e)||_.isEmpty(e))throw new Error('prepareSkopeForAPI : a skope "skope" property must a string not empty');c[d]=e;break;case"level":if(!_.isString(e)||_.isEmpty(e))throw new Error("prepareSkopeForAPI : a skope level must a string not empty for skope "+e.skope);c[d]=e;break;case"dyn_type":if(!_.isString(e)||!_.contains(serverControlParams.skopeDynTypes,e))throw new Error("prepareSkopeForAPI : missing or invalid dyn type for skope "+a);c[d]=e;break;case"opt_name":if(!_.isString(e)||_.isEmpty(e))throw new Error('prepareSkopeForAPI : invalid "opt_name" property for skope '+e.skope);c[d]=e;break;case"obj_id":if(!_.isString(e))throw new Error('prepareSkopeForAPI : invalid "obj_id" for skope '+e.skope);c[d]=e;break;case"is_winner":if(!_.isUndefined(e)&&!_.isBoolean(e))throw new Error('prepareSkopeForAPI : skope property "is_winner" must be a boolean');c[d]=e;break;case"is_forced":if(!_.isUndefined(e)&&!_.isBoolean(e))throw new Error('prepareSkopeForAPI : skope property "is_primary" must be a boolean');c[d]=e;break;case"db":if((_.isArray(e)||_.isEmpty(e))&&(e={}),_.isUndefined(e)||!_.isObject(e))throw new Error('prepareSkopeForAPI : skope property "db" must be an object');c[d]=e;break;case"changeset":if((_.isArray(e)||_.isEmpty(e))&&(e={}),_.isUndefined(e)||!_.isObject(e))throw new Error('prepareSkopeForAPI : skope property "changeset" must be an object');c[d]=e;break;case"has_db_val":if(!_.isUndefined(e)&&!_.isBoolean(e))throw new Error('prepareSkopeForAPI : skope property "has_db_val" must be a boolean');c[d]=e}}),c.color=b.skope_colors[c.skope]||"rgb(255, 255, 255)",c.id=c.skope+"_"+c.level,!_.isString(c.id)||_.isEmpty(c.id))throw new Error("prepareSkopeForAPI : a skope id must a string not empty");return _.isString(c.title)&&!_.isEmpty(c.title)||(c.title=id,c.long_title=id),c},currentSkopesCollectionReact:function(a,b){var c=this,d=$.extend(!0,[],a)||[],e=($.extend(!0,[],b)||[],$.Deferred()),f=[];_.each(d,function(a){api.czr_skope.has(a.id)||f.push(a)}),_.each(f,function(a){a=$.extend(!0,{},a),api.czr_skope.add(a.id,new api.CZR_skope(a.id,a))}),_.each(f,function(a){if(!api.czr_skope.has(a.id))throw new Error("Skope id : "+a.id+" has not been instantiated.");"pending"==api.czr_skope(a.id).isReady.state()&&api.czr_skope(a.id).ready()});var g=_.size(d),h=function(a){var b=a.container.attr("class").split(" ");_.each(a.container.attr("class").split(" "),function(a){"width-"==a.substring(0,6)&&(b=_.without(b,a))}),$.when(a.container.attr("class",b.join(" "))).done(function(){a.container.addClass("width-"+Math.round(100/g))})};return api.czr_skope.each(function(a){if(_.isUndefined(_.findWhere(d,{id:a().id})))a.visible(!1),a.isReady.then(function(){a.container.toggleClass("active-collection",!1)});else{a.visible(!0);var b=function(){h(a),a.container.toggleClass("active-collection",!0)};"pending"==a.isReady.state()?a.isReady.then(function(){b()}):b()}}),_.isEmpty(b)&&!_.isEmpty(a)&&api.czr_initialSkopeCollectionPopulated.resolve(),c.maybeSynchronizeGlobalSkope(),e.resolve("changed").promise()},maybeSynchronizeGlobalSkope:function(a){if(a=a||{},!_.isObject(a))throw new Error("maybeSynchronizeGlobalSkope : args must be an object");var b,c,d,e=this,f=$.Deferred();_.extend({isGlobalReset:!1,isSetting:!1,settingIdToReset:"",isSkope:!1,skopeIdToReset:""},a);if(e.isGlobalSkopeRegistered()){var g=api.czr_skope(e.getGlobalSkopeId()).dbValues();if(_.each(g,function(a,b){api.has(b)&&!_.isEqual(api.settings.settings[b].value,a)&&(api.settings.settings[b].value=a)}),a.isGlobalReset&&a.isSetting){if(b=a.settingIdToReset,c=api.CZR_Helpers.getOptionName(b),d=serverControlParams.defaultOptionsValues[c],_.isUndefined(api.settings.settings[b])||_.isUndefined(d))return;d!=api.settings.settings[b].value&&(api.settings.settings[b].value=d)}a.isGlobalReset&&a.isSkope&&_.each(api.settings.settings,function(a,b){e.isThemeSetting(b)&&(c=api.CZR_Helpers.getOptionName(b),_.has(serverControlParams.defaultOptionsValues,c)&&(api.settings.settings[b].value=serverControlParams.defaultOptionsValues[c]))})}return f.resolve().promise()}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{activeSkopeReact:function(a,b){var c=this,d=$.Deferred();if(!_.isUndefined(b)&&api.czr_skope.has(b))api.czr_skope(b).active(!1);else if(!_.isUndefined(b))throw new Error("listenToActiveSkope : previous scope does not exist in the collection",b);if(_.isUndefined(a)||!api.czr_skope.has(a))throw new Error("listenToActiveSkope : requested scope "+a+" does not exist in the collection");api.czr_skope(a).active(!0);var e=function(a){return api.czr_activeSkopeId(c.getGlobalSkopeId()),api.czr_serverNotification({status:"success",message:[a,"can only be customized site wide."].join(" ")}),d.resolve().promise()};if(c.isExcludedSidebarsWidgets()&&"widgets"==api.czr_activePanelId()&&a!=c.getGlobalSkopeId()&&api.czr_serverNotification({status:"success",message:["Widgets are created site wide."].join(" ")}),c.isExcludedWPCustomCss()&&"custom_css"==api.czr_activeSectionId()&&a!=c.getGlobalSkopeId())return e(api.section(api.czr_activeSectionId()).params.title);if("admin_sec"==api.czr_activeSectionId()&&a!=c.getGlobalSkopeId())return e(api.section(api.czr_activeSectionId()).params.title);if("nav_menu"!=api.czr_activeSectionId().substring(0,"nav_menu".length)&&"add_menu"!=api.czr_activeSectionId()||a==c.getGlobalSkopeId()||api.czr_serverNotification({status:"success",message:["Menus are created site wide."].join(" ")}),"nav_menus"==api.czr_activePanelId()&&_.each(api.panel(api.czr_activePanelId()).sections(),function(a){c.processSilentUpdates({section_id:a.id,awake_if_not_active:!0})}),api.state("switching-skope")(!0),c._writeCurrentSkopeTitle(a),api.trigger("czr-paint",{is_skope_switch:!0}),_.isUndefined(api.czr_activeSectionId()))return api.state("switching-skope")(!1),api.previewer.refresh(),d.resolve().promise();_.has(api,"czrModulePanelState")&&api.czrModulePanelState(!1);var f=c._getSilentUpdateCandidates();_.isUndefined(b)||_.each(api.czr_skope(b).dirtyValues(),function(a,b){_.contains(f,b)||f.push(b)}),_.isUndefined(a)||_.each(api.czr_skope(a).dirtyValues(),function(a,b){_.contains(f,b)||f.push(b)});var g=function(){c.processSilentUpdates({candidates:f,section_id:null,refresh:!1}).fail(function(){throw d.reject(),api.state("switching-skope")(!1),new Error("Fail to process silent updates in _debouncedProcessSilentUpdates")}).done(function(c){api.previewer.refresh().always(function(){api.trigger("skope-switched",a,b),d.resolve(),api.state("switching-skope")(!1)})})};return _.has(api,"czr_isModuleExpanded")&&!1!==api.czr_isModuleExpanded()?(api.czr_isModuleExpanded().setupModuleViewStateListeners(!1),(g=_.debounce(g,400))()):g(),d.promise()},_writeCurrentSkopeTitle:function(a){var b=this,c=api.czr_skope(a||api.czr_activeSkopeId())().long_title,d=function(){var d=b.getInheritedSkopeTitles(),e=b.getOverridenSkopeTitles();return $.trim(['<span class="czr-main-title"><span class="czr-toggle-title-notice fa fa-info-circle"></span>',"global"==api.czr_skope(a||api.czr_activeSkopeId())().skope?c:["Customizing",c].join(" "),"</span>",'<span class="czr-skope-inherits-from">',"In this context :",_.isEmpty(d)?" ":"inherits from",d,_.isEmpty(d)?"":_.isEmpty(e)?".":", and",_.isEmpty(e)?" ":"overridden by",e,_.isEmpty(e)?"":".","</span>"].join(" "))},e=function(a){a?$(".czr-scope-switcher").find(".spinner").fadeIn():$(".czr-scope-switcher").find(".spinner").fadeOut()};b.skopeWrapperEmbedded.then(function(){$(".czr-scope-switcher").find(".czr-current-skope-title").length?$.when($(".czr-scope-switcher").find(".czr-skope-title").fadeOut(200)).done(function(){$(this).html(d()).fadeIn(200)}):$(".czr-scope-switcher").prepend($("<h2/>",{"class":"czr-current-skope-title",html:['<span class="czr-skope-title">','<span class="spinner">',d(),"</span>","</span>"].join("")})),_.isUndefined(api.state("switching-skope").isBound)&&(api.state("switching-skope").bind(e),api.state("switching-skope").isBound=!0)})}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{processSilentUpdates:function(a){a=_.isString(a)?{candidates:[a]}:a||{};var b=this,c={candidates:[],section_id:api.czr_activeSectionId(),refresh:!0,awake_if_not_active:!1},d=$.Deferred();if(a=$.extend(c,a),_.isString(a.candidates)&&(a.candidates=[a.candidates]),_.isEmpty(a.candidates)&&(a.candidates=b._getSilentUpdateCandidates(a.section_id,a.awake_if_not_active)),!_.isArray(a.candidates))throw new Error("processSilentUpdates : the update candidates must be an array.");if(_.isEmpty(a.candidates))return d.resolve([]).promise();var e=function(){b.silentlyUpdateSettings(a.candidates,a.refresh).fail(function(){d.reject()}).done(function(c){_.delay(function(){b.setupActiveSkopedControls({section_id:a.section_id})},1e3),d.resolve(c)})};return"pending"==api.czr_skopeReady.state()?(d.resolve([]),api.czr_skopeReady.done(function(){e()})):e(),d.promise()},silentlyUpdateSettings:function(a,b){api.state.has("silent-update-processing")||api.state.create("silent-update-processing")(!1),api.state("silent-update-processing")(!0);var c=this,d={},e=$.Deferred();b=!!_.isUndefined(b)||b,(_.isUndefined(a)||_.isEmpty(a))&&(a=c._getSilentUpdateCandidates()),_.isString(a)&&(a=[a]),_.each(a,function(a){api.control.has(a)&&"czr_multi_module"==api.control(a).params.type||(d[a]=c.getSettingUpdatePromise(a))});var f=[],g=[];return _.each(d,function(a,b){a.done(function(a){var c=api.CZR_Helpers.build_setId(b),d=api.czr_skope(api.czr_activeSkopeId()).getSkopeSettingDirtyness(b);_.isEqual(api(c)(),a)||g.push(b),api(c).silent_set(a,d)}),f.push(a)}),$.when.apply(null,f).fail(function(){throw e.reject(),new Error("silentlyUpdateSettings FAILED. Candidates : "+a)}).always(function(){api.state("silent-update-processing")(!1)}).then(function(){_.each(f,function(b){if(_.isObject(b)&&"resolved"!==b.state())throw new Error("a silent update promise is unresolved : "+a)}),b&&!_.isEmpty(g)?api.previewer.refresh().always(function(){e.resolve(g)}):e.resolve(g)}),e.promise()},getSettingUpdatePromise:function(a){if(_.isUndefined(a))throw new Error("getSettingUpdatePromise : the provided setId is not defined");if(!api.has(api.CZR_Helpers.build_setId(a)))throw new Error("getSettingUpdatePromise : the provided wpSetId is not registered : "+api.CZR_Helpers.build_setId(a));var b=this,c=api.CZR_Helpers.build_setId(a),d=api(c)(),e=$.Deferred(),f=!1,g=api.czr_activeSkopeId(),h=api.czr_skopeBase.getSkopeSettingVal(a,g);if(_.isEqual(d,h))return e.resolve(h).promise();if(api.control.has(c)){var i=api.control(c).params.type,j=api.settings.controls[c];switch(i){case"czr_cropped_image":f=b._getCzrCroppedImagePromise(c,j);break;case"czr_module":b._processCzrModuleSilentActions(c,i,g,j)}}return _.has(api.settings.controls,"header_image")&&"header_image"==c&&(f=b._getHeaderImagePromise(c,g)),f&&_.isObject(f)?f.always(function(){e.resolve(h)}):e.resolve(h),e.promise()},_getSilentUpdateCandidates:function(a,b){var c=this,d=[];if(a=_.isUndefined(a)||_.isNull(a)?api.czr_activeSectionId():a,_.isEmpty(api.czr_activeSectionId())&&!b)return[];if(_.isUndefined(a))return api.consoleLog("_getSilentUpdateCandidates : No active section provided"),[];if(!api.section.has(a))throw new Error("_getSilentUpdateCandidates : The section "+a+" is not registered in the API.");var e=api.CZR_Helpers.getSectionSettingIds(a);return e=_.filter(e,function(a){return c.isSettingSkopeEligible(a)}),_.each(e,function(a){d.push(a)}),d}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{_processCzrModuleSilentActions:function(a,b,c,d){var e,f,g,h,i,j=_.has(api.control(a).params,"syncCollection")?api.control(a).params.syncCollection:"",k=api.CZR_Helpers.build_setId(a),l=api.czr_skopeBase.getSkopeSettingVal(k,c),m=api.czr_skope(api.czr_activeSkopeId());_.isEmpty(j)||_.isUndefined(j)||(e=api.CZR_Helpers.build_setId(j),f=api.czr_skopeBase.getSkopeSettingVal(e,c),g=api.settings.controls[e],h=api.controlConstructor.czr_multi_module,i=api.control(e).syncSektionModule()().id,api.control(e).container.remove(),api.control.remove(e),api(e).silent_set(f,m.getSkopeSettingDirtyness(e)),$.extend(g,{czr_skope:c}),api.control.add(e,new h(e,{params:g,previewer:api.previewer}))),_constructor=api.controlConstructor[b],api.control(a).container.remove(),api.control.remove(a),api(a).silent_set(l,m.getSkopeSettingDirtyness(k)),$.extend(d,{czr_skope:c}),api.control.add(a,new _constructor(a,{params:d,previewer:api.previewer})),_.isEmpty(j)||_.isUndefined(j)||(api.consoleLog("FIRE SEKTION MODULE?",i,api.control(a).czr_Module(i).isReady.state()),api.control(a).czr_Module(i).fireSektionModule())},_getCzrCroppedImagePromise:function(a,b){var c=api.controlConstructor.czr_cropped_image,d=$.Deferred(),e=api.has(a)?api(a)():null;return e=null===e?"":e,wp.media.attachment(e).fetch().done(function(){api.control(a).container.remove(),api.control.remove(a),b.attachment=this.attributes,api.control.add(a,new c(a,{params:b,previewer:api.previewer})),d.resolve()}).fail(function(){api.control(a).container.remove(),api.control.remove(a),b=_.omit(b,"attachment"),api.control.add(a,new c(a,{params:b,previewer:api.previewer})),d.reject()}),d.promise()},_getHeaderImagePromise:function(a,b){var c=$.Deferred();if(!_.has(api.settings.controls,"header_image")||"header_image"!=a)return c.resolve().promise();var d=api.controlConstructor.header,e=$.extend(!0,{},api.settings.controls.header_image);header_image_data=null===api.czr_skopeBase.getSkopeSettingVal("header_image_data",b)?"":api.czr_skopeBase.getSkopeSettingVal("header_image_data",b);var f,g=function(a){a=a||e,api.control("header_image").container.remove(),api.control.remove("header_image"),api.HeaderTool.UploadsList=api.czr_HeaderTool.UploadsList,api.HeaderTool.DefaultsList=api.czr_HeaderTool.DefaultsList,api.HeaderTool.CombinedList=api.czr_HeaderTool.CombinedList;var b=function(){api.control.add("header_image",new d("header_image",{params:a,previewer:api.previewer}))};(b=_.debounce(b,800))()};return _.has(header_image_data,"attachment_id")?(f=header_image_data.attachment_id,wp.media.attachment(f).fetch().done(function(){e.attachment=this.attributes,g(e),c.resolve()}).fail(function(){e=_.omit(e,"attachment"),api.control("header_image").container.remove(),api.control.remove("header_image"),api.HeaderTool.UploadsList=api.czr_HeaderTool.UploadsList,api.HeaderTool.DefaultsList=api.czr_HeaderTool.DefaultsList,api.HeaderTool.CombinedList=api.czr_HeaderTool.CombinedList,api.control.add("header_image",new d("header_image",{params:e,previewer:api.previewer})),c.reject()})):(g(),c.resolve()),c.promise()}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{setupActiveSkopedControls:function(a){var b,c,d,e,f=this;if(defaultSetupParams={controls:[],section_id:api.czr_activeSectionId()},d=$.extend(defaultSetupParams,a),!_.isObject(d)||!_.has(d,"controls")||!_.has(d,"section_id"))throw new Error("SetupControlsReset : the setupParams param must be an object with properties controls and section_id.");b=d.section_id,c=d.controls,e=[],!_.isEmpty(b)&&_.isString(b)||(b=api.czr_activeSectionId()),_.isEmpty(c)&&(c=api.CZR_Helpers.getSectionControlIds(b)),c=_.isString(c)?[c]:c,e=_.filter(c,function(a){var b=api.CZR_Helpers.getControlSettingId(a);return b&&!f.isSettingSkopeEligible(b)&&api.control(a).container.addClass("czr-not-skoped"),b&&f.isWPAuthorizedSetting(b)&&api.control(a).container.addClass("is-wp-authorized-setting"),b&&f.isSettingSkopeEligible(b)}),"nav_menu["!=b.substring(0,"nav_menu[".length)&&(_.isEmpty(c)||api.czr_skopeReady.then(function(){$.when(f.renderControlsSingleReset(e)).done(function(){f.listenSkopedControls(c)})}),f.renderCtrlSkpNotIcon(c))},listenSkopedControls:function(a){var b=this;_.each(a,function(a){if(api.has(a)&&!_.isUndefined(api.control(a))){var c=api.control(a),d=api.CZR_Helpers.getControlSettingId(a),e=(api.CZR_Helpers.getOptionName(d),{hasDBVal:!1,isDirty:!1,noticeVisible:!1,resetVisible:!1,isResetting:!1});_.has(c,"czr_states")||(c.czr_states=new api.Values,_.each(e,function(a,b){c.czr_states.create(b)}),b.bindControlStates(c)),c.czr_states("hasDBVal")(api.czr_skope(api.czr_activeSkopeId()).hasSkopeSettingDBValues(d)),c.czr_states("isDirty")(api.czr_skope(api.czr_activeSkopeId()).getSkopeSettingDirtyness(d)),_.has(c,"userEventMap")||(c.userEventMap=[{trigger:"click keydown",selector:".czr-setting-reset, .czr-cancel-button",name:"control_reset_warning",actions:function(){(c.czr_states("isDirty")()||c.czr_states("hasDBVal")())&&(_.each(_.without(api.CZR_Helpers.getSectionControlIds(c.section()),a),function(a){_.has(api.control(a),"czr_states")&&api.control(a).czr_states("resetVisible")(!1)}),c.czr_states("resetVisible")(!c.czr_states("resetVisible")()),c.czr_states("resetVisible")()&&c.czr_states("noticeVisible")(!1))}},{trigger:"click keydown",selector:".czr-control-do-reset",name:"control_do_reset",actions:function(){b.doResetSetting(a)}},{trigger:"click keydown",selector:".czr-skope-switch",name:"control_skope_switch",actions:function(a){var b=$(a.dom_event.currentTarget,a.dom_el).attr("data-skope-id");!_.isEmpty(b)&&api.czr_skope.has(b)&&api.czr_activeSkopeId(b)}},{trigger:"click keydown",selector:".czr-toggle-notice",name:"control_toggle_notice",actions:function(a){c.czr_states("noticeVisible")(!c.czr_states("noticeVisible")()),c.czr_states("noticeVisible")()&&c.czr_states("resetVisible")(!1)}}],api.CZR_Helpers.setupDOMListeners(c.userEventMap,{dom_el:c.container},b))}})},bindControlStates:function(a){if(!api.control.has(a.id))throw new Error("in bindControlStates, the provided ctrl id is not registered in the api : "+a.id);var b=this;api.CZR_Helpers.getControlSettingId(a.id);a.czr_states("hasDBVal").bind(function(b){a.container.toggleClass("has-db-val",b),b?_title="Reset your customized ( and published ) value":a.czr_states("isDirty")()?_title="Reset your customized ( but not yet published ) value":_title="Not customized yet, nothing to reset",a.container.find(".czr-setting-reset").attr("title",_title)}),a.czr_states("isDirty").bind(function(b){a.container.toggleClass("is-dirty",b);var c;c=b?"Reset your customized ( but not yet published ) value":a.czr_states("hasDBVal")()?"Reset your customized ( and published ) value":"Not customized yet, nothing to reset",a.container.find(".czr-setting-reset").attr("title",c)}),a.czr_states("noticeVisible").bind(function(c){a.container.toggleClass("czr-notice-visible",c);var d=a.getNotificationsContainerElement();!1!==d&&!1!==d.length&&(c?(b.updateCtrlSkpNot(a.id),d.stop().slideDown("fast",null,function(){$(this).css("height","auto")})):$.when(d.stop().slideUp("fast",null,function(){$(this).css("height","auto")})).done(function(){b.removeCtrlSkpNot(a.id)}))}),a.czr_states("resetVisible").bind(function(c){a.section()||api.czr_activeSectionId();c?$.when(b.renderControlResetWarningTmpl(a.id)).done(function(b){_.isEmpty(b)||(a.czr_resetDialogContainer=b.container,b.container.slideToggle("fast"),b.is_authorized||_.delay(function(){$.when(a.czr_resetDialogContainer.slideToggle("fast")).done(function(){a.czr_resetDialogContainer.remove()})},3e3))}):_.has(a,"czr_resetDialogContainer")&&a.czr_resetDialogContainer.length&&$.when(a.czr_resetDialogContainer.slideToggle("fast")).done(function(){a.czr_resetDialogContainer.remove()})})}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{renderControlsSingleReset:function(a){var b=this,c=$.Deferred();(_.isUndefined(a)||_.isEmpty(a))&&(a=api.CZR_Helpers.getSectionControlIds(api.czr_activeSectionId()),a=_.filter(a,function(a){var c=api.CZR_Helpers.getControlSettingId(a);return c&&b.isSettingSkopeEligible(c)}));var d=_.isArray(a)?a:[a],e=function(a){return _.isEmpty(a)?void c.resolve():void _.each(a,function(a){api.control.when(a,function(){var b=api.control(a);api.CZR_Helpers.getControlSettingId(a);return $(".czr-setting-reset",b.container).length?void c.resolve():void b.deferred.embedded.then(function(){$.when(b.container.find(".customize-control-title").first().prepend($("<span/>",{"class":"czr-setting-reset fa fa-refresh",title:""}))).done(function(){b.container.addClass("czr-skoped"),$(".czr-setting-reset",b.container).fadeIn(400),c.resolve()})})})})};return e=_.debounce(e,200),e(d),c.promise()},renderControlResetWarningTmpl:function(a){if(!api.control.has(a))return{};var b,c,d=api.control(a),e=api.CZR_Helpers.getControlSettingId(a),f="",g=function(){return!!_.contains(serverControlParams.wpBuiltinSettings,api.CZR_Helpers.getOptionName(e))||!_.contains(serverControlParams.themeSettingList,api.CZR_Helpers.getOptionName(e))}();d.czr_states("isDirty")()?(b=["Please confirm that you want to reset your current customizations for this option in ",api.czr_skope(api.czr_activeSkopeId())().title,"."].join(""),c="Your customizations have been reset."):g&&"global"==api.czr_skope(api.czr_activeSkopeId())().skope?b="This WordPress setting can not be reset site wide.":(b=["Please confirm that you want to reset this option in ",api.czr_skope(api.czr_activeSkopeId())().title,"."].join(""),c="The options have been reset.");var h=!(g&&"global"==api.czr_skope(api.czr_activeSkopeId())().skope&&!d.czr_states("isDirty")()),i={warning_message:b,success_message:c,is_authorized:h};try{f=wp.template("czr-reset-control")(i)}catch(j){throw new Error("Error when parsing the the reset control template : "+j)}return $(".customize-control-title",d.container).first().after($(f)),{container:$(".czr-ctrl-reset-warning",d.container),is_authorized:h}},doResetSetting:function(a){var b=this,c=api.CZR_Helpers.getControlSettingId(a),d=api.control(a),e=api.czr_activeSkopeId(),f=d.czr_states("isDirty")()?"_resetControlDirtyness":"_resetControlAPIVal",g=function(a,b){b=!_.isUndefined(b)&&b,a.czr_states("resetVisible")(!1),a.czr_states("isResetting")(!1),a.container.removeClass("czr-resetting-control")},h=function(a){var h=function(){api.czr_skopeBase.processSilentUpdates({candidates:a,refresh:!1}).fail(function(){api.consoleLog("Silent update failed after resetting control : "+a)}).done(function(){$.when($(".czr-crtl-reset-dialog",d.container).fadeOut("300")).done(function(){$.when($(".czr-reset-success",d.container).fadeIn("300")).done(function(c){_.delay(function(){$.when(c.fadeOut("300")).done(function(){g(d),b.setupActiveSkopedControls({controls:[a]}),_.delay(function(){d.czr_states("noticeVisible")(!0)},300),_.delay(function(){d.czr_states("noticeVisible")(!1)},4e3)})},1e3)})})})};b[f](a).done(function(){api.consoleLog("REFRESH AFTER A SETTING RESET"),api.previewer.refresh().fail(function(a){api.consoleLog("SETTING RESET REFRESH FAILED",a)}).done(function(a){if("global"==api.czr_skope(e)().skope&&"_resetControlAPIVal"==f){var b,d={},g=api.czr_skope(e)().opt_name;!_.isUndefined(a.skopesServerData)&&_.has(a.skopesServerData,"czr_skopes")&&(b=a.skopesServerData.czr_skopes,_.isUndefined(_.findWhere(b,{opt_name:g}))&&(d=_.findWhere(b,{opt_name:g}).db||{})),api.czr_skopeBase.maybeSynchronizeGlobalSkope({isGlobalReset:!0,isSetting:!0,settingIdToReset:c}).done(function(){h()})}else h()})})};d.czr_states("isResetting")(!0),d.container.addClass("czr-resetting-control"),api.czr_skopeReset[d.czr_states("isDirty")()?"resetChangeset":"resetPublished"]({skope_id:e,setId:c,is_setting:!0}).done(function(b){h(a)}).fail(function(c){$.when($(".czr-crtl-reset-dialog",d.container).fadeOut("300")).done(function(){$.when($(".czr-reset-fail",d.container).fadeIn("300")).done(function(){$(".czr-reset-fail",d.container).append("<p>"+c+"</p>"),_.delay(function(){g(d),b.setupActiveSkopedControls({controls:[a]})},2e3)})})})},_resetControlDirtyness:function(a){var b=api.CZR_Helpers.getControlSettingId(a),c=api.czr_skope(api.czr_activeSkopeId()),d=$.extend(!0,{},c.dirtyValues()),e={},f=$.extend(!0,{},c.changesetValues()),g={},h=$.Deferred();return e=_.omit(d,b),g=_.omit(f,b),c.dirtyValues(e),c.changesetValues(e),h.resolve().promise()},_resetControlAPIVal:function(a){var b=api.CZR_Helpers.getControlSettingId(a),c=api.czr_skope(api.czr_activeSkopeId()).dbValues(),d=$.extend(!0,{},c),e=$.Deferred();return _.has(api.control(a),"czr_states")&&(api.control(a).czr_states("hasDBVal")(!1),api.czr_skope(api.czr_activeSkopeId()).dbValues(_.omit(d,b))),e.resolve().promise()}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{renderCtrlSkpNotIcon:function(a){var b=_.isArray(a)?a:[a];_.each(b,function(a){api.control.when(a,function(){var b=api.control(a);b.deferred.embedded.then(function(){$(".czr-toggle-notice",b.container).length||$.when(b.container.find(".customize-control-title").first().append($("<span/>",{"class":"czr-toggle-notice fa fa-info-circle",title:"Display informations about the scope of this option."}))).done(function(){$(".czr-toggle-notice",b.container).fadeIn(400)})})})})},updateCtrlSkpNot:function(a){var b=this,c=_.isArray(a)?a:[a],d=function(a){return a&&b.isSettingSkopeEligible(a)},e=function(a,c){var e,f,g=api.czr_activeSkopeId(),h=b.getInheritedSkopeId(a,g),i=b.getAppliedPrioritySkopeId(a,g),j=[];
return d(a)?(h==i&&api.czr_skope.has(h)&&g==h&&(e=!_.isUndefined(api.czr_skope(g).dirtyValues()[a]),f=!_.isUndefined(api.czr_skope(g).dbValues()[a]),e?"global"==api.czr_skope(h)().skope?j.push(["Customized. Will be published site wide."].join(" ")):j.push(["Customized. Will be published for :",api.czr_skope(h)().title].join(" ")):f?"global"==api.czr_skope(h)().skope?j.push(["Customized and published site wide."].join(" ")):j.push(["Customized and published for :",api.czr_skope(h)().title].join(" ")):j.push("Default website value published site wide.")),h!==g&&api.czr_skope.has(h)&&(e=!_.isUndefined(api.czr_skope(h).dirtyValues()[a]),f=!_.isUndefined(api.czr_skope(h).dbValues()[a]),e||f?j.push("Inherited from : "+b.buildSkopeLink(h)):j.push("Default website value")),i!==g&&api.czr_skope.has(i)&&(e=!_.isUndefined(api.czr_skope(i).dirtyValues()[a]),j.push([e?"The value that will be published for":"The value currently published for",api.czr_skope(c)().title,e?"is customized in scope :":"is set in scope :",b.buildSkopeLink(i),e?", and will override this one once published because it has a higher priority.":", because it has a higher priority than this one."].join(" "))),j.join(" | ")):(j.push(["This option is always customized site wide and can't be reset."].join(" ")),j.join(" | "))};_.each(c,function(a){api.control.when(a,function(){var b=api.control(a),c=api.CZR_Helpers.getControlSettingId(a);_.has(b,"czr_states")&&b.czr_states("noticeVisible")()&&b.deferred.embedded.then(function(){var a=_.findWhere(api.czr_currentSkopesCollection(),{skope:"local"}).id,d=b.getNotificationsContainerElement();if(d&&d.length&&!_.isUndefined(a)){_html=e(c,a);var f=$(".czr-skope-notice",d);f.length?f.html(_html):d.append(['<span class="czr-notice czr-skope-notice">',_html,"</span>"].join(""))}})})})},removeCtrlSkpNot:function(a){var b=_.isArray(a)?a:[a];_.each(b,function(a){api.control.when(a,function(){var b=api.control(a);b.deferred.embedded.then(function(){var a=b.getNotificationsContainerElement();if(a&&a.length){var c=$(".czr-skope-notice",a);c.length&&c.remove()}})})})}});var CZRSkopeSaveMths=CZRSkopeSaveMths||{};$.extend(CZRSkopeSaveMths,{initialize:function(){this.changesetStatus="publish",this.saveBtn=$("#save")},save:function(a){var b,c=this,d=api.state("processing"),e=new api.Messenger({url:api.settings.url.parent,channel:"loader"});c.globalSaveDeferred=$.Deferred(),c.previewer=api.previewer,c.globalSkopeId=api.czr_skopeBase.getGlobalSkopeId(),c.saveArgs=a,a&&a.status&&(c.changesetStatus=a.status),api.state("saving")()&&c.globalSaveDeferred.reject("already_saving");var f=function(a,b){api.state("saving")(!1),api.state("processing").set(0),c.saveBtn.prop("disabled",!1),!_.isUndefined(a)&&a.setting_validities&&api._handleSettingValidities({settingValidities:a.setting_validities,focusInvalidControl:!0}),"pending"==b&&api.czr_serverNotification({message:a,status:"error"})},g=function(a){var b,d=$.Deferred();return api.state("saving")(!0),c.fireAllSubmission(a).always(function(a){b=a.response,f(b,this.state())}).fail(function(a){b=a.response,api.consoleLog("ALL SUBMISSIONS FAILED",b),c.globalSaveDeferred.reject(b),api.trigger("error",b),d.resolve(a.hasNewMenu)}).done(function(a){b=a.response,api.previewer.refresh({waitSkopeSynced:!0}).fail(function(a){c.globalSaveDeferred.reject(c.previewer,[b]),api.consoleLog("SAVE REFRESH FAIL",a)}).done(function(f){if(api.previewer.send("saved",b),b=_.extend({changeset_status:"publish"},b||{}),api.czr_isChangeSetOn()){var g=api._latestRevision;api.state("changesetStatus").set(b.changeset_status),"publish"===b.changeset_status&&(api.each(function(a){a._dirty&&(_.isUndefined(api._latestSettingRevisions[a.id])||api._latestSettingRevisions[a.id]<=g)&&(a._dirty=!1)}),api.state("changesetStatus").set(""),api.settings.changeset.uuid=b.next_changeset_uuid,e.send("changeset-uuid",api.settings.changeset.uuid))}else api.each(function(a){a._dirty=!1});f=_.extend({previewer:f.previewer||c.previewer,skopesServerData:f.skopesServerData||{}},f),c.reactWhenSaveDone(f.skopesServerData),c.globalSaveDeferred.resolveWith(c.previewer,[b]),api.trigger("saved",b||{}),d.resolve(a.hasNewMenu)})}),d.promise()};return 0===d()?g().done(function(a){a&&g({saveGlobal:!1,saveSkopes:!0})}):(b=function(){0===d()&&(api.state.unbind("change",b),g())},api.state.bind("change",b)),c.globalSaveDeferred.promise()}});var CZRSkopeSaveMths=CZRSkopeSaveMths||{};$.extend(CZRSkopeSaveMths,{getSubmitPromise:function(a){var b=this,c=$.Deferred(),d={};if(_.isEmpty(a)||!api.czr_skope.has(a))return api.consoleLog("getSubmitPromise : no skope id requested OR skope_id not registered : "+a),c.resolve().promise();var e=api.czr_skope(a)();return api.czr_skope(a).dirtyness()||a===b.globalSkopeId?(_.each(api.czr_skopeBase.getSkopeDirties(a),function(a,b){d[b]=_.extend({value:a})}),this.submit({skope_id:a,customize_changeset_data:d,dyn_type:e.dyn_type}).done(function(a){c.resolve(a)}).fail(function(b){api.consoleLog("GETSUBMIT FAILED PROMISE FOR SKOPE : ",a,b),c.reject(b)}),c.promise()):c.resolve().promise()},submit:function(a){var b,c=this,d={skope_id:null,the_dirties:{},customize_changeset_data:{},dyn_type:null,opt_name:null},e=[],f=$.Deferred();if(a=$.extend(d,a),_.isNull(a.skope_id))throw new Error("OVERRIDEN SAVE::submit : MISSING skope_id");if(_.isNull(a.the_dirties))throw new Error("OVERRIDEN SAVE::submit : MISSING the_dirties");if(_.has(api,"Notification")&&(api.each(function(a){a.notifications.each(function(b){"error"===b.type&&api.consoleLog("NOTIFICATION ERROR on SUBMIT SAVE",b),"error"!==b.type||b.data&&b.data.from_server||(e.push(a.id),settingInvalidities[a.id]||(settingInvalidities[a.id]={}),settingInvalidities[a.id][b.code]=b)})}),b=api.findControlsForSettings(e),!_.isEmpty(b)))return _.values(b)[0][0].focus(),f.rejectWith(c.previewer,[{setting_invalidities:settingInvalidities}]).promise();var g={skope_id:a.skope_id,action:"save",the_dirties:a.the_dirties,dyn_type:a.dyn_type,opt_name:a.opt_name};api.czr_isChangeSetOn()&&$.extend(g,{excludeCustomizedSaved:!1});var h=$.extend(c.previewer.query(g),{nonce:c.previewer.nonce.save,customize_changeset_status:c.changesetStatus,customize_changeset_data:JSON.stringify(a.customize_changeset_data)});api.czr_isChangeSetOn()&&(c.saveArgs&&c.saveArgs.date&&(h.customize_changeset_date=c.saveArgs.date),c.saveArgs&&c.saveArgs.title&&(h.customize_changeset_title=c.saveArgs.title));var i=wp.ajax.post("global"!==h.skope?"customize_skope_changeset_save":"customize_save",h);return c.saveBtn.prop("disabled",!0),api.trigger("save",i),i.fail(function(b){api.consoleLog("SUBMIT REQUEST FAIL",a.skope_id,b),"0"===b?b="not_logged_in":"-1"===b&&(b="invalid_nonce"),"invalid_nonce"===b?c.previewer.cheatin():"not_logged_in"===b&&(c.previewer.preview.iframe.hide(),c.previewer.login().done(function(){c.previewer.save(),c.previewer.preview.iframe.show()})),api.trigger("error",b),f.reject(b)}),i.done(function(a){f.resolve(a)}),f.promise()}});var CZRSkopeSaveMths=CZRSkopeSaveMths||{};$.extend(CZRSkopeSaveMths,{fireAllSubmission:function(a){var b=this,c=$.Deferred(),d=[],e=$.Deferred(),f={},g=[],h=[],i={saveGlobal:!0,saveSkopes:!0};a=$.extend(i,a),_.each(api.czr_skopeCollection(),function(a){"global"!==a.skope&&d.push(a.id)});var j=function(a){if(_.isUndefined(d[a+1])&&g.length==d.length){if(_.isEmpty(h))e.resolve(f);else{var b=function(){var a=[];return _.each(h,function(b){a.push(api.czr_skopeBase.buildServerResponse(b))}),$.trim(a.join(" | "))};e.reject(b())}return!0}},k=function(a){return a=a||0,_.isUndefined(d[a])&&(api.consoleLog("Undefined Skope in Save recursive call ",a,_skopesToUpdate,_skopesToUpdate[a]),e.resolve(f)),b.getSubmitPromise(d[a]).always(function(){g.push(a)}).fail(function(b){h.push(b),api.consoleLog("RECURSIVE PUSH FAIL FOR SKOPE : ",d[a]),j(a)||k(a+1)}).done(function(b){b=b||{},f=_.isEmpty(f)?b||{}:$.extend(f,b),j(a)||k(a+1)}),e.promise()},l=!1;_.each(api.czr_skope("global__all_").dirtyValues(),function(a,b){"nav_menu["==b.substring(0,"nav_menu[".length)&&(l=!0)});var m=function(){b.getSubmitPromise(b.globalSkopeId).fail(function(a){api.consoleLog("GLOBAL SAVE SUBMIT FAIL",a),a=api.czr_skopeBase.buildServerResponse(a),c.reject(a)}).done(function(a){f=_.isEmpty(f)?a||{}:$.extend(f,a),c.resolve({response:f,hasNewMenu:l})})};return l&&a.saveGlobal?m():a.saveGlobal&&a.saveSkopes?k().fail(function(a){api.consoleLog("RECURSIVE SAVE CALL FAIL",a),c.reject(a)}).done(function(a){b.cleanSkopeChangesetMetas().always(function(){m()})}):a.saveGlobal&&!a.saveSkopes?m():!a.saveGlobal&&a.saveSkopes&&k().fail(function(a){api.consoleLog("RECURSIVE SAVE CALL FAIL",a),c.reject(a)}).done(function(a){f=_.isEmpty(f)?a||{}:$.extend(f,a),b.cleanSkopeChangesetMetas().always(function(){c.resolve({response:f,hasNewMenu:l})})}),c.promise()},cleanSkopeChangesetMetas:function(){var a=$.Deferred();return _query=$.extend(api.previewer.query(),{nonce:api.previewer.nonce.save}),wp.ajax.post("czr_clean_skope_changeset_metas_after_publish",_query).always(function(){a.resolve()}).fail(function(a){api.consoleLog("cleanSkopeChangesetMetas failed",_query,a)}).done(function(a){api.consoleLog("cleanSkopeChangesetMetas done",_query,a)}),a.promise()}});var CZRSkopeSaveMths=CZRSkopeSaveMths||{};$.extend(CZRSkopeSaveMths,{reactWhenSaveDone:function(a){var b={};a=_.extend({czr_skopes:[],isChangesetDirty:!1},a),_.each(api.czr_skopeCollection(),function(a){b[a.opt_name]=api.czr_skopeBase.getSkopeDirties(a.id),api.czr_skope(a.id).dirtyValues({}),api.czr_skope(a.id).changesetValues({})});var c=[],d=a.czr_skopes;api.consoleLog("REACT WHEN SAVE DONE",b,d),_.each(b,function(a,b){_.each(a,function(a,e){if(!_.isUndefined(_.findWhere(d,{opt_name:b}))&&api.czr_skopeBase.isSettingSkopeEligible(e)){var f=_.findWhere(d,{opt_name:b}).db,g=_.findWhere(d,{opt_name:b}).skope,h=api.CZR_Helpers.build_setId(e),i=api.CZR_Helpers.getOptionName(e),j=f[h];_.isUndefined(j)&&"global"==g&&a===serverControlParams.defaultOptionsValues[i]||!_.isUndefined(j)&&_.isEqual(j,a)||c.push({opt_name:b,setId:h,server_val:j,api_val:a})}})}),_.isEmpty(c)?api.consoleLog("ALL RIGHT, SERVER AND API ARE SYNCHRONIZED AFTER SAVE"):api.consoleLog("SOME SETTINGS HAVE NOT BEEN PROPERLY SAVED : ",c),api.czr_skopeBase.maybeSynchronizeGlobalSkope(),api.czr_skopeBase.updateCtrlSkpNot(api.CZR_Helpers.getSectionControlIds())}});var CZRSkopeResetMths=CZRSkopeResetMths||{};$.extend(CZRSkopeResetMths,{initialize:function(){var a=this;a.previewer=api.previewer,api.state.create("czr-resetting")(!1),api.state("czr-resetting").bind(function(a){$(document.body).toggleClass("czr-resetting",!1!==a)})},resetChangeset:function(a){var b,c,d,e,f,g=$.Deferred(),h=this,i=api.state("processing"),j={is_setting:!1,is_skope:!1,skope_id:api.czr_activeSkopeId()||"",setId:""};a=_.extend(j,a);var k=a.skope_id,l=a.setId;return api.czr_isChangeSetOn()?(c=function(b,c){if(_.isUndefined(b))throw new Error("RESET: MISSING skope_id");if(api.state("czr-resetting")(!0),e={skope_id:b,action:"reset"},f=$.extend(h.previewer.query(e),{nonce:h.previewer.nonce.save}),a.is_setting)$.extend(f,{setting_id:c}),d="czr_changeset_setting_reset";else{if(!a.is_skope)return g.reject("reset_ajax_action_not_specified").promise();d="czr_changeset_skope_reset"}wp.ajax.post(d,f).always(function(){api.state("czr-resetting")(!1)}).fail(function(b){"0"===b?b="not_logged_in":"-1"===b&&(b="invalid_nonce"),"invalid_nonce"===b?h.previewer.cheatin():"not_logged_in"===b&&(h.previewer.preview.iframe.hide(),h.previewer.login().done(function(){h.resetChangeset(a),h.previewer.preview.iframe.show()})),api.consoleLog(d+" failed ",f,b),b=api.czr_skopeBase.buildServerResponse(b),api.trigger("error",b),api.czr_serverNotification({message:b,status:"error"}),g.reject(b)}).done(function(a){g.resolve(a)})},0===i()&&!1===api.state("czr-resetting")()?c(k,l):(b=function(){0===i()&&!1===api.state("czr-resetting")()&&(api.state.unbind("change",b),c(k,l))},api.state.bind("change",b)),g.promise()):g.resolve().promise()},resetPublished:function(a){var b,c,d,e,f,g=$.Deferred(),h=this,i=api.state("processing"),j={is_setting:!1,is_skope:!1,skope_id:api.czr_activeSkopeId()||"",setId:""};a=_.extend(j,a);var k=a.skope_id,l=a.setId;return c=function(b,c){if(_.isUndefined(b))throw new Error("RESET: MISSING skope_id");if(api.state("czr-resetting")(!0),e={skope_id:b,action:"reset"},f=$.extend(h.previewer.query(e),{nonce:h.previewer.nonce.save}),a.is_setting)$.extend(f,{setting_id:c}),d="czr_published_setting_reset";else{if(!a.is_skope)return g.reject("reset_ajax_action_not_specified").promise();d="czr_published_skope_reset"}api.consoleLog("in czr_reset submit : ",b,f),wp.ajax.post(d,f).always(function(){api.state("czr-resetting")(!1)}).fail(function(b){"0"===b?b="not_logged_in":"-1"===b&&(b="invalid_nonce"),"invalid_nonce"===b?h.previewer.cheatin():"not_logged_in"===b&&(h.previewer.preview.iframe.hide(),h.previewer.login().done(function(){h.resetChangeset(a),h.previewer.preview.iframe.show()})),api.consoleLog(d+" failed ",f,b),b=api.czr_skopeBase.buildServerResponse(b),api.trigger("error",b),api.czr_serverNotification({message:b,status:"error"}),g.reject(b)}).done(function(a){g.resolve(a)})},0===i()&&!1===api.state("czr-resetting")()?c(k,l):(b=function(){0===i()&&!1===api.state("czr-resetting")()&&(api.state.unbind("change",b),c(k,l))},api.state.bind("change",b)),g.promise()}});var CZRSkopeBaseMths=CZRSkopeBaseMths||{};$.extend(CZRSkopeBaseMths,{initWidgetSidebarSpecifics:function(){var a=this;a.isExcludedSidebarsWidgets()||api.czr_activeSkopeId.bind(function(b){a.forceSidebarDirtyRefresh(api.czr_activeSectionId(),b)}),$(document).bind("widget-added",function(b,c){if(!a.isExcludedSidebarsWidgets()){var d=c.closest(".customize-control").attr("id"),e=api.czr_skopeBase.widgetIdToSettingId(d,"customize-control-");if(!api.has(e))throw new Error("AN ADDED WIDGET COULD NOT BE BOUND IN SKOPE. "+e);a.listenAPISettings(e)}})},forceSidebarDirtyRefresh:function(a,b){var c=this;if(!c.isExcludedSidebarsWidgets()){var d=api.state("saved")(),e=function(){if(api.section.has(a)&&"sidebar"==api.section(a).params.type){var b=b||api.czr_activeSkopeId(),e="sidebars_widgets["+api.section(a).params.sidebarId+"]",f=c.getSkopeSettingVal(e,b);api.czr_skope(b).updateSkopeDirties(e,f),api.previewer.refresh({the_dirties:api.czr_skope(b).dirtyValues()}).done(function(){api.state("saved")(d)})}};e=_.debounce(e,500),e()}}});var CZRSkopeMths=CZRSkopeMths||{};$.extend(CZRSkopeMths,{initialize:function(a,b){var c=this;api.Value.prototype.initialize.call(c,null,b),c.isReady=$.Deferred(),c.embedded=$.Deferred(),c.el="czr-scope-"+a,$.extend(c,b||{}),c.visible=new api.Value((!0)),c.winner=new api.Value((!1)),c.priority=new api.Value,c.active=new api.Value((!1)),c.dirtyness=new api.Value((!1)),c.skopeResetDialogVisibility=new api.Value((!1)),c.hasDBValues=new api.Value((!1)),c.dirtyValues=new api.Value({}),c.dbValues=new api.Value({}),c.changesetValues=new api.Value({}),c.userEventMap=new api.Value([{trigger:"click keydown",selector:".czr-scope-switch, .czr-skp-switch-link",name:"skope_switch",actions:function(){api.czr_activeSkopeId(c().id)}},{trigger:"click keydown",selector:".czr-scope-reset",name:"skope_reset_warning",actions:"reactOnSkopeResetUserRequest"}]),c.skopeResetDialogVisibility.bind(function(a,b){return c.skopeResetDialogReact(a)},{deferred:!0}),c.dirtyValues.callbacks.add(function(){return c.dirtyValuesReact.apply(c,arguments)}),c.changesetValues.callbacks.add(function(){return c.changesetValuesReact.apply(c,arguments)}),c.dbValues.callbacks.add(function(){return c.dbValuesReact.apply(c,arguments)}),c.callbacks.add(function(){return c.skopeReact.apply(c,arguments)}),c.set(_.omit(b,function(a,b){return _.contains(["db","changeset","has_db_val"],b)})),c.embedded.fail(function(){throw new Error("The container of skope "+c().id+" has not been embededd")}).done(function(){c.setupDOMListeners(c.userEventMap(),{dom_el:c.container}),c.visible.bind(function(a){c.container.toggle(a)}),c.active.callbacks.add(function(){return c.activeStateReact.apply(c,arguments)}),c.dirtyness.callbacks.add(function(){return c.dirtynessReact.apply(c,arguments)}),c.hasDBValues.callbacks.add(function(){return c.hasDBValuesReact.apply(c,arguments)}),c.winner.callbacks.add(function(){return c.winnerReact.apply(c,arguments)}),c.dirtyness(!_.isEmpty(b.changeset)),c.hasDBValues(!_.isEmpty(b.db)),c.winner(b.is_winner),c.isReady.resolve()})},ready:function(){var a=this;$.when(a.embedSkopeDialogBox()).done(function(b){!1!==b.length?(b.css("background-color",a.color),a.container=b,a.embedded.resolve(b)):a.embedded.reject()})},dirtyValuesReact:function(a,b){var c=this;c.dirtyness(!_.isEmpty(a)),api.czr_dirtyness(!_.isEmpty(a));var d=[];_.each(b,function(b,c){_.has(a,c)||d.push(c)}),c().id==api.czr_activeSkopeId()&&(_.each(d,function(a){_.has(api.control(a),"czr_states")&&api.control(a).czr_states("isDirty")(!1)}),_.each(a,function(a,b){_.has(api.control(b),"czr_states")&&api.control(b).czr_states("isDirty")(!0)}))},changesetValuesReact:function(a,b){var c=this,d=$.extend(!0,{},c.dirtyValues());c.dirtyValues($.extend(d,a))},dbValuesReact:function(a,b){var c=this;c.hasDBValues(!_.isEmpty("global"!=c().skope?a:_.omit(a,function(a,b){return!api.czr_skopeBase.isThemeSetting(b)})));var d=[];_.each(b,function(b,c){_.has(a,c)||d.push(c)}),c().id==api.czr_activeSkopeId()&&(_.each(d,function(a){_.has(api.control(a),"czr_states")&&api.control(a).czr_states("hasDBVal")(!1)}),_.each(a,function(a,b){_.has(api.control(b),"czr_states")&&api.control(b).czr_states("hasDBVal")(!0)}))},skopeReact:function(a,b){var c=this,d=[],e=[];api.czr_skopeBase.isSkopeRegisteredInCollection(a.id)?(d=$.extend(!0,[],api.czr_skopeCollection()),e=d,_.each(d,function(b,d){b.id==c().id&&(e[d]=a)}),api.czr_skopeCollection(e)):(d=$.extend(!0,[],api.czr_skopeCollection()),d.push(a),api.czr_skopeCollection(d))},activeStateReact:function(a,b){var c=this;c.container.toggleClass("inactive").toggleClass("active",a),$(".czr-scope-switch",c.container).toggleClass("fa-toggle-on",a).toggleClass("fa-toggle-off",!a)},dirtynessReact:function(a,b){var c=this;$.when(this.container.toggleClass("dirty",a)).done(function(){a?$(".czr-scope-reset",c.container).fadeIn("slow").attr("title",["Reset the current customizations for",c().title].join(" ")):c.hasDBValues()||$(".czr-scope-reset",c.container).fadeOut("fast")})},hasDBValuesReact:function(a,b){var c=this;$.when(c.container.toggleClass("has-db-val",a)).done(function(){a?$(".czr-scope-reset",c.container).fadeIn("slow").attr("title",["global"==c().skope?"Reset the theme options published site wide":"Reset your website published options for","global"==c().skope?"":c().title].join(" ")):c.dirtyness()||$(".czr-scope-reset",c.container).fadeOut("fast")})},winnerReact:function(a){var b=this;this.container.toggleClass("is_winner",a),a&&_.each(api.czr_currentSkopesCollection(),function(a){if(a.id!=b().id){var c=$.extend(!0,{},a);$.extend(c,{is_winner:!1}),api.czr_skope(a.id)(c)}})},updateSkopeDirties:function(a,b){var c=this;api.CZR_Helpers.getOptionName(a);if(!api.czr_skopeBase.isSettingSkopeEligible(a)&&"global"!=c().skope)return api.czr_skope(api.czr_skopeBase.getGlobalSkopeId()).updateSkopeDirties(a,b);var d=$.extend(!0,{},c.dirtyValues()),e={};return e[a]=b,c.dirtyValues.set($.extend(d,e)),c.dirtyValues()},getSkopeSettingDirtyness:function(a){var b=this;return b.getSkopeSettingAPIDirtyness(a)||b.getSkopeSettingChangesetDirtyness(a)},getSkopeSettingAPIDirtyness:function(a){var b=this;return _.has(b.dirtyValues(),api.CZR_Helpers.build_setId(a))},getSkopeSettingChangesetDirtyness:function(a){var b=this;return api.czr_isChangeSetOn()?_.has(b.changesetValues(),api.CZR_Helpers.build_setId(a)):b.getSkopeSettingAPIDirtyness(a)},hasSkopeSettingDBValues:function(a){var b=api.CZR_Helpers.build_setId(a);return!_.isUndefined(api.czr_skope(api.czr_activeSkopeId()).dbValues()[b])}});var CZRSkopeMths=CZRSkopeMths||{};$.extend(CZRSkopeMths,{embedSkopeDialogBox:function(){var a=this,b=$.extend(!0,{},a()),c="";if(!$("#customize-header-actions").find(".czr-scope-switcher").length)throw new Error("The skope switcher wrapper is not printed, the skope can not be embedded.");try{c=wp.template("czr-skope")(_.extend(b,{el:a.el}))}catch(d){throw new Error("Error when parsing the template of a skope"+d)}return $(".czr-skopes-wrapper","#customize-header-actions").append($(c)),$("."+a.el,".czr-skopes-wrapper")},renderResetWarningTmpl:function(){var a,b,c=this,d=$.extend(!0,{},c()),e="";c.dirtyness()?(a=["Please confirm that you want to reset your current customizations for : ",c().title,"."].join(""),b=["Your customizations have been reset for ",c().title,"."].join("")):(a=["Please confirm that you want to reset your published customizations to defaults for : ",c().title,"."].join(""),b=["The options have been reset to defaults for ",c().title,"."].join(""));try{e=wp.template("czr-skope-pane")(_.extend(d,{el:c.el,warning_message:a,success_message:b}))}catch(f){throw new Error("Error when parsing the the reset skope template : "+f)}return $("#customize-preview").after($(e)),$("#czr-skope-pane")},getEl:function(){var a=this;return $(a.el,"#customize-header-actions")}});var CZRSkopeMths=CZRSkopeMths||{};$.extend(CZRSkopeMths,{reactOnSkopeResetUserRequest:function(){var a=this,b=function(){api.state("czr-resetting")(!0),api.czr_activeSkopeId()!=a().id?api.czr_activeSkopeId(a().id).done(function(){a.skopeResetDialogVisibility(!a.skopeResetDialogVisibility()).done(function(){api.state("czr-resetting")(!1)})}):a.skopeResetDialogVisibility(!a.skopeResetDialogVisibility()).done(function(){api.state("czr-resetting")(!1)})};return api.state("czr-resetting")()||0!==api.state("processing")()?void api.czr_serverNotification({message:"Slow down, you move too fast !",status:"success",auto_collapse:!0}):void(api.czr_activeSkopeId()!=a().id&&api.czr_skope(api.czr_activeSkopeId()).skopeResetDialogVisibility()?api.czr_skope(api.czr_activeSkopeId()).skopeResetDialogVisibility(!1).done(function(){b()}):b())},skopeResetDialogReact:function(a){var b=this,c=$.Deferred();return b.userResetEventMap=b.userResetEventMap||new api.Value([{trigger:"click keydown",selector:".czr-scope-reset-cancel",name:"skope_reset_cancel",actions:function(){b.skopeResetDialogVisibility(!b.skopeResetDialogVisibility())}},{trigger:"click keydown",selector:".czr-scope-do-reset",name:"skope_do_reset",actions:"doResetSkopeValues"}]),a?(api.czr_isResettingSkope(b().id),$.when(b.renderResetWarningTmpl()).done(function(a){b.resetPanel=a,b.resetPanel.addClass(b.dirtyness()?"dirty-reset":"db-reset"),b.setupDOMListeners(b.userResetEventMap(),{dom_el:b.resetPanel})}).then(function(){setTimeout(function(){var a=$("#customize-preview").height();b.resetPanel.css("line-height",a+"px").css("height",a+"px"),$("body").addClass("czr-skope-pane-open")},50)})):$.when($("body").removeClass("czr-skope-pane-open")).done(function(){_.has(b,"resetPanel")&&!1!==b.resetPanel.length&&setTimeout(function(){b.resetPanel.remove(),api.czr_isResettingSkope(!1)},300)}),_.delay(function(){c.resolve()},350),c.promise()},doResetSkopeValues:function(){var a=this,b=a().id,c=a.dirtyness()?"_resetSkopeDirties":"_resetSkopeAPIValues",d=function(){var d=function(){api.czr_skopeBase.processSilentUpdates({refresh:!1}).fail(function(){api.consoleLog("Silent update failed after resetting skope : "+b)}).done(function(){$.when($(".czr-reset-warning",a.resetPanel).fadeOut("300")).done(function(){$.when($(".czr-reset-success",a.resetPanel).fadeIn("300")).done(function(){_.delay(function(){api.czr_isResettingSkope(!1),a.skopeResetDialogVisibility(!1)},2e3)})})})};a[c]().done(function(){api.previewer.refresh().fail(function(a){api.consoleLog("SKOPE RESET REFRESH FAILED",a)}).done(function(a){if("global"==api.czr_skope(b)().skope&&"_resetSkopeAPIValues"==c){var e,f={},g=api.czr_skope(b)().opt_name;!_.isUndefined(a.skopesServerData)&&_.has(a.skopesServerData,"czr_skopes")&&(e=a.skopesServerData.czr_skopes,_.isUndefined(_.findWhere(e,{opt_name:g}))&&(f=_.findWhere(e,{opt_name:g}).db||{})),api.czr_skopeBase.maybeSynchronizeGlobalSkope({isGlobalReset:!0,isSkope:!0,skopeIdToReset:b}).done(function(){d()})}else d()})})};$("body").addClass("czr-resetting-skope"),api.czr_skopeReset[a.dirtyness()?"resetChangeset":"resetPublished"]({skope_id:a().id,is_skope:!0}).always(function(){$("body").removeClass("czr-resetting-skope")}).done(function(a){d()}).fail(function(b){a.skopeResetDialogVisibility(!1),api.consoleLog("Skope reset failed",b)})},_resetSkopeDirties:function(){var a=this,b=$.Deferred();return a.dirtyValues({}),a.changesetValues({}),b.resolve().promise()},_resetSkopeAPIValues:function(){var a=this,b=$.Deferred();return a.dbValues({}),b.resolve().promise()}}),function(a,b,c){serverControlParams.isSkopOn&&(a.Value.prototype.set=function(d,e){var f=this._value,g=b.Deferred(),h=this,i=[];return d=this._setter.apply(this,arguments),d=this.validate(d),null===d||c.isEqual(f,d)?this:(this._value=d,this._dirty=!0,this._deferreds?(c.each(h._deferreds,function(a){i.push(a.apply(null,[d,f,e]))}),b.when.apply(null,i).fail(function(){a.consoleLog("A deferred callback failed in api.Value::set()")}).then(function(){h.callbacks.fireWith(h,[d,f,e]),g.resolveWith(h,[d,f,e])}),g.promise(h)):(this.callbacks.fireWith(this,[d,f,e]),g.resolveWith(h,[d,f,e]).promise(h)))},a.Value.prototype.bind=function(){var a=this,d=!1,e=[];return b.each(arguments,function(a,b){d||(d=c.isObject(b)&&b.deferred),c.isFunction(b)&&e.push(b)}),d?(a._deferreds=a._deferreds||[],c.each(e,function(b){c.contains(b,a._deferreds)||a._deferreds.push(b)})):a.callbacks.add.apply(a.callbacks,arguments),this},a.Setting.prototype.silent_set=function(b,d){var e=this._value,f=a.state("saved")();return b=this._setter.apply(this,arguments),b=this.validate(b),null===b||c.isEqual(e,b)?this:(this._value=b,this._dirty=c.isUndefined(d)||!c.isBoolean(d)?this._dirty:d,this.callbacks.fireWith(this,[b,e,{silent:!0}]),a.state("saved")(f),this)})}(wp.customize,jQuery,_),function(a,b,c){a.bind("ready",function(){if(serverControlParams.isSkopOn){var d=a.previewer.query;a.previewer.query=function(e){if(!c.has(a,"czr_skope"))return a.consoleLog("QUERY : SKOPE IS NOT ON. FALLING BACK ON CORE QUERY"),d.apply(this);if("pending"==a.czr_initialSkopeCollectionPopulated.state())return a.consoleLog("QUERY : INITIAL SKOPE COLLECTION NOT POPULATED YET. FALLING BACK ON CORE QUERY"),d.apply(this);if("pending"==a.czr_isPreviewerSkopeAware.state()&&a.czr_isPreviewerSkopeAware.resolve(),!c.isObject(e)&&"resolved"==a.czr_initialSkopeCollectionPopulated.state()&&"resolved"==a.czr_initialSkopeCollectionPopulated.state())return d.apply(this);!c.isUndefined(e.skope_id)&&c.isString(e.skope_id)||(e.skope_id=a.czr_activeSkopeId()||a.czr_skopeBase.getGlobalSkopeId());var f,g={},h={},i={skope_id:null,action:null,the_dirties:{},dyn_type:null,opt_name:null};if(e=b.extend(i,e),!c.isObject(e.the_dirties))throw a.consoleLog("QUERY PARAMS : ",e),new Error("QUERY DIRTIES MUST BE AN OBJECT. Requested action : "+e.action);if("pending"!=a.czr_isPreviewerSkopeAware.state()&&c.isNull(e.skope_id))throw a.consoleLog("QUERY PARAMS : ",e),new Error("OVERRIDEN QUERY : NO SKOPE ID. FALLING BACK ON CORE QUERY. Requested action : "+e.action);if(!c.contains([null,"refresh","save","reset","changeset_update"],e.action))throw a.consoleLog("QUERY PARAMS : ",e),new Error("A REQUESTED QUERY HAS NO AUTHORIZED ACTION. Requested action : "+e.action);var j=function(){if("pending"==a.czr_initialSkopeCollectionPopulated.state())return{};var b={};return c.each(a.czr_currentSkopesCollection(),function(c){"global"!=c.skope&&(b[c.id]=a.czr_skopeBase.getSkopeDirties(c.id))}),b};switch(c.isNull(e.the_dirties)||c.isEmpty(e.the_dirties)?(g=a.dirtyValues({unsaved:e.excludeCustomizedSaved||!1}),h=j()):"global"==a.czr_skopeBase.getActiveSkopeName()?g=e.the_dirties:h[a.czr_activeSkopeId()]=e.the_dirties,e.action){case null:case"refresh":break;case"changeset_update":if(c.isUndefined(e.opt_name))throw new Error("Missing opt_name param in the changeset_update query for skope : "+e.skope_id);break;case"save":if(c.isNull(e.dyn_type)&&(e.dyn_type=a.czr_skope(e.skope_id)().dyn_type),c.isNull(e.dyn_type)||c.isUndefined(e.dyn_type))throw new Error("QUERY : A SAVE QUERY MUST HAVE A VALID DYN TYPE."+e.skope_id);break;case"reset":if(c.isNull(e.dyn_type)&&(e.dyn_type=a.czr_skope(e.skope_id)().dyn_type),c.isNull(e.dyn_type)||c.isUndefined(e.dyn_type))throw new Error("QUERY : A RESET QUERY MUST HAVE A VALID DYN TYPE."+e.skope_id)}var k={};return c.each(a.czr_currentSkopesCollection(),function(a){k[a.skope]={id:a.id,opt_name:a.opt_name}}),f={wp_customize:"on",customized:"{}"==JSON.stringify(g)?'{"__not_customized__"}':JSON.stringify(g),skopeCustomized:JSON.stringify(h),nonce:this.nonce.preview,skope:a.czr_skope(e.skope_id)().skope,level_id:a.czr_skope(e.skope_id)().level,skope_id:e.skope_id,dyn_type:e.dyn_type,opt_name:c.isNull(e.opt_name)?a.czr_skope(e.skope_id)().opt_name:e.opt_name,obj_id:a.czr_skope(e.skope_id)().obj_id,current_skopes:JSON.stringify(k)||{},channel:this.channel(),revisionIndex:a._latestRevision},f=a.czr_isChangeSetOn()?b.extend(f,{customize_theme:a.settings.theme.stylesheet,customize_changeset_uuid:a.settings.changeset.uuid}):b.extend(f,{theme:a.settings.theme.stylesheet})}}})}(wp.customize,jQuery,_),function(a,b,c){a.bind("czr-skope-started",function(){a.previewer.save=function(b){return a.czr_skopeSave.save()}})}(wp.customize,jQuery,_),function(a,b,c){if(serverControlParams.isSkopOn){a.Element.synchronizer.checkbox.update=function(a){this.element.prop("checked",a),this.element.iCheck("update")};var d=a.Element.synchronizer.val.update;a.Element.synchronizer.val.update=function(b){var c=this,e=function(){c.element.is("select")?c.element.val(b).trigger("change"):c.element.hasClass("wp-color-picker")?c.element.val(b).trigger("change"):c.element.val(b)};if(serverControlParams.isSkopOn){if("pending"==a.czr_skopeReady.state())return d.call(c,b);a.czr_skopeReady.then(function(){e()})}else e()},a.Element.synchronizer.val.refresh=function(){var a=this;return this.element.is("select")&&c.isNull(this.element.val())?c.isArray(a())?[]:c.isObject(a())?{}:"":this.element.val()}}}(wp.customize,jQuery,_),function(a,b,c){var d=a.Previewer.prototype.refresh,e=function(f){f=c.extend({waitSkopeSynced:!0,the_dirties:{}},f),c.has(a,"czr_activeSkopeId")&&!c.isUndefined(a.czr_activeSkopeId())||a.consoleLog("The api.czr_activeSkopeId() is undefined in the api.previewer._new_refresh() method.");var g=this,h=b.Deferred();if(!c.has(a,"czr_activeSkopeId")&&"pending"==a.czr_skopeReady.state())return a.czr_skopeReady.done(function(){e.apply(a.previewer,f)}),d.apply(g),h.resolve().promise();g.send("loading-initiated"),g.abort();var i=a.czr_getSkopeQueryParams({skope_id:a.czr_activeSkopeId(),action:"refresh",the_dirties:f.the_dirties||{}});return g.loading=new a.PreviewFrame({url:g.url(),previewUrl:g.previewUrl(),query:g.query(i)||{},container:g.container,signature:"WP_CUSTOMIZER_SIGNATURE"}),g.settingsModifiedWhileLoading={},onSettingChange=function(a){g.settingsModifiedWhileLoading[a.id]=!0},a.bind("change",onSettingChange),g.loading.always(function(){a.unbind("change",onSettingChange)}),a.czr_isChangeSetOn()||(g._previousPreview=g._previousPreview||g.preview),g.loading.done(function(b){var c,d=this;g.preview=d,g.targetWindow(d.targetWindow()),g.channel(d.channel()),c=function(b){d.unbind("synced",c),d.unbind("czr-skopes-synced",c),g._previousPreview?g._previousPreview.destroy():g.preview&&g.preview.destroy(),g._previousPreview=g.preview,g.deferred.active.resolve(),delete g.loading,a.trigger("pre_refresh_done",{previewer:g,skopesServerData:b||{}}),h.resolve({previewer:g,skopesServerData:b||{}})},a.czr_isChangeSetOn()||g.send("sync",{scroll:g.scroll,settings:a.get()}),f.waitSkopeSynced?d.bind("czr-skopes-synced",c):d.bind("synced",c),g.trigger("ready",b)}),g.loading.fail(function(b,c){a.consoleLog("LOADING FAILED : ",arguments),g.send("loading-failed"),
a.czr_isChangeSetOn()||"redirect"===b&&c&&g.previewUrl(c),"logged out"===b&&(g.preview&&(g.preview.destroy(),delete g.preview),g.login().done(g.refresh)),"cheatin"===b&&g.cheatin(),h.reject(b)}),h.promise()};a.bind("czr-skope-started",function(){czr_override_refresh_for_skope(),a.Previewer.prototype.refresh=e}),a.czr_getSkopeQueryParams=function(d){if(!a.czr_isChangeSetOn())return d;d=c.isObject(d)?d:{};var e=d.action||"refresh";switch(e){case"refresh":d=b.extend(d,{excludeCustomizedSaved:!0})}return d},czr_override_refresh_for_skope=function(){serverControlParams.isSkopOn&&(a.previewer.refresh=function(d){var f=b.Deferred(),g=function(b){var c,d=function(){return 0===a.state("processing").get()},g=function(){e.call(a.previewer,b).done(function(a){f.resolve(a)})};d()?g():(c=function(){d()&&(g(),a.state("processing").unbind(c))},a.state("processing").bind(c))};return g=c.debounce(g,a.previewer.refreshBuffer),g(d),f.promise()})}}(wp.customize,jQuery,_),function(a,b,c){serverControlParams.isSkopOn&&(a.dirtyValues=function(b){return a.czr_skopeBase.getSkopeDirties(a.czr_skopeBase.getGlobalSkopeId(),b)})}(wp.customize,jQuery,_),function(a,b,c){if(serverControlParams.isSkopOn&&a.czr_isChangeSetOn()){var d=a.requestChangesetUpdate;a.requestChangesetUpdate=function(e){var f=b.Deferred(),g=[],h=[],i=e||{},j=[],k=[],l=b.Deferred();(0===a._lastSavedRevision||c.isEmpty(a.state("changesetStatus")()))&&(i=c.extend(i,{blogname:{dummy_change:"dummy_change"}})),c.each(a.czr_currentSkopesCollection(),function(a){"global"!=a.skope&&g.push(a.id)});var m=function(d){if(c.isUndefined(g[d+1])&&h.length==g.length){if(c.isEmpty(j))l.resolve(k);else{var e=function(){var d=[];return c.each(j,function(b){d.push(a.czr_skopeBase.buildServerResponse(b))}),b.trim(d.join(" | "))};l.reject(e())}return!0}},n=function(b){return(c.isUndefined(b)||0==b)&&a.state("processing").set(1),b=b||0,c.isUndefined(g[b])?(a.consoleLog("Undefined Skope in changeset recursive call ",b,g,g[b]),l.resolve(k).promise()):(a._requestSkopeChangetsetUpdate(e,g[b]).always(function(){h.push(b)}).fail(function(c){j.push(c),a.consoleLog("CHANGESET UPDATE RECURSIVE FAIL FOR SKOPE : ",g[b]),m(b)||n(b+1)}).done(function(a){k.push(a),m(b)||n(b+1)}),l.promise())},o=a._lastSavedRevision;return d(i).fail(function(b){a.consoleLog("WP requestChangesetUpdateFail",b,a.czr_skopeBase.buildServerResponse(b)),a._lastSavedRevision=Math.max(a._latestRevision,a._lastSavedRevision),a.state("processing").set(0),f.reject(b),b=a.czr_skopeBase.buildServerResponse(b),a.czr_serverNotification({message:b,status:"error"})}).done(function(b){"pending"==a.czr_initialSkopeCollectionPopulated.state()&&f.resolve(b),a._lastSavedRevision=o,n().always(function(){a._lastSavedRevision=Math.max(a._latestRevision,a._lastSavedRevision),a.state("processing").set(0)}).fail(function(b){f.reject(b),a.consoleLog("CHANGESET UPDATE RECURSIVE PUSH FAIL",b,k),a.trigger("changeset-error",b),a.czr_serverNotification({message:b,status:"error"})}).done(function(){f.resolve(b)})}),f.promise()},a._requestSkopeChangetsetUpdate=function(d,e){if(c.isUndefined(e)||!a.czr_skope.has(e))throw new Error("In api._requestSkopeChangetsetUpdate() : a valid and registered skope_id must be provided");var f,g=new b.Deferred,h={};if(e=e||a.czr_activeSkopeId(),d&&c.extend(h,d),c.each(a.czr_skopeBase.getSkopeDirties(e),function(a,b){d&&null===d[b]||(h[b]=c.extend({},h[b]||{},{value:a}))}),c.isEmpty(h))return g.resolve({}),g.promise();if(a._latestRevision<=a._lastSavedRevision)return g.resolve({}),g.promise();a.trigger("skope-changeset-save",h);var i={skope_id:e,action:"changeset_update",opt_name:a.czr_skope(e).opt_name};return f=a.previewer.query(c.extend(i,{excludeCustomizedSaved:!0})),delete f.customized,c.extend(f,{nonce:a.settings.nonce.save,customize_changeset_data:JSON.stringify(h)}),wp.ajax.post("customize_skope_changeset_save",f).done(function(a){g.resolve(a)}).fail(function(b){a.consoleLog("SKOPE CHANGESET FAIL FOR SKOPE "+b.skope_id,b),g.reject(b)}).always(function(b){b.setting_validities&&a._handleSettingValidities({settingValidities:b.setting_validities})}),g.promise()}}}(wp.customize,jQuery,_),function(a,b,c){serverControlParams.isSkopOn&&(a.Setting.prototype.preview=function(b,d,e){var f,g=this;f=g.transport,c.has(a,"czr_isPreviewerSkopeAware")&&"pending"==a.czr_isPreviewerSkopeAware.state()&&this.previewer.refresh(),c.isObject(e)&&!0===e.not_preview_sent||c.has(e,"silent")&&!1!==e.silent||("postMessage"!==f||a.state("previewerAlive").get()||(f="refresh"),"postMessage"===f?g.previewer.send("setting",[g.id,g()]):"refresh"===f&&g.previewer.refresh())})}(wp.customize,jQuery,_),function(a,b,c){if("function"==typeof a.Section){var d=a.Section.prototype.initialize;a.Section.prototype.initialize=function(a,e){d.apply(this,[a,e]);var f=this;this.expanded.callbacks.add(function(a){if(a){var d=f.container.closest(".wp-full-overlay-sidebar-content"),e=f.container.find(".accordion-section-content");_resizeContentHeight=function(){e.css("height",d.innerHeight())},_resizeContentHeight(),b(window).on("resize.customizer-section",c.debounce(_resizeContentHeight,110))}})}}}(wp.customize,jQuery,_),function(a,b,c){a.CZR_Helpers=a.CZR_Helpers||{},a.CZR_Helpers=b.extend(a.CZR_Helpers,{getControlSettingId:function(b,d){if(d="default",!a.control.has(b))throw new Error("The requested control_id is not registered in the api yet : "+b);if(c.has(a.control(b),"settings")&&!c.isEmpty(a.control(b).settings)){if(!c.has(a.control(b).settings,d))throw new Error("The requested control_id does not have the requested setting type : "+b+" , "+d);if(c.isUndefined(a.control(b).settings[d].id))throw new Error("The requested control_id has no setting id assigned : "+b);return a.control(b).settings[d].id}},getDocSearchLink:function(a){a=c.isString(a)?a:"";var b=a.replace(/ /g,"+"),d=[serverControlParams.docURL,"search?query=",b].join("");return['<a href="'+d+'" title="'+serverControlParams.translatedStrings.readDocumentation+'" target="_blank">'," ",'<span class="fa fa-question-circle-o"></span>'].join("")},build_setId:function(a){return c.contains(serverControlParams.wpBuiltinSettings,a)?a:c.contains(serverControlParams.themeSettingList,a)&&-1==a.indexOf(serverControlParams.themeOptions)?[serverControlParams.themeOptions+"[",a,"]"].join(""):a},getOptionName:function(a){return-1==a.indexOf(serverControlParams.themeOptions)?a:a.replace(/\[|\]/g,"").replace(serverControlParams.themeOptions,"")},hasPartRefresh:function(b){if(c.has(a,"czr_partials"))return c.contains(c.map(a.czr_partials(),function(a,d){return c.contains(a.settings,b)}),!0)},getSectionControlIds:function(b){return b=b||a.czr_activeSectionId(),a.section.has(b)?c.map(a.section(b).controls(),function(a){return a.id}):[]},getSectionSettingIds:function(b){if(b=b||a.czr_activeSectionId(),a.section.has(b)){var d=this,e=[],f=d.getSectionControlIds(b);return c.each(f,function(b){c.each(a.control(b).settings,function(a,b){e.push(a.id)})}),e}},capitalize:function(a){return c.isString(a)?a.charAt(0).toUpperCase()+a.slice(1):a},truncate:function(a,b,d){if(c.isUndefined(a))return"";var e=a.length>b,f=e?a.substr(0,b-1):a;return f=d&&e?f.substr(0,f.lastIndexOf(" ")):f,e?f+"...":f},isMultiItemModule:function(b,d){if(!c.isUndefined(b)||c.isObject(d)){if(c.isObject(d)&&c.has(d,"module_type"))b=d.module_type;else if(c.isUndefined(b)||c.isNull(b))return;if(c.has(a.czrModuleMap,b))return a.czrModuleMap[b].crud||a.czrModuleMap[b].multi_item||!1}},isCrudModule:function(b,d){if(!c.isUndefined(b)||c.isObject(d)){if(c.isObject(d)&&c.has(d,"module_type"))b=d.module_type;else if(c.isUndefined(b)||c.isNull(b))return;if(c.has(a.czrModuleMap,b))return a.czrModuleMap[b].crud||!1}},hasModuleModOpt:function(b,d){if(!c.isUndefined(b)||c.isObject(d)){if(c.isObject(d)&&c.has(d,"module_type"))b=d.module_type;else if(c.isUndefined(b)||c.isNull(b))return;if(c.has(a.czrModuleMap,b))return a.czrModuleMap[b].has_mod_opt||!1}},setupInputCollectionFromDOM:function(){var d=this;if(!c.isFunction(d))throw new Error("setupInputCollectionFromDOM : inputParentInst is not valid.");var e=d.module,f=c.has(d(),"is_mod_opt");if(!c.has(d,"czr_Input")||c.isEmpty(d.inputCollection())){d.czr_Input=new a.Values,d.inputConstructor=f?e.inputModOptConstructor:e.inputConstructor;var g=f?d.defaultModOptModel:d.defaultItemModel;if(c.isEmpty(g)||c.isUndefined(g))throw new Error("No default model found in item or mod opt "+d.id+".");var h=b.extend(!0,{},d());h=c.isObject(h)?b.extend(g,h):g;var i={};return b("."+e.control.css_attr.sub_set_wrapper,d.container).each(function(g){var j=b(this).find("[data-type]").attr("data-type"),k=c.has(h,j)?h[j]:"";if(c.isUndefined(j)||c.isEmpty(j))return void a.consoleLog("setupInputCollectionFromDOM : missing data-type for "+e.id);if(!c.has(h,j))throw new Error("The item or mod opt property : "+j+" has been found in the DOM but not in the item or mod opt model : "+d.id+". The input can not be instantiated.");var l=b(this).attr("data-input-type"),m=b(this).attr("data-transport")||"inherit",n=c.has(e.inputOptions,l)?e.inputOptions[l]:{};d.czr_Input.add(j,new d.inputConstructor(j,{id:j,type:l,transport:m,input_value:k,input_options:n,container:b(this),input_parent:d,is_mod_opt:f,module:e})),d.czr_Input(j).ready(),i[j]=k}),d.inputCollection(i),d}},removeInputCollection:function(){var a=this;if(!c.isFunction(a))throw new Error("removeInputCollection : inputParentInst is not valid.");c.has(a,"czr_Input")&&(a.czr_Input.each(function(b){a.czr_Input.remove(b.id)}),a.inputCollection({}))},refreshModuleControl:function(c){var d=a.controlConstructor.czr_module,e=(a.control(c).params.type,a.settings.controls[c]);b.when(a.control(c).container.remove()).done(function(){a.control.remove(c),a.control.add(c,new d(c,{params:e,previewer:a.previewer}))})}})}(wp.customize,jQuery,_),function(a,b,c){a.CZR_Helpers=a.CZR_Helpers||{},a.CZR_Helpers=b.extend(a.CZR_Helpers,{addActions:function(a,b,d){var e=this;d=d||e,d[a]=d[a]||[],new_event_map=c.clone(d[a]),d[a]=c.union(new_event_map,c.isArray(b)?b:[b])},doActions:function(a,b,c){b.trigger(a,c)},setupDOMListeners:function(d,e,f){var g=this;f=f||g,c.map(d,function(d){if(!c.isString(d.selector)||c.isEmpty(d.selector))throw new Error("setupDOMListeners : selector must be a string not empty. Aborting setup of action(s) : "+d.actions.join(","));e.dom_el.on(d.trigger,d.selector,function(h,i){if(h.stopPropagation(),!a.utils.isKeydownButNotEnterEvent(h)){h.preventDefault();var j=c.clone(e);c.has(j,"model")&&c.has(j.model,"id")&&(c.has(f,"get")?j.model=f():j.model=f.getModel(j.model.id)),b.extend(j,{event:d,dom_event:h}),b.extend(j,i),g.executeEventActionChain(j,f)}})})},executeEventActionChain:function(a,b){var d=this;if(!c.has(a,"event")||!c.has(a.event,"actions"))throw new Error("executeEventActionChain : No obj.event or no obj.event.actions properties found");if("function"==typeof a.event.actions)return a.event.actions(a);c.isArray(a.event.actions)||(a.event.actions=[a.event.actions]);var e=!1;c.map(a.event.actions,function(f){if(!e){if("function"!=typeof b[f])throw new Error("executeEventActionChain : the action : "+f+" has not been found when firing event : "+a.event.selector);var g=c.has(a,"dom_el")&&-1!=a.dom_el.length?a.dom_el:d.container;g.trigger("before_"+f,c.omit(a,"event"));var h=b[f](a);!1===h&&(e=!0),g.trigger("after_"+f,c.omit(a,"event"))}})}})}(wp.customize,jQuery,_),function(a,b,c){a.czr_wpQueryDataReady=b.Deferred(),a.czr_wpQueryInfos=a.czr_wpQueryInfos||new a.Value,a.czr_partials=a.czr_partials||new a.Value,a.bind("ready",function(){a.previewer.bind("houston-widget-settings",function(b){var d=c.filter(b.registeredSidebars,function(a){return!c.findWhere(_wpCustomizeWidgetsSettings.registeredSidebars,{id:a.id})}),e=c.filter(b.registeredSidebars,function(a){return!c.has(b.renderedSidebars,a.id)});e=c.map(e,function(a){return a.id});var f=c.map(b.registeredSidebars,function(a){return a.id});a.czr_widgetZoneSettings=a.czr_widgetZoneSettings||new a.Value,a.czr_widgetZoneSettings.set({actives:b.renderedSidebars,inactives:e,registered:f,candidates:d,available_locations:b.availableWidgetLocations})}),a.previewer.bind("czr-query-data-ready",function(b){a.czr_wpQueryInfos(b),"pending"==a.czr_wpQueryDataReady.state()&&a.czr_wpQueryDataReady.resolve(b)}),a.previewer.bind("czr-partial-refresh",function(b){a.czr_partials.set(b)})})}(wp.customize,jQuery,_);var CZRInputMths=CZRInputMths||{};$.extend(CZRInputMths,{initialize:function(a,b){if(_.isUndefined(b.input_parent)||_.isEmpty(b.input_parent))throw new Error("No input_parent assigned to input "+b.id+". Aborting");if(_.isUndefined(b.module))throw new Error("No module assigned to input "+b.id+". Aborting");api.Value.prototype.initialize.call(this,null,b);var c=this;if($.extend(c,b||{}),c.isReady=$.Deferred(),_.isUndefined(b.input_value)||c.set(b.input_value),api.czrInputMap&&_.has(api.czrInputMap,c.type)){var d=api.czrInputMap[c.type];_.isFunction(c[d])&&c[d](b.input_options||null)}else api.consoleLog("Warning an input : "+c.id+" has no corresponding method defined in api.czrInputMap.");var e={text:"keyup",textarea:"keyup",password:"keyup",color:"colorpickerchange",range:"input propertychange"};c.input_event_map=[{trigger:$.trim(["change",e[c.type]||""].join(" ")),selector:"input[data-type], select[data-type], textarea[data-type]",name:"set_input_value",actions:function(a){if(!_.has(c.input_parent,"syncElements")||!_.has(c.input_parent.syncElements,c.id))throw new Error("WARNING : THE INPUT "+c.id+" HAS NO SYNCED ELEMENT.")}}]},ready:function(){var a=this;a.setupDOMListeners(a.input_event_map,{dom_el:a.container},a),a.callbacks.add(function(){return a.inputReact.apply(a,arguments)}),$.when(a.setupSynchronizer()).done(function(){a.isReady.resolve(a)})},setupSynchronizer:function(){var a=this,b=a.input_parent,c=a.container.find("[data-type]"),d=a.container.find("[data-type]").is("textarea");if(d)throw new Error("TO DO : THE TEXTAREA INPUT ARE NOT READY IN THE SYNCHRONIZER!");var e=new api.Element(c);b.syncElements=b.syncElements||{},b.syncElements[a.id]=e,e.sync(a),e.set(a())},inputReact:function(a,b,c){var d=this,e=d.input_parent(),f=_.clone(e);f=!_.isObject(f)||_.isEmpty(f)?{}:f,f[d.id]=a,d.input_parent.set(f,{input_changed:d.id,input_transport:d.transport,not_preview_sent:"postMessage"===d.transport}),_.has(d,"is_preItemInput")||d.input_parent.trigger(d.id+":changed",a),_.isEmpty(b)&&(_.isUndefined(b)||"postMessage"!==d.transport)||d._sendInput(a,b)},_sendInput:function(a,b){var c=this,d=c.module;_.isEqual(a,b)||(d.control.previewer.send("czr_input",{set_id:d.control.id,module_id:d.id,input_id:c.id,value:a}),d.trigger("input_sent",{input:a,dom_el:c.container}))},setupColorPicker:function(){var a=this;a.container.find("input").wpColorPicker({change:function(a,b){$(this).val($(this).wpColorPicker("color")).trigger("colorpickerchange").trigger("change")}})},setupSelect:function(){var a=this;$("select",a.container).not(".no-selecter-js").each(function(){$(this).selecter({})})},setupIcheck:function(a){var b=this;$("input[type=checkbox]",b.container).each(function(a){0===$(this).closest('div[class^="icheckbox"]').length&&$(this).iCheck({checkboxClass:"icheckbox_flat-grey",checkedClass:"checked",radioClass:"iradio_flat-grey"}).on("ifChanged",function(a){$(this).val(!1===$(this).is(":checked")?0:1),$(a.currentTarget).trigger("change")})})},setupStepper:function(a){var b=this;$('input[type="number"]',b.container).each(function(a){$(this).stepper()})},setupRangeSlider:function(a){var b,c=this,d=function(a,b){a.textContent=b+"%"};$(c.container).find("input").rangeslider({polyfill:!1,rangeClass:"rangeslider",disabledClass:"rangeslider--disabled",horizontalClass:"rangeslider--horizontal",verticalClass:"rangeslider--vertical",fillClass:"rangeslider__fill",handleClass:"rangeslider__handle",onInit:function(){b=$(".rangeslider__handle",this.$range),$(".rangeslider__handle",this.$range),d(b[0],this.value)}}).on("input",function(){d(b[0],this.value)})}}),$.extend(CZRInputMths,{setupImageUploader:function(){var a=this,b=a();return a.attachment={},a.container?(this.tmplRendered=$.Deferred(),this.setupContentRendering(b,{}),void this.tmplRendered.done(function(){a.czrImgUploaderBinding()})):this},setupContentRendering:function(a,b){var c,d=this;d.attachment.id!=a&&b!==a?(a||(d.attachment={},d.renderImageUploaderTemplate()),c=wp.media.attachment(a),_.isObject(c)&&_.has(c,"attributes")&&_.has(c.attributes,"sizes")?(d.attachment=c.attributes,d.renderImageUploaderTemplate()):wp.media.attachment(a).fetch().done(function(){d.attachment=this.attributes,d.renderImageUploaderTemplate()})):d.attachment.id&&d.attachment.id!==a||d.renderImageUploaderTemplate()},czrImgUploaderBinding:function(){var a=this;_.bindAll(a,"czrImgUploadRemoveFile","czrImgUploadOpenFrame","czrImgUploadSelect"),a.container.on("click keydown",".upload-button",a.czrImgUploadOpenFrame),a.container.on("click keydown",".thumbnail-image img",a.czrImgUploadOpenFrame),a.container.on("click keydown",".remove-button",a.czrImgUploadRemoveFile),a.bind(a.id+":changed",function(b,c){a.tmplRendered=$.Deferred(),a.setupContentRendering(b,c)})},czrImgUploadOpenFrame:function(a){api.utils.isKeydownButNotEnterEvent(a)||(a.preventDefault(),this.frame||this.czrImgUploadInitFrame(),this.frame.open())},czrImgUploadInitFrame:function(){var a=this,b=this.getUploaderLabels();a.frame=wp.media({button:{text:b.frame_button},states:[new wp.media.controller.Library({title:b.frame_title,library:wp.media.query({type:"image"}),multiple:!1,date:!1})]}),a.frame.on("select",a.czrImgUploadSelect)},czrImgUploadRemoveFile:function(a){var b=this;api.utils.isKeydownButNotEnterEvent(a)||(a.preventDefault(),b.attachment={},b.set(""))},czrImgUploadSelect:function(){var a=this,b=a.frame.state().get("selection").first().toJSON();window._wpmejsSettings||{};a.attachment=b,a.set(b.id)},renderImageUploaderTemplate:function(){var a=this;if(0!==$("#tmpl-czr-input-img-uploader-view-content").length){var b=wp.template("czr-input-img-uploader-view-content");if(b&&a.container){var c=a.container.find("."+a.module.control.css_attr.img_upload_container);if(c.length){var d={button_labels:a.getUploaderLabels(),settings:a.id,attachment:a.attachment,canUpload:!0};return c.html(b(d)),a.tmplRendered.resolve(),a.container.trigger(a.id+":content_rendered"),!0}}}},getUploaderLabels:function(){var a=serverControlParams.translatedStrings,b={select:a.select_image,change:a.change_image,remove:a.remove_image,"default":a.default_image,placeholder:a.placeholder_image,frame_title:a.frame_title_image,frame_button:a.frame_button_image};return _.each(b,function(a,b){if(_.isUndefined(a)){var c=this;throw new Error("A translated string is missing ( "+b+" ) for the image uploader input in module : "+c.module.id)}}),b}});var CZRInputMths=CZRInputMths||{};$.extend(CZRInputMths,{setupContentPicker:function(a){var b=this,c=[];$.extend({post:"",taxonomy:""},_.isObject(a)?a:{}),b.wpObjectTypes=a,b.container.find(".czr-input").append('<select data-select-type="content-picker-select" class="js-example-basic-simple"></select>'),c=[{trigger:"change",selector:"select[data-select-type]",name:"set_input_value",actions:function(a){var c=$(a.dom_event.currentTarget,a.dom_el),d=$(c,a.dom_el).select2("data"),e={},f={id:"",type_label:"",title:"",object_type:"",url:""};return d=_.isArray(d)?d[0]:d,!_.isObject(d)||_.isEmpty(d)?void api.consoleLog("Content Picker Input : the picked value should be an object not empty."):(_.each(f,function(a,b){return!_.has(d,b)||_.isEmpty(d[b])?void api.consoleLog("content_picker : missing input param : "+b):void(e[b]=d[b])}),void b.set(e))}}],b.setupDOMListeners(c,{dom_el:b.container},b),b.setupContentSelecter()},setupContentSelecter:function(){var a=this;a.container.find("select").select2({placeholder:{id:"-1",title:"Select"},data:a.setupSelectedContents(),ajax:{url:serverControlParams.AjaxUrl,type:"POST",dataType:"json",delay:250,debug:!0,data:function(b){var c=b.page?b.page-1:0;return c=b.term?b.page:c,{action:b.term?"search-available-content-items-customizer":"load-available-content-items-customizer",search:b.term,wp_customize:"on",page:c,wp_object_types:JSON.stringify(a.wpObjectTypes),CZRCpNonce:serverControlParams.CZRCpNonce}},processResults:function(a,b){if(!a.success)return{results:[]};var c=a.data.items,d=[];return _.each(c,function(a){d.push({id:a.id,title:a.title,type_label:a.type_label,object_type:a.object,url:a.url})}),{results:d,pagination:{more:10==a.data.items.length}}}},templateSelection:a.czrFormatContentSelected,templateResult:a.czrFormatContentSelected,escapeMarkup:function(a){return a}})},czrFormatContentSelected:function(a){if(a.loading)return a.text;var b="<div class='content-picker-item clearfix'><div class='content-item-bar'><span class='item-title'>"+a.title+"</span>";return a.type_label&&(b+="<span class='item-type'>"+a.type_label+"</span>"),b+="</div></div>"},setupSelectedContents:function(){var a=this,b=a();return b}});var CZRInputMths=CZRInputMths||{};$.extend(CZRInputMths,{setupTextEditor:function(){var a=this;a();if(!a.container)throw new Error("The input container is not set for WP text editor in module."+a.module.id);a.czrRenderInputTextEditorTemplate()&&(a.editor=tinyMCE("czr-customize-content_editor"),a.textarea=$("#czr-customize-content_editor"),a.editorPane=$("#czr-customize-content_editor-pane"),a.dragbar=$("#czr-customize-content_editor-dragbar"),a.editorFrame=$("#czr-customize-content_editor_ifr"),a.mceTools=$("#wp-czr-customize-content_editor-tools"),a.mceToolbar=a.editorPane.find(".mce-toolbar-grp"),a.mceStatusbar=a.editorPane.find(".mce-statusbar"),a.preview=$("#customize-preview"),a.collapse=$(".collapse-sidebar"),a.textpreview=a.container.find("textarea"),a.toggleButton=a.container.find("button.text_editor-button"),a.editorExpanded=new api.Value((!1)),a.czrUpdateTextPreview(),a.czrSetToggleButtonText(a.editorExpanded()),a.czrTextEditorBinding(),a.czrResizeEditorOnUserRequest())},czrTextEditorBinding:function(){var a=this,b=a.editor,c=a.textarea,d=a.toggleButton,e=(a.editorExpanded,a.editorPane);a.bind(a.id+":changed",a.czrUpdateTextPreview),_.bindAll(a,"czrOnVisualEditorChange","czrOnTextEditorChange","czrResizeEditorOnWindowResize"),d.on("click",function(){a.editorExpanded.set(!a.editorExpanded()),a.editorExpanded()&&b.focus()}),a.module.czr_ModuleState.bind(function(b){"expanded"!=b&&a.editorExpanded.set(!1)}),a.editorExpanded.bind(function(d){api.consoleLog("in input.editorExpanded",d,a()),b.locker&&b.locker!==a&&(b.locker.editorExpanded.set(!1),b.locker=null),b.locker&&b.locker!==a||($(document.body).toggleClass("czr-customize-content_editor-pane-open",d),b.locker=a),a.czrSetToggleButtonText(d),d?(b.setContent(wp.editor.autop(a())),b.on("input change keyup",a.czrOnVisualEditorChange),c.on("input",a.czrOnTextEditorChange),a.czrResizeEditor(window.innerHeight-e.height()),$(window).on("resize",a.czrResizeEditorOnWindowResize)):(b.off("input change keyup",a.czrOnVisualEditorChange),c.off("input",a.czrOnTextEditorChange),$(window).off("resize",a.czrResizeEditorOnWindowResize),a.czrResizeReset())})},czrOnVisualEditorChange:function(){var a,b=this,c=b.editor;a=wp.editor.removep(c.getContent()),b.set(a)},czrOnTextEditorChange:function(){var a,b=this,c=b.textarea;a=c.val(),b.set(a)},czrUpdateTextPreview:function(){var a,b=this,c=b();a=c.replace(/(<([^>]+)>)/gi,""),a.length>30&&(a=a.substring(0,34)+"..."),b.textpreview.val(a)},czrRenderInputTextEditorTemplate:function(){var a=this;if(0===$("#tmpl-czr-input-text_editor-view-content").length)throw new Error("Missing js template for text editor input in module : "+a.module.id);var b=wp.template("czr-input-text_editor-view-content"),c=a.container.find("input");if(b&&a.container)return api.consoleLog("Model injected in text editor tmpl : ",a()),c.after(b(a())),!0},czrIsEditorExpanded:function(){return $(document.body).hasClass("czr-customize-content_editor-pane-open")},czrResizeReset:function(){var a=this,b=a.preview,c=a.collapse,d=a.container.closest("ul.accordion-section-content");d.css("padding-bottom",""),b.css("bottom",""),c.css("bottom","")},czrResizeEditor:function(a){var b=window.innerHeight,c=window.innerWidth,d=40,e=1,f=782,g=56,h=8,i=4,j={},k=this,l=k.container.closest("ul.accordion-section-content"),m=k.mceTools,n=k.mceToolbar,o=k.mceStatusbar,p=k.preview,q=k.collapse,r=k.editorPane,s=k.editorFrame;k.editorExpanded()&&(_.isNaN(a)||(resizeHeight=b-a),j.height=resizeHeight,j.components=m.outerHeight()+n.outerHeight()+o.outerHeight(),resizeHeight<d&&(j.height=d),resizeHeight>b-e&&(j.height=b-e),b<r.outerHeight()&&(j.height=b),p.css("bottom",j.height),r.css("height",j.height),s.css("height",j.height-j.components),q.css("bottom",j.height+h),g>b-j.height&&q.css("bottom",o.outerHeight()+i),c<=f?l.css("padding-bottom",j.height):l.css("padding-bottom",""))},czrResizeEditorOnWindowResize:function(){var a=this,b=50,c=a.editorPane;a.editorExpanded()&&_.delay(function(){a.czrResizeEditor(window.innerHeight-c.height())},b)},czrResizeEditorOnUserRequest:function(){var a=this,b=a.dragbar,c=a.editorFrame;b.on("mousedown",function(){a.editorExpanded()&&$(document).on("mousemove.czr-customize-content_editor",function(b){b.preventDefault(),$(document.body).addClass("czr-customize-content_editor-pane-resize"),c.css("pointer-events","none"),a.czrResizeEditor(b.pageY)})}),b.on("mouseup",function(){a.editorExpanded()&&($(document).off("mousemove.czr-customize-content_editor"),$(document.body).removeClass("czr-customize-content_editor-pane-resize"),c.css("pointer-events",""))})},czrSetToggleButtonText:function(a){var b=this;b.toggleButton.text(serverControlParams.translatedStrings[a?"textEditorClose":"textEditorOpen"])}});var CZRItemMths=CZRItemMths||{};$.extend(CZRItemMths,{initialize:function(a,b){if(_.isUndefined(b.module)||_.isEmpty(b.module))throw new Error("No module assigned to item "+a+". Aborting");var c=this;api.Value.prototype.initialize.call(c,null,b),c.isReady=$.Deferred(),c.embedded=$.Deferred(),c.container=null,c.contentContainer=null,c.inputCollection=new api.Value({}),$.extend(c,b||{}),c.defaultItemModel=_.clone(b.defaultItemModel)||{id:"",title:""};var d=$.extend(c.defaultItemModel,b.initial_item_model);c.set(d),c.userEventMap=new api.Value([{trigger:"click keydown",selector:["."+c.module.control.css_attr.display_alert_btn,"."+c.module.control.css_attr.cancel_alert_btn].join(","),name:"toggle_remove_alert",actions:["toggleRemoveAlertVisibility"]},{trigger:"click keydown",selector:"."+c.module.control.css_attr.remove_view_btn,name:"remove_item",actions:["removeItem"]},{trigger:"click keydown",selector:["."+c.module.control.css_attr.edit_view_btn,"."+c.module.control.css_attr.item_title].join(","),name:"edit_view",actions:["setViewVisibility"]}]),c.isReady.done(function(){c.module.updateItemsCollection({item:c()}),c.callbacks.add(function(){return c.itemReact.apply(c,arguments)}),c.mayBeRenderItemWrapper(),c.embedded.done(function(){c.itemWrapperViewSetup(d)}),c.bind("contentRendered",function(){if(!_.has(c,"czr_Input")||_.isEmpty(c.inputCollection()))try{api.CZR_Helpers.setupInputCollectionFromDOM.call(c)}catch(a){api.consoleLog(a)}}),c.bind("contentRemoved",function(){_.has(c,"czr_Input")&&api.CZR_Helpers.removeInputCollection.call(c)})})},ready:function(){this.isReady.resolve()},itemReact:function(a,b){var c=this,d=c.module;d.updateItemsCollection({item:a}),c.writeItemViewTitle(a)}});var CZRItemMths=CZRItemMths||{};$.extend(CZRItemMths,{_sendItem:function(a,b){var c=this,d=c.module,e=[];_.each(b,function(b,c){b!=a[c]&&e.push(c)}),_.each(e,function(b){d.control.previewer.send("sub_setting",{set_id:d.control.id,id:a.id,changed_prop:b,value:a[b]}),d.trigger("item_sent",{item:a,dom_el:c.container,changed_prop:b})})},removeItem:function(){var a=this,b=this.module,c=_.clone(b.itemCollection());b.trigger("pre_item_dom_remove",a()),a._destroyView(),c=_.without(c,_.findWhere(c,{id:a.id})),b.itemCollection.set(c),b.trigger("pre_item_api_remove",a()),b.czr_Item.remove(a.id)},getModel:function(a){return this()}});var CZRItemMths=CZRItemMths||{};$.extend(CZRItemMths,{mayBeRenderItemWrapper:function(){var a=this;"pending"==a.embedded.state()&&$.when(a.renderItemWrapper()).done(function(b){if(a.container=b,_.isUndefined(a.container)||!a.container.length)throw new Error("In mayBeRenderItemWrapper the Item view has not been rendered : "+a.id);a.embedded.resolve()})},itemWrapperViewSetup:function(a){var b=this,c=this.module;a=b()||b.initial_item_model,b.czr_ItemState=new api.Value("closed"),b.writeItemViewTitle();var d=function(a,c,d){if(_.isUndefined(a)||!1===a.length)throw new Error("Module : "+b.module.id+", the item content has not been rendered for "+b.id);b.trigger("contentRendered"),b.contentContainer=a,b.toggleItemExpansion(c,d)};b.module.isMultiItem()?b.czr_ItemState.callbacks.add(function(a,e){var f=-1!==a.indexOf("expanded");f?_.isObject(b.contentContainer)&&!1!==b.contentContainer.length?b.toggleItemExpansion(a,e):$.when(b.renderItemContent(b()||b.initial_item_model)).done(function(b){(d=_.debounce(d,50))(b,a,e)}):b.toggleItemExpansion(a,e).done(function(){_.isObject(b.contentContainer)&&!1!==b.contentContainer.length&&(b.trigger("beforeContenRemoved"),$("."+c.control.css_attr.item_content,b.container).children().each(function(){$(this).remove()}),b.contentContainer=null,b.trigger("contentRemoved"))})}):(b.czr_ItemState.callbacks.add(function(a,c){b.toggleItemExpansion.apply(b,arguments)}),$.when(b.renderItemContent(a)).done(function(a){d(a,!0)})),api.CZR_Helpers.setupDOMListeners(b.userEventMap(),{model:a,dom_el:b.container},b)},renderItemWrapper:function(a){var b=this,c=b.module;if(a=a||b(),$_view_el=$("<li>",{"class":c.control.css_attr.single_item,"data-id":a.id,id:a.id}),c.itemsWrapper.append($_view_el),c.isMultiItem()){var d=c.getTemplateEl("rudItemPart",a);if(0===$("#tmpl-"+d).length)throw new Error("Missing template for item "+b.id+". The provided template script has no been found : #tmpl-"+c.getTemplateEl("rudItemPart",a));$_view_el.append($(wp.template(d)(a)))}return $_view_el.append($("<div/>",{"class":c.control.css_attr.item_content})),$_view_el},renderItemContent:function(a){var b=this,c=this.module;if(a=a||b(),0===$("#tmpl-"+c.getTemplateEl("itemInputList",a)).length)throw new Error("No item content template defined for module "+c.id+". The template script id should be : #tmpl-"+c.getTemplateEl("itemInputList",a));var d=wp.template(c.getTemplateEl("itemInputList",a));return d?($(d(a)).appendTo($("."+c.control.css_attr.item_content,b.container)),$($(d(a)),b.container)):this},writeItemViewTitle:function(a){var b=this,c=b.module,d=a||b(),e=_.has(d,"title")?api.CZR_Helpers.capitalize(d.title):d.id;e=api.CZR_Helpers.truncate(e,20),$("."+c.control.css_attr.item_title,b.container).text(e),api.CZR_Helpers.doActions("after_writeViewTitle",b.container,d,b)},setViewVisibility:function(a,b){var c=this,d=this.module;b?c.czr_ItemState.set("expanded_noscroll"):(d.closeAllItems(c.id),_.has(d,"preItem")&&d.preItemExpanded.set(!1),c.czr_ItemState.set("expanded"==c._getViewState()?"closed":"expanded"))},_getViewState:function(){return-1==this.czr_ItemState().indexOf("expanded")?"closed":"expanded"},toggleItemExpansion:function(a,b,c){var d=this,e=this.module,f=$.Deferred();return $("."+e.control.css_attr.item_content,d.container).first().slideToggle({duration:c||200,done:function(){var b="closed"!=a;d.container.toggleClass("open",b),e.closeAllAlerts();var c=$(this).siblings().find("."+e.control.css_attr.edit_view_btn);c.toggleClass("active",b),b?c.removeClass("fa-pencil").addClass("fa-minus-square").attr("title",serverControlParams.translatedStrings.close):c.removeClass("fa-minus-square").addClass("fa-pencil").attr("title",serverControlParams.translatedStrings.edit),"expanded"==a&&e._adjustScrollExpandedBlock(d.container),f.resolve()}}),f.promise()},toggleRemoveAlertVisibility:function(a){var b=this,c=this.module,d=$("."+c.control.css_attr.remove_alert_wrapper,b.container).first();a.dom_event;
if(c.closeAllItems(),_.has(c,"preItem")&&c.preItemExpanded.set(!1),$("."+c.control.css_attr.remove_alert_wrapper,b.container).not(d).each(function(){$(this).hasClass("open")&&$(this).slideToggle({duration:200,done:function(){$(this).toggleClass("open",!1),$(this).siblings().find("."+c.control.css_attr.display_alert_btn).toggleClass("active",!1)}})}),!wp.template(c.AlertPart)||!b.container)throw new Error("No removal alert template available for items in module :"+c.id);d.html(wp.template(c.AlertPart)({title:b().title||b.id})),d.slideToggle({duration:200,done:function(){var d=!$(this).hasClass("open")&&$(this).is(":visible");$(this).toggleClass("open",d),$(a.dom_el).find("."+c.control.css_attr.display_alert_btn).toggleClass("active",d),d&&c._adjustScrollExpandedBlock(b.container)}})},_destroyView:function(a){this.container.fadeOut({duration:a||400,done:function(){$(this).remove()}})}});var CZRModOptMths=CZRModOptMths||{};$.extend(CZRModOptMths,{initialize:function(a){if(_.isUndefined(a.module)||_.isEmpty(a.module))throw new Error("No module assigned to modOpt.");var b=this;api.Value.prototype.initialize.call(b,null,a),b.isReady=$.Deferred(),b.container=null,b.inputCollection=new api.Value({}),$.extend(b,a||{}),b.defaultModOptModel=_.clone(a.defaultModOptModel)||{is_mod_opt:!0};var c=$.extend(b.defaultModOptModel,a.initial_modOpt_model),d=b.module.control;b.set(c),api.czr_ModOptVisible=new api.Value((!1)),api.czr_ModOptVisible.bind(function(a){a?b.modOptWrapperViewSetup(c).done(function(c){b.container=c;try{api.CZR_Helpers.setupInputCollectionFromDOM.call(b).toggleModPanelView(a)}catch(d){api.consoleLog(d)}}):b.toggleModPanelView(a).done(function(){!1!==b.container.length?$.when(b.container.remove()).done(function(){api.CZR_Helpers.removeInputCollection.call(b)}):api.CZR_Helpers.removeInputCollection.call(b),b.container=null})}),b.isReady.done(function(){$("."+d.css_attr.edit_modopt_icon,d.container).length||$.when(d.container.find(".customize-control-title").first().append($("<span/>",{"class":[d.css_attr.edit_modopt_icon,"fa fa-cog"].join(" "),title:"Settings"}))).done(function(){$("."+d.css_attr.edit_modopt_icon,d.container).fadeIn(400)}),api.CZR_Helpers.setupDOMListeners([{trigger:"click keydown",selector:"."+d.css_attr.edit_modopt_icon,name:"toggle_mod_option",actions:function(){api.czr_ModOptVisible(!api.czr_ModOptVisible())}}],{dom_el:d.container},b)})},ready:function(){this.isReady.resolve()}});var CZRModOptMths=CZRModOptMths||{};$.extend(CZRModOptMths,{modOptWrapperViewSetup:function(a){var b=this,c=this.module,d=$.Deferred(),e=function(a){api.CZR_Helpers.setupDOMListeners([{trigger:"click keydown",selector:"."+c.control.css_attr.close_modopt_icon,name:"close_mod_option",actions:function(){api.czr_ModOptVisible(!1)}}],{dom_el:a},b)};return a=b()||b.initial_modOpt_model,$.when(b.renderModOptContent(a)).done(function(a){if(_.isUndefined(a)||!1===a.length)throw new Error("Module : "+b.module.id+", the modOpt content has not been rendered");e(a),d.resolve(a)}),d.promise()},renderModOptContent:function(a){var b=this,c=this.module;if(a=a||b(),0===$("#tmpl-"+c.getTemplateEl("modOptInputList",a)).length)throw new Error("No modOpt content template defined for module "+c.id+". The template script id should be : #tmpl-"+c.getTemplateEl("modOptInputList",a));var d=wp.template(c.getTemplateEl("modOptInputList",a));if(!d)return this;var e="";try{e="Options for "+c.control.params.label}catch(f){e="Settings"}return $("#widgets-left").after($("<div/>",{"class":c.control.css_attr.mod_opt_wrapper,html:[['<h2 class="mod-opt-title">',e,"</h2>"].join(""),'<span class="fa fa-times '+c.control.css_attr.close_modopt_icon+'" title="close"></span>'].join("")})),$("."+c.control.css_attr.mod_opt_wrapper).append($(d(a))),$("."+c.control.css_attr.mod_opt_wrapper)},toggleModPanelView:function(a){var b=this.module,c=(b.control,$.Deferred());return b.control.container.toggleClass("czr-modopt-visible",a),$("body").toggleClass("czr-editing-modopt",a),_.delay(function(){c.resolve()},200),c.promise()}});var CZRModuleMths=CZRModuleMths||{};$.extend(CZRModuleMths,{initialize:function(a,b){if(_.isUndefined(b.control)||_.isEmpty(b.control))throw new Error("No control assigned to module "+a);var c=this;api.Value.prototype.initialize.call(this,null,b),c.isReady=$.Deferred(),$.extend(c,b||{}),$.extend(c,{crudModulePart:"czr-crud-module-part",rudItemPart:"czr-rud-item-part",ruItemPart:"czr-ru-item-part",itemInputList:"",modOptInputList:"",AlertPart:"czr-rud-item-alert-part"}),c.embedded=$.Deferred(),c.isInSektion()||(c.container=$(c.control.selector),c.embedded.resolve()),c.embedded.done(function(){$.when(c.renderModuleParts()).done(function(a){if(!1===a.length)throw new Error("The items wrapper has not been rendered for module : "+c.id);c.itemsWrapper=a})}),c.defaultAPImodOptModel={initial_modOpt_model:{},defaultModOptModel:{},control:{},module:{}},c.defaultModOptModel={},c.modOptConstructor=api.CZRModOpt,c.itemCollection=new api.Value([]),c.defaultAPIitemModel={id:"",initial_item_model:{},defaultItemModel:{},control:{},module:{},is_added_by_user:!1},c.defaultItemModel={id:"",title:""},c.itemConstructor=api.CZRItem,c.czr_Item=new api.Values,c.inputConstructor=api.CZRInput,c.hasModOpt()&&(c.inputModOptConstructor=api.CZRInput),c.inputOptions={},c.isReady.done(function(){c.isDirty=new api.Value(b.dirty||!1),c.initializeModuleModel(b).done(function(a){c.set(a)}).fail(function(a){api.consoleLog("Module : "+c.id+" initialize module model failed : ",a)}).always(function(a){c.callbacks.add(function(){return c.moduleReact.apply(c,arguments)}),c.control.isModuleRegistered(c.id)||c.control.updateModulesCollection({module:b,is_registered:!1}),c.bind("items-collection-populated",function(a){c.itemCollection.callbacks.add(function(){return c.itemCollectionReact.apply(c,arguments)}),c.isMultiItem()&&c._makeItemsSortable()}),c.isInSektion()||c.populateSavedItemCollection(),c.hasModOpt()&&c.instantiateModOpt()})})},ready:function(){var a=this;a.isReady.resolve(),api.consoleLog("MODULE READY IN BASE MODULE CLASS : ",a.id)},initializeModuleModel:function(a){var b=this,c=$.Deferred();if(!b.isMultiItem()&&!b.isCrud()&&_.isEmpty(a.items)){var d=_.clone(b.defaultItemModel);a.items=[$.extend(d,{id:b.id})]}return c.resolve(a).promise()},itemCollectionReact:function(a,b,c){var d=this,e=d(),f=$.extend(!0,{},e);f.items=a,d.isDirty.set(!0),d.set(f,c||{})},moduleReact:function(a,b,c){var d=this,e=d.control;_.size(b.items)==_.size(a.items)&&!_.isEmpty(_.difference(a.items,b.items)),a.column_id!=b.column_id;e.updateModulesCollection({module:$.extend(!0,{},a),data:c})},getModuleSection:function(){return this.section},isInSektion:function(){var a=this;return _.has(a,"sektion_id")},isMultiItem:function(){return api.CZR_Helpers.isMultiItemModule(null,this)},isCrud:function(){return api.CZR_Helpers.isCrudModule(null,this)},hasModOpt:function(){return api.CZR_Helpers.hasModuleModOpt(null,this)},instantiateModOpt:function(){var a=this,b=a.prepareModOptForAPI(a().modOpt||{});a.czr_ModOpt=new a.modOptConstructor(b),a.czr_ModOpt.ready(),a.czr_ModOpt.callbacks.add(function(b,c,d){var e=a(),f=$.extend(!0,{},e);f.modOpt=b,a.isDirty(!0),a(f,d)})},prepareModOptForAPI:function(a){var b=this,c={};return a=_.isObject(a)?a:{},_.each(b.defaultAPImodOptModel,function(d,e){a[e];switch(e){case"initial_modOpt_model":_.each(b.getDefaultModOptModel(),function(b,c){_.has(a,c)||(a[c]=b)}),c[e]=a;break;case"defaultModOptModel":c[e]=_.clone(b.defaultModOptModel);break;case"control":c[e]=b.control;break;case"module":c[e]=b}}),c},getDefaultModOptModel:function(a){var b=this;return $.extend(_.clone(b.defaultModOptModel),{is_mod_opt:!0})}});var CZRModuleMths=CZRModuleMths||{};$.extend(CZRModuleMths,{populateSavedItemCollection:function(){var a=this,b=[];if(!_.isArray(a().items))throw new Error("populateSavedItemCollection : The saved items collection must be an array in module :"+a.id);_.each(a().items,function(a,c){_.has(a,"id")&&!_.has(a,"is_mod_opt")&&b.push(a)}),_.each(b,function(b,c){a.instantiateItem(b).ready()}),_.each(b,function(b){if(_.isUndefined(_.findWhere(a.itemCollection(),b.id)))throw new Error("populateSavedItemCollection : The saved items have not been properly populated in module : "+a.id)}),a.trigger("items-collection-populated")},instantiateItem:function(a,b){var c=this;if(item_candidate=c.prepareItemForAPI(a),!_.has(item_candidate,"id"))throw new Error("CZRModule::instantiateItem() : an item has no id and could not be added in the collection of : "+this.id);if(c.czr_Item.has(item_candidate.id))throw new Error("CZRModule::instantiateItem() : the following item id "+item_candidate.id+" already exists in module.czr_Item() for module "+this.id);if(c.czr_Item.add(item_candidate.id,new c.itemConstructor(item_candidate.id,item_candidate)),!c.czr_Item.has(item_candidate.id))throw new Error("CZRModule::instantiateItem() : instantiation failed for item id "+item_candidate.id+" for module "+this.id);return c.czr_Item(item_candidate.id)},prepareItemForAPI:function(a){var b=this,c={};return a=_.isObject(a)?a:{},_.each(b.defaultAPIitemModel,function(d,e){var f=a[e];switch(e){case"id":_.isEmpty(f)?c[e]=b.generateItemId(b.module_type):c[e]=f;break;case"initial_item_model":_.each(b.getDefaultItemModel(),function(b,c){_.has(a,c)||(a[c]=b)}),c[e]=a;break;case"defaultItemModel":c[e]=_.clone(b.defaultItemModel);break;case"control":c[e]=b.control;break;case"module":c[e]=b;break;case"is_added_by_user":c[e]=!!_.isBoolean(f)&&f}}),_.has(c,"id")||(c.id=b.generateItemId(b.module_type)),c.initial_item_model.id=c.id,c},generateItemId:function(a,b,c){if(c=c||1,c>100)throw new Error("Infinite loop when generating of a module id.");var d=this;b=b||d._getNextItemKeyInCollection();var e=a+"_"+b;if(!_.has(d,"itemCollection")||!_.isArray(d.itemCollection()))throw new Error("The item collection does not exist or is not properly set in module : "+d.id);return d.isItemRegistered(e)?(b++,c++,d.generateItemId(a,b,c)):e},_getNextItemKeyInCollection:function(){var a=this,b={},c=0;return _.isEmpty(a.itemCollection())||(b=_.max(a.itemCollection(),function(a){return parseInt(a.id.replace(/[^\/\d]/g,""),10)}),c=parseInt(b.id.replace(/[^\/\d]/g,""),10)+1),c},isItemRegistered:function(a){var b=this;return!_.isUndefined(_.findWhere(b.itemCollection(),{id:a}))},updateItemsCollection:function(a){var b=this,c=b.itemCollection();if(_new_collection=_.clone(c),_.has(a,"collection"))return void b.itemCollection.set(a.collection);if(!_.has(a,"item"))throw new Error("updateItemsCollection, no item provided "+b.control.id+". Aborting");var d=_.clone(a.item);_.findWhere(_new_collection,{id:d.id})?_.each(c,function(a,b){a.id==d.id&&(_new_collection[b]=d)}):_new_collection.push(d),b.itemCollection.set(_new_collection)},_getSortedDOMItemCollection:function(){var a=this,b=_.clone(a.itemCollection()),c=[],d=$.Deferred();if($("."+a.control.css_attr.single_item,a.container).each(function(a){var d=_.findWhere(b,{id:$(this).attr("data-id")});d&&(c[a]=d)}),b.length!=c.length)throw new Error("There was a problem when re-building the item collection from the DOM in module : "+a.id);return d.resolve(c).promise()},refreshItemCollection:function(){var a=this;a.czr_Item.each(function(b){$.when(a.czr_Item(b.id).container.remove()).done(function(){a.czr_Item.remove(b.id)})}),a.itemCollection=new api.Value([]),a.populateSavedItemCollection()}});var CZRModuleMths=CZRModuleMths||{};$.extend(CZRModuleMths,{getDefaultItemModel:function(a){var b=this;return $.extend(_.clone(b.defaultItemModel),{id:a||""})},_initNewItem:function(a,b){var c,d=this,e={id:""};return b="undefined"!=typeof b?b:_.size(d.itemCollection()),_.isNumber(b)?c=d.module_type+"_"+b:(c=b,b=0),e=a&&!_.isEmpty(a)?$.extend(a,{id:c}):this.getDefaultItemModel(c),_.has(e,"id")&&d._isItemIdPossible(c)?(_.map(d.getDefaultItemModel(),function(a,b){_.has(e,b)||(e[b]=a)}),e):d._initNewItem(e,b+1)}});var CZRModuleMths=CZRModuleMths||{};$.extend(CZRModuleMths,{renderModuleParts:function(){var a=this,b=a.isInSektion()?$(a.container).find(".czr-mod-content"):$(a.container);if(a.isCrud()){if(0===$("#tmpl-"+a.crudModulePart).length)throw new Error("No crud Module Part template for module "+a.id+". The template script id should be : #tmpl-"+a.crudModulePart);b.append($(wp.template(a.crudModulePart)({})))}var c=$("<ul/>",{"class":[a.control.css_attr.items_wrapper,a.module_type,a.isMultiItem()?"multi-item-mod":"mono-item-mod",a.isCrud()?"crud-mod":"not-crud-mod"].join(" ")});return b.append(c),$(c,b)},getTemplateEl:function(a,b){var c,d=this;switch(a){case"rudItemPart":c=d.rudItemPart;break;case"ruItemPart":c=d.ruItemPart;break;case"modOptInputList":c=d.modOptInputList;break;case"itemInputList":c=d.itemInputList}if(_.isEmpty(c))throw new Error("No valid template has been found in getTemplateEl() "+d.id+". Aborting");return c},getViewEl:function(a){var b=this;return $('[data-id = "'+a+'"]',b.container)},closeAllItems:function(a){var b=this,c=_.clone(b.itemCollection()),d=_.filter(c,function(b){return b.id!=a});_.each(d,function(a){b.czr_Item.has(a.id)&&"expanded"==b.czr_Item(a.id)._getViewState(a.id)&&b.czr_Item(a.id).czr_ItemState.set("closed")})},_adjustScrollExpandedBlock:function(a,b){if(a.length&&!_.isUndefined(this.getModuleSection())){var c,d=this,e=$(".accordion-section-content",d.section.container),f=e.scrollTop(),g=b||90;setTimeout(function(){a.offset().top+a.height()+g>$(window.top).height()&&(c=a.offset().top+a.height()+g-$(window.top).height(),c>0&&e.animate({scrollTop:f+c},500))},50)}},closeAllAlerts:function(){var a=this;$("."+a.control.css_attr.remove_alert_wrapper,a.container).each(function(){$(this).hasClass("open")&&$(this).slideToggle({duration:100,done:function(){$(this).toggleClass("open",!1),$(this).siblings().find("."+a.control.css_attr.display_alert_btn).toggleClass("active",!1)}})})},_makeItemsSortable:function(a){if(!wp.media.isTouchDevice&&$.fn.sortable){var b=this;$("."+b.control.css_attr.items_wrapper,b.container).sortable({handle:"."+b.control.css_attr.item_sort_handle,start:function(){_.has(api,"czrModulePanelState")&&api.czrModulePanelState.set(!1),_.has(api,"czrSekSettingsPanelState")&&api.czrSekSettingsPanelState.set(!1)},update:function(a,c){var d=function(){_.has(b,"preItem")&&b.preItemExpanded.set(!1),b.closeAllItems(),b.closeAllAlerts(),"postMessage"!=api(b.control.id).transport||api.CZR_Helpers.hasPartRefresh(b.control.id)||(refreshPreview=_.debounce(refreshPreview,500),refreshPreview())};b._getSortedDOMItemCollection().done(function(a){b.itemCollection.set(a)}).then(function(){d()})}})}}});var CZRDynModuleMths=CZRDynModuleMths||{};$.extend(CZRDynModuleMths,{initialize:function(a,b){var c=this;api.CZRModule.prototype.initialize.call(c,a,b),$.extend(c,{itemPreAddEl:""}),c.itemAddedMessage=serverControlParams.translatedStrings.successMessage,c.userEventMap=new api.Value([{trigger:"click keydown",selector:["."+c.control.css_attr.open_pre_add_btn,"."+c.control.css_attr.cancel_pre_add_btn].join(","),name:"pre_add_item",actions:["closeAllItems","closeAllAlerts","renderPreItemView","setPreItemViewVisibility"]},{trigger:"click keydown",selector:"."+c.control.css_attr.add_new_btn,name:"add_item",actions:["closeAllAlerts","closeAllItems","addItem"]}])},ready:function(){var a=this;api.consoleLog("MODULE READY IN DYN MODULE CLASS : ",a.id),a.setupDOMListeners(a.userEventMap(),{dom_el:a.container}),a.preItem=new api.Value(a.getDefaultItemModel()),a.preItemEmbedded=$.Deferred(),a.preItemEmbedded.done(function(){a.setupPreItemInputCollection()}),a.preItemExpanded=new api.Value((!1)),a.preItemExpanded.callbacks.add(function(b,c){a._togglePreItemViewExpansion(b)}),api.CZRModule.prototype.ready.call(a)},setupPreItemInputCollection:function(){var a=this;a.preItem.czr_Input=new api.Values,$("."+a.control.css_attr.pre_add_wrapper,a.container).find("."+a.control.css_attr.sub_set_wrapper).each(function(b){var c=$(this).find("[data-type]").attr("data-type")||"sub_set_"+b;a.preItem.czr_Input.add(c,new a.inputConstructor(c,{id:c,type:$(this).attr("data-input-type"),container:$(this),input_parent:a.preItem,module:a,is_preItemInput:!0})),a.preItem.czr_Input(c).ready()})},addItem:function(a){var b=this,c=b.preItem(),d=function(){b.preItemExpanded.set(!1),b._resetPreItemInputs(),b.toggleSuccessMessage("off")};if(_.isEmpty(c)||!_.isObject(c))throw new Error("addItem : an item should be an object and not empty. In : "+b.id+". Aborted.");d=_.debounce(d,2e3),b.instantiateItem(c,!0).ready(),b.czr_Item(c.id).isReady.then(function(){b.toggleSuccessMessage("on"),d(),b.trigger("item_added",c),"postMessage"!=api(b.control.id).transport||!_.has(a,"dom_event")||_.has(a.dom_event,"isTrigger")||api.CZR_Helpers.hasPartRefresh(b.control.id)||b.control.previewer.refresh()})},_resetPreItemInputs:function(){var a=this;a.preItem.set(a.getDefaultItemModel()),a.preItem.czr_Input.each(function(b){var c=b.id;_.has(a.getDefaultItemModel(),c)&&b.set(a.getDefaultItemModel()._input_id)})}});var CZRDynModuleMths=CZRDynModuleMths||{};$.extend(CZRDynModuleMths,{renderPreItemView:function(a){var b=this;if("pending"==b.preItemEmbedded.state()){if(!_.has(b,"itemPreAddEl")||0===$("#tmpl-"+b.itemPreAddEl).length)return this;var c=wp.template(b.itemPreAddEl);if(!c||!b.container)return this;var d=$("."+b.control.css_attr.pre_add_item_content,b.container);d.prepend(c()),b.preItemEmbedded.resolve()}},_getPreItemView:function(){var a=this;return $("."+a.control.css_attr.pre_add_item_content,a.container)},setPreItemViewVisibility:function(a){var b=this;b.preItemExpanded.set(!b.preItemExpanded())},_togglePreItemViewExpansion:function(a){var b=this,c=$("."+b.control.css_attr.pre_add_item_content,b.container);c.slideToggle({duration:200,done:function(){var c=$("."+b.control.css_attr.open_pre_add_btn,b.container);$(this).toggleClass("open",a),a?c.find(".fa").removeClass("fa-plus-square").addClass("fa-minus-square"):c.find(".fa").removeClass("fa-minus-square").addClass("fa-plus-square"),c.toggleClass("active",a),$(b.container).toggleClass(b.control.css_attr.adding_new,a),b._adjustScrollExpandedBlock($(this),120)}})},toggleSuccessMessage:function(a){var b=this,c=b.itemAddedMessage,d=$("."+b.control.css_attr.pre_add_wrapper,b.container);return $_success_wrapper=$("."+b.control.css_attr.pre_add_success,b.container),"on"==a?($_success_wrapper.find("p").text(c),$_success_wrapper.css("z-index",1000001).css("height",d.height()+"px").css("line-height",d.height()+"px")):$_success_wrapper.attr("style",""),b.container.toggleClass("czr-model-added","on"==a),this}});var CZRSocialModuleMths=CZRSocialModuleMths||{};$.extend(CZRSocialModuleMths,{initialize:function(a,b){var c=this;api.CZRDynModule.prototype.initialize.call(c,a,b),$.extend(c,{itemPreAddEl:"czr-module-social-pre-add-view-content",itemInputList:"czr-module-social-item-content"}),this.social_icons=["500px","adn","amazon","android","angellist","apple","behance","behance-square","bitbucket","bitbucket-square","black-tie","btc","buysellads","chrome","codepen","codiepie","connectdevelop","contao","dashcube","delicious","deviantart","digg","dribbble","dropbox","drupal","edge","empire","envelope","envelope-o","envelope-square","expeditedssl","facebook","facebook-f (alias)","facebook-official","facebook-square","firefox","flickr","fonticons","fort-awesome","forumbee","foursquare","get-pocket","gg","gg-circle","git","github","github-alt","github-square","gitlab","git-square","google","google-plus","google-plus-circle","google-plus-official","google-plus-square","google-wallet","gratipay","hacker-news","houzz","instagram","internet-explorer","ioxhost","joomla","jsfiddle","lastfm","lastfm-square","leanpub","linkedin","linkedin-square","linux","maxcdn","meanpath","medium","mixcloud","modx","odnoklassniki","odnoklassniki-square","opencart","openid","opera","optin-monster","pagelines","paypal","pied-piper","pied-piper-alt","pinterest","pinterest-p","pinterest-square","product-hunt","qq","rebel","reddit","reddit-alien","reddit-square","renren","rss","rss-square","safari","scribd","sellsy","share-alt","share-alt-square","shirtsinbulk","simplybuilt","skyatlas","skype","slack","slideshare","snapchat","soundcloud","spotify","stack-exchange","stack-overflow","steam","steam-square","stumbleupon","stumbleupon-circle","telegram","tencent-weibo","trello","tripadvisor","tumblr","tumblr-square","twitch","twitter","twitter-square","usb","viacoin","vimeo","vimeo-square","vine","vk","weibo","weixin","whatsapp","wikipedia-w","windows","wordpress","xing","xing-square","yahoo","y-combinator","yelp","youtube","youtube-play","youtube-square"],c.inputConstructor=api.CZRInput.extend(c.CZRSocialsInputMths||{}),c.itemConstructor=api.CZRItem.extend(c.CZRSocialsItem||{}),this.defaultItemModel={id:"",title:"","social-icon":"","social-link":"","social-color":serverControlParams.social_el_params.defaultSocialColor,"social-target":1},this.itemAddedMessage=serverControlParams.translatedStrings.socialLinkAdded,_.has(api,"czr_activeSectionId")&&c.control.section()==api.czr_activeSectionId()&&"resolved"!=c.isReady.state()&&c.ready(),api.section(c.control.section()).expanded.bind(function(a){"resolved"!=c.isReady.state()&&c.ready()}),c.isReady.then(function(){c.preItem.bind(function(a,b){_.has(a,"social-icon")&&(_.isEqual(a["social-icon"],b["social-icon"])||c.updateItemModel(c.preItem,!0))})})},updateItemModel:function(a,b){var c=a;if(b=b||!1,_.has(c(),"social-icon")&&!_.isEmpty(c()["social-icon"])){var d=$.extend(!0,{},c()),e=this.getTitleFromIcon(d["social-icon"]),f=serverControlParams.social_el_params.defaultSocialColor;e=[serverControlParams.translatedStrings.followUs,e].join(" "),b?(d=$.extend(d,{title:e,"social-color":f}),c.set(d)):(c.czr_Input("title").set(e),c.czr_Input("social-link").set(""),c.czr_Input("social-color")&&c.czr_Input("social-color").set(f))}},getTitleFromIcon:function(a){return api.CZR_Helpers.capitalize(a.replace("fa-","").replace("envelope","email"))},getIconFromTitle:function(a){return"fa-".title.toLowerCase().replace("envelope","email")},CZRSocialsInputMths:{setupSelect:function(){function a(a){if(!a.id)return a.text;var b=$('<span class="fa '+a.element.value.toLowerCase()+'">&nbsp;&nbsp;'+a.text+"</span>");return b}var b=this,c=b.input_parent,d=b.module,e=d.social_icons,f=c(),g=_.isEmpty(f.id);g&&(e=_.union([serverControlParams.translatedStrings.selectSocialIcon],e)),_.each(e,function(a,c){var e=g&&0===c?"":"fa-"+a.toLowerCase(),h={value:e,html:d.getTitleFromIcon(a)};e==f["social-icon"]&&$.extend(h,{selected:"selected"}),$('select[data-type="social-icon"]',b.container).append($("<option>",h))}),$('select[data-type="social-icon"]',b.container).select2({templateResult:a,templateSelection:a})},setupColorPicker:function(a){var b=this,c=(b.input_parent,b.module);$('input[data-type="social-color"]',b.container).wpColorPicker({defaultColor:serverControlParams.social_el_params.defaultSocialColor||"rgba(255,255,255,0.7)",change:function(a,b){_.has(b,"color")&&16777215==b.color._color?$(this).val(serverControlParams.social_el_params.defaultSocialColor||"rgba(255,255,255,0.7)"):$(this).val(b.color.toString()),$(this).trigger("colorpickerchange").trigger("change")}}),$('input[data-type="social-color"]',b.container).closest("div").on("click keydown",function(){c._adjustScrollExpandedBlock(b.container)})}},CZRSocialsItem:{ready:function(){var a=this;api.CZRItem.prototype.ready.call(a),a.bind("social-icon:changed",function(){a.module.updateItemModel(a)})},_buildTitle:function(a,b,c){var d=this,e=d.module;return a=a||("string"==typeof b?api.CZR_Helpers.capitalize(b.replace("fa-","")):""),a=api.CZR_Helpers.truncate(a,20),b=b||"fa-"+e.social_icons[0],c=c||serverControlParams.social_el_params.defaultSocialColor,'<div><span class="fa '+b+'" style="color:'+c+'"></span> '+a+"</div>"},writeItemViewTitle:function(a){var b=this,c=b.module,d=a||b(),e=c.getTitleFromIcon(d["social-icon"]);$("."+c.control.css_attr.item_title,b.container).html(b._buildTitle(e,d["social-icon"],d["social-color"]))}}});var CZRWidgetAreaModuleMths=CZRWidgetAreaModuleMths||{};$.extend(CZRWidgetAreaModuleMths,{initialize:function(a,b){var c=this;api.CZRDynModule.prototype.initialize.call(this,a,b),$.extend(c,{itemPreAddEl:"czr-module-widgets-pre-add-view-content",itemInputList:"czr-module-widgets-item-input-list",itemInputListReduced:"czr-module-widgets-item-input-list-reduced",ruItemPart:"czr-module-widgets-ru-item-part"}),c.inputConstructor=api.CZRInput.extend(c.CZRWZonesInputMths||{}),c.itemConstructor=api.CZRItem.extend(c.CZRWZonesItem||{}),c.serverParams=serverControlParams.widget_area_el_params||{},c.contexts=_.has(c.serverParams,"sidebar_contexts")?c.serverParams.sidebar_contexts:{},c.context_match_map={is_404:"404",is_category:"archive-category",is_home:"home",is_page:"page",is_search:"search",is_single:"single"},c.locations=_.has(c.serverParams,"sidebar_locations")?c.serverParams.sidebar_locations:{},c.defaultItemModel={id:"",title:serverControlParams.translatedStrings.widgetZone,contexts:_.without(_.keys(c.contexts),"_all_"),locations:[c.serverParams.defaultWidgetLocation],description:""},this.itemAddedMessage=serverControlParams.translatedStrings.widgetZoneAdded,_.has(api,"sidebar_insights")||(api.sidebar_insights=new api.Values,api.sidebar_insights.create("candidates"),api.sidebar_insights.create("actives"),api.sidebar_insights.create("inactives"),api.sidebar_insights.create("registered"),api.sidebar_insights.create("available_locations")),this.listenToSidebarInsights(),api.czr_widgetZoneSettings=api.czr_widgetZoneSettings||new api.Value,api.czr_widgetZoneSettings.bind(function(a,b){c.isReady.then(function(){_.each(a,function(a,b){api.sidebar_insights(b).set(a)})})}),c.preItem_location_alert_view_state=new api.Value("closed"),c.preItem_location_alert_view_state.callbacks.add(function(a,b){c._toggleLocationAlertExpansion(c.container,a)}),c.bind("item_added",function(a){c.addWidgetSidebar(a)}),c.bind("pre_item_api_remove",function(a){c.removeWidgetSidebar(a)});var d=new api.Values;d.create("fixed_for_current_session"),d.create("value"),api.section(c.serverParams.dynWidgetSection).fixTopMargin=d,api.section(c.serverParams.dynWidgetSection).fixTopMargin("fixed_for_current_session").set(!1),api.panel("widgets").expanded.callbacks.add(function(a,b){c.widgetPanelReact(),"resolved"!=c.isReady.state()&&c.ready()})},ready:function(){var a=this;api.CZRDynModule.prototype.ready.call(a),a.preItemExpanded.callbacks.add(function(b,c){b&&(a.preItem.czr_Input("locations")._setupLocationSelect(!0),a.preItem.czr_Input("locations").mayBeDisplayModelAlert())})},initializeModuleModel:function(a){var b=this,c=$.Deferred();return a.items=_.union(_.has(b.serverParams,"default_zones")?b.serverParams.default_zones:[],a.items),c.resolve(a).promise()},CZRWZonesInputMths:{ready:function(){var a=this;a.bind("locations:changed",function(){a.mayBeDisplayModelAlert()}),api.CZRInput.prototype.ready.call(a)},setupSelect:function(){"locations"==this.id&&this._setupLocationSelect(),"contexts"==this.id&&this._setupContextSelect()},_setupContextSelect:function(){var a=this,b=a(),c=(a.input_parent,a.module);_.each(c.contexts,function(c,d){var e={value:d,html:c};(d==b||_.contains(b,d))&&$.extend(e,{selected:"selected"}),$('select[data-type="contexts"]',a.container).append($("<option>",e))}),$('select[data-type="contexts"]',a.container).select2()},_setupLocationSelect:function(a){function b(a){if(!a.id)return a.text;if(_.contains(f,a.element.value))return a.text;var b=$('<span class="czr-unavailable-location fa fa-ban" title="'+serverControlParams.translatedStrings.unavailableLocation+'">&nbsp;&nbsp;'+a.text+"</span>");return b}var c=this,d=c(),e=(c.input_parent,c.module),f=api.sidebar_insights("available_locations")();$('select[data-type="locations"]',c.container).children().length||_.each(e.locations,function(a,b){var e={value:b,html:a};(b==d||_.contains(d,b))&&$.extend(e,{selected:"selected"}),$('select[data-type="locations"]',c.container).append($("<option>",e))}),a&&$('select[data-type="locations"]',c.container).select2("destroy"),$('select[data-type="locations"]',c.container).select2({templateResult:b,templateSelection:b})},mayBeDisplayModelAlert:function(){var a=this,b=a.input_parent,c=a.module;if(_.has(b(),"locations")&&!_.isEmpty(b().locations)){var d=$('select[data-type="locations"]',a.container).val(),e=api.sidebar_insights("available_locations")(),f=_.filter(d,function(a){return!_.contains(e,a)});!_.has(b(),"id")||_.isEmpty(b().id)?c.preItem_location_alert_view_state.set(_.isEmpty(f)?"closed":"expanded"):b.czr_itemLocationAlert.set(_.isEmpty(f)?"closed":"expanded")}}},CZRWZonesItem:{initialize:function(a,b){var c=this;c.module;c.czr_itemLocationAlert=new api.Value,api.CZRItem.prototype.initialize.call(c,null,b)},itemWrapperViewSetup:function(){var a=this,b=a.module;api.CZRItem.prototype.itemWrapperViewSetup.call(a),a.czr_itemLocationAlert.set("closed"),a.czr_itemLocationAlert.callbacks.add(function(c,d){b._toggleLocationAlertExpansion(a.container,c)}),a.writeSubtitleInfos(a()),a.czr_ItemState.callbacks.add(function(b,c){-1!=b.indexOf("expanded")&&a.bind("contentRendered",function(){a.czr_Input("locations")._setupLocationSelect(!0),a.czr_Input("locations").mayBeDisplayModelAlert()})})},itemReact:function(a,b){var c=this;api.CZRItem.prototype.itemReact.call(c,a,b),c.writeSubtitleInfos(a),c.updateSectionTitle(a).setModelUpdateTimer()},writeSubtitleInfos:function(a){var b=this,c=b.module,d=_.clone(a||b()),e=[],f=[],g="";if(!b.container.length)return this;d.locations=_.isString(d.locations)?[d.locations]:d.locations,_.each(d.locations,function(a){_.has(c.locations,a)?e.push(c.locations[a]):e.push(a)}),d.contexts=_.isString(d.contexts)?[d.contexts]:d.contexts,b._hasModelAllContexts(a)?f.push(c.contexts._all_):_.each(d.contexts,function(a){_.has(c.contexts,a)?f.push(c.contexts[a]):f.push(a)});var h=serverControlParams.translatedStrings.locations,i=serverControlParams.translatedStrings.contexts,j=serverControlParams.translatedStrings.notset;if(e=_.isEmpty(e)?'<span style="font-weight: bold;">'+j+"</span>":e.join(", "),f=_.isEmpty(f)?'<span style="font-weight: bold;">'+j+"</span>":f.join(", "),g="<u>"+h+"</u> : "+e+" <strong>|</strong> <u>"+i+"</u> : "+f,$(".czr-zone-infos",b.container).length)$(".czr-zone-infos",b.container).html(g);else{var k=$("<div/>",{"class":["czr-zone-infos",c.control.css_attr.item_sort_handle].join(" "),html:g});$("."+c.control.css_attr.item_btns,b.container).after(k)}return this},updateSectionTitle:function(a){var b="sidebar-widgets-"+a.id,c=a.title;return api.section.has(b)?($(".accordion-section-title",api.section(b).container).text(c),$(".customize-section-title h3",api.section(b).container).html('<span class="customize-action">'+api.section(b).params.customizeAction+"</span>"+c),this):this},setModelUpdateTimer:function(){var a=this,b=a.module;clearTimeout($.data(this,"modelUpdateTimer")),$.data(this,"modelUpdateTimer",setTimeout(function(){b.control.refreshPreview()},1e3))},_hasModelAllContexts:function(a){var b=this,c=b.module,d=_.keys(c.contexts);if(a=a||this(),_.has(a,"contexts"))return!!_.contains(a.contexts,"_all_")||_.isEmpty(_.difference(_.without(d,"_all_"),a.contexts))},_getMatchingContexts:function(a){var b=this,c=api.czr_wpQueryInfos().conditional_tags||{},d=_.filter(b.context_match_map,function(a,b){return!0===c[b]});return _.isEmpty(d)?a:d}},addWidgetSidebar:function(a,b){if(!_.isObject(a)&&_.isEmpty(b))throw new Error("No valid input were provided to add a new Widget Zone.");var c=this,d=_.isEmpty(a)?b:_.clone(a),e=_.isEmpty(a)?b:$.extend(_.clone(_.findWhere(api.Widgets.data.registeredSidebars,{id:c.serverParams.defaultWidgetSidebar})),{name:d.title,id:d.id});api.Widgets.registeredSidebars.add(e);var f=$.extend(_.clone(api.section("sidebar-widgets-"+c.serverParams.defaultWidgetSidebar).params),{id:"sidebar-widgets-"+d.id,instanceNumber:_.max(api.settings.sections,function(a){return a.instanceNumber}).instanceNumber+1,sidebarId:e.id,title:e.name,
description:"undefined"!=typeof b?b.description:api.section("sidebar-widgets-"+c.serverParams.defaultWidgetSidebar).params.description,priority:_.max(_.omit(api.settings.sections,c.serverParams.dynWidgetSection),function(a){return a.instanceNumber}).priority+1});api.section.add(f.id,new api.sectionConstructor[f.type](f.id,{params:f})),api.settings.sections[f.id]=f.id;var g="sidebars_widgets["+d.id+"]",h=$.extend(_.clone(api.settings.settings["sidebars_widgets["+c.serverParams.defaultWidgetSidebar+"]"]),{value:[]});api.settings.settings[g]=h,api.create(g,g,h.value,{transport:h.transport,previewer:api.previewer,dirty:!1});var i=$.extend(_.clone(api.settings.controls["sidebars_widgets["+c.serverParams.defaultWidgetSidebar+"]"]),{settings:{"default":g}}),j={};_.each(i,function(a,b){"string"==typeof a&&(a=a.replace(c.serverParams.defaultWidgetSidebar,d.id)),j[b]=a}),j.instanceNumber=_.max(api.settings.controls,function(a){return a.instanceNumber}).instanceNumber+1,api.settings.controls[g]=j,api.control.add(g,new api.controlConstructor[j.type](g,{params:j,previewer:api.previewer})),_.has(this,"container")&&this.container.trigger("widget_zone_created",{model:d,section_id:"sidebar-widgets-"+d.id,setting_id:g})},removeWidgetSidebar:function(a){var b=this;if(!_.isObject(a)||_.isEmpty(a))throw new Error("No valid data were provided to remove a Widget Zone.");api.Widgets.registeredSidebars.remove(a.id),api.section.has("sidebar-widgets-"+a.id)&&(api.section("sidebar-widgets-"+a.id).container.remove(),api.section.remove("sidebar-widgets-"+a.id),delete api.settings.sections["sidebar-widgets-"+a.id]),api.has("sidebars_widgets["+a.id+"]")&&(api.remove("sidebars_widgets["+a.id+"]"),delete api.settings.settings["sidebars_widgets["+a.id+"]"]),api.control.has("sidebars_widgets["+a.id+"]")&&(api.control("sidebars_widgets["+a.id+"]").container.remove(),api.control.remove("sidebars_widgets["+a.id+"]"),delete api.settings.controls["sidebars_widgets["+a.id+"]"]);var c=function(){api.previewer.refresh()};c=_.debounce(c,500),$.when(c()).done(function(){b.trigger("widget_zone_removed",{model:a,section_id:"sidebar-widgets-"+a.id,setting_id:"sidebars_widgets["+a.id+"]"})})},widgetPanelReact:function(){var a=this,b=api.panel("widgets").container.find(".control-panel-content").css("margin-top");api.section(a.serverParams.dynWidgetSection).fixTopMargin("value").set(b);var c=api.section(a.serverParams.dynWidgetSection).container.find(".accordion-section-content"),d=api.panel("widgets").container.find(".control-panel-content"),e=function(){c.css("margin-top",""),d.css("margin-top",api.section(a.serverParams.dynWidgetSection).fixTopMargin("value")())};api.bind("pane-contents-reflowed",_.debounce(function(){e()},150)),a.closeAllItems(),_.has(a,"preItemExpanded")&&a.preItemExpanded.set(!1)},widgetSectionReact:function(a,b){var c=this,d=api.section(c.serverParams.dynWidgetSection),e=d.container.closest(".wp-full-overlay-sidebar-content"),f=d.container.find(".accordion-section-content"),g=d.container.closest(".wp-full-overlay"),h=(d.container.find(".customize-section-back"),d.container.find(".accordion-section-title").first());$("#customize-header-actions").height();a&&(g.removeClass("section-open"),f.css("height","auto"),h.attr("tabindex","0"),f.css("margin-top",""),e.scrollTop(0)),c.closeAllItems(),f.slideToggle()},listenToSidebarInsights:function(){var a=this;api.sidebar_insights("registered").callbacks.add(function(b){var c=_.clone(a.itemCollection());_.map(c,function(c){a.getViewEl(c.id).length&&a.getViewEl(c.id).css("display",_.contains(b,c.id)?"block":"none")})}),api.sidebar_insights("inactives").callbacks.add(function(b){var c=_.clone(a.itemCollection());_.map(c,function(c){a.getViewEl(c.id).length&&(_.contains(b,c.id)?(a.getViewEl(c.id).addClass("inactive"),a.getViewEl(c.id).find(".czr-inactive-alert").length||a.getViewEl(c.id).find(".czr-item-title").append($("<span/>",{"class":"czr-inactive-alert",html:" [ "+serverControlParams.translatedStrings.inactiveWidgetZone+" ]"}))):(a.getViewEl(c.id).removeClass("inactive"),a.getViewEl(c.id).find(".czr-inactive-alert").length&&a.getViewEl(c.id).find(".czr-inactive-alert").remove()))})}),api.sidebar_insights("candidates").callbacks.add(function(b){_.isArray(b)&&_.map(b,function(b){_.isObject(b)&&(api.section.has("sidebar-widgets-"+b.id)||(a.addWidgetSidebar({},b),_.has(api.sidebar_insights("actives")(),b.id)&&api.section.has("sidebar-widgets-"+b.id)&&api.section("sidebar-widgets-"+b.id).activate()))})})},_adjustScrollExpandedBlock:function(a,b){if(a.length){var c,d=$(".wp-full-overlay-sidebar-content").scrollTop(),e=b||90;setTimeout(function(){a.offset().top+a.height()+e>$(window.top).height()&&(c=a.offset().top+a.height()+e-$(window.top).height(),$(".wp-full-overlay-sidebar-content").animate({scrollTop:d+c},600))},50)}},getDefaultItemModel:function(a){var b=this,c=b.itemCollection(),d=_.clone(b.defaultItemModel);d.contexts;return $.extend(d,{title:"Widget Zone "+(1*_.size(c)+1)})},getTemplateEl:function(a,b){var c,d=this;switch("rudItemPart"==a?a=_.has(b,"is_builtin")&&b.is_builtin?"ruItemPart":a:"itemInputList"==a&&(a=_.has(b,"is_builtin")&&b.is_builtin?"itemInputListReduced":a),a){case"rudItemPart":c=d.rudItemPart;break;case"ruItemPart":c=d.ruItemPart;break;case"itemInputList":c=d.itemInputList;break;case"itemInputListReduced":c=d.itemInputListReduced}if(_.isEmpty(c))throw new Error("No valid template has been found in getTemplateEl()");return c},_toggleLocationAlertExpansion:function(a,b){var c=a.find(".czr-location-alert");if(!c.length){var d=["<span>"+serverControlParams.translatedStrings.locationWarning+"</span>",api.CZR_Helpers.getDocSearchLink(serverControlParams.translatedStrings.locationWarning)].join("");c=$("<div/>",{"class":"czr-location-alert",html:d,style:"display:none"}),$('select[data-type="locations"]',a).closest("div").after(c)}c.toggle("expanded"==b)}});var CZRBodyBgModuleMths=CZRBodyBgModuleMths||{};$.extend(CZRBodyBgModuleMths,{initialize:function(a,b){var c=this;api.CZRModule.prototype.initialize.call(c,a,b),$.extend(c,{itemInputList:"czr-module-bodybg-item-content"}),c.inputConstructor=api.CZRInput.extend(c.CZRBodyBgInputMths||{}),c.itemConstructor=api.CZRItem.extend(c.CZBodyBgItemMths||{}),c.defaultItemModel={"background-color":"#eaeaea","background-image":"","background-repeat":"no-repeat","background-attachment":"fixed","background-position":"center center","background-size":"cover"},api.consoleLog("module ID",c.id),_.has(api,"czr_activeSectionId")&&c.control.section()==api.czr_activeSectionId()&&"resolved"!=c.isReady.state()&&c.ready(),api.section(c.control.section()).expanded.bind(function(a){"resolved"!=c.isReady.state()&&c.ready()})},CZRBodyBgInputMths:{setupSelect:function(){var a=this,b={"background-repeat":"bg_repeat_options","background-attachment":"bg_attachment_options","background-position":"bg_position_options"},c=(a.input_parent,serverControlParams.body_bg_module_params),d={};a.module;_.has(b,a.id)&&(_.isUndefined(c)||_.isUndefined(c[b[a.id]])||(d=c[b[a.id]],_.isEmpty(d)||(_.each(d,function(b,c){var d={value:c,html:b};(c==a()||_.contains(a(),c))&&$.extend(d,{selected:"selected"}),$("select[data-type]",a.container).append($("<option>",d))}),$("select[data-type]",a.container).select2())))}},CZBodyBgItemMths:{ready:function(){var a=this;api.CZRItem.prototype.ready.call(a),a.czr_Input("background-image").isReady.done(function(b){var c=function(b){var c=!_.isEmpty(b)||_.isNumber(b);_.each(["background-repeat","background-attachment","background-position","background-size"],function(b){a.czr_Input(b).container.toggle(c||!1)})};c(b()),a.bind("background-image:changed",function(){c(a.czr_Input("background-image")())})})}}}),function(a,b,c){a.czrModuleMap=a.czrModuleMap||{},b.extend(a.czrModuleMap,{czr_widget_areas_module:{mthds:CZRWidgetAreaModuleMths,crud:!0,sektion_allowed:!1,name:"Widget Areas"},czr_social_module:{mthds:CZRSocialModuleMths,crud:!0,sortable:!0,name:"Social Icons"},czr_background:{mthds:CZRBodyBgModuleMths,crud:!1,multi_item:!1,name:"Slider"}})}(wp.customize,jQuery,_);var CZRBaseControlMths=CZRBaseControlMths||{};$.extend(CZRBaseControlMths,{initialize:function(a,b){var c=this;c.css_attr=_.has(serverControlParams,"css_attr")?serverControlParams.css_attr:{},api.Control.prototype.initialize.call(c,a,b)},refreshPreview:function(a){this.previewer.refresh()}});var CZRBaseModuleControlMths=CZRBaseModuleControlMths||{};$.extend(CZRBaseModuleControlMths,{initialize:function(a,b){var c=this;c.czr_Module=new api.Values,c.czr_moduleCollection=new api.Value,c.czr_moduleCollection.set([]),c.moduleCollectionReady=$.Deferred(),c.moduleCollectionReady.done(function(a){!c.isMultiModuleControl(b.params),c.czr_moduleCollection.callbacks.add(function(){return c.moduleCollectionReact.apply(c,arguments)})}),c.isMultiModuleControl(b.params)&&(c.syncSektionModule=new api.Value),api.CZRBaseControl.prototype.initialize.call(c,a,b)},ready:function(){var a=this;if(a.isMultiModuleControl())a.syncSektionModule.bind(function(b,c){"resolved"!=a.moduleCollectionReady.state()&&(a.registerModulesOnInit(b),a.moduleCollectionReady.resolve())});else{var b={};_.each(a.getSavedModules(),function(c,d){b=c,a.instantiateModule(c,{}),a.container.attr("data-module",c.id)}),a.moduleCollectionReady.resolve(b)}a.bind("user-module-candidate",function(b){a.instantiateModule(b,{}).ready(b.is_added_by_user)})},getDefaultModuleApiModel:function(){var a={id:"",module_type:"",modOpt:{},items:[],crud:!1,multi_item:!1,sortable:!1,control:{}};return this.isMultiModuleControl()?$.extend(a,{column_id:"",sektion:{},sektion_id:"",is_added_by_user:!1,dirty:!1}):$.extend(a,{section:""})},getDefaultModuleDBModel:function(){var a={items:[]};return this.isMultiModuleControl()?$.extend(a,{id:"",module_type:"",column_id:"",sektion_id:"",dirty:!1}):a},isMultiModuleControl:function(a){return"czr_multi_module"==(a||this.params).type},getSyncCollectionControl:function(){var a=this;if(_.isUndefined(a.params.syncCollection))throw new Error("Control "+a.id+" has no synchronized sektion control defined.");return api.control(api.CZR_Helpers.build_setId(a.params.syncCollection))},getSavedModules:function(){var a=this,b=[],c=a.params.module_type,d=[],e=[],f={};return a.isMultiModuleControl()?b=$.extend(!0,[],api(a.id)()):(!api.CZR_Helpers.isMultiItemModule(c)||_.isEmpty(api(a.id)())||_.isObject(api(a.id)())||api.consoleLog("Module Control Init for "+a.id+"  : a mono item module control value should be an object if not empty."),d=_.isArray(api(a.id)())?api(a.id)():[api(a.id)()],_.each(d,function(b,d){if(api.CZR_Helpers.hasModuleModOpt(c)&&0===d){if(_.has(b,"id"))throw new Error("getSavedModules : the module "+c+" in control "+a.id+" has no mod_opt defined while it should.");f=b}_.has(b,"id")&&!_.has(b,"is_mod_opt")&&e.push(b)}),b.push({id:api.CZR_Helpers.getOptionName(a.id)+"_"+a.params.type,module_type:a.params.module_type,section:a.section(),modOpt:$.extend(!0,{},f),items:$.extend(!0,[],e)})),b},isModuleRegistered:function(a){var b=this;return!_.isUndefined(_.findWhere(b.czr_moduleCollection(),{id:a}))}});var CZRBaseModuleControlMths=CZRBaseModuleControlMths||{};$.extend(CZRBaseModuleControlMths,{instantiateModule:function(a,b){if(!_.has(a,"id"))throw new Error("CZRModule::instantiateModule() : a module has no id and could not be added in the collection of : "+this.id+". Aborted.");var c=this;if((_.isUndefined(b)||_.isEmpty(b))&&(b=c.getModuleConstructor(a)),!_.isEmpty(a.id)&&c.czr_Module.has(a.id))throw new Error("The module id already exists in the collection in control : "+c.id);var d=c.prepareModuleForAPI(a);if(c.czr_Module.add(d.id,new b(d.id,d)),!c.czr_Module.has(d.id))throw new Error("instantiateModule() : instantiation failed for module id "+d.id+" in control "+c.id);return c.czr_Module(d.id)},getModuleConstructor:function(a){var b=this,c={},d={};if(!_.has(a,"module_type"))throw new Error("CZRModule::getModuleConstructor : no module type found for module "+a.id);if(!_.has(api.czrModuleMap,a.module_type))throw new Error("Module type "+a.module_type+" is not listed in the module map api.czrModuleMap.");var e=api.czrModuleMap[a.module_type].mthds,f=api.czrModuleMap[a.module_type].crud,g=f?api.CZRDynModule:api.CZRModule;if(_.isEmpty(a.sektion_id)?d=g.extend(e):(c=g.extend(e),d=c.extend(b.getMultiModuleExtender(c))),_.isUndefined(d)||_.isEmpty(d)||!d)throw new Error("CZRModule::getModuleConstructor : no constructor found for module type : "+a.module_type+".");return d},prepareModuleForAPI:function(a){if(!_.isObject(a))throw new Error("prepareModuleForAPI : a module must be an object to be instantiated.");var b=this,c={};return _.each(b.getDefaultModuleApiModel(),function(d,e){var f=a[e];switch(e){case"id":_.isEmpty(f)?c[e]=b.generateModuleId(a.module_type):c[e]=f;break;case"module_type":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForAPI : a module type must a string not empty");c[e]=f;break;case"items":if(!_.isArray(f))throw new Error("prepareModuleForAPI : a module item list must be an array");c[e]=f;break;case"modOpt":if(!_.isObject(f))throw new Error("prepareModuleForAPI : a module modOpt property must be an object");c[e]=f;break;case"crud":if(_.has(api.czrModuleMap,a.module_type))f=api.czrModuleMap[a.module_type].crud;else if(!_.isUndefined(f)&&!_.isBoolean(f))throw new Error('prepareModuleForAPI : the module param "crud" must be a boolean');c[e]=f||!1;break;case"multi_item":if(_.has(api.czrModuleMap,a.module_type))f=api.czrModuleMap[a.module_type].crud||api.czrModuleMap[a.module_type].multi_item;else if(!_.isUndefined(f)&&!_.isBoolean(f))throw new Error('prepareModuleForAPI : the module param "multi_item" must be a boolean');c[e]=f||!1;break;case"sortable":if(_.has(api.czrModuleMap,a.module_type))f=api.czrModuleMap[a.module_type].sortable||api.czrModuleMap[a.module_type].crud||api.czrModuleMap[a.module_type].multi_item;else if(!_.isUndefined(f)&&!_.isBoolean(f))throw new Error('prepareModuleForAPI : the module param "sortable" must be a boolean');c[e]=f||!1;break;case"control":c[e]=b;break;case"section":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForAPI : a module section must be a string not empty");c[e]=f;break;case"column_id":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForAPI : a module column id must a string not empty");c[e]=f;break;case"sektion":if(!_.isObject(f)||_.isEmpty(f))throw new Error("prepareModuleForAPI : a module sektion must be an object not empty");c[e]=f;break;case"sektion_id":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForAPI : a module sektion id must be a string not empty");c[e]=f;break;case"is_added_by_user":if(!_.isUndefined(f)&&!_.isBoolean(f))throw new Error('prepareModuleForAPI : the module param "is_added_by_user" must be a boolean');c[e]=f||!1;break;case"dirty":c[e]=f||!1}}),c},generateModuleId:function(a,b,c){if(c=c||1,c>100)throw new Error("Infinite loop when generating of a module id.");var d=this;b=b||d._getNextModuleKeyInCollection();var e=a+"_"+b;if(!_.has(d,"czr_moduleCollection")||!_.isArray(d.czr_moduleCollection()))throw new Error("The module collection does not exist or is not properly set in control : "+d.id);return d.isModuleRegistered(e)?(b++,c++,d.generateModuleId(a,b,c)):e},_getNextModuleKeyInCollection:function(){var a=this,b={},c=0;return _.isEmpty(a.czr_moduleCollection())||(b=_.max(a.czr_moduleCollection(),function(a){return parseInt(a.id.replace(/[^\/\d]/g,""),10)}),c=parseInt(b.id.replace(/[^\/\d]/g,""),10)+1),c}});var CZRBaseModuleControlMths=CZRBaseModuleControlMths||{};$.extend(CZRBaseModuleControlMths,{registerModulesOnInit:function(a){var b=this,c=[];_.each(b.getSavedModules(),function(d,e){if(!a.czr_Item.has(d.sektion_id))return api.consoleLog("Warning Module "+d.id+" is orphan : it has no sektion to be embedded to. It Must be removed."),void c.push(d);var f=a.czr_Item(d.sektion_id);if(_.isUndefined(f))throw new Error("sektion instance missing. Impossible to instantiate module : "+d.id);$.extend(d,{sektion:f}),b.updateModulesCollection({module:d})}),b.moduleCollectionReady.then(function(){_.isEmpty(c)||b.moduleCollectionReact(b.czr_moduleCollection(),[],{orphans_module_removal:c})})},updateModulesCollection:function(a){var b=this,c=b.czr_moduleCollection(),d=$.extend(!0,[],c);if(_.has(a,"collection"))return void b.czr_moduleCollection.set(a.collection,a.data||{});if(!_.has(a,"module"))throw new Error("updateModulesCollection, no module provided "+b.id+". Aborting");var e=b.prepareModuleForAPI(_.clone(a.module));_.findWhere(d,{id:e.id})?_.each(c,function(a,b){a.id==e.id&&(d[b]=e)}):d.push(e);var f={};_.has(a,"data")&&(f=$.extend(!0,{},a.data),$.extend(f,{module:e})),b.czr_moduleCollection.set(d,f)},moduleCollectionReact:function(a,b,c){var d=this,e=_.size(a)>_.size(b),f=_.size(b)>_.size(a);_.size(b)==_.size(a);if(is_collection_sorted=!1,f){var g=_.filter(b,function(b){return _.isUndefined(_.findWhere(a,{id:b.id}))});g=g[0],d.czr_Module.remove(g.id)}_.isObject(c)&&_.has(c,"module")&&(c.module=d.prepareModuleForDB($.extend(!0,{},c.module))),!d.isMultiModuleControl()&&e||api(this.id).set(d.filterModuleCollectionBeforeAjax(a),c)},filterModuleCollectionBeforeAjax:function(a){var b,c=this,d=$.extend(!0,[],a);if(_.each(a,function(a,b){var e=$.extend(!0,{},a);d[b]=c.prepareModuleForDB(e)}),c.isMultiModuleControl())return d;if(_.size(a)>1)throw new Error("There should not be several modules in the collection of control : "+c.id);if(!_.isArray(a)||_.isEmpty(a)||!_.has(a[0],"items"))throw new Error("The setting value could not be populated in control : "+c.id);var e=a[0].id;if(!c.czr_Module.has(e))throw new Error("The single module control ("+c.id+") has no module registered with the id "+e);var f=c.czr_Module(e);if(!_.isArray(f().items))throw new Error("The module "+e+" should be an array in control : "+c.id);return b=f.isMultiItem()?f().items:f().items[0]||[],f.hasModOpt()?_.union([f().modOpt],b):b},prepareModuleForDB:function(a){if(!_.isObject(a))throw new Error("MultiModule Control::prepareModuleForDB : a module must be an object. Aborting.");var b=this,c={};return _.each(b.getDefaultModuleDBModel(),function(d,e){if(!_.has(a,e))throw new Error("MultiModule Control::prepareModuleForDB : a module is missing the property : "+e+" . Aborting.");var f=a[e];switch(e){case"items":if(!_.isArray(f))throw new Error("prepareModuleForDB : a module item list must be an array");c[e]=f;break;case"id":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForDB : a module id must a string not empty");c[e]=f;break;case"module_type":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForDB : a module type must a string not empty");c[e]=f;break;case"column_id":if(!_.isString(f)||_.isEmpty(f))throw new Error("prepareModuleForDB : a module column id must a string not empty");c[e]=f;break;case"sektion_id":if(!_.isObject(a.sektion)||!_.has(a.sektion,"id"))throw new Error("prepareModuleForDB : a module sektion must be an object with an id.");c[e]=a.sektion.id;break;case"dirty":if(b.czr_Module.has(a.id)?c[e]=b.czr_Module(a.id).isDirty():c[e]=f,!_.isBoolean(c[e]))throw new Error("prepareModuleForDB : a module dirty state must be a boolean.")}}),c}});var CZRMultiModuleControlMths=CZRMultiModuleControlMths||{};$.extend(CZRMultiModuleControlMths,{initialize:function(a,b){var c=this;api.consoleLog("IN MULTI MODULE INITIALIZE ? ",b),api(a).callbacks.add(function(){return c.syncColumn.apply(c,arguments)}),api.CZRBaseModuleControl.prototype.initialize.call(c,a,b)},ready:function(){var a=this;api.consoleLog("MODULE-COLLECTION CONTROL READY",this.id),api.CZRBaseModuleControl.prototype.ready.apply(a,arguments)},syncColumn:function(a,b,c){if(api.consoleLog("IN SYNC COLUMN",a,b,c),(_.isUndefined(c)||!c.silent)&&(api.consoleLog("IN SYNXXX",api.control("hu_theme_options[module-collection]").syncSektionModule()(),this.syncSektionModule()(),this.id),!_.has(c,"orphans_module_removal"))){var d=api.control(this.id),e=_.filter(a,function(a,c){return!_.findWhere(b,{id:a.id})});_.isEmpty(e)||(api.consoleLog("ADDED MODULE?",e),_.each(e,function(a){d.syncSektionModule().czr_Column(a.column_id).updateColumnModuleCollection({module:a})}));var f=_.filter(b,function(b,c){return!_.findWhere(a,{id:b.id})});_.isEmpty(f)||_.each(f,function(a){d.syncSektionModule().czr_Column(a.column_id).removeModuleFromColumnCollection(a)}),_.size(b)==_.size(a)&&_.has(c,"module")&&_.has(c,"source_column")&&_.has(c,"target_column")&&$.when(d.syncSektionModule().moveModuleFromTo(c.module,c.source_column,c.target_column)).done(function(){d.syncSektionModule().control.trigger("module-moved",{module:c.module,source_column:c.source_column,target_column:c.target_column})}),d.trigger("columns-synchronized",a)}},removeModule:function(a){var b=this;b.czr_Module.has(a.id)&&"resolved"==b.czr_Module(a.id).embedded.state()&&b.czr_Module(a.id).container.remove(),b.removeModuleFromCollection(a)},removeModuleFromCollection:function(a){var b=this,c=b.czr_moduleCollection(),d=$.extend(!0,[],c);d=_.filter(d,function(b){return b.id!=a.id}),b.czr_moduleCollection.set(d)}});var CZRMultiModuleControlMths=CZRMultiModuleControlMths||{};$.extend(CZRMultiModuleControlMths,{getMultiModuleExtender:function(a){var b=this;return $.extend(b.CZRModuleExtended,{initialize:function(b,c){var d=this;a.prototype.initialize.call(d,b,c),api.consoleLog("MODULE INSTANTIATED : ",d.id),$.extend(d,{singleModuleWrapper:"czr-single-module-wrapper",sektionModuleTitle:"czr-module-sektion-title-part",ruModuleEl:"czr-ru-module-sektion-content"}),d.czr_ModuleState=new api.Value((!1)),d.isReady.done(function(){d.setupModuleView()}),d.moduleTitleEmbedded=$.Deferred(),d.modColumn=new api.Value,d.modColumn.set(c.column_id),d.modColumn.bind(function(a,b){api.consoleLog("MODULE "+d.id+" HAS BEEN MOVED TO COLUMN",a,d());var c=d(),e=$.extend(!0,{},c);e.column_id=a,d.set(e,{target_column:a,source_column:b})})},ready:function(b){var c=this;api.consoleLog("MODULE READY IN EXTENDED MODULE CLASS : ",c.id),$.when(c.renderModuleWrapper(b)).done(function(a){if(_.isUndefined(a)||!1===a.length)throw new Error("Module container has not been embedded for module :"+c.id);c.container=a,c.embedded.resolve()}),a.prototype.ready.call(c)}}),b.CZRModuleExtended},CZRModuleExtended:{renderModuleWrapper:function(a){var b=this;if("resolved"==b.embedded.state())return b.container;if(0===$("#tmpl-"+b.singleModuleWrapper).length)throw new Error("No template for module "+b.id+". The template script id should be : #tmpl-"+b.singleModuleWrapper);var c=wp.template(b.singleModuleWrapper),d={id:b.id,type:b.module_type},e=$(c(d));return a?$.when($(".czr-module-collection-wrapper",b._getColumn().container).find(".czr-module-candidate").after(e)).done(function(){$(".czr-module-collection-wrapper",b._getColumn().container).find(".czr-module-candidate").remove()}):$(".czr-module-collection-wrapper",b._getColumn().container).append(e),e},setupModuleView:function(){var a=this;a.view_event_map=[{trigger:"click keydown",selector:[".czr-remove-mod","."+a.control.css_attr.cancel_alert_btn].join(","),name:"toggle_remove_alert",actions:["toggleModuleRemoveAlert"]},{trigger:"click keydown",selector:"."+a.control.css_attr.remove_view_btn,name:"remove_module",actions:["removeModule"]},{trigger:"click keydown",selector:".czr-edit-mod",name:"edit_module",actions:["setModuleViewVisibility","sendEditModule"]},{trigger:"click keydown",selector:".czr-module-back",name:"back_to_column",actions:["setModuleViewVisibility"]},{trigger:"mouseenter",selector:".czr-mod-header",name:"hovering_module",actions:function(b){a.control.previewer.send("start_hovering_module",{id:a.id})}},{trigger:"mouseleave",selector:".czr-mod-header",name:"hovering_module",actions:function(b){a.control.previewer.send("stop_hovering_module",{id:a.id})}}],a.embedded.done(function(){a.czr_ModuleState.callbacks.add(function(){return a.setupModuleViewStateListeners.apply(a,arguments)}),api.CZR_Helpers.setupDOMListeners(a.view_event_map,{module:{id:a.id},dom_el:a.container},a)})},setModuleViewVisibility:function(a,b){var c=this;c.czr_ModuleState(!c.czr_ModuleState()),api.czrModulePanelState.set(!1),api.czrSekSettingsPanelState.set(!1),c.control.syncSektionModule().closeAllOtherSektions($(a.dom_event.currentTarget,a.dom_el))},sendEditModule:function(a){var b=this;b.control.previewer.send("edit_module",{id:b.id})},setupModuleViewStateListeners:function(a){var b=this;api.czr_isModuleExpanded=api.czr_isModuleExpanded||new api.Value,a?api.czr_isModuleExpanded(b):api.czr_isModuleExpanded(!1),$.when(b.toggleModuleViewExpansion(a)).done(function(){a?(b.renderModuleTitle(),b.populateSavedItemCollection()):b.czr_Item.each(function(a){a.czr_ItemState.set("closed"),a._destroyView(0),b.czr_Item.remove(a.id)})})},renderModuleTitle:function(){var a=this;if("resolved"!=a.moduleTitleEmbedded.state()){if(0===$("#tmpl-"+a.sektionModuleTitle).length)throw new Error("No sektion title Module Part template for module "+a.id+". The template script id should be : #tmpl-"+a.sektionModuleTitle);$.when($(a.container).find(".czr-mod-content").prepend($(wp.template(a.sektionModuleTitle)({id:a.id})))).done(function(){a.moduleTitleEmbedded.resolve()})}},toggleModuleViewExpansion:function(a,b){var c=this;$(".czr-mod-content",c.container).slideToggle({duration:b||200,done:function(){var b=c.container.closest(".wp-full-overlay"),d=c.container.find(".czr-module-back"),e=c.container.find(".czr-module-title");c.container.toggleClass("open",a),b.toggleClass("czr-module-open",a),e.attr("tabindex",a?"-1":"0"),d.attr("tabindex",a?"0":"-1"),a?d.focus():e.focus(),a&&c._adjustScrollExpandedBlock(c.container)}})},toggleModuleRemoveAlert:function(a){var b=this,c=this.control,d=$("."+b.control.css_attr.remove_alert_wrapper,b.container).first(),e=(a.dom_event,c.syncSektionModule().czr_Column(b.column_id).container);if(_.has(b,"preItem")&&c.syncSektionModule().preItemExpanded.set(!1),$("."+b.control.css_attr.remove_alert_wrapper,e).not(d).each(function(){$(this).hasClass("open")&&$(this).slideToggle({duration:200,done:function(){$(this).toggleClass("open",!1),$(this).siblings().find("."+b.control.css_attr.display_alert_btn).toggleClass("active",!1)}})}),!wp.template(b.AlertPart)||!b.container)throw new Error("No removal alert template available for module :"+b.id);d.html(wp.template(b.AlertPart)({title:b().title||b.id})),d.slideToggle({duration:200,done:function(){var c=!$(this).hasClass("open")&&$(this).is(":visible");$(this).toggleClass("open",c),$(a.dom_el).find("."+b.control.css_attr.display_alert_btn).toggleClass("active",c),c&&b._adjustScrollExpandedBlock(b.container)}})},removeModule:function(a){this.control.removeModule(a.module)},_getColumn:function(){var a=this;return a.control.syncSektionModule().czr_Column(a.modColumn())},_getSektion:function(){}}});var CZRMultiplePickerMths=CZRMultiplePickerMths||{};$.extend(CZRMultiplePickerMths,{ready:function(){function a(a){return a.text.replace(/\u2013|\u2014/g,"")}var b=this,c=this.container.find("select");c.select2({closeOnSelect:!1,templateSelection:a}),c.on("change",function(a){0===$(this).find("option:selected").length&&b.setting.set([])})}});var CZRCroppedImageMths=CZRCroppedImageMths||{};!function(a,b,c){"function"==typeof wp.media.controller.Cropper&&"function"==typeof a.CroppedImageControl&&(wp.media.controller.CZRCustomizeImageCropper=wp.media.controller.Cropper.extend({doCrop:function(a){var b=a.get("cropDetails"),c=this.get("control");return b.dst_width=c.params.dst_width,b.dst_height=c.params.dst_height,wp.ajax.post("crop-image",{wp_customize:"on",nonce:a.get("nonces").edit,id:a.get("id"),context:c.id,cropDetails:b})}}),b.extend(CZRCroppedImageMths,{initFrame:function(){var a=_wpMediaViewsL10n;this.frame=wp.media({button:{text:a.select,close:!1},states:[new wp.media.controller.Library({title:this.params.button_labels.frame_title,library:wp.media.query({type:"image"}),multiple:!1,date:!1,priority:20,suggestedWidth:this.params.width,suggestedHeight:this.params.height}),new wp.media.controller.CZRCustomizeImageCropper({imgSelectOptions:this.calculateImageSelectOptions,control:this})]}),this.frame.on("select",this.onSelect,this),this.frame.on("cropped",this.onCropped,this),this.frame.on("skippedcrop",this.onSkippedCrop,this)},onSelect:function(){var a=this.frame.state().get("selection").first().toJSON();return a.mime&&a.mime.indexOf("image")>-1?void(c.contains(["image/svg+xml","image/gif"],a.mime)||this.params.width===a.width&&this.params.height===a.height&&!this.params.flex_width&&!this.params.flex_height?(this.setImageFromAttachment(a),this.frame.close()):this.frame.setState("cropper")):void this.frame.trigger("content:error")}}))}(wp.customize,jQuery,_);var CZRUploadMths=CZRUploadMths||{};$.extend(CZRUploadMths,{ready:function(){var a=this;this.params.removed=this.params.removed||"",this.success=$.proxy(this.success,this),this.uploader=$.extend({container:this.container,browser:this.container.find(".czr-upload"),success:this.success,plupload:{},params:{}},this.uploader||{}),a.params.extensions&&(a.uploader.plupload.filters=[{title:api.l10n.allowedFiles,extensions:a.params.extensions}]),a.params.context&&(a.uploader.params["post_data[context]"]=this.params.context),api.settings.theme.stylesheet&&(a.uploader.params["post_data[theme]"]=api.settings.theme.stylesheet),this.uploader=new wp.Uploader(this.uploader),this.remover=this.container.find(".remove"),this.remover.on("click keydown",function(b){"keydown"===b.type&&13!==b.which||(a.setting.set(a.params.removed),b.preventDefault())}),this.removerVisibility=$.proxy(this.removerVisibility,this),this.setting.bind(this.removerVisibility),this.removerVisibility(this.setting())},success:function(a){this.setting.set(a.get("id"))},removerVisibility:function(a){this.remover.toggle(a!=this.params.removed)}});var CZRLayoutSelectMths=CZRLayoutSelectMths||{};$.extend(CZRLayoutSelectMths,{ready:function(){this.setupSelect()},setupSelect:function(a){function b(a){if(!a.id)return a.text;if(_.has(c.params.layouts,a.element.value)){var b=c.params.layouts[a.element.value],d=b.src,e=b.label,f=$('<img src="'+d+'" class="czr-layout-img" title="'+e+'" /><span class="czr-layout-title">'+e+"</span>");return f}}var c=this;$_select=this.container.find("select"),$_select.select2({templateResult:b,templateSelection:b,minimumResultsForSearch:1/0})}}),function(a,b,c){b.extend(CZRBaseControlMths,a.Events),b.extend(CZRModuleMths,a.Events),b.extend(CZRItemMths,a.Events),b.extend(CZRModOptMths,a.Events),b.extend(CZRSkopeBaseMths,a.Events),b.extend(CZRSkopeMths,a.Events),b.extend(CZRBaseControlMths,a.CZR_Helpers),b.extend(CZRInputMths,a.CZR_Helpers),b.extend(CZRModuleMths,a.CZR_Helpers),b.extend(CZRSkopeMths,a.CZR_Helpers),a.CZR_skopeBase=a.Class.extend(CZRSkopeBaseMths),a.CZR_skopeSave=a.Class.extend(CZRSkopeSaveMths),a.CZR_skopeReset=a.Class.extend(CZRSkopeResetMths),a.CZR_skope=a.Value.extend(CZRSkopeMths),c.has(a,"HeaderTool")&&(a.czr_HeaderTool=b.extend(!0,{},a.HeaderTool)),a.CZRInput=a.Value.extend(CZRInputMths),a.CZRItem=a.Value.extend(CZRItemMths),a.CZRModOpt=a.Value.extend(CZRModOptMths),a.CZRModule=a.Value.extend(CZRModuleMths),a.CZRDynModule=a.CZRModule.extend(CZRDynModuleMths),c.isUndefined(window.CZRColumnMths)||(a.CZRColumn=a.Value.extend(CZRColumnMths)),a.CZRBaseControl=a.Control.extend(CZRBaseControlMths),a.CZRBaseModuleControl=a.CZRBaseControl.extend(CZRBaseModuleControlMths),a.CZRMultiModuleControl=a.CZRBaseModuleControl.extend(CZRMultiModuleControlMths),a.CZRUploadControl=a.Control.extend(CZRUploadMths),a.CZRLayoutControl=a.Control.extend(CZRLayoutSelectMths),a.CZRMultiplePickerControl=a.Control.extend(CZRMultiplePickerMths),b.extend(a.controlConstructor,{czr_upload:a.CZRUploadControl,czr_module:a.CZRBaseModuleControl,czr_multi_module:a.CZRMultiModuleControl,czr_multiple_picker:a.CZRMultiplePickerControl,czr_layouts:a.CZRLayoutControl}),"function"==typeof a.CroppedImageControl&&(a.CZRCroppedImageControl=a.CroppedImageControl.extend(CZRCroppedImageMths),
b.extend(a.controlConstructor,{czr_cropped_image:a.CZRCroppedImageControl})),a.czrInputMap=a.czrInputMap||{},b.extend(a.czrInputMap,{text:"",textarea:"",check:"setupIcheck",select:"setupSelect",number:"setupStepper",upload:"setupImageUploader",color:"setupColorPicker",content_picker:"setupContentPicker",text_editor:"setupTextEditor",password:"",range_slider:"setupRangeSlider"})}(wp.customize,jQuery,_),function(a,b,c){var d=serverControlParams.translatedStrings||{};a.czr_CrtlDependenciesReady=b.Deferred(),a.bind("ready",function(){c.has(a,"czr_ctrlDependencies")||(serverControlParams.isSkopOn?a.czr_skopeReady.done(function(){a.czr_ctrlDependencies=new a.CZR_ctrlDependencies,a.czr_CrtlDependenciesReady.resolve()}):(a.czr_ctrlDependencies=new a.CZR_ctrlDependencies,a.czr_CrtlDependenciesReady.resolve()))}),a.CZR_ctrlDependencies=a.Class.extend({dominiDeps:[],initialize:function(){var b=this;if(this.defaultDominusParams={dominus:"",servi:[],visibility:null,actions:null,onSectionExpand:!0},this.dominiDeps=c.extend(this.dominiDeps,this._getControlDeps()),!c.isArray(b.dominiDeps))throw new Error("Visibilities : the dominos dependency array is not an array.");a.czr_activeSectionId.bind(function(d){!c.isEmpty(d)&&a.section.has(d)&&b.setServiDependencies(d)}),a.bind("awaken-section",function(d){serverControlParams.isSkopOn&&c.has(a,"czr_skopeBase")?a.czr_skopeBase.processSilentUpdates({candidates:{},section_id:d.target,refresh:!1}).then(function(){b.setServiDependencies(d.target,d.source)}):b.setServiDependencies(d.target,d.source)}),this._handleFaviconNote()},setServiDependencies:function(d,e,f){var g=this,h=b.Deferred();if(f=f||!1,c.isUndefined(d)||!a.section.has(d))throw new Error("Control Dependencies : the targetSectionId is missing or not registered : "+d);if(a.section(d).czr_ctrlDependenciesReady=a.section(d).czr_ctrlDependenciesReady||b.Deferred(),!f&&"resolved"==a.section(d).czr_ctrlDependenciesReady.state())return h.resolve().promise();c.each(g.dominiDeps,function(b){if(!c.has(b,"dominus")||!c.isString(b.dominus)||c.isEmpty(b.dominus))throw new Error("Control Dependencies : a dominus control id must be a not empty string.");var e=a.CZR_Helpers.build_setId(b.dominus);if(a.control.has(e)&&a.control(e).section()==d){try{b=g._prepareDominusParams(b)}catch(i){return void a.consoleLog("prepareDominus Params error : "+i)}g._processDominusCallbacks(b.dominus,b,f).fail(function(){a.consoleLog("self._processDominusCallbacks fail for section "+d),h.reject()}).done(function(){h.resolve()})}});var i=a.CZR_Helpers.getSectionControlIds(d),j=function(b){var d=[];return c.each(g.dominiDeps,function(e){if(!c.has(e,"servi")||!c.isArray(e.servi)||!c.has(e,"dominus")||c.isEmpty(e.dominus))throw new Error("Control Dependencies : wrong params in _getServusDomini.");if(c.contains(e.servi,b)&&!c.contains(d,e.dominus)){try{e=g._prepareDominusParams(e)}catch(f){return void a.consoleLog("prepareDominus Params error : "+f)}d.push(e.dominus)}}),c.isArray(d)?d:[]},k=[];return c.each(i,function(a){c.isEmpty(j(a))||(k=c.union(k,j(a)))}),c.each(k,function(b){var c=a.CZR_Helpers.build_setId(b);a.control(c).section()!=d&&e!=a.control(c).section()&&a.trigger("awaken-section",{target:a.control(c).section(),source:d})}),h.always(function(){a.section(d).czr_ctrlDependenciesReady.resolve()}),h.promise()},_deferCallbackForControl:function(d,e,f){var g=b.Deferred();if(c.isEmpty(d)||!c.isString(d))throw new Error("_deferCallbackForControl : the control id is missing.");if(!c.isFunction(e))throw new Error("_deferCallbackForControl : callback must be a funtion.");return f=c.isUndefined(f)||!c.isArray(f)?[]:f,a.control.has(d)?"resolved"==a.control(d).deferred.embedded.state()?b.when(e.apply(null,f)).fail(function(){g.reject()}).done(function(){g.resolve()}):a.control(d).deferred.embedded.then(function(){b.when(e.apply(null,f)).fail(function(){g.reject()}).done(function(){g.resolve()})}):a.control.when(d,function(){a.control(d).deferred.embedded.then(function(){b.when(e.apply(null,f)).fail(function(){g.reject()}).done(function(){g.resolve()})})}),g.promise()},_processDominusCallbacks:function(d,e,f){var g=this,h=a.CZR_Helpers.build_setId(d),i=a(h),j=b.Deferred(),k=!1;return c.each(e.servi,function(b){if(a.control.has(a.CZR_Helpers.build_setId(b))){var d=function(a,b,d,e){var f=arguments;c.each(d,function(a,b){switch(b){case"visibility":g._setVisibility.apply(null,f);break;case"actions":c.isFunction(a)&&a.apply(null,f)}})},f=function(c){c=c||i();var f=a.CZR_Helpers.build_setId(b);g._deferCallbackForControl(f,d,[c,b,e]).always(function(){k=!0}).fail(function(){j.reject()}).done(function(){j.resolve()})};f(),c.has(i,"czr_visibilityServi")||(i.czr_visibilityServi=new a.Value([]));var h=i.czr_visibilityServi();c.contains(h,b)||(i.bind(function(a){f(a)}),i.czr_visibilityServi(c.union(h,[b])))}}),k?j.promise():j.resolve().promise()},_setVisibility:function(b,d,e,f){var g=a.CZR_Helpers.build_setId(d),h=e.visibility(b,d,e.dominus);if(f=f||!1,c.isBoolean(h)&&("unchanged"!=h||f)){var i=function(){a.state.has("silent-update-processing")&&a.state("silent-update-processing")()||(a.control(g,function(a){var b={duration:"fast",completeCallback:function(){},unchanged:!1};c.has(a,"active")&&(h=h&&a.active()),c.has(a,"defaultActiveArguments")&&(b=control.defaultActiveArguments),a.onChangeActive(h,a.defaultActiveArguments)}),a.state.has("silent-update-processing")&&a.state("silent-update-processing").unbind(i))};a.state.has("silent-update-processing")&&a.state("silent-update-processing")()?a.state("silent-update-processing").bind(i):i()}},_getControlDeps:function(){return{}},_prepareDominusParams:function(b){var d=this,e={};if(!c.isObject(b))throw new Error("Visibilities : a dominus param definition must be an object.");if(!c.has(b,"visibility")&&!c.has(b,"actions"))throw new Error("Visibilities : a dominus definition must include a visibility or an actions callback.");if(!c.has(b,"dominus")||!c.isString(b.dominus)||c.isEmpty(b.dominus))throw new Error("Visibilities : a dominus control id must be a not empty string.");var f=a.CZR_Helpers.build_setId(b.dominus);if(!a.control.has(f))throw new Error("Visibilities : a dominus control id is not registered : "+f);if(!c.has(b,"servi")||c.isUndefined(b.servi)||!c.isArray(b.servi)||c.isEmpty(b.servi))throw new Error("Visibilities : servi must be set as an array not empty.");return c.each(d.defaultDominusParams,function(a,d){var f=b[d];switch(d){case"visibility":if(!c.isUndefined(f)&&!c.isEmpty(f)&&!c.isFunction(f))throw new Error("Visibilities : a dominus visibility callback must be a function : "+b.dominus);break;case"actions":if(!c.isUndefined(f)&&!c.isEmpty(f)&&!c.isFunction(f))throw new Error("Visibilities : a dominus actions callback must be a function : "+b.dominus);break;case"onSectionExpand":if(!c.isUndefined(f)&&!c.isEmpty(f)&&!c.isBoolean(f))throw new Error("Visibilities : a dominus onSectionExpand param must be a boolean : "+b.dominus)}e[d]=f}),e},_handleFaviconNote:function(){var b=this,c=a.CZR_Helpers.build_setId(serverControlParams.faviconOptionName);if(!(!a.has("site_icon")||!a.control("site_icon")||a.has(c)&&0===+a(c)()||+a("site_icon")()>0)){var e=a.control("site_icon").params.description;_newDes=["<strong>",d.faviconNote||"","</strong><br/><br/>"].join("")+e,b._printFaviconNote(_newDes),a("site_icon").callbacks.add(function(d){+d>0?(a.control("site_icon").container.find(".description").text(e),a.has(c)&&a(c).set("")):b._printFaviconNote(_newDes)})}},_printFaviconNote:function(b){a.control("site_icon").container.find(".description").html(b)}})}(wp.customize,jQuery,_),function(a,b){b(function(b){function c(a){var c=a.siblings(".open");0!==c.length&&c.offset().top<0&&b(".wp-full-overlay-sidebar-content").animate({scrollTop:-b("#customize-theme-controls").offset().top-c.height()+a.offset().top+b(".wp-full-overlay-sidebar-content").offset().top},700)}var d=a.customize||d;b(".accordion-section").not(".control-panel").click(function(){c(b(this))}),d.czrSetupCheckbox=function(a,c){b("input[type=checkbox]",d.control(a).container).each(function(){0===b(this).val()||"0"==b(this).val()||"off"==b(this).val()||_.isEmpty(b(this).val())?b(this).prop("checked",!1):b(this).prop("checked",!0),0===b(this).closest('div[class^="icheckbox"]').length&&b(this).iCheck({checkboxClass:"icheckbox_flat-grey",radioClass:"iradio_flat-grey"}).on("ifChanged",function(a){b(this).val(!1===b(this).is(":checked")?0:1),b(a.currentTarget).trigger("change")})})},d.czrSetupSelect=function(a,c){b("select[data-customize-setting-link]",d.control(a).container).not(".no-selecter-js").each(function(){b(this).selecter({})})},d.czrSetupStepper=function(a,c){b('input[type="number"]',d.control(a).container).each(function(){b(this).stepper()})},d.control.each(function(a){_.has(a,"id")&&("widget_"!=a.id.substring(0,"widget_".length)&&"nav_menu"!=a.id.substring(0,"nav_menu".length)&&d.czrSetupCheckbox(a.id),"nav_menu_locations"!=a.id.substring(0,"nav_menu_locations".length)&&d.czrSetupSelect(a.id),d.czrSetupStepper(a.id))});var e=function(){var a=b("<span/>",{"class":"customize-controls-home fa fa-home",html:'<span class="screen-reader-text">Home</span>'});b.when(b("#customize-header-actions").append(a)).done(function(){a.keydown(function(a){9!==a.which&&(13===a.which&&this.click(),a.preventDefault())}).on("click.customize-controls-home",function(){d.section.has(d.czr_activeSectionId())?d.section(d.czr_activeSectionId()).expanded(!1):d.section.each(function(a){a.expanded(!1)}),d.panel.each(function(a){a.expanded(!1)})})})};e()})}(wp,jQuery);(function (api, $, _) {
          var _is_checked = function( to ) {
                  return 0 !== to && '0' !== to && false !== to && 'off' !== to;
          };
          var _tagline_text;
          api.CZR_ctrlDependencies.prototype.dominiDeps = _.extend(
                api.CZR_ctrlDependencies.prototype.dominiDeps,
                [
                    {
                            dominus : 'blogdescription',
                            servi   : ['tc_show_tagline', 'tc_sticky_show_tagline'],
                            visibility : function( to, servusShortId ) {
                                var _to_return = !_.isEmpty( to );
                                if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                  _to_return = _to_return && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_tagline' ) ).get() );
                                }
                                return _to_return;
                            },
                            actions : function( to, servusShortId ) {
                                if ( typeof undefined === typeof _tagline_text ) {
                                  _tagline_text = to;
                                }

                                var _servus            = api( api.CZR_Helpers.build_setId( servusShortId ) ),
                                    _debounced_refresh = _.debounce( function() {
                                      api.previewer.refresh();
                                    }, 600 );
                                if ( to != _tagline_text && 'tc_show_tagline' == servusShortId && _is_checked( _servus.get() ) ) {
                                  if ( _.isEmpty( _tagline_text ) || _.isEmpty( to ) ) {
                                    _debounced_refresh();
                                  }
                                }
                                _tagline_text = to;
                            }
                    },
                    {
                            dominus : 'page_for_posts',
                            servi   : ['tc_blog_restrict_by_cat'],
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'show_on_front',
                            servi   : ['tc_blog_restrict_by_cat', 'tc_show_post_navigation_home'],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_show_post_navigation_home' == servusShortId ) {
                                    return ( 'posts' == to  ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_post_navigation' ) ).get() );
                                  }
                                  if ( 'posts' == to ) {
                                    return true;
                                  }
                                  if ( 'page' == to && 'tc_blog_restrict_by_cat' == servusShortId ) { //show cat picker also if a page for posts is set
                                    return _is_checked( api( api.CZR_Helpers.build_setId( 'page_for_posts' ) ).get() );
                                  }
                                  return false;
                            },
                    },
                    {
                            dominus : 'tc_logo_upload',
                            servi   : ['tc_logo_resize'],
                            visibility : function( to ) {
                                  return _.isNumber( to );
                            },
                    },
                    {
                            dominus : 'tc_show_featured_pages',
                            servi   : serverControlParams.FPControls,
                            visibility : function( to ) {
                                  return _is_checked( to );
                            },
                    },
                    {
                            dominus : 'tc_front_slider',
                            servi   : [
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
                              'tc_posts_slider_restrict_by_cat' //pro-bundle
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( servusShortId.indexOf('tc_posts_slider_') > -1 ) {
                                    return 'tc_posts_slider' == to;
                                  }

                                  return _is_checked( to );
                            },
                            actions : function( to, servusShortId ) {
                                 var $_front_slider_container = api.control( api.CZR_Helpers.build_setId('tc_front_slider') ).container,
                                     $_label = $( 'label' , $_front_slider_container ),
                                     $_empty_sliders_notice = $( 'div.czr-notice', $_front_slider_container);

                                  if ( 'tc_posts_slider' == to ) {
                                    if ( 0 !== $_label.length && ! $('.czr-notice' , $_label ).length ) {
                                      var $_notice = $('<span>', { class: 'czr-notice', html : serverControlParams.translatedStrings.postSliderNote || '' } );
                                      $_label.append( $_notice );
                                    }
                                    else {
                                      $('.czr-notice' , $_label ).show();
                                    }
                                    if ( 0 !== $_empty_sliders_notice.length ) {
                                      $_empty_sliders_notice.hide();
                                    }
                                  }
                                  else {
                                    if ( 0 !== $( '.czr-notice' , $_label ).length )
                                      $( '.czr-notice' , $_label ).hide();
                                    if ( 0 !== $_empty_sliders_notice.length )
                                      $_empty_sliders_notice.show();
                                  }
                            }
                    },
                    {
                            dominus : 'tc_slider_default_height',
                            servi   : ['tc_slider_default_height_apply_all', 'tc_slider_change_default_img_size'],
                            visibility : function( to ) {
                                  var _defaultHeight = serverControlParams.defaultSliderHeight || 500;
                                  return _defaultHeight != to;
                            },
                    },
                    {
                            dominus : 'tc_posts_slider_link',
                            servi   : ['tc_posts_slider_button_text'],
                            visibility : function( to ) {
                                  return to.indexOf('cta') > -1;
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_shape',
                            servi   : ['tc_post_list_thumb_height'],
                            visibility : function( to ) {
                                  return to.indexOf('rectangular') > -1;
                            },
                    },
                    {
                            dominus : 'tc_post_list_thumb_position',
                            servi   : ['tc_post_list_thumb_alternate'],
                            visibility : function( to ) {
                                  return _.contains( [ 'left', 'right'], to );
                            },
                    },
                    {
                            dominus : 'tc_post_list_show_thumb',
                            servi   : [
                              'tc_post_list_use_attachment_as_thumb',
                              'tc_post_list_default_thumb',
                              'tc_post_list_thumb_shape',
                              'tc_post_list_thumb_alternate',
                              'tc_post_list_thumb_position',
                              'tc_post_list_thumb_height',
                              'tc_grid_thumb_height'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_grid_thumb_height' == servusShortId ) {
                                    return _is_checked(to)
                                        && $('.tc-grid-toggle-controls').hasClass('open')
                                        && 'grid' == api( api.CZR_Helpers.build_setId( 'tc_post_list_grid' ) ).get();
                                  }
                                  return _is_checked(to) ;
                            },
                    },
                    {
                            dominus : 'tc_post_list_grid',
                            servi   : [
                              'tc_grid_columns',
                              'tc_grid_expand_featured',
                              'tc_grid_in_blog',
                              'tc_grid_in_archive',
                              'tc_grid_in_search',
                              'tc_grid_thumb_height',
                              'tc_grid_bottom_border',
                              'tc_grid_shadow',
                              'tc_grid_icons',
                              'tc_grid_num_words',
                              'tc_post_list_grid',//trick, see the actions
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId )
                                      return true;

                                  if ( _.contains( serverControlParams.gridDesignControls, servusShortId ) ) {
                                      _bool =  $('.tc-grid-toggle-controls').hasClass('open') && 'grid' == to;

                                      if ( 'tc_grid_thumb_height' == servusShortId ) {
                                          return _bool && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_post_list_show_thumb' ) ).get() );
                                      }
                                      return _bool;
                                  }
                                  return 'grid' == to;
                            },
                            actions : function( to, servusShortId ) {
                                  if ( 'tc_post_list_grid' == servusShortId ) {
                                      $('.tc-grid-toggle-controls').toggle( 'grid' == to );
                                  }
                            }
                    },
                    {
                            dominus : 'tc_breadcrumb',
                            servi   : [
                              'tc_show_breadcrumb_home',
                              'tc_show_breadcrumb_in_pages',
                              'tc_show_breadcrumb_in_single_posts',
                              'tc_show_breadcrumb_in_post_lists',
                              'tc_breadcrumb_yoast'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_title_icon',
                            servi   : [
                              'tc_show_page_title_icon',
                              'tc_show_post_title_icon',
                              'tc_show_archive_title_icon',
                              'tc_show_post_list_title_icon',
                              'tc_show_sidebar_widget_icon',
                              'tc_show_footer_widget_icon'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas',
                            servi   : [
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
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_show_post_metas_update_date',
                            servi   : ['tc_post_metas_update_date_format'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_post_metas_update_notice_in_title',
                            servi   : [
                              'tc_post_metas_update_notice_text',
                              'tc_post_metas_update_notice_format',
                              'tc_post_metas_update_notice_interval'
                            ],
                            visibility : function( to ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_post_list_length',
                            servi   : ['tc_post_list_excerpt_length'],
                            visibility: function (to) {
                                  return 'excerpt' == to;
                            }
                    },
                    {
                            dominus : 'tc_sticky_show_title_logo',
                            servi   : ['tc_sticky_logo_upload'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                    {
                            dominus : 'tc_sticky_header',
                            servi   : [
                              'tc_sticky_show_tagline',
                              'tc_sticky_show_title_logo',
                              'tc_sticky_shrink_title_logo',
                              'tc_sticky_show_menu',
                              'tc_sticky_transparent_on_scroll',
                              'tc_sticky_logo_upload',
                              'tc_woocommerce_header_cart_sticky'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_woocommerce_header_cart_sticky' == servusShortId ) {
                                    return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_woocommerce_header_cart' ) ).get() );
                                  }
                                  if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                    return !_.isEmpty( api( api.CZR_Helpers.build_setId( 'blogdescription' ) ).get() ) && _is_checked( to ) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_show_tagline' ) ).get() );
                                  }

                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_woocommerce_header_cart',
                            servi   : ['tc_woocommerce_header_cart_sticky'],
                            visibility: function (to) {
                                  return _is_checked(to) && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() );
                            }
                    },
                    {
                            dominus : 'tc_comment_bubble_color_type',
                            servi   : ['tc_comment_bubble_color'],
                            visibility: function (to) {
                                  return 'custom' == to;
                            }
                    },
                    {
                            dominus : 'tc_comment_show_bubble',
                            servi   : [
                              'tc_comment_bubble_shape',
                              'tc_comment_bubble_color_type',
                              'tc_comment_bubble_color'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'tc_comment_bubble_color' == servusShortId ) {
                                    return _is_checked(to) && 'custom' == api( api.CZR_Helpers.build_setId( 'tc_comment_bubble_color_type' ) ).get();
                                  }
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_dropcap',
                            servi   : [
                              'tc_dropcap_minwords',
                              'tc_dropcap_design',
                              'tc_post_dropcap',
                              'tc_page_dropcap'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_enable_gallery',
                            servi   : [
                              'tc_gallery_fancybox',
                              'tc_gallery_style',
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_skin_random',
                            servi   : [
                              'tc_skin',
                            ],
                            visibility: function() {
                              return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_skin_select = api.control( api.CZR_Helpers.build_setId(servusShortId) ).container;
                                  $_skin_select.find('select').prop('disabled', '1' == to ? 'disabled' : '' );
                            },
                    },
                    {
                            dominus : 'tc_show_post_navigation',
                            servi   : [
                              'tc_show_post_navigation_page',
                              'tc_show_post_navigation_home',
                              'tc_show_post_navigation_single',
                              'tc_show_post_navigation_archive'
                            ],
                            visibility : function( to, servusShortId ) {
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_display_second_menu',
                            servi   : [
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_position',
                              'tc_second_menu_resp_setting',
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect'
                            ],
                            visibility : function( to, servusShortId ) {
                                  var _menu_style_val = api( api.CZR_Helpers.build_setId( 'tc_menu_style' )).get();
                                  if ( _.contains( ['nav_menu_locations[secondary]', 'tc_second_menu_resp_setting'], servusShortId ) )
                                    return _is_checked(to) && 'aside' == _menu_style_val;
                                  if ( _.contains( ['tc_menu_submenu_fade_effect', 'tc_menu_submenu_item_move_effect'], servusShortId ) )
                                    return ( _is_checked(to) && 'aside' == _menu_style_val ) || ( !_is_checked(to) && 'aside' != _menu_style_val );
                                  return _is_checked(to);
                            },
                    },
                    {
                            dominus : 'tc_menu_style',
                            servi   : [
                              'tc_menu_type',
                              'tc_menu_submenu_fade_effect',
                              'tc_menu_submenu_item_move_effect',
                              'tc_menu_resp_dropdown_limit_to_viewport',
                              'tc_display_menu_label',
                              'tc_display_second_menu',
                              'tc_second_menu_position',
                              'nav_menu_locations[secondary]',
                              'tc_second_menu_resp_setting',
                              'tc_menu_position', /* used to perform actions on menu position */
                              'tc_mc_effect', /* pro */
                              'tc_menu_style'
                            ],
                            visibility : function( to, servusShortId ) {
                                  if ( 'aside' != to ) {
                                    if ( _.contains([
                                        'tc_display_menu_label',
                                        'tc_display_second_menu',
                                        'nav_menu_locations[secondary]',
                                        'tc_second_menu_position',
                                        'tc_second_menu_resp_setting',
                                        'tc_mc_effect'] , servusShortId ) ) {
                                      return false;
                                    } else {
                                      return true;
                                    }
                                  }
                                  else {
                                    if ( _.contains([
                                        'tc_menu_type',
                                        'tc_menu_submenu_fade_effect',
                                        'tc_menu_submenu_item_move_effect',
                                        'nav_menu_locations[secondary]',
                                        'tc_second_menu_position',
                                        'tc_second_menu_resp_setting'], servusShortId ) ) {
                                      return _is_checked( api( api.CZR_Helpers.build_setId('tc_display_second_menu') ).get() );
                                    }
                                    else if ( _.contains([
                                        'tc_menu_resp_dropdown_limit_to_viewport',
                                        'tc_menu_position'], servusShortId ) ) {
                                      return false;
                                    }
                                    return true;
                                  }
                            },
                            actions : function( to, servusShortId ) {
                                  if ( 'tc_menu_style' == servusShortId ) {
                                    var $_container = api.control(api.CZR_Helpers.build_setId( servusShortId )).container;
                                        $_notice    = $_container.children('.czr-notice');
                                    if ( 0 === $_notice.length ) {
                                      $_notice = $('<span>', { class: 'czr-notice', html : serverControlParams.translatedStrings.sidenavNote || '' } );

                                      $_container.append( $_notice );
                                    }

                                    $_notice[ 'aside' == to ? 'show' : 'hide' ]();
                                }
                          }
                    },
                    {
                            dominus : 'tc_show_tagline',
                            servi   : ['tc_sticky_show_tagline', 'tc_show_tagline'],
                            visibility: function (to, servusShortId ) {
                                  var _to_return = !_.isEmpty( api( api.CZR_Helpers.build_setId( 'blogdescription' ) ).get() );
                                  if ( 'tc_sticky_show_tagline' == servusShortId ) {
                                    _to_return  = _to_return && _is_checked( api( api.CZR_Helpers.build_setId( 'tc_sticky_header' ) ).get() ) && _is_checked( to );
                                  }
                                  return _to_return;
                            },
                    },
                    {
                            dominus : 'tc_hide_all_menus',
                            servi   : ['tc_hide_all_menus'],
                            visibility: function (to) {
                                  return true;
                            },
                            actions : function( to, servusShortId ) {
                                  var $_nav_section_container = api.section('nav').container,
                                      $_controls = $_nav_section_container.find('li.customize-control').not( api.control(api.CZR_Helpers.build_setId(servusShortId)).container );
                                  $_controls.each( function() {
                                    if ( $(this).is(':visible') )
                                      $(this).fadeTo( 500 , true === to ? 0.5 : 1).css('pointerEvents', true === to ? 'none' : ''); //.fadeTo() duration, opacity, callback
                                  });//$.each()
                            }
                    },
                    {
                            dominus : 'tc_show_back_to_top',
                            servi   : ['tc_back_to_top_position'],
                            visibility: function (to) {
                                  return _is_checked(to);
                            }
                    },
                ]//dominiDeps {}
          );//_.extend()

}) ( wp.customize, jQuery, _);(function (wp, $) {
        $( function($) {
                var _build_control_id = function( _control ) {
                  return [ '#' , 'customize-control-tc_theme_options-', _control ].join('');
                };

                var _get_grid_design_controls = function() {
                  return $( serverControlParams.gridDesignControls.map( function( _control ) {
                    return _build_control_id( _control );
                  }).join(',') );
                };
                $( _get_grid_design_controls() ).addClass('tc-grid-design').hide();

                $('.tc-grid-toggle-controls').on( 'click', function() {
                  $( _get_grid_design_controls() ).slideToggle('fast');
                  $(this).toggleClass('open');
                } );
                $g_logo = $('<img>' , {class : 'tc-title-google-logo' , src : '//www.google.com/images/logos/google_logo_41.png' , height : 20 });
                $('#accordion-section-fonts_sec').prepend($g_logo);
                $('select[data-customize-setting-link="tc_theme_options[tc_skin]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintSkinOptionElement,
                    templateSelection: paintSkinOptionElement,
                    escapeMarkup: function(m) { return m; }
                }).on("select2-highlight", function(e) { //<- doesn't work with recent select2 and it doesn't provide alternatives :(
                 $(this).select2("val" , e.val, true );
                });
                function paintSkinOptionElement(state) {
                    if (!state.id) return state.text; // optgroup
                    return '<span class="tc-select2-skin-color" style="background:' + $(state.element).data('hex') + '">' + $(state.element).data('hex') + '<span>';
                }
                $('select[data-customize-setting-link="tc_theme_options[tc_fonts]"]').select2({
                    minimumResultsForSearch: -1, //no search box needed
                    templateResult: paintFontOptionElement,
                    templateSelection: paintFontOptionElement,
                    escapeMarkup: function(m) { return m; },
                }).on("select2-highlight", function(e) {//<- doesn't work with recent select2 and it doesn't provide alternatives :(
                  $(this).select2("val" , e.val, true );
                });

                function paintFontOptionElement(state) {
                    if ( ! state.id && ( -1 != state.text.indexOf('Google') ) )
                      return '<img class="tc-google-logo" src="//www.google.com/images/logos/google_logo_41.png" height="20"/> Font pairs'; // google font optgroup
                    else if ( ! state.id )
                      return state.text;// optgroup different than google font
                    return '<span class="tc-select2-font">' + state.text + '</span>';
                }
                var donate_displayed  = false,
                    is_pro            = 'customizr-pro' == serverControlParams.themeName;
                if (  ! serverControlParams.HideDonate && ! is_pro ) {
                  _render_donate_block();
                  donate_displayed = true;
                }
                if ( serverControlParams.ShowCTA && ! donate_displayed && ! is_pro ) {
                 _render_main_cta();
                }
                if ( ! is_pro ) {
                  _render_wfc_cta();
                  _render_fpu_cta();
                  _render_footer_cta();
                  _render_gc_cta();
                  _render_mc_cta();
                }

                function _render_rate_czr() {
                  var _cta = _.template(
                      $( "script#rate-czr" ).html()
                  );
                  $('#customize-footer-actions').append( _cta() );
                }

                function _render_donate_block() {
                  var donate_template = _.template(
                      $( "script#donate_template" ).html()
                  );

                  $('#customize-info').after( donate_template() );
                  $('.czr-close-request').click( function(e) {
                    e.preventDefault();
                    $('.donate-alert').slideToggle("fast");
                    $(this).hide();
                  });

                  $('.czr-hide-donate').click( function(e) {
                    _ajax_save();
                    setTimeout(function(){
                        $('#czr-donate-customizer').slideToggle("fast");
                    }, 200);
                  });

                  $('.czr-cancel-hide-donate').click( function(e) {
                    $('.donate-alert').slideToggle("fast");
                    setTimeout(function(){
                        $('.czr-close-request').show();
                    }, 200);
                  });
                }//end of donate block


                function _render_main_cta() {
                  var _cta = _.template(
                      $( "script#main_cta" ).html()
                  );
                  $('#customize-info').after( _cta() );
                }

                function _render_wfc_cta() {
                  var _cta = _.template(
                      $( "script#wfc_cta" ).html()
                  );
                  $('li[id*="tc_body_font_size"]').append( _cta() );
                }

                function _render_fpu_cta() {
                  var _cta = _.template(
                      $( "script#fpu_cta" ).html()
                  );
                  $('li[id*="tc_featured_text_three"]').append( _cta() );
                }

                function _render_gc_cta() {
                  var _cta = _.template(
                      $( "script#gc_cta" ).html()
                  );
                  $('li[id*="tc_post_list_show_thumb"] > .czr-customizr-title').before( _cta() );
                }

                function _render_mc_cta() {
                  var _cta = _.template(
                      $( "script#mc_cta" ).html()
                  );
                  $('li[id*="tc_theme_options-tc_display_menu_label"]').append( _cta() );
                }

                function _render_footer_cta() {
                  var _cta = _.template(
                      $( "script#footer_cta" ).html()
                  );
                  $('li[id*="tc_show_back_to_top"]').closest('ul').append( _cta() );
                }

                function _ajax_save() {
                    var AjaxUrl         = serverControlParams.AjaxUrl,
                    query = {
                        action  : 'hide_donate',
                        TCnonce :  serverControlParams.TCNonce,
                        wp_customize : 'on'
                    },
                    request = $.post( AjaxUrl, query );
                    request.done( function( response ) {
                        if ( '0' === response ) {
                            return;
                        }
                        if ( '-1' === response ) {
                            return;
                        }
                    });
                }//end of function
        });
}) ( wp, jQuery );