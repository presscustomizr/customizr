<?php
/**
* Fires the pro theme : constants definition, core classes loading
* Defined in the customizr dev folder but not part of the free theme distribution
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
if ( ! class_exists( 'CZR_cl_init_pro' ) ) :
  class CZR_cl_init_pro {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $_pro_classes;

    function __construct () {
        self::$instance =& $this;
        $this -> _pro_classes = array(
          'CZR_cl_activation_key'          => array('/addons/activation-key/activation/class_activation_key.php', array(  THEMENAME, 'customizr_pro' , CUSTOMIZR_VER )),
          'CZR_cl_theme_updater'           => array('/addons/activation-key/updates/class_theme_updater.php'),
          'CZR_cl_theme_check_updates'     => array('/addons/activation-key/updates/class_theme_check_updates.php', array(  THEMENAME , 'customizr_pro' , CUSTOMIZR_VER )),
          'CZR_cl_wfc'                     => array('/addons/wfc/wordpress-font-customizer.php'),
          'CZR_cl_fpu'                     => array('/addons/fpu/tc_unlimited_featured_pages.php'),
          'CZR_cl_pro_bundle'                  => array('/addons/bundle/pc-pro-bundle.php')
        );
        //set files to load according to the context : admin / front / customize
        add_filter( 'tc_get_files_to_load_pro' , array( $this , 'tc_set_files_to_load_pro' ) );
        //END TEST PURPOSES
        //load
        $this -> tc_pro_load();
    }//end of __construct()


    /**
    * Classes instanciation
    * @return void()
    *
    */
    private function tc_pro_load() {
      $_classes = apply_filters( 'tc_get_files_to_load_pro' , $this -> _pro_classes );

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
        if ( 'CZR_cl_activation_key' == $name || 'CZR_cl_theme_check_updates' == $name )
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
    function tc_set_files_to_load_pro($_to_load) {
      if ( ! is_admin() || ( is_admin() && CZR___::$instance -> tc_is_customizing() ) ) {
          unset($_to_load['CZR_cl_activation_key']);
          unset($_to_load['CZR_cl_theme_updater']);
          unset($_to_load['CZR_cl_theme_check_updates']);
      }
      return $_to_load;
    }//end of fn


  }//end of class
endif;

//may be load pro
if ( CZR___::tc_is_pro() )
  new CZR_cl_init_pro(CZR___::$theme_name );
