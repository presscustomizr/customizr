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
  <?php
  if ( czr_fn_has('edit_button') && (bool) $edit_comment_link = get_edit_comment_link() )
    czr_fn_render_template(
      'modules/edit_button', 'edit_button',
      array( 'model_args' => array(
          'edit_button_class' => 'comment-edit-link',
          'edit_button_link'  => $edit_comment_link,
        )
      )
    );

