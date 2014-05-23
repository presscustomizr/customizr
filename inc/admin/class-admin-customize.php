<?php
/**
* Customizer actions and filters
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

class TC_customize {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

		//add control class
		add_action ( 'customize_register'				, array( $this , 'tc_add_controls_class' ) ,10,1);

		//add the customizer built with the builder below
		add_action ( 'customize_register'				, array( $this , 'tc_customize_register' ) ,20,1 );

		//ARGS : customizer section, settings and controls accepted arguments
		add_filter ( '__customize_args'					, array( $this , 'tc_customize_arguments' ));

		//MAP  of the customizer : sections, settings, controls.
		add_filter ( '__customize_map'					, array( $this , 'tc_customizer_map' ));

		//BUILDER of the customizer using args + map
		add_filter ( '__customize_factory'				, array( $this , 'tc_customize_factory' ), 10, 3);

		//sanitazition filters
		add_filter ( '__sanitize_textarea'				, array( $this , 'tc_sanitize_textarea' ));
		add_filter ( '__sanitize_number'				, array( $this , 'tc_sanitize_number' ));
		add_filter ( '__sanitize_url'					, array( $this , 'tc_sanitize_url' ));

		//preview script
		add_action ( 'customize_preview_init'			, array( $this , 'tc_customize_preview_js' ));

		//add option to menus
		add_action ( 'admin_menu'						, array( $this , 'tc_add_options_menu' ));
		add_action ( 'wp_before_admin_bar_render'		, array( $this , 'tc_add_admin_bar_options_menu' ));

		//skin choices filter
		add_filter ( '__skin_choices'					, array( $this , 'tc_skin_choices' ), 20);

    }


	/**
	 * Add WordPress customizer page to the admin menu.
	 * @package Customizr
	 * @since Customizr 1.0 
	 */
	function tc_add_options_menu() {
	    $theme_page = add_theme_page(
	        __( 'Customiz\'it!' , 'customizr' ),   // Name of page
	        __( 'Customiz\'it!' , 'customizr' ),   // Label in menu
	        'edit_theme_options' ,          // Capability required
	        'customize.php'             // Menu slug, used to uniquely identify the page
	    );
	}





	 /**
	 * Add WordPress customizer page to the admin bar menu.
	 * @package Customizr
	 * @since Customizr 1.0 
	 */
	function tc_add_admin_bar_options_menu() {
	   if ( current_user_can( 'edit_theme_options' ) ) {
	     global $wp_admin_bar;
	     $wp_admin_bar->add_menu( array(
	       'parent' => false,
	       'id' => 'tc-customizr' ,
	       'title' =>  __( 'Customiz\'it!' , 'customizr' ),
	       'href' => admin_url( 'customize.php' ),
	       'meta'   => array(
              'title'  => __( 'Customize your website at any time!', 'customizr' ),
            ),
	     ));
	   }
	}




	 /**
	 * Adds controls to customizer
	 * @package Customizr
	 * @since Customizr 1.0 
	 */
	function tc_add_controls_class( $type) {
		locate_template( 'inc/admin/class-controls.php' , $load = true, $require_once = true );
	}





	/**
	 * Generates customizer sections, settings and controls
	 * @package Customizr
	 * @since Customizr 3.0 
	 */
	function tc_customize_register( $wp_customize) {
		return $this -> tc_customize_factory ( $wp_customize , $args = $this -> tc_customize_arguments(), $setup = $this -> tc_customizer_map() );
	}





	/**
	 * Return the list of available skins list
	 * 
	 * @package Customizr
	 * @since Customizr 3.0.11
	 */
	function tc_skin_choices() {
	    return array( 
		'blue.css' 		=> 	__( 'Blue' , 'customizr' ),
		'green.css'  	=> 	__( 'Green' , 'customizr' ),
		'yellow.css' 	=> 	__( 'Yellow' , 'customizr' ),
		'orange.css' 	=> 	__( 'Orange' , 'customizr' ),
		'red.css'		=> 	__( 'Red' , 'customizr' ),
		'purple.css'	=> 	__( 'Purple' , 'customizr' ),
		'grey.css'		=>	__( 'Grey' , 'customizr' ),
		'black.css' 	=> 	__( 'Black' , 'customizr' )
		);
	}




	/**
	 * Retrieves slider names and generate the select list
	 * @package Customizr
	 * @since Customizr 3.0.1
	 */
	function tc_slider_choices() {
	    $__options		=  	get_option('tc_theme_options');
	    $slider_names 	= 	isset($__options['tc_sliders']) ? $__options['tc_sliders'] : array();

		$slider_choices = array( 
			0 		=> 	__( '&mdash; No slider &mdash;' , 'customizr' ),
			'demo' 	=>	__( '&mdash; Demo Slider &mdash;' , 'customizr' )
			);
		if ( $slider_names ) {
			foreach( $slider_names as $tc_name => $slides) {
				$slider_choices[$tc_name] = $tc_name;
			}
		}
		return $slider_choices;
	}




	/**
	* Defines sections, settings and function of customizer and return and array
	* Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop) 
	*	
	* @package Customizr
	* @since Customizr 3.0 
	*/
	function tc_customizer_map($get_default = null) {
		
		//customizer option array
		$customize_array = array (
		'remove_section'  		 =>   array(
								'background_image' ,
								'static_front_page' ,
								'colors'
		),//end of remove_section array



		'add_section'   		=>   array(
								'tc_skins_settings'					=> array(
																	'title'			=>	__( 'Skin' , 'customizr' ),
																	'priority'		=>	10,
																	'description'	=>	__( 'Select a skin for Customizr' , 'customizr' )
								),

								'tc_logo_settings'					=> array(
																	'title'			=>	__( 'Logo &amp; Favicon' , 'customizr' ),
																	'priority'		=>	20,
																	'description'	=>	__( 'Set up logo and favicon options' , 'customizr' )
								),

								'tc_frontpage_settings'				=> array(
																	'title'			=>	__( 'Front Page' , 'customizr' ),
																	'priority'		=>	30,
																	'description'	=>	__( 'Set up front page options' , 'customizr' )
								),

								'tc_layout_settings'				=> array(
																	'title'			=>	__( 'Pages &amp; Posts Layout' , 'customizr' ),
																	'priority'		=>	150,
																	'description'	=>	__( 'Set up layout options' , 'customizr' )
								),

								'tc_page_comments'					=> array(
																	'title'			=>	__( 'Comments' , 'customizr' ),
																	'priority'		=>	160,
																	'description'	=> 	__( 'Set up comments options' , 'customizr' ),	
								),

								'tc_social_settings'				=> array(
																	'title'			=>	__( 'Social links' , 'customizr' ),	
																	'priority'		=>	170,
																	'description'	=>	__( 'Set up your social links' , 'customizr' ),
								),

								'tc_image_settings'					=> array(
																	'title'			=>	__( 'Images' , 'customizr' ),
																	'priority'		=>	180,
																	'description'	=>	__( 'Enable/disable lightbox effect on images' , 'customizr' ),
								),

								/*'tc_plugins_compatibility'			=> array(
																	'title'			=>	__( 'Plugins compatibility' , 'customizr' ),
																	'priority'		=>	190,
																	'description'	=>	__( 'Ensure Customizr compatibilty with some plugins' , 'customizr' ),
								),*/

								'tc_custom_css'						=> array(
																	'title'			=>	__( 'Custom CSS' , 'customizr' ),
																	'priority'		=>	200,
																	'description'	=>	__( 'Add your own CSS' , 'customizr' ),
								),

								'tc_debug_section'					=> array(
																	'title'			=>	__( 'Dev Tools (advanced users)' , 'customizr' ),
																	'priority'		=>	210,
																	'description'	=>	__( 'Enable/disable the Dev Tools' , 'customizr' ),
								),

		),//end of add_section array


		//specifies the transport for some options
		'get_setting'   		=>   array(
								'blogname' ,
								'blogdescription'
		),//end of get_setting array




		'add_setting_control'   =>   array(

								/*-----------------------------------------------------------------------------------------------------
														                  	NAVIGATION SECTION
								------------------------------------------------------------------------------------------------------*/							
								'menu_button'						=> array(
																	'setting_type'	=> 	null,
																	'control'		=>	'TC_controls' ,
																	'section'		=>	'nav' ,
																	'type'			=>	'button' ,
																	'link'			=>	'nav-menus.php' ,
																	'buttontext'	=> __( 'Manage menus' , 'customizr' ),
								),


								/*-----------------------------------------------------------------------------------------------------
														                  		SKIN SECTION
								------------------------------------------------------------------------------------------------------*/

								//skin select
								'tc_theme_options[tc_skin]'			=> array(
																	'default'		=>	'blue.css' ,
																	'label'			=>	__( 'Choose a predefined skin' , 'customizr' ),
																	'section'		=>	'tc_skins_settings' ,
																	'type'			=>	'select' ,
																	'choices'		=>	tc__f( '__skin_choices' )
								),

								//enable/disable top border
								'tc_theme_options[tc_top_border]'	=> array(
																	'default'		=>	1,//top border on by default
																	'label'			=>	__( 'Display top border' , 'customizr' ),
																	'control'		=>	'TC_controls' ,
																	'section'		=>	'tc_skins_settings' ,
																	'type'			=>	'checkbox' ,
																	'notice'		=>	__( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
								),

								/*-----------------------------------------------------------------------------------------------------
														                   LOGO & FAVICON SECTION
								------------------------------------------------------------------------------------------------------*/

								//logo upload
								'tc_theme_options[tc_logo_upload]'	=> array(
																	'control'		=>	'WP_Customize_Upload_Control' ,
																	'label'			=>	__( 'Logo Upload (supported formats : .jpg, .png, .gif)' , 'customizr' ),
																	'section'		=>	'tc_logo_settings' ,
								),

								//force logo resize 250 * 85
								'tc_theme_options[tc_logo_resize]'	=> array(
																	'default'		=>	1,
																	'label'			=>	__( 'Force logo dimensions to max-width:250px and max-height:100px' , 'customizr' ),
																	'section'		=>	'tc_logo_settings' ,
																	'type'     		=> 'checkbox' ,
								),

								//hr
								'hr_logo'							=> array(
																	'control'		=>	'TC_controls' ,
																	'section'		=>	'tc_logo_settings' ,
																	'type'     		=> 	'hr' ,
								),

								//favicon
								'tc_theme_options[tc_fav_upload]'	=> array(
																	'control'		=>	'WP_Customize_Upload_Control' ,
																	'label'    		=> __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
																	'section'		=>	'tc_logo_settings' ,
								),


								/*-----------------------------------------------------------------------------------------------------
														                   FRONT PAGE SETTINGS
								------------------------------------------------------------------------------------------------------*/
								//title
								'homecontent_title'					=> array(
																	'setting_type'	=> 	null,
																	'control'		=>	'TC_controls' ,
																	'title'   		=> __( 'Choose content and layout' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'			=> 'title' ,
																	'priority'      => 0,
								),

								//show on front
								'show_on_front'						=> array(
																	'label'			=>	__( 'Front page displays' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'			=> 'select' ,
																	'priority'      => 1,
																	'choices' 		=> array(
																					'nothing' 	=> __( 'Don\'t show any posts or page' , 'customizr'),
																					'posts' 	=> __( 'Your latest posts' , 'customizr'  ),
																					'page'  	=> __( 'A static page' , 'customizr'  ),
																	),
								),

								//page on front
								'page_on_front'						=> array(
																	'label'			=>	__( 'Front page' , 'customizr'  ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'       	=> 'dropdown-pages' ,
																	'priority'      => 1,
								),

								//page for posts
								'page_for_posts'					=> array(
																	'label'			=>	__( 'Posts page' , 'customizr'  ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'       	=> 'dropdown-pages' ,
																	'priority'      => 1,
								),

								//layout
								'tc_theme_options[tc_front_layout]'	=> array(
																	'default'       => 'f' ,//Default layout for home page is full width
																	'label'			=>	__( 'Set up the front page layout' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'       	=> 'select' ,
																	'choices'		=> array( //Same fields as in the tc_post_layout.php files
																					'r' 	=> __( 'Right sidebar' , 'customizr' ),
																					'l'  	=> __( 'Left sidebar' , 'customizr' ),
																					'b' 	=> __( '2 sidebars : Right and Left' , 'customizr' ),
																					'f'		=> __( 'No sidebars : full width layout' , 'customizr' )
																	),
																	'priority'       => 2,
								),

								//select slider
								'tc_theme_options[tc_front_slider]'	=> array(
																	'default'       => 'demo' ,
																	'control'		=> 'TC_controls' ,
																	'title'   		=> __( 'Slider options' , 'customizr' ),
																	'label'   		=> __( 'Select front page slider' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'    		=> 'select' ,
																	//!important
																	'choices' 		=> ($get_default == true) ? null : $this ->tc_slider_choices(),
																	'priority'      => 20,
								),

								//select slider
								'tc_theme_options[tc_slider_width]'	=> array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Full width slider' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'      => 30,
								),

								//slider check message
								'slider_check'						=> array(
																	'setting_type'	=> 	null,
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'			=> 'slider-check' ,
																	'priority'      => 40,
								),

								//Delay between each slides
								'tc_theme_options[tc_slider_delay]'	=> array(
																	'default'       => 5000,
																	'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
																	'control'		=> 'TC_controls' ,
																	'label'   		=> __( 'Delay between each slides' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'    		=> 'number' ,
																	'step'			=> 500,
																	'min'			=> 1000,
																	'notice'		=> __( 'in ms : 1000ms = 1s' , 'customizr' ),
																	'priority'      => 50,
								),

								//Front page widget area
								'tc_theme_options[tc_show_featured_pages]'	=> array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'title'   		=> __( 'Featured pages options' , 'customizr' ),
																	'label'   		=> __( 'Display home featured pages area' , 'customizr' ),
																	'section' 		=> 'tc_frontpage_settings' ,
																	'type'    		=> 'select' ,	
																	'choices' 		=> array(
																					1 => __( 'Enable' , 'customizr' ),
																					0 => __( 'Disable' , 'customizr' ),
																	),
																	'priority'      	=> 55,
								),

								//display featured page images
								'tc_theme_options[tc_show_featured_pages_img]' => array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Show images' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'The images are set with the "featured image" of each pages (in the page edit screen). Uncheck the option above to disable the featured page images.' , 'customizr' ),
																	'priority'      => 60,
								),

								//display featured page images
								'tc_theme_options[tc_featured_page_button_text]' => array(
																	'default'       => __( 'Read more &raquo;' , 'customizr' ),
																	'transport'     =>  'postMessage',
																	'label'    		=> __( 'Button text' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'text' ,
																	'priority'      => 65,
								),

								//widget page one
								'tc_theme_options[tc_featured_page_one]' => array(
																	'label'    		=> __( 'Home featured page one' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'      	=> 'dropdown-pages' ,
																	'priority'      => 70,
								),

								//widget page two
								'tc_theme_options[tc_featured_page_two]' => array(
																	'label'    		=> __( 'Home featured page two' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'      	=> 'dropdown-pages' ,
																	'priority'      => 80,
								),

								//widget page three
								'tc_theme_options[tc_featured_page_three]' => array(
																	'label'    		=> __( 'Home featured page three' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'      	=> 'dropdown-pages' ,
																	'priority'      => 90,
								),

								//widget page text one
								'tc_theme_options[tc_featured_text_one]' => array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
																	'transport' 	=> 'postMessage',
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Featured text one (200 car. max)' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'textarea' ,
																	'notice'		=> __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
																	'priority'      => 100,
								),

								//widget page text two
								'tc_theme_options[tc_featured_text_two]' => array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
																	'transport' 	=> 'postMessage',
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Featured text two (200 car. max)' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'textarea' ,
																	'notice'		=> __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
																	'priority'      => 110,
								),

								//widget page text three
								'tc_theme_options[tc_featured_text_three]' => array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
																	'transport' 	=> 'postMessage',
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Featured text three (200 car. max)' , 'customizr' ),
																	'section'  		=> 'tc_frontpage_settings' ,
																	'type'     		=> 'textarea' ,
																	'notice'		=> __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
																	'priority'      => 120,
								),

								/*-----------------------------------------------------------------------------------------------------
														                   SITE LAYOUT
								------------------------------------------------------------------------------------------------------*/
								//Breadcrumb
								'tc_theme_options[tc_breadcrumb]' => array(
																	'default'       => 1,//Breadcrumb is checked by default
																	'label'    		=> __( 'Display Breadcrumb' , 'customizr' ),
																	'section'  		=> 'tc_layout_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'      => 1,
								),

								//Global sidebar layout
								'tc_theme_options[tc_sidebar_global_layout]' => array(
																	'default'       => 'l' ,//Default sidebar layout is on the left
																	'label'   		=> __( 'Choose the global default layout' , 'customizr' ),
																	'section' 		=> 'tc_layout_settings' ,
																	'type'    		=> 'select' ,
																	'choices'		=> array( //Same fields as in the tc_post_layout.php files
																					'r' 	=> __( 'Right sidebar' , 'customizr' ),
																					'l'  	=> __( 'Left sidebar' , 'customizr' ),
																					'b' 	=> __( '2 sidebars : Right and Left' , 'customizr' ),
																					'f'		=> __( 'No sidebars : full width layout' , 'customizr' )
																	),
																	'priority'      => 2,
								),

								//force default layout on every posts
								'tc_theme_options[tc_sidebar_force_layout]'	=>	array(
																	'default'       => 0,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Force default layout everywhere' , 'customizr' ),
																	'section'  		=> 'tc_layout_settings' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'This option will override the specific layouts on all posts/pages, including the front page.' , 'customizr' ),
																	'priority'      => 3,
								),

								//Post sidebar layout
								'tc_theme_options[tc_sidebar_post_layout]'	=>	array(
																	'default'       => 'l' ,//Default sidebar layout is on the left
																	'label'   		=> __( 'Choose the posts default layout' , 'customizr' ),
																	'section' 		=> 'tc_layout_settings' ,
																	'type'    		=> 'select' ,
																	'choices'		=> array( //Same fields as in the tc_post_layout.php files
																					'r' 	=> __( 'Right sidebar' , 'customizr' ),
																					'l'  	=> __( 'Left sidebar' , 'customizr' ),
																					'b' 	=> __( '2 sidebars : Right and Left' , 'customizr' ),
																					'f'		=> __( 'No sidebars : full width layout' , 'customizr' )
																					),
																	'priority'      => 4,
								),

								//Post per page
								'posts_per_page'	=>	array(
																	'default'    	=> get_option( 'posts_per_page' ),
																	'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
																	'control'		=> 'TC_controls' ,
																	'label'   		=> __( 'Maximum number of posts per page' , 'customizr' ),
																	'section' 		=> 'tc_layout_settings' ,
																	'type'    		=> 'number' ,
																	'step'			=> 1,
																	'min'			=> 1,
																	//'priority'       => 8,
								),

								//Page sidebar layout
								'tc_theme_options[tc_sidebar_page_layout]'	=>	array(
																	'default'       => 'l' ,//Default sidebar layout is on the left
																	'label'   		=> __( 'Choose the pages default layout' , 'customizr' ),
																	'section' 		=> 'tc_layout_settings' ,
																	'type'    		=> 'select' ,
																	'choices'		=> array( //Same fields as in the tc_post_layout.php files
																					'r' 	=> __( 'Right sidebar' , 'customizr' ),
																					'l'  	=> __( 'Left sidebar' , 'customizr' ),
																					'b' 	=> __( '2 sidebars : Right and Left' , 'customizr' ),
																					'f'		=> __( 'No sidebars : full width layout' , 'customizr' )
																	//'priority'       => 6,
																	),
								),

								/*-----------------------------------------------------------------------------------------------------
														                   COMMENTS SETTINGS
								------------------------------------------------------------------------------------------------------*/
								'tc_theme_options[tc_page_comments]'	=>	array(
																	'default'       => 0,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Enable comments on pages' , 'customizr' ),
																	'section'  		=> 'tc_page_comments' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
								),


								/*-----------------------------------------------------------------------------------------------------
														             SOCIAL POSITIONS AND NETWORKS
								------------------------------------------------------------------------------------------------------*/
								//Position checkboxes
								'tc_theme_options[tc_social_in_header]'	=>	array(
																	'default'       => 1,
																	'label'    		=> __( 'Social links in header' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'       => 10
								),

								'tc_theme_options[tc_social_in_left-sidebar]'	=>	array(
																	'default'       => 0,
																	'label'    		=> __( 'Social links in left sidebar' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'       => 20
								),

								'tc_theme_options[tc_social_in_right-sidebar]'	=>	array(
																	'default'       => 0,
																	'label'    		=> __( 'Social links in right sidebar' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'       => 30
								),

								'tc_theme_options[tc_social_in_footer]'	=>	array(
																	'default'       => 1,
																	'label'    		=> __( 'Social links in footer' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'checkbox' ,
																	'priority'       => 40
								),
								

								//Networks
								'tc_theme_options[tc_rss]'	=>	array(
																	'default'       => get_bloginfo( 'rss_url' ),
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'RSS feed (default is the wordpress feed)' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url',
																	'priority'       => 50
								),

								'tc_theme_options[tc_twitter]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Twitter profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url',
																	'priority'       => 60
								),

								'tc_theme_options[tc_facebook]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Facebook profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 70
								),

								'tc_theme_options[tc_google]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Google+ profile url' , 'customizr' ), 
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 74 
								),

								'tc_theme_options[tc_instagram]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Instagram profile url' , 'customizr' ), 
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 78 
								),

								'tc_theme_options[tc_wordpress]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'WordPress profile url' , 'customizr' ), 
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 80 
								),

								'tc_theme_options[tc_youtube]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Youtube profile url' , 'customizr' ), 
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 90 
								),

								'tc_theme_options[tc_pinterest]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Pinterest profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 100 
								),

								'tc_theme_options[tc_github]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Github profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 110 
								),

								'tc_theme_options[tc_dribbble]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Dribbble profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 120 
								),

								'tc_theme_options[tc_linkedin]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'LinkedIn profile url' , 'customizr' ),
																	'section'  		=> 'tc_social_settings' ,
																	'type'     		=> 'url' ,
																	'priority'       => 130 
								),


								/*-----------------------------------------------------------------------------------------------------
														                   IMAGE SETTINGS
								------------------------------------------------------------------------------------------------------*/
								'tc_theme_options[tc_fancybox]'	=>	array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Enable/disable lightbox effect on images' , 'customizr' ),
																	'section'  		=> 'tc_image_settings' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'If enabled, this option activate a popin window whith a zoom effect when an image is clicked. This will not apply to image gallery.' , 'customizr' ),
								),

								'tc_theme_options[tc_fancybox_autoscale]'	=>	array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Autoscale images on zoom' , 'customizr' ),
																	'section'  		=> 'tc_image_settings' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'If enabled, this option will force images to fit the screen on lightbox zoom.' , 'customizr' ),
								),

								/*-----------------------------------------------------------------------------------------------------
														                   PLUGINS COMPATIBILITY
								------------------------------------------------------------------------------------------------------*/
								/*'tc_theme_options[tc_woocommerce_compatibility]'	=>	array(
																	'default'       => 1,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Enable Woocommerce compatibility' , 'customizr' ),
																	'section'  		=> 'tc_plugins_compatibility' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'If enabled, Customizr will use Woommerce specific hooks to help you build your online shop.' , 'customizr' ),
								),*/



								/*-----------------------------------------------------------------------------------------------------
														                   CUSTOM CSS
								------------------------------------------------------------------------------------------------------*/
								'tc_theme_options[tc_custom_css]'	=>	array(
																	'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
																	'section'  		=> 'tc_custom_css' ,
																	'type'     		=> 'textarea' ,
																	'notice'		=> __( 'Always use this field to add your custom css instead of editing directly the style.css file : it will not be deleted during theme updates. You can also paste your custom css in the style.css file of a child theme.' , 'customizr' )
								),

								/*-----------------------------------------------------------------------------------------------------
														                   DEVELOPER TOOLS
								------------------------------------------------------------------------------------------------------*/
								'dev_box_title'					=> array(
																	'setting_type'	=> 	null,
																	'control'		=>	'TC_controls' ,
																	'title'   		=> __( 'Developer Box' , 'customizr' ),
																	'section' 		=> 'tc_debug_section' ,
																	'type'			=> 'title' ,
																	//'priority'      => 0,
								),

								'tc_theme_options[tc_debug_box]'	=>	array(
																	'default'       => 0,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Only visible for logged in users with an admin profile.' , 'customizr' ),
																	'section'  		=> 'tc_debug_section' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'If enabled, this option displays a draggable information box to help you develop or debug your Customizr based website.' , 'customizr' ),
								),

								'dev_tooltip_title'					=> array(
																	'setting_type'	=> 	null,
																	'control'		=>	'TC_controls' ,
																	'title'   		=> __( 'Developer tooltips' , 'customizr' ),
																	'section' 		=> 'tc_debug_section' ,
																	'type'			=> 'title' ,
																	//'priority'      => 0,
								),

								'tc_theme_options[tc_debug_tips]'	=>	array(
																	'default'       => 0,
																	'control'		=> 'TC_controls' ,
																	'label'    		=> __( 'Only visible for logged in users with an admin profile.' , 'customizr' ),
																	'section'  		=> 'tc_debug_section' ,
																	'type'     		=> 'checkbox' ,
																	'notice'		=> __( 'If enabled, this option displays clickable contextual tooltips right inside your website.' , 'customizr' ),
								),

								'tc_theme_options[tc_debug_tips_color]'  => array(
																	'default'    	=> '#F00',
																	'transport' 	=> 'postMessage' ,
																	'sanitize_callback'    => array( $this, 'tc_sanitize_hex_color' ),
																	//'sanitize_js_callback' => 'maybe_hash_hex_color' ,
																	'control'		=> 'WP_Customize_Color_Control' ,
																	'label'   		=> __( 'Tip icon color', 'customizr'),
																	'section' 		=> 'tc_debug_section'
								)
			),//end of add_setting_control array

		);//end of customize_array

	return $customize_array;

	}//end of customize_setup function





	/**
	 * Defines arguments for sections, settings and controls
	 * @package Customizr
	 * @since Customizr 3.0 
	 */
	function tc_customize_arguments() {
		$args = array(
				'sections' => array(
							'title' ,
							'priority' ,
							'description'
				),
				'settings' => array(
							'default'			=>	null,
							'capability'		=>	'manage_options' ,
							'setting_type'		=>	'option' ,
							'sanitize_callback'	=>	null,
							'transport'			=>	null
				),
				'controls' => array(
							'title' ,
							'label' ,
							'section' ,
							'settings' ,
							'type' ,
							'choices' ,
							'priority' ,
							'sanitize_callback' ,
							'notice' ,
							'buttontext' ,//button specific
							'link' ,//button specific
							'step' ,//number specific
							'min' ,//number specific
							'range-input' ,
							'max',
							'cssid',
							'slider_default'
				)
		);
		return $args;
	}





	/**
	 * Generates customizer
	 * @package Customizr
	 * @since Customizr 3.0 
	 */
	function tc_customize_factory ( $wp_customize , $args, $setup ) {

		//remove sections
		if ( isset( $setup['remove_section'])) {
			foreach ( $setup['remove_section'] as $section) {
				$wp_customize	-> remove_section( $section);
			}
		}



		//add sections
		if ( isset( $setup['add_section'])) {
			foreach ( $setup['add_section'] as  $key => $options) {
				//generate section array
				$option_section = array();

				foreach( $args['sections'] as $sec) {
					$option_section[$sec] = isset( $options[$sec]) ?  $options[$sec] : null;
				}

				//add section
				$wp_customize	-> add_section( $key,$option_section);
			}//end foreach
		}//end if


		//get_settings
		if ( isset( $setup['get_setting'])) {
			foreach ( $setup['get_setting'] as $setting) {
				$wp_customize	-> get_setting( $setting )->transport = 'postMessage';
			}
		}



		//add settings and controls
		if ( isset( $setup['add_setting_control'])) {

			foreach ( $setup['add_setting_control'] as $key => $options) {

				//generate settings array
				$option_settings = array();
				foreach( $args['settings'] as $set => $set_value) {
					if ( $set == 'setting_type' ) {
						$option_settings['type'] = isset( $options['setting_type']) ?  $options['setting_type'] : $args['settings'][$set];
					}
					else {
						$option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
					}
				}

				//add setting
				$wp_customize	-> add_setting( $key, $option_settings);
			
				//generate controls array
				$option_controls = array();
				foreach( $args['controls'] as $con) {
					$option_controls[$con] = isset( $options[$con]) ?  $options[$con] : null;
				}

				//add control with a dynamic class instanciation if not default
				if(!isset( $options['control'])) {
						$wp_customize	-> add_control( $key,$option_controls );
				}
				else {
						$wp_customize	-> add_control( new $options['control']( $wp_customize, $key, $option_controls ));
				}

			}//end for each
		}//end if isset

	}//end of customize generator function





	/**
	 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 * @package Customizr
	 * @since Customizr 1.0 
	 */
	function tc_customize_preview_js() {
		wp_enqueue_script( 'tc-customizer' , get_template_directory_uri() . '/inc/admin/js/theme-customizer-preview.js' , array( 'customize-preview' ), '20120827' , true );
	}




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



}//end of class