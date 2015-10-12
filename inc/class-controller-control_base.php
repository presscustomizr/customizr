<?php
/**
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_control_base' ) ) :
  class TC_control_base {
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

      add_action( 'wp'   , array($this, 'tc_fire_views_on_query_ready') );
    }


    /***************************************************************************************************************
    * FIRE THE RELEVANT VIEW IN CHILDREN CONTROLLERS
    ***************************************************************************************************************/
    public function tc_fire_views_on_query_ready() {}

  }//end of class
endif;