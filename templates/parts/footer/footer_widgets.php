<?php
/**
 * The template for displaying the footer widgets
*/
?>
<div id="footer-widget-area" class="widget__wrapper" role="complementary" <?php czr_fn_echo('element_attributes') ?>>
  <div class="container widget__container">
    <div class="row">
      <?php do_action("__before_footer_widgets") ?>
      <?php
        $_footer_widgets  = apply_filters( 'czr_footer_widgets', CZR_init::$instance -> footer_widgets );
        $class = $_footer_widgets ? 'col-md-' . 12/( count($_footer_widgets) ) : '';

        foreach ( $_footer_widgets as $key => $area ):
        ?>
          <div id="<?php echo $key ?>" class="<?php echo $class ?> col-12">
            <?php dynamic_sidebar( $key ) ?>
          </div>
        <?php
        endforeach;
      ?>
      <?php do_action("__after_footer_widgets") ?>
    </div>
  </div>
</div>
