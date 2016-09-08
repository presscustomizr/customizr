<?php
/**
 * The template for displaying the header of a post in a post list
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="entry-header <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>> 
<?php /* Mayabe treat this case with CSS only */
  if ( czr_fn_get( 'has_header_format_icon' ) ): ?>
  <div class="tc-grid-icon"><i class="format-icon"></i></div>
<?php endif; ?>  
<?php if ( czr_fn_has('post_metas') && czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>
  <div class="entry-meta">
    <?php czr_fn_echo( 'cat_list', 'post_metas' ) ?>
  </div>
<?php endif; ?>  
  <h2 class="entry-title">
    <a href="<?php the_permalink() ?>" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  </h2>
</header>