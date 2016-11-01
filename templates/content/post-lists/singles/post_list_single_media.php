<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<?php if ( ( (bool) $media_content = czr_fn_get('media_content') ) || (bool) $has_icon = czr_fn_get('has_format_icon_media') ) : ?>
<section class="tc-thumbnail entry-media__holder <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-media__wrapper <?php czr_fn_echo('inner_wrapper_class') ?>">
  <?php if ( $media_content ): ?>
    <?php echo $media_content ?>
    <?php if ( czr_fn_get('has_media_action') && (bool) ( $original_thumb_url = czr_fn_get( 'original_thumb_url' ) ) ): ?>
      <div class="post-action">
        <a href="<?php echo esc_url( $original_thumb_url ) ?>" class="expand-img"><icon class="icn-expand"></icon></a>
      </div>
    <?php endif ?>
  <?php elseif ( $has_icon ): ?>
    <div class="post-type__icon">
      <i class="icn-format"><a class="bg-icon-link" rel="bookmark" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" href="<?php the_permalink() ?>"></a></i>
    </div>
  <?php endif ?>
  </div>
</section>
<?php endif;