<?php
//the controllers are organized by groups in 4 classes
//Header, Content, Footer, Modules.
//The controller group classes are instanciated on demand if any of a view (or its child) requested it
//If the model is used as a method parameter, it shall be an array()
//=> because a controller can be checked and fired before the model has been instanciated
//=> always check the existence of the id
if ( ! class_exists( 'TC_controllers' ) ) :
  class TC_controllers {
    static $instance;
    private $controllers;
    static $controllers_instances = array();

    function __construct( $args = array() ) {
      self::$instance =& $this;
      $this -> controllers = array(
        'header' => array(
          'head', 'title', 'logo_wrapper', 'logo', 'sticky_logo', /*'logo_title', */'tagline', 'mobile_tagline', 'reset_margin_top', 'favicon', 'menu', 'sidenav', 'navbar_menu', 'navbar_secondary_menu', 'menu_button', 'mobile_menu_button', 'sidenav_menu_button', 'sidenav_navbar_menu_button', 
        ),
        'content' => array(
          '404', 'attachment', 'headings', 'no_results', 'page', 'post', 'post_footer', 'post_list', 'post_metas_button', 'post_metas_text', 'right_sidebar', 'left_sidebar', 'posts_list_headings', 'posts_list_description', 'author_description', 'posts_list_title', 'posts_list_search_title', 'singular_article', 'singular_title', 'post_list_title', 'post_navigation_singular', 'post_navigation_posts', 'comments', 'comment_list', 'comment_block_title', 'comment', 'tracepingback', 'comment_navigation', 'author_info', 'singular_headings'
        ),
        'footer' => array(
          'btt_arrow', 'footer_btt', 'footer_push'
      //    'widgets', 'colophon', 'back_to_top'
        ),
        'modules' => array(
          'social_block' 
        //   'breadcrumb', 'comment_bubbles', 'featured_pages', 'gallery', 'post_list_grid', 'post_thumbnails', 'slider'
        ),
      );

      //Store a group controller instance for later uses
      //takes 2 params
      //group name (string)
      //group controller instance (object)
      add_action( 'group_controller_instanciated', array( $this, 'tc_store_controller_instance'),10, 2);
    }//__construct()




    /***********************************************************************************
    * EXPOSED API
    ***********************************************************************************/
    //1) checks if a controller has been specified for the view. It can be either a function, or the method of a class
    //
    //2) if nothing is specified for the view, then checks if the view controller belongs to a particular group
    //if a match is found, then the group controller class is instanciated if not yet
    //then the existence of the controller method is checked and fired if exists
    //
    //3) if no match is found, the view is not allowed
    public function tc_is_possible( $model ) {
      //the returned value can be a string or an array( instance, method)
      $controller_cb = $this -> tc_get_controller( $model );
      //FOR TEST ONLY
      //return true;

      if ( ! empty( $controller_cb ) ) {
        return apply_filters( 'tc_set_control' , (bool) CZR() -> helpers -> tc_return_cb_result( $controller_cb, $model ) );
      }
      return true;
    }



    //@return bool
    //@param array() or object() model
    public function tc_has_controller( $model = array() ) {
      return ! empty( $this -> tc_build_controller( $model ) );
    }



    //@return a function string or an array( instance, method )
    //@param array() or object() model
    private function tc_get_controller( $model = array() ) {
      $model = is_object($model) ? (array)$model : $model;
      $controller_cb = "";

      //IS A CONTROLLER SPECIFIED AS PROPERTY OF THIS MODEL ?
      //and is a valid callback
      if ( isset($model['controller']) && is_callable( $model['controller'] )) {
        //a callback can be function or a method of a class
        $controller_cb =  $model['controller'];
      }
      //IS THERE A PRE-DEFINED CONTROLLER FOR THE VIEW ?
      else {
        //the default controller match can either be:
        //a) based on the model id (has the precedence )
        //b) based on the controller model field, when not a callback
        //c) based on the template base name
        $controller_ids   = array_filter( array( $model['id'], ! empty( $model['controller'] ) ? $model['controller'] : '', basename( $model['template'] ) ) );
        if ( $this -> tc_has_default_controller( $controller_ids ) ) { 
          $controller_cb = $this -> tc_get_default_controller( $controller_ids );
          //make sure the default controller is well formed
          //the default controller should look like array( instance, method )
          if ( empty($controller_cb) ) {
            do_action( 'tc_dev_notice', 'View : '.$model['id'].'. The default group controller has not been instanciated');
            return "";
          }//if
        }//if has default controller
      }//else
      return $controller_cb;
    }



    //@return boolean
    //@walks the controllers setup array until a match is found
    private function tc_has_default_controller( $controller_ids ) {
      foreach ( $this -> controllers as $group => $views_id )
        foreach( $controller_ids as $id )
          if ( in_array($id, $views_id) )
            return true;
        //foreach
      //foreach
      return false;
    }


    //tries to find a default controller group for this method
    //@return array(instance, method) or array() if nothind found
    private function tc_get_default_controller( $controller_ids ) {
      $controller_cb = false;
      foreach ( $this -> controllers as $group => $views_id )
        foreach( $controller_ids as $id )
          if ( in_array($id, $views_id) ) {
            $controller_cb = $id;
            $controller_group = $group;
            break 2;
          }//if
        //foreach
      //foreach
      //return here is no match found
      if ( ! $controller_cb ) {
        do_action( 'tc_dev_notice', 'View : '.$id.'. No control method found.');
        return array();
      }


      //Is this group controller instanciated ?
      if ( ! array_key_exists($controller_group, self::$controllers_instances) ) {
        //this will actually load the class file and instanciate it
        $_instance = $this -> tc_instanciate_group_controller($controller_group);
      } else {
        $_instance = $this -> tc_get_controller_instance($controller_group);
      }

      //stop here if still nothing is instanciated
      if ( ! isset( $_instance) || ! is_object( $_instance ) ) {
        do_action( 'tc_dev_notice', 'View : '.$id.'. The control class for : ' . $controller_group . ' has not been instanciated.' );
        return array();
      }

      //build the method name
      $method_name = "tc_display_view_{$controller_cb}";//ex : tc_display_view_{favicon_control}()


      //make sure we have a class instance and that the requested controller method exists in it
      if ( method_exists( $_instance , $method_name ) )
        return array( $_instance, $method_name );

      do_action( 'tc_dev_notice', 'View : '.$id.'. The method : ' . $method_name . ' has not been found in group controller : '.$controller_group );
      return array();
    }


    //load the class file if exists
    //instanciate the class is exists
    //@param is a string : header, content, footer, modules
    //@return the $instance
    private function tc_instanciate_group_controller( $group ) {
      $_path  = "controllers/class-controller-{$group}.php";
      $_class = "TC_controller_{$group}";
      $_instance = false;

      tc_fw_require_once( $_path );

      if ( class_exists($_class) ) {
        $_instance = new $_class;
        do_action( 'group_controller_instanciated', $group, $_instance );
      }
      return $_instance;
    }


    //listens to group_controller_instanciated
    //stores the groupd controller instance in the property : self::$controllers_instances
    //@return void()
    public function tc_store_controller_instance( $group , $_instance ) {
      $controller_instances = self::$controllers_instances;
      if ( array_key_exists($group, $controller_instances) )
        return;

      $controller_instances[$group] = $_instance;
      self::$controllers_instances = $controller_instances;
    }


    //get an already instanciated controller group instance
    public function tc_get_controller_instance( $group ) {
      $controller_instances = self::$controllers_instances;
      if ( ! array_key_exists($group, $controller_instances) )
        return;
      return $controller_instances[$group];
    }



    /******************************************************************
    * HELPERS
    ******************************************************************/
        /**
    * Return object post type
    *
    * @since Customizr 3.0.10
    *
    */
    function tc_get_post_type() {
      global $post;

      if ( ! isset($post) )
        return;

      return $post -> post_type;
    }



    /**
    * Check if we show posts or page content on home page
    *
    * @since Customizr 3.0.6
    *
    */
    function tc_is_home_empty() {
      //check if the users has choosen the "no posts or page" option for home page
      return ( ( is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
    }



    /**
    * Boolean : check if we are in the no search results case
    *
    * @package Customizr
    * @since 3.0.10
    */
    function tc_is_no_results() {
      global $wp_query;
      return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
    }


  }//end of class
endif;
