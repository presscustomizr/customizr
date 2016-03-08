<?php if ( $post_metas_text_model -> tc_get_publication_date() ) : ?>
<span class="pub-date"><?php echo $post_metas_text_model -> tc_get_publication_date(); ?></span>
<?php endif; ?>
<?php if ( $post_metas_text_model -> tc_get_cat_list() ) : ?>
 <span class="w-cat"><?php _e( 'in', 'customizr' ) ?> <?php echo $post_metas_text_model -> tc_get_cat_list('/'); ?></span>
<?php endif; ?>
<?php if ( $post_metas_text_model -> tc_get_tag_list() ) : ?>
 <span class="w-tags"><?php _e( 'tagged', 'customizr' ) ?> <?php echo $post_metas_text_model -> tc_get_tag_list('/'); ?></span>
<?php endif; ?>

<?php if ( $post_metas_text_model -> tc_get_author() ) : ?>
 <span class="by-author"><?php _e( 'by', 'customizr' ) ?> <?php echo $post_metas_text_model -> tc_get_author(); ?></span>
<?php endif; ?>
<?php if ( $post_metas_text_model -> tc_get_update_date() ) :
  // tc_get_update_date params
  // 1) text for "today"
  // 2) text for "1 day ago"
  // 3) text for "more than 1 day ago"
  // accept %s as placeholder
  // used when update date shown in days option selected
?> 
 <span class="up-date">(<?php _e( 'updated', 'customizr') ?>: <?php echo $post_metas_text_model -> tc_get_update_date( __('today', 'customizr'), __('1 day ago', 'customizr'), __('%s days ago', 'customizr') ); ?>)</span>
<?php endif; ?>
