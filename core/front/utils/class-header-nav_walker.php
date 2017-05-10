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
      $item_html = '';
      //ask the parent to do the hard work
      parent::start_el( $item_html, $item, $depth, $args, $id);

      if ( $item->is_dropdown ) {
        //3 cases:
        //1 - dropdown on hover
        //2 - dropdown on click
        //3 - no dropdown ( sidenav )
        switch( $args->dropdown_type ) {

          case 'hover' :
                    $item_html = str_replace(
                      array( '<a', '</a>' ),
                      array(
                        '<a data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false"',
                        '<span class="caret__dropdown-toggler"><i class="icn-down-small"></i></span></a>'
                      ),
                      $item_html
                    );
                    break;

          case 'click' :
                    $item_html = str_replace(
                      array( '<a', '</a>' ),
                      array(
                        '<a data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false"',
                        '<span class="caret__dropdown-toggler"><i class="icn-down-small"></i></span></a>'
                      ),
                      $item_html
                    );
                    break;

          //no caret for case 3

        }//end switch


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


    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
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

      //since the &$output is passed by reference, it will modify the value on the fly based on the parent method treatment
      //we just have to make some additional treatments afterwards
      parent::start_el( $item_html, $page, $depth, $args, $current_page );

      if ( $args['has_children'] ) {

          //3 cases:
          //1 - dropdown on hover
          //2 - dropdown on click
          //3 - no dropdown ( sidenav )
          switch( $args[ 'dropdown_type' ] ) {

            case 'hover' :
                      $item_html = str_replace(
                        array( '<a', '</a>' ),
                        array(
                          '<a data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false"',
                          '<span class="caret__dropdown-toggler"><i class="icn-down-small"></i></span></a>'
                        ),
                        $item_html
                      );
                      break;

            case 'click' :
                      $item_html = str_replace(
                        array( '<a', '</a>' ),
                        array(
                          '<a data-toggle="czr-dropdown" aria-haspopup="true" aria-expanded="false"',
                          '<span class="caret__dropdown-toggler"><i class="icn-down-small"></i></span></a>'
                        ),
                        $item_html
                      );
                      break;

            //no caret for case 3

          }//end switch


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

 }//end of class
endif;
