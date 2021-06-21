<?php
/**
* Defines filters and actions used in several templates/classes
*
*/
if ( ! class_exists( 'CZR_utils' ) ) :
  class CZR_utils {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $inst;
      static $instance;
      public $default_options;
      public $db_options;
      public $options;//not used in customizer context only
      public $is_customizing;
      public $tc_options_prefixes;

      public static $_theme_setting_list;

      function __construct () {
        self::$inst =& $this;
        self::$instance =& $this;

        //Various WP filters for
        //content
        //thumbnails => parses image if smartload enabled
        //title
        add_action( 'wp_head'                 , array( $this , 'czr_fn_wp_filters') );

        //get all options
        add_filter( '__options'               , 'czr_fn_get_theme_options' , 10, 1 );
        //get single option
        add_filter( '__get_option'            , 'czr_fn_opt' , 10, 2 );//deprecated

        //some useful filters
        add_filter( '__ID'                    , 'czr_fn_get_id' );//deprecated
        add_filter( '__screen_layout'         , array( $this , 'czr_fn_get_layout' ) , 10 , 2 );//deprecated
        add_filter( '__is_home'               , 'czr_fn_is_real_home' );
        add_filter( '__is_home_empty'         , 'czr_fn_is_home_empty' );
        add_filter( '__post_type'             , 'czr_fn_get_post_type' );
        add_filter( '__is_no_results'         , 'czr_fn_is_no_results' );
        add_filter( '__article_selectors'     , array( $this , 'czr_fn_article_selectors' ) );

        //social networks
        add_filter( '__get_socials'           , 'czr_fn_get_social_networks', 10, 0 );
      }

      /**
      * hook : wp_head
      * @package Customizr
      * @since Customizr 3.3.0
      */
      function czr_fn_wp_filters() {
        add_filter( 'the_content'                         , array( $this , 'czr_fn_fancybox_content_filter' ) );
        if ( apply_filters( 'tc_enable_fancybox_in_wc_short_description', false  ) ) {
            add_filter( 'woocommerce_short_description'   , array( $this, 'czr_fn_fancybox_content_filter' ) );
        }
        /*
        * Smartload disabled for content retrieved via ajax
        */
        if ( apply_filters( 'tc_globally_enable_img_smart_load', ! czr_fn_is_ajax() && esc_attr( czr_fn_opt( 'tc_img_smart_load' ) ) ) ) {
            add_filter( 'the_content'                       , 'czr_fn_parse_imgs', PHP_INT_MAX );
            add_filter( 'tc_thumb_html'                     , 'czr_fn_parse_imgs' );
            if ( apply_filters( 'tc_enable_img_smart_load_in_wc_short_description', false  ) ) {
                add_filter( 'woocommerce_short_description' , 'czr_fn_parse_imgs' );
            }
        }
        add_filter( 'wp_title'                            , 'czr_fn_wp_filter_title' , 10, 2 );
      }




      /**
      * Returns the current skin's primary color
      *
      * @package Customizr
      * @since Customizr 3.1.23
      */
      function czr_fn_get_skin_color( $_what = null ) {
          $_color_map    = CZR___::$instance -> skin_classic_color_map;
          $_color_map    = ( is_array($_color_map) ) ? $_color_map : array();

          $_active_skin =  str_replace('.min.', '.', basename( CZR_init::$instance -> czr_fn_get_style_src() ) );
          //falls back to grey.css array( '#5A5A5A', '#343434' ) if not defined
          $_to_return = array( '#5A5A5A', '#343434' );

          switch ($_what) {
            case 'all':
              $_to_return = $_color_map;
            break;

            case 'pair':
              $_to_return = ( false != $_active_skin && array_key_exists( $_active_skin, $_color_map ) && is_array( $_color_map[$_active_skin] ) ) ? $_color_map[$_active_skin] : $_to_return;
            break;

            default:
              $_to_return = ( false != $_active_skin && isset($_color_map[$_active_skin][0]) ) ? $_color_map[$_active_skin][0] : $_to_return[0];
            break;
          }
          return apply_filters( 'tc_get_skin_color' , $_to_return , $_what );
      }




      /**
      * Returns an option from the options array of the theme.
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_opt( $option_name , $option_group = null, $use_default = true ) {
          return czr_fn_opt( $option_name , $option_group, $use_default );
      }

      //backward compatibility
      //used until FPU 2.0.33
      function czr_fn_parse_imgs( $_html ) {
        return czr_fn_parse_imgs( $_html );
      }


      /**
      * Returns the "real" queried post ID or if !isset, get_the_ID()
      * Checks some contextual booleans
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_id()  {
          return czr_fn_get_id();
      }




      /**
      * This function returns the layout (sidebar(s), or full width) to apply to a context
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      public static function czr_fn_get_layout( $post_id , $sidebar_or_class = 'class' ) {
          $__options                    = czr_fn__f ( '__options' );

          //Article wrapper class definition
          $global_layout                = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );

          /* DEFAULT LAYOUTS */
          //what is the default layout we want to apply? By default we apply the global default layout
          $tc_sidebar_default_layout    = esc_attr( $__options['tc_sidebar_global_layout'] );

          //checks if the 'force default layout' option is checked and return the default layout before any specific layout
          if( isset($__options['tc_sidebar_force_layout']) && 1 == $__options['tc_sidebar_force_layout'] ) {
            $class_tab  = $global_layout[$tc_sidebar_default_layout];
            $class_tab  = $class_tab['content'];
            $tc_screen_layout = array(
              'sidebar' => $tc_sidebar_default_layout,
              'class'   => $class_tab
            );
            return $tc_screen_layout[$sidebar_or_class];
          }

          global $wp_query, $post;
          $tc_specific_post_layout    = false;
          $is_singular_layout         = false;

          if ( apply_filters( 'tc_is_post_layout', is_single( $post_id ), $post_id ) || czr_fn_is_attachment_image() ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_post_layout'] );
            $is_singular_layout = true;
          } elseif ( apply_filters( 'tc_is_page_layout', is_page( $post_id ), $post_id ) ) {
            $tc_sidebar_default_layout  = esc_attr( $__options['tc_sidebar_page_layout'] );
            $is_singular_layout = true;
          }


          //builds the default layout option array including layout and article class
          $class_tab  = $global_layout[$tc_sidebar_default_layout];
          $class_tab  = $class_tab['content'];
          $tc_screen_layout             = array(
                      'sidebar' => $tc_sidebar_default_layout,
                      'class'   => $class_tab
          );

          //The following lines set the post specific layout if any, and if not keeps the default layout previously defined
          $tc_specific_post_layout    = false;

          //if we are displaying an attachement, we use the parent post/page layout by default
          //=> but if the attachment has a layout, it will win.
          if ( isset($post) && is_singular() && 'attachment' == $post->post_type ) {
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );
            if ( ! $tc_specific_post_layout ) {
                $tc_specific_post_layout  = esc_attr( get_post_meta( $post->post_parent , $key = 'layout_key' , $single = true ) );
            }
          }
          //for a singular post or page OR for the posts page
          elseif ( $is_singular_layout || is_singular() || czr_fn_is_attachment_image() || $wp_query -> is_posts_page )
            $tc_specific_post_layout  = esc_attr( get_post_meta( $post_id, $key = 'layout_key' , $single = true ) );


          //checks if we display home page, either posts or static page and apply the customizer option
          if( ( is_home() && 'posts' == get_option( 'show_on_front' ) ) || is_front_page() ) {
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

          $replacement = '<a$1href="$2" class="grouped_elements" rel="tc-fancybox-group'.$post -> ID.'"$3>';

          $r_content = preg_replace( $pattern, $replacement, $content);
          $content = $r_content ? $r_content : $content;
          return apply_filters( 'tc_fancybox_content_filter', $content );
      }





      /**
      * Returns the classes for the post div.
      *
      * @param string|array $class One or more classes to add to the class list.
      * @param int $post_id An optional post ID.
      * @package Customizr
      * @since 3.0.10
      */
      function czr_fn_get_post_class( $class = '', $post_id = null ) {
        //Separates classes with a single space, collates classes for post DIV
        return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
      }





      /**
      * Displays the selectors of the article depending on the context
      *
      * @package Customizr
      * @since 3.1.0
      */
      function czr_fn_article_selectors() {

        //gets global vars
        global $post;
        global $wp_query;

        //declares selector var
        $selectors                  = '';

        // SINGLE POST
        $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
        $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // POST LIST
        $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !czr_fn__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
        $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class( 'row-fluid grid-item' ) ) : $selectors;

        // PAGE
        $page_selector_bool         = isset($post) && 'page' == czr_fn__f('__post_type') && is_singular() && !czr_fn__f( '__is_home_empty');
        $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '.$this -> czr_fn_get_post_class('row-fluid') ) : $selectors;

        // ATTACHMENT
        //checks if attachement is image and add a selector
        $format_image               = wp_attachment_is_image() ? 'format-image' : '';
        $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '.$this -> czr_fn_get_post_class(array('row-fluid', $format_image) ) ) : $selectors;

        // NO SEARCH RESULTS
        $selectors                  = ( is_search() && 0 == $wp_query -> post_count ) ? apply_filters( 'tc_no_results_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        // 404
        $selectors                  = is_404() ? apply_filters( 'tc_404_selectors' , 'id="post-0" class="post error404 no-results not-found row-fluid"' ) : $selectors;

        echo apply_filters( 'tc_article_selectors', $selectors );

      }//end of function



    /**
    * Returns a boolean
    * check if user started to use the theme before ( strictly < ) the requested version
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_user_started_before_version( $_czr_ver, $_pro_ver = null ) {
      return czr_fn_user_started_before_version( $_czr_ver, $_pro_ver );
    }

  }//end of class
endif;

?>