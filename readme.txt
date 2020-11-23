=== Customizr ===
Contributors: nikeo, d4z_c0nf
Tags: one-column, two-columns, three-columns, left-sidebar, right-sidebar, buddypress, custom-menu, custom-colors, featured-images, full-width-template, theme-options, threaded-comments, translation-ready, sticky-post, post-formats, rtl-language-support, editor-style
Requires at least: 4.6
Tested up to: 5.6
Stable tag: 4.3.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors.

== Description ==
Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors. Provides a perfect user experience on smartphones. Powers more than 100K active sites around the world. Hundreds of 5-stars reviews received on WordPress.org.

== Changelog ==
https://github.com/presscustomizr/customizr/releases
= 4.3.10 November 23rd, 2020 =
* fixed : [Links] => when underline is disabled, hovering/activating a link should display the underline. fixes #1870

= 4.3.9 November 19th, 2020 =
* added : [CSS][links] added a new option to opt-out underline on links. Option located in customizer > Global Settings > Formatting

= 4.3.8 November 17th, 2020 =
* fixed : [javascript] console warning when resizing in console due to an error in flickity slider script

= 4.3.7 November 17th, 2020 =
* fixed : [TRT requirement][accessibility] Links within content must be underlined. fixes #1869
* fixed : [WP 5.6][jQuery] adapt to WP jQuery updated version. Prepare removal of jQuery Migrate in future WP 5.7 ( https://make.wordpress.org/core/2020/06/29/updating-jquery-version-shipped-with-wordpress/ )

= 4.3.6 November 4th, 2020 =
* fixed : [PHP] possible warning => "Deprecated: Invalid characters passed for attempted conversion" when converting hex colors. fixes #1866

= 4.3.5 November 2nd, 2020 =
* tested : [WordPress] Customizr v4.3.5 is 100% compatible with WP 5.5.3
* fixed : [Menu] right clicking a parent menu item breaks sub-menu items auto-collapse. fixes #1852
* fixed : [CSS] add back the "home" CSS class to body tag when user picked option "Don't show any posts or page". fixes #1861

= 4.3.4 October 7th, 2020 =
* added : [CSS] add current theme version as CSS class to body tag

= 4.3.3 September 18, 2020 =
* fixed : [admin] security issue. fixes #1857

= 4.3.2 September 7, 2020 =
* improved : Successfully tested with WP 5.5.1. Maintenance release, minor code cleaning. 

= 4.3.1 August 28 2020 =
* fixed : [search icon] when displayed on the left in the main header, search field is partially hidden when revealed. fixes #1854

= 4.3.0 July 21st 2020 =
* Customizr has been successfully tested with WP 5.5
* fixed : [compatibility with WP 5.5] adapt customizer color-picker script with latest version of WP 5.5

= 4.2.9 July 16th 2020 =
* fixed : [Admin] possible PHP error in admin

= 4.2.8 July 16th 2020 =
* fixed : [Theme check] adapt to latest additions by theme review team. for #1848
* fixed : [magnific popup] should not be loaded when ligthbox option is unchecked. fixes #1851
* improved : [performances] preload Google Fonts by default

= 4.2.7 May 30th 2020 =
* added : [search form] on desktop new simple default mode for search form in header. Full screen search is optional in Customizing ▸ Header ▸ Design settings for desktops and laptops ▸ Search icon. fixes #1807
* added : [post metas] add a way to hook before and after. fixes #1842
* fixed : [post date] user defined date format is not taken into account when rendering dates. fixes #1843
* fixed : [comments] when using a plugin like wpdiscuz the comment link (in post heading) links to nowhere. fixes #1837

= 4.2.6 May 14th 2020 =
* fixed : [javascript] possible error due to missing underscore.js asset when "lightbox image" option is unchecked

= 4.2.5 May 14th 2020 =
* fixed : [Javascript][plugin compatibility] including underscore in the main script can break other plugins. fixes #1830
* fixed : [slider arrows][mobile] next/previous arrows are hiding a significant part of the image. fixes #1833
* fixed : [Woocommerce][Gutenberg] products blocks don’t work. fixes #1829
* fixed : fixed [Post navigation][mobile] navigation words are cut out. fixes #1832
* fixed : [Search form] gutenberg search block style is broken. fixes #1838
* fixed : [TRT] add new required fields in style.css. fixes #1840
* improved : [javascript] make sure maybe deprecated $.browser exists before using it
* added : [template][hooks] add new hooks before and after post heading title in post lists. fixes #1831

= 4.2.4 April 20th 2020 =
* fixed : [Horizontal Menus] submenu expansion on click is broken. fixes #1827
* fixed : [Menus] make sure the cursor is a pointer for a menu item, even when it has no href attribute
* fixed : [Lazy loading] images are not lazy loaded when dynamic content is inserted in the DOM. fixes #1826

= 4.2.3 April 15th 2020 =
* fixed : [Mobile menu] regression introduced when improving mobile menu in last update.

= 4.2.2 April 14th 2020 =
* fixed : [Mobile menu] on mobile menu, when the parent item has no href attribute, the submenu can only be expanded by clicking on the caret icon, and not on the parent menu item title
* fixed : [Horizontal Menu] on touch devices, 2 touches were needed to expand submenus in horizontal menus

= 4.2.1 March 26th 2020 =
* fixed : retina display issue with Gif images. fixes #1819
* added : a new option to preload Google fonts. Disabled by default. fixes #1816

= 4.2.0 March 14th 2020 =
* fixed : style conflict with the Ninja Forms date picker. fixes #1810
* added : [performance] new options to defer Font Awesome icons and javascript to avoid render blocking issues. fixes #1812

= 4.1.55 February 18th 2020 =
* fixed : [javascript] potential breakage of front js when using a cache plugin along with masonry and/or infinite scrolling
* improved : [performance] removed Vivus.js library and $.fn.animateSvg().Could also break front javascript when using cache plugins and that $.fn.animateSvg() was invoked too early

= 4.1.54 February 17th 2020 =
* fixed : removed support for async attribute on main script because of a potential regression with pro masonry grid
* fixed : html markup errors when displaying post pagination
* fixed : [Html] the "navigation" role is unnecessary for element
* fixed : [Html] the "banner" role is unnecessary for header element
* fixed : error when computing colors with rgb to rgba
* improved : disable front page navigation by default. following #1764
* improved : [asset] update fontawesome to latest version. fixes #1804
* added : support for Viber link in social links
* added :  featured image in singular => added a new option allowing users to display the image in its original dimensions. fixes #1803


== Resources ==
* All images included in the theme are either created for the theme and inheriting its license, or licensed under CC0.
* All other theme assets like scripts, stylesheets are licensed under GNU General Public License version 2, see file license.txt, or GPL compatible licenses like MIT, WTFPL. See headers of each files for further details.
