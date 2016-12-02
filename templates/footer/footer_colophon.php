<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="colophon" class="colophon__row row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="float-sm-left">
    <?php if ( czr_fn_has( 'footer_credits' ) ) czr_fn_render_template( 'footer/footer_credits' ) ?>
  </div>
  <?php if ( czr_fn_has( 'footer_social_block' ) ) : ?>
  <div class="float-sm-right">
    <div class="social-links">
      <?php czr_fn_render_template( 'modules/social_block' ) ?>
    </div>
  </div>
  <?php endif ?>
</div>
