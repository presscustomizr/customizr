<?php
/**
 * The template for displaying the branding wrapper
 */
?>
<div class="branding__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="branding flex-column">
    <?php
      if ( czr_fn_is_registered_or_possible('logo_wrapper') ){
          czr_fn_render_template( 'header/parts/logo_wrapper' );
      } else {
          czr_fn_render_template( 'header/parts/title' );
      }

      if ( czr_fn_is_registered_or_possible( 'mobile_tagline' ) ) {
          czr_fn_render_template( 'header/parts/tagline' );
      }

    ?>
  </div>
  <div class="mobile-utils__wrapper nav__utils">
    <ul class="nav utils row flex-row flex-nowrap">
      <?php
          if ( czr_fn_is_registered_or_possible( 'mobile_wc_cart' ) ) {
              czr_fn_render_template( 'header/parts/woocommerce_cart', array(
                'model_id'   => 'woocommerce_cart',
                'model_args' => array(
                  'element_class'  => array('mobile-woocart__container'),
                  'display_widget' => false
                )
              ) );
          }
          if ( czr_fn_is_registered_or_possible( 'mobile_menu_button' ) ) {
            czr_fn_render_template( 'header/parts/menu_button' );
          }
      ?>
    </ul>
  </div>
</div>
