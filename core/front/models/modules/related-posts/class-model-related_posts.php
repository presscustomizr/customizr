<?php
class CZR_related_posts_model_class extends CZR_model {
  public $excerpt_length = 18;
  public $media_col      = 'col-xs-6';
  public $content_col    = 'col-xs-6';

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {/*
    * The alternate grid does the same
    */
    add_action( '__related_posts_loop_start', array( $this, 'czr_fn_setup_text_hooks') );
    add_action( '__related_posts_loop_end'  , array( $this, 'czr_fn_reset_text_hooks') );

    return $model;
  }

  /**
  * hook : __masonry_loop_start
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks( $model_id ) {
    echo "here";
    if ( $model_id == $this->id  )
      //filter the excerpt length
      add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : __masonry_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks( $model_id ) {
    if ( $model_id == $this->id  )
      remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }

  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    return $this->excerpt_length;
  }

  function czr_fn_get_article_selectors() {
    return czr_fn_get_the_post_list_article_selectors( array('col-xs-12', 'col-md-6', 'grid-item') );
  }


  function czr_fn_get_query() {
    /* Query setup */
    /* wp_reset_postdata(); */
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

    return !isset($break)? $args: array();
  }
}//end class