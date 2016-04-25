<?php
/**
 * The template for displaying the post/page/attachment titles in singular context
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<h1 class="<?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
  <?php the_title() ?>
  <?php if ( czr_has( 'comment_bubble' ) ) czr_render_template( 'modules/comment_bubble', 'comment_bubble' ) ?>
  <?php if ( czr_has( 'edit_button' ) ) czr_render_template( 'modules/edit_button', 'edit_button' ) ?>
  <?php if ( czr_has( 'recently_updated' ) ) czr_render_template( 'modules/recently_updated', 'recently_updated' ) ?>
</h1>
