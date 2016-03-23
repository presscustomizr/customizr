<section class="tc_content <?php tc_echo('element_class') ?>">
<?php do_action( 'before_render_view_inner_content') /*hack: waiting for tc_render to display the headings ??*/ ?>
  <section class="entry-content <?php tc_echo( 'content_class' ) ?>">
  <?php
    tc_echo( 'post_list_content', array(
       __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' )
   ) );
    wp_link_pages( array(
          'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
          'after'   => '</div>',
          'echo'    => 1
    ) );
  ?>
  </section>
</section>
