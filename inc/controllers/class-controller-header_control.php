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
if ( ! class_exists( 'TC_header_control' ) ) :
  class TC_header_control extends TC_control_base {
    static $instance;

    function __construct( $_args = array() ) {
      self::$instance =& $this;

      //Instanciates the parent class.
      parent::__construct( $_args );
    }



    /***************************************************************************************************************
    * FIRE RELEVANT VIEW
    ***************************************************************************************************************/
    //hook : 'wp'
    function tc_fire_views_on_query_ready() {
      if ( is_admin() )
        return;

      //HEAD ( and mandatory ) VIEW : INCLUDES wp_head
      tc_new(
        array( 'header' => array( array('inc/views/header', 'head') ) ),
        array( 'render_on_hook' => '__before_body' )
      );

      //OTHER HEADER OPTIONAL VIEWS
      if ( ! apply_filters( 'tc_display_header', true ) )
        return;

      //HEADER WRAPPER
      tc_new(
          array( 'views' => array( array('inc/views/header', 'header_main') ) ),
          array( 'render_on_hook' => '__header_main' )
      );

      //FAVICON
      //is there a WP favicon set ? If yes then let WP do the job
      if ( ! function_exists('has_site_icon') || ! has_site_icon() ) {
        tc_new(
          array( 'header' => array( array('inc/views/header', 'logo_title') ) ),
          array( 'render_on_hook' => 'wp_head' )
        );
      }

      //LOGO / TITLE
      tc_new(
        array( 'header' => array( array('inc/views/header', 'logo_title') ) ),
        array( 'render_on_hook' => '__header' )
      );


      //MENUS
      if ( ! (bool) TC_utils::$inst->tc_opt('tc_hide_all_menus') ) {
        tc_new( array('header' => array( array('inc/views/header', 'menu') ) ) );
        //the custom nav walker classes will be instanciated when firing the menus
        tc_new( array('header' => array( array('inc/views/header', 'nav_walker') ) ), array( '_instanciate' => false ) );
      }
    }


    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/

  }//end of class
endif;