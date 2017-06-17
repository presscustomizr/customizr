<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="colophon" class="colophon colophon__row row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-sm-6">
    <?php if ( czr_fn_has( 'footer_credits' ) ) czr_fn_render_template( 'footer/footer_credits' ) ?>
  </div>
  <?php if ( czr_fn_has( 'footer_social_block' ) ) : ?>
  <div class="col-sm-6 text-sm-right">
    <div class="social-links">
      <?php czr_fn_render_template( 'modules/common/social_block' ) ?>
    </div>
  </div>
  <?php endif ?>
</div>
