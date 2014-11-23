<?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_utils_settings_map' ) ) :
  class TC_utils_settings_map {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;
      private $is_wp_version_before_4_0;

      function __construct () {
          self::$instance =& $this;

          //update remove section map, since 3.2.0
          add_filter ( 'tc_remove_section_map'                , array( $this ,  'tc_update_remove_sections') );
          //update section map, since 3.2.0
          add_filter ( 'tc_add_section_map'                   , array( $this ,  'tc_update_section_map') );
          //update setting_control_map
          add_filter ( 'tc_add_setting_control_map'           , array( $this ,  'tc_update_setting_control_map'), 100 );
          //declare a private property to check wp version >= 4.0
          global $wp_version;
          $this -> is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;
      }//end of construct


    /**
    * Generates the featured pages options
    * 
    * @package Customizr
    * @since Customizr 3.0.15
    * 
    */
    private function tc_generates_featured_pages() {
      $default = array(
        'dropdown'  =>  array(
              'one'   => __( 'Home featured page one' , 'customizr' ),
              'two'   => __( 'Home featured page two' , 'customizr' ),
              'three' => __( 'Home featured page three' , 'customizr' )
        ),
        'text'    => array(
              'one'   => __( 'Featured text one (200 car. max)' , 'customizr' ),
              'two'   => __( 'Featured text two (200 car. max)' , 'customizr' ),
              'three' => __( 'Featured text three (200 car. max)' , 'customizr' )
        )
      );

      //declares some loop's vars and the settings array
      $priority       = 70;
      $incr         = 0;
      $fp_setting_control = array();

      //gets the featured pages id from init
      $fp_ids       = apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids);

      //dropdown field generator
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_theme_options[tc_featured_page_'. $id.']']    =  array(
                      'label'       => isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  sprintf( __('Custom featured page %1$s' , 'customizr' ) , $id ),
                      'section'     => 'tc_frontpage_settings' ,
                      'type'        => 'dropdown-pages' ,
                      'priority'      => $priority
                    );
        $incr += 10;
      }

      //text field generator
      $incr         = 10;
      foreach ( $fp_ids as $id ) {
        $priority = $priority + $incr;
        $fp_setting_control['tc_theme_options[tc_featured_text_' . $id . ']']   = array(
                      'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                      'transport'   => 'postMessage',
                      'control'   => 'TC_controls' ,
                      'label'       => isset($default['text'][$id]) ? $default['text'][$id] : sprintf( __('Featured text %1$s (200 car. max)' , 'customizr' ) , $id ),
                      'section'     => 'tc_frontpage_settings' ,
                      'type'        => 'textarea' ,
                      'notice'    => __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
                      'priority'      => $priority,
                    );
        $incr += 10;
      }

      return $fp_setting_control;
    }




    private function tc_generates_socials() {
      //gets the social network array
      $socials      = apply_filters( 'tc_default_socials' , TC_init::$instance -> socials );

      //declares some loop's vars and the settings array
      $priority       = 50;//start priority
      $incr         = 0;
      $socials_setting_control  = array();

      foreach ( $socials as $key => $data ) {
        $priority += $incr;
        $socials_setting_control['tc_theme_options[' . $key . ']']  = array(
                      'default'         => ( isset($data['default']) && !is_null($data['default']) ) ? $data['default'] : null ,
                      'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
                      'control'       => 'TC_controls' ,
                      'label'         => ( isset($data['option_label']) ) ? call_user_func( '__' , $data['option_label'] , 'customizr' ) : $key,
                      'section'       => 'tc_social_settings' ,
                      'type'          => 'url',
                      'priority'      => $priority,
                      'icon'          => "tc-icon-". str_replace('tc_', '', $key)
                    );
        $incr += 5;
      }

      return $socials_setting_control;
    }



    private function tc_get_skins($path) {
      //checks if path exists
      if ( !file_exists($path) )
        return;

      //gets the skins from init
      $default_skin_list    = TC_init::$instance -> skins;

      //declares the skin list array
      $skin_list        = array();

      //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
      $files            = scandir($path) ;
      foreach( $files as $file ) {
          //skips the minified
          if ( false !== strpos($file, '.min.') )
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
    private function tc_layout_choices() {
        $global_layout  = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
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
    private function tc_slider_choices() {
      $__options    =   get_option('tc_theme_options');
      $slider_names   =   isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

      $slider_choices = array( 
        0     =>  __( '&mdash; No slider &mdash;' , 'customizr' ),
        'demo'  =>  __( '&mdash; Demo Slider &mdash;' , 'customizr' )
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
    private function tc_build_skin_list() {
        $parent_skins   = $this -> tc_get_skins(TC_BASE .'inc/assets/css');
        $child_skins    = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css') ) ? $this -> tc_get_skins(TC_BASE_CHILD .'inc/assets/css') : array();
        $skin_list      = array_merge( $parent_skins , $child_skins );

      return apply_filters( 'tc_skin_list', $skin_list );
    }



    /**
    * Defines sections, settings and function of customizer and return and array
    * Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop) 
    * 
    * @package Customizr
    * @since Customizr 3.0 
    */
    function tc_customizer_map( $get_default = null ) {
      $add_panel = array(
        'add_panel'       =>   array(
              'tc-global-panel' => array(
                        'priority'       => 10,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Global settings' , 'customizr' ),
                        'description'    => __( "Global settings for the Customizr theme :skin, socials, links..." , 'customizr' )
              ),
              'tc-header-panel' => array(
                        'priority'       => 20,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Header : title, logo, menu, ...' , 'customizr' ),
                        'description'    => __( "Header settings for the Customizr theme." , 'customizr' )
              ),
              'tc-content-panel' => array(
                        'priority'       => 30,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Content : home, posts, ...' , 'customizr' ),
                        'description'    => __( "Content settings for the Customizr theme." , 'customizr' )
              ),
              'tc-footer-panel' => array(
                        'priority'       => 40,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Footer' , 'customizr' ),
                        'description'    => __( "Footer settings for the Customizr theme." , 'customizr' )
              ),
              'tc-advanced-panel' => array(
                        'priority'       => 1000,
                        'capability'     => 'edit_theme_options',
                        'title'          => __( 'Advanced options' , 'customizr' ),
                        'description'    => __( "Advanced settings for the Customizr theme." , 'customizr' )
              )
        )
      );//end of add_panel array
      $add_panel = apply_filters( 'tc_add_panel_map', $add_panel );

      //customizer option array
      $remove_section = array(
              'remove_section'       =>   array(
                          'background_image' ,
                          'static_front_page' ,
                          'colors'
              )
      );//end of remove_sections array
      $remove_section = apply_filters( 'tc_remove_section_map', $remove_section );



      $add_section = array(
              'add_section'       =>   array(
                      'tc_skins_settings'         => array(
                                        'title'     =>  __( 'Skin' , 'customizr' ),
                                        'priority'    =>  10,
                                        'description' =>  __( 'Select a skin for Customizr' , 'customizr' )
                      ),

                      'tc_logo_settings'          => array(
                                        'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                                        'priority'    =>  20,
                                        'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                      ),

                      'tc_frontpage_settings'       => array(
                                        'title'     =>  __( 'Front Page' , 'customizr' ),
                                        'priority'    =>  30,
                                        'description' =>  __( 'Set up front page options' , 'customizr' )
                      ),

                      'tc_layout_settings'        => array(
                                        'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                                        'priority'    =>  150,
                                        'description' =>  __( 'Set up layout options' , 'customizr' )
                      ),

                      'tc_page_comments'          => array(
                                        'title'     =>  __( 'Comments' , 'customizr' ),
                                        'priority'    =>  160,
                                        'description' =>  __( 'Set up comments options' , 'customizr' ),  
                      ),

                      'tc_social_settings'        => array(
                                        'title'     =>  __( 'Social links' , 'customizr' ), 
                                        'priority'    =>  170,
                                        'description' =>  __( 'Set up your social links' , 'customizr' ),
                      ),

                      'tc_image_settings'         => array(
                                        'title'     =>  __( 'Images' , 'customizr' ),
                                        'priority'    =>  180,
                                        'description' =>  __( 'Various images settings' , 'customizr' ),
                      ),

                      'tc_links_settings'         => array(
                                        'title'     =>  __( 'Links' , 'customizr' ),
                                        'priority'    =>  190,
                                        'description' =>  __( 'Various links settings' , 'customizr' ),
                      ),

                      'tc_custom_css'           => array(
                                        'title'     =>  __( 'Custom CSS' , 'customizr' ),
                                        'priority'    =>  200,
                                        'description' =>  __( 'Add your own CSS' , 'customizr' ),
                      ),

                      'tc_responsive'           => array(
                                        'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                        'priority'    =>  210,
                                        'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                      )
              )

      );//end of add_sections array
      $add_section = apply_filters( 'tc_add_section_map', $add_section );

      //specifies the transport for some options
      $get_setting    = array(
              'get_setting'       =>   array(
                      'blogname' ,
                      'blogdescription'
              )
      );//end of get_setting array
      $get_setting = apply_filters( 'tc_get_setting_map', $get_setting );




      /*-----------------------------------------------------------------------------------------------------
                          NAVIGATION SECTION
      ------------------------------------------------------------------------------------------------------*/  
      $navigation_option_map = array(         
              'menu_button'           => array(
                                'setting_type'  =>  null,
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'nav' ,
                                'type'      =>  'button' ,
                                'link'      =>  'nav-menus.php' ,
                                'buttontext'  => __( 'Manage menus' , 'customizr' ),
              ),
              //The hover menu type has been introduced in v3.1.0. 
              //For users already using the theme (no theme's option set), the default choice is click, for new users, it is hover.
              'tc_theme_options[tc_menu_type]'  => array(
                                'default'   =>  ( false == get_option('tc_theme_options') ) ? 'hover' : 'click' ,
                                'label'     =>  __( 'Select a submenu expansion option' , 'customizr' ),
                                'section'   =>  'nav' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'click'   => __( 'Expand submenus on click' , 'customizr'),
                                        'hover'   => __( 'Expand submenus on hover' , 'customizr'  ),
                                ),
              ),
      ); //end of navigation options
      $navigation_option_map = apply_filters( 'tc_navigation_option_map', $navigation_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                                        SKIN SECTION
      ------------------------------------------------------------------------------------------------------*/
      $skin_option_map    = array(
              //skin select
              'tc_theme_options[tc_skin]'     => array(
                                'default'   =>  'blue3.css' ,
                                'control'   => 'TC_controls' ,
                                'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                                'section'   =>  'tc_skins_settings' ,
                                'type'      =>  'select' ,
                                'choices'    =>  $this -> tc_build_skin_list(),
                                'transport'   =>  'postMessage',
              ),

              'tc_theme_options[tc_minified_skin]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Performance : use the minified CSS stylesheet", 'customizr' ),
                                'section'     => 'tc_skins_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'Using the minified version of the skin stylesheet will speed up your webpage load time.' , 'customizr' ),
              ),

              //enable/disable top border
              'tc_theme_options[tc_top_border]' => array(
                                'default'   =>  1,//top border on by default
                                'label'     =>  __( 'Display top border' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'tc_skins_settings' ,
                                'type'      =>  'checkbox' ,
                                'notice'    =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
              )
      );//end of skin options
      apply_filters( 'tc_skin_option_map', $skin_option_map, $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     LOGO & FAVICON SECTION
      ------------------------------------------------------------------------------------------------------*/
      $logo_favicon_option_map = array(
              'tc_theme_options[tc_logo_upload]'  => array(
                                'control'   =>  'TC_Customize_Upload_Control' ,
                                'label'     =>  __( 'Logo Upload (supported formats : .jpg, .png, .gif, svg, svgz)' , 'customizr' ),
                                'section'   =>  'tc_logo_settings' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' )
              ),

              //force logo resize 250 * 85
              'tc_theme_options[tc_logo_resize]'  => array(
                                'default'   =>  1,
                                'label'     =>  __( 'Force logo dimensions to max-width:250px and max-height:100px' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'tc_logo_settings' ,
                                'type'        => 'checkbox' ,
              ),

              //hr
              'hr_logo'             => array(
                                'control'   =>  'TC_controls' ,
                                'section'   =>  'tc_logo_settings' ,
                                'type'        =>  'hr' ,
              ),

              //favicon
              'tc_theme_options[tc_fav_upload]' => array(
                                'control'   =>  'TC_Customize_Upload_Control' ,
                                'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                                'section'   =>  'tc_logo_settings' ,
                                'type'      => 'tc_upload',
                                'sanitize_callback' => array( $this , 'tc_sanitize_number'),
              )
      );
      $logo_favicon_option_map = apply_filters( 'tc_logo_favicon_option_map', $logo_favicon_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                                     FRONT PAGE SETTINGS
      ------------------------------------------------------------------------------------------------------*/    
      $front_page_option_map = array(
              //title
              'homecontent_title'         => array(
                                'setting_type'  =>  null,
                                'control'   =>  'TC_controls' ,
                                'title'       => __( 'Choose content and layout' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'      => 'title' ,
                                'priority'      => 0,
              ),

              //show on front
              'show_on_front'           => array(
                                'label'     =>  __( 'Front page displays' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
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
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),

              //page for posts
              'page_for_posts'          => array(
                                'label'     =>  __( 'Posts page' , 'customizr'  ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'dropdown-pages' ,
                                'priority'      => 1,
              ),

              //layout
              'tc_theme_options[tc_front_layout]' => array(
                                'default'       => 'f' ,//Default layout for home page is full width
                                'label'     =>  __( 'Set up the front page layout' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'       => 2,
              ),

              //select slider
              'tc_theme_options[tc_front_slider]' => array(
                                'default'     => 'demo' ,
                                'control'     => 'TC_controls' ,
                                'title'       => __( 'Slider options' , 'customizr' ),
                                'label'       => __( 'Select front page slider' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'select' ,
                                //!important
                                'choices'     => ($get_default == true) ? null : $this -> tc_slider_choices(),
                                'priority'    => 20
              ),

              //select slider
              'tc_theme_options[tc_slider_width]' => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Full width slider' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 30,
              ),

              //Delay between each slides
              'tc_theme_options[tc_slider_delay]' => array(
                                'default'       => 5000,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Delay between each slides' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'number' ,
                                'step'      => 500,
                                'min'     => 1000,
                                'notice'    => __( 'in ms : 1000ms = 1s' , 'customizr' ),
                                'priority'      => 50,
              ),

              //Front page widget area
              'tc_theme_options[tc_show_featured_pages]'  => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'title'       => __( 'Featured pages options' , 'customizr' ),
                                'label'       => __( 'Display home featured pages area' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'select' , 
                                'choices'     => array(
                                        1 => __( 'Enable' , 'customizr' ),
                                        0 => __( 'Disable' , 'customizr' ),
                                ),
                                'priority'        => 55,
              ),

              //display featured page images
              'tc_theme_options[tc_show_featured_pages_img]' => array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Show images' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'The images are set with the "featured image" of each pages (in the page edit screen). Uncheck the option above to disable the featured page images.' , 'customizr' ),
                                'priority'      => 60,
              ),

              //display featured page images
              'tc_theme_options[tc_featured_page_button_text]' => array(
                                'default'       => __( 'Read more &raquo;' , 'customizr' ),
                                'transport'     =>  'postMessage',
                                'label'       => __( 'Button text' , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'text' ,
                                'priority'      => 65,
              )

      );//end of front_page_options
      $front_page_option_map = array_merge( $front_page_option_map , $this -> tc_generates_featured_pages() );
      $front_page_option_map = apply_filters( 'tc_front_page_option_map', $front_page_option_map , $get_default );





      /*-----------------------------------------------------------------------------------------------------
                                     SITE LAYOUT
      ------------------------------------------------------------------------------------------------------*/    
      $layout_option_map = array(
              //Breadcrumb
              'tc_theme_options[tc_breadcrumb]' => array(
                                'default'       => 1,//Breadcrumb is checked by default
                                'label'       => __( 'Display Breadcrumb' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 1,
              ),

              //Global sidebar layout
              'tc_theme_options[tc_sidebar_global_layout]' => array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the global default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'      => 2,
              ),

              //force default layout on every posts
              'tc_theme_options[tc_sidebar_force_layout]' =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Force default layout everywhere' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                                'priority'      => 3,
              ),

              //Post sidebar layout
              'tc_theme_options[tc_sidebar_post_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'      => 4,
              ),

              //Post per page
              'posts_per_page'  =>  array(
                                'default'     => get_option( 'posts_per_page' ),
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Maximum number of posts per page' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'     => 1,
                                //'priority'       => 8,
              ),

              //Post list length
              'tc_theme_options[tc_post_list_length]' =>  array(
                                'default'     => 'excerpt',
                                'label'       => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => array(
                                        'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                        'full'    => __( 'Display the full content' , 'customizr' )
                                        )
                                //'priority'       => 6,
              ),

              //Page sidebar layout
              'tc_theme_options[tc_sidebar_page_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                //'priority'       => 6,
                                ),
      );//end of layout_options
      $layout_option_map = apply_filters( 'tc_layout_option_map', $layout_option_map , $get_default);




      /*-----------------------------------------------------------------------------------------------------
                                     COMMENTS SETTINGS
      ------------------------------------------------------------------------------------------------------*/    
      $comment_option_map = array(
              'tc_theme_options[tc_page_comments]'  =>  array(
                                'default'     => 0,
                                'control'     => 'TC_controls',
                                'label'       => __( 'Enable comments on pages' , 'customizr' ),
                                'section'     => 'tc_page_comments',
                                'type'        => 'checkbox',
                                'priority'    => 20,
                                'notice'      => __( 'This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
              )
      );
      $comment_option_map = apply_filters( 'tc_comment_option_map', $comment_option_map , $get_default );



      /*-----------------------------------------------------------------------------------------------------
                               SOCIAL POSITIONS AND NETWORKS
      ------------------------------------------------------------------------------------------------------*/
      $social_layout_map = array(
              //Social position checkboxes
              'tc_theme_options[tc_social_in_header]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in header' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 10
              ),

              'tc_theme_options[tc_social_in_left-sidebar]' =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 20
              ),

              'tc_theme_options[tc_social_in_right-sidebar]'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 30
              ),
              'tc_theme_options[tc_social_in_footer]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in footer' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 40
              )
      );//end of social layout map
              
      $social_option_map = array_merge( $social_layout_map , $this -> tc_generates_socials() );
      $social_option_map = apply_filters( 'tc_social_option_map', $social_option_map, $get_default );




      /*-----------------------------------------------------------------------------------------------------
                                     IMAGE SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $images_option_map = array(
              'tc_theme_options[tc_fancybox]' =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Enable/disable lightbox effect on images' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option activate a popin window whith a zoom effect when an image is clicked. This will not apply to image gallery.' , 'customizr' ),
              ),

              'tc_theme_options[tc_fancybox_autoscale]' =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Autoscale images on zoom' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
              ),

              'tc_theme_options[tc_retina_support]' =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Enable/disable retina support' , 'customizr' ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => sprintf('%1$s <strong>%2$s</strong> : <a href="%4$splugin-install.php?tab=plugin-information&plugin=regenerate-thumbnails" title="%5$s" target="_blank">%3$s</a>.',
                                    __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
                                    __( "It is strongly recommended to regenerate your media library images in high definition with this free plugin" , 'customizr'),
                                    __( "regenerate thumbnails" , 'customizr'),
                                    admin_url(),
                                    __( "Open the description page of the Regenerate thumbnails plugin" , 'customizr')
                                )
              ),
               'tc_theme_options[tc_display_slide_loader]'  =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Sliders : display on loading icon before rendering the slides" , "customizr" ),
                                'section'     => 'tc_image_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'When checked, this option displays a loading icon when the slides are beeing setup.' , 'customizr' ),
              )
      );//end of images options
      $images_option_map = apply_filters( 'tc_images_option_map', $images_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     LINKS SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $links_option_map = array(
              'tc_theme_options[tc_link_scroll]'  =>  array(
                                'default'       => 0,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Enable/disable smooth scroll on click' , 'customizr' ),
                                'section'     => 'tc_links_settings' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'If enabled, this option activates a smooth page scroll when clicking on a link to an anchor of the same page.' , 'customizr' ),
              )
      );//end of links options
      $links_option_map = apply_filters( 'tc_links_option_map', $links_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                    RESPONSIVE SETTINGS
      ------------------------------------------------------------------------------------------------------*/
      $responsive_option_map = array(
              'tc_theme_options[tc_block_reorder]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Enable/disable blocks reordering on small devices' , 'customizr' ),
                                'section'     => 'tc_responsive' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'On responsive mode, for smartphone viewport, the sidebars are moved after the main content block.' , 'customizr' ),
              ),

              'tc_theme_options[tc_center_slides]'  =>  array(
                                'default'       => 1,
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Enable/disable slider's slides centering on any devices" , "customizr" ),
                                'section'     => 'tc_responsive' ,
                                'type'        => 'checkbox' ,
                                'notice'    => __( 'This option centers your slider (carousel) pictures vertically and horizontally on any devices when displayed in full width mode' , 'customizr' ),
              )

      );//end of links options
      $responsive_option_map = apply_filters( 'tc_responsive_option_map', $responsive_option_map , $get_default );


      /*-----------------------------------------------------------------------------------------------------
                                     CUSTOM CSS
      ------------------------------------------------------------------------------------------------------*/
      $custom_css_option_map = array(

              'tc_theme_options[tc_custom_css]' =>  array(
                                'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                                'section'     => 'tc_custom_css' ,
                                'type'        => 'textarea' ,
                                'notice'    => sprintf('%1$s <a href="http://themesandco.com/snippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                    __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                    __( 'child theme.' , 'customizr'),
                                    __( 'How to create and use a child theme ?' , 'customizr')
                                )
              )
      );//end of custom_css_options
      $custom_css_option_map = apply_filters( 'tc_custom_css_option_map', $custom_css_option_map , $get_default );

      $add_setting_control = array(
              'add_setting_control'   =>   array_merge(
                $navigation_option_map, 
                $skin_option_map, 
                $logo_favicon_option_map, 
                $front_page_option_map, 
                $layout_option_map,  
                $comment_option_map, 
                $social_option_map, 
                $images_option_map,
                $links_option_map,
                $responsive_option_map,
                $custom_css_option_map,
                apply_filters( 'tc_custom_setting_control', array() )
              )
      );
      $add_setting_control = apply_filters( 'tc_add_setting_control_map', $add_setting_control );

      //merges all customizer arrays
      $customizer_map = array_merge( $add_panel, $remove_section , $add_section , $get_setting , $add_setting_control );

      return apply_filters( 'tc_customizer_map', $customizer_map );

    }//end of tc_customizer_map function


    /**
     * adds sanitization callback funtion : textarea
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_textarea( $value) {
      $value = esc_html( $value);
      return $value;
    }



    /**
     * adds sanitization callback funtion : number
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_number( $value) {
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
    function tc_sanitize_url( $value) {
      $value = esc_url( $value);
      return $value;
    }



    /**
     * adds sanitization callback funtion : colors
     * @package Customizr
     * @since Customizr 1.1.4
     */
    function tc_sanitize_hex_color( $color ) {
      if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
        return '#' . $unhashed;

      return $color;
    }


    /**
    * Change upload's path to relative instead of absolute
    * @package Customizr
    * @since Customizr 3.1.11
    */
    function tc_sanitize_uploads( $url ) {
      $upload_dir = wp_upload_dir();
      return str_replace($upload_dir['baseurl'], '', $url);
    }
    


    /**
    * Update initial remove section map defined in class-fire-utils.php.
    * (nav and title_tagline sections are added back in tc_update_section_map() )
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_remove_sections( $_unchanged ) {
      return array(
        'remove_section'       =>   array(
                              'background_image' ,
                              'static_front_page' ,
                              'colors',
                              'nav',
                              'title_tagline',
                              'tc_page_comments'
            )
      );
    }



    /**
    * Update initial section map defined in class-fire-utils.php.
    * Add panel parameter (since WP4.0)
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_section_map( $_unchanged ) {
      //For nav menus option
      $locations      = get_registered_nav_menus();
      $menus          = wp_get_nav_menus();
      $num_locations  = count( array_keys( $locations ) );

      return array(
                  'add_section'       =>   array(
                        'tc_skins_settings'         => array(
                                            'title'     =>  __( 'Skin' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 1 : 10,
                                            'description' =>  __( 'Select a skin for Customizr' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_social_settings'        => array(
                                            'title'     =>  __( 'Social links' , 'customizr' ), 
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 9 : 20,
                                            'description' =>  __( 'Set up your social links' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_links_settings'         => array(
                                            'title'     =>  __( 'Links style and effects' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 22 : 30,
                                            'description' =>  __( 'Various links settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_titles_icons_settings'        => array(
                                            'title'     =>  __( 'Titles icons settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 18 : 40,
                                            'description' =>  __( 'Set up the titles icons options' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_image_settings'         => array(
                                            'title'     =>  __( 'Image settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 95 : 50,
                                            'description' =>  __( 'Various images settings' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_responsive'           => array(
                                            'title'     =>  __( 'Responsive settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 96 : 60,
                                            'description' =>  __( 'Various settings for responsive display' , 'customizr' ),
                                            'panel'   => 'tc-global-panel'
                        ),
                        'tc_header_layout'         => array(
                                            'title'    => $this -> is_wp_version_before_4_0 ? __( 'Header design and layout', 'customizr' ) : __( 'Design and layout', 'customizr' ),
                                            'priority' => $this -> is_wp_version_before_4_0 ? 5 : 20,
                                            'panel'   => 'tc-header-panel'
                        ),
                        'title_tagline'         => array(
                                            'title'    => __( 'Site Title & Tagline', 'customizr' ),
                                            'priority' => $this -> is_wp_version_before_4_0 ? 7 : 20,
                                            'panel'   => 'tc-header-panel'
                        ),
                        'tc_logo_settings'            => array(
                                            'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 8 : 30,
                                            'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                                            'panel'   => 'tc-header-panel'
                        ),
                        'nav'           => array(
                                  'title'          => __( 'Navigation' , 'customizr' ),
                                  'theme_supports' => 'menus',
                                  'priority'       => $this -> is_wp_version_before_4_0 ? 10 : 40,
                                  'description'    => sprintf( _n('Your theme supports %s menu. Select which menu you would like to use.', 'Your theme supports %s menus. Select which menu appears in each location.', $num_locations ), number_format_i18n( $num_locations ) ) . "\n\n" . __('You can edit your menu content on the Menus screen in the Appearance section.'),
                                  'panel'   => 'tc-header-panel'
                        ),

                        'tc_frontpage_settings'       => array(
                                            'title'     =>  __( 'Front Page' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 12 : 10,
                                            'description' =>  __( 'Set up front page options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),

                        'tc_layout_settings'        => array(
                                            'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 15 : 15,
                                            'description' =>  __( 'Set up layout options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),

                        'tc_post_list_settings'        => array(
                                            'title'     =>  __( 'Post lists : blog, archives, ...' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 16 : 20,
                                            'description' =>  __( 'Set up post lists options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_single_post_settings'        => array(
                                            'title'     =>  __( 'Single posts' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 17 : 24,
                                            'description' =>  __( 'Set up single posts options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_breadcrumb_settings'        => array(
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
                        'tc_post_metas_settings'        => array(
                                            'title'     =>  __( 'Post metas (category, tags, custom taxonomies)' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 20 : 50,
                                            'description' =>  __( 'Set up post metas options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_comments_settings'          => array(
                                            'title'     =>  __( 'Comments' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 25 : 60,
                                            'description' =>  __( 'Set up comments options' , 'customizr' ),
                                            'panel'   => 'tc-content-panel'
                        ),
                        'tc_footer_global_settings'          => array(
                                            'title'     =>  __( 'Footer global settings' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 40 : 10,
                                            'description' =>  __( 'Set up footer global options' , 'customizr' ),
                                            'panel'   => 'tc-footer-panel'
                        ),
                        'tc_custom_css'           => array(
                                            'title'     =>  __( 'Custom CSS' , 'customizr' ),
                                            'priority'    =>  $this -> is_wp_version_before_4_0 ? 100 : 10,
                                            'description' =>  __( 'Add your own CSS' , 'customizr' ),
                                            'panel'   => 'tc-advanced-panel'
                        )
                  )

        );//end of add_sections array
    }


    /**
    * Update initial setting_control map defined in class-fire-utils.php.
    * 
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_update_setting_control_map( $_map ) {
      //remove options for original section
      $_to_unset = array(
        'tc_theme_options[tc_breadcrumb]',
        'posts_per_page',
        'tc_theme_options[tc_post_list_length]',
        'tc_theme_options[tc_sidebar_global_layout]',
        'tc_theme_options[tc_sidebar_force_layout]',
        'tc_theme_options[tc_sidebar_post_layout]',
        'tc_theme_options[tc_sidebar_page_layout]',
        'tc_theme_options[tc_social_in_header]',
        'tc_theme_options[tc_social_in_left-sidebar]',
        'tc_theme_options[tc_social_in_right-sidebar]',
        'tc_theme_options[tc_social_in_footer]',
        'tc_theme_options[tc_top_border]',
        'tc_theme_options[tc_custom_css]',
        'tc_theme_options[tc_page_comments]'
      );
      foreach ($_to_unset as $_value) {
        if ( ! isset($_map['add_setting_control'][$_value]) )
          continue;
        unset( $_map['add_setting_control'][$_value] );
      }

      //adds back previously removed settings + brand new settings
      $_new_settings = array(
        /*********** OLD **************/
        //Breadcrumb
              'tc_theme_options[tc_breadcrumb]' => array(
                              'default'       => 1,//Breadcrumb is checked by default
                              'label'         => __( 'Display Breadcrumb' , 'customizr' ),
                              'control'     =>  'TC_controls' ,
                              'section'       => 'tc_breadcrumb_settings' ,
                              'type'          => 'checkbox' ,
                              'priority'      => 1,
              ),
                
              //Global sidebar layout
              'tc_theme_options[tc_sidebar_global_layout]' => array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'         => __( 'Choose the global default layout' , 'customizr' ),
                              'section'     => 'tc_layout_settings' ,
                              'type'          => 'select' ,
                              'choices'     => $this -> tc_layout_choices(),
                              'notice'      => __( 'Note : the home page layout has to be set in the home page section' , 'customizr' ),
                              'priority'      => 10
               ),

               //force default layout on every posts
              'tc_theme_options[tc_sidebar_force_layout]' =>  array(
                              'default'       => 0,
                              'control'     => 'TC_controls' ,
                              'label'         => __( 'Force default layout everywhere' , 'customizr' ),
                              'section'       => 'tc_layout_settings' ,
                              'type'          => 'checkbox' ,
                              'notice'      => __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
                              'priority'      => 20
              ),
              //Post sidebar layout
              'tc_theme_options[tc_sidebar_post_layout]'  =>  array(
                              'default'       => 'l' ,//Default sidebar layout is on the left
                              'label'       => __( 'Choose the posts default layout' , 'customizr' ),
                              'section'     => 'tc_layout_settings' ,
                              'type'        => 'select' ,
                              'choices'   => $this -> tc_layout_choices(),
                              'priority'      => 30
              ),
              //Page sidebar layout
                'tc_theme_options[tc_sidebar_page_layout]'  =>  array(
                                'default'       => 'l' ,//Default sidebar layout is on the left
                                'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                                'section'     => 'tc_layout_settings' ,
                                'type'        => 'select' ,
                                'choices'   => $this -> tc_layout_choices(),
                                'priority'       => 40
              ),


              //Post per page
              'posts_per_page'  =>  array(
                              'default'     => get_option( 'posts_per_page' ),
                              'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                              'control'     => 'TC_controls' ,
                              'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                              'section'       => 'tc_post_list_settings' ,
                              'type'          => 'number' ,
                              'step'        => 1,
                              'min'         => 1,
                              'priority'       => 10,
              ),
              //Post list length
              'tc_theme_options[tc_post_list_length]' =>  array(
                                'default'       => 'excerpt',
                                'label'         => __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'select' ,
                                'choices'       => array(
                                        'excerpt'   => __( 'Display the excerpt' , 'customizr' ),
                                        'full'    => __( 'Display the full content' , 'customizr' )
                                        ),
                                'priority'       => 20,
              ),

              /********** NEW **********/
              /* Header */
              //enable/disable top border
              'tc_theme_options[tc_top_border]' => array(
                                'default'       =>  1,//top border on by default
                                'label'         =>  __( 'Display top border' , 'customizr' ),
                                'control'       =>  'TC_controls' ,
                                'section'       =>  'tc_header_layout' ,
                                'type'          =>  'checkbox' ,
                                'notice'        =>  __( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
                                'priority'      => 5
              ),
              'tc_theme_options[tc_header_layout]'  =>  array(
                                'default'       => 'left',
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Layout" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'left'      => __( 'Logo / title on the left' , 'customizr' ),
                                        'centered'  => __( 'Logo / title centered' , 'customizr'),
                                        'right'     => __( 'Logo / title on the right' , 'customizr' )
                                ),
                                'priority'      => 10,
                                'transport'     => 'postMessage'
              ),

              'tc_theme_options[tc_show_tagline]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display the tagline" , "customizr" ),
                                'section'       => 'title_tagline' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage'
              ),
              'tc_theme_options[tc_display_boxed_navbar]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display menu in a box" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option wraps the header menu/tagline/social in a light grey box.' , 'customizr' ),
              ),
              'tc_theme_options[tc_sticky_header]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky on scroll" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage',
                                'notice'    => __( 'If checked, this option makes the header stick to the top of the page on scroll down.' , 'customizr' )
              ),
              'tc_theme_options[tc_sticky_show_tagline]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_show_title_logo]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the title / logo" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_shrink_title_logo]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : shrink title / logo" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_show_menu]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : display the menu" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 60,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_transparent_on_scroll]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Sticky header : semi-transparent on scroll" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 67,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_sticky_z_index]'  =>  array(
                                'default'       => 100,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Set the header z-index" , "customizr" ),
                                'section'       => 'tc_header_layout' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'transport'     => 'postMessage',
                                'notice'    => sprintf('%1$s <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/z-index" target="_blank">%2$s</a> ?',
                                    __( "What is" , 'customizr' ),
                                    __( "the z-index" , 'customizr')
                                ),
              ),

              /* Menu */
              'tc_theme_options[tc_menu_position]'  =>  array(
                                'default'       => 'left',
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Menu position" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'pull-menu-left'      => __( 'Menu on the left' , 'customizr' ),
                                        'pull-menu-right'     => __( 'Menu on the right' , 'customizr' )
                                ),
                                'priority'      => 20,
                                'transport'     => 'postMessage'
              ),
              'tc_theme_options[tc_menu_submenu_fade_effect]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Reveal the sub-menus blocks with a fade effect" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_menu_submenu_item_move_effect]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Hover move effect for the sub menu items" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40,
                                'transport'     => 'postMessage',
              ),
              'tc_theme_options[tc_menu_resp_dropdown_limit_to_viewport]'  =>  array(
                                'default'       => 0,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "In responsive mode, limit the height of the dropdown menu block to the visible viewport" , "customizr" ),
                                'section'       => 'nav' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50,
                                //'transport'     => 'postMessage',
              ),
              /* Links */
              'tc_theme_options[tc_link_hover_effect]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Fade effect on link hover" , "customizr" ),
                                'section'       => 'tc_links_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              
              /* Breadcrumb*/
              'tc_theme_options[tc_show_breadcrumb_home]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb on home page" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20
              ),
              'tc_theme_options[tc_show_breadcrumb_in_pages]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in pages" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 30
                                              
              ),
              'tc_theme_options[tc_show_breadcrumb_in_single_posts]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in single posts" , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 40
                                              
              ),
              'tc_theme_options[tc_show_breadcrumb_in_post_lists]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display the breadcrumb in posts lists : blog page, archives, search results..." , "customizr" ),
                                'section'       => 'tc_breadcrumb_settings' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 50
                                              
              ),

              /* Icons */
              'tc_theme_options[tc_show_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 10,
                                'notice'    => __( 'When this option is checked, a contextual icon is displayed next to the titles of pages, posts, archives, and WP built-in widgets.' , 'customizr' ),
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_page_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display a page icon next to the page title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display a post icon next to the single post title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 30,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_archive_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display an icon next to the archive title" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, an archive type icon is displayed in the heading of every types of archives, on the left of the title. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 40,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_list_title_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display an icon next to each post title in an archive page" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, a post type icon is displayed on the left of each post titles in an archive page. An archive page can be : category, tag, author, date archive, custom taxonomies, search results.' , 'customizr' ),
                                'priority'      => 50,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_sidebar_widget_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "WP sidebar widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 60,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_footer_widget_icon]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "WP footer widgets : display icons next to titles" , "customizr" ),
                                'section'       => 'tc_titles_icons_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 70,
                                'transport'   => 'postMessage'
              ),


              /* Post metas */
              'tc_theme_options[tc_show_post_metas]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'notice'    => __( 'When this option is checked, the post metas (like taxonomies, date and author) are displayed below the post titles.' , 'customizr' ),
                                'priority'      => 5,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_metas_home]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls' ,
                                'title'         => __( 'Select the contexts' , 'customizr' ),
                                'label'         => __( "Display posts metas on home" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 15,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_metas_single_post]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas for single posts" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_show_post_metas_post_lists]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Display posts metas in post lists (archives, blog page)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_show_post_metas_categories]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'title'         => __( 'Select the metas to display' , 'customizr' ),
                                'label'         => __( "Display hierarchical taxonomies (like categories)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 30
              ),
              
              'tc_theme_options[tc_show_post_metas_tags]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display non-hierarchical taxonomies (like tags)" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 35
              ),

              'tc_theme_options[tc_show_post_metas_publication_date]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the publication date" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 40
              ),

              'tc_theme_options[tc_show_post_metas_update_date]'  =>  array(
                                'default'       => 0,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the update date" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 45,
                                'notice'    => __( 'If this option is checked, additional date informations about the the last post update can be displayed (nothing will show up if the post has never been updated).' , 'customizr' ),
              ),

              'tc_theme_options[tc_post_metas_update_date_format]'  =>  array(
                                'default'       => 'days',
                                'control'       => 'TC_controls',
                                'label'         => __( "Select the last update format" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'days'     => __( 'Nb of days since last update' , 'customizr' ),
                                        'date'     => __( 'Date of the last update' , 'customizr' )
                                ),
                                'priority'      => 50
              ),
              'tc_theme_options[tc_show_post_metas_author]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls',
                                'label'         => __( "Display the author" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 55
              ),
              'tc_theme_options[tc_post_metas_update_notice_in_title]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls',
                                'label'         => __( "Display a recent update notice" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'checkbox',
                                'priority'      => 65,
                                'notice'    => __( 'If this option is checked, a customizable recent update notice is displayed next to the post title.' , 'customizr' )
              ),
              'tc_theme_options[tc_post_metas_update_notice_interval]'  =>  array(
                                'default'       => 10,
                                'control'       => 'TC_controls',
                                'label'         => __( "Display the notice if the last update is less (strictly) than n days old" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 70,
                                'notice'    => __( 'Set a maximum interval (in days) during which the last update notice will be displayed.' , 'customizr' ),
              ),
              'tc_theme_options[tc_post_metas_update_notice_text]'  =>  array(
                                'default'       => __( "Recently updated !" , "customizr" ),
                                'control'       => 'TC_controls',
                                'label'         => __( "Update notice text" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
                                'type'          => 'text',
                                'priority'      => 75,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_post_metas_update_notice_format]'  =>  array(
                                'default'       => 'label-default',
                                'control'       => 'TC_controls',
                                'label'         => __( "Update notice style" , "customizr" ),
                                'section'       => 'tc_post_metas_settings',
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
              ),

              /* Post list layout */
              'tc_theme_options[tc_post_list_excerpt_length]'  =>  array(
                                'default'       => 55,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Set the excerpt length (in number of words) " , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 0,
                                'priority'      => 23
              ),
              'tc_theme_options[tc_post_list_show_thumb]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'title'         => __( 'Thumbnails options' , 'customizr' ),
                                'label'         => __( "Display the post thumbnails" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 25,
                                'notice'    => __( 'When this option is checked, the post thumbnails are displayed in all post lists : blog, archives, author page, search pages, ...' , 'customizr' ),
              ),
              'tc_theme_options[tc_post_list_use_attachment_as_thumb]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "If no featured image is set, use the last image attached to this post." , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 28
              ),
              'tc_theme_options[tc_post_list_thumb_shape]'  =>  array(
                                'default'       => 'rounded',
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Thumbnails shape" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
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
                                'priority'      => 30
              ),
              'tc_theme_options[tc_post_list_thumb_height]' => array(
                                'default'       => 250,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                'section'     => 'tc_post_list_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'     => 0,
                                'priority'      => 35,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_post_list_thumb_position]'  =>  array(
                                'default'       => 'right',
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Thumbnails position" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'top'     => __( 'Top' , 'customizr' ),
                                        'right'   => __( 'Right' , 'customizr' ),
                                        'bottom'    => __( 'Bottom' , 'customizr' ),
                                        'left'    => __( 'Left' , 'customizr' ),
                                ),
                                'priority'      => 40
              ),
             
              'tc_theme_options[tc_post_list_thumb_alternate]'  =>  array(
                                'default'       => 1,
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Alternate thumbnail/content" , "customizr" ),
                                'section'       => 'tc_post_list_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 50
              ),
              
              'tc_theme_options[tc_single_post_thumb_location]'  =>  array(
                                'default'       => 'hide',
                                'control'     => 'TC_controls' ,
                                'label'         => __( "Post thumbnail position" , "customizr" ),
                                'section'       => 'tc_single_post_settings' ,
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'hide'                    => __( "Don't display" , 'customizr' ),
                                        '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                        '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                        '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                                ),
                                'priority'      => 10
              ),
              'tc_theme_options[tc_single_post_thumb_height]' => array(
                                'default'       => 250,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                                'section'     => 'tc_single_post_settings' ,
                                'type'        => 'number' ,
                                'step'        => 1,
                                'min'         => 0,
                                'priority'      => 20,
                                'transport'   => 'postMessage'
              ),
              /* Comments */
              'tc_theme_options[tc_comment_show_bubble]'  =>  array(
                                'default'       => 1,
                                'title'         => __('Comments bubbles' , 'customizr'),
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display the number of comments in a bubble next to the post title" , "customizr" ),
                                'section'       => 'tc_comments_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),

              'tc_theme_options[tc_comment_bubble_shape]' => array(
                                'default'     => 'default',
                                'control'     => 'TC_controls',
                                'label'       => __( 'Comments bubble shape' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'default'             => __( "Small bubbles" , 'customizr' ),
                                        'custom-bubble-one'   => __( 'Large bubbles' , 'customizr' ),
                                ),
                                'priority'    => 10,
              ),

              'tc_theme_options[tc_comment_bubble_color_type]' => array(
                                'default'     => 'custom',
                                'control'     => 'TC_controls',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'      =>  'select' ,
                                'choices'     => array(
                                        'skin'     => __( "Skin color" , 'customizr' ),
                                        'custom'   => __( 'Custom' , 'customizr' ),
                                ),
                                'priority'    => 20,
              ),

              'tc_theme_options[tc_comment_bubble_color]' => array(
                                'default'     => '#F00',
                                'control'     => 'WP_Customize_Color_Control',
                                'label'       => __( 'Comments bubble color' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        =>  'color' ,
                                'priority'    => 30,
                                'sanitize_callback'    => array( $this, 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color',
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_page_comments]'  =>  array(
                                'default'     => 0,
                                'control'     => 'TC_controls',
                                'title'       => __( 'Other comments settings' , 'customizr'),
                                'label'       => __( 'Enable comments on pages' , 'customizr' ),
                                'section'     => 'tc_comments_settings',
                                'type'        => 'checkbox',
                                'priority'    => 40,
                                'notice'      => sprintf('%1$s %2$s <a href="%3$s" target="_blank">%4$s</a>',
                                    __( 'If checked, this option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
                                    __( "Change other comments settings in the" , 'customizr'),
                                    admin_url() . 'options-discussion.php',
                                    __( 'discussion settings page.' , 'customizr' )
                                ),
              ),
              /* Footer */
              'tc_theme_options[tc_show_back_to_top]'  =>  array(
                                'default'       => 1,
                                'control'       => 'TC_controls' ,
                                'label'         => __( "Display a back to top arrow on scroll" , "customizr" ),
                                'section'       => 'tc_footer_global_settings' ,
                                'type'          => 'checkbox',
                                'priority'      => 1
              ),

              /* SOCIALS */
              //Social position checkboxes
              'tc_theme_options[tc_social_in_header]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in header' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'      => 10,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_footer]' =>  array(
                                'default'       => 1,
                                'label'       => __( 'Social links in footer' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 15,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_left-sidebar]' =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in left sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 20,
                                'transport'   => 'postMessage'
              ),

              'tc_theme_options[tc_social_in_right-sidebar]'  =>  array(
                                'default'       => 0,
                                'label'       => __( 'Social links in right sidebar' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 25,
                                'transport'   => 'postMessage'
              ),
              'tc_theme_options[tc_social_in_sidebar_title]'  =>  array(
                                'default'       => __( 'Social links' , 'customizr' ),
                                'label'       => __( 'Social link title in sidebars' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_social_settings' ,
                                'type'        => 'text' ,
                                'priority'       => 30,
                                'transport'   => 'postMessage',
                                'notice'    => __( 'Will be hidden if empty' , 'customizr' )
              ),
              'tc_theme_options[tc_custom_css]' =>  array(
                                'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
                                'section'     => 'tc_custom_css' ,
                                'type'        => 'textarea' ,
                                'notice'    => sprintf('%1$s <a href="http://themesandco.com/snippet/creating-child-theme-customizr/" title="%3$s" target="_blank">%2$s</a>',
                                    __( "Use this field to test small chunks of CSS code. For important CSS customizations, you'll want to modify the style.css file of a" , 'customizr' ),
                                    __( 'child theme.' , 'customizr'),
                                    __( 'How to create and use a child theme ?' , 'customizr')
                                )
              ),
              //Default slider's height
              'tc_theme_options[tc_slider_default_height]' => array(
                                'default'       => 500,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'   => 'TC_controls' ,
                                'label'       => __( "Set slider's height in pixels" , 'customizr' ),
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'number' ,
                                'step'      => 1,
                                'min'       => 0,
                                'priority'      => 52,
                                'transport' => 'postMessage'
              ),
              'tc_theme_options[tc_slider_default_height_apply_all]'  =>  array(
                                'default'       => 1,
                                'label'       => __( 'Apply this height to all sliders' , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 53,
              ),
              'tc_theme_options[tc_slider_change_default_img_size]'  =>  array(
                                'default'       => 0,
                                'label'       => __( "Replace the default image slider's height" , 'customizr' ),
                                'control'   =>  'TC_controls' ,
                                'section'     => 'tc_frontpage_settings' ,
                                'type'        => 'checkbox' ,
                                'priority'       => 54,
                                'notice'    => sprintf('%1$s <a href="http://themesandco.com/customizr/#images" target="_blank">%2$s</a>',
                                    __( "If this option is checked, your images will be resized with your custom height on upload. This is better for your overall loading performance." , 'customizr' ),
                                    __( "You might want to regenerate your thumbnails." , 'customizr')
                                ),
              )
            );

      $_map['add_setting_control'] = array_merge($_map['add_setting_control'] , $_new_settings );
      return $_map;
    }



  }//end of class
endif;