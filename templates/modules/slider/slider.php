<?php
/**
 * The template for displaying the theme's slider (wrapper)
 *
 */
?>
<div id="customizr-slider-<?php czr_fn_echo( 'id' ) ?>" class="section-slider <?php czr_fn_echo( 'element_class' ) ?> " <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_get( 'has_loader' ) ) : ?>
        <div id="czr-slider-loader-wrapper-<?php czr_fn_echo( 'id' ) ?>" class="czr-slider-loader-wrapper">
            <div class="czr-img-gif-loader"></div>
            <?php czr_fn_echo( 'pure_css_loader' ) ?>
        </div>
  <?php endif ?>
  <?php do_action( '__before_carousel_inner__' ); ?>

  <div class="<?php czr_fn_echo( 'inner_class' ) ?>" <?php czr_fn_echo( 'inner_attrs' ) ?>>
      <?php
        while ( (bool) $the_slide = czr_fn_get( 'the_slide' ) )
          czr_fn_render_template( 'modules/slider/slide', array( 'model_args' => array( 'the_slide' => $the_slide ) ) )
      ?>
  </div><!-- /.carousel-inner -->
  <?php  do_action( '__after_carousel_inner__' ) ?>
  <?php
    if ( czr_fn_get( 'has_slider_edit_link' ) )
      czr_fn_render_template(
        'modules/edit_button',
         array(
          'model_args' => array(
            'edit_button_class' => 'slider-btn-edit inverse',
            'edit_button_link'  => czr_fn_get( 'slider_edit_link' ),
            'edit_button_text'  => czr_fn_get( 'slider_edit_link_text' ),
          )
        )
      )
  ?>
  <?php if ( czr_fn_get( 'has_controls' ) ) : ?>
        <div class="slider-nav">
          <span class="slider-prev <?php czr_fn_echo( 'left_control_class' ) ?>"><i class="icn-left-open-big"></i></span>
          <span class="slider-next <?php czr_fn_echo( 'right_control_class' ) ?>"><i class="icn-right-open-big"></i></span>
        </div>
  <?php endif ?>
</div><!-- /#customizr-slider -->