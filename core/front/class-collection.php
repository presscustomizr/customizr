<?php
//This is the class managing the collection of models. Here's what it does :
//- registers and de-registers models
//- ensures that each model has a unique id and is well formed
//- Instantiates the relevant models according to their controllers (for models registered on or after 'wp')
//- Instantiates the specified model extended class if any
//- Handles the model's modifications, including deletion
//- Make sure the collection is a public array of model's instance
if ( ! class_exists( 'TC_Collection' ) ) :
  class TC_Collection {
    static $instance;
    //public $group = "";//header,content,footer,modules
    //private $args = array();//will store the updated args on model creation and use them to instantiate the child
    public static $pre_registered = array();//will store the models before they are actually checked, instantiated and registered => before 'wp'
    public static $collection = array();//will store all registered model instances
    public static $_delete_candidates = array();//will store deletion of models not added yet
    public static $_change_candidates = array();//will store change of models not added yet

    function __construct( $args = array() ) {
      self::$instance =& $this;
      //listens to filter 'tc_prepare_model', takes 1 param : raw model array()
      //makes sure the model has a unique $id set and a proper priority for its rendereing hook
      //model as param
      add_filter( 'tc_prepare_model'            , array( $this, 'tc_set_model_base_properties'), 10, 1 );

      //May be apply registered changes
      //model as param
      add_filter( 'tc_prepare_model'            , array( $this, 'tc_apply_registered_changes'), 20, 1 );


      //if 'wp' has not been fired yet, we will pre-register this model for later instantiation
      //2 params :
      //1) model id
      //2) model instance
      add_action ('pre_register_model'          , array( $this, 'tc_pre_register_model'), 10, 2 );

      //a model_alive event is emitted each time a model object has been properly instantiated and setup
      //=> update the collection by registering the model
      //Takes 2 params
      //1) model id
      //2) model instance
      add_action( 'model_alive'                 , array( $this, 'tc_update_collection' ), 10, 2 );

      //on 'wp', the pre_registered (if any) are registered
      add_action( 'wp'                          , array($this, 'tc_register_pre_registered') );

      //Reacts on 'tc_delete' event
      //1 param = model id
      add_action( 'tc_delete'                   , array( $this, 'tc_delete' ), 10, 1 );

      //listens to a model changed => update the model collection
      //model_property_changed takes two params :
      //model id
      //model params
      add_action( 'model_property_changed'      , array( $this, 'tc_update_collection' ), 10, 2 );

      //listens to a registered change applied to a model => remove it from the register changes list
      //takes one param : model id
      add_action( 'registered_changed_applied'  , array( $this, 'tc_deregister_change' ), 10, 1 );

      //reacts when a model has been deregistered from the collection
      //=> fire tc_delete()
      //=> take the model id as param
      add_action( 'model_deregistered'          , array( $this , 'tc_delete'), 10, 1 );
    }




    /**********************************************************************************
    * REGISTER A MODEL TO THE COLLECTION
    ***********************************************************************************/
    public function tc_register( $model = array() ) {
      $_model_params_array = $model;

      //the first check on the raw model provided
      //=> if the model has been pre-registered and has an id, we also have to check here if it's registered for deletion
      if ( ! $this -> tc_is_model_eligible( $model ) )
        return;

      //this pre-setup will ensure :
      //- the hook is there
      //- the id unicity
      //- the hook priority setup
      //It also makes sure that the registered changes will be applied
      $model = apply_filters( 'tc_prepare_model' , $model );

      //make sure the provided model has at least a hook property set
      //the model must be an array of params
      //the hook is the only mandatory param
      //the id is optional => will be set unique on model setup
      //if 'wp' has not been fired yet, we will pre-register this model for later registration
      ////Once the model is eligible and properly prepared (unique id), let's see if we can
      //1) register it,
      //2) pre-register it,
      //3) or simply abort registration
      if ( ! $this -> tc_can_register_model( $model ) )
        return;


      //Instantiates the model object : at this stage the 'wp' hook has been fired and we're ready to instantiate the (maybe pre-registered) model
      //at this stage, the model is an array and :
      //- has an id
      //- has a priority
      //- is at least assigned to a hook
      //- we've checked if it was registered for deletion
      //=> let's instantiate
      $_model_id = isset( $model['id'] ) ? $model['id'] : 'undefined';

      $model = $this -> tc_instantiate_model($model);

      //abort if the model has not been instantiated
      if ( ! is_object($model) ) {
        $_model_id = isset( $model['id'] ) ? $model['id'] : 'undefined';
        do_action('tc_dev_notice', "The model ( " . $_model_id . ") was not instantiated and could not be registered into the collection." );
        return;
      }

      //REGISTER THE MODEL INTO THE COLLECTION
      //At this stage, the model is an instanciated object
      //=> an event is emitted : this will trigger the collection update => the model will be registered in the collection
      do_action( 'model_alive' , $_model_id, $model );

      //specific event for this model.
      do_action( "{$_model_id}_model_alive", $_model_id, $model );


      if ( $this -> tc_is_registered( $model -> id ) ) {
        //emit an event on model registered
        //can be used with did_action() afterwards
        do_action( "model_registered_{$model -> id}" );
      } elseif ( ! empty( $model -> id ) ) {
        do_action('tc_dev_notice', "A model instance ( " . $model -> id . ") was not registered into the collection." );
        return;
      } else //silent exit ( for those models whose instatiation has been stopped in the constructor as result of the model business logic )
        return;
      return $model -> id;
    }







    /**********************************************************************************
    * BEFORE REGISTRATION
    ***********************************************************************************/
    //hook : 'tc_can_use_model'
    //Check if the model is registered for deletion first
    //the model must be an array of params
    //the hook is the only mandatory param => not anymore since tc_render_template
    //the id is optional => will be set unique on model instantiation
    public function tc_is_model_eligible( $model = array() ) {
      //is model registered for deletion ?
      if ( isset( $model['id'] ) && $this -> tc_has_registered_deletion( $model['id'] ) )
        return;

      if ( ! is_array($model) || empty($model) ) {
        do_action('tc_dev_notice', "TC_collection : A model is not eligible for the collection, it won't be registered. The model must be an array of params." );
        return;
      }
      return true;
    }



    //at this stage, the model has a hook but the id unicity, and the priority have not been checked yet
    //=> we need to make sure the model has a unique $id and a proper priority for its rendering hook
    //hook filter 'tc_prepare_model' in tc_register
    //@param model array
    //@return model array() updated
    public function tc_set_model_base_properties( $model = array() ) {
      $id       = isset($model['id']) ? $model['id'] : "";
      $priority = isset($model['priority']) ? $model['priority'] : "";
      $template = isset($model['template']) ? $model['template']  : "";//the template name can be used to define the id

      if ( isset($model['hook']) && ! empty($model['hook']) && false != $model['hook'] ) {
        //makes sure we assign a unique ascending priority if not set
        $model['priority']  = $this -> tc_set_priority( $model['hook'] , $priority );
      } else {
        $model['priority'] = 10;
        $model['hook'] = "";
      }
      //check or set the name unicity
      $model['id']        = $this -> tc_set_unique_id( $id , $model['hook'], $model['priority'], $template );

      //don't go further if we still have no id set
      if ( ! $model['id'] ) {
        do_action('tc_dev_notice', "A model has no id set." );
        return;
      }

      //at this stage the priority is set and the id is unique
      //a model with a unique id can be registered only once
      //a model with a promise registered deletion won't be registered
      if ( $this -> tc_is_registered( $model['id'] ) ) {
        do_action('tc_dev_notice', "TC_Collection. Model : ". $model['id'] ." . The id is still not unique. Not registered." );
        return;
      }
      return $model;
    }


    //at this point, the raw model has had a first setup to ensure id
    //@return boolean
    //@param array() raw model
    private function tc_can_register_model( $model = array() ) {
      $bool = false;
      //the first check is on the visibility
      //Typically : Has the user allowed this model's view in options ?
      if ( isset( $model['visibility']) && ! (bool) $model['visibility'] )
        $bool = false;

      //if the model has early hooks (before wp) , typically a pre_get_post action for example
      // => the the model has to be instantiated
      if ( isset($model['early_setup']) && ! empty($model['early_setup']) )
        $bool = true;

      //if 'wp' has not been fired yet, we will pre-register this model for later registration
      if ( ! did_action('wp') ) {
        //we will use this event to fire the pre-registration
        do_action( 'pre_register_model', $model['id'], $model );
        $bool = false;
      }
      //if 'wp' has been fired (or is currently being fired) 1) check the controller if set
      else {
        $bool = CZR() -> controllers -> tc_is_possible( $model );
      }
      return apply_filters('tc_can_register_model', $bool, $model );
    }



    /**********************************************************************************
    * PRE-REGISTRATION
    ***********************************************************************************/
    //update the pre_register static property
    //hook : pre_register_model
    //@return void()
    //@param id string
    //@param model array
    function tc_pre_register_model( $id, $model = array() ) {
      $pre_registered = self::$pre_registered;
      //is this model already pre_registered ?
      //=> if yes, it can't be registered again. However it should be accessible with a change action.
      if ( isset($pre_registered[$id]) ) {
        do_action('tc_dev_notice', "Model " . $id . " has already been pre-registered." );
        return;
      }

      $pre_registered[$id] = $model;
      self::$pre_registered = $pre_registered;
    }


    //@return void()
    //=> removes a pre_register model from the pre_registered list
    function tc_remove_pre_registered($id) {
      $pre_registered = self::$pre_registered;
      if ( isset($pre_registered[$id]) )
        unset($pre_registered[$id]);
      self::$pre_registered = $pre_registered;
    }


    //registers the pre-registered model when query is ready
    //hook : wp
    //@return void()
    function tc_register_pre_registered() {
      foreach ( self::$pre_registered as $id => $model ) {
        //removes from the pre_registered list
        $this -> tc_remove_pre_registered($id);
        //registers
        $this -> tc_register($model);
      }
      //say it to the api
      do_action( 'pre_registered_registered', self::$pre_registered );
    }



    //at this stage, the model has a unique id.
    //implement the registered changes before adding model to collection
    //@return the args array()
    //hook : tc_prepare_model
    //@return updated model
    public function tc_apply_registered_changes( $model ) {
      if ( ! isset($model['id']) )
        return $model;

      if ( ! $this -> tc_has_registered_change( $model['id']) )
        return $model;

      $id = $model['id'];

      //IS THERE A REGISTERED REQUEST FOR CHANGE ?
      $to_change  = self::$_change_candidates;
      //=> overwrite the modified args with the new ones
      $model = wp_parse_args( $to_change[$id], $model );
      //This event will trigger a removal of the change from the change list
      //=> tc_deregister_change
      do_action('registered_changed_applied' , $id, $model );

      return $model;
    }




    /**********************************************************************************
    * INSTANCIATE THE MODEL'S CLASS
    ***********************************************************************************/
    //some model's need to be augmented by an extended class.
    //hook : 'wp'
    //this method load the relevant model class file and return the instance
    //@return instance object
    public function tc_instantiate_model($model) {
      $instance = null;

      //try to instantiate the model specified in the model_class param
      //if not found try to retrieve it from the template param (mandatory):
      //a) The model_class, when specified, must refer to a valid model otherwise a notice will be fired.
      //b) Also if a whatever model has been instantiated it must be a subclass of TC_Model - otherwise a notice will be fired.
      //c) Else If no suitable model has been instantiated instantiate the base model class
      foreach ( array( 'model_class', 'template' ) as $_model_class ) {
        if ( ! isset($model[ $_model_class ]) || empty($model[ $_model_class ]) )
            continue;

        //A model class has been defined, let's try to load it and instantiate it
        //The model_class arg can also be an array in the form array( 'parent' => parent_model_class (string), 'name' => model_class ('string') )
        if ( 'model_class' == $_model_class && isset( $model['model_class']['name'] ) ) {
          $this -> tc_require_model_class( $model['model_class']['parent'] );
          $model_class     = $model[ $_model_class ]['name'];

        } else {
          $model_class     = $model[ $_model_class ];
        }

        $model_class_name     = sprintf( 'TC_%s_model_class', $this -> tc_require_model_class( $model_class ) );

        if ( class_exists($model_class_name) ) {
          $instance = new $model_class_name( $model );
        }

        if ( ! is_object($instance) && 'model_class' == $_model_class ) {
          do_action('tc_dev_notice', "Model : " . $model['id'] . ". The model has not been instantiated." );
          return;
        }
        //A model must be TC_model or a child class of TC_model.
        if ( is_object($instance) && ! is_subclass_of($instance, 'TC_Model') ) {
          do_action('tc_dev_notice', "Model : " . $model['id'] . ". View Instantiation aborted : the specified model class must be a child of TC_Model." );
          return;
        } else break;
      }//end foreach

      if ( ! is_object( $instance ) )
        return new TC_Model( $model );

      return $instance;
    }


    private function tc_require_model_class( $_model_class ) {
      $model_class_basename = basename( $_model_class );
      $model_class_dirname  = dirname( $_model_class );

      tc_fw_require_once( sprintf( 'models/%1$s/class-model-%2$s.php', $model_class_dirname, $model_class_basename ) );

      return $model_class_basename;
    }








    /**********************************************************************************
    * UPDATE COLLECION
    ***********************************************************************************/
    //hook : 'model_alive' and 'model_property_changed'
    //The job of this method is :
    //1) to add a model to the collection
    //2) or to update an existing model
    //
    //=> always update the model list before rendering something
    //=> a model might have been registered in the delete / change candidates
    //=> this is fired on model_property_changed event
    //=> when a single model property has been changed in TC_Model::tc_set_property()
    //@param id string
    //@param $model instance object
    public function tc_update_collection( $id = false, $model ) {
      if ( ! $id || ! is_object($model) )
        return;

      //Check if we have to run a registered deletion here
      if ( $this -> tc_is_registered( $id ) && $this -> tc_has_registered_deletion( $id ) ) {
        do_action( 'tc_delete' , $id );
        return;
      }

      //Adds or updates a specific model in the collection
      $collection = self::$collection;
      $collection[$id] = $model;
      self::$collection = $collection;

      //emit an event on each collection updates
      do_action( 'collection_updated', $id, $model );
    }




    /**********************************************************************************
    * DE-REGISTER AN INSTANCIATED MODEL
    ***********************************************************************************/
    //keep in mind that the instance of the previous model with initial argument will still exists...
    //so will the additional class instance if any
    //@todo shall we store all models instances and delete them when requested ?
    public function tc_deregister( $id, $model ) {
      if ( ! is_array($model) )
        return;

      //Removes the previously set action
      if ( ! is_object($model['view_instance']) )
        do_action('tc_dev_notice', 'Attempt to de-register, but no model instance for id : '. $id );
      else if ( ! empty( $model['hook'] ) )
        remove_action( $model['hook'], array( $model['view_instance'], 'tc_maybe_render'), $model['priority'] );

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
      //avoid if already registered for deletion
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
    //if the model is registered and already instantiated => de-register it, register it again with the new params and update the promise change array
    //if the model is registered in the collection but not instantiated yet => simply update the collection
    //if the model is not-registered in the collection, register a promise for change
    //@return void()
    //@todo : allow several changes for a model ?
    public function tc_change( $id = null, $new_model = array() ) {
      if ( is_null($id) || ! is_array($new_model) )
        return;

      $current_model  = $this -> tc_get_model($id);//gets the model as an array of parameters
      $model_instance = $this -> tc_get_model_instance($id);

      if ( ! $model_instance )
        $this -> tc_register_change( $id, $new_model );
      else {
        //if the view has already been instantiated
        //- the previously hooked actions have to be unhooked
        //- the model is destroyed
        //- the model is registered again with the new params
        if ( $model_instance -> tc_has_instantiated_view() ) {
          $model_instance -> tc_unhook_view();
        }
        //delete the current version of the model
        $this -> tc_delete( $id );
        //reset the new_model with existing properties
        $new_model = wp_parse_args( $new_model, $current_model );

        //register the new version of the model
        $this -> tc_register( $new_model );
      }
    }


    //stores a requested change for a model not yet registered
    //@id = id of the model
    //@args = model params to change
    //@return void()
    private function tc_register_change( $id, $new_model ) {
      $collection = self::$collection;
      $to_change = self::$_change_candidates;
      //avoid if already registered
      if ( array_key_exists($id, $to_change) )
        return;

      $to_change[$id] = $new_model;
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
    //@return a single model set of params array
    public function tc_get_model( $id = null ) {
      $collection = self::$collection;
      if ( ! is_null($id) && isset($collection[$id]) )
        return (array)$collection[$id];
      return array();
    }

    //@return model instance or false
    //@param model id string
    public function tc_get_model_instance( $id = null ) {
      if ( is_null($id) )
        return;

      $collection = self::$collection;
      if ( ! isset($collection[$id]) )
        return;
      return $collection[$id];
    }


    //@return the collection of models
    public function tc_get() {
      //uses self::$instance instead of this to always use the parent instance
      return self::$collection;
    }


    /**********************************************************************************
    * HELPERS
    ***********************************************************************************/
    //@return bool
    private function tc_is_pre_registered($id) {
      return array_key_exists($id, self::$pre_registered);
    }


    //this function recursively :
    //1) checks if the requested priority is available on the specified hook
    //2) set a new priority until until it's available
    private function tc_set_priority( $hook, $priority ) {
      $priority = empty($priority) ? 10 : (int)$priority;
      $available = true;
      //loop on the existing model object in the collection
      foreach ( $this -> tc_get() as $id => $model) {
        if ( ! isset($model -> hook) )
          continue;
        if ( $hook != $model -> hook )
          continue;
        if ( $hook == $model -> hook && $priority != $model -> priority )
          continue;
        $available = false;
      }
      return $available ? $priority : $this -> tc_set_priority( $hook, $priority + 1 );
    }


    //Recursively create a unique id when needed
    //@return string id
    private function tc_set_unique_id( $id, $hook, $priority, $template ) {
      //if id not set, then :
      //1) try to create a unique id from the template name if specified
      //2) otherwise create a unique id from hook_priority
      if ( empty($id) || is_null($id) ) {
        if ( ! empty($template) )
          $id = basename( $template );
        else
          $id = "{$hook}_{$priority}";
      }


      //return it now if available
      if ( ! $this -> tc_is_registered($id) && ! $this -> tc_is_pre_registered($id) )
        return apply_filters('tc_set_model_unique_id' , $id, $hook, $priority );

      //add hyphen add the end if not there
      $id                 = ! is_numeric(substr($id, -1)) ? $id . '_0' : $id;
      $id_exploded        = explode('_' , $id);
      $_index             = end($id_exploded);
      $_key               = key($id_exploded);
      //set new value
      $id_exploded[$_key] = $_index + 1;
      $id                 = implode( "_" , $id_exploded );

      //recursive check
      return $this -> tc_set_unique_id( $id, $hook, $priority, $template );
    }



    //@return array of child models
    //@return false if has no children
    private function tc_get_children($id) {
      if ( ! $this -> tc_has_children($id) )
        return;

      $model = $this -> tc_get_model($id);
      return ! empty( $model['children'] ) ? $model['children'] : false;
    }


    //checks if a model exists in the collection
    //@param string id
    //@return bool
    public function tc_is_registered( $id ) {
      return array_key_exists( $id, self::$collection );
    }

  }//end of class
endif;
