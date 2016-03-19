<?php if ( $grid_wrapper_model -> is_first_of_row ) : ?>
<section class="row-fluid grid-cols-<?php echo $grid_wrapper_model -> section_cols ?>">
<?php endif ?>
  <article <?php echo $grid_wrapper_model -> article_selectors ?> >
    <?php do_action( "__grid__" ) ?>
  </article>
<?php if ( $grid_wrapper_model -> is_last_of_row ) : ?>
</section>
<hr class="featurette-divider post-list-grid">
<?php endif ?>
