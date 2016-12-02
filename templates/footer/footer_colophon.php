<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="colophon" class="colophon__row row">
  <div class="col-sm-6 col-xs-12">
    <?php if ( czr_fn_has( 'footer_credits' ) ) czr_fn_render_template( 'footer/footer_credits' ) ?>
  </div>
  <div class="col-sm-6 col-xs-12">
    <div class="social-links float-sm-right">
    <?php
    if ( czr_fn_has( 'footer_socials' ) ) czr_fn_render_template(
      'modules/social_block',
      array(
        'model_id' => 'footer_socials',
      )
    )
    ?>
    </div>
  </div>
</div>
