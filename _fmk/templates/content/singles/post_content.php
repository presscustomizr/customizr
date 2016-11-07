<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php czr_fn_echo( 'article_selectors' ) ?> <?php czr_fn_echo( 'element_attributes' ) ?> >
  <?php do_action( '__before_inner_post_article' ) ?>
  <?php if ( czr_fn_has('post_thumbnail') && 'before_title' == czr_fn_get( 'thumbnail_position' ) ) czr_fn_render_template('content/singles/thumbnail_single', 'post_thumbnail'); ?>
  <?php if ( czr_fn_has('singular_headings') ) czr_fn_render_template('content/singles/singular_headings', 'singular_headings'); ?>
  <?php do_action( '__before_post_entry_content' ) ?>
  <section class ="entry-content <?php czr_fn_echo( 'element_class' ) ?>" >
    <?php do_action( '__before_inner_post_content' ) ?>
    <?php
    the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
    wp_link_pages( array(
      'before'        => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
      'after'         => '</div>',
      )
    );
    ?>
    <?php do_action( '__after_inner_post_content' ) ?>
  </section>
  <?php do_action( '__after_post_entry_content' ) ?>
  <footer class="entry-meta">
    <?php
    if ( czr_fn_has('single_author_info') ) {
       czr_fn_render_template('content/authors/author_info', 'single_author_info');
    }
    ?>
  </footer>
  <?php do_action( '__after_inner_post_article' ) ?>
</article>
