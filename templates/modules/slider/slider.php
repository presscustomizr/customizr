<?php
/**
 * The template for displaying the theme's slider (wrapper)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */

?>
<div id="customizr-slider-<?php tc_echo( 'id' ) ?>" class="<?php tc_echo( 'element_class' ) ?> " <?php tc_echo('element_attributes') ?>>

  <?php if ( tc_get( 'has_loader' ) ) : ?>
    <div id="tc-slider-loader-wrapper-<?php tc_echo( 'id' ) ?>" class="tc-slider-loader-wrapper" style="display:none;">
      <div class="tc-img-gif-loader"></div>
      <?php tc_echo( 'pure_css_loader' ) ?>
    </div>
    <script type="text/javascript">
      document.getElementById("tc-slider-loader-wrapper-<?php tc_echo( 'id' ) ?>").style.display="block";
    </script>
  <?php endif ?>
  <?php do_action( '__before_carousel_inner__' ); ?>

  <div class="<?php tc_echo( 'inner_class' ) ?>">
    <?php
      foreach ( tc_get( 'slides' ) as $slide_id => $slide_data ) {
        //used by each slider instance to set up the current slide data
        do_action( 'in_slider_' . tc_get( 'id' ) , $slide_id, $slide_data );
        //used by the slide model (we have just one instance of it as the slide data is set up by the slider model
        if ( tc_has( 'slide' ) )
          tc_render_template( 'modules/slider/slide', 'slide');
      }
    ?>
  </div><!-- /.carousel-inner -->
  <?php  do_action( '__after_carousel_inner__' ) ?>
  <?php if ( tc_has( 'slider_edit_button' ) ) tc_render_template( 'modules/edit_button', 'slider_edit_button' )  ?>
  <?php if ( tc_get( 'has_controls' ) ) : ?>
    <div class="tc-slider-controls <?php tc_echo( 'left_control_class' ) ?>">
      <a class="tc-carousel-control" href="#customizr-slider-<?php tc_echo( 'id' ) ?>" data-slide="prev">&lsaquo;</a>
    </div>
    <div class="tc-slider-controls <?php tc_echo( 'right_control_class' ) ?>">
      <a class="tc-carousel-control" href="#customizr-slider-<?php tc_echo( 'id' ) ?>" data-slide="next">&rsaquo;</a>
    </div>
  <?php endif ?>
</div><!-- /#customizr-slider -->
