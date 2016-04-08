<?php
/**
 * The template for displaying the post/page/attachment titles the lists of posts (alternate layout)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<h2 class="<?php tc_echo( 'element_class') ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__before_inner_post_page_title__' ) ?>
  <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  <?php do_action( '__after_inner_post_page_title__' ) ?>
</h2>
