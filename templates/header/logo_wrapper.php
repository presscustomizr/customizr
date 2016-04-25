<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="<?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
  <a class="site-logo" href="<?php czr_echo( 'link_url' ) ?>" title="<?php czr_echo( 'link_title' ) ?>" >
    <?php
      if ( czr_has('logo') )
        czr_render_template('header/logo', 'logo');
      if ( czr_has('sticky_logo') )
        czr_render_template('header/logo', 'sticky_logo');

    ?>
  </a>
</div>
