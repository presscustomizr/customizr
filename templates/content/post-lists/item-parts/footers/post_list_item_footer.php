<?php
/**
 * The template for displaying the footer of a post in a post list
 * In CZR loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
    <div class="post-tags entry-meta">
      <ul class="tags">
        <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
      </ul>
    </div>
  <?php endif; ?>
    <div class="post-info clearfix">
    <?php

      if ( $has_meta = czr_fn_has('post_metas') ) {
    ?>
      <span class="entry-meta">
    <?php
        if ( $author = czr_fn_get( 'author', 'post_metas' ) )
          echo $author;

        if ( $date = czr_fn_get( 'publication_date', 'post_metas', array( 'permalink' => true ) ) )
          if ( $author ) : ?><span class="v-separator">|</span><?php endif; echo $date;

        if ( $up_date = czr_fn_get( 'update_date', 'post_metas', array( 'permalink' => true ) ) )
          if ( $date ) : ?><span class="v-separator">-</span><?php endif; echo $up_date;
    ?>
      </span>
    <?php
      }

      if ( czr_fn_get('show_comment_meta') ) :
        czr_fn_comment_info( $has_meta ? '<span class="v-separator">|</span>' : '' );
      endif
    ?>
  </div>
</footer>