<?php
/**
 * The template part for displaying error 404 page content
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>

<?php global $content_class ?>

<div class="<?php echo $content_class; ?> format-quote">
	<div class="entry-content format-icon">
	 	<blockquote><p><?php _e('Speaking the Truth in times of universal deceit is a revolutionary act.','customizr') ?></p>
	 	<cite><?php _e('George Orwell','customizr') ?></cite>
	 	</blockquote>
		<p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.', 'customizr' ); ?></p>
		<?php get_search_form(); ?>
	</div>
	<hr class="featurette-divider">
</div><!--content -->
