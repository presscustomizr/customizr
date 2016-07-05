<?php
/**
* Loads front end stylesheets
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
if ( ! class_exists( 'CZR_cl_resources_styles' ) ) :
  class CZR_cl_resources_styles {
      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;
      public $current_random_skin;

      function __construct () {
          self::$instance =& $this;
          add_action( 'wp_enqueue_scripts'            , array( $this , 'czr_fn_enqueue_front_styles' ) );
          //Custom Stylesheets
          //Custom CSS
          add_filter('czr_user_options_style'          , array( $this , 'czr_fn_write_custom_css') , apply_filters( 'czr_custom_css_priority', 9999 ) );

          //set random skin
          add_filter ('czr_opt_tc_skin'                , array( $this, 'czr_fn_set_random_skin' ) );

      }



     /**
    * Registers and enqueues Customizr stylesheets
    * @package Customizr
    * @since Customizr 1.1
    */
    function czr_fn_enqueue_front_styles() {
          //Enqueue FontAwesome CSS
          if ( true == czr_fn_get_opt( 'tc_font_awesome_css' ) ) {
            $_path = apply_filters( 'czr_font_icons_path' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/css' );
            wp_enqueue_style( 'customizr-fa',
                $_path . '/fonts/' . CZR_cl_init::$instance -> czr_fn_maybe_use_min_style( 'font-awesome.css' ),
                array() , CUSTOMIZR_VER, 'all' );
          }

        wp_enqueue_style( 'customizr-common', CZR_cl_init::$instance -> czr_fn_get_style_src( 'common') , array() , CUSTOMIZR_VER, 'all' );
          //Customizr active skin
        wp_register_style( 'customizr-skin', CZR_cl_init::$instance -> czr_fn_get_style_src( 'skin'), array('customizr-common'), CUSTOMIZR_VER, 'all' );
        wp_enqueue_style( 'customizr-skin' );
        //Customizr stylesheet (style.css)
        wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), CUSTOMIZR_VER , 'all' );

        //Customizer user defined style options : the custom CSS is written with a high priority here
        wp_add_inline_style( 'customizr-skin', apply_filters( 'czr_user_options_style' , '' ) );
    }


    /**
    * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
    * hook : czr_user_options_style
    * @package Customizr
    * @since Customizr 2.0.7
    */
    function czr_fn_write_custom_css( $_css = null ) {
      $_css               = isset($_css) ? $_css : '';
      $tc_custom_css      = esc_html( czr_fn_get_opt( 'tc_custom_css') );
      if ( ! isset($tc_custom_css) || empty($tc_custom_css) )
        return $_css;

      return apply_filters( 'czr_write_custom_css',
        $_css . "\n" . html_entity_decode( $tc_custom_css ),
        $_css,
        czr_fn_get_opt( 'tc_custom_css')
      );
    }//end of function





    /**
    * Set random skin
    * hook czr_opt_tc_skin
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_random_skin ( $_skin ) {
      if ( false == esc_attr( czr_fn_get_opt( 'tc_skin_random' ) ) )
        return $_skin;

      //allow custom skins to be taken in account
      $_skins = apply_filters( 'czr_get_skincolor', CZR_cl_init::$instance -> skin_color_map, 'all' );

      //allow users to filter the list of skins they want to randomize
      $_skins = apply_filters( 'czr_skins_to_randomize', $_skins );

      /* Generate the random skin just once !*/
      if ( ! $this -> current_random_skin && is_array( $_skins ) )
        $this -> current_random_skin = array_rand( $_skins, 1 );

      return $this -> current_random_skin;
    }
  }//end of CZR_cl_resources
endif;
