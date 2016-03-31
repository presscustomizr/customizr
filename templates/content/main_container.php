<?php
/**
 * The template for displaying the main container
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="container" <?php tc_echo('element_attributes') ?>>
  <div class="<?php tc_echo( 'column_content_class' ) ?>">
    <?php do_action( '__main_container__') ?>
  </div>
</div>
