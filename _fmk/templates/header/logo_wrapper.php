<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="<?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <a class="site-logo" href="<?php czr_fn_echo( 'link_url' ) ?>" title="<?php czr_fn_echo( 'link_title' ) ?>" >
    <?php
      if ( czr_fn_has('logo') )
        czr_fn_render_template('header/logo', 'logo');
      if ( czr_fn_has('sticky_logo') )
        czr_fn_render_template('header/logo', 'sticky_logo');

    ?>
  </a>
</div>
