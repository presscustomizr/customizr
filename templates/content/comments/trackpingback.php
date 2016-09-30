<?php
/**
 * The template for displaying the single track-pingback in the list of comments
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
/* Case we're displaying a track or ping back */
?>
<li <?php comment_class() ?> id="comment-<?php comment_ID() ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a href="<?php echo esc_url( get_comment_author_url() ) ?>"><sup><?php czr_fn_echo('ping_number') ?></sup><strong><?php comment_excerpt() ?></strong> - <?php comment_author() ?></a>
<?php if ( ! CZR() -> czr_fn_is_customizing() && get_edit_comment_link() ) : ?>
  <a class="comment-edit-link btn btn-edit" href="<?php echo esc_url( get_edit_comment_link( $comment ) ); ?>"><i class="icn-edit"></i><?php _e('Edit', 'customizr') ?></a>
<?php endif;
