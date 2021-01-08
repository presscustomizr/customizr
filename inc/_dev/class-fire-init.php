<?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
*
*
*/
if ( ! class_exists( 'CZR_init' ) ) :
  class CZR_init {
      //declares the filtered default settings
      public $global_layout;
      public $skins;
      public $font_selectors;
      public $footer_widgets;
      public $widgets;
      public $post_list_layout;
      public $post_formats_with_no_heading;
      public $content_404;
      public $content_no_results;

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {

          self::$instance =& $this;
          //Default layout settings
          $this -> global_layout      = array(
              'r' => array(
                  'content'       => 'span9',
                  'sidebar'       => 'span3',
                  'customizer'    => __( 'Right sidebar' , 'customizr' ),
                  'metabox'       => __( 'Right sidebar' , 'customizr' ),
              ),
              'l' => array(
                  'content'       => 'span9',
                  'sidebar'       => 'span3',
                  'customizer'    => __( 'Left sidebar' , 'customizr' ),
                  'metabox'       => __( 'Left sidebar' , 'customizr' ),
              ),
              'b' => array(
                  'content'       => 'span6',
                  'sidebar'       => 'span3',
                  'customizer'    => __( '2 sidebars : Right and Left' , 'customizr' ),
                  'metabox'       => __( '2 sidebars : Right and Left' , 'customizr' ),
              ),
              'f' => array(
                  'content'       => 'span12',
                  'sidebar'       => false,
                  'customizer'    => __( 'No sidebars : full width layout', 'customizr' ),
                  'metabox'       => __( 'No sidebars : full width layout' , 'customizr' ),
              ),
          );

          //Default skins array
          $this -> skins              =  array(
              'blue.css'        =>  __( 'Blue' , 'customizr' ),
              'black.css'       =>  __( 'Black' , 'customizr' ),
              'black2.css'      =>  __( 'Flat black' , 'customizr' ),
              'grey.css'        =>  __( 'Grey' , 'customizr' ),
              'grey2.css'       =>  __( 'Light grey' , 'customizr' ),
              'purple2.css'     =>  __( 'Flat purple' , 'customizr' ),
              'purple.css'      =>  __( 'Purple' , 'customizr' ),
              'red2.css'        =>  __( 'Flat red' , 'customizr' ),
              'red.css'         =>  __( 'Red' , 'customizr' ),
              'orange.css'      =>  __( 'Orange' , 'customizr' ),
              'orange2.css'     =>  __( 'Flat orange' , 'customizr'),
              'yellow.css'      =>  __( 'Yellow' , 'customizr' ),
              'yellow2.css'     =>  __( 'Flat yellow' , 'customizr' ),
              'green.css'       =>  __( 'Green' , 'customizr' ),
              'green2.css'      =>  __( 'Light green' , 'customizr'),
              'blue3.css'       =>  __( 'Green blue' , 'customizr'),
              'blue2.css'       =>  __( 'Light blue ' , 'customizr' )
          );


          $this -> font_selectors     = array(
              'titles' => implode(',' , apply_filters( 'tc-titles-font-selectors' , array('.site-title' , '.site-description', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
              'body'   => implode(',' , apply_filters( 'tc-body-font-selectors' , array('body' , '.navbar .nav>li>a') ) )
          );

          //Default footer widgets
          $this -> footer_widgets     = array(
              'footer_one'    => array(
                              'name'                 => __( 'Footer Widget Area One' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' )
              ),
              'footer_two'    => array(
                              'name'                 => __( 'Footer Widget Area Two' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' )
              ),
              'footer_three'   => array(
                              'name'                 => __( 'Footer Widget Area Three' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' )
              )
          );//end of array

          //Default post list layout
          $this -> post_list_layout   = array(
              'content'           => 'span8',
              'thumb'             => 'span4',
              'show_thumb_first'  => false,
              'alternate'         => true
          );

          //Defines post formats with no headers
          $this -> post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

          //Default 404 content
          $this -> content_404        = array(
              'quote'             => '',
              'author'            => '',
              'text'              => ''
          );

          //Default no search result content
          $this -> content_no_results = array(
              'quote'             => '',
              'author'            => '',
              'text'              => ''
          );

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter('body_class'                              , array( $this , 'czr_fn_set_body_classes') );
      }//end of constructor



      /**
      * Returns the active path+skin.css or tc_common.css
      *
      * @package Customizr
      * @since Customizr 3.0.15
      */
      function czr_fn_get_style_src( $_wot = 'skin' ) {
          $_sheet    = ( 'skin' == $_wot ) ? esc_attr( czr_fn_opt( 'tc_skin' ) ) : 'tc_common.css';
          $_sheet    = esc_attr( czr_fn_opt( 'tc_minified_skin' ) ) ? str_replace('.css', '.min.css', $_sheet) : $_sheet;

          //Finds the good path : are we in a child theme and is there a skin to override?
          $remote_path    = ( czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/' : false ;
          $remote_path    = ( ! $remote_path && file_exists(TC_BASE .'inc/assets/css/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/' : $remote_path ;
          //Checks if there is a rtl version of common if needed
          if ( 'skin' != $_wot && ( is_rtl() || ( defined( 'WPLANG' ) && ( 'ar' == WPLANG || 'he_IL' == WPLANG ) ) ) ){
            $remote_rtl_path   = ( czr_fn_is_child() && file_exists(TC_BASE_CHILD .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL_CHILD .'inc/assets/css/rtl/' : false ;
            $remote_rtl_path   = ( ! $remote_rtl_path && file_exists(TC_BASE .'inc/assets/css/rtl/' . $_sheet) ) ? TC_BASE_URL .'inc/assets/css/rtl/' : $remote_rtl_path;
            $remote_path       = $remote_rtl_path ? $remote_rtl_path : $remote_path;
          }

          //Defines the active skin and fallback to blue.css if needed
          if ( 'skin' == $_wot )
            $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/grey.css';
          else
            $tc_get_style_src  = $remote_path ? $remote_path.$_sheet : TC_BASE_URL.'inc/assets/css/tc_common.css';

          return apply_filters ( 'tc_get_style_src' , $tc_get_style_src , $_wot );
      }





      /*
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_body_classes( $_classes ) {
          if ( czr_fn_is_checked( 'tc_link_hover_effect' ) )
            array_push( $_classes, 'tc-fade-hover-links' );
          if ( czr_fn_is_customizing() )
            array_push( $_classes, 'is-customizing' );
          if ( wp_is_mobile() )
            array_push( $_classes, 'tc-is-mobile' );
          if ( czr_fn_is_checked( 'tc_enable_dropcap' ) )
            array_push( $_classes, esc_attr( czr_fn_opt( 'tc_dropcap_design' ) ) );

          //adds the layout
          $_layout = CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
          if ( in_array( $_layout, array('b', 'l', 'r' , 'f') ) ) {
            array_push( $_classes, sprintf( 'tc-%s-sidebar',
              'f' == $_layout ? 'no' : $_layout
            ) );
          }

          //IMAGE CENTERED
          if ( (bool) esc_attr( czr_fn_opt( 'tc_center_img') ) ){
            $_classes = array_merge( $_classes , array( 'tc-center-images' ) );
          }

          //SKIN CLASS
          $_skin = sprintf( 'skin-%s' , basename( $this -> czr_fn_get_style_src() ) );
          array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

          //THEME VER + child theme info
          $ver = str_replace('.', '-', CUSTOMIZR_VER );
          $prefix = (defined('CZR_IS_PRO' ) && CZR_IS_PRO) ? 'customizr-pro-' : 'customizr-';
          $theme_class = $prefix . $ver;
          $_classes[] = get_template_directory() === get_stylesheet_directory() ? $theme_class : $theme_class.'-with-child-theme';

          // Nov 2020 : opt-out for underline on links
          if ( !(bool)esc_attr( czr_fn_opt( 'tc_link_underline') ) ){
              $_classes = array_merge( $_classes , array( 'tc-link-not-underlined' ) );
          }
          return $_classes;
      }
  }//end of class
endif;

?>