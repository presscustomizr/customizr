<?php 
  foreach ( $featured_pages_model -> featured_pages as $index => $featured_page ) {
    do_action( "in_featured_pages_{$featured_pages_model -> id}", $index, $featured_page );
    do_action( "__featured_page__" );
  }
?>
<hr class="featurette-divider <?php echo $featured_pages_model -> hook ?>">
