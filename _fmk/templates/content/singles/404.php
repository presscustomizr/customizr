<?php
/**
 * The template for displaying the 404 content
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="entry-header"<?php czr_fn_echo('element_attributes') ?>>
  <h1 class="entry-title "><?php _e('Ooops, page not found', 'customizr') ?></h1>
  <hr class="featurette-divider headings after-404-title">
</header>
<article id="post-0" class="post error404 no-results not-found row-fluid">
  <div class="tc-content span12 format-quote">
    <div class="entry-content format-icon">
      <blockquote><p><?php _e( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ) ?></p><cite><?php _e( 'George Orwell' , 'customizr' ) ?></cite></blockquote>
      <p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
    <hr class='featurette-divider after-content'>
  </div>
</article>
