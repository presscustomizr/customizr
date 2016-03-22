<div class="item <?php echo tc_get( 'item_class' ) ?>">
  <?php if ( tc_get( 'link_whole_slide' ) ) : ?>
  <a class="tc-slide-link" href="<?php echo tc_get( 'link_url' ) ?>" target="<?php echo tc_get( 'link_target' ) ?>" title=<?php _e( 'Go to', 'customizr' ) ?>>
  <?php endif ?>
    <div class="<?php echo tc_get( 'img_wrapper_class' ) ?>">

    <?php
        do_action('__before_all_slides');
 //     do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );
          echo tc_get( 'slide_background' );  
/*do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );*/
        do_action('__after_all_slides');
    ?>
    </div> <!-- .carousel-image -->
  <?php if ( tc_get( 'link_whole_slide' ) ) : ?>
  </a>
  <?php endif; ?>

  <?php if ( tc_get( 'has_caption' ) ) : ?>
  <div class="<?php echo tc_get( 'caption_class' ) ?>">
    <?php if ( tc_get( 'title' ) ): ?>
    <!-- TITLE -->
      <<?php echo tc_get( 'title_tag' ) ?> class ="<?php echo tc_get( 'title_class' ) ?>" <?php echo tc_get( 'color_style' ) ?>><?php echo tc_get( 'title' ) ?></<?php echo tc_get( 'title_tag' ) ?>>
    <?php endif; ?>
    <?php if ( tc_get( 'text' ) ) : ?>
    <!-- TEXT -->
      <p class ="<?php echo tc_get( 'text_class' ) ?>" <?php echo tc_get( 'color_style' ) ?>><?php echo tc_get( 'text' ) ?></p>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( tc_get( 'button_text' ) ): ?>
      <a class="<?php echo tc_get( 'button_class' ) ?>" href="<?php echo tc_get( 'button_link' ) ?>" target="<?php echo tc_get( 'link_target' ) ?>"><?php echo tc_get( 'button_text' ) ?></a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div><! -- /.item -->
