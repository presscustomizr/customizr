<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 */
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?>>
  <?php do_action( '__before_content_inner' ) ?>
  <?php
  /* heading */
  czr_fn_render_template( 'content/singular/headings/regular_post_heading' );
  ?>
  <div class="post-entry tc-content-inner">
    <section class="post-content entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <?php  czr_post_format_part(); ?>
      <div class="czr-wp-the-content">
        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
      </div>
      <footer class="post-footer clearfix">
        <?php
          czr_fn_link_pages();
        ?>
        <div class="row entry-meta justify-content-between align-items-center">
          <?php if ( czr_fn_is_registered_or_possible('post_metas') && czr_fn_get_property( 'tag_list', 'post_metas' ) ) : ?>
          <div class="post-tags col-xs-12 col-sm-auto col-sm">
            <ul class="tags">
              <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
            </ul>
          </div>
          <?php endif; ?>
        <?php
          if ( czr_fn_is_registered_or_possible('social_share') ) :
        ?>
          <div class="post-share col-xs-12 col-sm-auto col-sm">
              <!-- fake need to have social links somewhere -->
              <?php czr_fn_render_template( 'modules/common/social_block', array( 'model_id' => 'social_share' ) ) ?>
          </div>
        <?php
          endif
        ?>
        </div>
      </footer>
    </section><!-- .entry-content -->
  </div><!-- .post-entry -->
  <?php do_action( '__after_content_inner' ) ?>
</article>