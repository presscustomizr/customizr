<section class="<?php echo $post_list_content_model -> content_class ?>">
<?php 
  call_user_func($post_list_content_model -> content_cb,
     __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' )
 );
  wp_link_pages( array(
          'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
          'after'   => '</div>',
          'echo'    => 0
  ) );
?>
</section>
