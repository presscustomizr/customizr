<?php
//This is the view class. Each front end part of the Customizr theme is rendered through a view instance.
//Each view inherits its properties from a model instance.
//The model decides if it has to instanciate the view or not, according to the context

//Once properly instanciated, a view does not think to anything else than rendering what and where (hook) we ask it to.
//A view may add specific properties to its instance, and doing so, extending its underlying model.
//This class (and children) jobs are :
//- assign its rendering callback to the specified hook
//- render either html, WordPress template or more complex content, depending on the its model settings.
//- when renders on a template, the view must pass its model to the WordPress template through the $wp_query global


if ( ! class_exists( 'TC_View' ) ) :
  class TC_View {
    public $hook = "";//this is the default hook declared in the index.php template
    public $id = "";
    public $query = false;
    public $priority = 10;
    public $template = "";
    public $html = "";
    public $callback = "";
    public $cb_params = array();
    public $children = array();
    public $controller = "";
    public $visibility = true;//can be typically overriden by a check on a user option

    function __construct( $model = array() ) {
      $keys = array_keys( get_object_vars( $this ) );

      foreach ( $keys as $key ) {
        if ( isset( $model[ $key ] ) ) {
          $this->$key = $model[ $key ];
        }
      }

      //emit event on view instanciation
      //Will be listen to by the model and trigger the maybe_hook_view callback
      do_action( "view_instanciated_{$this -> id}", $this );

      //listens to a view pre-render => and fire the tc_apply_registered_changes_to_instance
      // => a change might have been registered
      //view id
      //view params
      add_action( 'pre_render_view'             , array( $this, 'tc_apply_registered_changes_to_instance' ), 10, 1 );
    }




    /**********************************************************************************
    * RENDERS
    ***********************************************************************************/
    //hook : $model['hook']
    //NOTE : the $this here can be the child class $this.
    public function tc_maybe_render() {
      //this event is used to check for late deletion or change before actually rendering
      //will fire tc_apply_registered_changes_to_instance
      //do_action( 'pre_render_view', $this -> id );

      if ( ! apply_filters( "tc_do_render_view_{$this -> id}", true ) )
        return;

      do_action( "before_render_view_{$this -> id}" );
      ?>

      <!-- HOOK CONTENT HERE : <?php echo "before_render_view_{$this -> id}"; ?> -->
      <!-- START RENDERING VIEW ID : <?php echo $this -> id; ?> -->

      <?php $this -> tc_render(); ?>

      <!-- END OF RENDERING VIEW ID : <?php echo $this -> id; ?> -->
      <!-- HOOK CONTENT HERE : <?php echo "after_render_view_{$this -> id}"; ?> -->
      <?php

      do_action( "after_render_view_{$this -> id}" );
    }



    //might be overriden in the child view if any
    public function tc_render() {
      if ( ! empty( $this -> html ) )
        echo $this -> html;

      if ( ! empty( $this -> template ) ) {
        //get the basename
        $_template = basename( $this -> template );

        //add the view instance to the wp_query wp global
        set_query_var( "{$_template}_model", $this );
        get_template_part( "templates/{$this -> template}" );
      }
      // $path = '';
      // $part = '';
      // get_template_part( $path , $part );

      if ( ! empty( $this -> callback ) )
        CZR() -> helpers -> tc_fire_cb( $this -> callback, $this -> cb_params );
    }


    //updates the view properties with the requested args
    //stores the clean and updated args in a view property.
    //@return void()
    //called directly sometimes
    //fired on 'pre_render_view'
    //fired on tc_change if view is instanciated
    public function tc_apply_registered_changes_to_instance( $id, $new_params = array() ) {
      if ( ! CZR() -> collection -> tc_has_registered_change( $id ) )
        return;

      $new_params = empty($new_params) ? CZR() -> collection -> tc_get_registered_changes( $id ) : $new_params;

      $this -> tc_update_model_instance( $id, $new_params );

      //This event will trigger a removal of the change from the change list
      //=> tc_deregister_change
      do_action('registered_changed_applied' , $id);
    }


    //at this stage, the view is instanciated
    //@return void()
    private function tc_update_model_instance( $id, $new_params ) {
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


    /**********************************************************************************
    * PUBLIC HELPERS
    ***********************************************************************************/
    public function tc_get_instance() {
      return $this;
    }

  }//class
endif;