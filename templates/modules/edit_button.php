<?php
/**
 * The template for displaying the edit button
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Case post/page/attachment */
if ( 'post' == tc_get( 'context' ) ) :

?>
<span class="edit-link btn btn-inverse btn-mini" <?php tc_echo('element_attributes') ?>><a class="post-edit-link" href="<?php echo get_edit_post_link() ?>" title="<?php _e( 'Edit' , 'customizr' ) ?>"><?php _e( 'Edit' , 'customizr' ) ?></a></span>
<?php


/* Case slide, the single slide inside the slider */
elseif ( 'slide' == tc_get( 'context' ) ) :

?>
<span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="<?php echo get_edit_post_link( tc_get('edit_post_id') ) . tc_get( 'edit_link_suffix' ) ?>" title="<?php _e( 'Edit', 'customizr' ) ?>" target="_blank"><?php _e( 'Edit', 'customizr') ?></a></span>
<?php
endif;


