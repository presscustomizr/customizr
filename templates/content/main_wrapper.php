<?php
/**
 * The template for displaying the main wrapper
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="main-wrapper" class="container" <?php tc_echo('element_attributes') ?>>
  <?php
    tc_render_template('modules/breadcrumb');
    tc_render_template('content/main_container');
  ?>
</div>
