=== Customizr ===
Contributors: nikeo, d4z_c0nf
Tags: one-column, two-columns, three-columns, left-sidebar, right-sidebar, buddypress, custom-menu, custom-colors, featured-images, full-width-template, theme-options, threaded-comments, translation-ready, sticky-post, post-formats, rtl-language-support, editor-style
Requires at least: 4.6
Tested up to: 5.6
Stable tag: 4.4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors.

== Description ==
Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors. Provides a perfect user experience on smartphones. Powers more than 100K active sites around the world. Hundreds of 5-stars reviews received on WordPress.org.

== Changelog ==
https://github.com/presscustomizr/customizr/releases
= 4.4.3 January 17th, 2021 =
* fixed : [PHP 8] error Uncaught ValueError: Unknown format specifier “;” in core/czr-customize-ccat.php:966
* added : [social links] mastodon icon
* added : [featured pages] support for shortcode in fp custom text.

= 4.4.2 January 8th, 2021 =
* fixed : [PHP 8.0] broken value checks on boolean options. for #1881

= 4.4.1 January 5th, 2021 =
* fixed : [performance] preload customizr.woff2 font. fixes #1835, fixes #1879
* fixed : [performance][php] removed duplicated queries for 'template' option and thumbnail models. fixes #1872
* fixed : [performance] improve loading performance of Font awesome icons to comply with Google lighthouse metrics ( preload warning removed )
* improved : [footer][performance] replaced font awesome WP icon by 'WP'

= 4.4.0 December 14th, 2020 =
* fixed : [PHP 8] Fix deprecation notices for optional function parameters declared before required parameter. #1876

= 4.3.14 December 10th, 2020 =
* fixed : [WP 5.6][WP 5.7] replaced deprecated shorthands
* fixed : [WP 5.6][fancybox] Close (x) link not working on pop-up image in galleries. Fixes #1874
* fixed : [WP Gallery Block] padding style conflict created by the theme. fixes #1873

= 4.3.13 December 2nd, 2020 =
* fixed : [links] external links icons not displayed. fixes #1871

= 4.3.12 December 1st, 2020 =
* fixed : [menu] javascript error on click on menu item with an anchor link

= 4.3.11 December 1st, 2020 =
* fixed : [headings] H3 heading size not smaller enough than H2 makes it difficult to distinguish
* fixed : [WP 5.7] remove jquery-migrate dependencies

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


== Resources ==
* All images included in the theme are either created for the theme and inheriting its license, or licensed under CC0.
* All other theme assets like scripts, stylesheets are licensed under GNU General Public License version 2, see file license.txt, or GPL compatible licenses like MIT, WTFPL. See headers of each files for further details.
