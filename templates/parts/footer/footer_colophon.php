<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="colophon" class="colophon <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo('element_inner_class') ?>">
    <div class="colophon__row row flex-row justify-content-between">
      <div class="col-12 col-sm-auto">
        <?php if ( czr_fn_is_registered_or_possible( 'footer_credits' ) ) czr_fn_render_template( 'footer/footer_credits' ) ?>
      </div>
      <?php if ( czr_fn_is_registered_or_possible( 'footer_social_block' ) ) : ?>
      <div class="col-12 col-sm-auto">
        <div class="social-links">
          <?php czr_fn_render_template( 'modules/common/social_block' ) ?>
        </div>
      </div>
      <?php endif ?>
    </div>
  </div>
</div>
