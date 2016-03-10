<header class="entry-header">
  <h1 class="entry-title "><?php _e('Ooops, page not found', 'customizr') ?></h1>
  <hr class="featurette-divider after-page-title">
</header>
<article <?php echo $content_404_model -> article_selectors ?> >
  <div class="<?php echo $content_404_model -> wrapper_class ?>">
    <div class="<?php echo $content_404_model -> inner_class ?>">
      <blockquote><p><?php _e( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ) ?></p><cite><?php _e( 'George Orwell' , 'customizr' ) ?></cite></blockquote>
      <p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ) ?></p>
      <?php get_search_form() ?>
    </div>
    <hr class='featurette-divider after-content'>
  </div>
</article>
