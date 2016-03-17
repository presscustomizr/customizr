<div class="item <?php echo $slide_model -> item_class ?>">
  <div class="<?php echo $slide_model -> img_wrapper_class ?>">
<!--apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) ); ?>"-->
    <?php
      do_action('__before_all_slides');
    /*do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );*/
        echo $slide_model -> img;  
/*
 *       echo apply_filters( 'tc_slide_background', $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data );
 */
/*do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );*/
      do_action('__after_all_slides');
    ?>
  </div> <!-- .carousel-image -->
</div><! -- /.item -->
