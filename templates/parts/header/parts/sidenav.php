<?php
/**
 * The template for displaying the sidenav wrapper.
 * Contains:
 * Sidenav Button
 * Sidenav Menu
 */
?>
<div id="tc-sn" class="tc-sn side-nav__container d-none d-lg-block" <?php czr_fn_echo('element_attributes') ?>>
    <nav class="tc-sn side-nav__nav" <?php czr_fn_echo('element_attributes') ?>>
      <div class="tc-sn-inner">
        <?php
          if ( czr_fn_is_registered_or_possible('sidenav_menu_button') ) {
            czr_fn_render_template( 'header/parts/menu_button', array(
              'model_args' => array(
                'data_attributes' => 'data-toggle="sidenav" aria-expanded="false"',
                'element_tag'     => 'div'
              )
            ) );
          }
          if ( czr_fn_is_registered_or_possible('sidenav_menu') ) {
            czr_fn_render_template( 'header/parts/menu', array(
              'model_id'   => 'sidenav_menu',
            ));
          };
        ?>
      </div><!-- /.tc-sn-inner  -->
    </nav>
</div>