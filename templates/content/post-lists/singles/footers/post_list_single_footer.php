<?php
/**
 * The template for displaying the footer of a post in a post list
 * In WP loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer" <?php czr_fn_echo('element_attributes') ?>>
  <div class="entry-meta">
  <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
    <div class="post-tags">
      <ul class="tags">
        <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
      </ul>
    </div>
  <?php endif; ?>
    <div class="post-info">
    <?php
      if ( czr_fn_has('post_metas') && $date = czr_fn_get( 'publication_date', 'post_metas' ) )
        echo $date;

      if ( czr_fn_is_possible( 'comment_info' ) ) :
        if ( $date ) : ?> <span class="v-separator">|</span> <?php endif;
        czr_fn_render_template( 'modules/comment_info' );
      endif
    ?>
    </div>
  </div>
</footer>