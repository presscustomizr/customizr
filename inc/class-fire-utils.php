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
    }





    /**
    * Return the default options array from a customizer map + add slider option
    *
    * @package Customizr
    * @since Customizr 3.3.0
    */
    function tc_get_default_options( $map, $option_group = null ) {
        //do we have to look in a specific group of option (plugin?)
        $option_group   = is_null($option_group) ? 'tc_theme_options' : $option_group;

        //initialize the default array with the sliders options
        $defaults       = is_null($option_group) ? array( 'tc_sliders' => array() ) : array() ;

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
        $option_group                   = is_null($option_group) ? 'tc_theme_options' : $option_group;

        $saved                          = (array) get_option( $option_group );

        $defaults                       = $this -> tc_get_default_options( TC_customize::$instance -> tc_customizer_map( $get_default = 'true' ) , $option_group );

        $__options                      = wp_parse_args( $saved, $defaults );
      
        //$__options                      = array_intersect_key( $__options, $defaults );
        

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
        $option_group                   = is_null($option_group) ? 'tc_theme_options' : $option_group;

        $saved              = (array) get_option( $option_group );

        $defaults           = $this -> tc_get_default_options( TC_customize::$instance -> tc_customizer_map( $get_default = 'true' ) , $option_group );

        $__options          = wp_parse_args( $saved, $defaults );

        //$options            = array_intersect_key( $saved , $defaults);

      return $__options[$option_name];
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
        $global_layout                = TC_init::$instance -> global_layout;

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
      $socials      = TC_init::$instance -> socials;

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
                isset($data['link_title']) ? $data['link_title'] : '' ,
                ( $key == 'tc_rss' ) ? '' : $target,
                apply_filters( 'tc_additional_social_attributes', '' , $key),
                ( isset($data['custom_icon_url']) && !empty($data['custom_icon_url']) ) ? sprintf('<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s"/>',
                                                        $data['custom_icon_url'],
                                                        $width,
                                                        $height,
                                                        isset($data['link_title']) ? $data['link_title'] : ''
                                                      ) : ''
            );
        }
      }
      return $html;
    }

}//end of class