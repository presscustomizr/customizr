<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to tc_comment_callback()
 *
 * @package Customizr
 * @since Customizr 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( have_comments() )
	echo apply_filters( 'tc_comment_separator', '<hr class="featurette-divider '. current_filter() .'">' );
?>

<div id="comments" class="<?php echo apply_filters( 'tc_comments_wrapper_class' , 'comments-area' ) ?>" >
	<?php 
		comment_form();
		if ( have_comments() )
			do_action ( '__comment' );
	?>
</div><!-- #comments .comments-area -->