<?php
/**
 * The template for displaying the grid item wrapper in lists of posts
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */

/* Are we at the start of the loop? in this case print a section wrapper element */
if ( czr_fn_get( 'print_start_wrapper' ) ) :

?>
<div class="grid-container__classic <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="grid__wrapper grid row">
<?php

endif;

    czr_fn_render_template(
      'modules/grid/grid_item',
      array( 'model_args' => czr_fn_get( 'grid_item' ) )
    );


/* Close se section at the end of the loop */
if ( czr_fn_get( 'print_end_wrapper' ) ) : ?>
  </div>
</div>
<?php

endif;