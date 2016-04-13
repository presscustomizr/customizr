<?php
/**
 * The template for displaying the single post content
 *
 * In WP loop
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
    'before'        => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
    'after'         => '</div>',
    )
  );
  ?>
  <?php do_action( '__after_inner_post_content' ) ?>
</section>
<footer class="entry-meta">
  <?php
  if ( tc_has('single_author_info') ) {
     tc_render_template('content/authors/author_info', 'single_author_info');
  }
  ?>
</footer>
