<?php if ( $grid_wrapper_model -> is_loop_start ) : ?>
<section class="tc-post-list-grid <?php echo $grid_wrapper_model -> element_class ?>">
<?php endif; ?>
  <?php if ( $grid_wrapper_model -> is_first_of_row ) : ?>
  <section class="row-fluid grid-cols-<?php echo $grid_wrapper_model -> section_cols ?>">
  <?php endif ?>
    <article <?php echo $grid_wrapper_model -> article_selectors ?> >
      <section class="tc-grid-post">
        <figure class="tc-grid-figure <?php echo $grid_wrapper_model -> figure_class ?>">
          <?php do_action( "__grid_thumb__" ) ?>
          <?php do_action( "__grid_caption__" ) ?>
        </figure>
        <?php if ( ! $grid_wrapper_model -> has_heading_in_caption ) :?>
          <?php do_action( "__grid_heading__" ) ?>
        <?php endif; ?>
     </section>
    </article>
    <hr class="featurette-divider __after_article">
  <?php if ( $grid_wrapper_model -> is_last_of_row ) : ?>
  </section>
  <hr class="featurette-divider post-list-grid">
  <?php endif ?>
<?php if ( $grid_wrapper_model -> is_loop_end ) : ?>
</section>
<?php endif ?>

