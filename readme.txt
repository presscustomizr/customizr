=== Customizr ===
Contributors: nikeo, d4z_c0nf
Tags: one-column, two-columns, three-columns, left-sidebar, right-sidebar, buddypress, custom-menu, custom-colors, featured-images, full-width-template, theme-options, threaded-comments, translation-ready, sticky-post, post-formats, rtl-language-support, editor-style
Requires at least: 4.6
Tested up to: 5.3.2
Stable tag: 4.1.51
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors.

== Description ==
Customizr is a simple and fast WordPress theme designed to help you attract and engage more visitors. Provides a perfect user experience on smartphones. Powers more than 100K active sites around the world. Hundreds of 5-stars reviews received on WordPress.org.

== Upgrade Notice ==
= 4.1.51 =
Implements a better search form ( displayed when clicking on the magnifying glass icon ), more suited to web standards. 100% compatible with WordPress 5.3.2.

== Changelog ==
= 4.1.51 January 21st 2020 =
* fixed : [Search form] current implementation can be misleading => added a search button next to the search input field. fixes #1795
* added : "flipboard" social network to the list of icons

= 4.1.50 December 23rd 2019 =
* fixed : Improves security for links to cross-orign destinations (social profiles, footer credits) => Add rel="noopener" or rel="noreferrer" when relevant
* fixed : display categories below the title when the post list layout is set to plain text full content. fixes #1792

= 4.1.49 December 12th 2019 =
* fixed : php error when inserting a WP gallery with Nimble WP editor module, which breaks customizer preview
* fixed : missing vertical spacing for WP galleries inserted with Nimble Builder WP editor

= 4.1.48 November 27th 2019 =
* fixed : broken style for caption of images and galleries when embedded as Gutenberg blocks. fixes #1789
* improved : removed unused files to reduces theme's folder size. fixes #1788
* updated : Nimble Builder admin notification

= 4.1.47 November 13th 2019 =
* Successfully tested with WordPress 5.3
* improved : fine tuning of post/page navigation options, in particular for the case when home is a static page
* improved : config page now provides child theme information

= 4.1.46 October 22nd 2019 =
* fixed : form fields, select, textarea, input, should be centered by default. fixes #1784
* fixed : font-size might be too small in WordPress text editor. fixes #1781

= 4.1.45 September 19th 2019 =
* fixed : post format meta boxes for the block editor. fixes #1774
* added : implement skip to content for TRT requirement ( https://make.wordpress.org/accessibility/handbook/markup/skip-links/ )

= 4.1.44 August 28th 2019 =
* improved : better keyboard navigation to comply with new TRT requirements : https://make.wordpress.org/themes/2019/08/03/planning-for-keyboard-navigation/. fixes #1771

= 4.1.43 July 27th 2019 =
* fixed : attachment page issue when building the gallery of siblings. fixes 1768
* fixed : add space after content before the first sidebar on mobile fixes #1767
* fixed : use consistent line-height accross html elements. fixes #1761
* improved : better style for admin notices on mobiles

= 4.1.42 June 30th 2019 =
* fixed : fix dropdown menu on hover not opening in tablet iOS based. fixes #1757
* fixed : WooCommerce compat reduce the font size of the "apply coupon" text. fixes #1754
* fixed H1 tag wrapping site-title, leading to possible multiple H1 on singular pages. fixes #1760

= 4.1.41 June 3rd 2019 =
* fixed : minor font-size issue. for #1755
* improved : replaced TGMPA class for plugin recommendation

= 4.1.40 May 29th 2019 =
* improved : deactivation of modular font-size for headings (Hx) by default for mobile devices. fixes #1746.

= 4.1.39 May 14th 2019 =
* fixed : reponsive wrapper "breaking" video post formats when using self-hosted or facebook video URLs we now handle only the responsiveness embeds which are iframes. fixes #1742
* fixed : remove 'hentry' among the post classes. fixes #1726
* fixed : added missing nimblecheck controls in the customizer. fixes #1752
* improved : remove offset for 2nd level submenu in desktops. fixes #1748
* improved : remove title attribute "Permalink To" on thumbnails links in post lists and featured pages
* added : new wp_body_open theme Hook. fixes #1722

= 4.1.38 April 24th 2019 =
* improved : block editor style in order to only enlarge the editor. fixes #1728
* fixed : smooth scroll throwing JS errors in latest chrome. fixes #1739
* fixed : using the letters "span" in categories could make their containers inherit the CSS rules defined with [class*="span"]. fixes #1734

= 4.1.37 April 9th, 2019 =
* fixed : image of featured pages not displayed when customizing

= 4.1.36 April 9th, 2019 =
* fixed : a bug with the images of featured pages not displayed sometimes.

= 4.1.35 April 5th, 2019 =
* fixed : make sure we catch the post_type_archive case when displaying the archive titles. fixes #1715
* fixed : remove title attribute on logo and site title + add aria-label attribute. fixes #1719
* fixed : compatibility issue with Event Tickets plugins. When the plugin was enabled no lists of posts were displayed. fixes #1724
* improved : style of checkboxes in customizer controls. fixes #1729
* added : a new option to control the current menu item highlighting. fixes #1718

= 4.1.34 March 22nd, 2019 =
* Fix: bug leading to related posts displayed in pages

= 4.1.33 March 21st, 2019 =
* fixed : WooCommerce product image issue

= 4.1.32 March 21st, 2019 =
* fixed : WooCommerce product image on top of the page disabled by default. because of https://github.com/presscustomizr/customizr/issues/1708#issuecomment-475151976

= 4.1.31 March 20th, 2019 =
* fixed : compatibility issue with the Event Tickets plugin. fixes #1700
* fixed : php syntax, ensure that the delimiter param is always passed to the explode PHP function. fixes #1709
* improved : new option to allow the WooCommerce featured image to be displayed before the main wrapper in full width. fixes #1708

= 4.1.30 February 28th, 2019 =
* fixed : PHP error when using PHP < 5.4 because of the use of the short array syntax. fixes #1697

= 4.1.29 February 27th, 2019 =
* fixed : make sure registered locations in the Customizr theme are always rendered when using the Nimble template and the Nimble header and footer
* improved : hide main slider nav arrows in mobile with CSS only. fixes #1680
* improved : "You May Also Like" smaller
* improved : post list pagination is larger
* added : a simple template tag parser, to be used with the filter 'czr_parse_template_tags'

= 4.1.28 February 14th, 2019 =
* fixed : removed unwanted lines displayed around the images of the featured pages on chrome
* fixed : assigned width and height attributes to placehoder images. fixes #1684
* improved : compatibility with the Social Media Share Buttons & Social Sharing Icons plugin. fixes #1683
    
= 4.1.27 February 2nd, 2019 =
* fixed : the absolute positioned header should be displayed only when on home AND on first paginated page of the blog. fixes #1665
* fixed : WooCommerce cart style issue when using several variations. fixes #1667
* fixed : reset margin-top when a p html elemnt is a child of a li html element. fixes #1400
* improved : extend anchor smooth scroll range of action. fixes #1662

= 4.1.26 January 16th, 2019 =
* fixed : wrong html5 shiv file path. fixes #1657
* improved : remove unused iphone checkboxes assets. fixes #1627
* added : an option to remove the various transparent header borders. fixes #1624
* added : compatibility with the Q2W3 plugin
* added : registered a new Nimble builder location above the footer. fixes #1664

== Resources ==
* All images included in the theme are either created for the theme and inheriting its license, or licensed under CC0.
* All other theme assets like scripts, stylesheets are licensed under GNU General Public License version 2, see file license.txt, or GPL compatible licenses like MIT, WTFPL. See headers of each files for further details.
