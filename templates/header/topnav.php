<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="hidden-tablet secondary-navbar__wrapper row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-md-6">
    <div class="secondary-nav__container">
      <?php czr_fn_render_template('header/menu', 'secondary_menu' ) ?>
    </div>
  </div>
  <div class="col-md-6">
    <div class="secondary-nav__socials">
      <?php czr_fn_render_template('header/header_socials') ?>
    </div>
  </div>
</div>

