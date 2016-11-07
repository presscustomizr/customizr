<?php
/**
 * The template for displaying the sidenav wrapper.
 * Contains:
 * Sidenav Button
 * Sidenav Menu
 */
?>
<nav id="tc-sn" class="tc-sn navbar" <?php czr_fn_echo('element_attributes') ?>>
  <div class="tc-sn-inner nav-collapse">
    <?php
      if ( czr_fn_has('sidenav_menu_button') )
        czr_fn_render_template('header/menu_button', 'sidenav_menu_button');
      if ( czr_fn_has('sidenav_menu') )
        czr_fn_render_template('header/menu', 'sidenav_menu');
    ?>
  </div><!-- /.tc-sn-inner  -->
</nav>
