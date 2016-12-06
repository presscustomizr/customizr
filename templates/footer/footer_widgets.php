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
<div id="footer-widget-area" class="container widget__wrapper" role="complementary" <?php czr_fn_echo('element_attributes') ?>>
  <div class="row widget__container">
    <?php do_action("__before_footer_widgets") ?>
    <?php
      $_footer_widgets  = apply_filters( 'tc_footer_widgets', CZR_init::$instance -> footer_widgets );
      $class = $_footer_widgets ? 'col-md-' . 12/( count($_footer_widgets) ) : '';

      foreach ( $_footer_widgets as $key => $area ):
      ?>
        <div id="<?php echo $key ?>" class="<?php echo $class ?> col-xs-12">
          <?php dynamic_sidebar( $key ) ?>
        </div>
      <?php
      endforeach;
    ?>
    <?php do_action("__after_footer_widgets") ?>
  </div>
</div>
