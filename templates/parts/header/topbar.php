<?php
/**
 * The template for displaying the topbar
*/
?>
<div class="topbar-navbar__wrapper row flex-row hidden-md-down flex-lg-nowrap justify-content-lg-between align-items-center" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_has( 'topbar_menu' ) ) :?>
    <div class="topbar-nav__container col col-auto">
      <nav id="topbar-nav" class="topbar-nav__nav <?php czr_fn_echo('nav_class') ?>">
        <?php
          czr_fn_render_template( 'header/menu', array(
            'model_id' => 'topbar_menu',
          ));
        ?>
      </nav>
    </div>
  <?php endif ?>
  <?php if ( czr_fn_has( 'topbar_social_block' ) ) : ?>
    <div class="topbar-nav__socials social-links hidden-md-down col col-auto">
      <?php czr_fn_render_template( 'modules/common/social_block',array(
              'model_args' => array(
                'element_class' => is_rtl() ? 'float-left' : 'float-right',
              )
            ));
      ?>
    </div>
  <?php endif ?>
</div>