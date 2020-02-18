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
?><?php
/**
* Defines filters and actions used in several templates/classes
*
*/
/**
* hook : 'wp_head'
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_wp_filters() {
    add_filter( 'the_content'     , 'czr_fn_fancybox_content_filter'  );
    if ( apply_filters( 'czr_enable_lightbox_in_wc_short_description', false  ) ) {
        add_filter( 'woocommerce_short_description', 'czr_fn_fancybox_content_filter' );
    }
    /*
    * Smartload disabled for content retrieved via ajax
    */
    if ( apply_filters( 'czr_globally_enable_img_smart_load', !czr_fn_is_ajax() && esc_attr( czr_fn_opt( 'tc_img_smart_load' ) ) ) ) {
        add_filter( 'the_content'    , 'czr_fn_parse_imgs', PHP_INT_MAX );
        add_filter( 'czr_thumb_html' , 'czr_fn_parse_imgs'  );
        if ( apply_filters( 'czr_enable_img_smart_load_in_wc_short_description', false  ) ) {
            add_filter( 'woocommerce_short_description', 'czr_fn_parse_imgs' );
        }
    }
    add_filter( 'wp_title'        , 'czr_fn_wp_title' , 10, 2 );
}





/**
* This function returns the filtered global layout defined in CZR_init
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_global_layout() {
  return apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
}

/**
* This function returns the current context content breadth
* @return string
*
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_content_breadth() {
  $sidebar_layout                 = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );

  switch ( $sidebar_layout ) {
    case 'b': $content_breadth = 'narrow';
              break;
    case 'f': $content_breadth = 'full';
              break;
    default : $content_breadth = 'semi-narrow';
  }

  return apply_filters( 'czr_content_breadth' , $content_breadth );
}

/**
* This function returns the layout (sidebar(s), or full width) to apply to a context
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
      global $post;
      //Article wrapper class definition
      $global_layout                 = czr_fn_get_global_layout();

      /* Force 404 layout */
      if ( is_404() ) {
            $czr_screen_layout = array(
                'sidebar' => false,
                'class'   => 'col-12 col-md-8 offset-md-2'
            );
            return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }


      /* DEFAULT LAYOUTS */
      //what is the default layout we want to apply? By default we apply the global default layout
      $czr_sidebar_default_layout    = esc_attr( czr_fn_opt('tc_sidebar_global_layout') );
      $czr_sidebar_force_layout      = esc_attr( czr_fn_opt('tc_sidebar_force_layout') );

      //checks if the 'force default layout' option is checked and return the default layout before any specific layout
      if( $czr_sidebar_force_layout ) {
            $class_tab  = $global_layout[$czr_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $czr_screen_layout = array(
                'sidebar' => $czr_sidebar_default_layout,
                'class'   => $class_tab
            );
            return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }

      global $wp_query, $post;
      $is_singular_layout          = false;


      if ( apply_filters( 'czr_is_post_layout', is_single( $post_id ), $post_id ) || czr_fn_is_attachment_image() ) {
            $_czr_sidebar_default_layout  = esc_attr( czr_fn_opt('tc_sidebar_post_layout') );
            $is_singular_layout           = true;
      } elseif ( apply_filters( 'czr_is_page_layout', is_page( $post_id ), $post_id ) ) {
            $_czr_sidebar_default_layout  = esc_attr( czr_fn_opt('tc_sidebar_page_layout') );
            $is_singular_layout           = true;
      }

      $czr_sidebar_default_layout     = empty($_czr_sidebar_default_layout) ? $czr_sidebar_default_layout : $_czr_sidebar_default_layout;

      //builds the default layout option array including layout and article class
      $class_tab  = $global_layout[$czr_sidebar_default_layout];
      $class_tab  = $class_tab['content'];
      $czr_screen_layout             = array(
                  'sidebar' => $czr_sidebar_default_layout,
                  'class'   => $class_tab
      );

      //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
      $czr_specific_post_layout    = false;

      //if we are displaying an attachement, we use the parent post/page layout by default
      //=> but if the attachment has a layout, it will win.
      if ( isset($post) && is_singular() && 'attachment' == $post->post_type ) {
            $czr_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
            if ( ! $czr_specific_post_layout ) {
                $czr_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
            }
      }

      //for a singular post or page OR for the posts page
      elseif ( $is_singular_layout || is_singular() || czr_fn_is_attachment_image() || $wp_query -> is_posts_page ) {
            $czr_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
      }

      //checks if we display home page, either posts or static page and apply the customizer option
      global $wp_the_query;
      if( ($wp_the_query->is_home() && 'posts' == get_option( 'show_on_front' ) ) || $wp_the_query->is_front_page()) {
            $czr_specific_post_layout = czr_fn_opt('tc_front_layout');
      }

      if ( $czr_specific_post_layout ) {

            $class_tab  = $global_layout[$czr_specific_post_layout];
            $class_tab  = $class_tab['content'];
            $czr_screen_layout = array(
                'sidebar' => $czr_specific_post_layout,
                'class'   => $class_tab
            );

      }

      return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
}

/**
* This function returns the column content wrapper class
*
* @package Customizr
*/
function czr_fn_get_page_wrapper_class() {
    if ( 'boxed' == esc_attr( czr_fn_opt( 'tc_site_layout') ) ) {
        $tc_page_wrap_class = array( 'container', 'czr-boxed' );
    } else {
        $tc_page_wrap_class = array();
    }

    return apply_filters( 'czr_page_wrapper_class' , $tc_page_wrap_class );
}


/**
* This function returns the column content wrapper class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_column_content_wrapper_class() {
    return apply_filters( 'czr_column_content_wrapper_classes' , array( 'flex-row', 'row', 'column-content-wrapper') );
}


/**
* This function returns the main container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_main_container_class() {
    return apply_filters( 'czr_main_container_classes' , array('container') );
}

/**
* This function returns the article container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_article_container_class() {
    return apply_filters( 'czr_article_container_class' , array( czr_fn_get_layout( czr_fn_get_id() , 'class' ) , 'article-container' ) );
}




/**
 * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
 *
 * @package Customizr
 * @since Customizr 2.0.7
 */
function czr_fn_fancybox_content_filter( $content) {
    $tc_fancybox = esc_attr( czr_fn_opt( 'tc_fancybox' ) );

    if ( 1 != $tc_fancybox )
      return $content;

    global $post;
    if ( ! isset($post) )
      return $content;

    //same as smartload ones
    $allowed_image_extentions = apply_filters( 'tc_lightbox_allowed_img_extensions', array(
      'bmp',
      'gif',
      'jpeg',
      'jpg',
      'jpe',
      'tif',
      'tiff',
      'ico',
      'png',
      'svg',
      'svgz'
    ) );

    if ( empty( $allowed_image_extentions ) || ! is_array( $allowed_image_extentions ) ) {
      return $content;
    }


    $img_extensions_pattern = sprintf( "(?:%s)", implode( '|', $allowed_image_extentions ) );
    $pattern                = '#<a([^>]+?)href=[\'"]?([^\'"\s>]+\.'.$img_extensions_pattern.'[^\'"\s>]*)[\'"]?([^>]*)>#i';


    $replacement = '<a$1href="$2" data-lb-type="grouped-post"$3>';

    $r_content   = preg_replace( $pattern, $replacement, $content);

    $content     = $r_content ? $r_content : $content;

    return apply_filters( 'czr_fancybox_content_filter', $content );
}



//hook : czr_dev_notice
function czr_fn_print_r($message) {
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
      return;
    ?>
      <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
    <?php
}




/* FMK MODEL / VIEW / COLLECTION HELPERS */
function czr_fn_stringify_array( $array, $sep = ' ' ) {
    if ( is_array( $array ) )
      $array = join( $sep, array_unique( array_filter( $array ) ) );
    return $array;
}


//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) ) {

        /* PHP 5.3- compliant*/
        $class_vars = get_class_vars( $cb[0] );

        if ( isset( $class_vars[ 'instance' ] ) && method_exists( $class_vars[ 'instance' ], $cb[1]) ) {
          $to_return = call_user_func( array( $class_vars[ 'instance' ] ,  $cb[1] ), $params );
        }

        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func( array( $_class_obj, $cb[1] ), $params );
        }
      }
    }
    else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func($cb, $params);
    }

    if ( $return )
      return $to_return;
}


function czr_fn_return_cb_result( $cb, $params = array() ) {
    return czr_fn_fire_cb( $cb, $params, $return = true );
}




/* Same as helpers above but passing the param argument as an exploded array of params*/
//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb_array( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) ) {

        /* PHP 5.3- compliant*/
        $class_vars = get_class_vars( $cb[0] );

        if ( isset( $class_vars[ 'instance' ] ) && method_exists( $class_vars[ 'instance' ], $cb[1]) ) {
          $to_return = call_user_func_array( array( $class_vars[ 'instance' ] ,  $cb[1] ), $params );
        }

        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      }
    }
    else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func_array($cb, $params);
    }

    if ( $return )
      return $to_return;
}

function czr_fn_return_cb_result_array( $cb, $params = array() ) {
    return czr_fn_fire_cb_array( $cb, $params, $return = true );
}




/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  boolean
*
*/
function czr_fn_is_slider_active( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //is the slider set to on for the queried id?
  if ( czr_fn_is_real_home() && czr_fn_opt( 'tc_front_slider' ) )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );

  $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );

  if ( ! empty( $_slider_on ) && $_slider_on )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );

  return apply_filters( 'czr_slider_active_status', false , $queried_id );
}

/**
* helper
* returns the slider name id
* @return  string
*
*/
function czr_fn_get_current_slider( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //gets the current slider id
  $_home_slider     = czr_fn_opt( 'tc_front_slider' );
  $slider_name_id   = ( czr_fn_is_real_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
  return apply_filters( 'czr_slider_name_id', $slider_name_id , $queried_id );
}


function czr_fn_post_has_title() {
    return ! in_array(
      get_post_format(),
      apply_filters( 'czr_post_formats_with_no_heading', array( 'aside' , 'status' , 'link' , 'quote' ) )
    );
}

/* TODO: caching system */
function czr_fn_get_logo_atts( $logo_type = '', $backward_compatibility = true ) {
    $logo_type_sep      = $logo_type ? '_sticky_' : '_';

    $_cache_key         = "czr{$logo_type_sep}logo_atts";
    $_logo_atts         = wp_cache_get( $_cache_key );

    if ( false !== $_logo_atts ) {
      return $_logo_atts;
    }

    $_logo_atts = array();

    $accepted_formats   = apply_filters( 'czr_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );

    //check if the logo is a path or is numeric
    //get src for both cases
    $_logo_src          = '';
    $_width             = false;
    $_height            = false;
    $_attachment_id     = false;
    //for the standard logo use the wp custom logo feature if set, otherwise fall back on the customizr custom logo
    $_logo_option       = '' === $logo_type ? get_theme_mod( 'custom_logo', '' ) : '';
    $_logo_option       = $_logo_option ? $_logo_option : esc_attr( czr_fn_opt( "tc{$logo_type_sep}logo_upload") );
    //check if option is an attachement id or a path (for backward compatibility)
    if ( is_numeric($_logo_option) ) {
      $_attachment_id   = $_logo_option;
      $_attachment_data = apply_filters( "tc{$logo_type_sep}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
      $_logo_src        = $_attachment_data[0];
      $_width           = ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
      $_height          = ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
    } elseif ( $backward_compatibility ) { //old treatment
      //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
      $upload_dir       = wp_upload_dir();
      $_saved_path      = esc_url ( czr_fn_opt( "tc{$logo_type_sep}logo_upload") );
      $_logo_src        = ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
    }
    //hook + makes ssl compliant
    $_logo_src          = apply_filters( "tc{$logo_type_sep}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;
    $filetype           = czr_fn_check_filetype ($_logo_src);

    if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
      $_logo_atts = array(
                'logo_src'           => $_logo_src,
                'logo_attachment_id' => $_attachment_id,
                'logo_width'         => $_width,
                'logo_height'        => $_height,
                'logo_type'          => trim($logo_type_sep,'_')
      );

    //cache this
    wp_cache_set( $_cache_key, $_logo_atts );

    return $_logo_atts;
}



/**
 * Check on if there's any header/sidenav menu location assigned
 * Locations are:
 * 1) primary navbar menu, or sidenav menu => 'main'
 * 2) topbar menu => 'topbar'
 * 2) secondary navbar menu => 'secondary'
 * @return bool
 */
function czr_fn_is_there_any_visible_menu_location_assigned() {

    $menu_locations = array(
        //location => condition
        'main'        => true,
        'topbar'      => 1 == czr_fn_opt( 'tc_header_desktop_topbar' ),
        'secondary'   => czr_fn_is_secondary_menu_enabled()
    );

    $menu_assigned = false;

    foreach ( $menu_locations as $menu_location => $condition ) {
        if ( ! ( $condition && has_nav_menu( $menu_location ) ) ) {
            continue;
        }

        $menu_assigned = true;
        break;
    }

    return $menu_assigned;
}



function czr_fn_li_wrap( $el, $attrs = '' ) {
  return "<li $attrs>$el</li>";
}










//back compat
if ( ! class_exists( 'CZR_utils' ) ) :
  class CZR_utils {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $inst;
    static $instance;

    function __construct () {
      self::$inst =& $this;
      self::$instance =& $this;
    }

    /**
    * Returns an option from the options array of the theme.
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_opt( $option_name , $option_group = null, $use_default = true ) {
      return czr_fn_opt( $option_name, $option_group, $use_default );
    }

    //used until FPU 2.0.33
    function czr_fn_parse_imgs( $_html ) {
      return czr_fn_parse_imgs( $_html );
    }
  }

  new CZR_utils;

endif;

?><?php
/**
* Query related functions
*/





/**
* hook : body_class
* @return  array of classes
*
* @package Customizr
* @since Customizr 3.3.2
*/
function czr_fn_set_post_list_context_class( $_class ) {
    if ( czr_fn_is_list_of_posts() )
      array_push( $_class , 'czr-post-list-context');
    return $_class;
}





/******************************
VARIOUS QUERY HELPERS
*******************************/



function czr_fn_get_query_context() {
    if ( is_page() )
        return 'page';
    if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
    if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
    if ( !is_404() && ! czr_fn_is_home_empty() )
        return 'archive';

    return false;
}

function czr_fn_is_single_post() {
    global $post;
    return apply_filters( 'czr_is_single_post', isset($post)
        && is_singular()
        && 'page' != $post -> post_type
        && ! czr_fn_is_attachment_image()
        && ! czr_fn_is_home_empty()
        && ! czr_fn_is_real_home()
        );
}

function czr_fn_is_single_attachment_image() {
    global $post;
    return apply_filters( 'czr_is_single_attachment_image',
        ! ( ! isset($post) || empty($post) || ! czr_fn_is_attachment_image() || !is_singular() ) );
}

function czr_fn_is_single_attachment() {
    global $post;
    return apply_filters( 'czr_is_single_attachment',
        ! ( ! isset($post) || empty($post) || 'attachment' != $post -> post_type || !is_singular() ) );
}

function czr_fn_is_single_page() {
    return apply_filters( 'czr_is_single_page',
        'page' == czr_fn_get_post_type()
        && is_singular()
        && ! czr_fn_is_home_empty()
    );
}




/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  number
*
*/
function czr_fn_get_real_id() {
    global $wp_query;
    $queried_id  = czr_fn_get_id();
    return apply_filters( 'czr_get_real_id', ( ! czr_fn_is_real_home() && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
}



/**
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_post_list_article_selectors( $post_class = '', $id_suffix = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                  = '';

    if ( isset($post) )
      $selectors = apply_filters( "czr_post_list_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'post', $post->ID, $id_suffix ),
        czr_fn_get_the_post_class( $post_class )
      ) );

    return apply_filters( 'czr_article_selectors', $selectors );
}//end of function






/**
* @override
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_singular_article_selectors( $post_class = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                   = '';

    // SINGLE POST/ATTACHMENT
    if ( isset($post) ) {
      $post_type  = czr_fn_get_post_type();
      $selectors  = apply_filters( "czr_article_singular_{$post_type}_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'page' == $post_type ? $post_type : 'post', $post->ID ),
        czr_fn_get_the_post_class( $post_class )
      ) );
    }

    return apply_filters( 'czr_article_selectors', $selectors );

}//end of function


/**
* Returns the classes for the post div.
*
* @param string|array $class One or more classes to add to the class list.
* @param int $post_id An optional post ID.
* @package Customizr
* @since 3.0.10
*/
function czr_fn_get_the_post_class( $class = '', $post_id = null ) {
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
* Returns the classes for the post div.
*
* @param string $type Optional. post type. Default 'post' .
* @param int $post_id An optional post ID.
* @param string $id_suffix An optional suffix.
* @package Customizr
* @since 3.0.10
*/
function czr_fn_get_the_post_id( $type = 'post', $post_id = null, $id_suffix = '' ) {
    //Separates classes with a single space, collates classes for post DIV
    return sprintf( 'id="%1$s-%2$s%3$s"', $type, $post_id, $id_suffix );
}

/**
* Returns whether or not the current wp_query post is the first one
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_start() {
    global $wp_query;
    return  0 == $wp_query -> current_post;
}

/**
* Returns whether or not the current wp_query post is the latest one
*
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_end() {
    global $wp_query;
    return $wp_query -> current_post == $wp_query -> post_count -1;
}

?><?php
/**
* Posts thumbnails functions
*/
/**********************
* THUMBNAIL MODELS
**********************/
/**
* Gets the thumbnail or the first images attached to the post if any
* inside loop
* @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_thumbnail_model( $args = array() ) {

    $defaults = array(
      'requested_size'            => null,
      'post_id'                   => null,
      'custom_thumb_id'           => null,
      'enable_wp_responsive_imgs' => true,
      'filtered_thumb_size_name'  => null,
      'placeholder'               => false
    );

    $args = wp_parse_args( $args, $defaults);
    extract( $args );

    //czr_fn_has_thumb() checks if there is a thumbnail or an attachment img ( typically img embedded in single post ) that we can use
    //=> the check on the attachement is done if true == czr_fn_opt( 'tc_post_list_use_attachment_as_thumb' )
    if ( ! czr_fn_has_thumb( $post_id, $custom_thumb_id ) ) {
      if ( ! $placeholder )
        return array();
      else
        return array( 'tc_thumb' => czr_fn_get_placeholder_thumb(), 'is_placeholder' => true );
    }

    $tc_thumb_size              = is_null($requested_size) ? apply_filters( 'czr_thumb_size_name' , 'tc-thumb' ) : $requested_size;
    $post_id                    = is_null($post_id) ? get_the_ID() : $post_id;

    $filtered_thumb_size_name   = ! is_null( $filtered_thumb_size_name ) ? $filtered_thumb_size_name : 'tc_thumb_size';
    $_filtered_thumb_size       = apply_filters( $filtered_thumb_size_name, $filtered_thumb_size_name ? CZR___::$instance -> $filtered_thumb_size_name : null );

    $_model                     = array();
    $_img_attr                  = array();
    $tc_thumb_height            = '';
    $tc_thumb_width             = '';

    //when null set it as the image setting for reponsive thumbnails (default)
    //because this method is also called from the slider of posts which refers to the slider responsive image setting
    //limit this just for wp version >= 4.4
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      $enable_wp_responsive_imgs = is_null( $enable_wp_responsive_imgs ) ? 1 == czr_fn_opt('tc_resp_thumbs_img') : $enable_wp_responsive_imgs;

    //try to extract $_thumb_id and $_thumb_type
    extract( czr_fn_maybe_set_and_get_thumb_info( $post_id, $custom_thumb_id ) );
    if ( ! apply_filters( 'tc_has_thumb_info', isset($_thumb_id) && false != $_thumb_id && ! is_null($_thumb_id) ) )
      return array();

    //Try to get the image
    $image                      = wp_get_attachment_image_src( $_thumb_id, $tc_thumb_size);
    if ( ! apply_filters('tc_has_wp_thumb_image', ! empty( $image[0] ) ) )
        return array();

    //check also if this array value isset. (=> JetPack photon bug)
    if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size )
      $tc_thumb_size          = 'large';
    if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
      $tc_thumb_size          = 'slider';

    $_img_attr['class']     = sprintf(
      'attachment-%1$s tc-thumb-type-%2$s czr-img',
      $tc_thumb_size ,
      $_thumb_type
    );
    //Add the style value
    $_style                 = apply_filters( 'czr_post_thumb_inline_style' , '', $image, $_filtered_thumb_size );
    if ( $_style )
      $_img_attr['style']   = $_style;
    $_img_attr              = apply_filters( 'czr_post_thumbnail_img_attributes' , $_img_attr );

    //we might not want responsive images
    if ( false === $enable_wp_responsive_imgs ) {
      //trick, will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
      //only when the passed srcset attr is not empty. This will avoid us to:
      //a) add a filter to get rid of already computed srcset
      // or
      //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
      //Side effect:
      //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
      //to avoid this we filter the attributes getting rid of the srcset if any.
      //Basically this trick, even if ugly, will avoid the srcset attr computation
      $_img_attr['srcset']  = " ";
      add_filter( 'wp_get_attachment_image_attributes', 'czr_fn_remove_srcset_attr' );
    }
    //get the thumb html
    if ( is_null($custom_thumb_id) && has_post_thumbnail( $post_id ) ) {
      //get_the_post_thumbnail( $post_id, $size, $attr )
      $tc_thumb = get_the_post_thumbnail( $post_id , $tc_thumb_size , $_img_attr);
    }
    else {
      //wp_get_attachment_image( $attachment_id, $size, $icon, $attr )
      $tc_thumb = wp_get_attachment_image( $_thumb_id, $tc_thumb_size, false, $_img_attr );
    }

    //get height and width if not empty
    if ( ! empty($image[1]) && ! empty($image[2]) ) {
      $tc_thumb_height        = $image[2];
      $tc_thumb_width         = $image[1];
    }
    //used for smart load when enabled
    $tc_thumb = apply_filters( 'czr_thumb_html', $tc_thumb, $requested_size, $post_id, $custom_thumb_id, $_img_attr, $tc_thumb_size );

    return apply_filters( 'czr_get_thumbnail_model',
      isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width", "_thumb_id" ) : array(),
      $post_id,
      $_thumb_id,
      $enable_wp_responsive_imgs
    );
}



/**
* inside loop
* @return array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_maybe_set_and_get_thumb_info( $_post_id = null, $_thumb_id = null ) {
    $_post_id     = is_null($_post_id) ? get_the_ID() : $_post_id;
    $_meta_thumb  = get_post_meta( $_post_id , 'tc-thumb-fld', true );
    //get_post_meta( $post_id, $key, $single );
    //always refresh the thumb meta if user logged in and current_user_can('upload_files')
    //When do we refresh ?
    //1) empty( $_meta_thumb )
    //2) is_user_logged_in() && current_user_can('upload_files')
    $_refresh_bool = empty( $_meta_thumb ) || ! $_meta_thumb;
    $_refresh_bool = ! isset($_meta_thumb["_thumb_id"]) || ! isset($_meta_thumb["_thumb_type"]);
    $_refresh_bool = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : $_refresh_bool;
    //if a custom $_thumb_id is requested => always refresh
    $_refresh_bool = ! is_null( $_thumb_id ) ? true : $_refresh_bool;

    if ( ! $_refresh_bool )
      return $_meta_thumb;

    return czr_fn_set_thumb_info( $_post_id , $_thumb_id, true );
}

/**************************
* EXPOSED HELPERS / SETTERS
**************************/
/*
* @return bool
*/
function czr_fn_has_thumb( $_post_id = null , $_thumb_id = null ) {
    $_post_id  = is_null($_post_id) ? get_the_ID() : $_post_id;

    //try to extract (OVERWRITE) $_thumb_id and $_thumb_type
    extract( czr_fn_maybe_set_and_get_thumb_info( $_post_id, $_thumb_id ) );
    return apply_filters( 'tc_has_thumb', wp_attachment_is_image($_thumb_id) && isset($_thumb_id) && false != $_thumb_id && ! empty($_thumb_id) );
}


/**
* update the thumb meta and maybe return the info
* also fired from admin on save_post
* @param post_id and (bool) return
* @return void or array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_set_thumb_info( $post_id = null , $_thumb_id = null, $_return = false ) {
    $post_id      = is_null($post_id) ? get_the_ID() : $post_id;
    $_thumb_type  = 'none';

    //IF a custom thumb id is requested
    if ( ! is_null( $_thumb_id ) && false !== $_thumb_id ) {
      $_thumb_type  = false !== $_thumb_id ? 'custom' : $_thumb_type;
    }
    //IF no custom thumb id :
    //1) check if has thumbnail
    //2) check attachements
    //3) default thumb
    else {
      if ( has_post_thumbnail( $post_id ) ) {
        $_thumb_id    = get_post_thumbnail_id( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'thumb' : $_thumb_type;
      } else {
        $_thumb_id    = czr_fn_get_id_from_attachment( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'attachment' : $_thumb_type;
      }
    }
    $_thumb_id = ( ! $_thumb_id || empty($_thumb_id) || ! is_numeric($_thumb_id) ) ? false : $_thumb_id;

    //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
    update_post_meta( $post_id , 'tc-thumb-fld', compact( "_thumb_id" , "_thumb_type" ) );
    if ( $_return )
      return apply_filters( 'czr_set_thumb_info' , compact( "_thumb_id" , "_thumb_type" ), $post_id );
}//end of fn


function czr_fn_get_id_from_attachment( $post_id ) {
    //define a filtrable boolean to set if attached images can be used as thumbnails
    //1) must be a non single post context
    //2) user option should be checked in customizer
    $_bool = 0 != esc_attr( czr_fn_opt( 'tc_post_list_use_attachment_as_thumb' ) );

    if ( ! is_admin() )
      $_bool = ! czr_fn_is_single_post() && $_bool;

    if ( ! apply_filters( 'czr_use_attachment_as_thumb' , $_bool ) )
      return;

    //Case if we display a post or a page
    if ( 'attachment' != get_post_type( $post_id ) ) {
      //look for the last attached image in a post or page
      $tc_args = apply_filters('czr_attachment_as_thumb_query_args' , array(
          'numberposts'             =>  1,
          'post_type'               =>  'attachment',
          'post_status'             =>  null,
          'post_parent'             =>  $post_id,
          'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' ),
          'orderby'                 => 'post_date',
          'order'                   => 'DESC'
        )
      );
      $attachments              = get_posts( $tc_args );
    }

    //case were we display an attachment (in search results for example)
    elseif ( 'attachment' == get_post_type( $post_id ) && wp_attachment_is_image( $post_id ) ) {
      $attachments = array( get_post( $post_id ) );
    }

    if ( ! isset($attachments) || empty($attachments ) )
      return;
    return isset( $attachments[0] ) && isset( $attachments[0] -> ID ) ? $attachments[0] -> ID : false;
}//end of fn



/**********************
* THUMBNAIL VIEW
**********************/
/**
* Display or return the thumbnail view
* @param : thumbnail model (img, width, height), layout value, echo bool
* @package Customizr
* @since Customizr 3.0.10
*/
function czr_fn_render_thumb_view( $_thumb_model , $layout = 'span3', $_echo = true ) {
    if ( empty( $_thumb_model ) )
      return;
    //extract "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
    extract( $_thumb_model );
    $thumb_img        = ! isset( $_thumb_model) ? false : $tc_thumb;
    $thumb_img        = apply_filters( 'czr_post_thumb_img', $thumb_img, czr_fn_get_id() );
    if ( ! $thumb_img )
      return;

    //handles the case when the image dimensions are too small
    $thumb_size       = apply_filters( 'czr_thumb_size' , CZR___::$instance -> tc_thumb_size, czr_fn_get_id()  );
    $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
    $no_effect_class  = ( esc_attr( czr_fn_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

    //default hover effect
    $thumb_wrapper    = sprintf('<div class="%4$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s"></a>%3$s</div>',
                                  implode( " ", apply_filters( 'czr_thumbnail_link_class', array( $no_effect_class ) ) ),
                                  get_permalink( get_the_ID() ),
                                  $thumb_img,
                                  implode( " ", apply_filters( 'czr_thumb_wrapper_class', array('thumb-wrapper') ) )
    );

    $thumb_wrapper    = apply_filters_ref_array( 'czr_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, czr_fn_get_id() ) );

    //cache the thumbnail view
    $html             = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
      apply_filters( 'czr_post_thumb_class', $layout ),
      $thumb_wrapper
    );
    $html = apply_filters_ref_array( 'czr_render_thumb_view', array( $html, $_thumb_model, $layout ) );
    if ( ! $_echo )
      return $html;
    echo $html;
}//end of function



/* ------------------------------------------------------------------------- *
*  Placeholder thumbs for preview demo mode
/* ------------------------------------------------------------------------- */
/* Echoes the <img> tag of the placeholder thumbnail
*  + an animated svg icon
*  the src property can be filtered
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_get_placeholder_thumb' ) ) {
  function czr_fn_get_placeholder_thumb( $_requested_size = 'thumb-standard' ) {
    $_unique_id = uniqid();
    $filter = false;

    $_sizes = array( 'thumb-medium', 'thumb-small', 'thumb-standard' );
    if ( ! in_array($_requested_size, $_sizes) )
      $_requested_size = 'thumb-medium';

    //default $img_src
    $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_requested_size}.png" );
    if ( apply_filters( 'czr-use-svg-thumb-placeholder', true ) ) {
        $_size = $_requested_size . '-empty';
        $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_size}.png" );
        $_svg_height = in_array($_size, array( 'thumb-medium', 'thumb-standard' ) ) ? 100 : 60;
        ob_start();
        ?>
        <svg class="czr-svg-placeholder <?php echo $_size; ?>" id="<?php echo $_unique_id; ?>" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M928 832q0-14-9-23t-23-9q-66 0-113 47t-47 113q0 14 9 23t23 9 23-9 9-23q0-40 28-68t68-28q14 0 23-9t9-23zm224 130q0 106-75 181t-181 75-181-75-75-181 75-181 181-75 181 75 75 181zm-1024 574h1536v-128h-1536v128zm1152-574q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5zm-1024-642h384v-128h-384v128zm-128 192h1536v-256h-828l-64 128h-644v128zm1664-256v1280q0 53-37.5 90.5t-90.5 37.5h-1536q-53 0-90.5-37.5t-37.5-90.5v-1280q0-53 37.5-90.5t90.5-37.5h1536q53 0 90.5 37.5t37.5 90.5z"/></svg>
        <?php
        $_svg_placeholder = ob_get_clean();
    }
    $_img_src = apply_filters( 'czr_placeholder_thumb_src', $_img_src, $_requested_size );
    $filter = apply_filters( 'czr_placeholder_thumb_filter', false );
    //make sure we did not lose the img_src
    if ( false == $_img_src )
      $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "front/img/{$_requested_size}.png" );

    $thumb_to_sizes = array(
      //thumb => width, height
      'thumb-medium' => array( '520', '245' ),
      'thumb-small' => array( '160', '160' ),
      'thumb-standard' => array( '300', '300' )
    );

    $attr = array(
      'class'             => 'czr-img-placeholder',
      'src'               => $_img_src,
      'alt'               => trim( strip_tags( get_the_title() ) ),
      'data-czr-post-id'  => $_unique_id,
      //$_requested_size is always forced to have a value present in this array $_sizes = array( 'thumb-medium', 'thumb-small', 'thumb-standard' );
      'width'             => $thumb_to_sizes[$_requested_size][0],
      'height'            => $thumb_to_sizes[$_requested_size][1]
    );

    $attr = apply_filters( 'czr_placeholder_image_attributes', $attr );
    $attr = array_filter( array_map( 'esc_attr', $attr ) );

    return sprintf( '%1$s%2$s<img class="%3$s" src="%4$s" alt="%5$s" data-czr-post-id="%6$s" width="%7$s" height="%8$s"/>',
      isset($_svg_placeholder) ? $_svg_placeholder : '',
      false !== $filter ? $filter : '',
      isset( $attr[ 'class' ] ) ? $attr[ 'class' ] : '',
      isset( $attr[ 'src' ] ) ? $attr[ 'src' ] : '',
      isset( $attr[ 'alt' ] ) ? $attr[ 'alt' ] : '',
      isset( $attr[ 'data-czr-post-id' ] ) ? $attr[ 'data-czr-post-id' ] : '',
      isset( $attr[ 'width' ] ) ? $attr[ 'width' ] : '',
      isset( $attr[ 'height' ] ) ? $attr[ 'height' ] : ''
    );
  }
}

/**********************
* HELPER CALLBACK
**********************/
/**
* hook wp_get_attachment_image_attributes
* Get rid of the srcset attribute (responsive images)
* @param $attr array of image attributes
* @return  array of image attributes
*
* @package Customizr
* @since Customizr 3.4.16
*/
function czr_fn_remove_srcset_attr( $attr ) {
    if ( isset( $attr[ 'srcset' ] ) ) {
      unset( $attr['srcset'] );
      //to ensure a "local" removal we have to remove this filter callback, so it won't hurt
      //responsive images sitewide
      remove_filter( current_filter(), 'czr_fn_remove_srcset_attr' );
    }
    return $attr;
}

?><?php
/*  Darken hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hex' ) ) {
   function czr_fn_darken_hex( $hex, $percent, $make_prop_value = true ) {

      $hsl      = czr_fn_hex2hsl( $hex );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $dark_hsl, $make_prop_value );
   }
}

/*  Lighten hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hex' ) ) {

   function czr_fn_lighten_hex($hex, $percent, $make_prop_value = true) {

      $hsl       = czr_fn_hex2hsl( $hex );

      $light_hsl   = czr_fn_lighten_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $light_hsl, $make_prop_value );
   }
}

/*  Darken rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_rgb' ) ) {
   function czr_fn_darken_rgb( $rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2rgb( $dark_hsl, $array, $make_prop_value );
   }
}

/*  Lighten rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_rgb' ) ) {

   function czr_fn_lighten_rgb($rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $light_hsl = czr_fn_lighten_hsl( $light_hsl, $percent );

      return czr_fn_hsl2rgb( $light_hsl, $array, $make_prop_value );

   }
}



/* Darken/Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hsl' ) ) {
   function czr_fn_darken_hsl( $hsl, $percentage, $array = true ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) - $percentage;
      $hsl[2] = ( $hsl[2] < 0 ) ? 0: $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/* Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hsl' ) ) {
   function czr_fn_lighten_hsl( $hsl, $percentage, $array = true  ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) + $percentage;
      $hsl[2] = ( $hsl[2] > 100 ) ? 1 : $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}



/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgb' ) ) {
   function czr_fn_hex2rgb( $hex, $array = false, $make_prop_value = false ) {

      $hex = trim( $hex, '# ' );

      if ( 3 == strlen( $hex ) ) {

         $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
         $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
         $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );

      }
      else {

         $r = hexdec( substr( $hex, 0, 2 ) );
         $g = hexdec( substr( $hex, 2, 2 ) );
         $b = hexdec( substr( $hex, 4, 2 ) );

      }

      $rgb = array( $r, $g, $b );

      if ( !$array ) {

         $rgb = implode( ",", $rgb );
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;

      }

      return $rgb;
  }
}

/*  Convert hexadecimal to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgba' ) ) {
   function czr_fn_hex2rgba( $hex, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb = $rgba = czr_fn_hex2rgb( $hex, $_array = true );

      $rgba[]     = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*  Convert rgb to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2rgba' ) ) {
   function czr_fn_rgb2rgba( $rgb, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb   = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb   = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb   = $rgba = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $rgba[] = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*
* Following heavily based on
* https://github.com/mexitek/phpColors
* MIT License
*/
/*  Convert  rgb to hexadecimal
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hex' ) ) {
   function czr_fn_rgb2hex( $rgb, $make_prop_value = false ) {

      $rgb = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      // Convert RGB to HEX
      $hex[0] = str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
      $hex[1] = str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
      $hex[2] = str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

      $hex = implode( '', $hex );

      return $make_prop_value ? "#{$hex}" : $hex;
   }
}

/*
* heavily based on
* phpColors
*/

/*  Convert rgb to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hsl' ) ) {
   function czr_fn_rgb2hsl( $rgb, $array = false ) {

      $rgb       = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb       = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb       = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $deltas    = array();

      $RGB       = array(
         'R'   => ( $rgb[0] / 255 ),
         'G'   => ( $rgb[1] / 255 ),
         'B'   => ( $rgb[2] / 255 )
      );


      $min       = min( array_values( $RGB ) );
      $max       = max( array_values( $RGB ) );
      $span      = $max - $min;

      $H = $S    = 0;
      $L         = ($max + $min)/2;

      if ( 0 != $span ) {

         if ( $L < 0.5 ) {
            $S = $span / ( $max + $min );
         }
         else {
            $S = $span / ( 2 - $max - $min );
         }

         foreach ( array( 'R', 'G', 'B' ) as $var ) {
            $deltas[$var] = ( ( ( $max - $RGB[$var] ) / 6 ) + ( $span / 2 ) ) / $span;
         }

         if ( $max == $RGB['R'] ) {
            $H = $deltas['B'] - $deltas['G'];
         }
         else if ( $max == $RGB['G'] ) {
            $H = ( 1 / 3 ) + $deltas['R'] - $deltas['B'];
         }
         else if ( $max == $RGB['B'] ) {
            $H = ( 2 / 3 ) + $deltas['G'] - $deltas['R'];
          }

         if ($H<0) {
            $H++;
         }

         if ($H>1) {
            $H--;
         }
      }

      $hsl = array( $H*360, $S, $L );


      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/*  Convert hsl to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2rgb' ) ) {

   function czr_fn_hsl2rgb( $hsl, $array=false, $make_prop_value = false ) {

      list($H,$S,$L) = array( $hsl[0]/360, $hsl[1], $hsl[2] );

      $rgb           = array_fill( 0, 3, $L * 255 );

      if ( 0 !=$S ) {

         if ($L < 0.5 ) {

            $var_2 = $L * ( 1 + $S );

         } else {

            $var_2 = ( $L + $S ) - ( $S * $L );

         }

         $var_1  = 2 * $L - $var_2;

         $rgb[0] = czr_fn_hue2rgbtone( $var_1, $var_2, $H + ( 1/3 ) );
         $rgb[1] = czr_fn_hue2rgbtone( $var_1, $var_2, $H );
         $rgb[2] = czr_fn_hue2rgbtone( $var_1, $var_2, $H - ( 1/3 ) );
      }

      if ( !$array ) {
         $rgb = implode(",", $rgb);
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;
      }

      return $rgb;
   }
}

/* Convert hsl to hex
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2hex' ) ) {
   function czr_fn_hsl2hex( $hsl = array(), $make_prop_value = false ) {
      $rgb = czr_fn_hsl2rgb( $hsl, $array = true );

      return czr_fn_rgb2hex( $rgb, $make_prop_value );
   }
}

/* Convert hex to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2hsl' ) ) {
   function czr_fn_hex2hsl( $hex ) {
      $rgb = czr_fn_hex2rgb( $hex, true );

      return czr_fn_rgb2hsl( $rgb, true );
   }
}

/* Convert hue to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hue2rgbtone' ) ) {
   function czr_fn_hue2rgbtone( $v1, $v2, $vH ) {
      $_to_return = $v1;

      if( $vH < 0 ) {
         $vH += 1;
      }
      if( $vH > 1 ) {
         $vH -= 1;
      }

      if( (6*$vH) < 1 ) {
         $_to_return = ($v1 + ($v2 - $v1) * 6 * $vH);
      }
      elseif( (2*$vH) < 1 ) {
         $_to_return = $v2;
      }
      elseif( (3*$vH) < 2 ) {
         $_to_return = ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
      }

      return round( 255 * $_to_return );
   }
}


/* Returns the complementary hsl color
/* ------------------------------------ */
function czr_fn_rgb_invert( $rgb )  {
   // Adjust Hue 180 degrees
   //$hsl[0] += ($hsl[0]>180) ? -180:180;
   $rgb_inverted =  array(
      255 - $rgb[0],
      255 - $rgb[1],
      255 - $rgb[2]
   );

   return $rgb_inverted;
}

/* Returns the complementary hsl color
/* ------------------------------------ */
function czr_fn_hex_invert( $hex, $make_prop_value = true )  {
   $rgb           = czr_fn_hex2rgb( $hex, $array = true );
   $rgb_inverted  = czr_fn_rgb_invert( $rgb );

   return czr_fn_rgb2hex( $rgb_inverted, $make_prop_value );
}

?>