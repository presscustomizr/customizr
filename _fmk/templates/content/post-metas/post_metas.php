<?php
/**
 * The template for displaying the post metas block ( button and text version )
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="entry-meta" <?php czr_fn_echo('element_attributes') ?>>
  <?php _e('This entry was posted', 'customizr') ?>
  <?php if ( czr_fn_get( 'cat_list' ) ) : ?>
   <span class="w-cat"><?php _e( 'in', 'customizr' ) ?> <?php czr_fn_echo( 'cat_list' ) ?></span>
  <?php endif; ?>
  <?php if ( czr_fn_get( 'tag_list' ) ) : ?>
   <span class="w-tags"><?php _e( 'tagged', 'customizr' ) ?> <?php czr_fn_echo( 'tag_list' ) ?></span>
  <?php endif; ?>
  <?php if ( czr_fn_get( 'publication_date' ) ) : ?>
   <span class="pub-date"><?php _e( 'on', 'customizr' ) ?> <?php czr_fn_echo( 'publication_date' ) ?></span>
  <?php endif; ?>
  <?php if ( czr_fn_get( 'author' ) ) : ?>
   <span class="by-author"><?php _e( 'by', 'customizr' ) ?> <?php czr_fn_echo( 'author' ) ?></span>
  <?php endif; ?>
  <?php if ( czr_fn_get( 'update_date' ) ) :
  // update_date params
  // 1) text for "today"
  // 2) text for "1 day ago"
  // 3) text for "more than 1 day ago"
  // accept %s as placeholder
  // used when update date shown in days option selected
  ?>
   <span class="up-date">(<?php _e( 'updated', 'customizr') ?>: <?php czr_fn_echo( 'update_date' , null, array (
        __('today', 'customizr') ,
        __('1 day ago', 'customizr'),
        __('%s days ago', 'customizr')
     ) ) ?>)</span>
  <?php endif ?>
  <?php if ( ! is_singular() && ! czr_fn_post_has_title() ): ?>
   | <a href="<?php the_permalink() ?>" title="<?php _e('Open', 'customizr') ?>"><?php _e('Open', 'customizr') ?> &raquo;</a>
  <?php endif ?>
</div>
