<?php
/**
 * The template for displaying the single page content
 *
 * In WP loop
 */
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?> >
  <?php do_action( '__before_inner_post_article' ) ?>
  <div class="post-entry tc-content-inner">
    <?php do_action( '__before_post_entry_content' ) ?>

    <section class="post-content entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <?php do_action( '__before_inner_post_content' ) ?>
      <?php
      the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
      ?>
      <footer class="post-footer">
        <?php
          wp_link_pages( array(
            'before'        => '<div class="post-pagination pagination row"><div class="col-md-12">',
            'after'         => '</div></div>',
            'link_before'   => '<span>',
            'link_after'    => '</span>',
            )
          );
        ?>
        <div class="entry-meta row">
          <div class="post-share pull-md-right col-md-4 col-xs-12">
            <!-- fake need to have social links somewhere -->
            <?php
              if ( czr_fn_has('social_share') )
                czr_fn_render_template( 'modules/social_block', 'social_share' );
            ?>
          </div>
        </div>
      </footer>

      <?php do_action( '__after_inner_post_content' ) ?>
    </section><!-- .entry-content -->

    <?php do_action( '__after_post_entry_content' ) ?>

  </div><!-- .post-entry -->
  <?php do_action( '__after_inner_post_article' ) ?>
</article>