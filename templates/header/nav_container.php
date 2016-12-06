<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="primary-nav__container" <?php czr_fn_echo('element_attributes') ?>>
   <nav class="collapse nav-collapse navbar-toggleable-md primary-nav__nav" id="collapse-nav">
    <?php
      if ( czr_fn_has('navbar_menu') ) czr_fn_render_template( 'header/menu', array( 'model_id' => 'navbar_menu') );
      if ( czr_fn_has('nav_utils') ) czr_fn_render_template( 'header/nav_utils' )
    ?>
  </nav>
</div>
