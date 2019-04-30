<?php
/**
 * The template for displaying a single slide item
 *
 */
?>
<div class="carousel-cell item <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_get_property( 'link_whole_slide' ) ) : ?>
    <a class="tc-slide-link bg-link" href="<?php czr_fn_echo( 'link_url' ) ?>" target="<?php czr_fn_echo( 'link_target' ) ?>"></a>
  <?php endif ?>
  <div class="czr-filter <?php czr_fn_echo( 'img_wrapper_class' ) ?>">
    <?php
        do_action('__before_all_slides_background__');
          czr_fn_echo( 'slide_background' );
        do_action('__after_all_slides_background__');
    ?>
  </div> <!-- .carousel-image -->

  <?php

if ( czr_fn_get_property( 'has_caption' ) ) :

  do_action('__before_all_slides_caption__');

  ?>
  <div class="carousel-caption slider-text">
    <?php if ( czr_fn_get_property( 'title' ) ): ?>
    <!-- TITLE -->
      <h2 class="czrs-title display-1 thick very-big" <?php czr_fn_echo( 'color_style' ) ?>><?php czr_fn_echo( 'title' ) ?></h2>
    <?php endif; ?>
    <?php if ( czr_fn_get_property( 'subtitle' ) ) : ?>
    <!-- TEXT -->
      <h3 class="czrs-subtitle semi-bold" <?php czr_fn_echo( 'color_style' ) ?>><?php czr_fn_echo( 'subtitle' ) ?></h3>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( czr_fn_get_property( 'button_text' ) ): ?>
      <div class="czrs-cta-wrapper">
        <a class="czrs-cta btn btn-skin-h-dark caps" href="<?php czr_fn_echo( 'button_link' ) ?>" target="<?php czr_fn_echo( 'link_target' ) ?>"><?php czr_fn_echo( 'button_text' ) ?></a>
      </div>
    <?php endif; ?>
  </div>
  <?php

  do_action('__after_all_slides_caption__');
  /* endif caption*/
endif;

  /* edit link */
  if ( (bool) $edit_url = czr_fn_get_property( 'edit_url' ) ) {
    czr_fn_edit_button( array( 'class' => 'slide-btn-edit inverse', 'link'  => $edit_url ) );
  }

?>
</div><!-- /.item -->