<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to tc_comment_callback()
 */

// Print once : plugins compatibility
if ( ! apply_filters( 'tc_render_comments_template', true ) )
  return;
?>
<div id="comments" class="comments_container comments czr-comments-block">
  <section class="post-comments">
    <?php
      comment_form( array(
        'class_form'         => 'czr-form comment-form',
        'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h4>',
        'title_reply'        => __( 'Leave a comment' , 'customizr' )
      ));
      if ( czr_fn_is_registered_or_possible('comment_list') ) {
        czr_fn_render_template( 'content/singular/comments/comment_list' );
      }
  ?>
  </section>
</div>