<?php
/**
 * The template for displaying the single page content
 *
 * In WP loop
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<article <?php tc_echo( 'article_selectors' ) ?> <?php tc_echo( 'element_attributes' ) ?> >
  <?php do_action( '__before_inner_page_article' ) ?>
  <?php if ( tc_has('singular_headings') ) tc_render_template('content/singles/page_headings', 'singular_headings'); ?>
  <?php do_action( '__before_page_entry_content' ) ?>
  <div class ="entry-content">
    <?php do_action( '__before_inner_page_content' ) ?>
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
    <?php do_action( '__after_inner_page_content' ) ?>
  </div>
  <?php do_action( '__after_page_entry_content' ) ?>
  <?php do_action( '__after_inner_page_article' ) ?>
</article>
