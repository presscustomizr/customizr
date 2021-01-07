<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 */
global $post;
?>
<article <?php echo czr_fn_get_the_singular_article_selectors() ?> <?php czr_fn_echo( 'element_attributes' ) ?>>
  <?php do_action( '__before_content_inner' ) ?>
  <?php
  /* heading */
  czr_fn_render_template( 'content/singular/headings/regular_attachment_image_heading' );
  /* navigation */
  czr_fn_render_template( 'content/singular/navigation/single_attachment_image_navigation' );
  $caption = czr_fn_get_property( 'attachment_caption' );
  ?>
  <div class="post-entry tc-content-inner">
    <section class="entry-attachment attachment-content" >
      <div class="attachment-figure-wrapper display-flex flex-wrap" >
        <figure class="attachment-image-figure">
          <div class="entry-media__holder">
            <a href="<?php czr_fn_echo( 'attachment_link_url' ) ?>" class="<?php czr_fn_echo( 'attachment_class' ) ?> bg-link" title="<?php the_title_attribute(); ?>" <?php czr_fn_echo( 'attachment_link_attributes' ) ?>></a>
            <?php echo wp_get_attachment_image( get_the_ID(), czr_fn_get_property( 'attachment_size' ) ) ?>
          </div>
          <?php if ( !empty($caption) ) :?>
            <figcaption class="wp-caption-text entry-caption">
              <?php echo $caption ?>
            </figcaption>
          <?php endif; ?>
        </figure>
      </div>
      <?php /* hidden ligthbox gallery with all the attachments referring to the same post parent */
      czr_fn_echo( 'attachment_gallery' )
      ?>
      <div class="entry-content">
        <div class="czr-wp-the-content">
          <?php the_content(); ?>
        </div>
      </div>
      <footer class="post-footer clearfix">
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
      </footer>
    </section><!-- .entry-content -->
  </div><!-- .post-entry -->
  <?php do_action( '__after_content_inner' ) ?>
</article>