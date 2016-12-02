<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="hidden-md-down secondary-navbar__wrapper row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="float-md-left">
    <nav class="secondary-nav__nav">
      <?php czr_fn_render_template( 'header/menu', array( 'model_id' => 'secondary_menu' ) ) ?>
    </nav>
  </div>
  <?php if ( czr_fn_has('header_social_block') ) : ?>
    <div class="float-md-right">
      <div class="secondary-nav__socials social-links">
          <?php czr_fn_render_template( 'modules/social_block' ) ?>
      </div>
    </div>
  <?php endif ?>
</div>