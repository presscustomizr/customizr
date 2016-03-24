<?php if ( 'post_list' == tc_get('context') ) : /* Titles in list of posts (alternate layout) */?>
<h2 class="<?php tc_echo( 'element_class') ?>" <?php tc_echo('element_attributes') ?>>
  <a href="<?php the_permalink() ?>" title="<?php _e( 'Permalink to' , 'customizr' ) ?> <?php esc_attr( strip_tags( get_the_title() ) ) ?>" rel="bookmark"><?php the_title() ?></a>
  <?php do_action( '__comment_bubble__' ) ?>
</h2>
<?php else : /* Titles in singular contexts */ ?>
<h1 class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php the_title() ?>
  <?php do_action( '__comment_bubble__' ) ?>
</h1>
<?php endif;
