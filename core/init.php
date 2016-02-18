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
        self::$instance -> tc_setup_loading();
        self::$instance -> tc_load();
        self::$instance -> collection = new TC_Collection();
        self::$instance -> controllers = new TC_Controllers();
        self::$instance -> helpers = new TC_Helpers();

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
      //TC_FRAMEWORK_PREFIX is the relative path where the framerk is located
      if( ! defined( 'TC_FRAMEWORK_PREFIX' ) ) define( 'TC_FRAMEWORK_PREFIX' , 'core/front/' );
      //TC_ASSETS_PREFIX is the relative path where the assets are located
      if( ! defined( 'TC_ASSETS_PREFIX' ) )   define( 'TC_ASSETS_PREFIX' , 'assets/' );
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

    private function tc_setup_loading() {
      //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
      $this -> tc_core = apply_filters( 'tc_core',
        array(
            'fire'      =>   array(
              array('core'       , 'init'),//defines default values (layout, socials, default slider...) and theme supports (after_setup_theme)
              array('core'       , 'plugins_compat'),//handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
              array('core/utils' , 'utils_settings_map'),//customizer setting map
              array('core/utils' , 'utils'),//helpers used everywhere
              array('core'       , 'resources'),//loads front stylesheets (skins) and javascripts
              array('core'       , 'widgets'),//widget factory
              array('core'       , 'placeholders'),//front end placeholders ajax actions for widgets, menus.... Must be fired if is_admin === true to allow ajax actions.
              array('core/back'  , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
              array('core/back'  , 'admin_page')//creates the welcome/help panel including changelog and system config
            ),
            'admin'     => array(
              array('core/back' , 'customize'),//loads customizer actions and resources
              array('core/back' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
            )
        )
      );
      //check the context
      if ( $this -> tc_is_pro() )
        require_once( sprintf( '%score/init-pro.php' , TC_BASE ) );

      self::$tc_option_group = 'tc_theme_options';

      //set files to load according to the context : admin / front / customize
      add_filter( 'tc_get_files_to_load' , array( $this , 'tc_set_files_to_load' ) );
    }


    /**
    * Class instanciation using a singleton factory :
    * Can be called to instanciate a specific class or group of classes
    * @param  array(). Ex : array ('admin' => array( array( 'inc/admin' , 'meta_boxes') ) )
    * @return  instances array()
    *
    * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
    *
    * @since Customizr 3.0
    */
    function tc_load( $_to_load = array(), $_no_filter = false ) {
      //do we apply a filter ? optional boolean can force no filter
      $_to_load = $_no_filter ? $_to_load : apply_filters( 'tc_get_files_to_load' , $_to_load );
      if ( empty($_to_load) )
        return;

      foreach ( $this -> tc_core as $group => $files )
        foreach ($files as $path_suffix ) {
          $this -> tc_require_once ( $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] . '.php');
        
          $classname = 'TC_' . $path_suffix[1];
          if ( in_array( $classname, apply_filters( 'tc_dont_instanciate_in_init', array( 'TC_nav_walker') ) ) )
            continue;
          //instanciates
          $instances = class_exists($classname)  ? new $classname : '';
        }
      
      //load the new framework classes
      $this -> tc_fw_require_once( 'class-model.php' );
      $this -> tc_fw_require_once( 'class-collection.php' );
      $this -> tc_fw_require_once( 'class-view.php' );
      $this -> tc_fw_require_once( 'class-controllers.php' );
      $this -> tc_fw_require_once( 'class-helpers.php' );
    }



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
          array( 'hook' => '__body__', 'template'   => 'header/header', 'priority' => 10 ),
          //         array( 'hook' => '__header__', 'template' => 'header/logo_title_wrapper', 'priority' => 10, 'model_class' => 'header/logo_title_wrapper' ),
          //logo
          array( 'hook' => '__header__', 'template' => 'header/logo_wrapper', 'priority' => 10, 'model_class' => 'header/logo_wrapper' ),
          array( 'hook' => '__logo_wrapper__', 'template' => 'header/logo', 'priority' => 10 , 'model_class' => 'header/logo'),
          //uses an extended logo module - IT WORKS :D
 //         array( 'hook' => '__logo_wrapper__', 'id' => 'sticky_logo', 'template' => 'header/logo', 'priority' => 10 , 'model_class' => 'header/sticky_logo'),
          array( 'hook' => '__logo_wrapper__', 'id' => 'sticky_logo', 'template' => 'header/logo', 'priority' => 10 , 'model_class' => 'header/logo', 'params' => array( 'type' => 'sticky' )),
          //title         
          array( 'hook' => '__header__', 'template' => 'header/title_wrapper', 'priority' => 10, 'model_class' => 'header/title_wrapper' ),
          array( 'hook' => '__title_wrapper__', 'template' => 'header/title', 'priority' => 10 , 'model_class' => 'header/title'),




          /*********************************************
          * CONTENT
          *********************************************/
          array( 'hook' => '__body__', 'template' => 'content/content_wrapper', 'priority' => 20, 'model_class' => 'content_wrapper' ),
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

    /***************************
    * HELPERS
    ****************************/
    /**
    * Check the context and return the modified array of class files to load and instanciate
    * hook : tc_get_files_to_load
    * @return boolean
    *
    * @since  Customizr 3.3+
    */
    function tc_set_files_to_load( $_to_load ) {
      $_to_load = empty($_to_load) ? $this -> tc_core : $_to_load;
      //Not customizing
      //1) IS NOT CUSTOMIZING : tc_is_customize_left_panel() || tc_is_customize_preview_frame() || tc_doing_customizer_ajax()
      //---1.1) IS ADMIN
      //-------1.1.a) Doing AJAX
      //-------1.1.b) Not Doing AJAX
      //---1.2) IS NOT ADMIN
      //2) IS CUSTOMIZING
      //---2.1) IS LEFT PANEL => customizer controls
      //---2.2) IS RIGHT PANEL => preview
      if ( ! $this -> tc_is_customizing() )
        {
          if ( is_admin() ) {
            //if doing ajax, we must not exclude the placeholders
            //because ajax actions are fired by admin_ajax.php where is_admin===true.
            if ( defined( 'DOING_AJAX' ) )
              $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize' ) );
            else
              $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize', 'fire|inc|placeholders' ) );
          }
          else
            //Skips all admin classes
            $_to_load = $this -> tc_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page') );
        }
      //Customizing
      else
        {
          //left panel => skip all front end classes
          if ( $this -> tc_is_customize_left_panel() ) {
            $_to_load = $this -> tc_unset_core_classes(
              $_to_load,
              array( 'header' , 'content' , 'footer' ),
              array( 'fire|inc|resources' , 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
            );
          }
          if ( $this -> tc_is_customize_preview_frame() ) {
            $_to_load = $this -> tc_unset_core_classes(
              $_to_load,
              array(),
              array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
            );
          }
        }
      return $_to_load;
    }



    /**
    * Helper
    * Alters the original classes tree
    * @param $_groups array() list the group of classes to unset like header, content, admin
    * @param $_files array() list the single file to unset.
    * Specific syntax for single files: ex in fire|inc/admin|admin_page
    * => fire is the group, inc/admin is the path, admin_page is the file suffix.
    * => will unset inc/admin/class-fire-admin_page.php
    *
    * @return array() describing the files to load
    *
    * @since  Customizr 3.0.11
    */
    public function tc_unset_core_classes( $_tree, $_groups = array(), $_files = array() ) {
      if ( empty($_tree) )
        return array();
      if ( ! empty($_groups) ) {
        foreach ( $_groups as $_group_to_remove ) {
          unset($_tree[$_group_to_remove]);
        }
      }
      if ( ! empty($_files) ) {
        foreach ( $_files as $_concat ) {
          //$_concat looks like : fire|inc|resources
          $_exploded = explode( '|', $_concat );
          //each single file entry must be a string like 'admin|inc/admin|customize'
          //=> when exploded by |, the array size must be 3 entries
          if ( count($_exploded) < 3 )
            continue;

          $gname = $_exploded[0];
          $_file_to_remove = $_exploded[2];
          if ( ! isset($_tree[$gname] ) )
            continue;
          foreach ( $_tree[$gname] as $_key => $path_suffix ) {
            if ( false !== strpos($path_suffix[1], $_file_to_remove ) )
              unset($_tree[$gname][$_key]);
          }//end foreach
        }//end foreach
      }//end if
      return $_tree;
    }//end of fn

    //called when requiring a file will - always give the precedence to the child-theme file if it exists
    function tc_get_theme_file( $path_suffix ) {
      if ( ! file_exists( $filename = TC_BASE_CHILD . $path_suffix ) )
        if ( ! file_exists( $filename = TC_BASE . $path_suffix ) )
          return false;
      
      return $filename;
    }

    //called when requiring a file url - will always give the precedence to the child-theme file if it exists
    function tc_get_theme_file_url( $url_suffix ) {
      if ( file_exists( TC_BASE_CHILD . $url_suffix ) )
        return TC_BASE_URL_CHILD . $url_suffix;
      if ( file_exists( $filename = TC_BASE . $url_suffix ) )
        return TC_BASE_URL . $url_suffix;
      
      return false;
    }

    //requires a file only if exists
    function tc_require_once( $path_suffix ) {
      if ( false !== $filename = $this -> tc_get_theme_file( $path_suffix ) ) {
        require_once( $filename );
        return true;
      }
      return false;
    }

    //requires a framework file only if exists
    function tc_fw_require_once( $path_suffix ) {
      return $this -> tc_require_once( TC_FRAMEWORK_PREFIX . $path_suffix );
    }

    /**
    * Are we in a customization context ? => ||
    * 1) Left panel ?
    * 2) Preview panel ?
    * 3) Ajax action from customizer ?
    * @return  bool
    * @since  3.2.9
    */
    function tc_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      return in_array( 1, array(
        $this -> tc_is_customize_left_panel(),
        $this -> tc_is_customize_preview_frame(),
        $this -> tc_doing_customizer_ajax()
      ) );
    }


    /**
    * Is the customizer left panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function tc_is_customize_left_panel() {
      global $pagenow;
      return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
    }


    /**
    * Is the customizer preview panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function tc_is_customize_preview_frame() {
      return ! is_admin() && isset($_REQUEST['wp_customize']);
    }


    /**
    * Always include wp_customize or customized in the custom ajax action triggered from the customizer
    * => it will be detected here on server side
    * typical example : the donate button
    *
    * @return boolean
    * @since  3.3.2
    */
    function tc_doing_customizer_ajax() {
      $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
      return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }


    /**
    * Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
    * @return boolean
    *
    * @since  Customizr 3.0.11
    */
    function tc_is_child() {
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
    * @return  boolean
    * @since  3.4+
    */
    static function tc_is_pro() {
      return file_exists( sprintf( '%sinc/init-pro.php' , TC_BASE ) ) && "customizr-pro" == self::$theme_name;
    }

  }
endif;//endif;

/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('tc_get_theme_file') ) {
  function tc_get_theme_file( $path_suffix ) {
    return TC___::$instance -> tc_get_theme_file( $path_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists('tc_get_theme_file_url') ) {
  function tc_get_theme_file_url( $url_suffix ) {
    return TC___::$instance -> tc_get_theme_file_url( $url_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists('tc_require_once') ) {
  function tc_require_once( $path_suffix ) {
    return TC___::$instance -> tc_require_once( $path_suffix );
  }
}

/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists('tc_fw_require_once') ) {
  function tc_fw_require_once( $path_suffix ) {
    return TC___::$instance -> tc_fw_require_once( $path_suffix );
  }
}
/*
 * @since 3.5.0
 */
//shortcut function to instanciate easier
if ( ! function_exists('tc_new') ) {
  function tc_new( $_to_load, $_args = array() ) {
    TC___::$instance -> tc__( $_to_load , $_args );
    return;
  }
}
/**
* The tc__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
*
* By convention in Customizr, filter hooks are used as follow :
* 1) declared with add_filters in class constructors (mainly) to hook on WP built-in callbacks or create "getters" used everywhere
* 2) declared with apply_filters in methods to make the code extensible for developers
* 3) accessed with tc__f() to return values (while front end content is handled with action hooks)
*
* Used everywhere in Customizr. Can pass up to five variables to the filter callback.
*
* @since Customizr 3.0
*/
if( ! function_exists( 'tc__f' ) ) :
    function tc__f ( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;

/**
 * @since 3.5.0
 * @return object CZR Instance
 */
function CZR() {
  return TC___::tc_instance();
}
