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
<div class="primary-navbar__wrapper row">
  <?php czr_fn_render_template('header/logo_title_wrapper'); ?>
  <?php czr_fn_render_template('header/nav_container'); ?>
</div>
