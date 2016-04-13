<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * In WP loop
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo('element_attributes') ?>>
  <?php
    //what is the thumbnail model  ?
    $_thumbnail_model = tc_has('post_list_standard_thumb') ? 'post_list_standard_thumb' : 'post_list_rectangular_thumb';

    if ( 'content' == tc_get( 'place_1' ) ) {
      if ( tc_has('content') ) { tc_render_template('content/post-lists/post_list_content', 'post_list_content'); }
      if ( tc_has($_thumbnail_model) ) { tc_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
    } else {
      if ( tc_has($_thumbnail_model) ) { tc_render_template('content/post-lists/post_list_thumbnail', $_thumbnail_model); }
      if ( tc_has('content') ) { tc_render_template('content/post-lists/post_list_content', 'post_list_content'); }
    }
  ?>
</article>
<hr class="featurette-divider">
