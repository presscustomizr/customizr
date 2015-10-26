<?php
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

      //listens to filter 'tc_pre_add_to_collection', takes 1 param : view params
      //makes sure the view has a unique $id and a proper priority for its rendereing hook
      add_filter( 'tc_pre_add_to_collection'    , array( $this, 'tc_pre_process_view_params'), 10, 1 );
      //implement the registered changes before adding view to collection
      add_filter( 'tc_pre_add_to_collection'    , array( $this, 'tc_pre_add_check_registered_changes'), 10, 1 );

      //listens to 'wp' and instanciate the relevant views
      //add_action( 'wp'                        , array( $this, 'tc_maybe_register_children' ), 999 );
      add_action( 'wp'                          , array( $this, 'tc_maybe_instanciate_collection' ), 1000 );
      add_action( 'children_registered'         , array( $this, 'tc_maybe_instanciate_collection') , 20, 1 );

      //listens to a collection pre-update => and fire the tc_apply_registered_changes_to_instance
      // => a change might have been registered
      //view id
      //view params
      add_action( 'pre_render_view'             , array( $this, 'tc_apply_registered_changes_to_instance' ), 10, 1 );

      //listens for a registered change applied to a view => remove it from the register changes list
      //takes one param : view id
      add_action( 'registered_changed_applied'  , array( $this, 'tc_deregister_change' ), 10, 1 );

      //listens to a view changed => update the view collection
      //view_properties_changed takes two params :
      //view id
      //view params
      add_action( 'view_properties_changed'     , array( $this, 'tc_update_collection' ), 10, 2 );

      //reacts when a view has been deregistered from the collection
      //=> fire tc_delete()
      //=> take the view id as param
      add_action( 'view_deregistered'         , array( $this , 'tc_delete'), 10, 1 );
    }




    /**********************************************************************************
    * REGISTERS
    ***********************************************************************************/
    public function tc_register( $view_params = array() ) {
      //the view must be an array of params
      //the hook is the only mandatory param
      //the id is optional => will be set unique on 'tc_pre_add_to_collection'
      if ( ! is_array($view_params) || empty($view_params) || ! isset($view_params['hook']) ) {
        do_action('tc_dev_notice', "A view params are not well formed, it won't be registered. The view must be an array of params. The hook is the only mandatory param." );
        return;
      }

      //pre-process the view params
      //at this stage, the view is at least assigned to a hook
      //=> The filter makes sure :
      //1) the priority is set
      //2) the id is unique
      //=> we'll use the specified id for the view. If no id specified, a unique id will generated based {hook}_{priority}
      //In both cases, its unicity will be ensure by a recursive function (increment +1 until unicity found)
      $view_params = apply_filters( 'tc_pre_add_to_collection', $view_params );

      //don't go further if we still have no id.
      if ( ! $view_params['id'] ) {
        do_action('tc_dev_notice', "A view has no id set." );
        return;
      } else
        $id = $view_params['id'];

      //at this stage the priority is set and the id is unique
      //a view with a unique id can be registered only once
      //a view with a promise registered deletion won't be registered
      if ( $this -> tc_view_exists($id) || $this -> tc_has_registered_deletion( $id ) )
        return;

      //adds the view to the static collection array
      $this -> tc_update_collection( $id, $view_params );

      //emit an event on view registered
      //can be used with did_action() afterwards
      do_action( "view_registered_{$id}" );
    }




    //at this stage, the view has a hook but the id unicity has not been checked yet
    //But, we need to make sure the view has a unique $id and a proper priority for its rendering hook
    //fired on filter 'tc_pre_add_to_collection' in tc_register
    //@return array() $view_params updated
    public function tc_pre_process_view_params( $view_params = array() ) {
      //normalizes the args
      $view_params              = wp_parse_args( $view_params, $this -> tc_get_default_params() );
      //makes sure we assign a unique ascending priority if not set
      $view_params['priority']  = $this -> tc_set_priority( $view_params['hook'], $view_params['priority'] );
      //check or set the name unicity
      $view_params['id']        = $this -> tc_set_unique_id( $view_params['id'], $view_params['hook'], $view_params['priority'] );
      return $view_params;
    }



    //at this stage, the view has a unique id.
    //implement the registered changes before adding view to collection
    //@return the args array()
    //fired on 'tc_pre_add_to_collection'
    //@return modified view params
    public function tc_pre_add_check_registered_changes( $view_params ) {
      $id         = $view_params['id'];
      //IS THERE A REGISTERED REQUEST FOR CHANGE ?
      $to_change  = self::$_change_candidates;
      //=> overwrite the modified args with the new ones
      if ( $this -> tc_has_registered_change( $id ) ) {
        $view_params = wp_parse_args( $to_change[$id], $view_params );
        //This event will trigger a removal of the change from the change list
        //=> tc_deregister_change
        do_action('registered_changed_applied' , $id);
      }
      return $view_params;
    }




    /**********************************************************************************
    * PREPARE VIEWS ON WP => check the controllers, check if it's been changed or deleted ?
    ***********************************************************************************/
    //hook : wp | 1000
    //hook : children_registered with a children collection as param
    public function tc_maybe_instanciate_collection( $collection = array() ) {
      do_action( "pre_instanciate_views" );
      //!! => when hooked on wp, the passed paramater is the wp object
      //=> that's why we must check that it's not an object
      $collection = ( is_object($collection) || empty($collection) ) ? (array)self::$view_collection : $collection;

      //instanciates the base view object
      //add_action( 'wp' , array( new TC_default_view($view_params), 'tc_preprocess_view' ), 0 );
      foreach ( $collection as $id => $view_params ) {
        //check the controller
        if ( ! CZR() -> controllers -> tc_is_possible($id)  )
          continue;

        //instanciate default view
        new TC_default_view($view_params);
      }//foreach
      //emit an event each time a collection is instanciated
      do_action( "collection_instanciated", $collection );
    }//fn




    /**********************************************************************************
    * DE-REGISTER AN INSTANCIATED VIEW
    ***********************************************************************************/
    //keep in mind that the instance of the previous view with initial argument will still exists...
    //so will the additional class instance if any
    //@todo shall we store all views instances and delete them when requested ?
    private function tc_deregister( $id, $view_params ) {
      if ( ! is_array($view_params) )
        return;

      //Removes the previously set action
      if ( ! is_object($view_params['_instance']) )
        do_action('tc_dev_notice', 'Attempt to de-register, but no view instance for id : '. $id );
      else if ( false !== $view_params['hook'] )
        remove_action( $view_params['hook'], array( $view_params['_instance'], 'tc_maybe_render'), $view_params['priority'] );

      //Emit an event on view deregistered
      //=> will trigger the view delete action from collection
      do_action( 'view_deregistered' , $id );
    }





    /**********************************************************************************
    * DELETE FROM COLLECTION
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
        //Emit an event on view deleted
        do_action( 'view_deleted' , $id );
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
    //if the view is registered and already instanciated => de-register it, register it again with the new params and update the promise change array
    //if the view is registered in the collection but not instanciated yet => simply update the collection
    //if the view is not-registered in the collection, register a promise for change
    //@return void()
    //@todo : allow several changes for a view ?
    public function tc_change( $id = null, $new_params = array() ) {
      if ( is_null($id) || ! is_array($new_params) )
        return;

      $current_params = $this -> tc_get_view($id);

      if ( $this -> tc_has_instance( $id ) ) {
        //updates modified view instance properties
        $this -> tc_apply_registered_changes_to_instance( $id, $new_params );
        //deregister previously hooked action and delete from collection
        $this -> tc_deregister( $id, $current_params );
        //register the new version of the view
        $this -> tc_register( $new_params );
      }
      else if ( $this -> tc_view_exists( $id ) )
        $this -> tc_update_collection( $id, $new_params );
      else
        $this -> tc_register_change( $id, $new_params );
      return;
    }



    //at this stage, the view is instanciated
    //@return void()
    private function tc_update_view_instance( $id, $new_params ) {
      //get current params
      $current_params = $this -> tc_get_view($id);
      if ( ! $current_params || ! is_array($current_params) )
        return;
      //pre-process new params
      $new_params = wp_parse_args( $new_params, $current_params );

      //update the modified view properties
      //=> will automatically trigger the collection update
      foreach ($new_params as $property => $value) {
        if ( $value != $current_params[$property] )
          $current_params['_instance'] -> tc_set_property( $property, $value );
      }
    }


    //updates the view properties with the requested args
    //stores the clean and updated args in a view property.
    //@return void()
    //called directly sometimes
    //fired on 'pre_render_view'
    //fired on tc_change if view is instanciated
    public function tc_apply_registered_changes_to_instance( $id, $new_params = array() ) {
      if ( ! $this -> tc_has_registered_change( $id ) )
        return;

      $new_params = empty($new_params) ? tc_get_registered_changes( $id ) : $new_params;

      $this -> tc_update_view_instance( $id, $new_params );

      //This event will trigger a removal of the change from the change list
      //=> tc_deregister_change
      do_action('registered_changed_applied' , $id);
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
    public function tc_deregister_change($id) {
      $to_change = self::$_change_candidates;
      if ( isset($to_change[$id]) )
        unset($to_change[$id]);
      self::$_change_candidates = $to_change;
    }

    //@return registered new view params
    private function tc_get_registered_changes($id) {
      $to_change = self::$_change_candidates;
      return $this -> tc_has_registered_change($id) ? $to_change[$id] : false;
    }

    private function tc_has_registered_change($id) {
      return array_key_exists( $id, self::$_change_candidates );
    }



    /**********************************************************************************
    * UPDATE COLLECION
    ***********************************************************************************/
    //=> always update the view list before rendering something
    //=> a view might have been registered in the delete / change candidates
    //=> this is fired on view_properties_changed event
    //=> when a single view property has been changed in TC_default_view::tc_set_property()
    //=> it can be fired directly, from
    public function tc_update_collection( $id = false, $new_params = array() ) {
      if ( ! $id )
        return;
      //Executes a registered deletion here
      if ( $this -> tc_view_exists( $id ) && $this -> tc_has_registered_deletion( $id ) )
        $this -> tc_delete( $id );

      //Adds or updates a specific view in the collection
      if ( is_array($new_params) && ! empty( $new_params ) ) {
        $view_collection = self::$view_collection;
        $view_collection[$id] = $new_params;
        self::$view_collection = $view_collection;
        //emit an event on each collection updates
        do_action( 'view_collection_updated', $id, $new_params );
      }
    }



    /**********************************************************************************
    * GETTERS / SETTERS
    ***********************************************************************************/
    public function tc_get_controller( $id ) {
      $collection = self::$view_collection;
      if ( $this -> tc_has_controller( $id ) )
        return $collection[$id]['controller'];
      return;
    }


    public function tc_has_controller( $id ) {
      $collection = self::$view_collection;
      return $this -> tc_view_exists( $id ) && false !== $collection[$id]['_instance'];
    }

    //helper to get the default
    //can be used to a get a single default param if specified and exists
    private function tc_get_default_params( $param = null ) {
      $defaults = array(
        'id'          => "",
        '_instance'   => false,//=> we store the current instance here for later access (remove_action), will not be retrieved when using get_object_vars() because this function only get non-static properties
        'hook'        => false,
        'template'    => "",
        'view_class'  => false,
        'query'       => false,
        'priority'    => 10,
        'html'        => "",
        'callback'    => "",
        'cb_params'   => array(),
        'early_setup' => false,
        'children'    => array(),
        'controller'  => ""
      );
      if ( ! is_null($param) )
        return isset($defaults[$param]) ? $defaults[$param] : false;
      return $defaults;
    }


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
      $available = true;
      foreach ( self::$view_collection as $id => $view) {
        if ( $hook != $view['hook'] )
          continue;
        if ( $hook == $view['hook'] && $priority != $view['priority'] )
          continue;
        $available = false;
      }
      return $available ? $priority : $this -> tc_set_priority( $hook, $priority + 1 );
    }


    //Recursively set a unique id when needed
    private function tc_set_unique_id( $id, $hook, $priority, $recursive = false ) {
      //if id not set, then create a unique id from hook_priority
      if ( empty($id) || is_null($id) )
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


    //@return array of child views
    //@return false if has no children
    private function tc_get_children($id) {
      if ( ! $this -> tc_has_children($id) )
        return;

      $view_params = $this -> tc_get_view($id);
      return ! empty( $view_params['children'] ) ? $view_params['children'] : false;
    }

    /**********************************************************************************
    * HELPERS
    ***********************************************************************************/
    private function tc_view_exists( $id ) {
      return array_key_exists( $id, self::$view_collection );
    }


    private function tc_has_instance( $id ) {
      $collection = self::$view_collection;
      return $this -> tc_view_exists( $id ) && false !== $collection[$id]['_instance'];
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





















//this is the default view class
//all views are setup and rendered from this class or a child of this class
//this class (and children) is responsible for
//1) rendering
//2) instanciating its children if any
if ( ! class_exists( 'TC_default_view' ) ) :
  class TC_default_view extends TC_views {
    public $hook = false;//this is the default hook declared in the index.php template
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

    function __construct( $view_params = array() ) {
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $view_params[ $key ] ) ) {
          $this->$key = $view_params[ $key ];
        }
      }

      //ACTIONS ON VIEW READY
      //hooks the view
      add_action( "view_ready_{$this -> id}", array( $this, 'tc_maybe_hook_view'),10, 1 );

      //Registers its children if any
      add_action( "view_ready_{$this -> id}", array( $this, 'tc_maybe_register_children') );

      //Execute various actions before actually hooking the view to front end
      //=> takes 2 params
      //$id
      //$instance
      $this -> tc_setup_view();

      //emit event on view instanciation
      do_action( "view_instanciated", $this -> id );
    }



    /***********************************************************************************
    * ACTIONS ON VIEW INSTANCIATION
    ***********************************************************************************/
    //fired in __construct()
    public function tc_setup_view( $id = null , $instance = null ) {
      if ( empty($this -> id) ) {
        do_action('tc_dev_notice', 'Wrong id for view : '. $this -> id );
        return;
      }

      $instance = $this;
      //instanciates an overriden class for this view if specified ?
      //the overriden class must be a child of TC_default_view
      //must be done now so that the child view class is instanciated with the right properties (args)
      if ( false !== $this -> view_class && class_exists($this -> view_class) ) {
        $view_class = $this -> view_class;
        $new_instance = new $view_class( $this -> tc_get_params() );
        if ( is_subclass_of($new_instance, 'TC_default_view') ) {
          //reset the previous instance with the new one
          $instance = $new_instance;
          //unset the previous default instance
          //unset( $instance -> _instance );
        }
      }

      //add this instance to the view description in the collection
      //=> can be used later for deregistration
      $instance -> tc_set_property( '_instance', $instance );

      do_action( "view_ready_{$this -> id}", $instance );
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
    * ACTIONS ON VIEW READY : REGISTERS CHILD VIEWS
    ***********************************************************************************/
    //hook : view ready
    //=> the collection here can be the full collection or a partial set of views (children for example)
    public function tc_maybe_register_children() {
      if ( ! $this -> tc_has_children() )
        return;

      $children_collection = array();
      foreach ( $this -> children as $id => $view_params ) {
        //re-inject the id into the view_params
        $view_params['id'] = $id;
        $this -> tc_register( $view_params );
        $children_collection[$id] = $view_params;
      }//foreach

      //emit an event if a children collection has been registered
      //=> will fire the instanciation of the children collection with tc_maybe_instanciate_collection
      do_action( 'children_registered', $children_collection );
    }


    /**********************************************************************************
    * RENDERS
    ***********************************************************************************/
    //hook : $view_params['hook']
    //NOTE : the $this here can be the child class $this.
    public function tc_maybe_render() {
      //this event is used to check for late deletion or change before actually rendering
      //will fire tc_apply_registered_changes_to_instance
      do_action( 'pre_render_view', $this -> id );

      if ( ! apply_filters( "tc_do_render_view_{$this -> id}", true ) )
        return;

      do_action( "before_render_view_{$this -> id}" );
        $this -> tc_render();
      do_action( "after_render_view_{$this -> id}" );
    }



    //might be overriden in the child view if any
    public function tc_render() {
      if ( ! empty( $this -> html ) )
        echo $this -> html;

      if ( ! empty( $this -> template ) ) {
        //add the view to the wp_query wp global
        set_query_var( "{$this -> template}_model", $this );
        get_template_part( "inc/views/templates/{$this -> template}" );
      }
      // $path = '';
      // $part = '';
      // get_template_part( $path , $part );

      if ( ! empty( $this -> callback ) )
        CZR() -> helpers -> tc_fire_cb( $this -> callback, $this -> cb_params );
    }



    /***********************************************************************************
    * GETTERS / SETTERS / HELPERS
    ***********************************************************************************/
    //Checks if a registered view has child views
    //@return boolean
    private function tc_has_children() {
      return ! empty($this -> children);
    }


    //normalizes the way we can access and change a single view property
    //=> emit an event to update the collection
    //@return void()
    public function tc_set_property( $property, $value ){
      $this -> $property = $value;
      //add an event 'view_properties_changed'
      //will trigger a collection update
      do_action( 'view_properties_changed', $this -> id, $this -> tc_get_params() );
    }


    //@returns the view description
    private function tc_get_params() {
      $view_params = array();
      foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
        $view_params[ $key ] = $this->$key;
      }
      return $view_params;
    }


    public function tc_get_instance() {
      return $this;
    }

  }
endif;






class TC_test_view_class extends TC_default_view {
  public $test_class_property = 'YOUPI';
  function __construct( $view_params = array() ) {
    $keys = array_keys( get_object_vars( parent::tc_get_instance() ) );
    foreach ( $keys as $key ) {
      if ( isset( $view_params[ $key ] ) ) {
        $this->$key = $view_params[ $key ];
      }
    }
  }

  /*public function tc_render() {
    ?>
      <h1>MY ID IS <span style="color:blue"><?php echo $this -> id ?></span>, AND I AM RENDERED BY THE VIEW CLASS</h1>
    <?php
  }*/
}





class TC_rendering {
  function callback_met( $text1 = "default1", $text2 = "default2"  ) {
    ?>
      <h1>THIS IS RENDERED BY A CALLBACK METHOD IN A CLASS, WITH 2 OPTIONAL PARAMS : <?php echo $text1; ?> and <?php echo $text2; ?></h1>
    <?php
  }
}


//@todo : children it would be good to add actions on pre_render_view, where we are in the parent's hook action.
//=> late check if new children have been registered
//=> if so, instanciate their views there