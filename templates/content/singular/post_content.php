<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 */
?>
<article <?php czr_fn_echo( 'article_selectors' ) ?> <?php czr_fn_echo( 'element_attributes' ) ?> >
  <?php do_action( '__before_inner_post_article' ) ?>
  <?php /* if ( czr_fn_has('post_thumbnail') && 'before_title' == czr_fn_get( 'thumbnail_position' ) ) czr_fn_render_template('content/singles/thumbnail_single', 'post_thumbnail'); ?>
  <?php if ( czr_fn_has('singular_headings') ) czr_fn_render_template('content/singles/singular_headings', 'singular_headings'); */ ?>
  <div class="post-entry">
    <?php do_action( '__before_post_entry_content' ) ?>

    <section class="post-content entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <?php do_action( '__before_inner_post_content' ) ?>
      <?php
      the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
      ?>
      <footer class="post-footer">
        <?php
          wp_link_pages( array(
            'before'        => '<div class="post-pagination row"><div class="col-md-12">',
            'after'         => '</div></div>',
            'link_before'   => '<span>',
            'link_after'    => '</span>',
            )
          );
        ?>
        <div class="entry-meta row">
          <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
          <div class="post-tags pull-md-left col-md-8">
            <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
          </div>
          <?php endif; ?>
          <div class="post-share pull-md-right col-md-4">
            <!-- fake need to have social links somewhere -->
            <ul class="socials">
              <li><a href="http://facebook.com/"><i class="fa fa-facebook"></i></a></li>
              <li><a href="http://linkedin.com/"><i class="fa fa-linkedin"></i></a></li>
              <li><a href="http://twitter.com/"><i class="fa fa-twitter"></i></a></li>
              <li><a href="http://plus.google.com/"><i class="fa fa-instagram"></i></a></li>
              <li><a href="http://plus.google.com/"><i class="fa fa-pinterest"></i></a></li>
              <li><a href="http://plus.google.com/"><i class="fa fa-google-plus"></i> </a></li>
            </ul>
          </div>
        </div>
      </footer>

      <?php do_action( '__after_inner_post_content' ) ?>
    </section><!-- .entry-content -->

    <?php do_action( '__after_post_entry_content' ) ?>

    <?php
    if ( czr_fn_has('single_author_info') ) {
       czr_fn_render_template('content/authors/author_info', 'single_author_info');
    }
    ?>
    </div><!-- .post-entry -->
  <?php do_action( '__after_inner_post_article' ) ?>
</article>