<?php
/**
 * The template for displaying the content in a post list element
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<section class="tc-content <?php czr_echo('element_class') ?>" <?php czr_echo('element_attributes') ?> >

  <?php do_action( 'before_post_list_entry_content' ) ?>

  <?php if ( czr_has('headings') ) { czr_render_template('content/post-lists/post_page_headings'); } ?>

  <section class="entry-content <?php czr_echo( 'content_class' ) ?>">
    <?php
      czr_echo( 'post_list_content', null, array(__( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ) );

      wp_link_pages( array(
            'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
            'after'   => '</div>',
            'echo'    => 1
      ) );
    ?>
  </section>

  <?php do_action( 'after_post_list_entry_content' ) ?>

</section>
