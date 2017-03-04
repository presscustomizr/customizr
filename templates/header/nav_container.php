<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container" <?php czr_fn_echo('element_attributes') ?>>
  <div class="primary-nav__wrapper navbar-toggleable-md">
     <nav class="collapse navbar-collapse primary-nav__nav" id="primary-nav">
      <?php
        if ( czr_fn_has( 'nav_search' ) ) {
          czr_fn_render_template( 'header/mobile_search_container' );
        }
        if ( czr_fn_has('navbar_menu') ) {
          czr_fn_render_template( 'header/menu', array(
            'model_id'   => 'navbar_menu',
            'model_args' => array(
              'element_class' => 'primary-nav__menu-wrapper',
              'menu_class'    => array( 'primary-nav__menu', 'regular', 'navbar-nav' ),
            )
          ));
        };
        if ( czr_fn_get('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/nav_utils' )
      ?>
    </nav>
  </div>
</div>
