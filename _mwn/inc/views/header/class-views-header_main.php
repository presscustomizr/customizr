<?php
/**
* Renders the main wrapper
* Instanciated from the children on 'wp'
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_header_main' ) ) :
  class TC_header_main extends TC_view_base {
    static $instance;

    function __construct( $_args ) {
      self::$instance =& $this;

      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      // Instanciates the parent class.
      // => Renders Customizr header on hook '__header_main'
      parent::__construct( $_args );

      //render the WP header
      add_action ( '__before_main_wrapper'  , 'get_header');
    }



    //hook : $this -> render_on_hook
    //overrides parent's method
    public function tc_render() {
      ?>
          <header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>" role="banner">
            <?php
              // The '__header' hook is used with the following callback functions (ordered by priorities) :
              //TC_header_main::$instance->tc_logo_title_display(), TC_header_main::$instance->tc_tagline_display(), TC_header_main::$instance->tc_navbar_display()
              do_action( '__header' );
            ?>
          </header>
      <?php
    }

  }//end of class
endif;