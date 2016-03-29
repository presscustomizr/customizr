<?php
/**
 * The template for displaying the edit post/page button link
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<span class="edit-link btn btn-inverse btn-mini" <?php tc_echo('element_attributes') ?>><a class="post-edit-link" href="<?php echo get_edit_post_link() ?>" title="<?php _e( 'Edit' , 'customizr' ) ?>"><?php _e( 'Edit' , 'customizr' ) ?></a></span>

