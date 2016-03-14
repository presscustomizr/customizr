<span class="meta-prep meta-prep-entry-date"><?php _e('Published' , 'customizr') ?></span>
  <span class="entry-date">
    <?php echo $attachment_post_metas_model -> tc_get_publication_date(); ?>
  </span>
<?php if ( $attachment_post_metas_model -> tc_is_attachment_size_defined() ) : ?>
  <?php _e('at dimensions' , 'customizr') ?><a href="<?php esc_url( wp_get_attachment_url() ) ?>" title="<?php _e('Link to full-size image' , 'customizr') ?>"> <?php echo $attachment_post_metas_model -> tc_get_attachment_width() ?> &times; <?php echo $attachment_post_metas_model -> tc_get_attachment_height() ?></a>
<?php endif; ?>
  <?php _e('in' , 'customizr') ?> <a href="<?php echo $attachment_post_metas_model -> tc_get_attachment_parent_url() ?>" title="<?php echo __('Return to ' , 'customizr') . esc_attr( strip_tags( $attachment_post_metas_model -> tc_get_attachment_parent_title() ) ) ?>" rel="gallery"><?php echo $attachment_post_metas_model -> tc_get_attachment_parent_title() ?></a>

