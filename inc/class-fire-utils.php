<?php
/**
* Defines filters and actions used in several templates/classes
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

class TC_utils {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $default_options;
    public $options;//not used in customizer context only
    public $is_customizing;

    function __construct () {

        self::$instance =& $this;

        //get all options
        add_filter  ( '__options'                           , array( $this , 'tc_get_theme_options' ), 10, 1);
        //get single option
        add_filter  ( '__get_option'                        , array( $this , 'tc_get_option' ), 10, 2 );

        //some useful filters
        add_filter  ( '__ID'                                , array( $this , 'tc_get_the_ID' ));
        add_filter  ( '__screen_layout'                     , array( $this , 'tc_get_current_screen_layout' ) , 10 , 2 );
        add_filter  ( '__is_home'                           , array( $this , 'tc_is_home' ) );
        add_filter  ( '__is_home_empty'                     , array( $this , 'tc_is_home_empty' ) );
        add_filter  ( '__post_type'                         , array( $this , 'tc_get_post_type' ) );
        add_filter  ( '__is_no_results'                     , array( $this , 'tc_is_no_results') );
        add_filter  ( '__article_selectors'                 , array( $this , 'tc_article_selectors' ));

        //social networks
        add_filter  ( '__get_socials'                       , array( $this , 'tc_get_social_networks' ) );

        //WP filters
        add_filter  ( 'the_content'                         , array( $this , 'tc_fancybox_content_filter' ));
        add_filter  ( 'wp_title'                            , array( $this , 'tc_wp_title' ), 10, 2 );

        //default options
        $this -> is_customizing   = $this -> tc_is_customizing();
        $this -> default_options  = $this -> tc_get_default_options();
    }



    /**
    * Returns a boolean on the customizer's state
    *
    * @package Customizr
    * @since Customizr 3.1.11
    */
    function tc_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      global $pagenow;
      $is_customizing = false;
      if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow ) {
              $is_customizing = true;
      } else if ( ! is_admin() && isset($_REQUEST['wp_customize']) ) {
              $is_customizing = true;
      }
      return $is_customizing;
    }

    


   /**
    * Returns the default options array
    *
    * @package Customizr
    * @since Customizr 3.1.11
    */
    function tc_get_default_options() {
      $def_options = get_option( "tc_theme_defaults");
      
      //Always update the default option when (OR) :
      // 1) they are not defined
      // 2) customzing => takes into account if user has set a filter or added a new customizer setting
      // 3) theme version not defined
      // 4) versions are different
      if ( ! $def_options || $this -> is_customizing || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , CUSTOMIZR_VER ) ) {
        $def_options          = $this -> tc_generate_default_options( $this -> tc_customizer_map( $get_default_option = 'true' ) , 'tc_theme_options' );
        //Adds the version
        $def_options['ver']   =  CUSTOMIZR_VER;
        update_option( "tc_theme_defaults" , $def_options );
      }
      return apply_filters( 'tc_default_options', $def_options );
    }




    /**
    * Generates the default options array from a customizer map + add slider option
    *
    * @package Customizr
    * @since Customizr 3.0.3
    */
    function tc_generate_default_options( $map, $option_group = null ) {
        //do we have to look in a specific group of option (plugin?)
        $option_group   = is_null($option_group) ? 'tc_theme_options' : $option_group;

        //initialize the default array with the sliders options
        //$defaults       = is_null($option_group) ? array( 'tc_sliders' => array() ) : array() ;

        foreach ($map['add_setting_control'] as $key => $options) {

          //check it is a customizr option
          if( false !== strpos( $key  , $option_group ) ) {

            //isolate the option name between brackets [ ]
            $option_name = '';
            $option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
            if ( isset( $match[1][0] ) ) 
              {
                  $option_name = $match[1][0];
              }

            //write default option in array
            if(isset($options['default'])) {
              $defaults[$option_name] = $options['default'];
            }
            else {
              $defaults[$option_name] = null;
            }
           
          }//end if

        }//end foreach

      return $defaults;
    }



    /**
    * Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
    * @package Customizr
    * @since Customizr 1.0
    *
    */
    function tc_get_theme_options ( $option_group = null ) {
        //do we have to look in a specific group of option (plugin?)
        $option_group       = is_null($option_group) ? 'tc_theme_options' : $option_group;
        $saved              = (array) get_option( $option_group );
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
     function tc_get_option( $option_name , $option_group = null ) {
        //do we have to look in a specific group of option (plugin?)
        $option_group       = is_null($option_group) ? 'tc_theme_options' : $option_group;
        $saved              = (array) get_option( $option_group );
        $defaults           = $this -> default_options;
        $__options          = wp_parse_args( $saved, $defaults );
        //$options            = array_intersect_key( $saved , $defaults);
        $returned_option    = isset($__options[$option_name]) ? $__options[$option_name] : false;
      return apply_filters( 'tc_get_option' , $returned_option , $option_name , $option_group );
    }




    /**
    * Returns the "real" queried post ID or if !isset, get_the_ID()
    * Checks some contextual booleans
    * 
    * @package Customizr
    * @since Customizr 1.0
    */
    function tc_get_the_ID()  {
        $queried_object   = get_queried_object();
        $tc_id            = get_post() ? get_the_ID() : null;
        $tc_id            = ( isset ($queried_object -> ID) ) ? $queried_object -> ID : $tc_id;
        return ( is_404() || is_search() || is_archive() ) ? null : $tc_id;
    }




    /**
    * This function returns the layout (sidebar(s), or full width) to apply to a context
    * 
    * @package Customizr
    * @since Customizr 1.0
    */
    function tc_get_current_screen_layout ( $post_id , $sidebar_or_class) {
        $__options                    = tc__f ( '__options' );

        global $post;
        
        //Article wrapper class definition
        $global_layout                = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );

        /* DEFAULT LAYOUTS */
        //get the global default layout
        $tc_sidebar_global_layout     = $__options['tc_sidebar_global_layout'];
        //get the post default layout
        $tc_sidebar_post_layout       = $__options['tc_sidebar_post_layout'];
        //get the page default layout
        $tc_sidebar_page_layout       = $__options['tc_sidebar_page_layout'];

        //what is the default layout we want to apply? By default we apply the global default layout
        $tc_sidebar_default_layout    = $tc_sidebar_global_layout;
        if ( is_single() )
          $tc_sidebar_default_layout  = $tc_sidebar_post_layout;
        if ( is_page() )
          $tc_sidebar_default_layout  = $tc_sidebar_page_layout;

        //builds the default layout option array including layout and article class
        $class_tab  = $global_layout[$tc_sidebar_default_layout];
        $class_tab  = $class_tab['content'];
        $tc_screen_layout             = array(
                    'sidebar' => $tc_sidebar_default_layout,
                    'class'   => $class_tab
        );

        //checks if the 'force default layout' option is checked and return the default layout before any specific layout
        $force_layout = $__options['tc_sidebar_force_layout'];
        if( $force_layout == 1) {
          $class_tab  = $global_layout[$tc_sidebar_global_layout];
          $class_tab  = $class_tab['content'];
          $tc_screen_layout = array(
            'sidebar' => $tc_sidebar_global_layout,
            'class'   => $class_tab
          );
          return $tc_screen_layout[$sidebar_or_class];
        }

        //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
        $tc_specific_post_layout    = false;
        global $wp_query;
        //if we are displaying an attachement, we use the parent post/page layout
        if ( $post && 'attachment' == $post -> post_type ) {
          $tc_specific_post_layout  = esc_attr(get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ));
        }
        //for a singular post or page OR for the posts page
        elseif ( is_singular() || $wp_query -> is_posts_page ) {
          $tc_specific_post_layout  = esc_attr(get_post_meta( $post_id, $key = 'layout_key' , $single = true ));
        }
        
        //checks if we display home page, either posts or static page and apply the customizer option
        if( (is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page()) {
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
    function tc_fancybox_content_filter( $content) {
      $tc_fancybox = esc_attr( tc__f( '__get_option' , 'tc_fancybox' ) );

      if ( 1 == $tc_fancybox ) 
      {
          global $post;
          if ( !isset($post) )
            return;
          $pattern ="/<a(.*?)href=( '|\")(.*?).(bmp|gif|jpeg|jpg|png)( '|\")(.*?)>/i";
          $replacement = '<a$1href=$2$3.$4$5 class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'"$6>';
          $content = preg_replace( $pattern, $replacement, $content);
      }

      

      return apply_filters( 'tc_fancybox_content_filter', $content );
    }




    /**
    * Title element formating
    *
    * @since Customizr 2.1.6
    *
    */
    function tc_wp_title( $title, $sep ) {
      global $paged, $page;

      if ( is_feed() )
        return $title;

      // Add the site name.
      $title .= get_bloginfo( 'name' );

      // Add the site description for the home/front page.
      $site_description = get_bloginfo( 'description' , 'display' );
      if ( $site_description && tc__f('__is_home') )
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
    function tc_is_home() {
      
      //get info whether the front page is a list of last posts or a page
      return ( (is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page() ) ? true : false;
    }

    



    /**
    * Check if we show posts or page content on home page
    *
    * @since Customizr 3.0.6
    *
    */
    function tc_is_home_empty() {
      
      //check if the users has choosen the "no posts or page" option for home page
      return ( (is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
    }




    /**
    * Return object post type
    *
    * @since Customizr 3.0.10
    *
    */
    function tc_get_post_type() {
      global $post;

      
      if ( !isset($post) )
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
    function tc_get_post_class( $class = '', $post_id = null ) {
      // Separates classes with a single space, collates classes for post DIV
      return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
    }






    /**
    * Boolean : check if we are in the no search results case
    *
    * @package Customizr
    * @since 3.0.10
    */
    function tc_is_no_results() {
      global $wp_query;
      return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
    }





    /**
    * Displays the selectors of the article depending on the context
    * 
    * @package Customizr
    * @since 3.1.0
    */
    function tc_article_selectors() {
      
      //gets global vars
      global $post;
      global $wp_query;

      //declares selector var
      $selectors                  = '';

      // SINGLE POST
      $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
      $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '.$this -> tc_get_post_class('row-fluid') ) : $selectors;

      // POST LIST
      $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !tc__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
      $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '.$this -> tc_get_post_class('row-fluid') ) : $selectors;

      // PAGE
      $page_selector_bool         = isset($post) && 'page' == tc__f('__post_type') && is_singular() && !tc__f( '__is_home_empty');
      $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '.$this -> tc_get_post_class('row-fluid') ) : $selectors;

      // ATTACHMENT
      //checks if attachement is image and add a selector
      $format_image               = wp_attachment_is_image() ? 'format-image' : '';
      $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '.$this -> tc_get_post_class(array('row-fluid', $format_image) ) ) : $selectors;

      // NO SEARCH RESULTS
      $selectors                  = ( is_search() && 0 == $wp_query -> post_count ) ? apply_filters( 'tc_no_results_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

      // 404
      $selectors                  = is_404() ? apply_filters( 'tc_404_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

      echo apply_filters( 'tc_article_selectors', $selectors );

    }//end of function




    /**
    * Gets the social networks list defined in customizer options
    * 
    * @package Customizr
    * @since Customizr 3.0.10 
    */
    function tc_get_social_networks() {
      $__options    = tc__f( '__options' );

      //gets the social network array
      $socials      = apply_filters( 'tc_default_socials' , TC_init::$instance -> socials );

      //declares some vars
      $target       = apply_filters( 'tc_socials_target', 'target=_blank' );
      $html         = '';

      foreach ( $socials as $key => $data ) {
        if ( $__options[$key] != '' ) {
            //gets height and width from image, we check if getimagesize can be used first with the error control operator
            $width = $height = '';
            if ( isset($data['custom_icon_url']) && @getimagesize($data['custom_icon_url']) ) { list( $width, $height ) = getimagesize($data['custom_icon_url']); }

            //there is one exception : rss feed has no target _blank and special icon title
            $html .= sprintf('<a class="%1$s" href="%2$s" title="%3$s" %4$s %5$s>%6$s</a>',
                apply_filters( 'tc_social_link_class',
                              sprintf('social-icon icon-%1$s' ,
                                ( $key == 'tc_rss' ) ? 'feed' : str_replace('tc_', '', $key)
                                )
                ),
                esc_url( $__options[$key]),
                isset($data['link_title']) ?  call_user_func( '__' , $data['link_title'] , 'customizr' ) : '' ,
                ( $key == 'tc_rss' ) ? '' : $target,
                apply_filters( 'tc_additional_social_attributes', '' , $key),
                ( isset($data['custom_icon_url']) && !empty($data['custom_icon_url']) ) ? sprintf('<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>',
                                                        $data['custom_icon_url'],
                                                        $width,
                                                        $height,
                                                        isset($data['link_title']) ? call_user_func( '__' , $data['link_title'] , 'customizr' ) : ''
                                                      ) : ''
            );
        }
      }
      return $html;
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




  function tc_generates_socials() {
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
                    'control'     => 'TC_controls' ,
                    'label'         => ( isset($data['option_label']) ) ? call_user_func( '__' , $data['option_label'] , 'customizr' ) : $key,
                    'section'       => 'tc_social_settings' ,
                    'type'          => 'url',
                    'priority'        => $priority
                  );
      $incr += 5;
    }

    return $socials_setting_control;
  }



    function tc_get_skins($path) {
    //checks if path exists
    if ( !file_exists($path) )
      return;

    //gets the skins from init
    $default_skin_list    = TC_init::$instance -> skins;

    //declares the skin list array
    $skin_list        = array();

    //gets the skins : filters the files with a css extension and generates and array[] : $key = filename.css => $value = filename
    $files            = scandir($path) ;
    foreach ( $files as $file) {
        //skips the minified
        if ( false !== strpos($file, '.min.') )
          continue;
        
        if ( $file[0] != '.' && !is_dir($path.$file) ) {
          if ( substr( $file, -4) == '.css' ) {
            $skin_list[$file] = isset($default_skin_list[$file]) ?  call_user_func( '__' , $default_skin_list[$file] , 'customizr' ) : substr_replace( $file , '' , -4 , 4);
          }
        }
      }//endforeach

      return $skin_list;
  }//end of function




  /**
  * Returns the layout choices array
  * 
  * @package Customizr
  * @since Customizr 3.1.0
  */
  function tc_layout_choices() {
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
  function tc_slider_choices() {
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
  function tc_skin_choices() {
      $parent_skins     = $this -> tc_get_skins(TC_BASE .'inc/css');
      $child_skins    = ( TC___::$instance -> tc_is_child() && file_exists(TC_BASE_CHILD .'inc/css') ) ? $this -> tc_get_skins(TC_BASE_CHILD .'inc/css') : array();
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
                              'default'   =>  'blue.css' ,
                              'label'     =>  __( 'Choose a predefined skin' , 'customizr' ),
                              'section'   =>  'tc_skins_settings' ,
                              'type'      =>  'select' ,
                              'choices'   =>  $this -> tc_skin_choices()
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
            //logo upload
            'tc_theme_options[tc_logo_upload]'  => array(
                              'control'   =>  'WP_Customize_Upload_Control' ,
                              'label'     =>  __( 'Logo Upload (supported formats : .jpg, .png, .gif)' , 'customizr' ),
                              'section'   =>  'tc_logo_settings' ,
                              'sanitize_callback' => array( $this , 'tc_sanitize_uploads' ),
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
                              'control'   =>  'WP_Customize_Upload_Control' ,
                              'label'       => __( 'Favicon Upload (supported formats : .ico, .png, .gif)' , 'customizr' ),
                              'section'   =>  'tc_logo_settings' ,
                              'sanitize_callback' => array( $this , 'tc_sanitize_uploads' ),
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
                              'default'       => 'demo' ,
                              'control'   => 'TC_controls' ,
                              'title'       => __( 'Slider options' , 'customizr' ),
                              'label'       => __( 'Select front page slider' , 'customizr' ),
                              'section'     => 'tc_frontpage_settings' ,
                              'type'        => 'select' ,
                              //!important
                              'choices'     => ($get_default == true) ? null : $this -> tc_slider_choices(),
                              'priority'      => 20,
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

            //slider check message
            'slider_check'            => array(
                              'setting_type'  =>  null,
                              'section'     => 'tc_frontpage_settings' ,
                              'type'      => 'slider-check' ,
                              'priority'      => 40,
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
                              'default'       => 0,
                              'control'   => 'TC_controls' ,
                              'label'       => __( 'Enable comments on pages' , 'customizr' ),
                              'section'     => 'tc_page_comments' ,
                              'type'        => 'checkbox' ,
                              'notice'    => __( 'This option will enable comments on pages. You can disable comments for a single page in the quick edit mode of the page list screen.' , 'customizr' ),
            )
    );
    $comment_option_map = apply_filters( 'tc_comment_option_map', $comment_option_map , $get_default );



    /*-----------------------------------------------------------------------------------------------------
                             SOCIAL POSITIONS AND NETWORKS
    ------------------------------------------------------------------------------------------------------*/
    $social_layout_map = array(
            //Position checkboxes
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
                              'default'       => 1,
                              'control'   => 'TC_controls' ,
                              'label'       => __( 'Enable/disable retina support' , 'customizr' ),
                              'section'     => 'tc_image_settings' ,
                              'type'        => 'checkbox' ,
                              'notice'    => __( 'If enabled, your website will include support for high resolution devices.' , 'customizr' ),
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
                              'notice'    => __( 'Always use this field to add your custom css instead of editing directly the style.css file : it will not be deleted during theme updates. You can also paste your custom css in the style.css file of a child theme.' , 'customizr' )
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
    $customizer_map = array_merge( $remove_section , $add_section , $get_setting , $add_setting_control );

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
  

}//end of class