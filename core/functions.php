<?php
/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists( 'czr_fn_column_content_wrapper_class' ) ) {
      function czr_fn_column_content_wrapper_class() {
            return CZR() -> czr_fn_column_content_wrapper_class();
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the column content wrapper class
if ( ! function_exists( 'czr_fn_main_container_wrapper_class' ) ) {
      function czr_fn_main_container_class() {
            return CZR() -> czr_fn_main_container_class();
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo the article container class
if ( ! function_exists( 'czr_fn_article_container_class' ) ) {
      function czr_fn_article_container_class() {
            return CZR() -> czr_fn_article_container_class();
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists( 'czr_fn_get_theme_file' ) ) {
      function czr_fn_get_theme_file( $path_suffix ) {
            return CZR() -> czr_fn_get_theme_file( $path_suffix );
      }
}
/*
 * @since 3.5.0
 */
//shortcut function to get a theme file
if ( ! function_exists( 'czr_fn_get_theme_file_url' ) ) {
      function czr_fn_get_theme_file_url( $url_suffix ) {
            return CZR() -> czr_fn_get_theme_file_url( $url_suffix );
      }
}
/*
 * @since 3.5.0
 */
//shortcut function to require a theme file
if ( ! function_exists( 'czr_fn_require_once' ) ) {
      function czr_fn_require_once( $path_suffix ) {
            return CZR() -> czr_fn_require_once( $path_suffix );
      }
}


/*
 * @since 3.5.0
 */
//shortcut function to set the current model which will be accessible by the czr_fn_get
if ( ! function_exists( 'czr_fn_set_current_model' ) ) {
      function czr_fn_set_current_model( $model ) {
            return CZR() -> czr_fn_set_current_model( $model );
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to reset the current model
if ( ! function_exists( 'czr_fn_reset_current_model' ) ) {
      function czr_fn_reset_current_model() {
            return CZR() -> czr_fn_reset_current_model();
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to get a current model property
if ( ! function_exists( 'czr_fn_get' ) ) {
      function czr_fn_get( $property, $model_id = null, $args = array() ) {
            return CZR() -> czr_fn_get( $property, $model_id, $args );
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to echo a current model property
if ( ! function_exists( 'czr_fn_echo' ) ) {
      function czr_fn_echo( $property, $model_id = null, $args = array() ) {
            return CZR() -> czr_fn_echo( $property, $model_id, $args );
      }
}

/*
 * @since 3.5.0
 */
//shortcut function to instantiate easier
if ( ! function_exists( 'czr_fn_new' ) ) {
      function czr_fn_new( $_to_load, $_args = array() ) {
            CZR() -> czr__fn_( $_to_load , $_args );
            return;
      }
}

/*
* Shortcut function to instantiate a model + render its template
* model and template should share the same name
* some templates are shared by several models => that's when the $_id param is useful
* @since 4.0.0
* @param string $template             The template to render (with tree path, without the trailing .php)
* @param array  $args {
*     Optional. Array of options.
*     @type string $model_id           Optional. The id of the model to feed the template with.
*                                      If not specified or not already registered the system will try to
*                                      register a model with classname retrieved from the template option,
*                                      if available, otherwise the base model class will be registered
*     @type array|string $model_class  Optional. array|string. The model class (with tree path, without the trailing .php)
*                                      to  feed the model with. When array, in the form array( parent, name )

*     @type array $model_args          Optional. array of params to be injected into the model
*
*
* }
* @return void
*/
if ( ! function_exists( 'czr_fn_render_template' ) ) {
      function czr_fn_render_template( $template, $args = array() ) {

            if ( empty( $template ) || ! is_string( $template ) )
                  return; /* Fire a notice? */

            //extract
            $_t                  =  $template;
            $_model_id           =  ! empty( $args['model_id'] )    ? $args['model_id'] : basename($_t);
            $_model_class        =  ! empty( $args['model_class'] ) ? $args['model_class'] : '';
            $_model_args         =  ! empty( $args['model_args'] )  ? $args['model_args']  : array();

            /*
            * Sometimes on rendering we want to reset some model properties to their defaults
            * declared in the model itself.
            * E.g. when we re-use "singleton" models, to automatically "re-init" them
            *
            * Sometimes, though, we don't want this.
            * E.g. when we re-use some "singleton" models in specific cases:
            * Common case, Gallery/Video/Audio... in list of posts:
            * In the list of posts we retrieve the existence of a media from the post list item model (e.g. inside the post_list_alternate).
            * In this case we ask (with or without proxies) to (say) the gallery model whether or not the item we want to render has a gallery.
            * The gallery model, then, is already initalized, and has already retrieved the information,
            * When rendering the gallery template, through the czr_fn_render_template function, thus, we just want to render what has been already
            * computed.
            * Will be care of the "caller" (post_list_alternate model, or the proxy it uses) to reset the gallery model's at each loop before retrieving
            * the informations it needs.
            */
            $_reset_to_defaults  =  is_array( $args ) && array_key_exists( 'reset_to_defaults' , $args) ? $args['reset_to_defaults']  : true;

            if ( czr_fn_is_registered( $_model_id ) ) {

                  $model_instance = czr_fn_get_model_instance( $_model_id );

                  //sets the template property on the fly based on what's requested
                  if ( ! czr_fn_get_model_property( $_model_id, 'template') ) {
                        $model_instance -> czr_fn_set_property('template' , $_t );
                  }

                  //update model with the one passed
                  if ( is_array($args) && array_key_exists( 'model_args', $args) ) {

                        $model_instance -> czr_fn_update( $_model_args, $_reset_to_defaults );

                  }
                  elseif ( $_reset_to_defaults ) {

                        $model_instance -> czr_fn_reset_to_defaults();
                  }

                  czr_fn_get_view_instance($_model_id ) -> czr_fn_maybe_render();
            }
            else {
                  czr_fn_register( array( 'id' => $_model_id, 'render' => true, 'template' => $_t, 'model_class' => $_model_class, 'args' => $_model_args ) );
            }
      }
}

//@return boolean
//states if registered and possible
//useful is a check has to be done in the template before "instant" registration.
if ( ! function_exists( 'czr_fn_has' ) ) {
      function czr_fn_has( $_t, $_id = null, $only_registered = false ) {
            $_model_id = is_null($_id) ? $_t : $_id;

            if ( CZR() -> collection -> czr_fn_is_registered( $_model_id ) ) {
                  return true;
            }
            //if the model is not registered yet, let's test its eligibility by accessing directly its controller boolean if exists
            elseif ( ! $only_registered ) {
                  return CZR() -> controllers -> czr_fn_is_possible( $_model_id );
            }

      }
}

//@return boolean
//states if registered only
if ( ! function_exists( 'czr_fn_is_registered' ) ) {
      function czr_fn_is_registered( $_model_id ) {
            return CZR() -> collection -> czr_fn_is_registered( $_model_id );
      }
}

//@return boolean
//states if possible
if ( ! function_exists( 'czr_fn_is_possible' ) ) {
      function czr_fn_is_possible( $_model_id ) {
            return CZR() -> controllers -> czr_fn_is_possible( $_model_id );
      }
}

//@return model object if exists
if ( ! function_exists( 'czr_fn_get_model_instance' ) ) {
      function czr_fn_get_model_instance( $_model_id ) {
            if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
                  return;

            return CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
      }
}


//@return model property if exists
//@param _model_id string
//@param property name string
if ( ! function_exists( 'czr_fn_get_model_property' ) ) {
      function czr_fn_get_model_property( $_model_id, $_property ) {
            if ( ! CZR() -> collection -> czr_fn_is_registered( $_model_id ) )
                  return;

            $model_instance = CZR() -> collection -> czr_fn_get_model_instance( $_model_id );
            return $model_instance -> czr_fn_get_property($_property);
      }
}

//@return view model object if exists
if ( ! function_exists( 'czr_fn_get_view_instance' ) ) {
      function czr_fn_get_view_instance( $_model_id ) {
            $model_instance = CZR() -> collection -> czr_fn_get_model_instance( $_model_id );

            if ( ! isset( $model_instance-> view_instance ) )
                  return;

            return $model_instance -> view_instance;
      }
}

// Shortcut function to register a model
// @return model id if registration went through
if ( ! function_exists( 'czr_fn_register' ) ) {
      function czr_fn_register( $model = array() ) {
            return CZR() -> collection -> czr_fn_register( $model );
      }
}

// Shortcut function to register a model if not already registered
// @return model id if:
// a) registration went through
// or
// b) model already registered
if ( ! function_exists( 'czr_fn_maybe_register' ) ) {
      function czr_fn_maybe_register( $model = array() ) {

            $_model_id = array_key_exists( 'id', $model ) && !empty( $model[ 'id' ] ) ? $model[ 'id' ] : false;

            if ( $_model_id && czr_fn_is_registered( $_model_id ) )
                  return $_model_id;

            return CZR() -> collection -> czr_fn_register( $model );
      }
}
?><?php
/**
* Defines the customizer setting map
* On live context, used to generate the default option values
*
*
*/


/**
* Defines sections, settings and function of customizer and return and array
* Also used to get the default options array, in this case $get_default = true and we DISABLE the __get_option (=>infinite loop)
*
* @package Customizr
* @since Customizr 3.0
*/
function czr_fn_get_customizer_map( $get_default = null,  $what = null ) {
    if ( ! ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) ) {
      return CZR_utils_settings_map::$instance -> czr_fn_get_customizer_map( $get_default, $what );
    }

    if ( ! empty( CZR___::$customizer_map ) ) {
      $_customizer_map = CZR___::$customizer_map;
    }
    else {
      //POPULATE THE MAP WITH DEFAULT CUSTOMIZR SETTINGS
      add_filter( 'czr_add_panel_map'           , 'czr_fn_popul_panels_map');
      add_filter( 'czr_remove_section_map'      , 'czr_fn_popul_remove_section_map');
      //theme switcher's enabled when user opened the customizer from the theme's page
      add_filter( 'czr_remove_section_map'      , 'czr_fn_set_theme_switcher_visibility');
      add_filter( 'czr_add_section_map'         , 'czr_fn_popul_section_map');
      //add controls to the map
      add_filter( 'czr_add_setting_control_map' , 'czr_fn_popul_setting_control_map', 10, 2 );


      //FILTER SPECIFIC SETTING-CONTROL MAPS
      //ADDS SETTING / CONTROLS TO THE RELEVANT SECTIONS
      add_filter( 'czr_fn_front_page_option_map', 'czr_fn_generates_featured_pages', 10, 2 );

      //MAYBE FORCE REMOVE SECTIONS (e.g. CUSTOM CSS section for wp >= 4.7 )
      add_filter( 'czr_add_section_map'         , 'czr_fn_force_remove_section_map' );

      //CACHE THE GLOBAL CUSTOMIZER MAP
      $_customizer_map = array_merge(
        array( 'add_panel'           => apply_filters( 'czr_add_panel_map', array() ) ),
        array( 'remove_section'      => apply_filters( 'czr_remove_section_map', array() ) ),
        array( 'add_section'         => apply_filters( 'czr_add_section_map', array() ) ),
        array( 'add_setting_control' => apply_filters( 'czr_add_setting_control_map', array(), $get_default ) )
      );
      CZR___::$customizer_map = $_customizer_map;
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
* hook : 'czr_add_setting_control_map'
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

    'czr_fn_links_option_map',
    'czr_fn_images_option_map',

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
    'czr_fn_single_page_option_map',
    'czr_fn_gallery_option_map', //No gallery options in c4 as of now
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

  $_settings_sections = apply_filters( 'czr_settings_sections', $_settings_sections );

  foreach ( $_settings_sections as $_section_cb ) {
    if ( ! function_exists( $_section_cb ) )
      continue;
    //applies a filter to each section settings map => allows plugins (featured pages for ex.) to add/remove settings
    //each section map takes one boolean param : $get_default
    $_section_map = apply_filters(
      $_section_cb,
      call_user_func( $_section_cb, $get_default )
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
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
  );
}

/*-----------------------------------------------------------------------------------------------------
                                  SKIN SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_skin_option_map( $get_default = null ) {
      return array(
            'tc_skin_color' => array(
                              'default'     => '#5a5a5a',
                              'control'     => 'WP_Customize_Color_Control',
                              'label'       => __( 'Skin color' , 'customizr' ),
                              'section'     => 'skins_sec',
                              'type'        =>  'color' ,
                              'priority'    => 30,
                              'sanitize_callback'    => 'czr_fn_sanitize_hex_color',
                              'sanitize_js_callback' => 'maybe_hash_hex_color',
                              'transport'   => 'refresh' //postMessage
            ),
      );//end of skin options
}



/*-----------------------------------------------------------------------------------------------------
                                 FONT SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_fonts_option_map( $get_default = null ) {
      return array(
            'tc_fonts'      => array(
                            'default'       => czr_fn_user_started_before_version( '3.4.39' , '1.2.39') ? '_g_fjalla_cantarell': '_g_sourcesanspro',
                            'label'         => __( 'Select a beautiful font pair (headings &amp; default fonts) or single font for your website.' , 'customizr' ),
                            'control'       =>  'CZR_controls',
                            'section'       => 'fonts_sec',
                            'type'          => 'select' ,
                            'choices'       => $get_default ? null : czr_fn_get_font( 'list' , 'name' ),
                            'priority'      => 10,
                            'transport'     => 'postMessage',
                            'notice'        => __( "This font picker allows you to preview and select among a handy selection of font pairs and single fonts. If you choose a pair, the first font will be applied to the site main headings : site name, site description, titles h1, h2, h3., while the second will be the default font of your website for any texts or paragraphs." , 'customizr' )
            ),
            'tc_body_font_size'      => array(
                            'default'       => czr_fn_user_started_before_version( '3.2.9', '1.0.1' ) ? 14 : 15,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
function czr_fn_social_option_map( $get_default = null ) {
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
                            'priority'    => 1,
                            'notice'    => __( 'If enabled, this option activates a popin window whith a zoom effect when an image is clicked. Note : to enable this effect on the images of your pages and posts, images have to be linked to the Media File.' , 'customizr' ),
          ),

          'tc_retina_support' =>  array(
                            'default'       => 0,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( 'High resolution (Retina) support' , 'customizr' ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 5,
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
                            'priority'    => 10,
                            'notice'    => __( 'If enabled, your slides scroll slower than the page (parallax effect).' , 'customizr' ),
          ),

          'tc_center_slider_img'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Dynamic slider images centering on any devices" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 15,
                            //'notice'    => __( 'This option dynamically centers your images on any devices vertically or horizontally (without stretching them) according to their initial dimensions.' , 'customizr' ),
          ),
          'tc_center_img'  =>  array(
                            'default'       => 1,
                            'control'   => 'CZR_controls' ,
                            'label'       => __( "Dynamic thumbnails centering on any devices" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 20,
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
                            'priority'    => 25,
          ),
          'tc_resp_thumbs_img'  =>  array(
                            'default'     => 0,
                            'control'     => 'CZR_controls' ,
                            'label'       => __( "Enable the WordPress responsive image feature for the theme's thumbnails" , "customizr" ),
                            'section'     => 'images_sec' ,
                            'notice'      => __( 'This feature has been introduced in WordPress v4.4+ (dec-2015), and might have minor side effects on some of your existing images. Check / uncheck this option to safely verify that your images are displayed nicely.' , 'customizr' ),
                            'type'        => 'checkbox' ,
                            'priority'    => 30,
          )
      )
    );

  return $_image_options;
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
                                  'right'     => __( 'Logo / title on the right' , 'customizr' ),
                                  'centered'  => __( 'Logo / title centered' , 'customizr'),
                          ),
                          'priority'      => 5,
                          'transport'    => czr_fn_is_partial_refreshed_on() ? 'postMessage' : 'refresh',
                          'notice'    => __( 'This setting might impact the side on which the menu is revealed.' , 'customizr' ),
          ),
          /*new*/
          'tc_header_topbar'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display the topbar" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 10,
          ),
          /* Implement Hueman way?
          'tc_header_topnav_mobile'  =>  array(
                            'default'       => 'hide',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Topnav in mobiles" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'choices'       => array(
                                    'hide'      => __( 'Hide' , 'customizr' ),
                                    'show'      => __( 'Show' , 'customizr'),
                                    'collapse'  => __( 'Collapse' , 'customizr' ),
                            ),
                            'type'          => 'select' ,
                            'priority'      => 13,
          ),*/
          /*end_new*/
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
          /* new */
          'tc_social_in_topnav' =>  array(
                            'default'       => 0,
                            'label'       => __( 'Social links in topnav' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'header_layout_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'      => 22,
                            'ubq_section'   => array(
                                                'section' => 'socials_sec',
                                                'priority' => '2'
                                             )
          ),
          'tc_search_in_header' => array(
                            'default'   => 1,
                            'label'     => __( 'Display a search button in the header' , 'customizr' ),
                            'control'   => 'CZR_controls' ,
                            'section'   => 'header_layout_sec',
                            'type'      => 'checkbox' ,
                            'priority'  => 24,

          ),
          /* end new */
          /* new */
          'tc_header_skin'  =>  array(
                            'default'       => 'light',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Header skin', 'customizr'),
                            'choices'       => array(
                                  'dark'   => __( 'Dark' , 'customizr' ),
                                  'light'  => __( 'Light' , 'customizr')
                            ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'select' ,
                            'priority'      => 26,
          ),
          /* Makes no
          'tc_header_type'  => array(
                            'default'       => 'standard',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Header type" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          =>  'select',
                            'choices'       => array(
                                  'absolute'  => __( 'Absolute' , 'customizr' ),
                                  'standard'  => __( 'Relative' , 'customizr'),
                            ),
                            'priority'      => 27,
          ),
          */
          /* end new */
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
          /* new TODO:
          'tc_sticky_mobile'  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky on scroll in mobiles" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 30,
                            'transport'     => 'postMessage',
          ),
          /* end new */
          'tc_sticky_show_tagline'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Sticky header : display the tagline" , "customizr" ),
                            'section'       => 'header_layout_sec' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 40,
                            'transport'     => 'postMessage',
          ),
          /* Removed in c4*/
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
          /* Removed in c4*/
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
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
  $menu_style = czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'navbar' : 'aside';

  return array(
          'tc_display_second_menu'  =>  array(
                            'default'       => 0,
                            'control'       => 'CZR_controls' ,
                            'label'         => __( "Display a secondary (horizontal) menu in the header." , "customizr" ),
                            'section'       => 'nav' ,
                            'type'          => 'checkbox' ,
                            'priority'      => 15,//must be located between the two menus
                            'notice'        => __( 'Displayed in the topnav if enabled' , 'customizr' ),
                            //For old customizr < 4.0
                            //'notice'        => __( "When you've set your main menu as a vertical side navigation, you can check this option to display a complementary horizontal menu in the header." , 'customizr' ),
          ),
          'tc_menu_style'  =>  array(
                          'default'       => $menu_style,
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
          'tc_menu_position'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.4.0', '1.2.0' ) ? 'pull-menu-left' : 'pull-menu-right',
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
                            'default'   =>  czr_fn_user_started_before_version( '3.1.0' , '1.0.0' ) ? 'click' : 'hover',
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
                            'choices'     => czr_fn_layout_choices(),
                            'priority'    => 2,
                            'ubq_section'   => array(
                                'section' => 'post_layout_sec',
                                'priority' => '0'
                            )
          ),

          //select slider
          'tc_front_slider' => array(
                            'default'     => 'tc_posts_slider' ,
                            'control'     => 'CZR_controls' ,
                            'title'       => __( 'Slider options' , 'customizr' ),
                            'label'       => __( 'Select front page slider' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'select' ,
                            //!important
                            'choices'     => ( true == $get_default ) ? null : czr_fn_slider_choices(),
                            'priority'    => 20
          ),
          //posts slider
          'tc_posts_slider_number' => array(
                            'default'     => 4,
                            'control'     => 'CZR_controls',
                            'label'       => __('Number of posts to display', 'customizr'),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'number',
                            'step'        => 1,
                            'min'         => 1,
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
                            'default'      => 1,
                            'control'     => 'CZR_controls' ,
                            'label'       => __( 'Full width slider' , 'customizr' ),
                            'section'     => 'frontpage_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'    => 30,
                            'notice'      => __( "When checked, the front page slider occupies the full viewport's width", 'customizr' ),
          ),

          //Delay between each slides
          'tc_slider_delay' => array(
                            'default'       => 5000,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
                          'choices'     => $get_default ? null : czr_fn_layout_choices(),
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
                          'choices'   => $get_default ? null : czr_fn_layout_choices(),
                          'priority'      => 30
          ),

          //Post per page
          'posts_per_page'  =>  array(
                          'default'     => get_option( 'posts_per_page' ),
                          'sanitize_callback' => 'czr_fn_sanitize_number',
                          'control'     => 'CZR_controls' ,
                          'title'         => __( 'Global Post Lists Settings' , 'customizr' ),
                          'label'         => __( 'Maximum number of posts per page' , 'customizr' ),
                          'section'       => 'post_lists_sec' ,
                          'type'          => 'number' ,
                          'step'        => 1,
                          'min'         => 1,
                          'priority'       => 10,
          ),

          //Page sidebar layout
          'tc_sidebar_page_layout'  =>  array(
                            'default'       => 'l' ,//Default sidebar layout is on the left
                            'label'       => __( 'Choose the pages default layout' , 'customizr' ),
                            'section'     => 'post_layout_sec' ,
                            'type'        => 'select' ,
                            'choices'   => $get_default ? null : czr_fn_layout_choices(),
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

  $_post_list_type = czr_fn_user_started_before_version( '3.2.18', '1.0.13' ) ? 'alternate' : 'grid';

  return array(
          'tc_post_list_excerpt_length'  =>  array(
                            'default'       => 50,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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

          /* Not used anymore in c4 */
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
                                    'regular'               => __( 'Regular', 'customizr' ),
                            ),
                            'priority'      => 77
          ),
          'tc_post_list_thumb_position'  =>  array(
                            'default'       => 'right',
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Thumbnails position" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'      =>  'select' ,
                            'choices'     => array(
                                    //Since Customizr4 you can only have thumb first/second
                                    //hence, only right/left
                                    'right'   => __( 'Right' , 'customizr' ),
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
                            'default'       => $_post_list_type,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Post List Design' , 'customizr' ),
                            'label'         => __( 'Select a Layout' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'select',
                            'choices'       => array(
                                    'alternate'       => __( 'Alternate thumbnails layout' , 'customizr'),
                                    'grid'            => __( 'Grid layout' , 'customizr'),
                                    //new
                                    'plain'           => __( 'Plain full layout' , 'customizr'),

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
                            'label'         => __( 'Display post format icons' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 63,
                            'transport'   => 'postMessage'
          ),
          'tc_grid_num_words'  =>  array(
                            'default'       => 10,
                            'sanitize_callback' => 'czr_fn_sanitize_number',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Max. length for post titles (in words)' , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'number' ,
                            'step'          => 1,
                            'min'           => 1,
                            'priority'      => 64
          ),

          /* new:
          In customizr 4 used only for the Classical grid */
          'tc_post_list_thumb_placeholder'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls' ,
                            'label'         => __( "Display thumbnail placeholder if no images available" , "customizr" ),
                            'section'       => 'post_lists_sec' ,
                            'type'          => 'checkbox',
                            'priority'      => 99
          ),

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
                        'sanitize_callback' => 'czr_fn_sanitize_number',
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
                               SINGLE PAGESS SECTION
------------------------------------------------------------------------------------------------------*/
function czr_fn_single_page_option_map( $get_default = null ) {
  return array(
      'tc_single_page_thumb_location'  =>  array(
                        'default'       => 'hide',
                        'control'     => 'CZR_controls' ,
                        'label'         => __( "Post thumbnail position" , "customizr" ),
                        'section'       => 'single_pages_sec' ,
                        'type'      =>  'select' ,
                        'choices'     => array(
                                'hide'                    => __( "Don't display" , 'customizr' ),
                                '__before_main_wrapper|200'   => __( 'Before the title in full width' , 'customizr' ),
                                '__before_content|0'     => __( 'Before the title boxed' , 'customizr' ),
                                '__after_content_title|10'    => __( 'After the title' , 'customizr' ),
                        ),
                        'priority'      => 10,
                        'notice'    => sprintf( '%s<br/>%s',
                          __( 'You can display the featured image (also called the post thumbnail) of your pages before their content, when they are displayed individually.' , 'customizr' ),
                          sprintf( __( "Don't know how to set a featured image to a page? Learn how in the %s.", "customizr" ),
                              sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
                          )
                        )
      ),
      'tc_single_page_thumb_height' => array(
                        'default'       => 250,
                        'sanitize_callback' => 'czr_fn_sanitize_number',
                        'control'   => 'CZR_controls' ,
                        'label'       => __( "Set the thumbnail's max height in pixels" , 'customizr' ),
                        'section'     => 'single_pages_sec' ,
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
                            'default'   => czr_fn_user_started_before_version( '3.4.39' , '1.2.39' ) ? 0 : 1,
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

          'tc_show_post_metas_author'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the author" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 40
          ),
          'tc_show_post_metas_publication_date'  =>  array(
                            'default'       => 1,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the publication date" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 45
          ),
          //Think about displaying this only in singles like hueman does!
          //it's very ugly in post lists :/
          'tc_show_post_metas_update_date'  =>  array(
                            'default'       => 0,
                            'control'     => 'CZR_controls',
                            'label'         => __( "Display the update date" , "customizr" ),
                            'section'       => 'post_metas_sec',
                            'type'          => 'checkbox',
                            'priority'      => 50,
          ),

  );
}



/*-----------------------------------------------------------------------------------------------------
                               GALLERY SECTION
-----------------------------------------------------------------------------------------------------*/
/* Totally removed in c4 */
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
                            'sanitize_callback' => 'czr_fn_sanitize_number',
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
          /* new */
          'tc_footer_skin'  =>  array(
                            'default'       => 'dark',
                            'control'       => 'CZR_controls' ,
                            'label'         => __( 'Footer skin', 'customizr'),
                            'choices'       => array(
                                  'dark'   => __( 'Dark' , 'customizr' ),
                                  'light'  => __( 'Light' , 'customizr')
                            ),
                            'section'       => 'footer_global_sec' ,
                            'type'          => 'select' ,
                            'priority'      => 0
          ),
          'tc_social_in_footer' =>  array(
                            'default'       => 1,
                            'label'       => __( 'Social links in footer' , 'customizr' ),
                            'control'   =>  'CZR_controls' ,
                            'section'     => 'footer_global_sec' ,
                            'type'        => 'checkbox' ,
                            'priority'       => 0,
                            'ubq_section'  => array(
                                                'section' => 'socials_sec',
                                                'priority' => '4'
                                             )
          ),
          'tc_sticky_footer'  =>  array(
                            'default'       => czr_fn_user_started_before_version( '3.4.0' , '1.1.14' ) ? 0 : 1,
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
  global $wp_version;

  return version_compare( $wp_version, '4.7', '>=' ) ? array() : array(
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
 * hook : czr_remove_section_map
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
* hook : czr_remove_section_map
*/
function czr_fn_set_theme_switcher_visibility( $_sections) {
  //Don't do anything is in preview frame
  //=> because once the preview is ready, a postMessage is sent to the panel frame to refresh the sections and panels
  //Do nothing if WP version under 4.2
  global $wp_version;
  if ( czr_fn_is_customize_preview_frame() || ! version_compare( $wp_version, '4.2', '>=') )
    return $_sections;

  if ( ! CZR_IS_PRO )
    return $_sections;
  else {
    array_push( $_sections, 'themes');
    return $_sections;
  }
}



/***************************************************************
* POPULATE SECTIONS
***************************************************************/
/**
* hook : tc_add_section_map
*/
function czr_fn_popul_section_map( $_sections ) {
  //declare a var to check wp version >= 4.0
  global $wp_version;
  $_is_wp_version_before_4_0 = ( ! version_compare( $wp_version, '4.0', '>=' ) ) ? true : false;

  //For nav menus option
  $locations                = get_registered_nav_menus();
  $menus                    = wp_get_nav_menus();
  $num_locations            = count( array_keys( $locations ) );


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
                        'priority' => $_is_wp_version_before_4_0 ? 7 : 0,
                        'panel'   => 'tc-global-panel'
    ),
    'logo_sec'            => array(
                        'title'     =>  __( 'Logo &amp; Favicon' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 8 : 5,
                        'description' =>  __( 'Set up logo and favicon options' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'skins_sec'         => array(
                        'title'     =>  __( 'Skin' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 1 : 7,
                        'description' =>  __( 'Select a skin for Customizr' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'fonts_sec'          => array(
                        'title'     =>  __( 'Fonts' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 40 : 10,
                        'description' =>  __( 'Set up the font global settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'socials_sec'        => array(
                        'title'     =>  __( 'Social links' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 9 : 20,
                        'description' =>  __( 'Set up your social links' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'links_sec'         => array(
                        'title'     =>  __( 'Links style and effects' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 22 : 30,
                        'description' =>  __( 'Various links settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'images_sec'         => array(
                        'title'     =>  __( 'Image settings' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 95 : 50,
                        'description' =>  __( 'Various images settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'authors_sec'               => array(
                        'title'     =>  __( 'Authors' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 220 : 70,
                        'description' =>  __( 'Post authors settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),
    'smoothscroll_sec'          => array(
                        'title'     =>  __( 'Smooth Scroll' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 97 : 75,
                        'description' =>  __( 'Smooth Scroll settings' , 'customizr' ),
                        'panel'   => 'tc-global-panel'
    ),

    /*---------------------------------------------------------------------------------------------
    -> PANEL : HEADER
    ----------------------------------------------------------------------------------------------*/
    'header_layout_sec'         => array(
                        'title'    => $_is_wp_version_before_4_0 ? __( 'Header design and layout', 'customizr' ) : __( 'Design and layout', 'customizr' ),
                        'priority' => $_is_wp_version_before_4_0 ? 5 : 20,
                        'panel'   => 'tc-header-panel'
    ),
    'nav'           => array(
                        'title'          => __( 'Navigation Menus' , 'customizr' ),
                        'theme_supports' => 'menus',
                        'priority'       => $_is_wp_version_before_4_0 ? 10 : 40,
                        'description'    => $nav_section_desc,
                        'panel'   => 'tc-header-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : CONTENT
    ----------------------------------------------------------------------------------------------*/
    'frontpage_sec'       => array(
                        'title'     =>  __( 'Front Page' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 12 : 10,
                        'description' =>  __( 'Set up front page options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),

    'post_layout_sec'        => array(
                        'title'     =>  __( 'Pages &amp; Posts Layout' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 15 : 15,
                        'description' =>  __( 'Set up layout options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),

    'post_lists_sec'        => array(
                        'title'     =>  __( 'Post lists : blog, archives, ...' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 16 : 20,
                        'description' =>  __( 'Set up post lists options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'single_posts_sec'        => array(
                        'title'     =>  __( 'Single posts' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 17 : 24,
                        'description' =>  __( 'Set up single posts options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'single_pages_sec'        => array(
                        'title'     =>  __( 'Single pages' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 17 : 24,
                        'description' =>  __( 'Set up single pages options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'breadcrumb_sec'        => array(
                        'title'     =>  __( 'Breadcrumb' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 11 : 30,
                        'description' =>  __( 'Set up breadcrumb options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'post_metas_sec'        => array(
                        'title'     =>  __( 'Post metas (category, tags, custom taxonomies)' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 20 : 50,
                        'description' =>  __( 'Set up post metas options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'galleries_sec'        => array(
                        'title'     =>  __( 'Galleries' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 20 : 55,
                        'description' =>  __( 'Set up gallery options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'paragraphs_sec'        => array(
                        'title'     =>  __( 'Paragraphs' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 20 : 55,
                        'description' =>  __( 'Set up paragraphs options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'comments_sec'          => array(
                        'title'     =>  __( 'Comments' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 25 : 60,
                        'description' =>  __( 'Set up comments options' , 'customizr' ),
                        'panel'   => 'tc-content-panel'
    ),
    'post_navigation_sec'          => array(
                        'title'     =>  __( 'Post/Page Navigation' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 30 : 65,
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
    /*---------------------------------------------------------------------------------------------
    -> PANEL : FOOTER
    ----------------------------------------------------------------------------------------------*/
    'footer_global_sec'          => array(
                        'title'     =>  __( 'Footer global settings' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 40 : 10,
                        'description' =>  __( 'Set up footer global options' , 'customizr' ),
                        'panel'   => 'tc-footer-panel'
    ),


    /*---------------------------------------------------------------------------------------------
    -> PANEL : ADVANCED
    ----------------------------------------------------------------------------------------------*/
    'custom_sec'           => array(
                        'title'     =>  __( 'Custom CSS' , 'customizr' ),
                        'priority'    =>  $_is_wp_version_before_4_0 ? 100 : 10,
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

  if ( ! CZR_IS_PRO ) {
    $_new_sections = array_merge( $_new_sections, array(
        /*---------------------------------------------------------------------------------------------
        -> SECTION : GO-PRO
        ----------------------------------------------------------------------------------------------*/
        'go_pro_sec'   => array(
                            'title'         => esc_html__( 'Upgrade to Customizr Pro', 'customizr' ),
                            'pro_text'      => esc_html__( 'Go Pro', 'customizr' ),
                            'pro_url'       => sprintf('%scustomizr-pro/', CZR_WEBSITE ),
                            'priority'      => 0,
                            'section_class' => 'CZR_Customize_Section_Pro',
                            'active_callback' => 'czr_fn_pro_section_active_cb'
        ),
    ) );
  }

  return array_merge( $_sections, $_new_sections );
}



function czr_fn_force_remove_section_map( $_sections ) {
  global $wp_version;

  // FORCE REMOVE SECTIONS
  // CUSTOM CSS section for wp >= 4.7
  if ( version_compare( $wp_version, '4.7', '>=' ) )
    unset( $_sections[ 'custom_sec' ] );

  return $_sections;
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
                  'sanitize_callback' => 'czr_fn_sanitize_textarea',
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

?><?php
/**
* Defines filters and actions used in several templates/classes
*
*/
/**
* hook : after_setup_theme
* @package Customizr
* @since Customizr 3.3.0
*/
function czr_fn_wp_filters() {
    add_filter( 'the_content'     , 'czr_fn_fancybox_content_filter'  );
    /*
    * Smartload disabled for content retrieved via ajax
    */
    if ( apply_filters( 'czr_globally_enable_img_smart_load', !czr_fn_is_ajax() && esc_attr( czr_fn_opt( 'tc_img_smart_load' ) ) ) ) {
        add_filter( 'the_content'    , 'czr_fn_parse_imgs', PHP_INT_MAX );
        add_filter( 'czr_thumb_html' , 'czr_fn_parse_imgs'  );
    }
    add_filter( 'wp_title'        , 'czr_fn_wp_title' , 10, 2 );
}





/**
* This function returns the filtered global layout defined in CZR_init
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_global_layout() {
  return apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
}

/**
* This function returns the CSS class to apply to content's element based on the layout
* @return array
*
*
* @package Customizr
* @since Customizr 4.0
*/
function czr_fn_get_in_content_width_class() {
  $global_sidebar_layout                 = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );

  switch ( $global_sidebar_layout ) {
    case 'b': $_class = 'narrow';
              break;
    case 'f': $_class = 'full';
              break;
    default : $_class = 'semi-narrow';
  }

  return apply_filters( 'czr_in_content_width_class' , array( $_class ) );
}

/**
* This function returns the layout (sidebar(s), or full width) to apply to a context
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
      global $post;
      //Article wrapper class definition
      $global_layout                 = czr_fn_get_global_layout();

      /* Force 404 layout */
      if ( is_404() ) {
            $czr_screen_layout = array(
                'sidebar' => false,
                'class'   => 'col-12 col-md-8 push-md-2'
            );
            return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }


      /* DEFAULT LAYOUTS */
      //what is the default layout we want to apply? By default we apply the global default layout
      $czr_sidebar_default_layout    = esc_attr( czr_fn_opt('tc_sidebar_global_layout') );
      $czr_sidebar_force_layout      = esc_attr( czr_fn_opt('tc_sidebar_force_layout') );

      //checks if the 'force default layout' option is checked and return the default layout before any specific layout
      if( $czr_sidebar_force_layout ) {
            $class_tab  = $global_layout[$czr_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $czr_screen_layout = array(
                'sidebar' => $czr_sidebar_default_layout,
                'class'   => $class_tab
            );
            return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
      }

      global $wp_query, $post;
      $is_singular_layout          = false;


      if ( apply_filters( 'czr_is_post_layout', is_single( $post_id ), $post_id ) ) {
            $_czr_sidebar_default_layout  = esc_attr( czr_fn_opt('tc_sidebar_post_layout') );
            $is_singular_layout           = true;
      } if ( apply_filters( 'czr_is_page_layout', is_page( $post_id ), $post_id ) ) {
            $_czr_sidebar_default_layout  = esc_attr( czr_fn_opt('tc_sidebar_page_layout') );
            $is_singular_layout           = true;
      }

      $czr_sidebar_default_layout     = empty($_czr_sidebar_default_layout) ? $czr_sidebar_default_layout : $_czr_sidebar_default_layout;

      //builds the default layout option array including layout and article class
      $class_tab  = $global_layout[$czr_sidebar_default_layout];
      $class_tab  = $class_tab['content'];
      $czr_screen_layout             = array(
                  'sidebar' => $czr_sidebar_default_layout,
                  'class'   => $class_tab
      );

      //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
      $czr_specific_post_layout    = false;

      //if we are displaying an attachement, we use the parent post/page layout
      if ( isset($post) && is_singular() && 'attachment' == $post->post_type ) {
            $czr_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
      }

      //for a singular post or page OR for the posts page
      elseif ( $is_singular_layout || is_singular() || $wp_query -> is_posts_page ) {
            $czr_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
      }

      //checks if we display home page, either posts or static page and apply the customizer option
      global $wp_the_query;
      if( ($wp_the_query->is_home() && 'posts' == get_option( 'show_on_front' ) ) || $wp_the_query->is_front_page()) {
            $czr_specific_post_layout = czr_fn_opt('tc_front_layout');
      }

      if ( $czr_specific_post_layout ) {

            $class_tab  = $global_layout[$czr_specific_post_layout];
            $class_tab  = $class_tab['content'];
            $czr_screen_layout = array(
                'sidebar' => $czr_specific_post_layout,
                'class'   => $class_tab
            );

      }

      return apply_filters( 'czr_screen_layout' , $czr_screen_layout[$sidebar_or_class], $post_id , $sidebar_or_class );
}


/**
* This function returns the column content wrapper class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_column_content_wrapper_class() {
    return apply_filters( 'czr_column_content_wrapper_classes' , array('row', 'column-content-wrapper') );
}

/**
* This function returns the main container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_main_container_class() {
    return apply_filters( 'czr_main_container_classes' , array('container') );
}

/**
* This function returns the article container class
*
* @package Customizr
* @since Customizr 3.5
*/
function czr_fn_get_article_container_class() {
    return apply_filters( 'czr_article_container_class' , array( czr_fn_get_layout( czr_fn_get_id() , 'class' ) , 'article-container' ) );
}




/**
 * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
 *
 * @package Customizr
 * @since Customizr 2.0.7
 */
function czr_fn_fancybox_content_filter( $content) {
    $tc_fancybox = esc_attr( czr_fn_opt( 'tc_fancybox' ) );

    if ( 1 != $tc_fancybox )
      return $content;

    global $post;
    if ( ! isset($post) )
      return $content;

    //same as smartload ones
    $allowed_image_extentions = apply_filters( 'tc_lightbox_allowed_img_extensions', array(
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
      return $content;
    }


    $img_extensions_pattern = sprintf( "(?:%s)", implode( '|', $allowed_image_extentions ) );
    $pattern                = '#<a([^>]+?)href=[\'"]?([^\'"\s>]+\.'.$img_extensions_pattern.'[^\'"\s>]*)[\'"]?([^>]*)>#i';


    $replacement = '<a$1href="$2" data-lb-type="grouped-post"$3>';

    $r_content   = preg_replace( $pattern, $replacement, $content);

    $content     = $r_content ? $r_content : $content;

    return apply_filters( 'czr_fancybox_content_filter', $content );
}






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

    $_socials         = czr_fn_opt('tc_social_links');
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
            !czr_fn_is_customizing() ? '' : sprintf( 'data-model-id="%1$s"', ! isset( $item['id'] ) ? 'czr_socials_'. $key : $item['id'] ),
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





//hook : czr_dev_notice
function czr_fn_print_r($message) {
    if ( ! is_user_logged_in() || ! current_user_can( 'edit_theme_options' ) || is_feed() )
      return;
    ?>
      <pre><h6 style="color:red"><?php echo $message ?></h6></pre>
    <?php
}




/* FMK MODEL / VIEW / COLLECTION HELPERS */
function czr_fn_stringify_array( $array, $sep = ' ' ) {
    if ( is_array( $array ) )
      $array = join( $sep, array_unique( array_filter( $array ) ) );
    return $array;
}


//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) ) {

        /* PHP 5.3- compliant*/
        $class_vars = get_class_vars( $cb[0] );

        if ( isset( $class_vars[ 'instance' ] ) && method_exists( $class_vars[ 'instance' ], $cb[1]) ) {
          $to_return = call_user_func( array( $class_vars[ 'instance' ] ,  $cb[1] ), $params );
        }

        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func( array( $_class_obj, $cb[1] ), $params );
        }
      }
    }
    else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func($cb, $params);
    }

    if ( $return )
      return $to_return;
}


function czr_fn_return_cb_result( $cb, $params = array() ) {
    return czr_fn_fire_cb( $cb, $params, $return = true );
}




/* Same as helpers above but passing the param argument as an exploded array of params*/
//A callback helper
//a callback can be function or a method of a class
//the class can be an instance!
function czr_fn_fire_cb_array( $cb, $params = array(), $return = false ) {
    $to_return = false;
    //method of a class => look for an array( 'class_name', 'method_name')
    if ( is_array($cb) && 2 == count($cb) ) {
      if ( is_object($cb[0]) ) {
        $to_return = call_user_func_array( array( $cb[0] ,  $cb[1] ), $params );
      }
      //instantiated with an instance property holding the object ?
      else if ( class_exists($cb[0]) ) {

        /* PHP 5.3- compliant*/
        $class_vars = get_class_vars( $cb[0] );

        if ( isset( $class_vars[ 'instance' ] ) && method_exists( $class_vars[ 'instance' ], $cb[1]) ) {
          $to_return = call_user_func_array( array( $class_vars[ 'instance' ] ,  $cb[1] ), $params );
        }

        else {
          $_class_obj = new $cb[0]();
          if ( method_exists($_class_obj, $cb[1]) )
            $to_return = call_user_func_array( array( $_class_obj, $cb[1] ), $params );
        }
      }
    }
    else if ( is_string($cb) && function_exists($cb) ) {
      $to_return = call_user_func_array($cb, $params);
    }

    if ( $return )
      return $to_return;
}

function czr_fn_return_cb_result_array( $cb, $params = array() ) {
    return czr_fn_fire_cb_array( $cb, $params, $return = true );
}




/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  boolean
*
*/
function czr_fn_is_slider_active( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //is the slider set to on for the queried id?
  if ( czr_fn_is_home() && czr_fn_opt( 'tc_front_slider' ) )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );

  $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );

  if ( ! empty( $_slider_on ) && $_slider_on )
    return apply_filters( 'czr_slider_active_status', true , $queried_id );

  return apply_filters( 'czr_slider_active_status', false , $queried_id );
}

/**
* helper
* returns the slider name id
* @return  string
*
*/
function czr_fn_get_current_slider( $queried_id = null ) {
  $queried_id = $queried_id ? $queried_id : czr_fn_get_real_id();
  //gets the current slider id
  $_home_slider     = czr_fn_opt( 'tc_front_slider' );
  $slider_name_id   = ( czr_fn_is_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
  return apply_filters( 'czr_slider_name_id', $slider_name_id , $queried_id );
}


function czr_fn_post_has_title() {
    return ! in_array(
      get_post_format(),
      apply_filters( 'czr_post_formats_with_no_heading', array( 'aside' , 'status' , 'link' , 'quote' ) )
    );
}

/* TODO: caching system */
function czr_fn_get_logo_atts( $logo_type = '', $backward_compatibility = true ) {
    $logo_type_sep      = $logo_type ? '_sticky_' : '_';

    $_cache_key         = "czr{$logo_type_sep}logo_atts";
    $_logo_atts         = wp_cache_get( $_cache_key );

    if ( false !== $_logo_atts )
      return $_logo_atts;

    $_logo_atts = array();

    $accepted_formats   = apply_filters( 'czr_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );

    //check if the logo is a path or is numeric
    //get src for both cases
    $_logo_src          = '';
    $_width             = false;
    $_height            = false;
    $_attachment_id     = false;
    $_logo_option       = esc_attr( czr_fn_opt( "tc{$logo_type_sep}logo_upload") );
    //check if option is an attachement id or a path (for backward compatibility)
    if ( is_numeric($_logo_option) ) {
      $_attachment_id   = $_logo_option;
      $_attachment_data = apply_filters( "tc{$logo_type_sep}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
      $_logo_src        = $_attachment_data[0];
      $_width           = ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
      $_height          = ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
    } elseif ( $backward_compatibility ) { //old treatment
      //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
      $upload_dir       = wp_upload_dir();
      $_saved_path      = esc_url ( czr_fn_opt( "tc{$logo_type_sep}logo_upload") );
      $_logo_src        = ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
    }
    //hook + makes ssl compliant
    $_logo_src          = apply_filters( "tc{$logo_type_sep}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;
    $filetype           = czr_fn_check_filetype ($_logo_src);

    if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
      $_logo_atts = array(
                'logo_src'           => $_logo_src,
                'logo_attachment_id' => $_attachment_id,
                'logo_width'         => $_width,
                'logo_height'        => $_height,
                'logo_type'          => trim($logo_type_sep,'_')
      );

    //cache this
    wp_cache_set( $_cache_key, $_logo_atts );

    return $_logo_atts;
}

















//back compat
if ( ! class_exists( 'CZR_utils' ) ) :
  class CZR_utils {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $inst;
    static $instance;

    function __construct () {
      self::$inst =& $this;
      self::$instance =& $this;
    }

    /**
    * Returns an option from the options array of the theme.
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_opt( $option_name , $option_group = null, $use_default = true ) {
      return czr_fn_opt( $option_name, $option_group, $use_default );
    }
  }

  new CZR_utils;

endif;

?><?php

/**
* Boolean helper to check if the secondary menu is enabled
* since v3.4+
*/
function czr_fn_is_secondary_menu_enabled() {

    return (bool) esc_attr( czr_fn_opt( 'tc_display_second_menu' ) ) && 'aside' == esc_attr( czr_fn_opt( 'tc_menu_style' ) );
}



?><?php
/**
* Query related functions
*/





/**
* hook : body_class
* @return  array of classes
*
* @package Customizr
* @since Customizr 3.3.2
*/
function czr_fn_set_post_list_context_class( $_class ) {
    if ( czr_fn_is_list_of_posts() )
      array_push( $_class , 'czr-post-list-context');
    return $_class;
}





/******************************
VARIOUS QUERY HELPERS
*******************************/



function czr_fn_is_list_of_posts() {
    //must be archive or search result. Returns false if home is empty in options.
    return apply_filters( 'czr_is_list_of_posts',
      ! is_singular()
      && ! is_404()
      && ! czr_fn_is_home_empty()
      && ! is_admin()
    );
}


function czr_fn_get_query_context() {
    if ( is_page() )
        return 'page';
    if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
    if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
    if ( !is_404() && ! czr_fn_is_home_empty() )
        return 'archive';

    return false;
}

function czr_fn_is_single_post() {
    global $post;
    return apply_filters( 'czr_is_single_post', isset($post)
        && is_singular()
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && ! czr_fn_is_home_empty()
        && ! czr_fn_is_home()
        );
}


function czr_fn_is_single_attachment() {
    global $post;
    return apply_filters( 'czr_is_single_attacment',
        ! ( ! isset($post) || empty($post) || 'attachment' != $post -> post_type || !is_singular() ) );
}

function czr_fn_is_single_page() {
    return apply_filters( 'czr_is_single_page',
        'page' == czr_fn_get_post_type()
        && is_singular()
        && ! czr_fn_is_home_empty()
    );
}




/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  number
*
*/
function czr_fn_get_real_id() {
    global $wp_query;
    $queried_id  = czr_fn_get_id();
    return apply_filters( 'czr_get_real_id', ( ! czr_fn_is_home() && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
}



/**
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_post_list_article_selectors( $post_class = '', $id_suffix = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                  = '';

    if ( isset($post) )
      $selectors = apply_filters( "czr_post_list_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'post', $post->ID, $id_suffix ),
        czr_fn_get_the_post_class( $post_class )
      ) );

    return apply_filters( 'czr_article_selectors', $selectors );
}//end of function






/**
* @override
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_singular_article_selectors( $post_class = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                   = '';

    // SINGLE POST/ATTACHMENT
    if ( isset($post) ) {
      $post_type  = czr_fn_get_post_type();
      $selectors  = apply_filters( "czr_article_singular_{$post_type}_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'page' == $post_type ? $post_type : 'post', $post->ID ),
        czr_fn_get_the_post_class( $post_class )
      ) );
    }

    return apply_filters( 'czr_article_selectors', $selectors );

}//end of function


/**
* Returns the classes for the post div.
*
* @param string|array $class One or more classes to add to the class list.
* @param int $post_id An optional post ID.
* @package Customizr
* @since 3.0.10
*/
function czr_fn_get_the_post_class( $class = '', $post_id = null ) {
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
* Returns the classes for the post div.
*
* @param string $type Optional. post type. Default 'post' .
* @param int $post_id An optional post ID.
* @param string $id_suffix An optional suffix.
* @package Customizr
* @since 3.0.10
*/
function czr_fn_get_the_post_id( $type = 'post', $post_id = null, $id_suffix = '' ) {
    //Separates classes with a single space, collates classes for post DIV
    return sprintf( 'id="%1$s-%2$s%3$s"', $type, $post_id, $id_suffix );
}

/**
* Returns whether or not the current wp_query post is the first one
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_start() {
    global $wp_query;
    return  0 == $wp_query -> current_post;
}

/**
* Returns whether or not the current wp_query post is the latest one
*
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_end() {
    global $wp_query;
    return $wp_query -> current_post == $wp_query -> post_count -1;
}

?><?php
/**
* Posts thumbnails functions
*/
/**********************
* THUMBNAIL MODELS
**********************/
/**
* Gets the thumbnail or the first images attached to the post if any
* inside loop
* @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
*
* @package Customizr
* @since Customizr 1.0
*/
function czr_fn_get_thumbnail_model( $requested_size = null, $_post_id = null , $_custom_thumb_id = null, $_enable_wp_responsive_imgs = true, $_filtered_thumb_size_name = null, $_placeholder = false ) {

    if ( ! czr_fn_has_thumb( $_post_id, $_custom_thumb_id ) ) {
      if ( ! $_placeholder )
        return array();
      else
        return array( 'tc_thumb' => czr_fn_get_placeholder_thumb(), 'is_placeholder' => true );
    }

    $tc_thumb_size              = is_null($requested_size) ? apply_filters( 'czr_thumb_size_name' , 'tc-thumb' ) : $requested_size;
    $_post_id                   = is_null($_post_id) ? get_the_ID() : $_post_id;

    $_filtered_thumb_size_name  = ! is_null( $_filtered_thumb_size_name ) ? $_filtered_thumb_size_name : 'tc_thumb_size';
    $_filtered_thumb_size       = apply_filters( $_filtered_thumb_size_name, $_filtered_thumb_size_name ? CZR_init::$instance -> $_filtered_thumb_size_name : null );

    $_model                     = array();
    $_img_attr                  = array();
    $tc_thumb_height            = '';
    $tc_thumb_width             = '';

    //when null set it as the image setting for reponsive thumbnails (default)
    //because this method is also called from the slider of posts which refers to the slider responsive image setting
    //limit this just for wp version >= 4.4
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      $_enable_wp_responsive_imgs = is_null( $_enable_wp_responsive_imgs ) ? 1 == czr_fn_opt('tc_resp_thumbs_img') : $_enable_wp_responsive_imgs;

    //try to extract $_thumb_id and $_thumb_type
    extract( czr_fn_get_thumb_info( $_post_id, $_custom_thumb_id ) );
    if ( ! isset($_thumb_id) || ! $_thumb_id || is_null($_thumb_id) )
      return array();

    //Try to get the image
    $image                      = wp_get_attachment_image_src( $_thumb_id, $tc_thumb_size);
    if ( empty( $image[0] ) )
      return array();

    //check also if this array value isset. (=> JetPack photon bug)
    if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size )
      $tc_thumb_size          = 'large';
    if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
      $tc_thumb_size          = 'slider';

    $_img_attr['class']     = sprintf( 'attachment-%1$s tc-thumb-type-%2$s' , $tc_thumb_size , $_thumb_type );
    //Add the style value
    $_style                 = apply_filters( 'czr_post_thumb_inline_style' , '', $image, $_filtered_thumb_size );
    if ( $_style )
      $_img_attr['style']   = $_style;
    $_img_attr              = apply_filters( 'czr_post_thumbnail_img_attributes' , $_img_attr );

    //we might not want responsive images
    if ( false === $_enable_wp_responsive_imgs ) {
      //trick, will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
      //only when the passed srcset attr is not empty. This will avoid us to:
      //a) add a filter to get rid of already computed srcset
      // or
      //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
      //Side effect:
      //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
      //to avoid this we filter the attributes getting rid of the srcset if any.
      //Basically this trick, even if ugly, will avoid the srcset attr computation
      $_img_attr['srcset']  = " ";
      add_filter( 'wp_get_attachment_image_attributes', 'czr_fn_remove_srcset_attr' );
    }
    //get the thumb html
    if ( is_null($_custom_thumb_id) && has_post_thumbnail( $_post_id ) )
      //get_the_post_thumbnail( $post_id, $size, $attr )
      $tc_thumb = get_the_post_thumbnail( $_post_id , $tc_thumb_size , $_img_attr);
    else
      //wp_get_attachment_image( $attachment_id, $size, $icon, $attr )
      $tc_thumb = wp_get_attachment_image( $_thumb_id, $tc_thumb_size, false, $_img_attr );

    //get height and width if not empty
    if ( ! empty($image[1]) && ! empty($image[2]) ) {
      $tc_thumb_height        = $image[2];
      $tc_thumb_width         = $image[1];
    }
    //used for smart load when enabled
    $tc_thumb = apply_filters( 'czr_thumb_html', $tc_thumb, $requested_size, $_post_id, $_custom_thumb_id );

    return apply_filters( 'czr_get_thumbnail_model',
      isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width", "_thumb_id" ) : array(),
      $_post_id,
      $_thumb_id,
      $_enable_wp_responsive_imgs
    );
  }



/**
* inside loop
* @return array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_get_thumb_info( $_post_id = null, $_thumb_id = null ) {
    $_post_id     = is_null($_post_id) ? get_the_ID() : $_post_id;
    $_meta_thumb  = get_post_meta( $_post_id , 'tc-thumb-fld', true );
    //get_post_meta( $post_id, $key, $single );
    //always refresh the thumb meta if user logged in and current_user_can('upload_files')
    //When do we refresh ?
    //1) empty( $_meta_thumb )
    //2) is_user_logged_in() && current_user_can('upload_files')
    $_refresh_bool = empty( $_meta_thumb ) || ! $_meta_thumb;
    $_refresh_bool = ! isset($_meta_thumb["_thumb_id"]) || ! isset($_meta_thumb["_thumb_type"]);
    $_refresh_bool = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : $_refresh_bool;
    //if a custom $_thumb_id is requested => always refresh
    $_refresh_bool = ! is_null( $_thumb_id ) ? true : $_refresh_bool;

    if ( ! $_refresh_bool )
      return $_meta_thumb;

    return czr_fn_set_thumb_info( $_post_id , $_thumb_id, true );
}

/**************************
* EXPOSED HELPERS / SETTERS
**************************/
/*
* @return string
*/
function czr_fn_get_single_thumbnail_position() {
    $_exploded_location     = explode( '|', esc_attr( czr_fn_opt( 'tc_single_post_thumb_location' ) ) );
    $_hook                  = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
    return $_hook;
}

/*
* @return bool
*/
function czr_fn_has_thumb( $_post_id = null , $_thumb_id = null ) {
    $_post_id  = is_null($_post_id) ? get_the_ID() : $_post_id;

    //try to extract (OVERWRITE) $_thumb_id and $_thumb_type
    extract( czr_fn_get_thumb_info( $_post_id, $_thumb_id ) );
    return wp_attachment_is_image($_thumb_id) && isset($_thumb_id) && false != $_thumb_id && ! empty($_thumb_id);
}


/**
* update the thumb meta and maybe return the info
* also fired from admin on save_post
* @param post_id and (bool) return
* @return void or array( "_thumb_id" , "_thumb_type" )
*/
function czr_fn_set_thumb_info( $post_id = null , $_thumb_id = null, $_return = false ) {
    $post_id      = is_null($post_id) ? get_the_ID() : $post_id;
    $_thumb_type  = 'none';

    //IF a custom thumb id is requested
    if ( ! is_null( $_thumb_id ) && false !== $_thumb_id ) {
      $_thumb_type  = false !== $_thumb_id ? 'custom' : $_thumb_type;
    }
    //IF no custom thumb id :
    //1) check if has thumbnail
    //2) check attachements
    //3) default thumb
    else {
      if ( has_post_thumbnail( $post_id ) ) {
        $_thumb_id    = get_post_thumbnail_id( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'thumb' : $_thumb_type;
      } else {
        $_thumb_id    = czr_fn_get_id_from_attachment( $post_id );
        $_thumb_type  = false !== $_thumb_id ? 'attachment' : $_thumb_type;
      }
      if ( ! $_thumb_id || empty( $_thumb_id ) ) {
        $_thumb_id    = esc_attr( czr_fn_opt( 'tc_post_list_default_thumb' ) );
        $_thumb_type  = ( false !== $_thumb_id && ! empty($_thumb_id) ) ? 'default' : $_thumb_type;
      }
    }
    $_thumb_id = ( ! $_thumb_id || empty($_thumb_id) || ! is_numeric($_thumb_id) ) ? false : $_thumb_id;

    //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
    update_post_meta( $post_id , 'tc-thumb-fld', compact( "_thumb_id" , "_thumb_type" ) );
    if ( $_return )
      return apply_filters( 'czr_set_thumb_info' , compact( "_thumb_id" , "_thumb_type" ), $post_id );
}//end of fn


function czr_fn_get_id_from_attachment( $post_id ) {
    //define a filtrable boolean to set if attached images can be used as thumbnails
    //1) must be a non single post context
    //2) user option should be checked in customizer
    $_bool = 0 != esc_attr( czr_fn_opt( 'tc_post_list_use_attachment_as_thumb' ) );

    if ( ! is_admin() )
      $_bool = ! czr_fn_is_single_post() && $_bool;

    if ( ! apply_filters( 'czr_use_attachment_as_thumb' , $_bool ) )
      return;

    //Case if we display a post or a page
    if ( 'attachment' != get_post_type( $post_id ) ) {
      //look for the last attached image in a post or page
      $tc_args = apply_filters('czr_attachment_as_thumb_query_args' , array(
          'numberposts'             =>  1,
          'post_type'               =>  'attachment',
          'post_status'             =>  null,
          'post_parent'             =>  $post_id,
          'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' ),
          'orderby'                 => 'post_date',
          'order'                   => 'DESC'
        )
      );
      $attachments              = get_posts( $tc_args );
    }

    //case were we display an attachment (in search results for example)
    elseif ( 'attachment' == get_post_type( $post_id ) && wp_attachment_is_image( $post_id ) ) {
      $attachments = array( get_post( $post_id ) );
    }

    if ( ! isset($attachments) || empty($attachments ) )
      return;
    return isset( $attachments[0] ) && isset( $attachments[0] -> ID ) ? $attachments[0] -> ID : false;
}//end of fn



/**********************
* THUMBNAIL VIEW
**********************/
/**
* Display or return the thumbnail view
* @param : thumbnail model (img, width, height), layout value, echo bool
* @package Customizr
* @since Customizr 3.0.10
*/
function czr_fn_render_thumb_view( $_thumb_model , $layout = 'span3', $_echo = true ) {
    if ( empty( $_thumb_model ) )
      return;
    //extract "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
    extract( $_thumb_model );
    $thumb_img        = ! isset( $_thumb_model) ? false : $tc_thumb;
    $thumb_img        = apply_filters( 'czr_post_thumb_img', $thumb_img, czr_fn_get_id() );
    if ( ! $thumb_img )
      return;

    //handles the case when the image dimensions are too small
    $thumb_size       = apply_filters( 'czr_thumb_size' , CZR_init::$instance -> tc_thumb_size, czr_fn_get_id()  );
    $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
    $no_effect_class  = ( esc_attr( czr_fn_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

    //default hover effect
    $thumb_wrapper    = sprintf('<div class="%5$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s" title="%3$s"></a>%4$s</div>',
                                  implode( " ", apply_filters( 'czr_thumbnail_link_class', array( $no_effect_class ) ) ),
                                  get_permalink( get_the_ID() ),
                                  esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                                  $thumb_img,
                                  implode( " ", apply_filters( 'czr_thumb_wrapper_class', array('thumb-wrapper') ) )
    );

    $thumb_wrapper    = apply_filters_ref_array( 'czr_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, czr_fn_get_id() ) );

    //cache the thumbnail view
    $html             = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
      apply_filters( 'czr_post_thumb_class', $layout ),
      $thumb_wrapper
    );
    $html = apply_filters_ref_array( 'czr_render_thumb_view', array( $html, $_thumb_model, $layout ) );
    if ( ! $_echo )
      return $html;
    echo $html;
}//end of function



/* ------------------------------------------------------------------------- *
*  Placeholder thumbs for preview demo mode
/* ------------------------------------------------------------------------- */
/* Echoes the <img> tag of the placeholder thumbnail
*  + an animated svg icon
*  the src property can be filtered
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_get_placeholder_thumb' ) ) {
  function czr_fn_get_placeholder_thumb( $_requested_size = 'thumb-standard' ) {
    $_unique_id = uniqid();
    $filter = false;

    $_sizes = array( 'thumb-medium', 'thumb-small', 'thumb-standard' );
    if ( ! in_array($_requested_size, $_sizes) )
      $_requested_size = 'thumb-medium';

    //default $img_src
    $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "/front/img/{$_requested_size}.png" );
    if ( apply_filters( 'czr-use-svg-thumb-placeholder', true ) ) {
        $_size = $_requested_size . '-empty';
        $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "/front/img/{$_size}.png" );
        $_svg_height = in_array($_size, array( 'thumb-medium', 'thumb-standard' ) ) ? 100 : 60;
        ob_start();
        ?>
        <svg class="czr-svg-placeholder <?php echo $_size; ?>" id="<?php echo $_unique_id; ?>" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M928 832q0-14-9-23t-23-9q-66 0-113 47t-47 113q0 14 9 23t23 9 23-9 9-23q0-40 28-68t68-28q14 0 23-9t9-23zm224 130q0 106-75 181t-181 75-181-75-75-181 75-181 181-75 181 75 75 181zm-1024 574h1536v-128h-1536v128zm1152-574q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5zm-1024-642h384v-128h-384v128zm-128 192h1536v-256h-828l-64 128h-644v128zm1664-256v1280q0 53-37.5 90.5t-90.5 37.5h-1536q-53 0-90.5-37.5t-37.5-90.5v-1280q0-53 37.5-90.5t90.5-37.5h1536q53 0 90.5 37.5t37.5 90.5z"/></svg>

        <script type="text/javascript">
          jQuery( function($){
            $( '#<?php echo $_unique_id; ?>' ).animateSvg( { svg_opacity : 0.3, filter_opacity : 0.5 } );
          });
        </script>
        <?php
        $_svg_placeholder = ob_get_clean();
    }
    $_img_src = apply_filters( 'czr_placeholder_thumb_src', $_img_src, $_requested_size );
    $filter = apply_filters( 'czr_placeholder_thumb_filter', false );
    //make sure we did not lose the img_src
    if ( false == $_img_src )
      $_img_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . "/front/img/{$_requested_size}.png" );
    return sprintf( '%1$s%2$s<img class="czr-img-placeholder" src="%3$s" alt="%4$s" data-czr-post-id="%5$s" />',
      isset($_svg_placeholder) ? $_svg_placeholder : '',
      false !== $filter ? $filter : '',
      $_img_src,
      get_the_title(),
      $_unique_id
    );
  }
}

/**********************
* HELPER CALLBACK
**********************/
/**
* hook wp_get_attachment_image_attributes
* Get rid of the srcset attribute (responsive images)
* @param $attr array of image attributes
* @return  array of image attributes
*
* @package Customizr
* @since Customizr 3.4.16
*/
function czr_fn_remove_srcset_attr( $attr ) {
    if ( isset( $attr[ 'srcset' ] ) ) {
      unset( $attr['srcset'] );
      //to ensure a "local" removal we have to remove this filter callback, so it won't hurt
      //responsive images sitewide
      remove_filter( current_filter(), 'czr_fn_remove_srcset_attr' );
    }
    return $attr;
}

?><?php
/*  Darken hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hex' ) ) {
   function czr_fn_darken_hex( $hex, $percent, $make_prop_value = true ) {

      $hsl      = czr_fn_hex2hsl( $hex, true );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $dark_hsl, $make_prop_value );
   }
}

/*  Lighten hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hex' ) ) {

   function czr_fn_lighten_hex($hex, $percent, $make_prop_value = true) {

      $hsl       = czr_fn_hex2hsl( $hex, true );

      $light_hsl   = czr_fn_lighten_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $light_hsl, $make_prop_value );
   }
}

/*  Darken rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_rgb' ) ) {
   function czr_fn_darken_rgb( $rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2rgb( $dark_hsl, $array, $make_prop_value );
   }
}

/*  Lighten rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_rgb' ) ) {

   function czr_fn_lighten_rgb($rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $light_hsl = czr_fn_lighten_hsl( $light_hsl, $percent );

      return czr_fn_hsl2rgb( $light_hsl, $array, $make_prop_value );

   }
}



/* Darken/Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hsl' ) ) {
   function czr_fn_darken_hsl( $hsl, $percentage, $array = true ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) - $percentage;
      $hsl[2] = ( $hsl[2] < 0 ) ? 0: $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/* Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hsl' ) ) {
   function czr_fn_lighten_hsl( $hsl, $percentage, $array = true  ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) + $percentage;
      $hsl[2] = ( $hsl[2] > 100 ) ? 1 : $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}



/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgb' ) ) {
   function czr_fn_hex2rgb( $hex, $array = false, $make_prop_value = false ) {

      $hex = trim( $hex, '# ' );

      if ( 3 == strlen( $hex ) ) {

         $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
         $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
         $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );

      }
      else {

         $r = hexdec( substr( $hex, 0, 2 ) );
         $g = hexdec( substr( $hex, 2, 2 ) );
         $b = hexdec( substr( $hex, 4, 2 ) );

      }

      $rgb = array( $r, $g, $b );

      if ( !$array ) {

         $rgb = implode( ",", $rgb );
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;

      }

      return $rgb;
  }
}

/*  Convert hexadecimal to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgba' ) ) {
   function czr_fn_hex2rgba( $hex, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb = $rgba = czr_fn_hex2rgb( $hex, $_array = true );

      $rgba[]     = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*  Convert rgb to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2rgba' ) ) {
   function czr_fn_rgb2rgba( $rgb, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb   = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb   = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb   = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $rgba[] = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*
* Following heavily based on
* https://github.com/mexitek/phpColors
* MIT License
*/
/*  Convert  rgb to hexadecimal
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hex' ) ) {
   function czr_fn_rgb2hex( $rgb, $make_prop_value = false ) {

      $rgb = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      // Convert RGB to HEX
      $hex[0] = str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
      $hex[1] = str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
      $hex[2] = str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

      $hex = implode( '', $hex );

      return $make_prop_value ? "#{$hex}" : $hex;
   }
}

/*
* heavily based on
* phpColors
*/

/*  Convert rgb to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hsl' ) ) {
   function czr_fn_rgb2hsl( $rgb, $array = false ) {

      $rgb       = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb       = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb       = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $deltas    = array();

      $RGB       = array(
         'R'   => ( $rgb[0] / 255 ),
         'G'   => ( $rgb[1] / 255 ),
         'B'   => ( $rgb[2] / 255 )
      );


      $min       = min( array_values( $RGB ) );
      $max       = max( array_values( $RGB ) );
      $span      = $max - $min;

      $H = $S    = 0;
      $L         = ($max + $min)/2;

      if ( 0 != $span ) {

         if ( $L < 0.5 ) {
            $S = $span / ( $max + $min );
         }
         else {
            $S = $span / ( 2 - $max - $min );
         }

         foreach ( array( 'R', 'G', 'B' ) as $var ) {
            $deltas[$var] = ( ( ( $max - $RGB[$var] ) / 6 ) + ( $span / 2 ) ) / $span;
         }

         if ( $max == $RGB['R'] ) {
            $H = $deltas['B'] - $deltas['G'];
         }
         else if ( $max == $RGB['G'] ) {
            $H = ( 1 / 3 ) + $deltas['R'] - $deltas['B'];
         }
         else if ( $max == $RGB['B'] ) {
            $H = ( 2 / 3 ) + $deltas['G'] - $deltas['R'];
          }

         if ($H<0) {
            $H++;
         }

         if ($H>1) {
            $H--;
         }
      }

      $hsl = array( $H*360, $S, $L );


      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/*  Convert hsl to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2rgb' ) ) {

   function czr_fn_hsl2rgb( $hsl, $array=false, $make_prop_value = false ) {

      list($H,$S,$L) = array( $hsl[0]/360, $hsl[1], $hsl[2] );

      $rgb           = array_fill( 0, 3, $L * 255 );

      if ( 0 !=$S ) {

         if ($L < 0.5 ) {

            $var_2 = $L * ( 1 + $S );

         } else {

            $var_2 = ( $L + $S ) - ( $S * $L );

         }

         $var_1  = 2 * $L - $var_2;

         $rgb[0] = czr_fn_hue2rgbtone( $var_1, $var_2, $H + ( 1/3 ) );
         $rgb[1] = czr_fn_hue2rgbtone( $var_1, $var_2, $H );
         $rgb[2] = czr_fn_hue2rgbtone( $var_1, $var_2, $H - ( 1/3 ) );
      }

      if ( !$array ) {
         $rgb = implode(",", $rgb);
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;
      }

      return $rgb;
   }
}

/* Convert hsl to hex
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2hex' ) ) {
   function czr_fn_hsl2hex( $hsl = array(), $make_prop_value = false ) {
      $rgb = czr_fn_hsl2rgb( $hsl, $array = true );

      return czr_fn_rgb2hex( $rgb, $make_prop_value );
   }
}

/* Convert hex to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2hsl' ) ) {
   function czr_fn_hex2hsl( $hex ) {
      $rgb = czr_fn_hex2rgb( $hex, true );

      return czr_fn_rgb2hsl( $rgb, true );
   }
}

/* Convert hue to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hue2rgbtone' ) ) {
   function czr_fn_hue2rgbtone( $v1, $v2, $vH ) {
      $_to_return = $v1;

      if( $vH < 0 ) {
         $vH += 1;
      }
      if( $vH > 1 ) {
         $vH -= 1;
      }

      if( (6*$vH) < 1 ) {
         $_to_return = ($v1 + ($v2 - $v1) * 6 * $vH);
      }
      elseif( (2*$vH) < 1 ) {
         $_to_return = $v2;
      }
      elseif( (3*$vH) < 2 ) {
         $_to_return = ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
      }

      return round( 255 * $_to_return );
   }
}

?>