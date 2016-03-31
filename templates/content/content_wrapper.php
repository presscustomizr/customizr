<?php
/**
 * The template for displaying the central content (article container)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="content" class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__content__') ?>
</div>
