<?php
/**
 * The template for displaying the headings of single post/page/attachment in post lists (alternate layout)
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="entry-header" <?php czr_echo('element_attributes') ?>>
  <?php
    if ( czr_has('post_page_title') ) { czr_render_template('content/post-lists/post_page_title', 'post_page_title'); }
    /* Post metas */
    if ( czr_has('post_metas_button') ) { czr_render_template( 'content/post-metas/post_metas', 'post_metas_button'); }
    elseif ( czr_has('post_metas_text') ) { czr_render_template('content/post-metas/post_metas_text', 'post_metas_text'); }
    elseif ( czr_has('post_metas_attachment') ) { czr_render_template('content/post-metas/attachment_post_metas', 'post_metas_attachment'); }
    //do_action( '__headings_content__' );
  ?>
</header>
