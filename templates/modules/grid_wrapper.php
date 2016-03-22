<?php if ( tc_get( 'is_loop_start' ) ) : ?>
<section class="tc-post-list-grid <?php echo tc_get( 'element_class' ) ?>">
<?php endif; ?>
  <?php if ( tc_get( 'is_first_of_row' ) ) : ?>
  <section class="row-fluid grid-cols-<?php echo tc_get( 'section_cols' ) ?>">
  <?php endif ?>
    <article <?php echo tc_get( 'article_selectors' ) ?> >
      <?php do_action('__grid__') ?>
    </article>
    <hr class="featurette-divider __after_article">
  <?php if ( tc_get( 'is_last_of_row' ) ) : ?>
  </section>
  <hr class="featurette-divider post-list-grid">
  <?php endif ?>
<?php if ( tc_get( 'is_loop_end' ) ) : ?>
</section>
<?php endif ?>

