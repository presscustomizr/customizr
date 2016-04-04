<?php
/**
 * The template for displaying the article wrapper in a post list context
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__post_list_' . tc_get( 'place_1' ) . '__' ) ?>
  <?php do_action( '__post_list_' . tc_get( 'place_2' ) . '__' ) ?>
</article>
<hr class="featurette-divider">
