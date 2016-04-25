<?php
/**
 * The template for displaying the site header
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="<?php czr_fn_echo( 'element_class' ) ?>" role="banner" <?php czr_fn_echo('element_attributes') ?>>
  <?php
    if ( czr_fn_has('logo') || czr_fn_has('sticky_logo') )
      czr_fn_render_template('header/logo_wrapper', 'logo_wrapper');
    else
      czr_fn_render_template('header/title');

    if ( czr_fn_has('mobile_tagline') ) { czr_fn_render_template('header/tagline', 'mobile_tagline'); }

    if ( czr_fn_has('navbar_wrapper') ) { czr_fn_render_template('header/navbar_wrapper', 'navbar_wrapper'); }
  ?>
</header>
<?php if ( czr_fn_get('has_sticky_pusher') ): ?>
  <div id="tc-reset-margin-top" class="container-fluid" style="margin-top:<?php czr_fn_echo( 'pusher_margin_top' ) ?>"></div>
<?php endif;
