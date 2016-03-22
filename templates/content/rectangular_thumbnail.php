<section class="<?php echo tc_get( 'wrapper_class' ) ?>">
 <div class="<?php echo tc_get( 'thumb_wrapper_class' ) ?>">
  <a class="<?php echo tc_get( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
    <?php echo tc_get( 'thumb_img' ) ?>
  </a>
 </div>
</section>
