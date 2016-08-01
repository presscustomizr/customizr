<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="col-lg-12 col-md-0 primary-nav__container">
   <nav class="collapse nav-collapse navbar-toggleable-md primary-nav__nav" id="collapse-nav">
    <?php if ( czr_fn_has('navbar_menu') ): ?>
      <?php czr_fn_render_template('header/menu', 'navbar_menu'); ?>
    <?php endif ?>  
    <?php czr_fn_render_template('header/nav_utils'); ?>
  </nav>
</div>
