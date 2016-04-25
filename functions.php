<?php
//@todo : breadcrumd tc_echo does not work in the template
//@todo : remove the
//Fire
require_once( get_template_directory() . '/core/init.php' );

/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists('tc_column_content_wrapper_class') ) {
  function tc_column_content_wrapper_class() {
    return CZR___::$instance -> tc_column_content_wrapper_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the article container class
if ( ! function_exists('tc_article_container_class') ) {
  function tc_article_container_class() {
    return CZR___::$instance -> tc_article_container_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('tc_get_theme_file') ) {
  function tc_get_theme_file( $path_suffix ) {
    return CZR___::$instance -> tc_get_theme_file( $path_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('tc_get_theme_file_url') ) {
  function tc_get_theme_file_url( $url_suffix ) {
    return CZR___::$instance -> tc_get_theme_file_url( $url_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists('tc_require_once') ) {
  function tc_require_once( $path_suffix ) {
    return CZR___::$instance -> tc_require_once( $path_suffix );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to require a framework file
if ( ! function_exists('tc_fw_require_once') ) {
  function tc_fw_require_once( $path_suffix ) {
    return CZR___::$instance -> tc_fw_require_once( $path_suffix );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to require a front framework file
if ( ! function_exists('tc_fw_front_require_once') ) {
  function tc_fw_front_require_once( $path_suffix ) {
    return CZR___::$instance -> tc_fw_front_require_once( $path_suffix );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to set the current model which will be accessible by the tc_get
if ( ! function_exists('tc_set_current_model') ) {
  function tc_set_current_model( $model ) {
    return CZR___::$instance -> tc_set_current_model( $model );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to reset the current model
if ( ! function_exists('tc_reset_current_model') ) {
  function tc_reset_current_model() {
    return CZR___::$instance -> tc_reset_current_model();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a current model property
if ( ! function_exists('tc_get') ) {
  function tc_get( $property, $model_id = null, $args = array() ) {
    return CZR___::$instance -> tc_get( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo a current model property
if ( ! function_exists('tc_echo') ) {
  function tc_echo( $property, $model_id = null, $args = array() ) {
    return CZR___::$instance -> tc_echo( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to instantiate easier
if ( ! function_exists('tc_new') ) {
  function tc_new( $_to_load, $_args = array() ) {
    CZR___::$instance -> tc__( $_to_load , $_args );
    return;
  }
}

//shortcut function to instantiate a model + render its template
//model and template should share the same name
//some templates are shared by several models => that's when the $_id param is useful
if ( ! function_exists('tc_render_template') ) {
  function tc_render_template( $_t, $_id = null ) {
    if ( ! $_t || empty($_t) )
        return;

    $_model_id = is_null($_id) ? basename($_t) : $_id;

    if ( tc_is_registered( $_model_id ) ) {
      //sets the template property on the fly based on what's requested
      if ( ! tc_get_model_property( $_model_id, 'template') ) {
        tc_get_model_instance( $_model_id ) -> tc_set_property('template' , $_t );
      }
      tc_get_view_instance($_model_id ) -> tc_maybe_render();
    }
    else {
      //$_model_instance = CZR() -> collection -> tc_get_model_instance( $_model_id );
      tc_register( array( 'id' => $_model_id, 'render' => true, 'template' => $_t ) );
    }
  }
}

//@return boolean
//states if registered and possible
//useful is a check has to be done in the template before "instant" registration.
function tc_has( $_t, $_id = null, $only_registered = false ) {
  $_model_id = is_null($_id) ? $_t : $_id;
  if ( CZR() -> collection -> tc_is_registered( $_model_id ) ) {
    return true;
  }
  //if the model is not registered yet, let's test its eligibility by accessing directly its controller boolean if exists
  elseif ( ! $only_registered ) {
    return CZR() -> controllers -> tc_is_possible( $_model_id );
  }
}

//@return boolean
//states if registered only
function tc_is_registered( $_model_id ) {
  return CZR() -> collection -> tc_is_registered( $_model_id );
}

//@return model object if exists
function tc_get_model_instance( $_model_id ) {
  if ( ! CZR() -> collection -> tc_is_registered( $_model_id ) )
    return;
  return CZR() -> collection -> tc_get_model_instance( $_model_id );
}

//@return model property if exists
//@param _model_id string
//@param property name string
function tc_get_model_property( $_model_id, $_property ) {
  if ( ! CZR() -> collection -> tc_is_registered( $_model_id ) )
    return;
  return CZR() -> collection -> tc_get_model_instance( $_model_id ) -> tc_get_property($_property);
}

//@return view model object if exists
function tc_get_view_instance( $_model_id ) {
  if ( ! isset(CZR() -> collection -> tc_get_model_instance( $_model_id ) -> view_instance) )
    return;
  return CZR() -> collection -> tc_get_model_instance( $_model_id ) -> view_instance;
}


function tc_register( $model = array() ) {
  return CZR() -> collection -> tc_register( $model );
}

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
    function tc__f ( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;

/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return CZR___::tc_instance();
}

// Fire Customizr
CZR();
