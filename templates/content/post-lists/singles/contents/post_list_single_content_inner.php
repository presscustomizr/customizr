<?php
/**
 * The template for displaying the inner content in a post list element
 *
 * In WP loop
 *
 */
?>
<div class="tc-content-inner <?php czr_fn_echo( 'element_class' ) ?>" <?php czr_fn_echo('element_attributes') ?> >
  <?php
    czr_fn_echo( 'the_post_list_content', null, array(
      $show_full_content = false,
      __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ),
    ) );

    wp_link_pages( array(
        'before'        => '<div class="post-pagination pagination row"><div class="col-md-12">',
        'after'         => '</div></div>',
        'link_before'   => '<span>',
        'link_after'    => '</span>',
        'echo'          => true
      )
    )
  ?>
</div>