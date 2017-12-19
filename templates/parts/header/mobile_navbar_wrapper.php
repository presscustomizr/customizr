<?php
/**
 * The template for displaying the mobile navbar wrapper.
 * The mobile navbar wrapper contains:
 * Branding
 * ( Tagline )
 * ( Woocommerce Cart Icon )
 * ( Menu button )
 * Mobile menu
 */
?>
<div class="mobile-navbar__wrapper <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
    <?php
    czr_fn_render_template( 'header/parts/mobile_branding_wrapper', array(
      'model_args' => array(
        'element_class'                => czr_fn_get_property( 'inner_elements_class' ),
        'search_form_container_class'  => czr_fn_get_property( 'inner_elements_class' ),
      )
    ) );
    czr_fn_render_template(  'header/parts/mobile_nav_container', array(
      'model_args' => array(
        'inner_elements_class'  => czr_fn_get_property( 'inner_elements_class' )
      )
    ) );
    ?>
</div>