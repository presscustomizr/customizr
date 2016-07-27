<?php
require_once( get_template_directory() . '/functions-test.php' );

//@todo : breadcrumd czr_fn_echo does not work in the template
//@todo : remove the
//Fire
require_once( get_template_directory() . '/core/init.php' );

//shortcut function to get a Customizr option
if ( ! function_exists('czr_fn_get_opt') ) {
  function czr_fn_get_opt( $option_name , $option_group = null, $use_default = true ) {
    return czr_fn_opt( $option_name , $option_group, $use_default ) ;
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists('czr_fn_column_content_wrapper_class') ) {
  function czr_fn_column_content_wrapper_class() {
    return CZR() -> czr_fn_column_content_wrapper_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the article container class
if ( ! function_exists('czr_fn_article_container_class') ) {
  function czr_fn_article_container_class() {
    return CZR() -> czr_fn_article_container_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('czr_fn_get_theme_file') ) {
  function czr_fn_get_theme_file( $path_suffix ) {
    return CZR() -> czr_fn_get_theme_file( $path_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('czr_fn_get_theme_file_url') ) {
  function czr_fn_get_theme_file_url( $url_suffix ) {
    return CZR() -> czr_fn_get_theme_file_url( $url_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists('czr_fn_require_once') ) {
  function czr_fn_require_once( $path_suffix ) {
    return CZR() -> czr_fn_require_once( $path_suffix );
  }
}


/*
 * @since 3.5.0
 */
//shortcut function to set the current model which will be accessible by the czr_fn_get
if ( ! function_exists('czr_fn_set_current_model') ) {
  function czr_fn_set_current_model( $model ) {
    return CZR() -> czr_fn_set_current_model( $model );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to reset the current model
if ( ! function_exists('czr_fn_reset_current_model') ) {
  function czr_fn_reset_current_model() {
    return CZR() -> czr_fn_reset_current_model();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a current model property
if ( ! function_exists('czr_fn_get') ) {
  function czr_fn_get( $property, $model_id = null, $args = array() ) {
    return CZR() -> czr_fn_get( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo a current model property
if ( ! function_exists('czr_fn_echo') ) {
  function czr_fn_echo( $property, $model_id = null, $args = array() ) {
    return CZR() -> czr_fn_echo( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to instantiate easier
if ( ! function_exists('czr_fn_new') ) {
  function czr_fn_new( $_to_load, $_args = array() ) {
    CZR() -> czr__fn_( $_to_load , $_args );
    return;
  }
}

//shortcut function to instantiate a model + render its template
//model and template should share the same name
//some templates are shared by several models => that's when the $_id param is useful
if ( ! function_exists('czr_fn_render_template') ) {
  function czr_fn_render_template( $_t, $_id = null ) {
    if ( ! $_t || empty($_t) )
        return;

    $_model_id = is_null($_id) ? basename($_t) : $_id;

    if ( czr_fn_is_registered( $_model_id ) ) {
      //sets the template property on the fly based on what's requested
      if ( ! czr_fn_get_model_property( $_model_id, 'template') ) {
        czr_fn_get_model_instance( $_model_id ) -> czr_fn_set_property('template' , $_t );
      }
      czr_fn_get_view_instance($_model_id ) -> czr_fn_maybe_render();
    }
    else {
      //$_model_instance = CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
      czr_fn_register( array( 'id' => $_model_id, 'render' => true, 'template' => $_t ) );
    }
  }
}

//@return boolean
//states if registered and possible
//useful is a check has to be done in the template before "instant" registration.
function czr_fn_has( $_t, $_id = null, $only_registered = false ) {
  $_model_id = is_null($_id) ? $_t : $_id;
  if ( CZR() -> collection -> czr_fn_is_registered( $_model_id ) ) {
    return true;
  }
  //if the model is not registered yet, let's test its eligibility by accessing directly its controller boolean if exists
  elseif ( ! $only_registered ) {
    return CZR() -> controllers -> czr_fn_is_possible( $_model_id );
  }
}

//@return boolean
//states if registered only
function czr_fn_is_registered( $_model_id ) {
  return CZR() -> collection -> czr_fn_is_registered( $_model_id );
}

//@return model object if exists
function czr_fn_get_model_instance( $_model_id ) {
  if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
    return;
  return CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
}

//@return model property if exists
//@param _model_id string
//@param property name string
function czr_fn_get_model_property( $_model_id, $_property ) {
  if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
    return;
  return CZR() -> collection -> czr_fn_get_model_instance( $_model_id ) -> czr_fn_get_property($_property);
}

//@return view model object if exists
function czr_fn_get_view_instance( $_model_id ) {
  if ( ! isset(CZR() -> collection -> czr_fn_get_model_instance( $_model_id ) -> view_instance) )
    return;
  return CZR() -> collection -> czr_fn_get_model_instance( $_model_id ) -> view_instance;
}


function czr_fn_register( $model = array() ) {
  return CZR() -> collection -> czr_fn_register( $model );
}


/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return CZR___::czr_fn_instance();
}

// Fire Customizr
CZR();
