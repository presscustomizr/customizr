<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="hidden-md-down secondary-navbar__wrapper row" <?php czr_fn_echo('element_attributes') ?>>
  <div class="col-md-9">
    <nav class="secondary-nav__nav">
      <?php czr_fn_render_template( 'header/menu', array( 'model_id' => 'secondary_menu' ) ) ?>
    </nav>
  </div>
  <?php if ( czr_fn_has('header_socials') ) : ?>
  <div class="col-md-3">
      <div class="secondary-nav__socials social-links">
          <?php
          czr_fn_render_template(
            'modules/social_block',
            array( 'model_id'   => 'header_socials' )
          );
          ?>
      </div>
  </div>
  <?php endif ?>
</div>