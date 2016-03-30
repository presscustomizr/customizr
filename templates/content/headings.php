<?php
/**
 * The template for displaying the headings in post lists and singular
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<header class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__headings_'. tc_get( 'type' ) . '__' ) ?>
  <?php if ( 'content' != tc_get( 'type' ) || is_singular() ) : ?>
    <hr class="featurette-divider headings <?php tc_echo('type') ?>">
  <?php endif ?>
</header>
