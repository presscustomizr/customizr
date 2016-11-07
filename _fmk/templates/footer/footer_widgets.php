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
<div class="container footer-widgets <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="row widget-area" role="complementary">
    <?php do_action("__before_footer_widgets") ?>
    <div id="footer_one" class="span4">
      <?php dynamic_sidebar( 'footer_one' ) ?>
    </div>
    <div id="footer_two" class="span4">
      <?php dynamic_sidebar(  'footer_two' ) ?>
    </div>
    <div id="footer_three" class="span4">
      <?php dynamic_sidebar(  'footer_three' ) ?>
    </div>
    <?php do_action("__after_footer_widgets") ?>
  </div>
</div>
