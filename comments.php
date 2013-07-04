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
if ( post_password_required() )
	return;
?>
<?php if ( have_comments() ) : ?>

	<hr class="featurette-divider">

<?php endif; ?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

		<?php 
			do_action ( '__comment_title' );

			do_action ( '__comment_list' );

			do_action ( '__comment_navigation' );
			
			do_action ( '__comment_close' );
		?>

	<?php endif; // have_comments() ?>

<?php comment_form(); ?>

</div><!-- #comments .comments-area -->