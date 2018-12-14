<?php
class CZR_slider_of_posts_model_class extends CZR_slider_model_class {
  public $slider_type = 'slider_of_posts';

  /**
  * @override
  * Helper
  * Return an array of the slide models from option or default
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  protected function czr_fn_get_the_slides( $slider_name_id, $img_size = 'slider-full' ) {
    return apply_filters( 'czr_the_slides', $this -> czr_fn_get_the_posts_slides( $slider_name_id, $img_size ) );
  }

  /**
  * helper
  * returns the slider edit text
  * @return  number
  *
  */
  function czr_fn_get_slider_edit_link_text() {
    return __( 'Customize or remove the posts slider', 'customizr' );
  }

  /**
  * Helper
  * Return an array of the post slide models
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  /* Steps;
  * 1) get the pre slides model
  * 2) get the actual model from the pre_model
  *
  *
  *
  * The difference between the pre_model and the actual model is because in the
  * transient we do not store the whole slide model, some info are not needed.
  * Also the actual model can be filtered, allowing user's filter and to translate it
  * mostly with qtranslate (polylang will force us, most likely if I don't find any
  * other suitable solution, to not use the transient).
  */
  private function czr_fn_get_the_posts_slides( $slider_name_id, $img_size ) {

    $pre_slides      = $this -> czr_fn_get_pre_posts_slides( array( 'img_size' => $img_size ) );

    //filter the pre_model
    $pre_slides      = apply_filters( 'czr_posts_slider_pre_model', $pre_slides, $this );

    //if the slider no longer exists or exists but is empty, return false
    if ( ! $this -> czr_fn_slider_exists( $pre_slides ) )
      return false;

    //extract pre_slides model
    $common          = isset( $pre_slides['common'] ) ? $pre_slides['common'] : array();
    $posts           = isset( $pre_slides['posts'] ) ? $pre_slides['posts'] : array();

    if ( empty( $common ) || empty( $posts) )
      return false;

    //inititalize the slides array
    $slides      = array();

    //GENERATE SLIDES ARRAY
    foreach ( $posts as $_post_slide ) {
      $slide_model = $this -> czr_fn_get_single_post_slide_model( $slider_name_id, $_post_slide, $common, $img_size);
      if ( ! $slide_model )
        continue;
      $slides[ $_post_slide['ID'] ] = $slide_model;
    }//end of slides loop
    //returns the slides or false if nothing
    return apply_filters('czr_the_posts_slides', ! empty($slides) ? $slides : false );
  }


  /**
  * Helper
  * Return an ass array of 'posts'=> array of the post slide pre models 'common' => common properties
  *
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_pre_posts_slides( $args ){
    $defaults       = array(
      'img_size'            => null,
      //options
      'stickies_only'            => esc_attr( czr_fn_opt( 'tc_posts_slider_stickies' ) ),
      'show_title'               => esc_attr( czr_fn_opt( 'tc_posts_slider_title' ) ),
      'show_excerpt'             => esc_attr( czr_fn_opt( 'tc_posts_slider_text' ) ),
      'button_text'              => esc_attr( czr_fn_opt( 'tc_posts_slider_button_text' ) ),
      'posts_per_page'           => esc_attr( czr_fn_opt( 'tc_posts_slider_number' ) ),
      'link_type'                => esc_attr( czr_fn_opt( 'tc_posts_slider_link') )
    );

    $args         = apply_filters( 'czr_get_pre_posts_slides_args', wp_parse_args( $args, $defaults ) );
    extract( $args );

    $slider_responsive_images   = $this->allow_resp_images;

    //retrieve posts from the db
    $queried_posts    = $this -> czr_fn_query_posts_slider( $args );
    if ( empty ( $queried_posts ) )
      return array();

    /*** tc_thumb setup filters ***/
    // remove smart load img parsing if any
    $smart_load_enabled = 1 == esc_attr( czr_fn_opt( 'tc_img_smart_load' ) );
    if ( $smart_load_enabled )
      remove_filter( 'czr_thumb_html', 'czr_fn_parse_imgs' );

    // prevent adding thumb inline style when no center img is added
    add_filter( 'czr_post_thumb_inline_style', '__return_empty_string', 100 );

    //Allow retrieving first attachment as thumb
    add_filter( 'czr_use_attachment_as_thumb', '__return_true', 100 );
    /*** end tc_thumb setup ***/

    /* Get the pre_model */
    $pre_slides = $pre_slides_posts = array();
    foreach ( $queried_posts as $_post ) {
      $pre_slide_model = $this ->  czr_fn_get_single_post_slide_pre_model( $_post , $img_size, $args );
      if ( ! $pre_slide_model )
        continue;
      $pre_slides_posts[] = $pre_slide_model;
    }

    /* tc_thumb reset filters */
    // re-add smart load parsing if removed
    if ( $smart_load_enabled )
      add_filter( 'czr_thumb_html', 'czr_fn_parse_imgs' );

    // remove thumb style reset
    remove_filter( 'czr_post_thumb_inline_style', '__return_empty_string', 100 );

    // remove forced retrieval first attachment as thumb;
    remove_filter( 'czr_use_attachment_as_thumb', '__return_true', 100 );
    /* end tc_thumb reset filters */

    /* end tc_thumb reset filters */
    if ( ! empty( $pre_slides_posts ) ) {
      /*** Setup shared properties ***/
      /* Shared by all post slides, stored in the "common" field */
      // button and link whole slide
      // has button to be displayed?
      if ( strstr($link_type, 'cta') )
        $button_text            = $this -> czr_fn_get_post_slide_button_text( $button_text );
      else
        $button_text            = '';
      //link the whole slide?
      $link_whole_slide       = strstr( $link_type, 'slide') ? true : false;
      /*** end Setup shared properties ***/
      /* Add common and posts to the actual pre_slides array */
      $pre_slides['common']  = compact( 'button_text', 'link_whole_slide');
      $pre_slides['posts']   = $pre_slides_posts;
    }

    return $pre_slides;
  }


  /**
  * Helper
  * Returns the array of eligible posts for the slider of posts
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_query_posts_slider( $args = array() ) {
    $defaults       = array(
      'stickies_only'    => 0,
      'post_status'      => 'publish',
      'post_type'        => 'post',
      'orderby'          => 'date',
      'order'            => 'DESC',
      'posts_per_page'   => 5,
      'offset'           => 0,
      'suppress_filters' => false, // <- for language plugins
    );

    $args           = apply_filters( 'czr_query_posts_slider_args', wp_parse_args( $args, $defaults ) );
    $_posts         = false;

    if ( is_array($args) && !empty($args) && array_key_exists( 'posts_per_page', $args) && $args['posts_per_page'] > 0 ) {

      // Do we have to show only sticky posts?
      if ( array_key_exists( 'stickies_only', $args) && $args['stickies_only'] ) {
        // Are there sticky posts?
        $_sticky_posts  = get_option('sticky_posts');
        if ( ! empty( $_sticky_posts ) ) {
          $args = array_merge( $args, array( 'post__in' => $_sticky_posts  ) );
        }
        else {
          $args = false;
        }
      }

      if ( !empty($args) )
        $_posts = get_posts( $args );
    }

    return apply_filters( 'czr_query_posts_slider', $_posts, $args );
  }



  /**
  * Return a single post slide pre model
  * Returns and array of pre slides with data
  *
  * This method will build up the single object model which will be
  * the base for the actual post slide model
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_single_post_slide_pre_model( $_post , $img_size, $args ){
    $ID                     = $_post->ID;

    //attachment image
    $thumb                  = czr_fn_get_thumbnail_model( array(
        'requested_size'            => $img_size,
        'post_id'                   => $ID,
        'enable_wp_responsive_imgs' => isset($args['slider_responsive_images']) ? $args['slider_responsive_images'] : null,
        'placeholder'               => false
    ));


    $slide_background       = isset($thumb) && isset($thumb['tc_thumb']) ? $thumb['tc_thumb'] : null;

    // we assign a default thumbnail if needed.
    if ( ! $slide_background ) {
        $placeholder_src = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . 'front/img/slide-placeholder.png' );
        if ( $placeholder_src ) {
            $slide_background = sprintf('<img width="1200" height="500" src="%1$s" class="attachment-%2$s tc-thumb-type-thumb wp-post-image wp-post-image" alt="">',
                $placeholder_src,
                $img_size
            );
        } else {
            return false;
        }
    }

    if ( czr_fn_is_checked( 'tc_slider_img_smart_load' ) ) {
        $slide_background = czr_fn_parse_imgs( $slide_background ); //<- to prepare the img smartload
    }

    //title
    $title                  = ( isset( $args['show_title'] ) && $args['show_title'] ) ? $this -> czr_fn_get_post_slide_title( $_post, $ID) : '';
    //lead text
    $text                   = ( isset( $args['show_excerpt'] ) && $args['show_excerpt'] ) ? $this -> czr_fn_get_post_slide_excerpt( $_post, $ID) : '';
    return compact( 'ID', 'title', 'text', 'slide_background' );
  }


  /**
  * Return a single post slide model
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_single_post_slide_model( $slider_name_id, $_post_slide , $common, $img_size ) {
    //extract $_post_slide and $common
    $ID                    = isset( $_post_slide['ID'] ) ? $_post_slide['ID'] : '';
    $title                 = isset( $_post_slide['title'] ) ? $_post_slide['title'] : '';
    $text                  = isset( $_post_slide['text'] ) ? $_post_slide['text'] : '';
    $slide_background      = isset( $_post_slide['slide_background'] ) ? $_post_slide['slide_background'] : '';

    $button_text           = isset( $common['button_text'] ) ? $common['button_text'] : '';
    $link_whole_slide      = isset( $common['link_whole_slide'] ) ? $common['link_whole_slide'] : '';

    //background image
    $slide_background       = apply_filters( 'czr_posts_slide_background', $slide_background, $ID );
    // we don't want to show slides with no image
    if ( ! $slide_background )
      return false;
    $title                  = apply_filters('czr_posts_slider_title', $title, $ID );
    //lead text
    $text                   = apply_filters('czr_posts_slider_text', $text, $ID );
    //button
    $button_text            = apply_filters('czr_posts_slider_button_text', $button_text, $ID );
    //link
    $link_id                = apply_filters( 'czr_posts_slide_link_id', $ID );
    $link_url               = apply_filters( 'czr_posts_slide_link_url', $link_id ? get_permalink( $link_id ) : '', $ID );
    $link_target            = apply_filters( 'czr_posts_slide_link_target', '_self', $ID );
    $link_whole_slide       = apply_filters( 'czr_posts_slide_link_whole_slide', $link_whole_slide, $ID );
    $color_style            = apply_filters( 'czr_posts_slide_color_style', '', $ID );

    return apply_filters( 'czr_single_post_slide_model', compact(
        'title',
        'text',
        'button_text',
        'link_id',
        'link_url',
        'link_target',
        'link_whole_slide',
        'color_style',
        'slide_background'
    ), $ID );
  }

  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/


  /**
  * Getter
  * Returns the trimmed post slide title
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_title( $_post, $ID ) {
    $title_length   = apply_filters('czr_post_slide_title_length', 80, $ID );
    $more           = apply_filters('czr_post_slide_more', '...', $ID );
    return $this -> czr_fn_get_post_title( $_post, $title_length, $more );
  }

  /**
  * Getter
  * Returns the trimmed post slide excerpt
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_excerpt( $_post, $ID ) {
    $excerpt_length  = apply_filters( 'czr_post_slide_text_length', 80, $ID );
    $more            = apply_filters( 'czr_post_slide_more', '...', $ID );
    return $this -> czr_fn_get_post_excerpt( $_post, $excerpt_length, $more );
  }

  /**
  * Getter
  * Returns the trimmed posts slider button text
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_button_text( $button_text ) {
    $button_text_length  = apply_filters( 'czr_posts_slider_button_text_length', 80 );
    $more                = apply_filters( 'czr_post_slide_more', '...');
    $button_text         = apply_filters( 'czr_posts_slider_button_text_pre_trim' , $button_text );
    return czr_fn_text_truncate( $button_text, $button_text_length, $more );
  }

  /**
  * Helper
  * Returns the trimmed post title
  *
  * @return string
  *
  * Slightly different and simplified version of get_the_title to avoid conflicts with plugins filtering the_title
  * and custom trimming.
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into CZR_utils?
  function czr_fn_get_post_title( $_post, $default_title_length, $more ) {
    $title = $_post->post_title;
    if ( ! empty( $_post->post_password ) ) {
      $protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s', 'customizr' ), $_post);
      $title = sprintf( $protected_title_format, $title );
    }
    $title = apply_filters( 'czr_post_title_pre_trim' , $title );
    return czr_fn_text_truncate( $title, $default_title_length, $more);
  }

  /**
  * Helper
  * Returns the trimmed post excerpt
  *
  * @return string
  *
  * Slightly different and simplified version of wp_trim_excerpt to avoid conflicts with plugins filtering get_excerpt and the_content
  * and custom trimming.
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into CZR_utils?
  function czr_fn_get_post_excerpt( $_post, $default_text_length, $more ) {
    if ( ! empty( $_post->post_password) )
      return __( 'There is no excerpt because this is a protected post.', 'customizr' );
    $excerpt = '' != $_post->post_excerpt ? $_post->post_excerpt : $_post->post_content;
    $excerpt = apply_filters( 'czr_post_excerpt_pre_sanitize' , $excerpt );
    // below some function applied to the_content & the_excerpt filters
    // we cannot use those filters 'cause some plugins, e.g. qtranslate
    // filter those as well invalidating our transient
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = wptexturize( $excerpt );
    $excerpt = convert_chars( $excerpt );
    $excerpt = wpautop( $excerpt );
    $excerpt = shortcode_unautop( $excerpt );
    $excerpt = str_replace(']]>', ']]&gt;', $excerpt );
    $excerpt = apply_filters( 'czr_post_excerpt_pre_trim' , $excerpt );
    return czr_fn_text_truncate( $excerpt, $default_text_length, $more);
  }
}