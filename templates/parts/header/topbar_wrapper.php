<?php
/**
 * The template for displaying the topbar
*/
?>
<div class="topbar-navbar__wrapper <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo('element_inner_class') ?>">
    <div class="row flex-row flex-lg-nowrap justify-content-start justify-content-lg-end align-items-center topbar-navbar__row">
      <?php if ( czr_fn_is_registered_or_possible( 'topbar_contact_info' ) ) :?>
        <div class="topbar-contact__info col col-auto <?php czr_fn_echo( 'contact_info_class' ) ?>">
          <?php
            czr_fn_render_template( 'modules/common/contact_info', array(
              'model_args' => array(
                'element_class' => 'nav header-contact__info'
              )
            ) );
          ?>
        </div>
      <?php elseif ( czr_fn_is_registered_or_possible( 'topbar_menu' ) ) :?>
        <div class="topbar-nav__container col col-auto hidden-md-down">
          <nav id="topbar-nav" class="topbar-nav__nav">
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
            'element_class' => 'col col-auto hidden-md-down',
          )
        ));
      ?>
      <?php if ( czr_fn_is_registered_or_possible( 'topbar_social_block' ) ) : ?>
        <div class="topbar-nav__socials social-links col col-auto <?php czr_fn_echo( 'social_block_class' ) ?>">
          <?php
            czr_fn_render_template( 'modules/common/social_block' );
          ?>
        </div>
      <?php endif;

        czr_fn_render_template( 'header/parts/topbar_nav_utils', array(
          'model_args' => array(
            'element_class' => 'hidden-md-down',
          )
        ));
      ?>
    </div>
  </div>
</div>