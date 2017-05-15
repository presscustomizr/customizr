<?php
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
        $def_options          = czr_fn_generate_default_options( czr_fn_get_customizer_map( $get_default_option = 'true' ) , 'tc_theme_options' );
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
  function czr_fn_get_theme_options ( $option_group = null ) {
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
      return apply_filters( "czr_opt_{$option_name}" , $_single_opt , $option_name , $option_group, $_default_val );
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







/***************************
* CTX COMPAT
****************************/
/**
* Helper : define a set of options not impacted by ctx like tc_slider, last_update_notice.
* @return  array of excluded option names
*/
function czr_fn_get_ctx_excluded_options() {
    return apply_filters(
      'czr_get_ctx_excluded_options',
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
* Boolean helper to check if the secondary menu is enabled
* since v3.4+
*/
function czr_fn_is_secondary_menu_enabled() {

    return (bool) esc_attr( czr_fn_get_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( czr_fn_get_opt( 'tc_menu_style' ) );
}





/**
* @return an array of font name / code OR a string of the font css code
* @parameter string name or google compliant suffix for href link
*
* @package Customizr
* @since Customizr 3.2.9
*/
function czr_fn_get_font( $_what = 'list' , $_requested = null
  ) {
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
* Boolean helper : tells if this option is excluded from the ctx treatments.
* @return bool
*/
function czr_fn_is_option_excluded_from_ctx( $opt_name ) {
    return in_array( $opt_name, czr_fn_get_ctx_excluded_options() );
}

if ( ! ( function_exists( 'czr_fn_get_raw_option' ) ) ) :
//@return an array of unfiltered options
//=> all options or a single option val
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null, $from_cache = true ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize( $alloptions );
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