<?php
/**
 * The template for displaying the site footer
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<footer id="footer" <?php tc_echo('element_attributes') ?>>
  <?php
  do_action( '__before_inner_footer' );
  if ( tc_has( 'footer_widgets_wrapper' ) )
    tc_render_template( 'footer/footer_widgets', 'footer_widgets_wrapper' );
  tc_render_template( 'footer/colophon' );
  do_action( '__after_inner_footer' );
  ?>
</footer>
