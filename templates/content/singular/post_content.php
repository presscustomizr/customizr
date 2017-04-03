<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 */
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?>>
  <div class="post-entry tc-content-inner">
    <section class="post-content entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
      <?php
      czr_post_format_part();
      the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
      ?>
      <footer class="post-footer container-fluid clearfix">
        <?php
          czr_fn_link_pages();
        ?>
        <div class="entry-meta clearfix">
          <?php if ( czr_fn_has('post_metas') && czr_fn_get( 'tag_list', 'post_metas' ) ) : ?>
          <div class="post-tags float-sm-left">
            <ul class="tags">
              <?php czr_fn_echo( 'tag_list', 'post_metas' ) ?>
            </ul>
          </div>
          <?php endif; ?>
        <?php
          if ( czr_fn_has('social_share') ) :
        ?>
          <div class="post-share float-sm-right">
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
</article>