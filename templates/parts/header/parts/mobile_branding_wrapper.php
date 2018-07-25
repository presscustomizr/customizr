<?php
/**
 * The template for displaying the branding wrapper
 */
?>
<div class="branding__container justify-content-between align-items-center <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="branding flex-column">
    <div class="branding-row d-flex align-self-start flex-row align-items-center">
      <?php
        if ( czr_fn_is_registered_or_possible('logo_wrapper') ){
            czr_fn_render_template( 'header/parts/logo_wrapper' );
        } else if ( czr_fn_is_registered_or_possible('title_alone') ) {
          czr_fn_render_template( 'header/parts/title' );
        }
        if ( czr_fn_is_registered_or_possible('title_next_logo') ) { ?>
            <div class="branding-aside col-auto">
              <?php czr_fn_render_template( 'header/parts/title' ); ?>
            </div>
        <?php
        }
      ?>
    </div>
    <?php
      if ( czr_fn_is_registered_or_possible( 'mobile_tagline' ) ) {
          czr_fn_render_template( 'header/parts/tagline', array(
              'model_args' => array(
                'element_class' => 'col col-auto',
              )
          ));
      }
    ?>
  </div>
  <div class="mobile-utils__wrapper nav__utils regular-nav">
    <ul class="nav utils row flex-row flex-nowrap">
      <?php
          if ( czr_fn_is_registered_or_possible( 'mobile_navbar_search' ) ) {
            czr_fn_render_template( 'header/parts/nav_search', array(
              'model_id'   => 'mobile_navbar_search',
              'model_args' => array(
                'search_toggle_class'         => array( 'czr-dropdown' ),
                'search_toggle_attributes'    => 'data-aria-haspopup="true"',
                'has_dropdown'                => true,
                'search_form_container_class' => czr_fn_get_property( 'search_form_container_class' )
              )
            ) );
          }
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
            czr_fn_render_template( 'header/parts/menu_button', array(
                'model_id'   => 'mobile_menu_button',
            ));
          }
      ?>
    </ul>
  </div>
</div>
