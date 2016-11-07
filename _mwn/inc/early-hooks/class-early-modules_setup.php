<?php
/**
* FIRED ON 'after_setup_theme'
* Before the query is ready
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
if ( ! class_exists( 'TC_modules_setup' ) ) :
  class TC_modules_setup {
      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      public $slider_full_size;
      public $slider_size;

      public $tc_grid_full_size;
      public $tc_grid_size;

      public $fp_ids;

      function __construct () {
        self::$instance =& $this;
        /***************************************************************************************************************
        * SLIDER
        ***************************************************************************************************************/
        //image sizes callbacks must be fired on instanciation == after_setup_theme:10
        //because declare filters used on after_setup_theme:20
        $this -> slider_full_size   = array( 'width' => 9999 , 'height' => 500, 'crop' => true ); //size name : slider-full
        $this -> slider_size        = array( 'width' => 1170 , 'height' => 500, 'crop' => true ); //size name : slider
        $this -> tc_set_user_defined_slider_img_sizes();
        //must be fired after set user defined
        $this -> tc_declare_slider_img_sizes();
        //Default slides content
        $this -> default_slides     = array(
          1 => array(
            'title'         =>  '',
            'text'          =>  '',
            'button_text'   =>  '',
            'link_id'       =>  null,
            'link_url'      =>  null,
            'active'        =>  'active',
            'color_style'   =>  '',
            'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        TC_BASE_URL.'inc/assets/img/customizr-theme-responsive.png',
                                        __( 'Customizr is a clean responsive theme' , 'customizr' )
                                )
          ),

          2 => array(
            'title'         =>  '',
            'text'          =>  '',
            'button_text'   =>  '',
            'link_id'       =>  null,
            'link_url'      =>  null,
            'active'        =>  '',
            'color_style'   =>  '',
            'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        TC_BASE_URL.'inc/assets/img/customizr-theme-customizer.png',
                                        __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                )
          )
        );///end of slides array



        /***************************************************************************************************************
        * FEATURED PAGES
        ***************************************************************************************************************/
        //Default featured pages ids
        $this -> fp_ids             = array( 'one' , 'two' , 'three' );


        /***************************************************************************************************************
        * POST LISTS GRID
        ***************************************************************************************************************/
        //image sizes callbacks must be fired on instanciation == after_setup_theme:10
        //because declare filters used on after_setup_theme:20
        $this -> tc_grid_full_size  = array( 'width' => 1170 , 'height' => 350, 'crop' => true ); //size name : tc-grid-full
        $this -> tc_grid_size       = array( 'width' => 570 , 'height' => 350, 'crop' => true ); //size name : tc-grid
        $this -> tc_set_user_defined_grid_img_sizes();
        //must be fired after set user defined

        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_set_expanded_sticky_bool_and_val') );
        //must be fired after the bool and the val properties have been set
        add_action( 'pre_get_posts'           , array( $this, 'tc_grid_maybe_excl_first_sticky'), 20 );

      }//constructor



      /***************************************************************************************************************
      * SLIDER EARLY ACTIONS
      ***************************************************************************************************************/
      /**
      * Set user defined options for images
      * Slider's height
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function tc_set_user_defined_slider_img_sizes() {
        if ( 0 != TC_utils::$inst -> tc_opt('tc_slider_change_default_img_size' ) && 500 != TC_utils::$inst -> tc_opt('tc_slider_default_height') ) {
            add_filter( 'tc_slider_full_size'    , array($this,  'tc_set_slider_img_height') );
            add_filter( 'tc_slider_size'         , array($this,  'tc_set_slider_img_height') );
        }
      }


      /**
      * Set slider new image sizes
      * Callback of slider_full_size and slider_size filters
      * hook : might be called from after_setup_theme
      * @package Customizr
      * @since Customizr 3.2.0
      *
      */
      function tc_set_slider_img_height( $_default_size ) {
        $_default_size['height'] = esc_attr( TC_utils::$inst -> tc_opt( 'tc_slider_default_height' ) );
        return $_default_size;
      }


      /**
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function tc_declare_slider_img_sizes() {
        //slider full width
        $slider_full_size = apply_filters( 'tc_slider_full_size' , $this -> slider_full_size );
        add_image_size( 'slider-full' , $slider_full_size['width'] , $slider_full_size['height'], $slider_full_size['crop'] );

        //slider boxed
        $slider_size      = apply_filters( 'tc_slider_size' , $this -> slider_size );
        add_image_size( 'slider' , $slider_size['width'] , $slider_size['height'], $slider_size['crop'] );
      }





      /***************************************************************************************************************
      * POST LISTS GRID EARLY ACTIONS
      ***************************************************************************************************************/
      /**
      * Set user defined options for images
      * GRID
      * hook : after_setup_theme
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function tc_set_user_defined_grid_img_sizes() {
        $tc_grid_full_size     = $this -> tc_grid_full_size;
        $tc_grid_size          = $this -> tc_grid_size;
        $_user_grid_height     = is_numeric( TC_utils::$inst -> tc_opt('tc_grid_thumb_height' ) )? esc_attr( TC_utils::$inst -> tc_opt('tc_grid_thumb_height') ) : $tc_grid_full_size['height'];

        add_image_size( 'tc-grid-full', $tc_grid_full_size['width'], $_user_grid_height, $tc_grid_full_size['crop'] );
        add_image_size( 'tc-grid', $tc_grid_size['width'], $_user_grid_height, $tc_grid_size['crop'] );

        if ( $_user_grid_height != $tc_grid_full_size['height'] )
          add_filter( 'tc_grid_full_size', array( $this,  'tc_set_grid_img_height') );
        if ( $_user_grid_height != $tc_grid_size['height'] )
          add_filter( 'tc_grid_size'     , array( $this,  'tc_set_grid_img_height') );
      }


      /**
      * Set post list desgin new image sizes
      * Callback of tc_grid_full_size and tc_grid_size filters
      *
      * @package Customizr
      * @since Customizr 3.1.12
      *
      */
      function tc_set_grid_img_height( $_default_size ) {
        $_default_size['height'] =  esc_attr( TC_utils::$inst -> tc_opt('tc_grid_thumb_height') ) ;
        return $_default_size;
      }



      /**
      * hook : pre_get_posts : 10
      *
      * @return void()
      * check if we have to expand the first sticky post
      * set 2 properties accessible in the grid child class
      */
      public function tc_grid_set_expanded_sticky_bool_and_val( $query = null ){
        //user option has to be enabled
        if ( ! apply_filters( 'tc_grid_expand_featured', esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_expand_featured') ) ) )
          return;

        global $wp_query, $wpdb;
        $query = ( $query ) ? $query : $wp_query;

        if ( ! $query->is_main_query() )
          TC_modules_control::$expanded_sticky_bool = false;
        if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $wp_query->is_posts_page ) )
          TC_modules_control::$expanded_sticky_bool = false;



        if ( ! TC_modules_control::$expanded_sticky_bool ) {
          $_sticky_posts = get_option('sticky_posts');
          // get last published sticky post
          if ( is_array($_sticky_posts) && ! empty( $_sticky_posts ) ) {
            $_where = implode(',', $_sticky_posts );
            TC_modules_control::$expanded_sticky_val = $wpdb->get_var(
                   "
                   SELECT ID
                   FROM $wpdb->posts
                   WHERE ID IN ( $_where )
                   ORDER BY post_date DESC
                   LIMIT 1
                   "
            );
            TC_modules_control::$expanded_sticky_bool = true;
          } else {
            TC_modules_control::$expanded_sticky_val = null;
            TC_modules_control::$expanded_sticky_bool = false;
          }
        }
      }


      /**
      * hook : pre_get_posts : 20
      * exclude the first sticky post
      */
      function tc_grid_maybe_excl_first_sticky( $query ){
        if ( TC_modules_control::$instance -> tc_is_grid_enabled() && TC_modules_control::$expanded_sticky_bool )
          $query->set('post__not_in', array( TC_modules_control::$expanded_sticky_val ) );
      }
  }//end of class
endif;