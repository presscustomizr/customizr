<?php
/**
 * The template for displaying the primary navbar utils.
 * Contains:
 * Search Button
 * ( Woocommerce Cart Icon )
 */
?>
<div class="topbar-nav__utils nav__utils col-auto <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
    <ul class="nav utils flex-row flex-nowrap regular-nav">
      <?php
      if ( czr_fn_is_registered_or_possible( 'desktop_topbar_search' ) ) {

        czr_fn_render_template( 'header/parts/nav_search', array(
          'model_id' => 'desktop_topbar_search',
          'model_args' => array(
            'search_toggle_class' => array( 'czr-overlay-toggle_btn' ),
          )
        ) );

      }

      if ( czr_fn_is_registered_or_possible( 'desktop_topbar_wc_cart' ) ) :

          czr_fn_render_template( 'header/parts/woocommerce_cart', array(
            'model_id'   => 'woocommerce_cart',
            'model_args' => array(
              'element_class' => array('nav__woocart', 'menu-item-has-children', 'czr-dropdown'),
            )
          ) );

      endif;

      ?>
    </ul>
</div>