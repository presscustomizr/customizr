<?php

if(!function_exists('tc_add_options_menu')) :
add_action ('admin_menu', 'tc_add_options_menu');
/**
 * Add WordPress customizer page to the admin menu.
 * @package Customizr
 * @since Customizr 1.0 
 */
	function tc_add_options_menu() {
	    $theme_page = add_theme_page(
	        __( 'Customiz\'it!', 'customizr' ),   // Name of page
	        __( 'Customiz\'it!', 'customizr' ),   // Label in menu
	        'edit_theme_options',          // Capability required
	        'customize.php'             // Menu slug, used to uniquely identify the page
	    );
	}
endif;




if(!function_exists('tc_add_admin_bar_options_menu')) :
add_action( 'wp_before_admin_bar_render', 'tc_add_admin_bar_options_menu' );
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
	       'id' => 'theme_editor_admin_bar',
	       'title' =>  __( 'Customiz\'it!', 'customizr' ),
	       'href' => admin_url( 'customize.php')
	     ));
	   }
	}
endif;




if(!function_exists('tc_add_control')) :
add_action('customize_register', 'tc_add_control',10,1);
 /**
 * adds controls to customizer
 * @package Customizr
 * @since Customizr 1.0 
 */
	function tc_add_control($type) {
		require_once( 'tc_customizr_control_class.php');
	}
endif;




if(!function_exists('tc_customize_register')) :
add_action( 'customize_register', 'tc_customize_register',20,1 );
 /**
 * Customize the customizer : add settings and controls
 * @package Customizr
 * @since Customizr 1.0 
 */
function tc_customize_register( $wp_customize ) {

	//Remove unecessary defaults controls, settings and sections
	$wp_customize-> remove_section('background_image');
	$wp_customize-> remove_section('static_front_page');
	$wp_customize-> remove_section('colors');
	//$wp_customize-> remove_settings();

	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	
	//Add button to Navigation Section
	$wp_customize->add_setting( 'menu_button', array(
		'capability'  	=> 'manage_options',
	) );
 
	$wp_customize->add_control( new TC_Controls($wp_customize, 'menu_button', array(
		'section' 		=> 'nav',
		'tc'			=> 'button',
		'link'			=> 'nav-menus.php',
		'buttontext'	=> __('Manage menus','customizr'),
	)));

	//SKINS
 	$wp_customize->add_section( 'tc_skins_settings', array(
        'title'          => __('Skin','customizr'),
        'priority'       => 10,
        'description'    => __( 'Select a skin for Customizr','customizr' ),
    ) );
	 	//skin select
	 	$wp_customize->add_setting( 'tc_theme_options[tc_skin]', array(
			'default'        => 'blue.css',//Default skin
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_skin]', array(
			'label'   	=> __( 'Choose a predefined skin','customizr' ),
			'section' 	=> 'tc_skins_settings',
			'type'    => 'select',
			'choices'	=> array( //Same fields as in the tc_post_layout.php files
					'blue.css' 		=> 	__( 'Blue','customizr' ),
					'green.css'  	=> 	__( 'Green','customizr' ),
					'yellow.css' 	=> 	__( 'Yellow','customizr' ),
					'orange.css' 	=> 	__( 'Orange','customizr' ),
					'red.css'		=> 	__( 'Red','customizr' ),
					'purple.css'	=> 	__( 'Purple','customizr' ),
					'grey.css'		=>	__( 'Grey','customizr' )
			),
			//'priority'       => 2,
		) );

	//LOGO & Favicon
 	$wp_customize->add_section( 'tc_logo_settings', array(
        'title'          => __('Logo &amp; Favicon','customizr'),
        'priority'       => 20,
        'description'    => __( 'Set up logo and favicon options','customizr' ),
    ) );
	 	
	 	
	 	//logo
	 	$wp_customize->add_setting('tc_theme_options[tc_logo_upload]', array(
	    //'default'           => 'image.jpg',
	    'capability'        => 'edit_theme_options',
	    'type'           => 'option',
		));

		$wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, 'tc_logo_upload', array(
		    'label'    => __('Logo Upload', 'customizr'),
		    'section'  => 'tc_logo_settings',
		    'settings' => 'tc_theme_options[tc_logo_upload]',
		)));

		//force logo resize 250 * 85
		$wp_customize->add_setting( 'tc_theme_options[tc_logo_resize]', array(
			'default'        => 1,
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );
		$wp_customize->add_control( 'tc_theme_options[tc_logo_resize]', array(
			'settings' => 'tc_theme_options[tc_logo_resize]',
			'label'    => __( 'Force logo dimensions to max-width:250px and max-height:100px','customizr' ),
			'section'  => 'tc_logo_settings',
			'type'     => 'checkbox',
		) );
		

		//hr
	 	$wp_customize->add_setting( 'hr_logo', array(
			'capability'  	=> 'manage_options',
		) );
	 
		$wp_customize->add_control( new TC_Controls($wp_customize, 'hr_logo', array(
			'section' 		=> 'tc_logo_settings',
			'tc'			=> 'hr',
		)));


		//favicon
		$wp_customize->add_setting('tc_theme_options[tc_fav_upload]', array(
		    //'default'           => 'image.jpg',
		    'capability'        => 'edit_theme_options',
		    'type'           => 'option',
			));

		$wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, 'tc_fav_upload', array(
		    'label'    => __('Favicon Upload', 'customizr'),
		    'section'  => 'tc_logo_settings',
		    'settings' => 'tc_theme_options[tc_fav_upload]',
		)));

	//$WP_Customize_Image_Control -> remove_tab('uploaded');

	// FRONT PAGE SETTINGS //
	$wp_customize->add_section( 'tc_frontpage_settings', array(
		'title'          => __( 'Front Page','customizr' ),
		'priority'       => 30,
		'description'    => __( 'Set up front page options','customizr' ),
	) );

	
		$wp_customize->add_setting( 'show_on_front', array(
				'default'        => get_option( 'show_on_front' ),
				'capability'     => 'manage_options',
				'type'           => 'option',
				//'theme_supports' => 'static-front-page',
			) );

		$wp_customize->add_control( 'show_on_front', array(
			'label'   => __( 'Front page displays','customizr' ),
			'section' => 'tc_frontpage_settings',
			'type'    => 'select',
			'priority'       => 1,
			'choices' => array(
				'posts' => __( 'Your latest posts','customizr'  ),
				'page'  => __( 'A static page','customizr'  ),
			),
		) );

		$wp_customize->add_setting( 'page_on_front', array(
			'type'       => 'option',
			'capability' => 'manage_options',
			//'theme_supports' => 'static-front-page',
		) );

		$wp_customize->add_control( 'page_on_front', array(
			'label'      => __( 'Front page','customizr'  ),
			'section'    => 'tc_frontpage_settings',
			'type'       => 'dropdown-pages',
			'priority'       => 1,
		) );

		$wp_customize->add_setting( 'page_for_posts', array(
			'type'           => 'option',
			'capability'     => 'manage_options',
			//'theme_supports' => 'static-front-page',
		) );

		$wp_customize->add_control( 'page_for_posts', array(
			'label'      => __( 'Posts page','customizr'  ),
			'section'    => 'tc_frontpage_settings',
			'type'       => 'dropdown-pages',
			'priority'       => 1,
		) );

	//Layout
		$wp_customize->add_setting( 'tc_theme_options[tc_front_layout]', array(
			'default'        => 'f',//Default layout for home page is full width
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_front_layout]', array(
			'label'   	=> __( 'Set up the front page layout','customizr' ),
			'section' 	=> 'tc_frontpage_settings',
			'type'    => 'select',
			'choices'	=> array( //Same fields as in the tc_post_layout.php files
					'r' 	=> __( 'Right sidebar','customizr' ),
					'l'  	=> __( 'Left sidebar','customizr' ),
					'b' 	=> __( '2 sidebars : Right and Left','customizr' ),
					'f'		=> __( 'No sidebars : full width layout','customizr' )
			),
			'priority'       => 2,
		) );


	//Slider
		//retrieve slider names and generate the select list
	    $slider_names = tc_get_options( 'tc_sliders');
		$choices = array( 
			0 		=> 	__( '&mdash; No slider &mdash;','customizr' ),
			'demo' 	=>	__( '&mdash; Demo Slider &mdash;','customizr' )
			);
		if ( $slider_names ) {
			foreach($slider_names as $tc_name => $slides) {
				$choices[$tc_name] = $tc_name;
			}
		}

		//hr
	 	$wp_customize->add_setting( 'hr_slider', array(
			'capability'  	=> 'manage_options',
		) );
	 
		$wp_customize->add_control( new TC_Controls($wp_customize, 'hr_slider', array(
			'section' 		=> 'tc_frontpage_settings',
			'tc'			=> 'hr',
			'priority'       => 5,
		)));


		//select slider
		$wp_customize->add_setting( 'tc_theme_options[tc_front_slider]', array(
			'default'        => 'demo',
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_front_slider]', array(
			'label'   => __( 'Select front page slider','customizr' ),
			'section' => 'tc_frontpage_settings',
			'type'    => 'select',
			'choices' => $choices,
			//'priority'       => 6,
		)) ;

		//Slider width checkbox
		$wp_customize->add_setting( 'tc_theme_options[tc_slider_width]', array(
			'default'        	=> 1,//Slide is full width by default
			'capability'     	=> 'manage_options',
			'type'           	=> 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_slider_width]', array(
			'settings' 			=> 'tc_theme_options[tc_slider_width]',
			'label'    			=> __( 'Full width slider','customizr' ),
			'section'  			=> 'tc_frontpage_settings',
			'type'     			=> 'checkbox',
			//'priority'       	=> 1,
		) );

		//Slider Check message
		$wp_customize->add_setting( 'slider_check', array(
			'capability'  	=> 'manage_options',
		) );
	 
		$wp_customize->add_control( new TC_Controls($wp_customize, 'slider_check', array(
			'section' 		=> 'tc_frontpage_settings',
			'tc'			=> 'slider-check',
			//'priority'       => 6,
		)));

		//Delay between each slides
		$wp_customize->add_setting( 'tc_theme_options[tc_slider_delay]', array(
			'default'    		=> 5000,
			'type'       		=> 'option',
			'capability' 		=> 'manage_options',
			'sanitize_callback' => 'tc_sanitize_number'
		) );

		$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_slider_delay]', array(
			'label'   		=> __( 'Delay between each slides','customizr' ),
			'section' 		=> 'tc_frontpage_settings',
			'tc'    		=> 'number',
			'step'			=> 500,
			'min'			=> 1000,
			'notice'		=> __( 'in ms : 1000ms = 1s','customizr' ),
			//'priority'       => 8,
		)));


		//Front page widget area
		$wp_customize->add_setting( 'tc_theme_options[tc_show_featured_pages]', array(
			'default'        => 1,
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_show_featured_pages]', array(
			'label'   => __( 'Display home featured pages area','customizr' ),
			'section' => 'tc_frontpage_settings',
			'type'    => 'select',
			'choices' => array(
				1 => __( 'Show','customizr' ),
				0 => __( 'Hide','customizr' ),
			),
			'priority'       => 50,
		) );

		//WIDGETS
			//Home featured page generator
				$front_widget_areas = array (
					'one'	=> __('one','customizr'),
					'two'	=> __('two','customizr'),
					'three'	=> __('three','customizr')
					);

				//priority index
				$priority = 60;
				
				//widget page
				foreach ($front_widget_areas as $key => $area) {
					$wp_customize->add_setting( 'tc_theme_options[tc_featured_page_'.$key.']', array(
						'capability'     => 'manage_options',
						'type'           => 'option',
					) );

					$wp_customize->add_control( 'tc_theme_options[tc_featured_page_'.$key.']', array(
						'label'   		=> sprintf(__( 'Home featured page %s','customizr' ),$area),
						'section' 		=> 'tc_frontpage_settings',
						'type'      	=> 'dropdown-pages',
						'priority'      => $priority,
			
					) );
					$priority = $priority +10;
				}

				//priority index
				$priority = 100;
				
				//widget text
				foreach ($front_widget_areas as $key => $area) {
					$wp_customize->add_setting( 'tc_theme_options[tc_featured_text_'.$key.']', array(
						//'default'		 	=> 
						'capability'     	=> 'manage_options',
						'type'           	=> 'option',
						'sanitize_callback' => 'tc_sanitize_textarea',
					) );

					$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_featured_text_'.$key.']', array(
						'settings' 		=> 'tc_theme_options[tc_featured_text_'.$key.']',
						'label'    		=> sprintf(__( 'Featured text %s (200 car. max)','customizr' ),$area),
						'section'  		=> 'tc_frontpage_settings',
						'tc'     		=> 'textarea',
						'notice'		=> __('Leave this field empty if you want to use the selected page excerpt.','customizr'),
						'priority'       => $priority,
					)));
					$priority = $priority +10;
				}


	// SITE LAYOUT //
	$wp_customize->add_section( 'tc_layout_settings', array(
		'title'          => __( 'Pages & Posts Layout','customizr' ),
		'priority'       => 150,
		'description'    => __( 'Set up layout options','customizr' ),
	) );

		
		//Breadcrumb
		$wp_customize->add_setting( 'tc_theme_options[tc_breadcrumb]', array(
			'default'        => 1,//Breadcrumb is checked by default
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_breadcrumb]', array(
			'settings' => 'tc_theme_options[tc_breadcrumb]',
			'label'    => __( 'Display Breadcrumb','customizr' ),
			'section'  => 'tc_layout_settings',
			'type'     => 'checkbox',
			'priority'       => 1,
		) );


		//Global sidebar layout
		$wp_customize->add_setting( 'tc_theme_options[tc_sidebar_global_layout]', array(
			'default'        => 'l',//Default sidebar layout is on the left
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_sidebar_global_layout]', array(
			'settings' => 'tc_theme_options[tc_sidebar_global_layout]',
			'label'   	=> __( 'Choose the global default layout','customizr' ),
			'section' 	=> 'tc_layout_settings',
			'type'    	=> 'select',
			'choices'	=> array( //Same fields as in the tc_post_layout.php files
					'r' 	=> __( 'Right sidebar','customizr' ),
					'l'  	=> __( 'Left sidebar','customizr' ),
					'b' 	=> __( '2 sidebars : Right and Left','customizr' ),
					'f'		=> __( 'No sidebars : full width layout','customizr' )
					),
			'priority'       => 2,
		) );

		//force default layout on every posts
		$wp_customize->add_setting( 'tc_theme_options[tc_sidebar_force_layout]', array(
			'default'        => 0,
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );
		$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_sidebar_force_layout]', array(
			'settings' 	=> 'tc_theme_options[tc_sidebar_force_layout]',
			'label'    	=> __( 'Force default layout everywhere','customizr' ),
			'section'  	=> 'tc_layout_settings',
			'tc'     	=> 'checkbox',
			'notice'	=> __('This option will override the specific layouts on all posts/pages, including the front page.','customizr'),
			'priority'       => 3,
		) ));


		//Post sidebar layout
		$wp_customize->add_setting( 'tc_theme_options[tc_sidebar_post_layout]', array(
			'default'        => 'l',//Default sidebar layout is on the left
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_sidebar_post_layout]', array(
			'label'   	=> __( 'Choose the posts default layout','customizr' ),
			'section' 	=> 'tc_layout_settings',
			'type'    	=> 'select',
			'choices'	=> array( //Same fields as in the tc_post_layout.php files
					'r' 	=> __( 'Right sidebar','customizr' ),
					'l'  	=> __( 'Left sidebar','customizr' ),
					'b' 	=> __( '2 sidebars : Right and Left','customizr' ),
					'f'		=> __( 'No sidebars : full width layout','customizr' )
					),
			'priority'       => 4,
		)) ;
		
		//Post per page
		$wp_customize->add_setting( 'posts_per_page', array(
			'default'    		=> get_option( 'posts_per_page' ),
			'type'       		=> 'option',
			'capability' 		=> 'manage_options',
			'sanitize_callback' => 'tc_sanitize_number'
		) );

		$wp_customize->add_control( new TC_Controls($wp_customize, 'posts_per_page', array(
			'label'   		=> __( 'Maximum number of posts per page','customizr' ),
			'section' 		=> 'tc_layout_settings',
			'tc'    		=> 'number',
			'step'			=> 1,
			'min'			=> 1,
			//'priority'       => 8,
		)));


		//Page sidebar layout
		$wp_customize->add_setting( 'tc_theme_options[tc_sidebar_page_layout]', array(
			'default'        => 'l',//Default sidebar layout is on the left
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );

		$wp_customize->add_control( 'tc_theme_options[tc_sidebar_page_layout]', array(
			'label'   	=> __( 'Choose the pages default layout','customizr' ),
			'section' 	=> 'tc_layout_settings',
			'type'    	=> 'select',
			'choices'	=> array( //Same fields as in the tc_post_layout.php files
					'r' 	=> __( 'Right sidebar','customizr' ),
					'l'  	=> __( 'Left sidebar','customizr' ),
					'b' 	=> __( '2 sidebars : Right and Left','customizr' ),
					'f'		=> __( 'No sidebars : full width layout','customizr' )
			//'priority'       => 6,
			),
		) );


	// COMMENTS SETTINGS //
	$wp_customize->add_section( 'tc_page_comments', array(
		'title'          => __( 'Comments','customizr' ),
		'priority'       => 170,
		'description'    => __( 'Set up comments options','customizr' ),
	) );

		$wp_customize->add_setting( 'tc_theme_options[tc_page_comments]', array(
			'default'        => 0,
			'capability'     => 'manage_options',
			'type'           => 'option',
		) );
		$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_page_comments]', array(
			'settings' 	=> 'tc_theme_options[tc_page_comments]',
			'label'    	=> __( 'Enable comments on pages','customizr' ),
			'section'  	=> 'tc_page_comments',
			'tc'     	=> 'checkbox',
			'notice'	=> __('This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.','customizr'),
		) ));



	// SOCIAL LINKS //
	$wp_customize->add_section( 'tc_social_settings', array(
		'title'          => __( 'Social links','customizr' ),
		'priority'       => 200,
		'description'    => __( 'Set up your social links','customizr' ),
	) );

		//Position checkboxes
		$social_pos = array(
			'header' 		=> __('header','customizr'),
			'right-sidebar' => __('right-sidebar','customizr'),
			'left-sidebar' 	=> __('left-sidebar','customizr'),
			'footer'		=> __('footer','customizr')
			);
		
		//field generator
		foreach ($social_pos as $key => $pos) {
			$pos_option_name = 'tc_social_in_'.$key;
			$wp_customize->add_setting( 'tc_theme_options['.$pos_option_name.']', array(
				'default'        => tc_get_options($pos_option_name),
				'capability'     => 'manage_options',
				'type'           => 'option',
			) );

			$wp_customize->add_control( 'tc_theme_options['.$pos_option_name.']', array(
				'settings' 		=> 'tc_theme_options['.$pos_option_name.']',
				'label'    		=> 	sprintf(__( 'Social links in %s','customizr' ) , $pos ),
				'priority' 		=> 0,
				'section'  		=> 'tc_social_settings',
				'type'     		=> 'checkbox',
			) );
		}
		
		//Social Url fields
		//define the option name  => label array
		$socials = array (
          'tc_rss'            => __( 'RSS feed (default is the wordpress feed)','customizr' ),
          'tc_twitter'        => __( 'Twitter profile url','customizr' ),
          'tc_facebook'       => __( 'Facebook profile url','customizr' ),
          'tc_google'         => __( 'Google+ profile url','customizr' ), 
          'tc_youtube'        => __( 'Youtube profile url','customizr' ),
          'tc_pinterest'      => __( 'Pinterest profile url','customizr' ),
          'tc_github'         => __( 'Github profile url','customizr' ),
          'tc_dribbble'       => __( 'Dribbble profile url','customizr' ),
          'tc_linkedin'       => __( 'LinkedIn profile url','customizr' )
          ); 

		//Social fields generator with url sanitization
		foreach ($socials as $key => $nw) {
			$nw_option_name = 'tc_theme_options['.$key.']';
			$wp_customize->add_setting( $nw_option_name, array(
				'default'        => ($key == 'tc_rss') ? get_bloginfo('rss_url') : tc_get_options($key),
				'capability'     => 'manage_options',
				'type'           => 'option',
				'sanitize_callback' => 'tc_sanitize_url',
			) );

			$wp_customize->add_control( new TC_Controls($wp_customize, $nw_option_name, array(
				'settings' 		=> $nw_option_name,
				'label'    		=> $nw,
				'section'  		=> 'tc_social_settings',
				'tc'     		=> 'url',
			)));
		}


		// IMAGE SETTINGS //
		$wp_customize->add_section( 'tc_image_settings', array(
			'title'          => __( 'Images','customizr' ),
			'priority'       => 210,
			'description'    => __( 'Enable/disable lightbox effect on images','customizr' ),
		) );

			$wp_customize->add_setting( 'tc_theme_options[tc_fancybox]', array(
				'default'        => 1,
				'capability'     => 'manage_options',
				'type'           => 'option',
			) );
			$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_fancybox]', array(
				'settings' 	=> 'tc_theme_options[tc_fancybox]',
				'label'    	=> __( 'Enable/disable lightbox effect on images' ),
				'section'  	=> 'tc_image_settings',
				'tc'     	=> 'checkbox',
				'notice'	=> __('If enabled, this option activate a popin window whith a zoom effect when an image is clicked. This will not apply to image gallery.','customizr'),
			) ));


		// CUSTOM CSS //
		$wp_customize->add_section( 'tc_custom_css', array(
			'title'          => __( 'Custom CSS','customizr' ),
			'priority'       => 220,
			'description'    => __( 'Add your own CSS','customizr' ),
		) );

			$wp_customize->add_setting( 'tc_theme_options[tc_custom_css]', array(
				//'default'		 	=> 
				'capability'     	=> 'manage_options',
				'type'           	=> 'option',
				'sanitize_callback' => 'tc_sanitize_textarea',
			) );

			$wp_customize->add_control( new TC_Controls($wp_customize, 'tc_theme_options[tc_custom_css]', array(
				'settings' 		=> 'tc_theme_options[tc_custom_css]',
				'label'    		=> __( 'Add your custom css here and design live! (for advanced users)','customizr' ),
				'section'  		=> 'tc_custom_css',
				'tc'     		=> 'textarea',
				'notice'		=> __('Always use this field to add your custom css instead of editing directly the style.css file : it will not be deleted during theme updates.','customizr')
			)));

	}
endif;





if(!function_exists('tc_customize_preview_js')) :
add_action( 'customize_preview_init', 'tc_customize_preview_js' );
/**
 *  Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 * @package Customizr
 * @since Customizr 1.0 
 */
	function tc_customize_preview_js() {
		wp_enqueue_script( 'tc-customizer', get_template_directory_uri() . '/inc/admin/js/theme-customizer.js', array( 'customize-preview' ), '20120827', true );
	}
endif;




if(!function_exists('tc_theme_activation')) :
add_action('admin_init','tc_theme_activation');
/**
*  On activation, redirect on the customization page, set the frontpage option to "posts" with 10 posts per page
* @package Customizr
* @since Customizr 1.0 
*/
	function tc_theme_activation()
	{
		global $pagenow;
		if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) 
		{
			#set frontpage to display_posts
			update_option('show_on_front', 'posts');

			#set max number of posts to 10
			update_option('posts_per_page', 10);

			#redirect to options page
			//header( 'Location: '.admin_url().'customize.php' ) ;
		}
	}
endif;