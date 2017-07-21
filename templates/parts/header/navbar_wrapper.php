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
<div class="primary-navbar__wrapper row align-items-center flex-lg-row <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php czr_fn_render_template(  'header/parts/branding_wrapper', array(
    'model_args' => array(
      'element_class'         => 'col col-auto',
      'inner_branding_class'  => 'brand_next' == czr_fn_opt( 'tc_header_desktop_tagline' ) ? 'flex-row tagline-aside' : 'flex-column tagline-below'
    )
  ) ) ?>
  <?php czr_fn_render_template(  'header/parts/nav_container', array(
    'model_args' => array(
      'element_class'  => 'justify-content-lg-around col col-lg-auto flex-lg-column',
    )
  ) ) ?>
</div>