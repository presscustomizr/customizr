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

        //registers the header's model
        add_action('header_model_alive' , array(self::$instance, 'tc_register_header_map') );

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
              array('core/utils' , 'utils_thumbnails'),//thumbnails helpers used almost everywhere
              array('core'       , 'resources'),//loads front stylesheets (skins) and javascripts
              array('core'       , 'widgets'),//widget factory
              array('core'       , 'placeholders'),//front end placeholders ajax actions for widgets, menus.... Must be fired if is_admin === true to allow ajax actions.
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
    function tc_register_model_map( $_map = array() ) {
      $_to_register =  ( empty( $_map ) || ! is_array($_map) ) ? $this -> tc_get_model_map() : $_map;

      foreach ( $_to_register as $model ) {
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
            array(
              'hook' => '__rooot__',
              'template' => 'rooot',
              'element_tag' => false
            ),
            array(
              'hook' => '__html__',
              'template' => 'header/head',
              'element_tag' => 'head'
            ),
            array(
              'hook' => 'wp_head' ,
              'template' => 'header/favicon',
              'element_tag' => false
            ),
            array(
              'hook' => '__html__',
              'template' => 'body',
              'priority' => 20,
              'element_tag' => false
            ),
            array(
              'hook' => '__body__',
              'template' => 'page_wrapper',
              'priority' => 20,
              'element_id' => 'tc-page-wrap'
            ),


          /*********************************************
          * HEADER
          *********************************************/
          array(
            'hook'        => '__page_wrapper__',
            'template'    => 'header/header',
            'element_tag' => 'header',
            'element_attributes' => 'role="banner"'
          ),




          /*********************************************
          * CONTENT
          *********************************************/
          /* MAIN WRAPPERS */
          array( 'hook' => '__page_wrapper__', 'template' => 'content/main_wrapper', 'priority' => 20, 'element_class' => apply_filters( 'tc_main_wrapper_classes' , array('container') ), 'element_id' => 'main-wrapper' ),
          array( 'hook' => '__main_wrapper__', 'template' => 'content/main_container', 'priority' => 20 ),
          //breadcrumb
          array( 'hook' => '__main_wrapper__', 'template' => 'modules/breadcrumb', 'priority' => 10, 'element_tag' => false ),

          /* LEFT SIDEBAR */
          array( 'hook' => '__main_container__', 'id' => 'left_sidebar', 'template' => 'modules/widget_area_wrapper', 'priority' => 10, 'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' ) ),
            //left sidebar content
            //socialblock in left sidebar
          array( 'hook' => '__widget_area_left__', 'template' => 'modules/social_block', 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'content/sidebar_social_block' ) ),
            array( 'hook' => '__widget_area_left__', 'id' => 'left', 'template' => 'modules/widget_area' ),
          /* CONTENT WRAPPER id="content" class="{article container class }"*/
          array( 'hook' => '__main_container__', 'template' => 'content/content_wrapper', 'priority' => 20 ),

          /* RIGHT SIDEBAR */
          array( 'hook' => '__main_container__', 'id' => 'right_sidebar', 'template' => 'modules/widget_area_wrapper', 'priority' => 30, 'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' ) ),
          //right sidebar content
          //socialblock in right sidebar
          array( 'hook' => '__widget_area_right__', 'template' => 'modules/social_block', 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'content/sidebar_social_block' ) ),
            array( 'hook' => '__widget_area_right__', 'id' => 'right', 'template' => 'modules/widget_area' ),

          /* OUTSIDE THE LOOP */
          //404
          array( 'hook' => '__content__', 'id' => '404', 'template' => 'content/content_404', 'model_class' => array( 'parent' => 'content/article', 'name' => 'content/404'), 'element_tag' => false ),
          //no results
          array( 'hook' => '__content__', 'id' => 'no_results', 'template' => 'content/content_no_results', 'model_class' => array( 'parent' => 'content/article', 'name' => 'content/no_results'), 'element_tag' => false ),

          //Headings: before the loop (for list of posts, like blog, category, archives ...)
          array( 'hook' => '__content__', 'template' => 'content/headings', 'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/posts_list_headings'), 'id' => 'posts_list_headings' ),
          array( 'hook' => '__headings_posts_list__', 'template' => 'content/posts_list_title', 'priority' => 10 ),
          //search results title
          array( 'hook' => '__headings_posts_list__', 'template' => 'content/posts_list_search_title', 'priority' => 10, 'model_class' => array( 'parent' => 'content/posts_list_title', 'name' => 'content/posts_list_search_title' ) ),
          array( 'hook' => '__headings_posts_list__', 'template' => 'content/posts_list_description', 'priority' => 20 ),
          //author description
          array( 'hook' => '__headings_posts_list__', 'id' => 'author_description', 'template' => 'content/author_info', 'element_tag' => false ,'priority' => 20 ),

          /* GENERIC LOOP */
          array( 'hook' => '__content__', 'id' => 'main_loop', 'template' => 'loop', 'element_tag' => false, 'priority' => 20 ),

          /*** ALTERNATE POST LIST ***/
          array( 'hook' => 'in_main_loop', 'template' => 'content/post_list_wrapper', 'priority' => 10, 'element_tag' => false, 'controller' => 'post_list', 'model_class' => array( 'parent' => 'content/article', 'name' => 'content/post_list_wrapper' ) ),

          //content
          //post content/excerpt
          array( 'hook' => '__post_list_content__', 'template' => 'content/post_list_content', 'id' => 'content', 'element_tag' => 'section' ),
          //thumbs
          array( 'hook' => '__post_list_thumb__', 'template' => 'content/post_list_thumbnail', 'id' => 'post_list_standard_thumb', 'element_tag' => false ),
          //the recangular thumb has a different model + a slighty different template
          array( 'hook' => '__post_list_thumb__', 'template' => 'content/rectangular_thumbnail', 'id' => 'post_list_rectangular_thumb', 'element_tag' => false, 'model_class' => array( 'parent' => 'content/post_list_thumbnail', 'name' => 'content/post_list_rectangular_thumbnail') ),

          //post in post lists headings
          array( 'hook' => 'before_render_view_inner_content', 'template' => 'content/headings', 'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post_page_headings' ) ),
            array( 'hook' => '__headings_content__', 'template' => 'content/post_page_title', 'id' => 'post_list_title', 'element_tag' => 'h2' ),
          /*** ALTERNATE POST LIST END ***/

          //page & post
          array( 'hook' => 'in_main_loop', 'template' => 'content/article', 'priority' => 10, 'element_tag' => false, 'element_class' => 'row-fluid', 'id' => 'singular_article' ),
          //page content
          array( 'hook' => '__article__', 'template' => 'content/post_page_content', 'id' => 'page' ),
            //page headings
            array( 'hook' => '__article__', 'id' => 'singular_headings', 'template' => 'content/headings', 'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post_page_headings' ), 'priority' => 10 ),
          //post content
          array( 'hook' => '__article__', 'template' => 'content/post_page_content', 'id' => 'post', 'element_tag' => 'section', 'model_class' => array( 'parent' => 'content/post_page_content', 'name' => 'content/post_content' ) ),
            //post headings
            array( 'hook' => 'before_render_view_post', 'id' => 'post_headings', 'template' => 'content/headings', 'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post_page_headings' ) ),
            //post footer (wrapper of the author_info)
            array( 'hook' => 'after_render_view_post', 'id' => 'post_footer', 'template' => 'content/author_info', 'element_tag' => 'footer', 'element_class' => 'entry-meta'),
          //attachment
          array( 'hook' => '__article__', 'template' => 'content/attachment', 'id' => 'attachment', 'model_class' => array( 'parent' => 'content/post_page_content', 'name' => 'content/attachment_content' ) ),
          //post and page titles in singular context
            array( 'hook' => '__headings_content__', 'template' => 'content/singular_title', 'model_class' => 'content/post_page_title', 'element_tag' => 'h1' ),

            //post thumbnail
            array( 'hook' => 'before_render_view_post', 'template' => 'content/rectangular_thumbnail', 'id' => 'post_thumbnail', 'element_tag' => 'section', 'model_class' => array( 'parent' => 'content/post_list_thumbnail', 'name' => 'content/post_thumbnail') ),

          //Post metas in the headings
          //the default class/template is for the buttons type
            array( 'hook' => '__headings_content__', 'template' => 'content/post_metas', 'element_class' => 'entry-meta', 'priority' => 20, 'id' => 'post_metas_button' ),
          //the text meta one uses a different template
            array( 'hook' => '__headings_content__', 'template' => 'content/post_metas_text', 'element_class' => 'entry-meta', 'priority' => 20, 'model_class' => array( 'parent' => 'content/post_metas', 'name' => 'content/post_metas_text' ) ),
          //attachment post mestas
            array( 'hook' => '__headings_content__', 'id' => 'post_metas_attachment', 'template' => 'content/attachment_post_metas', 'element_class' => 'entry-meta', 'priority' => 20, 'model_class' => array( 'parent' => 'content/post_metas', 'name' => 'content/attachment_post_metas' ) ),

          /* TODO: LINKS IN POST METAS FOR POSTS WITH NO TITLE ( needs to access the definition of the posts with no headings )...*/


          /* Comments */
          /* comment list */
          array( 'hook' => '__content__', 'template' => 'content/comments', 'element_class' => 'comments-area', 'element_id' => 'comments', 'priority' => '20'),
            array( 'hook' => '__comments__', 'template' => 'content/comment_block_title', 'priority' => '10', 'element_tag' => false),
            array( 'hook' => '__comments__', 'template' => 'content/comment_list', 'element_tag' => 'ul', 'element_class' => 'commentlist', 'priority' => '20'),
              array( 'hook' => '__comment_loop__', 'template' => 'content/comment',  'id' => 'comment', 'element_tag' => false ),
              array( 'hook' => '__comment_loop__', 'template' => 'content/tracepingback',  'id' => 'traceback', 'element_tag' => false ),
            array( 'hook' => '__comments__', 'template' => 'content/comment_navigation', 'priority' => '30'),

          /* end Comments */
          /* Post navigation */
          array( 'hook' => '__content__', 'template' => 'content/post_navigation_singular', 'element_tag' => 'nav', 'element_id' => 'nav-below', 'model_class' => array( 'parent' => 'content/post_navigation', 'name' => 'content/post_navigation_singular' ), 'priority' => 40 ),
          array( 'hook' => '__content__', 'template' => 'content/post_navigation_posts', 'element_tag' => 'nav', 'element_id' => 'nav-below', 'model_class' => array( 'parent' => 'content/post_navigation', 'name' => 'content/post_navigation_posts' ), 'priority' => 40 ),
            //singular links'
            array( 'hook' => 'post_navigation_singular', 'template' => 'content/post_navigation_links', 'model_class' => array( 'parent' => 'content/post_navigation_links', 'name' => 'content/post_navigation_links_singular'), 'id' => 'post_navigation_links_singular'),
            //posts links
            array( 'hook' => 'post_navigation_posts', 'template' => 'content/post_navigation_links', 'model_class' => array( 'parent' => 'content/post_navigation_links', 'name' => 'content/post_navigation_links_posts'), 'id' => 'post_navigation_links_posts' ),
          /* end post navigation */


          /*********************************************
          * FOOTER
          *********************************************/
          //sticky footer
          array( 'hook' => 'after_render_view_main_container', 'template' => 'footer/footer_push', 'priority' => 100, 'element_tag' => false ),

          array( 'hook' => '__page_wrapper__', 'template' => 'footer/footer', 'priority' => 30, 'element_tag' => 'footer' ),

          //a post grid displayed in any content
  //        array( 'hook' => '__footer__', 'template' => 'modules/grid-wrapper', 'priority' => 20 ),
  //        array( 'hook' => 'in_grid_wrapper', 'id' => 'secondary_loop', 'template' => 'loop', 'query' => array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'ignore_sticky_posts' => 1 ) ),
  //        array( 'hook' => 'in_secondary_loop', 'template' => 'modules/grid-item' ),

          //widget area in footer
          array( 'hook' => '__footer__', 'id' => 'footer_widgets_wrapper', 'template' => 'modules/widget_area_wrapper', 'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'footer/footer_widgets_area_wrapper' ) ),

          //footer one wrapper and widget area
          array( 'hook' => '__widget_area_footer__', 'id' => 'footer_one', 'priority' => '10', 'template' => 'modules/widget_area', 'element_id' => 'footer-one' , 'model_class' => 'footer/footer_widget_area_wrapper'),

          //footer two wrapper and widget area
          array( 'hook' => '__widget_area_footer__', 'id' => 'footer_two', 'priority' => '20', 'template' => 'modules/widget_area', 'model_class' => 'footer/footer_widget_area_wrapper', 'element_id' => 'footer-two' ),

          //footer three wrapper and widget area
          array( 'hook' => '__widget_area_footer__', 'id' => 'footer_three', 'priority' => '20', 'template' => 'modules/widget_area', 'model_class' => 'footer/footer_widget_area_wrapper', 'element_id' => 'footer-three' ),

          //colophon
          array( 'hook' => '__footer__', 'template' => 'footer/colophon_standard', 'model_class' => array( 'parent' => 'footer/colophon', 'name' => 'footer/colophon_standard'), 'priority' => 100 ),
          //TODO: COLOPHON BLOCKS ORDER IS RTL DEPENDANT
          //footer social
          array( 'hook' => '__colophon_one__', 'template' => 'modules/social_block', 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'footer/footer_social_block' ) ),
          //footer credits
          array( 'hook' => '__colophon_two__', 'template' => 'footer/footer_credits' ),
          //footer colophon btt link
          array( 'hook' => '__colophon_three__', 'template' => 'footer/footer_btt' ),

          //btt arrow
          array( 'hook' => 'after_render_view_page_wrapper', 'template' => 'footer/btt_arrow', 'element_class' => 'tc-btt-wrapper'),
        )
      );
    }



    function tc_register_header_map() {
      $_header_map = array(
          //LOGO
          array( 'hook' => '__header__', 'template' => 'header/logo_wrapper' ),
          array( 'hook' => '__logo_wrapper__', 'template' => 'header/logo', 'element_tag' => false ),
          array( 'hook' => '__logo_wrapper__', 'id' => 'sticky_logo', 'template' => 'header/logo' , 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/sticky_logo'), 'element_tag' => false ),

          //TITLE
          array( 'hook' => '__header__', 'template' => 'header/title'  ),

          //MOBILE TAGLINE
          array( 'hook' => '__header__', 'template' => 'header/mobile_tagline', 'id' => 'mobile_tagline', 'priority' => 20, 'model_class' => array( 'parent' => 'header/tagline', 'name' => 'header/mobile_tagline') ),

          //NAVBAR
          array( 'hook' => '__header__', 'template' => 'header/navbar_wrapper', 'priority' => 20 ),

          //socialblock in navbar
          array( 'hook' => '__navbar__', 'template' => 'modules/social_block', 'priority' => is_rtl() ? 20 : 10, 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'header/header_social_block' ) ),
          //tagline in navbar
          array( 'hook' => '__navbar__', 'template' => 'header/tagline', 'priority' => is_rtl() ? 10 : 20 ),
          //menu in navbar
          array( 'hook' => '__navbar__', 'id' => 'navbar_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),
          //secondary
          array( 'hook' => '__navbar__', 'id' => 'navbar_secondary_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/second_menu' ) ),
          //responsive menu button
          array( 'hook' => '__navbar__', 'id' => 'mobile_menu_button', 'template' => 'header/menu_button', 'priority' => 40 ),
          //sidenav navbar menu button
          array( 'hook' => '__navbar__', 'id' => 'sidenav_navbar_menu_button', 'template' => 'header/menu_button', 'priority' => 25, 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),

          //RESET MARGIN TOP (for sticky header)
          array( 'hook' => 'after_render_view_header', 'template' => 'header/reset_margin_top', 'priority' => 0, 'element_tag' => false ),

          //SIDENAV
          array( 'hook' => 'before_render_view_page_wrapper', 'template' => 'header/sidenav', 'element_tag' => 'nav', 'element_id' => 'tc-sn', 'element_class' => apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) ),
          //menu button
          array( 'hook' => '__sidenav__', 'id' => 'sidenav_menu_button', 'template' => 'header/menu_button', 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),
          //menu
          array( 'hook' => '__sidenav__', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/sidenav_menu' ) )
      );
      $this -> tc_register_model_map( $_header_map );
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
      if ( ! isset( $this -> is_customizing ) )
        //checks if is customizing : two contexts, admin and front (preview frame)
        $this -> is_customizing = in_array( 1, array(
          $this -> tc_is_customize_left_panel(),
          $this -> tc_is_customize_preview_frame(),
         $this -> tc_doing_customizer_ajax()
        ) );
      return $this -> is_customizing;
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
