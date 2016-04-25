<?php
/**
 * The template for displaying the edit button
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

/* Case post/page/attachment
 * In WP loop
 */
$text = __( 'Edit', 'customizr' );

if ( 'post' == czr_fn_get( 'context' ) ) :

?>
  <span class="edit-link btn btn-inverse btn-mini" <?php czr_fn_echo('element_attributes') ?>><a class="post-edit-link" href="<?php echo get_edit_post_link() ?>" title="<?php echo $text ?>"><?php echo $text ?></a></span>
<?php

/* Case slider, edit the slider */
elseif ( 'slider' == czr_fn_get( 'context' ) ) :

  $text  = __( 'Customize or remove', 'customizr' ) . '&nbsp;';
  $text .= ( 'slider_of_posts' == czr_fn_get( 'slider_edit_link_type' ) ) ? __( 'the posts slider', 'customizr' ) : __( 'this slider', 'customizr' );

?>
  <span class="slider deep-edit-link edit-link btn btn-inverse"><a class="slider-edit-link" href="<?php czr_fn_echo( 'slider_edit_link') ?>" title="<?php echo $text ?>" target="_blank"><?php echo $text ?></a></span>
<?php

/* Case slide, the single slide inside the slider */
elseif ( 'slide' == czr_fn_get( 'context' ) ) :

?>
  <span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="<?php echo get_edit_post_link( czr_fn_get('edit_post_id') ) . czr_fn_get( 'edit_link_suffix' ) ?>" title="<?php echo $text ?>" target="_blank"><?php echo $text ?></a></span>
<?php

endif;


