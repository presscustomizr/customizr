<?php
/**
 * The template for displaying the post metas block (either the buttons and the textual version)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="entry-meta" <?php tc_echo('element_attributes') ?>>
<?php

  /* Case we're displaying the textual version */
  if ( 'post_metas_text' == tc_get('type') ) :

?>
  <?php if ( tc_get( 'publication_date' ) ) : ?>
  <span class="pub-date"><?php tc_echo( 'publication_date')  ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'cat_list' ) ) : ?>
   <span class="w-cat"><?php _e( 'in', 'customizr' ) ?> <?php tc_echo( 'cat_list', array('/') ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'tag_list' ) ): ?>
   <span class="w-tags"><?php _e( 'tagged', 'customizr' ) ?> <?php tc_echo( 'tag_list', array('/') ) ?></span>
  <?php endif; ?>

  <?php if ( tc_get( 'author' ) ) : ?>
   <span class="by-author"><?php _e( 'by', 'customizr' ) ?> <?php tc_echo( 'author' ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'update_date' ) ):
  // tc_get_update_date params
  // 1) text for "today"
  // 2) text for "1 day ago"
  // 3) text for "more than 1 day ago"
  // accept %s as placeholder
  // used when update date shown in days option selected
  ?>
  <span class="up-date">(<?php _e( 'updated', 'customizr') ?>: <?php tc_echo( 'update_date', array (
      __('today', 'customizr'),
      __('1 day ago', 'customizr'),
      __('%s days ago', 'customizr')
    ) ); ?>)</span>
  <?php endif ?>
<?php

  /* Case we're displaying the standard (with buttons) version */
  else :

?>
  <?php _e('This entry was posted', 'customizr') ?>
  <?php if ( tc_get( 'cat_list' ) ) : ?>
   <span class="w-cat"><?php _e( 'in', 'customizr' ) ?> <?php tc_echo( 'cat_list' ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'tag_list' ) ) : ?>
   <span class="w-tags"><?php _e( 'tagged', 'customizr' ) ?> <?php tc_echo( 'tag_list' ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'publication_date' ) ) : ?>
   <span class="pub-date"><?php _e( 'on', 'customizr' ) ?> <?php tc_echo( 'publication_date' ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'author' ) ) : ?>
   <span class="by-author"><?php _e( 'by', 'customizr' ) ?> <?php tc_echo( 'author' ) ?></span>
  <?php endif; ?>
  <?php if ( tc_get( 'update_date' ) ) :
  // update_date params
  // 1) text for "today"
  // 2) text for "1 day ago"
  // 3) text for "more than 1 day ago"
  // accept %s as placeholder
  // used when update date shown in days option selected
  ?>
   <span class="up-date">(<?php _e( 'updated', 'customizr') ?>: <?php tc_echo( 'update_date' , array (
        __('today', 'customizr') ,
        __('1 day ago', 'customizr'),
        __('%s days ago', 'customizr')
     ) ) ?>)</span>
  <?php endif ?>
<?php
  /* end switch on the post metas layout */
  endif;
?>
  <?php if ( ! is_singular() && ! tc_post_has_title() ): ?>
   | <a href="<?php the_permalink() ?>" title="<?php _e('Open', 'customizr') ?>"><?php _e('Open', 'customizr') ?> &raquo;</a>
  <?php endif ?>
</div>
