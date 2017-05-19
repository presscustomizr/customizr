<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer post-info" <?php czr_fn_echo('element_attributes') ?>>
<?php

  if ( czr_fn_has('post_metas') ) :
?>
  <div class="entry-meta row justify-content-between align-items-center">
    <div class="col-md-auto col-12">
<?php
    if ( czr_fn_get( 'author', 'post_metas' ) )
      czr_fn_render_template( 'content/post-lists/item-parts/authors/author_info_small' )
?>
  </div>
    <div class="col-md-auto col-12">
<?php

    if ( $date = czr_fn_get( 'publication_date', 'post_metas', array( 'permalink' => true ) ) )
      echo $date;

    if ( $up_date = czr_fn_get( 'update_date', 'post_metas', array( 'permalink' => true ) ) )
      if ( $date ) : ?><span class="v-separator">-</span><?php endif; echo $up_date;
?>
    </div>
  </div>
<?php
  endif;

  if ( czr_fn_get('show_comment_meta') ) :
?>
  <div class="row justify-content-end">
    <div class="col col-auto">
      <?php czr_fn_comment_info(); ?>
    </div>
  <div>
<?php
  endif;
?>
</footer>