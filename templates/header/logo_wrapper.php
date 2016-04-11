<?php
/**
 * The template for displaying the logo wrapper
 */
?>
<div class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <a class="site-logo" href="<?php tc_echo( 'link_url' ) ?>" title="<?php tc_echo( 'link_title' ) ?>" >
    <?php
      //do_action('__logo_wrapper__');
      tc_render_template('header/logo');
      if ( tc_has('sticky_logo') )
        tc_render_template('header/logo', 'sticky_logo');

    ?>
  </a>
</div>
