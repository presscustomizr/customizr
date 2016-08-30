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
  <a href="<?php the_permalink() ?>"><?php /*test*/ the_post_thumbnail('normal', array( 'class' => 'post-thumbnail' )) ?></a>
</section>