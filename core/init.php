<?php
/**
* Fires the theme : constants definition, core classes loading
*
*
* @package      Customizr
* @subpackage   classes
* @since        4.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR___' ) ) :

  final class CZR___ extends CZR_BASE  {
        public $czr_core;

        public $collection;
        public $views;//object, stores the views
        public $controllers;//object, stores the controllers

        //stack
        public $current_model = array();

        private $existing_files     = array();
        private $not_existing_files = array();

        function __construct( $_args = array()) {

            //call CZR_BASE constructor
            parent::__construct( $_args );

            //allow c4 templates
            add_filter( 'czr_four_do'             , '__return_true' );
            //define a constant we can use everywhere
            //that will tell us we're in the new Customizr:
            //Will be highly used during the transion between the two themes
            if( ! defined( 'CUSTOMIZR_4' ) )            define( 'CUSTOMIZR_4' , true );

            //this action callback is the one responsible to load new czr main templates
            add_action( 'czr_four_template'       , array( $this , 'czr_fn_four_template_redirect' ), 10 , 1 );

            //filters to 'the_content', 'wp_title' => in utils
            add_action( 'wp_head' , 'czr_fn_wp_filters' );

            add_action( 'czr_dev_notice', array( $this, 'czr_fn_print_r') );

        }


        //hook : czr_dev_notice
        function czr_fn_print_r($message) {
          if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
            return;
          ?>
            <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
          <?php
        }


        public static function czr_fn_instance() {
              if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CZR___ ) ) {
                self::$instance = new CZR___();
                //defined in CZR_BASE
                self::$instance -> czr_fn_setup_constants();

                self::$instance -> czr_fn_setup_loading();
                self::$instance -> czr_fn_load();

                //FMK
                self::$instance -> collection = new CZR_Collection();
                self::$instance -> controllers = new CZR_Controllers();

                //register the model's map in front
                if ( ! is_admin() )
                  add_action('wp'         , array(self::$instance, 'czr_fn_register_model_map') );
              }
              return self::$instance;
        }





        /**
        * The purpose of this callback is to load the themes bootstrap4 main templates
        * hook : czr_four_template
        * @return  void
        */
        public function czr_fn_four_template_redirect( $template = null ) {
          $template = $template ? $template : 'index';
          $this -> czr_fn_require_once( CZR_MAIN_TEMPLATES_PATH . $template . '.php' );
        }




        private function czr_fn_setup_loading() {
              //this is the structure of the Customizr code : groups => ('path' , 'class_suffix')
              $this -> czr_core = apply_filters( 'czr_core',
                array(
                    'fire'      =>   array(
                        array('core'       , 'resources_styles'),
                        array('core'       , 'resources_fonts'),
                        array('core'       , 'resources_scripts'),
                        array('core'       , 'widgets'),//widget factory
                        array('core/back'  , 'admin_init'),//loads admin style and javascript ressources. Handles various pure admin actions (no customizer actions)
                        array('core/back'  , 'admin_page')//creates the welcome/help panel including changelog and system config
                    ),
                    'admin'     => array(
                        array('core/back' , 'customize'),//loads customizer actions and resources
                        array('core/back' , 'meta_boxes')//loads the meta boxes for pages, posts and attachment : slider and layout settings
                    ),
                    'header'    =>   array(
                        array('core/front/utils', 'nav_walker')
                    ),
                    'content'   =>   array(
                        array('core/front/utils', 'gallery')
                    ),
                    'addons'    => apply_filters( 'tc_addons_classes' , array() )
                )
              );

              //set files to load according to the context : admin / front / customize
              add_filter( 'czr_get_files_to_load' , array( $this , 'czr_fn_set_files_to_load' ) );
        }


        /**
        * Class instantiation using a singleton factory :
        * Can be called to instantiate a specific class or group of classes
        * @param  array(). Ex : array ('admin' => array( array( 'core/back' , 'meta_boxes') ) )
        * @return  instances array()
        *
        * Thanks to Ben Doherty (https://github.com/bendoh) for the great programming approach
        *
        * @since Customizr 3.0
        */
        function czr_fn_load( $_to_load = array(), $_no_filter = false ) {
            //loads utils
            if ( CZR_DEV_MODE ) {
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-0-base.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-1-settings_map.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-2-utils.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-3-options.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-4-query.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-5-thumbnails.php' );
                require_once( CZR_BASE_CHILD . 'core/_utils/fn-6-colors.php' );
            } else {
                require_once( get_template_directory() . '/core/functions.php' );
            }

            do_action( 'czr_load' );

            //loads init
            $this -> czr_fn_require_once( CZR_CORE_PATH . 'class-fire-init.php' );
            new CZR_init();

            //Retro Compat has to be fired after class-fire-init.php, according to R. Aliberti. Well probably.
            $this -> czr_fn_require_once( CZR_CORE_PATH  . 'class-fire-init_retro_compat.php' );

            //loads the plugin compatibility
            $this -> czr_fn_require_once( CZR_CORE_PATH . 'class-fire-plugins_compat.php' );
            new CZR_plugins_compat();


            //do we apply a filter ? optional boolean can force no filter
            $_to_load = $_no_filter ? $_to_load : apply_filters( 'czr_get_files_to_load' , $_to_load );
            if ( empty($_to_load) )
              return;

            foreach ( $_to_load as $group => $files ) {
                foreach ($files as $path_suffix ) {
                    $this -> czr_fn_require_once ( $path_suffix[0] . '/class-' . $group . '-' .$path_suffix[1] . '.php');
                    $classname = 'CZR_' . $path_suffix[1];

                    if ( in_array( $classname, apply_filters( 'czr_dont_instantiate_in_init', array( 'CZR_nav_walker') ) ) )
                      continue;
                    //instantiates
                    $instances = class_exists($classname)  ? new $classname : '';
                }
            }


            //load the new framework classes
            if ( CZR_DEV_MODE ) {
                $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-model.php' );
                $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-collection.php' );
                $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-view.php' );
                $this -> czr_fn_require_once( CZR_FRAMEWORK_PATH . 'class-controllers.php' );
            } else {
                $this -> czr_fn_require_once( CZR_CORE_PATH . 'fmk.php' );
            }

            //load front templates tags files
            if ( ! is_admin() )
              $this -> czr_fn_require_once( CZR_PHP_FRONT_PATH . 'template-tags/template-tags.php' );

            //may be load pro
            if ( CZR_IS_PRO ) {
                new CZR_init_pro(CZR___::$theme_name );
            }

        }//czf_fn_load()



        //hook : wp
        function czr_fn_register_model_map( $_map = array() ) {
          $_to_register =  ( empty( $_map ) || ! is_array($_map) ) ? $this -> czr_fn_get_model_map() : $_map;
          $CZR          = CZR();

          foreach ( $_to_register as $model ) {
            $CZR -> collection -> czr_fn_register( $model);
          }

        }


        //returns an array of models describing the theme's views
        private function czr_fn_get_model_map() {
          return apply_filters(
            'czr_model_map',
            array(

              /*********************************************
              * HEADER
              *********************************************/
              array(
                'model_class'    => 'header',
                'id'             => 'header'
              ),


              /*********************************************
              * Featured Pages
              *********************************************/
              /* contains the featured page item registration */
              array(
                'id'          => 'featured_pages',
                'model_class' => 'modules/featured-pages/featured_pages',
              ),
              /** end featured pages **/

              /*********************************************
              * CONTENT
              *********************************************/
              array(
                'id'             => 'main_content',
                'model_class'    => 'content',
              ),

              /*********************************************
              * FOOTER
              *********************************************/
              array(
                'id'           => 'footer',
                'model_class'  => 'footer',
              ),
            )
          );
        }




        /***************************
        * HELPERS
        ****************************/
        /**
        * Check the context and return the modified array of class files to load and instantiate
        * hook : czr_fn_get_files_to_load
        * @return boolean
        *
        * @since  Customizr 3.3+
        */
        function czr_fn_set_files_to_load( $_to_load ) {
          $_to_load = empty($_to_load) ? $this -> czr_core : $_to_load;
          //Not customizing
          //1) IS NOT CUSTOMIZING : czr_fn_is_customize_left_panel() || czr_fn_is_customize_preview_frame() || czr_fn_doing_customizer_ajax()
          //---1.1) IS ADMIN
          //-------1.1.a) Doing AJAX
          //-------1.1.b) Not Doing AJAX
          //---1.2) IS NOT ADMIN
          //2) IS CUSTOMIZING
          //---2.1) IS LEFT PANEL => customizer controls
          //---2.2) IS RIGHT PANEL => preview
          if ( ! czr_fn_is_customizing() ) {
              if ( is_admin() ) {
                //load
                czr_fn_require_once( CZR_CORE_PATH . 'czr-admin.php' );

                //if doing ajax, we must not exclude the placeholders
                //because ajax actions are fired by admin_ajax.php where is_admin===true.
                if ( defined( 'DOING_AJAX' ) )
                  $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|core/back|customize' ) );
                else
                  $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'header' , 'content' , 'footer' ), array( 'admin|core/back|customize', 'fire|core|placehloders' ) );
              }
              else {
                //Skips all admin classes
                $_to_load = $this -> czr_fn_unset_core_classes( $_to_load, array( 'admin' ), array( 'fire|core/admin|admin_init', 'fire|core/admin|admin_page') );
              }
          }
          //Customizing
          else
            {
              //load
              czr_fn_require_once( CZR_CORE_PATH . 'czr-admin.php' );
              czr_fn_require_once( CZR_CORE_PATH . 'czr-customize.php' );
              //new CZR_customize();

              //left panel => skip all front end classes
              if (czr_fn_is_customize_left_panel() ) {
                $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array( 'header' , 'content' , 'footer' ),
                  array( 'fire|core|resources_styles' , 'fire|core', 'fire|core|resources_scripts', 'fire|core/back|admin_page' , 'admin|core/back|meta_boxes' )
                );
              }
              if ( czr_fn_is_customize_preview_frame() ) {
                $_to_load = $this -> czr_fn_unset_core_classes(
                  $_to_load,
                  array(),
                  array( 'fire|core/back|admin_init', 'fire|core/back|admin_page' , 'admin|core/back|meta_boxes' )
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
        * Specific syntax for single files: ex in fire|core/back|admin_page
        * => fire is the group, core/back is the path, admin_page is the file suffix.
        * => will unset core/back/class-fire-admin_page.php
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
              //$_concat looks like : fire|core|resources
              $_exploded = explode( '|', $_concat );
              //each single file entry must be a string like 'admin|core/back|customize'
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


        //called when requiring a file - will always give the precedence to the child-theme file if it exists
        //then to the theme root
        function czr_fn_get_theme_file( $path_suffix ) {
            $path_prefixes = array_unique( apply_filters( 'czr_include_paths'     , array( '' ) ) );
            $roots         = array_unique( apply_filters( 'czr_include_roots_path', array( CZR_BASE_CHILD, CZR_BASE ) ) );

            foreach ( $roots as $root ) {
              foreach ( $path_prefixes as $path_prefix ) {

                $filename     = $root . $path_prefix . $path_suffix;
                $_exists      = in_array( $filename, $this->existing_files );
                $_exists_not  = in_array( $filename, $this->not_existing_files );


                if ( !$_exists_not && ( $_exists || file_exists( $filename ) ) ) {

                  //cache file existence
                  if ( !$_exists ) {
                    $this->existing_files[] = $filename;

                  }

                  return $filename;

                }
                else if ( !$_exists_not ) {
                  //cache file not existence
                  $this->not_existing_files[] = $filename;
                }

              }
            }

            return false;
        }

        //called when requiring a file url - will always give the precedence to the child-theme file if it exists
        //then to the theme root
        function czr_fn_get_theme_file_url( $url_suffix ) {
            $url_prefixes   = array_unique( apply_filters( 'czr_include_paths'     , array( '' ) ) );
            $roots          = array_unique( apply_filters( 'czr_include_roots_path', array( CZR_BASE_CHILD, CZR_BASE ) ) );
            $roots_urls     = array_unique( apply_filters( 'czr_include_roots_url' , array( CZR_BASE_URL_CHILD, CZR_BASE_URL ) ) );

            $combined_roots = array_combine( $roots, $roots_urls );

            foreach ( $roots as $root ) {

              foreach ( $url_prefixes as $url_prefix ) {

                $filename     = $root . $url_prefix . $url_suffix;
                $_exists      = in_array( $filename, $this->existing_files );
                $_exists_not  = in_array( $filename, $this->not_existing_files );

                if ( !$_exists_not && ( $_exists || file_exists( $filename ) ) ) {

                  //cache file existence
                  if ( !$_exists ) {
                    $this->existing_files[] = $filename;
                  }

                  return array_key_exists( $root, $combined_roots) ? $combined_roots[ $root ] . $url_prefix . $url_suffix : false;

                }
                else if ( !$_exists_not ) {
                  //cache file not existence
                  $this->not_existing_files[] = $filename;
                }

              }

            }

            return false;
        }


        //requires a file only if exists
        function czr_fn_require_once( $path_suffix ) {
            if ( false !== $filename = $this -> czr_fn_get_theme_file( $path_suffix ) )
              require_once( $filename );

            return (bool) $filename;
        }


        /*
        * Stores the current model in the class current_model stack
        * called by the View class before requiring the view template
        * @param $model
        */
        function czr_fn_set_current_model( $model ) {
            $this -> current_model[ $model -> id ] = &$model;
        }


        /*
        * Pops the current model from the current_model stack
        * called by the View class after the view template has been required/rendered
        */
        function czr_fn_reset_current_model() {
            array_pop( $this -> current_model );
        }


         /*
        * An handly function to get a current model property
        * @param $property (string), the property to get
        * @param $args (array) - optional, an ordered list of params to pass to the current model property getter (if defined)
        */
        function czr_fn_get( $property, $model_id = null, $args = array() ) {
            $current_model = false;
            if ( ! is_null($model_id) ) {
              if ( czr_fn_is_registered($model_id) )
                $current_model = czr_fn_get_model_instance( $model_id );
            } else {
              $current_model = end( $this -> current_model );
            }
            return is_object($current_model) ? $current_model -> czr_fn_get_property( $property, $args ) : false;
        }

        /*
        * An handly function to print a current model property (wrapper for czr_fn_get)
        * @param $property (string), the property to get
        * @param $args (array) - optional, an ordered list of params to pass to the current model property getter (if defined)
        */
        function czr_fn_echo( $property, $model_id = null, $args = array() ) {
            $prop_value = czr_fn_get( $property, $model_id, $args );
            /*
            * is_array returns false if an array is empty:
            * in that case we have to transform it in false or ''
            */
            $prop_value = $prop_value && is_array( $prop_value ) ? czr_fn_stringify_array( $prop_value ) : $prop_value;
            echo empty( $prop_value ) ? '' : $prop_value;
        }

        /*
        * An handly function to print the content wrapper class
        */
        function czr_fn_column_content_wrapper_class() {
            echo czr_fn_stringify_array( czr_fn_get_column_content_wrapper_class() );
        }

        /*
        * An handly function to print the main container class
        */
        function czr_fn_main_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_main_container_class() );
        }

        /*
        * An handly function to print the article containerr class
        */
        function czr_fn_article_container_class() {
            echo czr_fn_stringify_array( czr_fn_get_article_container_class() );
        }


  }//end of class
endif;//endif;

//Fire

/**
 * @since 4.0
 * @return object CZR Instance
 */
if ( ! function_exists( 'CZR' ) ) {
    function CZR() {
      return CZR___::czr_fn_instance();
    }
}

//require init-pro if it exists
if ( file_exists( get_template_directory() . '/core/init-pro.php' ) )
  require_once( get_template_directory() . '/core/init-pro.php' );

// Fire Customizr
CZR();