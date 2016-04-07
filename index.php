<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> <?php tc_echo('element_attributes') ?>>
<!--<![endif]-->
  <?php tc_render_template('header/head'); ?>

  <body <?php body_class(); ?> >
    <?php if ( tc_has('sidenav') && tc_has('header') ){ tc_render_template('header/sidenav'); }; ?>

    <?php do_action('__before_page_wrapper'); ?>

    <div id="tc-page-wrap">

      <?php tc_render_template('header'); ?>

        <?php tc_render_template('content'); ?>

      <?php tc_render_template('footer'); ?>

    </div>

    <?php do_action('__after_page_wrapper'); ?>

    <?php wp_footer() ?>
  </body>
</html>