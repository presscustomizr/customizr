<div id="customizr-slider-<?php echo $slider_model -> id ?>" class="<?php echo $slider_model -> element_class ?> ">
  <?php /* $slider_model_id -> tc_render_slider_loader_view( $slider_model-> slider_name_id ); */ ?>
  <?php if ( $slider_model -> has_loader ) : ?>    
    <div id="tc-slider-loader-wrapper-<?php echo $slider_model -> id ?>" class="tc-slider-loader-wrapper" style="display:none;">
      <div class="tc-img-gif-loader"></div>
      <?php echo $slider_model -> pure_css_loader ?>
    </div>
    <script type="text/javascript">
      document.getElementById("tc-slider-loader-wrapper-<?php echo $slider_model -> id ?>").style.display="block";
    </script>
  <?php endif; ?>
  <?php do_action( '__before_carousel_inner' , $slider_model -> slides, $slider_model -> slider_name_id )  ?>

  <div class="<?php echo $slider_model -> inner_class ?>">
    <?php
      foreach ($slider_model -> slides as $slide_id => $slide_data ) {
        //used by each slider instance to set up the current slide data
        do_action( "in_slider_{$slider_model -> id}", $slide_id, $slide_data );
        //used by the slide model (we have just one instance of it as the slide data is set up by the slider model
        do_action( "__slide__" );
      }
    ?>
  </div><!-- /.carousel-inner -->
  <?php  do_action( '__after_carousel_inner' , $slider_model -> slides, $slider_model -> slider_name_id )  ?>
    
  <?php if ( $slider_model -> has_controls ) : ?>
    <div class="tc-slider-controls <?php echo $slider_model -> left_control_class ?>">
      <a class="tc-carousel-control" href="#customizr-slider-<?php echo $slider_model -> id ?>" data-slide="prev">&lsaquo;</a>
    </div>
    <div class="tc-slider-controls <?php echo $slider_model -> right_control_class ?>">
      <a class="tc-carousel-control" href="#customizr-slider-<?php echo $slider_model -> id ?>" data-slide="next">&rsaquo;</a>
    </div>
  <?php endif; ?>
</div><!-- /#customizr-slider -->
