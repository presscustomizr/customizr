<<<<<<< HEAD
<?php
/**
* 
* This program is a free software; you can use it and/or modify it under the terms of the GNU 
* General Public License as published by the Free Software Foundation; either version 2 of the License, 
* or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU General Public License along with this program; if not, write 
* to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*
* @package   	Customizr
* @subpackage 	functions
* @since     	1.0
* @author    	Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright 	Copyright (c) 2013, Nicolas GUILLAUME
* @link      	http://themesandco.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/



/**
* This is where Customizr starts. This file defines and loads the theme's components :
* 1) A function tc__f() used everywhere in the theme, extension of WP built-in apply_filters()
* 2) Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, TC_WEBSITE
* 3) Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* 4) Text Domain
* 5) Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* 6) Plugins compatibility : jetpack, bbpress, qtranslate, woocommerce and more to come
* 7) Default filtered options for the customizer
* 8) Customizr theme's hooks API : front end components are rendered with action and filter hooks
* 
* The method TC__::tc__() loads the php files and instanciates all theme's classes.
* All classes files (except the class__.php file which loads the other) are named with the following convention : class-[group]-[class_name].php
* 
* The theme is entirely built on an extensible filter and action hooks API, which makes customizations easy as breeze, without ever needing to modify the core structure.
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended.
* 
*/



/**
* The best and safest way to extend Customizr with your own custom functions is to create a child theme.
* You can add functions here but they will be lost on upgrade. If you use a child theme, you are safe!
* More informations on how to create a child theme with Customizr here : http://themesandco.com/customizr/#child-theme
*/

//Fire Customizr
require_once( get_template_directory() . '/inc/init.php' );
=======
<?php
/**
*
* This program is a free software; you can use it and/or modify it under the terms of the GNU
* General Public License as published by the Free Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU General Public License along with this program; if not, write
* to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*
* @package   	Customizr
* @subpackage 	functions
* @since     	1.0
* @author    	Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright 	Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link      	http://presscustomizr.com/customizr
* @license   	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
* This is where Customizr starts. This file defines and loads the theme's components :
* => Constants : CUSTOMIZR_VER, TC_BASE, TC_BASE_CHILD, TC_BASE_URL, TC_BASE_URL_CHILD, THEMENAME, TC_WEBSITE
* => Default filtered values : images sizes, skins, featured pages, social networks, widgets, post list layout
* => Text Domain
* => Theme supports : editor style, automatic-feed-links, post formats, navigation menu, post-thumbnails, retina support
* => Plugins compatibility : JetPack, bbPress, qTranslate, WooCommerce and more to come
* => Default filtered options for the customizer
* => Customizr theme's hooks API : front end components are rendered with action and filter hooks
*
* The method TC__::tc__() loads the php files and instanciates all theme's classes.
* All classes files (except the class__.php file which loads the other) are named with the following convention : class-[group]-[class_name].php
*
* The theme is entirely built on an extensible filter and action hooks API, which makes customizations easy and safe, without ever needing to modify the core structure.
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended.
*
* If you're not familiar with the WordPress hooks concept, you might want to read those guides :
* http://docs.presscustomizr.com/article/26-wordpress-actions-filters-and-hooks-a-guide-for-non-developers
* https://codex.wordpress.org/Plugin_API
*/


//Fire Customizr
require_once( get_template_directory() . '/inc/init.php' );

/**
* THE BEST AND SAFEST WAY TO EXTEND THE CUSTOMIZR THEME WITH YOUR OWN CUSTOM CODE IS TO CREATE A CHILD THEME.
* You can add code here but it will be lost on upgrade. If you use a child theme, you are safe!
*
* Don't know what a child theme is ? Then you really want to spend 5 minutes learning how to use child themes in WordPress, you won't regret it :) !
* https://codex.wordpress.org/Child_Themes
*
* More informations about how to create a child theme with Customizr : http://docs.presscustomizr.com/article/24-creating-a-child-theme-for-customizr/
* A good starting point to customize the Customizr theme : http://docs.presscustomizr.com/article/35-how-to-customize-the-customizr-wordpress-theme/
*/
>>>>>>> upstream/master
