<?php
class CZR_related_posts_model_class extends CZR_model {

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $_preset = array(
      'excerpt_length'        => 22,
      'media_cols'            => 'col-xs-6',
      'content_cols'          => 'col-xs-6',
      'element_width'         => array( 'col-xs-12', 'col-lg-6' ),
    );

    return $_preset;
  }
  /*

  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_setup_late_properties() {
    $this -> czr_fn_setup_query();
    $this -> czr_fn_setup_text_hooks();
  }

  /*
  * Fired just before the view is rendered
  * @hook: post_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_reset_late_properties() {
    //all post lists do this
    $this -> czr_fn_reset_text_hooks();

    $this -> czr_fn_reset_query();
  }


  /**
  * hook : __masonry_loop_start
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks() {
    //filter the excerpt length
    add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : __masonry_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks() {
    remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length;
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }

  function czr_fn_get_article_selectors() {
    $_width  = is_array( $this -> element_width ) ? $this -> element_width : array();

    return czr_fn_get_the_post_list_article_selectors( array_merge( $_width, array( 'grid-item') ), "_{$this -> id}" );
  }


  function czr_fn_setup_query() {
    /* Taken from hueman */
    global $wp_query;

    /* Query setup */
    $post_id = get_the_ID();

    // Define shared post arguments
    $args = array(
      'no_found_rows'           => true,
      'update_post_meta_cache'  => false,
      'update_post_term_cache'  => false,
      'ignore_sticky_posts'     => 1,
      'orderby'                 => 'rand',
      'post__not_in'            => array($post_id),
      'posts_per_page'          => 3
    );

    // Related by categories
    if ( 'categories' == czr_fn_get_opt('tc_related_posts') ) {
      $cats = get_post_meta($post_id, 'related-cat', true);
      if ( !$cats ) {
        $cats = wp_get_post_categories($post_id, array('fields'=>'ids'));
        $args['category__in'] = $cats;
      } else {
        $args['cat'] = $cats;
      }
    }

    // Related by tags
    if ( 'tags' == czr_fn_get_opt('tc_related_posts') ) {
      $tags = get_post_meta($post_id, 'related-tag', true);
      if ( !$tags ) {
        $tags = wp_get_post_tags($post_id, array('fields'=>'ids'));
        $args['tag__in'] = $tags;
      } else {
        $args['tag_slug__in'] = explode(',', $tags);
      }
      if ( !$tags ) { $break = true; }
    }

    if ( isset($break) || empty( $args ) )
      return;

    $wp_query = new WP_Query( $args );

    $this -> czr_fn_update( array('query' => $wp_query ) );
  }


  function czr_fn_reset_query() {
    if ( ! $this -> query )
      return;

    wp_reset_query();
    wp_reset_postdata();
  }

}//end class