<?php
/**
 * The template for displaying the menu button ( both in the navbar and sidenav )
 */
?>
<div class="btn-toggle-nav <?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>
  <button type="button" class="btn menu-btn" <?php czr_echo( 'button_attr' ) ?> title ="<?php czr_echo( 'button_title' ) ?>">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </button>
<?php czr_echo( 'button_label' ) ?>
</div>
