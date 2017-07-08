<?php
/**
 * The template for displaying the branding wrapper
 */
?>
<div class="branding__container <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="branding flex-column">
  <?php
    if ( czr_fn_is_registered_or_possible('logo_wrapper') ){
      czr_fn_render_template( 'header/logo_wrapper' );
    } else
      czr_fn_render_template( 'header/title' );

    if ( czr_fn_is_registered_or_possible( 'branding_tagline' ) )
      czr_fn_render_template( 'header/tagline' );
  ?>
  </div>
</div>
