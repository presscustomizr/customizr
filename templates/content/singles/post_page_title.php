<?php
/**
 * The template for displaying the post and page titles in singular and post lists contexts
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

/* Titles in list of posts (alternate layout) */
if ( 'post_list' == tc_get('context') ) :

?>
<h2 class="<?php tc_echo( 'element_class') ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__before_inner_post_page_title__' ) ?>
  <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  <?php do_action( '__after_inner_post_page_title__' ) ?>
</h2>
<?php

/* Titles in singular contexts */
else :

?>
<h1 class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__before_inner_post_page_title__' ) ?>
  <?php the_title() ?>
  <?php do_action( '__after_inner_post_page_title__' ) ?>
</h1>
<?php endif;
