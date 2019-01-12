<?php
/**
* Fires the theme : constants definition, core classes loading
*
*/
if ( ! class_exists( 'CZR___' ) ) :
  final class CZR___ extends CZR_BASE {
    public $tc_core;
    public $is_customizing;
    public static $tc_option_group;

    function __construct () {
        //following R. Aliberti advise
        if( ! defined( 'CZR_IS_MODERN_STYLE' ) )            define( 'CZR_IS_MODERN_STYLE' , false );

        //call CZR_BASE constructor
        parent::__construct();

        self::$instance =& $this;

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
            // if ( czr_fn_is_child() && file_exists( TC_BASE_CHILD . $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] .'.php') ) {
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
        if ( czr_fn_is_child() && file_exists( TC_BASE_CHILD . $file_path ) ) {
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
        //---1.2) IS NOT ADMIN
        //2) IS CUSTOMIZING
        //---2.1) IS LEFT PANEL => customizer controls
        //---2.2) IS RIGHT PANEL => preview
        if ( ! czr_fn_is_customizing() )
          {
            if ( is_admin() ) {
              //load
              $this -> czr_fn_req_once( 'core/czr-admin-ccat.php' );

              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|inc/admin|customize' ) );
            }
            else {
              //load
              $this -> czr_fn_req_once( 'inc/czr-front-ccat.php' );

              //Skips all admin classes
              $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|inc/admin|admin_init', 'fire|inc/admin|admin_page') );
            }
          }
        //Customizing
        else
          {
            //load
            $this -> czr_fn_req_once( 'core/czr-admin-ccat.php' );
            $this -> czr_fn_req_once( 'core/czr-customize-ccat.php' );

            //left panel => skip all front end classes
            if ( czr_fn_is_customize_left_panel() ) {
              $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array( 'header' , 'content' , 'footer' ),
                  array( 'fire|inc|resources' , 'fire|inc/admin|admin_page' , 'admin|inc/admin|meta_boxes' )
              );
            }
            if ( czr_fn_is_customize_preview_frame() ) {
              //load
              $this -> czr_fn_req_once( 'inc/czr-front-ccat.php' );

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


  }//end of class
endif;

?>
