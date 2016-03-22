<?php _e('This entry was posted', 'customizr') ?>
<?php if ( tc_get( 'cat_list' ) ) : ?>
 <span class="w-cat"><?php _e( 'in', 'customizr' ) ?> <?php echo tc_get( 'cat_list()' ) ?></span>
<?php endif; ?>
<?php if ( tc_get( 'tag_list' ) ) : ?>
 <span class="w-tags"><?php _e( 'tagged', 'customizr' ) ?> <?php echo tc_get( 'tag_list' ) ?></span>
<?php endif; ?>
<?php if ( tc_get( 'publication_date' ) ) : ?>
 <span class="pub-date"><?php _e( 'on', 'customizr' ) ?> <?php echo tc_get( 'publication_date' ) ?></span>
<?php endif; ?>
<?php if ( tc_get( 'author' ) ) : ?>
 <span class="by-author"><?php _e( 'by', 'customizr' ) ?> <?php echo tc_get( 'author' ) ?></span>
<?php endif; ?>
<?php if ( tc_get( 'update_date' ) ) :
  // update_date params
  // 1) text for "today"
  // 2) text for "1 day ago"
  // 3) text for "more than 1 day ago"
  // accept %s as placeholder
  // used when update date shown in days option selected
?> 
 <span class="up-date">(<?php _e( 'updated', 'customizr') ?>: <?php echo tc_get( 'update_date' , array ( 
      __('today', 'customizr') , 
      __('1 day ago', 'customizr'), 
      __('%s days ago', 'customizr') 
   ) ) ?>)</span>
<?php endif; ?>
