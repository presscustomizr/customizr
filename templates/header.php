<?php
/**
 * The template for displaying the site header
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="tpnav-header__header tc-header" role="banner">
  <div class="topnav_navbars__wrapper <?php czr_fn_echo('element_class') ?>">
    <div class ="container-fluid">
      <?php czr_fn_render_template('header/topnav'); ?>
      <?php czr_fn_render_template('header/navbar_wrapper'); ?>
    </div>
  </div>
</header>
