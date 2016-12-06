<?php
/**
 * The template for displaying the single post content
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
      <footer class="post-footer container-fluid clearfix">
        <?php
          wp_link_pages( array(
            'before'        => '<div class="post-pagination pagination row"><div class="col-md-12">',
            'after'         => '</div></div>',
            'link_before'   => '<span>',
            'link_after'    => '</span>',
            )
          );
        ?>
        <div class="entry-meta row clearfix">
          <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
          <div class="post-tags float-md-left">
            <ul class="tags">
              <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
            </ul>
          </div>
          <?php endif; ?>
        <?php
          if ( czr_fn_has('social_share') ) :
        ?>
          <div class="post-share float-md-right">
              <!-- fake need to have social links somewhere -->
              <?php czr_fn_render_template( 'modules/social_block', array( 'model_id' => 'social_share' ) ) ?>
          </div>
        <?php
          endif
        ?>
        </div>
      </footer>

      <?php do_action( '__after_inner_post_content' ) ?>
    </section><!-- .entry-content -->

    <?php do_action( '__after_post_entry_content' ) ?>

  </div><!-- .post-entry -->
  <?php do_action( '__after_inner_post_article' ) ?>
</article>