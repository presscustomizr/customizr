<section class="<?php echo $post_list_content_model -> content_class ?>">
<?php 
  $post_list_content_model -> tc_the_post_list_content(
     __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' )
  );
  wp_link_pages( array(
          'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
          'after'   => '</div>',
          'echo'    => 0
  ) );
?>
</section>
