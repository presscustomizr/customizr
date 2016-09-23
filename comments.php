<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to tc_comment_callback()
 */
if ( czr_fn_has('comment_list') ) { czr_fn_render_template('content/comments/comment_list', 'comment_list'); }
comment_form();