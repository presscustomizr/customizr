<?php
/**
 * The template for displaying the single page content
 *
 * In WP loop
 */
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?>>
  <?php do_action( '__before_content_inner' ) ?>
  <?php
  czr_fn_render_template( 'content/singular/headings/regular_page_heading' );
  ?>
  <div class="post-entry tc-content-inner">
    <section class="post-content entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <div class="czr-wp-the-content">
        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
      </div>
      <footer class="post-footer clearfix">
        <?php
          czr_fn_link_pages();
        ?>
        <?php
          if ( czr_fn_is_registered_or_possible('social_share') ) :
        ?>
          <div class="entry-meta clearfix">
            <div class="post-share">
              <!-- fake need to have social links somewhere -->
              <?php czr_fn_render_template( 'modules/common/social_block', array( 'model_id' => 'social_share' ) ) ?>
            </div>
          </div>
        <?php
          endif;
        ?>
      </footer>
    </section><!-- .entry-content -->
  </div><!-- .post-entry -->
  <?php do_action( '__after_content_inner' ) ?>
</article>