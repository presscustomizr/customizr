<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
if ( apply_filters( 'czr_four_do', false ) ) {
  do_action( 'czr_four_template', 'footer' );
  return;
}

  	do_action( '__before_footer' ); ?>
  		<!-- FOOTER -->
  		<footer id="footer" class="<?php echo czr_fn__f('tc_footer_classes', '') ?>">
  		 	<?php do_action( '__footer' ); // hook of footer widget and colophon?>
  		</footer>
    </div><!-- //#tc-page-wrapper -->
		<?php
    do_action( '__after_page_wrap' );
		wp_footer(); //do not remove, used by the theme and many plugins
	  do_action( '__after_footer' ); ?>
	</body>
	<?php do_action( '__after_body' ); ?>
</html>