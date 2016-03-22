<?php 
  foreach ( tc_get( 'featured_pages' ) as $index => $featured_page ) {
    do_action( 'in_featured_pages_' . tc_get( 'id' ), $index, $featured_page );
    do_action( '__featured_page__' );
  }
?>
<hr class="featurette-divider <?php echo tc_get( 'hook' ) ?>">
