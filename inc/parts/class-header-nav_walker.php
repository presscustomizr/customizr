<?php
/**
* Cleaner walker for wp_nav_menu()
* Used for the user created main menus, not for : default menu and widget menus
* Walker_Nav_Menu is located in /wp-includes/nav-menu-template.php
* Walker is located in wp-includes/class-wp-walker.php
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_nav_walker' ) ) :
  class TC_nav_walker extends Walker_Nav_Menu {
    static $instance;
    public $tc_location;
    function __construct($_location) {
      self::$instance =& $this;
      $this -> tc_location = $_location;
      add_filter( 'tc_nav_menu_css_class' , array($this, 'tc_add_bootstrap_classes'), 10, 4 );
    }


    /**
    * hook : nav_menu_css_class
    */
    function tc_add_bootstrap_classes($classes, $item, $args, $depth ) {
      //cast $classes into array
      $classes = (array)$classes;
      //check if $item is a dropdown ( a parent )
      //this is_dropdown property has been added in the the display_element() override method
      if ( $item -> is_dropdown ) {
        if ( $depth === 0 && ! in_array( 'dropdown', $classes ) ) {
          $classes[] = 'dropdown';
        } elseif ( $depth > 0 && ! in_array( 'dropdown-submenu', $classes ) ) {
          $classes[] = 'dropdown-submenu';
        }
      }
      return $classes;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu\">\n";
    }


    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
      $item_html = '';
      //ask the parent to do the hard work
      parent::start_el( $item_html, $item, $depth, $args, $id);

      //this is_dropdown property has been added in the the display_element() override method
      if ( $item->is_dropdown ) {
        //makes top menu not clickable (default bootstrap behaviour)
        $search         = '<a';
        $replace        = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? '<a data-test="joie"' : '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
        $replace       .= strpos($item_html, 'href=') ? '' : ' href="#"' ;
        $replace        = apply_filters( 'tc_menu_open_on_click', $replace , $search, $this -> tc_location );
        $item_html      = str_replace( $search , $replace , $item_html);

        //adds arrows down
        if ( $depth === 0 )
            $item_html      = str_replace( '</a>' , ' <strong class="caret"></strong></a>' , $item_html);
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
      //we add a property here
      //will be used in override start_el() and class filter
      $element->is_dropdown = ! empty( $children_elements[$element->ID]);

      $element->classes = apply_filters( 'tc_nav_menu_css_class', array_filter( empty( $element->classes) ? array() : (array)$element->classes ), $element, $args, $depth );

      //let the parent do the rest of the job !
      parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output);
    }
  }//end of class
endif;


/**
* Replace the walker for tc_page_menu()
* Used for the specific default page menu only
*
* Walker_Page is located in wp-includes/post-template.php
* Walker is located in wp-includes/class-wp-walker.php
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_nav_walker_page' ) ) :
  class TC_nav_walker_page extends Walker_Page {
    function __construct() {
      add_filter('page_css_class' , array($this, 'tc_add_bootstrap_classes'), 10, 5 );
    }


    /**
    * hook : page_css_class
    */
    function tc_add_bootstrap_classes($css_class, $page = null, $depth = 0, $args = array(), $current_page = 0) {
      if ( is_array($css_class) && in_array('page_item_has_children', $css_class ) ) {
        if ( 0 === $depth) {
          $css_class[] = 'dropdown';
        } elseif ( $depth > 0) {
          $css_class[] = 'dropdown-submenu';
        }
      }
      if ( ! in_array( 'menu-item' , $css_class ) )
        $css_class[] = 'menu-item';
      return $css_class;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu\">\n";
    }


    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
      $item_html = '';
      //since the &$output is passed by reference, it will modify the value on the fly based on the parent method treatment
      //we just have to make some additional treatments afterwards
      parent::start_el( $item_html, $page, $depth, $args, $current_page );

      if ( $args['has_children'] ) {
        //makes top menu not clickable (default bootstrap behaviour)
        $search         = '<a';
        $replace        = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? $search : '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
        $replace       .= strpos($item_html, 'href=') ? '' : ' href="#"' ;
        $replace        = apply_filters( 'tc_menu_open_on_click', $replace , $search );
        $item_html      = str_replace( $search , $replace , $item_html);

        //adds arrows down
        if ( $depth === 0 )
          $item_html      = str_replace( '</a>' , ' <strong class="caret"></strong></a>' , $item_html);
      }

      elseif (stristr( $item_html, 'li class="divider' )) {
        $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
      }

      elseif (stristr( $item_html, 'li class="nav-header' )) {
        $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
      }

      $output .= $item_html;
    }
 }//end of class
endif;
