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
* Customizr's code acts like a collection of plugins that can be enabled, disabled or extended. More here : http://themesandco.com/customizr/hooks-api
*
*/


//Fire Customizr
require_once( get_template_directory() . '/inc/init.php' );

/**
* The best and safest way to extend Customizr with your own custom functions is to create a child theme.
* You can add functions here but they will be lost on upgrade. If you use a child theme, you are safe!
* More informations on how to create a child theme with Customizr here : http://themesandco.com/customizr/#child-theme
*/

if ( TC_utils::$instance -> tc_is_customizing() ) :
    class TC_customize_plus extends TC_customize  {

      function tc_customize_factory ( $wp_customize , $args, $setup ) {
          global $wp_version;
          //add panels if current WP version >= 4.0
          if ( isset( $setup['add_panel']) && version_compare( $wp_version, '4.0', '>=' ) ) {
            foreach ( $setup['add_panel'] as $p_key => $p_options ) {
              //declares the clean section option array
              $panel_options = array();
              //checks authorized panel args
              foreach( $args['panels'] as $p_set) {
                $panel_options[$p_set] = isset( $p_options[$p_set]) ?  $p_options[$p_set] : null;
              }
              $wp_customize -> add_panel( $p_key, $panel_options );
            }
          }

          //remove sections
          if ( isset( $setup['remove_section'])) {
            foreach ( $setup['remove_section'] as $section) {
              $wp_customize -> remove_section( $section);
            }
          }

          //add sections
          if ( isset( $setup['add_section'])) {
            foreach ( $setup['add_section'] as  $key => $options) {
              //generate section array
              $option_section = array();

              foreach( $args['sections'] as $sec) {
                $option_section[$sec] = isset( $options[$sec]) ?  $options[$sec] : null;
              }

              //add section
              $wp_customize -> add_section( $key,$option_section);
            }//end foreach
          }//end if

          //get_settings
          if ( isset( $setup['get_setting'])) {
            foreach ( $setup['get_setting'] as $setting) {
              $wp_customize -> get_setting( $setting )->transport = 'postMessage';
            }
          }

          //add settings and controls
          if ( isset( $setup['add_setting_control'])) {

            foreach ( $setup['add_setting_control'] as $key => $options) {
              //isolates the option name for the setting's filter
              $f_option_name = 'setting';
              $f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
                    if ( isset( $match[1][0] ) ) {$f_option_name = $match[1][0];}

              //declares settings array
              $option_settings = array();
              foreach( $args['settings'] as $set => $set_value) {
                if ( $set == 'setting_type' ) {
                  $option_settings['type'] = isset( $options['setting_type']) ?  $options['setting_type'] : $args['settings'][$set];
                  $option_settings['type'] = apply_filters( "{$f_option_name}_customizer_set", $option_settings['type'] , $set );
                }
                else {
                  $option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
                  $option_settings[$set] = apply_filters( "{$f_option_name}_customizer_set" , $option_settings[$set] , $set );
                }
              }

              //add setting
              $wp_customize -> add_setting( new WP_Customize_test ( $wp_customize, $key, $option_settings ) );

              //generate controls array
              $option_controls = array();
              foreach( $args['controls'] as $con) {
                $option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
              }

              //add control with a class instanciation if not default
              if( ! isset( $options['control']) )
                $wp_customize -> add_control( $key,$option_controls );
              else
                $wp_customize -> add_control( new $options['control']( $wp_customize, $key, $option_controls ));

            }//end for each
          }//end if isset
        }//end of customize generator function
    }

    // class WP_Customize_test extends WP_Customize_Setting {
    //   //public $id = 'background_image_thumb';

    //   /**
    //    * @since 3.4.0
    //    *
    //    * @param $value
    //    */
    //   protected function update( $value ) {

    //     switch( $this->type ) {
    //       case 'theme_mod' :
    //         return $this->_update_theme_mod( $value );

    //       case 'option' :
    //         return $this->_update_option( $value );

    //       default :

    //         /**
    //          * Fires when the {@see WP_Customize_Setting::update()} method is called for settings
    //          * not handled as theme_mods or options.
    //          *
    //          * The dynamic portion of the hook name, `$this->type`, refers to the type of setting.
    //          *
    //          * @since 3.4.0
    //          *
    //          * @param mixed                $value Value of the setting.
    //          * @param WP_Customize_Setting $this  WP_Customize_Setting instance.
    //          */
    //       return do_action( 'customize_update_' . $this->type, $value, $this );
    //     }
    //   }
    // }


    new TC_customize_plus();

endif;//end if customizing
