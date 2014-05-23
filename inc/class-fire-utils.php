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

        //Get default options
        $map = tc__f('__customize_map', $get_default = 'true' );
        $this -> default_options  = $this -> tc_get_default_options_from_customizer_map($map);
        add_filter  ( '__get_default_options'               , array( $this , 'tc_get_default_options' ) , 10);
        add_filter  ( '__default_options_from_customizer_map' , array( $this , 'tc_get_default_options_from_customizer_map' ));


        //if we are NOT in a customization context
        if ( !isset( $_REQUEST['wp_customize'] ) ) {
          $this -> options = $this -> tc_get_theme_options();
           //get all options
          add_filter  ( '__options'                         , array( $this , 'tc_get_theme_options_fast' ) );
           //get single option
          add_filter  ( '__get_option'                      , array( $this , 'tc_get_option_fast' ) );
        }
        else {
           //get all options
          add_filter  ( '__options'                         , array( $this , 'tc_get_theme_options' ) );
          //get single option
          add_filter  ( '__get_option'                      , array( $this , 'tc_get_option' ) );
        }

        //some useful filters
        add_filter  ( '__ID'                                , array( $this , 'tc_get_the_ID' ));
        add_filter  ( '__screen_layout'                     , array( $this , 'tc_get_current_screen_layout' ) , 10 , 2 );
        add_filter  ( '__is_home'                           , array( $this , 'tc_is_home' ) );
        add_filter  ( '__is_home_empty'                     , array( $this , 'tc_is_home_empty' ) );
        add_filter  ( '__post_type'                         , array( $this , 'tc_get_post_type' ) );
        add_filter  ( '__get_post_class'                    , array( $this , 'tc_get_post_class') , 10, 2 );
        add_filter  ( '__is_no_results'                     , array( $this , 'tc_is_no_results') );

        //WP filters
        add_filter  ( 'the_content'                         , array( $this , 'tc_fancybox_content_filter' ));
        add_filter  ( 'wp_title'                            , array( $this , 'tc_wp_title' ), 10, 2 );
    }



    /**
    * Get the saved options in Customizer Screen, merge them with the default theme options array and return the updated global options array
    * @package Customizr
    * @since Customizr 1.0
    *
    */
    function tc_get_theme_options () {
         
          $saved                          = (array) get_option( 'tc_theme_options' );

          $defaults                       = $this -> default_options;

          $__options                      = wp_parse_args( $saved, $defaults );
        
          //$__options                      = array_intersect_key( $__options, $defaults );
          tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        return $__options;
    }




    /**
    * Are we in a customization context? If yes, we must get the options dynamically from database
    * 
    * @package Customizr
    * @since Customizr 3.0.10
    *
    */
    function tc_get_theme_options_fast () {
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      return  $__options                      = $this -> options;
    }





    /**
   * Return the default options array from a customizer map + add slider option
   *
   * @package Customizr
   * @since Customizr 3.3.0
   */
    function tc_get_default_options() {
      $map = tc__f('__customize_map', $get_default = 'true' );

      $customizer_defaults = $this -> tc_get_default_options_from_customizer_map($map);

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      return $customizer_defaults;
    }





   /**
   * Return the default options array from a customizer map + add slider option
   *
   * @package Customizr
   * @since Customizr 3.3.0
   */
    function tc_get_default_options_from_customizer_map($map) {

       //record for debug
      //tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      
      $defaults = array(
        //initialize the default array with the sliders options
        'tc_sliders' => array(),
      );

      foreach ($map['add_setting_control'] as $key => $options) {

        //check it is a customizr option
        if(false !== strpos($haystack = $key  , $needle = 'tc_theme_options')) {

          //isolate the option name between brackets [ ]
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
     * Returns an option from the options array of the theme.
     *
     * @package Customizr
     * @since Customizr 1.0
     */
    function tc_get_option( $option_name) {
        
        $saved              = (array) get_option( 'tc_theme_options' );

        $defaults           = $this -> default_options;

        $__options          = wp_parse_args( $saved, $defaults );

        //$options            = array_intersect_key( $saved , $defaults);
       
        tc__f( 'rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      return $__options[$option_name];
    }





     /**
     * Are we in a customization context? If yes, we must get the options dynamically from database
     *
     * @package Customizr
     * @since Customizr 3.0.10
     */
    function tc_get_option_fast( $option_name) {
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      $__options          = $this -> options;
      return $__options[$option_name];
    }




      /**
      * This function is similiar to the wordpress function get_the_ID but add checks to some contextual booleans
      * 
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_the_ID()  {
          if (is_404() || is_search()) {
            return null;
          }
          else {
              $id  = get_the_ID();
          }
          tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        return $id;
      }





      /**
      * This function returns the layout (sidebars, or full width) to apply to a post or a context
      * 
      * @package Customizr
      * @since Customizr 1.0
      */
      function tc_get_current_screen_layout ( $post_id , $sidebar_or_class) {
        $__options              = tc__f ( '__options' );

        global $post;
        
        //Article wrapper class definition
          $class_tab = array(
            'r' => 'span9' ,
            'l' => 'span9' ,
            'b' => 'span6' ,
            'f' => 'span12' ,
            );

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

        //build the default layout option array including layout and article class
        $tc_screen_layout = array(
            'sidebar' => $tc_sidebar_default_layout,
            'class'   => $class_tab[$tc_sidebar_default_layout]
          );

        //finally we check if the 'force default layout' option is checked and return the default layout before any specific layout
        $force_layout = $__options['tc_sidebar_force_layout'];
        if( $force_layout == 1) {
          $tc_screen_layout = array(
            'sidebar' => $tc_sidebar_global_layout,
            'class'   => $class_tab[$tc_sidebar_global_layout]
          );
          return $tc_screen_layout[$sidebar_or_class];
        }

        //get the front page layout
        $tc_front_layout          =  $__options['tc_front_layout'];

        //get info whether the front page is a list of last posts or a page
        $tc_what_on_front         = get_option( 'show_on_front' );


        //get the post specific layout if any, and if we don't apply the default layout
        //if we are displaying an attachement, we use the parent post/page layout
        if ( $post && 'attachment' == $post -> post_type ) {
          $tc_specific_post_layout  = esc_attr(get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ));
        }
        else {
          $tc_specific_post_layout  = esc_attr(get_post_meta( $post_id, $key = 'layout_key' , $single = true ));
        }
        

        if((is_home() && $tc_what_on_front == 'posts' ) || is_front_page())
           $tc_specific_post_layout = $tc_front_layout;

        if( $tc_specific_post_layout) {
            $tc_screen_layout = array(
            'sidebar' => $tc_specific_post_layout,
            'class'   => $class_tab[$tc_specific_post_layout]
          );
        }

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        return $tc_screen_layout[$sidebar_or_class];
      }






     
    /**
     * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post.
     * 
     * @package Customizr
     * @since Customizr 2.0.7
     */
    function tc_fancybox_content_filter( $content) {
      $tc_fancybox = esc_attr( tc__f( '__get_option' , 'tc_fancybox' ) );

      if ( $tc_fancybox == 1 ) 
      {
           global $post;
           $pattern ="/<a(.*?)href=( '|\")(.*?).(bmp|gif|jpeg|jpg|png)( '|\")(.*?)>/i";
           $replacement = '<a$1href=$2$3.$4$5 class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'" title="'.$post->post_title.'"$6>';
           $content = preg_replace( $pattern, $replacement, $content);
      }

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      return $content;
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

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

      return $title;
    }




  /**
   * Check if we are displaying posts lists or front page
   *
   * @since Customizr 3.0.6
   *
   */
    function tc_is_home() {
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
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
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
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

      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
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

}//end of class