<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <a class="site-logo" href="<?php tc_echo( 'link_url' ) ?>" title="<?php tc_echo( 'link_title' ) ?>" >
    <?php
      if ( tc_has('logo') )
        tc_render_template('header/logo', 'logo');
      if ( tc_has('sticky_logo') )
        tc_render_template('header/logo', 'sticky_logo');

    ?>
  </a>
</div>
