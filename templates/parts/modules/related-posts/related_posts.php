<?php
/*
* Related posts wrapper template
*/
?>
<?php if ( have_posts() ) : ?>
<section class="post-related-articles czr-carousel <?php czr_fn_echo('element_class') ?>" id="related-posts-section" <?php czr_fn_echo('element_attributes') ?>>
  <header class="row flex-row">
    <h3 class="related-posts_title col"><?php _e('You may also like', 'customizr') ?></h3>
    <?php if ( $wp_query->post_count > 2 ) :
        //rtl compliance
        if ( is_rtl() ) {
            $_icon_prev =  'icn-right-open-big';
            $_icon_next =  'icn-left-open-big';
        }
        else {
            $_icon_prev =  'icn-left-open-big';
            $_icon_next =  'icn-right-open-big';
        }
    ?>
      <div class="related-posts_nav col col-auto">
        <span class="btn btn-skin-dark inverted czr-carousel-prev slider-control czr-carousel-control disabled <?php echo $_icon_prev ?>" title="<?php esc_attr_e('Previous related articles', 'customizr')?>" tabindex="0"></span>
        <span class="btn btn-skin-dark inverted czr-carousel-next slider-control czr-carousel-control <?php echo $_icon_next ?>" title="<?php esc_attr_e('Next related articles', 'customizr')?>" tabindex="0"></span>
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
            'article_selectors'  => czr_fn_get_property( 'article_selectors' ),
            'media_cols'         => czr_fn_get_property('media_cols'),
            'content_cols'       => czr_fn_get_property('content_cols')
          )
        )
      );
    endwhile
  ?>
  </div>
</section>
<?php endif;