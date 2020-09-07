<?php
/**
* Fires the pro theme : constants definition, core classes loading
* Defined in the customizr dev folder but not part of the free theme distribution
*
*/
if ( ! class_exists( 'CZR_init_pro' ) ) :
  class CZR_init_pro {
    //Access any method or var of the class with classname::$instance->var or method():
    static $instance;
    public $_pro_classes;
    private $_hide_pro_update_notification_for_versions;

    function __construct () {
        self::$instance =& $this;
        $this->_pro_classes = array(
          'TC_activation_key'          => array('/addons/activation-key/activation/class_activation_key.php', array(  CZR_THEMENAME, 'customizr_pro' , CUSTOMIZR_VER )),
          'TC_theme_updater'           => array('/addons/activation-key/updates/class_theme_updater.php'),
          'TC_theme_check_updates'     => array('/addons/activation-key/updates/class_theme_check_updates.php', array(  CZR_THEMENAME , 'customizr_pro' , CUSTOMIZR_VER )),
          'TC_wfc'                     => array('/addons/wfc/wordpress-font-customizer.php'),
          'TC_fpu'                     => array('/addons/fpu/tc_unlimited_featured_pages.php'),
          'PC_pro_bundle'              => array('/addons/bundle/pc-pro-bundle.php')
        );
        //set files to load according to the context : admin / front / customize
        add_filter( 'tc_get_files_to_load_pro' , array( $this , 'czr_fn_set_files_to_load_pro' ) );
        //load
        $this->czr_fn_pro_load();
        //hide update notification for a list of version
        //typically useful when several versions are released in a short time interval, to avoid hammering the wp admin dashboard with a new admin notice each time
        $this->_hide_pro_update_notification_for_versions = array( '2.1.31' );
        if( ! defined( 'DISPLAY_PRO_UPDATE_NOTIFICATION' ) ) {
            define( 'DISPLAY_PRO_UPDATE_NOTIFICATION' , ! in_array( CUSTOMIZR_VER, $this->_hide_pro_update_notification_for_versions ) );
        }
    }//end of __construct()


    /**
    * Classes instanciation
    * @return void()
    *
    */
    private function czr_fn_pro_load() {
      $_classes = apply_filters( 'tc_get_files_to_load_pro' , $this->_pro_classes );

      //loads and instantiates the activation / updates classes
      foreach ( $_classes as $name => $params ) {
        //don't load activation classes if not admin
        if ( ! is_admin() && false !== strpos($params[0], 'activation-key') )
          continue;

        $_file_path =  dirname( dirname( __FILE__ ) ) . $params[0];

        if( ! class_exists( $name ) && file_exists($_file_path) )
            require_once ( $_file_path );

        $_args = isset( $params[1] ) ? $params[1] : null;
        //instantiates only for the following classes, the other are instantiated in their respective files.
        if ( 'TC_activation_key' == $name || 'TC_theme_check_updates' == $name )
            new $name( $_args );
      }
    }


    /**
    * Helper : returns the modified array of class files to load and instantiate
    * Check the context
    * hook : tc_get_files_to_load_pro
    *
    * @return boolean
    * @since  Customizr 3.3+
    */
    function czr_fn_set_files_to_load_pro($_to_load) {
      if ( ! is_admin() || ( is_admin() && czr_fn_is_customizing() ) ) {
          unset($_to_load['TC_activation_key']);
          unset($_to_load['TC_theme_updater']);
          unset($_to_load['TC_theme_check_updates']);
      }
      return $_to_load;
    }//end of fn


  }//end of class
endif;

//Allow theme style switching via $_GET param czr_pro_modern_style when is Pro
add_filter( 'czr_is_modern_style', 'czr_fn_maybe_allow_pro_modern_style' );
if ( ! function_exists( 'czr_fn_maybe_allow_pro_modern_style' ) ) :
  function czr_fn_maybe_allow_pro_modern_style( $czr_is_modern_style ) {
    return ( isset( $_GET['czr_pro_modern_style'] ) && true == $_GET['czr_pro_modern_style'] ) ? czr_fn_is_pro() : $czr_is_modern_style;
  }
endif;