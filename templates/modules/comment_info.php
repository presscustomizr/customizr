<?php
/**
 * The template for displaying the comment info
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<a class="comments__link" href="<?php czr_fn_echo( 'comment_info_link', null, array( get_permalink() ) ) ?>" title="<?php echo get_comments_number() ?> <?php _e( 'Comment(s) on', 'customizr') ?> <?php echo esc_attr( strip_tags( get_the_title() ) ) ?>" data-disqus-identifier="javascript:this.page.identifier">
  <span>  
    <?php echo number_format_i18n( get_comments_number() ) . ' ' . _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ) ?>
  </span>
</a>