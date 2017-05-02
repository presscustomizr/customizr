<?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_utils_settings_map' ) ) :
  class CZR_utils_settings_map {
    static $instance;

    public $customizer_map = array();


    function __construct () {

        self::$instance =& $this;

        //TODO: what to do when these files do not exist?
        //I'm mostly thinking about server caching issues

        //require core utils settings map
        if ( file_exists( TC_BASE . 'core/utils/class-fire-utils_settings_map.php' ) ) {

          //require all the other files needed - they contain functions used in core/utils/class-fire-utils_settings_map.php
          require_once( TC_BASE . 'core/functions.php' );
          require_once( TC_BASE . 'core/utils/class-fire-utils_options.php' );
          require_once( TC_BASE . 'core/utils/class-fire-utils.php' );

          require_once( TC_BASE . 'core/utils/class-fire-utils_settings_map.php' );

        }

    }//end of construct



    /**
    * Defines sections, settings and function of customizer and return and array
    * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
    *
    * @package Customizr
    * @since Customizr 3.0
    * TODO: unify this
    */
    public function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {

      //Hook callbacks are defined in core/utils/class-fire-utils_settings_map.php
      if ( ! empty( $this -> customizer_map ) ) {
        $_customizer_map = $this -> customizer_map;
      }
      else {
        //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
        add_filter( 'tc_add_panel_map'        , 'czr_fn_popul_panels_map' );
        add_filter( 'tc_remove_section_map'   , 'czr_fn_popul_remove_section_map' );
        //theme switcher's enabled when user opened the customizer from the theme's page
        add_filter( 'tc_remove_section_map'   , 'czr_fn_set_theme_switcher_visibility' );
        add_filter( 'tc_add_section_map'      , 'czr_fn_popul_section_map' );
        //add controls to the map
        add_filter( 'tc_add_setting_control_map' , 'czr_fn_popul_setting_control_map' , 10, 2 );
        //$this -> tc_populate_setting_control_map();

        //FILTER SPECIFIC SETTING-CONTROL MAPS
        //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
        add_filter( 'czr_fn_front_page_option_map' ,'czr_fn_generates_featured_pages' );

        //MAYBE FORCE REMOVE SECTIONS (e.g. CUSTOM CSS section for wp >= 4.7 )
        add_filter( 'tc_add_section_map'           , 'czr_fn_force_remove_section_map' );


        //CACHE THE GLOBAL CUSTOMIZER MAP
        $_customizer_map = array_merge(
            array( 'add_panel'           => apply_filters( 'tc_add_panel_map', array() ) ),
            array( 'remove_section'      => apply_filters( 'tc_remove_section_map', array() ) ),
            array( 'add_section'         => apply_filters( 'tc_add_section_map', array() ) ),
            array( 'add_setting_control' => apply_filters( 'tc_add_setting_control_map', array(), $get_default ) )
        );
        $this -> customizer_map = $_customizer_map;
      }
      if ( is_null($what) ) {
        return apply_filters( 'tc_customizer_map', $_customizer_map );
      }

      $_to_return = $_customizer_map;
      switch ( $what ) {
          case 'add_panel':
            $_to_return = $_customizer_map['add_panel'];
          break;
          case 'remove_section':
            $_to_return = $_customizer_map['remove_section'];
          break;
          case 'add_section':
            $_to_return = $_customizer_map['add_section'];
          break;
          case 'add_setting_control':
            $_to_return = $_customizer_map['add_setting_control'];
          break;
      }
      return $_to_return;
    }


    /**
     * adds sanitization callback funtion : url
     * @package Customizr
     * @since Customizr 1.1.4
     * //kept for backward compatibility
     */
    function czr_fn_sanitize_url( $value) {
      $value = esc_url( $value);
      return $value;
    }

    /**
     * adds sanitization callback funtion : email
     * @package Customizr
     * @since Customizr 3.4.11
     * //kept for backward compatibility
     */
    function czr_fn_sanitize_email( $value) {
      return sanitize_email( $value );
    }

  }//end of class
endif;

?>