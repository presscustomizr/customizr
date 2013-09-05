<?php
/**
 * The sidebar containing the right widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php if ( is_active_sidebar( 'right' ) ) : ?>

	<div id="right" class="widget-area" role="complementary">

		<?php do_action( 'tc_top_right_sidebar' ); ?>

		<aside class="social-block widget widget_social">

			<?php do_action( '__social' , 'tc_social_in_right-sidebar' ); ?>

		</aside>

		<?php dynamic_sidebar( 'right' ); ?>

		<?php do_action( 'tc_bottom_right_sidebar' ); ?>

	</div><!-- #secondary -->
	
<?php endif; ?>