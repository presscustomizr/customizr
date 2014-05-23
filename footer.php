<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
?>
		<?php tc__f('rec' , __FILE__ , __FUNCTION__ ); ?>
		
		<?php do_action( '__before_footer' ); ?>
		
			<!-- FOOTER -->
			<footer id="footer">
			 	<?php do_action( '__footer' ); // hook of footer widget and colophon?>
			</footer>

		<?php wp_footer(); //do not remove, used by the theme and many plugins?>

		<?php do_action( '__after_footer' ); ?>

	</body>

</html>
