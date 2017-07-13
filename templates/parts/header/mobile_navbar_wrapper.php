<?php
/**
 * The template for displaying the mobile navbar wrapper.
 * The mobile navbar wrapper contains:
 * Branding
 * ( Tagline )
 * ( Woocommerce Cart Icon )
 * ( Menu button )
 * Mobile menu
 */
?>
<div class="mobile-navbar__wrapper row align-items-center <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php czr_fn_render_template(  'header/parts/mobile_branding_wrapper', array(
    'model_args' => array(
      'element_class'  => 'col col-auto justify-content-between align-items-center',
    )
  ) ) ?>
  <?php czr_fn_render_template(  'header/parts/mobile_nav_container' ) ?>
</div>