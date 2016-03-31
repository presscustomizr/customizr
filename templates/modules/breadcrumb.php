<?php
/**
 * The template for displaying the breadcrumb
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="tc-hot-crumble container" role="navigation" <?php tc_echo('element_attributes') ?>>
  <div class="row">
    <div class="span12">
      <?php /* or do not use a model but a tc function (template tag) */ ?>
      <?php tc_echo( 'breadcrumb' ) ?>
    </div>
  </div>
</div>

