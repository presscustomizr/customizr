<?php
/**
 * The template for displaying the article wrapper:
 * used for articles singulars (post/page/attachment)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__article__' ) ?>
</article>
