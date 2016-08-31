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
  <a href="<?php the_permalink() ?>"><?php /*test*/ the_post_thumbnail('normal', array( 'class' => 'post-thumbnail' )) ?></a>
<?php else: ?>
  <div class="post-type__icon">
    <icon class="icn-<?php czr_fn_echo('icon_type') ?>"></i>
  <div>      
<?php endif ?>
</section>