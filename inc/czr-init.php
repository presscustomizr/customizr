<?php
/**
* The czr_fn__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
*
* By convention in Customizr, filter hooks are used as follow :
* 1) declared with add_filters in class constructors (mainly) to hook on WP built-in callbacks or create "getters" used everywhere
* 2) declared with apply_filters in methods to make the code extensible for developers
* 3) accessed with czr_fn__f() to return values (while front end content is handled with action hooks)
*
* Used everywhere in Customizr. Can pass up to five variables to the filter callback.
*
* @since Customizr 3.0
*/
if( ! function_exists( 'czr_fn__f' ) ) :
    function czr_fn__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;

//This function is the only one with a different prefix.
//It has been kept in the theme for retro-compatibility.
if( ! function_exists( 'tc__f' ) ) :
    function tc__f( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
       return czr_fn__f( $tag , $value, $arg_one, $arg_two , $arg_three, $arg_four, $arg_five );
    }
endif;

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
if ( ! class_exists( 'CZR___' ) ) :
  class CZR___ {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $tc_core;
    public $is_customizing;
    public static $theme_name;
    public static $tc_option_group;

    function __construct () {
      self::$instance =& $this;

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
      //CZR_WEBSITE is the home website of Customizr
      if( ! defined( 'CZR_WEBSITE' ) )         define( 'CZR_WEBSITE' , $tc_base_data['authoruri'] );

      //OPTION PREFIX //all customizr theme options start by "tc_" by convention (actually since the theme was created.. tc for Themes & Co...)
      if( ! defined( 'CZR_OPT_PREFIX' ) )           define( 'CZR_OPT_PREFIX' , apply_filters( 'czr_options_prefixes', 'tc_' ) );
      //MAIN OPTIONS NAME
      if( ! defined( 'CZR_THEME_OPTIONS' ) )        define( 'CZR_THEME_OPTIONS', apply_filters( 'czr_options_name', 'tc_theme_options' ) );

      //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
      $this -> tc_core = apply_filters( 'tc_core',
        array(
            'fire'      =>   array(
              array('inc' , 'init'),//defines default values (layout, socials, default slider...) and theme supports (after_setup_theme)
              array('inc' , 'plugins_compat'),//handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
              array('inc' , 'utils_settings_map'),//customizer setting map
              array('inc' , 'utils'),//helpers used everywhere
              array('inc' , 'init_retro_compat'),
              array('inc' , 'resources'),//loads front stylesheets (skins) and javascripts
              array('inc' , 'widgets'),//widget factory
              array('inc' , 'placeholders'),//front end placeholders ajax actions for widgets, menus.... Must be fired if is_admin === true to allow ajax actions.
              array('inc/admin' , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
              array('inc/admin' , 'admin_page')//creates the welcome/help panel including changelog and system config
            ),
            'admin'     => array(
              array('inc/admin' , 'customize'),//loads customizer actions and resources
              array('inc/admin' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
            ),
            //the following files/classes define the action hooks for front end rendering : header, main content, footer
            'header'    =>   array(
              array('inc/parts' , 'header_main'),
              array('inc/parts' , 'menu'),
              array('inc/parts' , 'nav_walker')
            ),
            'content'   =>  array(
              array('inc/parts', '404'),
              array('inc/parts', 'attachment'),
              array('inc/parts', 'breadcrumb'),
              array('inc/parts', 'comments'),
              array('inc/parts', 'featured_pages'),
              array('inc/parts', 'gallery'),
              array('inc/parts', 'headings'),
              array('inc/parts', 'no_results'),
              array('inc/parts', 'page'),
              array('inc/parts', 'post_thumbnails'),
              array('inc/parts', 'post'),
              array('inc/parts', 'post_list'),
              array('inc/parts', 'post_list_grid'),
              array('inc/parts', 'post_metas'),
              array('inc/parts', 'post_navigation'),
              array('inc/parts', 'sidebar'),
              array('inc/parts', 'slider')
            ),
            'footer'    => array(
              array('inc/parts', 'footer_main'),
            ),
            'addons'    => apply_filters( 'tc_addons_classes' , array() )
        )//end of array
      );//end of filter

      self::$tc_option_group = 'tc_theme_options';

      //set files to load according to the context : admin / front / customize
      add_filter( 'tc_get_files_to_load' , array( $this , 'czr_fn_set_files_to_load' ) );

      //theme class groups instanciation
      //$this -> czr_fn__();
      add_action('czr_load', array( $this, 'czr_fn__') );
    }//end of __construct()



    /**
    * Class instanciation using a singleton factory :
    * Can be called to instantiate a specific class or group of classes
    * @param  array(). Ex : array ('admin' => array( array( 'inc/admin' , 'meta_boxes') ) )
    * @return  instances array()
    *
    * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
    *
    * @since Customizr 3.0
    */
    function czr_fn__( $_to_load = array(), $_no_filter = false ) {
      static $instances;
      //do we apply a filter ? optional boolean can force no filter
      $_to_load = $_no_filter ? $_to_load : apply_filters( 'tc_get_files_to_load' , $_to_load );

      if ( empty($_to_load) )
        return;

      foreach ( $_to_load as $group => $files ) {
        foreach ($files as $path_suffix ) {
          //checks if a child theme is used and if the required file has to be overriden
          // if ( $this -> czr_fn_is_child() && file_exists( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ) {
          //     require_once ( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
          // }
          // else {
          //     require_once ( TC_BASE . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ;
          // }

          $classname = 'CZR_' . $path_suffix[1];
          if( ! isset( $instances[ $classname ] ) )  {
            //check if the classname can be instantiated here
            if ( in_array( $classname, apply_filters( 'tc_dont_instantiate_in_init', array( 'CZR_nav_walker') ) ) )
              continue;
            //instantiates
            $instances[ $classname ] = class_exists($classname)  ? new $classname : '';
          }
        }
      }
      return $instances[ $classname ];
    }





    /***************************
    * HELPERS
    ****************************/
    function czr_fn_req_once( $file_path ) {
        //checks if a child theme is used and if the required file has to be overriden
        if ( $this -> czr_fn_is_child() && file_exists( TC_BASE_CHILD . $file_path ) ) {
            require_once ( TC_BASE_CHILD . $file_path ) ;
        }
        else {
            require_once ( TC_BASE . $file_path ) ;
        }
    }



    /**
    * Check the context and return the modified array of class files to load and instantiate
    * hook : tc_get_files_to_load
    * @return boolean
    *
    * @since  Customizr 3.3+
    */
    function czr_fn_set_files_to_load( $_to_load ) {
      $_to_load = empty($_to_load) ? $this -> tc_core : $_to_load;
      //Not customizing
      //1) IS NOT CUSTOMIZING : czr_fn_is_customize_left_panel() || czr_fn_is_customize_preview_frame() || czr_fn_doing_customizer_ajax()
      //---1.1) IS ADMIN
      //-------1.1.a) Doing AJAX
      //-------1.1.b) Not Doing AJAX
      //---1.2) IS NOT ADMIN
      //2) IS CUSTOMIZING
      //---2.1) IS LEFT PANEL => customizer controls
      //---2.2) IS RIGHT PANEL => preview
      if ( ! $this -> czr_fn_is_customizing() )
        {
          if ( is_admin() ) {
            //load
            $this -> czr_fn_req_once( 'inc/czr-admin.php' );

            //if doing ajax, we must not exclude the placeholders
            //because ajax actions are fired by admin_ajax.php where is_admin===true.
            if ( defined( 'DOING_AJAX' ) )
              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize' ) );
            else
              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize', 'fire|inc|placeholders' ) );
          }
          else {
            //load
            $this -> czr_fn_req_once( 'inc/czr-front.php' );

            //Skips all admin classes
            $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page') );
          }
        }
      //Customizing
      else
        {
          //load
          $this -> czr_fn_req_once( 'inc/czr-admin.php' );
          $this -> czr_fn_req_once( 'inc/czr-customize.php' );

          //left panel => skip all front end classes
          if ( $this -> czr_fn_is_customize_left_panel() ) {
            $_to_load = $this -> czr_fn_unset_core_classes(
                $_to_load,
                array( 'header' , 'content' , 'footer' ),
                array( 'fire|inc|resources' , 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
            );
          }
          if ( $this -> czr_fn_is_customize_preview_frame() ) {
            //load
            $this -> czr_fn_req_once( 'inc/czr-front.php' );

            $_to_load = $this -> czr_fn_unset_core_classes(
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
    public function czr_fn_unset_core_classes( $_tree, $_groups = array(), $_files = array() ) {
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




    /**
    * Checks if we use a child theme. Uses a deprecated WP functions (get _theme_data) for versions <3.4
    * @return boolean
    *
    * @since  Customizr 3.0.11
    */
    function czr_fn_is_child() {
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
    * Are we in a customization context ? => ||
    * 1) Left panel ?
    * 2) Preview panel ?
    * 3) Ajax action from customizer ?
    * @return  bool
    * @since  3.2.9
    */
    function czr_fn_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      return in_array( 1, array(
        $this -> czr_fn_is_customize_left_panel(),
        $this -> czr_fn_is_customize_preview_frame(),
        $this -> czr_fn_doing_customizer_ajax()
      ) );
    }


    /**
    * Is the customizer left panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function czr_fn_is_customize_left_panel() {
      global $pagenow;
      return is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow;
    }


    /**
    * Is the customizer preview panel being displayed ?
    * @return  boolean
    * @since  3.3+
    */
    function czr_fn_is_customize_preview_frame() {
      return is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) );
    }


    /**
    * Always include wp_customize or customized in the custom ajax action triggered from the customizer
    * => it will be detected here on server side
    * typical example : the donate button
    *
    * @return boolean
    * @since  3.3.2
    */
    function czr_fn_doing_customizer_ajax() {
      $_is_ajaxing_from_customizer = isset( $_POST['customized'] ) || isset( $_POST['wp_customize'] );
      return $_is_ajaxing_from_customizer && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }


    /**
    * @return  boolean
    * @since  3.4+
    */
    static function czr_fn_is_pro() {
      //TC_BASE is the root server path of the parent theme
      if( ! defined( 'TC_BASE' ) )            define( 'TC_BASE' , get_template_directory().'/' );
      return class_exists( 'CZR_init_pro' ) && "customizr-pro" == self::$theme_name;
    }
  }//end of class
endif;

/* HELPERS */
//@return boolean
if ( ! function_exists( 'czr_fn_is_partial_refreshed_on' ) ) {
  function czr_fn_is_partial_refreshed_on() {
    return apply_filters( 'tc_partial_refresh_on', true );
  }
}
/* HELPER FOR CHECKBOX OPTIONS */
//used in the customizer
//replace wp checked() function
if ( ! function_exists( 'czr_fn_checked' ) ) {
  function czr_fn_checked( $val ) {
    echo $val ? 'checked="checked"' : '';
  }
}
/**
* helper
* @return  bool
*/
if ( ! function_exists( 'czr_fn_has_social_links' ) ) {
  function czr_fn_has_social_links() {
    $_socials = czr_fn_get_opt('tc_social_links');
    return ! empty( $_socials );
  }
}

/**
* helper
* Prints the social links
* @return  void
*/
if ( ! function_exists( 'czr_fn_print_social_links' ) ) {
  function czr_fn_print_social_links() {
    echo CZR_utils::$inst->czr_fn_get_social_networks();
  }
}

/**
* helper
* Renders the main header
* @return  void
*/
if ( ! function_exists( 'czr_fn_render_main_header' ) ) {
  function czr_fn_render_main_header() {
    CZR_header_main::$instance->czr_fn_set_header_options();
  ?>
    <header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>" role="banner">
    <?php
      // The '__header' hook is used with the following callback functions (ordered by priorities) :
      //CZR_header_main::$instance->tc_logo_title_display(), CZR_header_main::$instance->czr_fn_tagline_display(), CZR_header_main::$instance->czr_fn_navbar_display()
      do_action( '__header' );
    ?>
    </header>
  <?php
  }
}
/**
* helper
* Renders or returns the filtered and escaped tagline
* @return  void
*/
if ( ! function_exists( 'czr_fn_get_tagline_text' ) ) {
  function czr_fn_get_tagline_text( $echo = true ) {
    $tagline_text = apply_filters( 'tc_tagline_text', esc_attr__( get_bloginfo( 'description' ) ) );
    if ( ! $echo )
      return $tagline_text;
    echo $tagline_text;
  }
}
?><?php
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
if ( ! class_exists( 'CZR_init_pro' ) ) :
  class CZR_init_pro {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $_pro_classes;

    function __construct () {
        self::$instance =& $this;
        $this -> _pro_classes = array(
          'TC_activation_key'          => array('/addons/activation-key/activation/class_activation_key.php', array(  THEMENAME, 'customizr_pro' , CUSTOMIZR_VER )),
          'TC_theme_updater'           => array('/addons/activation-key/updates/class_theme_updater.php'),
          'TC_theme_check_updates'     => array('/addons/activation-key/updates/class_theme_check_updates.php', array(  THEMENAME , 'customizr_pro' , CUSTOMIZR_VER )),
          'TC_wfc'                     => array('/addons/wfc/wordpress-font-customizer.php'),
          'TC_fpu'                     => array('/addons/fpu/tc_unlimited_featured_pages.php'),
          'PC_pro_bundle'              => array('/addons/bundle/pc-pro-bundle.php')
        );
        //set files to load according to the context : admin / front / customize
        add_filter( 'tc_get_files_to_load_pro' , array( $this , 'czr_fn_set_files_to_load_pro' ) );
        //load
        $this -> czr_fn_pro_load();
    }//end of __construct()


    /**
    * Classes instanciation
    * @return void()
    *
    */
    private function czr_fn_pro_load() {
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
      if ( ! is_admin() || ( is_admin() && CZR___::$instance -> czr_fn_is_customizing() ) ) {
          unset($_to_load['TC_activation_key']);
          unset($_to_load['TC_theme_updater']);
          unset($_to_load['TC_theme_check_updates']);
      }
      return $_to_load;
    }//end of fn


  }//end of class
endif;
?><?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
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
if ( ! class_exists( 'CZR_init' ) ) :
  class CZR_init {
      //declares the filtered default settings
      public $global_layout;
      public $tc_thumb_size;
      public $slider_full_size;
      public $slider_size;
      public $tc_grid_full_size;
      public $tc_grid_size;
      public $skins;
      public $skin_color_map;
      public $font_pairs;
      public $font_selectors;
      public $fp_ids;
      public $socials;
      public $sidebar_widgets;
      public $footer_widgets;
      public $widgets;
      public $post_list_layout;
      public $post_formats_with_no_heading;
      public $content_404;
      public $content_no_results;
      public $default_slides;

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      public static $comments_rendered = false;

      function __construct () {

          self::$instance =& $this;
          //Default layout settings
          $this -> global_layout      = array(
                                        'r' => array(
                                            'content'       => 'span9',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( 'Right sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Right sidebar' , 'customizr' ),
                                        ),
                                        'l' => array(
                                            'content'       => 'span9',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( 'Left sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Left sidebar' , 'customizr' ),
                                        ),
                                        'b' => array(
                                            'content'       => 'span6',
                                            'sidebar'       => 'span3',
                                            'customizer'    => __( '2 sidebars : Right and Left' , 'customizr' ),
                                            'metabox'       => __( '2 sidebars : Right and Left' , 'customizr' ),
                                        ),
                                        'f' => array(
                                            'content'       => 'span12',
                                            'sidebar'       => false,
                                            'customizer'    => __( 'No sidebars : full width layout', 'customizr' ),
                                            'metabox'       => __( 'No sidebars : full width layout' , 'customizr' ),
                                        ),
          );

          //Default images sizes
          $this -> tc_thumb_size      = array( 'width' => 270 , 'height' => 250, 'crop' => true ); //size name : tc-thumb
          $this -> slider_full_size   = array( 'width' => 9999 , 'height' => 500, 'crop' => true ); //size name : slider-full
          $this -> slider_size        = array( 'width' => 1170 , 'height' => 500, 'crop' => true ); //size name : slider
          $this -> tc_grid_full_size  = array( 'width' => 1170 , 'height' => 350, 'crop' => true ); //size name : tc-grid-full
          $this -> tc_grid_size       = array( 'width' => 570 , 'height' => 350, 'crop' => true ); //size name : tc-grid


          //Default skins array
          $this -> skins              =  array(
                'blue.css'        =>  __( 'Blue' , 'customizr' ),
                'black.css'       =>  __( 'Black' , 'customizr' ),
                'black2.css'      =>  __( 'Flat black' , 'customizr' ),
                'grey.css'        =>  __( 'Grey' , 'customizr' ),
                'grey2.css'       =>  __( 'Ligth grey' , 'customizr' ),
                'purple2.css'     =>  __( 'Flat purple' , 'customizr' ),
                'purple.css'      =>  __( 'Purple' , 'customizr' ),
                'red2.css'        =>  __( 'Flat red' , 'customizr' ),
                'red.css'         =>  __( 'Red' , 'customizr' ),
                'orange.css'      =>  __( 'Orange' , 'customizr' ),
                'orange2.css'     =>  __( 'Flat orange' , 'customizr'),
                'yellow.css'      =>  __( 'Yellow' , 'customizr' ),
                'yellow2.css'     =>  __( 'Flat yellow' , 'customizr' ),
                'green.css'       =>  __( 'Green' , 'customizr' ),
                'green2.css'      =>  __( 'Light green' , 'customizr'),
                'blue3.css'       =>  __( 'Green blue' , 'customizr'),
                'blue2.css'       =>  __( 'Light blue ' , 'customizr' )
          );

          //Main skin color array : array( link color, link hover color )
          $this -> skin_color_map     = apply_filters( 'tc_skin_color_map' , array(
                'blue.css'        =>  array( '#08c', '#005580' ),
                'blue2.css'       =>  array( '#27CBCD', '#1b8b8d' ),
                'blue3.css'       =>  array( '#27CDA5', '#1b8d71' ),
                'green.css'       =>  array( '#9db668', '#768d44' ),
                'green2.css'      =>  array( '#26CE61', '#1a8d43' ),
                'yellow.css'      =>  array( '#e9a825', '#b07b12' ),
                'yellow2.css'     =>  array( '#d2d62a', '#94971d' ),
                'orange.css'      =>  array( '#F78C40', '#e16309' ),
                'orange2.css'     =>  array( '#E79B5D', '#d87220' ),
                'red.css'         =>  array( '#e10707', '#970505' ),
                'red2.css'        =>  array( '#e7797a', '#db383a' ),
                'purple.css'      =>  array( '#e67fb9', '#da3f96' ),
                'purple2.css'     =>  array( '#8183D8', '#474ac6' ),
                'grey.css'        =>  array( '#5A5A5A', '#343434' ),
                'grey2.css'       =>  array( '#E4E4E4', '#bebebe' ),
                'black.css'       =>  array( '#000', '#000000' ),
                'black2.css'      =>  array( '#394143', '#16191a' )
          ) );

          //Default fonts pairs
          $this -> font_pairs             = array(
            'gfont' => array(
              'name'  => __('Google fonts pairs' , 'customizr'),
              'list'  => apply_filters( 'tc_gfont_pairs' , array(
                '_g_sintony_poppins'              => array( 'Sintony &amp; Poppins' , 'Sintony|Poppins' ),
                '_g_fjalla_cantarell'              => array( 'Fjalla One &amp; Cantarell' , 'Fjalla+One:400|Cantarell:400' ),
                '_g_lobster_raleway'               => array( 'Lobster &amp; Raleway' , 'Lobster:400|Raleway' ),
                '_g_alegreya_roboto'               => array( 'Alegreya &amp; Roboto' , 'Alegreya:700|Roboto' ),
                '_g_lato_grand_hotel'              => array( 'Lato &amp; Grand Hotel', 'Lato:400|Grand+Hotel' ),
                '_g_dosis_opensans'                => array( 'Dosis &amp; Open Sans' , 'Dosis:400|Open+Sans' ),
                '_g_dancing_script_eb_garamond'    => array( 'Dancing Script &amp; EB Garamond' , 'Dancing+Script:700|EB+Garamond' ),
                '_g_amatic_josephin'               => array( 'Amatic SC &amp; Josefin Sans' , 'Amatic+SC|Josefin+Sans:700' ),
                '_g_oswald_droid'                  => array( 'Oswald &amp; Droid Serif' , 'Oswald:700|Droid+Serif:400' ),
                '_g_playfair_alice'                => array( 'Playfair Display &amp; Alice' , 'Playfair+Display:700|Alice' ),
                '_g_medula_abel'                   => array( 'Medula One &amp; Abel' , 'Medula+One:400|Abel' ),
                '_g_coustard_leckerli'             => array( 'Coustard Ultra &amp; Leckerli One' , 'Coustard:900|Leckerli+One' ),
                '_g_sacramento_alice'              => array( 'Sacramento &amp; Alice' , 'Sacramento:400|Alice' ),
                '_g_squada_allerta'                => array( 'Squada One &amp; Allerta' , 'Squada+One:400|Allerta' ),
                '_g_bitter_sourcesanspro'          => array( 'Bitter &amp; Source Sans Pro' , 'Bitter:400|Source+Sans+Pro' ),
                '_g_montserrat_neuton'             => array( 'Montserrat &amp; Neuton' , 'Montserrat:400|Neuton' )
              ) )
            ),
            'wsfont' => array(
              'name'  => __('Web safe fonts pairs' , 'customizr'),
              'list'  => apply_filters( 'tc_wsfont_pairs' , array(
                'impact_palatino'               => array( 'Impact &amp; Palatino' , 'Impact,Charcoal,sans-serif|Palatino Linotype,Book Antiqua,Palatino,serif'),
                'georgia_verdana'               => array( 'Georgia &amp; Verdana' , 'Georgia,Georgia,serif|Verdana,Geneva,sans-serif' ),
                'tahoma_times'                  => array( 'Tahoma &amp; Times' , 'Tahoma,Geneva,sans-serif|Times New Roman,Times,serif'),
                'lucida_courrier'               => array( 'Lucida &amp; Courrier' , 'Lucida Sans Unicode,Lucida Grande,sans-serif|Courier New,Courier New,Courier,monospace')
              ) )
            ),
           'default' => array(
            'name'  => __('Single fonts' , 'customizr'),
            'list'  => apply_filters( 'tc_single_fonts' , array(
                  '_g_poppins'                    => array( 'Poppins' , 'Poppins|Poppins' ),
                  '_g_cantarell'                  => array( 'Cantarell' , 'Cantarell:400|Cantarell:400' ),
                  '_g_raleway'                    => array( 'Raleway' , 'Raleway|Raleway' ),
                  '_g_roboto'                     => array( 'Roboto' , 'Roboto|Roboto' ),
                  '_g_grand_hotel'                => array( 'Grand Hotel', 'Grand+Hotel|Grand+Hotel' ),
                  '_g_opensans'                   => array( 'Open Sans' , 'Open+Sans|Open+Sans' ),
                  '_g_script_eb_garamond'         => array( 'EB Garamond' , 'EB+Garamond|EB+Garamond' ),
                  '_g_josephin'                   => array( 'Josefin Sans' , 'Josefin+Sans:700|Josefin+Sans:700' ),
                  '_g_droid'                      => array( 'Droid Serif' , 'Droid+Serif:400|Droid+Serif:400' ),
                  '_g_alice'                      => array( 'Alice' , 'Alice|Alice' ),
                  '_g_abel'                       => array( 'Abel' , 'Abel|Abel' ),
                  '_g_leckerli'                   => array( 'Leckerli One' , 'Leckerli+One|Leckerli+One' ),
                  '_g_allerta'                    => array( 'Allerta' , 'Allerta|Allerta' ),
                  '_g_sourcesanspro'              => array( 'Source Sans Pro' , 'Source+Sans+Pro|Source+Sans+Pro' ),
                  '_g_neuton'                     => array( 'Neuton' , 'Neuton|Neuton' ),
                  'helvetica_arial'               => array( 'Helvetica' , 'Helvetica Neue,Helvetica,Arial,sans-serif|Helvetica Neue,Helvetica,Arial,sans-serif' ),
                  'palatino'                      => array( 'Palatino Linotype' , 'Palatino Linotype,Book Antiqua,Palatino,serif|Palatino Linotype,Book Antiqua,Palatino,serif' ),
                  'verdana'                       => array( 'Verdana' , 'Verdana,Geneva,sans-serif|Verdana,Geneva,sans-serif' ),
                  'time_new_roman'                => array( 'Times New Roman' , 'Times New Roman,Times,serif|Times New Roman,Times,serif' ),
                  'courier_new'                   => array( 'Courier New' , 'Courier New,Courier New,Courier,monospace|Courier New,Courier New,Courier,monospace' )
                )
              )
            )
          );//end of font pairs

          $this -> font_selectors     = array(
            'titles' => implode(',' , apply_filters( 'tc-titles-font-selectors' , array('.site-title' , '.site-description', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
            'body'   => implode(',' , apply_filters( 'tc-body-font-selectors' , array('body' , '.navbar .nav>li>a') ) )
          );


          //Default featured pages ids
          $this -> fp_ids             = array( 'one' , 'two' , 'three' );

          //Default social networks
          $this -> socials            = array(
            'tc_rss'            => array(
                                    'link_title'    => __( 'Subscribe to my rss feed' , 'customizr' ),
                                    'default'       => get_bloginfo( 'rss_url' ) //kept as it's the only one used in the transition
                                ),
            'tc_email'          => array(
                                    'link_title'    => __( 'E-mail' , 'customizr' ),
                                  ),
            'tc_twitter'        => array(
                                    'link_title'    => __( 'Follow me on Twitter' , 'customizr' ),
                                  ),
            'tc_facebook'       => array(
                                    'link_title'    => __( 'Follow me on Facebook' , 'customizr' ),
                                  ),
            'tc_google'         => array(
                                    'link_title'    => __( 'Follow me on Google+' , 'customizr' ),
                                  ),
            'tc_instagram'      => array(
                                    'link_title'    => __( 'Follow me on Instagram' , 'customizr' ),
                                  ),
            'tc_tumblr'       => array(
                                    'link_title'    => __( 'Follow me on Tumblr' , 'customizr' ),
                                  ),
            'tc_flickr'       => array(
                                    'link_title'    => __( 'Follow me on Flickr' , 'customizr' ),
                                  ),
            'tc_wordpress'      => array(
                                    'link_title'    => __( 'Follow me on WordPress' , 'customizr' ),
                                  ),
            'tc_youtube'        => array(
                                    'link_title'    => __( 'Follow me on Youtube' , 'customizr' ),
                                  ),
            'tc_pinterest'      => array(
                                    'link_title'    => __( 'Pin me on Pinterest' , 'customizr' ),
                                  ),
            'tc_github'         => array(
                                    'link_title'    => __( 'Follow me on Github' , 'customizr' ),
                                  ),
            'tc_dribbble'       => array(
                                    'link_title'    => __( 'Follow me on Dribbble' , 'customizr' ),
                                  ),
            'tc_linkedin'       => array(
                                    'link_title'    => __( 'Follow me on LinkedIn' , 'customizr' ),
                                  ),
            'tc_vk'             => array(
                                    'link_title'    => __( 'Follow me on VKontakte' , 'customizr' ),
                                  ),
            'tc_yelp'           => array(
                                    'link_title'    => __( 'Follow me on Yelp' , 'customizr' ),
                                  ),
            'tc_xing'           => array(
                                    'link_title'    => __( 'Follow me on Xing' , 'customizr' ),
                                  ),
            'tc_snapchat'       => array(
                                    'link_title'    => __( 'Contact me on Snapchat' , 'customizr' ),
                                  )
          );//end of social array


          //Default sidebar widgets
          $this -> sidebar_widgets    = array(
            'left'          => array(
                            'name'                 => __( 'Left Sidebar' , 'customizr' ),
                            'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
            ),
            'right'         => array(
                            'name'                 => __( 'Right Sidebar' , 'customizr' ),
                            'description'          => __( 'Appears on posts, static pages, archives and search pages' , 'customizr' )
            )
          );//end of array

          //Default footer widgets
          $this -> footer_widgets     = array(
            'footer_one'    => array(
                            'name'                 => __( 'Footer Widget Area One' , 'customizr' ),
                            'description'          => __( 'Just use it as you want !' , 'customizr' )
            ),
            'footer_two'    => array(
                            'name'                 => __( 'Footer Widget Area Two' , 'customizr' ),
                            'description'          => __( 'Just use it as you want !' , 'customizr' )
            ),
            'footer_three'   => array(
                            'name'                 => __( 'Footer Widget Area Three' , 'customizr' ),
                            'description'          => __( 'Just use it as you want !' , 'customizr' )
            )
          );//end of array

          //Default post list layout
          $this -> post_list_layout   = array(
            'content'           => 'span8',
            'thumb'             => 'span4',
            'show_thumb_first'  => false,
            'alternate'         => true
          );

          //Defines post formats with no headers
          $this -> post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

          //Default 404 content
          $this -> content_404        = array(
            'quote'             => __( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ),
            'author'            => __( 'George Orwell' , 'customizr' ),
            'text'              => __( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' )
          );

          //Default no search result content
          $this -> content_no_results = array(
            'quote'             => __( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ),
            'author'            => __( 'Sir Winston Churchill' , 'customizr' ),
            'text'              => __( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' , 'customizr' )
          );

          //Default slides content
          $this -> default_slides     = array(
            1 => array(
              'title'         =>  '',
              'text'          =>  '',
              'button_text'   =>  '',
              'link_id'       =>  null,
              'link_url'      =>  null,
              'active'        =>  'active',
              'color_style'   =>  '',
              'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                          TC_BASE_URL.'assets/front/img/customizr-theme.jpg',
                                          __( 'Customizr is a clean responsive theme' , 'customizr' )
                                  )
            ),

            2 => array(
              'title'         =>  '',
              'text'          =>  '',
              'button_text'   =>  '',
              'link_id'       =>  null,
              'link_url'      =>  null,
              'active'        =>  '',
              'color_style'   =>  '',
              'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                          TC_BASE_URL.'assets/front/img/demo_slide_2.jpg',
                                          __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                  )
            )
          );///end of slides array

          //Set image options set by user @since v3.2.0
          //! must be included in utils to be available in admin for plugins like regenerate thumbnails
          add_action( 'after_setup_theme'                      , array( $this, 'czr_fn_set_user_defined_settings'));

          //add the text domain, various theme supports : editor style, automatic-feed-links, post formats, post-thumbnails
          add_action( 'after_setup_theme'                      , array( $this , 'czr_fn_customizr_setup' ));
          //registers the menu
          add_action( 'after_setup_theme'                       , array( $this, 'czr_fn_register_menus'));

          //add retina support for high resolution devices
          add_filter( 'wp_generate_attachment_metadata'        , array( $this , 'czr_fn_add_retina_support') , 10 , 2 );
          add_filter( 'delete_attachment'                      , array( $this , 'czr_fn_clean_retina_images') );

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter('body_class'                              , array( $this , 'czr_fn_set_body_classes') );

          //prevent rendering the comments template more than once
          add_filter( 'tc_render_comments_template'            , array( $this,  'czr_fn_control_coments_template_rendering' ) );
      }//end of constructor



      /**
      * Set user defined options for images
      * Thumbnail's height
      * Slider's height
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function czr_fn_set_user_defined_settings() {
        $_options = get_option('tc_theme_options');
        //add "rectangular" image size
        if ( isset ( $_options['tc_post_list_thumb_shape'] ) && false !== strpos(esc_attr( $_options['tc_post_list_thumb_shape'] ), 'rectangular') ) {
          $_user_height     = isset ( $_options['tc_post_list_thumb_height'] ) ? esc_attr( $_options['tc_post_list_thumb_height'] ) : '250';
          $_user_height     = ! esc_attr( $_options['tc_post_list_thumb_shape'] ) ? '250' : $_user_height;
          $_rectangular_size    = apply_filters(
            'tc_rectangular_size' ,
            array( 'width' => '1170' , 'height' => $_user_height , 'crop' => true )
          );
          add_image_size( 'tc_rectangular_size' , $_rectangular_size['width'] , $_rectangular_size['height'], $_rectangular_size['crop'] );
        }

        if ( isset ( $_options['tc_slider_change_default_img_size'] ) && 0 != esc_attr( $_options['tc_slider_change_default_img_size'] ) && isset ( $_options['tc_slider_default_height'] ) && 500 != esc_attr( $_options['tc_slider_default_height'] ) ) {
            add_filter( 'tc_slider_full_size'    , array($this,  'czr_fn_set_slider_img_height') );
            add_filter( 'tc_slider_size'         , array($this,  'czr_fn_set_slider_img_height') );
        }


        /***********
        *** GRID ***
        ***********/
        if ( isset( $_options['tc_grid_thumb_height'] ) ) {
            $_user_height  = esc_attr( $_options['tc_grid_thumb_height'] );

        }
        $tc_grid_full_size     = $this -> tc_grid_full_size;
        $tc_grid_size          = $this -> tc_grid_size;
        $_user_grid_height     = isset( $_options['tc_grid_thumb_height'] ) && is_numeric( $_options['tc_grid_thumb_height'] ) ? esc_attr( $_options['tc_grid_thumb_height'] ) : $tc_grid_full_size['height'];

        add_image_size( 'tc-grid-full', $tc_grid_full_size['width'], $_user_grid_height, $tc_grid_full_size['crop'] );
        add_image_size( 'tc-grid', $tc_grid_size['width'], $_user_grid_height, $tc_grid_size['crop'] );

        if ( $_user_grid_height != $tc_grid_full_size['height'] )
          add_filter( 'tc_grid_full_size', array( $this,  'czr_fn_set_grid_img_height') );
        if ( $_user_grid_height != $tc_grid_size['height'] )
          add_filter( 'tc_grid_size'     , array( $this,  'czr_fn_set_grid_img_height') );

      }



      /**
      * Set slider new image sizes
      * Callback of slider_full_size and slider_size filters
      * hook : might be called from after_setup_theme
      * @package Customizr
      * @since Customizr 3.2.0
      *
      */
      function czr_fn_set_slider_img_height( $_default_size ) {
        $_options = get_option('tc_theme_options');

        $_default_size['height'] = esc_attr( $_options['tc_slider_default_height'] );
        return $_default_size;
      }


      /**
      * Set post list desgin new image sizes
      * Callback of tc_grid_full_size and tc_grid_size filters
      *
      * @package Customizr
      * @since Customizr 3.1.12
      *
      */
      function czr_fn_set_grid_img_height( $_default_size ) {
        $_options = get_option('tc_theme_options');

        $_default_size['height'] =  esc_attr( $_options['tc_grid_thumb_height'] ) ;
        return $_default_size;
      }



      /**
       * Sets up theme defaults and registers the various WordPress features
       * hook : after_setup_theme | 20
       *
       * @package Customizr
       * @since Customizr 1.0
       */

      function czr_fn_customizr_setup() {
        /* Set default content width for post images and media. */
        global $content_width;
        if (! isset( $content_width ) )
          $content_width = apply_filters( 'tc_content_width' , 1170 );

        /*
         * Makes Customizr available for translation.
         * Translations can be added to the /inc/lang/ directory.
         */
        load_theme_textdomain( 'customizr' , CZR___::czr_fn_is_pro() ? TC_BASE . '/inc/lang_pro' : TC_BASE . '/inc/lang' );

        /* Adds RSS feed links to <head> for posts and comments. */
        add_theme_support( 'automatic-feed-links' );

        /*  This theme supports nine post formats. */
        $post_formats   = apply_filters( 'tc_post_formats', array( 'aside' , 'gallery' , 'link' , 'image' , 'quote' , 'status' , 'video' , 'audio' , 'chat' ) );
        add_theme_support( 'post-formats' , $post_formats );

        /* support for page excerpt (added in v3.0.15) */
        add_post_type_support( 'page', 'excerpt' );

        /* This theme uses a custom image size for featured images, displayed on "standard" posts. */
        add_theme_support( 'post-thumbnails' );
          //set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop

        /* @since v3.2.3 see : https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/ */
        add_theme_support( 'title-tag' );
        //remove theme support => generates notice in admin @todo fix-it!
         /* remove_theme_support( 'custom-background' );
          remove_theme_support( 'custom-header' );*/

        //post thumbnails for featured pages and post lists (archive, search, ...)
        $tc_thumb_size    = apply_filters( 'tc_thumb_size' , $this -> tc_thumb_size );
        add_image_size( 'tc-thumb' , $tc_thumb_size['width'] , $tc_thumb_size['height'], $tc_thumb_size['crop'] );

        //slider full width
        $slider_full_size = apply_filters( 'tc_slider_full_size' , $this -> slider_full_size );
        add_image_size( 'slider-full' , $slider_full_size['width'] , $slider_full_size['height'], $slider_full_size['crop'] );

        //slider boxed
        $slider_size      = apply_filters( 'tc_slider_size' , $this -> slider_size );
        add_image_size( 'slider' , $slider_size['width'] , $slider_size['height'], $slider_size['crop'] );

        //add support for svg and svgz format in media upload
        add_filter( 'upload_mimes'                        , array( $this , 'czr_fn_custom_mtypes' ) );

        //add help button to admin bar
        add_action ( 'wp_before_admin_bar_render'         , array( $this , 'czr_fn_add_help_button' ));

      }



      /*
      * hook : after_setup_theme
      */
      function czr_fn_register_menus() {
        /* This theme uses wp_nav_menu() in one location. */
        register_nav_menu( 'main' , __( 'Main Menu' , 'customizr' ) );
        register_nav_menu( 'secondary' , __( 'Secondary (horizontal) Menu' , 'customizr' ) );
      }




      /**
      * Returns the active path+skin.css or tc_common.css
      *
      * @package Customizr
      * @since Customizr 3.0.15
      */
      function czr_fn_get_style_src( $_wot = 'skin' ) {
        $_sheet    = ( 'skin' == $_wot ) ? esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_skin' ) ) : 'tc_common.css';
        $_sheet    = $this -> czr_fn_maybe_use_min_style( $_sheet );

        //Finds the good path : are we in a child theme and is there a skin to override?
        $remote_path    = ( CZR___::$instance -> czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/' : false ;
        $remote_path    = ( ! $remote_path && file_exists(TC_BASE .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/' : $remote_path ;
        //Checks if there is a rtl version of common if needed
        if ( 'skin' != $_wot && ( is_rtl() || ( defined( 'WPLANG' ) && ( 'ar' == WPLANG || 'he_IL' == WPLANG ) ) ) ){
          $remote_rtl_path   = ( CZR___::$instance -> czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/rtl/' : false ;
          $remote_rtl_path   = ( ! $remote_rtl_path && file_exists(TC_BASE .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/rtl/' : $remote_rtl_path;
          $remote_path       = $remote_rtl_path ? $remote_rtl_path : $remote_path;
        }

        //Defines the active skin and fallback to blue.css if needed
        if ( 'skin' == $_wot )
          $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/grey.css';
        else
          $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/tc_common.css';

        return apply_filters ( 'tc_get_style_src' , $tc_get_style_src , $_wot );
      }



      /**
      * //Move in CZR_utils?
      *
      * Returns the min or normal version of the passed css filename (basename.type)
      * depending on whether or not the minified version should be used
      *
      * @param $_sheet string
      *
      * @return string
      *
      * @package Customizr
      * @since Customizr 3.4.19
      */
      function czr_fn_maybe_use_min_style( $_sheet ) {
        if ( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_minified_skin' ) ) )
          $_sheet = ( defined('CZR_NOT_MINIFIED_CSS') && true === CZR_NOT_MINIFIED_CSS ) ? $_sheet : str_replace('.css', '.min.css', $_sheet);
        return $_sheet;
      }



      /**
      * Returns the $mimes array with svg and svgz entries added
      *
      * @package Customizr
      * @since Customizr 3.1.19
      */
      function czr_fn_custom_mtypes( $mimes ) {
        if (! apply_filters( 'tc_add_svg_mime_type' , true ) )
          return $mimes;

        $mimes['svg']   = 'image/svg+xml';
        $mimes['svgz']  = 'image/svg+xml';
        return $mimes;
      }



      /**
     * This function handles the support for high resolution devices
     *
     * @hook wp_generate_attachment_metadata (10 ,2)
     * @package Customizr
     * @since Customizr 3.0.15
     * @credits http://wp.tutsplus.com/author/chrisbavota/
     */
      function czr_fn_add_retina_support( $metadata, $attachment_id ) {
        //checks if retina is enabled in options
        if ( 0 == CZR_utils::$inst->czr_fn_opt( 'tc_retina_support' ) )
          return $metadata;

        if ( ! is_array($metadata) )
          return $metadata;

        //Create the retina image for the main file
        if ( is_array($metadata) && isset($metadata['width']) && isset($metadata['height']) )
          $this -> czr_fn_create_retina_images( get_attached_file( $attachment_id ), $metadata['width'], $metadata['height'] , false, $_is_intermediate = false );

        //Create the retina images for each WP sizes
        foreach ( $metadata as $key => $data ) {
            if ( 'sizes' != $key )
              continue;
            foreach ( $data as $_size_name => $_attr ) {
                if ( is_array( $_attr ) && isset($_attr['width']) && isset($_attr['height']) )
                    $this -> czr_fn_create_retina_images( get_attached_file( $attachment_id ), $_attr['width'], $_attr['height'], true, $_is_intermediate = true );
            }
        }
        return $metadata;
      }//end of tc_retina_support



      /**
      * Creates retina-ready images
      *
      * @package Customizr
      * @since Customizr 3.0.15
      * @credits http://wp.tutsplus.com/author/chrisbavota/
      */
      function czr_fn_create_retina_images( $file, $width, $height, $crop = false , $_is_intermediate = true) {
          $resized_file = wp_get_image_editor( $file );
          if ( is_wp_error( $resized_file ) )
            return false;

          if ( $width || $height ) {
            $_suffix    = $_is_intermediate ? $width . 'x' . $height . '@2x' : '@2x';
            $filename   = $resized_file -> generate_filename( $_suffix );
            // if is not intermediate (main file name) => removes the "-" added by the generate_filename method
            $filename   = ! $_is_intermediate ? str_replace('-@2x', '@2x', $filename) : $filename;

            $resized_file -> resize( $width * 2, $height * 2, $crop );
            $resized_file -> save( $filename );

            $info = $resized_file -> get_size();

            /*return array(
                'file' => wp_basename( $filename ),
                'width' => $info['width'],
                'height' => $info['height'],
            );*/
          }
          //return false;
      }//end of function




      /**
     * This function deletes the generated retina images if they exist
     *
     * @hook delete_attachment
     * @package Customizr
     * @since Customizr 3.0.15
     * @credits http://wp.tutsplus.com/author/chrisbavota/
     */
      function czr_fn_clean_retina_images( $attachment_id ) {
        $meta = wp_get_attachment_metadata( $attachment_id );
        if ( !isset( $meta['file']) )
          return;

        $upload_dir = wp_upload_dir();
        $path = pathinfo( $meta['file'] );
        $sizes = $meta['sizes'];
        // append to the sizes the original file
        $sizes['original'] = array( 'file' => $path['basename'] );

        foreach ( $sizes as $size ) {
          $original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
          $retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );

          if ( file_exists( $retina_filename ) )
            unlink( $retina_filename );
        }
      }//end of function


      /**
      * Add help button
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_add_help_button() {
         if ( current_user_can( 'edit_theme_options' ) ) {
           global $wp_admin_bar;
           $wp_admin_bar->add_menu( array(
             'parent' => 'top-secondary', // Off on the right side
             'id' => 'tc-customizr-help' ,
             'title' =>  __( 'Help' , 'customizr' ),
             'href' => admin_url( 'themes.php?page=welcome.php&help=true' ),
             'meta'   => array(
                'title'  => __( 'Need help with Customizr? Click here!', 'customizr' ),
              ),
           ));
         }
      }



      /**
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_body_classes( $_classes ) {
        if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_link_hover_effect' ) ) )
          array_push( $_classes, 'tc-fade-hover-links' );
        if ( CZR___::$instance -> czr_fn_is_customizing() )
          array_push( $_classes, 'is-customizing' );
        if ( wp_is_mobile() )
          array_push( $_classes, 'tc-is-mobile' );
        if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_enable_dropcap' ) ) )
          array_push( $_classes, esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_dropcap_design' ) ) );

        //adds the layout
        $_layout = CZR_utils::czr_fn_get_layout( CZR_utils::czr_fn_id() , 'sidebar' );
        if ( in_array( $_layout, array('b', 'l', 'r' , 'f') ) ) {
          array_push( $_classes, sprintf( 'tc-%s-sidebar',
            'f' == $_layout ? 'no' : $_layout
          ) );
        }

        //IMAGE CENTERED
        if ( (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_img') ) ){
          $_classes = array_merge( $_classes , array( 'tc-center-images' ) );
        }

        //SKIN CLASS
        $_skin = sprintf( 'skin-%s' , basename( $this -> czr_fn_get_style_src() ) );
        array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

        return $_classes;
      }


      /**
      * Controls the rendering of the comments template
      *
      * @param bool $bool
      * @return bool $bool
      * hook : tc_render_comments_template
      *
      */
      function czr_fn_control_coments_template_rendering( $bool ) {
        $_to_return = !self::$comments_rendered && $bool;
        self::$comments_rendered = true;
        return $_to_return;
      }

  }//end of class
endif;

?><?php
/**
* Handles various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
*
* @package      Customizr
* @subpackage   classes
* @since        3.3+
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_plugins_compat' ) ) :
  class CZR_plugins_compat {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    //credits @Srdjan
    public $default_language, $current_language;

    function __construct () {

      self::$instance =& $this;
      //add various plugins compatibilty (Jetpack, Bbpress, Qtranslate, Woocommerce, The Event Calendar ...)
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_set_plugins_supported'), 20 );
      add_action ('after_setup_theme'          , array( $this , 'czr_fn_plugins_compatibility'), 30 );
      // remove qtranslateX theme options filter
      remove_filter('option_tc_theme_options', 'qtranxf_translate_option', 5);

    }//end of constructor



    /**
    * Set plugins supported ( before the plugin compat function is fired )
    * => allows to easily remove support by firing remove_theme_support() (with a priority < tc_plugins_compatibility) on hook 'after_setup_theme'
    * hook : after_setup_theme
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_plugins_supported() {
      //add support for plugins (added in v3.1+)
      add_theme_support( 'jetpack' );
      add_theme_support( 'bbpress' );
      add_theme_support( 'buddy-press' );
      add_theme_support( 'qtranslate-x' );
      add_theme_support( 'polylang' );
      add_theme_support( 'wpml' );
      add_theme_support( 'woocommerce' );
      add_theme_support( 'the-events-calendar' );
      add_theme_support( 'optimize-press' );
      add_theme_support( 'woo-sensei' );
      add_theme_support( 'visual-composer' );//or js-composer as they call it
      add_theme_support( 'disqus' );
      add_theme_support( 'uris' );
      add_theme_support( 'tc-unlimited-featured-pages' );
    }



    /**
    * This function handles the following plugins compatibility : Jetpack (for the carousel addon and photon), Bbpress, Qtranslate, Woocommerce
    *
    * @package Customizr
    * @since Customizr 3.0.15
    */
    function czr_fn_plugins_compatibility() {
      /* Unlimited Featured Pages  */
      if ( current_theme_supports( 'tc-unlimited-featured-pages' ) && $this -> czr_fn_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php') )
        $this -> czr_fn_set_tc_unlimited_featured_pages_compat();

      /* JETPACK */
      //adds compatibilty with the jetpack image carousel and photon
      if ( current_theme_supports( 'jetpack' ) && $this -> czr_fn_is_plugin_active('jetpack/jetpack.php') )
        $this -> czr_fn_set_jetpack_compat();


      /* BBPRESS */
      //if bbpress is installed and activated, we can check the existence of the contextual boolean function is_bbpress() to execute some code
      if ( current_theme_supports( 'bbpress' ) && $this -> czr_fn_is_plugin_active('bbpress/bbpress.php') )
        $this -> czr_fn_set_bbpress_compat();

      /* BUDDYPRESS */
      //if buddypress is installed and activated, we can check the existence of the contextual boolean function is_buddypress() to execute some code
      // we have to use buddy-press instead of buddypress as string for theme support as buddypress makes some checks on current_theme_supports('buddypress') which result in not using its templates
      if ( current_theme_supports( 'buddy-press' ) && $this -> czr_fn_is_plugin_active('buddypress/bp-loader.php') )
        $this -> czr_fn_set_buddypress_compat();

      /*
      * QTranslatex
      * Credits : @acub, http://websiter.ro
      */
      if ( current_theme_supports( 'qtranslate-x' ) && $this -> czr_fn_is_plugin_active('qtranslate-x/qtranslate.php') )
        $this -> czr_fn_set_qtranslatex_compat();

      /*
      * Polylang
      * Credits : Rocco Aliberti
      */
      if ( current_theme_supports( 'polylang' ) && $this -> czr_fn_is_plugin_active('polylang/polylang.php') )
        $this -> czr_fn_set_polylang_compat();

      /*
      * WPML
      */
      if ( current_theme_supports( 'wpml' ) && $this -> czr_fn_is_plugin_active('sitepress-multilingual-cms/sitepress.php') )
        $this -> czr_fn_set_wpml_compat();

      /* The Events Calendar */
      if ( current_theme_supports( 'the-events-calendar' ) && $this -> czr_fn_is_plugin_active('the-events-calendar/the-events-calendar.php') )
        $this -> czr_fn_set_the_events_calendar_compat();

      /* Optimize Press */
      if ( current_theme_supports( 'optimize-press' ) && $this -> czr_fn_is_plugin_active('optimizePressPlugin/optimizepress.php') )
        $this -> czr_fn_set_optimizepress_compat();

      /* Woocommerce */
      if ( current_theme_supports( 'woocommerce' ) && $this -> czr_fn_is_plugin_active('woocommerce/woocommerce.php') )
        $this -> czr_fn_set_woocomerce_compat();

      /* Sensei woocommerce addon */
      if ( current_theme_supports( 'woo-sensei') && $this -> czr_fn_is_plugin_active('woothemes-sensei/woothemes-sensei.php') )
        $this -> czr_fn_set_sensei_compat();

      /* Visual Composer */
      if ( current_theme_supports( 'visual-composer') && $this -> czr_fn_is_plugin_active('js_composer/js_composer.php') )
        $this -> czr_fn_set_vc_compat();

      /* Disqus Comment System */
      if ( current_theme_supports( 'disqus') && $this -> czr_fn_is_plugin_active('disqus-comment-system/disqus.php') )
        $this -> czr_fn_set_disqus_compat();

      /* Ultimate Responsive Image Slider  */
      if ( current_theme_supports( 'uris' ) && $this -> czr_fn_is_plugin_active('ultimate-responsive-image-slider/ultimate-responsive-image-slider.php') )
        $this -> czr_fn_set_uris_compat();
    }//end of plugin compatibility function



    /**
    * Jetpack compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_jetpack_compat() {
      //jetpack image carousel
      //this filter doesn't exist anymore it has been replaced by
      //tc_is_gallery_enabled
      //I think we can remove the following compatibility as everything seems to work (considering that it doesn't do anything atm)
      //and we haven't received any complain
      //Also we now have a whole gallery section of settings and we coul redirect users there to fine tune it
      add_filter( 'tc_gallery_bool', '__return_false' );

      //Photon jetpack's module conflicts with our smartload feature:
      //Photon removes the width,height attribute in php, then in js it compute them (when they have the special attribute 'data-recalc-dims')
      //based on the img src. When smartload is enabled the images parsed by its js which are not already smartloaded are dummy
      //and their width=height is 1. The image is correctly loaded but the space
      //assigned to it will be 1x1px. Photon js, is compatible with Auttomatic plugin lazy load and it sets the width/height
      //attribute only when the img is smartloaded. This is pretty useless to me, as it doesn't solve the main issue:
      //document's height change when the img are smartloaded.
      //Anyway to avoid the 1x1 issue we alter the img attribute (data-recalc-dims) which photon adds to the img tag(php) so
      //the width/height will not be erronously recalculated
      if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) )
        add_filter( 'tc_img_smartloaded', 'czr_fn_jp_smartload_img');
      function czr_fn_jp_smartload_img( $img ) {
        return str_replace( 'data-recalc-dims', 'data-tcjp-recalc-dims', $img );
      }
    }//end jetpack compat




    /**
    * BBPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_bbpress_compat() {
      if ( ! function_exists( 'czr_fn_bbpress_disable_feature' ) ) {
        function czr_fn_bbpress_disable_feature( $bool ) {
          return ( function_exists('is_bbpress') && is_bbpress() ) ? false : $bool;
        }
      }

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_bbpress_disable_feature');
      //disables thumbnails and excerpt for post lists
      add_filter( 'tc_show_post_list_thumb', 'czr_fn_bbpress_disable_feature' );
      //show full content in post lists
      add_filter( 'tc_show_excerpt', 'czr_fn_bbpress_disable_feature' );
      //disables Customizr author infos on forums
      add_filter( 'tc_show_author_metas_in_post', 'czr_fn_bbpress_disable_feature' );
      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_bbpress_disable_feature' );
      //disables post metas
      add_filter( 'tc_show_post_metas', 'czr_fn_bbpress_disable_feature', 100);
      //disable the grid
      add_filter( 'tc_set_grid_hooks' , 'czr_fn_bbpress_disable_feature', 100 );
      //disable the smartload help block
      add_filter( 'tc_is_img_smartload_help_on', 'czr_fn_bbpress_disable_feature' );
    }

    /**
    * BuddyPress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_buddypress_compat() {
      add_filter( 'tc_are_comments_enabled', 'czr_fn_buddypress_disable_comments' );
      function czr_fn_buddypress_disable_comments($bool){
        return ( is_page() && function_exists('is_buddypress') && is_buddypress() ) ? false : $bool;
      }
      //disable smartload in change-avatar buddypress profile page
      //to avoid the img tag (in a template loaded with backbone) being parsed on server side but
      //not correctly processed by the front js.
      //the action hook "xprofile_screen_change_avatar" is a buddypress specific hook
      //fired before wp_head where we hook tc_parse_imgs
      //side-effect: all the images in this pages will not be smartloaded, this isn't a big deal
      //as there should be at maximum 2 images there:
      //1) the avatar, if already set
      //2) a cover image, if already set
      //anyways this page is not a regular "front" page as it pertains more to a "backend" side
      //if we can call it that way.
      add_action( 'xprofile_screen_change_avatar', 'czr_fn_buddypress_maybe_disable_img_smartload' );
      function czr_fn_buddypress_maybe_disable_img_smartload() {
        add_filter( 'tc_opt_tc_img_smart_load', '__return_false' );
      }
    }

    /**
    * QtranslateX compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_qtranslatex_compat() {
      function czr_fn_url_lang($url) {
        return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
      }
      function czr_fn_apply_qtranslate ($text) {
        return call_user_func(  '__' , $text );
      }
      function czr_fn_remove_char_limit() {
        return 99999;
      }
      function czr_fn_change_transport( $value , $set ) {
        return ('transport' == $set) ? 'refresh' : $value;
      }

      //outputs correct urls for current language : in logo, slider
      foreach ( array( 'tc_slide_link_url', 'tc_logo_link_url') as $filter )
        add_filter( $filter, 'czr_fn_url_lang' );

      //outputs the qtranslate translation for slider
      foreach ( array( 'tc_slide_title', 'tc_slide_text', 'tc_slide_button_text', 'tc_slide_background_alt' ) as $filter )
        add_filter( $filter, 'czr_fn_apply_qtranslate' );
      //sets no character limit for slider (title, lead text and button title) => allow users to use qtranslate tags for as many languages they wants ([:en]English text[:de]German text...and so on)
      foreach ( array( 'tc_slide_title_length', 'tc_slide_text_length', 'tc_slide_button_length' ) as $filter )
        add_filter( $filter  , 'czr_fn_remove_char_limit');

      //outputs the qtranslate translation for archive titles;
      $tc_archive_titles = array( 'tag_archive', 'category_archive', 'author_archive', 'search_results');
      foreach ( $tc_archive_titles as $title )
        add_filter("tc_{$title}_title", 'czr_fn_apply_qtranslate' , 20);

      // QtranslateX for FP when no FPC or FPU running
      if ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) {
        //outputs correct urls for current language : fp
        add_filter( 'tc_fp_link_url' , 'czr_fn_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'tc_fp_text', 'czr_fn_apply_qtranslate' );
        add_filter( 'tc_fp_button_text', 'czr_fn_apply_qtranslate' );

        /* The following is pretty useless at the momment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
        add_filter( 'tc_featured_page_button_text_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_one_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_two_customizer_set' , 'czr_fn_change_transport', 20, 2);
        add_filter( 'tc_featured_text_three_customizer_set', 'czr_fn_change_transport', 20, 2);
      }

      //posts slider (this filter is not fired in admin )
      add_filter('tc_posts_slider_pre_model', 'czr_fn_posts_slider_qtranslate');
      function czr_fn_posts_slider_qtranslate( $pre_slides ){
        if ( empty($pre_slides) )
          return $pre_slides;

        // remove useles q-translation of the slider view
        foreach ( array( 'tc_slide_title', 'tc_slide_text', 'tc_slide_button_text', 'tc_slide_background_alt' ) as $filter )
          remove_filter( $filter, 'czr_fn_apply_qtranslate' );

        // allow q-translation pre trim/sanitize
        foreach ( array( 'tc_posts_slider_button_text_pre_trim', 'tc_post_title_pre_trim', 'tc_post_excerpt_pre_sanitize', 'tc_posts_slide_background' ) as $filter )
          add_filter( $filter, 'czr_fn_apply_qtranslate' );

        //translate button text
        $pre_slides['common']['button_text'] = $pre_slides['common']['button_text'] ? CZR_slider::$instance -> czr_fn_get_post_slide_button_text( $pre_slides['common']['button_text'] ) : '';

        //translate title and excerpt if needed
        $_posts = &$pre_slides['posts'];

        foreach ($_posts as &$_post) {
          $ID = $_post['ID'];
          $_p = get_post( $ID );
          if ( ! $_p ) continue;

          $_post['title'] = $_post['title'] ? CZR_slider::$instance -> czr_fn_get_post_slide_title($_p, $ID) : '';
          $_post['text']  = $_post['text'] ? CZR_slider::$instance -> czr_fn_get_post_slide_excerpt($_p, $ID) : '';
        }
        return $pre_slides;
      }
    }


    /**
    * Polylang compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_polylang_compat() {
      // Disable posts slider transient caching
      add_filter('tc_posts_slider_use_transient', '__return_false');

      // If Polylang is active, hook function on the admin pages
      if ( function_exists( 'pll_register_string' ) )
        add_action( 'admin_init', 'czr_fn_pll_strings_setup' );

      function czr_fn_pll_strings_setup() {
        // grab theme options
        $tc_options = czr_fn__f('__options');
        // grab settings map, useful for some options labels
        $tc_settings_map = CZR_utils_settings_map::$instance -> czr_fn_get_customizer_map( $get_default = true );
        $tc_controls_map = $tc_settings_map['add_setting_control'];
        // set $polylang_group;
        $polylang_group = 'customizr-pro' == CZR___::$theme_name ? 'Customizr-Pro' : 'Customizr';

        //get options to translate
        $tc_translatable_raw_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        $tc_pll_options              = array();

        //build array if option => array( label (gettext-ed), option )
        foreach ( $tc_translatable_raw_options as $tc_translatable_option )
          if ( isset( $tc_options[$tc_translatable_option] ) ) {
            switch ( $tc_translatable_option ) {
              case 'tc_front_slider'             : $label = __( 'Front page slider name', 'customizr' );
                                                   break;
              case 'tc_posts_slider_button_text' : $label = __( 'Posts slider button text', 'customizr' );
                                                   break;
              default:                             $label = $tc_controls_map[$tc_translatable_option]['label'];
                                                   break;
            }//endswitch
            $tc_pll_options[$tc_translatable_option]= array(
                'label'  => $label,
                'value'  => $tc_options[$tc_translatable_option]
            );
          }

        //register the strings to translate
        foreach ( $tc_pll_options as $tc_pll_option )
          pll_register_string( $tc_pll_option['label'], $tc_pll_option['value'], $polylang_group);
      }// end tc_pll_strings_setup function

      // Front
      // If Polylang is active, translate/swap featured page buttons/text/link and slider
      if ( function_exists( 'pll_get_post' ) && function_exists( 'pll__' ) && ! is_admin() ) {
        //strings translation
        //get the options to translate
        $tc_translatable_options = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();
        //translate
        foreach ( $tc_translatable_options as $tc_translatable_option )
          add_filter("tc_opt_$tc_translatable_option", 'pll__');

        /**
        * Tax filtering (home/blog posts filtered by cat)
        * @param array of term ids
        */
        function czr_fn_pll_translate_tax( $term_ids ){
          if ( ! ( is_array( $term_ids ) && ! empty( $term_ids ) ) )
            return $term_ids;

          $translated_terms = array();
          foreach ( $term_ids as $id ){
              $translated_term = pll_get_term( $id );
              $translated_terms[] = $translated_term ? $translated_term : $id;
          }
          return array_unique( $translated_terms );
        }

        //Translate category ids for the filtered posts in home/blog
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_pll_translate_tax');
        /*end tax filtering*/

        /* Slider of posts */
        if ( function_exists( 'pll_current_language') ) {
        // Filter the posts query for the current language
          add_filter( 'tc_query_posts_slider_join'      , 'czr_fn_pll_posts_slider_join' );
          add_filter( 'tc_query_posts_slider_join_where', 'czr_fn_pll_posts_slider_join' );
        }
        function czr_fn_pll_posts_slider_join( $join ) {
          global $wpdb;
          switch ( current_filter() ){
            case 'tc_query_posts_slider_join'        : $join .= " INNER JOIN $wpdb->term_relationships AS pll_tr";
                                                       break;
            case 'tc_query_posts_slider_join_where'  : $_join = $wpdb->prepare("pll_tr.object_id = posts.ID AND pll_tr.term_taxonomy_id=%d ",
                                                                                pll_current_language( 'term_taxonomy_id' )
                                                       );
                                                       $join .= $join ? 'AND ' . $_join : 'WHERE '. $_join;
                                                       break;
          }

          return $join;
        }
        /*end Slider of posts */

        //Featured pages ids "translation"
        // Substitute any page id with the equivalent page in current language (if found)
        add_filter( 'tc_fp_id', 'czr_fn_pll_page_id', 20 );
        function czr_fn_pll_page_id( $fp_page_id ) {
          return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
        }
      }//end Front
    }//end polylang compat


    /**
    * WPML compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_wpml_compat() {
      //credits : @Srdjan
      $this->default_language = apply_filters( 'wpml_default_language', null );
      $this->current_language = apply_filters( 'wpml_current_language', null );

      // Disable posts slider transient caching
      add_filter('tc_posts_slider_use_transient', '__return_false');
      //define the CONSTANT wpml context. This means that user have to set the translations again when switching from Customizr, to Customizr-Pro.
      //If we don't want to do this, let's go with 'Customizr-option' in any case.
      //Also I choose to use "-option" suffix to avoid confusions as with WPML you can also translate theme's strings ( gettexted -> __() ) and WPML by default assigns to theme the context 'customizr' (textdomain)
      define( 'TC_WPML_CONTEXT' ,  'customizr-option' );

      // We cannot use wpml-config.xml to translate theme options because we use to update the option even in front page after retrieved, so we have to act on
      // a different filter.
      // When registering and translate strings WPML requires a 'context' and a 'name' (in a readable format for translators) plus the string to translate
      // context will be concatenated to the name and md5 will run on the result. The new result will represent the KEY for the WPML translations cache array.
      // This means that
      // 1) We cannot use translated string for the "name" param (which actually they say should be in a readable format ..)
      // 2) We need a way to use the same "name" both when registering the string to translate and retrieving its translations
      function czr_fn_wpml_get_options_names_config() {
        $_wp_cache_key     = 'tc_wpml_get_options_names_config';
        $option_name_assoc = wp_cache_get( $_wp_cache_key );

        if ( false === $option_name_assoc ) {
          $options_to_translate = CZR_plugins_compat::$instance -> czr_fn_get_string_options_to_translate();

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config', array(
 //           'tc_front_slider'              => 'Front page slider name', //Handled in a different way by Srdjan
            'tc_posts_slider_button_text'  => 'Posts slider button text',
            'tc_tag_title'                 => 'Tag pages title',
            'tc_cat_title'                 => 'Category pages title',
            'tc_author_title'              => 'Author pages title',
            'tc_search_title'              => 'Search pages title',
            'tc_social_in_sidebar_title'   => 'Social link title in sidebars',
            'tc_featured_page_button_text' => 'Featured button text',
            'tc_featured_text_one'         => 'Featured text one',
            'tc_featured_text_two'         => 'Featured text two',
            'tc_featured_text_three'       => 'Featured text three'
          ) );

          foreach ( $option_name_assoc as $key => $value ) {
            //use array_key_exists when and if options_to_translate will be an associative array
            if ( ! in_array( $key, $options_to_translate ) )
              unset( $option_name_assoc[$key] );
            else
              //md5 and html are stripped in wpml string table rendering, we add it for a better key
              $option_name_assoc[$key]    = $value . ' - ' . md5($key); //name
          }

          $option_name_assoc = apply_filters( 'tc_wpml_options_names_config_pre_cache', $option_name_assoc );
          //cache this 'cause is used several times in filter callbacks
          wp_cache_set( $_wp_cache_key, $option_name_assoc );
        }
        return apply_filters( 'tc_wpml_get_options_names_config', $option_name_assoc );
      }

      //Wras wpml_object_id in a more convenient function which recursevely translates array of values
      //$object can be an array or a single value
      function czr_fn_wpml_object_id( $object_id, $type ) {
        if ( empty( $object_id ) )
          return $object_id;
        if ( is_array( $object_id ) )
          return array_map( 'czr_fn_wpml_object_id', $object_id, array_fill( 0, sizeof( $object_id ), $type ) );
        return apply_filters( 'wpml_object_id', $object_id, $type, true );
      }

      //credits: @Srdjan -> filter the slides in the current language
      function czr_fn_wpml_sliders_filter( $sliders ) {
        if ( is_array( $sliders ) )
          foreach ( $sliders as $name => $slides ) {
            foreach ( $slides as $key => $attachment_id ) {
              // Get current slide language
              $slide_language = apply_filters( 'wpml_element_language_code',
                            null, array('element_id' => $attachment_id,
                                'element_type' => 'attachment') );
              if ( CZR_plugins_compat::$instance->current_language != $slide_language ) {
                // Replace with translated slide
                $translated_slide_id = apply_filters( 'wpml_object_id',
                                $attachment_id, 'attachment', false );
                if ( $translated_slide_id )
                  $sliders[$name][$key] = $translated_slide_id;
              }
            }
            $sliders[$name] = array_unique( $sliders[$name] );
          }

        return $sliders;
      }
      //credits: @Srdjan,
      function czr_fn_wpml_add_theme_options_filter() {
        add_filter( 'option_tc_theme_options', 'czr_fn_wpml_theme_options_filter', 99 );
      }
      //credits: @Srdjan
      function czr_fn_wpml_theme_options_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
        }
        return $options;
      }
      //credits: @Srdjan
      function czr_fn_wpml_edit_attachment_action( $attachment_id ) {
        $languages = apply_filters( 'wpml_active_languages', array() );
        // TODO check which meta keys are a must
        $meta_data = get_post_custom( $attachment_id );
        foreach ( $languages as $language) {
            $translated_attachment_id = apply_filters( 'wpml_object_id',
                    $attachment_id, 'attachment', false, $language['code'] );
            // Update post meta
            foreach ( array('post_slider_key', 'slider_check_key') as $meta_key ) {
                if ( isset( $meta_data[$meta_key][0] ) ) {
                    update_post_meta( $translated_attachment_id, $meta_key, $meta_data[$meta_key][0] );
                }
            }
        }
      }

      function czr_fn_wpml_pre_update_option_filter( $options ) {
        if ( isset( $options['tc_sliders'] ) ) {
            // Force default language
            $current_language = CZR_plugins_compat::$instance->current_language;
            CZR_plugins_compat::$instance->current_language = CZR_plugins_compat::$instance->default_language;
            $options['tc_sliders'] = czr_fn_wpml_sliders_filter( $options['tc_sliders'] );
            CZR_plugins_compat::$instance->current_language = $current_language;
        }
        return $options;
      }

      add_action( 'admin_init', 'czr_fn_wpml_admin_setup' );

      function czr_fn_wpml_admin_setup() {
        // If wpml-string-translation is active perform admin pages translation
        if ( function_exists( 'icl_register_string' ) ) {
          $tc_wpml_option_name = czr_fn_wpml_get_options_names_config();
          $tc_wpml_options     = array_keys($tc_wpml_option_name);

          // grab theme options
          $tc_options = czr_fn__f('__options');

          // build array of options to translate
          foreach ( $tc_wpml_options as $tc_wpml_option )
            if ( isset( $tc_options[$tc_wpml_option] ) )
              icl_register_string( TC_WPML_CONTEXT,
                $tc_wpml_option_name[$tc_wpml_option],
                esc_attr($tc_options[$tc_wpml_option]) //value
            );
        }//end of string based admin translation
        //Taxonomies/Pages "transposing" in the Customizer
        //We actually could just do this instead of A) and B) in front, but we retrieve the options in front before the compat method is called (after_setup_theme with lower priority) and I prefer to keep front and back separated in this case. Different opinions are welcome, but not too much :P.
        //we have to filter the interesting options so they appear "translated" in the customizer too, 'cause wpml filters the pages/cats to choose (fp, cat pickers), and we kinda like this :), right (less memory)?
        //Side effect example for categories: TODO
        //In English we have set to filter blog posts for cat A,B and C.
        //In Italian we do not have cat C so there will be displayed transposed cats A and B
        //if we change this option in the Customizer with lang IT removing B, e.g., when we switch to EN we'll have that the array of cats contains just A, as it as been overwritten with the new setting
        if ( CZR___::$instance -> czr_fn_is_customize_left_panel() )
          add_filter( 'option_tc_theme_options', 'czr_fn_wpml_customizer_options_transpose' );
        function czr_fn_wpml_customizer_options_transpose( $options ) {
          $options_to_transpose = apply_filters ( 'tc_wpml_customizer_translate_options', array(
            'page'     => ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu') ) && ! class_exists('TC_fpc') ) ? array( 'tc_featured_page_one', 'tc_featured_page_two', 'tc_featured_page_three' ) : array(),
            'category' => array( 'tc_blog_restrict_by_cat' )
            )
          );
          foreach ( $options_to_transpose as $type => $option_to_transpose )
            foreach ( $option_to_transpose as $option )
              if ( isset( $options[$option] ) )
                $options[$option] = czr_fn_wpml_object_id( $options[$option], $type);
          return $options;
        }

        //credits @Srdjan
        // Filter slides in admin screens
        add_action( '__attachment_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        add_action( '__post_slider_infos', 'czr_fn_wpml_add_theme_options_filter', 9 );
        // Update translated slide post meta
        add_action( 'edit_attachment', 'czr_fn_wpml_edit_attachment_action', 99 );
        // Pre-save hook
        add_filter( 'pre_update_option_tc_theme_options', 'czr_fn_wpml_pre_update_option_filter', 99 );

      }// end tc_wpml_admin_setup function

      // Front
      // If WPML string translator is active, translate/swap featured page buttons/text/link and slider
      if ( ! is_admin() ) {
        // String transaltion binders : requires wpml icl_t function
        if ( function_exists( 'icl_t') ) {
          /*** TC - WPML bind, wrap WPML string translator function into convenient tc functions ***/
          //define our icl_t wrapper for options filtered with tc_opt_{$option}
          if ( ! function_exists( 'czr_fn_wpml_t_opt' ) ) {
            function czr_fn_wpml_t_opt( $string ) {
              return czr_fn_wpml_t( $string, str_replace('tc_opt_', '', current_filter() ) );
            }
          }
          //special function for the post slider button text pre trim filter
          if ( ! function_exists( 'czr_fn_wpml_t_ps_button_text' ) ) {
            function czr_fn_wpml_t_ps_button_text( $string ) {
              return czr_fn_wpml_t( $string, 'tc_posts_slider_button_text' );
            }
          }
          //define our icl_t wrapper
          if ( ! function_exists( 'czr_fn_wpml_t' ) ) {
            function czr_fn_wpml_t( $string, $opt ) {
              $tc_wpml_options_names = czr_fn_wpml_get_options_names_config();
              return icl_t( TC_WPML_CONTEXT, $tc_wpml_options_names[$opt], $string );
            }
          }
          /*** End TC - WPML bind ***/

          //get the options to translate
          $tc_wpml_options = array_keys( czr_fn_wpml_get_options_names_config() );

          //strings translation
          foreach ( $tc_wpml_options as $tc_wpml_option )
            add_filter("tc_opt_$tc_wpml_option", 'czr_fn_wpml_t_opt', 20 );

          //translates sliders? credits @Srdjan
          add_filter( 'tc_opt_tc_sliders', 'czr_fn_wpml_sliders_filter', 99 );

        }
        /*A) FP*/
        // Featured pages ids "translation"
        add_filter( 'tc_fp_id', 'czr_fn_wpml_page_id', 20 );
        function czr_fn_wpml_page_id( $fp_page_id ) {
          return czr_fn_wpml_object_id( $fp_page_id, 'page');
        }

        /*B) Tax */
        /**
        * Cat filtering (home/blog posts filtered by cat)
        *
        * AFAIK wpml needs to exactly know which kind of tax we're looking for, category, tag ecc..
        * @param array of term ids
        */
        function czr_fn_wpml_translate_cat( $cat_ids ){
          if ( ! ( is_array( $cat_ids ) && ! empty( $cat_ids ) ) )
            return $cat_ids;
          return array_unique( czr_fn_wpml_object_id( $cat_ids, 'category' ) );
        }
        //Translate category ids for the filtered posts in home/blog
        add_filter('tc_opt_tc_blog_restrict_by_cat', 'czr_fn_wpml_translate_cat');
        /*end tax filtering*/

        /* Slider of posts */
        if ( defined( 'ICL_LANGUAGE_CODE') ) {
        // Filter the posts query for the current language
          add_filter( 'tc_query_posts_slider_join'      , 'czr_fn_wpml_posts_slider_join' );
          add_filter( 'tc_query_posts_slider_join_where', 'czr_fn_wpml_posts_slider_join' );
        }
        function czr_fn_wpml_posts_slider_join( $join ) {
          global $wpdb;
          switch ( current_filter() ){
            case 'tc_query_posts_slider_join'        : $join .= " INNER JOIN {$wpdb->prefix}icl_translations AS wpml_tr";
                                                       break;
            case 'tc_query_posts_slider_join_where'  : $_join = $wpdb->prepare("wpml_tr.element_id = posts.ID AND wpml_tr.language_code=%s AND wpml_tr.element_type=%s",
                                                                    ICL_LANGUAGE_CODE,
                                                                    'post_post'
                                                       );
                                                       $join .= $join ? 'AND ' . $_join : 'WHERE '. $_join;
                                                       break;
          }

          return $join;
        }
        /*end Slider of posts */
        /*end Slider*/
      }//end Front
    }//end wpml compat




    /**
    * The Events Calendar compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_the_events_calendar_compat() {
      /*
      * Are we in the Events list context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_events_list' ) ) ) {
        function czr_fn_is_tec_events_list() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_post_type_archive();
        }
      }
      /*
      * Are we in single Event context?
      */
      if ( ! ( function_exists( 'czr_fn_is_tec_single_event' ) ) ) {
        function czr_fn_is_tec_single_event() {
          return function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() && is_single();
        }
      }
      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_tec_disable_tax_archive_title');
      function czr_fn_tec_disable_tax_archive_title( $bool ) {
        return czr_fn_is_tec_events_list() ? false : $bool;
      }

      // Events archive is displayed, wrongly, we our post lists classes, we have to prevent this
      add_filter( 'tc_post_list_controller', 'czr_fn_tec_disable_post_list');
      add_filter( 'tc_is_grid_enabled', 'czr_fn_tec_disable_post_list');
      function czr_fn_tec_disable_post_list( $bool ) {
        return czr_fn_is_tec_events_list() ? false : $bool;
      }

      // Now we have to display a post or page content
      add_filter( 'tc_show_single_post_content', 'czr_fn_tec_show_content' );
      function czr_fn_tec_show_content( $bool ) {
        //2 cases:
        //1 - in events lists - we force showing single post content
        //2 - in single events we have to prevent showing both page and post content
        if ( czr_fn_is_tec_events_list() )
          return true;
        else if( czr_fn_is_tec_single_event() )
          return false;
        return $bool;
      }

      // Force the tax name in the breadcrumb when list of events shown as 'Month'
      // The Events Calendar adds a filter on post_type_archive_title with __return_false callback
      // for their own reasons. This impacts on our breadcrumb 'cause we use the function post_type_archive_title() to build up the trail arg in posty_type_archives contexts.
      // What we do here is unhooking their callback before the breadcrumb is built and re-hook it after it has been displayed
      add_action( 'wp_head', 'czr_fn_tec_allow_display_breadcrumb_in_month_view');
      function czr_fn_tec_allow_display_breadcrumb_in_month_view() {
        if ( ! ( czr_fn_is_tec_events_list() && function_exists( 'tribe_is_month' ) && tribe_is_month() ) )
          return;

        add_filter( 'tc_breadcrumb_trail_args', 'czr_fn_tec_unhook_empty_post_type_archive_title');
        function czr_fn_tec_unhook_empty_post_type_archive_title( $args = null ) {
          remove_filter( 'post_type_archive_title', '__return_false', 10 );
          return $args;
        }
        add_filter( 'tc_breadcrumb_trail_display', 'czr_fn_tec_rehook_empty_post_type_archive_title', PHP_INT_MAX );
        function czr_fn_tec_rehook_empty_post_type_archive_title( $breadcrumb = null ) {
          add_filter( 'post_type_archive_title', '__return_false', 10 );
          return $breadcrumb;
        }
      }
      //disables post navigation in single tec pages
      add_filter( 'tc_show_post_navigation', 'czr_fn_tec_disable_post_navigation' );
      function czr_fn_tec_disable_post_navigation($bool) {
        return ( czr_fn_is_tec_single_event() ) ? false : $bool;
      }

      /*
      * Avoid php smartload image php parsing in events list content
      * See: https://github.com/presscustomizr/hueman/issues/285
      */
      add_filter( 'tc_disable_img_smart_load', 'czr_fn_tec_disable_img_smart_load_events_list', 999, 2);
      function czr_fn_tec_disable_img_smart_load_events_list( $_bool, $parent_filter ) {
        if ( 'the_content' == $parent_filter && czr_fn_is_tec_events_list() )
          return true;//disable
        return $_bool;
      }
    }//end the-events-calendar compat



    /**
    * OptimizePress compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_optimizepress_compat() {
      add_action('wp_print_scripts', 'czr_fn_op_dequeue_fancybox_js');
      function czr_fn_op_dequeue_fancybox_js(){
        if ( function_exists('is_le_page') ){
          /* Op Back End: Dequeue tc-scripts */
          if ( is_le_page() || defined('OP_LIVEEDITOR') ) {
            wp_dequeue_script('tc-scripts');
            wp_dequeue_script('tc-fancybox');
          }
          else {
            /* Front End: Dequeue Fancybox maybe already embedded in Customizr */
            wp_dequeue_script('tc-fancybox');
            //wp_dequeue_script(OP_SN.'-fancybox');
          }
        }
      }

      /* Remove fancybox loading icon*/
      add_action('wp_footer','czr_fn_op_remove_fancyboxloading');
      function czr_fn_op_remove_fancyboxloading(){
        echo "<script>
                if (typeof(opjq) !== 'undefined') {
                  opjq(document).ready(function(){
                    opjq('#fancybox-loading').remove();
                  });
                }
             </script>";
      }
    }//end optimizepress compat



    /**
    * Sensei compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_sensei_compat() {
      //unkooks the default sensei wrappers and add customizr's content wrapper and action hooks
      global $woothemes_sensei;
      remove_action( 'sensei_before_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper' ), 10 );
      remove_action( 'sensei_after_main_content', array( $woothemes_sensei->frontend, 'sensei_output_content_wrapper_end' ), 10 );

      add_action('sensei_before_main_content', 'czr_fn_sensei_wrappers', 10);
      add_action('sensei_after_main_content', 'czr_fn_sensei_wrappers', 10);


      function czr_fn_sensei_wrappers() {
        switch ( current_filter() ) {
          case 'sensei_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                             break;

          case 'sensei_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                             break;
        }//end of switch on hook
      }//end of nested function

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_sensei_disable_tax_archive_title');
      function czr_fn_sensei_disable_tax_archive_title( $bool ){
        return ( function_exists('is_sensei') && ( is_sensei() || is_post_type_archive('sensei_message') ) ) ? false : $bool;
      }

      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_sensei_disable_post_navigation' );
      function czr_fn_sensei_disable_post_navigation($bool) {
        return ( function_exists('is_sensei') && is_sensei() || is_singular('sensei_message') ) ? false : $bool;
      }


      //in my courses page avoid displaying both page and single content
      add_filter( 'tc_show_single_post_content', 'czr_fn_sensei_disable_single_content_in_my_courses');
      function czr_fn_sensei_disable_single_content_in_my_courses( $bool ) {
        global $post;
        return is_page() && 'course' === $post->post_type ? false : $bool;
      }

    }//end sensei compat




    /**
    * Woocommerce compat hooks
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_set_woocomerce_compat() {
      //unkooks the default woocommerce wrappersv and add customizr's content wrapper and action hooks
      remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
      remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
      add_action('woocommerce_before_main_content', 'czr_fn_woocommerce_wrappers', 10);
      add_action('woocommerce_after_main_content', 'czr_fn_woocommerce_wrappers', 10);

      //disable WooCommerce default breadcrumb
      if ( apply_filters( 'tc_disable_woocommerce_breadcrumb', true ) )
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

      function czr_fn_woocommerce_wrappers() {
        switch ( current_filter() ) {
          case 'woocommerce_before_main_content': CZR_plugins_compat::$instance -> czr_fn_mainwrapper_start();
                                                  break;

          case 'woocommerce_after_main_content' : CZR_plugins_compat::$instance -> czr_fn_mainwrapper_end();
                                                  break;
        }//end of switch on hook
      }//end of nested function
      //Helper
      function czr_fn_wc_is_checkout_cart() {
        return is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART');
      }
      //Helper
      function czr_fn_woocommerce_shop_page_id( $id = null ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() && function_exists('wc_get_page_id') ) ? wc_get_page_id( 'shop' ) : $id;
      }
      //Helper
      function czr_fn_woocommerce_shop_enable( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() && function_exists('is_shop') && is_shop() ) ? true : $bool;
      }

      //when in the woocommerce shop page use the "shop" id
      add_filter( 'tc_id', 'czr_fn_woocommerce_shop_page_id' );

      // use Customizr title
      // initially used to display the edit button
      add_filter( 'the_title', 'czr_fn_woocommerce_the_title' );
      function czr_fn_woocommerce_the_title( $_title ){
        if ( function_exists('is_woocommerce') && is_woocommerce() && ! is_page() )
            return apply_filters( 'tc_title_text', $_title );
        return $_title;
      }

      // hide tax archive title
      add_filter( 'tc_show_tax_archive_title', 'czr_fn_woocommerce_disable_tax_archive_title' );
      function czr_fn_woocommerce_disable_tax_archive_title( $bool ){
        return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //allow slider in the woocommerce shop page
      add_filter( 'tc_show_slider', 'czr_fn_woocommerce_shop_enable' );

      //allow page layout post meta in 'shop'
      add_filter( 'tc_is_page_layout', 'czr_fn_woocommerce_shop_enable' );

      //handles the woocomerce sidebar : removes action if sidebars not active
      if ( !is_active_sidebar( 'shop') ) {
        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
      }


      //disables post navigation
      add_filter( 'tc_show_post_navigation', 'czr_fn_woocommerce_disable_post_navigation' );
      function czr_fn_woocommerce_disable_post_navigation($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }


      //removes post comment action on after_loop hook
      add_filter( 'tc_are_comments_enabled', 'czr_fn_woocommerce_disable_comments' );
      function czr_fn_woocommerce_disable_comments($bool) {
         return ( function_exists('is_woocommerce') && is_woocommerce() ) ? false : $bool;
      }

      //link smooth scroll: exclude woocommerce tabs
      add_filter( 'tc_anchor_smoothscroll_excl', 'czr_fn_woocommerce_disable_link_scroll' );
      function czr_fn_woocommerce_disable_link_scroll( $excl ){
        if ( false == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_link_scroll') ) ) return $excl;

        if ( function_exists('is_woocommerce') && is_woocommerce() ) {
          if ( ! is_array( $excl ) )
            $excl = array();

          if ( ! is_array( $excl['deep'] ) )
            $excl['deep'] = array() ;

          if ( ! is_array( $excl['deep']['classes'] ) )
              $excl['deep']['classes'] = array();

          $excl['deep']['classes'][] = 'wc-tabs';
        }
        return $excl;
      }


      //changes customizr meta boxes priority (slider and layout not on top) if displaying woocommerce products in admin
      add_filter( 'tc_post_meta_boxes_priority', 'czr_fn_woocommerce_change_meta_boxes_priority' , 2 , 10 );
      function czr_fn_woocommerce_change_meta_boxes_priority($priority , $screen) {
         return ( 'product' == $screen ) ? 'default' : $priority ;
      }


      // Allow HEADER CART OPTIONS in the customizer
      // Returns a callback function needed by 'active_callback' to enable the options in the customizer
      add_filter( 'tc_woocommerce_options_enabled', 'czr_fn_woocommerce_options_enabled_cb' );
      function czr_fn_woocommerce_options_enabled_cb() {
        return '__return_true';
      }

      /* rendering the cart icon in the header */
      //narrow the tagline
      add_filter( 'tc_tagline_class', 'czr_fn_woocommerce_force_tagline_width', 100 );
      function czr_fn_woocommerce_force_tagline_width( $_class ) {
        return 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart' ) ) ? 'span6' : $_class ;
      }

      // Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
      add_filter( 'woocommerce_add_to_cart_fragments', 'czr_fn_woocommerce_add_to_cart_fragment' );
      function czr_fn_woocommerce_add_to_cart_fragment( $fragments ) {
        if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart' ) ) ) {
          $_cart_count = WC()->cart->get_cart_contents_count();
          $fragments['span.tc-wc-count'] = sprintf( '<span class="count btn-link tc-wc-count">%1$s</span>', $_cart_count ? $_cart_count : '' );
        }
        return $fragments;
      }

      //print the cart menu in the header
      add_action( '__navbar', 'czr_fn_woocommerce_header_cart', is_rtl() ? 9 : 19 );
      function czr_fn_woocommerce_header_cart() {
        if ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          return;

        $_main_item_class = '';
        $_cart_count      = WC()->cart->get_cart_contents_count();
        //highlight the cart icon when in the Cart or Ceckout page
        if ( czr_fn_wc_is_checkout_cart() ) {
          $_main_item_class = 'current-menu-item';
        }

       ?>
       <div class="tc-wc-menu tc-open-on-hover span1">
         <ul class="tc-wc-header-cart nav tc-hover-menu">
           <li class="<?php echo esc_attr( $_main_item_class ); ?> menu-item">
             <a class="cart-contents" href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php _e( 'View your shopping cart', 'customizr' ); ?>">
               <span class="count btn-link tc-wc-count"><?php echo $_cart_count ? $_cart_count : '' ?></span>
            </a>
            <?php
            ?>
            <?php if ( ! czr_fn_wc_is_checkout_cart() ) : //do not display the dropdown in the cart or checkout page ?>
              <ul class="dropdown-menu">
               <li>
                 <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                </li>
              </ul>
            <?php endif; ?>
           </li>
          </ul>
        </div>
      <?php
      }

      //add woommerce header cart classes to the header (sticky enabled)
      add_filter( 'tc_header_classes'   , 'czr_fn_woocommerce_set_header_classes');
      function czr_fn_woocommerce_set_header_classes( $_classes ) {
        if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          $_classes[]          = ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart_sticky' ) ) ) ? 'tc-wccart-off' : 'tc-wccart-on';
        return $_classes;
      }

      //add woocommerce header cart CSS
      add_filter('tc_user_options_style', 'czr_fn_woocommerce_header_cart_css');
      function czr_fn_woocommerce_header_cart_css( $_css ) {
        if ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_woocommerce_header_cart' ) ) )
          return $_css;

        /* The only real decision I took here is the following:
        * I let the "count" number possibily overflow the parent (span1) width
        * so that as it grows it won't break on a new line. This is quite an hack to
        * keep the cart space as small as possible (span1) and do not hurt the tagline too much (from span7 to span6). Also nobody will, allegedly, have more than 10^3 products in its cart
        */
        $_header_layout      = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout') );
        $_resp_pos_css       = 'right' == $_header_layout ? 'float: left;' : '';
        $_wc_t_align         = 'left';

        //dropdown top arrow, as we open the drodpdown on the right we have to move the top arrow accordingly
        $_dd_top_arrow       = '.navbar .tc-wc-menu .nav > li > .dropdown-menu:before { right: 9px; left: auto;} .navbar .tc-wc-menu .nav > li > .dropdown-menu:after { right: 10px; left: auto; }';

        //rtl custom css
        if ( is_rtl() ) {
          $_wc_t_align       = 'right';
          $_dd_top_arrow     = '';
        }

        return sprintf( "%s\n%s",
              $_css,
              ".sticky-enabled .tc-header.tc-wccart-off .tc-wc-menu { display: none; }
               .sticky-enabled .tc-tagline-off.tc-wccart-on .tc-wc-menu { margin-left: 0; margin-top: 3px; }
               .sticky-enabled .tc-tagline-off.tc-wccart-on .btn-toggle-nav { margin-top: 5px; }
               .tc-header .tc-wc-menu .nav { text-align: right; }
               $_dd_top_arrow
               .tc-header .tc-wc-menu .dropdown-menu {
                  right: 0; left: auto; width: 250px; padding: 2px;
               }
               .tc-header .tc-wc-menu {
                 float: right; clear:none; margin-top: 1px;
               }
               .tc-header .tc-wc-menu .nav > li {
                 float:none;
               }
               .tc-wc-menu ul.dropdown-menu .buttons a,
               .tc-wc-menu ul {
                 width: 100%;
                 -webkit-box-sizing: border-box;
                 -moz-box-sizing: border-box;
                 box-sizing: border-box;
               }
               .tc-wc-menu ul.dropdown-menu .buttons a {
                 margin: 10px 5px 0 0px; text-align: center;
               }
               .tc-wc-menu .nav > li > a:before {
                 content: '\\f07a';
                 position:absolute;
                 font-size:1.6em; left: 0;
               }
               .tc-header .tc-wc-menu .nav > li > a {
                 position: relative;
                 padding-right: 0 !important;
                 padding-left: 0 !important;
                 display:inline-block;
                 border-bottom: none;
                 text-align: right;
                 height: 1em;
                 min-width:1.8em;
               }
               .tc-wc-menu .count {
                 font-size: 0.7em;
                 margin-left: 2.1em;
                 position: relative;
                 top: 1em;
                 pointer-events: none;
               }
               .tc-wc-menu .woocommerce.widget_shopping_cart li {
                 padding: 0.5em;
               }
               .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart p,
               .tc-header .tc-wc-menu .woocommerce.widget_shopping_cart li {
                 padding-right: 1em;
                 padding-left: 1em;
                 text-align: $_wc_t_align;
                 font-size: inherit; font-family: inherit;
               }
               .tc-wc-menu .widget_shopping_cart .product_list_widget li a.remove {
                 position: relative; float: left; top: auto; margin-right: 0.2em;
               }
               .tc-wc-menu .widget_shopping_cart .product_list_widget {
                 max-height: 40vh;
                 overflow-y: auto;
                 padding: 1em 0;
               }
               @media (max-width: 979px) {
                .tc-wc-menu[class*=span] { width: auto; margin-top:7px; $_resp_pos_css }
                .tc-wc-menu .dropdown-menu { display: none !important;}
              }
              @media (max-width: 767px) { .sticky-enabled .tc-wccart-on .brand { width: 50%;} }
        ");
      }
      /*end rendering the cart icon in the header */
    }//end woocommerce compat


    /**
    * Visual Composer compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_vc_compat() {
      //link smooth scroll: exclude all anchor links inside vc wrappers (.vc_row)
      add_filter( 'tc_anchor_smoothscroll_excl', 'czr_fn_vc_disable_link_scroll' );
      function czr_fn_vc_disable_link_scroll( $excl ){
        if ( false == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_link_scroll') ) ) return $excl;

        if ( ! is_array( $excl ) )
          $excl = array();

        if ( ! is_array( $excl['deep'] ) )
          $excl['deep'] = array() ;

        if ( ! is_array( $excl['deep']['classes'] ) )
            $excl['deep']['classes'] = array();

        $excl['deep']['classes'][] = 'vc_row';

        return $excl;
      }
    }//end woocommerce compat


    /**
    * Disqus Comment System compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_disqus_compat() {
      if ( ! function_exists( 'czr_fn_disqus_comments_enabled' ) ) {
        function czr_fn_disqus_comments_enabled() {
          return function_exists( 'dsq_is_installed' ) && function_exists( 'dsq_can_replace' )
                 && dsq_is_installed() && dsq_can_replace();
        }
      }
      //replace the default comment link anchor with a more descriptive disqus anchor
      add_filter( 'tc_bubble_comment_anchor', 'czr_fn_disqus_bubble_comment_anchor' );
      function czr_fn_disqus_bubble_comment_anchor( $anchor ) {
        return czr_fn_disqus_comments_enabled() ? '#tc-disqus-comments' : $anchor;
      }
      //wrap disqus comments template in a convenient div
      add_action( 'tc_before_comments_template' , 'czr_fn_disqus_comments_wrapper' );
      add_action( 'tc_after_comments_template'  , 'czr_fn_disqus_comments_wrapper' );
      function czr_fn_disqus_comments_wrapper() {
        if ( ! czr_fn_disqus_comments_enabled() )
          return;

        switch ( current_filter() ) {
          case 'tc_before_comments_template' : echo '<div id="tc-disqus-comments">';
                                               break;
          case 'tc_after_comments_template'  : echo '</div>';
        }
      }
    }//end disqus compat


    /**
    * Ultimate Responsive Image Slider compat hooks
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    private function czr_fn_set_uris_compat() {
      add_filter ( 'tc_img_smart_load_options', 'czr_fn_uris_disable_img_smartload' ) ;
      function czr_fn_uris_disable_img_smartload( $options ){
        if ( ! is_array( $options ) )
          $options = array();

        if ( ! is_array( $options['opts'] ) )
          $options['opts'] = array();

        if ( ! is_array( $options['opts']['excludeImg'] ) )
          $options['opts']['excludeImg'] = array();

        $options['opts']['excludeImg'][] = '.sp-image';

        return $options;
      }
    }//end uris compat


    /**
    * TC Unlimited Featured Pages compat hooks
    * Since Customizr 3.4.24 we changed the functions and class prefixes
    * Olf fpu versions might refer to them throwing PHP errors
    * with the code below we basically "soft-disable" the plugin
    * without actyally disabling it to allow the user to update it
    * @package Customizr
    * @since Customizr 3.4.24
    */
    private function czr_fn_set_tc_unlimited_featured_pages_compat() {
      //This has to be fired after : tc_generates_featured_pages | 10 (priority)
      if ( class_exists( 'TC_fpu' ) &&  version_compare( TC_fpu::$instance -> plug_version, '2.0.24', '<' ) ) {

        //back
        if ( method_exists( 'TC_back_fpu', 'tc_add_controls_class' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_add_controls_class' ) ,10,1);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_controls_js_css' ) )
          remove_action ( 'customize_controls_enqueue_scripts' , array(TC_back_fpu::$instance , 'tc_customize_controls_js_css' ), 100);

        if ( method_exists( 'TC_back_fpu', 'tc_customize_register' ) )
          remove_action ( 'customize_register'         , array( TC_back_fpu::$instance , 'tc_customize_register' ) , 20, 1 );

        if ( method_exists( 'TC_back_fpu', 'tc_customize_preview_js' ) )
          remove_action ( 'customize_preview_init'     , array( TC_back_fpu::$instance , 'tc_customize_preview_js' ));

        //do not remove customizr free featured pages option panel
        if ( method_exists( 'TC_fpu', 'tc_delete_fp_options' ) )
          remove_filter ( 'tc_front_page_option_map'   , array( TC_fpu::$instance , 'tc_delete_fp_options' ), 20 );

        //front
        if ( class_exists( 'TC_front_fpu' ) ) {
          if ( method_exists( 'TC_front_fpu', 'tc_set_fp_hook' ) )
            remove_action( 'template_redirect'         , array( TC_front_fpu::$instance , 'tc_set_fp_hook'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_set_colors' ) )
            remove_action( 'wp_head'                   , array( TC_front_fpu::$instance , 'tc_set_colors'), 10 );
          if ( method_exists( 'TC_front_fpu', 'tc_enqueue_plug_resources' ) )
            remove_action( 'wp_enqueue_scripts'        , array( TC_front_fpu::$instance , 'tc_enqueue_plug_resources') );
        }

        //needed as some plugins (lang) will check the TC_fpu class existence
        add_filter( 'tc_other_plugins_force_fpu_disable', '__return_false' );
      }
    }


    /**
    * CUSTOMIZR WRAPPERS
    * print the customizr wrappers
    *
    * @since 3.3+
    *
    * originally used for woocommerce compatibility
    */
    function czr_fn_mainwrapper_start() {
      ?>
      <div id="main-wrapper" class="<?php echo implode(' ', apply_filters( 'tc_main_wrapper_classes' , array('container') ) ) ?>">

        <?php do_action( '__before_main_container' ); ##hook of the featured page (priority 10) and breadcrumb (priority 20)...and whatever you need! ?>

        <div class="container" role="main">
          <div class="<?php echo implode(' ', apply_filters( 'tc_column_content_wrapper_classes' , array('row' ,'column-content-wrapper') ) ) ?>">

            <?php do_action( '__before_article_container'); ##hook of left sidebar?>

              <div id="content" class="<?php echo implode(' ', apply_filters( 'tc_article_container_class' , array( CZR_utils::czr_fn_get_layout( CZR_utils::czr_fn_id() , 'class' ) , 'article-container' ) ) ) ?>">

                <?php do_action ('__before_loop');##hooks the header of the list of post : archive, search... ?>
      <?php
    }

    function czr_fn_mainwrapper_end() {
      ?>
                <?php do_action ('__after_loop');##hook of the comments and the posts navigation with priorities 10 and 20 ?>

              </div><!--.article-container -->

              <?php do_action( '__after_article_container'); ##hook of left sidebar?>

            </div><!--.row -->
        </div><!-- .container role: main -->

        <?php do_action( '__after_main_container' ); ?>

      </div><!-- //#main-wrapper -->
      <?php
    }



    /**
    * HELPER
    * Check whether the plugin is active by checking the active_plugins list.
    * copy of is_plugin_active declared in wp-admin/includes/plugin.php
    *
    * @since 3.3+
    *
    * @param string $plugin Base plugin path from plugins directory.
    * @return bool True, if in the active plugins list. False, not in the list.
    */
    function czr_fn_is_plugin_active( $plugin ) {
      return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this -> czr_fn_is_plugin_active_for_network( $plugin );
    }


    /**
    * HELPER
    * Check whether the plugin is active for the entire network.
    * copy of is_plugin_active_for_network declared in wp-admin/includes/plugin.php
    *
    * @since 3.3+
    *
    * @param string $plugin Base plugin path from plugins directory.
    * @return bool True, if active for the network, otherwise false.
    */
    function czr_fn_is_plugin_active_for_network( $plugin ) {
      if ( ! is_multisite() )
        return false;

      $plugins = get_site_option( 'active_sitewide_plugins');
      if ( isset($plugins[$plugin]) )
        return true;

      return false;
    }

    public function czr_fn_get_string_options_to_translate() {
      $string_options = array(
        'tc_front_slider',
        'tc_posts_slider_button_text',
        'tc_tag_title',
        'tc_cat_title',
        'tc_author_title',
        'tc_search_title',
        'tc_social_in_sidebar_title',
      );
      if ( ! apply_filters( 'tc_other_plugins_force_fpu_disable', class_exists('TC_fpu')  ) && ! class_exists('TC_fpc') ) {
        $fp_areas = CZR_init::$instance -> fp_ids;
        foreach ( $fp_areas as $fp_area )
          $string_options[] = 'tc_featured_text_' . $fp_area;

        $string_options[] = 'tc_featured_page_button_text';
      }
      return apply_filters( 'tc_get_string_options_to_translate', $string_options );
    }
  }//end of class
endif;

?><?php
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
    private $is_wp_version_before_4_0;
    public $customizer_map = array();

    function __construct () {
      self::$instance =& $this;
      //declare a private property to check wp version >= 4.0
      global $wp_version;
      $this -> is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;
    }//end of construct



    /**
    * Defines sections, settings and function of customizer and return and array
    * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    public function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {
      if ( ! empty( $this -> customizer_map ) ) {
        $_customizer_map = $this -> customizer_map;
      }
      else {
        //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
        add_filter( 'tc_add_panel_map'        , array( $this, 'czr_fn_popul_panels_map'));
        add_filter( 'tc_remove_section_map'   , array( $this, 'czr_fn_popul_remove_section_map'));
        //theme switcher's enabled when user opened the customizer from the theme's page
        add_filter( 'tc_remove_section_map'   , array( $this, 'czr_fn_set_theme_switcher_visibility'));
        add_filter( 'tc_add_section_map'      , array( $this, 'czr_fn_popul_section_map' ));
        //add controls to the map
        add_filter( 'tc_add_setting_control_map' , array( $this , 'czr_fn_popul_setting_control_map' ), 10, 2 );
        //$this -> tc_populate_setting_control_map();

        //FILTER SPECIFIC SETTING-CONTROL MAPS
        //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
        add_filter( 'czr_fn_front_page_option_map' , array( $this, 'czr_fn_generates_featured_pages' ));

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
    * Populate the control map
    * hook : 'tc_add_setting_control_map'
    * => loops on a callback list, each callback is a section setting group
    * @return array()
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_popul_setting_control_map( $_map, $get_default = null ) {
      $_new_map = array();
      $_settings_sections = array(
        //GLOBAL SETTINGS
        'czr_fn_logo_favicon_option_map',
        'czr_fn_skin_option_map',
        'czr_fn_fonts_option_map',
        'czr_fn_social_option_map',
        'czr_fn_icons_option_map',
        'czr_fn_links_option_map',
        'czr_fn_images_option_map',
        'czr_fn_responsive_option_map',
        'czr_fn_authors_option_map',
        'czr_fn_smoothscroll_option_map',
        //HEADER
        'czr_fn_header_design_option_map',
        'czr_fn_navigation_option_map',
        //CONTENT
        'czr_fn_front_page_option_map',
        'czr_fn_layout_option_map',
        'czr_fn_comment_option_map',
        'czr_fn_breadcrumb_option_map',
        'czr_fn_post_metas_option_map',
        'czr_fn_post_list_option_map',
        'czr_fn_single_post_option_map',
        'czr_fn_gallery_option_map',
        'czr_fn_paragraph_option_map',
        'czr_fn_post_navigation_option_map',
        //SIDEBARS
        'czr_fn_sidebars_option_map',
        //FOOTER
        'czr_fn_footer_global_settings_option_map',
        //ADVANCED OPTIONS
        'czr_fn_custom_css_option_map',
        'czr_fn_performance_option_map',
        'czr_fn_placeholders_notice_map',
        'czr_fn_external_resources_option_map'
      );

      foreach ( $_settings_sections as $_section_cb ) {
        if ( ! method_exists( $this , $_section_cb ) )
          continue;
        //applies a filter to each section settings map => allows plugins (featured pages for ex.) to add/remove settings
        //each section map takes one boolean param : $get_default
        $_section_map = apply_filters(
          $_section_cb,
          call_user_func_array( array( $this, $_section_cb ), array( $get_default ) )
        );

        if ( ! is_array( $_section_map) )
          continue;

        $_new_map = array_merge( $_new_map, $_section_map );
      }//foreach
      return array_merge( $_map, $_new_map );
    }


    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : GLOBAL SETTINGS
    *******************************************************************************************************
    ******************************************************************************************************/

    /*-----------------------------------------------------------------------------------------------------
                                   LOGO & FAVICON SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_logo_favicon_option_map( $get_default = null ) {
      global $wp_version;
      return array(
              'tc_logo_upload'  => array(
                                'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                'label'     =>  __( 'Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                'title'     => __( 'LOGO' , 'customizr'),
                                'section'   => 'logo_sec',
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                                'width'     => 250,
                                'height'    => 100,
                                'flex_width' => true,
                                'flex_height' => true,
                                //to keep the selected cropped size
                                'dst_width'  => false,
                                'dst_height'  => false
              ),
              //force logo resize 250 * 85
              'tc_logo_resize'  => array(
                                'default'   =>  1,
                                'label'     =>  __( 'Force logo dimensions to max-width:250px and max-height:100px' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'   =>  'logo_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( "Uncheck this option to keep your original logo dimensions." , 'customizr')
              ),
              'tc_sticky_logo_upload'  => array(
                                'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                'label'     =>  __( 'Sticky Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                'section'   =>  'logo_sec' ,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                        //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                                'width'     => 75,
                                'height'    => 30,
                                'flex_width' => true,
                                'flex_height' => true,
                                //to keep the selected cropped size
                                'dst_width'  => false,
                                'dst_height'  => false,
                                'notice'    => __( "Use this upload control to specify a different logo on sticky header mode." , 'customizr')
              ),

              //favicon
              'tc_fav_upload' => array(
                                'control'   =>  'CZR_Customize_Upload_Control' ,
                                'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                                'title'     => __( 'FAVICON' , 'customizr'),
                                'section'   =>  'logo_sec' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number'),
              )
      );
    }

    /*-----------------------------------------------------------------------------------------------------
                                      SKIN SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_skin_option_map( $get_default = null ) {
      return array(
              //skin select
              'tc_skin'     => array(
                                'default'   =>  CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.32' , '1.2.31') ? 'blue3.css' : 'grey.css',
                                'control'   => 'CZR_controls' ,
                                'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                                'section'   =>  'skins_sec' ,
                                'type'      =>  'select' ,
                                'choices'    =>  $this -> czr_fn_build_skin_list(),
                                'transport'   =>  'postMessage',
                                'notice'    => __( 'Disabled if the random option is on.' , 'customizr' )
              ),
              'tc_skin_random' => array(
                                'default'   => 0,
                                'control'   => 'CZR_controls',
                                'label'     => __('Randomize the skin', 'customizr'),
                                'section'   => 'skins_sec',
                                'type'      => 'checkbox',
                                'notice'    => __( 'Apply a random color skin on each page load.' , 'customizr' )
              )
        );//end of skin options
    }



    /*-----------------------------------------------------------------------------------------------------
                                     FONT SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_fonts_option_map( $get_default = null ) {
      return array(
              'tc_fonts'      => array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.39' , '1.2.39') ? '_g_fjalla_cantarell': '_g_poppins',
                                'label'         => __( 'Select a beautiful font pair (headings &amp; default fonts) or single font for your website.' , 'customizr' ),
                                'control'       =>  'CZR_controls',
                                'section'       => 'fonts_sec',
                                'type'          => 'select' ,
                                'choices'       => CZR_utils::$inst -> czr_fn_get_font( 'list' , 'name' ),
                                'priority'      => 10,
                                'transport'     => 'postMessage',
                                'notice'        => __( "This font picker allows you to preview and select among a handy selection of font pairs and single fonts. If you choose a pair, the first font will be applied to the site main headings : site name, site description, titles h1, h2, h3., while the second will be the default font of your website for any texts or paragraphs." , 'customizr' )
              ),
              'tc_body_font_size'      => array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.2.9', '1.0.1' ) ? 14 : 15,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'label'         => __( 'Set your website default font size in pixels.' , 'customizr' ),
                                'control'       =>  'CZR_controls',
                                'section'       => 'fonts_sec',
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 20,
                                'transport'     => 'postMessage',
                                'notice'        => __( "This option sets the default font size applied to any text element of your website, when no font size is already applied." , 'customizr' )
              )
      );
    }


    /*-----------------------------------------------------------------------------------------------------
                             SOCIAL NETWORKS + POSITION SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_social_option_map( $get_default = null  ) {
      return array(
          'tc_social_links' => array(
                'default'   => array(),//empty array by default
                'control'   => 'CZR_Customize_Modules',
                'label'     => __('Create and organize your social links', 'customizr'),
                'section'   => 'socials_sec',
                'type'      => 'czr_module',
                'module_type' => 'czr_social_module',
                'transport' => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                'priority'  => 10,
          )
      );
    }

    /*-----------------------------------------------------------------------------------------------------
                                   LINKS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_links_option_map( $get_default = null ) {
      return array(
              'tc_link_scroll'  =>  array(
                                'default'       => 0,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Smooth scroll on click' , 'customizr' ),
                                'section'     => 'links_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'      => sprintf( '%s<br/><strong>%s</strong> : %s', __( 'If enabled, this option activates a smooth page scroll when clicking on a link to an anchor of the same page.' , 'customizr' ), __( 'Important note' , 'customizr' ), __('this option can create conflicts with some plugins, make sure that your plugins features (if any) are working fine after enabling this option.', 'customizr') )
              ),
              'tc_link_hover_effect'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Fade effect on link hover" , "customizr" ),
                                'section'       => 'links_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),

              'tc_ext_link_style'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display an icon next to external links" , "customizr" ),
                                'section'       => 'links_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                'transport'     => 'postMessage'
              ),

              'tc_ext_link_target'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Open external links in a new tab" , "customizr" ),
                                'section'       => 'links_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'notice'    => __( 'This will be applied to the links included in post or page content only.' , 'customizr' ),
                                'transport'     => 'postMessage'
              )
      );//end of links options
    }



    /*-----------------------------------------------------------------------------------------------------
                                   ICONS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_icons_option_map( $get_default = null ) {
      return array(
              'tc_show_title_icon'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display icons next to titles" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 10,
                                'notice'    => __( 'When this option is checked, a contextual icon is displayed next to the titles of pages, posts, archives, and WP built-in widgets.' , 'customizr' ),
                                'transport'   => 'postMessage'
              ),
              'tc_show_page_title_icon'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display a page icon next to the page title" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_title_icon'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display a post icon next to the single post title" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 30,
                                'transport'   => 'postMessage'
              ),
              'tc_show_archive_title_icon'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display an icon next to the archive title" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, an archive type icon is displayed in the heading of every types of archives, on the left of the title. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 40,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_list_title_icon'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.0' , '1.0.11' ) ? 1 : 0,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display an icon next to each post title in an archive page" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, a post type icon is displayed on the left of each post titles in an archive page. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 50,
                                'transport'   => 'postMessage'
              ),
              'tc_show_sidebar_widget_icon'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "WP sidebar widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 60,
                                'transport'   => 'postMessage'
              ),
              'tc_show_footer_widget_icon'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "WP footer widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'titles_icons_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 70,
                                'transport'   => 'postMessage'
              )
      );
    }


    /*-----------------------------------------------------------------------------------------------------
                                   IMAGE SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_images_option_map( $get_default = null ) {
      global $wp_version;

      $_image_options =  array(
              'tc_fancybox' =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Lightbox effect on images' , 'customizr' ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option activates a popin window whith a zoom effect when an image is clicked. Note : to enable this effect on the images of your pages and posts, images have to be linked to the Media File.' , 'customizr' ),
              ),

              'tc_fancybox_autoscale' =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
              ),

              'tc_retina_support' =>  array(
                                'default'       => 0,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'High resolution (Retina) support' , 'customizr' ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => sprintf('%1$s <strong>%2$s</strong> : <a href="%4$splugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails" title="%5$s" target="_blank">%3$s</a>.',
                                    __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
                                    __( "It is strongly recommended to regenerate your media library images in high definition with this free plugin" , 'customizr'),
                                    __( "regenerate thumbnails" , 'customizr'),
                                    admin_url(),
                                    __( "Open the description page of the Regenerate thumbnails plugin" , 'customizr')
                                )
              ),
              'tc_slider_parallax'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Sliders : use parallax scrolling" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, your slides scroll slower than the page (parallax effect).' , 'customizr' ),
              ),
              'tc_display_slide_loader'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Sliders : display on loading icon before rendering the slides" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'When checked, this option displays a loading icon when the slides are being setup.' , 'customizr' ),
              ),

               'tc_center_slider_img'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Dynamic slider images centering on any devices" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                //'notice'    => __( 'This option dynamically centers your images on any devices vertically or horizontally (without stretching them) according to their initial dimensions.' , 'customizr' ),
              ),
              'tc_center_img'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Dynamic thumbnails centering on any devices" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'This option dynamically centers your images on any devices, vertically or horizontally according to their initial aspect ratio.' , 'customizr' ),
              )
      );//end of images options
      //add responsive image settings for wp >= 4.4
      if ( version_compare( $wp_version, '4.4', '>=' ) )
        $_image_options = array_merge( $_image_options, array(
               'tc_resp_slider_img'  =>  array(
                                'default'     => 0,
                                'control'     => 'CZR_controls' ,
                                'title'       => __( 'Responsive settings', 'customizr' ),
                                'label'       => __( "Enable the WordPress responsive image feature for the slider" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'type'        => 'checkbox' ,
              ),
              'tc_resp_thumbs_img'  =>  array(
                                'default'     => 0,
                                'control'     => 'CZR_controls' ,
                                'label'       => __( "Enable the WordPress responsive image feature for the theme's thumbnails" , "customizr" ),
                                'section'     => 'images_sec' ,
                                'notice'      => __( 'This feature has been introduced in WordPress v4.4+ (dec-2015), and might have minor side effects on some of your existing images. Check / uncheck this option to safely verify that your images are displayed nicely.' , 'customizr' ),
                                'type'        => 'checkbox' ,
              )
          )
        );

      return $_image_options;
    }






    /*-----------------------------------------------------------------------------------------------------
                                  RESPONSIVE SETTINGS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_responsive_option_map( $get_default = null ) {
      return array(
              'tc_block_reorder'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( 'Dynamic sidebar reordering on small devices' , 'customizr' ) ),
                                'section'     => 'responsive_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Activate this option to move the sidebars (if any) after the main content block, for smartphones or tablets viewport.' , 'customizr' ),
              )
      );//end of links options
    }



    /*-----------------------------------------------------------------------------------------------------
                                  AUTHORS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_authors_option_map( $get_default = null ) {
      return array(
              'tc_show_author_info'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display an author box after each single post content" , "customizr" ),
                                'section'       => 'authors_sec',
                                'type'          => 'checkbox',
                                'priority'      => 1,
                                'notice'        =>  __( 'Check this option to display an author info block after each single post content. Note : the Biographical info field must be filled out in the user profile.' , 'customizr' ),
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                  SMOOTH SCROLL SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_smoothscroll_option_map( $get_default = null ) {
      return array(
              'tc_smoothscroll'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __("Enable Smooth Scroll", "customizr"),
                                'section'       => 'smoothscroll_sec',
                                'type'          => 'checkbox',
                                'priority'      => 1,
                                'notice'    => __( 'This option enables a smoother page scroll.' , 'customizr' ),
                                'transport'     => 'postMessage'
              )
      );
    }



    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : HEADER
    *******************************************************************************************************
    ******************************************************************************************************/
    /*-----------------------------------------------------------------------------------------------------
                                   HEADER DESIGN AND LAYOUT
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_header_design_option_map( $get_default = null ) {
      return array(
              'tc_header_layout'  =>  array(
                              'default'       => 'left',
                              'title'         => __( 'Header design and layout' , 'customizr'),
                              'control'       => 'CZR_controls' ,
                              'label'         => __( "Choose a layout for the header" , "customizr" ),
                              'section'       => 'header_layout_sec' ,
                              'type'          =>  'select' ,
                              'choices'       => array(
                                      'left'      => __( 'Logo / title on the left' , 'customizr' ),
                                      'centered'  => __( 'Logo / title centered' , 'customizr'),
                                      'right'     => __( 'Logo / title on the right' , 'customizr' )
                              ),
                              'priority'      => 5,
                              'transport'    => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                              'notice'    => __( 'This setting might impact the side on which the menu is revealed.' , 'customizr' ),
              ),
              //enable/disable top border
              'tc_top_border' => array(
                                'default'       =>  1,//top border on by default
                                'label'         =>  __( 'Display top border' , 'customizr' ),
                                'control'       =>  'CZR_controls' ,
                                'section'       =>  'header_layout_sec' ,
                                'type'          =>  'checkbox' ,
                                'notice'        =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
                                'priority'      => 10
              ),
              'tc_show_tagline'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display the tagline in the header" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 15,
                                'transport'    => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                                'ubq_section'   => array(
                                                    'section' => 'title_tagline',
                                                    'priority' => '30'
                                                 )
              ),
              'tc_woocommerce_header_cart' => array(
                               'default'   => 1,
                               'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( "Display the shopping cart in the header" , "customizr" ) ),
                               'control'   => 'CZR_controls' ,
                               'section'   => 'header_layout_sec',
                               'notice'    => __( "WooCommerce: check to display a cart icon showing the number of items in your cart next to your header's tagline.", 'customizr' ),
                               'type'      => 'checkbox' ,
                               'priority'  => 18,
                               'active_callback' => apply_filters( 'tc_woocommerce_options_enabled', '__return_false' )
              ),
              'tc_social_in_header' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in header' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'header_layout_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 20,
                                'transport'    => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                                'ubq_section'   => array(
                                                    'section' => 'socials_sec',
                                                    'priority' => '1'
                                                 )
              ),
              'tc_display_boxed_navbar'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.13', '1.0.18' ) ? 1 : 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display menu in a box" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 25,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
              ),
              'tc_sticky_header'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Sticky header settings' , 'customizr'),
                                'label'         => __( "Sticky on scroll" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option makes the header stick to the top of the page on scroll down.' , 'customizr' )
              ),
              'tc_sticky_show_tagline'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'transport'     => 'postMessage',
              ),
              'tc_woocommerce_header_cart_sticky' => array(
                               'default'   => 1,
                               'label'     => sprintf('<span class="dashicons dashicons-cart"></span> %s', __( "Sticky header: display the shopping cart" , "customizr" ) ),
                               'control'   => 'CZR_controls' ,
                               'section'   => 'header_layout_sec',
                               'type'      => 'checkbox' ,
                               'priority'  => 45,
                               'transport' => 'postMessage',
                               'active_callback' => apply_filters( 'tc_woocommerce_options_enabled', '__return_false' ),
                               'notice'    => __( 'WooCommerce: if checked, your WooCommerce cart icon will remain visible when scrolling.' , 'customizr' )
              ),
              'tc_sticky_show_title_logo'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Sticky header : display the title / logo" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50,
                                'transport'     => 'postMessage',
              ),
              'tc_sticky_shrink_title_logo'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Sticky header : shrink title / logo" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
              ),
              'tc_sticky_show_menu'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Sticky header : display the menu" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
                                'notice'        => __('Also applied to the secondary menu if any.' , 'customizr')
              ),
              'tc_sticky_transparent_on_scroll'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Sticky header : semi-transparent on scroll" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 67,
                                'transport'     => 'postMessage',
              ),
              'tc_sticky_z_index'  =>  array(
                                'default'       => 100,
                                'control'       => 'CZR_controls' ,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'label'         => __( "Set the header z-index" , "customizr" ),
                                'section'       => 'header_layout_sec' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'transport'     => 'postMessage',
                                'notice'    => sprintf('%1$s <a href="%2$s" target="_blank">%3$s</a> ?',
                                    __( "What is" , 'customizr' ),
                                    esc_url('https://developer.mozilla.org/en-US/docs/Web/CSS/z-index'),
                                    __( "the z-index" , 'customizr')
                                ),
              )

      );
    }




    /*-----------------------------------------------------------------------------------------------------
                        NAVIGATION SECTION
    ------------------------------------------------------------------------------------------------------*/
    //NOTE : priorities 10 and 20 are "used" bu menus main and secondary
    function czr_fn_navigation_option_map( $get_default = null ) {
      return array(
              'tc_display_second_menu'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display a secondary (horizontal) menu in the header." , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 15,//must be located between the two menus
                                'notice'        => __( "When you've set your main menu as a vertical side navigation, you can check this option to display a complementary horizontal menu in the header." , 'customizr' ),
              ),
              'tc_menu_style'  =>  array(
                              'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'navbar' : 'aside',
                              'control'       => 'CZR_controls' ,
                              'title'         => __( 'Main menu design' , 'customizr'),
                              'label'         => __( 'Select a design : side menu (vertical) or regular (horizontal)' , 'customizr' ),
                              'section'       => 'nav' ,
                              'type'          => 'select',
                              'choices'       => array(
                                      'navbar'   => __( 'Regular (horizontal)'   ,  'customizr' ),
                                      'aside'    => __( 'Side Menu (vertical)' ,  'customizr' ),
                              ),
                              'priority'      => 30
              ),
              'tc_menu_resp_dropdown_limit_to_viewport'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "For mobile devices (responsive), limit the height of the dropdown menu block to the visible viewport." , "customizr" ) ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 35,
                                //'transport'     => 'postMessage',
              ),
              'tc_display_menu_label'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display a label next to the menu button." , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 45,
                                'notice'        => __( 'Note : the label is hidden on mobile devices.' , 'customizr' ),
              ),
              'tc_menu_position'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'pull-menu-left' : 'pull-menu-right',
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Menu position (for "main" menu)' , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                        'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                ),
                                'priority'      => 50,
                                'transport'     => 'postMessage',
              ),
              'tc_second_menu_position'  =>  array(
                                'default'       => 'pull-menu-left',
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Secondary (horizontal) menu design' , 'customizr'),
                                'label'         => __( 'Menu position (for the horizontal menu)' , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                        'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                ),
                                'priority'      => 55,
                                'transport'     => 'postMessage'
              ),
              //The hover menu type has been introduced in v3.1.0.
              //For users already using the theme (no theme's option set), the default choice is click, for new users, it is hover.
              'tc_menu_type'  => array(
                                'default'   =>  CZR_utils::$inst -> czr_fn_user_started_before_version( '3.1.0' , '1.0.0' ) ? 'click' : 'hover',
                                'control'   =>  'CZR_controls' ,
                                'label'     =>  __( 'Select a submenu expansion option' , 'customizr' ),
                                'section'   =>  'nav' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'click'   => __( 'Expand submenus on click' , 'customizr'),
                                        'hover'   => __( 'Expand submenus on hover' , 'customizr'  ),
                                ),
                                'priority'  =>   60
              ),
              'tc_menu_submenu_fade_effect'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Reveal the sub-menus blocks with a fade effect" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 70,
                                'transport'     => 'postMessage',
              ),
              'tc_menu_submenu_item_move_effect'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Hover move effect for the sub menu items" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 80,
                                'transport'     => 'postMessage',
              ),
              'tc_second_menu_resp_setting'  =>  array(
                                'default'       => 'in-sn-before',
                                'control'       => 'CZR_controls' ,
                                'label'         => sprintf('<span class="dashicons dashicons-smartphone"></span> %s', __( "Choose a mobile devices (responsive) behaviour for the secondary menu." , "customizr" ) ),
                                'section'       => 'nav',
                                'type'      =>  'select',
                                'choices'     => array(
                                    'in-sn-before'   => __( 'Move before inside the side menu ' , 'customizr'),
                                    'in-sn-after'   => __( 'Move after inside the side menu ' , 'customizr'),
                                    'display-in-header'   => __( 'Display in the header' , 'customizr'),
                                    'hide'   => __( 'Hide' , 'customizr'  ),
                                ),
                                'priority'      => 90,
                                // 'notice'        => __( 'Note : the label is hidden on mobile devices.' , 'customizr' ),
              ),
              'tc_hide_all_menus'  =>  array(
                                'default'       => 0,
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Remove all the menus.' , 'customizr'),
                                'label'         => __( "Don't display any menus in the header of your website" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 100,//must be located between the two menus
                                'notice'        => __( 'Use with caution : provide an alternative way to navigate in your website for your users.' , 'customizr' ),
              ),
      ); //end of navigation options
    }






    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : CONTENT
    *******************************************************************************************************
    ******************************************************************************************************/
    /*-----------------------------------------------------------------------------------------------------
                                   FRONT PAGE SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_front_page_option_map( $get_default = null ) {
      //prepare the cat picker notice
      global $wp_version;
      $_cat_picker_notice = sprintf( '%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' ,
        __( "Click inside the above field and pick post categories you want to display. No filter will be applied if empty.", 'customizr'),
        esc_url('codex.wordpress.org/Posts_Categories_SubPanel'),
        __('Learn more about post categories in WordPress' , 'customizr')
      );
      //for wp version >= 4.3 add deep links
      if ( ! version_compare( $wp_version, '4.3', '<' ) ) {
        $_cat_picker_notice = sprintf( '%1$s<br/><br/><ul><li>%2$s</li><li>%3$s</li></ul>',
          $_cat_picker_notice,
          sprintf( '%1$s <a href="%2$s">%3$s &raquo;</a>',
            __("Set the number of posts to display" , "customizr"),
            "javascript:wp.customize.section('frontpage_sec').container.find('.customize-section-back').trigger('click'); wp.customize.control('posts_per_page').focus();",
            __("here", "customizr")
          ),
          sprintf( '%1$s <a href="%2$s">%3$s &raquo;</a>',
            __('Jump to the blog design options' , 'customizr'),
            "javascript:wp.customize.section('frontpage_sec').container.find('.customize-section-back').trigger('click'); wp.customize.control('tc_theme_options[tc_post_list_grid]').focus();",
            __("here", "customizr")
          )
        );
      }


      return array(
              //title
              'homecontent_title'         => array(
                      'setting_type'  =>  null,
                      'control'   =>  'CZR_controls' ,
                      'title'       => __( 'Choose content and layout' , 'customizr' ),
                      'section'     => 'frontpage_sec' ,
                      'type'      => 'title' ,
                      'priority'      => 0,
              ),

              //show on front
              'show_on_front'           => array(
                                'label'     =>  __( 'Front page displays' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'      => 'select' ,
                                'priority'      => 1,
                                'choices'     => array(
                                        'nothing'   => __( 'Don\'t show any posts or page' , 'customizr'),
                                        'posts'   => __( 'Your latest posts' , 'customizr'),
                                        'page'    => __( 'A static page' , 'customizr'  ),
                                ),
              ),

              //page on front
              'page_on_front'           => array(
                                'label'     =>  __( 'Front page' , 'customizr'  ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),

              //page for posts
              'page_for_posts'          => array(
                                'label'     =>  __( 'Posts page' , 'customizr'  ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),
              'tc_show_post_navigation_home'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display navigation in your home blog" , "customizr" ),
                                'section'       => 'frontpage_sec',
                                'type'          => 'checkbox',
                                'priority'      => 1,
                                'transport'     => 'postMessage',
              ),
              //page for posts
              'tc_blog_restrict_by_cat'       => array(
                                'default'     => array(),
                                'label'       =>  __( 'Apply a category filter to your home / blog posts' , 'customizr'  ),
                                'section'     => 'frontpage_sec',
                                'control'     => 'CZR_Customize_Multipicker_Categories_Control',
                                'type'        => 'czr_multiple_picker',
                                'priority'    => 1,
                                'notice'      => $_cat_picker_notice
              ),
              //layout
              'tc_front_layout' => array(
                                'default'       => 'f' ,//Default layout for home page is full width
                                'label'       =>  __( 'Set up the front page layout' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'control'     => 'CZR_controls' ,
                                'type'        => 'select' ,
                                'choices'     => $this -> czr_fn_layout_choices(),
                                'priority'    => 2,
              ),

              //select slider
              'tc_front_slider' => array(
                                'default'     => 'demo' ,
                                'control'     => 'CZR_controls' ,
                                'title'       => __( 'Slider options' , 'customizr' ),
                                'label'       => __( 'Select front page slider' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'select' ,
                                //!important
                                'choices'     => ( true == $get_default ) ? null : $this -> czr_fn_slider_choices(),
                                'priority'    => 20
              ),
              //posts slider
              'tc_posts_slider_number' => array(
                                'default'     => 1 ,
                                'control'     => 'CZR_controls',
                                'label'       => __('Number of posts to display', 'customizr'),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'number',
                                'priority'    => 22,
                                'notice'      => __( "Only the posts with a featured image or at least an image inside their content will qualify for the slider. The number of post slides displayed won't exceed the number of available posts in your website.", 'customizr' )
              ),
              'tc_posts_slider_stickies' => array(
                                'default'     => 0,
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Include only sticky posts' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'    => 23,
                                'notice'      => sprintf('%1$s <a href="https://codex.wordpress.org/Sticky_Posts" target="_blank">%2$s</a>',
                                    __( 'You can choose to display only the sticky posts. If you\'re not sure how to set a sticky post, check', 'customizr' ),
                                    __('the WordPress documentation.', 'customizr' )
                                )

              ),
              'tc_posts_slider_title' => array(
                                'default'     => 1,
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Display the title' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'    => 24,
                                'notice'      => __( 'The title will be limited to 80 chars max', 'customizr' ),
              ),
              'tc_posts_slider_text' => array(
                                'default'     => 1,
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Display the excerpt' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'    => 25,
                                'notice'      => __( 'The excerpt will be limited to 80 chars max', 'customizr' ),
              ),
              'tc_posts_slider_link' => array(
                                'default'     => 'cta',
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Link post with' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'select' ,
                                'choices'     => array(
                                    'cta'        => __('Call to action button', 'customizr' ),
                                    'slide'      => __('Entire slide', 'customizr' ),
                                    'slide_cta'  => __('Entire slide and call to action button', 'customizr' )
                                ),
                                'priority'    => 26,

              ),
              'tc_posts_slider_button_text' => array(
                                'default'     => __( 'Read more &raquo;' , 'customizr' ),
                                'label'       => __( 'Button text' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'text' ,
                                'priority'    => 28,
                                'notice'      => __( 'The button text will be limited to 80 chars max. Leave this field empty to hide the button', 'customizr' ),
              ),
              //select slider
              'tc_slider_width' => array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Full width slider' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 30,
                                'notice'      => __( "When checked, the front page slider occupies the full viewport's width", 'customizr' ),
              ),

              //Delay between each slides
              'tc_slider_delay' => array(
                                'default'       => 5000,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Delay between each slides' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'number' ,
                                'step'      => 500,
                                'min'     => 1000,
                                'notice'    => __( 'in ms : 1000ms = 1s' , 'customizr' ),
                                'priority'      => 50,
              ),
              'tc_slider_default_height' => array(
                                'default'       => 500,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Set slider's height in pixels" , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'       => 0,
                                'priority'      => 52,
                                'transport' => 'postMessage'
              ),
              'tc_slider_default_height_apply_all'  =>  array(
                                'default'       => 1,
                                'label'       => __( 'Apply this height to all sliders' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 53,
              ),
              'tc_slider_change_default_img_size'  =>  array(
                                'default'       => 0,
                                'label'       => __( "Replace the default image slider's height" , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 54,
                                'notice'    => sprintf('%1$s <a href="http://docs.presscustomizr.com/article/74-recommended-plugins-for-the-customizr-wordpress-theme/#images" target="_blank">%2$s</a>',
                                    __( "If this option is checked, your images will be resized with your custom height on upload. This is better for your overall loading performance." , 'customizr' ),
                                    __( "You might want to regenerate your thumbnails." , 'customizr')
                                ),
              ),

              //Front page widget area
              'tc_show_featured_pages'  => array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'title'       => __( 'Featured pages options' , 'customizr' ),
                                'label'       => __( 'Display home featured pages area' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'select' ,
                                'choices'     => array(
                                        1 => __( 'Enable' , 'customizr' ),
                                        0 => __( 'Disable' , 'customizr' ),
                                ),
                                'priority'        => 55,
              ),

              //display featured page images
              'tc_show_featured_pages_img' => array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Show images' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'The images are set with the "featured image" of each pages (in the page edit screen). Uncheck the option above to disable the featured page images.' , 'customizr' ),
                                'priority'      => 60,
              ),

              //display featured page images
              'tc_featured_page_button_text' => array(
                                'default'       => __( 'Read more &raquo;' , 'customizr' ),
                                'transport'     =>  'postMessage',
                                'label'       => __( 'Button text' , 'customizr' ),
                                'section'     => 'frontpage_sec' ,
                                'type'        => 'text' ,
                                'priority'      => 65,
              )

      );//end of front_page_options
    }






    /*-----------------------------------------------------------------------------------------------------
                                   PAGES AND POST LAYOUT SETTINGS
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_layout_option_map( $get_default = null ) {
      return array(
              //Global sidebar layout
              'tc_sidebar_global_layout' => array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'         => __( 'Choose the global default layout' , 'customizr' ),
                              'section'     => 'post_layout_sec' ,
                              'type'          => 'select' ,
                              'choices'     => $this -> czr_fn_layout_choices(),
                              'notice'      => __( 'Note : the home page layout has to be set in the home page section' , 'customizr' ),
                              'priority'      => 10
               ),

              //force default layout on every posts
              'tc_sidebar_force_layout' =>  array(
                              'default'       => 0,
                              'control'     => 'CZR_controls' ,
                              'label'         => __( 'Force default layout everywhere' , 'customizr' ),
                              'section'       => 'post_layout_sec' ,
                              'type'          => 'checkbox' ,
                              'notice'      => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                              'priority'      => 20
              ),

              //Post sidebar layout
              'tc_sidebar_post_layout'  =>  array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                              'section'     => 'post_layout_sec' ,
                              'type'        => 'select' ,
                              'choices'   => $this -> czr_fn_layout_choices(),
                              'priority'      => 30
              ),

              //Post per page
              'posts_per_page'  =>  array(
                              'default'     => get_option( 'posts_per_page' ),
                              'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                              'control'     => 'CZR_controls' ,
                              'title'         => __( 'Global Post Lists Settings' , 'customizr' ),
                              'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                              'section'       => 'post_lists_sec' ,
                              'type'          => 'number' ,
                              'step'        => 1,
                              'min'         => 1,
                              'priority'       => 10,
              ),

              //Post list length
              'tc_post_list_length' =>  array(
                                'default'       => 'excerpt',
                                'label'         => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'select' ,
                                'choices'       => array(
                                        'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                        'full'    => __( 'Display the full content' , 'customizr' )
                                        ),
                                'priority'       => 20,
              ),

              //Page sidebar layout
              'tc_sidebar_page_layout'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                                'section'     => 'post_layout_sec' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> czr_fn_layout_choices(),
                                'priority'       => 40,
                                'notice'    => sprintf('<br/> %s<br/>%s',
                                    sprintf( __("The above layout options will set your layout globally for your post and pages. But you can also define the layout for each post and page individually. Learn how in the %s.", "customizr"),
                                        sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('http://docs.presscustomizr.com/article/107-customizr-theme-options-pages-and-posts-layout'), __("Customizr theme documentation" , "customizr" )
                                        )
                                    ),
                                    sprintf( __("If you need to change the layout design of the front page, then open the 'Front Page' section above this one.", "customizr") )
                                )
              ),
      );//end of layout_options

    }



    /*-----------------------------------------------------------------------------------------------------
                                  POST LISTS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_post_list_option_map( $get_default = null ) {
      global $wp_version;
      return array(
              'tc_post_list_excerpt_length'  =>  array(
                                'default'       => 50,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Set the excerpt length (in number of words) " , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 23
              ),
              'tc_post_list_show_thumb'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Thumbnails options' , 'customizr' ),
                                'label'         => __( "Display the post thumbnails" , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 68,
                                'notice'        => sprintf( '%s %s' , __( 'When this option is checked, the post thumbnails are displayed in all post lists : blog, archives, author page, search pages, ...' , 'customizr' ), __( 'Note : thumbnails are always displayed when the grid layout is choosen.' , 'customizr') )
              ),
              'tc_post_list_use_attachment_as_thumb'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "If no featured image is set, use the last image attached to this post." , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 70
              ),

              'tc_post_list_default_thumb'  => array(
                                'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                'label'         => __( 'Upload a default thumbnail' , 'customizr' ),
                                'section'   =>  'post_lists_sec' ,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                        //we can define suggested cropping area and allow it to be flexible (def 150x150 and not flexible)
                                'width'         => 570,
                                'height'        => 350,
                                'flex_width'    => true,
                                'flex_height'   => true,
                                //to keep the selected cropped size
                                'dst_width'     => false,
                                'dst_height'    => false,
                                'priority'      =>  73
              ),


              'tc_post_list_thumb_shape'  =>  array(
                                'default'       => 'rounded',
                                'control'     => 'CZR_controls' ,
                                'title'         => __( 'Thumbnails options for the alternate thumbnails layout' , 'customizr' ),
                                'label'         => __( "Thumbnails shape" , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'rounded'               => __( 'Rounded, expand on hover' , 'customizr'),
                                        'rounded-expanded'      => __( 'Rounded, no expansion' , 'customizr'),
                                        'squared'               => __( 'Squared, expand on hover' , 'customizr'),
                                        'squared-expanded'      => __( 'Squared, no expansion' , 'customizr'),
                                        'rectangular'           => __( 'Rectangular with no effect' , 'customizr'  ),
                                        'rectangular-blurred'   => __( 'Rectangular with blur effect on hover' , 'customizr'  ),
                                        'rectangular-unblurred' => __( 'Rectangular with unblur effect on hover' , 'customizr'),
                                ),
                                'priority'      => 77
              ),
              'tc_post_list_thumb_height' => array(
                                'default'       => 250,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                'section'     => 'post_lists_sec' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'     => 0,
                                'priority'      => 80,
                                'transport'   => 'postMessage'
              ),

              'tc_post_list_thumb_position'  =>  array(
                                'default'       => 'right',
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Thumbnails position" , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'top'     => __( 'Top' , 'customizr' ),
                                        'right'   => __( 'Right' , 'customizr' ),
                                        'bottom'    => __( 'Bottom' , 'customizr' ),
                                        'left'    => __( 'Left' , 'customizr' ),
                                ),
                                'priority'      => 90
              ),
              'tc_post_list_thumb_alternate'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Alternate thumbnail/content" , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 95
              ),

              /* ARCHIVE TITLES */
              'tc_cat_title'  =>  array(
                                'default'       => '',
                                'title'         => __( 'Archive titles' , 'customizr' ),
                                'label'       => __( 'Category pages titles' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'post_lists_sec' ,
                                'type'        => 'text' ,
                                'priority'       => 100
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_tag_title'  =>  array(
                                'default'         => '',
                                'label'       => __( 'Tag pages titles' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'post_lists_sec' ,
                                'type'        => 'text' ,
                                'priority'       => 105
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_author_title'  =>  array(
                                'default'         => '',
                                'label'       => __( 'Author pages titles' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'post_lists_sec' ,
                                'type'        => 'text' ,
                                'priority'       => 110
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_search_title'  =>  array(
                                'default'         => __( 'Search Results for :' , 'customizr' ),
                                'label'       => __( 'Search results page titles' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'post_lists_sec' ,
                                'type'        => 'text' ,
                                'priority'       => 115
                                //'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),

              'tc_post_list_grid'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.2.18', '1.0.13' ) ? 'alternate' : 'grid',
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Post List Design' , 'customizr' ),
                                'label'         => __( 'Select a Layout' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'select',
                                'choices'       => array(
                                        'alternate'       => __( 'Alternate thumbnails layout' , 'customizr'),
                                        'grid'            => __( 'Grid layout' , 'customizr')
                                ),
                                'priority'      => 40,
                                'notice'    => __( 'When you select the grid Layout, the post content is limited to the excerpt.' , 'customizr' ),
              ),
              'tc_grid_columns'  =>  array(
                                'default'       => '3',
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Number of columns per row' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'select',
                                'choices'       => array(
                                        '1'                     => __( '1' , 'customizr'),
                                        '2'                     => __( '2' , 'customizr'),
                                        '3'                     => __( '3' , 'customizr'),
                                        '4'                     => __( '4' , 'customizr')
                                ),
                                'priority'      => 45,
                                'notice'        => __( 'Note : columns are limited to 3 for single sidebar layouts and to 2 for double sidebar layouts.' , 'customizr' )
              ),
              'tc_grid_expand_featured'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Expand the last sticky post (for home and blog page only)' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 47
              ),
              'tc_grid_in_blog'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Apply the grid layout to Home/Blog' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 57
              ),
              'tc_grid_in_archive'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Apply the grid layout to Archives (archives, categories, author posts)' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 58
              ),
              'tc_grid_in_search'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Apply the grid layout to Search results' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 60,
                                'notice'        => __( 'Unchecked contexts are displayed with the alternate thumbnails layout.' , 'customizr' ),
               ),
              'tc_grid_shadow'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Apply a shadow to each grid items' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 61,
                                'transport'   => 'postMessage'
               ),
              'tc_grid_bottom_border'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Apply a colored bottom border to each grid items' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 62,
                                'transport'   => 'postMessage'
               ),
              'tc_grid_icons'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Display post format icons in the background' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 63,
                                'transport'   => 'postMessage'
               ),
              'tc_grid_num_words'  =>  array(
                                'default'       => 10,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'       => 'CZR_controls' ,
                                'label'         => __( 'Max. length for post titles (in words)' , "customizr" ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 1,
                                'priority'      => 64
              ),
              'tc_grid_thumb_height' => array(
                                'default'       => 350,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'       => 'CZR_controls' ,
                                'title'         => __( 'Thumbnails max height for the grid layout' , 'customizr' ),
                                'label'         => __( "Set the post grid thumbnail's max height in pixels" , 'customizr' ),
                                'section'       => 'post_lists_sec' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 65
                                //'transport'   => 'postMessage'
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                   SINGLE POSTS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_single_post_option_map( $get_default = null ) {
      return array(
          'tc_single_post_thumb_location'  =>  array(
                            'default'       => 'hide',
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Post thumbnail position" , "customizr" ),
                            'section'       => 'single_posts_sec' ,
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    'hide'                    => __( "Don't display" , 'customizr' ),
                                    '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                    '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                    '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                            ),
                            'priority'      => 10,
                            'notice'    => sprintf( '%s<br/>%s',
                              __( 'You can display the featured image (also called the post thumbnail) of your posts before their content, when they are displayed individually.' , 'customizr' ),
                              sprintf( __( "Don't know how to set a featured image to a post? Learn how in the %s.", "customizr" ),
                                  sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
                              )
                            )
          ),
          'tc_single_post_thumb_height' => array(
                            'default'       => 250,
                            'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                            'section'     => 'single_posts_sec' ,
                            'type'        => 'number' ,
                            'step'        => 1,
                            'min'         => 0,
                            'priority'      => 20,
                            'transport'   => 'postMessage'
          )
      );

    }







    /*-----------------------------------------------------------------------------------------------------
                                   BREADCRUMB SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_breadcrumb_option_map( $get_default = null ) {
        return array(
              'tc_breadcrumb' => array(
                              'default'       => 1,//Breadcrumb is checked by default
                              'label'         => __( 'Display Breadcrumb' , 'customizr' ),
                              'control'     =>  'CZR_controls' ,
                              'section'       => 'breadcrumb_sec' ,
                              'type'          => 'checkbox' ,
                              'priority'      => 1,
              ),
              'tc_show_breadcrumb_home'  =>  array(
                                'default'       => 0,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display the breadcrumb on home page" , "customizr" ),
                                'section'       => 'breadcrumb_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20
              ),
              'tc_show_breadcrumb_in_pages'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display the breadcrumb in pages" , "customizr" ),
                                'section'       => 'breadcrumb_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30

              ),
              'tc_show_breadcrumb_in_single_posts'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display the breadcrumb in single posts" , "customizr" ),
                                'section'       => 'breadcrumb_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40

              ),
              'tc_show_breadcrumb_in_post_lists'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display the breadcrumb in posts lists : blog page, archives, search results..." , "customizr" ),
                                'section'       => 'breadcrumb_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50

              ),
              'tc_breadcrumb_yoast' => array(
                                'default'   => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.39' , '1.2.39' ) ? 0 : 1,
                                'label'     => __( "Use Yoast SEO breadcrumbs" , "customizr" ),
                                'control'   => 'CZR_controls' ,
                                'section'   => 'breadcrumb_sec',
                                'notice'    => sprintf( __( "Jump to the Yoast SEO breadcrumbs %s" , "customizr"),
                                                sprintf( '<a href="%1$s" title="%3$s">%2$s &raquo;</a>',
                                                  "javascript:wp.customize.section('wpseo_breadcrumbs_customizer_section').focus();",
                                                  __("customization panel" , "customizr"),
                                                  esc_attr__("Yoast SEO breadcrumbs settings", "customizr")
                                                )
                                              ),
                                'type'      => 'checkbox' ,
                                'priority'  => 60,
                                'active_callback' => apply_filters( 'tc_yoast_breadcrumbs_option_enabled', '__return_false' )
              ),
      );

    }


    /*-----------------------------------------------------------------------------------------------------
                                  POST METAS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_post_metas_option_map( $get_default = null ){
      return array(
              'tc_show_post_metas'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts metas" , "customizr" ),
                                'section'       => 'post_metas_sec' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, the post metas (like taxonomies, date and author) are displayed below the post titles.' , 'customizr' ),
                                'priority'      => 5,
                                'transport'   => 'postMessage'
              ),
              'tc_post_metas_design'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'buttons' : 'no-buttons',
                                'control'     => 'CZR_controls' ,
                                'title'         => __( 'Metas Design' , 'customizr' ),
                                'label'         => __( "Select a design for the post metas" , "customizr" ),
                                'section'       => 'post_metas_sec' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                    'buttons'     => __( 'Buttons and text' , 'customizr' ),
                                    'no-buttons'  => __( 'Text only' , 'customizr' )
                                ),
                                'priority'      => 10
              ),
              'tc_show_post_metas_home'  =>  array(
                                'default'       => 0,
                                'control'     => 'CZR_controls' ,
                                'title'         => __( 'Select the contexts' , 'customizr' ),
                                'label'         => __( "Display posts metas on home" , "customizr" ),
                                'section'       => 'post_metas_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 15,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_metas_single_post'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts metas for single posts" , "customizr" ),
                                'section'       => 'post_metas_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_metas_post_lists'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts metas in post lists (archives, blog page)" , "customizr" ),
                                'section'       => 'post_metas_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'transport'   => 'postMessage'
              ),

              'tc_show_post_metas_categories'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls',
                                'title'         => __( 'Select the metas to display' , 'customizr' ),
                                'label'         => __( "Display hierarchical taxonomies (like categories)" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 30
              ),

              'tc_show_post_metas_tags'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls',
                                'label'         => __( "Display non-hierarchical taxonomies (like tags)" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 35
              ),

              'tc_show_post_metas_publication_date'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls',
                                'label'         => __( "Display the publication date" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 40
              ),
              'tc_show_post_metas_author'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls',
                                'label'         => __( "Display the author" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 45
              ),
              'tc_show_post_metas_update_date'  =>  array(
                                'default'       => 0,
                                'control'     => 'CZR_controls',
                                'label'         => __( "Display the update date" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 50,
                                'notice'    => __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' ),
              ),

              'tc_post_metas_update_date_format'  =>  array(
                                'default'       => 'days',
                                'control'       => 'CZR_controls',
                                'label'         => __( "Select the last update format" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'days'     => __( 'Nb of days since last update' , 'customizr' ),
                                        'date'     => __( 'Date of the last update' , 'customizr' )
                                ),
                                'priority'      => 55
              ),

              'tc_post_metas_update_notice_in_title'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 1 : 0,
                                'control'       => 'CZR_controls',
                                'title'         => __( 'Recent update notice after post titles' , 'customizr' ),
                                'label'         => __( "Display a recent update notice" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'checkbox',
                                'priority'      => 65,
                                'notice'    => __( 'If this option is checked, a customizable recent update notice is displayed next to the post title.' , 'customizr' )
              ),
              'tc_post_metas_update_notice_interval'  =>  array(
                                'default'       => 10,
                                'control'       => 'CZR_controls',
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'label'         => __( "Display the notice if the last update is less (strictly) than n days old" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'notice'    => __( 'Set a maximum interval (in days) during which the last update notice will be displayed.' , 'customizr' ),
              ),
              'tc_post_metas_update_notice_text'  =>  array(
                                'default'       => __( "Recently updated !" , "customizr" ),
                                'control'       => 'CZR_controls',
                                'label'         => __( "Update notice text" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          => 'text',
                                'priority'      => 75,
                                'transport'   => 'postMessage'
              ),
              'tc_post_metas_update_notice_format'  =>  array(
                                'default'       => 'label-default',
                                'control'       => 'CZR_controls',
                                'label'         => __( "Update notice style" , "customizr" ),
                                'section'       => 'post_metas_sec',
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'label-default'   => __( 'Default (grey)' , 'customizr' ),
                                        'label-success'   => __( 'Success (green)' , 'customizr' ),
                                        'label-warning'   => __( 'Alert (orange)' , 'customizr' ),
                                        'label-important' => __( 'Important (red)' , 'customizr' ),
                                        'label-info'      => __( 'Info (blue)' , 'customizr' )
                                ),
                                'priority'      => 80,
                                'transport'   => 'postMessage'
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                   GALLERY SECTION
    -----------------------------------------------------------------------------------------------------*/
    function czr_fn_gallery_option_map( $get_default = null ){
      return array(
              'tc_enable_gallery'  =>  array(
                                'default'       => 1,
                                'label'         => __('Enable Customizr galleries' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply Customizr effects to galleries images" , "customizr" ),
                                'section'       => 'galleries_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_gallery_fancybox'=>  array(
                                'default'       => 1,
                                'label'         => __('Enable Lightbox effect in galleries' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply lightbox effects to galleries images" , "customizr" ),
                                'section'       => 'galleries_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_gallery_style'=>  array(
                                'default'       => 1,
                                'label'         => __('Enable Customizr effects on hover' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply nice on hover expansion effect to the galleries images" , "customizr" ),
                                'section'       => 'galleries_sec' ,
                                'type'          => 'checkbox',
                                'transport'     => 'postMessage',
                                'priority'      => 1
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                   PARAGRAPHS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_paragraph_option_map( $get_default = null ){
      return array(
              'tc_enable_dropcap'  =>  array(
                                'default'       => 0,
                                'title'         => __( 'Drop caps', 'customizr'),
                                'label'         => __('Enable drop caps' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your post / page content" , "customizr" ),
                                'section'       => 'paragraphs_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),
              'tc_dropcap_minwords'  =>  array(
                                'default'       => 50,
                                'sanitize_callback' => array( $this , 'czr_fn_sanitize_number' ),
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Apply a drop cap when the paragraph includes at least the following number of words :" , "customizr" ),
                                'notice'         => __( "(number of words)" , "customizr" ),
                                'section'       => 'paragraphs_sec' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 1,
                                'priority'      => 10
              ),
              'tc_dropcap_design' => array(
                                'default'     => 'skin-shadow',
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Drop cap style' , 'customizr' ),
                                'section'     => 'paragraphs_sec',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'skin-shadow'    => __( "Skin color with shadow" , 'customizr' ),
                                        'simple-black'   => __( 'Simple black' , 'customizr' ),
                                ),
                                'priority'    => 20,
              ),
              'tc_post_dropcap'  =>  array(
                                'default'       => 0,
                                'label'         => __('Enable drop caps in posts' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your single posts content" , "customizr" ),
                                'section'       => 'paragraphs_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 30
              ),
              'tc_page_dropcap'  =>  array(
                                'default'       => 0,
                                'label'         => __('Enable drop caps in pages' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'notice'         => __( "Apply a drop cap to the first paragraph of your pages" , "customizr" ),
                                'section'       => 'paragraphs_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 40
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                   COMMENTS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_comment_option_map( $get_default = null ) {
      return array(
              'tc_comment_show_bubble'  =>  array(
                                'default'       => 1,
                                'title'         => __('Comments bubbles' , 'customizr'),
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display the number of comments in a bubble next to the post title" , "customizr" ),
                                'section'       => 'comments_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),

              'tc_comment_bubble_shape' => array(
                                'default'     => 'default',
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Comments bubble shape' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'default'             => __( "Small bubbles" , 'customizr' ),
                                        'custom-bubble-one'   => __( 'Large bubbles' , 'customizr' ),
                                ),
                                'priority'    => 10,
              ),

              'tc_comment_bubble_color_type' => array(
                                'default'     => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'custom' : 'skin',
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'skin'     => __( "Skin color" , 'customizr' ),
                                        'custom'   => __( 'Custom' , 'customizr' ),
                                ),
                                'priority'    => 20,
              ),
              'tc_comment_bubble_color' => array(
                                'default'     => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? '#F00' : CZR_utils::$inst -> czr_fn_get_skin_color(),
                                'control'     => 'WP_Customize_Color_Control',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'        =>  'color' ,
                                'priority'    => 30,
                                'sanitize_callback'    => array( $this, 'czr_fn_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color',
                                'transport'   => 'postMessage'
              ),
              'tc_page_comments'  =>  array(
                                'default'     => 0,
                                'control'     => 'CZR_controls',
                                'title'       => __( 'Other comments settings' , 'customizr'),
                                'label'       => __( 'Enable comments on pages' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'        => 'checkbox',
                                'priority'    => 40,
                                'notice'      => sprintf('%1$s<br/> %2$s <a href="%3$s" target="_blank">%4$s</a>',
                                    __( 'If checked, this option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
                                    __( "You can also change other comments settings in :" , 'customizr'),
                                    admin_url() . 'options-discussion.php',
                                    __( 'the discussion settings page.' , 'customizr' )
                                ),
              ),
              'tc_post_comments'  =>  array(
                                'default'     => 1,
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Enable comments on posts' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'        => 'checkbox',
                                'priority'    => 45,
                                'notice'      => sprintf('%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>.<br/>%4$s <a href="%5$s" target="_blank">%6$s</a>',
                                    __( 'If checked, this option enables comments on all types of single posts. You can disable comments for a single post in quick edit mode from the' , 'customizr' ),
                                    esc_url('codex.wordpress.org/Posts_Screen'),
                                    __( 'post screen', 'customizr'),
                                    __( "You can also change other comments settings in the" , 'customizr'),
                                    admin_url('options-discussion.php'),
                                    __( 'discussion settings page.' , 'customizr' )
                                ),
              ),
              'tc_show_comment_list'  =>  array(
                                'default'     => 1,
                                'control'     => 'CZR_controls',
                                'label'       => __( 'Display the comment list' , 'customizr' ),
                                'section'     => 'comments_sec',
                                'type'        => 'checkbox',
                                'priority'    => 50,
                                'notice'      =>__( 'By default, WordPress displays the past comments, even if comments are disabled in posts or pages. Unchecking this option allows you to not display this comment history.' , 'customizr' )
              )
      );
    }



    /*-----------------------------------------------------------------------------------------------------
                                   POST NAVIGATION SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_post_navigation_option_map( $get_default = null ) {
      return array(
              'tc_show_post_navigation'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts navigation" , "customizr" ),
                                'section'       => 'post_navigation_sec' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, the posts navigation is displayed below the posts' , 'customizr' ),
                                'priority'      => 5,
                                'transport'   => 'postMessage'
              ),

              'tc_show_post_navigation_page'  =>  array(
                                'default'       => 0,
                                'control'     => 'CZR_controls' ,
                                'title'         => __( 'Select the contexts' , 'customizr' ),
                                'label'         => __( "Display navigation in pages" , "customizr" ),
                                'section'       => 'post_navigation_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 10,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_navigation_single'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts navigation in single posts" , "customizr" ),
                                'section'       => 'post_navigation_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_show_post_navigation_archive'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Display posts navigation in post lists (archives, blog page, categories, search results ..)" , "customizr" ),
                                'section'       => 'post_navigation_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'transport'   => 'postMessage'
              ),
      );
    }


    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : SIDEBARS
    *******************************************************************************************************
    ******************************************************************************************************/
    /*-----------------------------------------------------------------------------------------------------
                                   SIDEBAR SOCIAL LINKS SETTINGS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_sidebars_option_map( $get_default = null ) {
      return array(
              'tc_social_in_left-sidebar' =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'sidebar_socials_sec',
                                'type'        => 'checkbox' ,
                                'priority'       => 20,
                                'ubq_section'   => array(
                                                    'section' => 'socials_sec',
                                                    'priority' => '2'
                                                 )
              ),

              'tc_social_in_right-sidebar'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'sidebar_socials_sec',
                                'type'        => 'checkbox' ,
                                'priority'       => 25,
                                'ubq_section'   => array(
                                                    'section' => 'socials_sec',
                                                    'priority' => '3'
                                                 )
              ),
              'tc_social_in_sidebar_title'  =>  array(
                                'default'       => __( 'Social links' , 'customizr' ),
                                'label'       => __( 'Social link title in sidebars' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'sidebar_socials_sec',
                                'type'        => 'text' ,
                                'priority'       => 30,
                                'transport'   => 'postMessage',
                                'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              )
      );
    }



    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : FOOTER
    *******************************************************************************************************
    ******************************************************************************************************/
    /*-----------------------------------------------------------------------------------------------------
                                   FOOTER GLOBAL SETTINGS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_footer_global_settings_option_map( $get_default = null ) {
      return array(
              'tc_social_in_footer' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in footer' , 'customizr' ),
                                'control'   =>  'CZR_controls' ,
                                'section'     => 'footer_global_sec' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 0,
                                'ubq_section'   => array(
                                                    'section' => 'socials_sec',
                                                    'priority' => '4'
                                                 )
              ),
              'tc_sticky_footer'  =>  array(
                                'default'       => CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.0' , '1.1.14' ) ? 0 : 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Stick the footer to the bottom of the page", "customizr" ),
                                'section'       => 'footer_global_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 1,
                                'transport'     => 'postMessage'
              ),
              'tc_show_back_to_top'  =>  array(
                                'default'       => 1,
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Display a back to top arrow on scroll" , "customizr" ),
                                'section'       => 'footer_global_sec' ,
                                'type'          => 'checkbox',
                                'priority'      => 5
                            ),
              'tc_back_to_top_position'  =>  array(
                                'default'       => 'right',
                                'control'       => 'CZR_controls' ,
                                'label'         => __( "Back to top arrow position" , "customizr" ),
                                'section'       => 'footer_global_sec' ,
                                'type'          => 'select',
                                'choices'       => array(
                                      'left'      => __( 'Left' , 'customizr' ),
                                      'right'     => __( 'Right' , 'customizr'),
                                ),
                                'priority'      => 5,
                                'transport'     => 'postMessage'
              ),

      );
    }





    /******************************************************************************************************
    *******************************************************************************************************
    * PANEL : ADVANCED OPTIONS
    *******************************************************************************************************
    ******************************************************************************************************/
    /*-----------------------------------------------------------------------------------------------------
                                   CUSTOM CSS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_custom_css_option_map( $get_default = null ) {
      return array(
              'tc_custom_css' =>  array(
                                'sanitize_callback' => 'wp_filter_nohtml_kses',
                                'sanitize_js_callback' => 'wp_filter_nohtml_kses',
                                'control'   => 'CZR_controls' ,
                                'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                                'section'     => 'custom_sec' ,
                                'type'        => 'textarea' ,
                                'notice'    => sprintf('%1$s <a href="%4$ssnippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                    __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                    __( 'child theme.' , 'customizr'),
                                    __( 'How to create and use a child theme ?' , 'customizr'),
                                    CZR_WEBSITE
                                ),
                                'transport'   => 'postMessage'
              ),
      );//end of custom_css_options
    }


    /*-----------------------------------------------------------------------------------------------------
                              WEBSITE PERFORMANCES SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_performance_option_map( $get_default = null ) {
      return array(
              'tc_minified_skin'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls' ,
                                'label'       => __( "Performance : use the minified CSS stylesheets", 'customizr' ),
                                'section'     => 'performances_sec' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Using the minified version of the stylesheets will speed up your webpage load time.' , 'customizr' ),
              ),
              'tc_img_smart_load'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Load images on scroll' , 'customizr' ),
                                'control'     =>  'CZR_controls',
                                'section'     => 'performances_sec',
                                'type'        => 'checkbox',
                                'priority'    => 20,
                                'notice'      => __('Check this option to delay the loading of non visible images. Images below the viewport will be loaded dynamically on scroll. This can boost performances by reducing the weight of long web pages with images.' , 'customizr')
              )
      );
    }

    /*-----------------------------------------------------------------------------------------------------
                              FRONT END NOTICES AND PLACEHOLDERS SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_placeholders_notice_map( $get_default = null ) {
      return array(
              'tc_display_front_help'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls',
                                'label'       => __( "Display help notices on front-end for logged in users.", 'customizr' ),
                                'section'     => 'placeholder_sec',
                                'type'        => 'checkbox',
                                'notice'    => __( 'When this options is enabled, various help notices and some placeholder blocks are displayed on the front-end of your website. They are only visible by logged in users with administration capabilities.' , 'customizr' )
              )
      );
    }

    /*-----------------------------------------------------------------------------------------------------
                              FRONT END EXTERNAL RESOURCES SECTION
    ------------------------------------------------------------------------------------------------------*/
    function czr_fn_external_resources_option_map( $get_default = null ) {
      return array(
              'tc_font_awesome_icons'  =>  array(
                                'default'       => 1,
                                'control'   => 'CZR_controls',
                                'label'       => __( "Load Font Awesome resources", 'customizr' ),
                                'section'     => 'extresources_sec',
                                'type'        => 'checkbox',
                                'notice'      => sprintf('<strong>%1$s</strong>. %2$s</br>%3$s',
                                    __( 'Use with caution' , 'customizr'),
                                    __( 'When checked, the Font Awesome icons and CSS will be loaded on front end. You might want to load the Font Awesome icons with a custom code, or let a plugin do it for you.', 'customizr' ),
                                    sprintf('%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>.',
                                                                        __( "Check out some example of uses", 'customizr'),
                                                                        esc_url('http://fontawesome.io/examples/'),
                                                                        __('here', 'customizr')
                                    )
                                )
              )

      );
    }

    /***************************************************************
    * POPULATE PANELS
    ***************************************************************/
    /**
    * hook : tc_add_panel_map
    * @return  associative array of customizer panels
    */
    function czr_fn_popul_panels_map( $panel_map ) {
      $_new_panels = array(
        'tc-global-panel' => array(
                  'priority'       => 10,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Global settings' , 'customizr' ),
                  'description'    => __( "Global settings for the Customizr theme :skin, socials, links..." , 'customizr' ),
                  'type'           => 'czr_panel'
        ),
        'tc-header-panel' => array(
                  'priority'       => 20,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Header' , 'customizr' ),
                  'description'    => __( "Header settings for the Customizr theme." , 'customizr' ),
                  'type'           => 'czr_panel'
        ),
        'tc-content-panel' => array(
                  'priority'       => 30,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Content : home, posts, ...' , 'customizr' ),
                  'description'    => __( "Content settings for the Customizr theme." , 'customizr' ),
                  'type'           => 'czr_panel'
        ),
        'tc-sidebars-panel' => array(
                  'priority'       => 30,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Sidebars' , 'customizr' ),
                  'description'    => __( "Sidebars settings for the Customizr theme." , 'customizr' ),
                  'type'           => 'czr_panel'
        ),
        'tc-footer-panel' => array(
                  'priority'       => 40,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Footer' , 'customizr' ),
                  'description'    => __( "Footer settings for the Customizr theme." , 'customizr' ),
                  'type'           => 'czr_panel'
        ),
        'tc-advanced-panel' => array(
                  'priority'       => 1000,
                  'capability'     => 'edit_theme_options',
                  'title'          => __( 'Advanced options' , 'customizr' ),
                  'description'    => __( "Advanced settings for the Customizr theme." , 'customizr' ),
                  'type'           => 'czr_panel'
        )
      );
      return array_merge( $panel_map, $_new_panels );
    }





    /***************************************************************
    * POPULATE REMOVE SECTIONS
    ***************************************************************/
    /**
     * hook : tc_remove_section_map
     */
    function czr_fn_popul_remove_section_map( $_sections ) {
      //customizer option array
      $remove_section = array(
        'static_front_page' ,
        'nav',
        'title_tagline',
        'tc_page_comments'
      );
      return array_merge( $_sections, $remove_section );
    }


    /***************************************************************
    * HANDLES THE THEME SWITCHER (since WP 4.2)
    ***************************************************************/
    /**
    * Print the themes section (themes switcher) when previewing the themes from wp-admin/themes.php
    * hook : tc_remove_section_map
    */
    function czr_fn_set_theme_switcher_visibility( $_sections) {
      //Don't do anything is in preview frame
      //=> because once the preview is ready, a postMessage is sent to the panel frame to refresh the sections and panels
      //Do nothing if WP version under 4.2
      global $wp_version;
      if ( CZR___::$instance -> czr_fn_is_customize_preview_frame() || ! version_compare( $wp_version, '4.2', '>=') )
        return $_sections;

      //when user access the theme switcher from the admin bar
      $_theme_switcher_requested = false;
      if ( isset( $_GET['autofocus'] ) ) {
        $autofocus = wp_unslash( $_GET['autofocus'] );
        if ( is_array( $autofocus ) && isset($autofocus['section']) ) {
          $_theme_switcher_requested = 'themes' == $autofocus['section'];
        }
      }

      if ( isset($_GET['theme']) || ! is_array($_sections) || $_theme_switcher_requested )
        return $_sections;

      array_push( $_sections, 'themes');
      return $_sections;
    }




    /***************************************************************
    * POPULATE SECTIONS
    ***************************************************************/
    /**
    * hook : tc_add_section_map
    */
    function czr_fn_popul_section_map( $_sections ) {
      //For nav menus option
      $locations      = get_registered_nav_menus();
      $menus          = wp_get_nav_menus();
      $num_locations  = count( array_keys( $locations ) );
      global $wp_version;
      $nav_section_desc =  sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations, 'customizr' ), number_format_i18n( $num_locations ) );
      //adapt the nav section description for v4.3 (menu in the customizer from now on)
      if ( version_compare( $wp_version, '4.3', '<' ) ) {
        $nav_section_desc .= "<br/>" . sprintf( __("You can create new menu and edit your menu's content %s." , "customizr"),
          sprintf( '<strong><a href="%1$s" target="_blank" title="%3$s">%2$s &raquo;</a></strong>',
            admin_url('nav-menus.php'),
            __("on the Menus screen in the Appearance section" , "customizr"),
            __("create/edit menus", "customizr")
          )
        );
      } else {
        $nav_section_desc .= "<br/>" . sprintf( __("You can create new menu and edit your menu's content %s." , "customizr"),
          sprintf( '<strong><a href="%1$s" title="%3$s">%2$s &raquo;</a><strong>',
            "javascript:wp.customize.section('nav').container.find('.customize-section-back').trigger('click'); wp.customize.panel('nav_menus').focus();",
            __("in the menu panel" , "customizr"),
            __("create/edit menus", "customizr")
          )
        );
      }

      $nav_section_desc .= "<br/><br/>". __( 'If a menu location has no menu assigned to it, a default page menu will be used.', 'customizr');

      $_new_sections = array(
        /*---------------------------------------------------------------------------------------------
        -> PANEL : GLOBAL SETTINGS
        ----------------------------------------------------------------------------------------------*/
        'title_tagline'         => array(
                            'title'    => __( 'Site Title & Tagline', 'customizr' ),
                            'priority' => $this -> is_wp_version_before_4_0 ? 7 : 0,
                            'panel'   => 'tc-global-panel'
        ),
        'logo_sec'            => array(
                            'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 8 : 5,
                            'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'skins_sec'         => array(
                            'title'     =>  __( 'Skin' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 1 : 7,
                            'description' =>  __( 'Select a skin for Customizr' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'fonts_sec'          => array(
                            'title'     =>  __( 'Fonts' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 40 : 10,
                            'description' =>  __( 'Set up the font global settings' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'socials_sec'        => array(
                            'title'     =>  __( 'Social links' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 9 : 20,
                            'description' =>  __( 'Set up your social links' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'links_sec'         => array(
                            'title'     =>  __( 'Links style and effects' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 22 : 30,
                            'description' =>  __( 'Various links settings' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'titles_icons_sec'        => array(
                            'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 18 : 40,
                            'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'images_sec'         => array(
                            'title'     =>  __( 'Image settings' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 95 : 50,
                            'description' =>  __( 'Various images settings' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'authors_sec'               => array(
                            'title'     =>  __( 'Authors' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 220 : 70,
                            'description' =>  __( 'Post authors settings' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),
        'smoothscroll_sec'          => array(
                            'title'     =>  __( 'Smooth Scroll' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 97 : 75,
                            'description' =>  __( 'Smooth Scroll settings' , 'customizr' ),
                            'panel'   => 'tc-global-panel'
        ),

        /*---------------------------------------------------------------------------------------------
        -> PANEL : HEADER
        ----------------------------------------------------------------------------------------------*/
        'header_layout_sec'         => array(
                            'title'    => $this -> is_wp_version_before_4_0 ? __( 'Header design and layout', 'customizr' ) : __( 'Design and layout', 'customizr' ),
                            'priority' => $this -> is_wp_version_before_4_0 ? 5 : 20,
                            'panel'   => 'tc-header-panel'
        ),
        'nav'           => array(
                            'title'          => __( 'Navigation Menus' , 'customizr' ),
                            'theme_supports' => 'menus',
                            'priority'       => $this -> is_wp_version_before_4_0 ? 10 : 40,
                            'description'    => $nav_section_desc,
                            'panel'   => 'tc-header-panel'
        ),


        /*---------------------------------------------------------------------------------------------
        -> PANEL : CONTENT
        ----------------------------------------------------------------------------------------------*/
        'frontpage_sec'       => array(
                            'title'     =>  __( 'Front Page' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 12 : 10,
                            'description' =>  __( 'Set up front page options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),

        'post_layout_sec'        => array(
                            'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 15 : 15,
                            'description' =>  __( 'Set up layout options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),

        'post_lists_sec'        => array(
                            'title'     =>  __( 'Post lists : blog, archives, ...' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 16 : 20,
                            'description' =>  __( 'Set up post lists options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'single_posts_sec'        => array(
                            'title'     =>  __( 'Single posts' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 17 : 24,
                            'description' =>  __( 'Set up single posts options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'breadcrumb_sec'        => array(
                            'title'     =>  __( 'Breadcrumb' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 11 : 30,
                            'description' =>  __( 'Set up breadcrumb options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),


        /*'tc_page_settings'        => array(
                            'title'     =>  __( 'Pages' , 'customizr' ),
                            'priority'    =>  25,
                            'description' =>  __( 'Set up pages options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),*/
        'post_metas_sec'        => array(
                            'title'     =>  __( 'Post metas (category, tags, custom taxonomies)' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 50,
                            'description' =>  __( 'Set up post metas options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'galleries_sec'        => array(
                            'title'     =>  __( 'Galleries' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 55,
                            'description' =>  __( 'Set up gallery options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'paragraphs_sec'        => array(
                            'title'     =>  __( 'Paragraphs' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 55,
                            'description' =>  __( 'Set up paragraphs options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'comments_sec'          => array(
                            'title'     =>  __( 'Comments' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 25 : 60,
                            'description' =>  __( 'Set up comments options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),
        'post_navigation_sec'          => array(
                            'title'     =>  __( 'Post/Page Navigation' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 30 : 65,
                            'description' =>  __( 'Set up post/page navigation options' , 'customizr' ),
                            'panel'   => 'tc-content-panel'
        ),


        /*---------------------------------------------------------------------------------------------
        -> PANEL : SIDEBARS
        ----------------------------------------------------------------------------------------------*/
        'sidebar_socials_sec'          => array(
                            'title'     =>  __( 'Socials in Sidebars' , 'customizr' ),
                            'priority'    =>  10,
                            'description' =>  __( 'Set up your social profiles links in the sidebar(s).' , 'customizr' ),
                            'panel'   => 'tc-sidebars-panel'
        ),
        'responsive_sec'           => array(
                            'title'     =>  __( 'Responsive settings' , 'customizr' ),
                            'priority'    =>  20,
                            'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                            'panel'   => 'tc-sidebars-panel'
        ),


        /*---------------------------------------------------------------------------------------------
        -> PANEL : FOOTER
        ----------------------------------------------------------------------------------------------*/
        'footer_global_sec'          => array(
                            'title'     =>  __( 'Footer global settings' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 40 : 10,
                            'description' =>  __( 'Set up footer global options' , 'customizr' ),
                            'panel'   => 'tc-footer-panel'
        ),


        /*---------------------------------------------------------------------------------------------
        -> PANEL : ADVANCED
        ----------------------------------------------------------------------------------------------*/
        'custom_sec'           => array(
                            'title'     =>  __( 'Custom CSS' , 'customizr' ),
                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 100 : 10,
                            'panel'   => 'tc-advanced-panel'
        ),
        'performances_sec'      => array(
                            'title'     =>  __( 'Website Performances' , 'customizr' ),
                            'priority'    => 20,
                            'description' =>  __( 'On the web, speed is key ! Improve the load time of your pages with those options.' , 'customizr' ),
                            'panel'   => 'tc-advanced-panel'
        ),
        'placeholder_sec'     => array(
                            'title'     =>  __( 'Front-end placeholders and help blocks' , 'customizr' ),
                            'priority'    => 30,
                            'panel'   => 'tc-advanced-panel'
        ),
        'extresources_sec'    => array(
                            'title'     =>  __( 'Front-end Icons (Font Awesome)' , 'customizr' ),
                            'priority'    => 40,
                            'panel'   => 'tc-advanced-panel'
        )
      );

      if ( ! CZR___::czr_fn_is_pro() ) {
        $_new_sections = array_merge( $_new_sections, array(
            /*---------------------------------------------------------------------------------------------
            -> SECTION : GO-PRO
            ----------------------------------------------------------------------------------------------*/
            'customizr_go_pro'   => array(
                                'title'         => esc_html__( 'Upgrade to Customizr Pro', 'customizr' ),
                                'pro_text'      => esc_html__( 'Go Pro', 'customizr' ),
                                'pro_url'       => sprintf('%scustomizr-pro/', CZR_WEBSITE ),
                                'priority'      => 0,
                                'section_class' => 'CZR_Customize_Section_Pro'
            ),
        ) );
      }

      return array_merge( $_sections, $_new_sections );
    }






    /***************************************************************
    * CONTROLS HELPERS
    ***************************************************************/
    /**
    * Generates the featured pages options
    * add the settings/controls to the relevant section
    * hook : tc_front_page_option_map
    *
    * @package Customizr
    * @since Customizr 3.0.15
    *
    */
    function czr_fn_generates_featured_pages( $_original_map ) {
      $default = array(
        'dropdown'  =>  array(
              'one'   => __( 'Home featured page one' , 'customizr' ),
              'two'   => __( 'Home featured page two' , 'customizr' ),
              'three' => __( 'Home featured page three' , 'customizr' )
        ),
        'text'    => array(
              'one'   => __( 'Featured text one (200 char. max)' , 'customizr' ),
              'two'   => __( 'Featured text two (200 char. max)' , 'customizr' ),
              'three' => __( 'Featured text three (200 char. max)' , 'customizr' )
        )
      );

      //declares some loop's vars and the settings array
      $priority       = 70;
      $incr         = 0;
      $fp_setting_control = array();

      //gets the featured pages id from init
      $fp_ids       = apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);

      //dropdown field generator
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_featured_page_'. $id]    =  array(
                      'default'     => 0,
                      'label'       => isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  sprintf( __('Custom featured page %1$s' , 'customizr' ) , $id ),
                      'section'     => 'frontpage_sec' ,
                      'type'        => 'dropdown-pages' ,
                      'priority'      => $priority
                    );
        $incr += 10;
      }

      //text field generator
      $incr         = 10;
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_featured_text_' . $id]   = array(
                      'sanitize_callback' => array( $this , 'czr_fn_sanitize_textarea' ),
                      'transport'   => 'postMessage',
                      'control'   => 'CZR_controls' ,
                      'label'       => isset($default['text'][$id]) ? $default['text'][$id] : sprintf( __('Featured text %1$s (200 char. max)' , 'customizr' ) , $id ),
                      'section'     => 'frontpage_sec' ,
                      'type'        => 'textarea' ,
                      'notice'    => __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
                      'priority'      => $priority,
                    );
        $incr += 10;
      }

      return array_merge( $_original_map , $fp_setting_control );
    }



    /**
    * Generates social network options
    * Populate the social section map of settings/controls
    * hook : tc_social_option_map
    *
    * @package Customizr
    * @since Customizr 3.0.15
    *
    */
    function czr_fn_generates_socials( $_original_map ) {
      //gets the social network array
      $socials      = apply_filters( 'tc_default_socials' , CZR_init::$instance -> socials );

      //declares some loop's vars and the settings array
      $priority     = 50;//start priority
      $incr         = 0;
      $_new_map     = array();

      foreach ( $socials as $key => $data ) {
        $priority += $incr;
        $type      = isset( $data['type'] ) && ! is_null( $data['type'] ) ? $data['type'] : 'url';

        $_new_map[$key]  = array(
                      'default'       => ( isset($data['default']) && !is_null($data['default']) ) ? $data['default'] : null,
                      'sanitize_callback' => array( $this , 'czr_fn_sanitize_' . $type ),
                      'control'       => 'CZR_controls' ,
                      'label'         => ( isset($data['option_label']) ) ? call_user_func( '__' , $data['option_label'] , 'customizr' ) : $key,
                      'section'       => 'socials_sec' ,
                      'type'          => $type,
                      'priority'      => $priority,
                      'icon'          => "tc-icon-". str_replace('tc_', '', $key)
                    );
        $incr += 5;
      }
      return array_merge( $_original_map, $_new_map );
    }




    /**
    * Generates skin select list
    *
    * @package Customizr
    * @since Customizr 3.0.15
    *
    */
    private function czr_fn_get_skins($path) {
      //checks if path exists
      if ( !file_exists($path) )
        return;

      //gets the skins from init
      $default_skin_list    = CZR_init::$instance -> skins;

      //declares the skin list array
      $skin_list        = array();

      //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
      $files            = scandir($path) ;
      foreach( $files as $file ) {
          //skips the minified and tc_common
          if ( false !== strpos($file, '.min.') || false !== strpos($file, 'tc_common') )
            continue;

          if ( $file[0] != '.' && !is_dir($path.$file) ) {
            if ( substr( $file, -4) == '.css' ) {
              $skin_list[$file] = isset($default_skin_list[$file]) ?  call_user_func( '__' , $default_skin_list[$file] , 'customizr' ) : substr_replace( $file , '' , -4 , 4);
            }
          }
        }//endforeach
      $_to_return = array();

      //Order skins like in the default array
      foreach( $default_skin_list as $_key => $value ) {
        if( isset($skin_list[$_key]) ) {
          $_to_return[$_key] = $skin_list[$_key];
        }
      }
      //add skins not included in default
      foreach( $skin_list as $_file => $_name ) {
        if( ! isset( $_to_return[$_file] ) )
          $_to_return[$_file] = $_name;
      }
      return $_to_return;
    }//end of function



    /**
    * Returns the layout choices array
    *
    * @package Customizr
    * @since Customizr 3.1.0
    */
    private function czr_fn_layout_choices() {
        $global_layout  = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
        $layout_choices = array();
        foreach ($global_layout as $key => $value) {
          $layout_choices[$key]   = ( $value['customizer'] ) ? call_user_func(  '__' , $value['customizer'] , 'customizr' ) : null ;
        }
        return $layout_choices;
    }


    /**
    * Retrieves slider names and generate the select list
    * @package Customizr
    * @since Customizr 3.0.1
    */
    private function czr_fn_slider_choices() {
      $__options    =   get_option('tc_theme_options');
      $slider_names   =   isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

      $slider_choices = array(
        0     =>  __( '&mdash; No slider &mdash;' , 'customizr' ),
        'demo'  =>  __( '&mdash; Demo Slider &mdash;' , 'customizr' ),
        'tc_posts_slider' => __('&mdash; Auto-generated slider from your blog posts &mdash;', 'customizr')
        );
      if ( $slider_names ) {
        foreach( $slider_names as $tc_name => $slides) {
          $slider_choices[$tc_name] = $tc_name;
        }
      }
      return $slider_choices;
    }


    /**
    * Returns the list of available skins from child (if exists) and parent theme
    *
    * @package Customizr
    * @since Customizr 3.0.11
    * @updated Customizr 3.0.15
    */
    private function czr_fn_build_skin_list() {
        $parent_skins   = $this -> czr_fn_get_skins(TC_BASE .'inc/assets/css');
        $child_skins    = ( CZR___::$instance -> czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css') ) ? $this -> czr_fn_get_skins(TC_BASE_CHILD .'inc/assets/css') : array();
        $skin_list      = array_merge( $parent_skins , $child_skins );

      return apply_filters( 'tc_skin_list', $skin_list );
    }



    /***************************************************************
    * SANITIZATION HELPERS
    ***************************************************************/
    /**
     * adds sanitization callback funtion : textarea
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function czr_fn_sanitize_textarea( $value) {
      $value = esc_html( $value);
      return $value;
    }



    /**
     * adds sanitization callback funtion : number
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function czr_fn_sanitize_number( $value) {
      if ( ! $value || is_null($value) )
        return $value;
      $value = esc_attr( $value); // clean input
      $value = (int) $value; // Force the value into integer type.
        return ( 0 < $value ) ? $value : null;
    }

    /**
     * adds sanitization callback funtion : url
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function czr_fn_sanitize_url( $value) {
      $value = esc_url( $value);
      return $value;
    }

    /**
     * adds sanitization callback funtion : email
     * @package Customizr
     * @since Customizr 3.4.11
     */
    function czr_fn_sanitize_email( $value) {
      return sanitize_email( $value );
    }

    /**
     * adds sanitization callback funtion : colors
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function czr_fn_sanitize_hex_color( $color ) {
      if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
        return '#' . $unhashed;

      return $color;
    }


    /**
    * Change upload's path to relative instead of absolute
    * @package Customizr
    * @since Customizr 3.1.11
    */
    function czr_fn_sanitize_uploads( $url ) {
      $upload_dir = wp_upload_dir();
      return str_replace($upload_dir['baseurl'], '', $url);
    }

  }//end of class
endif;

?><?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.39
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com> - Rocco ALIBERTI <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2017, Nicolas GUILLAUME, Rocco ALIBERTI
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_init_retro_compat' ) ) :
  class CZR_init_retro_compat {
    static $instance;

    /*
    * This is fired very early, before the new defaults are generated
    */
    function __construct () {
      self::$instance =& $this;

      //copy old options in the new framework
      //only if user is logged in
      //then each routine has to decide what to do also depending on the user started before
      if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
        $theme_options            = czr_fn_get_admin_option(CZR_THEME_OPTIONS);


        if ( ! empty( $theme_options ) ) {

          $_new_options_w_socials = $this -> czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

          $_to_update             = ! empty( $_new_options_w_socials );
          $theme_options          = $_to_update ? $_new_options_w_socials : $theme_options;

          if ( $_to_update ) {
            update_option( CZR_THEME_OPTIONS, $theme_options );
          }
        }
      }
    }//construct

    /*
    * returns array() the new set of options or empty if there's nothing to move
    */
    function czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options ) {
      $_options = $theme_options;


      //nothing t do if already moved
      if ( ! CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
        return array();

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_socials', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      $_old_socials          = CZR_init::$instance -> socials;
      $_old_filtered_socials = apply_filters( 'tc_default_socials', $_old_socials );

      /*
      * old socials were in the form
      * array( 'tc_twitter' => array( .., default=>'[url]' ,..) )
      * need to be ported in the form
      * array( 'tc_twitter' => '[url]' )
      * before parse them in the options.
      */
      $_social_options       = array();
      foreach ( $_old_filtered_socials as $social => $attrs ) {
        if ( isset( $attrs['default'] ) ) {
          $_social_options[$social] = $attrs['default'];
        }
      }

      //merge options with the defaults socials
      $_options     = wp_parse_args( $_options, $_social_options );


      $_to_update   = false;
      $_new_socials = array();
      $_index       = 0;

      /*
      * rss needs a special treatment for old users, it was a default
      * If it doesn't exist in the options we have to set it with the default value
      * if it exists but is null it will be skipped
      */
      foreach ( $_old_filtered_socials as $_old_social_id => $attributes ) {
        if ( ! empty( $_options[ $_old_social_id ] ) ) {

          //build new attributes
          $_title       = isset( $attributes[ 'link_title' ] ) ? esc_attr( $attributes[ 'link_title' ] ) :  '';
          $_social_icon = str_replace( array( 'tc_email', 'tc_'), array( 'fa-envelope', 'fa-' ), $_old_social_id );

          // email needs a special treatment
          $_social_link = esc_url_raw( 'tc_email' == $_old_social_id  ? sprintf( 'mailto:%s', $_options[ $_old_social_id ] ) : $_options[ $_old_social_id ] );

          if ( empty( $_social_link ) ) {
            continue;
          }

          //create module
          array_push( $_new_socials, array(
              'id'            => "czr_social_module_{$_index}",
              'title'         => $_title,
              'social-icon'   => $_social_icon,
              'social-link'   => $_social_link,
              'social-target' => 1,
            )
          );
          $_index++;

          $_to_update = true;
        }
      }

      if ( $_to_update ) {
        $theme_options[ 'tc_social_links' ] = $_new_socials;

        //save the state in the options
        $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
        array_push( $theme_options[ '__moved_opts' ], 'old_socials' );

        return $theme_options;
      }

      return array();
    }
  }
endif;
?><?php
/**
* Defines filters and actions used in several templates/classes
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
if ( ! class_exists( 'CZR_utils' ) ) :
  class CZR_utils {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $inst;
      static $instance;
      public $default_options;
      public $db_options;
      public $options;//not used in customizer context only
      public $is_customizing;
      public $tc_options_prefixes;

      public static $_theme_setting_list;

      function __construct () {
        self::$inst =& $this;
        self::$instance =& $this;

        //init properties
        add_action( 'after_setup_theme'       , array( $this , 'czr_fn_init_properties') );

        //IMPORTANT : this callback needs to be ran AFTER hu_init_properties.
        add_action( 'after_setup_theme'       , array( $this, 'czr_fn_cache_theme_setting_list' ), 100 );

        //Various WP filters for
        //content
        //thumbnails => parses image if smartload enabled
        //title
        add_action( 'wp_head'                 , array( $this , 'czr_fn_wp_filters') );

        //get all options
        add_filter( '__options'               , array( $this , 'czr_fn_get_theme_options' ), 10, 1);
        //get single option
        add_filter( '__get_option'            , array( $this , 'czr_fn_opt' ), 10, 2 );//deprecated

        //some useful filters
        add_filter( '__ID'                    , array( $this , 'czr_fn_id' ));//deprecated
        add_filter( '__screen_layout'         , array( $this , 'czr_fn_get_layout' ) , 10 , 2 );//deprecated
        add_filter( '__is_home'               , array( $this , 'czr_fn_is_home' ) );
        add_filter( '__is_home_empty'         , array( $this , 'czr_fn_is_home_empty' ) );
        add_filter( '__post_type'             , array( $this , 'czr_fn_get_post_type' ) );
        add_filter( '__is_no_results'         , array( $this , 'czr_fn_is_no_results') );
        add_filter( '__article_selectors'     , array( $this , 'czr_fn_article_selectors' ) );

        //social networks
        add_filter( '__get_socials'           , array( $this , 'czr_fn_get_social_networks' ), 10, 0 );

        //refresh the theme options right after the _preview_filter when previewing
        add_action( 'customize_preview_init'  , array( $this , 'czr_fn_customize_refresh_db_opt' ) );
      }

      /***************************
      * EARLY HOOKS
      ****************************/
      /**
      * Init CZR_utils class properties after_setup_theme
      * Fixes the bbpress bug : Notice: bbp_setup_current_user was called incorrectly. The current user is being initialized without using $wp->init()
      * czr_fn_get_default_options uses is_user_logged_in() => was causing the bug
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.2.3
      */
      function czr_fn_init_properties() {
        //all customizr theme options start by "tc_" by convention
        $this -> tc_options_prefixes = apply_filters('tc_options_prefixes', array('tc_') );
        $this -> is_customizing   = CZR___::$instance -> czr_fn_is_customizing();
        $this -> db_options       = false === get_option( CZR___::$tc_option_group ) ? array() : (array)get_option( CZR___::$tc_option_group );
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


      /* ------------------------------------------------------------------------- *
       *  CACHE THE LIST OF THEME SETTINGS ONLY
      /* ------------------------------------------------------------------------- */
      //Fired in __construct()
      //Note : the 'sidebar-areas' setting is not listed in that list because registered specifically
      function czr_fn_cache_theme_setting_list() {
          if ( is_array(self::$_theme_setting_list) && ! empty( self::$_theme_setting_list ) )
            return;
          $_settings_map = CZR_utils_settings_map::$instance -> czr_fn_get_customizer_map( null, 'add_setting_control' );
          $_settings = array();
          foreach ( $_settings_map as $_id => $data ) {
              $_settings[] = $_id;
          }

          self::$_theme_setting_list = $_settings;
      }


      /**
      * hook : wp_head
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_wp_filters() {
        add_filter( 'the_content'                         , array( $this , 'czr_fn_fancybox_content_filter' ) );
        /*
        * Smartload disabled for content retrieved via ajax
        */
        if ( apply_filters( 'tc_globally_enable_img_smart_load', ! $this -> czr_fn_is_ajax() && esc_attr( $this->czr_fn_opt( 'tc_img_smart_load' ) ) ) ) {
          add_filter( 'the_content'                       , array( $this , 'czr_fn_parse_imgs' ), PHP_INT_MAX );
          add_filter( 'tc_thumb_html'                     , array( $this , 'czr_fn_parse_imgs' ) );
        }
        add_filter( 'wp_title'                            , array( $this , 'czr_fn_wp_title' ), 10, 2 );
      }


      /**
      * hook : the_content
      * Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
      *
      * @return string
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_parse_imgs( $_html ) {
        $_bool = is_feed() || is_preview() || ( wp_is_mobile() && apply_filters('tc_disable_img_smart_load_mobiles', false ) );

        if ( apply_filters( 'tc_disable_img_smart_load', $_bool, current_filter() ) )
          return $_html;

        $allowed_image_extentions = apply_filters( 'tc_smartload_allowed_img_extensions', array(
          'bmp',
          'gif',
          'jpeg',
          'jpg',
          'jpe',
          'tif',
          'tiff',
          'ico',
          'png',
          'svg',
          'svgz'
        ) );

        if ( empty( $allowed_image_extentions ) || ! is_array( $allowed_image_extentions ) ) {
          return $_html;
        }

        $img_extensions_pattern = sprintf( "[%s]", implode( '|', $allowed_image_extentions ) );

        return preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+.'.$img_extensions_pattern.'[^\'"\s>]*)[\'"]?([^>]*)>#i', array( $this , 'czr_fn_regex_callback' ) , $_html);
      }


      /**
      * callback of preg_replace_callback in tc_parse_imgs
      * Inspired from Unveil Lazy Load plugin : https://wordpress.org/plugins/unveil-lazy-load/ by @marubon
      *
      * @return string
      * @package Customizr
      * @since Customizr 3.3.0
      */
      private function czr_fn_regex_callback( $matches ) {
        $_placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        if ( false !== strpos( $matches[0], 'data-src' ) || preg_match('/ data-smartload *= *"false" */', $matches[0]) ) {
          return $matches[0];
        } else {
          return apply_filters( 'tc_img_smartloaded',
            str_replace( array('srcset=', 'sizes='), array('data-srcset=', 'data-sizes='),
                sprintf('<img %1$s src="%2$s" data-src="%3$s" %4$s>',
                    $matches[1],
                    $_placeholder,
                    $matches[2],
                    $matches[3]
                )
            )
          );
        }
      }




      /**
      * Returns the current skin's primary color
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function czr_fn_get_skin_color( $_what = null ) {
        $_color_map    = CZR_init::$instance -> skin_color_map;
        $_color_map    = ( is_array($_color_map) ) ? $_color_map : array();

        $_active_skin =  str_replace('.min.', '.', basename( CZR_init::$instance -> czr_fn_get_style_src() ) );
        //falls back to grey.css array( '#5A5A5A', '#343434' ) if not defined
        $_to_return = array( '#5A5A5A', '#343434' );

        switch ($_what) {
          case 'all':
            $_to_return = $_color_map;
          break;

          case 'pair':
            $_to_return = ( false != $_active_skin && array_key_exists( $_active_skin, $_color_map ) && is_array( $_color_map[$_active_skin] ) ) ? $_color_map[$_active_skin] : $_to_return;
          break;

          default:
            $_to_return = ( false != $_active_skin && isset($_color_map[$_active_skin][0]) ) ? $_color_map[$_active_skin][0] : $_to_return[0];
          break;
        }
        return apply_filters( 'tc_get_skin_color' , $_to_return , $_what );
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
        $_is_tc_option = in_array( substr( $option_key, 0, 3 ), $this -> tc_options_prefixes );
        return apply_filters( 'tc_is_customizr_option', $_is_tc_option , $option_key );
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
          return apply_filters( 'tc_default_options', $def_options );

        //Always update/generate the default option when (OR) :
        // 1) current user can edit theme options
        // 2) they are not defined
        // 3) theme version not defined
        // 4) versions are different
        if ( current_user_can('edit_theme_options') || empty($def_options) || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
          $def_options          = $this -> czr_fn_generate_default_options( CZR_utils_settings_map::$instance -> czr_fn_get_customizer_map( $get_default_option = 'true' ) , 'tc_theme_options' );
          //Adds the version in default
          $def_options['ver']   =  CUSTOMIZR_VER;

          //writes the new value in db (merging raw options with the new defaults ).
          $this -> czr_fn_set_option( 'defaults', $def_options, 'tc_theme_options' );
        }
        return apply_filters( 'tc_default_options', $def_options );
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
          $option_group       = is_null($option_group) ? CZR___::$tc_option_group : $option_group;
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
        $option_group = is_null($option_group) ? CZR___::$tc_option_group : $option_group;
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
          if ( is_array( $_single_opt ) && ! class_exists( 'CZR_contx' ) )
            $_single_opt = $_default_val;
        }

        //allow contx filtering globally
        $_single_opt = apply_filters( "tc_opt" , $_single_opt , $option_name , $option_group, $_default_val );

        //allow single option filtering
        return apply_filters( "tc_opt_{$option_name}" , $_single_opt , $option_name , $option_group, $_default_val );
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
        $this -> db_options = false === get_option( CZR___::$tc_option_group ) ? array() : (array)get_option( CZR___::$tc_option_group );
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
        $option_group           = is_null($option_group) ? CZR___::$tc_option_group : $option_group;
        /*
        * Get raw theme options:
        * avoid filtering
        * avoid merging with defaults
        */
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
        $opts_group = is_null($opt_group) ? CZR___::$tc_option_group : $opt_group;
        $this -> db_options = false === get_option( $opt_group ) ? array() : (array)get_option( $opt_group );
        return $this -> db_options;
      }




      /**
      * Returns the "real" queried post ID or if !isset, get_the_ID()
      * Checks some contextual booleans
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_id()  {
        if ( in_the_loop() ) {
          $tc_id            = get_the_ID();
        } else {
          global $post;
          $queried_object   = get_queried_object();
          $tc_id            = ( ! empty ( $post ) && isset($post -> ID) ) ? $post -> ID : null;
          $tc_id            = ( isset ($queried_object -> ID) ) ? $queried_object -> ID : $tc_id;
        }
        $tc_id  = ( is_404() || is_search() || is_archive() ) ? null : $tc_id;
        return apply_filters( 'tc_id', $tc_id );
      }




      /**
      * This function returns the layout (sidebar(s), or full width) to apply to a context
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
          $__options                    = czr_fn__f ( '__options' );

          //Article wrapper class definition
          $global_layout                = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );

          /* DEFAULT LAYOUTS */
          //what is the default layout we want to apply? By default we apply the global default layout
          $tc_sidebar_default_layout    = esc_attr( $__options['tc_sidebar_global_layout'] );

          //checks if the 'force default layout' option is checked and return the default layout before any specific layout
          if( isset($__options['tc_sidebar_force_layout']) && 1 == $__options['tc_sidebar_force_layout'] ) {
            $class_tab  = $global_layout[$tc_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $tc_screen_layout = array(
              'sidebar' => $tc_sidebar_default_layout,
              'class'   => $class_tab
            );
            return $tc_screen_layout[$sidebar_or_class];
          }

          global $wp_query, $post;
          $tc_specific_post_layout    = false;
          $is_singular_layout         = false;

          if ( apply_filters( 'tc_is_post_layout', is_single( $post_id ), $post_id ) ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_post_layout'] );
            $is_singular_layout = true;
          }
          elseif ( apply_filters( 'tc_is_page_layout', is_page( $post_id ), $post_id ) ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_page_layout'] );
            $is_singular_layout = true;
          }


          //builds the default layout option array including layout and article class
          $class_tab  = $global_layout[$tc_sidebar_default_layout];
          $class_tab  = $class_tab['content'];
          $tc_screen_layout             = array(
                      'sidebar' => $tc_sidebar_default_layout,
                      'class'   => $class_tab
          );

          //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
          $tc_specific_post_layout    = false;

          //if we are displaying an attachement, we use the parent post/page layout
          if ( isset($post) && is_singular() && 'attachment' == $post->post_type ) {
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
          }
          //for a singular post or page OR for the posts page
          elseif ( $is_singular_layout || is_singular() || $wp_query -> is_posts_page )
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );


          //checks if we display home page, either posts or static page and apply the customizer option
          if( ( is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page() ) {
             $tc_specific_post_layout = $__options['tc_front_layout'];
          }

          if( $tc_specific_post_layout ) {
              $class_tab  = $global_layout[$tc_specific_post_layout];
              $class_tab  = $class_tab['content'];
              $tc_screen_layout = array(
              'sidebar' => $tc_specific_post_layout,
              'class'   => $class_tab
            );
          }

          return apply_filters( 'tc_screen_layout' , $tc_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }







      /**
       * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
       *
       * @package Customizr
       * @since Customizr 2.0.7
       */
      function czr_fn_fancybox_content_filter( $content) {
        $tc_fancybox = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fancybox' ) );

        if ( 1 != $tc_fancybox )
          return $content;

        global $post;
        if ( ! isset($post) )
          return $content;

        $pattern ="/<a(.*?)href=( '|\")(.*?).(bmp|gif|jpeg|jpg|png)( '|\")(.*?)>/i";
        $replacement = '<a$1href=$2$3.$4$5 class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'"$6>';
        $r_content = preg_replace( $pattern, $replacement, $content);
        $content = $r_content ? $r_content : $content;
        return apply_filters( 'tc_fancybox_content_filter', $content );
      }




      /**
      * Title element formating
      *
      * @since Customizr 2.1.6
      *
      */
      function czr_fn_wp_title( $title, $sep ) {
        if ( function_exists( '_wp_render_title_tag' ) )
          return $title;

        global $paged, $page;

        if ( is_feed() )
          return $title;

        // Add the site name.
        $title .= get_bloginfo( 'name' );

        // Add the site description for the home/front page.
        $site_description = get_bloginfo( 'description' , 'display' );
        if ( $site_description && czr_fn__f('__is_home') )
          $title = "$title $sep $site_description";

        // Add a page number if necessary.
        if ( $paged >= 2 || $page >= 2 )
          $title = "$title $sep " . sprintf( __( 'Page %s' , 'customizr' ), max( $paged, $page ) );

        return $title;
      }




      /**
      * Check if we are displaying posts lists or front page
      *
      * @since Customizr 3.0.6
      *
      */
      function czr_fn_is_home() {
        //get info whether the front page is a list of last posts or a page
        return ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page();
      }





      /**
      * Check if we show posts or page content on home page
      *
      * @since Customizr 3.0.6
      *
      */
      function czr_fn_is_home_empty() {
        //check if the users has choosen the "no posts or page" option for home page
        return ( ( is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
      }




      /**
      * Return object post type
      *
      * @since Customizr 3.0.10
      *
      */
      function czr_fn_get_post_type() {
        global $post;

        if ( ! isset($post) )
          return;

        return $post -> post_type;
      }






      /**
      * Returns the classes for the post div.
      *
      * @param string|array $class One or more classes to add to the class list.
      * @param int $post_id An optional post ID.
      * @package Customizr
      * @since 3.0.10
      */
      function czr_fn_get_post_class( $class = '', $post_id = null ) {
        //Separates classes with a single space, collates classes for post DIV
        return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
      }






      /**
      * Boolean : check if we are in the no search results case
      *
      * @package Customizr
      * @since 3.0.10
      */
      function czr_fn_is_no_results() {
        global $wp_query;
        return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
      }





      /**
      * Displays the selectors of the article depending on the context
      *
      * @package Customizr
      * @since 3.1.0
      */
      function czr_fn_article_selectors() {

        //gets global vars
        global $post;
        global $wp_query;

        //declares selector var
        $selectors                  = '';

        // SINGLE POST
        $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
        $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // POST LIST
        $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !czr_fn__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
        $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // PAGE
        $page_selector_bool         = isset($post) && 'page' == czr_fn__f('__post_type') && is_singular() && !czr_fn__f( '__is_home_empty');
        $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // ATTACHMENT
        //checks if attachement is image and add a selector
        $format_image               = wp_attachment_is_image() ? 'format-image' : '';
        $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class(array('row-fluid', $format_image) ) ) : $selectors;

        // NO SEARCH RESULTS
        $selectors                  = ( is_search() && 0 == $wp_query -> post_count ) ? apply_filters( 'tc_no_results_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        // 404
        $selectors                  = is_404() ? apply_filters( 'tc_404_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        echo apply_filters( 'tc_article_selectors', $selectors );

      }//end of function





      /**
      * Gets the social networks list defined in customizer options
      *
      *
      *
      * @package Customizr
      * @since Customizr 3.0.10
      *
      * @since Customizr 3.4.55 Added the ability to retrieve them as array
      * @param $output_type optional. Return type "string" or "array"
      */
      //MODEL LOOKS LIKE THIS
      //(
      //     [0] => Array
      //         (
      //             [is_mod_opt] => 1
      //             [module_id] => tc_social_links_czr_module
      //             [social-size] => 15
      //         )

      //     [1] => Array
      //         (
      //             [id] => czr_social_module_0
      //             [title] => Follow us on Renren
      //             [social-icon] => fa-renren
      //             [social-link] => http://customizr-dev.dev/feed/rss/
      //             [social-color] => #6d4c8e
      //             [social-target] => 1
      //         )
      // )
      function czr_fn_get_social_networks( $output_type = 'string' ) {

          $_socials         = $this -> czr_fn_opt('tc_social_links');
          $_default_color   = array('rgb(90,90,90)', '#5a5a5a'); //both notations
          $_default_size    = '14'; //px

          $_social_opts     = array( 'social-size' => $_default_size );

          if ( empty( $_socials ) )
            return;

          //get the social mod opts
          foreach( $_socials as $key => $item ) {
            if ( ! array_key_exists( 'is_mod_opt', $item ) )
              continue;
            $_social_opts = wp_parse_args( $item, $_social_opts );
          }

          //if the size is the default one, do not add the inline style css
          $social_size_css  = empty( $_social_opts['social-size'] ) || $_default_size == $_social_opts['social-size'] ? '' : "font-size:{$_social_opts['social-size']}px";

          $_social_links = array();
          foreach( $_socials as $key => $item ) {
              //skip if mod_opt
              if ( array_key_exists( 'is_mod_opt', $item ) )
                continue;

              //get the social icon suffix for backward compatibility (users custom CSS) we still add the class icon-*
              $icon_class            = isset($item['social-icon']) ? esc_attr($item['social-icon']) : '';
              $link_icon_class       = 'fa-' === substr( $icon_class, 0, 3 ) && 3 < strlen( $icon_class ) ?
                      ' icon-' . str_replace( array('rss', 'envelope'), array('feed', 'mail'), substr( $icon_class, 3, strlen($icon_class) ) ) :
                      '';

              /* Maybe build inline style */
              $social_color_css      = isset($item['social-color']) ? esc_attr($item['social-color']) : $_default_color[0];
              //if the color is the default one, do not print the inline style css
              $social_color_css      = in_array( $social_color_css, $_default_color ) ? '' : "color:{$social_color_css}";
              $style_props           = implode( ';', array_filter( array( $social_color_css, $social_size_css ) ) );

              $style_attr            = $style_props ? sprintf(' style="%1$s"', $style_props ) : '';

              array_push( $_social_links, sprintf('<a rel="nofollow" class="social-icon%6$s" %1$s title="%2$s" href="%3$s"%4$s%7$s><i class="fa %5$s"></i></a>',
                //do we have an id set ?
                //Typically not if the user still uses the old options value.
                //So, if the id is not present, let's build it base on the key, like when added to the collection in the customizer

                // Put them together
                  ! CZR___::$instance -> czr_fn_is_customizing() ? '' : sprintf( 'data-model-id="%1$s"', ! isset( $item['id'] ) ? 'czr_socials_'. $key : $item['id'] ),
                  isset($item['title']) ? esc_attr( $item['title'] ) : '',
                  ( isset($item['social-link']) && ! empty( $item['social-link'] ) ) ? esc_url( $item['social-link'] ) : 'javascript:void(0)',
                  ( isset($item['social-target']) && false != $item['social-target'] ) ? ' target="_blank"' : '',
                  $icon_class,
                  $link_icon_class,
                  $style_attr
              ) );
          }

          /*
          * return
          */
          switch ( $output_type ) :
            case 'array' : return $_social_links;
            default      : return implode( '', $_social_links );
          endswitch;
      }


    /**
    * Retrieve the file type from the file name
    * Even when it's not at the end of the file
    * copy of wp_check_filetype() in wp-includes/functions.php
    *
    * @since 3.2.3
    *
    * @param string $filename File name or path.
    * @param array  $mimes    Optional. Key is the file extension with value as the mime type.
    * @return array Values with extension first and mime type.
    */
    function czr_fn_check_filetype( $filename, $mimes = null ) {
      $filename = basename( $filename );
      if ( empty($mimes) )
        $mimes = get_allowed_mime_types();
      $type = false;
      $ext = false;
      foreach ( $mimes as $ext_preg => $mime_match ) {
        $ext_preg = '!\.(' . $ext_preg . ')!i';
        //was ext_preg = '!\.(' . $ext_preg . ')$!i';
        if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
          $type = $mime_match;
          $ext = $ext_matches[1];
          break;
        }
      }

      return compact( 'ext', 'type' );
    }

    /**
    * Check whether a category exists.
    * (wp category_exists isn't available in pre_get_posts)
    * @since 3.4.10
    *
    * @see term_exists()
    *
    * @param int $cat_id.
    * @return bool
    */
    public function czr_fn_category_id_exists( $cat_id ) {
      return term_exists( (int) $cat_id, 'category');
    }



    /**
    * @return a date diff object
    * @uses  date_diff if php version >=5.3.0, instantiates a fallback class if not
    *
    * @since 3.2.8
    *
    * @param date one object.
    * @param date two object.
    */
    private function czr_fn_date_diff( $_date_one , $_date_two ) {
      //if version is at least 5.3.0, use date_diff function
      if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0) {
        return date_diff( $_date_one , $_date_two );
      } else {
        $_date_one_timestamp   = $_date_one->format("U");
        $_date_two_timestamp   = $_date_two->format("U");
        return new CZR_DateInterval( $_date_two_timestamp - $_date_one_timestamp );
      }
    }



    /**
    * Return boolean OR number of days since last update OR PHP version < 5.2
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_post_has_update( $_bool = false) {
      //php version check for DateTime
      //http://php.net/manual/fr/class.datetime.php
      if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
        return false;

      //first proceed to a date check
      $dates_to_check = array(
        'created'   => get_the_date('Y-m-d g:i:s'),
        'updated'   => get_the_modified_date('Y-m-d g:i:s'),
        'current'   => date('Y-m-d g:i:s')
      );
      //ALL dates must be valid
      if ( 1 != array_product( array_map( array($this , 'czr_fn_is_date_valid') , $dates_to_check ) ) )
        return false;

      //Import variables into the current symbol table
      extract($dates_to_check);

      //Instantiate the different date objects
      $created                = new DateTime( $created );
      $updated                = new DateTime( $updated );
      $current                = new DateTime( $current );

      $created_to_updated     = $this -> czr_fn_date_diff( $created , $updated );
      $updated_to_today       = $this -> czr_fn_date_diff( $updated, $current );

      if ( true === $_bool )
        //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : true;
        return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? true : false;
      else
        //return ( 0 == $created_to_updated -> days && 0 == $created_to_updated -> s ) ? false : $updated_to_today -> days;
        return ( $created_to_updated -> s > 0 || $created_to_updated -> i > 0 ) ? $updated_to_today -> days : false;
    }



    /*
    * @return boolean
    * http://stackoverflow.com/questions/11343403/php-exception-handling-on-datetime-object
    */
    private function czr_fn_is_date_valid($str) {
      if ( ! is_string($str) )
         return false;

      $stamp = strtotime($str);
      if ( ! is_numeric($stamp) )
         return false;

      if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) )
         return true;

      return false;
    }



    /**
    * @return an array of font name / code OR a string of the font css code
    * @parameter string name or google compliant suffix for href link
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_get_font( $_what = 'list' , $_requested = null ) {
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


    /**
    * Boolean helper to check if the secondary menu is enabled
    * since v3.4+
    */
    function czr_fn_is_secondary_menu_enabled() {
      return (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_style' ) );
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
        'tc_get_ctx_excluded_options',
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
    * Boolean helper : tells if this option is excluded from the ctx treatments.
    * @return bool
    */
    function czr_fn_is_option_excluded_from_ctx( $opt_name ) {
      return in_array( $opt_name, $this -> czr_fn_get_ctx_excluded_options() );
    }


    /**
    * Returns the url of the customizer with the current url arguments + an optional customizer section args
    *
    * @param $autofocus(optional) is an array indicating the elements to focus on ( control,section,panel).
    * Ex : array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec').
    * Wordpress will cycle among autofocus keys focusing the existing element - See wp-admin/customize.php.
    * The actual focused element depends on its type according to this priority scale: control, section, panel.
    * In this sense when specifying a control, additional section and panel could be considered as fall-back.
    *
    * @param $control_wrapper(optional) is a string indicating the wrapper to apply to the passed control. By default is "tc_theme_options".
    * Ex: passing $aufocus = array('control' => 'tc_front_slider') will produce the query arg 'autofocus'=>array('control' => 'tc_theme_options[tc_front_slider]'
    *
    * @return url string
    * @since Customizr 3.4+
    */
    static function czr_fn_get_customizer_url( $autofocus = null, $control_wrapper = 'tc_theme_options' ) {
      $_current_url       = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $_customize_url     = add_query_arg( 'url', urlencode( $_current_url ), wp_customize_url() );
      $autofocus  = ( ! is_array($autofocus) || empty($autofocus) ) ? null : $autofocus;

      if ( is_null($autofocus) )
        return $_customize_url;

      $_ordered_keys = array( 'control', 'section', 'panel');

      // $autofocus must contain at least one key among (control,section,panel)
      if ( ! count( array_intersect( array_keys($autofocus), $_ordered_keys ) ) )
        return $_customize_url;

      // $autofocus must contain at least one key among (control,section,panel)
      if ( ! count( array_intersect( array_keys($autofocus), array( 'control', 'section', 'panel') ) ) )
        return $_customize_url;

      // wrap the control in the $control_wrapper if neded
      if ( array_key_exists( 'control', $autofocus ) && ! empty( $autofocus['control'] ) && $control_wrapper ){
        $autofocus['control'] = $control_wrapper . '[' . $autofocus['control'] . ']';
      }
      //Since wp 4.6.1 we order the params following the $_ordered_keys order
      $autofocus = array_merge( array_flip( $_ordered_keys ), $autofocus );

      if ( ! empty( $autofocus ) ) {
        //here we pass the first element of the array
        // We don't really have to care for not existent autofocus keys, wordpress will stash them when passing the values to the customize js
        return add_query_arg( array( 'autofocus' => array_slice( $autofocus, 0, 1 ) ), $_customize_url );
      } else
        return $_customize_url;
    }


    /**
    * Is there a menu assigned to a given location ?
    * Used in class-header-menu and class-fire-placeholders
    * @return bool
    * @since  v3.4+
    */
    function czr_fn_has_location_menu( $_location ) {
      $_all_locations  = get_nav_menu_locations();
      return isset($_all_locations[$_location]) && is_object( wp_get_nav_menu_object( $_all_locations[$_location] ) );
    }

    /**
    * Whether or not we are in the ajax context
    * @return bool
    * @since v3.4.37
    */
    function czr_fn_is_ajax() {

      /*
      * wp_doing_ajax() introduced in 4.7.0
      */
      $wp_doing_ajax = ( function_exists('wp_doing_ajax') && wp_doing_ajax() ) || ( ( defined('DOING_AJAX') && 'DOING_AJAX' ) );

      /*
      * https://core.trac.wordpress.org/ticket/25669#comment:19
      * http://stackoverflow.com/questions/18260537/how-to-check-if-the-request-is-an-ajax-request-with-php
      */
      $_is_ajax      = $wp_doing_ajax || ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

      return apply_filters( 'czr_is_ajax', $_is_ajax );
    }

  }//end of class
endif;


//Helper class to build a simple date diff object
//Alternative to date_diff for php version < 5.3.0
//http://stackoverflow.com/questions/9373718/php-5-3-date-diff-equivalent-for-php-5-2-on-own-function
if ( ! class_exists( 'CZR_DateInterval' ) ) :
Class CZR_DateInterval {
    /* Properties */
    public $y = 0;
    public $m = 0;
    public $d = 0;
    public $h = 0;
    public $i = 0;
    public $s = 0;

    /* Methods */
    public function __construct ( $time_to_convert ) {
      $FULL_YEAR = 60*60*24*365.25;
      $FULL_MONTH = 60*60*24*(365.25/12);
      $FULL_DAY = 60*60*24;
      $FULL_HOUR = 60*60;
      $FULL_MINUTE = 60;
      $FULL_SECOND = 1;

      //$time_to_convert = 176559;
      $seconds = 0;
      $minutes = 0;
      $hours = 0;
      $days = 0;
      $months = 0;
      $years = 0;

      while($time_to_convert >= $FULL_YEAR) {
          $years ++;
          $time_to_convert = $time_to_convert - $FULL_YEAR;
      }

      while($time_to_convert >= $FULL_MONTH) {
          $months ++;
          $time_to_convert = $time_to_convert - $FULL_MONTH;
      }

      while($time_to_convert >= $FULL_DAY) {
          $days ++;
          $time_to_convert = $time_to_convert - $FULL_DAY;
      }

      while($time_to_convert >= $FULL_HOUR) {
          $hours++;
          $time_to_convert = $time_to_convert - $FULL_HOUR;
      }

      while($time_to_convert >= $FULL_MINUTE) {
          $minutes++;
          $time_to_convert = $time_to_convert - $FULL_MINUTE;
      }

      $seconds = $time_to_convert; // remaining seconds
      $this->y = $years;
      $this->m = $months;
      $this->d = $days;
      $this->h = $hours;
      $this->i = $minutes;
      $this->s = $seconds;
      $this->days = ( 0 == $years ) ? $days : ( $years * 365 + $months * 30 + $days );
    }
}
endif;

?><?php
/**
* Loads front end stylesheets and scripts
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
if ( ! class_exists( 'CZR_resources' ) ) :
	class CZR_resources {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;
      public $tc_script_map;
      public $current_random_skin;

	    function __construct () {
	        self::$instance =& $this;
          add_action( 'wp_enqueue_scripts'            , array( $this , 'czr_fn_enqueue_gfonts' ) , 0 );
	        add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_styles' ) );
          add_action( 'wp_enqueue_scripts'						, array( $this , 'czr_fn_enqueue_front_scripts' ) );

	        //Custom CSS
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_custom_css') , apply_filters( 'tc_custom_css_priority', 9999 ) );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_fonts_inline_css') );
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_write_dropcap_inline_css') );

          /* See: https://github.com/presscustomizr/customizr/issues/605 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_apply_media_upload_front_patch' ) );
          /* See: https://github.com/presscustomizr/customizr/issues/787 */
          add_filter('tc_user_options_style'          , array( $this , 'czr_fn_maybe_avoid_double_social_icon' ) );

          //set random skin
          add_filter ('tc_opt_tc_skin'                , array( $this, 'czr_fn_set_random_skin' ) );

          //stores the front scripts map in a property
          $this -> tc_script_map = $this -> czr_fn_get_script_map();
	    }



	   /**
		* Registers and enqueues Customizr stylesheets
		* @package Customizr
		* @since Customizr 1.1
		*/
    function czr_fn_enqueue_front_styles() {
          //Enqueue FontAwesome CSS
          if ( true == CZR_utils::$inst -> czr_fn_opt( 'tc_font_awesome_icons' ) ) {
            $_path = apply_filters( 'tc_font_icons_path' , TC_BASE_URL . 'assets/shared/fonts/fa/css/' );
            wp_enqueue_style( 'customizr-fa',
                $_path . CZR_init::$instance -> czr_fn_maybe_use_min_style( 'font-awesome.css' ),
                array() , CUSTOMIZR_VER, 'all' );
          }

	      wp_enqueue_style( 'customizr-common', CZR_init::$instance -> czr_fn_get_style_src( 'common') , array() , CUSTOMIZR_VER, 'all' );
          //Customizr active skin
	      wp_register_style( 'customizr-skin', CZR_init::$instance -> czr_fn_get_style_src( 'skin'), array('customizr-common'), CUSTOMIZR_VER, 'all' );
	      wp_enqueue_style( 'customizr-skin' );
	      //Customizr stylesheet (style.css)
	      wp_enqueue_style( 'customizr-style', get_stylesheet_uri(), array( 'customizr-skin' ), CUSTOMIZR_VER , 'all' );

	      //Customizer user defined style options : the custom CSS is written with a high priority here
	      wp_add_inline_style( 'customizr-skin', apply_filters( 'tc_user_options_style' , '' ) );
		}



    /**
    * Helper to get all front end script
    * Fired from the constructor
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_get_script_map( $_handles = array() ) {
      $_map = array(
        'tc-js-params' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'tc-js-params.js' ),
          'dependencies' => array( 'jquery' )
        ),
        //adds support for map method in array prototype for old ie browsers <ie9
        'tc-js-arraymap-proto' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'oldBrowserCompat.min.js' ),
          'dependencies' => array()
        ),
        'tc-bootstrap' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'bootstrap.js' , 'bootstrap.min.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery', 'tc-js-params' )
        ),
        'tc-img-original-sizes' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryimgOriginalSizes.js' ),
          'dependencies' => array('jquery')
        ),
        'tc-smoothscroll' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'smoothScroll.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'underscore' )
        ),
        'tc-outline' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'outline.js' ),
          'dependencies' => array()
        ),
        'tc-waypoints' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'waypoints.js' ),
          'dependencies' => array('jquery')
        ),
        'tc-dropcap' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryaddDropCap.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
        ),
        'tc-img-smartload' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryimgSmartLoad.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
        ),
        'tc-ext-links' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryextLinks.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
        ),
        'tc-parallax' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryParallax.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap', 'underscore' )
        ),
        'tc-center-images' => array(
          'path' => 'assets/front/js/jquery-plugins/',
          'files' => array( 'jqueryCenterImages.js' ),
          'dependencies' => array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'underscore' )
        ),
        //!!no fancybox dependency if fancybox not required!
        'tc-main-front' => array(
          'path' => 'inc/assets/js/parts/',
          'files' => array( 'main.js' , 'main.min.js' ),
          'dependencies' => $this -> czr_fn_is_fancyboxjs_required() ? array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-fancybox' , 'underscore' ) : array( 'jquery' , 'tc-js-params', 'tc-img-original-sizes', 'tc-bootstrap' , 'underscore' )
        ),
        //loaded separately => not included in tc-script.js
        'tc-fancybox' => array(
          'path' => 'inc/assets/js/fancybox/',
          'files' => array( 'jquery.fancybox-1.3.4.min.js' ),
          'dependencies' => $this -> czr_fn_load_concatenated_front_scripts() ? array( 'jquery' ) : array( 'tc-js-arraymap-proto', 'jquery' , 'tc-js-params', 'tc-bootstrap' )
        ),
        //concats all scripts except fancybox
        'tc-scripts' => array(
          'path' => 'inc/assets/js/',
          'files' => array( 'tc-scripts.js' , 'tc-scripts.min.js' ),
          'dependencies' =>  $this -> czr_fn_is_fancyboxjs_required() ? array( 'jquery', 'tc-fancybox' ) : array( 'jquery' )
        )
      );//end of scripts map

      return apply_filters('tc_get_script_map' , $_map, $_handles );
    }



		/**
		* Loads Customizr front scripts
    * Dependencies are defined in the script map property
    *
		* @return  void()
		* @uses wp_enqueue_script() to manage script dependencies
		* @package Customizr
		* @since Customizr 1.0
		*/
		function czr_fn_enqueue_front_scripts() {
	    //wp scripts
	  	if ( is_singular() && get_option( 'thread_comments' ) )
		    wp_enqueue_script( 'comment-reply' );

	    wp_enqueue_script( 'jquery' );
	    wp_enqueue_script( 'jquery-ui-core' );

	    wp_enqueue_script(
        'modernizr'
        ,
        TC_BASE_URL . 'inc/assets/js/modernizr.min.js',
        array(),
        CUSTOMIZR_VER,
        //load in head if browser is chrome => fix the issue of 3Dtransform not detected in some cases
        ( isset($_SERVER['HTTP_USER_AGENT']) && false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) ? false : true
      );

      //customizr scripts and libs
	   	if ( $this -> czr_fn_load_concatenated_front_scripts() )	{
        if ( $this -> czr_fn_is_fancyboxjs_required() )
          $this -> czr_fn_enqueue_script( 'tc-fancybox' );
        //!!tc-scripts includes underscore, tc-js-arraymap-proto
        $this -> czr_fn_enqueue_script( 'tc-scripts' );
			}
			else {
        wp_enqueue_script( 'underscore' );
        //!!mind the dependencies
        $this -> czr_fn_enqueue_script( array( 'tc-js-params', 'tc-js-arraymap-proto', 'tc-img-original-sizes', 'tc-bootstrap', 'tc-smoothscroll', 'tc-outline', 'tc-waypoints' ) );

        if ( $this -> czr_fn_is_fancyboxjs_required() )
          $this -> czr_fn_enqueue_script( 'tc-fancybox' );

        $this -> czr_fn_enqueue_script( array( 'tc-dropcap' , 'tc-img-smartload', 'tc-ext-links', 'tc-center-images', 'tc-parallax', 'tc-main-front' ) );
			}//end of load concatenate script if

      //carousel options
      //gets slider options if any for home/front page or for others posts/pages
      $js_slidername      = czr_fn__f('__is_home') ? CZR_utils::$inst->czr_fn_opt( 'tc_front_slider' ) : get_post_meta( CZR_utils::czr_fn_id() , $key = 'post_slider_key' , $single = true );
      $js_sliderdelay     = czr_fn__f('__is_home') ? CZR_utils::$inst->czr_fn_opt( 'tc_slider_delay' ) : get_post_meta( CZR_utils::czr_fn_id() , $key = 'slider_delay_key' , $single = true );

			//has the post comments ? adds a boolean parameter in js
			global $wp_query;
			$has_post_comments 	= ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

			//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
			$anchor_smooth_scroll 		  = ( false != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_link_scroll') ) ) ? 'easeOutExpo' : 'linear';
			if ( false != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_link_scroll') ) )
				wp_enqueue_script('jquery-effects-core');
            $anchor_smooth_scroll_exclude =  apply_filters( 'tc_anchor_smoothscroll_excl' , array(
                'simple' => array( '[class*=edd]' , '.tc-carousel-control', '.carousel-control', '[data-toggle="modal"]', '[data-toggle="dropdown"]', '[data-toggle="tooltip"]', '[data-toggle="popover"]', '[data-toggle="collapse"]', '[data-toggle="tab"]', '[class*=upme]', '[class*=um-]' ),
                'deep'   => array(
                  'classes' => array(),
                  'ids'     => array()
                )
            ));

      $smooth_scroll_enabled = apply_filters('tc_enable_smoothscroll', ! wp_is_mobile() && 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_smoothscroll') ) );
      $smooth_scroll_options = apply_filters('tc_smoothscroll_options', array( 'touchpadSupport' => false ) );

      //smart load
      $smart_load_enabled   = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_img_smart_load' ) );
      $smart_load_opts      = apply_filters( 'tc_img_smart_load_options' , array(
            'parentSelectors' => array(
                '.article-container', '.__before_main_wrapper', '.widget-front',
            ),
            'opts'     => array(
                'excludeImg' => array( '.tc-holder-img' )
            )
      ));
			//gets current screen layout
    	$screen_layout      = CZR_utils::czr_fn_get_layout( CZR_utils::czr_fn_id() , 'sidebar'  );
    	//gets the global layout settings
    	$global_layout      = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
    	$sidebar_layout     = isset($global_layout[$screen_layout]['sidebar']) ? $global_layout[$screen_layout]['sidebar'] : false;
			//Gets the left and right sidebars class for js actions
			$left_sb_class     	= sprintf( '.%1$s.left.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );
	    $right_sb_class     = sprintf( '.%1$s.right.tc-sidebar', (false != $sidebar_layout) ? $sidebar_layout : 'span3' );

			wp_localize_script(
	        $this -> czr_fn_load_concatenated_front_scripts() ? 'tc-scripts' : 'tc-js-params',
	        'TCParams',
	        apply_filters( 'tc_customizr_script_params' , array(
	          	'_disabled'          => apply_filters( 'tc_disabled_front_js_parts', array() ),
              'FancyBoxState' 		=> $this -> czr_fn_is_fancyboxjs_required(),
	          	'FancyBoxAutoscale' => ( 1 == CZR_utils::$inst->czr_fn_opt( 'tc_fancybox_autoscale') ) ? true : false,
	          	'SliderName' 			  => $js_slidername,
	          	'SliderDelay' 			=> $js_sliderdelay,
	          	'SliderHover'			  => apply_filters( 'tc_stop_slider_hover', true ),
	          	'centerSliderImg'   => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_slider_img') ),
              'SmoothScroll'      => array( 'Enabled' => $smooth_scroll_enabled, 'Options' => $smooth_scroll_options ),
              'anchorSmoothScroll'			=> $anchor_smooth_scroll,
              'anchorSmoothScrollExclude' => $anchor_smooth_scroll_exclude,
	          	'ReorderBlocks' 		=> esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_block_reorder') ),
	          	'centerAllImg' 			=> esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_img') ),
	          	'HasComments' 			=> $has_post_comments,
	          	'LeftSidebarClass' 		=> $left_sb_class,
	          	'RightSidebarClass' 	=> $right_sb_class,
	          	'LoadModernizr' 		=> apply_filters( 'tc_load_modernizr' , true ),
	          	'stickyCustomOffset' 	=> apply_filters( 'tc_sticky_custom_offset' , array( "_initial" => 0, "_scrolling" => 0, "options" => array( "_static" => true, "_element" => "" ) ) ),
	          	'stickyHeader' 			=> esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_header' ) ),
	          	'dropdowntoViewport' 	=> esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_resp_dropdown_limit_to_viewport') ),
	          	'timerOnScrollAllBrowsers' => apply_filters( 'tc_timer_on_scroll_for_all_browser' , true), //<= if false, for ie only
              'extLinksStyle'       => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_ext_link_style' ) ),
              'extLinksTargetExt'   => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_ext_link_target' ) ),
              'extLinksSkipSelectors'   => apply_filters( 'tc_ext_links_skip_selectors' , array( 'classes' => array('btn', 'button') , 'ids' => array() ) ),
              'dropcapEnabled'      => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_enable_dropcap' ) ),
              'dropcapWhere'      => array( 'post' => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_dropcap' ) ) , 'page' => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_page_dropcap' ) ) ),
              'dropcapMinWords'     => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_dropcap_minwords' ) ),
              'dropcapSkipSelectors'  => apply_filters( 'tc_dropcap_skip_selectors' , array( 'tags' => array('IMG' , 'IFRAME', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'UL', 'OL'), 'classes' => array('btn') , 'id' => array() ) ),
              'imgSmartLoadEnabled' => $smart_load_enabled,
              'imgSmartLoadOpts'    => $smart_load_opts,
              'goldenRatio'         => apply_filters( 'tc_grid_golden_ratio' , 1.618 ),
              'gridGoldenRatioLimit' => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_thumb_height' ) ),
              'isSecondMenuEnabled'  => CZR_utils::$inst->czr_fn_is_secondary_menu_enabled(),
              'secondMenuRespSet'   => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_second_menu_resp_setting' ) )
	        	),
	        	CZR_utils::czr_fn_id()
		    )//end of filter
	     );

	    //fancybox style
	    if ( $this -> czr_fn_is_fancyboxjs_required() )
	      wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/assets/js/fancybox/jquery.fancybox-1.3.4.min.css' );

	    //holder.js is loaded when featured pages are enabled AND FP are set to show images and at least one holder should be displayed.
      $tc_show_featured_pages 	         = class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages();
    	if ( 0 != $tc_show_featured_pages && $this -> czr_fn_maybe_is_holder_js_required() ) {
	    	wp_enqueue_script(
	    		'holder',
	    		sprintf( '%1$sinc/assets/js/holder.min.js' , TC_BASE_URL ),
	    		array(),
	    		CUSTOMIZR_VER,
	    		$in_footer = true
	    	);
	    }

	    //load retina.js in footer if enabled
	    if ( apply_filters('tc_load_retinajs', 1 == CZR_utils::$inst->czr_fn_opt( 'tc_retina_support' ) ) )
	    	wp_enqueue_script( 'retinajs' ,TC_BASE_URL . 'inc/assets/js/retina.min.js', array(), CUSTOMIZR_VER, $in_footer = true);

	    //Load hammer.js for mobile
	    if ( apply_filters('tc_load_hammerjs', wp_is_mobile() ) )
	    	wp_enqueue_script( 'hammer' ,TC_BASE_URL . 'inc/assets/js/hammer.min.js', array('jquery'), CUSTOMIZR_VER );

		}



    /**
    * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
    * hook : tc_user_options_style
    * @package Customizr
    * @since Customizr 2.0.7
    */
    function czr_fn_write_custom_css( $_css = null ) {
      $_css               = isset($_css) ? $_css : '';
      $tc_custom_css      = esc_html( CZR_utils::$inst->czr_fn_opt( 'tc_custom_css') );
      if ( ! isset($tc_custom_css) || empty($tc_custom_css) )
        return $_css;

      return apply_filters( 'tc_write_custom_css',
        $_css . "\n" . html_entity_decode( $tc_custom_css ),
        $_css,
        CZR_utils::$inst->czr_fn_opt( 'tc_custom_css')
      );
    }//end of function


    /* See: https://github.com/presscustomizr/customizr/issues/605 */
    function czr_fn_apply_media_upload_front_patch( $_css ) {
      global $wp_version;
      if ( version_compare( '4.5', $wp_version, '<=' ) )
        $_css = sprintf("%s%s",
  		            	$_css,
                        'table { border-collapse: separate; }
                         body table { border-collapse: collapse; }
                        ');
      return $_css;
    }

    /*
    * Use the dynamic style to fix server side caching issue,
    * which is the main reason why we needed this patch
    * We don't subordinate this to the user_started_before a certain version
    * as it also fixes potential plugin compatibility (plugins which style .icon-* before)
    * https://github.com/presscustomizr/customizr/issues/787
    * ( all this will be removed in c4 )
    */
    function czr_fn_maybe_avoid_double_social_icon( $_css ) {
      return sprintf( "%s\n%s", $_css, '.social-links .social-icon:before { content: none } ');
    }

    /*
    * Callback of wp_enqueue_scripts
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_enqueue_gfonts() {
      $_font_pair         = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fonts' ) );
      $_all_font_pairs    = CZR_init::$instance -> font_pairs;
      if ( ! $this -> czr_fn_is_gfont( $_font_pair , '_g_') )
        return;

      wp_enqueue_style(
        'tc-gfonts',
        sprintf( '//fonts.googleapis.com/css?family=%s', str_replace( '|', '%7C', CZR_utils::$inst -> czr_fn_get_font( 'single' , $_font_pair ) ) ),
        array(),
        null,
        'all'
      );
    }



    /**
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_write_fonts_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      $_font_pair         = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fonts' ) );
      $_body_font_size    = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_body_font_size' ) );
      $_font_selectors    = CZR_init::$instance -> font_selectors;

      //create the $body and $titles vars
      extract( CZR_init::$instance -> font_selectors, EXTR_OVERWRITE );

      if ( ! isset($body) || ! isset($titles) )
        return;

      //adapt the selectors in edit context => add specificity for the mce-editor
      if ( ! is_null( $_context ) ) {
        $titles = ".{$_context} h1, .{$_context} h2, .{$_context} h3";
        $body   = "body.{$_context}";
      }

      $titles = apply_filters('tc_title_fonts_selectors' , $titles );
      $body   = apply_filters('tc_body_fonts_selectors' , $body );

      if ( 'helvetica_arial' != $_font_pair ) {//check if not default
        $_selector_fonts  = explode( '|', CZR_utils::$inst -> czr_fn_get_font( 'single' , $_font_pair ) );
        if ( ! is_array($_selector_fonts) )
          return $_css;

        foreach ($_selector_fonts as $_key => $_raw_font) {
          //create the $_family and $_weight vars
          extract( $this -> czr_fn_get_font_css_prop( $_raw_font , $this -> czr_fn_is_gfont( $_font_pair ) ) );

          switch ($_key) {
            case 0 : //titles font
              $_css .= "
                {$titles} {
                  font-family : {$_family};
                  font-weight : {$_weight};
                }\n";
            break;

            case 1 ://body font
              $_css .= "
                {$body} {
                  font-family : {$_family};
                  font-weight : {$_weight};
                }\n";
            break;
          }
        }
      }//end if

      if ( 15 != $_body_font_size ) {
        $_line_height = apply_filters('tc_body_line_height_ratio', 1.6 );
        $_css .= "
          {$body} {
            font-size : {$_body_font_size}px;
            line-height : {$_line_height}em;
          }\n";
        }

      return $_css;
    }//end of fn


    /**
    * Helper to check if the requested font code includes the Google font identifier : _g_
    * @return bool
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function czr_fn_is_gfont($_font , $_gfont_id = null ) {
      $_gfont_id = $_gfont_id ? $_gfont_id : '_g_';
      return false !== strpos( $_font , $_gfont_id );
    }


    /**
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function czr_fn_write_dropcap_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      if ( ! esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_enable_dropcap' ) ) )
        return $_css;

      $_main_color_pair = CZR_utils::$inst -> czr_fn_get_skin_color( 'pair' );
      $_color           = $_main_color_pair[0];
      $_shad_color      = $_main_color_pair[1];
      $_pad_right       = false !== strpos( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fonts' ) ), 'lobster' ) ? 26 : 8;
      $_css .= "
        .tc-dropcap {
          color: {$_color};
          float: left;
          font-size: 75px;
          line-height: 75px;
          padding-right: {$_pad_right}px;
          padding-left: 3px;
        }\n
        .skin-shadow .tc-dropcap {
          color: {$_color};
          text-shadow: {$_shad_color} -1px 0, {$_shad_color} 0 -1px, {$_shad_color} 0 1px, {$_shad_color} -1px -2px;
        }\n
        .simple-black .tc-dropcap {
          color: #444;
        }\n";

      return $_css;
    }


    /**
    * Set random skin
    * hook tc_opt_tc_skin
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_random_skin ( $_skin ) {
      if ( false == esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_skin_random' ) ) )
        return $_skin;

      //allow custom skins to be taken in account
      $_skins = apply_filters( 'tc_get_skin_color', CZR_init::$instance -> skin_color_map, 'all' );

      //allow users to filter the list of skins they want to randomize
      $_skins = apply_filters( 'tc_skins_to_randomize', $_skins );

      /* Generate the random skin just once !*/
      if ( ! $this -> current_random_skin && is_array( $_skins ) )
        $this -> current_random_skin = array_rand( $_skins, 1 );

      return $this -> current_random_skin;
    }


    /*************************************
    * HELPERS
    *************************************/
    /**
    * Helper to extract font-family and weight from a Customizr font option
    * @return array( font-family, weight )
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function czr_fn_get_font_css_prop( $_raw_font , $is_gfont = false ) {
      $_css_exp = explode(':', $_raw_font);
      $_weight  = isset( $_css_exp[1] ) ? $_css_exp[1] : 'inherit';
      $_family  = '';

      if ( $is_gfont ) {
        $_family = str_replace('+', ' ' , $_css_exp[0]);
      } else {
        $_family = implode("','", explode(',', $_css_exp[0] ) );
      }
      $_family = sprintf("'%s'" , $_family );

      return compact("_family" , "_weight" );
    }


    /**
    * Convenient method to normalize script enqueueing in the Customizr theme
    * @return  void
    * @uses wp_enqueue_script() to manage script dependencies
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_enqueue_script( $_handles = array() ) {
      if ( empty($_handles) )
        return;

      $_map = $this -> tc_script_map;
      //Picks the requested handles from map
      if ( 'string' == gettype($_handles) && isset($_map[$_handles]) ) {
        $_scripts = array( $_handles => $_map[$_handles] );
      }
      else {
        $_scripts = array();
        foreach ( $_handles as $_hand ) {
          if ( !isset( $_map[$_hand]) )
            continue;
          $_scripts[$_hand] = $_map[$_hand];
        }
      }

      //Enqueue the scripts with normalizes args
      foreach ( $_scripts as $_hand => $_params )
        call_user_func_array( 'wp_enqueue_script',  $this -> czr_fn_normalize_script_args( $_hand, $_params ) );

    }//end of fn



    /**
    * Helper to normalize the arguments passed to wp_enqueue_script()
    * Also handles the minified version of the file
    *
    * @return array of arguments for wp_enqueue_script
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_normalize_script_args( $_handle, $_params ) {
      //Do we load the minified version if available ?
      if ( count( $_params['files'] ) > 1 )
        $_filename = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? $_params['files'][0] : $_params['files'][1];
      else
        $_filename = $_params['files'][0];

      return array(
        $_handle,
        sprintf( '%1$s%2$s%3$s',TC_BASE_URL , $_params['path'], $_filename ),
        $_params['dependencies'],
        CUSTOMIZR_VER,
        apply_filters( "tc_load_{$_handle}_in_footer", false )
      );
    }

    /**
    * Helper
    *
    * @return boolean
    * @package Customizr
    * @since v3.3+
    */
    function czr_fn_load_concatenated_front_scripts() {
      return apply_filters( 'tc_load_concatenated_front_scripts' , ! defined('CZR_DEV')  || ( defined('CZR_DEV') && false == CZR_DEV ) );
    }

    /**
    * Helper to check if we need fancybox or not on front
    *
    * @return boolean
    * @package Customizr
    * @since v3.3+
    */
    private function czr_fn_is_fancyboxjs_required() {
      return CZR_utils::$inst -> czr_fn_opt( 'tc_fancybox' ) || CZR_utils::$inst -> czr_fn_opt( 'tc_gallery_fancybox');
    }

    /**
    * Helper to check if we need to enqueue holder js
    *
    * @return boolean
    * @package Customizr
    * @since v3.3+
    */
    function czr_fn_maybe_is_holder_js_required(){
      $bool = false;

      if ( ! ( class_exists('CZR_featured_pages') && CZR_featured_pages::$instance -> czr_fn_show_featured_pages_img() ) )
        return $bool;

      $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);

      foreach ( $fp_ids as $fp_single_id ){
        $featured_page_id = CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
        if ( null == $featured_page_id || ! $featured_page_id || ! CZR_featured_pages::$instance -> czr_fn_get_fp_img( null, $featured_page_id, null ) ) {
          $bool = true;
          break;
        }
      }
      return $bool;
    }
  }//end of CZR_ressources
endif;

?><?php
/**
* Widgets factory : registered the different widgetized areas
* The default widget areas are defined as properties of the CZR_utils class in class-fire-utils.php
* CZR_utils::$inst -> sidebar_widgets for left and right sidebars
* CZR_utils::$inst -> footer_widgets for the footer
* The widget area are then fired in the class below
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_widgets' ) ) :
  class CZR_widgets {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //widgets actions
      add_action( 'widgets_init'                    , array( $this , 'czr_fn_widgets_factory' ) );
    }

    /******************************************
    * REGISTER WIDGETS
    ******************************************/
    /**
    * Registers the widget areas
    * hook : widget_init
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_widgets_factory() {
      //default Customizr filtered args
      $default                  = apply_filters( 'tc_default_widget_args' ,
                                array(
                                  'name'                    => '',
                                  'id'                      => '',
                                  'description'             => '',
                                  'class'                   => '',
                                  'before_widget'           => '<aside id="%1$s" class="widget %2$s">',
                                  'after_widget'            => '</aside>',
                                  'before_title'            => '<h3 class="widget-title">',
                                  'after_title'             => '</h3>',
                                )
      );

      //gets the filtered default values
      $footer_widgets           = apply_filters( 'tc_footer_widgets'  , CZR_init::$instance -> footer_widgets );
      $sidebar_widgets          = apply_filters( 'tc_sidebar_widgets' , CZR_init::$instance -> sidebar_widgets );
      $widgets                  = apply_filters( 'tc_default_widgets' , array_merge( $sidebar_widgets , $footer_widgets ) );

      //declares the arguments array
      $args                     = array();

      //fills in the $args array and registers sidebars
      foreach ( $widgets as $id => $infos) {
          foreach ( $default as $key => $default_value ) {
            if ('id' == $key ) {
              $args[$key] = $id;
            }
            else if ( 'name' == $key || 'description' == $key) {
              $args[$key] = !isset($infos[$key]) ? $default_value : call_user_func( '__' , $infos[$key] , 'customizr' );
            }
            else {
              $args[$key] = !isset($infos[$key]) ? $default_value : $infos[$key];
            }
          }
        //registers sidebars
        register_sidebar( $args );
      }
    }
  }//end of class
endif;

?><?php
/**
* This class must be instantiated if is_admin() for the ajax call to work
* => because ajax request are fired with the admin_url(), even on front-end.
* more here : https://codex.wordpress.org/AJAX_in_Plugins
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_placeholders' ) ) :
  class CZR_placeholders {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'init'           , array( $this, 'czr_fn_placeholders_ajax_setup') );
      add_action( 'wp'             , array( $this, 'czr_fn_placeholders_write_ajax_js_in_footer') );
    }


    /*****************************************************
    * ADMIN AJAX HOOKS ALL PLACEHOLDERS
    *****************************************************/
    /**
    * hook : init => because we need to fire this function before the admin_ajax.php call
    * @since v3.4+
    */
    function czr_fn_placeholders_ajax_setup() {
      if ( ! $this -> czr_fn_is_front_help_enabled() )
        return;
      add_action( 'wp_ajax_dismiss_thumbnail_help'    , array( $this, 'czr_fn_dismiss_thumbnail_help' ) );
      add_action( 'wp_ajax_dismiss_img_smartload_help', array( $this, 'czr_fn_dismiss_img_smartload_help' ) );
      add_action( 'wp_ajax_dismiss_sidenav_help'      , array( $this, 'czr_fn_dismiss_sidenav_help' ) );
      add_action( 'wp_ajax_dismiss_second_menu_notice', array( $this, 'czr_fn_dismiss_second_menu_notice' ) );
      add_action( 'wp_ajax_dismiss_main_menu_notice'  , array( $this, 'czr_fn_dismiss_main_menu_notice' ) );
      add_action( 'wp_ajax_slider_notice_actions'     , array( $this, 'czr_fn_slider_notice_ajax_actions' ) );
      add_action( 'wp_ajax_fp_notice_actions'         , array( $this, 'czr_fn_fp_notice_ajax_actions' ) );
      add_action( 'wp_ajax_dismiss_widget_notice'     , array( $this, 'czr_fn_dismiss_widget_notice' ) );
    }



    /*****************************************************
    * MAYBE WRITE AJAX SCRIPTS IN FOOTER FOR ALL PLACEHOLDERS / NOTICES
    *****************************************************/
    /**
    * hook : wp => because we need to access some conditional tags like is_home when checking if the placeholder / notice are enabled
    * @since v3.4+
    */
    function czr_fn_placeholders_write_ajax_js_in_footer() {
      if ( ! $this -> czr_fn_is_front_help_enabled() )
        return;
      if ( $this -> czr_fn_is_thumbnail_help_on() )
          add_action( 'wp_footer'   , array( $this, 'czr_fn_write_thumbnail_help_js'), 100 );

      /* The actual printing of the js is controlled with a filter inside the callback */
      add_action( 'wp_footer'     , array( $this, 'czr_fn_maybe_write_img_sarmtload_help_js'), 100 );
      if ( $this -> czr_fn_is_sidenav_help_on() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_write_sidenav_help_js'), 100 );

      if ( $this -> czr_fn_is_second_menu_placeholder_on() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_write_second_menu_placeholder_js'), 100 );

      if ( $this -> czr_fn_is_main_menu_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_write_main_menu_notice_js'), 100 );

      if ( $this -> czr_fn_is_slider_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_write_slider_notice_js'), 100 );

      if ( $this -> czr_fn_is_fp_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_write_fp_notice_js'), 100 );

      if ( $this -> czr_fn_is_widget_placeholder_enabled() )
        add_action( 'wp_footer'   , array( $this, 'czr_fn_widget_placeholder_script'), 100 );
    }



    /*****************************************************
    * THUMBNAIL MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss thumbnail help
    * hook : wp_ajax_dismiss_thumbnail_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_thumbnail_help() {
      check_ajax_referer( 'tc-thumbnail-help-nonce', 'thumbnailNonce' );
      set_transient( 'tc_thumbnail_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_thumbnail_help_js() {
      ?>
      <script type="text/javascript" id="thumbnail-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_thumbnail_help',
                    thumbnailNonce :  "<?php echo wp_create_nonce( 'tc-thumbnail-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-thumbnail-help').slideToggle('fast');
            }).always(function() {console.log(arguments);});
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-thumbnail-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_thumbnail_help_on() {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'disabled' == get_transient("tc_thumbnail_help"),
        'hide' != CZR_utils::$inst->czr_fn_opt('tc_single_post_thumb_location'),
        ! is_admin() && ! is_single(),
        ! self::$instance -> czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_thumbnail_help_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }




    /*****************************************************
    * IMG SMARTLOAD MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss images smartload help
    * hook : wp_ajax_dismiss_img_smartload_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_img_smartload_help() {
      check_ajax_referer( 'tc-img-smartload-help-nonce', 'imgSmartLoadNonce' );
      set_transient( 'tc_img_smartload_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }

    /**
    * Print Smartload help block notice
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    static function czr_fn_get_smartload_help_block( $echo = false ) {
      //prepare js printing in the footer
      add_filter( 'tc_write_img_smartload_help_js', '__return_true' );

      ob_start();
      ?>
      <div class="tc-placeholder-wrap tc-img-smartload-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p>',
              __( "Did you know you can easily speed up your page load by deferring the loading of the non visible images?", "customizr" ),
              sprintf( __("%s and check the option 'Load images on scroll' under 'Website Performances' section.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', CZR_utils::czr_fn_get_customizer_url( array( "control" => "tc_img_smart_load", "section" => "performances_sec" ) ), __( "Jump to the customizer now", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
      $help_block = ob_get_contents();
      ob_end_clean();

      if ( ! $echo )
        return $help_block;
      echo $help_block;
    }



    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_maybe_write_img_sarmtload_help_js() {
      if ( ! apply_filters( 'tc_write_img_smartload_help_js', false ) ) return;
      ?>
      <script type="text/javascript" id="img-smartload-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_img_smartload_help',
                    imgSmartLoadNonce :  "<?php echo wp_create_nonce( 'tc-img-smartload-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-img-smartload-help').slideToggle('fast');
            }).always(function() {console.log(arguments);});
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-img-smartload-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_img_smartload_help_on( $text, $min_img_num = 2 ) {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      if ( $min_img_num ) {
        if ( ! $text )
          return false;
      }

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_img_smart_load' ) ),
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! self::$instance -> czr_fn_is_front_help_enabled(),
        'disabled' == get_transient("tc_img_smartload_help"),
        $min_img_num ? apply_filters('tc_img_smartload_help_n_images', $min_img_num ) > preg_match_all( '/(<img[^>]+>)/i', $text, $matches ) : false ,
        is_admin()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_img_smartload_help_on',
        ! (bool) array_sum( $_dont_display_conditions )
      );
    }





    /*****************************************************
    * SIDENAV MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss sidenav help
    * hook : wp_ajax_dismiss_sidenav_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_sidenav_help() {
      check_ajax_referer( 'tc-sidenav-help-nonce', 'sideNavNonce' );
      set_transient( 'tc_sidenav_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_sidenav_help_js() {
      ?>
      <script type="text/javascript" id="sidenav-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_sidenav_help',
                    sideNavNonce :  "<?php echo wp_create_nonce( 'tc-sidenav-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-sidenav-help').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-sidenav-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_sidenav_help_on() {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        CZR_utils::$inst->czr_fn_has_location_menu('main'),// => if the "main" location has a menu assigned
        'navbar' == CZR_utils::$inst->czr_fn_opt('tc_menu_style'),
        'disabled' == get_transient("tc_sidenav_help"),
        ! self::$instance -> czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_sidenav_help_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }






    /*****************************************************
    * SECOND MENU PLACEHOLDER : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss notice ajax callback
    * hook : wp_ajax_dismiss_second_menu_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_second_menu_notice() {
      check_ajax_referer( 'tc-second-menu-placeholder-nonce', 'secondMenuNonce' );
      set_transient( 'tc_second_menu_placehold', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_second_menu_placeholder_js() {
      ?>
      <script type="text/javascript" id="second-menu-placeholder">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_second_menu_notice',
                    secondMenuNonce :  "<?php echo wp_create_nonce( 'tc-second-menu-placeholder-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-menu-placeholder').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-menu-placeholder').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_second_menu_placeholder_on() {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;
      //don't display if main menu style is regular <=> 'navbar' == tc_menu_style
      if ( 'navbar' == CZR_utils::$inst->czr_fn_opt('tc_menu_style') )
        return false;
      //don't display if second menu is enabled : tc_display_second_menu
      if ( (bool)CZR_utils::$inst->czr_fn_opt('tc_display_second_menu') )
        return false;

      return apply_filters(
        "tc_is_second_menu_placeholder_on",
        self::$instance -> czr_fn_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && 'disabled' != get_transient("tc_second_menu_placehold")
      );
    }



    /*****************************************************
    * MAIN MENU NOTICE : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss notice ajax callback
    * hook : wp_ajax_dismiss_main_menu_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_main_menu_notice() {
      check_ajax_referer( 'tc-main-menu-notice-nonce', 'mainMenuNonce' );
      set_transient( 'tc_main_menu_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_main_menu_notice_js() {
      ?>
      <script type="text/javascript" id="main-menu-placeholder">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_main_menu_notice',
                    mainMenuNonce :  "<?php echo wp_create_nonce( 'tc-main-menu-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-main-menu-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-main-menu-notice').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_main_menu_notice_on() {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'navbar' != CZR_utils::$inst->czr_fn_opt('tc_menu_style'),
        (bool)CZR_utils::$inst->czr_fn_opt('tc_display_second_menu'),
        'disabled' == get_transient("tc_main_menu_notice"),
        ! self::$instance -> czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_main_menu_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }



    /*****************************************************
    * SLIDER : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Two cases :
    * 1) dismiss notice
    * 2) remove demo slider
    * hook : wp_ajax_slider_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_slider_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-slider-notice-nonce', 'sliderNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_slider':
          CZR_utils::$inst -> czr_fn_set_option( 'tc_front_slider' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_slider_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }


    /**
    * Prints dismiss notice js in the footer
    * Two cases :
    * 1) dismiss notice
    * 2) remove demo slider
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_slider_notice_js() {
      ?>
      <script type="text/javascript" id="slider-notice-actions">
        ( function( $ ) {
          var slider_ajax_request = function( remove_action, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'slider_notice_actions',
                    remove_action : remove_action,
                    sliderNoticeNonce :  "<?php echo wp_create_nonce( 'tc-slider-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              if ( 'remove_slider' == remove_action )
                $('div[id*="customizr-slider"]').fadeOut('slow');
              else
                $_el.closest('.tc-slider-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-slider-notice').click( function( e ) {
              e.preventDefault();
              slider_ajax_request( 'remove_notice', $(this) );
            } );
            $('.tc-inline-remove', '.tc-slider-notice').click( function( e ) {
              e.preventDefault();
              slider_ajax_request( 'remove_slider', $(this) );
            } );
          } );

        }) (jQuery)
      </script>
      <?php
    }


    /**
    * Do we display the slider notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_slider_notice_on( $_position = null ) {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! CZR_utils::$inst-> czr_fn_is_home(),
        'demo' != CZR_utils::$inst->czr_fn_opt('tc_front_slider'),
        'disabled' == get_transient("tc_slider_notice"),
        ! self::$instance -> czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_slider_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }




    /*****************************************************
    * FEATURED PAGES : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Two cases :
    * 1) dismiss notice
    * 2) remove fp
    * hook : wp_ajax_fp_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_fp_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-fp-notice-nonce', 'fpNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_fp':
          CZR_utils::$inst -> czr_fn_set_option( 'tc_show_featured_pages' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_fp_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }


    /**
    * Prints dismiss notice js in the footer
    * Two cases :
    * 1) dismiss notice
    * 2) remove fp
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_write_fp_notice_js() {
      ?>
      <script type="text/javascript" id="fp-notice-actions">
        ( function( $ ) {
          var fp_ajax_request = function( remove_action, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'fp_notice_actions',
                    remove_action : remove_action,
                    fpNoticeNonce :  "<?php echo wp_create_nonce( 'tc-fp-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              if ( 'remove_fp' == remove_action )
                $('#main-wrapper > .marketing').fadeOut('slow');
              else
                $_el.closest('.tc-fp-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-fp-notice').click( function( e ) {
              e.preventDefault();
              fp_ajax_request( 'remove_notice', $(this) );
            } );
            $('.tc-inline-remove', '.tc-fp-notice').click( function( e ) {
              e.preventDefault();
              fp_ajax_request( 'remove_fp', $(this) );
            } );
          } );

        }) (jQuery)
      </script>
      <?php
    }


    /**
    * Do we display the featured page notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_fp_notice_on( $_position = null ) {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! CZR_utils::$inst-> czr_fn_is_home(),
        ! (bool)CZR_utils::$inst->czr_fn_opt('tc_show_featured_pages'),
        'disabled' == get_transient("tc_fp_notice"),
        self::$instance -> czr_fn_is_one_fp_set(),
        CZR___::czr_fn_is_pro(),
        CZR_plugins_compat::$instance->czr_fn_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php'),
        ! self::$instance -> czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_fp_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }


    /**
    * Helper to check if at least one featured page has been set by the user.
    * @return bool
    * @since v3.4+
    */
    function czr_fn_is_one_fp_set() {
      $_fp_sets = array();
      $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);
      if ( ! is_array($fp_ids) )
        return;
      foreach ($fp_ids as $fp_single_id ) {
        $_fp_sets[] = (bool)CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
      }
      //returns true if at least one fp has been set.
      return (bool)array_sum($_fp_sets);
    }



    /************************************************************
    * WIDGET PLACEHOLDERS AJAX JS AND CALLBACK : FOR SIDEBARS AND FOOTER
    ************************************************************/
    /**
    * Prints dismiss widget notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_widget_placeholder_script() {
      ?>
      <script type="text/javascript" id="widget-placeholders">
        var tc_dismiss_widget_notice = function( _position, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_widget_notice',
                    WidgetNonce :  "<?php echo wp_create_nonce( 'tc-widget-placeholder-nonce' ); ?>",
                    position : _position
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                  return;
              // Check for cheaters.
              if ( '-1' === response )
                  return;
              if ( 'sidebar' == _position )
                $('.tc-widget-placeholder' , '.tc-sidebar').slideToggle('fast');
              else
                $_el.closest('.tc-widget-placeholder').slideToggle('fast');
            });
        };//end of fn
        jQuery( function($) {
          $('.tc-dismiss-notice, .tc-inline-dismiss-notice').click( function( e ) {
            e.preventDefault();
            var _position = $(this).attr('data-position');
            if ( ! _position || ! _position.length )
              return;
            czr_fn_dismiss_widget_notice( _position, $(this) );
          } );
        } );
      </script>
      <?php
    }


    /**
    * Dismiss widget notice ajax callback
    * hook : wp_ajax_dismiss_widget_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_widget_notice() {
      check_ajax_referer( 'tc-widget-placeholder-nonce', 'WidgetNonce' );
      if ( isset( $_POST['position'] ) )
        $_pos = esc_attr( $_POST['position'] );
      else
        wp_die(0);
      //20 years transient
      set_transient( "tc_widget_placehold_{$_pos}", 'disabled' , 60*60*24*365*20 );
      wp_die();
    }


    /**
    * Public helper, state if we can display a widget placeholder to the current user.
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_widget_placeholder_enabled( $_position = null ) {
      //never display when customizing
      if ( CZR___::$instance -> czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_position = is_null($_position) ? apply_filters('tc_widget_areas_position', array( 'sidebar', 'footer') ) : array($_position);

      return apply_filters( "tc_display_widget_placeholders",
        self::$instance -> czr_fn_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && array_sum( array_map( array( self::$instance , 'czr_fn_check_widget_placeholder_transient'), $_position ) )
      );
    }

    /**
    * @return  bool
    * @since Customizr 3.3+
    */
    function czr_fn_check_widget_placeholder_transient( $_position ){
      return 'disabled' != get_transient("tc_widget_placehold_{$_position}");
    }


    /**
    * @return  bool
    * @since Customizr 3.4+
    * User option to enabe/disable all notices. Enabled by default.
    */
    function czr_fn_is_front_help_enabled(){
      return apply_filters( 'tc_is_front_help_enabled' , (bool)CZR_utils::$inst->czr_fn_opt('tc_display_front_help') );
    }

  }//end of class
endif;

?><?php
if ( ! class_exists( 'CZR_prevdem' ) ) :
  final class CZR_prevdem {
    function __construct() {
      //SKIN
      add_filter('tc_opt_tc_skin' , array( $this, 'czr_fn_set_skin' ) );

      //HEADER
      //add_filter('option_blogname', array( $this, 'czr_fn_set_blogname'), 100 );
      add_filter('tc_social_in_header' , array( $this, 'czr_fn_set_header_socials' ) );
      add_filter('tc_tagline_display' , array( $this, 'czr_fn_set_header_tagline' ) );

      //FRONT PAGE
      add_filter('option_show_on_front', array( $this, 'czr_fn_set_front_page_content' ), 99999 );
      add_filter('pre_option_show_on_front', array( $this, 'czr_fn_set_front_page_content' ), 99999 );

      //FEATURED PAGES
      add_filter('fp_img_src', array( $this, 'czr_fn_set_fp_img_src'), 100 );
      add_filter('tc_fp_title', array( $this, 'czr_fn_set_fp_title'), 100, 3 );
      add_filter('tc_fp_text', array( $this, 'czr_fn_set_fp_text'), 100 );
      add_filter('tc_fp_link_url', array( $this, 'czr_fn_set_fp_link'), 100 );

      //THUMBNAILS
      add_filter('tc_has_thumb', '__return_true');
      add_filter('tc_has_thumb_info', '__return_true');
      add_filter('tc_has_wp_thumb_image', '__return_true');
      add_filter('tc_thumb_html', array( $this, 'czr_fn_filter_thumb_src'), 10, 6 );

      //SLIDER
      add_filter('tc_default_slides', array( $this, 'czr_fn_set_default_slides') );
      //adds infos in the caption data of the demo slider
      add_filter('tc_slide_caption_data' , array( $this, 'czr_fn_set_demo_slide_data'), 100, 3 );
      add_filter('tc_opt_tc_slider_delay', array( $this, 'czr_fn_set_demo_slider_delay') );

      //SINGLE POSTS
      add_filter('tc_show_single_post_thumbnail', '__return_true');
      add_filter('tc_single_post_thumb_hook', array( $this, 'czr_fn_set_single_post_thumb_hook') );
      add_filter('tc_single_post_thumb_height', array( $this, 'czr_fn_set_single_post_thumb_height') );

      //SOCIALS
      add_filter('option_tc_theme_options', array( $this, 'czr_fn_set_socials'), 100 );

      //WIDGETS
      add_action('dynamic_sidebar_before', array( $this, 'czr_fn_set_widgets'), 10, 2 );
      add_filter('tc_has_footer_widgets', '__return_true');
      add_filter('tc_has_footer_widgets_zone', '__return_true');
      add_filter('tc_has_sidebar_widgets', '__return_true');
    }//construct

    /* ------------------------------------------------------------------------- *
     *  Socials
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_socials( $options ) {
      if ( CZR___::$instance -> czr_fn_is_customize_left_panel() )
        return $options;

      $to_display = array('tc_facebook', 'tc_twitter', 'tc_linkedin', 'tc_google');
      foreach ($to_display as $social) {
         $options[$social] = 'javascript:void()';
      }
      $options['tc_rss'] = '';
      return $options;
    }

    /* ------------------------------------------------------------------------- *
     *  Skin
    /* ------------------------------------------------------------------------- */
    //hook : tc_opt_tc_skin
    function czr_fn_set_skin( $skin ) {
      $theme_skins = CZR_init::$instance -> skins;
      $new_skin = 'grey.css';
      if ( ! isset( $theme_skins[$new_skin] ) )
        return $skin;

      return $new_skin;
    }




    /* ------------------------------------------------------------------------- *
     *  Header
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_header_socials() {
      return '';
    }

    function czr_fn_set_header_tagline() {
      return '';
    }

    function czr_fn_set_blogname() {
        return 'Customizr';
    }


    /* ------------------------------------------------------------------------- *
     *  Front page
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_front_page_content( $value ) {
        return 'posts';
    }


    /* ------------------------------------------------------------------------- *
     *  Featured Pages
    /* ------------------------------------------------------------------------- */
    //hook : fp_img_src
    function czr_fn_set_fp_img_src($fp_img) {
      return CZR_featured_pages::$instance -> czr_fn_get_fp_img( 'tc-thumb' );
    }

    function czr_fn_set_fp_title( $text, $fp_single_id, $featured_page_id ) {
      switch ($fp_single_id) {
        case 'one':
          $text = __('Who We Are', 'customizr');
          break;

        case 'two':
          $text = __('What We Do', 'customizr');
          break;

        case 'three':
          $text = __('Contact Us', 'customizr');
          break;
      }
      return $text;
    }

    function czr_fn_set_fp_text() {
      return '';
    }

    function czr_fn_set_fp_link() {
      return 'javascript:void(0)';
    }

    /* ------------------------------------------------------------------------- *
     *  Thumbnails
    /* ------------------------------------------------------------------------- */
    //@param img :array (url, width, height, is_intermediate), or false, if no image is available.
    function czr_fn_filter_thumb_src( $tc_thumb, $requested_size, $_post_id, $_custom_thumb_id, $_img_attr, $tc_thumb_size ) {
      if ( ! empty($tc_thumb) )
        return $tc_thumb;

      $new_img_src = $this -> czr_fn_get_prevdem_img_src( $tc_thumb_size );
      if ( ! is_string($new_img_src) || empty($new_img_src) )
        return $tc_thumb;

      $_img_attr = is_array($_img_attr) ? $_img_attr : array();
      if ( false == $tc_thumb || empty( $tc_thumb ) ) {
        $tc_thumb = sprintf('<img src="%1$s" class="%2$s">',
          $new_img_src,
          isset($_img_attr['class']) ? $_img_attr['class'] : ''
        );
      } else {
        $regex = '#<img([^>]*) src="([^"/]*/?[^".]*\.[^"]*)"([^>]*)>#';
        $replace = "<img$1 src='$new_img_src'$3>";
        $tc_thumb = preg_replace($regex, $replace, $tc_thumb);
      }
      return $tc_thumb;
    }



    /* Placeholder thumb helper
    *  @return a random img src string
    *  Can be recursive if a specific img size is not found
    */
    function czr_fn_get_prevdem_img_src( $_size = 'tc-grid', $img_id = null, $i = 0 ) {
        //prevent infinite loop
        if ( 10 == $i ) {
          return;
        }
        $sizes_suffix_map = array(
            'tc-thumb'     => '270x250',
            'tc-grid-full'    => '1170x350',
            'tc-grid'  => '570x350',
            'slider' => '1170x500'
        );
        $requested_size = isset( $sizes_suffix_map[$_size] ) ? $sizes_suffix_map[$_size] : '570x350';
        $path = TC_BASE . '/assets/front/img/demo/';

        //Build or re-build the global dem img array
        if ( ! isset( $GLOBALS['prevdem_img'] ) || empty( $GLOBALS['prevdem_img'] ) ) {
            $imgs = array();
            if ( is_dir( $path ) ) {
              $imgs = scandir( $path );
            }
            $candidates = array();
            if ( ! $imgs || empty( $imgs ) )
              return array();

            foreach ( $imgs as $img ) {
              if ( '.' === $img[0] || is_dir( $path . $img ) ) {
                continue;
              }
              $candidates[] = $img;
            }
            $GLOBALS['prevdem_img'] = $candidates;
        }

        $candidates = $GLOBALS['prevdem_img'];

        //get a random image name if no specific image id requested
        $img_prefix = '';
        if ( is_null($img_id) ) {
            $rand_key = array_rand($candidates);
            $img_name = $candidates[ $rand_key ];
            //extract img prefix
            $img_prefix_expl = explode( '-', $img_name );
            $img_prefix = $img_prefix_expl[0];
        } else {
            $img_prefix = $img_id;
        }

        $requested_size_img_name = "{$img_prefix}-{$requested_size}.jpg";
        //if file does not exists, reset the global and recursively call it again
        if ( ! file_exists( $path . $requested_size_img_name ) ) {
          unset( $GLOBALS['prevdem_img'] );
          $i++;
          return $this -> czr_fn_get_prevdem_img_src( $_size, null, $i );
        }
        //unset all sizes of the img found and update the global
        $new_candidates = $candidates;
        foreach ( $candidates as $_key => $_img ) {
          if ( substr( $_img , 0, strlen( "{$img_prefix}-" ) ) == "{$img_prefix}-" ) {
            unset( $new_candidates[$_key] );
          }
        }
        $GLOBALS['prevdem_img'] = $new_candidates;
        return get_template_directory_uri() . '/assets/front/img/demo/' . $requested_size_img_name;
    }




    /* ------------------------------------------------------------------------- *
     *  Slider
    /* ------------------------------------------------------------------------- */
    //hook : tc_default_slides
    //@return array of default slides
    function czr_fn_set_default_slides() {
        $defaults = array(
            'title'         =>  '',
            'text'          =>  '',
            'button_text'   =>  '',
            'link_id'       =>  null,
            'link_url'      =>  null,
            'active'        =>  '',
            'color_style'   =>  '',
            'slide_background' =>  ''
        );
        $slides = array(
            1 => array(
              'active'        =>  'active',
              'slide_background'  =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        TC_BASE_URL.'assets/front/img/customizr-theme.jpg',
                                        __( 'Customizr is a clean responsive theme' , 'customizr' )
                                  )
            ),
            2 => array(
              'slide_background' => sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                    $this -> czr_fn_get_prevdem_img_src( 'slider', '4' ),
                                    __( 'Customizr is a clean responsive theme' , 'customizr' )
                                )
            ),
            3 => array(
              'slide_background' => sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        $this -> czr_fn_get_prevdem_img_src( 'slider', '16' ),
                                        __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                )
            )
        );
        $new_slides = array();
        foreach ($slides as $key => $value) {
          $new_slides[$key] = wp_parse_args( $value, $defaults );
        }
        return $new_slides;
    }

    //hook : tc_slide_caption_data
    function czr_fn_set_demo_slide_data( $data, $slider_name_id, $id ) {
        // if ( 'demo' != $slider_name_id || ! is_user_logged_in() )
        //   return $data;

        switch ( $id ) {
          case 1 :
            $data['title']        = '';
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = '';//__( 'Call to action' , 'customizr');
          break;

          case 2 :
            $data['title']        = __( 'The Customizr theme fits nicely on any mobile devices.', 'customizr' );
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = '';//__( 'Call to action' , 'customizr');
          break;

          case 3 :
            $data['title']        = __( 'Engage your visitors with a carousel in any pages.', 'customizr' );
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = __( 'Call to action' , 'customizr');
          break;
        };

        $data['link_target'] = '_blank';
        return $data;
    }


    function czr_fn_set_demo_slider_delay() {
      return 6000;
    }


    /* ------------------------------------------------------------------------- *
     *  Single Posts
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_single_post_thumb_hook() {
      return '__before_main_wrapper';
    }

    function czr_fn_set_single_post_thumb_height() {
      return 350;
    }

    /* ------------------------------------------------------------------------- *
     *  Widgets
    /* ------------------------------------------------------------------------- */
    //hook : 'dynamic_sidebar_before'
    // @param int|string $index       Index, name, or ID of the dynamic sidebar.
    // @param bool       $has_widgets Whether the sidebar is populated with widgets.
    //                                Default true.
    function czr_fn_set_widgets( $index, $bool ) {
      if ( true === $bool )
        return;

      //we only want to print default widgets in primary and secondary sidebars
      if ( ! in_array( $index, array( 'left', 'right', 'footer_one', 'footer_two', 'footer_three') ) )
        return;

      $default_args = apply_filters( 'tc_default_widget_args' ,
          array(
            'name'                    => '',
            'id'                      => '',
            'description'             => '',
            'class'                   => '',
            //'before_widget'           => '<aside id="%1$s" class="widget %2$s">',
            //'after_widget'            => '</aside>',
            'before_title'            => '<h3 class="widget-title">',
            'after_title'             => '</h3>'
          )
      );

      $_widgets_to_print = array();
      switch ($index) {
        case 'left':
        case 'right':
          $_widgets_to_print[] = array(
            'WP_Widget_Search' => array(
              'instance' => array(
                'title' => __( 'Search', 'customizr')
              ),
              'args' => $default_args
            ),
            'WP_Widget_Recent_Posts' => array(
              'instance' => array(
                'title' => __( 'Recent Posts', 'customizr'),
                'number' => 6
              ),
              'args' => $default_args
            ),
            'WP_Widget_Recent_Comments' => array(
              'instance' => array(
                'title' => __( 'Recent Comments', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_one':
          $_widgets_to_print[] = array(
            'WP_Widget_Recent_Posts' => array(
              'instance' => array(
                'title' => __( 'Recent Posts', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_two':
          $_widgets_to_print[] = array(
            'WP_Widget_Recent_Comments' => array(
              'instance' => array(
                'title' => __( 'Recent Comments', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_three':
          $_widgets_to_print[] = array(
            'WP_Widget_Search' => array(
              'instance' => array(
                'title' => __( 'Search', 'customizr')
              ),
              'args' => $default_args
            )
          );
        break;
      }
      if ( empty($_widgets_to_print) )
        return;

      //find the widget instance ids
      $_wgt_instances = array();

      foreach ( $_widgets_to_print as $_wgt ) {
        foreach (  $_wgt as $_class => $params ) {
            if ( class_exists( $_class) ) {
              $_instance = isset( $params['instance'] ) ? $params['instance'] : array();
              $_args = isset( $params['args'] ) ? $params['args'] : array();
              the_widget( $_class, $_instance, $_args );
            }
        }
      }



    }

  }//end of class
endif;

?><?php
//Creates a new instance
new CZR___;
do_action('czr_load');

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

//@return an array of options
function czr_fn_get_admin_option( $option_group = null ) {
  $option_group           = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;

  //here we could hook a callback to remove all the filters on "option_{CZR_THEME_OPTIONS}"
  do_action( "tc_before_getting_option_{$option_group}" );
  $options = get_option( $option_group, array() );
  //here we could hook a callback to re-add all the filters on "option_{CZR_THEME_OPTIONS}"
  do_action( "tc_after_getting_option_{$option_group}" );

  return $options;
}

//@return bool
function czr_fn_isprevdem() {
  $_active_theme = czr_fn_get_raw_option( 'template' );
  //get WP_Theme object
  $czr_theme                     = wp_get_theme();
  //Get infos from parent theme if using a child theme
  $czr_theme = $czr_theme -> parent() ? $czr_theme -> parent() : $czr_theme;
  return apply_filters( 'czr_fn_isprevdem', ( $_active_theme != strtolower( $czr_theme -> name ) && ! is_child_theme() && ! CZR___::czr_fn_is_pro() ) );
}

if ( czr_fn_isprevdem() && class_exists('CZR_prevdem') ) {
  new CZR_prevdem();
}

//may be load pro
if ( CZR___::czr_fn_is_pro() )
  new CZR_init_pro(CZR___::$theme_name );
?>