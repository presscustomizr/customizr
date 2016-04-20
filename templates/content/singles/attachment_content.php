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
<nav id="image-navigation" class="navigation" role="navigation" <?php tc_echo('element_attributes') ?>>
    <span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'customizr' ) ); ?></span>
    <span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); ?></span>
</nav><!-- //#image-navigation -->
<section class="entry-content">
  <div class="entry-attachment">
    <div class="attachment">
      <a href="<?php tc_echo( 'link_url' ) ?>" class="<?php tc_echo( 'attachment_class' ) ?>" title="<?php the_title_attribute(); ?>" rel="<?php tc_echo( 'link_rel' ) ?>"><?php echo wp_get_attachment_image( get_the_ID(), tc_get( 'attachment_size' ) ) ?></a>
      <div class="entry-caption">
        <?php the_excerpt(); ?>
      </div>
      <?php /* hidden fancybox gallery with all the attachments referring to the same post parent */
      tc_echo( 'gallery' )

      ?>
    </div>
  </div>
</section>
