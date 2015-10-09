<?php
/**
* LOOP CONTROLLER CLASS
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
if ( ! class_exists( 'TC_controller' ) ) :
  class TC_controller extends TC_loop_base {
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

      //SINGLE POST
      if ( $this -> tc_is_single_post() )
        tc_new( array('content' => array( array('inc/parts', 'post') ) ) );

      //POST LIST
      if ( $this -> tc_is_post_list() )
        tc_new( array('content' => array( array('inc/parts', 'post_list') ) ) );
    }



    /***************************************************************************************************************
    * HELPERS : CONTEXT CHECKER
    ***************************************************************************************************************/
    /**
    * Single post view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.2.0
    */
    static function tc_is_single_post() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      return apply_filters( 'tc_show_single_post_content',
        isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! tc__f( '__is_home_empty')
      );
    }



    /**
    * @return  bool
    * Controller of the posts list view
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    static function tc_is_post_list() {
      global $wp_query;
      //must be archive or search result. Returns false if home is empty in options.
      return apply_filters( 'tc_post_list_controller',
        ! is_singular()
        && ! is_404()
        && 0 != $wp_query -> post_count
        && ! tc__f( '__is_home_empty')
      );
    }

  }//end of class
endif;