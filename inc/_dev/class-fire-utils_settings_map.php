<?php
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

      public $customizer_map = array();

      private $is_wp_version_before_4_0;
      private $_is_settings_map_available;

      function __construct () {

            self::$instance =& $this;
            //declare a private property to check wp version >= 4.0
            global $wp_version;
            $this -> is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;

            //Be sure that all these files exist
            //I'm mostly thinking about server caching issues when users update
            //This is just an edge case, but let's be pedantic

            //require all the files needed by the new settings map - they contain functions used in core/utils/class-fire-utils_settings_map.php
            if ( $_is_settings_map_available = file_exists( TC_BASE . 'core/functions.php' ) ) {
                  require_once( TC_BASE . 'core/functions.php' );
            }

            if ( $_is_settings_map_available && $_is_settings_map_available = file_exists( TC_BASE . 'core/utils/class-fire-utils_options.php' ) ) {
                  require_once( TC_BASE . 'core/utils/class-fire-utils_options.php' );
            }

            if ( $_is_settings_map_available && $_is_settings_map_available = file_exists( TC_BASE . 'core/utils/class-fire-utils.php' ) ) {
                  require_once( TC_BASE . 'core/utils/class-fire-utils.php' );
            }
            //require core utils settings map
            if ( $_is_settings_map_available && $_is_settings_map_available = file_exists( TC_BASE . 'core/utils/class-fire-utils_settings_map.php' ) ) {
                  require_once( TC_BASE . 'core/utils/class-fire-utils_settings_map.php' );
            }

            $this->_is_settings_map_available = $_is_settings_map_available;

      }//end of construct



      /**
      * Defines sections, settings and function of customizer and return and array
      * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
      *
      * @package Customizr
      * @since Customizr 3.0
      * TODO: unify this
      */
      public function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {

            //when not all the deps have been satisfied return an empty array
            //this will produce only php warnings
            //TODO: consider to raise an exception
            if ( empty( $this->_is_settings_map_available ) ) {
                  return array();
            }


            //Hook callbacks are defined in core/utils/class-fire-utils_settings_map.php
            if ( ! empty( $this -> customizer_map ) ) {
                  $_customizer_map = $this -> customizer_map;
            }
            else {

                  //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
                  add_filter( 'tc_add_panel_map'            , 'czr_fn_popul_panels_map' );
                  add_filter( 'tc_remove_section_map'       , 'czr_fn_popul_remove_section_map' );
                  //theme switcher's enabled when user opened the customizer from the theme's page
                  add_filter( 'tc_remove_section_map'       , 'czr_fn_set_theme_switcher_visibility' );
                  add_filter( 'tc_add_section_map'          , 'czr_fn_popul_section_map' );
                  //add controls to the map
                  add_filter( 'tc_add_setting_control_map'  , 'czr_fn_popul_setting_control_map', 10, 2 );

                  //FILTER SPECIFIC SETTING-CONTROL MAPS
                  //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
                  add_filter( 'czr_fn_front_page_option_map' ,'czr_fn_generates_featured_pages', 10, 2 );


                  //MAYBE FORCE REMOVE SECTIONS (e.g. CUSTOM CSS section for wp >= 4.7 )
                  add_filter( 'tc_add_section_map'           , 'czr_fn_force_remove_section_map' );

                  /* CZR_4 compat */
                  /* ADD SPECIFIC SECTION SETTINGS */
                  //add controls to the map
                  add_filter( 'tc_add_section_map'                , array( $this, 'czr_fn_popul_section_map' ) );
                  add_filter( 'tc_add_setting_control_map'        , array( $this, 'czr_fn_popul_setting_control_map' ), 0, 2 );


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

                'czr_fn_icons_option_map', //Removed in c4
                'czr_fn_responsive_option_map', //Removed in c4, handled with Bootstrap css

            );

            foreach ( $_settings_sections as $_section_cb ) {
                  if ( ! method_exists( $this , $_section_cb ) )
                        continue;

                  //applies a filter to each section settings map => allows plugins (featured pages for ex.) to add/remove settings
                  //each section map takes one boolean param : $get_default
                  $_section_map = apply_filters(
                        $_section_cb,
                        call_user_func( array( $this, $_section_cb ), $get_default )
                  );

                  if ( ! is_array( $_section_map) )
                        continue;

                  $_new_map = array_merge( $_new_map, $_section_map );
            }//foreach

            /***** FILTER SPECIFIC SETTING-CONTROL MAPS defined in c4 ****/
            //alter czr4 settings sections
            $_alter_settings_sections = array(
                  //GLOBAL SETTINGS
                  'czr_fn_logo_favicon_option_map',
                  'czr_fn_skin_option_map',
                  'czr_fn_links_option_map',
                  'czr_fn_images_option_map',
                  //HEADER
                  'czr_fn_header_design_option_map',
                  'czr_fn_navigation_option_map',
                  //CONTENT
                  'czr_fn_post_metas_option_map',
                  'czr_fn_post_list_option_map',
                  'czr_fn_comment_option_map',
                  //SIDEBARS
                  'czr_fn_sidebars_option_map',
                  //FOOTER
                  'czr_fn_footer_global_settings_option_map'
            );

            foreach ( $_alter_settings_sections as $_alter_section_cb ) {
                  if ( ! method_exists( $this , $_alter_section_cb ) )
                        continue;
                  add_filter( $_alter_section_cb, array( $this, $_alter_section_cb ), 10, 2 );
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
      function czr_fn_logo_favicon_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            $_to_add = array(
                  //favicon
                  'tc_fav_upload' => array(
                                    'control'   =>  'CZR_Customize_Upload_Control' ,
                                    'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                                    'title'     => __( 'FAVICON' , 'customizr'),
                                    'section'   =>  'logo_sec' ,
                                    'type'      => 'tc_upload',
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                  )
            );

            return array_merge( $_map, $_to_add );
      }




      /*-----------------------------------------------------------------------------------------------------
                                        SKIN SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_skin_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_skin_color',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }


            $_to_add = array(

                  'tc_skin'     => array(
                              'default'   => czr_fn_user_started_before_version( '3.4.32' , '1.2.31') ? 'blue3.css' : 'grey.css',
                              'control'   => 'CZR_controls' ,
                              'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                              'section'   =>  'skins_sec' ,
                              'type'      =>  'select' ,
                              'choices'    =>  $get_default ? null : czr_fn_build_skin_list(),
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
                  ),
            );

            return array_merge( $_map, $_to_add );
      }


      /*-----------------------------------------------------------------------------------------------------
                                     LINKS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_links_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            $_to_add = array(
                  'tc_link_hover_effect'  =>  array(
                                'default'       => 1,
                                'control'     => 'CZR_controls' ,
                                'label'         => __( "Fade effect on link hover" , "customizr" ),
                                'section'       => 'links_sec' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
                  ),
            );

            return array_merge( $_map, $_to_add );
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
                                    'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display a page icon next to the page title" , "customizr" ),
                                    'section'       => 'titles_icons_sec' ,
                                    'type'          => 'checkbox',
                                    'priority'      => 20,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_show_post_title_icon'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.0', '1.0.11' ) ? 1 : 0,
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
                                    'default'       => czr_fn_user_started_before_version( '3.3.0' , '1.0.11' ) ? 1 : 0,
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
      function czr_fn_images_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

                  'tc_fancybox_autoscale' =>  array(
                                    'default'       => 1,
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                                    'section'     => 'images_sec' ,
                                    'type'        => 'checkbox' ,
                                    'priority'    => 2,
                                    'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
                  ),

                 'tc_display_slide_loader'  =>  array(
                                    'default'       => 1,
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( "Sliders : display on loading icon before rendering the slides" , "customizr" ),
                                    'section'     => 'images_sec' ,
                                    'type'        => 'checkbox' ,
                                    'priority'    => 15,
                                    'notice'    => __( 'When checked, this option displays a loading icon when the slides are being setup.' , 'customizr' ),
                  ),

            );

            return array_merge( $_map, $_to_add );
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
      function czr_fn_header_design_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_header_topbar',
                  'tc_social_in_topnav',
                  'tc_search_in_header',
                  'tc_header_skin',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            //to add
            $_to_add  = array(
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

                  'tc_display_boxed_navbar'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.13', '1.0.18' ) ? 1 : 0,
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( "Display menu in a box" , "customizr" ),
                                    'section'       => 'header_layout_sec' ,
                                    'type'          => 'checkbox' ,
                                    'priority'      => 25,
                                    'transport'     => 'postMessage',
                                    'notice'        => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
                  ),
            );

            return array_merge( $_map, $_to_add );

      }



      /*-----------------------------------------------------------------------------------------------------
                          NAVIGATION SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_navigation_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to add
            $_to_add  = array(
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
                  //override
                  'tc_menu_position'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'pull-menu-left' : 'pull-menu-right',
                                    'control'       => 'CZR_controls' ,
                                    'label'         => __( 'Menu position (for "main" menu)' , "customizr" ),
                                    'section'       => 'nav' ,
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                            'pull-menu-center'    => __( 'Menu centered' , 'customizr' ),
                                            'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                    ),
                                    'priority'      => 50,
                                    'transport'     => 'postMessage',
                                    'notice'        => sprintf( '%1$s <a href="%2$s">%3$s</a>.',
                                        __("Note : the menu centered position is available only when" , "customizr"),
                                        "javascript:wp.customize.section('header_layout_sec').focus();",
                                        __("the logo is centered", "customizr")
                                    )
                  ),
                  //override
                  'tc_second_menu_position'  =>  array(
                                    'default'       => 'pull-menu-left',
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Secondary (horizontal) menu design' , 'customizr'),
                                    'label'         => __( 'Menu position (for the horizontal menu)' , "customizr" ),
                                    'section'       => 'nav' ,
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                            'pull-menu-center'    => __( 'Menu centered' , 'customizr' ),
                                            'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                    ),
                                    'priority'      => 55,
                                    'transport'     => 'postMessage',
                                    'notice'        => sprintf( '%1$s <a href="%2$s">%3$s</a>.',
                                        __("Note : the menu centered position is available only when" , "customizr"),
                                        "javascript:wp.customize.section('header_layout_sec').focus();",
                                        __("the logo is centered", "customizr")
                                    )
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
                  ),

            );

            return array_merge( $_map, $_to_add );
      }


      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : CONTENT
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                    POST METAS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_post_metas_option_map( $_map, $get_default = null ){

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

                  /* Post metas design has been removed in c4 */
                  'tc_post_metas_design'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'buttons' : 'no-buttons',
                                    'control'       => 'CZR_controls' ,
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
                  'tc_post_metas_update_date_format'  =>  array(
                                    'default'       => 'days',
                                    'control'       => 'CZR_controls',
                                    'label'         => __( "Select the last update format" , "customizr" ),
                                    'section'       => 'post_metas_sec',
                                    'type'          =>  'select' ,
                                    'choices'       => array(
                                            'days'     => __( 'No. of days since last update' , 'customizr' ),
                                            'date'     => __( 'Date of the last update' , 'customizr' )
                                    ),
                                    'priority'      => 55
                  ),
                  /* Update notice in title has been completely removed in c4*/
                  'tc_post_metas_update_notice_in_title'  =>  array(
                                    'default'       => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 1 : 0,
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
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
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

            $_map = array_merge( $_map, $_to_add );

            //add notice to the update date option
            if ( isset( $_map[ 'tc_show_post_metas_update_date' ] ) )
                  $_map[ 'tc_show_post_metas_update_date' ]['notice'] = __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' );

            return $_map;

      }



      /*-----------------------------------------------------------------------------------------------------
                                    POST LISTS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_post_list_option_map( $_map, $get_default = null ) {


            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to unset
            $_to_unset = array(
                  'tc_post_list_thumb_placeholder',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }



            global $wp_version;

            //to add
            $_to_add  = array(
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
                  /* Used only for the standard grid: Removed in c4 */
                  'tc_post_list_default_thumb'  => array(
                                    'control'   =>  version_compare( $wp_version, '4.3', '>=' ) ? 'CZR_Customize_Cropped_Image_Control' : 'CZR_Customize_Upload_Control',
                                    'label'         => __( 'Upload a default thumbnail' , 'customizr' ),
                                    'section'   =>  'post_lists_sec' ,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
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

                  'tc_post_list_thumb_height' => array(
                                    'default'       => 250,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'control'   => 'CZR_controls' ,
                                    'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                    'section'     => 'post_lists_sec' ,
                                    'type'        => 'number' ,
                                    'step'      => 1,
                                    'min'     => 0,
                                    'priority'      => 80,
                                    'transport'   => 'postMessage'
                  ),
                  'tc_grid_thumb_height' => array(
                                    'default'       => 350,
                                    'sanitize_callback' => 'czr_fn_sanitize_number',
                                    'control'       => 'CZR_controls' ,
                                    'title'         => __( 'Thumbnails max height for the grid layout' , 'customizr' ),
                                    'label'         => __( "Set the post grid thumbnail's max height in pixels" , 'customizr' ),
                                    'section'       => 'post_lists_sec' ,
                                    'type'          => 'number' ,
                                    'step'          => 1,
                                    'min'           => 0,
                                    'priority'      => 65
                                    //'transport'   => 'postMessage'
                  ),

            );

            $_map = array_merge( $_map, $_to_add );

            //Add thumb shape
            $_map['tc_post_list_thumb_shape']['choices'] = array_merge( $_map['tc_post_list_thumb_shape']['choices'], array(
                                    'squared'               => __( 'Squared, expand on hover' , 'customizr'),
                                    'squared-expanded'      => __( 'Squared, no expansion' , 'customizr'),
                                    'rectangular'           => __( 'Rectangular with no effect' , 'customizr'  ),
                                    'rectangular-blurred'   => __( 'Rectangular with blur effect on hover' , 'customizr'  ),
                                    'rectangular-unblurred' => __( 'Rectangular with unblur effect on hover' , 'customizr')
            ) );

            //Remove czr4 only thumb shape
            unset( $_map['tc_post_list_thumb_shape']['choices']['regular'] );

            //Add thumb position
            $_map['tc_post_list_thumb_position']['choices'] = array_merge( $_map['tc_post_list_thumb_position']['choices'], array(
                                    'top'     => __( 'Top' , 'customizr' ),
                                    'bottom'    => __( 'Bottom' , 'customizr' ),
            ) );

            //Remove post list plain grid choice
            unset( $_map['tc_post_list_grid']['choices']['plain' ] );

            return $_map;
      }


      /*-----------------------------------------------------------------------------------------------------
                                     COMMENTS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_comment_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(
                  /* Removed in c4 */
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
                  /* Removed in c4 */
                  'tc_comment_bubble_color_type' => array(
                                  'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? 'custom' : 'skin',
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
                  /* Removed in c4 */
                  'tc_comment_bubble_color' => array(
                                  'default'     => czr_fn_user_started_before_version( '3.3.2' , '1.0.11' ) ? '#F00' : czr_fn_get_skin_color(),
                                  'control'     => 'WP_Customize_Color_Control',
                                  'label'       => __( 'Comments bubble color' , 'customizr' ),
                                  'section'     => 'comments_sec',
                                  'type'        =>  'color' ,
                                  'priority'    => 30,
                                  'sanitize_callback'    => 'czr_fn_sanitize_hex_color',
                                  'sanitize_js_callback' => 'maybe_hash_hex_color',
                                  'transport'   => 'postMessage'
                  ),

            );

            return array_merge( $_map, $_to_add );
      }


      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : SIDEBARS
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                     SIDEBAR SOCIAL LINKS SETTINGS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_sidebars_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }

            //to add
            $_to_add  = array(

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

            return array_merge( $_map, $_to_add );
      }



      /******************************************************************************************************
      *******************************************************************************************************
      * PANEL : FOOTER
      *******************************************************************************************************
      ******************************************************************************************************/
      /*-----------------------------------------------------------------------------------------------------
                                     FOOTER GLOBAL SETTINGS SECTION
      ------------------------------------------------------------------------------------------------------*/
      function czr_fn_footer_global_settings_option_map( $_map, $get_default = null ) {

            if ( !is_array( $_map ) || empty( $_map ) ) {
                  return $_map;
            }


            //to unset
            $_to_unset = array(
                  'tc_footer_skin',
            );

            foreach ( $_to_unset as $key ) {
                  unset( $_map[ $key ] );
            }

            return $_map;
      }


      /***************************************************************
      * POPULATE SECTIONS
      ***************************************************************/
      /**
      * hook : tc_add_section_map
      */
      function czr_fn_popul_section_map( $_sections ) {

            $_old_sections = array(

                  /*---------------------------------------------------------------------------------------------
                  -> PANEL : GLOBAL SETTINGS
                  ----------------------------------------------------------------------------------------------*/
                  'titles_icons_sec'        => array(
                                      'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                                      'priority'    =>  $this->is_wp_version_before_4_0 ? 18 : 40,
                                      'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                                      'panel'   => 'tc-global-panel'
                  ),

                  /*---------------------------------------------------------------------------------------------
                  -> PANEL : SIDEBARS
                  ----------------------------------------------------------------------------------------------*/
                  'responsive_sec'           => array(
                                      'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                      'priority'    =>  20,
                                      'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                                      'panel'   => 'tc-sidebars-panel'
                  ),
            );

            return array_merge( $_old_sections, $_sections );
      }




      /* Sanitize callbacks */
      /**
       * adds sanitization callback funtion : url
       * @package Customizr
       * @since Customizr 1.1.4
       * //kept for backward compatibility
       */
      function czr_fn_sanitize_url( $value) {
        $value = esc_url( $value);
        return $value;
      }

      /**
       * adds sanitization callback funtion : email
       * @package Customizr
       * @since Customizr 3.4.11
       * //kept for backward compatibility
       */
      function czr_fn_sanitize_email( $value) {
        return sanitize_email( $value );
      }

}//end of class
endif;

?>