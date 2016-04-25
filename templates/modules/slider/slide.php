<?php
/**
 * The template for displaying a single slide item
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="item <?php czr_echo( 'element_class' ) ?>" <?php czr_echo('element_attributes') ?>>

  <div class="<?php czr_echo( 'img_wrapper_class' ) ?>">
  <?php if ( czr_get( 'link_whole_slide' ) ) : ?>
    <a class="tc-slide-link" href="<?php czr_echo( 'link_url' ) ?>" target="<?php czr_echo( 'link_target' ) ?>" title=<?php _e( 'Go to', 'customizr' ) ?>>
  <?php endif ?>
    <?php
        do_action('__before_all_slides_background__');
          czr_echo( 'slide_background' );
        do_action('__after_all_slides_background__');
    ?>
  <?php if ( czr_get( 'link_whole_slide' ) ) : ?>
    </a>
  <?php endif; ?>
  </div> <!-- .carousel-image -->

  <?php

  if ( czr_get( 'has_caption' ) ) :

  do_action('__before_all_slides_caption__');

  ?>
  <div class="<?php czr_echo( 'caption_class' ) ?>">
    <?php if ( czr_get( 'title' ) ): ?>
    <!-- TITLE -->
      <<?php czr_echo( 'title_tag' ) ?> class ="<?php czr_echo( 'title_class' ) ?>" <?php czr_echo( 'color_style' ) ?>><?php czr_echo( 'title' ) ?></<?php czr_echo( 'title_tag' ) ?>>
    <?php endif; ?>
    <?php if ( czr_get( 'text' ) ) : ?>
    <!-- TEXT -->
      <p class ="<?php czr_echo( 'text_class' ) ?>" <?php czr_echo( 'color_style' ) ?>><?php czr_echo( 'text' ) ?></p>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( czr_get( 'button_text' ) ): ?>
      <a class="<?php czr_echo( 'button_class' ) ?>" href="<?php czr_echo( 'button_link' ) ?>" target="<?php czr_echo( 'link_target' ) ?>"><?php czr_echo( 'button_text' ) ?></a>
    <?php endif; ?>
  </div>
  <?php

  do_action('__after_all_slides_caption__');
  if ( czr_has( 'slide_edit_button' ) )
    czr_render_template( 'modules/edit_button', 'slide_edit_button' );
  /* endif caption*/
  endif;

  ?>
</div><! -- /.item -->
