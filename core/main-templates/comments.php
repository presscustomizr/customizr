<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to tc_comment_callback()
 */
?>
<div id="comments" class="comments_container">
  <section class="post-comments">
    <?php
      if ( czr_fn_has('comment_list') ) {
        czr_fn_render_template( 'content/comments/comment_list' );
      }
      comment_form( array(
        'class_form'         => 'czr-form comment-form',
        'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h4>',
        'title_reply'        => __( 'Leave a comment' , 'customizr' )
      ));
  ?>
  </section>
</div>