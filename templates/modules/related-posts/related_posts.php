<?php
/*
* Related posts wrapper template
*/
?>
<?php if ( have_posts() ) : ?>
<section class="post-related-articles czr-carousel" <?php czr_fn_echo('element_attributes') ?>>
  <header>
    <h3 class="related-posts_title"><?php _e('You may also like', 'customizr') ?></h3>
    <?php if ( $wp_query->post_count > 1 ) : ?>
      <div class="related-posts_nav">
        <span class="slider-prev slider-control disabled" title="Previous related articles"><i class="icn-left-open-big"></i></span>
        <span class="slider-next slider-control" title="Next related articles"><i class="icn-right-open-big"></i></span>
      </div>
    <?php endif ?>
  </header>
  <div class="row grid grid-container__square-mini">
  <?php
    while ( have_posts() ):
      the_post();
      czr_fn_render_template(
        'modules/related-posts/related_post',
        array(
          'model_args' => array(
            'article_selectors' => czr_fn_get( 'article_selectors' ),
            'media_cols'         => czr_fn_get('media_cols'),
            'content_cols'       => czr_fn_get('content_cols')
          )
        )
      );
    endwhile
  ?>
  </div>
</section>
<?php endif;