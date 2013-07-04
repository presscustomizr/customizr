<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
?>
		 </div><!--/#main-wrapper"-->

		 <!-- FOOTER -->
		<footer id="footer">

		 	<?php 
				do_action( '__sidebar' , 'footer' );

		 		do_action( '__footer' );//display template, you can hook here
		 	?>
		 </footer>

		<?php wp_footer(); ?>

	</body>

</html>