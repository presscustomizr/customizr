<?php
/**
 * The template for displaying the content of a search with no results
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="search-header" <?php tc_echo('element_attributes') ?>>
  <h1 class="entry-title format-icon"><?php _e('No Search Results for :', 'customizr') ?> <span><?php echo get_search_query() ?></span></h1>
  <hr class="featurette-divider headings after-noresults-title">
</header>
<article id="post-0" class="post error404 no-results not-found row-fluid">
  <div class="tc-content span12 format-quote">
    <div class="entry-content format-icon">
      <blockquote><p><?php _e( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ) ?></p><cite><?php _e( 'Sir Winston Churchill' , 'customizr' ) ?></cite></blockquote>
      <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
    <hr class='featurette-divider after-content'>
  </div>
</article>
