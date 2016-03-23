<h2 class="<?php tc_echo( 'element_class') ?>">
  <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  <?php do_action( '__comment_bubble__' ) ?>
</h2>
