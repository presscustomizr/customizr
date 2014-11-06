## Copyright ##
Customizr is a free WordPress theme designed by Nicolas Guillaume in Nice, France. (www.themesandco.com)
Feel free to use modify and redistribute this theme however you like.
You may remove any copyright references (unless required by third party components) and crediting is not necessary, but very appreciated... ;-D.
Customizr is distributed under the terms of the GNU GPL
Enjoy it!

## Installation ##
1. Upload the `customizr` folder to the `/wp-content/themes/` directory
Activation and Use
1. Activate the Theme through the 'Themes' menu in WordPress
2. See Appearance -> Customiz'it to change theme options

## License ##
Unless otherwise specified, all the theme files, scripts and images
are licensed under GNU General Public License version 2, see file license.txt.
The exceptions to this license are as follows:
* Bootstrap by Twitter and the Glyphicon set are licensed under the GPL-compatible [http://www.apache.org/licenses/LICENSE-2.0 Apache License v2.0]
* The script bootstrap-carousel.js v2.3.0 is licensed under the Apache License
* The script holder.js v1.9 is licensed under the Apache License
* The script modernizr.js is dual licensed under the BSD and MIT licenses
* The script jquery.iphonecheck.js is copyrighted by Thomas Reynolds, licensed GPL & MIT
* Icon Set:	IcoMoon - Free -- http://keyamoon.com/icomoon/ License:	CC BY-SA 3.0 -- http://creativecommons.org/licenses/by-sa/3.0/
* Icon Set:	Broccolidry -- http://dribbble.com/shots/587469-Free-16px-Broccolidryiconsaniconsetitisfullof-icons License: Aribitrary -- http://licence.visualidiot.com/
* Icon Set:	Iconic -- http://somerandomdude.com/work/iconic/ License:	CC BY-SA 3.0 -- http://creativecommons.org/licenses/by-sa/3.0/us/
* The image architecture.jpg is free for personal and commercial use. http://www.sxc.hu/photo/1415205
* The image columns.jpg is free for personal and commercial use. Reference: http://www.sxc.hu/photo/1400012
* The image laverie.jpg is free for personal and commercial use. Reference: http://www.sxc.hu/photo/1370161

##  Changelog ##
= 1.1.5 =
* Sanitization of home_url() in some files (with esc_url)
* Clearing of warning message in slides list : check on the $_GET['action'] index
* Addition of some localized strings
* Removal of the optional WP footer credit links

= 1.1.4 =
* addition of selected() and checked() functions in metaboxes input
* better sanitization of WP customizer inputs : 3 sanitization callbacks added in tc_customizr_control_class for number, textarea and url

= 1.1 =
* Better stylesheets enqueuing
* Fix the quick mode edit for slide custom post : add a script to disable the clearing of metas fields on update
* Add a fallback screen on activation if WP version < 3.4 => WP Customizer not supported
* Fix the slide caption texts rendering change the conditions (&& => ||)

= 1.0 =
* Initial Release

