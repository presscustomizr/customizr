<?php
/**
 * The template for displaying the breadcrumb
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="tc-hot-crumble container" role="navigation" <?php czr_fn_echo('element_attributes') ?>>
  <div class="row wide page-breadcrumbs">
    <div class="col-md-12">
      <div class="line-holder"></div>
      <?php /* or do not use a model but a tc function (template tag) */ ?>
      <?php czr_fn_echo( 'breadcrumb' ) ?>
    </div>
  </div>
</div>