<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__post_list_' . tc_get( 'place_1' ) . '__' ) ?>
  <?php do_action( '__post_list_' . tc_get( 'place_2' ) . '__' ) ?>
</article>
<hr class="featurette-divider">
