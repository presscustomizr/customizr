<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer row entry-meta" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-md-6 col-12">
    <?php czr_fn_render_template( 'content/post-lists/item-parts/authors/author_info_small' ) ?>
  </div>
  <div class="col-md-6 col-12">
    <div class="post-info clearfix">
    <?php
      if ( czr_fn_has('post_metas') && $date = czr_fn_get( 'publication_date', 'post_metas' ) )
        echo $date;

      if ( czr_fn_get('show_comment_meta') ) :
        czr_fn_comment_info( $before = $date ? '<span class="v-separator">|</span>' : '' );
      endif
    ?>
    </div>
  </div>
</footer>