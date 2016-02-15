<?php

/**
* Fires the theme : constants definition, core classes loading
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
if ( ! class_exists( 'TC___' ) ) :

  final class TC___ {
    public static $instance;//@todo make private in the future
    public $tc_core;
    public $is_customizing;
    public static $theme_name;
    public static $tc_option_group;

    public $views;//object, stores the views
    public $controllers;//object, stores the controllers

    public static function tc_instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof TC___ ) ) {
        self::$instance = new TC___();
        self::$instance -> tc_setup_constants();
        self::$instance -> tc_load();
        self::$instance -> collection = new TC_Collection();
        self::$instance -> controllers = new TC_Controllers();
        self::$instance -> helpers = new TC_Helpers();

        /* For testing purposes */
        self::$instance -> tc_register_menus();
        add_action( 'wp_enqueue_scripts', array( self::$instance, 'tc_enqueue_resources' ) );

        //register the model's map
        add_action('wp'         , array(self::$instance, 'tc_register_model_map') );
      }
      return self::$instance;
    }


    private function tc_setup_constants() {
      /* GETS INFORMATIONS FROM STYLE.CSS */
      // get themedata version wp 3.4+
      if( function_exists( 'wp_get_theme' ) ) {
        //get WP_Theme object of customizr
        $tc_theme                     = wp_get_theme();

        //Get infos from parent theme if using a child theme
        $tc_theme = $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

        $tc_base_data['prefix']       = $tc_base_data['title'] = $tc_theme -> name;
        $tc_base_data['version']      = $tc_theme -> version;
        $tc_base_data['authoruri']    = $tc_theme -> {'Author URI'};
      }

      // get themedata for lower versions (get_stylesheet_directory() points to the current theme root, child or parent)
      else {
           $tc_base_data                = call_user_func('get_' .'theme_data', get_stylesheet_directory().'/style.css' );
           $tc_base_data['prefix']      = $tc_base_data['title'];
      }

      self::$theme_name                 = sanitize_file_name( strtolower($tc_base_data['title']) );

      //CUSTOMIZR_VER is the Version
      if( ! defined( 'CUSTOMIZR_VER' ) )      define( 'CUSTOMIZR_VER' , $tc_base_data['version'] );
      //TC_BASE is the root server path of the parent theme
      if( ! defined( 'TC_BASE' ) )            define( 'TC_BASE' , get_template_directory().'/' );
      //TC_BASE_CHILD is the root server path of the child theme
      if( ! defined( 'TC_BASE_CHILD' ) )      define( 'TC_BASE_CHILD' , get_stylesheet_directory().'/' );
      //TC_BASE_URL http url of the loaded parent theme
      if( ! defined( 'TC_BASE_URL' ) )        define( 'TC_BASE_URL' , get_template_directory_uri() . '/' );
      //TC_BASE_URL_CHILD http url of the loaded child theme
      if( ! defined( 'TC_BASE_URL_CHILD' ) )  define( 'TC_BASE_URL_CHILD' , get_stylesheet_directory_uri() . '/' );
      //THEMENAME contains the Name of the currently loaded theme
      if( ! defined( 'THEMENAME' ) )          define( 'THEMENAME' , $tc_base_data['title'] );
      //TC_WEBSITE is the home website of Customizr
      if( ! defined( 'TC_WEBSITE' ) )         define( 'TC_WEBSITE' , $tc_base_data['authoruri'] );

    }//setup_contants()



    private function tc_load() {
      //load the new classes
      require_once( sprintf( '%score/class-model.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-collection.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-view.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-controllers.php' , TC_BASE ) );
      require_once( sprintf( '%score/class-helpers.php' , TC_BASE ) );
    }


    /* FOR TESTING PURPOSES */
    function tc_register_menus() {
      /* This theme uses wp_nav_menu() in one location. */
      register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );
    }

    function tc_enqueue_resources(){
      wp_enqueue_style( 'bootstrap-css', TC_BASE_URL . 'assets/bootstrap/css/bootstrap.css', array(), CUSTOMIZR_VER, 'all');
      wp_enqueue_script( 'bootstrap-js', TC_BASE_URL . 'assets/bootstrap/js/bootstrap.js', array(), CUSTOMIZR_VER, true);
    }
    /* FOR TESTING PURPOSES END */

    //hook : wp
    function tc_register_model_map() {
      foreach ($this -> tc_get_model_map() as $model ) {
        CZR() -> collection -> tc_register( $model);
      }
    }


    //returns an array of models describing the theme's views
    private function tc_get_model_map() {
      return apply_filters(
        'tc_model_map',
        array(
          /*********************************************
          * ROOT HTML STRUCTURE
          *********************************************/
          array( 'hook' => '__rooot__', 'template' => 'rooot' ),


          /*********************************************
          * HEADER
          *********************************************/
          array( 'hook' => '__before_body__', 'template' => 'header/head' ),
          array( 'hook' => '__body__', 'template' => 'header/header', 'priority' => 10 ),
          array( 'hook' => '__header__', 'template' => 'header/menu', 'priority' => 10 ),



          /*********************************************
          * CONTENT
          *********************************************/
          array( 'hook' => '__body__', 'template' => 'content/content_wrapper', 'priority' => 20, 'view_class' => 'content_wrapper' ),
          array( 'hook' => '__content__', 'id' => 'main_loop', 'template' => 'loop' ),
          //headings
          array( 'hook' => 'in_main_loop', 'template' => 'content/headings', 'priority' => 10 ),

          //page
          array( 'hook' => 'in_main_loop', 'template' => 'content/content', 'priority' => 20 ),

          //404
          array( 'hook' => 'in_main_loop', 'template' => 'content/404', 'priority' => 20 ),


          /*********************************************
          * FOOTER
          *********************************************/
          array( 'hook' => '__body__', 'template' => 'footer/footer', 'priority' => 30 ),

          //a post grid displayed in any content
          array( 'hook' => '__footer__', 'template' => 'modules/grid-wrapper', 'priority' => 20 ),
          array( 'hook' => 'in_grid_wrapper', 'id' => 'secondary_loop', 'template' => 'loop', 'query' => array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'ignore_sticky_posts' => 1 ) ),
          array( 'hook' => 'in_secondary_loop', 'template' => 'modules/grid-item' ),
        )
      );
    }


  }
endif;



//shortcut function to instanciate easier
if ( ! function_exists('tc_new') ) {
  function tc_new( $_to_load, $_args = array() ) {
    TC___::$instance -> tc__( $_to_load , $_args );
    return;
  }
}
//endif;

/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return TC___::tc_instance();
}
