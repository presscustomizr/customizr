<?php
/**
 * The template for displaying the post/page/attachment titles in singular context
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<h1 class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__before_inner_post_page_title__' ) ?>
  <?php the_title() ?>
  <?php do_action( '__after_inner_post_page_title__' ) ?>
</h1>
