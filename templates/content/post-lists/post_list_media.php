<?php
/**
 * The template for displaying the thumbnails in post lists (alternate layout) contexts
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<section class="tc-thumbnail entry-image__container <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
<?php if ( czr_fn_get('has_post_media') ): ?>
  <?php czr_fn_echo('media_content') ?>
  <?php if ( (bool) ( $original_thumb_url = czr_fn_get( 'original_thumb_url' ) ) ): ?>
    <div class="post-action">
      <a href="<?php echo $original_thumb_url ?>" class="expand-img"><icon class="icn-expand"></icon></a>
    </div>
  <?php endif ?>
<?php elseif ( (bool) $icon_type = czr_fn_get('icon_type') ): ?>
  <div class="post-type__icon">
    <i class="icn-<?php echo $icon_type ?>"><a class="bg-link" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" href="<?php the_permalink() ?>"></a></i>
  <div>      
<?php endif ?>
</section>