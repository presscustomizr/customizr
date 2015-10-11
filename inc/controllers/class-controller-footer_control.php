<?php
/**
* HEADER CONTROLLER CLASS
* FIRED ON INIT
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.10
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_footer_control' ) ) :
  class TC_footer_control {
    static $instance;

    function __construct( $_args ) {
      self::$instance =& $this;

      add_action( 'wp' , array( $this, 'tc_fire_views_on_query_ready' ) );
    }



    /***************************************************************************************************************
    * FIRE RELEVANT VIEW
    ***************************************************************************************************************/
    //hook : 'wp'
    function tc_fire_views_on_query_ready() {
      if ( is_admin() )
        return;

      if ( apply_filters( 'tc_display_main_footer' , true ) )
        tc_new( array('footer' => array( array('inc/views/footer', 'footer_main') ) ) );

    }


    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/

  }//end of class
endif;