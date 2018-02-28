<?php
/**
 * The template for displaying the footer pusher
 * This is a simple template which has reason to exist to allow users
 * to place it wherever they want, after an article, after the main wrapper
 * and so on.. by default it's placed after the main container (.container[role=main])
 * inside the #main-wrapper
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="footer-horizontal-widget-area" class="<?php czr_fn_echo('element_class') ?> widget__wrapper" role="complementary" <?php czr_fn_echo('element_attributes') ?>>
  <div class="<?php czr_fn_echo('inner_container_class') ?> widget__container">
    <div class="row">
      <?php do_action("__before_footer_horizontal_widgets") ?>
        <div class="col-12">
          <?php dynamic_sidebar( 'footer_horizontal' ) ?>
        </div>
      <?php do_action("__after_footer_horizontal_widgets") ?>
    </div>
  </div>
</div>
