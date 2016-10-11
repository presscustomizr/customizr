<?php
class CZR_cl_related_posts_wrapper_model_class extends CZR_cl_model {

  function czr_fn_get_query() {
    /* Query setup */
    wp_reset_postdata();
    global $post;

    // Define shared post arguments
    $args = array(
      'no_found_rows'       => true,
      'update_post_meta_cache'  => false,
      'update_post_term_cache'  => false,
      'ignore_sticky_posts'   => 1,
      'orderby'         => 'rand',
      'post__not_in'        => array($post->ID),
      'posts_per_page'      => 3
    );
    // Related by categories
    if ( 'categories' == czr_fn_get_opt('tc_related_posts') ) {
      $cats = get_post_meta($post->ID, 'related-cat', true);
      if ( !$cats ) {
        $cats = wp_get_post_categories($post->ID, array('fields'=>'ids'));
        $args['category__in'] = $cats;
      } else {
        $args['cat'] = $cats;
      }
    }
    // Related by tags
    if ( 'tags' == czr_fn_get_opt('tc_related_posts') ) {
      $tags = get_post_meta($post->ID, 'related-tag', true);
      if ( !$tags ) {
        $tags = wp_get_post_tags($post->ID, array('fields'=>'ids'));
        $args['tag__in'] = $tags;
      } else {
        $args['tag_slug__in'] = explode(',', $tags);
      }
      if ( !$tags ) { $break = true; }
    }

    return !isset($break)? $args: array();
  }
}//end class