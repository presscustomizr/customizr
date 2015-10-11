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
if ( ! class_exists( 'TC_footer_view' ) ) :
  class TC_footer_view {
    static $instance;
    public $args;
    public $render_on_hook = '__footer_main';//this is the default hook declared in the index.php template


    function __construct( $_args ) {
      self::$instance =& $this;
      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      //Actually renders the loop
      add_action( $this -> render_on_hook   , array($this, 'tc_render_main_footer') );
    }



    //hook : $this -> render_on_hook
    public function tc_render_main_footer() {
      ?>
        <!-- FOOTER -->
        <footer id="footer" class="<?php echo tc__f('tc_footer_classes', '') ?>">
          <?php do_action( '__footer' ); // hook of footer widget and colophon?>
        </footer>
      <?php
    }

  }//end of class
endif;