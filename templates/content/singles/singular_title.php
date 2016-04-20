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
<h1 class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php the_title() ?>
  <?php if ( tc_has( 'comment_bubble' ) ) tc_render_template( 'modules/comment_bubble', 'comment_bubble' ) ?>
  <?php if ( tc_has( 'edit_button' ) ) tc_render_template( 'modules/edit_button', 'edit_button' ) ?>
  <?php if ( tc_has( 'recently_updated' ) ) tc_render_template( 'modules/recently_updated', 'recently_updated' ) ?>
</h1>
