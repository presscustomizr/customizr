<?php

do_action( 'czr_four_doing_functions' );

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
    $CZR = CZR();
    return $CZR -> czr_fn_column_content_wrapper_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists('czr_fn_main_container_wrapper_class') ) {
  function czr_fn_main_container_class() {
    $CZR = CZR();
    return $CZR -> czr_fn_main_container_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the article container class
if ( ! function_exists('czr_fn_article_container_class') ) {
  function czr_fn_article_container_class() {
    $CZR = CZR();
    return $CZR -> czr_fn_article_container_class();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('czr_fn_get_theme_file') ) {
  function czr_fn_get_theme_file( $path_suffix ) {
    $CZR = CZR();
    return $CZR -> czr_fn_get_theme_file( $path_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('czr_fn_get_theme_file_url') ) {
  function czr_fn_get_theme_file_url( $url_suffix ) {
    $CZR = CZR();
    return $CZR -> czr_fn_get_theme_file_url( $url_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists('czr_fn_require_once') ) {
  function czr_fn_require_once( $path_suffix ) {
    $CZR = CZR();
    return $CZR -> czr_fn_require_once( $path_suffix );
  }
}


/*
 * @since 3.5.0
 */
//shortcut function to set the current model which will be accessible by the czr_fn_get
if ( ! function_exists('czr_fn_set_current_model') ) {
  function czr_fn_set_current_model( $model ) {
    $CZR = CZR();
    return $CZR -> czr_fn_set_current_model( $model );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to reset the current model
if ( ! function_exists('czr_fn_reset_current_model') ) {
  function czr_fn_reset_current_model() {
    $CZR = CZR();
    return $CZR -> czr_fn_reset_current_model();
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a current model property
if ( ! function_exists('czr_fn_get') ) {
  function czr_fn_get( $property, $model_id = null, $args = array() ) {
    $CZR = CZR();
    return $CZR -> czr_fn_get( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo a current model property
if ( ! function_exists('czr_fn_echo') ) {
  function czr_fn_echo( $property, $model_id = null, $args = array() ) {
    $CZR = CZR();
    return $CZR -> czr_fn_echo( $property, $model_id, $args );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to instantiate easier
if ( ! function_exists('czr_fn_new') ) {
  function czr_fn_new( $_to_load, $_args = array() ) {
    $CZR = CZR();
    $CZR -> czr__fn_( $_to_load , $_args );
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
if ( ! function_exists('czr_fn_render_template') ) {
  function czr_fn_render_template( $template, $args = array() ) {

    if ( empty( $template ) || ! is_string( $template ) )
      return; /* Fire a notice? */

    //extract
    $_t           =  $template;
    $_model_id    =  ! empty( $args['model_id'] ) ? $args['model_id'] : basename($_t);
    $_model_class =  ! empty( $args['model_class'] ) ? $args['model_class'] : '';
    $_model_args  =  ! empty( $args['model_args'] )  ? $args['model_args']  : array();

    if ( czr_fn_is_registered( $_model_id ) ) {
      $model_instance = czr_fn_get_model_instance( $_model_id );

      //sets the template property on the fly based on what's requested
      if ( ! czr_fn_get_model_property( $_model_id, 'template') ) {
        $model_instance -> czr_fn_set_property('template' , $_t );
      }
      //update model with the one passed
      if ( ! empty ( $_model_args ) )
        $model_instance -> czr_fn_update( $_model_args );

      czr_fn_get_view_instance($_model_id ) -> czr_fn_maybe_render();
    }
    else
      czr_fn_register( array( 'id' => $_model_id, 'render' => true, 'template' => $_t, 'model_class' => $_model_class, 'args' => $_model_args ) );
  }
}

//@return boolean
//states if registered and possible
//useful is a check has to be done in the template before "instant" registration.
function czr_fn_has( $_t, $_id = null, $only_registered = false ) {
  $_model_id = is_null($_id) ? $_t : $_id;
  $CZR = CZR();

  if ( $CZR -> collection -> czr_fn_is_registered( $_model_id ) ) {
    return true;
  }
  //if the model is not registered yet, let's test its eligibility by accessing directly its controller boolean if exists
  elseif ( ! $only_registered ) {
    return $CZR -> controllers -> czr_fn_is_possible( $_model_id );
  }
}

//@return boolean
//states if registered only
function czr_fn_is_registered( $_model_id ) {
  $CZR = CZR();
  return $CZR -> collection -> czr_fn_is_registered( $_model_id );
}

//@return boolean
//states if possible
function czr_fn_is_possible( $_model_id ) {
  $CZR = CZR();
  return $CZR -> controllers -> czr_fn_is_possible( $_model_id );
}

//@return model object if exists
function czr_fn_get_model_instance( $_model_id ) {
  $CZR = CZR();

  if ( ! $CZR -> collection -> czr_fn_is_registered( $_model_id ) )
    return;

  return $CZR -> collection -> czr_fn_get_model_instance( $_model_id );
}

//@return model property if exists
//@param _model_id string
//@param property name string
function czr_fn_get_model_property( $_model_id, $_property ) {
  $CZR = CZR();

  if ( ! $CZR -> collection -> czr_fn_is_registered( $_model_id ) )
    return;

  $model_instance = $CZR -> collection -> czr_fn_get_model_instance( $_model_id );
  return $model_instance -> czr_fn_get_property($_property);
}

//@return view model object if exists
function czr_fn_get_view_instance( $_model_id ) {
  $CZR = CZR();
  $model_instance = $CZR -> collection -> czr_fn_get_model_instance( $_model_id );

  if ( ! isset( $model_instance-> view_instance ) )
    return;

  return $model_instance -> view_instance;
}


function czr_fn_register( $model = array() ) {
  $CZR = CZR();
  return $CZR -> collection -> czr_fn_register( $model );
}


/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return CZR___::czr_fn_instance();
}

/****************************************************************************
****************************** HELPERS **************************************
*****************************************************************************/
/**
* Is the customizer left panel being displayed ?
* @return  boolean
* @since  3.4+
*/
function czr_fn_is_customize_left_panel() {
  global $pagenow;
  return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
}


/**
* Is the customizer preview panel being displayed ?
* @return  boolean
* @since  3.4+
*/
function czr_fn_is_customize_preview_frame() {
  return is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) );
}


/**
* Always include wp_customize or customized in the custom ajax action triggered from the customizer
* => it will be detected here on server side
* typical example : the donate button
*
* @return boolean
* @since  3.4+
*/
function czr_fn_doing_customizer_ajax() {
  $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
  return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
}


/**
* Are we in a customization context ? => ||
* 1) Left panel ?
* 2) Preview panel ?
* 3) Ajax action from customizer ?
* @return  bool
* @since  3.4+
*/
function czr_fn_is_customizing() {
  //checks if is customizing : two contexts, admin and front (preview frame)
  return czr_fn_is_customize_left_panel() ||
    czr_fn_is_customize_preview_frame() ||
    czr_fn_doing_customizer_ajax();
}


//@return boolean
if ( ! function_exists( 'czr_fn_is_partial_refreshed_on' ) ) {
  function czr_fn_is_partial_refreshed_on() {
    return apply_filters( 'czr_partial_refresh_on', true );
  }
}

/* HELPER FOR CHECKBOX OPTIONS */
//used in the customizer
//replace wp checked() function
if ( ! function_exists( 'czr_fn_checked' ) ) {
  function czr_fn_checked( $val ) {
    echo $val ? 'checked="checked"' : '';
  }
}

/**
* helper
* @return  bool
*/
if ( ! function_exists( 'czr_fn_has_social_links' ) ) {
  function czr_fn_has_social_links() {
    return ! empty ( czr_fn_get_opt('tc_social_links') );
  }
}
