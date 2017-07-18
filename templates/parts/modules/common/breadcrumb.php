<?php
/**
 * The template for displaying the breadcrumb
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="czr-hot-crumble container page-breadcrumbs" role="navigation" <?php czr_fn_echo('element_attributes') ?>>
  <div class="row">
    <?php /* or do not use a model but a tc function (template tag) */ ?>
    <?php czr_fn_echo( 'breadcrumb' ) ?>
  </div>
</div>