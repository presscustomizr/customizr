<?php
/**
 * The template for displaying the central content (article container)
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="content" class="<?php tc_echo( 'element_class' ) ?>" <?php tc_echo('element_attributes') ?>>
  <?php
    if ( tc_has('404') )
      tc_render_template('content/content_404', '404');

    elseif ( tc_has('no_results') )
      tc_render_template('content_no_results', 'no_results');

    if ( tc_has('posts_list_headings') )
      tc_render_template('content/headings', 'posts_list_headings');

    if ( tc_has('main_loop') )
      tc_render_template('loop', 'main_loop');

    if ( tc_has('comments') )
      tc_render_template('content/comments');

    if ( is_singular() && tc_has('post_navigation_singular') )
      tc_render_template('content/post_navigation', 'post_navigation_singular');
    elseif ( is_archive() && tc_has('post_navigation_posts') )
      tc_render_template('content/post_navigation', 'post_navigation_posts');
    //do_action( '__content__')
  ?>
</div>
