<?php
/**
* Cleaner walker for wp_nav_menu()
*
* Walker_Nav_Menu (WordPress default) example output:
*   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
*   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
*
* Roots_Nav_Walker example output:
*   <li class="menu-home"><a href="/">Home</a></li>
*   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_nav_walker' ) ) :
  class TC_nav_walker extends Walker_Nav_Menu {
      static $instance;
      function __construct () {
          self::$instance =& $this;
      }

      function check_current( $classes) {
        return preg_match( '/(current[-_])|active|dropdown/' , $classes);
      }

      function start_lvl(&$output, $depth = 0, $args = array()) {
        $output .= "\n<ul class=\"dropdown-menu\">\n";
      }

      function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $item_html = '';
        parent::start_el( $item_html, $item, $depth, $args);

        if ( $item->is_dropdown ) {
          //makes top menu not clickable (default bootstrap behaviour)
          $search         = '<a';
          $replace        = ( ! wp_is_mobile() && 'hover' == esc_attr( tc__f( '__get_option' , 'tc_menu_type' ) ) ) ? $search : '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
          $replace       .= strpos($item_html, 'href=') ? '' : ' href="#"' ;
          $replace        = apply_filters( 'tc_menu_open_on_click', $replace , $search );
          $item_html      = str_replace( $search , $replace , $item_html);

          //adds arrows down
          if ( $depth === 0 )
              $item_html      = str_replace( '</a>' , ' <b class="caret"></b></a>' , $item_html);
        }
        elseif (stristr( $item_html, 'li class="divider' )) {
          $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
        }
        elseif (stristr( $item_html, 'li class="nav-header' )) {
          $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
        }

        $output .= $item_html;
      }

      function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
        $element->is_dropdown = !empty( $children_elements[$element->ID]);

        if ( $element->is_dropdown) {
          if ( $depth === 0) {
            $element->classes[] = 'dropdown';
          } elseif ( $depth > 0) {
            $element->classes[] = 'dropdown-submenu';
          }
        }

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output);
      }
  }//end of class
endif;
