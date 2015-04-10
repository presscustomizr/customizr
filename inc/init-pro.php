<?php
/**
* Fires the pro theme : constants definition, core classes loading
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
if ( ! class_exists( 'TC_init_pro' ) ) :
    class TC_init_pro {
        //Access any method or var of the class with classname::$instance -> var or method():
        static $instance;

        function __construct () {
            self::$instance =& $this;
            $_classes = array(
              'TC_activation_key'          => array('/addons/activation-key/activation/class_activation_key.php', array(  THEMENAME, 'customizr_pro' , CUSTOMIZR_VER )),
              'TC_theme_updater'           => array('/addons/activation-key/updates/class_theme_updater.php'),
              'TC_theme_check_updates'     => array('/addons/activation-key/updates/class_theme_check_updates.php', array(  THEMENAME , 'customizr_pro' , CUSTOMIZR_VER )),
              'TC_wfc'                     => array('/addons/wfc/wordpress-font-customizer.php'),
              'TC_fpu'                     => array('/addons/fpu/tc_unlimited_featured_pages.php'),
              'TC_fc'                      => array('/addons/fc/footer-customizer.php'),
              'TC_gc'                      => array('/addons/gc/tc_grid_customizer.php')
            );

            //loads and instanciates the activation / updates classes
            foreach ( $_classes as $name => $params ) {
                //don't load activation classes if not admin or in customize
                if ( ( TC___::$instance -> is_customizing || ! is_admin() ) && false !== strpos($params[0], 'activation-key') )
                  continue;

                $_file_path =  dirname( dirname( __FILE__ ) ) . $params[0];

                if( ! class_exists( $name ) && file_exists($_file_path) )
                    require_once ( $_file_path );

                $_args = isset( $params[1] ) ? $params[1] : null;
                //instanciates only for the following classes, the other are instanciated in their respective files.
                if ( 'TC_activation_key' == $name || 'TC_theme_check_updates' == $name )
                    new $name( $_args );
            }


        }//end of __construct()

    }//end of class
endif;

//pro version
if ( 'customizr-pro' == TC___::$theme_name )
  new TC_init_pro(TC___::$theme_name );
