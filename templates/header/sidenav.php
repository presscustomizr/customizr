<?php
/**
 * The template for displaying the sidenav wrapper.
 * Contains:
 * Sidenav Button
 * Sidenav Menu
 */
?>
<div id="tc-sn" class="tc-sn primary-nav__container" <?php czr_fn_echo('element_attributes') ?>>
    <nav class="tc-sn primary-nav__nav" <?php czr_fn_echo('element_attributes') ?>>
      <div class="tc-sn-inner">
        <?php
          if ( czr_fn_has('sidenav_menu_button') ) {
            czr_fn_render_template( 'header/menu_button', array(
              'model_args' => array(
                'data_attributes' => 'data-toggle="sidenav"',
              )
            ) );
          }
          if ( czr_fn_has( 'nav_search' ) ) {
            czr_fn_render_template( 'header/mobile_search_container' );
          }
          if ( czr_fn_has('sidenav_menu') ) {
            czr_fn_render_template( 'header/menu', array(
              'model_id'   => 'sidenav_menu',
              'model_args' => array(
                'element_class' => 'primary-nav__menu-wrapper',
                'menu_class'    => array( 'primary-nav__menu', 'side' )
              )
            ));
          };
        ?>
      </div><!-- /.tc-sn-inner  -->
    </nav>
</div>