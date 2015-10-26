<?php
//the controllers are organized by groupd in 4 classes
//Header, Content, Footer, Modules.
//The controller group classes are instanciated on demand if any of a view (or its child) requested it
if ( ! class_exists( 'TC_controllers' ) ) :
  class TC_controllers {
    static $instance;
    private $controllers;
    static $controllers_instances = array();

    function __construct( $args = array() ) {
      self::$instance =& $this;
      $this -> controllers = array(
        'header' => array(
          'head', 'logo_title', 'tagline', 'menu', 'favicon', 'joie'
        ),
        'content' => array(
          '404', 'attachment', 'comments', 'headings', 'no_results', 'page', 'post', 'post_list', 'post_metas', 'post_navigation', 'sidebar'
        ),
        'footer' => array(
          'widgets', 'colophon', 'back_to_top'
        ),
        'modules' => array(
          'breadcrumb', 'comment_bubbles', 'featured_pages', 'gallery', 'post_list_grid', 'post_thumbnails', 'slider'
        )
      );

      //Store a group controller instance for later uses
      //takes 2 params
      //group name (string)
      //group controller instance (object)
      add_action( 'group_controller_instanciated', array( $this, 'tc_store_controller_instance'),10, 2);
    }

    //1) checks if a controller has been specified for the view. It can be either a function, or the method of a class
    //
    //2) if nothing is specified for the view, then checks if the view controller belongs to a particular group
    //if a match is found, then the group controller class is instanciated if not yet
    //then the existence of the controller method is checked and fired if exists
    //
    //3) if no match is found, the view is not allowed
    function tc_is_possible($id) {
      //the returned value can be a string or an array( instance, method)
      $controller_cb = $this -> tc_get_controller( $id );

      if ( false !== $controller_cb ) {
        return apply_filters( 'tc_set_control' , (bool) CZR() -> helpers -> tc_return_cb_result( $controller_cb ) );
      }
      return;
    }



    //@return a function string or an array( instance, method )
    private function tc_get_controller($id) {
      $controller_cb = false;
      //IS A CONTROLLER SPECIFIED AS PROPERTY OF THIS VIEW ?
      if ( ! empty( CZR() -> views -> tc_get_controller($id) ) ) {
        //a callback can be function or a method of a class
        $controller_cb = CZR() -> views -> tc_get_controller($id);
      }
      //IS THERE A PRE-DEFINED CONTROLLER FOR THE VIEW ?
      else if ( $this -> tc_has_default_controller( $id ) ) {
        $controller_cb = $this -> tc_get_default_controller($id);
        //make sure the default controller is well formed
        //the default controller should look like array( instance, method )
        if ( empty($controller_cb) ) {
          do_action( 'tc_dev_notice', 'View : '.$id.'. The default group controller has not been instanciated');
          return;
        }
      }//if
      return $controller_cb;
    }



    //@return boolean
    //@walks the controllers setup array until a match is found
    private function tc_has_default_controller($id) {
      $bool = false;
      foreach ( $this -> controllers as $group => $views_id ) {
        if ( $bool )
          continue;
        if ( in_array($id, $views_id) )
          $bool = true;
      }//foreach
      return $bool;
    }


    //tries to find a default controller group for this method
    //@return array(instance, method) or array() if nothind found
    private function tc_get_default_controller($id) {
      $controller_cb = false;
      foreach ( $this -> controllers as $group => $views_id ) {
        if ( false !== $controller_cb )
          continue;
        if ( in_array($id, $views_id) ) {
          $controller_cb = $id;
          $controller_group = $group;
        }
      }//foreach
      //return here is no match found
      if ( ! $controller_cb ) {
        do_action( 'tc_dev_notice', 'View : '.$id.'. No control method found.');
        return array();
      }


      //Is this group controller instanciated ?
      if ( ! array_key_exists($controller_group, self::$controllers_instances) ) {
        //this will actually load the class file and instanciate it
        $_instance = $this -> tc_instanciate_group_controller($controller_group);
      }

      //stop here if still nothing is instanciated
      if ( ! is_object( $_instance ) ) {
        do_action( 'tc_dev_notice', 'View : '.$id.'. The control class for : ' . $controller_group . ' has not been instanciated.' );
        return array();
      }

      //build the method name
      $method_name = "tc_display_view_{$controller_cb}";//ex : favicon_control



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
      $_file  = sprintf( '%1$sinc/controllers/class-controller-%2$s.php' , TC_BASE, $group );
      $_class = "TC_controller_{$group}";
      $_instance = false;
      if ( file_exists($_file) )
        require_once( $_file );
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

  }//end of class
endif;