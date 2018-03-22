<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="primary-nav__wrapper flex-lg-row align-items-center justify-content-end">
     <?php if ( czr_fn_is_registered_or_possible( 'navbar_primary_menu' ) || czr_fn_is_registered_or_possible( 'navbar_secondary_menu' ) ) { ?>
         <nav class="primary-nav__nav col" id="primary-nav">
          <?php
              czr_fn_render_template( 'header/parts/menu', array(
                'model_id'   =>  czr_fn_is_registered_or_possible( 'navbar_primary_menu' ) ? 'navbar_primary_menu' : 'navbar_secondary_menu',
              ) );
          ?>
        </nav>
    <?php }
      else {
        czr_fn_print_add_menu_button();
      }

      if ( czr_fn_is_registered_or_possible( 'primary_nav_utils' ) ) czr_fn_render_template( 'header/parts/primary_nav_utils' )
    ?>
  </div>
</div>
