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
	 	<blockquote><p><?php _e('Success is the ability to go from one failure to another with no loss of enthusiasm...','customizr') ?></p>
	 	<cite><?php _e('Sir Winston Churchill','customizr') ?></cite>
	 	</blockquote>
		<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'customizr' ); ?></p>
		<?php get_search_form(); ?>
	</div>
	<hr class="featurette-divider">
</div><!--content -->
