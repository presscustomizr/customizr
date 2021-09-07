<?php

/**
* The czr_fn__f() function is a wrapper of the WP built-in apply_filters() where the $value param becomes optional.
*
* By convention in Customizr, filter hooks are used as follow :
* 1) declared with add_filters in class constructors (mainly) to hook on WP built-in callbacks or create "getters" used everywhere
* 2) declared with apply_filters in methods to make the code extensible for developers
* 3) accessed with czr_fn__f() to return values (while front end content is handled with action hooks)
*
* Used everywhere in Customizr. Can pass up to five variables to the filter callback.
*
* @since Customizr 3.0
*/
if( !function_exists( 'czr_fn__f' ) ) :
    function czr_fn__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
        return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;

//This function is the only one with a different prefix.
//It has been kept in the theme for retro-compatibility.
if( !function_exists( 'tc__f' ) ) :
    function tc__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
        return czr_fn__f( $tag , $value, $arg_one, $arg_two , $arg_three, $arg_four, $arg_five );
    }
endif;

/*
 * @since 3.5.0
 */
if ( !function_exists( 'czr_fn_is_ms' ) ) {
      function czr_fn_is_ms() {
          $is_modern_style = true;
          //if defined( 'CZR_IS_MODERN_STYLE' ), declared in core/init.php, let's use it.
          if (  defined( 'CZR_IS_MODERN_STYLE' ) ) {
              $is_modern_style = CZR_IS_MODERN_STYLE;
          } else if ( !czr_fn_isprevdem() ) {
              $_czr_modern_style_option_value = czr_fn_opt( 'tc_style', CZR_THEME_OPTIONS, false );//false for no default

              switch ( $_czr_modern_style_option_value ) {
                case 'modern':
                    $is_modern_style = true;
                  break;

                case 'classic':
                    $is_modern_style = false;
                  break;

                default :
                  //'tc_style' OPTION STYLE NOT SET YET => WHAT DO WE DO ?
                  //1) FRESH INSTALL ( not an update )
                  //=> modern if free version is > 4.0 and pro > 2.0
                  //=> If pro fresh install, we know that the user started with the current pro version. But the user may have upgraded from free for which he/she started before 4.0
                  //  => that's why we need to know when he started with the free to decide
                  if ( CZR_IS_PRO ) {
                      //if user started using the pro before 2.0
                      if ( czr_fn_user_started_before_version( '4.0.0' , '2.0.0', 'pro' ) ) {
                          $is_modern_style = false;
                      } else {
                        if ( czr_fn_user_started_before_version( '4.0.0' , '2.0.0', 'free' ) ) {
                          $is_modern_style = false;
                        }
                      }
                  } else {
                      $is_modern_style = !czr_fn_user_started_before_version( '4.0.0' , '2.0.0', 'free' );
                  }
                  break;
              }

              if ( isset( $_GET['czr_ms'] ) && true == $_GET['czr_ms'] && !czr_fn_is_pro() ) {
                $is_modern_style = true;
              }
          }
          return apply_filters( 'czr_is_modern_style', $is_modern_style );
      }
}

if ( !function_exists( 'czr_fn_setup_constants' ) ):
    function czr_fn_setup_constants() {

        //fire an action hook before constants have been set up
        do_action( 'czr_before_setup_base_constants' );

        /* GETS INFORMATIONS FROM STYLE.CSS */
        // get themedata version wp 3.4+
        if ( function_exists( 'wp_get_theme' ) ) {
          //get WP_Theme object of customizr
          $tc_theme                     = wp_get_theme();

          //Get infos from parent theme if using a child theme
          $tc_theme = $tc_theme->parent() ? $tc_theme->parent() : $tc_theme;

          $tc_base_data['prefix']       = $tc_base_data['title'] = $tc_theme->name;
          $tc_base_data['version']      = $tc_theme->version;
          $tc_base_data['authoruri']    = $tc_theme->{'Author URI'};
        }

        // get themedata for lower versions (get_stylesheet_directory() points to the current theme root, child or parent)
        else {
             $tc_base_data                = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
             $tc_base_data['prefix']      = $tc_base_data['title'];
        }

        //CUSTOMIZR_VER is the Version
        if( !defined( 'CUSTOMIZR_VER' ) )            define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
        //CZR_BASE is the root server path of the parent theme
        if( !defined( 'CZR_BASE' ) )                 define( 'CZR_BASE' , get_template_directory().'/' );
        //CZR_UTILS_PREFIX is the relative path where the utils classes are located
        if( !defined( 'CZR_CORE_PATH' ) )            define( 'CZR_CORE_PATH' , 'core/' );
        //CZR_MAIN_TEMPLATES_PATH is the relative path where the czr4 WordPress templates are located
        if( !defined( 'CZR_MAIN_TEMPLATES_PATH' ) )  define( 'CZR_MAIN_TEMPLATES_PATH' , 'templates/' );
        //CZR_UTILS_PREFIX is the relative path where the utils classes are located
        if( !defined( 'CZR_UTILS_PATH' ) )           define( 'CZR_UTILS_PATH' , 'core/_dev/_utils/' );
        //CZR_FRAMEWORK_PATH is the relative path where the framework is located
        if( !defined( 'CZR_FRAMEWORK_PATH' ) )       define( 'CZR_FRAMEWORK_PATH' , 'core/_dev/_framework/' );
        //CZR_PHP_FRONT_PATH is the relative path where the framework front files are located
        if( !defined( 'CZR_PHP_FRONT_PATH' ) )       define( 'CZR_PHP_FRONT_PATH' , 'core/front/' );
        //CZR_ASSETS_PREFIX is the relative path where the assets are located
        if( !defined( 'CZR_ASSETS_PREFIX' ) )        define( 'CZR_ASSETS_PREFIX' , 'assets/' );
        //CZR_BASE_CHILD is the root server path of the child theme
        if( !defined( 'CZR_BASE_CHILD' ) )           define( 'CZR_BASE_CHILD' , get_stylesheet_directory().'/' );
        //CZR_BASE_URL http url of the loaded parent theme
        if( !defined( 'CZR_BASE_URL' ) )             define( 'CZR_BASE_URL' , get_template_directory_uri() . '/' );
        //CZR_BASE_URL_CHILD http url of the loaded child theme
        if( !defined( 'CZR_BASE_URL_CHILD' ) )       define( 'CZR_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' );

        //CZR_THEMENAME contains the Name of the currently loaded theme
        if( !defined( 'CZR_THEMENAME' ) )            define( 'CZR_THEMENAME' , $tc_base_data['title'] );

        if( !defined( 'CZR_SANITIZED_THEMENAME' ) )  define( 'CZR_SANITIZED_THEMENAME' , sanitize_file_name( strtolower($tc_base_data['title']) ) );

        //CZR_WEBSITE is the home website of Customizr
        if( !defined( 'CZR_WEBSITE' ) )              define( 'CZR_WEBSITE' , $tc_base_data['authoruri'] );
        //OPTION PREFIX //all customizr theme options start by "tc_" by convention (actually since the theme was created.. tc for Themes & Co...)
        if( !defined( 'CZR_OPT_PREFIX' ) )           define( 'CZR_OPT_PREFIX' , apply_filters( 'czr_options_prefixes', 'tc_' ) );
        //MAIN OPTIONS NAME
        if( !defined( 'CZR_THEME_OPTIONS' ) )        define( 'CZR_THEME_OPTIONS', apply_filters( 'czr_options_name', 'tc_theme_options' ) );
        //CZR_CZR_PATH is the relative path where the Customizer php is located
        if( !defined( 'CZR_CZR_PATH' ) )             define( 'CZR_CZR_PATH' , 'core/czr/' );
        //CZR_FRONT_ASSETS_URL http url of the front assets
        if( !defined( 'CZR_FRONT_ASSETS_URL' ) )     define( 'CZR_FRONT_ASSETS_URL' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/' );
        //if( !defined( 'CZR_OPT_AJAX_ACTION' ) )      define( 'CZR_OPT_AJAX_ACTION' , 'czr_fn_get_opt' );//DEPRECATED
        //IS PRO
        if( !defined( 'CZR_IS_PRO' ) )               define( 'CZR_IS_PRO' , czr_fn_is_pro() );

        //IS DEBUG MODE
        if( !defined( 'CZR_DEBUG_MODE' ) )           define( 'CZR_DEBUG_MODE', isset( $_GET['czr_debug'] ) && 1 == $_GET['czr_debug'] );

        //IS DEV MODE
        if( !defined( 'CZR_DEV_MODE' ) )             define( 'CZR_DEV_MODE', ( defined('CZR_DEV') && true === CZR_DEV ) );

        //REFRESH ASSETS MODE => Will load javascript assets with a timestamp
        if( !defined( 'CZR_REFRESH_ASSETS' ) )       define( 'CZR_REFRESH_ASSETS', ( isset( $_GET['czr_refresh'] ) && 1 == $_GET['czr_refresh'] ) );

        //retro compat for FPU and WFC plugins

        //TC_BASE_URL http url of the loaded parent theme (retro compat)
        if( !defined( 'TC_BASE' ) )            define( 'TC_BASE' , CZR_BASE );
        //TC_BASE_CHILD is the root server path of the child theme
        if( !defined( 'TC_BASE_CHILD' ) )      define( 'TC_BASE_CHILD' , CZR_BASE_CHILD );
        //TC_BASE_URL http url of the loaded parent theme (retro compat)
        if( !defined( 'TC_BASE_URL' ) )        define( 'TC_BASE_URL' , CZR_BASE_URL );
        //TC_BASE_URL_CHILD http url of the loaded child theme
        if( !defined( 'TC_BASE_URL_CHILD' ) )  define( 'TC_BASE_URL_CHILD' , CZR_BASE_URL_CHILD );
        if( !defined( 'PREV_REC_NOTICE_ID' ) )  define( 'PREV_REC_NOTICE_ID' , 'rec-notice-1119' );
        if( !defined( 'REC_NOTICE_ID' ) )  define( 'REC_NOTICE_ID' , 'rec-notice-0321' );

        //fire an action hook after constants have been set up
        do_action( 'czr_after_setup_base_constants' );
    }
endif;


//@return bool
function czr_fn_isprevdem() {
    global $wp_customize;
    $is_dirty = false;
    if ( is_object( $wp_customize ) && method_exists( $wp_customize, 'unsanitized_post_values' ) ) {
        $real_cust            = $wp_customize->unsanitized_post_values( array( 'exclude_changeset' => true ) );
        $_preview_index       = array_key_exists( 'customize_messenger_channel' , $_POST ) ? $_POST['customize_messenger_channel'] : '';
        $_is_first_preview    = false !== strpos( $_preview_index ,'-0' );
        $_doing_ajax_partial  = array_key_exists( 'wp_customize_render_partials', $_POST );
        //There might be cases when the unsanitized post values contains old widgets infos on initial preview load, giving a wrong dirtyness information
        $is_dirty             = ( !empty( $real_cust ) && !$_is_first_preview ) || $_doing_ajax_partial;
    }
    if ( empty( CZR_BASE::$cached_queries['template'] ) ) {
        CZR_BASE::$cached_queries['template'] = czr_fn_get_raw_option( 'template', null, false );
    }

    return apply_filters( 'czr_fn_isprevdem', !$is_dirty && CZR_BASE::$cached_queries['template'] != get_stylesheet() && !is_child_theme() && !czr_fn_is_pro() );
}

//@return bool
function czr_fn_is_pro_section_on() {
   return !CZR_IS_PRO && class_exists( 'CZR_Customize_Section_Pro' ) && !czr_fn_isprevdem();
}


//@return an array of unfiltered options
//=> all options or a single option val
if ( !( function_exists( 'czr_fn_get_raw_option' ) ) ) :
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null, $from_cache = true ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize( $alloptions );
    $alloptions = !is_array( $alloptions ) ? array() : $alloptions;//fixes https://github.com/presscustomizr/hueman/issues/492
    //is there any option group requested ?
    if ( !is_null( $opt_group ) && array_key_exists( $opt_group, $alloptions ) ) {
      $alloptions = maybe_unserialize( $alloptions[ $opt_group ] );
    }
    //shall we return a specific option ?
    if ( is_null( $opt_name ) ) {
        return $alloptions;
    } else {
        $opt_value = array_key_exists( $opt_name, $alloptions ) ? maybe_unserialize( $alloptions[ $opt_name ] ) : false;//fallback on cache option val
        //do we need to get the db value instead of the cached one ? <= might be safer with some user installs not properly handling the wp cache
        //=> typically used to checked the template name for czr_fn_isprevdem()
        if ( !$from_cache ) {
            global $wpdb;
            //@see wp-includes/option.php : get_option()
            $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $opt_name ) );
            if ( is_object( $row ) ) {
                $opt_value = $row->option_value;
            }
        }
        return $opt_value;
    }
}
endif;




if ( !( function_exists( 'czr_fn_get_unfiltered_theme_options' ) ) ) :
//@return an array of options
//This is mostly a copy of the built-in get_option with the difference that
//1) by default retrieves only the theme options
//2) removes the "pre_option_{$name}", "default_option_{$name}", "option_{$name}" filters
//3) doesn't care about the special case when $option in array array('siteurl', 'home', 'category_base', 'tag_base'),
//   as they are out of scope here
//
// The filter suppression is specially needed due to:
// a) avoid plugins (qtranslate, other lang plugins) filtering the theme options value, which might mess theme options when we update the options on front
// (e.g. to set the defaults, or to perform our retro compat options updates, or either to set the user started before option)
// b) speed up the theme option retrieval when we are sure we don't need the theme options to be filtered in any case
function czr_fn_get_unfiltered_theme_options( $option = null, $default = array() ) {
    $option           = is_null($option) ? CZR_THEME_OPTIONS : $option;

    global $wpdb;

    $option_group = trim( $option);

    if ( empty( $option ) )
        return false;

    if ( defined( 'WP_SETUP_CONFIG' ) )
        return false;

    if ( !wp_installing() ) {
        // prevent non-existent options from triggering multiple queries
        $notoptions = wp_cache_get( 'notoptions', 'options' );
        if ( isset( $notoptions[ $option ] ) ) {
            return $default;
        }

        $alloptions = wp_load_alloptions();

        if ( isset( $alloptions[$option] ) ) {
            $value = $alloptions[$option];
        } else {
            $value = wp_cache_get( $option, 'options' );

            if ( false === $value ) {
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );

                // Has to be get_row instead of get_var because of funkiness with 0, false, null values
                if ( is_object( $row ) ) {
                    $value = $row->option_value;
                    wp_cache_add( $option, $value, 'options' );
                } else { // option does not exist, so we must cache its non-existence
                    if ( !is_array( $notoptions ) ) {
                         $notoptions = array();
                    }
                    $notoptions[$option] = true;
                    wp_cache_set( 'notoptions', $notoptions, 'options' );

                    return $default;
                }
            }
        }
    } else {
        $suppress = $wpdb->suppress_errors();
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
        $wpdb->suppress_errors( $suppress );
        if ( is_object( $row ) ) {
            $value = $row->option_value;
        } else {
            return $default;
        }
    }

    return maybe_unserialize( $value );
}
endif;




/*-----------------------------------------------------------
/* PREVIOUSLY IN init.php and core/_utils/*.*
/*----------------------------------------------------------*/
//@return boolean
if ( !function_exists( 'czr_fn_is_partial_refreshed_on' ) ) {
  function czr_fn_is_partial_refreshed_on() {
    return apply_filters( 'tc_partial_refresh_on', true );
  }
}

/* HELPER FOR CHECKBOX OPTIONS */
//used in the customizer
//replace wp checked() function
if ( !function_exists( 'czr_fn_checked' ) ) {
  function czr_fn_checked( $val ) {
    echo $val ? 'checked="checked"' : '';
  }
}


/**
* helper
* @return  bool
*/
if ( !function_exists( 'czr_fn_has_social_links' ) ) {
      function czr_fn_has_social_links() {
            $_socials = czr_fn_opt('tc_social_links');
            return !empty( $_socials );
      }
}



/**
* @return  bool
* @since Customizr 3.4+
* User option to enabe/disable all notices. Enabled by default.
*/
function czr_fn_is_front_help_enabled(){
  return apply_filters( 'tc_is_front_help_enabled' , (bool)czr_fn_opt('tc_display_front_help') );
}






/*-----------------------------------------------------------
/* PREVIOUSLY methods init.php and functions in core/_utils/*.*
/*----------------------------------------------------------*/
/**
* @return  boolean
* @since  3.4+
*/
if ( !function_exists( 'czr_fn_is_pro' ) ) {
      function czr_fn_is_pro() {
            return class_exists( 'CZR_init_pro' ) && "customizr-pro" == CZR_SANITIZED_THEMENAME;
      }
}

/**
* Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
* @return boolean
*
* @since  Customizr 3.0.11
*/
function czr_fn_is_child() {
    // get themedata version wp 3.4+
    // if ( function_exists( 'wp_get_theme' ) ) {
    //   //get WP_Theme object of customizr
    //   $tc_theme       = wp_get_theme();
    //   //define a boolean if using a child theme
    //   return $tc_theme->parent() ? true : false;
    // }
    // else {
    //   $tc_theme       = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
    //   return !empty($tc_theme['Template']) ? true : false;
    // }
    return is_child_theme();
}

/**
* Is the customizer left panel being displayed ?
* @return  boolean
* @since  3.4+
*/
if ( !function_exists( 'czr_fn_is_customize_left_panel' ) ) {
      function czr_fn_is_customize_left_panel() {
          global $pagenow;
          return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
      }
}

/**
* Is the customizer preview panel being displayed ?
* @return  boolean
* @since  3.4+
*/
if ( !function_exists( 'czr_fn_is_customize_preview_frame' ) ) {
      function czr_fn_is_customize_preview_frame() {
            return is_customize_preview() || ( !is_admin() && isset($_REQUEST['customize_messenger_channel']) );
      }
}

/**
* Always include wp_customize or customized in the custom ajax action triggered from the customizer
* => it will be detected here on server side
* typical example : the donate button
*
* @return boolean
* @since  3.4+
*/
if ( !function_exists( 'czr_fn_doing_customizer_ajax' ) ) {
      function czr_fn_doing_customizer_ajax() {
            $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
            return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
      }
}

/**
* Are we in a customization context ? => ||
* 1) Left panel ?
* 2) Preview panel ?
* 3) Ajax action from customizer ?
* @return  bool
* @since  3.4+
*/
if ( !function_exists( 'czr_fn_is_customizing' ) ) {
    function czr_fn_is_customizing() {
        global $pagenow;
        // the check on $pagenow does NOT work on multisite install @see https://github.com/presscustomizr/nimble-builder/issues/240
        // That's why we also check with other global vars
        // @see wp-includes/theme.php, _wp_customize_include()
        $is_customize_php_page = ( is_admin() && 'customize.php' == basename( $_SERVER['PHP_SELF'] ) );
        $is_customize_admin_page_one = (
          $is_customize_php_page
          ||
          ( isset( $_REQUEST['wp_customize'] ) && 'on' == $_REQUEST['wp_customize'] )
          ||
          ( !empty( $_GET['customize_changeset_uuid'] ) || !empty( $_POST['customize_changeset_uuid'] ) )
        );
        $is_customize_admin_page_two = is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;

        //checks if is customizing : two contexts, admin and front (preview frame)
        return $is_customize_admin_page_one || $is_customize_admin_page_two || czr_fn_is_customize_preview_frame() ||  czr_fn_doing_customizer_ajax();
    }
}


//@return boolean
//Is used to check if we can display specific notes including deep links to the customizer
function czr_fn_user_can_see_customize_notices_on_front() {
    return !czr_fn_is_customizing() && is_user_logged_in() && current_user_can( 'edit_theme_options' ) && is_super_admin();
}






/**
* Returns an option from the options array of the theme.
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_opt( $option_name , $option_group = null, $use_default = true ) {
    //do we have to look for a specific group of option (plugin?)
    $option_group = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;

    if ( class_exists( 'CZR___' ) ) {
        //when customizing, the db_options property is refreshed each time the preview is refreshed in 'customize_preview_init'
        $_db_options  = empty(CZR___::$db_options) ? czr_fn_cache_db_options() : CZR___::$db_options;
    } else {
        $_db_options = false === get_option( $option_group ) ? array() : (array)get_option( $option_group );
    }

    //do we have to use the default ?
    $__options    = $_db_options;
    $_default_val = false;
    if ( $use_default && class_exists( 'CZR___' ) ) {
      $_defaults      = CZR___::$default_options;
      if ( isset($_defaults[$option_name]) )
        $_default_val = $_defaults[$option_name];
      $__options      = wp_parse_args( $_db_options, $_defaults );
    }

    //assign false value if does not exist, just like WP does
    $_single_opt    = isset($__options[$option_name]) ? $__options[$option_name] : false;

    //ctx retro compat => falls back to default val if ctx like option detected
    //important note : some options like tc_slider are not concerned by ctx
    if ( !czr_fn_is_option_excluded_from_ctx( $option_name ) ) {
      if ( is_array( $_single_opt ) && !class_exists( 'CZR_contx' ) )
        $_single_opt = $_default_val;
    }

    //allow contx filtering globally
    $_single_opt = apply_filters( "czr_opt" , $_single_opt , $option_name , $option_group, $_default_val );

    //allow single option filtering
    return apply_filters( "tc_opt_{$option_name}" , $_single_opt , $option_name , $option_group, $_default_val );
}


/**
* In live context (not customizing || admin) cache the theme options
*
* @package Customizr
* @since Customizr 3.2.0
*/
function czr_fn_cache_db_options($opt_group = null) {
    $opt_group = is_null($opt_group) ? CZR_THEME_OPTIONS : $opt_group;
    CZR___::$db_options = false === get_option( $opt_group ) ? array() : (array)get_option( $opt_group );
    return CZR___::$db_options;
}

/**
* Helper
* Returns whether or not the option is a theme/addon option
*
* @return bool
*
* @package Customizr
* @since Customizr 3.4.9
*/
function czr_fn_is_customizr_option( $option_key ) {
    $_is_czr_option = in_array( substr( $option_key, 0, 3 ), apply_filters( 'czr_options_prefixes', array( CZR_OPT_PREFIX ) ) );
    return apply_filters( 'czr_is_customizr_option', $_is_czr_option , $option_key );
}



/**
* Returns the default options array
* Fixes the bbpress bug : Notice: bbp_setup_current_user was called incorrectly. The current user is being initialized without using $wp->init()
* czr_fn_get_default_options uses is_user_logged_in() => was causing the bug
* hook : after_setup_theme (?)
*
* @package Customizr
* @since Customizr 3.1.11
*/
function czr_fn_get_default_options() {
    //CZR___::$db_options is set in the CZR_BASE::czr_fn_init_properties()
    $_db_opts     = empty(CZR___::$db_options) ? czr_fn_cache_db_options() : CZR___::$db_options;
    $def_options  = isset($_db_opts['defaults']) ? $_db_opts['defaults'] : array();

    //Don't update if default options are not empty + customizing context
    //customizing out ? => we can assume that the user has at least refresh the default once (because logged in, see conditions below) before accessing the customizer
    //customzing => takes into account if user has set a filter or added a new customizer setting
    if ( !empty($def_options) && czr_fn_is_customizing() )
      return apply_filters( 'czr_default_options', $def_options );

    //Never update the defaults when wp_installing()
    //prevent issue https://github.com/presscustomizr/hueman/issues/571
    //Always update/generate the default option when (OR) :
    // 1) current user can edit theme options
    // 2) they are not defined
    // 3) theme version not defined
    // 4) versions are different
    if ( !wp_installing() ) {
        if ( current_user_can('edit_theme_options') || empty($def_options) || !isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
          $def_options          = czr_fn_generate_default_options( czr_fn_get_customizer_map( $get_default_option = 'true' ) , CZR_THEME_OPTIONS );
          //Adds the version in default
          $def_options['ver']   =  CUSTOMIZR_VER;

          //writes the new value in db (merging raw options with the new defaults ).
          czr_fn_set_option( 'defaults', $def_options, CZR_THEME_OPTIONS );
        }
    }

    return apply_filters( 'czr_default_options', $def_options );
}




/**
* Generates the default options array from a customizer map + add slider option
*
* @package Customizr
* @since Customizr 3.0.3
*/
function czr_fn_generate_default_options( $map, $option_group = null ) {
    //do we have to look in a specific group of option (plugin?)
    $option_group   = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;

    //initialize the default array with the sliders options
    $defaults = array();

    foreach ($map['add_setting_control'] as $key => $options) {
      //check it is a customizr option
      if(  !czr_fn_is_customizr_option( $key ) )
        continue;

      $option_name = $key;
      //write default option in array
      if( array_key_exists( 'default', $options ) ) {
          // added check on 'nimblecheck' to fix https://github.com/presscustomizr/customizr/issues/1732
          $defaults[$option_name] = in_array( $options['type'], array( 'checkbox', 'nimblecheck' ) ) ? (bool)$options['default'] : $options['default'];
      } else {
        $defaults[$option_name] = null;
      }
    }//end foreach

    return $defaults;
}




/**
* Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
* @package Customizr
* @since Customizr 1.0
*
*/
function czr_fn_get_theme_options( $option_group = null ) {
    //do we have to look in a specific group of option (plugin?)
    $option_group       = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;
    $saved              = empty(CZR___::$db_options) ? czr_fn_cache_db_options() : CZR___::$db_options;
    $defaults           = CZR___::$default_options;
    $__options          = wp_parse_args( $saved, $defaults );
      //$__options        = array_intersect_key( $__options, $defaults );
    return $__options;
}


/* ------------------------------------------------------------------------- *
 *  GENERATES THE LIST OF THEME SETTINGS ONLY
/* ------------------------------------------------------------------------- */
function czr_fn_generate_theme_setting_list() {
    $_settings_map = czr_fn_get_customizer_map( null, 'add_setting_control' );
    $_settings = array();
    foreach ( $_settings_map as $_id => $data ) {
        $_settings[] = $_id;
    }

    return $_settings;
}


/**
* Set an option value in the theme option group
* @param $option_name : string ( like tc_skin )
* @param $option_value : sanitized option value, can be a string, a boolean or an array
* @param $option_group : string ( like tc_theme_options )
* @return  void
*
* @package Customizr
* @since Customizr 3.4+
*/
function czr_fn_set_option( $option_name , $option_value, $option_group = null ) {
    //Always make sure we have an option group, otherwise nothing will be written
    $option_group           = is_null( $option_group ) ? CZR_THEME_OPTIONS : $option_group;
    $_options               = czr_fn_get_unfiltered_theme_options( $option_group );
    $_options[$option_name] = $option_value;

    update_option( $option_group, $_options );
}











/***************************
* CTX COMPAT
****************************/
/**
* Helper : define a set of options not impacted by ctx like tc_slider, last_update_notice.
* @return  array of excluded option names
*/
function czr_fn_get_ctx_excluded_options() {
    return apply_filters(
      'tc_get_ctx_excluded_options',
      array(
        'defaults',
        'tc_sliders',
        'tc_social_links',
        'tc_blog_restrict_by_cat',
        'last_update_notice',
        'last_update_notice_pro',
        '__moved_opts'
      )
    );
}

/**
* Boolean helper : tells if this option is excluded from the ctx treatments.
* @return bool
*/
function czr_fn_is_option_excluded_from_ctx( $opt_name ) {
    return in_array( $opt_name, czr_fn_get_ctx_excluded_options() );
}

/**
* Boolean helper
* We are in a scenario when we need to use the transient value previouly used to store the user_started_using_the_theme infos, in order to write them in the theme options
* Those infos must be structured this way {string}|{string}. Example : 'with|4.0.2'
*
* @return bool
*/
function czr_is_valid_user_started_infos( $user_started_infos_candidate ) {
    if ( !is_string( $user_started_infos_candidate ) )
      return;

    $exploded = explode('|', $user_started_infos_candidate );
    //$exploded array must have exactly 2 entries
    // (
    //     [0] => with
    //     [1] => 4.0.2
    // )
    if ( 2 != count( $exploded ) )
      return;
    //the first entry can only be 'with' or 'before'
    if ( !in_array( $exploded[0], array('with', 'before') ) )
      return;
    //the second string entry must be a string and be a version. Let's check that it includes at least one dot.
    if ( !is_string( $exploded[1] ) || false === strpos( $exploded[1], '.') )
      return;

    return true;
}


/**
* Set a theme option which stores at which theme version started using it
*
* @package Customizr
*/
function czr_fn_setup_started_using_theme_option_and_constants() {
    do_action( 'czr_before_setting_started_using_theme' );

    $to_update_user_started_using_theme     = false;

    $free_transient_or_option               = 'started_using_customizr';
    $pro_transient_or_option                = 'started_using_customizr_pro';


    // get_unfiltered_theme_options
    $theme_options                          = czr_fn_get_unfiltered_theme_options();//returns an empty array as default

    $is_customizr_free_or_pro_fresh_install = 1 >= count( $theme_options );


    //we are sure we have to set the user started using theme if it's a fresh install
    if ( $is_customizr_free_or_pro_fresh_install ) {
        $to_update_user_started_using_theme    = true;

        $transient_or_option                   = CZR_IS_PRO ? $pro_transient_or_option : $free_transient_or_option;
        $theme_options[ $transient_or_option ] = sprintf('%s|%s' , 'with', CUSTOMIZR_VER );
    }
    else {
        //This is needed in any case because we might be in an a case were we are updating from an older customizr-free to a new customizr-pro
        //Do we have to set the user started using the free theme in the theme options?
        if ( !array_key_exists( $free_transient_or_option, $theme_options ) ) {

            //check the free transient
            //THERE CAN BE A TRANSIENT SET, if yes, use that value
            $transient_free_value = esc_attr( get_transient( $free_transient_or_option ) );
            //let's make sure that the saved transient is a clean candidate to be saved in the theme options
            if ( czr_is_valid_user_started_infos( $transient_free_value ) ) {
                $to_update_user_started_using_theme = true;
                $user_started_using_free_theme_value        = $transient_free_value;
                $theme_options[ $free_transient_or_option ] = $user_started_using_free_theme_value;
            } else {
                //use the last_update_notice set in the theme options
                $has_already_installed_free           = array_key_exists( 'last_update_notice', $theme_options );
                $free_infos                           = $has_already_installed_free ? $theme_options['last_update_notice'] : '__not_set__';
                $last_update_notice_free_version      = '__not_set__';
                if ( is_array( $free_infos ) && array_key_exists( 'version', $free_infos ) ) {
                    $last_update_notice_free_version = $free_infos['version'];
                }

                //When to update the user started using the free theme?
                //1) Is not pro version
                //or
                //2) If CZR_IS_PRO, update the user started using the FREE theme only if the last_update_notice is present
                //if not, we have no clue
                if ( CZR_IS_PRO && $has_already_installed_free || !CZR_IS_PRO ) {
                    $to_update_user_started_using_theme = true;
                    //we are in the case of a free user updating the free theme but has previously cleaned the transients in db
                    //=> we consider that this user started with the last update notice free version.
                    //Which is a better approximation than before ( @see the way the function czr_fn_user_started_before_version() works )
                    //we fallback on the current theme version
                    $user_started_using_free_theme_value  = sprintf( '%s|%s',
                      'with',
                      ( $has_already_installed_free && '__not_set__' != $last_update_notice_free_version ) ? $last_update_notice_free_version : CUSTOMIZR_VER
                    );

                    //set it
                    $theme_options[ $free_transient_or_option ] = $user_started_using_free_theme_value;
                }
            }


        }

        //Do we have to set the user started using the pro theme in the theme options?
        if ( CZR_IS_PRO && !array_key_exists( $pro_transient_or_option, $theme_options ) ) {

            $to_update_user_started_using_theme = true;

            //THERE CAN BE A TRANSIENT SET, if yes, use that value
            $transient_pro_value = esc_attr( get_transient( $pro_transient_or_option ) );
            //let's make sure that the saved transient is a clean candidate to be saved in the theme options
            if ( czr_is_valid_user_started_infos( $transient_pro_value ) ) {
                $user_started_using_pro_theme_value       = $transient_pro_value;
            } else {

                //this might be :
                //1) a free user updating to pro => with
                //2) a free user updating and has cleaned transient (edge case but possible ) => before
                //3) a pro user updating and has cleaned transient ( edge also ) => before
                //How do make the difference between 1) and ( 2 or 3 )
                //=> we need something written by the pro => the last update notice in options
                $is_already_pro_user  = array_key_exists( 'last_update_notice_pro', $theme_options );
                $is_pro_fresh_install = !$is_already_pro_user;

                if ( $is_already_pro_user ) {
                    $pro_infos            = $theme_options['last_update_notice_pro'];
                    $is_pro_fresh_install = is_array( $pro_infos ) && array_key_exists( 'version', $pro_infos ) && $pro_infos['version'] == CUSTOMIZR_VER;
                }

                $user_started_with_this_version = $is_pro_fresh_install;
                if ( $is_already_pro_user && !$is_pro_fresh_install ) {
                    $user_started_with_this_version = false;
                }

                //if already pro user, we are in the case of the transient that have been cleaned in db
                //if not, then it's a free user upgrading to pro
                $user_started_using_pro_theme_value  = sprintf('%s|%s' , $user_started_with_this_version ? 'with' : 'before', CUSTOMIZR_VER );
            }

            $theme_options[ $pro_transient_or_option ] = $user_started_using_pro_theme_value;
        }
    }

    //maybe update the db value, if the user can edit theme options
    if ( $to_update_user_started_using_theme && is_user_logged_in() && current_user_can('edit_theme_options') ) {
        update_option( CZR_THEME_OPTIONS, $theme_options );
        //do we want at this point remove the transients?
        //delete_transient( $free_transient_or_option );
        //delete_transient( $pro_transient_or_option );
    }


    //set constants that we can use throughout the theme without having to access the options every time
    if ( !defined( 'CZR_USER_STARTED_USING_FREE_THEME' ) ) {
        define( 'CZR_USER_STARTED_USING_FREE_THEME',  isset( $theme_options[ $free_transient_or_option ] ) ? esc_attr( $theme_options[ $free_transient_or_option ] ) : false );
    }
    if ( !defined( 'CZR_USER_STARTED_USING_PRO_THEME' ) ) {
        define( 'CZR_USER_STARTED_USING_PRO_THEME',  isset( $theme_options[ $pro_transient_or_option ] ) ? esc_attr( $theme_options[ $pro_transient_or_option ] ) : false );
    }

    do_action( 'czr_after_setting_started_using_theme' );
}



/**
* Returns a boolean
* check if user started to use the theme before ( strictly < ) the requested version
*
* @package Customizr
* @since Customizr 3.2.9
*/
function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null, $what_to_check = null  ) {
    if ( is_null( $what_to_check ) ) {
        $_ispro = CZR_IS_PRO;
    } else {
        $_ispro = 'pro' == $what_to_check;
    }


    //these constants are set in czr_fn_setup_started_using_theme_option_and_constants()
    //called by init-base.php at the very start of the theme bootstrap, after base constants are set
    if ( $_ispro ) {
        $user_started_using_theme_value = defined( 'CZR_USER_STARTED_USING_PRO_THEME' ) ? CZR_USER_STARTED_USING_PRO_THEME : false;
    }else {
        $user_started_using_theme_value = defined( 'CZR_USER_STARTED_USING_FREE_THEME' ) ? CZR_USER_STARTED_USING_FREE_THEME : false;
    }

    if ( !$user_started_using_theme_value )
      return false;

    $_ver   = $_ispro ? $_pro_ver : $_czr_ver;

    if ( !is_string( $_ver ) )
      return false;


    $_start_version_infos = explode('|', $user_started_using_theme_value );

    if ( !is_array( $_start_version_infos ) )
      return false;

    switch ( $_start_version_infos[0] ) {
      //in this case with now exactly what was the starting version (most common case)
      case 'with':
        return isset( $_start_version_infos[1] ) ? version_compare( $_start_version_infos[1] , $_ver, '<' ) : true;
      break;
      //here the user started to use the theme before, we don't know when.
      //but this was actually before this check was created
      case 'before':
        return true;
      break;

      default :
        return false;
      break;
    }
}



//@return bool
function czr_fn_user_started_with_current_version() {
    if ( czr_fn_is_pro() )
      return;

    //this constant is set in czr_fn_setup_started_using_theme_option_and_constants()
    //called by init-base.php at the very start of the theme bootstrap, after base constants are set
    $user_started_using_theme_value = ( defined( 'CZR_USER_STARTED_USING_FREE_THEME' ) ) ? CZR_USER_STARTED_USING_FREE_THEME : false ;
    if ( !$user_started_using_theme_value )
      return false;

    $_start_version_infos = explode( '|', $user_started_using_theme_value );

    //make sure we're good at this point
    if ( !is_string( CUSTOMIZR_VER ) || !is_array( $_start_version_infos ) || count( $_start_version_infos ) < 2 )
      return false;

    return 'with' == $_start_version_infos[0] && version_compare( $_start_version_infos[1] , CUSTOMIZR_VER, '==' );
}


/* ------------------------------------------------------------------------- *
 * FONTS
/* ------------------------------------------------------------------------- */
/**
* @return an array of font name / code OR a string of the font css code
* @parameter string name or google compliant suffix for href link
*
* @package Customizr
* @since Customizr 3.2.9
*/
function czr_fn_get_font( $_what = 'list' , $_requested = null ) {
    $_to_return = ( 'list' == $_what ) ? array() : false;
    $_font_groups = apply_filters(
      'tc_font_pairs',
      CZR___::$instance->font_pairs
    );

    foreach ( $_font_groups as $_group_slug => $_font_list ) {
      if ( 'list' == $_what ) {
        $_to_return[$_group_slug] = array();
        $_to_return[$_group_slug]['list'] = array();
        $_to_return[$_group_slug]['name'] = $_font_list['name'];
      }

      foreach ( $_font_list['list'] as $slug => $data ) {
        switch ($_requested) {
          case 'name':
            if ( 'list' == $_what )
              $_to_return[$_group_slug]['list'][$slug] =  $data[0];
          break;

          case 'code':
            if ( 'list' == $_what )
              $_to_return[$_group_slug]['list'][$slug] =  $data[1];
          break;

          default:
            if ( 'list' == $_what )
              $_to_return[$_group_slug]['list'][$slug] = $data;
            else if ( $slug == $_requested ) {
                return $data[1];
            }
          break;
        }
      }
    }
    return $_to_return;
}

// @return bool
// Helper to check if the requested font code includes the Google font identifier : _g_
// introduced for https://github.com/presscustomizr/customizr/issues/1816
function czr_fn_is_gfont($_font , $_gfont_id = null ) {
    $_gfont_id = $_gfont_id ? $_gfont_id : '_g_';
    return false !== strpos( $_font , $_gfont_id );
}


/**
* Returns the url of the customizer with the current url arguments + an optional customizer section args
*
* @param $autofocus(optional) is an array indicating the elements to focus on ( control,section,panel).
* Ex : array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec').
* Wordpress will cycle among autofocus keys focusing the existing element - See wp-admin/customize.php.
* // Following not valid anymore in wp 4.6.1, due to a bug?
* //The actual focused element depends on its type according to this priority scale: control, section, panel.
* //In this sense when specifying a control, additional section and panel could be considered as fall-back.
*
* @param $control_wrapper(optional) is a string indicating the wrapper to apply to the passed control. By default is "tc_theme_options".
* Ex: passing $aufocus = array('control' => 'tc_front_slider') will produce the query arg 'autofocus'=>array('control' => 'tc_theme_options[tc_front_slider]'
*
* @return url string
* @since Customizr 3.4+
*/
function czr_fn_get_customizer_url( $autofocus = null, $control_wrapper = 'tc_theme_options' ) {
   $_current_url       = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   $_customize_url     = add_query_arg( 'url', urlencode( $_current_url ), wp_customize_url() );
   $autofocus  = ( !is_array($autofocus) || empty($autofocus) ) ? null : $autofocus;

   if ( is_null($autofocus) ) {
      return $_customize_url;
   }

   $_ordered_keys = array( 'control', 'section', 'panel');

   // $autofocus must contain at least one key among (control,section,panel)
   if ( !count( array_intersect( array_keys($autofocus), $_ordered_keys ) ) ) {
      return $_customize_url;
   }

   // $autofocus must contain at least one key among (control,section,panel)
   if ( !count( array_intersect( array_keys($autofocus), array( 'control', 'section', 'panel') ) ) ) {
      return $_customize_url;
   }

   // wrap the control in the $control_wrapper if neded
   if ( array_key_exists( 'control', $autofocus ) && !empty( $autofocus['control'] ) && $control_wrapper ) {
      $autofocus['control'] = $control_wrapper . '[' . $autofocus['control'] . ']';
   }

   //Since wp 4.6.1 we order the params following the $_ordered_keys order
   $autofocus = array_merge( array_filter( array_flip( $_ordered_keys ), '__return_false'), $autofocus );

   if ( !empty( $autofocus ) ) {
      //here we pass the first element of the array
      // We don't really have to care for not existent autofocus keys, wordpress will stash them when passing the values to the customize js
      return add_query_arg( array( 'autofocus' => array_slice( $autofocus, 0, 1 ) ), $_customize_url );
   }

   return $_customize_url;
}


// @return string
function czr_fn_get_customizer_focus_icon( $args = array() ) {
    $args = wp_parse_args( $args,
        array(
            'wot' => '', //control, section, panel
            'id' => ''  // the wp.customize id of the control, section or panel
        )
    );
    $wot = $args['wot'];
    $id = $args['id'];

    return sprintf( '<a href="%1$s">%2$s</a>',
      czr_fn_get_customizer_focus_link( array( 'wot' => $wot, 'id' => $id ) ),
      '<span class="customize-partial-edit-shortcut"><button class="customize-partial-edit-shortcut-button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button></span>'
    );
}

//@return string
function czr_fn_get_customizer_focus_link( $args = array() ) {
    $args = wp_parse_args( $args,
          array(
              'wot' => '', //control, section, panel
              'id' => ''  // the wp.customize id of the control, section or panel
          )
      );
    $wot = $args['wot'];
    $id = $args['id'];
    return "javascript:wp.customize.preview.send( 'czr-{$wot}-focus', '{$id}' );";
}



/**
* Is there a menu assigned to a given location ?
* Used in class-header-menu and class-fire-placeholders
* @return bool
* @since  v3.4+
*/
function czr_fn_has_location_menu( $_location ) {
    $_all_locations  = get_nav_menu_locations();
    return isset($_all_locations[$_location]) && is_object( wp_get_nav_menu_object( $_all_locations[$_location] ) );
}




/**
* Boolean helper to check if the secondary menu is enabled
* since v3.4+
*/
function czr_fn_is_secondary_menu_enabled() {
  return (bool) esc_attr( czr_fn_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( czr_fn_opt( 'tc_menu_style' ) );
}




/**
* Whether or not we are in the ajax context
* @return bool
* @since v3.4.37
*/
function czr_fn_is_ajax() {
  /*
  * wp_doing_ajax() introduced in 4.7.0
  */
  $wp_doing_ajax = ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) || ( ( defined('DOING_AJAX') && 'DOING_AJAX' ) );

  /*
  * https://core.trac.wordpress.org/ticket/25669#comment:19
  * http://stackoverflow.com/questions/18260537/how-to-check-if-the-request-is-an-ajax-request-with-php
  */
  $_is_ajax      = $wp_doing_ajax || ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

  return apply_filters( 'czr_is_ajax', $_is_ajax );
}




/*
* @return string
*/
function czr_fn_get_author_meta_description_by_id( $author_ID ) {
  return get_the_author_meta( 'description', $author_ID ); //falls back on the current post author ID if $author_ID is falsy
}



//Helper class to build a simple date diff object
//Alternative to date_diff for php version < 5.3.0
//http://stackoverflow.com/questions/9373718/php-5-3-date-diff-equivalent-for-php-5-2-on-own-function
if ( !class_exists( 'CZR_DateInterval' ) ) :
Class CZR_DateInterval {
    /* Properties */
    public $y = 0;
    public $m = 0;
    public $d = 0;
    public $h = 0;
    public $i = 0;
    public $s = 0;

    /* Methods */
    public function __construct ( $time_to_convert ) {
      $FULL_YEAR = 60*60*24*365.25;
      $FULL_MONTH = 60*60*24*(365.25/12);
      $FULL_DAY = 60*60*24;
      $FULL_HOUR = 60*60;
      $FULL_MINUTE = 60;
      $FULL_SECOND = 1;

      //$time_to_convert = 176559;
      $seconds = 0;
      $minutes = 0;
      $hours = 0;
      $days = 0;
      $months = 0;
      $years = 0;

      while($time_to_convert >= $FULL_YEAR) {
          $years ++;
          $time_to_convert = $time_to_convert - $FULL_YEAR;
      }

      while($time_to_convert >= $FULL_MONTH) {
          $months ++;
          $time_to_convert = $time_to_convert - $FULL_MONTH;
      }

      while($time_to_convert >= $FULL_DAY) {
          $days ++;
          $time_to_convert = $time_to_convert - $FULL_DAY;
      }

      while($time_to_convert >= $FULL_HOUR) {
          $hours++;
          $time_to_convert = $time_to_convert - $FULL_HOUR;
      }

      while($time_to_convert >= $FULL_MINUTE) {
          $minutes++;
          $time_to_convert = $time_to_convert - $FULL_MINUTE;
      }

      $seconds = $time_to_convert; // remaining seconds
      $this->y = $years;
      $this->m = $months;
      $this->d = $days;
      $this->h = $hours;
      $this->i = $minutes;
      $this->s = $seconds;
      $this->days = ( 0 == $years ) ? $days : ( $years * 365 + $months * 30 + $days );
    }
}
endif;


/*
* @return boolean
* http://stackoverflow.com/questions/11343403/php-exception-handling-on-datetime-object
*/
function czr_fn_is_date_valid($str) {
    if ( !is_string($str) )
       return false;

    $stamp = strtotime($str);
    if ( !is_numeric($stamp) )
       return false;

    if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) )
       return true;

    return false;
}

/**
* @return a date diff object
* @uses  date_diff if php version >=5.3.0, instantiates a fallback class if not
*
* @since 3.2.8
*
* @param date one object.
* @param date two object.
*/
function czr_fn_date_diff( $_date_one , $_date_two ) {
  //if version is at least 5.3.0, use date_diff function
  if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0) {
    return date_diff( $_date_one , $_date_two );
  } else {
    $_date_one_timestamp   = $_date_one->format("U");
    $_date_two_timestamp   = $_date_two->format("U");
    return new CZR_DateInterval( $_date_two_timestamp - $_date_one_timestamp );
  }
}



/**
* Return boolean OR number of days since last update OR PHP version < 5.2
*
* @package Customizr
* @since Customizr 3.2.6
*/
function czr_fn_post_has_update( $_bool = false) {
    //php version check for DateTime
    //http://php.net/manual/fr/class.datetime.php
    if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
      return false;

    //first proceed to a date check
    $dates_to_check = array(
      'created'   => get_the_date('Y-m-d g:i:s'),
      'updated'   => get_the_modified_date('Y-m-d g:i:s'),
      'current'   => date('Y-m-d g:i:s')
    );
    //ALL dates must be valid
    if ( 1 != array_product( array_map( 'czr_fn_is_date_valid' , $dates_to_check ) ) )
      return false;

    //Import variables into the current symbol table
    extract($dates_to_check);

    //Instantiate the different date objects
    $created                = new DateTime( $created );
    $updated                = new DateTime( $updated );
    $current                = new DateTime( $current );

    $created_to_updated     = czr_fn_date_diff( $created , $updated );
    $updated_to_today       = czr_fn_date_diff( $updated, $current );

    if ( true === $_bool )
      //return ( 0 == $created_to_updated->days && 0 == $created_to_updated->s ) ? false : true;
      return ( $created_to_updated->s > 0 || $created_to_updated->i > 0 ) ? true : false;
    else
      //return ( 0 == $created_to_updated->days && 0 == $created_to_updated->s ) ? false : $updated_to_today->days;
      return ( $created_to_updated->s > 0 || $created_to_updated->i > 0 ) ? $updated_to_today->days : false;
}


/**
* Check whether a category exists.
* (wp category_exists isn't available in pre_get_posts)
* @since 3.4.10
*
* @see term_exists()
*
* @param int $cat_id.
* @return bool
*/
function czr_fn_category_id_exists( $cat_id ) {
    return term_exists( (int) $cat_id, 'category');
}


/**
* Retrieve the file type from the file name
* Even when it's not at the end of the file
* copy of wp_check_filetype() in wp-includes/functions.php
*
* @since 3.2.3
*
* @param string $filename File name or path.
* @param array  $mimes    Optional. Key is the file extension with value as the mime type.
* @return array Values with extension first and mime type.
*/
function czr_fn_check_filetype( $filename, $mimes = null ) {
    $filename = basename( $filename );
    if ( empty($mimes) )
      $mimes = get_allowed_mime_types();
    $type = false;
    $ext = false;
    foreach ( $mimes as $ext_preg => $mime_match ) {
      $ext_preg = '!\.(' . $ext_preg . ')!i';
      //was ext_preg = '!\.(' . $ext_preg . ')$!i';
      if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
        $type = $mime_match;
        $ext = $ext_matches[1];
        break;
      }
    }

    return compact( 'ext', 'type' );
}






/**
* Returns the "real" queried post ID or if !isset, get_the_ID()
* Checks some contextual booleans
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_id()  {
    if ( in_the_loop() ) {

      $czr_id           = get_the_ID();
    } else {
      global $post;
      $queried_object   = get_queried_object();
      $czr_id           = ( !empty ( $post ) && isset($post->ID) ) ? $post->ID : null;
      $czr_id           = ( isset ($queried_object->ID) ) ? $queried_object->ID : $czr_id;
    }

    $czr_id = ( is_404() || is_search() || is_archive() ) ? null : $czr_id;

    return apply_filters( 'czr_id', $czr_id );
}




/**
* hook : the_content
* Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
*
* @return string
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_parse_imgs( $_html ) {
    $_bool = is_feed() || is_preview() || ( wp_is_mobile() && apply_filters( 'czr_disable_img_smart_load_mobiles', false ) );

    if ( apply_filters( 'czr_disable_img_smart_load', $_bool, current_filter() ) )
      return $_html;

    $allowed_image_extentions = apply_filters( 'czr_smartload_allowed_img_extensions', array(
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

    if ( empty( $allowed_image_extentions ) || !is_array( $allowed_image_extentions ) ) {
      return $_html;
    }

    $img_extensions_pattern = sprintf( "(?:%s)", implode( '|', $allowed_image_extentions ) );
    $pattern                = '#<img([^>]+?)src=[\'"]?([^\'"\s>]+\.'.$img_extensions_pattern.'[^\'"\s>]*)[\'"]?([^>]*)>#i';

    return preg_replace_callback( $pattern, 'czr_fn_regex_callback' , $_html);
}


/**
* callback of preg_replace_callback in czr_fn_parse_imgs
* Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
*
* @return string
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_regex_callback( $matches ) {
    $_placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    if ( false !== strpos( $matches[0], 'data-src' ) || preg_match('/ data-smartload *= *"false" */', $matches[0]) ) {
      return $matches[0];
    } else {
      return apply_filters( 'czr_img_smartloaded',
        str_replace( array('srcset=', 'sizes='), array('data-srcset=', 'data-sizes='),
            sprintf('<img %1$s src="%2$s" data-src="%3$s" %4$s>',
                $matches[1],
                $_placeholder,
                $matches[2],
                $matches[3]
            )
        )
      );
    }
}

/**
* helper
* Check if we are displaying posts lists or front page
* => not real home
* @return  bool
*/
function czr_fn_is_home() {
    //get info whether the front page is a list of last posts or a page
    return is_home() || ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page();
}


/**
* Check if we are displaying posts lists or front page
*
*/
function czr_fn_is_real_home() {
  // Warning : when show_on_front is a page, but no page_on_front has been picked yet, is_home() is true
  // beware of https://github.com/presscustomizr/nimble-builder/issues/349
  return ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) )
  || ( is_home() && 0 == get_option( 'page_on_front' ) && 'page' == get_option( 'show_on_front' ) )//<= this is the case when the user want to display a page on home but did not pick a page yet
  || is_front_page();
}


/**
* Check if we show posts or page content on home page
*
* @since Customizr 3.0.6
*
*/
function czr_fn_is_home_empty() {
    //check if the users has choosen the "no posts or page" option for home page
    return ( ( is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
}



/**
* Title element formating
*
* @since Customizr 2.1.6
*
*/
function czr_fn_wp_filter_title( $title, $sep ) {
    if ( function_exists( '_wp_render_title_tag' ) )
      return $title;

    global $paged, $page;

    if ( is_feed() )
      return $title;

    // Add the site name.
    $title .= get_bloginfo( 'name' );

    // Add the site description for the home/front page.
    $site_description = get_bloginfo( 'description' , 'display' );
    if ( $site_description && czr_fn_is_real_home() )
      $title = "$title $sep $site_description";

    // Add a page number if necessary.
    if ( $paged >= 2 || $page >= 2 )
      $title = "$title $sep " . sprintf( __( 'Page %s' , 'customizr' ), max( $paged, $page ) );

    return $title;
}





/**
* Return object post type
*
* @since Customizr 3.0.10
*
*/
function czr_fn_get_post_type() {
    global $post;

    if ( !isset($post) )
      return;

    return $post->post_type;
}


/**
* Boolean : check if we are in the no search results case
*
* @package Customizr
* @since 3.0.10
*/
function czr_fn_is_no_results() {
    global $wp_query;
    return ( is_search() && 0 == $wp_query->post_count ) ? true : false;
}



/*-----------------------------------------------------------
/* PREVIOUSLY IN core/functions-ccat.php
/*----------------------------------------------------------*/

function czr_fn_is_list_of_posts() {
    //must be archive or search result. Returns false if home is empty in options.
    return apply_filters( 'czr_is_list_of_posts',
      !is_singular()
      && !is_404()
      && !czr_fn_is_home_empty()
      && !is_admin()
    );
}

//@return bool : whether the current post is an attachment and an image mime type
function czr_fn_is_attachment_image() {
    return apply_filters( 'czr_fn_is_attachment_image', is_attachment() && wp_attachment_is_image() );
}


/*-----------------------------------------------------------
/* PREVIOUSLY IN inc/czr-init-ccat.php (class-fire-utils_settings_map.php) and core/functions-ccat.php
/*----------------------------------------------------------*/


/**
* Returns the layout choices array
*
* @package Customizr
* @since Customizr 3.1.0
*/
function czr_fn_layout_choices() {
    $global_layout  = apply_filters( 'tc_global_layout' , CZR_init::$instance->global_layout );
    $layout_choices = array();
    foreach ($global_layout as $key => $value) {
      $layout_choices[$key]   = ( $value['customizer'] ) ? call_user_func(  '__' , $value['customizer'] , 'customizr' ) : null ;
    }
    return $layout_choices;
}


/**
* Retrieves slider names and generate the select list
* @package Customizr
* @since Customizr 3.0.1
*/
function czr_fn_slider_choices() {
  $__options      =   get_option('tc_theme_options');
  $slider_names   =   isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

  $slider_choices = array(
    0     =>  __( '&mdash; No slider &mdash;' , 'customizr' ),
    'demo'  =>  __( '&mdash; Demo Slider &mdash;' , 'customizr' ),
    'tc_posts_slider' => __('&mdash; Auto-generated slider from your blog posts &mdash;', 'customizr')
  );

  if ( $slider_names ) {
    foreach( $slider_names as $tc_name => $slides) {
      $slider_choices[$tc_name] = $tc_name;
    }
  }

  return $slider_choices;
}



/***************************************************************
* CUSTOMIZER ACTIVE CALLBACKS
***************************************************************/
/**
* active callback of section 'customizr_go_pro'
* @return  bool
*/
function czr_fn_pro_section_active_cb() {
    return !czr_fn_isprevdem();
}




/***************************************************************
* SANITIZATION HELPERS
***************************************************************/
/**
 * adds sanitization callback funtion : textarea
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_textarea( $value) {
  $value = esc_html( $value);
  return $value;
}



/**
 * adds sanitization callback funtion : number
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_number( $value) {
  if ( !$value || is_null($value) )
    return $value;

  $value = esc_attr( $value); // clean input
  $value = (int) $value; // Force the value into integer type.

  return ( 0 < $value ) ? $value : null;
}

/**
 * adds sanitization callback funtion : url
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_url( $value) {
  $value = esc_url( $value);
  return $value;
}

/**
 * adds sanitization callback funtion : email
 * @package Customizr
 * @since Customizr 3.4.11
 */
function czr_fn_sanitize_email( $value) {
  $sanitized_value = sanitize_email( $value );
  //return a proper WP error if the sanitizaion fails in the customizr
  if ( czr_fn_is_customizing() && $value && !$sanitized_value ) {
    return new WP_Error( 'required', __( 'Please fill the email input with a valid email address', 'customizr' ) );
  }
  return $sanitized_value;
}

/**
 * adds sanitization callback funtion : colors
 * @package Customizr
 * @since Customizr 1.1.4
 */
function czr_fn_sanitize_hex_color( $color ) {
  if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
    return '#' . $unhashed;

  return $color;
}


/**
* Change upload's path to relative instead of absolute
* @package Customizr
* @since Customizr 3.1.11
*/
function czr_fn_sanitize_uploads( $url ) {
  $upload_dir = wp_upload_dir();
  return str_replace($upload_dir['baseurl'], '', $url);
}




/**
* Gets the social networks list defined in customizer options
*
*
*
* @package Customizr
* @since Customizr 3.0.10
*
* @since Customizr 3.4.55 Added the ability to retrieve them as array
* @param $output_type optional. Return type "string" or "array"
*/
//MODEL LOOKS LIKE THIS
//(
//     [0] => Array
//         (
//             [is_mod_opt] => 1
//             [module_id] => tc_social_links_czr_module
//             [social-size] => 15
//         )

//     [1] => Array
//         (
//             [id] => czr_social_module_0
//             [title] => Follow us on Renren
//             [social-icon] => fa-renren
//             [social-link] => http://customizr-dev.dev/feed/rss/
//             [social-color] => #6d4c8e
//             [social-target] => 1
//         )
// )
function czr_fn_get_social_networks( $output_type = 'string' ) {

    $_socials         = czr_fn_opt('tc_social_links');
    $_default_color   = array('rgb(90,90,90)', '#5a5a5a'); //both notations
    $_default_size    = '14'; //px

    $_social_opts     = array( 'social-size' => $_default_size );

    if ( empty( $_socials ) )
      return;

    //get the social mod opts
    foreach( $_socials as $key => $item ) {
      if ( !array_key_exists( 'is_mod_opt', $item ) )
        continue;
      $_social_opts = wp_parse_args( $item, $_social_opts );
    }
    $font_size_value = $_social_opts['social-size'];
    //if the size is the default one, do not add the inline style css
    $social_size_css  = empty( $font_size_value ) || $_default_size == $font_size_value ? '' : "font-size:{$font_size_value}px";

    $_social_links = array();

    //FA5 backward compatibility with FA4
    //see https://github.com/presscustomizr/customizr/issues/1364
    $_fa_solid_icons = array(
      'fa-envelope',
      'fa-envelope-square',
      'fa-map-marker',
      'fa-mobile',
      'fa-mobile-alt',
      'fa-phone',
      'fa-phone-square',
      'fa-rss',
      'fa-rss-square',
      'fa-share-alt',
      'fa-share-alt-square',
      'fa-sms'
    );

    $_fa_icon_replacements = array(
        'fa-bitbucket-square'     => 'fa-bitbucket',
        'fa-facebook-official'    => 'fa-facebook-f',
        'fa-google-plus-circle'   => 'fa-google-plus',
        'fa-google-plus-official' => 'fa-google-plus',
        'fa-linkedin-square'      => 'fa-linkedin',
        'fa-youtube-play'         => 'fa-youtube'
    );

    foreach( $_socials as $key => $item ) {
        //skip if mod_opt
        if ( array_key_exists( 'is_mod_opt', $item ) )
          continue;

        //get the social icon suffix for backward compatibility (users custom CSS) we still add the class icon-*
        $icon_class            = isset($item['social-icon']) ? esc_attr($item['social-icon']) : '';
        $link_icon_class       = 'fa-' === substr( $icon_class, 0, 3 ) && 3 < strlen( $icon_class ) ?
                ' icon-' . str_replace( array('rss', 'envelope'), array('feed', 'mail'), substr( $icon_class, 3, strlen($icon_class) ) ) :
                '';


        //FA5 backward compatibility with FA4
        //see https://github.com/presscustomizr/customizr/issues/1364
        //by default they're brands
        $fa_group = 'fab';

        //perform replacements for missing icons
        $icon_class = str_replace( array_keys( $_fa_icon_replacements ), array_values( $_fa_icon_replacements ), $icon_class );

        //then treat the -o case: We just use the fa-envelope-o as of now
        if ( strlen( $icon_class ) - 2 == strpos( $icon_class, '-o' ) ) {
            $icon_class = str_replace( '-o', '', $icon_class );
            $fa_group = 'far';
        }
        //treat the few solid icons
        else if ( in_array( $icon_class, $_fa_solid_icons ) ){
            $fa_group = 'fas';
        }
        $icon_class   = "{$fa_group} {$icon_class}";

        // links like tel:*** or skype:**** or call:**** should work
        // implemented for https://github.com/presscustomizr/social-links-modules/issues/7
        $social_link = 'javascript:void(0)';

        $is_blank_target = ( isset($item['social-target']) && false != $item['social-target'] );

        // set the relationship attribute
        // fixes : https://github.com/presscustomizr/social-links-modules/issues/8
        // fixes : https://github.com/presscustomizr/hueman/issues/842
        $rel_attr = 'rel="nofollow"';
        if ( $is_blank_target ) {
            // fix potential performance and security issues with other attributes
            // @see https://web.dev/external-anchors-use-rel-noopener
            $rel_attr = 'rel="nofollow noopener noreferrer"';
        }

        if ( isset($item['social-link']) && !empty( $item['social-link'] ) ) {
            if ( false !== strpos($item['social-link'], 'callto:') || false !== strpos($item['social-link'], 'tel:') || false !== strpos($item['social-link'], 'skype:') || false !== strpos($item['social-link'], 'viber:') ) {
                $social_link = esc_attr( $item['social-link'] );
                $rel_attr = '';//we don't need to set a relationship attribute in this case
            } else {
                $social_link = esc_url( $item['social-link'] );
            }
        }

        /* Maybe build inline style */
        $social_color_css      = isset($item['social-color']) ? esc_attr($item['social-color']) : $_default_color[0];
        //if the color is the default one, do not print the inline style css
        $social_color_css      = in_array( $social_color_css, $_default_color ) ? '' : "color:{$social_color_css}";
        $style_props           = implode( ';', array_filter( array( $social_color_css, $social_size_css ) ) );

        $style_attr            = $style_props ? sprintf(' style="%1$s"', $style_props ) : '';

        array_push( $_social_links, sprintf('<a %8$s class="social-icon%6$s" %1$s title="%2$s" aria-label="%2$s" href="%3$s" %4$s %7$s><i class="%5$s"></i></a>',
          //do we have an id set ?
          //Typically not if the user still uses the old options value.
          //So, if the id is not present, let's build it base on the key, like when added to the collection in the customizer

          // Put them together
            !czr_fn_is_customizing() ? '' : sprintf( 'data-model-id="%1$s"', !isset( $item['id'] ) ? 'czr_socials_'. $key : $item['id'] ),
            isset($item['title']) ? esc_attr( $item['title'] ) : '',
            $social_link,
            $is_blank_target ? ' target="_blank"' : '',
            $icon_class,
            $link_icon_class,
            $style_attr,
            $rel_attr
        ) );
    }

    /*
    * return
    */
    switch ( $output_type ) :
      case 'array' : return $_social_links;
      default      : return implode( '', $_social_links );
    endswitch;
}


/**
* helper
* Prints the social links. Used as partial refresh callback
* @return  void
*/
if ( !function_exists( 'czr_fn_print_social_links' ) ) {
    function czr_fn_print_social_links() {
        if ( !czr_fn_is_ms() ) {
          echo czr_fn_get_social_networks();
        } else {
          czr_fn_render_template( 'modules/common/social_block' );
        }
    }
}

/* HELPER FOR CHECKBOX OPTIONS */
//the new options use 1 and 0
function czr_fn_is_checked( $opt_name = '') {
    $val = czr_fn_opt( $opt_name );
    //cast to string if array
    $val = is_array($val) ? $val[0] : $val;
    return czr_fn_booleanize_checkbox_val( $val );
}

function czr_fn_booleanize_checkbox_val( $val ) {
    if ( !$val )
      return false;
    if ( is_bool( $val ) && $val )
      return true;
    switch ( (string) $val ) {
      case 'off':
      case '' :
      case 'false' :
        return false;
      case 'on':
      case '1' :
      case 'true' :
        return true;
      default : return false;
    }
}

if ( !function_exists( 'czr_fn_text_truncate' ) ):
  /**
  * Helper
  * Returns the passed text truncated at $text_length char.
  * with the $more text added
  *
  * @return string
  *
  */
  function czr_fn_text_truncate( $text, $max_text_length, $more, $strip_tags = true ) {
      if ( !$text )
          return '';

      if ( $strip_tags )
        $text       = strip_tags( $text );

      if ( !$max_text_length )
          return $text;

      $end_substr = $text_length = strlen( $text );
      if ( $text_length > $max_text_length ) {
          $text      .= ' ';
          $end_substr = strpos( $text, ' ' , $max_text_length);
          $end_substr = ( FALSE !== $end_substr ) ? $end_substr : $max_text_length;
          $text       = trim( substr( $text , 0 , $end_substr ) );
      }

      if ( $more && $end_substr < $text_length )
        return $text . ' ' .$more;

      return $text;

  }
endif;



if ( !function_exists( 'czr_fn_is_home_and_header_transparent_set' ) ):
  // @return bool
  function czr_fn_is_home_and_header_transparent_set() {
      // Conditions to meet are:
      // 1) option checked
      // 2) is real home
      // 3) is the first page of a paginated home see https://github.com/presscustomizr/customizr/issues/1665

      if ( apply_filters( 'czr_header_transparent_disabled_if_not_first_page', true ) ) {
        global $wp_query;

        $_is_not_first_page = isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ||
                              isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 1;

        $disable_because_not_first_page   = $_is_not_first_page;
      } else {
        $disable_because_not_first_page   = false;
      }
      return apply_filters( 'czr_header_transparent', ( 1 == esc_attr( czr_fn_opt( 'tc_header_transparent_home' ) ) ) && czr_fn_is_real_home() && !$disable_because_not_first_page );
  }
endif;


if ( !function_exists( 'czr_fn_get_header_skin' ) ):
  /**
  * Helper
  * Returns the header skin string
  *
  * @return string
  *
  */
  function czr_fn_get_header_skin() {
      $skin_color = czr_fn_opt( 'tc_header_skin' );
      if ( czr_fn_is_home_and_header_transparent_set() ) {
          $skin_color = czr_fn_opt( 'tc_home_header_skin' );
      }
      return $skin_color;
  }
endif;




/**
* HELPER
* Check whether the plugin is active by checking the active_plugins list.
* copy of is_plugin_active declared in wp-admin/includes/plugin.php
*
* @since 3.3+
*
* @param string $plugin Base plugin path from plugins directory.
* @return bool True, if in the active plugins list. False, not in the list.
*/
function czr_fn_is_plugin_active( $plugin ) {
  return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || czr_fn_is_plugin_active_for_network( $plugin );
}


/**
* HELPER
* Check whether the plugin is active for the entire network.
* copy of is_plugin_active_for_network declared in wp-admin/includes/plugin.php
*
* @since 3.3+
*
* @param string $plugin Base plugin path from plugins directory.
* @return bool True, if active for the network, otherwise false.
*/
function czr_fn_is_plugin_active_for_network( $plugin ) {
  if ( !is_multisite() )
    return false;

  $plugins = get_site_option( 'active_sitewide_plugins');
  if ( isset($plugins[$plugin]) )
    return true;

  return false;
}

// @return string
function czr_fn_is_full_nimble_tmpl() {
  $bool = false;
  if ( function_exists('Nimble\sek_get_locale_template') ) {
    $tmpl_name = \Nimble\sek_get_locale_template();
    $tmpl_name = ( !empty( $tmpl_name ) && is_string( $tmpl_name ) ) ? basename( $tmpl_name ) : '';

    // kept for retro-compat.
    // since Nimble Builder v1.4.0, the 'nimble_full_tmpl_ghf.php' has been deprecated
    $bool = 'nimble_full_tmpl_ghf.php' === $tmpl_name;

    // "is full Nimble template" when header, footer and content use Nimble templates.
    if ( function_exists('Nimble\sek_page_uses_nimble_header_footer') ) {
        $bool = ( 'nimble_template.php' === $tmpl_name || 'nimble-tmpl.php' === $tmpl_name ) && Nimble\sek_page_uses_nimble_header_footer();
    }
  }
  return $bool;
}


/* ------------------------------------------------------------------------- *
 * Template tags parsing
/* ------------------------------------------------------------------------- */
function czr_fn_get_year() {
    return esc_attr( date('Y') );
}

function czr_fn_find_pattern_match($matches) {
    $replace_values = array(
        'home_url' => 'home_url',
        'year' => 'czr_fn_get_year',
        'site_title' => 'get_bloginfo'
    );

    if ( array_key_exists( $matches[1], $replace_values ) ) {
      $dyn_content = $replace_values[$matches[1]];
      if ( function_exists( $dyn_content ) ) {
        return call_user_func( $dyn_content ); //$dyn_content();//<= @todo handle the case when the callback is a method
      } else if ( is_string($dyn_content) ) {
        return $dyn_content;
      } else {
        return null;
      }
    }
    return null;
}
// fired @filter 'czr_parse_template_tags'
function czr_fn_parse_template_tags( $val ) {
    //the pattern could also be '!\{\{(\w+)\}\}!', but adding \s? allows us to allow spaces around the term inside curly braces
    //see https://stackoverflow.com/questions/959017/php-regex-templating-find-all-occurrences-of-var#comment71815465_959026
    return is_string( $val ) ? preg_replace_callback( '!\{\{\s?(\w+)\s?\}\}!', 'czr_fn_find_pattern_match', $val) : $val;
}
add_filter( 'czr_parse_template_tags', 'czr_fn_parse_template_tags' );

