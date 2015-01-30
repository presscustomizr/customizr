<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_contx' ) ) :
  class TC_contx {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    static $customize_context;

    function __construct () {
      self::$instance =& $this;
      //add_action ( 'init'                         , array( $this , 'tc_set_customize_context') );
      self::$customize_context = $this -> tc_get_context();
      //add_action ( 'init'                         , array( $this , 'tc_init_customize_transient') );
      //clean the transient if customizer has been fired without saving
      //add_action ( 'admin_init'                   , array( $this , 'tc_init_customize_transient') );

      add_action ( 'wp_before_admin_bar_render'   , array( $this , 'tc_remove_initial_customize_menu' ));
      add_action ( 'admin_bar_menu'               , array( $this , 'tc_add_customize_menu' ), 100);

      ### LOAD EXTENDED WP SETTING CLASS ###
      add_action ( 'customize_register'           , array( $this , 'tc_load_customize_settings_class' ) ,0,1);

      ### ACTIONS ON CUSTOMIZER SAVE ###
      //Check if customizer has been saved properly before updating settings in DB => avoid cross page customization
      //add_action ( 'customize_save'               , array( $this , 'tc_check_cross_page_customization' ) );

      ### AJAX ACTIONS ###
      //Updates object suffix if needed
      //add_action ( 'wp_ajax_tc_update_context'    , array( $this , 'tc_ajax_update_context' ), 0 );

      ### ADD JS Params to control.js ##
      add_filter( 'tc_js_customizer_control_params' , array( $this , 'tc_add_controljs_params' ) );

      ### FILTER OPTIONS ON GET (OUTSIDE CUSTOMIZER) ###
      add_filter( 'tc_get_option'                 , array( $this , 'tc_contx_option'), 10 , 3 );

    }//end of construct


    function tc_contx_option( $original , $option_name , $option_group ) {
      $_context = TC_contx::$instance ->tc_get_context();
      //make sure only tc_theme_options are filtered
      //if not an array then back to old way.
      if ( TC___::$tc_option_group != $option_group || ! is_array($original) )
        return $original;

      //do we have a option for this context ?
      if ( isset($original[$_context]) )
        return $original[$_context];
      //@to do add other all like all pages, all posts, all cat, all tags, all_authors
      //do we have all_contexts defined ?
      if ( isset($original['all_contexts']) )
        return $original['all_contexts'];

      return;
    }


    function tc_get_context( $_requesting_wot = null ) {
      //Handle the case when we request it in AJAX => no transient update!
      if ( TC___::$instance -> tc_doing_customizer_ajax() )
        return $this -> tc_build_context( 'ajaxing', $_requesting_wot );

      //Those conditions are important : the customizer_register function is ran several time during the customizer init
      //We want to define the transient only once, on the first run
      //@to do faut il rajouter la condition did_action('after_setup_theme') ?
      //@to do && defined('IFRAME_REQUEST') ?

      if ( TC___::$instance -> tc_is_customizing() && ! TC___::$instance -> tc_doing_customizer_ajax() && defined('IFRAME_REQUEST') )
        return $this -> tc_build_context( 'customizing', $_requesting_wot );


      //Not customizing context
      if ( ! TC___::$instance -> tc_is_customizing() && ! TC___::$instance -> tc_doing_customizer_ajax() )
        return $this -> tc_build_context( null, $_requesting_wot);

      return;
    }



    private function tc_build_context( $_doing_wot = null, $_requesting_wot = null ) { //$type = null , $obj_id = null
      $parts    = array();

      switch ( $_doing_wot ) {
        case 'ajaxing':
          return isset($_POST['TCContext']) ? $_POST['TCContext'] : null;
        break;

        case 'customizing':
          $parts = $this -> tc_get_url_contx();
        break;

        default:
          $parts = $this -> tc_get_query_contx();
        break;
      }

      if ( is_array( $parts) && ! empty( $parts ) )
        list($type , $obj_id) =  $parts;

      switch ( $_requesting_wot ) {
        case 'type':
          if ( false != $type )
            return "_{$type}";
        break;

        default:
          if  ( false !== $type && false !== $obj_id )
            return "_{$type}_{$obj_id}";
          else if ( false != $type && ! $obj_id )
            return "_{$type}";
        break;
      }
      return "";
    }


    private function tc_get_url_contx() {
      $type         = isset( $_GET['type']) ? $_GET['type'] : false;
      $obj_id       = isset( $_GET['obj_id']) ? $_GET['obj_id'] : false;
      return apply_filters( 'tc_get_url_contx' , array( $type , $obj_id ) );
    }


    /*
    * @return array
    */
    private function tc_get_query_contx() {
      //don't call get_queried_object if the $query is not defined yet
      global $wp_query;
      if ( ! isset($wp_query) || empty($wp_query) )
        return array();

      $current_obj  = get_queried_object();
      $type         = false;
      $obj_id       = false;

      //post, custom post types, page
      if ( isset($current_obj -> post_type) ) {
          $type       = $current_obj -> post_type;
          $obj_id     = $current_obj -> ID;
      }

      //taxinomies : tags, categories, custom tax type
      if ( isset($current_obj -> taxonomy) && isset($current_obj -> term_id) ) {
          $type       = $current_obj -> taxonomy;
          $obj_id     = $current_obj -> term_id;
      }

      //author page
      if ( isset($current_obj -> data -> user_login ) && isset($current_obj -> ID) ) {
          $type       = 'user';
          $obj_id     = $current_obj -> ID;
      }

      if ( is_404() )
        $type       = '404';
      if ( is_search() )
        $type       = 'search';
      if ( is_date() )
        $type       = 'date';

      return apply_filters( 'tc_get_query_contx' , array( $type , $obj_id ) , $current_obj );
    }



    function tc_load_customize_settings_class() {
      locate_template( 'inc/class-contx-wp-settings.php' , $load = true, $require_once = true );
    }


    function tc_add_controljs_params( $_params ) {
      if ( is_array($_params) )
        return array_merge(
          $_params,
          array(
            'TCContext'     => self::$customize_context
            )
        );
    }


    function tc_remove_initial_customize_menu() {
      if ( ! current_user_can( 'edit_theme_options' ) || is_admin() )
        return;
      global $wp_admin_bar;
      $wp_admin_bar->remove_menu('customize');
    }


    function tc_add_customize_menu() {
      if ( ! current_user_can( 'edit_theme_options' ) || is_admin() )
        return;

      global $wp_admin_bar;
      //declares $type and $obj_id
      list($type , $obj_id) = $this -> tc_get_query_contx();

      $current_url    = join(",", array(
                          ( is_ssl() ? 'https://' : 'http://' ),
                          $_SERVER['HTTP_HOST'],
                          $_SERVER['REQUEST_URI']));

      $type   = is_null($type) ? false : $type;
      $obj_id = is_null($obj_id) ? false : $obj_id;
      $title = '';

      if ( false != $type && false != $obj_id ) {
         $args    = array( 'url' => urlencode( $current_url ) , 'type' => $type , 'obj_id' => $obj_id );
         $title   = sprintf('%1$s #%2$s' , $type, $obj_id );
      } else if ( false != $type && ! $obj_id ) {
        $args  = array( 'url' => urlencode( $current_url ) , 'type' => $type );
        $title  = sprintf('%1$s' , $type );
      } else {
        $args  = array();
      }
      //Add it under appearance
      $wp_admin_bar -> add_menu( array(
          'parent' => 'appearance',
          'id'     => 'pc-wp-admin-appeance',
          'title'  => sprintf( '%1$s %2$s' , __( 'Customize' , 'customizr' ), $title ),
          'href'   => add_query_arg( $args , wp_customize_url() ),
          'meta'   => array(
              'class' => 'hide-if-no-customize',
              'title'   => sprintf( '%1$s %2$s' , __( 'Customize this context :' , 'customizr' ) , $title ),
          ),
      ) );
      //Add it in the wp admin bar
      $wp_admin_bar->add_menu( array(
         'parent'   => false,
         'id'     => 'tc-customize-button' ,
         'title'    => sprintf( '%1$s' , __( 'Customize' , 'customizr' ) ),
         'href'     => add_query_arg( $args , wp_customize_url() ),
         'meta'     => array(
            'class' => 'hide-if-no-customize',
             'title'    => sprintf( '%1$s %2$s' , __( 'Customize this context :' , 'customizr' ) , $title ),
            ),
     ));
    }




    function tc_check_cross_page_customization() {
      $db_obj_suffix        = self::$customize_context;
        ?>
      <pre>
        <?php print_r( 'in tc check cross page customization' ); ?>
      </pre>
        <?php
      ?>
        <pre>
          <?php print_r( $db_obj_suffix ); ?>
        </pre>
      <?php
      ?>
        <pre>
          <?php print_r( $_POST['customized'] ); ?>
        </pre>
      <?php

      /*$customized_sets      = json_decode( wp_unslash( $_POST['customized'] ), true );

      $obj_suffix_to_save   = isset($customized_sets["tc_hidden_context"]) ? $customized_sets["tc_hidden_context"] : '';
      $obj_suffix_to_save   = ! $obj_suffix_to_save ? '' : $obj_suffix_to_save;

      if ( $db_obj_suffix != $obj_suffix_to_save ){
        //first reset the transient
        set_transient( 'tc_current_customize_context' , $obj_suffix_to_save, 60*60 );
        //then die
        //die;
      }*/
    }


    //ON CUSTOMIZER SAVE :
    //1) identify the current context with obj suffix
    function tc_ajax_update_context() {
      //check_ajax_referer( 'tc-customizer-nonce', 'TCNonce' );
/*      ?>
        <pre>
          <?php print_r($_POST); ?>
        </pre>
      <?php*/

      $db_obj_suffix    = self::$customize_context;
      ?>
        <pre>
          <?php print_r('BEFORE : '. $db_obj_suffix); ?>
        </pre>
      <?php
      $customizer_current_suffix  = isset($_POST['TCContext']) ? $_POST['TCContext'] : '';
      if ( $db_obj_suffix != $customizer_current_suffix ){
        set_transient( 'tc_current_customize_context' , $customizer_current_suffix, 60*60 );
      }
      echo get_transient( 'tc_current_customize_context');
      ?>
        <pre>
          <?php print_r('AFTER : '. get_transient( 'tc_current_customize_context')); ?>
        </pre>
      <?php
      die;
    }

  }
endif;