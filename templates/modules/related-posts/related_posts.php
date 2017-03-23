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
        <span class="btn btn-skin-dark inverted czr-carousel-prev slider-control czr-carousel-control disabled icn-left-open-big" title="<?php esc_attr_e('Previous related articles', 'customizr')?>" tabindex="0"></span>
        <span class="btn btn-skin-dark inverted czr-carousel-next slider-control czr-carousel-control icn-right-open-big" title="<?php esc_attr_e('Next related articles', 'customizr')?>" tabindex="0"></span>
      </div>
    <?php endif ?>
  </header>
  <div class="row grid-container__square-mini <?php echo $wp_query->post_count > 1 ? 'carousel-inner' : '' ?>">
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