<?php
/**
 * The template for displaying the theme's slider (wrapper)
 *
 */
?>
<div id="customizr-slider-<?php czr_fn_echo( 'id' ) ?>" class="section-slider <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <div class="czr-slider-holder">
<?php
    if ( czr_fn_get_property( 'has_loader' ) ) : ?>
        <div id="czr-slider-loader-wrapper-<?php czr_fn_echo( 'id' ) ?>" class="czr-slider-loader-wrapper">
            <div class="czr-img-gif-loader"></div>
<?php
            czr_fn_echo( 'pure_css_loader' )
?>
        </div>
<?php
    endif;
    do_action( '__before_carousel_inner' );
  ?>
  <div class="<?php czr_fn_echo( 'inner_class' ) ?>" <?php czr_fn_echo( 'inner_attrs' ) ?> >
<?php
        while ( (bool) $the_slide = czr_fn_get_property( 'the_slide' ) )
          czr_fn_render_template( 'modules/slider/slide', array( 'model_args' => array( 'the_slide' => $the_slide ) ) )
?>
  </div><!-- /.carousel-inner -->
<?php
    do_action( '__after_carousel_inner' );
    if ( czr_fn_get_property( 'has_slider_edit_link' ) ) {
      echo czr_fn_edit_button( array(
        'echo' => false,
        'class' => 'slider-btn-edit inverse',
        'link'  => czr_fn_is_customizing() ? czr_fn_get_customizer_focus_link( array( 'wot' => 'control', 'id' => 'tc_theme_options[tc_front_slider]' ) ) : czr_fn_get_property( 'slider_edit_link' ),
        'text'  => czr_fn_get_property( 'slider_edit_link_text' ),
        'visible_when_customizing' => true
      ) );
    }

    if ( czr_fn_get_property( 'has_controls' ) ) {
      czr_fn_carousel_nav();
    }
?>
  </div>
</div><!-- /#customizr-slider -->