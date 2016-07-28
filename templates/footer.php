<?php
/**
 * The template for displaying the site footer
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<footer id="footer" class="container-fluid footer__wrapper">
  <?php
  do_action( '__before_inner_footer' );

    czr_fn_render_template( 'footer/footer_widgets');
    czr_fn_render_template( 'footer/colophon' );
    
  do_action( '__after_inner_footer' );
  ?>
</footer>
<?php czr_fn_render_template( 'footer/btt_arrow' ); ?>

