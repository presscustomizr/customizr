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
if ( czr_get( 'is_loop_start' ) ) :

?>
<section class="tc-post-list-grid <?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
<?php

endif;

?>
  <?php

  /* Is the item we're about to display the first of it's row? In this case print a row wrapper */
  if ( czr_get( 'is_first_of_row' ) ) :

  ?>
  <section class="row-fluid grid-cols-<?php czr_echo( 'section_cols' ) ?>">
  <?php

  endif

   ?>
    <article <?php czr_echo( 'article_selectors' ) ?> >
      <?php if ( czr_has( 'grid_item' ) ) czr_render_template( 'modules/grid/grid_item', 'grid_item' ); ?>
    </article>
    <hr class="featurette-divider __after_article">
  <?php

  /* close the row if the displayed item is the last of row */
  if ( czr_get( 'is_last_of_row' ) ) :

  ?>
  </section>
  <hr class="featurette-divider post-list-grid">
  <?php

  endif;

/* Close se section at the end of the loop */
if ( czr_get( 'is_loop_end' ) ) : ?>
</section>
<?php

endif;
