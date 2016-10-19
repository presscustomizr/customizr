<?php
/*
* Related posts wrapper template
*/
?>
<?php

global $wp_query, $wp_the_query;
//do we have a custom query ?
$wp_query = new WP_Query( czr_fn_get('query') );

if ( have_posts() ) {
  do_action('__related_posts_loop_start', czr_fn_get('id') );
?>
<section class="post-related-articles czr-carousel" <?php czr_fn_echo('element_attributes') ?>>
  <header>
    <h3 class="related-posts_title"><?php _e('You may also like', 'customizr') ?></h3>
    <?php if ( $wp_query->post_count > 1 ) : ?>
      <div class="related-posts_nav">
        <span class="slider-prev slider-control" title="Previous related articles"><i class="icn-left-open-big"></i></span>
        <span class="slider-next slider-control" title="Next related articles"><i class="icn-right-open-big"></i></span>
      </div>
    <?php endif ?>
  </header>
  <div class="row grid square-grid__mini">
  <?php
    while ( have_posts() ):
      the_post();
      if ( czr_fn_has( 'related_post_item' ) )
        czr_fn_render_template(
          'modules/related-posts/related_post_item', 'related_post_item', array(
            'article_selectors' => czr_fn_get( 'article_selectors' ),
            'media_col' => czr_fn_get('media_col'),
            'content_col' => czr_fn_get('content_col')
        ));
    endwhile
  ?>
  </div>
</section>

<?php
  do_action('__related_posts_loop_end', czr_fn_get('id') );
}
wp_reset_query();
wp_reset_postdata();