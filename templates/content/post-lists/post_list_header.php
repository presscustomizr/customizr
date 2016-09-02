<?php
/**
 * The template for displaying the header of a post in a post list
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="entry-header" <?php czr_fn_echo('element_attributes') ?>>    
<?php if ( czr_fn_has('post_metas') && czr_fn_get( 'cat_list', 'post_metas' ) ) : ?>
  <div class="entry-meta">
    <?php czr_fn_echo( 'cat_list', 'post_metas' ) ?>
  </div>
<?php endif; ?>  
  <h2 class="entry-title">
    <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  </h2>
</header>