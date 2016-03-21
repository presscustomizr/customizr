<section class="tc-grid-post">
  <figure class="tc-grid-figure <?php echo $grid_item_model -> figure_class ?>">
    <?php if ( $grid_item_model -> has_icon ): ?>
      <div class="tc-grid-icon format-icon" <?php echo $grid_item_model -> icon_attributes ?>></div>
    <?php endif ?>
    <?php echo $grid_item_model -> thumb_img ?>
    <?php do_action( '__comment_bubble__' );?>
    <figcaption class="tc-grid-excerpt">
      <div class="entry-summary">
        <div class="tc-g-cont"><?php the_excerpt() ?></div>
        <?php do_action( '__grid_post_title__' );?>
      </div>
      <a class="tc-grid-bg-link" href="<?php the_permalink() ?>" title="<?php esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
    </figcaption>
  </figure>
</section>
