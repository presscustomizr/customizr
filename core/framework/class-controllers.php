<?php
//the controllers are organized by groups in 4 classes
//Header, Content, Footer, Modules.
//The controller group classes are instantiated on demand if any of a view (or its child) requested it
//If the model is used as a method parameter, it shall be an array()
//=> because a controller can be checked and fired before the model has been instantiated
//=> always check the existence of the id
if ( ! class_exists( 'CZR_controllers' ) ) :
  class CZR_controllers {
    static $instance;
    private $controllers;
    static $controllers_instances = array();

    function __construct( $args = array() ) {
          self::$instance =& $this;
          $this -> controllers = array(
            'header' => array(
              'head',
              'title',
              'logo_wrapper',
              'logo',
              'sticky_logo', /*'logo_title', */
              'tagline',
              'mobile_tagline',
              'favicon',
              'menu',
              'sidenav',
              'navbar_menu',
              'navbar_secondary_menu',
              'menu_button',
              'mobile_menu_button',
              'sidenav_menu_button',
              'sidenav_navbar_menu_button',
              'header_social_block'
            ),
            'content' => array(
              'post_list',
              'post_list_masonry',
              'post_list_plain',
              'post_list_plain_excerpt',
              'right_sidebar',
              'left_sidebar',
              'single_author_info',
              'comment_list',
              'comments',
              'posts_navigation',
              'post_list_heading',
              'author_description',
              'posts_list_description',
              'search_heading',
              'post_heading',
              //'404', 'attachment', 'headings', 'no_results', 'page', 'post', 'single_author_info', 'post_list', 'post_metas','right_sidebar', 'left_sidebar', 'posts_list_headings', 'posts_list_description', 'author_description', 'posts_list_title', 'posts_list_search_title', 'singular_article', 'post_list_title', 'post_navigation_singular', 'post_navigation_posts', 'comments', 'comment_list', 'comment', 'tracepingback', 'author_info', 'singular_headings', 'post_list_standard_thumb', 'post_list_rectangular_thumb', 'post_thumbnail'
            ),
            'footer' => array(
              'btt_arrow',
              'footer_btt',
              'footer_push',
              'footer_widgets',
              'colophon',
              'footer_social_block'
          //    'widgets', , 'back_to_top'
            ),
            'modules' => array(
              'social_block',
              'breadcrumb',
              'comment_info',
              'post_list_grid',
              'main_slider',
              'main_posts_slider',
              'featured_pages',
              'edit_button'
              //'social_block', 'breadcrumb', 'comment_info', 'post_list_grid', 'featured_pages', 'main_slider', 'recently_updated', 'edit_button', 'help_block'
            //   'breadcrumb', 'comment_bubbles', 'featured_pages', 'gallery', 'post_list_grid', 'post_thumbnails', 'slider'
            ),
          );

          //Store a group controller instance for later uses
          //takes 2 params
          //group name (string)
          //group controller instance (object)
          add_action( 'group_controller_instantiated', array( $this, 'czr_fn_store_controller_instance'),10, 2);
    }//__construct()




    /***********************************************************************************
    * EXPOSED API
    ***********************************************************************************/
    //1) checks if a controller has been specified for the view. It can be either a function, or the method of a class
    //
    //2) if nothing is specified for the view, then checks if the view controller belongs to a particular group
    //if a match is found, then the group controller class is instantiated if not yet
    //then the existence of the controller method is checked and fired if exists
    //
    //3) if no match is found, the view is not allowed
    //@ $model can be
    //  1) a model object,
    //  2) a model array (not instanciated),
    //  3) a string id of the model
    public function czr_fn_is_possible( $model ) {
          //if the specified param is an id, then get the model from the collection
          if ( is_string($model) )
            $model = array('id' => $model );

          //abort with true if still no model at this stage.
          if ( empty($model) )
            return true;

          //the returned value can be a string or an array( instance, method)
          $controller_cb = $this -> czr_fn_get_controller( $model );
          //FOR TEST ONLY
          //return true;

          if ( ! empty( $controller_cb ) ) {
            return apply_filters( 'czr_fn_set_control' , (bool) czr_fn_return_cb_result( $controller_cb, $model ) );
          }
          return true;
    }



    //@return bool
    //@param array() or object() model
    public function czr_fn_has_controller( $model = array() ) {
          return ! empty( $this -> czr_fn_build_controller( $model ) );
    }



    //@return a function string or an array( instance, method )
    //@param array() or object() model
    private function czr_fn_get_controller( $model = array() ) {
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
            $controller_ids   = array_filter( array(
                $model['id'],
                ! empty( $model['controller'] ) ? $model['controller'] : '',
              )
            );
            if ( $this -> czr_fn_has_default_controller( $controller_ids ) ) {
              $controller_cb = $this -> czr_fn_get_default_controller( $controller_ids );
              //make sure the default controller is well formed
              //the default controller should look like array( instance, method )
              if ( empty($controller_cb) ) {
                do_action( 'czr_dev_notice', 'View : '.$model['id'].'. The default group controller has not been instantiated');
                return "";
              }//if
            }//if has default controller
          }//else
          return $controller_cb;
    }



    //@return boolean
    //@walks the controllers setup array until a match is found
    private function czr_fn_has_default_controller( $controller_ids ) {
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
    private function czr_fn_get_default_controller( $controller_ids ) {
          $controller_cb = false;
          foreach ( $this -> controllers as $group => $views_id ) {
            foreach( $controller_ids as $id ) {
              if ( in_array($id, $views_id) ) {
                $controller_cb = $id;
                $controller_group = $group;
                break 2;
              }//if
            }//foreach
          }//foreach
          //return here is no match found
          if ( ! $controller_cb ) {
            do_action( 'czr_dev_notice', 'View : '.$id.'. No control method found.');
            return array();
          }


          //Is this group controller instantiated ?
          if ( ! array_key_exists($controller_group, self::$controllers_instances) ) {
            //this will actually load the class file and instantiate it
            $_instance = $this -> czr_fn_instantiate_group_controller($controller_group);
          } else {
            $_instance = $this -> czr_fn_get_controller_instance($controller_group);
          }

          //stop here if still nothing is instantiated
          if ( ! isset( $_instance) || ! is_object( $_instance ) ) {
            do_action( 'czr_dev_notice', 'View : '.$id.'. The control class for : ' . $controller_group . ' has not been instantiated.' );
            return array();
          }

          //build the method name
          $method_name = "czr_fn_display_view_{$controller_cb}";//ex : czr_fn_display_view_{favicon_control}()


          //make sure we have a class instance and that the requested controller method exists in it
          if ( method_exists( $_instance , $method_name ) )
            return array( $_instance, $method_name );

          do_action( 'czr_dev_notice', 'model : '.$id.'. The method : ' . $method_name . ' has not been found in group controller : '.$controller_group );
          return array();
    }


    //load the class file if exists
    //instantiate the class is exists
    //@param is a string : header, content, footer, modules
    //@return the $instance
    private function czr_fn_instantiate_group_controller( $group ) {
          $_path     = "controllers/class-controller-{$group}.php";
          $_class    = "CZR_controller_{$group}";
          $_instance = false;
          $CZR       = CZR();

          $CZR -> czr_fn_require_once( CZR_FRAMEWORK_FRONT_PATH . $_path );

          if ( class_exists($_class) ) {
            $_instance = new $_class;
            do_action( 'group_controller_instantiated', $group, $_instance );
          }
          return $_instance;
    }


    //listens to group_controller_instantiated
    //stores the groupd controller instance in the property : self::$controllers_instances
    //@return void()
    public function czr_fn_store_controller_instance( $group , $_instance ) {
          $controller_instances = self::$controllers_instances;
          if ( array_key_exists($group, $controller_instances) )
            return;

          $controller_instances[$group] = $_instance;
          self::$controllers_instances = $controller_instances;
    }


    //get an already instantiated controller group instance
    public function czr_fn_get_controller_instance( $group ) {
          $controller_instances = self::$controllers_instances;
          if ( ! array_key_exists($group, $controller_instances) )
            return;
          return $controller_instances[$group];
    }
  }//end of class
endif;
