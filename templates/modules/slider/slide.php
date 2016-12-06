<?php
/**
 * The template for displaying a single slide item
 *
 */
?>
<div class="carousel-cell item <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?>>
  <?php if ( czr_fn_get( 'link_whole_slide' ) ) : ?>
    <a class="tc-slide-link bg-link" href="<?php czr_fn_echo( 'link_url' ) ?>" target="<?php czr_fn_echo( 'link_target' ) ?>" title="<?php _e( 'Go to', 'customizr' ) ?>"></a>
  <?php endif ?>
  <div class="filter <?php czr_fn_echo( 'img_wrapper_class' ) ?>">
    <?php
        do_action('__before_all_slides_background__');
          czr_fn_echo( 'slide_background' );
        do_action('__after_all_slides_background__');
    ?>
  </div> <!-- .carousel-image -->

  <?php

if ( czr_fn_get( 'has_caption' ) ) :

  do_action('__before_all_slides_caption__');

  ?>
  <div class="carousel-caption slider-text">
    <?php if ( czr_fn_get( 'title' ) ): ?>
    <!-- TITLE -->
      <h2 class="display thick" <?php czr_fn_echo( 'color_style' ) ?>><?php czr_fn_echo( 'title' ) ?></h2>
    <?php endif; ?>
    <?php if ( czr_fn_get( 'subtitle' ) ) : ?>
    <!-- TEXT -->
      <h3 class="semi-bold" <?php czr_fn_echo( 'color_style' ) ?>><?php czr_fn_echo( 'subtitle' ) ?></h3>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( czr_fn_get( 'button_text' ) ): ?>
      <a class="btn btn-skin btn-large" href="<?php czr_fn_echo( 'button_link' ) ?>" target="<?php czr_fn_echo( 'link_target' ) ?>"><?php czr_fn_echo( 'button_text' ) ?></a>
    <?php endif; ?>
  </div>
  <?php

  do_action('__after_all_slides_caption__');
  /* endif caption*/
endif;

  /* edit link */
  if ( (bool) $edit_url = czr_fn_get( 'edit_url' ) )
      czr_fn_render_template(
        'modules/edit_button',
        array(
          'model_args' => array(
            'edit_button_class' => 'slide-btn-edit inverse',
            'edit_button_link'  => $edit_url
          )
        )
      );
  ?>
</div><! -- /.item -->