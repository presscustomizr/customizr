<?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
*
*/

if ( ! class_exists( 'CZR_init' ) ) :
  class CZR_init {
      //declares the filtered default settings
      public $global_layout;
      public $font_selectors;
      public $footer_widgets;
      public $footer_horizontal_widgets;

      public $css_container_widths;
      public $css_mq_breakpoints;


      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
          self::$instance =& $this;

          //html5
          //tag cloud - same font size
          //tag cloud add button classes
          add_action( 'after_setup_theme'       , array( $this , 'czr_fn_base_customizr_setup' ));

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter( 'body_class'              , array( $this , 'czr_fn_set_body_classes') );
          //Add the context
          add_filter ( 'body_class'             , 'czr_fn_set_post_list_context_class' );

          //Add custom search form
          add_filter( 'get_search_form'         , array( $this, 'czr_fn_search_form' ), 0 );

          add_action( 'template_redirect'       , array( $this, 'czr_fn_ajax_response' ) );

          $right_sidebar_text_alignment = is_rtl() ? 'text-md-left' : 'text-md-right';
          //Default layout settings
          $this -> global_layout      = array(
              'r' => array(
                  'content'       => 'col-12 col-md-9',
                  'l-sidebar'     => false,
                  'r-sidebar'     => 'col-12 col-md-3 ' . $right_sidebar_text_alignment,
                  'customizer'    => __( 'Right sidebar' , 'customizr' ),
                  'metabox'       => __( 'Right sidebar' , 'customizr' ),
              ),
              'l' => array(
                  'content'       => 'col-12 col-md-9',
                  'l-sidebar'     => 'col-12 col-md-3 order-md-first',
                  'r-sidebar'     => false,
                  'customizer'    => __( 'Left sidebar' , 'customizr' ),
                  'metabox'       => __( 'Left sidebar' , 'customizr' ),
              ),
              'b' => array(
                  'content'       => 'col-12 col-md-6',
                  'l-sidebar'     => 'col-12 col-md-3 order-md-first',
                  'r-sidebar'     => 'col-12 col-md-3 ' . $right_sidebar_text_alignment,
                  'customizer'    => __( '2 sidebars : Right and Left' , 'customizr' ),
                  'metabox'       => __( '2 sidebars : Right and Left' , 'customizr' ),
              ),
              'f' => array(
                  'content'       => 'col-12',
                  'l-sidebar'     => false,
                  'r-sidebar'     => false,
                  'customizer'    => __( 'No sidebars : full width layout', 'customizr' ),
                  'metabox'       => __( 'No sidebars : full width layout' , 'customizr' ),
              ),
          );



          //CSS variable definition (as for bootstrap)
          $this -> css_container_widths = apply_filters( 'czr_css_container_widths',             array(
                  'xl' => '1140',
                  'lg' => '960',
                  'md' => '720',
                  'sm' => '540'
          ));

          $this -> css_mq_breakpoints = apply_filters( 'czr_css_mq_breakpoints', array(
                  'xl' => '1200',
                  'lg' => '992',
                  'md' => '768',
                  'sm' => '576'
          ));


          $this -> font_selectors     = array(
              'titles' => implode(',' , apply_filters( 'czr-titles-font-selectors' , array('.navbar-brand' , '.header-tagline', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
              'body'   => implode(',' , apply_filters( 'czr-body-font-selectors' , array('body') ) )
          );


          //Default footer widgets
          $this -> footer_widgets     = array(
              'footer_one'    => array(
                              'name'                 => __( 'Footer Widget Area One' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' ),
                              'before_title'            => '<h5 class="widget-title">',
                              'after_title'             => '</h5>'
              ),
              'footer_two'    => array(
                              'name'                 => __( 'Footer Widget Area Two' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' ),
                              'before_title'            => '<h5 class="widget-title">',
                              'after_title'             => '</h5>'
              ),
              'footer_three'   => array(
                              'name'                 => __( 'Footer Widget Area Three' , 'customizr' ),
                              'description'          => __( 'Just use it as you want !' , 'customizr' ),
                              'before_title'            => '<h5 class="widget-title">',
                              'after_title'             => '</h5>'
              )
          );//end of array

          //Default horizontal footer widget area
          $this -> footer_horizontal_widgets     = array(
              'footer_horizontal'    => array(
                              'name'                 => __( 'Footer Horizontal Widget Area' , 'customizr' ),
                              'description'          => __( 'Appears before the site footer' , 'customizr' ),
                              'before_title'            => '<h5 class="widget-title">',
                              'after_title'             => '</h5>'
              ),
          );//end of array
      }//end of constructor







      /**
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_body_classes( $_classes ) {

          $_classes = is_array( $_classes ) ? $_classes : array();

          $_classes[] = 0 != esc_attr( czr_fn_opt( 'tc_link_hover_effect' ) ) ? 'czr-link-hover-underline' : 'czr-link-hover-underline-off';

          if ( czr_fn_is_customizing() )
            $_classes[] = 'is-customizing';
          if ( wp_is_mobile() )
            $_classes[] = 'czr-is-mobile';
          if ( 0 != esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ) )
            $_classes[] = esc_attr( czr_fn_opt( 'tc_dropcap_design' ) );


          //header and footer skins
          $header_skin_opt = esc_attr( czr_fn_get_header_skin() );
          $footer_skin_opt = esc_attr( czr_fn_opt( 'tc_footer_skin' ) );

          //set header and footer skin with default fallback
          $_classes[] = sprintf( 'header-skin-%1$s', $header_skin_opt ? $header_skin_opt : 'light' );
          $_classes[] = sprintf( 'footer-skin-%1$s', $footer_skin_opt ? $footer_skin_opt : 'dark' );

          //adds the layout
          $_layout = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
          if ( in_array( $_layout, array('b', 'l', 'r', 'f') ) ) {
            $_classes[] = sprintf( 'czr-%s-sidebar',
              'f' == $_layout ? 'no' : $_layout
            );
          }
          //IMAGE CENTERED
          if ( (bool) esc_attr( czr_fn_opt( 'tc_center_img') ) ) {
            $_classes[] = 'tc-center-images';
          }

          //BOXED LAYOUT
          $_classes[] = ( 'boxed' == esc_attr( czr_fn_opt( 'tc_site_layout') ) ) ? 'czr-boxed-layout' : 'czr-full-layout';

          //SIDENAV POSITIONING
          if ( czr_fn_is_possible('sidenav') ) {

            $header_layouts  = esc_attr( czr_fn_opt( 'tc_header_layout' ) );
            $direction_class = strstr( $header_layouts, is_rtl() ? 'left' : 'right' ) ? 'sn-left' : 'sn-right';

            $_classes[] = apply_filters( 'tc_sidenav_body_class', $direction_class );

          }

          return $_classes;
      }



      function czr_fn_base_customizr_setup() {
          add_theme_support( 'html5', array( 'comment-form', 'caption' ) );
          // gutenberg alignwide/full cover images.
          add_theme_support( 'align-wide' );

          //tag cloud - same font size
          add_filter( 'widget_tag_cloud_args'               , array( $this, 'czr_fn_add_widget_tag_cloud_args' ));
          //tag cloud add button classes
          add_filter( 'wp_generate_tag_cloud_data'          , array( $this, 'czr_fn_add_tag_cloud_button_classes') );
      }




      /**
      * Add tag cloud widget args so to have all tags with the same font size.
      *
      * @since Customizr 4.0
      *
      * @param array $args arguments for tag cloud widget.
      * @return array modified arguments.
      */
      function czr_fn_add_widget_tag_cloud_args( $args ) {
          if ( is_array( $args ) ) {
            $args['largest'] = 1;
            $args['smallest'] = 1;
            $args['unit'] = 'em';
          }

          return $args;
      }

      /**
      * Add button classes tp tag cloud links
      *
      *
      * @since Customizr 4.0
      *
      * @param array $args arguments for tag cloud widget.
      * @return array modified arguments.
      */
      function czr_fn_add_tag_cloud_button_classes( $tags_data ) {
          if ( is_array( $tags_data ) ) {
            foreach ( $tags_data as &$tag_data ) {
              if ( is_array( $tag_data ) ) {
                $tag_data['class'] = array_key_exists( 'class', $tag_data ) ? "{$tag_data['class']} btn btn-skin-dark-oh inverted" : "btn btn-skin-dark-oh inverted";
              }
            }
          }
          return $tags_data;
      }




      /*
      * hook : get_search_form
      *
      * @since Customizr 4.0
      * @return string custom search form html
      */
      function czr_fn_search_form() {
          ob_start();
           czr_fn_render_template( 'modules/search/searchform' );
          $form = ob_get_clean();

          return $form;
      }

      //@return void()
      //hook : template_redirect
      function czr_fn_ajax_response() {
          //check
          if ( ! czr_fn_is_ajax() )
              return false;

          //do nothing if not doing a specific huajax call
          if ( ! ( isset( $_GET[ 'czrajax' ] ) && $_GET[ 'czrajax' ] ) )
              return false;

          // Require an action parameter
          if ( ! isset( $_REQUEST['action'] ) || empty( $_REQUEST['action'] ) )
              die( '0' );

          // Will be used by hu_is_ajax();
          if ( ! defined( 'DOING_AJAX' ) )
              define( 'DOING_AJAX', true );

          //Nonce is not needed as long as we don't write in the db
          //Furthermore, when a cache plugin is used, front nonces can not be used.
          //@see https://github.com/presscustomizr/hueman/issues/512
          //'frontNonce'   => array( 'id' => 'CZRFrontNonce', 'handle' => wp_create_nonce( 'czr-front-nonce' ) )
          if ( isset( $_REQUEST['withNonce'] ) && true == $_REQUEST['withNonce'] )
            check_ajax_referer( 'czr-front-nonce', 'CZRFrontNonce' );

          @header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
          send_nosniff_header();

          $action = $_REQUEST['action'];//we know it is set at this point
          do_action( "czr_ajax_{$action}"  );
          die( '0' );
      }


  }//end of class
endif;