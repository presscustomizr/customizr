<?php
/**
 * The template for displaying the branding wrapper
 */
?>
  <div class="branding__container" <?php czr_fn_echo('element_attributes') ?>>
    <div class="branding">
    <?php
      if ( czr_fn_has('logo_wrapper') ){
        czr_fn_render_template( 'header/logo_wrapper' );
        if ( czr_fn_has( 'tagline' ) )
          czr_fn_render_template( 'header/tagline' );
      } else
        czr_fn_render_template( 'header/title' );
    ?>
    </div>
    <div class="mobile-utils__wrapper hidden-lg-up">
      <?php if ( czr_fn_has('woocommerce_cart', null, $only_registered = true ) ) : ?>
        <?php
          czr_fn_render_template( 'header/woocommerce_cart', array(
            'model_args' => array(
              'element_class'  => array('mobile-woocart__container'),
              'display_widget' => false
            )
          ) );
        ?>
      <?php endif ?>
      <?php czr_fn_render_template( 'header/menu_button' ) ?>
    </div>
  </div>
