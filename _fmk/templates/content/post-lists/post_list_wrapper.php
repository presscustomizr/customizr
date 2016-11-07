<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php czr_fn_echo( 'article_selectors' ) ?> <?php czr_fn_echo('element_attributes') ?>>
  <?php
    //what is the thumbnail model  ?
    $_thumbnail_model = czr_fn_has('post_list_standard_thumb') ? 'post_list_standard_thumb' : 'post_list_rectangular_thumb';

    if ( 'content' == czr_fn_get( 'place_1' ) ) {
      if ( czr_fn_has('content') ) { czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content'); }
      if ( czr_fn_has($_thumbnail_model) ) { czr_fn_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
    } else {
      if ( czr_fn_has($_thumbnail_model) ) { czr_fn_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
      if ( czr_fn_has('content') ) { czr_fn_render_template('content/post-lists/post_list_content', 'post_list_content'); }
    }
  ?>
<hr class="featurette-divider __loop">
</article>
