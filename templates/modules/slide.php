<div class="item <?php tc_echo( 'item_class' ) ?>">
  <?php if ( tc_get( 'link_whole_slide' ) ) : ?>
  <a class="tc-slide-link" href="<?php tc_echo( 'link_url' ) ?>" target="<?php tc_echo( 'link_target' ) ?>" title=<?php _e( 'Go to', 'customizr' ) ?>>
  <?php endif ?>
    <div class="<?php tc_echo( 'img_wrapper_class' ) ?>">

    <?php
        do_action('__before_all_slides');
 //     do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );
          tc_echo( 'slide_background' );  
/*do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );*/
        do_action('__after_all_slides');
    ?>
    </div> <!-- .carousel-image -->
  <?php if ( tc_get( 'link_whole_slide' ) ) : ?>
  </a>
  <?php endif; ?>

  <?php if ( tc_get( 'has_caption' ) ) : ?>
  <div class="<?php tc_echo( 'caption_class' ) ?>">
    <?php if ( tc_get( 'title' ) ): ?>
    <!-- TITLE -->
      <<?php tc_echo( 'title_tag' ) ?> class ="<?php tc_echo( 'title_class' ) ?>" <?php tc_echo( 'color_style' ) ?>><?php tc_echo( 'title' ) ?></<?php tc_echo( 'title_tag' ) ?>>
    <?php endif; ?>
    <?php if ( tc_get( 'text' ) ) : ?>
    <!-- TEXT -->
      <p class ="<?php tc_echo( 'text_class' ) ?>" <?php tc_echo( 'color_style' ) ?>><?php tc_echo( 'text' ) ?></p>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( tc_get( 'button_text' ) ): ?>
      <a class="<?php tc_echo( 'button_class' ) ?>" href="<?php tc_echo( 'button_link' ) ?>" target="<?php tc_echo( 'link_target' ) ?>"><?php tc_echo( 'button_text' ) ?></a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div><! -- /.item -->
