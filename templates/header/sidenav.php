<nav id="tc-sn" class="tc-sn navbar" <?php tc_echo('element_attributes') ?>>
  <div class="tc-sn-inner nav-collapse">
    <?php
      if ( tc_has('sidenav_menu_button') )
        tc_render_template('header/menu_button', 'sidenav_menu_button');
      if ( tc_has('sidenav_menu_button') )
        tc_render_template('header/menu');

    //do_action( '__sidenav__' ); /*hook of social, tagline, menu, ordered by priorities 10, 20, 20*/ ?>
  </div><!-- /.tc-sn-inner  -->
</nav>
