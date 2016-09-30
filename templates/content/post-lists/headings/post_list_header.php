<?php
/**
 * The template for displaying the header of a post in a post list
 * In WP loop
 *
 * @package Customizr
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo( 'entry_title_class' ) ?>">
  <?php /* Maybe treat this case with CSS only */
    if ( czr_fn_get( 'has_header_format_icon' ) ): ?>
      <div class="tc-grid-icon"><i class="format-icon"></i></div>
  <?php endif; ?>
  <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>
    <div class="entry-meta">
      <?php czr_fn_echo( 'cat_list', 'post_metas' ) ?>
    </div>
  <?php endif; ?>
    <h2 class="entry-title ">
      <a class="czr-title" href="<?php the_permalink() ?>" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" rel="bookmark"><?php the_title() ?></a>
    </h2>
    <?php
      if ( ( ! CZR() -> czr_fn_is_customizing() && get_edit_post_link() ) ) : ?>
        <a class="post-edit-link btn-edit" title="__( 'Edit', 'customizr' )" href="<?php echo get_edit_post_link() ?>"><i class="icn-edit"></i><?php _e( 'Edit post', 'customizr' ) ?></a>
    <?php endif ?>
  </div>
</header>