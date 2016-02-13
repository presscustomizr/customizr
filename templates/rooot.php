<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<?php do_action('__before_body__') ; ?>

  <body <?php body_class(); ?> <?php echo apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"') ?>>
    <?php do_action('__body__'); ?>
  </body>
</html>