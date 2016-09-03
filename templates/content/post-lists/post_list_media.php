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
<section class="tc-thumbnail entry-image__holder <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
<?php if ( czr_fn_get('czr_has_media') ): ?>
  <?php czr_fn_echo('media_content') ?>
<?php else: ?>
  <div class="post-type__icon">
    <icon class="icn-<?php czr_fn_echo('icon_type') ?>"></i>
  <div>      
<?php endif ?>
</section>