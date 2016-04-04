<header class="<?php tc_echo( 'element_class' ) ?>" role="banner" <?php tc_echo('element_attributes') ?>>
  <?php
    if ( tc_has('logo') )
      tc_render_template('header/logo_wrapper');
    else
      tc_render_template('header/title');

    if ( tc_has('mobile_tagline') )
      tc_render_template('header/tagline', 'mobile_tagline');

    tc_render_template('header/navbar_wrapper');
    //do_action( '__header__');
  ?>
</header>
<?php if ( tc_get('has_sticky_pusher') ): ?>
  <div id="tc-reset-margin-top" class="container-fluid" style="margin-top:<?php tc_echo( 'pusher_margin_top' ) ?>"></div>
<?php endif;
