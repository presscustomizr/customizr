<?php
/**
 * The template for displaying the post/page/attachment titles the lists of posts (alternate layout)
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<h2 class="<?php czr_echo( 'element_class') ?>" <?php czr_echo('element_attributes') ?>>
  <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  <?php if ( czr_has( 'comment_bubble' ) ) czr_render_template( 'modules/comment_bubble', 'comment_bubble' ) ?>
  <?php if ( czr_has( 'edit_button' ) ) czr_render_template( 'modules/edit_button', 'edit_button' ) ?>
  <?php if ( czr_has( 'recently_updated' ) ) czr_render_template( 'modules/recently_updated', 'recently_updated' ) ?>
</h2>
