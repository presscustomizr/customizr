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
if ( comments_open() || czr_has( 'comment_list' )  ) : ?>
  <hr class="featurette-divider">
<?php endif;
comment_form();
if ( czr_has('comment_list') ) { czr_render_template('content/comments/comment_list', 'comment_list'); }
