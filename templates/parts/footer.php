<?php
/**
 * The template for displaying the site footer
 *
 */
?>
<?php do_action( '__before_footer' ) ?>
<footer id="footer" class="footer__wrapper" <?php czr_fn_echo('element_attributes') ?>>
  <?php
  do_action( '__before_inner_footer' );

  if ( czr_fn_is_registered_or_possible( 'footer_widgets' ) )
    czr_fn_render_template( 'footer/footer_widgets' );

  if ( czr_fn_is_registered_or_possible( 'footer_colophon' ) )
    czr_fn_render_template( 'footer/footer_colophon', array(
    	'model_args' => array(
    		'element_inner_class' => czr_fn_get_property( 'footer_clph_container_class' )
    	)
    ) );

  do_action( '__after_inner_footer' );
  ?>
</footer>
<?php do_action( '__after_footer' ) ?>
