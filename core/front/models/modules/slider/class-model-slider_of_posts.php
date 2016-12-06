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
  protected function czr_fn_get_the_slides( $slider_name_id, $img_size = 'full' ) {
    $use_transient   = apply_filters( 'tc_posts_slider_use_transient', ! czr_fn_is_customizing() );
    //Do not use transient when in the customizer preview (this class is not called in the customize left panel)
    $store_transient = $load_transient = $use_transient;

    // delete transient when in the customize preview
    if ( ! $use_transient )
      delete_transient( 'tc_posts_slides' );

    return apply_filters( 'tc_the_slides', $this -> czr_fn_get_the_posts_slides( $slider_name_id, $img_size, $load_transient , $store_transient ) );
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
  * 1) get the pre slides model, can be either stored in the transient or live generated
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
  private function czr_fn_get_the_posts_slides( $slider_name_id, $img_size, $load_transient = true, $store_transient = true ) {
    $load_transient  = apply_filters( 'tc_posts_slider_load_transient'  , $load_transient );
    $store_transient = apply_filters( 'tc_posts_slider_store_transient', $store_transient );
    $pre_slides      = $this -> czr_fn_get_pre_posts_slides( compact( 'img_size', 'load_transient', 'store_transient' ) );
    //filter the pre_model
    $pre_slides      = apply_filters( 'tc_posts_slider_pre_model', $pre_slides, $this );
    //if the slider not longer exists or exists but is empty, return false
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
  * This method takes care of building the array of convenient objects
  * we'll use to build the actual posts slider model
  * and store it in a transient when required
  *
  * Setps:
  * - check if there's a transient and we have to use it => get the transient
  * a pre_model array
  *
  * - if not transient (both cases: no transient, don't use transient)
  *  build the array of posts to use
  *
  * - eventually store the transient
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_pre_posts_slides( $args ){
    $defaults       = array(
      'img_size'            => null,
      'load_transient'      => true,
      'store_transient'     => true,
      'transient_name'      => 'tc_posts_slides',
      //options
      'stickies_only'       => esc_attr( czr_fn_get_opt( 'tc_posts_slider_stickies' ) ),
      'show_title'          => esc_attr( czr_fn_get_opt( 'tc_posts_slider_title' ) ),
      'show_excerpt'        => esc_attr( czr_fn_get_opt( 'tc_posts_slider_text' ) ),
      'button_text'         => esc_attr( czr_fn_get_opt( 'tc_posts_slider_button_text' ) ),
      'limit'               => esc_attr( czr_fn_get_opt( 'tc_posts_slider_number' ) ),
      'link_type'           => esc_attr( czr_fn_get_opt( 'tc_posts_slider_link') ),
    );
    $args         = apply_filters( 'czr_get_pre_posts_slides_args', wp_parse_args( $args, $defaults ) );
    extract( $args );
    $load_transient = false;

    if ( $load_transient )
      // the transient stores the pre_model
      $pre_slides = get_transient( $transient_name );
    else
      $pre_slides = false;
    // We have to retrieve the posts and build the pre_model when $pre_slides_model is null:
    // a) we don't have to use the transient
    // b) the transient doesn't exist
    if ( false !== $pre_slides )
      return $pre_slides;
    //retrieve posts from the db
    $queried_posts    = $this -> czr_fn_query_posts_slider( $args );
    if ( empty ( $queried_posts ) )
      return array();
    /*** tc_thumb setup filters ***/
    // remove smart load img parsing if any
    $smart_load_enabled = 1 == esc_attr( czr_fn_get_opt( 'tc_img_smart_load' ) );
    if ( $smart_load_enabled )
      remove_filter( 'tc_thumb_html', 'czr_fn_parse_imgs' );
    /*** end tc_thumb setup ***/
    //allow responsive images?
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      $args['slider_responsive_images'] = 0 == esc_attr( czr_fn_get_opt('tc_resp_slider_img') ) ? false : true ;
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
      add_filter('tc_thumb_html', 'czr_fn_parse_imgs' );
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
    if ( $store_transient )
      set_transient( $transient_name, $pre_slides , 60*60*24*365*20 );//20 years of peace
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
    global $wpdb;
    $defaults       = array(
      'stickies_only'  => 0,
      'post_status'    => 'publish',
      'post_type'      => 'post',
      'show_title'     => true,
      'show_excerpt'   => true,
      'pt_where'       => " AND meta_key='_thumbnail_id'",
      'pa_where'       => " AND attachments.post_type='attachment' AND attachments.post_mime_type LIKE '%image%' AND attachments.post_parent=posts.ID",
      'join'           => '',
      'join_where'     => '',
      'order_by'       => 'posts.post_date DESC',
      'limit'          => 5,
      'offset'         => 0,
    );
    $args           = apply_filters( 'tc_query_posts_slider_args', wp_parse_args( $args, $defaults ) );
    extract( $args );
    /* Set up */
    $columns        = 'posts.ID, posts.post_date';
    $columns       .= $show_title   ? ', posts.post_title' : '';
    $columns       .= $show_excerpt ? ', posts.post_excerpt, posts.post_content' : '';
    // if we have to show the title or the excerpt the post_password field is needed to know whether or not is a protected post
    $columns       .= $show_title || $show_excerpt ? ', posts.post_password' : '';
    $pt_where       = "posts.post_status='$post_status' AND posts.post_type='$post_type'". $pt_where;
    $pa_where       = "posts.post_status='$post_status' AND posts.post_type='$post_type'". $pa_where;
    // Do we have to show only sticky posts?
    if ( $stickies_only ) {
      // Are there sticky posts?
      $_sticky_posts  = get_option('sticky_posts');
      $_sticky_column = '';
      if ( ! empty( $_sticky_posts ) ) {
        $_sticky_posts_ids = implode(',', $_sticky_posts );
        $_filter_stickies_only = sprintf(" AND posts.ID IN (%s)",
            $_sticky_posts_ids
        );
        $pa_where .= $_filter_stickies_only;
        $pt_where .= $_filter_stickies_only;
      }
    }
    $sql = sprintf( 'SELECT DISTINCT %1$s FROM ( %2$s ) as posts %3$s %4$s ORDER BY %5$s LIMIT %6$s OFFSET %7$s',
             apply_filters( 'tc_query_posts_slider_columns', $columns, $args ),
             $this -> czr_fn_get_posts_have_tc_thumb_sql(
               apply_filters( 'tc_query_posts_slider_columns', $columns, $args ),
               apply_filters( 'tc_query_posts_slide_thumbnail_where', $pt_where, $args ),
               apply_filters( 'tc_query_posts_slider_attachment_where', $pa_where, $args )
             ),
             apply_filters( 'tc_query_posts_slider_join', $join, $args ),
             apply_filters( 'tc_query_posts_slider_join_where', $join_where, $args ),
             apply_filters( 'tc_query_posts_slider_orderby', $order_by, $args ),
             $limit,
             $offset
    );
    $sql = apply_filters( 'tc_query_posts_slider_sql', $sql, $args );
    $_posts = $wpdb->get_results( $sql );
    return apply_filters( 'tc_query_posts_slider', $_posts, $args );
  }


  /**
  * Helper
  * Returns the SQL to retrieve the posts which have a thumbnail OR attachments
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_tc_thumb_sql( $_columns, $_pt_where = '', $_pa_where = '' ) {
    return apply_filters( 'czr_get_posts_have_tc_thumb_sql', sprintf( '%1$s UNION %2$s',
        $this -> czr_fn_get_posts_have_thumbnail_sql( $_columns, $_pt_where ),
        $this -> czr_fn_get_posts_have_attachment_sql( $_columns, $_pa_where )
    ));
  }


  /**
  * Helper
  * Returns the SQL to retrieve the posts which have a thumbnail
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_thumbnail_sql( $_columns, $_where = '' ) {
    global $wpdb;
    return apply_filters( 'czr_get_posts_have_thumbnail_sql', "
        SELECT $_columns
        FROM $wpdb->posts AS posts INNER JOIN $wpdb->postmeta AS metas
        ON posts.ID=metas.post_id
        WHERE $_where
   ");
  }


  /**
  * Helper
  * Returns the SQL to retrieve the posts which have an attachment
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_attachment_sql( $_columns, $_where = '' ) {
    global $wpdb;
    return apply_filters( 'czr_get_posts_have_attachment_sql', "
        SELECT $_columns FROM $wpdb->posts attachments, $wpdb->posts posts
        WHERE $_where
    ");
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
    $thumb                  = czr_fn_get_thumbnail_model($img_size, $ID, null, isset($args['slider_responsive_images']) ? $args['slider_responsive_images'] : null, '' );
    $slide_background       = isset($thumb) && isset($thumb['tc_thumb']) ? $thumb['tc_thumb'] : null;
    // we don't want to show slides with no image
    if ( ! $slide_background )
      return false;
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
        'slide_background',
        'edit_suffix'
    ), $ID );
  }

  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/

  /**
  * Setter
  *
  * @package Customizr
  * @since Customizr 3.4.9
  */
  function czr_fn_cache_posts_slider( $args = array() ) {
    $defaults = array (
      //use the home slider_width
      'img_size'        => 1 == czr_fn_get_opt( 'tc_slider_width' ) ? 'slider-full' : 'slider',
      'load_transient'  => false,
      'store_transient' => true,
      'transient_name'  => 'tc_posts_slides'
    );
    $this -> czr_fn_get_pre_posts_slides( wp_parse_args( $args, $defaults) );
  }

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
    return $this -> czr_fn_trim_text( $button_text, $button_text_length, $more );
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
    return $this -> czr_fn_trim_text( $title, $default_title_length, $more);
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
    return $this -> czr_fn_trim_text( $excerpt, $default_text_length, $more);
  }
}