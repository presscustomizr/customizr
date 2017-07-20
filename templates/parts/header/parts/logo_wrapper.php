<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="navbar-brand align-self-start <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a class="navbar-brand-sitelogo" href="<?php _e( esc_url( home_url( '/' ) ) ) ?>" title="<?php esc_attr_e( get_bloginfo( 'name' ) ) ?> | <?php esc_attr_e( get_bloginfo( 'description' ) ) ?>" >
    <?php
      if ( czr_fn_is_registered_or_possible('logo') )
        czr_fn_render_template( 'header/parts/logo' );
    ?>
  </a>
</div>
