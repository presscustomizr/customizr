<?php
/**
 * The template for displaying the featured pages wrapper
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="container marketing" <?php czr_echo('element_attributes') ?>>
  <?php
    do_action( '__before_fp' );
    while ( czr_get( 'has_featured_page' ) ) {
      if ( czr_has( 'featured_page' ) )
        czr_render_template( 'modules/featured-pages/featured_page', 'featured_page' );
    }
    do_action( '__after_fp' );
  ?>
</div>
<hr class="featurette-divider">
