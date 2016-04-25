<?php
/**
 * The template for displaying the site title (with its wrapper)
 */
?>
<div class="<?php czr_echo('element_class') ?>" <?php czr_echo('element_attributes') ?>>
  <h1>
    <a class="site-title" href="<?php czr_echo( 'link_url' ) ?>" title="<?php czr_echo( 'link_title' ) ?>" >
      <?php _e( esc_attr( get_bloginfo( 'name' ) ) ); ?>
    </a>
  </h1>
</div>
