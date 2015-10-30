<?php
//Models contain the underlying data of each views.

//A model is an object instanciated by the collection class.
//Before instanciation, the collection class has made sure that the model has at least :
//1) a unique id
//2) a hook
//3) a priority set.

//Once alive, a model's job is :
//- instanciate its view. The model must check if its related view has to be instanciated with the default view class or a child of it.
//- assign the view to its rendering hook
//- register its child models if any


if ( ! class_exists( 'TC_Model' ) ) :
  class TC_Model {
    static $instance;

    //the model properties
    //each view will inherit those properties
    public $hook = "";//this is the default hook declared in the index.php template
    public $_instance;
    public $id = "";
    public $view_class = false;
    public $query = false;
    public $priority = 10;
    public $template = "";
    public $html = "";
    public $callback = "";
    public $cb_params = array();
    public $early_setup = false;
    public $children = array();
    public $controller = "";
    public $visibility = true;//can be typically overriden by a check on a user option

    //on instanciation the id is unique and the priority propery setup
    //=> those treatments have been managed by the collection
    function __construct( $model = array() ) {
      self::$instance =& $this;

      //equivalent of wp_parse_args() with default model property values
      $this -> tc_update_model_properties( $model );

      //at this stage the mode must at least have :
      //1) a unique id
      //2) a priority set
      //3) a hook
      if ( ! $this -> tc_can_model_be_instanciated() )
        return;

      //this will trigger the collection update
      do_action( 'model_instanciated' , $this -> id, $this );

      //Registers its children if any
      $this -> tc_maybe_register_children();

      //listens to 'wp' and instanciate the model's view
      add_action( 'wp'                          , array( $this, 'tc_maybe_instanciate_view' ), 1000 );
    }



    /***********************************************************************************
    * ACTIONS ON VIEW READY
    * => THE POSSIBLE VIEW CLASS IS NOW INSTANCIATED
    ***********************************************************************************/
    //hook the rendering method to the hook
    //$this -> _instance can be used. It can be a child of this class.
    public function tc_maybe_hook_view($instance) {
      $_this = $instance;
      if ( empty($_this -> id) ) {
        do_action('tc_dev_notice', 'A view is missing an id' );
        return;
      }

      //Renders the view on the requested hook
      if ( false !== $_this -> hook ) {
        add_action( $_this -> hook, array( $_this -> _instance , 'tc_maybe_render' ), $_this -> priority );
        //emit an event each time a view is hooked
        do_action( 'view_hooked' , $_this -> id );
      }
    }




    /**********************************************************************************
    * INSTANCIATE THE MODEL VIEW => check the controllers,
    * @wp_timezone_override_offset(); check if it's been changed or deleted ?
    ***********************************************************************************/
    //default hook : wp | 1000
    public function tc_maybe_instanciate_view() {
      do_action( "pre_instanciate_view" );

      if ( ! CZR() -> controllers -> tc_is_possible($id)  )
        return;

      //instanciate the view with the right class
      $instance = $this -> tc_instanciate_view_class()
      if ( ! is_object($instance) ) {
        do_action('tc_dev_notice', "Model : " . $this -> id . ". The view has not been instanciated." );
        return;
      }
      //A view must be TC_view or a child class of TC_view.
      if ( is_subclass_of($instance, 'TC_View') ) {
        do_action('tc_dev_notice', "Model : " . $this -> id . ". The view must be a child of TC_View." );
        return;
      }

      //add this instance to the view description in the collection
      //=> can be used later for deregistration
      $instance -> tc_set_property( '_instance', $instance );

      //emit an event each time a collection is instanciated
      do_action( "view_instanciated", $this -> id );
    }//fn




    //this method load the relevant view class file and return the instance
    //@return instance object
    private function tc_instanciate_view_class() {
      if ( false !== $this -> view_class && class_exists($this -> view_class) ) {
        $view_class = $this -> view_class;
        $new_instance = new $view_class( $this -> tc_get() );
        if ( is_subclass_of($new_instance, 'TC_View') ) {
          //reset the previous instance with the new one
          $instance = $new_instance;
          //unset the previous default instance
          //unset( $instance -> _instance );
        }
      }
      return $instance;
    }


    /**********************************************************************************
    * ACTIONS ON MODEL INSTANCIATION : REGISTERS CHILD MODEL
    ***********************************************************************************/
    //hook : view ready
    //=> the collection here can be the full collection or a partial set of views (children for example)
    public function tc_maybe_register_children() {
      if ( ! $this -> tc_has_children() )
        return;

      $children_collection = array();
      foreach ( $this -> children as $id => $model ) {
        //re-inject the id into the view_params
        $model['id'] = $id;
        CZR() -> collection -> tc_register( $model );
        $children_collection[$id] = $model;
      }//foreach

      //emit an event if a children collection has been registered
      //=> will fire the instanciation of the children collection with tc_maybe_instanciate_collection
      do_action( 'children_registered', $children_collection );
    }


    /***********************************************************************************
    * EXPOSED GETTERS / SETTERS
    ***********************************************************************************/
    //Checks if a registered view has child views
    //@return boolean
    public function tc_has_children() {
      return ! empty($this -> children);
    }


    //normalizes the way we can access and change a single model property
    //=> emit an event to update the collection
    //@return void()
    public function tc_set_property( $property, $value ){
      $this -> $property = $value;

      //will trigger a collection update
      //pass : id, (object) model, changed property, new property value
      do_action( 'model_property_changed', $this -> id, $this , $property, $value );
    }


    //@returns the model property as an array of params
    public function tc_get() {
      $model = array();
      foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
        $model[ $key ] = $this->$key;
      }
      return $model;
    }


    public function tc_get_instance() {
      return $this;
    }


    //@return void()
    //update the model properties with a set of new ones
    //is fired on instanciation
    //@param = array()
    public function tc_update_model_properties( $model = array() ) {
      $keys = array_keys( get_object_vars( $this ) );
      foreach ( $keys as $key ) {
        if ( isset( $model[ $key ] ) ) {
          $this->$key = $model[ $key ];
        }
      }
      //emit an event when a model is updated
      do_action( 'model_updated', $this -> id );
    }



    /**********************************************************************************
    * PRIVATE HELPERS
    ***********************************************************************************/
    //@return bool
    private function tc_can_model_be_instanciated() {
      //the model must be an array of params
      //the hook is the only mandatory param
      //the id is optional => will be set unique on model instanciation
      if ( ! is_numeric( $this -> priority ) || empty($this -> id) || empty( $this -> hook ) ) {
        do_action('tc_dev_notice', "In TC_Model class, a model is not ready for the collection, it won't be registered. at this stage, the model must have an id, a hook and a numeric priority." );
        return;
      }
      return true;
    }
  }//end of class
endif;




// /**
//  * Check if any filter has been registered for a hook AND a priority
//  * inspired from the WP original
//  * adds the priority_to_check param
//  */
// //NOT USED!!!!
// private function tc_has_filter( $tag, $function_to_check = false, $priority_to_check = 10 ) {
//   // Don't reset the internal array pointer
//   $wp_filter = $GLOBALS['wp_filter'];
//   $has = ! empty( $wp_filter[ $tag ] );

//   // Make sure at least one priority has a filter callback
//   if ( $has ) {
//     $exists = false;
//     foreach ( $wp_filter[ $tag ] as $callbacks ) {
//       if ( ! empty( $callbacks ) ) {
//         $exists = true;
//         break;
//       }
//     }

//     if ( ! $exists ) {
//       $has = false;
//     }
//   }

//   if ( false === $function_to_check || false === $has )
//     return $has;

//   if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
//     return false;

//   return isset( $wp_filter[$tag][$priority_to_check] ) && isset( $wp_filter[$tag][$priority_to_check][$idx] );
// }