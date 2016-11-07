<?php
/**
 * The template for displaying the tagline (both mobile and desktop one)
 */
?>
<?php if ( 'mobile' == czr_fn_get('context') ) : ?>
<div class="container outside">
<?php endif ?>
  <h2 class="site-description <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo( 'element_attributes' ) ?>>
    <?php _e( esc_attr( get_bloginfo( 'description' ) ) ) ?>
  </h2>
<?php if ( 'mobile' == czr_fn_get('context') ) : ?>
</div>
<?php endif;
