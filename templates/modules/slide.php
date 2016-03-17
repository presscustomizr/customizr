<div class="item <?php echo $slide_model -> item_class ?>">
  <div class="<?php echo $slide_model -> img_wrapper_class ?>">

    <?php
      do_action('__before_all_slides');
 //     do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );
        echo $slide_model -> img;  
/*
 *       echo apply_filters( 'tc_slide_background', $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data );
 */
/*do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );*/
      do_action('__after_all_slides');
    ?>
  </div> <!-- .carousel-image -->
  <?php if ( $slide_model -> has_caption ) : ?>
  <div class="<?php echo $slide_model -> caption_class ?>">
    <?php if ( $slide_model -> title ): ?>
    <!-- TITLE -->
      <<?php echo $slide_model -> title_tag ?> class ="<?php echo $slide_model -> title_class ?>" <?php echo $slide_model -> color_style ?>><?php echo $slide_model -> title ?></<?php echo $slide_model -> title_tag ?>>
    <?php endif; ?>
    <?php if ( $slide_model -> text ): ?>
    <!-- TEXT -->
      <p class ="<?php echo $slide_model -> text_class ?>" <?php echo $slide_model -> color_style ?>><?php echo $slide_model -> text ?></p>
    <?php endif; ?>
    <!-- BUTTON -->
    <?php if ( $slide_model -> button_text ): ?>
      <a class="<?php echo $slide_model -> button_class ?>" href="<?php echo $slide_model -> button_link ?>" target="<?php echo $slide_model -> link_target ?>"><?php echo $slide_model -> button_text ?></a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div><! -- /.item -->
