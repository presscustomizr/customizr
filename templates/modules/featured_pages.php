<div class="container marketing" <?php tc_echo('element_attributes') ?>>
  <?php
    do_action( 'before_fp' );
    foreach ( tc_get( 'featured_pages' ) as $index => $featured_page ) {
      do_action( 'in_featured_pages_' . tc_get( 'id' ), $index, $featured_page );
      do_action( '__featured_page__' );
    }
    do_action( 'after_fp' );
  ?>
</div>
<hr class="featurette-divider <?php tc_echo( 'hook' ) ?>">
