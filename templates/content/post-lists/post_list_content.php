<?php
/**
 * The template for displaying the content in a post list element
 *
 * In WP loop
 *
 * @package Customizr
 */
?>
<section class="tc-content entry-content__holder <?php czr_fn_echo('element_class') ?>" <?php czr_fn_echo('element_attributes') ?> >
  
  <?php do_action( 'before_post_list_entry_content' ) ?>

  <?php if ( czr_fn_get( 'is_full_image' ) ) : ?>
    <a class="bg-link" rel="bookmark" title="<?php the_title_attribute( array( 'before' => __('Permalink to ', 'customizr') ) ) ?>" href="<?php the_permalink() ?>"></a>
  <?php endif ?>

  <?php if ( czr_fn_has('post_list_header') ) czr_fn_render_template('content/post-lists/post_list_header') ?>

  <div class="<?php czr_fn_echo( 'content_class' ) ?>">
    <?php
      czr_fn_echo( 'post_list_content', null, array(
        __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ),
        wp_link_pages( array(
            'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
            'after'   => '</div>',
            'echo'    => 0
        ) )
      ) );
    ?>
  </div>

  <?php if ( czr_fn_has('post_list_footer') ) czr_fn_render_template('content/post-lists/post_list_footer') ?>
    
  <?php do_action( 'after_post_list_entry_content' ) ?>

</section>