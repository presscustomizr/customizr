<section class ="entry-content <?php tc_echo( 'element_class' ) ?>">
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
</section>
<?php /* hack waiting for tc_render so we can "require" the author info*/ ?>
<footer><?php do_action( 'post_footer' ) ?></footer>
