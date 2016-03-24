<section class="<?php tc_echo( 'wrapper_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <div class="<?php tc_echo( 'thumb_wrapper_class' ) ?>">
    <div class="round-div"></div>
    <a class="<?php tc_echo( 'link_class' ) ?>" href="<?php the_permalink() ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ) ?>"></a>
    <?php tc_echo( 'thumb_img' ) ?>
  </div>
</section>
