<?php
/**
 * The template for displaying the article wrapper:
 * used for articles singulars (post/page/attachment)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo('element_attributes') ?>>
  <?php
    do_action( '__before_inner_singular_article' );
    if ( tc_has('post_thumbnail') && 'before_title' == tc_get( 'thumbnail_position' ) ) { tc_render_template('content/singles/thumbnail_single', 'post_thumbnail'); }
    if ( tc_has('singular_headings') ) {
      tc_render_template('content/singles/singular_headings', 'singular_headings');
    }
    if ( tc_has('page') ) { tc_render_template('content/singles/page_content'); }
    elseif( tc_has('post') ) { tc_render_template('content/singles/post_content'); }
    elseif( tc_has('attachment') ) { tc_render_template('content/singles/attachment_content'); }
    do_action( '__after_inner_singular_article' );
    //do_action( '__article__' ) ?>
</article>
