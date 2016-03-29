<?php
/**
 * The template for displaying the edit button
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Case post/page/attachment */
$text = __( 'Edit', 'customizr' );

if ( 'post' == tc_get( 'context' ) ) :

?>
  <span class="edit-link btn btn-inverse btn-mini" <?php tc_echo('element_attributes') ?>><a class="post-edit-link" href="<?php echo get_edit_post_link() ?>" title="<?php echo $text ?>"><?php echo $text ?></a></span>
<?php

/* Case slider, edit the slider */
elseif ( 'slider' == tc_get( 'context' ) ) :

  $text  = __( 'Customize or remove', 'customizr' ) . '&nbsp;';
  $text .= ( 'slider_of_posts' == tc_get( 'slider_edit_link_type' ) ) ? __( 'the posts slider', 'customizr' ) : __( 'this slider', 'customizr' );

?>
  <span class="slider deep-edit-link edit-link btn btn-inverse"><a class="slider-edit-link" href="<?php tc_echo( 'slider_edit_link') ?>" title="<?php echo $text ?>" target="_blank"><?php echo $text ?></a></span>
<?php

/* Case slide, the single slide inside the slider */
elseif ( 'slide' == tc_get( 'context' ) ) :

?>
  <span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="<?php echo get_edit_post_link( tc_get('edit_post_id') ) . tc_get( 'edit_link_suffix' ) ?>" title="<?php echo $text ?>" target="_blank"><?php echo $text ?></a></span>
<?php

endif;


