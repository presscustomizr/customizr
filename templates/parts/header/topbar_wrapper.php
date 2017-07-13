<?php
/**
 * The template for displaying the topbar
*/
?>
<div class="topbar-navbar__wrapper row flex-row flex-lg-nowrap justify-content-end align-items-center <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_is_registered_or_possible( 'topbar_menu' ) ) :?>
    <div class="topbar-nav__container col col-auto">
      <nav id="topbar-nav" class="topbar-nav__nav <?php czr_fn_echo('nav_class') ?>">
        <?php
          czr_fn_render_template( 'header/parts/menu', array(
            'model_id' => 'topbar_menu',
          ));
        ?>
      </nav>
    </div>
  <?php endif ?>
  <?php if ( czr_fn_is_registered_or_possible( 'topbar_tagline' ) )
    czr_fn_render_template( 'header/parts/tagline', array(
      'model_args' => array(
        'element_class' => 'col col-auto',
      )
    ));
  ?>
  <?php if ( czr_fn_is_registered_or_possible( 'topbar_social_block' ) ) : ?>
    <div class="topbar-nav__socials social-links col col-auto">
      <?php
        czr_fn_render_template( 'modules/common/social_block' );
      ?>
    </div>
  <?php endif;

    czr_fn_render_template( 'header/parts/topbar_nav_utils' );

  ?>
</div>