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


    function __construct () {
      self::$instance =& $this;
      add_action ( 'init'                         , array( $this , 'tc_clean_customize_transient') );
      //clean the transient if customizer has been fired without saving
      add_action ( 'admin_init'                   , array( $this , 'tc_clean_customize_transient') );
      add_action ( 'wp_before_admin_bar_render'   , array( $this , 'tc_remove_initial_customize_menu' ));
      add_action ( 'admin_bar_menu'               , array( $this , 'tc_add_customize_menu' ), 100);

      ### ACTIONS ON CUSTOMIZER SAVE ###
      //Check if customizer has been saved properly before updating settings in DB => avoid cross page customization
      add_action ( 'customize_save'               , array( $this , 'tc_check_cross_page_customization' ) );

      ### AJAX ACTIONS ###
      //Updates object suffix if needed
      add_action ( 'wp_ajax_tc_update_context'    , array( $this , 'tc_ajax_update_context' ), 0 );

      ### ADD AN HIDDEN CONTEXT FIELD TO THE CUSTOMIZER MAP ##
      //update setting_control_map
      add_filter ( 'tc_add_setting_control_map'   , array( $this ,  'tc_add_hidden_context_field'), 100 );
    }//end of construct


    function tc_add_hidden_context_field( $_map ) {
      $hidden_fields_option_map = array(
        //send the obj suffix in $_POST => to be check before saving to avoid cross customizations
        'tc_hidden_context'             => array(
          'control'      => 'TC_controls' ,
          'section'      => 'tc_skins_settings',
          'type'         => 'tc-context-hidden',
          'tc_context'   => $this -> tc_get_context()
        ),
      ); //end of hidden fields options

      $_map['add_setting_control'] = array_merge($_map['add_setting_control'] , $hidden_fields_option_map );
      return $_map;
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
      list($type , $obj_id) = $this -> tc_get_context_parts();

      $current_url    = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

      $type   = is_null($type) ? false : $type;
      $obj_id = is_null($obj_id) ? false : $obj_id;

      //declare empty vars
      $args  = array();
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


    function tc_clean_customize_transient() {
      if ( TC___::$instance -> tc_is_customizing() || TC___::$instance -> tc_doing_customizer_ajax() )
        return;
      delete_transient( 'tc_current_customize_context' );
    }


    function tc_get_context() {
      //Handle the case when we request it in AJAX => no transient update!
      if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        return get_transient( 'tc_current_customize_context');

      $obj_suffix = '';

      //Those conditions are important : the customizer_register function is ran several time during the customizer init
      //We want to define the transient only once, on the first run
      //@to do faut il rajouter la condition did_action('after_setup_theme') ?
      if ( TC___::$instance -> tc_is_customizing() && defined('IFRAME_REQUEST') ) {
          $transient  = get_transient( 'tc_current_customize_context' );
          if ( isset( $_GET['type']) && isset( $_GET['obj_id']) ) {
            $obj_suffix = TC_contextualizr::$instance -> tc_build_context( $_GET['type'], $_GET['obj_id'] );
          } else if (isset( $_GET['type']) && !isset( $_GET['obj_id'])) {
            $obj_suffix = TC_contextualizr::$instance -> tc_build_context( $_GET['type'] );
          }
          else {
            $obj_suffix = '';
          }//end if isset $_GET vars

          //if no transient OR transient exists but different than the current context
          //(customizer not saved or 2 customizer at the same time for example)
          //then reset the transient
          if ( ! $transient || $transient != $obj_suffix )
              set_transient( 'tc_current_customize_context', $obj_suffix, 60*60 );
          $obj_suffix = get_transient( 'tc_current_customize_context' );
      }//end if customizing
      else if ( TC___::$instance -> tc_is_customizing() ) {
          $obj_suffix = get_transient( 'tc_current_customize_context' );
      }
      if ( ! TC___::$instance -> tc_is_customizing() ) {
        $obj_suffix = $this -> tc_build_context();
      }
      return $obj_suffix;
    }


    function tc_build_context( $type = null , $obj_id = null) {
      if ( is_null($type) && is_null($obj_id) ) {
        $parts = $this -> tc_get_context_parts();
        if ( ! empty( $parts ) )
          list($type , $obj_id) =  $parts;
      }
      $type   = is_null($type) ? false : $type;
      $obj_id = is_null($obj_id) ? false : $obj_id;

      if  ( false != $type && false != $obj_id )
        return "_{$type}_{$obj_id}";
      else if ( false != $type && ! $obj_id )
        return "_{$type}";
      else
        return "";
    }



    /*
    * @return array
    */
    function tc_get_context_parts() {
      //don't call get_queried_object if the $query is not defined yet
      global $wp_query;
      if ( ! isset($wp_query) || empty($wp_query) )
        return array();

      $current_obj  = get_queried_object();
      $type         = '';
      $obj_id       = '';

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

      return apply_filters( 'tc_get_context_parts' , array( $type , $obj_id ) , $current_obj );
    }



    function tc_check_cross_page_customization() {
      $db_obj_suffix    = $this -> tc_get_context();

      $customized_sets  = json_decode( wp_unslash( $_POST['customized'] ), true );

      $obj_suffix_to_save = isset($customized_sets["tc_hidden_context"]) ? $customized_sets["tc_hidden_context"] : '';
      $obj_suffix_to_save = ! $obj_suffix_to_save ? '' : $obj_suffix_to_save;

      if ( $db_obj_suffix != $obj_suffix_to_save ){
        //first reset the transient
        set_transient( 'tc_current_customize_context' , $obj_suffix_to_save, 60*60 );
        //then die
        //die;
      }
    }

    //ON CUSTOMIZER SAVE :
    //1) identify the current context with obj suffix
    function tc_ajax_update_context() {
      check_ajax_referer( 'tc-customizer-nonce', 'TCNonce' );
          /*?>
        <pre>
          <?php print_r($_POST); ?>
        </pre>
      <?php*/

      $db_obj_suffix    = $this -> tc_get_context();
      /*?>
        <pre>
          <?php print_r('BEFORE : '. $db_obj_suffix); ?>
        </pre>
      <?php*/
      $customizer_current_suffix  = isset($_POST['TCContext']) ? $_POST['TCContext'] : '';
      if ( $db_obj_suffix != $customizer_current_suffix ){
        set_transient( 'tc_current_customize_context' , $customizer_current_suffix, 60*60 );
      }
      echo get_transient( 'tc_current_customize_context');
      /*?>
        <pre>
          <?php print_r('AFTER : '. get_transient( 'tc_current_customize_context')); ?>
        </pre>
      <?php*/
      die;
    }

  }
endif;
