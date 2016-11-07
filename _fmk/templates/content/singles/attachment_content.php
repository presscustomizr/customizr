<?php
/**
 * The template for displaying the single attachment content
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php czr_fn_echo( 'article_selectors' ) ?> <?php czr_fn_echo( 'element_attributes' ) ?> >
  <?php do_action( '__before_inner_attachment_article' ) ?>
  <?php if ( czr_fn_has('singular_headings') ) czr_fn_render_template('content/singles/singular_headings', 'singular_headings'); ?>
  <nav id="image-navigation" class="navigation" role="navigation">
    <span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'customizr' ) ); ?></span>
    <span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); ?></span>
  </nav><!-- //#image-navigation -->
  <?php do_action( '__before_attachment_entry_content' ) ?>
  <section class="entry-content">
  <?php do_action( '__before_inner_attachment_content' ) ?>
    <div class="entry-attachment">
      <div class="attachment">
        <a href="<?php czr_fn_echo( 'link_url' ) ?>" class="<?php czr_fn_echo( 'attachment_class' ) ?>" title="<?php the_title_attribute(); ?>" rel="<?php czr_fn_echo( 'link_rel' ) ?>"><?php echo wp_get_attachment_image( get_the_ID(), czr_fn_get( 'attachment_size' ) ) ?></a>
        <div class="entry-caption">
          <?php the_excerpt(); ?>
        </div>
        <?php /* hidden fancybox gallery with all the attachments referring to the same post parent */
        czr_fn_echo( 'gallery' )
        ?>
      </div>
    </div>
  <?php do_action( '__after_inner_attachment_content' ) ?>
  </section>
  <?php do_action( '__before_attachment_entry_content' ) ?>
</article>
