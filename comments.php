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

//if protected we don't display comments before password
if ( post_password_required() )
	return;

//if displaying pages, we check if the customizer option is on
if ( is_page() && 1 != esc_attr( tc__f( '__get_option' , 'tc_page_comments' ) ) )
   return;

?>
<?php tc__f('rec' , __FILE__ , __FUNCTION__ ); ?>

<?php if ( have_comments() ) : ?>

	<hr class="featurette-divider">

<?php endif; ?>

<div id="comments" class="comments-area">
	
	<?php 
	$args = array(
	  'title_reply'       => __( 'Leave a Comment' , 'customizr' ),
	);

	comment_form($args); 

	?>

	<?php if ( have_comments() ) : ?>

		<?php do_action ( '__comment' );?>

	<?php endif; // have_comments() ?>

</div><!-- #comments .comments-area -->