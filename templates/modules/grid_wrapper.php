<?php if ( tc_get( 'is_loop_start' ) ) : ?>
<section class="tc-post-list-grid <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
<?php endif; ?>
  <?php if ( tc_get( 'is_first_of_row' ) ) : ?>
  <section class="row-fluid grid-cols-<?php tc_echo( 'section_cols' ) ?>">
  <?php endif ?>
    <article <?php tc_echo( 'article_selectors' ) ?> >
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

