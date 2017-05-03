<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container_new <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="primary-nav__wrapper_new navbar-toggleable-md">
     <nav class="collapse navbar-collapse primary-nav__nav" id="primary-nav">
      <?php
        if ( czr_fn_has( 'nav_search' ) ) {
          czr_fn_render_template( 'header/mobile_search_container' );
        }
        if ( czr_fn_has('navbar_menu_test') ) {
          czr_fn_render_template( 'header/menu', array(
            'model_id'   => 'navbar_menu_test',
            'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_primary_menu' ),
            'model_args' => array(
              'element_class' => 'primary-nav__menu-wrapper_new',
              'menu_class'    => array( 'primary-nav__menu_new', 'regular_new', 'navbar-nav', 'nav__menu' ),
            )
          ));
        };

      ?>
    </nav>
  </div>
  <?php
    if ( czr_fn_get('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/new/nav_utils' )
  ?>
</div>
