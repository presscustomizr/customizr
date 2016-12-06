<?php
/**
 * The template for displaying the footer credits
 *
 */
?>
<p class="copyright" <?php czr_fn_echo('element_attributes') ?>>
  <span class="tc-copyright-text">&copy; <?php echo esc_attr( date('Y') ) ?></span> <a href="<?php echo esc_url( home_url() ) ?>" title="<?php echo esc_attr( get_bloginfo() ) ?>"><?php echo esc_attr( get_bloginfo() ) ?></a>
  &ndash; <?php _e( 'All rights reserved', 'customizr' ) ?>
</p>