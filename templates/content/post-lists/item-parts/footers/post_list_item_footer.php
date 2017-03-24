<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
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
    <div class="post-info clearfix">
    <?php
      if ( czr_fn_has('post_metas') && $author = czr_fn_get( 'author', 'post_metas' ) )
        echo $author;
      if ( czr_fn_has('post_metas') && $date = czr_fn_get( 'publication_date', 'post_metas' ) )
        if ( $author ) : ?><span class="v-separator">|</span><?php endif; echo $date;

      if ( czr_fn_get('show_comment_meta') ) :
        if ( $author || $date ) : ?><span class="v-separator">|</span><?php endif;
        czr_fn_render_template( 'modules/common/comment_info' );
      endif
    ?>
    </div>
  </div>
</footer>