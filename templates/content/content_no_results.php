<header class="search-header">
  <h1 class="<?php echo $content_no_results_model -> title_class ?>"><?php _e('No Search Results for :', 'customizr') ?> <span><?php echo get_search_query() ?></span></h1>
  <hr class="featurette-divider after-page-title">
</header>
<article <?php echo $content_no_results_model -> article_selectors ?> >
  <div class="<?php echo $content_no_results_model -> wrapper_class ?>">
    <div class="<?php echo $content_no_results_model -> inner_class ?>">
      <blockquote><p><?php _e( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ) ?></p><cite><?php _e( 'Sir Winston Churchill' , 'customizr' ) ?></cite></blockquote>
      <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
    <hr class='featurette-divider after-content'>
  </div>
</article>
