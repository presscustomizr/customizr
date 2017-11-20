<?php
if ( ! class_exists( 'CZR_prevdem' ) ) :
  final class CZR_prevdem {
    function __construct() {
      //SKIN
      add_filter( 'tc_opt_tc_skin_color' , array( $this, 'czr_fn_set_skin_color' ) );

      //FONT
      add_filter( 'tc_opt_tc_fonts', array( $this, 'czr_fn_set_font') );

      //HEADER
      //add_filter( 'option_blogname', array( $this, 'czr_fn_set_blogname'), 100 );
      add_filter( 'tc_social_in_header' , array( $this, 'czr_fn_set_header_socials' ) );
      add_filter( 'tc_tagline_display' , array( $this, 'czr_fn_set_header_tagline' ) );
      add_filter( 'tc_opt_tc_menu_style' , array( $this, 'czr_fn_set_header_menu_style' ) );
      add_filter( 'tc_opt_tc_menu_position' , array( $this, 'czr_fn_set_header_primay_navbar_menu_position' ) );

      //FRONT PAGE
      add_filter( 'option_show_on_front', array( $this, 'czr_fn_set_front_page_content' ), 99999 );
      add_filter( 'pre_option_show_on_front', array( $this, 'czr_fn_set_front_page_content' ), 99999 );

      //FEATURED PAGES
      add_filter( 'tc_opt_tc_show_featured_pages', '__return_true' );
      add_filter( 'fp_img_src', array( $this, 'czr_fn_set_fp_img_src'), 100 );
      add_filter( 'czr_fp_title', array( $this, 'czr_fn_set_fp_title'), 100, 3 );
      add_filter( 'czr_fp_text', array( $this, 'czr_fn_set_fp_text'), 100 );
      add_filter( 'czr_fp_link_url', array( $this, 'czr_fn_set_fp_link'), 100 );

      //THUMBNAILS
      add_filter( 'tc_has_thumb', '__return_true');
      add_filter( 'tc_has_thumb_info', '__return_true');
      add_filter( 'tc_has_wp_thumb_image', '__return_true');
      add_filter( 'czr_thumb_html', array( $this, 'czr_fn_filter_thumb_src'), 10, 6 );

      //SLIDER
      add_filter( 'tc_opt_tc_front_slider', array( $this, 'czr_fn_set_slider') );
      add_filter( 'tc_default_slides', array( $this, 'czr_fn_set_default_slides') );
      //adds infos in the caption data of the demo slider
      add_filter( 'czr_slide_caption_data' , array( $this, 'czr_fn_set_demo_slide_data'), 100, 3 );
      add_filter( 'tc_opt_tc_slider_delay', array( $this, 'czr_fn_set_demo_slider_delay') );
      add_filter( 'tc_opt_tc_slider_img_smart_load', '__return_false' );

      //SINGLE POSTS AND PAGES
      add_filter( 'tc_show_single_post_thumbnail', '__return_true');
      add_filter( 'tc_show_single_page_thumbnail', '__return_true');
      add_filter( 'tc_single_post_thumb_hook', array( $this, 'czr_fn_set_singular_thumb_hook') );
      add_filter( 'tc_single_page_thumb_hook', array( $this, 'czr_fn_set_singular_thumb_hook') );
      add_filter( 'tc_single_post_thumb_height', array( $this, 'czr_fn_set_singular_thumb_height') );
      add_filter( 'tc_single_page_thumb_height', array( $this, 'czr_fn_set_singular_thumb_height') );
      add_filter( 'tc_opt_tc_single_post_thumb_location', array( $this, 'czr_fn_display_single_post_thumbnail') );
      //block locations
      add_filter( 'tc_opt_tc_single_author_block_location', array( $this, 'czr_fn_set_single_block_location') );
      add_filter( 'tc_opt_tc_single_related_posts_block_location', array( $this, 'czr_fn_set_single_block_location') );
      add_filter( 'tc_opt_tc_singular_comments_block_location', array( $this, 'czr_fn_set_single_block_location') );

      //SOCIALS
      add_filter( 'option_tc_theme_options', array( $this, 'czr_fn_set_socials'), 100 );

      //WIDGETS
      add_action( 'dynamic_sidebar_before', array( $this, 'czr_fn_set_widgets'), 10, 2 );
      add_filter( 'tc_has_footer_widgets', '__return_true');
      //add_filter( 'tc_has_footer_widgets_zone', '__return_true');
      add_filter( 'tc_has_sidebar_widgets', '__return_true');
    }//construct



    /* ------------------------------------------------------------------------- *
     *  Skin
    /* ------------------------------------------------------------------------- */
    //hook : tc_opt_tc_skin_color
    function czr_fn_set_skin_color( $skin ) {
      return '#5a5a5a';
    }


    /* ------------------------------------------------------------------------- *
     *  Font
    /* ------------------------------------------------------------------------- */
    //hook : tc_opt_tc_fonts
    function czr_fn_set_font() {
      return '_g_poppins';
    }


    /* ------------------------------------------------------------------------- *
     *  Header
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_header_socials() {
      return '';
    }

    function czr_fn_set_header_tagline() {
      return '';
    }

    function czr_fn_set_header_menu_style() {
      return 'regular';
    }

    function czr_fn_set_header_primay_navbar_menu_position() {
      return 'pull-menu-right';
    }


    function czr_fn_set_blogname() {
        return 'Customizr';
    }


    /* ------------------------------------------------------------------------- *
     *  Front page : WP Core
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_front_page_content( $value ) {
        if ( czr_fn_is_customizing() )
          return $value;
        return 'posts';
    }


    /* ------------------------------------------------------------------------- *
     *  Featured Pages
    /* ------------------------------------------------------------------------- */
    //hook : fp_img_src
    function czr_fn_set_fp_img_src($fp_img) {
      return czr_fn_get_thumbnail_model( array( 'requested_size' => 'tc-thumb' ) );
    }

    function czr_fn_set_fp_title( $text, $fp_single_id, $featured_page_id ) {
      switch ($fp_single_id) {
        case 'one':
          $text = __( 'Who We Are', 'customizr');
          break;

        case 'two':
          $text = __( 'What We Do', 'customizr');
          break;

        case 'three':
          $text = __( 'Contact Us', 'customizr');
          break;
      }
      return $text;
    }

    function czr_fn_set_fp_text() {
      return '';
    }

    function czr_fn_set_fp_link() {
      return 'javascript:void(0)';
    }

    /* ------------------------------------------------------------------------- *
     *  Thumbnails
    /* ------------------------------------------------------------------------- */
    //@param img :array (url, width, height, is_intermediate), or false, if no image is available.
    function czr_fn_filter_thumb_src( $tc_thumb, $requested_size, $_post_id, $_custom_thumb_id, $_img_attr, $tc_thumb_size ) {
      if ( ! empty($tc_thumb) )
        return $tc_thumb;

      $new_img_src = $this -> czr_fn_get_prevdem_img_src( $tc_thumb_size );
      if ( ! is_string($new_img_src) || empty($new_img_src) )
        return $tc_thumb;

      $_img_attr = is_array($_img_attr) ? $_img_attr : array();
      if ( false == $tc_thumb || empty( $tc_thumb ) ) {
        $tc_thumb = sprintf( '<img src="%1$s" class="%2$s">',
          $new_img_src,
          isset($_img_attr['class']) ? $_img_attr['class'] : ''
        );
      } else {
        $regex = '#<img([^>]*) src="([^"/]*/?[^".]*\.[^"]*)"([^>]*)>#';
        $replace = "<img$1 src='$new_img_src'$3>";
        $tc_thumb = preg_replace($regex, $replace, $tc_thumb);
      }
      return $tc_thumb;
    }



    /* Placeholder thumb helper
    *  @return a random img src string
    *  Can be recursive if a specific img size is not found
    */
    function czr_fn_get_prevdem_img_src( $_size = 'tc-grid', $img_id = null, $i = 0 ) {
        //prevent infinite loop
        if ( 10 == $i ) {
          return;
        }
        $sizes_suffix_map = array(
            'tc-thumb'     => '270x250',
            'tc-grid-full'    => '1170x350',
            'tc-grid'  => '570x350',
            'slider' => '1170x500'
        );
        $requested_size = isset( $sizes_suffix_map[$_size] ) ? $sizes_suffix_map[$_size] : '570x350';
        $path = TC_BASE . '/assets/front/img/demo/';

        //Build or re-build the global dem img array
        if ( ! isset( $GLOBALS['prevdem_img'] ) || empty( $GLOBALS['prevdem_img'] ) ) {
            $imgs = array();
            if ( is_dir( $path ) ) {
              $imgs = scandir( $path );
            }
            $candidates = array();
            if ( ! $imgs || empty( $imgs ) )
              return array();

            foreach ( $imgs as $img ) {
              if ( '.' === $img[0] || is_dir( $path . $img ) ) {
                continue;
              }
              $candidates[] = $img;
            }
            $GLOBALS['prevdem_img'] = $candidates;
        }

        $candidates = $GLOBALS['prevdem_img'];

        //get a random image name if no specific image id requested
        $img_prefix = '';
        if ( is_null($img_id) ) {
            $rand_key = array_rand($candidates);
            $img_name = $candidates[ $rand_key ];
            //extract img prefix
            $img_prefix_expl = explode( '-', $img_name );
            $img_prefix = $img_prefix_expl[0];
        } else {
            $img_prefix = $img_id;
        }

        $requested_size_img_name = "{$img_prefix}-{$requested_size}.jpg";
        //if file does not exists, reset the global and recursively call it again
        if ( ! file_exists( $path . $requested_size_img_name ) ) {
          unset( $GLOBALS['prevdem_img'] );
          $i++;
          return $this -> czr_fn_get_prevdem_img_src( $_size, null, $i );
        }
        //unset all sizes of the img found and update the global
        $new_candidates = $candidates;
        foreach ( $candidates as $_key => $_img ) {
          if ( substr( $_img , 0, strlen( "{$img_prefix}-" ) ) == "{$img_prefix}-" ) {
            unset( $new_candidates[$_key] );
          }
        }
        $GLOBALS['prevdem_img'] = $new_candidates;
        return get_template_directory_uri() . '/assets/front/img/demo/' . $requested_size_img_name;
    }




    /* ------------------------------------------------------------------------- *
     *  Slider
    /* ------------------------------------------------------------------------- */
    //hook : tc_opt_tc_front_slider
    function czr_fn_set_slider() {
      return 'demo';
    }

    //hook : tc_default_slides
    //@return array of default slides
    function czr_fn_set_default_slides() {
        $defaults = array(
            'title'         =>  '',
            'text'          =>  '',
            'button_text'   =>  '',
            'link_id'       =>  null,
            'link_url'      =>  null,
            'active'        =>  '',
            'color_style'   =>  '',
            'slide_background' =>  ''
        );
        $slides = array(
            1 => array(
              'active'        =>  'active',
              'slide_background'  =>  sprintf( '<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        TC_BASE_URL.'assets/front/img/customizr-theme.jpg',
                                        __( 'Customizr is a clean responsive theme' , 'customizr' )
                                  )
            ),
            2 => array(
              'slide_background' => sprintf( '<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                    $this -> czr_fn_get_prevdem_img_src( 'slider', '4' ),
                                    __( 'Customizr is a clean responsive theme' , 'customizr' )
                                )
            ),
            3 => array(
              'slide_background' => sprintf( '<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                        $this -> czr_fn_get_prevdem_img_src( 'slider', '16' ),
                                        __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                                )
            )
        );
        $new_slides = array();
        foreach ($slides as $key => $value) {
          $new_slides[$key] = wp_parse_args( $value, $defaults );
        }
        return $new_slides;
    }

    //hook : tc_slide_caption_data
    function czr_fn_set_demo_slide_data( $data, $slider_name_id, $id ) {
        // if ( 'demo' != $slider_name_id || ! is_user_logged_in() )
        //   return $data;

        switch ( $id ) {
          case 1 :
            $data['title']        = '';
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = '';//__( 'Call to action' , 'customizr');
          break;

          case 2 :
            $data['title']        = __( 'The Customizr theme fits nicely on any mobile devices.', 'customizr' );
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = '';//__( 'Call to action' , 'customizr');
          break;

          case 3 :
            $data['title']        = __( 'Engage your visitors with a carousel in any pages.', 'customizr' );
            $data['link_url']     = 'javascript:void(0)';
            $data['button_text']  = __( 'Call to action' , 'customizr');
          break;
        };

        $data['link_target'] = '_blank';
        return $data;
    }


    function czr_fn_set_demo_slider_delay() {
      return 6000;
    }


    /* ------------------------------------------------------------------------- *
     *  Single Posts
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_singular_thumb_hook() {
      return '__before_main_wrapper';
    }
    function czr_fn_set_singular_thumb_height() {
      return 350;
    }
    function czr_fn_set_single_block_location() {
      return 'below_post_content';
    }
    //hook : tc_opt_tc_single_post_thumb_location
    function czr_fn_display_single_post_thumbnail() {
      return  '__before_main_wrapper|200';
    }


    /* ------------------------------------------------------------------------- *
     *  Socials
    /* ------------------------------------------------------------------------- */
    function czr_fn_set_socials( $options ) {
      if ( czr_fn_is_customize_left_panel() )
        return $options;

      $to_display = array( 'tc_facebook', 'tc_twitter', 'tc_linkedin', 'tc_google');
      foreach ($to_display as $social) {
         $options[$social] = 'javascript:void()';
      }
      $options['tc_rss'] = '';
      return $options;
    }


    /* ------------------------------------------------------------------------- *
     *  Widgets
    /* ------------------------------------------------------------------------- */
    //hook : 'dynamic_sidebar_before'
    // @param int|string $index       Index, name, or ID of the dynamic sidebar.
    // @param bool       $has_widgets Whether the sidebar is populated with widgets.
    //                                Default true.
    function czr_fn_set_widgets( $index, $bool ) {
      if ( true === $bool )
        return;

      //we only want to print default widgets in primary and secondary sidebars
      if ( ! in_array( $index, array( 'left', 'right', 'footer_one', 'footer_two', 'footer_three') ) )
        return;

      $default_args = apply_filters( 'tc_default_widget_args' ,
          array(
            'name'                    => '',
            'id'                      => '',
            'description'             => '',
            'class'                   => '',
            //'before_widget'           => '<aside id="%1$s" class="widget %2$s">',
            //'after_widget'            => '</aside>',
            'before_title'            => '<h3 class="widget-title">',
            'after_title'             => '</h3>'
          )
      );

      $_widgets_to_print = array();
      switch ($index) {
        case 'left':
        case 'right':
          $_widgets_to_print[] = array(
            'WP_Widget_Search' => array(
              'instance' => array(
                'title' => __( 'Search', 'customizr')
              ),
              'args' => $default_args
            ),
            'WP_Widget_Recent_Posts' => array(
              'instance' => array(
                'title' => __( 'Recent Posts', 'customizr'),
                'number' => 6
              ),
              'args' => $default_args
            ),
            'WP_Widget_Recent_Comments' => array(
              'instance' => array(
                'title' => __( 'Recent Comments', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_one':
          $_widgets_to_print[] = array(
            'WP_Widget_Recent_Posts' => array(
              'instance' => array(
                'title' => __( 'Recent Posts', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_two':
          $_widgets_to_print[] = array(
            'WP_Widget_Recent_Comments' => array(
              'instance' => array(
                'title' => __( 'Recent Comments', 'customizr'),
                'number' => 4
              ),
              'args' => $default_args
            )
          );
        break;
        case 'footer_three':
          $_widgets_to_print[] = array(
            'WP_Widget_Search' => array(
              'instance' => array(
                'title' => __( 'Search', 'customizr')
              ),
              'args' => $default_args
            )
          );
        break;
      }
      if ( empty($_widgets_to_print) )
        return;

      //find the widget instance ids
      $_wgt_instances = array();

      foreach ( $_widgets_to_print as $_wgt ) {
        foreach (  $_wgt as $_class => $params ) {
            if ( class_exists( $_class) ) {
              $_instance = isset( $params['instance'] ) ? $params['instance'] : array();
              $_args = isset( $params['args'] ) ? $params['args'] : array();
              the_widget( $_class, $_instance, $_args );
            }
        }
      }



    }

  }//end of class
endif;

?>