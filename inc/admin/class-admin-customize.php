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
		
		//control scripts and style
		add_action ( 'customize_controls_enqueue_scripts'	, array( $this , 'tc_customize_controls_js_css' ));

		//add the customizer built with the builder below
		add_action ( 'customize_register'				, array( $this , 'tc_customize_register' ) , 20, 1 );

		//preview scripts
		add_action ( 'customize_preview_init'			, array( $this , 'tc_customize_preview_js' ));

		//add option to menus
		add_action ( 'wp_before_admin_bar_render'		, array( $this , 'tc_add_admin_bar_options_menu' ));
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
		       'parent' 	=> false,
		       'id' 		=> 'tc-customizr' ,
		       'title' 		=>  __( 'Customiz\'it!' , 'customizr' ),
		       'href' 		=> admin_url( 'customize.php' ),
		       'meta'   	=> array(
	           'title'  	=> __( 'Customize your website at any time!', 'customizr' ),
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



	function tc_get_skins($path) {
		//checks if path exists
		if ( !file_exists($path) )
			return;

		//gets the skins from init
		$default_skin_list		= TC_init::$instance -> skins;

		//declares the skin list array
		$skin_list 				= array();

		//gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
		$files      			= scandir($path) ;
		foreach ( $files as $file) {
	        if ( $file[0] != '.' && !is_dir($path.$file) ) {
	        	if ( substr( $file, -4) == '.css' ) {
	        		$skin_list[$file] = isset($default_skin_list[$file]) ? $default_skin_list[$file] : substr_replace( $file , '' , -4 , 4);
	        	}
	        }
	    }//endforeach

	    return $skin_list;
	}//end of function

	



	/**
	 * Returns the list of available skins from child (if exists) and parent theme
	 * 
	 * @package Customizr
	 * @since Customizr 3.0.11
	 * @updated Customizr 3.0.15
	 */
	function tc_skin_choices() {
	    $parent_skins 		= $this -> tc_get_skins(TC_BASE .'inc/css');
	    $child_skins		= ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/css') ) ? $this -> tc_get_skins(TC_BASE_CHILD .'inc/css') : array();
	    $skin_list 			= array_merge( $parent_skins , $child_skins );

		return apply_filters( 'tc_skin_list', $skin_list );
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
	* Returns the layout choices array
	* 
	* @package Customizr
	* @since Customizr 3.1.0
	*/
	function tc_layout_choices() {
	   	$global_layout 	= TC_init::$instance -> global_layout;
	   	$layout_choices = array(); 
	   	foreach ($global_layout as $key => $value) {
	   		$layout_choices[$key] 	= ( $value['customizer'] ) ? call_user_func(  '__' , $value['customizer'] , 'customizr' ) : null ;
	   	}
	   	return $layout_choices;
	}





	/**
	 * Generates the featured pages options
	 * 
	 * @package Customizr
	 * @since Customizr 3.0.15
	 * 
	 */
	function tc_generates_featured_pages() {
		$default = array(
			'dropdown' 	=> 	array(
						'one' 	=> __( 'Home featured page one' , 'customizr' ),
						'two' 	=> __( 'Home featured page two' , 'customizr' ),
						'three' => __( 'Home featured page three' , 'customizr' )
			),
			'text'		=> array(
						'one' 	=> __( 'Featured text one (200 car. max)' , 'customizr' ),
						'two' 	=> __( 'Featured text two (200 car. max)' , 'customizr' ),
						'three' => __( 'Featured text three (200 car. max)' , 'customizr' )
			)
		);

		//declares some loop's vars and the settings array
		$priority 			= 70;
		$incr 				= 0;
		$fp_setting_control	= array();

		//gets the featured pages id from init
		$fp_ids				= TC_init::$instance -> fp_ids;

		//dropdown field generator
		foreach ( $fp_ids as $id ) {
			$priority = $priority + $incr;
			$fp_setting_control['tc_theme_options[tc_featured_page_'. $id.']'] 		=  array(
										'label'    		=> isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  'Custom featured page ' . $id,
										'section'  		=> 'tc_frontpage_settings' ,
										'type'      	=> 'dropdown-pages' ,
										'priority'      => $priority
									);
			$incr += 10;
		}

		//text field generator
		$incr 				= 10;
		foreach ( $fp_ids as $id ) {
			$priority = $priority + $incr;
			$fp_setting_control['tc_theme_options[tc_featured_text_' . $id . ']'] 	= array(
										'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
										'transport' 	=> 'postMessage',
										'control'		=> 'TC_controls' ,
										'label'    		=> isset($default['text'][$id]) ? $default['text'][$id] : 'Featured text two ' . $id,
										'section'  		=> 'tc_frontpage_settings' ,
										'type'     		=> 'textarea' ,
										'notice'		=> __( 'You need to select a page first. Leave this field empty if you want to use the page excerpt.' , 'customizr' ),
										'priority'      => $priority,
									);
			$incr += 10;
		}

		return $fp_setting_control;
	}


	function tc_generates_socials() {
		//gets the social network array
		$socials 			= TC_init::$instance -> socials;

		//declares some loop's vars and the settings array
		$priority 			= 50;//start priority
		$incr 				= 0;
		$socials_setting_control	= array();

		foreach ( $socials as $key => $data ) {
			$priority += $incr;
			$socials_setting_control['tc_theme_options[' . $key . ']'] 	= array(
										'default'       	=> ( isset($data['default']) && !is_null($data['default']) ) ? $data['default'] : null ,
										'sanitize_callback' => array( $this , 'tc_sanitize_url' ),
										'control'			=> 'TC_controls' ,
										'label'    			=> ( isset($data['option_label']) ) ? $data['option_label'] : $key,
										'section'  			=> 'tc_social_settings' ,
										'type'     			=> 'url',
										'priority'      	=> $priority
									);
			$incr += 5;
		}

		return $socials_setting_control;
	}



	/**
	* Defines sections, settings and function of customizer and return and array
	* Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop) 
	*	
	* @package Customizr
	* @since Customizr 3.0 
	*/
	function tc_customizer_map( $get_default = null ) {

		//customizer option array
		$remove_section = array(
						'remove_section'  		 =>   array(
												'background_image' ,
												'static_front_page' ,
												'colors'
						)
		);//end of remove_sections array
		$remove_section = apply_filters( 'tc_remove_section_map', $remove_section );



		$add_section = array(
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
																			'description'	=>	__( 'Various images settings' , 'customizr' ),
										),

										'tc_links_settings'					=> array(
																			'title'			=>	__( 'Links' , 'customizr' ),
																			'priority'		=>	190,
																			'description'	=>	__( 'Various links settings' , 'customizr' ),
										),

										'tc_custom_css'						=> array(
																			'title'			=>	__( 'Custom CSS' , 'customizr' ),
																			'priority'		=>	200,
																			'description'	=>	__( 'Add your own CSS' , 'customizr' ),
										),

										'tc_debug_section'					=> array(
																			'title'			=>	__( 'Dev Tools (advanced users)' , 'customizr' ),
																			'priority'		=>	210,
																			'description'	=>	__( 'Enable/disable the Dev Tools' , 'customizr' ),
										)
						)

		);//end of add_sections array
		$add_section = apply_filters( 'tc_add_section_map', $add_section );




		//specifies the transport for some options
		$get_setting 		= array(
						'get_setting'   		=>   array(
										'blogname' ,
										'blogdescription'
						)
		);//end of get_setting array
		$get_setting = apply_filters( 'tc_get_setting_map', $get_setting );




		/*-----------------------------------------------------------------------------------------------------
												NAVIGATION SECTION
		------------------------------------------------------------------------------------------------------*/	
		$navigation_option_map = array(					
						'menu_button'						=> array(
															'setting_type'	=> 	null,
															'control'		=>	'TC_controls' ,
															'section'		=>	'nav' ,
															'type'			=>	'button' ,
															'link'			=>	'nav-menus.php' ,
															'buttontext'	=> __( 'Manage menus' , 'customizr' ),
						),
						//The hover menu type has been introduced in v3.1.0. 
	 					//For users already using the theme (no theme's option set), the default choice is click, for new users, it is hover.
						'tc_theme_options[tc_menu_type]'	=> array(
															'default'		=>	( false == get_option('tc_theme_options') ) ? 'hover' : 'click' ,
															'label'			=>	__( 'Select a submenu expansion option' , 'customizr' ),
															'section'		=>	'nav' ,
															'type'			=>	'select' ,
															'choices' 		=> array(
																			'click' 	=> __( 'Expand submenus on click' , 'customizr'),
																			'hover' 	=> __( 'Expand submenus on hover' , 'customizr'  ),
															),
						),
		); //end of navigation options
		$navigation_option_map = apply_filters( 'tc_navigation_option_map', $navigation_option_map );



		/*-----------------------------------------------------------------------------------------------------
								                  		SKIN SECTION
		------------------------------------------------------------------------------------------------------*/
		$skin_option_map 		= array(
						//skin select
						'tc_theme_options[tc_skin]'			=> array(
															'default'		=>	'blue.css' ,
															'label'			=>	__( 'Choose a predefined skin' , 'customizr' ),
															'section'		=>	'tc_skins_settings' ,
															'type'			=>	'select' ,
															'choices'		=>	$this -> tc_skin_choices()
						),

						//enable/disable top border
						'tc_theme_options[tc_top_border]'	=> array(
															'default'		=>	1,//top border on by default
															'label'			=>	__( 'Display top border' , 'customizr' ),
															'control'		=>	'TC_controls' ,
															'section'		=>	'tc_skins_settings' ,
															'type'			=>	'checkbox' ,
															'notice'		=>	__( 'Uncheck this option to remove the colored top border.' , 'customizr' ),
						)
		);//end of skin options
		apply_filters( 'tc_skin_option_map', $skin_option_map );


		/*-----------------------------------------------------------------------------------------------------
								                   LOGO & FAVICON SECTION
		------------------------------------------------------------------------------------------------------*/
		$logo_favicon_option_map = array(
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
															'control'		=>	'TC_controls' ,
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
						)
		);
		$logo_favicon_option_map = apply_filters( 'tc_logo_favicon_option_map', $logo_favicon_option_map );



		/*-----------------------------------------------------------------------------------------------------
								                   FRONT PAGE SETTINGS
		------------------------------------------------------------------------------------------------------*/		
		$front_page_option_map = array(
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
															'choices'		=> $this -> tc_layout_choices(),
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
															'choices' 		=> ($get_default == true) ? null : $this -> tc_slider_choices(),
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
						)

		);//end of front_page_options
		$front_page_option_map = array_merge( $front_page_option_map , $this -> tc_generates_featured_pages() );
		$front_page_option_map = apply_filters( 'tc_front_page_option_map', $front_page_option_map );





		/*-----------------------------------------------------------------------------------------------------
								                   SITE LAYOUT
		------------------------------------------------------------------------------------------------------*/		
		$layout_option_map = array(
						//Breadcrumb
						'tc_theme_options[tc_breadcrumb]' => array(
															'default'       => 1,//Breadcrumb is checked by default
															'label'    		=> __( 'Display Breadcrumb' , 'customizr' ),
															'control'		=>	'TC_controls' ,
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
															'choices'		=> $this -> tc_layout_choices(),
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
															'choices'		=> $this -> tc_layout_choices(),
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

						//Post list length
						'tc_theme_options[tc_post_list_length]'	=>	array(
															'default'    	=> 'excerpt',
															'label'   		=> __( 'Select the length of posts in lists (home, search, archives, ...)' , 'customizr' ),
															'section' 		=> 'tc_layout_settings' ,
															'type'    		=> 'select' ,
															'choices'		=> array(
																			'excerpt' 	=> __( 'Display the excerpt' , 'customizr' ),
																			'full'  	=> __( 'Display the full content' , 'customizr' )
																			)
															//'priority'       => 6,
						),

						//Page sidebar layout
						'tc_theme_options[tc_sidebar_page_layout]'	=>	array(
															'default'       => 'l' ,//Default sidebar layout is on the left
															'label'   		=> __( 'Choose the pages default layout' , 'customizr' ),
															'section' 		=> 'tc_layout_settings' ,
															'type'    		=> 'select' ,
															'choices'		=> $this -> tc_layout_choices(),
															//'priority'       => 6,
															),
		);//end of layout_options
		$layout_option_map = apply_filters( 'tc_layout_option_map', $layout_option_map );




		/*-----------------------------------------------------------------------------------------------------
								                   COMMENTS SETTINGS
		------------------------------------------------------------------------------------------------------*/		
		$comment_option_map = array(

						'tc_theme_options[tc_page_comments]'	=>	array(
															'default'       => 0,
															'control'		=> 'TC_controls' ,
															'label'    		=> __( 'Enable comments on pages' , 'customizr' ),
															'section'  		=> 'tc_page_comments' ,
															'type'     		=> 'checkbox' ,
															'notice'		=> __( 'This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
						)
		);
		$comment_option_map = apply_filters( 'tc_comment_option_map', $comment_option_map );



		/*-----------------------------------------------------------------------------------------------------
								             SOCIAL POSITIONS AND NETWORKS
		------------------------------------------------------------------------------------------------------*/
		$social_layout_map = array(
						//Position checkboxes
						'tc_theme_options[tc_social_in_header]'	=>	array(
															'default'       => 1,
															'label'    		=> __( 'Social links in header' , 'customizr' ),
															'control'		=>	'TC_controls' ,
															'section'  		=> 'tc_social_settings' ,
															'type'     		=> 'checkbox' ,
															'priority'      => 10
						),

						'tc_theme_options[tc_social_in_left-sidebar]'	=>	array(
															'default'       => 0,
															'label'    		=> __( 'Social links in left sidebar' , 'customizr' ),
															'control'		=>	'TC_controls' ,
															'section'  		=> 'tc_social_settings' ,
															'type'     		=> 'checkbox' ,
															'priority'       => 20
						),

						'tc_theme_options[tc_social_in_right-sidebar]'	=>	array(
															'default'       => 0,
															'label'    		=> __( 'Social links in right sidebar' , 'customizr' ),
															'control'		=>	'TC_controls' ,
															'section'  		=> 'tc_social_settings' ,
															'type'     		=> 'checkbox' ,
															'priority'       => 30
						),

						'tc_theme_options[tc_social_in_footer]'	=>	array(
															'default'       => 1,
															'label'    		=> __( 'Social links in footer' , 'customizr' ),
															'control'		=>	'TC_controls' ,
															'section'  		=> 'tc_social_settings' ,
															'type'     		=> 'checkbox' ,
															'priority'       => 40
						)
		);//end of social layout map
						
		$social_option_map = array_merge( $social_layout_map , $this -> tc_generates_socials() );
		$social_option_map = apply_filters( 'tc_social_option_map', $social_option_map );




		/*-----------------------------------------------------------------------------------------------------
								                   IMAGE SETTINGS
		------------------------------------------------------------------------------------------------------*/
		$images_option_map = array(
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

						'tc_theme_options[tc_retina_support]'	=>	array(
															'default'       => 1,
															'control'		=> 'TC_controls' ,
															'label'    		=> __( 'Enable/disable retina support' , 'customizr' ),
															'section'  		=> 'tc_image_settings' ,
															'type'     		=> 'checkbox' ,
															'notice'		=> __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
						)
		);//end of images options
		$images_option_map = apply_filters( 'tc_images_option_map', $images_option_map );


		/*-----------------------------------------------------------------------------------------------------
								                   IMAGE SETTINGS
		------------------------------------------------------------------------------------------------------*/
		$links_option_map = array(
						'tc_theme_options[tc_link_scroll]'	=>	array(
															'default'       => 0,
															'control'		=> 'TC_controls' ,
															'label'    		=> __( 'Enable/disable smooth scroll on click' , 'customizr' ),
															'section'  		=> 'tc_links_settings' ,
															'type'     		=> 'checkbox' ,
															'notice'		=> __( 'If enabled, this option activates a smooth page scroll when clicking on a link to an anchor of the same page.' , 'customizr' ),
						)
		);//end of links options
		$links_option_map = apply_filters( 'tc_links_option_map', $links_option_map );

		/*-----------------------------------------------------------------------------------------------------
								                   CUSTOM CSS
		------------------------------------------------------------------------------------------------------*/
		$custom_css_option_map = array(

						'tc_theme_options[tc_custom_css]'	=>	array(
															'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
															'control'		=> 'TC_controls' ,
															'label'    		=> __( 'Add your custom css here and design live! (for advanced users)' , 'customizr' ),
															'section'  		=> 'tc_custom_css' ,
															'type'     		=> 'textarea' ,
															'notice'		=> __( 'Always use this field to add your custom css instead of editing directly the style.css file : it will not be deleted during theme updates. You can also paste your custom css in the style.css file of a child theme.' , 'customizr' )
						)
		);//end of custom_css_options
		$custom_css_option_map = apply_filters( 'tc_custom_css_option_map', $custom_css_option_map );

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
							$custom_css_option_map,
							apply_filters( 'tc_custom_setting_control', array() )
						)
		);
		$add_setting_control = apply_filters( 'tc_add_setting_control_map', $add_setting_control );

		//merges all customizer arrays
		$customizer_map = array_merge( $remove_section , $add_section , $get_setting , $add_setting_control );

		return apply_filters( 'tc_customizer_map', $customizer_map );

	}//end of tc_customizer_map function





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
							'slider_default',
				)
		);
		return apply_filters( 'tc_customizer_arguments', $args );
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
				//isolates the option name for the setting's filter
				$f_option_name = 'setting';
				$f_option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
	            if ( isset( $match[1][0] ) ) {$f_option_name = $match[1][0];}

				//declares settings array
				$option_settings = array();
				foreach( $args['settings'] as $set => $set_value) {
					if ( $set == 'setting_type' ) {
						$option_settings['type'] = isset( $options['setting_type']) ?  $options['setting_type'] : $args['settings'][$set];
						$option_settings['type'] = apply_filters( $f_option_name .'_customizer_set', $option_settings['type'] , $set );
					}
					else {
						$option_settings[$set] = isset( $options[$set]) ?  $options[$set] : $args['settings'][$set];
						$option_settings[$set] = apply_filters( $f_option_name .'_customizer_set' , $option_settings[$set] , $set );
					}
				}

				//add setting
				$wp_customize	-> add_setting( $key, $option_settings );
			
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
		wp_enqueue_script( 
			'tc-customizer-preview' ,
			get_template_directory_uri() . '/inc/admin/js/theme-customizer-preview.js' ,
			array( 'customize-preview' ),
			'20120827' ,
			true );
	}



	/**
	 * Add script to controls
	 * Dependency : customize-controls located in wp-includes/script-loader.php
	 * Hooked on customize_controls_enqueue_scripts located in wp-admin/customize.php
	 * @package Customizr
	 * @since Customizr 3.1.0
	 */
	function tc_customize_controls_js_css() {

		wp_register_style( 
			'tc-customizer-controls-style' ,
			get_template_directory_uri() . '/inc/admin/css/theme-customizer-control.css' ,
			array( 'customize-controls' ),
			null,
			$media = 'all'
		);
		wp_enqueue_style('tc-customizer-controls-style');
		wp_enqueue_script( 
			'tc-customizer-controls' ,
			get_template_directory_uri() . '/inc/admin/js/theme-customizer-control.js' ,
			array( 'customize-controls' ),
			CUSTOMIZR_VER ,
			true
			);

		//gets the featured pages id from init
		$fp_ids				= TC_init::$instance -> fp_ids;

		//declares the common fp control fields and the dynamic arrays
		$fp_controls 			= array(
			'tc_theme_options[tc_show_featured_pages_img]',
			'tc_theme_options[tc_featured_page_button_text]'
		);
		$page_dropdowns 		= array();
		$text_fields			= array();

		//adds filtered page dropdown fields
		foreach ( $fp_ids as $id ) {
			$page_dropdowns[] 	= 'tc_theme_options[tc_featured_page_'. $id.']';
			$text_fields[]		= 'tc_theme_options[tc_featured_text_'. $id.']';
		}

		//localizes
		wp_localize_script( 
	        'tc-customizer-controls', 
	        'TCControlParams', 
		        array(
		        	'FPControls' => array_merge( $fp_controls , $page_dropdowns , $text_fields )
		        )
        );
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