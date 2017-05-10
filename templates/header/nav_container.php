<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="hidden-md-down nav__container-top row flex-lg-row justify-content-lg-end align-items-center">
    <?php if ( czr_fn_has( 'tagline' ) )
      czr_fn_render_template( 'header/tagline' ,array(
                'model_args' => array(
                  'element_class' => 'col col-auto',
                )
      ));
    ?>
    <?php if ( czr_fn_has('navbar_social_block') ) : ?>
      <div class="navbar-nav__socials social-links hidden-md-down col col-auto">
        <?php czr_fn_render_template( 'modules/common/social_block',array(
                'model_args' => array(
                  'element_class' => '',
                )
              ));
        ?>
      </div>
    <?php endif ?>
  </div>
  <div class="primary-nav__wrapper navbar-toggleable-md flex-lg-row justify-content-lg-between">
     <nav class="collapse navbar-collapse primary-nav__nav col-lg" id="primary-nav">
      <?php
        if ( czr_fn_has( 'nav_search' ) ) {
          czr_fn_render_template( 'header/mobile_search_container' );
        }
        if ( czr_fn_has('navbar_primary_menu') || czr_fn_has( 'navbar_secondary_menu' ) ) {
          czr_fn_render_template( 'header/menu', array(
            'model_id'   =>  czr_fn_has('navbar_primary_menu') ? 'navbar_primary_menu' : 'navbar_secondary_menu',
          ) );
        };

      ?>
    </nav>
    <?php
      if ( czr_fn_get('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/nav_utils' )
    ?>
  </div>
</div>
