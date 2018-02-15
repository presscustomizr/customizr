<?php
/**
 * The template for displaying the topbar
*/
?>
<div class="topbar-navbar__wrapper <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo('element_inner_class') ?>">
    <?php do_action( '__before_topbar_navbar_row' ); ?>
    <div class="row flex-row flex-lg-nowrap justify-content-start justify-content-lg-end align-items-center topbar-navbar__row">
      <?php do_action( '__before_topbar_navbar_row_inner' ); ?>
      <?php if ( czr_fn_is_registered_or_possible( 'topbar_menu' ) ) :?>
        <div class="topbar-nav__container col col-auto d-none d-lg-flex">
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
            'element_class' => 'col col-auto d-none d-lg-flex',
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
            'element_class' => 'd-none d-lg-flex',
          )
        ));
      ?>
      <?php do_action( '__after_topbar_navbar_row_inner' ); ?>
    </div>
    <?php do_action( '__after_topbar_navbar_row' ); ?>
  </div>
</div>