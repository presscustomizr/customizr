<?php

/**
* Fires the theme : constants definition, core classes loading
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC___' ) ) :

  final class TC___ {
    public static $instance;//@todo make private in the future
    public $tc_core;
    public $is_customizing;
    public static $theme_name;
    public static $tc_option_group;

    public $views;//object, stores the views
    public $controllers;//object, stores the controllers

    public static function tc_instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof TC___ ) ) {
        self::$instance = new TC___();
        self::$instance -> tc_setup_constants();
        self::$instance -> tc_load();
        self::$instance -> collection = new TC_Collection();
        self::$instance -> controllers = new TC_Controllers();
        self::$instance -> helpers = new TC_Helpers();
      }
      return self::$instance;
    }


    private function tc_load() {
      //load the new classes
      require_once( sprintf( '%score/class-model.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-collection.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-view.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-controllers.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-helpers.php' , TC_BASE ) );
    }

  }
endif;



//shortcut function to instanciate easier
if ( ! function_exists('tc_new') ) {
  function tc_new( $_to_load, $_args = array() ) {
    TC___::$instance -> tc__( $_to_load , $_args );
    return;
  }
}
//endif;

/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return TC___::tc_instance();
}