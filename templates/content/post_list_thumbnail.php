<section class="<?php echo $post_list_thumbnail_model -> wrapper_class ?>">
  <div class="<?php echo $post_list_thumbnail_model -> thumb_wrapper_class ?>">
    <div class="round-div"></div>
    <a class="<?php echo $post_list_thumbnail_model -> link_class ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
    <?php echo $post_list_thumbnail_model -> thumb_img ?>
  </div>
</section>
