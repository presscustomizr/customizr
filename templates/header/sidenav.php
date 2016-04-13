<?php
/**
 * The template for displaying the sidenav wrapper.
 * Contains:
 * Sidenav Button
 * Sidenav Menu
 */
?>
<nav id="tc-sn" class="tc-sn navbar" <?php tc_echo('element_attributes') ?>>
  <div class="tc-sn-inner nav-collapse">
    <?php
      if ( tc_has('sidenav_menu_button') )
        tc_render_template('header/menu_button', 'sidenav_menu_button');
      if ( tc_has('sidenav_menu') )
        tc_render_template('header/menu', 'sidenav_menu');
    ?>
  </div><!-- /.tc-sn-inner  -->
</nav>
