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
			<aside class="social-block widget widget_social">
				<?php echo tc_get_social('tc_social_in_right-sidebar') ?>
			</aside>
			<?php dynamic_sidebar( 'right' ); ?>
		</div><!-- #secondary -->
	<?php endif; ?>