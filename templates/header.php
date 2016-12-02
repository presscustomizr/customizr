<?php
/**
 * The template for displaying the site header
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="tpnav-header__header tc-header <?php czr_fn_echo('element_class') ?>" role="banner" <?php czr_fn_echo('element_attributes') ?>>
  <div class="topnav_navbars__wrapper <?php czr_fn_echo('elements_container_class') ?>">
    <div class="container-fluid">
      <?php if ( czr_fn_has('navbar_secondary_menu') ) czr_fn_render_template( 'header/topnav' ) ?>
      <?php if ( czr_fn_has('navbar_wrapper') ) czr_fn_render_template( 'header/navbar_wrapper' ) ?>
    </div>
  </div>
</header>
