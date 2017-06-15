<?php
/**
* Declares Customizr default settings
* Adds theme supports using WP functions
* Adds plugins compatibilities
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

if ( ! class_exists( 'CZR_init' ) ) :
  class CZR_init {
      //declares the filtered default settings
      public $global_layout;
      public $font_selectors;
      public $footer_widgets;
      public $widgets;

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
          self::$instance =& $this;

          //modify the query with pre_get_posts
          //! wp_loaded is fired after WordPress is fully loaded but before the query is set
          add_action( 'wp_loaded'                              , array( $this, 'czr_fn_set_early_hooks') );

          //add classes to body tag : fade effect on link hover, is_customizing. Since v3.2.0
          add_filter('body_class'                              , array( $this , 'czr_fn_set_body_classes') );
          //Add the context
          add_filter ( 'body_class'                            , 'czr_fn_set_post_list_context_class' );

          //may be filter the thumbnail inline style
          add_filter( 'czr_post_thumb_inline_style'            , array( $this , 'czr_fn_change_thumb_inline_css' ), 10, 3 );

          //Add custom search form
          add_filter( 'get_search_form'                        , array( $this, 'czr_fn_search_form' ), 0 );

          //Default layout settings
          $this -> global_layout      = array(
                                        'r' => array(
                                            'content'       => 'col-12 col-md-9',
                                            'l-sidebar'     => false,
                                            'r-sidebar'     => 'col-12 col-md-3',
                                            'customizer'    => __( 'Right sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Right sidebar' , 'customizr' ),
                                        ),
                                        'l' => array(
                                            'content'       => 'col-12 col-md-9 push-md-3',
                                            'l-sidebar'     => 'col-12 col-md-3 pull-md-9',
                                            'r-sidebar'     => false,
                                            'customizer'    => __( 'Left sidebar' , 'customizr' ),
                                            'metabox'       => __( 'Left sidebar' , 'customizr' ),
                                        ),
                                        'b' => array(
                                            'content'       => 'col-12 col-md-6 push-md-3',
                                            'l-sidebar'     => 'col-12 col-md-3 pull-md-6',
                                            'r-sidebar'     => 'col-12 col-md-3',
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

          $this -> font_selectors     = array(
            'titles' => implode(',' , apply_filters( 'czr-titles-font-selectors' , array('.navbar-brand' , '.navbar-brand-tagline', 'h1', 'h2', 'h3', '.tc-dropcap' ) ) ),
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
      }//end of constructor



      /**
      * //Move in CZR_utils?
      *
      * Returns the min or normal version of the passed css filename (basename.type)
      * depending on whether or not the minified version should be used
      *
      * @param $_sheet string
      *
      * @return string
      *
      * @package Customizr
      * @since Customizr 3.4.19
      */
      function czr_fn_maybe_use_min_style( $_sheet ) {
          if ( esc_attr( czr_fn_opt( 'tc_minified_skin' ) ) )
            $_sheet = ( defined('CZR_NOT_MINIFIED_CSS') && true === CZR_NOT_MINIFIED_CSS ) ? $_sheet : str_replace('.css', '.min.css', $_sheet);
          return $_sheet;
      }




      /**
      * Adds various classes on the body element.
      * hook body_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_body_classes( $_classes ) {

          $_classes = is_array( $_classes ) ? $_classes : array();

          if ( 0 != esc_attr( czr_fn_opt( 'tc_link_hover_effect' ) ) )
            $_classes[] = 'czr-fade-hover-links';
          if ( czr_fn_is_customizing() )
            $_classes[] = 'is-customizing';
          if ( wp_is_mobile() )
            $_classes[] = 'czr-is-mobile';
          if ( 0 != esc_attr( czr_fn_opt( 'tc_enable_dropcap' ) ) )
            $_classes[] = esc_attr( czr_fn_opt( 'tc_dropcap_design' ) );


          //header and footer skins
          $_classes[] = 'header-skin-' . ( esc_attr( czr_fn_opt( 'tc_header_skin' ) ) );
          $_classes[] = 'footer-skin-' . ( esc_attr( czr_fn_opt( 'tc_footer_skin' ) ) );

          //adds the layout
          $_layout = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
          if ( in_array( $_layout, array('b', 'l', 'r', 'f') ) ) {
            $_classes[] = sprintf( 'czr-%s-sidebar',
              'f' == $_layout ? 'no' : $_layout
            );
          }
          //IMAGE CENTERED
          if ( (bool) esc_attr( czr_fn_opt( 'tc_center_img') ) ){
            $_classes[] = 'tc-center-images';
          }

          //SKIN CLASS
          // $_skin = sprintf( 'skin-%s' , basename( $this->czr_fn_get_style_src() ) );
          // $_classes[] = substr( $_skin , 0 , strpos($_skin, '.') );

          //SIDENAV POSITIONING
          if ( czr_fn_is_possible('sidenav') ) {

            $header_layouts = esc_attr( czr_fn_opt( 'tc_header_layout' ) );

            $_classes[] = apply_filters( 'tc_sidenav_body_class', strstr( $header_layouts, 'right' ) ? 'sn-left' : 'sn-right' );

          }

          return $_classes;
      }




      /**
      * hook czr_post_thumb_inline_style
      * Replace default widht:auto by width:100%
      * @param array of args passed by apply_filters_ref_array method
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_change_thumb_inline_css( $_style, $image, $_filtered_thumb_size) {
          //conditions :
          //note : handled with javascript if tc_center_img option enabled
          $_bool = array_product(
            array(
              ! esc_attr( czr_fn_opt( 'tc_center_img') ),
              false != $image,
              ! empty($image),
              isset($_filtered_thumb_size['width']),
              isset($_filtered_thumb_size['height'])
            )
          );
          if ( ! $_bool )
            return $_style;

          $_width     = $_filtered_thumb_size['width'];
          $_height    = $_filtered_thumb_size['height'];
          $_new_style = '';
          //if we have a width and a height and at least on dimension is < to default thumb
          if ( ! empty($image[1])
            && ! empty($image[2])
            && ( $image[1] < $_width || $image[2] < $_height )
            ) {
              $_new_style           = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
          }
          if ( empty($image[1]) || empty($image[2]) )
            $_new_style             = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
          return $_new_style;
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
              $tag_data['class'] = array_key_exists( 'class', $tag_data ) ? "{$tag_data['class']} btn btn-skin-darkest-oh inverted" : "btn btn-dark inverted";
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


  }//end of class
endif;
