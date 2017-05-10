<?php
/**
 * The template for displaying the topbar
*/
?>
<div class="topbar-navbar__wrapper row flex-row <?php czr_fn_echo('element_class') ?> flex-lg-nowrap justify-content-lg-between align-items-lg-start" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_has( 'topbar_menu' ) ) :?>
    <?php if ( czr_fn_get( 'has_mobile_button' ) ) :?>
      <div class="hamburger-toggler__wrapper col-12 float-left hidden-lg-up">
        <?php
          czr_fn_render_template( 'header/menu_button', array(
            'model_args' => array(
              'data_attributes' => 'data-toggle="collapse" data-target="#topbar-nav"',
            )
          ));
        ?>
      </div>
    <?php endif ?>
    <div class="topbar-nav__container col col-auto">
      <nav id="topbar-nav" class="topbar-nav__nav float-left <?php czr_fn_echo('nav_class') ?>">
        <?php
          czr_fn_render_template( 'header/menu', array(
            'model_id' => 'tobar_menu',
            'model_args' => array(
              'theme_location' => 'topbar',
              'element_class' => 'topbar-nav__menu-wrapper',
              'menu_class'    => czr_fn_get('menu_class'),
              'menu_id'       => 'topbar-menu'
            )
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