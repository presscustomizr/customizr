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
      $_is_czr_option = in_array( substr( $option_key, 0, 3 ), array( CZR_OPT_PREFIX ) );
      return apply_filters( 'czr_is_customizr_option', $_is_czr_option , $option_key );
  }



 /**
  * Returns the default options array
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
      // 1) user is logged in
      // 2) they are not defined
      // 3) theme version not defined
      // 4) versions are different
      if ( is_user_logged_in() || empty($def_options) || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
        $def_options          = czr_fn_generate_default_options( czr_fn_get_customizer_map( $get_default_option = 'true' ) , 'tc_theme_options' );
        //Adds the version in default
        $def_options['ver']   =  CUSTOMIZR_VER;

        $_db_opts['defaults'] = $def_options;
        //writes the new value in db
        update_option( "tc_theme_options" , $_db_opts );
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
      $option_group   = is_null($option_group) ? 'tc_theme_options' : $option_group;

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
      $_options               = czr_fn_get_theme_options( $option_group );
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
      $opts_group = is_null($opt_group) ? CZR_THEME_OPTIONS : $opt_group;
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
      'czr_fn_get_ctx_excluded_options',
      array(
        'defaults',
        'tc_sliders',
        'tc_blog_restrict_by_cat',
        'last_update_notice',
        'last_update_notice_pro',
        'tc_social_links'
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


//@return an array of unfiltered options
//=> all options or a single option val
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize($alloptions);
    if ( ! is_null( $opt_group ) && isset($alloptions[$opt_group]) ) {
      $alloptions = maybe_unserialize($alloptions[$opt_group]);
    }
    if ( is_null( $opt_name ) )
      return $alloptions;
    return isset( $alloptions[$opt_name] ) ? maybe_unserialize($alloptions[$opt_name]) : false;
}