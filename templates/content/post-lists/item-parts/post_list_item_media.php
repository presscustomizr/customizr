<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * In WP loop
 *
 */
?>
<?php if ( ( (bool) $media_content = czr_fn_get('media_content') ) || (bool) $has_icon = czr_fn_get('has_format_icon_media') ) : ?>
<section class="tc-thumbnail entry-media__holder <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-media__wrapper czr__r-i <?php czr_fn_echo('inner_wrapper_class') ?>">
  <?php
  if ( $media_content ):
    echo $media_content;

    if ( czr_fn_get('has_media_action') && (bool) ( $original_thumb_url = czr_fn_get( 'original_thumb_url' ) ) ):
      czr_fn_post_action( $link = $original_thumb_url, $class = 'expand-img' );
    endif;

    elseif ( $has_icon ):
  ?>
      <div class="post-type__icon">
        <a class="bg-icon-link icn-format" rel="bookmark" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" href="<?php the_permalink() ?>"></a>
      </div>
  <?php
  endif ?>
  </div>
</section>
<?php endif;