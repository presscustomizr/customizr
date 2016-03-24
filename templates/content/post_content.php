<section class ="entry-content <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( 'before_render_view_inner_post_content' ) ?>
  <?php
  the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
  wp_link_pages( array(
    'before'        => '<div class="btn-toolbar page-links"><div class="btn-group">' . __( 'Pages:' , 'customizr' ),
    'after'         => '</div></div>',
    'link_before'   => '<button class="btn btn-small">',
    'link_after'    => '</button>',
    'separator'     => '',
    )
  );
  ?>
  <?php do_action( 'after_render_view_inner_post_content' ) ?>
</section>
<footer><?php do_action( '__post_footer__' ) ?></footer>
