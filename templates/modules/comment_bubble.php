<span class="comments-link">
 <a href="<?php echo $comment_bubble_model -> link ?>" title="<?php comments_number() ?> <?php _e( 'Comment(s) on', 'customizr') ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" data-disqus-identifier="javascript:this.page.identifier"><span class="tc-comment-bubble <?php echo $comment_bubble_model -> inner_class ?>"><?php echo $comment_bubble_model -> text ?></span></a>
</span>
