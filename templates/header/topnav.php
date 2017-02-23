<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="hidden-md-down secondary-navbar__wrapper row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="float-left">
    <nav class="secondary-nav__nav">
      <?php
        czr_fn_render_template( 'header/menu', array(
          'model_id' => 'secondary_menu',
          'model_args' => array(
            'theme_location' => 'secondary',
            'element_class' => 'secondary-nav__menu-wrapper',
            'menu_class'    => array( 'secondary-nav__menu', 'regular', 'list__menu' )
          )
        ));
      ?>
    </nav>
  </div>
  <?php if ( czr_fn_has('header_social_block') && czr_fn_has('social_in_topnav')) : ?>
    <div class="float-right">
      <div class="secondary-nav__socials social-links">
          <?php czr_fn_render_template( 'modules/social_block' ) ?>
      </div>
    </div>
  <?php endif ?>
</div>