<?php
/**
 * The template for displaying the footer of a post in a post list
 * In WP loop
 *
 * @package Customizr
 */
?>
<footer class="entry-footer row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-md-6 col-xs-12">
    <?php czr_fn_render_template( 'content/authors/author_info_small' ) ?>
  </div>
  <div class="col-md-6 col-xs-12">
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