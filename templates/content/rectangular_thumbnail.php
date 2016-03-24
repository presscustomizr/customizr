<section class="tc-thumbnail <?php tc_echo( 'wrapper_class' ) ?>" <?php tc_echo('element_attributes') ?>>
 <div class="<?php tc_echo( 'thumb_wrapper_class' ) ?>">
  <a class="<?php tc_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>">
    <?php tc_echo( 'thumb_img' ) ?>
  </a>
 </div>
</section>
