<?php
/**
 * The template for displaying the primary navbar utils.
 * Contains:
 * Search Button
 * ( Woocommerce Cart Icon )
 * (Socials)
 */
?>
<div class="primary-nav__utils nav__utils hidden-md-down col col-auto" <?php czr_fn_echo('element_attributes') ?>>
    <ul class="nav utils row flex-row flex-nowrap">
      <?php
      if ( czr_fn_has( 'nav_search' ) ) czr_fn_render_template( 'header/nav_search' );

      if ( czr_fn_has( 'woocommerce_cart', null, $only_registered = true ) ) :

          czr_fn_render_template( 'header/woocommerce_cart', array(
            'model_args' => array(
              'element_class' => array('primary-nav__woocart', 'hidden-md-down', 'menu-item-has-children', 'czr-dropdown'),
            )
          ) );

      endif;

      if ( czr_fn_has( 'sidenav' ) ) :
          czr_fn_render_template( 'header/menu_button', array(
            'model_args' => array(
              'data_attributes' => 'data-toggle="sidenav" aria-expanded="false"',
            )
          ) );
      endif;
      ?>
    </ul>
</div>