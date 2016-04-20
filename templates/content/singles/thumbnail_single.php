<?php
/**
 * The template for displaying the thumbnail in single post page
 * Depending on the optional position it can be inside or outside the WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="row-fluid tc-single-post-thumbnail-wrapper <?php tc_echo( 'thumb_position' ) ?>" <?php tc_echo('element_attributes') ?>>
  <section class="tc-thumbnail span12" >
   <div>
    <a class="tc-rectangular-thumb" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
      <?php tc_echo( 'thumb_img' ) ?>
    </a>
   </div>
  </section>
</div>
