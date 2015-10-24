<?php
/**
* The tc__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
*
* By convention in Customizr, filter hooks are used as follow :
* 1) declared with add_filters in class constructors (mainly) to hook on WP built-in callbacks or create "getters" used everywhere
* 2) declared with apply_filters in methods to make the code extensible for developers
* 3) accessed with tc__f() to return values (while front end content is handled with action hooks)
*
* Used everywhere in Customizr. Can pass up to five variables to the filter callback.
*
* @since Customizr 3.0
*/
if( ! function_exists( 'tc__f' ) ) :
    function tc__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;



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
        self::$instance -> views = new TC_Views();
        //self::$instance -> controllers = new TC_Controllers();
      }
      return self::$instance;
    }



    private function tc_setup_constants() {
      /* GETS INFORMATIONS FROM STYLE.CSS */
      // get themedata version wp 3.4+
      if( function_exists( 'wp_get_theme' ) ) {
        //get WP_Theme object of customizr
        $tc_theme                     = wp_get_theme();

        //Get infos from parent theme if using a child theme
        $tc_theme = $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

        $tc_base_data['prefix']       = $tc_base_data['title'] = $tc_theme -> name;
        $tc_base_data['version']      = $tc_theme -> version;
        $tc_base_data['authoruri']    = $tc_theme -> {'Author URI'};
      }

      // get themedata for lower versions (get_stylesheet_directory() points to the current theme root, child or parent)
      else {
           $tc_base_data                = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
           $tc_base_data['prefix']      = $tc_base_data['title'];
      }

      self::$theme_name                 = sanitize_file_name( strtolower($tc_base_data['title']) );

      //CUSTOMIZR_VER is the Version
      if( ! defined( 'CUSTOMIZR_VER' ) )      define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
      //TC_BASE is the root server path of the parent theme
      if( ! defined( 'TC_BASE' ) )            define( 'TC_BASE' , get_template_directory().'/' );
      //TC_BASE_CHILD is the root server path of the child theme
      if( ! defined( 'TC_BASE_CHILD' ) )      define( 'TC_BASE_CHILD' , get_stylesheet_directory().'/' );
      //TC_BASE_URL http url of the loaded parent theme
      if( ! defined( 'TC_BASE_URL' ) )        define( 'TC_BASE_URL' , get_template_directory_uri() . '/' );
      //TC_BASE_URL_CHILD http url of the loaded child theme
      if( ! defined( 'TC_BASE_URL_CHILD' ) )  define( 'TC_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' );
      //THEMENAME contains the Name of the currently loaded theme
      if( ! defined( 'THEMENAME' ) )          define( 'THEMENAME' , $tc_base_data['title'] );
      //TC_WEBSITE is the home website of Customizr
      if( ! defined( 'TC_WEBSITE' ) )         define( 'TC_WEBSITE' , $tc_base_data['authoruri'] );

    }//setup_contants()



    private function tc_load() {
      //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
      $this -> tc_core = apply_filters( 'tc_core',
        array(
            'fire'      =>   array(
              array('inc' , 'init'),//defines default values (layout, socials, default slider...) and theme supports (after_setup_theme)
              array('inc' , 'plugins_compat'),//handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
              array('inc' , 'utils_settings_map'),//customizer setting map
              array('inc' , 'utils'),//helpers used everywhere
              array('inc' , 'resources'),//loads front stylesheets (skins) and javascripts
              array('inc' , 'widgets'),//widget factory
              array('inc' , 'placeholders'),//front end placeholders ajax actions for widgets, menus.... Must be fired if is_admin === true to allow ajax actions.
              array('inc/admin' , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
              array('inc/admin' , 'admin_page')//creates the welcome/help panel including changelog and system config
            ),
            'admin'     => array(
              array('inc/admin' , 'customize'),//loads customizer actions and resources
              array('inc/admin' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
            ),
            'addons'    => apply_filters( 'tc_addons_classes' , array() )
        )//end of array
      );//end of filter

      //check the context
      if ( $this -> tc_is_pro() )
        require_once( sprintf( '%sinc/init-pro.php' , TC_BASE ) );

      self::$tc_option_group = 'tc_theme_options';

      //set files to load according to the context : admin / front / customize
      add_filter( 'tc_get_files_to_load' , array( $this , 'tc_set_files_to_load' ) );

      //theme class groups instanciation
      $this -> tc__();
    }




    /**
    * Class instanciation using a singleton factory :
    * Can be called to instanciate a specific class or group of classes
    * @param  array(). Ex : array ('admin' => array( array( 'inc/admin' , 'meta_boxes') ) )
    * @return  instances array()
    *
    * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
    *
    * @since Customizr 3.0
    */
    function tc__( $_to_load = array(), $_args = array() ) {
      static $instances;
      $_args = wp_parse_args( $_args, array( '_no_filter' => false, '_singleton' => true ) );
      //do we apply a filter ? optional boolean can force no filter
      $_to_load = ( isset($_args['_no_filter']) && $_args['_no_filter'] ) ? $_to_load : apply_filters( 'tc_get_files_to_load' , $_to_load );
      if ( empty($_to_load) )
        return;

      foreach ( $_to_load as $group => $files ) {
        foreach ($files as $path_suffix ) {
          //checks if a child theme is used and if the required file has to be overriden
          if ( $this -> tc_is_child() && file_exists( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ) {
              require_once ( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
          }
          else {
              require_once ( TC_BASE . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
          }
          if ( isset($_args['_instanciate']) && ! $_args['_instanciate'] )
            continue;

          $classname = 'TC_' . $path_suffix[1];
           //clean up the args to remove what we don't need for the class constructors
          $_constructor_args = array_diff_key($_args, array( '_no_filter' => false, '_singleton' => true , '_instanciate' => true) );

          //SINGLETON FACTORY
          if( ! isset( $instances[ $classname ] ) && isset($_args['_singleton']) && $_args['_singleton'] )  {
            //check if the classname can be instanciated here
            if ( in_array( $classname, apply_filters( 'tc_dont_instanciate_in_init', array( 'TC_nav_walker') ) ) )
              continue;
            //instanciates
            $instances[ $classname ] = class_exists($classname)  ? new $classname($_constructor_args) : '';
          }
          else if( ! isset($_args['_singleton']) || ! $_args['_singleton'] ) {
            if ( class_exists($classname) ) {
              //stores the instance id in a property for later use
              $_args['instance_id'] = ( ! isset($instances[ $classname ]) || ! is_array($instances[ $classname ]) ) ? 1 : count($instances[ $classname ]);

              if ( isset($instances[ $classname ]) ) {
                if ( ! is_array($instances[ $classname ]) )
                  $instances[ $classname ] = array($instances[ $classname ]);
                $instances[ $classname ][] = new $classname($_constructor_args);
              }
              else
                $instances[ $classname ] = array( new $classname($_constructor_args) );
            }//if
          }//if
        }//foreach
      }//foreach
      if ( is_array($instances) && isset($classname) )
        return $instances[ $classname ];
    }



    /***************************
    * HELPERS
    ****************************/
    /**
    * Check the context and return the modified array of class files to load and instanciate
    * hook : tc_get_files_to_load
    * @return boolean
    *
    * @since  Customizr 3.3+
    */
    function tc_set_files_to_load( $_to_load ) {
      $_to_load = empty($_to_load) ? $this -> tc_core : $_to_load;
      //Not customizing
      //1) IS NOT CUSTOMIZING : tc_is_customize_left_panel() || tc_is_customize_preview_frame() || tc_doing_customizer_ajax()
      //---1.1) IS ADMIN
      //-------1.1.a) Doing AJAX
      //-------1.1.b) Not Doing AJAX
      //---1.2) IS NOT ADMIN
      //2) IS CUSTOMIZING
      //---2.1) IS LEFT PANEL => customizer controls
      //---2.2) IS RIGHT PANEL => preview
      if ( ! $this -> tc_is_customizing() )
        {
          if ( is_admin() ) {
            //if doing ajax, we must not exclude the placeholders
            //because ajax actions are fired by admin_ajax.php where is_admin===true.
            if ( defined( 'DOING_AJAX' ) )
              $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize' ) );
            else
              $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize', 'fire|inc|placeholders' ) );
          }
          else
            //Skips all admin classes
            $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page') );
        }
      //Customizing
      else
        {
          //left panel => skip all front end classes
          if ( $this -> tc_is_customize_left_panel() ) {
            $_to_load = $this -> tc_unset_core_classes(
              $_to_load,
              array( 'header' , 'content' , 'footer' ),
              array( 'fire|inc|resources' , 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
            );
          }
          if ( $this -> tc_is_customize_preview_frame() ) {
            $_to_load = $this -> tc_unset_core_classes(
              $_to_load,
              array(),
              array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
            );
          }
        }
      return $_to_load;
    }



    /**
    * Helper
    * Alters the original classes tree
    * @param $_groups array() list the group of classes to unset like header, content, admin
    * @param $_files array() list the single file to unset.
    * Specific syntax for single files: ex in fire|inc/admin|admin_page
    * => fire is the group, inc/admin is the path, admin_page is the file suffix.
    * => will unset inc/admin/class-fire-admin_page.php
    *
    * @return array() describing the files to load
    *
    * @since  Customizr 3.0.11
    */
    public function tc_unset_core_classes( $_tree, $_groups = array(), $_files = array() ) {
      if ( empty($_tree) )
        return array();
      if ( ! empty($_groups) ) {
        foreach ( $_groups as $_group_to_remove ) {
          unset($_tree[$_group_to_remove]);
        }
      }
      if ( ! empty($_files) ) {
        foreach ( $_files as $_concat ) {
          //$_concat looks like : fire|inc|resources
          $_exploded = explode( '|', $_concat );
          //each single file entry must be a string like 'admin|inc/admin|customize'
          //=> when exploded by |, the array size must be 3 entries
          if ( count($_exploded) < 3 )
            continue;

          $gname = $_exploded[0];
          $_file_to_remove = $_exploded[2];
          if ( ! isset($_tree[$gname] ) )
            continue;
          foreach ( $_tree[$gname] as $_key => $path_suffix ) {
            if ( false !== strpos($path_suffix[1], $_file_to_remove ) )
              unset($_tree[$gname][$_key]);
          }//end foreach
        }//end foreach
      }//end if
      return $_tree;
    }//end of fn




    /**
    * Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
    * @return boolean
    *
    * @since  Customizr 3.0.11
    */
    function tc_is_child() {
      // get themedata version wp 3.4+
      if ( function_exists( 'wp_get_theme' ) ) {
        //get WP_Theme object of customizr
        $tc_theme       = wp_get_theme();
        //define a boolean if using a child theme
        return $tc_theme -> parent() ? true : false;
      }
      else {
        $tc_theme       = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
        return ! empty($tc_theme['Template']) ? true : false;
      }
    }


    /**
    * Are we in a customization context ? => ||
    * 1) Left panel ?
    * 2) Preview panel ?
    * 3) Ajax action from customizer ?
    * @return  bool
    * @since  3.2.9
    */
    function tc_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      return in_array( 1, array(
        $this -> tc_is_customize_left_panel(),
        $this -> tc_is_customize_preview_frame(),
        $this -> tc_doing_customizer_ajax()
      ) );
    }


    /**
    * Is the customizer left panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function tc_is_customize_left_panel() {
      global $pagenow;
      return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
    }


    /**
    * Is the customizer preview panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function tc_is_customize_preview_frame() {
      return ! is_admin() && isset($_REQUEST['wp_customize']);
    }


    /**
    * Always include wp_customize or customized in the custom ajax action triggered from the customizer
    * => it will be detected here on server side
    * typical example : the donate button
    *
    * @return boolean
    * @since  3.3.2
    */
    function tc_doing_customizer_ajax() {
      $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
      return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }


    /**
    * @return  boolean
    * @since  3.4+
    */
    static function tc_is_pro() {
      return file_exists( sprintf( '%sinc/init-pro.php' , TC_BASE ) ) && "customizr-pro" == self::$theme_name;
    }
  }//end of class
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




















if ( ! class_exists( 'TC_views' ) ) :
  class TC_views {
    static $instance;

    //public $group = "";//header,content,footer,modules

    //private $args = array();//will store the updated args on view creation and use them to instanciate the child
    public static $view_collection = array();//will store all views added to front end
    public static $_delete_candidates = array();//will store deletion of views not added yet
    public static $_change_candidates = array();//will store change of views not added yet

    function __construct( $args = array() ) {
      self::$instance =& $this;

      //Gets the accessible non-static properties of the given object according to scope.
      // $keys = array_keys( get_object_vars( self::$instance ) );

      // foreach ( $keys as $key ) {
      //   if ( isset( $args[ $key ] ) ) {
      //     $this->$key = $args[ $key ];
      //   }
      // }

      //listens to filter 'tc_view_params', takes 2 params
      //view params
      //view id
      add_filter( 'tc_view_params'            , array( $this, 'tc_pre_process_view_params'), 10, 2 );

      //listens to 'wp' and instanciate the relevant views
      add_action( 'wp'                        , array( $this, 'tc_instanciate_contextual_views' ), 0 );

      //listens to a collection pre-update => and fire the tc_update_view_properties
      // => a change might have been registered
      // pre_add_to_collection takes two params :
      //view id
      //view params
      add_action( 'pre_add_to_collection'     , array( $this, 'tc_update_view_properties'), 10, 2 );

      //listens to a collection pre-update => and fire the tc_update_view_properties
      // => a change might have been registered
      //view id
      //view params
      add_action( 'pre_render_view'           , array( $this, 'tc_update_view_properties' ), 10, 2 );

      //listens to a view changed => update the view collection
      //view_properties_changed takes two params :
      //view id
      //view params
      add_action( 'view_properties_changed'   , array( $this, 'tc_update_collection' ), 10, 2 );

      //reacts when a view has been deregistered from the collection
      //=> fire tc_delete()
      //=> take the view id as param
      add_action( 'view_deregistered'         , array( $this , 'tc_delete'), 10, 1 );
    }




    /**********************************************************************************
    * REGISTERS
    ***********************************************************************************/
    public function tc_register( $view_params = array() ) {
      if ( ! is_array($view_params) )
        return;

      //pre-process the view params
      //=> makes sure id is unique and priority is ok
      $view_params = apply_filters( 'tc_view_params', $view_params, $view_params['id'] );

      //adds the view to the static object
      $this -> tc_add_to_collection( $view_params['id'], $view_params );
    }




    /**********************************************************************************
    * PREPARE VIEW ON WP => check the controllers, check if it's been changed or deleted ?
    ***********************************************************************************/
    //hook : wp | 0
    public function tc_instanciate_contextual_views() {
      //check the controller
      // if ( ! CZR() -> controller( $group, $name ) )
      //   return;

      //instanciates the base view object
      //add_action( 'wp' , array( new TC_default_view($view_params), 'tc_preprocess_view' ), 0 );
      $collection = self::$view_collection;
      foreach ( $collection as $id => $view_params ) {
        //instanciate default view
        $instance = new TC_default_view($view_params);

        //add this instance to the view description in the collection
        //=> can be used later for deregistration
        $view_params['_instance'] = $instance;

        //instanciate an additional class for this view if specified ?
        //must be done now so that the child view class is instanciated with the right properties (args)
        $complement_view_instance = false;
        if ( false !== $view_params['view_class'] && class_exists($view_params['view_class']) ) {
          $view_class = $view_params['view_class'];
          $complement_view_instance = new $view_class( $view_params );

          //add the complement view instance to the args
          $view_params['view_class_instance'] = $complement_view_instance;

          //add an event 'view_properties_changed'
          //updates the view properties with the class intance(s) : view_class_instance
          do_action('view_properties_changed', $id, $view_params );
        }


        //if the complement view class is a child of TC_view
        //then use it to override the default
        //=> if the class to instanciate extends TC_views, then the tc_render() method can be overriden by this class
        if ( $complement_view_instance && method_exists($complement_view_instance, 'tc_maybe_render') )
          $_instance_for_action = $complement_view_instance;
        else
          $_instance_for_action = $instance;

        //Renders the view on the requested hook
        if ( false !== $view_params['hook'] )
          add_action( $view_params['hook'], array( $_instance_for_action, 'tc_maybe_render'), $view_params['priority'] );

      }//foreach

    }//fn


    //helper to get the default
    //can be used to a get a single default param if specified and exists
    private function tc_get_default_params( $param = null ) {
      $defaults = array(
        'id'          => "",
        '_instance'   => false,//=> we store the current instance here for later access (remove_action), will not be retrieved when using get_object_vars() because this function only get non-static properties
        'hook'        => false,
        'template'    => "",
        'view_class'  => false,
        'view_class_instance' => false,
        'query'       => false,
        'priority'    => 10,
        'html'        => "",
        'callback'    => "",
        'cb_params'   => array()
      );
      if ( ! is_null($param) )
        return isset($defaults[$param]) ? $defaults[$param] : false;
      return $defaults;
    }


    //add the view description to the private views array
    //at this stage the id is unique
    //the args are well formed
    private function tc_add_to_collection( $id, $view_params ) {
      //implement registered changed
      do_action( 'pre_add_to_collection', $id, $view_params );

      /* if ( is_array($this -> tc_has_registered_deletion( $id )) )
        array_walk_recursive($this -> tc_has_registered_deletion( $id ), function(&$v) { $v = htmlspecialchars($v); }); */
      ?>
        <pre>
          <?php print_r($this -> tc_has_registered_deletion( $id )); ?>
        </pre>
      <?php

      //Deletion requested ?
      //Stop here
      if ( $this -> tc_has_registered_deletion( $id ) )
        return;

      $view_collection = self::$view_collection;
      $view_collection[$id] = $view_params;
      self::$view_collection = $view_collection;
    }



    //makes sure the view has a unique $id and a proper priority for its rendereing hook
    //fired on filter 'tc_view_params' in tc_register
    //@return array() $view_params updated
    public function tc_pre_process_view_params( $view_params, $id ) {
      //normalizes the args
      $view_params = wp_parse_args( $view_params, $this -> tc_get_default_params() );
      //makes sure we assign a unique ascending priority if not set
      $view_params['priority'] = $this -> tc_set_priority( $view_params['hook'], $view_params['priority'] );
      //check the name unicity
      $view_params['id']       = $this -> tc_set_unique_id( $view_params['id'], $view_params['hook'], $view_params['priority'] );
      return $view_params;
    }




    //updates the view properties with the requested args
    //stores the clean and updated args in a view property.
    //@return the args array()
    //called directly sometimes
    //fired on 'pre_add_to_collection'
    public function tc_update_view_properties( $id, $view_params ) {
      //IS THERE A REGISTERED REQUEST FOR CHANGE ?
      $to_change = self::$_change_candidates;
      //=> overwrite the modified args with the new ones
      if ( $this -> tc_has_registered_change( $id ) ) {
        $view_params = wp_parse_args( $to_change[$id], $view_params );
        //remove this change from the list
        $this -> tc_deregister_change($id);
      }

      //Gets the accessible non-static properties of the given object according to scope.
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $view_params[ $key ] ) ) {
          $this->$key = $view_params[ $key ];
        }
      }

      //emit an event, each time property view has been changed
      do_action( 'view_properties_changed', $view_params['id'], $view_params );

      return $view_params;
    }



    /**********************************************************************************
    * DE-REGISTER
    ***********************************************************************************/
    //keep in mind that the instance of the previous view with initial argument will still exists...
    //so will the additional class instance if any
    //@todo shall we store all views instances and delete them when requested ?
    private function tc_deregister( $id, $view_params ) {
      if ( ! is_array($view_params) )
        return;

      //has the previous view an additional class ?
      //If yes, and if it has overriden tc_may_render, then it must be the one we use in remove_action
      if ( false !== $view_params['view_class'] && class_exists($view_params['view_class']) )
        $complement_view_instance = $view_params['view_class_instance'];

      //if the complement view class is a child of TC_view
      //then use this instance to remove the previsouly defined action
      $default_view_instance = method_exists($complement_view_instance, 'tc_maybe_render') ? $complement_view_instance : $view_params['_instance'];


      //Removes the previously set action
      if ( false !== $view_params['hook'] )
        remove_action( $view_params['hook'], array( $default_view_instance, 'tc_maybe_render'), $view_params['priority'] );

      //Deletes the view from collection
      do_action( 'view_deregistered' , $id );
    }



    /**********************************************************************************
    * RENDERS
    ***********************************************************************************/
    //hook : $this -> render_on_hook
    //NOTE : the $this here can be the child class $this.
    public function tc_maybe_render() {
      //this event is used to check for late deletion or change before actually rendering
      do_action( 'pre_render_view', $this -> id, $this -> tc_get_view($this -> id) );

      if ( ! apply_filters( "tc_do_render_view_{$this -> id}", $this -> tc_view_exists($this -> id) ) )
        return;

      $this -> tc_render();

      /*if ( empty($this -> template) && is_user_logged_in() && current_user_can('edit_theme_options') ) :
        ?>
        <div class="row-fluid">
          <div class="span12" style="text-align:center">
            <h1>NO TEMPLATE WAS SPECIFIED</h1>
            <p>This warning is visible for admin users only.</p>
          </div>
        </div>
        <?php
      else :*/
    }



    //might be overriden in the child view if any
    public function tc_render() {
      if ( ! empty( $this -> html ) )
        echo $this -> html;

      if ( ! empty( $this -> template ) )
        get_template_part( "inc/views/templates/{$this -> template}" );
      // $path = '';
      // $part = '';
      // get_template_part( $path , $part );

      if ( ! empty( $this -> callback ) )
        $this -> tc_fire_render_cb( $this -> callback, $this -> cb_params );
    }



    //A callback helper
    //a callback can be function or a method of a class
    private function tc_fire_render_cb( $cb, $params ) {
      //method of a class => look for an array( 'class_name', 'method_name')
      if ( is_array($cb) && 2 == count($cb) && class_exists($cb[0]) ) {
        //instanciated with an instance property holding the object ?
        if ( isset($cb[0]::$instance) && method_exists($cb[0]::$instance, $cb[1]) ) {
          call_user_func_array( array( $cb[0]::$instance ,  $cb[1] ), $params );
        }
        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      } else if ( is_string($cb) && function_exists($cb) ) {
        call_user_func_array($cb, $params);
      }
    }





    /**********************************************************************************
    * DELETE
    ***********************************************************************************/
    //the view might not have been created yet
    //=> register a promise deletion in this case
    //IMPORTANT : always use the TC_views::$instance -> _views property to access the view list here
    //=> because it can be accessed from a child class
    public function tc_delete( $id = null ) {
      if ( is_null($id) )
        return;

      $view_collection = self::$view_collection;
      if ( isset($view_collection[$id]) ) {
        unset($view_collection[$id]);
        self::$view_collection = $view_collection;
        //may be remove from the deletion list
        $this -> tc_deregister_deletion($id);
      }
      else
        $this -> tc_register_deletion( $id );
      return;
    }


    private function tc_deregister_deletion($id) {
      $to_delete = self::$_delete_candidates;
      if ( $this -> tc_has_registered_deletion($id) )
        unset($to_delete[$id]);
      self::$_delete_candidates = $to_delete;
    }


    private function tc_register_deletion($id) {
      $to_delete = self::$_delete_candidates;
      //avoid if already registered
      if ( $this -> tc_has_registered_deletion($id) )
        return;

      $to_delete[$id] = $id;
      self::$_delete_candidates =  $to_delete;
    }


    private function tc_has_registered_deletion($id) {
      return array_key_exists( $id, self::$_delete_candidates );
    }


    /**********************************************************************************
    * CHANGE
    ***********************************************************************************/
    public function tc_change( $id = null, $new_params = array() ) {
      if ( is_null($id) || ! is_array($new_params) )
        return;

      //updates the view collection or registers an update
      $view_collection = self::$view_collection;
      if ( isset($view_collection[$id]) ) {
        //update the view collection
        $current_params = $view_collection[$id];
        $new_params = wp_parse_args( $new_params, $current_params );
        $this -> tc_do_change_view( $id, $current_params, $new_params );
      }
      else
        $this -> tc_register_change( $id, $new_params );
      return;
    }




    private function tc_do_change_view( $id, $current_params, $new_params ) {
      //updates the collection with the new view params
      do_action( 'view_properties_changed', $id, $new_params );

      //deregister previously hooked action and delete from collection
      $this -> tc_deregister( $id, $current_params );

      //register the new version of the view
      $this -> tc_register( $new_params );

      //may be remove from the promise change list
      $this -> tc_deregister_change($id);
    }



    //stores a requested change for a view not yet registered
    //@id = id of the view
    //@args = view params to change
    //@return void()
    private function tc_register_change( $id, $new_params ) {
      $view_collection = self::$view_collection;
      $to_change = self::$_change_candidates;
      //avoid if already registered
      if ( array_key_exists($id, $to_change) )
        return;

      $to_change[$id] = $new_params;
      self::$_change_candidates = $to_change;
    }


    //removes a change in the promise change list.
    //Fired after a changed has been actually done.
    private function tc_deregister_change($id) {
      $to_change = self::$_change_candidates;
      if ( isset($to_change[$id]) )
        unset($to_change[$id]);
      self::$_change_candidates = $to_change;
    }


    private function tc_has_registered_change($id) {
      return array_key_exists( $id, self::$_change_candidates );
    }



    /**********************************************************************************
    * UPDATE COLLECION
    ***********************************************************************************/
    //=> always update the view list before rendering something
    //=> a view might have been registered in the delete / change candidates
    //=> this is fired on view_properties_changed event => when a single view has been changed in tc_update_view_properties
    public function tc_update_collection( $id = false, $new_params = array() ) {
      if ( ! $id )
        return;
      //DELETE CHECK
      $to_delete = self::$_delete_candidates;

      if ( array_key_exists( $id, $to_delete ) )
        $this -> tc_delete( $id );

      //when fired on view_properties_changed
      if ( is_array($new_params) && ! empty( $new_params ) ) {
        $view_collection = self::$view_collection;
        $view_collection[$id] = $new_params;
        self::$view_collection = $view_collection;
      }
    }



    /**********************************************************************************
    * GETTERS / SETTERS
    ***********************************************************************************/
    //@return a single view set of params
    public function tc_get_view( $id = null ) {
      $view_collection = self::$view_collection;
      if ( ! is_null($id) && isset($view_collection[$id]) )
        return $view_collection[$id];
      return;
    }


    //@return all views descriptions
    public function tc_get_collection() {
      //uses self::$instance instead of this to always use the parent instance
      return self::$view_collection;
    }


    //this function recursively :
    //1) checks if the requested priority is available on the specified hook
    //2) set a new priority until until it's available
    private function tc_set_priority( $hook, $priority ) {
      $view_collection = self::$view_collection;
      $available = true;
      foreach ($view_collection as $id => $view) {
        if ( $hook == $view['hook'] && $priority != $view['priority'] )
          continue;
        $available = false;
      }
      return $available ? $priority : $this -> tc_set_priority( $hook, $priority + 1 );
    }


    //Recursively set a unique id when needed
    private function tc_set_unique_id( $id, $hook, $priority, $recursive = false ) {
      //if id not set, then create a unique id from hook_priority
      if ( empty($id) )
        $id = "{$hook}_{$priority}";

      if ( $recursive ) {
        //add hyphen add the end if not there
        $id                 = ! is_numeric(substr($id, -1)) ? $id . '_0' : $id;
        $id_exploded        = explode('_' , $id);
        $_index               = end($id_exploded);
        $_key                 = key($id_exploded);
        //set new value
        $id_exploded[$_key] = $_index + 1;
        $id                 = implode( "_" , $id_exploded );
      }

      $view_collection = self::$view_collection;
      return isset($view_collection[$id]) ? $this -> tc_set_unique_id( $id, $hook, $priority, true ) : $id;
    }



    /**********************************************************************************
    * HELPERS
    ***********************************************************************************/
    private function tc_view_exists( $id ) {
      return array_key_exists( $id, self::$view_collection );
    }


    /**
     * Check if any filter has been registered for a hook AND a priority
     * inspired from the WP original
     * adds the priority_to_check param
     */
    //NOT USED!!!!
    private function tc_has_filter( $tag, $function_to_check = false, $priority_to_check = 10 ) {
      // Don't reset the internal array pointer
      $wp_filter = $GLOBALS['wp_filter'];
      $has = ! empty( $wp_filter[ $tag ] );

      // Make sure at least one priority has a filter callback
      if ( $has ) {
        $exists = false;
        foreach ( $wp_filter[ $tag ] as $callbacks ) {
          if ( ! empty( $callbacks ) ) {
            $exists = true;
            break;
          }
        }

        if ( ! $exists ) {
          $has = false;
        }
      }

      if ( false === $function_to_check || false === $has )
        return $has;

      if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
        return false;

      return isset( $wp_filter[$tag][$priority_to_check] ) && isset( $wp_filter[$tag][$priority_to_check][$idx] );
    }

  }//end of class
endif;




if ( ! class_exists( 'TC_default_view' ) ) :
  class TC_default_view extends TC_views {
    public $hook = false;//this is the default hook declared in the index.php template
    public $_instance;
    public $id = "";
    public $view_class = false;
    public $view_class_instance = false;//if an additional view class has been registered to the view, its instance will be stored there
    public $query = false;
    public $priority = 10;
    public $template = "";
    public $html = "";
    public $callback = "";
    public $cb_params = array();

    function __construct( $view_params = array() ) {
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $view_params[ $key ] ) ) {
          $this->$key = $view_params[ $key ];
        }
      }
    }

    public function tc_get_instance() {
      return $this;
    }
  }
endif;








class TC_test_view_class extends TC_default_view {
  function __construct( $view_params = array() ) {
    $keys = array_keys( get_object_vars( parent::tc_get_instance() ) );
    foreach ( $keys as $key ) {
      if ( isset( $view_params[ $key ] ) ) {
        $this->$key = $view_params[ $key ];
      }
    }
  }

  public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }
}





class TC_rendering {
  function callback_met( $text1 = "default1", $text2 = "default2"  ) {
    ?>
      <h1>THIS IS RENDERED BY A CALLBACK METHOD IN A CLASS, WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
    <?php
  }
}







/*if ( ! class_exists( 'TC_Controllers' ) ) :
  class TC_Controllers {
    static $instance;

  }
endif;//class_exists*/





















// Fire Customizr
//CZR();


CZR() -> views -> tc_delete( 'joie');

//CZR() -> views -> tc_delete( 'joie');
//CZR() -> views -> tc_change( 'joie', array('template' => '', 'html' => '<h1>Yo Man this is a changed view</h1>', 'view_class' => '') );


//Create a new test view
CZR() -> views -> tc_register(
  array( 'hook' => '__after_header', 'template' => 'custom', 'view_class' => 'TC_test_view_class', 'html' => '<h1>Yo Man this is some html to render</h1>' )
);
CZR() -> views -> tc_register(
  array( 'hook' => '__after_header', 'id' => 'joie', 'template' => 'custom', 'view_class' => 'TC_test_view_class' )
);

CZR() -> views -> tc_register(
  array( 'hook' => '__after_header', 'html' => '<h1>Yo Man this is some html to render</h1>' )
);
CZR() -> views -> tc_register(
  array( 'hook' => '__after_header', 'callback' => array( 'TC_rendering', 'callback_met') )
);
CZR() -> views -> tc_register(
  array( 'hook' => '__after_header', 'callback' => 'callback_fn', 'cb_params' => array('custom1', 'custom2') )
);





function callback_fn( $text1 = "default1", $text2 = "default2"  ) {
  ?>
    <h1>THIS IS RENDERED BY A CALLBACK FUNCTION WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
  <?php
}

add_action('__after_header' , function() {
  ?>
    <pre>
      <?php print_r( CZR() -> views -> tc_get_collection() ); ?>
    </pre>
  <?php
}, 100);

//@todo register deletion is not working

//@todo => better handling of the static view properties stored in TC___.
//=> can we move collection helper into TC___?
//=> would it be interesting to use some getter / setter from TC___

//@todo => implement the controller in tc_preprocess_view
//@todo => add an action on wp right after tc_preprocess_view, to apply the registered changes if any
//@todo => review the change and deletion implementation following the addition of tc_preprocess_view
