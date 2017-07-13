<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="primary-nav__wrapper navbar-toggleable-md flex-lg-row justify-content-end">
     <nav class="collapse navbar-collapse primary-nav__nav col-lg" id="primary-nav">
      <?php

        if ( czr_fn_is_registered_or_possible('navbar_primary_menu') || czr_fn_is_registered_or_possible( 'navbar_secondary_menu' ) ) {
          czr_fn_render_template( 'header/parts/menu', array(
            'model_id'   =>  czr_fn_is_registered_or_possible('navbar_primary_menu') ? 'navbar_primary_menu' : 'navbar_secondary_menu',
          ) );
        };

      ?>
    </nav>
    <?php
      if ( czr_fn_is_registered_or_possible( 'primary_nav_utils' ) ) czr_fn_render_template( 'header/parts/primary_nav_utils' )
    ?>
  </div>
</div>
