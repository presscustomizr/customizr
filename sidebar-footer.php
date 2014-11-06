<?php
/**
 * The sidebar containing the footer widget areas.
 *
 * If no active widgets they will be hidden completely.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>

<?php
if ( !is_active_sidebar( 'footer_one' ) && !is_active_sidebar( 'footer_two') && !is_active_sidebar( 'footer_three'))
	return;
?>
<div class="container footer-widgets">
	<div class="row widget-area" role="complementary">
		<?php if ( is_active_sidebar( 'footer_one' ) ) : ?>
		<div class="span4">
			<?php dynamic_sidebar( 'footer_one' ); ?>
		</div>
		<?php endif; ?>

		<?php if ( is_active_sidebar( 'footer_two' ) ) : ?>
		<div class="span4">
			<?php dynamic_sidebar( 'footer_two' ); ?>
		</div>
		<?php endif; ?>
		
		<?php if ( is_active_sidebar( 'footer_three' ) ) : ?>
		<div class="span4">
			<?php dynamic_sidebar( 'footer_three' ); ?>
		</div>
		<?php endif; ?>
	</div><!-- .row widget-area -->
</div><!--.footer-widgets -->