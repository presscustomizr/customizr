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
if ( ! class_exists( 'TC_view_base' ) ) :
  class TC_view_base {
    static $instance;
    public $render_on_hook = false;//this is the default hook declared in the index.php template

    function __construct( $_args ) {
      self::$instance =& $this;

      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $_args[ $key ] ) ) {
          $this->$key = $_args[ $key ];
        }
      }

      //Renders the view on the requested hook
      if ( false !== $this -> render_on_hook )
        add_action( $this -> render_on_hook   , array($this, 'tc_render') );
    }


    //hook : $this -> render_on_hook
    public function tc_render() {}

  }//end of class
endif;