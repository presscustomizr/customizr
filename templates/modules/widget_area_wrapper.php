<?php
/**
 * The template for displaying a widget area wrapper
 * Used by:
 * 1) left and right sidebars wrapper
 * 2) footer-widgets = the wrapper which sorrounds the single footer widget areas
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="<?php tc_echo('element_class') ?>" <?php tc_echo('element_attributes') ?>>
  <div id="<?php tc_echo( 'inner_id' ) ?>" class="<?php tc_echo( 'inner_class' ) ?>" role="complementary">
    <?php do_action( '__widget_area'. tc_get( 'action_hook_suffix' ) . '__' ) ?>
  </div>
</div>
