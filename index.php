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
  <?php
    tc_render_template('header/head');
    tc_render_template('body');
  ?>
</html>