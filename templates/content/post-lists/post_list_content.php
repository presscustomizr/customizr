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
  <div class="entry-content__wrapper <?php czr_fn_echo('inner_wrapper_class') ?>">
    <?php do_action( 'before_post_list_entry_content' ) ?>

    <?php if ( czr_fn_get('has_header') && czr_fn_has('post_list_header') )
      czr_fn_render_template('content/post-lists/headings/post_list_header', 'post_list_header', array(
        'has_header_format_icon' => czr_fn_get('has_header_format_icon')
        )
      );
    ?>

    <div class="tc-content-inner <?php czr_fn_echo( 'content_inner_class' ) ?>">
      <?php
        czr_fn_echo( 'the_post_list_content', null, array(
          $show_full_content = false,
          __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ),
          wp_link_pages( array(
            'before'        => '<div class="post-pagination row"><div class="col-md-12">',
            'after'         => '</div></div>',
            'link_before'   => '<span>',
            'link_after'    => '</span>',
            'echo'          => false
            )
          )
        ) );
      ?>
    </div>

    <?php if ( czr_fn_get('has_footer') && czr_fn_has('post_list_footer') ) czr_fn_render_template('content/post-lists/footers/post_list_footer') ?>

    <?php do_action( 'after_post_list_entry_content' ) ?>
  </div>
</section>