/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'tcicons\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-bubble' : '&#xe000;',
		'icon-facebook':'&#xe019;',
		'icon-twitter':'&#xe01a;',
		'icon-google': '&#xe018;',
		'icon-feed': '&#xe01b;',
		'icon-github': '&#xe01c;',
		'icon-dribbble': '&#xe01d;',
		'icon-pinterest': '&#xe01e;',
		'icon-youtube': '&#xe01f;',
		'icon-linkedin': '&#xe020;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, html, c, el;
	for (i = 0; i < els.length; i += 1) {
		el = els[i];
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};