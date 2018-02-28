<?php
/**
 * The template for displaying the horizontal footer widget area
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
