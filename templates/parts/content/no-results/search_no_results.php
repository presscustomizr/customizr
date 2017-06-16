<?php
/**
 * The template for displaying the search no results content
 */
?>
<header class="entry-header text-center" <?php czr_fn_echo('element_attributes') ?>>
  <h2 class="entry-title"><?php _e('Nothing Found', 'customizr') ?></h2>
</header>
<hr class='featurette-divider'>
<article id="post-0" class="post error404 no-results not-found row text-center">
  <div class="tc-content col-12">
    <div class="entry-content">
      <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
  </div>
</article>