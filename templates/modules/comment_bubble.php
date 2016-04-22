<?php
/**
 * The template for displaying the comment bubble
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
/* At the moment the switch between the two layouts is handled in the model. Consider a split in the future */

/* Case default bubble layout */
if ( 'default' == tc_get( 'type' ) ) :

?>
<span class="comments-link" <?php tc_echo('element_attributes') ?>>
  <a href="<?php tc_echo( 'comment_bubble_link', null, array( get_permalink() ) ) ?>" title="<?php echo get_comments_number() ?> <?php _e( 'Comment(s) on', 'customizr') ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" data-disqus-identifier="javascript:this.page.identifier"><span class="tc-comment-bubble default-bubble"><?php echo number_format_i18n( get_comments_number() )?></span></a>
</span>
<?php

elseif ( 'custom-bubble-one' == tc_get('type') ):

?>
<span class="comments-link" <?php tc_echo('element_attributes') ?>>
  <a href="<?php tc_echo( 'comment_bubble_link', null, array( get_permalink() ) ) ?>" title="<?php echo get_comments_number() ?> <?php _e( 'Comment(s) on', 'customizr') ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" data-disqus-identifier="javascript:this.page.identifier"><span class="tc-comment-bubble custom-bubble-one"><?php echo number_format_i18n( get_comments_number() ) . ' ' . _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ) ?></span></a>
</span>
<?php

endif;
