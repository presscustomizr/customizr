<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php global $content_class ?>

<div class="<?php echo $content_class; ?>">
	
	<?php get_template_part( 'parts/post-header'); ?>
	
	<?php //bubble color computation
	    $nbr = get_comments_number();
	    $style = ($nbr == 0) ? 'style="color:#AFAFAF" ':'';
 	?>
 	
		<?php if(is_single() || is_page())
	        echo '<hr class="featurette-divider">';
	        ?>
		<?php if ( !is_single()) : //for lists of posts?> 
			<?php //display an icon for div if there is no title
					$icon_class = in_array(get_post_format(), array(  'quote', 'aside', 'status', 'link')) ? 'format-icon':'';
				?>
			<?php if (!get_post_format()) :  // Only display Excerpts for lists of posts with format different than quote, status, link, aside ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
			
			<?php elseif(in_array(get_post_format(), array( 'image','gallery'))) : ?>
				<div class="entry-content">
					<p class="format-icon"></p>
				</div><!-- .entry-content -->
			
			<?php else : ?>
			
				<div class="entry-content <?php echo $icon_class ?>">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'customizr' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:', 'customizr' ), 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->
			<?php endif; //!is_single() ?>

		<?php else : ?>
			
				<div class="entry-content">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'customizr' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:', 'customizr' ), 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->

		<?php endif; ?>
	
	<?php get_template_part( 'parts/post-footer'); ?>
		
</div>
