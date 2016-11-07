<?php
/**
 * The template for displaying the headings in singular
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

/* Case we're displaying the headings of the contents such as posts/pages/attachments both as singular and as elements of lists of posts */
?>
<header class="entry-header" <?php czr_fn_echo('element_attributes') ?>>
  <?php
    if ( czr_fn_has('post_page_title') ) { czr_fn_render_template('content/singles/singular_title', 'post_page_title'); }
  ?>
  <hr class="featurette-divider headings singular-content">
</header>
