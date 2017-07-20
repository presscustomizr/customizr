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
  <a href="<?php echo esc_url( get_comment_author_url() ) ?>"><sup><?php czr_fn_echo('ping_number') ?></sup><span class="excerpt"><?php comment_excerpt() ?></span> - <?php comment_author() ?></a>
  <?php
  if ( (bool) $edit_comment_link = get_edit_comment_link() ) {
    if ( (bool) $edit_post_link = get_edit_post_link() ) { czr_fn_edit_button( array( 'class' => 'comment-edit-link', 'link'  => $edit_comment_link ) ); }
  }

