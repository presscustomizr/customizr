<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="secondary-navbar__wrapper row <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_has( 'secondary_menu' ) ) :?>
    <?php if ( czr_fn_get( 'has_mobile_button' ) ) :?>
      <div class="hamburger-toggler__wrapper col-12 float-left hidden-lg-up">
        <?php
          czr_fn_render_template( 'header/menu_button', array(
            'model_args' => array(
              'data_attributes' => 'data-toggle="collapse" data-target="#secondary-nav"',
              'element_class'   => 'float-right'
            )
          ));
        ?>
      </div>
    <?php endif ?>
    <div class="secondary-nav__container">
      <nav id="secondary-nav" class="secondary-nav__nav float-left <?php czr_fn_echo('nav_class') ?>">
        <?php
          czr_fn_render_template( 'header/menu', array(
            'model_id' => 'secondary_menu',
            'model_args' => array(
              'theme_location' => 'secondary',
              'element_class' => 'secondary-nav__menu-wrapper',
              'menu_class'    => czr_fn_get('menu_class'),
              'menu_id'       => 'secondary-menu'
            )
          ));
        ?>
      </nav>
    </div>
  <?php endif ?>
  <?php if ( czr_fn_has('social_in_topnav') ) : ?>
    <div class="secondary-nav__socials social-links hidden-md-down">
      <?php czr_fn_render_template( 'modules/common/social_block' ) ?>
    </div>
  <?php endif ?>
</div>