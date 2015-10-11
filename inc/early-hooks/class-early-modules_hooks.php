<?php
/**
* FIRED ON 'after_setup_theme'
* Before the query is ready
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
if ( ! class_exists( 'TC_modules_hooks' ) ) :
  class TC_modules_hooks {
      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
        self::$instance =& $this;

        /***************************************************************************************************************
        * POST LISTS GRID EARLY HOOKS
        ***************************************************************************************************************/
        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_set_expanded_sticky_bool_and_val') );
        //must be fired after the bool and the val properties have been set
        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_maybe_excl_first_sticky'), 20 );

      }//constructor


      /***************************************************************************************************************
      * POST LISTS GRID EARLY ACTIONS
      ***************************************************************************************************************/
      /**
      * hook : pre_get_posts : 10
      *
      * @return void()
      * check if we have to expand the first sticky post
      * set 2 properties accessible in the grid child class
      */
      public function tc_grid_set_expanded_sticky_bool_and_val( $query = null ){
        //user option has to be enabled
        if ( ! apply_filters( 'tc_grid_expand_featured', esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_expand_featured') ) ) )
          return;

        global $wp_query, $wpdb;
        $query = ( $query ) ? $query : $wp_query;

        if ( ! $query->is_main_query() )
          TC_modules_control::$expanded_sticky_bool = false;
        if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $wp_query->is_posts_page ) )
          TC_modules_control::$expanded_sticky_bool = false;



        if ( ! TC_modules_control::$expanded_sticky_bool ) {
          $_sticky_posts = get_option('sticky_posts');
          // get last published sticky post
          if ( is_array($_sticky_posts) && ! empty( $_sticky_posts ) ) {
            $_where = implode(',', $_sticky_posts );
            TC_modules_control::$expanded_sticky_val = $wpdb->get_var(
                   "
                   SELECT ID
                   FROM $wpdb->posts
                   WHERE ID IN ( $_where )
                   ORDER BY post_date DESC
                   LIMIT 1
                   "
            );
            TC_modules_control::$expanded_sticky_bool = true;
          } else {
            TC_modules_control::$expanded_sticky_val = null;
            TC_modules_control::$expanded_sticky_bool = false;
          }
        }
      }


      /**
      * hook : pre_get_posts : 20
      * exclude the first sticky post
      */
      function tc_grid_maybe_excl_first_sticky( $query ){
        if ( TC_modules_control::$instance -> tc_is_grid_enabled() && TC_modules_control::$expanded_sticky_bool )
          $query->set('post__not_in', array( TC_modules_control::$expanded_sticky_val ) );
      }
  }//end of class
endif;