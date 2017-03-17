<?php
/**
 * The template for displaying the post action button
 *
 */
?>
<div class="post-action btn btn-skin-darkest-shaded <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a href="<?php echo esc_url( czr_fn_get( 'post_action_link' ) ) ?>" class="<?php czr_fn_echo( 'post_action_link_class' ) ?> icn-expand"></a>
</div>