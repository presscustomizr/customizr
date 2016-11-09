<?php
/**
 * The template for displaying the 404 content
 */
?>
<header class="entry-header text-xs-center"<?php czr_fn_echo('element_attributes') ?>>
  <h1 class="entry-title big-text-10 m-t-05"><?php _e( '404', 'customizr') ?></h1>
  <h2><?php _e('Ooops, page not found', 'customizr') ?></h2>
</header>
<hr class='featurette-divider'>
<article id="post-0" class="post error404 no-results not-found row text-xs-center">
  <div class="tc-content col-xs-12">
    <div class="entry-content">
      <p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
  </div>
</article>