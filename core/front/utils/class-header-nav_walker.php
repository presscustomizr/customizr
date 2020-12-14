<?php
/**
* Cleaner walker for wp_nav_menu()
* Used for the user created main menus, not for : default menu and widget menus
* Walker_Nav_Menu is located in /wp-includes/nav-menu-template.php
* Walker is located in wp-includes/class-wp-walker.php
*/
if ( ! class_exists( 'CZR_nav_walker' ) ) :
  class CZR_nav_walker extends Walker_Nav_Menu {
    static $instance;
    public $czr_location;
    function __construct($_location) {
      self::$instance =& $this;
      $this -> czr_location = $_location;

      add_filter( 'czr_nav_menu_css_class' , array($this, 'czr_fn_add_bootstrap_classes'), 10, 4 );

    }


    /**
    * hook : czr_nav_menu_css_class
    */
    function czr_fn_add_bootstrap_classes($classes, $item, $args, $depth ) {
      //cast $classes into array
      $classes = (array)$classes;

      //check if $item is a dropdown ( a parent )
      //this is_dropdown property has been added in the the display_element() override method
      if ( $item -> is_dropdown ) {
        if ( $depth === 0 ) {
          if ( ! in_array( 'czr-dropdown', $classes ) )
            $classes[] = 'czr-dropdown';
        } elseif ( $depth > 0 ) {
          if ( ! in_array( 'czr-dropdown-submenu', $classes ) )
            $classes[] = 'czr-dropdown-submenu';
        }
        //if ( ! in_array( 'btn-group', $classes ) )
         // $classes[] = 'btn-group';
      }

      if ( $depth > 0 ) {
        if ( ! in_array( 'dropdown-item', $classes ) )
          $classes[] = 'dropdown-item';
      }

      $_active     = array_intersect( $classes, array( 'current-menu-ancestor', 'current-menu-item', 'current-menu-parent' ) );

      if ( ! empty( $_active ) && ! in_array( 'current-active' , $classes ) )
        $classes[] = 'current-active';

      return $classes;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu czr-dropdown-menu\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "</ul>\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

      //since WP 3.6.0
      add_filter( 'nav_menu_link_attributes' , 'czr_fn_add_nav_link_class', 10, 4 );

      $item_html = '';
      //ask the parent to do the hard work
      parent::start_el( $item_html, $item, $depth, $args, $id);


      //since WP 3.6.0
      remove_filter( 'nav_menu_link_attributes' , 'czr_fn_add_nav_link_class', 10, 4 );

      if ( $item->is_dropdown ) {
        $item_html = czr_fn_maybe_add_dropdown_html( $item_html, $args->dropdown_on );

      }else {

        if (stristr( $item_html, 'li class="divider' )) {
          $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
        }
        if (stristr( $item_html, 'li class="nav-header' )) {
          $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
        }

      }

      $output .= $item_html;
    }


    function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output) {
      //we add a property here
      //will be used in override start_el() and class filter
      $element->is_dropdown = ! empty( $children_elements[$element->ID]);

      $element->classes = apply_filters( 'czr_nav_menu_css_class', array_filter( empty( $element->classes) ? array() : (array)$element->classes ), $element, $args, $depth );

      //let the parent do the rest of the job !
      parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output);
    }

  }//end of class
endif;


/**
* Replace the walker for czr_fn_page_menu()
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
if ( ! class_exists( 'CZR_nav_walker_page' ) ) :
  class CZR_nav_walker_page extends Walker_Page {
    public $czr_location;

    function __construct() {
      add_filter( 'page_css_class' , array($this, 'czr_fn_add_bootstrap_classes'), 10, 4 );


      //
    }


    /**
    * hook : page_css_class
    */
    function czr_fn_add_bootstrap_classes($classes, $page = null, $depth = 0, $args = array() ) {

      /* Scope the changes only to Customizr fallback page menu!! */
      if ( ! ( isset( $args['fallback_cb'] ) && isset( $args['fallback_cb'][1] ) && 'czr_fn_page_menu' == $args['fallback_cb'][1] ) )
        return $classes;

      if ( ! is_array($classes) )
        return $classes;

      //check if the current menu item is a dropdown (has children)
      if ( in_array('page_item_has_children', $classes ) ) {
        if ( $depth === 0 ) {
          if ( ! in_array( 'czr-dropdown', $classes ) )
            $classes[] = 'czr-dropdown';
        } elseif ( $depth > 0 ) {
          if ( ! in_array( 'czr-dropdown-submenu', $classes ) )
            $classes[] = 'czr-dropdown-submenu';
        }
        //if ( ! in_array( 'btn-group', $classes ) )
        //  $classes[] = 'btn-group';

        if ( ! in_array( 'menu-item-has-children' , $classes ) )
          $classes[] = 'menu-item-has-children';
      }

      if ( $depth > 0 ) {
        if ( ! in_array( 'dropdown-item', $classes ) )
          $classes[] = 'dropdown-item';
      }

      if ( ! in_array( 'menu-item' , $classes ) )
        $classes[] = 'menu-item';

      $_active     = array_intersect( $classes, array( 'current_page_ancestor', 'current_page_item', 'current_page_parent' ) );
      if ( ! empty( $_active ) && ! in_array( 'current-active' , $classes ) )
        $classes[] = 'current-active';

       return $classes;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu czr-dropdown-menu\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "</ul>\n";
    }

    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {

      //since WP 4.8.0
      add_filter( 'page_menu_link_attributes' , 'czr_fn_add_nav_link_class', 10, 4 );

      //since the &$output is passed by reference, it will modify the value on the fly based on the parent method treatment
      //we just have to make some additional treatments afterwards
      parent::start_el( $item_html, $page, $depth, $args, $current_page );

      if ( $args['has_children'] ) {

          $item_html = czr_fn_maybe_add_dropdown_html( $item_html, $args['dropdown_on'] );

      }else {

        if (stristr( $item_html, 'li class="divider' )) {
          $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
        }
        if (stristr( $item_html, 'li class="nav-header' )) {
          $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
        }

      }
      //add link class attribute
      //before 4.8 there was no available filter to add a custom class to the link tag
      //also the link add no possibility to have a class at all, so we can easily add it
      global $wp_version;
      if ( version_compare( $wp_version, '4.8', '<' ) ) {
        $item_html = str_replace( '<a', '<a class="nav__link"', $item_html );
      }

      remove_filter( 'page_menu_link_attributes' , 'czr_fn_add_nav_link_class', 10, 4 );

      $output .= $item_html;

    }

 }//end of class
endif;


//maybe alter the item_html to add needed html code for dropdown
function czr_fn_maybe_add_dropdown_html( $item_html, $dropdown_on ) {

    //3 cases:
    //1 - dropdown on hover | dropdown on click for regular nav
    //2 - dropdown on click for vertical navs
    //3 - no dropdown ( vertical navs )
    switch( $dropdown_on ) {

      case 'link-action' :
                $item_html = str_replace(
                  array( '<a', '</a>' ),
                  array(
                    '<a data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false"',
                    '<span class="caret__dropdown-toggler"><i class="icn-down-small"></i></span></a>'
                  ),
                  $item_html
                );
                break;
      case 'caret-click' :
                $item_html = str_replace(
                  array( '<a', '</a>' ),
                  array(
                    //wrap both the link and the caret toggler in a convenient wrapper
                    '<span class="display-flex nav__link-wrapper align-items-start"><a',
                    '</a><button data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false" class="caret__dropdown-toggler czr-btn-link"><i class="icn-down-small"></i></button></span>'
                  ),
                  $item_html
                );
                break;
      //no caret for case 3

    }//end switch

    return $item_html;
}


//add menu item link class
function czr_fn_add_nav_link_class( $atts, $item, $args, $depth) {
    if ( !is_array( $atts ) )
      return $atts;

    if ( array_key_exists( 'class', $atts ) && ! empty( $atts[ 'class' ] ) ) {
        $atts['class'] = $atts['class'] . ' nav__link';
    } else {
      $atts['class'] = 'nav__link';
    }

    return $atts;
}