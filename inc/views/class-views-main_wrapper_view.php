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
  class TC_main_wrapper_view extends TC_view_base {
    static $instance;

    function __construct( $_args = array() ) {
      self::$instance =& $this;

      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      //Instanciates the parent class.
      parent::__construct( $_args );

    }



    //hook : $this -> render_on_hook
    //overrides parent's method
    public function tc_render() {
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