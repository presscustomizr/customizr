<?php
/**
 * The template for displaying the navbar wrapper.
 * The navbar wrapper contains:
 * Social Block
 * Tagline
 * ( Woocommerce Cart Icon )
 * Navbar menus
 * Navbar menu buttons
 */
?>
<div class="primary-navbar__wrapper <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo('element_inner_class') ?>">
    <div class="row align-items-center flex-row primary-navbar__row">
      <?php czr_fn_render_template(  'header/parts/branding_wrapper', array(
        'model_args' => array(
          'element_class'         => 'col col-auto',
        )
      ) ) ?>
      <?php czr_fn_render_template(  'header/parts/nav_container', array(
        'model_args' => array(
          'element_class'  => 'justify-content-lg-around col col-lg-auto flex-lg-column',
        )
      ) ) ?>
    </div>
  </div>
</div>