<?php
/**
 * The template for displaying the attachments metas
 * (in singular and list of posts context)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="entry-meta" <?php tc_echo('element_attributes') ?>>
  <span class="meta-prep meta-prep-entry-date"><?php _e('Published' , 'customizr') ?></span>
    <span class="entry-date">
      <?php tc_echo( 'publication_date' ) ?>
    </span>
  <?php if ( tc_get( 'attachment_size' ) ) : ?>
    <?php _e('at dimensions' , 'customizr') ?><a href="<?php esc_url( wp_get_attachment_url() ) ?>" title="<?php _e('Link to full-size image' , 'customizr') ?>"> <?php tc_echo( 'attachment_width') ?> &times; <?php tc_echo( 'attachment_height' ) ?></a>
  <?php endif; ?>
    <?php _e('in' , 'customizr') ?> <a href="<?php tc_echo( 'attachment_parent_url' ) ?>" title="<?php echo __('Return to ' , 'customizr') . esc_attr( strip_tags( tc_get( 'attachment_parent_title' ) ) ) ?>" rel="gallery"><?php tc_echo( 'attachment_parent_title' ) ?></a>
</div>