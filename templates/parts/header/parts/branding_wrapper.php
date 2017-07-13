<?php
/**
 * The template for displaying the branding wrapper
 * July 2017 : no specific model for this template. The 'inner_branding_class' property => is added to the default model when invoking the czr_fn_render_template
 */
?>
<div class="branding__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="branding align-items-center <?php czr_fn_echo( 'inner_branding_class' ); ?>">
  <?php
    if ( czr_fn_is_registered_or_possible('logo_wrapper') ){
      czr_fn_render_template( 'header/parts/logo_wrapper' );
    } else
      czr_fn_render_template( 'header/parts/title' );

    if ( czr_fn_is_registered_or_possible( 'branding_tagline' ) )
      czr_fn_render_template( 'header/parts/tagline' );
  ?>
  </div>
</div>
