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

/* SPECIAL TREATMENT FOR IMAGE IN DOC */
add_filter( 'the_content' , 'tc_parse_imgs' );

function tc_parse_imgs( $content ) {
  // if ( ! isset( $_GET['lazy_load'] ) || 'true' != $_GET['lazy_load'] )
  //   return $content;

  if( is_feed() || is_preview() || wp_is_mobile() ) return $content;
  if (strpos( $content, 'data-src' ) !== false) return $content;
    $content = preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', 'tc_regex_callback', $content);

  return $content;
}

function tc_regex_callback($matches) {

  $dummy_image = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

  if (preg_match('/ data-lazy *= *"false" */', $matches[0])){
      return '<img' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '>';
  } else {
      return '<img' . $matches[1] . 'src="' . $dummy_image . '" data-src="' . $matches[2] . '"' . $matches[3] . '><noscript><img' . $matches[1] . 'src="' . $matches[2] . '"' . $matches[3] . '></noscript>';
  }
}

add_action('wp_footer' , 'tc_smart_img_load');

function tc_smart_img_load() {
  ?>
  <script type="text/javascript" id="tc-smart-img-load">
    ;(function($) {
        var $w = $(window),
            th = 200,
            attrib = "data-src",
            $_images = $('img[data-src]'),
            _load_all_images_on_first_scroll = false,
            _inViewPort,
            timer,
            increment = 1;//used to wait a little bit after the first user scroll actions to trigger the timer

        $_images.bind('scrollin', {}, function() {
            _load_img(this);
        });
        $w.scroll( _better_scroll_event_handler );
        $w.resize(_event_handler);
        _event_handler();

        function _better_scroll_event_handler(evt) {
          //use a timer
          if ( timer) {
              increment++;
              window.clearTimeout(timer);
          }

          timer = window.setTimeout(function() {
            _event_handler(evt);
          }, increment > 5 ? 50 : 0 );
        }

        function _event_handler(evt) {
            _inViewPort = $_images.filter(function() {
                var $e = $(this),
                    wt = $w.scrollTop(),
                    wb = wt + $w.height(),
                    et = $e.offset().top,
                    eb = et + $e.height();
                return eb >= wt - th && et <= wb + th;
            });
            if ( evt && 'scroll' == evt.type && _load_all_images_on_first_scroll )
              $_images = $_images.trigger('scrollin');
            else
              $_images = $_images.not(_inViewPort.trigger('scrollin'));
        }

        function _load_img(img) {
            var $img = $(img),
                src = $img.attr(attrib);
            $img.unbind('scrollin').hide().removeAttr(attrib);
            img.src = src;
            $img.fadeIn();
        }
        return this;
    })(jQuery);
  </script>
  <?php
}