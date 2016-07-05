<?php
/**
*
*/
if ( ! class_exists( 'CZR_cl_utils_options' ) ) :
  class CZR_cl_utils_options {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $inst;
      static $instance;
      public $default_options;
      public $db_options;
      public $options;//not used in customizer context only
      public $is_customizing;
      public $czr_options_prefixes;

      function __construct () {
        self::$inst =& $this;
        self::$instance =& $this;

        //init properties
        add_action( 'after_setup_theme'       , array( $this , 'czr_fn_init_properties') );

        //refresh the theme options right after the _preview_filter when previewing
        add_action( 'customize_preview_init'  , array( $this , 'czr_fn_customize_refresh_db_opt' ) );
      }

      /***************************
      * EARLY HOOKS
      ****************************/
      /**
      * Init CZR_cl_utils class properties after_setup_theme
      * Fixes the bbpress bug : Notice: bbp_setup_current_user was called incorrectly. The current user is being initialized without using $wp->init()
      * czr_fn_get_default_options uses is_user_logged_in() => was causing the bug
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.2.3
      */
      function czr_fn_init_properties() {
        //all customizr theme options start by "tc_" by convention
        $this -> czr_options_prefixes = apply_filters('czr_options_prefixes', array('tc_') );
        $this -> is_customizing   = CZR() -> czr_fn_is_customizing();
        $this -> db_options       = false === get_option( CZR___::$czr_option_group ) ? array() : (array)get_option( CZR___::$czr_option_group );
        $this -> default_options  = $this -> czr_fn_get_default_options();
        $_trans                   = CZR___::czr_fn_is_pro() ? 'started_using_customizr_pro' : 'started_using_customizr';

        //What was the theme version when the user started to use Customizr?
        //new install = no options yet
        //very high duration transient, this transient could actually be an option but as per the themes guidelines, too much options are not allowed.
        if ( 1 >= count( $this -> db_options ) || ! esc_attr( get_transient( $_trans ) ) ) {
          set_transient(
            $_trans,
            sprintf('%s|%s' , 1 >= count( $this -> db_options ) ? 'with' : 'before', CUSTOMIZR_VER ),
            60*60*24*9999
          );
        }
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
        $_is_czr_option = in_array( substr( $option_key, 0, 3 ), $this -> czr_options_prefixes );
        return apply_filters( 'czr_is_customizr_option', $_is_czr_option , $option_key );
      }



     /**
      * Returns the default options array
      *
      * @package Customizr
      * @since Customizr 3.1.11
      */
      function czr_fn_get_default_options() {
        $_db_opts     = empty($this -> db_options) ? $this -> czr_fn_cache_db_options() : $this -> db_options;
        $def_options  = isset($_db_opts['defaults']) ? $_db_opts['defaults'] : array();

        //Don't update if default options are not empty + customizing context
        //customizing out ? => we can assume that the user has at least refresh the default once (because logged in, see conditions below) before accessing the customizer
        //customzing => takes into account if user has set a filter or added a new customizer setting
        if ( ! empty($def_options) && $this -> is_customizing )
          return apply_filters( 'czr_default_options', $def_options );

        //Always update/generate the default option when (OR) :
        // 1) user is logged in
        // 2) they are not defined
        // 3) theme version not defined
        // 4) versions are different
        if ( is_user_logged_in() || empty($def_options) || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
          $def_options          = $this -> czr_fn_generate_default_options( CZR_cl_utils_settings_map::$instance -> czr_fn_get_customizer_map( $get_default_option = 'true' ) , 'tc_theme_options' );
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
          if(  ! $this -> czr_fn_is_customizr_option( $key ) )
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
          $option_group       = is_null($option_group) ? CZR___::$czr_option_group : $option_group;
          $saved              = empty($this -> db_options) ? $this -> czr_fn_cache_db_options() : $this -> db_options;
          $defaults           = $this -> default_options;
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
        $option_group = is_null($option_group) ? CZR___::$czr_option_group : $option_group;
        //when customizing, the db_options property is refreshed each time the preview is refreshed in 'customize_preview_init'
        $_db_options  = empty($this -> db_options) ? $this -> czr_fn_cache_db_options() : $this -> db_options;

        //do we have to use the default ?
        $__options    = $_db_options;
        $_default_val = false;
        if ( $use_default ) {
          $_defaults      = $this -> default_options;
          if ( isset($_defaults[$option_name]) )
            $_default_val = $_defaults[$option_name];
          $__options      = wp_parse_args( $_db_options, $_defaults );
        }

        //assign false value if does not exist, just like WP does
        $_single_opt    = isset($__options[$option_name]) ? $__options[$option_name] : false;

        //ctx retro compat => falls back to default val if ctx like option detected
        //important note : some options like tc_slider are not concerned by ctx
        if ( ! $this -> czr_fn_is_option_excluded_from_ctx( $option_name ) ) {
          if ( is_array( $_single_opt ) && ! class_exists( 'CZR_cl_contx' ) )
            $_single_opt = $_default_val;
        }

        //allow contx filtering globally
        $_single_opt = apply_filters( "czr_opt" , $_single_opt , $option_name , $option_group, $_default_val );

        //allow single option filtering
        return apply_filters( "czr_opt_{$option_name}" , $_single_opt , $option_name , $option_group, $_default_val );
      }



      /**
      * The purpose of this callback is to refresh and store the theme options in a property on each customize preview refresh
      * => preview performance improvement
      * 'customize_preview_init' is fired on wp_loaded, once WordPress is fully loaded ( after 'init', before 'wp') and right after the call to 'customize_register'
      * This method is fired just after the theme option has been filtered for each settings by the WP_Customize_Setting::_preview_filter() callback
      * => if this method is fired before this hook when customizing, the user changes won't be taken into account on preview refresh
      *
      * hook : customize_preview_init
      * @return  void
      *
      * @since  v3.4+
      */
      function czr_fn_customize_refresh_db_opt(){
        $this -> db_options = false === get_option( CZR___::$czr_option_group ) ? array() : (array)get_option( CZR___::$czr_option_group );
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
        $option_group           = is_null($option_group) ? CZR___::$czr_option_group : $option_group;
        $_options               = $this -> czr_fn_get_theme_options( $option_group );
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
        $opts_group = is_null($opt_group) ? CZR___::$czr_option_group : $opt_group;
        $this -> db_options = false === get_option( $opt_group ) ? array() : (array)get_option( $opt_group );
        return $this -> db_options;
      }





    /**
    * Returns a boolean
    * check if user started to use the theme before ( strictly < ) the requested version
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null ) {
      $_ispro = CZR___::czr_fn_is_pro();

      if ( $_ispro && ! get_transient( 'started_using_customizr_pro' ) )
        return false;

      if ( ! $_ispro && ! get_transient( 'started_using_customizr' ) )
        return false;

      $_trans = $_ispro ? 'started_using_customizr_pro' : 'started_using_customizr';
      $_ver   = $_ispro ? $_pro_ver : $_czr_ver;
      if ( ! $_ver )
        return false;

      $_start_version_infos = explode('|', esc_attr( get_transient( $_trans ) ) );

      if ( ! is_array( $_start_version_infos ) )
        return false;

      switch ( $_start_version_infos[0] ) {
        //in this case with now exactly what was the starting version (most common case)
        case 'with':
          return version_compare( $_start_version_infos[1] , $_ver, '<' );
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
          'last_update_notice_pro'
        )
      );
    }


    /**
    * Boolean helper : tells if this option is excluded from the ctx treatments.
    * @return bool
    */
    function czr_fn_is_option_excluded_from_ctx( $opt_name ) {
      return in_array( $opt_name, $this -> czr_fn_get_ctx_excluded_options() );
    }
}
endif;