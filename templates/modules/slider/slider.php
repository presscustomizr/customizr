<?php
/**
 * The template for displaying the theme's slider (wrapper)
 *
 */
?>
<div id="customizr-slider-<?php czr_fn_echo( 'id' ) ?>" class="section-slider <?php czr_fn_echo( 'element_class' ) ?> " <?php czr_fn_echo('element_attributes') ?>>

  <?php if ( czr_fn_get( 'has_loader' ) ) : ?>
        <div id="tc-slider-loader-wrapper-<?php czr_fn_echo( 'id' ) ?>" class="tc-slider-loader-wrapper" style="display:none;">
            <div class="tc-img-gif-loader"></div>
            <?php czr_fn_echo( 'pure_css_loader' ) ?>
        </div>
        <script type="text/javascript">
          document.getElementById("tc-slider-loader-wrapper-<?php czr_fn_echo( 'id' ) ?>").style.display="block";
        </script>
  <?php endif ?>
  <?php do_action( '__before_carousel_inner__' ); ?>
  
  <div class="<?php czr_fn_echo( 'inner_class' ) ?>" <?php czr_fn_echo( 'inner_attrs' ) ?>>
      <?php
        while ( czr_fn_get( 'has_slide' ) ) {
          if ( czr_fn_has( 'slide' ) )
            czr_fn_render_template( 'modules/slider/slide', 'slide');
        }
      ?>
  </div><!-- /.carousel-inner -->
  <?php  do_action( '__after_carousel_inner__' ) ?>
  <?php if ( czr_fn_has( 'slider_edit_button' ) ) czr_fn_render_template( 'modules/edit_button', 'slider_edit_button' )  ?>
  <?php if ( czr_fn_get( 'has_controls' ) ) : ?>
        <div class="slider-nav"> 
          <span class="slider-prev <?php czr_fn_echo( 'left_control_class' ) ?>"><i class="icn-left-open-big"></i></span> 
          <span class="slider-next <?php czr_fn_echo( 'right_control_class' ) ?>"><i class="icn-right-open-big"></i></span>
        </div>
  <?php endif ?>
</div><!-- /#customizr-slider -->