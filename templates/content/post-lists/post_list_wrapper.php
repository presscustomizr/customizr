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
<article <?php czr_echo( 'article_selectors' ) ?> <?php czr_echo('element_attributes') ?>>
  <?php
    //what is the thumbnail model  ?
    $_thumbnail_model = czr_has('post_list_standard_thumb') ? 'post_list_standard_thumb' : 'post_list_rectangular_thumb';

    if ( 'content' == czr_get( 'place_1' ) ) {
      if ( czr_has('content') ) { czr_render_template('content/post-lists/post_list_content', 'post_list_content'); }
      if ( czr_has($_thumbnail_model) ) { czr_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
    } else {
      if ( czr_has($_thumbnail_model) ) { czr_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
      if ( czr_has('content') ) { czr_render_template('content/post-lists/post_list_content', 'post_list_content'); }
    }
  ?>
<hr class="featurette-divider __loop">
</article>
