<?php
/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists( 'czr_fn_is_modern_style' ) ) {
      function czr_fn_is_modern_style() {
            if ( isset( $_GET['czr_modern_style'] ) && true == $_GET['czr_modern_style'] )
              return true;
            return defined( 'CZR_MODERN_STYLE' ) ? CZR_MODERN_STYLE : false;
      }
}


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
if( ! function_exists( 'czr_fn__f' ) ) :
    function czr_fn__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
        return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;

//This function is the only one with a different prefix.
//It has been kept in the theme for retro-compatibility.
if( ! function_exists( 'tc__f' ) ) :
    function tc__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
        return czr_fn__f( $tag , $value, $arg_one, $arg_two , $arg_three, $arg_four, $arg_five );
    }
endif;

if ( !( function_exists( 'czr_fn_isprevdem' ) ) ) :
//@return bool
function czr_fn_isprevdem() {
    global $wp_customize;
    $is_dirty = false;
    if ( is_object( $wp_customize ) && method_exists( $wp_customize, 'unsanitized_post_values' ) ) {
        $real_cust            = $wp_customize -> unsanitized_post_values( array( 'exclude_changeset' => true ) );
        $_preview_index       = array_key_exists( 'customize_messenger_channel' , $_POST ) ? $_POST['customize_messenger_channel'] : '';
        $_is_first_preview    = false !== strpos( $_preview_index ,'-0' );
        $_doing_ajax_partial  = array_key_exists( 'wp_customize_render_partials', $_POST );
        //There might be cases when the unsanitized post values contains old widgets infos on initial preview load, giving a wrong dirtyness information
        $is_dirty             = ( ! empty( $real_cust ) && ! $_is_first_preview ) || $_doing_ajax_partial;
    }
    return apply_filters( 'czr_fn_isprevdem', ! $is_dirty && czr_fn_get_raw_option( 'template', null, false ) != get_stylesheet() && ! is_child_theme() && ! czr_fn_is_pro() );
}
endif;



//@return an array of unfiltered options
//=> all options or a single option val
if ( !( function_exists( 'czr_fn_get_raw_option' ) ) ) :
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null, $from_cache = true ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize( $alloptions );
    $alloptions = ! is_array( $alloptions ) ? array() : $alloptions;//fixes https://github.com/presscustomizr/hueman/issues/492
    //is there any option group requested ?
    if ( ! is_null( $opt_group ) && array_key_exists( $opt_group, $alloptions ) ) {
      $alloptions = maybe_unserialize( $alloptions[ $opt_group ] );
    }
    //shall we return a specific option ?
    if ( is_null( $opt_name ) ) {
        return $alloptions;
    } else {
        $opt_value = array_key_exists( $opt_name, $alloptions ) ? maybe_unserialize( $alloptions[ $opt_name ] ) : false;//fallback on cache option val
        //do we need to get the db value instead of the cached one ? <= might be safer with some user installs not properly handling the wp cache
        //=> typically used to checked the template name for czr_fn_isprevdem()
        if ( ! $from_cache ) {
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




if ( ! ( function_exists( 'czr_fn_get_admin_option' ) ) ) :
//@return an array of options
function czr_fn_get_admin_option( $option_group = null ) {
    $option_group           = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;

    //here we could hook a callback to remove all the filters on "option_{CZR_THEME_OPTIONS}"
    do_action( "czr_before_getting_option_{$option_group}" );
    $options = get_option( $option_group, array() );
    //here we could hook a callback to re-add all the filters on "option_{CZR_THEME_OPTIONS}"
    do_action( "czr_after_getting_option_{$option_group}" );
    return $options;
}
endif;




/*-----------------------------------------------------------
/* PREVIOUSLY IN init.php and core/_utils/*.*
/*----------------------------------------------------------*/
//@return boolean
if ( ! function_exists( 'czr_fn_is_partial_refreshed_on' ) ) {
  function czr_fn_is_partial_refreshed_on() {
    return apply_filters( 'tc_partial_refresh_on', true );
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
            $_socials = czr_fn_opt('tc_social_links');
            return ! empty( $_socials );
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
if ( ! function_exists( 'czr_fn_is_pro' ) ) {
      function czr_fn_is_pro() {
            return class_exists( 'CZR_init_pro' ) && "customizr-pro" == CZR___::$theme_name;
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
    if ( function_exists( 'wp_get_theme' ) ) {
      //get WP_Theme object of customizr
      $tc_theme       = wp_get_theme();
      //define a boolean if using a child theme
      return $tc_theme -> parent() ? true : false;
    }
    else {
      $tc_theme       = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
      return ! empty($tc_theme['Template']) ? true : false;
    }
}

/**
* Is the customizer left panel being displayed ?
* @return  boolean
* @since  3.4+
*/
if ( ! function_exists( 'czr_fn_is_customize_left_panel' ) ) {
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
if ( ! function_exists( 'czr_fn_is_customize_preview_frame' ) ) {
      function czr_fn_is_customize_preview_frame() {
            return is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) );
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
if ( ! function_exists( 'czr_fn_doing_customizer_ajax' ) ) {
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
if ( ! function_exists( 'czr_fn_is_customizing' ) ) {
    function czr_fn_is_customizing() {
        //checks if is customizing : two contexts, admin and front (preview frame)
        return czr_fn_is_customize_left_panel() ||
               czr_fn_is_customize_preview_frame() ||
               czr_fn_doing_customizer_ajax();
    }
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
    //when customizing, the db_options property is refreshed each time the preview is refreshed in 'customize_preview_init'
    $_db_options  = empty(CZR___::$db_options) ? czr_fn_cache_db_options() : CZR___::$db_options;

    //do we have to use the default ?
    $__options    = $_db_options;
    $_default_val = false;
    if ( $use_default ) {
      $_defaults      = CZR___::$default_options;
      if ( isset($_defaults[$option_name]) )
        $_default_val = $_defaults[$option_name];
      $__options      = wp_parse_args( $_db_options, $_defaults );
    }

    //assign false value if does not exist, just like WP does
    $_single_opt    = isset($__options[$option_name]) ? $__options[$option_name] : false;

    //ctx retro compat => falls back to default val if ctx like option detected
    //important note : some options like tc_slider are not concerned by ctx
    if ( ! czr_fn_is_option_excluded_from_ctx( $option_name ) ) {
      if ( is_array( $_single_opt ) && ! class_exists( 'CZR_contx' ) )
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
    if ( ! empty($def_options) && czr_fn_is_customizing() )
      return apply_filters( 'czr_default_options', $def_options );

    //Always update/generate the default option when (OR) :
    // 1) current user can edit theme options
    // 2) they are not defined
    // 3) theme version not defined
    // 4) versions are different
    if ( current_user_can('edit_theme_options') || empty($def_options) || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
      $def_options          = czr_fn_generate_default_options( czr_fn_get_customizer_map( $get_default_option = 'true' ) , CZR_THEME_OPTIONS );
      //Adds the version in default
      $def_options['ver']   =  CUSTOMIZR_VER;

      //writes the new value in db (merging raw options with the new defaults ).
      czr_fn_set_option( 'defaults', $def_options, CZR_THEME_OPTIONS );
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
      if(  ! czr_fn_is_customizr_option( $key ) )
        continue;

      $option_name = $key;
      //write default option in array
      if( isset($options['default']) )
        $defaults[$option_name] = ( 'checkbox' == $options['type'] ) ? (bool) $options['default'] : $options['default'];
      else
        $defaults[$option_name] = null;
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
    $option_group           = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;
    $_options               = czr_fn_get_admin_option( $option_group );
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
* Returns a boolean
* check if user started to use the theme before ( strictly < ) the requested version
*
* @package Customizr
* @since Customizr 3.2.9
*/
function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null ) {
    $_ispro = CZR_IS_PRO;

    //the transient is set in CZR___::czr_fn_init_properties()
    $_trans = $_ispro ? 'started_using_customizr_pro' : 'started_using_customizr';

    if ( ! get_transient( $_trans ) )
      return false;

    $_ver   = $_ispro ? $_pro_ver : $_czr_ver;

    if ( ! is_string( $_ver ) )
      return false;


    $_start_version_infos = explode('|', esc_attr( get_transient( $_trans ) ) );

    if ( ! is_array( $_start_version_infos ) )
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
      CZR_init::$instance -> font_pairs
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
   $autofocus  = ( ! is_array($autofocus) || empty($autofocus) ) ? null : $autofocus;

   if ( is_null($autofocus) ) {
      return $_customize_url;
   }

   $_ordered_keys = array( 'control', 'section', 'panel');

   // $autofocus must contain at least one key among (control,section,panel)
   if ( ! count( array_intersect( array_keys($autofocus), $_ordered_keys ) ) ) {
      return $_customize_url;
   }

   // $autofocus must contain at least one key among (control,section,panel)
   if ( ! count( array_intersect( array_keys($autofocus), array( 'control', 'section', 'panel') ) ) ) {
      return $_customize_url;
   }

   // wrap the control in the $control_wrapper if neded
   if ( array_key_exists( 'control', $autofocus ) && ! empty( $autofocus['control'] ) && $control_wrapper ) {
      $autofocus['control'] = $control_wrapper . '[' . $autofocus['control'] . ']';
   }

   //Since wp 4.6.1 we order the params following the $_ordered_keys order
   $autofocus = array_merge( array_filter( array_flip( $_ordered_keys ), '__return_false'), $autofocus );

   if ( ! empty( $autofocus ) ) {
      //here we pass the first element of the array
      // We don't really have to care for not existent autofocus keys, wordpress will stash them when passing the values to the customize js
      return add_query_arg( array( 'autofocus' => array_slice( $autofocus, 0, 1 ) ), $_customize_url );
   }

   return $_customize_url;
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
  $_is_ajax      = $wp_doing_ajax || ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

  return apply_filters( 'czr_is_ajax', $_is_ajax );
}







//Helper class to build a simple date diff object
//Alternative to date_diff for php version < 5.3.0
//http://stackoverflow.com/questions/9373718/php-5-3-date-diff-equivalent-for-php-5-2-on-own-function
if ( ! class_exists( 'CZR_DateInterval' ) ) :
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
    if ( ! is_string($str) )
       return false;

    $stamp = strtotime($str);
    if ( ! is_numeric($stamp) )
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
      //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : true;
      return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? true : false;
    else
      //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : $updated_to_today -> days;
      return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? $updated_to_today -> days : false;
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
      $czr_id           = ( ! empty ( $post ) && isset($post -> ID) ) ? $post -> ID : null;
      $czr_id           = ( isset ($queried_object -> ID) ) ? $queried_object -> ID : $czr_id;
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

    if ( empty( $allowed_image_extentions ) || ! is_array( $allowed_image_extentions ) ) {
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
* Check if we are displaying posts lists or front page
*
* @since Customizr 3.0.6
*
*/
function czr_fn_is_home() {
  //get info whether the front page is a list of last posts or a page
  return ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) )
    || ( 0 == get_option( 'page_on_front' ) && 'page' == get_option( 'show_on_front' ) )//<= this is the case when the user want to display a page on home but did not pick a page yet
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
function czr_fn_wp_title( $title, $sep ) {
    if ( function_exists( '_wp_render_title_tag' ) )
      return $title;

    global $paged, $page;

    if ( is_feed() )
      return $title;

    // Add the site name.
    $title .= get_bloginfo( 'name' );

    // Add the site description for the home/front page.
    $site_description = get_bloginfo( 'description' , 'display' );
    if ( $site_description && czr_fn_is_home() )
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

    if ( ! isset($post) )
      return;

    return $post -> post_type;
}


/**
* Boolean : check if we are in the no search results case
*
* @package Customizr
* @since 3.0.10
*/
function czr_fn_is_no_results() {
    global $wp_query;
    return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
}


/*-----------------------------------------------------------
/* PREVIOUSLY IN inc/czr-init.php (class-fire-utils_settings_map.php) and core/functions.php
/*----------------------------------------------------------*/


/**
* Returns the layout choices array
*
* @package Customizr
* @since Customizr 3.1.0
*/
function czr_fn_layout_choices() {
    $global_layout  = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
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


/**
* active callback of section 'customizr_go_pro'
* @return  bool
*/
function czr_fn_pro_section_active_cb() {
    return ! czr_fn_isprevdem();
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
  if ( ! $value || is_null($value) )
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
  return sanitize_email( $value );
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