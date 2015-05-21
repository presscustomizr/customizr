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
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended. More here : http://presscustomizr.com/customizr/hooks-api
*
*/


//Fire Customizr
require_once( get_template_directory() . '/inc/init.php' );

/**
* The best and safest way to extend Customizr with your own custom functions is to create a child theme.
* You can add functions here but they will be lost on upgrade. If you use a child theme, you are safe!
* More informations on how to create a child theme with Customizr here : http://presscustomizr.com/customizr/#child-theme
*/


/**
 * Get the meta_type with get_current_screen() ->
 * meta_type for page => screen -> base = post
 * meta_type for post => screen -> base = post
 * meta_type for author => screen -> base = profile
 * meta_type for tags => $_GET taxonomy
 * meta_type for category => $_GET taxonomy
 *
 *
 * Get the id with $_GET
 * page, post and CPT => $_GET['post']
 * all taxonomies $_GET['tag_ID']
 * authors => $_GET['user_id']
 *
 *
 * Get the post type with screen ->
 *
*/

//add_action('current_screen' , 'tc_get_admin_object');
//add_action( 'edit_form_after_title' , 'tc_get_admin_object' );
function tc_get_admin_object( $screen ) {
  if ( ! is_admin() )
    return;

  /*if ( null != $post ) :
    ?>
      <pre>
        <?php print_r($post -> type . ' - ' . $post -> ID ); ?>
      </pre>
    <?php
  endif;*/

  ?>
    <pre>
      <?php print_r($_GET); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( $screen ); ?>
    </pre>
  <?php

}



add_action('__after_header' , 'tc_display_options');
function tc_display_options() {
  $_options = get_option('tc_theme_options');
  $_option = TC_utils::$inst->tc_opt('tc_skin');
  ?>
    <pre>
      <?php print_r( TC_contx::$instance -> tc_build_context() ); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( 'Context meta type: ' . TC_contx::$instance -> tc_get_context( 'meta_type' ) ); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( 'Context type: ' . TC_contx::$instance -> tc_get_context( 'type' ) ); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( 'Context: ' . TC_contx::$instance -> tc_get_context() ); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( 'Skin : ' . $_option ); ?>
    </pre>
  <?php
  ?>
    <pre>
      <?php print_r( $_options ); ?>
    </pre>
  <?php
}