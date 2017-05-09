<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container_new <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="primary-nav__wrapper_new navbar-toggleable-md col col-auto">
     <nav class="collapse navbar-collapse primary-nav__nav" id="primary-nav">
      <?php
        if ( czr_fn_has( 'nav_search' ) ) {
          czr_fn_render_template( 'header/mobile_search_container' );
        }
        if ( czr_fn_has('navbar_primary_menu') || czr_fn_has( 'navbar_secondary_menu' ) ) {
          czr_fn_render_template( 'header/menu', array(
            'model_id'   =>  czr_fn_has('navbar_primary_menu') ? 'navbar_primary_menu' : 'navbar_secondary_menu'
          ));
        };

      ?>
    </nav>
  </div>
  <?php
    if ( czr_fn_get('with_nav_utils') && czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/new/nav_utils' )
  ?>
</div>
