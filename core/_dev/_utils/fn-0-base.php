<?php
/*
* An handly function to print the page wrapper class
*/
//shortcut function to echo the #tc-page-wrap class
if ( ! function_exists( 'czr_fn_page_wrapper_class' ) ) {
      function czr_fn_page_wrapper_class() {
            echo czr_fn_stringify_array( czr_fn_get_page_wrapper_class() );
      }
}

/*
* An handly function to print the content wrapper class
*/
if ( ! function_exists( 'czr_fn_column_content_wrapper_class' ) ) {
      function czr_fn_column_content_wrapper_class() {
            echo czr_fn_stringify_array( czr_fn_get_column_content_wrapper_class() );
      }
}


/*
* An handly function to print the main container class
*/
if ( ! function_exists( 'czr_fn_main_container_wrapper_class' ) ) {
      function czr_fn_main_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_main_container_class() );
      }
}

/*
* An handly function to print the article containerr class
*/
if ( ! function_exists( 'czr_fn_article_container_class' ) ) {
      function czr_fn_article_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_article_container_class() );
      }
}


/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists( 'czr_fn_get_theme_file_path' ) ) {
      function czr_fn_get_theme_file_path( $path_suffix ) {
            return CZR() -> czr_fn_get_theme_file_path( $path_suffix );
      }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists( 'czr_fn_get_theme_file_url' ) ) {
      function czr_fn_get_theme_file_url( $url_suffix ) {
            return CZR() -> czr_fn_get_theme_file_url( $url_suffix );
      }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists( 'czr_fn_require_once' ) ) {
      function czr_fn_require_once( $path_suffix ) {
            return CZR() -> czr_fn_require_once( $path_suffix );
      }
}


/*
 * @since 3.5.0
 */
//shortcut function to set the current model which will be accessible by the czr_fn_get
if ( ! function_exists( 'czr_fn_set_current_model' ) ) {
      function czr_fn_set_current_model( $model ) {
            return CZR() -> czr_fn_set_current_model( $model );
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to reset the current model
if ( ! function_exists( 'czr_fn_reset_current_model' ) ) {
      function czr_fn_reset_current_model() {
            return CZR() -> czr_fn_reset_current_model();
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a current model property
if ( ! function_exists( 'czr_fn_get_property' ) ) {
      function czr_fn_get_property( $property, $model_id = null, $args = array() ) {
            return CZR() -> czr_fn_get_property( $property, $model_id, $args );
      }
}



//shortcut function to get a current model
if ( ! function_exists( 'czr_fn_get_model' ) ) {
      function czr_fn_get_model() {
            return CZR() -> czr_fn_get_current_model();
      }
}


/*
 * @since 3.5.0
 */
//shortcut function to echo a current model property
if ( ! function_exists( 'czr_fn_echo' ) ) {
      function czr_fn_echo( $property, $model_id = null, $args = array() ) {
            return CZR() -> czr_fn_echo( $property, $model_id, $args );
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to instantiate easier
if ( ! function_exists( 'czr_fn_new' ) ) {
      function czr_fn_new( $_to_load, $_args = array() ) {
            CZR() -> czr__fn_( $_to_load , $_args );
            return;
      }
}

/*
* Shortcut function to instantiate a model + render its template
* model and template should share the same name
* some templates are shared by several models => that's when the $_id param is useful
* @since 4.0.0
* @param string $template             The template to render (with tree path, without the trailing .php)
* @param array  $args {
*     Optional. Array of options.
*     @type string $model_id           Optional. The id of the model to feed the template with.
*                                      If not specified or not already registered the system will try to
*                                      register a model with classname retrieved from the template option,
*                                      if available, otherwise the base model class will be registered
*     @type array|string $model_class  Optional. array|string. The model class (with tree path, without the trailing .php)
*                                      to  feed the model with. When array, in the form array( parent, name )

*     @type array $model_args          Optional. array of params to be injected into the model
*
*
* }
* @return void
*/
if ( ! function_exists( 'czr_fn_render_template' ) ) {
      function czr_fn_render_template( $template, $args = array() ) {

            if ( empty( $template ) || ! is_string( $template ) )
                  return; /* Fire a notice? */

            //extract
            $_t                  =  $template;
            $_model_id           =  ! empty( $args['model_id'] )    ? $args['model_id'] : basename( $_t );
            $_model_class        =  ! empty( $args['model_class'] ) ? $args['model_class'] : '';
            $_model_args         =  ! empty( $args['model_args'] )  ? $args['model_args']  : array();

            /*
            * Sometimes on rendering we want to reset some model properties to their defaults
            * declared in the model itself.
            * E.g. when we re-use "singleton" models, to automatically "re-init" them
            *
            * Sometimes, though, we don't want this.
            * E.g. when we re-use some "singleton" models in specific cases:
            * Common case, Gallery/Video/Audio... in list of posts:
            * In the list of posts we retrieve the existence of a media from the post list item model (e.g. inside the post_list_alternate).
            * In this case we ask (with or without proxies) to (say) the gallery model whether or not the item we want to render has a gallery.
            * The gallery model, then, is already initalized, and has already retrieved the information,
            * When rendering the gallery template, through the czr_fn_render_template function, thus, we just want to render what has been already
            * computed.
            * Will be care of the "caller" (post_list_alternate model, or the proxy it uses) to reset the gallery model's at each loop before retrieving
            * the informations it needs.
            */
            $_reset_to_defaults  =  is_array( $args ) && array_key_exists( 'reset_to_defaults' , $args) ? $args['reset_to_defaults']  : true;

            if ( czr_fn_is_registered( $_model_id ) ) {

                $model_instance = czr_fn_get_model_instance( $_model_id );

                //sets the template property on the fly based on what's requested
                if ( ! czr_fn_get_model_property( $_model_id, 'template') ) {
                    $model_instance -> czr_fn_set_property('template' , $_t );
                }

                //update model with the one passed
                if ( is_array($args) && array_key_exists( 'model_args', $args) ) {
                      $model_instance -> czr_fn_update( $_model_args, $_reset_to_defaults );
                } elseif ( $_reset_to_defaults ) {
                      $model_instance -> czr_fn_reset_to_defaults();
                }

                czr_fn_get_view_instance( $_model_id ) -> czr_fn_maybe_render();
            } else {
                //REGISTERS AND RENDERS
                //This is typically the case when we invoke the function inside a template, when the model is not yet registered
                //=> the render param is therefore set to true ( @see class model : czr_fn_maybe_hook_or_render_view  :
                //if ( did_action( 'template_redirect' ) && $this -> render ) {
                //     $instance -> czr_fn_maybe_render();
                //     return;//this is the end, beautiful friend.
                // })
                czr_fn_register(
                    array(
                      'id'          => $_model_id,
                      'render'      => true,
                      'template'    => $_t,
                      'model_class' => $_model_class,
                      'args'        => $_model_args
                    )
                );
            }
      }
}

//@return boolean
//states if registered or possible
//useful is a check has to be done in the template before "instant" registration.
//takes the template base name $_t ( which usually matches the model id )
//a specific model $_id can also be provided, in this case it will used to check the registration state and the controller
if ( ! function_exists( 'czr_fn_is_registered_or_possible' ) ) {
      function czr_fn_is_registered_or_possible( $_t, $_id = null, $only_registered = false ) {
            $_model_id = is_null($_id) ? $_t : $_id;

            if ( CZR() -> collection -> czr_fn_is_registered( $_model_id ) ) {
                  return true;
            }
            //if the model is not registered yet, let's test its eligibility by accessing directly its controller boolean if exists
            elseif ( ! $only_registered ) {
                  return CZR() -> controllers -> czr_fn_is_possible( $_model_id );
            }

      }
}

//@return boolean
//states if registered only
if ( ! function_exists( 'czr_fn_is_registered' ) ) {
      function czr_fn_is_registered( $_model_id ) {
            return CZR() -> collection -> czr_fn_is_registered( $_model_id );
      }
}

//@return boolean
//states if possible
if ( ! function_exists( 'czr_fn_is_possible' ) ) {
      function czr_fn_is_possible( $_model_id ) {
            return CZR() -> controllers -> czr_fn_is_possible( $_model_id );
      }
}

//@return model object if exists
if ( ! function_exists( 'czr_fn_get_model_instance' ) ) {
      function czr_fn_get_model_instance( $_model_id ) {
            if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
                  return;

            return CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
      }
}


//@return model property if exists
//@param _model_id string
//@param property name string
if ( ! function_exists( 'czr_fn_get_model_property' ) ) {
      function czr_fn_get_model_property( $_model_id, $_property ) {
            if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
                  return;

            $model_instance = CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
            return $model_instance -> czr_fn_get_property($_property);
      }
}

//@return view model object if exists
if ( ! function_exists( 'czr_fn_get_view_instance' ) ) {
      function czr_fn_get_view_instance( $_model_id ) {
            $model_instance = CZR() -> collection -> czr_fn_get_model_instance( $_model_id );

            if ( ! isset( $model_instance-> view_instance ) )
                  return;

            return $model_instance -> view_instance;
      }
}

// Shortcut function to register a model
// @return model id if registration went through
if ( ! function_exists( 'czr_fn_register' ) ) {
      function czr_fn_register( $model = array() ) {
            return CZR() -> collection -> czr_fn_register( $model );
      }
}

// Shortcut function to register a model if not already registered
// @return model id if:
// a) registration went through
// or
// b) model already registered
if ( ! function_exists( 'czr_fn_maybe_register' ) ) {
      function czr_fn_maybe_register( $model = array() ) {

            $_model_id = array_key_exists( 'id', $model ) && !empty( $model[ 'id' ] ) ? $model[ 'id' ] : false;

            if ( $_model_id && czr_fn_is_registered( $_model_id ) )
                  return $_model_id;

            return CZR() -> collection -> czr_fn_register( $model );
      }
}
?>