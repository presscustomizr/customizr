<?php
//Models contain the underlying data of each views.

//A model is an object instantiated by the collection class.
//Before instantiation, the collection class has made sure that the model has at least :
//1) a unique id
//2) a hook
//3) a priority set.

//Once alive, a model's job is :
//- instantiate its view. The model must check if its related view has to be instantiated with the default view class or a child of it.
//- assign the view to its rendering hook
//- register its child models if any


if ( ! class_exists( 'CZR_Model' ) ) :
  class CZR_Model {
    static $instance;

    //the model properties
    //each view will inherit those properties
    public $hook = "";//this is the default hook declared in the index.php template
    public $render = false;
    public $view_instance;
    public $id = "";
    public $model_class = false;
    public $query = false;
    public $priority = 10;
    public $template = "";
    public $element_tag;
    public $element_id;
    public $element_class = "";
    public $element_attributes;
    public $html = "";
    public $callback = "";
    public $cb_params = array();
    public $early_setup = false;
    public $parent = '';//stores the model id string from which a child has been instantiated.
    public $children = array();
    public $controller = "";
    public $visibility = true;//can be typically overriden by a check on a user option

    public $defaults   = array();

    //on instantiation the id is unique and the priority propery setup
    //=> those treatments have been managed by the collection
    function __construct( $model = array() ) {
          self::$instance =& $this;
          $CZR            = CZR();

          //here is where extension classes can set their preset models
          $_preset = $this -> czr_fn_get_preset_model();
          if ( is_array($_preset) && ! empty( $_preset ) )
            $model = wp_parse_args( $_preset, $model );

          //here is where extension classes can modify the model params before they're parsed
          //becoming model properties
          $model = $this -> czr_fn_extend_params( $model );


          if ( empty( $model ) ) {
            do_action('czr_dev_notice', 'in CZR_MODEL construct : a model has no id ');
            return;
          } elseif ( FALSE == $model['id'] ) {
            //if model ID has been set to false => silent exit. Useful in cases when in czr_fn_extend_params the model
            //itself understands that it has to exit its instantiation
            $CZR -> collection -> czr_fn_delete( $this -> id );
            return;
          }
          //equivalent of wp_parse_args() with default model property values
          $this -> czr_fn_update( $model );

          //at this stage the mode must at least have :
          //1) a unique id
          //2) a priority set
          //3) a hook => not anymore since czr_fn_render_template()
          if ( ! $this -> czr_fn_can_model_be_instantiated() ) {
            $CZR -> collection -> czr_fn_delete( $this -> id );
            return;
          }
          //set-up the children
          $this -> czr_fn_maybe_setup_children();

          //Registers its children if any
          $this -> czr_fn_maybe_register_children();

          //maybe alter body class
          if ( method_exists( $this, 'czr_fn_body_class' ) )
            add_filter( 'body_class', array( $this, 'czr_fn_body_class' ) );

          //maybe add style
          $this -> czr_fn_maybe_add_style();

          //a way to allow models to act on their view
          $this -> czr_fn_add_view_pre_and_post_actions();

          //Allow models to filter their view visibility
          add_filter( "czr_do_render_view_{$this -> id}", array( $this, 'czr_fn_maybe_render_this_model_view' ), 0 );

          //adds the view instance to the model : DO WE REALLY NEED TO DO THAT ?
          //view instance as param
          add_action( "view_instantiated_{$this -> id}"   , array( $this, 'czr_fn_add_view_to_model'), 10, 1 );

          //takes the view instance as param
          add_action( "view_instantiated_{$this -> id}"   , array( $this, 'czr_fn_maybe_hook_or_render_view'), 20, 1 );


          //Maybe instantiate the model's view
          //listens to 'wp' if not fired yet, or fire the instantiation
          if ( ! did_action('wp') )
            add_action( 'wp'                                , array( $this, 'czr_fn_maybe_instantiate_view' ), 999 );
          else
            $this -> czr_fn_maybe_instantiate_view();

    }//construct


    //add this instance to the view description in the collection
    //=> can be used later for deregistration
    //hook : view_instantiated
    function czr_fn_add_view_to_model( $view_instance ) {
          $this -> czr_fn_set_property( 'view_instance', $view_instance );
    }



    /**********************************************************************************
    * INSTANCIATE THE MODEL VIEW => check the controllers,
    * @wp_timezone_override_offset(); check if it's been changed or deleted ?
    ***********************************************************************************/
    //default hook : wp | 1000
    //@return void()
    public function czr_fn_maybe_instantiate_view() {
          do_action( "pre_instantiate_view" );
          $CZR            = CZR();
          //this check has already been done before instantiating the model.
          //Do we really need this again here ?
          if ( ! $CZR -> controllers -> czr_fn_is_possible($this -> czr_fn_get())  )
            return;

          //instantiate the view with the current model object as param
          $view_instance = new CZR_View( $this );
    }//fn



    /**********************************************************************************
    * ACTIONS ON MODEL INSTANCIATION : MAYBE ADD SPECIFIC MODEL STYLE
    ***********************************************************************************/
    public function czr_fn_maybe_add_style() {
          //for now just add filter to czr_fn_user_options_style
          if ( method_exists( $this, 'czr_fn_user_options_style_cb' ) )
            add_filter( 'czr_user_options_style', array( $this, 'czr_fn_user_options_style_cb' ) );
    }//fn


    /**********************************************************************************
    * ACTIONS ON MODEL INSTANCIATION : MAYBE SETUP CHILDREN FOR SUCCESSIVE REGISTRATION
    ***********************************************************************************/
    public function czr_fn_maybe_setup_children() {
          //set-up the children
          if ( ! method_exists( $this, 'czr_fn_setup_children') )
            return;

          $children = apply_filters( "czr_{$this -> id}_children_list", $this -> czr_fn_setup_children() );
          $this -> czr_fn_set_property( 'children', $children );
          $this -> czr_fn_set_property( 'parent', $this -> id );
    }//fn


    /***********************************************************************************
    * ACTIONS ON VIEW READY
    * => THE POSSIBLE VIEW CLASS IS NOW INSTANCIATED
    ***********************************************************************************/
    //hook : 'view_instantiated'
    //@param $instance is the view instance object, can be CZR_View or a child of CZR_View
    //hook the rendering method to the hook
    //$this -> view_instance can be used. It can be a child of this class.
    public function czr_fn_maybe_hook_or_render_view($instance) {
          if ( empty($this -> id) ) {
            do_action('czr_dev_notice', 'In CZR_Model, a model is missing its id.' );
            return;
          }

          //Are we in czr_fn_render_template case
          //=> Typically yes if did_action('template_redirect'), since every model are registered on 'wp'
          //AND if the render property is forced to true
          //if not check if template_redirect has already been fired, to see if we are in a czr_fn_render case
          if ( did_action('template_redirect') && $this -> render ) {
            $instance -> czr_fn_maybe_render();
            return;//this is the end, beautiful friend.
          }

          //What are the requested hook and priority ?
          //=> this can be overriden in an extended model for example
          //? using the czr_fn_set_property ( 'hook' , 'value' ) could also be an option in an extended model ?
          $_da_hook     = apply_filters("_da_hook_{$this -> id}" , $this -> hook );
          $_da_priority = apply_filters("_da_priority_{$this -> id}" , $this -> priority );


          //Renders the view on the requested hook
          //'cause yes we do have a hook at this point, who doubts about that ?
          //Well me, but I know I shouldn't. I'm just a freaky damned scary lone coder in the night riding a sparkle horse.
          if ( false == $_da_hook )
            return;

          add_action( $_da_hook, array( $instance , 'czr_fn_maybe_render' ), $_da_priority );
          //emit an event each time a view is hooked
          do_action( 'view_hooked' , $this -> id );
    }


    //@param instance view
    //@return void()
    public function czr_fn_unhook_view() {
          if ( false == $this -> hook || ! is_object( $this -> view_instance) )
            return;
          remove_action( $this -> hook, array( $instance , 'czr_fn_maybe_render' ), $this -> priority );
          //say it
          do_action( 'view_unhooked' , $this -> id );
    }



    /**********************************************************************************
    * ACTIONS ON MODEL INSTANCIATION : REGISTERS CHILD MODEL
    ***********************************************************************************/
    //hook : view ready
    //=> the collection here can be the full collection or a partial set of views (children for example)
    public function czr_fn_maybe_register_children() {
          if ( ! $this -> czr_fn_has_children() )
            return;

          $CZR            = CZR();
          $children_collection = array();
          foreach ( $this -> children as $id => $model ) {
            //re-inject the id into the view_params
     //       $model['id'] = $id;
            $id = $CZR -> collection -> czr_fn_register( $model );
            if ( $id )
              $children_collection[$id] = $model;
          }//foreach

          //update the children property, at this stage will contain a list of the model ids of the registered children
          $this -> czr_fn_set_property( 'children', array_keys( $children_collection ) );
          //emit an event if a children collection has been registered
          //=> will fire the instantiation of the children collection with czr_fn_maybe_instantiate_collection
          do_action( 'children_registered', $children_collection );
    }


    /***********************************************************************************
    * EXPOSED GETTERS / SETTERS
    ***********************************************************************************/
    //Checks if a registered view has child views
    //@return boolean
    public function czr_fn_has_children() {
          return ! empty($this -> children);
    }


    //normalizes the way we can access and change a single model property
    //=> emit an event to update the collection
    //@return void()
    public function czr_fn_set_property( $property, $value ) {
          $this -> $property = $value;

          //will trigger a collection update
          //pass : id, (object) model, changed property, new property value
          do_action( 'model_property_changed', $this -> id, $this , $property, $value );
    }

    //normalizes the way we can access and change a single model property
    //@return the property
    public function czr_fn_get_property( $property, $args = array() ) {
          if ( method_exists( $this, "czr_fn_get_{$property}" ) )
            return call_user_func_array( array($this, "czr_fn_get_{$property}"), $args );
          return isset ( $this -> $property ) ? $this -> $property : '';
    }

    //@returns the model property as an array of params
    public function czr_fn_get() {
          $model = array();
          foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
            $model[ $key ] = $this->$key;
          }
          return $model;
    }

    //@return array()
    //Extension models can use this to declare a preset model
    //is fired on instantiation
    //@return array()
    protected function czr_fn_get_preset_model() {
      return array();
    }

    //@return array()
    //Extension models can use this to update the model params with a set of new ones
    //is fired on instantiation
    //@param = array()
    protected function czr_fn_extend_params( $model = array() ) {
          //parse args into the model
          if ( ! empty( $model[ 'args' ] ) && is_array( $model[ 'args' ] ) ) {
            $model = wp_parse_args( $model[ 'args' ], $model );
            unset( $model[ 'args' ] );
          }
          return $model;
    }

    //@return void()
    //update the model properties with a set of new ones
    //is fired on instantiation
    //@param = array()
    public function czr_fn_update( $model = array() ) {
          /*
          * Parse model into $this->defaults, if not empty
          * This property will be merged with the array of properties
          * It allows us, when updating a model, to specify only those properties that need to be different from the defaults.
          * Specially interesting for those "singleton" models, whose only one instance is used throughout the page.
          * The fact that the models retain the properties could cause undesired effects.
          * E.g.
          * - Edit Button
          * the edit button model, which feeds the template,
          * is filled by the slider of posts with a text saying "Customize or remove the posts slider",
          * this means that if we render the edit button in a list of posts below the slider
          * we are forced to specify the new text, which, in most of the cases would be just "Edit" (defaults)
          */
          if ( ! empty( $this -> defaults ) )
            $model = wp_parse_args( $model, $this->defaults );

          foreach ( $model as $key => $value ) {
            if ( ! isset( $this->key) || ( isset( $this->$key ) && $model[ $key ] != $this->$key ) )
              $this->$key = $model[ $key ];
          }

          //emit an event when a model is updated
          do_action( 'model_updated', $this -> id );
    }



    /***********************************************************************************
    * ACTIONS ON VIEW READY
    * => THE POSSIBLE VIEW CLASS IS NOW INSTANCIATED
    ***********************************************************************************/
    //@hook czr_do_render_view_{$this -> id}
    //@return bool
    // Controls the rendering of the model's view
    public function czr_fn_maybe_render_this_model_view() {
          return $this -> visibility;
    }

    // Maybe add pre_rendering_view action hook callback to filter the model before rendering
    // Extended classes might want to override this method, so to hook them to a specific pre rendering id
    // I prefer to not allow the automatic hooking to a specific view without checking the existence of a callback to avoid the useless adding of a "dummy" cb ot the array of action callbacks
    // Though makes sense to hook a certain model ID to its view pre_rendering to parse its own properties before rendering. Example:
    // The class parameter will be stored in the model as an array to allow a better way to filter it ( e.g. avoid duplications), but to make it suitable for the rendering, it must be transformed in a string
    // Maybe we can think about make the model attributes, when needed, a set of
    // value, "sanitize" callback and let the view class do this..
    protected function czr_fn_add_view_pre_and_post_actions() {

          //by default filter this module before rendering (for default properties parsing, e.g. element_class )
          add_action( "pre_rendering_view_{$this -> id}", array($this, "czr_fn_pre_rendering_my_view_cb" ), 9999 );

          //by default filter this module before rendering (for default properties parsing, e.g. element_class )
          add_action( "post_rendering_view_{$this -> id}", array($this, "czr_fn_post_rendering_my_view_cb" ), 9999 );
    }


    /*
    * Before rendering this model view allow the setup of the late properties (e.g. in the loops)
    * Always sanitize those properties for being printed ( e.g. array to string )
    */
    public function czr_fn_pre_rendering_my_view_cb( $model ) {
          if ( method_exists( $this, 'czr_fn_setup_late_properties' ) )
            $this -> czr_fn_setup_late_properties();

          $this -> czr_fn_sanitize_model_properties( $model );
    }

    /*
    * After rendering this model view allow the reset (e.g. in the loops)
    */
    public function czr_fn_post_rendering_my_view_cb( $model ) {
          if ( method_exists( $this, 'czr_fn_reset_late_properties' ) )
            $this -> czr_fn_reset_late_properties();

    }

    protected function czr_fn_sanitize_model_properties( $model ) {
          $this -> element_class  = $this -> czr_fn_stringify_model_property( 'element_class' );
    }


    /**********************************************************************************
    * HELPERS
    ***********************************************************************************/
    protected function czr_fn_stringify_model_property( $property ) {
          if ( isset( $this -> $property ) )
           return czr_fn_stringify_array( $this -> $property );
          return '';
    }


    //@return bool
    //at this stage the mode must at least have :
    //1) a unique id
    //2) a priority set
    private function czr_fn_can_model_be_instantiated() {
      //the model must be an array of params
      //the hook is the only mandatory param => not anymore since czr_fn_render_template()
      if ( ! is_numeric( $this -> priority ) || empty($this -> id) ) {
        do_action('czr_dev_notice', "In CZR_Model class, a model instantiation aborted. Model is not ready for the collection, it won't be registered. at this stage, the model must have an id, a hook and a numeric priority." );
        return;
      }
      return true;
    }

    //checks if the model exists and is an instance
    //@return bool
    public function czr_fn_has_instantiated_view() {
          return is_object( $this -> view_instance );
    }

  }//end of class
endif;