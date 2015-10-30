<?php
//This is the class managing the collection of models. Here's what it does :
//- registers and de-registers models
//- ensures that each model has a unique id and is well formed
//- Instanciates the relevant models according to their controllers (for model registered on or after 'wp')
//- Handles the model's modifications, including deletion
//- Make sure the collection is a public array of model's instance
if ( ! class_exists( 'TC_Collection' ) ) :
  class TC_Collection {
    static $instance;
    //public $group = "";//header,content,footer,modules
    //private $args = array();//will store the updated args on model creation and use them to instanciate the child
    public static $collection = array();//will store all registered models
    public static $_delete_candidates = array();//will store deletion of models not added yet
    public static $_change_candidates = array();//will store change of models not added yet

    function __construct( $args = array() ) {
      self::$instance =& $this;

      //the first check on the raw model provided
      add_filter( 'tc_can_use_model'            , array( $this, 'tc_is_model_eligible'), 10, 1 );

      //listens to filter 'tc_prepare_model', takes 1 param : raw model array()
      //makes sure the model has a unique $id set and a proper priority for its rendereing hook
      add_filter( 'tc_prepare_model'            , array( $this, 'tc_prepare_model'), 10, 1 );

      //Once the model is eligible and properly prepared (unique id), let's see if we can
      //1) register it,
      //2) pre-register it,
      //3) or simply abort registration
      add_filter( 'tc_can_register_model'       , array( $this, 'tc_check_can_register_model'), 10, 1 );

      //listens to the model setup event
      //=> implement any previously registered changes to this model
      add_action( 'model_setup'                 , array( $this, 'tc_pre_add_check_registered_changes'), 10, 1 );

      //listens for a registered change applied to a model => remove it from the register changes list
      //takes one param : model id
      add_action( 'registered_changed_applied'  , array( $this, 'tc_deregister_change' ), 10, 1 );

      //model_setup is emitted each time a model object has been properly instanciated and setup
      //=> update the collection. 2 params
      //1) model id
      //2) model instance
      add_action( 'model_instanciated'          , array( $this, 'tc_update_collection' ), 10, 2 );

      //listens to a model changed => update the model collection
      //model_property_changed takes two params :
      //model id
      //model params
      add_action( 'model_property_changed'      , array( $this, 'tc_update_collection' ), 10, 2 );

      //reacts when a model has been deregistered from the collection
      //=> fire tc_delete()
      //=> take the model id as param
      add_action( 'model_deregistered'          , array( $this , 'tc_delete'), 10, 1 );
    }




    /**********************************************************************************
    * REGISTER A MODEL TO THE COLLECTION
    ***********************************************************************************/
    public function tc_register( $model = array() ) {
      if ( ! apply_filters( 'tc_can_use_model' , $model ) )
        return;
      //this pre-setup will ensure :
      //- the hook is there
      //- the id unicity
      //- the hook priority setup
      $model = apply_filters( 'tc_prepare_model' , $model );

      //make sure the provided model has at least a hook
      //the model must be an array of params
      //the hook is the only mandatory param
      //the id is optional => will be set unique on model setup
      if ( ! apply_filters( 'tc_can_register_model' , $model ) )
        return;

      //if the model registration occurs after 'wp', then we can early check the model's controller here
      if ( ! $this -> tc_is_model_authorized( $model ) )
        return;

      //Instanciates the model object
      //after this stage, the model is at least assigned to a hook
      //=> On model instanciatiation, make sure :
      //1) the priority is set
      //2) the id is unique
      //=> we'll use the specified id for the model. If no id specified, a unique id will generated based on the template name if set, or the {hook}_{priority}
      //In any cases, the id unicity will be ensure by a recursive function (increment +1 until unicity found)
      //3) a hook has been specified
      //=> At the end of the model setup, a model_setup event is emitted
      $model = new TC_Model( $model );

      if ( $this -> tc_model_exists( $model -> id ) ) {
        //emit an event on model registered
        //can be used with did_action() afterwards
        do_action( "model_registered_{$id}" );
      }
    }


    /**********************************************************************************
    * BEFORE REGISTRATION
    ***********************************************************************************/
    //hook : 'tc_can_use_model'
    //the model must be an array of params
    //the hook is the only mandatory param
    //the id is optional => will be set unique on model instanciation
    public function tc_is_model_eligible( $model = array() ) {
      if ( ! is_array($model) || empty($model) || ! isset($model['hook']) ) {
        do_action('tc_dev_notice', "A model is not ready for the collection, it won't be registered. The model must be an array of params. The hook is the only mandatory param." );
        return;
      }
    }



    //at this stage, the model has a hook but the id unicity, and the priority have not been checked yet
    //=> we need to make sure the model has a unique $id and a proper priority for its rendering hook
    //fired on filter 'tc_prepare_model' in tc_register
    //@return model array() updated
    public function tc_prepare_model( $model = array() ) {
      //makes sure we assign a unique ascending priority if not set
      $model['priority']  = $this -> tc_set_priority( $model['id'] , $$model['priority'] );
      //check or set the name unicity
      $model['id']        = $this -> tc_set_unique_id( $model['id'] , $model['hook'], $model['priority'] );

      //don't go further if we still have no id set
      if ( ! $model['id'] ) {
        do_action('tc_dev_notice', "A model has no id set." );
      }

      //at this stage the priority is set and the id is unique
      //a model with a unique id can be registered only once
      //a model with a promise registered deletion won't be registered
      if ( $this -> tc_model_exists( $model['id'] ) || $this -> tc_has_registered_deletion( $this -> id ) )
        return;
      else {
        //emit an event when a model has been setup
        //this event will trigger the addition of the model to the collection
        do_action( 'model_setup', $this -> id, $this );
      }
    }



    //hook : tc_can_register_model
    //at this point, the raw model has had a first setup to ensure id
    //@return boolean
    //@param array() raw model
    public function tc_check_can_register_model( $model = array() ) {
      //the first check is on the visibility
      //Typically : Has the user allowed this model's view in options ?
      if ( isset( $model['visibility']) && ! (bool) $model['visibility'] )
        return;

      //if the model has early hooks (before wp) , typically a pre_get_post action for example
      // => the the model has to be instanciated
      if ( isset($model['early_setup']) && ! empty($model['early_setup']) )
        return true;

      //if 'wp' has not been fired yet, we will pre-register this model for later registration
      if ( ! did_action('wp') ) {
        //we will use this event to fire the pre-registration
        do_action( 'pre_register_model', $model );
      }
      //if 'wp' has been fired (or is currently being fired) 1) check the controller if set
      else {
        return CZR() -> controllers -> tc_is_possible( $model['id'] )
      }
      return;
    }


    function tc_is_model_authorized($model) {

    }



    /**********************************************************************************
    * UPDATE COLLECION
    ***********************************************************************************/
    //The job of this method is :
    //1) to add a model to the collection
    //2) or to update an existing model
    //
    //=> always update the model list before rendering something
    //=> a model might have been registered in the delete / change candidates
    //=> this is fired on model_property_changed event
    //=> when a single model property has been changed in TC_Model::tc_set_property()
    public function tc_update_collection( $id = false, $model ) {
      if ( ! $id || ! is_object($model) )
        return;
      //Executes a registered deletion here
      if ( $this -> tc_model_exists( $id ) && $this -> tc_has_registered_deletion( $id ) )
        $this -> tc_delete( $id );

      //Adds or updates a specific model in the collection
      if ( is_array($new_params) && ! empty( $new_params ) ) {
        $collection = self::$collection;
        $collection[$id] = $new_params;
        self::$collection = $collection;
        //emit an event on each collection updates
        do_action( 'collection_updated', $id, $new_params );
      }
    }




    /**********************************************************************************
    * DE-REGISTER AN INSTANCIATED MODEL
    ***********************************************************************************/
    //keep in mind that the instance of the previous model with initial argument will still exists...
    //so will the additional class instance if any
    //@todo shall we store all models instances and delete them when requested ?
    private function tc_deregister( $id, $model ) {
      if ( ! is_array($model) )
        return;

      //Removes the previously set action
      if ( ! is_object($model['_instance']) )
        do_action('tc_dev_notice', 'Attempt to de-register, but no model instance for id : '. $id );
      else if ( ! empty( $model['hook'] ) )
        remove_action( $model['hook'], array( $model['_instance'], 'tc_maybe_render'), $model['priority'] );

      //Emit an event on model deregistered
      //=> will trigger the model delete action from collection
      do_action( 'model_deregistered' , $id );
    }





    /**********************************************************************************
    * DELETE A MODEL FROM THE COLLECTION
    ***********************************************************************************/
    //the model might not have been created yet
    //=> register a promise deletion in this case
    //IMPORTANT : always use the TC_Collection::$instance -> _models property to access the model list here
    //=> because it can be accessed from a child class
    public function tc_delete( $id = null ) {
      if ( is_null($id) )
        return;

      $collection = self::$collection;
      if ( isset($collection[$id]) ) {
        unset($collection[$id]);
        self::$collection = $collection;
        //may be remove from the deletion list
        $this -> tc_deregister_deletion($id);
        //Emit an event on model deleted
        do_action( 'model_deleted' , $id );
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
    * CHANGE A REGISTERED MODEL
    ***********************************************************************************/
    //if the model is registered and already instanciated => de-register it, register it again with the new params and update the promise change array
    //if the model is registered in the collection but not instanciated yet => simply update the collection
    //if the model is not-registered in the collection, register a promise for change
    //@return void()
    //@todo : allow several changes for a model ?
    public function tc_change( $id = null, $new_params = array() ) {
      if ( is_null($id) || ! is_array($new_params) )
        return;

      $current_params = $this -> tc_get_model($id);

      if ( $this -> tc_has_instance( $id ) ) {
        //updates modified model instance properties
        $this -> tc_apply_registered_changes_to_instance( $id, $new_params );
        //deregister previously hooked action and delete from collection
        $this -> tc_deregister( $id, $current_params );
        //register the new version of the model
        $this -> tc_register( $new_params );
      }
      else if ( $this -> tc_model_exists( $id ) )
        $this -> tc_update_collection( $id, $new_params );
      else
        $this -> tc_register_change( $id, $new_params );
      return;
    }


    //stores a requested change for a model not yet registered
    //@id = id of the model
    //@args = model params to change
    //@return void()
    private function tc_register_change( $id, $new_params ) {
      $collection = self::$collection;
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

    //@return registered updated model given its id
    public function tc_get_registered_changes($id) {
      $to_change = self::$_change_candidates;
      return $this -> tc_has_registered_change($id) ? $to_change[$id] : array();
    }

    public function tc_has_registered_change($id) {
      return array_key_exists( $id, self::$_change_candidates );
    }




    /**********************************************************************************
    * GETTERS / SETTERS
    ***********************************************************************************/
    //@return a single model set of params
    public function tc_get_model( $id = null ) {
      $collection = self::$collection;
      if ( ! is_null($id) && isset($collection[$id]) )
        return $collection[$id];
      return;
    }


    //@return the collection of models
    public function tc_get() {
      //uses self::$instance instead of this to always use the parent instance
      return self::$collection;
    }


    //this function recursively :
    //1) checks if the requested priority is available on the specified hook
    //2) set a new priority until until it's available
    private function tc_set_priority( $hook, $priority ) {
      $available = true;
      foreach ( self::$collection as $id => $model) {
        if ( $hook != $model['hook'] )
          continue;
        if ( $hook == $model['hook'] && $priority != $model['priority'] )
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

      $collection = self::$collection;
      return isset($collection[$id]) ? $this -> tc_set_unique_id( $id, $hook, $priority, true ) : $id;
    }


    //@return array of child models
    //@return false if has no children
    private function tc_get_children($id) {
      if ( ! $this -> tc_has_children($id) )
        return;

      $model = $this -> tc_get_model($id);
      return ! empty( $model['children'] ) ? $model['children'] : false;
    }



    /**********************************************************************************
    * HELPERS
    ***********************************************************************************/
    //this function recursively :
    //1) checks if the requested priority is available on the specified hook
    //2) set a new priority until until it's available
    private function tc_set_priority( $hook, $priority ) {
      $available = true;
      foreach ( CZR() -> collection -> tc_get() as $id => $model) {
        if ( $hook != $model['hook'] )
          continue;
        if ( $hook == $model['hook'] && $priority != $model['priority'] )
          continue;
        $available = false;
      }
      return $available ? $priority : $this -> tc_set_priority( $hook, $priority + 1 );
    }


    //Recursively create a unique id when needed
    //@return string id
    private function tc_set_unique_id( $id, $hook, $priority ) {
      //add an event here
      $id = apply_filters('tc_set_model_unique_id' , $id, $hook, $priority );

      //if id not set, then create a unique id from hook_priority
      if ( empty($id) || is_null($id) )
        $id = "{$hook}_{$priority}";

      if ( ! CZR() -> collection -> tc_model_exists($id) )
        return $id;

      //add hyphen add the end if not there
      $id                 = ! is_numeric(substr($id, -1)) ? $id . '_0' : $id;
      $id_exploded        = explode('_' , $id);
      $_index             = end($id_exploded);
      $_key               = key($id_exploded);
      //set new value
      $id_exploded[$_key] = $_index + 1;
      $id                 = implode( "_" , $id_exploded );

      //recursive check
      return $this -> tc_set_unique_id( $id, $hook, $priority );
    }



    //checks if a model exists in the collection
    //@param string id
    //@return bool
    public function tc_model_exists( $id ) {
      return array_key_exists( $id, self::$collection );
    }


    //checks if the model exists and is an instance
    //@return bool
    private function tc_has_instance( $id ) {
      $collection = self::$collection;
      return $this -> tc_model_exists( $id ) && is_object( $collection[$id] );
    }
  }//end of class
endif;