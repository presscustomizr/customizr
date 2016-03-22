<section class="tc-grid-post">
  <figure class="tc-grid-figure <?php echo $grid_item_model -> figure_class ?>">
    <?php if ( $grid_item_model -> has_icon ): ?>
      <div class="tc-grid-icon format-icon" <?php echo $grid_item_model -> icon_attributes ?>></div>
    <?php endif ?>
    <?php echo $grid_item_model -> thumb_img ?>
    <?php do_action( '__comment_bubble__' ) ?>
    <figcaption class="tc-grid-excerpt">
      <div class="entry-summary">
        <div class="tc-g-cont"><?php the_excerpt() ?></div>
        <?php if( $grid_item_model -> is_expanded ): ?>
        <h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php echo $grid_item_model -> title ?></a></h2>
        <?php endif ?>
      </div>
      <a class="tc-grid-bg-link" href="<?php the_permalink() ?>" title="<?php esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
      <?php if( $grid_item_model -> is_expanded ): ?>
      <span class="tc-grid-fade_expt"></span>
      <?php endif ?>
    </figcaption>
  </figure>
<?php if( ! $grid_item_model -> is_expanded ) : ?>
  <header class="entry-header">
    <h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php echo $grid_item_model -> title ?></a></h2>
    <?php do_action('__post_metas__') ?>
  </header>
<?php endif ?>
</section>
