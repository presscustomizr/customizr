<?php
/**
 * The template for displaying the featured pages wrapper
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="container marketing" <?php tc_echo('element_attributes') ?>>
  <?php
    do_action( '__before_fp' );
    foreach ( tc_get( 'featured_pages' ) as $index => $featured_page ) {
      do_action( 'in_featured_pages_' . tc_get( 'id' ), $index, $featured_page );
      if ( tc_has( 'featured_page' ) )
        tc_render_template( 'modules/featured-pages/featured_page', 'featured_page' );
    }
    do_action( '__after_fp' );
  ?>
</div>
<hr class="featurette-divider">
