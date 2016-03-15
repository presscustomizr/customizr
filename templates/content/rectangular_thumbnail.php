<div class="<?php echo $rectangular_thumbnail_model -> wrapper_class ?>">
  <a class="<?php echo $rectangular_thumbnail_model -> link_class ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
    <?php call_user_func( $rectangular_thumbnail_model -> the_thumbnail ) ?>
  </a>
</div>
