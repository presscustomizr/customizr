<?php
/**
 * The sidebar containing the left widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php if ( is_active_sidebar( 'left' ) ) : ?>

	<div id="left" class="widget-area" role="complementary">

		<?php do_action( 'tc_top_left_sidebar' ); ?>

		<aside class="social-block widget widget_social">

			<?php do_action( '__social' , 'tc_social_in_left-sidebar' ); ?>

		</aside>

		<?php dynamic_sidebar( 'left' ); ?>

		<?php do_action( 'tc_bottom_left_sidebar' ); ?>

	</div><!-- #secondary -->
	
<?php endif; ?>