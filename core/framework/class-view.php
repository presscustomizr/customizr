<?php
//This is the view class. Each front end part of the Customizr theme is rendered through a view instance.
//Each view inherits its properties from a model instance.
//The model decides if it has to instantiate the view or not, according to the context

//Once properly instantiated with the model as parameter, a view does not think to anything else than rendering what and where (hook) we ask it to.
//This class jobs are :
//- render either html, WordPress template or more complex content, depending on the its model settings.
//- when renders on a template, the view must pass its model to the WordPress template through the $wp_query global


if ( ! class_exists( 'CZR_View' ) ) :
  class CZR_View {
    public $model;
    function __construct( $model ) {
          // $keys = array_keys( get_object_vars( $this ) );

          // foreach ( $keys as $key ) {
          //   if ( isset( $model[ $key ] ) ) {
          //     $this->$key = $model[ $key ];
          //   }
          // }
          $this -> model = $model;

          //emit event on view instantiation
          //Will be listen to by the model and trigger the maybe_hook_view callback
          do_action( "view_instantiated_{$this -> model -> id}", $this );
    }




    /**********************************************************************************
    * RENDERS
    ***********************************************************************************/
    //hook : $model['hook']
    //NOTE : the $this here can be the child class $this.
    public function czr_fn_maybe_render() {
          //this event is used to check for late deletion or change before actually rendering
          //will fire czr_fn_apply_registered_changes_to_instance
          //do_action( 'pre_render_view', $this -> id );

          if ( ! apply_filters( "czr_do_render_view_{$this -> model -> id}", true ) )
            return;

          //allow filtering of the model before rendering (the view's model is passed by reference)
          do_action_ref_array( 'pre_rendering_view', array(&$this -> model) );
          do_action_ref_array( "pre_rendering_view_{$this -> model -> id}", array(&$this -> model) );

          do_action( "__before_{$this -> model -> id}" );

          $czr_fn_print_debug =  ! czr_fn_is_customizing() && is_user_logged_in() && current_user_can( 'edit_theme_options' );

          ?>
          <?php
          if ( $czr_fn_print_debug ) {
            echo "<!-- HOOK CONTENT HERE : __before_{$this -> model -> id} -->";

            /* Maybe merge debug info into the model element attributes */
            $this -> model -> element_attributes =  join( ' ', array_filter( array(
                $this -> model -> element_attributes,
                'data-model_id="'. $this -> model -> id .'"',
                isset( $this -> model -> template ) ? 'data-template="'. $this -> model -> template .'"' : ''
            )) );
            echo "<!-- START RENDERING VIEW ID : {$this -> model -> id} -->";
          }

            $this -> czr_fn_render();

          if ( $czr_fn_print_debug ) {
            echo "<!-- END OF RENDERING VIEW ID : {$this -> model -> id} -->";
            echo "<!-- HOOK CONTENT HERE : __after_{$this -> model -> id} -->";
          }
          do_action( "__after_{$this -> model -> id}" );

          // (the view's model is passed by reference)
          do_action_ref_array( 'post_rendering_view', array(&$this -> model) );
          do_action_ref_array( "post_rendering_view_{$this -> model -> id}", array(&$this -> model) );
    }



    //might be overriden in the child view if any
    public function czr_fn_render() {
          if ( ! empty( $this -> model -> html ) )
            echo $this -> model -> html;

          if ( ! empty( $this -> model -> template ) ) {
            //get the filename
            $_template_file = czr_fn_get_theme_file("templates/{$this -> model -> template}.php" );

            if ( false !== $_template_file ) {
              czr_fn_set_current_model( $this -> model );
              ob_start();
                load_template( $_template_file, $require_once = false );
              $_temp_content = ob_get_contents();

              ob_end_clean();
              if ( ! empty($_temp_content) )
                echo $_temp_content;

              czr_fn_reset_current_model();
            }
          }

          if ( ! empty( $this -> model -> callback ) )
            czr_fn_fire_cb( $this -> model -> callback, $this -> model -> cb_params );
    }



    //at this stage, the view is instantiated
    //@return void()
    private function czr_fn_update_model_instance( $id, $new_params ) {
          //get current params
          $current_params = $this -> czr_fn_get_view($id);
          if ( ! $current_params || ! is_array($current_params) )
            return;
          //pre-process new params
          $new_params = wp_parse_args( $new_params, $current_params );

          //update the modified view properties
          //=> will automatically trigger the collection update
          foreach ($new_params as $property => $value) {
            if ( $value != $current_params[$property] )
              $current_params['view_instance'] -> czr_fn_set_property( $property, $value );
          }
    }


    /**********************************************************************************
    * PUBLIC HELPERS
    ***********************************************************************************/
    public function czr_fn_get_instance() {
          return $this;
    }

  }//class
endif;
