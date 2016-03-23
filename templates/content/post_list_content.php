<section class="<?php tc_echo( 'content_class' ) ?>">
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
