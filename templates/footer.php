<?php
/**
 * The template for displaying the site footer
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<footer id="footer" <?php tc_echo('element_attributes') ?>>
  <?php
  if ( tc_has( 'footer_widgets_wrapper' ) )
    tc_render_template( 'footer/footer_widgets', 'footer_widgets_wrapper' );
  tc_render_template( 'footer/colophon' );
  ?>
</footer>
