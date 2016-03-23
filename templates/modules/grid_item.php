<section class="tc-grid-post">
  <figure class="tc-grid-figure <?php tc_echo( 'figure_class' ) ?>">
    <?php if ( tc_get( 'has_icon' ) ): ?>
      <div class="tc-grid-icon format-icon" <?php tc_echo( 'icon_attributes' ) ?>></div>
    <?php endif ?>
    <?php tc_echo( 'thumb_img' ) ?>
    <?php do_action( '__comment_bubble__' ) ?>
    <figcaption class="tc-grid-excerpt">
      <div class="entry-summary">
        <div class="tc-g-cont"><?php the_excerpt() ?></div>
        <?php if( tc_get( 'is_expanded' ) ): ?>
        <h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php tc_echo( 'title' ) ?></a></h2>
        <?php endif ?>
      </div>
      <a class="tc-grid-bg-link" href="<?php the_permalink() ?>" title="<?php esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
      <?php if( tc_get( 'is_expanded' ) ): ?>
      <span class="tc-grid-fade_expt"></span>
      <?php endif ?>
    </figcaption>
  </figure>
<?php if( ! tc_get( 'is_expanded' ) ) : ?>
  <header class="entry-header">
  <?php if ( tc_post_has_title() ) : ?>
    <h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php tc_echo( 'title' ) ?></a></h2>
  <?php endif ?>
    <?php do_action('__post_metas__') ?>
  </header>
<?php endif ?>
</section>
