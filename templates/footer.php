<?php
/**
 * The template for displaying the site footer
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<footer id="footer" <?php czr_fn_echo('element_attributes') ?>>
  <?php
  do_action( '__before_inner_footer' );
  if ( czr_fn_has( 'footer_widgets_wrapper' ) )
    czr_fn_render_template( 'footer/footer_widgets', 'footer_widgets_wrapper' );
  czr_fn_render_template( 'footer/colophon' );
  do_action( '__after_inner_footer' );
  ?>
</footer>
