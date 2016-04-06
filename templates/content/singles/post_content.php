<?php
/**
 * The template for displaying the single post content
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<section class ="entry-content <?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php do_action( '__before_inner_post_content' ) ?>
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
  <?php do_action( 'after_render_view_inner_post_content' ) ?>
</section>
<footer class="entry-meta">
  <?php
  if ( tc_has('author_description') ) {
     tc_render_template('content/authors/author_info', 'author_description');
  }
  ?>
</footer>
