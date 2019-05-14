<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * In WP loop
 *
 */
?>
<section class="tc-thumbnail entry-media__holder <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-media__wrapper czr__r-i <?php czr_fn_echo('inner_wrapper_class') ?>">
  <?php
  if ( czr_fn_get_property( 'media_template' ) ):
    if ( czr_fn_get_property( 'has_permalink' ) ) : ?>
      <a class="<?php czr_fn_echo( 'link_class' ) ?>" rel="bookmark" href="<?php the_permalink() ?>"></a>
  <?php
    endif; //bg-link

      //render the $media_template;
      czr_fn_render_template( czr_fn_get_property( 'media_template' ), czr_fn_get_property( 'media_args' ) );

    elseif ( 'format-icon' == czr_fn_get_property( 'media' ) ):
  ?>
      <div class="post-type__icon">
        <a class="bg-icon-link icn-format" rel="bookmark" href="<?php the_permalink() ?>"></a>
      </div>

  <?php
  endif ?>
  </div>
</section>