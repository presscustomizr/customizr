<?php
/**
 * The template for displaying the topnav
*/
?>
<div class="secondary-navbar__wrapper row <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
  <?php
    if ( czr_fn_get( 'has_mobile_button' ) ) :
  ?>
  <div class="col-xs-12 float-left">
    <?php
      czr_fn_render_template( 'header/menu_button', array(
        'model_args' => array(
          'data_attributes' => 'data-toggle="collapse" data-target="#secondary-nav"',
          'element_class'   => 'hidden-lg-up float-right'
        )
      ));
    ?>
  </div>
  <?php
    endif;
  ?>
  <div class="float-left secondary-nav__container">
    <nav id="secondary-nav" class="secondary-nav__nav <?php czr_fn_echo('nav_class') ?>">
      <?php
        czr_fn_render_template( 'header/menu', array(
          'model_id' => 'secondary_menu',
          'model_args' => array(
            'theme_location' => 'secondary',
            'element_class' => 'secondary-nav__menu-wrapper',
            'menu_class'    => array( 'secondary-nav__menu', 'regular', 'list__menu' ),
            'menu_id'       => 'secondary-menu'
          )
        ));
      ?>
    </nav>
  </div>
  <?php if ( czr_fn_has('header_social_block') && czr_fn_has('social_in_topnav')) : ?>
    <div class="float-right hidden-md-down">
      <div class="secondary-nav__socials social-links">
          <?php czr_fn_render_template( 'modules/social_block' ) ?>
      </div>
    </div>
  <?php endif ?>
</div>