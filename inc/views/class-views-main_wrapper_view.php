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
if ( ! class_exists( 'TC_main_wrapper_view' ) ) :
  class TC_main_wrapper_view {
    static $instance;
    public $args;
    public $render_on_hook = '__main_wrapper';//this is the default hook declared in the index.php template


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
      add_action( $this -> render_on_hook   , array($this, 'tc_render_main_wrapper') );
    }



    //hook : $this -> render_on_hook
    public function tc_render_main_wrapper() {
      ?>
        <div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

            <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

            <div class="container" role="main">
                <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">
                  <?php do_action('__daloop'); ?>
                </div><!--.row -->
            </div><!-- .container role: main -->

            <?php do_action( '__after_main_container' ); ?>

        </div><!--#main-wrapper"-->
      <?php
    }

  }//end of class
endif;